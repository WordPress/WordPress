/* global ajaxurl, pwsL10n */
(function($){
	$(function(){
		var pw_new = $('.user-pass1-wrap'),
			pw_line = pw_new.find('.wp-pwd'),
			pw_field = $('#pass1'),
			pw_field2 = $('#pass2'),
			pw_togglebtn = pw_new.find('.wp-hide-pw'),
			pw_generatebtn = pw_new.find('button.wp-generate-pw'),
			pw_2 = $('.user-pass2-wrap'),
			parentform = pw_new.closest('form'),
			pw_strength = $('#pass-strength-result'),
			pw_submitbtn_edit = $('#submit'),
			pw_submitbtn_new = $( '#createusersub' ),
			pw_checkbox = $('.pw-checkbox'),
			pw_weak = $('.pw-weak')
		;

		var generatePassword = function() {
			pw_field.val( pw_field.data( 'pw' ) );
			pw_field.trigger( 'propertychange' );
			pw_field.attr( 'type', 'text' ).focus();
			pw_field[0].setSelectionRange(100, 100);
		};

		pw_2.hide();
		pw_line.hide();
		pw_togglebtn.show();
		pw_generatebtn.show();

		if ( pw_field.data( 'reveal' ) == 1 ) {
			generatePassword();
		}

		parentform.on('submit', function(){
			pw_field2.val( pw_field.val() );
			pw_field.attr('type', 'password');
		});


		pw_field.on('input propertychange', function(){
			setTimeout( function(){
				var cssClass = pw_strength.attr('class');
				pw_field.removeClass( 'short bad good strong' );
				if ( 'undefined' !== typeof cssClass ) {
					pw_field.addClass( cssClass );
					if ( cssClass == 'short' || cssClass == 'bad' ) {
						if ( ! pw_checkbox.attr( 'checked' ) ) {
							pw_submitbtn_new.attr( 'disabled','disabled' );
							pw_submitbtn_edit.attr( 'disabled','disabled' );
						}
						pw_weak.show();
					} else {
						pw_submitbtn_new.removeAttr( 'disabled' );
						pw_submitbtn_edit.removeAttr( 'disabled' );
						pw_weak.hide();
					}
				}
			}, 1 );
		} );

		pw_checkbox.change( function() {
			if ( pw_checkbox.attr( 'checked' ) ) {
				pw_submitbtn_new.removeAttr( 'disabled' );
				pw_submitbtn_edit.removeAttr( 'disabled' );
			} else {
				pw_submitbtn_new.attr( 'disabled','disabled' );
				pw_submitbtn_edit.attr( 'disabled','disabled' );
			}
		} );

		/**
		 * Fix a LastPass mismatch issue, LastPass only changes pass2.
		 *
		 * This fixes the issue by copying any changes from the hidden
		 * pass2 field to the pass1 field.
		 */
		pw_field2.on( 'input propertychange', function() {
			pw_field.val( pw_field2.val() );
			pw_field.trigger( 'propertychange' );
		} );

		pw_new.on( 'click', 'button.wp-generate-pw', function(){
			pw_generatebtn.hide();
			pw_line.show();
			generatePassword();
		});

		pw_togglebtn.on( 'click', function() {
			var show = pw_togglebtn.attr( 'data-toggle' );
			if ( show == 1 ) {
				pw_field.attr( 'type', 'text' );
				pw_togglebtn.attr( 'data-toggle', 0 )
					.find( '.text' )
						.text( 'hide' )
				;
			} else {
				pw_field.attr( 'type', 'password' );
				pw_togglebtn.attr( 'data-toggle', 1 )
					.find( '.text' )
						.text( 'show' )
				;
			}
			pw_field.focus();
			pw_field[0].setSelectionRange(100, 100);
		});
	});

	function check_pass_strength() {
		var pass1 = $('#pass1').val(), pass2 = $('#pass2').val(), strength;

		$('#pass-strength-result').removeClass('short bad good strong');
		if ( ! pass1 ) {
			$('#pass-strength-result').html( pwsL10n.empty );
			return;
		}

		strength = wp.passwordStrength.meter( pass1, wp.passwordStrength.userInputBlacklist(), pass2 );

		switch ( strength ) {
			case 2:
				$('#pass-strength-result').addClass('bad').html( pwsL10n.bad );
				break;
			case 3:
				$('#pass-strength-result').addClass('good').html( pwsL10n.good );
				break;
			case 4:
				$('#pass-strength-result').addClass('strong').html( pwsL10n.strong );
				break;
			case 5:
				$('#pass-strength-result').addClass('short').html( pwsL10n.mismatch );
				break;
			default:
				$('#pass-strength-result').addClass('short').html( pwsL10n['short'] );
		}
	}

	$(document).ready( function() {
		var $colorpicker, $stylesheet, user_id, current_user_id,
			select = $( '#display_name' );

		$('#pass1').val('').on( 'input propertychange', check_pass_strength );
		$('#pass2').val('').on( 'input propertychange', check_pass_strength );
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
					inputs.display_firstlast = inputs.display_firstname + ' ' + inputs.display_lastname;
					inputs.display_lastfirst = inputs.display_lastname + ' ' + inputs.display_firstname;
				}

				$.each( $('option', select), function( i, el ){
					dub.push( el.value );
				});

				$.each(inputs, function( id, value ) {
					if ( ! value ) {
						return;
					}

					var val = value.replace(/<\/?[a-z][^>]*>/gi, '');

					if ( inputs[id].length && $.inArray( val, dub ) === -1 ) {
						dub.push(val);
						$('<option />', {
							'text': val
						}).appendTo( select );
					}
				});
			});
		}

		$colorpicker = $( '#color-picker' );
		$stylesheet = $( '#colors-css' );
		user_id = $( 'input#user_id' ).val();
		current_user_id = $( 'input[name="checkuser_id"]' ).val();

		$colorpicker.on( 'click.colorpicker', '.color-option', function() {
			var colors,
				$this = $(this);

			if ( $this.hasClass( 'selected' ) ) {
				return;
			}

			$this.siblings( '.selected' ).removeClass( 'selected' );
			$this.addClass( 'selected' ).find( 'input[type="radio"]' ).prop( 'checked', true );

			// Set color scheme
			if ( user_id === current_user_id ) {
				// Load the colors stylesheet.
				// The default color scheme won't have one, so we'll need to create an element.
				if ( 0 === $stylesheet.length ) {
					$stylesheet = $( '<link rel="stylesheet" />' ).appendTo( 'head' );
				}
				$stylesheet.attr( 'href', $this.children( '.css_url' ).val() );

				// repaint icons
				if ( typeof wp !== 'undefined' && wp.svgPainter ) {
					try {
						colors = $.parseJSON( $this.children( '.icon_colors' ).val() );
					} catch ( error ) {}

					if ( colors ) {
						wp.svgPainter.setColors( colors );
						wp.svgPainter.paint();
					}
				}

				// update user option
				$.post( ajaxurl, {
					action:       'save-user-color-scheme',
					color_scheme: $this.children( 'input[name="admin_color"]' ).val(),
					nonce:        $('#color-nonce').val()
				}).done( function( response ) {
					if ( response.success ) {
						$( 'body' ).removeClass( response.data.previousScheme ).addClass( response.data.currentScheme );
					}
				});
			}
		});
	});

	$( '#destroy-sessions' ).on( 'click', function( e ) {
		var $this = $(this);

		wp.ajax.post( 'destroy-sessions', {
			nonce: $( '#_wpnonce' ).val(),
			user_id: $( '#user_id' ).val()
		}).done( function( response ) {
			$this.prop( 'disabled', true );
			$this.siblings( '.notice' ).remove();
			$this.before( '<div class="notice notice-success inline"><p>' + response.message + '</p></div>' );
		}).fail( function( response ) {
			$this.siblings( '.notice' ).remove();
			$this.before( '<div class="notice notice-error inline"><p>' + response.message + '</p></div>' );
		});

		e.preventDefault();
	});

})(jQuery);
