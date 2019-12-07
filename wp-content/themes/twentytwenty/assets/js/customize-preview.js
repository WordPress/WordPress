/* global twentyTwentyBgColors, twentyTwentyPreviewEls, jQuery, _, wp */
/**
 * Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * @since Twenty Twenty 1.0
 */

( function( $, api, _ ) {
	/**
	 * Return a value for our partial refresh.
	 *
	 * @param {Object} partial  Current partial.
	 *
	 * @return {jQuery.Promise} Resolved promise.
	 */
	function returnDeferred( partial ) {
		var deferred = new $.Deferred();

		deferred.resolveWith( partial, _.map( partial.placements(), function() {
			return '';
		} ) );

		return deferred.promise();
	}

	// Selective refresh for "Fixed Background Image"
	api.selectiveRefresh.partialConstructor.cover_fixed = api.selectiveRefresh.Partial.extend( {

		/**
		 * Override the refresh method
		 *
		 * @return {jQuery.Promise} Resolved promise.
		 */
		refresh: function() {
			var partial, cover, params;

			partial = this;
			params = partial.params;
			cover = $( params.selector );

			if ( cover.length && cover.hasClass( 'bg-image' ) ) {
				cover.toggleClass( 'bg-attachment-fixed' );
			}

			return returnDeferred( partial );
		}

	} );

	// Selective refresh for "Image Overlay Opacity"
	api.selectiveRefresh.partialConstructor.cover_opacity = api.selectiveRefresh.Partial.extend( {

		/**
		 * Input attributes.
		 *
		 * @type {Object}
		 */
		attrs: {},

		/**
		 * Override the refresh method
		 *
		 * @return {jQuery.Promise} Resolved promise.
		 */
		refresh: function() {
			var partial, ranges, attrs, setting, params, cover, className, classNames;

			partial = this;
			attrs = partial.attrs;
			ranges = _.range( attrs.min, attrs.max + attrs.step, attrs.step );
			params = partial.params;
			setting = api( params.primarySetting );
			cover = $( params.selector );

			if ( cover.length ) {
				classNames = _.map( ranges, function( val ) {
					return 'opacity-' + val;
				} );

				className = classNames[ ranges.indexOf( parseInt( setting.get(), 10 ) ) ];

				cover.removeClass( classNames.join( ' ' ) );
				cover.addClass( className );
			}

			return returnDeferred( partial );
		}

	} );

	// Add listener for the "header_footer_background_color" control.
	api( 'header_footer_background_color', function( value ) {
		value.bind( function( to ) {
			// Add background color to header and footer wrappers.
			$( 'body:not(.overlay-header)#site-header, #site-footer' ).css( 'background-color', to );

			// Change body classes if this is the same background-color as the content background.
			if ( to.toLowerCase() === api( 'background_color' ).get().toLowerCase() ) {
				$( 'body' ).addClass( 'reduced-spacing' );
			} else {
				$( 'body' ).removeClass( 'reduced-spacing' );
			}
		} );
	} );

	// Add listener for the "background_color" control.
	api( 'background_color', function( value ) {
		value.bind( function( to ) {
			// Change body classes if this is the same background-color as the header/footer background.
			if ( to.toLowerCase() === api( 'header_footer_background_color' ).get().toLowerCase() ) {
				$( 'body' ).addClass( 'reduced-spacing' );
			} else {
				$( 'body' ).removeClass( 'reduced-spacing' );
			}
		} );
	} );

	// Add listener for the accent color.
	api( 'accent_hue', function( value ) {
		value.bind( function() {
			// Generate the styles.
			// Add a small delay to be sure the accessible colors were generated.
			setTimeout( function() {
				Object.keys( twentyTwentyBgColors ).forEach( function( context ) {
					twentyTwentyGenerateColorA11yPreviewStyles( context );
				} );
			}, 50 );
		} );
	} );

	// Add listeners for background-color settings.
	Object.keys( twentyTwentyBgColors ).forEach( function( context ) {
		wp.customize( twentyTwentyBgColors[ context ].setting, function( value ) {
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
	 * @since Twenty Twenty 1.0
	 *
	 * @param {string} context The area for which we want to generate styles. Can be for example "content", "header" etc.
	 *
	 * @return {void}
	 */
	function twentyTwentyGenerateColorA11yPreviewStyles( context ) {
		// Get the accessible colors option.
		var a11yColors = window.parent.wp.customize( 'accent_accessible_colors' ).get(),
			stylesheedID = 'twentytwenty-customizer-styles-' + context,
			stylesheet = $( '#' + stylesheedID ),
			styles = '';
		// If the stylesheet doesn't exist, create it and append it to <head>.
		if ( ! stylesheet.length ) {
			$( '#twentytwenty-style-inline-css' ).after( '<style id="' + stylesheedID + '"></style>' );
			stylesheet = $( '#' + stylesheedID );
		}
		if ( ! _.isUndefined( a11yColors[ context ] ) ) {
			// Check if we have elements defined.
			if ( twentyTwentyPreviewEls[ context ] ) {
				_.each( twentyTwentyPreviewEls[ context ], function( items, setting ) {
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
	$( document ).ready( function() {
		twentyTwentyGenerateColorA11yPreviewStyles( 'content' );
		twentyTwentyGenerateColorA11yPreviewStyles( 'header-footer' );
	} );
}( jQuery, wp.customize, _ ) );
