<?php
/*
Plugin Name: Micro Anywhere
Plugin URI: http://www.undergroundwebdesigns.com/micro-anywhere-wordpress.html
Description: Adds microformat buttons to the wordpress post and pages editor, letting you embed microformated data into your blog.
Version: 1.3
Author: Alex Willemsma
Author URI: http://www.undergroundwebdesigns.com
*/
?>
<?php
/*  Copyright 2008  Alex Willemsma  (email : webmaster@undergroundwebdesigns.com)

	This file is part of Micro Anywhere

    Micro Anywhere is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Mico Anywhere is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Micro Anywhere.  If not, see <http://www.gnu.org/licenses/>.
*/
?>
<?php

function micro_addbuttons() {
   // Don't bother doing this stuff if the current user lacks permissions
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
     return;
 
   // Add only in Rich Editor mode
   if ( get_user_option('rich_editing') == 'true') {
     add_filter("mce_external_plugins", "micro_enableHCal");
     add_filter('mce_buttons', 'micro_register_button');
   }
}
 
function micro_register_button($buttons) {
   array_push($buttons, "separator", "hcalendar", "hcard");
   return $buttons;
}
 
// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
function micro_enableHCal ($plugins)
{
   $plugins['hcalendar'] = get_bloginfo('url').'/wp-content/plugins/micro-anywhere/hcalendar/editor_plugin.js';
   $plugins['hcard'] = get_bloginfo('url').'/wp-content/plugins/micro-anywhere/hcard/editor_plugin.js';
   return $plugins;
}
 
// init process for button control
add_action('init', 'micro_addbuttons');

function my_refresh_mce($ver) {
  ++$ver; // or $ver .= 3; or ++$ver; etc.
  return $ver;
}
add_filter('tiny_mce_version', 'my_refresh_mce');
?>
