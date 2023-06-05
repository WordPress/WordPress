<?php
/**
 * Webhook
 *
 * This class handles storing and retrieving webhook data from the associated.
 *
 * Webhooks are enqueued to their associated actions, delivered, and logged.
 *
 * @version  3.2.0
 * @package  WooCommerce\Webhooks
 * @since    2.2.0
 */

use Automattic\Jetpack\Constants;
use Automattic\WooCommerce\Utilities\NumberUtil;
use Automattic\WooCommerce\Utilities\OrderUtil;

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/legacy/class-wc-legacy-webhook.php';

/**
 * Webhook class.
 */
class WC_Webhook extends WC_Legacy_Webhook {

	/**
	 * Store which object IDs this webhook has processed (ie scheduled to be delivered)
	 * within the current page request.
	 *
	 * @var array
	 */
	protected $processed = array();

	/**
	 * Stores webhook data.
	 *
	 * @var array
	 */
	protected $data = array(
		'date_created'     => null,
		'date_modified'    => null,
		'status'           => 'disabled',
		'delivery_url'     => '',
		'secret'           => '',
		'name'             => '',
		'topic'            => '',
		'hooks'            => '',
		'resource'         => '',
		'event'            => '',
		'failure_count'    => 0,
		'user_id'          => 0,
		'api_version'      => 3,
		'pending_delivery' => false,
	);

	/**
	 * Load webhook data based on how WC_Webhook is called.
	 *
	 * @param WC_Webhook|int $data Webhook ID or data.
	 * @throws Exception If webhook cannot be read/found and $data is set.
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data );

		if ( $data instanceof WC_Webhook ) {
			$this->set_id( absint( $data->get_id() ) );
		} elseif ( is_numeric( $data ) ) {
			$this->set_id( $data );
		}

		$this->data_store = WC_Data_Store::load( 'webhook' );

		// If we have an ID, load the webhook from the DB.
		if ( $this->get_id() ) {
			try {
				$this->data_store->read( $this );
			} catch ( Exception $e ) {
				$this->set_id( 0 );
				$this->set_object_read( true );
			}
		} else {
			$this->set_object_read( true );
		}
	}

	/**
	 * Enqueue the hooks associated with the webhook.
	 *
	 * @since 2.2.0
	 */
	public function enqueue() {
		$hooks = $this->get_hooks();
		$url   = $this->get_delivery_url();

		if ( is_array( $hooks ) && ! empty( $url ) ) {
			foreach ( $hooks as $hook ) {
				add_action( $hook, array( $this, 'process' ) );
			}
		}
	}

	/**
	 * Process the webhook for delivery by verifying that it should be delivered.
	 * and scheduling the delivery (in the background by default, or immediately).
	 *
	 * @since  2.2.0
	 * @param  mixed $arg The first argument provided from the associated hooks.
	 * @return mixed $arg Returns the argument in case the webhook was hooked into a filter.
	 */
	public function process( $arg ) {

		// Verify that webhook should be processed for delivery.
		if ( ! $this->should_deliver( $arg ) ) {
			return;
		}

		// Mark this $arg as processed to ensure it doesn't get processed again within the current request.
		$this->processed[] = $arg;

		/**
		 * Process webhook delivery.
		 *
		 * @since 3.3.0
		 * @hooked wc_webhook_process_delivery - 10
		 */
		do_action( 'woocommerce_webhook_process_delivery', $this, $arg );

		return $arg;
	}

	/**
	 * Helper to check if the webhook should be delivered, as some hooks.
	 * (like `wp_trash_post`) will fire for every post type, not just ours.
	 *
	 * @since  2.2.0
	 * @param  mixed $arg First hook argument.
	 * @return bool       True if webhook should be delivered, false otherwise.
	 */
	private function should_deliver( $arg ) {
		$should_deliver = $this->is_active() && $this->is_valid_topic() && $this->is_valid_action( $arg ) && $this->is_valid_resource( $arg ) && ! $this->is_already_processed( $arg );

		/**
		 * Let other plugins intercept deliver for some messages queue like rabbit/zeromq.
		 *
		 * @param bool       $should_deliver True if the webhook should be sent, or false to not send it.
		 * @param WC_Webhook $this The current webhook class.
		 * @param mixed      $arg First hook argument.
		 */
		return apply_filters( 'woocommerce_webhook_should_deliver', $should_deliver, $this, $arg );
	}

	/**
	 * Returns if webhook is active.
	 *
	 * @since  3.6.0
	 * @return bool  True if validation passes.
	 */
	private function is_active() {
		return 'active' === $this->get_status();
	}

	/**
	 * Returns if topic is valid.
	 *
	 * @since  3.6.0
	 * @return bool  True if validation passes.
	 */
	private function is_valid_topic() {
		return wc_is_webhook_valid_topic( $this->get_topic() );
	}

	/**
	 * Validates the criteria for certain actions.
	 *
	 * @since  3.6.0
	 * @param  mixed $arg First hook argument.
	 * @return bool       True if validation passes.
	 */
	private function is_valid_action( $arg ) {
		$current_action = current_action();
		$return         = true;

		switch ( $current_action ) {
			case 'delete_post':
			case 'wp_trash_post':
			case 'untrashed_post':
				$return = $this->is_valid_post_action( $arg );
				break;
			case 'delete_user':
				$return = $this->is_valid_user_action( $arg );
				break;
		}

		if ( 0 === strpos( $current_action, 'woocommerce_process_shop' ) || 0 === strpos( $current_action, 'woocommerce_process_product' ) ) {
			$return = $this->is_valid_processing_action( $arg );
		}

		return $return;
	}

	/**
	 * Validates post actions.
	 *
	 * @since  3.6.0
	 * @param  mixed $arg First hook argument.
	 * @return bool       True if validation passes.
	 */
	private function is_valid_post_action( $arg ) {
		// Only deliver deleted/restored event for coupons, orders, and products.
		if ( isset( $GLOBALS['post_type'] ) && ! in_array( $GLOBALS['post_type'], array( 'shop_coupon', 'shop_order', 'product' ), true ) ) {
			return false;
		}

		// Check if is delivering for the correct resource.
		if ( isset( $GLOBALS['post_type'] ) && str_replace( 'shop_', '', $GLOBALS['post_type'] ) !== $this->get_resource() ) {
			return false;
		}
		return true;
	}

	/**
	 * Validates user actions.
	 *
	 * @since  3.6.0
	 * @param  mixed $arg First hook argument.
	 * @return bool       True if validation passes.
	 */
	private function is_valid_user_action( $arg ) {
		$user = get_userdata( absint( $arg ) );

		// Only deliver deleted customer event for users with customer role.
		if ( ! $user || ! in_array( 'customer', (array) $user->roles, true ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Validates WC processing actions.
	 *
	 * @since  3.6.0
	 * @param  mixed $arg First hook argument.
	 * @return bool       True if validation passes.
	 */
	private function is_valid_processing_action( $arg ) {
		// The `woocommerce_process_shop_*` and `woocommerce_process_product_*` hooks
		// fire for create and update of products and orders, so check the post
		// creation date to determine the actual event.
		$resource = get_post( absint( $arg ) );

		// Drafts don't have post_date_gmt so calculate it here.
		$gmt_date = get_gmt_from_date( $resource->post_date );

		// A resource is considered created when the hook is executed within 10 seconds of the post creation date.
		$resource_created = ( ( time() - 10 ) <= strtotime( $gmt_date ) );

		if ( 'created' === $this->get_event() && ! $resource_created ) {
			return false;
		} elseif ( 'updated' === $this->get_event() && $resource_created ) {
			return false;
		}
		return true;
	}

	/**
	 * Checks the resource for this webhook is valid e.g. valid post status.
	 *
	 * @since  3.6.0
	 * @param  mixed $arg First hook argument.
	 * @return bool       True if validation passes.
	 */
	private function is_valid_resource( $arg ) {
		$resource = $this->get_resource();

		if ( in_array( $resource, array( 'product', 'coupon' ), true ) ) {
			$status = get_post_status( absint( $arg ) );

			// Ignore auto drafts for all resources.
			if ( in_array( $status, array( 'auto-draft', 'new' ), true ) ) {
				return false;
			}
		}

		if ( 'order' === $resource ) {
			// Check registered order types for order types args.
			if ( ! OrderUtil::is_order( absint( $arg ), wc_get_order_types( 'order-webhooks' ) ) ) {
				return false;
			}

			$order = wc_get_order( absint( $arg ) );

			// Ignore standard drafts for orders.
			if ( in_array( $order->get_status(), array( 'draft', 'auto-draft', 'new' ), true ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Checks if the specified resource has already been queued for delivery within the current request.
	 *
	 * Helps avoid duplication of data being sent for topics that have more than one hook defined.
	 *
	 * @param mixed $arg First hook argument.
	 *
	 * @return bool
	 */
	protected function is_already_processed( $arg ) {
		return false !== array_search( $arg, $this->processed, true );
	}

	/**
	 * Deliver the webhook payload using wp_safe_remote_request().
	 *
	 * @since 2.2.0
	 * @param mixed $arg First hook argument.
	 */
	public function deliver( $arg ) {
		$start_time = microtime( true );
		$payload    = $this->build_payload( $arg );

		// Setup request args.
		$http_args = array(
			'method'      => 'POST',
			'timeout'     => MINUTE_IN_SECONDS,
			'redirection' => 0,
			'httpversion' => '1.0',
			'blocking'    => true,
			'user-agent'  => sprintf( 'WooCommerce/%s Hookshot (WordPress/%s)', Constants::get_constant( 'WC_VERSION' ), $GLOBALS['wp_version'] ),
			'body'        => trim( wp_json_encode( $payload ) ),
			'headers'     => array(
				'Content-Type' => 'application/json',
			),
			'cookies'     => array(),
		);

		$http_args = apply_filters( 'woocommerce_webhook_http_args', $http_args, $arg, $this->get_id() );

		// Add custom headers.
		$delivery_id                                      = $this->get_new_delivery_id();
		$http_args['headers']['X-WC-Webhook-Source']      = home_url( '/' ); // Since 2.6.0.
		$http_args['headers']['X-WC-Webhook-Topic']       = $this->get_topic();
		$http_args['headers']['X-WC-Webhook-Resource']    = $this->get_resource();
		$http_args['headers']['X-WC-Webhook-Event']       = $this->get_event();
		$http_args['headers']['X-WC-Webhook-Signature']   = $this->generate_signature( $http_args['body'] );
		$http_args['headers']['X-WC-Webhook-ID']          = $this->get_id();
		$http_args['headers']['X-WC-Webhook-Delivery-ID'] = $delivery_id;

		// Webhook away!
		$response = wp_safe_remote_request( $this->get_delivery_url(), $http_args );

		$duration = NumberUtil::round( microtime( true ) - $start_time, 5 );

		$this->log_delivery( $delivery_id, $http_args, $response, $duration );

		do_action( 'woocommerce_webhook_delivery', $http_args, $response, $duration, $arg, $this->get_id() );
	}

	/**
	 * Get Legacy API payload.
	 *
	 * @since  3.0.0
	 * @param  string $resource    Resource type.
	 * @param  int    $resource_id Resource ID.
	 * @param  string $event       Event type.
	 * @return array
	 */
	private function get_legacy_api_payload( $resource, $resource_id, $event ) {
		// Include & load API classes.
		WC()->api->includes();
		WC()->api->register_resources( new WC_API_Server( '/' ) );

		switch ( $resource ) {
			case 'coupon':
				$payload = WC()->api->WC_API_Coupons->get_coupon( $resource_id );
				break;

			case 'customer':
				$payload = WC()->api->WC_API_Customers->get_customer( $resource_id );
				break;

			case 'order':
				$payload = WC()->api->WC_API_Orders->get_order( $resource_id, null, apply_filters( 'woocommerce_webhook_order_payload_filters', array() ) );
				break;

			case 'product':
				// Bulk and quick edit action hooks return a product object instead of an ID.
				if ( 'updated' === $event && is_a( $resource_id, 'WC_Product' ) ) {
					$resource_id = $resource_id->get_id();
				}
				$payload = WC()->api->WC_API_Products->get_product( $resource_id );
				break;

			// Custom topics include the first hook argument.
			case 'action':
				$payload = array(
					'action' => current( $this->get_hooks() ),
					'arg'    => $resource_id,
				);
				break;

			default:
				$payload = array();
				break;
		}

		return $payload;
	}

	/**
	 * Get WP API integration payload.
	 *
	 * @since  3.0.0
	 * @param  string $resource    Resource type.
	 * @param  int    $resource_id Resource ID.
	 * @param  string $event       Event type.
	 * @return array
	 */
	private function get_wp_api_payload( $resource, $resource_id, $event ) {
		switch ( $resource ) {
			case 'coupon':
			case 'customer':
			case 'order':
			case 'product':
				// Bulk and quick edit action hooks return a product object instead of an ID.
				if ( 'product' === $resource && 'updated' === $event && is_a( $resource_id, 'WC_Product' ) ) {
					$resource_id = $resource_id->get_id();
				}

				$version = str_replace( 'wp_api_', '', $this->get_api_version() );
				$payload = wc()->api->get_endpoint_data( "/wc/{$version}/{$resource}s/{$resource_id}" );
				break;

			// Custom topics include the first hook argument.
			case 'action':
				$payload = array(
					'action' => current( $this->get_hooks() ),
					'arg'    => $resource_id,
				);
				break;

			default:
				$payload = array();
				break;
		}

		return $payload;
	}

	/**
	 * Build the payload data for the webhook.
	 *
	 * @since  2.2.0
	 * @param  mixed $resource_id First hook argument, typically the resource ID.
	 * @return mixed              Payload data.
	 */
	public function build_payload( $resource_id ) {
		// Build the payload with the same user context as the user who created
		// the webhook -- this avoids permission errors as background processing
		// runs with no user context.
		$current_user = get_current_user_id();
		wp_set_current_user( $this->get_user_id() );

		$resource = $this->get_resource();
		$event    = $this->get_event();

		// If a resource has been deleted, just include the ID.
		if ( 'deleted' === $event ) {
			$payload = array(
				'id' => $resource_id,
			);
		} else {
			if ( in_array( $this->get_api_version(), wc_get_webhook_rest_api_versions(), true ) ) {
				$payload = $this->get_wp_api_payload( $resource, $resource_id, $event );
			} else {
				$payload = $this->get_legacy_api_payload( $resource, $resource_id, $event );
			}
		}

		// Restore the current user.
		wp_set_current_user( $current_user );

		return apply_filters( 'woocommerce_webhook_payload', $payload, $resource, $resource_id, $this->get_id() );
	}

	/**
	 * Generate a base64-encoded HMAC-SHA256 signature of the payload body so the
	 * recipient can verify the authenticity of the webhook. Note that the signature
	 * is calculated after the body has already been encoded (JSON by default).
	 *
	 * @since  2.2.0
	 * @param  string $payload Payload data to hash.
	 * @return string
	 */
	public function generate_signature( $payload ) {
		$hash_algo = apply_filters( 'woocommerce_webhook_hash_algorithm', 'sha256', $payload, $this->get_id() );

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		return base64_encode( hash_hmac( $hash_algo, $payload, wp_specialchars_decode( $this->get_secret(), ENT_QUOTES ), true ) );
	}

	/**
	 * Generate a new unique hash as a delivery id based on current time and wehbook id.
	 * Return the hash for inclusion in the webhook request.
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_new_delivery_id() {
		// Since we no longer use comments to store delivery logs, we generate a unique hash instead based on current time and webhook ID.
		return wp_hash( $this->get_id() . strtotime( 'now' ) );
	}

	/**
	 * Log the delivery request/response.
	 *
	 * @since 2.2.0
	 * @param string         $delivery_id Previously created hash.
	 * @param array          $request     Request data.
	 * @param array|WP_Error $response    Response data.
	 * @param float          $duration    Request duration.
	 */
	public function log_delivery( $delivery_id, $request, $response, $duration ) {
		$logger  = wc_get_logger();
		$message = array(
			'Webhook Delivery' => array(
				'Delivery ID' => $delivery_id,
				'Date'        => date_i18n( __( 'M j, Y @ G:i', 'woocommerce' ), strtotime( 'now' ), true ),
				'URL'         => $this->get_delivery_url(),
				'Duration'    => $duration,
				'Request'     => array(
					'Method'  => $request['method'],
					'Headers' => array_merge(
						array(
							'User-Agent' => $request['user-agent'],
						),
						$request['headers']
					),
				),
				'Body'        => wp_slash( $request['body'] ),
			),
		);

		// Parse response.
		if ( is_wp_error( $response ) ) {
			$response_code    = $response->get_error_code();
			$response_message = $response->get_error_message();
			$response_headers = array();
			$response_body    = '';
		} else {
			$response_code    = wp_remote_retrieve_response_code( $response );
			$response_message = wp_remote_retrieve_response_message( $response );
			$response_headers = wp_remote_retrieve_headers( $response );
			$response_body    = wp_remote_retrieve_body( $response );
		}

		$message['Webhook Delivery']['Response'] = array(
			'Code'    => $response_code,
			'Message' => $response_message,
			'Headers' => $response_headers,
			'Body'    => $response_body,
		);

		if ( ! Constants::is_true( 'WP_DEBUG' ) ) {
			$message['Webhook Delivery']['Body']             = 'Webhook body is not logged unless WP_DEBUG mode is turned on. This is to avoid the storing of personal data in the logs.';
			$message['Webhook Delivery']['Response']['Body'] = 'Webhook body is not logged unless WP_DEBUG mode is turned on. This is to avoid the storing of personal data in the logs.';
		}

		$logger->info(
			wc_print_r( $message, true ),
			array(
				'source' => 'webhooks-delivery',
			)
		);

		// Track failures.
		// Check for a success, which is a 2xx, 301 or 302 Response Code.
		if ( intval( $response_code ) >= 200 && intval( $response_code ) < 303 ) {
			$this->set_failure_count( 0 );
			$this->save();
		} else {
			$this->failed_delivery();
		}
	}

	/**
	 * Track consecutive delivery failures and automatically disable the webhook.
	 * if more than 5 consecutive failures occur. A failure is defined as a.
	 * non-2xx response.
	 *
	 * @since 2.2.0
	 */
	private function failed_delivery() {
		$failures = $this->get_failure_count();

		if ( $failures > apply_filters( 'woocommerce_max_webhook_delivery_failures', 5 ) ) {
			$this->set_status( 'disabled' );

			do_action( 'woocommerce_webhook_disabled_due_delivery_failures', $this->get_id() );
		} else {
			$this->set_failure_count( ++$failures );
		}

		$this->save();
	}

	/**
	 * Get the delivery logs for this webhook.
	 *
	 * @since  3.3.0
	 * @return string
	 */
	public function get_delivery_logs() {
		return esc_url( add_query_arg( 'log_file', wc_get_log_file_name( 'webhooks-delivery' ), admin_url( 'admin.php?page=wc-status&tab=logs' ) ) );
	}

	/**
	 * Get the delivery log specified by the ID. The delivery log includes:
	 *
	 * + duration
	 * + summary
	 * + request method/url
	 * + request headers/body
	 * + response code/message/headers/body
	 *
	 * @since 2.2
	 * @deprecated 3.3.0
	 * @param int $delivery_id Delivery ID.
	 * @return void
	 */
	public function get_delivery_log( $delivery_id ) {
		wc_deprecated_function( 'WC_Webhook::get_delivery_log', '3.3' );
	}

	/**
	 * Send a test ping to the delivery URL, sent when the webhook is first created.
	 *
	 * @since  2.2.0
	 * @return bool|WP_Error
	 */
	public function deliver_ping() {
		$args = array(
			'user-agent' => sprintf( 'WooCommerce/%s Hookshot (WordPress/%s)', Constants::get_constant( 'WC_VERSION' ), $GLOBALS['wp_version'] ),
			'body'       => 'webhook_id=' . $this->get_id(),
		);

		$test          = wp_safe_remote_post( $this->get_delivery_url(), $args );
		$response_code = wp_remote_retrieve_response_code( $test );

		if ( is_wp_error( $test ) ) {
			/* translators: error message */
			return new WP_Error( 'error', sprintf( __( 'Error: Delivery URL cannot be reached: %s', 'woocommerce' ), $test->get_error_message() ) );
		}

		if ( 200 !== $response_code ) {
			/* translators: error message */
			return new WP_Error( 'error', sprintf( __( 'Error: Delivery URL returned response code: %s', 'woocommerce' ), absint( $response_code ) ) );
		}

		$this->set_pending_delivery( false );
		$this->save();

		return true;
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get the friendly name for the webhook.
	 *
	 * @since  2.2.0
	 * @param  string $context What the value is for.
	 *                         Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_name( $context = 'view' ) {
		return apply_filters( 'woocommerce_webhook_name', $this->get_prop( 'name', $context ), $this->get_id() );
	}

	/**
	 * Get the webhook status.
	 *
	 * - 'active' - delivers payload.
	 * - 'paused' - does not deliver payload, paused by admin.
	 * - 'disabled' - does not delivery payload, paused automatically due to consecutive failures.
	 *
	 * @since  2.2.0
	 * @param  string $context What the value is for.
	 *                         Valid values are 'view' and 'edit'.
	 * @return string status
	 */
	public function get_status( $context = 'view' ) {
		return apply_filters( 'woocommerce_webhook_status', $this->get_prop( 'status', $context ), $this->get_id() );
	}

	/**
	 * Get webhook created date.
	 *
	 * @since  3.2.0
	 * @param  string $context  What the value is for.
	 *                          Valid values are 'view' and 'edit'.
	 * @return WC_DateTime|null Object if the date is set or null if there is no date.
	 */
	public function get_date_created( $context = 'view' ) {
		return $this->get_prop( 'date_created', $context );
	}

	/**
	 * Get webhook modified date.
	 *
	 * @since  3.2.0
	 * @param  string $context  What the value is for.
	 *                          Valid values are 'view' and 'edit'.
	 * @return WC_DateTime|null Object if the date is set or null if there is no date.
	 */
	public function get_date_modified( $context = 'view' ) {
		return $this->get_prop( 'date_modified', $context );
	}

	/**
	 * Get the secret used for generating the HMAC-SHA256 signature.
	 *
	 * @since  2.2.0
	 * @param  string $context What the value is for.
	 *                         Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_secret( $context = 'view' ) {
		return apply_filters( 'woocommerce_webhook_secret', $this->get_prop( 'secret', $context ), $this->get_id() );
	}

	/**
	 * Get the webhook topic, e.g. `order.created`.
	 *
	 * @since  2.2.0
	 * @param  string $context What the value is for.
	 *                         Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_topic( $context = 'view' ) {
		return apply_filters( 'woocommerce_webhook_topic', $this->get_prop( 'topic', $context ), $this->get_id() );
	}

	/**
	 * Get the delivery URL.
	 *
	 * @since  2.2.0
	 * @param  string $context What the value is for.
	 *                         Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_delivery_url( $context = 'view' ) {
		return apply_filters( 'woocommerce_webhook_delivery_url', $this->get_prop( 'delivery_url', $context ), $this->get_id() );
	}

	/**
	 * Get the user ID for this webhook.
	 *
	 * @since  2.2.0
	 * @param  string $context What the value is for.
	 *                         Valid values are 'view' and 'edit'.
	 * @return int
	 */
	public function get_user_id( $context = 'view' ) {
		return $this->get_prop( 'user_id', $context );
	}

	/**
	 * API version.
	 *
	 * @since  3.0.0
	 * @param  string $context What the value is for.
	 *                         Valid values are 'view' and 'edit'.
	 * @return string
	 */
	public function get_api_version( $context = 'view' ) {
		$version = $this->get_prop( 'api_version', $context );

		return 0 < $version ? 'wp_api_v' . $version : 'legacy_v3';
	}

	/**
	 * Get the failure count.
	 *
	 * @since  2.2.0
	 * @param  string $context What the value is for.
	 *                         Valid values are 'view' and 'edit'.
	 * @return int
	 */
	public function get_failure_count( $context = 'view' ) {
		return $this->get_prop( 'failure_count', $context );
	}

	/**
	 * Get pending delivery.
	 *
	 * @since  3.2.0
	 * @param  string $context What the value is for.
	 *                         Valid values are 'view' and 'edit'.
	 * @return bool
	 */
	public function get_pending_delivery( $context = 'view' ) {
		return $this->get_prop( 'pending_delivery', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	 */

	/**
	 * Set webhook name.
	 *
	 * @since 3.2.0
	 * @param string $name Webhook name.
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', $name );
	}

	/**
	 * Set webhook created date.
	 *
	 * @since 3.2.0
	 * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime.
	 *                                  If the DateTime string has no timezone or offset,
	 *                                  WordPress site timezone will be assumed.
	 *                                  Null if their is no date.
	 */
	public function set_date_created( $date = null ) {
		$this->set_date_prop( 'date_created', $date );
	}

	/**
	 * Set webhook modified date.
	 *
	 * @since 3.2.0
	 * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime.
	 *                                  If the DateTime string has no timezone or offset,
	 *                                  WordPress site timezone will be assumed.
	 *                                  Null if their is no date.
	 */
	public function set_date_modified( $date = null ) {
		$this->set_date_prop( 'date_modified', $date );
	}

	/**
	 * Set status.
	 *
	 * @since 3.2.0
	 * @param string $status Status.
	 */
	public function set_status( $status ) {
		if ( ! array_key_exists( $status, wc_get_webhook_statuses() ) ) {
			$status = 'disabled';
		}

		$this->set_prop( 'status', $status );
	}

	/**
	 * Set the secret used for generating the HMAC-SHA256 signature.
	 *
	 * @since 2.2.0
	 * @param string $secret Secret.
	 */
	public function set_secret( $secret ) {
		$this->set_prop( 'secret', $secret );
	}

	/**
	 * Set the webhook topic and associated hooks.
	 * The topic resource & event are also saved separately.
	 *
	 * @since 2.2.0
	 * @param string $topic Webhook topic.
	 */
	public function set_topic( $topic ) {
		$topic = wc_clean( $topic );

		if ( ! wc_is_webhook_valid_topic( $topic ) ) {
			$topic = '';
		}

		$this->set_prop( 'topic', $topic );
	}

	/**
	 * Set the delivery URL.
	 *
	 * @since 2.2.0
	 * @param string $url Delivery URL.
	 */
	public function set_delivery_url( $url ) {
		$this->set_prop( 'delivery_url', esc_url_raw( $url, array( 'http', 'https' ) ) );
	}

	/**
	 * Set user ID.
	 *
	 * @since 3.2.0
	 * @param int $user_id User ID.
	 */
	public function set_user_id( $user_id ) {
		$this->set_prop( 'user_id', (int) $user_id );
	}

	/**
	 * Set API version.
	 *
	 * @since 3.0.0
	 * @param int|string $version REST API version.
	 */
	public function set_api_version( $version ) {
		if ( ! is_numeric( $version ) ) {
			$version = $this->data_store->get_api_version_number( $version );
		}

		$this->set_prop( 'api_version', (int) $version );
	}

	/**
	 * Set pending delivery.
	 *
	 * @since 3.2.0
	 * @param bool $pending_delivery Set true if is pending for delivery.
	 */
	public function set_pending_delivery( $pending_delivery ) {
		$this->set_prop( 'pending_delivery', (bool) $pending_delivery );
	}

	/**
	 * Set failure count.
	 *
	 * @since 3.2.0
	 * @param bool $failure_count Total of failures.
	 */
	public function set_failure_count( $failure_count ) {
		$this->set_prop( 'failure_count', intval( $failure_count ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Non-CRUD Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get the associated hook names for a topic.
	 *
	 * @since  2.2.0
	 * @param  string $topic Topic name.
	 * @return array
	 */
	private function get_topic_hooks( $topic ) {
		$topic_hooks = array(
			'coupon.created'   => array(
				'woocommerce_process_shop_coupon_meta',
				'woocommerce_new_coupon',
			),
			'coupon.updated'   => array(
				'woocommerce_process_shop_coupon_meta',
				'woocommerce_update_coupon',
			),
			'coupon.deleted'   => array(
				'wp_trash_post',
			),
			'coupon.restored'  => array(
				'untrashed_post',
			),
			'customer.created' => array(
				'user_register',
				'woocommerce_created_customer',
				'woocommerce_new_customer',
			),
			'customer.updated' => array(
				'profile_update',
				'woocommerce_update_customer',
			),
			'customer.deleted' => array(
				'delete_user',
			),
			'order.created'    => array(
				'woocommerce_new_order',
			),
			'order.updated'    => array(
				'woocommerce_update_order',
				'woocommerce_order_refunded',
			),
			'order.deleted'    => array(
				'wp_trash_post',
			),
			'order.restored'   => array(
				'untrashed_post',
			),
			'product.created'  => array(
				'woocommerce_process_product_meta',
				'woocommerce_new_product',
				'woocommerce_new_product_variation',
			),
			'product.updated'  => array(
				'woocommerce_process_product_meta',
				'woocommerce_update_product',
				'woocommerce_update_product_variation',
			),
			'product.deleted'  => array(
				'wp_trash_post',
			),
			'product.restored' => array(
				'untrashed_post',
			),
		);

		$topic_hooks = apply_filters( 'woocommerce_webhook_topic_hooks', $topic_hooks, $this );

		return isset( $topic_hooks[ $topic ] ) ? $topic_hooks[ $topic ] : array();
	}

	/**
	 * Get the hook names for the webhook.
	 *
	 * @since  2.2.0
	 * @return array
	 */
	public function get_hooks() {
		if ( 'action' === $this->get_resource() ) {
			$hooks = array( $this->get_event() );
		} else {
			$hooks = $this->get_topic_hooks( $this->get_topic() );
		}

		return apply_filters( 'woocommerce_webhook_hooks', $hooks, $this->get_id() );
	}

	/**
	 * Get the resource for the webhook, e.g. `order`.
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_resource() {
		$topic = explode( '.', $this->get_topic() );

		return apply_filters( 'woocommerce_webhook_resource', $topic[0], $this->get_id() );
	}

	/**
	 * Get the event for the webhook, e.g. `created`.
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_event() {
		$topic = explode( '.', $this->get_topic() );

		return apply_filters( 'woocommerce_webhook_event', isset( $topic[1] ) ? $topic[1] : '', $this->get_id() );
	}

	/**
	 * Get the webhook i18n status.
	 *
	 * @return string
	 */
	public function get_i18n_status() {
		$status   = $this->get_status();
		$statuses = wc_get_webhook_statuses();

		return isset( $statuses[ $status ] ) ? $statuses[ $status ] : $status;
	}
}
