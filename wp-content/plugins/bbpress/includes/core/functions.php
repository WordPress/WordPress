<?php

/**
 * bbPress Core Functions
 *
 * @package bbPress
 * @subpackage Functions
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/** Versions ******************************************************************/

/**
 * Output the bbPress version
 *
 * @since bbPress (r3468)
 * @uses bbp_get_version() To get the bbPress version
 */
function bbp_version() {
	echo bbp_get_version();
}
	/**
	 * Return the bbPress version
	 *
	 * @since bbPress (r3468)
	 * @retrun string The bbPress version
	 */
	function bbp_get_version() {
		return bbpress()->version;
	}

/**
 * Output the bbPress database version
 *
 * @since bbPress (r3468)
 * @uses bbp_get_version() To get the bbPress version
 */
function bbp_db_version() {
	echo bbp_get_db_version();
}
	/**
	 * Return the bbPress database version
	 *
	 * @since bbPress (r3468)
	 * @retrun string The bbPress version
	 */
	function bbp_get_db_version() {
		return bbpress()->db_version;
	}

/**
 * Output the bbPress database version directly from the database
 *
 * @since bbPress (r3468)
 * @uses bbp_get_version() To get the current bbPress version
 */
function bbp_db_version_raw() {
	echo bbp_get_db_version_raw();
}
	/**
	 * Return the bbPress database version directly from the database
	 *
	 * @since bbPress (r3468)
	 * @retrun string The current bbPress version
	 */
	function bbp_get_db_version_raw() {
		return get_option( '_bbp_db_version', '' );
	}

/** Post Meta *****************************************************************/

/**
 * Update a posts forum meta ID
 *
 * @since bbPress (r3181)
 *
 * @param int $post_id The post to update
 * @param int $forum_id The forum
 */
function bbp_update_forum_id( $post_id, $forum_id ) {

	// Allow the forum ID to be updated 'just in time' before save
	$forum_id = apply_filters( 'bbp_update_forum_id', $forum_id, $post_id );

	// Update the post meta forum ID
	update_post_meta( $post_id, '_bbp_forum_id', (int) $forum_id );
}

/**
 * Update a posts topic meta ID
 *
 * @since bbPress (r3181)
 *
 * @param int $post_id The post to update
 * @param int $forum_id The forum
 */
function bbp_update_topic_id( $post_id, $topic_id ) {

	// Allow the topic ID to be updated 'just in time' before save
	$topic_id = apply_filters( 'bbp_update_topic_id', $topic_id, $post_id );

	// Update the post meta topic ID
	update_post_meta( $post_id, '_bbp_topic_id', (int) $topic_id );
}

/**
 * Update a posts reply meta ID
 *
 * @since bbPress (r3181)
 *
 * @param int $post_id The post to update
 * @param int $forum_id The forum
 */
function bbp_update_reply_id( $post_id, $reply_id ) {

	// Allow the reply ID to be updated 'just in time' before save
	$reply_id = apply_filters( 'bbp_update_reply_id', $reply_id, $post_id );

	// Update the post meta reply ID
	update_post_meta( $post_id, '_bbp_reply_id',(int) $reply_id );
}

/** Views *********************************************************************/

/**
 * Get the registered views
 *
 * Does nothing much other than return the {@link $bbp->views} variable
 *
 * @since bbPress (r2789)
 *
 * @return array Views
 */
function bbp_get_views() {
	return bbpress()->views;
}

/**
 * Register a bbPress view
 *
 * @todo Implement feeds - See {@link http://trac.bbpress.org/ticket/1422}
 *
 * @since bbPress (r2789)
 *
 * @param string $view View name
 * @param string $title View title
 * @param mixed $query_args {@link bbp_has_topics()} arguments.
 * @param bool $feed Have a feed for the view? Defaults to true. NOT IMPLEMENTED
 * @param string $capability Capability that the current user must have
 * @uses sanitize_title() To sanitize the view name
 * @uses esc_html() To sanitize the view title
 * @return array The just registered (but processed) view
 */
function bbp_register_view( $view, $title, $query_args = '', $feed = true, $capability = '' ) {

	// Bail if user does not have capability
	if ( ! empty( $capability ) && ! current_user_can( $capability ) )
		return false;

	$bbp   = bbpress();
	$view  = sanitize_title( $view );
	$title = esc_html( $title );

	if ( empty( $view ) || empty( $title ) )
		return false;

	$query_args = bbp_parse_args( $query_args, '', 'register_view' );

	// Set show_stickies to false if it wasn't supplied
	if ( !isset( $query_args['show_stickies'] ) )
		$query_args['show_stickies'] = false;

	$bbp->views[$view] = array(
		'title'  => $title,
		'query'  => $query_args,
		'feed'   => $feed
	);

	return $bbp->views[$view];
}

/**
 * Deregister a bbPress view
 *
 * @since bbPress (r2789)
 *
 * @param string $view View name
 * @uses sanitize_title() To sanitize the view name
 * @return bool False if the view doesn't exist, true on success
 */
function bbp_deregister_view( $view ) {
	$bbp  = bbpress();
	$view = sanitize_title( $view );

	if ( !isset( $bbp->views[$view] ) )
		return false;

	unset( $bbp->views[$view] );

	return true;
}

/**
 * Run the view's query
 *
 * @since bbPress (r2789)
 *
 * @param string $view Optional. View id
 * @param mixed $new_args New arguments. See {@link bbp_has_topics()}
 * @uses bbp_get_view_id() To get the view id
 * @uses bbp_get_view_query_args() To get the view query args
 * @uses sanitize_title() To sanitize the view name
 * @uses bbp_has_topics() To make the topics query
 * @return bool False if the view doesn't exist, otherwise if topics are there
 */
function bbp_view_query( $view = '', $new_args = '' ) {

	$view = bbp_get_view_id( $view );
	if ( empty( $view ) )
		return false;

	$query_args = bbp_get_view_query_args( $view );

	if ( !empty( $new_args ) ) {
		$new_args   = bbp_parse_args( $new_args, '', 'view_query' );
		$query_args = array_merge( $query_args, $new_args );
	}

	return bbp_has_topics( $query_args );
}

/**
 * Return the view's query arguments
 *
 * @since bbPress (r2789)
 *
 * @param string $view View name
 * @uses bbp_get_view_id() To get the view id
 * @return array Query arguments
 */
function bbp_get_view_query_args( $view ) {
	$view   = bbp_get_view_id( $view );
	$retval = !empty( $view ) ? bbpress()->views[$view]['query'] : false;

	return apply_filters( 'bbp_get_view_query_args', $retval, $view );
}

/** Errors ********************************************************************/

/**
 * Adds an error message to later be output in the theme
 *
 * @since bbPress (r3381)
 *
 * @see WP_Error()
 * @uses WP_Error::add();
 *
 * @param string $code Unique code for the error message
 * @param string $message Translated error message
 * @param string $data Any additional data passed with the error message
 */
function bbp_add_error( $code = '', $message = '', $data = '' ) {
	bbpress()->errors->add( $code, $message, $data );
}

/**
 * Check if error messages exist in queue
 *
 * @since bbPress (r3381)
 *
 * @see WP_Error()
 *
 * @uses is_wp_error()
 * @usese WP_Error::get_error_codes()
 */
function bbp_has_errors() {
	$has_errors = bbpress()->errors->get_error_codes() ? true : false;

	return apply_filters( 'bbp_has_errors', $has_errors, bbpress()->errors );
}

/** Mentions ******************************************************************/

/**
 * Set the pattern used for matching usernames for mentions.
 *
 * Moved into its own function to allow filtering of the regex pattern
 * anywhere mentions might be used.
 *
 * @since bbPress (r4997)
 * @return string Pattern to match usernames with
 */
function bbp_find_mentions_pattern() {
	return apply_filters( 'bbp_find_mentions_pattern', '/[@]+([A-Za-z0-9-_\.@]+)\b/' );
}

/**
 * Searches through the content to locate usernames, designated by an @ sign.
 *
 * @since bbPress (r4323)
 *
 * @param string $content The content
 * @return bool|array $usernames Existing usernames. False if no matches.
 */
function bbp_find_mentions( $content = '' ) {
	$pattern   = bbp_find_mentions_pattern();
	preg_match_all( $pattern, $content, $usernames );
	$usernames = array_unique( array_filter( $usernames[1] ) );

	// Bail if no usernames
	if ( empty( $usernames ) ) {
		$usernames = false;
	}

	return apply_filters( 'bbp_find_mentions', $usernames, $pattern, $content );
}

/**
 * Finds and links @-mentioned users in the content
 *
 * @since bbPress (r4323)
 *
 * @uses bbp_find_mentions() To get usernames in content areas
 * @return string $content Content filtered for mentions
 */
function bbp_mention_filter( $content = '' ) {

	// Get Usernames and bail if none exist
	$usernames = bbp_find_mentions( $content );
	if ( empty( $usernames ) )
		return $content;

	// Loop through usernames and link to profiles
	foreach ( (array) $usernames as $username ) {

		// Skip if username does not exist or user is not active
		$user = get_user_by( 'slug', $username );
		if ( empty( $user->ID ) || bbp_is_user_inactive( $user->ID ) )
			continue;

		// Replace name in content
		$content = preg_replace( '/(@' . $username . '\b)/', sprintf( '<a href="%1$s" rel="nofollow">@%2$s</a>', bbp_get_user_profile_url( $user->ID ), $username ), $content );
	}

	// Return modified content
	return $content;
}

/** Post Statuses *************************************************************/

/**
 * Return the public post status ID
 *
 * @since bbPress (r3504)
 *
 * @return string
 */
function bbp_get_public_status_id() {
	return bbpress()->public_status_id;
}

/**
 * Return the pending post status ID
 *
 * @since bbPress (r3581)
 *
 * @return string
 */
function bbp_get_pending_status_id() {
	return bbpress()->pending_status_id;
}

/**
 * Return the private post status ID
 *
 * @since bbPress (r3504)
 *
 * @return string
 */
function bbp_get_private_status_id() {
	return bbpress()->private_status_id;
}

/**
 * Return the hidden post status ID
 *
 * @since bbPress (r3504)
 *
 * @return string
 */
function bbp_get_hidden_status_id() {
	return bbpress()->hidden_status_id;
}

/**
 * Return the closed post status ID
 *
 * @since bbPress (r3504)
 *
 * @return string
 */
function bbp_get_closed_status_id() {
	return bbpress()->closed_status_id;
}

/**
 * Return the spam post status ID
 *
 * @since bbPress (r3504)
 *
 * @return string
 */
function bbp_get_spam_status_id() {
	return bbpress()->spam_status_id;
}

/**
 * Return the trash post status ID
 *
 * @since bbPress (r3504)
 *
 * @return string
 */
function bbp_get_trash_status_id() {
	return bbpress()->trash_status_id;
}

/**
 * Return the orphan post status ID
 *
 * @since bbPress (r3504)
 *
 * @return string
 */
function bbp_get_orphan_status_id() {
	return bbpress()->orphan_status_id;
}

/** Rewrite IDs ***************************************************************/

/**
 * Return the unique ID for user profile rewrite rules
 *
 * @since bbPress (r3762)
 * @return string
 */
function bbp_get_user_rewrite_id() {
	return bbpress()->user_id;
}

/**
 * Return the unique ID for all edit rewrite rules (forum|topic|reply|tag|user)
 *
 * @since bbPress (r3762)
 * @return string
 */
function bbp_get_edit_rewrite_id() {
	return bbpress()->edit_id;
}

/**
 * Return the unique ID for all search rewrite rules
 *
 * @since bbPress (r4579)
 *
 * @return string
 */
function bbp_get_search_rewrite_id() {
	return bbpress()->search_id;
}

/**
 * Return the unique ID for user topics rewrite rules
 *
 * @since bbPress (r4321)
 * @return string
 */
function bbp_get_user_topics_rewrite_id() {
	return bbpress()->tops_id;
}

/**
 * Return the unique ID for user replies rewrite rules
 *
 * @since bbPress (r4321)
 * @return string
 */
function bbp_get_user_replies_rewrite_id() {
	return bbpress()->reps_id;
}

/**
 * Return the unique ID for user caps rewrite rules
 *
 * @since bbPress (r4181)
 * @return string
 */
function bbp_get_user_favorites_rewrite_id() {
	return bbpress()->favs_id;
}

/**
 * Return the unique ID for user caps rewrite rules
 *
 * @since bbPress (r4181)
 * @return string
 */
function bbp_get_user_subscriptions_rewrite_id() {
	return bbpress()->subs_id;
}

/**
 * Return the unique ID for topic view rewrite rules
 *
 * @since bbPress (r3762)
 * @return string
 */
function bbp_get_view_rewrite_id() {
	return bbpress()->view_id;
}

/** Rewrite Extras ************************************************************/

/**
 * Get the id used for paginated requests
 *
 * @since bbPress (r4926)
 * @return string
 */
function bbp_get_paged_rewrite_id() {
	return bbpress()->paged_id;
}

/**
 * Get the slug used for paginated requests
 *
 * @since bbPress (r4926)
 * @global object $wp_rewrite The WP_Rewrite object
 * @return string
 */
function bbp_get_paged_slug() {
	global $wp_rewrite;
	return $wp_rewrite->pagination_base;
}

/**
 * Delete a blogs rewrite rules, so that they are automatically rebuilt on
 * the subsequent page load.
 *
 * @since bbPress (r4198)
 */
function bbp_delete_rewrite_rules() {
	delete_option( 'rewrite_rules' );
}

/** Requests ******************************************************************/

/**
 * Return true|false if this is a POST request
 *
 * @since bbPress (r4790)
 * @return bool
 */
function bbp_is_post_request() {
	return (bool) ( 'POST' === strtoupper( $_SERVER['REQUEST_METHOD'] ) );
}

/**
 * Return true|false if this is a GET request
 *
 * @since bbPress (r4790)
 * @return bool
 */
function bbp_is_get_request() {
	return (bool) ( 'GET' === strtoupper( $_SERVER['REQUEST_METHOD'] ) );
}

