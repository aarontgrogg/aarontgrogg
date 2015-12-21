<?php
// add category nicenames in body and post class
	function atg_body_class($classes) {
		$url = $_SERVER['REQUEST_URI'];
		$bodyclass = trim(str_replace('/',' ',$url));
		return $bodyclass;
	}
	//add_filter('body_class', 'atg_body_class');
?>
