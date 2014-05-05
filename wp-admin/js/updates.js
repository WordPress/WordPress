window.wp = window.wp || {};

(function( $, wp ) {

	wp.updates = {};

	/**
	 * Decrement update counts throughout the various menus
	 *
	 * @param {string} updateType
	 */
	wp.updates.decrementCount = function( upgradeType ) {
		var count, pluginCount, $elem;

		$elem = $( '#wp-admin-bar-updates .ab-label' );
		count = $elem.text();
		count = parseInt( count, 10 ) - 1;
		if ( count < 0 ) {
			return;
		}
		$( '#wp-admin-bar-updates .ab-item' ).removeAttr( 'title' );
		$elem.text( count );

		$elem = $( 'a[href="update-core.php"] .update-plugins' );
		$elem.each( function( index, elem ) {
			elem.className = elem.className.replace( /count-\d+/, 'count-' + count );
		} );
		$elem.removeAttr( 'title' );
		$elem.find( '.update-count' ).text( count );

		if ( 'plugin' === upgradeType ) {
			$elem = $( '#menu-plugins' );
			pluginCount = $elem.find( '.plugin-count' ).eq(0).text();
			pluginCount = parseInt( pluginCount, 10 ) - 1;
			if ( pluginCount < 0 ) {
				return;
			}
			$elem.find( '.plugin-count' ).text( pluginCount );
			$elem.find( '.update-plugins' ).each( function( index, elem ) {
				elem.className = elem.className.replace( /count-\d+/, 'count-' + pluginCount );
			} );
		}
	};

	$( window ).on( 'message', function( e ) {
		var event = e.originalEvent,
			message,
			loc = document.location,
			expectedOrigin = loc.protocol + '//' + loc.hostname;

		if ( event.origin !== expectedOrigin ) {
			return;
		}

		message = $.parseJSON( event.data );

		if ( typeof message.action === 'undefined' || message.action !== 'decrementUpdateCount' ) {
			return;
		}

		wp.updates.decrementCount( message.upgradeType );

	} );

})( jQuery, window.wp );
