<?php
/**
 * Metadata API
 *
 * Functions for retrieving and manipulating metadata of various WordPress object types.  Metadata
 * for an object is a represented by a simple key-value pair.  Objects may contain multiple
 * metadata entries that share the same key and differ only in their value.
 *
 * @package WordPress
 * @subpackage Meta
 * @since 2.9.0
 */

/**
 * Add metadata for the specified object.
 *
 * @since 2.9.0
 * @uses $wpdb WordPress database object for queries.
 * @uses do_action() Calls 'added_{$meta_type}_meta' with meta_id of added metadata entry,
 * 		object ID, meta key, and meta value
 *
 * @param string $meta_type Type of object metadata is for (e.g., comment, post, or user)
 * @param int $object_id ID of the object metadata is for
 * @param string $meta_key Metadata key
 * @param string $meta_value Metadata value
 * @param bool $unique Optional, default is false.  Whether the specified metadata key should be
 * 		unique for the object.  If true, and the object already has a value for the specified
 * 		metadata key, no change will be made
 * @return bool True on successful update, false on failure.
 */
function add_metadata($meta_type, $object_id, $meta_key, $meta_value, $unique = false) {
	if ( !$meta_type || !$meta_key )
		return false;

	if ( !$object_id = absint($object_id) )
		return false;

	if ( ! $table = _get_meta_table($meta_type) )
		return false;

	global $wpdb;

	$column = esc_sql($meta_type . '_id');

	// expected_slashed ($meta_key)
	$meta_key = stripslashes($meta_key);

	if ( $unique && $wpdb->get_var( $wpdb->prepare(
		"SELECT COUNT(*) FROM $table WHERE meta_key = %s AND $column = %d",
		$meta_key, $object_id ) ) )
		return false;

	$_meta_value = $meta_value;
	$meta_value = maybe_serialize( stripslashes_deep($meta_value) );

	$wpdb->insert( $table, array(
		$column => $object_id,
		'meta_key' => $meta_key,
		'meta_value' => $meta_value
	) );

	wp_cache_delete($object_id, $meta_type . '_meta');
	// users cache stores usermeta that must be cleared.
	if ( 'user' == $meta_type )
		clean_user_cache($object_id);

	do_action( "added_{$meta_type}_meta", $wpdb->insert_id, $object_id, $meta_key, $_meta_value );

	return true;
}

/**
 * Update metadata for the specified object.  If no value already exists for the specified object
 * ID and metadata key, the metadata will be added.
 *
 * @since 2.9.0
 * @uses $wpdb WordPress database object for queries.
 * @uses do_action() Calls 'update_{$meta_type}_meta' before updating metadata with meta_id of
 * 		metadata entry to update, object ID, meta key, and meta value
 * @uses do_action() Calls 'updated_{$meta_type}_meta' after updating metadata with meta_id of
 * 		updated metadata entry, object ID, meta key, and meta value
 *
 * @param string $meta_type Type of object metadata is for (e.g., comment, post, or user)
 * @param int $object_id ID of the object metadata is for
 * @param string $meta_key Metadata key
 * @param string $meta_value Metadata value
 * @param string $prev_value Optional.  If specified, only update existing metadata entries with
 * 		the specified value.  Otherwise, update all entries.
 * @return bool True on successful update, false on failure.
 */
function update_metadata($meta_type, $object_id, $meta_key, $meta_value, $prev_value = '') {
	if ( !$meta_type || !$meta_key )
		return false;

	if ( !$object_id = absint($object_id) )
		return false;

	if ( ! $table = _get_meta_table($meta_type) )
		return false;

	global $wpdb;

	$column = esc_sql($meta_type . '_id');
	$id_column = 'user' == $meta_type ? 'umeta_id' : 'meta_id';

	// expected_slashed ($meta_key)
	$meta_key = stripslashes($meta_key);

	if ( ! $meta_id = $wpdb->get_var( $wpdb->prepare( "SELECT $id_column FROM $table WHERE meta_key = %s AND $column = %d", $meta_key, $object_id ) ) )
		return add_metadata($meta_type, $object_id, $meta_key, $meta_value);

	// Compare existing value to new value if no prev value given and the key exists only once.
	if ( empty($prev_value) ) {
		$old_value = get_metadata($meta_type, $object_id, $meta_key);
		if ( count($old_value) == 1 ) {
			if ( $old_value[0] === $meta_value )
				return false;
		}
	}

	$_meta_value = $meta_value;
	$meta_value = maybe_serialize( stripslashes_deep($meta_value) );

	$data  = compact( 'meta_value' );
	$where = array( $column => $object_id, 'meta_key' => $meta_key );

	if ( !empty( $prev_value ) ) {
		$prev_value = maybe_serialize($prev_value);
		$where['meta_value'] = $prev_value;
	}

	do_action( "update_{$meta_type}_meta", $meta_id, $object_id, $meta_key, $_meta_value );

	$wpdb->update( $table, $data, $where );
	wp_cache_delete($object_id, $meta_type . '_meta');
	// users cache stores usermeta that must be cleared.
	if ( 'user' == $meta_type )
		clean_user_cache($object_id);

	do_action( "updated_{$meta_type}_meta", $meta_id, $object_id, $meta_key, $_meta_value );

	return true;
}

/**
 * Delete metadata for the specified object.
 *
 * @since 2.9.0
 * @uses $wpdb WordPress database object for queries.
 * @uses do_action() Calls 'deleted_{$meta_type}_meta' after deleting with meta_id of
 * 		deleted metadata entries, object ID, meta key, and meta value
 *
 * @param string $meta_type Type of object metadata is for (e.g., comment, post, or user)
 * @param int $object_id ID of the object metadata is for
 * @param string $meta_key Metadata key
 * @param string $meta_value Optional. Metadata value.  If specified, only delete metadata entries
 * 		with this value.  Otherwise, delete all entries with the specified meta_key.
 * @param bool $delete_all Optional, default is false.  If true, delete matching metadata entries
 * 		for all objects, ignoring the specified object_id.  Otherwise, only delete matching
 * 		metadata entries for the specified object_id.
 * @return bool True on successful delete, false on failure.
 */
function delete_metadata($meta_type, $object_id, $meta_key, $meta_value = '', $delete_all = false) {
	if ( !$meta_type || !$meta_key )
		return false;

	if ( (!$object_id = absint($object_id)) && !$delete_all )
		return false;

	if ( ! $table = _get_meta_table($meta_type) )
		return false;

	global $wpdb;

	$type_column = esc_sql($meta_type . '_id');
	$id_column = 'user' == $meta_type ? 'umeta_id' : 'meta_id';
	// expected_slashed ($meta_key)
	$meta_key = stripslashes($meta_key);
	$meta_value = maybe_serialize( stripslashes_deep($meta_value) );

	$query = $wpdb->prepare( "SELECT $id_column FROM $table WHERE meta_key = %s", $meta_key );

	if ( !$delete_all )
		$query .= $wpdb->prepare(" AND $type_column = %d", $object_id );

	if ( $meta_value )
		$query .= $wpdb->prepare(" AND meta_value = %s", $meta_value );

	$meta_ids = $wpdb->get_col( $query );
	if ( !count( $meta_ids ) )
		return false;

	$query = "DELETE FROM $table WHERE $id_column IN( " . implode( ',', $meta_ids ) . " )";

	$count = $wpdb->query($query);

	if ( !$count )
		return false;

	wp_cache_delete($object_id, $meta_type . '_meta');
	// users cache stores usermeta that must be cleared.
	if ( 'user' == $meta_type )
		clean_user_cache($object_id);

	do_action( "deleted_{$meta_type}_meta", $meta_ids, $object_id, $meta_key, $meta_value );

	return true;
}

/**
 * Retrieve metadata for the specified object.
 *
 * @since 2.9.0
 *
 * @param string $meta_type Type of object metadata is for (e.g., comment, post, or user)
 * @param int $object_id ID of the object metadata is for
 * @param string $meta_key Optional.  Metadata key.  If not specified, retrieve all metadata for
 * 		the specified object.
 * @param bool $single Optional, default is false.  If true, return only the first value of the
 * 		specified meta_key.  This parameter has no effect if meta_key is not specified.
 * @return string|array Single metadata value, or array of values
 */
function get_metadata($meta_type, $object_id, $meta_key = '', $single = false) {
	if ( !$meta_type )
		return false;

	if ( !$object_id = absint($object_id) )
		return false;

	$meta_cache = wp_cache_get($object_id, $meta_type . '_meta');

	if ( !$meta_cache ) {
		update_meta_cache($meta_type, $object_id);
		$meta_cache = wp_cache_get($object_id, $meta_type . '_meta');
	}

	if ( ! $meta_key )
		return $meta_cache;

	if ( isset($meta_cache[$meta_key]) ) {
		if ( $single )
			return maybe_unserialize( $meta_cache[$meta_key][0] );
		else
			return array_map('maybe_unserialize', $meta_cache[$meta_key]);
	}

	if ($single)
		return '';
	else
		return array();
}

/**
 * Update the metadata cache for the specified objects.
 *
 * @since 2.9.0
 * @uses $wpdb WordPress database object for queries.
 *
 * @param string $meta_type Type of object metadata is for (e.g., comment, post, or user)
 * @param int|array $object_ids array or comma delimited list of object IDs to update cache for
 * @return mixed Metadata cache for the specified objects, or false on failure.
 */
function update_meta_cache($meta_type, $object_ids) {
	if ( empty( $meta_type ) || empty( $object_ids ) )
		return false;

	if ( ! $table = _get_meta_table($meta_type) )
		return false;

	$column = esc_sql($meta_type . '_id');

	global $wpdb;

	if ( !is_array($object_ids) ) {
		$object_ids = preg_replace('|[^0-9,]|', '', $object_ids);
		$object_ids = explode(',', $object_ids);
	}

	$object_ids = array_map('intval', $object_ids);

	$cache_key = $meta_type . '_meta';
	$ids = array();
	foreach ( $object_ids as $id ) {
		if ( false === wp_cache_get($id, $cache_key) )
			$ids[] = $id;
	}

	if ( empty( $ids ) )
		return false;

	// Get meta info
	$id_list = join(',', $ids);
	$cache = array();
	$meta_list = $wpdb->get_results( $wpdb->prepare("SELECT $column, meta_key, meta_value FROM $table WHERE $column IN ($id_list)",
		$meta_type), ARRAY_A );

	if ( !empty($meta_list) ) {
		foreach ( $meta_list as $metarow) {
			$mpid = intval($metarow[$column]);
			$mkey = $metarow['meta_key'];
			$mval = $metarow['meta_value'];

			// Force subkeys to be array type:
			if ( !isset($cache[$mpid]) || !is_array($cache[$mpid]) )
				$cache[$mpid] = array();
			if ( !isset($cache[$mpid][$mkey]) || !is_array($cache[$mpid][$mkey]) )
				$cache[$mpid][$mkey] = array();

			// Add a value to the current pid/key:
			$cache[$mpid][$mkey][] = $mval;
		}
	}

	foreach ( $ids as $id ) {
		if ( ! isset($cache[$id]) )
			$cache[$id] = array();
	}

	foreach ( array_keys($cache) as $object)
		wp_cache_set($object, $cache[$object], $cache_key);

	return $cache;
}

/**
 * Retrieve the name of the metadata table for the specified object type.
 *
 * @since 2.9.0
 * @uses $wpdb WordPress database object for queries.
 *
 * @param string $meta_type Type of object to get metadata table for (e.g., comment, post, or user)
 * @return mixed Metadata table name, or false if no metadata table exists
 */
function _get_meta_table($type) {
	global $wpdb;

	$table_name = $type . 'meta';

	if ( empty($wpdb->$table_name) )
		return false;

	return $wpdb->$table_name;
}
?>
