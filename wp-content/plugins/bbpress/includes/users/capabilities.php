<?php

/**
 * bbPress User Capabilites
 *
 * Used to map user capabilities to WordPress's existing capabilities.
 *
 * @package bbPress
 * @subpackage Capabilities
 */

/**
 * Maps primary capabilities
 *
 * @since bbPress (r4242)
 *
 * @param array $caps Capabilities for meta capability
 * @param string $cap Capability name
 * @param int $user_id User id
 * @param mixed $args Arguments
 * @uses apply_filters() Filter mapped results
 * @return array Actual capabilities for meta capability
 */
function bbp_map_primary_meta_caps( $caps = array(), $cap = '', $user_id = 0, $args = array() ) {

	// What capability is being checked?
	switch ( $cap ) {
		case 'spectate'    :
		case 'participate' :
		case 'moderate'    :

			// Do not allow inactive users
			if ( bbp_is_user_inactive( $user_id ) ) {
				$caps = array( 'do_not_allow' );

			// Moderators are always participants
			} else {
				$caps = array( $cap );
			}

			break;
	}

	return apply_filters( 'bbp_map_primary_meta_caps', $caps, $cap, $user_id, $args );
}

/**
 * Return a user's main role
 *
 * @since bbPress (r3860)
 *
 * @param int $user_id
 * @uses bbp_get_user_id() To get the user id
 * @uses get_userdata() To get the user data
 * @uses apply_filters() Calls 'bbp_set_user_role' with the role and user id
 * @return string
 */
function bbp_set_user_role( $user_id = 0, $new_role = '' ) {

	// Validate user id
	$user_id = bbp_get_user_id( $user_id, false, false );
	$user    = get_userdata( $user_id );

	// User exists
	if ( !empty( $user ) ) {

		// Get users forum role
		$role = bbp_get_user_role( $user_id );

		// User already has this role so no new role is set
		if ( $new_role === $role ) {
			$new_role = false;

		// Users role is different than the new role
		} else {

			// Remove the old role
			if ( ! empty( $role ) ) {
				$user->remove_role( $role );
			}

			// Add the new role
			if ( !empty( $new_role ) ) {

				// Make sure bbPress roles are added
				bbp_add_forums_roles();

				$user->add_role( $new_role );
			}
		}

	// User does don exist so return false
	} else {
		$new_role = false;
	}

	return apply_filters( 'bbp_set_user_role', $new_role, $user_id, $user );
}

/**
 * Return a user's forums role
 *
 * @since bbPress (r3860)
 *
 * @param int $user_id
 * @uses bbp_get_user_id() To get the user id
 * @uses get_userdata() To get the user data
 * @uses apply_filters() Calls 'bbp_get_user_role' with the role and user id
 * @return string
 */
function bbp_get_user_role( $user_id = 0 ) {

	// Validate user id
	$user_id = bbp_get_user_id( $user_id );
	$user    = get_userdata( $user_id );
	$role    = false;

	// User has roles so look for a bbPress one
	if ( ! empty( $user->roles ) ) {

		// Look for a bbPress role
		$roles = array_intersect(
			array_values( $user->roles ),
			array_keys( bbp_get_dynamic_roles() )
		);

		// If there's a role in the array, use the first one. This isn't very
		// smart, but since roles aren't exactly hierarchical, and bbPress
		// does not yet have a UI for multiple user roles, it's fine for now.
		if ( !empty( $roles ) ) {
			$role = array_shift( $roles );
		}
	}

	return apply_filters( 'bbp_get_user_role', $role, $user_id, $user );
}

/**
 * Return a user's blog role
 *
 * @since bbPress (r4446)
 *
 * @param int $user_id
 * @uses bbp_get_user_id() To get the user id
 * @uses get_userdata() To get the user data
 * @uses apply_filters() Calls 'bbp_get_user_blog_role' with the role and user id
 * @return string
 */
function bbp_get_user_blog_role( $user_id = 0 ) {

	// Add bbPress roles (returns $wp_roles global)
	bbp_add_forums_roles();

	// Validate user id
	$user_id = bbp_get_user_id( $user_id );
	$user    = get_userdata( $user_id );
	$role    = false;

	// User has roles so lets
	if ( ! empty( $user->roles ) ) {

		// Look for a non bbPress role
		$roles     = array_intersect(
			array_values( $user->roles ),
			array_keys( bbp_get_blog_roles() )
		);

		// If there's a role in the array, use the first one. This isn't very
		// smart, but since roles aren't exactly hierarchical, and WordPress
		// does not yet have a UI for multiple user roles, it's fine for now.
		if ( !empty( $roles ) ) {
			$role = array_shift( $roles );
		}
	}

	return apply_filters( 'bbp_get_user_blog_role', $role, $user_id, $user );
}

/**
 * Helper function hooked to 'bbp_profile_update' action to save or
 * update user roles and capabilities.
 *
 * @since bbPress (r4235)
 *
 * @param int $user_id
 * @uses bbp_reset_user_caps() to reset caps
 * @usse bbp_save_user_caps() to save caps
 */
function bbp_profile_update_role( $user_id = 0 ) {

	// Bail if no user ID was passed
	if ( empty( $user_id ) )
		return;

	// Bail if no role
	if ( ! isset( $_POST['bbp-forums-role'] ) )
		return;

	// Fromus role we want the user to have
	$new_role    = sanitize_text_field( $_POST['bbp-forums-role'] );
	$forums_role = bbp_get_user_role( $user_id );

	// Bail if no role change
	if ( $new_role === $forums_role )
		return;

	// Bail if trying to set their own role
	if ( bbp_is_user_home_edit() )
		return;
	
	// Bail if current user cannot promote the passing user
	if ( ! current_user_can( 'promote_user', $user_id ) )
		return;

	// Set the new forums role
	bbp_set_user_role( $user_id, $new_role );
}

/**
 * Add the default role to the current user if needed
 *
 * This function will bail if the forum is not global in a multisite
 * installation of WordPress, or if the user is marked as spam or deleted.
 *
 * @since bbPress (r3380)
 *
 * @uses is_user_logged_in() To bail if user is not logged in
 * @uses bbp_get_user_role() To bail if user already has a role
 * @uses bbp_is_user_inactive() To bail if user is inactive
 * @uses bbp_allow_global_access() To know whether to save role to database
 * @uses bbp_get_user_role_map() To get the WP to BBP role map array
 * @uses bbp_get_default_role() To get the site's default forums role
 * @uses get_option()
 *
 * @return If not multisite, not global, or user is deleted/spammed
 */
function bbp_set_current_user_default_role() {

	/** Sanity ****************************************************************/

	// Bail if deactivating bbPress
	if ( bbp_is_deactivation() )
		return;

	// Catch all, to prevent premature user initialization
	if ( ! did_action( 'set_current_user' ) )
		return;

	// Bail if not logged in or already a member of this site
	if ( ! is_user_logged_in() )
		return;

	// Get the current user ID
	$user_id = bbp_get_current_user_id();

	// Bail if user already has a forums role
	if ( bbp_get_user_role( $user_id ) )
		return;

	// Bail if user is marked as spam or is deleted
	if ( bbp_is_user_inactive( $user_id ) )
		return;

	/** Ready *****************************************************************/

	// Load up bbPress once
	$bbp         = bbpress();

	// Get whether or not to add a role to the user account
	$add_to_site = bbp_allow_global_access();

	// Get the current user's WordPress role. Set to empty string if none found.
	$user_role   = bbp_get_user_blog_role( $user_id );

	// Get the role map
	$role_map    = bbp_get_user_role_map();

	/** Forum Role ************************************************************/

	// Use a mapped role
	if ( isset( $role_map[$user_role] ) ) {
		$new_role = $role_map[$user_role];

	// Use the default role
	} else {
		$new_role = bbp_get_default_role();
	}

	/** Add or Map ************************************************************/

	// Add the user to the site
	if ( true === $add_to_site ) {

		// Make sure bbPress roles are added
		bbp_add_forums_roles();

		$bbp->current_user->add_role( $new_role );

	// Don't add the user, but still give them the correct caps dynamically
	} else {		
		$bbp->current_user->caps[$new_role] = true;
		$bbp->current_user->get_role_caps();
	}
}

/**
 * Return a map of WordPress roles to bbPress roles. Used to automatically grant
 * appropriate bbPress roles to WordPress users that wouldn't already have a
 * role in the forums. Also guarantees WordPress admins get the Keymaster role.
 *
 * @since bbPress (r4334)
 *
 * @return array Filtered array of WordPress roles to bbPress roles
 */
function bbp_get_user_role_map() {

	// Get the default role once here
	$default_role = bbp_get_default_role();

	// Return filtered results, forcing admins to keymasters.
	return (array) apply_filters( 'bbp_get_user_role_map', array (
		'administrator' => bbp_get_keymaster_role(),
		'editor'        => $default_role,
		'author'        => $default_role,
		'contributor'   => $default_role,
		'subscriber'    => $default_role
	) );
}

/** User Status ***************************************************************/

/**
 * Checks if the user has been marked as a spammer.
 *
 * @since bbPress (r3355)
 *
 * @param int $user_id int The ID for the user.
 * @return bool True if spammer, False if not.
 */
function bbp_is_user_spammer( $user_id = 0 ) {

	// Default to current user
	if ( empty( $user_id ) && is_user_logged_in() )
		$user_id = bbp_get_current_user_id();

	// No user to check
	if ( empty( $user_id ) )
		return false;

	// Assume user is not spam
	$is_spammer = false;

	// Get user data
	$user = get_userdata( $user_id );

	// No user found
	if ( empty( $user ) ) {
		$is_spammer = false;

	// Check if spam
	} elseif ( !empty( $user->spam ) ) {
		$is_spammer = true;
	}

	return (bool) apply_filters( 'bbp_core_is_user_spammer', $is_spammer );
}

/**
 * Mark a users topics and replies as spam when the user is marked as spam
 *
 * @since bbPress (r3405)
 *
 * @global WPDB $wpdb
 * @param int $user_id Optional. User ID to spam. Defaults to displayed user.

 * @uses bbp_is_single_user()
 * @uses bbp_is_user_home()
 * @uses bbp_get_displayed_user_id()
 * @uses bbp_is_user_keymaster()
 * @uses get_blogs_of_user()
 * @uses get_current_blog_id()
 * @uses bbp_get_topic_post_type()
 * @uses bbp_get_reply_post_type()
 * @uses switch_to_blog()
 * @uses get_post_type()
 * @uses bbp_spam_topic()
 * @uses bbp_spam_reply()
 * @uses restore_current_blog()
 *
 * @return If no user ID passed
 */
function bbp_make_spam_user( $user_id = 0 ) {

	// Use displayed user if it's not yourself
	if ( empty( $user_id ) && bbp_is_single_user() && !bbp_is_user_home() )
		$user_id = bbp_get_displayed_user_id();

	// Bail if no user ID
	if ( empty( $user_id ) )
		return false;

	// Bail if user ID is keymaster
	if ( bbp_is_user_keymaster( $user_id ) )
		return false;

	// Arm the torpedos
	global $wpdb;

	// Get the blog IDs of the user to mark as spam
	$blogs = get_blogs_of_user( $user_id, true );

	// If user has no blogs, they are a guest on this site
	if ( empty( $blogs ) )
		$blogs[$wpdb->blogid] = array();

	// Make array of post types to mark as spam
	$post_types  = array( bbp_get_topic_post_type(), bbp_get_reply_post_type() );
	$post_types  = "'" . implode( "', '", $post_types ) . "'";

	// Loop through blogs and remove their posts
	foreach ( (array) array_keys( $blogs ) as $blog_id ) {

		// Switch to the blog ID
		switch_to_blog( $blog_id );

		// Get topics and replies
		$posts = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_author = %d AND post_status = '%s' AND post_type IN ( {$post_types} )", $user_id, bbp_get_public_status_id() ) );

		// Loop through posts and spam them
		if ( !empty( $posts ) ) {
			foreach ( $posts as $post_id ) {

				// The routines for topics ang replies are different, so use the
				// correct one based on the post type
				switch ( get_post_type( $post_id ) ) {

					case bbp_get_topic_post_type() :
						bbp_spam_topic( $post_id );
						break;

					case bbp_get_reply_post_type() :
						bbp_spam_reply( $post_id );
						break;
				}
			}
		}

		// Switch back to current blog
		restore_current_blog();
	}

	// Success
	return true;
}

/**
 * Mark a users topics and replies as spam when the user is marked as spam
 *
 * @since bbPress (r3405)
 *
 * @global WPDB $wpdb
 * @param int $user_id Optional. User ID to unspam. Defaults to displayed user.
 *
 * @uses bbp_is_single_user()
 * @uses bbp_is_user_home()
 * @uses bbp_get_displayed_user_id()
 * @uses bbp_is_user_keymaster()
 * @uses get_blogs_of_user()
 * @uses bbp_get_topic_post_type()
 * @uses bbp_get_reply_post_type()
 * @uses switch_to_blog()
 * @uses get_post_type()
 * @uses bbp_unspam_topic()
 * @uses bbp_unspam_reply()
 * @uses restore_current_blog()
 *
 * @return If no user ID passed
 */
function bbp_make_ham_user( $user_id = 0 ) {

	// Use displayed user if it's not yourself
	if ( empty( $user_id ) && bbp_is_single_user() && !bbp_is_user_home() )
		$user_id = bbp_get_displayed_user_id();

	// Bail if no user ID
	if ( empty( $user_id ) )
		return false;

	// Bail if user ID is keymaster
	if ( bbp_is_user_keymaster( $user_id ) )
		return false;

	// Arm the torpedos
	global $wpdb;

	// Get the blog IDs of the user to mark as spam
	$blogs = get_blogs_of_user( $user_id, true );

	// If user has no blogs, they are a guest on this site
	if ( empty( $blogs ) )
		$blogs[$wpdb->blogid] = array();

	// Make array of post types to mark as spam
	$post_types = array( bbp_get_topic_post_type(), bbp_get_reply_post_type() );
	$post_types = "'" . implode( "', '", $post_types ) . "'";

	// Loop through blogs and remove their posts
	foreach ( (array) array_keys( $blogs ) as $blog_id ) {

		// Switch to the blog ID
		switch_to_blog( $blog_id );

		// Get topics and replies
		$posts = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_author = %d AND post_status = '%s' AND post_type IN ( {$post_types} )", $user_id, bbp_get_spam_status_id() ) );

		// Loop through posts and spam them
		if ( !empty( $posts ) ) {
			foreach ( $posts as $post_id ) {

				// The routines for topics ang replies are different, so use the
				// correct one based on the post type
				switch ( get_post_type( $post_id ) ) {

					case bbp_get_topic_post_type() :
						bbp_unspam_topic( $post_id );
						break;

					case bbp_get_reply_post_type() :
						bbp_unspam_reply( $post_id );
						break;
				}
			}
		}

		// Switch back to current blog
		restore_current_blog();
	}

	// Success
	return true;
}

/**
 * Checks if the user has been marked as deleted.
 *
 * @since bbPress (r3355)
 *
 * @param int $user_id int The ID for the user.
 * @return bool True if deleted, False if not.
 */
function bbp_is_user_deleted( $user_id = 0 ) {

	// Default to current user
	if ( empty( $user_id ) && is_user_logged_in() )
		$user_id = bbp_get_current_user_id();

	// No user to check
	if ( empty( $user_id ) )
		return false;

	// Assume user is not deleted
	$is_deleted = false;

	// Get user data
	$user = get_userdata( $user_id );

	// No user found
	if ( empty( $user ) ) {
		$is_deleted = true;

	// Check if deleted
	} elseif ( !empty( $user->deleted ) ) {
		$is_deleted = true;
	}

	return (bool) apply_filters( 'bbp_core_is_user_deleted', $is_deleted );
}

/**
 * Checks if user is active
 *
 * @since bbPress (r3502)
 *
 * @uses is_user_logged_in() To check if user is logged in
 * @uses bbp_get_displayed_user_id() To get current user ID
 * @uses bbp_is_user_spammer() To check if user is spammer
 * @uses bbp_is_user_deleted() To check if user is deleted
 *
 * @param int $user_id The user ID to check
 * @return bool True if public, false if not
 */
function bbp_is_user_active( $user_id = 0 ) {

	// Default to current user
	if ( empty( $user_id ) && is_user_logged_in() )
		$user_id = bbp_get_current_user_id();

	// No user to check
	if ( empty( $user_id ) )
		return false;

	// Check spam
	if ( bbp_is_user_spammer( $user_id ) )
		return false;

	// Check deleted
	if ( bbp_is_user_deleted( $user_id ) )
		return false;

	// Assume true if not spam or deleted
	return true;
}

/**
 * Checks if user is not active.
 *
 * @since bbPress (r3502)
 *
 * @uses is_user_logged_in() To check if user is logged in
 * @uses bbp_get_displayed_user_id() To get current user ID
 * @uses bbp_is_user_active() To check if user is active
 *
 * @param int $user_id The user ID to check. Defaults to current user ID
 * @return bool True if inactive, false if active
 */
function bbp_is_user_inactive( $user_id = 0 ) {

	// Default to current user
	if ( empty( $user_id ) && is_user_logged_in() )
		$user_id = bbp_get_current_user_id();

	// No user to check
	if ( empty( $user_id ) )
		return false;

	// Return the inverse of active
	return !bbp_is_user_active( $user_id );
}

/**
 * Checks if user is a keymaster
 *
 * @since bbPress (r4783)
 *
 * @param int $user_id 
 * @return bool True if keymaster, false if not
 */
function bbp_is_user_keymaster( $user_id = 0 ) {

	// Default to current user ID if none is passed
	$_user_id = (int) ! empty( $user_id ) ? $user_id : bbp_get_current_user_id();

	// Filter and return
	return (bool) apply_filters( 'bbp_is_user_keymaster', user_can( $_user_id, 'keep_gate' ), $_user_id, $user_id );
}

/**
 * Does a user have a profile for the current site
 *
 * @since bbPress (r4362)
 *
 * @param int $user_id User ID to check
 * @param int $blog_id Blog ID to check
 *
 * @uses bbp_get_user_id() To verify the user ID
 * @uses get_userdata() To get the user's data
 * @uses bbp_is_user_keymaster() To determine if user can see inactive users
 * @uses bbp_is_user_inactive() To check if user is spammer or deleted
 * @uses apply_filters() To allow override of this functions result
 *
 * @return boolean Whether or not the user has a profile on this blog_id
 */
function bbp_user_has_profile( $user_id = 0 ) {

	// Assume every user has a profile
	$retval  = true;

	// Validate user ID, default to displayed or current user
	$user_id = bbp_get_user_id( $user_id, true, true );

	// Try to get this user's data
	$user    = get_userdata( $user_id );

	// No user found, return false
	if ( empty( $user ) ) {
		$retval = false;

	// User is inactive, and current user is not a keymaster
	} elseif ( ! bbp_is_user_keymaster() && bbp_is_user_inactive( $user->ID ) ) {
		$retval = false;
	}

	// Filter and return
	return (bool) apply_filters( 'bbp_show_user_profile', $retval, $user_id );
}
