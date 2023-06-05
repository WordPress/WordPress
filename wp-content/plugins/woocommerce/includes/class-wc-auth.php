<?php
/**
 * WooCommerce Auth
 *
 * Handles wc-auth endpoint requests.
 *
 * @package WooCommerce\RestApi
 * @since   2.4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Auth class.
 */
class WC_Auth {

	/**
	 * Version.
	 *
	 * @var int
	 */
	const VERSION = 1;

	/**
	 * Setup class.
	 *
	 * @since 2.4.0
	 */
	public function __construct() {
		// Add query vars.
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );

		// Register auth endpoint.
		add_action( 'init', array( __CLASS__, 'add_endpoint' ), 0 );

		// Handle auth requests.
		add_action( 'parse_request', array( $this, 'handle_auth_requests' ), 0 );
	}

	/**
	 * Add query vars.
	 *
	 * @since  2.4.0
	 * @param  array $vars Query variables.
	 * @return string[]
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'wc-auth-version';
		$vars[] = 'wc-auth-route';
		return $vars;
	}

	/**
	 * Add auth endpoint.
	 *
	 * @since 2.4.0
	 */
	public static function add_endpoint() {
		add_rewrite_rule( '^wc-auth/v([1]{1})/(.*)?', 'index.php?wc-auth-version=$matches[1]&wc-auth-route=$matches[2]', 'top' );
	}

	/**
	 * Get scope name.
	 *
	 * @since 2.4.0
	 * @param  string $scope Permission scope.
	 * @return string
	 */
	protected function get_i18n_scope( $scope ) {
		$permissions = array(
			'read'       => __( 'Read', 'woocommerce' ),
			'write'      => __( 'Write', 'woocommerce' ),
			'read_write' => __( 'Read/Write', 'woocommerce' ),
		);

		return $permissions[ $scope ];
	}

	/**
	 * Return a list of permissions a scope allows.
	 *
	 * @since  2.4.0
	 * @param  string $scope Permission scope.
	 * @return array
	 */
	protected function get_permissions_in_scope( $scope ) {
		$permissions = array();
		switch ( $scope ) {
			case 'read':
				$permissions[] = __( 'View coupons', 'woocommerce' );
				$permissions[] = __( 'View customers', 'woocommerce' );
				$permissions[] = __( 'View orders and sales reports', 'woocommerce' );
				$permissions[] = __( 'View products', 'woocommerce' );
				break;
			case 'write':
				$permissions[] = __( 'Create webhooks', 'woocommerce' );
				$permissions[] = __( 'Create coupons', 'woocommerce' );
				$permissions[] = __( 'Create customers', 'woocommerce' );
				$permissions[] = __( 'Create orders', 'woocommerce' );
				$permissions[] = __( 'Create products', 'woocommerce' );
				break;
			case 'read_write':
				$permissions[] = __( 'Create webhooks', 'woocommerce' );
				$permissions[] = __( 'View and manage coupons', 'woocommerce' );
				$permissions[] = __( 'View and manage customers', 'woocommerce' );
				$permissions[] = __( 'View and manage orders and sales reports', 'woocommerce' );
				$permissions[] = __( 'View and manage products', 'woocommerce' );
				break;
		}
		return apply_filters( 'woocommerce_api_permissions_in_scope', $permissions, $scope );
	}

	/**
	 * Build auth urls.
	 *
	 * @since  2.4.0
	 * @param  array  $data     Data to build URL.
	 * @param  string $endpoint Endpoint.
	 * @return string
	 */
	protected function build_url( $data, $endpoint ) {
		$url = wc_get_endpoint_url( 'wc-auth/v' . self::VERSION, $endpoint, home_url( '/' ) );

		return add_query_arg(
			array(
				'app_name'     => wc_clean( $data['app_name'] ),
				'user_id'      => wc_clean( $data['user_id'] ),
				'return_url'   => rawurlencode( $this->get_formatted_url( $data['return_url'] ) ),
				'callback_url' => rawurlencode( $this->get_formatted_url( $data['callback_url'] ) ),
				'scope'        => wc_clean( $data['scope'] ),
			),
			$url
		);
	}

	/**
	 * Decode and format a URL.
	 *
	 * @param  string $url URL.
	 * @return string
	 */
	protected function get_formatted_url( $url ) {
		$url = urldecode( $url );

		if ( ! strstr( $url, '://' ) ) {
			$url = 'https://' . $url;
		}

		return $url;
	}

	/**
	 * Make validation.
	 *
	 * @since  2.4.0
	 * @throws Exception When validate fails.
	 */
	protected function make_validation() {
		$data   = array();
		$params = array(
			'app_name',
			'user_id',
			'return_url',
			'callback_url',
			'scope',
		);

		foreach ( $params as $param ) {
			if ( empty( $_REQUEST[ $param ] ) ) { // WPCS: input var ok, CSRF ok.
				/* translators: %s: parameter */
				throw new Exception( sprintf( __( 'Missing parameter %s', 'woocommerce' ), $param ) );
			}

			$data[ $param ] = wp_unslash( $_REQUEST[ $param ] ); // WPCS: input var ok, CSRF ok, sanitization ok.
		}

		if ( ! in_array( $data['scope'], array( 'read', 'write', 'read_write' ), true ) ) {
			/* translators: %s: scope */
			throw new Exception( sprintf( __( 'Invalid scope %s', 'woocommerce' ), wc_clean( $data['scope'] ) ) );
		}

		foreach ( array( 'return_url', 'callback_url' ) as $param ) {
			$param = $this->get_formatted_url( $data[ $param ] );

			if ( false === filter_var( $param, FILTER_VALIDATE_URL ) ) {
				/* translators: %s: url */
				throw new Exception( sprintf( __( 'The %s is not a valid URL', 'woocommerce' ), $param ) );
			}
		}

		$callback_url = $this->get_formatted_url( $data['callback_url'] );

		if ( 0 !== stripos( $callback_url, 'https://' ) ) {
			throw new Exception( __( 'The callback_url needs to be over SSL', 'woocommerce' ) );
		}
	}

	/**
	 * Create keys.
	 *
	 * @since  2.4.0
	 *
	 * @param  string $app_name    App name.
	 * @param  string $app_user_id User ID.
	 * @param  string $scope       Scope.
	 *
	 * @return array
	 */
	protected function create_keys( $app_name, $app_user_id, $scope ) {
		global $wpdb;

		$description = sprintf(
			'%s - API (%s)',
			wc_trim_string( wc_clean( $app_name ), 170 ),
			gmdate( 'Y-m-d H:i:s' )
		);
		$user        = wp_get_current_user();

		// Created API keys.
		$permissions     = in_array( $scope, array( 'read', 'write', 'read_write' ), true ) ? sanitize_text_field( $scope ) : 'read';
		$consumer_key    = 'ck_' . wc_rand_hash();
		$consumer_secret = 'cs_' . wc_rand_hash();

		$wpdb->insert(
			$wpdb->prefix . 'woocommerce_api_keys',
			array(
				'user_id'         => $user->ID,
				'description'     => $description,
				'permissions'     => $permissions,
				'consumer_key'    => wc_api_hash( $consumer_key ),
				'consumer_secret' => $consumer_secret,
				'truncated_key'   => substr( $consumer_key, -7 ),
			),
			array(
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
			)
		);

		return array(
			'key_id'          => $wpdb->insert_id,
			'user_id'         => $app_user_id,
			'consumer_key'    => $consumer_key,
			'consumer_secret' => $consumer_secret,
			'key_permissions' => $permissions,
		);
	}

	/**
	 * Post consumer data.
	 *
	 * @since  2.4.0
	 *
	 * @throws Exception When validation fails.
	 * @param  array  $consumer_data Consumer data.
	 * @param  string $url           URL.
	 * @return bool
	 */
	protected function post_consumer_data( $consumer_data, $url ) {
		$params = array(
			'body'    => wp_json_encode( $consumer_data ),
			'timeout' => 60,
			'headers' => array(
				'Content-Type' => 'application/json;charset=' . get_bloginfo( 'charset' ),
			),
		);

		$response = wp_safe_remote_post( esc_url_raw( $url ), $params );

		if ( is_wp_error( $response ) ) {
			throw new Exception( $response->get_error_message() );
		} elseif ( 200 !== intval( $response['response']['code'] ) ) {
			throw new Exception( __( 'An error occurred in the request and at the time were unable to send the consumer data', 'woocommerce' ) );
		}

		return true;
	}

	/**
	 * Handle auth requests.
	 *
	 * @since 2.4.0
	 * @throws Exception When auth_endpoint validation fails.
	 */
	public function handle_auth_requests() {
		global $wp;

		if ( ! empty( $_GET['wc-auth-version'] ) ) { // WPCS: input var ok, CSRF ok.
			$wp->query_vars['wc-auth-version'] = wc_clean( wp_unslash( $_GET['wc-auth-version'] ) ); // WPCS: input var ok, CSRF ok.
		}

		if ( ! empty( $_GET['wc-auth-route'] ) ) { // WPCS: input var ok, CSRF ok.
			$wp->query_vars['wc-auth-route'] = wc_clean( wp_unslash( $_GET['wc-auth-route'] ) ); // WPCS: input var ok, CSRF ok.
		}

		// wc-auth endpoint requests.
		if ( ! empty( $wp->query_vars['wc-auth-version'] ) && ! empty( $wp->query_vars['wc-auth-route'] ) ) {
			$this->auth_endpoint( $wp->query_vars['wc-auth-route'] );
		}
	}

	/**
	 * Auth endpoint.
	 *
	 * @since 2.4.0
	 * @throws Exception When validation fails.
	 * @param string $route Route.
	 */
	protected function auth_endpoint( $route ) {
		ob_start();

		$consumer_data = array();

		try {
			$route = strtolower( wc_clean( $route ) );
			$this->make_validation();

			$data = wp_unslash( $_REQUEST ); // WPCS: input var ok, CSRF ok.

			// Login endpoint.
			if ( 'login' === $route && ! is_user_logged_in() ) {
				/**
				 * If a merchant is using the WordPress SSO (handled through Jetpack)
				 * to manage their authorisation then it is likely they'll find that
				 * their username and password do not work through this form. We
				 * instead need to redirect them to the WordPress login so that they
				 * can then be redirected back here with a valid token.
				 */

				// Check if Jetpack is installed and activated.
				if ( class_exists( 'Jetpack' ) && Jetpack::connection()->is_active() ) {

					// Check if the user is using the WordPress.com SSO.
					if ( Jetpack::is_module_active( 'sso' ) ) {

						$redirect_url = $this->build_url( $data, 'authorize' );

						// Build the SSO URL.
						$login_url = Jetpack_SSO::get_instance()->build_sso_button_url(
							array(
								'redirect_to' => rawurlencode( esc_url_raw( $redirect_url ) ),
								'action'      => 'login',
							)
						);

						// Perform the redirect.
						wp_safe_redirect( $login_url );
						exit;
					}
				}

				wc_get_template(
					'auth/form-login.php',
					array(
						'app_name'     => wc_clean( $data['app_name'] ),
						'return_url'   => add_query_arg(
							array(
								'success' => 0,
								'user_id' => wc_clean( $data['user_id'] ),
							),
							$this->get_formatted_url( $data['return_url'] )
						),
						'redirect_url' => $this->build_url( $data, 'authorize' ),
					)
				);
				exit;

			} elseif ( 'login' === $route && is_user_logged_in() ) {
				// Redirect with user is logged in.
				wp_redirect( esc_url_raw( $this->build_url( $data, 'authorize' ) ) );
				exit;

			} elseif ( 'authorize' === $route && ! is_user_logged_in() ) {
				// Redirect with user is not logged in and trying to access the authorize endpoint.
				wp_redirect( esc_url_raw( $this->build_url( $data, 'login' ) ) );
				exit;

			} elseif ( 'authorize' === $route && current_user_can( 'manage_woocommerce' ) ) {
				// Authorize endpoint.
				wc_get_template(
					'auth/form-grant-access.php',
					array(
						'app_name'    => wc_clean( $data['app_name'] ),
						'return_url'  => add_query_arg(
							array(
								'success' => 0,
								'user_id' => wc_clean( $data['user_id'] ),
							),
							$this->get_formatted_url( $data['return_url'] )
						),
						'scope'       => $this->get_i18n_scope( wc_clean( $data['scope'] ) ),
						'permissions' => $this->get_permissions_in_scope( wc_clean( $data['scope'] ) ),
						'granted_url' => wp_nonce_url( $this->build_url( $data, 'access_granted' ), 'wc_auth_grant_access', 'wc_auth_nonce' ),
						'logout_url'  => wp_logout_url( $this->build_url( $data, 'login' ) ),
						'user'        => wp_get_current_user(),
					)
				);
				exit;

			} elseif ( 'access_granted' === $route && current_user_can( 'manage_woocommerce' ) ) {
				// Granted access endpoint.
				if ( ! isset( $_GET['wc_auth_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['wc_auth_nonce'] ) ), 'wc_auth_grant_access' ) ) { // WPCS: input var ok.
					throw new Exception( __( 'Invalid nonce verification', 'woocommerce' ) );
				}

				$consumer_data = $this->create_keys( $data['app_name'], $data['user_id'], $data['scope'] );
				$response      = $this->post_consumer_data( $consumer_data, $this->get_formatted_url( $data['callback_url'] ) );

				if ( $response ) {
					wp_redirect(
						esc_url_raw(
							add_query_arg(
								array(
									'success' => 1,
									'user_id' => wc_clean( $data['user_id'] ),
								),
								$this->get_formatted_url( $data['return_url'] )
							)
						)
					);
					exit;
				}
			} else {
				throw new Exception( __( 'You do not have permission to access this page', 'woocommerce' ) );
			}
		} catch ( Exception $e ) {
			$this->maybe_delete_key( $consumer_data );

			/* translators: %s: error message */
			wp_die( sprintf( esc_html__( 'Error: %s.', 'woocommerce' ), esc_html( $e->getMessage() ) ), esc_html__( 'Access denied', 'woocommerce' ), array( 'response' => 401 ) );
		}
	}

	/**
	 * Maybe delete key.
	 *
	 * @since 2.4.0
	 *
	 * @param array $key Key.
	 */
	private function maybe_delete_key( $key ) {
		global $wpdb;

		if ( isset( $key['key_id'] ) ) {
			$wpdb->delete( $wpdb->prefix . 'woocommerce_api_keys', array( 'key_id' => $key['key_id'] ), array( '%d' ) );
		}
	}
}
new WC_Auth();
