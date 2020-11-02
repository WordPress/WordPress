/* global twentytwentyoneGetHexLum, backgroundColorNotice */

( function() {
	/**
	 * Add/remove the notice.
	 *
	 * @param {boolean} enable - Whether we want to enable or disable the notice.
	 *
	 * @return {void}
	 */
	function twentytwentyoneBackgroundColorNotice( enable ) {
		if ( enable ) {
			wp.customize( 'background_color' ).notifications.add( 'backgroundColorNotice', new wp.customize.Notification( 'backgroundColorNotice', {
				type: 'info',
				message: backgroundColorNotice.message
			} ) );
		} else {
			wp.customize( 'background_color' ).notifications.remove( 'backgroundColorNotice' );
		}
	}

	// Wait until the customizer has finished loading.
	wp.customize.bind( 'ready', function() {
		var supportsDarkMode = ( 127 <= twentytwentyoneGetHexLum( wp.customize( 'background_color' ).get() ) && wp.customize( 'respect_user_color_preference' ).get() );

		// Hide the "respect_user_color_preference" setting if the background-color is dark.
		if ( 127 > twentytwentyoneGetHexLum( wp.customize( 'background_color' ).get() ) ) {
			wp.customize.control( 'respect_user_color_preference' ).deactivate();
		}

		// Add notice on init if needed.
		if ( wp.customize( 'respect_user_color_preference' ) ) {
			twentytwentyoneBackgroundColorNotice( true );
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
				if ( ! supportsDarkMode ) {
					twentytwentyoneBackgroundColorNotice( false );
				} else {
					twentytwentyoneBackgroundColorNotice( true );
				}
			} );
		} );
	} );
}() );
