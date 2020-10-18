<?php
/**
 * Site API
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 5.1.0
 */

/**
 * Inserts a new site into the database.
 *
 * @since 5.1.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param array $data {
 *     Data for the new site that should be inserted.
 *
 *     @type string $domain       Site domain. Default empty string.
 *     @type string $path         Site path. Default '/'.
 *     @type int    $network_id   The site's network ID. Default is the current network ID.
 *     @type string $registered   When the site was registered, in SQL datetime format. Default is
 *                                the current time.
 *     @type string $last_updated When the site was last updated, in SQL datetime format. Default is
 *                                the value of $registered.
 *     @type int    $public       Whether the site is public. Default 1.
 *     @type int    $archived     Whether the site is archived. Default 0.
 *     @type int    $mature       Whether the site is mature. Default 0.
 *     @type int    $spam         Whether the site is spam. Default 0.
 *     @type int    $deleted      Whether the site is deleted. Default 0.
 *     @type int    $lang_id      The site's language ID. Currently unused. Default 0.
 *     @type int    $user_id      User ID for the site administrator. Passed to the
 *                                `wp_initialize_site` hook.
 *     @type string $title        Site title. Default is 'Site %d' where %d is the site ID. Passed
 *                                to the `wp_initialize_site` hook.
 *     @type array  $options      Custom option $key => $value pairs to use. Default empty array. Passed
 *                                to the `wp_initialize_site` hook.
 *     @type array  $meta         Custom site metadata $key => $value pairs to use. Default empty array.
 *                                Passed to the `wp_initialize_site` hook.
 * }
 * @return int|WP_Error The new site's ID on success, or error object on failure.
 */
function wp_insert_site( array $data ) {
	global $wpdb;

	$now = current_time( 'mysql', true );

	$defaults = array(
		'domain'       => '',
		'path'         => '/',
		'network_id'   => get_current_network_id(),
		'registered'   => $now,
		'last_updated' => $now,
		'public'       => 1,
		'archived'     => 0,
		'mature'       => 0,
		'spam'         => 0,
		'deleted'      => 0,
		'lang_id'      => 0,
	);

	$prepared_data = wp_prepare_site_data( $data, $defaults );
	if ( is_wp_error( $prepared_data ) ) {
		return $prepared_data;
	}

	if ( false === $wpdb->insert( $wpdb->blogs, $prepared_data ) ) {
		return new WP_Error( 'db_insert_error', __( 'Could not insert site into the database.' ), $wpdb->last_error );
	}

	$site_id = (int) $wpdb->insert_id;

	clean_blog_cache( $site_id );

	$new_site = get_site( $site_id );

	if ( ! $new_site ) {
		return new WP_Error( 'get_site_error', __( 'Could not retrieve site data.' ) );
	}

	/**
	 * Fires once a site has been inserted into the database.
	 *
	 * @since 5.1.0
	 *
	 * @param WP_Site $new_site New site object.
	 */
	do_action( 'wp_insert_site', $new_site );

	// Extract the passed arguments that may be relevant for site initialization.
	$args = array_diff_key( $data, $defaults );
	if ( isset( $args['site_id'] ) ) {
		unset( $args['site_id'] );
	}

	/**
	 * Fires when a site's initialization routine should be executed.
	 *
	 * @since 5.1.0
	 *
	 * @param WP_Site $new_site New site object.
	 * @param array   $args     Arguments for the initialization.
	 */
	do_action( 'wp_initialize_site', $new_site, $args );

	// Only compute extra hook parameters if the deprecated hook is actually in use.
	if ( has_action( 'wpmu_new_blog' ) ) {
		$user_id = ! empty( $args['user_id'] ) ? $args['user_id'] : 0;
		$meta    = ! empty( $args['options'] ) ? $args['options'] : array();

		// WPLANG was passed with `$meta` to the `wpmu_new_blog` hook prior to 5.1.0.
		if ( ! array_key_exists( 'WPLANG', $meta ) ) {
			$meta['WPLANG'] = get_network_option( $new_site->network_id, 'WPLANG' );
		}

		// Rebuild the data expected by the `wpmu_new_blog` hook prior to 5.1.0 using allowed keys.
		// The `$allowed_data_fields` matches the one used in `wpmu_create_blog()`.
		$allowed_data_fields = array( 'public', 'archived', 'mature', 'spam', 'deleted', 'lang_id' );
		$meta                = array_merge( array_intersect_key( $data, array_flip( $allowed_data_fields ) ), $meta );

		/**
		 * Fires immediately after a new site is created.
		 *
		 * @since MU (3.0.0)
		 * @deprecated 5.1.0 Use {@see 'wp_insert_site'} instead.
		 *
		 * @param int    $site_id    Site ID.
		 * @param int    $user_id    User ID.
		 * @param string $domain     Site domain.
		 * @param string $path       Site path.
		 * @param int    $network_id Network ID. Only relevant on multi-network installations.
		 * @param array  $meta       Meta data. Used to set initial site options.
		 */
		do_action_deprecated(
			'wpmu_new_blog',
			array( $new_site->id, $user_id, $new_site->domain, $new_site->path, $new_site->network_id, $meta ),
			'5.1.0',
			'wp_insert_site'
		);
	}

	return (int) $new_site->id;
}

/**
 * Updates a site in the database.
 *
 * @since 5.1.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int   $site_id ID of the site that should be updated.
 * @param array $data    Site data to update. See {@see wp_insert_site()} for the list of supported keys.
 * @return int|WP_Error The updated site's ID on success, or error object on failure.
 */
function wp_update_site( $site_id, array $data ) {
	global $wpdb;

	if ( empty( $site_id ) ) {
		return new WP_Error( 'site_empty_id', __( 'Site ID must not be empty.' ) );
	}

	$old_site = get_site( $site_id );
	if ( ! $old_site ) {
		return new WP_Error( 'site_not_exist', __( 'Site does not exist.' ) );
	}

	$defaults                 = $old_site->to_array();
	$defaults['network_id']   = (int) $defaults['site_id'];
	$defaults['last_updated'] = current_time( 'mysql', true );
	unset( $defaults['blog_id'], $defaults['site_id'] );

	$data = wp_prepare_site_data( $data, $defaults, $old_site );
	if ( is_wp_error( $data ) ) {
		return $data;
	}

	if ( false === $wpdb->update( $wpdb->blogs, $data, array( 'blog_id' => $old_site->id ) ) ) {
		return new WP_Error( 'db_update_error', __( 'Could not update site in the database.' ), $wpdb->last_error );
	}

	clean_blog_cache( $old_site );

	$new_site = get_site( $old_site->id );

	/**
	 * Fires once a site has been updated in the database.
	 *
	 * @since 5.1.0
	 *
	 * @param WP_Site $new_site New site object.
	 * @param WP_Site $old_site Old site object.
	 */
	do_action( 'wp_update_site', $new_site, $old_site );

	return (int) $new_site->id;
}

/**
 * Deletes a site from the database.
 *
 * @since 5.1.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int $site_id ID of the site that should be deleted.
 * @return WP_Site|WP_Error The deleted site object on success, or error object on failure.
 */
function wp_delete_site( $site_id ) {
	global $wpdb;

	if ( empty( $site_id ) ) {
		return new WP_Error( 'site_empty_id', __( 'Site ID must not be empty.' ) );
	}

	$old_site = get_site( $site_id );
	if ( ! $old_site ) {
		return new WP_Error( 'site_not_exist', __( 'Site does not exist.' ) );
	}

	$errors = new WP_Error();

	/**
	 * Fires before a site should be deleted from the database.
	 *
	 * Plugins should amend the `$errors` object via its `WP_Error::add()` method. If any errors
	 * are present, the site will not be deleted.
	 *
	 * @since 5.1.0
	 *
	 * @param WP_Error $errors   Error object to add validation errors to.
	 * @param WP_Site  $old_site The site object to be deleted.
	 */
	do_action( 'wp_validate_site_deletion', $errors, $old_site );

	if ( ! empty( $errors->errors ) ) {
		return $errors;
	}

	/**
	 * Fires before a site is deleted.
	 *
	 * @since MU (3.0.0)
	 * @deprecated 5.1.0
	 *
	 * @param int  $site_id The site ID.
	 * @param bool $drop    True if site's table should be dropped. Default false.
	 */
	do_action_deprecated( 'delete_blog', array( $old_site->id, true ), '5.1.0' );

	/**
	 * Fires when a site's uninitialization routine should be executed.
	 *
	 * @since 5.1.0
	 *
	 * @param WP_Site $old_site Deleted site object.
	 */
	do_action( 'wp_uninitialize_site', $old_site );

	if ( is_site_meta_supported() ) {
		$blog_meta_ids = $wpdb->get_col( $wpdb->prepare( "SELECT meta_id FROM $wpdb->blogmeta WHERE blog_id = %d ", $old_site->id ) );
		foreach ( $blog_meta_ids as $mid ) {
			delete_metadata_by_mid( 'blog', $mid );
		}
	}

	if ( false === $wpdb->delete( $wpdb->blogs, array( 'blog_id' => $old_site->id ) ) ) {
		return new WP_Error( 'db_delete_error', __( 'Could not delete site from the database.' ), $wpdb->last_error );
	}

	clean_blog_cache( $old_site );

	/**
	 * Fires once a site has been deleted from the database.
	 *
	 * @since 5.1.0
	 *
	 * @param WP_Site $old_site Deleted site object.
	 */
	do_action( 'wp_delete_site', $old_site );

	/**
	 * Fires after the site is deleted from the network.
	 *
	 * @since 4.8.0
	 * @deprecated 5.1.0
	 *
	 * @param int  $site_id The site ID.
	 * @param bool $drop    True if site's tables should be dropped. Default false.
	 */
	do_action_deprecated( 'deleted_blog', array( $old_site->id, true ), '5.1.0' );

	return $old_site;
}

/**
 * Retrieves site data given a site ID or site object.
 *
 * Site data will be cached and returned after being passed through a filter.
 * If the provided site is empty, the current site global will be used.
 *
 * @since 4.6.0
 *
 * @param WP_Site|int|null $site Optional. Site to retrieve. Default is the current site.
 * @return WP_Site|null The site object or null if not found.
 */
function get_site( $site = null ) {
	if ( empty( $site ) ) {
		$site = get_current_blog_id();
	}

	if ( $site instanceof WP_Site ) {
		$_site = $site;
	} elseif ( is_object( $site ) ) {
		$_site = new WP_Site( $site );
	} else {
		$_site = WP_Site::get_instance( $site );
	}

	if ( ! $_site ) {
		return null;
	}

	/**
	 * Fires after a site is retrieved.
	 *
	 * @since 4.6.0
	 *
	 * @param WP_Site $_site Site data.
	 */
	$_site = apply_filters( 'get_site', $_site );

	return $_site;
}

/**
 * Adds any sites from the given IDs to the cache that do not already exist in cache.
 *
 * @since 4.6.0
 * @since 5.1.0 Introduced the `$update_meta_cache` parameter.
 * @access private
 *
 * @see update_site_cache()
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param array $ids               ID list.
 * @param bool  $update_meta_cache Optional. Whether to update the meta cache. Default true.
 */
function _prime_site_caches( $ids, $update_meta_cache = true ) {
	global $wpdb;

	$non_cached_ids = _get_non_cached_ids( $ids, 'sites' );
	if ( ! empty( $non_cached_ids ) ) {
		$fresh_sites = $wpdb->get_results( sprintf( "SELECT * FROM $wpdb->blogs WHERE blog_id IN (%s)", implode( ',', array_map( 'intval', $non_cached_ids ) ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		update_site_cache( $fresh_sites, $update_meta_cache );
	}
}

/**
 * Updates sites in cache.
 *
 * @since 4.6.0
 * @since 5.1.0 Introduced the `$update_meta_cache` parameter.
 *
 * @param array $sites             Array of site objects.
 * @param bool  $update_meta_cache Whether to update site meta cache. Default true.
 */
function update_site_cache( $sites, $update_meta_cache = true ) {
	if ( ! $sites ) {
		return;
	}
	$site_ids = array();
	foreach ( $sites as $site ) {
		$site_ids[] = $site->blog_id;
		wp_cache_add( $site->blog_id, $site, 'sites' );
		wp_cache_add( $site->blog_id . 'short', $site, 'blog-details' );
	}

	if ( $update_meta_cache ) {
		update_sitemeta_cache( $site_ids );
	}
}

/**
 * Updates metadata cache for list of site IDs.
 *
 * Performs SQL query to retrieve all metadata for the sites matching `$site_ids` and stores them in the cache.
 * Subsequent calls to `get_site_meta()` will not need to query the database.
 *
 * @since 5.1.0
 *
 * @param array $site_ids List of site IDs.
 * @return array|false An array of metadata on success, false if there is nothing to update.
 */
function update_sitemeta_cache( $site_ids ) {
	// Ensure this filter is hooked in even if the function is called early.
	if ( ! has_filter( 'update_blog_metadata_cache', 'wp_check_site_meta_support_prefilter' ) ) {
		add_filter( 'update_blog_metadata_cache', 'wp_check_site_meta_support_prefilter' );
	}
	return update_meta_cache( 'blog', $site_ids );
}

/**
 * Retrieves a list of sites matching requested arguments.
 *
 * @since 4.6.0
 * @since 4.8.0 Introduced the 'lang_id', 'lang__in', and 'lang__not_in' parameters.
 *
 * @see WP_Site_Query::parse_query()
 *
 * @param string|array $args {
 *     Optional. Array or query string of site query parameters. Default empty.
 *
 *     @type array        $site__in          Array of site IDs to include. Default empty.
 *     @type array        $site__not_in      Array of site IDs to exclude. Default empty.
 *     @type bool         $count             Whether to return a site count (true) or array of site objects.
 *                                           Default false.
 *     @type array        $date_query        Date query clauses to limit sites by. See WP_Date_Query.
 *                                           Default null.
 *     @type string       $fields            Site fields to return. Accepts 'ids' (returns an array of site IDs)
 *                                           or empty (returns an array of complete site objects). Default empty.
 *     @type int          $ID                A site ID to only return that site. Default empty.
 *     @type int          $number            Maximum number of sites to retrieve. Default 100.
 *     @type int          $offset            Number of sites to offset the query. Used to build LIMIT clause.
 *                                           Default 0.
 *     @type bool         $no_found_rows     Whether to disable the `SQL_CALC_FOUND_ROWS` query. Default true.
 *     @type string|array $orderby           Site status or array of statuses. Accepts 'id', 'domain', 'path',
 *                                           'network_id', 'last_updated', 'registered', 'domain_length',
 *                                           'path_length', 'site__in' and 'network__in'. Also accepts false,
 *                                           an empty array, or 'none' to disable `ORDER BY` clause.
 *                                           Default 'id'.
 *     @type string       $order             How to order retrieved sites. Accepts 'ASC', 'DESC'. Default 'ASC'.
 *     @type int          $network_id        Limit results to those affiliated with a given network ID. If 0,
 *                                           include all networks. Default 0.
 *     @type array        $network__in       Array of network IDs to include affiliated sites for. Default empty.
 *     @type array        $network__not_in   Array of network IDs to exclude affiliated sites for. Default empty.
 *     @type string       $domain            Limit results to those affiliated with a given domain. Default empty.
 *     @type array        $domain__in        Array of domains to include affiliated sites for. Default empty.
 *     @type array        $domain__not_in    Array of domains to exclude affiliated sites for. Default empty.
 *     @type string       $path              Limit results to those affiliated with a given path. Default empty.
 *     @type array        $path__in          Array of paths to include affiliated sites for. Default empty.
 *     @type array        $path__not_in      Array of paths to exclude affiliated sites for. Default empty.
 *     @type int          $public            Limit results to public sites. Accepts '1' or '0'. Default empty.
 *     @type int          $archived          Limit results to archived sites. Accepts '1' or '0'. Default empty.
 *     @type int          $mature            Limit results to mature sites. Accepts '1' or '0'. Default empty.
 *     @type int          $spam              Limit results to spam sites. Accepts '1' or '0'. Default empty.
 *     @type int          $deleted           Limit results to deleted sites. Accepts '1' or '0'. Default empty.
 *     @type int          $lang_id           Limit results to a language ID. Default empty.
 *     @type array        $lang__in          Array of language IDs to include affiliated sites for. Default empty.
 *     @type array        $lang__not_in      Array of language IDs to exclude affiliated sites for. Default empty.
 *     @type string       $search            Search term(s) to retrieve matching sites for. Default empty.
 *     @type array        $search_columns    Array of column names to be searched. Accepts 'domain' and 'path'.
 *                                           Default empty array.
 *     @type bool         $update_site_cache Whether to prime the cache for found sites. Default true.
 * }
 * @return array|int List of WP_Site objects, a list of site IDs when 'fields' is set to 'ids',
 *                   or the number of sites when 'count' is passed as a query var.
 */
function get_sites( $args = array() ) {
	$query = new WP_Site_Query();

	return $query->query( $args );
}

/**
 * Prepares site data for insertion or update in the database.
 *
 * @since 5.1.0
 *
 * @param array        $data     Associative array of site data passed to the respective function.
 *                               See {@see wp_insert_site()} for the possibly included data.
 * @param array        $defaults Site data defaults to parse $data against.
 * @param WP_Site|null $old_site Optional. Old site object if an update, or null if an insertion.
 *                               Default null.
 * @return array|WP_Error Site data ready for a database transaction, or WP_Error in case a validation
 *                        error occurred.
 */
function wp_prepare_site_data( $data, $defaults, $old_site = null ) {

	// Maintain backward-compatibility with `$site_id` as network ID.
	if ( isset( $data['site_id'] ) ) {
		if ( ! empty( $data['site_id'] ) && empty( $data['network_id'] ) ) {
			$data['network_id'] = $data['site_id'];
		}
		unset( $data['site_id'] );
	}

	/**
	 * Filters passed site data in order to normalize it.
	 *
	 * @since 5.1.0
	 *
	 * @param array $data Associative array of site data passed to the respective function.
	 *                    See {@see wp_insert_site()} for the possibly included data.
	 */
	$data = apply_filters( 'wp_normalize_site_data', $data );

	$allowed_data_fields = array( 'domain', 'path', 'network_id', 'registered', 'last_updated', 'public', 'archived', 'mature', 'spam', 'deleted', 'lang_id' );
	$data                = array_intersect_key( wp_parse_args( $data, $defaults ), array_flip( $allowed_data_fields ) );

	$errors = new WP_Error();

	/**
	 * Fires when data should be validated for a site prior to inserting or updating in the database.
	 *
	 * Plugins should amend the `$errors` object via its `WP_Error::add()` method.
	 *
	 * @since 5.1.0
	 *
	 * @param WP_Error     $errors   Error object to add validation errors to.
	 * @param array        $data     Associative array of complete site data. See {@see wp_insert_site()}
	 *                               for the included data.
	 * @param WP_Site|null $old_site The old site object if the data belongs to a site being updated,
	 *                               or null if it is a new site being inserted.
	 */
	do_action( 'wp_validate_site_data', $errors, $data, $old_site );

	if ( ! empty( $errors->errors ) ) {
		return $errors;
	}

	// Prepare for database.
	$data['site_id'] = $data['network_id'];
	unset( $data['network_id'] );

	return $data;
}

/**
 * Normalizes data for a site prior to inserting or updating in the database.
 *
 * @since 5.1.0
 *
 * @param array $data Associative array of site data passed to the respective function.
 *                    See {@see wp_insert_site()} for the possibly included data.
 * @return array Normalized site data.
 */
function wp_normalize_site_data( $data ) {
	// Sanitize domain if passed.
	if ( array_key_exists( 'domain', $data ) ) {
		$data['domain'] = trim( $data['domain'] );
		$data['domain'] = preg_replace( '/\s+/', '', sanitize_user( $data['domain'], true ) );
		if ( is_subdomain_install() ) {
			$data['domain'] = str_replace( '@', '', $data['domain'] );
		}
	}

	// Sanitize path if passed.
	if ( array_key_exists( 'path', $data ) ) {
		$data['path'] = trailingslashit( '/' . trim( $data['path'], '/' ) );
	}

	// Sanitize network ID if passed.
	if ( array_key_exists( 'network_id', $data ) ) {
		$data['network_id'] = (int) $data['network_id'];
	}

	// Sanitize status fields if passed.
	$status_fields = array( 'public', 'archived', 'mature', 'spam', 'deleted' );
	foreach ( $status_fields as $status_field ) {
		if ( array_key_exists( $status_field, $data ) ) {
			$data[ $status_field ] = (int) $data[ $status_field ];
		}
	}

	// Strip date fields if empty.
	$date_fields = array( 'registered', 'last_updated' );
	foreach ( $date_fields as $date_field ) {
		if ( ! array_key_exists( $date_field, $data ) ) {
			continue;
		}

		if ( empty( $data[ $date_field ] ) || '0000-00-00 00:00:00' === $data[ $date_field ] ) {
			unset( $data[ $date_field ] );
		}
	}

	return $data;
}

/**
 * Validates data for a site prior to inserting or updating in the database.
 *
 * @since 5.1.0
 *
 * @param WP_Error     $errors   Error object, passed by reference. Will contain validation errors if
 *                               any occurred.
 * @param array        $data     Associative array of complete site data. See {@see wp_insert_site()}
 *                               for the included data.
 * @param WP_Site|null $old_site The old site object if the data belongs to a site being updated,
 *                               or null if it is a new site being inserted.
 */
function wp_validate_site_data( $errors, $data, $old_site = null ) {
	// A domain must always be present.
	if ( empty( $data['domain'] ) ) {
		$errors->add( 'site_empty_domain', __( 'Site domain must not be empty.' ) );
	}

	// A path must always be present.
	if ( empty( $data['path'] ) ) {
		$errors->add( 'site_empty_path', __( 'Site path must not be empty.' ) );
	}

	// A network ID must always be present.
	if ( empty( $data['network_id'] ) ) {
		$errors->add( 'site_empty_network_id', __( 'Site network ID must be provided.' ) );
	}

	// Both registration and last updated dates must always be present and valid.
	$date_fields = array( 'registered', 'last_updated' );
	foreach ( $date_fields as $date_field ) {
		if ( empty( $data[ $date_field ] ) ) {
			$errors->add( 'site_empty_' . $date_field, __( 'Both registration and last updated dates must be provided.' ) );
			break;
		}

		// Allow '0000-00-00 00:00:00', although it be stripped out at this point.
		if ( '0000-00-00 00:00:00' !== $data[ $date_field ] ) {
			$month      = substr( $data[ $date_field ], 5, 2 );
			$day        = substr( $data[ $date_field ], 8, 2 );
			$year       = substr( $data[ $date_field ], 0, 4 );
			$valid_date = wp_checkdate( $month, $day, $year, $data[ $date_field ] );
			if ( ! $valid_date ) {
				$errors->add( 'site_invalid_' . $date_field, __( 'Both registration and last updated dates must be valid dates.' ) );
				break;
			}
		}
	}

	if ( ! empty( $errors->errors ) ) {
		return;
	}

	// If a new site, or domain/path/network ID have changed, ensure uniqueness.
	if ( ! $old_site
		|| $data['domain'] !== $old_site->domain
		|| $data['path'] !== $old_site->path
		|| $data['network_id'] !== $old_site->network_id
	) {
		if ( domain_exists( $data['domain'], $data['path'], $data['network_id'] ) ) {
			$errors->add( 'site_taken', __( 'Sorry, that site already exists!' ) );
		}
	}
}

/**
 * Runs the initialization routine for a given site.
 *
 * This process includes creating the site's database tables and
 * populating them with defaults.
 *
 * @since 5.1.0
 *
 * @global wpdb     $wpdb     WordPress database abstraction object.
 * @global WP_Roles $wp_roles WordPress role management object.
 *
 * @param int|WP_Site $site_id Site ID or object.
 * @param array       $args    {
 *     Optional. Arguments to modify the initialization behavior.
 *
 *     @type int    $user_id Required. User ID for the site administrator.
 *     @type string $title   Site title. Default is 'Site %d' where %d is the
 *                           site ID.
 *     @type array  $options Custom option $key => $value pairs to use. Default
 *                           empty array.
 *     @type array  $meta    Custom site metadata $key => $value pairs to use.
 *                           Default empty array.
 * }
 * @return bool|WP_Error True on success, or error object on failure.
 */
function wp_initialize_site( $site_id, array $args = array() ) {
	global $wpdb, $wp_roles;

	if ( empty( $site_id ) ) {
		return new WP_Error( 'site_empty_id', __( 'Site ID must not be empty.' ) );
	}

	$site = get_site( $site_id );
	if ( ! $site ) {
		return new WP_Error( 'site_invalid_id', __( 'Site with the ID does not exist.' ) );
	}

	if ( wp_is_site_initialized( $site ) ) {
		return new WP_Error( 'site_already_initialized', __( 'The site appears to be already initialized.' ) );
	}

	$network = get_network( $site->network_id );
	if ( ! $network ) {
		$network = get_network();
	}

	$args = wp_parse_args(
		$args,
		array(
			'user_id' => 0,
			/* translators: %d: Site ID. */
			'title'   => sprintf( __( 'Site %d' ), $site->id ),
			'options' => array(),
			'meta'    => array(),
		)
	);

	/**
	 * Filters the arguments for initializing a site.
	 *
	 * @since 5.1.0
	 *
	 * @param array      $args    Arguments to modify the initialization behavior.
	 * @param WP_Site    $site    Site that is being initialized.
	 * @param WP_Network $network Network that the site belongs to.
	 */
	$args = apply_filters( 'wp_initialize_site_args', $args, $site, $network );

	$orig_installing = wp_installing();
	if ( ! $orig_installing ) {
		wp_installing( true );
	}

	$switch = false;
	if ( get_current_blog_id() !== $site->id ) {
		$switch = true;
		switch_to_blog( $site->id );
	}

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	// Set up the database tables.
	make_db_current_silent( 'blog' );

	$home_scheme    = 'http';
	$siteurl_scheme = 'http';
	if ( ! is_subdomain_install() ) {
		if ( 'https' === parse_url( get_home_url( $network->site_id ), PHP_URL_SCHEME ) ) {
			$home_scheme = 'https';
		}
		if ( 'https' === parse_url( get_network_option( $network->id, 'siteurl' ), PHP_URL_SCHEME ) ) {
			$siteurl_scheme = 'https';
		}
	}

	// Populate the site's options.
	populate_options(
		array_merge(
			array(
				'home'        => untrailingslashit( $home_scheme . '://' . $site->domain . $site->path ),
				'siteurl'     => untrailingslashit( $siteurl_scheme . '://' . $site->domain . $site->path ),
				'blogname'    => wp_unslash( $args['title'] ),
				'admin_email' => '',
				'upload_path' => get_network_option( $network->id, 'ms_files_rewriting' ) ? UPLOADBLOGSDIR . "/{$site->id}/files" : get_blog_option( $network->site_id, 'upload_path' ),
				'blog_public' => (int) $site->public,
				'WPLANG'      => get_network_option( $network->id, 'WPLANG' ),
			),
			$args['options']
		)
	);

	// Clean blog cache after populating options.
	clean_blog_cache( $site );

	// Populate the site's roles.
	populate_roles();
	$wp_roles = new WP_Roles();

	// Populate metadata for the site.
	populate_site_meta( $site->id, $args['meta'] );

	// Remove all permissions that may exist for the site.
	$table_prefix = $wpdb->get_blog_prefix();
	delete_metadata( 'user', 0, $table_prefix . 'user_level', null, true );   // Delete all.
	delete_metadata( 'user', 0, $table_prefix . 'capabilities', null, true ); // Delete all.

	// Install default site content.
	wp_install_defaults( $args['user_id'] );

	// Set the site administrator.
	add_user_to_blog( $site->id, $args['user_id'], 'administrator' );
	if ( ! user_can( $args['user_id'], 'manage_network' ) && ! get_user_meta( $args['user_id'], 'primary_blog', true ) ) {
		update_user_meta( $args['user_id'], 'primary_blog', $site->id );
	}

	if ( $switch ) {
		restore_current_blog();
	}

	wp_installing( $orig_installing );

	return true;
}

/**
 * Runs the uninitialization routine for a given site.
 *
 * This process includes dropping the site's database tables and deleting its uploads directory.
 *
 * @since 5.1.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int|WP_Site $site_id Site ID or object.
 * @return bool|WP_Error True on success, or error object on failure.
 */
function wp_uninitialize_site( $site_id ) {
	global $wpdb;

	if ( empty( $site_id ) ) {
		return new WP_Error( 'site_empty_id', __( 'Site ID must not be empty.' ) );
	}

	$site = get_site( $site_id );
	if ( ! $site ) {
		return new WP_Error( 'site_invalid_id', __( 'Site with the ID does not exist.' ) );
	}

	if ( ! wp_is_site_initialized( $site ) ) {
		return new WP_Error( 'site_already_uninitialized', __( 'The site appears to be already uninitialized.' ) );
	}

	$users = get_users(
		array(
			'blog_id' => $site->id,
			'fields'  => 'ids',
		)
	);

	// Remove users from the site.
	if ( ! empty( $users ) ) {
		foreach ( $users as $user_id ) {
			remove_user_from_blog( $user_id, $site->id );
		}
	}

	$switch = false;
	if ( get_current_blog_id() !== $site->id ) {
		$switch = true;
		switch_to_blog( $site->id );
	}

	$uploads = wp_get_upload_dir();

	$tables = $wpdb->tables( 'blog' );

	/**
	 * Filters the tables to drop when the site is deleted.
	 *
	 * @since MU (3.0.0)
	 *
	 * @param string[] $tables  Array of names of the site tables to be dropped.
	 * @param int      $site_id The ID of the site to drop tables for.
	 */
	$drop_tables = apply_filters( 'wpmu_drop_tables', $tables, $site->id );

	foreach ( (array) $drop_tables as $table ) {
		$wpdb->query( "DROP TABLE IF EXISTS `$table`" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * Filters the upload base directory to delete when the site is deleted.
	 *
	 * @since MU (3.0.0)
	 *
	 * @param string $uploads['basedir'] Uploads path without subdirectory. @see wp_upload_dir()
	 * @param int    $site_id            The site ID.
	 */
	$dir     = apply_filters( 'wpmu_delete_blog_upload_dir', $uploads['basedir'], $site->id );
	$dir     = rtrim( $dir, DIRECTORY_SEPARATOR );
	$top_dir = $dir;
	$stack   = array( $dir );
	$index   = 0;

	while ( $index < count( $stack ) ) {
		// Get indexed directory from stack.
		$dir = $stack[ $index ];

		// phpcs:disable WordPress.PHP.NoSilencedErrors.Discouraged
		$dh = @opendir( $dir );
		if ( $dh ) {
			$file = @readdir( $dh );
			while ( false !== $file ) {
				if ( '.' === $file || '..' === $file ) {
					$file = @readdir( $dh );
					continue;
				}

				if ( @is_dir( $dir . DIRECTORY_SEPARATOR . $file ) ) {
					$stack[] = $dir . DIRECTORY_SEPARATOR . $file;
				} elseif ( @is_file( $dir . DIRECTORY_SEPARATOR . $file ) ) {
					@unlink( $dir . DIRECTORY_SEPARATOR . $file );
				}

				$file = @readdir( $dh );
			}
			@closedir( $dh );
		}
		$index++;
	}

	$stack = array_reverse( $stack ); // Last added directories are deepest.
	foreach ( (array) $stack as $dir ) {
		if ( $dir != $top_dir ) {
			@rmdir( $dir );
		}
	}

	// phpcs:enable WordPress.PHP.NoSilencedErrors.Discouraged
	if ( $switch ) {
		restore_current_blog();
	}

	return true;
}

/**
 * Checks whether a site is initialized.
 *
 * A site is considered initialized when its database tables are present.
 *
 * @since 5.1.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int|WP_Site $site_id Site ID or object.
 * @return bool True if the site is initialized, false otherwise.
 */
function wp_is_site_initialized( $site_id ) {
	global $wpdb;

	if ( is_object( $site_id ) ) {
		$site_id = $site_id->blog_id;
	}
	$site_id = (int) $site_id;

	/**
	 * Filters the check for whether a site is initialized before the database is accessed.
	 *
	 * Returning a non-null value will effectively short-circuit the function, returning
	 * that value instead.
	 *
	 * @since 5.1.0
	 *
	 * @param bool|null $pre     The value to return instead. Default null
	 *                           to continue with the check.
	 * @param int       $site_id The site ID that is being checked.
	 */
	$pre = apply_filters( 'pre_wp_is_site_initialized', null, $site_id );
	if ( null !== $pre ) {
		return (bool) $pre;
	}

	$switch = false;
	if ( get_current_blog_id() !== $site_id ) {
		$switch = true;
		remove_action( 'switch_blog', 'wp_switch_roles_and_user', 1 );
		switch_to_blog( $site_id );
	}

	$suppress = $wpdb->suppress_errors();
	$result   = (bool) $wpdb->get_results( "DESCRIBE {$wpdb->posts}" );
	$wpdb->suppress_errors( $suppress );

	if ( $switch ) {
		restore_current_blog();
		add_action( 'switch_blog', 'wp_switch_roles_and_user', 1, 2 );
	}

	return $result;
}

/**
 * Clean the blog cache
 *
 * @since 3.5.0
 *
 * @global bool $_wp_suspend_cache_invalidation
 *
 * @param WP_Site|int $blog The site object or ID to be cleared from cache.
 */
function clean_blog_cache( $blog ) {
	global $_wp_suspend_cache_invalidation;

	if ( ! empty( $_wp_suspend_cache_invalidation ) ) {
		return;
	}

	if ( empty( $blog ) ) {
		return;
	}

	$blog_id = $blog;
	$blog    = get_site( $blog_id );
	if ( ! $blog ) {
		if ( ! is_numeric( $blog_id ) ) {
			return;
		}

		// Make sure a WP_Site object exists even when the site has been deleted.
		$blog = new WP_Site(
			(object) array(
				'blog_id' => $blog_id,
				'domain'  => null,
				'path'    => null,
			)
		);
	}

	$blog_id         = $blog->blog_id;
	$domain_path_key = md5( $blog->domain . $blog->path );

	wp_cache_delete( $blog_id, 'sites' );
	wp_cache_delete( $blog_id, 'site-details' );
	wp_cache_delete( $blog_id, 'blog-details' );
	wp_cache_delete( $blog_id . 'short', 'blog-details' );
	wp_cache_delete( $domain_path_key, 'blog-lookup' );
	wp_cache_delete( $domain_path_key, 'blog-id-cache' );
	wp_cache_delete( $blog_id, 'blog_meta' );

	/**
	 * Fires immediately after a site has been removed from the object cache.
	 *
	 * @since 4.6.0
	 *
	 * @param int     $id              Blog ID.
	 * @param WP_Site $blog            Site object.
	 * @param string  $domain_path_key md5 hash of domain and path.
	 */
	do_action( 'clean_site_cache', $blog_id, $blog, $domain_path_key );

	wp_cache_set( 'last_changed', microtime(), 'sites' );

	/**
	 * Fires after the blog details cache is cleared.
	 *
	 * @since 3.4.0
	 * @deprecated 4.9.0 Use {@see 'clean_site_cache'} instead.
	 *
	 * @param int $blog_id Blog ID.
	 */
	do_action_deprecated( 'refresh_blog_details', array( $blog_id ), '4.9.0', 'clean_site_cache' );
}

/**
 * Adds metadata to a site.
 *
 * @since 5.1.0
 *
 * @param int    $site_id    Site ID.
 * @param string $meta_key   Metadata name.
 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
 * @param bool   $unique     Optional. Whether the same key should not be added.
 *                           Default false.
 * @return int|false Meta ID on success, false on failure.
 */
function add_site_meta( $site_id, $meta_key, $meta_value, $unique = false ) {
	return add_metadata( 'blog', $site_id, $meta_key, $meta_value, $unique );
}

/**
 * Removes metadata matching criteria from a site.
 *
 * You can match based on the key, or key and value. Removing based on key and
 * value, will keep from removing duplicate metadata with the same key. It also
 * allows removing all metadata matching key, if needed.
 *
 * @since 5.1.0
 *
 * @param int    $site_id    Site ID.
 * @param string $meta_key   Metadata name.
 * @param mixed  $meta_value Optional. Metadata value. If provided,
 *                           rows will only be removed that match the value.
 *                           Must be serializable if non-scalar. Default empty.
 * @return bool True on success, false on failure.
 */
function delete_site_meta( $site_id, $meta_key, $meta_value = '' ) {
	return delete_metadata( 'blog', $site_id, $meta_key, $meta_value );
}

/**
 * Retrieves metadata for a site.
 *
 * @since 5.1.0
 *
 * @param int    $site_id Site ID.
 * @param string $key     Optional. The meta key to retrieve. By default,
 *                        returns data for all keys. Default empty.
 * @param bool   $single  Optional. Whether to return a single value.
 *                        This parameter has no effect if $key is not specified.
 *                        Default false.
 * @return mixed An array if $single is false. The value of meta data field
 *               if $single is true. False for an invalid $site_id.
 */
function get_site_meta( $site_id, $key = '', $single = false ) {
	return get_metadata( 'blog', $site_id, $key, $single );
}

/**
 * Updates metadata for a site.
 *
 * Use the $prev_value parameter to differentiate between meta fields with the
 * same key and site ID.
 *
 * If the meta field for the site does not exist, it will be added.
 *
 * @since 5.1.0
 *
 * @param int    $site_id    Site ID.
 * @param string $meta_key   Metadata key.
 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
 * @param mixed  $prev_value Optional. Previous value to check before updating.
 *                           If specified, only update existing metadata entries with
 *                           this value. Otherwise, update all entries. Default empty.
 * @return int|bool Meta ID if the key didn't exist, true on successful update,
 *                  false on failure or if the value passed to the function
 *                  is the same as the one that is already in the database.
 */
function update_site_meta( $site_id, $meta_key, $meta_value, $prev_value = '' ) {
	return update_metadata( 'blog', $site_id, $meta_key, $meta_value, $prev_value );
}

/**
 * Deletes everything from site meta matching meta key.
 *
 * @since 5.1.0
 *
 * @param string $meta_key Metadata key to search for when deleting.
 * @return bool Whether the site meta key was deleted from the database.
 */
function delete_site_meta_by_key( $meta_key ) {
	return delete_metadata( 'blog', null, $meta_key, '', true );
}

/**
 * Updates the count of sites for a network based on a changed site.
 *
 * @since 5.1.0
 *
 * @param WP_Site      $new_site The site object that has been inserted, updated or deleted.
 * @param WP_Site|null $old_site Optional. If $new_site has been updated, this must be the previous
 *                               state of that site. Default null.
 */
function wp_maybe_update_network_site_counts_on_update( $new_site, $old_site = null ) {
	if ( null === $old_site ) {
		wp_maybe_update_network_site_counts( $new_site->network_id );
		return;
	}

	if ( $new_site->network_id != $old_site->network_id ) {
		wp_maybe_update_network_site_counts( $new_site->network_id );
		wp_maybe_update_network_site_counts( $old_site->network_id );
	}
}

/**
 * Triggers actions on site status updates.
 *
 * @since 5.1.0
 *
 * @param WP_Site      $new_site The site object after the update.
 * @param WP_Site|null $old_site Optional. If $new_site has been updated, this must be the previous
 *                               state of that site. Default null.
 */
function wp_maybe_transition_site_statuses_on_update( $new_site, $old_site = null ) {
	$site_id = $new_site->id;

	// Use the default values for a site if no previous state is given.
	if ( ! $old_site ) {
		$old_site = new WP_Site( new stdClass() );
	}

	if ( $new_site->spam != $old_site->spam ) {
		if ( 1 == $new_site->spam ) {

			/**
			 * Fires when the 'spam' status is added to a site.
			 *
			 * @since MU (3.0.0)
			 *
			 * @param int $site_id Site ID.
			 */
			do_action( 'make_spam_blog', $site_id );
		} else {

			/**
			 * Fires when the 'spam' status is removed from a site.
			 *
			 * @since MU (3.0.0)
			 *
			 * @param int $site_id Site ID.
			 */
			do_action( 'make_ham_blog', $site_id );
		}
	}

	if ( $new_site->mature != $old_site->mature ) {
		if ( 1 == $new_site->mature ) {

			/**
			 * Fires when the 'mature' status is added to a site.
			 *
			 * @since 3.1.0
			 *
			 * @param int $site_id Site ID.
			 */
			do_action( 'mature_blog', $site_id );
		} else {

			/**
			 * Fires when the 'mature' status is removed from a site.
			 *
			 * @since 3.1.0
			 *
			 * @param int $site_id Site ID.
			 */
			do_action( 'unmature_blog', $site_id );
		}
	}

	if ( $new_site->archived != $old_site->archived ) {
		if ( 1 == $new_site->archived ) {

			/**
			 * Fires when the 'archived' status is added to a site.
			 *
			 * @since MU (3.0.0)
			 *
			 * @param int $site_id Site ID.
			 */
			do_action( 'archive_blog', $site_id );
		} else {

			/**
			 * Fires when the 'archived' status is removed from a site.
			 *
			 * @since MU (3.0.0)
			 *
			 * @param int $site_id Site ID.
			 */
			do_action( 'unarchive_blog', $site_id );
		}
	}

	if ( $new_site->deleted != $old_site->deleted ) {
		if ( 1 == $new_site->deleted ) {

			/**
			 * Fires when the 'deleted' status is added to a site.
			 *
			 * @since 3.5.0
			 *
			 * @param int $site_id Site ID.
			 */
			do_action( 'make_delete_blog', $site_id );
		} else {

			/**
			 * Fires when the 'deleted' status is removed from a site.
			 *
			 * @since 3.5.0
			 *
			 * @param int $site_id Site ID.
			 */
			do_action( 'make_undelete_blog', $site_id );
		}
	}

	if ( $new_site->public != $old_site->public ) {

		/**
		 * Fires after the current blog's 'public' setting is updated.
		 *
		 * @since MU (3.0.0)
		 *
		 * @param int    $site_id Site ID.
		 * @param string $value   The value of the site status.
		 */
		do_action( 'update_blog_public', $site_id, $new_site->public );
	}
}

/**
 * Cleans the necessary caches after specific site data has been updated.
 *
 * @since 5.1.0
 *
 * @param WP_Site $new_site The site object after the update.
 * @param WP_Site $old_site The site obejct prior to the update.
 */
function wp_maybe_clean_new_site_cache_on_update( $new_site, $old_site ) {
	if ( $old_site->domain !== $new_site->domain || $old_site->path !== $new_site->path ) {
		clean_blog_cache( $new_site );
	}
}

/**
 * Updates the `blog_public` option for a given site ID.
 *
 * @since 5.1.0
 *
 * @param int    $site_id Site ID.
 * @param string $public  The value of the site status.
 */
function wp_update_blog_public_option_on_site_update( $site_id, $public ) {

	// Bail if the site's database tables do not exist (yet).
	if ( ! wp_is_site_initialized( $site_id ) ) {
		return;
	}

	update_blog_option( $site_id, 'blog_public', $public );
}

/**
 * Sets the last changed time for the 'sites' cache group.
 *
 * @since 5.1.0
 */
function wp_cache_set_sites_last_changed() {
	wp_cache_set( 'last_changed', microtime(), 'sites' );
}

/**
 * Aborts calls to site meta if it is not supported.
 *
 * @since 5.1.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param mixed $check Skip-value for whether to proceed site meta function execution.
 * @return mixed Original value of $check, or false if site meta is not supported.
 */
function wp_check_site_meta_support_prefilter( $check ) {
	if ( ! is_site_meta_supported() ) {
		/* translators: %s: Database table name. */
		_doing_it_wrong( __FUNCTION__, sprintf( __( 'The %s table is not installed. Please run the network database upgrade.' ), $GLOBALS['wpdb']->blogmeta ), '5.1.0' );
		return false;
	}

	return $check;
}
