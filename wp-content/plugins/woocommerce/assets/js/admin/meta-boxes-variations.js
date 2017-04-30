/* global wp, woocommerce_admin_meta_boxes_variations */
jQuery( function ( $ ) {

	var variation_sortable_options = {
		items: '.woocommerce_variation',
		cursor: 'move',
		axis: 'y',
		handle: 'h3',
		scrollSensitivity: 40,
		forcePlaceholderSize: true,
		helper: 'clone',
		opacity: 0.65,
		placeholder: 'wc-metabox-sortable-placeholder',
		start: function( event, ui ) {
			ui.item.css( 'background-color', '#f6f6f6' );
		},
		stop: function ( event, ui ) {
			ui.item.removeAttr( 'style' );
			variation_row_indexes();
		}
	};

	// Add a variation
	$( '#variable_product_options' ).on( 'click', 'button.add_variation', function () {

		$('.woocommerce_variations').block({
			message: null,
			overlayCSS: {
				background: '#fff url(' + woocommerce_admin_meta_boxes_variations.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center',
				opacity: 0.6
			}
		});

		var loop = $('.woocommerce_variation').size();

		var data = {
			action: 'woocommerce_add_variation',
			post_id: woocommerce_admin_meta_boxes_variations.post_id,
			loop: loop,
			security: woocommerce_admin_meta_boxes_variations.add_variation_nonce
		};

		$.post( woocommerce_admin_meta_boxes_variations.ajax_url, data, function ( response ) {

			$( '.woocommerce_variations' ).append( response );

			$( '.tips' ).tipTip({
				'attribute': 'data-tip',
				'fadeIn': 50,
				'fadeOut': 50
			});

			$( 'input.variable_is_downloadable, input.variable_is_virtual' ).change();

			$( '.woocommerce_variations' ).unblock();
			$( '#variable_product_options' ).trigger( 'woocommerce_variations_added' );
		});

		return false;

	});

	$( '#variable_product_options').on( 'click', 'button.link_all_variations', function () {

		var answer = window.confirm( woocommerce_admin_meta_boxes_variations.i18n_link_all_variations );

		if ( answer ) {

			$( '#variable_product_options' ).block({
				message: null,
				overlayCSS: {
					background: '#fff url(' + woocommerce_admin_meta_boxes_variations.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center',
					opacity: 0.6
				}
			});

			var data = {
				action: 'woocommerce_link_all_variations',
				post_id: woocommerce_admin_meta_boxes_variations.post_id,
				security: woocommerce_admin_meta_boxes_variations.link_variation_nonce
			};

			$.post( woocommerce_admin_meta_boxes_variations.ajax_url, data, function ( response ) {

				var count = parseInt( response, 10 );

				if ( 1 === count ) {
					window.alert( count + ' ' + woocommerce_admin_meta_boxes_variations.i18n_variation_added );
				} else if ( 0 === count || count > 1 ) {
					window.alert( count + ' ' + woocommerce_admin_meta_boxes_variations.i18n_variations_added );
				} else {
					window.alert( woocommerce_admin_meta_boxes_variations.i18n_no_variations_added );
				}

				if ( count > 0 ) {
					var this_page = window.location.toString();

					this_page = this_page.replace( 'post-new.php?', 'post.php?post=' + woocommerce_admin_meta_boxes_variations.post_id + '&action=edit&' );

					$( '#variable_product_options' ).load( this_page + ' #variable_product_options_inner', function () {
						$( '#variable_product_options' ).unblock();
						$( '#variable_product_options' ).trigger( 'woocommerce_variations_added' );

						$( '.tips' ).tipTip({
							'attribute': 'data-tip',
							'fadeIn': 50,
							'fadeOut': 50
						});
					} );
				} else {
					$( '#variable_product_options' ).unblock();
				}
			});
		}
		return false;
	});

	$( '#variable_product_options' ).on( 'click', 'button.remove_variation', function ( e ) {
		e.preventDefault();
		var answer = window.confirm( woocommerce_admin_meta_boxes_variations.i18n_remove_variation );
		if ( answer ) {

			var el = $( this ).parent().parent();

			var variation = $( this ).attr( 'rel' );

			if ( variation > 0 ) {

				$( el ).block({
					message: null,
					overlayCSS: {
						background: '#fff url(' + woocommerce_admin_meta_boxes_variations.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center',
						opacity: 0.6
					}
				});

				var data = {
					action: 'woocommerce_remove_variation',
					variation_id: variation,
					security: woocommerce_admin_meta_boxes_variations.delete_variation_nonce
				};

				$.post( woocommerce_admin_meta_boxes_variations.ajax_url, data, function ( response ) {
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
	});

	$( '.wc-metaboxes-wrapper' ).on( 'click', 'a.bulk_edit', function ( event ) {
		var bulk_edit  = $( 'select#field_to_edit' ).val(),
			checkbox,
			answer,
			value;

		switch ( bulk_edit ) {
			case 'toggle_enabled' :
				checkbox = $( 'input[name^="variable_enabled"]' );
				checkbox.attr('checked', ! checkbox.attr( 'checked' ) );
			break;
			case 'toggle_downloadable' :
				checkbox = $( 'input[name^="variable_is_downloadable"]' );
				checkbox.attr( 'checked', ! checkbox.attr( 'checked' ) );
				$('input.variable_is_downloadable').change();
			break;
			case 'toggle_virtual' :
				checkbox = $('input[name^="variable_is_virtual"]');
				checkbox.attr( 'checked', ! checkbox.attr( 'checked' ) );
				$( 'input.variable_is_virtual' ).change();
			break;
			case 'delete_all' :
				answer = window.confirm( woocommerce_admin_meta_boxes_variations.i18n_delete_all_variations );

				if ( answer ) {

					answer = window.confirm( woocommerce_admin_meta_boxes_variations.i18n_last_warning );

					if ( answer ) {

						var variation_ids = [];

						$( '.woocommerce_variations .woocommerce_variation' ).block({
							message: null,
							overlayCSS: {
								background: '#fff url(' + woocommerce_admin_meta_boxes_variations.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center',
								opacity: 0.6
							}
						});

						$( '.woocommerce_variations .woocommerce_variation .remove_variation' ).each( function () {

							var variation = $( this ).attr( 'rel' );
							if ( variation > 0 ) {
								variation_ids.push( variation );
							}
						});

						var data = {
							action: 'woocommerce_remove_variations',
							variation_ids: variation_ids,
							security: woocommerce_admin_meta_boxes_variations.delete_variations_nonce
						};

						$.post( woocommerce_admin_meta_boxes_variations.ajax_url, data, function( response ) {
							$( '.woocommerce_variations .woocommerce_variation' ).fadeOut( '300', function () {
								$( '.woocommerce_variations .woocommerce_variation' ).remove();
							});
						});
					}
				}
			break;
			case 'variable_regular_price_increase':
			case 'variable_regular_price_decrease':
			case 'variable_sale_price_increase':
			case 'variable_sale_price_decrease':
				var edit_field;
				if ( bulk_edit.lastIndexOf( 'variable_regular_price', 0 ) === 0 ) {
					edit_field = 'variable_regular_price';
				} else {
					edit_field = 'variable_sale_price';
				}

				value = window.prompt( woocommerce_admin_meta_boxes_variations.i18n_enter_a_value_fixed_or_percent ).toString();

				$( ':input[name^="' + edit_field + '"]' ).each( function() {
					var current_value = Number( $( this ).val() ), new_value;

					if ( value.indexOf( '%' ) >= 0 ) {
						var mod_value = Number( ( Number( current_value ) / 100 ) * Number( value.replace(/\%/, '' ) ) );
					} else {
						var mod_value = Number( value );
					}

					if ( bulk_edit.indexOf( 'increase' ) !== -1 ) {
						new_value = current_value + mod_value;
					} else {
						new_value = current_value - mod_value;
					}

					$( this ).val( new_value ).change();
				});
			break;
			case 'variable_regular_price' :
			case 'variable_sale_price' :
			case 'variable_stock' :
			case 'variable_weight' :
			case 'variable_length' :
			case 'variable_width' :
			case 'variable_height' :
			case 'variable_download_limit' :
			case 'variable_download_expiry' :
				value = window.prompt( woocommerce_admin_meta_boxes_variations.i18n_enter_a_value );

				$( ':input[name^="' + bulk_edit + '"]').not('[name*="dates"]').val( value ).change();
			break;
			default:
				$( 'select#field_to_edit' ).trigger( bulk_edit );
			break;

		}
	});

	$( '#variable_product_options' ).on( 'change', 'input.variable_is_downloadable', function () {

		$( this ).closest( '.woocommerce_variation' ).find( '.show_if_variation_downloadable' ).hide();

		if ( $( this ).is( ':checked' ) ) {
			$( this ).closest( '.woocommerce_variation' ).find( '.show_if_variation_downloadable' ).show();
		}

	});

	$( '#variable_product_options' ).on( 'change', 'input.variable_is_virtual', function () {

		$( this ).closest( '.woocommerce_variation' ).find( '.hide_if_variation_virtual' ).show();

		if ( $( this ).is( ':checked' ) ) {
			$( this ).closest( '.woocommerce_variation' ).find( '.hide_if_variation_virtual' ).hide();
		}

	});

	$( 'input.variable_is_downloadable, input.variable_is_virtual' ).change();

	// Ordering
	$( '#variable_product_options' ).on( 'woocommerce_variations_added', function () {
		$( '.woocommerce_variations' ).sortable( variation_sortable_options );
	} );

	$( '.woocommerce_variations' ).sortable( variation_sortable_options );

	function variation_row_indexes() {
		$( '.woocommerce_variations .woocommerce_variation' ).each( function ( index, el ) {
			$( '.variation_menu_order', el ).val( parseInt( $( el ).index( '.woocommerce_variations .woocommerce_variation' ), 10 ) );
		});
	}

	// Uploader
	var variable_image_frame;
	var setting_variation_image_id;
	var setting_variation_image;
	var wp_media_post_id = wp.media.model.settings.post.id;

	$( '#variable_product_options' ).on( 'click', '.upload_image_button', function ( event ) {

		var $button                = $( this );
		var post_id                = $button.attr( 'rel' );
		var $parent                = $button.closest( '.upload_image' );
		setting_variation_image    = $parent;
		setting_variation_image_id = post_id;

		event.preventDefault();

		if ( $button.is( '.remove' ) ) {

			setting_variation_image.find( '.upload_image_id' ).val( '' );
			setting_variation_image.find( 'img' ).attr( 'src', woocommerce_admin_meta_boxes_variations.woocommerce_placeholder_img_src );
			setting_variation_image.find( '.upload_image_button' ).removeClass( 'remove' );

		} else {

			// If the media frame already exists, reopen it.
			if ( variable_image_frame ) {
				variable_image_frame.uploader.uploader.param( 'post_id', setting_variation_image_id );
				variable_image_frame.open();
				return;
			} else {
				wp.media.model.settings.post.id = setting_variation_image_id;
			}

			// Create the media frame.
			variable_image_frame = wp.media.frames.variable_image = wp.media({
				// Set the title of the modal.
				title: woocommerce_admin_meta_boxes_variations.i18n_choose_image,
				button: {
					text: woocommerce_admin_meta_boxes_variations.i18n_set_image
				},
				states : [
					new wp.media.controller.Library({
						title: woocommerce_admin_meta_boxes_variations.i18n_choose_image,
						filterable :	'all'
					})
				]
			});

			// When an image is selected, run a callback.
			variable_image_frame.on( 'select', function () {

				var attachment = variable_image_frame.state().get( 'selection' ).first().toJSON();

				setting_variation_image.find( '.upload_image_id' ).val( attachment.id );
				setting_variation_image.find( '.upload_image_button' ).addClass( 'remove' );
				setting_variation_image.find( 'img' ).attr( 'src', attachment.url );

				wp.media.model.settings.post.id = wp_media_post_id;
			});

			// Finally, open the modal.
			variable_image_frame.open();
		}
	});

	// Restore ID
	$( 'a.add_media' ).on(' click', function () {
		wp.media.model.settings.post.id = wp_media_post_id;
	});

});
