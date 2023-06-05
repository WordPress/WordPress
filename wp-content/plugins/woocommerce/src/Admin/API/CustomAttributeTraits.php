<?php
/**
 * Traits for handling custom product attributes and their terms.
 */

namespace Automattic\WooCommerce\Admin\API;

defined( 'ABSPATH' ) || exit;

/**
 * CustomAttributeTraits class.
 *
 * @internal
 */
trait CustomAttributeTraits {
	/**
	 * Get a single attribute by its slug.
	 *
	 * @internal
	 * @param string $slug The attribute slug.
	 * @return WP_Error|object The matching attribute object or WP_Error if not found.
	 */
	public function get_custom_attribute_by_slug( $slug ) {
		$matching_attributes = $this->get_custom_attributes( array( 'slug' => $slug ) );

		if ( empty( $matching_attributes ) ) {
			return new \WP_Error(
				'woocommerce_rest_product_attribute_not_found',
				__( 'No product attribute with that slug was found.', 'woocommerce' ),
				array( 'status' => 404 )
			);
		}

		foreach ( $matching_attributes as $attribute_key => $attribute_value ) {
			return array( $attribute_key => $attribute_value );
		}
	}

	/**
	 * Query custom attributes by name or slug.
	 *
	 * @param string $args Search arguments, either name or slug.
	 * @return array Matching attributes, formatted for response.
	 */
	protected function get_custom_attributes( $args ) {
		global $wpdb;

		$args = wp_parse_args(
			$args,
			array(
				'name' => '',
				'slug' => '',
			)
		);

		if ( empty( $args['name'] ) && empty( $args['slug'] ) ) {
			return array();
		}

		$mode = $args['name'] ? 'name' : 'slug';

		if ( 'name' === $mode ) {
			$name = $args['name'];
			// Get as close as we can to matching the name property of custom attributes using SQL.
			$like = '%"name";s:%:"%' . $wpdb->esc_like( $name ) . '%"%';
		} else {
			$slug = sanitize_title_for_query( $args['slug'] );
			// Get as close as we can to matching the slug property of custom attributes using SQL.
			$like = '%s:' . strlen( $slug ) . ':"' . $slug . '";a:6:{%';
		}

		// Find all serialized product attributes with names like the search string.
		$query_results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT meta_value
				FROM {$wpdb->postmeta}
				WHERE meta_key = '_product_attributes'
				AND meta_value LIKE %s
				LIMIT 100",
				$like
			),
			ARRAY_A
		);

		$custom_attributes = array();

		foreach ( $query_results as $raw_product_attributes ) {

			$meta_attributes = maybe_unserialize( $raw_product_attributes['meta_value'] );

			if ( empty( $meta_attributes ) || ! is_array( $meta_attributes ) ) {
				continue;
			}

			foreach ( $meta_attributes as $meta_attribute_key => $meta_attribute_value ) {
				$meta_value = array_merge(
					array(
						'name'        => '',
						'is_taxonomy' => 0,
					),
					(array) $meta_attribute_value
				);

				// Skip non-custom attributes.
				if ( ! empty( $meta_value['is_taxonomy'] ) ) {
					continue;
				}

				// Skip custom attributes that didn't match the query.
				// (There can be any number of attributes in the meta value).
				if ( ( 'name' === $mode ) && ( false === stripos( $meta_value['name'], $name ) ) ) {
					continue;
				}

				if ( ( 'slug' === $mode ) && ( $meta_attribute_key !== $slug ) ) {
					continue;
				}

				// Combine all values when there are multiple matching custom attributes.
				if ( isset( $custom_attributes[ $meta_attribute_key ] ) ) {
					$custom_attributes[ $meta_attribute_key ]['value'] .= ' ' . WC_DELIMITER . ' ' . $meta_value['value'];
				} else {
					$custom_attributes[ $meta_attribute_key ] = $meta_attribute_value;
				}
			}
		}

		return $custom_attributes;
	}
}
