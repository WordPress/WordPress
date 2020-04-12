/**
 * Interactions used by the User Privacy tools in WordPress.
 *
 * @output wp-admin/js/privacy-tools.js
 */

// Privacy request action handling.
jQuery( document ).ready( function( $ ) {
	var strings = window.privacyToolsL10n || {};

	function setActionState( $action, state ) {
		$action.children().addClass( 'hidden' );
		$action.children( '.' + state ).removeClass( 'hidden' );
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
			$progress      = $requestRow.find( '.export-progress' ),
			$rowActions    = $this.parents( '.row-actions' ),
			requestID      = $action.data( 'request-id' ),
			nonce          = $action.data( 'nonce' ),
			exportersCount = $action.data( 'exporters-count' ),
			sendAsEmail    = $action.data( 'send-as-email' ) ? true : false;

		event.preventDefault();
		event.stopPropagation();

		$rowActions.addClass( 'processing' );

		$action.blur();
		clearResultsAfterRow( $requestRow );
		setExportProgress( 0 );

		function onExportDoneSuccess( zipUrl ) {
			var summaryMessage = strings.emailSent;

			setActionState( $action, 'export-personal-data-success' );

			appendResultsAfterRow( $requestRow, 'notice-success', summaryMessage, [] );

			if ( 'undefined' !== typeof zipUrl ) {
				window.location = zipUrl;
			} else if ( ! sendAsEmail ) {
				onExportFailure( strings.noExportFile );
			}

			setTimeout( function(){ $rowActions.removeClass( 'processing' ); }, 500 );
		}

		function onExportFailure( errorMessage ) {
			setActionState( $action, 'export-personal-data-failed' );
			if ( errorMessage ) {
				appendResultsAfterRow( $requestRow, 'notice-error', strings.exportError, [ errorMessage ] );
			}

			setTimeout( function(){ $rowActions.removeClass( 'processing' ); }, 500 );
		}

		function setExportProgress( exporterIndex ) {
			var progress       = ( exportersCount > 0 ? exporterIndex / exportersCount : 0 );
			var progressString = Math.round( progress * 100 ).toString() + '%';
			$progress.html( progressString );
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
					// e.g. invalid request ID.
					setTimeout( function(){ onExportFailure( response.data ); }, 500 );
					return;
				}

				if ( ! responseData.done ) {
					setTimeout( doNextExport( exporterIndex, pageIndex + 1 ) );
				} else {
					setExportProgress( exporterIndex );
					if ( exporterIndex < exportersCount ) {
						setTimeout( doNextExport( exporterIndex + 1, 1 ) );
					} else {
						setTimeout( function(){ onExportDoneSuccess( responseData.url ); }, 500 );
					}
				}
			}).fail( function( jqxhr, textStatus, error ) {
				// e.g. Nonce failure.
				setTimeout( function(){ onExportFailure( error ); }, 500 );
			});
		}

		// And now, let's begin.
		setActionState( $action, 'export-personal-data-processing' );
		doNextExport( 1, 1 );
	});

	$( '.remove-personal-data-handle' ).click( function( event ) {
		var $this         = $( this ),
			$action       = $this.parents( '.remove-personal-data' ),
			$requestRow   = $this.parents( 'tr' ),
			$progress     = $requestRow.find( '.erasure-progress' ),
			$rowActions   = $this.parents( '.row-actions' ),
			requestID     = $action.data( 'request-id' ),
			nonce         = $action.data( 'nonce' ),
			erasersCount  = $action.data( 'erasers-count' ),
			hasRemoved    = false,
			hasRetained   = false,
			messages      = [];

		event.preventDefault();
		event.stopPropagation();

		$rowActions.addClass( 'processing' );

		$action.blur();
		clearResultsAfterRow( $requestRow );
		setErasureProgress( 0 );

		function onErasureDoneSuccess() {
			var summaryMessage = strings.noDataFound;
			var classes = 'notice-success';

			setActionState( $action, 'remove-personal-data-success' );

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
			appendResultsAfterRow( $requestRow, classes, summaryMessage, messages );

			setTimeout( function(){ $rowActions.removeClass( 'processing' ); }, 500 );
		}

		function onErasureFailure() {
			setActionState( $action, 'remove-personal-data-failed' );
			appendResultsAfterRow( $requestRow, 'notice-error', strings.removalError, [] );

			setTimeout( function(){ $rowActions.removeClass( 'processing' ); }, 500 );
		}

		function setErasureProgress( eraserIndex ) {
			var progress       = ( erasersCount > 0 ? eraserIndex / erasersCount : 0 );
			var progressString = Math.round( progress * 100 ).toString() + '%';
			$progress.html( progressString );
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
					setTimeout( function(){ onErasureFailure(); }, 500 );
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
					setErasureProgress( eraserIndex );
					if ( eraserIndex < erasersCount ) {
						setTimeout( doNextErasure( eraserIndex + 1, 1 ) );
					} else {
						setTimeout( function(){ onErasureDoneSuccess(); }, 500 );
					}
				}
			}).fail( function() {
				setTimeout( function(){ onErasureFailure(); }, 500 );
			});
		}

		// And now, let's begin.
		setActionState( $action, 'remove-personal-data-processing' );

		doNextErasure( 1, 1 );
	});

	// Privacy Policy page, copy action.
	$( document ).on( 'click', function( event ) {
		var $parent,
			$container,
			range,
			__ = wp.i18n.__,
			$target = $( event.target ),
			copiedNotice = $target.siblings( '.success' );

		if ( $target.is( 'button.privacy-text-copy' ) ) {
			$parent = $target.parent().parent();
			$container = $parent.find( 'div.wp-suggested-text' );

			if ( ! $container.length ) {
				$container = $parent.find( 'div.policy-text' );
			}

			if ( $container.length ) {
				try {
					var documentPosition = document.documentElement.scrollTop,
						bodyPosition     = document.body.scrollTop;

					// Setup copy.
					window.getSelection().removeAllRanges();

					// Hide tutorial content to remove from copied content.
					range = document.createRange();
					$container.addClass( 'hide-privacy-policy-tutorial' );

					// Copy action.
					range.selectNodeContents( $container[0] );
					window.getSelection().addRange( range );
					document.execCommand( 'copy' );

					// Reset section.
					$container.removeClass( 'hide-privacy-policy-tutorial' );
					window.getSelection().removeAllRanges();

					// Return scroll position - see #49540.
					if ( documentPosition > 0 && documentPosition !== document.documentElement.scrollTop ) {
						document.documentElement.scrollTop = documentPosition;
					} else if ( bodyPosition > 0 && bodyPosition !== document.body.scrollTop ) {
						document.body.scrollTop = bodyPosition;
					}

					// Display and speak notice to indicate action complete.
					copiedNotice.addClass( 'visible' );
					wp.a11y.speak( __( 'The section has been copied to your clipboard.' ) );

					// Delay notice dismissal.
					setTimeout( function(){ copiedNotice.removeClass( 'visible' ); }, 3000 );
				} catch ( er ) {}
			}
		}
	});
});

