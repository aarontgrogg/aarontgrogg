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

?><!doctype html>
<html dir="ltr" lang="en-US">
	<head>
		<meta charset="<?php bloginfo('charset'); ?>">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<title><?php
			// Print the <title> tag based on what is being viewed
			global $page, $paged;
			// separator, echo to screen?, separator location
			wp_title('|', true, 'right');
			// Add a page number if necessary:
			if ( $paged >= 2 || $page >= 2 ) {
				echo ' | ' . sprintf(__('Page %s', 'twentyten'), max($paged, $page));
			}
		?></title>
		<link rel="stylesheet" href="<?php atg_css_min(); ?>">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
		<meta name="description" content="<?php atg_page_description(); ?>">
		<meta http-equiv="imagetoolbar" content="false">
		<meta name="bitly-verification" content="624ac79f6370">
		<meta name="google-site-verification" content="lbVeIvYlafhq4llvj199Sh3gUfq55tMu065LnAgjliw">
<?php
	/* We add some JavaScript to pages with the comment form
	 * to support sites with threaded comments (when in use).
	 */
	if (is_single() || is_page() || is_home()) : ?>
		<meta name="googlebot" content="index,noarchive,follow,noodp">
		<meta name="robots" content="all,index,follow">
		<meta name="msnbot" content="all,index,follow">
	<?php else: ?>
		<meta name="googlebot" content="noindex,noarchive,follow,noodp">
		<meta name="robots" content="noindex,follow">
		<meta name="msnbot" content="noindex,follow">
<?php	endif; // end (is_single() || is_page() || is_home()) ?>
		<meta name="referrer" content="always">
		<meta property="twitter:card" content="summary">
		<meta property="twitter:site" content="@aarontgrogg">
		<meta property="og:url" content="<?php atg_page_link(); ?>">
		<meta property="og:title" content="<?php atg_page_title(); ?>">
		<meta property="og:description" content="<?php atg_page_description(); ?>">
		<meta property="og:image" content="https://aarontgrogg.com/resume/Atg-clean.png">
<?php
	/* We add some JavaScript to pages with the comment form
	 * to support sites with threaded comments (when in use).
	 */
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script('comment-reply');

	/* Always have wp_head() just before the closing </head>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to add elements to <head> such
	 * as styles, scripts, and meta tags.
	 */
	wp_head();
?>
	</head>
	<body <?php body_class(); ?>>
		<header id="header" role="banner">
			<h1 id="site-title" class="vcard" itemscope itemtype="http://data-vocabulary.org/Person" xmlns:v="http://rdf.data-vocabulary.org/#" typeof="v:Person">
				<a href="<?php echo home_url('/'); ?>" title="<?php echo esc_attr( get_bloginfo('name', 'display') ); ?>" class="url" itemprop="url" rel="home v:url">
					<span class="fn n" itemprop="name" property="v:name">
						<span class="given-name">Aaron</span> <span class="middle-name">T</span>. <span class="family-name">Grogg</span>
					</span>
				</a>
			</h1>
			<h2 id="site-description"><?php bloginfo('description'); ?></h2>
		</header><!-- #header -->
		<nav id="access" role="navigation">
		  <?php /*  Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff */ ?>
			<div class="skip-link screen-reader-text"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'twentyten' ); ?>"><?php _e( 'Skip to content', 'twentyten' ); ?></a></div>
			<?php /* Our navigation menu.  If one isn't filled out, wp_nav_menu falls back to wp_page_menu.  The menu assigned to the primary position is the one used.  If none is assigned, the menu with the lowest ID is used.  */ ?>
			<?php wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'primary' ) ); ?>
			<div id="search" class="widget-container widget_search">
				<?php get_search_form(); ?>
			</div>
		</nav><!-- #access -->
		<div id="content" role="main"<?php if (is_home()) { echo ' class="hfeed"';} ?>>
<?php
// Flush any currently open buffers.
while (ob_get_level() > 0) {
    ob_end_flush();
}
ob_start();
?>