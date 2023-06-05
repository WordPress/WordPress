<?php
/**
 * Admin View: Notice - Regenerating product lookup table.
 *
 * @package WooCommerce\Admin
 */

use Automattic\Jetpack\Constants;

defined( 'ABSPATH' ) || exit;

$pending_actions_url = admin_url( 'admin.php?page=wc-status&tab=action-scheduler&s=wc_update_product_lookup_tables&status=pending' );
$cron_disabled       = Constants::is_true( 'DISABLE_WP_CRON' );
$cron_cta            = $cron_disabled ? __( 'You can manually run queued updates here.', 'woocommerce' ) : __( 'View progress &rarr;', 'woocommerce' );
?>
<div id="message" class="updated woocommerce-message">
	<p>
		<strong><?php esc_html_e( 'WooCommerce is updating product data in the background', 'woocommerce' ); ?></strong><br>
		<?php
		esc_html_e( 'Product display, sorting, and reports may not be accurate until this finishes. It will take a few minutes and this notice will disappear when complete.', 'woocommerce' );

		if ( $cron_disabled ) {
			echo '<br>' . esc_html__( 'Note: WP CRON has been disabled on your install which may prevent this update from completing.', 'woocommerce' );
		}
		?>
		&nbsp;<a href="<?php echo esc_url( $pending_actions_url ); ?>"><?php echo esc_html( $cron_cta ); ?></a>
	</p>
</div>
