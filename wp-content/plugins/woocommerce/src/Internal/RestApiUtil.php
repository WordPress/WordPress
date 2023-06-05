<?php
/**
 * ApiUtil class file.
 */

namespace Automattic\WooCommerce\Internal;

/**
 * Helper methos for the REST API.
 *
 * Class ApiUtil
 *
 * @package Automattic\WooCommerce\Internal
 */
class RestApiUtil {

	/**
	 * Converts a create refund request from the public API format:
	 *
	 * [
	 *   "reason" => "",
	 *   "api_refund" => "x",
	 *   "api_restock" => "x",
	 *   "line_items" => [
	 *     "id" => "111",
	 *     "quantity" => 222,
	 *     "refund_total" => 333,
	 *     "refund_tax" => [
	 *       [
	 *          "id": "444",
	 *          "refund_total": 555
	 *       ],...
	 *   ],...
	 * ]
	 *
	 * ...to the internally used format:
	 *
	 * [
	 *   "reason" => null,      (if it's missing or any empty value, set as null)
	 *   "api_refund" => true,  (if it's missing or non-bool, set as "true")
	 *   "api_restock" => true, (if it's missing or non-bool, set as "true")
	 *   "line_items" => [      (convert sequential array to associative based on "id")
	 *     "111" => [
	 *       "qty" => 222,      (rename "quantity" to "qty")
	 *       "refund_total" => 333,
	 *       "refund_tax" => [  (convert sequential array to associative based on "id" and "refund_total)
	 *         "444" => 555,...
	 *       ],...
	 *   ]
	 * ]
	 *
	 * It also calculates the amount if missing and whenever possible, see maybe_calculate_refund_amount_from_line_items.
	 *
	 * The conversion is done in a way that if the request is already in the internal format,
	 * then nothing is changed for compatibility. For example, if the line items array
	 * is already an associative array or any of its elements
	 * is missing the "id" key, then the entire array is left unchanged.
	 * Same for the "refund_tax" array inside each line item.
	 *
	 * @param \WP_REST_Request $request The request to adjust.
	 */
	public static function adjust_create_refund_request_parameters( \WP_REST_Request &$request ) {
		if ( empty( $request['reason'] ) ) {
			$request['reason'] = null;
		}

		if ( ! is_bool( $request['api_refund'] ) ) {
			$request['api_refund'] = true;
		}

		if ( ! is_bool( $request['api_restock'] ) ) {
			$request['api_restock'] = true;
		}

		if ( empty( $request['line_items'] ) ) {
			$request['line_items'] = array();
		} else {
			$request['line_items'] = self::adjust_line_items_for_create_refund_request( $request['line_items'] );
		}

		if ( ! isset( $request['amount'] ) ) {
			$amount = self::calculate_refund_amount_from_line_items( $request );
			if ( null !== $amount ) {
				$request['amount'] = strval( $amount );
			}
		}
	}

	/**
	 * Calculate the "amount" parameter for the request based on the amounts found in line items.
	 * This will ONLY be possible if ALL of the following is true:
	 *
	 * - "line_items" in the request is a non-empty array.
	 * - All line items have a "refund_total" field with a numeric value.
	 * - All values inside "refund_tax" in all line items are a numeric value.
	 *
	 * The request is assumed to be in internal format already.
	 *
	 * @param \WP_REST_Request $request The request to maybe calculate the total amount for.
	 * @return number|null The calculated amount, or null if it can't be calculated.
	 */
	private static function calculate_refund_amount_from_line_items( $request ) {
		$line_items = $request['line_items'];

		if ( ! is_array( $line_items ) || empty( $line_items ) ) {
			return null;
		}

		$amount = 0;

		foreach ( $line_items as $item ) {
			if ( ! isset( $item['refund_total'] ) || ! is_numeric( $item['refund_total'] ) ) {
				return null;
			}

			$amount += $item['refund_total'];

			if ( ! isset( $item['refund_tax'] ) ) {
				continue;
			}

			foreach ( $item['refund_tax'] as $tax ) {
				if ( ! is_numeric( $tax ) ) {
					return null;
				}
				$amount += $tax;
			}
		}

		return $amount;
	}

	/**
	 * Convert the line items of a refund request to internal format (see adjust_create_refund_request_parameters).
	 *
	 * @param array $line_items The line items to convert.
	 * @return array The converted line items.
	 */
	private static function adjust_line_items_for_create_refund_request( $line_items ) {
		if ( ! is_array( $line_items ) || empty( $line_items ) || self::is_associative( $line_items ) ) {
			return $line_items;
		}

		$new_array = array();
		foreach ( $line_items as $item ) {
			if ( ! isset( $item['id'] ) ) {
				return $line_items;
			}

			if ( isset( $item['quantity'] ) && ! isset( $item['qty'] ) ) {
				$item['qty'] = $item['quantity'];
			}
			unset( $item['quantity'] );

			if ( isset( $item['refund_tax'] ) ) {
				$item['refund_tax'] = self::adjust_taxes_for_create_refund_request_line_item( $item['refund_tax'] );
			}

			$id               = $item['id'];
			$new_array[ $id ] = $item;

			unset( $new_array[ $id ]['id'] );
		}

		return $new_array;
	}

	/**
	 * Adjust the taxes array from a line item in a refund request, see adjust_create_refund_parameters.
	 *
	 * @param array $taxes_array The array to adjust.
	 * @return array The adjusted array.
	 */
	private static function adjust_taxes_for_create_refund_request_line_item( $taxes_array ) {
		if ( ! is_array( $taxes_array ) || empty( $taxes_array ) || self::is_associative( $taxes_array ) ) {
			return $taxes_array;
		}

		$new_array = array();
		foreach ( $taxes_array as $item ) {
			if ( ! isset( $item['id'] ) || ! isset( $item['refund_total'] ) ) {
				return $taxes_array;
			}

			$id               = $item['id'];
			$refund_total     = $item['refund_total'];
			$new_array[ $id ] = $refund_total;
		}

		return $new_array;
	}

	/**
	 * Is an array sequential or associative?
	 *
	 * @param array $array The array to check.
	 * @return bool True if the array is associative, false if it's sequential.
	 */
	private static function is_associative( array $array ) {
		return array_keys( $array ) !== range( 0, count( $array ) - 1 );
	}
}
