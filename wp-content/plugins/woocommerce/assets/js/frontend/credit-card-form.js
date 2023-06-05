jQuery( function( $ ) {
	$( '.wc-credit-card-form-card-number' ).payment( 'formatCardNumber' );
	$( '.wc-credit-card-form-card-expiry' ).payment( 'formatCardExpiry' );
	$( '.wc-credit-card-form-card-cvc' ).payment( 'formatCardCVC' );

	$( document.body )
		.on( 'updated_checkout wc-credit-card-form-init', function() {
			$( '.wc-credit-card-form-card-number' ).payment( 'formatCardNumber' );
			$( '.wc-credit-card-form-card-expiry' ).payment( 'formatCardExpiry' );
			$( '.wc-credit-card-form-card-cvc' ).payment( 'formatCardCVC' );
		})
		.trigger( 'wc-credit-card-form-init' );
} );
