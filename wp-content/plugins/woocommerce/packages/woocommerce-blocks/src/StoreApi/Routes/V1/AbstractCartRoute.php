<?php

namespace Automattic\WooCommerce\StoreApi\Routes\V1;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use Automattic\WooCommerce\StoreApi\SchemaController;
use Automattic\WooCommerce\StoreApi\Schemas\V1\AbstractSchema;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CartItemSchema;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CartSchema;
use Automattic\WooCommerce\StoreApi\SessionHandler;
use Automattic\WooCommerce\StoreApi\Utilities\CartController;
use Automattic\WooCommerce\StoreApi\Utilities\DraftOrderTrait;
use Automattic\WooCommerce\StoreApi\Utilities\JsonWebToken;
use Automattic\WooCommerce\StoreApi\Utilities\OrderController;

/**
 * Abstract Cart Route
 */
abstract class AbstractCartRoute extends AbstractRoute {
	use DraftOrderTrait;

	/**
	 * The route's schema.
	 *
	 * @var string
	 */
	const SCHEMA_TYPE = 'cart';

	/**
	 * Schema class instance.
	 *
	 * @var CartSchema
	 */
	protected $schema;

	/**
	 * Schema class for the cart.
	 *
	 * @var CartSchema
	 */
	protected $cart_schema;

	/**
	 * Schema class for the cart item.
	 *
	 * @var CartItemSchema
	 */
	protected $cart_item_schema;

	/**
	 * Cart controller class instance.
	 *
	 * @var CartController
	 */
	protected $cart_controller;

	/**
	 * Order controller class instance.
	 *
	 * @var OrderController
	 */
	protected $order_controller;

	/**
	 * Constructor.
	 *
	 * @param SchemaController $schema_controller Schema Controller instance.
	 * @param AbstractSchema   $schema Schema class for this route.
	 */
	public function __construct( SchemaController $schema_controller, AbstractSchema $schema ) {
		parent::__construct( $schema_controller, $schema );

		$this->cart_schema      = $this->schema_controller->get( CartSchema::IDENTIFIER );
		$this->cart_item_schema = $this->schema_controller->get( CartItemSchema::IDENTIFIER );
		$this->cart_controller  = new CartController();
		$this->order_controller = new OrderController();
	}

	/**
	 * Are we updating data or getting data?
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return boolean
	 */
	protected function is_update_request( \WP_REST_Request $request ) {
		return in_array( $request->get_method(), [ 'POST', 'PUT', 'PATCH', 'DELETE' ], true );
	}

	/**
	 * Get the route response based on the type of request.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return \WP_REST_Response
	 */
	public function get_response( \WP_REST_Request $request ) {
		$this->load_cart_session( $request );
		$this->cart_controller->calculate_totals();

		$response    = null;
		$nonce_check = $this->requires_nonce( $request ) ? $this->check_nonce( $request ) : null;

		if ( is_wp_error( $nonce_check ) ) {
			$response = $nonce_check;
		}

		if ( ! $response ) {
			try {
				$response = $this->get_response_by_request_method( $request );
			} catch ( RouteException $error ) {
				$response = $this->get_route_error_response( $error->getErrorCode(), $error->getMessage(), $error->getCode(), $error->getAdditionalData() );
			} catch ( \Exception $error ) {
				$response = $this->get_route_error_response( 'woocommerce_rest_unknown_server_error', $error->getMessage(), 500 );
			}
		}

		if ( is_wp_error( $response ) ) {
			$response = $this->error_to_response( $response );
		}

		if ( $this->is_update_request( $request ) ) {
			$this->cart_updated( $request );
		}

		return $this->add_response_headers( $response );
	}

	/**
	 * Add nonce headers to a response object.
	 *
	 * @param \WP_REST_Response $response The response object.
	 *
	 * @return \WP_REST_Response
	 */
	protected function add_response_headers( \WP_REST_Response $response ) {
		$nonce = wp_create_nonce( 'wc_store_api' );

		$response->header( 'Nonce', $nonce );
		$response->header( 'Nonce-Timestamp', time() );
		$response->header( 'User-ID', get_current_user_id() );
		$response->header( 'Cart-Token', $this->get_cart_token() );

		// The following headers are deprecated and should be removed in a future version.
		$response->header( 'X-WC-Store-API-Nonce', $nonce );

		return $response;
	}

	/**
	 * Load the cart session before handling responses.
	 *
	 * @param \WP_REST_Request $request Request object.
	 */
	protected function load_cart_session( \WP_REST_Request $request ) {
		$cart_token = $request->get_header( 'Cart-Token' );

		if ( $cart_token && JsonWebToken::validate( $cart_token, $this->get_cart_token_secret() ) ) {
			// Overrides the core session class.
			add_filter(
				'woocommerce_session_handler',
				function () {
					return SessionHandler::class;
				}
			);
		}

		$this->cart_controller->load_cart();
	}

	/**
	 * Generates a cart token for the response headers.
	 *
	 * Current namespace is used as the token Issuer.
	 * *
	 *
	 * @return string
	 */
	protected function get_cart_token() {
		return JsonWebToken::create(
			[
				'user_id' => wc()->session->get_customer_id(),
				'exp'     => $this->get_cart_token_expiration(),
				'iss'     => $this->namespace,
			],
			$this->get_cart_token_secret()
		);
	}

	/**
	 * Gets the secret for the cart token using wp_salt.
	 *
	 * @return string
	 */
	protected function get_cart_token_secret() {
		return '@' . wp_salt();
	}

	/**
	 * Gets the expiration of the cart token. Defaults to 48h.
	 *
	 * @return int
	 */
	protected function get_cart_token_expiration() {
		/**
		 * Filters the session expiration.
		 *
		 * @since 8.7.0
		 *
		 * @param int $expiration Expiration in seconds.
		 */
		return time() + intval( apply_filters( 'wc_session_expiration', DAY_IN_SECONDS * 2 ) );
	}

	/**
	 * Checks if a nonce is required for the route.
	 *
	 * @param \WP_REST_Request $request Request.
	 *
	 * @return bool
	 */
	protected function requires_nonce( \WP_REST_Request $request ) {
		return $this->is_update_request( $request );
	}

	/**
	 * Triggered after an update to cart data. Re-calculates totals and updates draft orders (if they already exist) to
	 * keep all data in sync.
	 *
	 * @param \WP_REST_Request $request Request object.
	 */
	protected function cart_updated( \WP_REST_Request $request ) {
		$draft_order = $this->get_draft_order();

		if ( $draft_order ) {
			$this->order_controller->update_order_from_cart( $draft_order );

			wc_do_deprecated_action(
				'woocommerce_blocks_cart_update_order_from_request',
				array(
					$draft_order,
					$request,
				),
				'7.2.0',
				'woocommerce_store_api_cart_update_order_from_request',
				'This action was deprecated in WooCommerce Blocks version 7.2.0. Please use woocommerce_store_api_cart_update_order_from_request instead.'
			);

			/**
			 * Fires when the order is synced with cart data from a cart route.
			 *
			 * @since 7.2.0
			 *
			 * @param \WC_Order $draft_order Order object.
			 * @param \WC_Customer $customer Customer object.
			 * @param \WP_REST_Request $request Full details about the request.
			 */
			do_action( 'woocommerce_store_api_cart_update_order_from_request', $draft_order, $request );
		}
	}

	/**
	 * For non-GET endpoints, require and validate a nonce to prevent CSRF attacks.
	 *
	 * Nonces will mismatch if the logged in session cookie is different! If using a client to test, set this cookie
	 * to match the logged in cookie in your browser.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return \WP_Error|boolean
	 */
	protected function check_nonce( \WP_REST_Request $request ) {
		$nonce = null;

		if ( $request->get_header( 'Nonce' ) ) {
			$nonce = $request->get_header( 'Nonce' );
		} elseif ( $request->get_header( 'X-WC-Store-API-Nonce' ) ) {
			$nonce = $request->get_header( 'X-WC-Store-API-Nonce' );

			// @todo Remove handling and sending of deprecated X-WC-Store-API-Nonce Header (Blocks 7.5.0)
			wc_deprecated_argument( 'X-WC-Store-API-Nonce', '7.2.0', 'Use the "Nonce" Header instead. This header will be removed after Blocks release 7.5' );
			rest_handle_deprecated_argument( 'X-WC-Store-API-Nonce', 'Use the "Nonce" Header instead. This header will be removed after Blocks release 7.5', '7.2.0' );
		}

		/**
		 * Filters the Store API nonce check.
		 *
		 * This can be used to disable the nonce check when testing API endpoints via a REST API client.
		 *
		 * @since 4.5.0
		 *
		 * @param boolean $disable_nonce_check If true, nonce checks will be disabled.
		 *
		 * @return boolean
		 */
		if ( apply_filters( 'woocommerce_store_api_disable_nonce_check', false ) ) {
			return true;
		}

		if ( null === $nonce ) {
			return $this->get_route_error_response( 'woocommerce_rest_missing_nonce', __( 'Missing the Nonce header. This endpoint requires a valid nonce.', 'woocommerce' ), 401 );
		}

		if ( ! wp_verify_nonce( $nonce, 'wc_store_api' ) ) {
			return $this->get_route_error_response( 'woocommerce_rest_invalid_nonce', __( 'Nonce is invalid.', 'woocommerce' ), 403 );
		}

		return true;
	}

	/**
	 * Get route response when something went wrong.
	 *
	 * @param string $error_code String based error code.
	 * @param string $error_message User facing error message.
	 * @param int    $http_status_code HTTP status. Defaults to 500.
	 * @param array  $additional_data Extra data (key value pairs) to expose in the error response.
	 *
	 * @return \WP_Error WP Error object.
	 */
	protected function get_route_error_response( $error_code, $error_message, $http_status_code = 500, $additional_data = [] ) {
		switch ( $http_status_code ) {
			case 409:
				// If there was a conflict, return the cart so the client can resolve it.
				$cart = $this->cart_controller->get_cart_instance();

				return new \WP_Error(
					$error_code,
					$error_message,
					array_merge(
						$additional_data,
						[
							'status' => $http_status_code,
							'cart'   => $this->cart_schema->get_item_response( $cart ),
						]
					)
				);
		}

		return new \WP_Error( $error_code, $error_message, [ 'status' => $http_status_code ] );
	}
}
