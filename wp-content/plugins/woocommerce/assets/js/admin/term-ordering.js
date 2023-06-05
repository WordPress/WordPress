/*global ajaxurl, woocommerce_term_ordering_params */

/* Modifided script from the simple-page-ordering plugin */
jQuery( function( $ ) {

	var table_selector   = 'table.wp-list-table',
		item_selector    = 'tbody tr:not(.inline-edit-row)',
		term_id_selector = '.column-handle input[name="term_id"]',
		column_handle    = '<td class="column-handle"></td>';

	if ( 0 === $( table_selector ).find( '.column-handle' ).length ) {
		$( table_selector ).find( 'tr:not(.inline-edit-row)' ).append( column_handle );

		term_id_selector = '.check-column input';
	}

	// Stand-in wcTracks.recordEvent in case tracks is not available (for any reason).
	window.wcTracks = window.wcTracks || {};
	window.wcTracks.recordEvent = window.wcTracks.recordEvent  || function() { };

	$( table_selector ).find( '.column-handle' ).show();

	$.wc_add_missing_sort_handles = function() {
		var all_table_rows = $( table_selector ).find('tbody > tr');
		var rows_with_handle = $( table_selector ).find('tbody > tr > td.column-handle').parent();
		if ( all_table_rows.length !== rows_with_handle.length ) {
			all_table_rows.each(function(index, elem){
				if ( ! rows_with_handle.is( elem ) ) {
					$( elem ).append( column_handle );
				}
			});
		}
		$( table_selector ).find( '.column-handle' ).show();
	};

	$( document ).ajaxComplete( function( event, request, options ) {
		if (
			request &&
			4 === request.readyState &&
			200 === request.status &&
			options.data &&
			( 0 <= options.data.indexOf( '_inline_edit' ) || 0 <= options.data.indexOf( 'add-tag' ) )
		) {
			$.wc_add_missing_sort_handles();
			$( document.body ).trigger( 'init_tooltips' );
		}
	} );

	$( table_selector ).sortable({
		items: item_selector,
		cursor: 'move',
		handle: '.column-handle',
		axis: 'y',
		forcePlaceholderSize: true,
		helper: 'clone',
		opacity: 0.65,
		placeholder: 'product-cat-placeholder',
		scrollSensitivity: 40,
		start: function( event, ui ) {
			if ( ! ui.item.hasClass( 'alternate' ) ) {
				ui.item.css( 'background-color', '#ffffff' );
			}
			ui.item.children( 'td, th' ).css( 'border-bottom-width', '0' );
			ui.item.css( 'outline', '1px solid #aaa' );
		},
		stop: function( event, ui ) {
			ui.item.removeAttr( 'style' );
			ui.item.children( 'td, th' ).css( 'border-bottom-width', '1px' );
		},
		update: function( event, ui ) {
			var termid     = ui.item.find( term_id_selector ).val(); // this post id
			var termparent = ui.item.find( '.parent' ).html();            // post parent

			var prevtermid = ui.item.prev().find( term_id_selector ).val();
			var nexttermid = ui.item.next().find( term_id_selector ).val();

			// Can only sort in same tree
			var prevtermparent, nexttermparent;
			if ( prevtermid !== undefined ) {
				prevtermparent = ui.item.prev().find( '.parent' ).html();
				if ( prevtermparent !== termparent) {
					prevtermid = undefined;
				}
			}

			if ( nexttermid !== undefined ) {
				nexttermparent = ui.item.next().find( '.parent' ).html();
				if ( nexttermparent !== termparent) {
					nexttermid = undefined;
				}
			}

			// If previous and next not at same tree level, or next not at same tree level and
			// the previous is the parent of the next, or just moved item beneath its own children.
			if (
				( prevtermid === undefined && nexttermid === undefined ) ||
				( nexttermid === undefined && nexttermparent === prevtermid ) ||
				( nexttermid !== undefined && prevtermparent === termid )
			) {
				$( table_selector ).sortable( 'cancel' );
				return;
			}

			window.wcTracks.recordEvent( 'product_attributes_ordering_term', {
				is_category:
					woocommerce_term_ordering_params.taxonomy === 'product_cat'
						? 'yes'
						: 'no',
			} );

			// Show Spinner
			ui.item.find( '.check-column input' ).hide();
			ui.item
				.find( '.check-column' )
				.append( '<img alt="processing" src="images/wpspin_light.gif" class="waiting" style="margin-left: 6px;" />' );

			// Go do the sorting stuff via ajax.
			$.post(
				ajaxurl,
				{
					action: 'woocommerce_term_ordering',
					id: termid,
					nextid: nexttermid,
					thetaxonomy: woocommerce_term_ordering_params.taxonomy
				},
				function(response) {
					if ( response === 'children' ) {
						window.location.reload();
					} else {
						ui.item.find( '.check-column input' ).show();
						ui.item.find( '.check-column' ).find( 'img' ).remove();
					}
				}
			);

			// Fix cell colors
			$( 'table.widefat tbody tr' ).each( function() {
				var i = jQuery( 'table.widefat tbody tr' ).index( this );
				if ( i%2 === 0 ) {
					jQuery( this ).addClass( 'alternate' );
				} else {
					jQuery( this ).removeClass( 'alternate' );
				}
			});
		}
	});

});
