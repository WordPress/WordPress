<?php
/**
 * Admin View: Page - Admin options.
 *
 * @package WooCommerce\Integrations
 */

defined( 'ABSPATH' ) || exit;

?>

<table class="form-table">
	<tr valign="top">
		<th scope="row" class="titledesc">
			<label><?php esc_html_e( 'Database File Path', 'woocommerce' ); ?></label>
		</th>
		<td class="forminp">
			<fieldset>
				<legend class="screen-reader-text"><span><?php esc_html_e( 'Database File Path', 'woocommerce' ); ?></span></legend>
				<input class="input-text regular-input" type="text" value="<?php echo esc_attr( $this->database_service->get_database_path() ); ?>" readonly>
				<p class="description"><?php esc_html_e( 'The location that the MaxMind database should be stored. By default, the integration will automatically save the database here.', 'woocommerce' ); ?></p>
			</fieldset>
		</td>
	</tr>
</table>
