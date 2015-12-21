<?php
/**
 * Gzips and minifies the JavaScript and CSS within the head tags of a page. 
 * Can also gzip and minify the page itself
 *
 **/
class compressor {

	/**
	* Constructor
	* Sets the options and defines the gzip headers
	**/
	function compressor($options=false) {
	
		if(!empty($options['skip_startup'])) {
		return;
		}	

		//Allow merging of other classes with this one
		foreach($options AS $key=>$value) {
		$this->$key = $value;
		}

		//Set options
		$this->set_options();
		
		//Define the gzip headers
		$this->set_gzip_headers();
		
		//Start things off
		$this->start();
	
	}
	
	/**
	* Options are read from config.php
	**/
	function set_options() {
	
	   //Set paths with new options
	   $this->view->set_paths($this->options['document_root']);
		
	   //Read in options	   			   	   	
	   $full_options = array("javascript"=>array("cachedir"=>$this->options['javascript_cachedir'],
										   "gzip"=>$this->options['gzip']['javascript'],
										   "minify"=>$this->options['minify']['javascript'],
										   "far_future_expires"=>$this->options['far_future_expires']['javascript']													   
										   ),
							  "css"=>array("cachedir"=>$this->options['css_cachedir'],
										   "gzip"=>$this->options['gzip']['css'],
										   "minify"=>$this->options['minify']['css'],				
										   "far_future_expires"=>$this->options['far_future_expires']['css']													   										   									   
										   ),
							   "page"=>array("gzip"=>$this->options['gzip']['page'],
											 "minify"=>$this->options['minify']['page'],
											)													   
					          );	
							  	
		$this->options = $full_options; //overwrite ethe options array that we passed in
					
		//Make sure cachedir does not have trailing slash
		foreach($this->options AS $key=>$option) {			
			   if(!empty($option['cachedir'])) {
				   if(substr($option['cachedir'],-1,1) == "/") {
				   $cachedir = substr($option['cachedir'],0,-1); 
				   $option['cachedir'] = $cachedir;
				   }			   
			   }
			   $this->options[$key] = $option;			
		}
		
	$this->options['show_timer'] = false; //time the javascript and css compression?
				
							   
	}	

	
	/**
	* Start saving the output buffer
	* 
	**/	
	function start() {
		
	ob_start();	
	
	}
	
	/**
	* Compress passes content directly
	* 
	**/	
	function compress($content) {
				
	$this->finish($content);
	
	}
	
	/**
	* Tells the script to ignore certain files
	* 
	**/	
	function ignore($files=false) {
					
		if(!empty($files)) {
		$files_array = explode(",",$files);
		}
		
		//Ignore these files		
		if(!empty($files_array)) {
			foreach($files_array AS $key=>$value) {
			$this->ignore_files[] = trim($value);		
			}
		}
			
	}	
	
			

	/**
	* Do work and return output buffer
	*
	**/	
	function finish($content=false) {
		
	$this->runtime = $this->startTimer();
	$this->times['start_compress'] = $this->returnTime($this->runtime);
	
	if(!$content) {
	$this->content = ob_get_clean();
	} else {
	$this->content = $content;
	}
		
	//Run the functions specified in options
	foreach($this->options AS $func=>$option) {
		if(method_exists($this,$func)) {
			if(!empty($option['gzip']) || !empty($option['minify']) || !empty($option['far_future_expires'])) {
			$this->$func($option,$func);
			}
		}
	}
		
	//Delete old cache files
	if(!empty($this->compressed_files) && is_array($this->compressed_files)) {
	$this->compressed_files_string = implode("",$this->compressed_files); //Make a string with the names of the compressed files
	}
	
	if(!empty($this->options['cleanup']['on'])) {
	$this->do_cleanup(); //Delete any files that don't match the string
	}
	
	$this->times['end'] = $this->returnTime($this->runtime);
	
	//Echo content to the browser
	if(empty($this->supress_output)) {
		if(!empty($this->return_content)) {
		return $this->content;
		} else {
		echo $this->content;
		}
	}
	
	
	
	}

	/**
	* GZIP and minify the javascript as required
	*
	**/	
	function javascript($options,$type) {
	
	//Remove any files in the remove list
	$this->do_remove();
					
	$this->content = $this->do_compress(array('cachedir'=>$options['cachedir'],
					 						  'tag'=>'script',
										      'type'=>'text/javascript',
										      'ext'=>'js',
										      'src'=>'src',
										      'self_close'=>false,
											  'gzip'=>$options['gzip'],
											  'minify'=>$options['minify'],		
											  'far_future_expires'=>$options['far_future_expires'],
											  'header'=>$type,
											  'save_name'=>$type),$this->content);	
	
	
	}

	/**
	* GZIP and minify the CSS as required
	*
	**/	
	function css($options,$type) {
	
	$head = $this->get_head($this->content);			
	if(!$head) { //Need a head
	return;
	}
	
	//Get links
	preg_match_all("!<link[^>]+text/css[^>]+>!is",$head,$matches);	
	if(count($matches[0]) == 0) {
	return;
	}
			
	//find variants
	foreach($matches[0] AS $link) {
		
		preg_match_all("@(rel)=[\"'](.*?)[\"']@",$link,$variants,PREG_SET_ORDER); //|media
						
		if(is_array($variants)) {
			$marker = "";
			foreach($variants AS $variant_type) {
			$marker .= $variant_type[2];	
			$return[$variant_type[1]] = $variant_type[2];
			}
		}
		
		//Sub this new marker into the link
		$marker = str_replace(" ","",$marker);
		$new_link = preg_replace("@type=('|\")(.*?)('|\")@","type=$1" . "%%MARK%%" . "$3",$link);
		$new_link = str_replace("%%MARK%%",md5($marker),$new_link);
		$this->content = str_replace($link,$new_link,$this->content);
		$return['real_type'] = $marker;
		$media_types[md5($marker)] = $return;		
					
	}
	
	//print_r($media_types);	
	$this->process_report['media_types'] = $media_types;
								
			//Compress separately for each media type	
			foreach($media_types AS $key=>$value) {
												
				$this->content = $this->do_compress(array('cachedir'=>$options['cachedir'],
														  'tag'=>'link',
														  'type'=>$key,
														  'ext'=>'css',
														  'src'=>'href',
														  'rel'=>!empty($value['rel']) ? $value['rel'] : false,	
														  'media'=>!empty($value['media']) ? $value['media'] : false,
														  'self_close'=>true,
														  'gzip'=>$options['gzip'],	
														  'minify'=>$options['minify'],
														  'far_future_expires'=>$options['far_future_expires'],												  
														  'header'=>$type,
														  'save_name'=>$type.$value['real_type']),$this->content);	
														  
				//Replace out the markers
				$this->content = str_replace($key,'text/css',$this->content);												  
													  
			}
		

	
	
	}
	
	/**
	* GZIP and minify the page itself as required
	*
	**/		
	function page($options,$type) {


		//Minify page itself
		if(!empty($options['minify'])) {
		$this->content = $this->trimwhitespace($this->content);		
		}
	
		//Gzip page itself
		if(!empty($options['gzip'])) {
		
			 $content = $this->create_gz_compress($this->content);
			 if($content) {			 
			 $this->set_gzip_header();
			 $this->content = $content;
			 }
		 
		 }
	
	
	}
	
	/**
	* Return GZIP compressed content string with header
	*
	**/			
	function create_gz_compress($content) {
	
	
		if(strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') && function_exists('gzcompress')) {
		
				$Size = strlen( $this->content );
				$Crc = crc32( $this->content );		
				 
				$content = "\x1f\x8b\x08\x00\x00\x00\x00\x00";
				
				$this->content = gzcompress( $this->content,2);
				$this->content = substr( $this->content, 0, strlen( $this->content) - 4 );
				
				$content .= ( $this->content );
				$content .= ( pack( 'V', $Crc ) );
				$content .= ( pack( 'V', $Size ) ); 			 
			 
				return $content;	
			 
		} else {
		return false;
		}
	
	
	}
	
	
	/**
	* Sets the correct gzip header
	*
	**/	
	function set_gzip_header() {	
	
		if(strpos(" ".$_SERVER["HTTP_ACCEPT_ENCODING"], "x-gzip"))	{
			$encoding = "x-gzip";
		}
		if(strpos(" ".$_SERVER["HTTP_ACCEPT_ENCODING"], "gzip")) 	{
			$encoding = "gzip";
		}
			
		if(!empty($encoding)) {
		header("Content-Encoding: ".$encoding);
		}
	
	}	
	
	/**
	* Completely remove any JS scripts in the remove list
	*
	**/	
	function do_remove() {		

	
	if(empty($this->remove_files)) {
	return;
	}
		

	//Get non-immune scripts
	$script_array = $this->get_script_array($this->content,array('cachedir'=>$options['cachedir'],
																  'tag'=>'script',
																  'type'=>'text/javascript',
																  'ext'=>'js',
																  'src'=>'src'));	
	
		//If we have scripts		
		if(is_array($script_array)) {												
		
		//Pull out remove immune
		preg_match("@<!-- REMOVE IMMUNE -->(.*?)<!-- END REMOVE IMMUNE -->@is", $this->content, $match);		
		$_immune = $match[0];		
		$this->content = str_replace($_immune,'@@@COMPRESSOR:TRIM:REMOVEIMMUNE@@@', $this->content);			
		
		
			foreach($script_array AS $script) {			
				foreach($this->remove_files AS $file) {
					if(!empty($file) && !empty($script)) {
						if(strstr($script,$file)) { //Remove the scripts from the source if they are on the remove list		
							$this->content = str_replace($script,"",$this->content);			
							$this->process_report['notice'][$script] = array('from'=>htmlspecialchars($script),
																			 'notice'=>'The file was replaced by a standard library and removed');															
						}			
					}
				}			
			}
			
			//Put back
			$this->content = str_replace("@@@COMPRESSOR:TRIM:REMOVEIMMUNE@@@",$_immune,$this->content);
			
			//Remove comments
			$this->content = str_replace("<!-- REMOVE IMMUNE -->","",$this->content);
			$this->content = str_replace("<!-- END REMOVE IMMUNE -->","",$this->content);			
			
		}

	
	}
	
	/**
	* Compress JS or CSS and return source
	*
	**/	
	function do_compress($options,$source) {	
		
		//Save the original extension
		$options['original_ext'] = $options['ext'];
		
		//Change the extension
		if(!empty($options['gzip']) || !empty($options['far_future_expires'])) {
		$options['ext'] = "php";	
		}
		
		//Set cachedir		
		$cachedir = $options['cachedir'];		
		
		//Get array of scripts
		$script_array = $this->get_script_array($source,$options);	
												
		//Get date string for making hash
		$datestring = $this->get_file_dates($script_array,$options);
				
		//If only one script found
		if(!is_array($script_array)) {
		$_script_array = array($script_array);
		} else {
		$_script_array = $script_array;
		}
			
		//Get the cache hash
		$cache_file = '_cmp_' . $options['save_name'] . '_' . md5(implode("_",$_script_array).$datestring.implode("_",$options));
		$cache_file = urlencode($cache_file);
		//echo $cache_file . "\n";
							
		//Check if the cache file exists
		if (file_exists($cachedir . '/' . $cache_file . ".$options[ext]")) {		
		$script_array = $this->get_file_locations($script_array,$options);	//Put in locations and remove certain scripts		
		$source = $this->_remove_scripts($script_array,$source);
		$newfile = $this->get_new_file($options,$cache_file);
		$source = str_replace("@@@marker@@@","",$source); //No longer use marker $source = str_replace("@@@marker@@@",$new_file,$source);
		//Move to top
		$source = preg_replace("!<head([^>]+)?>!is","$0 \n".$newfile."\n",$source);
		return $source;
		}
			
			//If the file didn't exist, continue ...			
			$script_array = $this->get_file_locations($script_array,$options);	
																										
			//Create file
			$contents = "";
			if(is_array($script_array)) {
				foreach($script_array AS $key=>$info) {	
													
					//Get the code
					if (file_exists($info['src'])) {
					    
					   $file_contents = file_get_contents($info['src']);

						//Convert to absolute paths
						if($options['header'] == "css") { //Minify CSS
						$file_contents = $this->convert_paths_to_absolute($file_contents,$info);
						$file_contents = $this->add_media_header($file_contents,$info);
						}									   		   				   
					   
					   $contents .=  $file_contents . "\n";		
					   
			   		   $source = $this->_remove_scripts($script_array,$source);
					   					   
					}				
				
				}	
			}	
			
		if(!empty($contents)) {	
				
			//Allow for minification of javascript
			if($options['header'] == "javascript" && $options['minify'] && substr(phpversion(),0,1) == 5) { //Only minify on php5+
			$contents = $this->jsmin->minify($contents);			
			}
						
			//Allow for minification of CSS
			if($options['header'] == "css" && $options['minify']) { //Minify CSS
			$contents = $this->minify_text($contents);
			}
				
			//Allow for gzipping and headers
			if($options['gzip'] || $options['far_future_expires']) {		
			$contents = $this->gzip_header[$options['header']] . $contents;		
			}
			
			//Write to cache and display
			if($contents) {
				if ($fp = fopen($cachedir . '/' . $cache_file . '.' . $options['ext'], 'wb')) {
						fwrite($fp, $contents);
						fclose($fp);
						
						//Set permissions, required by some hosts
						chmod($cachedir . '/' . $cache_file . '.' . $options['ext'], octdec("0755")); 
						
						//Create the link to the new file
						$newfile = $this->get_new_file($options,$cache_file);						
																		
						$source = str_replace("@@@marker@@@","",$source);
						$source = preg_replace("!<head([^>]+)?>!is","$0 \n".$newfile."\n",$source);
						
						$this->process_report['scripts'][] = array('type'=>$options['header'] . " " . $options['rel'],
																   'from'=>$script_array,
												   				   'to'=>$cachedir . '/' . $cache_file . '.' . $options['ext']);						
						
				} 
			}
								
		}
		
		return $source;
	
	}
	
	/**
	* Replaces the script or css links in the source with a marker
	*
	*/
	function _remove_scripts($script_array,$source) {	
	
		$maxKey = array_pop(array_keys($script_array));
		
		foreach($script_array AS $key=>$value) {
		
			if($key == $maxKey) { //Remove script
			$source = str_replace($value['location'],"@@@marker@@@",$source);
			} else {
			$source = str_replace($value['location'],"",$source);
			}
		
		}
		return $source;
	
	
	}
	
	/**
	* Returns the filename for our new compressed file
	*
	**/
	function get_new_file($options,$cache_file) {
		
		
	$relative_cachedir = str_replace($this->view->prevent_trailing_slash($this->unify_dir_separator($this->view->paths['full']['document_root'])),"",$this->view->prevent_trailing_slash($this->unify_dir_separator($options['cachedir'])));
		
	$newfile = "<" . $options['tag'] . " type=\"" . $options['type'] . "\" $options[src]=\"http://" . $_SERVER['HTTP_HOST'] . "/" . $this->view->prevent_leading_slash($relative_cachedir) ."/$cache_file." . $options['ext'] . "\"";
						
						if(!empty($options['rel'])) {
						$newfile .= " rel=\"" . $options['rel'] . "\"";
						}
						
						if(!empty($options['media'])) {
						$newfile .= " media=\"" . $options['media'] . "\"";
						}						
						
						if(!empty($options['self_close'])) {
						$newfile .= " />";
						} else {
						$newfile .= "></" . $options['tag'] . ">";
						}
						
		$this->compressed_files[] = $newfile;				
						
		return $newfile;	
	
	
	}
	
	/**
	* Returns the last modified dates of the files being compressed
	* In this way we can see if any changes have been made
	**/	
	function get_file_dates($files,$options) {
		
		$files = $this->get_file_locations($files,$options);
						
		if(!is_array($files)) {
		return;
		}
				
		foreach($files AS $key=>$value) {
			if(file_exists($value['src'])) {
				$thedate = filemtime($value['src']);
				$dates[] = $thedate;
			}
		}
		
		if(is_array($dates)) {
		return implode(".",$dates);
		}		
	
	
	}
	
	/**
	* Gets an array of scripts/css files to be processed
	* 
	**/		
	function get_script_array($source,$options) {	
	
		$head = $this->get_head($source);			
		if($head) {
		$regex = "!<" . $options['tag'] . "[^>]+type=['\"](" . $options['type'] . ")['\"]([^>]+)?>(</" . $options['tag'] . ">)?!is";
		preg_match_all($regex, $head, $matches);
		}
							
		if(!empty($matches[0])) {				
		$script_array = $matches[0];
		} else {
		$script_array = "";
		}
						
		if(empty($script_array)) { //No file
		return $source;
		}
		
		//Make sure src element present
		foreach($script_array AS $key=>$value) {
			if(!strstr($value,$options['src'])) {
			unset($script_array[$key]);
			}
		}

				
		//Remove empty sources and any externally linked files
		foreach($script_array AS $key=>$value) {
		$regex = "!" . $options['src'] . "=['\"](.*?)['\"]!is";
		preg_match($regex, $value, $src);
			if(!$src[1]){
			unset($script_array[$key]);
			}
			if(strlen($src[1])> 7 && strcasecmp(substr($src[1],0,7),'http://')==0) {
				if(!strstr($src[1],$_SERVER['HTTP_HOST'])) {
				unset($script_array[$key]);		
				$this->process_report['skipped'][$src[1]] = array('from'=>$src[1],
													 'reason'=>'Cannot compress external files');												
				}			
			}
			
		}		
		
				
		return $script_array;

	}
	
	/**
	* Gets the path locations of the scripts being compressed
	* 
	**/		
	function get_file_locations($script_array,$options) {
	
		if(!is_array($script_array)) {
		return;
		}
					
		//Remove empty sources
		foreach($script_array AS $key=>$value) {
		preg_match("!" . $options['src'] . "=['\"](.*?)['\"]!is", $value, $src);
			if(!$src[1]) {
			unset($script_array[$key]);
			}
		}			
		//Create file
		foreach($script_array AS $key=>$value) {
			//Get the src
			preg_match("!" . $options['src'] . "=['\"](.*?)['\"]!is", $value, $src);
			$src[1] = str_replace("http://".$_SERVER['HTTP_HOST'],"",$src[1]);
						
			if(substr($src[1],0,1) == "/") {
			$current_src = $this->view->prevent_trailing_slash($this->view->paths['full']['document_root']) . $src[1];
			} else {
			$current_src = $this->view->paths['full']['current_directory'] . $src[1];
			}			
			
			if($current_src != $this->strip_querystring($current_src)) {
			$this->process_report['notice'][$current_src] = array('from'=>$current_src,
													 			  'notice'=>'The querystring was stripped from this script');				
			}
			
			$current_src = $this->strip_querystring($current_src);
						
			//Make sure script exists
			if(file_exists($current_src)) {
						
				//Make sure script has the correct extension
				$extentsion_length = strlen($options['original_ext']);
				if(".".substr($this->view->get_basename($current_src),(-1*$extentsion_length)) == ".".$options['original_ext']) {			
				$return_array[] = array('src'=>$current_src,
										'location'=>$value);
				} else {
				$this->process_report['skipped'][$current_src] = array('from'=>$current_src,
														 'reason'=>'Must have ' . $options['original_ext'] . ' extension');
				}
			
			} else {
				if(!strstr($current_src,'php_speedy_control')) {
					$this->process_report['skipped'][$current_src] = array('from'=>$current_src,
															 'reason'=>'Not on server');						
				}
			}
											
		}
		
		//Remove ignore files
		if(!empty($this->ignore_files)) {
			foreach($return_array AS $return_key=>$file_list) {
				foreach($this->ignore_files AS $ignore) {
					if(strstr($this->view->get_basename($file_list['src']),$ignore)) {
					$this->process_report['notice'][$file_list['src']] = array('from'=>$file_list['src'],
													 			  'notice'=>'The file was on the ignore list and skipped');									
					unset($return_array[$return_key]);
					}			
				}		
			}
		}
		
				
		return $return_array;
	
	}

	/**
	* Sets the headers to be sent in the javascript and css files
	* 
	**/	
	function set_gzip_headers() {
	
	//When will the file expire?
	$offset = 6000000 * 60 ;
	$ExpStr = "Expires: " . 
	gmdate("D, d M Y H:i:s",
	time() + $offset) . " GMT";
	
	$types = array("css","javascript");
	
	foreach($types AS $type) {
	
		$this->gzip_header[$type] = "";
	
		if(!empty($this->options[$type]['gzip'])) { ////ob_start ("ob_gzhandler");					
			$this->gzip_header[$type] = '<?php	
		// Gzip encode the contents of the output buffer.
		function compress_output_option($output) {
		if(strlen($output) >= 1000) {
		
			$compressed_out = "\x1f\x8b\x08\x00\x00\x00\x00\x00";
			$compressed_out .= substr(gzcompress($output, 2), 0, -4);
		
			if(strpos(" ".$_SERVER["HTTP_ACCEPT_ENCODING"], "x-gzip"))	{
				$encoding = "x-gzip";
			}
			if(strpos(" ".$_SERVER["HTTP_ACCEPT_ENCODING"], "gzip")) 	{
				$encoding = "gzip";
			}
		
			header("Content-Encoding: ".$encoding);
			return $compressed_out;
		} else {
			return $output;
		}
		}
		if (strstr($_SERVER["HTTP_ACCEPT_ENCODING"], "gzip") || strstr($_SERVER["HTTP_ACCEPT_ENCODING"], "x-gzip")) {
			if(function_exists("gzcompress")) {
			ob_start("compress_output_option");
			} else {
			ob_start ("ob_gzhandler");
			}		
		}
		?>';	
		}
	
		if(!empty($this->options[$type]['far_future_expires'])) {
			$this->gzip_header[$type] .= '<?php	
				header("Cache-Control: must-revalidate");
				header("' . $ExpStr . '");
				?>';	
		}
		
			$this->gzip_header[$type] .= '<?php	
				header("Content-type: text/' . $type .'; charset: UTF-8");
				?>';			
	
	} // end FE
	
			
	
	}

	/**
	* Returns a path or url without the querystring
	* 
	**/	
	function strip_querystring($path) {
	
		if ($commapos = strpos($path, '?')) {
			return substr($path, 0, $commapos);
		} else {
			return $path;
		}
	
	}

	/**
	* Strips whitespace and comments from a text string
	* 
	**/	
	function minify_text($txt) {
	
		// Compress whitespace.
		$txt = preg_replace('/\s+/', ' ', $txt);
		// Remove comments.
		$txt = preg_replace('/\/\*.*?\*\//', '', $txt);
		//Further minification (Thanks to Andy & David)		
		//$txt = preg_replace('/\s*(,|;|:|{|})\s/','$1', $txt);
		
		return $txt;
	
	}

	/**
	* Safely trim whitespace from an HTML page
	* Adapted from smarty code http://www.smarty.net/
	**/		
	function trimwhitespace($source)
	{
		// Pull out the script blocks
		preg_match_all("!<script[^>]+>.*?</script>!is", $source, $match);
		$_script_blocks = $match[0];
		$source = preg_replace("!<script[^>]+>.*?</script>!is",
							   '@@@COMPRESSOR:TRIM:SCRIPT@@@', $source);
	
		// Pull out the pre blocks
		preg_match_all("!<pre>.*?</pre>!is", $source, $match);
		$_pre_blocks = $match[0];
		$source = preg_replace("!<pre>.*?</pre>!is",
							   '@@@COMPRESSOR:TRIM:PRE@@@', $source);
	
		// Pull out the textarea blocks
		preg_match_all("!<textarea[^>]+>.*?</textarea>!is", $source, $match);
		$_textarea_blocks = $match[0];
		$source = preg_replace("!<textarea[^>]+>.*?</textarea>!is",
							   '@@@COMPRESSOR:TRIM:TEXTAREA@@@', $source);
	
		// remove all leading spaces, tabs and carriage returns NOT
		// preceeded by a php close tag.
		$source = trim(preg_replace('/((?<!\?>)\n)[\s]+/m', '\1', $source));
		
		//Remove comments
		//$source =  preg_replace("/<!--.*-->/U","",$source); 			
	
		// replace textarea blocks
		$this->trimwhitespace_replace("@@@COMPRESSOR:TRIM:TEXTAREA@@@",$_textarea_blocks, $source);
	
		// replace pre blocks
		$this->trimwhitespace_replace("@@@COMPRESSOR:TRIM:PRE@@@",$_pre_blocks, $source);
	
		// replace script blocks
		$this->trimwhitespace_replace("@@@COMPRESSOR:TRIM:SCRIPT@@@",$_script_blocks, $source);
	
		return $source;
	}

	/**
	* Helper function for trimwhitespace
	* 
	**/		
	function trimwhitespace_replace($search_str, $replace, &$subject) {
		$_len = strlen($search_str);
		$_pos = 0;
		for ($_i=0, $_count=count($replace); $_i<$_count; $_i++)
			if (($_pos=strpos($subject, $search_str, $_pos))!==false)
				$subject = substr_replace($subject, $replace[$_i], $_pos, $_len);
			else
				break;
	
	}	
	
	/**
	* Gets the directory we are in
	* 
	**/		
	function get_current_path($trailing=false) {
	
	
	   $current_dir = $this->view->paths->relative->current_directory;		
	   
	   //Remove trailing slash
	   if($trailing) {
		   if(substr($current_dir,-1,1) == "/") {
		   $current_dir = substr($current_dir,0,-1); 
		   }
	   }
	
	   return $current_dir;
	
	}

	/**
	* Gets the head part of the $source
	* 
	**/			
	function get_head($source) {
			
		preg_match("!<head([^>]+)?>.*?</head>!is", $source, $matches);
				
		if(!empty($matches[0])) {
		
			$head = $matches[0];
			
			// Pull out the comment blocks, so as to avoid touching conditional comments
			$head = preg_replace("@<!--.*?-->@is",
								   '@@@COMPRESSOR:TRIM:HEADCOMMENT@@@', $head);		
						
			return $head;	
			
		}
	
	
	}
	
	/**
	* Removes old cache files
	* 
	**/		
	function do_cleanup() {
		
	//Get all directories
	foreach($this->options AS $key=>$value) {
		if(!empty($value['cachedir'])) {
			$active_dirs[] = $value['cachedir'];
		}
	}
			
		if(!empty($active_dirs)) {	
			foreach($active_dirs AS $path) {
			
			$files = $this->get_files_in_dir($path);
					
				foreach($files AS $file) {
						
					if (strstr($file,"_cmp_") && !strstr($this->compressed_files_string,$file)) {
						if(file_exists($path . "/" . $file)) {
						unlink($path . "/" . $file);
						}
					} // end if
			
				}
			
			}
		}
	

	
	}	
	
	/**
	* Returns list of files in a directory
	* 
	**/	
	function get_files_in_dir($path) {
		
	// open this directory 
	$myDirectory = opendir($path);
	
	// get each entry
	while($entryName = readdir($myDirectory))
	{
		$dirArray[] = $entryName;
	}
	// close directory
	closedir($myDirectory);	
	
	return $dirArray;
	
	
	}
	
	//Adds CSS media info
	function add_media_header($content,$path) {
	
	preg_match("@(media)=[\"'](.*?)[\"']@",$path['location'],$media); //|media
	
		if($media[2]) {
		$content = "@media " . $media[2] . " {" . $content;
		$content .= " }";
		}
	
	return $content;
	
	}
	
	
	//Find background images in the CSS and convert their paths to absolute
	function convert_paths_to_absolute($content,$path) {
		
		preg_match_all( "/url\((.*?)\)/is",$content,$matches);
				
		if(count($matches[1]) > 0) {
		
			$counter = 0;
			foreach($matches[1] AS $key=>$file) {
			
			$counter++;
			
				$original_file = trim($file);
				$file = preg_replace("@'|\"@","",$original_file);
		
				if(substr($file,0,1) != "/" && substr($file,0,5) != "http:") { //Not absolute
														
					$full_path_to_image = str_replace($this->view->get_basename($path['src']),"",$path['src']);
					$absolute_path = "/". $this->view->prevent_leading_slash(str_replace($this->unify_dir_separator($this->view->paths['full']['document_root']),"",$this->unify_dir_separator($full_path_to_image . $file)));								
										
					$marker = md5($counter);	
					$markers[$marker] = $absolute_path;					
					
					$content = str_replace($original_file,$marker,$content);				
				}
			
			}
		}
		
		if(!empty($markers) && is_array($markers)) {
			//Replace the markers for the real path
			foreach($markers AS $md5=>$real_path) {
			$content = str_replace($md5,$real_path,$content);
			}
		}
		
		return $content;
	
	}
	
	//Make the sep the same
	function unify_dir_separator($path) {
	
		if (DIRECTORY_SEPARATOR != '/') {
				return str_replace (DIRECTORY_SEPARATOR, '/', $path);
		} else {
				return $path;
		}	
	
	
	}
	
	
	//Start script timing
	function startTimer() {
	   $mtime = microtime();
	   $mtime = explode(" ",$mtime);
	   $mtime = $mtime[1] + $mtime[0];
	   $starttime = $mtime;
	   return $starttime;
	} 
	
	//Return current time
	function returnTime($starttime) {
		$mtime = microtime();
	   $mtime = explode(" ",$mtime);
	   $mtime = $mtime[1] + $mtime[0];
	   $endtime = $mtime;
	   $totaltime = ($endtime - $starttime);
	   return $totaltime;
	}	
	
		

} // end class



?>