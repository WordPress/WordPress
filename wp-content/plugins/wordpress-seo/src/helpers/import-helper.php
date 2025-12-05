<?php

namespace Yoast\WP\SEO\Helpers;

/**
 * The Import Helper.
 */
class Import_Helper {

	/**
	 * Flattens a multidimensional array of settings. Recursive.
	 *
	 * @param array  $array_to_flatten The array to be flattened.
	 * @param string $key_prefix       The key to be used as a prefix.
	 *
	 * @return array The flattened array.
	 */
	public function flatten_settings( $array_to_flatten, $key_prefix = '' ) {
		$result = [];
		foreach ( $array_to_flatten as $key => $value ) {
			if ( \is_array( $value ) ) {
				$result = \array_merge( $result, $this->flatten_settings( $value, $key_prefix . '/' . $key ) );
			}
			else {
				$result[ $key_prefix . '/' . $key ] = $value;
			}
		}

		return $result;
	}
}
