<?php
/**
 * The loop that displays posts.
 *
 * The loop displays the posts and the post content.  See
 * http://codex.wordpress.org/The_Loop to understand it and
 * http://codex.wordpress.org/Template_Tags to understand
 * the tags used in it.
 *
 * This can be overridden in child themes with loop.php or
 * loop-template.php, where 'template' is the loop context
 * requested by a template. For example, loop-index.php would
 * be used if it exists and we ask for the loop with:
 * <code>get_template_part( 'loop', 'index' );</code>
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */
?>

<?php /* Display navigation to next/previous pages when applicable */ ?>
<?php if ( $wp_query->max_num_pages > 1 ) : ?>
				<nav id="nav-above" class="navigation">
					<ul><li class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'twentyten' ) . '</span> %title' ); ?></li>
						<li class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'twentyten' ) . '</span>' ); ?></li>
					</ul>
				</nav><!-- #nav-above -->
<?php endif; ?>

<?php /* If there are no posts to display, such as an empty archive page */ ?>
<?php if ( ! have_posts() ) : ?>
				<article id="post-0" class="post error404 not-found">
					<h1 class="entry-title"><?php _e( 'Not Found', 'twentyten' ); ?></h1>
					<div class="entry-content">
						<p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'twentyten' ); ?></p>
						<?php get_search_form(); ?>
					</div><!-- .entry-content -->
				</article><!-- #post-0 -->
<?php endif; ?>

<?php
	/* Start the Loop.
	 *
	 * In Twenty Ten we use the same loop in multiple contexts.
	 * It is broken into three main parts: when we're displaying
	 * posts that are in the gallery category, when we're displaying
	 * posts in the asides category, and finally all other posts.
	 *
	 * Additionally, we sometimes check for whether we are on an
	 * archive page, a search page, etc., allowing for small differences
	 * in the loop on each template without actually duplicating
	 * the rest of the loop that is shared.
	 *
	 * Without further ado, the loop:
	 */ ?>
<?php while ( have_posts() ) : the_post();

	/* How to display posts in the Gallery category. */
	if ( in_category( _x('gallery', 'gallery category slug', 'twentyten') ) ) : ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
					<div class="entry-meta">
						<?php twentyten_posted_on(); ?>
					</div><!-- .entry-meta -->
					<div class="entry-content">
<?php 	if ( post_password_required() ) :
			the_content();
		else :
?>						<div class="gallery-thumb">
<?php
							$images = get_children( array( 'post_parent' => $post->ID, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'menu_order', 'order' => 'ASC', 'numberposts' => 999 ) );
							$total_images = count( $images );
							$image = array_shift( $images );
							$image_img_tag = wp_get_attachment_image( $image->ID, 'thumbnail' );
?>							<a class="size-thumbnail" href="<?php the_permalink(); ?>"><?php echo $image_img_tag; ?></a>
						</div><!-- .gallery-thumb -->
						<p><em><?php
					printf( __( 'This gallery contains <a %1$s>%2$s photos</a>.', 'twentyten' ),
						'href="' . get_permalink() . '" title="' . sprintf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ) . '" rel="bookmark"',
						$total_images
					); ?></em></p>
<?php 					the_excerpt();
		endif;
?>					</div><!-- .entry-content -->
					<div class="entry-utility">
						<a href="<?php echo get_term_link( _x('gallery', 'gallery category slug', 'twentyten'), 'category' ); ?>" title="<?php esc_attr_e( 'View posts in the Gallery category', 'twentyten' ); ?>"><?php _e( 'More Galleries', 'twentyten' ); ?></a>
						<span class="meta-sep">|</span>
						<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% Comments', 'twentyten' ) ); ?></span>
						<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?>
					</div><!-- .entry-utility -->
				</article><!-- #post-## -->
<?php
	/* How to display posts in the asides category */
	elseif ( in_category( _x('asides', 'asides category slug', 'twentyten') ) ) : ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php 	if ( is_archive() || is_search() ) : // Display excerpts for archives and search. ?>
					<div class="entry-summary">
<?php 					the_excerpt(); ?>
					</div><!-- .entry-summary -->
<?php 	else :
?>					<div class="entry-content">
<?php 					the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?>
						<ul class="categories">
							<li><?php the_category(',</li><li> '); ?></li>
						</ul>
					</div><!-- .entry-content -->
<?php 	endif;
?>					<footer class="entry-meta">
						<?php //twentyten_posted_on(); ?>
						<div class="entry-utility">
							<?php //twentyten_posted_in(); ?>
							<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="edit-link">', '</span>' ); ?>
						</div><!-- .entry-utility -->
					</footer><!-- .entry-meta -->
				</article><!-- #post-## -->
<?php 
	/* How to display all other posts. */
	else :
?>				<article id="post-<?php the_ID(); ?>" class="vevent hentry">
					<h1 class="summary entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark">First Wednesday: <?php the_title(); ?></a></h1>
<?php	if ( is_archive() || is_search() ) : // Only display excerpts for archives and search. ?>
					<div class="entry-summary">
						<?php the_content(); ?>
					</div><!-- .entry-summary -->
<?php 	else :
?>					<div class="description entry-content">
						<?php the_content(); ?>
						<ul class="categories">
							<li><?php the_category(',</li><li> '); ?></li>
						</ul>
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>
					</div><!-- .entry-content -->
<?php 	endif;
?>					<footer class="entry-meta">
						<?php //twentyten_posted_on(); ?>
						<div class="entry-utility">
							<?php //twentyten_posted_in(); ?>
							<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="edit-link">', '</span>' ); ?>
						</div><!-- .entry-utility -->
					</footer><!-- .entry-meta -->
				</article><!-- #post-## -->
<?php comments_template( '', true ); ?>
<?php endif; // This was the if statement that broke the loop into three parts based on categories. ?>
<?php endwhile; // End the loop. Whew. ?>
<?php /* Display navigation to next/previous pages when applicable */ ?>
<?php if (  $wp_query->max_num_pages > 1 ) : ?>
				<nav id="nav-below" class="navigation">
					<ul><li class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'twentyten' ) . '</span> %title' ); ?></li>
						<li class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'twentyten' ) . '</span>' ); ?></li>
					</ul>
				</nav><!-- #nav-below -->
<?php endif; ?>
