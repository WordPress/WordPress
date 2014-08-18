jQuery( function( $ ) {

	var locale_json = wc_address_i18n_params.locale.replace( /&quot;/g, '"' ),
		locale = $.parseJSON( locale_json ),
		required = ' <abbr class="required" title="' + wc_address_i18n_params.i18n_required_text + '">*</abbr>';

	$( 'body' )

		// Handle locale
		.bind( 'country_to_state_changing', function( event, country, wrapper ) {

			var thisform = wrapper,
				thislocale;

			if ( typeof locale[ country ] !== 'undefined' ) {
				thislocale = locale[ country ];
			} else {
				thislocale = locale['default'];
			}

			// Handle locale fields
			var locale_fields = $.parseJSON( wc_address_i18n_params.locale_fields );

			$.each( locale_fields, function( key, value ) {

				var field = thisform.find( value );

				if ( thislocale[ key ] ) {

					if ( thislocale[ key ].label ) {
						field.find( 'label' ).html( thislocale[ key ].label );
					}

					if ( thislocale[ key ].placeholder ) {
						field.find( 'input' ).attr( 'placeholder', thislocale[ key ].placeholder );
					}

					field.find( 'label abbr' ).remove();

					if ( typeof thislocale[ key ].required === 'undefined' && locale['default'][ key ].required === true ) {
						field.find( 'label' ).append( required );
					} else if ( thislocale[ key ].required === true ) {
						field.find( 'label' ).append( required );
					}

					if ( key !== 'state' ) {
						if ( thislocale[ key ].hidden === true ) {
							field.hide().find( 'input' ).val( '' );
						} else {
							field.show();
						}
					}

				} else if ( locale['default'][ key ] ) {
					if ( locale['default'][ key ].required === true ) {
						if ( field.find( 'label abbr' ).size() === 0 ) field.find( 'label' ).append( required );
					}

					if ( key !== 'state' ) {
						if ( typeof locale['default'][ key ].hidden === 'undefined' || locale['default'][ key ].hidden === false ) {
							field.show();
						} else if ( locale['default'][ key ].hidden === true ) {
							field.hide().find( 'input' ).val( '' );
						}
					}
				}

			});

			var $postcodefield = thisform.find( '#billing_postcode_field, #shipping_postcode_field' ),
				$cityfield     = thisform.find( '#billing_city_field, #shipping_city_field' ),
				$statefield    = thisform.find( '#billing_state_field, #shipping_state_field' );

			if ( ! $postcodefield.attr( 'data-o_class' ) ) {
				$postcodefield.attr( 'data-o_class', $postcodefield.attr( 'class' ) );
				$cityfield.attr( 'data-o_class', $cityfield.attr( 'class' ) );
				$statefield.attr( 'data-o_class', $statefield.attr( 'class' ) );
			}

			// Re-order postcode/city
			if ( thislocale.postcode_before_city ) {

				$postcodefield.add( $cityfield ).add( $statefield ).removeClass( 'form-row-first form-row-last' ).addClass( 'form-row-wide' );
				$postcodefield.insertBefore( $cityfield );

			} else {
				// Default
				$postcodefield.attr( 'class', $postcodefield.attr( 'data-o_class' ) );
				$cityfield.attr( 'class', $cityfield.attr( 'data-o_class' ) );
				$statefield.attr( 'class', $statefield.attr( 'data-o_class' ) );
				$postcodefield.insertAfter( $statefield );
			}

	});

});
