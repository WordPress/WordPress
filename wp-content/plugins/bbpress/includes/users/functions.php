<?php

/**
 * bbPress User Functions
 *
 * @package bbPress
 * @subpackage Functions
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Redirect back to $url when attempting to use the login page
 *
 * @since bbPress (r2815)
 *
 * @param string $url The url
 * @param string $raw_url Raw url
 * @param object $user User object
 * @uses is_wp_error() To check if the user param is a {@link WP_Error}
 * @uses admin_url() To get the admin url
 * @uses home_url() To get the home url
 * @uses esc_url() To escape the url
 * @uses wp_safe_redirect() To redirect
 */
function bbp_redirect_login( $url = '', $raw_url = '', $user = '' ) {

	// Raw redirect_to was passed, so use it
	if ( !empty( $raw_url ) )
		$url = $raw_url;

	// $url was manually set in wp-login.php to redirect to admin
	elseif ( admin_url() === $url )
		$url = home_url();

	// $url is empty
	elseif ( empty( $url ) )
		$url = home_url();

	return apply_filters( 'bbp_redirect_login', $url, $raw_url, $user );
}

/**
 * Is an anonymous topic/reply being made?
 *
 * @since bbPress (r2688)
 *
 * @uses is_user_logged_in() Is the user logged in?
 * @uses bbp_allow_anonymous() Is anonymous posting allowed?
 * @uses apply_filters() Calls 'bbp_is_anonymous' with the return value
 * @return bool True if anonymous is allowed and user is not logged in, false if
 *               anonymous is not allowed or user is logged in
 */
function bbp_is_anonymous() {
	if ( !is_user_logged_in() && bbp_allow_anonymous() )
		$is_anonymous = true;
	else
		$is_anonymous = false;

	return apply_filters( 'bbp_is_anonymous', $is_anonymous );
}

/**
 * Echoes the values for current poster (uses WP comment cookies)
 *
 * @since bbPress (r2734)
 *
 * @param string $key Which value to echo?
 * @uses bbp_get_current_anonymous_user_data() To get the current anonymous user
 *                                              data
 */
function bbp_current_anonymous_user_data( $key = '' ) {
	echo bbp_get_current_anonymous_user_data( $key );
}

	/**
	 * Get the cookies for current poster (uses WP comment cookies).
	 *
	 * @since bbPress (r2734)
	 *
	 * @param string $key Optional. Which value to get? If not given, then
	 *                     an array is returned.
	 * @uses sanitize_comment_cookies() To sanitize the current poster data
	 * @uses wp_get_current_commenter() To get the current poster data	 *
	 * @return string|array Cookie(s) for current poster
	 */
	function bbp_get_current_anonymous_user_data( $key = '' ) {
		$cookie_names = array(
			'name'  => 'comment_author',
			'email' => 'comment_author_email',
			'url'   => 'comment_author_url',

			// Here just for the sake of them, use the above ones
			'comment_author'       => 'comment_author',
			'comment_author_email' => 'comment_author_email',
			'comment_author_url'   => 'comment_author_url',
		);

		sanitize_comment_cookies();

		$bbp_current_poster = wp_get_current_commenter();

		if ( !empty( $key ) && in_array( $key, array_keys( $cookie_names ) ) )
			return $bbp_current_poster[$cookie_names[$key]];

		return $bbp_current_poster;
	}

/**
 * Set the cookies for current poster (uses WP comment cookies)
 *
 * @since bbPress (r2734)
 *
 * @param array $anonymous_data With keys 'bbp_anonymous_name',
 *                               'bbp_anonymous_email', 'bbp_anonymous_website'.
 *                               Should be sanitized (see
 *                               {@link bbp_filter_anonymous_post_data()} for
 *                               sanitization)
 * @uses apply_filters() Calls 'comment_cookie_lifetime' for cookie lifetime.
 *                        Defaults to 30000000.
 */
function bbp_set_current_anonymous_user_data( $anonymous_data = array() ) {
	if ( empty( $anonymous_data ) || !is_array( $anonymous_data ) )
		return;

	$comment_cookie_lifetime = apply_filters( 'comment_cookie_lifetime', 30000000 );

	setcookie( 'comment_author_'       . COOKIEHASH, $anonymous_data['bbp_anonymous_name'],    time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN );
	setcookie( 'comment_author_email_' . COOKIEHASH, $anonymous_data['bbp_anonymous_email'],   time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN );
	setcookie( 'comment_author_url_'   . COOKIEHASH, $anonymous_data['bbp_anonymous_website'], time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN );
}

/**
 * Get the poster IP address
 *
 * @since bbPress (r3120)
 *
 * @return string
 */
function bbp_current_author_ip() {
	$retval = preg_replace( '/[^0-9a-fA-F:., ]/', '', $_SERVER['REMOTE_ADDR'] );

	return apply_filters( 'bbp_current_author_ip', $retval );
}

/**
 * Get the poster user agent
 *
 * @since bbPress (r3446)
 *
 * @return string
 */
function bbp_current_author_ua() {
	$retval = !empty( $_SERVER['HTTP_USER_AGENT'] ) ? substr( $_SERVER['HTTP_USER_AGENT'], 0, 254 ) : '';

	return apply_filters( 'bbp_current_author_ua', $retval );
}

/** Post Counts ***************************************************************/

/**
 * Return the raw database count of topics by a user
 *
 * @since bbPress (r3633)
 * @global WPDB $wpdb
 * @uses bbp_get_user_id()
 * @uses get_posts_by_author_sql()
 * @uses bbp_get_topic_post_type()
 * @uses apply_filters()
 * @return int Raw DB count of topics
 */
function bbp_get_user_topic_count_raw( $user_id = 0 ) {
	$user_id = bbp_get_user_id( $user_id );
	if ( empty( $user_id ) )
		return false;

	global $wpdb;

	$where = get_posts_by_author_sql( bbp_get_topic_post_type(), true, $user_id );
	$count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} {$where}" );

	return (int) apply_filters( 'bbp_get_user_topic_count_raw', $count, $user_id );
}

/**
 * Return the raw database count of replies by a user
 *
 * @since bbPress (r3633)
 * @global WPDB $wpdb
 * @uses bbp_get_user_id()
 * @uses get_posts_by_author_sql()
 * @uses bbp_get_reply_post_type()
 * @uses apply_filters()
 * @return int Raw DB count of replies
 */
function bbp_get_user_reply_count_raw( $user_id = 0 ) {
	$user_id = bbp_get_user_id( $user_id );
	if ( empty( $user_id ) )
		return false;

	global $wpdb;

	$where = get_posts_by_author_sql( bbp_get_reply_post_type(), true, $user_id );
	$count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} {$where}" );

	return (int) apply_filters( 'bbp_get_user_reply_count_raw', $count, $user_id );
}

/** Favorites *****************************************************************/

/**
 * Get the users who have made the topic favorite
 *
 * @since bbPress (r2658)
 *
 * @param int $topic_id Optional. Topic id
 * @uses wpdb::get_col() To execute our query and get the column back
 * @uses apply_filters() Calls 'bbp_get_topic_favoriters' with the users and
 *                        topic id
 * @return array|bool Results if the topic has any favoriters, otherwise false
 */
function bbp_get_topic_favoriters( $topic_id = 0 ) {
	$topic_id = bbp_get_topic_id( $topic_id );
	if ( empty( $topic_id ) )
		return;

	global $wpdb;

	$key   = $wpdb->prefix . '_bbp_favorites';
	$users = wp_cache_get( 'bbp_get_topic_favoriters_' . $topic_id, 'bbpress_users' );
	if ( false === $users ) {
		$users = $wpdb->get_col( "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = '{$key}' and FIND_IN_SET('{$topic_id}', meta_value) > 0" );
		wp_cache_set( 'bbp_get_topic_favoriters_' . $topic_id, $users, 'bbpress_users' );
	}

	return apply_filters( 'bbp_get_topic_favoriters', $users );
}

/**
 * Get a user's favorite topics
 *
 * @since bbPress (r2652)
 *
 * @param int $user_id Optional. User id
 * @uses bbp_get_user_favorites_topic_ids() To get the user's favorites
 * @uses bbp_has_topics() To get the topics
 * @uses apply_filters() Calls 'bbp_get_user_favorites' with the topic query and
 *                        user id
 * @return array|bool Results if user has favorites, otherwise false
 */
function bbp_get_user_favorites( $user_id = 0 ) {
	$user_id = bbp_get_user_id( $user_id );
	if ( empty( $user_id ) )
		return false;

	// If user has favorites, load them
	$favorites = bbp_get_user_favorites_topic_ids( $user_id );
	if ( !empty( $favorites ) ) {
		$query = bbp_has_topics( array( 'post__in' => $favorites ) );
	} else {
		$query = false;
	}

	return apply_filters( 'bbp_get_user_favorites', $query, $user_id, $favorites );
}

/**
 * Get a user's favorite topics' ids
 *
 * @since bbPress (r2652)
 *
 * @param int $user_id Optional. User id
 * @uses bbp_get_user_id() To get the user id
 * @uses get_user_option() To get the user favorites
 * @uses apply_filters() Calls 'bbp_get_user_favorites_topic_ids' with
 *                        the favorites and user id
 * @return array|bool Results if user has favorites, otherwise false
 */
function bbp_get_user_favorites_topic_ids( $user_id = 0 ) {
	$user_id = bbp_get_user_id( $user_id );
	if ( empty( $user_id ) )
		return false;

	$favorites = get_user_option( '_bbp_favorites', $user_id );
	$favorites = array_filter( wp_parse_id_list( $favorites ) );

	return (array) apply_filters( 'bbp_get_user_favorites_topic_ids', $favorites, $user_id );
}

/**
 * Check if a topic is in user's favorites or not
 *
 * @since bbPress (r2652)
 *
 * @param int $user_id Optional. User id
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_user_id() To get the user id
 * @uses bbp_get_user_favorites_topic_ids() To get the user favorites
 * @uses bbp_get_topic() To get the topic
 * @uses bbp_get_topic_id() To get the topic id
 * @uses apply_filters() Calls 'bbp_is_user_favorite' with the bool, user id,
 *                        topic id and favorites
 * @return bool True if the topic is in user's favorites, otherwise false
 */
function bbp_is_user_favorite( $user_id = 0, $topic_id = 0 ) {

	$user_id = bbp_get_user_id( $user_id, true, true );
	if ( empty( $user_id ) )
		return false;

	$retval    = false;
	$favorites = bbp_get_user_favorites_topic_ids( $user_id );

	if ( !empty( $favorites ) ) {

		// Checking a specific topic id
		if ( !empty( $topic_id ) ) {
			$topic    = bbp_get_topic( $topic_id );
			$topic_id = !empty( $topic ) ? $topic->ID : 0;

		// Using the global topic id
		} elseif ( bbp_get_topic_id() ) {
			$topic_id = bbp_get_topic_id();

		// Use the current post id
		} elseif ( !bbp_get_topic_id() ) {
			$topic_id = get_the_ID();
		}

		// Is topic_id in the user's favorites
		if ( !empty( $topic_id ) ) {
			$retval = in_array( $topic_id, $favorites );
		}
	}

	return (bool) apply_filters( 'bbp_is_user_favorite', (bool) $retval, $user_id, $topic_id, $favorites );
}

/**
 * Add a topic to user's favorites
 *
 * @since bbPress (r2652)
 *
 * @param int $user_id Optional. User id
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_user_favorites_topic_ids() To get the user favorites
 * @uses update_user_option() To update the user favorites
 * @uses do_action() Calls 'bbp_add_user_favorite' with the user id and topic id
 * @return bool Always true
 */
function bbp_add_user_favorite( $user_id = 0, $topic_id = 0 ) {
	if ( empty( $user_id ) || empty( $topic_id ) )
		return false;

	$topic = bbp_get_topic( $topic_id );
	if ( empty( $topic ) )
		return false;

	$favorites = bbp_get_user_favorites_topic_ids( $user_id );
	if ( !in_array( $topic_id, $favorites ) ) {
		$favorites[] = $topic_id;
		$favorites   = implode( ',', wp_parse_id_list( array_filter( $favorites ) ) );
		update_user_option( $user_id, '_bbp_favorites', $favorites );
	}

	do_action( 'bbp_add_user_favorite', $user_id, $topic_id );

	return true;
}

/**
 * Remove a topic from user's favorites
 *
 * @since bbPress (r2652)
 *
 * @param int $user_id Optional. User id
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_user_favorites_topic_ids() To get the user favorites
 * @uses update_user_option() To update the user favorites
 * @uses delete_user_option() To delete the user favorites meta
 * @uses do_action() Calls 'bbp_remove_user_favorite' with the user & topic id
 * @return bool True if the topic was removed from user's favorites, otherwise
 *               false
 */
function bbp_remove_user_favorite( $user_id, $topic_id ) {
	if ( empty( $user_id ) || empty( $topic_id ) )
		return false;

	$favorites = (array) bbp_get_user_favorites_topic_ids( $user_id );
	if ( empty( $favorites ) )
		return false;

	$pos = array_search( $topic_id, $favorites );
	if ( is_numeric( $pos ) ) {
		array_splice( $favorites, $pos, 1 );
		$favorites = array_filter( $favorites );

		if ( !empty( $favorites ) ) {
			$favorites = implode( ',', wp_parse_id_list( $favorites ) );
			update_user_option( $user_id, '_bbp_favorites', $favorites );
		} else {
			delete_user_option( $user_id, '_bbp_favorites' );
		}
	}

	do_action( 'bbp_remove_user_favorite', $user_id, $topic_id );

	return true;
}

/**
 * Handles the front end adding and removing of favorite topics
 *
 * @param string $action The requested action to compare this function to
 * @uses bbp_get_user_id() To get the user id
 * @uses bbp_verify_nonce_request() To verify the nonce and check the request
 * @uses current_user_can() To check if the current user can edit the user
 * @uses bbPress:errors:add() To log the error messages
 * @uses bbp_is_user_favorite() To check if the topic is in user's favorites
 * @uses bbp_remove_user_favorite() To remove the user favorite
 * @uses bbp_add_user_favorite() To add the user favorite
 * @uses do_action() Calls 'bbp_favorites_handler' with success, user id, topic
 *                    id and action
 * @uses bbp_is_favorites() To check if it's the favorites page
 * @uses bbp_get_favorites_link() To get the favorites page link
 * @uses bbp_get_topic_permalink() To get the topic permalink
 * @uses wp_safe_redirect() To redirect to the url
 */
function bbp_favorites_handler( $action = '' ) {

	if ( !bbp_is_favorites_active() )
		return false;

	// Bail if no topic ID is passed
	if ( empty( $_GET['topic_id'] ) )
		return;

	// Setup possible get actions
	$possible_actions = array(
		'bbp_favorite_add',
		'bbp_favorite_remove',
	);

	// Bail if actions aren't meant for this function
	if ( !in_array( $action, $possible_actions ) )
		return;

	// What action is taking place?
	$topic_id    = intval( $_GET['topic_id'] );
	$user_id     = bbp_get_user_id( 0, true, true );

	// Check for empty topic
	if ( empty( $topic_id ) ) {
		bbp_add_error( 'bbp_favorite_topic_id', __( '<strong>ERROR</strong>: No topic was found! Which topic are you marking/unmarking as favorite?', 'bbpress' ) );

	// Check nonce
	} elseif ( ! bbp_verify_nonce_request( 'toggle-favorite_' . $topic_id ) ) {
		bbp_add_error( 'bbp_favorite_nonce', __( '<strong>ERROR</strong>: Are you sure you wanted to do that?', 'bbpress' ) );

	// Check current user's ability to edit the user
	} elseif ( !current_user_can( 'edit_user', $user_id ) ) {
		bbp_add_error( 'bbp_favorite_permissions', __( '<strong>ERROR</strong>: You don\'t have the permission to edit favorites of that user!', 'bbpress' ) );
	}

	// Bail if errors
	if ( bbp_has_errors() )
		return;

	/** No errors *************************************************************/

	$is_favorite = bbp_is_user_favorite( $user_id, $topic_id );
	$success     = false;

	if ( true === $is_favorite && 'bbp_favorite_remove' === $action )
		$success = bbp_remove_user_favorite( $user_id, $topic_id );
	elseif ( false === $is_favorite && 'bbp_favorite_add' === $action )
		$success = bbp_add_user_favorite( $user_id, $topic_id );

	// Do additional favorites actions
	do_action( 'bbp_favorites_handler', $success, $user_id, $topic_id, $action );

	// Success!
	if ( true === $success ) {

		// Redirect back from whence we came
		if ( bbp_is_favorites() ) {
			$redirect = bbp_get_favorites_permalink( $user_id );
		} elseif ( bbp_is_single_user() ) {
			$redirect = bbp_get_user_profile_url();
		} elseif ( is_singular( bbp_get_topic_post_type() ) ) {
			$redirect = bbp_get_topic_permalink( $topic_id );
		} elseif ( is_single() || is_page() ) {
			$redirect = get_permalink();
		} else {
			$redirect = get_permalink( $topic_id );
		}

		wp_safe_redirect( $redirect );

		// For good measure
		exit();

	// Fail! Handle errors
	} elseif ( true === $is_favorite && 'bbp_favorite_remove' === $action ) {
		bbp_add_error( 'bbp_favorite_remove', __( '<strong>ERROR</strong>: There was a problem removing that topic from favorites!', 'bbpress' ) );
	} elseif ( false === $is_favorite && 'bbp_favorite_add' === $action ) {
		bbp_add_error( 'bbp_favorite_add',    __( '<strong>ERROR</strong>: There was a problem favoriting that topic!', 'bbpress' ) );
	}
}

/** Subscriptions *************************************************************/

/**
 * Get the users who have subscribed to the forum
 *
 * @since bbPress (r5156)
 *
 * @param int $forum_id Optional. forum id
 * @uses wpdb::get_col() To execute our query and get the column back
 * @uses apply_filters() Calls 'bbp_get_forum_subscribers' with the subscribers
 * @return array|bool Results if the forum has any subscribers, otherwise false
 */
function bbp_get_forum_subscribers( $forum_id = 0 ) {
	$forum_id = bbp_get_forum_id( $forum_id );
	if ( empty( $forum_id ) )
		return;

	global $wpdb;

	$key   = $wpdb->prefix . '_bbp_forum_subscriptions';
	$users = wp_cache_get( 'bbp_get_forum_subscribers_' . $forum_id, 'bbpress_users' );
	if ( false === $users ) {
		$users = $wpdb->get_col( "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = '{$key}' and FIND_IN_SET('{$forum_id}', meta_value) > 0" );
		wp_cache_set( 'bbp_get_forum_subscribers_' . $forum_id, $users, 'bbpress_users' );
	}

	return apply_filters( 'bbp_get_forum_subscribers', $users );
}

/**
 * Get the users who have subscribed to the topic
 *
 * @since bbPress (r2668)
 *
 * @param int $topic_id Optional. Topic id
 * @uses wpdb::get_col() To execute our query and get the column back
 * @uses apply_filters() Calls 'bbp_get_topic_subscribers' with the subscribers
 * @return array|bool Results if the topic has any subscribers, otherwise false
 */
function bbp_get_topic_subscribers( $topic_id = 0 ) {
	$topic_id = bbp_get_topic_id( $topic_id );
	if ( empty( $topic_id ) )
		return;

	global $wpdb;

	$key   = $wpdb->prefix . '_bbp_subscriptions';
	$users = wp_cache_get( 'bbp_get_topic_subscribers_' . $topic_id, 'bbpress_users' );
	if ( false === $users ) {
		$users = $wpdb->get_col( "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = '{$key}' and FIND_IN_SET('{$topic_id}', meta_value) > 0" );
		wp_cache_set( 'bbp_get_topic_subscribers_' . $topic_id, $users, 'bbpress_users' );
	}

	return apply_filters( 'bbp_get_topic_subscribers', $users );
}

/**
 * Get a user's subscribed topics
 *
 * @since bbPress (r2668)
 *
 * @deprecated since bbPress (r5156)
 *
 * @param int $user_id Optional. User id
 * @uses bbp_get_user_topic_subscriptions() To get the user's subscriptions
 * @return array|bool Results if user has subscriptions, otherwise false
 */
function bbp_get_user_subscriptions( $user_id = 0 ) {
	_deprecated_function( __FUNCTION__, 2.5, 'bbp_get_user_topic_subscriptions()' );
	$query = bbp_get_user_topic_subscriptions( $user_id );
	return apply_filters( 'bbp_get_user_subscriptions', $query, $user_id );
}

/**
 * Get a user's subscribed topics
 *
 * @since bbPress (r2668)
 *
 * @param int $user_id Optional. User id
 * @uses bbp_get_user_subscribed_topic_ids() To get the user's subscriptions
 * @uses bbp_has_topics() To get the topics
 * @uses apply_filters() Calls 'bbp_get_user_subscriptions' with the topic query
 *                        and user id
 * @return array|bool Results if user has subscriptions, otherwise false
 */
function bbp_get_user_topic_subscriptions( $user_id = 0 ) {

	// Default to the displayed user
	$user_id = bbp_get_user_id( $user_id );
	if ( empty( $user_id ) ) {
		return false;
	}

	// If user has subscriptions, load them
	$subscriptions = bbp_get_user_subscribed_topic_ids( $user_id );
	if ( !empty( $subscriptions ) ) {
		$query = bbp_has_topics( array( 'post__in' => $subscriptions ) );
	} else {
		$query = false;
	}

	return apply_filters( 'bbp_get_user_topic_subscriptions', $query, $user_id );
}

/**
 * Get a user's subscribed forums
 *
 * @since bbPress (r5156)
 *
 * @param int $user_id Optional. User id
 * @uses bbp_get_user_subscribed_forum_ids() To get the user's subscriptions
 * @uses bbp_has_forums() To get the forums
 * @uses apply_filters() Calls 'bbp_get_user_forum_subscriptions' with the forum
 *                        query and user id
 * @return array|bool Results if user has subscriptions, otherwise false
 */
function bbp_get_user_forum_subscriptions( $user_id = 0 ) {

	// Default to the displayed user
	$user_id = bbp_get_user_id( $user_id );
	if ( empty( $user_id ) ) {
		return false;
	}

	// If user has subscriptions, load them
	$subscriptions = bbp_get_user_subscribed_forum_ids( $user_id );
	if ( !empty( $subscriptions ) ) {
		$query = bbp_has_forums( array( 'post__in' => $subscriptions ) );
	} else {
		$query = false;
	}

	return apply_filters( 'bbp_get_user_forum_subscriptions', $query, $user_id );
}

/**
 * Get a user's subscribed forum ids
 *
 * @since bbPress (r5156)
 *
 * @param int $user_id Optional. User id
 * @uses bbp_get_user_id() To get the user id
 * @uses get_user_option() To get the user's subscriptions
 * @uses apply_filters() Calls 'bbp_get_user_subscribed_forum_ids' with
 *                        the subscriptions and user id
 * @return array|bool Results if user has subscriptions, otherwise false
 */
function bbp_get_user_subscribed_forum_ids( $user_id = 0 ) {
	$user_id = bbp_get_user_id( $user_id );
	if ( empty( $user_id ) )
		return false;

	$subscriptions = get_user_option( '_bbp_forum_subscriptions', $user_id );
	$subscriptions = array_filter( wp_parse_id_list( $subscriptions ) );

	return (array) apply_filters( 'bbp_get_user_subscribed_forum_ids', $subscriptions, $user_id );
}

/**
 * Get a user's subscribed topics' ids
 *
 * @since bbPress (r2668)
 *
 * @param int $user_id Optional. User id
 * @uses bbp_get_user_id() To get the user id
 * @uses get_user_option() To get the user's subscriptions
 * @uses apply_filters() Calls 'bbp_get_user_subscribed_topic_ids' with
 *                        the subscriptions and user id
 * @return array|bool Results if user has subscriptions, otherwise false
 */
function bbp_get_user_subscribed_topic_ids( $user_id = 0 ) {
	$user_id = bbp_get_user_id( $user_id );
	if ( empty( $user_id ) )
		return false;

	$subscriptions = get_user_option( '_bbp_subscriptions', $user_id );
	$subscriptions = array_filter( wp_parse_id_list( $subscriptions ) );

	return (array) apply_filters( 'bbp_get_user_subscribed_topic_ids', $subscriptions, $user_id );
}

/**
 * Check if a topic or forum is in user's subscription list or not
 *
 * @since bbPress (r5156)
 *
 * @param int $user_id Optional. User id
 * @param int $forum_id Optional. Topic id
 * @uses get_post() To get the post object
 * @uses bbp_get_user_subscribed_forum_ids() To get the user's forum subscriptions
 * @uses bbp_get_user_subscribed_topic_ids() To get the user's topic subscriptions
 * @uses bbp_get_forum_post_type() To get the forum post type
 * @uses bbp_get_topic_post_type() To get the topic post type
 * @uses apply_filters() Calls 'bbp_is_user_subscribed' with the bool, user id,
 *                        forum/topic id and subsriptions
 * @return bool True if the forum or topic is in user's subscriptions, otherwise false
 */
function bbp_is_user_subscribed( $user_id = 0, $object_id = 0 ) {

	// Assume user is not subscribed
	$retval = false;

	// Setup ID's array
	$subscribed_ids = array();

	// User and object ID's are passed
	if ( ! empty( $user_id ) && ! empty( $object_id ) ) {

		// Get the post type
		$post_type = get_post_type( $object_id );

		// Post exists, so check the types
		if ( ! empty( $post_type ) ) {

			switch( $post_type ) {

				// Forum
				case bbp_get_forum_post_type() :
					$subscribed_ids = bbp_get_user_subscribed_forum_ids( $user_id );
					$retval         = bbp_is_user_subscribed_to_forum( $user_id, $object_id, $subscribed_ids );
					break;

				// Topic (default)
				case bbp_get_topic_post_type() :
				default :
					$subscribed_ids = bbp_get_user_subscribed_topic_ids( $user_id );
					$retval         = bbp_is_user_subscribed_to_topic( $user_id, $object_id, $subscribed_ids );
					break;
			}
		}
	}

	return (bool) apply_filters( 'bbp_is_user_subscribed', $retval, $user_id, $object_id, $subscribed_ids );
}

/**
 * Check if a forum is in user's subscription list or not
 *
 * @since bbPress (r5156)
 *
 * @param int $user_id Optional. User id
 * @param int $forum_id Optional. Topic id
 * @param array $subscribed_ids Optional. Array of forum ID's to check
 * @uses bbp_get_user_id() To get the user id
 * @uses bbp_get_user_subscribed_forum_ids() To get the user's subscriptions
 * @uses bbp_get_forum() To get the forum
 * @uses bbp_get_forum_id() To get the forum id
 * @uses apply_filters() Calls 'bbp_is_user_subscribed' with the bool, user id,
 *                        forum id and subsriptions
 * @return bool True if the forum is in user's subscriptions, otherwise false
 */
function bbp_is_user_subscribed_to_forum( $user_id = 0, $forum_id = 0, $subscribed_ids = array() ) {

	// Assume user is not subscribed
	$retval = false;

	// Validate user
	$user_id = bbp_get_user_id( $user_id, true, true );
	if ( ! empty( $user_id ) ) {

		// Get subscription ID's if none passed
		if ( empty( $subscribed_ids ) ) {
			$subscribed_ids = bbp_get_user_subscribed_forum_ids( $user_id );
		}

		// User has forum subscriptions
		if ( ! empty( $subscribed_ids ) ) {

			// Checking a specific forum id
			if ( ! empty( $forum_id ) ) {
				$forum    = bbp_get_forum( $forum_id );
				$forum_id = ! empty( $forum ) ? $forum->ID : 0;

			// Using the global forum id
			} elseif ( bbp_get_forum_id() ) {
				$forum_id = bbp_get_forum_id();

			// Use the current post id
			} elseif ( ! bbp_get_forum_id() ) {
				$forum_id = get_the_ID();
			}

			// Is forum_id in the user's favorites
			if ( ! empty( $forum_id ) ) {
				$retval = in_array( $forum_id, $subscribed_ids );
			}
		}
	}

	return (bool) apply_filters( 'bbp_is_user_subscribed_to_forum', (bool) $retval, $user_id, $forum_id, $subscribed_ids );
}

/**
 * Check if a topic is in user's subscription list or not
 *
 * @since bbPress (r5156)
 *
 * @param int $user_id Optional. User id
 * @param int $topic_id Optional. Topic id
 * @param array $subscribed_ids Optional. Array of topic ID's to check
 * @uses bbp_get_user_id() To get the user id
 * @uses bbp_get_user_subscribed_topic_ids() To get the user's subscriptions
 * @uses bbp_get_topic() To get the topic
 * @uses bbp_get_topic_id() To get the topic id
 * @uses apply_filters() Calls 'bbp_is_user_subscribed' with the bool, user id,
 *                        topic id and subsriptions
 * @return bool True if the topic is in user's subscriptions, otherwise false
 */
function bbp_is_user_subscribed_to_topic( $user_id = 0, $topic_id = 0, $subscribed_ids = array() ) {

	// Assume user is not subscribed
	$retval = false;

	// Validate user
	$user_id = bbp_get_user_id( $user_id, true, true );
	if ( !empty( $user_id ) ) {

		// Get subscription ID's if none passed
		if ( empty( $subscribed_ids ) ) {
			$subscribed_ids = bbp_get_user_subscribed_topic_ids( $user_id );
		}

		// User has topic subscriptions
		if ( ! empty( $subscribed_ids ) ) {

			// Checking a specific topic id
			if ( ! empty( $topic_id ) ) {
				$topic    = bbp_get_topic( $topic_id );
				$topic_id = ! empty( $topic ) ? $topic->ID : 0;

			// Using the global topic id
			} elseif ( bbp_get_topic_id() ) {
				$topic_id = bbp_get_topic_id();

			// Use the current post id
			} elseif ( !bbp_get_topic_id() ) {
				$topic_id = get_the_ID();
			}

			// Is topic_id in the user's favorites
			if ( ! empty( $topic_id ) ) {
				$retval = in_array( $topic_id, $subscribed_ids );
			}
		}
	}

	return (bool) apply_filters( 'bbp_is_user_subscribed_to_topic', (bool) $retval, $user_id, $topic_id, $subscribed_ids );
}

/**
 * Add a topic to user's subscriptions
 *
 * @since bbPress (r5156)
 *
 * @param int $user_id Optional. User id
 * @param int $topic_id Optional. Topic id
 * @uses get_post() To get the post object
 * @uses bbp_get_user_subscribed_forum_ids() To get the user's forum subscriptions
 * @uses bbp_get_user_subscribed_topic_ids() To get the user's topic subscriptions
 * @uses bbp_get_forum_post_type() To get the forum post type
 * @uses bbp_get_topic_post_type() To get the topic post type
 * @uses update_user_option() To update the user's subscriptions
 * @uses do_action() Calls 'bbp_add_user_subscription' with the user & topic id
 * @return bool Always true
 */
function bbp_add_user_subscription( $user_id = 0, $object_id = 0 ) {
	if ( empty( $user_id ) || empty( $object_id ) ) {
		return false;
	}

	// Get the post type
	$post_type = get_post_type( $object_id );
	if ( empty( $post_type ) ) {
		return false;
	}

	switch( $post_type ) {

		// Forum
		case bbp_get_forum_post_type() :
			bbp_add_user_forum_subscription( $user_id, $object_id );
			break;

		// Topic
		case bbp_get_topic_post_type() :
		default :
			bbp_add_user_topic_subscription( $user_id, $object_id );
			break;
	}

	do_action( 'bbp_add_user_subscription', $user_id, $object_id, $post_type );

	return true;
}

/**
 * Add a forum to user's subscriptions
 *
 * @since bbPress (r5156)
 *
 * @param int $user_id Optional. User id
 * @param int $forum_id Optional. forum id
 * @uses bbp_get_user_subscribed_forum_ids() To get the user's subscriptions
 * @uses bbp_get_forum() To get the forum
 * @uses update_user_option() To update the user's subscriptions
 * @uses do_action() Calls 'bbp_add_user_subscription' with the user & forum id
 * @return bool Always true
 */
function bbp_add_user_forum_subscription( $user_id = 0, $forum_id = 0 ) {
	if ( empty( $user_id ) || empty( $forum_id ) ) {
		return false;
	}

	$forum = bbp_get_forum( $forum_id );
	if ( empty( $forum ) ) {
		return false;
	}

	$subscriptions = (array) bbp_get_user_subscribed_forum_ids( $user_id );
	if ( !in_array( $forum_id, $subscriptions ) ) {
		$subscriptions[] = $forum_id;
		$subscriptions   = implode( ',', wp_parse_id_list( array_filter( $subscriptions ) ) );
		update_user_option( $user_id, '_bbp_forum_subscriptions', $subscriptions );

		wp_cache_delete( 'bbp_get_forum_subscribers_' . $forum_id, 'bbpress_users' );
	}

	do_action( 'bbp_add_user_forum_subscription', $user_id, $forum_id );

	return true;
}

/**
 * Add a topic to user's subscriptions
 *
 * @since bbPress (r2668)
 *
 * @param int $user_id Optional. User id
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_user_subscribed_topic_ids() To get the user's subscriptions
 * @uses bbp_get_topic() To get the topic
 * @uses update_user_option() To update the user's subscriptions
 * @uses do_action() Calls 'bbp_add_user_subscription' with the user & topic id
 * @return bool Always true
 */
function bbp_add_user_topic_subscription( $user_id = 0, $topic_id = 0 ) {
	if ( empty( $user_id ) || empty( $topic_id ) ) {
		return false;
	}

	$topic = bbp_get_topic( $topic_id );
	if ( empty( $topic ) ) {
		return false;
	}

	$subscriptions = (array) bbp_get_user_subscribed_topic_ids( $user_id );
	if ( !in_array( $topic_id, $subscriptions ) ) {
		$subscriptions[] = $topic_id;
		$subscriptions   = implode( ',', wp_parse_id_list( array_filter( $subscriptions ) ) );
		update_user_option( $user_id, '_bbp_subscriptions', $subscriptions );

		wp_cache_delete( 'bbp_get_topic_subscribers_' . $topic_id, 'bbpress_users' );
	}

	do_action( 'bbp_add_user_topic_subscription', $user_id, $topic_id );

	return true;
}

/**
 * Remove a topic from user's subscriptions
 *
 * @since bbPress (r2668)
 *
 * @param int $user_id Optional. User id
 * @param int $topic_id Optional. Topic id
 * @uses get_post() To get the post object
 * @uses bbp_get_forum_post_type() To get the forum post type
 * @uses bbp_get_topic_post_type() To get the topic post type
 * @uses bbp_remove_user_forum_subscription() To remove the user's subscription
 * @uses bbp_remove_user_topic_subscription() To remove the user's subscription
 * @uses do_action() Calls 'bbp_remove_user_subscription' with the user id and
 *                    topic id
 * @return bool True if the topic was removed from user's subscriptions,
 *               otherwise false
 */
function bbp_remove_user_subscription( $user_id = 0, $object_id = 0 ) {
	if ( empty( $user_id ) || empty( $object_id ) ) {
		return false;
	}

	$post_type = get_post_type( $object_id );
	if ( empty( $post_type ) ) {
		return false;
	}

	switch( $post_type ) {

		// Forum
		case bbp_get_forum_post_type() :
			bbp_remove_user_forum_subscription( $user_id, $object_id );
			break;

		// Topic
		case bbp_get_topic_post_type() :
		default :
			bbp_remove_user_topic_subscription( $user_id, $object_id );
			break;
	}

	do_action( 'bbp_remove_user_subscription', $user_id, $object_id, $post_type );

	return true;
}

/**
 * Remove a forum from user's subscriptions
 *
 * @since bbPress (r5156)
 *
 * @param int $user_id Optional. User id
 * @param int $forum_id Optional. forum id
 * @uses bbp_get_user_subscribed_forum_ids() To get the user's subscriptions
 * @uses update_user_option() To update the user's subscriptions
 * @uses delete_user_option() To delete the user's subscriptions meta
 * @uses do_action() Calls 'bbp_remove_user_subscription' with the user id and
 *                    forum id
 * @return bool True if the forum was removed from user's subscriptions,
 *               otherwise false
 */
function bbp_remove_user_forum_subscription( $user_id, $forum_id ) {
	if ( empty( $user_id ) || empty( $forum_id ) ) {
		return false;
	}

	$subscriptions = (array) bbp_get_user_subscribed_forum_ids( $user_id );
	if ( empty( $subscriptions ) ) {
		return false;
	}

	$pos = array_search( $forum_id, $subscriptions );
	if ( false === $pos ) {
		return false;
	}

	array_splice( $subscriptions, $pos, 1 );
	$subscriptions = array_filter( $subscriptions );

	if ( !empty( $subscriptions ) ) {
		$subscriptions = implode( ',', wp_parse_id_list( $subscriptions ) );
		update_user_option( $user_id, '_bbp_forum_subscriptions', $subscriptions );
	} else {
		delete_user_option( $user_id, '_bbp_forum_subscriptions' );
	}

	wp_cache_delete( 'bbp_get_forum_subscribers_' . $forum_id, 'bbpress_users' );

	do_action( 'bbp_remove_user_forum_subscription', $user_id, $forum_id );

	return true;
}

/**
 * Remove a topic from user's subscriptions
 *
 * @since bbPress (r5156)
 *
 * @param int $user_id Optional. User id
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_user_subscribed_topic_ids() To get the user's subscriptions
 * @uses update_user_option() To update the user's subscriptions
 * @uses delete_user_option() To delete the user's subscriptions meta
 * @uses do_action() Calls 'bbp_remove_user_topic_subscription' with the user id and
 *                    topic id
 * @return bool True if the topic was removed from user's subscriptions,
 *               otherwise false
 */
function bbp_remove_user_topic_subscription( $user_id, $topic_id ) {
	if ( empty( $user_id ) || empty( $topic_id ) ) {
		return false;
	}

	$subscriptions = (array) bbp_get_user_subscribed_topic_ids( $user_id );
	if ( empty( $subscriptions ) ) {
		return false;
	}

	$pos = array_search( $topic_id, $subscriptions );
	if ( false === $pos ) {
		return false;
	}

	array_splice( $subscriptions, $pos, 1 );
	$subscriptions = array_filter( $subscriptions );

	if ( !empty( $subscriptions ) ) {
		$subscriptions = implode( ',', wp_parse_id_list( $subscriptions ) );
		update_user_option( $user_id, '_bbp_subscriptions', $subscriptions );
	} else {
		delete_user_option( $user_id, '_bbp_subscriptions' );
	}

	wp_cache_delete( 'bbp_get_topic_subscribers_' . $topic_id, 'bbpress_users' );

	do_action( 'bbp_remove_user_topic_subscription', $user_id, $topic_id );

	return true;
}

/**
 * Handles the front end subscribing and unsubscribing forums
 *
 * @since bbPress (r5156)
 *
 * @param string $action The requested action to compare this function to
 * @uses bbp_is_subscriptions_active() To check if the subscriptions are active
 * @uses bbp_get_user_id() To get the user id
 * @uses bbp_verify_nonce_request() To verify the nonce and check the request
 * @uses current_user_can() To check if the current user can edit the user
 * @uses bbPress:errors:add() To log the error messages
 * @uses bbp_is_user_subscribed() To check if the forum is in user's
 *                                 subscriptions
 * @uses bbp_remove_user_subscription() To remove the user subscription
 * @uses bbp_add_user_subscription() To add the user subscription
 * @uses do_action() Calls 'bbp_subscriptions_handler' with success, user id,
 *                    forum id and action
 * @uses bbp_is_subscription() To check if it's the subscription page
 * @uses bbp_get_forum_permalink() To get the forum permalink
 * @uses wp_safe_redirect() To redirect to the url
 */
function bbp_forum_subscriptions_handler( $action = '' ) {

	if ( ! bbp_is_subscriptions_active() ) {
		return false;
	}

	// Bail if no forum ID is passed
	if ( empty( $_GET['forum_id'] ) ) {
		return;
	}

	// Setup possible get actions
	$possible_actions = array(
		'bbp_subscribe',
		'bbp_unsubscribe',
	);

	// Bail if actions aren't meant for this function
	if ( ! in_array( $action, $possible_actions ) ) {
		return;
	}

	// Get required data
	$user_id  = bbp_get_user_id( 0, true, true );
	$forum_id = intval( $_GET['forum_id'] );

	// Check for empty forum
	if ( empty( $forum_id ) ) {
		bbp_add_error( 'bbp_subscription_forum_id', __( '<strong>ERROR</strong>: No forum was found! Which forum are you subscribing/unsubscribing to?', 'bbpress' ) );

	// Check nonce
	} elseif ( ! bbp_verify_nonce_request( 'toggle-subscription_' . $forum_id ) ) {
		bbp_add_error( 'bbp_subscription_forum_id', __( '<strong>ERROR</strong>: Are you sure you wanted to do that?', 'bbpress' ) );

	// Check current user's ability to edit the user
	} elseif ( !current_user_can( 'edit_user', $user_id ) ) {
		bbp_add_error( 'bbp_subscription_permissions', __( '<strong>ERROR</strong>: You don\'t have the permission to edit favorites of that user!', 'bbpress' ) );
	}

	// Bail if we have errors
	if ( bbp_has_errors() ) {
		return;
	}

	/** No errors *************************************************************/

	$is_subscription = bbp_is_user_subscribed( $user_id, $forum_id );
	$success         = false;

	if ( true === $is_subscription && 'bbp_unsubscribe' === $action ) {
		$success = bbp_remove_user_subscription( $user_id, $forum_id );
	} elseif ( false === $is_subscription && 'bbp_subscribe' === $action ) {
		$success = bbp_add_user_subscription( $user_id, $forum_id );
	}

	// Do additional subscriptions actions
	do_action( 'bbp_subscriptions_handler', $success, $user_id, $forum_id, $action );

	// Success!
	if ( true === $success ) {

		// Redirect back from whence we came
		if ( bbp_is_subscriptions() ) {
			$redirect = bbp_get_subscriptions_permalink( $user_id );
		} elseif ( bbp_is_single_user() ) {
			$redirect = bbp_get_user_profile_url();
		} elseif ( is_singular( bbp_get_forum_post_type() ) ) {
			$redirect = bbp_get_forum_permalink( $forum_id );
		} elseif ( is_single() || is_page() ) {
			$redirect = get_permalink();
		} else {
			$redirect = get_permalink( $forum_id );
		}

		wp_safe_redirect( $redirect );

		// For good measure
		exit();

	// Fail! Handle errors
	} elseif ( true === $is_subscription && 'bbp_unsubscribe' === $action ) {
		bbp_add_error( 'bbp_unsubscribe', __( '<strong>ERROR</strong>: There was a problem unsubscribing from that forum!', 'bbpress' ) );
	} elseif ( false === $is_subscription && 'bbp_subscribe' === $action ) {
		bbp_add_error( 'bbp_subscribe',    __( '<strong>ERROR</strong>: There was a problem subscribing to that forum!', 'bbpress' ) );
	}
}

/**
 * Handles the front end subscribing and unsubscribing topics
 *
 * @param string $action The requested action to compare this function to
 * @uses bbp_is_subscriptions_active() To check if the subscriptions are active
 * @uses bbp_get_user_id() To get the user id
 * @uses bbp_verify_nonce_request() To verify the nonce and check the request
 * @uses current_user_can() To check if the current user can edit the user
 * @uses bbPress:errors:add() To log the error messages
 * @uses bbp_is_user_subscribed() To check if the topic is in user's
 *                                 subscriptions
 * @uses bbp_remove_user_subscription() To remove the user subscription
 * @uses bbp_add_user_subscription() To add the user subscription
 * @uses do_action() Calls 'bbp_subscriptions_handler' with success, user id,
 *                    topic id and action
 * @uses bbp_is_subscription() To check if it's the subscription page
 * @uses bbp_get_topic_permalink() To get the topic permalink
 * @uses wp_safe_redirect() To redirect to the url
 */
function bbp_subscriptions_handler( $action = '' ) {

	if ( !bbp_is_subscriptions_active() ) {
		return false;
	}

	// Bail if no topic ID is passed
	if ( empty( $_GET['topic_id'] ) ) {
		return;
	}

	// Setup possible get actions
	$possible_actions = array(
		'bbp_subscribe',
		'bbp_unsubscribe',
	);

	// Bail if actions aren't meant for this function
	if ( !in_array( $action, $possible_actions ) ) {
		return;
	}

	// Get required data
	$user_id  = bbp_get_user_id( 0, true, true );
	$topic_id = intval( $_GET['topic_id'] );

	// Check for empty topic
	if ( empty( $topic_id ) ) {
		bbp_add_error( 'bbp_subscription_topic_id', __( '<strong>ERROR</strong>: No topic was found! Which topic are you subscribing/unsubscribing to?', 'bbpress' ) );

	// Check nonce
	} elseif ( ! bbp_verify_nonce_request( 'toggle-subscription_' . $topic_id ) ) {
		bbp_add_error( 'bbp_subscription_topic_id', __( '<strong>ERROR</strong>: Are you sure you wanted to do that?', 'bbpress' ) );

	// Check current user's ability to edit the user
	} elseif ( !current_user_can( 'edit_user', $user_id ) ) {
		bbp_add_error( 'bbp_subscription_permissions', __( '<strong>ERROR</strong>: You don\'t have the permission to edit favorites of that user!', 'bbpress' ) );
	}

	// Bail if we have errors
	if ( bbp_has_errors() ) {
		return;
	}

	/** No errors *************************************************************/

	$is_subscription = bbp_is_user_subscribed( $user_id, $topic_id );
	$success         = false;

	if ( true === $is_subscription && 'bbp_unsubscribe' === $action ) {
		$success = bbp_remove_user_subscription( $user_id, $topic_id );
	} elseif ( false === $is_subscription && 'bbp_subscribe' === $action ) {
		$success = bbp_add_user_subscription( $user_id, $topic_id );
	}

	// Do additional subscriptions actions
	do_action( 'bbp_subscriptions_handler', $success, $user_id, $topic_id, $action );

	// Success!
	if ( true === $success ) {

		// Redirect back from whence we came
		if ( bbp_is_subscriptions() ) {
			$redirect = bbp_get_subscriptions_permalink( $user_id );
		} elseif ( bbp_is_single_user() ) {
			$redirect = bbp_get_user_profile_url();
		} elseif ( is_singular( bbp_get_topic_post_type() ) ) {
			$redirect = bbp_get_topic_permalink( $topic_id );
		} elseif ( is_single() || is_page() ) {
			$redirect = get_permalink();
		} else {
			$redirect = get_permalink( $topic_id );
		}

		wp_safe_redirect( $redirect );

		// For good measure
		exit();

	// Fail! Handle errors
	} elseif ( true === $is_subscription && 'bbp_unsubscribe' === $action ) {
		bbp_add_error( 'bbp_unsubscribe', __( '<strong>ERROR</strong>: There was a problem unsubscribing from that topic!', 'bbpress' ) );
	} elseif ( false === $is_subscription && 'bbp_subscribe' === $action ) {
		bbp_add_error( 'bbp_subscribe',    __( '<strong>ERROR</strong>: There was a problem subscribing to that topic!', 'bbpress' ) );
	}
}

/** Edit **********************************************************************/

/**
 * Handles the front end user editing
 *
 * @param string $action The requested action to compare this function to
 * @uses is_multisite() To check if it's a multisite
 * @uses bbp_is_user_home() To check if the user is at home (the display page
 *                           is the one of the logged in user)
 * @uses get_option() To get the displayed user's new email id option
 * @uses wpdb::prepare() To sanitize our sql query
 * @uses wpdb::get_var() To execute our query and get back the variable
 * @uses wpdb::query() To execute our query
 * @uses wp_update_user() To update the user
 * @uses delete_option() To delete the displayed user's email id option
 * @uses bbp_get_user_profile_edit_url() To get the edit profile url
 * @uses wp_safe_redirect() To redirect to the url
 * @uses bbp_verify_nonce_request() To verify the nonce and check the request
 * @uses current_user_can() To check if the current user can edit the user
 * @uses do_action() Calls 'personal_options_update' or
 *                   'edit_user_options_update' (based on if it's the user home)
 *                   with the displayed user id
 * @uses edit_user() To edit the user based on the post data
 * @uses get_userdata() To get the user data
 * @uses is_email() To check if the string is an email id or not
 * @uses wpdb::get_blog_prefix() To get the blog prefix
 * @uses is_network_admin() To check if the user is the network admin
 * @uses revoke_super_admin() To revoke super admin priviledges
 * @uses grant_super_admin() To grant super admin priviledges
 * @uses is_wp_error() To check if the value retrieved is a {@link WP_Error}
 */
function bbp_edit_user_handler( $action = '' ) {

	// Bail if action is not 'bbp-update-user'
	if ( 'bbp-update-user' !== $action )
		return;

	// Get the displayed user ID
	$user_id = bbp_get_displayed_user_id();

	// Execute confirmed email change. See send_confirmation_on_profile_email().
	if ( is_multisite() && bbp_is_user_home_edit() && isset( $_GET['newuseremail'] ) ) {

		$new_email = get_option( $user_id . '_new_email' );

		if ( $new_email['hash'] === $_GET['newuseremail'] ) {
			$user             = new WP_User();
			$user->ID         = $user_id;
			$user->user_email = esc_html( trim( $new_email['newemail'] ) );

			global $wpdb;

			if ( $wpdb->get_var( $wpdb->prepare( "SELECT user_login FROM {$wpdb->signups} WHERE user_login = %s", bbp_get_displayed_user_field( 'user_login', 'raw' ) ) ) ) {
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->signups} SET user_email = %s WHERE user_login = %s", $user->user_email, bbp_get_displayed_user_field( 'user_login', 'raw' ) ) );
			}

			wp_update_user( get_object_vars( $user ) );
			delete_option( $user_id . '_new_email' );

			wp_safe_redirect( add_query_arg( array( 'updated' => 'true' ), bbp_get_user_profile_edit_url( $user_id ) ) );
			exit();
		}

	// Delete new email address from user options
	} elseif ( is_multisite() && bbp_is_user_home_edit() && !empty( $_GET['dismiss'] ) && ( $user_id . '_new_email' === $_GET['dismiss'] ) ) {
		delete_option( $user_id . '_new_email' );
		wp_safe_redirect( add_query_arg( array( 'updated' => 'true' ), bbp_get_user_profile_edit_url( $user_id ) ) );
		exit();
	}

	// Nonce check
	if ( ! bbp_verify_nonce_request( 'update-user_' . $user_id ) ) {
		bbp_add_error( 'bbp_update_user_nonce', __( '<strong>ERROR</strong>: Are you sure you wanted to do that?', 'bbpress' ) );
		return;
	}

	// Cap check
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		bbp_add_error( 'bbp_update_user_capability', __( '<strong>ERROR</strong>: Are you sure you wanted to do that?', 'bbpress' ) );
		return;
	}

	// Do action based on who's profile you're editing
	$edit_action = bbp_is_user_home_edit() ? 'personal_options_update' : 'edit_user_profile_update';
	do_action( $edit_action, $user_id );

	// Prevent edit_user() from wiping out the user's Toolbar on front setting
	if ( !isset( $_POST['admin_bar_front'] ) && _get_admin_bar_pref( 'front', $user_id ) ) {
		$_POST['admin_bar_front'] = 1;
	}

	// Handle user edit
	$edit_user = edit_user( $user_id );

	// Error(s) editng the user, so copy them into the global
	if ( is_wp_error( $edit_user ) ) {
		bbpress()->errors = $edit_user;

	// Successful edit to redirect
	} elseif ( is_integer( $edit_user ) ) {

		// Maybe update super admin ability
		if ( is_multisite() && ! bbp_is_user_home_edit() ) {
			empty( $_POST['super_admin'] ) ? revoke_super_admin( $edit_user ) : grant_super_admin( $edit_user );
		}

		$redirect = add_query_arg( array( 'updated' => 'true' ), bbp_get_user_profile_edit_url( $edit_user ) );

		wp_safe_redirect( $redirect );
		exit;
	}
}

/**
 * Conditionally hook the core WordPress output actions to the end of the
 * default user's edit profile template.
 *
 * This allows clever plugin authors to conditionally unhook the WordPress core
 * output actions if they don't want any unexpected junk to appear there, and
 * also avoids needing to pollute the templates with additional logic and actions.
 *
 * @since bbPress (r4273)
 *
 * @uses bbp_is_user_home_edit() To switch the action fired
 * @uses get_userdata() To get the current user's data
 * @uses bbp_get_displayed_user_id() To get the currently displayed user ID
 */
function bbp_user_edit_after() {
	$action = bbp_is_user_home_edit() ? 'show_user_profile' : 'edit_user_profile';

	do_action( $action, get_userdata( bbp_get_displayed_user_id() ) );
}

/** User Queries **************************************************************/

/**
 * Get the topics that a user created
 *
 * @since bbPress (r2660)
 *
 * @param int $user_id Optional. User id
 * @uses bbp_get_user_id() To get the topic id
 * @uses bbp_has_topics() To get the topics created by the user
 * @return array|bool Results if the user has created topics, otherwise false
 */
function bbp_get_user_topics_started( $user_id = 0 ) {

	// Validate user
	$user_id = bbp_get_user_id( $user_id );
	if ( empty( $user_id ) )
		return false;

	// Try to get the topics
	$query = bbp_has_topics( array(
		'author' => $user_id
	) );

	return apply_filters( 'bbp_get_user_topics_started', $query, $user_id );
}

/**
 * Get the replies that a user created
 *
 * @since bbPress (r4225)
 *
 * @param int $user_id Optional. User id
 * @uses bbp_get_user_id() To get the topic id
 * @uses bbp_has_replies() To get the topics created by the user
 * @return array|bool Results if the user has created topics, otherwise false
 */
function bbp_get_user_replies_created( $user_id = 0 ) {

	// Validate user
	$user_id = bbp_get_user_id( $user_id );
	if ( empty( $user_id ) )
		return false;

	// Try to get the topics
	$query = bbp_has_replies( array(
		'post_type' => bbp_get_reply_post_type(),
		'order'     => 'DESC',
		'author'    => $user_id
	) );

	return apply_filters( 'bbp_get_user_replies_created', $query, $user_id );
}

/**
 * Get the total number of users on the forums
 *
 * @since bbPress (r2769)
 * @uses count_users() To execute our query and get the var back
 * @uses apply_filters() Calls 'bbp_get_total_users' with number of users
 * @return int Total number of users
 */
function bbp_get_total_users() {
	$user_count = count_users();
	return apply_filters( 'bbp_get_total_users', (int) $user_count['total_users'] );
}

/** Premissions ***************************************************************/

/**
 * Redirect if unathorized user is attempting to edit another user
 *
 * This is hooked to 'bbp_template_redirect' and controls the conditions under
 * which a user can edit another user (or themselves.) If these conditions are
 * met. We assume a user cannot perform this task, and look for ways they can
 * earn the ability to access this template.
 *
 * @since bbPress (r3605)
 *
 * @uses bbp_is_topic_edit()
 * @uses current_user_can()
 * @uses bbp_get_topic_id()
 * @uses wp_safe_redirect()
 * @uses bbp_get_topic_permalink()
 */
function bbp_check_user_edit() {

	// Bail if not editing a topic
	if ( ! bbp_is_single_user_edit() )
		return;

	// Default to false
	$redirect = true;

	// Allow user to edit their own profile
	if ( bbp_is_user_home_edit() ) {
		$redirect = false;

	// Allow if current user can edit the displayed user
	} elseif ( current_user_can( 'edit_user', bbp_get_displayed_user_id() ) ) {
		$redirect = false;

	// Allow if user can manage network users, or edit-any is enabled
	} elseif ( current_user_can( 'manage_network_users' ) || apply_filters( 'enable_edit_any_user_configuration', false ) ) {
		$redirect = false;
	}

	// Maybe redirect back to profile page
	if ( true === $redirect ) {
		wp_safe_redirect( bbp_get_user_profile_url( bbp_get_displayed_user_id() ) );
		exit();
	}
}

/**
 * Check if a user is blocked, or cannot spectate the forums.
 *
 * @since bbPress (r2996)
 *
 * @uses is_user_logged_in() To check if user is logged in
 * @uses bbp_is_user_keymaster() To check if user is a keymaster
 * @uses current_user_can() To check if the current user can spectate
 * @uses is_bbpress() To check if in a bbPress section of the site
 * @uses bbp_set_404() To set a 404 status
 */
function bbp_forum_enforce_blocked() {

	// Bail if not logged in or keymaster
	if ( ! is_user_logged_in() || bbp_is_user_keymaster() ) {
		return;
	}

	// Set 404 if in bbPress and user cannot spectate
	if ( is_bbpress() && ! current_user_can( 'spectate' ) ) {
		bbp_set_404();
	}
}

/** Sanitization **************************************************************/

/**
 * Sanitize displayed user data, when viewing and editing any user.
 *
 * This somewhat monolithic function handles the escaping and sanitization of
 * user data for a bbPress profile. There are two reasons this all happers here:
 *
 * 1. bbPress took a similar approach to WordPress, and funnels all user profile
 *    data through a central helper. This eventually calls sanitize_user_field()
 *    which applies a few context based filters, which some third party plugins
 *    might be relying on bbPress to play nicely with.
 *
 * 2. Early versions of bbPress 2.x templates did not escape this data meaning
 *    a backwards compatible approach like this one was necessary to protect
 *    existing installations that may have custom template parts.
 *
 * @since bbPress (r5368)
 *
 * @param string $value
 * @param string $field
 * @param string $context
 * @return string
 */
function bbp_sanitize_displayed_user_field( $value = '', $field = '', $context = 'display' ) {

	// Bail if not editing or displaying (maybe we'll do more here later)
	if ( ! in_array( $context, array( 'edit', 'display' ) ) ) {
		return $value;
	}

	// By default, no filter set (consider making this an array later)
	$filter = false;

	// Big switch statement to decide which user field we're sanitizing and how
	switch ( $field ) {

		// Description is a paragraph
		case 'description' :
			$filter = ( 'edit' === $context ) ? '' : 'wp_kses_data';
			break;

		// Email addresses are sanitized with a specific function
		case 'user_email'  :
			$filter = 'sanitize_email';
			break;

		// Name & login fields
		case 'user_login'   :
		case 'display_name' :
		case 'first_name'   :
		case 'last_name'    :
		case 'nick_name'    :
			$filter = ( 'edit' === $context ) ? 'esc_attr' : 'esc_html';
			break;

		// wp-includes/default-filters.php escapes this for us via esc_url()
		case 'user_url' :
			break;
	}

	// Run any applicable filters on the value
	if ( ! empty( $filter ) ) {
		$value = call_user_func( $filter, $value );
	}

	return $value;
}

/** Converter *****************************************************************/

/**
 * Convert passwords from previous platfrom encryption to WordPress encryption.
 *
 * @since bbPress (r3813)
 * @global WPDB $wpdb
 */
function bbp_user_maybe_convert_pass() {

	// Bail if no username
	$username = !empty( $_POST['log'] ) ? $_POST['log'] : '';
	if ( empty( $username ) )
		return;

	global $wpdb;

	// Bail if no user password to convert
	$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->users} INNER JOIN {$wpdb->usermeta} ON user_id = ID WHERE meta_key = '_bbp_class' AND user_login = '%s' LIMIT 1", $username ) );
	if ( empty( $row ) || is_wp_error( $row ) )
		return;

	// Setup admin (to include converter)
	require_once( bbpress()->includes_dir . 'admin/admin.php' );

	// Create the admin object
	bbp_admin();

	// Convert password
	require_once( bbpress()->admin->admin_dir . 'converter.php' );
	require_once( bbpress()->admin->admin_dir . 'converters/' . $row->meta_value . '.php' );

	// Create the converter
	$converter = bbp_new_converter( $row->meta_value );

	// Try to call the conversion method
	if ( is_a( $converter, 'BBP_Converter_Base' ) && method_exists( $converter, 'callback_pass' ) ) {
		$converter->callback_pass( $username, $_POST['pwd'] );
	}
}
