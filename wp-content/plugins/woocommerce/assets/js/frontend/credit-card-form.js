jQuery( function( $ ) {
	$( '.wc-credit-card-form-card-number' ).payment( 'formatCardNumber' );
	$( '.wc-credit-card-form-card-expiry' ).payment( 'formatCardExpiry' );
	$( '.wc-credit-card-form-card-cvc' ).payment( 'formatCardCVC' );

	$( 'body' )
		.on( 'updated_checkout', function() {
			$( '.wc-credit-card-form-card-number' ).payment( 'formatCardNumber' );
			$( '.wc-credit-card-form-card-expiry' ).payment( 'formatCardExpiry' );
			$( '.wc-credit-card-form-card-cvc' ).payment( 'formatCardCVC' );
		});
} );