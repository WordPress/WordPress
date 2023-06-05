<?php
namespace Automattic\WooCommerce\StoreApi\Formatters;

/**
 * Currency Formatter.
 *
 * Formats an array of monetary values by inserting currency data.
 */
class CurrencyFormatter implements FormatterInterface {
	/**
	 * Format a given value and return the result.
	 *
	 * @param array $value Value to format.
	 * @param array $options Options that influence the formatting.
	 * @return array
	 */
	public function format( $value, array $options = [] ) {
		$position = get_option( 'woocommerce_currency_pos' );
		$symbol   = html_entity_decode( get_woocommerce_currency_symbol() );
		$prefix   = '';
		$suffix   = '';

		switch ( $position ) {
			case 'left_space':
				$prefix = $symbol . ' ';
				break;
			case 'left':
				$prefix = $symbol;
				break;
			case 'right_space':
				$suffix = ' ' . $symbol;
				break;
			case 'right':
				$suffix = $symbol;
				break;
		}

		return array_merge(
			(array) $value,
			[
				'currency_code'               => get_woocommerce_currency(),
				'currency_symbol'             => $symbol,
				'currency_minor_unit'         => wc_get_price_decimals(),
				'currency_decimal_separator'  => wc_get_price_decimal_separator(),
				'currency_thousand_separator' => wc_get_price_thousand_separator(),
				'currency_prefix'             => $prefix,
				'currency_suffix'             => $suffix,
			]
		);
	}
}
