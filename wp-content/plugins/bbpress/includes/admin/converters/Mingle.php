<?php

/**
 * Implementation of Mingle Forums converter.
 *
 * @since bbPress (r4691)
 * @link Codex Docs http://codex.bbpress.org/import-forums/mingle
 */
class Mingle extends BBP_Converter_Base {

	/**
	 * Main constructor
	 *
	 * @uses Mingle::setup_globals()
	 */
	function __construct() {
		parent::__construct();
		$this->setup_globals();
	}

	/**
	 * Sets up the field mappings
	 */
	public function setup_globals()	{

		/** Forum Section ******************************************************/

		// Forum id (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'forum_forums',
			'from_fieldname' => 'id',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_forum_id'
		);

		// Forum parent id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'forum_forums',
			'from_fieldname' => 'parent_id',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_forum_parent_id'
		);

		// Forum title.
		$this->field_map[] = array(
			'from_tablename' => 'forum_forums',
			'from_fieldname' => 'name',
			'to_type'        => 'forum',
			'to_fieldname'   => 'post_title'
		);

		// Forum slug (Clean name to avoid confilcts)
		$this->field_map[] = array(
			'from_tablename'  => 'forum_forums',
			'from_fieldname'  => 'name',
			'to_type'         => 'forum',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);
		// Forum description.
		$this->field_map[] = array(
			'from_tablename'  => 'forum_forums',
			'from_fieldname'  => 'description',
			'to_type'         => 'forum',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_null'
		);

		// Forum display order (Starts from 1)
		$this->field_map[] = array(
			'from_tablename' => 'forum_forums',
			'from_fieldname' => 'sort',
			'to_type'        => 'forum',
			'to_fieldname'   => 'menu_order'
		);

		// Forum dates.
		$this->field_map[] = array(
			'to_type'        => 'forum',
			'to_fieldname'   => 'post_date',
			'default'        => date('Y-m-d H:i:s')
		);
		$this->field_map[] = array(
			'to_type'        => 'forum',
			'to_fieldname'   => 'post_date_gmt',
			'default'        => date('Y-m-d H:i:s')
		);
		$this->field_map[] = array(
			'to_type'        => 'forum',
			'to_fieldname'   => 'post_modified',
			'default'        => date('Y-m-d H:i:s')
		);
		$this->field_map[] = array(
			'to_type'        => 'forum',
			'to_fieldname'   => 'post_modified_gmt',
			'default'        => date('Y-m-d H:i:s')
		);

		/** Topic Section ******************************************************/

		// Topic id (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'forum_threads',
			'from_fieldname' => 'id',
			'to_type'        => 'topic',
			'to_fieldname'   => '_bbp_topic_id'
		);

		// Topic parent forum id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'forum_threads',
			'from_fieldname'  => 'parent_id',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_forum_id',
			'callback_method' => 'callback_forumid'
		);

		// Topic author.
		$this->field_map[] = array(
			'from_tablename'  => 'forum_threads',
			'from_fieldname'  => 'starter',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_author',
			'callback_method' => 'callback_userid'
		);

		// Topic content.
		// Note: We join the forum_posts table because forum_topics do not have topic content.
		$this->field_map[] = array(
			'from_tablename'  => 'forum_posts',
			'from_fieldname'  => 'text',
			'join_tablename'  => 'forum_threads',
			'join_type'       => 'INNER',
			'join_expression' => 'ON forum_posts.parent_id = forum_threads.id GROUP BY forum_threads.id',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_html'
		);
		// Topic title.
		$this->field_map[] = array(
			'from_tablename' => 'forum_threads',
			'from_fieldname' => 'subject',
			'to_type'        => 'topic',
			'to_fieldname'   => 'post_title'
		);

		// Topic slug (Clean name to avoid conflicts)
		$this->field_map[] = array(
			'from_tablename'  => 'forum_threads',
			'from_fieldname'  => 'subject',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Topic parent forum id (If no parent, then 0)
		$this->field_map[] = array(
			'from_tablename'  => 'forum_threads',
			'from_fieldname'  => 'parent_id',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_parent',
			'callback_method' => 'callback_forumid'
		);

		// Sticky status (Stored in postmeta))
		$this->field_map[] = array(
			'from_tablename'  => 'forum_threads',
			'from_fieldname'  => 'status',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_old_sticky_status',
			'callback_method' => 'callback_sticky_status'
		);

		// Topic dates.
		$this->field_map[] = array(
			'from_tablename'  => 'forum_threads',
			'from_fieldname'  => 'date',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_date'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'forum_threads',
			'from_fieldname'  => 'date',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_date_gmt'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'forum_threads',
			'from_fieldname'  => 'last_post',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_modified'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'forum_threads',
			'from_fieldname'  => 'last_post',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_modified_gmt'
		);
		$this->field_map[] = array(
			'from_tablename' => 'forum_threads',
			'from_fieldname' => 'last_post',
			'to_type'        => 'topic',
			'to_fieldname'   => '_bbp_last_active_time'
		);

		// Topic status (Open or Closed)
		$this->field_map[] = array(
			'from_tablename'  => 'forum_threads',
			'from_fieldname'  => 'closed',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_status',
			'callback_method' => 'callback_topic_status'
		);

		/** Tags Section ******************************************************/

		/**
		 * Mingle Forums do not support topic tags
         */

		/** Reply Section ******************************************************/

		// Reply id (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'forum_posts',
			'from_fieldname' => 'id',
			'to_type'        => 'reply',
			'to_fieldname'   => '_bbp_post_id'
		);

		// Setup reply section table joins
		// We need join the 'forum_threads' table to only import replies
		$this->field_map[] = array(
			'from_tablename'  => 'forum_threads',
			'from_fieldname'  => 'date',
			'join_tablename'  => 'forum_posts',
			'join_type'       => 'INNER',
			'join_expression' => 'ON forum_posts.parent_id = forum_threads.id',
			'from_expression' => 'WHERE forum_threads.subject != forum_posts.subject',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_last_active_time'
		);

		// Reply parent forum id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'forum_posts',
			'from_fieldname'  => 'parent_id',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_forum_id',
			'callback_method' => 'callback_topicid_to_forumid'
		);

		// Reply parent topic id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'forum_posts',
			'from_fieldname'  => 'parent_id',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_topic_id',
			'callback_method' => 'callback_topicid'
		);

		// Reply author.
		$this->field_map[] = array(
			'from_tablename'  => 'forum_posts',
			'from_fieldname'  => 'author_id',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_author',
			'callback_method' => 'callback_userid'
		);

		// Reply title.
		$this->field_map[] = array(
			'from_tablename' => 'forum_posts',
			'from_fieldname' => 'subject',
			'to_type'        => 'reply',
			'to_fieldname'   => 'post_title'
		);

		// Reply slug (Clean name to avoid conflicts)
		$this->field_map[] = array(
			'from_tablename'  => 'forum_posts',
			'from_fieldname'  => 'subject',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Reply content.
		$this->field_map[] = array(
			'from_tablename'  => 'forum_posts',
			'from_fieldname'  => 'text',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_html'
		);

		// Reply parent topic id (If no parent, then 0)
		$this->field_map[] = array(
			'from_tablename'  => 'forum_posts',
			'from_fieldname'  => 'parent_id',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_parent',
			'callback_method' => 'callback_topicid'
		);

		// Reply dates.
		$this->field_map[] = array(
			'from_tablename'  => 'forum_posts',
			'from_fieldname'  => 'date',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_date'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'forum_posts',
			'from_fieldname'  => 'date',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_date_gmt'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'forum_posts',
			'from_fieldname'  => 'date',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_modified'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'forum_posts',
			'from_fieldname'  => 'date',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_modified_gmt'
		);

		/** User Section ******************************************************/

		// Store old User id (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'users',
			'from_fieldname' => 'ID',
			'to_type'        => 'user',
			'to_fieldname'   => '_bbp_user_id'
		);

		// Store old User password (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'users',
			'from_fieldname' => 'user_pass',
			'to_type'        => 'user',
			'to_fieldname'   => '_bbp_password'
		);

		// User name.
		$this->field_map[] = array(
			'from_tablename' => 'users',
			'from_fieldname' => 'user_login',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_login'
		);

		// User nice name.
		$this->field_map[] = array(
			'from_tablename' => 'users',
			'from_fieldname' => 'user_nicename',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_nicename'
		);

		// User email.
		$this->field_map[] = array(
			'from_tablename' => 'users',
			'from_fieldname' => 'user_email',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_email'
		);

		// User homepage.
		$this->field_map[] = array(
			'from_tablename' => 'users',
			'from_fieldname' => 'user_url',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_url'
		);

		// User registered.
		$this->field_map[] = array(
			'from_tablename' => 'users',
			'from_fieldname' => 'user_registered',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_registered'
		);

		// User status.
		$this->field_map[] = array(
			'from_tablename' => 'users',
			'from_fieldname' => 'user_status',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_status'
		);

		// User display name.
		$this->field_map[] = array(
			'from_tablename' => 'users',
			'from_fieldname' => 'display_name',
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
	 * Translate the topic status from Mingle numeric's to WordPress's strings.
	 *
	 * @param int $status Mingle v1.x numeric topic status
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
	 * Translate the topic sticky status type from Mingle numeric's to WordPress's strings.
	 *
	 * @param int $status Mingle numeric forum type
	 * @return string WordPress safe
	 */
	public function callback_sticky_status( $status = 0 ) {
		switch ( $status ) {
			case 'sticky' :
				$status = 'sticky';       // Mingle Sticky 'status = sticky'
				break;

			case 'open'  :
			default :
				$status = 'normal';       // Mingle Normal Topic 'status = open'
				break;
		}
		return $status;
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
