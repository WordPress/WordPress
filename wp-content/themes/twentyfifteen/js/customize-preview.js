/**
 * Live-update changed settings in real time in the Customizer preview. 
 */

( function( $ ) {
	var $style = $( '#twentyfifteen-color-scheme-css' );

	if ( ! $style.length ) {
		$style = $( 'head' ).append( '<style type="text/css" id="twentyfifteen-color-scheme-css" />' )
		                    .find( '#twentyfifteen-color-scheme-css' );
	}

	// Site title and description.
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-title a' ).text( to );
		} );
	} );

	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-description' ).text( to );
		} );
	} );
	
	wp.customize( 'color_scheme_css', function( value ) {
		value.bind( function( to ) {
			$style.html( to );
		} );
	} );

} )( jQuery );