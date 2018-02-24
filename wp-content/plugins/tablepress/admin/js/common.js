/**
 * JavaScript code for all TablePress admin screens
 *
 * @package TablePress
 * @subpackage Views JavaScript
 * @author Tobias Bäthge
 * @since 1.0.0
 */

/* global confirm, tp, postboxes, pagenow, tp, tablepress_common */

// Ensure the global `tp` object exists.
window.tp = window.tp || {};

jQuery( document ).ready( function( $ ) {

	'use strict';

	/**
	 * Enable toggle/order functionality for post meta boxes
	 * For TablePress, pagenow has the form "tablepress_{$action}"
	 *
	 * @since 1.0.0
	 */
	postboxes.add_postbox_toggles( pagenow );

	/**
	 * Remove/add title to value on focus/blur of text fields "Table Name" and "Table Description" on "Add new Table" screen
	 *
	 * @since 1.0.0
	 */
	$( '#tablepress-page' )
	.on( 'focus', '.placeholder', function() {
		if ( this.value === this.defaultValue ) {
			this.value = '';
			$(this).removeClass( 'placeholder-active' );
		}
	} )
	.on( 'blur', '.placeholder', function() {
		if ( '' === this.value ) {
			this.value = this.defaultValue;
			$(this).addClass( 'placeholder-active' );
		}
	} );

	/**
	 * Check that numerical fields (e.g. column/row number fields) only contain numbers
	 *
	 * Provides this functionality for browsers that don't yet support <input type="number" />.
	 *
	 * @since 1.0.0
	 */
	$( '#tablepress-page' ).on( 'blur', '.numbers-only, .form-field-numbers-only input', function( /* event */ ) {
		var $input = $(this);
		$input.val( $input.val().replace( /[^0-9]/g, '' ) );
	} );

	/**
	 * Show a AYS warning when a "Delete" link is clicked
	 *
	 * @since 1.0.0
	 */
	$( '#tablepress-page' ).on( 'click', '.delete-link', function() {
		if ( ! confirm( tablepress_common.ays_delete_single_table ) ) {
			return false;
		}

		// Prevent onunload warning.
		tp.made_changes = false;
	} );

	/**
	 * Select all text in the Shortcode (readonly) text fields, when clicked
	 *
	 * @since 1.0.0
	 */
	$( '#tablepress-page' ).on( 'click', '.table-shortcode', function() {
		$(this).focus().select();
	} );

} );
