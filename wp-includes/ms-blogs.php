<?php

/**
 * Site/blog functions that work with the blogs table and related data.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since MU
 */

/**
 * Update the last_updated field for the current blog.
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
	 * @param int $blog_id Blog ID.
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
	$bloginfo = get_blog_details( (int) $blog_id, false ); // only get bare details!
	return ( $bloginfo ) ? esc_url( 'http://' . $bloginfo->domain . $bloginfo->path ) : '';
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
 * Given a blog's (subdomain or directory) slug, retrieve its id.
 *
 * @since MU
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $slug
 * @return int A blog id
 */
function get_id_from_blogname( $slug ) {
	global $wpdb;

	$current_site = get_current_site();
	$slug = trim( $slug, '/' );

	$blog_id = wp_cache_get( 'get_id_from_blogname_' . $slug, 'blog-details' );
	if ( $blog_id )
		return $blog_id;

	if ( is_subdomain_install() ) {
		$domain = $slug . '.' . $current_site->domain;
		$path = $current_site->path;
	} else {
		$domain = $current_site->domain;
		$path = $current_site->path . $slug . '/';
	}

	$blog_id = $wpdb->get_var( $wpdb->prepare("SELECT blog_id FROM {$wpdb->blogs} WHERE domain = %s AND path = %s", $domain, $path) );
	wp_cache_set( 'get_id_from_blogname_' . $slug, $blog_id, 'blog-details' );
	return $blog_id;
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
 * @return object|false Blog details on success. False on failure.
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
		$details = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->blogs WHERE blog_id = %d /* get_blog_details */", $blog_id ) );
		if ( ! $details ) {
			// Set the full cache.
			wp_cache_set( $blog_id, -1, 'blog-details' );
			return false;
		}
	}

	if ( ! $get_all ) {
		wp_cache_set( $blog_id . $all, $details, 'blog-details' );
		return $details;
	}

	switch_to_blog( $blog_id );
	$details->blogname		= get_option( 'blogname' );
	$details->siteurl		= get_option( 'siteurl' );
	$details->post_count	= get_option( 'post_count' );
	restore_current_blog();

	/**
	 * Filter a blog's details.
	 *
	 * @since MU
	 *
	 * @param object $details The blog details.
	 */
	$details = apply_filters( 'blog_details', $details );

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

	$details = get_blog_details( $blog_id, false );
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

	$current_details = get_blog_details($blog_id, false);
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
 * @param stdClass $blog The blog details as returned from get_blog_details()
 */
function clean_blog_cache( $blog ) {
	$blog_id = $blog->blog_id;
	$domain_path_key = md5( $blog->domain . $blog->path );

	wp_cache_delete( $blog_id , 'blog-details' );
	wp_cache_delete( $blog_id . 'short' , 'blog-details' );
	wp_cache_delete(  $domain_path_key, 'blog-lookup' );
	wp_cache_delete( 'current_blog_' . $blog->domain, 'site-options' );
	wp_cache_delete( 'current_blog_' . $blog->domain . $blog->path, 'site-options' );
	wp_cache_delete( 'get_id_from_blogname_' . trim( $blog->path, '/' ), 'blog-details' );
	wp_cache_delete( $domain_path_key, 'blog-id-cache' );
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
	 * Filter a blog option value.
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
 * @param int    $id     The blog id
 * @param string $option The option key
 * @param mixed  $value  The option value
 * @return bool True on success, false on failure.
 */
function update_blog_option( $id, $option, $value, $deprecated = null ) {
	$id = (int) $id;

	if ( null !== $deprecated  )
		_deprecated_argument( __FUNCTION__, '3.1' );

	if ( get_current_blog_id() == $id )
		return update_option( $option, $value );

	switch_to_blog( $id );
	$return = update_option( $option, $value );
	restore_current_blog();

	refresh_blog_details( $id );

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
	global $wpdb;

	if ( empty( $new_blog ) )
		$new_blog = $GLOBALS['blog_id'];

	$GLOBALS['_wp_switched_stack'][] = $GLOBALS['blog_id'];

	/*
	 * If we're switching to the same blog id that we're on,
	 * set the right vars, do the associated actions, but skip
	 * the extra unnecessary work
	 */
	if ( $new_blog == $GLOBALS['blog_id'] ) {
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
	$prev_blog_id = $GLOBALS['blog_id'];
	$GLOBALS['blog_id'] = $new_blog;

	if ( function_exists( 'wp_cache_switch_to_blog' ) ) {
		wp_cache_switch_to_blog( $new_blog );
	} else {
		global $wp_object_cache;

		if ( is_object( $wp_object_cache ) && isset( $wp_object_cache->global_groups ) )
			$global_groups = $wp_object_cache->global_groups;
		else
			$global_groups = false;

		wp_cache_init();

		if ( function_exists( 'wp_cache_add_global_groups' ) ) {
			if ( is_array( $global_groups ) ) {
				wp_cache_add_global_groups( $global_groups );
			} else {
				wp_cache_add_global_groups( array( 'users', 'userlogins', 'usermeta', 'user_meta', 'useremail', 'userslugs', 'site-transient', 'site-options', 'site-lookup', 'blog-lookup', 'blog-details', 'rss', 'global-posts', 'blog-id-cache' ) );
			}
			wp_cache_add_non_persistent_groups( array( 'comment', 'counts', 'plugins' ) );
		}
	}

	if ( did_action( 'init' ) ) {
		wp_roles()->reinit();
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
	global $wpdb;

	if ( empty( $GLOBALS['_wp_switched_stack'] ) )
		return false;

	$blog = array_pop( $GLOBALS['_wp_switched_stack'] );

	if ( $GLOBALS['blog_id'] == $blog ) {
		/** This filter is documented in wp-includes/ms-blogs.php */
		do_action( 'switch_blog', $blog, $blog );
		// If we still have items in the switched stack, consider ourselves still 'switched'
		$GLOBALS['switched'] = ! empty( $GLOBALS['_wp_switched_stack'] );
		return true;
	}

	$wpdb->set_blog_id( $blog );
	$prev_blog_id = $GLOBALS['blog_id'];
	$GLOBALS['blog_id'] = $blog;
	$GLOBALS['table_prefix'] = $wpdb->get_blog_prefix();

	if ( function_exists( 'wp_cache_switch_to_blog' ) ) {
		wp_cache_switch_to_blog( $blog );
	} else {
		global $wp_object_cache;

		if ( is_object( $wp_object_cache ) && isset( $wp_object_cache->global_groups ) )
			$global_groups = $wp_object_cache->global_groups;
		else
			$global_groups = false;

		wp_cache_init();

		if ( function_exists( 'wp_cache_add_global_groups' ) ) {
			if ( is_array( $global_groups ) ) {
				wp_cache_add_global_groups( $global_groups );
			} else {
				wp_cache_add_global_groups( array( 'users', 'userlogins', 'usermeta', 'user_meta', 'useremail', 'userslugs', 'site-transient', 'site-options', 'site-lookup', 'blog-lookup', 'blog-details', 'rss', 'global-posts', 'blog-id-cache' ) );
			}
			wp_cache_add_non_persistent_groups( array( 'comment', 'counts', 'plugins' ) );
		}
	}

	if ( did_action( 'init' ) ) {
		wp_roles()->reinit();
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
		_deprecated_argument( __FUNCTION__, '3.1' );

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

	$details = get_blog_details( $id, false );
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

