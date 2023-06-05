<?php
/**
 * Admin View: Notice - Secure connection.
 *
 * @package WooCommerce\Admin\Notices
 */

defined( 'ABSPATH' ) || exit;

?>
<div id="message" class="updated woocommerce-message">
	<a class="woocommerce-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wc-hide-notice', 'no_secure_connection' ), 'woocommerce_hide_notices_nonce', '_wc_notice_nonce' ) ); ?>"><?php esc_html_e( 'Dismiss', 'woocommerce' ); ?></a>

	<p>
	<?php
		echo wp_kses_post( sprintf(
			/* translators: %s: documentation URL */
			__( 'Your store does not appear to be using a secure connection. We highly recommend serving your entire website over an HTTPS connection to help keep customer data secure. <a href="%s">Learn more here.</a>', 'woocommerce' ),
			'https://docs.woocommerce.com/document/ssl-and-https/'
		) );
	?>
	</p>
</div>
