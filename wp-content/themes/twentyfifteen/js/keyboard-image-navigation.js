/**
 * Twenty Fifteen keyboard support for image navigation.
 */

( function( $ ) {
	$( document ).on( 'keydown.twentyfifteen', function( e ) {
		var url = false;

		// Left arrow key code.
		if ( e.which === 37 ) {
			url = $( '.nav-previous a' ).attr( 'href' );

		// Right arrow key code.
		} else if ( e.which === 39 ) {
			url = $( '.nav-next a' ).attr( 'href' );
		}

		if ( url && ( ! $( 'textarea, input' ).is( ':focus' ) ) ) {
			window.location = url;
		}
	} );
} )( jQuery );
