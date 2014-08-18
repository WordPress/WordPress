<?php

/**
 * bbPress Common Functions
 *
 * Common functions are ones that are used by more than one component, like
 * forums, topics, replies, users, topic tags, etc...
 *
 * @package bbPress
 * @subpackage Functions
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/** Formatting ****************************************************************/

/**
 * A bbPress specific method of formatting numeric values
 *
 * @since bbPress (r2486)
 *
 * @param string $number Number to format
 * @param string $decimals Optional. Display decimals
 * @uses apply_filters() Calls 'bbp_number_format' with the formatted values,
 *                        number and display decimals bool
 * @return string Formatted string
 */
function bbp_number_format( $number = 0, $decimals = false, $dec_point = '.', $thousands_sep = ',' ) {

	// If empty, set $number to (int) 0
	if ( ! is_numeric( $number ) )
		$number = 0;

	return apply_filters( 'bbp_number_format', number_format( $number, $decimals, $dec_point, $thousands_sep ), $number, $decimals, $dec_point, $thousands_sep );
}

/**
 * A bbPress specific method of formatting numeric values
 *
 * @since bbPress (r3857)
 *
 * @param string $number Number to format
 * @param string $decimals Optional. Display decimals
 * @uses apply_filters() Calls 'bbp_number_format' with the formatted values,
 *                        number and display decimals bool
 * @return string Formatted string
 */
function bbp_number_format_i18n( $number = 0, $decimals = false ) {

	// If empty, set $number to (int) 0
	if ( ! is_numeric( $number ) )
		$number = 0;

	return apply_filters( 'bbp_number_format_i18n', number_format_i18n( $number, $decimals ), $number, $decimals );
}

/**
 * Convert time supplied from database query into specified date format.
 *
 * @since bbPress (r2455)
 *
 * @param int|object $post Optional. Default is global post object. A post_id or
 *                          post object
 * @param string $d Optional. Default is 'U'. Either 'G', 'U', or php date
 *                             format
 * @param bool $translate Optional. Default is false. Whether to translate the
 *                                   result
 * @uses mysql2date() To convert the format
 * @uses apply_filters() Calls 'bbp_convert_date' with the time, date format
 *                        and translate bool
 * @return string Returns timestamp
 */
function bbp_convert_date( $time, $d = 'U', $translate = false ) {
	$time = mysql2date( $d, $time, $translate );

	return apply_filters( 'bbp_convert_date', $time, $d, $translate );
}

/**
 * Output formatted time to display human readable time difference.
 *
 * @since bbPress (r2544)
 *
 * @param string $older_date Unix timestamp from which the difference begins.
 * @param string $newer_date Optional. Unix timestamp from which the
 *                            difference ends. False for current time.
 * @param int $gmt Optional. Whether to use GMT timezone. Default is false.
 * @uses bbp_get_time_since() To get the formatted time
 */
function bbp_time_since( $older_date, $newer_date = false, $gmt = false ) {
	echo bbp_get_time_since( $older_date, $newer_date, $gmt );
}
	/**
	 * Return formatted time to display human readable time difference.
	 *
	 * @since bbPress (r2544)
	 *
	 * @param string $older_date Unix timestamp from which the difference begins.
	 * @param string $newer_date Optional. Unix timestamp from which the
	 *                            difference ends. False for current time.
	 * @param int $gmt Optional. Whether to use GMT timezone. Default is false.
	 * @uses current_time() To get the current time in mysql format
	 * @uses human_time_diff() To get the time differene in since format
	 * @uses apply_filters() Calls 'bbp_get_time_since' with the time
	 *                        difference and time
	 * @return string Formatted time
	 */
	function bbp_get_time_since( $older_date, $newer_date = false, $gmt = false ) {

		// Setup the strings
		$unknown_text   = apply_filters( 'bbp_core_time_since_unknown_text',   __( 'sometime',  'bbpress' ) );
		$right_now_text = apply_filters( 'bbp_core_time_since_right_now_text', __( 'right now', 'bbpress' ) );
		$ago_text       = apply_filters( 'bbp_core_time_since_ago_text',       __( '%s ago',    'bbpress' ) );

		// array of time period chunks
		$chunks = array(
			array( 60 * 60 * 24 * 365 , __( 'year',   'bbpress' ), __( 'years',   'bbpress' ) ),
			array( 60 * 60 * 24 * 30 ,  __( 'month',  'bbpress' ), __( 'months',  'bbpress' ) ),
			array( 60 * 60 * 24 * 7,    __( 'week',   'bbpress' ), __( 'weeks',   'bbpress' ) ),
			array( 60 * 60 * 24 ,       __( 'day',    'bbpress' ), __( 'days',    'bbpress' ) ),
			array( 60 * 60 ,            __( 'hour',   'bbpress' ), __( 'hours',   'bbpress' ) ),
			array( 60 ,                 __( 'minute', 'bbpress' ), __( 'minutes', 'bbpress' ) ),
			array( 1,                   __( 'second', 'bbpress' ), __( 'seconds', 'bbpress' ) )
		);

		if ( !empty( $older_date ) && !is_numeric( $older_date ) ) {
			$time_chunks = explode( ':', str_replace( ' ', ':', $older_date ) );
			$date_chunks = explode( '-', str_replace( ' ', '-', $older_date ) );
			$older_date  = gmmktime( (int) $time_chunks[1], (int) $time_chunks[2], (int) $time_chunks[3], (int) $date_chunks[1], (int) $date_chunks[2], (int) $date_chunks[0] );
		}

		// $newer_date will equal false if we want to know the time elapsed
		// between a date and the current time. $newer_date will have a value if
		// we want to work out time elapsed between two known dates.
		$newer_date = ( !$newer_date ) ? strtotime( current_time( 'mysql', $gmt ) ) : $newer_date;

		// Difference in seconds
		$since = $newer_date - $older_date;

		// Something went wrong with date calculation and we ended up with a negative date.
		if ( 0 > $since ) {
			$output = $unknown_text;

		// We only want to output two chunks of time here, eg:
		//     x years, xx months
		//     x days, xx hours
		// so there's only two bits of calculation below:
		} else {

			// Step one: the first chunk
			for ( $i = 0, $j = count( $chunks ); $i < $j; ++$i ) {
				$seconds = $chunks[$i][0];

				// Finding the biggest chunk (if the chunk fits, break)
				$count = floor( $since / $seconds );
				if ( 0 != $count ) {
					break;
				}
			}

			// If $i iterates all the way to $j, then the event happened 0 seconds ago
			if ( !isset( $chunks[$i] ) ) {
				$output = $right_now_text;

			} else {

				// Set output var
				$output = ( 1 == $count ) ? '1 '. $chunks[$i][1] : $count . ' ' . $chunks[$i][2];

				// Step two: the second chunk
				if ( $i + 2 < $j ) {
					$seconds2 = $chunks[$i + 1][0];
					$name2    = $chunks[$i + 1][1];
					$count2   = floor( ( $since - ( $seconds * $count ) ) / $seconds2 );

					// Add to output var
					if ( 0 != $count2 ) {
						$output .= ( 1 == $count2 ) ? _x( ',', 'Separator in time since', 'bbpress' ) . ' 1 '. $name2 : _x( ',', 'Separator in time since', 'bbpress' ) . ' ' . $count2 . ' ' . $chunks[$i + 1][2];
					}
				}

				// No output, so happened right now
				if ( ! (int) trim( $output ) ) {
					$output = $right_now_text;
				}
			}
		}

		// Append 'ago' to the end of time-since if not 'right now'
		if ( $output != $right_now_text ) {
			$output = sprintf( $ago_text, $output );
		}

		return apply_filters( 'bbp_get_time_since', $output, $older_date, $newer_date );
	}

/**
 * Formats the reason for editing the topic/reply.
 *
 * Does these things:
 *  - Trimming
 *  - Removing periods from the end of the string
 *  - Trimming again
 *
 * @since bbPress (r2782)
 *
 * @param int $topic_id Optional. Topic id
 * @return string Status of topic
 */
function bbp_format_revision_reason( $reason = '' ) {
	$reason = (string) $reason;

	// Format reason for proper display
	if ( empty( $reason ) )
		return $reason;

	// Trimming
	$reason = trim( $reason );

	// We add our own full stop.
	while ( substr( $reason, -1 ) === '.' )
		$reason = substr( $reason, 0, -1 );

	// Trim again
	$reason = trim( $reason );

	return $reason;
}

/** Misc **********************************************************************/

/**
 * Return the unescaped redirect_to request value
 *
 * @bbPress (r4655)
 *
 * @return string The URL to redirect to, if set
 */
function bbp_get_redirect_to() {
	$retval = !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '';

	return apply_filters( 'bbp_get_redirect_to', $retval );
}

/**
 * Append 'view=all' to query string if it's already there from referer
 *
 * @since bbPress (r3325)
 *
 * @param string $original_link Original Link to be modified
 * @param bool $force Override bbp_get_view_all() check
 * @uses current_user_can() To check if the current user can moderate
 * @uses add_query_arg() To add args to the url
 * @uses apply_filters() Calls 'bbp_add_view_all' with the link and original link
 * @return string The link with 'view=all' appended if necessary
 */
function bbp_add_view_all( $original_link = '', $force = false ) {

	// Are we appending the view=all vars?
	if ( bbp_get_view_all() || !empty( $force ) ) {
		$link = add_query_arg( array( 'view' => 'all' ), $original_link );
	} else {
		$link = $original_link;
	}

	return apply_filters( 'bbp_add_view_all', $link, $original_link );
}

/**
 * Remove 'view=all' from query string
 *
 * @since bbPress (r3325)
 *
 * @param string $original_link Original Link to be modified
 * @uses current_user_can() To check if the current user can moderate
 * @uses add_query_arg() To add args to the url
 * @uses apply_filters() Calls 'bbp_add_view_all' with the link and original link
 * @return string The link with 'view=all' appended if necessary
 */
function bbp_remove_view_all( $original_link = '' ) {
	return apply_filters( 'bbp_add_view_all', remove_query_arg( 'view', $original_link ), $original_link );
}

/**
 * If current user can and is vewing all topics/replies
 *
 * @since bbPress (r3325)
 *
 * @uses current_user_can() To check if the current user can moderate
 * @uses apply_filters() Calls 'bbp_get_view_all' with the link and original link
 * @return bool Whether current user can and is viewing all
 */
function bbp_get_view_all( $cap = 'moderate' ) {
	$retval = ( ( !empty( $_GET['view'] ) && ( 'all' === $_GET['view'] ) && current_user_can( $cap ) ) );
	return apply_filters( 'bbp_get_view_all', (bool) $retval );
}

/**
 * Assist pagination by returning correct page number
 *
 * @since bbPress (r2628)
 *
 * @uses get_query_var() To get the 'paged' value
 * @return int Current page number
 */
function bbp_get_paged() {
	global $wp_query;

	// Check the query var
	if ( get_query_var( 'paged' ) ) {
		$paged = get_query_var( 'paged' );

	// Check query paged
	} elseif ( !empty( $wp_query->query['paged'] ) ) {
		$paged = $wp_query->query['paged'];
	}

	// Paged found
	if ( !empty( $paged ) )
		return (int) $paged;

	// Default to first page
	return 1;
}

/**
 * Fix post author id on post save
 *
 * When a logged in user changes the status of an anonymous reply or topic, or
 * edits it, the post_author field is set to the logged in user's id. This
 * function fixes that.
 *
 * @since bbPress (r2734)
 *
 * @param array $data Post data
 * @param array $postarr Original post array (includes post id)
 * @uses bbp_get_topic_post_type() To get the topic post type
 * @uses bbp_get_reply_post_type() To get the reply post type
 * @uses bbp_is_topic_anonymous() To check if the topic is by an anonymous user
 * @uses bbp_is_reply_anonymous() To check if the reply is by an anonymous user
 * @return array Data
 */
function bbp_fix_post_author( $data = array(), $postarr = array() ) {

	// Post is not being updated or the post_author is already 0, return
	if ( empty( $postarr['ID'] ) || empty( $data['post_author'] ) )
		return $data;

	// Post is not a topic or reply, return
	if ( !in_array( $data['post_type'], array( bbp_get_topic_post_type(), bbp_get_reply_post_type() ) ) )
		return $data;

	// Is the post by an anonymous user?
	if ( ( bbp_get_topic_post_type() === $data['post_type'] && !bbp_is_topic_anonymous( $postarr['ID'] ) ) ||
	     ( bbp_get_reply_post_type() === $data['post_type'] && !bbp_is_reply_anonymous( $postarr['ID'] ) ) )
		return $data;

	// The post is being updated. It is a topic or a reply and is written by an anonymous user.
	// Set the post_author back to 0
	$data['post_author'] = 0;

	return $data;
}

/**
 * Check the date against the _bbp_edit_lock setting.
 *
 * @since bbPress (r3133)
 *
 * @param string $post_date_gmt
 *
 * @uses get_option() Get the edit lock time
 * @uses current_time() Get the current time
 * @uses strtotime() Convert strings to time
 * @uses apply_filters() Allow output to be manipulated
 *
 * @return bool
 */
function bbp_past_edit_lock( $post_date_gmt ) {

	// Assume editing is allowed
	$retval = false;

	// Bail if empty date
	if ( ! empty( $post_date_gmt ) ) {

		// Period of time
		$lockable  = '+' . get_option( '_bbp_edit_lock', '5' ) . ' minutes';

		// Now
		$cur_time  = current_time( 'timestamp', true );

		// Add lockable time to post time
		$lock_time = strtotime( $lockable, strtotime( $post_date_gmt ) );

		// Compare
		if ( $cur_time >= $lock_time ) {
			$retval = true;
		}
	}

	return apply_filters( 'bbp_past_edit_lock', (bool) $retval, $cur_time, $lock_time, $post_date_gmt );
}

/** Statistics ****************************************************************/

/**
 * Get the forum statistics
 *
 * @since bbPress (r2769)
 *
 * @param mixed $args Optional. The function supports these arguments (all
 *                     default to true):
 *  - count_users: Count users?
 *  - count_forums: Count forums?
 *  - count_topics: Count topics? If set to false, private, spammed and trashed
 *                   topics are also not counted.
 *  - count_private_topics: Count private topics? (only counted if the current
 *                           user has read_private_topics cap)
 *  - count_spammed_topics: Count spammed topics? (only counted if the current
 *                           user has edit_others_topics cap)
 *  - count_trashed_topics: Count trashed topics? (only counted if the current
 *                           user has view_trash cap)
 *  - count_replies: Count replies? If set to false, private, spammed and
 *                   trashed replies are also not counted.
 *  - count_private_replies: Count private replies? (only counted if the current
 *                           user has read_private_replies cap)
 *  - count_spammed_replies: Count spammed replies? (only counted if the current
 *                           user has edit_others_replies cap)
 *  - count_trashed_replies: Count trashed replies? (only counted if the current
 *                           user has view_trash cap)
 *  - count_tags: Count tags? If set to false, empty tags are also not counted
 *  - count_empty_tags: Count empty tags?
 * @uses bbp_count_users() To count the number of registered users
 * @uses bbp_get_forum_post_type() To get the forum post type
 * @uses bbp_get_topic_post_type() To get the topic post type
 * @uses bbp_get_reply_post_type() To get the reply post type
 * @uses wp_count_posts() To count the number of forums, topics and replies
 * @uses wp_count_terms() To count the number of topic tags
 * @uses current_user_can() To check if the user is capable of doing things
 * @uses number_format_i18n() To format the number
 * @uses apply_filters() Calls 'bbp_get_statistics' with the statistics and args
 * @return object Walked forum tree
 */
function bbp_get_statistics( $args = '' ) {

	// Parse arguments against default values
	$r = bbp_parse_args( $args, array(
		'count_users'           => true,
		'count_forums'          => true,
		'count_topics'          => true,
		'count_private_topics'  => true,
		'count_spammed_topics'  => true,
		'count_trashed_topics'  => true,
		'count_replies'         => true,
		'count_private_replies' => true,
		'count_spammed_replies' => true,
		'count_trashed_replies' => true,
		'count_tags'            => true,
		'count_empty_tags'      => true
	), 'get_statistics' );

	// Defaults
	$user_count            = 0;
	$forum_count           = 0;
	$topic_count           = 0;
	$topic_count_hidden    = 0;
	$reply_count           = 0;
	$reply_count_hidden    = 0;
	$topic_tag_count       = 0;
	$empty_topic_tag_count = 0;

	// Users
	if ( !empty( $r['count_users'] ) ) {
		$user_count = bbp_get_total_users();
	}

	// Forums
	if ( !empty( $r['count_forums'] ) ) {
		$forum_count = wp_count_posts( bbp_get_forum_post_type() )->publish;
	}

	// Post statuses
	$private = bbp_get_private_status_id();
	$spam    = bbp_get_spam_status_id();
	$trash   = bbp_get_trash_status_id();
	$closed  = bbp_get_closed_status_id();

	// Topics
	if ( !empty( $r['count_topics'] ) ) {
		$all_topics  = wp_count_posts( bbp_get_topic_post_type() );

		// Published (publish + closed)
		$topic_count = $all_topics->publish + $all_topics->{$closed};

		if ( current_user_can( 'read_private_topics' ) || current_user_can( 'edit_others_topics' ) || current_user_can( 'view_trash' ) ) {

			// Private
			$topics['private'] = ( !empty( $r['count_private_topics'] ) && current_user_can( 'read_private_topics' ) ) ? (int) $all_topics->{$private} : 0;

			// Spam
			$topics['spammed'] = ( !empty( $r['count_spammed_topics'] ) && current_user_can( 'edit_others_topics'  ) ) ? (int) $all_topics->{$spam}    : 0;

			// Trash
			$topics['trashed'] = ( !empty( $r['count_trashed_topics'] ) && current_user_can( 'view_trash'          ) ) ? (int) $all_topics->{$trash}   : 0;

			// Total hidden (private + spam + trash)
			$topic_count_hidden = $topics['private'] + $topics['spammed'] + $topics['trashed'];

			// Generate the hidden topic count's title attribute
			$topic_titles[] = !empty( $topics['private'] ) ? sprintf( __( 'Private: %s', 'bbpress' ), number_format_i18n( $topics['private'] ) ) : '';
			$topic_titles[] = !empty( $topics['spammed'] ) ? sprintf( __( 'Spammed: %s', 'bbpress' ), number_format_i18n( $topics['spammed'] ) ) : '';
			$topic_titles[] = !empty( $topics['trashed'] ) ? sprintf( __( 'Trashed: %s', 'bbpress' ), number_format_i18n( $topics['trashed'] ) ) : '';

			// Compile the hidden topic title
			$hidden_topic_title = implode( ' | ', array_filter( $topic_titles ) );
		}
	}

	// Replies
	if ( !empty( $r['count_replies'] ) ) {

		$all_replies = wp_count_posts( bbp_get_reply_post_type() );

		// Published
		$reply_count = $all_replies->publish;

		if ( current_user_can( 'read_private_replies' ) || current_user_can( 'edit_others_replies' ) || current_user_can( 'view_trash' ) ) {

			// Private
			$replies['private'] = ( !empty( $r['count_private_replies'] ) && current_user_can( 'read_private_replies' ) ) ? (int) $all_replies->{$private} : 0;

			// Spam
			$replies['spammed'] = ( !empty( $r['count_spammed_replies'] ) && current_user_can( 'edit_others_replies'  ) ) ? (int) $all_replies->{$spam}    : 0;

			// Trash
			$replies['trashed'] = ( !empty( $r['count_trashed_replies'] ) && current_user_can( 'view_trash'           ) ) ? (int) $all_replies->{$trash}   : 0;

			// Total hidden (private + spam + trash)
			$reply_count_hidden = $replies['private'] + $replies['spammed'] + $replies['trashed'];

			// Generate the hidden topic count's title attribute
			$reply_titles[] = !empty( $replies['private'] ) ? sprintf( __( 'Private: %s', 'bbpress' ), number_format_i18n( $replies['private'] ) ) : '';
			$reply_titles[] = !empty( $replies['spammed'] ) ? sprintf( __( 'Spammed: %s', 'bbpress' ), number_format_i18n( $replies['spammed'] ) ) : '';
			$reply_titles[] = !empty( $replies['trashed'] ) ? sprintf( __( 'Trashed: %s', 'bbpress' ), number_format_i18n( $replies['trashed'] ) ) : '';

			// Compile the hidden replies title
			$hidden_reply_title = implode( ' | ', array_filter( $reply_titles ) );

		}
	}

	// Topic Tags
	if ( !empty( $r['count_tags'] ) && bbp_allow_topic_tags() ) {

		// Get the count
		$topic_tag_count = wp_count_terms( bbp_get_topic_tag_tax_id(), array( 'hide_empty' => true ) );

		// Empty tags
		if ( !empty( $r['count_empty_tags'] ) && current_user_can( 'edit_topic_tags' ) ) {
			$empty_topic_tag_count = wp_count_terms( bbp_get_topic_tag_tax_id() ) - $topic_tag_count;
		}
	}

	// Tally the tallies
	$statistics = array_map( 'number_format_i18n', array_map( 'absint', compact(
		'user_count',
		'forum_count',
		'topic_count',
		'topic_count_hidden',
		'reply_count',
		'reply_count_hidden',
		'topic_tag_count',
		'empty_topic_tag_count'
	) ) );

	// Add the hidden (topic/reply) count title attribute strings because we
	// don't need to run the math functions on these (see above)
	$statistics['hidden_topic_title'] = isset( $hidden_topic_title ) ? $hidden_topic_title : '';
	$statistics['hidden_reply_title'] = isset( $hidden_reply_title ) ? $hidden_reply_title : '';

	return apply_filters( 'bbp_get_statistics', $statistics, $r );
}

/** New/edit topic/reply helpers **********************************************/

/**
 * Filter anonymous post data
 *
 * We use REMOTE_ADDR here directly. If you are behind a proxy, you should
 * ensure that it is properly set, such as in wp-config.php, for your
 * environment. See {@link http://core.trac.wordpress.org/ticket/9235}
 *
 * Note that bbp_pre_anonymous_filters() is responsible for sanitizing each
 * of the filtered core anonymous values here.
 *
 * If there are any errors, those are directly added to {@link bbPress:errors}
 *
 * @since bbPress (r2734)
 *
 * @param mixed $args Optional. If no args are there, then $_POST values are
 *                     used.
 * @uses apply_filters() Calls 'bbp_pre_anonymous_post_author_name' with the
 *                        anonymous user name
 * @uses apply_filters() Calls 'bbp_pre_anonymous_post_author_email' with the
 *                        anonymous user email
 * @uses apply_filters() Calls 'bbp_pre_anonymous_post_author_website' with the
 *                        anonymous user website
 * @return bool|array False on errors, values in an array on success
 */
function bbp_filter_anonymous_post_data( $args = '' ) {

	// Parse arguments against default values
	$r = bbp_parse_args( $args, array (
		'bbp_anonymous_name'    => !empty( $_POST['bbp_anonymous_name']    ) ? $_POST['bbp_anonymous_name']    : false,
		'bbp_anonymous_email'   => !empty( $_POST['bbp_anonymous_email']   ) ? $_POST['bbp_anonymous_email']   : false,
		'bbp_anonymous_website' => !empty( $_POST['bbp_anonymous_website'] ) ? $_POST['bbp_anonymous_website'] : false,
	), 'filter_anonymous_post_data' );

	// Filter variables and add errors if necessary
	$r['bbp_anonymous_name'] = apply_filters( 'bbp_pre_anonymous_post_author_name',  $r['bbp_anonymous_name']  );
	if ( empty( $r['bbp_anonymous_name'] ) )
		bbp_add_error( 'bbp_anonymous_name',  __( '<strong>ERROR</strong>: Invalid author name submitted!',   'bbpress' ) );

	$r['bbp_anonymous_email'] = apply_filters( 'bbp_pre_anonymous_post_author_email', $r['bbp_anonymous_email'] );
	if ( empty( $r['bbp_anonymous_email'] ) )
		bbp_add_error( 'bbp_anonymous_email', __( '<strong>ERROR</strong>: Invalid email address submitted!', 'bbpress' ) );

	// Website is optional
	$r['bbp_anonymous_website'] = apply_filters( 'bbp_pre_anonymous_post_author_website', $r['bbp_anonymous_website'] );

	// Return false if we have any errors
	$retval = bbp_has_errors() ? false : $r;

	// Finally, return sanitized data or false
	return apply_filters( 'bbp_filter_anonymous_post_data', $retval, $r );
}

/**
 * Check for duplicate topics/replies
 *
 * Check to make sure that a user is not making a duplicate post
 *
 * @since bbPress (r2763)
 *
 * @param array $post_data Contains information about the comment
 * @uses current_user_can() To check if the current user can throttle
 * @uses get_meta_sql() To generate the meta sql for checking anonymous email
 * @uses apply_filters() Calls 'bbp_check_for_duplicate_query' with the
 *                        duplicate check query and post data
 * @uses wpdb::get_var() To execute our query and get the var back
 * @uses get_post_meta() To get the anonymous user email post meta
 * @uses do_action() Calls 'bbp_post_duplicate_trigger' with the post data when
 *                    it is found that it is a duplicate
 * @return bool True if it is not a duplicate, false if it is
 */
function bbp_check_for_duplicate( $post_data = array() ) {

	// No duplicate checks for those who can throttle
	if ( current_user_can( 'throttle' ) )
		return true;

	// Define global to use get_meta_sql() and get_var() methods
	global $wpdb;

	// Parse arguments against default values
	$r = bbp_parse_args( $post_data, array(
		'post_author'    => 0,
		'post_type'      => array( bbp_get_topic_post_type(), bbp_get_reply_post_type() ),
		'post_parent'    => 0,
		'post_content'   => '',
		'post_status'    => bbp_get_trash_status_id(),
		'anonymous_data' => false
	), 'check_for_duplicate' );

	// Check for anonymous post
	if ( empty( $r['post_author'] ) && ( !empty( $r['anonymous_data'] ) && !empty( $r['anonymous_data']['bbp_anonymous_email'] ) ) ) {
		$clauses = get_meta_sql( array( array(
			'key'   => '_bbp_anonymous_email',
			'value' => $r['anonymous_data']['bbp_anonymous_email']
		) ), 'post', $wpdb->posts, 'ID' );

		$join    = $clauses['join'];
		$where   = $clauses['where'];
	} else {
		$join    = $where = '';
	}

	// Unslash $r to pass through $wpdb->prepare()
	//
	// @see: http://bbpress.trac.wordpress.org/ticket/2185/
	// @see: http://core.trac.wordpress.org/changeset/23973/
	$r = function_exists( 'wp_unslash' ) ? wp_unslash( $r ) : stripslashes_deep( $r );

	// Prepare duplicate check query
	$query  = $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} {$join} WHERE post_type = %s AND post_status != %s AND post_author = %d AND post_content = %s {$where}", $r['post_type'], $r['post_status'], $r['post_author'], $r['post_content'] );
	$query .= !empty( $r['post_parent'] ) ? $wpdb->prepare( " AND post_parent = %d", $r['post_parent'] ) : '';
	$query .= " LIMIT 1";
	$dupe   = apply_filters( 'bbp_check_for_duplicate_query', $query, $r );

	if ( $wpdb->get_var( $dupe ) ) {
		do_action( 'bbp_check_for_duplicate_trigger', $post_data );
		return false;
	}

	return true;
}

/**
 * Check for flooding
 *
 * Check to make sure that a user is not making too many posts in a short amount
 * of time.
 *
 * @since bbPress (r2734)
 *
 * @param false|array $anonymous_data Optional - if it's an anonymous post. Do
 *                                     not supply if supplying $author_id.
 *                                     Should have key 'bbp_author_ip'.
 *                                     Should be sanitized (see
 *                                     {@link bbp_filter_anonymous_post_data()}
 *                                     for sanitization)
 * @param int $author_id Optional. Supply if it's a post by a logged in user.
 *                        Do not supply if supplying $anonymous_data.
 * @uses get_option() To get the throttle time
 * @uses get_transient() To get the last posted transient of the ip
 * @uses bbp_get_user_last_posted() To get the last posted time of the user
 * @uses current_user_can() To check if the current user can throttle
 * @return bool True if there is no flooding, false if there is
 */
function bbp_check_for_flood( $anonymous_data = false, $author_id = 0 ) {

	// Option disabled. No flood checks.
	$throttle_time = get_option( '_bbp_throttle_time' );
	if ( empty( $throttle_time ) )
		return true;

	// User is anonymous, so check a transient based on the IP
	if ( !empty( $anonymous_data ) && is_array( $anonymous_data ) ) {
		$last_posted = get_transient( '_bbp_' . bbp_current_author_ip() . '_last_posted' );

		if ( !empty( $last_posted ) && time() < $last_posted + $throttle_time ) {
			return false;
		}

	// User is logged in, so check their last posted time
	} elseif ( !empty( $author_id ) ) {
		$author_id   = (int) $author_id;
		$last_posted = bbp_get_user_last_posted( $author_id );

		if ( isset( $last_posted ) && time() < $last_posted + $throttle_time && !current_user_can( 'throttle' ) ) {
			return false;
		}
	} else {
		return false;
	}

	return true;
}

/**
 * Checks topics and replies against the discussion moderation of blocked keys
 *
 * @since bbPress (r3581)
 *
 * @param array $anonymous_data Anonymous user data
 * @param int $author_id Topic or reply author ID
 * @param string $title The title of the content
 * @param string $content The content being posted
 * @uses bbp_is_user_keymaster() Allow keymasters to bypass blacklist
 * @uses bbp_current_author_ip() To get current user IP address
 * @uses bbp_current_author_ua() To get current user agent
 * @return bool True if test is passed, false if fail
 */
function bbp_check_for_moderation( $anonymous_data = false, $author_id = 0, $title = '', $content = '' ) {

	// Allow for moderation check to be skipped
	if ( apply_filters( 'bbp_bypass_check_for_moderation', false, $anonymous_data, $author_id, $title, $content ) )
		return true;

	// Bail if keymaster is author
	if ( !empty( $author_id ) && bbp_is_user_keymaster( $author_id ) )
		return true;

	// Define local variable(s)
	$_post     = array();
	$match_out = '';

	/** Blacklist *************************************************************/

	// Get the moderation keys
	$blacklist = trim( get_option( 'moderation_keys' ) );

	// Bail if blacklist is empty
	if ( empty( $blacklist ) )
		return true;

	/** User Data *************************************************************/

	// Map anonymous user data
	if ( !empty( $anonymous_data ) ) {
		$_post['author'] = $anonymous_data['bbp_anonymous_name'];
		$_post['email']  = $anonymous_data['bbp_anonymous_email'];
		$_post['url']    = $anonymous_data['bbp_anonymous_website'];

	// Map current user data
	} elseif ( !empty( $author_id ) ) {

		// Get author data
		$user = get_userdata( $author_id );

		// If data exists, map it
		if ( !empty( $user ) ) {
			$_post['author'] = $user->display_name;
			$_post['email']  = $user->user_email;
			$_post['url']    = $user->user_url;
		}
	}

	// Current user IP and user agent
	$_post['user_ip'] = bbp_current_author_ip();
	$_post['user_ua'] = bbp_current_author_ua();

	// Post title and content
	$_post['title']   = $title;
	$_post['content'] = $content;

	/** Max Links *************************************************************/

	$max_links = get_option( 'comment_max_links' );
	if ( !empty( $max_links ) ) {

		// How many links?
		$num_links = preg_match_all( '/<a [^>]*href/i', $content, $match_out );

		// Allow for bumping the max to include the user's URL
		$num_links = apply_filters( 'comment_max_links_url', $num_links, $_post['url'] );

		// Das ist zu viele links!
		if ( $num_links >= $max_links ) {
			return false;
		}
	}

	/** Words *****************************************************************/

	// Get words separated by new lines
	$words = explode( "\n", $blacklist );

	// Loop through words
	foreach ( (array) $words as $word ) {

		// Trim the whitespace from the word
		$word = trim( $word );

		// Skip empty lines
		if ( empty( $word ) ) { continue; }

		// Do some escaping magic so that '#' chars in the
		// spam words don't break things:
		$word    = preg_quote( $word, '#' );
		$pattern = "#$word#i";

		// Loop through post data
		foreach ( $_post as $post_data ) {

			// Check each user data for current word
			if ( preg_match( $pattern, $post_data ) ) {

				// Post does not pass
				return false;
			}
		}
	}

	// Check passed successfully
	return true;
}

/**
 * Checks topics and replies against the discussion blacklist of blocked keys
 *
 * @since bbPress (r3446)
 *
 * @param array $anonymous_data Anonymous user data
 * @param int $author_id Topic or reply author ID
 * @param string $title The title of the content
 * @param string $content The content being posted
 * @uses bbp_is_user_keymaster() Allow keymasters to bypass blacklist
 * @uses bbp_current_author_ip() To get current user IP address
 * @uses bbp_current_author_ua() To get current user agent
 * @return bool True if test is passed, false if fail
 */
function bbp_check_for_blacklist( $anonymous_data = false, $author_id = 0, $title = '', $content = '' ) {

	// Allow for blacklist check to be skipped
	if ( apply_filters( 'bbp_bypass_check_for_blacklist', false, $anonymous_data, $author_id, $title, $content ) )
		return true;

	// Bail if keymaster is author
	if ( !empty( $author_id ) && bbp_is_user_keymaster( $author_id ) )
		return true;

	// Define local variable
	$_post = array();

	/** Blacklist *************************************************************/

	// Get the moderation keys
	$blacklist = trim( get_option( 'blacklist_keys' ) );

	// Bail if blacklist is empty
	if ( empty( $blacklist ) )
		return true;

	/** User Data *************************************************************/

	// Map anonymous user data
	if ( !empty( $anonymous_data ) ) {
		$_post['author'] = $anonymous_data['bbp_anonymous_name'];
		$_post['email']  = $anonymous_data['bbp_anonymous_email'];
		$_post['url']    = $anonymous_data['bbp_anonymous_website'];

	// Map current user data
	} elseif ( !empty( $author_id ) ) {

		// Get author data
		$user = get_userdata( $author_id );

		// If data exists, map it
		if ( !empty( $user ) ) {
			$_post['author'] = $user->display_name;
			$_post['email']  = $user->user_email;
			$_post['url']    = $user->user_url;
		}
	}

	// Current user IP and user agent
	$_post['user_ip'] = bbp_current_author_ip();
	$_post['user_ua'] = bbp_current_author_ua();

	// Post title and content
	$_post['title']   = $title;
	$_post['content'] = $content;

	/** Words *****************************************************************/

	// Get words separated by new lines
	$words = explode( "\n", $blacklist );

	// Loop through words
	foreach ( (array) $words as $word ) {

		// Trim the whitespace from the word
		$word = trim( $word );

		// Skip empty lines
		if ( empty( $word ) ) { continue; }

		// Do some escaping magic so that '#' chars in the
		// spam words don't break things:
		$word    = preg_quote( $word, '#' );
		$pattern = "#$word#i";

		// Loop through post data
		foreach ( $_post as $post_data ) {

			// Check each user data for current word
			if ( preg_match( $pattern, $post_data ) ) {

				// Post does not pass
				return false;
			}
		}
	}

	// Check passed successfully
	return true;
}

/** Subscriptions *************************************************************/

/**
 * Sends notification emails for new replies to subscribed topics
 *
 * Gets new post's ID and check if there are subscribed users to that topic, and
 * if there are, send notifications
 *
 * Note: in bbPress 2.6, we've moved away from 1 email per subscriber to 1 email
 * with everyone BCC'd. This may have negative repercussions for email services
 * that limit the number of addresses in a BCC field (often to around 500.) In
 * those cases, we recommend unhooking this function and creating your own
 * custom emailer script.
 * 
 * @since bbPress (r2668)
 *
 * @param int $reply_id ID of the newly made reply
 * @uses bbp_is_subscriptions_active() To check if the subscriptions are active
 * @uses bbp_get_reply_id() To validate the reply ID
 * @uses bbp_get_topic_id() To validate the topic ID
 * @uses bbp_get_forum_id() To validate the forum ID
 * @uses bbp_get_reply() To get the reply
 * @uses bbp_is_reply_published() To make sure the reply is published
 * @uses bbp_get_topic_id() To validate the topic ID
 * @uses bbp_get_topic() To get the reply's topic
 * @uses bbp_is_topic_published() To make sure the topic is published
 * @uses bbp_get_reply_author_display_name() To get the reply author's display name
 * @uses do_action() Calls 'bbp_pre_notify_subscribers' with the reply id,
 *                    topic id and user id
 * @uses bbp_get_topic_subscribers() To get the topic subscribers
 * @uses apply_filters() Calls 'bbp_subscription_mail_message' with the
 *                    message, reply id, topic id and user id
 * @uses apply_filters() Calls 'bbp_subscription_mail_title' with the
 *                    topic title, reply id, topic id and user id
 * @uses apply_filters() Calls 'bbp_subscription_mail_headers'
 * @uses get_userdata() To get the user data
 * @uses wp_mail() To send the mail
 * @uses do_action() Calls 'bbp_post_notify_subscribers' with the reply id,
 *                    topic id and user id
 * @return bool True on success, false on failure
 */
function bbp_notify_subscribers( $reply_id = 0, $topic_id = 0, $forum_id = 0, $anonymous_data = false, $reply_author = 0 ) {

	// Bail if subscriptions are turned off
	if ( !bbp_is_subscriptions_active() ) {
		return false;
	}

	/** Validation ************************************************************/

	$reply_id = bbp_get_reply_id( $reply_id );
	$topic_id = bbp_get_topic_id( $topic_id );
	$forum_id = bbp_get_forum_id( $forum_id );

	/** Topic *****************************************************************/

	// Bail if topic is not published
	if ( !bbp_is_topic_published( $topic_id ) ) {
		return false;
	}

	/** Reply *****************************************************************/

	// Bail if reply is not published
	if ( !bbp_is_reply_published( $reply_id ) ) {
		return false;
	}

	// Poster name
	$reply_author_name = bbp_get_reply_author_display_name( $reply_id );

	/** Mail ******************************************************************/

	// Remove filters from reply content and topic title to prevent content
	// from being encoded with HTML entities, wrapped in paragraph tags, etc...
	remove_all_filters( 'bbp_get_reply_content' );
	remove_all_filters( 'bbp_get_topic_title'   );

	// Strip tags from text and setup mail data
	$topic_title   = strip_tags( bbp_get_topic_title( $topic_id ) );
	$reply_content = strip_tags( bbp_get_reply_content( $reply_id ) );
	$reply_url     = bbp_get_reply_url( $reply_id );
	$blog_name     = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	$do_not_reply  = '<noreply@' . ltrim( get_home_url(), '^(http|https)://' ) . '>';

	// For plugins to filter messages per reply/topic/user
	$message = sprintf( __( '%1$s wrote:

%2$s

Post Link: %3$s

-----------

You are receiving this email because you subscribed to a forum topic.

Login and visit the topic to unsubscribe from these emails.', 'bbpress' ),

		$reply_author_name,
		$reply_content,
		$reply_url
	);

	$message = apply_filters( 'bbp_subscription_mail_message', $message, $reply_id, $topic_id );
	if ( empty( $message ) ) {
		return;
	}

	// For plugins to filter titles per reply/topic/user
	$subject = apply_filters( 'bbp_subscription_mail_title', '[' . $blog_name . '] ' . $topic_title, $reply_id, $topic_id );
	if ( empty( $subject ) ) {
		return;
	}

	/** Users *****************************************************************/

	// Array to hold BCC's
	$headers = array();

	// Setup the From header
	$headers[] = 'From: ' . get_bloginfo( 'name' ) . ' ' . $do_not_reply;
	
	// Get topic subscribers and bail if empty
	$user_ids = bbp_get_topic_subscribers( $topic_id, true );
	if ( empty( $user_ids ) ) {
		return false;
	}

	// Loop through users
	foreach ( (array) $user_ids as $user_id ) {

		// Don't send notifications to the person who made the post
		if ( !empty( $reply_author ) && (int) $user_id === (int) $reply_author ) {
			continue;
		}

		// Get email address of subscribed user
		$headers[] = 'Bcc: ' . get_userdata( $user_id )->user_email;
	}

	/** Send it ***************************************************************/

	// Custom headers
	$headers = apply_filters( 'bbp_subscription_mail_headers', $headers );

	do_action( 'bbp_pre_notify_subscribers', $reply_id, $topic_id, $user_ids );

	// Send notification email
	wp_mail( $do_not_reply, $subject, $message, $headers );

	do_action( 'bbp_post_notify_subscribers', $reply_id, $topic_id, $user_ids );

	return true;
}

/**
 * Sends notification emails for new topics to subscribed forums
 *
 * Gets new post's ID and check if there are subscribed users to that forum, and
 * if there are, send notifications
 *
 * Note: in bbPress 2.6, we've moved away from 1 email per subscriber to 1 email
 * with everyone BCC'd. This may have negative repercussions for email services
 * that limit the number of addresses in a BCC field (often to around 500.) In
 * those cases, we recommend unhooking this function and creating your own
 * custom emailer script.
 * 
 * @since bbPress (r5156)
 *
 * @param int $topic_id ID of the newly made reply
 * @uses bbp_is_subscriptions_active() To check if the subscriptions are active
 * @uses bbp_get_topic_id() To validate the topic ID
 * @uses bbp_get_forum_id() To validate the forum ID
 * @uses bbp_is_topic_published() To make sure the topic is published
 * @uses bbp_get_forum_subscribers() To get the forum subscribers
 * @uses bbp_get_topic_author_display_name() To get the topic author's display name
 * @uses do_action() Calls 'bbp_pre_notify_forum_subscribers' with the topic id,
 *                    forum id and user id
 * @uses apply_filters() Calls 'bbp_forum_subscription_mail_message' with the
 *                    message, topic id, forum id and user id
 * @uses apply_filters() Calls 'bbp_forum_subscription_mail_title' with the
 *                    topic title, topic id, forum id and user id
 * @uses apply_filters() Calls 'bbp_forum_subscription_mail_headers'
 * @uses get_userdata() To get the user data
 * @uses wp_mail() To send the mail
 * @uses do_action() Calls 'bbp_post_notify_forum_subscribers' with the topic,
 *                    id, forum id and user id
 * @return bool True on success, false on failure
 */
function bbp_notify_forum_subscribers( $topic_id = 0, $forum_id = 0, $anonymous_data = false, $topic_author = 0 ) {

	// Bail if subscriptions are turned off
	if ( !bbp_is_subscriptions_active() ) {
		return false;
	}

	/** Validation ************************************************************/

	$topic_id = bbp_get_topic_id( $topic_id );
	$forum_id = bbp_get_forum_id( $forum_id );

	/** Topic *****************************************************************/

	// Bail if topic is not published
	if ( ! bbp_is_topic_published( $topic_id ) ) {
		return false;
	}

	// Poster name
	$topic_author_name = bbp_get_topic_author_display_name( $topic_id );

	/** Mail ******************************************************************/

	// Remove filters from reply content and topic title to prevent content
	// from being encoded with HTML entities, wrapped in paragraph tags, etc...
	remove_all_filters( 'bbp_get_topic_content' );
	remove_all_filters( 'bbp_get_topic_title'   );

	// Strip tags from text and setup mail data
	$topic_title   = strip_tags( bbp_get_topic_title( $topic_id ) );
	$topic_content = strip_tags( bbp_get_topic_content( $topic_id ) );
	$topic_url     = get_permalink( $topic_id );
	$blog_name     = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	$do_not_reply  = '<noreply@' . ltrim( get_home_url(), '^(http|https)://' ) . '>';

	// For plugins to filter messages per reply/topic/user
	$message = sprintf( __( '%1$s wrote:

%2$s

Topic Link: %3$s

-----------

You are receiving this email because you subscribed to a forum.

Login and visit the topic to unsubscribe from these emails.', 'bbpress' ),

		$topic_author_name,
		$topic_content,
		$topic_url
	);

	$message = apply_filters( 'bbp_forum_subscription_mail_message', $message, $topic_id, $forum_id, $user_id );
	if ( empty( $message ) ) {
		return;
	}

	// For plugins to filter titles per reply/topic/user
	$subject = apply_filters( 'bbp_forum_subscription_mail_title', '[' . $blog_name . '] ' . $topic_title, $topic_id, $forum_id, $user_id );
	if ( empty( $subject ) ) {
		return;
	}

	/** User ******************************************************************/

	// Array to hold BCC's
	$headers = array();

	// Setup the From header
	$headers[] = 'From: ' . get_bloginfo( 'name' ) . ' ' . $do_not_reply;
	
	// Get topic subscribers and bail if empty
	$user_ids = bbp_get_forum_subscribers( $forum_id, true );
	if ( empty( $user_ids ) ) {
		return false;
	}

	// Loop through users
	foreach ( (array) $user_ids as $user_id ) {

		// Don't send notifications to the person who made the post
		if ( !empty( $topic_author ) && (int) $user_id === (int) $topic_author ) {
			continue;
		}

		// Get email address of subscribed user
		$headers[] = 'Bcc: ' . get_userdata( $user_id )->user_email;
	}

	/** Send it ***************************************************************/

	// Custom headers
	$headers = apply_filters( 'bbp_subscription_mail_headers', $headers );

	do_action( 'bbp_pre_notify_forum_subscribers', $topic_id, $forum_id, $user_ids );

	// Send notification email
	wp_mail( $do_not_reply, $subject, $message, $headers );

	do_action( 'bbp_post_notify_forum_subscribers', $topic_id, $forum_id, $user_ids );

	return true;
}

/** Login *********************************************************************/

/**
 * Return a clean and reliable logout URL
 *
 * @param string $url URL
 * @param string $redirect_to Where to redirect to?
 * @uses add_query_arg() To add args to the url
 * @uses apply_filters() Calls 'bbp_logout_url' with the url and redirect to
 * @return string The url
 */
function bbp_logout_url( $url = '', $redirect_to = '' ) {

	// Make sure we are directing somewhere
	if ( empty( $redirect_to ) && !strstr( $url, 'redirect_to' ) ) {

		// Rejig the $redirect_to
		if ( !isset( $_SERVER['REDIRECT_URL'] ) || ( $redirect_to !== home_url( $_SERVER['REDIRECT_URL'] ) ) ) {
			$redirect_to = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';
		}

		$redirect_to = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		// Sanitize $redirect_to and add it to full $url
		$redirect_to = add_query_arg( array( 'loggedout'   => 'true'                    ), esc_url( $redirect_to ) );
		$url         = add_query_arg( array( 'redirect_to' => urlencode( $redirect_to ) ), $url                    );
	}

	// Filter and return
	return apply_filters( 'bbp_logout_url', $url, $redirect_to );
}

/** Queries *******************************************************************/

/**
 * Merge user defined arguments into defaults array.
 *
 * This function is used throughout bbPress to allow for either a string or array
 * to be merged into another array. It is identical to wp_parse_args() except
 * it allows for arguments to be passively or aggressively filtered using the
 * optional $filter_key parameter.
 *
 * @since bbPress (r3839)
 *
 * @param string|array $args Value to merge with $defaults
 * @param array $defaults Array that serves as the defaults.
 * @param string $filter_key String to key the filters from
 * @return array Merged user defined values with defaults.
 */
function bbp_parse_args( $args, $defaults = array(), $filter_key = '' ) {

	// Setup a temporary array from $args
	if ( is_object( $args ) ) {
		$r = get_object_vars( $args );
	} elseif ( is_array( $args ) ) {
		$r =& $args;
	} else {
		wp_parse_str( $args, $r );
	}

	// Passively filter the args before the parse
	if ( !empty( $filter_key ) ) {
		$r = apply_filters( 'bbp_before_' . $filter_key . '_parse_args', $r );
	}

	// Parse
	if ( is_array( $defaults ) && !empty( $defaults ) ) {
		$r = array_merge( $defaults, $r );
	}

	// Aggressively filter the args after the parse
	if ( !empty( $filter_key ) ) {
		$r = apply_filters( 'bbp_after_' . $filter_key . '_parse_args', $r );
	}

	// Return the parsed results
	return $r;
}

/**
 * Adds ability to include or exclude specific post_parent ID's
 *
 * @since bbPress (r2996)
 *
 * @global DB $wpdb
 * @global WP $wp
 * @param string $where
 * @param WP_Query $object
 * @return string
 */
function bbp_query_post_parent__in( $where, $object = '' ) {
	global $wpdb, $wp;

	// Noop if WP core supports this already
	if ( in_array( 'post_parent__in', $wp->private_query_vars ) )
		return $where;

	// Bail if no object passed
	if ( empty( $object ) )
		return $where;

	// Only 1 post_parent so return $where
	if ( is_numeric( $object->query_vars['post_parent'] ) )
		return $where;

	// Including specific post_parent's
	if ( ! empty( $object->query_vars['post_parent__in'] ) ) {
		$ids    = implode( ',', wp_parse_id_list( $object->query_vars['post_parent__in'] ) );
		$where .= " AND {$wpdb->posts}.post_parent IN ($ids)";

	// Excluding specific post_parent's
	} elseif ( ! empty( $object->query_vars['post_parent__not_in'] ) ) {
		$ids    = implode( ',', wp_parse_id_list( $object->query_vars['post_parent__not_in'] ) );
		$where .= " AND {$wpdb->posts}.post_parent NOT IN ($ids)";
	}

	// Return possibly modified $where
	return $where;
}

/**
 * Query the DB and get the last public post_id that has parent_id as post_parent
 *
 * @param int $parent_id Parent id
 * @param string $post_type Post type. Defaults to 'post'
 * @uses bbp_get_topic_post_type() To get the topic post type
 * @uses wp_cache_get() To check if there is a cache of the last child id
 * @uses wpdb::prepare() To prepare the query
 * @uses wpdb::get_var() To get the result of the query in a variable
 * @uses wp_cache_set() To set the cache for future use
 * @uses apply_filters() Calls 'bbp_get_public_child_last_id' with the child
 *                        id, parent id and post type
 * @return int The last active post_id
 */
function bbp_get_public_child_last_id( $parent_id = 0, $post_type = 'post' ) {
	global $wpdb;

	// Bail if nothing passed
	if ( empty( $parent_id ) )
		return false;

	// The ID of the cached query
	$cache_id = 'bbp_parent_' . $parent_id . '_type_' . $post_type . '_child_last_id';

	// Check for cache and set if needed
	$child_id = wp_cache_get( $cache_id, 'bbpress_posts' );
	if ( false === $child_id ) {
		$post_status = array( bbp_get_public_status_id() );

		// Add closed status if topic post type
		if ( $post_type === bbp_get_topic_post_type() ) {
			$post_status[] = bbp_get_closed_status_id();
		}

		// Join post statuses together
		$post_status = "'" . implode( "', '", $post_status ) . "'";

		$child_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_parent = %d AND post_status IN ( {$post_status} ) AND post_type = '%s' ORDER BY ID DESC LIMIT 1;", $parent_id, $post_type ) );
		wp_cache_set( $cache_id, $child_id, 'bbpress_posts' );
	}

	// Filter and return
	return apply_filters( 'bbp_get_public_child_last_id', (int) $child_id, $parent_id, $post_type );
}

/**
 * Query the DB and get a count of public children
 *
 * @param int $parent_id Parent id
 * @param string $post_type Post type. Defaults to 'post'
 * @uses bbp_get_topic_post_type() To get the topic post type
 * @uses wp_cache_get() To check if there is a cache of the children count
 * @uses wpdb::prepare() To prepare the query
 * @uses wpdb::get_var() To get the result of the query in a variable
 * @uses wp_cache_set() To set the cache for future use
 * @uses apply_filters() Calls 'bbp_get_public_child_count' with the child
 *                        count, parent id and post type
 * @return int The number of children
 */
function bbp_get_public_child_count( $parent_id = 0, $post_type = 'post' ) {
	global $wpdb;

	// Bail if nothing passed
	if ( empty( $parent_id ) )
		return false;

	// The ID of the cached query
	$cache_id    = 'bbp_parent_' . $parent_id . '_type_' . $post_type . '_child_count';

	// Check for cache and set if needed
	$child_count = wp_cache_get( $cache_id, 'bbpress_posts' );
	if ( false === $child_count ) {
		$post_status = array( bbp_get_public_status_id() );

		// Add closed status if topic post type
		if ( $post_type === bbp_get_topic_post_type() ) {
			$post_status[] = bbp_get_closed_status_id();
		}

		// Join post statuses together
		$post_status = "'" . implode( "', '", $post_status ) . "'";

		$child_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_parent = %d AND post_status IN ( {$post_status} ) AND post_type = '%s';", $parent_id, $post_type ) );
		wp_cache_set( $cache_id, $child_count, 'bbpress_posts' );
	}

	// Filter and return
	return apply_filters( 'bbp_get_public_child_count', (int) $child_count, $parent_id, $post_type );
}

/**
 * Query the DB and get a the child id's of public children
 *
 * @param int $parent_id Parent id
 * @param string $post_type Post type. Defaults to 'post'
 * @uses bbp_get_topic_post_type() To get the topic post type
 * @uses wp_cache_get() To check if there is a cache of the children
 * @uses wpdb::prepare() To prepare the query
 * @uses wpdb::get_col() To get the result of the query in an array
 * @uses wp_cache_set() To set the cache for future use
 * @uses apply_filters() Calls 'bbp_get_public_child_ids' with the child ids,
 *                        parent id and post type
 * @return array The array of children
 */
function bbp_get_public_child_ids( $parent_id = 0, $post_type = 'post' ) {
	global $wpdb;

	// Bail if nothing passed
	if ( empty( $parent_id ) )
		return false;

	// The ID of the cached query
	$cache_id  = 'bbp_parent_public_' . $parent_id . '_type_' . $post_type . '_child_ids';

	// Check for cache and set if needed
	$child_ids = wp_cache_get( $cache_id, 'bbpress_posts' );
	if ( false === $child_ids ) {
		$post_status = array( bbp_get_public_status_id() );

		// Add closed status if topic post type
		if ( $post_type === bbp_get_topic_post_type() ) {
			$post_status[] = bbp_get_closed_status_id();
		}

		// Join post statuses together
		$post_status = "'" . implode( "', '", $post_status ) . "'";

		$child_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_parent = %d AND post_status IN ( {$post_status} ) AND post_type = '%s' ORDER BY ID DESC;", $parent_id, $post_type ) );
		wp_cache_set( $cache_id, $child_ids, 'bbpress_posts' );
	}

	// Filter and return
	return apply_filters( 'bbp_get_public_child_ids', $child_ids, $parent_id, $post_type );
}

/**
 * Query the DB and get a the child id's of all children
 *
 * @param int $parent_id Parent id
 * @param string $post_type Post type. Defaults to 'post'
 * @uses bbp_get_topic_post_type() To get the topic post type
 * @uses wp_cache_get() To check if there is a cache of the children
 * @uses wpdb::prepare() To prepare the query
 * @uses wpdb::get_col() To get the result of the query in an array
 * @uses wp_cache_set() To set the cache for future use
 * @uses apply_filters() Calls 'bbp_get_public_child_ids' with the child ids,
 *                        parent id and post type
 * @return array The array of children
 */
function bbp_get_all_child_ids( $parent_id = 0, $post_type = 'post' ) {
	global $wpdb;

	// Bail if nothing passed
	if ( empty( $parent_id ) )
		return false;

	// The ID of the cached query
	$cache_id  = 'bbp_parent_all_' . $parent_id . '_type_' . $post_type . '_child_ids';

	// Check for cache and set if needed
	$child_ids = wp_cache_get( $cache_id, 'bbpress_posts' );
	if ( false === $child_ids ) {
		$post_status = array( bbp_get_public_status_id() );

		// Extra post statuses based on post type
		switch ( $post_type ) {

			// Forum
			case bbp_get_forum_post_type() :
				$post_status[] = bbp_get_private_status_id();
				$post_status[] = bbp_get_hidden_status_id();
				break;

			// Topic
			case bbp_get_topic_post_type() :
				$post_status[] = bbp_get_closed_status_id();
				$post_status[] = bbp_get_trash_status_id();
				$post_status[] = bbp_get_spam_status_id();
				break;

			// Reply
			case bbp_get_reply_post_type() :
				$post_status[] = bbp_get_trash_status_id();
				$post_status[] = bbp_get_spam_status_id();
				break;
		}

		// Join post statuses together
		$post_status = "'" . implode( "', '", $post_status ) . "'";

		$child_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_parent = %d AND post_status IN ( {$post_status} ) AND post_type = '%s' ORDER BY ID DESC;", $parent_id, $post_type ) );
		wp_cache_set( $cache_id, $child_ids, 'bbpress_posts' );
	}

	// Filter and return
	return apply_filters( 'bbp_get_all_child_ids', $child_ids, (int) $parent_id, $post_type );
}

/** Globals *******************************************************************/

/**
 * Get the unfiltered value of a global $post's key
 *
 * Used most frequently when editing a forum/topic/reply
 *
 * @since bbPress (r3694)
 *
 * @global WP_Query $post
 * @param string $field Name of the key
 * @param string $context How to sanitize - raw|edit|db|display|attribute|js
 * @return string Field value
 */
function bbp_get_global_post_field( $field = 'ID', $context = 'edit' ) {
	global $post;

	$retval = isset( $post->$field ) ? $post->$field : '';
	$retval = sanitize_post_field( $field, $retval, $post->ID, $context );

	return apply_filters( 'bbp_get_global_post_field', $retval, $post );
}

/** Nonces ********************************************************************/

/**
 * Makes sure the user requested an action from another page on this site.
 *
 * To avoid security exploits within the theme.
 *
 * @since bbPress (r4022)
 *
 * @uses do_action() Calls 'bbp_check_referer' on $action.
 * @param string $action Action nonce
 * @param string $query_arg where to look for nonce in $_REQUEST
 */
function bbp_verify_nonce_request( $action = '', $query_arg = '_wpnonce' ) {

	/** Home URL **************************************************************/

	// Parse home_url() into pieces to remove query-strings, strange characters,
	// and other funny things that plugins might to do to it.
	$parsed_home = parse_url( home_url( '/', ( is_ssl() ? 'https' : 'http' ) ) );

	// Maybe include the port, if it's included
	if ( isset( $parsed_home['port'] ) ) {
		$parsed_host = $parsed_home['host'] . ':' . $parsed_home['port'];
	} else {
		$parsed_host = $parsed_home['host'];
	}

	// Set the home URL for use in comparisons
	$home_url = trim( strtolower( $parsed_home['scheme'] . '://' . $parsed_host . $parsed_home['path'] ), '/' );

	/** Requested URL *********************************************************/

	// Maybe include the port, if it's included in home_url()
	if ( isset( $parsed_home['port'] ) ) {
		$request_host = $_SERVER['HTTP_HOST'] . ':' . $_SERVER['SERVER_PORT'];
	} else {
		$request_host = $_SERVER['HTTP_HOST'];
	}

	// Build the currently requested URL
	$scheme        = is_ssl() ? 'https://' : 'http://';
	$requested_url = strtolower( $scheme . $request_host . $_SERVER['REQUEST_URI'] );

	/** Look for match ********************************************************/

	// Filter the requested URL, for configurations like reverse proxying
	$matched_url = apply_filters( 'bbp_verify_nonce_request_url', $requested_url );

	// Check the nonce
	$result = isset( $_REQUEST[$query_arg] ) ? wp_verify_nonce( $_REQUEST[$query_arg], $action ) : false;

	// Nonce check failed
	if ( empty( $result ) || empty( $action ) || ( strpos( $matched_url, $home_url ) !== 0 ) ) {
		$result = false;
	}

	// Do extra things
	do_action( 'bbp_verify_nonce_request', $action, $result );

	return $result;
}

/** Feeds *********************************************************************/

/**
 * This function is hooked into the WordPress 'request' action and is
 * responsible for sniffing out the query vars and serving up RSS2 feeds if
 * the stars align and the user has requested a feed of any bbPress type.
 *
 * @since bbPress (r3171)
 *
 * @param array $query_vars
 * @return array
 */
function bbp_request_feed_trap( $query_vars = array() ) {

	// Looking at a feed
	if ( isset( $query_vars['feed'] ) ) {

		// Forum/Topic/Reply Feed
		if ( isset( $query_vars['post_type'] ) ) {

			// Matched post type
			$post_type = false;

			// Post types to check
			$post_types = array(
				bbp_get_forum_post_type(),
				bbp_get_topic_post_type(),
				bbp_get_reply_post_type()
			);

			// Cast query vars as array outside of foreach loop
			$qv_array = (array) $query_vars['post_type'];

			// Check if this query is for a bbPress post type
			foreach ( $post_types as $bbp_pt ) {
			    if ( in_array( $bbp_pt, $qv_array, true ) ) {
				    $post_type = $bbp_pt;
				    break;
			    }
			}

			// Looking at a bbPress post type
			if ( ! empty( $post_type ) ) {

				// Supported select query vars
				$select_query_vars = array(
					'p'        => false,
					'name'     => false,
					$post_type => false,
				);

				// Setup matched variables to select
				foreach ( $query_vars as $key => $value ) {
					if ( isset( $select_query_vars[$key] ) ) {
						$select_query_vars[$key] = $value;
					}
				}

				// Remove any empties
				$select_query_vars = array_filter( $select_query_vars );

				// What bbPress post type are we looking for feeds on?
				switch ( $post_type ) {

					// Forum
					case bbp_get_forum_post_type() :

						// Define local variable(s)
						$meta_query = array();

						// Single forum
						if ( !empty( $select_query_vars ) ) {

							// Load up our own query
							query_posts( array_merge( array(
								'post_type' => bbp_get_forum_post_type(),
								'feed'      => true
							), $select_query_vars ) );

							// Restrict to specific forum ID
							$meta_query = array( array(
								'key'     => '_bbp_forum_id',
								'value'   => bbp_get_forum_id(),
								'type'    => 'numeric',
								'compare' => '='
							) );
						}

						// Only forum replies
						if ( !empty( $_GET['type'] ) && ( bbp_get_reply_post_type() === $_GET['type'] ) ) {

							// The query
							$the_query = array(
								'author'         => 0,
								'feed'           => true,
								'post_type'      => bbp_get_reply_post_type(),
								'post_parent'    => 'any',
								'post_status'    => array( bbp_get_public_status_id(), bbp_get_closed_status_id() ),
								'posts_per_page' => bbp_get_replies_per_rss_page(),
								'order'          => 'DESC',
								'meta_query'     => $meta_query
							);

							// Output the feed
							bbp_display_replies_feed_rss2( $the_query );

						// Only forum topics
						} elseif ( !empty( $_GET['type'] ) && ( bbp_get_topic_post_type() === $_GET['type'] ) ) {

							// The query
							$the_query = array(
								'author'         => 0,
								'feed'           => true,
								'post_type'      => bbp_get_topic_post_type(),
								'post_parent'    => bbp_get_forum_id(),
								'post_status'    => array( bbp_get_public_status_id(), bbp_get_closed_status_id() ),
								'posts_per_page' => bbp_get_topics_per_rss_page(),
								'order'          => 'DESC'
							);

							// Output the feed
							bbp_display_topics_feed_rss2( $the_query );

						// All forum topics and replies
						} else {

							// Exclude private/hidden forums if not looking at single
							if ( empty( $select_query_vars ) )
								$meta_query = array( bbp_exclude_forum_ids( 'meta_query' ) );

							// The query
							$the_query = array(
								'author'         => 0,
								'feed'           => true,
								'post_type'      => array( bbp_get_reply_post_type(), bbp_get_topic_post_type() ),
								'post_parent'    => 'any',
								'post_status'    => array( bbp_get_public_status_id(), bbp_get_closed_status_id() ),
								'posts_per_page' => bbp_get_replies_per_rss_page(),
								'order'          => 'DESC',
								'meta_query'     => $meta_query
							);

							// Output the feed
							bbp_display_replies_feed_rss2( $the_query );
						}

						break;

					// Topic feed - Show replies
					case bbp_get_topic_post_type() :

						// Single topic
						if ( !empty( $select_query_vars ) ) {

							// Load up our own query
							query_posts( array_merge( array(
								'post_type' => bbp_get_topic_post_type(),
								'feed'      => true
							), $select_query_vars ) );

							// Output the feed
							bbp_display_replies_feed_rss2( array( 'feed' => true ) );

						// All topics
						} else {

							// The query
							$the_query = array(
								'author'         => 0,
								'feed'           => true,
								'post_parent'    => 'any',
								'posts_per_page' => bbp_get_topics_per_rss_page(),
								'show_stickies'  => false
							);

							// Output the feed
							bbp_display_topics_feed_rss2( $the_query );
						}

						break;

					// Replies
					case bbp_get_reply_post_type() :

						// The query
						$the_query = array(
							'posts_per_page' => bbp_get_replies_per_rss_page(),
							'meta_query'     => array( array( ) ),
							'feed'           => true
						);

						// All replies
						if ( empty( $select_query_vars ) ) {
							bbp_display_replies_feed_rss2( $the_query );
						}

						break;
				}
			}

		// Single Topic Vview
		} elseif ( isset( $query_vars[ bbp_get_view_rewrite_id() ] ) ) {

			// Get the view
			$view = $query_vars[ bbp_get_view_rewrite_id() ];

			// We have a view to display a feed
			if ( !empty( $view ) ) {

				// Get the view query
				$the_query = bbp_get_view_query_args( $view );

				// Output the feed
				bbp_display_topics_feed_rss2( $the_query );
			}
		}

		// @todo User profile feeds
	}

	// No feed so continue on
	return $query_vars;
}

/** Templates ******************************************************************/

/**
 * Used to guess if page exists at requested path
 *
 * @since bbPress (r3304)
 *
 * @uses get_option() To see if pretty permalinks are enabled
 * @uses get_page_by_path() To see if page exists at path
 *
 * @param string $path
 * @return mixed False if no page, Page object if true
 */
function bbp_get_page_by_path( $path = '' ) {

	// Default to false
	$retval = false;

	// Path is not empty
	if ( !empty( $path ) ) {

		// Pretty permalinks are on so path might exist
		if ( get_option( 'permalink_structure' ) ) {
			$retval = get_page_by_path( $path );
		}
	}

	return apply_filters( 'bbp_get_page_by_path', $retval, $path );
}

/**
 * Sets the 404 status.
 *
 * Used primarily with topics/replies inside hidden forums.
 *
 * @since bbPress (r3051)
 *
 * @global WP_Query $wp_query
 * @uses WP_Query::set_404()
 */
function bbp_set_404() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.', 'bbpress' ), '3.1' );
		return false;
	}

	$wp_query->set_404();
}
