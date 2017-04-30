<?php

/**
 * bbPress Replies Admin Class
 *
 * @package bbPress
 * @subpackage Administration
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'BBP_Replies_Admin' ) ) :
/**
 * Loads bbPress replies admin area
 *
 * @package bbPress
 * @subpackage Administration
 * @since bbPress (r2464)
 */
class BBP_Replies_Admin {

	/** Variables *************************************************************/

	/**
	 * @var The post type of this admin component
	 */
	private $post_type = '';

	/** Functions *************************************************************/

	/**
	 * The main bbPress admin loader
	 *
	 * @since bbPress (r2515)
	 *
	 * @uses BBP_Replies_Admin::setup_globals() Setup the globals needed
	 * @uses BBP_Replies_Admin::setup_actions() Setup the hooks and actions
	 * @uses BBP_Replies_Admin::setup_actions() Setup the help text
	 */
	public function __construct() {
		$this->setup_globals();
		$this->setup_actions();
	}

	/**
	 * Setup the admin hooks, actions and filters
	 *
	 * @since bbPress (r2646)
	 * @access private
	 *
	 * @uses add_action() To add various actions
	 * @uses add_filter() To add various filters
	 * @uses bbp_get_forum_post_type() To get the forum post type
	 * @uses bbp_get_topic_post_type() To get the topic post type
	 * @uses bbp_get_reply_post_type() To get the reply post type
	 */
	private function setup_actions() {

		// Add some general styling to the admin area
		add_action( 'bbp_admin_head',        array( $this, 'admin_head'       ) );

		// Messages
		add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );

		// Reply column headers.
		add_filter( 'manage_' . $this->post_type . '_posts_columns',  array( $this, 'column_headers' ) );

		// Reply columns (in post row)
		add_action( 'manage_' . $this->post_type . '_posts_custom_column',  array( $this, 'column_data' ), 10, 2 );
		add_filter( 'post_row_actions',                                     array( $this, 'row_actions' ), 10, 2 );

		// Reply metabox actions
		add_action( 'add_meta_boxes', array( $this, 'attributes_metabox'      ) );
		add_action( 'save_post',      array( $this, 'attributes_metabox_save' ) );

		// Check if there are any bbp_toggle_reply_* requests on admin_init, also have a message displayed
		add_action( 'load-edit.php',  array( $this, 'toggle_reply'        ) );
		add_action( 'admin_notices',  array( $this, 'toggle_reply_notice' ) );

		// Anonymous metabox actions
		add_action( 'add_meta_boxes', array( $this, 'author_metabox'      ) );

		// Add ability to filter topics and replies per forum
		add_filter( 'restrict_manage_posts', array( $this, 'filter_dropdown'  ) );
		add_filter( 'bbp_request',           array( $this, 'filter_post_rows' ) );

		// Contextual Help
		add_action( 'load-edit.php',     array( $this, 'edit_help' ) );
		add_action( 'load-post.php',     array( $this, 'new_help'  ) );
		add_action( 'load-post-new.php', array( $this, 'new_help'  ) );
	}

	/**
	 * Should we bail out of this method?
	 *
	 * @since bbPress (r4067)
	 * @return boolean
	 */
	private function bail() {
		if ( !isset( get_current_screen()->post_type ) || ( $this->post_type !== get_current_screen()->post_type ) )
			return true;

		return false;
	}

	/**
	 * Admin globals
	 *
	 * @since bbPress (r2646)
	 * @access private
	 */
	private function setup_globals() {
		$this->post_type = bbp_get_reply_post_type();
	}

	/** Contextual Help *******************************************************/

	/**
	 * Contextual help for bbPress reply edit page
	 *
	 * @since bbPress (r3119)
	 * @uses get_current_screen()
	 */
	public function edit_help() {

		if ( $this->bail() ) return;

		// Overview
		get_current_screen()->add_help_tab( array(
			'id'		=> 'overview',
			'title'		=> __( 'Overview', 'bbpress' ),
			'content'	=>
				'<p>' . __( 'This screen provides access to all of your replies. You can customize the display of this screen to suit your workflow.', 'bbpress' ) . '</p>'
		) );

		// Screen Content
		get_current_screen()->add_help_tab( array(
			'id'		=> 'screen-content',
			'title'		=> __( 'Screen Content', 'bbpress' ),
			'content'	=>
				'<p>' . __( 'You can customize the display of this screen&#8217;s contents in a number of ways:', 'bbpress' ) . '</p>' .
				'<ul>' .
					'<li>' . __( 'You can hide/display columns based on your needs and decide how many replies to list per screen using the Screen Options tab.',                                                                                                                                                                          'bbpress' ) . '</li>' .
					'<li>' . __( 'You can filter the list of replies by reply status using the text links in the upper left to show All, Published, Draft, or Trashed replies. The default view is to show all replies.',                                                                                                                   'bbpress' ) . '</li>' .
					'<li>' . __( 'You can view replies in a simple title list or with an excerpt. Choose the view you prefer by clicking on the icons at the top of the list on the right.',                                                                                                                                             'bbpress' ) . '</li>' .
					'<li>' . __( 'You can refine the list to show only replies in a specific category or from a specific month by using the dropdown menus above the replies list. Click the Filter button after making your selection. You also can refine the list by clicking on the reply author, category or tag in the replies list.', 'bbpress' ) . '</li>' .
				'</ul>'
		) );

		// Available Actions
		get_current_screen()->add_help_tab( array(
			'id'		=> 'action-links',
			'title'		=> __( 'Available Actions', 'bbpress' ),
			'content'	=>
				'<p>' . __( 'Hovering over a row in the replies list will display action links that allow you to manage your reply. You can perform the following actions:', 'bbpress' ) . '</p>' .
				'<ul>' .
					'<li>' . __( '<strong>Edit</strong> takes you to the editing screen for that reply. You can also reach that screen by clicking on the reply title.',                                                                                 'bbpress' ) . '</li>' .
					//'<li>' . __( '<strong>Quick Edit</strong> provides inline access to the metadata of your reply, allowing you to update reply details without leaving this screen.',                                                                  'bbpress' ) . '</li>' .
					'<li>' . __( '<strong>Trash</strong> removes your reply from this list and places it in the trash, from which you can permanently delete it.',                                                                                       'bbpress' ) . '</li>' .
					'<li>' . __( '<strong>Spam</strong> removes your reply from this list and places it in the spam queue, from which you can permanently delete it.',                                                                                   'bbpress' ) . '</li>' .
					'<li>' . __( '<strong>Preview</strong> will show you what your draft reply will look like if you publish it. View will take you to your live site to view the reply. Which link is available depends on your reply&#8217;s status.', 'bbpress' ) . '</li>' .
				'</ul>'
		) );

		// Bulk Actions
		get_current_screen()->add_help_tab( array(
			'id'		=> 'bulk-actions',
			'title'		=> __( 'Bulk Actions', 'bbpress' ),
			'content'	=>
				'<p>' . __( 'You can also edit or move multiple replies to the trash at once. Select the replies you want to act on using the checkboxes, then select the action you want to take from the Bulk Actions menu and click Apply.',           'bbpress' ) . '</p>' .
				'<p>' . __( 'When using Bulk Edit, you can change the metadata (categories, author, etc.) for all selected replies at once. To remove a reply from the grouping, just click the x next to its name in the Bulk Edit area that appears.', 'bbpress' ) . '</p>'
		) );

		// Help Sidebar
		get_current_screen()->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'bbpress' ) . '</strong></p>' .
			'<p>' . __( '<a href="http://codex.bbpress.org" target="_blank">bbPress Documentation</a>',    'bbpress' ) . '</p>' .
			'<p>' . __( '<a href="http://bbpress.org/forums/" target="_blank">bbPress Support Forums</a>', 'bbpress' ) . '</p>'
		);
	}

	/**
	 * Contextual help for bbPress reply edit page
	 *
	 * @since bbPress (r3119)
	 * @uses get_current_screen()
	 */
	public function new_help() {

		if ( $this->bail() ) return;

		$customize_display = '<p>' . __( 'The title field and the big reply editing Area are fixed in place, but you can reposition all the other boxes using drag and drop, and can minimize or expand them by clicking the title bar of each box. Use the Screen Options tab to unhide more boxes (Excerpt, Send Trackbacks, Custom Fields, Discussion, Slug, Author) or to choose a 1- or 2-column layout for this screen.', 'bbpress' ) . '</p>';

		get_current_screen()->add_help_tab( array(
			'id'      => 'customize-display',
			'title'   => __( 'Customizing This Display', 'bbpress' ),
			'content' => $customize_display,
		) );

		get_current_screen()->add_help_tab( array(
			'id'      => 'title-reply-editor',
			'title'   => __( 'Title and Reply Editor', 'bbpress' ),
			'content' =>
				'<p>' . __( '<strong>Title</strong> - Enter a title for your reply. After you enter a title, you&#8217;ll see the permalink below, which you can edit.', 'bbpress' ) . '</p>' .
				'<p>' . __( '<strong>Reply Editor</strong> - Enter the text for your reply. There are two modes of editing: Visual and HTML. Choose the mode by clicking on the appropriate tab. Visual mode gives you a WYSIWYG editor. Click the last icon in the row to get a second row of controls. The HTML mode allows you to enter raw HTML along with your reply text. You can insert media files by clicking the icons above the reply editor and following the directions. You can go to the distraction-free writing screen via the Fullscreen icon in Visual mode (second to last in the top row) or the Fullscreen button in HTML mode (last in the row). Once there, you can make buttons visible by hovering over the top area. Exit Fullscreen back to the regular reply editor.', 'bbpress' ) . '</p>'
		) );

		$publish_box = '<p>' . __( '<strong>Publish</strong> - You can set the terms of publishing your reply in the Publish box. For Status, Visibility, and Publish (immediately), click on the Edit link to reveal more options. Visibility includes options for password-protecting a reply or making it stay at the top of your blog indefinitely (sticky). Publish (immediately) allows you to set a future or past date and time, so you can schedule a reply to be published in the future or backdate a reply.', 'bbpress' ) . '</p>';

		if ( current_theme_supports( 'reply-thumbnails' ) && post_type_supports( 'reply', 'thumbnail' ) ) {
			$publish_box .= '<p>' . __( '<strong>Featured Image</strong> - This allows you to associate an image with your reply without inserting it. This is usually useful only if your theme makes use of the featured image as a reply thumbnail on the home page, a custom header, etc.', 'bbpress' ) . '</p>';
		}

		get_current_screen()->add_help_tab( array(
			'id'      => 'reply-attributes',
			'title'   => __( 'Reply Attributes', 'bbpress' ),
			'content' =>
				'<p>' . __( 'Select the attributes that your reply should have:', 'bbpress' ) . '</p>' .
				'<ul>' .
					'<li>' . __( '<strong>Forum</strong> dropdown determines the parent forum that the reply belongs to. Select the forum, or leave the default (Use Forum of Topic) to post the reply in forum of the topic.', 'bbpress' ) . '</li>' .
					'<li>' . __( '<strong>Topic</strong> determines the parent topic that the reply belongs to.', 'bbpress' ) . '</li>' .
					'<li>' . __( '<strong>Reply To</strong> determines the threading of the reply.', 'bbpress' ) . '</li>' .
				'</ul>'
		) );

		get_current_screen()->add_help_tab( array(
			'id'      => 'publish-box',
			'title'   => __( 'Publish Box', 'bbpress' ),
			'content' => $publish_box,
		) );

		get_current_screen()->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'bbpress' ) . '</strong></p>' .
			'<p>' . __( '<a href="http://codex.bbpress.org" target="_blank">bbPress Documentation</a>',    'bbpress' ) . '</p>' .
			'<p>' . __( '<a href="http://bbpress.org/forums/" target="_blank">bbPress Support Forums</a>', 'bbpress' ) . '</p>'
		);
	}

	/**
	 * Add the reply attributes metabox
	 *
	 * @since bbPress (r2746)
	 *
	 * @uses bbp_get_reply_post_type() To get the reply post type
	 * @uses add_meta_box() To add the metabox
	 * @uses do_action() Calls 'bbp_reply_attributes_metabox'
	 */
	public function attributes_metabox() {

		if ( $this->bail() ) return;

		add_meta_box (
			'bbp_reply_attributes',
			__( 'Reply Attributes', 'bbpress' ),
			'bbp_reply_metabox',
			$this->post_type,
			'side',
			'high'
		);

		do_action( 'bbp_reply_attributes_metabox' );
	}

	/**
	 * Pass the reply attributes for processing
	 *
	 * @since bbPress (r2746)
	 *
	 * @param int $reply_id Reply id
	 * @uses current_user_can() To check if the current user is capable of
	 *                           editing the reply
	 * @uses do_action() Calls 'bbp_reply_attributes_metabox_save' with the
	 *                    reply id and parent id
	 * @return int Parent id
	 */
	public function attributes_metabox_save( $reply_id ) {

		if ( $this->bail() ) return $reply_id;

		// Bail if doing an autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $reply_id;

		// Bail if not a post request
		if ( ! bbp_is_post_request() )
			return $reply_id;

		// Check action exists
		if ( empty( $_POST['action'] ) )
			return $reply_id;

		// Nonce check
		if ( empty( $_POST['bbp_reply_metabox'] ) || !wp_verify_nonce( $_POST['bbp_reply_metabox'], 'bbp_reply_metabox_save' ) )
			return $reply_id;

		// Current user cannot edit this reply
		if ( !current_user_can( 'edit_reply', $reply_id ) )
			return $reply_id;

		// Get the reply meta post values
		$topic_id = !empty( $_POST['parent_id']    ) ? (int) $_POST['parent_id']    : 0;
		$forum_id = !empty( $_POST['bbp_forum_id'] ) ? (int) $_POST['bbp_forum_id'] : bbp_get_topic_forum_id( $topic_id );
		$reply_to = !empty( $_POST['bbp_reply_to'] ) ? (int) $_POST['bbp_reply_to'] : 0;

		// Get reply author data
		$anonymous_data = bbp_filter_anonymous_post_data();
		$author_id      = bbp_get_reply_author_id( $reply_id );
		$is_edit        = (bool) isset( $_POST['save'] );

		// Formally update the reply
		bbp_update_reply( $reply_id, $topic_id, $forum_id, $anonymous_data, $author_id, $is_edit, $reply_to );

		// Allow other fun things to happen
		do_action( 'bbp_reply_attributes_metabox_save', $reply_id, $topic_id, $forum_id, $reply_to );
		do_action( 'bbp_author_metabox_save',           $reply_id, $anonymous_data                 );

		return $reply_id;
	}

	/**
	 * Add the author info metabox
	 *
	 * Allows editing of information about an author
	 *
	 * @since bbPress (r2828)
	 *
	 * @uses bbp_get_topic() To get the topic
	 * @uses bbp_get_reply() To get the reply
	 * @uses bbp_get_topic_post_type() To get the topic post type
	 * @uses bbp_get_reply_post_type() To get the reply post type
	 * @uses add_meta_box() To add the metabox
	 * @uses do_action() Calls 'bbp_author_metabox' with the topic/reply
	 *                    id
	 */
	public function author_metabox() {

		if ( $this->bail() ) return;

		// Bail if post_type is not a reply
		if ( empty( $_GET['action'] ) || ( 'edit' !== $_GET['action'] ) )
			return;

		// Add the metabox
		add_meta_box(
			'bbp_author_metabox',
			__( 'Author Information', 'bbpress' ),
			'bbp_author_metabox',
			$this->post_type,
			'side',
			'high'
		);

		do_action( 'bbp_author_metabox', get_the_ID() );
	}

	/**
	 * Add some general styling to the admin area
	 *
	 * @since bbPress (r2464)
	 *
	 * @uses bbp_get_forum_post_type() To get the forum post type
	 * @uses bbp_get_topic_post_type() To get the topic post type
	 * @uses bbp_get_reply_post_type() To get the reply post type
	 * @uses sanitize_html_class() To sanitize the classes
	 * @uses do_action() Calls 'bbp_admin_head'
	 */
	public function admin_head() {

		if ( $this->bail() ) return;

		?>

		<style type="text/css" media="screen">
		/*<![CDATA[*/

			strong.label {
				display: inline-block;
				width: 60px;
			}

			.column-bbp_forum_topic_count,
			.column-bbp_forum_reply_count,
			.column-bbp_topic_reply_count,
			.column-bbp_topic_voice_count {
				width: 8% !important;
			}

			.column-author,
			.column-bbp_reply_author,
			.column-bbp_topic_author {
				width: 10% !important;
			}

			.column-bbp_topic_forum,
			.column-bbp_reply_forum,
			.column-bbp_reply_topic {
				width: 10% !important;
			}

			.column-bbp_forum_freshness,
			.column-bbp_topic_freshness {
				width: 10% !important;
			}

			.column-bbp_forum_created,
			.column-bbp_topic_created,
			.column-bbp_reply_created {
				width: 15% !important;
			}

			.status-closed {
				background-color: #eaeaea;
			}

			.status-spam {
				background-color: #faeaea;
			}

		/*]]>*/
		</style>

		<?php
	}

	/**
	 * Toggle reply
	 *
	 * Handles the admin-side spamming/unspamming of replies
	 *
	 * @since bbPress (r2740)
	 *
	 * @uses bbp_get_reply() To get the reply
	 * @uses current_user_can() To check if the user is capable of editing
	 *                           the reply
	 * @uses wp_die() To die if the user isn't capable or the post wasn't
	 *                 found
	 * @uses check_admin_referer() To verify the nonce and check referer
	 * @uses bbp_is_reply_spam() To check if the reply is marked as spam
	 * @uses bbp_unspam_reply() To unmark the reply as spam
	 * @uses bbp_spam_reply() To mark the reply as spam
	 * @uses do_action() Calls 'bbp_toggle_reply_admin' with success, post
	 *                    data, action and message
	 * @uses add_query_arg() To add custom args to the url
	 * @uses wp_safe_redirect() Redirect the page to custom url
	 */
	public function toggle_reply() {

		if ( $this->bail() ) return;

		// Only proceed if GET is a reply toggle action
		if ( bbp_is_get_request() && !empty( $_GET['action'] ) && in_array( $_GET['action'], array( 'bbp_toggle_reply_spam' ) ) && !empty( $_GET['reply_id'] ) ) {
			$action    = $_GET['action'];            // What action is taking place?
			$reply_id  = (int) $_GET['reply_id'];    // What's the reply id?
			$success   = false;                      // Flag
			$post_data = array( 'ID' => $reply_id ); // Prelim array

			// Get reply and die if empty
			$reply = bbp_get_reply( $reply_id );
			if ( empty( $reply ) ) // Which reply?
				wp_die( __( 'The reply was not found!', 'bbpress' ) );

			if ( !current_user_can( 'moderate', $reply->ID ) ) // What is the user doing here?
				wp_die( __( 'You do not have the permission to do that!', 'bbpress' ) );

			switch ( $action ) {
				case 'bbp_toggle_reply_spam' :
					check_admin_referer( 'spam-reply_' . $reply_id );

					$is_spam = bbp_is_reply_spam( $reply_id );
					$message = $is_spam ? 'unspammed' : 'spammed';
					$success = $is_spam ? bbp_unspam_reply( $reply_id ) : bbp_spam_reply( $reply_id );

					break;
			}

			$success = wp_update_post( $post_data );
			$message = array( 'bbp_reply_toggle_notice' => $message, 'reply_id' => $reply->ID );

			if ( false === $success || is_wp_error( $success ) )
				$message['failed'] = '1';

			// Do additional reply toggle actions (admin side)
			do_action( 'bbp_toggle_reply_admin', $success, $post_data, $action, $message );

			// Redirect back to the reply
			$redirect = add_query_arg( $message, remove_query_arg( array( 'action', 'reply_id' ) ) );
			wp_safe_redirect( $redirect );

			// For good measure
			exit();
		}
	}

	/**
	 * Toggle reply notices
	 *
	 * Display the success/error notices from
	 * {@link BBP_Admin::toggle_reply()}
	 *
	 * @since bbPress (r2740)
	 *
	 * @uses bbp_get_reply() To get the reply
	 * @uses bbp_get_reply_title() To get the reply title of the reply
	 * @uses esc_html() To sanitize the reply title
	 * @uses apply_filters() Calls 'bbp_toggle_reply_notice_admin' with
	 *                        message, reply id, notice and is it a failure
	 */
	public function toggle_reply_notice() {

		if ( $this->bail() ) return;

		// Only proceed if GET is a reply toggle action
		if ( bbp_is_get_request() && !empty( $_GET['bbp_reply_toggle_notice'] ) && in_array( $_GET['bbp_reply_toggle_notice'], array( 'spammed', 'unspammed' ) ) && !empty( $_GET['reply_id'] ) ) {
			$notice     = $_GET['bbp_reply_toggle_notice'];         // Which notice?
			$reply_id   = (int) $_GET['reply_id'];                  // What's the reply id?
			$is_failure = !empty( $_GET['failed'] ) ? true : false; // Was that a failure?

			// Empty? No reply?
			if ( empty( $notice ) || empty( $reply_id ) )
				return;

			// Get reply and bail if empty
			$reply = bbp_get_reply( $reply_id );
			if ( empty( $reply ) )
				return;

			$reply_title = bbp_get_reply_title( $reply->ID );

			switch ( $notice ) {
				case 'spammed' :
					$message = $is_failure === true ? sprintf( __( 'There was a problem marking the reply "%1$s" as spam.', 'bbpress' ), $reply_title ) : sprintf( __( 'Reply "%1$s" successfully marked as spam.', 'bbpress' ), $reply_title );
					break;

				case 'unspammed' :
					$message = $is_failure === true ? sprintf( __( 'There was a problem unmarking the reply "%1$s" as spam.', 'bbpress' ), $reply_title ) : sprintf( __( 'Reply "%1$s" successfully unmarked as spam.', 'bbpress' ), $reply_title );
					break;
			}

			// Do additional reply toggle notice filters (admin side)
			$message = apply_filters( 'bbp_toggle_reply_notice_admin', $message, $reply->ID, $notice, $is_failure );

			?>

			<div id="message" class="<?php echo $is_failure === true ? 'error' : 'updated'; ?> fade">
				<p style="line-height: 150%"><?php echo esc_html( $message ); ?></p>
			</div>

			<?php
		}
	}

	/**
	 * Manage the column headers for the replies page
	 *
	 * @since bbPress (r2577)
	 *
	 * @param array $columns The columns
	 * @uses apply_filters() Calls 'bbp_admin_replies_column_headers' with
	 *                        the columns
	 * @return array $columns bbPress reply columns
	 */
	public function column_headers( $columns ) {

		if ( $this->bail() ) return $columns;

		$columns = array(
			'cb'                => '<input type="checkbox" />',
			'title'             => __( 'Title',   'bbpress' ),
			'bbp_reply_forum'   => __( 'Forum',   'bbpress' ),
			'bbp_reply_topic'   => __( 'Topic',   'bbpress' ),
			'bbp_reply_author'  => __( 'Author',  'bbpress' ),
			'bbp_reply_created' => __( 'Created', 'bbpress' ),
		);

		return apply_filters( 'bbp_admin_replies_column_headers', $columns );
	}

	/**
	 * Print extra columns for the replies page
	 *
	 * @since bbPress (r2577)
	 *
	 * @param string $column Column
	 * @param int $reply_id reply id
	 * @uses bbp_get_reply_topic_id() To get the topic id of the reply
	 * @uses bbp_topic_title() To output the reply's topic title
	 * @uses apply_filters() Calls 'reply_topic_row_actions' with an array
	 *                        of reply topic actions
	 * @uses bbp_get_topic_permalink() To get the topic permalink
	 * @uses bbp_get_topic_forum_id() To get the forum id of the topic of
	 *                                 the reply
	 * @uses bbp_get_forum_permalink() To get the forum permalink
	 * @uses admin_url() To get the admin url of post.php
	 * @uses add_query_arg() To add custom args to the url
	 * @uses apply_filters() Calls 'reply_topic_forum_row_actions' with an
	 *                        array of reply topic forum actions
	 * @uses bbp_reply_author_display_name() To output the reply author name
	 * @uses get_the_date() Get the reply creation date
	 * @uses get_the_time() Get the reply creation time
	 * @uses esc_attr() To sanitize the reply creation time
	 * @uses bbp_get_reply_last_active_time() To get the time when the reply was
	 *                                    last active
	 * @uses do_action() Calls 'bbp_admin_replies_column_data' with the
	 *                    column and reply id
	 */
	public function column_data( $column, $reply_id ) {

		if ( $this->bail() ) return;

		// Get topic ID
		$topic_id = bbp_get_reply_topic_id( $reply_id );

		// Populate Column Data
		switch ( $column ) {

			// Topic
			case 'bbp_reply_topic' :

				// Output forum name
				if ( !empty( $topic_id ) ) {

					// Topic Title
					$topic_title = bbp_get_topic_title( $topic_id );
					if ( empty( $topic_title ) ) {
						$topic_title = esc_html__( 'No Topic', 'bbpress' );
					}

					// Output the title
					echo $topic_title;

				// Reply has no topic
				} else {
					esc_html_e( 'No Topic', 'bbpress' );
				}

				break;

			// Forum
			case 'bbp_reply_forum' :

				// Get Forum ID's
				$reply_forum_id = bbp_get_reply_forum_id( $reply_id );
				$topic_forum_id = bbp_get_topic_forum_id( $topic_id );

				// Output forum name
				if ( !empty( $reply_forum_id ) ) {

					// Forum Title
					$forum_title = bbp_get_forum_title( $reply_forum_id );
					if ( empty( $forum_title ) ) {
						$forum_title = esc_html__( 'No Forum', 'bbpress' );
					}

					// Alert capable users of reply forum mismatch
					if ( $reply_forum_id !== $topic_forum_id ) {
						if ( current_user_can( 'edit_others_replies' ) || current_user_can( 'moderate' ) ) {
							$forum_title .= '<div class="attention">' . esc_html__( '(Mismatch)', 'bbpress' ) . '</div>';
						}
					}

					// Output the title
					echo $forum_title;

				// Reply has no forum
				} else {
					_e( 'No Forum', 'bbpress' );
				}

				break;

			// Author
			case 'bbp_reply_author' :
				bbp_reply_author_display_name ( $reply_id );
				break;

			// Freshness
			case 'bbp_reply_created':

				// Output last activity time and date
				printf( '%1$s <br /> %2$s',
					get_the_date(),
					esc_attr( get_the_time() )
				);

				break;

			// Do action for anything else
			default :
				do_action( 'bbp_admin_replies_column_data', $column, $reply_id );
				break;
		}
	}

	/**
	 * Reply Row actions
	 *
	 * Remove the quick-edit action link under the reply title and add the
	 * content and spam link
	 *
	 * @since bbPress (r2577)
	 *
	 * @param array $actions Actions
	 * @param array $reply Reply object
	 * @uses bbp_get_reply_post_type() To get the reply post type
	 * @uses bbp_reply_content() To output reply content
	 * @uses bbp_get_reply_permalink() To get the reply link
	 * @uses bbp_get_reply_title() To get the reply title
	 * @uses current_user_can() To check if the current user can edit or
	 *                           delete the reply
	 * @uses bbp_is_reply_spam() To check if the reply is marked as spam
	 * @uses get_post_type_object() To get the reply post type object
	 * @uses add_query_arg() To add custom args to the url
	 * @uses remove_query_arg() To remove custom args from the url
	 * @uses wp_nonce_url() To nonce the url
	 * @uses get_delete_post_link() To get the delete post link of the reply
	 * @return array $actions Actions
	 */
	public function row_actions( $actions, $reply ) {

		if ( $this->bail() ) return $actions;

		unset( $actions['inline hide-if-no-js'] );

		// Reply view links to topic
		$actions['view'] = '<a href="' . esc_url( bbp_get_reply_url( $reply->ID ) ) . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;', 'bbpress' ), bbp_get_reply_title( $reply->ID ) ) ) . '" rel="permalink">' . esc_html__( 'View', 'bbpress' ) . '</a>';

		// User cannot view replies in trash
		if ( ( bbp_get_trash_status_id() === $reply->post_status ) && !current_user_can( 'view_trash' ) )
			unset( $actions['view'] );

		// Only show the actions if the user is capable of viewing them
		if ( current_user_can( 'moderate', $reply->ID ) ) {
			if ( in_array( $reply->post_status, array( bbp_get_public_status_id(), bbp_get_spam_status_id() ) ) ) {
				$spam_uri  = wp_nonce_url( add_query_arg( array( 'reply_id' => $reply->ID, 'action' => 'bbp_toggle_reply_spam' ), remove_query_arg( array( 'bbp_reply_toggle_notice', 'reply_id', 'failed', 'super' ) ) ), 'spam-reply_'  . $reply->ID );
				if ( bbp_is_reply_spam( $reply->ID ) ) {
					$actions['spam'] = '<a href="' . esc_url( $spam_uri ) . '" title="' . esc_attr__( 'Mark the reply as not spam', 'bbpress' ) . '">' . esc_html__( 'Not spam', 'bbpress' ) . '</a>';
				} else {
					$actions['spam'] = '<a href="' . esc_url( $spam_uri ) . '" title="' . esc_attr__( 'Mark this reply as spam',    'bbpress' ) . '">' . esc_html__( 'Spam',     'bbpress' ) . '</a>';
				}
			}
		}

		// Trash
		if ( current_user_can( 'delete_reply', $reply->ID ) ) {
			if ( bbp_get_trash_status_id() === $reply->post_status ) {
				$post_type_object   = get_post_type_object( bbp_get_reply_post_type() );
				$actions['untrash'] = "<a title='" . esc_attr__( 'Restore this item from the Trash', 'bbpress' ) . "' href='" . add_query_arg( array( '_wp_http_referer' => add_query_arg( array( 'post_type' => bbp_get_reply_post_type() ), admin_url( 'edit.php' ) ) ), wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $reply->ID ) ), 'untrash-' . $reply->post_type . '_' . $reply->ID ) ) . "'>" . esc_html__( 'Restore', 'bbpress' ) . "</a>";
			} elseif ( EMPTY_TRASH_DAYS ) {
				$actions['trash'] = "<a class='submitdelete' title='" . esc_attr__( 'Move this item to the Trash', 'bbpress' ) . "' href='" . add_query_arg( array( '_wp_http_referer' => add_query_arg( array( 'post_type' => bbp_get_reply_post_type() ), admin_url( 'edit.php' ) ) ), get_delete_post_link( $reply->ID ) ) . "'>" . esc_html__( 'Trash', 'bbpress' ) . "</a>";
			}

			if ( bbp_get_trash_status_id() === $reply->post_status || !EMPTY_TRASH_DAYS ) {
				$actions['delete'] = "<a class='submitdelete' title='" . esc_attr__( 'Delete this item permanently', 'bbpress' ) . "' href='" . add_query_arg( array( '_wp_http_referer' => add_query_arg( array( 'post_type' => bbp_get_reply_post_type() ), admin_url( 'edit.php' ) ) ), get_delete_post_link( $reply->ID, '', true ) ) . "'>" . esc_html__( 'Delete Permanently', 'bbpress' ) . "</a>";
			} elseif ( bbp_get_spam_status_id() === $reply->post_status ) {
				unset( $actions['trash'] );
			}
		}

		return $actions;
	}

	/**
	 * Add forum dropdown to topic and reply list table filters
	 *
	 * @since bbPress (r2991)
	 *
	 * @uses bbp_get_reply_post_type() To get the reply post type
	 * @uses bbp_get_topic_post_type() To get the topic post type
	 * @uses bbp_dropdown() To generate a forum dropdown
	 * @return bool False. If post type is not topic or reply
	 */
	public function filter_dropdown() {

		if ( $this->bail() ) return;

		// Add Empty Spam button
		if ( !empty( $_GET['post_status'] ) && ( bbp_get_spam_status_id() === $_GET['post_status'] ) && current_user_can( 'moderate' ) ) {
			wp_nonce_field( 'bulk-destroy', '_destroy_nonce' );
			$title = esc_attr__( 'Empty Spam', 'bbpress' );
			submit_button( $title, 'button-secondary apply', 'delete_all', false );
		}

		// Get which forum is selected
		$selected = !empty( $_GET['bbp_forum_id'] ) ? $_GET['bbp_forum_id'] : '';

		// Show the forums dropdown
		bbp_dropdown( array(
			'selected'  => $selected,
			'show_none' => __( 'In all forums', 'bbpress' )
		) );
	}

	/**
	 * Adjust the request query and include the forum id
	 *
	 * @since bbPress (r2991)
	 *
	 * @param array $query_vars Query variables from {@link WP_Query}
	 * @uses is_admin() To check if it's the admin section
	 * @uses bbp_get_topic_post_type() To get the topic post type
	 * @uses bbp_get_reply_post_type() To get the reply post type
	 * @return array Processed Query Vars
	 */
	public function filter_post_rows( $query_vars ) {

		if ( $this->bail() ) return $query_vars;

		// Add post_parent query_var if one is present
		if ( !empty( $_GET['bbp_forum_id'] ) ) {
			$query_vars['meta_key']   = '_bbp_forum_id';
			$query_vars['meta_value'] = $_GET['bbp_forum_id'];
		}

		// Return manipulated query_vars
		return $query_vars;
	}

	/**
	 * Custom user feedback messages for reply post type
	 *
	 * @since bbPress (r3080)
	 *
	 * @global int $post_ID
	 * @uses bbp_get_topic_permalink()
	 * @uses wp_post_revision_title()
	 * @uses esc_url()
	 * @uses add_query_arg()
	 *
	 * @param array $messages
	 *
	 * @return array
	 */
	public function updated_messages( $messages ) {
		global $post_ID;

		if ( $this->bail() ) return $messages;

		// URL for the current topic
		$topic_url = bbp_get_topic_permalink( bbp_get_reply_topic_id( $post_ID ) );

		// Current reply's post_date
		$post_date = bbp_get_global_post_field( 'post_date', 'raw' );

		// Messages array
		$messages[$this->post_type] = array(
			0 =>  '', // Left empty on purpose

			// Updated
			1 =>  sprintf( __( 'Reply updated. <a href="%s">View topic</a>', 'bbpress' ), $topic_url ),

			// Custom field updated
			2 => __( 'Custom field updated.', 'bbpress' ),

			// Custom field deleted
			3 => __( 'Custom field deleted.', 'bbpress' ),

			// Reply updated
			4 => __( 'Reply updated.', 'bbpress' ),

			// Restored from revision
			// translators: %s: date and time of the revision
			5 => isset( $_GET['revision'] )
					? sprintf( __( 'Reply restored to revision from %s', 'bbpress' ), wp_post_revision_title( (int) $_GET['revision'], false ) )
					: false,

			// Reply created
			6 => sprintf( __( 'Reply created. <a href="%s">View topic</a>', 'bbpress' ), $topic_url ),

			// Reply saved
			7 => __( 'Reply saved.', 'bbpress' ),

			// Reply submitted
			8 => sprintf( __( 'Reply submitted. <a target="_blank" href="%s">Preview topic</a>', 'bbpress' ), esc_url( add_query_arg( 'preview', 'true', $topic_url ) ) ),

			// Reply scheduled
			9 => sprintf( __( 'Reply scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview topic</a>', 'bbpress' ),
					// translators: Publish box date format, see http://php.net/date
					date_i18n( __( 'M j, Y @ G:i', 'bbpress' ),
					strtotime( $post_date ) ),
					$topic_url ),

			// Reply draft updated
			10 => sprintf( __( 'Reply draft updated. <a target="_blank" href="%s">Preview topic</a>', 'bbpress' ), esc_url( add_query_arg( 'preview', 'true', $topic_url ) ) ),
		);

		return $messages;
	}
}
endif; // class_exists check

/**
 * Setup bbPress Replies Admin
 *
 * This is currently here to make hooking and unhooking of the admin UI easy.
 * It could use dependency injection in the future, but for now this is easier.
 *
 * @since bbPress (r2596)
 *
 * @uses BBP_Replies_Admin
 */
function bbp_admin_replies() {
	bbpress()->admin->replies = new BBP_Replies_Admin();
}
