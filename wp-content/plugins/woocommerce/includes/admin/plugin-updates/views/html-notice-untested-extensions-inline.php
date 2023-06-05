<?php
/**
 * Admin View: Notice - Untested extensions.
 *
 * @package WooCommerce\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wc_plugin_upgrade_notice extensions_warning <?php echo esc_attr( $upgrade_type ); ?>">
	<p><?php echo wp_kses_post( $message ); ?></p>

	<table class="plugin-details-table" cellspacing="0">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Plugin', 'woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Tested up to WooCommerce version', 'woocommerce' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $plugins as $plugin ) : ?>
				<tr>
					<td><?php echo esc_html( $plugin['Name'] ); ?></td>
					<td><?php echo esc_html( $plugin['WC tested up to'] ); ?></td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
</div>
