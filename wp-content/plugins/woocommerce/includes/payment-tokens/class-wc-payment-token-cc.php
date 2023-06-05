<?php
/**
 * Class WC_Payment_Token_CC file.
 *
 * @package WooCommerce\PaymentTokens
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WooCommerce Credit Card Payment Token.
 *
 * Representation of a payment token for credit cards.
 *
 * @class       WC_Payment_Token_CC
 * @version     3.0.0
 * @since       2.6.0
 * @package     WooCommerce\PaymentTokens
 */
class WC_Payment_Token_CC extends WC_Payment_Token {

	/**
	 * Token Type String.
	 *
	 * @var string
	 */
	protected $type = 'CC';

	/**
	 * Stores Credit Card payment token data.
	 *
	 * @var array
	 */
	protected $extra_data = array(
		'last4'        => '',
		'expiry_year'  => '',
		'expiry_month' => '',
		'card_type'    => '',
	);

	/**
	 * Get type to display to user.
	 *
	 * @since  2.6.0
	 * @param  string $deprecated Deprecated since WooCommerce 3.0.
	 * @return string
	 */
	public function get_display_name( $deprecated = '' ) {
		$display = sprintf(
			/* translators: 1: credit card type 2: last 4 digits 3: expiry month 4: expiry year */
			__( '%1$s ending in %2$s (expires %3$s/%4$s)', 'woocommerce' ),
			wc_get_credit_card_type_label( $this->get_card_type() ),
			$this->get_last4(),
			$this->get_expiry_month(),
			substr( $this->get_expiry_year(), 2 )
		);
		return $display;
	}

	/**
	 * Hook prefix
	 *
	 * @since 3.0.0
	 */
	protected function get_hook_prefix() {
		return 'woocommerce_payment_token_cc_get_';
	}

	/**
	 * Validate credit card payment tokens.
	 *
	 * These fields are required by all credit card payment tokens:
	 * expiry_month  - string Expiration date (MM) for the card
	 * expiry_year   - string Expiration date (YYYY) for the card
	 * last4         - string Last 4 digits of the card
	 * card_type     - string Card type (visa, mastercard, etc)
	 *
	 * @since 2.6.0
	 * @return boolean True if the passed data is valid
	 */
	public function validate() {
		if ( false === parent::validate() ) {
			return false;
		}

		if ( ! $this->get_last4( 'edit' ) ) {
			return false;
		}

		if ( ! $this->get_expiry_year( 'edit' ) ) {
			return false;
		}

		if ( ! $this->get_expiry_month( 'edit' ) ) {
			return false;
		}

		if ( ! $this->get_card_type( 'edit' ) ) {
			return false;
		}

		if ( 4 !== strlen( $this->get_expiry_year( 'edit' ) ) ) {
			return false;
		}

		if ( 2 !== strlen( $this->get_expiry_month( 'edit' ) ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Returns the card type (mastercard, visa, ...).
	 *
	 * @since  2.6.0
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string Card type
	 */
	public function get_card_type( $context = 'view' ) {
		return $this->get_prop( 'card_type', $context );
	}

	/**
	 * Set the card type (mastercard, visa, ...).
	 *
	 * @since 2.6.0
	 * @param string $type Credit card type (mastercard, visa, ...).
	 */
	public function set_card_type( $type ) {
		$this->set_prop( 'card_type', $type );
	}

	/**
	 * Returns the card expiration year (YYYY).
	 *
	 * @since  2.6.0
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string Expiration year
	 */
	public function get_expiry_year( $context = 'view' ) {
		return $this->get_prop( 'expiry_year', $context );
	}

	/**
	 * Set the expiration year for the card (YYYY format).
	 *
	 * @since 2.6.0
	 * @param string $year Credit card expiration year.
	 */
	public function set_expiry_year( $year ) {
		$this->set_prop( 'expiry_year', $year );
	}

	/**
	 * Returns the card expiration month (MM).
	 *
	 * @since  2.6.0
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string Expiration month
	 */
	public function get_expiry_month( $context = 'view' ) {
		return $this->get_prop( 'expiry_month', $context );
	}

	/**
	 * Set the expiration month for the card (formats into MM format).
	 *
	 * @since 2.6.0
	 * @param string $month Credit card expiration month.
	 */
	public function set_expiry_month( $month ) {
		$this->set_prop( 'expiry_month', str_pad( $month, 2, '0', STR_PAD_LEFT ) );
	}

	/**
	 * Returns the last four digits.
	 *
	 * @since  2.6.0
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string Last 4 digits
	 */
	public function get_last4( $context = 'view' ) {
		return $this->get_prop( 'last4', $context );
	}

	/**
	 * Set the last four digits.
	 *
	 * @since 2.6.0
	 * @param string $last4 Credit card last four digits.
	 */
	public function set_last4( $last4 ) {
		$this->set_prop( 'last4', $last4 );
	}
}
