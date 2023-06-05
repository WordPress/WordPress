<?php
/**
 * Admin View: Notice - Updating
 *
 * @package WooCommerce\Admin
 */

use Automattic\Jetpack\Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pending_actions_url = admin_url( 'admin.php?page=wc-status&tab=action-scheduler&s=woocommerce_run_update&status=pending' );
$cron_disabled       = Constants::is_true( 'DISABLE_WP_CRON' );
$cron_cta            = $cron_disabled ? __( 'You can manually run queued updates here.', 'woocommerce' ) : __( 'View progress &rarr;', 'woocommerce' );
?>
<div id="message" class="updated woocommerce-message wc-connect">
	<p>
		<strong><?php esc_html_e( 'WooCommerce database update', 'woocommerce' ); ?></strong><br>
		<?php esc_html_e( 'WooCommerce is updating the database in the background. The database update process may take a little while, so please be patient.', 'woocommerce' ); ?>
		<?php
		if ( $cron_disabled ) {
			echo '<br>' . esc_html__( 'Note: WP CRON has been disabled on your install which may prevent this update from completing.', 'woocommerce' );
		}
		?>
		&nbsp;<a href="<?php echo esc_url( $pending_actions_url ); ?>"><?php echo esc_html( $cron_cta ); ?></a>
	</p>
</div>
