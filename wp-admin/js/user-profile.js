/**
 * @output wp-admin/js/user-profile.js
 */

/* global ajaxurl, pwsL10n, userProfileL10n */
(function($) {
	var updateLock = false,
		__ = wp.i18n.__,
		$pass1Row,
		$pass1,
		$pass2,
		$weakRow,
		$weakCheckbox,
		$toggleButton,
		$submitButtons,
		$submitButton,
		currentPass,
		$passwordWrapper;

	function generatePassword() {
		if ( typeof zxcvbn !== 'function' ) {
			setTimeout( generatePassword, 50 );
			return;
		} else if ( ! $pass1.val() || $passwordWrapper.hasClass( 'is-open' ) ) {
			// zxcvbn loaded before user entered password, or generating new password.
			$pass1.val( $pass1.data( 'pw' ) );
			$pass1.trigger( 'pwupdate' );
			showOrHideWeakPasswordCheckbox();
		} else {
			// zxcvbn loaded after the user entered password, check strength.
			check_pass_strength();
			showOrHideWeakPasswordCheckbox();
		}

		/*
		 * This works around a race condition when zxcvbn loads quickly and
		 * causes `generatePassword()` to run prior to the toggle button being
		 * bound.
		 */
		bindToggleButton();

		// Install screen.
		if ( 1 !== parseInt( $toggleButton.data( 'start-masked' ), 10 ) ) {
			// Show the password not masked if admin_password hasn't been posted yet.
			$pass1.attr( 'type', 'text' );
		} else {
			// Otherwise, mask the password.
			$toggleButton.trigger( 'click' );
		}

		// Once zxcvbn loads, passwords strength is known.
		$( '#pw-weak-text-label' ).text( __( 'Confirm use of weak password' ) );

		// Focus the password field.
		$( $pass1 ).trigger( 'focus' );
	}

	function bindPass1() {
		currentPass = $pass1.val();

		if ( 1 === parseInt( $pass1.data( 'reveal' ), 10 ) ) {
			generatePassword();
		}

		$pass1.on( 'input' + ' pwupdate', function () {
			if ( $pass1.val() === currentPass ) {
				return;
			}

			currentPass = $pass1.val();

			// Refresh password strength area.
			$pass1.removeClass( 'short bad good strong' );
			showOrHideWeakPasswordCheckbox();
		} );
	}

	function resetToggle( show ) {
		$toggleButton
			.attr({
				'aria-label': show ? __( 'Show password' ) : __( 'Hide password' )
			})
			.find( '.text' )
				.text( show ? __( 'Show' ) : __( 'Hide' ) )
			.end()
			.find( '.dashicons' )
				.removeClass( show ? 'dashicons-hidden' : 'dashicons-visibility' )
				.addClass( show ? 'dashicons-visibility' : 'dashicons-hidden' );
	}

	function bindToggleButton() {
		if ( !! $toggleButton ) {
			// Do not rebind.
			return;
		}
		$toggleButton = $pass1Row.find('.wp-hide-pw');
		$toggleButton.show().on( 'click', function () {
			if ( 'password' === $pass1.attr( 'type' ) ) {
				$pass1.attr( 'type', 'text' );
				resetToggle( false );
			} else {
				$pass1.attr( 'type', 'password' );
				resetToggle( true );
			}
		});
	}

	/**
	 * Handle the password reset button. Sets up an ajax callback to trigger sending
	 * a password reset email.
	 */
	function bindPasswordResetLink() {
		$( '#generate-reset-link' ).on( 'click', function() {
			var $this  = $(this),
				data = {
					'user_id': userProfileL10n.user_id, // The user to send a reset to.
					'nonce':   userProfileL10n.nonce    // Nonce to validate the action.
				};

				// Remove any previous error messages.
				$this.parent().find( '.notice-error' ).remove();

				// Send the reset request.
				var resetAction =  wp.ajax.post( 'send-password-reset', data );

				// Handle reset success.
				resetAction.done( function( response ) {
					addInlineNotice( $this, true, response );
				} );

				// Handle reset failure.
				resetAction.fail( function( response ) {
					addInlineNotice( $this, false, response );
				} );

		});

	}

	/**
	 * Helper function to insert an inline notice of success or failure.
	 *
	 * @param {jQuery Object} $this   The button element: the message will be inserted
	 *                                above this button
	 * @param {bool}          success Whether the message is a success message.
	 * @param {string}        message The message to insert.
	 */
	function addInlineNotice( $this, success, message ) {
		var resultDiv = $( '<div />' );

		// Set up the notice div.
		resultDiv.addClass( 'notice inline' );

		// Add a class indicating success or failure.
		resultDiv.addClass( 'notice-' + ( success ? 'success' : 'error' ) );

		// Add the message, wrapping in a p tag, with a fadein to highlight each message.
		resultDiv.text( $( $.parseHTML( message ) ).text() ).wrapInner( '<p />');

		// Disable the button when the callback has succeeded.
		$this.prop( 'disabled', success );

		// Remove any previous notices.
		$this.siblings( '.notice' ).remove();

		// Insert the notice.
		$this.before( resultDiv );
	}

	function bindPasswordForm() {
		var $generateButton,
			$cancelButton;

		$pass1Row = $( '.user-pass1-wrap, .user-pass-wrap, .reset-pass-submit' );

		// Hide the confirm password field when JavaScript support is enabled.
		$('.user-pass2-wrap').hide();

		$submitButton = $( '#submit, #wp-submit' ).on( 'click', function () {
			updateLock = false;
		});

		$submitButtons = $submitButton.add( ' #createusersub' );

		$weakRow = $( '.pw-weak' );
		$weakCheckbox = $weakRow.find( '.pw-checkbox' );
		$weakCheckbox.on( 'change', function() {
			$submitButtons.prop( 'disabled', ! $weakCheckbox.prop( 'checked' ) );
		} );

		$pass1 = $('#pass1');
		if ( $pass1.length ) {
			bindPass1();
		} else {
			// Password field for the login form.
			$pass1 = $( '#user_pass' );
		}

		/*
		 * Fix a LastPass mismatch issue, LastPass only changes pass2.
		 *
		 * This fixes the issue by copying any changes from the hidden
		 * pass2 field to the pass1 field, then running check_pass_strength.
		 */
		$pass2 = $( '#pass2' ).on( 'input', function () {
			if ( $pass2.val().length > 0 ) {
				$pass1.val( $pass2.val() );
				$pass2.val('');
				currentPass = '';
				$pass1.trigger( 'pwupdate' );
			}
		} );

		// Disable hidden inputs to prevent autofill and submission.
		if ( $pass1.is( ':hidden' ) ) {
			$pass1.prop( 'disabled', true );
			$pass2.prop( 'disabled', true );
		}

		$passwordWrapper = $pass1Row.find( '.wp-pwd' );
		$generateButton  = $pass1Row.find( 'button.wp-generate-pw' );

		bindToggleButton();

		$generateButton.show();
		$generateButton.on( 'click', function () {
			updateLock = true;

			// Make sure the password fields are shown.
			$generateButton.not( '.skip-aria-expanded' ).attr( 'aria-expanded', 'true' );
			$passwordWrapper
				.show()
				.addClass( 'is-open' );

			// Enable the inputs when showing.
			$pass1.attr( 'disabled', false );
			$pass2.attr( 'disabled', false );

			// Set the password to the generated value.
			generatePassword();

			// Show generated password in plaintext by default.
			resetToggle ( false );

			// Generate the next password and cache.
			wp.ajax.post( 'generate-password' )
				.done( function( data ) {
					$pass1.data( 'pw', data );
				} );
		} );

		$cancelButton = $pass1Row.find( 'button.wp-cancel-pw' );
		$cancelButton.on( 'click', function () {
			updateLock = false;

			// Disable the inputs when hiding to prevent autofill and submission.
			$pass1.prop( 'disabled', true );
			$pass2.prop( 'disabled', true );

			// Clear password field and update the UI.
			$pass1.val( '' ).trigger( 'pwupdate' );
			resetToggle( false );

			// Hide password controls.
			$passwordWrapper
				.hide()
				.removeClass( 'is-open' );

			// Stop an empty password from being submitted as a change.
			$submitButtons.prop( 'disabled', false );

			$generateButton.attr( 'aria-expanded', 'false' );
		} );

		$pass1Row.closest( 'form' ).on( 'submit', function () {
			updateLock = false;

			$pass1.prop( 'disabled', false );
			$pass2.prop( 'disabled', false );
			$pass2.val( $pass1.val() );
		});
	}

	function check_pass_strength() {
		var pass1 = $('#pass1').val(), strength;

		$('#pass-strength-result').removeClass('short bad good strong empty');
		if ( ! pass1 || '' ===  pass1.trim() ) {
			$( '#pass-strength-result' ).addClass( 'empty' ).html( '&nbsp;' );
			return;
		}

		strength = wp.passwordStrength.meter( pass1, wp.passwordStrength.userInputDisallowedList(), pass1 );

		switch ( strength ) {
			case -1:
				$( '#pass-strength-result' ).addClass( 'bad' ).html( pwsL10n.unknown );
				break;
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

	function showOrHideWeakPasswordCheckbox() {
		var passStrength = $('#pass-strength-result')[0];

		if ( passStrength.className ) {
			$pass1.addClass( passStrength.className );
			if ( $( passStrength ).is( '.short, .bad' ) ) {
				if ( ! $weakCheckbox.prop( 'checked' ) ) {
					$submitButtons.prop( 'disabled', true );
				}
				$weakRow.show();
			} else {
				if ( $( passStrength ).is( '.empty' ) ) {
					$submitButtons.prop( 'disabled', true );
					$weakCheckbox.prop( 'checked', false );
				} else {
					$submitButtons.prop( 'disabled', false );
				}
				$weakRow.hide();
			}
		}
	}

	$( function() {
		var $colorpicker, $stylesheet, user_id, current_user_id,
			select       = $( '#display_name' ),
			current_name = select.val(),
			greeting     = $( '#wp-admin-bar-my-account' ).find( '.display-name' );

		$( '#pass1' ).val( '' ).on( 'input' + ' pwupdate', check_pass_strength );
		$('#pass-strength-result').show();
		$('.color-palette').on( 'click', function() {
			$(this).siblings('input[name="admin_color"]').prop('checked', true);
		});

		if ( select.length ) {
			$('#first_name, #last_name, #nickname').on( 'blur.user_profile', function() {
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

			/**
			 * Replaces "Howdy, *" in the admin toolbar whenever the display name dropdown is updated for one's own profile.
			 */
			select.on( 'change', function() {
				if ( user_id !== current_user_id ) {
					return;
				}

				var display_name = this.value.trim() || current_name;

				greeting.text( display_name );
			} );
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

			// Set color scheme.
			if ( user_id === current_user_id ) {
				// Load the colors stylesheet.
				// The default color scheme won't have one, so we'll need to create an element.
				if ( 0 === $stylesheet.length ) {
					$stylesheet = $( '<link rel="stylesheet" />' ).appendTo( 'head' );
				}
				$stylesheet.attr( 'href', $this.children( '.css_url' ).val() );

				// Repaint icons.
				if ( typeof wp !== 'undefined' && wp.svgPainter ) {
					try {
						colors = JSON.parse( $this.children( '.icon_colors' ).val() );
					} catch ( error ) {}

					if ( colors ) {
						wp.svgPainter.setColors( colors );
						wp.svgPainter.paint();
					}
				}

				// Update user option.
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
		bindPasswordResetLink();
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

	// Warn the user if password was generated but not saved.
	$( window ).on( 'beforeunload', function () {
		if ( true === updateLock ) {
			return __( 'Your new password has not been saved.' );
		}
	} );

	/*
	 * We need to generate a password as soon as the Reset Password page is loaded,
	 * to avoid double clicking the button to retrieve the first generated password.
	 * See ticket #39638.
	 */
	$( function() {
		if ( $( '.reset-pass-submit' ).length ) {
			$( '.reset-pass-submit button.wp-generate-pw' ).trigger( 'click' );
		}
	});

})(jQuery);
