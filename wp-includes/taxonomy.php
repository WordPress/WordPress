<?php

/**
 * Adds a new term to the database.  Optionally marks it as an alias of an existing term.
 * @param int|string $term The term to add or update.
 * @param string $taxonomy The taxonomy to which to add the term
 * @param int|string $alias_of The id or slug of the new term's alias.
 */
function wp_insert_term( $term, $taxonomy, $args = array() ) {
	global $wpdb;

	$update = false;
	if ( is_int($term) ) {
		$update = true;
		$term_id = $term;
	} else {
		$name = $term;
	}

	$defaults = array( 'alias_of' => '', 'description' => '', 'parent' => 0, 'slug' => '');
	$args = wp_parse_args($args, $defaults);
	extract($args);

	$parent = (int) $parent;

	if ( empty($slug) )
		$slug = sanitize_title($name);
	else
		$slug = sanitize_title($slug);

	$term_group = 0;	
	if ( $alias_of ) {
		$alias = $wpdb->fetch_row("SELECT term_id, term_group FROM $wpdb->terms WHERE slug = '$alias_of'");
		if ( $alias->term_group ) {
			// The alias we want is already in a group, so let's use that one.
			$term_group = $alias->term_group;
		} else {
			// The alias isn't in a group, so let's create a new one and firstly add the alias term to it.
			$term_group = $wpdb->get_var("SELECT MAX() term_group FROM $wpdb->terms GROUP BY term_group") + 1;
			$wpdb->query("UPDATE $wpdb->terms SET term_group = $term_group WHERE term_id = $alias->term_id");
		}
	}

	if ( $update ) {
		$wpdb->query("UPDATE $wpdb->terms SET name = '$name', slug = '$slug', term_group = '$term_group' WHERE term_id = '$term_id'");
	} else if ( ! $term_id = is_term($slug) ) {
		$wpdb->query("INSERT INTO $wpdb->terms (name, slug, term_group) VALUES ('$name', '$slug', '$term_group')");
		$term_id = (int) $wpdb->insert_id;
	}

	if ( empty($slug) ) {
		$slug = sanitize_title($slug, $term_id);
		$wpdb->query("UPDATE $wpdb->terms SET slug = '$slug' WHERE term_id = '$term_id'");
	}
		
	$tt_id = $wpdb->get_var("SELECT tt.term_taxonomy_id FROM $wpdb->term_taxonomy AS tt INNER JOIN $wpdb->terms AS t ON tt.term_id = t.term_id WHERE tt.taxonomy = '$taxonomy' AND t.term_id = $term_id");

	if ( !$update && !empty($tt_id) )
		return array('term_id' => $term_id, 'term_taxonomy_id' => $tt_id);

	if ( $update ) {
		$wpdb->query("UPDATE $wpdb->term_taxonomy SET term_id = '$term_id', taxonomy = '$taxonomy', description = '$description', parent = '$parent', count = 0 WHERE term_taxonomy_id = '$tt_id'");
	} else {
		$wpdb->query("INSERT INTO $wpdb->term_taxonomy (term_id, taxonomy, description, parent, count) VALUES ('$term_id', '$taxonomy', '$description', '$parent', '0')");
		$tt_id = (int) $wpdb->insert_id;
	}

	return array('term_id' => $term_id, 'term_taxonomy_id' => $tt_id);
}

/**
 * Removes a term from the database.
 */
function wp_delete_term() {}

function wp_update_term( $term, $taxonomy, $fields = array() ) {
	$term = (int) $term;

	// First, get all of the original fields
	$term = get_term ($term, $taxonomy, ARRAY_A);

	// Escape data pulled from DB.
	$term = add_magic_quotes($term);

	// Merge old and new fields with new fields overwriting old ones.
	$fields = array_merge($term, $fields);

	return wp_insert_term($term, $taxonomy, $fields);
}

/**
 * Returns the index of a defined term, or 0 (false) if the term doesn't exist.
 */
function is_term($term, $taxonomy = '') {
	global $wpdb;

	if ( is_int($term) ) {
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
	
/**
 * Given an array of terms, returns those that are defined term slugs.  Ignores integers.
 * @param array $terms The term slugs to check for a definition.
 */
function get_defined_terms($terms) {
	global $wpdb;

	foreach ( $terms as $term ) {
		if ( !is_int($term) )
			$searches[] = $term;
	}

	$terms = "'" . implode("', '", $searches) . "'";
	return $wpdb->get_col("SELECT slug FROM $wpdb->terms WHERE slug IN ($terms)");
}
	
/**
 * Relates an object (post, link etc) to a term and taxonomy type.  Creates the term and taxonomy
 * relationship if it doesn't already exist.  Creates a term if it doesn't exist (using the slug).
 * @param int $object_id The object to relate to.
 * @param array|int|string $term The slug or id of the term.
 * @param array|string $taxonomies The context(s) in which to relate the term to the object.
 */
function wp_set_object_terms($object_id, $terms, $taxonomies, $append = false) {
	global $wpdb;

	$object_id = (int) $object_id;

	if ( !is_array($taxonomies) )
		$taxonomies = array($taxonomies);
	
	if ( !is_array($terms) )
		$terms = array($terms);

	if ( ! $append ) {
		$in_taxonomies = "'" . implode("', '", $taxonomies) . "'";
		$old_terms = $wpdb->get_col("SELECT tr.term_taxonomy_id FROM $wpdb->term_relationships AS tr INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tt.taxonomy IN ($in_taxonomies) AND tr.object_id = '$object_id'");
	}

	$tt_ids = array();

	foreach ( $taxonomies as $taxonomy ) {
		foreach ($terms as $term) {
			if ( !$id = is_term($term, $taxonomy) )
				$id = wp_insert_term($term, $taxonomy);
			$id = $id['term_taxonomy_id'];
			$tt_ids[] = $id;
			if ( $wpdb->get_var("SELECT term_taxonomy_id FROM $wpdb->term_relationships WHERE object_id = '$object_id' AND term_taxonomy_id = '$id'") )
				continue;
			$wpdb->query("INSERT INTO $wpdb->term_relationships (object_id, term_taxonomy_id) VALUES ('$object_id', '$id')");
			$wpdb->query("UPDATE $wpdb->term_taxonomy SET count = count + 1 WHERE term_taxonomy_id = $id");
		}
	}

	if ( ! $append ) {
		$delete_terms = array_diff($old_terms, $tt_ids);
		if ( $delete_terms ) {
			$delete_terms = "'" . implode("', '", $delete_terms) . "'";
			$wpdb->query("DELETE FROM $wpdb->term_relationships WHERE term_taxonomy_id IN ($delete_terms)");
			$wpdb->query("UPDATE $wpdb->term_taxonomy SET count = count - 1 WHERE term_taxonomy_id IN ($delete_terms)");
		}
	}

	return $tt_ids;
}

/**
 * Returns the terms associated with the given object(s), in the supplied taxonomies.
 * @param int|array $object_id The id of the object(s)) to retrieve for.
 * @param string|array $taxonomies The taxonomies to retrieve terms from.
 * @return array The requested term data.	 	 	 
 */
function get_object_terms($object_id, $taxonomy, $args = array()) {
	global $wpdb;
	$taxonomies = ($single_taxonomy = !is_array($taxonomy)) ? array($taxonomy) : $taxonomy;
	$object_ids = ($single_object = !is_array($object_id)) ? array($object_id) : $object_id;

	$defaults = array('orderby' => 'name', 'order' => 'ASC', 'get' => 'everything');
	$args = wp_parse_args( $args, $defaults );
	extract($args);

	if ( 'count' == $orderby )
		$orderby = 'tt.count';
	else if ( 'name' == $orderby )
		$orderby = 't.name';

	$taxonomies = "'" . implode("', '", $taxonomies) . "'";
	$object_ids = implode(', ', $object_ids);

	if ( 'everything' == $get )
		$select_this = 't.*';
	else if ( 'ids' == $get )
		$select_this = 't.term_id';

	$query = "SELECT $select_this FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON tt.term_id = t.term_id INNER JOIN $wpdb->term_relationships AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tt.taxonomy IN ($taxonomies) AND tr.object_id IN ($object_ids) ORDER BY $orderby $order";

	if ( 'everything' == $get )
		$taxonomy_data = $wpdb->get_results($query);
	else if ( 'ids' == $get )
		$taxonomy_data = $wpdb->get_col($query);

	if ( ! $taxonomy_data )
		return array();

	if ($single_taxonomy && $single_object) {
		// Just one kind of taxonomy for one object.
		return $taxonomy_data;
	} else {
		foreach ($taxonomy_data as $data) {
			if ($single_taxonomy) {
				// Many objects, one taxonomy type.
				$return[$data->object_id][] = $data;
			} elseif ($single_object) {
				// One object, many taxonomies.
				$return[$data->taxonomy][] = $data;
			} else {
				// Many objects, many taxonomies.
				$return[$data->object_id][$data->taxonomy][] = $data;
			}
		}
		return $return;			
	}
}

function &get_terms($taxonomies, $args = '') {
	global $wpdb;

	if ( !is_array($taxonomies) )
		$taxonomies = array($taxonomies);
	$in_taxonomies = "'" . implode("', '", $taxonomies) . "'";

	$defaults = array('orderby' => 'name', 'order' => 'ASC',
		'hide_empty' => true, 'exclude' => '', 'include' => '',
		'number' => '', 'get' => 'everything');
	$args = wp_parse_args( $args, $defaults );
	$args['number'] = (int) $args['number'];
	extract($args);

	if ( 'count' == $orderby )
		$orderby = 'tt.count';
	else if ( 'name' == $orderby )
		$orderby = 't.name';

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

	if ( $hide_empty )
		$where .= ' AND tt.count > 0';

	if ( !empty($number) )
		$number = 'LIMIT ' . $number;
	else
		$number = '';

	if ( 'everything' == $get )
		$select_this = 't.*, tt.*';
	else if ( 'ids' == $get )
		$select_this = 't.term_id';

	$query = "SELECT $select_this FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ($in_taxonomies) $where ORDER BY $orderby $order $number";

	if ( 'everything' == $get )
		$terms = $wpdb->get_results($query);
	else if ( 'ids' == $get )
		$terms = $wpdb->get_col($query);

	if ( empty($terms) )
		return array();

	$terms = apply_filters('get_terms', $terms, $taxonomies, $args);
	return $terms;
}

function &get_term(&$term, $taxonomy, $output = OBJECT) {
	global $wpdb;

	if ( empty($term) )
		return null;

	if ( is_object($term) ) {
		wp_cache_add($term->term_id, $term, "term:$taxonomy");
		$_term = $term;
	} else {
		$term = (int) $term;
		if ( ! $_term = wp_cache_get($term, "term:$taxonomy") ) {
			$_term = $wpdb->get_row("SELECT t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy = '$taxonomy' AND t.term_id = '$term' LIMIT 1");
			wp_cache_add($term, $_term, "term:$taxonomy");
		}
	}

	$_term = apply_filters('get_term', $_term, $taxonomy);

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

?>