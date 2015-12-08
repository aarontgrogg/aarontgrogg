<?php
/**
 * TwentyTen functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, twentyten_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, navigation menus, and the like.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_theme_setup' );
 * function my_child_theme_setup() {
 *     // We are providing our own filter for excerpt_length (or using the unfiltered value)
 *     remove_filter( 'excerpt_length', 'twentyten_excerpt_length' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * Used to set the width of images and content. Should be equal to the width the theme
 * is designed for, generally via the style.css stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 640;

/** Tell WordPress to run twentyten_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'twentyten_setup' );

if ( ! function_exists( 'twentyten_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override twentyten_setup() in a child theme, add your own twentyten_setup to your child theme's
 * functions.php file.
 *
 * @uses add_theme_support() To add support for post thumbnails and automatic feed links.
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses add_custom_background() To add support for a custom background.
 * @uses add_editor_style() To style the visual editor.
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_custom_image_header() To add support for a custom header.
 * @uses register_default_headers() To register the default custom header images provided with the theme.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_setup() {

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
	load_theme_textdomain( 'twentyten', TEMPLATEPATH . '/languages' );

	$locale = get_locale();
	$locale_file = TEMPLATEPATH . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'twentyten' ),
	) );

	// This theme allows users to set a custom background
	add_custom_background();

	// Your changeable header business starts here
	define( 'HEADER_TEXTCOLOR', '' );
	// No CSS, just IMG call. The %s is a placeholder for the theme template directory URI.
	define( 'HEADER_IMAGE', '%s/images/headers/path.jpg' );

	// The height and width of your custom header. You can hook into the theme's own filters to change these values.
	// Add a filter to twentyten_header_image_width and twentyten_header_image_height to change these values.
	define( 'HEADER_IMAGE_WIDTH', apply_filters( 'twentyten_header_image_width', 940 ) );
	define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'twentyten_header_image_height', 198 ) );

	// We'll be using post thumbnails for custom header images on posts and pages.
	// We want them to be 940 pixels wide by 198 pixels tall.
	// Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
	set_post_thumbnail_size( HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true );

	// Don't support text inside the header image.
	define( 'NO_HEADER_TEXT', true );

	// Add a way for the custom header to be styled in the admin panel that controls
	// custom headers. See twentyten_admin_header_style(), below.
	add_custom_image_header( '', 'twentyten_admin_header_style' );

	// ... and thus ends the changeable header business.

	// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
	register_default_headers( array(
		'berries' => array(
			'url' => '%s/images/headers/berries.jpg',
			'thumbnail_url' => '%s/images/headers/berries-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Berries', 'twentyten' )
		),
		'cherryblossom' => array(
			'url' => '%s/images/headers/cherryblossoms.jpg',
			'thumbnail_url' => '%s/images/headers/cherryblossoms-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Cherry Blossoms', 'twentyten' )
		),
		'concave' => array(
			'url' => '%s/images/headers/concave.jpg',
			'thumbnail_url' => '%s/images/headers/concave-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Concave', 'twentyten' )
		),
		'fern' => array(
			'url' => '%s/images/headers/fern.jpg',
			'thumbnail_url' => '%s/images/headers/fern-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Fern', 'twentyten' )
		),
		'forestfloor' => array(
			'url' => '%s/images/headers/forestfloor.jpg',
			'thumbnail_url' => '%s/images/headers/forestfloor-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Forest Floor', 'twentyten' )
		),
		'inkwell' => array(
			'url' => '%s/images/headers/inkwell.jpg',
			'thumbnail_url' => '%s/images/headers/inkwell-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Inkwell', 'twentyten' )
		),
		'path' => array(
			'url' => '%s/images/headers/path.jpg',
			'thumbnail_url' => '%s/images/headers/path-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Path', 'twentyten' )
		),
		'sunset' => array(
			'url' => '%s/images/headers/sunset.jpg',
			'thumbnail_url' => '%s/images/headers/sunset-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Sunset', 'twentyten' )
		)
	) );
}
endif;

if ( ! function_exists( 'twentyten_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_custom_image_header() in twentyten_setup().
 *
 * @since Twenty Ten 1.0
 */
function twentyten_admin_header_style() {
?>
<style type="text/css">
/* Shows the same border as on front end */
#headimg {
	border-bottom: 1px solid #000;
	border-top: 4px solid #000;
}
/* If NO_HEADER_TEXT is false, you would style the text with these selectors:
	#headimg #name { }
	#headimg #desc { }
*/
</style>
<?php
}
endif;

/**
 * Makes some changes to the <title> tag, by filtering the output of wp_title().
 *
 * If we have a site description and we're viewing the home page or a blog posts
 * page (when using a static front page), then we will add the site description.
 *
 * If we're viewing a search result, then we're going to recreate the title entirely.
 * We're going to add page numbers to all titles as well, to the middle of a search
 * result title and the end of all other titles.
 *
 * The site title also gets added to all titles.
 *
 * @since Twenty Ten 1.0
 *
 * @param string $title Title generated by wp_title()
 * @param string $separator The separator passed to wp_title(). Twenty Ten uses a
 * 	vertical bar, "|", as a separator in header.php.
 * @return string The new title, ready for the <title> tag.
 */
function twentyten_filter_wp_title( $title, $separator ) {
	// Don't affect wp_title() calls in feeds.
	if ( is_feed() )
		return $title;

	// The $paged global variable contains the page number of a listing of posts.
	// The $page global variable contains the page number of a single post that is paged.
	// We'll display whichever one applies, if we're not looking at the first page.
	global $paged, $page;

	if ( is_search() ) {
		// If we're a search, let's start over:
		$title = sprintf( __( 'Search results for %s', 'twentyten' ), '"' . get_search_query() . '"' );
		// Add a page number if we're on page 2 or more:
		if ( $paged >= 2 )
			$title .= " $separator " . sprintf( __( 'Page %s', 'twentyten' ), $paged );
		// Add the site name to the end:
		$title .= " $separator " . get_bloginfo( 'name', 'display' );
		// We're done. Let's send the new title back to wp_title():
		return $title;
	}

	// Otherwise, let's start by adding the site name to the end:
	$title .= get_bloginfo( 'name', 'display' );

	// If we have a site description and we're on the home/front page, add the description:
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title .= " $separator " . $site_description;

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $separator " . sprintf( __( 'Page %s', 'twentyten' ), max( $paged, $page ) );

	// Return the new title to wp_title():
	return $title;
}
add_filter( 'wp_title', 'twentyten_filter_wp_title', 10, 2 );

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * To override this in a child theme, remove the filter and optionally add
 * your own function tied to the wp_page_menu_args filter hook.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'twentyten_page_menu_args' );

/**
 * Sets the post excerpt length to 40 characters.
 *
 * To override this length in a child theme, remove the filter and add your own
 * function tied to the excerpt_length filter hook.
 *
 * @since Twenty Ten 1.0
 * @return int
 */
function twentyten_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'twentyten_excerpt_length' );

/**
 * Returns a "Continue Reading" link for excerpts
 *
 * @since Twenty Ten 1.0
 * @return string "Continue Reading" link
 */
function twentyten_continue_reading_link() {
	return ' <a href="'. get_permalink() . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyten' ) . '</a>';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and twentyten_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 *
 * @since Twenty Ten 1.0
 * @return string An ellipsis
 */
function twentyten_auto_excerpt_more( $more ) {
	return ' &hellip;' . twentyten_continue_reading_link();
}
add_filter( 'excerpt_more', 'twentyten_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 *
 * @since Twenty Ten 1.0
 * @return string Excerpt with a pretty "Continue Reading" link
 */
function twentyten_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= twentyten_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'twentyten_custom_excerpt_more' );

/**
 * Remove inline styles printed when the gallery shortcode is used.
 *
 * Galleries are styled by the theme in Twenty Ten's style.css.
 *
 * @since Twenty Ten 1.0
 * @return string The gallery style filter, with the styles themselves removed.
 */
function twentyten_remove_gallery_css( $css ) {
	return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
}
add_filter( 'gallery_style', 'twentyten_remove_gallery_css' );

if ( ! function_exists( 'twentyten_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own twentyten_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">
		<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 40 ); ?>
			<?php printf( __( '%s <span class="says">says:</span>', 'twentyten' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
		</div><!-- .comment-author .vcard -->
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em><?php _e( 'Your comment is awaiting moderation.', 'twentyten' ); ?></em>
			<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
			<?php
				/* translators: 1: date, 2: time */
				printf( __( '%1$s at %2$s', 'twentyten' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'twentyten' ), ' ' );
			?>
		</div><!-- .comment-meta .commentmetadata -->

		<div class="comment-body"><?php comment_text(); ?></div>

		<div class="reply">
			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div><!-- .reply -->
	</div><!-- #comment-##  -->

	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'twentyten' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', 'twentyten'), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}
endif;

/**
 * Register widgetized areas, including two sidebars and four widget-ready columns in the footer.
 *
 * To override twentyten_widgets_init() in a child theme, remove the action hook and add your own
 * function tied to the init hook.
 *
 * @since Twenty Ten 1.0
 * @uses register_sidebar
 */
function twentyten_widgets_init() {
	// Area 1, located at the top of the sidebar.
	register_sidebar( array(
		'name' => __( 'Primary Widget Area', 'twentyten' ),
		'id' => 'primary-widget-area',
		'description' => __( 'The primary widget area', 'twentyten' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 2, located below the Primary Widget Area in the sidebar. Empty by default.
	register_sidebar( array(
		'name' => __( 'Secondary Widget Area', 'twentyten' ),
		'id' => 'secondary-widget-area',
		'description' => __( 'The secondary widget area', 'twentyten' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 3, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'First Footer Widget Area', 'twentyten' ),
		'id' => 'first-footer-widget-area',
		'description' => __( 'The first footer widget area', 'twentyten' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 4, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Second Footer Widget Area', 'twentyten' ),
		'id' => 'second-footer-widget-area',
		'description' => __( 'The second footer widget area', 'twentyten' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 5, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Third Footer Widget Area', 'twentyten' ),
		'id' => 'third-footer-widget-area',
		'description' => __( 'The third footer widget area', 'twentyten' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 6, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Fourth Footer Widget Area', 'twentyten' ),
		'id' => 'fourth-footer-widget-area',
		'description' => __( 'The fourth footer widget area', 'twentyten' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
/** Register sidebars by running twentyten_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'twentyten_widgets_init' );

/**
 * Removes the default styles that are packaged with the Recent Comments widget.
 *
 * To override this in a child theme, remove the filter and optionally add your own
 * function tied to the widgets_init action hook.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
}
add_action( 'widgets_init', 'twentyten_remove_recent_comments_style' );

if ( ! function_exists( 'twentyten_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post—date/time and author.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_posted_on() {
	printf( __( '<span class="%1$s">Posted on</span> %2$s <span class="meta-sep">by</span> %3$s', 'twentyten' ),
		'meta-prep meta-prep-author',
		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',
			get_permalink(),
			esc_attr( get_the_time() ),
			get_the_date()
		),
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
			get_author_posts_url( get_the_author_meta( 'ID' ) ),
			sprintf( esc_attr__( 'View all posts by %s', 'twentyten' ), get_the_author() ),
			get_the_author()
		)
	);
}
endif;

if ( ! function_exists( 'twentyten_posted_in' ) ) :
/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 *
 * @since Twenty Ten 1.0
 */
function twentyten_posted_in() {
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list ) {
		$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'twentyten' );
	} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
		$posted_in = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'twentyten' );
	} else {
		$posted_in = __( 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'twentyten' );
	}
	// Prints the string, replacing the placeholders.
	printf(
		$posted_in,
		get_the_category_list( ', ' ),
		$tag_list,
		get_permalink(),
		the_title_attribute( 'echo=0' )
	);
}
endif;

// add default text in Edit Post container
// from: http://justintadlock.com/archives/2009/04/05/how-to-preset-text-in-the-wordpress-post-editor
function fw_editor_content( $content ) {
	$content = ''
		.'Opening blurb...'.PHP_EOL
		.PHP_EOL
		.'[VEVENT]'.PHP_EOL
		.PHP_EOL
		.'Closing blurb.'.PHP_EOL
		.PHP_EOL
		.'<strong class="red">1</strong>'.PHP_EOL
		.'<strong class="red">2</strong>'.PHP_EOL
		.'<strong class="red">3</strong>'.PHP_EOL
		.'<strong class="green">4</strong>'.PHP_EOL
		.'<strong class="green">5</strong>'.PHP_EOL
		.'<strong class="green">6</strong>'.PHP_EOL
		.'<strong class="orange">7</strong>'.PHP_EOL
		.'<strong class="blue">A</strong>'.PHP_EOL
		.'<strong class="blue">C</strong>'.PHP_EOL
		.'<strong class="blue">E</strong>'.PHP_EOL
		.'<strong class="orange">B</strong>'.PHP_EOL
		.'<strong class="orange">D</strong>'.PHP_EOL
		.'<strong class="orange">F</strong>'.PHP_EOL
		.'<strong class="orange">M</strong>'.PHP_EOL
		.'<strong class="yellow">N</strong>'.PHP_EOL
		.'<strong class="yellow">Q</strong>'.PHP_EOL
		.'<strong class="yellow">R</strong>'.PHP_EOL
		.'<strong class="brown">J</strong>'.PHP_EOL
		.'<strong class="brown">Z</strong>'.PHP_EOL
		.'<strong class="gray">L</strong>'.PHP_EOL
		.PHP_EOL
		.'Hope to see you all there, and in case you need additional help finding the place, my cell is 301-332-7660.'.PHP_EOL
		.PHP_EOL
		.'As always, <strong>no RSVPs or regrets required</strong>, just show or don’t, and please invite anyone and everyone you like.'.PHP_EOL
		.PHP_EOL
		.'Hope to see you all on Wednesday,'.PHP_EOL
		.'Atg'.PHP_EOL;
	return $content;
}

/* add vevent to Post */
/* fields needed:
	dtstart
	dtend
	location
	street-address
	note
	locality
	region
	region-title
	postal-code
	tel
	url
*/
/* end-result HTML:
	<div id="event" class="vcard">
		<abbr class="dtstart" title="2011-05-04T18:00-04:0000">May 4, 2011 6:00pm</abbr> – <abbr class="dtend" title="2011-02-02T23:00-04:0000">Whenever</abbr>
		<span class="location org fn">Lucy's Cantina Royale</span>
		<span class="adr"><span class="street-address">1 Penn Plaza</span>
		<span class="note">Between 7th &amp; 8th Avenues</span>
		<span class="locality">New York</span>, <span class="region"><abbr title="New York">NY</abbr></span> <span class="postal-code">10119</span></span>
		<span class="tel">(212) 643-1270</span>
		<a class="url" href="http://www.lucyscantinaroyale.com/">http://www.lucyscantinaroyale.com</a>
		<a target="_blank" href="http://maps.google.com/maps?f=q&source=s_q&hl=en&geocode=&q=1+Penn+Plaza,+New+York,+NY+10119-0002&aq=&sll=37.0625,-95.677068&sspn=54.005807,75.849609&ie=UTF8&hq=&hnear=1+Penn+Plaza,+New+York,+10119&z=17">Google Map</a>
	</div>
*/
require_once( WP_PLUGIN_DIR . '/easy-custom-fields/easy-custom-fields.php' );
$field_data = array (
	'vevent' => array (                                     			// unique group id
		'title' => 'vevent Details',   									// Group Title
		'context' => 'advanced',            							// context as in http://codex.wordpress.org/Function_Reference/add_meta_box
		'pages' => array( 'post' ), 									// pages as in http://codex.wordpress.org/Function_Reference/add_meta_box
		'fields' => array(
			'dtstart' => array(
				'label' => 'Start Date'
			),/*
			'dtend' => array(
				'label' => 'End Date'
			),*/
			'location' => array(
				'label' => 'Location Name'
			),
			'street-address' => array(
				'label' => 'Street Address'
			),
			'note' => array(
				'label' => 'Cross Streets'
			),
			'locality' => array(
				'label' => 'City'
			),
			'region' => array(
				'label' => 'State Abbreviation'
			),
			'region-title' => array(
				'label' => 'Full State Name'
			),
			'postal-code' => array(
				'label' => 'Zip Code'
			),
			'tel' => array(
				'label' => 'Phone'
			),
			'url' => array(
				'label' => 'Website'
			),
		),
	),
);
$easy_cf = new Easy_CF($field_data);
add_filter( 'default_content', 'fw_editor_content' );

// add text fields to Edit Post page
function fw_add_vevent( $content ) {
	/*
		build vevent HTML block and replace in-post:
			[VEVENT]
		with:
			<div id="event" class="vcard">
				<abbr class="dtstart" title="2011-05-04T18:00-04:0000">May 4, 2011 6:00pm</abbr> – <abbr class="dtend" title="2011-02-02T23:00-04:0000">Whenever</abbr>
				<span class="location org fn">Lucy's Cantina Royale</span>
				<span class="adr"><span class="street-address">1 Penn Plaza</span>
				<span class="note">Between 7th &amp; 8th Avenues</span>
				<span class="locality">New York</span>, <span class="region"><abbr title="New York">NY</abbr></span> <span class="postal-code">10119</span></span>
				<span class="tel">(212) 643-1270</span>
				<a class="url" href="http://www.lucyscantinaroyale.com/">http://www.lucyscantinaroyale.com</a>
				<a target="_blank" href="http://maps.google.com/maps?f=q&source=s_q&hl=en&geocode=&q=1+Penn+Plaza,+New+York,+NY+10119-0002&aq=&sll=37.0625,-95.677068&sspn=54.005807,75.849609&ie=UTF8&hq=&hnear=1+Penn+Plaza,+New+York,+10119&z=17">Google Map</a>
			</div>
	*/
	// set-upvariables
	global $post;
	$title = 'First Wednesday: '.$post->post_title;
	$fwurl = get_bloginfo('url') .'/'. $post->post_name;

	$dtstart = (get_post_meta($post->ID, 'dtstart', true)) ? htmlentities(get_post_meta($post->ID, 'dtstart', true)) : '';
	$location = (get_post_meta($post->ID, 'location', true)) ? htmlentities(get_post_meta($post->ID, 'location', true)) : '';
	$streetaddress = (get_post_meta($post->ID, 'street-address', true)) ? htmlentities(get_post_meta($post->ID, 'street-address', true)) : '';
	$note = (get_post_meta($post->ID, 'note', true)) ? htmlentities(get_post_meta($post->ID, 'note', true)) : '';
	$locality = (get_post_meta($post->ID, 'locality', true)) ? htmlentities(get_post_meta($post->ID, 'locality', true)) : '';
	$region = (get_post_meta($post->ID, 'region', true)) ? htmlentities(get_post_meta($post->ID, 'region', true)) : '';
	$regiontitle = (get_post_meta($post->ID, 'region-title', true)) ? htmlentities(get_post_meta($post->ID, 'region-title', true)) : '';
	$postalcode = (get_post_meta($post->ID, 'postal-code', true)) ? htmlentities(get_post_meta($post->ID, 'postal-code', true)) : '';
	$tel = (get_post_meta($post->ID, 'tel', true)) ? htmlentities(get_post_meta($post->ID, 'tel', true)) : '';
	$url = (get_post_meta($post->ID, 'url', true)) ? htmlentities(get_post_meta($post->ID, 'url', true)) : '';
	$googlemap = 'http://maps.google.com/maps?f=q&source=s_q&hl=en&geocode=&aq=&ie=UTF8&hq=&hnear='.$streetaddress.','.$locality.'+'.$region.'+'.$postalcode.'&q='.$streetaddress.','.$locality.'+'.$region.'+'.$postalcode.'&z=17';

	$date = explode('-',$dtstart);
	$year = $date[0];
	$month = $date[1];
	$day = $date[2];
	$nextday = (int)$day+1;
	if ($nextday < 10) {$nextday = '0'.$nextday;}
	$start = $year . $month . $day . 'T220000Z';
	$end = $year . $month . $nextday . 'T020000Z';
	$formaldate = mktime(18,0,0,(int)$month,(int)$day,(int)$year);

	$description = $location . '| ' .$streetaddress. '| ' .$note. '| ' .$locality. ', ' .$region. ' ' .$postalcode. '| ' .$tel. '| ' .$url;
	$where = $location . '| ' .$streetaddress. '| ' .$locality. ', ' .$region. ' ' .$postalcode;

	// google link
	$google = "http://www.google.com/calendar/event?action=TEMPLATE&trp=false"
		. "&text=".urlencode($title)
		. "&dates=".$start."/".$end
		. "&details=".urlencode(str_replace("| ", "\n", $fwurl."\n\n".$description))
		. "&location=".urlencode(str_replace("| ", ", ", $where))
		. "&sprop=".urlencode($url)
		. "&sprop=name:First%20Wednesday";
	// yahoo link
	$yahoo = "http://calendar.yahoo.com/"
		. "?TITLE=".urlencode($title)
		. "&DESC=".urlencode(str_replace("| ", "\n", $fwurl."\n\n".$description))
		. "&in_loc=".urlencode(str_replace("| ", ", ", $where))
		. "&ST=".$start."%2B0000"
		. "&DUR=360"
		. "&TYPE=20&VIEW=d&v=60";
	// outlook, iCal, etc.
	$ical = get_stylesheet_directory_uri() . "/ics-calendar-link.php"
		. "?start=".$start
		. "&end=".$end
		. "&title=".urlencode($title)
		. "&desc=".urlencode($fwurl."||".$description)
		. "&location=".urlencode(str_replace("| ", ", ", $where))
		. "&url=".urlencode($fwurl);
	// build links
	$links = '<ul id="addtocalendar">'.PHP_EOL
		. '	<li id="addtogoogle"><a href="'.$google.'" target="_blank" title="Add to Google Calendar"><img src="'.get_stylesheet_directory_uri().'/images/addtogoogle.png" width="40" heigt="40" alt="Add to Google Calendar" /></a></li>'.PHP_EOL
		. '	<li id="addtoyahoo"><a href="'.$yahoo.'" target="_blank" title="Add to Yahoo! Calendar"><img src="'.get_stylesheet_directory_uri().'/images/addtoyahoo.png" width="40" heigt="40" alt="Add to Yahoo! Calendar" /></a></li>'.PHP_EOL
		. '	<li id="addtoical"><a href="'.$ical.'" target="_blank" title="Add to iCal Calendar"><img src="'.get_stylesheet_directory_uri().'/images/addtoical.png" width="40" heigt="40" alt="Add to iCal Calendar" /></a></li>'.PHP_EOL
		. '	<li id="addtooutlook"><a href="'.$ical.'" target="_blank" title="Add to Outlook Calendar"><img src="'.get_stylesheet_directory_uri().'/images/addtooutlook.png" width="40" heigt="40" alt="Add to Outlook Calendar" /></a></li>'.PHP_EOL
		. '</ul>'.PHP_EOL;
	// build vevent
	$html = '<div id="event" class="vcard">'.PHP_EOL
		.'	<abbr class="dtstart" title="'.$dtstart.'T18:00-04:0000">'.date('M d, Y',$formaldate).' 6:00pm</abbr> – <abbr class="dtend" title="'.$dtstart.'T23:00-04:0000">Whenever</abbr><br />'.PHP_EOL
		.'	<span class="location org fn">'.$location.'</span><br />'.PHP_EOL
		.'	<span class="adr"><span class="street-address">'.$streetaddress.'</span><br />'.PHP_EOL
		.'	<span class="note">'.$note.'</span><br />'.PHP_EOL
		.'	<span class="locality">'.$locality.'</span>, <span class="region"><abbr title="'.$regiontitle.'">'.$region.'</abbr></span> <span class="postal-code">'.$postalcode.'</span></span><br />'.PHP_EOL
		.'	<span class="tel">'.$tel.'</span><br />'.PHP_EOL
		.'	<a class="url" href="'.$url.'">'.$url.'</a><br />'.PHP_EOL
		.'	<a target="_blank" href="'.$googlemap.'">Google Map</a>'.PHP_EOL
		.'	'.$links.PHP_EOL
		.'</div>'.PHP_EOL;

	$content = str_replace('[VEVENT]', $html, $content);

	return $content;
}
add_filter( 'the_content', 'fw_add_vevent' );
