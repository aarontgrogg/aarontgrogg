<?php
/*
	Theme Name: Atg
	Theme URI: http://aarontgrogg.com/
	Description: A slightly-modified version of the 2010 theme for WordPress.
	Author: Aaron T. Grogg (after the WordPress team)
	Version: 1.1
	Template: boilerplate
	Tags: black, blue, white, two-columns, fixed-width, custom-header, custom-background, threaded-comments, sticky-post, translation-ready, microformats, rtl-language-support, editor-style
*/


	// global values, cache-buster string is updated by DeployBot, changed to commit string
	$_ATG_CACHEBUSTER = 'CACHE_BUSTER';
	$_ATG_CACHE_ASSETS = (object) [
		'css' => get_stylesheet_directory_uri() . '/styles-min.CACHE_BUSTER.css',
		'js' => get_stylesheet_directory_uri() . '/scripts-min.CACHE_BUSTER.js'
	];


//	remove 'http://s0.wp.com/wp-content/js/devicepx-jetpack.js' script
	add_action('wp_enqueue_scripts', create_function(null, "wp_dequeue_script('devicepx');"), 20);

//	remove jetpack open graph tags: http://antesarkkinen.com/blog/how-to-disable-jetpack-open-graph-tags/
	remove_action('wp_head', 'jetpack_og_tags');

//	http://wordpress.org/support/topic/how-to-disable-jetpacks-open-graph-tags
	add_filter( 'jetpack_enable_opengraph', '__return_false', 99 );

//	WP Admin: add default title into the Title editor field
	if (!function_exists( 'atg_add_default_title' )) {
		function atg_add_default_title($content) {
			if ($content === '') {
				$content = "Today's Readings";
			}
			return $content;
		}
	}
	add_filter( 'default_title', 'atg_add_default_title' );

//	WP Admin: add default content into the Content editor field
	if (!function_exists( 'atg_add_default_content' )) {
		function atg_add_default_content($content) {
			if ($content === '') {
				$content .= "".PHP_EOL;
				$content .= "And finally, ".PHP_EOL;
				$content .= "".PHP_EOL;
				$content .= "Happy reading,".PHP_EOL;
				$content .= "Atg";
			}
			return $content;
		}
	}
	add_filter( 'default_content', 'atg_add_default_content' );

//	WP Admin: add default text into the Excerpt editor field
	if (!function_exists( 'atg_add_default_excerpt' )) {
		function atg_add_default_excerpt($content) {
			$content = "The latest installment in my &quot;Today&#039;s Readings&quot; series, offering my rants and thoughts based on findings from around the world-wide web. Happy reading!";
			return $content;
		}
	}
	add_filter( 'default_excerpt', 'atg_add_default_excerpt' );

//	WP Admin: revert accidental character conversions
	if (!function_exists( 'atg_revert_characters' )) {
		function atg_revert_characters( $content , $postarr ) {
		    $content = preg_replace("/≥/", ">", $content);
		    $content = preg_replace("/≤/", "<", $content);
		    $content = preg_replace("/„/", "\"", $content);
		    $content = preg_replace("/“/", "\"", $content);
		    $content = preg_replace("/«/", "\"", $content);
		    $content = preg_replace("/÷/", "\/", $content);
		    $content = preg_replace("/÷/", "\/", $content);
		    $content = preg_replace("/►/", "\)", $content);
		    $content = preg_replace("/◄/", "\(", $content);
		    return $content;
		}
	}
	add_filter( 'wp_insert_post_data' , 'atg_revert_characters' , '99', 2 );


//	utility function to convert post excerpt into meta mark-up
	if (!function_exists( 'atg_clean_content' )) {
		function atg_clean_content( $content, $length = 100 ) {
			$excerpt = strip_tags( strip_shortcodes( $content ) );
			$excerpt = preg_replace("/\s+/", " ", $excerpt);
			$words = explode(" ", $excerpt, $length + 1);
			if (count($words) > $length) {
				array_pop($words);
				array_push($words, "...");
				$excerpt = implode(" ", $words);
			}
			return trim( esc_attr($excerpt) );
		}
	}


//	add pre-* links in the <head>
	if ( ! function_exists( 'atg_add_pre_party' ) ) :
		function atg_add_pre_party() {

			// get global objects
			global $_ATG_CACHE_ASSETS;

			$html =  '<link rel="subresource" href="'.$_ATG_CACHE_ASSETS->css.'">'.PHP_EOL
					.'<link rel="subresource" href="'.$_ATG_CACHE_ASSETS->js.'">'.PHP_EOL
					.'<link rel="dns-prefetch" href="//dcs.netbiscuits.net/">'.PHP_EOL
					.'<link rel="dns-prefetch" href="//stats.wp.com/">'.PHP_EOL;

			echo $html;

		} // atg_add_pre_party
	endif; // function_exists


//	add the critical CSS in the <head>
	if ( ! function_exists( 'atg_add_css' ) ) :
		function atg_add_css() {

			// get global objects
			global $_ATG_CACHEBUSTER;
			global $_ATG_CACHE_ASSETS;

			// get cache-buster
			$cachebuster = $_ATG_CACHEBUSTER;

			// url for the css file (site uri)
			$url = $_ATG_CACHE_ASSETS->css;

			// check if they need the critical CSS
			if ( $_COOKIE['atg-csscached'] == $cachebuster ) {
				// if they have the cookie, then they have the CSS file cached, so simply enqueue it
				wp_enqueue_style( 'atg-styles', $url );
			} else {
				// write the critical CSS into the page
				echo '<style>';
					include( get_stylesheet_directory() . '/critical-min.css' );
				echo '</style>'.PHP_EOL;
				// add loadCSS to the page; note the PHP variables mixed in for the cookie setting
				echo "<script>!function(e,t){'use strict';function s(s){function n(){var t,s;for(s=0;s<a.length;s+=1)a[s].href&&a[s].href.indexOf(r.href)>-1&&(t=!0);t?r.media='all':e.setTimeout(n)}var r=t.createElement('link'),i=t.getElementsByTagName('script')[0],a=t.styleSheets;return r.rel='stylesheet',r.href=s,r.media='only x',i.parentNode.insertBefore(r,i),n(),r}s('".$url."'),t.cookie='atg-csscached=".$cachebuster.";expires=\"".date("D, j M Y h:i:s e", strtotime("+1 week"))."\";path=/'}(this,this.document);</script>".PHP_EOL;
				// add the full CSS file inside of a noscript, just in case
				echo '<noscript><link rel="stylesheet" href="'.$url.'"></noscript>'.PHP_EOL;
			}

		} // atg_add_css
	endif; // function_exists


//	add the site JS at the </body>
	if ( ! function_exists( 'atg_add_js' ) ) :
		function atg_add_js() {

			// get global objects
			global $_ATG_CACHEBUSTER;
			global $_ATG_CACHE_ASSETS;

			// get cache-buster
			$cachebuster = $_ATG_CACHEBUSTER;

			// url for the css file (site uri)
			$url = $_ATG_CACHE_ASSETS->js;

			// enqueue the js file
			wp_enqueue_script( 'atg-scripts', $url, array(), $cachebuster, true );

		} // atg_add_js
	endif; // function_exists


//	utility function to get page title
	if (!function_exists( 'atg_page_title' )) {
		function atg_page_title() {
			global $post;
			$title = '';
			if ( is_single() || is_page() ) {
				$title = $post->post_title;
			} else {
				$title = bloginfo('name');
			}
			echo atg_clean_content( $title );
		}
	}

//	utility function to get page description
	if (!function_exists( 'atg_page_description' )) {
		function atg_page_description() {
			global $post;
			$excerpt = '';
			if ( is_single() || is_page() ) {
				if ( $post->post_excerpt && $post->post_excerpt !== "" ) {
					$excerpt = $post->post_excerpt;
				} else {
					$excerpt = $post->post_content;
				}
			} else {
				$excerpt = bloginfo( 'description' );
			}
			echo atg_clean_content( $excerpt );
		}
	}

//	utility function to get page type
	if (!function_exists( 'atg_page_type' )) {
		function atg_page_type() {
			$type = '';
			if (is_home()) {
				$type = 'blog';
			} else {
				$type = 'article';
			}
			echo $type;
		}
	}

//	utility function to get page link
	if (!function_exists( 'atg_page_link' )) {
		function atg_page_link() {
			global $post;
			$link = '';
			if ( is_single() || is_page() ) {
				$link = get_permalink();
			} else {
				$link = bloginfo('url');
			}
			echo $link;
		}
	}

//	utility function to encode content in URLs
	if (!function_exists( 'atg_link_encode' )) {
		function atg_link_encode( $content ) {
			return urlencode( html_entity_decode( $content, ENT_COMPAT, 'UTF-8' ) );
		}
	}

//	add 'social-share' icons SVG to all pages
	if (!function_exists( 'atg_add_svg_icons' )) {
		function atg_add_svg_icons() {
			echo '<div style="height:0;width:0;position:absolute;visibility:hidden">';
			echo '<!-- Icons courtesy of http://iconmonstr.com/ -->';
			@include( STYLESHEETPATH . '/icons/icons.svg');
			echo '</div>' . PHP_EOL;
		}
	}	

//	add 'social-share' links to articles
	if (!function_exists( 'atg_social_share_links' )) {
		function atg_social_share_links() {
			global $post;
			$permalink = atg_link_encode( get_permalink() );
			$title = get_the_title();
			?>
						<ul class="social-links social-share">
							<li class="twitter">
								<a href="https://twitter.com/share?url=<?php echo $permalink; ?>&amp;text=<?php echo atg_link_encode( '@aarontgrogg ' . $title ); ?>" target="_blank" title="<?php _e( 'Share on Twitter', 'twentyten' ); ?>">
									<svg class="icon icon-twitter"><use xlink:href="#twitter"></use></svg>
								</a>
							</li>
							<li class="google-plus">
								<a href="https://plus.google.com/share?url=<?php echo $permalink; ?>" target="_blank" title="<?php _e( 'Share on Google+', 'twentyten' ); ?>">
									<svg class="icon icon-googleplus"><use xlink:href="#googleplus"></use></svg>
								</a>
							</li>
							<li class="linkedin">
								<a href="https://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo $permalink; ?>&amp;title=<?php echo atg_link_encode( $title ); ?>" target="_blank" title="<?php _e( 'Share on LinkedIn', 'twentyten' ); ?>">
									<svg class="icon icon-linkedin"><use xlink:href="#linkedin"></use></svg>
								</a>
							</li>
							<li class="email">
								<a href="mailto:?Subject=<?php echo atg_link_encode( $title . ' | Aaron T. Grogg' ); ?>&amp;Body=<?php echo $permalink; ?>" target="_blank" title="<?php _e( 'Share via Email', 'twentyten' ); ?>">
									<svg class="icon icon-email"><use xlink:href="#email"></use></svg>
								</a>
							</li>
						</ul>
			<?php
		}
	} // atg_social_share_links

//	add "Top ^" link to all home page posts
	if (!function_exists( 'atg_add_top_of_page_link' )) {
		function atg_add_top_of_page_link($content) {
			return $content . '<p class="top-link"><a href="#top" title="Jump to top of page">Top<span>&#8682;</span></a></p>';
		}
	}
	add_filter( 'the_content', 'atg_add_top_of_page_link' );

//	http://justintadlock.com/archives/2011/07/01/captions-in-wordpress
	/*
		also add this CSS:
		.wp-caption.aligncenter img {
			margin: 0 auto;
		}
		.wp-caption.aligncenter figcaption {
			text-align: center;
		}
	*/
	if (!function_exists( 'atg_figure_caption' )) {
		function atg_figure_caption( $output, $attr, $content ) {

			/* We're not worried about captions in feeds, so just return the output here. */
			if ( is_feed() ) { return $output; }

		  /* Kill all width/height attributes: http://css-tricks.com/snippets/wordpress/remove-width-and-height-attributes-from-inserted-images/ */
		  $content = preg_replace( '/(width|height)="\d*"\s/', "", $content );

		  /* Set up the default arguments. */
			$defaults = array(
				'id' => '',
				'align' => 'alignnone',
				'width' => '',
				'caption' => ''
			);

			/* Merge the defaults with user input. */
			$attr = shortcode_atts( $defaults, $attr );

			/* If the width is less than 1 or there is no caption, return the content wrapped between the [caption]< tags. */
			if ( 1 > $attr['width'] || empty( $attr['caption'] ) ) { return $content; }

			/* Set up the attributes for the caption <div>. */
			$attributes = ( !empty( $attr['id'] ) ? ' id="' . esc_attr( $attr['id'] ) . '"' : '' );
			$attributes .= ' class="wp-caption ' . esc_attr( $attr['align'] ) . '"';
			//$attributes .= ' style="width: ' . esc_attr( $attr['width'] ) . 'px"';

			/* Open the caption <div>. */
			$output = '<figure' . $attributes .'>';

			/* Allow shortcodes for the content the caption was created for. */
			$output .= do_shortcode( $content );

			/* Append the caption text. */
			if ($attr['caption'] !== '') {
				$output .= '<figcaption class="wp-caption-text">' . $attr['caption'] . '</figcaption>';
			}

			/* Close the caption </div>. */
			$output .= '</figure>';

			/* Return the formatted, clean caption. */
			return $output;
		}
	}
	add_filter( 'img_caption_shortcode', 'atg_figure_caption', 10, 3 );

//	shortcode for creating vcards in post content
	/* sample use:
		  [vcard Aaron Grogg http://aarontgrogg.com/]
	   sample output:
		  <span class="vcard" itemscope="" itemtype="http://data-vocabulary.org/Person"><a class="url" itemprop="url" href="http://demosthenes.info/" rel="v:url"><span class="fn n" itemprop="name"><span class="given-name">Dudley</span> <span class="family-name">Storey</span></span></a></span>
	*/
	if (!function_exists( 'atg_vcard_shortcode' )) {
		function atg_vcard_shortcode( $atts ) {
			/*extract( shortcode_atts( array(
				'f' => '',
				'l' => '',
				'u' => '',
			), $atts ) );*/
			return '<span class="vcard" itemscope="" itemtype="http://data-vocabulary.org/Person" xmlns:v="http://rdf.data-vocabulary.org/#" typeof="v:Person"><a class="url" itemprop="url" href="'.esc_attr($atts[2]).'" rel="v:url"><span class="fn n" itemprop="name"><span class="given-name">'.esc_attr($atts[0]).'</span> <span class="family-name">'.esc_attr($atts[1]).'</span></span></a></span>';
		}
	}
	add_shortcode( 'vcard', 'atg_vcard_shortcode' );
