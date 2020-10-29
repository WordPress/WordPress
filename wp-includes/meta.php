<?php
/**
 * Core Metadata API
 *
 * Functions for retrieving and manipulating metadata of various WordPress object types. Metadata
 * for an object is a represented by a simple key-value pair. Objects may contain multiple
 * metadata entries that share the same key and differ only in their value.
 *
 * @package WordPress
 * @subpackage Meta
 */

/**
 * Add metadata for the specified object.
 *
 * @since 2.9.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $meta_type  Type of object metadata is for (e.g., comment, post, term, or user).
 * @param int    $object_id  ID of the object metadata is for
 * @param string $meta_key   Metadata key
 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
 * @param bool   $unique     Optional, default is false.
 *                           Whether the specified metadata key should be unique for the object.
 *                           If true, and the object already has a value for the specified metadata key,
 *                           no change will be made.
 * @return int|false The meta ID on success, false on failure.
 */
function add_metadata( $meta_type, $object_id, $meta_key, $meta_value, $unique = false ) {
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

	$meta_subtype = get_object_subtype( $meta_type, $object_id );

	$column = sanitize_key( $meta_type . '_id' );

	// expected_slashed ($meta_key)
	$meta_key   = wp_unslash( $meta_key );
	$meta_value = wp_unslash( $meta_value );
	$meta_value = sanitize_meta( $meta_key, $meta_value, $meta_type, $meta_subtype );

	/**
	 * Filters whether to add metadata of a specific type.
	 *
	 * The dynamic portion of the hook, `$meta_type`, refers to the meta
	 * object type (comment, post, term, or user). Returning a non-null value
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
	if ( null !== $check ) {
		return $check;
	}

	if ( $unique && $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*) FROM $table WHERE meta_key = %s AND $column = %d",
			$meta_key,
			$object_id
		)
	) ) {
		return false;
	}

	$_meta_value = $meta_value;
	$meta_value  = maybe_serialize( $meta_value );

	/**
	 * Fires immediately before meta of a specific type is added.
	 *
	 * The dynamic portion of the hook, `$meta_type`, refers to the meta
	 * object type (comment, post, term, or user).
	 *
	 * @since 3.1.0
	 *
	 * @param int    $object_id   Object ID.
	 * @param string $meta_key    Meta key.
	 * @param mixed  $_meta_value Meta value.
	 */
	do_action( "add_{$meta_type}_meta", $object_id, $meta_key, $_meta_value );

	$result = $wpdb->insert(
		$table,
		array(
			$column      => $object_id,
			'meta_key'   => $meta_key,
			'meta_value' => $meta_value,
		)
	);

	if ( ! $result ) {
		return false;
	}

	$mid = (int) $wpdb->insert_id;

	wp_cache_delete( $object_id, $meta_type . '_meta' );

	/**
	 * Fires immediately after meta of a specific type is added.
	 *
	 * The dynamic portion of the hook, `$meta_type`, refers to the meta
	 * object type (comment, post, term, or user).
	 *
	 * @since 2.9.0
	 *
	 * @param int    $mid         The meta ID after successful update.
	 * @param int    $object_id   Object ID.
	 * @param string $meta_key    Meta key.
	 * @param mixed  $_meta_value Meta value.
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
 * @param string $meta_type  Type of object metadata is for (e.g., comment, post, term, or user).
 * @param int    $object_id  ID of the object metadata is for
 * @param string $meta_key   Metadata key
 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
 * @param mixed  $prev_value Optional. If specified, only update existing metadata entries with
 *                           the specified value. Otherwise, update all entries.
 * @return int|bool The new meta field ID if a field with the given key didn't exist and was
 *                  therefore added, true on successful update, false on failure.
 */
function update_metadata( $meta_type, $object_id, $meta_key, $meta_value, $prev_value = '' ) {
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

	$meta_subtype = get_object_subtype( $meta_type, $object_id );

	$column    = sanitize_key( $meta_type . '_id' );
	$id_column = 'user' == $meta_type ? 'umeta_id' : 'meta_id';

	// expected_slashed ($meta_key)
	$raw_meta_key = $meta_key;
	$meta_key     = wp_unslash( $meta_key );
	$passed_value = $meta_value;
	$meta_value   = wp_unslash( $meta_value );
	$meta_value   = sanitize_meta( $meta_key, $meta_value, $meta_type, $meta_subtype );

	/**
	 * Filters whether to update metadata of a specific type.
	 *
	 * The dynamic portion of the hook, `$meta_type`, refers to the meta
	 * object type (comment, post, term, or user). Returning a non-null value
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
	if ( null !== $check ) {
		return (bool) $check;
	}

	// Compare existing value to new value if no prev value given and the key exists only once.
	if ( empty( $prev_value ) ) {
		$old_value = get_metadata( $meta_type, $object_id, $meta_key );
		if ( count( $old_value ) == 1 ) {
			if ( $old_value[0] === $meta_value ) {
				return false;
			}
		}
	}

	$meta_ids = $wpdb->get_col( $wpdb->prepare( "SELECT $id_column FROM $table WHERE meta_key = %s AND $column = %d", $meta_key, $object_id ) );
	if ( empty( $meta_ids ) ) {
		return add_metadata( $meta_type, $object_id, $raw_meta_key, $passed_value );
	}

	$_meta_value = $meta_value;
	$meta_value  = maybe_serialize( $meta_value );

	$data  = compact( 'meta_value' );
	$where = array(
		$column    => $object_id,
		'meta_key' => $meta_key,
	);

	if ( ! empty( $prev_value ) ) {
		$prev_value          = maybe_serialize( $prev_value );
		$where['meta_value'] = $prev_value;
	}

	foreach ( $meta_ids as $meta_id ) {
		/**
		 * Fires immediately before updating metadata of a specific type.
		 *
		 * The dynamic portion of the hook, `$meta_type`, refers to the meta
		 * object type (comment, post, term, or user).
		 *
		 * @since 2.9.0
		 *
		 * @param int    $meta_id     ID of the metadata entry to update.
		 * @param int    $object_id   Object ID.
		 * @param string $meta_key    Meta key.
		 * @param mixed  $_meta_value Meta value.
		 */
		do_action( "update_{$meta_type}_meta", $meta_id, $object_id, $meta_key, $_meta_value );

		if ( 'post' == $meta_type ) {
			/**
			 * Fires immediately before updating a post's metadata.
			 *
			 * @since 2.9.0
			 *
			 * @param int    $meta_id    ID of metadata entry to update.
			 * @param int    $object_id  Post ID.
			 * @param string $meta_key   Meta key.
			 * @param mixed  $meta_value Meta value. This will be a PHP-serialized string representation of the value if
			 *                           the value is an array, an object, or itself a PHP-serialized string.
			 */
			do_action( 'update_postmeta', $meta_id, $object_id, $meta_key, $meta_value );
		}
	}

	$result = $wpdb->update( $table, $data, $where );
	if ( ! $result ) {
		return false;
	}

	wp_cache_delete( $object_id, $meta_type . '_meta' );

	foreach ( $meta_ids as $meta_id ) {
		/**
		 * Fires immediately after updating metadata of a specific type.
		 *
		 * The dynamic portion of the hook, `$meta_type`, refers to the meta
		 * object type (comment, post, term, or user).
		 *
		 * @since 2.9.0
		 *
		 * @param int    $meta_id     ID of updated metadata entry.
		 * @param int    $object_id   Object ID.
		 * @param string $meta_key    Meta key.
		 * @param mixed  $_meta_value Meta value.
		 */
		do_action( "updated_{$meta_type}_meta", $meta_id, $object_id, $meta_key, $_meta_value );

		if ( 'post' == $meta_type ) {
			/**
			 * Fires immediately after updating a post's metadata.
			 *
			 * @since 2.9.0
			 *
			 * @param int    $meta_id    ID of updated metadata entry.
			 * @param int    $object_id  Post ID.
			 * @param string $meta_key   Meta key.
			 * @param mixed  $meta_value Meta value. This will be a PHP-serialized string representation of the value if
			 *                           the value is an array, an object, or itself a PHP-serialized string.
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
 * @param string $meta_type  Type of object metadata is for (e.g., comment, post, term, or user).
 * @param int    $object_id  ID of the object metadata is for
 * @param string $meta_key   Metadata key
 * @param mixed  $meta_value Optional. Metadata value. Must be serializable if non-scalar. If specified, only delete
 *                           metadata entries with this value. Otherwise, delete all entries with the specified meta_key.
 *                           Pass `null`, `false`, or an empty string to skip this check. (For backward compatibility,
 *                           it is not possible to pass an empty string to delete those entries with an empty string
 *                           for a value.)
 * @param bool   $delete_all Optional, default is false. If true, delete matching metadata entries for all objects,
 *                           ignoring the specified object_id. Otherwise, only delete matching metadata entries for
 *                           the specified object_id.
 * @return bool True on successful delete, false on failure.
 */
function delete_metadata( $meta_type, $object_id, $meta_key, $meta_value = '', $delete_all = false ) {
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

	$type_column = sanitize_key( $meta_type . '_id' );
	$id_column   = 'user' == $meta_type ? 'umeta_id' : 'meta_id';
	// expected_slashed ($meta_key)
	$meta_key   = wp_unslash( $meta_key );
	$meta_value = wp_unslash( $meta_value );

	/**
	 * Filters whether to delete metadata of a specific type.
	 *
	 * The dynamic portion of the hook, `$meta_type`, refers to the meta
	 * object type (comment, post, term, or user). Returning a non-null value
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
	if ( null !== $check ) {
		return (bool) $check;
	}

	$_meta_value = $meta_value;
	$meta_value  = maybe_serialize( $meta_value );

	$query = $wpdb->prepare( "SELECT $id_column FROM $table WHERE meta_key = %s", $meta_key );

	if ( ! $delete_all ) {
		$query .= $wpdb->prepare( " AND $type_column = %d", $object_id );
	}

	if ( '' !== $meta_value && null !== $meta_value && false !== $meta_value ) {
		$query .= $wpdb->prepare( ' AND meta_value = %s', $meta_value );
	}

	$meta_ids = $wpdb->get_col( $query );
	if ( ! count( $meta_ids ) ) {
		return false;
	}

	if ( $delete_all ) {
		if ( '' !== $meta_value && null !== $meta_value && false !== $meta_value ) {
			$object_ids = $wpdb->get_col( $wpdb->prepare( "SELECT $type_column FROM $table WHERE meta_key = %s AND meta_value = %s", $meta_key, $meta_value ) );
		} else {
			$object_ids = $wpdb->get_col( $wpdb->prepare( "SELECT $type_column FROM $table WHERE meta_key = %s", $meta_key ) );
		}
	}

	/**
	 * Fires immediately before deleting metadata of a specific type.
	 *
	 * The dynamic portion of the hook, `$meta_type`, refers to the meta
	 * object type (comment, post, term, or user).
	 *
	 * @since 3.1.0
	 *
	 * @param array  $meta_ids    An array of metadata entry IDs to delete.
	 * @param int    $object_id   Object ID.
	 * @param string $meta_key    Meta key.
	 * @param mixed  $_meta_value Meta value.
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

	$query = "DELETE FROM $table WHERE $id_column IN( " . implode( ',', $meta_ids ) . ' )';

	$count = $wpdb->query( $query );

	if ( ! $count ) {
		return false;
	}

	if ( $delete_all ) {
		foreach ( (array) $object_ids as $o_id ) {
			wp_cache_delete( $o_id, $meta_type . '_meta' );
		}
	} else {
		wp_cache_delete( $object_id, $meta_type . '_meta' );
	}

	/**
	 * Fires immediately after deleting metadata of a specific type.
	 *
	 * The dynamic portion of the hook name, `$meta_type`, refers to the meta
	 * object type (comment, post, term, or user).
	 *
	 * @since 2.9.0
	 *
	 * @param array  $meta_ids    An array of deleted metadata entry IDs.
	 * @param int    $object_id   Object ID.
	 * @param string $meta_key    Meta key.
	 * @param mixed  $_meta_value Meta value.
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
 * @param string $meta_type Type of object metadata is for (e.g., comment, post, term, or user).
 * @param int    $object_id ID of the object metadata is for
 * @param string $meta_key  Optional. Metadata key. If not specified, retrieve all metadata for
 *                          the specified object.
 * @param bool   $single    Optional, default is false.
 *                          If true, return only the first value of the specified meta_key.
 *                          This parameter has no effect if meta_key is not specified.
 * @return mixed Single metadata value, or array of values
 */
function get_metadata( $meta_type, $object_id, $meta_key = '', $single = false ) {
	if ( ! $meta_type || ! is_numeric( $object_id ) ) {
		return false;
	}

	$object_id = absint( $object_id );
	if ( ! $object_id ) {
		return false;
	}

	/**
	 * Filters whether to retrieve metadata of a specific type.
	 *
	 * The dynamic portion of the hook, `$meta_type`, refers to the meta
	 * object type (comment, post, term, or user). Returning a non-null value
	 * will effectively short-circuit the function.
	 *
	 * @since 3.1.0
	 *
	 * @param null|array|string $value     The value get_metadata() should return - a single metadata value,
	 *                                     or an array of values.
	 * @param int               $object_id Object ID.
	 * @param string            $meta_key  Meta key.
	 * @param bool              $single    Whether to return only the first value of the specified $meta_key.
	 */
	$check = apply_filters( "get_{$meta_type}_metadata", null, $object_id, $meta_key, $single );
	if ( null !== $check ) {
		if ( $single && is_array( $check ) ) {
			return $check[0];
		} else {
			return $check;
		}
	}

	$meta_cache = wp_cache_get( $object_id, $meta_type . '_meta' );

	if ( ! $meta_cache ) {
		$meta_cache = update_meta_cache( $meta_type, array( $object_id ) );
		if ( isset( $meta_cache[ $object_id ] ) ) {
			$meta_cache = $meta_cache[ $object_id ];
		} else {
			$meta_cache = null;
		}
	}

	if ( ! $meta_key ) {
		return $meta_cache;
	}

	if ( isset( $meta_cache[ $meta_key ] ) ) {
		if ( $single ) {
			return maybe_unserialize( $meta_cache[ $meta_key ][0] );
		} else {
			return array_map( 'maybe_unserialize', $meta_cache[ $meta_key ] );
		}
	}

	if ( $single ) {
		return '';
	} else {
		return array();
	}
}

/**
 * Determine if a meta key is set for a given object
 *
 * @since 3.3.0
 *
 * @param string $meta_type Type of object metadata is for (e.g., comment, post, term, or user).
 * @param int    $object_id ID of the object metadata is for
 * @param string $meta_key  Metadata key.
 * @return bool True of the key is set, false if not.
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
	if ( null !== $check ) {
		return (bool) $check;
	}

	$meta_cache = wp_cache_get( $object_id, $meta_type . '_meta' );

	if ( ! $meta_cache ) {
		$meta_cache = update_meta_cache( $meta_type, array( $object_id ) );
		$meta_cache = $meta_cache[ $object_id ];
	}

	if ( isset( $meta_cache[ $meta_key ] ) ) {
		return true;
	}

	return false;
}

/**
 * Get meta data by meta ID
 *
 * @since 3.3.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $meta_type Type of object metadata is for (e.g., comment, post, term, or user).
 * @param int    $meta_id   ID for a specific meta row
 * @return object|false Meta object or false.
 */
function get_metadata_by_mid( $meta_type, $meta_id ) {
	global $wpdb;

	if ( ! $meta_type || ! is_numeric( $meta_id ) || floor( $meta_id ) != $meta_id ) {
		return false;
	}

	$meta_id = intval( $meta_id );
	if ( $meta_id <= 0 ) {
		return false;
	}

	$table = _get_meta_table( $meta_type );
	if ( ! $table ) {
		return false;
	}

	$id_column = ( 'user' == $meta_type ) ? 'umeta_id' : 'meta_id';

	/**
	 * Filters whether to retrieve metadata of a specific type by meta ID.
	 *
	 * The dynamic portion of the hook, `$meta_type`, refers to the meta
	 * object type (comment, post, term, or user). Returning a non-null value
	 * will effectively short-circuit the function.
	 *
	 * @since 5.0.0
	 *
	 * @param mixed $value    The value get_metadata_by_mid() should return.
	 * @param int   $meta_id  Meta ID.
	 */
	$check = apply_filters( "get_{$meta_type}_metadata_by_mid", null, $meta_id );
	if ( null !== $check ) {
		return $check;
	}

	$meta = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE $id_column = %d", $meta_id ) );

	if ( empty( $meta ) ) {
		return false;
	}

	if ( isset( $meta->meta_value ) ) {
		$meta->meta_value = maybe_unserialize( $meta->meta_value );
	}

	return $meta;
}

/**
 * Update meta data by meta ID
 *
 * @since 3.3.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $meta_type  Type of object metadata is for (e.g., comment, post, term, or user).
 * @param int    $meta_id    ID for a specific meta row
 * @param string $meta_value Metadata value
 * @param string $meta_key   Optional, you can provide a meta key to update it
 * @return bool True on successful update, false on failure.
 */
function update_metadata_by_mid( $meta_type, $meta_id, $meta_value, $meta_key = false ) {
	global $wpdb;

	// Make sure everything is valid.
	if ( ! $meta_type || ! is_numeric( $meta_id ) || floor( $meta_id ) != $meta_id ) {
		return false;
	}

	$meta_id = intval( $meta_id );
	if ( $meta_id <= 0 ) {
		return false;
	}

	$table = _get_meta_table( $meta_type );
	if ( ! $table ) {
		return false;
	}

	$column    = sanitize_key( $meta_type . '_id' );
	$id_column = 'user' == $meta_type ? 'umeta_id' : 'meta_id';

	/**
	 * Filters whether to update metadata of a specific type by meta ID.
	 *
	 * The dynamic portion of the hook, `$meta_type`, refers to the meta
	 * object type (comment, post, term, or user). Returning a non-null value
	 * will effectively short-circuit the function.
	 *
	 * @since 5.0.0
	 *
	 * @param null|bool   $check      Whether to allow updating metadata for the given type.
	 * @param int         $meta_id    Meta ID.
	 * @param mixed       $meta_value Meta value. Must be serializable if non-scalar.
	 * @param string|bool $meta_key   Meta key, if provided.
	 */
	$check = apply_filters( "update_{$meta_type}_metadata_by_mid", null, $meta_id, $meta_value, $meta_key );
	if ( null !== $check ) {
		return (bool) $check;
	}

	// Fetch the meta and go on if it's found.
	$meta = get_metadata_by_mid( $meta_type, $meta_id );
	if ( $meta ) {
		$original_key = $meta->meta_key;
		$object_id    = $meta->{$column};

		// If a new meta_key (last parameter) was specified, change the meta key,
		// otherwise use the original key in the update statement.
		if ( false === $meta_key ) {
			$meta_key = $original_key;
		} elseif ( ! is_string( $meta_key ) ) {
			return false;
		}

		$meta_subtype = get_object_subtype( $meta_type, $object_id );

		// Sanitize the meta
		$_meta_value = $meta_value;
		$meta_value  = sanitize_meta( $meta_key, $meta_value, $meta_type, $meta_subtype );
		$meta_value  = maybe_serialize( $meta_value );

		// Format the data query arguments.
		$data = array(
			'meta_key'   => $meta_key,
			'meta_value' => $meta_value,
		);

		// Format the where query arguments.
		$where               = array();
		$where[ $id_column ] = $meta_id;

		/** This action is documented in wp-includes/meta.php */
		do_action( "update_{$meta_type}_meta", $meta_id, $object_id, $meta_key, $_meta_value );

		if ( 'post' == $meta_type ) {
			/** This action is documented in wp-includes/meta.php */
			do_action( 'update_postmeta', $meta_id, $object_id, $meta_key, $meta_value );
		}

		// Run the update query, all fields in $data are %s, $where is a %d.
		$result = $wpdb->update( $table, $data, $where, '%s', '%d' );
		if ( ! $result ) {
			return false;
		}

		// Clear the caches.
		wp_cache_delete( $object_id, $meta_type . '_meta' );

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
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $meta_type Type of object metadata is for (e.g., comment, post, term, or user).
 * @param int    $meta_id   ID for a specific meta row
 * @return bool True on successful delete, false on failure.
 */
function delete_metadata_by_mid( $meta_type, $meta_id ) {
	global $wpdb;

	// Make sure everything is valid.
	if ( ! $meta_type || ! is_numeric( $meta_id ) || floor( $meta_id ) != $meta_id ) {
		return false;
	}

	$meta_id = intval( $meta_id );
	if ( $meta_id <= 0 ) {
		return false;
	}

	$table = _get_meta_table( $meta_type );
	if ( ! $table ) {
		return false;
	}

	// object and id columns
	$column    = sanitize_key( $meta_type . '_id' );
	$id_column = 'user' == $meta_type ? 'umeta_id' : 'meta_id';

	/**
	 * Filters whether to delete metadata of a specific type by meta ID.
	 *
	 * The dynamic portion of the hook, `$meta_type`, refers to the meta
	 * object type (comment, post, term, or user). Returning a non-null value
	 * will effectively short-circuit the function.
	 *
	 * @since 5.0.0
	 *
	 * @param null|bool $delete  Whether to allow metadata deletion of the given type.
	 * @param int       $meta_id Meta ID.
	 */
	$check = apply_filters( "delete_{$meta_type}_metadata_by_mid", null, $meta_id );
	if ( null !== $check ) {
		return (bool) $check;
	}

	// Fetch the meta and go on if it's found.
	$meta = get_metadata_by_mid( $meta_type, $meta_id );
	if ( $meta ) {
		$object_id = (int) $meta->{$column};

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
		wp_cache_delete( $object_id, $meta_type . '_meta' );

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
 * @param string       $meta_type  Type of object metadata is for (e.g., comment, post, term, or user).
 * @param string|int[] $object_ids Array or comma delimited list of object IDs to update cache for.
 * @return array|false Metadata cache for the specified objects, or false on failure.
 */
function update_meta_cache( $meta_type, $object_ids ) {
	global $wpdb;

	if ( ! $meta_type || ! $object_ids ) {
		return false;
	}

	$table = _get_meta_table( $meta_type );
	if ( ! $table ) {
		return false;
	}

	$column = sanitize_key( $meta_type . '_id' );

	if ( ! is_array( $object_ids ) ) {
		$object_ids = preg_replace( '|[^0-9,]|', '', $object_ids );
		$object_ids = explode( ',', $object_ids );
	}

	$object_ids = array_map( 'intval', $object_ids );

	/**
	 * Filters whether to update the metadata cache of a specific type.
	 *
	 * The dynamic portion of the hook, `$meta_type`, refers to the meta
	 * object type (comment, post, term, or user). Returning a non-null value
	 * will effectively short-circuit the function.
	 *
	 * @since 5.0.0
	 *
	 * @param mixed $check      Whether to allow updating the meta cache of the given type.
	 * @param int[] $object_ids Array of object IDs to update the meta cache for.
	 */
	$check = apply_filters( "update_{$meta_type}_metadata_cache", null, $object_ids );
	if ( null !== $check ) {
		return (bool) $check;
	}

	$cache_key = $meta_type . '_meta';
	$ids       = array();
	$cache     = array();
	foreach ( $object_ids as $id ) {
		$cached_object = wp_cache_get( $id, $cache_key );
		if ( false === $cached_object ) {
			$ids[] = $id;
		} else {
			$cache[ $id ] = $cached_object;
		}
	}

	if ( empty( $ids ) ) {
		return $cache;
	}

	// Get meta info
	$id_list   = join( ',', $ids );
	$id_column = 'user' == $meta_type ? 'umeta_id' : 'meta_id';
	$meta_list = $wpdb->get_results( "SELECT $column, meta_key, meta_value FROM $table WHERE $column IN ($id_list) ORDER BY $id_column ASC", ARRAY_A );

	if ( ! empty( $meta_list ) ) {
		foreach ( $meta_list as $metarow ) {
			$mpid = intval( $metarow[ $column ] );
			$mkey = $metarow['meta_key'];
			$mval = $metarow['meta_value'];

			// Force subkeys to be array type:
			if ( ! isset( $cache[ $mpid ] ) || ! is_array( $cache[ $mpid ] ) ) {
				$cache[ $mpid ] = array();
			}
			if ( ! isset( $cache[ $mpid ][ $mkey ] ) || ! is_array( $cache[ $mpid ][ $mkey ] ) ) {
				$cache[ $mpid ][ $mkey ] = array();
			}

			// Add a value to the current pid/key:
			$cache[ $mpid ][ $mkey ][] = $mval;
		}
	}

	foreach ( $ids as $id ) {
		if ( ! isset( $cache[ $id ] ) ) {
			$cache[ $id ] = array();
		}
		wp_cache_add( $id, $cache[ $id ], $cache_key );
	}

	return $cache;
}

/**
 * Retrieves the queue for lazy-loading metadata.
 *
 * @since 4.5.0
 *
 * @return WP_Metadata_Lazyloader $lazyloader Metadata lazyloader queue.
 */
function wp_metadata_lazyloader() {
	static $wp_metadata_lazyloader;

	if ( null === $wp_metadata_lazyloader ) {
		$wp_metadata_lazyloader = new WP_Metadata_Lazyloader();
	}

	return $wp_metadata_lazyloader;
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
 * Retrieve the name of the metadata table for the specified object type.
 *
 * @since 2.9.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $type Type of object to get metadata table for (e.g., comment, post, term, or user).
 * @return string|false Metadata table name, or false if no metadata table exists
 */
function _get_meta_table( $type ) {
	global $wpdb;

	$table_name = $type . 'meta';

	if ( empty( $wpdb->$table_name ) ) {
		return false;
	}

	return $wpdb->$table_name;
}

/**
 * Determines whether a meta key is considered protected.
 *
 * @since 3.1.3
 *
 * @param string      $meta_key  Meta key.
 * @param string|null $meta_type Optional. Type of object metadata is for (e.g., comment, post, term, or user).
 * @return bool Whether the meta key is considered protected.
 */
function is_protected_meta( $meta_key, $meta_type = '' ) {
	$sanitized_key = preg_replace( "/[^\x20-\x7E\p{L}]/", '', $meta_key );
	$protected     = strlen( $sanitized_key ) > 0 && ( '_' === $sanitized_key[0] );

	/**
	 * Filters whether a meta key is considered protected.
	 *
	 * @since 3.2.0
	 *
	 * @param bool        $protected Whether the key is considered protected.
	 * @param string      $meta_key  Meta key.
	 * @param string|null $meta_type Type of object metadata is for (e.g., comment, post, term, or user).
	 */
	return apply_filters( 'is_protected_meta', $protected, $meta_key, $meta_type );
}

/**
 * Sanitize meta value.
 *
 * @since 3.1.3
 * @since 4.9.8 The `$object_subtype` parameter was added.
 *
 * @param string $meta_key       Meta key.
 * @param mixed  $meta_value     Meta value to sanitize.
 * @param string $object_type    Type of object the meta is registered to.
 * @param string $object_subtype Optional. The subtype of the object type.
 *
 * @return mixed Sanitized $meta_value.
 */
function sanitize_meta( $meta_key, $meta_value, $object_type, $object_subtype = '' ) {
	if ( ! empty( $object_subtype ) && has_filter( "sanitize_{$object_type}_meta_{$meta_key}_for_{$object_subtype}" ) ) {

		/**
		 * Filters the sanitization of a specific meta key of a specific meta type and subtype.
		 *
		 * The dynamic portions of the hook name, `$object_type`, `$meta_key`,
		 * and `$object_subtype`, refer to the metadata object type (comment, post, term, or user),
		 * the meta key value, and the object subtype respectively.
		 *
		 * @since 4.9.8
		 *
		 * @param mixed  $meta_value     Meta value to sanitize.
		 * @param string $meta_key       Meta key.
		 * @param string $object_type    Object type.
		 * @param string $object_subtype Object subtype.
		 */
		return apply_filters( "sanitize_{$object_type}_meta_{$meta_key}_for_{$object_subtype}", $meta_value, $meta_key, $object_type, $object_subtype );
	}

	/**
	 * Filters the sanitization of a specific meta key of a specific meta type.
	 *
	 * The dynamic portions of the hook name, `$meta_type`, and `$meta_key`,
	 * refer to the metadata object type (comment, post, term, or user) and the meta
	 * key value, respectively.
	 *
	 * @since 3.3.0
	 *
	 * @param mixed  $meta_value      Meta value to sanitize.
	 * @param string $meta_key        Meta key.
	 * @param string $object_type     Object type.
	 */
	return apply_filters( "sanitize_{$object_type}_meta_{$meta_key}", $meta_value, $meta_key, $object_type );
}

/**
 * Registers a meta key.
 *
 * It is recommended to register meta keys for a specific combination of object type and object subtype. If passing
 * an object subtype is omitted, the meta key will be registered for the entire object type, however it can be partly
 * overridden in case a more specific meta key of the same name exists for the same object type and a subtype.
 *
 * If an object type does not support any subtypes, such as users or comments, you should commonly call this function
 * without passing a subtype.
 *
 * @since 3.3.0
 * @since 4.6.0 {@link https://core.trac.wordpress.org/ticket/35658 Modified
 *              to support an array of data to attach to registered meta keys}. Previous arguments for
 *              `$sanitize_callback` and `$auth_callback` have been folded into this array.
 * @since 4.9.8 The `$object_subtype` argument was added to the arguments array.
 * @since 5.3.0 Valid meta types expanded to include "array" and "object".
 *
 * @param string $object_type    Type of object this meta is registered to.
 * @param string $meta_key       Meta key to register.
 * @param array  $args {
 *     Data used to describe the meta key when registered.
 *
 *     @type string     $object_subtype    A subtype; e.g. if the object type is "post", the post type. If left empty,
 *                                         the meta key will be registered on the entire object type. Default empty.
 *     @type string     $type              The type of data associated with this meta key.
 *                                         Valid values are 'string', 'boolean', 'integer', 'number', 'array', and 'object'.
 *     @type string     $description       A description of the data attached to this meta key.
 *     @type bool       $single            Whether the meta key has one value per object, or an array of values per object.
 *     @type string     $sanitize_callback A function or method to call when sanitizing `$meta_key` data.
 *     @type string     $auth_callback     Optional. A function or method to call when performing edit_post_meta,
 *                                         add_post_meta, and delete_post_meta capability checks.
 *     @type bool|array $show_in_rest      Whether data associated with this meta key can be considered public and
 *                                         should be accessible via the REST API. A custom post type must also declare
 *                                         support for custom fields for registered meta to be accessible via REST.
 *                                         When registering complex meta values this argument may optionally be an
 *                                         array with 'schema' or 'prepare_callback' keys instead of a boolean.
 * }
 * @param string|array $deprecated Deprecated. Use `$args` instead.
 *
 * @return bool True if the meta key was successfully registered in the global array, false if not.
 *                       Registering a meta key with distinct sanitize and auth callbacks will fire those
 *                       callbacks, but will not add to the global registry.
 */
function register_meta( $object_type, $meta_key, $args, $deprecated = null ) {
	global $wp_meta_keys;

	if ( ! is_array( $wp_meta_keys ) ) {
		$wp_meta_keys = array();
	}

	$defaults = array(
		'object_subtype'    => '',
		'type'              => 'string',
		'description'       => '',
		'single'            => false,
		'sanitize_callback' => null,
		'auth_callback'     => null,
		'show_in_rest'      => false,
	);

	// There used to be individual args for sanitize and auth callbacks
	$has_old_sanitize_cb = false;
	$has_old_auth_cb     = false;

	if ( is_callable( $args ) ) {
		$args = array(
			'sanitize_callback' => $args,
		);

		$has_old_sanitize_cb = true;
	} else {
		$args = (array) $args;
	}

	if ( is_callable( $deprecated ) ) {
		$args['auth_callback'] = $deprecated;
		$has_old_auth_cb       = true;
	}

	/**
	 * Filters the registration arguments when registering meta.
	 *
	 * @since 4.6.0
	 *
	 * @param array  $args        Array of meta registration arguments.
	 * @param array  $defaults    Array of default arguments.
	 * @param string $object_type Object type.
	 * @param string $meta_key    Meta key.
	 */
	$args = apply_filters( 'register_meta_args', $args, $defaults, $object_type, $meta_key );
	$args = wp_parse_args( $args, $defaults );

	// Require an item schema when registering array meta.
	if ( false !== $args['show_in_rest'] && 'array' === $args['type'] ) {
		if ( ! is_array( $args['show_in_rest'] ) || ! isset( $args['show_in_rest']['schema']['items'] ) ) {
			_doing_it_wrong( __FUNCTION__, __( 'When registering an "array" meta type to show in the REST API, you must specify the schema for each array item in "show_in_rest.schema.items".' ), '5.3.0' );

			return false;
		}
	}

	$object_subtype = ! empty( $args['object_subtype'] ) ? $args['object_subtype'] : '';

	// If `auth_callback` is not provided, fall back to `is_protected_meta()`.
	if ( empty( $args['auth_callback'] ) ) {
		if ( is_protected_meta( $meta_key, $object_type ) ) {
			$args['auth_callback'] = '__return_false';
		} else {
			$args['auth_callback'] = '__return_true';
		}
	}

	// Back-compat: old sanitize and auth callbacks are applied to all of an object type.
	if ( is_callable( $args['sanitize_callback'] ) ) {
		if ( ! empty( $object_subtype ) ) {
			add_filter( "sanitize_{$object_type}_meta_{$meta_key}_for_{$object_subtype}", $args['sanitize_callback'], 10, 4 );
		} else {
			add_filter( "sanitize_{$object_type}_meta_{$meta_key}", $args['sanitize_callback'], 10, 3 );
		}
	}

	if ( is_callable( $args['auth_callback'] ) ) {
		if ( ! empty( $object_subtype ) ) {
			add_filter( "auth_{$object_type}_meta_{$meta_key}_for_{$object_subtype}", $args['auth_callback'], 10, 6 );
		} else {
			add_filter( "auth_{$object_type}_meta_{$meta_key}", $args['auth_callback'], 10, 6 );
		}
	}

	// Global registry only contains meta keys registered with the array of arguments added in 4.6.0.
	if ( ! $has_old_auth_cb && ! $has_old_sanitize_cb ) {
		unset( $args['object_subtype'] );

		$wp_meta_keys[ $object_type ][ $object_subtype ][ $meta_key ] = $args;

		return true;
	}

	return false;
}

/**
 * Checks if a meta key is registered.
 *
 * @since 4.6.0
 * @since 4.9.8 The `$object_subtype` parameter was added.
 *
 * @param string $object_type    The type of object.
 * @param string $meta_key       The meta key.
 * @param string $object_subtype Optional. The subtype of the object type.
 *
 * @return bool True if the meta key is registered to the object type and, if provided,
 *              the object subtype. False if not.
 */
function registered_meta_key_exists( $object_type, $meta_key, $object_subtype = '' ) {
	$meta_keys = get_registered_meta_keys( $object_type, $object_subtype );

	return isset( $meta_keys[ $meta_key ] );
}

/**
 * Unregisters a meta key from the list of registered keys.
 *
 * @since 4.6.0
 * @since 4.9.8 The `$object_subtype` parameter was added.
 *
 * @param string $object_type    The type of object.
 * @param string $meta_key       The meta key.
 * @param string $object_subtype Optional. The subtype of the object type.
 * @return bool True if successful. False if the meta key was not registered.
 */
function unregister_meta_key( $object_type, $meta_key, $object_subtype = '' ) {
	global $wp_meta_keys;

	if ( ! registered_meta_key_exists( $object_type, $meta_key, $object_subtype ) ) {
		return false;
	}

	$args = $wp_meta_keys[ $object_type ][ $object_subtype ][ $meta_key ];

	if ( isset( $args['sanitize_callback'] ) && is_callable( $args['sanitize_callback'] ) ) {
		if ( ! empty( $object_subtype ) ) {
			remove_filter( "sanitize_{$object_type}_meta_{$meta_key}_for_{$object_subtype}", $args['sanitize_callback'] );
		} else {
			remove_filter( "sanitize_{$object_type}_meta_{$meta_key}", $args['sanitize_callback'] );
		}
	}

	if ( isset( $args['auth_callback'] ) && is_callable( $args['auth_callback'] ) ) {
		if ( ! empty( $object_subtype ) ) {
			remove_filter( "auth_{$object_type}_meta_{$meta_key}_for_{$object_subtype}", $args['auth_callback'] );
		} else {
			remove_filter( "auth_{$object_type}_meta_{$meta_key}", $args['auth_callback'] );
		}
	}

	unset( $wp_meta_keys[ $object_type ][ $object_subtype ][ $meta_key ] );

	// Do some clean up
	if ( empty( $wp_meta_keys[ $object_type ][ $object_subtype ] ) ) {
		unset( $wp_meta_keys[ $object_type ][ $object_subtype ] );
	}
	if ( empty( $wp_meta_keys[ $object_type ] ) ) {
		unset( $wp_meta_keys[ $object_type ] );
	}

	return true;
}

/**
 * Retrieves a list of registered meta keys for an object type.
 *
 * @since 4.6.0
 * @since 4.9.8 The `$object_subtype` parameter was added.
 *
 * @param string $object_type    The type of object. Post, comment, user, term.
 * @param string $object_subtype Optional. The subtype of the object type.
 * @return array List of registered meta keys.
 */
function get_registered_meta_keys( $object_type, $object_subtype = '' ) {
	global $wp_meta_keys;

	if ( ! is_array( $wp_meta_keys ) || ! isset( $wp_meta_keys[ $object_type ] ) || ! isset( $wp_meta_keys[ $object_type ][ $object_subtype ] ) ) {
		return array();
	}

	return $wp_meta_keys[ $object_type ][ $object_subtype ];
}

/**
 * Retrieves registered metadata for a specified object.
 *
 * The results include both meta that is registered specifically for the
 * object's subtype and meta that is registered for the entire object type.
 *
 * @since 4.6.0
 *
 * @param string $object_type Type of object to request metadata for. (e.g. comment, post, term, user)
 * @param int    $object_id   ID of the object the metadata is for.
 * @param string $meta_key    Optional. Registered metadata key. If not specified, retrieve all registered
 *                            metadata for the specified object.
 * @return mixed A single value or array of values for a key if specified. An array of all registered keys
 *               and values for an object ID if not. False if a given $meta_key is not registered.
 */
function get_registered_metadata( $object_type, $object_id, $meta_key = '' ) {
	$object_subtype = get_object_subtype( $object_type, $object_id );

	if ( ! empty( $meta_key ) ) {
		if ( ! empty( $object_subtype ) && ! registered_meta_key_exists( $object_type, $meta_key, $object_subtype ) ) {
			$object_subtype = '';
		}

		if ( ! registered_meta_key_exists( $object_type, $meta_key, $object_subtype ) ) {
			return false;
		}

		$meta_keys     = get_registered_meta_keys( $object_type, $object_subtype );
		$meta_key_data = $meta_keys[ $meta_key ];

		$data = get_metadata( $object_type, $object_id, $meta_key, $meta_key_data['single'] );

		return $data;
	}

	$data = get_metadata( $object_type, $object_id );
	if ( ! $data ) {
		return array();
	}

	$meta_keys = get_registered_meta_keys( $object_type );
	if ( ! empty( $object_subtype ) ) {
		$meta_keys = array_merge( $meta_keys, get_registered_meta_keys( $object_type, $object_subtype ) );
	}

	return array_intersect_key( $data, $meta_keys );
}

/**
 * Filter out `register_meta()` args based on a whitelist.
 * `register_meta()` args may change over time, so requiring the whitelist
 * to be explicitly turned off is a warranty seal of sorts.
 *
 * @access private
 * @since 4.6.0
 *
 * @param array $args         Arguments from `register_meta()`.
 * @param array $default_args Default arguments for `register_meta()`.
 *
 * @return array Filtered arguments.
 */
function _wp_register_meta_args_whitelist( $args, $default_args ) {
	return array_intersect_key( $args, $default_args );
}

/**
 * Returns the object subtype for a given object ID of a specific type.
 *
 * @since 4.9.8
 *
 * @param string $object_type Type of object to request metadata for. (e.g. comment, post, term, user)
 * @param int    $object_id   ID of the object to retrieve its subtype.
 * @return string The object subtype or an empty string if unspecified subtype.
 */
function get_object_subtype( $object_type, $object_id ) {
	$object_id      = (int) $object_id;
	$object_subtype = '';

	switch ( $object_type ) {
		case 'post':
			$post_type = get_post_type( $object_id );

			if ( ! empty( $post_type ) ) {
				$object_subtype = $post_type;
			}
			break;

		case 'term':
			$term = get_term( $object_id );
			if ( ! $term instanceof WP_Term ) {
				break;
			}

			$object_subtype = $term->taxonomy;
			break;

		case 'comment':
			$comment = get_comment( $object_id );
			if ( ! $comment ) {
				break;
			}

			$object_subtype = 'comment';
			break;

		case 'user':
			$user = get_user_by( 'id', $object_id );
			if ( ! $user ) {
				break;
			}

			$object_subtype = 'user';
			break;
	}

	/**
	 * Filters the object subtype identifier for a non standard object type.
	 *
	 * The dynamic portion of the hook, `$object_type`, refers to the object
	 * type (post, comment, term, or user).
	 *
	 * @since 4.9.8
	 *
	 * @param string $object_subtype Empty string to override.
	 * @param int    $object_id      ID of the object to get the subtype for.
	 */
	return apply_filters( "get_object_subtype_{$object_type}", $object_subtype, $object_id );
}
