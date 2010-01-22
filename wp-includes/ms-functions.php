<?php
/*
	Helper functions for WPMU
*/
function load_muplugin_textdomain($domain, $path = false) {
	$locale = get_locale();
	if ( empty($locale) )
		$locale = 'en_US';

	if ( false === $path )
		$path = WPMU_PLUGIN_DIR;

	$mofile = WPMU_PLUGIN_DIR . "/$domain-$locale.mo";
	load_textdomain($domain, $mofile);
}

function wpmu_update_blogs_date() {
	global $wpdb;

	$wpdb->update( $wpdb->blogs, array('last_updated' => current_time('mysql', true)), array('blog_id' => $wpdb->blogid) );
	refresh_blog_details( $wpdb->blogid );

	do_action( 'wpmu_blog_updated', $wpdb->blogid );
}

function get_blogaddress_by_id( $blog_id ) {
	$bloginfo = get_blog_details( (int) $blog_id, false ); // only get bare details!
	return clean_url("http://" . $bloginfo->domain . $bloginfo->path);
}

function get_blogaddress_by_name( $blogname ) {
	global $current_site;

	if ( is_subdomain_install() ) {
		if ( $blogname == 'main' )
			$blogname = 'www';
		return clean_url( "http://" . $blogname . "." . $current_site->domain . $current_site->path );
	} else {
		return clean_url( "http://" . $current_site->domain . $current_site->path . $blogname . '/' );
	}
}

function get_blogaddress_by_domain( $domain, $path ){
	if ( is_subdomain_install() ) {
		$url = "http://".$domain.$path;
	} else {
		if ( $domain != $_SERVER['HTTP_HOST'] ) {
			$blogname = substr( $domain, 0, strpos( $domain, '.' ) );
			if ( $blogname != 'www.' ) {
				$url = 'http://' . substr( $domain, strpos( $domain, '.' ) + 1 ) . $path . $blogname . '/';
			} else { // we're installing the main blog
				$url = 'http://' . substr( $domain, strpos( $domain, '.' ) + 1 ) . $path;
			}
		} else { // main blog
			$url = 'http://' . $domain . $path;
		}
	}
	return clean_url($url);
}

function get_sitestats() {
	global $wpdb;

	$stats['blogs'] = get_blog_count();

	$count_ts = get_site_option( "get_user_count_ts" );
	if ( time() - $count_ts > 3600 ) {
		$count = $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->users}" );
		update_site_option( "user_count", $count );
		update_site_option( "user_count_ts", time() );
	} else {
		$count = get_site_option( "user_count" );
	}
	$stats['users'] = $count;
	return $stats;
}

function get_admin_users_for_domain( $sitedomain = '', $path = '' ) {
	global $wpdb;

	if ( $sitedomain == '' )
		$site_id = $wpdb->siteid;
	else
		$site_id = $wpdb->get_var( $wpdb->prepare("SELECT id FROM $wpdb->site WHERE domain = %s AND path = %s", $sitedomain, $path) );

	if ( $site_id != false )
		return $wpdb->get_results( $wpdb->prepare("SELECT u.ID, u.user_login, u.user_pass FROM $wpdb->users AS u, $wpdb->sitemeta AS sm WHERE sm.meta_key = 'admin_user_id' AND u.ID = sm.meta_value AND sm.site_id = %d", $site_id), ARRAY_A );

	return false;
}

function get_user_details( $username ) {
	global $wpdb;
	return $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->users WHERE user_login = %s", $username) );
}

function is_main_blog() {
	global $current_blog, $current_site;
	if ( $current_blog->domain == $current_site->domain && $current_blog->path == $current_site->path )
		return true;
	return false;
}

function get_id_from_blogname( $name ) {
	global $wpdb, $current_site;
	$blog_id = wp_cache_get( "get_id_from_blogname_" . $name, 'blog-details' );
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

function get_blog_details( $id, $getall = true ) {
	global $wpdb;

	if ( !is_numeric( $id ) )
		$id = get_id_from_blogname( $id );

	$all = $getall == true ? '' : 'short';
	$details = wp_cache_get( $id . $all, 'blog-details' );

	if ( $details ) {
		if ( $details == -1 )
			return false;
		elseif ( !is_object($details) ) // Clear old pre-serialized objects. Cache clients do better with that.
			wp_cache_delete( $id . $all, 'blog-details' );
		else
			return $details;
	}

	$details = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->blogs WHERE blog_id = %d /* get_blog_details */", $id) );
	if ( !$details ) {
		wp_cache_set( $id . $all, -1, 'blog-details' );
		return false;
	}

	if ( !$getall ) {
		wp_cache_set( $id . $all, $details, 'blog-details' );
		return $details;
	}

	$wpdb->suppress_errors();
	switch_to_blog( $id );
	$details->blogname		= get_option( 'blogname' );
	$details->siteurl		= get_option( 'siteurl' );
	$details->post_count	= get_option( 'post_count' );
	restore_current_blog();
	$wpdb->suppress_errors( false );

	$details = apply_filters('blog_details', $details);

	wp_cache_set( $id . $all, $details, 'blog-details' );

	$key = md5( $details->domain . $details->path );
	wp_cache_set( $key, $details, 'blog-lookup' );

	return $details;
}

function refresh_blog_details( $id ) {
	$id = (int) $id;
	$details = get_blog_details( $id, false );

	wp_cache_delete( $id , 'blog-details' );
	wp_cache_delete( $id . 'short' , 'blog-details' );
	wp_cache_delete( md5( $details->domain . $details->path )  , 'blog-lookup' );
	wp_cache_delete( 'current_blog_' . $details->domain, 'site-options' );
	wp_cache_delete( 'current_blog_' . $details->domain . $details->path, 'site-options' );
}

function get_current_user_id() {
	global $current_user;
	return $current_user->ID;
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
 * @since NA
 * @package WordPress MU
 * @subpackage Option
 * @uses apply_filters() Calls 'blog_option_$optionname' with the option name value.
 *
 * @param int $blog_id is the id of the blog.
 * @param string $setting Name of option to retrieve. Should already be SQL-escaped
 * @param string $default (optional) Default value returned if option not found.
 * @return mixed Value set for the option.
 */
function get_blog_option( $blog_id, $setting, $default = false ) {
	global $wpdb;

	$key = $blog_id."-".$setting."-blog_option";
	$value = wp_cache_get( $key, "site-options" );
	if ( $value == null ) {
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
	} elseif ( $value == 'noop' ) {
		$value = $default;
	} elseif ( $value == 'falsevalue' ) {
		$value = false;
	}
	// If home is not set use siteurl.
	if ( 'home' == $setting && '' == $value )
		return get_blog_option( $blog_id, 'siteurl' );

	if ( 'siteurl' == $setting || 'home' == $setting || 'category_base' == $setting )
		$value = preg_replace( '|/+$|', '', $value );

	if (! @unserialize( $value ) )
		$value = stripslashes( $value );

	return apply_filters( 'blog_option_' . $setting, maybe_unserialize( $value ), $blog_id );
}

function add_blog_option( $id, $key, $value ) {
	$id = (int) $id;

	switch_to_blog($id);
	add_option( $key, $value );
	restore_current_blog();
	wp_cache_set( $id."-".$key."-blog_option", $value, 'site-options' );
}

function delete_blog_option( $id, $key ) {
	$id = (int) $id;

	switch_to_blog($id);
	delete_option( $key );
	restore_current_blog();
	wp_cache_set( $id."-".$key."-blog_option", '', 'site-options' );
}

function update_blog_option( $id, $key, $value, $refresh = true ) {
	$id = (int) $id;

	switch_to_blog($id);
	update_option( $key, $value );
	restore_current_blog();

	if ( $refresh == true )
		refresh_blog_details( $id );
	wp_cache_set( $id."-".$key."-blog_option", $value, 'site-options');
}

function switch_to_blog( $new_blog ) {
	global $wpdb, $table_prefix, $blog_id, $switched, $switched_stack, $wp_roles, $current_user, $wp_object_cache;

	if ( empty($new_blog) )
		$new_blog = $blog_id;

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

	if ( is_object( $current_user ) )
		$current_user->for_blog( $blog_id );

	if ( is_object( $wp_object_cache ) && isset( $wp_object_cache->global_groups ) )
		$global_groups = $wp_object_cache->global_groups;
	else
		$global_groups = false;

	wp_cache_init();
	if ( function_exists('wp_cache_add_global_groups') ) {
		if ( is_array( $global_groups ) )
			wp_cache_add_global_groups( $global_groups );
		else
			wp_cache_add_global_groups( array( 'users', 'userlogins', 'usermeta', 'site-options', 'site-lookup', 'blog-lookup', 'blog-details', 'rss', 'site-transient', 'global-posts' ) );
		wp_cache_add_non_persistent_groups(array( 'comment', 'counts', 'plugins' ));
	}

	do_action('switch_blog', $blog_id, $prev_blog_id);
	$switched = true;
	return true;
}

function restore_current_blog() {
	global $table_prefix, $wpdb, $blog_id, $switched, $switched_stack, $wp_roles, $current_user, $wp_object_cache;

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

	if ( is_object( $current_user ) )
		$current_user->for_blog( $blog_id );

	if ( is_object( $wp_object_cache ) && isset( $wp_object_cache->global_groups ) )
		$global_groups = $wp_object_cache->global_groups;
	else
		$global_groups = false;

	wp_cache_init();
	if ( function_exists('wp_cache_add_global_groups') ) {
		if ( is_array( $global_groups ) )
			wp_cache_add_global_groups( $global_groups );
		else
			wp_cache_add_global_groups( array( 'users', 'userlogins', 'usermeta', 'site-options', 'site-lookup', 'blog-lookup', 'blog-details', 'rss', 'site-transient' ) );
		wp_cache_add_non_persistent_groups(array( 'comment', 'counts', 'plugins' ));
	}

	do_action('switch_blog', $blog_id, $prev_blog_id);

	/* If we still have items in the switched stack, consider ourselves still 'switched' */
	$switched = ( is_array( $switched_stack ) && count( $switched_stack ) > 0 );
	return true;
}

function get_blogs_of_user( $id, $all = false ) {
	global $wpdb;

	$cache_suffix = $all ? '_all' : '_short';
	$return = wp_cache_get( 'blogs_of_user_' . $id . $cache_suffix, 'users' );
	if ( $return )
		return apply_filters( 'get_blogs_of_user', $return, $id, $all );

	$user = get_userdata( (int) $id );
	if ( !$user )
		return false;

	$blogs = $match = array();
	foreach ( (array) $user as $key => $value ) {
		if ( false !== strpos( $key, '_capabilities') && 0 === strpos( $key, $wpdb->base_prefix ) && preg_match( '/' . $wpdb->base_prefix . '(\d+)_capabilities/', $key, $match ) ) {
			$blog = get_blog_details( $match[1] );
			if ( $blog && isset( $blog->domain ) && ( $all == true || $all == false && ( $blog->archived == 0 && $blog->spam == 0 && $blog->deleted == 0 ) ) ) {
				$blogs[$match[1]]->userblog_id	= $match[1];
				$blogs[$match[1]]->blogname		= $blog->blogname;
				$blogs[$match[1]]->domain		= $blog->domain;
				$blogs[$match[1]]->path			= $blog->path;
				$blogs[$match[1]]->site_id		= $blog->site_id;
				$blogs[$match[1]]->siteurl		= $blog->siteurl;
			}
		}
	}

	wp_cache_add( 'blogs_of_user_' . $id . $cache_suffix, $blogs, 'users', 5 );
	return apply_filters( 'get_blogs_of_user', $blogs, $id, $all );
}

function get_active_blog_for_user( $user_id ) { // get an active blog for user - either primary blog or from blogs list
	global $wpdb;
	$blogs = get_blogs_of_user( $user_id );
	if ( empty( $blogs ) ) {
		$details = get_dashboard_blog();
		add_user_to_blog( $details->blog_id, $user_id, 'subscriber' );
		update_usermeta( $user_id, 'primary_blog', $details->blog_id );
		wp_cache_delete( $user_id, 'users' );
		return $details;
	}

	$primary_blog = get_usermeta( $user_id, "primary_blog" );
	$details = get_dashboard_blog();
	if ( $primary_blog ) {
		$blogs = get_blogs_of_user( $user_id );
		if ( isset( $blogs[ $primary_blog ] ) == false ) {
			add_user_to_blog( $details->blog_id, $user_id, 'subscriber' );
			update_usermeta( $user_id, 'primary_blog', $details->blog_id );
			wp_cache_delete( $user_id, 'users' );
		} else {
			$details = get_blog_details( $primary_blog );
		}
	} else {
		add_user_to_blog( $details->blog_id, $user_id, 'subscriber' ); // Add subscriber permission for dashboard blog
		update_usermeta( $user_id, 'primary_blog', $details->blog_id );
	}

	if ( ( is_object( $details ) == false ) || ( is_object( $details ) && $details->archived == 1 || $details->spam == 1 || $details->deleted == 1 ) ) {
		$blogs = get_blogs_of_user( $user_id, true ); // if a user's primary blog is shut down, check their other blogs.
		$ret = false;
		if ( is_array( $blogs ) && count( $blogs ) > 0 ) {
			foreach ( (array) $blogs as $blog_id => $blog ) {
				if ( $blog->site_id != $wpdb->siteid )
					continue;
				$details = get_blog_details( $blog_id );
				if ( is_object( $details ) && $details->archived == 0 && $details->spam == 0 && $details->deleted == 0 ) {
					$ret = $blog;
					$changed = false;
					if ( get_usermeta( $user_id , 'primary_blog' ) != $blog_id ) {
						update_usermeta( $user_id, 'primary_blog', $blog_id );
						$changed = true;
					}
					if ( !get_usermeta($user_id , 'source_domain') ) {
						update_usermeta( $user_id, 'source_domain', $blog->domain );
						$changed = true;
					}
					if ( $changed )
						wp_cache_delete( $user_id, 'users' );
					break;
				}
			}
		} else {
			// Should never get here
			$dashboard_blog = get_dashboard_blog();
			add_user_to_blog( $dashboard_blog->blog_id, $user_id, 'subscriber' ); // Add subscriber permission for dashboard blog
			update_usermeta( $user_id, 'primary_blog', $dashboard_blog->blog_id );
			return $dashboard_blog;
		}
		return $ret;
	} else {
		return $details;
	}
}

function is_user_member_of_blog( $user_id, $blog_id = 0 ) {
	$user_id = (int) $user_id;
	$blog_id = (int) $blog_id;

	if ( $blog_id == 0 ) {
		global $wpdb;
		$blog_id = $wpdb->blogid;
	}

	$blogs = get_blogs_of_user( $user_id );
	if ( is_array( $blogs ) )
		return array_key_exists( $blog_id, $blogs );
	else
		return false;
}

function is_archived( $id ) {
	return get_blog_status($id, 'archived');
}

function update_archived( $id, $archived ) {
	update_blog_status($id, 'archived', $archived);
	return $archived;
}

function update_blog_status( $id, $pref, $value, $refresh = 1 ) {
	global $wpdb;

	if ( !in_array( $pref, array( 'site_id', 'domain', 'path', 'registered', 'last_updated', 'public', 'archived', 'mature', 'spam', 'deleted', 'lang_id') ) )
		return $value;

	$wpdb->update( $wpdb->blogs, array($pref => $value, 'last_updated' => current_time('mysql', true)), array('blog_id' => $id) );
	if ( $refresh == 1 )
		refresh_blog_details($id);

	if ( $pref == 'spam' ) {
		if ( $value == 1 )
			do_action( "make_spam_blog", $id );
		else
			do_action( "make_ham_blog", $id );
	}

	return $value;
}

function get_blog_status( $id, $pref ) {
	global $wpdb;

	$details = get_blog_details( $id, false );
	if ( $details )
		return $details->$pref;

	return $wpdb->get_var( $wpdb->prepare("SELECT $pref FROM {$wpdb->blogs} WHERE blog_id = %d", $id) );
}

function get_last_updated( $deprecated = '', $start = 0, $quantity = 40 ) {
	global $wpdb;
	return $wpdb->get_results( $wpdb->prepare("SELECT blog_id, domain, path FROM $wpdb->blogs WHERE site_id = %d AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' AND last_updated != '0000-00-00 00:00:00' ORDER BY last_updated DESC limit %d, %d", $wpdb->siteid, $start, $quantity ) , ARRAY_A );
}

function get_most_active_blogs( $num = 10, $display = true ) {
	$most_active = get_site_option( "most_active" );
	$update = false;
	if ( is_array( $most_active ) ) {
		if ( ( $most_active['time'] + 60 ) < time() ) { // cache for 60 seconds.
			$update = true;
		}
	} else {
		$update = true;
	}

	if ( $update == true ) {
		unset( $most_active );
		$blogs = get_blog_list( 0, 'all', false ); // $blog_id -> $details
		if ( is_array( $blogs ) ) {
			reset( $blogs );
			foreach ( (array) $blogs as $key => $details ) {
				$most_active[ $details['blog_id'] ] = $details['postcount'];
				$blog_list[ $details['blog_id'] ] = $details; // array_slice() removes keys!!
			}
			arsort( $most_active );
			reset( $most_active );
			foreach ( (array) $most_active as $key => $details )
				$t[ $key ] = $blog_list[ $key ];

			unset( $most_active );
			$most_active = $t;
		}
		update_site_option( "most_active", $most_active );
	}

	if ( $display == true ) {
		if ( is_array( $most_active ) ) {
			reset( $most_active );
			foreach ( (array) $most_active as $key => $details ) {
				$url = clean_url("http://" . $details['domain'] . $details['path']);
				echo "<li>" . $details['postcount'] . " <a href='$url'>$url</a></li>";
			}
		}
	}
	return array_slice( $most_active, 0, $num );
}

function get_blog_list( $start = 0, $num = 10, $deprecated = '' ) {
	global $wpdb;

	$blogs = get_site_option( "blog_list" );
	$update = false;
	if ( is_array( $blogs ) ) {
		if ( ( $blogs['time'] + 60 ) < time() ) { // cache for 60 seconds.
			$update = true;
		}
	} else {
		$update = true;
	}

	if ( $update == true ) {
		unset( $blogs );
		$blogs = $wpdb->get_results( $wpdb->prepare("SELECT blog_id, domain, path FROM $wpdb->blogs WHERE site_id = %d AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' ORDER BY registered DESC", $wpdb->siteid), ARRAY_A );

		foreach ( (array) $blogs as $details ) {
			$blog_list[ $details['blog_id'] ] = $details;
			$blog_list[ $details['blog_id'] ]['postcount'] = $wpdb->get_var( "SELECT COUNT(ID) FROM " . $wpdb->base_prefix . $details['blog_id'] . "_posts WHERE post_status='publish' AND post_type='post'" );
		}
		unset( $blogs );
		$blogs = $blog_list;
		update_site_option( "blog_list", $blogs );
	}

	if ( false == is_array( $blogs ) )
		return array();

	if ( $num == 'all' )
		return array_slice( $blogs, $start, count( $blogs ) );
	else
		return array_slice( $blogs, $start, $num );
}

function get_user_count() {
	global $wpdb;

	$count_ts = get_site_option( "user_count_ts" );
	if ( time() - $count_ts > 3600 ) {
		$count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(ID) as c FROM $wpdb->users WHERE spam = '0' AND deleted = '0'") );
		update_site_option( "user_count", $count );
		update_site_option( "user_count_ts", time() );
	}

	$count = get_site_option( "user_count" );

	return $count;
}

function get_blog_count( $id = 0 ) {
	global $wpdb;

	if ( $id == 0 )
		$id = $wpdb->siteid;

	$count_ts = get_site_option( "blog_count_ts" );
	if ( time() - $count_ts > 3600 ) {
		$count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(blog_id) as c FROM $wpdb->blogs WHERE site_id = %d AND spam = '0' AND deleted = '0' and archived = '0'", $id) );
		update_site_option( "blog_count", $count );
		update_site_option( "blog_count_ts", time() );
	}

	$count = get_site_option( "blog_count" );

	return $count;
}

function get_blog_post( $blog_id, $post_id ) {
	global $wpdb;

	$key = $blog_id . "-" . $post_id;
	$post = wp_cache_get( $key, "global-posts" );
	if ( $post == false ) {
		$post = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->get_blog_prefix( $blog_id ) . "posts WHERE ID = %d", $post_id ) );
		wp_cache_add( $key, $post, "global-posts" );
	}

	return $post;
}

// deprecated, see clean_post_cache()
function clear_global_post_cache( $post_id ) {
	return;
}

function add_user_to_blog( $blog_id, $user_id, $role ) {
	switch_to_blog($blog_id);

	$user = new WP_User($user_id);

	if ( empty($user) )
		return new WP_Error('user_does_not_exist', __('That user does not exist.'));

	if ( !get_usermeta($user_id, 'primary_blog') ) {
		update_usermeta($user_id, 'primary_blog', $blog_id);
		$details = get_blog_details($blog_id);
		update_usermeta($user_id, 'source_domain', $details->domain);
	}

	$user->set_role($role);

	do_action('add_user_to_blog', $user_id, $role, $blog_id);
	wp_cache_delete( $user_id, 'users' );
	restore_current_blog();
	return true;
}

function remove_user_from_blog($user_id, $blog_id = '', $reassign = '') {
	global $wpdb;
	switch_to_blog($blog_id);
	$user_id = (int) $user_id;
	do_action('remove_user_from_blog', $user_id, $blog_id);

	// If being removed from the primary blog, set a new primary if the user is assigned
	// to multiple blogs.
	$primary_blog = get_usermeta($user_id, 'primary_blog');
	if ( $primary_blog == $blog_id ) {
		$new_id = '';
		$new_domain = '';
		$blogs = get_blogs_of_user($user_id);
		foreach ( (array) $blogs as $blog ) {
			if ( $blog->userblog_id == $blog_id )
				continue;
			$new_id = $blog->userblog_id;
			$new_domain = $blog->domain;
			break;
		}

		update_usermeta($user_id, 'primary_blog', $new_id);
		update_usermeta($user_id, 'source_domain', $new_domain);
	}

	// wp_revoke_user($user_id);
	$user = new WP_User($user_id);
	$user->remove_all_caps();

	$blogs = get_blogs_of_user($user_id);
	if ( count($blogs) == 0 ) {
		update_usermeta($user_id, 'primary_blog', '');
		update_usermeta($user_id, 'source_domain', '');
	}

	if ( $reassign != '' ) {
		$reassign = (int) $reassign;
		$wpdb->query( $wpdb->prepare("UPDATE $wpdb->posts SET post_author = %d WHERE post_author = %d", $reassign, $user_id) );
		$wpdb->query( $wpdb->prepare("UPDATE $wpdb->links SET link_owner = %d WHERE link_owner = %d", $reassign, $user_id) );
	}

	restore_current_blog();
}

function create_empty_blog( $domain, $path, $weblog_title, $site_id = 1 ) {
	$domain			= addslashes( $domain );
	$weblog_title	= addslashes( $weblog_title );

	if ( empty($path) )
		$path = '/';

	// Check if the domain has been used already. We should return an error message.
	if ( domain_exists($domain, $path, $site_id) )
		return __('error: Blog URL already taken.');

	// Need to backup wpdb table names, and create a new wp_blogs entry for new blog.
	// Need to get blog_id from wp_blogs, and create new table names.
	// Must restore table names at the end of function.

	if ( ! $blog_id = insert_blog($domain, $path, $site_id) )
		return __('error: problem creating blog entry');

	switch_to_blog($blog_id);
	install_blog($blog_id);
	restore_current_blog();

	return $blog_id;
}

function get_blog_permalink( $_blog_id, $post_id ) {
	$key = "{$_blog_id}-{$post_id}-blog_permalink";
	$link = wp_cache_get( $key, 'site-options' );
	if ( $link == false ) {
		switch_to_blog( $_blog_id );
		$link = get_permalink( $post_id );
		restore_current_blog();
		wp_cache_add( $key, $link, 'site-options', 360 );
	}
	return $link;
}

function get_blog_id_from_url( $domain, $path = '/' ) {
	global $wpdb;

	$domain = strtolower( $wpdb->escape( $domain ) );
	$path = strtolower( $wpdb->escape( $path ) );
	$id = wp_cache_get( md5( $domain . $path ), 'blog-id-cache' );

	if ( $id == -1 ) { // blog does not exist
		return 0;
	} elseif ( $id ) {
		return (int)$id;
	}

	$id = $wpdb->get_var( "SELECT blog_id FROM $wpdb->blogs WHERE domain = '$domain' and path = '$path' /* get_blog_id_from_url */" );

	if ( !$id ) {
		wp_cache_set( md5( $domain . $path ), -1, 'blog-id-cache' );
		return false;
	}
	wp_cache_set( md5( $domain . $path ), $id, 'blog-id-cache' );

	return $id;
}

// wpmu admin functions

function wpmu_admin_do_redirect( $url = '' ) {
	$ref = '';
	if ( isset( $_GET['ref'] ) )
		$ref = $_GET['ref'];
	if ( isset( $_POST['ref'] ) )
		$ref = $_POST['ref'];

	if ( $ref ) {
		$ref = wpmu_admin_redirect_add_updated_param( $ref );
		wp_redirect( $ref );
		exit();
	}
	if ( empty( $_SERVER['HTTP_REFERER'] ) == false ) {
		wp_redirect( $_SERVER['HTTP_REFERER'] );
		exit();
	}

	$url = wpmu_admin_redirect_add_updated_param( $url );
	if ( isset( $_GET['redirect'] ) ) {
		if ( substr( $_GET['redirect'], 0, 2 ) == 's_' )
			$url .= "&action=blogs&s=". wp_specialchars( substr( $_GET['redirect'], 2 ) );
	} elseif ( isset( $_POST['redirect'] ) ) {
		$url = wpmu_admin_redirect_add_updated_param( $_POST['redirect'] );
	}
	wp_redirect( $url );
	exit();
}

function wpmu_admin_redirect_add_updated_param( $url = '' ) {
	if ( strpos( $url, 'updated=true' ) === false ) {
		if ( strpos( $url, '?' ) === false )
			return $url . '?updated=true';
		else
			return $url . '&updated=true';
	}
	return $url;
}

function is_blog_user( $blog_id = 0 ) {
	global $current_user, $wpdb;

	if ( !$blog_id )
		$blog_id = $wpdb->blogid;

	$cap_key = $wpdb->base_prefix . $blog_id . '_capabilities';

	if ( is_array($current_user->$cap_key) && in_array(1, $current_user->$cap_key) )
		return true;

	return false;
}

function validate_email( $email, $check_domain = true) {
	if (ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+'.'@'.
		'[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.'.
		'[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$', $email))
	{
		if ($check_domain && function_exists('checkdnsrr')) {
			list (, $domain)  = explode('@', $email);

			if (checkdnsrr($domain.'.', 'MX') || checkdnsrr($domain.'.', 'A'))
				return true;
			return false;
		}
		return true;
	}
	return false;
}

function is_email_address_unsafe( $user_email ) {
	$banned_names = get_site_option( "banned_email_domains" );
	if ($banned_names && !is_array( $banned_names ))
		$banned_names = explode( "\n", $banned_names);

	if ( is_array( $banned_names ) && empty( $banned_names ) == false ) {
		$email_domain = strtolower( substr( $user_email, 1 + strpos( $user_email, '@' ) ) );
		foreach ( (array) $banned_names as $banned_domain ) {
			if ( $banned_domain == '' )
				continue;
			if (
				strstr( $email_domain, $banned_domain ) ||
				(
					strstr( $banned_domain, '/' ) &&
					preg_match( $banned_domain, $email_domain )
				)
			)
			return true;
		}
	}
	return false;
}

function wpmu_validate_user_signup($user_name, $user_email) {
	global $wpdb;

	$errors = new WP_Error();

	$user_name = preg_replace( "/\s+/", '', sanitize_user( $user_name, true ) );
	$user_email = sanitize_email( $user_email );

	if ( empty( $user_name ) )
	   	$errors->add('user_name', __("Please enter a username"));

	$maybe = array();
	preg_match( "/[a-z0-9]+/", $user_name, $maybe );

	if ( $user_name != $maybe[0] )
		$errors->add('user_name', __("Only lowercase letters and numbers allowed"));

	$illegal_names = get_site_option( "illegal_names" );
	if ( is_array( $illegal_names ) == false ) {
		$illegal_names = array(  "www", "web", "root", "admin", "main", "invite", "administrator" );
		add_site_option( "illegal_names", $illegal_names );
	}
	if ( in_array( $user_name, $illegal_names ) == true )
		$errors->add('user_name',  __("That username is not allowed"));

	if ( is_email_address_unsafe( $user_email ) )
		$errors->add('user_email',  __("You cannot use that email address to signup. We are having problems with them blocking some of our email. Please use another email provider."));

	if ( strlen( $user_name ) < 4 )
		$errors->add('user_name',  __("Username must be at least 4 characters"));

	if ( strpos( " " . $user_name, "_" ) != false )
		$errors->add('user_name', __("Sorry, usernames may not contain the character '_'!"));

	// all numeric?
	$match = array();
	preg_match( '/[0-9]*/', $user_name, $match );
	if ( $match[0] == $user_name )
		$errors->add('user_name', __("Sorry, usernames must have letters too!"));

	if ( !is_email( $user_email ) )
		$errors->add('user_email', __("Please enter a correct email address"));

	if ( !validate_email( $user_email ) )
		$errors->add('user_email', __("Please check your email address."));

	$limited_email_domains = get_site_option( 'limited_email_domains' );
	if ( is_array( $limited_email_domains ) && empty( $limited_email_domains ) == false ) {
		$emaildomain = substr( $user_email, 1 + strpos( $user_email, '@' ) );
		if ( in_array( $emaildomain, $limited_email_domains ) == false )
			$errors->add('user_email', __("Sorry, that email address is not allowed!"));
	}

	// Check if the username has been used already.
	if ( username_exists($user_name) )
		$errors->add('user_name', __("Sorry, that username already exists!"));

	// Check if the email address has been used already.
	if ( email_exists($user_email) )
		$errors->add('user_email', __("Sorry, that email address is already used!"));

	// Has someone already signed up for this username?
	$signup = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->signups WHERE user_login = %s", $user_name) );
	if ( $signup != null ) {
		$registered_at =  mysql2date('U', $signup->registered);
		$now = current_time( 'timestamp', true );
		$diff = $now - $registered_at;
		// If registered more than two days ago, cancel registration and let this signup go through.
		if ( $diff > 172800 )
			$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->signups WHERE user_login = %s", $user_name) );
		else
			$errors->add('user_name', __("That username is currently reserved but may be available in a couple of days."));

		if ( $signup->active == 0 && $signup->user_email == $user_email )
			$errors->add('user_email_used', __("username and email used"));
	}

	$signup = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->signups WHERE user_email = %s", $user_email) );
	if ( $signup != null ) {
		$diff = current_time( 'timestamp', true ) - mysql2date('U', $signup->registered);
		// If registered more than two days ago, cancel registration and let this signup go through.
		if ( $diff > 172800 )
			$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->signups WHERE user_email = %s", $user_email) );
		else
			$errors->add('user_email', __("That email address has already been used. Please check your inbox for an activation email. It will become available in a couple of days if you do nothing."));
	}

	$result = array('user_name' => $user_name, 'user_email' => $user_email,	'errors' => $errors);

	return apply_filters('wpmu_validate_user_signup', $result);
}

function wpmu_validate_blog_signup($blogname, $blog_title, $user = '') {
	global $wpdb, $domain, $base, $current_site;

	$blogname = preg_replace( "/\s+/", '', sanitize_user( $blogname, true ) );
	$blog_title = strip_tags( $blog_title );
	$blog_title = substr( $blog_title, 0, 50 );

	$errors = new WP_Error();
	$illegal_names = get_site_option( "illegal_names" );
	if ( $illegal_names == false ) {
		$illegal_names = array( "www", "web", "root", "admin", "main", "invite", "administrator" );
		add_site_option( "illegal_names", $illegal_names );
	}

	if ( empty( $blogname ) )
		$errors->add('blogname', __("Please enter a blog name"));

	$maybe = array();
	preg_match( "/[a-z0-9]+/", $blogname, $maybe );
	if ( $blogname != $maybe[0] )
		$errors->add('blogname', __("Only lowercase letters and numbers allowed"));

	if ( in_array( $blogname, $illegal_names ) == true )
		$errors->add('blogname',  __("That name is not allowed"));

	if ( strlen( $blogname ) < 4 && !is_super_admin() )
		$errors->add('blogname',  __("Blog name must be at least 4 characters"));

	if ( strpos( " " . $blogname, "_" ) != false )
		$errors->add('blogname', __("Sorry, blog names may not contain the character '_'!"));

	// do not allow users to create a blog that conflicts with a page on the main blog.
	if ( !is_subdomain_install() && $wpdb->get_var( $wpdb->prepare( "SELECT post_name FROM " . $wpdb->get_blog_prefix( $current_site->blog_id ) . "posts WHERE post_type = 'page' AND post_name = %s", $blogname ) ) )
		$errors->add( 'blogname', __( "Sorry, you may not use that blog name" ) );

	// all numeric?
	$match = array();
	preg_match( '/[0-9]*/', $blogname, $match );
	if ( $match[0] == $blogname )
		$errors->add('blogname', __("Sorry, blog names must have letters too!"));

	$blogname = apply_filters( "newblogname", $blogname );

	$blog_title = stripslashes(  $blog_title );

	if ( empty( $blog_title ) )
		$errors->add('blog_title', __("Please enter a blog title"));

	// Check if the domain/path has been used already.
	if ( is_subdomain_install() ) {
		$mydomain = "$blogname.$domain";
		$path = $base;
	} else {
		$mydomain = "$domain";
		$path = $base.$blogname.'/';
	}
	if ( domain_exists($mydomain, $path) )
		$errors->add('blogname', __("Sorry, that blog already exists!"));

	if ( username_exists( $blogname ) ) {
		if ( is_object( $user ) == false || ( is_object($user) && ( $user->user_login != $blogname ) ) )
			$errors->add( 'blogname', __( "Sorry, that blog is reserved!" ) );
	}

	// Has someone already signed up for this domain?
	$signup = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->signups WHERE domain = %s AND path = %s", $mydomain, $path) ); // TODO: Check email too?
	if ( ! empty($signup) ) {
		$diff = current_time( 'timestamp', true ) - mysql2date('U', $signup->registered);
		// If registered more than two days ago, cancel registration and let this signup go through.
		if ( $diff > 172800 )
			$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->signups WHERE domain = %s AND path = %s", $mydomain, $path) );
		else
			$errors->add('blogname', __("That blog is currently reserved but may be available in a couple days."));
	}

	$result = array('domain' => $mydomain, 'path' => $path, 'blogname' => $blogname, 'blog_title' => $blog_title, 'errors' => $errors);
	return apply_filters('wpmu_validate_blog_signup', $result);
}

// Record signup information for future activation. wpmu_validate_signup() should be run
// on the inputs before calling wpmu_signup().
function wpmu_signup_blog($domain, $path, $title, $user, $user_email, $meta = '') {
	global $wpdb;

	$key = substr( md5( time() . rand() . $domain ), 0, 16 );
	$meta = serialize($meta);
	$domain = $wpdb->escape($domain);
	$path = $wpdb->escape($path);
	$title = $wpdb->escape($title);

	$wpdb->insert( $wpdb->signups, array(
		'domain' => $domain,
		'path' => $path,
		'title' => $title,
		'user_login' => $user,
		'user_email' => $user_email,
		'registered' => current_time('mysql', true),
		'activation_key' => $key,
		'meta' => $meta
	) );

	wpmu_signup_blog_notification($domain, $path, $title, $user, $user_email, $key, $meta);
}

function wpmu_signup_user($user, $user_email, $meta = '') {
	global $wpdb;

	// Format data
	$user = preg_replace( "/\s+/", '', sanitize_user( $user, true ) );
	$user_email = sanitize_email( $user_email );
	$key = substr( md5( time() . rand() . $user_email ), 0, 16 );
	$meta = serialize($meta);

	$wpdb->insert( $wpdb->signups, array(
		'domain' => '',
		'path' => '',
		'title' => '',
		'user_login' => $user,
		'user_email' => $user_email,
		'registered' => current_time('mysql', true),
		'activation_key' => $key,
		'meta' => $meta
	) );

	wpmu_signup_user_notification($user, $user_email, $key, $meta);
}

// Notify user of signup success.
function wpmu_signup_blog_notification($domain, $path, $title, $user, $user_email, $key, $meta = '') {
	global $current_site;

	if ( !apply_filters('wpmu_signup_blog_notification', $domain, $path, $title, $user, $user_email, $key, $meta) )
		return false;

	// Send email with activation link.
	if ( !is_subdomain_install() || $current_site->id != 1 )
		$activate_url = "http://" . $current_site->domain . $current_site->path . "wp-activate.php?key=$key";
	else
		$activate_url = "http://{$domain}{$path}wp-activate.php?key=$key";

	$activate_url = clean_url($activate_url);
	$admin_email = get_site_option( "admin_email" );
	if ( $admin_email == '' )
		$admin_email = 'support@' . $_SERVER['SERVER_NAME'];
	$from_name = get_site_option( "site_name" ) == '' ? 'WordPress' : wp_specialchars( get_site_option( "site_name" ) );
	$message_headers = "MIME-Version: 1.0\n" . "From: \"{$from_name}\" <{$admin_email}>\n" . "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";
	$message = sprintf( apply_filters( 'wpmu_signup_blog_notification_email', __( "To activate your blog, please click the following link:\n\n%s\n\nAfter you activate, you will receive *another email* with your login.\n\nAfter you activate, you can visit your blog here:\n\n%s" ) ), $activate_url, clean_url( "http://{$domain}{$path}" ), $key );
	// TODO: Don't hard code activation link.
	$subject = sprintf( apply_filters( 'wpmu_signup_blog_notification_subject', __( '[%1s] Activate %2s' ) ), $from_name, clean_url( 'http://' . $domain . $path ) );
	wp_mail($user_email, $subject, $message, $message_headers);
	return true;
}

function wpmu_signup_user_notification($user, $user_email, $key, $meta = '') {
	global $current_site;

	if ( !apply_filters('wpmu_signup_user_notification', $user, $user_email, $key, $meta) )
		return false;

	// Send email with activation link.
	$admin_email = get_site_option( "admin_email" );
	if ( $admin_email == '' )
		$admin_email = 'support@' . $_SERVER['SERVER_NAME'];
	$from_name = get_site_option( "site_name" ) == '' ? 'WordPress' : wp_specialchars( get_site_option( "site_name" ) );
	$message_headers = "MIME-Version: 1.0\n" . "From: \"{$from_name}\" <{$admin_email}>\n" . "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";
	$message = sprintf( apply_filters( 'wpmu_signup_user_notification_email', __( "To activate your user, please click the following link:\n\n%s\n\nAfter you activate, you will receive *another email* with your login.\n\n" ) ), site_url( "wp-activate.php?key=$key" ), $key );
	// TODO: Don't hard code activation link.
	$subject = sprintf( __( apply_filters( 'wpmu_signup_user_notification_subject', '[%1s] Activate %2s' ) ), $from_name, $user);
	wp_mail($user_email, $subject, $message, $message_headers);
	return true;
}

function wpmu_activate_signup($key) {
	global $wpdb, $current_site;

	$signup = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->signups WHERE activation_key = %s", $key) );

	if ( empty($signup) )
		return new WP_Error('invalid_key', __('Invalid activation key.'));

	if ( $signup->active )
		return new WP_Error('already_active', __('The blog is already active.'), $signup);

	$meta = unserialize($signup->meta);
	$user_login = $wpdb->escape($signup->user_login);
	$user_email = $wpdb->escape($signup->user_email);
	wpmu_validate_user_signup($user_login, $user_email);
	$password = wp_generate_password();

	$user_id = username_exists($user_login);

	if ( ! $user_id )
		$user_id = wpmu_create_user($user_login, $password, $user_email);
	else
		$user_already_exists = true;

	if ( ! $user_id )
		return new WP_Error('create_user', __('Could not create user'), $signup);

	$now = current_time('mysql', true);

	if ( empty($signup->domain) ) {
		$wpdb->update( $wpdb->signups, array('active' => 1, 'activated' => $now), array('activation_key' => $key) );

		if ( isset( $user_already_exists ) )
			return new WP_Error( 'user_already_exists', __( 'That username is already activated.' ), $signup);

		wpmu_welcome_user_notification($user_id, $password, $meta);
		$user_site = get_site_option( 'dashboard_blog', $current_site->blog_id );

		if ( $user_site == false )
			add_user_to_blog( '1', $user_id, get_site_option( 'default_user_role', 'subscriber' ) );
		else
			add_user_to_blog( $user_site, $user_id, get_site_option( 'default_user_role', 'subscriber' ) );

		add_new_user_to_blog( $user_id, $user_email, $meta );
		do_action('wpmu_activate_user', $user_id, $password, $meta);
		return array('user_id' => $user_id, 'password' => $password, 'meta' => $meta);
	}

	wpmu_validate_blog_signup($signup->domain, $signup->title);
	$blog_id = wpmu_create_blog( $signup->domain, $signup->path, $signup->title, $user_id, $meta, $wpdb->siteid );

	// TODO: What to do if we create a user but cannot create a blog?
	if ( is_wp_error($blog_id) ) {
		// If blog is taken, that means a previous attempt to activate this blog failed in between creating the blog and
		// setting the activation flag.  Let's just set the active flag and instruct the user to reset their password.
		if ( 'blog_taken' == $blog_id->get_error_code() ) {
			$blog_id->add_data( $signup );
			$wpdb->update( $wpdb->signups, array( 'active' => 1, 'activated' => $now ), array( 'activation_key' => $key ) );
		}
		return $blog_id;
	}

	$wpdb->update( $wpdb->signups, array('active' => 1, 'activated' => $now), array('activation_key' => $key) );
	wpmu_welcome_notification($blog_id, $user_id, $password, $signup->title, $meta);
	do_action('wpmu_activate_blog', $blog_id, $user_id, $password, $signup->title, $meta);

	return array('blog_id' => $blog_id, 'user_id' => $user_id, 'password' => $password, 'title' => $signup->title, 'meta' => $meta);
}

function wpmu_create_user( $user_name, $password, $email) {
	$user_name = preg_replace( "/\s+/", '', sanitize_user( $user_name, true ) );

	$user_id = wp_create_user( $user_name, $password, $email );
	if ( is_wp_error($user_id) )
		return false;

	$user = new WP_User($user_id);

	// Newly created users have no roles or caps until they are added to a blog.
	update_user_option($user_id, 'capabilities', '');
	update_user_option($user_id, 'user_level', '');

	do_action( 'wpmu_new_user', $user_id );

	return $user_id;
}

function wpmu_create_blog($domain, $path, $title, $user_id, $meta = '', $site_id = 1) {
	$domain = preg_replace( "/\s+/", '', sanitize_user( $domain, true ) );

	if ( is_subdomain_install() )
		$domain = str_replace( '@', '', $domain );

	$title = strip_tags( $title );
	$user_id = (int) $user_id;

	if ( empty($path) )
		$path = '/';

	// Check if the domain has been used already. We should return an error message.
	if ( domain_exists($domain, $path, $site_id) )
		return new WP_Error('blog_taken', __('Blog already exists.'));

	if ( !defined("WP_INSTALLING") )
		define( "WP_INSTALLING", true );

	if ( ! $blog_id = insert_blog($domain, $path, $site_id) )
		return new WP_Error('insert_blog', __('Could not create blog.'));

	switch_to_blog($blog_id);
	install_blog($blog_id, $title);
	wp_install_defaults($user_id);

	add_user_to_blog($blog_id, $user_id, 'administrator');

	if ( is_array($meta) ) foreach ($meta as $key => $value) {
		if ( $key == 'public' || $key == 'archived' || $key == 'mature' || $key == 'spam' || $key == 'deleted' || $key == 'lang_id' )
			update_blog_status( $blog_id, $key, $value );
		else
			update_option( $key, $value );
	}

	add_option( 'WPLANG', get_site_option( 'WPLANG' ) );
	update_option( 'blog_public', $meta['public'] );

	if ( !is_super_admin() && get_usermeta( $user_id, 'primary_blog' ) == get_site_option( 'dashboard_blog', 1 ) )
		update_usermeta( $user_id, 'primary_blog', $blog_id );

	restore_current_blog();
	do_action( 'wpmu_new_blog', $blog_id, $user_id );

	return $blog_id;
}

function newblog_notify_siteadmin( $blog_id, $deprecated = '' ) {
	global $current_site;
	if ( get_site_option( 'registrationnotification' ) != 'yes' )
		return false;

	$email = get_site_option( 'admin_email' );
	if ( is_email($email) == false )
		return false;

	$options_site_url = clean_url("http://{$current_site->domain}{$current_site->path}wp-admin/ms-options.php");

	switch_to_blog( $blog_id );
	$blogname = get_option( 'blogname' );
	$siteurl = get_option( 'siteurl' );
	restore_current_blog();

	$msg = sprintf( __( "New Blog: %1s
URL: %2s
Remote IP: %3s

Disable these notifications: %4s"), $blogname, $siteurl, $_SERVER['REMOTE_ADDR'], $options_site_url);
	$msg = apply_filters( 'newblog_notify_siteadmin', $msg );

	wp_mail( $email, sprintf( __( "New Blog Registration: %s" ), $siteurl ), $msg );
	return true;
}

function newuser_notify_siteadmin( $user_id ) {
	global $current_site;

	if ( get_site_option( 'registrationnotification' ) != 'yes' )
		return false;

	$email = get_site_option( 'admin_email' );

	if ( is_email($email) == false )
		return false;

	$user = new WP_User($user_id);

	$options_site_url = clean_url("http://{$current_site->domain}{$current_site->path}wp-admin/ms-options.php");
	$msg = sprintf(__("New User: %1s
Remote IP: %2s

Disable these notifications: %3s"), $user->user_login, $_SERVER['REMOTE_ADDR'], $options_site_url);

	$msg = apply_filters( 'newuser_notify_siteadmin', $msg );
	wp_mail( $email, sprintf(__("New User Registration: %s"), $user->user_login), $msg );
	return true;
}

function domain_exists($domain, $path, $site_id = 1) {
	global $wpdb;
	return $wpdb->get_var( $wpdb->prepare("SELECT blog_id FROM $wpdb->blogs WHERE domain = %s AND path = %s AND site_id = %d", $domain, $path, $site_id) );
}

function insert_blog($domain, $path, $site_id) {
	global $wpdb;

	$path = trailingslashit($path);
	$site_id = (int) $site_id;

	$result = $wpdb->insert( $wpdb->blogs, array('site_id' => $site_id, 'domain' => $domain, 'path' => $path, 'registered' => current_time('mysql')) );
	if ( ! $result )
		return false;

	refresh_blog_details($wpdb->insert_id);
	return $wpdb->insert_id;
}

// Install an empty blog.  wpdb should already be switched.
function install_blog($blog_id, $blog_title = '') {
	global $wpdb, $table_prefix, $wp_roles;
	$wpdb->suppress_errors();

	// Cast for security
	$blog_id = (int) $blog_id;

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	if ( $wpdb->get_results("SELECT ID FROM $wpdb->posts") )
		die(__('<h1>Already Installed</h1><p>You appear to have already installed WordPress. To reinstall please clear your old database tables first.</p>') . '</body></html>');

	$wpdb->suppress_errors(false);

	$url = get_blogaddress_by_id($blog_id);

	// Set everything up
	make_db_current_silent();
	populate_options();
	populate_roles();
	$wp_roles->_init();

	// fix url.
	update_option('siteurl', $url);
	update_option('home', $url);
	update_option('fileupload_url', $url . "files" );
	update_option('upload_path', "wp-content/blogs.dir/" . $blog_id . "/files");
	update_option('blogname', stripslashes( $blog_title ) );
	update_option('admin_email', '');
	$wpdb->update( $wpdb->options, array('option_value' => ''), array('option_name' => 'admin_email') );

	// Default category
	$wpdb->insert( $wpdb->terms, array('term_id' => 1, 'name' => __('Uncategorized'), 'slug' => sanitize_title(__('Uncategorized')), 'term_group' => 0) );
	$wpdb->insert( $wpdb->term_taxonomy, array('term_id' => 1, 'taxonomy' => 'category', 'description' => '', 'parent' => 0, 'count' => 1) );

	// Default link category
	$cat_name = __('Blogroll');
	$cat_slug = sanitize_title($cat_name);

	$blogroll_id = $wpdb->get_var( $wpdb->prepare( "SELECT cat_ID FROM {$wpdb->sitecategories} WHERE category_nicename = %s", $cat_slug ) );

	if ( $blogroll_id == null ) {
		$wpdb->insert( $wpdb->sitecategories, array('cat_ID' => 0, 'cat_name' => $cat_name, 'category_nicename' => $cat_slug, 'last_updated' => current_time('mysql', true)) );
		$blogroll_id = $wpdb->insert_id;
	}
	$wpdb->insert( $wpdb->terms, array('term_id' => $blogroll_id, 'name' => $cat_name, 'slug' => $cat_slug, 'term_group' => 0) );
	$wpdb->insert( $wpdb->term_taxonomy, array('term_id' => $blogroll_id, 'taxonomy' => 'link_category', 'description' => '', 'parent' => 0, 'count' => 2) );
	update_option('default_link_category', $blogroll_id);

	// remove all perms
	$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE meta_key = %s", $table_prefix.'user_level') );
	$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE meta_key = %s", $table_prefix.'capabilities') );

	$wpdb->suppress_errors( false );
}

// Deprecated, use wp_install_defaults()
// should be switched already as $blog_id is ignored.
function install_blog_defaults($blog_id, $user_id) {
	global $wpdb;

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$wpdb->suppress_errors();

	wp_install_defaults($user_id);

	$wpdb->suppress_errors( false );
}

function wpmu_welcome_notification($blog_id, $user_id, $password, $title, $meta = '') {
	global $current_site;

	if ( !apply_filters('wpmu_welcome_notification', $blog_id, $user_id, $password, $title, $meta) )
		return false;

	$welcome_email = stripslashes( get_site_option( 'welcome_email' ) );
	if ( $welcome_email == false )
		$welcome_email = stripslashes( __( "Dear User,

Your new SITE_NAME blog has been successfully set up at:
BLOG_URL

You can log in to the administrator account with the following information:
Username: USERNAME
Password: PASSWORD
Login Here: BLOG_URLwp-login.php

We hope you enjoy your new weblog.
Thanks!

--The WordPress Team
SITE_NAME" ) );

	$url = get_blogaddress_by_id($blog_id);
	$user = new WP_User($user_id);

	$welcome_email = str_replace( "SITE_NAME", $current_site->site_name, $welcome_email );
	$welcome_email = str_replace( "BLOG_TITLE", $title, $welcome_email );
	$welcome_email = str_replace( "BLOG_URL", $url, $welcome_email );
	$welcome_email = str_replace( "USERNAME", $user->user_login, $welcome_email );
	$welcome_email = str_replace( "PASSWORD", $password, $welcome_email );

	$welcome_email = apply_filters( "update_welcome_email", $welcome_email, $blog_id, $user_id, $password, $title, $meta);
	$admin_email = get_site_option( "admin_email" );

	if ( $admin_email == '' )
		$admin_email = 'support@' . $_SERVER['SERVER_NAME'];

	$from_name = get_site_option( "site_name" ) == '' ? 'WordPress' : wp_specialchars( get_site_option( "site_name" ) );
	$message_headers = "MIME-Version: 1.0\n" . "From: \"{$from_name}\" <{$admin_email}>\n" . "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";
	$message = $welcome_email;

	if ( empty( $current_site->site_name ) )
		$current_site->site_name = "WordPress MU";

	$subject = apply_filters( 'update_welcome_subject', sprintf(__('New %1$s Blog: %2$s'), $current_site->site_name, stripslashes( $title ) ) );
	wp_mail($user->user_email, $subject, $message, $message_headers);
	return true;
}

function wpmu_welcome_user_notification($user_id, $password, $meta = '') {
	global $current_site;

	if ( !apply_filters('wpmu_welcome_user_notification', $user_id, $password, $meta) )
		return false;

	$welcome_email = get_site_option( 'welcome_user_email' );

	$user = new WP_User($user_id);

	$welcome_email = apply_filters( "update_welcome_user_email", $welcome_email, $user_id, $password, $meta);
	$welcome_email = str_replace( "SITE_NAME", $current_site->site_name, $welcome_email );
	$welcome_email = str_replace( "USERNAME", $user->user_login, $welcome_email );
	$welcome_email = str_replace( "PASSWORD", $password, $welcome_email );
	$welcome_email = str_replace( "LOGINLINK", wp_login_url(), $welcome_email );

	$admin_email = get_site_option( "admin_email" );

	if ( $admin_email == '' )
		$admin_email = 'support@' . $_SERVER['SERVER_NAME'];

	$from_name = get_site_option( "site_name" ) == '' ? 'WordPress' : wp_specialchars( get_site_option( "site_name" ) );
	$message_headers = "MIME-Version: 1.0\n" . "From: \"{$from_name}\" <{$admin_email}>\n" . "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";
	$message = $welcome_email;

	if ( empty( $current_site->site_name ) )
		$current_site->site_name = "WordPress MU";

	$subject = apply_filters( 'update_welcome_user_subject', sprintf(__('New %1$s User: %2$s'), $current_site->site_name, $user->user_login) );
	wp_mail($user->user_email, $subject, $message, $message_headers);
	return true;
}

function get_current_site() {
	global $current_site;
	return $current_site;
}

function get_user_id_from_string( $string ) {
	global $wpdb;

	$user_id = 0;
	if ( is_email( $string ) ) {
		$user = get_user_by_email($string);
		if ( $user )
			$user_id = $user->ID;
	} elseif ( is_numeric( $string ) ) {
		$user_id = $string;
	} else {
		$user = get_userdatabylogin($string);
		if ( $user )
			$user_id = $user->ID;
	}

	return $user_id;
}

function get_most_recent_post_of_user( $user_id ) {
	global $wpdb;

	$user_blogs = get_blogs_of_user( (int) $user_id );
	$most_recent_post = array();

	// Walk through each blog and get the most recent post
	// published by $user_id
	foreach ( (array) $user_blogs as $blog ) {
		$recent_post = $wpdb->get_row( $wpdb->prepare("SELECT ID, post_date_gmt FROM {$wpdb->base_prefix}{$blog->userblog_id}_posts WHERE post_author = %d AND post_type = 'post' AND post_status = 'publish' ORDER BY post_date_gmt DESC LIMIT 1", $user_id ), ARRAY_A);

		// Make sure we found a post
		if ( isset($recent_post['ID']) ) {
			$post_gmt_ts = strtotime($recent_post['post_date_gmt']);

			// If this is the first post checked or if this post is
			// newer than the current recent post, make it the new
			// most recent post.
			if ( !isset($most_recent_post['post_gmt_ts']) || ( $post_gmt_ts > $most_recent_post['post_gmt_ts'] ) ) {
				$most_recent_post = array(
					'blog_id'		=> $blog->userblog_id,
					'post_id'		=> $recent_post['ID'],
					'post_date_gmt'	=> $recent_post['post_date_gmt'],
					'post_gmt_ts'	=> $post_gmt_ts
				);
			}
		}
	}

	return $most_recent_post;
}

/* Misc functions */
function fix_upload_details( $uploads ) {
	$uploads['url'] = str_replace( UPLOADS, "files", $uploads['url'] );
	$uploads['baseurl'] = str_replace( UPLOADS, "files", $uploads['baseurl'] );
	return $uploads;
}

function get_dirsize( $directory ) {
	$dirsize = get_transient( 'dirsize_cache' );
	if ( is_array( $dirsize ) && isset( $dirsize[ $directory ][ 'size' ] ) )
		return $dirsize[ $directory ][ 'size' ];

	if ( false == is_array( $dirsize ) )
		$dirsize = array();

	$dirsize[ $directory ][ 'size' ] = recurse_dirsize( $directory );

	set_transient( 'dirsize_cache', $dirsize, 3600 );
	return $dirsize[ $directory ][ 'size' ];
}

function clear_dirsize_cache( $file = true ) {
	delete_transient( 'dirsize_cache' );
	return $file;
}
add_filter( 'wp_handle_upload', 'clear_dirsize_cache' );
add_action( 'delete_attachment', 'clear_dirsize_cache' );

function recurse_dirsize( $directory ) {
	$size = 0;

	if ( substr( $directory, -1 ) == '/' )
		$directory = substr($directory,0,-1);

	if ( !file_exists($directory) || !is_dir( $directory ) || !is_readable( $directory ) )
		return false;

	if ($handle = opendir($directory)) {
		while(($file = readdir($handle)) !== false) {
			$path = $directory.'/'.$file;
			if ($file != '.' && $file != '..') {
				if (is_file($path)) {
					$size += filesize($path);
				} elseif (is_dir($path)) {
					$handlesize = recurse_dirsize($path);
					if ($handlesize >= 0)
						$size += $handlesize;
					else
						return false;
				}
			}
		}
		closedir($handle);
	}
	return $size;
}

function upload_is_user_over_quota( $echo = true ) {
	if ( get_site_option( 'upload_space_check_disabled' ) )
		return true;

	$spaceAllowed = get_space_allowed();
	if ( empty( $spaceAllowed ) || !is_numeric( $spaceAllowed ) )
		$spaceAllowed = 10;	// Default space allowed is 10 MB

	$dirName = BLOGUPLOADDIR;
	$size = get_dirsize($dirName) / 1024 / 1024;

	if ( ($spaceAllowed-$size) < 0 ) {
		if ( $echo )
			_e( "Sorry, you have used your space allocation. Please delete some files to upload more files." ); //No space left
		return true;
	} else {
		return false;
	}
}

function check_upload_mimes( $mimes ) {
	$site_exts = explode( " ", get_site_option( "upload_filetypes" ) );
	foreach ( $site_exts as $ext ) {
		foreach ( $mimes as $ext_pattern => $mime )
			if ( $ext != '' && strpos( $ext_pattern, $ext ) !== false ) {
				$site_mimes[$ext_pattern] = $mime;
		}
	}
	return $site_mimes;
}

function update_posts_count( $deprecated = '' ) {
	global $wpdb;
	update_option( "post_count", (int) $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_status = 'publish' and post_type = 'post'" ) );
}

function wpmu_log_new_registrations( $blog_id, $user_id ) {
	global $wpdb;
	$user = new WP_User( (int) $user_id );
	$wpdb->insert( $wpdb->registration_log, array('email' => $user->user_email, 'IP' => preg_replace( '/[^0-9., ]/', '',$_SERVER['REMOTE_ADDR'] ), 'blog_id' => $blog_id, 'date_registered' => current_time('mysql')) );
}

function fix_import_form_size( $size ) {
	if ( upload_is_user_over_quota( false ) == true )
		return 0;

	$spaceAllowed = 1024 * 1024 * get_space_allowed();
	$dirName = BLOGUPLOADDIR;
	$dirsize = get_dirsize($dirName) ;
	if ( $size > $spaceAllowed - $dirsize )
		return $spaceAllowed - $dirsize; // remaining space
	else
		return $size; // default
}

if ( !function_exists('graceful_fail') ) :
function graceful_fail( $message ) {
	$message = apply_filters('graceful_fail', $message);
	$message_template = apply_filters( 'graceful_fail_template',
'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Error!</title>
<style type="text/css">
img {
	border: 0;
}
body {
line-height: 1.6em; font-family: Georgia, serif; width: 390px; margin: auto;
text-align: center;
}
.message {
	font-size: 22px;
	width: 350px;
	margin: auto;
}
</style>
</head>
<body>
<p class="message">%s</p>
</body>
</html>' );
	die( sprintf( $message_template, $message ) );
}
endif;

/* Delete blog */
class delete_blog {
	function delete_blog() {
		$this->reallydeleteblog = false;
		add_action('admin_menu', array(&$this, 'admin_menu'));
		add_action('admin_footer', array(&$this, 'admin_footer'));
	}

	function admin_footer() {
		global $wpdb, $current_blog, $current_site;
		if ( $current_blog->domain . $current_blog->path == $current_site->domain . $current_site->path )
			return false;

		if ( $this->reallydeleteblog == true )
			wpmu_delete_blog( $wpdb->blogid );
	}

	function admin_menu() {
		global $current_blog, $current_site;
		if ( $current_blog->domain . $current_blog->path != $current_site->domain . $current_site->path )
			add_submenu_page('options-general.php', __('Delete Blog'), __('Delete Blog'), 'manage_options', 'delete-blog', array(&$this, 'plugin_content'));
	}

	function plugin_content() {
		global $current_blog, $current_site;
		$this->delete_blog_hash = get_settings('delete_blog_hash');
		echo '<div class="wrap"><h2>' . __('Delete Blog') . '</h2>';
		if ( $_POST['action'] == "deleteblog" && $_POST['confirmdelete'] == '1' ) {
			$hash = substr( md5( $_SERVER['REQUEST_URI'] . time() ), 0, 6 );
			update_option( "delete_blog_hash", $hash );
			$url_delete = get_option( "siteurl" ) . "/wp-admin/options-general.php?page=delete-blog&h=" . $hash;
			$msg = __("Dear User,
You recently clicked the 'Delete Blog' link on your blog and filled in a
form on that page.
If you really want to delete your blog, click the link below. You will not
be asked to confirm again so only click this link if you are 100% certain:
URL_DELETE

If you delete your blog, please consider opening a new blog here
some time in the future! (But remember your current blog and username
are gone forever.)

Thanks for using the site,
Webmaster
SITE_NAME
");
			$msg = str_replace( "URL_DELETE", $url_delete, $msg );
			$msg = str_replace( "SITE_NAME", $current_site->site_name, $msg );
			wp_mail( get_option( "admin_email" ), "[ " . get_option( "blogname" ) . " ] ".__("Delete My Blog"), $msg );
			?>
			<p><?php _e('Thank you. Please check your email for a link to confirm your action. Your blog will not be deleted until this link is clicked.') ?></p>
			<?php
		} elseif ( isset( $_GET['h'] ) && $_GET['h'] != '' && get_option('delete_blog_hash') != false ) {
			if ( get_option('delete_blog_hash') == $_GET['h'] ) {
				$this->reallydeleteblog = true;
				echo "<p>" . sprintf(__('Thank you for using %s, your blog has been deleted. Happy trails to you until we meet again.'), $current_site->site_name) . "</p>";
			} else {
				$this->reallydeleteblog = false;
				echo "<p>" . __("I'm sorry, the link you clicked is stale. Please select another option.") . "</p>";
			}
		} else {
?>
			<p><?php printf(__('If you do not want to use your %s blog any more, you can delete it using the form below. When you click <strong>Delete My Blog</strong> you will be sent an email with a link in it. Click on this link to delete your blog.'), $current_site->site_name); ?></p>
			<p><?php _e('Remember, once deleted your blog cannot be restored.') ?></p>
			<form method='post' name='deletedirect'>
			<input type="hidden" name="page" value="<?php echo esc_attr($_GET['page']) ?>" />
			<input type='hidden' name='action' value='deleteblog' />
			<p><input id='confirmdelete' type='checkbox' name='confirmdelete' value='1' /> <label for='confirmdelete'><strong><?php printf( __("I'm sure I want to permanently disable my blog, and I am aware I can never get it back or use %s again."), $current_blog->domain); ?></strong></label></p>
			<p class="submit"><input type='submit' value='<?php esc_attr_e('Delete My Blog Permanently') ?>' /></p>
			</form>
<?php
		}
		echo "</div>";
	}
}
$delete_blog_obj = new delete_blog();

/* Global Categories */
function global_terms( $term_id, $deprecated = '' ) {
	global $wpdb;

	$term_id = intval( $term_id );
	$c = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->terms WHERE term_id = %d", $term_id ) );

	$global_id = $wpdb->get_var( $wpdb->prepare( "SELECT cat_ID FROM $wpdb->sitecategories WHERE category_nicename = %s", $c->slug ) );
	if ( $global_id == null ) {
		$wpdb->insert( $wpdb->sitecategories, array('cat_name' => $c->name, 'category_nicename' => $c->slug) );
		$global_id = $wpdb->insert_id;
	}

	if ( $global_id == $term_id )
		return $global_id;

	if ( get_option( 'default_category' ) == $term_id )
		update_option( 'default_category', $global_id );

	$wpdb->update( $wpdb->terms, array('term_id' => $global_id), array('term_id' => $term_id) );
	$wpdb->update( $wpdb->term_taxonomy, array('term_id' => $global_id), array('term_id' => $term_id) );
	$wpdb->update( $wpdb->term_taxonomy, array('parent' => $global_id), array('parent' => $term_id) );

	clean_term_cache($term_id);

	return $global_id;
}

function redirect_this_site( $deprecated = '' ) {
	global $current_site;
	return array( $current_site->domain );
}

function upload_is_file_too_big( $upload ) {
	if ( is_array( $upload ) == false || defined( 'WP_IMPORTING' ) )
		return $upload;

	if ( strlen( $upload[ 'bits' ] )  > ( 1024 * get_site_option( 'fileupload_maxk', 1500 ) ) )
		return sprintf(__( "This file is too big. Files must be less than %dKb in size.<br />" ), get_site_option( 'fileupload_maxk', 1500 ));

	return $upload;
}

function wordpressmu_authenticate_siteadmin( $user, $password = '' ) {
	if ( is_super_admin( $user->user_login ) == false && ( $primary_blog = get_usermeta( $user->user_id, "primary_blog" ) ) ) {
		$details = get_blog_details( $primary_blog );
		if ( is_object( $details ) && $details->spam == 1 )
			return new WP_Error('blog_suspended', __('Blog Suspended.'));
	}
	return $user;
}

function wordpressmu_wp_mail_from( $email ) {
	if ( strpos( $email, 'wordpress@' ) !== false )
		$email = get_option( 'admin_email' );
	return $email;
}

/*
XMLRPC getUsersBlogs() for a multiblog environment
http://trac.mu.wordpress.org/attachment/ticket/551/xmlrpc-mu.php
*/
function wpmu_blogger_getUsersBlogs( $args ) {
	global $current_blog;
	$domain = $current_blog->domain;
	$path = $current_blog->path . 'xmlrpc.php';

	$rpc = new IXR_Client("http://{$domain}{$path}");
	$rpc->query('wp.getUsersBlogs', $args[1], $args[2]);
	$blogs = $rpc->getResponse();

	if ( isset($blogs['faultCode']) )
		return new IXR_Error($blogs['faultCode'], $blogs['faultString']);

	if ( $_SERVER['HTTP_HOST'] == $domain && $_SERVER['REQUEST_URI'] == $path ) {
		return $blogs;
	} else {
		foreach ( (array) $blogs as $blog ) {
			if ( strpos($blog['url'], $_SERVER['HTTP_HOST']) )
				return array($blog);
		}
		return array();
	}
}

function attach_wpmu_xmlrpc( $methods ) {
	$methods['blogger.getUsersBlogs'] = 'wpmu_blogger_getUsersBlogs';
	return $methods;
}

function mu_locale( $locale ) {
	if ( defined('WP_INSTALLING') == false ) {
		$mu_locale = get_option('WPLANG');
		if ( $mu_locale === false )
			$mu_locale = get_site_option('WPLANG');

		if ( $mu_locale !== false )
			return $mu_locale;
	}
	return $locale;
}

function signup_nonce_fields() {
	$id = mt_rand();
	echo "<input type='hidden' name='signup_form_id' value='{$id}' />";
	wp_nonce_field('signup_form_' . $id, '_signup_form', false);
}

function signup_nonce_check( $result ) {
	if ( !strpos( $_SERVER[ 'PHP_SELF' ], 'wp-signup.php' ) )
		return $result;

	if ( wp_create_nonce('signup_form_' . $_POST[ 'signup_form_id' ]) != $_POST['_signup_form'] )
		wp_die( __('Please try again!') );

	return $result;
}

function maybe_redirect_404() {
	global $current_site;
	if ( is_main_blog() && is_404() && defined( 'NOBLOGREDIRECT' ) && constant( 'NOBLOGREDIRECT' ) != '' ) {
		$destination = constant( 'NOBLOGREDIRECT' );
		if ( $destination == '%siteurl%' )
			$destination = $current_site->domain . $current_site->path;
		wp_redirect( $destination );
		exit();
	}
}

function remove_tinymce_media_button( $buttons ) {
	unset( $buttons[ array_search( 'media', $buttons ) ] );
	return $buttons;
}

function maybe_add_existing_user_to_blog() {
	if ( false === strpos( $_SERVER[ 'REQUEST_URI' ], '/newbloguser/' ) )
		return false;

	$parts = explode( '/', $_SERVER[ 'REQUEST_URI' ] );
	$key = array_pop( $parts );

	if ( $key == '' )
		$key = array_pop( $parts );

	$details = get_option( "new_user_" . $key );
	add_existing_user_to_blog( $details );
	delete_option( 'new_user_' . $key );
	wp_die( sprintf(__('You have been added to this blog. Please visit the <a href="%s">homepage</a> or <a href="%s">login</a> using your username and password.'), site_url(), admin_url() ) );
}

function add_existing_user_to_blog( $details = false ) {
	if ( is_array( $details ) ) {
		add_user_to_blog( '', $details[ 'user_id' ], $details[ 'role' ] );
		do_action( "added_existing_user", $details[ 'user_id' ] );
	}
}

function add_new_user_to_blog( $user_id, $email, $meta ) {
	global $current_site;
	if ( $meta[ 'add_to_blog' ] ) {
		$blog_id = $meta[ 'add_to_blog' ];
		$role = $meta[ 'new_role' ];
		remove_user_from_blog($user_id, $current_site->blogid); // remove user from main blog.
		add_user_to_blog( $blog_id, $user_id, $role );
		update_usermeta( $user_id, 'primary_blog', $blog_id );
	}
}

function fix_phpmailer_messageid( $phpmailer ) {
	global $current_site;
	$phpmailer->Hostname = $current_site->domain;
}

function is_user_spammy( $username = 0 ) {
	if ( $username == 0 ) {
		global $current_user;
		$user_id = $current_user->ID;
	} else {
		$user_id = get_user_id_from_string( $username );
	}
	$u = new WP_User( $user_id );

	if ( $u->spam == 1 )
		return true;

	return false;
}

function login_spam_check( $user, $password ) {
	if ( is_user_spammy( $user->ID ) )
		return new WP_Error('invalid_username', __('<strong>ERROR</strong>: your account has been marked as a spammer.'));
	return $user;
}
add_action( 'wp_authenticate_user', 'login_spam_check', 10, 2 );

function update_blog_public( $old_value, $value ) {
	global $wpdb;
	do_action('update_blog_public');
	update_blog_status( $wpdb->blogid, 'public', (int) $value );
}
add_action('update_option_blog_public', 'update_blog_public', 10, 2);

function strtolower_usernames( $username, $raw, $strict ) {
	return strtolower( $username );
}

/* Redirect all hits to "dashboard" blog to wp-admin/ Dashboard. */
function redirect_mu_dashboard() {
	global $current_site, $current_blog;

	$dashboard_blog = get_dashboard_blog();
	if ( $current_blog->blog_id == $dashboard_blog->blog_id && $dashboard_blog->blog_id != $current_site->blog_id ) {
		$protocol = ( is_ssl() ? 'https://' : 'http://' );
		wp_redirect( $protocol . $dashboard_blog->domain . trailingslashit( $dashboard_blog->path ) . 'wp-admin/' );
		die();
	}
}
add_action( 'template_redirect', 'redirect_mu_dashboard' );

function get_dashboard_blog() {
	global $current_site;

	if ( get_site_option( 'dashboard_blog' ) == false )
		return get_blog_details( $current_site->blog_id );
	else
		return get_blog_details( get_site_option( 'dashboard_blog' ) );
}

function is_user_option_local( $key, $user_id = 0, $blog_id = 0 ) {
	global $current_user, $wpdb;

	if ( $user_id == 0 )
		$user_id = $current_user->ID;
	if ( $blog_id == 0 )
		$blog_id = $wpdb->blogid;

	$local_key = $wpdb->base_prefix . $blog_id . "_" . $key;

	if ( isset( $current_user->$local_key ) )
		return true;

	return false;
}

function fix_active_plugins( $value ) {
	if ( false == is_array( $value ) )
		$value = array();
	return $value;
}
add_filter( "option_active_plugins", "fix_active_plugins" );

if ( !function_exists('rss_gc') ) :
function rss_gc() {
	global $wpdb;
	// Garbage Collection
	$rows = $wpdb->get_results( "SELECT meta_key FROM {$wpdb->sitemeta} WHERE meta_key LIKE 'rss\_%\_ts' AND meta_value < unix_timestamp( date_sub( NOW(), interval 7200 second ) )" );
	if ( is_array( $rows ) ) {
		foreach ( $rows as $row ) {
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->sitemeta} WHERE meta_key = %s", $row->meta_key ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->sitemeta} WHERE meta_key = %s", str_replace( '_ts', '', $row->meta_key ) ) );
		}
	}
}
endif;
add_action( 'wp_rss_gc', 'rss_gc' );

function retrieve_password_sitename( $title ) {
	global $current_site;
	return sprintf( __( '[%s] Password Reset' ), $current_site->site_name );
}
add_filter( 'retrieve_password_title', 'retrieve_password_sitename' );

function reset_password_sitename( $title ) {
	global $current_site;
	return sprintf( __( '[%s] Your new password' ), $current_site->site_name );
}
add_filter( 'password_reset_title', 'reset_password_sitename' );

function lowercase_username( $username, $raw_username, $strict ) {
	return strtolower( $username );
}
add_filter( 'sanitize_user', 'lowercase_username', 10, 3 );

function mu_upload_dir( $uploads ) {
	$dir = $uploads[ 'basedir' ];
	if ( defined( 'BLOGUPLOADDIR' ) )
		$dir = constant( 'BLOGUPLOADDIR' );
	$dir = untrailingslashit( $dir ) . $uploads[ 'subdir' ];
	$uploads[ 'path' ] = $dir;

	return $uploads;
}
add_filter( 'upload_dir', 'mu_upload_dir' );

function users_can_register_signup_filter() {
	$registration = get_site_option('registration');
	if ( $registration == 'all' || $registration == 'user' )
		return true;
	else
		return false;
}
add_filter('option_users_can_register', 'users_can_register_signup_filter');

function welcome_user_msg_filter( $text ) {
	if ( !$text ) {
		return __( "Dear User,

Your new account is set up.

You can log in with the following information:
Username: USERNAME
Password: PASSWORD
LOGINLINK

Thanks!

--The Team @ SITE_NAME" );
	}
	return $text;
}
add_filter( 'site_option_welcome_user_email', 'welcome_user_msg_filter' );

function first_page_filter( $text ) {
	if ( !$text )
		return __( "This is an example of a WordPress page, you could edit this to put information about yourself or your site so readers know where you are coming from. You can create as many pages like this one or sub-pages as you like and manage all of your content inside of WordPress." );

	return $text;
}
add_filter( 'site_option_first_page', 'first_page_filter' );

function first_comment_filter( $text ) {
	if ( !$text )
		return __( "This is an example of a WordPress comment, you could edit this to put information about yourself or your site so readers know where you are coming from. You can create as many comments like this one or sub-comments as you like and manage all of your content inside of WordPress." );

	return $text;
}
add_filter( 'site_option_first_comment', 'first_comment_filter' );

function first_comment_author_filter( $text ) {
	if ( !$text )
		return __( "Mr WordPress" );

	return $text;
}
add_filter( 'site_option_first_comment_author', 'first_comment_author_filter' );

function first_comment_url_filter( $text ) {
	global $current_site;
	if ( !$text )
		return 'http://' . $current_site->domain . $current_site->path;

	return $text;
}
add_filter( 'site_option_first_comment_url', 'first_comment_url_filter' );

function mu_filter_plugins_list( $active_plugins ) {
	$active_sitewide_plugins = get_site_option( 'active_sitewide_plugins' );

	if ( !$active_sitewide_plugins )
		return $active_plugins;

	$plugins = array_merge( (array) $active_plugins, array_keys( (array) $active_sitewide_plugins ) );
	sort( $plugins );
	return $plugins;
}
add_filter( 'active_plugins', 'mu_filter_plugins_list' );

 /**
 * Whether to force SSL on content.
 *
 * @since 2.8.5
 *
 * @param string|bool $force
 * @return bool True if forced, false if not forced.
 */
function force_ssl_content( $force = '' ) {
	static $forced_content;

	if ( '' != $force ) {
		$old_forced = $forced_content;
		$forced_content = $force;
		return $old_forced;
	}

	return $forced_content;
}

/**
 * Formats an String URL to use HTTPS if HTTP is found.
 * Useful as a filter.
 *
 * @since 2.8.5
 **/
function filter_SSL( $url) {
	if ( !is_string( $url ) )
		return get_bloginfo( 'url' ); //return home blog url with proper scheme

	$arrURL = parse_url( $url );

	if ( force_ssl_content() && is_ssl() ) {
		if ( 'http' === $arrURL['scheme'] && 'https' !== $arrURL['scheme'] )
			$url = str_replace( $arrURL['scheme'], 'https', $url );
	}

	return $url;
}

function maybe_cancel_post_by_email() {
	if ( !defined( 'POST_BY_EMAIL' ) || !POST_BY_EMAIL )
		die( __( 'This action has been disabled by the administrator' ) );
}
add_action( 'wp-mail.php', 'maybe_cancel_post_by_email' );

?>
