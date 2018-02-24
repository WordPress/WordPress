/**
 * JavaScript code for the "Table" button in the QuickTags editor toolbar
 *
 * @package TablePress
 * @subpackage Views JavaScript
 * @author Tobias Bäthge
 * @since 1.0.0
 */

/* global tablepress_editor_button, QTags, tb_show */

jQuery( document ).ready( function( $ ) {

	'use strict';

	/**
	 * Open a Thickbox with the List of tables, for adding a Shortcode to the editor
	 * used by both the TinyMCE editor button and the Quicktags toolbar button
	 *
	 * @since 1.0.0
	 */
	window.tablepress_open_shortcode_thickbox = function() {
		var width = $( window ).width(),
			W = ( 720 < width ) ? 720 : width,
			H = $( window ).height();
		if ( $( '#wpadminbar' ).length ) {
			H -= parseInt( jQuery( '#wpadminbar' ).css( 'height' ), 10 );
		}

		tb_show( tablepress_editor_button.thickbox_title, tablepress_editor_button.thickbox_url + '&TB_iframe=true&height=' + ( H - 85 ) + '&width=' + ( W - 80 ), false );
	};

	// only do this if QuickTags is available
	if ( 'undefined' === typeof( QTags ) ) {
		return;
	}

	/**
	 * Register a button for the Quicktags (aka HTML editor) toolbar
	 *
	 * @since 1.0.0
	 */
	QTags.addButton(
		'tablepress_quicktags_button',				// ID
		tablepress_editor_button.caption,			// button caption
		window.tablepress_open_shortcode_thickbox,	// click callback
		false,										// unused
		false,										// access key
		tablepress_editor_button.title,				// button title
		115											// button position (here: between code and more)
	);

} );
