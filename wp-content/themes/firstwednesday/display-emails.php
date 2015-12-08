<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
	Template Name: Display Emails
 */
/*
	maybe initially put all emails in a Page/Post, fetch from there, loop through?
    * The FILTER_SANITIZE_EMAIL filter removes all illegal e-mail characters from a string
    * The FILTER_VALIDATE_EMAIL filter validates value as an e-mail address
*/
	get_header();
	
	// get emails from DB
	$emails = $wpdb->get_results( 'SELECT email FROM `atg_email_addresses` WHERE name = "Aaron Grogg";' ); // for testing
	//$emails = $wpdb->get_results( 'SELECT email FROM `atg_email_addresses`;' ); // for everyone
	$i = count($emails);
	for (; --$i ;) {
		$to .= $emails[$i]->email . '; ';
	}
	echo $to . '<br />' . PHP_EOL;
	echo $subject . '<br />' . PHP_EOL;
	echo $body . PHP_EOL;
	
	get_footer();
?>