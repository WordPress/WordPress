<?php

/**
 * Implementation of Vanilla 2.0.18.1 Converter
 *
 * @since bbPress (r4717)
 * @link Codex Docs http://codex.bbpress.org/import-forums/vanilla
 */
class Vanilla extends BBP_Converter_Base {

	/**
	 * Main Constructor
	 *
	 * @uses Vanilla::setup_globals()
	 */
	function __construct() {
		parent::__construct();
		$this->setup_globals();
	}

	/**
	 * Sets up the field mappings
	 */
	public function setup_globals() {

		/** Forum Section *****************************************************/

		// Forum id (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'Category',
			'from_fieldname'  => 'CategoryID',
			'from_expression' => 'WHERE Category.CategoryID > 0',
			'to_type'         => 'forum',
			'to_fieldname'    => '_bbp_forum_id'
		);

		// Forum parent id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'Category',
			'from_fieldname'  => 'ParentCategoryID',
			'to_type'         => 'forum',
			'to_fieldname'    => '_bbp_forum_parent_id',
			'callback_method' => 'callback_forum_parent'
		);

		// Forum topic count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'Category',
			'from_fieldname' => 'CountDiscussions',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_topic_count'
		);

		// Forum reply count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'Category',
			'from_fieldname' => 'CountComments',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_reply_count'
		);

		// Forum total topic count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'Category',
			'from_fieldname' => 'CountDiscussions',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_total_topic_count'
		);

		// Forum total reply count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'Category',
			'from_fieldname' => 'CountComments',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_total_reply_count'
		);

		// Forum title.
		$this->field_map[] = array(
			'from_tablename' => 'Category',
			'from_fieldname' => 'Name',
			'to_type'        => 'forum',
			'to_fieldname'   => 'post_title'
		);

		// Forum slug (Clean name to avoid confilcts)
		$this->field_map[] = array(
			'from_tablename'  => 'Category',
			'from_fieldname'  => 'Name',
			'to_type'         => 'forum',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Forum description.
		$this->field_map[] = array(
			'from_tablename'  => 'Category',
			'from_fieldname'  => 'Description',
			'to_type'         => 'forum',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_null'
		);

		// Forum display order (Starts from 1)
		$this->field_map[] = array(
			'from_tablename' => 'Category',
			'from_fieldname' => 'Sort',
			'to_type'        => 'forum',
			'to_fieldname'   => 'menu_order'
		);

		// Forum dates.
		$this->field_map[] = array(
			'from_tablename' => 'Category',
			'from_fieldname' => 'DateInserted',
			'to_type'        => 'forum',
			'to_fieldname'   => 'post_date',
		);
		$this->field_map[] = array(
			'from_tablename' => 'Category',
			'from_fieldname' => 'DateInserted',
			'to_type'        => 'forum',
			'to_fieldname'   => 'post_date_gmt',
		);
		$this->field_map[] = array(
			'from_tablename' => 'Category',
			'from_fieldname' => 'DateUpdated',
			'to_type'        => 'forum',
			'to_fieldname'   => 'post_modified',
		);
		$this->field_map[] = array(
			'from_tablename' => 'Category',
			'from_fieldname' => 'DateUpdated',
			'to_type'        => 'forum',
			'to_fieldname'   => 'post_modified_gmt',
		);

		/** Topic Section *****************************************************/

		// Topic id (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'Discussion',
			'from_fieldname' => 'DiscussionID',
			'to_type'        => 'topic',
			'to_fieldname'   => '_bbp_topic_id'
		);

		// Topic reply count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'Discussion',
			'from_fieldname'  => 'CountComments',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_reply_count',
			'callback_method' => 'callback_topic_reply_count'
		);

		// Topic total reply count (Includes unpublished replies, Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'Discussion',
			'from_fieldname'  => 'CountComments',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_total_reply_count',
			'callback_method' => 'callback_topic_reply_count'
		);

		// Topic parent forum id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'Discussion',
			'from_fieldname'  => 'CategoryID',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_forum_id',
			'callback_method' => 'callback_forumid'
		);

		// Topic author.
		$this->field_map[] = array(
			'from_tablename'  => 'Discussion',
			'from_fieldname'  => 'InsertUserID',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_author',
			'callback_method' => 'callback_userid'
		);

		// Topic title.
		$this->field_map[] = array(
			'from_tablename' => 'Discussion',
			'from_fieldname' => 'Name',
			'to_type'        => 'topic',
			'to_fieldname'   => 'post_title'
		);

		// Topic slug (Clean name to avoid conflicts)
		$this->field_map[] = array(
			'from_tablename'  => 'Discussion',
			'from_fieldname'  => 'Name',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Topic content.
		$this->field_map[] = array(
			'from_tablename'  => 'Discussion',
			'from_fieldname'  => 'Body',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_html'
		);

		// Topic status (Open or Closed)
		$this->field_map[] = array(
			'from_tablename'  => 'Discussion',
			'from_fieldname'  => 'closed',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_status',
			'callback_method' => 'callback_topic_status'
		);

		// Topic author ip (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'Discussion',
			'from_fieldname'  => 'InsertIPAddress',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_author_ip'
		);

		// Topic parent forum id (If no parent, then 0)
		$this->field_map[] = array(
			'from_tablename'  => 'Discussion',
			'from_fieldname'  => 'CategoryID',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_parent',
			'callback_method' => 'callback_forumid'
		);

		// Topic dates.
		$this->field_map[] = array(
			'from_tablename' => 'Discussion',
			'from_fieldname' => 'DateInserted',
			'to_type'        => 'topic',
			'to_fieldname'   => 'post_date'
		);
		$this->field_map[] = array(
			'from_tablename' => 'Discussion',
			'from_fieldname' => 'DateInserted',
			'to_type'        => 'topic',
			'to_fieldname'   => 'post_date_gmt'
		);
		$this->field_map[] = array(
			'from_tablename' => 'Discussion',
			'from_fieldname' => 'DateUpdated',
			'to_type'        => 'topic',
			'to_fieldname'   => 'post_modified'
		);
		$this->field_map[] = array(
			'from_tablename' => 'Discussion',
			'from_fieldname' => 'DateUpdated',
			'to_type'        => 'topic',
			'to_fieldname'   => 'post_modified_gmt'
		);
		$this->field_map[] = array(
			'from_tablename' => 'Discussion',
			'from_fieldname' => 'DateLastComment',
			'to_type'        => 'topic',
			'to_fieldname'   => '_bbp_last_active_time'
		);

		/** Tags Section ******************************************************/

		// Topic id.
		$this->field_map[] = array(
			'from_tablename'  => 'TagDiscussion',
			'from_fieldname'  => 'DiscussionID',
			'to_type'         => 'tags',
			'to_fieldname'    => 'objectid',
			'callback_method' => 'callback_topicid'
		);

		// Taxonomy ID.
		$this->field_map[] = array(
			'from_tablename'  => 'TagDiscussion',
			'from_fieldname'  => 'TagID',
			'to_type'         => 'tags',
			'to_fieldname'    => 'taxonomy'
		);

		// Term text.
		$this->field_map[] = array(
			'from_tablename'  => 'Tag',
			'from_fieldname'  => 'Name',
			'join_tablename'  => 'TagDiscussion',
			'join_type'       => 'INNER',
			'join_expression' => 'USING (tagid)',
			'to_type'         => 'tags',
			'to_fieldname'    => 'name'
		);

		/** Reply Section *****************************************************/

		// Reply id (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'Comment',
			'from_fieldname'  => 'CommentID',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_post_id'
		);

		// Reply parent topic id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'Comment',
			'from_fieldname'  => 'DiscussionID',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_topic_id',
			'callback_method' => 'callback_topicid'
		);

		// Reply parent forum id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'Discussion',
			'from_fieldname'  => 'CategoryID',
			'join_tablename'  => 'Comment',
			'join_type'       => 'INNER',
			'join_expression' => 'USING (DiscussionID)',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_forum_id',
			'callback_method' => 'callback_topicid_to_forumid'
		);

		// Reply title.
		// Note: We join the Discussion table because Comment table does not include topic title.
		$this->field_map[] = array(
			'from_tablename'  => 'Discussion',
			'from_fieldname'  => 'Name',
			'join_tablename'  => 'Comment',
			'join_type'       => 'INNER',
			'join_expression' => 'USING (DiscussionID)',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_title',
			'callback_method' => 'callback_reply_title'
		);

		// Reply slug (Clean name to avoid conflicts)
		// Note: We join the Discussion table because Comment table does not include topic title.
		$this->field_map[] = array(
			'from_tablename'  => 'Discussion',
			'from_fieldname'  => 'Name',
			'join_tablename'  => 'Comment',
			'join_type'       => 'INNER',
			'join_expression' => 'USING (DiscussionID)',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Reply author ip (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'Comment',
			'from_fieldname' => 'InsertIPAddress',
			'to_type'        => 'reply',
			'to_fieldname'   => '_bbp_author_ip'
		);

		// Reply author.
		$this->field_map[] = array(
			'from_tablename'  => 'Comment',
			'from_fieldname'  => 'InsertUserID',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_author',
			'callback_method' => 'callback_userid'
		);

		// Reply content.
		$this->field_map[] = array(
			'from_tablename'  => 'Comment',
			'from_fieldname'  => 'Body',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_html'
		);

		// Reply parent topic id (If no parent, then 0)
		$this->field_map[] = array(
			'from_tablename'  => 'Comment',
			'from_fieldname'  => 'DiscussionID',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_parent',
			'callback_method' => 'callback_topicid'
		);

		// Reply dates.
		$this->field_map[] = array(
			'from_tablename' => 'Comment',
			'from_fieldname' => 'DateInserted',
			'to_type'        => 'reply',
			'to_fieldname'   => 'post_date'
		);
		$this->field_map[] = array(
			'from_tablename' => 'Comment',
			'from_fieldname' => 'DateInserted',
			'to_type'        => 'reply',
			'to_fieldname'   => 'post_date_gmt'
		);
		$this->field_map[] = array(
			'from_tablename' => 'Comment',
			'from_fieldname' => 'DateUpdated',
			'to_type'        => 'reply',
			'to_fieldname'   => 'post_modified'
		);
		$this->field_map[] = array(
			'from_tablename' => 'Comment',
			'from_fieldname' => 'DateUpdated',
			'to_type'        => 'reply',
			'to_fieldname'   => 'post_modified_gmt'
		);

		/** User Section ******************************************************/

		// Store old User id (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename'  => 'User',
			'from_fieldname'  => 'UserID',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_user_id'
		);

		// Store old User password (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'User',
			'from_fieldname' => 'Password',
			'to_type'        => 'user',
			'to_fieldname'   => '_bbp_password'
		);

		// User name.
		$this->field_map[] = array(
			'from_tablename' => 'User',
			'from_fieldname' => 'Name',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_login'
		);

		// User nice name.
		$this->field_map[] = array(
			'from_tablename' => 'User',
			'from_fieldname' => 'Name',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_nicename'
		);

		// User email.
		$this->field_map[] = array(
			'from_tablename' => 'User',
			'from_fieldname' => 'Email',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_email'
		);

		// User registered.
		$this->field_map[] = array(
			'from_tablename' => 'User',
			'from_fieldname' => 'DateInserted',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_registered'
		);

		// Display Name
		$this->field_map[] = array(
			'from_tablename' => 'User',
			'from_fieldname' => 'Name',
			'to_type'        => 'user',
			'to_fieldname'   => 'display_name'
		);
	}

	/**
	 * This method allows us to indicates what is or is not converted for each
	 * converter.
	 */
	public function info() {
		return '';
	}

	/**
	 * Translate the topic status from Vanilla v2.x numeric's to WordPress's strings.
	 *
	 * @param int $status Vanilla v2.x numeric topic status
	 * @return string WordPress safe
	 */
	public function callback_topic_status( $status = 0 ) {
		switch ( $status ) {
			case 1 :
				$status = 'closed';
				break;

			case 0  :
			default :
				$status = 'publish';
				break;
		}
		return $status;
	}

	/**
	 * Clean Root Parent ID -1 to 0
	 *
	 * @param int $parent Vanilla v2.x Parent ID
	 * @return int
	 */
	public function callback_forum_parent( $parent = 0 ) {
		if ( $parent == -1 ) {
			return 0;
		} else {
			return $parent;
		}
	}

	/**
	 * Verify the topic reply count.
	 *
	 * @param int $count Vanilla v2.x reply count
	 * @return string WordPress safe
	 */
	public function callback_topic_reply_count( $count = 1 ) {
		$count = absint( (int) $count - 1 );
		return $count;
	}

	/**
	 * Set the reply title
	 *
	 * @param string $title Vanilla v2.x topic title of this reply
	 * @return string Prefixed topic title, or empty string
	 */
	public function callback_reply_title( $title = '' ) {
		$title = !empty( $title ) ? __( 'Re: ', 'bbpress' ) . html_entity_decode( $title ) : '';
		return $title;
	}

	/**
	 * This method is to save the salt and password together. That
	 * way when we authenticate it we can get it out of the database
	 * as one value. Array values are auto sanitized by WordPress.
	 */
	public function callback_savepass( $field, $row ) {
		return false;
	}

	/**
	 * This method is to take the pass out of the database and compare
	 * to a pass the user has typed in.
	 */
	public function authenticate_pass( $password, $serialized_pass ) {
		return false;
	}

	/**
	* This callback processes any custom BBCodes with parser.php
	*/
	protected function callback_html( $field ) {
		require_once( bbpress()->admin->admin_dir . 'parser.php' );
		$bbcode = BBCode::getInstance();
		$bbcode->enable_smileys = false;
		$bbcode->smiley_regex   = false;
		return html_entity_decode( $bbcode->Parse( $field ) );
	}
}
