<?php
/**
 * WP Async Request
 *
 * @package WP-Background-Processing
 */
/*
Library URI: https://github.com/deliciousbrains/wp-background-processing/blob/fbbc56f2480910d7959972ec9ec0819a13c6150a/classes/wp-async-request.php
Author: Delicious Brains Inc.
Author URI: https://deliciousbrains.com/
License: GNU General Public License v2.0
License URI: https://github.com/deliciousbrains/wp-background-processing/commit/126d7945dd3d39f39cb6488ca08fe1fb66cb351a
*/

if ( ! class_exists( 'WP_Async_Request' ) ) {

	/**
	 * Abstract WP_Async_Request class.
	 *
	 * @abstract
	 */
	abstract class WP_Async_Request {

		/**
		 * Prefix
		 *
		 * (default value: 'wp')
		 *
		 * @var string
		 * @access protected
		 */
		protected $prefix = 'wp';

		/**
		 * Action
		 *
		 * (default value: 'async_request')
		 *
		 * @var string
		 * @access protected
		 */
		protected $action = 'async_request';

		/**
		 * Identifier
		 *
		 * @var mixed
		 * @access protected
		 */
		protected $identifier;

		/**
		 * Data
		 *
		 * (default value: array())
		 *
		 * @var array
		 * @access protected
		 */
		protected $data = array();

		/**
		 * Initiate new async request
		 */
		public function __construct() {
			$this->identifier = $this->prefix . '_' . $this->action;

			add_action( 'wp_ajax_' . $this->identifier, array( $this, 'maybe_handle' ) );
			add_action( 'wp_ajax_nopriv_' . $this->identifier, array( $this, 'maybe_handle' ) );
		}

		/**
		 * Set data used during the request
		 *
		 * @param array $data Data.
		 *
		 * @return $this
		 */
		public function data( $data ) {
			$this->data = $data;

			return $this;
		}

		/**
		 * Dispatch the async request
		 *
		 * @return array|WP_Error
		 */
		public function dispatch() {
			$url  = add_query_arg( $this->get_query_args(), $this->get_query_url() );
			$args = $this->get_post_args();

			return wp_remote_post( esc_url_raw( $url ), $args );
		}

		/**
		 * Get query args
		 *
		 * @return array
		 */
		protected function get_query_args() {
			if ( property_exists( $this, 'query_args' ) ) {
				return $this->query_args;
			}

			$args = array(
				'action' => $this->identifier,
				'nonce'  => wp_create_nonce( $this->identifier ),
			);

			/**
			 * Filters the post arguments used during an async request.
			 *
			 * @param array $url
			 */
			return apply_filters( $this->identifier . '_query_args', $args );
		}

		/**
		 * Get query URL
		 *
		 * @return string
		 */
		protected function get_query_url() {
			if ( property_exists( $this, 'query_url' ) ) {
				return $this->query_url;
			}

			$url = admin_url( 'admin-ajax.php' );

			/**
			 * Filters the post arguments used during an async request.
			 *
			 * @param string $url
			 */
			return apply_filters( $this->identifier . '_query_url', $url );
		}

		/**
		 * Get post args
		 *
		 * @return array
		 */
		protected function get_post_args() {
			if ( property_exists( $this, 'post_args' ) ) {
				return $this->post_args;
			}

			$args = array(
				'timeout'   => 0.01,
				'blocking'  => false,
				'body'      => $this->data,
				'cookies'   => $_COOKIE,
				'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
			);

			/**
			 * Filters the post arguments used during an async request.
			 *
			 * @param array $args
			 */
			return apply_filters( $this->identifier . '_post_args', $args );
		}

		/**
		 * Maybe handle
		 *
		 * Check for correct nonce and pass to handler.
		 */
		public function maybe_handle() {
			// Don't lock up other requests while processing
			session_write_close();

			check_ajax_referer( $this->identifier, 'nonce' );

			$this->handle();

			wp_die();
		}

		/**
		 * Handle
		 *
		 * Override this method to perform any actions required
		 * during the async request.
		 */
		abstract protected function handle();

	}
}
