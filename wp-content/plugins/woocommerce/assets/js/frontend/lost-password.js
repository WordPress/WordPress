jQuery( function( $ ) {
	$( '.lost_reset_password' ).on( 'submit', function () {
		$( 'button[type="submit"]', this ).attr( 'disabled', 'disabled' );
	});
});
