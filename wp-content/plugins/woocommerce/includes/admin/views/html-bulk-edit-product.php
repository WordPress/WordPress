<?php
/**
 * Admin View: Bulk Edit Products
 */

use Automattic\WooCommerce\Utilities\I18nUtil;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<fieldset class="inline-edit-col-right">
	<div id="woocommerce-fields-bulk" class="inline-edit-col">

		<h4><?php _e( 'Product data', 'woocommerce' ); ?></h4>

		<?php do_action( 'woocommerce_product_bulk_edit_start' ); ?>

		<div class="inline-edit-group">
			<label class="alignleft">
				<span class="title"><?php _e( 'Price', 'woocommerce' ); ?></span>
				<span class="input-text-wrap">
					<select class="change_regular_price change_to" name="change_regular_price">
						<?php
						$options = array(
							''  => __( '— No change —', 'woocommerce' ),
							'1' => __( 'Change to:', 'woocommerce' ),
							'2' => __( 'Increase existing price by (fixed amount or %):', 'woocommerce' ),
							'3' => __( 'Decrease existing price by (fixed amount or %):', 'woocommerce' ),
						);
						foreach ( $options as $key => $value ) {
							echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
						}
						?>
					</select>
				</span>
			</label>
			<label class="change-input">
				<input type="text" name="_regular_price" class="text regular_price" placeholder="<?php printf( esc_attr__( 'Enter price (%s)', 'woocommerce' ), get_woocommerce_currency_symbol() ); ?>" value="" />
			</label>
		</div>

		<div class="inline-edit-group">
			<label class="alignleft">
				<span class="title"><?php _e( 'Sale', 'woocommerce' ); ?></span>
				<span class="input-text-wrap">
					<select class="change_sale_price change_to" name="change_sale_price">
						<?php
						$options = array(
							''  => __( '— No change —', 'woocommerce' ),
							'1' => __( 'Change to:', 'woocommerce' ),
							'2' => __( 'Increase existing sale price by (fixed amount or %):', 'woocommerce' ),
							'3' => __( 'Decrease existing sale price by (fixed amount or %):', 'woocommerce' ),
							'4' => __( 'Set to regular price decreased by (fixed amount or %):', 'woocommerce' ),
						);
						foreach ( $options as $key => $value ) {
							echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
						}
						?>
					</select>
				</span>
			</label>
			<label class="change-input">
				<input type="text" name="_sale_price" class="text sale_price" placeholder="<?php printf( esc_attr__( 'Enter sale price (%s)', 'woocommerce' ), get_woocommerce_currency_symbol() ); ?>" value="" />
			</label>
		</div>

		<?php if ( wc_tax_enabled() ) : ?>
			<label>
				<span class="title"><?php _e( 'Tax status', 'woocommerce' ); ?></span>
				<span class="input-text-wrap">
					<select class="tax_status" name="_tax_status">
						<?php
						$options = array(
							''         => __( '— No change —', 'woocommerce' ),
							'taxable'  => __( 'Taxable', 'woocommerce' ),
							'shipping' => __( 'Shipping only', 'woocommerce' ),
							'none'     => _x( 'None', 'Tax status', 'woocommerce' ),
						);
						foreach ( $options as $key => $value ) {
							echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
						}
						?>
					</select>
				</span>
			</label>

			<label>
				<span class="title"><?php _e( 'Tax class', 'woocommerce' ); ?></span>
				<span class="input-text-wrap">
					<select class="tax_class" name="_tax_class">
						<?php
						$options = array(
							''         => __( '— No change —', 'woocommerce' ),
							'standard' => __( 'Standard', 'woocommerce' ),
						);

						$tax_classes = WC_Tax::get_tax_classes();

						if ( ! empty( $tax_classes ) ) {
							foreach ( $tax_classes as $class ) {
								$options[ sanitize_title( $class ) ] = esc_html( $class );
							}
						}

						foreach ( $options as $key => $value ) {
							echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
						}
						?>
					</select>
				</span>
			</label>
		<?php endif; ?>

		<?php if ( wc_product_weight_enabled() ) : ?>
			<div class="inline-edit-group">
				<label class="alignleft">
					<span class="title"><?php _e( 'Weight', 'woocommerce' ); ?></span>
					<span class="input-text-wrap">
						<select class="change_weight change_to" name="change_weight">
							<?php
								$options = array(
									''  => __( '— No change —', 'woocommerce' ),
									'1' => __( 'Change to:', 'woocommerce' ),
								);
							foreach ( $options as $key => $value ) {
								echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
							}
							?>
						</select>
					</span>
				</label>
				<label class="change-input">
					<?php
					$placeholder = sprintf(
						/* translators: 1. Weight number; 2. Weight unit; E.g. 2 kg */
						__( '%1$s (%2$s)', 'woocommerce' ),
						wc_format_localized_decimal( 0 ),
						I18nUtil::get_weight_unit_label( get_option( 'woocommerce_weight_unit', 'kg' ) )
					);
					?>
					<input
						type="text"
						name="_weight"
						class="text weight"
						placeholder="<?php echo esc_attr( $placeholder ); ?>"
						value=""
					>
				</label>
			</div>
		<?php endif; ?>

		<?php if ( wc_product_dimensions_enabled() ) : ?>
			<div class="inline-edit-group dimensions">
				<label class="alignleft">
					<span class="title"><?php _e( 'L/W/H', 'woocommerce' ); ?></span>
					<span class="input-text-wrap">
						<select class="change_dimensions change_to" name="change_dimensions">
							<?php
							$options = array(
								''  => __( '— No change —', 'woocommerce' ),
								'1' => __( 'Change to:', 'woocommerce' ),
							);
							foreach ( $options as $key => $value ) {
								echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
							}
							?>
						</select>
					</span>
				</label>
				<label class="change-input">
					<?php
					$dimension_unit_label = I18nUtil::get_dimensions_unit_label( get_option( 'woocommerce_dimension_unit', 'cm' ) );
					?>
					<input
						type="text"
						name="_length"
						class="text length"
						<?php /* translators: %s is dimension unit label */ ?>
						placeholder="<?php printf( esc_attr__( 'Length (%s)', 'woocommerce' ), esc_html( $dimension_unit_label ) ); ?>"
						value=""
					>
					<input
						type="text"
						name="_width"
						class="text width"
						<?php /* translators: %s is dimension unit label */ ?>
						placeholder="<?php printf( esc_attr__( 'Width (%s)', 'woocommerce' ), esc_html( $dimension_unit_label ) ); ?>"
						value=""
					>
					<input
						type="text"
						name="_height"
						class="text height"
						<?php /* translators: %s is dimension unit label */ ?>
						placeholder="<?php printf( esc_attr__( 'Height (%s)', 'woocommerce' ), esc_html( $dimension_unit_label ) ); ?>"
						value=""
					>
				</label>
			</div>
		<?php endif; ?>

		<label>
			<span class="title"><?php _e( 'Shipping class', 'woocommerce' ); ?></span>
			<span class="input-text-wrap">
				<select class="shipping_class" name="_shipping_class">
					<option value=""><?php _e( '— No change —', 'woocommerce' ); ?></option>
					<option value="_no_shipping_class"><?php _e( 'No shipping class', 'woocommerce' ); ?></option>
					<?php
					foreach ( $shipping_class as $key => $value ) {
						echo '<option value="' . esc_attr( $value->slug ) . '">' . esc_html( $value->name ) . '</option>';
					}
					?>
				</select>
			</span>
		</label>

		<label>
			<span class="title"><?php _e( 'Visibility', 'woocommerce' ); ?></span>
			<span class="input-text-wrap">
				<select class="visibility" name="_visibility">
					<?php
					$options = array(
						''        => __( '— No change —', 'woocommerce' ),
						'visible' => __( 'Catalog &amp; search', 'woocommerce' ),
						'catalog' => __( 'Catalog', 'woocommerce' ),
						'search'  => __( 'Search', 'woocommerce' ),
						'hidden'  => __( 'Hidden', 'woocommerce' ),
					);
					foreach ( $options as $key => $value ) {
						echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
					}
					?>
				</select>
			</span>
		</label>
		<label>
			<span class="title"><?php _e( 'Featured', 'woocommerce' ); ?></span>
			<span class="input-text-wrap">
				<select class="featured" name="_featured">
					<?php
					$options = array(
						''    => __( '— No change —', 'woocommerce' ),
						'yes' => __( 'Yes', 'woocommerce' ),
						'no'  => __( 'No', 'woocommerce' ),
					);
					foreach ( $options as $key => $value ) {
						echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
					}
					?>
				</select>
			</span>
		</label>

		<label>
			<span class="title"><?php _e( 'In stock?', 'woocommerce' ); ?></span>
			<span class="input-text-wrap">
				<select class="stock_status" name="_stock_status">
					<?php
					echo '<option value="">' . esc_html__( '— No Change —', 'woocommerce' ) . '</option>';

					foreach ( wc_get_product_stock_status_options() as $key => $value ) {
						echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
					}
					?>
				</select>
			</span>
		</label>
		<?php if ( 'yes' == get_option( 'woocommerce_manage_stock' ) ) : ?>

			<label>
				<span class="title"><?php _e( 'Manage stock?', 'woocommerce' ); ?></span>
				<span class="input-text-wrap">
					<select class="manage_stock" name="_manage_stock">
						<?php
						$options = array(
							''    => __( '— No change —', 'woocommerce' ),
							'yes' => __( 'Yes', 'woocommerce' ),
							'no'  => __( 'No', 'woocommerce' ),
						);
						foreach ( $options as $key => $value ) {
							echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
						}
						?>
					</select>
				</span>
			</label>

			<div class="inline-edit-group">
				<label class="alignleft stock_qty_field">
					<span class="title"><?php _e( 'Stock qty', 'woocommerce' ); ?></span>
					<span class="input-text-wrap">
						<select class="change_stock change_to" name="change_stock">
							<?php
							$options = array(
								''  => __( '— No change —', 'woocommerce' ),
								'1' => __( 'Change to:', 'woocommerce' ),
								'2' => __( 'Increase existing stock by:', 'woocommerce' ),
								'3' => __( 'Decrease existing stock by:', 'woocommerce' ),
							);
							foreach ( $options as $key => $value ) {
								echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
							}
							?>
						</select>
					</span>
				</label>
				<label class="change-input">
					<input type="text" name="_stock" class="text stock" placeholder="<?php esc_attr_e( 'Stock qty', 'woocommerce' ); ?>" step="any" value="">
				</label>
			</div>

			<label>
				<span class="title"><?php _e( 'Backorders?', 'woocommerce' ); ?></span>
				<span class="input-text-wrap">
					<select class="backorders" name="_backorders">
						<?php
						echo '<option value="">' . esc_html__( '— No Change —', 'woocommerce' ) . '</option>';

						foreach ( wc_get_product_backorder_options() as $key => $value ) {
							echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
						}
						?>
					</select>
				</span>
			</label>

		<?php endif; ?>

		<label>
			<span class="title"><?php esc_html_e( 'Sold individually?', 'woocommerce' ); ?></span>
			<span class="input-text-wrap">
				<select class="sold_individually" name="_sold_individually">
					<?php
					$options = array(
						''    => __( '— No change —', 'woocommerce' ),
						'yes' => __( 'Yes', 'woocommerce' ),
						'no'  => __( 'No', 'woocommerce' ),
					);
					foreach ( $options as $key => $value ) {
						echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
					}
					?>
				</select>
			</span>
		</label>

		<?php do_action( 'woocommerce_product_bulk_edit_end' ); ?>

		<input type="hidden" name="woocommerce_bulk_edit" value="1" />
		<input type="hidden" name="woocommerce_quick_edit_nonce" value="<?php echo wp_create_nonce( 'woocommerce_quick_edit_nonce' ); ?>" />
	</div>
</fieldset>
