<?php
/**
 * Class for creating a Time Based One-Time Password provider.
 *
 * @package Two_Factor
 */

/**
 * Class Two_Factor_Totp
 */
class Two_Factor_Totp extends Two_Factor_Provider {

	/**
	 * The user meta key for the TOTP Secret key.
	 *
	 * @var string
	 */
	const SECRET_META_KEY = '_two_factor_totp_key';

	/**
	 * The user meta key for the last successful TOTP token timestamp logged in with.
	 *
	 * @var string
	 */
	const LAST_SUCCESSFUL_LOGIN_META_KEY = '_two_factor_totp_last_successful_login';

	const DEFAULT_KEY_BIT_SIZE        = 160;
	const DEFAULT_CRYPTO              = 'sha1';
	const DEFAULT_DIGIT_COUNT         = 6;
	const DEFAULT_TIME_STEP_SEC       = 30;
	const DEFAULT_TIME_STEP_ALLOWANCE = 4;

	/**
	 * Characters used in base32 encoding.
	 *
	 * @var string
	 */
	private static $base_32_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

	/**
	 * Class constructor. Sets up hooks, etc.
	 *
	 * @codeCoverageIgnore
	 */
	protected function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'two_factor_user_options_' . __CLASS__, array( $this, 'user_two_factor_options' ) );

		parent::__construct();
	}

	/**
	 * Register the rest-api endpoints required for this provider.
	 *
	 * @codeCoverageIgnore
	 */
	public function register_rest_routes() {
		register_rest_route(
			Two_Factor_Core::REST_NAMESPACE,
			'/totp',
			array(
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'rest_delete_totp' ),
					'permission_callback' => function ( $request ) {
						return Two_Factor_Core::rest_api_can_edit_user_and_update_two_factor_options( $request['user_id'] );
					},
					'args'                => array(
						'user_id' => array(
							'required' => true,
							'type'     => 'integer',
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'rest_setup_totp' ),
					'permission_callback' => function ( $request ) {
						return Two_Factor_Core::rest_api_can_edit_user_and_update_two_factor_options( $request['user_id'] );
					},
					'args'                => array(
						'user_id'         => array(
							'required' => true,
							'type'     => 'integer',
						),
						'key'             => array(
							'type'              => 'string',
							'default'           => '',
							'validate_callback' => null, // Note: validation handled in ::rest_setup_totp().
						),
						'code'            => array(
							'type'              => 'string',
							'default'           => '',
							'validate_callback' => null, // Note: validation handled in ::rest_setup_totp().
						),
						'enable_provider' => array(
							'required' => false,
							'type'     => 'boolean',
							'default'  => false,
						),
					),
				),
			)
		);
	}

	/**
	 * Returns the name of the provider.
	 */
	public function get_label() {
		return _x( 'Authenticator App', 'Provider Label', 'two-factor' );
	}

	/**
	 * Returns the "continue with" text provider for the login screen.
	 *
	 * @since 0.9.0
	 */
	public function get_alternative_provider_label() {
		return __( 'Use your authenticator app for time-based one-time passwords (TOTP)', 'two-factor' );
	}

	/**
	 * Enqueue scripts
	 *
	 * @codeCoverageIgnore
	 * @param string $hook_suffix Hook suffix.
	 */
	public function enqueue_assets( $hook_suffix ) {
		$environment_prefix = file_exists( TWO_FACTOR_DIR . '/dist' ) ? '/dist' : '';

		wp_register_script(
			'two-factor-qr-code-generator',
			plugins_url( $environment_prefix . '/includes/qrcode-generator/qrcode.js', __DIR__ ),
			array(),
			TWO_FACTOR_VERSION,
			true
		);
	}

	/**
	 * Rest API endpoint for handling deactivation of TOTP.
	 *
	 * @param WP_REST_Request $request The Rest Request object.
	 * @return array Success array.
	 */
	public function rest_delete_totp( $request ) {
		$user_id = $request['user_id'];
		$user    = get_user_by( 'id', $user_id );

		$this->delete_user_totp_key( $user_id );

		if ( ! Two_Factor_Core::disable_provider_for_user( $user_id, 'Two_Factor_Totp' ) ) {
			return new WP_Error( 'db_error', __( 'Unable to disable TOTP provider for this user.', 'two-factor' ), array( 'status' => 500 ) );
		}

		ob_start();
		$this->user_two_factor_options( $user );
		$html = ob_get_clean();

		return array(
			'success' => true,
			'html'    => $html,
		);
	}

	/**
	 * REST API endpoint for setting up TOTP.
	 *
	 * @param WP_REST_Request $request The Rest Request object.
	 * @return WP_Error|array Array of data on success, WP_Error on error.
	 */
	public function rest_setup_totp( $request ) {
		$user_id = $request['user_id'];
		$user    = get_user_by( 'id', $user_id );

		$key  = $request['key'];
		$code = preg_replace( '/\s+/', '', $request['code'] );

		if ( ! $this->is_valid_key( $key ) ) {
			return new WP_Error( 'invalid_key', __( 'Invalid Two Factor Authentication secret key.', 'two-factor' ), array( 'status' => 400 ) );
		}

		if ( ! $this->is_valid_authcode( $key, $code ) ) {
			return new WP_Error( 'invalid_key_code', __( 'Invalid Two Factor Authentication code.', 'two-factor' ), array( 'status' => 400 ) );
		}

		if ( ! $this->set_user_totp_key( $user_id, $key ) ) {
			return new WP_Error( 'db_error', __( 'Unable to save Two Factor Authentication code. Please re-scan the QR code and enter the code provided by your application.', 'two-factor' ), array( 'status' => 500 ) );
		}

		if ( $request->get_param( 'enable_provider' ) && ! Two_Factor_Core::enable_provider_for_user( $user_id, 'Two_Factor_Totp' ) ) {
			return new WP_Error( 'db_error', __( 'Unable to enable TOTP provider for this user.', 'two-factor' ), array( 'status' => 500 ) );
		}

		ob_start();
		$this->user_two_factor_options( $user );
		$html = ob_get_clean();

		return array(
			'success' => true,
			'html'    => $html,
		);
	}

	/**
	 * Generates a URL that can be used to create a QR code.
	 *
	 * @param WP_User $user       The user to generate a URL for.
	 * @param string  $secret_key The secret key.
	 *
	 * @return string
	 */
	public static function generate_qr_code_url( $user, $secret_key ) {
		$issuer = get_bloginfo( 'name', 'display' );

		/**
		 * Filter the Issuer for the TOTP.
		 *
		 * Must follow the TOTP format for a "issuer". Do not URL Encode.
		 *
		 * @see https://github.com/google/google-authenticator/wiki/Key-Uri-Format#issuer
		 * @param string $issuer The issuer for TOTP.
		 */
		$issuer = apply_filters( 'two_factor_totp_issuer', $issuer );

		/**
		 * Filter the Label for the TOTP.
		 *
		 * Must follow the TOTP format for a "label". Do not URL Encode.
		 *
		 * @see https://github.com/google/google-authenticator/wiki/Key-Uri-Format#label
		 * @param string  $totp_title The label for the TOTP.
		 * @param WP_User $user       The User object.
		 * @param string  $issuer     The issuer of the TOTP. This should be the prefix of the result.
		 */
		$totp_title = apply_filters( 'two_factor_totp_title', $issuer . ':' . $user->user_login, $user, $issuer );

		$totp_url = add_query_arg(
			array(
				'secret' => rawurlencode( $secret_key ),
				'issuer' => rawurlencode( $issuer ),
			),
			'otpauth://totp/' . rawurlencode( $totp_title )
		);

		/**
		 * Filter the TOTP generated URL.
		 *
		 * Must follow the TOTP format. Do not URL Encode.
		 *
		 * @see https://github.com/google/google-authenticator/wiki/Key-Uri-Format
		 * @param string  $totp_url The TOTP URL.
		 * @param WP_User $user     The user object.
		 */
		$totp_url = apply_filters( 'two_factor_totp_url', $totp_url, $user );
		$totp_url = esc_url_raw( $totp_url, array( 'otpauth' ) );

		return $totp_url;
	}

	/**
	 * Display TOTP options on the user settings page.
	 *
	 * @param WP_User $user The current user being edited.
	 * @return void
	 *
	 * @codeCoverageIgnore
	 */
	public function user_two_factor_options( $user ) {
		if ( ! isset( $user->ID ) ) {
			return;
		}

		$key = $this->get_user_totp_key( $user->ID );

		wp_enqueue_script( 'two-factor-qr-code-generator' );
		wp_enqueue_script( 'wp-api-request' );
		wp_enqueue_script( 'jquery' );

		?>
		<div id="two-factor-totp-options">
		<?php
		if ( empty( $key ) ) :
			$key      = $this->generate_key();
			$totp_url = $this->generate_qr_code_url( $user, $key );

			?>
			<p>
				<?php esc_html_e( 'Please scan the QR code or manually copy the shared secret key from below to your Authenticator app:', 'two-factor' ); ?>
			</p>
			<p id="two-factor-qr-code">
				<a href="<?php echo esc_url( $totp_url ); ?>">
					<?php esc_html_e( 'Loading…', 'two-factor' ); ?>
					<img src="<?php echo esc_url( admin_url( 'images/spinner.gif' ) ); ?>" alt="" />
				</a>
			</p>
			<style>
				#two-factor-qr-code {
					/* The size of the image will change based on the length of the URL inside it. */
					min-width: 205px;
					min-height: 205px;
				}
			</style>

			<script>
				(function(){
					var qr_generator = function() {
						/*
						* 0 = Automatically select the version, to avoid going over the limit of URL
						*     length.
						* L = Least amount of error correction, because it's not needed when scanning
						*     on a monitor, and it lowers the image size.
						*/
						var qr = qrcode( 0, 'L' );

						qr.addData( <?php echo wp_json_encode( $totp_url ); ?> );
						qr.make();

						document.querySelector( '#two-factor-qr-code a' ).innerHTML = qr.createSvgTag( 5 );

						// For accessibility, markup the SVG with a title and role.
						var svg = document.querySelector( '#two-factor-qr-code a svg' ),
							title = document.createElement( 'title' );

						svg.role = 'image';
						svg.ariaLabel = <?php echo wp_json_encode( __( 'Authenticator App QR Code', 'two-factor' ) ); ?>;
						title.innerText = svg.ariaLabel;
						svg.appendChild( title );
					};

					// Run now if the document is loaded, otherwise on DOMContentLoaded.
					if ( document.readyState === 'complete' ) {
						qr_generator();
					} else {
						window.addEventListener( 'DOMContentLoaded', qr_generator );
					}
				})();
			</script>

			<p>
				<?php esc_html_e( 'Shared secret key:', 'two-factor' ); ?> <code><?php echo esc_html( $key ); ?></code>
			</p>
			<hr />
			<p>
				<?php esc_html_e( 'Enter the code generated by the Authenticator app to complete the setup:', 'two-factor' ); ?>
			</p>
			<p>
				<input type="hidden" id="two-factor-totp-key" name="two-factor-totp-key" value="<?php echo esc_attr( $key ); ?>" />
				<label for="two-factor-totp-authcode">
					<?php esc_html_e( 'Authentication Code:', 'two-factor' ); ?>
					<?php
						/* translators: Example auth code. */
						$placeholder = sprintf( __( 'eg. %s', 'two-factor' ), '123456' );
					?>
					<input type="text" inputmode="numeric" name="two-factor-totp-authcode" id="two-factor-totp-authcode" class="input" value="" size="20" pattern="[0-9 ]*" placeholder="<?php echo esc_attr( $placeholder ); ?>" autocomplete="off" />
				</label>
				<input type="submit" class="button totp-submit" name="two-factor-totp-submit" value="<?php esc_attr_e( 'Verify', 'two-factor' ); ?>" />
			</p>

			<script>
				(function($){
					// Focus the auth code input when the checkbox is clicked.
					document.getElementById('enabled-Two_Factor_Totp').addEventListener('click', function(e) {
						if ( e.target.checked ) {
							document.getElementById('two-factor-totp-authcode').focus();
						}
					});

					$('.totp-submit').click( function( e ) {
						e.preventDefault();
						var key = $('#two-factor-totp-key').val(),
							code = $('#two-factor-totp-authcode').val();

						wp.apiRequest( {
							method: 'POST',
							path: <?php echo wp_json_encode( Two_Factor_Core::REST_NAMESPACE . '/totp' ); ?>,
							data: {
								user_id: <?php echo wp_json_encode( $user->ID ); ?>,
								key: key,
								code: code,
								enable_provider: true,
							}
						} ).fail( function( response, status ) {
							var errorMessage = response.responseJSON.message || status,
								$error = $( '#totp-setup-error' );

							if ( ! $error.length ) {
								$error = $('<div class="error" id="totp-setup-error"><p></p></div>').insertAfter( $('.totp-submit') );
							}

							$error.find('p').text( errorMessage );

							$( '#enabled-Two_Factor_Totp' ).prop( 'checked', false ).trigger('change');
							$('#two-factor-totp-authcode').val('');
						} ).then( function( response ) {
							$( '#enabled-Two_Factor_Totp' ).prop( 'checked', true ).trigger('change');
							$( '#two-factor-totp-options' ).html( response.html );
						} );
					} );
				})(jQuery);
			</script>

		<?php else : ?>
			<p class="success">
				<?php esc_html_e( 'An authenticator app is currently configured. You will need to re-scan the QR code on all devices if reset.', 'two-factor' ); ?>
			</p>
			<p>
				<button type="button" class="button button-secondary reset-totp-key hide-if-no-js">
					<?php esc_html_e( 'Reset authenticator app', 'two-factor' ); ?>
				</button>
				<script>
					( function( $ ) {
						$( '.button.reset-totp-key' ).click( function( e ) {
							e.preventDefault();

							wp.apiRequest( {
								method: 'DELETE',
								path: <?php echo wp_json_encode( Two_Factor_Core::REST_NAMESPACE . '/totp' ); ?>,
								data: {
									user_id: <?php echo wp_json_encode( $user->ID ); ?>,
								}
							} ).then( function( response ) {
								$( '#enabled-Two_Factor_Totp' ).prop( 'checked', false );
								$( '#two-factor-totp-options' ).html( response.html );
							} );
						} );
					} )( jQuery );
				</script>
			</p>
		<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Get the TOTP secret key for a user.
	 *
	 * @param  int $user_id User ID.
	 *
	 * @return string
	 */
	public function get_user_totp_key( $user_id ) {
		return (string) get_user_meta( $user_id, self::SECRET_META_KEY, true );
	}

	/**
	 * Set the TOTP secret key for a user.
	 *
	 * @param int    $user_id User ID.
	 * @param string $key TOTP secret key.
	 *
	 * @return boolean If the key was stored successfully.
	 */
	public function set_user_totp_key( $user_id, $key ) {
		return update_user_meta( $user_id, self::SECRET_META_KEY, $key );
	}

	/**
	 * Delete the TOTP secret key for a user.
	 *
	 * @param  int $user_id User ID.
	 *
	 * @return boolean If the key was deleted successfully.
	 */
	public function delete_user_totp_key( $user_id ) {
		delete_user_meta( $user_id, self::LAST_SUCCESSFUL_LOGIN_META_KEY );
		return delete_user_meta( $user_id, self::SECRET_META_KEY );
	}

	/**
	 * Check if the TOTP secret key has a proper format.
	 *
	 * @param  string $key TOTP secret key.
	 *
	 * @return boolean
	 */
	public function is_valid_key( $key ) {
		$check = sprintf( '/^[%s]+$/', self::$base_32_chars );

		if ( 1 === preg_match( $check, $key ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Validates authentication.
	 *
	 * @param WP_User $user WP_User object of the logged-in user.
	 *
	 * @return bool Whether the user gave a valid code
	 */
	public function validate_authentication( $user ) {
		$code = $this->sanitize_code_from_request( 'authcode', self::DEFAULT_DIGIT_COUNT );
		if ( ! $code ) {
			return false;
		}

		return $this->validate_code_for_user( $user, $code );
	}

	/**
	 * Validates an authentication code for a given user, preventing re-use and older TOTP keys.
	 *
	 * @param WP_User $user WP_User object of the logged-in user.
	 * @param int     $code The TOTP token to validate.
	 *
	 * @return bool Whether the code is valid for the user and a newer code has not been used.
	 */
	public function validate_code_for_user( $user, $code ) {
		$valid_timestamp = $this->get_authcode_valid_ticktime(
			$this->get_user_totp_key( $user->ID ),
			$code
		);

		if ( ! $valid_timestamp ) {
			return false;
		}

		$last_totp_login = (int) get_user_meta( $user->ID, self::LAST_SUCCESSFUL_LOGIN_META_KEY, true );

		// The TOTP authentication is not valid, if we've seen the same or newer code.
		if ( $last_totp_login && $last_totp_login >= $valid_timestamp ) {
			return false;
		}

		update_user_meta( $user->ID, self::LAST_SUCCESSFUL_LOGIN_META_KEY, $valid_timestamp );

		return true;
	}


	/**
	 * Checks if a given code is valid for a given key, allowing for a certain amount of time drift.
	 *
	 * @param string $key      The share secret key to use.
	 * @param string $authcode The code to test.
	 *
	 * @return bool Whether the code is valid within the time frame.
	 */
	public static function is_valid_authcode( $key, $authcode ) {
		return (bool) self::get_authcode_valid_ticktime( $key, $authcode );
	}

	/**
	 * Checks if a given code is valid for a given key, allowing for a certain amount of time drift.
	 *
	 * @param string $key      The share secret key to use.
	 * @param string $authcode The code to test.
	 *
	 * @return false|int Returns the timestamp of the authcode on success, False otherwise.
	 */
	public static function get_authcode_valid_ticktime( $key, $authcode ) {
		/**
		 * Filter the maximum ticks to allow when checking valid codes.
		 *
		 * Ticks are the allowed offset from the correct time in 30 second increments,
		 * so the default of 4 allows codes that are two minutes to either side of server time
		 *
		 * @deprecated 0.7.0 Use {@see 'two_factor_totp_time_step_allowance'} instead.
		 * @param int $max_ticks Max ticks of time correction to allow. Default 4.
		 */
		$max_ticks = apply_filters_deprecated( 'two-factor-totp-time-step-allowance', array( self::DEFAULT_TIME_STEP_ALLOWANCE ), '0.7.0', 'two_factor_totp_time_step_allowance' );

		$max_ticks = apply_filters( 'two_factor_totp_time_step_allowance', self::DEFAULT_TIME_STEP_ALLOWANCE );

		// Array of all ticks to allow, sorted using absolute value to test closest match first.
		$ticks = range( - $max_ticks, $max_ticks );
		usort( $ticks, array( __CLASS__, 'abssort' ) );

		$time = floor( time() / self::DEFAULT_TIME_STEP_SEC );

		foreach ( $ticks as $offset ) {
			$log_time = $time + $offset;
			if ( hash_equals( self::calc_totp( $key, $log_time ), $authcode ) ) {
				// Return the tick timestamp.
				return $log_time * self::DEFAULT_TIME_STEP_SEC;
			}
		}

		return false;
	}

	/**
	 * Generates key
	 *
	 * @param int $bitsize Nume of bits to use for key.
	 *
	 * @return string $bitsize long string composed of available base32 chars.
	 */
	public static function generate_key( $bitsize = self::DEFAULT_KEY_BIT_SIZE ) {
		$bytes  = ceil( $bitsize / 8 );
		$secret = wp_generate_password( $bytes, true, true );

		return self::base32_encode( $secret );
	}

	/**
	 * Pack stuff
	 *
	 * @param string $value The value to be packed.
	 *
	 * @return string Binary packed string.
	 */
	public static function pack64( $value ) {
		// 64bit mode (PHP_INT_SIZE == 8).
		if ( PHP_INT_SIZE >= 8 ) {
			// If we're on PHP 5.6.3+ we can use the new 64bit pack functionality.
			if ( version_compare( PHP_VERSION, '5.6.3', '>=' ) && PHP_INT_SIZE >= 8 ) {
				return pack( 'J', $value ); // phpcs:ignore PHPCompatibility.ParameterValues.NewPackFormat.NewFormatFound
			}
			$highmap = 0xffffffff << 32;
			$higher  = ( $value & $highmap ) >> 32;
		} else {
			/*
			 * 32bit PHP can't shift 32 bits like that, so we have to assume 0 for the higher
			 * and not pack anything beyond it's limits.
			 */
			$higher = 0;
		}

		$lowmap = 0xffffffff;
		$lower  = $value & $lowmap;

		return pack( 'NN', $higher, $lower );
	}

	/**
	 * Calculate a valid code given the shared secret key
	 *
	 * @param string $key        The shared secret key to use for calculating code.
	 * @param mixed  $step_count The time step used to calculate the code, which is the floor of time() divided by step size.
	 * @param int    $digits     The number of digits in the returned code.
	 * @param string $hash       The hash used to calculate the code.
	 * @param int    $time_step  The size of the time step.
	 *
	 * @return string The totp code
	 */
	public static function calc_totp( $key, $step_count = false, $digits = self::DEFAULT_DIGIT_COUNT, $hash = self::DEFAULT_CRYPTO, $time_step = self::DEFAULT_TIME_STEP_SEC ) {
		$secret = self::base32_decode( $key );

		if ( false === $step_count ) {
			$step_count = floor( time() / $time_step );
		}

		$timestamp = self::pack64( $step_count );

		$hash = hash_hmac( $hash, $timestamp, $secret, true );

		$offset = ord( $hash[19] ) & 0xf;

		$code = (
				( ( ord( $hash[ $offset + 0 ] ) & 0x7f ) << 24 ) |
				( ( ord( $hash[ $offset + 1 ] ) & 0xff ) << 16 ) |
				( ( ord( $hash[ $offset + 2 ] ) & 0xff ) << 8 ) |
				( ord( $hash[ $offset + 3 ] ) & 0xff )
			) % pow( 10, $digits );

		return str_pad( $code, $digits, '0', STR_PAD_LEFT );
	}

	/**
	 * Whether this Two Factor provider is configured and available for the user specified.
	 *
	 * @param WP_User $user WP_User object of the logged-in user.
	 *
	 * @return boolean
	 */
	public function is_available_for_user( $user ) {
		// Only available if the secret key has been saved for the user.
		$key = $this->get_user_totp_key( $user->ID );

		return ! empty( $key );
	}

	/**
	 * Prints the form that prompts the user to authenticate.
	 *
	 * @param WP_User $user WP_User object of the logged-in user.
	 *
	 * @codeCoverageIgnore
	 */
	public function authentication_page( $user ) {
		require_once ABSPATH . '/wp-admin/includes/template.php';
		?>
		<p class="two-factor-prompt">
			<?php esc_html_e( 'Enter the code generated by your authenticator app.', 'two-factor' ); ?>
		</p>
		<p>
			<label for="authcode"><?php esc_html_e( 'Authentication Code:', 'two-factor' ); ?></label>
			<input type="text" inputmode="numeric" autocomplete="one-time-code" name="authcode" id="authcode" class="input authcode" value="" size="20" pattern="[0-9 ]*" placeholder="123 456" autocomplete="one-time-code" data-digits="<?php echo esc_attr( self::DEFAULT_DIGIT_COUNT ); ?>" />
		</p>
		<script type="text/javascript">
			setTimeout( function(){
				var d;
				try{
					d = document.getElementById('authcode');
					d.focus();
				} catch(e){}
			}, 200);
		</script>
		<?php
		submit_button( __( 'Verify', 'two-factor' ) );
	}

	/**
	 * Returns a base32 encoded string.
	 *
	 * @param string $string String to be encoded using base32.
	 *
	 * @return string base32 encoded string without padding.
	 */
	public static function base32_encode( $string ) {
		if ( empty( $string ) ) {
			return '';
		}

		$binary_string = '';

		foreach ( str_split( $string ) as $character ) {
			$binary_string .= str_pad( base_convert( ord( $character ), 10, 2 ), 8, '0', STR_PAD_LEFT );
		}

		$five_bit_sections = str_split( $binary_string, 5 );
		$base32_string     = '';

		foreach ( $five_bit_sections as $five_bit_section ) {
			$base32_string .= self::$base_32_chars[ base_convert( str_pad( $five_bit_section, 5, '0' ), 2, 10 ) ];
		}

		return $base32_string;
	}

	/**
	 * Decode a base32 string and return a binary representation
	 *
	 * @param string $base32_string The base 32 string to decode.
	 *
	 * @throws Exception If string contains non-base32 characters.
	 *
	 * @return string Binary representation of decoded string
	 */
	public static function base32_decode( $base32_string ) {

		$base32_string = strtoupper( $base32_string );

		if ( ! preg_match( '/^[' . self::$base_32_chars . ']+$/', $base32_string, $match ) ) {
			throw new Exception( 'Invalid characters in the base32 string.' );
		}

		$l      = strlen( $base32_string );
		$n      = 0;
		$j      = 0;
		$binary = '';

		for ( $i = 0; $i < $l; $i++ ) {

			$n  = $n << 5; // Move buffer left by 5 to make room.
			$n  = $n + strpos( self::$base_32_chars, $base32_string[ $i ] );    // Add value into buffer.
			$j += 5; // Keep track of number of bits in buffer.

			if ( $j >= 8 ) {
				$j      -= 8;
				$binary .= chr( ( $n & ( 0xFF << $j ) ) >> $j );
			}
		}

		return $binary;
	}

	/**
	 * Used with usort to sort an array by distance from 0
	 *
	 * @param int $a First array element.
	 * @param int $b Second array element.
	 *
	 * @return int -1, 0, or 1 as needed by usort
	 */
	private static function abssort( $a, $b ) {
		$a = abs( $a );
		$b = abs( $b );
		if ( $a === $b ) {
			return 0;
		}
		return ( $a < $b ) ? -1 : 1;
	}

	/**
	 * Return user meta keys to delete during plugin uninstall.
	 *
	 * @return array
	 */
	public static function uninstall_user_meta_keys() {
		return array(
			self::SECRET_META_KEY,
			self::LAST_SUCCESSFUL_LOGIN_META_KEY,
		);
	}
}
