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
?>
		<footer id="footer" role="contentinfo">
			<div id="colophon">
<?php
	/* A sidebar in the footer? Yep. You can can customize
	 * your footer with four columns of widgets.
	 */
	get_sidebar( 'footer' );
?>
				<div id="site-info">
					<a href="<?php echo home_url( '/' ) ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
				</div><!-- #site-info -->
			</div><!-- #colophon -->
		</footer><!-- #footer -->
<?php
	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */

	wp_footer();
?>
	</body>
</html>