(function($){

	function check_pass_strength() {
		var pass1 = $('#pass1').val(), user = $('#user_login').val(), pass2 = $('#pass2').val(), strength;

		$('#pass-strength-result').removeClass('short bad good strong');
		if ( ! pass1 ) {
			$('#pass-strength-result').html( pwsL10n.empty );
			return;
		}

		strength = wp.passwordStrength.meter( pass1, wp.passwordStrength.userInputBlacklist(), pass2 );

		switch ( strength ) {
			case 2:
				$('#pass-strength-result').addClass('bad').html( pwsL10n['bad'] );
				break;
			case 3:
				$('#pass-strength-result').addClass('good').html( pwsL10n['good'] );
				break;
			case 4:
				$('#pass-strength-result').addClass('strong').html( pwsL10n['strong'] );
				break;
			case 5:
				$('#pass-strength-result').addClass('short').html( pwsL10n['mismatch'] );
				break;
			default:
				$('#pass-strength-result').addClass('short').html( pwsL10n['short'] );
		}
	}

	$(document).ready( function() {
		var select = $('#display_name');

		$('#pass1').val('').keyup( check_pass_strength );
		$('#pass2').val('').keyup( check_pass_strength );
		$('#pass-strength-result').show();
		$('.color-palette').click( function() {
			$(this).siblings('input[name="admin_color"]').prop('checked', true);
		});

		if ( select.length ) {
			$('#first_name, #last_name, #nickname').bind( 'blur.user_profile', function() {
				var dub = [],
					inputs = {
						display_nickname  : $('#nickname').val() || '',
						display_username  : $('#user_login').val() || '',
						display_firstname : $('#first_name').val() || '',
						display_lastname  : $('#last_name').val() || ''
					};

				if ( inputs.display_firstname && inputs.display_lastname ) {
					inputs['display_firstlast'] = inputs.display_firstname + ' ' + inputs.display_lastname;
					inputs['display_lastfirst'] = inputs.display_lastname + ' ' + inputs.display_firstname;
				}

				$.each( $('option', select), function( i, el ){
					dub.push( el.value );
				});

				$.each(inputs, function( id, value ) {
					if ( ! value )
						return;

					var val = value.replace(/<\/?[a-z][^>]*>/gi, '');

					if ( inputs[id].length && $.inArray( val, dub ) == -1 ) {
						dub.push(val);
						$('<option />', {
							'text': val
						}).appendTo( select );
					}
				});
			});
		}

		var $colorpicker = $( '#color-picker' ),
			$stylesheet = $( '#colors-css' ),
			user_id = $( 'input#user_id' ).val(),
			current_user_id = $( 'input[name="checkuser_id"]' ).val();

		// dropdown toggle
		$colorpicker.on( 'click', '.dropdown-current', function() {
			$colorpicker.toggleClass( 'picker-expanded' );
		});

		$colorpicker.on( 'click', '.color-option', function() {

			var color_scheme = $( this ).children( 'input[name="admin_color"]' ).val();

			// update selected
			$( this ).siblings( '.selected' ).removeClass( 'selected' )
			$( this ).addClass( 'selected' );
			$( this ).find( 'input' ).prop( 'checked', true );

			// update current
			$colorpicker.find( '.dropdown-current label' ).html( $( this ).children( 'label' ).html() );
			$colorpicker.find( '.dropdown-current table' ).html( $( this ).children( 'table' ).html() );
			$colorpicker.toggleClass( 'picker-expanded' );

			// preview/save color scheme
			if ( user_id == current_user_id ) {

				// repaint icons
				$stylesheet.attr( 'href', $( this ).children( '.css_url' ).val() );
				svgPainter.setColors( $.parseJSON( $( this ).children( '.icon_colors' ).val() ) );
				svgPainter.paint();

				// update user option
				$.post( ajaxurl, {
					action: 'save-user-color-scheme',
					color_scheme: color_scheme,
					user_id: user_id
				});

			}

		});

	});

})(jQuery);
