/* global ajaxurl, pwsL10n, userProfileL10n */
(function($) {
	var updateLock = false,

		$pass1Row,
		$pass1Wrap,
		$pass1,
		$pass1Text,
		$pass1Label,
		$pass2,
		$weakRow,
		$weakCheckbox,
		$toggleButton,
		$submitButtons,
		$submitButton,
		currentPass,
		inputEvent;

	/*
	 * Use feature detection to determine whether password inputs should use
	 * the `keyup` or `input` event. Input is preferred but lacks support
	 * in legacy browsers.
	 */
	if ( 'oninput' in document.createElement( 'input' ) ) {
		inputEvent = 'input';
	} else {
		inputEvent = 'keyup';
	}

	function generatePassword() {
		if ( typeof zxcvbn !== 'function' ) {
			setTimeout( generatePassword, 50 );
		} else {
			$pass1.val( $pass1.data( 'pw' ) );
			$pass1.trigger( 'pwupdate' );
			if ( 1 !== parseInt( $toggleButton.data( 'start-masked' ), 10 ) ) {
				$pass1Wrap.addClass( 'show-password' );
			} else {
				$toggleButton.trigger( 'click' );
			}
		}
	}

	function bindPass1() {
		var passStrength = $('#pass-strength-result')[0];

		currentPass = $pass1.val();

		$pass1Wrap = $pass1.parent();

		$pass1Text = $( '<input type="text"/>' )
			.attr( {
				'id':           'pass1-text',
				'name':         'pass1-text',
				'autocomplete': 'off'
			} )
			.addClass( $pass1[0].className )
			.data( 'pw', $pass1.data( 'pw' ) )
			.val( $pass1.val() )
			.on( inputEvent, function () {
				if ( $pass1Text.val() === currentPass ) {
					return;
				}
				$pass2.val( $pass1Text.val() );
				$pass1.val( $pass1Text.val() ).trigger( 'pwupdate' );
				currentPass = $pass1Text.val();
			} );

		$pass1.after( $pass1Text );

		if ( 1 === parseInt( $pass1.data( 'reveal' ), 10 ) ) {
			generatePassword();
		}

		$pass1.on( inputEvent + ' pwupdate', function () {
			if ( $pass1.val() === currentPass ) {
				return;
			}

			currentPass = $pass1.val();
			if ( $pass1Text.val() !== currentPass ) {
				$pass1Text.val( currentPass );
			}
			$pass1.add( $pass1Text ).removeClass( 'short bad good strong' );

			if ( passStrength.className ) {
				$pass1.add( $pass1Text ).addClass( passStrength.className );
				if ( 'short' === passStrength.className || 'bad' === passStrength.className ) {
					if ( ! $weakCheckbox.prop( 'checked' ) ) {
						$submitButtons.prop( 'disabled', true );
					}
					$weakRow.show();
				} else {
					$submitButtons.prop( 'disabled', false );
					$weakRow.hide();
				}
			}
		} );
	}

	function bindToggleButton() {
		$toggleButton = $pass1Row.find('.wp-hide-pw');
		$toggleButton.show().on( 'click', function () {
			if ( 1 === parseInt( $toggleButton.data( 'toggle' ), 10 ) ) {
				$pass1Wrap.addClass( 'show-password' );
				$toggleButton
					.data( 'toggle', 0 )
					.attr({
						'aria-label': userProfileL10n.ariaHide
					})
					.find( '.text' )
						.text( userProfileL10n.hide )
					.end()
					.find( '.dashicons' )
						.removeClass('dashicons-visibility')
						.addClass('dashicons-hidden');

				$pass1Text.focus();

				$pass1Label.attr( 'for', 'pass1-text' );

				if ( ! _.isUndefined( $pass1Text[0].setSelectionRange ) ) {
					$pass1Text[0].setSelectionRange( 0, 100 );
				}
			} else {
				$pass1Wrap.removeClass( 'show-password' );
				$toggleButton
					.data( 'toggle', 1 )
					.attr({
						'aria-label': userProfileL10n.ariaShow
					})
					.find( '.text' )
						.text( userProfileL10n.show )
					.end()
					.find( '.dashicons' )
						.removeClass('dashicons-hidden')
						.addClass('dashicons-visibility');

				$pass1.focus();

				$pass1Label.attr( 'for', 'pass1' );

				if ( ! _.isUndefined( $pass1[0].setSelectionRange ) ) {
					$pass1[0].setSelectionRange( 0, 100 );
				}
			}
		});
	}

	function bindPasswordForm() {
		var $passwordWrapper,
			$generateButton,
			$cancelButton;

		$pass1Row = $('.user-pass1-wrap');
		$pass1Label = $pass1Row.find('th label').attr( 'for', 'pass1-text' );

		// hide this
		$('.user-pass2-wrap').hide();

		$submitButton = $( '#submit' ).on( 'click', function () {
			updateLock = false;
		});

		$submitButtons = $submitButton.add( ' #createusersub' );

		$weakRow = $( '.pw-weak' );
		$weakCheckbox = $weakRow.find( '.pw-checkbox' );
		$weakCheckbox.change( function() {
			$submitButtons.prop( 'disabled', ! $weakCheckbox.prop( 'checked' ) );
		} );

		$pass1 = $('#pass1');
		if ( $pass1.length ) {
			bindPass1();
		}

		/**
		 * Fix a LastPass mismatch issue, LastPass only changes pass2.
		 *
		 * This fixes the issue by copying any changes from the hidden
		 * pass2 field to the pass1 field, then running check_pass_strength.
		 */
		$pass2 = $('#pass2').on( inputEvent, function () {
			if ( $pass2.val().length > 0 ) {
				$pass1.val( $pass2.val() );
				$pass2.val('');
				currentPass = '';
				$pass1.trigger( 'pwupdate' );
			}
		} );

		$passwordWrapper = $pass1Row.find('.wp-pwd').hide();

		bindToggleButton();

		$generateButton = $pass1Row.find( 'button.wp-generate-pw' ).show();
		$generateButton.on( 'click', function () {
			updateLock = true;

			$generateButton.hide();
			$passwordWrapper.show();

			if ( $pass1Text.val().length === 0 ) {
				generatePassword();
			}

			_.defer( function() {
				$pass1Text.focus();
				if ( ! _.isUndefined( $pass1Text[0].setSelectionRange ) ) {
					$pass1Text[0].setSelectionRange( 0, 100 );
				}
			}, 0 );
		} );

		$cancelButton = $pass1Row.find( 'button.wp-cancel-pw' );
		$cancelButton.on( 'click', function () {
			updateLock = false;

			$generateButton.show();
			$passwordWrapper.hide();

			// Clear password field to prevent update
			$pass1.val( '' ).trigger( 'pwupdate' );
			$submitButtons.prop( 'disabled', false );
		} );

		$pass1Row.closest('form').on( 'submit', function () {
			updateLock = false;

			$pass2.val( $pass1.val() );
			$pass1Wrap.removeClass( 'show-password' );
		});
	}

	function check_pass_strength() {
		var pass1 = $('#pass1').val(), strength;

		$('#pass-strength-result').removeClass('short bad good strong');
		if ( ! pass1 ) {
			$('#pass-strength-result').html( '&nbsp;' );
			return;
		}

		strength = wp.passwordStrength.meter( pass1, wp.passwordStrength.userInputBlacklist(), pass1 );

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

		$('#pass1').val('').on( inputEvent + ' pwupdate', check_pass_strength );
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

		bindPasswordForm();
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

	window.generatePassword = generatePassword;

	/* Warn the user if password was generated but not saved */
	$( window ).on( 'beforeunload', function () {
		if ( true === updateLock ) {
			return userProfileL10n.warn;
		}
	} );

})(jQuery);
