jQuery( function( $ ) {

	// wc_country_select_params is required to continue, ensure the object exists
	if ( typeof wc_country_select_params === 'undefined' ) {
		return false;
	}

	/* State/Country select boxes */
	var states_json = wc_country_select_params.countries.replace( /&quot;/g, '"' ),
		states = $.parseJSON( states_json );

	$( 'select.country_to_state, input.country_to_state' ).change( function() {

		var country = $( this ).val(),
			$statebox = $( this ).closest( 'div' ).find( '#billing_state, #shipping_state, #calc_shipping_state' ),
			$parent = $statebox.parent(),
			input_name = $statebox.attr( 'name' ),
			input_id = $statebox.attr( 'id' ),
			value = $statebox.val(),
			placeholder = $statebox.attr( 'placeholder' );

		if ( states[ country ] ) {
			if ( states[ country ].length === 0 ) {

				$statebox.parent().hide().find( '.chosen-container' ).remove();
				$statebox.replaceWith( '<input type="hidden" class="hidden" name="' + input_name + '" id="' + input_id + '" value="" placeholder="' + placeholder + '" />' );

				$( 'body' ).trigger( 'country_to_state_changed', [country, $( this ).closest( 'div' )] );

			} else {

				var options = '',
					state = states[ country ];

				for( var index in state ) {
					if ( state.hasOwnProperty( index ) ) {
						options = options + '<option value="' + index + '">' + state[ index ] + '</option>';
					}
				}

				$statebox.parent().show();

				if ( $statebox.is( 'input' ) ) {
					// Change for select
					$statebox.replaceWith( '<select name="' + input_name + '" id="' + input_id + '" class="state_select" placeholder="' + placeholder + '"></select>' );
					$statebox = $( this ).closest( 'div' ).find( '#billing_state, #shipping_state, #calc_shipping_state' );
				}

				$statebox.html( '<option value="">' + wc_country_select_params.i18n_select_state_text + '</option>' + options );

				$statebox.val( value );

				$( 'body' ).trigger( 'country_to_state_changed', [country, $( this ).closest( 'div' )] );

			}
		} else {
			if ( $statebox.is( 'select' ) ) {

				$parent.show().find( '.chosen-container' ).remove();
				$statebox.replaceWith( '<input type="text" class="input-text" name="' + input_name + '" id="' + input_id + '" placeholder="' + placeholder + '" />' );

				$( 'body' ).trigger( 'country_to_state_changed', [country, $( this ).closest( 'div' )] );

			} else if ( $statebox.is( '.hidden' ) ) {

				$parent.show().find( '.chosen-container' ).remove();
				$statebox.replaceWith( '<input type="text" class="input-text" name="' + input_name + '" id="' + input_id + '" placeholder="' + placeholder + '" />' );

				$( 'body' ).trigger( 'country_to_state_changed', [country, $( this ).closest( 'div' )] );

			}
		}

		$( 'body' ).trigger( 'country_to_state_changing', [country, $( this ).closest( 'div' )] );

	}).change();

});
