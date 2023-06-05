/* global jQuery, woocommerce_admin_system_status, wcSetClipboard, wcClearClipboard */
jQuery( function ( $ ) {
	/**
	 * Users country and state fields
	 */
	var wcSystemStatus = {
		init: function () {
			$( document.body )
				.on(
					'click',
					'a.help_tip, a.woocommerce-help-tip, woocommerce-product-type-tip',
					this.preventTipTipClick
				)
				.on( 'click', 'a.debug-report', this.generateReport )
				.on( 'click', '#copy-for-support', this.copyReport )
				.on( 'aftercopy', '#copy-for-support', this.copySuccess )
				.on( 'aftercopyfailure', '#copy-for-support', this.copyFail )
				.on( 'click', '#download-for-support', this.downloadReport );
		},

		/**
		 * Prevent anchor behavior when click on TipTip.
		 *
		 * @return {Bool}
		 */
		preventTipTipClick: function() {
			return false;
		},

		/**
		 * Generate system status report.
		 *
		 * @return {Bool}
		 */
		generateReport: function() {
			var report = '';

			$( '.wc_status_table thead, .wc_status_table tbody' ).each( function() {
				if ( $( this ).is( 'thead' ) ) {
					var label = $( this ).find( 'th:eq(0)' ).data( 'exportLabel' ) || $( this ).text();
					report = report + '\n### ' + label.trim() + ' ###\n\n';
				} else {
					$( 'tr', $( this ) ).each( function() {
						var label       = $( this ).find( 'td:eq(0)' ).data( 'exportLabel' ) || $( this ).find( 'td:eq(0)' ).text();
						var the_name    = label.trim().replace( /(<([^>]+)>)/ig, '' ); // Remove HTML.

						// Find value
						var $value_html = $( this ).find( 'td:eq(2)' ).clone();
						$value_html.find( '.private' ).remove();
						$value_html.find( '.dashicons-yes' ).replaceWith( '&#10004;' );
						$value_html.find( '.dashicons-no-alt, .dashicons-warning' ).replaceWith( '&#10060;' );

						// Format value
						var the_value   = $value_html.text().trim();
						var value_array = the_value.split( ', ' );

						if ( value_array.length > 1 ) {
							// If value have a list of plugins ','.
							// Split to add new line.
							var temp_line ='';
							$.each( value_array, function( key, line ) {
								temp_line = temp_line + line + '\n';
							});

							the_value = temp_line;
						}

						report = report + '' + the_name + ': ' + the_value + '\n';
					});
				}
			});

			try {
				$( '#debug-report' ).slideDown();
				$( '#debug-report' ).find( 'textarea' ).val( '`' + report + '`' ).trigger( 'focus' ).trigger( 'select' );
				$( this ).fadeOut();
				return false;
			} catch ( e ) {
				/* jshint devel: true */
				console.log( e );
			}

			return false;
		},

		/**
		 * Copy for report.
		 *
		 * @param {Object} evt Copy event.
		 */
		copyReport: function( evt ) {
			wcClearClipboard();
			wcSetClipboard( $( '#debug-report' ).find( 'textarea' ).val(), $( this ) );
			evt.preventDefault();
		},

		/**
		 * Display a "Copied!" tip when success copying
		 */
		copySuccess: function() {
			$( '#copy-for-support' ).tipTip({
				'attribute':  'data-tip',
				'activation': 'focus',
				'fadeIn':     50,
				'fadeOut':    50,
				'delay':      0
			}).trigger( 'focus' );
		},

		/**
		 * Displays the copy error message when failure copying.
		 */
		copyFail: function() {
			$( '.copy-error' ).removeClass( 'hidden' );
			$( '#debug-report' ).find( 'textarea' ).trigger( 'focus' ).trigger( 'select' );
		},

		downloadReport: function() {
			var ssr_text = new Blob( [ $( '#debug-report' ).find( 'textarea' ).val() ], { type: 'text/plain' } );

			var domain = window.location.hostname;
			var datetime = new Date().toISOString().slice( 0, 19 ).replace( /:/g, '-' );

			var a = document.createElement( 'a' );
			a.download = 'SystemStatusReport_' + domain + '_' + datetime + '.txt';
			a.href = window.URL.createObjectURL( ssr_text );
			a.textContent = 'Download ready';
			a.style='display:none';
			a.click();
			a.remove();
		}
	};

	wcSystemStatus.init();

	$( '.wc_status_table' ).on( 'click', '.run-tool .button', function( evt ) {
		evt.stopImmediatePropagation();
		return window.confirm( woocommerce_admin_system_status.run_tool_confirmation );
	});

	$( '#log-viewer-select' ).on( 'click', 'h2 a.page-title-action', function( evt ) {
		evt.stopImmediatePropagation();
		return window.confirm( woocommerce_admin_system_status.delete_log_confirmation );
	});
});
