<?php
/*
Plugin Name: PHP Speedy WP
Plugin URI: http://aciddrop.com
Description: Speeds up the display of your blog by combining your JS and CSS files, adding far future expires headers and GZIPing.
Version: 0.4.7
Author: Leon Chevalier
Author URI: http://aciddrop.com/
Copyright (c) 2008 Leon Chevalier
Released under the GNU General Public License (GPL)
http://www.gnu.org/licenses/gpl.txt
0.1 - Initial Release
0.2 - Fixed bug in ignore (thanks Jeromy)
0.2.1 - Changed control of when plugin loads
	  - Fixed problem with space on ignore list
0.2.2 - Really fixed bug in ignore (thanks Steve)
0.3   - Added write of debugging info
	  - Now works on more pages
0.4   - Added Speedy handling of plugin libraries
	  - Wordpress exclusively handles gzipping
	  - Compressed files now appear first in document
	  - Fixed bug in debugging info write
	  - Made compressed files include at start of head
0.4.1 - Doesn't die if WP_Scripts class not present
0.4.2 - Fixed bug if no css or js in head
0.4.3 - Fixed is_* functions for all versions
0.4.4 - Fixed display for trackbacks (thanks Steve)
0.4.5 - Added check for empty POST array (thanks Jeromy)
0.4.6 - Updated test page to show use of PHP Speedy standalone
0.4.7 - Updated to work with WP 2.6 (thanks Kaspars!)
*/

 
/**
 * COMPATIBILITY CLASSES ************************************
 *
 **/

/**
* Same as wordpress print_scripts function but returns output
**/
if(class_exists('WP_Scripts')) {
class wp_scripts_extend extends WP_Scripts {

	/**
	 * Returns script tags
	 *
	 * Prints the scripts passed to it or the print queue.  Also prints all necessary dependencies.
	 *
	 * @param mixed handles (optional) Scripts to be printed.  (void) prints queue, (string) prints that script, (array of strings) prints those scripts.
	 * @return array Scripts that have been printed
	 */
	function return_scripts( $handles = false ) {
		global $wp_db_version;

		// Print the queue if nothing is passed.  If a string is passed, print that script.  If an array is passed, print those scripts.
		$handles = false === $handles ? $this->queue : (array) $handles;
		$this->all_deps( $handles );

		$to_print = apply_filters( 'print_scripts_array', array_keys($this->to_print) );

		foreach( $to_print as $handle ) {
			if ( !in_array($handle, $this->printed) && isset($this->scripts[$handle]) ) {
				if ( $this->scripts[$handle]->src ) { // Else it defines a group.
					$ver = $this->scripts[$handle]->ver ? $this->scripts[$handle]->ver : $wp_db_version;
					if ( isset($this->args[$handle]) )
						$ver .= '&amp;' . $this->args[$handle];
					$src = 0 === strpos($this->scripts[$handle]->src, 'http://') ? $this->scripts[$handle]->src : get_option( 'siteurl' ) . $this->scripts[$handle]->src;
					$src = $this->scripts[$handle]->src;

					if (!preg_match('|^https?://|', $src)) {
						$src = get_option('siteurl') . $src;
					}

					$src = add_query_arg('ver', $ver, $src);
					$src = clean_url(apply_filters( 'script_loader_src', $src ));
					$out = "<script type='text/javascript' src='$src'></script>\n";
				}
				$this->printed[] = $out;
			}
		}

		$this->to_print = array();
		return $this->printed;
	}		
	
}
} else {
$GLOBALS['php_speedy_wp']['no_script_loader'] = true;
}

/**
 * View class for compat with Speedy lib
 *
 **/
class speedy_view {

	function render ($file_name, $vars = array ()) {
	$return = array('filename'=>$filename,
				 'vars'=>$vars
				 );
	echo $return['vars']['error'];
	}
	
	function set_paths() {
	
	//Get document root
	if (DIRECTORY_SEPARATOR != '/') { //Windows
		$full_path = str_replace (DIRECTORY_SEPARATOR, '/', ABSPATH);
		$cookie_path = str_replace (DIRECTORY_SEPARATOR, '/', SITECOOKIEPATH);			
	} else {
		$full_path = ABSPATH;
		$cookie_path = SITECOOKIEPATH;				
	}
	
	//Set doc root
	$document_root = preg_replace("@" . $cookie_path . "$@","",$full_path);
			
	$this->paths['full']['current_directory'] = $document_root;
	$this->paths['full']['document_root'] = $document_root;
	
	//Relative current dir
	$this->paths['relative']['current_directory'] = "/" . PLUGINDIR . "/" . basename(dirname(__FILE__));	
	$this->paths['relative']['current_directory'] = get_bloginfo ('wpurl').'/'.ltrim ($this->paths['relative']['current_directory'], '/');
	$this->paths['relative']['current_directory'] = str_replace("http://".$_SERVER['HTTP_HOST'],"",$this->paths['relative']['current_directory']);
	$this->paths['relative']['current_directory'] = str_replace("https://".$_SERVER['HTTP_HOST'],"",$this->paths['relative']['current_directory']);		
			
	}
	
	/**
	 * 
	 * 
	 **/	
	 function ensure_trailing_slash($path) {
	 
	 	if(substr($path,-1,1) != "/") {
		$path .= "/";
		}	 
	 
	 	return $path;
	 
	 }

	/**
	 * 
	 * 
	 **/	
	 function prevent_trailing_slash($path) {
	 
	 	if(substr($path,-1,1) == "/") {
		$path = substr($path,0,-1); 
		}	 
	 
	 	return $path;
	 
	 }
	 
	/**
	 * 
	 * 
	 **/	
	 function prevent_leading_slash($path) {
	 
	 	if(substr($path,0,1) == "/" || substr($path,0,1) == "\\") {
		$path = substr($path,1); 
		}	 
	 
	 	return $path;
	 
	 }		 
	 
	/**
	 * Version of basename works on nix and windows
	 *
	 **/	
	function get_basename($filename) {
	
	$basename = preg_replace( '/^.+[\\\\\\/]/', '', $filename );
	
	return $basename;
	
	}	 	
	
}
/**
 * Extend the admin controller to overwrite the view render function
 *
 **/
require_once(rtrim (dirname (__FILE__), '/') . "/libs/php_speedy/controller/admin.php");
class admin_ext extends admin {

	function admin_ext() {
	
	$this->view = new speedy_view();
	
	}


}

/**
 * Instantiate classes
 *
 **/

//Include libs
$plugin_base = rtrim (dirname (__FILE__), '/');
require_once($plugin_base . "/libs/php_speedy/controller/compressor.php");		

$compressor = new compressor(array('skip_startup'=>true));
$compressor->view = new speedy_view(); //compat

if(substr(phpversion(),0,1) == 5) { //php5 only
require_once($plugin_base . "/libs/php_speedy/libs/php/jsmin.php");
$compressor->jsmin = new JSMin(null); //compat	
}

//Instantiate helper classes
$php_speedy_wp_utility = new php_speedy_wp_utility();
$php_speedy_wp_admin = new admin_ext(array('skip_startup'=>true));

$speedy_admin = new php_speedy_wp_admin(array(
							  'utility'=>$php_speedy_wp_utility,
							  'admin'=>$php_speedy_wp_admin,
							  'compressor'=>$compressor,
							  'input'=>array_merge($_GET,$_POST)
							  )
						);			
		
/**
 * Class that handles public facing pages
 *
 **/
class php_speedy_wp_controller {
	
	//Constructor
	function php_speedy_wp_controller($array=null) {
	
		if(is_array($array)) {
			foreach($array AS $key=>$value) {
			$this->$key = $value;
			}
		}
					
	}

	//Go
	function run() {
		
	//Pass the options through
	$this->admin->get_options();
	
	if(empty($this->admin->compress_options['active'])) { //Only continue if Speedy active
		if(strstr($_SERVER['HTTP_USER_AGENT'],"PHP Speedy Config Test")) {
		ob_start(array($this,'add_scripts')); 
		}
	return;
	}
	
	//Wordpress does the gzipping
	$this->admin->compress_options['gzip']['page'] = 0;
			
	$this->admin->compressor->options = $this->admin->compress_options;
	$this->admin->compressor->set_options();	
	$this->admin->compressor->set_gzip_headers(); //Set headers		
	
	//Set ignore file
	if(!empty($this->admin->compress_options['ignore_list'])) {
	$this->admin->compressor->ignore(trim($this->admin->compress_options['ignore_list']));
	}	
	
	//add_action( 'wp_head', array($this,'add_scripts'),9);	
	add_action( 'wp_footer', array($this,'add_footer'));
	add_action( 'wp_redirect', array($this,'redirect_filter')); //Don't get in the way of redirects
	
	ob_start(array($this,'finish')); 
	
	}
	
	function finish($content) {
	
		if(empty($this->doing_wp_redirect)) {		
		
			$this->admin->compressor->return_content = true;
			$content = $this->add_scripts($content);
			return $this->admin->compressor->finish($content);
			
		} else {
			header("location:".redirect_canonical(NULL,false));
		}
	
	}
	
	function redirect_filter($location) {
	
	$this->doing_wp_redirect = true;
	
	}

	//Adds the scripts from the JavaScript libraries chosen in the config
	function add_scripts($content) {
	
	if(!empty($GLOBALS['php_speedy_wp']['no_script_loader'])) {
	return $content;
	}
	
	$wp_scripts_extend = new wp_scripts_extend();
		
	if(!empty($this->admin->compress_options['js_libraries'])) {
	
		$to_enqueue = explode(",",$this->admin->compress_options['js_libraries']);
			
			foreach($to_enqueue AS $script) {
			$wp_scripts_extend->enqueue($script);
			
				//Remove src
				$src = end(explode("/",$wp_scripts_extend->scripts[$script]->src));
				$this->admin->compressor->remove_files[] = trim($src);
			
			}
		
		$scripts = $wp_scripts_extend->return_scripts();
		
			if(is_array($scripts)) {
				$script_out = implode("\n",$scripts);
				$script_out = "<!-- REMOVE IMMUNE -->" . $script_out . "<!-- END REMOVE IMMUNE -->";
				$content = 	preg_replace("!<head([^>]+)?>!is","$0 ".$script_out,$content);
			}

	}
				
	return $content;	
			
	}
	
	//Adds the credit footer	
	function add_footer() {
	
	if(empty($this->admin->compress_options['footer']['text']) && empty($this->admin->compress_options['footer']['image'])) {
	return;
	}
		
	echo "<p>";
		
	$message_array = array(
					 get_bloginfo('name') . " proudly uses <a href='http://aciddrop.com/php-speedy' target=_new>PHP Speedy</a>",
					 "<a href='http://aciddrop.com/php-speedy' target=_new>Blog performance enhanced by PHP Speedy</a>",
					 "<a href='http://aciddrop.com/php-speedy' target=_new>JS and CSS Optimization by PHP Speedy</a>",
					 "<a href='http://aciddrop.com/php-speedy' target=_new>Site Optimization by PHP Speedy</a>",
					 "<a href='http://aciddrop.com/php-speedy' target=_new>PHP Optimization by PHP Speedy</a>",					 
					 "<a href='http://aciddrop.com/php-speedy' target=_new>Site speeded up by PHP Speedy</a>",					
					 "Load time improved by <a href='http://aciddrop.com/php-speedy' target=_new>PHP Speedy</a>",										  					 
					 "<a href='http://aciddrop.com/php-speedy' target=_new>Site speeded up by PHP Speedy</a>",					
					 get_bloginfo('name') . " load time improved by <a href='http://aciddrop.com/php-speedy' target=_new>PHP Speedy</a>",					
					 "<a href='http://aciddrop.com/php-speedy' target=_new>Load time improved by PHP Speedy</a>",										 					 
					 );
	
	preg_match("@[0-9]@",md5($_SERVER['REQUEST_URI']),$matches);
	
	if(!empty($matches[0]) && !empty($this->admin->compress_options['footer']['text'])) {
	echo $message_array[$matches[0]];
	}
		
	if(!empty($this->admin->compress_options['footer']['image'])) {
	echo "&nbsp;<a href='http://aciddrop.com/php-speedy' target=_new><img src='" .  $this->admin->speedy_lib_path ."/images/php_speedy_footer_link.png' style='vertical-align:middle' border='0' alt='" . strip_tags($message_array[$matches[0]]) . "' /></a>";	
	}

	echo "</p>";
	
	}

}

/**
 * Class that handles admin pages
 * Also contains method used by public facing class (could be split in future version)
 **/
class php_speedy_wp_admin {
	
	//Constructor
	function php_speedy_wp_admin($array=null) {
	
		if(is_array($array)) {
			foreach($array AS $key=>$value) {
			$this->$key = $value;
			}
		}
		
		//Paths
		$this->set_paths();			
				
		//Go go go 
		$this->run();
			
	}
	
	//Forest
	function run() {
	
	$this->process_input();
	
	//Get and check
	$this->get_options();
	$this->check_configuration();
	
	//Add menu pages
	$this->add_pages();
	
	
	
	}
	
	//Something submitted
	function process_input() {
	
	if($this->input['action']) {
		$func = $this->input['action'];
		if(method_exists($this,$func)) {
		$this->$func(); //Run the function for the subpage
		}	
	}	
	
	
	}
	

	//Paths for the application to use
	function set_paths() {

	$url = explode ('&', $_SERVER['REQUEST_URI']);
	$this->base_admin_url = $url[0];
	
	$this->home_url = basename(dirname(__FILE__)) . '/' . basename(__FILE__);
	$this->plugin_base = rtrim (dirname (__FILE__), '/');
	$this->plugin_rel_base = $this->plugin_url();
	$this->speedy_lib_path = $this->plugin_rel_base . "/libs/php_speedy";	
			
	}
	
	/**
	 * Get a URL to the plugin.  Useful for specifying JS and CSS files (Thanks to Search Unleashed)
	 *
	 * For example, <img src="<?php echo $this->url () ?>/myimage.png"/>
	 *
	 * @return string URL
	 **/
	
	function plugin_url ($type='rel')	{
	
		$url = substr ($this->plugin_base, strlen ($this->utility->realpath (ABSPATH)));
		if (DIRECTORY_SEPARATOR != '/')
			$url = str_replace (DIRECTORY_SEPARATOR, '/', $url);

		$url = get_bloginfo ('wpurl').'/'.ltrim ($url, '/');
		
		if($type == "rel") {
		$url = str_replace("http://".$_SERVER['HTTP_HOST'],"",$url);
		$url = str_replace("https://".$_SERVER['HTTP_HOST'],"",$url);		
		}
	
		// Do an SSL check - only works on Apache
		global $is_IIS;
		if (isset ($_SERVER['HTTPS']) && !$is_IIS)
			$url = str_replace ('http://', 'https://', $url);

		return $url;
		
	}	
		
	//Adds the options for backend
	function add_pages() {
		
	// Hook for adding admin menus
	add_action('admin_menu', array($this,'mt_add_pages'));
	add_action('template_redirect', array($this,'do_compression'));
	
	
	}	
	
	// action function for above hook
	function mt_add_pages() {
				
		add_options_page('php_speedy_wp.php', 'PHP Speedy', 0, $this->home_url, array(&$this, 'menu_system'));
	
	}	
	
	//Do compression for a page or post
	function do_compression($content) {
	
	global $wp_query;
			
		//Post check
		$is_post_array = false;
		if(is_array($_POST)) {
			$post_contents = implode("",$_POST);
			if(!empty($post_contents)) {
			$is_post_array = true;
			}
		}
	
		if((
		!empty($wp_query->is_single)
		|| !empty($wp_query->is_preview) 
		|| !empty($wp_query->is_page) 
		|| !empty($wp_query->is_archive)		
		|| !empty($wp_query->is_date) 	
		|| !empty($wp_query->is_category) 
		|| !empty($wp_query->is_tag) 
		|| !empty($wp_query->is_search)
		|| !empty($wp_query->is_home) 		
		|| !empty($wp_query->is_404)
		) && empty($is_post_array) && empty($wp_query->is_feed) && empty($wp_query->is_author) && empty($wp_query->is_comment_feed) && empty($wp_query->is_trackback) && empty($wp_query->is_comments_popup) && empty($wp_query->is_admin) && empty($wp_query->is_attachment) && empty($wp_query->is_robots)) {
		$speedy_controller = new php_speedy_wp_controller(array('admin'=>$this));
		$speedy_controller->run();	
		} else {
			if(empty($wp_query->is_feed) && empty($wp_query->is_author) && empty($wp_query->is_comment_feed) && empty($wp_query->is_trackback) && empty($wp_query->is_comments_popup) && empty($wp_query->is_admin) && empty($wp_query->is_attachment) && empty($wp_query->is_robots)) {
				$this->write_debugging_info();
			}
		}
	
	}
	
	//Why isn't speedy showing?
	function write_debugging_info() {
	
		//What plugins are installed? Maybe they're messing with Speedy
		$debug['plugins'] = get_option('active_plugins');
		
		//The post array
		$debug['post'] = $_POST;
		
		//What does wp_db_query contain?
		global $wp_query;
		$debug['wp_query'] = $wp_query;
	
		$out = print_r($debug,true);
			
		//Write to cache dir
		if(file_exists($this->options['cache_dir']['value'] . '/debugging_info.txt')) {
		unlink($this->options['cache_dir']['value'] . '/debugging_info.txt');
		}
		if ($fp = fopen($this->options['cache_dir']['value'] . '/debugging_info.txt', 'wb')) {
		fwrite($fp, $out);
		fclose($fp);		
		}
	
	}	
	
	//Get Speedy options
	function get_options() {
	
	//Get JS libs info
	$this->get_js_handles();
	
	//Include options file
	require($this->plugin_base . "/libs/php_speedy/config.php");
	$this->compress_options = $compress_options;
	
	if(!$compress_options['javascript_cachedir']) {
	$compress_options['javascript_cachedir'] = $this->plugin_base . "/cache";
	}
	
	$this->active = $compress_options['active'];
	
	$this->options = array('cache_dir'=>array('title'=>'Cache Directory',
					 						  'intro'=>'PHP Speedy will store your compressed JavaScript and CSS files in a cache directory. <br/>You can enter a new directory below if desired. This directory must be within your document root and writable by the server. If in doubt just use the directory suggested.',
										      'key'=>'Directory',
										      'value'=>$compress_options['javascript_cachedir']
											  ),
			   'options_file'=>array('title'=>'Options file',
					 						  'intro'=>'PHP Speedy stores its configuration in an options file. The file cannot be changed, but must be writable by the server.',
										      'key'=>'Options_File',
										      'value'=>$this->plugin_base . "/libs/php_speedy/config.php"
											  ),
			   'js_libraries'=>array('title'=>'JavaScript Libraries',
					 						  'intro'=>'If your plugins or theme use a JavaScript library, it is advisable to let PHP Speedy handle where it is included.<br />
											  			Speedy has determined that the libraries below could be in use by your installation. It is recommended that you tick all the scripts to let PHP Speedy handle them.<br/>
														If your plugins or theme use a higher version or you are sure you don\'t use the library at all, leave unticked.',
										      'key'=>'JS_Libraries',
										      'value'=>$compress_options['js_libraries']
											  ),													  
			   'ignore_list'=>array('title'=>'Ignore list',
					 						  'intro'=>'PHP Speedy can ignore certain scripts of your choosing. Please enter the filenames of the scripts you would like to ignore below, separated by a comma.',
										      'key'=>'Ignore_List',
										      'value'=>$compress_options['ignore_list']
											  ),											  								  											  
						   	  'minify'=>array('title'=>'Minify Options',
					 						  'intro'=>'Minifying removes whitespace and other unnecessary characters.',
										      'value'=>$compress_options['minify']
											  ),							
						   	    'gzip'=>array('title'=>'Gzip Options',
					 						  'intro'=>'Gzipping compresses the code via Gzip compression. This is recommended only for small scale sites, and is off by default.
											  			<br/>For larger sites, you should Gzip via the web server.',
										      'value'=>$compress_options['gzip']
											  ),								
		   	      'far_future_expires'=>array('title'=>'Far Future Expires Options',
					 						  'intro'=>'This adds an expires header to your JavaScipt and CSS files which ensures they are cached client-side by the browser.
											  			<br/>When you change your JS or CSS, a new filename is generated and the latest version is therefore downloaded and cached.',
										      'value'=>$compress_options['far_future_expires']
											  ),
			       			  'cleanup'=>array('title'=>'File cleanup',
					 						  'intro'=>'When you change your JavaScript or CCS PHP Speedy will automatically generate a new compressed file and remove any unused files from the directory.
											  <br/>However, if different pages in your site use different JS or CSS files Speedy will get confused and cleanup files it shouldn\'t. In this case, you should turn off the cleanup process.',
										      'value'=>$compress_options['cleanup']
											  ),
			       			  'footer'=>array('title'=>'Footer text',
					 						  'intro'=>'PHP Speedy can add a link in your blog footer back to the PHP Speedy website. The link can be a text link, a small image link or both.
													   <br/>Please support PHP Speedy by enabling this.',
										      'value'=>$compress_options['footer']
											  )									  												  										  
											  
						 );	
						 			 
	
	
	}
	
	//Check configuration
	function check_configuration() {
	
	$this->config['cache_writeable'] = $this->utility->file_check_directory($this->options['cache_dir']['value']);
	$this->config['options_file_writeable'] = $this->utility->file_check_directory($this->options['options_file']['value']);
	
	if($this->config['cache_writeable'] == 0 || $this->config['options_file_writeable'] == 0) {
	$this->allow_activation = 0;
	} else {
	$this->allow_activation = 1;
	}
	
	//print_r($this->config);
	
	}
	
	//Standard page vars
	function create_page_vars() {
	
	$vars['speedy_lib_path'] = $this->speedy_lib_path;
	$vars['plugin_rel_base'] = $this->plugin_rel_base;
	$vars['cache_dir'] = $this->options['cache_dir'];
	$vars['config'] = $this->config;
	$vars['options'] = $this->options;
	$vars['options_list'] = $this->options_list;
	$vars['message'] = $this->message;	
	$vars['allow_activation'] = $this->allow_activation;
	
	$vars['js_libraries'] = $this->js_libraries;
	
	$this->vars = $vars;		
	
	
	}	

	//Configure PHP Speedy
	function page_configure() {
				
	$this->create_page_vars();	
	$this->render('private.php_speedy.page_configure',$this->vars);	
	
	}

	//Update PHP Speedy config
	function update_configuration() {
	
	//Set wordpress gzipping
	if(empty($this->input['speedy']['gzip']['page'])) {
	update_option('gzipcompression','');
	} else {
	update_option('gzipcompression','1');
	}
	
	//For Speedy lib compatibility
	$this->input['speedy']['javascript_cachedir'] = $this->input['speedy']['cache_dir'];
	$this->input['speedy']['css_cachedir'] = $this->input['speedy']['cache_dir'];
	
	//JS Libraries stored as a string
	if(is_array($this->input['speedy']['js_libraries'])) {
	$this->input['speedy']['js_libraries'] = implode(",",$this->input['speedy']['js_libraries']);
	} else {
	$this->input['speedy']['js_libraries'] = "";
	}
		
		//Save the options		
		$this->admin->view->paths['full']['current_directory'] = $this->plugin_base . "/libs/php_speedy/";
		$this->admin->options_file = "config.php";
		foreach($this->input['speedy'] AS $key=>$option) {
			if(is_array($option)) {
				foreach($option AS $option_name=>$option_value) {
				$this->admin->save_option("['" . strtolower($key) . "']['" . strtolower($option_name) . "']",$option_value);	
				}			
			} else {
			$this->admin->save_option("['" . strtolower($key) . "']",$option);			
			}			
		}

		$this->message = "Configuration saved. Please <a href='" . $this->base_admin_url . "&sub=test_config'>test your configuration</a>.";
		
	
	}

	//Test the config	
	function test_config() {
		
	$this->delete_all_in_cache_dir();
	$this->check_configuration();	
	$this->get_options();
	
	if(!empty($this->compress_options['active'])) { //Only continue if Speedy active
	$this->deactivate_speedy();
	$do_activate = true;
	}
			
	//Get the HTML
	require_once( ABSPATH . 'wp-includes/class-snoopy.php');
	$snoopy = new Snoopy();
	$snoopy->agent = "PHP Speedy Config Test";
	$snoopy->fetch(get_option('home'));
	
	$html = $snoopy->results;
	
	if(!empty($do_activate)) {
	$this->activate_speedy();
	}
	
	//Pass the options through
	$this->compress_options['gzip']['page'] = 0;
	$this->compressor->options = $this->compress_options;
	$this->compressor->set_options();	
	$this->compressor->set_gzip_headers(); //Set headers
		
	//Set ignore file
	if(!empty($this->compress_options['ignore_list'])) {
	$this->compressor->ignore(trim($this->compress_options['ignore_list']));
	}
	
	//Set remove files
	if(!empty($this->compress_options['js_libraries']) && empty($GLOBALS['php_speedy_wp']['no_script_loader'])) {
	global $wp_scripts;
	$to_enqueue = explode(",",$this->compress_options['js_libraries']);
	
		foreach($to_enqueue AS $script) {		
			//Remove src
			$src = basename($wp_scripts->scripts[$script]->src);
			$this->compressor->remove_files[] = trim($src);
		
		}
	}	
		
	//Do compression	
	$this->compressor->supress_output = true;	
	$this->compressor->finish($html);

	//Create report
	//$this->delete_test_files();
	
	//Print report
	$this->print_report();
	
	//print_r($this->compressor->process_report);
	
	
	}
	
	//Get the handles of JS Libraries that we think may be in use
	function get_js_handles() {

	if(!empty($GLOBALS['php_speedy_wp']['no_script_loader'])) {
	return;
	}

	//Check scripts on this installation
	$this->check_scripts();
	
	//Get libraries
	global $wp_scripts;		
	
	// check if $wp_scripts is loaded and not empty
    if(!is_object($wp_scripts) && empty($wp_scripts)) {
         $wp_scripts = new WP_Scripts();
	}	
	
    if (method_exists($wp_scripts,'default_scripts')) {
		   // if wp 2.5
		   $wp_did_register = $wp_scripts->scripts;
    } else {
		   // if wp 2.6
		   $wp_did_register = $wp_scripts->registered;
    }	
	
	//Which libraries are used by this installation?
	foreach($wp_did_register AS $object) {
		$thename = str_replace(".js","",basename($object->src));		
		if(!empty($this->utility->file_tree[$thename])) {
		$this->js_libraries[] = array('install'=>$this->utility->file_tree[$thename],
								   'wp'=>$object);
		$this->js_handles[] = $object->handle;
		}
	}
	
	if(is_array($this->js_handles)) {
		return $this->js_handles;
	}
		
	}
	
	//Check plugin and theme dir for scripts //created $this->file_tree
	function check_scripts() {
	
	global $wp_scripts;
	
	//Get plugin files
	$this->utility->scan_directory_recursively($this->compressor->view->ensure_trailing_slash($this->plugin_base)."..","js");
	
	//Get theme files
	$this->utility->scan_directory_recursively(STYLESHEETPATH,"js");

	
	}
	
	//Printy
	function print_report() {
	
	if(empty($this->compress_options['active'])) {
	$this->message = "Happy with the test? You should now <a href='" . $this->base_admin_url . "&sub=activate_deactivate'>activate PHP Speedy</a>";
	} else {
	unset($this->message);
	}
		
	$this->create_page_vars();	
	$this->vars['scripts'] = $this->compressor->process_report['scripts'];
	$this->vars['skipped'] = $this->compressor->process_report['skipped'];	
	$this->vars['notice'] = $this->compressor->process_report['notice'];		
	$this->render('private.php_speedy.page_report', $this->vars);
	
	}
	
	//Turn her on
	function activate_speedy() {
	
	//Set message
	$this->message = "PHP Speedy has been activated";
	
	//Save
	$this->admin->view->paths['full']['current_directory'] = $this->plugin_base . "/libs/php_speedy/";
	$this->admin->options_file = "config.php";
	$this->admin->save_option("['active']",1);		
	
	
	}
	
	//And Off
	function deactivate_speedy() {

	//Set message	
	$this->message = "PHP Speedy has been deactivated";

	//Save
	$this->admin->view->paths['full']['current_directory'] = $this->plugin_base . "/libs/php_speedy/";
	$this->admin->options_file = "config.php";
	$this->admin->save_option("['active']",0);		
	
	}	
	
	//Turn on and off
	function activate_deactivate() {
		
	$this->create_page_vars();	
	
	if($this->active) {
	$this->vars['title'] = "Deactivation";
	$this->vars['action'] = "deactivate_speedy";	
	$this->vars['intro'] = "Click the button below to stop PHP Speedy on your site.";		
	} else {
	$this->vars['title'] = "Activation";
	$this->vars['action'] = "activate_speedy";		
	$this->vars['intro'] = "Ready to start PHP Speedy? Just click the button below to activate. You can then deactivate at any time from this page.";			
	}	
	
	$this->render('private.php_speedy.page_active_deactive', $this->vars);
	
	
	}
	
	
	//Deletey
	function delete_test_files() {
	
		if(is_array($this->compressor->process_report['scripts'])) {
			foreach($this->compressor->process_report['scripts'] AS $key=>$value) {
			
				if(file_exists($value['to'])) {
				unlink($value['to']);
				}	
			
			}
		}
	
	
	}	
	
	//Everything must go
	function delete_all_in_cache_dir() {
	
		$path = $this->options['cache_dir']['value'];	
		$files = $this->compressor->get_files_in_dir($path);
			
		foreach($files AS $file) {
				
			if (strstr($file,"_cmp_")) {
				if(file_exists($path . "/" . $file)) {
				unlink($path . "/" . $file);
				}
			} // end if
	
		}
	
	
	
	}
	
	//Build menus
	function menu_system() {
			
		//Assign default option
		if(!$this->input['sub']) {
		$this->input['sub'] = "page_configure"; //Default is the options page
		}
		
		//Create the menu for the options here and display menu
		$this->create_submenu();
		$vars['url']  = $this->base_admin_url;		
		$vars['submenu'] = $this->submenu;
		$this->render('private.php_speedy.menu', $vars);
				
		$func = $this->input['sub'];
		if(method_exists($this,$func)) {
		$this->$func(); //Run the function for the subpage
		}
	
	
	}
	
	//Create the submenus
	function create_submenu() {
	
	$menu['page_configure'] = "Configure";
		
	if(!empty($this->compress_options['javascript_cachedir'])) {
	$menu['test_config'] = "Test Configuration";
	
		if(!empty($this->compress_options['active'])) {
		$menu['activate_deactivate'] = "Deactivate";	
		} else {
		$menu['activate_deactivate'] = "Activate";	
		}
	
	}	
	
	
		foreach($menu AS $key=>$value) {
			if($this->input['sub'] == $key) {
			$selected = true;
			} else {
			$selected = false;
			}
			$this->submenu[$key] = array('name'=>$value,
										 'id'=>$key,
										 'selected'=>$selected);		
		
		}
		
	}	
	
	//Say
	function print_message($msg) {
	
	echo "<div style='margin:20px'><span style='padding:10px;background: #FFF6BF; color: #817134; border-color: #FFD324;'>" . $msg . "</span></div>";
	
	
	}
	

	/**
	 * Renders a section of display code.
	 * 
	 **/	
	function render ($file_name, $vars = array ())
	{
		//Set variable names
		foreach ($vars AS $key => $val) {
			$$key = $val;
		}

		if (file_exists (TEMPLATEPATH."/view/$file_name.php")) {
			include (TEMPLATEPATH."/view/$file_name.php");
			
		 } else if (file_exists ($this->plugin_base. "/view/" . "$file_name.php")) {		 
		 
		 	include ($this->plugin_base. "/view/"."$file_name.php");					
			
		 } else {			
			echo "
			<body style='font-family:verdana;font-size:11px'>
			<p>
			Rendering of template $file_name.php failed. 
			<br/>Debug info:		
				<p>Looking for file in: 
					<ul>
						<li>" . TEMPLATEPATH."/view/$file_name.php" . "</li>
						<li>" . $this->plugin_base . "/view/"."$file_name.php" ."</li>
					</ul>			
				</p>		
			</p>
			</body>";
		 }
	}
	

	
	
} // end class

//Utility class
class php_speedy_wp_utility {
	
	//Constructor
	function php_speedy_wp_utility($array=null) {
	
		if(is_array($array)) {
			foreach($array AS $key=>$value) {
			$this->$key = $value;
			}
		}
		
			
	}

	//Check dir for file permissions
	function file_check_directory($directory) {

	  $directory = rtrim($directory, '/\\');
	  		
	  // Check to see if the directory or file is writable.
	  if (!is_writable($directory)) {	  
		if (@chmod($directory, octdec("0777"))) {
		return true;
		} else {
		return false;
		}
	  } else {	
	  return true;	
	  }

	}
	
	/**
	 * Version of realpath that will work on systems without realpath (Thanks to Search Unleashed)
	 *
	 * @param string $path The path to canonicalize
	 * @return string Canonicalized path
	 **/
	
	function realpath ($path)
	{
		$path = str_replace ('~', $_SERVER['DOCUMENT_ROOT'], $path);
		if (function_exists ('realpath'))
			return realpath ($path);
		else if (DIRECTORY_SEPARATOR == '/')
		{
	    // canonicalize
	    $path = explode (DIRECTORY_SEPARATOR, $path);
	    $newpath = array ();
	    for ($i = 0; $i < sizeof ($path); $i++)
			{
				if ($path[$i] === '' || $path[$i] === '.')
					continue;
					
				if ($path[$i] === '..')
				{
					array_pop ($newpath);
					continue;
				}
				
				array_push ($newpath, $path[$i]);
	    }
	
	    $finalpath = DIRECTORY_SEPARATOR.implode (DIRECTORY_SEPARATOR, $newpath);
      return $finalpath;
		}
		
		return $path;
	}	
	
	//Recursive scandir
	function scan_directory_recursively($directory, $filter=FALSE) {
	
     if(substr($directory,-1) == '/')
     {
         $directory = substr($directory,0,-1);
     }
     if(!file_exists($directory) || !is_dir($directory))
     {
         return FALSE;
     }elseif(is_readable($directory))
     {
         $directory_list = opendir($directory);
         while($file = readdir($directory_list))
         {
             if($file != '.' && $file != '..')
             {
                 $path = $directory.'/'.$file;
                 if(is_readable($path))
                 {
                     $subdirectories = explode('/',$path);
                     if(is_dir($path))
                     {
                         $directory_tree[] = array(
                            'path'      => $path,
                             'name'      => end($subdirectories),
                             'kind'      => 'directory',
                             'content'   => $this->scan_directory_recursively($path, $filter));
                     }elseif(is_file($path))
                     {
                         $extension = end(explode('.',end($subdirectories)));
                         if($filter === FALSE || $filter == $extension)
                         {
							$thename = str_replace(".".$extension,"",end($subdirectories));
							$this->file_tree[$thename] = array(
                             'path'        => $path,
                             'name'        => $thename,
                             'extension' => $extension,
                             'size'        => filesize($path),
                             'kind'        => 'file');
							 						 
                             $directory_tree[] = array(
                             'path'        => $path,
                             'name'        => end($subdirectories),
                             'extension' => $extension,
                             'size'        => filesize($path),
                             'kind'        => 'file');
                         }
                     }
                 }
             }
         }
         closedir($directory_list); 
         return $directory_tree;
     }else{
         return FALSE;    
     }


	 }	
	 
 
	 

}
?>