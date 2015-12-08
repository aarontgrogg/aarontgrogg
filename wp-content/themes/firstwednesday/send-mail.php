<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
	Template Name: Send Post as Email
 */
/*
	maybe initially put all emails in a Page/Post, fetch from there, loop through?
    * The FILTER_SANITIZE_EMAIL filter removes all illegal e-mail characters from a string
    * The FILTER_VALIDATE_EMAIL filter validates value as an e-mail address
*/
	get_header();

	// get Post id from URL
	$id = $_GET['post'];
	// fetch Post, get title & content
	$post = wp_get_single_post( $id );
	$link = home_url( '/' ).$post->post_name;
	$title = $post->post_title;
	$content = $post->post_content;
	$content .= '<p>e: <a href="mailto:firstwednesday@aarontgrogg.com">firstwednesday@aarontgrogg.com</a><br />'.
				'w: <a href="http://aarontgrogg.com/firstwednesday/">http://aarontgrogg.com/firstwednesday/</a></p>';
	// filter content
	$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]&gt;', $content);
	$content = str_replace('<p>','<p style="font-family:Georgia,serif;font-size:12px;margin-top:12px;">',$content);
	$content = str_replace('<div ','<div style="font-family:Georgia,serif;font-size:12px;margin-top:12px;" ',$content);
	$content = str_replace('strong class="yellow"','strong class="#CDCF00"',$content);
	$content = str_replace('strong class="','strong style="color:',$content);
	// build email
	$subject = 'First Wednesday: '.$title;
	$body = '<p style="font-family:Georgia,serif;font-size:10px;margin:0;">If this email doesn\'t look so good, <a style="color:#EF0000;font-family:Georgia,serif;font-size:10px;color:0;" href="'.$link.'">try the website</a>.</p>'.
			'<h1 style="margin:16px 0 0;padding:0;><a href="http://aarontgrogg.com/firstwednesday/" style="display:block;text-decoration:none;">'.
			'<span style="color:#EF0000;font-family:Arial,Helvetica,sans-serif;font-size:60px;letter-spacing:-0.1em;line-height:1;text-transform:uppercase;">'.
			'<strong style="font-family:\'Arial Black\',Arial,Helvetica,sans-serif;font-weight:bold;letter-spacing:-0.04em;text-transform:none;">First</strong> Wednesday'.
			'</span></a></h1>'.
			'<p style="background:#EF0000;color:#FFFFFF;font-family:Georgia,serif;font-size:14px;font-style:italic;margin:0 0 16px;padding:4px 10px;text-align:right;">Friends, Food and Drink</p>';
	$body .= $content;
	$body .= '<p style="border-top:3px solid #EF0000;font-family:Georgia,serif;font-size:12px;margin:20px 0 0;padding:4px 10px;">';
	$body .= '<a href="mailto:firstwednesday@aarontgrogg.com?subject=Unsubscribe%20from%20First%20Wednesday%20Notifications">Unsubscribe</a> | ';
	$body .= '<a href="mailto:firstwednesday@aarontgrogg.com?subject=Referral%20for%20First%20Wednesday%20Notifications&body=Please%20include%20the%20following%20person%20in%20future%20notifications:%20">Refer</a>';
	$body .= '</p>';
	// get emails from DB
	//$emails = $wpdb->get_results( 'SELECT email FROM `atg_email_addresses` WHERE name = "Aaron Grogg";' ); // for testing
	$emails = $wpdb->get_results( 'SELECT email FROM `atg_email_addresses`;' ); // for everyone
	$to = '';
	$i = count($emails);
	for (; --$i ;) {
		$to .= $emails[$i]->email . '; ';
	}
	echo '<textarea style="width:100%;height:100px;border:1px solid #ccc;" onfocus="this.select();">' . $to . '</textarea><br />' . PHP_EOL;
	echo '<input style="width:100%;height:1.5em;border:1px solid #ccc;" onfocus="this.select();" value="' . $subject . '"><br />' . PHP_EOL;
	echo $body . PHP_EOL;

	get_footer();
?>
