<?php
/**
 * WooCommerce Terms
 *
 * Functions for handling terms/term meta.
 *
 * @package WooCommerce\Functions
 * @version 2.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Change get terms defaults for attributes to order by the sorting setting, or default to menu_order for sortable taxonomies.
 *
 * @since 3.6.0 Sorting options are now set as the default automatically, so you no longer have to request to orderby menu_order.
 *
 * @param array $defaults   An array of default get_terms() arguments.
 * @param array $taxonomies An array of taxonomies.
 * @return array
 */
function wc_change_get_terms_defaults( $defaults, $taxonomies ) {
	if ( is_array( $taxonomies ) && 1 < count( $taxonomies ) ) {
		return $defaults;
	}
	$taxonomy = is_array( $taxonomies ) ? (string) current( $taxonomies ) : $taxonomies;
	$orderby  = 'name';

	if ( taxonomy_is_product_attribute( $taxonomy ) ) {
		$orderby = wc_attribute_orderby( $taxonomy );
	} elseif ( in_array( $taxonomy, apply_filters( 'woocommerce_sortable_taxonomies', array( 'product_cat' ) ), true ) ) {
		$orderby = 'menu_order';
	}

	// Change defaults. Invalid values will be changed later @see wc_change_pre_get_terms.
	// These are in place so we know if a specific order was requested.
	switch ( $orderby ) {
		case 'menu_order':
		case 'name_num':
		case 'parent':
			$defaults['orderby'] = $orderby;
			break;
	}

	return $defaults;
}
add_filter( 'get_terms_defaults', 'wc_change_get_terms_defaults', 10, 2 );

/**
 * Adds support to get_terms for menu_order argument.
 *
 * @since 3.6.0
 * @param WP_Term_Query $terms_query Instance of WP_Term_Query.
 */
function wc_change_pre_get_terms( $terms_query ) {
	$args = &$terms_query->query_vars;

	// Put back valid orderby values.
	if ( 'menu_order' === $args['orderby'] ) {
		$args['orderby']               = 'name';
		$args['force_menu_order_sort'] = true;
	}

	if ( 'name_num' === $args['orderby'] ) {
		$args['orderby']            = 'name';
		$args['force_numeric_name'] = true;
	}

	// When COUNTING, disable custom sorting.
	if ( 'count' === $args['fields'] ) {
		return;
	}

	// Support menu_order arg used in previous versions.
	if ( ! empty( $args['menu_order'] ) ) {
		$args['order']                 = 'DESC' === strtoupper( $args['menu_order'] ) ? 'DESC' : 'ASC';
		$args['force_menu_order_sort'] = true;
	}

	if ( ! empty( $args['force_menu_order_sort'] ) ) {
		$args['orderby']  = 'meta_value_num';
		$args['meta_key'] = 'order'; // phpcs:ignore
		$terms_query->meta_query->parse_query_vars( $args );
	}
}
add_action( 'pre_get_terms', 'wc_change_pre_get_terms', 10, 1 );

/**
 * Adjust term query to handle custom sorting parameters.
 *
 * @param array $clauses    Clauses.
 * @param array $taxonomies Taxonomies.
 * @param array $args       Arguments.
 * @return array
 */
function wc_terms_clauses( $clauses, $taxonomies, $args ) {
	global $wpdb;

	// No need to filter when counting.
	if ( strpos( $clauses['fields'], 'COUNT(*)' ) !== false ) {
		return $clauses;
	}

	// Force numeric sort if using name_num custom sorting param.
	if ( ! empty( $args['force_numeric_name'] ) ) {
		$clauses['orderby'] = str_replace( 'ORDER BY t.name', 'ORDER BY t.name+0', $clauses['orderby'] );
	}

	// For sorting, force left join in case order meta is missing.
	if ( ! empty( $args['force_menu_order_sort'] ) ) {
		$clauses['join']    = str_replace( "INNER JOIN {$wpdb->termmeta} ON ( t.term_id = {$wpdb->termmeta}.term_id )", "LEFT JOIN {$wpdb->termmeta} ON ( t.term_id = {$wpdb->termmeta}.term_id AND {$wpdb->termmeta}.meta_key='order')", $clauses['join'] );
		$clauses['where']   = str_replace( "{$wpdb->termmeta}.meta_key = 'order'", "( {$wpdb->termmeta}.meta_key = 'order' OR {$wpdb->termmeta}.meta_key IS NULL )", $clauses['where'] );
		$clauses['orderby'] = 'DESC' === $args['order'] ? str_replace( 'meta_value+0', 'meta_value+0 DESC, t.name', $clauses['orderby'] ) : str_replace( 'meta_value+0', 'meta_value+0 ASC, t.name', $clauses['orderby'] );
	}

	return $clauses;
}
add_filter( 'terms_clauses', 'wc_terms_clauses', 99, 3 );

/**
 * Helper to get cached object terms and filter by field using wp_list_pluck().
 * Works as a cached alternative for wp_get_post_terms() and wp_get_object_terms().
 *
 * @since  3.0.0
 * @param  int    $object_id Object ID.
 * @param  string $taxonomy  Taxonomy slug.
 * @param  string $field     Field name.
 * @param  string $index_key Index key name.
 * @return array
 */
function wc_get_object_terms( $object_id, $taxonomy, $field = null, $index_key = null ) {
	// Test if terms exists. get_the_terms() return false when it finds no terms.
	$terms = get_the_terms( $object_id, $taxonomy );

	if ( ! $terms || is_wp_error( $terms ) ) {
		return array();
	}

	return is_null( $field ) ? $terms : wp_list_pluck( $terms, $field, $index_key );
}

/**
 * Cached version of wp_get_post_terms().
 * This is a private function (internal use ONLY).
 *
 * @since  3.0.0
 * @param  int    $product_id Product ID.
 * @param  string $taxonomy   Taxonomy slug.
 * @param  array  $args       Query arguments.
 * @return array
 */
function _wc_get_cached_product_terms( $product_id, $taxonomy, $args = array() ) {
	$cache_key   = 'wc_' . $taxonomy . md5( wp_json_encode( $args ) );
	$cache_group = WC_Cache_Helper::get_cache_prefix( 'product_' . $product_id ) . $product_id;
	$terms       = wp_cache_get( $cache_key, $cache_group );

	if ( false !== $terms ) {
		return $terms;
	}

	$terms = wp_get_post_terms( $product_id, $taxonomy, $args );

	wp_cache_add( $cache_key, $terms, $cache_group );

	return $terms;
}

/**
 * Wrapper used to get terms for a product.
 *
 * @param  int    $product_id Product ID.
 * @param  string $taxonomy   Taxonomy slug.
 * @param  array  $args       Query arguments.
 * @return array
 */
function wc_get_product_terms( $product_id, $taxonomy, $args = array() ) {
	if ( ! taxonomy_exists( $taxonomy ) ) {
		return array();
	}

	return apply_filters( 'woocommerce_get_product_terms', _wc_get_cached_product_terms( $product_id, $taxonomy, $args ), $product_id, $taxonomy, $args );
}

/**
 * Sort by name (numeric).
 *
 * @param  WP_Post $a First item to compare.
 * @param  WP_Post $b Second item to compare.
 * @return int
 */
function _wc_get_product_terms_name_num_usort_callback( $a, $b ) {
	$a_name = (float) $a->name;
	$b_name = (float) $b->name;

	if ( abs( $a_name - $b_name ) < 0.001 ) {
		return 0;
	}

	return ( $a_name < $b_name ) ? -1 : 1;
}

/**
 * Sort by parent.
 *
 * @param  WP_Post $a First item to compare.
 * @param  WP_Post $b Second item to compare.
 * @return int
 */
function _wc_get_product_terms_parent_usort_callback( $a, $b ) {
	if ( $a->parent === $b->parent ) {
		return 0;
	}
	return ( $a->parent < $b->parent ) ? 1 : -1;
}

/**
 * WooCommerce Dropdown categories.
 *
 * @param array $args Args to control display of dropdown.
 */
function wc_product_dropdown_categories( $args = array() ) {
	global $wp_query;

	$args = wp_parse_args(
		$args,
		array(
			'pad_counts'         => 1,
			'show_count'         => 1,
			'hierarchical'       => 1,
			'hide_empty'         => 1,
			'show_uncategorized' => 1,
			'orderby'            => 'name',
			'selected'           => isset( $wp_query->query_vars['product_cat'] ) ? $wp_query->query_vars['product_cat'] : '',
			'show_option_none'   => __( 'Select a category', 'woocommerce' ),
			'option_none_value'  => '',
			'value_field'        => 'slug',
			'taxonomy'           => 'product_cat',
			'name'               => 'product_cat',
			'class'              => 'dropdown_product_cat',
		)
	);

	if ( 'order' === $args['orderby'] ) {
		$args['orderby']  = 'meta_value_num';
		$args['meta_key'] = 'order'; // phpcs:ignore
	}

	wp_dropdown_categories( $args );
}

/**
 * Custom walker for Product Categories.
 *
 * Previously used by wc_product_dropdown_categories, but wp_dropdown_categories has been fixed in core.
 *
 * @param mixed ...$args Variable number of parameters to be passed to the walker.
 * @return mixed
 */
function wc_walk_category_dropdown_tree( ...$args ) {
	if ( ! class_exists( 'WC_Product_Cat_Dropdown_Walker', false ) ) {
		include_once WC()->plugin_path() . '/includes/walkers/class-wc-product-cat-dropdown-walker.php';
	}

	// The user's options are the third parameter.
	if ( empty( $args[2]['walker'] ) || ! is_a( $args[2]['walker'], 'Walker' ) ) {
		$walker = new WC_Product_Cat_Dropdown_Walker();
	} else {
		$walker = $args[2]['walker'];
	}

	return $walker->walk( ...$args );
}

/**
 * Migrate data from WC term meta to WP term meta.
 *
 * When the database is updated to support term meta, migrate WC term meta data across.
 * We do this when the new version is >= 34370, and the old version is < 34370 (34370 is when term meta table was added).
 *
 * @param string $wp_db_version The new $wp_db_version.
 * @param string $wp_current_db_version The old (current) $wp_db_version.
 */
function wc_taxonomy_metadata_migrate_data( $wp_db_version, $wp_current_db_version ) {
	if ( $wp_db_version >= 34370 && $wp_current_db_version < 34370 ) {
		global $wpdb;
		if ( $wpdb->query( "INSERT INTO {$wpdb->termmeta} ( term_id, meta_key, meta_value ) SELECT woocommerce_term_id, meta_key, meta_value FROM {$wpdb->prefix}woocommerce_termmeta;" ) ) {
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}woocommerce_termmeta" );
		}
	}
}
add_action( 'wp_upgrade', 'wc_taxonomy_metadata_migrate_data', 10, 2 );

/**
 * Move a term before the a given element of its hierarchy level.
 *
 * @param int    $the_term Term ID.
 * @param int    $next_id  The id of the next sibling element in save hierarchy level.
 * @param string $taxonomy Taxnomy.
 * @param int    $index    Term index (default: 0).
 * @param mixed  $terms    List of terms. (default: null).
 * @return int
 */
function wc_reorder_terms( $the_term, $next_id, $taxonomy, $index = 0, $terms = null ) {
	if ( ! $terms ) {
		$terms = get_terms( $taxonomy, 'hide_empty=0&parent=0&menu_order=ASC' );
	}
	if ( empty( $terms ) ) {
		return $index;
	}

	$id = intval( $the_term->term_id );

	$term_in_level = false; // Flag: is our term to order in this level of terms.

	foreach ( $terms as $term ) {
		$term_id = intval( $term->term_id );

		if ( $term_id === $id ) { // Our term to order, we skip.
			$term_in_level = true;
			continue; // Our term to order, we skip.
		}
		// the nextid of our term to order, lets move our term here.
		if ( null !== $next_id && $term_id === $next_id ) {
			$index++;
			$index = wc_set_term_order( $id, $index, $taxonomy, true );
		}

		// Set order.
		$index++;
		$index = wc_set_term_order( $term_id, $index, $taxonomy );

		/**
		 * After a term has had it's order set.
		*/
		do_action( 'woocommerce_after_set_term_order', $term, $index, $taxonomy );

		// If that term has children we walk through them.
		$children = get_terms( $taxonomy, "parent={$term_id}&hide_empty=0&menu_order=ASC" );
		if ( ! empty( $children ) ) {
			$index = wc_reorder_terms( $the_term, $next_id, $taxonomy, $index, $children );
		}
	}

	// No nextid meaning our term is in last position.
	if ( $term_in_level && null === $next_id ) {
		$index = wc_set_term_order( $id, $index + 1, $taxonomy, true );
	}

	return $index;
}

/**
 * Set the sort order of a term.
 *
 * @param int    $term_id   Term ID.
 * @param int    $index     Index.
 * @param string $taxonomy  Taxonomy.
 * @param bool   $recursive Recursive (default: false).
 * @return int
 */
function wc_set_term_order( $term_id, $index, $taxonomy, $recursive = false ) {

	$term_id = (int) $term_id;
	$index   = (int) $index;

	update_term_meta( $term_id, 'order', $index );

	if ( ! $recursive ) {
		return $index;
	}

	$children = get_terms( $taxonomy, "parent=$term_id&hide_empty=0&menu_order=ASC" );

	foreach ( $children as $term ) {
		$index++;
		$index = wc_set_term_order( $term->term_id, $index, $taxonomy, true );
	}

	clean_term_cache( $term_id, $taxonomy );

	return $index;
}

/**
 * Function for recounting product terms, ignoring hidden products.
 *
 * @param array  $terms                       List of terms.
 * @param object $taxonomy                    Taxonomy.
 * @param bool   $callback                    Callback.
 * @param bool   $terms_are_term_taxonomy_ids If terms are from term_taxonomy_id column.
 */
function _wc_term_recount( $terms, $taxonomy, $callback = true, $terms_are_term_taxonomy_ids = true ) {
	global $wpdb;

	/**
	 * Filter to allow/prevent recounting of terms as it could be expensive.
	 * A likely scenario for this is when bulk importing products. We could
	 * then prevent it from recounting per product but instead recount it once
	 * when import is done. Of course this means the import logic has to support this.
	 *
	 * @since 5.2
	 * @param bool
	 */
	if ( ! apply_filters( 'woocommerce_product_recount_terms', true ) ) {
		return;
	}

	// Standard callback.
	if ( $callback ) {
		_update_post_term_count( $terms, $taxonomy );
	}

	$exclude_term_ids            = array();
	$product_visibility_term_ids = wc_get_product_visibility_term_ids();

	if ( $product_visibility_term_ids['exclude-from-catalog'] ) {
		$exclude_term_ids[] = $product_visibility_term_ids['exclude-from-catalog'];
	}

	if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && $product_visibility_term_ids['outofstock'] ) {
		$exclude_term_ids[] = $product_visibility_term_ids['outofstock'];
	}

	$query = array(
		'fields' => "
			SELECT COUNT( DISTINCT ID ) FROM {$wpdb->posts} p
		",
		'join'   => '',
		'where'  => "
			WHERE 1=1
			AND p.post_status = 'publish'
			AND p.post_type = 'product'

		",
	);

	if ( count( $exclude_term_ids ) ) {
		$query['join']  .= " LEFT JOIN ( SELECT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN ( " . implode( ',', array_map( 'absint', $exclude_term_ids ) ) . ' ) ) AS exclude_join ON exclude_join.object_id = p.ID';
		$query['where'] .= ' AND exclude_join.object_id IS NULL';
	}

	// Pre-process term taxonomy ids.
	if ( ! $terms_are_term_taxonomy_ids ) {
		// We passed in an array of TERMS in format id=>parent.
		$terms = array_filter( (array) array_keys( $terms ) );
	} else {
		// If we have term taxonomy IDs we need to get the term ID.
		$term_taxonomy_ids = $terms;
		$terms             = array();
		foreach ( $term_taxonomy_ids as $term_taxonomy_id ) {
			$term    = get_term_by( 'term_taxonomy_id', $term_taxonomy_id, $taxonomy->name );
			$terms[] = $term->term_id;
		}
	}

	// Exit if we have no terms to count.
	if ( empty( $terms ) ) {
		return;
	}

	// Ancestors need counting.
	if ( is_taxonomy_hierarchical( $taxonomy->name ) ) {
		foreach ( $terms as $term_id ) {
			$terms = array_merge( $terms, get_ancestors( $term_id, $taxonomy->name ) );
		}
	}

	// Unique terms only.
	$terms = array_unique( $terms );

	// Count the terms.
	foreach ( $terms as $term_id ) {
		$terms_to_count = array( absint( $term_id ) );

		if ( is_taxonomy_hierarchical( $taxonomy->name ) ) {
			// We need to get the $term's hierarchy so we can count its children too.
			$children = get_term_children( $term_id, $taxonomy->name );

			if ( $children && ! is_wp_error( $children ) ) {
				$terms_to_count = array_unique( array_map( 'absint', array_merge( $terms_to_count, $children ) ) );
			}
		}

		// Generate term query.
		$term_query          = $query;
		$term_query['join'] .= " INNER JOIN ( SELECT object_id FROM {$wpdb->term_relationships} INNER JOIN {$wpdb->term_taxonomy} using( term_taxonomy_id ) WHERE term_id IN ( " . implode( ',', array_map( 'absint', $terms_to_count ) ) . ' ) ) AS include_join ON include_join.object_id = p.ID';

		// Get the count.
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$count = $wpdb->get_var( implode( ' ', $term_query ) );

		// Update the count.
		update_term_meta( $term_id, 'product_count_' . $taxonomy->name, absint( $count ) );
	}

	delete_transient( 'wc_term_counts' );
}

/**
 * Recount terms after the stock amount changes.
 *
 * @param int $product_id Product ID.
 */
function wc_recount_after_stock_change( $product_id ) {
	if ( 'yes' !== get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
		return;
	}

	_wc_recount_terms_by_product( $product_id );
}
add_action( 'woocommerce_product_set_stock_status', 'wc_recount_after_stock_change' );


/**
 * Overrides the original term count for product categories and tags with the product count.
 * that takes catalog visibility into account.
 *
 * @param array        $terms      List of terms.
 * @param string|array $taxonomies Single taxonomy or list of taxonomies.
 * @return array
 */
function wc_change_term_counts( $terms, $taxonomies ) {
	if ( is_admin() || wp_doing_ajax() ) {
		return $terms;
	}

	if ( ! isset( $taxonomies[0] ) || ! in_array( $taxonomies[0], apply_filters( 'woocommerce_change_term_counts', array( 'product_cat', 'product_tag' ) ), true ) ) {
		return $terms;
	}

	$o_term_counts = get_transient( 'wc_term_counts' );
	$term_counts   = false === $o_term_counts ? array() : $o_term_counts;

	foreach ( $terms as &$term ) {
		if ( is_object( $term ) ) {
			$term_counts[ $term->term_id ] =
				isset( $term_counts[ $term->term_id ] ) ?
					$term_counts[ $term->term_id ] :
					get_term_meta( $term->term_id, 'product_count_' . $taxonomies[0], true );

			if ( '' !== $term_counts[ $term->term_id ] ) {
				$term->count = absint( $term_counts[ $term->term_id ] );
			}
		}
	}

	// Update transient.
	if ( $term_counts !== $o_term_counts ) {
		set_transient( 'wc_term_counts', $term_counts, DAY_IN_SECONDS * 30 );
	}

	return $terms;
}
add_filter( 'get_terms', 'wc_change_term_counts', 10, 2 );

/**
 * Return products in a given term, and cache value.
 *
 * To keep in sync, product_count will be cleared on "set_object_terms".
 *
 * @param int    $term_id  Term ID.
 * @param string $taxonomy Taxonomy.
 * @return array
 */
function wc_get_term_product_ids( $term_id, $taxonomy ) {
	$product_ids = get_term_meta( $term_id, 'product_ids', true );

	if ( false === $product_ids || ! is_array( $product_ids ) ) {
		$product_ids = get_objects_in_term( $term_id, $taxonomy );
		update_term_meta( $term_id, 'product_ids', $product_ids );
	}

	return $product_ids;
}

/**
 * When a post is updated and terms recounted (called by _update_post_term_count), clear the ids.
 *
 * @param int    $object_id  Object ID.
 * @param array  $terms      An array of object terms.
 * @param array  $tt_ids     An array of term taxonomy IDs.
 * @param string $taxonomy   Taxonomy slug.
 * @param bool   $append     Whether to append new terms to the old terms.
 * @param array  $old_tt_ids Old array of term taxonomy IDs.
 */
function wc_clear_term_product_ids( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {
	foreach ( $old_tt_ids as $term_id ) {
		delete_term_meta( $term_id, 'product_ids' );
	}
	foreach ( $tt_ids as $term_id ) {
		delete_term_meta( $term_id, 'product_ids' );
	}
}
add_action( 'set_object_terms', 'wc_clear_term_product_ids', 10, 6 );

/**
 * Get full list of product visibilty term ids.
 *
 * @since  3.0.0
 * @return int[]
 */
function wc_get_product_visibility_term_ids() {
	if ( ! taxonomy_exists( 'product_visibility' ) ) {
		wc_doing_it_wrong( __FUNCTION__, 'wc_get_product_visibility_term_ids should not be called before taxonomies are registered (woocommerce_after_register_post_type action).', '3.1' );
		return array();
	}
	return array_map(
		'absint',
		wp_parse_args(
			wp_list_pluck(
				get_terms(
					array(
						'taxonomy'   => 'product_visibility',
						'hide_empty' => false,
					)
				),
				'term_taxonomy_id',
				'name'
			),
			array(
				'exclude-from-catalog' => 0,
				'exclude-from-search'  => 0,
				'featured'             => 0,
				'outofstock'           => 0,
				'rated-1'              => 0,
				'rated-2'              => 0,
				'rated-3'              => 0,
				'rated-4'              => 0,
				'rated-5'              => 0,
			)
		)
	);
}

/**
 * Recounts all terms.
 *
 * @since 5.2
 * @return void
 */
function wc_recount_all_terms() {
	$product_cats = get_terms(
		'product_cat',
		array(
			'hide_empty' => false,
			'fields'     => 'id=>parent',
		)
	);
	_wc_term_recount( $product_cats, get_taxonomy( 'product_cat' ), true, false );
	$product_tags = get_terms(
		'product_tag',
		array(
			'hide_empty' => false,
			'fields'     => 'id=>parent',
		)
	);
	_wc_term_recount( $product_tags, get_taxonomy( 'product_tag' ), true, false );
}

/**
 * Recounts terms by product.
 *
 * @since 5.2
 * @param int $product_id The ID of the product.
 * @return void
 */
function _wc_recount_terms_by_product( $product_id = '' ) {
	if ( empty( $product_id ) ) {
		return;
	}

	$product_terms = get_the_terms( $product_id, 'product_cat' );

	if ( $product_terms ) {
		$product_cats = array();

		foreach ( $product_terms as $term ) {
			$product_cats[ $term->term_id ] = $term->parent;
		}

		_wc_term_recount( $product_cats, get_taxonomy( 'product_cat' ), false, false );
	}

	$product_terms = get_the_terms( $product_id, 'product_tag' );

	if ( $product_terms ) {
		$product_tags = array();

		foreach ( $product_terms as $term ) {
			$product_tags[ $term->term_id ] = $term->parent;
		}

		_wc_term_recount( $product_tags, get_taxonomy( 'product_tag' ), false, false );
	}
}
