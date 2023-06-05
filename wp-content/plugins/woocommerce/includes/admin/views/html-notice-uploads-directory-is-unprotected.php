<?php
/**
 * Admin View: Notice - Uploads directory is unprotected.
 *
 * @package WooCommerce\Admin\Notices
 * @since   4.2.0
 */

defined( 'ABSPATH' ) || exit;

$uploads = wp_get_upload_dir();

?>
<div id="message" class="error woocommerce-message">
	<a class="woocommerce-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wc-hide-notice', 'uploads_directory_is_unprotected' ), 'woocommerce_hide_notices_nonce', '_wc_notice_nonce' ) ); ?>"><?php esc_html_e( 'Dismiss', 'woocommerce' ); ?></a>

	<p>
	<?php
		echo wp_kses_post(
			sprintf(
				/* translators: 1: uploads directory URL 2: documentation URL */
				__( 'Your store\'s uploads directory is <a href="%1$s">browsable via the web</a>. We strongly recommend <a href="%2$s">configuring your web server to prevent directory indexing</a>.', 'woocommerce' ),
				esc_url( $uploads['baseurl'] . '/woocommerce_uploads' ),
				'https://docs.woocommerce.com/document/digital-downloadable-product-handling/#protecting-your-uploads-directory'
			)
		);
		?>
	</p>
</div>
