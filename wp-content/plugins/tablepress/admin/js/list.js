/**
 * JavaScript code for the "List Tables" screen
 *
 * @package TablePress
 * @subpackage Views JavaScript
 * @author Tobias Bäthge
 * @since 1.0.0
 */

/* global prompt, confirm, tablepress_common, tablepress_list, tb_show, ajaxurl */

jQuery( document ).ready( function( $ ) {

	'use strict';

	/**
	 * Show a popup box with the table's Shortcode
	 *
	 * @since 1.0.0
	 */
	$( '.tablepress-all-tables' ).on( 'click', '.shortcode a', function( /* event */ ) {
		prompt( tablepress_list.shortcode_popup, $(this).attr( 'title' ) );
		return false;
	} );

	/**
	 * Load a Thickbox with a table preview
	 *
	 * @since 1.0.0
	 */
	$( '.tablepress-all-tables' ).on( 'click', '.table-preview a', function( /* event */ ) {
		var width = $( window ).width() - 120,
			height = $( window ).height() - 120,
			$this = $(this);
		if ( $( '#wpadminbar' ).length ) {
			height -= parseInt( $( '#wpadminbar' ).css( 'height' ), 10 );
		}
		tb_show( $this.text(), $this.attr( 'href' ) + 'TB_iframe=true&height=' + height + '&width=' + width, false );
		return false;
	} );

	/**
	 * Process links with a class "ajax-link" with AJAX
	 *
	 * @since 1.0.0
	 */
	$( '#tablepress-page' ).on( 'click', '.ajax-link', function( /* event */ ) {
		var $link = $(this),
			action = $link.data( 'action' ),
			item = $link.data( 'item' ),
			target = $link.data( 'target' );
		$.get(
			ajaxurl,
			this.href.split('?')['1'], /* query string of the link */
			function( result ) {
				if ( '1' !== result ) {
					return;
				}

				switch ( action ) {
					case 'hide_message':
						/* Donation message links show new message */
						if ( 'donation_nag' === item && '' !== target ) {
							$link.closest( 'div' ).after( '<div class="donation-message-after-click-message notice notice-success"><p><strong>' + tablepress_list['donation-message-' + target] + '</strong></p></div>' );
							$( '.donation-message-after-click-message' ).delay( 10000 ).fadeOut( 2000, function() { $(this).remove(); } );
						}

						/* Remove original message */
						$link.closest( 'div' ).remove();
						break;
				}
			}
		);
		return false;
	} );

	/**
	 * Submit Bulk Actions only if an action was selected an a table's checkbox was checked
	 *
	 * @since 1.0.0
	 */
	$( '#doaction, #doaction2' ).on( 'click', function() {
		var bulk_action,
			confirm_message,
			num_selected = $( '.tablepress-all-tables' ).find( 'tbody' ).find( 'input:checked' ).length;

		// determine location of clicked bulk action controls
		if ( 'doaction' === this.id ) {
			bulk_action = 'top';
		} else {
			bulk_action = 'bottom';
		}

		// check whether an action was selected, and whether tables were selected
		if ( '-1' === $( '#bulk-action-' + bulk_action ).val() ) {
			return false;
		}
		if ( 0 === num_selected ) {
			return false;
		}

		// Show AYS prompt for deletion
		if ( 'delete' === $( '#bulk-action-' + bulk_action ).val() ) {
			if ( 1 === num_selected ) {
				confirm_message = tablepress_common.ays_delete_single_table;
			} else {
				confirm_message = tablepress_common.ays_delete_multiple_tables;
			}

			if ( ! confirm( confirm_message ) ) {
				return false;
			}
		}
	} );

} );
