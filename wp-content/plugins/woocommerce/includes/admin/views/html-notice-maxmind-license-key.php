<?php
/**
 * Admin View: Notice - Missing MaxMind license key
 *
 * @package WooCommerce\Admin
 */

defined( 'ABSPATH' ) || exit;

?>

<div id="message" class="updated woocommerce-message">
	<a class="woocommerce-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wc-hide-notice', 'maxmind_license_key' ), 'woocommerce_hide_notices_nonce', '_wc_notice_nonce' ) ); ?>"><?php esc_html_e( 'Dismiss', 'woocommerce' ); ?></a>

	<p>
		<strong><?php esc_html_e( 'Geolocation has not been configured.', 'woocommerce' ); ?></strong>
	</p>

	<p>
		<?php
		echo wp_kses_post(
			sprintf(
				/* translators: %1%s: integration page %2$s: general settings page */
				__( 'You must enter a valid license key on the <a href="%1$s">MaxMind integration settings page</a> in order to use the geolocation service. If you do not need geolocation for shipping or taxes, you should change the default customer location on the <a href="%2$s">general settings page</a>.', 'woocommerce' ),
				admin_url( 'admin.php?page=wc-settings&tab=integration&section=maxmind_geolocation' ),
				admin_url( 'admin.php?page=wc-settings&tab=general' )
			)
		);
		?>
	</p>
</div>
