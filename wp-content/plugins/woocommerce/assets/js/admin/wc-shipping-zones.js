/* global shippingZonesLocalizeScript, ajaxurl */
( function( $, data, wp, ajaxurl ) {
	$( function() {
		var $table          = $( '.wc-shipping-zones' ),
			$tbody          = $( '.wc-shipping-zone-rows' ),
			$save_button    = $( '.wc-shipping-zone-save' ),
			$row_template   = wp.template( 'wc-shipping-zone-row' ),
			$blank_template = wp.template( 'wc-shipping-zone-row-blank' ),

			// Backbone model
			ShippingZone       = Backbone.Model.extend({
				changes: {},
				logChanges: function( changedRows ) {
					var changes = this.changes || {};

					_.each( changedRows, function( row, id ) {
						changes[ id ] = _.extend( changes[ id ] || { zone_id : id }, row );
					} );

					this.changes = changes;
					this.trigger( 'change:zones' );
				},
				discardChanges: function( id ) {
					var changes      = this.changes || {},
						set_position = null,
						zones        = _.indexBy( this.get( 'zones' ), 'zone_id' );

					// Find current set position if it has moved since last save
					if ( changes[ id ] && changes[ id ].zone_order !== undefined ) {
						set_position = changes[ id ].zone_order;
					}

					// Delete all changes
					delete changes[ id ];

					// If the position was set, and this zone does exist in DB, set the position again so the changes are not lost.
					if ( set_position !== null && zones[ id ] && zones[ id ].zone_order !== set_position ) {
						changes[ id ] = _.extend( changes[ id ] || {}, { zone_id : id, zone_order : set_position } );
					}

					this.changes = changes;

					// No changes? Disable save button.
					if ( 0 === _.size( this.changes ) ) {
						shippingZoneView.clearUnloadConfirmation();
					}
				},
				save: function() {
					if ( _.size( this.changes ) ) {
						$.post( ajaxurl + ( ajaxurl.indexOf( '?' ) > 0 ? '&' : '?' ) + 'action=woocommerce_shipping_zones_save_changes', {
							wc_shipping_zones_nonce : data.wc_shipping_zones_nonce,
							changes                 : this.changes
						}, this.onSaveResponse, 'json' );
					} else {
						shippingZone.trigger( 'saved:zones' );
					}
				},
				onSaveResponse: function( response, textStatus ) {
					if ( 'success' === textStatus ) {
						if ( response.success ) {
							shippingZone.set( 'zones', response.data.zones );
							shippingZone.trigger( 'change:zones' );
							shippingZone.changes = {};
							shippingZone.trigger( 'saved:zones' );
						} else {
							window.alert( data.strings.save_failed );
						}
					}
				}
			} ),

			// Backbone view
			ShippingZoneView = Backbone.View.extend({
				rowTemplate: $row_template,
				initialize: function() {
					this.listenTo( this.model, 'change:zones', this.setUnloadConfirmation );
					this.listenTo( this.model, 'saved:zones', this.clearUnloadConfirmation );
					this.listenTo( this.model, 'saved:zones', this.render );
					$tbody.on( 'change', { view: this }, this.updateModelOnChange );
					$tbody.on( 'sortupdate', { view: this }, this.updateModelOnSort );
					$( window ).on( 'beforeunload', { view: this }, this.unloadConfirmation );
					$( document.body ).on( 'click', '.wc-shipping-zone-add', { view: this }, this.onAddNewRow );
				},
				onAddNewRow: function() {
					var $link = $( this );
					window.location.href = $link.attr( 'href' );
				},
				block: function() {
					$( this.el ).block({
						message: null,
						overlayCSS: {
							background: '#fff',
							opacity: 0.6
						}
					});
				},
				unblock: function() {
					$( this.el ).unblock();
				},
				render: function() {
					var zones = _.indexBy( this.model.get( 'zones' ), 'zone_id' ),
						view  = this;

					view.$el.empty();
					view.unblock();

					if ( _.size( zones ) ) {
						// Sort zones
						zones = _( zones )
							.chain()
							.sortBy( function ( zone ) { return parseInt( zone.zone_id, 10 ); } )
							.sortBy( function ( zone ) { return parseInt( zone.zone_order, 10 ); } )
							.value();

						// Populate $tbody with the current zones
						$.each( zones, function( id, rowData ) {
							view.renderRow( rowData );
						} );
					} else {
						view.$el.append( $blank_template );
					}

					view.initRows();
				},
				renderRow: function( rowData ) {
					var view = this;
					view.$el.append( view.rowTemplate( rowData ) );
					view.initRow( rowData );
				},
				initRow: function( rowData ) {
					var view = this;
					var $tr = view.$el.find( 'tr[data-id="' + rowData.zone_id + '"]');

					// List shipping methods
					view.renderShippingMethods( rowData.zone_id, rowData.shipping_methods );
					$tr.find( '.wc-shipping-zone-delete' ).on( 'click', { view: this }, this.onDeleteRow );
				},
				initRows: function() {
					// Stripe
					if ( 0 === ( $( 'tbody.wc-shipping-zone-rows tr' ).length % 2 ) ) {
						$table.find( 'tbody.wc-shipping-zone-rows' ).next( 'tbody' ).find( 'tr' ).addClass( 'odd' );
					} else {
						$table.find( 'tbody.wc-shipping-zone-rows' ).next( 'tbody' ).find( 'tr' ).removeClass( 'odd' );
					}
					// Tooltips
					$( '#tiptip_holder' ).removeAttr( 'style' );
					$( '#tiptip_arrow' ).removeAttr( 'style' );
					$( '.tips' ).tipTip({ 'attribute': 'data-tip', 'fadeIn': 50, 'fadeOut': 50, 'delay': 50 });
				},
				renderShippingMethods: function( zone_id, shipping_methods ) {
					var $tr          = $( '.wc-shipping-zones tr[data-id="' + zone_id + '"]');
					var $method_list = $tr.find('.wc-shipping-zone-methods ul');

					$method_list.find( '.wc-shipping-zone-method' ).remove();

					if ( _.size( shipping_methods ) ) {
						shipping_methods = _.sortBy( shipping_methods, function( method ) {
							return parseInt( method.method_order, 10 );
						} );

						_.each( shipping_methods, function( shipping_method ) {
							var class_name = 'method_disabled';

							if ( 'yes' === shipping_method.enabled ) {
								class_name = 'method_enabled';
							}

							$method_list.append(
								'<li class="wc-shipping-zone-method ' + class_name + '">' + shipping_method.title + '</li>'
							);
						} );
					} else {
						$method_list.append( '<li class="wc-shipping-zone-method">' + data.strings.no_shipping_methods_offered + '</li>' );
					}
				},
				onDeleteRow: function( event ) {
					var view    = event.data.view,
						model   = view.model,
						zones   = _.indexBy( model.get( 'zones' ), 'zone_id' ),
						changes = {},
						row     = $( this ).closest('tr'),
						zone_id = row.data('id');

					event.preventDefault();

					if ( window.confirm( data.strings.delete_confirmation_msg ) ) {
						if ( zones[ zone_id ] ) {
							delete zones[ zone_id ];
							changes[ zone_id ] = _.extend( changes[ zone_id ] || {}, { deleted : 'deleted' } );
							model.set( 'zones', zones );
							model.logChanges( changes );
							event.data.view.block();
							event.data.view.model.save();
						}
					}
				},
				setUnloadConfirmation: function() {
					this.needsUnloadConfirm = true;
					$save_button.prop( 'disabled', false );
				},
				clearUnloadConfirmation: function() {
					this.needsUnloadConfirm = false;
					$save_button.prop( 'disabled', true );
				},
				unloadConfirmation: function( event ) {
					if ( event.data.view.needsUnloadConfirm ) {
						event.returnValue = data.strings.unload_confirmation_msg;
						window.event.returnValue = data.strings.unload_confirmation_msg;
						return data.strings.unload_confirmation_msg;
					}
				},
				updateModelOnChange: function( event ) {
					var model     = event.data.view.model,
						$target   = $( event.target ),
						zone_id   = $target.closest( 'tr' ).data( 'id' ),
						attribute = $target.data( 'attribute' ),
						value     = $target.val(),
						zones   = _.indexBy( model.get( 'zones' ), 'zone_id' ),
						changes = {};

					if ( ! zones[ zone_id ] || zones[ zone_id ][ attribute ] !== value ) {
						changes[ zone_id ] = {};
						changes[ zone_id ][ attribute ] = value;
					}

					model.logChanges( changes );
				},
				updateModelOnSort: function( event ) {
					var view    = event.data.view,
						model   = view.model,
						zones   = _.indexBy( model.get( 'zones' ), 'zone_id' ),
						rows    = $( 'tbody.wc-shipping-zone-rows tr' ),
						changes = {};

					// Update sorted row position
					_.each( rows, function( row ) {
						var zone_id = $( row ).data( 'id' ),
							old_position = null,
							new_position = parseInt( $( row ).index(), 10 );

						if ( zones[ zone_id ] ) {
							old_position = parseInt( zones[ zone_id ].zone_order, 10 );
						}

						if ( old_position !== new_position ) {
							changes[ zone_id ] = _.extend( changes[ zone_id ] || {}, { zone_order : new_position } );
						}
					} );

					if ( _.size( changes ) ) {
						model.logChanges( changes );
						event.data.view.block();
						event.data.view.model.save();
					}
				}
			} ),
			shippingZone = new ShippingZone({
				zones: data.zones
			} ),
			shippingZoneView = new ShippingZoneView({
				model:    shippingZone,
				el:       $tbody
			} );

		shippingZoneView.render();

		$tbody.sortable({
			items: 'tr',
			cursor: 'move',
			axis: 'y',
			handle: 'td.wc-shipping-zone-sort',
			scrollSensitivity: 40
		});
	});
})( jQuery, shippingZonesLocalizeScript, wp, ajaxurl );
