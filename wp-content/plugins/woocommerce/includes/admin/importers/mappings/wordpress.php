<?php
/**
 * WordPress mappings
 *
 * @package WooCommerce\Admin\Importers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add mappings for WordPress tables.
 *
 * @since 3.1.0
 * @param array $mappings Importer columns mappings.
 * @return array
 */
function wc_importer_wordpress_mappings( $mappings ) {

	$wp_mappings = array(
		'post_id'      => 'id',
		'post_title'   => 'name',
		'post_content' => 'description',
		'post_excerpt' => 'short_description',
		'post_parent'  => 'parent_id',
	);

	return array_merge( $mappings, $wp_mappings );
}
add_filter( 'woocommerce_csv_product_import_mapping_default_columns', 'wc_importer_wordpress_mappings' );
