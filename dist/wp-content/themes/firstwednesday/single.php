<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */

get_header();
	if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
			<section id="content" role="main">
				<article id="post-<?php the_ID(); ?>" class="vevent hentry">
					<h1 class="summary entry-title">First Wednesday: <?php the_title(); ?></h1>
					<div class="description entry-content">
						<?php the_content(); ?>
						<ul class="categories">
							<li><?php the_category(',</li><li> '); ?></li>
						</ul>
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>
					</div><!-- .entry-content -->
					<footer class="entry-meta">
						<?php //twentyten_posted_on(); ?>
						<div class="entry-utility">
							<?php //twentyten_posted_in(); ?>
							<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="edit-link">', '</span>' ); ?>
						</div><!-- .entry-utility -->
					</footer><!-- .entry-meta -->
				</article><!-- #post-## -->
				<nav id="nav-below" class="navigation">
					<ul><li class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'twentyten' ) . '</span> %title' ); ?></li>
						<li class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'twentyten' ) . '</span>' ); ?></li>
					</ul>
				</nav><!-- #nav-below -->
<?php comments_template( '', true );
	endwhile; // end of the loop
?>			</section><!-- #content -->
<?php
get_sidebar();
get_footer(); ?>
