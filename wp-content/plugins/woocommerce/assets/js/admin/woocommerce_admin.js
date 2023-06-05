/* global woocommerce_admin */
( function ( $, woocommerce_admin ) {
	$( function () {
		if ( 'undefined' === typeof woocommerce_admin ) {
			return;
		}

		// Add buttons to product screen.
		var $product_screen = $( '.edit-php.post-type-product' ),
			$title_action = $product_screen.find( '.page-title-action:first' ),
			$blankslate = $product_screen.find( '.woocommerce-BlankState' );

		if ( 0 === $blankslate.length ) {
			if ( woocommerce_admin.urls.add_product ) {
				$title_action
					.first()
					.attr( 'href', woocommerce_admin.urls.add_product );
			}
			if ( woocommerce_admin.urls.export_products ) {
				$title_action.after(
					'<a href="' +
						woocommerce_admin.urls.export_products +
						'" class="page-title-action">' +
						woocommerce_admin.strings.export_products +
						'</a>'
				);
			}
			if ( woocommerce_admin.urls.import_products ) {
				$title_action.after(
					'<a href="' +
						woocommerce_admin.urls.import_products +
						'" class="page-title-action">' +
						woocommerce_admin.strings.import_products +
						'</a>'
				);
			}
		} else {
			$title_action.hide();
		}

		// Progress indicators when showing steps.
		$( '.woocommerce-progress-form-wrapper .button-next' ).on(
			'click',
			function () {
				$( '.wc-progress-form-content' ).block( {
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6,
					},
				} );
				return true;
			}
		);

		// Field validation error tips
		$( document.body )
			.on( 'wc_add_error_tip', function ( e, element, error_type ) {
				var offset = element.position();

				if ( element.parent().find( '.wc_error_tip' ).length === 0 ) {
					element.after(
						'<div class="wc_error_tip ' +
							error_type +
							'">' +
							woocommerce_admin[ error_type ] +
							'</div>'
					);
					element
						.parent()
						.find( '.wc_error_tip' )
						.css(
							'left',
							offset.left +
								element.width() -
								element.width() / 2 -
								$( '.wc_error_tip' ).width() / 2
						)
						.css( 'top', offset.top + element.height() )
						.fadeIn( '100' );
				}
			} )

			.on( 'wc_remove_error_tip', function ( e, element, error_type ) {
				element
					.parent()
					.find( '.wc_error_tip.' + error_type )
					.fadeOut( '100', function () {
						$( this ).remove();
					} );
			} )

			.on( 'click', function () {
				$( '.wc_error_tip' ).fadeOut( '100', function () {
					$( this ).remove();
				} );
			} )

			.on(
				'blur',
				'.wc_input_decimal[type=text], .wc_input_price[type=text], .wc_input_country_iso[type=text]',
				function () {
					$( '.wc_error_tip' ).fadeOut( '100', function () {
						$( this ).remove();
					} );
				}
			)

			.on(
				'change',
				'.wc_input_price[type=text], .wc_input_decimal[type=text], .wc-order-totals #refund_amount[type=text], ' +
					'.wc_input_variations_price[type=text]',
				function () {
					var regex,
						decimalRegex,
						decimailPoint = woocommerce_admin.decimal_point;

					if (
						$( this ).is( '.wc_input_price' ) ||
						$( this ).is( '.wc_input_variations_price' ) ||
						$( this ).is( '#refund_amount' )
					) {
						decimailPoint = woocommerce_admin.mon_decimal_point;
					}

					regex = new RegExp(
						'[^-0-9%\\' + decimailPoint + ']+',
						'gi'
					);
					decimalRegex = new RegExp(
						'\\' + decimailPoint + '+',
						'gi'
					);

					var value = $( this ).val();
					var newvalue = value
						.replace( regex, '' )
						.replace( decimalRegex, decimailPoint );

					if ( value !== newvalue ) {
						$( this ).val( newvalue );
					}
				}
			)

			.on(
				'keyup',
				// eslint-disable-next-line max-len
				'.wc_input_price[type=text], .wc_input_decimal[type=text], .wc_input_country_iso[type=text], .wc-order-totals #refund_amount[type=text], .wc_input_variations_price[type=text]',
				function () {
					var regex, error, decimalRegex;
					var checkDecimalNumbers = false;
					if (
						$( this ).is( '.wc_input_price' ) ||
						$( this ).is( '.wc_input_variations_price' ) ||
						$( this ).is( '#refund_amount' )
					) {
						checkDecimalNumbers = true;
						regex = new RegExp(
							'[^-0-9%\\' +
								woocommerce_admin.mon_decimal_point +
								']+',
							'gi'
						);
						decimalRegex = new RegExp(
							'[^\\' + woocommerce_admin.mon_decimal_point + ']',
							'gi'
						);
						error = 'i18n_mon_decimal_error';
					} else if ( $( this ).is( '.wc_input_country_iso' ) ) {
						regex = new RegExp( '([^A-Z])+|(.){3,}', 'im' );
						error = 'i18n_country_iso_error';
					} else {
						checkDecimalNumbers = true;
						regex = new RegExp(
							'[^-0-9%\\' +
								woocommerce_admin.decimal_point +
								']+',
							'gi'
						);
						decimalRegex = new RegExp(
							'[^\\' + woocommerce_admin.decimal_point + ']',
							'gi'
						);
						error = 'i18n_decimal_error';
					}

					var value = $( this ).val();
					var newvalue = value.replace( regex, '' );

					// Check if newvalue have more than one decimal point.
					if (
						checkDecimalNumbers &&
						1 < newvalue.replace( decimalRegex, '' ).length
					) {
						newvalue = newvalue.replace( decimalRegex, '' );
					}

					if ( value !== newvalue ) {
						$( document.body ).triggerHandler( 'wc_add_error_tip', [
							$( this ),
							error,
						] );
					} else {
						$(
							document.body
						).triggerHandler( 'wc_remove_error_tip', [
							$( this ),
							error,
						] );
					}
				}
			)

			.on(
				'change',
				'#_sale_price.wc_input_price[type=text], .wc_input_price[name^=variable_sale_price]',
				function () {
					var sale_price_field = $( this ),
						regular_price_field;

					if (
						sale_price_field
							.attr( 'name' )
							.indexOf( 'variable' ) !== -1
					) {
						regular_price_field = sale_price_field
							.parents( '.variable_pricing' )
							.find(
								'.wc_input_price[name^=variable_regular_price]'
							);
					} else {
						regular_price_field = $( '#_regular_price' );
					}

					var sale_price = parseFloat(
						window.accounting.unformat(
							sale_price_field.val(),
							woocommerce_admin.mon_decimal_point
						)
					);
					var regular_price = parseFloat(
						window.accounting.unformat(
							regular_price_field.val(),
							woocommerce_admin.mon_decimal_point
						)
					);

					if ( sale_price >= regular_price ) {
						$( this ).val( '' );
					}
				}
			)

			.on(
				'keyup',
				'#_sale_price.wc_input_price[type=text], .wc_input_price[name^=variable_sale_price]',
				function () {
					var sale_price_field = $( this ),
						regular_price_field;

					if (
						sale_price_field
							.attr( 'name' )
							.indexOf( 'variable' ) !== -1
					) {
						regular_price_field = sale_price_field
							.parents( '.variable_pricing' )
							.find(
								'.wc_input_price[name^=variable_regular_price]'
							);
					} else {
						regular_price_field = $( '#_regular_price' );
					}

					var sale_price = parseFloat(
						window.accounting.unformat(
							sale_price_field.val(),
							woocommerce_admin.mon_decimal_point
						)
					);
					var regular_price = parseFloat(
						window.accounting.unformat(
							regular_price_field.val(),
							woocommerce_admin.mon_decimal_point
						)
					);

					if ( sale_price >= regular_price ) {
						$( document.body ).triggerHandler( 'wc_add_error_tip', [
							$( this ),
							'i18n_sale_less_than_regular_error',
						] );
					} else {
						$(
							document.body
						).triggerHandler( 'wc_remove_error_tip', [
							$( this ),
							'i18n_sale_less_than_regular_error',
						] );
					}
				}
			)

			.on( 'init_tooltips', function () {
				$( '.tips, .help_tip, .woocommerce-help-tip' ).tipTip( {
					attribute: 'data-tip',
					fadeIn: 50,
					fadeOut: 50,
					delay: 200,
					keepAlive: true,
				} );

				$( '.column-wc_actions .wc-action-button' ).tipTip( {
					fadeIn: 50,
					fadeOut: 50,
					delay: 200,
				} );

				// Add tiptip to parent element for widefat tables
				$( '.parent-tips' ).each( function () {
					$( this )
						.closest( 'a, th' )
						.attr( 'data-tip', $( this ).data( 'tip' ) )
						.tipTip( {
							attribute: 'data-tip',
							fadeIn: 50,
							fadeOut: 50,
							delay: 200,
							keepAlive: true,
						} )
						.css( 'cursor', 'help' );
				} );
			} )

			.on( 'click', '.wc-confirm-delete', function ( event ) {
				if (
					! window.confirm( woocommerce_admin.i18n_confirm_delete )
				) {
					event.stopPropagation();
				}
			} );

		// Tooltips
		$( document.body ).trigger( 'init_tooltips' );

		// wc_input_table tables
		$( '.wc_input_table.sortable tbody' ).sortable( {
			items: 'tr',
			cursor: 'move',
			axis: 'y',
			scrollSensitivity: 40,
			forcePlaceholderSize: true,
			helper: 'clone',
			opacity: 0.65,
			placeholder: 'wc-metabox-sortable-placeholder',
			start: function ( event, ui ) {
				ui.item.css( 'background-color', '#f6f6f6' );
			},
			stop: function ( event, ui ) {
				ui.item.removeAttr( 'style' );
			},
		} );
		// Focus on inputs within the table if clicked instead of trying to sort.
		$( '.wc_input_table.sortable tbody input' ).on( 'click', function () {
			$( this ).trigger( 'focus' );
		} );

		$( '.wc_input_table .remove_rows' ).on( 'click', function () {
			var $tbody = $( this ).closest( '.wc_input_table' ).find( 'tbody' );
			if ( $tbody.find( 'tr.current' ).length > 0 ) {
				var $current = $tbody.find( 'tr.current' );
				$current.each( function () {
					$( this ).remove();
				} );
			}
			return false;
		} );

		var controlled = false;
		var shifted = false;
		var hasFocus = false;

		$( document.body ).on( 'keyup keydown', function ( e ) {
			shifted = e.shiftKey;
			controlled = e.ctrlKey || e.metaKey;
		} );

		$( '.wc_input_table' )
			.on( 'focus click', 'input', function ( e ) {
				var $this_table = $( this ).closest( 'table, tbody' );
				var $this_row = $( this ).closest( 'tr' );

				if (
					( e.type === 'focus' && hasFocus !== $this_row.index() ) ||
					( e.type === 'click' && $( this ).is( ':focus' ) )
				) {
					hasFocus = $this_row.index();

					if ( ! shifted && ! controlled ) {
						$( 'tr', $this_table )
							.removeClass( 'current' )
							.removeClass( 'last_selected' );
						$this_row
							.addClass( 'current' )
							.addClass( 'last_selected' );
					} else if ( shifted ) {
						$( 'tr', $this_table ).removeClass( 'current' );
						$this_row
							.addClass( 'selected_now' )
							.addClass( 'current' );

						if ( $( 'tr.last_selected', $this_table ).length > 0 ) {
							if (
								$this_row.index() >
								$( 'tr.last_selected', $this_table ).index()
							) {
								$( 'tr', $this_table )
									.slice(
										$(
											'tr.last_selected',
											$this_table
										).index(),
										$this_row.index()
									)
									.addClass( 'current' );
							} else {
								$( 'tr', $this_table )
									.slice(
										$this_row.index(),
										$(
											'tr.last_selected',
											$this_table
										).index() + 1
									)
									.addClass( 'current' );
							}
						}

						$( 'tr', $this_table ).removeClass( 'last_selected' );
						$this_row.addClass( 'last_selected' );
					} else {
						$( 'tr', $this_table ).removeClass( 'last_selected' );
						if (
							controlled &&
							$( this ).closest( 'tr' ).is( '.current' )
						) {
							$this_row.removeClass( 'current' );
						} else {
							$this_row
								.addClass( 'current' )
								.addClass( 'last_selected' );
						}
					}

					$( 'tr', $this_table ).removeClass( 'selected_now' );
				}
			} )
			.on( 'blur', 'input', function () {
				hasFocus = false;
			} );

		// Additional cost and Attribute term tables
		$(
			'.woocommerce_page_wc-settings .shippingrows tbody tr:even, table.attributes-table tbody tr:nth-child(odd)'
		).addClass( 'alternate' );

		// Show order items on orders page
		$( document.body ).on( 'click', '.show_order_items', function () {
			$( this ).closest( 'td' ).find( 'table' ).toggle();
			return false;
		} );

		// Select availability
		$( 'select.availability' )
			.on( 'change', function () {
				if ( $( this ).val() === 'all' ) {
					$( this ).closest( 'tr' ).next( 'tr' ).hide();
				} else {
					$( this ).closest( 'tr' ).next( 'tr' ).show();
				}
			} )
			.trigger( 'change' );

		// Hidden options
		$( '.hide_options_if_checked' ).each( function () {
			$( this )
				.find( 'input:eq(0)' )
				.on( 'change', function () {
					if ( $( this ).is( ':checked' ) ) {
						$( this )
							.closest( 'fieldset, tr' )
							.nextUntil(
								'.hide_options_if_checked, .show_options_if_checked',
								'.hidden_option'
							)
							.hide();
					} else {
						$( this )
							.closest( 'fieldset, tr' )
							.nextUntil(
								'.hide_options_if_checked, .show_options_if_checked',
								'.hidden_option'
							)
							.show();
					}
				} )
				.trigger( 'change' );
		} );

		$( '.show_options_if_checked' ).each( function () {
			$( this )
				.find( 'input:eq(0)' )
				.on( 'change', function () {
					if ( $( this ).is( ':checked' ) ) {
						$( this )
							.closest( 'fieldset, tr' )
							.nextUntil(
								'.hide_options_if_checked, .show_options_if_checked',
								'.hidden_option'
							)
							.show();
					} else {
						$( this )
							.closest( 'fieldset, tr' )
							.nextUntil(
								'.hide_options_if_checked, .show_options_if_checked',
								'.hidden_option'
							)
							.hide();
					}
				} )
				.trigger( 'change' );
		} );

		// Reviews.
		$( 'input#woocommerce_enable_reviews' )
			.on( 'change', function () {
				if ( $( this ).is( ':checked' ) ) {
					$( '#woocommerce_enable_review_rating' )
						.closest( 'tr' )
						.show();
				} else {
					$( '#woocommerce_enable_review_rating' )
						.closest( 'tr' )
						.hide();
				}
			} )
			.trigger( 'change' );

		// Attribute term table
		$( 'table.attributes-table tbody tr:nth-child(odd)' ).addClass(
			'alternate'
		);

		// Toggle gateway on/off.
		$( '.wc_gateways' ).on(
			'click',
			'.wc-payment-gateway-method-toggle-enabled',
			function () {
				var $link = $( this ),
					$row = $link.closest( 'tr' ),
					$toggle = $link.find( '.woocommerce-input-toggle' );

				var data = {
					action: 'woocommerce_toggle_gateway_enabled',
					security: woocommerce_admin.nonces.gateway_toggle,
					gateway_id: $row.data( 'gateway_id' ),
				};

				$toggle.addClass( 'woocommerce-input-toggle--loading' );

				$.ajax( {
					url: woocommerce_admin.ajax_url,
					data: data,
					dataType: 'json',
					type: 'POST',
					success: function ( response ) {
						if ( true === response.data ) {
							$toggle.removeClass(
								'woocommerce-input-toggle--enabled, woocommerce-input-toggle--disabled'
							);
							$toggle.addClass(
								'woocommerce-input-toggle--enabled'
							);
							$toggle.removeClass(
								'woocommerce-input-toggle--loading'
							);
						} else if ( false === response.data ) {
							$toggle.removeClass(
								'woocommerce-input-toggle--enabled, woocommerce-input-toggle--disabled'
							);
							$toggle.addClass(
								'woocommerce-input-toggle--disabled'
							);
							$toggle.removeClass(
								'woocommerce-input-toggle--loading'
							);
						} else if ( 'needs_setup' === response.data ) {
							window.location.href = $link.attr( 'href' );
						}
					},
				} );

				return false;
			}
		);

		$( '#wpbody' ).on( 'click', '#doaction, #doaction2', function () {
			var action = $( this ).is( '#doaction' )
				? $( '#bulk-action-selector-top' ).val()
				: $( '#bulk-action-selector-bottom' ).val();

			if ( 'remove_personal_data' === action ) {
				return window.confirm(
					woocommerce_admin.i18n_remove_personal_data_notice
				);
			}
		} );

		var marketplaceSectionDropdown = $(
			'#marketplace-current-section-dropdown'
		);
		var marketplaceSectionName = $( '#marketplace-current-section-name' );
		var marketplaceMenuIsOpen = false;

		// Add event listener to toggle Marketplace menu on touch devices
		if ( marketplaceSectionDropdown.length ) {
			if ( isTouchDevice() ) {
				marketplaceSectionName.on( 'click', function () {
					marketplaceMenuIsOpen = ! marketplaceMenuIsOpen;
					if ( marketplaceMenuIsOpen ) {
						marketplaceSectionDropdown.addClass( 'is-open' );
						$( document ).on( 'click', maybeToggleMarketplaceMenu );
					} else {
						marketplaceSectionDropdown.removeClass( 'is-open' );
						$( document ).off(
							'click',
							maybeToggleMarketplaceMenu
						);
					}
				} );
			} else {
				document.body.classList.add( 'no-touch' );
			}
		}

		// Close menu if the user clicks outside it
		function maybeToggleMarketplaceMenu( e ) {
			if (
				! marketplaceSectionDropdown.is( e.target ) &&
				marketplaceSectionDropdown.has( e.target ).length === 0
			) {
				marketplaceSectionDropdown.removeClass( 'is-open' );
				marketplaceMenuIsOpen = false;
				$( document ).off( 'click', maybeToggleMarketplaceMenu );
			}
		}

		function isTouchDevice() {
			return (
				'ontouchstart' in window ||
				navigator.maxTouchPoints > 0 ||
				navigator.msMaxTouchPoints > 0
			);
		}
	} );
} )( jQuery, woocommerce_admin );
