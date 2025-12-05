<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO
 */

/**
 * WPSEO_Custom_Fields.
 */
class WPSEO_Custom_Fields {

	/**
	 * Custom fields cache.
	 *
	 * @var array|null
	 */
	protected static $custom_fields = null;

	/**
	 * Retrieves the custom field names as an array.
	 *
	 * @link WordPress core: wp-admin/includes/template.php. Reused query from it.
	 *
	 * @return array The custom fields.
	 */
	public static function get_custom_fields() {
		global $wpdb;

		// Use cached value if available.
		if ( self::$custom_fields !== null ) {
			return self::$custom_fields;
		}

		self::$custom_fields = [];

		/**
		 * Filters the number of custom fields to retrieve for the drop-down
		 * in the Custom Fields meta box.
		 *
		 * @param int $limit Number of custom fields to retrieve. Default 30.
		 */
		$limit  = apply_filters( 'postmeta_form_limit', 30 );
		$sql    = "SELECT DISTINCT meta_key
			FROM $wpdb->postmeta
			WHERE meta_key NOT BETWEEN '_' AND '_z' AND SUBSTRING(meta_key, 1, 1) != '_'
			LIMIT %d";
		$fields = $wpdb->get_col( $wpdb->prepare( $sql, $limit ) );

		/**
		 * Filters the custom fields that are auto-completed and replaced as replacement variables
		 * in the meta box and sidebar.
		 *
		 * @param string[] $fields The custom field names.
		 */
		$fields = apply_filters( 'wpseo_replacement_variables_custom_fields', $fields );

		if ( is_array( $fields ) ) {
			self::$custom_fields = array_map( [ 'WPSEO_Custom_Fields', 'add_custom_field_prefix' ], $fields );
		}

		return self::$custom_fields;
	}

	/**
	 * Adds the cf_ prefix to a field.
	 *
	 * @param string $field The field to prefix.
	 *
	 * @return string The prefixed field.
	 */
	private static function add_custom_field_prefix( $field ) {
		return 'cf_' . $field;
	}
}
