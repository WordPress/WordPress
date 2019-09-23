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

			<div class="footer-inner section-inner">

				<?php

				$has_footer_menu = has_nav_menu( 'footer' );
				$has_social_menu = has_nav_menu( 'social' );

				$footer_top_classes = '';

				$footer_top_classes .= $has_footer_menu ? ' has-footer-menu' : '';
				$footer_top_classes .= $has_social_menu ? ' has-social-menu' : '';

				$footer_social_wrapper_class = $has_footer_menu ? 'footer-social-wrapper' : '';

				if ( $has_footer_menu || $has_social_menu ) {
					?>
					<div class="footer-top<?php echo esc_attr( $footer_top_classes ); ?>">
						<?php if ( $has_footer_menu ) { ?>

							<nav aria-label="<?php esc_attr_e( 'Footer menu', 'twentytwenty' ); ?>">

								<ul class="footer-menu reset-list-style">
									<?php
									wp_nav_menu(
										array(
											'container'  => '',
											'depth'      => 1,
											'items_wrap' => '%3$s',
											'theme_location' => 'footer',
										)
									);
									?>
								</ul>

							</nav><!-- .site-nav -->

						<?php } ?>
						<?php if ( $has_social_menu ) { ?>

							<div class="<?php esc_attr( $footer_social_wrapper_class ); ?>">

								<nav aria-label="<?php esc_attr_e( 'Social links', 'twentytwenty' ); ?>">

									<ul class="social-menu footer-social reset-list-style social-icons s-icons">

										<?php
										wp_nav_menu(
											array(
												'theme_location' => 'social',
												'container' => '',
												'container_class' => '',
												'items_wrap' => '%3$s',
												'menu_id' => '',
												'menu_class' => '',
												'depth'   => 1,
												'link_before' => '<span class="screen-reader-text">',
												'link_after' => '</span>',
												'fallback_cb' => '',
											)
										);
										?>

									</ul>

								</nav><!-- .social-menu -->

							</div><!-- .footer-social-wrapper -->

						<?php } ?>
					</div><!-- .footer-top -->

				<?php } ?>


				<?php if ( is_active_sidebar( 'footer-one' ) || is_active_sidebar( 'footer-two' ) ) { ?>

					<div class="footer-widgets-outer-wrapper">

						<div class="footer-widgets-wrapper">

							<?php if ( is_active_sidebar( 'footer-one' ) ) { ?>

								<div class="footer-widgets column-one grid-item">
									<?php dynamic_sidebar( 'footer-one' ); ?>
								</div>

							<?php } ?>

							<?php if ( is_active_sidebar( 'footer-two' ) ) { ?>

								<div class="footer-widgets column-two grid-item">
									<?php dynamic_sidebar( 'footer-two' ); ?>
								</div>

							<?php } ?>

						</div><!-- .footer-widgets-wrapper -->

					</div><!-- .footer-widgets-outer-wrapper -->

				<?php } ?>

				<div class="footer-bottom">

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
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo bloginfo( 'name' ); ?></a></a>
						</p>

						<p class="powered-by-wordpress">
							<?php
							/* Translators: %s = Link to WordPress.org */
							printf( _x( 'Powered by %s', 'Translators: %s = Link to WordPress.org', 'twentytwenty' ), '<a href="https://wordpress.org">' . __( 'WordPress', 'twentytwenty' ) . '</a>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core trusts translations
							?>
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

				</div><!-- .footer-bottom -->

			</div><!-- .footer-inner -->

		</footer><!-- #site-footer -->

		<?php wp_footer(); ?>

	</body>
</html>
