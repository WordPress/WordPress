<?php
/**
 * REST API Reports exportable traits
 *
 * Collection of utility methods for exportable reports.
 */

namespace Automattic\WooCommerce\Admin\API\Reports;

defined( 'ABSPATH' ) || exit;

/**
 * ExportableTraits class.
 */
trait ExportableTraits {
	/**
	 * Format numbers for CSV using store precision setting.
	 *
	 * @param string|float $value Numeric value.
	 * @return string Formatted value.
	 */
	public static function csv_number_format( $value ) {
		$decimals = wc_get_price_decimals();
		// See: @woocommerce/currency: getCurrencyFormatDecimal().
		return number_format( $value, $decimals, '.', '' );
	}
}
