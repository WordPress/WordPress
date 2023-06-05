<?php
/**
 * Class WC_Product_Variation_Data_Store_CPT file.
 *
 * @package WooCommerce\DataStores
 */

use Automattic\Jetpack\Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC Variation Product Data Store: Stored in CPT.
 *
 * @version  3.0.0
 */
class WC_Product_Variation_Data_Store_CPT extends WC_Product_Data_Store_CPT implements WC_Object_Data_Store_Interface {

	/**
	 * Callback to remove unwanted meta data.
	 *
	 * @param object $meta Meta object.
	 * @return bool false if excluded.
	 */
	protected function exclude_internal_meta_keys( $meta ) {
		return ! in_array( $meta->meta_key, $this->internal_meta_keys, true ) && 0 !== stripos( $meta->meta_key, 'attribute_' ) && 0 !== stripos( $meta->meta_key, 'wp_' );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD Methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * Reads a product from the database and sets its data to the class.
	 *
	 * @since 3.0.0
	 * @param WC_Product_Variation $product Product object.
	 * @throws WC_Data_Exception If WC_Product::set_tax_status() is called with an invalid tax status (via read_product_data), or when passing an invalid ID.
	 */
	public function read( &$product ) {
		$product->set_defaults();

		if ( ! $product->get_id() ) {
			return;
		}

		$post_object = get_post( $product->get_id() );

		if ( ! $post_object ) {
			return;
		}

		if ( 'product_variation' !== $post_object->post_type ) {
			throw new WC_Data_Exception( 'variation_invalid_id', __( 'Invalid product type: passed ID does not correspond to a product variation.', 'woocommerce' ) );
		}

		$product->set_props(
			array(
				'name'              => $post_object->post_title,
				'slug'              => $post_object->post_name,
				'date_created'      => $this->string_to_timestamp( $post_object->post_date_gmt ),
				'date_modified'     => $this->string_to_timestamp( $post_object->post_modified_gmt ),
				'status'            => $post_object->post_status,
				'menu_order'        => $post_object->menu_order,
				'reviews_allowed'   => 'open' === $post_object->comment_status,
				'parent_id'         => $post_object->post_parent,
				'attribute_summary' => $post_object->post_excerpt,
			)
		);

		// The post parent is not a valid variable product so we should prevent this.
		if ( $product->get_parent_id( 'edit' ) && 'product' !== get_post_type( $product->get_parent_id( 'edit' ) ) ) {
			$product->set_parent_id( 0 );
		}

		$this->read_downloads( $product );
		$this->read_product_data( $product );
		$this->read_extra_data( $product );
		$product->set_attributes( wc_get_product_variation_attributes( $product->get_id() ) );

		$updates = array();
		/**
		 * If a variation title is not in sync with the parent e.g. saved prior to 3.0, or if the parent title has changed, detect here and update.
		 */
		$new_title = $this->generate_product_title( $product );

		if ( $post_object->post_title !== $new_title ) {
			$product->set_name( $new_title );
			$updates = array_merge( $updates, array( 'post_title' => $new_title ) );
		}

		/**
		 * If the attribute summary is not in sync, update here. Used when searching for variations by attribute values.
		 * This is meant to also cover the case when global attribute name or value is updated, then the attribute summary is updated
		 * for respective products when they're read.
		 */
		$new_attribute_summary = $this->generate_attribute_summary( $product );

		if ( $new_attribute_summary !== $post_object->post_excerpt ) {
			$product->set_attribute_summary( $new_attribute_summary );
			$updates = array_merge( $updates, array( 'post_excerpt' => $new_attribute_summary ) );
		}

		if ( ! empty( $updates ) ) {
			$GLOBALS['wpdb']->update( $GLOBALS['wpdb']->posts, $updates, array( 'ID' => $product->get_id() ) );
			clean_post_cache( $product->get_id() );
		}

		// Set object_read true once all data is read.
		$product->set_object_read( true );
	}

	/**
	 * Create a new product.
	 *
	 * @since 3.0.0
	 * @param WC_Product_Variation $product Product object.
	 */
	public function create( &$product ) {
		if ( ! $product->get_date_created() ) {
			$product->set_date_created( time() );
		}

		$new_title = $this->generate_product_title( $product );

		if ( $product->get_name( 'edit' ) !== $new_title ) {
			$product->set_name( $new_title );
		}

		$attribute_summary = $this->generate_attribute_summary( $product );
		$product->set_attribute_summary( $attribute_summary );

		// The post parent is not a valid variable product so we should prevent this.
		if ( $product->get_parent_id( 'edit' ) && 'product' !== get_post_type( $product->get_parent_id( 'edit' ) ) ) {
			$product->set_parent_id( 0 );
		}

		$id = wp_insert_post(
			apply_filters(
				'woocommerce_new_product_variation_data',
				array(
					'post_type'      => 'product_variation',
					'post_status'    => $product->get_status() ? $product->get_status() : 'publish',
					'post_author'    => get_current_user_id(),
					'post_title'     => $product->get_name( 'edit' ),
					'post_excerpt'   => $product->get_attribute_summary( 'edit' ),
					'post_content'   => '',
					'post_parent'    => $product->get_parent_id(),
					'comment_status' => 'closed',
					'ping_status'    => 'closed',
					'menu_order'     => $product->get_menu_order(),
					'post_date'      => gmdate( 'Y-m-d H:i:s', $product->get_date_created( 'edit' )->getOffsetTimestamp() ),
					'post_date_gmt'  => gmdate( 'Y-m-d H:i:s', $product->get_date_created( 'edit' )->getTimestamp() ),
					'post_name'      => $product->get_slug( 'edit' ),
				)
			),
			true
		);

		if ( $id && ! is_wp_error( $id ) ) {
			$product->set_id( $id );

			$this->update_post_meta( $product, true );
			$this->update_terms( $product, true );
			$this->update_visibility( $product, true );
			$this->update_attributes( $product, true );
			$this->handle_updated_props( $product );

			$product->save_meta_data();
			$product->apply_changes();

			$this->update_version_and_type( $product );
			$this->update_guid( $product );

			$this->clear_caches( $product );

			do_action( 'woocommerce_new_product_variation', $id, $product );
		}
	}

	/**
	 * Updates an existing product.
	 *
	 * @since 3.0.0
	 * @param WC_Product_Variation $product Product object.
	 */
	public function update( &$product ) {
		$product->save_meta_data();

		if ( ! $product->get_date_created() ) {
			$product->set_date_created( time() );
		}

		$new_title = $this->generate_product_title( $product );

		if ( $product->get_name( 'edit' ) !== $new_title ) {
			$product->set_name( $new_title );
		}

		// The post parent is not a valid variable product so we should prevent this.
		if ( $product->get_parent_id( 'edit' ) && 'product' !== get_post_type( $product->get_parent_id( 'edit' ) ) ) {
			$product->set_parent_id( 0 );
		}

		$changes = $product->get_changes();

		if ( array_intersect( array( 'attributes' ), array_keys( $changes ) ) ) {
			$product->set_attribute_summary( $this->generate_attribute_summary( $product ) );
		}

		// Only update the post when the post data changes.
		if ( array_intersect( array( 'name', 'parent_id', 'status', 'menu_order', 'date_created', 'date_modified', 'attributes' ), array_keys( $changes ) ) ) {
			$post_data = array(
				'post_title'        => $product->get_name( 'edit' ),
				'post_excerpt'      => $product->get_attribute_summary( 'edit' ),
				'post_parent'       => $product->get_parent_id( 'edit' ),
				'comment_status'    => 'closed',
				'post_status'       => $product->get_status( 'edit' ) ? $product->get_status( 'edit' ) : 'publish',
				'menu_order'        => $product->get_menu_order( 'edit' ),
				'post_date'         => gmdate( 'Y-m-d H:i:s', $product->get_date_created( 'edit' )->getOffsetTimestamp() ),
				'post_date_gmt'     => gmdate( 'Y-m-d H:i:s', $product->get_date_created( 'edit' )->getTimestamp() ),
				'post_modified'     => isset( $changes['date_modified'] ) ? gmdate( 'Y-m-d H:i:s', $product->get_date_modified( 'edit' )->getOffsetTimestamp() ) : current_time( 'mysql' ),
				'post_modified_gmt' => isset( $changes['date_modified'] ) ? gmdate( 'Y-m-d H:i:s', $product->get_date_modified( 'edit' )->getTimestamp() ) : current_time( 'mysql', 1 ),
				'post_type'         => 'product_variation',
				'post_name'         => $product->get_slug( 'edit' ),
			);

			/**
			 * When updating this object, to prevent infinite loops, use $wpdb
			 * to update data, since wp_update_post spawns more calls to the
			 * save_post action.
			 *
			 * This ensures hooks are fired by either WP itself (admin screen save),
			 * or an update purely from CRUD.
			 */
			if ( doing_action( 'save_post' ) ) {
				$GLOBALS['wpdb']->update( $GLOBALS['wpdb']->posts, $post_data, array( 'ID' => $product->get_id() ) );
				clean_post_cache( $product->get_id() );
			} else {
				wp_update_post( array_merge( array( 'ID' => $product->get_id() ), $post_data ) );
			}
			$product->read_meta_data( true ); // Refresh internal meta data, in case things were hooked into `save_post` or another WP hook.

		} else { // Only update post modified time to record this save event.
			$GLOBALS['wpdb']->update(
				$GLOBALS['wpdb']->posts,
				array(
					'post_modified'     => current_time( 'mysql' ),
					'post_modified_gmt' => current_time( 'mysql', 1 ),
				),
				array(
					'ID' => $product->get_id(),
				)
			);
			clean_post_cache( $product->get_id() );
		}

		$this->update_post_meta( $product );
		$this->update_terms( $product );
		$this->update_visibility( $product, true );
		$this->update_attributes( $product );
		$this->handle_updated_props( $product );

		$product->apply_changes();

		$this->update_version_and_type( $product );

		$this->clear_caches( $product );

		do_action( 'woocommerce_update_product_variation', $product->get_id(), $product );
	}

	/*
	|--------------------------------------------------------------------------
	| Additional Methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * Generates a title with attribute information for a variation.
	 * Products will get a title of the form "Name - Value, Value" or just "Name".
	 *
	 * @since 3.0.0
	 * @param WC_Product $product Product object.
	 * @return string
	 */
	protected function generate_product_title( $product ) {
		$attributes = (array) $product->get_attributes();

		// Do not include attributes if the product has 3+ attributes.
		$should_include_attributes = count( $attributes ) < 3;

		// Do not include attributes if an attribute name has 2+ words and the
		// product has multiple attributes.
		if ( $should_include_attributes && 1 < count( $attributes ) ) {
			foreach ( $attributes as $name => $value ) {
				if ( false !== strpos( $name, '-' ) ) {
					$should_include_attributes = false;
					break;
				}
			}
		}

		$should_include_attributes = apply_filters( 'woocommerce_product_variation_title_include_attributes', $should_include_attributes, $product );
		$separator                 = apply_filters( 'woocommerce_product_variation_title_attributes_separator', ' - ', $product );
		$title_base                = get_post_field( 'post_title', $product->get_parent_id() );
		$title_suffix              = $should_include_attributes ? wc_get_formatted_variation( $product, true, false ) : '';

		return apply_filters( 'woocommerce_product_variation_title', $title_suffix ? $title_base . $separator . $title_suffix : $title_base, $product, $title_base, $title_suffix );
	}

	/**
	 * Generates attribute summary for the variation.
	 *
	 * Attribute summary contains comma-delimited 'attribute_name: attribute_value' pairs for all attributes.
	 *
	 * @since 3.6.0
	 * @param WC_Product_Variation $product Product variation to generate the attribute summary for.
	 *
	 * @return string
	 */
	protected function generate_attribute_summary( $product ) {
		return wc_get_formatted_variation( $product, true, true );
	}

	/**
	 * Make sure we store the product version (to track data changes).
	 *
	 * @param WC_Product $product Product object.
	 * @since 3.0.0
	 */
	protected function update_version_and_type( &$product ) {
		wp_set_object_terms( $product->get_id(), '', 'product_type' );
		update_post_meta( $product->get_id(), '_product_version', Constants::get_constant( 'WC_VERSION' ) );
	}

	/**
	 * Read post data.
	 *
	 * @since 3.0.0
	 * @param WC_Product_Variation $product Product object.
	 * @throws WC_Data_Exception If WC_Product::set_tax_status() is called with an invalid tax status.
	 */
	protected function read_product_data( &$product ) {
		$id = $product->get_id();

		$product->set_props(
			array(
				'description'       => get_post_meta( $id, '_variation_description', true ),
				'regular_price'     => get_post_meta( $id, '_regular_price', true ),
				'sale_price'        => get_post_meta( $id, '_sale_price', true ),
				'date_on_sale_from' => get_post_meta( $id, '_sale_price_dates_from', true ),
				'date_on_sale_to'   => get_post_meta( $id, '_sale_price_dates_to', true ),
				'manage_stock'      => get_post_meta( $id, '_manage_stock', true ),
				'stock_status'      => get_post_meta( $id, '_stock_status', true ),
				'low_stock_amount'  => get_post_meta( $id, '_low_stock_amount', true ),
				'shipping_class_id' => current( $this->get_term_ids( $id, 'product_shipping_class' ) ),
				'virtual'           => get_post_meta( $id, '_virtual', true ),
				'downloadable'      => get_post_meta( $id, '_downloadable', true ),
				'gallery_image_ids' => array_filter( explode( ',', get_post_meta( $id, '_product_image_gallery', true ) ) ),
				'download_limit'    => get_post_meta( $id, '_download_limit', true ),
				'download_expiry'   => get_post_meta( $id, '_download_expiry', true ),
				'image_id'          => get_post_thumbnail_id( $id ),
				'backorders'        => get_post_meta( $id, '_backorders', true ),
				'sku'               => get_post_meta( $id, '_sku', true ),
				'stock_quantity'    => get_post_meta( $id, '_stock', true ),
				'weight'            => get_post_meta( $id, '_weight', true ),
				'length'            => get_post_meta( $id, '_length', true ),
				'width'             => get_post_meta( $id, '_width', true ),
				'height'            => get_post_meta( $id, '_height', true ),
				'tax_class'         => ! metadata_exists( 'post', $id, '_tax_class' ) ? 'parent' : get_post_meta( $id, '_tax_class', true ),
			)
		);

		if ( $product->is_on_sale( 'edit' ) ) {
			$product->set_price( $product->get_sale_price( 'edit' ) );
		} else {
			$product->set_price( $product->get_regular_price( 'edit' ) );
		}

		$parent_object   = get_post( $product->get_parent_id() );
		$terms           = get_the_terms( $product->get_parent_id(), 'product_visibility' );
		$term_names      = is_array( $terms ) ? wp_list_pluck( $terms, 'name' ) : array();
		$exclude_search  = in_array( 'exclude-from-search', $term_names, true );
		$exclude_catalog = in_array( 'exclude-from-catalog', $term_names, true );

		if ( $exclude_search && $exclude_catalog ) {
			$catalog_visibility = 'hidden';
		} elseif ( $exclude_search ) {
			$catalog_visibility = 'catalog';
		} elseif ( $exclude_catalog ) {
			$catalog_visibility = 'search';
		} else {
			$catalog_visibility = 'visible';
		}

		$product->set_parent_data(
			array(
				'title'              => $parent_object ? $parent_object->post_title : '',
				'status'             => $parent_object ? $parent_object->post_status : '',
				'sku'                => get_post_meta( $product->get_parent_id(), '_sku', true ),
				'manage_stock'       => get_post_meta( $product->get_parent_id(), '_manage_stock', true ),
				'backorders'         => get_post_meta( $product->get_parent_id(), '_backorders', true ),
				'stock_quantity'     => wc_stock_amount( get_post_meta( $product->get_parent_id(), '_stock', true ) ),
				'weight'             => get_post_meta( $product->get_parent_id(), '_weight', true ),
				'length'             => get_post_meta( $product->get_parent_id(), '_length', true ),
				'width'              => get_post_meta( $product->get_parent_id(), '_width', true ),
				'height'             => get_post_meta( $product->get_parent_id(), '_height', true ),
				'tax_class'          => get_post_meta( $product->get_parent_id(), '_tax_class', true ),
				'shipping_class_id'  => absint( current( $this->get_term_ids( $product->get_parent_id(), 'product_shipping_class' ) ) ),
				'image_id'           => get_post_thumbnail_id( $product->get_parent_id() ),
				'purchase_note'      => get_post_meta( $product->get_parent_id(), '_purchase_note', true ),
				'catalog_visibility' => $catalog_visibility,
			)
		);

		// Pull data from the parent when there is no user-facing way to set props.
		$product->set_sold_individually( get_post_meta( $product->get_parent_id(), '_sold_individually', true ) );
		$product->set_tax_status( get_post_meta( $product->get_parent_id(), '_tax_status', true ) );
		$product->set_cross_sell_ids( get_post_meta( $product->get_parent_id(), '_crosssell_ids', true ) );
	}

	/**
	 * For all stored terms in all taxonomies, save them to the DB.
	 *
	 * @since 3.0.0
	 * @param WC_Product $product Product object.
	 * @param bool       $force Force update. Used during create.
	 */
	protected function update_terms( &$product, $force = false ) {
		$changes = $product->get_changes();

		if ( $force || array_key_exists( 'shipping_class_id', $changes ) ) {
			wp_set_post_terms( $product->get_id(), array( $product->get_shipping_class_id( 'edit' ) ), 'product_shipping_class', false );
		}
	}

	/**
	 * Update visibility terms based on props.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Product $product Product object.
	 * @param bool       $force Force update. Used during create.
	 */
	protected function update_visibility( &$product, $force = false ) {
		$changes = $product->get_changes();

		if ( $force || array_intersect( array( 'stock_status' ), array_keys( $changes ) ) ) {
			$terms = array();

			if ( 'outofstock' === $product->get_stock_status() ) {
				$terms[] = 'outofstock';
			}

			wp_set_post_terms( $product->get_id(), $terms, 'product_visibility', false );
		}
	}

	/**
	 * Update attribute meta values.
	 *
	 * @since 3.0.0
	 * @param WC_Product $product Product object.
	 * @param bool       $force Force update. Used during create.
	 */
	protected function update_attributes( &$product, $force = false ) {
		$changes = $product->get_changes();

		if ( $force || array_key_exists( 'attributes', $changes ) ) {
			global $wpdb;

			$product_id             = $product->get_id();
			$attributes             = $product->get_attributes();
			$updated_attribute_keys = array();
			foreach ( $attributes as $key => $value ) {
				update_post_meta( $product_id, 'attribute_' . $key, wp_slash( $value ) );
				$updated_attribute_keys[] = 'attribute_' . $key;
			}

			// Remove old taxonomies attributes so data is kept up to date - first get attribute key names.
			$delete_attribute_keys = $wpdb->get_col(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.QuotedDynamicPlaceholderGeneration
					"SELECT meta_key FROM {$wpdb->postmeta} WHERE meta_key LIKE %s AND meta_key NOT IN ( '" . implode( "','", array_map( 'esc_sql', $updated_attribute_keys ) ) . "' ) AND post_id = %d",
					$wpdb->esc_like( 'attribute_' ) . '%',
					$product_id
				)
			);

			foreach ( $delete_attribute_keys as $key ) {
				delete_post_meta( $product_id, $key );
			}
		}
	}

	/**
	 * Helper method that updates all the post meta for a product based on it's settings in the WC_Product class.
	 *
	 * @since 3.0.0
	 * @param WC_Product $product Product object.
	 * @param bool       $force Force update. Used during create.
	 */
	public function update_post_meta( &$product, $force = false ) {
		$meta_key_to_props = array(
			'_variation_description' => 'description',
		);

		$props_to_update = $force ? $meta_key_to_props : $this->get_props_to_update( $product, $meta_key_to_props );

		foreach ( $props_to_update as $meta_key => $prop ) {
			$value   = $product->{"get_$prop"}( 'edit' );
			$updated = update_post_meta( $product->get_id(), $meta_key, $value );
			if ( $updated ) {
				$this->updated_props[] = $prop;
			}
		}

		parent::update_post_meta( $product, $force );
	}

	/**
	 * Update product variation guid.
	 *
	 * @param WC_Product_Variation $product Product variation object.
	 *
	 * @since 3.6.0
	 */
	protected function update_guid( $product ) {
		global $wpdb;

		$guid = home_url(
			add_query_arg(
				array(
					'post_type' => 'product_variation',
					'p'         => $product->get_id(),
				),
				''
			)
		);
		$wpdb->update( $wpdb->posts, array( 'guid' => $guid ), array( 'ID' => $product->get_id() ) );
	}
}
