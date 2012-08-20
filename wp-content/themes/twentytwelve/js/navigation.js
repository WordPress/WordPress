/**
 * navigation.js
 *
 * Handles toggling the navigation menu for small screens.
 */

jQuery( document ).ready( function( $ ) {
	if ( ! $( '#masthead .menu' ).children().length ) {
		$( '#masthead .menu-toggle' ).hide();
	}

	$( '.menu-toggle' ).off( 'click' ).click( function() {
		$( '#masthead .menu' ).stop().slideToggle();
		$( this ).toggleClass( 'toggled-on' );
	} );
} );