/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 * Things like fonts, site title and description, and background color changes.
 *
 * See related settings in Twenty_Twelve_Options::customize_preview_js()
 */

( function( $ ) {
	// Site title and description.
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-title a' ).html( to );
		} );
	} );
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-description' ).html( to );
		} );
	} );

	// Custom fonts.
	wp.customize( twentytwelve_customizer.option_key + '[enable_fonts]', function( value ) {
		value.bind( function( to ) {
			if ( to ) {
				$( 'head' ).append( '<link rel="stylesheet" id="twentytwelve-fonts-css" href="' + twentytwelve_customizer.link + '" type="text/css" media="all" />' );
			} else {
				$( '#twentytwelve-fonts-css' ).remove();
			}
		} );
	} );

	// Hook into background color change and adjust body class value as needed.
	wp.customize( 'background_color', function( value ) {
		var body = $( 'body' );
		value.bind( function( to ) {
			if ( '#ffffff' == to || '#fff' == to || '' == to )
				body.addClass( 'custom-background-white' );
			else if ( '' == to )
				body.addClass( 'custom-background-empty' );
			else
				body.removeClass( 'custom-background-empty custom-background-white' );
		} );
	} );
} )( jQuery );