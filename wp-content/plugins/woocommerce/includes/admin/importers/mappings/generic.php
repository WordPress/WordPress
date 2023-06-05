<?php
/**
 * Generic mappings
 *
 * @package WooCommerce\Admin\Importers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add generic mappings.
 *
 * @since 3.1.0
 * @param array $mappings Importer columns mappings.
 * @return array
 */
function wc_importer_generic_mappings( $mappings ) {
	$generic_mappings = array(
		__( 'Title', 'woocommerce' )         => 'name',
		__( 'Product Title', 'woocommerce' ) => 'name',
		__( 'Price', 'woocommerce' )         => 'regular_price',
		__( 'Parent SKU', 'woocommerce' )    => 'parent_id',
		__( 'Quantity', 'woocommerce' )      => 'stock_quantity',
		__( 'Menu order', 'woocommerce' )    => 'menu_order',
	);

	return array_merge( $mappings, $generic_mappings );
}
add_filter( 'woocommerce_csv_product_import_mapping_default_columns', 'wc_importer_generic_mappings' );
