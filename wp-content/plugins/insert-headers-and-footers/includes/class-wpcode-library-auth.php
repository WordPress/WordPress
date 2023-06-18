<?php
/**
 * Class for handling the WPCode library authentication.
 *
 * @package WPCode
 */

/**
 * Class WPCode_Library_Auth.
 */
class WPCode_Library_Auth {
	/**
	 * The base api URL.
	 *
	 * @var string
	 */
	public $library_url = 'https://library.wpcode.com';

	/**
	 * Is the current plugin authenticated with the WPCode Library?
	 *
	 * @var bool
	 */
	private $has_auth;

	/**
	 * The api key used for authenticated requests to the library.
	 *
	 * @var string
	 */
	private $auth_key;

	/**
	 * The auth data from the db.
	 *
	 * @var array
	 */
	private $auth_data;

	/**
	 * Library auth constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_wpcode_library_start_auth', array( $this, 'ajax_auth_url' ) );
		add_action( 'wp_ajax_wpcode_library_store_auth', array( $this, 'store_auth_key' ) );
		add_action( 'wp_ajax_wpcode_library_delete_auth', array( $this, 'delete_auth' ) );
	}

	/**
	 * Ajax handler that returns the auth url used to start the Connect process.
	 *
	 * @return void
	 */
	public function ajax_auth_url() {
		check_ajax_referer( 'wpcode_admin' );

		if ( ! current_user_can( 'wpcode_activate_snippets' ) ) {
			wp_send_json_error( esc_html__( 'You do not have permissions to connect WPCode to the library.', 'insert-headers-and-footers' ) );
		}

		$site_name = get_bloginfo( 'name' );
		if ( empty( $site_name ) ) {
			$site_name = __( 'Your WordPress Site', 'insert-headers-and-footers' );
		}

		// This is needed, so we don't run into issues with special characters.
		// Base64 encode without padding for better compatibility between PHP versions.
		$site_name = rtrim( strtr( base64_encode( $site_name ), '+/', '-_' ), '=' );

		$auth_url = add_query_arg(
			array(
				'site'    => $site_name,
				'version' => WPCODE_VERSION,
			),
			$this->get_api_url( 'connect' )
		);

		wp_send_json_success(
			array(
				'url' => $auth_url,
			)
		);
	}

	/**
	 * Get the full URL to an API endpoint by passing the path.
	 *
	 * @param string $path The path for the API endpoint.
	 *
	 * @return string
	 */
	public function get_api_url( $path ) {
		return trailingslashit( $this->library_url ) . 'api/' . $path;
	}

	/**
	 * Ajax handler to save the auth API key.
	 *
	 * @return void
	 */
	public function store_auth_key() {
		check_ajax_referer( 'wpcode_admin' );

		if ( ! current_user_can( 'wpcode_activate_snippets' ) ) {
			wp_send_json_error( esc_html__( 'You do not have permissions to connect WPCode to the library.', 'insert-headers-and-footers' ) );
		}

		$key               = ! empty( $_POST['key'] ) ? sanitize_key( $_POST['key'] ) : false;
		$username          = ! empty( $_POST['username'] ) ? sanitize_user( wp_unslash( $_POST['username'] ) ) : false;
		$origin            = ! empty( $_POST['origin'] ) ? esc_url_raw( wp_unslash( $_POST['origin'] ) ) : false;
		$deploy_snippet_id = ! empty( $_POST['deploy_snippet_id'] ) ? sanitize_key( $_POST['deploy_snippet_id'] ) : false;

		if ( ! $key || $this->library_url !== $origin ) {
			wp_send_json_error();
		}

		// Don't autoload this as we'll only need it on some pages and in specific requests.
		update_option(
			'wpcode_library_api_auth',
			array(
				'key'          => $key,
				'username'     => $username,
				'connected_at' => time(),
			),
			false
		);

		if ( ! empty( $deploy_snippet_id ) ) {
			// If we have a snippet id from the deployment process, set that as a transient to show a notice, so they can pick up where they started.
			set_transient( 'wpcode_deploy_snippet_id', $deploy_snippet_id, HOUR_IN_SECONDS );
		}

		// Reset the auth data.
		unset( $this->auth_data );
		unset( $this->auth_key );
		unset( $this->has_auth );

		do_action( 'wpcode_library_api_auth_connected' );

		wp_send_json_success(
			array(
				'title' => __( 'Authentication successfully completed', 'insert-headers-and-footers' ),
				'text'  => __( 'Reloading page, please wait.', 'insert-headers-and-footers' ),
			)
		);
	}

	/**
	 * Ajax handler to delete the auth data and disconnect the site from the WPCode Library.
	 *
	 * @return void
	 */
	public function delete_auth() {
		check_ajax_referer( 'wpcode_admin' );

		if ( ! current_user_can( 'wpcode_activate_snippets' ) ) {
			wp_send_json_error( esc_html__( 'You do not have permissions to connect WPCode to the library.', 'insert-headers-and-footers' ) );
		}

		if ( delete_option( 'wpcode_library_api_auth' ) ) {
			do_action( 'wpcode_library_api_auth_deleted' );
			wp_send_json_success();
		}

		wp_send_json_error();
	}

	/**
	 * Check if the site is authenticated.
	 *
	 * @return bool
	 */
	public function has_auth() {
		if ( ! isset( $this->has_auth ) ) {
			$auth_key = $this->get_auth_key();

			$this->has_auth = ! empty( $auth_key );
		}

		return $this->has_auth;
	}

	/**
	 * The auth key.
	 *
	 * @return bool|string
	 */
	public function get_auth_key() {
		if ( ! isset( $this->auth_key ) ) {
			$data           = $this->get_auth_data();
			$this->auth_key = isset( $data['key'] ) ? $data['key'] : false;
		}

		return $this->auth_key;
	}

	/**
	 * Get the auth data from the db.
	 *
	 * @return array|bool
	 */
	public function get_auth_data() {
		if ( ! isset( $this->auth_data ) ) {
			$this->auth_data = get_option( 'wpcode_library_api_auth', false );
		}

		return $this->auth_data;
	}

	/**
	 * The auth username.
	 *
	 * @return bool|string
	 */
	public function get_auth_username() {
		$data = $this->get_auth_data();

		return isset( $data['username'] ) ? $data['username'] : false;
	}
}
