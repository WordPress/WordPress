<?php
/**
 * Default mappings
 *
 * @package WooCommerce\Admin\Importers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Importer current locale.
 *
 * @since 3.1.0
 * @return string
 */
function wc_importer_current_locale() {
	$locale = get_locale();
	if ( function_exists( 'get_user_locale' ) ) {
		$locale = get_user_locale();
	}

	return $locale;
}

/**
 * Add English mapping placeholders when not using English as current language.
 *
 * @since 3.1.0
 * @param array $mappings Importer columns mappings.
 * @return array
 */
function wc_importer_default_english_mappings( $mappings ) {
	if ( 'en_US' === wc_importer_current_locale() ) {
		return $mappings;
	}

	$weight_unit    = get_option( 'woocommerce_weight_unit' );
	$dimension_unit = get_option( 'woocommerce_dimension_unit' );
	$new_mappings   = array(
		'ID'                                      => 'id',
		'Type'                                    => 'type',
		'SKU'                                     => 'sku',
		'Name'                                    => 'name',
		'Published'                               => 'published',
		'Is featured?'                            => 'featured',
		'Visibility in catalog'                   => 'catalog_visibility',
		'Short description'                       => 'short_description',
		'Description'                             => 'description',
		'Date sale price starts'                  => 'date_on_sale_from',
		'Date sale price ends'                    => 'date_on_sale_to',
		'Tax status'                              => 'tax_status',
		'Tax class'                               => 'tax_class',
		'In stock?'                               => 'stock_status',
		'Stock'                                   => 'stock_quantity',
		'Backorders allowed?'                     => 'backorders',
		'Low stock amount'                        => 'low_stock_amount',
		'Sold individually?'                      => 'sold_individually',
		sprintf( 'Weight (%s)', $weight_unit )    => 'weight',
		sprintf( 'Length (%s)', $dimension_unit ) => 'length',
		sprintf( 'Width (%s)', $dimension_unit )  => 'width',
		sprintf( 'Height (%s)', $dimension_unit ) => 'height',
		'Allow customer reviews?'                 => 'reviews_allowed',
		'Purchase note'                           => 'purchase_note',
		'Sale price'                              => 'sale_price',
		'Regular price'                           => 'regular_price',
		'Categories'                              => 'category_ids',
		'Tags'                                    => 'tag_ids',
		'Shipping class'                          => 'shipping_class_id',
		'Images'                                  => 'images',
		'Download limit'                          => 'download_limit',
		'Download expiry days'                    => 'download_expiry',
		'Parent'                                  => 'parent_id',
		'Upsells'                                 => 'upsell_ids',
		'Cross-sells'                             => 'cross_sell_ids',
		'Grouped products'                        => 'grouped_products',
		'External URL'                            => 'product_url',
		'Button text'                             => 'button_text',
		'Position'                                => 'menu_order',
	);

	return array_merge( $mappings, $new_mappings );
}
add_filter( 'woocommerce_csv_product_import_mapping_default_columns', 'wc_importer_default_english_mappings', 100 );

/**
 * Add English special mapping placeholders when not using English as current language.
 *
 * @since 3.1.0
 * @param array $mappings Importer columns mappings.
 * @return array
 */
function wc_importer_default_special_english_mappings( $mappings ) {
	if ( 'en_US' === wc_importer_current_locale() ) {
		return $mappings;
	}

	$new_mappings = array(
		'Attribute %d name'     => 'attributes:name',
		'Attribute %d value(s)' => 'attributes:value',
		'Attribute %d visible'  => 'attributes:visible',
		'Attribute %d global'   => 'attributes:taxonomy',
		'Attribute %d default'  => 'attributes:default',
		'Download %d ID'        => 'downloads:id',
		'Download %d name'      => 'downloads:name',
		'Download %d URL'       => 'downloads:url',
		'Meta: %s'              => 'meta:',
	);

	return array_merge( $mappings, $new_mappings );
}
add_filter( 'woocommerce_csv_product_import_mapping_special_columns', 'wc_importer_default_special_english_mappings', 100 );
