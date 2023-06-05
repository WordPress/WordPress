<?php
namespace Automattic\WooCommerce\StoreApi\Utilities;

use Automattic\WooCommerce\Checkout\Helpers\ReserveStock;
use Automattic\WooCommerce\StoreApi\Exceptions\InvalidCartException;
use Automattic\WooCommerce\StoreApi\Exceptions\NotPurchasableException;
use Automattic\WooCommerce\StoreApi\Exceptions\OutOfStockException;
use Automattic\WooCommerce\StoreApi\Exceptions\PartialOutOfStockException;
use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use Automattic\WooCommerce\StoreApi\Exceptions\TooManyInCartException;
use Automattic\WooCommerce\StoreApi\Utilities\ArrayUtils;
use Automattic\WooCommerce\StoreApi\Utilities\DraftOrderTrait;
use Automattic\WooCommerce\StoreApi\Utilities\NoticeHandler;
use Automattic\WooCommerce\StoreApi\Utilities\QuantityLimits;
use Automattic\WooCommerce\Blocks\Package;
use WP_Error;

/**
 * Woo Cart Controller class.
 *
 * Helper class to bridge the gap between the cart API and Woo core.
 */
class CartController {
	use DraftOrderTrait;

	/**
	 * Makes the cart and sessions available to a route by loading them from core.
	 */
	public function load_cart() {
		if ( ! did_action( 'woocommerce_load_cart_from_session' ) && function_exists( 'wc_load_cart' ) ) {
			wc_load_cart();
		}
	}

	/**
	 * Recalculates the cart totals.
	 */
	public function calculate_totals() {
		$cart = $this->get_cart_instance();
		$cart->get_cart();
		$cart->calculate_fees();
		$cart->calculate_shipping();
		$cart->calculate_totals();
	}

	/**
	 * Based on the core cart class but returns errors rather than rendering notices directly.
	 *
	 * @todo Overriding the core add_to_cart method was necessary because core outputs notices when an item is added to
	 * the cart. For us this would cause notices to build up and output on the store, out of context. Core would need
	 * refactoring to split notices out from other cart actions.
	 *
	 * @throws RouteException Exception if invalid data is detected.
	 *
	 * @param array $request Add to cart request params.
	 * @return string
	 */
	public function add_to_cart( $request ) {
		$cart    = $this->get_cart_instance();
		$request = wp_parse_args(
			$request,
			[
				'id'             => 0,
				'quantity'       => 1,
				'variation'      => [],
				'cart_item_data' => [],
			]
		);

		$request = $this->filter_request_data( $this->parse_variation_data( $request ) );
		$product = $this->get_product_for_cart( $request );
		$cart_id = $cart->generate_cart_id(
			$this->get_product_id( $product ),
			$this->get_variation_id( $product ),
			$request['variation'],
			$request['cart_item_data']
		);

		$this->validate_add_to_cart( $product, $request );

		$quantity_limits  = new QuantityLimits();
		$existing_cart_id = $cart->find_product_in_cart( $cart_id );

		if ( $existing_cart_id ) {
			$cart_item           = $cart->cart_contents[ $existing_cart_id ];
			$quantity_validation = $quantity_limits->validate_cart_item_quantity( $request['quantity'] + $cart_item['quantity'], $cart_item );

			if ( is_wp_error( $quantity_validation ) ) {
				throw new RouteException( $quantity_validation->get_error_code(), $quantity_validation->get_error_message(), 400 );
			}

			$cart->set_quantity( $existing_cart_id, $request['quantity'] + $cart->cart_contents[ $existing_cart_id ]['quantity'], true );

			return $existing_cart_id;
		}

		// Normalize quantity.
		$add_to_cart_limits = $quantity_limits->get_add_to_cart_limits( $product );
		$request_quantity   = (int) $request['quantity'];

		if ( $add_to_cart_limits['maximum'] ) {
			$request_quantity = min( $request_quantity, $add_to_cart_limits['maximum'] );
		}

		$request_quantity = max( $request_quantity, $add_to_cart_limits['minimum'] );
		$request_quantity = $quantity_limits->limit_to_multiple( $request_quantity, $add_to_cart_limits['multiple_of'] );

		/**
		 * Filters the item being added to the cart.
		 *
		 * @since 2.5.0
		 *
		 * @internal Matches filter name in WooCommerce core.
		 *
		 * @param array $cart_item_data Array of cart item data being added to the cart.
		 * @param string $cart_id Id of the item in the cart.
		 * @return array Updated cart item data.
		 */
		$cart->cart_contents[ $cart_id ] = apply_filters(
			'woocommerce_add_cart_item',
			array_merge(
				$request['cart_item_data'],
				array(
					'key'          => $cart_id,
					'product_id'   => $this->get_product_id( $product ),
					'variation_id' => $this->get_variation_id( $product ),
					'variation'    => $request['variation'],
					'quantity'     => $request_quantity,
					'data'         => $product,
					'data_hash'    => wc_get_cart_item_data_hash( $product ),
				)
			),
			$cart_id
		);

		/**
		 * Filters the entire cart contents when the cart changes.
		 *
		 * @since 2.5.0
		 *
		 * @internal Matches filter name in WooCommerce core.
		 *
		 * @param array $cart_contents Array of all cart items.
		 * @return array Updated array of all cart items.
		 */
		$cart->cart_contents = apply_filters( 'woocommerce_cart_contents_changed', $cart->cart_contents );

		/**
		 * Fires when an item is added to the cart.
		 *
		 * This hook fires when an item is added to the cart. This is triggered from the Store API in this context, but
		 * WooCommerce core add to cart events trigger the same hook.
		 *
		 * @since 2.5.0
		 *
		 * @internal Matches action name in WooCommerce core.
		 *
		 * @param string $cart_id ID of the item in the cart.
		 * @param integer $product_id ID of the product added to the cart.
		 * @param integer $request_quantity Quantity of the item added to the cart.
		 * @param integer $variation_id Variation ID of the product added to the cart.
		 * @param array $variation Array of variation data.
		 * @param array $cart_item_data Array of other cart item data.
		 */
		do_action(
			'woocommerce_add_to_cart',
			$cart_id,
			$this->get_product_id( $product ),
			$request_quantity,
			$this->get_variation_id( $product ),
			$request['variation'],
			$request['cart_item_data']
		);

		return $cart_id;
	}

	/**
	 * Based on core `set_quantity` method, but validates if an item is sold individually first and enforces any limits in
	 * place.
	 *
	 * @throws RouteException Exception if invalid data is detected.
	 *
	 * @param string  $item_id Cart item id.
	 * @param integer $quantity Cart quantity.
	 */
	public function set_cart_item_quantity( $item_id, $quantity = 1 ) {
		$cart_item = $this->get_cart_item( $item_id );

		if ( empty( $cart_item ) ) {
			throw new RouteException( 'woocommerce_rest_cart_invalid_key', __( 'Cart item does not exist.', 'woocommerce' ), 409 );
		}

		$product = $cart_item['data'];

		if ( ! $product instanceof \WC_Product ) {
			throw new RouteException( 'woocommerce_rest_cart_invalid_product', __( 'Cart item is invalid.', 'woocommerce' ), 404 );
		}

		$quantity_validation = ( new QuantityLimits() )->validate_cart_item_quantity( $quantity, $cart_item );

		if ( is_wp_error( $quantity_validation ) ) {
			throw new RouteException( $quantity_validation->get_error_code(), $quantity_validation->get_error_message(), 400 );
		}

		$cart = $this->get_cart_instance();
		$cart->set_quantity( $item_id, $quantity );
	}

	/**
	 * Validate all items in the cart and check for errors.
	 *
	 * @throws RouteException Exception if invalid data is detected.
	 *
	 * @param \WC_Product $product Product object associated with the cart item.
	 * @param array       $request Add to cart request params.
	 */
	public function validate_add_to_cart( \WC_Product $product, $request ) {
		if ( ! $product->is_purchasable() ) {
			$this->throw_default_product_exception( $product );
		}

		if ( ! $product->is_in_stock() ) {
			throw new RouteException(
				'woocommerce_rest_product_out_of_stock',
				sprintf(
					/* translators: %s: product name */
					__( 'You cannot add &quot;%s&quot; to the cart because the product is out of stock.', 'woocommerce' ),
					$product->get_name()
				),
				400
			);
		}

		if ( $product->managing_stock() && ! $product->backorders_allowed() ) {
			$qty_remaining = $this->get_remaining_stock_for_product( $product );
			$qty_in_cart   = $this->get_product_quantity_in_cart( $product );

			if ( $qty_remaining < $qty_in_cart + $request['quantity'] ) {
				throw new RouteException(
					'woocommerce_rest_product_partially_out_of_stock',
					sprintf(
						/* translators: 1: product name 2: quantity in stock */
						__( 'You cannot add that amount of &quot;%1$s&quot; to the cart because there is not enough stock (%2$s remaining).', 'woocommerce' ),
						$product->get_name(),
						wc_format_stock_quantity_for_display( $qty_remaining, $product )
					),
					400
				);
			}
		}

		/**
		 * Filters if an item being added to the cart passed validation checks.
		 *
		 * Allow 3rd parties to validate if an item can be added to the cart. This is a legacy hook from Woo core.
		 * This filter will be deprecated because it encourages usage of wc_add_notice. For the API we need to capture
		 * notices and convert to exceptions instead.
		 *
		 * @since 7.2.0
		 *
		 * @deprecated
		 * @param boolean $passed_validation True if the item passed validation.
		 * @param integer $product_id Product ID being validated.
		 * @param integer $quantity Quantity added to the cart.
		 * @param integer $variation_id Variation ID being added to the cart.
		 * @param array $variation Variation data.
		 * @return boolean
		 */
		$passed_validation = apply_filters(
			'woocommerce_add_to_cart_validation',
			true,
			$this->get_product_id( $product ),
			$request['quantity'],
			$this->get_variation_id( $product ),
			$request['variation']
		);

		if ( ! $passed_validation ) {
			// Validation did not pass - see if an error notice was thrown.
			NoticeHandler::convert_notices_to_exceptions( 'woocommerce_rest_add_to_cart_error' );

			// If no notice was thrown, throw the default notice instead.
			$this->throw_default_product_exception( $product );
		}

		/**
		 * Fires during validation when adding an item to the cart via the Store API.
		 *
		 * @param \WC_Product $product Product object being added to the cart.
		 * @param array       $request Add to cart request params including id, quantity, and variation attributes.
		 * @deprecated 7.1.0 Use woocommerce_store_api_validate_add_to_cart instead.
		 */
		wc_do_deprecated_action(
			'wooocommerce_store_api_validate_add_to_cart',
			array(
				$product,
				$request,
			),
			'7.1.0',
			'woocommerce_store_api_validate_add_to_cart',
			'This action was deprecated in WooCommerce Blocks version 7.1.0. Please use woocommerce_store_api_validate_add_to_cart instead.'
		);

		/**
		 * Fires during validation when adding an item to the cart via the Store API.
		 *
		 * Fire action to validate add to cart. Functions hooking into this should throw an \Exception to prevent
		 * add to cart from happening.
		 *
		 * @since 7.1.0
		 *
		 * @param \WC_Product $product Product object being added to the cart.
		 * @param array       $request Add to cart request params including id, quantity, and variation attributes.
		 */
		do_action( 'woocommerce_store_api_validate_add_to_cart', $product, $request );
	}

	/**
	 * Generates the error message for out of stock products and adds product names to it.
	 *
	 * @param string $singular The message to use when only one product is in the list.
	 * @param string $plural The message to use when more than one product is in the list.
	 * @param array  $items The list of cart items whose names should be inserted into the message.
	 * @returns string The translated and correctly pluralised message.
	 */
	private function add_product_names_to_message( $singular, $plural, $items ) {
		$product_names = wc_list_pluck( $items, 'getProductName' );
		$message       = ( count( $items ) > 1 ) ? $plural : $singular;
		return sprintf(
			$message,
			ArrayUtils::natural_language_join( $product_names, true )
		);
	}

	/**
	 * Takes a string describing the type of stock extension, whether there is a single product or multiple products
	 * causing this exception and returns an appropriate error message.
	 *
	 * @param string $exception_type     The type of exception encountered.
	 * @param string $singular_or_plural Whether to get the error message for a single product or multiple.
	 *
	 * @return string
	 */
	private function get_error_message_for_stock_exception_type( $exception_type, $singular_or_plural ) {
		$stock_error_messages = [
			'out_of_stock'         => [
				/* translators: %s: product name. */
				'singular' => __(
					'%s is out of stock and cannot be purchased. Please remove it from your cart.',
					'woocommerce'
				),
				/* translators: %s: product names. */
				'plural'   => __(
					'%s are out of stock and cannot be purchased. Please remove them from your cart.',
					'woocommerce'
				),
			],
			'not_purchasable'      => [
				/* translators: %s: product name. */
				'singular' => __(
					'%s cannot be purchased. Please remove it from your cart.',
					'woocommerce'
				),
				/* translators: %s: product names. */
				'plural'   => __(
					'%s cannot be purchased. Please remove them from your cart.',
					'woocommerce'
				),
			],
			'too_many_in_cart'     => [
				/* translators: %s: product names. */
				'singular' => __(
					'There are too many %s in the cart. Only 1 can be purchased. Please reduce the quantity in your cart.',
					'woocommerce'
				),
				/* translators: %s: product names. */
				'plural'   => __(
					'There are too many %s in the cart. Only 1 of each can be purchased. Please reduce the quantities in your cart.',
					'woocommerce'
				),
			],
			'partial_out_of_stock' => [
				/* translators: %s: product names. */
				'singular' => __(
					'There is not enough %s in stock. Please reduce the quantity in your cart.',
					'woocommerce'
				),
				/* translators: %s: product names. */
				'plural'   => __(
					'There are not enough %s in stock. Please reduce the quantities in your cart.',
					'woocommerce'
				),
			],
		];

		if (
			isset( $stock_error_messages[ $exception_type ] ) &&
			isset( $stock_error_messages[ $exception_type ][ $singular_or_plural ] )
		) {
			return $stock_error_messages[ $exception_type ][ $singular_or_plural ];
		}

		return __( 'There was an error with an item in your cart.', 'woocommerce' );
	}

	/**
	 * Validate cart and check for errors.
	 *
	 * @throws InvalidCartException Exception if invalid data is detected in the cart.
	 */
	public function validate_cart() {
		$this->validate_cart_items();
		$this->validate_cart_coupons();

		$cart        = $this->get_cart_instance();
		$cart_errors = new WP_Error();

		/**
		 * Fires an action to validate the cart.
		 *
		 * Functions hooking into this should add custom errors using the provided WP_Error instance.
		 *
		 * @since 7.2.0
		 *
		 * @example See docs/examples/validate-cart.md
		 *
		 * @param \WP_Error $errors  WP_Error object.
		 * @param \WC_Cart  $cart    Cart object.
		 */
		do_action( 'woocommerce_store_api_cart_errors', $cart_errors, $cart );

		if ( $cart_errors->has_errors() ) {
			throw new InvalidCartException(
				'woocommerce_cart_error',
				$cart_errors,
				409
			);
		}

		// Before running the woocommerce_check_cart_items hook, unhook validation from the core cart.
		remove_action( 'woocommerce_check_cart_items', array( $cart, 'check_cart_items' ), 1 );
		remove_action( 'woocommerce_check_cart_items', array( $cart, 'check_cart_coupons' ), 1 );

		/**
		 * Fires when cart items are being validated.
		 *
		 * Allow 3rd parties to validate cart items. This is a legacy hook from Woo core.
		 * This filter will be deprecated because it encourages usage of wc_add_notice. For the API we need to capture
		 * notices and convert to wp errors instead.
		 *
		 * @since 7.2.0
		 *
		 * @deprecated
		 * @internal Matches action name in WooCommerce core.
		 */
		do_action( 'woocommerce_check_cart_items' );

		$cart_errors = NoticeHandler::convert_notices_to_wp_errors( 'woocommerce_rest_cart_item_error' );

		if ( $cart_errors->has_errors() ) {
			throw new InvalidCartException(
				'woocommerce_cart_error',
				$cart_errors,
				409
			);
		}
	}

	/**
	 * Validate all items in the cart and check for errors.
	 *
	 * @throws InvalidCartException Exception if invalid data is detected due to insufficient stock levels.
	 */
	public function validate_cart_items() {
		$cart       = $this->get_cart_instance();
		$cart_items = $this->get_cart_items();

		$errors                        = [];
		$out_of_stock_products         = [];
		$too_many_in_cart_products     = [];
		$partial_out_of_stock_products = [];
		$not_purchasable_products      = [];

		foreach ( $cart_items as $cart_item_key => $cart_item ) {
			try {
				$this->validate_cart_item( $cart_item );
			} catch ( RouteException $error ) {
				$errors[] = new WP_Error( $error->getErrorCode(), $error->getMessage(), $error->getAdditionalData() );
			} catch ( TooManyInCartException $error ) {
				$too_many_in_cart_products[] = $error;
			} catch ( NotPurchasableException $error ) {
				$not_purchasable_products[] = $error;
			} catch ( PartialOutOfStockException $error ) {
				$partial_out_of_stock_products[] = $error;
			} catch ( OutOfStockException $error ) {
				$out_of_stock_products[] = $error;
			}
		}

		if ( count( $errors ) > 0 ) {

			$error = new WP_Error();
			foreach ( $errors as $wp_error ) {
				$error->merge_from( $wp_error );
			}

			throw new InvalidCartException(
				'woocommerce_cart_error',
				$error,
				409
			);
		}

		$error = $this->stock_exceptions_to_wp_errors( $too_many_in_cart_products, $not_purchasable_products, $partial_out_of_stock_products, $out_of_stock_products );
		if ( $error->has_errors() ) {

			throw new InvalidCartException(
				'woocommerce_stock_availability_error',
				$error,
				409
			);
		}
	}

	/**
	 * This method will take arrays of exceptions relating to stock, and will convert them to a WP_Error object.
	 *
	 * @param TooManyInCartException[]     $too_many_in_cart_products     Array of TooManyInCartExceptions.
	 * @param NotPurchasableException[]    $not_purchasable_products      Array of NotPurchasableExceptions.
	 * @param PartialOutOfStockException[] $partial_out_of_stock_products Array of PartialOutOfStockExceptions.
	 * @param OutOfStockException[]        $out_of_stock_products         Array of OutOfStockExceptions.
	 *
	 * @return WP_Error  The WP_Error object returned. Will have errors if any exceptions were in the args. It will be empty if they do not.
	 */
	private function stock_exceptions_to_wp_errors( $too_many_in_cart_products, $not_purchasable_products, $partial_out_of_stock_products, $out_of_stock_products ) {
		$error = new WP_Error();

		if ( count( $out_of_stock_products ) > 0 ) {

			$singular_error = $this->get_error_message_for_stock_exception_type( 'out_of_stock', 'singular' );
			$plural_error   = $this->get_error_message_for_stock_exception_type( 'out_of_stock', 'plural' );

			$error->add(
				'woocommerce_rest_product_out_of_stock',
				$this->add_product_names_to_message( $singular_error, $plural_error, $out_of_stock_products )
			);
		}

		if ( count( $not_purchasable_products ) > 0 ) {
			$singular_error = $this->get_error_message_for_stock_exception_type( 'not_purchasable', 'singular' );
			$plural_error   = $this->get_error_message_for_stock_exception_type( 'not_purchasable', 'plural' );

			$error->add(
				'woocommerce_rest_product_not_purchasable',
				$this->add_product_names_to_message( $singular_error, $plural_error, $not_purchasable_products )
			);
		}

		if ( count( $too_many_in_cart_products ) > 0 ) {
			$singular_error = $this->get_error_message_for_stock_exception_type( 'too_many_in_cart', 'singular' );
			$plural_error   = $this->get_error_message_for_stock_exception_type( 'too_many_in_cart', 'plural' );

			$error->add(
				'woocommerce_rest_product_too_many_in_cart',
				$this->add_product_names_to_message( $singular_error, $plural_error, $too_many_in_cart_products )
			);
		}

		if ( count( $partial_out_of_stock_products ) > 0 ) {
			$singular_error = $this->get_error_message_for_stock_exception_type( 'partial_out_of_stock', 'singular' );
			$plural_error   = $this->get_error_message_for_stock_exception_type( 'partial_out_of_stock', 'plural' );

			$error->add(
				'woocommerce_rest_product_partially_out_of_stock',
				$this->add_product_names_to_message( $singular_error, $plural_error, $partial_out_of_stock_products )
			);
		}

		return $error;
	}

	/**
	 * Validates an existing cart item and returns any errors.
	 *
	 * @throws TooManyInCartException Exception if more than one product that can only be purchased individually is in
	 * the cart.
	 * @throws PartialOutOfStockException Exception if an item has a quantity greater than what is available in stock.
	 * @throws OutOfStockException Exception thrown when an item is entirely out of stock.
	 * @throws NotPurchasableException Exception thrown when an item is not purchasable.
	 * @param array $cart_item Cart item array.
	 */
	public function validate_cart_item( $cart_item ) {
		$product = $cart_item['data'];

		if ( ! $product instanceof \WC_Product ) {
			return;
		}

		if ( ! $product->is_purchasable() ) {
			throw new NotPurchasableException(
				'woocommerce_rest_product_not_purchasable',
				$product->get_name()
			);
		}

		if ( $product->is_sold_individually() && $cart_item['quantity'] > 1 ) {
			throw new TooManyInCartException(
				'woocommerce_rest_product_too_many_in_cart',
				$product->get_name()
			);
		}

		if ( ! $product->is_in_stock() ) {
			throw new OutOfStockException(
				'woocommerce_rest_product_out_of_stock',
				$product->get_name()
			);
		}

		if ( $product->managing_stock() && ! $product->backorders_allowed() ) {
			$qty_remaining = $this->get_remaining_stock_for_product( $product );
			$qty_in_cart   = $this->get_product_quantity_in_cart( $product );

			if ( $qty_remaining < $qty_in_cart ) {
				throw new PartialOutOfStockException(
					'woocommerce_rest_product_partially_out_of_stock',
					$product->get_name()
				);
			}
		}

		/**
		 * Fire action to validate add to cart. Functions hooking into this should throw an \Exception to prevent
		 * add to cart from occurring.
		 *
		 * @param \WC_Product $product Product object being added to the cart.
		 * @param array       $cart_item Cart item array.
		 * @deprecated 7.1.0 Use woocommerce_store_api_validate_cart_item instead.
		 */
		wc_do_deprecated_action(
			'wooocommerce_store_api_validate_cart_item',
			array(
				$product,
				$cart_item,
			),
			'7.1.0',
			'woocommerce_store_api_validate_cart_item',
			'This action was deprecated in WooCommerce Blocks version 7.1.0. Please use woocommerce_store_api_validate_cart_item instead.'
		);

		/**
		 * Fire action to validate add to cart. Functions hooking into this should throw an \Exception to prevent
		 * add to cart from occurring.
		 *
		 * @since 7.1.0
		 *
		 * @param \WC_Product $product Product object being added to the cart.
		 * @param array       $cart_item Cart item array.
		 */
		do_action( 'woocommerce_store_api_validate_cart_item', $product, $cart_item );
	}

	/**
	 * Validate all coupons in the cart and check for errors.
	 *
	 * @throws InvalidCartException Exception if invalid data is detected.
	 */
	public function validate_cart_coupons() {
		$cart_coupons = $this->get_cart_coupons();
		$errors       = [];

		foreach ( $cart_coupons as $code ) {
			$coupon = new \WC_Coupon( $code );
			try {
				$this->validate_cart_coupon( $coupon );
			} catch ( RouteException $error ) {
				$errors[] = new WP_Error( $error->getErrorCode(), $error->getMessage(), $error->getAdditionalData() );
			}
		}

		if ( ! empty( $errors ) ) {

			$error = new WP_Error();
			foreach ( $errors as $wp_error ) {
				$error->merge_from( $wp_error );
			}

			throw new InvalidCartException(
				'woocommerce_coupons_error',
				$error,
				409
			);
		}
	}

	/**
	 * Validate the cart and get a list of errors.
	 *
	 * @return WP_Error A WP_Error instance containing the cart's errors.
	 */
	public function get_cart_errors() {
		$errors = new WP_Error();

		try {
			$this->validate_cart();
		} catch ( RouteException $error ) {
			$errors->add( $error->getErrorCode(), $error->getMessage(), $error->getAdditionalData() );
		} catch ( InvalidCartException $error ) {
			$errors->merge_from( $error->getError() );
		} catch ( \Exception $error ) {
			$errors->add( $error->getCode(), $error->getMessage() );
		}

		return $errors;
	}

	/**
	 * Get main instance of cart class.
	 *
	 * @throws RouteException When cart cannot be loaded.
	 * @return \WC_Cart
	 */
	public function get_cart_instance() {
		$cart = wc()->cart;

		if ( ! $cart || ! $cart instanceof \WC_Cart ) {
			throw new RouteException( 'woocommerce_rest_cart_error', __( 'Unable to retrieve cart.', 'woocommerce' ), 500 );
		}

		return $cart;
	}

	/**
	 * Return a cart item from the woo core cart class.
	 *
	 * @param string $item_id Cart item id.
	 * @return array
	 */
	public function get_cart_item( $item_id ) {
		$cart = $this->get_cart_instance();
		return isset( $cart->cart_contents[ $item_id ] ) ? $cart->cart_contents[ $item_id ] : [];
	}

	/**
	 * Returns all cart items.
	 *
	 * @param callable $callback Optional callback to apply to the array filter.
	 * @return array
	 */
	public function get_cart_items( $callback = null ) {
		$cart = $this->get_cart_instance();
		return $callback ? array_filter( $cart->get_cart(), $callback ) : array_filter( $cart->get_cart() );
	}

	/**
	 * Get hashes for items in the current cart. Useful for tracking changes.
	 *
	 * @return array
	 */
	public function get_cart_hashes() {
		$cart = $this->get_cart_instance();
		return [
			'line_items' => $cart->get_cart_hash(),
			'shipping'   => md5( wp_json_encode( $cart->shipping_methods ) ),
			'fees'       => md5( wp_json_encode( $cart->get_fees() ) ),
			'coupons'    => md5( wp_json_encode( $cart->get_applied_coupons() ) ),
			'taxes'      => md5( wp_json_encode( $cart->get_taxes() ) ),
		];
	}

	/**
	 * Empty cart contents.
	 */
	public function empty_cart() {
		$cart = $this->get_cart_instance();
		$cart->empty_cart();
	}

	/**
	 * See if cart has applied coupon by code.
	 *
	 * @param string $coupon_code Cart coupon code.
	 * @return bool
	 */
	public function has_coupon( $coupon_code ) {
		$cart = $this->get_cart_instance();
		return $cart->has_discount( $coupon_code );
	}

	/**
	 * Returns all applied coupons.
	 *
	 * @param callable $callback Optional callback to apply to the array filter.
	 * @return array
	 */
	public function get_cart_coupons( $callback = null ) {
		$cart = $this->get_cart_instance();
		return $callback ? array_filter( $cart->get_applied_coupons(), $callback ) : array_filter( $cart->get_applied_coupons() );
	}

	/**
	 * Get shipping packages from the cart with calculated shipping rates.
	 *
	 * @todo this can be refactored once https://github.com/woocommerce/woocommerce/pull/26101 lands.
	 *
	 * @param bool $calculate_rates Should rates for the packages also be returned.
	 * @return array
	 */
	public function get_shipping_packages( $calculate_rates = true ) {
		$cart = $this->get_cart_instance();

		// See if we need to calculate anything.
		if ( ! $cart->needs_shipping() ) {
			return [];
		}

		$packages = $cart->get_shipping_packages();

		// Add extra package data to array.
		if ( count( $packages ) ) {
			$packages = array_map(
				function( $key, $package, $index ) {
					$package['package_id']   = isset( $package['package_id'] ) ? $package['package_id'] : $key;
					$package['package_name'] = isset( $package['package_name'] ) ? $package['package_name'] : $this->get_package_name( $package, $index );
					return $package;
				},
				array_keys( $packages ),
				$packages,
				range( 1, count( $packages ) )
			);
		}

		$packages = $calculate_rates ? wc()->shipping()->calculate_shipping( $packages ) : $packages;

		return $packages;
	}

	/**
	 * Creates a name for a package.
	 *
	 * @param array $package Shipping package from WooCommerce.
	 * @param int   $index Package number.
	 * @return string
	 */
	protected function get_package_name( $package, $index ) {
		/**
		 * Filters the shipping package name.
		 *
		 * @since 4.3.0
		 *
		 * @internal Matches filter name in WooCommerce core.
		 *
		 * @param string $shipping_package_name Shipping package name.
		 * @param string $package_id Shipping package ID.
		 * @param array $package Shipping package from WooCommerce.
		 * @return string Shipping package name.
		 */
		return apply_filters(
			'woocommerce_shipping_package_name',
			$index > 1 ?
				sprintf(
					/* translators: %d: shipping package number */
					_x( 'Shipment %d', 'shipping packages', 'woocommerce' ),
					$index
				) :
				_x( 'Shipment 1', 'shipping packages', 'woocommerce' ),
			$package['package_id'],
			$package
		);
	}

	/**
	 * Selects a shipping rate.
	 *
	 * @param int|string $package_id ID of the package to choose a rate for.
	 * @param string     $rate_id ID of the rate being chosen.
	 */
	public function select_shipping_rate( $package_id, $rate_id ) {
		$cart                        = $this->get_cart_instance();
		$session_data                = wc()->session->get( 'chosen_shipping_methods' ) ? wc()->session->get( 'chosen_shipping_methods' ) : [];
		$session_data[ $package_id ] = $rate_id;

		wc()->session->set( 'chosen_shipping_methods', $session_data );
	}

	/**
	 * Based on the core cart class but returns errors rather than rendering notices directly.
	 *
	 * @todo Overriding the core apply_coupon method was necessary because core outputs notices when a coupon gets
	 * applied. For us this would cause notices to build up and output on the store, out of context. Core would need
	 * refactoring to split notices out from other cart actions.
	 *
	 * @throws RouteException Exception if invalid data is detected.
	 *
	 * @param string $coupon_code Coupon code.
	 */
	public function apply_coupon( $coupon_code ) {
		$cart            = $this->get_cart_instance();
		$applied_coupons = $this->get_cart_coupons();
		$coupon          = new \WC_Coupon( $coupon_code );

		if ( $coupon->get_code() !== $coupon_code ) {
			throw new RouteException(
				'woocommerce_rest_cart_coupon_error',
				sprintf(
					/* translators: %s coupon code */
					__( '"%s" is an invalid coupon code.', 'woocommerce' ),
					esc_html( $coupon_code )
				),
				400
			);
		}

		if ( $this->has_coupon( $coupon_code ) ) {
			throw new RouteException(
				'woocommerce_rest_cart_coupon_error',
				sprintf(
					/* translators: %s coupon code */
					__( 'Coupon code "%s" has already been applied.', 'woocommerce' ),
					esc_html( $coupon_code )
				),
				400
			);
		}

		if ( ! $coupon->is_valid() ) {
			throw new RouteException(
				'woocommerce_rest_cart_coupon_error',
				wp_strip_all_tags( $coupon->get_error_message() ),
				400
			);
		}

		// Prevents new coupons being added if individual use coupons are already in the cart.
		$individual_use_coupons = $this->get_cart_coupons(
			function( $code ) {
				$coupon = new \WC_Coupon( $code );
				return $coupon->get_individual_use();
			}
		);

		foreach ( $individual_use_coupons as $code ) {
			$individual_use_coupon = new \WC_Coupon( $code );

			/**
			 * Filters if a coupon can be applied alongside other individual use coupons.
			 *
			 * @since 2.6.0
			 *
			 * @internal Matches filter name in WooCommerce core.
			 *
			 * @param boolean $apply_with_individual_use_coupon Defaults to false.
			 * @param \WC_Coupon $coupon Coupon object applied to the cart.
			 * @param \WC_Coupon $individual_use_coupon Individual use coupon already applied to the cart.
			 * @param array $applied_coupons Array of applied coupons already applied to the cart.
			 * @return boolean
			 */
			if ( false === apply_filters( 'woocommerce_apply_with_individual_use_coupon', false, $coupon, $individual_use_coupon, $applied_coupons ) ) {
				throw new RouteException(
					'woocommerce_rest_cart_coupon_error',
					sprintf(
						/* translators: %s: coupon code */
						__( '"%s" has already been applied and cannot be used in conjunction with other coupons.', 'woocommerce' ),
						$code
					),
					400
				);
			}
		}

		if ( $coupon->get_individual_use() ) {
			/**
			 * Filter coupons to remove when applying an individual use coupon.
			 *
			 * @since 2.6.0
			 *
			 * @internal Matches filter name in WooCommerce core.
			 *
			 * @param array $coupons Array of coupons to remove from the cart.
			 * @param \WC_Coupon $coupon Coupon object applied to the cart.
			 * @param array $applied_coupons Array of applied coupons already applied to the cart.
			 * @return array
			 */
			$coupons_to_remove = array_diff( $applied_coupons, apply_filters( 'woocommerce_apply_individual_use_coupon', array(), $coupon, $applied_coupons ) );

			foreach ( $coupons_to_remove as $code ) {
				$cart->remove_coupon( $code );
			}

			$applied_coupons = array_diff( $applied_coupons, $coupons_to_remove );
		}

		$applied_coupons[] = $coupon_code;
		$cart->set_applied_coupons( $applied_coupons );

		/**
		 * Fires after a coupon has been applied to the cart.
		 *
		 * @since 2.6.0
		 *
		 * @internal Matches action name in WooCommerce core.
		 *
		 * @param string $coupon_code The coupon code that was applied.
		 */
		do_action( 'woocommerce_applied_coupon', $coupon_code );
	}

	/**
	 * Validates an existing cart coupon and returns any errors.
	 *
	 * @throws RouteException Exception if invalid data is detected.
	 *
	 * @param \WC_Coupon $coupon Coupon object applied to the cart.
	 */
	protected function validate_cart_coupon( \WC_Coupon $coupon ) {
		if ( ! $coupon->is_valid() ) {
			$cart = $this->get_cart_instance();
			$cart->remove_coupon( $coupon->get_code() );
			$cart->calculate_totals();
			throw new RouteException(
				'woocommerce_rest_cart_coupon_error',
				sprintf(
					/* translators: %1$s coupon code, %2$s reason. */
					__( 'The "%1$s" coupon has been removed from your cart: %2$s', 'woocommerce' ),
					$coupon->get_code(),
					wp_strip_all_tags( $coupon->get_error_message() )
				),
				409
			);
		}
	}

	/**
	 * Gets the qty of a product across line items.
	 *
	 * @param \WC_Product $product Product object.
	 * @return int
	 */
	protected function get_product_quantity_in_cart( $product ) {
		$cart               = $this->get_cart_instance();
		$product_quantities = $cart->get_cart_item_quantities();
		$product_id         = $product->get_stock_managed_by_id();

		return isset( $product_quantities[ $product_id ] ) ? $product_quantities[ $product_id ] : 0;
	}

	/**
	 * Gets remaining stock for a product.
	 *
	 * @param \WC_Product $product Product object.
	 * @return int
	 */
	protected function get_remaining_stock_for_product( $product ) {
		$reserve_stock = new ReserveStock();
		$qty_reserved  = $reserve_stock->get_reserved_stock( $product, $this->get_draft_order_id() );

		return $product->get_stock_quantity() - $qty_reserved;
	}

	/**
	 * Get a product object to be added to the cart.
	 *
	 * @throws RouteException Exception if invalid data is detected.
	 *
	 * @param array $request Add to cart request params.
	 * @return \WC_Product|Error Returns a product object if purchasable.
	 */
	protected function get_product_for_cart( $request ) {
		$product = wc_get_product( $request['id'] );

		if ( ! $product || 'trash' === $product->get_status() ) {
			throw new RouteException(
				'woocommerce_rest_cart_invalid_product',
				__( 'This product cannot be added to the cart.', 'woocommerce' ),
				400
			);
		}

		return $product;
	}

	/**
	 * For a given product, get the product ID.
	 *
	 * @param \WC_Product $product Product object associated with the cart item.
	 * @return int
	 */
	protected function get_product_id( \WC_Product $product ) {
		return $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();
	}

	/**
	 * For a given product, get the variation ID.
	 *
	 * @param \WC_Product $product Product object associated with the cart item.
	 * @return int
	 */
	protected function get_variation_id( \WC_Product $product ) {
		return $product->is_type( 'variation' ) ? $product->get_id() : 0;
	}

	/**
	 * Default exception thrown when an item cannot be added to the cart.
	 *
	 * @throws RouteException Exception with code woocommerce_rest_product_not_purchasable.
	 *
	 * @param \WC_Product $product Product object associated with the cart item.
	 */
	protected function throw_default_product_exception( \WC_Product $product ) {
		throw new RouteException(
			'woocommerce_rest_product_not_purchasable',
			sprintf(
				/* translators: %s: product name */
				__( '&quot;%s&quot; is not available for purchase.', 'woocommerce' ),
				$product->get_name()
			),
			400
		);
	}

	/**
	 * Filter data for add to cart requests.
	 *
	 * @param array $request Add to cart request params.
	 * @return array Updated request array.
	 */
	protected function filter_request_data( $request ) {
		$product_id   = $request['id'];
		$variation_id = 0;
		$product      = wc_get_product( $product_id );

		if ( $product->is_type( 'variation' ) ) {
			$product_id   = $product->get_parent_id();
			$variation_id = $product->get_id();
		}

		/**
		 * Filter cart item data for add to cart requests.
		 *
		 * @since 2.5.0
		 *
		 * @internal Matches filter name in WooCommerce core.
		 *
		 * @param array $cart_item_data Array of other cart item data.
		 * @param integer $product_id ID of the product added to the cart.
		 * @param integer $variation_id Variation ID of the product added to the cart.
		 * @param integer $quantity Quantity of the item added to the cart.
		 * @return array
		 */
		$request['cart_item_data'] = (array) apply_filters(
			'woocommerce_add_cart_item_data',
			$request['cart_item_data'],
			$product_id,
			$variation_id,
			$request['quantity']
		);

		if ( $product->is_sold_individually() ) {
			/**
			 * Filter sold individually quantity for add to cart requests.
			 *
			 * @since 2.5.0
			 *
			 * @internal Matches filter name in WooCommerce core.
			 *
			 * @param integer $sold_individually_quantity Defaults to 1.
			 * @param integer $quantity Quantity of the item added to the cart.
			 * @param integer $product_id ID of the product added to the cart.
			 * @param integer $variation_id Variation ID of the product added to the cart.
			 * @param array $cart_item_data Array of other cart item data.
			 * @return integer
			 */
			$request['quantity'] = apply_filters( 'woocommerce_add_to_cart_sold_individually_quantity', 1, $request['quantity'], $product_id, $variation_id, $request['cart_item_data'] );
		}

		return $request;
	}

	/**
	 * If variations are set, validate and format the values ready to add to the cart.
	 *
	 * @throws RouteException Exception if invalid data is detected.
	 *
	 * @param array $request Add to cart request params.
	 * @return array Updated request array.
	 */
	protected function parse_variation_data( $request ) {
		$product = $this->get_product_for_cart( $request );

		// Remove variation request if not needed.
		if ( ! $product->is_type( array( 'variation', 'variable' ) ) ) {
			$request['variation'] = [];
			return $request;
		}

		// Flatten data and format posted values.
		$variable_product_attributes = $this->get_variable_product_attributes( $product );
		$request['variation']        = $this->sanitize_variation_data( wp_list_pluck( $request['variation'], 'value', 'attribute' ), $variable_product_attributes );

		// If we have a parent product, find the variation ID.
		if ( $product->is_type( 'variable' ) ) {
			$request['id'] = $this->get_variation_id_from_variation_data( $request, $product );
		}

		// Now we have a variation ID, get the valid set of attributes for this variation. They will have an attribute_ prefix since they are from meta.
		$expected_attributes = wc_get_product_variation_attributes( $request['id'] );
		$missing_attributes  = [];

		foreach ( $variable_product_attributes as $attribute ) {
			if ( ! $attribute['is_variation'] ) {
				continue;
			}

			$prefixed_attribute_name = 'attribute_' . sanitize_title( $attribute['name'] );
			$expected_value          = isset( $expected_attributes[ $prefixed_attribute_name ] ) ? $expected_attributes[ $prefixed_attribute_name ] : '';
			$attribute_label         = wc_attribute_label( $attribute['name'] );

			if ( isset( $request['variation'][ wc_variation_attribute_name( $attribute['name'] ) ] ) ) {
				$given_value = $request['variation'][ wc_variation_attribute_name( $attribute['name'] ) ];

				if ( $expected_value === $given_value ) {
					continue;
				}

				// If valid values are empty, this is an 'any' variation so get all possible values.
				if ( '' === $expected_value && in_array( $given_value, $attribute->get_slugs(), true ) ) {
					continue;
				}

				throw new RouteException(
					'woocommerce_rest_invalid_variation_data',
					/* translators: %1$s: Attribute name, %2$s: Allowed values. */
					sprintf( __( 'Invalid value posted for %1$s. Allowed values: %2$s', 'woocommerce' ), $attribute_label, implode( ', ', $attribute->get_slugs() ) ),
					400
				);
			}

			// Fills request array with unspecified attributes that have default values. This ensures the variation always has full data.
			if ( '' !== $expected_value && ! isset( $request['variation'][ wc_variation_attribute_name( $attribute['name'] ) ] ) ) {
				$request['variation'][ wc_variation_attribute_name( $attribute['name'] ) ] = $expected_value;
			}

			// If no attribute was posted, only error if the variation has an 'any' attribute which requires a value.
			if ( '' === $expected_value ) {
				$missing_attributes[] = $attribute_label;
			}
		}

		if ( ! empty( $missing_attributes ) ) {
			throw new RouteException(
				'woocommerce_rest_missing_variation_data',
				/* translators: %s: Attribute name. */
				__( 'Missing variation data for variable product.', 'woocommerce' ) . ' ' . sprintf( _n( '%s is a required field', '%s are required fields', count( $missing_attributes ), 'woocommerce' ), wc_format_list_of_items( $missing_attributes ) ),
				400
			);
		}

		ksort( $request['variation'] );

		return $request;
	}

	/**
	 * Try to match request data to a variation ID and return the ID.
	 *
	 * @throws RouteException Exception if variation cannot be found.
	 *
	 * @param array       $request Add to cart request params.
	 * @param \WC_Product $product Product being added to the cart.
	 * @return int Matching variation ID.
	 */
	protected function get_variation_id_from_variation_data( $request, $product ) {
		$data_store       = \WC_Data_Store::load( 'product' );
		$match_attributes = $request['variation'];
		$variation_id     = $data_store->find_matching_product_variation( $product, $match_attributes );

		if ( empty( $variation_id ) ) {
			throw new RouteException(
				'woocommerce_rest_variation_id_from_variation_data',
				__( 'No matching variation found.', 'woocommerce' ),
				400
			);
		}

		return $variation_id;
	}

	/**
	 * Format and sanitize variation data posted to the API.
	 *
	 * Labels are converted to names (e.g. Size to pa_size), and values are cleaned.
	 *
	 * @throws RouteException Exception if variation cannot be found.
	 *
	 * @param array $variation_data Key value pairs of attributes and values.
	 * @param array $variable_product_attributes Product attributes we're expecting.
	 * @return array
	 */
	protected function sanitize_variation_data( $variation_data, $variable_product_attributes ) {
		$return = [];

		foreach ( $variable_product_attributes as $attribute ) {
			if ( ! $attribute['is_variation'] ) {
				continue;
			}
			$attribute_label          = wc_attribute_label( $attribute['name'] );
			$variation_attribute_name = wc_variation_attribute_name( $attribute['name'] );

			// Attribute labels e.g. Size.
			if ( isset( $variation_data[ $attribute_label ] ) ) {
				$return[ $variation_attribute_name ] =
					$attribute['is_taxonomy']
						?
						sanitize_title( $variation_data[ $attribute_label ] )
						:
						html_entity_decode(
							wc_clean( $variation_data[ $attribute_label ] ),
							ENT_QUOTES,
							get_bloginfo( 'charset' )
						);
				continue;
			}

			// Attribute slugs e.g. pa_size.
			if ( isset( $variation_data[ $attribute['name'] ] ) ) {
				$return[ $variation_attribute_name ] =
					$attribute['is_taxonomy']
						?
						sanitize_title( $variation_data[ $attribute['name'] ] )
						:
						html_entity_decode(
							wc_clean( $variation_data[ $attribute['name'] ] ),
							ENT_QUOTES,
							get_bloginfo( 'charset' )
						);
			}
		}
		return $return;
	}

	/**
	 * Get product attributes from the variable product (which may be the parent if the product object is a variation).
	 *
	 * @throws RouteException Exception if product is invalid.
	 *
	 * @param \WC_Product $product Product being added to the cart.
	 * @return array
	 */
	protected function get_variable_product_attributes( $product ) {
		if ( $product->is_type( 'variation' ) ) {
			$product = wc_get_product( $product->get_parent_id() );
		}

		if ( ! $product || 'trash' === $product->get_status() ) {
			throw new RouteException(
				'woocommerce_rest_cart_invalid_parent_product',
				__( 'This product cannot be added to the cart.', 'woocommerce' ),
				400
			);
		}

		return $product->get_attributes();
	}
}
