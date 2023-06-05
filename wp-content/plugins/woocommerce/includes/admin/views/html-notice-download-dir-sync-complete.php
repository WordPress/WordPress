<?php
/**
 * Admin View: Notice - Product downloads directories sync complete.
 */

defined( 'ABSPATH' ) || exit;

?>
<div id="message" class="updated woocommerce-message">
	<a class="woocommerce-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wc-hide-notice', 'download_directories_sync_complete' ), 'woocommerce_hide_notices_nonce', '_wc_notice_nonce' ) ); ?>"><?php _e( 'Dismiss', 'woocommerce' ); ?></a>

	<p>
		<?php
		$settings_screen_link = '<a href="' . esc_url( get_admin_url( null, 'admin.php?page=wc-settings&tab=products&section=download_urls' ) ) . '">';
		$documentation_link   = '<a href="https://woocommerce.com/document/approved-download-directories">';
		$closing_link         = '</a>';

		printf(
			/* translators: %1$s and %3$s are HTML (opening link tags). %2$s is also HTML (closing link tag). */
			esc_html__( 'The %1$sApproved Product Download Directories list%2$s has been updated. To protect your site, please review the list and make any changes that might be required. For more information, please refer to %3$sthis guide%2$s.', 'woocommerce' ),
			$settings_screen_link,
			$closing_link,
			$documentation_link
		);
		?>
	</p>
</div>
