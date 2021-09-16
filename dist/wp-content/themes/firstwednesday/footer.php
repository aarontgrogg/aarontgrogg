<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content
 * after.  Calls sidebar-footer.php for bottom widgets.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */
?>			<footer role="contentinfo">
<?php
	/* A sidebar in the footer? Yep. You can can customize
	 * your footer with four columns of widgets.
	 */
	get_sidebar( 'footer' );
?>
				<div id="site-info" class="vcard">
					<a class="url org fn" href="<?php echo home_url( '/' ) ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
				</div><!-- #site-info -->
				<div id="site-generator">
					<?php do_action( 'twentyten_credits' ); ?>
					<a href="<?php echo esc_url( __('http://wordpress.org/', 'twentyten') ); ?>" rel="generator"><?php printf( __('Proudly powered by %s.', 'twentyten'), 'WordPress' ); ?></a>
				</div><!-- #site-generator -->
			</footer><!-- footer -->
		</div><!-- #wrapper -->
<?php
	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */

wp_footer();
?>
	<script src="<?php echo get_bloginfo( 'template_directory' ) ?>/scripts.js"></script>
	</body>
</html>
