/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {

	// Collect information from panel-customizer.js about which panels are opening
	wp.customize.bind( 'preview-ready', function() {
		wp.customize.preview.bind( 'section-highlight', function( data ) {

			// If there's an expanded panel section, scroll down to that panel & highlight in the preview
			if ( true === data.expanded ) {
				$.scrollTo( $( '.' + data.section ), {
					duration: 600,
					offset: { 'top': -40 }
				} );
				$( '.' + data.section ).addClass( 'twentyseventeen-highlight' );

			// If we've left the panel, remove the highlight and scroll back to the top
			} else {
				$.scrollTo( $( '#masthead' ), {
					duration: 300,
					offset: { 'top': 0 }
				} );
				$( '.' + data.section ).removeClass( 'twentyseventeen-highlight' );
			}
		} );
	} );

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

	// Header text color.
	wp.customize( 'header_textcolor', function( value ) {
		value.bind( function( to ) {
			if ( 'blank' === to ) {
				$( '.site-title, .site-description' ).css( {
					'clip': 'rect(1px, 1px, 1px, 1px)',
					'position': 'absolute'
				} );
				$( 'body' ).addClass( 'title-tagline-hidden' );
			} else {
				$( '.site-title, .site-description' ).css( {
					'clip': 'auto',
					'position': 'relative'
				} );
				$( '.site-branding, .site-branding a, .site-description, .site-description a' ).css( {
					'color': to
				} );
				$( 'body' ).removeClass( 'title-tagline-hidden' );
			}
		} );
	} );

	// Color scheme.
	wp.customize( 'colorscheme', function( value ) {
		value.bind( function( to ) {

			// Update color body class.
			$( 'body' ).removeClass( 'colors-light colors-dark colors-custom' )
				.addClass( 'colors-' + to );
		} );
	} );

	// Custom color hue.
	wp.customize( 'colorscheme_hue', function( value ) {
		value.bind( function( to ) {

			// Update custom color CSS
			var style = $( '#custom-theme-colors' ),
			    hue = style.data( 'hue' ),
			    css = style.html();

			css = css.split( hue + ',' ).join( to + ',' ); // Equivalent to css.replaceAll, with hue followed by comma to prevent values with units from being changed.
			style.html( css )
			     .data( 'hue', to );
		} );
	} );

	// Page layouts.
	wp.customize( 'page_options', function( value ) {
		value.bind( function( to ) {
			if ( 'one-column' === to ) {
				$( 'body' ).addClass( 'page-one-column' ).removeClass( 'page-two-column' );
			} else {
				$( 'body' ).removeClass( 'page-one-column' ).addClass( 'page-two-column' );
			}
		} );
	} );
} )( jQuery );
