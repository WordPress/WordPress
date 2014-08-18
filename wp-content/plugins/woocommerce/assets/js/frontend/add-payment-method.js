jQuery( function( $ ) {

	// woocommerce_params is required to continue, ensure the object exists
	if ( typeof woocommerce_params === 'undefined' )
		return false;

	$( '#add_payment_method' )

	/* Payment option selection */

	.on( 'click init_add_payment_method', '.payment_methods input.input-radio', function() {
		if ( $( '.payment_methods input.input-radio' ).length > 1 ) {
			var target_payment_box = $( 'div.payment_box.' + $( this ).attr( 'ID' ) );
			if ( $( this ).is( ':checked' ) && ! target_payment_box.is( ':visible' ) ) {
				$( 'div.payment_box' ).filter( ':visible' ).slideUp( 250 );
				if ( $( this ).is( ':checked' ) ) {
					$( 'div.payment_box.' + $( this ).attr( 'ID' ) ).slideDown( 250 );
				}
			}
		} else {
			$( 'div.payment_box' ).show();
		}
	})

	// Trigger initial click
	.find( 'input[name=payment_method]:checked' ).click();

	$( '#add_payment_method' ).submit( function() {
		$( '#add_payment_method' ).block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_params.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', backgroundSize: '16px 16px', opacity: 0.6 } });
	});

	$( 'body' ).trigger( 'init_add_payment_method' );

});
