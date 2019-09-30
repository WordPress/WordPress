<?php
/**
 * The template for displaying the footer
 *
 * Contains the opening of the #site-footer div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since 1.0.0
 */

?>		
			<footer id="site-footer" role="contentinfo" class="header-footer-group">

				<div class="section-inner">

					<div class="footer-credits">

						<p class="footer-copyright">&copy;
							<?php
							echo esc_html(
								date_i18n(
									/* Translators: Y = Format parameter for date() https://php.net/manual/en/function.date.php */
									_x( 'Y', 'Translators: Y = Current year', 'twentytwenty' )
								)
							);
							?>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo bloginfo( 'name' ); ?></a>
						</p>

						<p class="powered-by-wordpress">
							<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'twentytwenty' ) ); ?>">
								<?php
								_e( 'Powered by WordPress', 'twentytwenty' ); // phpcs:ignore WordPress.Security.EscapeOutput.UnsafePrintingFunction -- core trusts translations
								?>
							</a>
						</p><!-- .theme-credits -->

					</div><!-- .footer-credits -->

					<a class="to-the-top" href="#site-header">
						<span class="to-the-top-long">
							<?php
							// Translators: %s = HTML character for an arrow.
							printf( esc_html( _x( 'To the top %s', '%s = HTML character for an arrow', 'twentytwenty' ) ), '<span class="arrow">&uarr;</span>' );
							?>
						</span>
						<span class="to-the-top-short">
							<?php
							// Translators: %s = HTML character for an arrow.
							printf( esc_html( _x( 'Up %s', '%s = HTML character for an arrow', 'twentytwenty' ) ), '<span class="arrow">&uarr;</span>' );
							?>
						</span>
					</a>

				</div><!-- .section-inner -->

			</footer><!-- #site-footer -->

		<?php wp_footer(); ?>

	</body>
</html>
