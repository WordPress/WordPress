<?php
/**
 * Product attribute table for reuse.
 *
 * @package WooCommerce\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<table cellpadding="0" cellspacing="0">
	<tbody>
	<tr>
		<td class="attribute_name">
			<label><?php esc_html_e( 'Name', 'woocommerce' ); ?>:</label>

			<?php if ( $attribute->is_taxonomy() ) : ?>
				<strong><?php echo esc_html( wc_attribute_label( $attribute->get_name() ) ); ?></strong>
				<input type="hidden" name="attribute_names[<?php echo esc_attr( $i ); ?>]" value="<?php echo esc_attr( $attribute->get_name() ); ?>" />
			<?php else : ?>
				<input type="text" class="attribute_name" name="attribute_names[<?php echo esc_attr( $i ); ?>]" value="<?php echo esc_attr( $attribute->get_name() ); ?>" placeholder="<?php esc_attr_e( 'f.e. size or color', 'woocommerce' ); ?>" />
			<?php endif; ?>
			<input type="hidden" name="attribute_position[<?php echo esc_attr( $i ); ?>]" class="attribute_position" value="<?php echo esc_attr( $attribute->get_position() ); ?>" />
		</td>
		<td rowspan="3">
			<label><?php esc_html_e( 'Value(s)', 'woocommerce' ); ?>:</label>
			<?php
			if ( $attribute->is_taxonomy() && $attribute->get_taxonomy_object() ) {
				$attribute_taxonomy = $attribute->get_taxonomy_object();
				$attribute_types    = wc_get_attribute_types();

				if ( ! array_key_exists( $attribute_taxonomy->attribute_type, $attribute_types ) ) {
					$attribute_taxonomy->attribute_type = 'select';
				}

				if ( 'select' === $attribute_taxonomy->attribute_type ) {
					$attribute_orderby = ! empty( $attribute_taxonomy->attribute_orderby ) ? $attribute_taxonomy->attribute_orderby : 'name';
					?>
					<select multiple="multiple"
							data-minimum_input_length="0"
							data-limit="50" data-return_id="id"
							data-placeholder="<?php esc_attr_e( 'Select terms', 'woocommerce' ); ?>"
							data-orderby="<?php echo esc_attr( $attribute_orderby ); ?>"
							class="multiselect attribute_values wc-taxonomy-term-search"
							name="attribute_values[<?php echo esc_attr( $i ); ?>][]"
							data-taxonomy="<?php echo esc_attr( $attribute->get_taxonomy() ); ?>">
						<?php
						$selected_terms = $attribute->get_terms();
						if ( $selected_terms ) {
							foreach ( $selected_terms as $selected_term ) {
								/**
								 * Filter the selected attribute term name.
								 *
								 * @since 3.4.0
								 * @param string  $name Name of selected term.
								 * @param array   $term The selected term object.
								 */
								echo '<option value="' . esc_attr( $selected_term->term_id ) . '" selected="selected">' . esc_html( apply_filters( 'woocommerce_product_attribute_term_name', $selected_term->name, $selected_term ) ) . '</option>';
							}
						}
						?>
					</select>
					<button class="button plus select_all_attributes"><?php esc_html_e( 'Select all', 'woocommerce' ); ?></button>
					<button class="button minus select_no_attributes"><?php esc_html_e( 'Select none', 'woocommerce' ); ?></button>
					<button class="button fr plus add_new_attribute"><?php esc_html_e( 'Create value', 'woocommerce' ); ?></button>
					<?php
				}

				/**
				 * Hook to display custom attribute terms.
				 *
				 * @since 3.4.0
				 * @param array|null $attribute_taxonomy Attribute taxonomy object.
				 * @param number $i Attribute index.
				 * @param WC_Product_Attribute $attribute Attribute object.
				 */
				do_action( 'woocommerce_product_option_terms', $attribute_taxonomy, $i, $attribute );
			} else {
				?>
				<textarea name="attribute_values[<?php echo esc_attr( $i ); ?>]" cols="5" rows="5" placeholder="<?php /* translators: %s: WC_DELIMITER */ printf( esc_attr__( 'Enter options for customers to choose from, f.e. “Blue” or “Large”. Use “%s” to separate different options.', 'woocommerce' ), esc_attr( WC_DELIMITER ) ); ?>"><?php echo esc_textarea( wc_implode_text_attributes( $attribute->get_options() ) ); ?></textarea>
				<?php
			}
			?>
		</td>
	</tr>
	<tr>
		<td>
			<label><input type="checkbox" class="woocommerce_attribute_visible_on_product_page checkbox" <?php checked( $attribute->get_visible(), true ); ?> name="attribute_visibility[<?php echo esc_attr( $i ); ?>]" value="1" /> <?php esc_html_e( 'Visible on the product page', 'woocommerce' ); ?></label>
		</td>
	</tr>
	<tr>
		<td>
			<div class="enable_variation show_if_variable">
				<label><input type="checkbox" class="woocommerce_attribute_used_for_variations checkbox" <?php checked( $attribute->get_variation(), true ); ?> <?php echo esc_attr( isset( $is_variations_screen ) ? 'disabled' : '' ); ?> name="attribute_variation[<?php echo esc_attr( $i ); ?>]" value="1" /> <?php esc_html_e( 'Used for variations', 'woocommerce' ); ?></label>
				<?php
				if ( isset( $is_variations_screen ) ) {
					?>
					<input type="hidden" name="attribute_variation[<?php echo esc_attr( $i ); ?>]" value="1" />
					<?php
				}
				?>
			</div>
		</td>
	</tr>
	<?php
	if ( ! isset( $is_variations_screen ) ) {
		/**
		 * Hook to display custom attribute terms.
		 *
		 * @since 3.4.0
		 * @param WC_Product_Attribute $attribute Attribute object.
		 * @param number $i Attribute index.
		 */
		do_action( 'woocommerce_after_product_attribute_settings', $attribute, $i );
	}
	?>
	</tbody>
</table>
