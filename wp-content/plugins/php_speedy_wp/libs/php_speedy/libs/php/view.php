<?php
/**
 * Very basic templating class
 * 
 *
 **/
class compressor_view {

	/**
	* 
	* 
	**/
	function compressor_view($options=null) {
		
	$this->set_paths();	
	
	}

	/**
	 * Sets the paths
	 * 
	 **/	
	function set_paths($document_root=null) {

	if($document_root && !empty($_SERVER['SCRIPT_NAME'])) {	//Get the view directory	
	$this->paths['full']['current_directory'] = $document_root . str_replace($this->get_basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
	} else if(!empty($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['SCRIPT_NAME'])) {
	$this->paths['full']['current_directory'] = $_SERVER['DOCUMENT_ROOT'] . str_replace($this->get_basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
	}
	
	if(!file_exists($this->paths['full']['current_directory'])) {
	$this->paths['full']['current_directory'] = getcwd();
	}
	$this->paths['full']['current_directory'] = $this->ensure_trailing_slash($this->paths['full']['current_directory']);

	//Save doc root
	$this->paths['full']['document_root'] = $this->ensure_trailing_slash($_SERVER['DOCUMENT_ROOT']);
	
	//Set the current relative path
	$this->paths['relative']['current_directory'] = str_replace($_SERVER['DOCUMENT_ROOT'],"",$this->paths['full']['current_directory']);
	
	//Set the view directory
	$this->paths['full']['view'] = $this->paths['full']['current_directory'] . "view/";	
	
	//Set the css directory
	$this->paths['relative']['css_directory'] = $this->paths['relative']['current_directory'] . "libs/css/";		
		
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
	 
	 	if(substr($path,-1,1) == "/" || substr($path,-1,1) == "\\") {
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
	 * Renders a section of display code.
	 * 
	 **/	
	function render ($file_name, $vars = array ())
	{
		//Set variable names
		foreach ($vars AS $key => $val) {
			$$key = $val;
		}

		if (file_exists ($this->paths['full']['view']."$file_name.php")) {
			include ($this->paths['full']['view']."$file_name.php");
			
		 } else if (file_exists ("view/" . "$file_name.php")) {		 
		 
		 	include ("view/"."$file_name.php");					
			
		 } else {			
			echo "
			<body style='font-family:verdana;font-size:11px'>
			<p>
			Rendering of template $file_name.php failed. 
			<br/>Debug info:		
				<p>Looking for file in: 
					<ul>
						<li>" . $this->paths['full']['view']."$file_name.php" . "</li>
						<li>" . "view/"."$file_name.php" ."</li>
					</ul>			
				</p>
				<p>Server info:
					<ul>
						<li><strong>Document root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</li>
						<li><strong>Script name:</strong> " . $_SERVER['SCRIPT_NAME']."</li>
					</ul>			
				</p>				
			</p>
			</body>";
		 }
	}

	/**
	 * Version of realpath that will work on systems without realpath
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

	/**
	 * Version of basename works on nix and windows
	 *
	 **/	
	function get_basename($filename) {
	
	$basename = preg_replace( '/^.+[\\\\\\/]/', '', $filename );
	
	return $basename;
	
	}
	
}
?>