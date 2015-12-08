<?php

/*
	Plugin Name: Navigation Menu IDs & Classes
	Plugin URI: http://aarontgrogg.com/2011/09/28/wordpress-plug-in-navigation-menu-ids-classes/
	Description: Limits WP classes to those chosen by the Theme owner, and adds page name slug as LI's ID.
	Version: 2.5
	Author: Aaron T. Grogg
	Author URI: http://aarontgrogg.com/
	License: GPLv2 or later
*/

/*
	Dev:
		http://wordpress.dev/page-d/
		plug-in deactivated:
			"menu-item menu-item-type-post_type menu-item-object-page current-menu-item page_item page-item-739 current_page_item menu-item-829"
			all are in array below
		plug-in activated:
			only gets page-name class

*/


/*	Global variables & functions */

	//	Define global variable of all possible WP navigation menu item classes
		/*
			TODO: find a way to get a dynamic list of available classes for "this" WP installation, maybe using one of these?
			- wp_list_pages() is located in wp-includes/post-template.php
			- wp_page_menu() is located in wp-includes/post-template.php
			- wp_nav_menu() is located in wp-includes/nav-menu-template.php
			- wp_get_nav_menu_items() is located in wp-includes/nav-menu.php
		*/
		$NMIC_Classes = array('menu-item','menu-item-object-category','menu-item-object-tag','menu-item-object-page','menu-item-type-post_type','menu-item-type-taxonomy','current-menu-item','current-menu-parent','current-menu-ancestor','menu-item-home','page_item','current_page_item','current_page_parent','current_page_ancestor');
	//	Sort array
		sort($NMIC_Classes);

	//	Utility function, "slugifies" string
		if ( ! function_exists( 'NMIC_slugify_string' ) ):
			function NMIC_slugify_string( $v ) {
				$v = preg_replace('/[^a-zA-Z0-9\s]/', '', $v);
				$v = str_replace(' ', '-', $v);
				$v = strtolower($v);
				return $v;
			}
		endif; // NMIC_slugify_string


/*	Admin page set-up */

	//	Add submenu link to Settings section if in Admin
		if ( ! function_exists( 'NMIC_create_admin_page' ) ):
			function NMIC_create_admin_page() {
				add_submenu_page('options-general.php', 'Navigation Menu IDs & Classes Admin', 'Navigation Menu IDs & Classes', 'administrator', 'NMIC-admin', 'NMIC_build_admin_page');
			}
		endif; // NMIC_create_admin_page
		add_action('admin_menu', 'NMIC_create_admin_page');

	//	You get this if you click the Admin link above
		if ( ! function_exists( 'NMIC_build_admin_page' ) ):
			function NMIC_build_admin_page() {
			?>
				<div id="nmic-options-wrap">
					<div class="icon32" id="icon-tools"><br /></div>
					<h2>Navigation Menu IDs &amp; Classes Admin</h2>
					<div id="instructions">
						<p>This plug-in performs several changes to the HTML of navigational menu <code>&lt;li&gt;</code> items:
							<ol>
								<li>allows you to either:
									<ol>
										<li>keep your Theme's native WordPress <code>id</code> structure (this may result in somewhat useless <code>id</code>s, such as <code>id="menu-item-828"</code>),</li>
										<li>assign an <code>id</code> based on the link's destination (which could result in duplicate <code>id</code>s, if you have multiple navigation menus on a page),</li>
										<li>or remove IDs completely from your navigation menu <code>&lt;li&gt;</code> items.</li>
									</ol>
								</li>
								<li>limits the classes that WordPress adds to only those you select from the list below, and</li>
								<li>removes any empty <code>id</code> or <code>class</code> attributes.</li>
							</ol>
						</p>
						<p>Check any <code>class</code> below that you want included in your navigational menu <code>&lt;li&gt;</code> items.</p>
					</div>
					<form id="nmic-options-form" method="post" action="options.php" enctype="multipart/form-data">
						<?php settings_fields('plugin_options'); ?>
						<?php do_settings_sections('NMIC-admin'); ?>
						<p class="submit"><input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>"></p>
					</form>
					<script>
						(function(window){
							window.NMIC = {
								// prepare for object reference to the form's checkboxes
								checkboxes : null,
								// function to check all class options
								checkall : function() {
									NMIC.checkboxes.prop('checked', true);
								},
								// function to uncheck all class options
								uncheckall : function() {
									NMIC.checkboxes.prop('checked', false);
								},
								// let's get it started!
								init : function(){
									// create local reference to options form
									var form = jQuery('#nmic-options-form');
									// create object reference to all the checkboxes in the form
									NMIC.checkboxes = form.find('input[type="checkbox"]');
									// build the "Check All | Uncheck All" HTML
									var html = '<p>'
											+ '<a href="javascript:NMIC.checkall();" title="Check all options">Check All</a> | '
											+ '<a href="javascript:NMIC.uncheckall();" title="Uncheck all options">Uncheck All</a>'
											+ '</p>';
									// And prepend to the form
									form.prepend(html);
								}
							}
							NMIC.init();
						})(window);
					</script>
				</div>
			<?php
			}
		endif; // NMIC_build_admin_page

	//	Add Admin Page CSS
		if ( ! function_exists( 'NMIC_admin_register_head' ) ):
			function NMIC_admin_register_head() {
				echo '<link rel="stylesheet" href="' .WP_PLUGIN_URL.'/navigation-menu-ids-classes/admin-style.css">'.PHP_EOL;
			}
		endif; // NMIC_admin_register_head
		add_action('admin_head', 'NMIC_admin_register_head');

	//	Register form elements
		if ( ! function_exists( 'NMIC_register_and_build_fields' ) ):
			function NMIC_register_and_build_fields() {
				// Get global reference
				global $NMIC_Classes;
				// Register WP actions
				register_setting('plugin_options', 'plugin_options', 'NMIC_form_processing');
				add_settings_section('main_section', '', 'NMIC_section_cb', 'NMIC-admin');
				// Loop through WP classes and build a listener for each
				foreach ($NMIC_Classes as $class) {
					add_settings_field($class, $class, 'create_class_option', 'NMIC-admin', 'main_section', array( 'label_for' => 'nmic_'.$class ) );
				}
			}
		endif; // NMIC_register_and_build_fields
		add_action('admin_init', 'NMIC_register_and_build_fields');

	//	Add Admin Page processing
		if ( ! function_exists( 'NMIC_form_processing' ) ):
			function NMIC_form_processing($plugin_options) {
				// Make sure at least one option was checked
				if ($plugin_options) {
					// Pass selected options to WP
					return $plugin_options;
				}
			}
		endif; // NMIC_form_processing

	//	In case you need it...
		if ( ! function_exists( 'NMIC_section_cb' ) ):
			function NMIC_section_cb() {
				// I don't do anything here, but you could if you wanted...
			}
		endif; // NMIC_section_cb

	//	Adds the checkbox & class name to the Admin options page
		if ( ! function_exists( 'create_class_option' ) ):
			function create_class_option($class) {
				$class = $class['label_for'];
				// Grab the options object
				$options = get_option('plugin_options');
				// Determine if the option was previously selected
				$checked = (isset($options[$class]) && $options[$class]) ? 'checked="checked" ' : '';
				// Output the HTML for this option
				echo '<input type="checkbox" id="'.$class.'" name="plugin_options['.$class.']" class="check-field" value="true" ' .$checked. '/>';
			}
		endif; // create_class_option


/*	Apply the plug-in functionality */

	//	Limit the nav classes to only those selected by the Theme owner
		if ( ! function_exists( 'NMIC_limit_classes' ) ):
			function NMIC_limit_classes( $oldclasses, $item ) {
				// $oldclasses = array of classes WP wants to append; $item = WP $item object
				// If there are any custom classes, push them into a new array
				$custom = get_post_meta( $item->ID, '_menu_item_classes', true );
				// Avoid pushing any empty values into $newclasses array
				if ($custom && $custom[0] && $custom[0] !== '') {
					$newclasses = $custom;
				}
				// Get a slugified-version of the target page/post title
				$current = NMIC_slugify_string(($item->post_type === 'page') ? $item->post_title : $item->title);
				// Push that title into the $newclasses array
				$newclasses[] = $current;
				// Grab the options object
				$options = get_option('plugin_options');
				// Loop through all the WP classes and push any that match the owner's list into the $newclasses array
				foreach($oldclasses as $class) {
					$option = 'nmic_'.$class;
					if (isset($options[$option]) && $options[$option]) {
						$newclasses[] = $class;
					}
				}
				// Return the new list of classes to WP
				return $newclasses;
			}
		endif; // NMIC_limit_classes
		// Add filter for standard menus
		add_filter( 'page_css_class', 'NMIC_limit_classes', 10, 2 );
		// Add filter for custom menus
		add_filter( 'nav_menu_css_class', 'NMIC_limit_classes', 10, 2 );

	//	Add page->slug as id attributes; this only works for custom menu items, standard menus would require a rewrite or a large chunk of Walker, which I'm really not that into...
		if ( ! function_exists( 'NMIC_add_id_attribute' ) ):
			function NMIC_add_id_attribute( $id, $item ) {
				// Add an ID to the nav item
				//return 'nav-'.NMIC_slugify_string($item->title);
				return '';
			}
		endif; // NMIC_add_id_attribute
		add_filter( 'nav_menu_item_id', 'NMIC_add_id_attribute', 10, 2 );

?>
