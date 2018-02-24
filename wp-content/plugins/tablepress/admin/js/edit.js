/**
 * JavaScript code for the "Edit" screen
 *
 * @package TablePress
 * @subpackage Views JavaScript
 * @author Tobias Bäthge
 * @since 1.0.0
 */

/* global alert, confirm, tp, tablepress_strings, tablepress_options, ajaxurl, wpLink, tb_show, wp, JSON */

// Ensure the global `tp` object exists.
window.tp = window.tp || {};

jQuery( document ).ready( function( $ ) {

	'use strict';

	/* Wrapper to find elements in the page faster with JS-native functions */
	var $id = function( element_id ) {
		return $( document.getElementById( element_id ) );
	};

	/**
	 * TablePress object, mostly with functionality for the "Edit" screen
	 *
	 * @since 1.0.0
	 */
	tp.made_changes = false;

	tp.table = {
		id: $id( 'table-id' ).val(),
		new_id: $id( 'table-new-id' ).val(),
		rows: parseInt( $id( 'number-rows' ).val(), 10 ),
		columns: parseInt( $id( 'number-columns' ).val(), 10 ),
		head: $id( 'option-table-head' ).prop( 'checked' ),
		foot: $id( 'option-table-foot' ).prop( 'checked' ),
		no_data_columns_pre: 2,
		no_data_columns_post: 1,
		body_cells_pre: '<tr><td><span class="move-handle"></span></td><td><input type="checkbox" /><input type="hidden" class="visibility" name="table[visibility][rows][]" value="1" /></td>',
		body_cells_post: '<td><span class="move-handle"></span></td></tr>',
		body_cell: '<td><textarea rows="1"></textarea></td>',
		head_cell: '<th class="head"><span class="sort-control sort-desc" title="' + tablepress_strings.sort_desc + '"><span class="sorting-indicator"></span></span><span class="sort-control sort-asc" title="' + tablepress_strings.sort_asc + '"><span class="sorting-indicator"></span></span><span class="move-handle"></span></th>',
		foot_cell: '<th><input type="checkbox" /><input type="hidden" class="visibility" name="table[visibility][columns][]" value="1" /></th>',
		set_table_changed: function() {
			tp.made_changes = true;
		},
		unset_table_changed: function() {
			tp.made_changes = false;
			$id( 'edit-form-body' ).one( 'change', 'textarea', tp.table.set_table_changed );
			// @TODO: maybe use .tablepress-postbox-table here and further below
			$( '#tablepress_edit-table-information, #tablepress_edit-table-options, #tablepress_edit-datatables-features' ).one( 'change', 'input, textarea, select', tp.table.set_table_changed );
		},
		change_id: function( /* event */ ) {
			// empty table IDs are not allowed
			if ( '' === $.trim( $id( 'table-new-id' ).val() ) ) {
				alert( tablepress_strings.table_id_not_empty );
				$id( 'table-new-id' ).val( tp.table.new_id ).focus().select();
				return;
			}
			// the '0' table ID is not allowed
			if ( '0' === $.trim( $id( 'table-new-id' ).val() ) ) {
				alert( tablepress_strings.table_id_not_zero );
				$id( 'table-new-id' ).val( tp.table.new_id ).focus().select();
				return;
			}

			if ( this.value === tp.table.new_id ) {
				return;
			}

			if ( confirm( tablepress_strings.ays_change_table_id ) ) {
				tp.table.new_id = this.value;
				$( '.table-shortcode' ).val( '[' + tablepress_options.shortcode + ' id=' + tp.table.new_id + ' /]' ).click(); // click() to focus and select
				tp.table.set_table_changed();
			} else {
				$(this).val( tp.table.new_id );
			}
		},
		change_table_head: function( /* event */ ) {
			tp.table.head = $(this).prop( 'checked' );
			$id( 'option-use-datatables' ).prop( 'disabled', ! tp.table.head ).change();
			$id( 'notice-datatables-head-row' ).toggle( ! tp.table.head );
			tp.rows.stripe();
		},
		change_table_foot: function( /* event */ ) {
			tp.table.foot = $(this).prop( 'checked' );
			tp.rows.stripe();
		},
		change_print_name_description: function( /* event */ ) {
			$id( this.id + '-position' ).prop( 'disabled', ! $(this).prop( 'checked' ) );
		},
		change_datatables: function() {
			var $datatables_checkbox = $id( 'option-use-datatables' ),
				checkboxes_disabled = ! ( $datatables_checkbox.prop( 'checked' ) && ! $datatables_checkbox.prop( 'disabled' ) );
			$datatables_checkbox.closest( 'tbody' ).find( 'input' ).not( $datatables_checkbox ).prop( 'disabled', checkboxes_disabled );
			tp.table.change_datatables_pagination();
		},
		change_datatables_pagination: function() {
			var $pagination_checkbox = $id( 'option-datatables-paginate' ),
				pagination_enabled = ( $pagination_checkbox.prop( 'checked' ) && ! $pagination_checkbox.prop( 'disabled' ) );
			$id( 'option-datatables-lengthchange' ).prop( 'disabled', ! pagination_enabled );
			$id( 'option-datatables-paginate_entries' ).prop( 'disabled', ! pagination_enabled );
		},
		prepare_ajax_request: function( wp_action, wp_nonce ) {
			var $table_body = $id( 'edit-form-body' ),
				table_data = [],
				table_options,
				table_number = { rows: tp.table.rows, columns: tp.table.columns, hidden_rows: 0, hidden_columns: 0 },
				table_visibility = { rows: [], columns: [] };

			$table_body.children().each( function( idx, row ) {
				table_data[ idx ] = $( row ).find( 'textarea' )
					.map( function() {
						return $(this).val();
					} )
					.get();
			} );
			table_data = JSON.stringify( table_data );

			// @TODO: maybe for options saving: https://stackoverflow.com/questions/1184624/convert-form-data-to-javascript-object-with-jquery
			// or each()-loop through all checkboxes/textfields/selects
			table_options = {
				// Table Options
				table_head: tp.table.head,
				table_foot: tp.table.foot,
				alternating_row_colors: $id( 'option-alternating-row-colors' ).prop( 'checked' ),
				row_hover: $id( 'option-row-hover' ).prop( 'checked' ),
				print_name: $id( 'option-print-name' ).prop( 'checked' ),
				print_description: $id( 'option-print-description' ).prop( 'checked' ),
				print_name_position: $id( 'option-print-name-position' ).val(),
				print_description_position: $id( 'option-print-description-position' ).val(),
				extra_css_classes: $id( 'option-extra-css-classes' ).val(),
				// DataTables JS features
				use_datatables: $id( 'option-use-datatables' ).prop( 'checked' ),
				datatables_sort: $id( 'option-datatables-sort' ).prop( 'checked' ),
				datatables_filter: $id( 'option-datatables-filter' ).prop( 'checked' ),
				datatables_paginate: $id( 'option-datatables-paginate' ).prop( 'checked' ),
				datatables_lengthchange: $id( 'option-datatables-lengthchange' ).prop( 'checked' ),
				datatables_paginate_entries: $id( 'option-datatables-paginate_entries' ).val(),
				datatables_info: $id( 'option-datatables-info' ).prop( 'checked' ),
				datatables_scrollx: $id( 'option-datatables-scrollx' ).prop( 'checked' ),
				datatables_custom_commands: $id( 'option-datatables-custom-commands' ).val()
			};
			table_options = JSON.stringify( table_options );

			table_visibility.rows = $table_body.find( 'input[type="hidden"]' )
				.map( function() {
					if ( '1' === $(this).val() ) {
						return 1;
					}
					table_number.hidden_rows += 1;
					return 0;
				} )
				.get();
			table_visibility.columns = $id( 'edit-form-foot' ).find( 'input[type="hidden"]' )
				.map( function() {
					if ( '1' === $(this).val() ) {
						return 1;
					}
					table_number.hidden_columns += 1;
					return 0;
				} )
				.get();
			table_visibility = JSON.stringify( table_visibility );

			// request_data =
			return {
				action: wp_action,
				_ajax_nonce : $( wp_nonce ).val(),
				tablepress: {
					id: tp.table.id,
					new_id: tp.table.new_id,
					name: $id( 'table-name' ).val(),
					description: $id( 'table-description' ).val(),
					number: table_number,
					data: table_data,
					options: table_options,
					visibility: table_visibility
				}
			};
		},
		preview: {
			trigger: function( /* event */ ) {
				if ( ! tp.made_changes ) {
					tp.table.preview.show( $(this).attr( 'href' ) + '&TB_iframe=true' );
					return false;
				}

				// validation checks
				if ( $id( 'option-datatables-paginate' ).prop( 'checked' ) && ! ( /^[1-9][0-9]{0,4}$/ ).test( $id( 'option-datatables-paginate_entries' ).val() ) ) {
					alert( tablepress_strings.num_pagination_entries_invalid );
					$id( 'option-datatables-paginate_entries' ).focus().select();
					return;
				}
				if ( ( /[^A-Za-z0-9- _]/ ).test( $id( 'option-extra-css-classes' ).val() ) ) {
					alert( tablepress_strings.extra_css_classes_invalid );
					$id( 'option-extra-css-classes' ).focus().select();
					return;
				}

				$(this).closest( 'p' ).append( '<span class="animation-preview spinner is-active" title="' + tablepress_strings.preparing_preview + '"/>' );
				$( 'body' ).addClass( 'wait' );
				$id( 'table-preview' ).empty(); // clear preview

				$.ajax({
					'type': 'POST',
					'url': ajaxurl,
					'data': tp.table.prepare_ajax_request( 'tablepress_preview_table', '#nonce-preview-table' ),
					'success': tp.table.preview.ajax_success,
					'error': tp.table.preview.ajax_error,
					'dataType': 'json'
				} );

				return false;
			},
			ajax_success: function( data, status /*, jqXHR */ ) {
				if ( ( 'undefined' === typeof status ) || ( 'success' !== status ) ) {
					tp.table.preview.error( 'AJAX call successful, but unclear status.' );
				} else if ( ( 'undefined' === typeof data ) || ( null === data ) || ( '-1' === data ) || ( 'undefined' === typeof data.success ) || ( true !== data.success ) ) {
					tp.table.preview.error( 'AJAX call successful, but unclear data.' );
				} else {
					tp.table.preview.success( data );
				}
			},
			ajax_error: function( jqXHR, status, error_thrown ) {
				tp.table.preview.error( 'AJAX call failed: ' + status + ' - ' + error_thrown );
			},
			success: function( data ) {
				$id( 'table-preview' ).empty();
				$( '<iframe id="table-preview-iframe" />' ).load( function() {
					var $iframe = $(this).contents();
					$iframe.find( 'head' ).append( data.head_html );
					$iframe.find( 'body' ).append( data.body_html );
				} ).appendTo( '#table-preview' );
				$( '.animation-preview' ).remove();
				$( 'body' ).removeClass( 'wait' );
				tp.table.preview.show( '#TB_inline?inlineId=preview-container' );
			},
			error: function( message ) {
				$( '.animation-preview' ).closest( 'p' )
					.after( '<div class="ajax-alert preview-error error"><p>' + tablepress_strings.preview_error + ': ' + message + '</p></div>' );
				$( '.animation-preview' ).remove();
				$( '.preview-error' ).delay( 6000 ).fadeOut( 2000, function() { $(this).remove(); } );
				$( 'body' ).removeClass( 'wait' );
			},
			show: function( url ) {
				var width = $( window ).width() - 120,
					height = $( window ).height() - 120;
				if ( $( '#wpadminbar' ).length ) {
					height -= parseInt( $( '#wpadminbar' ).css( 'height' ), 10 );
				}
				tb_show( $( '.show-preview-button' ).first().text(), url + '&height=' + height + '&width=' + width, false );
			}
		}
	};

	tp.rows = {
		create: function( num_rows ) {
			var i, j,
				column_idxs,
				new_rows = '';

			for ( i = 0; i < num_rows; i++ ) {
				new_rows += tp.table.body_cells_pre;
				for ( j = 0; j < tp.table.columns; j++ ) {
					new_rows += tp.table.body_cell;
				}
				new_rows += tp.table.body_cells_post;
			}

			column_idxs = $id( 'edit-form-foot' ).find( '.column-hidden' )
				.map( function() { return $(this).index(); } ).get();
			return $( new_rows ).each( function( row_idx, row ) {
				$( row ).children()
					.filter( function( idx ) { return ( -1 !== $.inArray( idx, column_idxs ) ); } )
					.addClass( 'column-hidden' );
			} );
		},
		append: function( /* event */ ) {
			var num_rows = $id( 'rows-append-number' ).val();

			if ( ! ( /^[1-9][0-9]{0,4}$/ ).test( num_rows ) ) {
				alert( tablepress_strings.append_num_rows_invalid );
				$id( 'rows-append-number' ).focus().select();
				return;
			}

			$id( 'edit-form-body' ).append( tp.rows.create( num_rows ) );

			tp.rows.stripe();
			tp.reindex();
		},
		insert: function( event ) {
			var $selected_rows = $id( 'edit-form-body' ).find( 'input:checked' )
				.prop( 'checked', event.shiftKey ).closest( 'tr' );

			if ( 0 === $selected_rows.length ) {
				alert( tablepress_strings.no_rows_selected );
				return;
			}

			$selected_rows.before( tp.rows.create( 1 ) );

			tp.rows.stripe();
			tp.reindex();
		},
		duplicate: function( event ) {
			var $selected_rows = $id( 'edit-form-body' ).find( 'input:checked' )
				.prop( 'checked', event.shiftKey ).closest( 'tr' );

			if ( 0 === $selected_rows.length ) {
				alert( tablepress_strings.no_rows_selected );
				return;
			}

			$selected_rows.each( function( idx, row ) {
				var $row = $( row ),
					$textareas = $row.find( 'textarea' ),
					$duplicated_row = $row.clone();
				$duplicated_row.find( 'textarea' ).removeAttr( 'id' ).each( function( idx, cell ) {
					$( cell ).val( $textareas.eq( idx ).val() ); // setting val() is necessary, as clone() doesn't copy the current value, see jQuery bugs 5524, 2285, 3016
				} );
				$row.after( $duplicated_row );
			} );

			tp.rows.stripe();
			tp.reindex();
		},
		hide: function( event ) {
			var $selected_rows = $id( 'edit-form-body' ).find( 'input:checked' )
				.prop( 'checked', event.shiftKey ).closest( 'tr' );

			if ( 0 === $selected_rows.length ) {
				alert( tablepress_strings.no_rows_selected );
				return;
			}

			$selected_rows.addClass( 'row-hidden' ).find( '.visibility' ).val( '0' );

			tp.rows.stripe();
			tp.table.set_table_changed();
		},
		unhide: function( event ) {
			var $selected_rows = $id( 'edit-form-body' ).find( 'input:checked' )
				.prop( 'checked', event.shiftKey ).closest( 'tr' );

			if ( 0 === $selected_rows.length ) {
				alert( tablepress_strings.no_rows_selected );
				return;
			}

			$selected_rows.removeClass( 'row-hidden' ).find( '.visibility' ).val( '1' );

			tp.rows.stripe();
			tp.table.set_table_changed();
		},
		remove: function( /* event */ ) {
			var confirm_message,
				$selected_rows = $id( 'edit-form-body' ).find( 'input:checked' ).closest( 'tr' );

			if ( 0 === $selected_rows.length ) {
				alert( tablepress_strings.no_rows_selected );
				return;
			}

			if ( tp.table.rows === $selected_rows.length ) {
				alert( tablepress_strings.no_remove_all_rows );
				return;
			}

			if ( 1 === $selected_rows.length ) {
				confirm_message = tablepress_strings.ays_remove_rows_singular;
			} else {
				confirm_message = tablepress_strings.ays_remove_rows_plural;
			}
			if ( ! confirm( confirm_message ) ) {
				return;
			}

			$selected_rows.remove();

			tp.rows.stripe();
			tp.reindex();
		},
		move: {
			start: function( event, ui ) {
				$( ui.placeholder ).removeClass( 'row-hidden' ).css( 'visibility', 'visible' )
					.html( '<td colspan="' + ( tp.table.columns + tp.table.no_data_columns_pre + tp.table.no_data_columns_post ) + '"><div/></td>' );
				$( ui.helper ).removeClass( 'odd head-row foot-row' );
			},
			change: function( event, ui ) {
				tp.rows.stripe( ui.helper );
			},
			stop: function( /* event, ui */ ) {
				tp.rows.stripe();
			}
		},
		sort: function() {
			var column_idx = $(this).parent().index(),
				direction = ( $(this).hasClass( 'sort-asc' ) ) ? 1 : -1,
				$table_body = $('#edit-form-body'),
				$head_rows = $table_body.find( '.head-row' ).prevAll().addBack(),
				$foot_rows = $table_body.find( '.foot-row' ).nextAll().addBack(),
				rows = $table_body.children().not( $head_rows ).not( $foot_rows ).get(),
				/*
				 * Natural Sort algorithm for Javascript - Version 0.8.1 - Released under MIT license
				 * Author: Jim Palmer (based on chunking idea from Dave Koelle)
				 * See: https://github.com/overset/javascript-natural-sort and http://www.overset.com/2008/09/01/javascript-natural-sort-algorithm-with-unicode-support/
				 */
				natural_sort = function( a, b ) {
					var re = /(^([+\-]?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?(?=\D|\s|$))|^0x[\da-fA-F]+$|\d+)/g,
						sre = /^\s+|\s+$/g,   // trim pre-post whitespace
						snre = /\s+/g,        // normalize all whitespace to single ' ' character
						dre = /(^([\w ]+,?[\w ]+)?[\w ]+,?[\w ]+\d+:\d+(:\d+)?[\w ]?|^\d{1,4}[\/\-]\d{1,4}[\/\-]\d{1,4}|^\w+, \w+ \d+, \d{4})/,
						hre = /^0x[0-9a-f]+$/i,
						ore = /^0/,
						// strip whitespace
						x = a.replace(sre, '') || '',
						y = b.replace(sre, '') || '',
						// chunk/tokenize
						xN = x.replace(re, '\0$1\0').replace(/\0$/,'').replace(/^\0/,'').split('\0'),
						yN = y.replace(re, '\0$1\0').replace(/\0$/,'').replace(/^\0/,'').split('\0'),
						// numeric, hex or date detection
						xD = parseInt(x.match(hre), 16) || (xN.length !== 1 && Date.parse(x)),
						yD = parseInt(y.match(hre), 16) || xD && y.match(dre) && Date.parse(y) || null,
						normChunk = function(s, l) {
							// normalize spaces; find floats not starting with '0', string or 0 if not defined (Clint Priest)
							return (!s.match(ore) || l === 1) && parseFloat(s) || s.replace(snre, ' ').replace(sre, '') || 0;
						},
						oFxNcL, oFyNcL;
					// first try and sort Hex codes or Dates
					if (yD) {
						if (xD < yD) { return -1; }
						else if (xD > yD) { return 1; }
					}
					// natural sorting through split numeric strings and default strings
					for(var cLoc = 0, xNl = xN.length, yNl = yN.length, numS = Math.max(xNl, yNl); cLoc < numS; cLoc++) {
						oFxNcL = normChunk(xN[cLoc] || '', xNl);
						oFyNcL = normChunk(yN[cLoc] || '', yNl);
						// handle numeric vs string comparison - number < string - (Kyle Adams)
						if (isNaN(oFxNcL) !== isNaN(oFyNcL)) {
							return isNaN(oFxNcL) ? 1 : -1;
						}
						// if unicode use locale comparison
						if (/[^\x00-\x80]/.test(oFxNcL + oFyNcL) && oFxNcL.localeCompare) {
							var comp = oFxNcL.localeCompare(oFyNcL);
							return comp / Math.abs(comp);
						}
						if (oFxNcL < oFyNcL) { return -1; }
						else if (oFxNcL > oFyNcL) { return 1; }
					}
				};

			$.each( rows, function( row_idx, row ) {
				row.sort_key = ( '' + $( row ).children().eq( column_idx ).find( 'textarea' ).val() ).toLowerCase(); // convert to string, and lower case for case insensitive sorting
			} );

			rows.sort( function( a, b ) {
				return direction * natural_sort( a.sort_key, b.sort_key );
			} );

			// might not be necessary:
			$.each( rows, function( row_idx, row ) {
				row.sort_key = null;
			} );

			$table_body.append( $head_rows );
			$table_body.append( rows );
			$table_body.append( $foot_rows );

			tp.rows.stripe();
			tp.reindex();
		},
		stripe: function( helper ) {
			if ( 'undefined' === typeof helper ) {
				helper = null;
			}
			helper = $( helper );
			var $rows = $id( 'edit-form-body' ).children().removeClass( 'odd head-row foot-row' ).not( helper );
			$rows.filter( ':even' ).addClass( 'odd' );
			$rows = $rows.not( '.row-hidden' );
			if ( helper.hasClass( 'row-hidden' ) ) {
				$rows = $rows.not( '.ui-sortable-placeholder' );
			}
			if ( tp.table.head ) {
				$rows.first().addClass( 'head-row' );
			}
			if ( tp.table.foot ) {
				$rows.last().addClass( 'foot-row' );
			}
		}
	};

	tp.columns = {
		append: function( /* event */ ) {
			var i,
				num_columns = $id( 'columns-append-number' ).val(),
				new_head_cells = '', new_body_cells = '', new_foot_cells = '';

			if ( ! ( /^[1-9][0-9]{0,4}$/ ).test( num_columns ) ) {
				alert( tablepress_strings.append_num_columns_invalid );
				$id( 'columns-append-number' ).focus().select();
				return;
			}

			for ( i = 0; i < num_columns; i++ ) {
				new_body_cells += tp.table.body_cell;
				new_head_cells += tp.table.head_cell;
				new_foot_cells += tp.table.foot_cell;
			}

			$id( 'edit-form-body' ).children().each( function( row_idx, row ) {
				$( row ).children().slice( - tp.table.no_data_columns_post )
					.before( new_body_cells );
			} );
			$id( 'edit-form-head' ).children().slice( - tp.table.no_data_columns_post )
				.before( new_head_cells );
			$id( 'edit-form-foot' ).children().slice( - tp.table.no_data_columns_post )
				.before( new_foot_cells );

			tp.reindex();
		},
		insert: function( event ) {
			var column_idxs,
				$selected_columns = $id( 'edit-form-foot' ).find( 'input:checked' )
					.prop( 'checked', event.shiftKey ).closest( 'th' );

			if ( 0 === $selected_columns.length ) {
				alert( tablepress_strings.no_columns_selected );
				return;
			}

			column_idxs = $selected_columns.map( function() { return $(this).index(); } ).get();
			$id( 'edit-form-body' ).children().each( function( row_idx, row ) {
				$( row ).children()
					.filter( function( idx ) { return ( -1 !== $.inArray( idx, column_idxs ) ); } )
					.before( tp.table.body_cell );
			} );
			$id( 'edit-form-head' ).children()
				.filter( function( idx ) { return ( -1 !== $.inArray( idx, column_idxs ) ); } )
				.before( tp.table.head_cell );
			$selected_columns.before( tp.table.foot_cell );

			tp.reindex();
		},
		duplicate: function( event ) {
			var column_idxs,
				$selected_columns = $id( 'edit-form-foot' ).find( 'input:checked' )
					.prop( 'checked', event.shiftKey ).closest( 'th' );

			if ( 0 === $selected_columns.length ) {
				alert( tablepress_strings.no_columns_selected );
				return;
			}

			column_idxs = $selected_columns.map( function() { return $(this).index(); } ).get();
			$id( 'edit-form' ).find( 'tr' ).each( function( row_idx, row ) {
				$( row ).children().each( function( idx, cell ) {
					if ( -1 !== $.inArray( idx, column_idxs ) ) {
						var $cell = $( cell ),
							$duplicated_cell = $cell.clone();
							$duplicated_cell.find( 'textarea' ).removeAttr( 'id' ).val( $cell.find( 'textarea' ).val() ); // setting val() is necessary, as clone() doesn't copy the current value, see jQuery bugs 5524, 2285, 3016
						$cell.after( $duplicated_cell );
					}
				} );
			} );

			tp.reindex();
		},
		hide: function( event ) {
			var column_idxs,
				$selected_columns = $id( 'edit-form-foot' ).find( 'input:checked' )
					.prop( 'checked', event.shiftKey ).closest( 'th' );

			if ( 0 === $selected_columns.length ) {
				alert( tablepress_strings.no_columns_selected );
				return;
			}

			column_idxs = $selected_columns.map( function() { return $(this).index(); } ).get();
			$id( 'edit-form-body' ).children().add( '#edit-form-head' ).each( function( row_idx, row ) {
				$( row ).children()
					.filter( function( idx ) { return ( -1 !== $.inArray( idx, column_idxs ) ); } )
					.addClass( 'column-hidden' );
			} );
			$selected_columns.addClass( 'column-hidden' ).find( '.visibility' ).val( '0' );

			tp.table.set_table_changed();
		},
		unhide: function( event ) {
			var column_idxs,
				$selected_columns = $id( 'edit-form-foot' ).find( 'input:checked' )
					.prop( 'checked', event.shiftKey ).closest( 'th' );

			if ( 0 === $selected_columns.length ) {
				alert( tablepress_strings.no_columns_selected );
				return;
			}

			column_idxs = $selected_columns.map( function() { return $(this).index(); } ).get();
			$id( 'edit-form-body' ).children().add( '#edit-form-head' ).each( function( row_idx, row ) {
				$( row ).children()
					.filter( function( idx ) { return ( -1 !== $.inArray( idx, column_idxs ) ); } )
					.removeClass( 'column-hidden' );
			} );
			$selected_columns.removeClass( 'column-hidden' ).find( '.visibility' ).val( '1' );

			tp.table.set_table_changed();
		},
		remove: function( /* event */ ) {
			var column_idxs,
				confirm_message,
				$selected_columns = $id( 'edit-form-foot' ).find( 'input:checked' ).closest( 'th' );

			if ( 0 === $selected_columns.length ) {
				alert( tablepress_strings.no_columns_selected );
				return;
			}

			if ( tp.table.columns === $selected_columns.length ) {
				alert( tablepress_strings.no_remove_all_columns );
				return;
			}

			if ( 1 === $selected_columns.length ) {
				confirm_message = tablepress_strings.ays_remove_columns_singular;
			} else {
				confirm_message = tablepress_strings.ays_remove_columns_plural;
			}
			if ( ! confirm( confirm_message ) ) {
				return;
			}

			column_idxs = $selected_columns.map( function() { return $(this).index(); } ).get();
			$id( 'edit-form-body' ).children().add( '#edit-form-head' ).each( function( row_idx, row ) {
				$( row ).children()
					.filter( function( idx ) { return ( -1 !== $.inArray( idx, column_idxs ) ); } )
					.remove();
			} );
			$selected_columns.remove();

			tp.reindex();
		},
		move: {
			source_idx: -1,
			target_idx: -1,
			$rows: null,
			$row_children: null,
			$cell: null,
			$cells: null,
			$placeholder: null,
			$helper: null,
			start: function( event, ui ) {
				var $item = $( ui.item ),
					column_width;

				tp.columns.move.source_idx = $item.index();

				tp.columns.move.$rows = $id( 'edit-form-body' ).children().add( '#edit-form-foot' );

				tp.columns.move.$cells = tp.columns.move.$rows
					.find( ':nth-child(' + ( tp.columns.move.source_idx + 1 ) + ')' )
					.each( function() {
						tp.columns.move.$cell = $(this);
						$( '<td class="move-placeholder"><div/></td>' ).insertBefore( tp.columns.move.$cell );
						tp.columns.move.$cell.insertAfter( tp.columns.move.$cell.nextAll().last() )
							.clone().addClass( 'move-hover' ).insertAfter( tp.columns.move.$cell )
							.find( 'textarea' ).val( tp.columns.move.$cell.find( 'textarea' ).val() );
							// last line works around problem with clone() of textareas, see jQuery bugs 5524, 2285, 3016
					} )
					.hide();

				tp.columns.move.$helper = tp.columns.move.$rows.find( '.move-hover' );
				/* // seems not to be working for rows, so disable it for columns
					.each( function() {
						tp.columns.move.$cell = $(this);
						tp.columns.move.$cell.css( 'top', ( tp.columns.move.$cell.position().top - 3 ) + 'px' );
					} );
				*/

				column_width = tp.columns.move.$helper.eq(1).width(); // eq(0) is table foot
				tp.columns.move.$helper.eq(0).width( column_width );
				tp.columns.move.$placeholder = tp.columns.move.$rows.find( '.move-placeholder' );
				tp.columns.move.$placeholder.find( 'div' ).width( column_width );
			},
			change: function( event, ui ) {
				tp.columns.move.target_idx = $( ui.placeholder ).index();

				if ( ( tp.columns.move.target_idx - tp.columns.move.source_idx ) === 1 ) {
					tp.columns.move.target_idx += 1;
				} else {
					if ( tp.columns.move.target_idx === tp.columns.move.source_idx ) {
						tp.columns.move.target_idx -= 1;
					}
				}

				tp.columns.move.$placeholder.each( function() {
					tp.columns.move.$cell = $(this);
					tp.columns.move.$cell.insertBefore( tp.columns.move.$cell.parent().children().eq( tp.columns.move.target_idx ) );
				} );

				if ( tp.columns.move.target_idx > tp.columns.move.source_idx ) {
					tp.columns.move.target_idx -= 1;
				}

				tp.columns.move.source_idx = tp.columns.move.target_idx;
			},
			sort: function( event, ui ) {
				tp.columns.move.$helper.css( 'left', ui.position.left );
			},
			stop: function( /* event, ui */ ) {
				tp.columns.move.$helper.remove();
				tp.columns.move.$cells
					.each( function() {
						tp.columns.move.$cell = $(this);
						tp.columns.move.$cell.insertBefore( tp.columns.move.$cell.parent().find( '.move-placeholder' ) );
					} )
					.show();
				tp.columns.move.$placeholder.remove();

				tp.columns.move.source_idx = tp.columns.move.target_idx = -1;
				tp.columns.move.$rows = tp.columns.move.$row_children = tp.columns.move.$cell = tp.columns.move.$cells = tp.columns.move.$placeholder = tp.columns.move.$helper = null;

				tp.reindex();
			}
		},
		number_to_letter: function( number ) {
			var column = '';
			while ( number > 0 ) {
				column = String.fromCharCode( 65 + ( ( number-1) % 26 ) ) + column;
				number = Math.floor( (number-1) / 26 );
			}
			return column;
		}/*,
		letter_to_number: function( column ) {
			column = column.toUpperCase();
			var count = column.length,
				number = 0,
				i;
			for ( i = 0; i < count; i++ ) {
				number += ( column.charCodeAt( count-1-i ) - 64 ) * Math.pow( 26, i );
			}
			return number;
		}*/
	};

	tp.cells = {
		$focus: $( null ),
		$textarea: null,
		autogrow: function( /* event */ ) {
			tp.cells.$focus.removeClass( 'focus' );
			tp.cells.$focus = $(this).closest( 'tr' ).addClass( 'focus' );
		},
		advanced_editor: {
			prompt_shown: false,
			keyopen: function( event ) {
				if ( ! event.shiftKey ) {
					return;
				}

				var $advanced_editor = $id( 'advanced-editor-content' );
				tp.cells.$textarea = $(this).blur();
				$advanced_editor.val( tp.cells.$textarea.val() );
				$id( 'advanced-editor' ).wpdialog( 'open' );
				$advanced_editor.get(0).selectionStart = $advanced_editor.get(0).selectionEnd = $advanced_editor.val().length;
				$advanced_editor.focus();
			},
			buttonopen: function() {
				if ( ! tp.cells.advanced_editor.prompt_shown ) {
					if ( ! confirm( tablepress_strings.advanced_editor_open ) ) {
						return;
					}
				}

				tp.cells.advanced_editor.prompt_shown = true;
				$id( 'edit-form-body' ).one( 'click', 'textarea', function() {
					var $advanced_editor = $id( 'advanced-editor-content' );
					tp.cells.$textarea = $(this).blur();
					$advanced_editor.val( tp.cells.$textarea.val() );
					$id( 'advanced-editor' ).wpdialog( 'open' );
					$advanced_editor.get(0).selectionStart = $advanced_editor.get(0).selectionEnd = $advanced_editor.val().length;
					$advanced_editor.focus();
				} );
			},
			save: function() {
				var $ve_content = $id( 'advanced-editor-content' ).blur().val();
				if ( tp.cells.$textarea.val() !== $ve_content ) {
					tp.cells.$textarea.val( $ve_content );
					// position cursor at the end
					tp.cells.$textarea.get(0).selectionStart = tp.cells.$textarea.get(0).selectionEnd = tp.cells.$textarea.val().length;
					tp.table.set_table_changed();
				}
				tp.cells.$textarea.focus();
				tp.cells.advanced_editor.close();
			},
			close: function() {
				$id( 'advanced-editor' ).wpdialog( 'close' );
				return false;
			}
		},
		checkboxes: {
			last_clicked: { '#edit-form-body' : false, '#edit-form-foot' : false },
			multi_select: function ( event ) {
				if ( 'undefined' === event.shiftKey ) {
					return true;
				}

				if ( event.shiftKey ) {
					if ( ! tp.cells.checkboxes.last_clicked[ event.data.parent ] ) {
						return true;
					}

					var $checkboxes = $( event.data.parent ).find( ':checkbox' ),
						first_cb = $checkboxes.index( tp.cells.checkboxes.last_clicked[ event.data.parent ] ),
						last_cb = $checkboxes.index( this );
					if ( first_cb !== last_cb ) {
						$checkboxes.slice( Math.min( first_cb, last_cb ), Math.max( first_cb, last_cb ) ).prop( 'checked', $(this).prop( 'checked' ) );
					}
				}
				tp.cells.checkboxes.last_clicked[ event.data.parent ] = this;
				return true;
			}
		}
	};

	tp.content = {
		link: {
			prompt_shown: false,
			add: function( /* event */ ) {
				if ( ! tp.content.link.prompt_shown ) {
					if ( ! confirm( tablepress_strings.link_add ) ) {
						return;
					}
				}

				tp.content.link.prompt_shown = true;
				// mousedown instead of click to allow selection of text
				// mousedown will set the desired target textarea, and mouseup anywhere will show the link box
				// other approaches can lead to the wrong textarea being selected
				$id( 'edit-form-body' ).one( 'mousedown', 'textarea', function() {
					var editor_id = this.id;
					$( document ).one( 'mouseup', function() {
						if ( typeof wpLink !== 'undefined' ) {
							wpLink.open( editor_id );
							tp.table.set_table_changed();
						}
					} );
				} );
			}
		},
		image: {
			prompt_shown: false,
			add: function( /* event */ ) {
				if ( ! tp.content.image.prompt_shown ) {
					if ( ! confirm( tablepress_strings.image_add ) ) {
						return;
					}
				}

				tp.content.image.prompt_shown = true;
				$id( 'edit-form-body' ).one( 'click', 'textarea', function() {
					var editor = this.id,
						options = {
							frame: 'post',
							state: 'insert',
							title: wp.media.view.l10n.addMedia,
							multiple: true
						};

					// Move caret to the end, to prevent inserting right between existing text, as that's ugly in small cells (though possible in the Advanced Editor and Insert Link dialog).
					this.selectionStart = this.selectionEnd = this.value.length;

					// Remove focus from the textarea to prevent Opera from showing the outline of the textarea above the modal.
					// See: WP Core #22445
					$(this).blur();

					wp.media.editor.open( editor, options );
					tp.table.set_table_changed();
				} );
			}
		},
		span: {
			prompt_shown: false,
			add: function( span ) {
				var span_add_msg = ( '#rowspan#' === span ) ? tablepress_strings.rowspan_add : tablepress_strings.colspan_add;

				// init object, due to string keys
				if ( false === tp.content.span.prompt_shown ) {
					tp.content.span.prompt_shown = {};
					tp.content.span.prompt_shown['#rowspan#'] = tp.content.span.prompt_shown['#colspan#'] = false;
				}

				// Automatically deactivate DataTables, if cells are combined
				if ( $id( 'option-use-datatables' ).prop( 'checked' ) ) {
					if ( confirm( tablepress_strings.span_add_datatables_warning ) ) {
						$id( 'option-use-datatables' ).prop( 'checked', false ).change();
					} else {
						return;
					}
				}

				if ( ! tp.content.span.prompt_shown[ span ] ) {
					if ( ! confirm( span_add_msg ) ) {
						return;
					}
				}

				tp.content.span.prompt_shown[ span ] = true;
				$id( 'edit-form-body' ).one( 'click', 'textarea', function() {
					var $textarea = $(this),
						col_idx = $textarea.parent().index(),
						row_idx = $textarea.closest( 'tr' ).index();
					if ( '#rowspan#' === span ) {
						if ( 0 === row_idx ) {
							alert( tablepress_strings.no_rowspan_first_row );
							return;
						}
						if ( tp.table.head && 1 === row_idx ) {
							alert( tablepress_strings.no_rowspan_table_head );
							return;
						}
						if ( tp.table.foot && ( tp.table.rows - 1 ) === row_idx ) {
							alert( tablepress_strings.no_rowspan_table_foot );
							return;
						}
					} else if ( ( '#colspan#' === span ) && ( tp.table.no_data_columns_pre === col_idx ) ) {
						alert( tablepress_strings.no_colspan_first_col );
						return;
					}
					$textarea.val( span );
					tp.table.set_table_changed();
				} );
			}
		}
	};

	tp.check = {
		table_id: function( event ) {
			if ( ( 37 === event.which ) || ( 39 === event.which ) ) {
				return;
			}
			var $input = $(this);
			$input.val( $input.val().replace( /[^0-9a-zA-Z-_]/g, '' ) );
		},
		changes_saved: function() {
			if ( tp.made_changes ) {
				return tablepress_strings.unsaved_changes_unload;
			}
		}
	};

	tp.reindex = function() {
		var $row,
			$rows = $id( 'edit-form-body' ).children(),
			$cell, known_references = {};

		tp.table.rows = $rows.length;
		if ( tp.table.rows > 0 ) {
			tp.table.columns = $rows.first().children().length - tp.table.no_data_columns_pre - tp.table.no_data_columns_post;
		} else {
			tp.table.columns = 0;
		}

		$rows
		.each( function( row_idx, row ) {
			$row = $( row );
			$row.find( 'textarea' )
				.val( function( column_idx, value ) {
					// If the cell is not a formula, there's nothing to do here
					if ( ( '' === value ) || ( '=' !== value.charAt(0) ) ) {
						return value;
					}

					return value.replace( /([A-Z]+[0-9]+)(?::([A-Z]+[0-9]+))?/g, function( full_match, first_cell, second_cell ) {
						// first_cell must always exist, while second_cell only exists in ranges like A4:B7
						// we will use full_match as our result variable, so that we don't need an extra one

						if ( ! known_references.hasOwnProperty( first_cell ) ) {
							$cell = $id( 'cell-' + first_cell );
							if ( $cell.length ) {
								known_references[ first_cell ] = tp.columns.number_to_letter( $cell.parent().index() - tp.table.no_data_columns_pre + 1 ) + ( $cell.closest( 'tr' ).index() + 1 );
							} else {
								known_references[ first_cell ] = first_cell;
							}
						}
						full_match = known_references[ first_cell ];

						if ( ( 'undefined' !== typeof second_cell ) && ( '' !== second_cell ) ) { // Chrome and IE pass an undefined variable, while Firefox passes an empty string
							if ( ! known_references.hasOwnProperty( second_cell ) ) {
								$cell = $id( 'cell-' + second_cell );
								if ( $cell.length ) {
									known_references[ second_cell ] = tp.columns.number_to_letter( $cell.parent().index() - tp.table.no_data_columns_pre + 1 ) + ( $cell.closest( 'tr' ).index() + 1 );
								} else {
									known_references[ second_cell ] = second_cell;
								}
							}
							full_match += ':' + known_references[ second_cell ];
						}

						return full_match;
					} );
				} )
				.attr( 'name', function( column_idx /*, old_name */ ) {
					return 'table[data][' + row_idx + '][' + column_idx + ']';
				} );

			$row.find( '.move-handle' ).html( row_idx + 1 );
		} )
		.each( function( row_idx, row ) { // need a second loop here to not break logic in previous loop, that queries textareas by their old ID
			$( row ).find( 'textarea' ).attr( 'id', function( column_idx /*, old_id */ ) {
				return 'cell-' + tp.columns.number_to_letter( column_idx + 1 ) + ( row_idx + 1 );
			} );
		});
		$id( 'edit-form-head' ).find( '.move-handle' )
			.html( function( idx ) { return tp.columns.number_to_letter( idx + 1 ); } );

		$id( 'number-rows' ).val( tp.table.rows );
		$id( 'number-columns' ).val( tp.table.columns );

		tp.table.set_table_changed();
	};

	tp.save_changes = {
		trigger: function( event ) {
			// validation checks
			if ( $id( 'option-datatables-paginate' ).prop( 'checked' ) && ! ( /^[1-9][0-9]{0,4}$/ ).test( $id( 'option-datatables-paginate_entries' ).val() ) ) {
				alert( tablepress_strings.num_pagination_entries_invalid );
				$id( 'option-datatables-paginate_entries' ).focus().select();
				return;
			}
			if ( ( /[^A-Za-z0-9- _]/ ).test( $id( 'option-extra-css-classes' ).val() ) ) {
				alert( tablepress_strings.extra_css_classes_invalid );
				$id( 'option-extra-css-classes' ).focus().select();
				return;
			}

			if ( event.shiftKey ) {
				tp.made_changes = false; // to prevent onunload warning
				$id( 'tablepress-page' ).find( 'form' ).submit();
				return;
			}

			$(this).closest( 'p' ).append( '<span class="animation-saving spinner is-active" title="' + tablepress_strings.saving_changes + '"/>' );
			$( '.save-changes-button' ).prop( 'disabled', true );
			$( 'body' ).addClass( 'wait' );

			$.ajax({
				'type': 'POST',
				'url': ajaxurl,
				'data': tp.table.prepare_ajax_request( 'tablepress_save_table', '#nonce-edit-table' ),
				'success': tp.save_changes.ajax_success,
				'error': tp.save_changes.ajax_error,
				'dataType': 'json'
			} );
		},
		ajax_success: function( data, status /*, jqXHR */ ) {
			if ( ( 'undefined' === typeof status ) || ( 'success' !== status ) ) {
				tp.save_changes.error( 'AJAX call successful, but unclear status. Try again while holding down the &#8220;Shift&#8221; key.' );
			} else if ( ( 'undefined' === typeof data ) || ( null === data ) || ( '-1' === data ) || ( 'undefined' === typeof data.success ) ) {
				tp.save_changes.error( 'AJAX call successful, but unclear data. Try again while holding down the &#8220;Shift&#8221; key.' );
			} else if ( true !== data.success ) {
				var debug_html = '';
				// Print debug information, if we are in debug mode
				if ( ( 'undefined' !== typeof data.error_details ) && ( tablepress_options.print_debug_output ) ) {
					debug_html = '</p><p>These errors were encountered:</p><pre>' + data.error_details + '</pre><p>'; // Some HTML magic because this is wrapped in <p> when printed
				}
				tp.save_changes.error( 'AJAX call successful, internal saving process failed. Try again while holding down the &#8220;Shift&#8221; key.' + debug_html );
			} else {
				tp.save_changes.success( data );
			}
		},
		ajax_error: function( jqXHR, status, error_thrown ) {
			tp.save_changes.error( 'AJAX call failed: ' + status + ' - ' + error_thrown + '. Try again while holding down the &#8220;Shift&#8221; key.' );
		},
		success: function( data ) {
			// saving was successful, so the original ID has changed to the (maybe) new ID -> we need to adjust all occurrences
			if ( tp.table.id !== data.table_id ) {
				// update URL (for HTML5 browsers only), but only if ID really changed, to not get dummy entries in the browser history
				if ( ( 'pushState' in window.history ) && null !== window.history.pushState ) {
					window.history.pushState( '', '', window.location.href.replace( /table_id=[0-9a-zA-Z-_]+/gi, 'table_id=' + data.table_id ) );
				}
			}
			// update CSS class for data field form
			$id( 'edit-form' ).removeClass( 'tablepress-edit-screen-id-' + tp.table.id ).addClass( 'tablepress-edit-screen-id-' + data.table_id );
			// update table ID in input fields (type text and hidden)
			tp.table.id = tp.table.new_id = data.table_id;
			$id( 'table-id' ).val( tp.table.id );
			$id( 'table-new-id' ).val( tp.table.new_id );
			// update the Shortcode text field
			$( '.table-shortcode' ).val( '[' + tablepress_options.shortcode + ' id=' + tp.table.new_id + ' /]' );
			// update the nonces
			$id( 'nonce-edit-table' ).val( data.new_edit_nonce );
			$id( 'nonce-preview-table' ).val( data.new_preview_nonce );
			// update URLs in Preview links
			var $show_preview_buttons = $( '.show-preview-button' );
			if ( $show_preview_buttons.length ) { // check necessary, because Preview button might not be visible
				$show_preview_buttons.attr( 'href',
					$show_preview_buttons.first().attr( 'href' )
						.replace( /item=[a-zA-Z0-9_-]+/g, 'item=' + data.table_id )
						.replace( /&_wpnonce=[a-z0-9]+/ig, '&_wpnonce=' + data.new_preview_nonce )
				);
			}
			// update last modified date and user nickname
			$id( 'last-modified' ).text( data.last_modified );
			$id( 'last-editor' ).text( data.last_editor );
			tp.table.unset_table_changed();
			tp.save_changes.after_saving_dialog( 'success', tablepress_strings[ data.message ] );
		},
		error: function( message ) {
			tp.save_changes.after_saving_dialog( 'error', message );
		},
		after_saving_dialog: function( type, message ) {
			if ( 'undefined' === typeof message ) {
				message = '';
			} else {
				message = ': ' + message;
			}
			var delay,
				div_class = 'save-changes-' + type;
			if ( 'success' === type ) {
				div_class += ' notice notice-success';
				delay = 3000;
			} else {
				div_class += ' notice notice-error';
				delay = 6000;
			}
			$( '.animation-saving' ).closest( 'p' )
				.after( '<div class="ajax-alert ' + div_class + '"><p>' + tablepress_strings['save_changes_' + type] + message + '</p></div>' );
			$( '.animation-saving' ).remove();
			$( '.save-changes-' + type ).delay( delay ).fadeOut( 2000, function() { $(this).remove(); } );
			$( '.save-changes-button' ).prop( 'disabled', false );
			$( 'body' ).removeClass( 'wait' );
		}
	};

	tp.init = function() {
		var callbacks = {
			'click': {
				'#rows-insert':			tp.rows.insert,
				'#columns-insert':		tp.columns.insert,
				'#rows-duplicate':		tp.rows.duplicate,
				'#columns-duplicate':	tp.columns.duplicate,
				'#rows-remove':			tp.rows.remove,
				'#columns-remove':		tp.columns.remove,
				'#rows-hide':			tp.rows.hide,
				'#columns-hide':		tp.columns.hide,
				'#rows-unhide':			tp.rows.unhide,
				'#columns-unhide':		tp.columns.unhide,
				'#rows-append':			tp.rows.append,
				'#columns-append':		tp.columns.append,
				'#link-add':			tp.content.link.add,
				'#image-add':			tp.content.image.add,
				'#span-add-rowspan':	function() { tp.content.span.add( '#rowspan#' ); },
				'#span-add-colspan':	function() { tp.content.span.add( '#colspan#' ); },
				'.show-preview-button': tp.table.preview.trigger,
				'.save-changes-button': tp.save_changes.trigger,
				'.show-help-box':		function() {
					$(this).next().wpdialog( {
						title: $(this).attr( 'title' ),
						height: 470,
						width: 320,
						modal: true,
						dialogClass: 'wp-dialog',
						resizable: false
					} );
				}
			},
			'keyup': {
				'#table-new-id':		tp.check.table_id
			},
			'change': {
				'#option-table-head':			tp.table.change_table_head,
				'#option-table-foot':			tp.table.change_table_foot,
				'#option-use-datatables':		tp.table.change_datatables,
				'#option-datatables-paginate':	tp.table.change_datatables_pagination
			},
			'blur': {
				'#table-new-id':		tp.table.change_id	// onchange would not recognize changed values from tp.check.table_id
			}
		},
		$table = $id( 'edit-form-body' );

		$.each( callbacks, function( event, event_callbacks ) {
			$.each( event_callbacks, function( selector, callback ) {
				$( selector ).on( event, callback );
			} );
		} );

		$( window ).on( 'beforeunload', tp.check.changes_saved );

		// do this before the next lines, to not trigger set_table_changed()
		$id( 'option-table-head' ).change(); // init changed/disabled states of DataTables JS features checkboxes
		$id( 'option-print-name' ).change( tp.table.change_print_name_description ).change(); // init dropdowns for name and description position
		$id( 'option-print-description' ).change( tp.table.change_print_name_description ).change();

		// just once is enough, will be reset after saving
		$table.one( 'change', 'textarea', tp.table.set_table_changed );
		$( '#tablepress_edit-table-information, #tablepress_edit-table-options, #tablepress_edit-datatables-features' ).one( 'change', 'input, textarea, select', tp.table.set_table_changed );

		if ( tablepress_options.cells_advanced_editor ) {
			$table.on( 'click', 'textarea', tp.cells.advanced_editor.keyopen );
			$id( 'advanced-editor-open' ).on( 'click', tp.cells.advanced_editor.buttonopen );
			$id( 'advanced-editor-confirm' ).on( 'click', tp.cells.advanced_editor.save );
			$id( 'advanced-editor-cancel' ).on( 'click', tp.cells.advanced_editor.close );
			$id( 'advanced-editor' ).wpdialog( {
				autoOpen: false,
				title: $id( 'advanced-editor-open' ).val(),
				width: 600,
				modal: true,
				dialogClass: 'wp-dialog',
				resizable: false
			} );
			// Fix issue with input fields not being usable (they are immediately losing focus without this) in the wpLink dialog when called through the "Advanced Editor"
			$id( 'wp-link' ).on( 'focus', 'input', function( event ) {
				event.stopPropagation();
			} );
		} else {
			$id( 'advanced-editor-open' ).hide();
		}

		// Fix issue with input fields not being usable (they are immediately losing focus without this) in the sidebar of the new Media Manager
		$( 'body' ).on( 'focus', '.media-modal .media-frame-content input, .media-modal .media-frame-content textarea', function( event ) {
			event.stopPropagation();
		} );

		if ( tablepress_options.cells_auto_grow ) {
			$table.on( 'focus', 'textarea', tp.cells.autogrow );
		}

		$id( 'edit-form-body' ).on( 'click', 'input:checkbox', { parent: '#edit-form-body' }, tp.cells.checkboxes.multi_select );
		$id( 'edit-form-foot' ).on( 'click', 'input:checkbox', { parent: '#edit-form-foot' }, tp.cells.checkboxes.multi_select );

		$id( 'edit-form-head' ).on( 'click', '.sort-control', tp.rows.sort );

		// on form submit: Enable disabled fields, so that they are transmitted in the POST request
		$id( 'tablepress-page' ).find( 'form' ).on( 'submit', function() {
			$(this).find( '.tablepress-postbox-table' ).find( 'input, select' ).prop( 'disabled', false );
		} );

		$table.sortable( {
			axis: 'y',
			containment: $id( 'edit-form' ), // to get better behavior when dragging before/after the first/last row
			forceHelperSize: true, // necessary?
			handle: '.move-handle',
			start: tp.rows.move.start,
			change: tp.rows.move.change,
			stop: tp.rows.move.stop,
			update: tp.reindex
		} ); // disableSelection() prohibits selection of text in textareas via keyboard

		$id( 'edit-form-head' ).sortable( {
			axis: 'x',
			items: '.head',
			containment: 'parent',
			forceHelperSize: true, // necessary?
			helper: 'clone',
			handle: '.move-handle',
			start: tp.columns.move.start,
			stop: tp.columns.move.stop,
			change: tp.columns.move.change,
			sort: tp.columns.move.sort
		} ).disableSelection();
	};

	// Run TablePress initialization.
	tp.init();

} );
