/**
 * JavaScript code for the "Export" screen
 *
 * @package TablePress
 * @subpackage Views JavaScript
 * @author Tobias Bäthge
 * @since 1.0.0
 */

jQuery( document ).ready( function( $ ) {

	'use strict';

	/**
	 * Check, whether inputs are valid
	 *
	 * @since 1.0.0
	 */
	$( '#tablepress-page' ).find( 'form' ).on( 'submit', function( /* event */ ) {
		var selected_tables = $( '#tables-export' ).val(),
			num_selected = ( selected_tables ) ? selected_tables.length : 0;

		// only submit form, if at least one table was selected
		if ( 0 === num_selected ) {
			return false;
		}

		// at this point, the form is valid and will be submitted

		// add selected tables as a list to a hidden field
		$( '#tables-export-list' ).val( selected_tables.join( ',' ) );

		// on form submit: Enable disabled fields, so that they are transmitted in the POST request
		$( '#tables-export-zip-file' ).prop( 'disabled', false );
	} );

	/**
	 * Show export delimiter dropdown box only if export format is CSV
	 *
	 * @since 1.0.0
	 */
	$( '#tables-export-format' ).on( 'change', function() {
		var non_csv_selected = ( 'csv' !== $(this).val() );
		$( '#tables-export-csv-delimiter' ).prop( 'disabled', non_csv_selected );
		$( '#tables-export-csv-delimiter-description' ).toggle( non_csv_selected );
	} )
	.change();

	/**
	 * Automatically check and disable the "ZIP file" checkbox whenever more than one table is selected
	 *
	 * @since 1.0.0
	 */
	var zip_file_manually_checked = false;
	$( '#tables-export-zip-file' ).on( 'change', function() {
		zip_file_manually_checked = $(this).prop( 'checked' );
	} );
	$( '#tables-export' ).on( 'change', function() {
		var selected_tables = $(this).val(),
			num_selected = ( selected_tables ) ? selected_tables.length : 0,
			zip_file_required = ( num_selected > 1 );
		$( '#tables-export-zip-file' )
			.prop( 'disabled', zip_file_required )
			.prop( 'checked', zip_file_required || zip_file_manually_checked );
		$( '#tables-export-zip-file-description' ).toggle( zip_file_required );
		// set state of "Select all" checkbox
		$( '#tables-export-select-all' ).prop( 'checked', 0 === $(this).find( 'option' ).not( ':selected' ).length );
	} )
	.change();

	/**
	 * Select all entries from the multiple-select dropdown on checkbox change
	 *
	 * @since 1.0.0
	 */
	$( '#tables-export-select-all' ).on( 'change', function() {
		var $tables = $( '#tables-export' );
		$tables.find( 'option' ).prop( 'selected', $(this).prop( 'checked' ) );
		$tables.change(); // to update ZIP file checkbox
	} );

	/**
	 * Automatically focus the tables dropdown
	 *
	 * @since 1.0.0
	 */
	$( '#tables-export' ).focus();

} );
