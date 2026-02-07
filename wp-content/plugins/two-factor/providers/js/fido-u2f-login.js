/* global window, u2f, u2fL10n, jQuery */
( function( $ ) {
	if ( ! window.u2fL10n ) {
		window.console.error( 'u2fL10n is not defined' );
		return;
	}

	u2f.sign( u2fL10n.request[0].appId, u2fL10n.request[0].challenge, u2fL10n.request, function( data ) {
		if ( data.errorCode ) {
			window.console.error( 'Registration Failed', data.errorCode );
		} else {
			$( '#u2f_response' ).val( JSON.stringify( data ) );
			$( '#loginform' ).submit();
		}
	} );
}( jQuery ) );
