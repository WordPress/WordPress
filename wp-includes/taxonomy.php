<?php

//
// Taxonomy Registration
//

/**
 * @global array $wp_taxonomies Fill me out please
 */
$wp_taxonomies = array();
$wp_taxonomies['category'] = (object) array('name' => 'category', 'object_type' => 'post', 'hierarchical' => true, 'update_count_callback' => '_update_post_term_count');
$wp_taxonomies['post_tag'] = (object) array('name' => 'post_tag', 'object_type' => 'post', 'hierarchical' => false, 'update_count_callback' => '_update_post_term_count');
$wp_taxonomies['link_category'] = (object) array('name' => 'link_category', 'object_type' => 'link', 'hierarchical' => false);

/**
 * get_object_taxonomies() - Appears to return all of the names that are of $object_type
 *
 * It appears that this function can be used to find all of the names inside of
 * $wp_taxonomies global variable.
 *
 * @example 
 *      <?php $taxonomies = get_object_taxonomies('post'); ?>
 *      Should result in <pre>Array(
 *      'category',
 *      'post_tag'
 *      )</pre>
 *
 * @package Taxonomy
 * @global array $wp_taxonomies
 * @param string $object_type Name of the type of taxonomy object
 * @return array The names of all within the object_type.
 *
 * @internal
 *      This won't appear but just a note to say that this is all conjecture and parts or whole
 *      might be inaccurate or wrong.
 */
function get_object_taxonomies($object_type) {
	global $wp_taxonomies;

	$taxonomies = array();
	foreach ( $wp_taxonomies as $taxonomy ) {
		if ( $object_type == $taxonomy->object_type )
			$taxonomies[] = $taxonomy->name;
	}

	return $taxonomies;
}

/**
 * get_taxonomy() - Returns the "taxonomy" object of $taxonomy.
 *
 * The get_taxonomy function will first check that the parameter string given
 * is a taxonomy object and if it is, it will return it.
 *
 * @package Taxonomy
 * @global array $wp_taxonomies
 * @param string $taxonomy Name of taxonomy object to return
 * @return object The Taxonomy Object
 *
 * @internal
 *      This won't appear but just a note to say that this is all conjecture and parts or whole
 *      might be inaccurate or wrong.
 */
function get_taxonomy( $taxonomy ) {
	global $wp_taxonomies;

	if ( ! is_taxonomy($taxonomy) )
		return false;

	return $wp_taxonomies[$taxonomy];
}

/**
 * is_taxonomy() - Checks that the taxonomy name exists
 *
 * @package Taxonomy
 * @global array $wp_taxonomies
 * @param string $taxonomy Name of taxonomy object
 * @return bool Whether the taxonomy exists or not.
 *
 * @internal
 *      This won't appear but just a note to say that this is all conjecture and parts or whole
 *      might be inaccurate or wrong.
 */
function is_taxonomy( $taxonomy ) {
	global $wp_taxonomies;

	return isset($wp_taxonomies[$taxonomy]);	
}

/**
 * is_taxonomy_hierarchical() - Whether the taxonomy object is hierarchical
 *
 * Checks to make sure that the taxonomy is an object first. Then Gets the object, and finally
 * returns the hierarchical value in the object.
 *
 * @package Taxonomy
 * @global array $wp_taxonomies
 * @param string $taxonomy Name of taxonomy object
 * @return bool Whether the taxonomy is hierarchical
 *
 * @internal
 *      This won't appear but just a note to say that this is all conjecture and parts or whole
 *      might be inaccurate or wrong.
 */
function is_taxonomy_hierarchical($taxonomy) {
	if ( ! is_taxonomy($taxonomy) )
		return false;

	$taxonomy = get_taxonomy($taxonomy);
	return $taxonomy->hierarchical;
}

/**
 * register_taxonomy() - Create or modify a taxonomy object.
 *
 * A simple function for creating or modifying a taxonomy object based on the parameters given.
 * The function will accept an array (third optional parameter), along with strings for the 
 * taxonomy name and another string for the object type.
 *
 * The function keeps a default set, allowing for the $args to be optional but allow the other
 * functions to still work. It is possible to overwrite the default set, which contains two
 * keys: hierarchical and update_count_callback.
 *
 * hierarachical has some defined purpose at other parts of the API, but is bool value.
 *
 * update_count_callback works much like a hook, in that it will be called (or something from
 *      somewhere).
 *
 * @package Taxonomy
 * @global array $wp_taxonomies
 * @param string $taxonomy Name of taxonomy object
 * @param string $object_type Name of the object type for the taxonomy object.
 * @param array $args See above description for the two keys values.
 * @return null Nothing is returned, so expect error maybe or use is_taxonomy() to check.
 *
 * @internal
 *      This won't appear but just a note to say that this is all conjecture and parts or whole
 *      might be inaccurate or wrong.
 */
function register_taxonomy( $taxonomy, $object_type, $args = array() ) {
	global $wp_taxonomies;

	$defaults = array('hierarchical' => false, 'update_count_callback' => '');
	$args = wp_parse_args($args, $defaults);

	$args['name'] = $taxonomy;
	$args['object_type'] = $object_type;
	$wp_taxonomies[$taxonomy] = (object) $args;
}

//
// Term API
//

/**
 * get_objects_in_term() - Return object_ids of valid taxonomy and term
 *
 * The strings of $taxonomies must exist before this function will continue. On failure of finding
 * a valid taxonomy, it will return an WP_Error class, kind of like Exceptions in PHP 5, except you
 * can't catch them. Even so, you can still test for the WP_Error class and get the error message.
 *
 * The $terms aren't checked the same as $taxonomies, but still need to exist for $object_ids to
 * be returned.
 *
 * It is possible to change the order that object_ids is returned by either using PHP sort family 
 * functions or using the database by using $args with either ASC or DESC array. The value should
 * be in the key named 'order'.
 *
 * @package Taxonomy
 * @subpackage Term
 * @global object $wpdb Database Query
 * @param string|array $terms String of term or array of string values of terms that will be used
 * @param string|array $taxonomies String of taxonomy name or Array of string values of taxonomy names
 * @param array $args Change the order of the object_ids, either ASC or DESC
 * @return object WP_Error - A PHP 4 compatible Exception class prototype
 * @return array Empty array if there are no $object_ids
 * @return array Array of $object_ids
 *
 * @internal
 *      This won't appear but just a note to say that this is all conjecture and parts or whole
 *      might be inaccurate or wrong.
 */
function get_objects_in_term( $terms, $taxonomies, $args = array() ) {
	global $wpdb;

	if ( !is_array( $terms) )
		$terms = array($terms);

	if ( !is_array($taxonomies) )
		$taxonomies = array($taxonomies);

	foreach ( $taxonomies as $taxonomy ) {
		if ( ! is_taxonomy($taxonomy) )
			return new WP_Error('invalid_taxonomy', __('Invalid Taxonomy'));
	}

	$defaults = array('order' => 'ASC');
	$args = wp_parse_args( $args, $defaults );
	extract($args, EXTR_SKIP);

	$terms = array_map('intval', $terms);

	$taxonomies = "'" . implode("', '", $taxonomies) . "'";
	$terms = "'" . implode("', '", $terms) . "'";

	$object_ids = $wpdb->get_col("SELECT tr.object_id FROM $wpdb->term_relationships AS tr INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tt.taxonomy IN ($taxonomies) AND tt.term_id IN ($terms) ORDER BY tr.object_id $order");

	if ( ! $object_ids )
		return array();

	return $object_ids;
}

/**
 * get_term() - 
 *
 * 
 *
 * @package Taxonomy
 * @subpackage Term
 * @global object $wpdb Database Query
 * @param int|object $term
 * @param string $taxonomy
 * @param string $output Either OBJECT, ARRAY_A, or ARRAY_N
 * @return mixed Term Row from database
 *
 * @internal
 *      This won't appear but just a note to say that this is all conjecture and parts or whole
 *      might be inaccurate or wrong.
 */
function &get_term(&$term, $taxonomy, $output = OBJECT, $filter = 'raw') {
	global $wpdb;

	if ( empty($term) )
		return null;

	if ( ! is_taxonomy($taxonomy) )
		return new WP_Error('invalid_taxonomy', __('Invalid Taxonomy'));

	if ( is_object($term) ) {
		wp_cache_add($term->term_id, $term, $taxonomy);
		$_term = $term;
	} else {
		$term = (int) $term;
		if ( ! $_term = wp_cache_get($term, $taxonomy) ) {
			$_term = $wpdb->get_row("SELECT t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy = '$taxonomy' AND t.term_id = '$term' LIMIT 1");
			wp_cache_add($term, $_term, $taxonomy);
		}
	}

	$_term = apply_filters('get_term', $_term, $taxonomy);
	$_term = apply_filters("get_$taxonomy", $_term, $taxonomy);
	$_term = sanitize_term($_term, $taxonomy, $filter);

	if ( $output == OBJECT ) {
		return $_term;
	} elseif ( $output == ARRAY_A ) {
		return get_object_vars($_term);
	} elseif ( $output == ARRAY_N ) {
		return array_values(get_object_vars($_term));
	} else {
		return $_term;
	}
}

/**
 * get_term_by() - 
 *
 * 
 *
 * @package Taxonomy
 * @subpackage Term
 * @global object $wpdb Database Query
 * @param string $field 
 * @param string $value 
 * @param string $taxonomy 
 * @param string $output Either OBJECT, ARRAY_A, or ARRAY_N
 * @return mixed Term Row from database
 *
 * @internal
 *      This won't appear but just a note to say that this is all conjecture and parts or whole
 *      might be inaccurate or wrong.
 */
function get_term_by($field, $value, $taxonomy, $output = OBJECT, $filter = 'raw') {
	global $wpdb;

	if ( ! is_taxonomy($taxonomy) )
		return false;

	if ( 'slug' == $field ) {
		$field = 't.slug';
		$value = sanitize_title($value);
		if ( empty($value) )
			return false;
	} else if ( 'name' == $field ) {
		// Assume already escaped
		$field = 't.name';
	} else {
		$field = 't.term_id';
		$value = (int) $value;
	}

	$term = $wpdb->get_row("SELECT t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy = '$taxonomy' AND $field = '$value' LIMIT 1");
	if ( !$term )
		return false;

	wp_cache_add($term->term_id, $term, $taxonomy);

	$term = sanitize_term($term, $taxonomy, $filter);

	if ( $output == OBJECT ) {
		return $term;
	} elseif ( $output == ARRAY_A ) {
		return get_object_vars($term);
	} elseif ( $output == ARRAY_N ) {
		return array_values(get_object_vars($term));
	} else {
		return $term;
	}
}

function get_term_children( $term, $taxonomy ) {
	if ( ! is_taxonomy($taxonomy) )
		return new WP_Error('invalid_taxonomy', __('Invalid Taxonomy'));

	$terms = _get_term_hierarchy($taxonomy);

	if ( ! isset($terms[$term]) )
		return array();

	$children = $terms[$term];

	foreach ( $terms[$term] as $child ) {
		if ( isset($terms[$child]) )
			$children = array_merge($children, get_term_children($child, $taxonomy));
	}

	return $children;
}

function get_term_field( $field, $term, $taxonomy, $context = 'display' ) {
	$term = (int) $term;
	$term = get_term( $term, $taxonomy );

	if ( is_wp_error($term) )
		return $term;

	if ( !is_object($term) )
		return '';

	if ( !isset($term->$field) )
		return '';

	return sanitize_term_field($field, $term->$field, $term->term_id, $taxonomy, $context);
}

function get_term_to_edit( $id, $taxonomy ) {
	$term = get_term( $id, $taxonomy );

	if ( is_wp_error($term) )
		return $term;

	if ( !is_object($term) )
		return '';

	return sanitize_term($term, $taxonomy, 'edit');
}

function &get_terms($taxonomies, $args = '') {
	global $wpdb;

	$single_taxonomy = false;
	if ( !is_array($taxonomies) ) {
		$single_taxonomy = true;
		$taxonomies = array($taxonomies);
	}

	foreach ( $taxonomies as $taxonomy ) {
		if ( ! is_taxonomy($taxonomy) )
			return new WP_Error('invalid_taxonomy', __('Invalid Taxonomy'));
	}

	$in_taxonomies = "'" . implode("', '", $taxonomies) . "'";

	$defaults = array('orderby' => 'name', 'order' => 'ASC',
		'hide_empty' => true, 'exclude' => '', 'include' => '',
		'number' => '', 'fields' => 'all', 'slug' => '', 'parent' => '',
		'hierarchical' => true, 'child_of' => 0, 'get' => '', 'name__like' => '');
	$args = wp_parse_args( $args, $defaults );
	$args['number'] = (int) $args['number'];
	if ( !$single_taxonomy || !is_taxonomy_hierarchical($taxonomies[0]) ||
		'' != $args['parent'] ) {
		$args['child_of'] = 0;
		$args['hierarchical'] = false;
	}

	if ( 'all' == $args['get'] ) {
		$args['child_of'] = 0;
		$args['hide_empty'] = 0;
		$args['hierarchical'] = false;
	}
	extract($args, EXTR_SKIP);

	if ( $child_of ) {
		$hierarchy = _get_term_hierarchy($taxonomies[0]);
		if ( !isset($hierarchy[$child_of]) )
			return array();
	}

	if ( $parent ) {
		$hierarchy = _get_term_hierarchy($taxonomies[0]);
		if ( !isset($hierarchy[$parent]) )
			return array();
	}

	$key = md5( serialize( $args ) . serialize( $taxonomies ) );
	if ( $cache = wp_cache_get( 'get_terms', 'terms' ) ) {
		if ( isset( $cache[ $key ] ) )
			return apply_filters('get_terms', $cache[$key], $taxonomies, $args);
	}

	if ( 'count' == $orderby )
		$orderby = 'tt.count';
	else if ( 'name' == $orderby )
		$orderby = 't.name';
	else
		$orderby = 't.term_id';

	$where = '';
	$inclusions = '';
	if ( !empty($include) ) {
		$exclude = '';
		$interms = preg_split('/[\s,]+/',$include);
		if ( count($interms) ) {
			foreach ( $interms as $interm ) {
				if (empty($inclusions))
					$inclusions = ' AND ( t.term_id = ' . intval($interm) . ' ';
				else
					$inclusions .= ' OR t.term_id = ' . intval($interm) . ' ';
			}
		}
	}

	if ( !empty($inclusions) )
		$inclusions .= ')';
	$where .= $inclusions;

	$exclusions = '';
	if ( !empty($exclude) ) {
		$exterms = preg_split('/[\s,]+/',$exclude);
		if ( count($exterms) ) {
			foreach ( $exterms as $exterm ) {
				if (empty($exclusions))
					$exclusions = ' AND ( t.term_id <> ' . intval($exterm) . ' ';
				else
					$exclusions .= ' AND t.term_id <> ' . intval($exterm) . ' ';
			}
		}
	}

	if ( !empty($exclusions) )
		$exclusions .= ')';
	$exclusions = apply_filters('list_terms_exclusions', $exclusions, $args );
	$where .= $exclusions;

	if ( !empty($slug) ) {
		$slug = sanitize_title($slug);
		$where .= " AND t.slug = '$slug'";
	}

	if ( !empty($name__like) )
		$where .= " AND t.name LIKE '{$name__like}%'";

	if ( '' != $parent ) {
		$parent = (int) $parent;
		$where .= " AND tt.parent = '$parent'";
	}

	if ( $hide_empty && !$hierarchical )
		$where .= ' AND tt.count > 0';

	if ( !empty($number) )
		$number = 'LIMIT ' . $number;
	else
		$number = '';

	if ( 'all' == $fields )
		$select_this = 't.*, tt.*';
	else if ( 'ids' == $fields )
		$select_this = 't.term_id';
	else if ( 'names' == $fields )
		$select_this == 't.name';

	$query = "SELECT $select_this FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ($in_taxonomies) $where ORDER BY $orderby $order $number";

	if ( 'all' == $fields ) {
		$terms = $wpdb->get_results($query);
		update_term_cache($terms);
	} else if ( 'ids' == $fields ) {
		$terms = $wpdb->get_col($query);
	}

	if ( empty($terms) )
		return array();

	if ( $child_of || $hierarchical ) {
		$children = _get_term_hierarchy($taxonomies[0]);
		if ( ! empty($children) )
			$terms = & _get_term_children($child_of, $terms, $taxonomies[0]);
	}

	/*
	// Update category counts to include children.
	if ( $pad_counts )
		_pad_category_counts($type, $categories);

	// Make sure we show empty categories that have children.
	if ( $hierarchical && $hide_empty ) {
		foreach ( $categories as $k => $category ) {
			if ( ! $category->{'link' == $type ? 'link_count' : 'category_count'} ) {
				$children = _get_cat_children($category->cat_ID, $categories);
				foreach ( $children as $child )
					if ( $child->{'link' == $type ? 'link_count' : 'category_count'} )
						continue 2;

				// It really is empty
				unset($categories[$k]);
			}
		}
	}
	reset ( $categories );
	*/

	$cache[ $key ] = $terms;
	wp_cache_set( 'get_terms', $cache, 'term' );

	$terms = apply_filters('get_terms', $terms, $taxonomies, $args);
	return $terms;
}

/**
 * Returns the index of a defined term, or 0 (false) if the term doesn't exist.
 */
function is_term($term, $taxonomy = '') {
	global $wpdb;

	if ( is_int($term) ) {
		if ( 0 == $term )
			return 0;
		$where = "t.term_id = '$term'";
	} else {
		if ( ! $term = sanitize_title($term) )
			return 0;
		$where = "t.slug = '$term'";
	}

	$term_id = $wpdb->get_var("SELECT term_id FROM $wpdb->terms as t WHERE $where");

	if ( empty($taxonomy) || empty($term_id) )
		return $term_id;

	return $wpdb->get_row("SELECT tt.term_id, tt.term_taxonomy_id FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy as tt ON tt.term_id = t.term_id WHERE $where AND tt.taxonomy = '$taxonomy'", ARRAY_A);
}

function sanitize_term($term, $taxonomy, $context = 'display') {
	$fields = array('term_id', 'name', 'description', 'slug', 'count', 'parent', 'term_group');

	$do_object = false;
	if ( is_object($term) )
		$do_object = true;

	foreach ( $fields as $field ) {
		if ( $do_object )
			$term->$field = sanitize_term_field($field, $term->$field, $term->term_id, $taxonomy, $context);
		else
			$term[$field] = sanitize_term_field($field, $term[$field], $term['term_id'], $taxonomy, $context);	
	}

	return $term;
}

function sanitize_term_field($field, $value, $term_id, $taxonomy, $context) {
	if ( 'parent' == $field  || 'term_id' == $field || 'count' == $field
		|| 'term_group' == $field )
		$value = (int) $value;

	if ( 'edit' == $context ) {
		$value = apply_filters("edit_term_$field", $value, $term_id, $taxonomy);
		$value = apply_filters("edit_${taxonomy}_$field", $value, $term_id);
		if ( 'description' == $field )
			$value = format_to_edit($value);
		else
			$value = attribute_escape($value);
	} else if ( 'db' == $context ) {
		$value = apply_filters("pre_term_$field", $value, $taxonomy);
		$value = apply_filters("pre_${taxonomy}_$field", $value);	
	} else {
		// Use display filters by default.
		$value = apply_filters("term_$field", $value, $term_id, $taxonomy, $context);
		$value = apply_filters("${taxonomy}_$field", $value, $term_id, $context);
	}

	if ( 'attribute' == $context )
		$value = attribute_escape($value);
	else if ( 'js' == $context )
		$value = js_escape($value);

	return $value;
}

function wp_count_terms( $taxonomy, $args = array() ) {
	global $wpdb;

	$defaults = array('ignore_empty' => false);
	$args = wp_parse_args($args, $defaults);
	extract($args, EXTR_SKIP);

	$where = '';
	if ( $ignore_empty )
		$where = 'AND count > 0';

	return $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->term_taxonomy WHERE taxonomy = '$taxonomy' $where");
}

function wp_delete_object_term_relationships( $object_id, $taxonomies ) {
	global $wpdb;

	$object_id = (int) $object_id;

	if ( !is_array($taxonomies) )
		$taxonomies = array($taxonomies);

	foreach ( $taxonomies as $taxonomy ) {
		$terms = wp_get_object_terms($object_id, $taxonomy, 'fields=tt_ids');
		$in_terms = "'" . implode("', '", $terms) . "'";
		$wpdb->query("DELETE FROM $wpdb->term_relationships WHERE object_id = '$object_id' AND term_taxonomy_id IN ($in_terms)");
		wp_update_term_count($terms, $taxonomy);
	}

	// TODO clear the cache
}

/**
 * Removes a term from the database.
 */
function wp_delete_term( $term, $taxonomy, $args = array() ) {
	global $wpdb;

	$term = (int) $term;

	if ( ! $ids = is_term($term, $taxonomy) )
		return false;
	$tt_id = $ids['term_taxonomy_id'];

	$defaults = array();
	$args = wp_parse_args($args, $defaults);
	extract($args, EXTR_SKIP);

	if ( isset($default) ) {
		$default = (int) $default;
		if ( ! is_term($default, $taxonomy) )
			unset($default);
	}

	// Update children to point to new parent
	if ( is_taxonomy_hierarchical($taxonomy) ) {
		$term_obj = get_term($term, $taxonomy);
		$parent = $term_obj->parent;

		$wpdb->query("UPDATE $wpdb->term_taxonomy SET parent = '$parent' WHERE parent = '$term_obj->term_id' AND taxonomy = '$taxonomy'");
	}

	$objects = $wpdb->get_col("SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id = '$tt_id'");

	foreach ( (array) $objects as $object ) {
		$terms = wp_get_object_terms($object, $taxonomy, 'fields=ids');
		if ( 1 == count($terms) && isset($default) )
			$terms = array($default);
		else
			$terms = array_diff($terms, array($term));
		wp_set_object_terms($object, $terms, $taxonomy);
	}

	$wpdb->query("DELETE FROM $wpdb->term_taxonomy WHERE term_taxonomy_id = '$tt_id'");

	// Delete the term if no taxonomies use it.
	if ( !$wpdb->get_var("SELECT COUNT(*) FROM $wpdb->term_taxonomy WHERE term_id = '$term'") )
		$wpdb->query("DELETE FROM $wpdb->terms WHERE term_id = '$term'");

	clean_term_cache($term, $taxonomy);

	do_action("delete_$taxonomy", $term, $tt_id);

	return true;
}

/**
 * Returns the terms associated with the given object(s), in the supplied taxonomies.
 * @param int|array $object_id The id of the object(s)) to retrieve for.
 * @param string|array $taxonomies The taxonomies to retrieve terms from.
 * @return array The requested term data.	 	 	 
 */
function wp_get_object_terms($object_ids, $taxonomies, $args = array()) {
	global $wpdb;

	if ( !is_array($taxonomies) )
		$taxonomies = array($taxonomies);

	foreach ( $taxonomies as $taxonomy ) {
		if ( ! is_taxonomy($taxonomy) )
			return new WP_Error('invalid_taxonomy', __('Invalid Taxonomy'));
	}

	if ( !is_array($object_ids) )
		$object_ids = array($object_ids);
	$object_ids = array_map('intval', $object_ids);

	$defaults = array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'all');
	$args = wp_parse_args( $args, $defaults );
	extract($args, EXTR_SKIP);

	if ( 'count' == $orderby )
		$orderby = 'tt.count';
	else if ( 'name' == $orderby )
		$orderby = 't.name';

	$taxonomies = "'" . implode("', '", $taxonomies) . "'";
	$object_ids = implode(', ', $object_ids);

	if ( 'all' == $fields )
		$select_this = 't.*, tt.*';
	else if ( 'ids' == $fields )
		$select_this = 't.term_id';
	else if ( 'all_with_object_id' == $fields )
		$select_this = 't.*, tt.*, tr.object_id';

	$query = "SELECT $select_this FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON tt.term_id = t.term_id INNER JOIN $wpdb->term_relationships AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tt.taxonomy IN ($taxonomies) AND tr.object_id IN ($object_ids) ORDER BY $orderby $order";

	if ( 'all' == $fields || 'all_with_object_id' == $fields ) {
		$terms = $wpdb->get_results($query);
		update_term_cache($terms);
	} else if ( 'ids' == $fields ) {
		$terms = $wpdb->get_col($query);
	} else if ( 'tt_ids' == $fields ) {
		$terms = $wpdb->get_col("SELECT tr.term_taxonomy_id FROM $wpdb->term_relationships AS tr INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tr.object_id IN ($object_ids) AND tt.taxonomy IN ($taxonomies) ORDER BY tr.term_taxonomy_id $order");
	}

	if ( ! $terms )
		return array();

	return $terms;
}

/**
 * Adds a new term to the database.  Optionally marks it as an alias of an existing term.
 * @param int|string $term The term to add or update.
 * @param string $taxonomy The taxonomy to which to add the term
 * @param int|string $alias_of The id or slug of the new term's alias.
 */
function wp_insert_term( $term, $taxonomy, $args = array() ) {
	global $wpdb;

	if ( ! is_taxonomy($taxonomy) )
		return new WP_Error('invalid_taxonomy', __('Invalid taxonomy'));

	if ( is_int($term) && 0 == $term )
		return new WP_Error('invalid_term_id', __('Invalid term ID'));

	$defaults = array( 'alias_of' => '', 'description' => '', 'parent' => 0, 'slug' => '');
	$args = wp_parse_args($args, $defaults);
	$args['name'] = $term;
	$args = sanitize_term($args, $taxonomy, 'db');
	extract($args, EXTR_SKIP);

	if ( empty($slug) )
		$slug = sanitize_title($name);

	$term_group = 0;	
	if ( $alias_of ) {
		$alias = $wpdb->fetch_row("SELECT term_id, term_group FROM $wpdb->terms WHERE slug = '$alias_of'");
		if ( $alias->term_group ) {
			// The alias we want is already in a group, so let's use that one.
			$term_group = $alias->term_group;
		} else {
			// The alias isn't in a group, so let's create a new one and firstly add the alias term to it.
			$term_group = $wpdb->get_var("SELECT MAX(term_group) FROM $wpdb->terms GROUP BY term_group") + 1;
			$wpdb->query("UPDATE $wpdb->terms SET term_group = $term_group WHERE term_id = $alias->term_id");
		}
	}

	if ( ! $term_id = is_term($slug) ) {
		$wpdb->query("INSERT INTO $wpdb->terms (name, slug, term_group) VALUES ('$name', '$slug', '$term_group')");
		$term_id = (int) $wpdb->insert_id;
	}

	if ( empty($slug) ) {
		$slug = sanitize_title($slug, $term_id);
		$wpdb->query("UPDATE $wpdb->terms SET slug = '$slug' WHERE term_id = '$term_id'");
	}

	$tt_id = $wpdb->get_var("SELECT tt.term_taxonomy_id FROM $wpdb->term_taxonomy AS tt INNER JOIN $wpdb->terms AS t ON tt.term_id = t.term_id WHERE tt.taxonomy = '$taxonomy' AND t.term_id = $term_id");

	if ( !empty($tt_id) )
		return array('term_id' => $term_id, 'term_taxonomy_id' => $tt_id);

	$wpdb->query("INSERT INTO $wpdb->term_taxonomy (term_id, taxonomy, description, parent, count) VALUES ('$term_id', '$taxonomy', '$description', '$parent', '0')");
	$tt_id = (int) $wpdb->insert_id;

	do_action("create_term", $term_id, $tt_id);
	do_action("create_$taxonomy", $term_id, $tt_id);

	$term_id = apply_filters('term_id_filter', $term_id, $tt_id);

	clean_term_cache($term_id, $taxonomy);

	do_action("created_term", $term_id, $tt_id);
	do_action("created_$taxonomy", $term_id, $tt_id);

	return array('term_id' => $term_id, 'term_taxonomy_id' => $tt_id);
}

/**
 * Relates an object (post, link etc) to a term and taxonomy type.  Creates the term and taxonomy
 * relationship if it doesn't already exist.  Creates a term if it doesn't exist (using the slug).
 * @param int $object_id The object to relate to.
 * @param array|int|string $term The slug or id of the term.
 * @param array|string $taxonomy The context in which to relate the term to the object.
 */
function wp_set_object_terms($object_id, $terms, $taxonomy, $append = false) {
	global $wpdb;

	$object_id = (int) $object_id;

	if ( ! is_taxonomy($taxonomy) )
		return new WP_Error('invalid_taxonomy', __('Invalid Taxonomy'));

	if ( !is_array($terms) )
		$terms = array($terms);

	if ( ! $append )
		$old_terms =  wp_get_object_terms($object_id, $taxonomy, 'fields=tt_ids');

	$tt_ids = array();
	$term_ids = array();

	foreach ($terms as $term) {
		if ( !$id = is_term($term, $taxonomy) )
			$id = wp_insert_term($term, $taxonomy);
		$term_ids[] = $id['term_id'];
		$id = $id['term_taxonomy_id'];
		$tt_ids[] = $id;

		if ( $wpdb->get_var("SELECT term_taxonomy_id FROM $wpdb->term_relationships WHERE object_id = '$object_id' AND term_taxonomy_id = '$id'") )
			continue;
		$wpdb->query("INSERT INTO $wpdb->term_relationships (object_id, term_taxonomy_id) VALUES ('$object_id', '$id')");
	}

	wp_update_term_count($tt_ids, $taxonomy);

	if ( ! $append ) {
		$delete_terms = array_diff($old_terms, $tt_ids);
		if ( $delete_terms ) {
			$in_delete_terms = "'" . implode("', '", $delete_terms) . "'";
			$wpdb->query("DELETE FROM $wpdb->term_relationships WHERE object_id = '$object_id' AND term_taxonomy_id IN ($in_delete_terms)");
			wp_update_term_count($delete_terms, $taxonomy);
		}
	}

	// TODO clean old_terms. Need term_id instead of tt_id
	clean_term_cache($term_ids, $taxonomy);

	return $tt_ids;
}

function wp_update_term( $term, $taxonomy, $args = array() ) {
	global $wpdb;

	if ( ! is_taxonomy($taxonomy) )
		return new WP_Error('invalid_taxonomy', __('Invalid taxonomy'));

	$term_id = (int) $term;

	// First, get all of the original args
	$term = get_term ($term_id, $taxonomy, ARRAY_A);

	$term = sanitize_term($term, $taxonomy, 'db');

	// Escape data pulled from DB.
	$term = add_magic_quotes($term);

	// Merge old and new args with new args overwriting old ones.
	$args = array_merge($term, $args);

	$defaults = array( 'alias_of' => '', 'description' => '', 'parent' => 0, 'slug' => '');
	$args = wp_parse_args($args, $defaults);
	extract($args, EXTR_SKIP);

	if ( empty($slug) )
		$slug = sanitize_title($name);
	else
		$slug = sanitize_title($slug);

	if ( $alias_of ) {
		$alias = $wpdb->fetch_row("SELECT term_id, term_group FROM $wpdb->terms WHERE slug = '$alias_of'");
		if ( $alias->term_group ) {
			// The alias we want is already in a group, so let's use that one.
			$term_group = $alias->term_group;
		} else {
			// The alias isn't in a group, so let's create a new one and firstly add the alias term to it.
			$term_group = $wpdb->get_var("SELECT MAX(term_group) FROM $wpdb->terms GROUP BY term_group") + 1;
			$wpdb->query("UPDATE $wpdb->terms SET term_group = $term_group WHERE term_id = $alias->term_id");
		}
	}

	$wpdb->query("UPDATE $wpdb->terms SET name = '$name', slug = '$slug', term_group = '$term_group' WHERE term_id = '$term_id'");

	if ( empty($slug) ) {
		$slug = sanitize_title($name, $term_id);
		$wpdb->query("UPDATE $wpdb->terms SET slug = '$slug' WHERE term_id = '$term_id'");
	}

	$tt_id = $wpdb->get_var("SELECT tt.term_taxonomy_id FROM $wpdb->term_taxonomy AS tt INNER JOIN $wpdb->terms AS t ON tt.term_id = t.term_id WHERE tt.taxonomy = '$taxonomy' AND t.term_id = $term_id");

	$wpdb->query("UPDATE $wpdb->term_taxonomy SET term_id = '$term_id', taxonomy = '$taxonomy', description = '$description', parent = '$parent' WHERE term_taxonomy_id = '$tt_id'");

	do_action("edit_term", $term_id, $tt_id);
	do_action("edit_$taxonomy", $term_id, $tt_id);

	$term_id = apply_filters('term_id_filter', $term_id, $tt_id);

	clean_term_cache($term_id, $taxonomy);

	do_action("edited_term", $term_id, $tt_id);
	do_action("edited_$taxonomy", $term_id, $tt_id);

	return array('term_id' => $term_id, 'term_taxonomy_id' => $tt_id);
}

function wp_update_term_count( $terms, $taxonomy ) {
	global $wpdb;

	if ( empty($terms) )
		return false;

	if ( !is_array($terms) )
		$terms = array($terms);

	$terms = array_map('intval', $terms);

	$taxonomy = get_taxonomy($taxonomy);
	if ( !empty($taxonomy->update_count_callback) )
		return call_user_func($taxonomy->update_count_callback, $terms);

	// Default count updater
	foreach ($terms as $term) {
		$count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->term_relationships WHERE term_taxonomy_id = '$term'");
		$wpdb->query("UPDATE $wpdb->term_taxonomy SET count = '$count' WHERE term_taxonomy_id = '$term'");
	}

	return true;
}

//
// Cache
//

function clean_object_term_cache($object_ids, $object_type) {
	global $object_term_cache, $blog_id;

	if ( !is_array($ids) )
		$ids = array($ids);

	$taxonomies = get_object_taxonomies($object_type);

	foreach ( $ids as $id ) {
		foreach ( $taxonomies as $taxonomy ) {
			if ( isset($object_term_cache[$blog_id][$id][$taxonomy]) )
				unset($object_term_cache[$blog_id][$id][$taxonomy]);
		}
	}
}

function clean_term_cache($ids, $taxonomy) {
	if ( !is_array($ids) )
		$ids = array($ids);

	foreach ( $ids as $id ) {
		wp_cache_delete($id, $taxonomy);
	}

	wp_cache_delete('all_ids', $taxonomy);
	wp_cache_delete('get', $taxonomy);
	delete_option("{$taxonomy}_children");
	wp_cache_delete('get_terms', 'terms');
}

function &get_object_term_cache($id, $taxonomy) {
	global $object_term_cache, $blog_id;

	if ( isset($object_term_cache[$blog_id][$id][$taxonomy]) )
		return $object_term_cache[$blog_id][$id][$taxonomy];

	if ( isset($object_term_cache[$blog_id][$id]) )
		return array();

	return false;
}

function update_object_term_cache($object_ids, $object_type) {
	global $wpdb, $object_term_cache, $blog_id;

	if ( empty($object_ids) )
		return;

	if ( !is_array($object_ids) )
		$object_ids = explode(',', $object_ids);

	$count = count( $object_ids);
	for ( $i = 0; $i < $count; $i++ ) {
		$object_id = (int) $object_ids[ $i ];
		if ( isset( $object_term_cache[$blog_id][$object_id] ) ) {
			unset( $object_ids[ $i ] );
			continue;
		}
	}

	if ( count( $object_ids ) == 0 )
		return;

	$terms = wp_get_object_terms($object_ids, get_object_taxonomies($object_type), 'fields=all_with_object_id');

	if ( empty($terms) )
		return;

	foreach ( $terms as $term )
		$object_term_cache[$blog_id][$term->object_id][$term->taxonomy][$term->term_id] = $term;

	foreach ( $object_ids as $id ) {
		if ( ! isset($object_term_cache[$blog_id][$id]) )
				$object_term_cache[$blog_id][$id] = array();
	}
}

function update_term_cache($terms, $taxonomy = '') {
	foreach ( $terms as $term ) {
		$term_taxonomy = $taxonomy;
		if ( empty($term_taxonomy) )
			$term_taxonomy = $term->taxonomy;

		wp_cache_add($term->term_id, $term, $term_taxonomy);
	}
}

//
// Private
//

function _get_term_hierarchy($taxonomy) {
	// TODO Make sure taxonomy is hierarchical
	$children = get_option("{$taxonomy}_children");
	if ( is_array($children) )
		return $children;

	$children = array();
	$terms = get_terms($taxonomy, 'get=all');
	foreach ( $terms as $term ) {
		if ( $term->parent > 0 )
			$children[$term->parent][] = $term->term_id;
	}
	update_option("{$taxonomy}_children", $children);

	return $children;
}

function &_get_term_children($term_id, $terms, $taxonomy) {
	if ( empty($terms) )
		return array();

	$term_list = array();
	$has_children = _get_term_hierarchy($taxonomy);

	if  ( ( 0 != $term_id ) && ! isset($has_children[$term_id]) )
		return array();

	foreach ( $terms as $term ) {
		$use_id = false;
		if ( !is_object($term) ) {
			$term = get_term($term, $taxonomy);
			$use_id = true;
		}

		if ( $term->term_id == $term_id )
			continue;

		if ( $term->parent == $term_id ) {
			if ( $use_id )
				$term_list[] = $term->term_id;
			else
				$term_list[] = $term;

			if ( !isset($has_children[$term->term_id]) )
				continue;

			if ( $children = _get_term_children($term->term_id, $terms, $taxonomy) )
				$term_list = array_merge($term_list, $children);
		}
	}

	return $term_list;
}

//
// Default callbacks
//

function _update_post_term_count( $terms ) {
	global $wpdb;

	foreach ( $terms as $term ) {
		$count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->term_relationships, $wpdb->posts WHERE $wpdb->posts.ID = $wpdb->term_relationships.object_id AND post_status = 'publish' AND post_type = 'post' AND term_taxonomy_id = '$term'");
		$wpdb->query("UPDATE $wpdb->term_taxonomy SET count = '$count' WHERE term_taxonomy_id = '$term'");
	}
}

?>