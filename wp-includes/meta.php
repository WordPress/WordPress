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

require ABSPATH . WPINC . '/class-wp-metadata-lazyloader.php';

/**
 * Adds metadata for the specified object.
 *
 * For historical reasons both the meta key and the meta value are expected to be "slashed" (slashes escaped) on input.
 *
 * @since 2.9.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $meta_type  Type of object metadata is for. Accepts 'blog', 'post', 'comment', 'term',
 *                           'user', or any other object type with an associated meta table.
 * @param int    $object_id  ID of the object metadata is for.
 * @param string $meta_key   Metadata key.
 * @param mixed  $meta_value Metadata value. Arrays and objects are stored as serialized data and
 *                           will be returned as the same type when retrieved. Other data types will
 *                           be stored as strings in the database:
 *                           - false is stored and retrieved as an empty string ('')
 *                           - true is stored and retrieved as '1'
 *                           - numbers (both integer and float) are stored and retrieved as strings
 *                           Must be serializable if non-scalar.
 * @param bool   $unique     Optional. Whether the specified metadata key should be unique for the object.
 *                           If true, and the object already has a value for the specified metadata key,
 *                           no change will be made. Default false.
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
	 * Short-circuits adding metadata of a specific type.
	 *
	 * The dynamic portion of the hook name, `$meta_type`, refers to the meta object type
	 * (blog, post, comment, term, user, or any other type with an associated meta table).
	 * Returning a non-null value will effectively short-circuit the function.
	 *
	 * Possible hook names include:
	 *
	 *  - `add_blog_metadata`
	 *  - `add_post_metadata`
	 *  - `add_comment_metadata`
	 *  - `add_term_metadata`
	 *  - `add_user_metadata`
	 *
	 * @since 3.1.0
	 *
	 * @param null|int|false $check      Whether to allow adding metadata for the given type. Return false or a meta ID
	 *                                   to short-circuit the function. Return null to continue with the default behavior.
	 * @param int            $object_id  ID of the object metadata is for.
	 * @param string         $meta_key   Metadata key.
	 * @param mixed          $meta_value Metadata value. Must be serializable if non-scalar.
	 * @param bool           $unique     Whether the specified meta key should be unique for the object.
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
	 * The dynamic portion of the hook name, `$meta_type`, refers to the meta object type
	 * (blog, post, comment, term, user, or any other type with an associated meta table).
	 *
	 * Possible hook names include:
	 *
	 *  - `add_blog_meta`
	 *  - `add_post_meta`
	 *  - `add_comment_meta`
	 *  - `add_term_meta`
	 *  - `add_user_meta`
	 *
	 * @since 3.1.0
	 *
	 * @param int    $object_id   ID of the object metadata is for.
	 * @param string $meta_key    Metadata key.
	 * @param mixed  $_meta_value Metadata value.
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
	 * The dynamic portion of the hook name, `$meta_type`, refers to the meta object type
	 * (blog, post, comment, term, user, or any other type with an associated meta table).
	 *
	 * Possible hook names include:
	 *
	 *  - `added_blog_meta`
	 *  - `added_post_meta`
	 *  - `added_comment_meta`
	 *  - `added_term_meta`
	 *  - `added_user_meta`
	 *
	 * @since 2.9.0
	 *
	 * @param int    $mid         The meta ID after successful update.
	 * @param int    $object_id   ID of the object metadata is for.
	 * @param string $meta_key    Metadata key.
	 * @param mixed  $_meta_value Metadata value.
	 */
	do_action( "added_{$meta_type}_meta", $mid, $object_id, $meta_key, $_meta_value );

	return $mid;
}

/**
 * Updates metadata for the specified object. If no value already exists for the specified object
 * ID and metadata key, the metadata will be added.
 *
 * For historical reasons both the meta key and the meta value are expected to be "slashed" (slashes escaped) on input.
 *
 * @since 2.9.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $meta_type  Type of object metadata is for. Accepts 'blog', 'post', 'comment', 'term',
 *                           'user', or any other object type with an associated meta table.
 * @param int    $object_id  ID of the object metadata is for.
 * @param string $meta_key   Metadata key.
 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
 * @param mixed  $prev_value Optional. Previous value to check before updating.
 *                           If specified, only update existing metadata entries with
 *                           this value. Otherwise, update all entries. Default empty string.
 * @return int|bool The new meta field ID if a field with the given key didn't exist
 *                  and was therefore added, true on successful update,
 *                  false on failure or if the value passed to the function
 *                  is the same as the one that is already in the database.
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
	$id_column = ( 'user' === $meta_type ) ? 'umeta_id' : 'meta_id';

	// expected_slashed ($meta_key)
	$raw_meta_key = $meta_key;
	$meta_key     = wp_unslash( $meta_key );
	$passed_value = $meta_value;
	$meta_value   = wp_unslash( $meta_value );
	$meta_value   = sanitize_meta( $meta_key, $meta_value, $meta_type, $meta_subtype );

	/**
	 * Short-circuits updating metadata of a specific type.
	 *
	 * The dynamic portion of the hook name, `$meta_type`, refers to the meta object type
	 * (blog, post, comment, term, user, or any other type with an associated meta table).
	 * Returning a non-null value will effectively short-circuit the function.
	 *
	 * Possible hook names include:
	 *
	 *  - `update_blog_metadata`
	 *  - `update_post_metadata`
	 *  - `update_comment_metadata`
	 *  - `update_term_metadata`
	 *  - `update_user_metadata`
	 *
	 * @since 3.1.0
	 *
	 * @param null|bool $check      Whether to allow updating metadata for the given type.
	 * @param int       $object_id  ID of the object metadata is for.
	 * @param string    $meta_key   Metadata key.
	 * @param mixed     $meta_value Metadata value. Must be serializable if non-scalar.
	 * @param mixed     $prev_value Optional. Previous value to check before updating.
	 *                              If specified, only update existing metadata entries with
	 *                              this value. Otherwise, update all entries.
	 */
	$check = apply_filters( "update_{$meta_type}_metadata", null, $object_id, $meta_key, $meta_value, $prev_value );
	if ( null !== $check ) {
		return (bool) $check;
	}

	// Compare existing value to new value if no prev value given and the key exists only once.
	if ( empty( $prev_value ) ) {
		$old_value = get_metadata_raw( $meta_type, $object_id, $meta_key );
		if ( is_countable( $old_value ) && count( $old_value ) === 1 ) {
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
		 * The dynamic portion of the hook name, `$meta_type`, refers to the meta object type
		 * (blog, post, comment, term, user, or any other type with an associated meta table).
		 *
		 * Possible hook names include:
		 *
		 *  - `update_blog_meta`
		 *  - `update_post_meta`
		 *  - `update_comment_meta`
		 *  - `update_term_meta`
		 *  - `update_user_meta`
		 *
		 * @since 2.9.0
		 *
		 * @param int    $meta_id     ID of the metadata entry to update.
		 * @param int    $object_id   ID of the object metadata is for.
		 * @param string $meta_key    Metadata key.
		 * @param mixed  $_meta_value Metadata value.
		 */
		do_action( "update_{$meta_type}_meta", $meta_id, $object_id, $meta_key, $_meta_value );

		if ( 'post' === $meta_type ) {
			/**
			 * Fires immediately before updating a post's metadata.
			 *
			 * @since 2.9.0
			 *
			 * @param int    $meta_id    ID of metadata entry to update.
			 * @param int    $object_id  Post ID.
			 * @param string $meta_key   Metadata key.
			 * @param mixed  $meta_value Metadata value. This will be a PHP-serialized string representation of the value
			 *                           if the value is an array, an object, or itself a PHP-serialized string.
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
		 * The dynamic portion of the hook name, `$meta_type`, refers to the meta object type
		 * (blog, post, comment, term, user, or any other type with an associated meta table).
		 *
		 * Possible hook names include:
		 *
		 *  - `updated_blog_meta`
		 *  - `updated_post_meta`
		 *  - `updated_comment_meta`
		 *  - `updated_term_meta`
		 *  - `updated_user_meta`
		 *
		 * @since 2.9.0
		 *
		 * @param int    $meta_id     ID of updated metadata entry.
		 * @param int    $object_id   ID of the object metadata is for.
		 * @param string $meta_key    Metadata key.
		 * @param mixed  $_meta_value Metadata value.
		 */
		do_action( "updated_{$meta_type}_meta", $meta_id, $object_id, $meta_key, $_meta_value );

		if ( 'post' === $meta_type ) {
			/**
			 * Fires immediately after updating a post's metadata.
			 *
			 * @since 2.9.0
			 *
			 * @param int    $meta_id    ID of updated metadata entry.
			 * @param int    $object_id  Post ID.
			 * @param string $meta_key   Metadata key.
			 * @param mixed  $meta_value Metadata value. This will be a PHP-serialized string representation of the value
			 *                           if the value is an array, an object, or itself a PHP-serialized string.
			 */
			do_action( 'updated_postmeta', $meta_id, $object_id, $meta_key, $meta_value );
		}
	}

	return true;
}

/**
 * Deletes metadata for the specified object.
 *
 * For historical reasons both the meta key and the meta value are expected to be "slashed" (slashes escaped) on input.
 *
 * @since 2.9.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $meta_type  Type of object metadata is for. Accepts 'blog', 'post', 'comment', 'term',
 *                           'user', or any other object type with an associated meta table.
 * @param int    $object_id  ID of the object metadata is for.
 * @param string $meta_key   Metadata key.
 * @param mixed  $meta_value Optional. Metadata value. Must be serializable if non-scalar.
 *                           If specified, only delete metadata entries with this value.
 *                           Otherwise, delete all entries with the specified meta_key.
 *                           Pass `null`, `false`, or an empty string to skip this check.
 *                           (For backward compatibility, it is not possible to pass an empty string
 *                           to delete those entries with an empty string for a value.)
 *                           Default empty string.
 * @param bool   $delete_all Optional. If true, delete matching metadata entries for all objects,
 *                           ignoring the specified object_id. Otherwise, only delete
 *                           matching metadata entries for the specified object_id. Default false.
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
	$id_column   = ( 'user' === $meta_type ) ? 'umeta_id' : 'meta_id';

	// expected_slashed ($meta_key)
	$meta_key   = wp_unslash( $meta_key );
	$meta_value = wp_unslash( $meta_value );

	/**
	 * Short-circuits deleting metadata of a specific type.
	 *
	 * The dynamic portion of the hook name, `$meta_type`, refers to the meta object type
	 * (blog, post, comment, term, user, or any other type with an associated meta table).
	 * Returning a non-null value will effectively short-circuit the function.
	 *
	 * Possible hook names include:
	 *
	 *  - `delete_blog_metadata`
	 *  - `delete_post_metadata`
	 *  - `delete_comment_metadata`
	 *  - `delete_term_metadata`
	 *  - `delete_user_metadata`
	 *
	 * @since 3.1.0
	 *
	 * @param null|bool $delete     Whether to allow metadata deletion of the given type.
	 * @param int       $object_id  ID of the object metadata is for.
	 * @param string    $meta_key   Metadata key.
	 * @param mixed     $meta_value Metadata value. Must be serializable if non-scalar.
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
	 * The dynamic portion of the hook name, `$meta_type`, refers to the meta object type
	 * (blog, post, comment, term, user, or any other type with an associated meta table).
	 *
	 * Possible hook names include:
	 *
	 *  - `delete_blog_meta`
	 *  - `delete_post_meta`
	 *  - `delete_comment_meta`
	 *  - `delete_term_meta`
	 *  - `delete_user_meta`
	 *
	 * @since 3.1.0
	 *
	 * @param string[] $meta_ids    An array of metadata entry IDs to delete.
	 * @param int      $object_id   ID of the object metadata is for.
	 * @param string   $meta_key    Metadata key.
	 * @param mixed    $_meta_value Metadata value.
	 */
	do_action( "delete_{$meta_type}_meta", $meta_ids, $object_id, $meta_key, $_meta_value );

	// Old-style action.
	if ( 'post' === $meta_type ) {
		/**
		 * Fires immediately before deleting metadata for a post.
		 *
		 * @since 2.9.0
		 *
		 * @param string[] $meta_ids An array of metadata entry IDs to delete.
		 */
		do_action( 'delete_postmeta', $meta_ids );
	}

	$query = "DELETE FROM $table WHERE $id_column IN( " . implode( ',', $meta_ids ) . ' )';

	$count = $wpdb->query( $query );

	if ( ! $count ) {
		return false;
	}

	if ( $delete_all ) {
		$data = (array) $object_ids;
	} else {
		$data = array( $object_id );
	}
	wp_cache_delete_multiple( $data, $meta_type . '_meta' );

	/**
	 * Fires immediately after deleting metadata of a specific type.
	 *
	 * The dynamic portion of the hook name, `$meta_type`, refers to the meta object type
	 * (blog, post, comment, term, user, or any other type with an associated meta table).
	 *
	 * Possible hook names include:
	 *
	 *  - `deleted_blog_meta`
	 *  - `deleted_post_meta`
	 *  - `deleted_comment_meta`
	 *  - `deleted_term_meta`
	 *  - `deleted_user_meta`
	 *
	 * @since 2.9.0
	 *
	 * @param string[] $meta_ids    An array of metadata entry IDs to delete.
	 * @param int      $object_id   ID of the object metadata is for.
	 * @param string   $meta_key    Metadata key.
	 * @param mixed    $_meta_value Metadata value.
	 */
	do_action( "deleted_{$meta_type}_meta", $meta_ids, $object_id, $meta_key, $_meta_value );

	// Old-style action.
	if ( 'post' === $meta_type ) {
		/**
		 * Fires immediately after deleting metadata for a post.
		 *
		 * @since 2.9.0
		 *
		 * @param string[] $meta_ids An array of metadata entry IDs to delete.
		 */
		do_action( 'deleted_postmeta', $meta_ids );
	}

	return true;
}

/**
 * Retrieves the value of a metadata field for the specified object type and ID.
 *
 * If the meta field exists, a single value is returned if `$single` is true,
 * or an array of values if it's false.
 *
 * If the meta field does not exist, the result depends on get_metadata_default().
 * By default, an empty string is returned if `$single` is true, or an empty array
 * if it's false.
 *
 * @since 2.9.0
 *
 * @see get_metadata_raw()
 * @see get_metadata_default()
 *
 * @param string $meta_type Type of object metadata is for. Accepts 'blog', 'post', 'comment', 'term',
 *                          'user', or any other object type with an associated meta table.
 * @param int    $object_id ID of the object metadata is for.
 * @param string $meta_key  Optional. Metadata key. If not specified, retrieve all metadata for
 *                          the specified object. Default empty string.
 * @param bool   $single    Optional. If true, return only the first value of the specified `$meta_key`.
 *                          This parameter has no effect if `$meta_key` is not specified. Default false.
 * @return mixed An array of values if `$single` is false.
 *               The value of the meta field if `$single` is true.
 *               False for an invalid `$object_id` (non-numeric, zero, or negative value),
 *               or if `$meta_type` is not specified.
 *               An empty array if a valid but non-existing object ID is passed and `$single` is false.
 *               An empty string if a valid but non-existing object ID is passed and `$single` is true.
 *               Note: Non-serialized values are returned as strings:
 *               - false values are returned as empty strings ('')
 *               - true values are returned as '1'
 *               - numbers (both integer and float) are returned as strings
 *               Arrays and objects retain their original type.
 */
function get_metadata( $meta_type, $object_id, $meta_key = '', $single = false ) {
	$value = get_metadata_raw( $meta_type, $object_id, $meta_key, $single );

	if ( ! is_null( $value ) ) {
		/**
		 * Filters the value of metadata after it is retrieved.
		 *
		 * The dynamic portion of the hook name, `$meta_type`, refers to the meta
		 * object type (post, comment, term, user, etc).
		 *
		 * The dynamic portion of the hook name, `$meta_key`, refers to the specific
		 * meta key (when provided).
		 *
		 * Examples:
		 *  - post_metadata
		 *  - user_metadata
		 *  - user_metadata_nickname
		 *
		 * @param mixed  $value     The metadata value.
		 * @param int    $object_id Object ID.
		 * @param string $meta_key  Metadata key.
		 * @param bool   $single    Whether only the first value should be returned.
		 */
		$value = apply_filters( "{$meta_type}_metadata", $value, $object_id, $meta_key, $single );

		if ( $meta_key ) {
			$value = apply_filters( "{$meta_type}_metadata_{$meta_key}", $value, $object_id, $single );
		}

		return $value;
	}

	$default = get_metadata_default( $meta_type, $object_id, $meta_key, $single );

	$default = apply_filters( "{$meta_type}_metadata", $default, $object_id, $meta_key, $single );

	if ( $meta_key ) {
		$default = apply_filters( "{$meta_type}_metadata_{$meta_key}", $default, $object_id, $single );
	}

	return $default;
}


/**
 * Retrieves raw metadata value for the specified object.
 *
 * @since 5.5.0
 *
 * @param string $meta_type Type of object metadata is for. Accepts 'blog', 'post', 'comment', 'term',
 *                          'user', or any other object type with an associated meta table.
 * @param int    $object_id ID of the object metadata is for.
 * @param string $meta_key  Optional. Metadata key. If not specified, retrieve all metadata for
 *                          the specified object. Default empty string.
 * @param bool   $single    Optional. If true, return only the first value of the specified `$meta_key`.
 *                          This parameter has no effect if `$meta_key` is not specified. Default false.
 * @return mixed An array of values if `$single` is false.
 *               The value of the meta field if `$single` is true.
 *               False for an invalid `$object_id` (non-numeric, zero, or negative value),
 *               or if `$meta_type` is not specified.
 *               Null if the value does not exist.
 */
function get_metadata_raw( $meta_type, $object_id, $meta_key = '', $single = false ) {
	if ( ! $meta_type || ! is_numeric( $object_id ) ) {
		return false;
	}

	$object_id = absint( $object_id );
	if ( ! $object_id ) {
		return false;
	}

	/**
	 * Short-circuits the return value of a meta field.
	 *
	 * The dynamic portion of the hook name, `$meta_type`, refers to the meta object type
	 * (blog, post, comment, term, user, or any other type with an associated meta table).
	 * Returning a non-null value will effectively short-circuit the function.
	 *
	 * Possible filter names include:
	 *
	 *  - `get_blog_metadata`
	 *  - `get_post_metadata`
	 *  - `get_comment_metadata`
	 *  - `get_term_metadata`
	 *  - `get_user_metadata`
	 *
	 * @since 3.1.0
	 * @since 5.5.0 Added the `$meta_type` parameter.
	 *
	 * @param mixed  $value     The value to return, either a single metadata value or an array
	 *                          of values depending on the value of `$single`. Default null.
	 * @param int    $object_id ID of the object metadata is for.
	 * @param string $meta_key  Metadata key.
	 * @param bool   $single    Whether to return only the first value of the specified `$meta_key`.
	 * @param string $meta_type Type of object metadata is for. Accepts 'blog', 'post', 'comment', 'term',
	 *                          'user', or any other object type with an associated meta table.
	 */
	$check = apply_filters( "get_{$meta_type}_metadata", null, $object_id, $meta_key, $single, $meta_type );
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

	return null;
}

/**
 * Retrieves default metadata value for the specified meta key and object.
 *
 * By default, an empty string is returned if `$single` is true, or an empty array
 * if it's false.
 *
 * @since 5.5.0
 *
 * @param string $meta_type Type of object metadata is for. Accepts 'blog', 'post', 'comment', 'term',
 *                          'user', or any other object type with an associated meta table.
 * @param int    $object_id ID of the object metadata is for.
 * @param string $meta_key  Metadata key.
 * @param bool   $single    Optional. If true, return only the first value of the specified `$meta_key`.
 *                          This parameter has no effect if `$meta_key` is not specified. Default false.
 * @return mixed An array of default values if `$single` is false.
 *               The default value of the meta field if `$single` is true.
 */
function get_metadata_default( $meta_type, $object_id, $meta_key, $single = false ) {
	if ( $single ) {
		$value = '';
	} else {
		$value = array();
	}

	/**
	 * Filters the default metadata value for a specified meta key and object.
	 *
	 * The dynamic portion of the hook name, `$meta_type`, refers to the meta object type
	 * (blog, post, comment, term, user, or any other type with an associated meta table).
	 *
	 * Possible filter names include:
	 *
	 *  - `default_blog_metadata`
	 *  - `default_post_metadata`
	 *  - `default_comment_metadata`
	 *  - `default_term_metadata`
	 *  - `default_user_metadata`
	 *
	 * @since 5.5.0
	 *
	 * @param mixed  $value     The value to return, either a single metadata value or an array
	 *                          of values depending on the value of `$single`.
	 * @param int    $object_id ID of the object metadata is for.
	 * @param string $meta_key  Metadata key.
	 * @param bool   $single    Whether to return only the first value of the specified `$meta_key`.
	 * @param string $meta_type Type of object metadata is for. Accepts 'blog', 'post', 'comment', 'term',
	 *                          'user', or any other object type with an associated meta table.
	 */
	$value = apply_filters( "default_{$meta_type}_metadata", $value, $object_id, $meta_key, $single, $meta_type );

	if ( ! $single && ! wp_is_numeric_array( $value ) ) {
		$value = array( $value );
	}

	return $value;
}

/**
 * Determines if a meta field with the given key exists for the given object ID.
 *
 * @since 3.3.0
 *
 * @param string $meta_type Type of object metadata is for. Accepts 'blog', 'post', 'comment', 'term',
 *                          'user', or any other object type with an associated meta table.
 * @param int    $object_id ID of the object metadata is for.
 * @param string $meta_key  Metadata key.
 * @return bool Whether a meta field with the given key exists.
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
	$check = apply_filters( "get_{$meta_type}_metadata", null, $object_id, $meta_key, true, $meta_type );
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
 * Retrieves metadata by meta ID.
 *
 * @since 3.3.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $meta_type Type of object metadata is for. Accepts 'blog', 'post', 'comment', 'term',
 *                          'user', or any other object type with an associated meta table.
 * @param int    $meta_id   ID for a specific meta row.
 * @return stdClass|false {
 *     Metadata object, or boolean `false` if the metadata doesn't exist.
 *
 *     @type string $meta_key   The meta key.
 *     @type mixed  $meta_value The unserialized meta value.
 *     @type string $meta_id    Optional. The meta ID when the meta type is any value except 'user'.
 *     @type string $umeta_id   Optional. The meta ID when the meta type is 'user'.
 *     @type string $blog_id    Optional. The object ID when the meta type is 'blog'.
 *     @type string $post_id    Optional. The object ID when the meta type is 'post'.
 *     @type string $comment_id Optional. The object ID when the meta type is 'comment'.
 *     @type string $term_id    Optional. The object ID when the meta type is 'term'.
 *     @type string $user_id    Optional. The object ID when the meta type is 'user'.
 * }
 */
function get_metadata_by_mid( $meta_type, $meta_id ) {
	global $wpdb;

	if ( ! $meta_type || ! is_numeric( $meta_id ) || floor( $meta_id ) != $meta_id ) {
		return false;
	}

	$meta_id = (int) $meta_id;
	if ( $meta_id <= 0 ) {
		return false;
	}

	$table = _get_meta_table( $meta_type );
	if ( ! $table ) {
		return false;
	}

	/**
	 * Short-circuits the return value when fetching a meta field by meta ID.
	 *
	 * The dynamic portion of the hook name, `$meta_type`, refers to the meta object type
	 * (blog, post, comment, term, user, or any other type with an associated meta table).
	 * Returning a non-null value will effectively short-circuit the function.
	 *
	 * Possible hook names include:
	 *
	 *  - `get_blog_metadata_by_mid`
	 *  - `get_post_metadata_by_mid`
	 *  - `get_comment_metadata_by_mid`
	 *  - `get_term_metadata_by_mid`
	 *  - `get_user_metadata_by_mid`
	 *
	 * @since 5.0.0
	 *
	 * @param stdClass|null $value   The value to return.
	 * @param int           $meta_id Meta ID.
	 */
	$check = apply_filters( "get_{$meta_type}_metadata_by_mid", null, $meta_id );
	if ( null !== $check ) {
		return $check;
	}

	$id_column = ( 'user' === $meta_type ) ? 'umeta_id' : 'meta_id';

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
 * Updates metadata by meta ID.
 *
 * @since 3.3.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string       $meta_type  Type of object metadata is for. Accepts 'blog', 'post', 'comment', 'term',
 *                                 'user', or any other object type with an associated meta table.
 * @param int          $meta_id    ID for a specific meta row.
 * @param string       $meta_value Metadata value. Must be serializable if non-scalar.
 * @param string|false $meta_key   Optional. You can provide a meta key to update it. Default false.
 * @return bool True on successful update, false on failure.
 */
function update_metadata_by_mid( $meta_type, $meta_id, $meta_value, $meta_key = false ) {
	global $wpdb;

	// Make sure everything is valid.
	if ( ! $meta_type || ! is_numeric( $meta_id ) || floor( $meta_id ) != $meta_id ) {
		return false;
	}

	$meta_id = (int) $meta_id;
	if ( $meta_id <= 0 ) {
		return false;
	}

	$table = _get_meta_table( $meta_type );
	if ( ! $table ) {
		return false;
	}

	$column    = sanitize_key( $meta_type . '_id' );
	$id_column = ( 'user' === $meta_type ) ? 'umeta_id' : 'meta_id';

	/**
	 * Short-circuits updating metadata of a specific type by meta ID.
	 *
	 * The dynamic portion of the hook name, `$meta_type`, refers to the meta object type
	 * (blog, post, comment, term, user, or any other type with an associated meta table).
	 * Returning a non-null value will effectively short-circuit the function.
	 *
	 * Possible hook names include:
	 *
	 *  - `update_blog_metadata_by_mid`
	 *  - `update_post_metadata_by_mid`
	 *  - `update_comment_metadata_by_mid`
	 *  - `update_term_metadata_by_mid`
	 *  - `update_user_metadata_by_mid`
	 *
	 * @since 5.0.0
	 *
	 * @param null|bool    $check      Whether to allow updating metadata for the given type.
	 * @param int          $meta_id    Meta ID.
	 * @param mixed        $meta_value Meta value. Must be serializable if non-scalar.
	 * @param string|false $meta_key   Meta key, if provided.
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

		/*
		 * If a new meta_key (last parameter) was specified, change the meta key,
		 * otherwise use the original key in the update statement.
		 */
		if ( false === $meta_key ) {
			$meta_key = $original_key;
		} elseif ( ! is_string( $meta_key ) ) {
			return false;
		}

		$meta_subtype = get_object_subtype( $meta_type, $object_id );

		// Sanitize the meta.
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

		if ( 'post' === $meta_type ) {
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

		if ( 'post' === $meta_type ) {
			/** This action is documented in wp-includes/meta.php */
			do_action( 'updated_postmeta', $meta_id, $object_id, $meta_key, $meta_value );
		}

		return true;
	}

	// And if the meta was not found.
	return false;
}

/**
 * Deletes metadata by meta ID.
 *
 * @since 3.3.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $meta_type Type of object metadata is for. Accepts 'blog', 'post', 'comment', 'term',
 *                          'user', or any other object type with an associated meta table.
 * @param int    $meta_id   ID for a specific meta row.
 * @return bool True on successful delete, false on failure.
 */
function delete_metadata_by_mid( $meta_type, $meta_id ) {
	global $wpdb;

	// Make sure everything is valid.
	if ( ! $meta_type || ! is_numeric( $meta_id ) || floor( $meta_id ) != $meta_id ) {
		return false;
	}

	$meta_id = (int) $meta_id;
	if ( $meta_id <= 0 ) {
		return false;
	}

	$table = _get_meta_table( $meta_type );
	if ( ! $table ) {
		return false;
	}

	// Object and ID columns.
	$column    = sanitize_key( $meta_type . '_id' );
	$id_column = ( 'user' === $meta_type ) ? 'umeta_id' : 'meta_id';

	/**
	 * Short-circuits deleting metadata of a specific type by meta ID.
	 *
	 * The dynamic portion of the hook name, `$meta_type`, refers to the meta object type
	 * (blog, post, comment, term, user, or any other type with an associated meta table).
	 * Returning a non-null value will effectively short-circuit the function.
	 *
	 * Possible hook names include:
	 *
	 *  - `delete_blog_metadata_by_mid`
	 *  - `delete_post_metadata_by_mid`
	 *  - `delete_comment_metadata_by_mid`
	 *  - `delete_term_metadata_by_mid`
	 *  - `delete_user_metadata_by_mid`
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
		if ( 'post' === $meta_type || 'comment' === $meta_type ) {
			/**
			 * Fires immediately before deleting post or comment metadata of a specific type.
			 *
			 * The dynamic portion of the hook name, `$meta_type`, refers to the meta
			 * object type (post or comment).
			 *
			 * Possible hook names include:
			 *
			 *  - `delete_postmeta`
			 *  - `delete_commentmeta`
			 *
			 * @since 3.4.0
			 *
			 * @param int $meta_id ID of the metadata entry to delete.
			 */
			do_action( "delete_{$meta_type}meta", $meta_id );
		}

		// Run the query, will return true if deleted, false otherwise.
		$result = (bool) $wpdb->delete( $table, array( $id_column => $meta_id ) );

		// Clear the caches.
		wp_cache_delete( $object_id, $meta_type . '_meta' );

		/** This action is documented in wp-includes/meta.php */
		do_action( "deleted_{$meta_type}_meta", (array) $meta_id, $object_id, $meta->meta_key, $meta->meta_value );

		// Old-style action.
		if ( 'post' === $meta_type || 'comment' === $meta_type ) {
			/**
			 * Fires immediately after deleting post or comment metadata of a specific type.
			 *
			 * The dynamic portion of the hook name, `$meta_type`, refers to the meta
			 * object type (post or comment).
			 *
			 * Possible hook names include:
			 *
			 *  - `deleted_postmeta`
			 *  - `deleted_commentmeta`
			 *
			 * @since 3.4.0
			 *
			 * @param int $meta_id Deleted metadata entry ID.
			 */
			do_action( "deleted_{$meta_type}meta", $meta_id );
		}

		return $result;

	}

	// Meta ID was not found.
	return false;
}

/**
 * Updates the metadata cache for the specified objects.
 *
 * @since 2.9.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string       $meta_type  Type of object metadata is for. Accepts 'blog', 'post', 'comment', 'term',
 *                                 'user', or any other object type with an associated meta table.
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
	 * Short-circuits updating the metadata cache of a specific type.
	 *
	 * The dynamic portion of the hook name, `$meta_type`, refers to the meta object type
	 * (blog, post, comment, term, user, or any other type with an associated meta table).
	 * Returning a non-null value will effectively short-circuit the function.
	 *
	 * Possible hook names include:
	 *
	 *  - `update_blog_metadata_cache`
	 *  - `update_post_metadata_cache`
	 *  - `update_comment_metadata_cache`
	 *  - `update_term_metadata_cache`
	 *  - `update_user_metadata_cache`
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

	$cache_group    = $meta_type . '_meta';
	$non_cached_ids = array();
	$cache          = array();
	$cache_values   = wp_cache_get_multiple( $object_ids, $cache_group );

	foreach ( $cache_values as $id => $cached_object ) {
		if ( false === $cached_object ) {
			$non_cached_ids[] = $id;
		} else {
			$cache[ $id ] = $cached_object;
		}
	}

	if ( empty( $non_cached_ids ) ) {
		return $cache;
	}

	// Get meta info.
	$id_list   = implode( ',', $non_cached_ids );
	$id_column = ( 'user' === $meta_type ) ? 'umeta_id' : 'meta_id';

	$meta_list = $wpdb->get_results( "SELECT $column, meta_key, meta_value FROM $table WHERE $column IN ($id_list) ORDER BY $id_column ASC", ARRAY_A );

	if ( ! empty( $meta_list ) ) {
		foreach ( $meta_list as $metarow ) {
			$mpid = (int) $metarow[ $column ];
			$mkey = $metarow['meta_key'];
			$mval = $metarow['meta_value'];

			// Force subkeys to be array type.
			if ( ! isset( $cache[ $mpid ] ) || ! is_array( $cache[ $mpid ] ) ) {
				$cache[ $mpid ] = array();
			}
			if ( ! isset( $cache[ $mpid ][ $mkey ] ) || ! is_array( $cache[ $mpid ][ $mkey ] ) ) {
				$cache[ $mpid ][ $mkey ] = array();
			}

			// Add a value to the current pid/key.
			$cache[ $mpid ][ $mkey ][] = $mval;
		}
	}

	$data = array();
	foreach ( $non_cached_ids as $id ) {
		if ( ! isset( $cache[ $id ] ) ) {
			$cache[ $id ] = array();
		}
		$data[ $id ] = $cache[ $id ];
	}
	wp_cache_add_multiple( $data, $cache_group );

	return $cache;
}

/**
 * Retrieves the queue for lazy-loading metadata.
 *
 * @since 4.5.0
 *
 * @return WP_Metadata_Lazyloader Metadata lazyloader queue.
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
 * @param array  $meta_query        A meta query.
 * @param string $type              Type of meta.
 * @param string $primary_table     Primary database table name.
 * @param string $primary_id_column Primary ID column name.
 * @param object $context           Optional. The main query object. Default null.
 * @return string[]|false {
 *     Array containing JOIN and WHERE SQL clauses to append to the main query,
 *     or false if no table exists for the requested meta type.
 *
 *     @type string $join  SQL fragment to append to the main JOIN clause.
 *     @type string $where SQL fragment to append to the main WHERE clause.
 * }
 */
function get_meta_sql( $meta_query, $type, $primary_table, $primary_id_column, $context = null ) {
	$meta_query_obj = new WP_Meta_Query( $meta_query );
	return $meta_query_obj->get_sql( $type, $primary_table, $primary_id_column, $context );
}

/**
 * Retrieves the name of the metadata table for the specified object type.
 *
 * @since 2.9.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $type Type of object metadata is for. Accepts 'blog', 'post', 'comment', 'term',
 *                     'user', or any other object type with an associated meta table.
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
 * @param string $meta_key  Metadata key.
 * @param string $meta_type Optional. Type of object metadata is for. Accepts 'blog', 'post', 'comment', 'term',
 *                          'user', or any other object type with an associated meta table. Default empty string.
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
	 * @param bool   $protected Whether the key is considered protected.
	 * @param string $meta_key  Metadata key.
	 * @param string $meta_type Type of object metadata is for. Accepts 'blog', 'post', 'comment', 'term',
	 *                          'user', or any other object type with an associated meta table.
	 */
	return apply_filters( 'is_protected_meta', $protected, $meta_key, $meta_type );
}

/**
 * Sanitizes meta value.
 *
 * @since 3.1.3
 * @since 4.9.8 The `$object_subtype` parameter was added.
 *
 * @param string $meta_key       Metadata key.
 * @param mixed  $meta_value     Metadata value to sanitize.
 * @param string $object_type    Type of object metadata is for. Accepts 'blog', 'post', 'comment', 'term',
 *                               'user', or any other object type with an associated meta table.
 * @param string $object_subtype Optional. The subtype of the object type. Default empty string.
 * @return mixed Sanitized $meta_value.
 */
function sanitize_meta( $meta_key, $meta_value, $object_type, $object_subtype = '' ) {
	if ( ! empty( $object_subtype ) && has_filter( "sanitize_{$object_type}_meta_{$meta_key}_for_{$object_subtype}" ) ) {

		/**
		 * Filters the sanitization of a specific meta key of a specific meta type and subtype.
		 *
		 * The dynamic portions of the hook name, `$object_type`, `$meta_key`,
		 * and `$object_subtype`, refer to the metadata object type (blog, comment, post, term, or user),
		 * the meta key value, and the object subtype respectively.
		 *
		 * @since 4.9.8
		 *
		 * @param mixed  $meta_value     Metadata value to sanitize.
		 * @param string $meta_key       Metadata key.
		 * @param string $object_type    Type of object metadata is for. Accepts 'blog', 'post', 'comment', 'term',
		 *                               'user', or any other object type with an associated meta table.
		 * @param string $object_subtype Object subtype.
		 */
		return apply_filters( "sanitize_{$object_type}_meta_{$meta_key}_for_{$object_subtype}", $meta_value, $meta_key, $object_type, $object_subtype );
	}

	/**
	 * Filters the sanitization of a specific meta key of a specific meta type.
	 *
	 * The dynamic portions of the hook name, `$meta_type`, and `$meta_key`,
	 * refer to the metadata object type (blog, comment, post, term, or user) and the meta
	 * key value, respectively.
	 *
	 * @since 3.3.0
	 *
	 * @param mixed  $meta_value  Metadata value to sanitize.
	 * @param string $meta_key    Metadata key.
	 * @param string $object_type Type of object metadata is for. Accepts 'blog', 'post', 'comment', 'term',
	 *                            'user', or any other object type with an associated meta table.
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
 * If an object type does not support any subtypes, such as blogs, users, or comments, you should commonly call this function
 * without passing a subtype.
 *
 * @since 3.3.0
 * @since 4.6.0 {@link https://core.trac.wordpress.org/ticket/35658 Modified
 *              to support an array of data to attach to registered meta keys}. Previous arguments for
 *              `$sanitize_callback` and `$auth_callback` have been folded into this array.
 * @since 4.9.8 The `$object_subtype` argument was added to the arguments array.
 * @since 5.3.0 Valid meta types expanded to include "array" and "object".
 * @since 5.5.0 The `$default` argument was added to the arguments array.
 * @since 6.4.0 The `$revisions_enabled` argument was added to the arguments array.
 * @since 6.7.0 The `label` argument was added to the arguments array.
 *
 * @param string       $object_type Type of object metadata is for. Accepts 'blog', 'post', 'comment', 'term',
 *                                  'user', or any other object type with an associated meta table.
 * @param string       $meta_key    Meta key to register.
 * @param array        $args {
 *     Data used to describe the meta key when registered.
 *
 *     @type string     $object_subtype    A subtype; e.g. if the object type is "post", the post type. If left empty,
 *                                         the meta key will be registered on the entire object type. Default empty.
 *     @type string     $type              The type of data associated with this meta key.
 *                                         Valid values are 'string', 'boolean', 'integer', 'number', 'array', and 'object'.
 *     @type string     $label             A human-readable label of the data attached to this meta key.
 *     @type string     $description       A description of the data attached to this meta key.
 *     @type bool       $single            Whether the meta key has one value per object, or an array of values per object.
 *     @type mixed      $default           The default value returned from get_metadata() if no value has been set yet.
 *                                         When using a non-single meta key, the default value is for the first entry.
 *                                         In other words, when calling get_metadata() with `$single` set to `false`,
 *                                         the default value given here will be wrapped in an array.
 *     @type callable   $sanitize_callback A function or method to call when sanitizing `$meta_key` data.
 *     @type callable   $auth_callback     Optional. A function or method to call when performing edit_post_meta,
 *                                         add_post_meta, and delete_post_meta capability checks.
 *     @type bool|array $show_in_rest      Whether data associated with this meta key can be considered public and
 *                                         should be accessible via the REST API. A custom post type must also declare
 *                                         support for custom fields for registered meta to be accessible via REST.
 *                                         When registering complex meta values this argument may optionally be an
 *                                         array with 'schema' or 'prepare_callback' keys instead of a boolean.
 *     @type bool       $revisions_enabled Whether to enable revisions support for this meta_key. Can only be used when the
 *                                         object type is 'post'.
 * }
 * @param string|array $deprecated Deprecated. Use `$args` instead.
 * @return bool True if the meta key was successfully registered in the global array, false if not.
 *              Registering a meta key with distinct sanitize and auth callbacks will fire those callbacks,
 *              but will not add to the global registry.
 */
function register_meta( $object_type, $meta_key, $args, $deprecated = null ) {
	global $wp_meta_keys;

	if ( ! is_array( $wp_meta_keys ) ) {
		$wp_meta_keys = array();
	}

	$defaults = array(
		'object_subtype'    => '',
		'type'              => 'string',
		'label'             => '',
		'description'       => '',
		'default'           => '',
		'single'            => false,
		'sanitize_callback' => null,
		'auth_callback'     => null,
		'show_in_rest'      => false,
		'revisions_enabled' => false,
	);

	// There used to be individual args for sanitize and auth callbacks.
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
	 * @param string $object_type Type of object metadata is for. Accepts 'blog', 'post', 'comment', 'term',
	 *                            'user', or any other object type with an associated meta table.
	 * @param string $meta_key    Meta key.
	 */
	$args = apply_filters( 'register_meta_args', $args, $defaults, $object_type, $meta_key );
	unset( $defaults['default'] );
	$args = wp_parse_args( $args, $defaults );

	// Require an item schema when registering array meta.
	if ( false !== $args['show_in_rest'] && 'array' === $args['type'] ) {
		if ( ! is_array( $args['show_in_rest'] ) || ! isset( $args['show_in_rest']['schema']['items'] ) ) {
			_doing_it_wrong( __FUNCTION__, __( 'When registering an "array" meta type to show in the REST API, you must specify the schema for each array item in "show_in_rest.schema.items".' ), '5.3.0' );

			return false;
		}
	}

	$object_subtype = ! empty( $args['object_subtype'] ) ? $args['object_subtype'] : '';
	if ( $args['revisions_enabled'] ) {
		if ( 'post' !== $object_type ) {
			_doing_it_wrong( __FUNCTION__, __( 'Meta keys cannot enable revisions support unless the object type supports revisions.' ), '6.4.0' );

			return false;
		} elseif ( ! empty( $object_subtype ) && ! post_type_supports( $object_subtype, 'revisions' ) ) {
			_doing_it_wrong( __FUNCTION__, __( 'Meta keys cannot enable revisions support unless the object subtype supports revisions.' ), '6.4.0' );

			return false;
		}
	}

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

	if ( array_key_exists( 'default', $args ) ) {
		$schema = $args;
		if ( is_array( $args['show_in_rest'] ) && isset( $args['show_in_rest']['schema'] ) ) {
			$schema = array_merge( $schema, $args['show_in_rest']['schema'] );
		}

		$check = rest_validate_value_from_schema( $args['default'], $schema );
		if ( is_wp_error( $check ) ) {
			_doing_it_wrong( __FUNCTION__, __( 'When registering a default meta value the data must match the type provided.' ), '5.5.0' );

			return false;
		}

		if ( ! has_filter( "default_{$object_type}_metadata", 'filter_default_metadata' ) ) {
			add_filter( "default_{$object_type}_metadata", 'filter_default_metadata', 10, 5 );
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
 * Filters into default_{$object_type}_metadata and adds in default value.
 *
 * @since 5.5.0
 *
 * @param mixed  $value     Current value passed to filter.
 * @param int    $object_id ID of the object metadata is for.
 * @param string $meta_key  Metadata key.
 * @param bool   $single    If true, return only the first value of the specified `$meta_key`.
 *                          This parameter has no effect if `$meta_key` is not specified.
 * @param string $meta_type Type of object metadata is for. Accepts 'blog', 'post', 'comment', 'term',
 *                          'user', or any other object type with an associated meta table.
 * @return mixed An array of default values if `$single` is false.
 *               The default value of the meta field if `$single` is true.
 */
function filter_default_metadata( $value, $object_id, $meta_key, $single, $meta_type ) {
	global $wp_meta_keys;

	if ( wp_installing() ) {
		return $value;
	}

	if ( ! is_array( $wp_meta_keys ) || ! isset( $wp_meta_keys[ $meta_type ] ) ) {
		return $value;
	}

	$defaults = array();
	foreach ( $wp_meta_keys[ $meta_type ] as $sub_type => $meta_data ) {
		foreach ( $meta_data as $_meta_key => $args ) {
			if ( $_meta_key === $meta_key && array_key_exists( 'default', $args ) ) {
				$defaults[ $sub_type ] = $args;
			}
		}
	}

	if ( ! $defaults ) {
		return $value;
	}

	// If this meta type does not have subtypes, then the default is keyed as an empty string.
	if ( isset( $defaults[''] ) ) {
		$metadata = $defaults[''];
	} else {
		$sub_type = get_object_subtype( $meta_type, $object_id );
		if ( ! isset( $defaults[ $sub_type ] ) ) {
			return $value;
		}
		$metadata = $defaults[ $sub_type ];
	}

	if ( $single ) {
		$value = $metadata['default'];
	} else {
		$value = array( $metadata['default'] );
	}

	return $value;
}

/**
 * Checks if a meta key is registered.
 *
 * @since 4.6.0
 * @since 4.9.8 The `$object_subtype` parameter was added.
 *
 * @param string $object_type    Type of object metadata is for. Accepts 'blog', 'post', 'comment', 'term',
 *                               'user', or any other object type with an associated meta table.
 * @param string $meta_key       Metadata key.
 * @param string $object_subtype Optional. The subtype of the object type. Default empty string.
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
 * @param string $object_type    Type of object metadata is for. Accepts 'blog', 'post', 'comment', 'term',
 *                               'user', or any other object type with an associated meta table.
 * @param string $meta_key       Metadata key.
 * @param string $object_subtype Optional. The subtype of the object type. Default empty string.
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

	// Do some clean up.
	if ( empty( $wp_meta_keys[ $object_type ][ $object_subtype ] ) ) {
		unset( $wp_meta_keys[ $object_type ][ $object_subtype ] );
	}
	if ( empty( $wp_meta_keys[ $object_type ] ) ) {
		unset( $wp_meta_keys[ $object_type ] );
	}

	return true;
}

/**
 * Retrieves a list of registered metadata args for an object type, keyed by their meta keys.
 *
 * @since 4.6.0
 * @since 4.9.8 The `$object_subtype` parameter was added.
 *
 * @param string $object_type    Type of object metadata is for. Accepts 'blog', 'post', 'comment', 'term',
 *                               'user', or any other object type with an associated meta table.
 * @param string $object_subtype Optional. The subtype of the object type. Default empty string.
 * @return array[] List of registered metadata args, keyed by their meta keys.
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
 * @param string $object_type Type of object metadata is for. Accepts 'blog', 'post', 'comment', 'term',
 *                            'user', or any other object type with an associated meta table.
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
 * Filters out `register_meta()` args based on an allowed list.
 *
 * `register_meta()` args may change over time, so requiring the allowed list
 * to be explicitly turned off is a warranty seal of sorts.
 *
 * @access private
 * @since 5.5.0
 *
 * @param array $args         Arguments from `register_meta()`.
 * @param array $default_args Default arguments for `register_meta()`.
 * @return array Filtered arguments.
 */
function _wp_register_meta_args_allowed_list( $args, $default_args ) {
	return array_intersect_key( $args, $default_args );
}

/**
 * Returns the object subtype for a given object ID of a specific type.
 *
 * @since 4.9.8
 *
 * @param string $object_type Type of object metadata is for. Accepts 'blog', 'post', 'comment', 'term',
 *                            'user', or any other object type with an associated meta table.
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
	 * Filters the object subtype identifier.
	 *
	 * The dynamic portion of the hook name, `$object_type`, refers to the meta object type
	 * (blog, post, comment, term, user, or any other type with an associated meta table).
	 *
	 * Possible hook names include:
	 *
	 *  - `get_object_subtype_blog`
	 *  - `get_object_subtype_post`
	 *  - `get_object_subtype_comment`
	 *  - `get_object_subtype_term`
	 *  - `get_object_subtype_user`
	 *
	 * @since 4.9.8
	 *
	 * @param string $object_subtype Object subtype or empty string to override.
	 * @param int    $object_id      ID of the object to get the subtype for.
	 */
	return apply_filters( "get_object_subtype_{$object_type}", $object_subtype, $object_id );
}
