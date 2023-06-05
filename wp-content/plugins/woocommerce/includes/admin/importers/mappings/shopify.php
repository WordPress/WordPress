<?php
/**
 * Shopify mappings
 *
 * @package WooCommerce\Admin\Importers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add Shopify mappings.
 *
 * @since 3.7.0
 * @param array $mappings    Importer columns mappings.
 * @param array $raw_headers Raw headers from CSV being imported.
 * @return array
 */
function wc_importer_shopify_mappings( $mappings, $raw_headers ) {
	// Only map if this is looks like a Shopify export.
	if ( 0 !== count( array_diff( array( 'Title', 'Body (HTML)', 'Type', 'Variant SKU' ), $raw_headers ) ) ) {
		return $mappings;
	}
	$shopify_mappings = array(
		'Variant SKU'               => 'sku',
		'Title'                     => 'name',
		'Body (HTML)'               => 'description',
		'Quantity'                  => 'stock_quantity',
		'Variant Inventory Qty'     => 'stock_quantity',
		'Image Src'                 => 'images',
		'Variant Image'             => 'images',
		'Variant SKU'               => 'sku',
		'Variant Price'             => 'sale_price',
		'Variant Compare At Price'  => 'regular_price',
		'Type'                      => 'category_ids',
		'Tags'                      => 'tag_ids_spaces',
		'Variant Grams'             => 'weight',
		'Variant Requires Shipping' => 'meta:shopify_requires_shipping',
		'Variant Taxable'           => 'tax_status',
	);
	return array_merge( $mappings, $shopify_mappings );
}
add_filter( 'woocommerce_csv_product_import_mapping_default_columns', 'wc_importer_shopify_mappings', 10, 2 );

/**
 * Add special wildcard Shopify mappings.
 *
 * @since 3.7.0
 * @param array $mappings    Importer columns mappings.
 * @param array $raw_headers Raw headers from CSV being imported.
 * @return array
 */
function wc_importer_shopify_special_mappings( $mappings, $raw_headers ) {
	// Only map if this is looks like a Shopify export.
	if ( 0 !== count( array_diff( array( 'Title', 'Body (HTML)', 'Type', 'Variant SKU' ), $raw_headers ) ) ) {
		return $mappings;
	}
	$shopify_mappings = array(
		'Option%d Name'  => 'attributes:name',
		'Option%d Value' => 'attributes:value',
	);
	return array_merge( $mappings, $shopify_mappings );
}
add_filter( 'woocommerce_csv_product_import_mapping_special_columns', 'wc_importer_shopify_special_mappings', 10, 2 );

/**
 * Expand special Shopify columns to WC format.
 *
 * @since 3.7.0
 * @param  array $data Array of data.
 * @return array Expanded data.
 */
function wc_importer_shopify_expand_data( $data ) {
	if ( isset( $data['meta:shopify_requires_shipping'] ) ) {
		$requires_shipping = wc_string_to_bool( $data['meta:shopify_requires_shipping'] );

		if ( ! $requires_shipping ) {
			if ( isset( $data['type'] ) ) {
				$data['type'][] = 'virtual';
			} else {
				$data['type'] = array( 'virtual' );
			}
		}

		unset( $data['meta:shopify_requires_shipping'] );
	}
	return $data;
}
add_filter( 'woocommerce_product_importer_pre_expand_data', 'wc_importer_shopify_expand_data' );
