jQuery( function( $ ) {

	// wc_cart_params is required to continue, ensure the object exists
	if ( typeof wc_cart_params === 'undefined' ) {
		return false;
	}

	// Shipping calculator
	$( document ).on( 'click', '.shipping-calculator-button', function() {
		$( '.shipping-calculator-form' ).slideToggle( 'slow' );

		return false;
	}).on( 'change', 'select.shipping_method, input[name^=shipping_method]', function() {

		var shipping_methods = [];

		$( 'select.shipping_method, input[name^=shipping_method][type=radio]:checked, input[name^=shipping_method][type=hidden]' ).each( function( index, input ) {
			shipping_methods[ $( this ).data( 'index' ) ] = $( this ).val();
		} );

		$( 'div.cart_totals' ).block({ message: null, overlayCSS: { background: '#fff url(' + wc_cart_params.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6 } });

		var data = {
			action: 'woocommerce_update_shipping_method',
			security: wc_cart_params.update_shipping_method_nonce,
			shipping_method: shipping_methods
		};

		$.post( wc_cart_params.ajax_url, data, function( response ) {

			$( 'div.cart_totals' ).replaceWith( response );
			$( 'body' ).trigger( 'updated_shipping_method' );

		});
	});

	$( '.shipping-calculator-form' ).hide();

});
