/**
 * Javascript to make Customizer Control load and save option presets.
 * @package Twenty8teen
 */

( function( wp, $ ) {
 	wp.customize.controlConstructor['preset'] = wp.customize.Control.extend( {
		nameField: null,
 	  ready: function() {
 			var control = this;
			control.nameField = control.container.find( '.preset-name-field' );
			control.container.on( 'click', '.apply-preset-item', function( event ) {
				var self = $( this ),
					preset = self.val();
				event.stopPropagation();
				control.doNotice( '' );
				control.retrievePresetValues( preset )
					.done( function( response ) {
						var undo = control.applyPresetValues( response.values );
						self.parent().find( '.revert-preset-item' )
							.removeClass( 'hidden' )
							.data( 'revert', undo );
            if ( ! control.nameField.val() ) {
              control.nameField.val( preset );
              control.updateChecks( self.data( 'keys' ) );
            }
					} )
					.fail( function( response ) {
						control.doNotice( response.message );
					} );
			} );

			control.container.on( 'click', '.revert-preset-item', function( event ) {
 				event.stopPropagation();
				control.doNotice( '' );
				control.applyPresetValues( $( this ).data( 'revert' ) );
			} );

 			control.container.on( 'click', '.add-content', function( event ) {
 				event.stopPropagation();
				control.doNotice( '' );
				$( this ).addClass( 'hidden' );
				control.container.find( '.new-content-item' ).removeClass( 'hidden' );
  			} );

 			control.container.on( 'click', '.save-new-preset', function( event ) {
 				event.stopPropagation();
				control.doNotice( '' );
 				control.savePreset( control.nameField.val().trim() )
					.done( function( response ) {
						var old = control.container
							.find( '.preset-item[data-preset="' + response.preset + '"]' );
						if ( old.length ) {
							old.replaceWith( response.content );
						}
						else {
							control.container.find( '.preset-item' ).last()
								.after( response.content );
						}
						control.container.find( '.add-content' ).removeClass( 'hidden' );
						control.container.find( '.new-content-item' ).addClass( 'hidden' );
					} )
					.fail( function( response ) {
						control.doNotice( response.message );
					} );
 			} );

 			control.container.on( 'click', '.cancel-new-preset', function( event ) {
 				event.stopPropagation();
				control.doNotice( '' );
 				control.nameField.val( '' );
				control.container.find( '.add-content' ).removeClass( 'hidden' );
				control.container.find( '.new-content-item' ).addClass( 'hidden' );
 			} );

 			control.container.on( 'click', '.submitdelete', function( event ) {
				var preset = $( this ).val();
 				event.stopPropagation();
				control.doNotice( '' );
 				control.deletePreset( preset )
					.done( function( response ) {
						var old = control.container
							.find( '.preset-item[data-preset="' + preset + '"]' );
						if ( response.content ) {
							old.replaceWith( response.content );
						}
						else {
							old.remove();
						}
					} )
					.always( function( response ) {
						control.doNotice( response.message );
					} );
 			} );
		},

		/**
		 * Get the checkbox value (setting ID) and then that settings' value.
		 */
		getSelectedValues: function() {
 			var control = this,
				obj = {};
 			$( '.new-content-item input[type="checkbox"]:checked', control.container )
 				.each( function( key, data ) {
					var setting = wp.customize( this.value );
					if ( setting ) {
						obj[this.value] = setting.get();
					}
 				} );
			return obj;
		},

		/**
		 * Set each setting to the supplied preset value, saving the old value.
		 */
		applyPresetValues: function ( values ) {
			var old = {};
			_.each( values, function( value, id ) {
				var setting = wp.customize( id );
				if ( setting ) {
					old[id] = setting.get();
					setting.set( value );
				}
			} );
			return old;
		},

		/**
		 * Update the checkboxes to reflect the supplied comma separated list.
		 */
 		updateChecks: function( list ) {
 			var control = this;
 			var keys = list.split( ',' );
			control.container.find( 'input[type="checkbox"]' ).each( function() {
 				$( this ).prop( 'checked', keys.includes( this.value ) );
 			} );
 		},

		/**
		 * Ajax request to retrieve preset values.
		 */
 		retrievePresetValues: function( preset ) {
			var request = wp.ajax.post( 'twenty8teen_retrieve_preset', {
				presets_nonce: wp.customize.settings.nonce['twenty8teen-customize-presets' + this.id],
				wp_customize: 'on',
				customize_theme: wp.customize.settings.theme.stylesheet,
				preset: preset
			} );
			return request;
 		},

		/**
		 * Ajax request to save preset values.
		 */
 		savePreset: function( preset ) {
			var control = this,
				request = wp.ajax.post( 'twenty8teen_save_preset', {
					presets_nonce: wp.customize.settings.nonce['twenty8teen-customize-presets' + control.id],
					wp_customize: 'on',
					customize_theme: wp.customize.settings.theme.stylesheet,
					preset: preset,
					preset_values: JSON.stringify( control.getSelectedValues() )
				} );
			return request;
 		},

		/**
		 * Ajax request to delete a preset.
		 */
 		deletePreset: function( preset ) {
			var request = wp.ajax.post( 'twenty8teen_delete_preset', {
				presets_nonce: wp.customize.settings.nonce['twenty8teen-customize-presets-delete' + this.id],
				wp_customize: 'on',
				customize_theme: wp.customize.settings.theme.stylesheet,
				preset: preset
			} );
			return request;
 		},

		/**
		 * Generic add and remove notification.
		 */
		doNotice: function( msg ) {
			var control = this;
			if ( msg ) {
				control.notifications.add( 'preset_msg', new wp.customize.Notification(
	        'preset_msg', {
	          type: 'warning',
						fromServer: true,
	          message: msg
	        }
	      ) );
			}
			else {
				control.notifications.remove( 'preset_msg' );
			}
		}

 	} );

} )( wp, jQuery );
