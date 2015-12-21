<?php
/**
 * Multisite Robots.txt Manager
 * @package Multisite Robots.txt Manager
 * @author tribalNerd (tribalnerd@technerdia.com)
 * @copyright Copyright (c) 2012-2013, techNerdia LLC.
 * @link http://msrtm.technerdia.com/
 * @license http://www.gnu.org/licenses/gpl.html
 * @version 0.4.0
 */

/**
 * ===================================================== Plugin Uninstall Function
 */

if( !defined( 'ABSPATH' ) ) { exit; } 					/* Wordpress check */
if( !defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit; } 	/* Kill direct access */

function msrtm_uninstall() {
	if( !is_user_logged_in() && !current_user_can('manage_options') ) { wp_die( __( 'Authorized Access Required', 'ms_robotstxt_manager' ) ); }

	/** if not multisite, return to avoid function errors */
	if( !function_exists( 'switch_to_blog' ) ) { return; }

		/** query db */
		global $wpdb;
		$blog_ids = $wpdb->get_results( 'SELECT blog_id FROM '. $wpdb->blogs .' ORDER BY blog_id' );

		/** remove from each blog */
		foreach ( $blog_ids as $value ) {
			$id = $value->blog_id;
			switch_to_blog($id);
				remove_filter( 'robots_txt', array( 'msrtm_robots_txt', 'msrtm_show_robots_txt' ) );
				delete_option( 'ms_robotstxt_default' );
				delete_option( 'ms_robotstxt_sitemap' );
				delete_option( 'msrtm_plugin_check' );
				delete_option( 'msrtm_rules_check' );
				delete_option( 'ms_robotstxt' );
		}

		/** make sure removed from root network site */
		switch_to_blog(1);
			remove_filter( 'robots_txt', array( 'msrtm_robots_txt', 'msrtm_show_robots_txt' ) );
			delete_option( 'ms_robotstxt_default' );
			delete_option( 'ms_robotstxt_sitemap' );
			delete_option( 'msrtm_plugin_check' );
			delete_option( 'msrtm_rules_check' );
			delete_option( 'ms_robotstxt' );
		restore_current_blog();

	return;
}
/** run uninstall function */
msrtm_uninstall();
?>