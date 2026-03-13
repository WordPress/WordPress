/**
 * Live-update changed settings in real time in the Customizer preview.
 */

( function( $ ) {
		api = wp.customize;

	// Site title.
	api( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.logo h1' ).text( to );
		} );
	} );

	// Site tagline.
	api( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.logo span' ).text( to );
		} );
	} );
} )( jQuery );