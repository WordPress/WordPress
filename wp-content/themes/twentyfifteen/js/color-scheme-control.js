/* global colorScheme */
/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Adds listener to Color Scheme control to update other color controls with new values/defaults
 */

( function( wp ) {
	wp.customize.controlConstructor.colorScheme = wp.customize.Control.extend( {
		ready: function() {
			var parentSection    = this.container.closest( '.control-section' ),
				headerTextColor  = parentSection.find( '#customize-control-header_textcolor .color-picker-hex' ),
				backgroundColor  = parentSection.find( '#customize-control-background_color .color-picker-hex' ),
				sidebarColor     = parentSection.find( '#customize-control-header_background_color .color-picker-hex' ),
				sidebarTextColor = parentSection.find( '#customize-control-sidebar_textcolor .color-picker-hex' );

			this.setting.bind( 'change', function( value ) {
				// if Header Text is not hidden, update value
				if ( 'blank' !== wp.customize( 'header_textcolor' ).get() ) {
					wp.customize( 'header_textcolor' ).set( colorScheme[value].colors[4] );
					headerTextColor.val( colorScheme[value].colors[4] )
						.data( 'data-default-color', colorScheme[value].colors[4] )
						.wpColorPicker( 'color', colorScheme[value].colors[4] )
						.wpColorPicker( 'defaultColor', colorScheme[value].colors[4] );
				}

				// update Background Color
				wp.customize( 'background_color' ).set( colorScheme[value].colors[0] );
				backgroundColor.val( colorScheme[value].colors[0] )
					.data( 'data-default-color', colorScheme[value].colors[0] )
					.wpColorPicker( 'color', colorScheme[value].colors[0] )
					.wpColorPicker( 'defaultColor', colorScheme[value].colors[0] );

				// update Header/Sidebar Background Color
				wp.customize( 'header_background_color' ).set( colorScheme[value].colors[1] );
				sidebarColor.val( colorScheme[value].colors[1] )
					.data( 'data-default-color', colorScheme[value].colors[1] )
					.wpColorPicker( 'color', colorScheme[value].colors[1] )
					.wpColorPicker( 'defaultColor', colorScheme[value].colors[1] );

				// update Sidebar Text Color
				wp.customize( 'sidebar_textcolor' ).set( colorScheme[value].colors[4] );
				sidebarTextColor.val( colorScheme[value].colors[4] )
					.data( 'data-default-color', colorScheme[value].colors[4] )
					.wpColorPicker( 'color', colorScheme[value].colors[4] )
					.wpColorPicker( 'defaultColor', colorScheme[value].colors[4] );
			} );
		}
	} );
} )( this.wp );