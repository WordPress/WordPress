<?php
/**
 * WordPress Taxonomy Administration API.
 *
 * @package WordPress
 * @subpackage Administration
 */

//
// Category
//

/**
 * Check whether a category exists.
 *
 * @since 2.0.0
 *
 * @see term_exists()
 *
 * @param int|string $cat_name Category name.
 * @param int        $parent   Optional. ID of parent term.
 * @return mixed
 */
function category_exists( $cat_name, $parent = null ) {
	$id = term_exists( $cat_name, 'category', $parent );
	if ( is_array( $id ) ) {
		$id = $id['term_id'];
	}
	return $id;
}

/**
 * Get category object for given ID and 'edit' filter context.
 *
 * @since 2.0.0
 *
 * @param int $id
 * @return object
 */
function get_category_to_edit( $id ) {
	$category = get_term( $id, 'category', OBJECT, 'edit' );
	_make_cat_compat( $category );
	return $category;
}

/**
 * Add a new category to the database if it does not already exist.
 *
 * @since 2.0.0
 *
 * @param int|string $cat_name
 * @param int        $parent
 * @return int|WP_Error
 */
function wp_create_category( $cat_name, $parent = 0 ) {
	if ( $id = category_exists( $cat_name, $parent ) ) {
		return $id;
	}

	return wp_insert_category(
		array(
			'cat_name'        => $cat_name,
			'category_parent' => $parent,
		)
	);
}

/**
 * Create categories for the given post.
 *
 * @since 2.0.0
 *
 * @param string[] $categories Array of category names to create.
 * @param int      $post_id    Optional. The post ID. Default empty.
 * @return array List of categories to create for the given post.
 */
function wp_create_categories( $categories, $post_id = '' ) {
	$cat_ids = array();
	foreach ( $categories as $category ) {
		if ( $id = category_exists( $category ) ) {
			$cat_ids[] = $id;
		} elseif ( $id = wp_create_category( $category ) ) {
			$cat_ids[] = $id;
		}
	}

	if ( $post_id ) {
		wp_set_post_categories( $post_id, $cat_ids );
	}

	return $cat_ids;
}

/**
 * Updates an existing Category or creates a new Category.
 *
 * @since 2.0.0
 * @since 2.5.0 $wp_error parameter was added.
 * @since 3.0.0 The 'taxonomy' argument was added.
 *
 * @param array $catarr {
 *     Array of arguments for inserting a new category.
 *
 *     @type int        $cat_ID               Category ID. A non-zero value updates an existing category.
 *                                            Default 0.
 *     @type string     $taxonomy             Taxonomy slug. Default 'category'.
 *     @type string     $cat_name             Category name. Default empty.
 *     @type string     $category_description Category description. Default empty.
 *     @type string     $category_nicename    Category nice (display) name. Default empty.
 *     @type int|string $category_parent      Category parent ID. Default empty.
 * }
 * @param bool  $wp_error Optional. Default false.
 * @return int|object The ID number of the new or updated Category on success. Zero or a WP_Error on failure,
 *                    depending on param $wp_error.
 */
function wp_insert_category( $catarr, $wp_error = false ) {
	$cat_defaults = array(
		'cat_ID'               => 0,
		'taxonomy'             => 'category',
		'cat_name'             => '',
		'category_description' => '',
		'category_nicename'    => '',
		'category_parent'      => '',
	);
	$catarr       = wp_parse_args( $catarr, $cat_defaults );

	if ( trim( $catarr['cat_name'] ) == '' ) {
		if ( ! $wp_error ) {
			return 0;
		} else {
			return new WP_Error( 'cat_name', __( 'You did not enter a category name.' ) );
		}
	}

	$catarr['cat_ID'] = (int) $catarr['cat_ID'];

	// Are we updating or creating?
	$update = ! empty( $catarr['cat_ID'] );

	$name        = $catarr['cat_name'];
	$description = $catarr['category_description'];
	$slug        = $catarr['category_nicename'];
	$parent      = (int) $catarr['category_parent'];
	if ( $parent < 0 ) {
		$parent = 0;
	}

	if ( empty( $parent )
		|| ! term_exists( $parent, $catarr['taxonomy'] )
		|| ( $catarr['cat_ID'] && term_is_ancestor_of( $catarr['cat_ID'], $parent, $catarr['taxonomy'] ) ) ) {
		$parent = 0;
	}

	$args = compact( 'name', 'slug', 'parent', 'description' );

	if ( $update ) {
		$catarr['cat_ID'] = wp_update_term( $catarr['cat_ID'], $catarr['taxonomy'], $args );
	} else {
		$catarr['cat_ID'] = wp_insert_term( $catarr['cat_name'], $catarr['taxonomy'], $args );
	}

	if ( is_wp_error( $catarr['cat_ID'] ) ) {
		if ( $wp_error ) {
			return $catarr['cat_ID'];
		} else {
			return 0;
		}
	}
	return $catarr['cat_ID']['term_id'];
}

/**
 * Aliases wp_insert_category() with minimal args.
 *
 * If you want to update only some fields of an existing category, call this
 * function with only the new values set inside $catarr.
 *
 * @since 2.0.0
 *
 * @param array $catarr The 'cat_ID' value is required. All other keys are optional.
 * @return int|bool The ID number of the new or updated Category on success. Zero or FALSE on failure.
 */
function wp_update_category( $catarr ) {
	$cat_ID = (int) $catarr['cat_ID'];

	if ( isset( $catarr['category_parent'] ) && ( $cat_ID == $catarr['category_parent'] ) ) {
		return false;
	}

	// First, get all of the original fields
	$category = get_term( $cat_ID, 'category', ARRAY_A );
	_make_cat_compat( $category );

	// Escape data pulled from DB.
	$category = wp_slash( $category );

	// Merge old and new fields with new fields overwriting old ones.
	$catarr = array_merge( $category, $catarr );

	return wp_insert_category( $catarr );
}

//
// Tags
//

/**
 * Check whether a post tag with a given name exists.
 *
 * @since 2.3.0
 *
 * @param int|string $tag_name
 * @return mixed
 */
function tag_exists( $tag_name ) {
	return term_exists( $tag_name, 'post_tag' );
}

/**
 * Add a new tag to the database if it does not already exist.
 *
 * @since 2.3.0
 *
 * @param int|string $tag_name
 * @return array|WP_Error
 */
function wp_create_tag( $tag_name ) {
	return wp_create_term( $tag_name, 'post_tag' );
}

/**
 * Get comma-separated list of tags available to edit.
 *
 * @since 2.3.0
 *
 * @param int    $post_id
 * @param string $taxonomy Optional. The taxonomy for which to retrieve terms. Default 'post_tag'.
 * @return string|bool|WP_Error
 */
function get_tags_to_edit( $post_id, $taxonomy = 'post_tag' ) {
	return get_terms_to_edit( $post_id, $taxonomy );
}

/**
 * Get comma-separated list of terms available to edit for the given post ID.
 *
 * @since 2.8.0
 *
 * @param int    $post_id
 * @param string $taxonomy Optional. The taxonomy for which to retrieve terms. Default 'post_tag'.
 * @return string|bool|WP_Error
 */
function get_terms_to_edit( $post_id, $taxonomy = 'post_tag' ) {
	$post_id = (int) $post_id;
	if ( ! $post_id ) {
		return false;
	}

	$terms = get_object_term_cache( $post_id, $taxonomy );
	if ( false === $terms ) {
		$terms = wp_get_object_terms( $post_id, $taxonomy );
		wp_cache_add( $post_id, wp_list_pluck( $terms, 'term_id' ), $taxonomy . '_relationships' );
	}

	if ( ! $terms ) {
		return false;
	}
	if ( is_wp_error( $terms ) ) {
		return $terms;
	}
	$term_names = array();
	foreach ( $terms as $term ) {
		$term_names[] = $term->name;
	}

	$terms_to_edit = esc_attr( join( ',', $term_names ) );

	/**
	 * Filters the comma-separated list of terms available to edit.
	 *
	 * @since 2.8.0
	 *
	 * @see get_terms_to_edit()
	 *
	 * @param string $terms_to_edit A comma-separated list of term names.
	 * @param string $taxonomy      The taxonomy name for which to retrieve terms.
	 */
	$terms_to_edit = apply_filters( 'terms_to_edit', $terms_to_edit, $taxonomy );

	return $terms_to_edit;
}

/**
 * Add a new term to the database if it does not already exist.
 *
 * @since 2.8.0
 *
 * @param int|string $tag_name
 * @param string $taxonomy Optional. The taxonomy for which to retrieve terms. Default 'post_tag'.
 * @return array|WP_Error
 */
function wp_create_term( $tag_name, $taxonomy = 'post_tag' ) {
	if ( $id = term_exists( $tag_name, $taxonomy ) ) {
		return $id;
	}

	return wp_insert_term( $tag_name, $taxonomy );
}
