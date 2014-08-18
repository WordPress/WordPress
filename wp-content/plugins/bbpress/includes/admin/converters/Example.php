<?php

/**
 * Example converter base impoprter template for bbPress
 *
 * @since bbPress (r4689)
 * @link Codex Docs http://codex.bbpress.org/import-forums/custom-import
 */
class Example extends BBP_Converter_Base {

	/**
	 * Main Constructor
	 *
	 * @uses Example_Converter::setup_globals()
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

		// Setup table joins for the forum section at the base of this section

		// Forum id (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'forums_table',
			'from_fieldname'  => 'the_forum_id',
			'to_type'         => 'forum',
			'to_fieldname'    => '_bbp_forum_id'
		);

		// Forum parent id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'forums_table',
			'from_fieldname'  => 'the_parent_id',
			'to_type'         => 'forum',
			'to_fieldname'    => '_bbp_forum_parent_id'
		);

		// Forum topic count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'forums_table',
			'from_fieldname' => 'the_topic_count',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_topic_count'
		);

		// Forum reply count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'forums_table',
			'from_fieldname' => 'the_reply_count',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_reply_count'
		);

		// Forum total topic count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'forums_table',
			'from_fieldname' => 'the_total_topic_count',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_total_topic_count'
		);

		// Forum total reply count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'forums_table',
			'from_fieldname' => 'the_total_reply_count',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_total_reply_count'
		);

		// Forum title.
		$this->field_map[] = array(
			'from_tablename'  => 'forums_table',
			'from_fieldname'  => 'the_forum_title',
			'to_type'         => 'forum',
			'to_fieldname'    => 'post_title'
		);

		// Forum slug (Clean name to avoid confilcts)
		$this->field_map[] = array(
			'from_tablename'  => 'forums_table',
			'from_fieldname'  => 'the_forum_slug',
			'to_type'         => 'forum',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Forum description.
		$this->field_map[] = array(
			'from_tablename'  => 'forums_table',
			'from_fieldname'  => 'the_forum_description',
			'to_type'         => 'forum',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_null'
		);

		// Forum display order (Starts from 1)
		$this->field_map[] = array(
			'from_tablename'  => 'forums_table',
			'from_fieldname'  => 'the_forum_order',
			'to_type'         => 'forum',
			'to_fieldname'    => 'menu_order'
		);

		// Forum dates.
		$this->field_map[] = array(
			'to_type'         => 'forum',
			'to_fieldname'    => 'post_date',
			'default' => date('Y-m-d H:i:s')
		);
		$this->field_map[] = array(
			'to_type'         => 'forum',
			'to_fieldname'    => 'post_date_gmt',
			'default' => date('Y-m-d H:i:s')
		);
		$this->field_map[] = array(
			'to_type'         => 'forum',
			'to_fieldname'    => 'post_modified',
			'default' => date('Y-m-d H:i:s')
		);
		$this->field_map[] = array(
			'to_type'         => 'forum',
			'to_fieldname'    => 'post_modified_gmt',
			'default' => date('Y-m-d H:i:s')
		);

		// Setup the table joins for the forum section
		$this->field_map[] = array(
			'from_tablename'  => 'groups_table',
			'from_fieldname'  => 'forum_id',
			'join_tablename'  => 'forums_table',
			'join_type'       => 'INNER',
			'join_expression' => 'USING groups_table.forum_id = forums_table.forum_id',
		//	'from_expression' => 'WHERE forums_table.forum_id != 1',
			'to_type'         => 'forum'
		);

		/** Topic Section *****************************************************/

		// Setup table joins for the topic section at the base of this section

		// Topic id (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'topics_table',
			'from_fieldname'  => 'the_topic_id',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_topic_id'
		);

		// Topic reply count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'topics_table',
			'from_fieldname'  => 'the_topic_reply_count',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_reply_count',
			'callback_method' => 'callback_topic_reply_count'
		);

		// Topic total reply count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'topics_table',
			'from_fieldname'  => 'the_total_topic_reply_count',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_total_reply_count',
			'callback_method' => 'callback_topic_reply_count'
		);

		// Topic parent forum id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'topics_table',
			'from_fieldname'  => 'the_topic_parent_forum_id',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_forum_id',
			'callback_method' => 'callback_forumid'
		);

		// Topic author.
		$this->field_map[] = array(
			'from_tablename'  => 'topics_table',
			'from_fieldname'  => 'the_topic_author_id',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_author',
			'callback_method' => 'callback_userid'
		);

		// Topic author ip (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'topics_table',
			'from_fieldname'  => 'the_topic_author_ip_address',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_author_ip'
		);

		// Topic content.
		$this->field_map[] = array(
			'from_tablename'  => 'topics_table',
			'from_fieldname'  => 'the_topic_content',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_html'
		);

		// Topic title.
		$this->field_map[] = array(
			'from_tablename'  => 'topics_table',
			'from_fieldname'  => 'the_topic_title',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_title'
		);

		// Topic slug (Clean name to avoid conflicts)
		$this->field_map[] = array(
			'from_tablename'  => 'topics_table',
			'from_fieldname'  => 'the_topic_slug',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Topic parent forum id (If no parent, then 0)
		$this->field_map[] = array(
			'from_tablename'  => 'topics_table',
			'from_fieldname'  => 'the_topic_parent_forum_id',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_parent',
			'callback_method' => 'callback_forumid'
		);

		// Sticky status (Stored in postmeta))
		$this->field_map[] = array(
			'from_tablename'  => 'topics_table',
			'from_fieldname'  => 'the_topic_sticky_status',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_old_sticky_status',
			'callback_method' => 'callback_sticky_status'
		);

		// Topic dates.
		$this->field_map[] = array(
			'from_tablename'  => 'topics_table',
			'from_fieldname'  => 'the_topic_creation_date',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_date',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'topics_table',
			'from_fieldname'  => 'the_topic_creation_date',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_date_gmt',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'topics_table',
			'from_fieldname'  => 'the_topic_modified_date',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_modified',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'topics_table',
			'from_fieldname'  => 'the_topic_modified_date',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_modified_gmt',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'topics_table',
			'from_fieldname'  => 'the_topic_modified_date',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_last_active_time',
			'callback_method' => 'callback_datetime'
		);

		// Setup any table joins needed for the topic section
		$this->field_map[] = array(
			'from_tablename'  => 'replies_table',
			'from_fieldname'  => 'the_topic_id',
			'join_tablename'  => 'topics_table',
			'join_type'       => 'INNER',
			'join_expression' => 'USING replies_table.the_topic_id = topics_table.the_topic_id',
			'from_expression' => 'WHERE forums_table.the_topic_id = 0',
			'to_type'         => 'topic'
		);

		/** Tags Section ******************************************************/

		// Setup table joins for the tag section at the base of this section
		// Setup any table joins needed for the tags section
		$this->field_map[] = array(
			'from_tablename'  => 'tag_table',
			'from_fieldname'  => 'the_topic_id',
			'join_tablename'  => 'tagcontent_table',
			'join_type'       => 'INNER',
			'join_expression' => 'USING tagcontent_table.tag_id = tags_table.tag_id',
			'from_expression' => 'WHERE tagcontent_table.tag_id = tag_table.tag_id',
			'to_type'         => 'tags'
		);

		// Topic id.
		$this->field_map[] = array(
			'from_tablename'  => 'tagcontent_table',
			'from_fieldname'  => 'contentid',
			'to_type'         => 'tags',
			'to_fieldname'    => 'objectid',
			'callback_method' => 'callback_topicid'
		);

		// Taxonomy ID.
		$this->field_map[] = array(
			'from_tablename'  => 'tagcontent_table',
			'from_fieldname'  => 'tagid',
			'to_type'         => 'tags',
			'to_fieldname'    => 'taxonomy'
		);

		// Term text.
		$this->field_map[] = array(
			'from_tablename'  => 'tag_table',
			'from_fieldname'  => 'tagtext',
			'to_type'         => 'tags',
			'to_fieldname'    => 'name'
		);

		// Term slug.
		$this->field_map[] = array(
			'from_tablename'  => 'tag_table',
			'from_fieldname'  => 'tagslug',
			'to_type'         => 'tags',
			'to_fieldname'    => 'slug',
			'callback_method' => 'callback_slug'
		);

		// Term description.
		$this->field_map[] = array(
			'from_tablename'  => 'tag_table',
			'from_fieldname'  => 'tagdescription',
			'to_type'         => 'tags',
			'to_fieldname'    => 'description'
		);

		/** Reply Section *****************************************************/

		// Setup table joins for the reply section at the base of this section

		// Reply id (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'replies_table',
			'from_fieldname'  => 'the_reply_id',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_post_id'
		);

		// Reply parent forum id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'replies_table',
			'from_fieldname'  => 'the_reply_parent_forum_id',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_forum_id',
			'callback_method' => 'callback_topicid_to_forumid'
		);

		// Reply parent topic id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'replies_table',
			'from_fieldname'  => 'the_reply_parent_topic_id',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_topic_id',
			'callback_method' => 'callback_topicid'
		);

		// Reply author ip (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'replies_table',
			'from_fieldname'  => 'the_reply_author_ip_address',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_author_ip'
		);

		// Reply author.
		$this->field_map[] = array(
			'from_tablename'  => 'replies_table',
			'from_fieldname'  => 'the_reply_author_id',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_author',
			'callback_method' => 'callback_userid'
		);

		// Reply title.
		$this->field_map[] = array(
			'from_tablename'  => 'replies_table',
			'from_fieldname'  => 'the_reply_title',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_title'
		);

		// Reply slug (Clean name to avoid conflicts)
		$this->field_map[] = array(
			'from_tablename'  => 'replies_table',
			'from_fieldname'  => 'the_reply_slug',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Reply content.
		$this->field_map[] = array(
			'from_tablename'  => 'replies_table',
			'from_fieldname'  => 'the_reply_content',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_html'
		);

		// Reply order.
		$this->field_map[] = array(
			'from_tablename'  => 'replies_table',
			'from_fieldname'  => 'the_reply_order',
			'to_type'         => 'reply',
			'to_fieldname'    => 'menu_order'
		);

		// Reply parent topic id (If no parent, then 0)
		$this->field_map[] = array(
			'from_tablename'  => 'replies_table',
			'from_fieldname'  => 'the_reply_parent_topic_id',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_parent',
			'callback_method' => 'callback_topicid'
		);

		// Reply dates.
		$this->field_map[] = array(
			'from_tablename'  => 'replies_table',
			'from_fieldname'  => 'the_reply_creation_date',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_date',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'replies_table',
			'from_fieldname'  => 'the_reply_creation_date',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_date_gmt',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'replies_table',
			'from_fieldname'  => 'the_reply_modified_date',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_modified',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'replies_table',
			'from_fieldname'  => 'the_reply_modified_date',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_modified_gmt',
			'callback_method' => 'callback_datetime'
		);

		// Setup any table joins needed for the reply section
		$this->field_map[] = array(
			'from_tablename'  => 'topics_table',
			'from_fieldname'  => 'the_topic_id',
			'join_tablename'  => 'replies_table',
			'join_type'       => 'INNER',
			'join_expression' => 'USING topics_table.the_topic_id = replies_table.the_topic_id',
			'from_expression' => 'WHERE topics_table.first_post != 0',
			'to_type'         => 'reply'
		);

		/** User Section ******************************************************/

		// Setup table joins for the user section at the base of this section

		// Store old User id (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename'  => 'users_table',
			'from_fieldname'  => 'the_users_id',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_user_id'
		);

		// Store old User password (Stored in usermeta serialized with salt)
		$this->field_map[] = array(
			'from_tablename'  => 'users_table',
			'from_fieldname'  => 'the_users_password',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_password',
			'callback_method' => 'callback_savepass'
		);

		// Store old User Salt (This is only used for the SELECT row info for the above password save)
		$this->field_map[] = array(
			'from_tablename'  => 'users_table',
			'from_fieldname'  => 'the_users_password_salt',
			'to_type'         => 'user',
			'to_fieldname'    => ''
		);

		// User password verify class (Stored in usermeta for verifying password)
		$this->field_map[] = array(
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_class',
			'default' => 'Example'
		);

		// User name.
		$this->field_map[] = array(
			'from_tablename'  => 'users_table',
			'from_fieldname'  => 'the_users_username',
			'to_type'         => 'user',
			'to_fieldname'    => 'user_login'
		);

		// User nice name.
		$this->field_map[] = array(
			'from_tablename' => 'users_table',
			'from_fieldname' => 'the_users_nicename',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_nicename'
		);

		// User email.
		$this->field_map[] = array(
			'from_tablename'  => 'users_table',
			'from_fieldname'  => 'the_users_email_address',
			'to_type'         => 'user',
			'to_fieldname'    => 'user_email'
		);

		// User homepage.
		$this->field_map[] = array(
			'from_tablename'  => 'users_table',
			'from_fieldname'  => 'the_users_homepage_url',
			'to_type'         => 'user',
			'to_fieldname'    => 'user_url'
		);

		// User registered.
		$this->field_map[] = array(
			'from_tablename'  => 'users_table',
			'from_fieldname'  => 'the_users_registration_date',
			'to_type'         => 'user',
			'to_fieldname'    => 'user_registered',
			'callback_method' => 'callback_datetime'
		);

		// User status.
		$this->field_map[] = array(
			'from_tablename' => 'users_table',
			'from_fieldname' => 'the_users_status',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_status'
		);

		// User display name.
		$this->field_map[] = array(
			'from_tablename' => 'users_table',
			'from_fieldname' => 'the_users_display_name',
			'to_type'        => 'user',
			'to_fieldname'   => 'display_name'
		);

		// User AIM (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename'  => 'users_table',
			'from_fieldname'  => 'the_users_aim',
			'to_type'         => 'user',
			'to_fieldname'    => 'aim'
		);

		// User Yahoo (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename'  => 'users_table',
			'from_fieldname'  => 'the_users_yahoo',
			'to_type'         => 'user',
			'to_fieldname'    => 'yim'
		);

		// User Jabber (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'users_table',
			'from_fieldname' => 'the_users_jabber',
			'to_type'        => 'user',
			'to_fieldname'   => 'jabber'
		);

		// Setup any table joins needed for the user section
		$this->field_map[] = array(
			'from_tablename'  => 'users_profile_table',
			'from_fieldname'  => 'the_users_id',
			'join_tablename'  => 'users_table',
			'join_type'       => 'INNER',
			'join_expression' => 'USING users_profile_table.the_user_id = users_table.the_user_id',
			'from_expression' => 'WHERE users_table.the_user_id != -1',
			'to_type'         => 'user'
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
	 * This method is to save the salt and password together.  That
	 * way when we authenticate it we can get it out of the database
	 * as one value. Array values are auto sanitized by WordPress.
	 */
	public function callback_savepass( $field, $row ) {
		$pass_array = array( 'hash' => $field, 'salt' => $row['salt'] );
		return $pass_array;
	}

	/**
	 * This method is to take the pass out of the database and compare
	 * to a pass the user has typed in.
	 */
	public function authenticate_pass( $password, $serialized_pass ) {
		$pass_array = unserialize( $serialized_pass );
		return ( $pass_array['hash'] == md5( md5( $password ). $pass_array['salt'] ) );
	}
}
