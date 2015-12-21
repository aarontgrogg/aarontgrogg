<?
require("controller/compressor.php");
require("libs/php/view.php"); //Include this for path getting help

//We need to know the config
require("config.php");

//Con. the view library
$view = new compressor_view();

//Con. the js min library
if(substr(phpversion(),0,1) == 5) {
require_once('libs/php/jsmin.php');
$jsmin = new JSMin($contents);
}

//Con. the compression controller
$compressor = new compressor(array('view'=>$view,
								   'options'=>$compress_options,
								   'jsmin'=>$jsmin)
							 );
?>