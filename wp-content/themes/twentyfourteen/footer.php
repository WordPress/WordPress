<?php
/**
 * The template for displaying the footer.
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */
?>

		</div><!-- #main -->

		<?php get_sidebar( 'footer' ); ?>

		<footer id="colophon" class="site-footer" role="contentinfo">
			<div class="site-info">
				<?php do_action( 'twentyfourteen_credits' ); ?>
				<a href="<?php echo esc_url( __( 'http://wordpress.org/', 'twentythirteen' ) ); ?>" title="<?php esc_attr_e( 'Semantic Personal Publishing Platform', 'twentythirteen' ); ?>"><?php printf( __( 'Proudly powered by %s', 'twentythirteen' ), 'WordPress' ); ?></a>
			</div><!-- .site-info -->
		</footer><!-- #colophon -->
	</div><!-- #page -->

	<?php wp_footer(); ?>
</body>
</html>