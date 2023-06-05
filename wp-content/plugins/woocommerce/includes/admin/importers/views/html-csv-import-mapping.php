<?php
/**
 * Admin View: Importer - CSV mapping
 *
 * @package WooCommerce\Admin\Importers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form class="wc-progress-form-content woocommerce-importer" method="post" action="<?php echo esc_url( $this->get_next_step_link() ); ?>">
	<header>
		<h2><?php esc_html_e( 'Map CSV fields to products', 'woocommerce' ); ?></h2>
		<p><?php esc_html_e( 'Select fields from your CSV file to map against products fields, or to ignore during import.', 'woocommerce' ); ?></p>
	</header>
	<section class="wc-importer-mapping-table-wrapper">
		<table class="widefat wc-importer-mapping-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Column name', 'woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Map to field', 'woocommerce' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $headers as $index => $name ) : ?>
					<?php $mapped_value = $mapped_items[ $index ]; ?>
					<tr>
						<td class="wc-importer-mapping-table-name">
							<?php echo esc_html( $name ); ?>
							<?php if ( ! empty( $sample[ $index ] ) ) : ?>
								<span class="description"><?php esc_html_e( 'Sample:', 'woocommerce' ); ?> <code><?php echo esc_html( $sample[ $index ] ); ?></code></span>
							<?php endif; ?>
						</td>
						<td class="wc-importer-mapping-table-field">
							<input type="hidden" name="map_from[<?php echo esc_attr( $index ); ?>]" value="<?php echo esc_attr( $name ); ?>" />
							<select name="map_to[<?php echo esc_attr( $index ); ?>]">
								<option value=""><?php esc_html_e( 'Do not import', 'woocommerce' ); ?></option>
								<option value="">--------------</option>
								<?php foreach ( $this->get_mapping_options( $mapped_value ) as $key => $value ) : ?>
									<?php if ( is_array( $value ) ) : ?>
										<optgroup label="<?php echo esc_attr( $value['name'] ); ?>">
											<?php foreach ( $value['options'] as $sub_key => $sub_value ) : ?>
												<option value="<?php echo esc_attr( $sub_key ); ?>" <?php selected( $mapped_value, $sub_key ); ?>><?php echo esc_html( $sub_value ); ?></option>
											<?php endforeach ?>
										</optgroup>
									<?php else : ?>
										<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $mapped_value, $key ); ?>><?php echo esc_html( $value ); ?></option>
									<?php endif; ?>
								<?php endforeach ?>
							</select>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</section>
	<div class="wc-actions">
		<button type="submit" class="button button-primary button-next" value="<?php esc_attr_e( 'Run the importer', 'woocommerce' ); ?>" name="save_step"><?php esc_html_e( 'Run the importer', 'woocommerce' ); ?></button>
		<input type="hidden" name="file" value="<?php echo esc_attr( $this->file ); ?>" />
		<input type="hidden" name="delimiter" value="<?php echo esc_attr( $this->delimiter ); ?>" />
		<input type="hidden" name="update_existing" value="<?php echo (int) $this->update_existing; ?>" />
		<?php if ( $args['character_encoding'] ) { ?>
			<input type="hidden" name="character_encoding" value="<?php echo esc_html( $args['character_encoding'] ); ?>" />
		<?php } ?>
		<?php wp_nonce_field( 'woocommerce-csv-importer' ); ?>
	</div>
</form>
