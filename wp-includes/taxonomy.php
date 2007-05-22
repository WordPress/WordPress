<?php

// TODO: add_term(), edit_term(), and remove_term() work with terms within context of
// taxonomies.  insert_term(), update_term(), delete_term() work with just the terms table.
// insert_term_taxonomy(), update_term_taxonomy(), and delete_term_taxonomy() work
// with just the term taxonomy table.  Right now we only have add_term().

/**
 * Adds a new term to the database.  Optionally marks it as an alias of an existing term.
 * @param string $term The term to add.
 * @param string $taxonomy The taxonomy to which to add the term
 * @param int|string $alias_of The id or slug of the new term's alias.
 */
function add_term( $term, $taxonomy, $args = array() ) {
	global $wpdb;

	$slug = sanitize_title($term);
	$defaults = array( 'alias_of' => '', 'description' => '', 'parent' => 0);
	$args = wp_parse_args($args, $defaults);
	extract($args);

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

	if ( ! $term_id = is_term($slug) ) {
		$wpdb->query("INSERT INTO $wpdb->terms (name, slug, term_group) VALUES ('$term', '$slug', '$term_group')");
		$term_id = (int) $wpdb->insert_id;
	}
	
	$tt_id = $wpdb->get_var("SELECT tt.term_taxonomy_id FROM $wpdb->term_taxonomy AS tt INNER JOIN $wpdb->terms AS t ON tt.term_id = t.term_id WHERE tt.taxonomy = '$taxonomy' AND t.term_id = $term_id");

	if ( !empty($tt_id) )
		return $term_id;
			
	$wpdb->query("INSERT INTO $wpdb->term_taxonomy (term_id, taxonomy, description, parent, count) VALUES ('$term_id', '$taxonomy', '$description', '$parent', '0')");
	// TODO: Maybe return both term_id and tt_id.
	return $term_id;
}

/**
 * Removes a term from the database.
 */
function remove_term() {}
	
	
/**
 * Returns the index of a defined term, or 0 (false) if the term doesn't exist.
 */
function is_term($term, $taxonomy = '') {
	global $wpdb;
	if ( ! $term = sanitize_title($term) )
		return 0;

	return $wpdb->get_var("SELECT term_id FROM $wpdb->terms WHERE slug = '$term'");
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
 * @param array|int|string $term The slug or id of the term.
 * @param int $object_id The object to relate to.
 * @param array|string $taxonomies The context(s) in which to relate the term to the object.
 */
function add_term_relationship($terms, $object_id, $taxonomies) {
	global $wpdb;
		
	if ( !is_array($taxonomies) )
		$taxonomies = array($taxonomies);
	
	if ( !is_array($terms) )
		$terms = array($terms);
		
	$defined_terms = get_defined_terms($terms);

	foreach ( $terms as $term ) {
		if ( !is_int($term) ) {
			if ( !isset($defined_terms[$term]) )
				$new_terms[] = $term;
			$slugs[] = $term;
		} else {
			$term_ids[] = $term;
		}
	}

	$term_clause = isset($term_ids) ? 'tt.term_id IN (' . implode(', ', $term_ids) . ')' : '';
	if ( isset($slugs) ) {
		if ($term_clause) {
			$term_clause .= ' OR ';
		}
		$term_clause .= "t.slug IN ('" . implode("', '", $slugs) . "')";
		$term_join = "INNER JOIN $wpdb->terms AS t ON tt.term_id = t.term_id";
	} else {
		$term_join = '';
	}
		
	// Now add or increment the term taxonomy relationships.  This is inefficient at the moment.
	foreach ( $taxonomies as $taxonomy ) {
		foreach ( $terms as $term ) {
			add_term($term, $taxonomy);
		}
	}
		
	$taxonomies = "'" . implode("', '", $taxonomies) . "'";
		
	// Finally, relate the term and taxonomy to the object.
	// Use IGNORE to avoid dupe warnings for now.
	$wpdb->query("INSERT IGNORE INTO $wpdb->term_relationships(object_id, term_taxonomy_id) SELECT '$object_id', term_taxonomy_id FROM $wpdb->term_taxonomy AS tt $term_join WHERE ($term_clause) AND tt.taxonomy IN ($taxonomies)");
}
	
/**
 * Returns the terms associated with the given object(s), in the supplied taxonomies.
 * @param int|array $object_id The id of the object(s)) to retrieve for.
 * @param string|array $taxonomies The taxonomies to retrieve terms from.
 * @return array The requested term data.	 	 	 
 */
function get_object_terms($object_id, $taxonomy) {
	global $wpdb;
	$taxonomies = ($single_taxonomy = !is_array($taxonomy)) ? array($taxonomy) : $taxonomy;
	$object_ids = ($single_object = !is_array($object_id)) ? array($object_id) : $object_id;

	$taxonomies = "'" . implode("', '", $taxonomies) . "'";		
	$object_ids = implode(', ', $object_ids);		

	if ( $taxonomy_data = $wpdb->get_results("SELECT t.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON tt.term_id = t.term_id INNER JOIN $wpdb->term_relationships AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tt.taxonomy IN ($taxonomies) AND tr.object_id IN ($object_ids)") ) {
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
	} else {
		return array();
	}		
}	

?>