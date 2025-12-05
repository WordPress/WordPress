<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO
 */

/**
 * WPSEO_Custom_Taxonomies.
 */
class WPSEO_Custom_Taxonomies {

	/**
	 * Custom taxonomies cache.
	 *
	 * @var array|null
	 */
	protected static $custom_taxonomies = null;

	/**
	 * Gets the names of the custom taxonomies, prepends 'ct_' and 'ct_desc', and returns them in an array.
	 *
	 * @return array The custom taxonomy prefixed names.
	 */
	public static function get_custom_taxonomies() {
		// Use cached value if available.
		if ( self::$custom_taxonomies !== null ) {
			return self::$custom_taxonomies;
		}

		self::$custom_taxonomies = [];
		$args                    = [
			'public'   => true,
			'_builtin' => false,
		];
		$custom_taxonomies       = get_taxonomies( $args, 'names', 'and' );

		if ( is_array( $custom_taxonomies ) ) {
			foreach ( $custom_taxonomies as $custom_taxonomy ) {
				array_push(
					self::$custom_taxonomies,
					self::add_custom_taxonomies_prefix( $custom_taxonomy ),
					self::add_custom_taxonomies_description_prefix( $custom_taxonomy )
				);
			}
		}

		return self::$custom_taxonomies;
	}

	/**
	 * Adds the ct_ prefix to a taxonomy.
	 *
	 * @param string $taxonomy The taxonomy to prefix.
	 *
	 * @return string The prefixed taxonomy.
	 */
	private static function add_custom_taxonomies_prefix( $taxonomy ) {
		return 'ct_' . $taxonomy;
	}

	/**
	 * Adds the ct_desc_ prefix to a taxonomy.
	 *
	 * @param string $taxonomy The taxonomy to prefix.
	 *
	 * @return string The prefixed taxonomy.
	 */
	private static function add_custom_taxonomies_description_prefix( $taxonomy ) {
		return 'ct_desc_' . $taxonomy;
	}
}
