/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

(function( $ ) {

	// Default color.
	wp.customize( 'colorscheme', function( value ) {
		value.bind( function( to ) {
			// Update custom color CSS.
			var style = $( '#custom-theme-colors' ),
				hue = style.data( 'hue' ),
				css = style.html(),
				color;

			if( to  === 'custom' ){
				//If a "custom" color option is selected, use the currently set colorscheme_primary_hue
				color = wp.customize.get().colorscheme_primary_hue;
			} else {
				//If the "default" option is selected, get the default primary_hue
				color = 199;
			}

			// Equivalent to css.replaceAll, with hue followed by comma to prevent values with units from being changed.
			css = css.split( hue + ',' ).join( color + ',' );
			style.html( css ).data( 'hue', color );
		});
	});

	// Primary color.
	wp.customize( 'colorscheme_primary_hue', function( value ) {
		value.bind( function( to ) {

			// Update custom color CSS.
			var style = $( '#custom-theme-colors' ),
				hue = style.data( 'hue' ),
				css = style.html();

			// Equivalent to css.replaceAll, with hue followed by comma to prevent values with units from being changed.
			css = css.split( hue + ',' ).join( to + ',' );
			style.html( css ).data( 'hue', to );
		});
	});

	// Image filter.
	wp.customize( 'image_filter', function( value ) {
		value.bind( function( to ) {
			if ( 'active' === to ) {
				$( 'body' ).addClass( 'image-filters-enabled' );
			} else {
				$( 'body' ).removeClass( 'image-filters-enabled' );
			}
		} );
	} );

})( jQuery );
