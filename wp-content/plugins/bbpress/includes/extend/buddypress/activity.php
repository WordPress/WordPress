<?php

/**
 * bbPress BuddyPress Activity Class
 *
 * @package bbPress
 * @subpackage BuddyPress
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'BBP_BuddyPress_Activity' ) ) :
/**
 * Loads BuddyPress Activity extension
 *
 * @since bbPress (r3395)
 *
 * @package bbPress
 * @subpackage BuddyPress
 */
class BBP_BuddyPress_Activity {

	/** Variables *************************************************************/

	/**
	 * The name of the BuddyPress component, used in activity streams
	 *
	 * @var string
	 */
	private $component = '';

	/**
	 * Forum Create Activity Action
	 *
	 * @var string
	 */
	private $forum_create = '';

	/**
	 * Topic Create Activity Action
	 *
	 * @var string
	 */
	private $topic_create = '';

	/**
	 * Topic Close Activity Action
	 *
	 * @var string
	 */
	private $topic_close = '';

	/**
	 * Topic Edit Activity Action
	 *
	 * @var string
	 */
	private $topic_edit = '';

	/**
	 * Topic Open Activity Action
	 *
	 * @var string
	 */
	private $topic_open = '';

	/**
	 * Reply Create Activity Action
	 *
	 * @var string
	 */
	private $reply_create = '';

	/**
	 * Reply Edit Activity Action
	 *
	 * @var string
	 */
	private $reply_edit = '';

	/** Setup Methods *********************************************************/

	/**
	 * The bbPress BuddyPress Activity loader
	 *
	 * @since bbPress (r3395)
	 */
	public function __construct() {
		$this->setup_globals();
		$this->setup_actions();
		$this->setup_filters();
		$this->fully_loaded();
	}

	/**
	 * Extension variables
	 *
	 * @since bbPress (r3395)
	 * @access private
	 * @uses apply_filters() Calls various filters
	 */
	private function setup_globals() {

		// The name of the BuddyPress component, used in activity streams
		$this->component    = 'bbpress';

		// Forums
		$this->forum_create = 'bbp_forum_create';

		// Topics
		$this->topic_create = 'bbp_topic_create';
		$this->topic_edit   = 'bbp_topic_edit';
		$this->topic_close  = 'bbp_topic_close';
		$this->topic_open   = 'bbp_topic_open';

		// Replies
		$this->reply_create = 'bbp_reply_create';
		$this->reply_edit   = 'bbp_reply_edit';
	}

	/**
	 * Setup the actions
	 *
	 * @since bbPress (r3395)
	 * @access private
	 * @uses add_filter() To add various filters
	 * @uses add_action() To add various actions
	 */
	private function setup_actions() {

		// Register the activity stream actions
		add_action( 'bp_register_activity_actions',      array( $this, 'register_activity_actions' )        );

		// Hook into topic and reply creation
		add_action( 'bbp_new_topic',                     array( $this, 'topic_create'              ), 10, 4 );
		add_action( 'bbp_new_reply',                     array( $this, 'reply_create'              ), 10, 5 );

		// Hook into topic and reply status changes
		add_action( 'edit_post',                         array( $this, 'topic_update'              ), 10, 2 );
		add_action( 'edit_post',                         array( $this, 'reply_update'              ), 10, 2 );

		// Hook into topic and reply deletion
		add_action( 'bbp_delete_topic',                  array( $this, 'topic_delete'              ), 10, 1 );
		add_action( 'bbp_delete_reply',                  array( $this, 'reply_delete'              ), 10, 1 );

		// Append forum filters in site wide activity streams
		add_action( 'bp_activity_filter_options',        array( $this, 'activity_filter_options'   ), 10    );

		// Append forum filters in single member activity streams
		add_action( 'bp_member_activity_filter_options', array( $this, 'activity_filter_options'   ), 10    );

		// Append forum filters in single group activity streams
		add_action( 'bp_group_activity_filter_options',  array( $this, 'activity_filter_options'   ), 10    );
	}

	/**
	 * Setup the filters
	 *
	 * @since bbPress (r3395)
	 * @access private
	 * @uses add_filter() To add various filters
	 * @uses add_action() To add various actions
	 */
	private function setup_filters() {

		// Obey BuddyPress commenting rules
		add_filter( 'bp_activity_can_comment',   array( $this, 'activity_can_comment'   )        );

		// Link directly to the topic or reply
		add_filter( 'bp_activity_get_permalink', array( $this, 'activity_get_permalink' ), 10, 2 );
	}

	/**
	 * Allow the variables, actions, and filters to be modified by third party
	 * plugins and themes.
	 *
	 * @since bbPress (r3902)
	 */
	private function fully_loaded() {
		do_action_ref_array( 'bbp_buddypress_activity_loaded', array( $this ) );
	}

	/** Methods ***************************************************************/

	/**
	 * Register our activity actions with BuddyPress
	 *
	 * @since bbPress (r3395)
	 * @uses bp_activity_set_action()
	 */
	public function register_activity_actions() {

		// Sitewide activity stream items
		bp_activity_set_action( $this->component, $this->topic_create, esc_html__( 'New forum topic', 'bbpress' ) );
		bp_activity_set_action( $this->component, $this->reply_create, esc_html__( 'New forum reply', 'bbpress' ) );
	}

	/**
	 * Wrapper for recoding bbPress actions to the BuddyPress activity stream
	 *
	 * @since bbPress (r3395)
	 * @param type $args Array of arguments for bp_activity_add()
	 * @uses bbp_get_current_user_id()
	 * @uses bp_core_current_time()
	 * @uses bbp_parse_args()
	 * @uses aplly_filters()
	 * @uses bp_activity_add()
	 * @return type Activity ID if successful, false if not
	 */
	private function record_activity( $args = array() ) {

		// Default activity args
		$activity = bbp_parse_args( $args, array(
			'id'                => null,
			'user_id'           => bbp_get_current_user_id(),
			'type'              => '',
			'action'            => '',
			'item_id'           => '',
			'secondary_item_id' => '',
			'content'           => '',
			'primary_link'      => '',
			'component'         => $this->component,
			'recorded_time'     => bp_core_current_time(),
			'hide_sitewide'     => false
		), 'record_activity' );

		// Add the activity
		return bp_activity_add( $activity );
	}

	/**
	 * Wrapper for deleting bbPress actions from BuddyPress activity stream
	 *
	 * @since bbPress (r3395)
	 * @param type $args Array of arguments for bp_activity_add()
	 * @uses bbp_get_current_user_id()
	 * @uses bp_core_current_time()
	 * @uses bbp_parse_args()
	 * @uses aplly_filters()
	 * @uses bp_activity_add()
	 * @return type Activity ID if successful, false if not
	 */
	public function delete_activity( $args = '' ) {

		// Default activity args
		$activity = bbp_parse_args( $args, array(
			'item_id'           => false,
			'component'         => $this->component,
			'type'              => false,
			'user_id'           => false,
			'secondary_item_id' => false
		), 'delete_activity' );

		// Delete the activity
		bp_activity_delete_by_item_id( $activity );
	}

	/**
	 * Check for an existing activity stream entry for a given post_id
	 *
	 * @param int $post_id ID of the topic or reply
	 * @uses get_post_meta()
	 * @uses bp_activity_get_specific()
	 * @return int if an activity id is verified, false if not
	 */
	private static function get_activity_id( $post_id = 0 ) {

		// Try to get the activity ID of the post
		$activity_id = (int) get_post_meta( $post_id, '_bbp_activity_id', true );

		// Bail if no activity ID is in post meta
		if ( empty( $activity_id ) )
			return null;

		// Get the activity stream item, bail if it doesn't exist
		$existing = bp_activity_get_specific( array( 'activity_ids' => $activity_id, 'show_hidden' => true, 'spam' => 'all', ) );
		if ( empty( $existing['total'] ) || ( 1 !== (int) $existing['total'] ) )
			return null;

		// Return the activity ID since we've verified the connection
		return $activity_id;
	}

	/**
	 * Maybe disable activity stream comments on select actions
	 *
	 * @since bbPress (r3399)
	 * @global BP_Activity_Template $activities_template
	 * @param boolean $can_comment
	 * @uses bp_get_activity_action_name()
	 * @return boolean
	 */
	public function activity_can_comment( $can_comment = true ) {
		global $activities_template;

		// Already forced off, so comply
		if ( false === $can_comment )
			return $can_comment;

		// Check if blog & forum activity stream commenting is off
		if ( ( false === $activities_template->disable_blogforum_replies ) || (int) $activities_template->disable_blogforum_replies ) {

			// Get the current action name
			$action_name = bp_get_activity_action_name();

			// Setup the array of possibly disabled actions
			$disabled_actions = array(
				$this->topic_create,
				$this->reply_create
			);

			// Check if this activity stream action is disabled
			if ( in_array( $action_name, $disabled_actions ) ) {
				$can_comment = false;
			}
		}

		return $can_comment;
	}

	/**
	 * Maybe link directly to topics and replies in activity stream entries
	 *
	 * @since bbPress (r3399)
	 * @param string $link
	 * @param mixed $activity_object
	 * @return string The link to the activity stream item
	 */
	public function activity_get_permalink( $link = '', $activity_object = false ) {

		// Setup the array of actions to link directly to
		$disabled_actions = array(
			$this->topic_create,
			$this->reply_create
		);

		// Check if this activity stream action is directly linked
		if ( in_array( $activity_object->type, $disabled_actions ) ) {
			$link = $activity_object->primary_link;
		}

		return $link;
	}

	/**
	 * Append forum options to activity filter select box
	 *
	 * @since bbPress (r3653)
	 */
	function activity_filter_options() {
	?>

		<option value="<?php echo $this->topic_create; ?>"><?php esc_html_e( 'Topics',  'bbpress' ); ?></option>
		<option value="<?php echo $this->reply_create; ?>"><?php esc_html_e( 'Replies', 'bbpress' ); ?></option>

	<?php
	}

	/** Topics ****************************************************************/

	/**
	 * Record an activity stream entry when a topic is created or updated
	 *
	 * @since bbPress (r3395)
	 * @param int $topic_id
	 * @param int $forum_id
	 * @param array $anonymous_data
	 * @param int $topic_author_id
	 * @uses bbp_get_topic_id()
	 * @uses bbp_get_forum_id()
	 * @uses bbp_get_user_profile_link()
	 * @uses bbp_get_topic_permalink()
	 * @uses bbp_get_topic_title()
	 * @uses bbp_get_topic_content()
	 * @uses bbp_get_forum_permalink()
	 * @uses bbp_get_forum_title()
	 * @uses bp_create_excerpt()
	 * @uses apply_filters()
	 * @return Bail early if topic is by anonymous user
	 */
	public function topic_create( $topic_id = 0, $forum_id = 0, $anonymous_data = array(), $topic_author_id = 0 ) {

		// Bail early if topic is by anonymous user
		if ( !empty( $anonymous_data ) )
			return;

		// Bail if site is private
		if ( !bbp_is_site_public() )
			return;

		// Validate activity data
		$user_id  = (int) $topic_author_id;
		$topic_id = bbp_get_topic_id( $topic_id );
		$forum_id = bbp_get_forum_id( $forum_id );

		// Bail if user is not active
		if ( bbp_is_user_inactive( $user_id ) )
			return;

		// Bail if topic is not published
		if ( !bbp_is_topic_published( $topic_id ) )
			return;

		// User link for topic author
		$user_link = bbp_get_user_profile_link( $user_id  );

		// Topic
		$topic_permalink = bbp_get_topic_permalink( $topic_id );
		$topic_title     = get_post_field( 'post_title',   $topic_id, 'raw' );
		$topic_content   = get_post_field( 'post_content', $topic_id, 'raw' );
		$topic_link      = '<a href="' . $topic_permalink . '">' . $topic_title . '</a>';

		// Forum
		$forum_permalink = bbp_get_forum_permalink( $forum_id );
		$forum_title     = get_post_field( 'post_title', $forum_id, 'raw' );
		$forum_link      = '<a href="' . $forum_permalink . '">' . $forum_title . '</a>';

		// Activity action & text
		$activity_text    = sprintf( esc_html__( '%1$s started the topic %2$s in the forum %3$s', 'bbpress' ), $user_link, $topic_link, $forum_link );
		$activity_action  = apply_filters( 'bbp_activity_topic_create',         $activity_text, $user_id,   $topic_id,   $forum_id );
		$activity_content = apply_filters( 'bbp_activity_topic_create_excerpt', $topic_content                                     );

		// Compile and record the activity stream results
		$activity_id = $this->record_activity( array(
			'id'                => $this->get_activity_id( $topic_id ),
			'user_id'           => $user_id,
			'action'            => $activity_action,
			'content'           => $activity_content,
			'primary_link'      => $topic_permalink,
			'type'              => $this->topic_create,
			'item_id'           => $topic_id,
			'secondary_item_id' => $forum_id,
			'recorded_time'     => get_post_time( 'Y-m-d H:i:s', true, $topic_id ),
			'hide_sitewide'     => ! bbp_is_forum_public( $forum_id, false )
		) );

		// Add the activity entry ID as a meta value to the topic
		if ( !empty( $activity_id ) ) {
			update_post_meta( $topic_id, '_bbp_activity_id', $activity_id );
		}
	}

	/**
	 * Delete the activity stream entry when a topic is spammed, trashed, or deleted
	 *
	 * @param int $topic_id
	 * @uses bp_activity_delete()
	 */
	public function topic_delete( $topic_id = 0 ) {

		// Get activity ID, bail if it doesn't exist
		if ( $activity_id = $this->get_activity_id( $topic_id ) )
			return bp_activity_delete( array( 'id' => $activity_id ) );

		return false;
	}

	/**
	 * Update the activity stream entry when a topic status changes
	 *
	 * @param int $post_id
	 * @param obj $post
	 * @uses get_post_type()
	 * @uses bbp_get_topic_post_type()
	 * @uses bbp_get_topic_id()
	 * @uses bbp_is_topic_anonymous()
	 * @uses bbp_get_public_status_id()
	 * @uses bbp_get_closed_status_id()
	 * @uses bbp_get_topic_forum_id()
	 * @uses bbp_get_topic_author_id()
	 * @return Bail early if not a topic, or topic is by anonymous user
	 */
	public function topic_update( $topic_id = 0, $post = null ) {

		// Bail early if not a topic
		if ( get_post_type( $post ) !== bbp_get_topic_post_type() )
			return;

		$topic_id = bbp_get_topic_id( $topic_id );

		// Bail early if topic is by anonymous user
		if ( bbp_is_topic_anonymous( $topic_id ) )
			return;

		// Action based on new status
		if ( in_array( $post->post_status, array( bbp_get_public_status_id(), bbp_get_closed_status_id() ) ) ) {

			// Validate topic data
			$forum_id        = bbp_get_topic_forum_id( $topic_id );
			$topic_author_id = bbp_get_topic_author_id( $topic_id );

			$this->topic_create( $topic_id, $forum_id, array(), $topic_author_id );
		} else {
			$this->topic_delete( $topic_id );
		}
	}

	/** Replies ***************************************************************/

	/**
	 * Record an activity stream entry when a reply is created
	 *
	 * @since bbPress (r3395)
	 * @param int $topic_id
	 * @param int $forum_id
	 * @param array $anonymous_data
	 * @param int $topic_author_id
	 * @uses bbp_get_reply_id()
	 * @uses bbp_get_topic_id()
	 * @uses bbp_get_forum_id()
	 * @uses bbp_get_user_profile_link()
	 * @uses bbp_get_reply_url()
	 * @uses bbp_get_reply_content()
	 * @uses bbp_get_topic_permalink()
	 * @uses bbp_get_topic_title()
	 * @uses bbp_get_forum_permalink()
	 * @uses bbp_get_forum_title()
	 * @uses bp_create_excerpt()
	 * @uses apply_filters()
	 * @return Bail early if topic is by anonywous user
	 */
	public function reply_create( $reply_id = 0, $topic_id = 0, $forum_id = 0, $anonymous_data = array(), $reply_author_id = 0 ) {

		// Do not log activity of anonymous users
		if ( !empty( $anonymous_data ) )
			return;

		// Bail if site is private
		if ( !bbp_is_site_public() )
			return;

		// Validate activity data
		$user_id  = (int) $reply_author_id;
		$reply_id = bbp_get_reply_id( $reply_id );
		$topic_id = bbp_get_topic_id( $topic_id );
		$forum_id = bbp_get_forum_id( $forum_id );

		// Bail if user is not active
		if ( bbp_is_user_inactive( $user_id ) )
			return;

		// Bail if reply is not published
		if ( !bbp_is_reply_published( $reply_id ) )
			return;

		// Setup links for activity stream
		$user_link = bbp_get_user_profile_link( $user_id  );

		// Reply
		$reply_url     = bbp_get_reply_url( $reply_id );
		$reply_content = get_post_field( 'post_content', $reply_id, 'raw' );

		// Topic
		$topic_permalink = bbp_get_topic_permalink( $topic_id );
		$topic_title     = get_post_field( 'post_title', $topic_id, 'raw' );
		$topic_link      = '<a href="' . $topic_permalink . '">' . $topic_title . '</a>';

		// Forum
		$forum_permalink = bbp_get_forum_permalink( $forum_id );
		$forum_title     = get_post_field( 'post_title', $forum_id, 'raw' );
		$forum_link      = '<a href="' . $forum_permalink . '">' . $forum_title . '</a>';

		// Activity action & text
		$activity_text    = sprintf( esc_html__( '%1$s replied to the topic %2$s in the forum %3$s', 'bbpress' ), $user_link, $topic_link, $forum_link );
		$activity_action  = apply_filters( 'bbp_activity_reply_create',         $activity_text, $user_id, $reply_id,  $topic_id );
		$activity_content = apply_filters( 'bbp_activity_reply_create_excerpt', $reply_content                                  );

		// Compile and record the activity stream results
		$activity_id = $this->record_activity( array(
			'id'                => $this->get_activity_id( $reply_id ),
			'user_id'           => $user_id,
			'action'            => $activity_action,
			'content'           => $activity_content,
			'primary_link'      => $reply_url,
			'type'              => $this->reply_create,
			'item_id'           => $reply_id,
			'secondary_item_id' => $topic_id,
			'recorded_time'     => get_post_time( 'Y-m-d H:i:s', true, $reply_id ),
			'hide_sitewide'     => ! bbp_is_forum_public( $forum_id, false )
		) );

		// Add the activity entry ID as a meta value to the reply
		if ( !empty( $activity_id ) ) {
			update_post_meta( $reply_id, '_bbp_activity_id', $activity_id );
		}
	}

 	/**
	 * Delete the activity stream entry when a reply is spammed, trashed, or deleted
	 *
	 * @param int $reply_id
	 * @uses get_post_meta()
	 * @uses bp_activity_delete()
	 */
	public function reply_delete( $reply_id ) {

		// Get activity ID, bail if it doesn't exist
		if ( $activity_id = $this->get_activity_id( $reply_id ) )
			return bp_activity_delete( array( 'id' => $activity_id ) );

		return false;
	}

	/**
	 * Update the activity stream entry when a reply status changes
	 *
	 * @param int $post_id
	 * @param obj $post
	 * @uses get_post_type()
	 * @uses bbp_get_reply_post_type()
	 * @uses bbp_get_reply_id()
	 * @uses bbp_is_reply_anonymous()
	 * @uses bbp_get_public_status_id()
	 * @uses bbp_get_closed_status_id()
	 * @uses bbp_get_reply_topic_id()
	 * @uses bbp_get_reply_forum_id()
	 * @uses bbp_get_reply_author_id()
	 * @return Bail early if not a reply, or reply is by anonymous user
	 */
	public function reply_update( $reply_id, $post ) {

		// Bail early if not a reply
		if ( get_post_type( $post ) !== bbp_get_reply_post_type() )
			return;

		$reply_id = bbp_get_reply_id( $reply_id );

		// Bail early if reply is by anonymous user
		if ( bbp_is_reply_anonymous( $reply_id ) )
			return;

		// Action based on new status
		if ( bbp_get_public_status_id() === $post->post_status ) {

			// Validate reply data
			$topic_id        = bbp_get_reply_topic_id( $reply_id );
			$forum_id        = bbp_get_reply_forum_id( $reply_id );
			$reply_author_id = bbp_get_reply_author_id( $reply_id );

			$this->reply_create( $reply_id, $topic_id, $forum_id, array(), $reply_author_id );
		} else {
			$this->reply_delete( $reply_id );
		}
	}
}
endif;
