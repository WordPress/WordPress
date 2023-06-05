/*global inlineEditPost, woocommerce_admin, woocommerce_quick_edit */
jQuery(
	function( $ ) {
		$( '#the-list' ).on(
			'click',
			'.editinline',
			function() {

				inlineEditPost.revert();

				var post_id = $( this ).closest( 'tr' ).attr( 'id' );

				post_id = post_id.replace( 'post-', '' );

				var $wc_inline_data = $( '#woocommerce_inline_' + post_id );

				var sku        = $wc_inline_data.find( '.sku' ).text(),
				regular_price  = $wc_inline_data.find( '.regular_price' ).text(),
				sale_price     = $wc_inline_data.find( '.sale_price ' ).text(),
				weight         = $wc_inline_data.find( '.weight' ).text(),
				length         = $wc_inline_data.find( '.length' ).text(),
				width          = $wc_inline_data.find( '.width' ).text(),
				height         = $wc_inline_data.find( '.height' ).text(),
				shipping_class = $wc_inline_data.find( '.shipping_class' ).text(),
				visibility     = $wc_inline_data.find( '.visibility' ).text(),
				stock_status   = $wc_inline_data.find( '.stock_status' ).text(),
				stock          = $wc_inline_data.find( '.stock' ).text(),
				featured       = $wc_inline_data.find( '.featured' ).text(),
				manage_stock   = $wc_inline_data.find( '.manage_stock' ).text(),
				menu_order     = $wc_inline_data.find( '.menu_order' ).text(),
				tax_status     = $wc_inline_data.find( '.tax_status' ).text(),
				tax_class      = $wc_inline_data.find( '.tax_class' ).text(),
				backorders     = $wc_inline_data.find( '.backorders' ).text(),
				product_type   = $wc_inline_data.find( '.product_type' ).text();

				var formatted_regular_price = regular_price.replace( '.', woocommerce_admin.mon_decimal_point ),
				formatted_sale_price        = sale_price.replace( '.', woocommerce_admin.mon_decimal_point );

				$( 'input[name="_sku"]', '.inline-edit-row' ).val( sku );
				$( 'input[name="_regular_price"]', '.inline-edit-row' ).val( formatted_regular_price );
				$( 'input[name="_sale_price"]', '.inline-edit-row' ).val( formatted_sale_price );
				$( 'input[name="_weight"]', '.inline-edit-row' ).val( weight );
				$( 'input[name="_length"]', '.inline-edit-row' ).val( length );
				$( 'input[name="_width"]', '.inline-edit-row' ).val( width );
				$( 'input[name="_height"]', '.inline-edit-row' ).val( height );

				$( 'select[name="_shipping_class"] option:selected', '.inline-edit-row' ).attr( 'selected', false ).trigger( 'change' );
				$( 'select[name="_shipping_class"] option[value="' + shipping_class + '"]' ).attr( 'selected', 'selected' )
					.trigger( 'change' );

				$( 'input[name="_stock"]', '.inline-edit-row' ).val( stock );
				$( 'input[name="menu_order"]', '.inline-edit-row' ).val( menu_order );

				$(
					'select[name="_tax_status"] option, ' +
					'select[name="_tax_class"] option, ' +
					'select[name="_visibility"] option, ' +
					'select[name="_stock_status"] option, ' +
					'select[name="_backorders"] option'
				).prop( 'selected', false ).removeAttr( 'selected' );

				var is_variable_product = 'variable' === product_type;
				$( 'select[name="_stock_status"] ~ .wc-quick-edit-warning', '.inline-edit-row' ).toggle( is_variable_product );
				$( 'select[name="_stock_status"] option[value="' + (is_variable_product ? '' : stock_status) + '"]', '.inline-edit-row' )
					.attr( 'selected', 'selected' );

				$( 'select[name="_tax_status"] option[value="' + tax_status + '"]', '.inline-edit-row' ).attr( 'selected', 'selected' );
				$( 'select[name="_tax_class"] option[value="' + tax_class + '"]', '.inline-edit-row' ).attr( 'selected', 'selected' );
				$( 'select[name="_visibility"] option[value="' + visibility + '"]', '.inline-edit-row' ).attr( 'selected', 'selected' );
				$( 'select[name="_backorders"] option[value="' + backorders + '"]', '.inline-edit-row' ).attr( 'selected', 'selected' );

				if ( 'yes' === featured ) {
					$( 'input[name="_featured"]', '.inline-edit-row' ).prop( 'checked', true );
				} else {
					$( 'input[name="_featured"]', '.inline-edit-row' ).prop( 'checked', false );
				}

				// Conditional display.
				var product_is_virtual = $wc_inline_data.find( '.product_is_virtual' ).text();

				var product_supports_stock_status = 'external' !== product_type;
				var product_supports_stock_fields = 'external' !== product_type && 'grouped' !== product_type;

				$( '.stock_fields, .manage_stock_field, .stock_status_field, .backorder_field' ).show();

				if ( product_supports_stock_fields ) {
					if ( 'yes' === manage_stock ) {
						$( '.stock_qty_field, .backorder_field', '.inline-edit-row' ).show().removeAttr( 'style' );
						$( '.stock_status_field' ).hide();
						$( '.manage_stock_field input' ).prop( 'checked', true );
					} else {
						$( '.stock_qty_field, .backorder_field', '.inline-edit-row' ).hide();
						$( '.stock_status_field' ).show().removeAttr( 'style' );
						$( '.manage_stock_field input' ).prop( 'checked', false );
					}
				} else if ( product_supports_stock_status ) {
					$( '.stock_fields, .manage_stock_field, .backorder_field' ).hide();
				} else {
					$( '.stock_fields, .manage_stock_field, .stock_status_field, .backorder_field' ).hide();
				}

				if ( 'simple' === product_type || 'external' === product_type ) {
					$( '.price_fields', '.inline-edit-row' ).show().removeAttr( 'style' );
				} else {
					$( '.price_fields', '.inline-edit-row' ).hide();
				}

				if ( 'yes' === product_is_virtual ) {
					$( '.dimension_fields', '.inline-edit-row' ).hide();
				} else {
					$( '.dimension_fields', '.inline-edit-row' ).show().removeAttr( 'style' );
				}

				// Rename core strings.
				$( 'input[name="comment_status"]' ).parent().find( '.checkbox-title' ).text( woocommerce_quick_edit.strings.allow_reviews );
			}
		);

		$( '#the-list' ).on(
			'change',
			'.inline-edit-row input[name="_manage_stock"]',
			function() {

				if ( $( this ).is( ':checked' ) ) {
					$( '.stock_qty_field, .backorder_field', '.inline-edit-row' ).show().removeAttr( 'style' );
					$( '.stock_status_field' ).hide();
				} else {
					$( '.stock_qty_field, .backorder_field', '.inline-edit-row' ).hide();
					$( '.stock_status_field' ).show().removeAttr( 'style' );
				}

			}
		);

		$( '#wpbody' ).on(
			'click',
			'#doaction, #doaction2',
			function() {
				$( 'input.text', '.inline-edit-row' ).val( '' );
				$( '#woocommerce-fields' ).find( 'select' ).prop( 'selectedIndex', 0 );
				$( '#woocommerce-fields-bulk' ).find( '.inline-edit-group .change-input' ).hide();
			}
		);

		$( '#wpbody' ).on(
			'change',
			'#woocommerce-fields-bulk .inline-edit-group .change_to',
			function() {

				if ( 0 < $( this ).val() ) {
					$( this ).closest( 'div' ).find( '.change-input' ).show();
				} else {
					$( this ).closest( 'div' ).find( '.change-input' ).hide();
				}

			}
		);

		$( '#wpbody' ).on(
			'click',
			'.trash-product',
			function() {
				return window.confirm( woocommerce_admin.i18n_delete_product_notice );
			}
		);
	}
);
