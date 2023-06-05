<?php
/**
 * WooCommerce Updates
 *
 * Functions for updating data, used by the background updater. These functions must be included
 * in the list returned by WC_Install::get_db_update_callbacks.
 *
 * Please note that these functions are invoked when WooCommerce is updated from a previous version,
 * but NOT when WooCommerce is newly installed.
 *
 * Database schema changes must be incorporated to the SQL returned by WC_Install::get_schema, which is applied
 * via dbDelta at both install and update time. If any other kind of database change is required
 * at install time (e.g. populating tables), use the 'woocommerce_installed' hook.
 *
 * @package WooCommerce\Functions
 * @version 3.3.0
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Database\Migrations\MigrationHelper;
use Automattic\WooCommerce\Internal\Admin\Marketing\MarketingSpecs;
use Automattic\WooCommerce\Internal\AssignDefaultCategory;
use Automattic\WooCommerce\Internal\ProductAttributesLookup\DataRegenerator;
use Automattic\WooCommerce\Internal\ProductAttributesLookup\LookupDataStore;
use Automattic\WooCommerce\Internal\ProductDownloads\ApprovedDirectories\Register as Download_Directories;
use Automattic\WooCommerce\Internal\ProductDownloads\ApprovedDirectories\Synchronize as Download_Directories_Sync;

/**
 * Update file paths for 2.0
 *
 * @return void
 */
function wc_update_200_file_paths() {
	global $wpdb;

	// Upgrade old style files paths to support multiple file paths.
	$existing_file_paths = $wpdb->get_results( "SELECT meta_value, meta_id, post_id FROM {$wpdb->postmeta} WHERE meta_key = '_file_path' AND meta_value != '';" );

	if ( $existing_file_paths ) {

		foreach ( $existing_file_paths as $existing_file_path ) {

			$old_file_path = trim( $existing_file_path->meta_value );

			if ( ! empty( $old_file_path ) ) {
				// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
				$file_paths = serialize( array( md5( $old_file_path ) => $old_file_path ) );

				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_key = '_file_paths', meta_value = %s WHERE meta_id = %d", $file_paths, $existing_file_path->meta_id ) );

				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}woocommerce_downloadable_product_permissions SET download_id = %s WHERE product_id = %d", md5( $old_file_path ), $existing_file_path->post_id ) );

			}
		}
	}
}

/**
 * Update permalinks for 2.0
 *
 * @return void
 */
function wc_update_200_permalinks() {
	// Setup default permalinks if shop page is defined.
	$permalinks   = get_option( 'woocommerce_permalinks' );
	$shop_page_id = wc_get_page_id( 'shop' );

	if ( empty( $permalinks ) && $shop_page_id > 0 ) {

		$base_slug = $shop_page_id > 0 && get_post( $shop_page_id ) ? get_page_uri( $shop_page_id ) : 'shop';

		$category_base = 'yes' === get_option( 'woocommerce_prepend_shop_page_to_urls' ) ? trailingslashit( $base_slug ) : '';
		$category_slug = get_option( 'woocommerce_product_category_slug' ) ? get_option( 'woocommerce_product_category_slug' ) : _x( 'product-category', 'slug', 'woocommerce' );
		$tag_slug      = get_option( 'woocommerce_product_tag_slug' ) ? get_option( 'woocommerce_product_tag_slug' ) : _x( 'product-tag', 'slug', 'woocommerce' );

		if ( 'yes' === get_option( 'woocommerce_prepend_shop_page_to_products' ) ) {
			$product_base = trailingslashit( $base_slug );
		} else {
			$product_slug = get_option( 'woocommerce_product_slug' );
			if ( false !== $product_slug && ! empty( $product_slug ) ) {
				$product_base = trailingslashit( $product_slug );
			} else {
				$product_base = trailingslashit( _x( 'product', 'slug', 'woocommerce' ) );
			}
		}

		if ( 'yes' === get_option( 'woocommerce_prepend_category_to_products' ) ) {
			$product_base .= trailingslashit( '%product_cat%' );
		}

		$permalinks = array(
			'product_base'   => untrailingslashit( $product_base ),
			'category_base'  => untrailingslashit( $category_base . $category_slug ),
			'attribute_base' => untrailingslashit( $category_base ),
			'tag_base'       => untrailingslashit( $category_base . $tag_slug ),
		);

		update_option( 'woocommerce_permalinks', $permalinks );
	}
}

/**
 * Update sub-category display options for 2.0
 *
 * @return void
 */
function wc_update_200_subcat_display() {
	// Update subcat display settings.
	if ( 'yes' === get_option( 'woocommerce_shop_show_subcategories' ) ) {
		if ( 'yes' === get_option( 'woocommerce_hide_products_when_showing_subcategories' ) ) {
			update_option( 'woocommerce_shop_page_display', 'subcategories' );
		} else {
			update_option( 'woocommerce_shop_page_display', 'both' );
		}
	}

	if ( 'yes' === get_option( 'woocommerce_show_subcategories' ) ) {
		if ( 'yes' === get_option( 'woocommerce_hide_products_when_showing_subcategories' ) ) {
			update_option( 'woocommerce_category_archive_display', 'subcategories' );
		} else {
			update_option( 'woocommerce_category_archive_display', 'both' );
		}
	}
}

/**
 * Update tax rates for 2.0
 *
 * @return void
 */
function wc_update_200_taxrates() {
	global $wpdb;

	// Update tax rates.
	$loop      = 0;
	$tax_rates = get_option( 'woocommerce_tax_rates' );

	if ( $tax_rates ) {
		foreach ( $tax_rates as $tax_rate ) {

			foreach ( $tax_rate['countries'] as $country => $states ) {

				$states = array_reverse( $states );

				foreach ( $states as $state ) {

					if ( '*' === $state ) {
						$state = '';
					}

					$wpdb->insert(
						$wpdb->prefix . 'woocommerce_tax_rates',
						array(
							'tax_rate_country'  => $country,
							'tax_rate_state'    => $state,
							'tax_rate'          => $tax_rate['rate'],
							'tax_rate_name'     => $tax_rate['label'],
							'tax_rate_priority' => 1,
							'tax_rate_compound' => ( 'yes' === $tax_rate['compound'] ) ? 1 : 0,
							'tax_rate_shipping' => ( 'yes' === $tax_rate['shipping'] ) ? 1 : 0,
							'tax_rate_order'    => $loop,
							'tax_rate_class'    => $tax_rate['class'],
						)
					);

					$loop++;
				}
			}
		}
	}

	$local_tax_rates = get_option( 'woocommerce_local_tax_rates' );

	if ( $local_tax_rates ) {
		foreach ( $local_tax_rates as $tax_rate ) {

			$location_type = ( 'postcode' === $tax_rate['location_type'] ) ? 'postcode' : 'city';

			if ( '*' === $tax_rate['state'] ) {
				$tax_rate['state'] = '';
			}

			$wpdb->insert(
				$wpdb->prefix . 'woocommerce_tax_rates',
				array(
					'tax_rate_country'  => $tax_rate['country'],
					'tax_rate_state'    => $tax_rate['state'],
					'tax_rate'          => $tax_rate['rate'],
					'tax_rate_name'     => $tax_rate['label'],
					'tax_rate_priority' => 2,
					'tax_rate_compound' => ( 'yes' === $tax_rate['compound'] ) ? 1 : 0,
					'tax_rate_shipping' => ( 'yes' === $tax_rate['shipping'] ) ? 1 : 0,
					'tax_rate_order'    => $loop,
					'tax_rate_class'    => $tax_rate['class'],
				)
			);

			$tax_rate_id = $wpdb->insert_id;

			if ( $tax_rate['locations'] ) {
				foreach ( $tax_rate['locations'] as $location ) {

					$wpdb->insert(
						$wpdb->prefix . 'woocommerce_tax_rate_locations',
						array(
							'location_code' => $location,
							'tax_rate_id'   => $tax_rate_id,
							'location_type' => $location_type,
						)
					);

				}
			}

			$loop++;
		}
	}

	update_option( 'woocommerce_tax_rates_backup', $tax_rates );
	update_option( 'woocommerce_local_tax_rates_backup', $local_tax_rates );
	delete_option( 'woocommerce_tax_rates' );
	delete_option( 'woocommerce_local_tax_rates' );
}

/**
 * Update order item line items for 2.0
 *
 * @return void
 */
function wc_update_200_line_items() {
	global $wpdb;

	// Now its time for the massive update to line items - move them to the new DB tables.
	// Reverse with UPDATE `wpwc_postmeta` SET meta_key = '_order_items' WHERE meta_key = '_order_items_old'.
	$order_item_rows = $wpdb->get_results(
		"SELECT meta_value, post_id FROM {$wpdb->postmeta} WHERE meta_key = '_order_items'"
	);

	foreach ( $order_item_rows as $order_item_row ) {

		$order_items = (array) maybe_unserialize( $order_item_row->meta_value );

		foreach ( $order_items as $order_item ) {

			if ( ! isset( $order_item['line_total'] ) && isset( $order_item['taxrate'] ) && isset( $order_item['cost'] ) ) {
				$order_item['line_tax']          = number_format( ( $order_item['cost'] * $order_item['qty'] ) * ( $order_item['taxrate'] / 100 ), 2, '.', '' );
				$order_item['line_total']        = $order_item['cost'] * $order_item['qty'];
				$order_item['line_subtotal_tax'] = $order_item['line_tax'];
				$order_item['line_subtotal']     = $order_item['line_total'];
			}

			$order_item['line_tax']          = isset( $order_item['line_tax'] ) ? $order_item['line_tax'] : 0;
			$order_item['line_total']        = isset( $order_item['line_total'] ) ? $order_item['line_total'] : 0;
			$order_item['line_subtotal_tax'] = isset( $order_item['line_subtotal_tax'] ) ? $order_item['line_subtotal_tax'] : 0;
			$order_item['line_subtotal']     = isset( $order_item['line_subtotal'] ) ? $order_item['line_subtotal'] : 0;

			$item_id = wc_add_order_item(
				$order_item_row->post_id,
				array(
					'order_item_name' => $order_item['name'],
					'order_item_type' => 'line_item',
				)
			);

			// Add line item meta.
			if ( $item_id ) {
				wc_add_order_item_meta( $item_id, '_qty', absint( $order_item['qty'] ) );
				wc_add_order_item_meta( $item_id, '_tax_class', $order_item['tax_class'] );
				wc_add_order_item_meta( $item_id, '_product_id', $order_item['id'] );
				wc_add_order_item_meta( $item_id, '_variation_id', $order_item['variation_id'] );
				wc_add_order_item_meta( $item_id, '_line_subtotal', wc_format_decimal( $order_item['line_subtotal'] ) );
				wc_add_order_item_meta( $item_id, '_line_subtotal_tax', wc_format_decimal( $order_item['line_subtotal_tax'] ) );
				wc_add_order_item_meta( $item_id, '_line_total', wc_format_decimal( $order_item['line_total'] ) );
				wc_add_order_item_meta( $item_id, '_line_tax', wc_format_decimal( $order_item['line_tax'] ) );

				$meta_rows = array();

				// Insert meta.
				if ( ! empty( $order_item['item_meta'] ) ) {
					foreach ( $order_item['item_meta'] as $key => $meta ) {
						// Backwards compatibility.
						if ( is_array( $meta ) && isset( $meta['meta_name'] ) ) {
							$meta_rows[] = '(' . $item_id . ',"' . esc_sql( $meta['meta_name'] ) . '","' . esc_sql( $meta['meta_value'] ) . '")';
						} else {
							$meta_rows[] = '(' . $item_id . ',"' . esc_sql( $key ) . '","' . esc_sql( $meta ) . '")';
						}
					}
				}

				// Insert meta rows at once.
				if ( count( $meta_rows ) > 0 ) {
					$wpdb->query(
						$wpdb->prepare(
							"INSERT INTO {$wpdb->prefix}woocommerce_order_itemmeta ( order_item_id, meta_key, meta_value )
							VALUES " . implode( ',', $meta_rows ) . ';', // @codingStandardsIgnoreLine
							$order_item_row->post_id
						)
					);
				}

				// Delete from DB (rename).
				$wpdb->query(
					$wpdb->prepare(
						"UPDATE {$wpdb->postmeta}
						SET meta_key = '_order_items_old'
						WHERE meta_key = '_order_items'
						AND post_id = %d",
						$order_item_row->post_id
					)
				);
			}

			unset( $meta_rows, $item_id, $order_item );
		}
	}

	// Do the same kind of update for order_taxes - move to lines.
	// Reverse with UPDATE `wpwc_postmeta` SET meta_key = '_order_taxes' WHERE meta_key = '_order_taxes_old'.
	$order_tax_rows = $wpdb->get_results(
		"SELECT meta_value, post_id FROM {$wpdb->postmeta}
		WHERE meta_key = '_order_taxes'"
	);

	foreach ( $order_tax_rows as $order_tax_row ) {

		$order_taxes = (array) maybe_unserialize( $order_tax_row->meta_value );

		if ( ! empty( $order_taxes ) ) {
			foreach ( $order_taxes as $order_tax ) {

				if ( ! isset( $order_tax['label'] ) || ! isset( $order_tax['cart_tax'] ) || ! isset( $order_tax['shipping_tax'] ) ) {
					continue;
				}

				$item_id = wc_add_order_item(
					$order_tax_row->post_id,
					array(
						'order_item_name' => $order_tax['label'],
						'order_item_type' => 'tax',
					)
				);

				// Add line item meta.
				if ( $item_id ) {
					wc_add_order_item_meta( $item_id, 'compound', absint( isset( $order_tax['compound'] ) ? $order_tax['compound'] : 0 ) );
					wc_add_order_item_meta( $item_id, 'tax_amount', wc_clean( $order_tax['cart_tax'] ) );
					wc_add_order_item_meta( $item_id, 'shipping_tax_amount', wc_clean( $order_tax['shipping_tax'] ) );
				}

				// Delete from DB (rename).
				$wpdb->query(
					$wpdb->prepare(
						"UPDATE {$wpdb->postmeta}
						SET meta_key = '_order_taxes_old'
						WHERE meta_key = '_order_taxes'
						AND post_id = %d",
						$order_tax_row->post_id
					)
				);
			}
		}
	}
}

/**
 * Update image settings for 2.0
 *
 * @return void
 */
function wc_update_200_images() {
	// Grab the pre 2.0 Image options and use to populate the new image options settings,
	// cleaning up afterwards like nice people do.
	foreach ( array( 'catalog', 'single', 'thumbnail' ) as $value ) {

		$old_settings = array_filter(
			array(
				'width'  => get_option( 'woocommerce_' . $value . '_image_width' ),
				'height' => get_option( 'woocommerce_' . $value . '_image_height' ),
				'crop'   => get_option( 'woocommerce_' . $value . '_image_crop' ),
			)
		);

		if ( ! empty( $old_settings ) && update_option( 'shop_' . $value . '_image_size', $old_settings ) ) {

			delete_option( 'woocommerce_' . $value . '_image_width' );
			delete_option( 'woocommerce_' . $value . '_image_height' );
			delete_option( 'woocommerce_' . $value . '_image_crop' );

		}
	}
}

/**
 * Update DB version for 2.0
 *
 * @return void
 */
function wc_update_200_db_version() {
	WC_Install::update_db_version( '2.0.0' );
}

/**
 * Update Brazilian States for 2.0.9
 *
 * @return void
 */
function wc_update_209_brazillian_state() {
	global $wpdb;

	// phpcs:disable WordPress.DB.SlowDBQuery

	// Update brazillian state codes.
	$wpdb->update(
		$wpdb->postmeta,
		array(
			'meta_value' => 'BA',
		),
		array(
			'meta_key'   => '_billing_state',
			'meta_value' => 'BH',
		)
	);
	$wpdb->update(
		$wpdb->postmeta,
		array(
			'meta_value' => 'BA',
		),
		array(
			'meta_key'   => '_shipping_state',
			'meta_value' => 'BH',
		)
	);
	$wpdb->update(
		$wpdb->usermeta,
		array(
			'meta_value' => 'BA',
		),
		array(
			'meta_key'   => 'billing_state',
			'meta_value' => 'BH',
		)
	);
	$wpdb->update(
		$wpdb->usermeta,
		array(
			'meta_value' => 'BA',
		),
		array(
			'meta_key'   => 'shipping_state',
			'meta_value' => 'BH',
		)
	);

	// phpcs:enable WordPress.DB.SlowDBQuery
}

/**
 * Update DB version for 2.0.9
 *
 * @return void
 */
function wc_update_209_db_version() {
	WC_Install::update_db_version( '2.0.9' );
}

/**
 * Remove pages for 2.1
 *
 * @return void
 */
function wc_update_210_remove_pages() {
	// Pages no longer used.
	wp_trash_post( get_option( 'woocommerce_pay_page_id' ) );
	wp_trash_post( get_option( 'woocommerce_thanks_page_id' ) );
	wp_trash_post( get_option( 'woocommerce_view_order_page_id' ) );
	wp_trash_post( get_option( 'woocommerce_change_password_page_id' ) );
	wp_trash_post( get_option( 'woocommerce_edit_address_page_id' ) );
	wp_trash_post( get_option( 'woocommerce_lost_password_page_id' ) );
}

/**
 * Update file paths to support multiple files for 2.1
 *
 * @return void
 */
function wc_update_210_file_paths() {
	global $wpdb;

	// Upgrade file paths to support multiple file paths + names etc.
	$existing_file_paths = $wpdb->get_results( "SELECT meta_value, meta_id FROM {$wpdb->postmeta} WHERE meta_key = '_file_paths' AND meta_value != '';" );

	if ( $existing_file_paths ) {

		foreach ( $existing_file_paths as $existing_file_path ) {

			$needs_update = false;
			$new_value    = array();
			$value        = maybe_unserialize( trim( $existing_file_path->meta_value ) );

			if ( $value ) {
				foreach ( $value as $key => $file ) {
					if ( ! is_array( $file ) ) {
						$needs_update      = true;
						$new_value[ $key ] = array(
							'file' => $file,
							'name' => wc_get_filename_from_url( $file ),
						);
					} else {
						$new_value[ $key ] = $file;
					}
				}
				if ( $needs_update ) {
					// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
					$new_value = serialize( $new_value );

					$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_key = %s, meta_value = %s WHERE meta_id = %d", '_downloadable_files', $new_value, $existing_file_path->meta_id ) );
				}
			}
		}
	}
}

/**
 * Update DB version for 2.1
 *
 * @return void
 */
function wc_update_210_db_version() {
	WC_Install::update_db_version( '2.1.0' );
}

/**
 * Update shipping options for 2.2
 *
 * @return void
 */
function wc_update_220_shipping() {
	$woocommerce_ship_to_destination = 'shipping';

	if ( get_option( 'woocommerce_ship_to_billing_address_only' ) === 'yes' ) {
		$woocommerce_ship_to_destination = 'billing_only';
	} elseif ( get_option( 'woocommerce_ship_to_billing' ) === 'yes' ) {
		$woocommerce_ship_to_destination = 'billing';
	}

	add_option( 'woocommerce_ship_to_destination', $woocommerce_ship_to_destination, '', 'no' );
}

/**
 * Update order statuses for 2.2
 *
 * @return void
 */
function wc_update_220_order_status() {
	global $wpdb;
	$wpdb->query(
		"UPDATE {$wpdb->posts} as posts
		LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID = rel.object_id
		LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
		LEFT JOIN {$wpdb->terms} AS term USING( term_id )
		SET posts.post_status = 'wc-pending'
		WHERE posts.post_type = 'shop_order'
		AND posts.post_status = 'publish'
		AND tax.taxonomy = 'shop_order_status'
		AND	term.slug LIKE 'pending%';"
	);
	$wpdb->query(
		"UPDATE {$wpdb->posts} as posts
		LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID = rel.object_id
		LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
		LEFT JOIN {$wpdb->terms} AS term USING( term_id )
		SET posts.post_status = 'wc-processing'
		WHERE posts.post_type = 'shop_order'
		AND posts.post_status = 'publish'
		AND tax.taxonomy = 'shop_order_status'
		AND	term.slug LIKE 'processing%';"
	);
	$wpdb->query(
		"UPDATE {$wpdb->posts} as posts
		LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID = rel.object_id
		LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
		LEFT JOIN {$wpdb->terms} AS term USING( term_id )
		SET posts.post_status = 'wc-on-hold'
		WHERE posts.post_type = 'shop_order'
		AND posts.post_status = 'publish'
		AND tax.taxonomy = 'shop_order_status'
		AND	term.slug LIKE 'on-hold%';"
	);
	$wpdb->query(
		"UPDATE {$wpdb->posts} as posts
		LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID = rel.object_id
		LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
		LEFT JOIN {$wpdb->terms} AS term USING( term_id )
		SET posts.post_status = 'wc-completed'
		WHERE posts.post_type = 'shop_order'
		AND posts.post_status = 'publish'
		AND tax.taxonomy = 'shop_order_status'
		AND	term.slug LIKE 'completed%';"
	);
	$wpdb->query(
		"UPDATE {$wpdb->posts} as posts
		LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID = rel.object_id
		LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
		LEFT JOIN {$wpdb->terms} AS term USING( term_id )
		SET posts.post_status = 'wc-cancelled'
		WHERE posts.post_type = 'shop_order'
		AND posts.post_status = 'publish'
		AND tax.taxonomy = 'shop_order_status'
		AND	term.slug LIKE 'cancelled%';"
	);
	$wpdb->query(
		"UPDATE {$wpdb->posts} as posts
		LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID = rel.object_id
		LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
		LEFT JOIN {$wpdb->terms} AS term USING( term_id )
		SET posts.post_status = 'wc-refunded'
		WHERE posts.post_type = 'shop_order'
		AND posts.post_status = 'publish'
		AND tax.taxonomy = 'shop_order_status'
		AND	term.slug LIKE 'refunded%';"
	);
	$wpdb->query(
		"UPDATE {$wpdb->posts} as posts
		LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID = rel.object_id
		LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
		LEFT JOIN {$wpdb->terms} AS term USING( term_id )
		SET posts.post_status = 'wc-failed'
		WHERE posts.post_type = 'shop_order'
		AND posts.post_status = 'publish'
		AND tax.taxonomy = 'shop_order_status'
		AND	term.slug LIKE 'failed%';"
	);
}

/**
 * Update variations for 2.2
 *
 * @return void
 */
function wc_update_220_variations() {
	global $wpdb;
	// Update variations which manage stock.
	$update_variations = $wpdb->get_results(
		"SELECT DISTINCT posts.ID AS variation_id, posts.post_parent AS variation_parent FROM {$wpdb->posts} as posts
		LEFT OUTER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id AND postmeta.meta_key = '_stock'
		LEFT OUTER JOIN {$wpdb->postmeta} as postmeta2 ON posts.ID = postmeta2.post_id AND postmeta2.meta_key = '_manage_stock'
		WHERE posts.post_type = 'product_variation'
		AND postmeta.meta_value IS NOT NULL
		AND postmeta.meta_value != ''
		AND postmeta2.meta_value IS NULL"
	);

	foreach ( $update_variations as $variation ) {
		$parent_backorders = get_post_meta( $variation->variation_parent, '_backorders', true );
		add_post_meta( $variation->variation_id, '_manage_stock', 'yes', true );
		add_post_meta( $variation->variation_id, '_backorders', $parent_backorders ? $parent_backorders : 'no', true );
	}
}

/**
 * Update attributes for 2.2
 *
 * @return void
 */
function wc_update_220_attributes() {
	global $wpdb;
	// Update taxonomy names with correct sanitized names.
	$attribute_taxonomies = $wpdb->get_results( 'SELECT attribute_name, attribute_id FROM ' . $wpdb->prefix . 'woocommerce_attribute_taxonomies' );

	foreach ( $attribute_taxonomies as $attribute_taxonomy ) {
		$sanitized_attribute_name = wc_sanitize_taxonomy_name( $attribute_taxonomy->attribute_name );
		if ( $sanitized_attribute_name !== $attribute_taxonomy->attribute_name ) {
			if ( ! $wpdb->get_var( $wpdb->prepare( "SELECT 1=1 FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = %s;", $sanitized_attribute_name ) ) ) {
				// Update attribute.
				$wpdb->update(
					"{$wpdb->prefix}woocommerce_attribute_taxonomies",
					array(
						'attribute_name' => $sanitized_attribute_name,
					),
					array(
						'attribute_id' => $attribute_taxonomy->attribute_id,
					)
				);

				// Update terms.
				$wpdb->update(
					$wpdb->term_taxonomy,
					array( 'taxonomy' => wc_attribute_taxonomy_name( $sanitized_attribute_name ) ),
					array( 'taxonomy' => 'pa_' . $attribute_taxonomy->attribute_name )
				);
			}
		}
	}

	delete_transient( 'wc_attribute_taxonomies' );
	WC_Cache_Helper::invalidate_cache_group( 'woocommerce-attributes' );
}

/**
 * Update DB version for 2.2
 *
 * @return void
 */
function wc_update_220_db_version() {
	WC_Install::update_db_version( '2.2.0' );
}

/**
 * Update options for 2.3
 *
 * @return void
 */
function wc_update_230_options() {
	// _money_spent and _order_count may be out of sync - clear them
	delete_metadata( 'user', 0, '_money_spent', '', true );
	delete_metadata( 'user', 0, '_order_count', '', true );
	delete_metadata( 'user', 0, '_last_order', '', true );

	// To prevent taxes being hidden when using a default 'no address' in a store with tax inc prices, set the woocommerce_default_customer_address to use the store base address by default.
	if ( '' === get_option( 'woocommerce_default_customer_address', false ) && wc_prices_include_tax() ) {
		update_option( 'woocommerce_default_customer_address', 'base' );
	}
}

/**
 * Update DB version for 2.3
 *
 * @return void
 */
function wc_update_230_db_version() {
	WC_Install::update_db_version( '2.3.0' );
}

/**
 * Update calc discount options for 2.4
 *
 * @return void
 */
function wc_update_240_options() {
	/**
	 * Coupon discount calculations.
	 * Maintain the old coupon logic for upgrades.
	 */
	update_option( 'woocommerce_calc_discounts_sequentially', 'yes' );
}

/**
 * Update shipping methods for 2.4
 *
 * @return void
 */
function wc_update_240_shipping_methods() {
	/**
	 * Flat Rate Shipping.
	 * Update legacy options to new math based options.
	 */
	$shipping_methods = array(
		'woocommerce_flat_rates'                        => new WC_Shipping_Legacy_Flat_Rate(),
		'woocommerce_international_delivery_flat_rates' => new WC_Shipping_Legacy_International_Delivery(),
	);
	foreach ( $shipping_methods as $flat_rate_option_key => $shipping_method ) {
		// Stop this running more than once if routine is repeated.
		if ( version_compare( $shipping_method->get_option( 'version', 0 ), '2.4.0', '<' ) ) {
			$shipping_classes  = WC()->shipping()->get_shipping_classes();
			$has_classes       = count( $shipping_classes ) > 0;
			$cost_key          = $has_classes ? 'no_class_cost' : 'cost';
			$min_fee           = $shipping_method->get_option( 'minimum_fee' );
			$math_cost_strings = array(
				'cost'          => array(),
				'no_class_cost' => array(),
			);

			$math_cost_strings[ $cost_key ][] = $shipping_method->get_option( 'cost' );
			$fee                              = $shipping_method->get_option( 'fee' );

			if ( $fee ) {
				$math_cost_strings[ $cost_key ][] = strstr( $fee, '%' ) ? '[fee percent="' . str_replace( '%', '', $fee ) . '" min="' . esc_attr( $min_fee ) . '"]' : $fee;
			}

			foreach ( $shipping_classes as $shipping_class ) {
				$rate_key                       = 'class_cost_' . $shipping_class->slug;
				$math_cost_strings[ $rate_key ] = $math_cost_strings['no_class_cost'];
			}

			$flat_rates = array_filter( (array) get_option( $flat_rate_option_key, array() ) );

			if ( $flat_rates ) {
				foreach ( $flat_rates as $shipping_class => $rate ) {
					$rate_key = 'class_cost_' . $shipping_class;
					if ( $rate['cost'] || $rate['fee'] ) {
						$math_cost_strings[ $rate_key ][] = $rate['cost'];
						$math_cost_strings[ $rate_key ][] = strstr( $rate['fee'], '%' ) ? '[fee percent="' . str_replace( '%', '', $rate['fee'] ) . '" min="' . esc_attr( $min_fee ) . '"]' : $rate['fee'];
					}
				}
			}

			if ( 'item' === $shipping_method->type ) {
				foreach ( $math_cost_strings as $key => $math_cost_string ) {
					$math_cost_strings[ $key ] = array_filter( array_map( 'trim', $math_cost_strings[ $key ] ) );
					if ( ! empty( $math_cost_strings[ $key ] ) ) {
						$last_key                                = max( 0, count( $math_cost_strings[ $key ] ) - 1 );
						$math_cost_strings[ $key ][0]            = '( ' . $math_cost_strings[ $key ][0];
						$math_cost_strings[ $key ][ $last_key ] .= ' ) * [qty]';
					}
				}
			}

			$math_cost_strings['cost'][] = $shipping_method->get_option( 'cost_per_order' );

			// Save settings.
			foreach ( $math_cost_strings as $option_id => $math_cost_string ) {
				$shipping_method->settings[ $option_id ] = implode( ' + ', array_filter( $math_cost_string ) );
			}

			$shipping_method->settings['version'] = '2.4.0';
			$shipping_method->settings['type']    = 'item' === $shipping_method->settings['type'] ? 'class' : $shipping_method->settings['type'];

			update_option( $shipping_method->plugin_id . $shipping_method->id . '_settings', $shipping_method->settings );
		}
	}
}

/**
 * Update API keys for 2.4
 *
 * @return void
 */
function wc_update_240_api_keys() {
	global $wpdb;
	/**
	 * Update the old user API keys to the new Apps keys.
	 */
	$api_users = $wpdb->get_results( "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'woocommerce_api_consumer_key'" );
	$apps_keys = array();

	// Get user data.
	foreach ( $api_users as $_user ) {
		$user        = get_userdata( $_user->user_id );
		$apps_keys[] = array(
			'user_id'         => $user->ID,
			'permissions'     => $user->woocommerce_api_key_permissions,
			'consumer_key'    => wc_api_hash( $user->woocommerce_api_consumer_key ),
			'consumer_secret' => $user->woocommerce_api_consumer_secret,
			'truncated_key'   => substr( $user->woocommerce_api_consumer_secret, -7 ),
		);
	}

	if ( ! empty( $apps_keys ) ) {
		// Create new apps.
		foreach ( $apps_keys as $app ) {
			$wpdb->insert(
				$wpdb->prefix . 'woocommerce_api_keys',
				$app,
				array(
					'%d',
					'%s',
					'%s',
					'%s',
					'%s',
				)
			);
		}

		// Delete old user keys from usermeta.
		foreach ( $api_users as $_user ) {
			$user_id = intval( $_user->user_id );
			delete_user_meta( $user_id, 'woocommerce_api_consumer_key' );
			delete_user_meta( $user_id, 'woocommerce_api_consumer_secret' );
			delete_user_meta( $user_id, 'woocommerce_api_key_permissions' );
		}
	}
}

/**
 * Update webhooks for 2.4
 *
 * @return void
 */
function wc_update_240_webhooks() {
	// phpcs:disable WordPress.DB.SlowDBQuery

	/**
	 * Webhooks.
	 * Make sure order.update webhooks get the woocommerce_order_edit_status hook.
	 */
	$order_update_webhooks = get_posts(
		array(
			'posts_per_page' => -1,
			'post_type'      => 'shop_webhook',
			'meta_key'       => '_topic',
			'meta_value'     => 'order.updated',
		)
	);
	foreach ( $order_update_webhooks as $order_update_webhook ) {
		$webhook = new WC_Webhook( $order_update_webhook->ID );
		$webhook->set_topic( 'order.updated' );
	}

	// phpcs:enable WordPress.DB.SlowDBQuery
}

/**
 * Update refunds for 2.4
 *
 * @return void
 */
function wc_update_240_refunds() {
	global $wpdb;
	/**
	 * Refunds for full refunded orders.
	 * Update fully refunded orders to ensure they have a refund line item so reports add up.
	 */
	$refunded_orders = get_posts(
		array(
			'posts_per_page' => -1,
			'post_type'      => 'shop_order',
			'post_status'    => array( 'wc-refunded' ),
		)
	);

	// Ensure emails are disabled during this update routine.
	remove_all_actions( 'woocommerce_order_status_refunded_notification' );
	remove_all_actions( 'woocommerce_order_partially_refunded_notification' );
	remove_action( 'woocommerce_order_status_refunded', array( 'WC_Emails', 'send_transactional_email' ) );
	remove_action( 'woocommerce_order_partially_refunded', array( 'WC_Emails', 'send_transactional_email' ) );

	foreach ( $refunded_orders as $refunded_order ) {
		$order_total    = get_post_meta( $refunded_order->ID, '_order_total', true );
		$refunded_total = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT SUM( postmeta.meta_value )
				FROM $wpdb->postmeta AS postmeta
				INNER JOIN $wpdb->posts AS posts ON ( posts.post_type = 'shop_order_refund' AND posts.post_parent = %d )
				WHERE postmeta.meta_key = '_refund_amount'
				AND postmeta.post_id = posts.ID",
				$refunded_order->ID
			)
		);

		if ( $order_total > $refunded_total ) {
			wc_create_refund(
				array(
					'amount'     => $order_total - $refunded_total,
					'reason'     => __( 'Order fully refunded', 'woocommerce' ),
					'order_id'   => $refunded_order->ID,
					'line_items' => array(),
					'date'       => $refunded_order->post_modified,
				)
			);
		}
	}

	wc_delete_shop_order_transients();
}

/**
 * Update DB version for 2.4
 *
 * @return void
 */
function wc_update_240_db_version() {
	WC_Install::update_db_version( '2.4.0' );
}

/**
 * Update variations for 2.4.1
 *
 * @return void
 */
function wc_update_241_variations() {
	global $wpdb;

	// Select variations that don't have any _stock_status implemented on WooCommerce 2.2.
	$update_variations = $wpdb->get_results(
		"SELECT DISTINCT posts.ID AS variation_id, posts.post_parent AS variation_parent
		FROM {$wpdb->posts} as posts
		LEFT OUTER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id AND postmeta.meta_key = '_stock_status'
		WHERE posts.post_type = 'product_variation'
		AND postmeta.meta_value IS NULL"
	);

	foreach ( $update_variations as $variation ) {
		// Get the parent _stock_status.
		$parent_stock_status = get_post_meta( $variation->variation_parent, '_stock_status', true );

		// Set the _stock_status.
		add_post_meta( $variation->variation_id, '_stock_status', $parent_stock_status ? $parent_stock_status : 'instock', true );

		// Delete old product children array.
		delete_transient( 'wc_product_children_' . $variation->variation_parent );
	}

	// Invalidate old transients such as wc_var_price.
	WC_Cache_Helper::get_transient_version( 'product', true );
}

/**
 * Update DB version for 2.4.1
 *
 * @return void
 */
function wc_update_241_db_version() {
	WC_Install::update_db_version( '2.4.1' );
}

/**
 * Update currency settings for 2.5
 *
 * @return void
 */
function wc_update_250_currency() {
	global $wpdb;
	// Fix currency settings for LAK currency.
	$current_currency = get_option( 'woocommerce_currency' );

	if ( 'KIP' === $current_currency ) {
		update_option( 'woocommerce_currency', 'LAK' );
	}

	// phpcs:disable WordPress.DB.SlowDBQuery

	// Update LAK currency code.
	$wpdb->update(
		$wpdb->postmeta,
		array(
			'meta_value' => 'LAK',
		),
		array(
			'meta_key'   => '_order_currency',
			'meta_value' => 'KIP',
		)
	);

	// phpcs:enable WordPress.DB.SlowDBQuery
}

/**
 * Update DB version for 2.5
 *
 * @return void
 */
function wc_update_250_db_version() {
	WC_Install::update_db_version( '2.5.0' );
}

/**
 * Update ship to countries options for 2.6
 *
 * @return void
 */
function wc_update_260_options() {
	// woocommerce_calc_shipping option has been removed in 2.6.
	if ( 'no' === get_option( 'woocommerce_calc_shipping' ) ) {
		update_option( 'woocommerce_ship_to_countries', 'disabled' );
	}

	WC_Admin_Notices::add_notice( 'legacy_shipping' );
}

/**
 * Update term meta for 2.6
 *
 * @return void
 */
function wc_update_260_termmeta() {
	global $wpdb;
	/**
	 * Migrate term meta to WordPress tables.
	 */
	if ( get_option( 'db_version' ) >= 34370 && $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}woocommerce_termmeta';" ) ) {
		if ( $wpdb->query( "INSERT INTO {$wpdb->termmeta} ( term_id, meta_key, meta_value ) SELECT woocommerce_term_id, meta_key, meta_value FROM {$wpdb->prefix}woocommerce_termmeta;" ) ) {
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}woocommerce_termmeta" );
			wp_cache_flush();
		}
	}
}

/**
 * Update zones for 2.6
 *
 * @return void
 */
function wc_update_260_zones() {
	global $wpdb;
	/**
	 * Old (table rate) shipping zones to new core shipping zones migration.
	 * zone_enabled and zone_type are no longer used, but it's safe to leave them be.
	 */
	if ( $wpdb->get_var( "SHOW COLUMNS FROM `{$wpdb->prefix}woocommerce_shipping_zones` LIKE 'zone_enabled';" ) ) {
		$wpdb->query( "ALTER TABLE {$wpdb->prefix}woocommerce_shipping_zones CHANGE `zone_type` `zone_type` VARCHAR(40) NOT NULL DEFAULT '';" );
		$wpdb->query( "ALTER TABLE {$wpdb->prefix}woocommerce_shipping_zones CHANGE `zone_enabled` `zone_enabled` INT(1) NOT NULL DEFAULT 1;" );
	}
}

/**
 * Update zone methods for 2.6
 *
 * @return void
 */
function wc_update_260_zone_methods() {
	global $wpdb;

	/**
	 * Shipping zones in WC 2.6.0 use a table named woocommerce_shipping_zone_methods.
	 * Migrate the old data out of woocommerce_shipping_zone_shipping_methods into the new table and port over any known options (used by table rates and flat rate boxes).
	 */
	if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}woocommerce_shipping_zone_shipping_methods';" ) ) {
		$old_methods = $wpdb->get_results( "SELECT zone_id, shipping_method_type, shipping_method_order, shipping_method_id FROM {$wpdb->prefix}woocommerce_shipping_zone_shipping_methods;" );

		if ( $old_methods ) {
			$max_new_id = $wpdb->get_var( "SELECT MAX(instance_id) FROM {$wpdb->prefix}woocommerce_shipping_zone_methods" );
			$max_old_id = $wpdb->get_var( "SELECT MAX(shipping_method_id) FROM {$wpdb->prefix}woocommerce_shipping_zone_shipping_methods" );

			// Avoid ID conflicts.
			$wpdb->query( $wpdb->prepare( "ALTER TABLE {$wpdb->prefix}woocommerce_shipping_zone_methods AUTO_INCREMENT = %d;", max( $max_new_id, $max_old_id ) + 1 ) );

			// Store changes.
			$changes = array();

			// Move data.
			foreach ( $old_methods as $old_method ) {
				$wpdb->insert(
					$wpdb->prefix . 'woocommerce_shipping_zone_methods',
					array(
						'zone_id'      => $old_method->zone_id,
						'method_id'    => $old_method->shipping_method_type,
						'method_order' => $old_method->shipping_method_order,
					)
				);

				$new_instance_id = $wpdb->insert_id;

				// Move main settings.
				$older_settings_key = 'woocommerce_' . $old_method->shipping_method_type . '-' . $old_method->shipping_method_id . '_settings';
				$old_settings_key   = 'woocommerce_' . $old_method->shipping_method_type . '_' . $old_method->shipping_method_id . '_settings';
				add_option( 'woocommerce_' . $old_method->shipping_method_type . '_' . $new_instance_id . '_settings', get_option( $old_settings_key, get_option( $older_settings_key ) ) );

				// Handling for table rate and flat rate box shipping.
				if ( 'table_rate' === $old_method->shipping_method_type ) {
					// Move priority settings.
					add_option( 'woocommerce_table_rate_default_priority_' . $new_instance_id, get_option( 'woocommerce_table_rate_default_priority_' . $old_method->shipping_method_id ) );
					add_option( 'woocommerce_table_rate_priorities_' . $new_instance_id, get_option( 'woocommerce_table_rate_priorities_' . $old_method->shipping_method_id ) );

					// Move rates.
					$wpdb->update(
						$wpdb->prefix . 'woocommerce_shipping_table_rates',
						array(
							'shipping_method_id' => $new_instance_id,
						),
						array(
							'shipping_method_id' => $old_method->shipping_method_id,
						)
					);
				} elseif ( 'flat_rate_boxes' === $old_method->shipping_method_type ) {
					$wpdb->update(
						$wpdb->prefix . 'woocommerce_shipping_flat_rate_boxes',
						array(
							'shipping_method_id' => $new_instance_id,
						),
						array(
							'shipping_method_id' => $old_method->shipping_method_id,
						)
					);
				}

				$changes[ $old_method->shipping_method_id ] = $new_instance_id;
			}

			// $changes contains keys (old method ids) and values (new instance ids) if extra processing is needed in plugins.
			// Store this to an option so extensions can pick it up later, then fire an action.
			update_option( 'woocommerce_updated_instance_ids', $changes );
			do_action( 'woocommerce_updated_instance_ids', $changes );
		}
	}

	// Change ranges used to ...
	$wpdb->query( "UPDATE {$wpdb->prefix}woocommerce_shipping_zone_locations SET location_code = REPLACE( location_code, '-', '...' );" );
}

/**
 * Update refunds for 2.6
 *
 * @return void
 */
function wc_update_260_refunds() {
	global $wpdb;
	/**
	 * Refund item qty should be negative.
	 */
	$wpdb->query(
		"UPDATE {$wpdb->prefix}woocommerce_order_itemmeta as item_meta
		LEFT JOIN {$wpdb->prefix}woocommerce_order_items as items ON item_meta.order_item_id = items.order_item_id
		LEFT JOIN {$wpdb->posts} as posts ON items.order_id = posts.ID
		SET item_meta.meta_value = item_meta.meta_value * -1
		WHERE item_meta.meta_value > 0 AND item_meta.meta_key = '_qty' AND posts.post_type = 'shop_order_refund'"
	);
}

/**
 * Update DB version for 2.6
 *
 * @return void
 */
function wc_update_260_db_version() {
	WC_Install::update_db_version( '2.6.0' );
}

/**
 * Update webhooks for 3.0
 *
 * @return void
 */
function wc_update_300_webhooks() {
	// phpcs:disable WordPress.DB.SlowDBQuery

	/**
	 * Make sure product.update webhooks get the woocommerce_product_quick_edit_save
	 * and woocommerce_product_bulk_edit_save hooks.
	 */
	$product_update_webhooks = get_posts(
		array(
			'posts_per_page' => -1,
			'post_type'      => 'shop_webhook',
			'meta_key'       => '_topic',
			'meta_value'     => 'product.updated',
		)
	);
	foreach ( $product_update_webhooks as $product_update_webhook ) {
		$webhook = new WC_Webhook( $product_update_webhook->ID );
		$webhook->set_topic( 'product.updated' );
	}

	// phpcs:enable WordPress.DB.SlowDBQuery
}

/**
 * Add an index to the field comment_type to improve the response time of the query
 * used by WC_Comments::wp_count_comments() to get the number of comments by type.
 */
function wc_update_300_comment_type_index() {
	global $wpdb;

	$index_exists = $wpdb->get_row( "SHOW INDEX FROM {$wpdb->comments} WHERE column_name = 'comment_type' and key_name = 'woo_idx_comment_type'" );

	if ( is_null( $index_exists ) ) {
		// Add an index to the field comment_type to improve the response time of the query
		// used by WC_Comments::wp_count_comments() to get the number of comments by type.
		$wpdb->query( "ALTER TABLE {$wpdb->comments} ADD INDEX woo_idx_comment_type (comment_type)" );
	}
}

/**
 * Update grouped products for 3.0
 *
 * @return void
 */
function wc_update_300_grouped_products() {
	global $wpdb;
	$parents = $wpdb->get_col( "SELECT DISTINCT( post_parent ) FROM {$wpdb->posts} WHERE post_parent > 0 AND post_type = 'product';" );
	foreach ( $parents as $parent_id ) {
		$parent = wc_get_product( $parent_id );
		if ( $parent && $parent->is_type( 'grouped' ) ) {
			$children_ids = get_posts(
				array(
					'post_parent'    => $parent_id,
					'posts_per_page' => -1,
					'post_type'      => 'product',
					'fields'         => 'ids',
				)
			);
			update_post_meta( $parent_id, '_children', $children_ids );

			// Update children to remove the parent.
			$wpdb->update(
				$wpdb->posts,
				array(
					'post_parent' => 0,
				),
				array(
					'post_parent' => $parent_id,
				)
			);
		}
	}
}

/**
 * Update shipping tax classes for 3.0
 *
 * @return void
 */
function wc_update_300_settings() {
	$woocommerce_shipping_tax_class = get_option( 'woocommerce_shipping_tax_class' );
	if ( '' === $woocommerce_shipping_tax_class ) {
		update_option( 'woocommerce_shipping_tax_class', 'inherit' );
	} elseif ( 'standard' === $woocommerce_shipping_tax_class ) {
		update_option( 'woocommerce_shipping_tax_class', '' );
	}
}

/**
 * Convert meta values into term for product visibility.
 */
function wc_update_300_product_visibility() {
	global $wpdb;

	WC_Install::create_terms();

	$featured_term = get_term_by( 'name', 'featured', 'product_visibility' );

	if ( $featured_term ) {
		$wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO {$wpdb->term_relationships} SELECT post_id, %d, 0 FROM {$wpdb->postmeta} WHERE meta_key = '_featured' AND meta_value = 'yes';", $featured_term->term_taxonomy_id ) );
	}

	$exclude_search_term = get_term_by( 'name', 'exclude-from-search', 'product_visibility' );

	if ( $exclude_search_term ) {
		$wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO {$wpdb->term_relationships} SELECT post_id, %d, 0 FROM {$wpdb->postmeta} WHERE meta_key = '_visibility' AND meta_value IN ('hidden', 'catalog');", $exclude_search_term->term_taxonomy_id ) );
	}

	$exclude_catalog_term = get_term_by( 'name', 'exclude-from-catalog', 'product_visibility' );

	if ( $exclude_catalog_term ) {
		$wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO {$wpdb->term_relationships} SELECT post_id, %d, 0 FROM {$wpdb->postmeta} WHERE meta_key = '_visibility' AND meta_value IN ('hidden', 'search');", $exclude_catalog_term->term_taxonomy_id ) );
	}

	$outofstock_term = get_term_by( 'name', 'outofstock', 'product_visibility' );

	if ( $outofstock_term ) {
		$wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO {$wpdb->term_relationships} SELECT post_id, %d, 0 FROM {$wpdb->postmeta} WHERE meta_key = '_stock_status' AND meta_value = 'outofstock';", $outofstock_term->term_taxonomy_id ) );
	}

	$rating_term = get_term_by( 'name', 'rated-1', 'product_visibility' );

	if ( $rating_term ) {
		$wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO {$wpdb->term_relationships} SELECT post_id, %d, 0 FROM {$wpdb->postmeta} WHERE meta_key = '_wc_average_rating' AND ROUND( meta_value ) = 1;", $rating_term->term_taxonomy_id ) );
	}

	$rating_term = get_term_by( 'name', 'rated-2', 'product_visibility' );

	if ( $rating_term ) {
		$wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO {$wpdb->term_relationships} SELECT post_id, %d, 0 FROM {$wpdb->postmeta} WHERE meta_key = '_wc_average_rating' AND ROUND( meta_value ) = 2;", $rating_term->term_taxonomy_id ) );
	}

	$rating_term = get_term_by( 'name', 'rated-3', 'product_visibility' );

	if ( $rating_term ) {
		$wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO {$wpdb->term_relationships} SELECT post_id, %d, 0 FROM {$wpdb->postmeta} WHERE meta_key = '_wc_average_rating' AND ROUND( meta_value ) = 3;", $rating_term->term_taxonomy_id ) );
	}

	$rating_term = get_term_by( 'name', 'rated-4', 'product_visibility' );

	if ( $rating_term ) {
		$wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO {$wpdb->term_relationships} SELECT post_id, %d, 0 FROM {$wpdb->postmeta} WHERE meta_key = '_wc_average_rating' AND ROUND( meta_value ) = 4;", $rating_term->term_taxonomy_id ) );
	}

	$rating_term = get_term_by( 'name', 'rated-5', 'product_visibility' );

	if ( $rating_term ) {
		$wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO {$wpdb->term_relationships} SELECT post_id, %d, 0 FROM {$wpdb->postmeta} WHERE meta_key = '_wc_average_rating' AND ROUND( meta_value ) = 5;", $rating_term->term_taxonomy_id ) );
	}
}

/**
 * Update DB Version.
 */
function wc_update_300_db_version() {
	WC_Install::update_db_version( '3.0.0' );
}

/**
 * Add an index to the downloadable product permissions table to improve performance of update_user_by_order_id.
 */
function wc_update_310_downloadable_products() {
	global $wpdb;

	$index_exists = $wpdb->get_row( "SHOW INDEX FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions WHERE column_name = 'order_id' and key_name = 'order_id'" );

	if ( is_null( $index_exists ) ) {
		$wpdb->query( "ALTER TABLE {$wpdb->prefix}woocommerce_downloadable_product_permissions ADD INDEX order_id (order_id)" );
	}
}

/**
 * Find old order notes and ensure they have the correct type for exclusion.
 */
function wc_update_310_old_comments() {
	global $wpdb;

	$wpdb->query( "UPDATE $wpdb->comments comments LEFT JOIN $wpdb->posts as posts ON comments.comment_post_ID = posts.ID SET comment_type = 'order_note' WHERE posts.post_type = 'shop_order' AND comment_type = '';" );
}

/**
 * Update DB Version.
 */
function wc_update_310_db_version() {
	WC_Install::update_db_version( '3.1.0' );
}

/**
 * Update shop_manager capabilities.
 */
function wc_update_312_shop_manager_capabilities() {
	$role = get_role( 'shop_manager' );
	$role->remove_cap( 'unfiltered_html' );
}

/**
 * Update DB Version.
 */
function wc_update_312_db_version() {
	WC_Install::update_db_version( '3.1.2' );
}

/**
 * Update state codes for Mexico.
 */
function wc_update_320_mexican_states() {
	global $wpdb;

	$mx_states = array(
		'Distrito Federal'    => 'CMX',
		'Jalisco'             => 'JAL',
		'Nuevo Leon'          => 'NLE',
		'Aguascalientes'      => 'AGS',
		'Baja California'     => 'BCN',
		'Baja California Sur' => 'BCS',
		'Campeche'            => 'CAM',
		'Chiapas'             => 'CHP',
		'Chihuahua'           => 'CHH',
		'Coahuila'            => 'COA',
		'Colima'              => 'COL',
		'Durango'             => 'DGO',
		'Guanajuato'          => 'GTO',
		'Guerrero'            => 'GRO',
		'Hidalgo'             => 'HGO',
		'Estado de Mexico'    => 'MEX',
		'Michoacan'           => 'MIC',
		'Morelos'             => 'MOR',
		'Nayarit'             => 'NAY',
		'Oaxaca'              => 'OAX',
		'Puebla'              => 'PUE',
		'Queretaro'           => 'QRO',
		'Quintana Roo'        => 'ROO',
		'San Luis Potosi'     => 'SLP',
		'Sinaloa'             => 'SIN',
		'Sonora'              => 'SON',
		'Tabasco'             => 'TAB',
		'Tamaulipas'          => 'TMP',
		'Tlaxcala'            => 'TLA',
		'Veracruz'            => 'VER',
		'Yucatan'             => 'YUC',
		'Zacatecas'           => 'ZAC',
	);

	foreach ( $mx_states as $old => $new ) {
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE $wpdb->postmeta
				SET meta_value = %s
				WHERE meta_key IN ( '_billing_state', '_shipping_state' )
				AND meta_value = %s",
				$new,
				$old
			)
		);
		$wpdb->update(
			"{$wpdb->prefix}woocommerce_shipping_zone_locations",
			array(
				'location_code' => 'MX:' . $new,
			),
			array(
				'location_code' => 'MX:' . $old,
			)
		);
		$wpdb->update(
			"{$wpdb->prefix}woocommerce_tax_rates",
			array(
				'tax_rate_state' => strtoupper( $new ),
			),
			array(
				'tax_rate_state' => strtoupper( $old ),
			)
		);
	}
}

/**
 * Update DB Version.
 */
function wc_update_320_db_version() {
	WC_Install::update_db_version( '3.2.0' );
}

/**
 * Update image settings to use new aspect ratios and widths.
 */
function wc_update_330_image_options() {
	$old_thumbnail_size = get_option( 'shop_catalog_image_size', array() );
	$old_single_size    = get_option( 'shop_single_image_size', array() );

	if ( ! empty( $old_thumbnail_size['width'] ) ) {
		$width     = absint( $old_thumbnail_size['width'] );
		$height    = absint( $old_thumbnail_size['height'] );
		$hard_crop = ! empty( $old_thumbnail_size['crop'] );

		if ( ! $width ) {
			$width = 300;
		}

		if ( ! $height ) {
			$height = $width;
		}

		update_option( 'woocommerce_thumbnail_image_width', $width );

		// Calculate cropping mode from old image options.
		if ( ! $hard_crop ) {
			update_option( 'woocommerce_thumbnail_cropping', 'uncropped' );
		} elseif ( $width === $height ) {
			update_option( 'woocommerce_thumbnail_cropping', '1:1' );
		} else {
			$ratio    = $width / $height;
			$fraction = wc_decimal_to_fraction( $ratio );

			if ( $fraction ) {
				update_option( 'woocommerce_thumbnail_cropping', 'custom' );
				update_option( 'woocommerce_thumbnail_cropping_custom_width', $fraction[0] );
				update_option( 'woocommerce_thumbnail_cropping_custom_height', $fraction[1] );
			}
		}
	}

	// Single is uncropped.
	if ( ! empty( $old_single_size['width'] ) ) {
		update_option( 'woocommerce_single_image_width', absint( $old_single_size['width'] ) );
	}
}

/**
 * Migrate webhooks from post type to CRUD.
 */
function wc_update_330_webhooks() {
	register_post_type( 'shop_webhook' );

	// Map statuses from post_type to Webhooks CRUD.
	$statuses = array(
		'publish' => 'active',
		'draft'   => 'paused',
		'pending' => 'disabled',
	);

	$posts = get_posts(
		array(
			'posts_per_page' => -1,
			'post_type'      => 'shop_webhook',
			'post_status'    => 'any',
		)
	);

	foreach ( $posts as $post ) {
		$webhook = new WC_Webhook();
		$webhook->set_name( $post->post_title );
		$webhook->set_status( isset( $statuses[ $post->post_status ] ) ? $statuses[ $post->post_status ] : 'disabled' );
		$webhook->set_delivery_url( get_post_meta( $post->ID, '_delivery_url', true ) );
		$webhook->set_secret( get_post_meta( $post->ID, '_secret', true ) );
		$webhook->set_topic( get_post_meta( $post->ID, '_topic', true ) );
		$webhook->set_api_version( get_post_meta( $post->ID, '_api_version', true ) );
		$webhook->set_user_id( $post->post_author );
		$webhook->set_pending_delivery( false );
		$webhook->save();

		wp_delete_post( $post->ID, true );
	}

	unregister_post_type( 'shop_webhook' );
}

/**
 * Assign default cat to all products with no cats.
 */
function wc_update_330_set_default_product_cat() {
	/*
	 * When a product category is deleted, we need to check
	 * if the product has no categories assigned. Then assign
	 * it a default category.
	 */
	wc_get_container()->get( AssignDefaultCategory::class )->maybe_assign_default_product_cat();
}

/**
 * Update product stock status to use the new onbackorder status.
 */
function wc_update_330_product_stock_status() {
	global $wpdb;

	if ( 'yes' !== get_option( 'woocommerce_manage_stock' ) ) {
		return;
	}

	$min_stock_amount = (int) get_option( 'woocommerce_notify_no_stock_amount', 0 );

	// Get all products that have stock management enabled, stock less than or equal to min stock amount, and backorders enabled.
	$post_ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT t1.post_id FROM $wpdb->postmeta t1
			INNER JOIN $wpdb->postmeta t2
				ON t1.post_id = t2.post_id
				AND t1.meta_key = '_manage_stock' AND t1.meta_value = 'yes'
				AND t2.meta_key = '_stock' AND t2.meta_value <= %d
			INNER JOIN $wpdb->postmeta t3
				ON t2.post_id = t3.post_id
				AND t3.meta_key = '_backorders' AND ( t3.meta_value = 'yes' OR t3.meta_value = 'notify' )",
			$min_stock_amount
		)
	);

	if ( empty( $post_ids ) ) {
		return;
	}

	$post_ids = array_map( 'absint', $post_ids );

	// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
	// Set the status to onbackorder for those products.
	$wpdb->query(
		"UPDATE $wpdb->postmeta
		SET meta_value = 'onbackorder'
		WHERE meta_key = '_stock_status' AND post_id IN ( " . implode( ',', $post_ids ) . ' )'
	);
	// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared
}

/**
 * Clear addons page transients
 */
function wc_update_330_clear_transients() {
	delete_transient( 'wc_addons_sections' );
	delete_transient( 'wc_addons_featured' );
}

/**
 * Set PayPal's sandbox credentials.
 */
function wc_update_330_set_paypal_sandbox_credentials() {

	$paypal_settings = get_option( 'woocommerce_paypal_settings' );

	if ( isset( $paypal_settings['testmode'] ) && 'yes' === $paypal_settings['testmode'] ) {
		foreach ( array( 'api_username', 'api_password', 'api_signature' ) as $credential ) {
			if ( ! empty( $paypal_settings[ $credential ] ) ) {
				$paypal_settings[ 'sandbox_' . $credential ] = $paypal_settings[ $credential ];
			}
		}

		update_option( 'woocommerce_paypal_settings', $paypal_settings );
	}
}

/**
 * Update DB Version.
 */
function wc_update_330_db_version() {
	WC_Install::update_db_version( '3.3.0' );
}

/**
 * Update state codes for Ireland and BD.
 */
function wc_update_340_states() {
	$country_states = array(
		'IE' => array(
			'CK' => 'CO',
			'DN' => 'D',
			'GY' => 'G',
			'TY' => 'TA',
		),
		'BD' => array(
			'BAG'  => 'BD-05',
			'BAN'  => 'BD-01',
			'BAR'  => 'BD-02',
			'BARI' => 'BD-06',
			'BHO'  => 'BD-07',
			'BOG'  => 'BD-03',
			'BRA'  => 'BD-04',
			'CHA'  => 'BD-09',
			'CHI'  => 'BD-10',
			'CHU'  => 'BD-12',
			'COX'  => 'BD-11',
			'COM'  => 'BD-08',
			'DHA'  => 'BD-13',
			'DIN'  => 'BD-14',
			'FAR'  => 'BD-15',
			'FEN'  => 'BD-16',
			'GAI'  => 'BD-19',
			'GAZI' => 'BD-18',
			'GOP'  => 'BD-17',
			'HAB'  => 'BD-20',
			'JAM'  => 'BD-21',
			'JES'  => 'BD-22',
			'JHA'  => 'BD-25',
			'JHE'  => 'BD-23',
			'JOY'  => 'BD-24',
			'KHA'  => 'BD-29',
			'KHU'  => 'BD-27',
			'KIS'  => 'BD-26',
			'KUR'  => 'BD-28',
			'KUS'  => 'BD-30',
			'LAK'  => 'BD-31',
			'LAL'  => 'BD-32',
			'MAD'  => 'BD-36',
			'MAG'  => 'BD-37',
			'MAN'  => 'BD-33',
			'MEH'  => 'BD-39',
			'MOU'  => 'BD-38',
			'MUN'  => 'BD-35',
			'MYM'  => 'BD-34',
			'NAO'  => 'BD-48',
			'NAR'  => 'BD-43',
			'NARG' => 'BD-40',
			'NARD' => 'BD-42',
			'NAT'  => 'BD-44',
			'NAW'  => 'BD-45',
			'NET'  => 'BD-41',
			'NIL'  => 'BD-46',
			'NOA'  => 'BD-47',
			'PAB'  => 'BD-49',
			'PAN'  => 'BD-52',
			'PAT'  => 'BD-51',
			'PIR'  => 'BD-50',
			'RAJB' => 'BD-53',
			'RAJ'  => 'BD-54',
			'RAN'  => 'BD-56',
			'RANP' => 'BD-55',
			'SAT'  => 'BD-58',
			'SHA'  => 'BD-57',
			'SIR'  => 'BD-59',
			'SUN'  => 'BD-61',
			'SYL'  => 'BD-60',
			'TAN'  => 'BD-63',
			'THA'  => 'BD-64',
		),
	);

	update_option( 'woocommerce_update_340_states', $country_states );
}

/**
 * Update next state in the queue.
 *
 * @return bool True to run again, false if completed.
 */
function wc_update_340_state() {
	global $wpdb;

	$country_states = array_filter( (array) get_option( 'woocommerce_update_340_states', array() ) );

	if ( empty( $country_states ) ) {
		return false;
	}

	foreach ( $country_states as $country => $states ) {
		foreach ( $states as $old => $new ) {
			$wpdb->query(
				$wpdb->prepare(
					"UPDATE $wpdb->postmeta
					SET meta_value = %s
					WHERE meta_key IN ( '_billing_state', '_shipping_state' )
					AND meta_value = %s",
					$new,
					$old
				)
			);
			$wpdb->update(
				"{$wpdb->prefix}woocommerce_shipping_zone_locations",
				array(
					'location_code' => $country . ':' . $new,
				),
				array(
					'location_code' => $country . ':' . $old,
				)
			);
			$wpdb->update(
				"{$wpdb->prefix}woocommerce_tax_rates",
				array(
					'tax_rate_state' => strtoupper( $new ),
				),
				array(
					'tax_rate_state' => strtoupper( $old ),
				)
			);
			unset( $country_states[ $country ][ $old ] );

			if ( empty( $country_states[ $country ] ) ) {
				unset( $country_states[ $country ] );
			}
			break 2;
		}
	}

	if ( ! empty( $country_states ) ) {
		return update_option( 'woocommerce_update_340_states', $country_states );
	}

	delete_option( 'woocommerce_update_340_states' );

	return false;
}

/**
 * Set last active prop for users.
 */
function wc_update_340_last_active() {
	global $wpdb;
	// @codingStandardsIgnoreStart.
	$wpdb->query(
		$wpdb->prepare( "
			INSERT INTO {$wpdb->usermeta} (user_id, meta_key, meta_value)
			SELECT DISTINCT users.ID, 'wc_last_active', %s
			FROM {$wpdb->users} as users
			LEFT OUTER JOIN {$wpdb->usermeta} AS usermeta ON users.ID = usermeta.user_id AND usermeta.meta_key = 'wc_last_active'
			WHERE usermeta.meta_value IS NULL
			",
			(string) strtotime( date( 'Y-m-d', current_time( 'timestamp', true ) ) )
		)
	);
	// @codingStandardsIgnoreEnd.
}

/**
 * Update DB Version.
 */
function wc_update_340_db_version() {
	WC_Install::update_db_version( '3.4.0' );
}

/**
 * Remove duplicate foreign keys
 *
 * @return void
 */
function wc_update_343_cleanup_foreign_keys() {
	global $wpdb;

	$create_table_sql = $wpdb->get_var( "SHOW CREATE TABLE {$wpdb->prefix}wc_download_log", 1 );

	if ( ! empty( $create_table_sql ) ) {
		// Extract and remove the foreign key constraints matching %wc_download_log_ib%.
		if ( preg_match_all( '/CONSTRAINT `([^`]*wc_download_log_ib[^`]*)` FOREIGN KEY/', $create_table_sql, $matches ) && ! empty( $matches[1] ) ) {
			foreach ( $matches[1] as $foreign_key_name ) {
				$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_download_log DROP FOREIGN KEY `{$foreign_key_name}`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			}
		}
	}
}

/**
 * Update DB version.
 *
 * @return void
 */
function wc_update_343_db_version() {
	WC_Install::update_db_version( '3.4.3' );
}

/**
 * Recreate user roles so existing users will get the new capabilities.
 *
 * @return void
 */
function wc_update_344_recreate_roles() {
	WC_Install::remove_roles();
	WC_Install::create_roles();
}

/**
 * Update DB version.
 *
 * @return void
 */
function wc_update_344_db_version() {
	WC_Install::update_db_version( '3.4.4' );
}

/**
 * Set the comment type to 'review' for product reviews that don't have a comment type.
 */
function wc_update_350_reviews_comment_type() {
	global $wpdb;

	$wpdb->query(
		"UPDATE {$wpdb->prefix}comments JOIN {$wpdb->prefix}posts ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}comments.comment_post_ID AND ( {$wpdb->prefix}posts.post_type = 'product' OR {$wpdb->prefix}posts.post_type = 'product_variation' ) SET {$wpdb->prefix}comments.comment_type = 'review' WHERE {$wpdb->prefix}comments.comment_type = ''"
	);
}

/**
 * Update DB Version.
 */
function wc_update_350_db_version() {
	WC_Install::update_db_version( '3.5.0' );
}

/**
 * Drop the fk_wc_download_log_permission_id FK as we use a new one with the table and blog prefix for MS compatability.
 *
 * @return void
 */
function wc_update_352_drop_download_log_fk() {
	global $wpdb;

	$create_table_sql = $wpdb->get_var( "SHOW CREATE TABLE {$wpdb->prefix}wc_download_log", 1 );

	if ( ! empty( $create_table_sql ) ) {
		if ( strpos( $create_table_sql, 'CONSTRAINT `fk_wc_download_log_permission_id` FOREIGN KEY' ) !== false ) {
			$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_download_log DROP FOREIGN KEY fk_wc_download_log_permission_id" ); // phpcs:ignore WordPress.WP.PreparedSQL.NotPrepared
		}
	}
}

/**
 * Remove edit_user capabilities from shop managers and use "translated" capabilities instead.
 * See wc_shop_manager_has_capability function.
 */
function wc_update_354_modify_shop_manager_caps() {
	global $wp_roles;

	if ( ! class_exists( 'WP_Roles' ) ) {
		return;
	}

	if ( ! isset( $wp_roles ) ) {
		$wp_roles = new WP_Roles(); // @codingStandardsIgnoreLine
	}

	$wp_roles->remove_cap( 'shop_manager', 'edit_users' );
}

/**
 * Update DB Version.
 */
function wc_update_354_db_version() {
	WC_Install::update_db_version( '3.5.4' );
}

/**
 * Update product lookup tables in bulk.
 */
function wc_update_360_product_lookup_tables() {
	wc_update_product_lookup_tables();
}

/**
 * Renames ordering meta to be consistent across taxonomies.
 */
function wc_update_360_term_meta() {
	global $wpdb;

	$wpdb->query( "UPDATE {$wpdb->termmeta} SET meta_key = 'order' WHERE meta_key LIKE 'order_pa_%';" );
}

/**
 * Add new user_order_remaining_expires to speed up user download permission fetching.
 *
 * @return void
 */
function wc_update_360_downloadable_product_permissions_index() {
	global $wpdb;

	$index_exists = $wpdb->get_row( "SHOW INDEX FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions WHERE key_name = 'user_order_remaining_expires'" );

	if ( is_null( $index_exists ) ) {
		$wpdb->query( "ALTER TABLE {$wpdb->prefix}woocommerce_downloadable_product_permissions ADD INDEX user_order_remaining_expires (user_id,order_id,downloads_remaining,access_expires)" );
	}
}

/**
 * Update DB Version.
 */
function wc_update_360_db_version() {
	WC_Install::update_db_version( '3.6.0' );
}

/**
 * Put tax classes into a DB table.
 *
 * @return void
 */
function wc_update_370_tax_rate_classes() {
	global $wpdb;

	$classes = array_map( 'trim', explode( "\n", get_option( 'woocommerce_tax_classes' ) ) );

	if ( $classes ) {
		foreach ( $classes as $class ) {
			if ( empty( $class ) ) {
				continue;
			}
			WC_Tax::create_tax_class( $class );
		}
	}
	delete_option( 'woocommerce_tax_classes' );
}

/**
 * Update currency settings for 3.7.0
 *
 * @return void
 */
function wc_update_370_mro_std_currency() {
	global $wpdb;

	// Fix currency settings for MRU and STN currency.
	$current_currency = get_option( 'woocommerce_currency' );

	if ( 'MRO' === $current_currency ) {
		update_option( 'woocommerce_currency', 'MRU' );
	}

	if ( 'STD' === $current_currency ) {
		update_option( 'woocommerce_currency', 'STN' );
	}

	// Update MRU currency code.
	$wpdb->update(
		$wpdb->postmeta,
		array(
			'meta_value' => 'MRU', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		),
		array(
			'meta_key'   => '_order_currency', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value' => 'MRO', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);

	// Update STN currency code.
	$wpdb->update(
		$wpdb->postmeta,
		array(
			'meta_value' => 'STN', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		),
		array(
			'meta_key'   => '_order_currency', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value' => 'STD', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
}

/**
 * Update DB Version.
 */
function wc_update_370_db_version() {
	WC_Install::update_db_version( '3.7.0' );
}

/**
 * We've moved the MaxMind database to a new location, as per the TOS' requirement that the database not
 * be publicly accessible.
 */
function wc_update_390_move_maxmind_database() {
	// Make sure to use all of the correct filters to pull the local database path.
	$old_path = apply_filters( 'woocommerce_geolocation_local_database_path', WP_CONTENT_DIR . '/uploads/GeoLite2-Country.mmdb', 2 );

	// Generate a prefix for the old file and store it in the integration as it would expect it.
	$prefix = wp_generate_password( 32, false );
	update_option( 'woocommerce_maxmind_geolocation_settings', array( 'database_prefix' => $prefix ) );

	// Generate the new path in the same way that the integration will.
	$uploads_dir = wp_upload_dir();
	$new_path    = trailingslashit( $uploads_dir['basedir'] ) . 'woocommerce_uploads/' . $prefix . '-GeoLite2-Country.mmdb';
	$new_path    = apply_filters( 'woocommerce_geolocation_local_database_path', $new_path, 2 );
	$new_path    = apply_filters( 'woocommerce_maxmind_geolocation_database_path', $new_path );

	// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	@rename( $old_path, $new_path );
}

/**
 * So that we can best meet MaxMind's TOS, the geolocation database update cron should run once per 15 days.
 */
function wc_update_390_change_geolocation_database_update_cron() {
	wp_clear_scheduled_hook( 'woocommerce_geoip_updater' );
	wp_schedule_event( time() + ( DAY_IN_SECONDS * 15 ), 'fifteendays', 'woocommerce_geoip_updater' );
}

/**
 * Update DB version.
 */
function wc_update_390_db_version() {
	WC_Install::update_db_version( '3.9.0' );
}

/**
 * Increase column size
 */
function wc_update_400_increase_size_of_column() {
	global $wpdb;
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_product_meta_lookup MODIFY COLUMN `min_price` decimal(19,4) NULL default NULL" );
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_product_meta_lookup MODIFY COLUMN `max_price` decimal(19,4) NULL default NULL" );
}

/**
 * Reset ActionScheduler migration status. Needs AS >= 3.0 shipped with WC >= 4.0.
 */
function wc_update_400_reset_action_scheduler_migration_status() {
	if (
		class_exists( 'ActionScheduler_DataController' ) &&
		method_exists( 'ActionScheduler_DataController', 'mark_migration_incomplete' )
	) {
		\ActionScheduler_DataController::mark_migration_incomplete();
	}
}

/**
 * Update DB version.
 */
function wc_update_400_db_version() {
	WC_Install::update_db_version( '4.0.0' );
}

/**
 * Register attributes as terms for variable products, in increments of 100 products.
 *
 * This migration was added to support a new mechanism to improve the filtering of
 * variable products by attribute (https://github.com/woocommerce/woocommerce/pull/26260),
 * however that mechanism was later reverted (https://github.com/woocommerce/woocommerce/pull/27625)
 * due to numerous issues found. Thus the migration is no longer needed.
 *
 * @return bool true if the migration needs to be run again.
 */
function wc_update_440_insert_attribute_terms_for_variable_products() {
	return false;
}

/**
 * Update DB version.
 */
function wc_update_440_db_version() {
	WC_Install::update_db_version( '4.4.0' );
}

/**
 * Update DB version to 4.5.0.
 */
function wc_update_450_db_version() {
	WC_Install::update_db_version( '4.5.0' );
}

/**
 * Sanitize all coupons code.
 *
 * @return bool True to run again, false if completed.
 */
function wc_update_450_sanitize_coupons_code() {
	global $wpdb;

	$coupon_id      = 0;
	$last_coupon_id = get_option( 'woocommerce_update_450_last_coupon_id', '0' );

	$coupons = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT ID, post_title FROM $wpdb->posts WHERE ID > %d AND post_type = 'shop_coupon' LIMIT 10",
			$last_coupon_id
		),
		ARRAY_A
	);

	if ( empty( $coupons ) ) {
		delete_option( 'woocommerce_update_450_last_coupon_id' );
		return false;
	}

	foreach ( $coupons as $key => $data ) {
		$coupon_id = intval( $data['ID'] );
		$code      = trim( wp_filter_kses( $data['post_title'] ) );

		if ( ! empty( $code ) && $data['post_title'] !== $code ) {
			$wpdb->update(
				$wpdb->posts,
				array(
					'post_title' => $code,
				),
				array(
					'ID' => $coupon_id,
				),
				array(
					'%s',
				),
				array(
					'%d',
				)
			);

			// Clean cache.
			clean_post_cache( $coupon_id );
			wp_cache_delete( WC_Cache_Helper::get_cache_prefix( 'coupons' ) . 'coupon_id_from_code_' . $data['post_title'], 'coupons' );
		}
	}

	// Start the run again.
	if ( $coupon_id ) {
		return update_option( 'woocommerce_update_450_last_coupon_id', $coupon_id );
	}

	delete_option( 'woocommerce_update_450_last_coupon_id' );
	return false;
}

/**
 * Fixes product review count that might have been incorrect.
 *
 * See @link https://github.com/woocommerce/woocommerce/issues/27688.
 */
function wc_update_500_fix_product_review_count() {
	global $wpdb;

	$product_id      = 0;
	$last_product_id = get_option( 'woocommerce_update_500_last_product_id', '0' );

	$products_data = $wpdb->get_results(
		$wpdb->prepare(
			"
				SELECT post_id, meta_value
				FROM $wpdb->postmeta
				JOIN $wpdb->posts
					ON $wpdb->postmeta.post_id = $wpdb->posts.ID
				WHERE
					post_type = 'product'
					AND post_status = 'publish'
					AND post_id > %d
					AND meta_key = '_wc_review_count'
				ORDER BY post_id ASC
				LIMIT 10
			",
			$last_product_id
		),
		ARRAY_A
	);

	if ( empty( $products_data ) ) {
		delete_option( 'woocommerce_update_500_last_product_id' );
		return false;
	}

	$product_ids_to_check = array_column( $products_data, 'post_id' );
	$actual_review_counts = WC_Comments::get_review_counts_for_product_ids( $product_ids_to_check );

	foreach ( $products_data as $product_data ) {
		$product_id           = intval( $product_data['post_id'] );
		$current_review_count = intval( $product_data['meta_value'] );

		if ( intval( $actual_review_counts[ $product_id ] ) !== $current_review_count ) {
			WC_Comments::clear_transients( $product_id );
		}
	}

	// Start the run again.
	if ( $product_id ) {
		return update_option( 'woocommerce_update_500_last_product_id', $product_id );
	}

	delete_option( 'woocommerce_update_500_last_product_id' );
	return false;
}

/**
 * Update DB version to 5.0.0.
 */
function wc_update_500_db_version() {
	WC_Install::update_db_version( '5.0.0' );
}

/**
 * Creates the refund and returns policy page.
 *
 * See @link https://github.com/woocommerce/woocommerce/issues/29235.
 */
function wc_update_560_create_refund_returns_page() {
	/**
	 * Filter on the pages created to return what we expect.
	 *
	 * @param array $pages The default WC pages.
	 */
	function filter_created_pages( $pages ) {
		$page_to_create = array( 'refund_returns' );

		return array_intersect_key( $pages, array_flip( $page_to_create ) );
	}

	add_filter( 'woocommerce_create_pages', 'filter_created_pages' );

	WC_Install::create_pages();

	remove_filter( 'woocommerce_create_pages', 'filter_created_pages' );
}

/**
 * Update DB version to 5.6.0.
 */
function wc_update_560_db_version() {
	WC_Install::update_db_version( '5.6.0' );
}

/**
 * Migrate rate limit options to the new table.
 *
 * See @link https://github.com/woocommerce/woocommerce/issues/27103.
 */
function wc_update_600_migrate_rate_limit_options() {
	global $wpdb;

	$rate_limits   = $wpdb->get_results(
		"
			SELECT option_name, option_value
			FROM $wpdb->options
			WHERE option_name LIKE 'woocommerce_rate_limit_add_payment_method_%'
		",
		ARRAY_A
	);
	$prefix_length = strlen( 'woocommerce_rate_limit_' );

	foreach ( $rate_limits as $rate_limit ) {
		$new_delay = (int) $rate_limit['option_value'] - time();

		// Migrate the limit if it hasn't expired yet.
		if ( 0 < $new_delay ) {
			$action_id = substr( $rate_limit['option_name'], $prefix_length );
			WC_Rate_Limiter::set_rate_limit( $action_id, $new_delay );
		}

		delete_option( $rate_limit['option_name'] );
	}
}

/**
 * Update DB version to 6.0.0.
 */
function wc_update_600_db_version() {
	WC_Install::update_db_version( '6.0.0' );
}

/**
 * Create the product attributes lookup table and initiate its filling,
 * unless the table had been already created manually (via the tools page).
 *
 * @return false Always false, since the LookupDataStore class handles all the data filling process.
 */
function wc_update_630_create_product_attributes_lookup_table() {
	$data_store       = wc_get_container()->get( LookupDataStore::class );
	$data_regenerator = wc_get_container()->get( DataRegenerator::class );

	/**
	 * If the table exists and contains data, it was manually created by user before the migration ran.
	 * If the table exists but is empty, it was likely created right now via dbDelta, so a table regenerations is needed (unless one is in progress already).
	 */
	if ( ! $data_store->check_lookup_table_exists() || ( ! $data_store->lookup_table_has_data() && ! $data_store->regeneration_is_in_progress() ) ) {
		$data_regenerator->initiate_regeneration();
	}

	return false;
}

/**
 *
 * Update DB version to 6.3.0.
 */
function wc_update_630_db_version() {
	WC_Install::update_db_version( '6.3.0' );
}

/**
 * Create the primary key for the product attributes lookup table if it doesn't exist already.
 *
 * @return bool Always false.
 */
function wc_update_640_add_primary_key_to_product_attributes_lookup_table() {
	wc_get_container()->get( DataRegenerator::class )->create_table_primary_index();

	return false;
}

/**
 *
 * Update DB version to 6.4.0.
 */
function wc_update_640_db_version() {
	WC_Install::update_db_version( '6.4.0' );
}

/**
 * Add the standard WooCommerce upload directories to the Approved Product Download Directories list
 * and start populating it based on existing product download URLs, but do not enable the feature
 * (for existing installations, a site admin should review and make a conscious decision to enable).
 */
function wc_update_650_approved_download_directories() {
	$directory_sync = wc_get_container()->get( Download_Directories_Sync::class );
	$directory_sync->init_hooks();
	$directory_sync->init_feature( true, false );
}

/**
 * In some cases, the approved download directories table may not have been successfully created during the update to
 * 6.5.0. If this was the case we will need to re-initialize the feature.
 */
function wc_update_651_approved_download_directories() {
	global $wpdb;

	$download_directories = wc_get_container()->get( Download_Directories::class );
	$directory_sync       = wc_get_container()->get( Download_Directories_Sync::class );

	// Check if at least 1 row exists, without scanning the entire table.
	$is_populated = (bool) $wpdb->get_var(
		'SELECT 1 FROM ' . $download_directories->get_table() . ' LIMIT 1'
	);

	// If the table contains rules (or does not yet, but a sync is in-progress) we should do nothing else at this point.
	if ( $is_populated || $directory_sync->in_progress() ) {
		return;
	}

	// Otherwise, it seems reasonable to assume that the feature was not initialized as expected during the update to
	// 6.5.0. Let's give that another try.
	$directory_sync->init_hooks();
	$directory_sync->init_feature( true, false );
}

/**
 * Purges the comments count cache after 6.7.0 split reviews from the comments page.
 */
function wc_update_670_purge_comments_count_cache() {
	if ( ! is_callable( 'WC_Comments::delete_comments_count_cache' ) ) {
		return;
	}

	WC_Comments::delete_comments_count_cache();
}
/**
 * Remove unnecessary foreign keys.
 *
 * @return void
 */
function wc_update_700_remove_download_log_fk() {
	global $wpdb;

	$create_table_sql = $wpdb->get_var( "SHOW CREATE TABLE {$wpdb->prefix}wc_download_log", 1 );

	if ( ! empty( $create_table_sql ) ) {
		if ( preg_match_all( '/CONSTRAINT `([^`]*)` FOREIGN KEY/', $create_table_sql, $matches ) && ! empty( $matches[1] ) ) {
			foreach ( $matches[1] as $foreign_key_name ) {
				$wpdb->query( "ALTER TABLE {$wpdb->prefix}wc_download_log DROP FOREIGN KEY `{$foreign_key_name}`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			}
		}
	}
}

/**
 * Remove the transient data for recommended marketing extensions.
 */
function wc_update_700_remove_recommended_marketing_plugins_transient() {
	delete_transient( MarketingSpecs::RECOMMENDED_PLUGINS_TRANSIENT );
}

/**
 * Update the New Zealand state codes in the database
 * after they were updated in code to the CLDR standard.
 */
function wc_update_721_adjust_new_zealand_states() {
	return MigrationHelper::migrate_country_states(
		'NZ',
		array(
			'NL' => 'NTL',
			'AK' => 'AUK',
			'WA' => 'WKO',
			'BP' => 'BOP',
			'TK' => 'TKI',
			'GI' => 'GIS',
			'HB' => 'HKB',
			'MW' => 'MWT',
			'WE' => 'WGN',
			'NS' => 'NSN',
			'MB' => 'MBH',
			'TM' => 'TAS',
			'WC' => 'WTC',
			'CT' => 'CAN',
			'OT' => 'OTA',
			'SL' => 'STL',
		)
	);
}

/**
 * Update the Ukraine state codes in the database
 * after they were updated in code to the CLDR standard.
 */
function wc_update_721_adjust_ukraine_states() {
	return MigrationHelper::migrate_country_states(
		'UA',
		array(
			'VN' => 'UA05',
			'LH' => 'UA09',
			'VL' => 'UA07',
			'DP' => 'UA12',
			'DT' => 'UA14',
			'ZT' => 'UA18',
			'ZK' => 'UA21',
			'ZP' => 'UA23',
			'IF' => 'UA26',
			'KV' => 'UA32',
			'KH' => 'UA35',
			'LV' => 'UA46',
			'MY' => 'UA48',
			'OD' => 'UA51',
			'PL' => 'UA53',
			'RV' => 'UA56',
			'SM' => 'UA59',
			'TP' => 'UA61',
			'KK' => 'UA63',
			'KS' => 'UA65',
			'KM' => 'UA68',
			'CK' => 'UA71',
			'CH' => 'UA74',
			'CV' => 'UA77',
		)
	);
}

/**
 * Update the New Zealand state codes in the database after they were updated in code to the CLDR standard.
 *
 * This is a simple wrapper for the corresponding 7.2.1 update function. The reason we do this (instead of
 * reusing the original function directly) is for better traceability in the Action Scheduler log, in case
 * of problems.
 */
function wc_update_722_adjust_new_zealand_states() {
	return wc_update_721_adjust_new_zealand_states();
}

/**
 * Update the Ukraine state codes in the database after they were updated in code to the CLDR standard.
 *
 * This is a simple wrapper for the corresponding 7.2.1 update function. The reason we do this (instead of
 * reusing the original function directly) is for better traceability in the Action Scheduler log, in case
 * of problems.
 */
function wc_update_722_adjust_ukraine_states() {
	return wc_update_721_adjust_ukraine_states();
}

/**
 * Add new columns date_paid and date_completed to wp_wc_order_stats table in order to provide the option
 * of using the dates in the reports
 */
function wc_update_750_add_columns_to_order_stats_table() {
	global $wpdb;

	$wpdb->query(
		"UPDATE {$wpdb->prefix}wc_order_stats AS order_stats
		INNER JOIN {$wpdb->postmeta} AS postmeta
			ON postmeta.post_id = order_stats.order_id
			and postmeta.meta_key = '_date_paid'
		SET order_stats.date_paid = IFNULL(FROM_UNIXTIME(postmeta.meta_value), '0000-00-00 00:00:00');"
	);

	$wpdb->query(
		"UPDATE {$wpdb->prefix}wc_order_stats AS order_stats
		INNER JOIN {$wpdb->postmeta} AS postmeta
			ON postmeta.post_id = order_stats.order_id
			and postmeta.meta_key = '_date_completed'
		SET order_stats.date_completed = IFNULL(FROM_UNIXTIME(postmeta.meta_value), '0000-00-00 00:00:00');"
	);

}

/**
 * Disable the experimental product management experience.
 *
 * @return void
 */
function wc_update_750_disable_new_product_management_experience() {
	if ( 'yes' === get_option( 'woocommerce_new_product_management_enabled' ) ) {
		update_option( 'woocommerce_new_product_management_enabled', 'no' );
	}
}

/**
 * Remove the multichannel marketing feature flag and options. This feature is now enabled by default.
 */
function wc_update_770_remove_multichannel_marketing_feature_options() {
	delete_option( 'woocommerce_multichannel_marketing_enabled' );
	delete_option( 'woocommerce_marketing_overview_welcome_hidden' );
}
