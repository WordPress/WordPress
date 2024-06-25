/**
 * Twenty Fourteen keyboard support for image navigation.
 */
( function( $ ) {
	$( document ).on( 'keydown.twentyfourteen', function( e ) {
		var url = false;

		// Left arrow key code.
		if ( e.which === 37 ) {
			url = $( '.previous-image a' ).attr( 'href' );

		// Right arrow key code.
		} else if ( e.which === 39 ) {
			url = $( '.entry-attachment a' ).attr( 'href' );
		}

		if ( url && ( ! $( 'textarea, input' ).is( ':focus' ) ) ) {
			window.location = url;
		}
	} );
} )( jQuery );