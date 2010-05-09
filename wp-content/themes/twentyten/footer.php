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
				<a href="<?php echo home_url( '/' ) ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
					<?php bloginfo( 'name' ); ?>
				</a>
			</div>

			<div id="site-generator">
				<?php do_action( 'twentyten_credits' ); ?>
				<a href="<?php echo esc_url( __('http://wordpress.org/', 'twentyten') ); ?>"
						title="<?php esc_attr_e('Semantic Personal Publishing Platform', 'twentyten'); ?>" rel="generator">
					<?php printf( __('Proudly powered by %s.', 'twentyten'), 'WordPress' ); ?>
				</a>
			</div>

		</div><!-- #colophon -->
	</div><!-- #footer -->

</div><!-- #wrapper -->

<?php wp_footer(); ?>

</body>
</html>
