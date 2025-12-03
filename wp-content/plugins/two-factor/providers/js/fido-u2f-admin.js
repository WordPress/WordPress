/* global window, u2fL10n, jQuery */
( function( $ ) {
	var $button = $( '#register_security_key' );
	var $statusNotice = $( '#security-keys-section .security-key-status' );
	var u2fSupported = ( window.u2f && 'register' in window.u2f );

	if ( ! u2fSupported ) {
		$statusNotice.text( u2fL10n.text.u2f_not_supported );
	}

	$button.click( function() {
		var registerRequest;

		if ( $( this ).prop( 'disabled' ) ) {
			return false;
		}

		$( this ).prop( 'disabled', true );
		$( '.register-security-key .spinner' ).addClass( 'is-active' );
		$statusNotice.text( '' );

		registerRequest = {
			version: u2fL10n.register.request.version,
			challenge: u2fL10n.register.request.challenge
		};

		window.u2f.register( u2fL10n.register.request.appId, [ registerRequest ], u2fL10n.register.sigs, function( data ) {
			$( '.register-security-key .spinner' ).removeClass( 'is-active' );
			$button.prop( 'disabled', false );

			if ( data.errorCode ) {
				if ( u2fL10n.text.error_codes[ data.errorCode ] ) {
					$statusNotice.text( u2fL10n.text.error_codes[ data.errorCode ] );
				} else {
					$statusNotice.text( u2fL10n.text.error_codes[ u2fL10n.text.error ] );
				}

				return false;
			}

			$( '#do_new_security_key' ).val( 'true' );
			$( '#u2f_response' ).val( JSON.stringify( data ) );

			// See: http://stackoverflow.com/questions/833032/submit-is-not-a-function-error-in-javascript
			$( '<form>' )[0].submit.call( $( '#your-profile' )[0] );
		} );
	} );
}( jQuery ) );
