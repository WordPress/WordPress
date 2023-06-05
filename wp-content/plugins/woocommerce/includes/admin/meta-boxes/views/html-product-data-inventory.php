<?php
/**
 * Displays the inventory tab in the product data meta box.
 *
 * @package WooCommerce\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="inventory_product_data" class="panel woocommerce_options_panel hidden">
	<div class="inline notice woocommerce-message show_if_variable">
		<p>
			<?php echo esc_html_e( 'Settings below apply to all variations without manual stock management enabled.', 'woocommerce' ); ?> <a target="_blank" href="https://woocommerce.com/document/variable-product/"><?php esc_html_e( 'Learn more', 'woocommerce' ); ?></a>
		</p>
	</div>
	<div class="options_group">
		<?php
		if ( wc_product_sku_enabled() ) {
			woocommerce_wp_text_input(
				array(
					'id'          => '_sku',
					'value'       => $product_object->get_sku( 'edit' ),
					'label'       => '<abbr title="' . esc_attr__( 'Stock Keeping Unit', 'woocommerce' ) . '">' . esc_html__( 'SKU', 'woocommerce' ) . '</abbr>',
					'desc_tip'    => true,
					'description' => __( 'SKU refers to a Stock-keeping unit, a unique identifier for each distinct product and service that can be purchased.', 'woocommerce' ),
				)
			);
		}

		do_action( 'woocommerce_product_options_sku' );

		if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) {

			woocommerce_wp_checkbox(
				array(
					'id'            => '_manage_stock',
					'value'         => $product_object->get_manage_stock( 'edit' ) ? 'yes' : 'no',
					'wrapper_class' => 'show_if_simple show_if_variable',
					'label'         => __( 'Stock management', 'woocommerce' ),
					'description'   => __( 'Track stock quantity for this product', 'woocommerce' ),
				)
			);

			do_action( 'woocommerce_product_options_stock' );

			echo '<div class="stock_fields show_if_simple show_if_variable">';

			woocommerce_wp_text_input(
				array(
					'id'                => '_stock',
					'value'             => wc_stock_amount( $product_object->get_stock_quantity( 'edit' ) ?? 1 ),
					'label'             => __( 'Quantity', 'woocommerce' ),
					'desc_tip'          => true,
					'description'       => __( 'Stock quantity. If this is a variable product this value will be used to control stock for all variations, unless you define stock at variation level.', 'woocommerce' ),
					'type'              => 'number',
					'custom_attributes' => array(
						'step' => 'any',
					),
					'data_type'         => 'stock',
				)
			);

			echo '<input type="hidden" name="_original_stock" value="' . esc_attr( wc_stock_amount( $product_object->get_stock_quantity( 'edit' ) ) ) . '" />';

			$backorder_args = array(
				'id'          => '_backorders',
				'value'       => $product_object->get_backorders( 'edit' ),
				'label'       => __( 'Allow backorders?', 'woocommerce' ),
				'options'     => wc_get_product_backorder_options(),
				'desc_tip'    => true,
				'description' => __( 'If managing stock, this controls whether or not backorders are allowed. If enabled, stock quantity can go below 0.', 'woocommerce' ),
			);

			/**
			 * Allow 3rd parties to control whether "Allow backorder?" option will use radio buttons or a select.
			 *
			 * @since 7.6.0
			 *
			 * @param bool If false, "Allow backorders?" will be shown as a select. Default: it will use radio buttons.
			 */
			if ( apply_filters( 'woocommerce_product_allow_backorder_use_radio', true ) ) {
				woocommerce_wp_radio( $backorder_args );
			} else {
				woocommerce_wp_select( $backorder_args );
			}

			woocommerce_wp_text_input(
				array(
					'id'                => '_low_stock_amount',
					'value'             => $product_object->get_low_stock_amount( 'edit' ),
					'placeholder'       => sprintf(
						/* translators: %d: Amount of stock left */
						esc_attr__( 'Store-wide threshold (%d)', 'woocommerce' ),
						esc_attr( get_option( 'woocommerce_notify_low_stock_amount' ) )
					),
					'label'             => __( 'Low stock threshold', 'woocommerce' ),
					'desc_tip'          => true,
					'description'       => __( 'When product stock reaches this amount you will be notified by email. It is possible to define different values for each variation individually. The shop default value can be set in Settings > Products > Inventory.', 'woocommerce' ),
					'type'              => 'number',
					'custom_attributes' => array(
						'step' => 'any',
					),
				)
			);

			do_action( 'woocommerce_product_options_stock_fields' );

			echo '</div>';
		} else {

			woocommerce_wp_note(
				array(
					'id'               => '_manage_stock_disabled',
					'label'            => __( 'Stock management', 'woocommerce' ),
					'label-aria-label' => __( 'Stock management disabled in store settings', 'woocommerce' ),
					'message'          => sprintf(
						/* translators: %s: url for store settings */
						__( 'Disabled in <a href="%s" aria-label="stock management store settings">store settings</a>.', 'woocommerce' ),
						esc_url( 'admin.php?page=wc-settings&tab=products&section=inventory' )
					),
					'wrapper_class'    => 'show_if_simple show_if_variable',
				)
			);

		}

		$stock_status_options = wc_get_product_stock_status_options();
		$stock_status_count   = count( $stock_status_options );
		$stock_status_args    = array(
			'id'            => '_stock_status',
			'value'         => $product_object->get_stock_status( 'edit' ),
			'wrapper_class' => 'stock_status_field hide_if_variable hide_if_external hide_if_grouped',
			'label'         => __( 'Stock status', 'woocommerce' ),
			'options'       => $stock_status_options,
			'desc_tip'      => true,
			'description'   => __( 'Controls whether or not the product is listed as "in stock" or "out of stock" on the frontend.', 'woocommerce' ),
		);

		/**
		 * Allow 3rd parties to control whether the "Stock status" option will use radio buttons or a select.
		 *
		 * @since 7.6.0
		 *
		 * @param bool If false, the "Stock status" will be shown as a select. Default: it will use radio buttons.
		 */
		if ( apply_filters( 'woocommerce_product_stock_status_use_radio', $stock_status_count <= 3 && $stock_status_count >= 1 ) ) {
			woocommerce_wp_radio( $stock_status_args );
		} else {
			woocommerce_wp_select( $stock_status_args );
		}

		do_action( 'woocommerce_product_options_stock_status' );
		?>
	</div>

	<div class="inventory_sold_individually options_group show_if_simple show_if_variable">
		<?php
		woocommerce_wp_checkbox(
			array(
				'id'            => '_sold_individually',
				'value'         => $product_object->get_sold_individually( 'edit' ) ? 'yes' : 'no',
				'wrapper_class' => 'show_if_simple show_if_variable',
				'label'         => __( 'Sold individually', 'woocommerce' ),
				'description'   => __( 'Limit purchases to 1 item per order', 'woocommerce' ),
			)
		);

		echo wc_help_tip( __( 'Check to let customers to purchase only 1 item in a single order. This is particularly useful for items that have limited quantity, for example art or handmade goods.', 'woocommerce' ) );

		do_action( 'woocommerce_product_options_sold_individually' );
		?>
	</div>

	<?php do_action( 'woocommerce_product_options_inventory_product_data' ); ?>
</div>
