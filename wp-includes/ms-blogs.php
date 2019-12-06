<?php

/**
 * Site/blog functions that work with the blogs table and related data.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since MU (3.0.0)
 */

require_once( ABSPATH . WPINC . '/ms-site.php' );
require_once( ABSPATH . WPINC . '/ms-network.php' );

/**
 * Update the last_updated field for the current site.
 *
 * @since MU (3.0.0)
 */
function wpmu_update_blogs_date() {
	$site_id = get_current_blog_id();

	update_blog_details( $site_id, array( 'last_updated' => current_time( 'mysql', true ) ) );
	/**
	 * Fires after the blog details are updated.
	 *
	 * @since MU (3.0.0)
	 *
	 * @param int $blog_id Site ID.
	 */
	do_action( 'wpmu_blog_updated', $site_id );
}

/**
 * Get a full blog URL, given a blog id.
 *
 * @since MU (3.0.0)
 *
 * @param int $blog_id Blog ID.
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
 * @since MU (3.0.0)
 *
 * @param string $blogname The (subdomain or directory) name
 * @return string
 */
function get_blogaddress_by_name( $blogname ) {
	if ( is_subdomain_install() ) {
		if ( 'main' === $blogname ) {
			$blogname = 'www';
		}
		$url = rtrim( network_home_url(), '/' );
		if ( ! empty( $blogname ) ) {
			$url = preg_replace( '|^([^\.]+://)|', '${1}' . $blogname . '.', $url );
		}
	} else {
		$url = network_home_url( $blogname );
	}
	return esc_url( $url . '/' );
}

/**
 * Retrieves a sites ID given its (subdomain or directory) slug.
 *
 * @since MU (3.0.0)
 * @since 4.7.0 Converted to use `get_sites()`.
 *
 * @param string $slug A site's slug.
 * @return int|null The site ID, or null if no site is found for the given slug.
 */
function get_id_from_blogname( $slug ) {
	$current_network = get_network();
	$slug            = trim( $slug, '/' );

	if ( is_subdomain_install() ) {
		$domain = $slug . '.' . preg_replace( '|^www\.|', '', $current_network->domain );
		$path   = $current_network->path;
	} else {
		$domain = $current_network->domain;
		$path   = $current_network->path . $slug . '/';
	}

	$site_ids = get_sites(
		array(
			'number'                 => 1,
			'fields'                 => 'ids',
			'domain'                 => $domain,
			'path'                   => $path,
			'update_site_meta_cache' => false,
		)
	);

	if ( empty( $site_ids ) ) {
		return null;
	}

	return array_shift( $site_ids );
}

/**
 * Retrieve the details for a blog from the blogs table and blog options.
 *
 * @since MU (3.0.0)
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

	if ( is_array( $fields ) ) {
		if ( isset( $fields['blog_id'] ) ) {
			$blog_id = $fields['blog_id'];
		} elseif ( isset( $fields['domain'] ) && isset( $fields['path'] ) ) {
			$key  = md5( $fields['domain'] . $fields['path'] );
			$blog = wp_cache_get( $key, 'blog-lookup' );
			if ( false !== $blog ) {
				return $blog;
			}
			if ( substr( $fields['domain'], 0, 4 ) == 'www.' ) {
				$nowww = substr( $fields['domain'], 4 );
				$blog  = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->blogs WHERE domain IN (%s,%s) AND path = %s ORDER BY CHAR_LENGTH(domain) DESC", $nowww, $fields['domain'], $fields['path'] ) );
			} else {
				$blog = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->blogs WHERE domain = %s AND path = %s", $fields['domain'], $fields['path'] ) );
			}
			if ( $blog ) {
				wp_cache_set( $blog->blog_id . 'short', $blog, 'blog-details' );
				$blog_id = $blog->blog_id;
			} else {
				return false;
			}
		} elseif ( isset( $fields['domain'] ) && is_subdomain_install() ) {
			$key  = md5( $fields['domain'] );
			$blog = wp_cache_get( $key, 'blog-lookup' );
			if ( false !== $blog ) {
				return $blog;
			}
			if ( substr( $fields['domain'], 0, 4 ) == 'www.' ) {
				$nowww = substr( $fields['domain'], 4 );
				$blog  = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->blogs WHERE domain IN (%s,%s) ORDER BY CHAR_LENGTH(domain) DESC", $nowww, $fields['domain'] ) );
			} else {
				$blog = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->blogs WHERE domain = %s", $fields['domain'] ) );
			}
			if ( $blog ) {
				wp_cache_set( $blog->blog_id . 'short', $blog, 'blog-details' );
				$blog_id = $blog->blog_id;
			} else {
				return false;
			}
		} else {
			return false;
		}
	} else {
		if ( ! $fields ) {
			$blog_id = get_current_blog_id();
		} elseif ( ! is_numeric( $fields ) ) {
			$blog_id = get_id_from_blogname( $fields );
		} else {
			$blog_id = $fields;
		}
	}

	$blog_id = (int) $blog_id;

	$all     = $get_all ? '' : 'short';
	$details = wp_cache_get( $blog_id . $all, 'blog-details' );

	if ( $details ) {
		if ( ! is_object( $details ) ) {
			if ( -1 == $details ) {
				return false;
			} else {
				// Clear old pre-serialized objects. Cache clients do better with that.
				wp_cache_delete( $blog_id . $all, 'blog-details' );
				unset( $details );
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
				if ( -1 == $details ) {
					return false;
				} else {
					// Clear old pre-serialized objects. Cache clients do better with that.
					wp_cache_delete( $blog_id, 'blog-details' );
					unset( $details );
				}
			} else {
				return $details;
			}
		}
	}

	if ( empty( $details ) ) {
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
	 * @since MU (3.0.0)
	 * @deprecated 4.7.0 Use {@see 'site_details'} instead.
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
 * @since MU (3.0.0)
 *
 * @param int $blog_id Optional. Blog ID. Defaults to current blog.
 */
function refresh_blog_details( $blog_id = 0 ) {
	$blog_id = (int) $blog_id;
	if ( ! $blog_id ) {
		$blog_id = get_current_blog_id();
	}

	clean_blog_cache( $blog_id );
}

/**
 * Update the details for a blog. Updates the blogs table for a given blog id.
 *
 * @since MU (3.0.0)
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int   $blog_id Blog ID.
 * @param array $details Array of details keyed by blogs table field names.
 * @return bool True if update succeeds, false otherwise.
 */
function update_blog_details( $blog_id, $details = array() ) {
	global $wpdb;

	if ( empty( $details ) ) {
		return false;
	}

	if ( is_object( $details ) ) {
		$details = get_object_vars( $details );
	}

	$site = wp_update_site( $blog_id, $details );

	if ( is_wp_error( $site ) ) {
		return false;
	}

	return true;
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
 * Retrieve option value for a given blog id based on name of option.
 *
 * If the option does not exist or does not have a value, then the return value
 * will be false. This is useful to check whether you need to install an option
 * and is commonly used during installation of plugin options and to test
 * whether upgrading is required.
 *
 * If the option was serialized then it will be unserialized when it is returned.
 *
 * @since MU (3.0.0)
 *
 * @param int    $id      A blog ID. Can be null to refer to the current blog.
 * @param string $option  Name of option to retrieve. Expected to not be SQL-escaped.
 * @param mixed  $default Optional. Default value to return if the option does not exist.
 * @return mixed Value set for the option.
 */
function get_blog_option( $id, $option, $default = false ) {
	$id = (int) $id;

	if ( empty( $id ) ) {
		$id = get_current_blog_id();
	}

	if ( get_current_blog_id() == $id ) {
		return get_option( $option, $default );
	}

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
 * @since MU (3.0.0)
 *
 * @param int    $id     A blog ID. Can be null to refer to the current blog.
 * @param string $option Name of option to add. Expected to not be SQL-escaped.
 * @param mixed  $value  Optional. Option value, can be anything. Expected to not be SQL-escaped.
 * @return bool False if option was not added and true if option was added.
 */
function add_blog_option( $id, $option, $value ) {
	$id = (int) $id;

	if ( empty( $id ) ) {
		$id = get_current_blog_id();
	}

	if ( get_current_blog_id() == $id ) {
		return add_option( $option, $value );
	}

	switch_to_blog( $id );
	$return = add_option( $option, $value );
	restore_current_blog();

	return $return;
}

/**
 * Removes option by name for a given blog id. Prevents removal of protected WordPress options.
 *
 * @since MU (3.0.0)
 *
 * @param int    $id     A blog ID. Can be null to refer to the current blog.
 * @param string $option Name of option to remove. Expected to not be SQL-escaped.
 * @return bool True, if option is successfully deleted. False on failure.
 */
function delete_blog_option( $id, $option ) {
	$id = (int) $id;

	if ( empty( $id ) ) {
		$id = get_current_blog_id();
	}

	if ( get_current_blog_id() == $id ) {
		return delete_option( $option );
	}

	switch_to_blog( $id );
	$return = delete_option( $option );
	restore_current_blog();

	return $return;
}

/**
 * Update an option for a particular blog.
 *
 * @since MU (3.0.0)
 *
 * @param int    $id         The blog id.
 * @param string $option     The option key.
 * @param mixed  $value      The option value.
 * @param mixed  $deprecated Not used.
 * @return bool True on success, false on failure.
 */
function update_blog_option( $id, $option, $value, $deprecated = null ) {
	$id = (int) $id;

	if ( null !== $deprecated ) {
		_deprecated_argument( __FUNCTION__, '3.1.0' );
	}

	if ( get_current_blog_id() == $id ) {
		return update_option( $option, $value );
	}

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
 *  - plugins. See #14941
 *
 * @see restore_current_blog()
 * @since MU (3.0.0)
 *
 * @global wpdb            $wpdb               WordPress database abstraction object.
 * @global int             $blog_id
 * @global array           $_wp_switched_stack
 * @global bool            $switched
 * @global string          $table_prefix
 * @global WP_Object_Cache $wp_object_cache
 *
 * @param int  $new_blog_id The ID of the blog to switch to. Default: current blog.
 * @param bool $deprecated  Not used.
 * @return true Always returns true.
 */
function switch_to_blog( $new_blog_id, $deprecated = null ) {
	global $wpdb;

	$prev_blog_id = get_current_blog_id();
	if ( empty( $new_blog_id ) ) {
		$new_blog_id = $prev_blog_id;
	}

	$GLOBALS['_wp_switched_stack'][] = $prev_blog_id;

	/*
	 * If we're switching to the same blog id that we're on,
	 * set the right vars, do the associated actions, but skip
	 * the extra unnecessary work
	 */
	if ( $new_blog_id == $prev_blog_id ) {
		/**
		 * Fires when the blog is switched.
		 *
		 * @since MU (3.0.0)
		 *
		 * @param int $new_blog_id  New blog ID.
		 * @param int $prev_blog_id Previous blog ID.
		 */
		do_action( 'switch_blog', $new_blog_id, $prev_blog_id );
		$GLOBALS['switched'] = true;
		return true;
	}

	$wpdb->set_blog_id( $new_blog_id );
	$GLOBALS['table_prefix'] = $wpdb->get_blog_prefix();
	$GLOBALS['blog_id']      = $new_blog_id;

	if ( function_exists( 'wp_cache_switch_to_blog' ) ) {
		wp_cache_switch_to_blog( $new_blog_id );
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
				wp_cache_add_global_groups( array( 'users', 'userlogins', 'usermeta', 'user_meta', 'useremail', 'userslugs', 'site-transient', 'site-options', 'blog-lookup', 'blog-details', 'rss', 'global-posts', 'blog-id-cache', 'networks', 'sites', 'site-details', 'blog_meta' ) );
			}
			wp_cache_add_non_persistent_groups( array( 'counts', 'plugins' ) );
		}
	}

	/** This filter is documented in wp-includes/ms-blogs.php */
	do_action( 'switch_blog', $new_blog_id, $prev_blog_id );
	$GLOBALS['switched'] = true;

	return true;
}

/**
 * Restore the current blog, after calling switch_to_blog().
 *
 * @see switch_to_blog()
 * @since MU (3.0.0)
 *
 * @global wpdb            $wpdb               WordPress database abstraction object.
 * @global array           $_wp_switched_stack
 * @global int             $blog_id
 * @global bool            $switched
 * @global string          $table_prefix
 * @global WP_Object_Cache $wp_object_cache
 *
 * @return bool True on success, false if we're already on the current blog.
 */
function restore_current_blog() {
	global $wpdb;

	if ( empty( $GLOBALS['_wp_switched_stack'] ) ) {
		return false;
	}

	$new_blog_id  = array_pop( $GLOBALS['_wp_switched_stack'] );
	$prev_blog_id = get_current_blog_id();

	if ( $new_blog_id == $prev_blog_id ) {
		/** This filter is documented in wp-includes/ms-blogs.php */
		do_action( 'switch_blog', $new_blog_id, $prev_blog_id );
		// If we still have items in the switched stack, consider ourselves still 'switched'
		$GLOBALS['switched'] = ! empty( $GLOBALS['_wp_switched_stack'] );
		return true;
	}

	$wpdb->set_blog_id( $new_blog_id );
	$GLOBALS['blog_id']      = $new_blog_id;
	$GLOBALS['table_prefix'] = $wpdb->get_blog_prefix();

	if ( function_exists( 'wp_cache_switch_to_blog' ) ) {
		wp_cache_switch_to_blog( $new_blog_id );
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
				wp_cache_add_global_groups( array( 'users', 'userlogins', 'usermeta', 'user_meta', 'useremail', 'userslugs', 'site-transient', 'site-options', 'blog-lookup', 'blog-details', 'rss', 'global-posts', 'blog-id-cache', 'networks', 'sites', 'site-details', 'blog_meta' ) );
			}
			wp_cache_add_non_persistent_groups( array( 'counts', 'plugins' ) );
		}
	}

	/** This filter is documented in wp-includes/ms-blogs.php */
	do_action( 'switch_blog', $new_blog_id, $prev_blog_id );

	// If we still have items in the switched stack, consider ourselves still 'switched'
	$GLOBALS['switched'] = ! empty( $GLOBALS['_wp_switched_stack'] );

	return true;
}

/**
 * Switches the initialized roles and current user capabilities to another site.
 *
 * @since 4.9.0
 *
 * @param int $new_site_id New site ID.
 * @param int $old_site_id Old site ID.
 */
function wp_switch_roles_and_user( $new_site_id, $old_site_id ) {
	if ( $new_site_id == $old_site_id ) {
		return;
	}

	if ( ! did_action( 'init' ) ) {
		return;
	}

	wp_roles()->for_site( $new_site_id );
	wp_get_current_user()->for_site( $new_site_id );
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
 * @since MU (3.0.0)
 *
 * @param int $id Blog ID.
 * @return string Whether the blog is archived or not.
 */
function is_archived( $id ) {
	return get_blog_status( $id, 'archived' );
}

/**
 * Update the 'archived' status of a particular blog.
 *
 * @since MU (3.0.0)
 *
 * @param int    $id       Blog ID.
 * @param string $archived The new status.
 * @return string $archived
 */
function update_archived( $id, $archived ) {
	update_blog_status( $id, 'archived', $archived );
	return $archived;
}

/**
 * Update a blog details field.
 *
 * @since MU (3.0.0)
 * @since 5.1.0 Use wp_update_site() internally.
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int    $blog_id    Blog ID.
 * @param string $pref       Field name.
 * @param string $value      Field value.
 * @param null   $deprecated Not used.
 * @return string|false $value
 */
function update_blog_status( $blog_id, $pref, $value, $deprecated = null ) {
	global $wpdb;

	if ( null !== $deprecated ) {
		_deprecated_argument( __FUNCTION__, '3.1.0' );
	}

	if ( ! in_array( $pref, array( 'site_id', 'domain', 'path', 'registered', 'last_updated', 'public', 'archived', 'mature', 'spam', 'deleted', 'lang_id' ) ) ) {
		return $value;
	}

	$result = wp_update_site(
		$blog_id,
		array(
			$pref => $value,
		)
	);

	if ( is_wp_error( $result ) ) {
		return false;
	}

	return $value;
}

/**
 * Get a blog details field.
 *
 * @since MU (3.0.0)
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int    $id   Blog ID.
 * @param string $pref Field name.
 * @return bool|string|null $value
 */
function get_blog_status( $id, $pref ) {
	global $wpdb;

	$details = get_site( $id );
	if ( $details ) {
		return $details->$pref;
	}

	return $wpdb->get_var( $wpdb->prepare( "SELECT %s FROM {$wpdb->blogs} WHERE blog_id = %d", $pref, $id ) );
}

/**
 * Get a list of most recently updated blogs.
 *
 * @since MU (3.0.0)
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param mixed $deprecated Not used.
 * @param int   $start      Optional. Number of blogs to offset the query. Used to build LIMIT clause.
 *                          Can be used for pagination. Default 0.
 * @param int   $quantity   Optional. The maximum number of blogs to retrieve. Default 40.
 * @return array The list of blogs.
 */
function get_last_updated( $deprecated = '', $start = 0, $quantity = 40 ) {
	global $wpdb;

	if ( ! empty( $deprecated ) ) {
		_deprecated_argument( __FUNCTION__, 'MU' ); // never used
	}

	return $wpdb->get_results( $wpdb->prepare( "SELECT blog_id, domain, path FROM $wpdb->blogs WHERE site_id = %d AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' AND last_updated != '0000-00-00 00:00:00' ORDER BY last_updated DESC limit %d, %d", get_current_network_id(), $start, $quantity ), ARRAY_A );
}

/**
 * Handler for updating the site's last updated date when a post is published or
 * an already published post is changed.
 *
 * @since 3.3.0
 *
 * @param string  $new_status The new post status.
 * @param string  $old_status The old post status.
 * @param WP_Post $post       Post object.
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
 * Handler for updating the current site's last updated date when a published
 * post is deleted.
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
 * Handler for updating the current site's posts count when a post is deleted.
 *
 * @since 4.0.0
 *
 * @param int $post_id Post ID.
 */
function _update_posts_count_on_delete( $post_id ) {
	$post = get_post( $post_id );

	if ( ! $post || 'publish' !== $post->post_status || 'post' !== $post->post_type ) {
		return;
	}

	update_posts_count();
}

/**
 * Handler for updating the current site's posts count when a post status changes.
 *
 * @since 4.0.0
 * @since 4.9.0 Added the `$post` parameter.
 *
 * @param string  $new_status The status the post is changing to.
 * @param string  $old_status The status the post is changing from.
 * @param WP_Post $post       Post object
 */
function _update_posts_count_on_transition_post_status( $new_status, $old_status, $post = null ) {
	if ( $new_status === $old_status ) {
		return;
	}

	if ( 'post' !== get_post_type( $post ) ) {
		return;
	}

	if ( 'publish' !== $new_status && 'publish' !== $old_status ) {
		return;
	}

	update_posts_count();
}

/**
 * Count number of sites grouped by site status.
 *
 * @since 5.3.0
 *
 * @param int $network_id Optional. The network to get counts for. Default is the current network ID.
 * @return int[] {
 *     Numbers of sites grouped by site status.
 *
 *     @type int $all      The total number of sites.
 *     @type int $public   The number of public sites.
 *     @type int $archived The number of archived sites.
 *     @type int $mature   The number of mature sites.
 *     @type int $spam     The number of spam sites.
 *     @type int $deleted  The number of deleted sites.
 * }
 */
function wp_count_sites( $network_id = null ) {
	if ( empty( $network_id ) ) {
		$network_id = get_current_network_id();
	}

	$counts = array();
	$args   = array(
		'network_id'    => $network_id,
		'number'        => 1,
		'fields'        => 'ids',
		'no_found_rows' => false,
	);

	$q             = new WP_Site_Query( $args );
	$counts['all'] = $q->found_sites;

	$_args    = $args;
	$statuses = array( 'public', 'archived', 'mature', 'spam', 'deleted' );

	foreach ( $statuses as $status ) {
		$_args            = $args;
		$_args[ $status ] = 1;

		$q                 = new WP_Site_Query( $_args );
		$counts[ $status ] = $q->found_sites;
	}

	return $counts;
}
