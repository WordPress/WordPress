<?php

/**
 * bbPress Admin Tools Page
 *
 * @package bbPress
 * @subpackage Administration
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/** Repair ********************************************************************/

/**
 * Admin repair page
 *
 * @since bbPress (r2613)
 *
 * @uses bbp_admin_repair_list() To get the recount list
 * @uses check_admin_referer() To verify the nonce and the referer
 * @uses wp_cache_flush() To flush the cache
 * @uses do_action() Calls 'admin_notices' to display the notices
 * @uses screen_icon() To display the screen icon
 * @uses wp_nonce_field() To add a hidden nonce field
 */
function bbp_admin_repair() {
?>

	<div class="wrap">

		<?php screen_icon( 'tools' ); ?>

		<h2 class="nav-tab-wrapper"><?php bbp_tools_admin_tabs( __( 'Repair Forums', 'bbpress' ) ); ?></h2>

		<p><?php esc_html_e( 'bbPress keeps track of relationships between forums, topics, replies, and topic tags, and users. Occasionally these relationships become out of sync, most often after an import or migration. Use the tools below to manually recalculate these relationships.', 'bbpress' ); ?></p>
		<p class="description"><?php esc_html_e( 'Some of these tools create substantial database overhead. Avoid running more than 1 repair job at a time.', 'bbpress' ); ?></p>

		<form class="settings" method="post" action="">
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Relationships to Repair:', 'bbpress' ) ?></th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><span><?php esc_html_e( 'Repair', 'bbpress' ) ?></span></legend>

								<?php foreach ( bbp_admin_repair_list() as $item ) : ?>

									<label><input type="checkbox" class="checkbox" name="<?php echo esc_attr( $item[0] ) . '" id="' . esc_attr( str_replace( '_', '-', $item[0] ) ); ?>" value="1" /> <?php echo esc_html( $item[1] ); ?></label><br />

								<?php endforeach; ?>

							</fieldset>
						</td>
					</tr>
				</tbody>
			</table>

			<fieldset class="submit">
				<input class="button-primary" type="submit" name="submit" value="<?php esc_attr_e( 'Repair Items', 'bbpress' ); ?>" />
				<?php wp_nonce_field( 'bbpress-do-counts' ); ?>
			</fieldset>
		</form>
	</div>

<?php
}

/**
 * Handle the processing and feedback of the admin tools page
 *
 * @since bbPress (r2613)
 *
 * @uses bbp_admin_repair_list() To get the recount list
 * @uses check_admin_referer() To verify the nonce and the referer
 * @uses wp_cache_flush() To flush the cache
 * @uses do_action() Calls 'admin_notices' to display the notices
 */
function bbp_admin_repair_handler() {

	if ( ! bbp_is_post_request() )
		return;

	check_admin_referer( 'bbpress-do-counts' );

	// Stores messages
	$messages = array();

	wp_cache_flush();

	foreach ( (array) bbp_admin_repair_list() as $item ) {
		if ( isset( $item[2] ) && isset( $_POST[$item[0]] ) && 1 === absint( $_POST[$item[0]] ) && is_callable( $item[2] ) ) {
			$messages[] = call_user_func( $item[2] );
		}
	}

	if ( count( $messages ) ) {
		foreach ( $messages as $message ) {
			bbp_admin_tools_feedback( $message[1] );
		}
	}
}

/**
 * Assemble the admin notices
 *
 * @since bbPress (r2613)
 *
 * @param string|WP_Error $message A message to be displayed or {@link WP_Error}
 * @param string $class Optional. A class to be added to the message div
 * @uses WP_Error::get_error_messages() To get the error messages of $message
 * @uses add_action() Adds the admin notice action with the message HTML
 * @return string The message HTML
 */
function bbp_admin_tools_feedback( $message, $class = false ) {
	if ( is_string( $message ) ) {
		$message = '<p>' . $message . '</p>';
		$class = $class ? $class : 'updated';
	} elseif ( is_wp_error( $message ) ) {
		$errors = $message->get_error_messages();

		switch ( count( $errors ) ) {
			case 0:
				return false;
				break;

			case 1:
				$message = '<p>' . $errors[0] . '</p>';
				break;

			default:
				$message = '<ul>' . "\n\t" . '<li>' . implode( '</li>' . "\n\t" . '<li>', $errors ) . '</li>' . "\n" . '</ul>';
				break;
		}

		$class = $class ? $class : 'error';
	} else {
		return false;
	}

	$message = '<div id="message" class="' . esc_attr( $class ) . '">' . $message . '</div>';
	$message = str_replace( "'", "\'", $message );
	$lambda  = create_function( '', "echo '$message';" );

	add_action( 'admin_notices', $lambda );

	return $lambda;
}

/**
 * Get the array of the repair list
 *
 * @since bbPress (r2613)
 *
 * @uses apply_filters() Calls 'bbp_repair_list' with the list array
 * @return array Repair list of options
 */
function bbp_admin_repair_list() {
	$repair_list = array(
		0  => array( 'bbp-sync-topic-meta',          __( 'Recalculate the parent topic for each post',        'bbpress' ), 'bbp_admin_repair_topic_meta'               ),
		5  => array( 'bbp-sync-forum-meta',          __( 'Recalculate the parent forum for each post',        'bbpress' ), 'bbp_admin_repair_forum_meta'               ),
		10 => array( 'bbp-sync-forum-visibility',    __( 'Recalculate private and hidden forums',             'bbpress' ), 'bbp_admin_repair_forum_visibility'         ),
		15 => array( 'bbp-sync-all-topics-forums',   __( 'Recalculate last activity in each topic and forum', 'bbpress' ), 'bbp_admin_repair_freshness'                ),
		20 => array( 'bbp-sync-all-topics-sticky',   __( 'Recalculate the sticky relationship of each topic', 'bbpress' ), 'bbp_admin_repair_sticky'                   ),
		25 => array( 'bbp-sync-all-reply-positions', __( 'Recalculate the position of each reply',            'bbpress' ), 'bbp_admin_repair_reply_menu_order'         ),
		30 => array( 'bbp-group-forums',             __( 'Repair BuddyPress Group Forum relationships',       'bbpress' ), 'bbp_admin_repair_group_forum_relationship' ),
		35 => array( 'bbp-forum-topics',             __( 'Count topics in each forum',                        'bbpress' ), 'bbp_admin_repair_forum_topic_count'        ),
		40 => array( 'bbp-forum-replies',            __( 'Count replies in each forum',                       'bbpress' ), 'bbp_admin_repair_forum_reply_count'        ),
		45 => array( 'bbp-topic-replies',            __( 'Count replies in each topic',                       'bbpress' ), 'bbp_admin_repair_topic_reply_count'        ),
		50 => array( 'bbp-topic-voices',             __( 'Count voices in each topic',                        'bbpress' ), 'bbp_admin_repair_topic_voice_count'        ),
		55 => array( 'bbp-topic-hidden-replies',     __( 'Count spammed & trashed replies in each topic',     'bbpress' ), 'bbp_admin_repair_topic_hidden_reply_count' ),
		60 => array( 'bbp-user-topics',              __( 'Count topics for each user',                        'bbpress' ), 'bbp_admin_repair_user_topic_count'         ),
		65 => array( 'bbp-user-replies',             __( 'Count replies for each user',                       'bbpress' ), 'bbp_admin_repair_user_reply_count'         ),
		70 => array( 'bbp-user-favorites',           __( 'Remove trashed topics from user favorites',         'bbpress' ), 'bbp_admin_repair_user_favorites'           ),
		75 => array( 'bbp-user-topic-subscriptions', __( 'Remove trashed topics from user subscriptions',     'bbpress' ), 'bbp_admin_repair_user_topic_subscriptions' ),
		80 => array( 'bbp-user-forum-subscriptions', __( 'Remove trashed forums from user subscriptions',     'bbpress' ), 'bbp_admin_repair_user_forum_subscriptions' ),
		85 => array( 'bbp-user-role-map',            __( 'Remap existing users to default forum roles',       'bbpress' ), 'bbp_admin_repair_user_roles'               )
	);
	ksort( $repair_list );

	return (array) apply_filters( 'bbp_repair_list', $repair_list );
}

/**
 * Recount topic replies
 *
 * @since bbPress (r2613)
 *
 * @uses bbp_get_reply_post_type() To get the reply post type
 * @uses wpdb::query() To run our recount sql queries
 * @uses is_wp_error() To check if the executed query returned {@link WP_Error}
 * @return array An array of the status code and the message
 */
function bbp_admin_repair_topic_reply_count() {
	global $wpdb;

	$statement = __( 'Counting the number of replies in each topic&hellip; %s', 'bbpress' );
	$result    = __( 'Failed!', 'bbpress' );

	// Post types and status
	$tpt = bbp_get_topic_post_type();
	$rpt = bbp_get_reply_post_type();
	$pps = bbp_get_public_status_id();
	$cps = bbp_get_closed_status_id();

	// Delete the meta key _bbp_reply_count for each topic
	$sql_delete = "DELETE `postmeta` FROM `{$wpdb->postmeta}` AS `postmeta`
						LEFT JOIN `{$wpdb->posts}` AS `posts` ON `posts`.`ID` = `postmeta`.`post_id`
						WHERE `posts`.`post_type` = '{$tpt}'
						AND `postmeta`.`meta_key` = '_bbp_reply_count'";

	if ( is_wp_error( $wpdb->query( $sql_delete ) ) ) {
		return array( 1, sprintf( $statement, $result ) );
	}

	// Recalculate the meta key _bbp_reply_count for each topic
	$sql = "INSERT INTO `{$wpdb->postmeta}` (`post_id`, `meta_key`, `meta_value`) (
			SELECT `topics`.`ID` AS `post_id`, '_bbp_reply_count' AS `meta_key`, COUNT(`replies`.`ID`) As `meta_value`
				FROM `{$wpdb->posts}` AS `topics`
					LEFT JOIN `{$wpdb->posts}` as `replies`
						ON  `replies`.`post_parent` = `topics`.`ID`
						AND `replies`.`post_status` = '{$pps}'
						AND `replies`.`post_type`   = '{$rpt}'
				WHERE `topics`.`post_type` = '{$tpt}'
					AND `topics`.`post_status` IN ( '{$pps}', '{$cps}' )
				GROUP BY `topics`.`ID`);";

	if ( is_wp_error( $wpdb->query( $sql ) ) ) {
		return array( 2, sprintf( $statement, $result ) );
	}

	return array( 0, sprintf( $statement, __( 'Complete!', 'bbpress' ) ) );
}

/**
 * Recount topic voices
 *
 * @since bbPress (r2613)
 *
 * @uses bbp_get_reply_post_type() To get the reply post type
 * @uses wpdb::query() To run our recount sql queries
 * @uses is_wp_error() To check if the executed query returned {@link WP_Error}
 * @return array An array of the status code and the message
 */
function bbp_admin_repair_topic_voice_count() {
	global $wpdb;

	$statement = __( 'Counting the number of voices in each topic&hellip; %s', 'bbpress' );
	$result    = __( 'Failed!', 'bbpress' );

	$sql_delete = "DELETE FROM `{$wpdb->postmeta}` WHERE `meta_key` = '_bbp_voice_count';";
	if ( is_wp_error( $wpdb->query( $sql_delete ) ) )
		return array( 1, sprintf( $statement, $result ) );

	// Post types and status
	$tpt = bbp_get_topic_post_type();
	$rpt = bbp_get_reply_post_type();
	$pps = bbp_get_public_status_id();
	$cps = bbp_get_closed_status_id();

	$sql = "INSERT INTO `{$wpdb->postmeta}` (`post_id`, `meta_key`, `meta_value`) (
			SELECT `postmeta`.`meta_value`, '_bbp_voice_count', COUNT(DISTINCT `post_author`) as `meta_value`
				FROM `{$wpdb->posts}` AS `posts`
				LEFT JOIN `{$wpdb->postmeta}` AS `postmeta`
					ON `posts`.`ID` = `postmeta`.`post_id`
					AND `postmeta`.`meta_key` = '_bbp_topic_id'
				WHERE `posts`.`post_type` IN ( '{$tpt}', '{$rpt}' )
					AND `posts`.`post_status` IN ( '{$pps}', '{$cps}' )
					AND `posts`.`post_author` != '0'
				GROUP BY `postmeta`.`meta_value`);";

	if ( is_wp_error( $wpdb->query( $sql ) ) )
		return array( 2, sprintf( $statement, $result ) );

	return array( 0, sprintf( $statement, __( 'Complete!', 'bbpress' ) ) );
}

/**
 * Recount topic hidden replies (spammed/trashed)
 *
 * @since bbPress (r2747)
 *
 * @uses wpdb::query() To run our recount sql queries
 * @uses is_wp_error() To check if the executed query returned {@link WP_Error}
 * @return array An array of the status code and the message
 */
function bbp_admin_repair_topic_hidden_reply_count() {
	global $wpdb;

	$statement = __( 'Counting the number of spammed and trashed replies in each topic&hellip; %s', 'bbpress' );
	$result    = __( 'Failed!', 'bbpress' );

	$sql_delete = "DELETE FROM `{$wpdb->postmeta}` WHERE `meta_key` = '_bbp_reply_count_hidden';";
	if ( is_wp_error( $wpdb->query( $sql_delete ) ) )
		return array( 1, sprintf( $statement, $result ) );

	$sql = "INSERT INTO `{$wpdb->postmeta}` (`post_id`, `meta_key`, `meta_value`) (SELECT `post_parent`, '_bbp_reply_count_hidden', COUNT(`post_status`) as `meta_value` FROM `{$wpdb->posts}` WHERE `post_type` = '" . bbp_get_reply_post_type() . "' AND `post_status` IN ( '" . implode( "','", array( bbp_get_trash_status_id(), bbp_get_spam_status_id() ) ) . "') GROUP BY `post_parent`);";
	if ( is_wp_error( $wpdb->query( $sql ) ) )
		return array( 2, sprintf( $statement, $result ) );

	return array( 0, sprintf( $statement, __( 'Complete!', 'bbpress' ) ) );
}

/**
 * Repair group forum ID mappings after a bbPress 1.1 to bbPress 2.2 conversion
 *
 * @since bbPress (r4395)
 *
 * @global WPDB $wpdb
 * @return If a wp_error() occurs and no converted forums are found
 */
function bbp_admin_repair_group_forum_relationship() {
	global $wpdb;

	$statement = __( 'Repairing BuddyPress group-forum relationships&hellip; %s', 'bbpress' );
	$g_count     = 0;
	$f_count     = 0;
	$s_count     = 0;

	// Copy the BuddyPress filter here, incase BuddyPress is not active
	$prefix            = apply_filters( 'bp_core_get_table_prefix', $wpdb->base_prefix );
	$groups_table      = $prefix . 'bp_groups';
	$groups_meta_table = $prefix . 'bp_groups_groupmeta';

	// Get the converted forum IDs
	$forum_ids = $wpdb->query( "SELECT `forum`.`ID`, `forummeta`.`meta_value`
								FROM `{$wpdb->posts}` AS `forum`
									LEFT JOIN `{$wpdb->postmeta}` AS `forummeta`
										ON `forum`.`ID` = `forummeta`.`post_id`
										AND `forummeta`.`meta_key` = '_bbp_old_forum_id'
								WHERE `forum`.`post_type` = 'forum'
								GROUP BY `forum`.`ID`;" );

	// Bail if forum IDs returned an error
	if ( is_wp_error( $forum_ids ) || empty( $wpdb->last_result ) )
		return array( 2, sprintf( $statement, __( 'Failed!', 'bbpress' ) ) );

	// Stash the last results
	$results = $wpdb->last_result;

	// Update each group forum
	foreach ( $results as $group_forums ) {

		// Only update if is a converted forum
		if ( ! isset( $group_forums->meta_value ) )
			continue;

		// Attempt to update group meta
		$updated = $wpdb->query( "UPDATE `{$groups_meta_table}` SET `meta_value` = '{$group_forums->ID}' WHERE `meta_key` = 'forum_id' AND `meta_value` = '{$group_forums->meta_value}';" );

		// Bump the count
		if ( !empty( $updated ) && ! is_wp_error( $updated ) ) {
			++$g_count;
		}

		// Update group to forum relationship data
		$group_id = (int) $wpdb->get_var( "SELECT `group_id` FROM `{$groups_meta_table}` WHERE `meta_key` = 'forum_id' AND `meta_value` = '{$group_forums->ID}';" );
		if ( !empty( $group_id ) ) {

			// Update the group to forum meta connection in forums
			update_post_meta( $group_forums->ID, '_bbp_group_ids', array( $group_id ) );

			// Get the group status
			$group_status = $wpdb->get_var( "SELECT `status` FROM `{$groups_table}` WHERE `id` = '{$group_id}';" );

			// Sync up forum visibility based on group status
			switch ( $group_status ) {

				// Public groups have public forums
				case 'public' :
					bbp_publicize_forum( $group_forums->ID );

					// Bump the count for output later
					++$s_count;
					break;

				// Private/hidden groups have hidden forums
				case 'private' :
				case 'hidden'  :
					bbp_hide_forum( $group_forums->ID );

					// Bump the count for output later
					++$s_count;
					break;
			}

			// Bump the count for output later
			++$f_count;
		}
	}

	// Make some logical guesses at the old group root forum
	if ( function_exists( 'bp_forums_parent_forum_id' ) ) {
		$old_default_forum_id = bp_forums_parent_forum_id();
	} elseif ( defined( 'BP_FORUMS_PARENT_FORUM_ID' ) ) {
		$old_default_forum_id = (int) BP_FORUMS_PARENT_FORUM_ID;
	} else {
		$old_default_forum_id = 1;
	}

	// Try to get the group root forum
	$posts = get_posts( array(
		'post_type'   => bbp_get_forum_post_type(),
		'meta_key'    => '_bbp_old_forum_id',
		'meta_value'  => $old_default_forum_id,
		'numberposts' => 1
	) );

	// Found the group root forum
	if ( ! empty( $posts ) ) {

		// Rename 'Default Forum'  since it's now visible in sitewide forums
		if ( 'Default Forum' === $posts[0]->post_title ) {
			wp_update_post( array(
				'ID'         => $posts[0]->ID,
				'post_title' => __( 'Group Forums', 'bbpress' ),
				'post_name'  => __( 'group-forums', 'bbpress' ),
			) );
		}

		// Update the group forums root metadata
		update_option( '_bbp_group_forums_root_id', $posts[0]->ID );
	}

	// Remove old bbPress 1.1 roles (BuddyPress)
	remove_role( 'member'    );
	remove_role( 'inactive'  );
	remove_role( 'blocked'   );
	remove_role( 'moderator' );
	remove_role( 'keymaster' );

	// Complete results
	$result = sprintf( __( 'Complete! %s groups updated; %s forums updated; %s forum statuses synced.', 'bbpress' ), bbp_number_format( $g_count ), bbp_number_format( $f_count ), bbp_number_format( $s_count ) );
	return array( 0, sprintf( $statement, $result ) );
}

/**
 * Recount forum topics
 *
 * @since bbPress (r2613)
 *
 * @uses wpdb::query() To run our recount sql queries
 * @uses is_wp_error() To check if the executed query returned {@link WP_Error}
 * @uses bbp_get_forum_post_type() To get the forum post type
 * @uses get_posts() To get the forums
 * @uses bbp_update_forum_topic_count() To update the forum topic count
 * @return array An array of the status code and the message
 */
function bbp_admin_repair_forum_topic_count() {
	global $wpdb;

	$statement = __( 'Counting the number of topics in each forum&hellip; %s', 'bbpress' );
	$result    = __( 'Failed!', 'bbpress' );

	$sql_delete = "DELETE FROM {$wpdb->postmeta} WHERE meta_key IN ( '_bbp_topic_count', '_bbp_total_topic_count' );";
	if ( is_wp_error( $wpdb->query( $sql_delete ) ) )
		return array( 1, sprintf( $statement, $result ) );

	$forums = get_posts( array( 'post_type' => bbp_get_forum_post_type(), 'numberposts' => -1 ) );
	if ( !empty( $forums ) ) {
		foreach ( $forums as $forum ) {
			bbp_update_forum_topic_count( $forum->ID );
		}
	} else {
		return array( 2, sprintf( $statement, $result ) );
	}

	return array( 0, sprintf( $statement, __( 'Complete!', 'bbpress' ) ) );
}

/**
 * Recount forum replies
 *
 * @since bbPress (r2613)
 *
 * @uses wpdb::query() To run our recount sql queries
 * @uses is_wp_error() To check if the executed query returned {@link WP_Error}
 * @uses bbp_get_forum_post_type() To get the forum post type
 * @uses get_posts() To get the forums
 * @uses bbp_update_forum_reply_count() To update the forum reply count
 * @return array An array of the status code and the message
 */
function bbp_admin_repair_forum_reply_count() {
	global $wpdb;

	$statement = __( 'Counting the number of replies in each forum&hellip; %s', 'bbpress' );
	$result    = __( 'Failed!', 'bbpress' );

	// Post type
	$fpt = bbp_get_forum_post_type();

	// Delete the meta keys _bbp_reply_count and _bbp_total_reply_count for each forum
	$sql_delete = "DELETE `postmeta` FROM `{$wpdb->postmeta}` AS `postmeta`
						LEFT JOIN `{$wpdb->posts}` AS `posts` ON `posts`.`ID` = `postmeta`.`post_id`
						WHERE `posts`.`post_type` = '{$fpt}'
						AND `postmeta`.`meta_key` = '_bbp_reply_count'
						OR `postmeta`.`meta_key` = '_bbp_total_reply_count'";

	if ( is_wp_error( $wpdb->query( $sql_delete ) ) ) {
 		return array( 1, sprintf( $statement, $result ) );
	}
 
	// Recalculate the metas key _bbp_reply_count and _bbp_total_reply_count for each forum
	$forums = get_posts( array( 'post_type' => bbp_get_forum_post_type(), 'numberposts' => -1 ) );
	if ( !empty( $forums ) ) {
		foreach ( $forums as $forum ) {
			bbp_update_forum_reply_count( $forum->ID );
		}
	} else {
		return array( 2, sprintf( $statement, $result ) );
	}

	return array( 0, sprintf( $statement, __( 'Complete!', 'bbpress' ) ) );
}

/**
 * Recount topics by the users
 *
 * @since bbPress (r3889)
 *
 * @uses bbp_get_reply_post_type() To get the reply post type
 * @uses wpdb::query() To run our recount sql queries
 * @uses is_wp_error() To check if the executed query returned {@link WP_Error}
 * @return array An array of the status code and the message
 */
function bbp_admin_repair_user_topic_count() {
	global $wpdb;

	$statement   = __( 'Counting the number of topics each user has created&hellip; %s', 'bbpress' );
	$result      = __( 'Failed!', 'bbpress' );
	$sql_select  = "SELECT `post_author`, COUNT(DISTINCT `ID`) as `_count` FROM `{$wpdb->posts}` WHERE `post_type` = '" . bbp_get_topic_post_type() . "' AND `post_status` = '" . bbp_get_public_status_id() . "' GROUP BY `post_author`;";
	$insert_rows = $wpdb->get_results( $sql_select );

	if ( is_wp_error( $insert_rows ) )
		return array( 1, sprintf( $statement, $result ) );

	$key           = $wpdb->prefix . '_bbp_topic_count';
	$insert_values = array();
	foreach ( $insert_rows as $insert_row )
		$insert_values[] = "('{$insert_row->post_author}', '{$key}', '{$insert_row->_count}')";

	if ( !count( $insert_values ) )
		return array( 2, sprintf( $statement, $result ) );

	$sql_delete = "DELETE FROM `{$wpdb->usermeta}` WHERE `meta_key` = '{$key}';";
	if ( is_wp_error( $wpdb->query( $sql_delete ) ) )
		return array( 3, sprintf( $statement, $result ) );

	foreach ( array_chunk( $insert_values, 10000 ) as $chunk ) {
		$chunk = "\n" . implode( ",\n", $chunk );
		$sql_insert = "INSERT INTO `{$wpdb->usermeta}` (`user_id`, `meta_key`, `meta_value`) VALUES $chunk;";

		if ( is_wp_error( $wpdb->query( $sql_insert ) ) ) {
			return array( 4, sprintf( $statement, $result ) );
		}
	}

	return array( 0, sprintf( $statement, __( 'Complete!', 'bbpress' ) ) );
}

/**
 * Recount topic replied by the users
 *
 * @since bbPress (r2613)
 *
 * @uses bbp_get_reply_post_type() To get the reply post type
 * @uses wpdb::query() To run our recount sql queries
 * @uses is_wp_error() To check if the executed query returned {@link WP_Error}
 * @return array An array of the status code and the message
 */
function bbp_admin_repair_user_reply_count() {
	global $wpdb;

	$statement   = __( 'Counting the number of topics to which each user has replied&hellip; %s', 'bbpress' );
	$result      = __( 'Failed!', 'bbpress' );
	$sql_select  = "SELECT `post_author`, COUNT(DISTINCT `ID`) as `_count` FROM `{$wpdb->posts}` WHERE `post_type` = '" . bbp_get_reply_post_type() . "' AND `post_status` = '" . bbp_get_public_status_id() . "' GROUP BY `post_author`;";
	$insert_rows = $wpdb->get_results( $sql_select );

	if ( is_wp_error( $insert_rows ) )
		return array( 1, sprintf( $statement, $result ) );

	$key           = $wpdb->prefix . '_bbp_reply_count';
	$insert_values = array();
	foreach ( $insert_rows as $insert_row )
		$insert_values[] = "('{$insert_row->post_author}', '{$key}', '{$insert_row->_count}')";

	if ( !count( $insert_values ) )
		return array( 2, sprintf( $statement, $result ) );

	$sql_delete = "DELETE FROM `{$wpdb->usermeta}` WHERE `meta_key` = '{$key}';";
	if ( is_wp_error( $wpdb->query( $sql_delete ) ) )
		return array( 3, sprintf( $statement, $result ) );

	foreach ( array_chunk( $insert_values, 10000 ) as $chunk ) {
		$chunk = "\n" . implode( ",\n", $chunk );
		$sql_insert = "INSERT INTO `{$wpdb->usermeta}` (`user_id`, `meta_key`, `meta_value`) VALUES $chunk;";

		if ( is_wp_error( $wpdb->query( $sql_insert ) ) ) {
			return array( 4, sprintf( $statement, $result ) );
		}
	}

	return array( 0, sprintf( $statement, __( 'Complete!', 'bbpress' ) ) );
}

/**
 * Clean the users' favorites
 *
 * @since bbPress (r2613)
 *
 * @uses bbp_get_topic_post_type() To get the topic post type
 * @uses wpdb::query() To run our recount sql queries
 * @uses is_wp_error() To check if the executed query returned {@link WP_Error}
 * @return array An array of the status code and the message
 */
function bbp_admin_repair_user_favorites() {
	global $wpdb;

	$statement = __( 'Removing trashed topics from user favorites&hellip; %s', 'bbpress' );
	$result    = __( 'Failed!', 'bbpress' );
	$key       = $wpdb->prefix . '_bbp_favorites';
	$users     = $wpdb->get_results( "SELECT `user_id`, `meta_value` AS `favorites` FROM `{$wpdb->usermeta}` WHERE `meta_key` = '{$key}';" );

	if ( is_wp_error( $users ) )
		return array( 1, sprintf( $statement, $result ) );

	$topics = $wpdb->get_col( "SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_type` = '" . bbp_get_topic_post_type() . "' AND `post_status` = '" . bbp_get_public_status_id() . "';" );

	if ( is_wp_error( $topics ) )
		return array( 2, sprintf( $statement, $result ) );

	$values = array();
	foreach ( $users as $user ) {
		if ( empty( $user->favorites ) || !is_string( $user->favorites ) )
			continue;

		$favorites = array_intersect( $topics, explode( ',', $user->favorites ) );
		if ( empty( $favorites ) || !is_array( $favorites ) )
			continue;

		$favorites_joined = implode( ',', $favorites );
		$values[]         = "('{$user->user_id}', '{$key}, '{$favorites_joined}')";

		// Cleanup
		unset( $favorites, $favorites_joined );
	}

	if ( !count( $values ) ) {
		$result = __( 'Nothing to remove!', 'bbpress' );
		return array( 0, sprintf( $statement, $result ) );
	}

	$sql_delete = "DELETE FROM `{$wpdb->usermeta}` WHERE `meta_key` = '{$key}';";
	if ( is_wp_error( $wpdb->query( $sql_delete ) ) )
		return array( 4, sprintf( $statement, $result ) );

	foreach ( array_chunk( $values, 10000 ) as $chunk ) {
		$chunk = "\n" . implode( ",\n", $chunk );
		$sql_insert = "INSERT INTO `$wpdb->usermeta` (`user_id`, `meta_key`, `meta_value`) VALUES $chunk;";
		if ( is_wp_error( $wpdb->query( $sql_insert ) ) ) {
			return array( 5, sprintf( $statement, $result ) );
		}
	}

	return array( 0, sprintf( $statement, __( 'Complete!', 'bbpress' ) ) );
}

/**
 * Clean the users' topic subscriptions
 *
 * @since bbPress (r2668)
 *
 * @uses bbp_get_topic_post_type() To get the topic post type
 * @uses wpdb::query() To run our recount sql queries
 * @uses is_wp_error() To check if the executed query returned {@link WP_Error}
 * @return array An array of the status code and the message
 */
function bbp_admin_repair_user_topic_subscriptions() {
	global $wpdb;

	$statement = __( 'Removing trashed topics from user subscriptions&hellip; %s', 'bbpress' );
	$result    = __( 'Failed!', 'bbpress' );
	$key       = $wpdb->prefix . '_bbp_subscriptions';
	$users     = $wpdb->get_results( "SELECT `user_id`, `meta_value` AS `subscriptions` FROM `{$wpdb->usermeta}` WHERE `meta_key` = '{$key}';" );

	if ( is_wp_error( $users ) )
		return array( 1, sprintf( $statement, $result ) );

	$topics = $wpdb->get_col( "SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_type` = '" . bbp_get_topic_post_type() . "' AND `post_status` = '" . bbp_get_public_status_id() . "';" );
	if ( is_wp_error( $topics ) )
		return array( 2, sprintf( $statement, $result ) );

	$values = array();
	foreach ( $users as $user ) {
		if ( empty( $user->subscriptions ) || !is_string( $user->subscriptions ) )
			continue;

		$subscriptions = array_intersect( $topics, explode( ',', $user->subscriptions ) );
		if ( empty( $subscriptions ) || !is_array( $subscriptions ) )
			continue;

		$subscriptions_joined = implode( ',', $subscriptions );
		$values[]             = "('{$user->user_id}', '{$key}', '{$subscriptions_joined}')";

		// Cleanup
		unset( $subscriptions, $subscriptions_joined );
	}

	if ( !count( $values ) ) {
		$result = __( 'Nothing to remove!', 'bbpress' );
		return array( 0, sprintf( $statement, $result ) );
	}

	$sql_delete = "DELETE FROM `{$wpdb->usermeta}` WHERE `meta_key` = '{$key}';";
	if ( is_wp_error( $wpdb->query( $sql_delete ) ) )
		return array( 4, sprintf( $statement, $result ) );

	foreach ( array_chunk( $values, 10000 ) as $chunk ) {
		$chunk = "\n" . implode( ",\n", $chunk );
		$sql_insert = "INSERT INTO `{$wpdb->usermeta}` (`user_id`, `meta_key`, `meta_value`) VALUES $chunk;";
		if ( is_wp_error( $wpdb->query( $sql_insert ) ) ) {
			return array( 5, sprintf( $statement, $result ) );
		}
	}

	return array( 0, sprintf( $statement, __( 'Complete!', 'bbpress' ) ) );
}

/**
 * Clean the users' forum subscriptions
 *
 * @since bbPress (r5155)
 *
 * @uses bbp_get_forum_post_type() To get the topic post type
 * @uses wpdb::query() To run our recount sql queries
 * @uses is_wp_error() To check if the executed query returned {@link WP_Error}
 * @return array An array of the status code and the message
 */
function bbp_admin_repair_user_forum_subscriptions() {
	global $wpdb;

	$statement = __( 'Removing trashed forums from user subscriptions&hellip; %s', 'bbpress' );
	$result    = __( 'Failed!', 'bbpress' );
	$key       = $wpdb->prefix . '_bbp_forum_subscriptions';
	$users     = $wpdb->get_results( "SELECT `user_id`, `meta_value` AS `subscriptions` FROM `{$wpdb->usermeta}` WHERE `meta_key` = '{$key}';" );

	if ( is_wp_error( $users ) )
		return array( 1, sprintf( $statement, $result ) );

	$forums = $wpdb->get_col( "SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_type` = '" . bbp_get_forum_post_type() . "' AND `post_status` = '" . bbp_get_public_status_id() . "';" );
	if ( is_wp_error( $forums ) )
		return array( 2, sprintf( $statement, $result ) );

	$values = array();
	foreach ( $users as $user ) {
		if ( empty( $user->subscriptions ) || !is_string( $user->subscriptions ) )
			continue;

		$subscriptions = array_intersect( $forums, explode( ',', $user->subscriptions ) );
		if ( empty( $subscriptions ) || !is_array( $subscriptions ) )
			continue;

		$subscriptions_joined = implode( ',', $subscriptions );
		$values[]             = "('{$user->user_id}', '{$key}', '{$subscriptions_joined}')";

		// Cleanup
		unset( $subscriptions, $subscriptions_joined );
	}

	if ( !count( $values ) ) {
		$result = __( 'Nothing to remove!', 'bbpress' );
		return array( 0, sprintf( $statement, $result ) );
	}

	$sql_delete = "DELETE FROM `{$wpdb->usermeta}` WHERE `meta_key` = '{$key}';";
	if ( is_wp_error( $wpdb->query( $sql_delete ) ) )
		return array( 4, sprintf( $statement, $result ) );

	foreach ( array_chunk( $values, 10000 ) as $chunk ) {
		$chunk = "\n" . implode( ",\n", $chunk );
		$sql_insert = "INSERT INTO `{$wpdb->usermeta}` (`user_id`, `meta_key`, `meta_value`) VALUES $chunk;";
		if ( is_wp_error( $wpdb->query( $sql_insert ) ) ) {
			return array( 5, sprintf( $statement, $result ) );
		}
	}

	return array( 0, sprintf( $statement, __( 'Complete!', 'bbpress' ) ) );
}

/**
 * This repair tool will map each user of the current site to their respective
 * forums role. By default, Admins will be Key Masters, and every other role
 * will be the default role defined in Settings > Forums (Participant).
 *
 * @since bbPress (r4340)
 *
 * @uses bbp_get_user_role_map() To get the map of user roles
 * @uses get_editable_roles() To get the current WordPress roles
 * @uses get_users() To get the users of each role (limited to ID field)
 * @uses bbp_set_user_role() To set each user's forums role
 */
function bbp_admin_repair_user_roles() {

	$statement    = __( 'Remapping forum role for each user on this site&hellip; %s', 'bbpress' );
	$changed      = 0;
	$role_map     = bbp_get_user_role_map();
	$default_role = bbp_get_default_role();

	// Bail if no role map exists
	if ( empty( $role_map ) )
		return array( 1, sprintf( $statement, __( 'Failed!', 'bbpress' ) ) );

	// Iterate through each role...
	foreach ( array_keys( bbp_get_blog_roles() ) as $role ) {

		// Reset the offset
		$offset = 0;

		// If no role map exists, give the default forum role (bbp-participant)
		$new_role = isset( $role_map[$role] ) ? $role_map[$role] : $default_role;

		// Get users of this site, limited to 1000
		while ( $users = get_users( array(
				'role'   => $role,
				'fields' => 'ID',
				'number' => 1000,
				'offset' => $offset
			) ) ) {

			// Iterate through each user of $role and try to set it
			foreach ( (array) $users as $user_id ) {
				if ( bbp_set_user_role( $user_id, $new_role ) ) {
					++$changed; // Keep a count to display at the end
				}
			}

			// Bump the offset for the next query iteration
			$offset = $offset + 1000;
		}
	}

	$result = sprintf( __( 'Complete! %s users updated.', 'bbpress' ), bbp_number_format( $changed ) );
	return array( 0, sprintf( $statement, $result ) );
}

/**
 * Recaches the last post in every topic and forum
 *
 * @since bbPress (r3040)
 *
 * @uses wpdb::query() To run our recount sql queries
 * @uses is_wp_error() To check if the executed query returned {@link WP_Error}
 * @return array An array of the status code and the message
 */
function bbp_admin_repair_freshness() {
	global $wpdb;

	$statement = __( 'Recomputing latest post in every topic and forum&hellip; %s', 'bbpress' );
	$result    = __( 'Failed!', 'bbpress' );

	// First, delete everything.
	if ( is_wp_error( $wpdb->query( "DELETE FROM `$wpdb->postmeta` WHERE `meta_key` IN ( '_bbp_last_reply_id', '_bbp_last_topic_id', '_bbp_last_active_id', '_bbp_last_active_time' );" ) ) )
		return array( 1, sprintf( $statement, $result ) );

	// Next, give all the topics with replies the ID their last reply.
	if ( is_wp_error( $wpdb->query( "INSERT INTO `$wpdb->postmeta` (`post_id`, `meta_key`, `meta_value`)
			( SELECT `topic`.`ID`, '_bbp_last_reply_id', MAX( `reply`.`ID` )
			FROM `$wpdb->posts` AS `topic` INNER JOIN `$wpdb->posts` AS `reply` ON `topic`.`ID` = `reply`.`post_parent`
			WHERE `reply`.`post_status` IN ( '" . bbp_get_public_status_id() . "' ) AND `topic`.`post_type` = 'topic' AND `reply`.`post_type` = 'reply'
			GROUP BY `topic`.`ID` );" ) ) )
		return array( 2, sprintf( $statement, $result ) );

	// For any remaining topics, give a reply ID of 0.
	if ( is_wp_error( $wpdb->query( "INSERT INTO `$wpdb->postmeta` (`post_id`, `meta_key`, `meta_value`)
			( SELECT `ID`, '_bbp_last_reply_id', 0
			FROM `$wpdb->posts` AS `topic` LEFT JOIN `$wpdb->postmeta` AS `reply`
			ON `topic`.`ID` = `reply`.`post_id` AND `reply`.`meta_key` = '_bbp_last_reply_id'
			WHERE `reply`.`meta_id` IS NULL AND `topic`.`post_type` = 'topic' );" ) ) )
		return array( 3, sprintf( $statement, $result ) );

	// Now we give all the forums with topics the ID their last topic.
	if ( is_wp_error( $wpdb->query( "INSERT INTO `$wpdb->postmeta` (`post_id`, `meta_key`, `meta_value`)
			( SELECT `forum`.`ID`, '_bbp_last_topic_id', `topic`.`ID`
			FROM `$wpdb->posts` AS `forum` INNER JOIN `$wpdb->posts` AS `topic` ON `forum`.`ID` = `topic`.`post_parent`
			WHERE `topic`.`post_status` IN ( '" . bbp_get_public_status_id() . "' ) AND `forum`.`post_type` = 'forum' AND `topic`.`post_type` = 'topic'
			GROUP BY `forum`.`ID` );" ) ) )
		return array( 4, sprintf( $statement, $result ) );

	// For any remaining forums, give a topic ID of 0.
	if ( is_wp_error( $wpdb->query( "INSERT INTO `$wpdb->postmeta` (`post_id`, `meta_key`, `meta_value`)
			( SELECT `ID`, '_bbp_last_topic_id', 0
			FROM `$wpdb->posts` AS `forum` LEFT JOIN `$wpdb->postmeta` AS `topic`
			ON `forum`.`ID` = `topic`.`post_id` AND `topic`.`meta_key` = '_bbp_last_topic_id'
			WHERE `topic`.`meta_id` IS NULL AND `forum`.`post_type` = 'forum' );" ) ) )
		return array( 5, sprintf( $statement, $result ) );

	// After that, we give all the topics with replies the ID their last reply (again, this time for a different reason).
	if ( is_wp_error( $wpdb->query( "INSERT INTO `$wpdb->postmeta` (`post_id`, `meta_key`, `meta_value`)
			( SELECT `topic`.`ID`, '_bbp_last_active_id', MAX( `reply`.`ID` )
			FROM `$wpdb->posts` AS `topic` INNER JOIN `$wpdb->posts` AS `reply` ON `topic`.`ID` = `reply`.`post_parent`
			WHERE `reply`.`post_status` IN ( '" . bbp_get_public_status_id() . "' ) AND `topic`.`post_type` = 'topic' AND `reply`.`post_type` = 'reply'
			GROUP BY `topic`.`ID` );" ) ) )
		return array( 6, sprintf( $statement, $result ) );

	// For any remaining topics, give a reply ID of themself.
	if ( is_wp_error( $wpdb->query( "INSERT INTO `$wpdb->postmeta` (`post_id`, `meta_key`, `meta_value`)
			( SELECT `ID`, '_bbp_last_active_id', `ID`
			FROM `$wpdb->posts` AS `topic` LEFT JOIN `$wpdb->postmeta` AS `reply`
			ON `topic`.`ID` = `reply`.`post_id` AND `reply`.`meta_key` = '_bbp_last_active_id'
			WHERE `reply`.`meta_id` IS NULL AND `topic`.`post_type` = 'topic' );" ) ) )
		return array( 7, sprintf( $statement, $result ) );

	// Give topics with replies their last update time.
	if ( is_wp_error( $wpdb->query( "INSERT INTO `$wpdb->postmeta` (`post_id`, `meta_key`, `meta_value`)
			( SELECT `topic`.`ID`, '_bbp_last_active_time', MAX( `reply`.`post_date` )
			FROM `$wpdb->posts` AS `topic` INNER JOIN `$wpdb->posts` AS `reply` ON `topic`.`ID` = `reply`.`post_parent`
			WHERE `reply`.`post_status` IN ( '" . bbp_get_public_status_id() . "' ) AND `topic`.`post_type` = 'topic' AND `reply`.`post_type` = 'reply'
			GROUP BY `topic`.`ID` );" ) ) )
		return array( 8, sprintf( $statement, $result ) );

	// Give topics without replies their last update time.
	if ( is_wp_error( $wpdb->query( "INSERT INTO `$wpdb->postmeta` (`post_id`, `meta_key`, `meta_value`)
			( SELECT `ID`, '_bbp_last_active_time', `post_date`
			FROM `$wpdb->posts` AS `topic` LEFT JOIN `$wpdb->postmeta` AS `reply`
			ON `topic`.`ID` = `reply`.`post_id` AND `reply`.`meta_key` = '_bbp_last_active_time'
			WHERE `reply`.`meta_id` IS NULL AND `topic`.`post_type` = 'topic' );" ) ) )
		return array( 9, sprintf( $statement, $result ) );

	// Forums need to know what their last active item is as well. Now it gets a bit more complex to do in the database.
	$forums = $wpdb->get_col( "SELECT `ID` FROM `$wpdb->posts` WHERE `post_type` = 'forum' and `post_status` != 'auto-draft';" );
	if ( is_wp_error( $forums ) )
		return array( 10, sprintf( $statement, $result ) );

 	// Loop through forums
 	foreach ( $forums as $forum_id ) {
		if ( !bbp_is_forum_category( $forum_id ) ) {
			bbp_update_forum( array( 'forum_id' => $forum_id ) );
		}
	}

	// Loop through categories when forums are done
	foreach ( $forums as $forum_id ) {
		if ( bbp_is_forum_category( $forum_id ) ) {
			bbp_update_forum( array( 'forum_id' => $forum_id ) );
		}
	}

	// Complete results
	return array( 0, sprintf( $statement, __( 'Complete!', 'bbpress' ) ) );
}

/**
 * Repairs the relationship of sticky topics to the actual parent forum
 *
 * @since bbPress (r4695)
 *
 * @uses wpdb::get_col() To run our recount sql queries
 * @uses is_wp_error() To check if the executed query returned {@link WP_Error}
 * @return array An array of the status code and the message
 */
function bbp_admin_repair_sticky() {
	global $wpdb;

	$statement = __( 'Repairing the sticky topic to the parent forum relationships&hellip; %s', 'bbpress' );
	$result    = __( 'Failed!', 'bbpress' );
	$forums    = $wpdb->get_col( "SELECT ID FROM `{$wpdb->posts}` WHERE `post_type` = 'forum';" );

	// Bail if no forums found
	if ( empty( $forums ) || is_wp_error( $forums ) )
		return array( 1, sprintf( $statement, $result ) );

	// Loop through forums and get their sticky topics
	foreach ( $forums as $forum ) {
		$forum_stickies[$forum] = get_post_meta( $forum, '_bbp_sticky_topics', true );
	}

	// Cleanup
	unset( $forums, $forum );

	// Loop through each forum with sticky topics
	foreach ( $forum_stickies as $forum_id => $stickies ) {

		// Skip if no stickies
		if ( empty( $stickies ) ) {
			continue;
		}

		// Loop through each sticky topic
		foreach ( $stickies as $id => $topic_id ) {

			// If the topic is not a super sticky, and the forum ID does not
			// match the topic's forum ID, unset the forum's sticky meta.
			if ( ! bbp_is_topic_super_sticky( $topic_id ) && $forum_id !== bbp_get_topic_forum_id( $topic_id ) ) {
				unset( $forum_stickies[$forum_id][$id] );
			}
		}

		// Get sticky topic ID's, or use empty string
		$stickers = empty( $forum_stickies[$forum_id] ) ? '' : array_values( $forum_stickies[$forum_id] );

		// Update the forum's sticky topics meta
		update_post_meta( $forum_id, '_bbp_sticky_topics', $stickers );
	}

	// Complete results
	return array( 0, sprintf( $statement, __( 'Complete!', 'bbpress' ) ) );
}

/**
 * Recaches the private and hidden forums
 *
 * @since bbPress (r4104)
 *
 * @uses delete_option() to delete private and hidden forum pointers
 * @uses WP_Query() To query post IDs
 * @uses is_wp_error() To return if error occurred
 * @uses update_option() To update the private and hidden post ID pointers
 * @return array An array of the status code and the message
 */
function bbp_admin_repair_forum_visibility() {
	$statement = __( 'Recalculating forum visibility &hellip; %s', 'bbpress' );

	// Bail if queries returned errors
	if ( ! bbp_repair_forum_visibility() ) {
		return array( 2, sprintf( $statement, __( 'Failed!',   'bbpress' ) ) );

	// Complete results
	} else {
		return array( 0, sprintf( $statement, __( 'Complete!', 'bbpress' ) ) );
	}
}

/**
 * Recaches the forum for each post
 *
 * @since bbPress (r3876)
 *
 * @uses wpdb::query() To run our recount sql queries
 * @uses is_wp_error() To check if the executed query returned {@link WP_Error}
 * @return array An array of the status code and the message
 */
function bbp_admin_repair_forum_meta() {
	global $wpdb;

	$statement = __( 'Recalculating the forum for each post &hellip; %s', 'bbpress' );
	$result    = __( 'Failed!', 'bbpress' );

	// First, delete everything.
	if ( is_wp_error( $wpdb->query( "DELETE FROM `$wpdb->postmeta` WHERE `meta_key` = '_bbp_forum_id';" ) ) )
		return array( 1, sprintf( $statement, $result ) );

	// Next, give all the topics with replies the ID their last reply.
	if ( is_wp_error( $wpdb->query( "INSERT INTO `$wpdb->postmeta` (`post_id`, `meta_key`, `meta_value`)
			( SELECT `forum`.`ID`, '_bbp_forum_id', `forum`.`post_parent`
			FROM `$wpdb->posts`
				AS `forum`
			WHERE `forum`.`post_type` = 'forum'
			GROUP BY `forum`.`ID` );" ) ) )
		return array( 2, sprintf( $statement, $result ) );

	// Next, give all the topics with replies the ID their last reply.
	if ( is_wp_error( $wpdb->query( "INSERT INTO `$wpdb->postmeta` (`post_id`, `meta_key`, `meta_value`)
			( SELECT `topic`.`ID`, '_bbp_forum_id', `topic`.`post_parent`
			FROM `$wpdb->posts`
				AS `topic`
			WHERE `topic`.`post_type` = 'topic'
			GROUP BY `topic`.`ID` );" ) ) )
		return array( 3, sprintf( $statement, $result ) );

	// Next, give all the topics with replies the ID their last reply.
	if ( is_wp_error( $wpdb->query( "INSERT INTO `$wpdb->postmeta` (`post_id`, `meta_key`, `meta_value`)
			( SELECT `reply`.`ID`, '_bbp_forum_id', `topic`.`post_parent`
			FROM `$wpdb->posts`
				AS `reply`
			INNER JOIN `$wpdb->posts`
				AS `topic`
				ON `reply`.`post_parent` = `topic`.`ID`
			WHERE `topic`.`post_type` = 'topic'
				AND `reply`.`post_type` = 'reply'
			GROUP BY `reply`.`ID` );" ) ) )
		return array( 4, sprintf( $statement, $result ) );

	// Complete results
	return array( 0, sprintf( $statement, __( 'Complete!', 'bbpress' ) ) );
}

/**
 * Recaches the topic for each post
 *
 * @since bbPress (r3876)
 *
 * @uses wpdb::query() To run our recount sql queries
 * @uses is_wp_error() To check if the executed query returned {@link WP_Error}
 * @return array An array of the status code and the message
 */
function bbp_admin_repair_topic_meta() {
	global $wpdb;

	$statement = __( 'Recalculating the topic for each post &hellip; %s', 'bbpress' );
	$result    = __( 'Failed!', 'bbpress' );

	// First, delete everything.
	if ( is_wp_error( $wpdb->query( "DELETE FROM `$wpdb->postmeta` WHERE `meta_key` = '_bbp_topic_id';" ) ) )
		return array( 1, sprintf( $statement, $result ) );

	// Next, give all the topics with replies the ID their last reply.
	if ( is_wp_error( $wpdb->query( "INSERT INTO `$wpdb->postmeta` (`post_id`, `meta_key`, `meta_value`)
			( SELECT `topic`.`ID`, '_bbp_topic_id', `topic`.`ID`
			FROM `$wpdb->posts`
				AS `topic`
			WHERE `topic`.`post_type` = 'topic'
			GROUP BY `topic`.`ID` );" ) ) )
		return array( 3, sprintf( $statement, $result ) );

	// Next, give all the topics with replies the ID their last reply.
	if ( is_wp_error( $wpdb->query( "INSERT INTO `$wpdb->postmeta` (`post_id`, `meta_key`, `meta_value`)
			( SELECT `reply`.`ID`, '_bbp_topic_id', `topic`.`ID`
			FROM `$wpdb->posts`
				AS `reply`
			INNER JOIN `$wpdb->posts`
				AS `topic`
				ON `reply`.`post_parent` = `topic`.`ID`
			WHERE `topic`.`post_type` = 'topic'
				AND `reply`.`post_type` = 'reply'
			GROUP BY `reply`.`ID` );" ) ) )
		return array( 4, sprintf( $statement, $result ) );

	// Complete results
	return array( 0, sprintf( $statement, __( 'Complete!', 'bbpress' ) ) );
}

/**
 * Recalculate reply menu order
 *
 * @since bbPress (r5367)
 *
 * @uses wpdb::query() To run our recount sql queries
 * @uses is_wp_error() To check if the executed query returned {@link WP_Error}
 * @uses bbp_get_reply_post_type() To get the reply post type
 * @uses bbp_update_reply_position() To update the reply position
 * @return array An array of the status code and the message
 */
function bbp_admin_repair_reply_menu_order() {
	global $wpdb;

	$statement = __( 'Recalculating reply menu order &hellip; %s', 'bbpress' );
	$result    = __( 'No reply positions to recalculate!',         'bbpress' );

	// Delete cases where `_bbp_reply_to` was accidentally set to itself
	if ( is_wp_error( $wpdb->query( "DELETE FROM `{$wpdb->postmeta}` WHERE `meta_key` = '_bbp_reply_to' AND `post_id` = `meta_value`;" ) ) ) {
		return array( 1, sprintf( $statement, $result ) ); 
	}

	// Post type
	$rpt = bbp_get_reply_post_type();

	// Get an array of reply id's to update the menu oder for each reply
	$replies = $wpdb->get_results( "SELECT `a`.`ID` FROM `{$wpdb->posts}` AS `a`
										INNER JOIN (
											SELECT `menu_order`, `post_parent`
											FROM `{$wpdb->posts}`
											GROUP BY `menu_order`, `post_parent`
											HAVING COUNT( * ) >1
										)`b`
										ON `a`.`menu_order` = `b`.`menu_order`
										AND `a`.`post_parent` = `b`.`post_parent`
										WHERE `post_type` = '{$rpt}';", OBJECT_K );

	// Bail if no replies returned
	if ( empty( $replies ) ) {
		return array( 1, sprintf( $statement, $result ) );
	}

	// Recalculate the menu order position for each reply
	foreach ( $replies as $reply ) {
		bbp_update_reply_position( $reply->ID );
	}

	// Cleanup
	unset( $replies, $reply );

	// Flush the cache; things are about to get ugly.
	wp_cache_flush();

	return array( 0, sprintf( $statement, __( 'Complete!', 'bbpress' ) ) );
}

/** Reset ********************************************************************/

/**
 * Admin reset page
 *
 * @since bbPress (r2613)
 *
 * @uses check_admin_referer() To verify the nonce and the referer
 * @uses do_action() Calls 'admin_notices' to display the notices
 * @uses screen_icon() To display the screen icon
 * @uses wp_nonce_field() To add a hidden nonce field
 */
function bbp_admin_reset() {
?>

	<div class="wrap">

		<?php screen_icon( 'tools' ); ?>

		<h2 class="nav-tab-wrapper"><?php bbp_tools_admin_tabs( __( 'Reset Forums', 'bbpress' ) ); ?></h2>
		<p><?php esc_html_e( 'Revert your forums back to a brand new installation. This process cannot be undone.', 'bbpress' ); ?></p>
		<p><strong><?php esc_html_e( 'Backup your database before proceeding.', 'bbpress' ); ?></strong></p>

		<form class="settings" method="post" action="">
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'The following data will be removed:', 'bbpress' ) ?></th>
						<td>
							<?php esc_html_e( 'All Forums',           'bbpress' ); ?><br />
							<?php esc_html_e( 'All Topics',           'bbpress' ); ?><br />
							<?php esc_html_e( 'All Replies',          'bbpress' ); ?><br />
							<?php esc_html_e( 'All Topic Tags',       'bbpress' ); ?><br />
							<?php esc_html_e( 'Related Meta Data',    'bbpress' ); ?><br />
							<?php esc_html_e( 'Forum Settings',       'bbpress' ); ?><br />
							<?php esc_html_e( 'Forum Activity',       'bbpress' ); ?><br />
							<?php esc_html_e( 'Forum User Roles',     'bbpress' ); ?><br />
							<?php esc_html_e( 'Importer Helper Data', 'bbpress' ); ?><br />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Delete imported users?', 'bbpress' ); ?></th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><span><?php esc_html_e( "Say it ain't so!", 'bbpress' ); ?></span></legend>
								<label><input type="checkbox" class="checkbox" name="bbpress-delete-imported-users" id="bbpress-delete-imported-users" value="1" /> <?php esc_html_e( 'This option will delete all previously imported users, and cannot be undone.', 'bbpress' ); ?></label>
								<p class="description"><?php esc_html_e( 'Note: Resetting without this checked will delete the meta-data necessary to delete these users.', 'bbpress' ); ?></p>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Are you sure you want to do this?', 'bbpress' ); ?></th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><span><?php esc_html_e( "Say it ain't so!", 'bbpress' ); ?></span></legend>
								<label><input type="checkbox" class="checkbox" name="bbpress-are-you-sure" id="bbpress-are-you-sure" value="1" /> <?php esc_html_e( 'This process cannot be undone.', 'bbpress' ); ?></label>
								<p class="description"><?php esc_html_e( 'Human sacrifice, dogs and cats living together... mass hysteria!', 'bbpress' ); ?></p>
							</fieldset>
						</td>
					</tr>
				</tbody>
			</table>

			<fieldset class="submit">
				<input class="button-primary" type="submit" name="submit" value="<?php esc_attr_e( 'Reset bbPress', 'bbpress' ); ?>" />
				<?php wp_nonce_field( 'bbpress-reset' ); ?>
			</fieldset>
		</form>
	</div>

<?php
}

/**
 * Handle the processing and feedback of the admin tools page
 *
 * @since bbPress (r2613)
 *
 * @uses check_admin_referer() To verify the nonce and the referer
 * @uses wp_cache_flush() To flush the cache
 */
function bbp_admin_reset_handler() {

	// Bail if not resetting
	if ( ! bbp_is_post_request() || empty( $_POST['bbpress-are-you-sure'] ) )
		return;

	// Only keymasters can proceed
	if ( ! bbp_is_user_keymaster() )
		return;

	check_admin_referer( 'bbpress-reset' );

	global $wpdb;

	// Stores messages
	$messages = array();
	$failed   = __( 'Failed',   'bbpress' );
	$success  = __( 'Success!', 'bbpress' );

	// Flush the cache; things are about to get ugly.
	wp_cache_flush();

	/** Posts *****************************************************************/

	$statement  = __( 'Deleting Posts&hellip; %s', 'bbpress' );
	$sql_posts  = $wpdb->get_results( "SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_type` IN ('forum', 'topic', 'reply')", OBJECT_K );
	$sql_delete = "DELETE FROM `{$wpdb->posts}` WHERE `post_type` IN ('forum', 'topic', 'reply')";
	$result     = is_wp_error( $wpdb->query( $sql_delete ) ) ? $failed : $success;
	$messages[] = sprintf( $statement, $result );

	/** Post Meta *************************************************************/

	if ( !empty( $sql_posts ) ) {
		$sql_meta = array();
		foreach ( $sql_posts as $key => $value ) {
			$sql_meta[] = $key;
		}
		$statement  = __( 'Deleting Post Meta&hellip; %s', 'bbpress' );
		$sql_meta   = implode( "', '", $sql_meta );
		$sql_delete = "DELETE FROM `{$wpdb->postmeta}` WHERE `post_id` IN ('{$sql_meta}');";
		$result     = is_wp_error( $wpdb->query( $sql_delete ) ) ? $failed : $success;
		$messages[] = sprintf( $statement, $result );
	}

	/** Topic Tags ************************************************************/

	$statement  = __( 'Deleting Topic Tags&hellip; %s', 'bbpress' );
	$sql_delete = "DELETE a,b,c FROM `{$wpdb->terms}` AS a LEFT JOIN `{$wpdb->term_taxonomy}` AS c ON a.term_id = c.term_id LEFT JOIN `{$wpdb->term_relationships}` AS b ON b.term_taxonomy_id = c.term_taxonomy_id WHERE c.taxonomy = 'topic-tag';";
	$result     = is_wp_error( $wpdb->query( $sql_delete ) ) ? $failed : $success;
	$messages[] = sprintf( $statement, $result );

	/** User ******************************************************************/

	// Delete users
	if ( !empty( $_POST['bbpress-delete-imported-users'] ) ) {
		$sql_users  = $wpdb->get_results( "SELECT `user_id` FROM `{$wpdb->usermeta}` WHERE `meta_key` = '_bbp_user_id'", OBJECT_K );
		if ( !empty( $sql_users ) ) {
			$sql_meta = array();
			foreach ( $sql_users as $key => $value ) {
				$sql_meta[] = $key;
			}
			$statement  = __( 'Deleting User&hellip; %s', 'bbpress' );
			$sql_meta   = implode( "', '", $sql_meta );
			$sql_delete = "DELETE FROM `{$wpdb->users}` WHERE `ID` IN ('{$sql_meta}');";
			$result     = is_wp_error( $wpdb->query( $sql_delete ) ) ? $failed : $success;
			$messages[] = sprintf( $statement, $result );
			$statement  = __( 'Deleting User Meta&hellip; %s', 'bbpress' );
			$sql_delete = "DELETE FROM `{$wpdb->usermeta}` WHERE `user_id` IN ('{$sql_meta}');";
			$result     = is_wp_error( $wpdb->query( $sql_delete ) ) ? $failed : $success;
			$messages[] = sprintf( $statement, $result );
		}

	// Delete imported user metadata
	} else {
		$statement  = __( 'Deleting User Meta&hellip; %s', 'bbpress' );
		$sql_delete = "DELETE FROM `{$wpdb->usermeta}` WHERE `meta_key` LIKE '%%_bbp_%%';";
		$result     = is_wp_error( $wpdb->query( $sql_delete ) ) ? $failed : $success;
		$messages[] = sprintf( $statement, $result );
	}

	/** Converter *************************************************************/

	$statement  = __( 'Deleting Conversion Table&hellip; %s', 'bbpress' );
	$table_name = $wpdb->prefix . 'bbp_converter_translator';
	if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) === $table_name ) {
		$wpdb->query( "DROP TABLE {$table_name}" );
		$result = $success;
	} else {
		$result = $failed;
	}
	$messages[] = sprintf( $statement, $result );

	/** Options ***************************************************************/

	$statement  = __( 'Deleting Settings&hellip; %s', 'bbpress' );
	bbp_delete_options();
	$messages[] = sprintf( $statement, $success );

	/** Roles *****************************************************************/

	$statement  = __( 'Deleting Roles and Capabilities&hellip; %s', 'bbpress' );
	remove_role( bbp_get_moderator_role() );
	remove_role( bbp_get_participant_role() );
	bbp_remove_caps();
	$messages[] = sprintf( $statement, $success );

	/** Output ****************************************************************/

	if ( count( $messages ) ) {
		foreach ( $messages as $message ) {
			bbp_admin_tools_feedback( $message );
		}
	}
}
