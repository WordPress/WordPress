<?php
/**
 * WooCommerce Payment Tokens
 *
 * An API for storing and managing tokens for gateways and customers.
 *
 * @package WooCommerce\Classes
 * @version 3.0.0
 * @since   2.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Payment tokens class.
 */
class WC_Payment_Tokens {

	/**
	 * Gets valid tokens from the database based on user defined criteria.
	 *
	 * @since  2.6.0
	 * @param  array $args Query arguments {
	 *     Array of query parameters.
	 *
	 *     @type string $token_id   Token ID.
	 *     @type string $user_id    User ID.
	 *     @type string $gateway_id Gateway ID.
	 *     @type string $type       Token type.
	 * }
	 * @return WC_Payment_Token[]
	 */
	public static function get_tokens( $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'token_id'   => '',
				'user_id'    => '',
				'gateway_id' => '',
				'type'       => '',
			)
		);

		$data_store    = WC_Data_Store::load( 'payment-token' );
		$token_results = $data_store->get_tokens( $args );
		$tokens        = array();

		if ( ! empty( $token_results ) ) {
			foreach ( $token_results as $token_result ) {
				$_token = self::get( $token_result->token_id, $token_result );
				if ( ! empty( $_token ) ) {
					$tokens[ $token_result->token_id ] = $_token;
				}
			}
		}

		return $tokens;
	}

	/**
	 * Returns an array of payment token objects associated with the passed customer ID.
	 *
	 * @since 2.6.0
	 * @param  int    $customer_id Customer ID.
	 * @param  string $gateway_id  Optional Gateway ID for getting tokens for a specific gateway.
	 * @return WC_Payment_Token[]  Array of token objects.
	 */
	public static function get_customer_tokens( $customer_id, $gateway_id = '' ) {
		if ( $customer_id < 1 ) {
			return array();
		}

		$tokens = self::get_tokens(
			array(
				'user_id'    => $customer_id,
				'gateway_id' => $gateway_id,
				/**
				 * Controls the maximum number of Payment Methods that will be listed via the My Account page.
				 *
				 * @since 7.2.0
				 *
				 * @param int $limit Defaults to the value of the `posts_per_page` option.
				 */
				'limit'      => apply_filters( 'woocommerce_get_customer_payment_tokens_limit', get_option( 'posts_per_page' ) ),
			)
		);

		return apply_filters( 'woocommerce_get_customer_payment_tokens', $tokens, $customer_id, $gateway_id );
	}

	/**
	 * Returns a customers default token or NULL if there is no default token.
	 *
	 * @since  2.6.0
	 * @param  int $customer_id Customer ID.
	 * @return WC_Payment_Token|null
	 */
	public static function get_customer_default_token( $customer_id ) {
		if ( $customer_id < 1 ) {
			return null;
		}

		$data_store = WC_Data_Store::load( 'payment-token' );
		$token      = $data_store->get_users_default_token( $customer_id );

		if ( $token ) {
			return self::get( $token->token_id, $token );
		} else {
			return null;
		}
	}

	/**
	 * Returns an array of payment token objects associated with the passed order ID.
	 *
	 * @since 2.6.0
	 * @param int $order_id       Order ID.
	 * @return WC_Payment_Token[] Array of token objects.
	 */
	public static function get_order_tokens( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return array();
		}

		$token_ids = $order->get_payment_tokens();

		if ( empty( $token_ids ) ) {
			return array();
		}

		$tokens = self::get_tokens(
			array(
				'token_id' => $token_ids,
			)
		);

		return apply_filters( 'woocommerce_get_order_payment_tokens', $tokens, $order_id );
	}

	/**
	 * Get a token object by ID.
	 *
	 * @since 2.6.0
	 *
	 * @param int    $token_id Token ID.
	 * @param object $token_result Token result.
	 * @return null|WC_Payment_Token Returns a valid payment token or null if no token can be found.
	 */
	public static function get( $token_id, $token_result = null ) {
		$data_store = WC_Data_Store::load( 'payment-token' );

		if ( is_null( $token_result ) ) {
			$token_result = $data_store->get_token_by_id( $token_id );
			// Still empty? Token doesn't exist? Don't continue.
			if ( empty( $token_result ) ) {
				return null;
			}
		}

		$token_class = self::get_token_classname( $token_result->type );

		if ( class_exists( $token_class ) ) {
			$meta        = $data_store->get_metadata( $token_id );
			$passed_meta = array();
			if ( ! empty( $meta ) ) {
				foreach ( $meta as $meta_key => $meta_value ) {
					$passed_meta[ $meta_key ] = $meta_value[0];
				}
			}
			return new $token_class( $token_id, (array) $token_result, $passed_meta );
		}

		return null;
	}

	/**
	 * Remove a payment token from the database by ID.
	 *
	 * @since 2.6.0
	 * @param int $token_id Token ID.
	 */
	public static function delete( $token_id ) {
		$type = self::get_token_type_by_id( $token_id );
		if ( ! empty( $type ) ) {
			$class = self::get_token_classname( $type );
			$token = new $class( $token_id );
			$token->delete();
		}
	}

	/**
	 * Loops through all of a users payment tokens and sets is_default to false for all but a specific token.
	 *
	 * @since 2.6.0
	 * @param int $user_id  User to set a default for.
	 * @param int $token_id The ID of the token that should be default.
	 */
	public static function set_users_default( $user_id, $token_id ) {
		$data_store   = WC_Data_Store::load( 'payment-token' );
		$users_tokens = self::get_customer_tokens( $user_id );
		foreach ( $users_tokens as $token ) {
			if ( $token_id === $token->get_id() ) {
				$data_store->set_default_status( $token->get_id(), true );
				do_action( 'woocommerce_payment_token_set_default', $token_id, $token );
			} else {
				$data_store->set_default_status( $token->get_id(), false );
			}
		}
	}

	/**
	 * Returns what type (credit card, echeck, etc) of token a token is by ID.
	 *
	 * @since  2.6.0
	 * @param  int $token_id Token ID.
	 * @return string        Type.
	 */
	public static function get_token_type_by_id( $token_id ) {
		$data_store = WC_Data_Store::load( 'payment-token' );
		return $data_store->get_token_type_by_id( $token_id );
	}

	/**
	 * Get classname based on token type.
	 *
	 * @since 3.8.0
	 * @param string $type Token type.
	 * @return string
	 */
	protected static function get_token_classname( $type ) {
		/**
		 * Filter payment token class per type.
		 *
		 * @since 3.8.0
		 * @param string $class Payment token class.
		 * @param string $type Token type.
		 */
		return apply_filters( 'woocommerce_payment_token_class', 'WC_Payment_Token_' . $type, $type );
	}
}
