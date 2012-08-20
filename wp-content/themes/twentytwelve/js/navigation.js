/**
 * navigation.js
 *
 * Handles toggling the navigation menu for small screens.
 */

jQuery( document ).ready( function( $ ) {
	if ( $( '#masthead .menu' ).children().length ) {
		$( '#masthead h3.assistive-text' ).addClass( 'menu-toggle' );
	}

	$( '.menu-toggle' ).off( 'click' ).click( function() {
		$( '#masthead .menu' ).stop().slideToggle();
		$( this ).toggleClass( 'toggled-on' );
	} );
} );