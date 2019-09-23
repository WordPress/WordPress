/* global backgroundColors, previewElements, jQuery, _, wp */
/**
 * Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * @since 1.0.0
 */

( function() {
	// Add listener for the "header_footer_background_color" control.
	wp.customize( 'header_footer_background_color', function( value ) {
		value.bind( function( to ) {
			// Add background color to header and footer wrappers.
			jQuery( '#site-header,#site-footer' ).css( 'background-color', to );
		} );
	} );

	// Add listener for the accent color.
	wp.customize( 'accent_hue', function( value ) {
		value.bind( function() {
			// Generate the styles.
			// Add a small delay to be sure the accessible colors were generated.
			setTimeout( function() {
				Object.keys( backgroundColors ).forEach( function( context ) {
					twentyTwentyGenerateColorA11yPreviewStyles( context );
				} );
			}, 50 );
		} );
	} );

	// Add listeners for background-color settings.
	Object.keys( backgroundColors ).forEach( function( context ) {
		wp.customize( backgroundColors[ context ].setting, function( value ) {
			value.bind( function() {
				// Generate the styles.
				// Add a small delay to be sure the accessible colors were generated.
				setTimeout( function() {
					twentyTwentyGenerateColorA11yPreviewStyles( context );
				}, 50 );
			} );
		} );
	} );

	/**
	 * Add styles to elements in the preview pane.
	 *
	 * @since 1.0.0
	 *
	 * @param {string} context The area for which we want to generate styles. Can be for example "content", "header" etc.
	 *
	 * @return {void}
	 */
	function twentyTwentyGenerateColorA11yPreviewStyles( context ) {
		// Get the accessible colors option.
		var a11yColors = window.parent.wp.customize( 'accent_accessible_colors' ).get(),
			stylesheedID = 'twentytwenty-customizer-styles-' + context,
			stylesheet = jQuery( '#' + stylesheedID ),
			styles = '';
		// If the stylesheet doesn't exist, create it and append it to <head>.
		if ( ! stylesheet.length ) {
			jQuery( '#twentytwenty-style-inline-css' ).after( '<style id="' + stylesheedID + '"></style>' );
			stylesheet = jQuery( '#' + stylesheedID );
		}
		if ( ! _.isUndefined( a11yColors[ context ] ) ) {
			// Check if we have elements defined.
			if ( previewElements[ context ] ) {
				_.each( previewElements[ context ], function( items, setting ) {
					_.each( items, function( elements, property ) {
						if ( ! _.isUndefined( a11yColors[ context ][ setting ] ) ) {
							styles += elements.join( ',' ) + '{' + property + ':' + a11yColors[ context ][ setting ] + ';}';
						}
					} );
				} );
			}
		}
		// Add styles.
		stylesheet.html( styles );
	}
	// Generate styles on load. Handles page-changes on the preview pane.
	jQuery( document ).ready( function() {
		twentyTwentyGenerateColorA11yPreviewStyles( 'content' );
		twentyTwentyGenerateColorA11yPreviewStyles( 'header-footer' );
	} );
}() );
