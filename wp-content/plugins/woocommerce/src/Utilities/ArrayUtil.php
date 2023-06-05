<?php
/**
 * A class of utilities for dealing with arrays.
 */

namespace Automattic\WooCommerce\Utilities;

/**
 * A class of utilities for dealing with arrays.
 */
class ArrayUtil {

	/**
	 * Automatic selector type for the 'select' method.
	 */
	public const SELECT_BY_AUTO = 0;

	/**
	 * Object method selector type for the 'select' method.
	 */
	public const SELECT_BY_OBJECT_METHOD = 1;

	/**
	 * Object property selector type for the 'select' method.
	 */
	public const SELECT_BY_OBJECT_PROPERTY = 2;

	/**
	 * Array key selector type for the 'select' method.
	 */
	public const SELECT_BY_ARRAY_KEY = 3;

	/**
	 * Get a value from an nested array by specifying the entire key hierarchy with '::' as separator.
	 *
	 * E.g. for [ 'foo' => [ 'bar' => [ 'fizz' => 'buzz' ] ] ] the value for key 'foo::bar::fizz' would be 'buzz'.
	 *
	 * @param array  $array The array to get the value from.
	 * @param string $key The complete key hierarchy, using '::' as separator.
	 * @param mixed  $default The value to return if the key doesn't exist in the array.
	 *
	 * @return mixed The retrieved value, or the supplied default value.
	 * @throws \Exception $array is not an array.
	 */
	public static function get_nested_value( array $array, string $key, $default = null ) {
		$key_stack = explode( '::', $key );
		$subkey    = array_shift( $key_stack );

		if ( isset( $array[ $subkey ] ) ) {
			$value = $array[ $subkey ];

			if ( count( $key_stack ) ) {
				foreach ( $key_stack as $subkey ) {
					if ( is_array( $value ) && isset( $value[ $subkey ] ) ) {
						$value = $value[ $subkey ];
					} else {
						$value = $default;
						break;
					}
				}
			}
		} else {
			$value = $default;
		}

		return $value;
	}

	/**
	 * Checks if a given key exists in an array and its value can be evaluated as 'true'.
	 *
	 * @param array  $array The array to check.
	 * @param string $key The key for the value to check.
	 * @return bool True if the key exists in the array and the value can be evaluated as 'true'.
	 */
	public static function is_truthy( array $array, string $key ) {
		return isset( $array[ $key ] ) && $array[ $key ];
	}

	/**
	 * Gets the value for a given key from an array, or a default value if the key doesn't exist in the array.
	 *
	 * This is equivalent to "$array[$key] ?? $default" except in one case:
	 * when they key exists, has a null value, and a non-null default is supplied:
	 *
	 * $array = ['key' => null]
	 * $array['key'] ?? 'default' => 'default'
	 * ArrayUtil::get_value_or_default($array, 'key', 'default') => null
	 *
	 * @param array  $array The array to get the value from.
	 * @param string $key The key to use to retrieve the value.
	 * @param null   $default The default value to return if the key doesn't exist in the array.
	 * @return mixed|null The value for the key, or the default value passed.
	 */
	public static function get_value_or_default( array $array, string $key, $default = null ) {
		return array_key_exists( $key, $array ) ? $array[ $key ] : $default;
	}

	/**
	 * Converts an array of numbers to a human-readable range, such as "1,2,3,5" to "1-3, 5". It also supports
	 * floating point numbers, however with some perhaps unexpected / undefined behaviour if used within a range.
	 * Source: https://stackoverflow.com/a/34254663/4574
	 *
	 * @param array     $items    An array (in any order, see $sort) of individual numbers.
	 * @param string    $item_separator  The string that separates sequential range groups.  Defaults to ', '.
	 * @param string    $range_separator The string that separates ranges.  Defaults to '-'.  A plausible example otherwise would be ' to '.
	 * @param bool|true $sort     Sort the array prior to iterating?  You'll likely always want to sort, but if not, you can set this to false.
	 *
	 * @return string
	 */
	public static function to_ranges_string( array $items, string $item_separator = ', ', string $range_separator = '-', bool $sort = true ): string {
		if ( $sort ) {
			sort( $items );
		}

		$point = null;
		$range = false;
		$str   = '';

		foreach ( $items as $i ) {
			if ( null === $point ) {
				$str .= $i;
			} elseif ( ( $point + 1 ) === $i ) {
				$range = true;
			} else {
				if ( $range ) {
					$str  .= $range_separator . $point;
					$range = false;
				}
				$str .= $item_separator . $i;
			}
			$point = $i;
		}

		if ( $range ) {
			$str .= $range_separator . $point;
		}

		return $str;
	}

	/**
	 * Helper function to generate a callback which can be executed on an array to select a value from each item.
	 *
	 * @param string $selector_name Field/property/method name to select.
	 * @param int    $selector_type Selector type.
	 *
	 * @return \Closure Callback to select the value.
	 */
	private static function get_selector_callback( string $selector_name, int $selector_type = self::SELECT_BY_AUTO ): \Closure {
		if ( self::SELECT_BY_OBJECT_METHOD === $selector_type ) {
			$callback = function( $item ) use ( $selector_name ) {
				return $item->$selector_name();
			};
		} elseif ( self::SELECT_BY_OBJECT_PROPERTY === $selector_type ) {
			$callback = function( $item ) use ( $selector_name ) {
				return $item->$selector_name;
			};
		} elseif ( self::SELECT_BY_ARRAY_KEY === $selector_type ) {
			$callback = function( $item ) use ( $selector_name ) {
				return $item[ $selector_name ];
			};
		} else {
			$callback = function( $item ) use ( $selector_name ) {
				if ( is_array( $item ) ) {
					return $item[ $selector_name ];
				} elseif ( method_exists( $item, $selector_name ) ) {
					return $item->$selector_name();
				} else {
					return $item->$selector_name;
				}
			};
		}
		return $callback;
	}

	/**
	 * Select one single value from all the items in an array of either arrays or objects based on a selector.
	 * For arrays, the selector is a key name; for objects, the selector can be either a method name or a property name.
	 *
	 * @param array  $items Items to apply the selection to.
	 * @param string $selector_name Key, method or property name to use as a selector.
	 * @param int    $selector_type Selector type, one of the SELECT_BY_* constants.
	 * @return array The selected values.
	 */
	public static function select( array $items, string $selector_name, int $selector_type = self::SELECT_BY_AUTO ): array {
		$callback = self::get_selector_callback( $selector_name, $selector_type );
		return array_map( $callback, $items );
	}

	/**
	 * Returns a new assoc array with format [ $key1 => $item1, $key2 => $item2, ... ] where $key is the value of the selector and items are original items passed.
	 *
	 * @param array  $items Items to use for conversion.
	 * @param string $selector_name Key, method or property name to use as a selector.
	 * @param int    $selector_type Selector type, one of the SELECT_BY_* constants.
	 *
	 * @return array The converted assoc array.
	 */
	public static function select_as_assoc( array $items, string $selector_name, int $selector_type = self::SELECT_BY_AUTO ): array {
		$selector_callback = self::get_selector_callback( $selector_name, $selector_type );
		$result            = array();
		foreach ( $items as $item ) {
			$key = $selector_callback( $item );
			self::ensure_key_is_array( $result, $key );
			$result[ $key ][] = $item;
		}
		return $result;
	}

	/**
	 * Returns whether two assoc array are same. The comparison is done recursively by keys, and the functions returns on first difference found.
	 *
	 * @param array $array1 First array to compare.
	 * @param array $array2 Second array to compare.
	 * @param bool  $strict Whether to use strict comparison.
	 *
	 * @return bool Whether the arrays are different.
	 */
	public static function deep_compare_array_diff( array $array1, array $array2, bool $strict = true ) {
		return self::deep_compute_or_compare_array_diff( $array1, $array2, true, $strict );
	}

	/**
	 * Computes difference between two assoc arrays recursively. Similar to PHP's native assoc_array_diff, but also supports nested arrays.
	 *
	 * @param array $array1 First array.
	 * @param array $array2 Second array.
	 * @param bool  $strict Whether to also match type of values.
	 *
	 * @return array The difference between the two arrays.
	 */
	public static function deep_assoc_array_diff( array $array1, array $array2, bool $strict = true ): array {
		return self::deep_compute_or_compare_array_diff( $array1, $array2, false, $strict );
	}

	/**
	 * Helper method to compare to compute difference between two arrays. Comparison is done recursively.
	 *
	 * @param array $array1 First array.
	 * @param array $array2 Second array.
	 * @param bool  $compare Whether to compare the arrays. If true, then function will return false on first difference, in order to be slightly more efficient.
	 * @param bool  $strict Whether to do string comparison.
	 *
	 * @return array|bool The difference between the two arrays, or if array are same, depending upon $compare param.
	 */
	private static function deep_compute_or_compare_array_diff( array $array1, array $array2, bool $compare, bool $strict = true ) {
		$diff = array();
		foreach ( $array1 as $key => $value ) {
			if ( is_array( $value ) ) {
				if ( ! array_key_exists( $key, $array2 ) || ! is_array( $array2[ $key ] ) ) {
					if ( $compare ) {
						return true;
					}
					$diff[ $key ] = $value;
				}
				$new_diff = self::deep_assoc_array_diff( $value, $array2[ $key ], $strict );
				if ( ! empty( $new_diff ) ) {
					if ( $compare ) {
						return true;
					}
					$diff[ $key ] = $new_diff;
				}
			} elseif ( $strict ) {
				if ( ! array_key_exists( $key, $array2 ) || $value !== $array2[ $key ] ) {
					if ( $compare ) {
						return true;
					}
					$diff[ $key ] = $value;
				}
			} else {
				// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison -- Intentional when $strict is false.
				if ( ! array_key_exists( $key, $array2 ) || $value != $array2[ $key ] ) {
					if ( $compare ) {
						return true;
					}
					$diff[ $key ] = $value;
				}
			}
		}

		return $compare ? false : $diff;
	}

	/**
	 * Push a value to an array, but only if the value isn't in the array already.
	 *
	 * @param array $array The array.
	 * @param mixed $value The value to maybe push.
	 * @return bool True if the value has been added to the array, false if the value was already in the array.
	 */
	public static function push_once( array &$array, $value ) : bool {
		if ( in_array( $value, $array, true ) ) {
			return false;
		}

		$array[] = $value;
		return true;
	}

	/**
	 * Ensure that an associative array has a given key, and if not, set the key to an empty array.
	 *
	 * @param array  $array The array to check.
	 * @param string $key The key to check.
	 * @param bool   $throw_if_existing_is_not_array If true, an exception will be thrown if the key already exists in the array but the value is not an array.
	 * @return bool True if the key has been added to the array, false if not (the key already existed).
	 * @throws \Exception The key already exists in the array but the value is not an array.
	 */
	public static function ensure_key_is_array( array &$array, string $key, bool $throw_if_existing_is_not_array = false ): bool {
		if ( ! isset( $array[ $key ] ) ) {
			$array[ $key ] = array();
			return true;
		}

		if ( $throw_if_existing_is_not_array && ! is_array( $array[ $key ] ) ) {
			$type = is_object( $array[ $key ] ) ? get_class( $array[ $key ] ) : gettype( $array[ $key ] );
			throw new \Exception( "Array key exists but it's not an array, it's a {$type}" );
		}

		return false;
	}
}

