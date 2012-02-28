/**
 * navigation.js
 *
 * Handles toggling the navigation menu for small screens.
 */
jQuery( document ).ready( function( $ ) {
	var masthead = $( '#masthead' ),
	    timeout = false;

	$.fn.smallMenu = function() {
		masthead.find( '.site-navigation' ).removeClass( 'main-navigation' ).addClass( 'main-small-navigation' );
		masthead.find( '.site-navigation h3' ).removeClass( 'assistive-text' ).addClass( 'menu-toggle' );

		$( '.menu-toggle' ).click( function() {
			masthead.find( '.menu' ).toggle();
			$( this ).toggleClass( 'toggled-on' );
		} );
	};

	// Check viewport width on first load.
	if ( $( window ).width() < 600 )
		$.fn.smallMenu();

	// Check viewport width when user resizes the browser window.
	$( window ).resize( function() {
		var browserWidth = $( window ).width();

		if ( false !== timeout )
			clearTimeout( timeout );

		timeout = setTimeout( function() {
			if ( browserWidth < 600 ) {
				$.fn.smallMenu();
			} else {
				masthead.find( '.site-navigation' ).removeClass( 'main-small-navigation' ).addClass( 'main-navigation' );
				masthead.find( '.site-navigation h3' ).removeClass( 'menu-toggle' ).addClass( 'assistive-text' );
				masthead.find( '.menu' ).removeAttr( 'style' );
			}
		}, 200 );
	} );
} );