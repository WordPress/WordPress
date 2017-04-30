<?php
/**
 * WooCommerce Terms
 *
 * Functions for handling terms/term meta.
 *
 * @author 		WooThemes
 * @category 	Core
 * @package 	WooCommerce/Functions
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Wrapper for wp_get_post_terms which supports ordering by parent.
 *
 * NOTE: At this point in time, ordering by menu_order for example isn't possible with this function. wp_get_post_terms has no
 *   filters which we can utilise to modify it's query. https://core.trac.wordpress.org/ticket/19094
 * 
 * @param  int $product_id
 * @param  string $taxonomy
 * @param  array  $args
 * @return array
 */
function wc_get_product_terms( $product_id, $taxonomy, $args = array() ) {
	if ( ! taxonomy_exists( $taxonomy ) ) {
		return array();
	}

	if ( empty( $args['orderby'] ) && taxonomy_is_product_attribute( $taxonomy ) ) {
		$args['orderby'] = wc_attribute_orderby( $taxonomy );
	}

	// Support ordering by parent
	if ( ! empty( $args['orderby'] ) && $args['orderby'] === 'parent' ) {
		$fields = isset( $args['fields'] ) ? $args['fields'] : 'all';

		// Unset for wp_get_post_terms
		unset( $args['orderby'] );
		unset( $args['fields'] );

		$terms = wp_get_post_terms( $product_id, $taxonomy, $args );

		usort( $terms, '_wc_get_product_terms_parent_usort_callback' );

		switch ( $fields ) {
			case 'names' :
				$terms = wp_list_pluck( $terms, 'name' );
				break;
			case 'ids' :
				$terms = wp_list_pluck( $terms, 'term_id' );
				break;
			case 'slugs' :
				$terms = wp_list_pluck( $terms, 'slug' );
				break;
		}
	} elseif ( ! empty( $args['orderby'] ) && $args['orderby'] === 'menu_order' ) {
		// wp_get_post_terms doesn't let us use custom sort order
		$args['include'] = wp_get_post_terms( $product_id, $taxonomy, array( 'fields' => 'ids' ) );
		
		if ( empty( $args['include'] ) ) {
			$terms = array();
		} else {
			// This isn't needed for get_terms
			unset( $args['orderby'] );

			// Set args for get_terms
			$args['menu_order'] = isset( $args['order'] ) ? $args['order'] : 'ASC';
			$args['hide_empty'] = isset( $args['hide_empty'] ) ? $args['hide_empty'] : 0;
			$args['fields']     = isset( $args['fields'] ) ? $args['fields'] : 'names';

			// Ensure slugs is valid for get_terms - slugs isn't supported
			$args['fields']     = $args['fields'] === 'slugs' ? 'id=>slug' : $args['fields'];
			$terms              = get_terms( $taxonomy, $args );
		}
	} else {
		$terms = wp_get_post_terms( $product_id, $taxonomy, $args );
	}

	return $terms;
}

/**
 * Sort by parent
 * @param  WP_POST object $a
 * @param  WP_POST object $b
 * @return int
 */
function _wc_get_product_terms_parent_usort_callback( $a, $b ) {
	if ( $a->parent === $b->parent ) {
		return 0;
	}
	return ( $a->parent < $b->parent ) ? 1 : -1;
}

/**
 * WooCommerce Dropdown categories
 *
 * Stuck with this until a fix for http://core.trac.wordpress.org/ticket/13258
 * We use a custom walker, just like WordPress does
 *
 * @param int $show_counts (default: 1)
 * @param int $hierarchical (default: 1)
 * @param int $show_uncategorized (default: 1)
 * @return string
 */
function wc_product_dropdown_categories( $args = array(), $deprecated_hierarchical = 1, $deprecated_show_uncategorized = 1, $deprecated_orderby = '' ) {
	global $wp_query, $woocommerce;

	if ( ! is_array( $args ) ) {
		_deprecated_argument( 'wc_product_dropdown_categories()', '2.1', 'show_counts, hierarchical, show_uncategorized and orderby arguments are invalid - pass a single array of values instead.' );

		$args['show_counts']        = $args;
		$args['hierarchical']       = $deprecated_hierarchical;
		$args['show_uncategorized'] = $deprecated_show_uncategorized;
		$args['orderby']            = $deprecated_orderby;
	}

	$defaults = array(
		'pad_counts'         => 1,
		'show_counts'        => 1,
		'hierarchical'       => 1,
		'hide_empty'         => 1,
		'show_uncategorized' => 1,
		'orderby'            => 'name',
		'selected'           => isset( $wp_query->query['product_cat'] ) ? $wp_query->query['product_cat'] : '',
		'menu_order'         => false
	);

	$args = wp_parse_args( $args, $defaults );

	if ( $args['orderby'] == 'order' ) {
		$args['menu_order'] = 'asc';
		$args['orderby']    = 'name';
	}

	$terms = get_terms( 'product_cat', $args );

	if ( ! $terms )
		return;

	$output  = "<select name='product_cat' id='dropdown_product_cat'>";
	$output .= '<option value="" ' .  selected( isset( $_GET['product_cat'] ) ? $_GET['product_cat'] : '', '', false ) . '>' . __( 'Select a category', 'woocommerce' ) . '</option>';
	$output .= wc_walk_category_dropdown_tree( $terms, 0, $args );

	if ( $args['show_uncategorized'] )
		$output .= '<option value="0" ' . selected( isset( $_GET['product_cat'] ) ? $_GET['product_cat'] : '', '0', false ) . '>' . __( 'Uncategorized', 'woocommerce' ) . '</option>';

	$output .="</select>";

	echo $output;
}

/**
 * Walk the Product Categories.
 *
 * @return mixed
 */
function wc_walk_category_dropdown_tree() {
	global $woocommerce;

	if ( ! class_exists( 'WC_Product_Cat_Dropdown_Walker' ) )
		include_once( WC()->plugin_path() . '/includes/walkers/class-product-cat-dropdown-walker.php' );

	$args = func_get_args();

	// the user's options are the third parameter
	if ( empty( $args[2]['walker']) || !is_a($args[2]['walker'], 'Walker' ) )
		$walker = new WC_Product_Cat_Dropdown_Walker;
	else
		$walker = $args[2]['walker'];

	return call_user_func_array(array( &$walker, 'walk' ), $args );
}

/**
 * WooCommerce Term/Order item Meta API - set table name
 *
 * @return void
 */
function wc_taxonomy_metadata_wpdbfix() {
	global $wpdb;
	$termmeta_name = 'woocommerce_termmeta';
	$itemmeta_name = 'woocommerce_order_itemmeta';

	$wpdb->woocommerce_termmeta = $wpdb->prefix . $termmeta_name;
	$wpdb->order_itemmeta = $wpdb->prefix . $itemmeta_name;

	$wpdb->tables[] = 'woocommerce_termmeta';
	$wpdb->tables[] = 'woocommerce_order_itemmeta';
}
add_action( 'init', 'wc_taxonomy_metadata_wpdbfix', 0 );
add_action( 'switch_blog', 'wc_taxonomy_metadata_wpdbfix', 0 );

/**
 * WooCommerce Term Meta API - Update term meta
 *
 * @param mixed $term_id
 * @param mixed $meta_key
 * @param mixed $meta_value
 * @param string $prev_value (default: '')
 * @return bool
 */
function update_woocommerce_term_meta( $term_id, $meta_key, $meta_value, $prev_value = '' ) {
	return update_metadata( 'woocommerce_term', $term_id, $meta_key, $meta_value, $prev_value );
}

/**
 * WooCommerce Term Meta API - Add term meta
 *
 * @param mixed $term_id
 * @param mixed $meta_key
 * @param mixed $meta_value
 * @param bool $unique (default: false)
 * @return bool
 */
function add_woocommerce_term_meta( $term_id, $meta_key, $meta_value, $unique = false ){
	return add_metadata( 'woocommerce_term', $term_id, $meta_key, $meta_value, $unique );
}

/**
 * WooCommerce Term Meta API - Delete term meta
 *
 * @param mixed $term_id
 * @param mixed $meta_key
 * @param string $meta_value (default: '')
 * @param bool $delete_all (default: false)
 * @return bool
 */
function delete_woocommerce_term_meta( $term_id, $meta_key, $meta_value = '', $delete_all = false ) {
	return delete_metadata( 'woocommerce_term', $term_id, $meta_key, $meta_value, $delete_all );
}

/**
 * WooCommerce Term Meta API - Get term meta
 *
 * @param mixed $term_id
 * @param mixed $key
 * @param bool $single (default: true)
 * @return mixed
 */
function get_woocommerce_term_meta( $term_id, $key, $single = true ) {
	return get_metadata( 'woocommerce_term', $term_id, $key, $single );
}

/**
 * Move a term before the a	given element of its hierarchy level
 *
 * @param int $the_term
 * @param int $next_id the id of the next sibling element in save hierarchy level
 * @param string $taxonomy
 * @param int $index (default: 0)
 * @param mixed $terms (default: null)
 * @return int
 */
function wc_reorder_terms( $the_term, $next_id, $taxonomy, $index = 0, $terms = null ) {

	if( ! $terms ) $terms = get_terms($taxonomy, 'menu_order=ASC&hide_empty=0&parent=0' );
	if( empty( $terms ) ) return $index;

	$id	= $the_term->term_id;

	$term_in_level = false; // flag: is our term to order in this level of terms

	foreach ($terms as $term) {

		if( $term->term_id == $id ) { // our term to order, we skip
			$term_in_level = true;
			continue; // our term to order, we skip
		}
		// the nextid of our term to order, lets move our term here
		if(null !== $next_id && $term->term_id == $next_id) {
			$index++;
			$index = wc_set_term_order($id, $index, $taxonomy, true);
		}

		// set order
		$index++;
		$index = wc_set_term_order($term->term_id, $index, $taxonomy);

		// if that term has children we walk through them
		$children = get_terms($taxonomy, "parent={$term->term_id}&menu_order=ASC&hide_empty=0");
		if( !empty($children) ) {
			$index = wc_reorder_terms( $the_term, $next_id, $taxonomy, $index, $children );
		}
	}

	// no nextid meaning our term is in last position
	if( $term_in_level && null === $next_id )
		$index = wc_set_term_order($id, $index+1, $taxonomy, true);

	return $index;
}

/**
 * Set the sort order of a term
 *
 * @param int $term_id
 * @param int $index
 * @param string $taxonomy
 * @param bool $recursive (default: false)
 * @return int
 */
function wc_set_term_order( $term_id, $index, $taxonomy, $recursive = false ) {

	$term_id 	= (int) $term_id;
	$index 		= (int) $index;

	// Meta name
	if ( taxonomy_is_product_attribute( $taxonomy ) )
		$meta_name =  'order_' . esc_attr( $taxonomy );
	else
		$meta_name = 'order';

	update_woocommerce_term_meta( $term_id, $meta_name, $index );

	if( ! $recursive ) return $index;

	$children = get_terms($taxonomy, "parent=$term_id&menu_order=ASC&hide_empty=0");

	foreach ( $children as $term ) {
		$index ++;
		$index = wc_set_term_order($term->term_id, $index, $taxonomy, true);
	}

	clean_term_cache( $term_id, $taxonomy );

	return $index;
}

/**
 * Add term ordering to get_terms
 *
 * It enables the support a 'menu_order' parameter to get_terms for the product_cat taxonomy.
 * By default it is 'ASC'. It accepts 'DESC' too
 *
 * To disable it, set it ot false (or 0)
 *
 * @param array $clauses
 * @param array $taxonomies
 * @param array $args
 * @return array
 */
function wc_terms_clauses( $clauses, $taxonomies, $args ) {
	global $wpdb, $woocommerce;

	// No sorting when menu_order is false
	if ( isset( $args['menu_order'] ) && $args['menu_order'] == false ) {
		return $clauses;
	}

	// No sorting when orderby is non default
	if ( isset( $args['orderby'] ) && $args['orderby'] != 'name' ) {
		return $clauses;
	}

	// No sorting in admin when sorting by a column
	if ( is_admin() && isset( $_GET['orderby'] ) ) {
		return $clauses;
	}

	// wordpress should give us the taxonomies asked when calling the get_terms function. Only apply to categories and pa_ attributes
	$found = false;
	foreach ( (array) $taxonomies as $taxonomy ) {
		if ( taxonomy_is_product_attribute( $taxonomy ) || in_array( $taxonomy, apply_filters( 'woocommerce_sortable_taxonomies', array( 'product_cat' ) ) ) ) {
			$found = true;
			break;
		}
	}
	if ( ! $found ) {
		return $clauses;
	}

	// Meta name
	if ( ! empty( $taxonomies[0] ) && taxonomy_is_product_attribute( $taxonomies[0] ) ) {
		$meta_name =  'order_' . esc_attr( $taxonomies[0] );
	} else {
		$meta_name = 'order';
	}

	// query fields
	if ( strpos( 'COUNT(*)', $clauses['fields'] ) === false )  {
		$clauses['fields']  .= ', tm.* ';
	}

	//query join
	$clauses['join'] .= " LEFT JOIN {$wpdb->woocommerce_termmeta} AS tm ON (t.term_id = tm.woocommerce_term_id AND tm.meta_key = '". $meta_name ."') ";

	// default to ASC
	if ( ! isset( $args['menu_order'] ) || ! in_array( strtoupper($args['menu_order']), array('ASC', 'DESC')) ) {
		$args['menu_order'] = 'ASC';
	}

	$order = "ORDER BY tm.meta_value+0 " . $args['menu_order'];

	if ( $clauses['orderby'] ):
		$clauses['orderby'] = str_replace('ORDER BY', $order . ',', $clauses['orderby'] );
	else:
		$clauses['orderby'] = $order;
	endif;

	return $clauses;
}
add_filter( 'terms_clauses', 'wc_terms_clauses', 10, 3 );

/**
 * Function for recounting product terms, ignoring hidden products.
 * @param  array  $terms
 * @param  string  $taxonomy
 * @param  boolean $callback
 * @param  boolean $terms_are_term_taxonomy_ids
 * @return void
 */
function _wc_term_recount( $terms, $taxonomy, $callback = true, $terms_are_term_taxonomy_ids = true ) {
	global $wpdb;

	// Standard callback
	if ( $callback ) {
		_update_post_term_count( $terms, $taxonomy );
	}

	// Stock query
	if ( get_option( 'woocommerce_hide_out_of_stock_items' ) == 'yes' ) {
		$stock_join  = "LEFT JOIN {$wpdb->postmeta} AS meta_stock ON posts.ID = meta_stock.post_id";
		$stock_query = "
		AND meta_stock.meta_key = '_stock_status'
		AND meta_stock.meta_value = 'instock'
		";
	} else {
		$stock_query = $stock_join = '';
	}

	// Main query
	$count_query = "
		SELECT COUNT( DISTINCT posts.ID ) FROM {$wpdb->posts} as posts
		LEFT JOIN {$wpdb->postmeta} AS meta_visibility ON posts.ID = meta_visibility.post_id
		LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID=rel.object_ID
		LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
		LEFT JOIN {$wpdb->terms} AS term USING( term_id )
		LEFT JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
		$stock_join
		WHERE 	post_status = 'publish'
		AND 	post_type 	= 'product'
		AND 	meta_visibility.meta_key = '_visibility'
		AND 	meta_visibility.meta_value IN ( 'visible', 'catalog' )
		$stock_query
	";

	// Pre-process term taxonomy ids
	if ( ! $terms_are_term_taxonomy_ids ) {
		// We passed in an array of TERMS in format id=>parent
		$terms = array_filter( (array) array_keys( $terms ) );
	} else {
		// If we have term taxonomy IDs we need to get the term ID
		$term_taxonomy_ids = $terms;
		$terms             = array();
		foreach ( $term_taxonomy_ids as $term_taxonomy_id ) {
			$term    = get_term_by( 'term_taxonomy_id', $term_taxonomy_id, $taxonomy->name );
			$terms[] = $term->term_id;
		}
	}

	// Exit if we have no terms to count
	if ( ! $terms ) {
		return;
	}

	// Ancestors need counting
	if ( is_taxonomy_hierarchical( $taxonomy->name ) ) {
		foreach ( $terms as $term_id ) {
			$terms = array_merge( $terms, get_ancestors( $term_id, $taxonomy->name ) );
		}
	}

	// Unique terms only
	$terms = array_unique( $terms );

	// Count the terms
	foreach ( $terms as $term_id ) {
		$terms_to_count = array( absint( $term_id ) );

		if ( is_taxonomy_hierarchical( $taxonomy->name ) ) {
			// We need to get the $term's hierarchy so we can count its children too
			if ( ( $children = get_term_children( $term_id, $taxonomy->name ) ) && ! is_wp_error( $children ) ) {
				$terms_to_count = array_unique( array_map( 'absint', array_merge( $terms_to_count, $children ) ) );
			}
		}

		// Generate term query
		$term_query = 'AND term_id IN ( ' . implode( ',', $terms_to_count ) . ' )';

		// Get the count
		$count = $wpdb->get_var( $count_query . $term_query );

		// Update the count
		update_woocommerce_term_meta( $term_id, 'product_count_' . $taxonomy->name, absint( $count ) );
	}
}

/**
 * Recount terms after the stock amount changes
 * @param  int $product_id 
 * @return void
 */
function wc_recount_after_stock_change( $product_id ) {
	if ( get_option( 'woocommerce_hide_out_of_stock_items' ) != 'yes' ) 
		return;

	$product_terms = get_the_terms( $product_id, 'product_cat' );

	if ( $product_terms ) {
		foreach ( $product_terms as $term )
			$product_cats[ $term->term_id ] = $term->parent;

		_wc_term_recount( $product_cats, get_taxonomy( 'product_cat' ), false, false );
	}

	$product_terms = get_the_terms( $product_id, 'product_tag' );

	if ( $product_terms ) {
		foreach ( $product_terms as $term )
			$product_tags[ $term->term_id ] = $term->parent;

		_wc_term_recount( $product_tags, get_taxonomy( 'product_tag' ), false, false );
	}

	delete_transient( 'wc_term_counts' );
}
add_action( 'woocommerce_product_set_stock_status', 'wc_recount_after_stock_change' );


/**
 * Overrides the original term count for product categories and tags with the product count
 * that takes catalog visibility into account.
 *
 * @param array $terms
 * @param mixed $taxonomies
 * @param mixed $args
 * @return array
 */
function wc_change_term_counts( $terms, $taxonomies, $args ) {
	if ( is_admin() || is_ajax() ) {
		return $terms;
	}

	if ( ! isset( $taxonomies[0] ) || ! in_array( $taxonomies[0], apply_filters( 'woocommerce_change_term_counts', array( 'product_cat', 'product_tag' ) ) ) ) {
		return $terms;
	}

	$term_counts = $o_term_counts = get_transient( 'wc_term_counts' );

	foreach ( $terms as &$term ) {
		if ( is_object( $term ) ) {
			$term_counts[ $term->term_id ] = isset( $term_counts[ $term->term_id ] ) ? $term_counts[ $term->term_id ] : get_woocommerce_term_meta( $term->term_id, 'product_count_' . $taxonomies[0] , true );

			if ( $term_counts[ $term->term_id ] !== '' ) {
				$term->count = absint( $term_counts[ $term->term_id ] );
			}
		}
	}

	// Update transient
	if ( $term_counts != $o_term_counts ) {
		set_transient( 'wc_term_counts', $term_counts, YEAR_IN_SECONDS );
	}

	return $terms;
}
add_filter( 'get_terms', 'wc_change_term_counts', 10, 3 );
