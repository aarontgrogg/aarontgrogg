<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */

get_header(); ?>
			<section id="content" role="main">
				<article id="post-0" class="post error404 not-found">
					<h1 class="entry-title"><?php _e( 'Not Found', 'twentyten' ); ?></h1>
					<div class="entry-content">
						<p><?php _e( 'Apologies, but the page you requested could not be found. Perhaps searching will help.', 'twentyten' ); ?></p>
					</div><!-- .entry-content -->
				</article><!-- #post-0 -->
			</section><!-- #content -->
<?php
get_sidebar();
get_footer(); ?>
