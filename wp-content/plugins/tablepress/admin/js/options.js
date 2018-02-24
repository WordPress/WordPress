/**
 * JavaScript code for the "Options" screen, without the CodeMirror handling
 *
 * @package TablePress
 * @subpackage Views JavaScript
 * @author Tobias Bäthge
 * @since 1.0.0
 */

/* global confirm, tablepress_strings */

jQuery( document ).ready( function( $ ) {

	'use strict';

	/**
	 * Enable/disable the regular textarea according to state of "Load Custom CSS" checkbox.
	 *
	 * @since 1.0.0
	 */
	$( '#option-use-custom-css' ).on( 'change', function() {
		$( '#option-custom-css' ).prop( 'disabled', ! $(this).prop( 'checked' ) );
	} ).change();

	/**
	 * On form submit: Enable disabled fields, so that they are transmitted in the POST request.
	 *
	 * @since 1.0.0
	 */
	$( '#tablepress-page' ).on( 'submit', 'form', function() {
		$(this).find( 'input, select, textarea' ).prop( 'disabled', false );
	} );

	/**
	 * Require double confirmation when wanting to uninstall TablePress.
	 *
	 * @since 1.0.0
	 */
	$( '#uninstall-tablepress' ).on( 'click', function() {
		if ( confirm( tablepress_strings.uninstall_warning_1 ) ) {
			return confirm( tablepress_strings.uninstall_warning_2 );
		} else {
			return false;
		}
	} );

} );
