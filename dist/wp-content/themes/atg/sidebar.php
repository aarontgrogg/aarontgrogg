<?php
/**
 * The Sidebar containing the primary and secondary widget areas.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */
?>
<?php
ob_flush();
flush();
?>
		</div><!-- #content -->
		<div id="sidebar" class="widget-area">
				<div class="contact">
					<h3 class="widget-title"><?php _e( 'Contact', 'twentyten' ); ?></h3>
					<ul class="social-links social-connect">
						<li class="xfolkentry rss">
							<a class="taggedlink" rel="me author" href="http://aarontgrogg.com/feed/" title="<?php _e( 'Subscribe to my RSS', 'twentyten' ); ?>">
								<svg class="icon icon-rss"><use xlink:href="#rss"></use></svg>
							</a>
						</li>
						<li class="xfolkentry twitter">
							<a class="taggedlink" rel="me author" href="https://twitter.com/aarontgrogg" title="<?php _e( 'Follow me on Twitter', 'twentyten' ); ?>">
								<svg class="icon icon-twitter"><use xlink:href="#twitter"></use></svg>
							</a>
						</li>
						<li class="xfolkentry google-plus">
							<a class="taggedlink" rel="me author" href="https://plus.google.com/+AaronGrogg/posts" title="<?php _e( 'Add me on Google+', 'twentyten' ); ?>">
								<svg class="icon icon-googleplus"><use xlink:href="#googleplus"></use></svg>
							</a>
						</li>
						<li class="xfolkentry linkedin">
							<a class="taggedlink" rel="me author" href="https://www.linkedin.com/in/aarontgrogg" title="<?php _e( 'Find me on LinkedIn', 'twentyten' ); ?>">
								<svg class="icon icon-linkedin"><use xlink:href="#linkedin"></use></svg>
							</a>
						</li>
						<li class="xfolkentry github">
							<a class="taggedlink" rel="me author" href="https://github.com/aarontgrogg" title="<?php _e( 'Follow me on Github', 'twentyten' ); ?>">
								<svg class="icon icon-github"><use xlink:href="#github"></use></svg>
							</a>
						</li>
					</ul>
				</div>
			<ul class="xoxo">
<?php
	/* When we call the dynamic_sidebar() function, it'll spit out
	 * the widgets for that widget area. If it instead returns false,
	 * then the sidebar simply doesn't exist, so we'll hard-code in
	 * some default sidebar stuff just in case.
	 */
	if ( ! dynamic_sidebar( 'primary-widget-area' ) ) : ?>
			<li id="search" class="widget-container widget_search">
				<?php get_search_form(); ?>
			</li>
			<li id="archives" class="widget-container">
				<h3 class="widget-title"><?php _e( 'Archives', 'twentyten' ); ?></h3>
				<ul>
					<?php wp_get_archives( 'type=monthly' ); ?>
				</ul>
			</li>
			<li id="meta" class="widget-container">
				<h3 class="widget-title"><?php _e( 'Meta', 'twentyten' ); ?></h3>
				<ul>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
					<?php wp_meta(); ?>
				</ul>
			</li>
	<?php endif; // end primary widget area ?>
			</ul>
<?php
	// A second sidebar for widgets, just because.
	if ( is_active_sidebar( 'secondary-widget-area' ) ) : ?>
			<ul class="secondary xoxo">
				<?php dynamic_sidebar( 'secondary-widget-area' ); ?>
			</ul>
<?php endif; ?>

		</div><!-- #sidebar -->
