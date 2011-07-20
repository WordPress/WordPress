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
 * @return bool The meta ID on successful update, false on failure.
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
	$meta_value = stripslashes_deep($meta_value);
	$meta_value = sanitize_meta( $meta_key, $meta_value, $meta_type );

	$check = apply_filters( "add_{$meta_type}_metadata", null, $object_id, $meta_key, $meta_value, $unique );
	if ( null !== $check )
		return $check;

	if ( $unique && $wpdb->get_var( $wpdb->prepare(
		"SELECT COUNT(*) FROM $table WHERE meta_key = %s AND $column = %d",
		$meta_key, $object_id ) ) )
		return false;

	$_meta_value = $meta_value;
	$meta_value = maybe_serialize( $meta_value );

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
	// users cache stores usermeta that must be cleared.
	if ( 'user' == $meta_type )
		clean_user_cache($object_id);

	do_action( "added_{$meta_type}_meta", $mid, $object_id, $meta_key, $_meta_value );

	return $mid;
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
	$meta_value = stripslashes_deep($meta_value);
	$meta_value = sanitize_meta( $meta_key, $meta_value, $meta_type );

	$check = apply_filters( "update_{$meta_type}_metadata", null, $object_id, $meta_key, $meta_value, $prev_value );
	if ( null !== $check )
		return (bool) $check;

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
	$meta_value = maybe_serialize( $meta_value );

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
	$meta_value = stripslashes_deep($meta_value);

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

	do_action( "delete_{$meta_type}_meta", $meta_ids, $object_id, $meta_key, $_meta_value );

	$query = "DELETE FROM $table WHERE $id_column IN( " . implode( ',', $meta_ids ) . " )";

	$count = $wpdb->query($query);

	if ( !$count )
		return false;

	wp_cache_delete($object_id, $meta_type . '_meta');
	// users cache stores usermeta that must be cleared.
	if ( 'user' == $meta_type )
		clean_user_cache($object_id);

	do_action( "deleted_{$meta_type}_meta", $meta_ids, $object_id, $meta_key, $_meta_value );

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

	if ( !$meta_key )
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

	if ( ! $meta_type )
		return false;

	if ( !$meta_id = absint( $meta_id ) )
		return false;

	if ( ! $table = _get_meta_table($meta_type) )
		return false;

	$id_column = ( 'user' == $meta_type ) ? 'umeta_id' : 'meta_id';

	$meta = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE $id_column = %d", $meta_id ) );

	if ( empty( $meta ) )
		return false;

	if ( isset( $meta->meta_value ) )
		$meta->meta_value = maybe_unserialize( $meta->meta_value );

	return $meta;
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
	$id_list = join(',', $ids);
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
		wp_cache_add( $id, $cache[$id], $cache_key );
	}

	return $cache;
}

/**
 * Given a meta query, generates SQL clauses to be appended to a main query
 *
 * @since 3.2.0
 *
 * @see WP_Meta_Query
 *
 * @param array (optional) $meta_query A meta query
 * @param string $type Type of meta
 * @param string $primary_table
 * @param string $primary_id_column
 * @param object $context (optional) The main query object
 * @return array( 'join' => $join_sql, 'where' => $where_sql )
 */
function get_meta_sql( $meta_query = false, $type, $primary_table, $primary_id_column, $context = null ) {
	$meta_query_obj = new WP_Meta_Query( $meta_query );
	return $meta_query_obj->get_sql( $type, $primary_table, $primary_id_column, $context );
}

/**
 * Container class for a multiple metadata query
 *
 * @since 3.2.0
 */
class WP_Meta_Query {
	/**
	* List of metadata queries. A single query is an associative array:
	* - 'key' string The meta key
	* - 'value' string|array The meta value
	* - 'compare' (optional) string How to compare the key to the value.
	*              Possible values: '=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN'.
	*              Default: '='
	* - 'type' string (optional) The type of the value.
	*              Possible values: 'NUMERIC', 'BINARY', 'CHAR', 'DATE', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED'.
	*              Default: 'CHAR'
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
	 * Constructor
	 *
	 * @param array (optional) $meta_query A meta query
	 */
	function __construct( $meta_query = false ) {
		if ( !$meta_query )
			return;

		if ( isset( $meta_query['relation'] ) && strtoupper( $meta_query['relation'] ) == 'OR' ) {
			$this->relation = 'OR';
		} else {
			$this->relation = 'AND';
		}

		$this->queries = array();

		foreach ( $meta_query as $key => $query ) {
			if ( ! is_array( $query ) )
				continue;

			$this->queries[] = $query;
		}
	}

	/**
	 * Constructs a meta query based on 'meta_*' query vars
	 *
	 * @since 3.2.0
	 * @access public
	 *
	 * @param array $qv The query variables
	 */
	function parse_query_vars( $qv ) {
		$meta_query = array();

		// Simple query needs to be first for orderby=meta_value to work correctly
		foreach ( array( 'key', 'compare', 'type' ) as $key ) {
			if ( !empty( $qv[ "meta_$key" ] ) )
				$meta_query[0][ $key ] = $qv[ "meta_$key" ];
		}

		// WP_Query sets 'meta_value' = '' by default
		if ( isset( $qv[ 'meta_value' ] ) && '' !== $qv[ 'meta_value' ] )
			$meta_query[0]['value'] = $qv[ 'meta_value' ];

		if ( !empty( $qv['meta_query'] ) && is_array( $qv['meta_query'] ) ) {
			$meta_query = array_merge( $meta_query, $qv['meta_query'] );
		}

		$this->__construct( $meta_query );
	}

	/**
	 * Generates SQL clauses to be appended to a main query.
	 *
	 * @since 3.2.0
	 * @access public
	 *
	 * @param string $type Type of meta
	 * @param string $primary_table
	 * @param string $primary_id_column
	 * @param object $context (optional) The main query object
	 * @return array( 'join' => $join_sql, 'where' => $where_sql )
	 */
	function get_sql( $type, $primary_table, $primary_id_column, $context = null ) {
		global $wpdb;

		if ( ! $meta_table = _get_meta_table( $type ) )
			return false;

		$meta_id_column = esc_sql( $type . '_id' );

		$join = array();
		$where = array();

		foreach ( $this->queries as $k => $q ) {
			$meta_key = isset( $q['key'] ) ? trim( $q['key'] ) : '';
			$meta_compare = isset( $q['compare'] ) ? strtoupper( $q['compare'] ) : '=';
			$meta_type = isset( $q['type'] ) ? strtoupper( $q['type'] ) : 'CHAR';

			if ( ! in_array( $meta_compare, array( '=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' ) ) )
				$meta_compare = '=';

			if ( 'NUMERIC' == $meta_type )
				$meta_type = 'SIGNED';
			elseif ( ! in_array( $meta_type, array( 'BINARY', 'CHAR', 'DATE', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED' ) ) )
				$meta_type = 'CHAR';

			$i = count( $join );
			$alias = $i ? 'mt' . $i : $meta_table;

			// Set JOIN
			$join[$i]  = "INNER JOIN $meta_table";
			$join[$i] .= $i ? " AS $alias" : '';
			$join[$i] .= " ON ($primary_table.$primary_id_column = $alias.$meta_id_column)";

			$where[$k] = '';
			if ( !empty( $meta_key ) )
				$where[$k] = $wpdb->prepare( "$alias.meta_key = %s", $meta_key );

			if ( !isset( $q['value'] ) ) {
				if ( empty( $where[$k] ) )
					unset( $join[$i] );
				continue;
			}

			$meta_value = $q['value'];

			if ( in_array( $meta_compare, array( 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' ) ) ) {
				if ( ! is_array( $meta_value ) )
					$meta_value = preg_split( '/[,\s]+/', $meta_value );

				if ( empty( $meta_value ) ) {
					unset( $join[$i] );
					continue;
				}
			} else {
				$meta_value = trim( $meta_value );
			}

			if ( 'IN' == substr( $meta_compare, -2) ) {
				$meta_compare_string = '(' . substr( str_repeat( ',%s', count( $meta_value ) ), 1 ) . ')';
			} elseif ( 'BETWEEN' == substr( $meta_compare, -7) ) {
				$meta_value = array_slice( $meta_value, 0, 2 );
				$meta_compare_string = '%s AND %s';
			} elseif ( 'LIKE' == substr( $meta_compare, -4 ) ) {
				$meta_value = '%' . like_escape( $meta_value ) . '%';
				$meta_compare_string = '%s';
			} else {
				$meta_compare_string = '%s';
			}

			if ( ! empty( $where[$k] ) )
				$where[$k] .= ' AND ';

			$where[$k] = ' (' . $where[$k] . $wpdb->prepare( "CAST($alias.meta_value AS {$meta_type}) {$meta_compare} {$meta_compare_string})", $meta_value );
		}

		$where = array_filter( $where );

		if ( empty( $where ) )
			$where = '';
		else
			$where = ' AND (' . implode( "\n{$this->relation} ", $where ) . ' )';

		$join = implode( "\n", $join );
		if ( ! empty( $join ) )
			$join = ' ' . $join;

		return apply_filters_ref_array( 'get_meta_sql', array( compact( 'join', 'where' ), $this->queries, $type, $primary_table, $primary_id_column, $context ) );
	}
}

/**
 * Retrieve the name of the metadata table for the specified object type.
 *
 * @since 2.9.0
 * @uses $wpdb WordPress database object for queries.
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
 * Determine whether a meta key is protected
 *
 * @since 3.1.3
 *
 * @param string $meta_key Meta key
 * @return bool True if the key is protected, false otherwise.
 */
function is_protected_meta( $meta_key, $meta_type = null ) {
	$protected = ( '_' == $meta_key[0] );

	return apply_filters( 'is_protected_meta', $protected, $meta_key, $meta_type );
}

/**
 * Sanitize meta value
 *
 * @since 3.1.3
 *
 * @param string $meta_key Meta key
 * @param mixed $meta_value Meta value to sanitize
 * @param string $meta_type Type of meta
 * @return mixed Sanitized $meta_value
 */
function sanitize_meta( $meta_key, $meta_value, $meta_type ) {
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
 * @param array $args Arguments
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

?>
