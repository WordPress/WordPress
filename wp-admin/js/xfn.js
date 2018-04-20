/**
 * Generates the XHTML Friends Network 'rel' string from the inputs.
 *
 * @deprecated 3.5.0
 */
jQuery( document ).ready(function( $ ) {
	$( '#link_rel' ).prop( 'readonly', true );
	$( '#linkxfndiv input' ).bind( 'click keyup', function() {
		var isMe = $( '#me' ).is( ':checked' ), inputs = '';
		$( 'input.valinp' ).each( function() {
			if ( isMe ) {
				$( this ).prop( 'disabled', true ).parent().addClass( 'disabled' );
			} else {
				$( this ).removeAttr( 'disabled' ).parent().removeClass( 'disabled' );
				if ( $( this ).is( ':checked' ) && $( this ).val() !== '') {
					inputs += $( this ).val() + ' ';
				}
			}
		});
		$( '#link_rel' ).val( ( isMe ) ? 'me' : inputs.substr( 0,inputs.length - 1 ) );
	});
});

// Privacy request action handling

jQuery( document ).ready( function( $ ) {
	var strings = window.privacyToolsL10n || {};

	function set_action_state( $action, state ) {
		$action.children().hide();
		$action.children( '.' + state ).show();
	}

	function clearResultsAfterRow( $requestRow ) {
		if ( $requestRow.next().hasClass( 'request-results' ) ) {
			$requestRow.next().remove();
		}
	}

	function appendResultsAfterRow( $requestRow, classes, summaryMessage, additionalMessages ) {
		clearResultsAfterRow( $requestRow );
		if ( additionalMessages.length ) {
			// TODO - render additionalMessages after the summaryMessage
		}

		$requestRow.after( function() {
			return '<tr class="request-results"><td colspan="5"><div class="notice inline notice-alt ' + classes + '"><p>' +
				summaryMessage +
				'</p></div></td></tr>';
		} );
	}

	$( '.remove_personal_data a' ).click( function( event ) {
		event.preventDefault();
		event.stopPropagation();

		var $this         = $( this );
		var $action       = $this.parents( '.remove_personal_data' );
		var $requestRow   = $this.parents( 'tr' );
		var requestID     = $action.data( 'request-id' );
		var nonce         = $action.data( 'nonce' );
		var erasersCount  = $action.data( 'erasers-count' );

		var removedCount  = 0;
		var retainedCount = 0;
		var messages      = [];

		$action.blur();
		clearResultsAfterRow( $requestRow );

		function on_erasure_done_success() {
			set_action_state( $action, 'remove_personal_data_idle' );
			var summaryMessage = strings.noDataFound;
			var classes = 'notice-success';
			if ( 0 === removedCount ) {
				if ( 0 === retainedCount ) {
					summaryMessage = strings.noDataFound;
				} else {
					summaryMessage = strings.noneRemoved;
					classes = 'notice-warning';
				}
			} else {
				if ( 0 === retainedCount ) {
					summaryMessage = strings.foundAndRemoved;
				} else {
					summaryMessage = strings.someNotRemoved;
					classes = 'notice-warning';
				}
			}
			appendResultsAfterRow( $requestRow, 'notice-success', summaryMessage, [] );
		}

		function on_erasure_failure() {
			set_action_state( $action, 'remove_personal_data_failed' );
			appendResultsAfterRow( $requestRow, 'notice-error', strings.anErrorOccurred, [] );
		}

		function do_next_erasure( eraserIndex, pageIndex ) {
			$.ajax( {
				url: window.ajaxurl,
				data: {
					action: 'wp-privacy-erase-personal-data',
					eraser: eraserIndex,
					id: requestID,
					page: pageIndex,
					security: nonce
				},
				method: 'post'
			} ).done( function( response ) {
				if ( ! response.success ) {
					on_erasure_failure();
					return;
				}
				var responseData = response.data;
				if ( responseData.num_items_removed ) {
					removedCount += responseData.num_items_removed;
				}
				if ( responseData.num_items_retained ) {
					retainedCount += responseData.num_items_removed;
				}
				if ( responseData.messages ) {
					messages = messages.concat( responseData.messages );
				}
				if ( ! responseData.done ) {
					setTimeout( do_next_erasure( eraserIndex, pageIndex + 1 ) );
				} else {
					if ( eraserIndex < erasersCount ) {
						setTimeout( do_next_erasure( eraserIndex + 1, 1 ) );
					} else {
						on_erasure_done_success();
					}
				}
			} ).fail( function() {
				on_erasure_failure();
			} );
		}

		// And now, let's begin
		set_action_state( $action, 'remove_personal_data_processing' );

		do_next_erasure( 1, 1 );
	} );
} );
