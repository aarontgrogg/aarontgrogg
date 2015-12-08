<?php
/*
Plugin Name: Save with keyboard
Plugin URI: http://wordpress.org/extend/plugins/save-with-keyboard
Description: This plugin lets you save your posts, pages, theme and plugin files in the most natural way: pressing Ctrl+S (or Cmd+S on Mac).
Author: Mattia Trapani (zupolgec)
Author URI: http://mtrapani.com
Version: 1.1
License: WTFPL (http://sam.zoy.org/wtfpl)
*/

if( is_admin() ) {
	// array of admin pages and buttons to "click" with Ctrl+S in those pages
	$button_to_click = array(
		'post.php' => 'publish',
		'post-new.php' => 'save-post',
		'theme-editor.php' => 'submit',
		'plugin-editor.php' => 'submit'
	);
	
	foreach($button_to_click as $page => $button_id) {
		add_action( 'admin_footer-'.$page, 'swk_add_script' );
	}
}

function swk_add_script() {
	global $pagenow, $button_to_click;
	?>
	<script>
		var button_id = "<?php echo $button_to_click[$pagenow]; ?>";
		
		jQuery('#'+button_id).attr('title', 'Ctrl+S or Cmd+S to click');
		
		jQuery(document).keydown( function(e) {
			if( (e.keyCode || e.which) == 83 && (e.ctrlKey || e.metaKey) ) {
				jQuery('#'+button_id).click();
				
				return false;
			}
		});
	</script>
<?php
}
