<?php
/**
 * Plugin Name: AP Ticket System Link
 * Description: Adds an internal ticketing system link to AdvantiPro website dashboards.
 * Plugin URI:  http://www.advantipro.de/
 * Version:     1.0
 * Author:      Aaron T. Grogg
 * Author URI: 	http://aarontgrogg.com/
 * License: 	GPLv2 or later
 */


! defined( 'ABSPATH' ) and exit;


// 	Function that outputs the contents of the dashboard widget
	if ( ! function_exists( 'ap_add_dashboard_widget' ) ):
		function ap_add_dashboard_widget( $post, $callback_args ) {
			echo 'To create a new support ticket, you can either <a href="http://www.advantiprotickets.com/admin/new_ticket.php" target=_blank">login into the ticketing system directly</a> or <a href="mailto:support@advantiprotickets.com">submit a ticket via email</a> (the Subject of your email will become the Subject of the ticket).';
		}
	endif; // ap_add_dashboard_widget


// 	Function used in the action hook
	if ( ! function_exists( 'ap_add_dashboard_widgets' ) ):
		function ap_add_dashboard_widgets() {
			add_meta_box('ap_ticket_system_widget', 'AdvantiPro Ticket System', 'ap_add_dashboard_widget', 'dashboard', 'side', 'high');
		}
	endif; // ap_add_dashboard_widgets


// 	Register the new dashboard widget with the 'wp_dashboard_setup' action
	add_action('wp_dashboard_setup', 'ap_add_dashboard_widgets' );


?>
