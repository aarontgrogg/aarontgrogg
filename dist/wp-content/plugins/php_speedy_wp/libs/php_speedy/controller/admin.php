<?php
/**
 * 
 * 
 *
 **/
class admin {

	/**
	* Constructor
	* Sets the options and defines the gzip headers
	**/
	function admin($options=null) {
	
		if(!empty($options['skip_startup'])) {
		return;
		}
	
		//Ensure no caching
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', mktime(0, 0, 0, date("m"), date("d")-2, date("Y"))).' GMT');
		header('Expires: '.gmdate('D, d M Y H:i:s', mktime(0, 0, 0, date("m"), date("d")-1, date("Y"))).' GMT');		
		header("Cache-Control: private");	
		header("Pragma: no-cache");	

		foreach($options AS $key=>$value) {
		$this->$key = $value;
		}
		
		//Set name of options file
		$this->options_file = "config.php";
		require_once($this->options_file);
		$this->compress_options = $compress_options;
		
		//Make sure login valid
		$this->manage_password();
		$this->password_not_required = array('install_enter_password'=>1,
											 'install_set_password'=>1
											);
		if(empty($this->password_not_required[$this->input['page']])) {
		$this->check_login();
		}
		
		//Set page functions for the installation and admin, makes sure nothing else can be run
		$this->page_functions = array('install_set_password'=>1,
									  'install_enter_password'=>1,
									  'install_stage_1'=>1,
									  'install_stage_2'=>1,
									  'install_stage_3'=>1);
		
		//Show page
		if(!empty($this->page_functions[$this->input['page']]) && method_exists($this,$this->input['page'])) {
		$func = $this->input['page'];
		$this->$func();
		}
		
	
	}

	/**
	* 
	* 
	**/	
	function install_set_password() {
	
	if(!empty($this->compress_options['username']) && !empty($this->compress_options['password'])) {


		$page_variables = array("title"=>"Enter your password for installtion",
								"paths"=>$this->view->paths,
								"page"=>'install_enter_password'); //take document root from the options file


	} else {
	
		$page_variables = array("title"=>"Set your password for installtion",
								"paths"=>$this->view->paths,
								"page"=>'install_set_password'); //take document root from the options file
	
	}	
	
		//Show the install page
		$this->view->render("admin_container",$page_variables);	
	
	
	
	}

	/**
	* 
	* 
	**/	
	function install_stage_1() {
				
	$page_variables = array("title"=>"Welcome to compressor installation!",
							"paths"=>$this->view->paths,
							"page"=>$this->input['page'],
							"document_root"=>$this->compress_options['document_root'],
							"compress_options"=>$this->compress_options);
	
	
	//Show the install page
	$this->view->render("admin_container",$page_variables);
	
	
	}
	
	/**
	* 
	* 
	**/	
	function install_stage_2() {
		
	//Save the options file
	if(!empty($this->input['user']['document_root'])) {
	$_SERVER['DOCUMENT_ROOT'] = $this->input['user']['document_root'];
	}
	if(!empty($this->input['submit'])) {
	$save = $this->save_option('[\'document_root\']',$_SERVER['DOCUMENT_ROOT']);
	}
	$this->view->set_paths(); //Set paths with the new document root
	
	$options = array('Minify'=>$this->compress_options['minify'],
	                 'GZIP'=>$this->compress_options['gzip'],
					 'Far_future_expires'=>$this->compress_options['far_future_expires']
					 );
	
	$page_variables = array("title"=>"Installation stage 2",
							"paths"=>$this->view->paths,
							"page"=>$this->input['page'],
							"message"=>$save,
							"javascript_cachedir"=>$this->view->paths['full']['current_directory'],
							"css_cachedir"=>$this->view->paths['full']['current_directory'],
							"options"=>$options,
							"compress_options"=>$this->compress_options);	
	
	//Show the install page
	$this->view->render("admin_container",$page_variables);
	
	
	}	
	
	/**
	* 
	* 
	**/	
	function install_stage_3() {
	
	//Check we can write to the specified directory
	$content = "Test";
	$test_dirs = array('javascript'=>$this->view->ensure_trailing_slash($this->input['user']['javascript_cachedir']),'css'=>$this->view->ensure_trailing_slash($this->input['user']['css_cachedir'])
					  );
	foreach($test_dirs AS $name=>$dir) {
	
		$fp = @fopen($dir."test", 'w');

		if(!$fp) {
		// unable to open file for writing
		$this->error("<p>
						Unable to write to the $name directory you specified. Please make sure the directory exists and is writable.
					 <p>
					 <p>
					 	You can usually do this from your FTP client. Just navigate to the directory, right click and look for a Properties or CHMOD option. 
					 </p>
					  <p>
					   Once you have done so, please refresh this page.
					   </p>");
		} else {
		// write the file
			fwrite($fp, $content);
			fclose($fp);
			unlink($dir."test");
		}	
		
	}	
		
	//Create the options file
	$this->options_file = "config.php";
	if(!empty($this->input['submit'])) {
		//Save the options		
		foreach($this->input['user'] AS $key=>$option) {
			if(is_array($option)) {
				foreach($option AS $option_name=>$option_value) {
				$this->save_option("['" . strtolower($key) . "']['" . strtolower($option_name) . "']",$option_value);	
				}			
			} else {
			$this->save_option("['" . strtolower($key) . "']",$option);			
			}			
		}
	}
	
	$page_variables = array("title"=>"Installation stage 3",
							"paths"=>$this->view->paths,
							"page"=>$this->input['page'],
							"message"=>"Configuration saved");	
	
	//Show the install page
	$this->view->render("admin_container",$page_variables);
	
	
	}		
	
	/**
	* Saves an admin option
	* 
	**/	
	function save_option($option_name,$option_value) {	
	
	//See if file exists	
	$option_file = $this->view->paths['full']['current_directory'].$this->options_file;
		
	if(file_exists($option_file)) {
		
		$content = file_get_contents($option_file);
		$content = preg_replace("@(" . $this->regex_escape($option_name) . ") = \"(.*?)\"@is","$1 = \"" . $option_value . "\"",$content);

		$fp = @fopen($option_file, 'w');

		if(!$fp) {
		// unable to open file for writing
		$this->error('<p>
						Unable to open the config file for writing. Please change the config.php file so that is it writable.
					  </p>
					  <p>
					   You can usually do this from your FTP client. Just navigate to <strong>' . $option_file .'</strong> , right click the file, and look for a Properties or CHMOD option. Set to 777, or "write".
					   </p>
					   <p>
					   Once you have done so, please refresh this page.
					   </p>');
		} else {
		// write the file
			fwrite($fp, $content);
			fclose($fp);
			return "Saved " . $option_name;
		}
	
	
	} else {
	
		$this->error('Config file does not exist. Please download the full script from http://www.aciddrop.com');
	
	}
	
	}
	
	/**
	* Some basic protection
	* 
	**/			
	function check_login() {
						
		if(($this->input['user']['username'] != $this->compress_options['username']) || 
			($this->input['user']['password'] != $this->compress_options['password'])) {
			
			if(!empty($this->input['user']['username'])) {
			$this->error('Login failed');
			} else {
			$this->error('You need to be logged in to view this page');
			}
			
			}
	
	
	}

	/**
	* Set the initial password
	* 
	**/		
	function manage_password() {
		
	//If posting a username and pass, md5 encode
	if(!empty($this->input['user']['username'])) {
		 
			$this->input['user']['username'] = md5($this->input['user']['username']);
			$this->input['user']['password'] = md5($this->input['user']['password']);	
			
		 //If the pass isn't there, write it
		 if(empty($this->compress_options['username']) && empty($this->compress_options['password'])) {
		 		 
			$save = $this->save_option('[\'username\']',($this->input['user']['username']));
			$save .= "<br/>" . $this->save_option('[\'password\']',($this->input['user']['password']));	
			$save .= "<br />Logged you in";
			$this->save = $save;
			
			//Update
			$this->compress_options['username'] = $this->input['user']['username'];
			$this->compress_options['password'] = $this->input['user']['password'];
		 
		 }			
								
	}	
	
	//If passing a username and pass, don't md5 encode
	if(!empty($this->input['user']['_username'])) {
		 
			$this->input['user']['username'] = ($this->input['user']['_username']);
			$this->input['user']['password'] = ($this->input['user']['_password']);						
	}			
		
	
	}
	
	/**
	* Display an error
	* 
	**/		
	function error($string) {
				
	$page_variables = array("title"=>"Oopps! Something went wrong",
							"paths"=>$this->view->paths,
							"error"=>$string,
							"page"=>'error');	
								
	//Show the install page
	$this->view->render("admin_container",$page_variables);
	die();	
	
	}	
	

	/**
	* Make safe for regex
	* 
	**/		
	function regex_escape($string) 	{
		return  addcslashes($string,'\^$.[]|()?*+{}');
	}
	
}
?>