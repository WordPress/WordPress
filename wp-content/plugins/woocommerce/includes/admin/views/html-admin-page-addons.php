<?php
/**
 * Admin View: Page - Addons
 *
 * @package WooCommerce\Admin
 * @var string $view
 * @var object $addons
 * @var object $promotions
 * @var array $sections
 * @var string $current_section
 */

use Automattic\WooCommerce\Admin\RemoteInboxNotifications as PromotionRuleEngine;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_section_name = __( 'Browse Categories', 'woocommerce' );
?>
<div class="woocommerce wc-addons-wrap">
	<h1 class="screen-reader-text"><?php esc_html_e( 'Marketplace', 'woocommerce' ); ?></h1>

	<?php if ( $sections ) : ?>
	<div class="marketplace-header">
		<h1 class="marketplace-header__title"><?php esc_html_e( 'WooCommerce Marketplace', 'woocommerce' ); ?></h1>
		<p class="marketplace-header__description"><?php esc_html_e( 'Grow your business with hundreds of free and paid WooCommerce extensions.', 'woocommerce' ); ?></p>
		<form class="marketplace-header__search-form" method="GET">
			<input
				type="text"
				name="search"
				value="<?php echo esc_attr( ! empty( $search ) ? sanitize_text_field( wp_unslash( $search ) ) : '' ); ?>"
				placeholder="<?php esc_attr_e( 'Search for extensions', 'woocommerce' ); ?>"
			/>
			<button type="submit">
				<span class="dashicons dashicons-search"></span>
			</button>
			<input type="hidden" name="page" value="wc-addons">
			<input type="hidden" name="section" value="_all">
		</form>
	</div>

	<div class="top-bar">
		<ul class="marketplace-header__tabs">
			<li class="marketplace-header__tab">
				<a
					class="marketplace-header__tab-link is-current"
					href="<?php echo esc_url( admin_url( 'admin.php?page=wc-addons' ) ); ?>"
				>
					<?php esc_html_e( 'Browse Extensions', 'woocommerce' ); ?>
				</a>
			</li>
			<li class="marketplace-header__tab">
				<a
					class="marketplace-header__tab-link marketplace-header__tab-link_helper"
					href="<?php echo esc_url( admin_url( 'admin.php?page=wc-addons&section=helper' ) ); ?>"
				>
					<?php
					$count_html = WC_Helper_Updater::get_updates_count_html();
					/* translators: %s: WooCommerce.com Subscriptions tab count HTML. */
					echo ( sprintf( __( 'My Subscriptions %s', 'woocommerce' ), $count_html ) );
					?>
				</a>
			</li>
		</ul>
	</div>

	<div class="wp-header-end"></div>

	<div class="wrap">
		<div class="marketplace-content-wrapper">
			<?php require __DIR__ . '/html-admin-page-addons-category-nav.php'; ?>
			<?php if ( ! empty( $search ) && ! is_wp_error( $addons ) && 0 === count( $addons ) ) : ?>
				<h1 class="search-form-title">
					<?php esc_html_e( 'Sorry, could not find anything. Try searching again using a different term.', 'woocommerce' ); ?></p>
				</h1>
			<?php endif; ?>
			<?php if ( ! empty( $search ) && ! is_wp_error( $addons ) && count( $addons ) > 0 ) : ?>
				<h1 class="search-form-title">
					<?php // translators: search keyword. ?>
					<?php printf( esc_html__( 'Search results for "%s"', 'woocommerce' ), esc_html( sanitize_text_field( wp_unslash( $search ) ) ) ); ?>
				</h1>
			<?php endif; ?>

			<?php if ( '_featured' === $current_section ) : ?>
				<div class="addons-featured">
					<?php WC_Admin_Addons::render_featured(); ?>
				</div>
			<?php endif; ?>
			<?php if ( '_featured' !== $current_section ) : ?>
				<?php if ( is_wp_error( $addons ) ) : ?>
					<?php WC_Admin_Addons::output_empty( $addons->get_error_message() ); ?>
				<?php else: ?>
					<?php
					if ( ! empty( $promotions ) && WC()->is_wc_admin_active() ) {
						foreach ( $promotions as $promotion ) {
							WC_Admin_Addons::output_search_promotion_block( $promotion );
						}
					}
					?>
					<ul class="products">
						<?php foreach ( $addons as $addon ) : ?>
							<?php
							if ( 'shipping_methods' === $current_section ) {
								// Do not show USPS or Canada Post extensions for US and CA stores, respectively.
								$country = WC()->countries->get_base_country();
								if ( 'US' === $country
									 && false !== strpos(
										$addon->link,
										'woocommerce.com/products/usps-shipping-method'
									)
								) {
									continue;
								}
								if ( 'CA' === $country
									 && false !== strpos(
										$addon->link,
										'woocommerce.com/products/canada-post-shipping-method'
									)
								) {
									continue;
								}
							}

							WC_Admin_Addons::render_product_card( $addon );
							?>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php else : ?>
			<?php /* translators: a url */ ?>
			<p><?php printf( wp_kses_post( __( 'Our catalog of WooCommerce Extensions can be found on WooCommerce.com here: <a href="%s">WooCommerce Extensions Catalog</a>', 'woocommerce' ) ), 'https://woocommerce.com/product-category/woocommerce-extensions/' ); ?></p>
		<?php endif; ?>

		<?php if ( 'Storefront' !== $theme['Name'] && '_featured' !== $current_section ) : ?>
			<?php
				$storefront_url = WC_Admin_Addons::add_in_app_purchase_url_params( 'https://woocommerce.com/storefront/?utm_source=extensionsscreen&utm_medium=product&utm_campaign=wcaddon' );
			?>
			<div class="storefront">
				<a href="<?php echo esc_url( $storefront_url ); ?>" target="_blank"><img src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/storefront.png" alt="<?php esc_attr_e( 'Storefront', 'woocommerce' ); ?>" /></a>
				<h2><?php esc_html_e( 'Looking for a WooCommerce theme?', 'woocommerce' ); ?></h2>
				<p><?php echo wp_kses_post( __( 'We recommend Storefront, the <em>official</em> WooCommerce theme.', 'woocommerce' ) ); ?></p>
				<p><?php echo wp_kses_post( __( 'Storefront is an intuitive, flexible and <strong>free</strong> WordPress theme offering deep integration with WooCommerce and many of the most popular customer-facing extensions.', 'woocommerce' ) ); ?></p>
				<p>
					<a href="<?php echo esc_url( $storefront_url ); ?>" target="_blank" class="button"><?php esc_html_e( 'Read all about it', 'woocommerce' ); ?></a>
					<a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-theme&theme=storefront' ), 'install-theme_storefront' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Download &amp; install', 'woocommerce' ); ?></a>
				</p>
			</div>
		<?php endif; ?>
	</div>
</div>
