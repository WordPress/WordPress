<?php

namespace Yoast\WP\SEO\Presenters\Admin;

use WPSEO_Shortlinker;
use Yoast\WP\SEO\Conditionals\WooCommerce_Conditional;
use Yoast\WP\SEO\Presenters\Abstract_Presenter;
use Yoast\WP\SEO\Promotions\Application\Promotion_Manager;

/**
 * Presenter class for the Yoast SEO sidebar.
 */
class Sidebar_Presenter extends Abstract_Presenter {

	/**
	 * Presents the sidebar.
	 *
	 * @return string The sidebar HTML.
	 */
	public function present() {
		$title = \__( 'BLACK FRIDAY | 30% OFF', 'wordpress-seo' );

		$assets_uri            = \trailingslashit( \plugin_dir_url( \WPSEO_FILE ) );
		$is_woocommerce_active = ( new WooCommerce_Conditional() )->is_met();
		$shortlink             = ( $is_woocommerce_active ) ? WPSEO_Shortlinker::get( 'https://yoa.st/admin-sidebar-upsell-woocommerce' ) : WPSEO_Shortlinker::get( 'https://yoa.st/jj' );

		\ob_start();
		?>
		<div class="wpseo_content_cell" id="sidebar-container">
			<div id="sidebar" class="yoast-sidebar">
				<div class="wpseo_content_cell_title yoast-sidebar__title">
					<?php
					/* translators: %1$s expands to Yoast */
					\printf( \esc_html__( '%1$s recommendations for you', 'wordpress-seo' ), 'Yoast' );
					?>
				</div>

				<div class="yoast-sidebar__product"
					style="background-color: <?php echo ( $is_woocommerce_active ) ? 'rgb(14, 30, 101)' : 'rgb(166, 30, 105)'; ?>;">
					<figure class="product-image">
						<figure class="product-image">
							<img
								width="64" height="64"
								src="<?php echo ( $is_woocommerce_active ) ? \esc_url( $assets_uri . 'packages/js/images/woo-seo-logo-new.svg' ) : \esc_url( $assets_uri . 'packages/js/images/yoast-premium-logo-new.svg' ); ?>"
								class="attachment-full size-full content-visible"
								alt="Yoast SEO logo"
								loading="lazy"
								decoding="async"
								fetchpriority="low"
								sizes="(min-width: 1321px) 64">
						</figure>
					</figure>
					<?php
					if (
						\YoastSEO()->classes->get( Promotion_Manager::class )
							->is( 'black-friday-promotion' ) ) :
						?>
						<div class="sidebar__sale_banner_container">
							<div class="sidebar__sale_banner">
								<span class="banner_text"><?php echo \esc_html( $title ); ?></span>
							</div>
						</div>
					<?php endif; ?>
					<h2 class="yoast-get-premium-title">
						<?php
							/* translators: %1$s and %2$s expand to a span wrap to avoid linebreaks. %3$s expands to "Yoast SEO Premium". */
							echo ( $is_woocommerce_active ) ? \sprintf( \esc_html__( '%1$s%2$s %3$s', 'wordpress-seo' ), '<span>', '</span>', 'Yoast WooCommerce SEO' ) : \sprintf( \esc_html__( '%1$s%2$s %3$s', 'wordpress-seo' ), '<span>', '</span>', 'Yoast SEO Premium' );
						?>
					</h2>
					<span>
					<?php
						echo ( $is_woocommerce_active ) ? \esc_html__( 'SEO that scales with your product catalog.', 'wordpress-seo' ) : \esc_html__( 'Now with Local, News & Video SEO + 1 Google Docs seat!', 'wordpress-seo' );
						echo '<ul>';
						echo '<li>' . \esc_html__( 'AI tools included', 'wordpress-seo' ) . '</li>';
						echo '<li>';
						/* translators: %1$s expands to "Yoast SEO academy". */
						\printf( \esc_html__( '%1$s access', 'wordpress-seo' ), 'Yoast SEO academy' );
						echo '</li>';
						echo '<li>' . \esc_html__( '24/7 support', 'wordpress-seo' ) . '</li>';
						echo '</ul>';
					?>
					<p class="plugin-buy-button">
						<a class="yoast-button-upsell" data-action="load-nfd-ctb"
							data-ctb-id="f6a84663-465f-4cb5-8ba5-f7a6d72224b2" target="_blank"
							href="<?php echo \esc_url( $shortlink ); ?>">
							<?php
							if (
								\YoastSEO()->classes->get( Promotion_Manager::class )
									->is( 'black-friday-promotion' ) ) {
								echo \esc_html__( 'Buy now for 30% off', 'wordpress-seo' );
							}
							else {
								echo \esc_html__( 'Buy now', 'wordpress-seo' );
							}
							?>
							<span aria-hidden="true" class="yoast-button-upsell__caret"></span>
						</a>
					</p>
					<p class="yoast-price-micro-copy">
						<?php
							echo \esc_html__( '30-day money back guarantee.', 'wordpress-seo' );
						?>
					</p>
					<hr class="yoast-upsell-hr" aria-hidden="true">
					<div class="review-container">
						<a href="https://www.g2.com/products/yoast-yoast/reviews" target="_blank" rel="noopener">
								<span class="rating">
									<img alt="" loading="lazy" fetchpriority="low" decoding="async" height="22"
										width="22"
										src="<?php echo \esc_url( $assets_uri . 'packages/js/images/g2_logo_white_optm.svg' ); ?>">
									<img alt="" loading="lazy" fetchpriority="low" decoding="async" height="20"
										width="20"
										src="<?php echo \esc_url( $assets_uri . 'packages/js/images/star-rating-star.svg' ); ?>">
									<img alt="" loading="lazy" fetchpriority="low" decoding="async" height="20"
										width="20"
										src="<?php echo \esc_url( $assets_uri . 'packages/js/images/star-rating-star.svg' ); ?>">
									<img alt="" loading="lazy" fetchpriority="low" decoding="async" height="20"
										width="20"
										src="<?php echo \esc_url( $assets_uri . 'packages/js/images/star-rating-star.svg' ); ?>">
									<img alt="" loading="lazy" fetchpriority="low" decoding="async" height="20"
										width="20"
										src="<?php echo \esc_url( $assets_uri . 'packages/js/images/star-rating-star.svg' ); ?>">
									<img alt="" loading="lazy" fetchpriority="low" decoding="async" height="20"
										width="20"
										src="<?php echo \esc_url( $assets_uri . 'packages/js/images/star-rating-half.svg' ); ?>">
									<span class="rating-text">4.6 / 5</span>

								</span>
						</a>
					</div>
				</div>
			</div>
			<div class="yoast-sidebar__section">
				<h2>
					<?php
					\esc_html_e( 'Learn SEO', 'wordpress-seo' );
					?>
				</h2>
				<p>
					<?php
					$academy_shortlink = WPSEO_Shortlinker::get( 'https://yoa.st/3t6' );

					/* translators: %1$s expands to Yoast SEO academy, which is a clickable link. */
					\printf( \esc_html__( 'Want to learn SEO from Team Yoast? Check out our %1$s!', 'wordpress-seo' ), '<a href="' . \esc_url( $academy_shortlink ) . '" target="_blank"><strong>Yoast SEO academy</strong></a>' );
					echo '<br/>';
					\esc_html_e( 'We have both free and premium online courses to learn everything you need to know about SEO.', 'wordpress-seo' );
					?>
				</p>
				<p>
					<a href="<?php echo \esc_url( $academy_shortlink ); ?>" style="font-weight: 500" target="_blank">
						<?php
						/* translators: %1$s expands to Yoast SEO academy */
						\printf( \esc_html__( 'Check out %1$s', 'wordpress-seo' ), 'Yoast SEO academy' );
						?>
						<span class="screen-reader-text">
							<?php
							/* translators: Hidden accessibility text. */
							\printf( \esc_html__( '(Opens in a new browser tab)', 'wordpress-seo' ) );
							?>
						</span>
						<img
							src='data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="10" height="9" viewBox="0 0 10 9" fill="none"><path d="M3.99951 1.49988H1.99951C1.44723 1.49988 0.999512 1.94759 0.999512 2.49988V7.49988C0.999512 8.05216 1.44723 8.49988 1.99951 8.49988H6.99951C7.5518 8.49988 7.99951 8.05216 7.99951 7.49988V5.49988M5.99951 0.499878H8.99951M8.99951 0.499878V3.49988M8.99951 0.499878L3.99951 5.49988" stroke="%230085ba" stroke-linecap="round" stroke-linejoin="round"/></svg>'
							alt="" aria-hidden="true" width="10" height="9"
						/>
					</a>
				</p>
			</div>
		</div>
		<?php
		return \ob_get_clean();
	}
}
