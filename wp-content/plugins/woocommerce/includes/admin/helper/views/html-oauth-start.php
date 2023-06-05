<?php
/**
 * Admin -> WooCommerce -> Extensions -> WooCommerce.com Subscriptions main page.
 *
 * @package WooCommerce\Views
 */

defined( 'ABSPATH' ) || exit();

?>

<div class="wrap woocommerce wc-addons-wrap wc-helper">
	<?php require WC_Helper::get_view_filename( 'html-section-nav.php' ); ?>
	<h1 class="screen-reader-text"><?php esc_html_e( 'WooCommerce Extensions', 'woocommerce' ); ?></h1>
	<?php require WC_Helper::get_view_filename( 'html-section-notices.php' ); ?>

		<div class="start-container">
			<div class="text">
				<img src="<?php echo esc_url( WC()->plugin_url() . '/assets/images/woocommerce_logo.png' ); ?>" alt="<?php esc_attr_e( 'WooCommerce', 'woocommerce' ); ?>" style="width:180px;">

				<?php if ( ! empty( $_GET['wc-helper-status'] ) && 'helper-disconnected' === $_GET['wc-helper-status'] ) : ?>
					<p><strong><?php esc_html_e( 'Sorry to see you go.', 'woocommerce' ); ?></strong> <?php esc_html_e( 'Feel free to reconnect again using the button below.', 'woocommerce' ); ?></p>
				<?php endif; ?>

				<h2><?php esc_html_e( 'Manage your subscriptions, get important product notifications, and updates, all from the convenience of your WooCommerce dashboard', 'woocommerce' ); ?></h2>
				<p><?php esc_html_e( 'Once connected, your WooCommerce.com purchases will be listed here.', 'woocommerce' ); ?></p>
				<p><a class="button button-primary button-helper-connect" href="<?php echo esc_url( $connect_url ); ?>"><?php esc_html_e( 'Connect', 'woocommerce' ); ?></a></p>
			</div>
		</div>
</div>
