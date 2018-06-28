/**
 * Generates the XHTML Friends Network 'rel' string from the inputs.
 *
 * @deprecated 3.5.0
 * @output wp-admin/js/xfn.js
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

	function setActionState( $action, state ) {
		$action.children().hide();
		$action.children( '.' + state ).show();
	}

	function clearResultsAfterRow( $requestRow ) {
		$requestRow.removeClass( 'has-request-results' );

		if ( $requestRow.next().hasClass( 'request-results' ) ) {
			$requestRow.next().remove();
		}
	}

	function appendResultsAfterRow( $requestRow, classes, summaryMessage, additionalMessages ) {
		var itemList = '',
			resultRowClasses = 'request-results';

		clearResultsAfterRow( $requestRow );

		if ( additionalMessages.length ) {
			$.each( additionalMessages, function( index, value ) {
				itemList = itemList + '<li>' + value + '</li>';
			});
			itemList = '<ul>' + itemList + '</ul>';
		}

		$requestRow.addClass( 'has-request-results' );

		if ( $requestRow.hasClass( 'status-request-confirmed' ) ) {
			resultRowClasses = resultRowClasses + ' status-request-confirmed';
		}

		if ( $requestRow.hasClass( 'status-request-failed' ) ) {
			resultRowClasses = resultRowClasses + ' status-request-failed';
		}

		$requestRow.after( function() {
			return '<tr class="' + resultRowClasses + '"><th colspan="5">' +
				'<div class="notice inline notice-alt ' + classes + '">' +
				'<p>' + summaryMessage + '</p>' +
				itemList +
				'</div>' +
				'</td>' +
				'</tr>';
		});
	}

	$( '.export-personal-data-handle' ).click( function( event ) {

		var $this          = $( this ),
			$action        = $this.parents( '.export-personal-data' ),
			$requestRow    = $this.parents( 'tr' ),
			requestID      = $action.data( 'request-id' ),
			nonce          = $action.data( 'nonce' ),
			exportersCount = $action.data( 'exporters-count' ),
			sendAsEmail    = $action.data( 'send-as-email' ) ? true : false;

		event.preventDefault();
		event.stopPropagation();

		$action.blur();
		clearResultsAfterRow( $requestRow );

		function onExportDoneSuccess( zipUrl ) {
			setActionState( $action, 'export-personal-data-success' );
			if ( 'undefined' !== typeof zipUrl ) {
				window.location = zipUrl;
			} else if ( ! sendAsEmail ) {
				onExportFailure( strings.noExportFile );
			}
		}

		function onExportFailure( errorMessage ) {
			setActionState( $action, 'export-personal-data-failed' );
			if ( errorMessage ) {
				appendResultsAfterRow( $requestRow, 'notice-error', strings.exportError, [ errorMessage ] );
			}
		}

		function doNextExport( exporterIndex, pageIndex ) {
			$.ajax(
				{
					url: window.ajaxurl,
					data: {
						action: 'wp-privacy-export-personal-data',
						exporter: exporterIndex,
						id: requestID,
						page: pageIndex,
						security: nonce,
						sendAsEmail: sendAsEmail
					},
					method: 'post'
				}
			).done( function( response ) {
				var responseData = response.data;

				if ( ! response.success ) {

					// e.g. invalid request ID
					onExportFailure( response.data );
					return;
				}

				if ( ! responseData.done ) {
					setTimeout( doNextExport( exporterIndex, pageIndex + 1 ) );
				} else {
					if ( exporterIndex < exportersCount ) {
						setTimeout( doNextExport( exporterIndex + 1, 1 ) );
					} else {
						onExportDoneSuccess( responseData.url );
					}
				}
			}).fail( function( jqxhr, textStatus, error ) {

				// e.g. Nonce failure
				onExportFailure( error );
			});
		}

		// And now, let's begin
		setActionState( $action, 'export-personal-data-processing' );
		doNextExport( 1, 1 );
	});

	$( '.remove-personal-data-handle' ).click( function( event ) {

		var $this         = $( this ),
			$action       = $this.parents( '.remove-personal-data' ),
			$requestRow   = $this.parents( 'tr' ),
			requestID     = $action.data( 'request-id' ),
			nonce         = $action.data( 'nonce' ),
			erasersCount  = $action.data( 'erasers-count' ),
			hasRemoved    = false,
			hasRetained   = false,
			messages      = [];

		event.stopPropagation();

		$action.blur();
		clearResultsAfterRow( $requestRow );

		function onErasureDoneSuccess() {
			var summaryMessage = strings.noDataFound;
			var classes = 'notice-success';

			setActionState( $action, 'remove-personal-data-idle' );

			if ( false === hasRemoved ) {
				if ( false === hasRetained ) {
					summaryMessage = strings.noDataFound;
				} else {
					summaryMessage = strings.noneRemoved;
					classes = 'notice-warning';
				}
			} else {
				if ( false === hasRetained ) {
					summaryMessage = strings.foundAndRemoved;
				} else {
					summaryMessage = strings.someNotRemoved;
					classes = 'notice-warning';
				}
			}
			appendResultsAfterRow( $requestRow, 'notice-success', summaryMessage, messages );
		}

		function onErasureFailure() {
			setActionState( $action, 'remove-personal-data-failed' );
			appendResultsAfterRow( $requestRow, 'notice-error', strings.removalError, [] );
		}

		function doNextErasure( eraserIndex, pageIndex ) {
			$.ajax({
				url: window.ajaxurl,
				data: {
					action: 'wp-privacy-erase-personal-data',
					eraser: eraserIndex,
					id: requestID,
					page: pageIndex,
					security: nonce
				},
				method: 'post'
			}).done( function( response ) {
				var responseData = response.data;

				if ( ! response.success ) {
					onErasureFailure();
					return;
				}
				if ( responseData.items_removed ) {
					hasRemoved = hasRemoved || responseData.items_removed;
				}
				if ( responseData.items_retained ) {
					hasRetained = hasRetained || responseData.items_retained;
				}
				if ( responseData.messages ) {
					messages = messages.concat( responseData.messages );
				}
				if ( ! responseData.done ) {
					setTimeout( doNextErasure( eraserIndex, pageIndex + 1 ) );
				} else {
					if ( eraserIndex < erasersCount ) {
						setTimeout( doNextErasure( eraserIndex + 1, 1 ) );
					} else {
						onErasureDoneSuccess();
					}
				}
			}).fail( function() {
				onErasureFailure();
			});
		}

		// And now, let's begin
		setActionState( $action, 'remove-personal-data-processing' );

		doNextErasure( 1, 1 );
	});
});

( function( $ ) {

	// Privacy policy page, copy button.
	$( document ).on( 'click', function( event ) {
		var $target = $( event.target );
		var $parent, $container, range;

		if ( $target.is( 'button.privacy-text-copy' ) ) {
			$parent = $target.parent().parent();
			$container = $parent.find( 'div.wp-suggested-text' );

			if ( ! $container.length ) {
				$container = $parent.find( 'div.policy-text' );
			}

			if ( $container.length ) {
				try {
					window.getSelection().removeAllRanges();
					range = document.createRange();
					$container.addClass( 'hide-privacy-policy-tutorial' );

					range.selectNodeContents( $container[0] );
					window.getSelection().addRange( range );
					document.execCommand( 'copy' );

					$container.removeClass( 'hide-privacy-policy-tutorial' );
					window.getSelection().removeAllRanges();
				} catch ( er ) {}
			}
		}
	});

} ( jQuery ) );
