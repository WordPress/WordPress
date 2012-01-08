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
 */
function wpmu_update_blogs_date() {
	global $wpdb;

	update_blog_details( $wpdb->blogid, array('last_updated' => current_time('mysql', true)) );

	do_action( 'wpmu_blog_updated', $wpdb->blogid );
}

/**
 * Get a full blog URL, given a blog id.
 *
 * @since MU
 *
 * @param int $blog_id Blog ID
 * @return string
 */
function get_blogaddress_by_id( $blog_id ) {
	$bloginfo = get_blog_details( (int) $blog_id, false ); // only get bare details!
	return esc_url( 'http://' . $bloginfo->domain . $bloginfo->path );
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
	global $current_site;

	if ( is_subdomain_install() ) {
		if ( $blogname == 'main' )
			$blogname = 'www';
		$url = rtrim( network_home_url(), '/' );
		if ( !empty( $blogname ) )
			$url = preg_replace( '|^([^\.]+://)|', '$1' . $blogname . '.', $url );
	} else {
		$url = network_home_url( $blogname );
	}
	return esc_url( $url . '/' );
}

/**
 * Get a full blog URL, given a domain and a path.
 *
 * @since MU
 *
 * @param string $domain
 * @param string $path
 * @return string
 */
function get_blogaddress_by_domain( $domain, $path ) {
	if ( is_subdomain_install() ) {
		$url = "http://" . $domain.$path;
	} else {
		if ( $domain != $_SERVER['HTTP_HOST'] ) {
			$blogname = substr( $domain, 0, strpos( $domain, '.' ) );
			$url = 'http://' . substr( $domain, strpos( $domain, '.' ) + 1 ) . $path;
			// we're not installing the main blog
			if ( $blogname != 'www.' )
				$url .= $blogname . '/';
		} else { // main blog
			$url = 'http://' . $domain . $path;
		}
	}
	return esc_url( $url );
}

/**
 * Given a blog's (subdomain or directory) name, retrieve it's id.
 *
 * @since MU
 *
 * @param string $name
 * @return int A blog id
 */
function get_id_from_blogname( $name ) {
	global $wpdb, $current_site;
	$blog_id = wp_cache_get( 'get_id_from_blogname_' . $name, 'blog-details' );
	if ( $blog_id )
		return $blog_id;

	if ( is_subdomain_install() ) {
		$domain = $name . '.' . $current_site->domain;
		$path = $current_site->path;
	} else {
		$domain = $current_site->domain;
		$path = $current_site->path . $name . '/';
	}
	$blog_id = $wpdb->get_var( $wpdb->prepare("SELECT blog_id FROM {$wpdb->blogs} WHERE domain = %s AND path = %s", $domain, $path) );
	wp_cache_set( 'get_id_from_blogname_' . $name, $blog_id, 'blog-details' );
	return $blog_id;
}

/**
 * Retrieve the details for a blog from the blogs table and blog options.
 *
 * @since MU
 *
 * @param int|string|array $fields A blog ID, a blog name, or an array of fields to query against.
 * @param bool $get_all Whether to retrieve all details or only the details in the blogs table. Default is true.
 * @return object Blog details.
 */
function get_blog_details( $fields, $get_all = true ) {
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
		if ( !is_numeric( $fields ) )
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

	$details->blogname		= get_blog_option( $blog_id, 'blogname' );
	$details->siteurl		= get_blog_option( $blog_id, 'siteurl' );
	$details->post_count	= get_blog_option( $blog_id, 'post_count' );

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
 * @param int $blog_id Blog ID
 */
function refresh_blog_details( $blog_id ) {
	$blog_id = (int) $blog_id;
	$details = get_blog_details( $blog_id, false );

	wp_cache_delete( $blog_id , 'blog-details' );
	wp_cache_delete( $blog_id . 'short' , 'blog-details' );
	wp_cache_delete( md5( $details->domain . $details->path )  , 'blog-lookup' );
	wp_cache_delete( 'current_blog_' . $details->domain, 'site-options' );
	wp_cache_delete( 'current_blog_' . $details->domain . $details->path, 'site-options' );
}

/**
 * Update the details for a blog. Updates the blogs table for a given blog id.
 *
 * @since MU
 *
 * @param int $blog_id Blog ID
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
	foreach ( array_intersect( array_keys( $details ), $fields ) as $field )
		$update_details[$field] = $details[$field];

	$wpdb->update( $wpdb->blogs, $update_details, array('blog_id' => $blog_id) );

	// If spam status changed, issue actions.
	if ( $details[ 'spam' ] != $current_details[ 'spam' ] ) {
		if ( $details[ 'spam' ] == 1 )
			do_action( "make_spam_blog", $blog_id );
		else
			do_action( "make_ham_blog", $blog_id );
	}

	if ( isset($details[ 'public' ]) )
		update_blog_option( $blog_id, 'blog_public', $details[ 'public' ] );

	refresh_blog_details($blog_id);

	return true;
}

/**
 * Retrieve option value based on setting name and blog_id.
 *
 * If the option does not exist or does not have a value, then the return value
 * will be false. This is useful to check whether you need to install an option
 * and is commonly used during installation of plugin options and to test
 * whether upgrading is required.
 *
 * There is a filter called 'blog_option_$option' with the $option being
 * replaced with the option name. The filter takes two parameters. $value and
 * $blog_id. It returns $value.
 * The 'option_$option' filter in get_option() is not called.
 *
 * @since MU
 * @uses apply_filters() Calls 'blog_option_$optionname' with the option name value.
 *
 * @param int $blog_id Optional. Blog ID, can be null to refer to the current blog.
 * @param string $setting Name of option to retrieve. Should already be SQL-escaped.
 * @param string $default (optional) Default value returned if option not found.
 * @return mixed Value set for the option.
 */
function get_blog_option( $blog_id, $setting, $default = false ) {
	global $wpdb;

	if ( null === $blog_id )
		$blog_id = $wpdb->blogid;

	$key = $blog_id . '-' . $setting . '-blog_option';
	$value = wp_cache_get( $key, 'site-options' );
	if ( $value == null ) {
		if ( $blog_id == $wpdb->blogid ) {
			$value = get_option( $setting, $default );
			$notoptions = wp_cache_get( 'notoptions', 'options' );
			if ( isset( $notoptions[$setting] ) ) {
				wp_cache_set( $key, 'noop', 'site-options' );
				$value = $default;
			} elseif ( $value == false ) {
				wp_cache_set( $key, 'falsevalue', 'site-options' );
			} else {
				wp_cache_set( $key, $value, 'site-options' );
			}
			return apply_filters( 'blog_option_' . $setting, $value, $blog_id );
		} else {
			$blog_prefix = $wpdb->get_blog_prefix( $blog_id );
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$blog_prefix}options WHERE option_name = %s", $setting ) );
			if ( is_object( $row ) ) { // Has to be get_row instead of get_var because of funkiness with 0, false, null values
				$value = $row->option_value;
				if ( $value == false )
					wp_cache_set( $key, 'falsevalue', 'site-options' );
				else
					wp_cache_set( $key, $value, 'site-options' );
			} else { // option does not exist, so we must cache its non-existence
				wp_cache_set( $key, 'noop', 'site-options' );
				$value = $default;
			}
		}
	} elseif ( $value == 'noop' ) {
		$value = $default;
	} elseif ( $value == 'falsevalue' ) {
		$value = false;
	}
	// If home is not set use siteurl.
	if ( 'home' == $setting && '' == $value )
		return get_blog_option( $blog_id, 'siteurl' );

	if ( 'siteurl' == $setting || 'home' == $setting || 'category_base' == $setting )
		$value = untrailingslashit( $value );

	return apply_filters( 'blog_option_' . $setting, maybe_unserialize( $value ), $blog_id );
}

/**
 * Add an option for a particular blog.
 *
 * @since MU
 *
 * @param int $id The blog id
 * @param string $key The option key
 * @param mixed $value The option value
 * @return bool True on success, false on failure.
 */
function add_blog_option( $id, $key, $value ) {
	$id = (int) $id;

	switch_to_blog($id);
	$return = add_option( $key, $value );
	restore_current_blog();
	if ( $return )
		wp_cache_set( $id . '-' . $key . '-blog_option', $value, 'site-options' );
	return $return;
}

/**
 * Delete an option for a particular blog.
 *
 * @since MU
 *
 * @param int $id The blog id
 * @param string $key The option key
 * @return bool True on success, false on failure.
 */
function delete_blog_option( $id, $key ) {
	$id = (int) $id;

	switch_to_blog($id);
	$return = delete_option( $key );
	restore_current_blog();
	if ( $return )
		wp_cache_set( $id . '-' . $key . '-blog_option', '', 'site-options' );
	return $return;
}

/**
 * Update an option for a particular blog.
 *
 * @since MU
 *
 * @param int $id The blog id
 * @param string $key The option key
 * @param mixed $value The option value
 * @return bool True on success, false on failrue.
 */
function update_blog_option( $id, $key, $value, $deprecated = null ) {
	$id = (int) $id;

	if ( null !== $deprecated  )
		_deprecated_argument( __FUNCTION__, '3.1' );

	switch_to_blog($id);
	$return = update_option( $key, $value );
	restore_current_blog();

	refresh_blog_details( $id );

	if ( $return )
		wp_cache_set( $id . '-' . $key . '-blog_option', $value, 'site-options');
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
 * @param int $new_blog The id of the blog you want to switch to. Default: current blog
 * @param bool $validate Whether to check if $new_blog exists before proceeding
 * @return bool	True on success, False if the validation failed
 */
function switch_to_blog( $new_blog, $validate = false ) {
	global $wpdb, $table_prefix, $blog_id, $switched, $switched_stack, $wp_roles, $wp_object_cache;

	if ( empty($new_blog) )
		$new_blog = $blog_id;

	if ( $validate && ! get_blog_details( $new_blog ) )
		return false;

	if ( empty($switched_stack) )
		$switched_stack = array();

	$switched_stack[] = $blog_id;

	/* If we're switching to the same blog id that we're on,
	* set the right vars, do the associated actions, but skip
	* the extra unnecessary work */
	if ( $blog_id == $new_blog ) {
		do_action( 'switch_blog', $blog_id, $blog_id );
		$switched = true;
		return true;
	}

	$wpdb->set_blog_id($new_blog);
	$table_prefix = $wpdb->prefix;
	$prev_blog_id = $blog_id;
	$blog_id = $new_blog;

	if ( is_object( $wp_roles ) ) {
		$wpdb->suppress_errors();
		if ( method_exists( $wp_roles ,'_init' ) )
			$wp_roles->_init();
		elseif ( method_exists( $wp_roles, '__construct' ) )
			$wp_roles->__construct();
		$wpdb->suppress_errors( false );
	}

	if ( did_action('init') ) {
		$current_user = wp_get_current_user();
		if ( is_object( $current_user ) )
			$current_user->for_blog( $blog_id );
	}

	if ( is_object( $wp_object_cache ) && isset( $wp_object_cache->global_groups ) )
		$global_groups = $wp_object_cache->global_groups;
	else
		$global_groups = false;

	wp_cache_init();
	if ( function_exists('wp_cache_add_global_groups') ) {
		if ( is_array( $global_groups ) )
			wp_cache_add_global_groups( $global_groups );
		else
			wp_cache_add_global_groups( array( 'users', 'userlogins', 'usermeta', 'user_meta', 'site-transient', 'site-options', 'site-lookup', 'blog-lookup', 'blog-details', 'rss', 'global-posts' ) );
		wp_cache_add_non_persistent_groups(array( 'comment', 'counts', 'plugins' ));
	}

	do_action('switch_blog', $blog_id, $prev_blog_id);
	$switched = true;
	return true;
}

/**
 * Restore the current blog, after calling switch_to_blog()
 *
 * @see switch_to_blog()
 * @since MU
 *
 * @return bool True on success, False if we're already on the current blog
 */
function restore_current_blog() {
	global $table_prefix, $wpdb, $blog_id, $switched, $switched_stack, $wp_roles, $wp_object_cache;

	if ( !$switched )
		return false;

	if ( !is_array( $switched_stack ) )
		return false;

	$blog = array_pop( $switched_stack );
	if ( $blog_id == $blog ) {
		do_action( 'switch_blog', $blog, $blog );
		/* If we still have items in the switched stack, consider ourselves still 'switched' */
		$switched = ( is_array( $switched_stack ) && count( $switched_stack ) > 0 );
		return true;
	}

	$wpdb->set_blog_id($blog);
	$prev_blog_id = $blog_id;
	$blog_id = $blog;
	$table_prefix = $wpdb->prefix;

	if ( is_object( $wp_roles ) ) {
		$wpdb->suppress_errors();
		if ( method_exists( $wp_roles ,'_init' ) )
			$wp_roles->_init();
		elseif ( method_exists( $wp_roles, '__construct' ) )
			$wp_roles->__construct();
		$wpdb->suppress_errors( false );
	}

	if ( did_action('init') ) {
		$current_user = wp_get_current_user();
		if ( is_object( $current_user ) )
			$current_user->for_blog( $blog_id );
	}

	if ( is_object( $wp_object_cache ) && isset( $wp_object_cache->global_groups ) )
		$global_groups = $wp_object_cache->global_groups;
	else
		$global_groups = false;

	wp_cache_init();
	if ( function_exists('wp_cache_add_global_groups') ) {
		if ( is_array( $global_groups ) )
			wp_cache_add_global_groups( $global_groups );
		else
			wp_cache_add_global_groups( array( 'users', 'userlogins', 'usermeta', 'user_meta', 'site-transient', 'site-options', 'site-lookup', 'blog-lookup', 'blog-details', 'rss', 'global-posts' ) );
		wp_cache_add_non_persistent_groups(array( 'comment', 'counts', 'plugins' ));
	}

	do_action('switch_blog', $blog_id, $prev_blog_id);

	/* If we still have items in the switched stack, consider ourselves still 'switched' */
	$switched = ( is_array( $switched_stack ) && count( $switched_stack ) > 0 );
	return true;
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
 * @param int $id The blog id
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
 * @param int $blog_id BLog ID
 * @param string $pref A field name
 * @param string $value Value for $pref
 * @return string $value
 */
function update_blog_status( $blog_id, $pref, $value, $deprecated = null ) {
	global $wpdb;

	if ( null !== $deprecated  )
		_deprecated_argument( __FUNCTION__, '3.1' );

	if ( !in_array( $pref, array( 'site_id', 'domain', 'path', 'registered', 'last_updated', 'public', 'archived', 'mature', 'spam', 'deleted', 'lang_id') ) )
		return $value;

	$wpdb->update( $wpdb->blogs, array($pref => $value, 'last_updated' => current_time('mysql', true)), array('blog_id' => $blog_id) );

	refresh_blog_details($blog_id);

	if ( 'spam' == $pref )
		( $value == 1 ) ? do_action( 'make_spam_blog', $blog_id ) :	do_action( 'make_ham_blog', $blog_id );
	elseif ( 'mature' == $pref )
		( $value == 1 ) ? do_action( 'mature_blog', $blog_id ) : do_action( 'unmature_blog', $blog_id );
	elseif ( 'archived' == $pref )
		( $value == 1 ) ? do_action( 'archive_blog', $blog_id ) : do_action( 'unarchive_blog', $blog_id );
	elseif ( 'archived' == $pref )
		( $value == 1 ) ? do_action( 'archive_blog', $blog_id ) : do_action( 'unarchive_blog', $blog_id );

	return $value;
}

/**
 * Get a blog details field.
 *
 * @since MU
 *
 * @param int $id The blog id
 * @param string $pref A field name
 * @return bool $value
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
 * @param mixed $deprecated Not used
 * @param int $start The offset
 * @param int $quantity The maximum number of blogs to retrieve. Default is 40.
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
 * @param object $post Post object
 */
function _update_blog_date_on_post_publish( $new_status, $old_status, $post ) {
	$post_type_obj = get_post_type_object( $post->post_type );
	if ( ! $post_type_obj->public )
		return;

	if ( 'publish' != $new_status && 'publish' != $old_status )
		return;

	// Post was freshly published, published post was saved, or published post was unpublished.

	wpmu_update_blogs_date();
}
