/* global twentytwentyoneGetHexLum, backgroundColorNotice */

( function() {
	// Wait until the customizer has finished loading.
	wp.customize.bind( 'ready', function() {
		var supportsDarkMode = ( 127 <= twentytwentyoneGetHexLum( wp.customize( 'background_color' ).get() ) && wp.customize( 'respect_user_color_preference' ).get() );

		// Hide the "respect_user_color_preference" setting if the background-color is dark.
		if ( 127 > twentytwentyoneGetHexLum( wp.customize( 'background_color' ).get() ) ) {
			wp.customize.control( 'respect_user_color_preference' ).deactivate();
		}

		// Add notice on init if needed.
		if ( window.matchMedia( '(prefers-color-scheme: dark)' ).matches && wp.customize( 'respect_user_color_preference' ) ) {
			if ( supportsDarkMode ) {
				wp.customize( 'background_color' ).notifications.add( 'backgroundColorNotice', new wp.customize.Notification( 'backgroundColorNotice', {
					type: 'info',
					message: backgroundColorNotice.message
				} ) );
			}

			// Remove notice when the value changes.
			wp.customize( 'background_color', function( setting ) {
				setting.bind( function() {
					setting.notifications.remove( 'backgroundColorNotice' );
				} );
			} );
		}

		// Handle changes to the background-color.
		wp.customize( 'background_color', function( setting ) {
			setting.bind( function( value ) {
				if ( 127 > twentytwentyoneGetHexLum( value ) ) {
					wp.customize.control( 'respect_user_color_preference' ).deactivate();
					supportsDarkMode = false;
				} else {
					wp.customize.control( 'respect_user_color_preference' ).activate();
					supportsDarkMode = wp.customize( 'respect_user_color_preference' ).get();
				}
			} );
		} );

		// Handle changes to the "respect_user_color_preference" setting.
		wp.customize( 'respect_user_color_preference', function( setting ) {
			setting.bind( function( value ) {
				supportsDarkMode = value && 127 < twentytwentyoneGetHexLum( wp.customize( 'background_color' ).get() );
				if ( window.matchMedia( '(prefers-color-scheme: dark)' ).matches ) {
					if ( ! supportsDarkMode ) {
						wp.customize( 'background_color' ).notifications.remove( 'backgroundColorNotice' );
					} else {
						wp.customize( 'background_color' ).notifications.add( 'backgroundColorNotice', new wp.customize.Notification( 'backgroundColorNotice', {
							type: 'info',
							message: backgroundColorNotice.message
						} ) );
					}
				}
			} );
		} );
	} );
}() );
