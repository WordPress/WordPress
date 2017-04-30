<?php

/**
 * bbPress Updater
 *
 * @package bbPress
 * @subpackage Updater
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * If there is no raw DB version, this is the first installation
 *
 * @since bbPress (r3764)
 *
 * @uses get_option()
 * @uses bbp_get_db_version() To get bbPress's database version
 * @return bool True if update, False if not
 */
function bbp_is_install() {
	return ! bbp_get_db_version_raw();
}

/**
 * Compare the bbPress version to the DB version to determine if updating
 *
 * @since bbPress (r3421)
 *
 * @uses get_option()
 * @uses bbp_get_db_version() To get bbPress's database version
 * @return bool True if update, False if not
 */
function bbp_is_update() {
	$raw    = (int) bbp_get_db_version_raw();
	$cur    = (int) bbp_get_db_version();
	$retval = (bool) ( $raw < $cur );
	return $retval;
}

/**
 * Determine if bbPress is being activated
 *
 * Note that this function currently is not used in bbPress core and is here
 * for third party plugins to use to check for bbPress activation.
 *
 * @since bbPress (r3421)
 *
 * @return bool True if activating bbPress, false if not
 */
function bbp_is_activation( $basename = '' ) {
	global $pagenow;

	$bbp    = bbpress();
	$action = false;

	// Bail if not in admin/plugins
	if ( ! ( is_admin() && ( 'plugins.php' === $pagenow ) ) ) {
		return false;
	}

	if ( ! empty( $_REQUEST['action'] ) && ( '-1' !== $_REQUEST['action'] ) ) {
		$action = $_REQUEST['action'];
	} elseif ( ! empty( $_REQUEST['action2'] ) && ( '-1' !== $_REQUEST['action2'] ) ) {
		$action = $_REQUEST['action2'];
	}

	// Bail if not activating
	if ( empty( $action ) || !in_array( $action, array( 'activate', 'activate-selected' ) ) ) {
		return false;
	}

	// The plugin(s) being activated
	if ( $action === 'activate' ) {
		$plugins = isset( $_GET['plugin'] ) ? array( $_GET['plugin'] ) : array();
	} else {
		$plugins = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
	}

	// Set basename if empty
	if ( empty( $basename ) && !empty( $bbp->basename ) ) {
		$basename = $bbp->basename;
	}

	// Bail if no basename
	if ( empty( $basename ) ) {
		return false;
	}

	// Is bbPress being activated?
	return in_array( $basename, $plugins );
}

/**
 * Determine if bbPress is being deactivated
 *
 * @since bbPress (r3421)
 * @return bool True if deactivating bbPress, false if not
 */
function bbp_is_deactivation( $basename = '' ) {
	global $pagenow;

	$bbp    = bbpress();
	$action = false;

	// Bail if not in admin/plugins
	if ( ! ( is_admin() && ( 'plugins.php' === $pagenow ) ) ) {
		return false;
	}

	if ( ! empty( $_REQUEST['action'] ) && ( '-1' !== $_REQUEST['action'] ) ) {
		$action = $_REQUEST['action'];
	} elseif ( ! empty( $_REQUEST['action2'] ) && ( '-1' !== $_REQUEST['action2'] ) ) {
		$action = $_REQUEST['action2'];
	}

	// Bail if not deactivating
	if ( empty( $action ) || !in_array( $action, array( 'deactivate', 'deactivate-selected' ) ) ) {
		return false;
	}

	// The plugin(s) being deactivated
	if ( $action === 'deactivate' ) {
		$plugins = isset( $_GET['plugin'] ) ? array( $_GET['plugin'] ) : array();
	} else {
		$plugins = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
	}

	// Set basename if empty
	if ( empty( $basename ) && !empty( $bbp->basename ) ) {
		$basename = $bbp->basename;
	}

	// Bail if no basename
	if ( empty( $basename ) ) {
		return false;
	}

	// Is bbPress being deactivated?
	return in_array( $basename, $plugins );
}

/**
 * Update the DB to the latest version
 *
 * @since bbPress (r3421)
 * @uses update_option()
 * @uses bbp_get_db_version() To get bbPress's database version
 */
function bbp_version_bump() {
	update_option( '_bbp_db_version', bbp_get_db_version() );
}

/**
 * Setup the bbPress updater
 *
 * @since bbPress (r3419)
 *
 * @uses bbp_version_updater()
 * @uses bbp_version_bump()
 * @uses flush_rewrite_rules()
 */
function bbp_setup_updater() {

	// Bail if no update needed
	if ( ! bbp_is_update() )
		return;

	// Call the automated updater
	bbp_version_updater();
}

/**
 * Create a default forum, topic, and reply
 *
 * @since bbPress (r3767)
 * @param array $args Array of arguments to override default values
 */
function bbp_create_initial_content( $args = array() ) {

	// Parse arguments against default values
	$r = bbp_parse_args( $args, array(
		'forum_parent'  => 0,
		'forum_status'  => 'publish',
		'forum_title'   => __( 'General',                                  'bbpress' ),
		'forum_content' => __( 'General chit-chat',                        'bbpress' ),
		'topic_title'   => __( 'Hello World!',                             'bbpress' ),
		'topic_content' => __( 'I am the first topic in your new forums.', 'bbpress' ),
		'reply_title'   => __( 'Re: Hello World!',                         'bbpress' ),
		'reply_content' => __( 'Oh, and this is what a reply looks like.', 'bbpress' ),
	), 'create_initial_content' );

	// Create the initial forum
	$forum_id = bbp_insert_forum( array(
		'post_parent'  => $r['forum_parent'],
		'post_status'  => $r['forum_status'],
		'post_title'   => $r['forum_title'],
		'post_content' => $r['forum_content']
	) );

	// Create the initial topic
	$topic_id = bbp_insert_topic(
		array(
			'post_parent'  => $forum_id,
			'post_title'   => $r['topic_title'],
			'post_content' => $r['topic_content']
		),
		array( 'forum_id'  => $forum_id )
	);

	// Create the initial reply
	$reply_id = bbp_insert_reply(
		array(
			'post_parent'  => $topic_id,
			'post_title'   => $r['reply_title'],
			'post_content' => $r['reply_content']
		),
		array(
			'forum_id'     => $forum_id,
			'topic_id'     => $topic_id
		)
	);

	return array(
		'forum_id' => $forum_id,
		'topic_id' => $topic_id,
		'reply_id' => $reply_id
	);
}

/**
 * bbPress's version updater looks at what the current database version is, and
 * runs whatever other code is needed.
 *
 * This is most-often used when the data schema changes, but should also be used
 * to correct issues with bbPress meta-data silently on software update.
 *
 * @since bbPress (r4104)
 */
function bbp_version_updater() {

	// Get the raw database version
	$raw_db_version = (int) bbp_get_db_version_raw();

	/** 2.0 Branch ************************************************************/

	// 2.0, 2.0.1, 2.0.2, 2.0.3
	if ( $raw_db_version < 200 ) {
		// No changes
	}

	/** 2.1 Branch ************************************************************/

	// 2.1, 2.1.1
	if ( $raw_db_version < 211 ) {

		/**
		 * Repair private and hidden forum data
		 *
		 * @link http://bbpress.trac.wordpress.org/ticket/1891
		 */
		bbp_admin_repair_forum_visibility();
	}

	/** 2.2 Branch ************************************************************/

	// 2.2
	if ( $raw_db_version < 220 ) {

		// Remove the Moderator role from the database
		remove_role( bbp_get_moderator_role() );

		// Remove the Participant role from the database
		remove_role( bbp_get_participant_role() );

		// Remove capabilities
		bbp_remove_caps();
	}

	/** 2.3 Branch ************************************************************/

	// 2.3
	if ( $raw_db_version < 230 ) {
		// No changes
	}

	/** All done! *************************************************************/

	// Bump the version
	bbp_version_bump();

	// Delete rewrite rules to force a flush
	bbp_delete_rewrite_rules();
}

/**
 * Redirect user to bbPress's What's New page on activation
 *
 * @since bbPress (r4389)
 *
 * @internal Used internally to redirect bbPress to the about page on activation
 *
 * @uses is_network_admin() To bail if being network activated
 * @uses set_transient() To drop the activation transient for 30 seconds
 *
 * @return If network admin or bulk activation
 */
function bbp_add_activation_redirect() {

	// Bail if activating from network, or bulk
	if ( is_network_admin() || isset( $_GET['activate-multi'] ) )
		return;

	// Add the transient to redirect
    set_transient( '_bbp_activation_redirect', true, 30 );
}

/**
 * Hooked to the 'bbp_activate' action, this helper function automatically makes
 * the current user a Key Master in the forums if they just activated bbPress,
 * regardless of the bbp_allow_global_access() setting.
 *
 * @since bbPress (r4910)
 *
 * @internal Used to internally make the current user a keymaster on activation
 *
 * @uses current_user_can() to bail if user cannot activate plugins
 * @uses get_current_user_id() to get the current user ID
 * @uses get_current_blog_id() to get the current blog ID
 * @uses is_user_member_of_blog() to bail if the current user does not have a role
 * @uses bbp_is_user_keymaster() to bail if the user is already a keymaster
 * @uses bbp_set_user_role() to make the current user a keymaster
 *
 * @return If user can't activate plugins or is already a keymaster
 */
function bbp_make_current_user_keymaster() {

	// Bail if the current user can't activate plugins since previous pageload
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	// Get the current user ID
	$user_id = get_current_user_id();
	$blog_id = get_current_blog_id();

	// Bail if user is not actually a member of this site
	if ( ! is_user_member_of_blog( $user_id, $blog_id ) ) {
		return;
	}

	// Bail if the current user already has a forum role to prevent
	// unexpected role and capability escalation.
	if ( bbp_get_user_role( $user_id ) ) {
		return;
	}

	// Make the current user a keymaster
	bbp_set_user_role( $user_id, bbp_get_keymaster_role() );

	// Reload the current user so caps apply immediately
	wp_get_current_user();
}
