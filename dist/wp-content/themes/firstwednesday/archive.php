<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */

get_header(); ?>
<?php
	/* Queue the first post, that way we know
	 * what date we're dealing with (if that is the case).
	 *
	 * We reset this later so we can run the loop
	 * properly with a call to rewind_posts().
	 */
	if ( have_posts() )
		the_post();
?>			<section id="content" role="main">
				<h1 class="page-title"><?php 
					if ( is_day() ) :
						printf( __( 'Daily Archives: <span>%s</span>', 'twentyten' ), get_the_date() );
					elseif ( is_month() ) :
						printf( __( 'Monthly Archives: <span>%s</span>', 'twentyten' ), get_the_date('F Y') );
					elseif ( is_year() ) :
						printf( __( 'Yearly Archives: <span>%s</span>', 'twentyten' ), get_the_date('Y') );
					else :
						_e( 'Blog Archives', 'twentyten' );
					endif;
				?></h1>
<?php
	/* Since we called the_post() above, we need to
	 * rewind the loop back to the beginning that way
	 * we can run the loop properly, in full.
	 */
	rewind_posts();
	/* Run the loop for the archives page to output the posts.
	 * If you want to overload this in a child theme then include a file
	 * called loop-archives.php and that will be used instead.
	 */
	 get_template_part( 'loop', 'archive' );
?>			</section><!-- #content -->
<?php
get_sidebar();
get_footer(); ?>
