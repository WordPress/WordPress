<?php

/**
 * Site/blog functions that work with the blogs table and related data.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since MU
 */

/**
 * Update the last_updated field for the current site.
 *
 * @since MU
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 */
function wpmu_update_blogs_date() {
	global $wpdb;

	update_blog_details( $wpdb->blogid, array('last_updated' => current_time('mysql', true)) );
	/**
	 * Fires after the blog details are updated.
	 *
	 * @since MU
	 *
	 * @param int $blog_id Site ID.
	 */
	do_action( 'wpmu_blog_updated', $wpdb->blogid );
}

/**
 * Get a full blog URL, given a blog id.
 *
 * @since MU
 *
 * @param int $blog_id Blog ID
 * @return string Full URL of the blog if found. Empty string if not.
 */
function get_blogaddress_by_id( $blog_id ) {
	$bloginfo = get_site( (int) $blog_id );

	if ( empty( $bloginfo ) ) {
		return '';
	}

	$scheme = parse_url( $bloginfo->home, PHP_URL_SCHEME );
	$scheme = empty( $scheme ) ? 'http' : $scheme;

	return esc_url( $scheme . '://' . $bloginfo->domain . $bloginfo->path );
}

/**
 * Get a full blog URL, given a blog name.
 *
 * @since MU
 *
 * @param string $blogname The (subdomain or directory) name
 * @return string
 */
function get_blogaddress_by_name( $blogname ) {
	if ( is_subdomain_install() ) {
		if ( $blogname == 'main' )
			$blogname = 'www';
		$url = rtrim( network_home_url(), '/' );
		if ( !empty( $blogname ) )
			$url = preg_replace( '|^([^\.]+://)|', "\${1}" . $blogname . '.', $url );
	} else {
		$url = network_home_url( $blogname );
	}
	return esc_url( $url . '/' );
}

/**
 * Retrieves a sites ID given its (subdomain or directory) slug.
 *
 * @since MU
 * @since 4.7.0 Converted to use get_sites().
 *
 * @param string $slug A site's slug.
 * @return int|null The site ID, or null if no site is found for the given slug.
 */
function get_id_from_blogname( $slug ) {
	$current_network = get_network();
	$slug = trim( $slug, '/' );

	if ( is_subdomain_install() ) {
		$domain = $slug . '.' . preg_replace( '|^www\.|', '', $current_network->domain );
		$path = $current_network->path;
	} else {
		$domain = $current_network->domain;
		$path = $current_network->path . $slug . '/';
	}

	$site_ids = get_sites( array(
		'number' => 1,
		'fields' => 'ids',
		'domain' => $domain,
		'path' => $path,
	) );

	if ( empty( $site_ids ) ) {
		return null;
	}

	return array_shift( $site_ids );
}

/**
 * Retrieve the details for a blog from the blogs table and blog options.
 *
 * @since MU
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int|string|array $fields  Optional. A blog ID, a blog slug, or an array of fields to query against.
 *                                  If not specified the current blog ID is used.
 * @param bool             $get_all Whether to retrieve all details or only the details in the blogs table.
 *                                  Default is true.
 * @return WP_Site|false Blog details on success. False on failure.
 */
function get_blog_details( $fields = null, $get_all = true ) {
	global $wpdb;

	if ( is_array($fields ) ) {
		if ( isset($fields['blog_id']) ) {
			$blog_id = $fields['blog_id'];
		} elseif ( isset($fields['domain']) && isset($fields['path']) ) {
			$key = md5( $fields['domain'] . $fields['path'] );
			$blog = wp_cache_get($key, 'blog-lookup');
			if ( false !== $blog )
				return $blog;
			if ( substr( $fields['domain'], 0, 4 ) == 'www.' ) {
				$nowww = substr( $fields['domain'], 4 );
				$blog = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->blogs WHERE domain IN (%s,%s) AND path = %s ORDER BY CHAR_LENGTH(domain) DESC", $nowww, $fields['domain'], $fields['path'] ) );
			} else {
				$blog = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->blogs WHERE domain = %s AND path = %s", $fields['domain'], $fields['path'] ) );
			}
			if ( $blog ) {
				wp_cache_set($blog->blog_id . 'short', $blog, 'blog-details');
				$blog_id = $blog->blog_id;
			} else {
				return false;
			}
		} elseif ( isset($fields['domain']) && is_subdomain_install() ) {
			$key = md5( $fields['domain'] );
			$blog = wp_cache_get($key, 'blog-lookup');
			if ( false !== $blog )
				return $blog;
			if ( substr( $fields['domain'], 0, 4 ) == 'www.' ) {
				$nowww = substr( $fields['domain'], 4 );
				$blog = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->blogs WHERE domain IN (%s,%s) ORDER BY CHAR_LENGTH(domain) DESC", $nowww, $fields['domain'] ) );
			} else {
				$blog = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->blogs WHERE domain = %s", $fields['domain'] ) );
			}
			if ( $blog ) {
				wp_cache_set($blog->blog_id . 'short', $blog, 'blog-details');
				$blog_id = $blog->blog_id;
			} else {
				return false;
			}
		} else {
			return false;
		}
	} else {
		if ( ! $fields )
			$blog_id = get_current_blog_id();
		elseif ( ! is_numeric( $fields ) )
			$blog_id = get_id_from_blogname( $fields );
		else
			$blog_id = $fields;
	}

	$blog_id = (int) $blog_id;

	$all = $get_all == true ? '' : 'short';
	$details = wp_cache_get( $blog_id . $all, 'blog-details' );

	if ( $details ) {
		if ( ! is_object( $details ) ) {
			if ( $details == -1 ) {
				return false;
			} else {
				// Clear old pre-serialized objects. Cache clients do better with that.
				wp_cache_delete( $blog_id . $all, 'blog-details' );
				unset($details);
			}
		} else {
			return $details;
		}
	}

	// Try the other cache.
	if ( $get_all ) {
		$details = wp_cache_get( $blog_id . 'short', 'blog-details' );
	} else {
		$details = wp_cache_get( $blog_id, 'blog-details' );
		// If short was requested and full cache is set, we can return.
		if ( $details ) {
			if ( ! is_object( $details ) ) {
				if ( $details == -1 ) {
					return false;
				} else {
					// Clear old pre-serialized objects. Cache clients do better with that.
					wp_cache_delete( $blog_id, 'blog-details' );
					unset($details);
				}
			} else {
				return $details;
			}
		}
	}

	if ( empty($details) ) {
		$details = WP_Site::get_instance( $blog_id );
		if ( ! $details ) {
			// Set the full cache.
			wp_cache_set( $blog_id, -1, 'blog-details' );
			return false;
		}
	}

	if ( ! $details instanceof WP_Site ) {
		$details = new WP_Site( $details );
	}

	if ( ! $get_all ) {
		wp_cache_set( $blog_id . $all, $details, 'blog-details' );
		return $details;
	}

	switch_to_blog( $blog_id );
	$details->blogname   = get_option( 'blogname' );
	$details->siteurl    = get_option( 'siteurl' );
	$details->post_count = get_option( 'post_count' );
	$details->home       = get_option( 'home' );
	restore_current_blog();

	/**
	 * Filters a blog's details.
	 *
	 * @since MU
	 * @deprecated 4.7.0 Use site_details
	 *
	 * @param object $details The blog details.
	 */
	$details = apply_filters_deprecated( 'blog_details', array( $details ), '4.7.0', 'site_details' );

	wp_cache_set( $blog_id . $all, $details, 'blog-details' );

	$key = md5( $details->domain . $details->path );
	wp_cache_set( $key, $details, 'blog-lookup' );

	return $details;
}

/**
 * Clear the blog details cache.
 *
 * @since MU
 *
 * @param int $blog_id Optional. Blog ID. Defaults to current blog.
 */
function refresh_blog_details( $blog_id = 0 ) {
	$blog_id = (int) $blog_id;
	if ( ! $blog_id ) {
		$blog_id = get_current_blog_id();
	}

	$details = get_site( $blog_id );
	if ( ! $details ) {
		// Make sure clean_blog_cache() gets the blog ID
		// when the blog has been previously cached as
		// non-existent.
		$details = (object) array(
			'blog_id' => $blog_id,
			'domain' => null,
			'path' => null
		);
	}

	clean_blog_cache( $details );

	/**
	 * Fires after the blog details cache is cleared.
	 *
	 * @since 3.4.0
	 *
	 * @param int $blog_id Blog ID.
	 */
	do_action( 'refresh_blog_details', $blog_id );
}

/**
 * Update the details for a blog. Updates the blogs table for a given blog id.
 *
 * @since MU
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int   $blog_id Blog ID
 * @param array $details Array of details keyed by blogs table field names.
 * @return bool True if update succeeds, false otherwise.
 */
function update_blog_details( $blog_id, $details = array() ) {
	global $wpdb;

	if ( empty($details) )
		return false;

	if ( is_object($details) )
		$details = get_object_vars($details);

	$current_details = get_site( $blog_id );
	if ( empty($current_details) )
		return false;

	$current_details = get_object_vars($current_details);

	$details = array_merge($current_details, $details);
	$details['last_updated'] = current_time('mysql', true);

	$update_details = array();
	$fields = array( 'site_id', 'domain', 'path', 'registered', 'last_updated', 'public', 'archived', 'mature', 'spam', 'deleted', 'lang_id');
	foreach ( array_intersect( array_keys( $details ), $fields ) as $field ) {
		if ( 'path' === $field ) {
			$details[ $field ] = trailingslashit( '/' . trim( $details[ $field ], '/' ) );
		}

		$update_details[ $field ] = $details[ $field ];
	}

	$result = $wpdb->update( $wpdb->blogs, $update_details, array('blog_id' => $blog_id) );

	if ( false === $result )
		return false;

	// If spam status changed, issue actions.
	if ( $details['spam'] != $current_details['spam'] ) {
		if ( $details['spam'] == 1 ) {
			/**
			 * Fires when the blog status is changed to 'spam'.
			 *
			 * @since MU
			 *
			 * @param int $blog_id Blog ID.
			 */
			do_action( 'make_spam_blog', $blog_id );
		} else {
			/**
			 * Fires when the blog status is changed to 'ham'.
			 *
			 * @since MU
			 *
			 * @param int $blog_id Blog ID.
			 */
			do_action( 'make_ham_blog', $blog_id );
		}
	}

	// If mature status changed, issue actions.
	if ( $details['mature'] != $current_details['mature'] ) {
		if ( $details['mature'] == 1 ) {
			/**
			 * Fires when the blog status is changed to 'mature'.
			 *
			 * @since 3.1.0
			 *
			 * @param int $blog_id Blog ID.
			 */
			do_action( 'mature_blog', $blog_id );
		} else {
			/**
			 * Fires when the blog status is changed to 'unmature'.
			 *
			 * @since 3.1.0
			 *
			 * @param int $blog_id Blog ID.
			 */
			do_action( 'unmature_blog', $blog_id );
		}
	}

	// If archived status changed, issue actions.
	if ( $details['archived'] != $current_details['archived'] ) {
		if ( $details['archived'] == 1 ) {
			/**
			 * Fires when the blog status is changed to 'archived'.
			 *
			 * @since MU
			 *
			 * @param int $blog_id Blog ID.
			 */
			do_action( 'archive_blog', $blog_id );
		} else {
			/**
			 * Fires when the blog status is changed to 'unarchived'.
			 *
			 * @since MU
			 *
			 * @param int $blog_id Blog ID.
			 */
			do_action( 'unarchive_blog', $blog_id );
		}
	}

	// If deleted status changed, issue actions.
	if ( $details['deleted'] != $current_details['deleted'] ) {
		if ( $details['deleted'] == 1 ) {
			/**
			 * Fires when the blog status is changed to 'deleted'.
			 *
			 * @since 3.5.0
			 *
			 * @param int $blog_id Blog ID.
			 */
			do_action( 'make_delete_blog', $blog_id );
		} else {
			/**
			 * Fires when the blog status is changed to 'undeleted'.
			 *
			 * @since 3.5.0
			 *
			 * @param int $blog_id Blog ID.
			 */
			do_action( 'make_undelete_blog', $blog_id );
		}
	}

	if ( isset( $details['public'] ) ) {
		switch_to_blog( $blog_id );
		update_option( 'blog_public', $details['public'] );
		restore_current_blog();
	}

	refresh_blog_details($blog_id);

	return true;
}

/**
 * Clean the blog cache
 *
 * @since 3.5.0
 *
 * @global bool $_wp_suspend_cache_invalidation
 *
 * @param WP_Site $blog The site object to be cleared from cache.
 */
function clean_blog_cache( $blog ) {
	global $_wp_suspend_cache_invalidation;

	if ( ! empty( $_wp_suspend_cache_invalidation ) ) {
		return;
	}

	$blog_id = $blog->blog_id;
	$domain_path_key = md5( $blog->domain . $blog->path );

	wp_cache_delete( $blog_id, 'sites' );
	wp_cache_delete( $blog_id, 'site-details' );
	wp_cache_delete( $blog_id, 'blog-details' );
	wp_cache_delete( $blog_id . 'short' , 'blog-details' );
	wp_cache_delete( $domain_path_key, 'blog-lookup' );
	wp_cache_delete( $domain_path_key, 'blog-id-cache' );
	wp_cache_delete( 'current_blog_' . $blog->domain, 'site-options' );
	wp_cache_delete( 'current_blog_' . $blog->domain . $blog->path, 'site-options' );

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
}

/**
 * Cleans the site details cache for a site.
 *
 * @since 4.7.4
 *
 * @param int $site_id Optional. Site ID. Default is the current site ID.
 */
function clean_site_details_cache( $site_id = 0 ) {
	$site_id = (int) $site_id;
	if ( ! $site_id ) {
		$site_id = get_current_blog_id();
	}

	wp_cache_delete( $site_id, 'site-details' );
	wp_cache_delete( $site_id, 'blog-details' );
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
 * Adds any sites from the given ids to the cache that do not already exist in cache.
 *
 * @since 4.6.0
 * @access private
 *
 * @see update_site_cache()
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param array $ids ID list.
 */
function _prime_site_caches( $ids ) {
	global $wpdb;

	$non_cached_ids = _get_non_cached_ids( $ids, 'sites' );
	if ( ! empty( $non_cached_ids ) ) {
		$fresh_sites = $wpdb->get_results( sprintf( "SELECT * FROM $wpdb->blogs WHERE blog_id IN (%s)", join( ",", array_map( 'intval', $non_cached_ids ) ) ) );

		update_site_cache( $fresh_sites );
	}
}

/**
 * Updates sites in cache.
 *
 * @since 4.6.0
 *
 * @param array $sites Array of site objects.
 */
function update_site_cache( $sites ) {
	if ( ! $sites ) {
		return;
	}

	foreach ( $sites as $site ) {
		wp_cache_add( $site->blog_id, $site, 'sites' );
		wp_cache_add( $site->blog_id . 'short', $site, 'blog-details' );
	}
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
 *     @type bool         $update_site_cache Whether to prime the cache for found sites. Default false.
 * }
 * @return array List of sites.
 */
function get_sites( $args = array() ) {
	$query = new WP_Site_Query();

	return $query->query( $args );
}

/**
 * Retrieve option value for a given blog id based on name of option.
 *
 * If the option does not exist or does not have a value, then the return value
 * will be false. This is useful to check whether you need to install an option
 * and is commonly used during installation of plugin options and to test
 * whether upgrading is required.
 *
 * If the option was serialized then it will be unserialized when it is returned.
 *
 * @since MU
 *
 * @param int    $id      A blog ID. Can be null to refer to the current blog.
 * @param string $option  Name of option to retrieve. Expected to not be SQL-escaped.
 * @param mixed  $default Optional. Default value to return if the option does not exist.
 * @return mixed Value set for the option.
 */
function get_blog_option( $id, $option, $default = false ) {
	$id = (int) $id;

	if ( empty( $id ) )
		$id = get_current_blog_id();

	if ( get_current_blog_id() == $id )
		return get_option( $option, $default );

	switch_to_blog( $id );
	$value = get_option( $option, $default );
	restore_current_blog();

	/**
	 * Filters a blog option value.
	 *
	 * The dynamic portion of the hook name, `$option`, refers to the blog option name.
	 *
	 * @since 3.5.0
	 *
	 * @param string  $value The option value.
	 * @param int     $id    Blog ID.
	 */
	return apply_filters( "blog_option_{$option}", $value, $id );
}

/**
 * Add a new option for a given blog id.
 *
 * You do not need to serialize values. If the value needs to be serialized, then
 * it will be serialized before it is inserted into the database. Remember,
 * resources can not be serialized or added as an option.
 *
 * You can create options without values and then update the values later.
 * Existing options will not be updated and checks are performed to ensure that you
 * aren't adding a protected WordPress option. Care should be taken to not name
 * options the same as the ones which are protected.
 *
 * @since MU
 *
 * @param int    $id     A blog ID. Can be null to refer to the current blog.
 * @param string $option Name of option to add. Expected to not be SQL-escaped.
 * @param mixed  $value  Optional. Option value, can be anything. Expected to not be SQL-escaped.
 * @return bool False if option was not added and true if option was added.
 */
function add_blog_option( $id, $option, $value ) {
	$id = (int) $id;

	if ( empty( $id ) )
		$id = get_current_blog_id();

	if ( get_current_blog_id() == $id )
		return add_option( $option, $value );

	switch_to_blog( $id );
	$return = add_option( $option, $value );
	restore_current_blog();

	return $return;
}

/**
 * Removes option by name for a given blog id. Prevents removal of protected WordPress options.
 *
 * @since MU
 *
 * @param int    $id     A blog ID. Can be null to refer to the current blog.
 * @param string $option Name of option to remove. Expected to not be SQL-escaped.
 * @return bool True, if option is successfully deleted. False on failure.
 */
function delete_blog_option( $id, $option ) {
	$id = (int) $id;

	if ( empty( $id ) )
		$id = get_current_blog_id();

	if ( get_current_blog_id() == $id )
		return delete_option( $option );

	switch_to_blog( $id );
	$return = delete_option( $option );
	restore_current_blog();

	return $return;
}

/**
 * Update an option for a particular blog.
 *
 * @since MU
 *
 * @param int    $id         The blog id.
 * @param string $option     The option key.
 * @param mixed  $value      The option value.
 * @param mixed  $deprecated Not used.
 * @return bool True on success, false on failure.
 */
function update_blog_option( $id, $option, $value, $deprecated = null ) {
	$id = (int) $id;

	if ( null !== $deprecated  )
		_deprecated_argument( __FUNCTION__, '3.1.0' );

	if ( get_current_blog_id() == $id )
		return update_option( $option, $value );

	switch_to_blog( $id );
	$return = update_option( $option, $value );
	restore_current_blog();

	return $return;
}

/**
 * Switch the current blog.
 *
 * This function is useful if you need to pull posts, or other information,
 * from other blogs. You can switch back afterwards using restore_current_blog().
 *
 * Things that aren't switched:
 *  - autoloaded options. See #14992
 *  - plugins. See #14941
 *
 * @see restore_current_blog()
 * @since MU
 *
 * @global wpdb            $wpdb
 * @global int             $blog_id
 * @global array           $_wp_switched_stack
 * @global bool            $switched
 * @global string          $table_prefix
 * @global WP_Object_Cache $wp_object_cache
 *
 * @param int  $new_blog   The id of the blog you want to switch to. Default: current blog
 * @param bool $deprecated Deprecated argument
 * @return true Always returns True.
 */
function switch_to_blog( $new_blog, $deprecated = null ) {
	global $wpdb, $wp_roles;

	$blog_id = get_current_blog_id();
	if ( empty( $new_blog ) ) {
		$new_blog = $blog_id;
	}

	$GLOBALS['_wp_switched_stack'][] = $blog_id;

	/*
	 * If we're switching to the same blog id that we're on,
	 * set the right vars, do the associated actions, but skip
	 * the extra unnecessary work
	 */
	if ( $new_blog == $blog_id ) {
		/**
		 * Fires when the blog is switched.
		 *
		 * @since MU
		 *
		 * @param int $new_blog New blog ID.
		 * @param int $new_blog Blog ID.
		 */
		do_action( 'switch_blog', $new_blog, $new_blog );
		$GLOBALS['switched'] = true;
		return true;
	}

	$wpdb->set_blog_id( $new_blog );
	$GLOBALS['table_prefix'] = $wpdb->get_blog_prefix();
	$prev_blog_id = $blog_id;
	$GLOBALS['blog_id'] = $new_blog;

	if ( function_exists( 'wp_cache_switch_to_blog' ) ) {
		wp_cache_switch_to_blog( $new_blog );
	} else {
		global $wp_object_cache;

		if ( is_object( $wp_object_cache ) && isset( $wp_object_cache->global_groups ) ) {
			$global_groups = $wp_object_cache->global_groups;
		} else {
			$global_groups = false;
		}
		wp_cache_init();

		if ( function_exists( 'wp_cache_add_global_groups' ) ) {
			if ( is_array( $global_groups ) ) {
				wp_cache_add_global_groups( $global_groups );
			} else {
				wp_cache_add_global_groups( array( 'users', 'userlogins', 'usermeta', 'user_meta', 'useremail', 'userslugs', 'site-transient', 'site-options', 'site-lookup', 'blog-lookup', 'blog-details', 'rss', 'global-posts', 'blog-id-cache', 'networks', 'sites', 'site-details' ) );
			}
			wp_cache_add_non_persistent_groups( array( 'counts', 'plugins' ) );
		}
	}

	if ( did_action( 'init' ) ) {
		$wp_roles = new WP_Roles();
		$current_user = wp_get_current_user();
		$current_user->for_blog( $new_blog );
	}

	/** This filter is documented in wp-includes/ms-blogs.php */
	do_action( 'switch_blog', $new_blog, $prev_blog_id );
	$GLOBALS['switched'] = true;

	return true;
}

/**
 * Restore the current blog, after calling switch_to_blog()
 *
 * @see switch_to_blog()
 * @since MU
 *
 * @global wpdb            $wpdb
 * @global array           $_wp_switched_stack
 * @global int             $blog_id
 * @global bool            $switched
 * @global string          $table_prefix
 * @global WP_Object_Cache $wp_object_cache
 *
 * @return bool True on success, false if we're already on the current blog
 */
function restore_current_blog() {
	global $wpdb, $wp_roles;

	if ( empty( $GLOBALS['_wp_switched_stack'] ) ) {
		return false;
	}

	$blog = array_pop( $GLOBALS['_wp_switched_stack'] );
	$blog_id = get_current_blog_id();

	if ( $blog_id == $blog ) {
		/** This filter is documented in wp-includes/ms-blogs.php */
		do_action( 'switch_blog', $blog, $blog );
		// If we still have items in the switched stack, consider ourselves still 'switched'
		$GLOBALS['switched'] = ! empty( $GLOBALS['_wp_switched_stack'] );
		return true;
	}

	$wpdb->set_blog_id( $blog );
	$prev_blog_id = $blog_id;
	$GLOBALS['blog_id'] = $blog;
	$GLOBALS['table_prefix'] = $wpdb->get_blog_prefix();

	if ( function_exists( 'wp_cache_switch_to_blog' ) ) {
		wp_cache_switch_to_blog( $blog );
	} else {
		global $wp_object_cache;

		if ( is_object( $wp_object_cache ) && isset( $wp_object_cache->global_groups ) ) {
			$global_groups = $wp_object_cache->global_groups;
		} else {
			$global_groups = false;
		}

		wp_cache_init();

		if ( function_exists( 'wp_cache_add_global_groups' ) ) {
			if ( is_array( $global_groups ) ) {
				wp_cache_add_global_groups( $global_groups );
			} else {
				wp_cache_add_global_groups( array( 'users', 'userlogins', 'usermeta', 'user_meta', 'useremail', 'userslugs', 'site-transient', 'site-options', 'site-lookup', 'blog-lookup', 'blog-details', 'rss', 'global-posts', 'blog-id-cache', 'networks', 'sites', 'site-details' ) );
			}
			wp_cache_add_non_persistent_groups( array( 'counts', 'plugins' ) );
		}
	}

	if ( did_action( 'init' ) ) {
		$wp_roles = new WP_Roles();
		$current_user = wp_get_current_user();
		$current_user->for_blog( $blog );
	}

	/** This filter is documented in wp-includes/ms-blogs.php */
	do_action( 'switch_blog', $blog, $prev_blog_id );

	// If we still have items in the switched stack, consider ourselves still 'switched'
	$GLOBALS['switched'] = ! empty( $GLOBALS['_wp_switched_stack'] );

	return true;
}

/**
 * Determines if switch_to_blog() is in effect
 *
 * @since 3.5.0
 *
 * @global array $_wp_switched_stack
 *
 * @return bool True if switched, false otherwise.
 */
function ms_is_switched() {
	return ! empty( $GLOBALS['_wp_switched_stack'] );
}

/**
 * Check if a particular blog is archived.
 *
 * @since MU
 *
 * @param int $id The blog id
 * @return string Whether the blog is archived or not
 */
function is_archived( $id ) {
	return get_blog_status($id, 'archived');
}

/**
 * Update the 'archived' status of a particular blog.
 *
 * @since MU
 *
 * @param int    $id       The blog id
 * @param string $archived The new status
 * @return string $archived
 */
function update_archived( $id, $archived ) {
	update_blog_status($id, 'archived', $archived);
	return $archived;
}

/**
 * Update a blog details field.
 *
 * @since MU
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int    $blog_id BLog ID
 * @param string $pref    A field name
 * @param string $value   Value for $pref
 * @param null   $deprecated
 * @return string|false $value
 */
function update_blog_status( $blog_id, $pref, $value, $deprecated = null ) {
	global $wpdb;

	if ( null !== $deprecated  )
		_deprecated_argument( __FUNCTION__, '3.1.0' );

	if ( ! in_array( $pref, array( 'site_id', 'domain', 'path', 'registered', 'last_updated', 'public', 'archived', 'mature', 'spam', 'deleted', 'lang_id') ) )
		return $value;

	$result = $wpdb->update( $wpdb->blogs, array($pref => $value, 'last_updated' => current_time('mysql', true)), array('blog_id' => $blog_id) );

	if ( false === $result )
		return false;

	refresh_blog_details( $blog_id );

	if ( 'spam' == $pref ) {
		if ( $value == 1 ) {
			/** This filter is documented in wp-includes/ms-blogs.php */
			do_action( 'make_spam_blog', $blog_id );
		} else {
			/** This filter is documented in wp-includes/ms-blogs.php */
			do_action( 'make_ham_blog', $blog_id );
		}
	} elseif ( 'mature' == $pref ) {
		if ( $value == 1 ) {
			/** This filter is documented in wp-includes/ms-blogs.php */
			do_action( 'mature_blog', $blog_id );
		} else {
			/** This filter is documented in wp-includes/ms-blogs.php */
			do_action( 'unmature_blog', $blog_id );
		}
	} elseif ( 'archived' == $pref ) {
		if ( $value == 1 ) {
			/** This filter is documented in wp-includes/ms-blogs.php */
			do_action( 'archive_blog', $blog_id );
		} else {
			/** This filter is documented in wp-includes/ms-blogs.php */
			do_action( 'unarchive_blog', $blog_id );
		}
	} elseif ( 'deleted' == $pref ) {
		if ( $value == 1 ) {
			/** This filter is documented in wp-includes/ms-blogs.php */
			do_action( 'make_delete_blog', $blog_id );
		} else {
			/** This filter is documented in wp-includes/ms-blogs.php */
			do_action( 'make_undelete_blog', $blog_id );
		}
	} elseif ( 'public' == $pref ) {
		/**
		 * Fires after the current blog's 'public' setting is updated.
		 *
		 * @since MU
		 *
		 * @param int    $blog_id Blog ID.
		 * @param string $value   The value of blog status.
 		 */
		do_action( 'update_blog_public', $blog_id, $value ); // Moved here from update_blog_public().
	}

	return $value;
}

/**
 * Get a blog details field.
 *
 * @since MU
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int    $id   The blog id
 * @param string $pref A field name
 * @return bool|string|null $value
 */
function get_blog_status( $id, $pref ) {
	global $wpdb;

	$details = get_site( $id );
	if ( $details )
		return $details->$pref;

	return $wpdb->get_var( $wpdb->prepare("SELECT %s FROM {$wpdb->blogs} WHERE blog_id = %d", $pref, $id) );
}

/**
 * Get a list of most recently updated blogs.
 *
 * @since MU
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param mixed $deprecated Not used
 * @param int   $start      The offset
 * @param int   $quantity   The maximum number of blogs to retrieve. Default is 40.
 * @return array The list of blogs
 */
function get_last_updated( $deprecated = '', $start = 0, $quantity = 40 ) {
	global $wpdb;

	if ( ! empty( $deprecated ) )
		_deprecated_argument( __FUNCTION__, 'MU' ); // never used

	return $wpdb->get_results( $wpdb->prepare("SELECT blog_id, domain, path FROM $wpdb->blogs WHERE site_id = %d AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' AND last_updated != '0000-00-00 00:00:00' ORDER BY last_updated DESC limit %d, %d", $wpdb->siteid, $start, $quantity ) , ARRAY_A );
}

/**
 * Retrieves a list of networks.
 *
 * @since 4.6.0
 *
 * @param string|array $args Optional. Array or string of arguments. See WP_Network_Query::parse_query()
 *                           for information on accepted arguments. Default empty array.
 * @return int|array List of networks or number of found networks if `$count` argument is true.
 */
function get_networks( $args = array() ) {
	$query = new WP_Network_Query();

	return $query->query( $args );
}

/**
 * Retrieves network data given a network ID or network object.
 *
 * Network data will be cached and returned after being passed through a filter.
 * If the provided network is empty, the current network global will be used.
 *
 * @since 4.6.0
 *
 * @global WP_Network $current_site
 *
 * @param WP_Network|int|null $network Optional. Network to retrieve. Default is the current network.
 * @return WP_Network|null The network object or null if not found.
 */
function get_network( $network = null ) {
	global $current_site;
	if ( empty( $network ) && isset( $current_site ) ) {
		$network = $current_site;
	}

	if ( $network instanceof WP_Network ) {
		$_network = $network;
	} elseif ( is_object( $network ) ) {
		$_network = new WP_Network( $network );
	} else {
		$_network = WP_Network::get_instance( $network );
	}

	if ( ! $_network ) {
		return null;
	}

	/**
	 * Fires after a network is retrieved.
	 *
	 * @since 4.6.0
	 *
	 * @param WP_Network $_network Network data.
	 */
	$_network = apply_filters( 'get_network', $_network );

	return $_network;
}

/**
 * Removes a network from the object cache.
 *
 * @since 4.6.0
 *
 * @global bool $_wp_suspend_cache_invalidation
 *
 * @param int|array $ids Network ID or an array of network IDs to remove from cache.
 */
function clean_network_cache( $ids ) {
	global $_wp_suspend_cache_invalidation;

	if ( ! empty( $_wp_suspend_cache_invalidation ) ) {
		return;
	}

	foreach ( (array) $ids as $id ) {
		wp_cache_delete( $id, 'networks' );

		/**
		 * Fires immediately after a network has been removed from the object cache.
		 *
		 * @since 4.6.0
		 *
		 * @param int $id Network ID.
		 */
		do_action( 'clean_network_cache', $id );
	}

	wp_cache_set( 'last_changed', microtime(), 'networks' );
}

/**
 * Updates the network cache of given networks.
 *
 * Will add the networks in $networks to the cache. If network ID already exists
 * in the network cache then it will not be updated. The network is added to the
 * cache using the network group with the key using the ID of the networks.
 *
 * @since 4.6.0
 *
 * @param array $networks Array of network row objects.
 */
function update_network_cache( $networks ) {
	foreach ( (array) $networks as $network ) {
		wp_cache_add( $network->id, $network, 'networks' );
	}
}

/**
 * Adds any networks from the given IDs to the cache that do not already exist in cache.
 *
 * @since 4.6.0
 * @access private
 *
 * @see update_network_cache()
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param array $network_ids Array of network IDs.
 */
function _prime_network_caches( $network_ids ) {
	global $wpdb;

	$non_cached_ids = _get_non_cached_ids( $network_ids, 'networks' );
	if ( !empty( $non_cached_ids ) ) {
		$fresh_networks = $wpdb->get_results( sprintf( "SELECT $wpdb->site.* FROM $wpdb->site WHERE id IN (%s)", join( ",", array_map( 'intval', $non_cached_ids ) ) ) );

		update_network_cache( $fresh_networks );
	}
}

/**
 * Handler for updating the blog date when a post is published or an already published post is changed.
 *
 * @since 3.3.0
 *
 * @param string $new_status The new post status
 * @param string $old_status The old post status
 * @param object $post       Post object
 */
function _update_blog_date_on_post_publish( $new_status, $old_status, $post ) {
	$post_type_obj = get_post_type_object( $post->post_type );
	if ( ! $post_type_obj || ! $post_type_obj->public ) {
		return;
	}

	if ( 'publish' != $new_status && 'publish' != $old_status ) {
		return;
	}

	// Post was freshly published, published post was saved, or published post was unpublished.

	wpmu_update_blogs_date();
}

/**
 * Handler for updating the blog date when a published post is deleted.
 *
 * @since 3.4.0
 *
 * @param int $post_id Post ID
 */
function _update_blog_date_on_post_delete( $post_id ) {
	$post = get_post( $post_id );

	$post_type_obj = get_post_type_object( $post->post_type );
	if ( ! $post_type_obj || ! $post_type_obj->public ) {
		return;
	}

	if ( 'publish' != $post->post_status ) {
		return;
	}

	wpmu_update_blogs_date();
}

/**
 * Handler for updating the blog posts count date when a post is deleted.
 *
 * @since 4.0.0
 *
 * @param int $post_id Post ID.
 */
function _update_posts_count_on_delete( $post_id ) {
	$post = get_post( $post_id );

	if ( ! $post || 'publish' !== $post->post_status ) {
		return;
	}

	update_posts_count();
}

/**
 * Handler for updating the blog posts count date when a post status changes.
 *
 * @since 4.0.0
 *
 * @param string $new_status The status the post is changing to.
 * @param string $old_status The status the post is changing from.
 */
function _update_posts_count_on_transition_post_status( $new_status, $old_status ) {
	if ( $new_status === $old_status ) {
		return;
	}

	if ( 'publish' !== $new_status && 'publish' !== $old_status ) {
		return;
	}

	update_posts_count();
}
