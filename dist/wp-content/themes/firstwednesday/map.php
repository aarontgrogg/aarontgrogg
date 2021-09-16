<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
	Template Name: Map
 */

get_header(); ?>
			<section id="content" role="main">
				<ol id="events">
<?php
		global $post;
		$args = array( 'numberposts' => 100000, 'post_type' => 'post', 'post_status' => 'publish' );
		$posts = get_posts( $args );
		foreach( $posts as $post ) : setup_postdata($post);
			$content = get_the_content();
			$content = stristr($content, '<span class="location org fn">');
			//$content = substr($content, strripos($content,'</strong></p>'));
?>					<li><h2><?php echo the_title(); ?></h2>
						<?php echo $content; ?>
					</li>
<?php	endforeach; ?>
				</ol>
				<iframe id="googlemap"></iframe>
			</section><!-- #content -->
<?php
get_sidebar();
get_footer(); ?>
