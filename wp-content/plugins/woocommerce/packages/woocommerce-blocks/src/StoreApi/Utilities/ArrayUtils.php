<?php
namespace Automattic\WooCommerce\StoreApi\Utilities;

/**
 * ArrayUtils class used for custom functions to operate on arrays
 */
class ArrayUtils {
	/**
	 * Join a string with a natural language conjunction at the end.
	 *
	 * @param array $array  The array to join together with the natural language conjunction.
	 * @param bool  $enclose_items_with_quotes Whether each item in the array should be enclosed within quotation marks.
	 *
	 * @return string a string containing a list of items and a natural language conjuction.
	 */
	public static function natural_language_join( $array, $enclose_items_with_quotes = false ) {
		if ( true === $enclose_items_with_quotes ) {
			$array = array_map(
				function( $item ) {
					return '"' . $item . '"';
				},
				$array
			);
		}
		$last = array_pop( $array );
		if ( $array ) {
			return sprintf(
				/* translators: 1: The first n-1 items of a list 2: the last item in the list. */
				__( '%1$s and %2$s', 'woocommerce' ),
				implode( ', ', $array ),
				$last
			);
		}
		return $last;
	}
}
