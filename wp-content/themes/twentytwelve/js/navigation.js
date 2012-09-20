/**
 * navigation.js
 *
 * Handles toggling the navigation menu for small screens.
 */
( function() {
	var button = document.getElementById( 'site-navigation' ).getElementsByTagName( 'h3' )[0],
	    menu   = document.getElementById( 'site-navigation' ).getElementsByTagName( 'ul' )[0];

	if ( undefined === button )
		return false;

	// Hide button if menu is missing or empty.
	if ( undefined === menu || ! menu.childNodes.length ) {
		button.style.display = 'none';
		return false;
	}

	button.onclick = function() {
		if ( -1 == menu.className.indexOf( 'nav-menu' ) )
			menu.className = 'nav-menu';

		if ( -1 != button.className.indexOf( 'toggled-on' ) ) {
			button.className = button.className.replace( ' toggled-on', '' );
			menu.className = menu.className.replace( ' toggled-on', '' );
		} else {
			button.className += ' toggled-on';
			menu.className += ' toggled-on';
		}
	};
} )();