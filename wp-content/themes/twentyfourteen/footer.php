<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */
?>

		</div><!-- #main -->

		<footer id="colophon" class="site-footer">

			<?php get_sidebar( 'footer' ); ?>

			<div class="site-info">
				<?php do_action( 'twentyfourteen_credits' ); ?>
				<?php
				if ( function_exists( 'the_privacy_policy_link' ) ) {
					the_privacy_policy_link( '', '<span role="separator" aria-hidden="true"></span>' );
				}
				?>
				<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'twentyfourteen' ) ); ?>" class="imprint">
<<<<<<< HEAD
					<?php
					/* translators: %s: WordPress */
					printf( __( 'Proudly powered by %s', 'twentyfourteen' ), 'WordPress' );
					?>
=======
					<?php printf( __( 'Proudly powered by %s', 'twentyfourteen' ), 'WordPress' ); ?>
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
				</a>
			</div><!-- .site-info -->
		</footer><!-- #colophon -->
	</div><!-- #page -->

	<?php wp_footer(); ?>
</body>
</html>
