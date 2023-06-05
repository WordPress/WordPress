<?php
/**
 * Represents a price with a currency.
 */

namespace Automattic\WooCommerce\Admin\Marketing;

/**
 * Price class
 *
 * @since x.x.x
 */
class Price {
	/**
	 * The price.
	 *
	 * @var string
	 */
	protected $value;

	/**
	 * The currency of the price.
	 *
	 * @var string
	 */
	protected $currency;

	/**
	 * Price constructor.
	 *
	 * @param string $value    The value of the price.
	 * @param string $currency The currency of the price.
	 */
	public function __construct( string $value, string $currency ) {
		$this->value    = $value;
		$this->currency = $currency;
	}

	/**
	 * Get value of the price.
	 *
	 * @return string
	 */
	public function get_value(): string {
		return $this->value;
	}

	/**
	 * Get the currency of the price.
	 *
	 * @return string
	 */
	public function get_currency(): string {
		return $this->currency;
	}
}
