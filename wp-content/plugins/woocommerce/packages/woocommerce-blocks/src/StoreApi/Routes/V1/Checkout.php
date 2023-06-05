<?php
namespace Automattic\WooCommerce\StoreApi\Routes\V1;

use Automattic\WooCommerce\StoreApi\Payments\PaymentContext;
use Automattic\WooCommerce\StoreApi\Payments\PaymentResult;
use Automattic\WooCommerce\StoreApi\Exceptions\InvalidStockLevelsInCartException;
use Automattic\WooCommerce\StoreApi\Exceptions\InvalidCartException;
use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use Automattic\WooCommerce\StoreApi\Utilities\DraftOrderTrait;
use Automattic\WooCommerce\Checkout\Helpers\ReserveStock;
use Automattic\WooCommerce\Checkout\Helpers\ReserveStockException;

/**
 * Checkout class.
 */
class Checkout extends AbstractCartRoute {
	use DraftOrderTrait;

	/**
	 * The route identifier.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'checkout';

	/**
	 * The routes schema.
	 *
	 * @var string
	 */
	const SCHEMA_TYPE = 'checkout';

	/**
	 * Holds the current order being processed.
	 *
	 * @var \WC_Order
	 */
	private $order = null;

	/**
	 * Get the path of this REST route.
	 *
	 * @return string
	 */
	public function get_path() {
		return '/checkout';
	}

	/**
	 * Checks if a nonce is required for the route.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return bool
	 */
	protected function requires_nonce( \WP_REST_Request $request ) {
		return true;
	}

	/**
	 * Get method arguments for this REST route.
	 *
	 * @return array An array of endpoints.
	 */
	public function get_args() {
		return [
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_response' ],
				'permission_callback' => '__return_true',
				'args'                => [
					'context' => $this->get_context_param( [ 'default' => 'view' ] ),
				],
			],
			[
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'get_response' ],
				'permission_callback' => '__return_true',
				'args'                => array_merge(
					[
						'payment_data' => [
							'description' => __( 'Data to pass through to the payment method when processing payment.', 'woocommerce' ),
							'type'        => 'array',
							'items'       => [
								'type'       => 'object',
								'properties' => [
									'key'   => [
										'type' => 'string',
									],
									'value' => [
										'type' => [ 'string', 'boolean' ],
									],
								],
							],
						],
					],
					$this->schema->get_endpoint_args_for_item_schema( \WP_REST_Server::CREATABLE )
				),
			],
			'schema'      => [ $this->schema, 'get_public_item_schema' ],
			'allow_batch' => [ 'v1' => true ],
		];
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
			} catch ( InvalidCartException $error ) {
				$response = $this->get_route_error_response_from_object( $error->getError(), $error->getCode(), $error->getAdditionalData() );
			} catch ( RouteException $error ) {
				$response = $this->get_route_error_response( $error->getErrorCode(), $error->getMessage(), $error->getCode(), $error->getAdditionalData() );
			} catch ( \Exception $error ) {
				$response = $this->get_route_error_response( 'woocommerce_rest_unknown_server_error', $error->getMessage(), 500 );
			}
		}

		if ( is_wp_error( $response ) ) {
			$response = $this->error_to_response( $response );
		}

		return $this->add_response_headers( $response );
	}

	/**
	 * Prepare a single item for response. Handles setting the status based on the payment result.
	 *
	 * @param mixed            $item Item to format to schema.
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response $response Response data.
	 */
	public function prepare_item_for_response( $item, \WP_REST_Request $request ) {
		$response     = parent::prepare_item_for_response( $item, $request );
		$status_codes = [
			'success' => 200,
			'pending' => 202,
			'failure' => 400,
			'error'   => 500,
		];

		if ( isset( $item->payment_result ) && $item->payment_result instanceof PaymentResult ) {
			$response->set_status( $status_codes[ $item->payment_result->status ] ?? 200 );
		}

		return $response;
	}

	/**
	 * Convert the cart into a new draft order, or update an existing draft order, and return an updated cart response.
	 *
	 * @throws RouteException On error.
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	protected function get_route_response( \WP_REST_Request $request ) {
		$this->create_or_update_draft_order( $request );

		return $this->prepare_item_for_response(
			(object) [
				'order'          => $this->order,
				'payment_result' => new PaymentResult(),
			],
			$request
		);
	}

	/**
	 * Process an order.
	 *
	 * 1. Obtain Draft Order
	 * 2. Process Request
	 * 3. Process Customer
	 * 4. Validate Order
	 * 5. Process Payment
	 *
	 * @throws RouteException On error.
	 * @throws InvalidStockLevelsInCartException On error.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return \WP_REST_Response
	 */
	protected function get_route_post_response( \WP_REST_Request $request ) {
		/**
		 * Validate items etc are allowed in the order before the order is processed. This will fix violations and tell
		 * the customer.
		 */
		$this->cart_controller->validate_cart();

		/**
		 * Obtain Draft Order and process request data.
		 *
		 * Note: Customer data is persisted from the request first so that OrderController::update_addresses_from_cart
		 * uses the up to date customer address.
		 */
		$this->update_customer_from_request( $request );
		$this->create_or_update_draft_order( $request );
		$this->update_order_from_request( $request );

		/**
		 * Process customer data.
		 *
		 * Update order with customer details, and sign up a user account as necessary.
		 */
		$this->process_customer( $request );

		/**
		 * Validate order.
		 *
		 * This logic ensures the order is valid before payment is attempted.
		 */
		$this->order_controller->validate_order_before_payment( $this->order );

		wc_do_deprecated_action(
			'__experimental_woocommerce_blocks_checkout_order_processed',
			array(
				$this->order,
			),
			'6.3.0',
			'woocommerce_store_api_checkout_order_processed',
			'This action was deprecated in WooCommerce Blocks version 6.3.0. Please use woocommerce_store_api_checkout_order_processed instead.'
		);

		wc_do_deprecated_action(
			'woocommerce_blocks_checkout_order_processed',
			array(
				$this->order,
			),
			'7.2.0',
			'woocommerce_store_api_checkout_order_processed',
			'This action was deprecated in WooCommerce Blocks version 7.2.0. Please use woocommerce_store_api_checkout_order_processed instead.'
		);

		/**
		 * Fires before an order is processed by the Checkout Block/Store API.
		 *
		 * This hook informs extensions that $order has completed processing and is ready for payment.
		 *
		 * This is similar to existing core hook woocommerce_checkout_order_processed. We're using a new action:
		 * - To keep the interface focused (only pass $order, not passing request data).
		 * - This also explicitly indicates these orders are from checkout block/StoreAPI.
		 *
		 * @since 7.2.0
		 *
		 * @see https://github.com/woocommerce/woocommerce-gutenberg-products-block/pull/3238
		 * @example See docs/examples/checkout-order-processed.md

		 * @param \WC_Order $order Order object.
		 */
		do_action( 'woocommerce_store_api_checkout_order_processed', $this->order );

		/**
		 * Process the payment and return the results.
		 */
		$payment_result = new PaymentResult();

		if ( $this->order->needs_payment() ) {
			$this->process_payment( $request, $payment_result );
		} else {
			$this->process_without_payment( $request, $payment_result );
		}

		return $this->prepare_item_for_response(
			(object) [
				'order'          => wc_get_order( $this->order ),
				'payment_result' => $payment_result,
			],
			$request
		);
	}

	/**
	 * Get route response when something went wrong.
	 *
	 * @param string $error_code String based error code.
	 * @param string $error_message User facing error message.
	 * @param int    $http_status_code HTTP status. Defaults to 500.
	 * @param array  $additional_data  Extra data (key value pairs) to expose in the error response.
	 * @return \WP_Error WP Error object.
	 */
	protected function get_route_error_response( $error_code, $error_message, $http_status_code = 500, $additional_data = [] ) {
		$error_from_message = new \WP_Error(
			$error_code,
			$error_message
		);
		// 409 is when there was a conflict, so we return the cart so the client can resolve it.
		if ( 409 === $http_status_code ) {
			return $this->add_data_to_error_object( $error_from_message, $additional_data, $http_status_code, true );
		}
		return $this->add_data_to_error_object( $error_from_message, $additional_data, $http_status_code );
	}

	/**
	 * Get route response when something went wrong.
	 *
	 * @param \WP_Error $error_object User facing error message.
	 * @param int       $http_status_code HTTP status. Defaults to 500.
	 * @param array     $additional_data  Extra data (key value pairs) to expose in the error response.
	 * @return \WP_Error WP Error object.
	 */
	protected function get_route_error_response_from_object( $error_object, $http_status_code = 500, $additional_data = [] ) {
		// 409 is when there was a conflict, so we return the cart so the client can resolve it.
		if ( 409 === $http_status_code ) {
			return $this->add_data_to_error_object( $error_object, $additional_data, $http_status_code, true );
		}
		return $this->add_data_to_error_object( $error_object, $additional_data, $http_status_code );
	}

	/**
	 * Adds additional data to the \WP_Error object.
	 *
	 * @param \WP_Error $error The error object to add the cart to.
	 * @param array     $data The data to add to the error object.
	 * @param int       $http_status_code The HTTP status code this error should return.
	 * @param bool      $include_cart Whether the cart should be included in the error data.
	 * @returns \WP_Error The \WP_Error with the cart added.
	 */
	private function add_data_to_error_object( $error, $data, $http_status_code, bool $include_cart = false ) {
		$data = array_merge( $data, [ 'status' => $http_status_code ] );
		if ( $include_cart ) {
			$data = array_merge( $data, [ 'cart' => wc()->api->get_endpoint_data( '/wc/store/v1/cart' ) ] );
		}
		$error->add_data( $data );
		return $error;
	}

	/**
	 * Create or update a draft order based on the cart.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @throws RouteException On error.
	 */
	private function create_or_update_draft_order( \WP_REST_Request $request ) {
		$this->order = $this->get_draft_order();

		if ( ! $this->order ) {
			$this->order = $this->order_controller->create_order_from_cart();
		} else {
			$this->order_controller->update_order_from_cart( $this->order );
		}

		wc_do_deprecated_action(
			'__experimental_woocommerce_blocks_checkout_update_order_meta',
			array(
				$this->order,
			),
			'6.3.0',
			'woocommerce_store_api_checkout_update_order_meta',
			'This action was deprecated in WooCommerce Blocks version 6.3.0. Please use woocommerce_store_api_checkout_update_order_meta instead.'
		);

		wc_do_deprecated_action(
			'woocommerce_blocks_checkout_update_order_meta',
			array(
				$this->order,
			),
			'7.2.0',
			'woocommerce_store_api_checkout_update_order_meta',
			'This action was deprecated in WooCommerce Blocks version 7.2.0. Please use woocommerce_store_api_checkout_update_order_meta instead.'
		);

		/**
		 * Fires when the Checkout Block/Store API updates an order's meta data.
		 *
		 * This hook gives extensions the chance to add or update meta data on the $order.
		 * Throwing an exception from a callback attached to this action will make the Checkout Block render in a warning state, effectively preventing checkout.
		 *
		 * This is similar to existing core hook woocommerce_checkout_update_order_meta.
		 * We're using a new action:
		 * - To keep the interface focused (only pass $order, not passing request data).
		 * - This also explicitly indicates these orders are from checkout block/StoreAPI.
		 *
		 * @since 7.2.0
		 *
		 * @see https://github.com/woocommerce/woocommerce-gutenberg-products-block/pull/3686
		 *
		 * @param \WC_Order $order Order object.
		 */
		do_action( 'woocommerce_store_api_checkout_update_order_meta', $this->order );

		// Confirm order is valid before proceeding further.
		if ( ! $this->order instanceof \WC_Order ) {
			throw new RouteException(
				'woocommerce_rest_checkout_missing_order',
				__( 'Unable to create order', 'woocommerce' ),
				500
			);
		}

		// Store order ID to session.
		$this->set_draft_order_id( $this->order->get_id() );

		/**
		 * Try to reserve stock for the order.
		 *
		 * If creating a draft order on checkout entry, set the timeout to 10 mins.
		 * If POSTing to the checkout (attempting to pay), set the timeout to 60 mins (using the woocommerce_hold_stock_minutes option).
		 */
		try {
			$reserve_stock = new ReserveStock();
			$duration      = $request->get_method() === 'POST' ? (int) get_option( 'woocommerce_hold_stock_minutes', 60 ) : 10;
			$reserve_stock->reserve_stock_for_order( $this->order, $duration );
		} catch ( ReserveStockException $e ) {
			throw new RouteException(
				$e->getErrorCode(),
				$e->getMessage(),
				$e->getCode()
			);
		}
	}

	/**
	 * Updates the current customer session using data from the request (e.g. address data).
	 *
	 * Address session data is synced to the order itself later on by OrderController::update_order_from_cart()
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 */
	private function update_customer_from_request( \WP_REST_Request $request ) {
		$customer = wc()->customer;

		// Billing address is a required field.
		foreach ( $request['billing_address'] as $key => $value ) {
			if ( is_callable( [ $customer, "set_billing_$key" ] ) ) {
				$customer->{"set_billing_$key"}( $value );
			}
		}

		// If shipping address (optional field) was not provided, set it to the given billing address (required field).
		$shipping_address_values = $request['shipping_address'] ?? $request['billing_address'];

		foreach ( $shipping_address_values as $key => $value ) {
			if ( is_callable( [ $customer, "set_shipping_$key" ] ) ) {
				$customer->{"set_shipping_$key"}( $value );
			} elseif ( 'phone' === $key ) {
				$customer->update_meta_data( 'shipping_phone', $value );
			}
		}

		/**
		 * Fires when the Checkout Block/Store API updates a customer from the API request data.
		 *
		 * @since 8.2.0
		 *
		 * @param \WC_Customer $customer Customer object.
		 * @param \WP_REST_Request $request Full details about the request.
		 */
		do_action( 'woocommerce_store_api_checkout_update_customer_from_request', $customer, $request );

		$customer->save();
	}

	/**
	 * Update the current order using the posted values from the request.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 */
	private function update_order_from_request( \WP_REST_Request $request ) {
		$this->order->set_customer_note( $request['customer_note'] ?? '' );
		$this->order->set_payment_method( $this->get_request_payment_method_id( $request ) );

		wc_do_deprecated_action(
			'__experimental_woocommerce_blocks_checkout_update_order_from_request',
			array(
				$this->order,
				$request,
			),
			'6.3.0',
			'woocommerce_store_api_checkout_update_order_from_request',
			'This action was deprecated in WooCommerce Blocks version 6.3.0. Please use woocommerce_store_api_checkout_update_order_from_request instead.'
		);

		wc_do_deprecated_action(
			'woocommerce_blocks_checkout_update_order_from_request',
			array(
				$this->order,
				$request,
			),
			'7.2.0',
			'woocommerce_store_api_checkout_update_order_from_request',
			'This action was deprecated in WooCommerce Blocks version 7.2.0. Please use woocommerce_store_api_checkout_update_order_from_request instead.'
		);

		/**
		 * Fires when the Checkout Block/Store API updates an order's from the API request data.
		 *
		 * This hook gives extensions the chance to update orders based on the data in the request. This can be used in
		 * conjunction with the ExtendSchema class to post custom data and then process it.
		 *
		 * @since 7.2.0
		 *
		 * @param \WC_Order $order Order object.
		 * @param \WP_REST_Request $request Full details about the request.
		 */
		do_action( 'woocommerce_store_api_checkout_update_order_from_request', $this->order, $request );

		$this->order->save();
	}

	/**
	 * For orders which do not require payment, just update status.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @param PaymentResult    $payment_result Payment result object.
	 */
	private function process_without_payment( \WP_REST_Request $request, PaymentResult $payment_result ) {
		// Transition the order to pending, and then completed. This ensures transactional emails fire for pending_to_complete events.
		$this->order->update_status( 'pending' );
		$this->order->payment_complete();

		// Mark the payment as successful.
		$payment_result->set_status( 'success' );
		$payment_result->set_redirect_url( $this->order->get_checkout_order_received_url() );
	}

	/**
	 * Fires an action hook instructing active payment gateways to process the payment for an order and provide a result.
	 *
	 * @throws RouteException On error.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @param PaymentResult    $payment_result Payment result object.
	 */
	private function process_payment( \WP_REST_Request $request, PaymentResult $payment_result ) {
		try {
			// Transition the order to pending before making payment.
			$this->order->update_status( 'pending' );

			// Prepare the payment context object to pass through payment hooks.
			$context = new PaymentContext();
			$context->set_payment_method( $this->get_request_payment_method_id( $request ) );
			$context->set_payment_data( $this->get_request_payment_data( $request ) );
			$context->set_order( $this->order );

			/**
			 * Process payment with context.
			 *
			 * @hook woocommerce_rest_checkout_process_payment_with_context
			 *
			 * @throws \Exception If there is an error taking payment, an \Exception object can be thrown with an error message.
			 *
			 * @param PaymentContext $context        Holds context for the payment, including order ID and payment method.
			 * @param PaymentResult  $payment_result Result object for the transaction.
			 */
			do_action_ref_array( 'woocommerce_rest_checkout_process_payment_with_context', [ $context, &$payment_result ] );

			if ( ! $payment_result instanceof PaymentResult ) {
				throw new RouteException( 'woocommerce_rest_checkout_invalid_payment_result', __( 'Invalid payment result received from payment method.', 'woocommerce' ), 500 );
			}
		} catch ( \Exception $e ) {
			throw new RouteException( 'woocommerce_rest_checkout_process_payment_error', $e->getMessage(), 402 );
		}
	}

	/**
	 * Gets the chosen payment method ID from the request.
	 *
	 * @throws RouteException On error.
	 * @param \WP_REST_Request $request Request object.
	 * @return string
	 */
	private function get_request_payment_method_id( \WP_REST_Request $request ) {
		$payment_method = $this->get_request_payment_method( $request );
		return is_null( $payment_method ) ? '' : $payment_method->id;
	}

	/**
	 * Gets the chosen payment method from the request.
	 *
	 * @throws RouteException On error.
	 * @param \WP_REST_Request $request Request object.
	 * @return \WC_Payment_Gateway|null
	 */
	private function get_request_payment_method( \WP_REST_Request $request ) {
		$available_gateways      = WC()->payment_gateways->get_available_payment_gateways();
		$request_payment_method  = wc_clean( wp_unslash( $request['payment_method'] ?? '' ) );
		$requires_payment_method = $this->order->needs_payment();

		if ( empty( $request_payment_method ) ) {
			if ( $requires_payment_method ) {
				throw new RouteException(
					'woocommerce_rest_checkout_missing_payment_method',
					__( 'No payment method provided.', 'woocommerce' ),
					400
				);
			}
			return null;
		}

		if ( ! isset( $available_gateways[ $request_payment_method ] ) ) {
			throw new RouteException(
				'woocommerce_rest_checkout_payment_method_disabled',
				sprintf(
					// Translators: %s Payment method ID.
					__( 'The %s payment gateway is not available.', 'woocommerce' ),
					esc_html( $request_payment_method )
				),
				400
			);
		}

		return $available_gateways[ $request_payment_method ];
	}

	/**
	 * Gets and formats payment request data.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return array
	 */
	private function get_request_payment_data( \WP_REST_Request $request ) {
		static $payment_data = [];
		if ( ! empty( $payment_data ) ) {
			return $payment_data;
		}
		if ( ! empty( $request['payment_data'] ) ) {
			foreach ( $request['payment_data'] as $data ) {
				$payment_data[ sanitize_key( $data['key'] ) ] = wc_clean( $data['value'] );
			}
		}

		return $payment_data;
	}

	/**
	 * Order processing relating to customer account.
	 *
	 * Creates a customer account as needed (based on request & store settings) and  updates the order with the new customer ID.
	 * Updates the order with user details (e.g. address).
	 *
	 * @throws RouteException API error object with error details.
	 * @param \WP_REST_Request $request Request object.
	 */
	private function process_customer( \WP_REST_Request $request ) {
		try {
			if ( $this->should_create_customer_account( $request ) ) {
				$customer_id = $this->create_customer_account(
					$request['billing_address']['email'],
					$request['billing_address']['first_name'],
					$request['billing_address']['last_name']
				);
				// Log the customer in.
				wc_set_customer_auth_cookie( $customer_id );

				// Associate customer with the order.
				$this->order->set_customer_id( $customer_id );
				$this->order->save();
			}
		} catch ( \Exception $error ) {
			switch ( $error->getMessage() ) {
				case 'registration-error-invalid-email':
					throw new RouteException(
						'registration-error-invalid-email',
						__( 'Please provide a valid email address.', 'woocommerce' ),
						400
					);
				case 'registration-error-email-exists':
					throw new RouteException(
						'registration-error-email-exists',
						__( 'An account is already registered with your email address. Please log in before proceeding.', 'woocommerce' ),
						400
					);
			}
		}

		// Persist customer address data to account.
		$this->order_controller->sync_customer_data_with_order( $this->order );
	}

	/**
	 * Check request options and store (shop) config to determine if a user account should be created as part of order
	 * processing.
	 *
	 * @param \WP_REST_Request $request The current request object being handled.
	 * @return boolean True if a new user account should be created.
	 */
	private function should_create_customer_account( \WP_REST_Request $request ) {
		if ( is_user_logged_in() ) {
			return false;
		}

		// Return false if registration is not enabled for the store.
		if ( false === filter_var( wc()->checkout()->is_registration_enabled(), FILTER_VALIDATE_BOOLEAN ) ) {
			return false;
		}

		// Return true if the store requires an account for all purchases. Note - checkbox is not displayed to shopper in this case.
		if ( true === filter_var( wc()->checkout()->is_registration_required(), FILTER_VALIDATE_BOOLEAN ) ) {
			return true;
		}

		// Create an account if requested via the endpoint.
		if ( true === filter_var( $request['create_account'], FILTER_VALIDATE_BOOLEAN ) ) {
			// User has requested an account as part of checkout processing.
			return true;
		}

		return false;
	}

	/**
	 * Create a new account for a customer.
	 *
	 * The account is created with a generated username. The customer is sent
	 * an email notifying them about the account and containing a link to set
	 * their (initial) password.
	 *
	 * Intended as a replacement for wc_create_new_customer in WC core.
	 *
	 * @throws \Exception If an error is encountered when creating the user account.
	 *
	 * @param string $user_email The email address to use for the new account.
	 * @param string $first_name The first name to use for the new account.
	 * @param string $last_name  The last name to use for the new account.
	 *
	 * @return int User id if successful
	 */
	private function create_customer_account( $user_email, $first_name, $last_name ) {
		if ( empty( $user_email ) || ! is_email( $user_email ) ) {
			throw new \Exception( 'registration-error-invalid-email' );
		}

		if ( email_exists( $user_email ) ) {
			throw new \Exception( 'registration-error-email-exists' );
		}

		$username = wc_create_new_customer_username( $user_email );

		// Handle password creation.
		$password           = wp_generate_password();
		$password_generated = true;

		// Use WP_Error to handle registration errors.
		$errors = new \WP_Error();

		/**
		 * Fires before a customer account is registered.
		 *
		 * This hook fires before customer accounts are created and passes the form data (username, email) and an array
		 * of errors.
		 *
		 * This could be used to add extra validation logic and append errors to the array.
		 *
		 * @since 7.2.0
		 *
		 * @internal Matches filter name in WooCommerce core.
		 *
		 * @param string $username Customer username.
		 * @param string $user_email Customer email address.
		 * @param \WP_Error $errors Error object.
		 */
		do_action( 'woocommerce_register_post', $username, $user_email, $errors );

		/**
		 * Filters registration errors before a customer account is registered.
		 *
		 * This hook filters registration errors. This can be used to manipulate the array of errors before
		 * they are displayed.
		 *
		 * @since 7.2.0
		 *
		 * @internal Matches filter name in WooCommerce core.
		 *
		 * @param \WP_Error $errors Error object.
		 * @param string $username Customer username.
		 * @param string $user_email Customer email address.
		 * @return \WP_Error
		 */
		$errors = apply_filters( 'woocommerce_registration_errors', $errors, $username, $user_email );

		if ( is_wp_error( $errors ) && $errors->get_error_code() ) {
			throw new \Exception( $errors->get_error_code() );
		}

		/**
		 * Filters customer data before a customer account is registered.
		 *
		 * This hook filters customer data. It allows user data to be changed, for example, username, password, email,
		 * first name, last name, and role.
		 *
		 * @since 7.2.0
		 *
		 * @param array $customer_data An array of customer (user) data.
		 * @return array
		 */
		$new_customer_data = apply_filters(
			'woocommerce_new_customer_data',
			array(
				'user_login' => $username,
				'user_pass'  => $password,
				'user_email' => $user_email,
				'first_name' => $first_name,
				'last_name'  => $last_name,
				'role'       => 'customer',
				'source'     => 'store-api,',
			)
		);

		$customer_id = wp_insert_user( $new_customer_data );

		if ( is_wp_error( $customer_id ) ) {
			throw $this->map_create_account_error( $customer_id );
		}

		// Set account flag to remind customer to update generated password.
		update_user_option( $customer_id, 'default_password_nag', true, true );

		/**
		 * Fires after a customer account has been registered.
		 *
		 * This hook fires after customer accounts are created and passes the customer data.
		 *
		 * @since 7.2.0
		 *
		 * @internal Matches filter name in WooCommerce core.
		 *
		 * @param integer $customer_id New customer (user) ID.
		 * @param array $new_customer_data Array of customer (user) data.
		 * @param string $password_generated The generated password for the account.
		 */
		do_action( 'woocommerce_created_customer', $customer_id, $new_customer_data, $password_generated );

		return $customer_id;
	}

	/**
	 * Convert an account creation error to an exception.
	 *
	 * @param \WP_Error $error An error object.
	 * @return \Exception.
	 */
	private function map_create_account_error( \WP_Error $error ) {
		switch ( $error->get_error_code() ) {
			// WordPress core error codes.
			case 'empty_username':
			case 'invalid_username':
			case 'empty_email':
			case 'invalid_email':
			case 'email_exists':
			case 'registerfail':
				return new \Exception( 'woocommerce_rest_checkout_create_account_failure' );
		}
		return new \Exception( 'woocommerce_rest_checkout_create_account_failure' );
	}
}
