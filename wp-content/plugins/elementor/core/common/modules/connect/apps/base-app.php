<?php
namespace Elementor\Core\Common\Modules\Connect\Apps;

use Elementor\Core\Admin\Admin_Notices;
use Elementor\Core\Common\Modules\Connect\Admin;
use Elementor\Core\Utils\Collection;
use Elementor\Core\Utils\Http;
use Elementor\Core\Utils\Str;
use Elementor\Plugin;
use Elementor\Tracker;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Base_App {

	const OPTION_NAME_PREFIX = 'elementor_connect_';

	const OPTION_CONNECT_SITE_KEY = self::OPTION_NAME_PREFIX . 'site_key';

	const SITE_URL = 'https://my.elementor.com/connect/v1';

	const API_URL = 'https://my.elementor.com/api/connect/v1';

	const HTTP_RETURN_TYPE_OBJECT = 'object';
	const HTTP_RETURN_TYPE_ARRAY = 'array';

	protected $data = [];

	protected $auth_mode = '';

	/**
	 * @var Http
	 */
	protected $http;

	/**
	 * @since 2.3.0
	 * @access protected
	 * @abstract
	 * TODO: make it public.
	 */
	abstract protected function get_slug();

	/**
	 * @since 2.8.0
	 * @access public
	 * TODO: make it abstract.
	 */
	public function get_title() {
		return $this->get_slug();
	}

	/**
	 * @since 2.3.0
	 * @access protected
	 * @abstract
	 */
	abstract protected function update_settings();

	/**
	 * @since 2.3.0
	 * @access public
	 * @static
	 */
	public static function get_class_name() {
		return get_called_class();
	}

	/**
	 * @access public
	 * @abstract
	 */
	public function render_admin_widget() {
		// PHPCS - the method get_title return a plain string.
		echo '<h2>' . $this->get_title() . '</h2>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( $this->is_connected() ) {
			$remote_user = $this->get( 'user' );
			$title = sprintf(
				/* translators: %s: Remote user. */
				esc_html__( 'Connected as %s', 'elementor' ),
				'<strong>' . esc_html( $remote_user->email ) . '</strong>'
			);
			$label = esc_html__( 'Disconnect', 'elementor' );
			$url = $this->get_admin_url( 'disconnect' );
			$attr = '';

			printf(
				'%s <a %s href="%s">%s</a>',
				// PHPCS - the variable $title is already escaped above.
				$title, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				// PHPCS - the variable $attr is a plain string.
				$attr, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				esc_attr( $url ),
				esc_html( $label )
			);
		} else {
			echo 'Not Connected';
		}

		echo '<hr>';

		$this->print_app_info();

		if ( current_user_can( 'manage_options' ) ) {
			printf( '<div><a href="%s">%s</a></div>', esc_url( $this->get_admin_url( 'reset' ) ), esc_html__( 'Reset Data', 'elementor' ) );
		}

		echo '<hr>';
	}


	/**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function get_option_name() {
		return static::OPTION_NAME_PREFIX . $this->get_slug();
	}

	/**
	 * @since 2.3.0
	 * @access public
	 */
	public function admin_notice() {
		$notices = $this->get( 'notices' );

		if ( ! $notices ) {
			return;
		}

		$this->print_notices( $notices );

		$this->delete( 'notices' );
	}


	public function get_app_token_from_cli_token( $cli_token ) {
		$response = $this->request( 'get_app_token_from_cli_token', [
			'cli_token' => $cli_token,
		] );

		if ( is_wp_error( $response ) ) {
			// PHPCS - the variable $response does not contain a user input value.
			wp_die( $response, $response->get_error_message() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		// Use state as usual.
		$_REQUEST['state'] = $this->get( 'state' );
		$_REQUEST['code'] = $response->code;
	}
	/**
	 * @since 2.3.0
	 * @access public
	 */
	public function action_authorize() {
		if ( $this->is_connected() ) {
			$this->add_notice( esc_html__( 'Already connected.', 'elementor' ), 'info' );
			$this->redirect_to_admin_page();
			return;
		}

		$this->set_client_id();
		$this->set_request_state();

		$this->redirect_to_remote_authorize_url();
	}

	public function action_reset() {
		$this->redirect_to_admin_page();
	}

	/**
	 * @since 2.3.0
	 * @access public
	 */
	public function action_get_token() {
		if ( $this->is_connected() ) {
			$this->redirect_to_admin_page();
		}

		//phpcs:ignore WordPress.Security.NonceVerification.Recommended - The user as been authorized before in 'connect'.
		$state = Utils::get_super_global_value( $_REQUEST, 'state' );

		if ( $state !== $this->get( 'state' ) ) {
			$this->add_notice( 'Get Token: Invalid Request.', 'error' );
			$this->redirect_to_admin_page();
		}

		$response = $this->request( 'get_token', [
			'grant_type' => 'authorization_code',
			'code' => Utils::get_super_global_value( $_REQUEST, 'code' ), //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			'redirect_uri' => rawurlencode( $this->get_admin_url( 'get_token' ) ),
			'client_id' => $this->get( 'client_id' ),
		] );

		if ( is_wp_error( $response ) ) {
			$notice = 'Cannot Get Token:' . $response->get_error_message();
			$this->add_notice( $notice, 'error' );
			$this->redirect_to_admin_page();
		}

		$this->delete( 'state' );
		$this->set( (array) $response );

		if ( ! empty( $response->data_share_opted_in ) && current_user_can( 'manage_options' ) ) {
			Tracker::set_opt_in( true );
		}

		$this->after_connect();

		// Add the notice *after* the method `after_connect`, so an app can redirect without the notice.
		$this->add_notice( esc_html__( 'Connected successfully.', 'elementor' ) );

		$this->redirect_to_admin_page();
	}

	/**
	 * @since 2.3.0
	 * @access public
	 */
	public function action_disconnect() {
		if ( $this->is_connected() ) {
			$this->disconnect();
			$this->add_notice( esc_html__( 'Disconnected successfully.', 'elementor' ) );
		}

		$this->redirect_to_admin_page();
	}

	/**
	 * @since 2.8.0
	 * @access public
	 */
	public function action_reconnect() {
		$this->disconnect();

		$this->action_authorize();
	}

	/**
	 * @since 2.3.0
	 * @access public
	 */
	public function get_admin_url( $action, $params = [] ) {
		$params = [
			'app' => $this->get_slug(),
			'action' => $action,
			'nonce' => wp_create_nonce( $this->get_slug() . $action ),
		] + $params;

		$admin_url = Str::encode_idn_url( get_admin_url() );
		$admin_url .= 'admin.php?page=' . Admin::PAGE_ID;

		return add_query_arg( $params, $admin_url );
	}

	/**
	 * @since 2.3.0
	 * @access public
	 */
	public function is_connected() {
		return (bool) $this->get( 'access_token' );
	}

	/**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function init() {}

	/**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function init_data() {}

	/**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function after_connect() {}

	/**
	 * @since 2.3.0
	 * @access public
	 */
	public function get( $key, $default_value = null ) {
		$this->init_data();

		return isset( $this->data[ $key ] ) ? $this->data[ $key ] : $default_value;
	}

	/**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function set( $key, $value = null ) {
		$this->init_data();

		if ( is_array( $key ) ) {
			$this->data = array_replace_recursive( $this->data, $key );
		} else {
			$this->data[ $key ] = $value;
		}

		$this->update_settings();
	}

	/**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function delete( $key = null ) {
		$this->init_data();

		if ( $key ) {
			unset( $this->data[ $key ] );
		} else {
			$this->data = [];
		}

		$this->update_settings();
	}

	/**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function add( $key, $value, $default_value = '' ) {
		$new_value = $this->get( $key, $default_value );

		if ( is_array( $new_value ) ) {
			$new_value[] = $value;
		} elseif ( is_string( $new_value ) ) {
			$new_value .= $value;
		} elseif ( is_numeric( $new_value ) ) {
			$new_value += $value;
		}

		$this->set( $key, $new_value );
	}

	/**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function add_notice( $content, $type = 'success' ) {
		$this->add( 'notices', compact( 'content', 'type' ), [] );
	}

	/**
	 * @param       $action
	 * @param array $request_body
	 * @param false $as_array
	 *
	 * @return mixed|\WP_Error
	 */
	protected function request( $action, $request_body = [], $as_array = false ) {
		$request_body = $this->get_connect_info() + $request_body;

		return $this->http_request(
			'POST',
			$action,
			[
				'timeout' => 25,
				'body' => $request_body,
				'headers' => $this->is_connected() ?
					[ 'X-Elementor-Signature' => $this->generate_signature( $request_body ) ] :
					[],
			],
			[
				'return_type' => $as_array ? static::HTTP_RETURN_TYPE_ARRAY : static::HTTP_RETURN_TYPE_OBJECT,
			]
		);
	}

	/**
	 * Get Base Connect Info
	 *
	 * Returns an array of connect info.
	 *
	 * @return array
	 */
	protected function get_base_connect_info() {
		return [
			'app' => $this->get_slug(),
			'access_token' => $this->get( 'access_token' ),
			'client_id' => $this->get( 'client_id' ),
			'local_id' => get_current_user_id(),
			'site_key' => $this->get_site_key(),
			'home_url' => trailingslashit( home_url() ),
		];
	}

	/**
	 * Get all the connect information
	 *
	 * @return array
	 */
	protected function get_connect_info() {
		$connect_info = $this->get_base_connect_info();

		$additional_info = [];

		/**
		 * Additional connect info.
		 *
		 * Filters the connection information when connecting to Elementor servers.
		 * This hook can be used to add more information or add more data.
		 *
		 * @param array    $additional_info Additional connecting information array.
		 * @param Base_App $this            The base app instance.
		 */
		$additional_info = apply_filters( 'elementor/connect/additional-connect-info', $additional_info, $this );

		return array_merge( $connect_info, $additional_info );
	}

	/**
	 * @param $endpoint
	 *
	 * @return array
	 */
	protected function generate_authentication_headers( $endpoint ) {
		$connect_info = ( new Collection( $this->get_connect_info() ) )
			->map_with_keys( function ( $value, $key ) {
				// For bc `get_connect_info` returns the connect info with underscore,
				// headers with underscore are not valid, so all the keys with underscore will be replaced to hyphen.
				return [ str_replace( '_', '-', $key ) => $value ];
			} )
			->replace_recursive( [ 'endpoint' => $endpoint ] )
			->sort_keys();

		return $connect_info
			->merge( [ 'X-Elementor-Signature' => $this->generate_signature( $connect_info->all() ) ] )
			->all();
	}

	/**
	 * Send an http request
	 *
	 * @param       $method
	 * @param       $endpoint
	 * @param array $args
	 * @param array $options
	 *
	 * @return mixed|\WP_Error
	 */
	protected function http_request( $method, $endpoint, $args = [], $options = [] ) {
		$options = wp_parse_args( $options, [
			'return_type' => static::HTTP_RETURN_TYPE_OBJECT,
		] );

		$args = array_replace_recursive( [
			'headers' => $this->is_connected() ? $this->generate_authentication_headers( $endpoint ) : [],
			'method' => $method,
			'timeout' => 10,
		], $args );

		$response = $this->http->request_with_fallback(
			$this->get_generated_urls( $endpoint ),
			$args
		);

		if ( is_wp_error( $response ) && empty( $options['with_error_data'] ) ) {
			// PHPCS - the variable $response does not contain a user input value.
			wp_die( $response, [ 'back_link' => true ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$body = wp_remote_retrieve_body( $response );
		$response_code = (int) wp_remote_retrieve_response_code( $response );

		if ( ! $response_code ) {
			return new \WP_Error( 500, 'No Response' );
		}

		// Server sent a success message without content.
		if ( 'null' === $body ) {
			$body = true;
		}

		$body = json_decode( $body, static::HTTP_RETURN_TYPE_ARRAY === $options['return_type'] );

		if ( false === $body ) {
			return new \WP_Error( 422, 'Wrong Server Response' );
		}

		if ( 201 === $response_code ) {
			return $body;
		}

		if ( 200 !== $response_code ) {
			// In case $as_array = true.
			$body = (object) $body;

			$message = isset( $body->message ) ? $body->message : wp_remote_retrieve_response_message( $response );
			$code = (int) ( isset( $body->code ) ? $body->code : $response_code );

			if ( ! $code ) {
				$code = $response_code;
			}

			if ( 401 === $code ) {
				$this->delete();

				$should_retry = ! in_array( $this->auth_mode, [ 'xhr', 'cli' ], true );

				if ( $should_retry ) {
					$this->action_authorize();
				}
			}

			if ( isset( $options['with_error_data'] ) && true === $options['with_error_data'] ) {
				return new \WP_Error( $code, $message, $body );
			}

			return new \WP_Error( $code, $message );
		}

		return $body;
	}

	/**
	 * Create a signature for the http request
	 *
	 * @param array $payload
	 *
	 * @return false|string
	 */
	private function generate_signature( $payload = [] ) {
		return hash_hmac(
			'sha256',
			wp_json_encode( $payload, JSON_NUMERIC_CHECK ),
			$this->get( 'access_token_secret' )
		);
	}

	/**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function get_api_url() {
		return static::API_URL . '/' . $this->get_slug();
	}
	/**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function get_remote_site_url() {
		return static::SITE_URL . '/' . $this->get_slug();
	}

	/**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function get_remote_authorize_url() {
		$redirect_uri = $this->get_auth_redirect_uri();

		$allowed_query_params_to_propagate = [
			'utm_source',
			'utm_medium',
			'utm_campaign',
			'utm_term',
			'utm_content',
			'source',
			'screen_hint',
		];

		$query_params = ( new Collection( $_GET ) ) // phpcs:ignore
			->only( $allowed_query_params_to_propagate )
			->merge( [
				'action' => 'authorize',
				'response_type' => 'code',
				'client_id' => $this->get( 'client_id' ),
				'auth_secret' => $this->get( 'auth_secret' ),
				'state' => $this->get( 'state' ),
				'redirect_uri' => rawurlencode( $redirect_uri ),
				'may_share_data' => current_user_can( 'manage_options' ) && ! Tracker::is_allow_track(),
				'reconnect_nonce' => wp_create_nonce( $this->get_slug() . 'reconnect' ),
			] );

		$utm_campaign = get_transient( 'elementor_core_campaign' );

		if ( ! empty( $utm_campaign ) ) {
			foreach ( [ 'source', 'medium', 'campaign' ] as $key ) {
				if ( ! empty( $utm_campaign[ $key ] ) ) {
					$query_params->offsetSet( 'utm_' . $key, $utm_campaign[ $key ] );
				}
			}
		}

		return add_query_arg( $query_params->all(), $this->get_remote_site_url() );
	}

	/**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function redirect_to_admin_page( $url = '' ) {
		if ( ! $url ) {
			$url = Admin::$url;
		}

		switch ( $this->auth_mode ) {
			case 'popup':
				$this->print_popup_close_script( $url );
				break;

			case 'cli':
			case 'rest':
				$this->admin_notice();
				die;

			default:
				wp_safe_redirect( $url );
				die;
		}
	}

	/**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function set_client_id() {
		$source = Utils::get_super_global_value( $_REQUEST, 'source' ) ?? ''; //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification is not required here.
		$response = $this->request(
			'get_client_id',
			[
				'source' => esc_attr( $source ),
			]
		);

		if ( is_wp_error( $response ) ) {
			// PHPCS - the variable $response does not contain a user input value.
			wp_die( $response, $response->get_error_message() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$this->set( 'client_id', $response->client_id );
		$this->set( 'auth_secret', $response->auth_secret );
	}

	/**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function set_request_state() {
		$this->set( 'state', wp_generate_password( 12, false ) );
	}

	protected function get_popup_success_event_data() {
		return [];
	}

	/**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function print_popup_close_script( $url ) {
		$data = $this->get_popup_success_event_data();

		?>
		<script>
			if ( opener && opener !== window ) {
				opener.jQuery( 'body' ).trigger(
					'elementor/connect/success/<?php echo esc_attr( Utils::get_super_global_value( $_REQUEST, 'callback_id' ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification is not required here. ?>',
					<?php echo wp_json_encode( $data ); ?>
				);

				opener.dispatchEvent( new CustomEvent( 'elementor/connect/success' ),
					<?php echo wp_json_encode( $data ); ?>
				);

				window.close();
				opener.focus();
			} else {
				location = '<?php echo esc_url( $url ); ?>';
			}
		</script>
		<?php
		die;
	}

	/**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function disconnect() {
		if ( $this->is_connected() ) {
			// Try update the server, but not needed to handle errors.
			$this->request( 'disconnect' );
		}

		$this->delete();
	}

	/**
	 * @since 2.3.0
	 * @access protected
	 */
	public function get_site_key() {
		$site_key = get_option( static::OPTION_CONNECT_SITE_KEY );

		if ( ! $site_key ) {
			$site_key = md5( uniqid( wp_generate_password() ) );
			update_option( static::OPTION_CONNECT_SITE_KEY, $site_key );
		}

		return $site_key;
	}

	protected function redirect_to_remote_authorize_url() {
		switch ( $this->auth_mode ) {
			case 'cli':
			case 'rest':
				$this->get_app_token_from_cli_token( Utils::get_super_global_value( $_REQUEST, 'token' ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification is not required here.
				return;
			default:
				wp_redirect( $this->get_remote_authorize_url() ); //phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect -- Safe redirect is used here.
				die;
		}
	}

	protected function get_auth_redirect_uri() {
		$redirect_uri = $this->get_admin_url( 'get_token' );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification is not required here.
		$val = Utils::get_super_global_value( $_REQUEST, 'redirect_to' );
		if ( $val ) {
			$redirect_uri = add_query_arg( [ 'redirect_to' => $val ], $redirect_uri );
		}

		switch ( $this->auth_mode ) {
			case 'popup':
				$redirect_uri = add_query_arg( [
					'mode' => 'popup',
					'callback_id' => esc_attr( Utils::get_super_global_value( $_REQUEST, 'callback_id' ) ), //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification is not required here.
				], $redirect_uri );
				break;
		}

		return $redirect_uri;
	}


	protected function print_notices( $notices ) {
		switch ( $this->auth_mode ) {
			case 'cli':
				foreach ( $notices as $notice ) {
					printf( '[%s] %s', wp_kses_post( $notice['type'] ), wp_kses_post( $notice['content'] ) );
				}
				break;

			case 'rest':
				// After `wp_send_json` the script will die.
				$this->delete( 'notices' );
				wp_send_json( $notices );
				break;

			default:
				/**
				 * @var Admin_Notices $admin_notices
				 */
				$admin_notices = Plugin::$instance->admin->get_component( 'admin-notices' );

				foreach ( $notices as $notice ) {
					$options = [
						'description' => wp_kses_post( wpautop( $notice['content'] ) ),
						'type' => $notice['type'],
						'icon' => false,
					];

					$admin_notices->print_admin_notice( $options );
				}
		}
	}

	protected function get_app_info() {
		return [];
	}

	protected function print_app_info() {
		$app_info = $this->get_app_info();

		foreach ( $app_info as $key => $item ) {
			if ( $item['value'] ) {
				$status = 'Exist';
				$color = 'green';
			} else {
				$status = 'Empty';
				$color = 'red';
			}

			// PHPCS - the values of $item['label'], $color, $status are plain strings.
			printf( '%s: <strong style="color:%s">%s</strong><br>', $item['label'], $color, $status ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	private function get_generated_urls( $endpoint ) {
		$base_urls = $this->get_api_url();

		if ( ! is_array( $base_urls ) ) {
			$base_urls = [ $base_urls ];
		}

		return array_map( function ( $base_url ) use ( $endpoint ) {
			return trailingslashit( $base_url ) . $endpoint;
		}, $base_urls );
	}

	private function init_auth_mode() {
		$is_rest = defined( 'REST_REQUEST' ) && REST_REQUEST;
		$is_ajax = wp_doing_ajax();

		if ( $is_rest || $is_ajax ) {
			// Set default to 'xhr' if rest or ajax request.
			$this->set_auth_mode( 'xhr' );
		}

		$mode = Utils::get_super_global_value( $_REQUEST, 'mode' );

		if ( $mode ) {
			$allowed_auth_modes = [
				'popup',
			];

			if ( defined( 'WP_CLI' ) && WP_CLI ) {
				$allowed_auth_modes[] = 'cli';
			}

			if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
				$allowed_auth_modes[] = 'rest';
			}

			if ( in_array( $mode, $allowed_auth_modes, true ) ) {
				$this->set_auth_mode( $mode );
			}
		}
	}

	public function set_auth_mode( $mode ) {
		$this->auth_mode = $mode;
	}

	/**
	 * @since 2.3.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'admin_notices', [ $this, 'admin_notice' ] );

		$this->init_auth_mode();

		$this->http = new Http();

		/**
		 * Allow extended apps to customize the __construct without call parent::__construct.
		 */
		$this->init();
	}
}
