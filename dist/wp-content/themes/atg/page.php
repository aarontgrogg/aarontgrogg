<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the wordpress construct of pages
 * and that other 'pages' on your wordpress site will use a
 * different template.
 *
 * @package WordPress
 * @subpackage Boilerplate
 * @since Boilerplate 1.0
 */

get_header(); ?>
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
				<article itemscope itemType="http://schema.org/BlogPosting" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php if ( is_front_page() ) { ?>
					<header>
						<h2 itemprop="headline" class="entry-title"><?php the_title(); ?></h2>
					</header>
				<?php } else { ?>	
					<header>
						<h1 itemprop="headline" class="entry-title"><?php the_title(); ?></h1>
					</header>
				<?php } ?>
					<?php atg_social_share_links(); ?>
					<main itemprop="articleBody" role="main" class="entry-content">
						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '' . __( 'Pages:', 'boilerplate' ), 'after' => '' ) ); ?>
						<?php edit_post_link( __( 'Edit', 'boilerplate' ), '', '' ); ?>
					</main><!-- .entry-content -->
				</article><!-- #post-## -->
				<?php comments_template( '', true ); ?>
<?php endwhile; ?>
<?php get_sidebar(); ?>
<?php get_footer(); ?>