<?php

if( ! class_exists( "Yoast_Update_Manager", false ) ) {

	class Yoast_Update_Manager {

		/**
		 * @var Yoast_Product
		 */
		protected $product;

		/**
		 * @var Yoast_License_Manager
		 */
		protected $license_manager;

		/**
		 * @var string
		 */
		protected $error_message = '';

		/**
		 * @var object
		 */
		protected $update_response = null;

		/**
		 * @var string The transient name storing the API response
		 */
		private $response_transient_key = '';

		/**
		 * @var string The transient name that stores failed request tries
		 */
		private $request_failed_transient_key = '';

		/**
		 * Constructor
		 *
		 * @param string $api_url     The url to the EDD shop
		 * @param string $item_name   The item name in the EDD shop
		 * @param string $license_key The (valid) license key
		 * @param string $slug        The slug. This is either the plugin main file path or the theme slug.
		 * @param string $version     The current plugin or theme version
		 * @param string $author      (optional) The item author.
		 */
		public function __construct( Yoast_Product $product, $license_manager ) {
			$this->product = $product;
			$this->license_manager = $license_manager;

			// generate transient names
			$this->response_transient_key = $this->product->get_transient_prefix() . '-update-response';
			$this->request_failed_transient_key = $this->product->get_transient_prefix() . '-update-request-failed';

			// maybe delete transient
			$this->maybe_delete_transients();
		}

		/**
		 * Deletes the various transients
		 * If we're on the update-core.php?force-check=1 page
		 */
		private function maybe_delete_transients() {
			global $pagenow;

			if( $pagenow === 'update-core.php' && isset( $_GET['force-check'] ) ) {
				delete_transient( $this->response_transient_key );
				delete_transient( $this->request_failed_transient_key );
			}
		}

		/**
		 * If the update check returned a WP_Error, show it to the user
		 */
		public function show_update_error() {

			if ( $this->error_message === '' ) {
				return;
			}

			?>
			<div class="error">
				<p><?php printf( __( '%s failed to check for updates because of the following error: <em>%s</em>', $this->product->get_text_domain() ), $this->product->get_item_name(), $this->error_message ); ?></p>
			</div>
			<?php
			}

		/**
		 * Calls the API and, if successfull, returns the object delivered by the API.
		 *
		 * @uses         get_bloginfo()
		 * @uses         wp_remote_post()
		 * @uses         is_wp_error()
		 *
		 * @return false||object
		 */
		private function call_remote_api() {

			// only check if the failed transient is not set (or if it's expired)
			if( get_transient( $this->request_failed_transient_key ) !== false ) {
				return false;
			}

			// start request process
			global $wp_version;

			// set a transient to prevent failed update checks on every page load
			// this transient will be removed if a request succeeds
			set_transient( $this->request_failed_transient_key, 'failed', 10800 );

			// setup api parameters
			$api_params = array(
				'edd_action' => 'get_version',
				'license'    => $this->license_manager->get_license_key(),
				'item_name'       => $this->product->get_item_name(),
				'wp_version'       => $wp_version,
				'item_version'     => $this->product->get_version(),
				'url' => home_url(),
				'slug' => $this->product->get_slug()
			);

			// setup request parameters
			$request_params = array(
				'method' => 'POST',
				'body'      => $api_params
			);

			require_once dirname( __FILE__ ) . '/class-api-request.php';
			$request = new Yoast_API_Request( $this->product->get_api_url(), $request_params );

			if( $request->is_valid() !== true ) {

				// show error message
				$this->error_message = $request->get_error_message();
				add_action( 'admin_notices', array( $this, 'show_update_error' ) );

				return false;
			}

			// request succeeded, delete transient indicating a request failed
			delete_transient( $this->request_failed_transient_key );

			// decode response
			$response = $request->get_response();

			// check if response returned that a given site was inactive
			if( isset( $response->license_check ) && ! empty( $response->license_check ) && $response->license_check != 'valid' ) {

				// deactivate local license
				$this->license_manager->set_license_status( 'invalid' );

				// show notice to let the user know we deactivated his/her license
				$this->error_message = __( "This site has not been activated properly on yoast.com and thus cannot check for future updates. Please activate your site with a valid license key.", $this->product->get_text_domain() );
				add_action( 'admin_notices', array( $this, 'show_update_error' ) );
			}

			$response->sections = maybe_unserialize( $response->sections );

			// store response
			set_transient( $this->response_transient_key, $response, 10800 );

			return $response;
		}

		/**
		 * Gets the remote product data (from the EDD API)
		 *
		 * - If it was previously fetched in the current requests, this gets it from the instance property
		 * - Next, it tries the 3-hour transient
		 * - Next, it calls the remote API and stores the result
		 *
		 * @return object
		 */
		protected function get_remote_data() {

			// always use property if it's set
			if( null !== $this->update_response ) {
				return $this->update_response;
			}

			// get cached remote data
			$data = $this->get_cached_remote_data();

			// if cache is empty or expired, call remote api
			if( $data === false ) {
				$data = $this->call_remote_api();
			}

			$this->update_response = $data;
			return $data;
		}

		/**
		 * Gets the remote product data from a 3-hour transient
		 *
		 * @return bool|mixed
		 */
		private function get_cached_remote_data() {

			$data = get_transient( $this->response_transient_key );

			if( $data ) {
				return $data;
			}

			return false;
		}

	}
	
}