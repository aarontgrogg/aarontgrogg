<?php

/*
	Plugin Name: Enhanced Admin Links in Multisite 'My Sites' Drop-Downs
	Plugin URI: http://aarontgrogg.com/2013/03/26/wordpress-plugin-enhanced-admin-links-in-multisite-my-sites-drop-downs/
	Description: For multisite installations, adds 'Posts', 'Pages', 'Themes', 'Plugins', 'Tools', and 'Settings' links to all of the Admin 'My Sites' drop-down menus.
	Version: 1.6
	Author: Aaron T. Grogg
	Author URI: http://aarontgrogg.com/
	License: GPLv2 or later
*/

/*	Function to add links to My Sites drop-downs */
	if ( !function_exists( 'add_links_to_my_sites' ) ) {

		function add_links_to_my_sites() {

			// Make sure user is allowed to do this
			if (current_user_can('manage_network')) {

				// Grab a couple variables...
				global $wp_admin_bar;
				$all_nodes = $wp_admin_bar->get_nodes();
				$links_to_add = array( 'Posts', 'Pages', 'Themes', 'Plugins', 'Tools', 'Settings' );

				// Loop through all nodes
				foreach ($all_nodes as $node) {

					// If we encounter a Dashboard, we want to add the new links
					if ($node->title === __('Dashboard')) {

						// Loop through and add the new menu links
						foreach ($links_to_add as $link) {

							$href = $node->href;

							// Network doesn't have a Posts, Pages, or Tools page, so skip them
							if (strpos($href, 'wp-admin/network') && ($link === 'Posts' || $link === 'Pages' || $link === 'Tools')) {
								continue;
							}

							// Grab a couple variables...
							$parent = $node->parent;
							$lc = strtolower($link);
							$id = $parent . '-' . $lc;

							// There are a few problem children, the rest play nicely
							if ($link === 'Posts') {
								$href = $href . 'edit.php';
							} else if ($link === 'Pages') {
								$href = $href . 'edit.php?post_type=page';
							} else if ($link === 'Settings' && !strpos($href, 'wp-admin/network')) {
								$href = $href . 'options-general.php';
							} else {
								$href = $href . $lc . '.php';
							}

							// For everything else, push the new link to the menu array
							$wp_admin_bar->add_menu( array(
								'parent' => $parent,
								'id' => $id,
								'title' => __($link),
								'href' => $href,
							));

						} // foreach

					} // if ($node->title === 'Dashboard')

				} // foreach

			} // if (current_user_can('manage_network'))

		} // function add_links_to_my_sites

	} // if ( !function_exists( 'add_links_to_my_sites' ) )

	// If we're in admin mode, add action to admin_bar_menu hook
	add_action('admin_bar_menu', 'add_links_to_my_sites', 200);

?>
