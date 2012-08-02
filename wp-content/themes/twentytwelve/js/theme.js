/**
 * navigation.js
 *
 * Handles toggling the navigation menu for small screens.
 */

jQuery( document ).ready( function( $ ) {
	var masthead = $( '#masthead' ),
		largeWindow = window.matchMedia( 'screen and (min-width: 600px)' ),
		timeout = false;

	$.fn.smallMenu = function() {
		masthead.find( '.site-navigation' ).removeClass( 'main-navigation' ).addClass( 'main-small-navigation' );
		masthead.find( '.site-navigation h3' ).removeClass( 'assistive-text' ).addClass( 'menu-toggle' );

		$( '.menu-toggle' ).off( 'click' ).click( function() {
			masthead.find( '.menu' ).slideToggle();
			$( this ).toggleClass( 'toggled-on' );
		} );
	};

	// Check viewport width on first load.
	if ( ! largeWindow.matches )
		$.fn.smallMenu();

	// Check viewport width when user resizes the browser window.
	$( window ).resize( function() {
		if ( false !== timeout )
			clearTimeout( timeout );

		timeout = setTimeout( function() {
			if ( ! largeWindow.matches ) {
				$.fn.smallMenu();
			} else {
				masthead.find( '.site-navigation' ).removeClass( 'main-small-navigation' ).addClass( 'main-navigation' );
				masthead.find( '.site-navigation h3' ).removeClass( 'menu-toggle' ).addClass( 'assistive-text' );
				masthead.find( '.menu' ).removeAttr( 'style' );
			}
		}, 200 );
	} );
} );