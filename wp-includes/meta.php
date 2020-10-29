<?php
/**
 * Metadata API
 *
 * Functions for retrieving and manipulating metadata of various WordPress object types. Metadata
 * for an object is a represented by a simple key-value pair. Objects may contain multiple
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
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $meta_type Type of object metadata is for (e.g., comment, post, or user)
 * @param int $object_id ID of the object metadata is for
 * @param string $meta_key Metadata key
 * @param mixed $meta_value Metadata value. Must be serializable if non-scalar.
 * @param bool $unique Optional, default is false. Whether the specified metadata key should be
 * 		unique for the object. If true, and the object already has a value for the specified
 * 		metadata key, no change will be made
 * @return int|bool The meta ID on success, false on failure.
 */
function add_metadata($meta_type, $object_id, $meta_key, $meta_value, $unique = false) {
	global $wpdb;

	if ( ! $meta_type || ! $meta_key || ! is_numeric( $object_id ) ) {
		return false;
	}

	$object_id = absint( $object_id );
	if ( ! $object_id ) {
		return false;
	}

	$table = _get_meta_table( $meta_type );
	if ( ! $table ) {
		return false;
	}

	$column = sanitize_key($meta_type . '_id');

	// expected_slashed ($meta_key)
	$meta_key = wp_unslash($meta_key);
	$meta_value = wp_unslash($meta_value);
	$meta_value = sanitize_meta( $meta_key, $meta_value, $meta_type );

	/**
	 * Filter whether to add metadata of a specific type.
	 *
	 * The dynamic portion of the hook, `$meta_type`, refers to the meta
	 * object type (comment, post, or user). Returning a non-null value
	 * will effectively short-circuit the function.
	 *
	 * @since 3.1.0
	 *
	 * @param null|bool $check      Whether to allow adding metadata for the given type.
	 * @param int       $object_id  Object ID.
	 * @param string    $meta_key   Meta key.
	 * @param mixed     $meta_value Meta value. Must be serializable if non-scalar.
	 * @param bool      $unique     Whether the specified meta key should be unique
	 *                              for the object. Optional. Default false.
	 */
	$check = apply_filters( "add_{$meta_type}_metadata", null, $object_id, $meta_key, $meta_value, $unique );
	if ( null !== $check )
		return $check;

	if ( $unique && $wpdb->get_var( $wpdb->prepare(
		"SELECT COUNT(*) FROM $table WHERE meta_key = %s AND $column = %d",
		$meta_key, $object_id ) ) )
		return false;

	$_meta_value = $meta_value;
	$meta_value = maybe_serialize( $meta_value );

	/**
	 * Fires immediately before meta of a specific type is added.
	 *
	 * The dynamic portion of the hook, `$meta_type`, refers to the meta
	 * object type (comment, post, or user).
	 *
	 * @since 3.1.0
	 *
	 * @param int    $object_id  Object ID.
	 * @param string $meta_key   Meta key.
	 * @param mixed  $meta_value Meta value.
	 */
	do_action( "add_{$meta_type}_meta", $object_id, $meta_key, $_meta_value );

	$result = $wpdb->insert( $table, array(
		$column => $object_id,
		'meta_key' => $meta_key,
		'meta_value' => $meta_value
	) );

	if ( ! $result )
		return false;

	$mid = (int) $wpdb->insert_id;

	wp_cache_delete($object_id, $meta_type . '_meta');

	/**
	 * Fires immediately after meta of a specific type is added.
	 *
	 * The dynamic portion of the hook, `$meta_type`, refers to the meta
	 * object type (comment, post, or user).
	 *
	 * @since 2.9.0
	 *
	 * @param int    $mid        The meta ID after successful update.
	 * @param int    $object_id  Object ID.
	 * @param string $meta_key   Meta key.
	 * @param mixed  $meta_value Meta value.
	 */
	do_action( "added_{$meta_type}_meta", $mid, $object_id, $meta_key, $_meta_value );

	return $mid;
}

/**
 * Update metadata for the specified object. If no value already exists for the specified object
 * ID and metadata key, the metadata will be added.
 *
 * @since 2.9.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $meta_type Type of object metadata is for (e.g., comment, post, or user)
 * @param int $object_id ID of the object metadata is for
 * @param string $meta_key Metadata key
 * @param mixed $meta_value Metadata value. Must be serializable if non-scalar.
 * @param mixed $prev_value Optional. If specified, only update existing metadata entries with
 * 		the specified value. Otherwise, update all entries.
 * @return int|bool Meta ID if the key didn't exist, true on successful update, false on failure.
 */
function update_metadata($meta_type, $object_id, $meta_key, $meta_value, $prev_value = '') {
	global $wpdb;

	if ( ! $meta_type || ! $meta_key || ! is_numeric( $object_id ) ) {
		return false;
	}

	$object_id = absint( $object_id );
	if ( ! $object_id ) {
		return false;
	}

	$table = _get_meta_table( $meta_type );
	if ( ! $table ) {
		return false;
	}

	$column = sanitize_key($meta_type . '_id');
	$id_column = 'user' == $meta_type ? 'umeta_id' : 'meta_id';

	// expected_slashed ($meta_key)
	$meta_key = wp_unslash($meta_key);
	$passed_value = $meta_value;
	$meta_value = wp_unslash($meta_value);
	$meta_value = sanitize_meta( $meta_key, $meta_value, $meta_type );

	/**
	 * Filter whether to update metadata of a specific type.
	 *
	 * The dynamic portion of the hook, `$meta_type`, refers to the meta
	 * object type (comment, post, or user). Returning a non-null value
	 * will effectively short-circuit the function.
	 *
	 * @since 3.1.0
	 *
	 * @param null|bool $check      Whether to allow updating metadata for the given type.
	 * @param int       $object_id  Object ID.
	 * @param string    $meta_key   Meta key.
	 * @param mixed     $meta_value Meta value. Must be serializable if non-scalar.
	 * @param mixed     $prev_value Optional. If specified, only update existing
	 *                              metadata entries with the specified value.
	 *                              Otherwise, update all entries.
	 */
	$check = apply_filters( "update_{$meta_type}_metadata", null, $object_id, $meta_key, $meta_value, $prev_value );
	if ( null !== $check )
		return (bool) $check;

	// Compare existing value to new value if no prev value given and the key exists only once.
	if ( empty($prev_value) ) {
		$old_value = get_metadata($meta_type, $object_id, $meta_key);
		if ( count($old_value) == 1 ) {
			if ( $old_value[0] === $meta_value )
				return false;
		}
	}

	$meta_ids = $wpdb->get_col( $wpdb->prepare( "SELECT $id_column FROM $table WHERE meta_key = %s AND $column = %d", $meta_key, $object_id ) );
	if ( empty( $meta_ids ) ) {
		return add_metadata($meta_type, $object_id, $meta_key, $passed_value);
	}

	$_meta_value = $meta_value;
	$meta_value = maybe_serialize( $meta_value );

	$data  = compact( 'meta_value' );
	$where = array( $column => $object_id, 'meta_key' => $meta_key );

	if ( !empty( $prev_value ) ) {
		$prev_value = maybe_serialize($prev_value);
		$where['meta_value'] = $prev_value;
	}

	foreach ( $meta_ids as $meta_id ) {
		/**
		 * Fires immediately before updating metadata of a specific type.
		 *
		 * The dynamic portion of the hook, `$meta_type`, refers to the meta
		 * object type (comment, post, or user).
		 *
		 * @since 2.9.0
		 *
		 * @param int    $meta_id    ID of the metadata entry to update.
		 * @param int    $object_id  Object ID.
		 * @param string $meta_key   Meta key.
		 * @param mixed  $meta_value Meta value.
		 */
		do_action( "update_{$meta_type}_meta", $meta_id, $object_id, $meta_key, $_meta_value );
	}

	if ( 'post' == $meta_type ) {
		foreach ( $meta_ids as $meta_id ) {
			/**
			 * Fires immediately before updating a post's metadata.
			 *
			 * @since 2.9.0
			 *
			 * @param int    $meta_id    ID of metadata entry to update.
			 * @param int    $object_id  Object ID.
			 * @param string $meta_key   Meta key.
			 * @param mixed  $meta_value Meta value.
			 */
			do_action( 'update_postmeta', $meta_id, $object_id, $meta_key, $meta_value );
		}
	}

	$result = $wpdb->update( $table, $data, $where );
	if ( ! $result )
		return false;

	wp_cache_delete($object_id, $meta_type . '_meta');

	foreach ( $meta_ids as $meta_id ) {
		/**
		 * Fires immediately after updating metadata of a specific type.
		 *
		 * The dynamic portion of the hook, `$meta_type`, refers to the meta
		 * object type (comment, post, or user).
		 *
		 * @since 2.9.0
		 *
		 * @param int    $meta_id    ID of updated metadata entry.
		 * @param int    $object_id  Object ID.
		 * @param string $meta_key   Meta key.
		 * @param mixed  $meta_value Meta value.
		 */
		do_action( "updated_{$meta_type}_meta", $meta_id, $object_id, $meta_key, $_meta_value );
	}

	if ( 'post' == $meta_type ) {
		foreach ( $meta_ids as $meta_id ) {
			/**
			 * Fires immediately after updating a post's metadata.
			 *
			 * @since 2.9.0
			 *
			 * @param int    $meta_id    ID of updated metadata entry.
			 * @param int    $object_id  Object ID.
			 * @param string $meta_key   Meta key.
			 * @param mixed  $meta_value Meta value.
			 */
			do_action( 'updated_postmeta', $meta_id, $object_id, $meta_key, $meta_value );
		}
	}

	return true;
}

/**
 * Delete metadata for the specified object.
 *
 * @since 2.9.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $meta_type Type of object metadata is for (e.g., comment, post, or user)
 * @param int $object_id ID of the object metadata is for
 * @param string $meta_key Metadata key
 * @param mixed $meta_value Optional. Metadata value. Must be serializable if non-scalar. If specified, only delete metadata entries
 * 		with this value. Otherwise, delete all entries with the specified meta_key.
 * @param bool $delete_all Optional, default is false. If true, delete matching metadata entries
 * 		for all objects, ignoring the specified object_id. Otherwise, only delete matching
 * 		metadata entries for the specified object_id.
 * @return bool True on successful delete, false on failure.
 */
function delete_metadata($meta_type, $object_id, $meta_key, $meta_value = '', $delete_all = false) {
	global $wpdb;

	if ( ! $meta_type || ! $meta_key || ! is_numeric( $object_id ) && ! $delete_all ) {
		return false;
	}

	$object_id = absint( $object_id );
	if ( ! $object_id && ! $delete_all ) {
		return false;
	}

	$table = _get_meta_table( $meta_type );
	if ( ! $table ) {
		return false;
	}

	$type_column = sanitize_key($meta_type . '_id');
	$id_column = 'user' == $meta_type ? 'umeta_id' : 'meta_id';
	// expected_slashed ($meta_key)
	$meta_key = wp_unslash($meta_key);
	$meta_value = wp_unslash($meta_value);

	/**
	 * Filter whether to delete metadata of a specific type.
	 *
	 * The dynamic portion of the hook, `$meta_type`, refers to the meta
	 * object type (comment, post, or user). Returning a non-null value
	 * will effectively short-circuit the function.
	 *
	 * @since 3.1.0
	 *
	 * @param null|bool $delete     Whether to allow metadata deletion of the given type.
	 * @param int       $object_id  Object ID.
	 * @param string    $meta_key   Meta key.
	 * @param mixed     $meta_value Meta value. Must be serializable if non-scalar.
	 * @param bool      $delete_all Whether to delete the matching metadata entries
	 *                              for all objects, ignoring the specified $object_id.
	 *                              Default false.
	 */
	$check = apply_filters( "delete_{$meta_type}_metadata", null, $object_id, $meta_key, $meta_value, $delete_all );
	if ( null !== $check )
		return (bool) $check;

	$_meta_value = $meta_value;
	$meta_value = maybe_serialize( $meta_value );

	$query = $wpdb->prepare( "SELECT $id_column FROM $table WHERE meta_key = %s", $meta_key );

	if ( !$delete_all )
		$query .= $wpdb->prepare(" AND $type_column = %d", $object_id );

	if ( $meta_value )
		$query .= $wpdb->prepare(" AND meta_value = %s", $meta_value );

	$meta_ids = $wpdb->get_col( $query );
	if ( !count( $meta_ids ) )
		return false;

	if ( $delete_all )
		$object_ids = $wpdb->get_col( $wpdb->prepare( "SELECT $type_column FROM $table WHERE meta_key = %s", $meta_key ) );

	/**
	 * Fires immediately before deleting metadata of a specific type.
	 *
	 * The dynamic portion of the hook, `$meta_type`, refers to the meta
	 * object type (comment, post, or user).
	 *
	 * @since 3.1.0
	 *
	 * @param array  $meta_ids   An array of metadata entry IDs to delete.
	 * @param int    $object_id  Object ID.
	 * @param string $meta_key   Meta key.
	 * @param mixed  $meta_value Meta value.
	 */
	do_action( "delete_{$meta_type}_meta", $meta_ids, $object_id, $meta_key, $_meta_value );

	// Old-style action.
	if ( 'post' == $meta_type ) {
		/**
		 * Fires immediately before deleting metadata for a post.
		 *
		 * @since 2.9.0
		 *
		 * @param array $meta_ids An array of post metadata entry IDs to delete.
		 */
		do_action( 'delete_postmeta', $meta_ids );
	}

	$query = "DELETE FROM $table WHERE $id_column IN( " . implode( ',', $meta_ids ) . " )";

	$count = $wpdb->query($query);

	if ( !$count )
		return false;

	if ( $delete_all ) {
		foreach ( (array) $object_ids as $o_id ) {
			wp_cache_delete($o_id, $meta_type . '_meta');
		}
	} else {
		wp_cache_delete($object_id, $meta_type . '_meta');
	}

	/**
	 * Fires immediately after deleting metadata of a specific type.
	 *
	 * The dynamic portion of the hook name, `$meta_type`, refers to the meta
	 * object type (comment, post, or user).
	 *
	 * @since 2.9.0
	 *
	 * @param array  $meta_ids   An array of deleted metadata entry IDs.
	 * @param int    $object_id  Object ID.
	 * @param string $meta_key   Meta key.
	 * @param mixed  $meta_value Meta value.
	 */
	do_action( "deleted_{$meta_type}_meta", $meta_ids, $object_id, $meta_key, $_meta_value );

	// Old-style action.
	if ( 'post' == $meta_type ) {
		/**
		 * Fires immediately after deleting metadata for a post.
		 *
		 * @since 2.9.0
		 *
		 * @param array $meta_ids An array of deleted post metadata entry IDs.
		 */
		do_action( 'deleted_postmeta', $meta_ids );
	}

	return true;
}

/**
 * Retrieve metadata for the specified object.
 *
 * @since 2.9.0
 *
 * @param string $meta_type Type of object metadata is for (e.g., comment, post, or user)
 * @param int $object_id ID of the object metadata is for
 * @param string $meta_key Optional. Metadata key. If not specified, retrieve all metadata for
 * 		the specified object.
 * @param bool $single Optional, default is false. If true, return only the first value of the
 * 		specified meta_key. This parameter has no effect if meta_key is not specified.
 * @return string|array Single metadata value, or array of values
 */
function get_metadata($meta_type, $object_id, $meta_key = '', $single = false) {
	if ( ! $meta_type || ! is_numeric( $object_id ) ) {
		return false;
	}

	$object_id = absint( $object_id );
	if ( ! $object_id ) {
		return false;
	}

	/**
	 * Filter whether to retrieve metadata of a specific type.
	 *
	 * The dynamic portion of the hook, `$meta_type`, refers to the meta
	 * object type (comment, post, or user). Returning a non-null value
	 * will effectively short-circuit the function.
	 *
	 * @since 3.1.0
	 *
	 * @param null|array|string $value     The value get_metadata() should
	 *                                     return - a single metadata value,
	 *                                     or an array of values.
	 * @param int               $object_id Object ID.
	 * @param string            $meta_key  Meta key.
	 * @param string|array      $single    Meta value, or an array of values.
	 */
	$check = apply_filters( "get_{$meta_type}_metadata", null, $object_id, $meta_key, $single );
	if ( null !== $check ) {
		if ( $single && is_array( $check ) )
			return $check[0];
		else
			return $check;
	}

	$meta_cache = wp_cache_get($object_id, $meta_type . '_meta');

	if ( !$meta_cache ) {
		$meta_cache = update_meta_cache( $meta_type, array( $object_id ) );
		$meta_cache = $meta_cache[$object_id];
	}

	if ( ! $meta_key ) {
		return $meta_cache;
	}

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
 * Determine if a meta key is set for a given object
 *
 * @since 3.3.0
 *
 * @param string $meta_type Type of object metadata is for (e.g., comment, post, or user)
 * @param int $object_id ID of the object metadata is for
 * @param string $meta_key Metadata key.
 * @return boolean true of the key is set, false if not.
 */
function metadata_exists( $meta_type, $object_id, $meta_key ) {
	if ( ! $meta_type || ! is_numeric( $object_id ) ) {
		return false;
	}

	$object_id = absint( $object_id );
	if ( ! $object_id ) {
		return false;
	}

	/** This filter is documented in wp-includes/meta.php */
	$check = apply_filters( "get_{$meta_type}_metadata", null, $object_id, $meta_key, true );
	if ( null !== $check )
		return (bool) $check;

	$meta_cache = wp_cache_get( $object_id, $meta_type . '_meta' );

	if ( !$meta_cache ) {
		$meta_cache = update_meta_cache( $meta_type, array( $object_id ) );
		$meta_cache = $meta_cache[$object_id];
	}

	if ( isset( $meta_cache[ $meta_key ] ) )
		return true;

	return false;
}

/**
 * Get meta data by meta ID
 *
 * @since 3.3.0
 *
 * @param string $meta_type Type of object metadata is for (e.g., comment, post, or user)
 * @param int $meta_id ID for a specific meta row
 * @return object Meta object or false.
 */
function get_metadata_by_mid( $meta_type, $meta_id ) {
	global $wpdb;

	if ( ! $meta_type || ! is_numeric( $meta_id ) ) {
		return false;
	}

	$meta_id = absint( $meta_id );
	if ( ! $meta_id ) {
		return false;
	}

	$table = _get_meta_table( $meta_type );
	if ( ! $table ) {
		return false;
	}

	$id_column = ( 'user' == $meta_type ) ? 'umeta_id' : 'meta_id';

	$meta = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE $id_column = %d", $meta_id ) );

	if ( empty( $meta ) )
		return false;

	if ( isset( $meta->meta_value ) )
		$meta->meta_value = maybe_unserialize( $meta->meta_value );

	return $meta;
}

/**
 * Update meta data by meta ID
 *
 * @since 3.3.0
 *
 * @param string $meta_type Type of object metadata is for (e.g., comment, post, or user)
 * @param int $meta_id ID for a specific meta row
 * @param string $meta_value Metadata value
 * @param string $meta_key Optional, you can provide a meta key to update it
 * @return bool True on successful update, false on failure.
 */
function update_metadata_by_mid( $meta_type, $meta_id, $meta_value, $meta_key = false ) {
	global $wpdb;

	// Make sure everything is valid.
	if ( ! $meta_type || ! is_numeric( $meta_id ) ) {
		return false;
	}

	$meta_id = absint( $meta_id );
	if ( ! $meta_id ) {
		return false;
	}

	$table = _get_meta_table( $meta_type );
	if ( ! $table ) {
		return false;
	}

	$column = sanitize_key($meta_type . '_id');
	$id_column = 'user' == $meta_type ? 'umeta_id' : 'meta_id';

	// Fetch the meta and go on if it's found.
	if ( $meta = get_metadata_by_mid( $meta_type, $meta_id ) ) {
		$original_key = $meta->meta_key;
		$object_id = $meta->{$column};

		// If a new meta_key (last parameter) was specified, change the meta key,
		// otherwise use the original key in the update statement.
		if ( false === $meta_key ) {
			$meta_key = $original_key;
		} elseif ( ! is_string( $meta_key ) ) {
			return false;
		}

		// Sanitize the meta
		$_meta_value = $meta_value;
		$meta_value = sanitize_meta( $meta_key, $meta_value, $meta_type );
		$meta_value = maybe_serialize( $meta_value );

		// Format the data query arguments.
		$data = array(
			'meta_key' => $meta_key,
			'meta_value' => $meta_value
		);

		// Format the where query arguments.
		$where = array();
		$where[$id_column] = $meta_id;

		/** This action is documented in wp-includes/meta.php */
		do_action( "update_{$meta_type}_meta", $meta_id, $object_id, $meta_key, $_meta_value );

		if ( 'post' == $meta_type ) {
			/** This action is documented in wp-includes/meta.php */
			do_action( 'update_postmeta', $meta_id, $object_id, $meta_key, $meta_value );
		}

		// Run the update query, all fields in $data are %s, $where is a %d.
		$result = $wpdb->update( $table, $data, $where, '%s', '%d' );
		if ( ! $result )
			return false;

		// Clear the caches.
		wp_cache_delete($object_id, $meta_type . '_meta');

		/** This action is documented in wp-includes/meta.php */
		do_action( "updated_{$meta_type}_meta", $meta_id, $object_id, $meta_key, $_meta_value );

		if ( 'post' == $meta_type ) {
			/** This action is documented in wp-includes/meta.php */
			do_action( 'updated_postmeta', $meta_id, $object_id, $meta_key, $meta_value );
		}

		return true;
	}

	// And if the meta was not found.
	return false;
}

/**
 * Delete meta data by meta ID
 *
 * @since 3.3.0
 *
 * @param string $meta_type Type of object metadata is for (e.g., comment, post, or user)
 * @param int $meta_id ID for a specific meta row
 * @return bool True on successful delete, false on failure.
 */
function delete_metadata_by_mid( $meta_type, $meta_id ) {
	global $wpdb;

	// Make sure everything is valid.
	if ( ! $meta_type || ! is_numeric( $meta_id ) ) {
		return false;
	}

	$meta_id = absint( $meta_id );
	if ( ! $meta_id ) {
		return false;
	}

	$table = _get_meta_table( $meta_type );
	if ( ! $table ) {
		return false;
	}

	// object and id columns
	$column = sanitize_key($meta_type . '_id');
	$id_column = 'user' == $meta_type ? 'umeta_id' : 'meta_id';

	// Fetch the meta and go on if it's found.
	if ( $meta = get_metadata_by_mid( $meta_type, $meta_id ) ) {
		$object_id = $meta->{$column};

		/** This action is documented in wp-includes/meta.php */
		do_action( "delete_{$meta_type}_meta", (array) $meta_id, $object_id, $meta->meta_key, $meta->meta_value );

		// Old-style action.
		if ( 'post' == $meta_type || 'comment' == $meta_type ) {
			/**
			 * Fires immediately before deleting post or comment metadata of a specific type.
			 *
			 * The dynamic portion of the hook, `$meta_type`, refers to the meta
			 * object type (post or comment).
			 *
			 * @since 3.4.0
			 *
			 * @param int $meta_id ID of the metadata entry to delete.
			 */
			do_action( "delete_{$meta_type}meta", $meta_id );
		}

		// Run the query, will return true if deleted, false otherwise
		$result = (bool) $wpdb->delete( $table, array( $id_column => $meta_id ) );

		// Clear the caches.
		wp_cache_delete($object_id, $meta_type . '_meta');

		/** This action is documented in wp-includes/meta.php */
		do_action( "deleted_{$meta_type}_meta", (array) $meta_id, $object_id, $meta->meta_key, $meta->meta_value );

		// Old-style action.
		if ( 'post' == $meta_type || 'comment' == $meta_type ) {
			/**
			 * Fires immediately after deleting post or comment metadata of a specific type.
			 *
			 * The dynamic portion of the hook, `$meta_type`, refers to the meta
			 * object type (post or comment).
			 *
			 * @since 3.4.0
			 *
			 * @param int $meta_ids Deleted metadata entry ID.
			 */
			do_action( "deleted_{$meta_type}meta", $meta_id );
		}

		return $result;

	}

	// Meta id was not found.
	return false;
}

/**
 * Update the metadata cache for the specified objects.
 *
 * @since 2.9.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $meta_type Type of object metadata is for (e.g., comment, post, or user)
 * @param int|array $object_ids array or comma delimited list of object IDs to update cache for
 * @return mixed Metadata cache for the specified objects, or false on failure.
 */
function update_meta_cache($meta_type, $object_ids) {
	global $wpdb;

	if ( ! $meta_type || ! $object_ids ) {
		return false;
	}

	$table = _get_meta_table( $meta_type );
	if ( ! $table ) {
		return false;
	}

	$column = sanitize_key($meta_type . '_id');

	if ( !is_array($object_ids) ) {
		$object_ids = preg_replace('|[^0-9,]|', '', $object_ids);
		$object_ids = explode(',', $object_ids);
	}

	$object_ids = array_map('intval', $object_ids);

	$cache_key = $meta_type . '_meta';
	$ids = array();
	$cache = array();
	foreach ( $object_ids as $id ) {
		$cached_object = wp_cache_get( $id, $cache_key );
		if ( false === $cached_object )
			$ids[] = $id;
		else
			$cache[$id] = $cached_object;
	}

	if ( empty( $ids ) )
		return $cache;

	// Get meta info
	$id_list = join( ',', $ids );
	$id_column = 'user' == $meta_type ? 'umeta_id' : 'meta_id';
	$meta_list = $wpdb->get_results( "SELECT $column, meta_key, meta_value FROM $table WHERE $column IN ($id_list) ORDER BY $id_column ASC", ARRAY_A );

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
		wp_cache_add( $id, $cache[$id], $cache_key );
	}

	return $cache;
}

/**
 * Given a meta query, generates SQL clauses to be appended to a main query.
 *
 * @since 3.2.0
 *
 * @see WP_Meta_Query
 *
 * @param array $meta_query         A meta query.
 * @param string $type              Type of meta.
 * @param string $primary_table     Primary database table name.
 * @param string $primary_id_column Primary ID column name.
 * @param object $context           Optional. The main query object
 * @return array Associative array of `JOIN` and `WHERE` SQL.
 */
function get_meta_sql( $meta_query, $type, $primary_table, $primary_id_column, $context = null ) {
	$meta_query_obj = new WP_Meta_Query( $meta_query );
	return $meta_query_obj->get_sql( $type, $primary_table, $primary_id_column, $context );
}

/**
 * Class for generating SQL clauses that filter a primary query according to metadata keys and values.
 *
 * `WP_Meta_Query` is a helper that allows primary query classes, such as {@see WP_Query} and {@see WP_User_Query},
 * to filter their results by object metadata, by generating `JOIN` and `WHERE` subclauses to be attached
 * to the primary SQL query string.
 *
 * @since 3.2.0
 */
class WP_Meta_Query {
	/**
	 * Array of metadata queries.
	 *
	 * See {@see WP_Meta_Query::__construct()} for information on meta query arguments.
	 *
	 * @since 3.2.0
	 * @access public
	 * @var array
	 */
	public $queries = array();

	/**
	 * The relation between the queries. Can be one of 'AND' or 'OR'.
	 *
	 * @since 3.2.0
	 * @access public
	 * @var string
	 */
	public $relation;

	/**
	 * Database table to query for the metadata.
	 *
	 * @since 4.1.0
	 * @access public
	 * @var string
	 */
	public $meta_table;

	/**
	 * Column in meta_table that represents the ID of the object the metadata belongs to.
	 *
	 * @since 4.1.0
	 * @access public
	 * @var string
	 */
	public $meta_id_column;

	/**
	 * Database table that where the metadata's objects are stored (eg $wpdb->users).
	 *
	 * @since 4.1.0
	 * @access public
	 * @var string
	 */
	public $primary_table;

	/**
	 * Column in primary_table that represents the ID of the object.
	 *
	 * @since 4.1.0
	 * @access public
	 * @var string
	 */
	public $primary_id_column;

	/**
	 * A flat list of table aliases used in JOIN clauses.
	 *
	 * @since 4.1.0
	 * @access protected
	 * @var array
	 */
	protected $table_aliases = array();

	/**
	 * Constructor.
	 *
	 * @since 3.2.0
	 * @access public
	 *
	 * @param array $meta_query {
	 *     Array of meta query clauses.
	 *
	 *     @type string $relation Optional. The MySQL keyword used to join
	 *                            the clauses of the query. Accepts 'AND', or 'OR'. Default 'AND'.
	 *     @type array {
	 *         Optional. An array of first-order clause parameters, or another fully-formed meta query.
	 *
	 *         @type string $key     Meta key to filter by.
	 *         @type string $value   Meta value to filter by.
	 *         @type string $compare MySQL operator used for comparing the $value. Accepts '=',
	 *                               '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN',
	 *                               'BETWEEN', 'NOT BETWEEN', 'REGEXP', 'NOT REGEXP', or 'RLIKE'.
	 *                               Default is 'IN' when `$value` is an array, '=' otherwise.
	 *         @type string $type    MySQL data type that the meta_value column will be CAST to for
	 *                               comparisons. Accepts 'NUMERIC', 'BINARY', 'CHAR', 'DATE',
	 *                               'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', or 'UNSIGNED'.
	 *                               Default is 'CHAR'.
	 *     }
	 * }
	 */
	public function __construct( $meta_query = false ) {
		if ( !$meta_query )
			return;

		if ( isset( $meta_query['relation'] ) && strtoupper( $meta_query['relation'] ) == 'OR' ) {
			$this->relation = 'OR';
		} else {
			$this->relation = 'AND';
		}

		$this->queries = $this->sanitize_query( $meta_query );
	}

	/**
	 * Ensure the 'meta_query' argument passed to the class constructor is well-formed.
	 *
	 * Eliminates empty items and ensures that a 'relation' is set.
	 *
	 * @since 4.1.0
	 * @access public
	 *
	 * @param array $queries Array of query clauses.
	 * @return array Sanitized array of query clauses.
	 */
	public function sanitize_query( $queries ) {
		$clean_queries = array();

		if ( ! is_array( $queries ) ) {
			return $clean_queries;
		}

		foreach ( $queries as $key => $query ) {
			if ( 'relation' === $key ) {
				$relation = $query;

			} else if ( ! is_array( $query ) ) {
				continue;

			// First-order clause.
			} else if ( $this->is_first_order_clause( $query ) ) {
				if ( isset( $query['value'] ) && array() === $query['value'] ) {
					unset( $query['value'] );
				}

				$clean_queries[] = $query;

			// Otherwise, it's a nested query, so we recurse.
			} else {
				$cleaned_query = $this->sanitize_query( $query );

				if ( ! empty( $cleaned_query ) ) {
					$clean_queries[] = $cleaned_query;
				}
			}
		}

		if ( empty( $clean_queries ) ) {
			return $clean_queries;
		}

		// Sanitize the 'relation' key provided in the query.
		if ( isset( $relation ) && 'OR' === strtoupper( $relation ) ) {
			$clean_queries['relation'] = 'OR';

		/*
		 * If there is only a single clause, call the relation 'OR'.
		 * This value will not actually be used to join clauses, but it
		 * simplifies the logic around combining key-only queries.
		 */
		} else if ( 1 === count( $clean_queries ) ) {
			$clean_queries['relation'] = 'OR';

		// Default to AND.
		} else {
			$clean_queries['relation'] = 'AND';
		}

		return $clean_queries;
	}

	/**
	 * Determine whether a query clause is first-order.
	 *
	 * A first-order meta query clause is one that has either a 'key' or
	 * a 'value' array key.
	 *
	 * @since 4.1.0
	 * @access protected
	 *
	 * @param array $query Meta query arguments.
	 * @return bool Whether the query clause is a first-order clause.
	 */
	protected function is_first_order_clause( $query ) {
		return isset( $query['key'] ) || isset( $query['value'] );
	}

	/**
	 * Constructs a meta query based on 'meta_*' query vars
	 *
	 * @since 3.2.0
	 * @access public
	 *
	 * @param array $qv The query variables
	 */
	public function parse_query_vars( $qv ) {
		$meta_query = array();

		/*
		 * For orderby=meta_value to work correctly, simple query needs to be
		 * first (so that its table join is against an unaliased meta table) and
		 * needs to be its own clause (so it doesn't interfere with the logic of
		 * the rest of the meta_query).
		 */
		$primary_meta_query = array();
		foreach ( array( 'key', 'compare', 'type' ) as $key ) {
			if ( ! empty( $qv[ "meta_$key" ] ) ) {
				$primary_meta_query[ $key ] = $qv[ "meta_$key" ];
			}
		}

		// WP_Query sets 'meta_value' = '' by default.
		if ( isset( $qv['meta_value'] ) && '' !== $qv['meta_value'] && ( ! is_array( $qv['meta_value'] ) || $qv['meta_value'] ) ) {
			$primary_meta_query['value'] = $qv['meta_value'];
		}

		$existing_meta_query = isset( $qv['meta_query'] ) && is_array( $qv['meta_query'] ) ? $qv['meta_query'] : array();

		if ( ! empty( $primary_meta_query ) && ! empty( $existing_meta_query ) ) {
			$meta_query = array(
				'relation' => 'AND',
				$primary_meta_query,
				$existing_meta_query,
			);
		} else if ( ! empty( $primary_meta_query ) ) {
			$meta_query = array(
				$primary_meta_query,
			);
		} else if ( ! empty( $existing_meta_query ) ) {
			$meta_query = $existing_meta_query;
		}

		$this->__construct( $meta_query );
	}

	/**
	 * Return the appropriate alias for the given meta type if applicable.
	 *
	 * @since 3.7.0
	 * @access public
	 *
	 * @param string $type MySQL type to cast meta_value.
	 * @return string MySQL type.
	 */
	public function get_cast_for_type( $type = '' ) {
		if ( empty( $type ) )
			return 'CHAR';

		$meta_type = strtoupper( $type );

		if ( ! preg_match( '/^(?:BINARY|CHAR|DATE|DATETIME|SIGNED|UNSIGNED|TIME|NUMERIC(?:\(\d+(?:,\s?\d+)?\))?|DECIMAL(?:\(\d+(?:,\s?\d+)?\))?)$/', $meta_type ) )
			return 'CHAR';

		if ( 'NUMERIC' == $meta_type )
			$meta_type = 'SIGNED';

		return $meta_type;
	}

	/**
	 * Generates SQL clauses to be appended to a main query.
	 *
	 * @since 3.2.0
	 * @access public
	 *
	 * @param string $type              Type of meta, eg 'user', 'post'.
	 * @param string $primary_table     Database table where the object being filtered is stored (eg wp_users).
	 * @param string $primary_id_column ID column for the filtered object in $primary_table.
	 * @param object $context           Optional. The main query object.
	 * @return array {
	 *     Array containing JOIN and WHERE SQL clauses to append to the main query.
	 *
	 *     @type string $join  SQL fragment to append to the main JOIN clause.
	 *     @type string $where SQL fragment to append to the main WHERE clause.
	 * }
	 */
	public function get_sql( $type, $primary_table, $primary_id_column, $context = null ) {
		global $wpdb;

		if ( ! $meta_table = _get_meta_table( $type ) ) {
			return false;
		}

		$this->meta_table     = $meta_table;
		$this->meta_id_column = sanitize_key( $type . '_id' );

		$this->primary_table     = $primary_table;
		$this->primary_id_column = $primary_id_column;

		$sql = $this->get_sql_clauses();

		/*
		 * If any JOINs are LEFT JOINs (as in the case of NOT EXISTS), then all JOINs should
		 * be LEFT. Otherwise posts with no metadata will be excluded from results.
		 */
		if ( false !== strpos( $sql['join'], 'LEFT JOIN' ) ) {
			$sql['join'] = str_replace( 'INNER JOIN', 'LEFT JOIN', $sql['join'] );
		}

		/**
		 * Filter the meta query's generated SQL.
		 *
		 * @since 3.1.0
		 *
		 * @param array $args {
		 *     An array of meta query SQL arguments.
		 *
		 *     @type array  $clauses           Array containing the query's JOIN and WHERE clauses.
		 *     @type array  $queries           Array of meta queries.
		 *     @type string $type              Type of meta.
		 *     @type string $primary_table     Primary table.
		 *     @type string $primary_id_column Primary column ID.
		 *     @type object $context           The main query object.
		 * }
		 */
		return apply_filters_ref_array( 'get_meta_sql', array( $sql, $this->queries, $type, $primary_table, $primary_id_column, $context ) );
	}

	/**
	 * Generate SQL clauses to be appended to a main query.
	 *
	 * Called by the public {@see WP_Meta_Query::get_sql()}, this method
	 * is abstracted out to maintain parity with the other Query classes.
	 *
	 * @since 4.1.0
	 * @access protected
	 *
	 * @return array {
	 *     Array containing JOIN and WHERE SQL clauses to append to the main query.
	 *
	 *     @type string $join  SQL fragment to append to the main JOIN clause.
	 *     @type string $where SQL fragment to append to the main WHERE clause.
	 * }
	 */
	protected function get_sql_clauses() {
		/*
		 * $queries are passed by reference to get_sql_for_query() for recursion.
		 * To keep $this->queries unaltered, pass a copy.
		 */
		$queries = $this->queries;
		$sql = $this->get_sql_for_query( $queries );

		if ( ! empty( $sql['where'] ) ) {
			$sql['where'] = ' AND ' . $sql['where'];
		}

		return $sql;
	}

	/**
	 * Generate SQL clauses for a single query array.
	 *
	 * If nested subqueries are found, this method recurses the tree to
	 * produce the properly nested SQL.
	 *
	 * @since 4.1.0
	 * @access protected
	 *
	 * @param array $query Query to parse, passed by reference.
	 * @param int   $depth Optional. Number of tree levels deep we currently are.
	 *                     Used to calculate indentation. Default 0.
	 * @return array {
	 *     Array containing JOIN and WHERE SQL clauses to append to a single query array.
	 *
	 *     @type string $join  SQL fragment to append to the main JOIN clause.
	 *     @type string $where SQL fragment to append to the main WHERE clause.
	 * }
	 */
	protected function get_sql_for_query( &$query, $depth = 0 ) {
		$sql_chunks = array(
			'join'  => array(),
			'where' => array(),
		);

		$sql = array(
			'join'  => '',
			'where' => '',
		);

		$indent = '';
		for ( $i = 0; $i < $depth; $i++ ) {
			$indent .= "  ";
		}

		foreach ( $query as $key => &$clause ) {
			if ( 'relation' === $key ) {
				$relation = $query['relation'];
			} else if ( is_array( $clause ) ) {

				// This is a first-order clause.
				if ( $this->is_first_order_clause( $clause ) ) {
					$clause_sql = $this->get_sql_for_clause( $clause, $query );

					$where_count = count( $clause_sql['where'] );
					if ( ! $where_count ) {
						$sql_chunks['where'][] = '';
					} else if ( 1 === $where_count ) {
						$sql_chunks['where'][] = $clause_sql['where'][0];
					} else {
						$sql_chunks['where'][] = '( ' . implode( ' AND ', $clause_sql['where'] ) . ' )';
					}

					$sql_chunks['join'] = array_merge( $sql_chunks['join'], $clause_sql['join'] );
				// This is a subquery, so we recurse.
				} else {
					$clause_sql = $this->get_sql_for_query( $clause, $depth + 1 );

					$sql_chunks['where'][] = $clause_sql['where'];
					$sql_chunks['join'][]  = $clause_sql['join'];
				}
			}
		}

		// Filter to remove empties.
		$sql_chunks['join']  = array_filter( $sql_chunks['join'] );
		$sql_chunks['where'] = array_filter( $sql_chunks['where'] );

		if ( empty( $relation ) ) {
			$relation = 'AND';
		}

		// Filter duplicate JOIN clauses and combine into a single string.
		if ( ! empty( $sql_chunks['join'] ) ) {
			$sql['join'] = implode( ' ', array_unique( $sql_chunks['join'] ) );
		}

		// Generate a single WHERE clause with proper brackets and indentation.
		if ( ! empty( $sql_chunks['where'] ) ) {
			$sql['where'] = '( ' . "\n  " . $indent . implode( ' ' . "\n  " . $indent . $relation . ' ' . "\n  " . $indent, $sql_chunks['where'] ) . "\n" . $indent . ')';
		}

		return $sql;
	}

	/**
	 * Generate SQL JOIN and WHERE clauses for a first-order query clause.
	 *
	 * "First-order" means that it's an array with a 'key' or 'value'.
	 *
	 * @since 4.1.0
	 * @access public
	 *
	 * @param array $clause       Query clause, passed by reference.
	 * @param array $parent_query Parent query array.
	 * @return array {
	 *     Array containing JOIN and WHERE SQL clauses to append to a first-order query.
	 *
	 *     @type string $join  SQL fragment to append to the main JOIN clause.
	 *     @type string $where SQL fragment to append to the main WHERE clause.
	 * }
	 */
	public function get_sql_for_clause( &$clause, $parent_query ) {
		global $wpdb;

		$sql_chunks = array(
			'where' => array(),
			'join' => array(),
		);

		if ( isset( $clause['compare'] ) ) {
			$clause['compare'] = strtoupper( $clause['compare'] );
		} else {
			$clause['compare'] = isset( $clause['value'] ) && is_array( $clause['value'] ) ? 'IN' : '=';
		}

		if ( ! in_array( $clause['compare'], array(
			'=', '!=', '>', '>=', '<', '<=',
			'LIKE', 'NOT LIKE',
			'IN', 'NOT IN',
			'BETWEEN', 'NOT BETWEEN',
			'EXISTS', 'NOT EXISTS',
			'REGEXP', 'NOT REGEXP', 'RLIKE'
		) ) ) {
			$clause['compare'] = '=';
		}

		$meta_compare = $clause['compare'];

		// First build the JOIN clause, if one is required.
		$join = '';

		// We prefer to avoid joins if possible. Look for an existing join compatible with this clause.
		$alias = $this->find_compatible_table_alias( $clause, $parent_query );
		if ( false === $alias ) {
			$i = count( $this->table_aliases );
			$alias = $i ? 'mt' . $i : $this->meta_table;

			// JOIN clauses for NOT EXISTS have their own syntax.
			if ( 'NOT EXISTS' === $meta_compare ) {
				$join .= " LEFT JOIN $this->meta_table";
				$join .= $i ? " AS $alias" : '';
				$join .= $wpdb->prepare( " ON ($this->primary_table.$this->primary_id_column = $alias.$this->meta_id_column AND $alias.meta_key = %s )", $clause['key'] );

			// All other JOIN clauses.
			} else {
				$join .= " INNER JOIN $this->meta_table";
				$join .= $i ? " AS $alias" : '';
				$join .= " ON ( $this->primary_table.$this->primary_id_column = $alias.$this->meta_id_column )";
			}

			$this->table_aliases[] = $alias;
			$sql_chunks['join'][] = $join;
		}

		// Save the alias to this clause, for future siblings to find.
		$clause['alias'] = $alias;

		// Next, build the WHERE clause.

		// meta_key.
		if ( array_key_exists( 'key', $clause ) ) {
			if ( 'NOT EXISTS' === $meta_compare ) {
				$sql_chunks['where'][] = $alias . '.' . $this->meta_id_column . ' IS NULL';
			} else {
				$sql_chunks['where'][] = $wpdb->prepare( "$alias.meta_key = %s", trim( $clause['key'] ) );
			}
		}

		// meta_value.
		if ( array_key_exists( 'value', $clause ) ) {
			$meta_value = $clause['value'];
			$meta_type = $this->get_cast_for_type( isset( $clause['type'] ) ? $clause['type'] : '' );

			if ( in_array( $meta_compare, array( 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' ) ) ) {
				if ( ! is_array( $meta_value ) ) {
					$meta_value = preg_split( '/[,\s]+/', $meta_value );
				}
			} else {
				$meta_value = trim( $meta_value );
			}

			switch ( $meta_compare ) {
				case 'IN' :
				case 'NOT IN' :
					$meta_compare_string = '(' . substr( str_repeat( ',%s', count( $meta_value ) ), 1 ) . ')';
					$where = $wpdb->prepare( $meta_compare_string, $meta_value );
					break;

				case 'BETWEEN' :
				case 'NOT BETWEEN' :
					$meta_value = array_slice( $meta_value, 0, 2 );
					$where = $wpdb->prepare( '%s AND %s', $meta_value );
					break;

				case 'LIKE' :
				case 'NOT LIKE' :
					$meta_value = '%' . $wpdb->esc_like( $meta_value ) . '%';
					$where = $wpdb->prepare( '%s', $meta_value );
					break;

				// EXISTS with a value is interpreted as '='.
				case 'EXISTS' :
					$meta_compare = '=';
					$where = $wpdb->prepare( '%s', $meta_value );
					break;

				// 'value' is ignored for NOT EXISTS.
				case 'NOT EXISTS' :
					$where = '';
					break;

				default :
					$where = $wpdb->prepare( '%s', $meta_value );
					break;

			}

			if ( $where ) {
				$sql_chunks['where'][] = "CAST($alias.meta_value AS {$meta_type}) {$meta_compare} {$where}";
			}
		}

		/*
		 * Multiple WHERE clauses (for meta_key and meta_value) should
		 * be joined in parentheses.
		 */
		if ( 1 < count( $sql_chunks['where'] ) ) {
			$sql_chunks['where'] = array( '( ' . implode( ' AND ', $sql_chunks['where'] ) . ' )' );
		}

		return $sql_chunks;
	}

	/**
	 * Identify an existing table alias that is compatible with the current
	 * query clause.
	 *
	 * We avoid unnecessary table joins by allowing each clause to look for
	 * an existing table alias that is compatible with the query that it
	 * needs to perform.
	 *
	 * An existing alias is compatible if (a) it is a sibling of `$clause`
	 * (ie, it's under the scope of the same relation), and (b) the combination
	 * of operator and relation between the clauses allows for a shared table join.
	 * In the case of {@see WP_Meta_Query}, this only applies to 'IN' clauses that
	 * are connected by the relation 'OR'.
	 *
	 * @since 4.1.0
	 * @access protected
	 *
	 * @param  array       $clause       Query clause.
	 * @param  array       $parent_query Parent query of $clause.
	 * @return string|bool Table alias if found, otherwise false.
	 */
	protected function find_compatible_table_alias( $clause, $parent_query ) {
		$alias = false;

		foreach ( $parent_query as $sibling ) {
			// If the sibling has no alias yet, there's nothing to check.
			if ( empty( $sibling['alias'] ) ) {
				continue;
			}

			// We're only interested in siblings that are first-order clauses.
			if ( ! is_array( $sibling ) || ! $this->is_first_order_clause( $sibling ) ) {
				continue;
			}

			$compatible_compares = array();

			// Clauses connected by OR can share joins as long as they have "positive" operators.
			if ( 'OR' === $parent_query['relation'] ) {
				$compatible_compares = array( '=', 'IN', 'BETWEEN', 'LIKE', 'REGEXP', 'RLIKE', '>', '>=', '<', '<=' );

			// Clauses joined by AND with "negative" operators share a join only if they also share a key.
			} else if ( isset( $sibling['key'] ) && isset( $clause['key'] ) && $sibling['key'] === $clause['key'] ) {
				$compatible_compares = array( '!=', 'NOT IN', 'NOT LIKE' );
			}

			$clause_compare  = strtoupper( $clause['compare'] );
			$sibling_compare = strtoupper( $sibling['compare'] );
			if ( in_array( $clause_compare, $compatible_compares ) && in_array( $sibling_compare, $compatible_compares ) ) {
				$alias = $sibling['alias'];
				break;
			}
		}

		/**
		 * Filter the table alias identified as compatible with the current clause.
		 *
		 * @since 4.1.0
		 *
		 * @param string|bool $alias        Table alias, or false if none was found.
		 * @param array       $clause       First-order query clause.
		 * @param array       $parent_query Parent of $clause.
		 * @param object      $this         WP_Meta_Query object.
		 */
		return apply_filters( 'meta_query_find_compatible_table_alias', $alias, $clause, $parent_query, $this ) ;
	}
}

/**
 * Retrieve the name of the metadata table for the specified object type.
 *
 * @since 2.9.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $type Type of object to get metadata table for (e.g., comment, post, or user)
 * @return mixed Metadata table name, or false if no metadata table exists
 */
function _get_meta_table($type) {
	global $wpdb;

	$table_name = $type . 'meta';

	if ( empty($wpdb->$table_name) )
		return false;

	return $wpdb->$table_name;
}

/**
 * Determine whether a meta key is protected.
 *
 * @since 3.1.3
 *
 * @param string      $meta_key Meta key
 * @param string|null $meta_type
 * @return bool True if the key is protected, false otherwise.
 */
function is_protected_meta( $meta_key, $meta_type = '' ) {
	$sanitized_key = preg_replace( "/[^\x20-\x7E\p{L}]/", '', $meta_key );
	$protected     = strlen( $sanitized_key ) > 0 && ( '_' === $sanitized_key[0] );

	/**
	 * Filter whether a meta key is protected.
	 *
	 * @since 3.2.0
	 *
	 * @param bool   $protected Whether the key is protected. Default false.
	 * @param string $meta_key  Meta key.
	 * @param string $meta_type Meta type.
	 */
	return apply_filters( 'is_protected_meta', $protected, $meta_key, $meta_type );
}

/**
 * Sanitize meta value.
 *
 * @since 3.1.3
 *
 * @param string $meta_key Meta key
 * @param mixed $meta_value Meta value to sanitize
 * @param string $meta_type Type of meta
 * @return mixed Sanitized $meta_value
 */
function sanitize_meta( $meta_key, $meta_value, $meta_type ) {

	/**
	 * Filter the sanitization of a specific meta key of a specific meta type.
	 *
	 * The dynamic portions of the hook name, `$meta_type`, and `$meta_key`,
	 * refer to the metadata object type (comment, post, or user) and the meta
	 * key value,
	 * respectively.
	 *
	 * @since 3.3.0
	 *
	 * @param mixed  $meta_value Meta value to sanitize.
	 * @param string $meta_key   Meta key.
	 * @param string $meta_type  Meta type.
	 */
	return apply_filters( "sanitize_{$meta_type}_meta_{$meta_key}", $meta_value, $meta_key, $meta_type );
}

/**
 * Register meta key
 *
 * @since 3.3.0
 *
 * @param string $meta_type Type of meta
 * @param string $meta_key Meta key
 * @param string|array $sanitize_callback A function or method to call when sanitizing the value of $meta_key.
 * @param string|array $auth_callback Optional. A function or method to call when performing edit_post_meta, add_post_meta, and delete_post_meta capability checks.
 */
function register_meta( $meta_type, $meta_key, $sanitize_callback, $auth_callback = null ) {
	if ( is_callable( $sanitize_callback ) )
		add_filter( "sanitize_{$meta_type}_meta_{$meta_key}", $sanitize_callback, 10, 3 );

	if ( empty( $auth_callback ) ) {
		if ( is_protected_meta( $meta_key, $meta_type ) )
			$auth_callback = '__return_false';
		else
			$auth_callback = '__return_true';
	}

	if ( is_callable( $auth_callback ) )
		add_filter( "auth_{$meta_type}_meta_{$meta_key}", $auth_callback, 10, 6 );
}
