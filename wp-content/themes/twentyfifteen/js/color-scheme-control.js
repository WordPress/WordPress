/* global colorScheme */
/**
 * Customizer enhancements for a better user experience.
 *
 * Adds listener to Color Scheme control to update other color controls with new values/defaults
 */

( function( api ) {
	api.controlConstructor.select = api.Control.extend( {
		ready: function() {
			if ( 'color_scheme' === this.id ) {
				this.setting.bind( 'change', function( value ) {
					// If Header Text is not hidden, update value.
					if ( 'blank' !== api( 'header_textcolor' ).get() ) {
						api( 'header_textcolor' ).set( colorScheme[value].colors[4] );
						api.control( 'header_textcolor' ).container.find( '.color-picker-hex' )
							.data( 'data-default-color', colorScheme[value].colors[4] )
							.wpColorPicker( 'defaultColor', colorScheme[value].colors[4] );
					}

					// Update Background Color.
					api( 'background_color' ).set( colorScheme[value].colors[0] );
					api.control( 'background_color' ).container.find( '.color-picker-hex' )
						.data( 'data-default-color', colorScheme[value].colors[0] )
						.wpColorPicker( 'defaultColor', colorScheme[value].colors[0] );

					// Update Header/Sidebar Background Color.
					api( 'header_background_color' ).set( colorScheme[value].colors[1] );
					api.control( 'header_background_color' ).container.find( '.color-picker-hex' )
						.data( 'data-default-color', colorScheme[value].colors[1] )
						.wpColorPicker( 'defaultColor', colorScheme[value].colors[1] );

					// Update Sidebar Text Color.
					api( 'sidebar_textcolor' ).set( colorScheme[value].colors[4] );
					api.control( 'sidebar_textcolor' ).container.find( '.color-picker-hex' )
						.data( 'data-default-color', colorScheme[value].colors[4] )
						.wpColorPicker( 'defaultColor', colorScheme[value].colors[4] );
				} );
			}
		}
	} );
} )( wp.customize );
