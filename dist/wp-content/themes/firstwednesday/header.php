<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<title><?php
			/*
			 * Print the <title> tag based on what is being viewed.
			 * We filter the output of wp_title() a bit -- see
			 * twentyten_filter_wp_title() in functions.php.
			 */
			wp_title( '|', true, 'right' );

			?></title>
		<link rel="profile" href="http://gmpg.org/xfn/11" />
		<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		<!--[if lt IE 9]><script src="http://aarontgrogg.com/j/ie-shiv.js"></script><![endif]-->
		<?php
			/* We add some JavaScript to pages with the comment form
			 * to support sites with threaded comments (when in use).
			 */
			if ( is_singular() && get_option( 'thread_comments' ) )
				wp_enqueue_script( 'comment-reply' );

			/* Always have wp_head() just before the closing </head>
			 * tag of your theme, or you will break many plugins, which
			 * generally use this hook to add elements to <head> such
			 * as styles, scripts, and meta tags.
			 */
			wp_head();
		?>
	</head>
	<body <?php body_class(); ?>>
		<nav id="access" role="navigation"><a class="skip-link screen-reader-text" href="#content" title="<?php esc_attr_e( 'Skip to content', 'twentyten' ); ?>"><?php _e( 'Skip to content', 'twentyten' ); ?></a></nav><!-- #access -->
		<div id="wrapper"<?php echo ( is_home() || is_front_page() ) ? ' class="hfeed"' : ''; ?>>
			<header role="banner">
				<<?php $heading_tag = ( is_home() || is_front_page() ) ? 'h1' : 'div'; echo $heading_tag; ?> id="site-title">
					<a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><strong>First</strong> Wednesday</a>
				</<?php echo $heading_tag; ?>><!-- #site-title -->
				<div id="site-description"><?php bloginfo( 'description' ); ?></div><!-- #site-description -->
			</header><!-- header -->
