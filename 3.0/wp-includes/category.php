<?php
/**
 * WordPress Category API
 *
 * @package WordPress
 */

/**
 * Retrieves all category IDs.
 *
 * @since 2.0.0
 * @link http://codex.wordpress.org/Function_Reference/get_all_category_ids
 *
 * @return object List of all of the category IDs.
 */
function get_all_category_ids() {
	if ( ! $cat_ids = wp_cache_get( 'all_category_ids', 'category' ) ) {
		$cat_ids = get_terms( 'category', array('fields' => 'ids', 'get' => 'all') );
		wp_cache_add( 'all_category_ids', $cat_ids, 'category' );
	}

	return $cat_ids;
}

/**
 * Retrieve list of category objects.
 *
 * If you change the type to 'link' in the arguments, then the link categories
 * will be returned instead. Also all categories will be updated to be backwards
 * compatible with pre-2.3 plugins and themes.
 *
 * @since 2.1.0
 * @see get_terms() Type of arguments that can be changed.
 * @link http://codex.wordpress.org/Function_Reference/get_categories
 *
 * @param string|array $args Optional. Change the defaults retrieving categories.
 * @return array List of categories.
 */
function &get_categories( $args = '' ) {
	$defaults = array( 'taxonomy' => 'category' );
	$args = wp_parse_args( $args, $defaults );

	$taxonomy = apply_filters( 'get_categories_taxonomy', $args['taxonomy'], $args );

	// Back compat
	if ( isset($args['type']) && 'link' == $args['type'] ) {
		_deprecated_argument( __FUNCTION__, '3.0', '' );
		$taxonomy = $args['taxonomy'] = 'link_category';
	}

	$categories = (array) get_terms( $taxonomy, $args );

	foreach ( array_keys( $categories ) as $k )
		_make_cat_compat( $categories[$k] );

	return $categories;
}

/**
 * Retrieves category data given a category ID or category object.
 *
 * If you pass the $category parameter an object, which is assumed to be the
 * category row object retrieved the database. It will cache the category data.
 *
 * If you pass $category an integer of the category ID, then that category will
 * be retrieved from the database, if it isn't already cached, and pass it back.
 *
 * If you look at get_term(), then both types will be passed through several
 * filters and finally sanitized based on the $filter parameter value.
 *
 * The category will converted to maintain backwards compatibility.
 *
 * @since 1.5.1
 * @uses get_term() Used to get the category data from the taxonomy.
 *
 * @param int|object $category Category ID or Category row object
 * @param string $output Optional. Constant OBJECT, ARRAY_A, or ARRAY_N
 * @param string $filter Optional. Default is raw or no WordPress defined filter will applied.
 * @return mixed Category data in type defined by $output parameter.
 */
function &get_category( $category, $output = OBJECT, $filter = 'raw' ) {
	$category = get_term( $category, 'category', $output, $filter );
	if ( is_wp_error( $category ) )
		return $category;

	_make_cat_compat( $category );

	return $category;
}

/**
 * Retrieve category based on URL containing the category slug.
 *
 * Breaks the $category_path parameter up to get the category slug.
 *
 * Tries to find the child path and will return it. If it doesn't find a
 * match, then it will return the first category matching slug, if $full_match,
 * is set to false. If it does not, then it will return null.
 *
 * It is also possible that it will return a WP_Error object on failure. Check
 * for it when using this function.
 *
 * @since 2.1.0
 *
 * @param string $category_path URL containing category slugs.
 * @param bool $full_match Optional. Whether full path should be matched.
 * @param string $output Optional. Constant OBJECT, ARRAY_A, or ARRAY_N
 * @return null|object|array Null on failure. Type is based on $output value.
 */
function get_category_by_path( $category_path, $full_match = true, $output = OBJECT ) {
	$category_path = rawurlencode( urldecode( $category_path ) );
	$category_path = str_replace( '%2F', '/', $category_path );
	$category_path = str_replace( '%20', ' ', $category_path );
	$category_paths = '/' . trim( $category_path, '/' );
	$leaf_path  = sanitize_title( basename( $category_paths ) );
	$category_paths = explode( '/', $category_paths );
	$full_path = '';
	foreach ( (array) $category_paths as $pathdir )
		$full_path .= ( $pathdir != '' ? '/' : '' ) . sanitize_title( $pathdir );

	$categories = get_terms( 'category', array('get' => 'all', 'slug' => $leaf_path) );

	if ( empty( $categories ) )
		return null;

	foreach ( $categories as $category ) {
		$path = '/' . $leaf_path;
		$curcategory = $category;
		while ( ( $curcategory->parent != 0 ) && ( $curcategory->parent != $curcategory->term_id ) ) {
			$curcategory = get_term( $curcategory->parent, 'category' );
			if ( is_wp_error( $curcategory ) )
				return $curcategory;
			$path = '/' . $curcategory->slug . $path;
		}

		if ( $path == $full_path )
			return get_category( $category->term_id, $output );
	}

	// If full matching is not required, return the first cat that matches the leaf.
	if ( ! $full_match )
		return get_category( $categories[0]->term_id, $output );

	return null;
}

/**
 * Retrieve category object by category slug.
 *
 * @since 2.3.0
 *
 * @param string $slug The category slug.
 * @return object Category data object
 */
function get_category_by_slug( $slug  ) {
	$category = get_term_by( 'slug', $slug, 'category' );
	if ( $category )
		_make_cat_compat( $category );

	return $category;
}


/**
 * Retrieve the ID of a category from its name.
 *
 * @since 1.0.0
 *
 * @param string $cat_name Optional. Default is 'General' and can be any category name.
 * @return int 0, if failure and ID of category on success.
 */
function get_cat_ID( $cat_name='General' ) {
	$cat = get_term_by( 'name', $cat_name, 'category' );
	if ( $cat )
		return $cat->term_id;
	return 0;
}


/**
 * Retrieve the name of a category from its ID.
 *
 * @since 1.0.0
 *
 * @param int $cat_id Category ID
 * @return string Category name, or an empty string if category doesn't exist.
 */
function get_cat_name( $cat_id ) {
	$cat_id = (int) $cat_id;
	$category = &get_category( $cat_id );
	if ( ! $category || is_wp_error( $category ) )
		return '';
	return $category->name;
}


/**
 * Check if a category is an ancestor of another category.
 *
 * You can use either an id or the category object for both parameters. If you
 * use an integer the category will be retrieved.
 *
 * @since 2.1.0
 *
 * @param int|object $cat1 ID or object to check if this is the parent category.
 * @param int|object $cat2 The child category.
 * @return bool Whether $cat2 is child of $cat1
 */
function cat_is_ancestor_of( $cat1, $cat2 ) {
	if ( ! isset($cat1->term_id) )
		$cat1 = &get_category( $cat1 );
	if ( ! isset($cat2->parent) )
		$cat2 = &get_category( $cat2 );

	if ( empty($cat1->term_id) || empty($cat2->parent) )
		return false;
	if ( $cat2->parent == $cat1->term_id )
		return true;

	return cat_is_ancestor_of( $cat1, get_category( $cat2->parent ) );
}


/**
 * Sanitizes category data based on context.
 *
 * @since 2.3.0
 * @uses sanitize_term() See this function for what context are supported.
 *
 * @param object|array $category Category data
 * @param string $context Optional. Default is 'display'.
 * @return object|array Same type as $category with sanitized data for safe use.
 */
function sanitize_category( $category, $context = 'display' ) {
	return sanitize_term( $category, 'category', $context );
}


/**
 * Sanitizes data in single category key field.
 *
 * @since 2.3.0
 * @uses sanitize_term_field() See function for more details.
 *
 * @param string $field Category key to sanitize
 * @param mixed $value Category value to sanitize
 * @param int $cat_id Category ID
 * @param string $context What filter to use, 'raw', 'display', etc.
 * @return mixed Same type as $value after $value has been sanitized.
 */
function sanitize_category_field( $field, $value, $cat_id, $context ) {
	return sanitize_term_field( $field, $value, $cat_id, 'category', $context );
}

/* Tags */


/**
 * Retrieves all post tags.
 *
 * @since 2.3.0
 * @see get_terms() For list of arguments to pass.
 * @uses apply_filters() Calls 'get_tags' hook on array of tags and with $args.
 *
 * @param string|array $args Tag arguments to use when retrieving tags.
 * @return array List of tags.
 */
function &get_tags( $args = '' ) {
	$tags = get_terms( 'post_tag', $args );

	if ( empty( $tags ) ) {
		$return = array();
		return $return;
	}

	$tags = apply_filters( 'get_tags', $tags, $args );
	return $tags;
}


/**
 * Retrieve post tag by tag ID or tag object.
 *
 * If you pass the $tag parameter an object, which is assumed to be the tag row
 * object retrieved the database. It will cache the tag data.
 *
 * If you pass $tag an integer of the tag ID, then that tag will
 * be retrieved from the database, if it isn't already cached, and pass it back.
 *
 * If you look at get_term(), then both types will be passed through several
 * filters and finally sanitized based on the $filter parameter value.
 *
 * @since 2.3.0
 *
 * @param int|object $tag
 * @param string $output Optional. Constant OBJECT, ARRAY_A, or ARRAY_N
 * @param string $filter Optional. Default is raw or no WordPress defined filter will applied.
 * @return object|array Return type based on $output value.
 */
function &get_tag( $tag, $output = OBJECT, $filter = 'raw' ) {
	return get_term( $tag, 'post_tag', $output, $filter );
}


/* Cache */


/**
 * Update the categories cache.
 *
 * This function does not appear to be used anymore or does not appear to be
 * needed. It might be a legacy function left over from when there was a need
 * for updating the category cache.
 *
 * @since 1.5.0
 *
 * @return bool Always return True
 */
function update_category_cache() {
	return true;
}


/**
 * Remove the category cache data based on ID.
 *
 * @since 2.1.0
 * @uses clean_term_cache() Clears the cache for the category based on ID
 *
 * @param int $id Category ID
 */
function clean_category_cache( $id ) {
	clean_term_cache( $id, 'category' );
}


/**
 * Update category structure to old pre 2.3 from new taxonomy structure.
 *
 * This function was added for the taxonomy support to update the new category
 * structure with the old category one. This will maintain compatibility with
 * plugins and themes which depend on the old key or property names.
 *
 * The parameter should only be passed a variable and not create the array or
 * object inline to the parameter. The reason for this is that parameter is
 * passed by reference and PHP will fail unless it has the variable.
 *
 * There is no return value, because everything is updated on the variable you
 * pass to it. This is one of the features with using pass by reference in PHP.
 *
 * @since 2.3.0
 * @access private
 *
 * @param array|object $category Category Row object or array
 */
function _make_cat_compat( &$category ) {
	if ( is_object( $category ) ) {
		$category->cat_ID = &$category->term_id;
		$category->category_count = &$category->count;
		$category->category_description = &$category->description;
		$category->cat_name = &$category->name;
		$category->category_nicename = &$category->slug;
		$category->category_parent = &$category->parent;
	} elseif ( is_array( $category ) && isset( $category['term_id'] ) ) {
		$category['cat_ID'] = &$category['term_id'];
		$category['category_count'] = &$category['count'];
		$category['category_description'] = &$category['description'];
		$category['cat_name'] = &$category['name'];
		$category['category_nicename'] = &$category['slug'];
		$category['category_parent'] = &$category['parent'];
	}
}


?>
