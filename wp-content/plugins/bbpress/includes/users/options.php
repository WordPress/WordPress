<?php

/**
 * bbPress User Options
 *
 * @package bbPress
 * @subpackage UserOptions
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Get the default user options and their values
 *
 * @since bbPress (r3910)
 * @return array Filtered user option names and values
 */
function bbp_get_default_user_options() {

	// Default options
	return apply_filters( 'bbp_get_default_user_options', array(
		'_bbp_last_posted'   => '0', // For checking flooding
		'_bbp_topic_count'   => '0', // Total topics per site
		'_bbp_reply_count'   => '0', // Total replies per site
		'_bbp_favorites'     => '',  // Favorite topics per site
		'_bbp_subscriptions' => ''   // Subscribed topics per site
	) );
}

/**
 * Add default user options
 *
 * This is destructive, so existing bbPress user options will be overridden.
 *
 * @since bbPress (r3910)
 * @uses bbp_get_default_user_options() To get default options
 * @uses update_user_option() Adds default options
 * @uses do_action() Calls 'bbp_add_user_options'
 */
function bbp_add_user_options( $user_id = 0 ) {

	// Validate user id
	$user_id = bbp_get_user_id( $user_id );
	if ( empty( $user_id ) )
		return;

	// Add default options
	foreach ( bbp_get_default_user_options() as $key => $value )
		update_user_option( $user_id, $key, $value );

	// Allow previously activated plugins to append their own user options.
	do_action( 'bbp_add_user_options', $user_id );
}

/**
 * Delete default user options
 *
 * Hooked to bbp_uninstall, it is only called once when bbPress is uninstalled.
 * This is destructive, so existing bbPress user options will be destroyed.
 *
 * @since bbPress (r3910)
 * @uses bbp_get_default_user_options() To get default options
 * @uses delete_user_option() Removes default options
 * @uses do_action() Calls 'bbp_delete_options'
 */
function bbp_delete_user_options( $user_id = 0 ) {

	// Validate user id
	$user_id = bbp_get_user_id( $user_id );
	if ( empty( $user_id ) )
		return;

	// Add default options
	foreach ( array_keys( bbp_get_default_user_options() ) as $key )
		delete_user_option( $user_id, $key );

	// Allow previously activated plugins to append their own options.
	do_action( 'bbp_delete_user_options', $user_id );
}

/**
 * Add filters to each bbPress option and allow them to be overloaded from
 * inside the $bbp->options array.
 *
 * @since bbPress (r3910)
 * @uses bbp_get_default_user_options() To get default options
 * @uses add_filter() To add filters to 'pre_option_{$key}'
 * @uses do_action() Calls 'bbp_add_option_filters'
 */
function bbp_setup_user_option_filters() {

	// Add filters to each bbPress option
	foreach ( array_keys( bbp_get_default_user_options() ) as $key )
		add_filter( 'get_user_option_' . $key, 'bbp_filter_get_user_option', 10, 3 );

	// Allow previously activated plugins to append their own options.
	do_action( 'bbp_setup_user_option_filters' );
}

/**
 * Filter default options and allow them to be overloaded from inside the
 * $bbp->user_options array.
 *
 * @since bbPress (r3910)
 * @param bool $value Optional. Default value false
 * @return mixed false if not overloaded, mixed if set
 */
function bbp_filter_get_user_option( $value = false, $option = '', $user = 0 ) {
	$bbp = bbpress();

	// Check the options global for preset value
	if ( isset( $user->ID ) && isset( $bbp->user_options[$user->ID] ) && !empty( $bbp->user_options[$user->ID][$option] ) )
		$value = $bbp->user_options[$user->ID][$option];

	// Always return a value, even if false
	return $value;
}

/** Post Counts ***************************************************************/

/**
 * Output a users topic count
 *
 * @since bbPress (r3632)
 *
 * @param int $user_id
 * @param boolean $integer Optional. Whether or not to format the result
 * @uses bbp_get_user_topic_count()
 * @return string
 */
function bbp_user_topic_count( $user_id = 0, $integer = false ) {
	echo bbp_get_user_topic_count( $user_id, $integer );
}
	/**
	 * Return a users reply count
	 *
	 * @since bbPress (r3632)
	 *
	 * @param int $user_id
	 * @param boolean $integer Optional. Whether or not to format the result
	 * @uses bbp_get_user_id()
	 * @uses get_user_option()
	 * @uses apply_filters()
	 * @return string
	 */
	function bbp_get_user_topic_count( $user_id = 0, $integer = false ) {

		// Validate user id
		$user_id = bbp_get_user_id( $user_id );
		if ( empty( $user_id ) )
			return false;

		$count  = (int) get_user_option( '_bbp_topic_count', $user_id );
		$filter = ( false === $integer ) ? 'bbp_get_user_topic_count_int' : 'bbp_get_user_topic_count';

		return apply_filters( $filter, $count, $user_id );
	}

/**
 * Output a users reply count
 *
 * @since bbPress (r3632)
 *
 * @param int $user_id
 * @param boolean $integer Optional. Whether or not to format the result
 * @uses bbp_get_user_reply_count()
 * @return string
 */
function bbp_user_reply_count( $user_id = 0, $integer = false ) {
	echo bbp_get_user_reply_count( $user_id, $integer );
}
	/**
	 * Return a users reply count
	 *
	 * @since bbPress (r3632)
	 *
	 * @param int $user_id
	 * @param boolean $integer Optional. Whether or not to format the result
	 * @uses bbp_get_user_id()
	 * @uses get_user_option()
	 * @uses apply_filters()
	 * @return string
	 */
	function bbp_get_user_reply_count( $user_id = 0, $integer = false ) {

		// Validate user id
		$user_id = bbp_get_user_id( $user_id );
		if ( empty( $user_id ) )
			return false;

		$count  = (int) get_user_option( '_bbp_reply_count', $user_id );
		$filter = ( true === $integer ) ? 'bbp_get_user_topic_count_int' : 'bbp_get_user_topic_count';

		return apply_filters( $filter, $count, $user_id );
	}

/**
 * Output a users total post count
 *
 * @since bbPress (r3632)
 *
 * @param int $user_id
 * @param boolean $integer Optional. Whether or not to format the result
 * @uses bbp_get_user_post_count()
 * @return string
 */
function bbp_user_post_count( $user_id = 0, $integer = false ) {
	echo bbp_get_user_post_count( $user_id, $integer );
}
	/**
	 * Return a users total post count
	 *
	 * @since bbPress (r3632)
	 *
	 * @param int $user_id
	 * @param boolean $integer Optional. Whether or not to format the result
	 * @uses bbp_get_user_id()
	 * @uses get_user_option()
	 * @uses apply_filters()
	 * @return string
	 */
	function bbp_get_user_post_count( $user_id = 0, $integer = false ) {

		// Validate user id
		$user_id = bbp_get_user_id( $user_id );
		if ( empty( $user_id ) )
			return false;

		$topics  = bbp_get_user_topic_count( $user_id, true );
		$replies = bbp_get_user_reply_count( $user_id, true );
		$count   = (int) $topics + $replies;
		$filter  = ( true === $integer ) ? 'bbp_get_user_post_count_int' : 'bbp_get_user_post_count';

		return apply_filters( $filter, $count, $user_id );
	}

/** Last Posted ***************************************************************/

/**
 * Update a users last posted time, for use with post throttling
 *
 * @since bbPress (r3910)
 * @param int $user_id User ID to update
 * @param int $time Time in time() format
 * @return bool False if no user or failure, true if successful
 */
function bbp_update_user_last_posted( $user_id = 0, $time = 0 ) {

	// Validate user id
	$user_id = bbp_get_user_id( $user_id );
	if ( empty( $user_id ) )
		return false;

	// Set time to now if nothing is passed
	if ( empty( $time ) )
		$time = time();

	return update_user_option( $user_id, '_bbp_last_posted', $time );
}

/**
 * Output the raw value of the last posted time.
 *
 * @since bbPress (r3910)
 * @param int $user_id User ID to retrieve value for
 * @uses bbp_get_user_last_posted() To output the last posted time
 */
function bbp_user_last_posted( $user_id = 0 ) {
	echo bbp_get_user_last_posted( $user_id );
}

	/**
	 * Return the raw value of teh last posted time.
	 *
	 * @since bbPress (r3910)
	 * @param int $user_id User ID to retrieve value for
	 * @return mixed False if no user, time() format if exists
	 */
	function bbp_get_user_last_posted( $user_id = 0 ) {

		// Validate user id
		$user_id = bbp_get_user_id( $user_id );
		if ( empty( $user_id ) )
			return false;

		$time = get_user_option( '_bbp_last_posted', $user_id );

		return apply_filters( 'bbp_get_user_last_posted', $time, $user_id );
	}
