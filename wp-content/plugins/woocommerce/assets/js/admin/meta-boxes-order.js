// eslint-disable-next-line max-len
/*global woocommerce_admin_meta_boxes, woocommerce_admin, accounting, woocommerce_admin_meta_boxes_order, wcSetClipboard, wcClearClipboard */
jQuery( function ( $ ) {

	// Stand-in wcTracks.recordEvent in case tracks is not available (for any reason).
	window.wcTracks = window.wcTracks || {};
	window.wcTracks.recordEvent = window.wcTracks.recordEvent  || function() { };

	/**
	 * Order Data Panel
	 */
	var wc_meta_boxes_order = {
		states: null,
		init: function() {
			if (
				! (
					typeof woocommerce_admin_meta_boxes_order === 'undefined' ||
					typeof woocommerce_admin_meta_boxes_order.countries === 'undefined'
				)
			) {
				/* State/Country select boxes */
				this.states = JSON.parse( woocommerce_admin_meta_boxes_order.countries.replace( /&quot;/g, '"' ) );
			}

			$( '.js_field-country' ).selectWoo().on( 'change', this.change_country );
			$( '.js_field-country' ).trigger( 'change', [ true ] );
			$( document.body ).on( 'change', 'select.js_field-state', this.change_state );
			$( '#woocommerce-order-actions input, #woocommerce-order-actions a' ).on( 'click', function() {
				window.onbeforeunload = '';
			});
			$( 'a.edit_address' ).on( 'click', this.edit_address );
			$( 'a.billing-same-as-shipping' ).on( 'click', this.copy_billing_to_shipping );
			$( 'a.load_customer_billing' ).on( 'click', this.load_billing );
			$( 'a.load_customer_shipping' ).on( 'click', this.load_shipping );
			$( '#customer_user' ).on( 'change', this.change_customer_user );
		},

		change_country: function( e, stickValue ) {
			// Check for stickValue before using it
			if ( typeof stickValue === 'undefined' ){
				stickValue = false;
			}

			// Prevent if we don't have the metabox data
			if ( wc_meta_boxes_order.states === null ){
				return;
			}

			var $this = $( this ),
				country = $this.val(),
				$state = $this.parents( 'div.edit_address' ).find( ':input.js_field-state' ),
				$parent = $state.parent(),
				stateValue = $state.val(),
				input_name = $state.attr( 'name' ),
				input_id = $state.attr( 'id' ),
				value = $this.data( 'woocommerce.stickState-' + country ) ? $this.data( 'woocommerce.stickState-' + country ) : stateValue,
				placeholder = $state.attr( 'placeholder' ),
				$newstate;

			if ( stickValue ){
				$this.data( 'woocommerce.stickState-' + country, value );
			}

			// Remove the previous DOM element
			$parent.show().find( '.select2-container' ).remove();

			if ( ! $.isEmptyObject( wc_meta_boxes_order.states[ country ] ) ) {
				var state = wc_meta_boxes_order.states[ country ],
					$defaultOption = $( '<option value=""></option>' )
						.text( woocommerce_admin_meta_boxes_order.i18n_select_state_text );

				$newstate = $( '<select></select>' )
					.prop( 'id', input_id )
					.prop( 'name', input_name )
					.prop( 'placeholder', placeholder )
					.addClass( 'js_field-state select short' )
					.append( $defaultOption );

				$.each( state, function( index ) {
					var $option = $( '<option></option>' )
						.prop( 'value', index )
						.text( state[ index ] );
					if ( index === stateValue ) {
						$option.prop( 'selected' );
					}
					$newstate.append( $option );
				} );

				$newstate.val( value );

				$state.replaceWith( $newstate );

				$newstate.show().selectWoo().hide().trigger( 'change' );
			} else {
				$newstate = $( '<input type="text" />' )
					.prop( 'id', input_id )
					.prop( 'name', input_name )
					.prop( 'placeholder', placeholder )
					.addClass( 'js_field-state' )
					.val( stateValue );
				$state.replaceWith( $newstate );
			}

			// This event has a typo - deprecated in 2.5.0
			$( document.body ).trigger( 'contry-change.woocommerce', [country, $( this ).closest( 'div' )] );
			$( document.body ).trigger( 'country-change.woocommerce', [country, $( this ).closest( 'div' )] );
		},

		change_state: function() {
			// Here we will find if state value on a select has changed and stick it to the country data
			var $this = $( this ),
				state = $this.val(),
				$country = $this.parents( 'div.edit_address' ).find( ':input.js_field-country' ),
				country = $country.val();

			$country.data( 'woocommerce.stickState-' + country, state );
		},

		init_tiptip: function() {
			$( '#tiptip_holder' ).removeAttr( 'style' );
			$( '#tiptip_arrow' ).removeAttr( 'style' );
			$( '.tips' ).tipTip({
				'attribute': 'data-tip',
				'fadeIn': 50,
				'fadeOut': 50,
				'delay': 200,
				'keepAlive': true
			});
		},

		edit_address: function( e ) {
			e.preventDefault();

			var $this          = $( this ),
				$wrapper       = $this.closest( '.order_data_column' ),
				$edit_address  = $wrapper.find( 'div.edit_address' ),
				$address       = $wrapper.find( 'div.address' ),
				$country_input = $edit_address.find( '.js_field-country' ),
				$state_input   = $edit_address.find( '.js_field-state' ),
				is_billing     = Boolean( $edit_address.find( 'input[name^="_billing_"]' ).length );

			$address.hide();
			$this.parent().find( 'a' ).toggle();

			if ( ! $country_input.val() ) {
				$country_input.val( woocommerce_admin_meta_boxes_order.default_country ).trigger( 'change' );
				$state_input.val( woocommerce_admin_meta_boxes_order.default_state ).trigger( 'change' );
			}

			$edit_address.show();

			var event_name = is_billing ? 'order_edit_billing_address_click' : 'order_edit_shipping_address_click';
			window.wcTracks.recordEvent( event_name, {
				order_id: woocommerce_admin_meta_boxes.post_id,
				status: $( '#order_status' ).val()
			} );
		},

		change_customer_user: function() {
			if ( ! $( '#_billing_country' ).val() ) {
				$( 'a.edit_address' ).trigger( 'click' );
				wc_meta_boxes_order.load_billing( true );
				wc_meta_boxes_order.load_shipping( true );
			}
		},

		load_billing: function( force ) {
			if ( true === force || window.confirm( woocommerce_admin_meta_boxes.load_billing ) ) {

				// Get user ID to load data for
				var user_id = $( '#customer_user' ).val();

				if ( ! user_id ) {
					window.alert( woocommerce_admin_meta_boxes.no_customer_selected );
					return false;
				}

				var data = {
					user_id : user_id,
					action  : 'woocommerce_get_customer_details',
					security: woocommerce_admin_meta_boxes.get_customer_details_nonce
				};

				$( this ).closest( 'div.edit_address' ).block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});

				$.ajax({
					url: woocommerce_admin_meta_boxes.ajax_url,
					data: data,
					type: 'POST',
					success: function( response ) {
						if ( response && response.billing ) {
							$.each( response.billing, function( key, data ) {
								$( ':input#_billing_' + key ).val( data ).trigger( 'change' );
							});
						}
						$( 'div.edit_address' ).unblock();
					}
				});
			}
			return false;
		},

		load_shipping: function( force ) {
			if ( true === force || window.confirm( woocommerce_admin_meta_boxes.load_shipping ) ) {

				// Get user ID to load data for
				var user_id = $( '#customer_user' ).val();

				if ( ! user_id ) {
					window.alert( woocommerce_admin_meta_boxes.no_customer_selected );
					return false;
				}

				var data = {
					user_id:      user_id,
					action:       'woocommerce_get_customer_details',
					security:     woocommerce_admin_meta_boxes.get_customer_details_nonce
				};

				$( this ).closest( 'div.edit_address' ).block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});

				$.ajax({
					url: woocommerce_admin_meta_boxes.ajax_url,
					data: data,
					type: 'POST',
					success: function( response ) {
						if ( response && response.billing ) {
							$.each( response.shipping, function( key, data ) {
								$( ':input#_shipping_' + key ).val( data ).trigger( 'change' );
							});
						}
						$( 'div.edit_address' ).unblock();
					}
				});
			}
			return false;
		},

		copy_billing_to_shipping: function() {
			if ( window.confirm( woocommerce_admin_meta_boxes.copy_billing ) ) {
				$('.order_data_column :input[name^="_billing_"]').each( function() {
					var input_name = $(this).attr('name');
					input_name     = input_name.replace( '_billing_', '_shipping_' );
					$( ':input#' + input_name ).val( $(this).val() ).trigger( 'change' );
				});
			}
			return false;
		}
	};

	/**
	 * Order Items Panel
	 */
	var wc_meta_boxes_order_items = {
		init: function() {
			this.stupidtable.init();

			$( '#woocommerce-order-items' )
				.on( 'click', 'button.add-line-item', this.add_line_item )
				.on( 'click', 'button.add-coupon', this.add_coupon )
				.on( 'click', 'a.remove-coupon', this.remove_coupon )
				.on( 'click', 'button.refund-items', this.refund_items )
				.on( 'click', '.cancel-action', this.cancel )
				.on( 'click', '.refund-actions .cancel-action', this.track_cancel )
				.on( 'click', 'button.add-order-item', this.add_item )
				.on( 'click', 'button.add-order-fee', this.add_fee )
				.on( 'click', 'button.add-order-shipping', this.add_shipping )
				.on( 'click', 'button.add-order-tax', this.add_tax )
				.on( 'click', 'button.save-action', this.save_line_items )
				.on( 'click', 'a.delete-order-tax', this.delete_tax )
				.on( 'click', 'button.calculate-action', this.recalculate )
				.on( 'click', 'a.edit-order-item', this.edit_item )
				.on( 'click', 'a.delete-order-item', this.delete_item )

				// Refunds
				.on( 'click', '.delete_refund', this.refunds.delete_refund )
				.on( 'click', 'button.do-api-refund, button.do-manual-refund', this.refunds.do_refund )
				.on( 'change', '.refund input.refund_line_total, .refund input.refund_line_tax', this.refunds.input_changed )
				.on( 'change keyup', '.wc-order-refund-items #refund_amount', this.refunds.amount_changed )
				.on( 'change', 'input.refund_order_item_qty', this.refunds.refund_quantity_changed )

				// Qty
				.on( 'change', 'input.quantity', this.quantity_changed )

				// Subtotal/total
				.on( 'keyup change', '.split-input :input', function() {
					var $subtotal = $( this ).parent().prev().find(':input');
					if ( $subtotal && ( $subtotal.val() === '' || $subtotal.is( '.match-total' ) ) ) {
						$subtotal.val( $( this ).val() ).addClass( 'match-total' );
					}
				})

				.on( 'keyup', '.split-input :input', function() {
					$( this ).removeClass( 'match-total' );
				})

				// Meta
				.on( 'click', 'button.add_order_item_meta', this.item_meta.add )
				.on( 'click', 'button.remove_order_item_meta', this.item_meta.remove )

				// Reload items
				.on( 'wc_order_items_reload', this.reload_items )
				.on( 'wc_order_items_reloaded', this.reloaded_items );

			$( document.body )
				.on( 'wc_backbone_modal_loaded', this.backbone.init )
				.on( 'wc_backbone_modal_response', this.backbone.response );
		},

		block: function() {
			$( '#woocommerce-order-items' ).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},

		unblock: function() {
			$( '#woocommerce-order-items' ).unblock();
		},

		filter_data: function( handle, data ) {
			const filteredData = $( '#woocommerce-order-items' )
				.triggerHandler(
					`woocommerce_order_meta_box_${handle}_ajax_data`,
					[ data ]
				);

			if ( filteredData ) {
				return filteredData;
			}

			return data;
		},

		reload_items: function() {
			var data = {
				order_id: woocommerce_admin_meta_boxes.post_id,
				action:   'woocommerce_load_order_items',
				security: woocommerce_admin_meta_boxes.order_item_nonce
			};

			data = wc_meta_boxes_order_items.filter_data( 'reload_items', data );

			wc_meta_boxes_order_items.block();

			$.ajax({
				url:  woocommerce_admin_meta_boxes.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					$( '#woocommerce-order-items' ).find( '.inside' ).empty();
					$( '#woocommerce-order-items' ).find( '.inside' ).append( response );
					wc_meta_boxes_order_items.reloaded_items();
					wc_meta_boxes_order_items.unblock();
				}
			});
		},

		reloaded_items: function() {
			wc_meta_boxes_order.init_tiptip();
			wc_meta_boxes_order_items.stupidtable.init();
		},

		// When the qty is changed, increase or decrease costs
		quantity_changed: function() {
			var $row          = $( this ).closest( 'tr.item' );
			var qty           = $( this ).val();
			var o_qty         = $( this ).attr( 'data-qty' );
			var line_total    = $( 'input.line_total', $row );
			var line_subtotal = $( 'input.line_subtotal', $row );

			// Totals
			var unit_total = accounting.unformat( line_total.attr( 'data-total' ), woocommerce_admin.mon_decimal_point ) / o_qty;
			line_total.val(
				parseFloat( accounting.formatNumber( unit_total * qty, woocommerce_admin_meta_boxes.rounding_precision, '' ) )
					.toString()
					.replace( '.', woocommerce_admin.mon_decimal_point )
			);

			var unit_subtotal = accounting.unformat( line_subtotal.attr( 'data-subtotal' ), woocommerce_admin.mon_decimal_point ) / o_qty;
			line_subtotal.val(
				parseFloat( accounting.formatNumber( unit_subtotal * qty, woocommerce_admin_meta_boxes.rounding_precision, '' ) )
					.toString()
					.replace( '.', woocommerce_admin.mon_decimal_point )
			);

			// Taxes
			$( 'input.line_tax', $row ).each( function() {
				var $line_total_tax    = $( this );
				var tax_id             = $line_total_tax.data( 'tax_id' );
				var unit_total_tax     = accounting.unformat(
					$line_total_tax.attr( 'data-total_tax' ),
					woocommerce_admin.mon_decimal_point
				) / o_qty;
				var $line_subtotal_tax = $( 'input.line_subtotal_tax[data-tax_id="' + tax_id + '"]', $row );
				var unit_subtotal_tax  = accounting.unformat(
					$line_subtotal_tax.attr( 'data-subtotal_tax' ),
					woocommerce_admin.mon_decimal_point
				) / o_qty;
				var round_at_subtotal  = 'yes' === woocommerce_admin_meta_boxes.round_at_subtotal;
				var precision          = woocommerce_admin_meta_boxes[
					round_at_subtotal ? 'rounding_precision' : 'currency_format_num_decimals'
					];

				if ( 0 < unit_total_tax ) {
					$line_total_tax.val(
						parseFloat( accounting.formatNumber( unit_total_tax * qty, precision, '' ) )
							.toString()
							.replace( '.', woocommerce_admin.mon_decimal_point )
					);
				}

				if ( 0 < unit_subtotal_tax ) {
					$line_subtotal_tax.val(
						parseFloat( accounting.formatNumber( unit_subtotal_tax * qty, precision, '' ) )
							.toString()
							.replace( '.', woocommerce_admin.mon_decimal_point )
					);
				}
			});

			$( this ).trigger( 'quantity_changed' );
		},

		add_line_item: function() {
			$( 'div.wc-order-add-item' ).slideDown();
			$( 'div.wc-order-data-row-toggle' ).not( 'div.wc-order-add-item' ).slideUp();

			window.wcTracks.recordEvent( 'order_edit_add_items_click', {
				order_id: woocommerce_admin_meta_boxes.post_id,
				status: $( '#order_status' ).val()
			} );

			return false;
		},

		add_coupon: function() {
			window.wcTracks.recordEvent( 'order_edit_add_coupon_click', {
				order_id: woocommerce_admin_meta_boxes.post_id,
				status: $( '#order_status' ).val()
			} );

			var value = window.prompt( woocommerce_admin_meta_boxes.i18n_apply_coupon );

			if ( null == value ) {
				window.wcTracks.recordEvent( 'order_edit_add_coupon_cancel', {
					order_id: woocommerce_admin_meta_boxes.post_id,
					status: $( '#order_status' ).val()
				} );
			} else {
				wc_meta_boxes_order_items.block();

				var user_id    = $( '#customer_user' ).val();
				var user_email = $( '#_billing_email' ).val();

				var data = $.extend( {}, wc_meta_boxes_order_items.get_taxable_address(), {
					action     : 'woocommerce_add_coupon_discount',
					dataType   : 'json',
					order_id   : woocommerce_admin_meta_boxes.post_id,
					security   : woocommerce_admin_meta_boxes.order_item_nonce,
					coupon     : value,
					user_id    : user_id,
					user_email : user_email
				} );

				data = wc_meta_boxes_order_items.filter_data( 'add_coupon', data );

				$.ajax( {
					url:     woocommerce_admin_meta_boxes.ajax_url,
					data:    data,
					type:    'POST',
					success: function( response ) {
						if ( response.success ) {
							$( '#woocommerce-order-items' ).find( '.inside' ).empty();
							$( '#woocommerce-order-items' ).find( '.inside' ).append( response.data.html );

							// Update notes.
							if ( response.data.notes_html ) {
								$( 'ul.order_notes' ).empty();
								$( 'ul.order_notes' ).append( $( response.data.notes_html ).find( 'li' ) );
							}

							wc_meta_boxes_order_items.reloaded_items();
							wc_meta_boxes_order_items.unblock();
						} else {
							window.alert( response.data.error );
						}
						wc_meta_boxes_order_items.unblock();
					},
					complete: function() {
						window.wcTracks.recordEvent( 'order_edit_added_coupon', {
							order_id: data.order_id,
							status: $( '#order_status' ).val()
						} );
					}
				} );
			}
			return false;
		},

		remove_coupon: function() {
			var $this = $( this );
			wc_meta_boxes_order_items.block();

			var data = $.extend( {}, wc_meta_boxes_order_items.get_taxable_address(), {
				action : 'woocommerce_remove_order_coupon',
				dataType : 'json',
				order_id : woocommerce_admin_meta_boxes.post_id,
				security : woocommerce_admin_meta_boxes.order_item_nonce,
				coupon : $this.data( 'code' )
			} );

			data = wc_meta_boxes_order_items.filter_data( 'remove_coupon', data );

			$.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {
				if ( response.success ) {
					$( '#woocommerce-order-items' ).find( '.inside' ).empty();
					$( '#woocommerce-order-items' ).find( '.inside' ).append( response.data.html );

					// Update notes.
					if ( response.data.notes_html ) {
						$( 'ul.order_notes' ).empty();
						$( 'ul.order_notes' ).append( $( response.data.notes_html ).find( 'li' ) );
					}

					wc_meta_boxes_order_items.reloaded_items();
					wc_meta_boxes_order_items.unblock();
				} else {
					window.alert( response.data.error );
				}
				wc_meta_boxes_order_items.unblock();
			});
		},

		refund_items: function() {
			$( 'div.wc-order-refund-items' ).slideDown();
			$( 'div.wc-order-data-row-toggle' ).not( 'div.wc-order-refund-items' ).slideUp();
			$( 'div.wc-order-totals-items' ).slideUp();
			$( '#woocommerce-order-items' ).find( 'div.refund' ).show();
			$( '.wc-order-edit-line-item .wc-order-edit-line-item-actions' ).hide();

			window.wcTracks.recordEvent( 'order_edit_refund_button_click', {
				order_id: woocommerce_admin_meta_boxes.post_id,
				status: $( '#order_status' ).val()
			} );

			return false;
		},

		cancel: function() {
			$( 'div.wc-order-data-row-toggle' ).not( 'div.wc-order-bulk-actions' ).slideUp();
			$( 'div.wc-order-bulk-actions' ).slideDown();
			$( 'div.wc-order-totals-items' ).slideDown();
			$( '#woocommerce-order-items' ).find( 'div.refund' ).hide();
			$( '.wc-order-edit-line-item .wc-order-edit-line-item-actions' ).show();

			// Reload the items
			if ( 'true' === $( this ).attr( 'data-reload' ) ) {
				wc_meta_boxes_order_items.reload_items();
			}

			window.wcTracks.recordEvent( 'order_edit_add_items_cancelled', {
				order_id: woocommerce_admin_meta_boxes.post_id,
				status: $( '#order_status' ).val()
			} );

			return false;
		},

		track_cancel: function() {
			window.wcTracks.recordEvent( 'order_edit_refund_cancel', {
				order_id: woocommerce_admin_meta_boxes.post_id,
				status: $( '#order_status' ).val()
			} );
		},

		add_item: function() {
			$( this ).WCBackboneModal({
				template: 'wc-modal-add-products'
			});

			return false;
		},

		add_fee: function() {
			window.wcTracks.recordEvent( 'order_edit_add_fee_click', {
				order_id: woocommerce_admin_meta_boxes.post_id,
				status: $( '#order_status' ).val()
			} );

			var value = window.prompt( woocommerce_admin_meta_boxes.i18n_add_fee );

			if ( null == value ) {
				window.wcTracks.recordEvent( 'order_edit_add_fee_cancel', {
					order_id: woocommerce_admin_meta_boxes.post_id,
					status: $( '#order_status' ).val()
				} );
			} else {
				wc_meta_boxes_order_items.block();

				var data = $.extend( {}, wc_meta_boxes_order_items.get_taxable_address(), {
					action  : 'woocommerce_add_order_fee',
					dataType: 'json',
					order_id: woocommerce_admin_meta_boxes.post_id,
					security: woocommerce_admin_meta_boxes.order_item_nonce,
					amount  : value
				} );

				data = wc_meta_boxes_order_items.filter_data( 'add_fee', data );

				$.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {
					if ( response.success ) {
						$( '#woocommerce-order-items' ).find( '.inside' ).empty();
						$( '#woocommerce-order-items' ).find( '.inside' ).append( response.data.html );
						wc_meta_boxes_order_items.reloaded_items();
						wc_meta_boxes_order_items.unblock();
						window.wcTracks.recordEvent( 'order_edit_added_fee', {
							order_id: woocommerce_admin_meta_boxes.post_id,
							status: $( '#order_status' ).val()
						} );
					} else {
						window.alert( response.data.error );
					}
					wc_meta_boxes_order_items.unblock();
				});
			}
			return false;
		},

		add_shipping: function() {
			wc_meta_boxes_order_items.block();

			var data = {
				action   : 'woocommerce_add_order_shipping',
				order_id : woocommerce_admin_meta_boxes.post_id,
				security : woocommerce_admin_meta_boxes.order_item_nonce,
				dataType : 'json'
			};

			data = wc_meta_boxes_order_items.filter_data( 'add_shipping', data );

			$.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {
				if ( response.success ) {
					$( 'table.woocommerce_order_items tbody#order_shipping_line_items' ).append( response.data.html );
					window.wcTracks.recordEvent( 'order_edit_add_shipping', {
						order_id: woocommerce_admin_meta_boxes.post_id,
						status: $( '#order_status' ).val()
					} );
				} else {
					window.alert( response.data.error );
				}
				wc_meta_boxes_order_items.unblock();
			});

			return false;
		},

		add_tax: function() {
			$( this ).WCBackboneModal({
				template: 'wc-modal-add-tax'
			});
			return false;
		},

		edit_item: function() {
			$( this ).closest( 'tr' ).find( '.view' ).hide();
			$( this ).closest( 'tr' ).find( '.edit' ).show();
			$( this ).hide();
			$( 'button.add-line-item' ).trigger( 'click' );
			$( 'button.cancel-action' ).attr( 'data-reload', true );
			window.wcTracks.recordEvent( 'order_edit_edit_item_click', {
				order_id: woocommerce_admin_meta_boxes.post_id,
				status: $( '#order_status' ).val()
			} );
			return false;
		},

		delete_item: function() {
			var notice = woocommerce_admin_meta_boxes.remove_item_notice;

			if ( $( this ).parents( 'tbody#order_fee_line_items' ).length ) {
				notice = woocommerce_admin_meta_boxes.remove_fee_notice;
			}

			if ( $( this ).parents( 'tbody#order_shipping_line_items' ).length ) {
				notice = woocommerce_admin_meta_boxes.remove_shipping_notice;
			}

			var answer = window.confirm( notice );

			if ( answer ) {
				var $item         = $( this ).closest( 'tr.item, tr.fee, tr.shipping' );
				var order_item_id = $item.attr( 'data-order_item_id' );

				wc_meta_boxes_order_items.block();

				var data = $.extend( {}, wc_meta_boxes_order_items.get_taxable_address(), {
					order_id      : woocommerce_admin_meta_boxes.post_id,
					order_item_ids: order_item_id,
					action        : 'woocommerce_remove_order_item',
					security      : woocommerce_admin_meta_boxes.order_item_nonce
				} );

				// Check if items have changed, if so pass them through so we can save them before deleting.
				if ( 'true' === $( 'button.cancel-action' ).attr( 'data-reload' ) ) {
					data.items = $( 'table.woocommerce_order_items :input[name], .wc-order-totals-items :input[name]' ).serialize();
				}

				data = wc_meta_boxes_order_items.filter_data( 'delete_item', data );

				$.ajax({
					url:     woocommerce_admin_meta_boxes.ajax_url,
					data:    data,
					type:    'POST',
					success: function( response ) {
						if ( response.success ) {
							$( '#woocommerce-order-items' ).find( '.inside' ).empty();
							$( '#woocommerce-order-items' ).find( '.inside' ).append( response.data.html );

							// Update notes.
							if ( response.data.notes_html ) {
								$( 'ul.order_notes' ).empty();
								$( 'ul.order_notes' ).append( $( response.data.notes_html ).find( 'li' ) );
							}

							wc_meta_boxes_order_items.reloaded_items();
							wc_meta_boxes_order_items.unblock();
						} else {
							window.alert( response.data.error );
						}
						wc_meta_boxes_order_items.unblock();
					},
					complete: function() {
						window.wcTracks.recordEvent( 'order_edit_remove_item', {
							order_id: woocommerce_admin_meta_boxes.post_id,
							status: $( '#order_status' ).val()
						} );
					}
				});
			}
			return false;
		},

		delete_tax: function() {
			if ( window.confirm( woocommerce_admin_meta_boxes.i18n_delete_tax ) ) {
				wc_meta_boxes_order_items.block();

				var data = {
					action:   'woocommerce_remove_order_tax',
					rate_id:  $( this ).attr( 'data-rate_id' ),
					order_id: woocommerce_admin_meta_boxes.post_id,
					security: woocommerce_admin_meta_boxes.order_item_nonce
				};

				data = wc_meta_boxes_order_items.filter_data( 'delete_tax', data );

				$.ajax({
					url:  woocommerce_admin_meta_boxes.ajax_url,
					data: data,
					type: 'POST',
					success: function( response ) {
						if ( response.success ) {
							$( '#woocommerce-order-items' ).find( '.inside' ).empty();
							$( '#woocommerce-order-items' ).find( '.inside' ).append( response.data.html );
							wc_meta_boxes_order_items.reloaded_items();
							wc_meta_boxes_order_items.unblock();
						} else {
							window.alert( response.data.error );
						}
						wc_meta_boxes_order_items.unblock();
					},
					complete: function() {
						window.wcTracks.recordEvent( 'order_edit_delete_tax', {
							order_id: data.order_id,
							status: $( '#order_status' ).val()
						} );
					}
				});
			} else {
				window.wcTracks.recordEvent( 'order_edit_delete_tax_cancel', {
					order_id: woocommerce_admin_meta_boxes.post_id,
					status: $( '#order_status' ).val()
				} );
			}
			return false;
		},

		get_taxable_address: function() {
			var country          = '';
			var state            = '';
			var postcode         = '';
			var city             = '';

			if ( 'shipping' === woocommerce_admin_meta_boxes.tax_based_on ) {
				country  = $( '#_shipping_country' ).val();
				state    = $( '#_shipping_state' ).val();
				postcode = $( '#_shipping_postcode' ).val();
				city     = $( '#_shipping_city' ).val();
			}

			if ( 'billing' === woocommerce_admin_meta_boxes.tax_based_on || ! country ) {
				country  = $( '#_billing_country' ).val();
				state    = $( '#_billing_state' ).val();
				postcode = $( '#_billing_postcode' ).val();
				city     = $( '#_billing_city' ).val();
			}

			return {
				country:  country,
				state:    state,
				postcode: postcode,
				city:     city
			};
		},

		recalculate: function() {
			if ( window.confirm( woocommerce_admin_meta_boxes.calc_totals ) ) {
				wc_meta_boxes_order_items.block();

				var data = $.extend( {}, wc_meta_boxes_order_items.get_taxable_address(), {
					action:   'woocommerce_calc_line_taxes',
					order_id: woocommerce_admin_meta_boxes.post_id,
					items:    $( 'table.woocommerce_order_items :input[name], .wc-order-totals-items :input[name]' ).serialize(),
					security: woocommerce_admin_meta_boxes.calc_totals_nonce
				} );

				data = wc_meta_boxes_order_items.filter_data( 'recalculate', data );

				$( document.body ).trigger( 'order-totals-recalculate-before', data );

				$.ajax({
					url:  woocommerce_admin_meta_boxes.ajax_url,
					data: data,
					type: 'POST',
					success: function( response ) {
						$( '#woocommerce-order-items' ).find( '.inside' ).empty();
						$( '#woocommerce-order-items' ).find( '.inside' ).append( response );
						wc_meta_boxes_order_items.reloaded_items();
						wc_meta_boxes_order_items.unblock();

						$( document.body ).trigger( 'order-totals-recalculate-success', response );
					},
					complete: function( response ) {
						$( document.body ).trigger( 'order-totals-recalculate-complete', response );

						window.wcTracks.recordEvent( 'order_edit_recalc_totals', {
							order_id: woocommerce_admin_meta_boxes.post_id,
							ok_cancel: 'OK',
							status: $( '#order_status' ).val()
						} );
					}
				});
			} else {
				window.wcTracks.recordEvent( 'order_edit_recalc_totals', {
					order_id: woocommerce_admin_meta_boxes.post_id,
					ok_cancel: 'cancel',
					status: $( '#order_status' ).val()
				} );
			}

			return false;
		},

		save_line_items: function() {
			var data = {
				order_id: woocommerce_admin_meta_boxes.post_id,
				items:    $( 'table.woocommerce_order_items :input[name], .wc-order-totals-items :input[name]' ).serialize(),
				action:   'woocommerce_save_order_items',
				security: woocommerce_admin_meta_boxes.order_item_nonce
			};

			data = wc_meta_boxes_order_items.filter_data( 'save_line_items', data );

			wc_meta_boxes_order_items.block();

			$.ajax({
				url:  woocommerce_admin_meta_boxes.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					if ( response.success ) {
						$( '#woocommerce-order-items' ).find( '.inside' ).empty();
						$( '#woocommerce-order-items' ).find( '.inside' ).append( response.data.html );

						// Update notes.
						if ( response.data.notes_html ) {
							$( 'ul.order_notes' ).empty();
							$( 'ul.order_notes' ).append( $( response.data.notes_html ).find( 'li' ) );
						}

						wc_meta_boxes_order_items.reloaded_items();
						wc_meta_boxes_order_items.unblock();
					} else {
						wc_meta_boxes_order_items.unblock();
						window.alert( response.data.error );
					}
				},
				complete: function() {
					window.wcTracks.recordEvent( 'order_edit_save_line_items', {
						order_id: woocommerce_admin_meta_boxes.post_id,
						status: $( '#order_status' ).val()
					} );
				}
			});

			$( this ).trigger( 'items_saved' );

			return false;
		},

		refunds: {

			do_refund: function() {
				wc_meta_boxes_order_items.block();

				if ( window.confirm( woocommerce_admin_meta_boxes.i18n_do_refund ) ) {
					var refund_amount   = $( 'input#refund_amount' ).val();
					var refund_reason   = $( 'input#refund_reason' ).val();
					var refunded_amount = $( 'input#refunded_amount' ).val();

					// Get line item refunds
					var line_item_qtys       = {};
					var line_item_totals     = {};
					var line_item_tax_totals = {};

					$( '.refund input.refund_order_item_qty' ).each(function( index, item ) {
						if ( $( item ).closest( 'tr' ).data( 'order_item_id' ) ) {
							if ( item.value ) {
								line_item_qtys[ $( item ).closest( 'tr' ).data( 'order_item_id' ) ] = item.value;
							}
						}
					});

					$( '.refund input.refund_line_total' ).each(function( index, item ) {
						if ( $( item ).closest( 'tr' ).data( 'order_item_id' ) ) {
							line_item_totals[ $( item ).closest( 'tr' ).data( 'order_item_id' ) ] = accounting.unformat(
								item.value,
								woocommerce_admin.mon_decimal_point
							);
						}
					});

					$( '.refund input.refund_line_tax' ).each(function( index, item ) {
						if ( $( item ).closest( 'tr' ).data( 'order_item_id' ) ) {
							var tax_id = $( item ).data( 'tax_id' );

							if ( ! line_item_tax_totals[ $( item ).closest( 'tr' ).data( 'order_item_id' ) ] ) {
								line_item_tax_totals[ $( item ).closest( 'tr' ).data( 'order_item_id' ) ] = {};
							}

							line_item_tax_totals[ $( item ).closest( 'tr' ).data( 'order_item_id' ) ][ tax_id ] = accounting.unformat(
								item.value,
								woocommerce_admin.mon_decimal_point
							);
						}
					});

					var data = {
						action                : 'woocommerce_refund_line_items',
						order_id              : woocommerce_admin_meta_boxes.post_id,
						refund_amount         : refund_amount,
						refunded_amount       : refunded_amount,
						refund_reason         : refund_reason,
						line_item_qtys        : JSON.stringify( line_item_qtys, null, '' ),
						line_item_totals      : JSON.stringify( line_item_totals, null, '' ),
						line_item_tax_totals  : JSON.stringify( line_item_tax_totals, null, '' ),
						api_refund            : $( this ).is( '.do-api-refund' ),
						restock_refunded_items: $( '#restock_refunded_items:checked' ).length ? 'true': 'false',
						security              : woocommerce_admin_meta_boxes.order_item_nonce
					};

					data = wc_meta_boxes_order_items.filter_data( 'do_refund', data );

					$.ajax( {
						url:     woocommerce_admin_meta_boxes.ajax_url,
						data:    data,
						type:    'POST',
						success: function( response ) {
							if ( true === response.success ) {
								// Redirect to same page for show the refunded status
								window.location.reload();
							} else {
								window.alert( response.data.error );
								wc_meta_boxes_order_items.reload_items();
								wc_meta_boxes_order_items.unblock();
							}
						},
						complete: function() {
							window.wcTracks.recordEvent( 'order_edit_refunded', {
								order_id: data.order_id,
								status: $( '#order_status' ).val(),
								api_refund: data.api_refund,
								has_reason: Boolean( data.refund_reason.length ),
								restock: 'true' === data.restock_refunded_items
							} );
						}
					} );
				} else {
					wc_meta_boxes_order_items.unblock();
				}
			},

			delete_refund: function() {
				if ( window.confirm( woocommerce_admin_meta_boxes.i18n_delete_refund ) ) {
					var $refund   = $( this ).closest( 'tr.refund' );
					var refund_id = $refund.attr( 'data-order_refund_id' );

					wc_meta_boxes_order_items.block();

					var data = {
						action:    'woocommerce_delete_refund',
						refund_id: refund_id,
						security:  woocommerce_admin_meta_boxes.order_item_nonce
					};

					data = wc_meta_boxes_order_items.filter_data( 'delete_refund', data );

					$.ajax({
						url:     woocommerce_admin_meta_boxes.ajax_url,
						data:    data,
						type:    'POST',
						success: function() {
							wc_meta_boxes_order_items.reload_items();
						}
					});
				}
				return false;
			},

			input_changed: function() {
				var refund_amount     = 0;
				var $items            = $( '.woocommerce_order_items' ).find( 'tr.item, tr.fee, tr.shipping' );
				var round_at_subtotal = 'yes' === woocommerce_admin_meta_boxes.round_at_subtotal;

				$items.each(function() {
					var $row               = $( this );
					var refund_cost_fields = $row.find( '.refund input:not(.refund_order_item_qty)' );

					refund_cost_fields.each(function( index, el ) {
						var field_amount = accounting.unformat( $( el ).val() || 0, woocommerce_admin.mon_decimal_point );
						refund_amount += parseFloat( round_at_subtotal ?
							field_amount :
							accounting.formatNumber( field_amount, woocommerce_admin_meta_boxes.currency_format_num_decimals, '' ) );
					});
				});

				$( '#refund_amount' )
					.val( accounting.formatNumber(
						refund_amount,
						woocommerce_admin_meta_boxes.currency_format_num_decimals,
						'',
						woocommerce_admin.mon_decimal_point
					) )
					.trigger( 'change' );
			},

			amount_changed: function() {
				var total = accounting.unformat( $( this ).val(), woocommerce_admin.mon_decimal_point );

				$( 'button .wc-order-refund-amount .amount' ).text( accounting.formatMoney( total, {
					symbol:    woocommerce_admin_meta_boxes.currency_format_symbol,
					decimal:   woocommerce_admin_meta_boxes.currency_format_decimal_sep,
					thousand:  woocommerce_admin_meta_boxes.currency_format_thousand_sep,
					precision: woocommerce_admin_meta_boxes.currency_format_num_decimals,
					format:    woocommerce_admin_meta_boxes.currency_format
				} ) );
			},

			// When the refund qty is changed, increase or decrease costs
			refund_quantity_changed: function() {
				var $row              = $( this ).closest( 'tr.item' );
				var qty               = $row.find( 'input.quantity' ).val();
				var refund_qty        = $( this ).val();
				var line_total        = $( 'input.line_total', $row );
				var refund_line_total = $( 'input.refund_line_total', $row );

				// Totals
				var unit_total = accounting.unformat( line_total.attr( 'data-total' ), woocommerce_admin.mon_decimal_point ) / qty;

				refund_line_total.val(
					parseFloat( accounting.formatNumber( unit_total * refund_qty, woocommerce_admin_meta_boxes.rounding_precision, '' ) )
						.toString()
						.replace( '.', woocommerce_admin.mon_decimal_point )
				).trigger( 'change' );

				// Taxes
				$( '.refund_line_tax', $row ).each( function() {
					var $refund_line_total_tax = $( this );
					var tax_id                 = $refund_line_total_tax.data( 'tax_id' );
					var line_total_tax         = $( 'input.line_tax[data-tax_id="' + tax_id + '"]', $row );
					var unit_total_tax         = accounting.unformat(
						line_total_tax.data( 'total_tax' ),
						woocommerce_admin.mon_decimal_point
					) / qty;

					if ( 0 < unit_total_tax ) {
						var round_at_subtotal = 'yes' === woocommerce_admin_meta_boxes.round_at_subtotal;
						var precision         = woocommerce_admin_meta_boxes[
							round_at_subtotal ? 'rounding_precision' : 'currency_format_num_decimals'
							];

						$refund_line_total_tax.val(
							parseFloat( accounting.formatNumber( unit_total_tax * refund_qty, precision, '' ) )
								.toString()
								.replace( '.', woocommerce_admin.mon_decimal_point )
						).trigger( 'change' );
					} else {
						$refund_line_total_tax.val( 0 ).trigger( 'change' );
					}
				});

				// Restock checkbox
				if ( refund_qty > 0 ) {
					$( '#restock_refunded_items' ).closest( 'tr' ).show();
				} else {
					$( '#restock_refunded_items' ).closest( 'tr' ).hide();
					$( '.woocommerce_order_items input.refund_order_item_qty' ).each( function() {
						if ( $( this ).val() > 0 ) {
							$( '#restock_refunded_items' ).closest( 'tr' ).show();
						}
					});
				}

				$( this ).trigger( 'refund_quantity_changed' );
			}
		},

		item_meta: {

			add: function() {
				var $button = $( this );
				var $item = $button.closest( 'tr.item, tr.shipping' );
				var $items = $item.find('tbody.meta_items');
				var index  = $items.find('tr').length + 1;
				var $row   = '<tr data-meta_id="0">' +
					'<td>' +
					'<input type="text" maxlength="255" placeholder="' +
					woocommerce_admin_meta_boxes_order.placeholder_name +
					'" name="meta_key[' + $item.attr( 'data-order_item_id' ) +
					'][new-' + index + ']" />' +
					'<textarea placeholder="' +
					woocommerce_admin_meta_boxes_order.placeholder_value +
					'" name="meta_value[' +
					$item.attr( 'data-order_item_id' ) +
					'][new-' +
					index +
					']"></textarea>' +
					'</td>' +
					'<td width="1%"><button class="remove_order_item_meta button">&times;</button></td>' +
					'</tr>';
				$items.append( $row );

				return false;
			},

			remove: function() {
				if ( window.confirm( woocommerce_admin_meta_boxes.remove_item_meta ) ) {
					var $row = $( this ).closest( 'tr' );
					$row.find( ':input' ).val( '' );
					$row.hide();
				}
				return false;
			}
		},

		backbone: {

			init: function( e, target ) {
				if ( 'wc-modal-add-products' === target ) {
					$( document.body ).trigger( 'wc-enhanced-select-init' );

					$( this ).on( 'change', '.wc-product-search', function() {
						if ( ! $( this ).closest( 'tr' ).is( ':last-child' ) ) {
							return;
						}
						var item_table      = $( this ).closest( 'table.widefat' ),
							item_table_body = item_table.find( 'tbody' ),
							index           = item_table_body.find( 'tr' ).length,
							row             = item_table_body.data( 'row' ).replace( /\[0\]/g, '[' + index + ']' );

						item_table_body.append( '<tr>' + row + '</tr>' );
						$( document.body ).trigger( 'wc-enhanced-select-init' );
					} );
				}
			},

			response: function( e, target, data ) {
				if ( 'wc-modal-add-tax' === target ) {
					var rate_id = data.add_order_tax;
					var manual_rate_id = '';

					if ( data.manual_tax_rate_id ) {
						manual_rate_id = data.manual_tax_rate_id;
					}

					wc_meta_boxes_order_items.backbone.add_tax( rate_id, manual_rate_id );
				}
				if ( 'wc-modal-add-products' === target ) {
					// Build array of data.
					var item_table      = $( this ).find( 'table.widefat' ),
						item_table_body = item_table.find( 'tbody' ),
						rows            = item_table_body.find( 'tr' ),
						add_items       = [];

					$( rows ).each( function() {
						var item_id = $( this ).find( ':input[name="item_id"]' ).val(),
							item_qty = $( this ).find( ':input[name="item_qty"]' ).val();

						add_items.push( {
							'id' : item_id,
							'qty': item_qty ? item_qty: 1
						} );
					} );

					return wc_meta_boxes_order_items.backbone.add_items( add_items );
				}
			},

			add_items: function( add_items ) {
				wc_meta_boxes_order_items.block();

				var data = {
					action   : 'woocommerce_add_order_item',
					order_id : woocommerce_admin_meta_boxes.post_id,
					security : woocommerce_admin_meta_boxes.order_item_nonce,
					data     : add_items
				};

				// Check if items have changed, if so pass them through so we can save them before adding a new item.
				if ( 'true' === $( 'button.cancel-action' ).attr( 'data-reload' ) ) {
					data.items = $( 'table.woocommerce_order_items :input[name], .wc-order-totals-items :input[name]' ).serialize();
				}

				data = wc_meta_boxes_order_items.filter_data( 'add_items', data );

				$.ajax({
					type: 'POST',
					url: woocommerce_admin_meta_boxes.ajax_url,
					data: data,
					success: function( response ) {
						if ( response.success ) {
							$( '#woocommerce-order-items' ).find( '.inside' ).empty();
							$( '#woocommerce-order-items' ).find( '.inside' ).append( response.data.html );

							// Update notes.
							if ( response.data.notes_html ) {
								$( 'ul.order_notes' ).empty();
								$( 'ul.order_notes' ).append( $( response.data.notes_html ).find( 'li' ) );
							}

							wc_meta_boxes_order_items.reloaded_items();
							wc_meta_boxes_order_items.unblock();
						} else {
							wc_meta_boxes_order_items.unblock();
							window.alert( response.data.error );
						}
					},
					complete: function() {
						window.wcTracks.recordEvent( 'order_edit_add_products', {
							order_id: woocommerce_admin_meta_boxes.post_id,
							status: $( '#order_status' ).val()
						} );
					},
					dataType: 'json'
				});
			},

			add_tax: function( rate_id, manual_rate_id ) {
				if ( manual_rate_id ) {
					rate_id = manual_rate_id;
				}

				if ( ! rate_id ) {
					return false;
				}

				var rates = $( '.order-tax-id' ).map( function() {
					return $( this ).val();
				}).get();

				// Test if already exists
				if ( -1 === $.inArray( rate_id, rates ) ) {
					wc_meta_boxes_order_items.block();

					var data = {
						action:   'woocommerce_add_order_tax',
						rate_id:  rate_id,
						order_id: woocommerce_admin_meta_boxes.post_id,
						security: woocommerce_admin_meta_boxes.order_item_nonce
					};

					data = wc_meta_boxes_order_items.filter_data( 'add_tax', data );

					$.ajax({
						url      : woocommerce_admin_meta_boxes.ajax_url,
						data     : data,
						dataType : 'json',
						type     : 'POST',
						success  : function( response ) {
							if ( response.success ) {
								$( '#woocommerce-order-items' ).find( '.inside' ).empty();
								$( '#woocommerce-order-items' ).find( '.inside' ).append( response.data.html );
								wc_meta_boxes_order_items.reloaded_items();
							} else {
								window.alert( response.data.error );
							}
							wc_meta_boxes_order_items.unblock();
						},
						complete: function() {
							window.wcTracks.recordEvent( 'order_edit_add_tax', {
								order_id: woocommerce_admin_meta_boxes.post_id,
								status: $( '#order_status' ).val()
							} );
						}
					});
				} else {
					window.alert( woocommerce_admin_meta_boxes.i18n_tax_rate_already_exists );
				}
			}
		},

		stupidtable: {
			init: function() {
				$( '.woocommerce_order_items' ).stupidtable();
				$( '.woocommerce_order_items' ).on( 'aftertablesort', this.add_arrows );
			},

			add_arrows: function( event, data ) {
				var th    = $( this ).find( 'th' );
				var arrow = data.direction === 'asc' ? '&uarr;' : '&darr;';
				var index = data.column;
				th.find( '.wc-arrow' ).remove();
				th.eq( index ).append( '<span class="wc-arrow">' + arrow + '</span>' );
			}
		}
	};

	/**
	 * Order Notes Panel
	 */
	var wc_meta_boxes_order_notes = {
		init: function() {
			$( '#woocommerce-order-notes' )
				.on( 'click', 'button.add_note', this.add_order_note )
				.on( 'click', 'a.delete_note', this.delete_order_note );

		},

		add_order_note: function() {
			if ( ! $( 'textarea#add_order_note' ).val() ) {
				return;
			}

			$( '#woocommerce-order-notes' ).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});

			var data = {
				action:    'woocommerce_add_order_note',
				post_id:   woocommerce_admin_meta_boxes.post_id,
				note:      $( 'textarea#add_order_note' ).val(),
				note_type: $( 'select#order_note_type' ).val(),
				security:  woocommerce_admin_meta_boxes.add_order_note_nonce
			};

			$.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {
				$( 'ul.order_notes .no-items' ).remove();
				$( 'ul.order_notes' ).prepend( response );
				$( '#woocommerce-order-notes' ).unblock();
				$( '#add_order_note' ).val( '' );
				window.wcTracks.recordEvent( 'order_edit_add_order_note', {
					order_id: woocommerce_admin_meta_boxes.post_id,
					note_type: data.note_type || 'private',
					status: $( '#order_status' ).val()
				} );
			});

			return false;
		},

		delete_order_note: function() {
			if ( window.confirm( woocommerce_admin_meta_boxes.i18n_delete_note ) ) {
				var note = $( this ).closest( 'li.note' );

				$( note ).block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});

				var data = {
					action:   'woocommerce_delete_order_note',
					note_id:  $( note ).attr( 'rel' ),
					security: woocommerce_admin_meta_boxes.delete_order_note_nonce
				};

				$.post( woocommerce_admin_meta_boxes.ajax_url, data, function() {
					$( note ).remove();
				});
			}

			return false;
		}
	};

	/**
	 * Order Downloads Panel
	 */
	var wc_meta_boxes_order_downloads = {
		init: function() {
			$( '.order_download_permissions' )
				.on( 'click', 'button.grant_access', this.grant_access )
				.on( 'click', 'button.revoke_access', this.revoke_access )
				.on( 'click', '#copy-download-link', this.copy_link )
				.on( 'aftercopy', '#copy-download-link', this.copy_success )
				.on( 'aftercopyfailure', '#copy-download-link', this.copy_fail );
		},

		grant_access: function() {
			var products = $( '#grant_access_id' ).val();

			if ( ! products ) {
				return;
			}

			$( '.order_download_permissions' ).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});

			var data = {
				action:      'woocommerce_grant_access_to_download',
				product_ids: products,
				loop:        $('.order_download_permissions .wc-metabox').length,
				order_id:    woocommerce_admin_meta_boxes.post_id,
				security:    woocommerce_admin_meta_boxes.grant_access_nonce
			};

			$.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {

				if ( response ) {
					$( '.order_download_permissions .wc-metaboxes' ).append( response );
				} else {
					window.alert( woocommerce_admin_meta_boxes.i18n_download_permission_fail );
				}

				$( document.body ).trigger( 'wc-init-datepickers' );
				$( '#grant_access_id' ).val( '' ).trigger( 'change' );
				$( '.order_download_permissions' ).unblock();
			});

			return false;
		},

		revoke_access: function () {
			if ( window.confirm( woocommerce_admin_meta_boxes.i18n_permission_revoke ) ) {
				var el            = $( this ).parent().parent();
				var product       = $( this ).attr( 'rel' ).split( ',' )[0];
				var file          = $( this ).attr( 'rel' ).split( ',' )[1];
				var permission_id = $( this ).data( 'permission_id' );

				if ( product > 0 ) {
					$( el ).block({
						message: null,
						overlayCSS: {
							background: '#fff',
							opacity: 0.6
						}
					});

					var data = {
						action:        'woocommerce_revoke_access_to_download',
						product_id:    product,
						download_id:   file,
						permission_id: permission_id,
						order_id:      woocommerce_admin_meta_boxes.post_id,
						security:      woocommerce_admin_meta_boxes.revoke_access_nonce
					};

					$.post( woocommerce_admin_meta_boxes.ajax_url, data, function() {
						// Success
						$( el ).fadeOut( '300', function () {
							$( el ).remove();
						});
					});

				} else {
					$( el ).fadeOut( '300', function () {
						$( el ).remove();
					});
				}
			}
			return false;
		},

		/**
		 * Copy download link.
		 *
		 * @param {Object} evt Copy event.
		 */
		copy_link: function( evt ) {
			wcClearClipboard();
			wcSetClipboard( $( this ).attr( 'href' ), $( this ) );
			evt.preventDefault();
		},

		/**
		 * Display a "Copied!" tip when success copying
		 */
		copy_success: function() {
			$( this ).tipTip({
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
		copy_fail: function() {
			$( this ).tipTip({
				'attribute':  'data-tip-failed',
				'activation': 'focus',
				'fadeIn':     50,
				'fadeOut':    50,
				'delay':      0
			}).trigger( 'focus' );
		}
	};

	/**
	 * Configures ajax request for custom metadata box in order edit screen.
	 */
	var wc_meta_boxes_order_custom_meta = {
		init: function() {
			if ( ! $('#order_custom').length ) {
				return;
			}

			$( '#order_custom #the-list' ).wpList( {
				/**
				 * Add order id and action to the request.
				 */
				addBefore: function( settings ) {
					settings.data += "&order_id=" + woocommerce_admin_meta_boxes.post_id + "&action=woocommerce_order_add_meta";
					return settings;
				},

				addAfter: function() {
					$('table#list-table').show();
				},

				delBefore: function( settings ) {
					settings.data.order_id = woocommerce_admin_meta_boxes.post_id;
					settings.data.action   = 'woocommerce_order_delete_meta';
					return settings;
				}
			});
		}
	};

	wc_meta_boxes_order.init();
	wc_meta_boxes_order_items.init();
	wc_meta_boxes_order_notes.init();
	wc_meta_boxes_order_downloads.init();
	wc_meta_boxes_order_custom_meta.init();
});
