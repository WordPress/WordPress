<?php
namespace Automattic\WooCommerce\StoreApi\Formatters;

/**
 * Money Formatter.
 *
 * Formats monetary values using store settings.
 */
class MoneyFormatter implements FormatterInterface {
	/**
	 * Format a given value and return the result.
	 *
	 * @param mixed $value Value to format.
	 * @param array $options Options that influence the formatting.
	 * @return mixed
	 */
	public function format( $value, array $options = [] ) {
		$options = wp_parse_args(
			$options,
			[
				'decimals'      => wc_get_price_decimals(),
				'rounding_mode' => PHP_ROUND_HALF_UP,
			]
		);

		return (string) intval(
			round(
				( (float) wc_format_decimal( $value ) ) * ( 10 ** absint( $options['decimals'] ) ),
				0,
				absint( $options['rounding_mode'] )
			)
		);
	}
}
