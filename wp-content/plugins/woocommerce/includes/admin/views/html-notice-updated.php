<?php
/**
 * Admin View: Notice - Updated.
 *
 * @package WooCommerce\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated woocommerce-message wc-connect woocommerce-message--success">
	<a class="woocommerce-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wc-hide-notice', 'update', remove_query_arg( 'do_update_woocommerce' ) ), 'woocommerce_hide_notices_nonce', '_wc_notice_nonce' ) ); ?>"><?php esc_html_e( 'Dismiss', 'woocommerce' ); ?></a>

	<p><?php esc_html_e( 'WooCommerce database update complete. Thank you for updating to the latest version!', 'woocommerce' ); ?></p>
</div>
