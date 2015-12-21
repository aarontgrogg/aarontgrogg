<?php
#########################################
## Compressor option file ##############
#########################################
## Path info
$compress_options['javascript_cachedir'] = "/home/aarontgrogg/aarontgrogg.com/wp-content/plugins/php_speedy_wp/cache";
$compress_options['css_cachedir'] = "/home/aarontgrogg/aarontgrogg.com/wp-content/plugins/php_speedy_wp/cache";
## Comma separated list of JS Libraries to include
$compress_options['js_libraries'] = "";
## Ignore list
$compress_options['ignore_list'] = "";
## Minify options
$compress_options['minify']['javascript'] = "1";
$compress_options['minify']['page'] = "1";
$compress_options['minify']['css'] = "1";
## Gzip options
$compress_options['gzip']['javascript'] = "1";
$compress_options['gzip']['page'] = "1";
$compress_options['gzip']['css'] = "1";
## Versioning
$compress_options['far_future_expires']['javascript'] = "1";
$compress_options['far_future_expires']['css'] = "1";
## On or off 
$compress_options['active'] = "1";
## Display a link back to PHP Speedy
$compress_options['footer']['text'] = "0";
$compress_options['footer']['image'] = "0";
## Display a link back to PHP Speedy
$compress_options['cleanup']['on'] = "1";
#########################################
?>