/* global ajaxurl */
/* global wpseo_bulk_editor_nonce */
/* jshint -W097 */
'use strict';
var bulk_editor = function( current_table ) {
	var new_class = current_table.find( '[class^=wpseo-new]' ).first().attr( 'class' );
	var new_id = '#' + new_class + '-';
	var existing_id = new_id.replace( 'new', 'existing' );
	var column_value = current_table.find( 'th[id^=col_existing_yoast]' ).first().text().replace( 'Existing ', '' );

	var save_method = new_class.replace( '-new-', '_save_' );
	var save_all_method = 'wpseo_save_all_' + current_table.attr( 'class' ).split( 'wpseo_bulk_' )[ 1 ];

	var bulk_type = save_method.replace( 'wpseo_save_', '' );

	var options = {
		new_class: '.' + new_class,
		new_id: new_id,
		existing_id: existing_id
	};

	var instance = {

		submit_new: function( id ) {
			var new_target = options.new_id + id;
			var existing_target = options.existing_id + id;

			var new_value;
			if ( jQuery( options.new_id + id ).prop( 'type' ) === 'select-one' ) {
				new_value = jQuery( new_target ).find( ':selected' ).text();
			}
			else {
				new_value = jQuery( new_target ).val();
			}

			var current_value = jQuery( existing_target ).html();

			if ( new_value === current_value ) {
				jQuery( new_target ).val( '' ).focus();
			}
			else {
				if ( ( new_value === '' ) && !window.confirm( 'Are you sure you want to remove the existing ' + column_value + '?' ) ) {
					jQuery( new_target ).focus();
					jQuery( new_target ).val( '' ).focus();
					return;
				}

				var data = {
					action: save_method,
					_ajax_nonce: wpseo_bulk_editor_nonce,
					wpseo_post_id: id,
					new_value: new_value,
					existing_value: current_value
				};

				jQuery.post( ajaxurl, data, instance.handle_response );
			}
		},

		submit_all: function( event ) {
			event.preventDefault();

			var data = {
				action: save_all_method,
				_ajax_nonce: wpseo_bulk_editor_nonce
			};

			data.send = false;
			data.items = {};
			data.existing_items = {};

			jQuery( options.new_class ).each( function() {
					var id = jQuery( this ).data( 'id' );
					var value = jQuery( this ).val();
					var existing_value = jQuery( options.existing_id + id ).html();

					if ( value !== '' ) {
						if ( value === existing_value ) {
							jQuery( options.new_id + id ).val( '' ).focus();
						}
						else {
							data.send = true;
							data.items[ id ] = value;
							data.existing_items[ id ] = existing_value;
						}
					}
				}
			);

			if ( data.send ) {
				jQuery.post( ajaxurl, data, instance.handle_responses );
			}
		},

		handle_response: function( response, status ) {
			if ( status !== 'success' ) {
				return;
			}

			var resp = response;
			if ( typeof resp === 'string' ) {
				resp = JSON.parse( resp );
			}

			if ( resp instanceof Array ) {
				jQuery.each( resp, function() {
						instance.handle_response( this, status );
					}
				);
			}
			else {
				if ( resp.status === 'success' ) {
					var new_value = resp[ 'new_' + bulk_type ];

					jQuery( options.existing_id + resp.post_id ).html( new_value.replace( /\\(?!\\)/g, '' ) );
					jQuery( options.new_id + resp.post_id ).val( '' ).focus();
				}
			}
		},

		handle_responses: function( responses, status ) {
			var resps = jQuery.parseJSON( responses );
			jQuery.each( resps, function() {
					instance.handle_response( this, status );
				}
			);
		},

		set_events: function() {
			current_table.find( '.wpseo-save' ).click( function() {
					var id = jQuery( this ).data( 'id' );
					instance.submit_new( id, this );
				}
			);

			current_table.find( '.wpseo-save-all' ).click( instance.submit_all );

			current_table.find( options.new_class ).keypress(
				function( event ) {
					if ( event.which === 13 ) {
						event.preventDefault();
						var id = jQuery( this ).data( 'id' );
						instance.submit_new( id, this );
					}
				}
			);
		}
	};

	return instance;
};

jQuery( document ).ready( function() {
		var parent_tables = jQuery( 'table[class*="wpseo_bulk"]' );
		parent_tables.each(
			function( number, parent_table ) {
				var current_table = jQuery( parent_table );
				var bulk_edit = bulk_editor( current_table );

				bulk_edit.set_events();
			}
		);
	}
);
