/**
 * JavaScript code for the "Import" screen
 *
 * @package TablePress
 * @subpackage Views JavaScript
 * @author Tobias Bäthge
 * @since 1.0.0
 */

/* global alert, tablepress_import */

jQuery( document ).ready( function( $ ) {

	'use strict';

	/**
	 * Show select box for table to replace only if needed
	 *
	 * @since 1.0.0
	 */
	$( '#row-import-type' ).on( 'change', 'input', function() {
		$( '#tables-import-existing-table' ).prop( 'disabled', ( 'replace' !== $(this).val() && 'append' !== $(this).val() ) );
	} )
	.find( 'input:checked' ).change();

	/**
	 * Show only the import source field that was selected with the radio button
	 *
	 * @since 1.0.0
	 */
	$( '#row-import-source' ).on( 'change', 'input', function() {
		$( '#row-import-source-file-upload, #row-import-source-url, #row-import-source-server, #row-import-source-form-field' ).hide();
		$( '#row-import-source-' + $(this).val() ).show();
	} )
	.find( 'input:checked' ).change();

	/**
	 * Show only the WP-Table Reloaded import source field that was selected with the radio button
	 *
	 * @since 1.0.0
	 */
	$( '#row-import-wp-table-reloaded-source' ).on( 'change', 'input', function() {
		$( '#row-import-wp-table-reloaded-source-dump-file, #row-import-wp-table-reloaded-source-db' ).hide();
		$( '#row-import-wp-table-reloaded-source-' + $(this).val() ).show();
	} )
	.find( 'input:checked' ).change();

	/**
	 * Select correct value in import format dropdown on file select
	 *
	 * @since 1.0.0
	 */
	$( '#tables-import-file-upload, #tables-import-url, #tables-import-server' ).on( 'change', function( event ) {
		var path = $(this).val(),
			filename_start,
			extension_start,
			filename = path,
			extension = 'csv';

		// default extension: CSV for file upload and server, HTML for URL
		if ( 'tables-import-url' === event.target.id ) {
			extension = 'html';
		}
		// determine filename from full path
		filename_start = path.lastIndexOf( '\\' );
		if ( -1 !== filename_start ) { // Windows-based path
			filename = path.substr( filename_start + 1 );
		} else {
			filename_start = path.lastIndexOf( '/' );
			if ( -1 !== filename_start ) { // Windows-based path
				filename = path.substr( filename_start + 1 );
			}
		}
		// determine extension from filename
		extension_start = filename.lastIndexOf( '.' );
		if ( -1 !== extension_start ) {
			extension = filename.substr( extension_start + 1 ).toLowerCase();
		}

		// allow .htm for HTML as well
		if ( 'htm' === extension ) {
			extension = 'html';
		}

		// Don't change the format for ZIP archives
		if ( 'zip' === extension ) {
			return;
		}

		$( '#tables-import-format' ).val( extension );
	} );

	/**
	 * Check, whether inputs are valid
	 *
	 * @since 1.0.0
	 */
	$( '#tablepress-page' ).find( 'form' ).on( 'submit.tablepress', function( /* event */ ) {
		var import_source = $( '#row-import-source' ).find( 'input:checked' ).val(),
			selected_import_source_field = $( '#tables-import-' + import_source ).get(0),
			valid_form = true,
			import_type = $( '#row-import-type' ).find( 'input:checked' ).val();

		/* the value of the selected import source field must be set/changed from the default */
		if ( selected_import_source_field.defaultValue === selected_import_source_field.value ) {
			$( selected_import_source_field )
				.addClass( 'invalid' )
				.one( 'change', function() { $(this).removeClass( 'invalid' ); } )
				.focus().select();
			valid_form = false;
		}

		/* if replace or append is selected, a table must be selected */
		if ( 'replace' === import_type || 'append' === import_type ) {
			if ( '' === $( '#tables-import-existing-table' ).val() ) {
				$( '#row-import-type' )
					.one( 'change', 'input', function() { $( '#tables-import-existing-table' ).removeClass( 'invalid' ); } );
				$( '#tables-import-existing-table' )
					.addClass( 'invalid' )
					.one( 'change', function() { $(this).removeClass( 'invalid' ); } )
					.focus().select();
				valid_form = false;
			}
		}

		if ( ! valid_form ) {
			return false;
		}
		// at this point, the form is valid and will be submitted
	} );

	/**
	 * Remove form validation check if "Import from WP-Table Reloaded" button is clicked
	 *
	 * @since 1.0.0
	 */
	$( '#tablepress-page' ).find( '#submit_wp_table_reloaded_import' ).on( 'click', function() {
		$( '#tablepress-page' ).find( 'form' ).off( 'submit.tablepress' );

		/* File upload must have a file, if Dump File is selected as the source */
		if ( $( '#import-wp-table-reloaded-source-dump-file' ).prop( 'checked' ) && '' === $( '#tables-import-wp-table-reloaded-dump-file' ).val() ) {
			$( '#tables-import-wp-table-reloaded-dump-file' )
				.addClass( 'invalid' )
				.one( 'change', function() { $(this).removeClass( 'invalid' ); } )
				.focus().select();
			return false;
		}

		/* At least one checkbox must be check, to have something imported */
		if ( ! $( '#import-wp-table-reloaded-tables' ).prop( 'checked' ) && ! $( '#import-wp-table-reloaded-css' ).prop( 'checked' ) ) {
			alert( tablepress_import.error_wp_table_reloaded_nothing_selected );
			return false;
		}

	} );

} );
