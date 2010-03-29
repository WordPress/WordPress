<?php
/**
 * The template used to display the footer
 *
 * Contains the closing of the id=main div and all content
 * after.  Calls sidebar-footer.php for bottom widgets
 *
 * @package WordPress
 * @subpackage Twenty Ten
 * @since 3.0.0
 */
?>

	</div><!-- #main -->

	<div id="footer">
		<div id="colophon">

<?php get_sidebar( 'footer' ); ?>

			<div id="site-info">
				<a href="<?php echo home_url( '/' ) ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
			</div>

			<div id="site-generator">
				<?php printf( __( 'Proudly powered by <span id="generator-link">%s</span>.', 'twentyten' ), '<a href="http://wordpress.org/" title="' . esc_attr__( 'Semantic Personal Publishing Platform', 'twentyten' ) . '" rel="generator">' . __( 'WordPress', 'twentyten' ) . '</a>' ); ?>
			</div>

		</div><!-- #colophon -->
	</div><!-- #footer -->

</div><!-- #wrapper -->

<?php wp_footer(); ?>

</body>
</html>
