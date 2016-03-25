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
<?php atg_add_pre_party(); ?>
<?php atg_add_css(); ?>
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
<!-- favicons & markup generated by: http://realfavicongenerator.net/ -->
<link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="/apple-touch-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="/apple-touch-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="/apple-touch-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon-180x180.png">
<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
<link rel="icon" type="image/png" href="/favicon-194x194.png" sizes="194x194">
<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">
<link rel="icon" type="image/png" href="/android-chrome-192x192.png" sizes="192x192">
<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
<link rel="manifest" href="/manifest.json">
<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#444444">
<meta name="apple-mobile-web-app-title" content="Atg.com">
<meta name="application-name" content="Atg.com">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="/mstile-144x144.png">
<meta name="theme-color" content="#ffffff">
<link rel="manifest" href="manifest.json">
<meta name="mobile-web-app-capable" content="yes">
<meta name="description" content="<?php atg_page_description(); ?>">
<meta http-equiv="imagetoolbar" content="false">
<meta name="bitly-verification" content="624ac79f6370">
<meta name="msvalidate.01" content="3583C3B4E1932435C9AC18EE175673F1">
<meta name="google-site-verification" content="lbVeIvYlafhq4llvj199Sh3gUfq55tMu065LnAgjliw">
<?php
	// Block all robots for Dev, pick and choose for live
	if (bloginfo('url') === 'https://aarontgrogg.dreamhosters.com'):
		$googlebot = "noindex,noarchive,nofollow,nosnippet,noodp";
		$robots = "noindex,nofollow";
	elseif (is_single() || is_page() || is_home()) :
		$googlebot = "index,archive,follow,noodp";
		$robots = "all,index,follow";
	else:
		$googlebot = "noindex,noarchive,follow,noodp";
		$robots = "noindex,follow";
	endif;
?><meta name="googlebot" content="<?php echo $googlebot; ?>">
<meta name="robots" content="<?php echo $robots; ?>">
<meta name="msnbot" content="<?php echo $robots; ?>">
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
<?php atg_add_svg_icons(); ?>
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
			<?php 
				/* Our navigation menu.  If one isn't filled out, wp_nav_menu falls back to wp_page_menu.  The menu assigned to the primary position is the one used.  If none is assigned, the menu with the lowest ID is used.  */
				// exclude the "Offline" page from the top nav
				$offline = get_page_by_path( 'offline' );
				wp_nav_menu( array( 
					'container_class' => 'menu-header', 
					'theme_location' => 'primary',  
					'exclude' => $offline->ID ) );
			?>
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
