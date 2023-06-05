<?php
namespace Automattic\WooCommerce\StoreApi\Schemas\V1;

use Automattic\WooCommerce\StoreApi\SchemaController;
use Automattic\WooCommerce\StoreApi\Payments\PaymentResult;
use Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema;


/**
 * CheckoutSchema class.
 */
class CheckoutSchema extends AbstractSchema {
	/**
	 * The schema item name.
	 *
	 * @var string
	 */
	protected $title = 'checkout';

	/**
	 * The schema item identifier.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'checkout';

	/**
	 * Billing address schema instance.
	 *
	 * @var BillingAddressSchema
	 */
	protected $billing_address_schema;

	/**
	 * Shipping address schema instance.
	 *
	 * @var ShippingAddressSchema
	 */
	protected $shipping_address_schema;

	/**
	 * Image Attachment schema instance.
	 *
	 * @var ImageAttachmentSchema
	 */
	protected $image_attachment_schema;

	/**
	 * Constructor.
	 *
	 * @param ExtendSchema     $extend Rest Extending instance.
	 * @param SchemaController $controller Schema Controller instance.
	 */
	public function __construct( ExtendSchema $extend, SchemaController $controller ) {
		parent::__construct( $extend, $controller );
		$this->billing_address_schema  = $this->controller->get( BillingAddressSchema::IDENTIFIER );
		$this->shipping_address_schema = $this->controller->get( ShippingAddressSchema::IDENTIFIER );
		$this->image_attachment_schema = $this->controller->get( ImageAttachmentSchema::IDENTIFIER );
	}

	/**
	 * Checkout schema properties.
	 *
	 * @return array
	 */
	public function get_properties() {
		return [
			'order_id'          => [
				'description' => __( 'The order ID to process during checkout.', 'woocommerce' ),
				'type'        => 'integer',
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
			],
			'status'            => [
				'description' => __( 'Order status. Payment providers will update this value after payment.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
			],
			'order_key'         => [
				'description' => __( 'Order key used to check validity or protect access to certain order data.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
			],
			'customer_note'     => [
				'description' => __( 'Note added to the order by the customer during checkout.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => [ 'view', 'edit' ],
			],
			'customer_id'       => [
				'description' => __( 'Customer ID if registered. Will return 0 for guests.', 'woocommerce' ),
				'type'        => 'integer',
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
			],
			'billing_address'   => [
				'description' => __( 'Billing address.', 'woocommerce' ),
				'type'        => 'object',
				'context'     => [ 'view', 'edit' ],
				'properties'  => $this->billing_address_schema->get_properties(),
				'arg_options' => [
					'sanitize_callback' => [ $this->billing_address_schema, 'sanitize_callback' ],
					'validate_callback' => [ $this->billing_address_schema, 'validate_callback' ],
				],
				'required'    => true,
			],
			'shipping_address'  => [
				'description' => __( 'Shipping address.', 'woocommerce' ),
				'type'        => 'object',
				'context'     => [ 'view', 'edit' ],
				'properties'  => $this->shipping_address_schema->get_properties(),
				'arg_options' => [
					'sanitize_callback' => [ $this->shipping_address_schema, 'sanitize_callback' ],
					'validate_callback' => [ $this->shipping_address_schema, 'validate_callback' ],
				],
			],
			'payment_method'    => [
				'description' => __( 'The ID of the payment method being used to process the payment.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => [ 'view', 'edit' ],
				'enum'        => array_values( wp_list_pluck( WC()->payment_gateways->get_available_payment_gateways(), 'id' ) ),
			],
			'create_account'    => [
				'description' => __( 'Whether to create a new user account as part of order processing.', 'woocommerce' ),
				'type'        => 'boolean',
				'context'     => [ 'view', 'edit' ],
			],
			'payment_result'    => [
				'description' => __( 'Result of payment processing, or false if not yet processed.', 'woocommerce' ),
				'type'        => 'object',
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
				'properties'  => [
					'payment_status'  => [
						'description' => __( 'Status of the payment returned by the gateway. One of success, pending, failure, error.', 'woocommerce' ),
						'readonly'    => true,
						'type'        => 'string',
					],
					'payment_details' => [
						'description' => __( 'An array of data being returned from the payment gateway.', 'woocommerce' ),
						'readonly'    => true,
						'type'        => 'array',
						'items'       => [
							'type'       => 'object',
							'properties' => [
								'key'   => [
									'type' => 'string',
								],
								'value' => [
									'type' => 'string',
								],
							],
						],
					],
					'redirect_url'    => [
						'description' => __( 'A URL to redirect the customer after checkout. This could be, for example, a link to the payment processors website.', 'woocommerce' ),
						'readonly'    => true,
						'type'        => 'string',
					],
				],
			],
			self::EXTENDING_KEY => $this->get_extended_schema( self::IDENTIFIER ),
		];
	}

	/**
	 * Return the response for checkout.
	 *
	 * @param object $item Results from checkout action.
	 * @return array
	 */
	public function get_item_response( $item ) {
		return $this->get_checkout_response( $item->order, $item->payment_result );
	}

	/**
	 * Get the checkout response based on the current order and any payments.
	 *
	 * @param \WC_Order     $order Order object.
	 * @param PaymentResult $payment_result Payment result object.
	 * @return array
	 */
	protected function get_checkout_response( \WC_Order $order, PaymentResult $payment_result = null ) {
		return [
			'order_id'          => $order->get_id(),
			'status'            => $order->get_status(),
			'order_key'         => $order->get_order_key(),
			'customer_note'     => $order->get_customer_note(),
			'customer_id'       => $order->get_customer_id(),
			'billing_address'   => $this->billing_address_schema->get_item_response( $order ),
			'shipping_address'  => $this->shipping_address_schema->get_item_response( $order ),
			'payment_method'    => $order->get_payment_method(),
			'payment_result'    => [
				'payment_status'  => $payment_result->status,
				'payment_details' => $this->prepare_payment_details_for_response( $payment_result->payment_details ),
				'redirect_url'    => $payment_result->redirect_url,
			],
			self::EXTENDING_KEY => $this->get_extended_data( self::IDENTIFIER ),
		];
	}

	/**
	 * This prepares the payment details for the response so it's following the
	 * schema where it's an array of objects.
	 *
	 * @param array $payment_details An array of payment details from the processed payment.
	 *
	 * @return array An array of objects where each object has the key and value
	 *               as distinct properties.
	 */
	protected function prepare_payment_details_for_response( array $payment_details ) {
		return array_map(
			function( $key, $value ) {
				return (object) [
					'key'   => $key,
					'value' => $value,
				];
			},
			array_keys( $payment_details ),
			$payment_details
		);
	}
}
