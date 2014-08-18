<?php

/**
 * Implementation of Kunena v3.x Forums for Joomla Forum converter.
 *
 * @since bbPress (r5144)
 * @link Codex Docs http://codex.bbpress.org/import-forums/kunena/
 */
class Kunena3 extends BBP_Converter_Base {

	/**
	 * Main Constructor
	 *
	 * @uses Kunena3::setup_globals()
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
			'from_tablename' => 'kunena_categories',
			'from_fieldname' => 'id',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_forum_id'
		);

		// Forum parent id (If no parent, then 0, Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_categories',
			'from_fieldname'  => 'parent_id',
			'to_type'         => 'forum',
			'to_fieldname'    => '_bbp_forum_parent_id'
		);

		// Forum topic count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'kunena_categories',
			'from_fieldname' => 'numTopics',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_topic_count'
		);

		// Forum reply count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'kunena_categories',
			'from_fieldname' => 'numPosts',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_reply_count'
		);

		// Forum total topic count (Includes unpublished topics, Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_categories',
			'from_fieldname'  => 'numTopics',
			'to_type'         => 'forum',
			'to_fieldname'    => '_bbp_total_topic_count'
		);

		// Forum total reply count (Includes unpublished replies, Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_categories',
			'from_fieldname'  => 'numPosts',
			'to_type'         => 'forum',
			'to_fieldname'    => '_bbp_total_reply_count'
		);

		// Forum title.
		$this->field_map[] = array(
			'from_tablename' => 'kunena_categories',
			'from_fieldname' => 'name',
			'to_type'        => 'forum',
			'to_fieldname'   => 'post_title'
		);

		// Forum slug (Clean name to avoid conflicts)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_categories',
			'from_fieldname'  => 'alias',
			'to_type'         => 'forum',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Forum description.
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_categories',
			'from_fieldname'  => 'description',
			'to_type'         => 'forum',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_null'
		);

		// Forum display order (Starts from 1)
		$this->field_map[] = array(
			'from_tablename' => 'kunena_categories',
			'from_fieldname' => 'ordering',
			'to_type'        => 'forum',
			'to_fieldname'   => 'menu_order'
		);

		// Forum type (Category = 0 or Forum = >0, Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_categories',
			'from_fieldname'  => 'parent_id',
			'to_type'         => 'forum',
			'to_fieldname'    => '_bbp_forum_type',
			'callback_method' => 'callback_forum_type'
		);

		// Forum status (Open = 0 or Closed = 1, Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_categories',
			'from_fieldname'  => 'locked',
			'to_type'         => 'forum',
			'to_fieldname'    => '_bbp_status',
			'callback_method' => 'callback_forum_status'
		);

		// Forum dates.
		$this->field_map[] = array(
			'to_type'      => 'forum',
			'to_fieldname' => 'post_date',
			'default'      => date('Y-m-d H:i:s')
		);
		$this->field_map[] = array(
			'to_type'      => 'forum',
			'to_fieldname' => 'post_date_gmt',
			'default'      => date('Y-m-d H:i:s')
		);
		$this->field_map[] = array(
			'to_type'      => 'forum',
			'to_fieldname' => 'post_modified',
			'default'      => date('Y-m-d H:i:s')
		);
		$this->field_map[] = array(
			'to_type'      => 'forum',
			'to_fieldname' => 'post_modified_gmt',
			'default'      => date('Y-m-d H:i:s')
		);

		/** Topic Section *****************************************************/

		// Topic id (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'kunena_topics',
			'from_fieldname' => 'id',
			'to_type'        => 'topic',
			'to_fieldname'   => '_bbp_topic_id'
		);

		// Topic reply count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_topics',
			'from_fieldname'  => 'posts',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_reply_count',
			'callback_method' => 'callback_topic_reply_count'
		);

		// Topic total reply count (Includes unpublished replies, Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_topics',
			'from_fieldname'  => 'posts',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_total_reply_count',
			'callback_method' => 'callback_topic_reply_count'
		);

		// Topic parent forum id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_topics',
			'from_fieldname'  => 'category_id',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_forum_id',
			'callback_method' => 'callback_forumid'
		);

		// Topic author.
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_topics',
			'from_fieldname'  => 'first_post_userid',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_author',
			'callback_method' => 'callback_userid'
		);

		// Topic Author ip (Stored in postmeta)
		// Note: We join the 'kunena_messages' table because 'kunena_topics' table does not include author ip.
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_messages',
			'from_fieldname'  => 'ip',
			'join_tablename'  => 'kunena_topics',
			'join_type'       => 'LEFT',
			'join_expression' => 'USING (id)',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_author_ip'
		);

		// Topic content.
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_topics',
			'from_fieldname'  => 'first_post_message',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_html'
		);

		// Topic title.
		$this->field_map[] = array(
			'from_tablename' => 'kunena_topics',
			'from_fieldname' => 'subject',
			'to_type'        => 'topic',
			'to_fieldname'   => 'post_title'
		);

		// Topic slug (Clean name to avoid conflicts)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_topics',
			'from_fieldname'  => 'subject',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Topic parent forum id (If no parent, then 0)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_topics',
			'from_fieldname'  => 'category_id',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_parent',
			'callback_method' => 'callback_forumid'
		);

		// Topic dates.
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_topics',
			'from_fieldname'  => 'first_post_time',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_date',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_topics',
			'from_fieldname'  => 'first_post_time',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_date_gmt',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_topics',
			'from_fieldname'  => 'last_post_time',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_modified',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_topics',
			'from_fieldname'  => 'last_post_time',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_modified_gmt',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_topics',
			'from_fieldname'  => 'last_post_time',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_last_active_time',
			'callback_method' => 'callback_datetime'
		);

		// Topic status (Open or Closed, Kunena v3.x 0=open & 1=closed)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_topics',
			'from_fieldname'  => 'locked',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_status',
			'callback_method' => 'callback_topic_status'
		);

		/** Tags Section ******************************************************/

		/**
		 * Kunena v3.x Forums do not support topic tags out of the box
		 */

		/** Reply Section ******************************************************/

		// Reply id (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_messages',
			'from_fieldname'  => 'id',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_post_id'
		);

		// Reply parent forum id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_messages',
			'from_fieldname'  => 'catid',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_forum_id',
			'callback_method' => 'callback_topicid_to_forumid'
		);

		// Reply parent topic id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_messages',
			'from_fieldname'  => 'thread',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_topic_id',
			'callback_method' => 'callback_topicid'
		);

		// Reply author ip (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'kunena_messages',
			'from_fieldname' => 'ip',
			'to_type'        => 'reply',
			'to_fieldname'   => '_bbp_author_ip'
		);

		// Reply author.
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_messages',
			'from_fieldname'  => 'userid',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_author',
			'callback_method' => 'callback_userid'
		);

		// Reply title.
		$this->field_map[] = array(
			'from_tablename' => 'kunena_messages',
			'from_fieldname' => 'subject',
			'to_type'        => 'reply',
			'to_fieldname'   => 'post_title',
			'callback_method' => 'callback_reply_title'
		);

		// Reply slug (Clean name to avoid conflicts)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_messages',
			'from_fieldname'  => 'subject',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Reply content.
		// Note: We join the 'kunena_messages_text' table because 'kunena_messages' table does not include reply content.
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_messages_text',
			'from_fieldname'  => 'message',
			'join_tablename'  => 'kunena_messages',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON kunena_messages_text.mesid = kunena_messages.id LEFT JOIN jos_kunena_topics AS kunena_topics ON kunena_messages.thread = kunena_topics.id WHERE kunena_messages.parent != 0',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_html'
		);

		// Reply parent topic id (If no parent, then 0)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_messages',
			'from_fieldname'  => 'thread',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_parent',
			'callback_method' => 'callback_topicid'
		);

		// Reply dates.
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_messages',
			'from_fieldname'  => 'time',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_date',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_messages',
			'from_fieldname'  => 'time',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_date_gmt',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_messages',
			'from_fieldname'  => 'time',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_modified',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_messages',
			'from_fieldname'  => 'time',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_modified_gmt',
			'callback_method' => 'callback_datetime'
		);

		/** User Section ******************************************************/

		//Note: We are importing the Joomla User details and the Kunena v3.x user profile details.

		// Store old User id (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'users',
			'from_fieldname' => 'id',
			'to_type'        => 'user',
			'to_fieldname'   => '_bbp_user_id'
		);

		// Store old User password (Stored in usermeta serialized with salt)
		$this->field_map[] = array(
			'from_tablename'  => 'users',
			'from_fieldname'  => 'password',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_password',
			'callback_method' => 'callback_savepass'
		);

		// Store old User Salt (This is only used for the SELECT row info for the above password save)
//		$this->field_map[] = array(
//			'from_tablename' => 'users',
//			'from_fieldname' => 'salt',
//			'to_type'        => 'user',
//			'to_fieldname'   => ''
//		);

		// User password verify class (Stored in usermeta for verifying password)
		$this->field_map[] = array(
			'to_type'      => 'users',
			'to_fieldname' => '_bbp_class',
			'default'      => 'Kunena3'
		);

		// User name.
		$this->field_map[] = array(
			'from_tablename' => 'users',
			'from_fieldname' => 'username',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_login'
		);

		// User nice name.
		$this->field_map[] = array(
			'from_tablename' => 'users',
			'from_fieldname' => 'username',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_nicename'
		);

		// User email.
		$this->field_map[] = array(
			'from_tablename' => 'users',
			'from_fieldname' => 'email',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_email'
		);

		// User homepage.
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_users',
			'from_fieldname'  => 'websiteurl',
			'join_tablename'  => 'users',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON kunena_users.userid = users.id',
			'to_type'         => 'user',
			'to_fieldname'    => 'user_url'
		);

		// User registered.
		$this->field_map[] = array(
			'from_tablename'  => 'users',
			'from_fieldname'  => 'registerDate',
			'to_type'         => 'user',
			'to_fieldname'    => 'user_registered',
			'callback_method' => 'callback_datetime'
		);

		// User display name.
		$this->field_map[] = array(
			'from_tablename' => 'users',
			'from_fieldname' => 'name',
			'to_type'        => 'user',
			'to_fieldname'   => 'display_name'
		);

		// User AIM (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_users',
			'from_fieldname'  => 'aim',
			'join_tablename'  => 'users',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON kunena_users.userid = users.id',
			'to_type'         => 'user',
			'to_fieldname'    => 'aim'
		);

		// User Yahoo (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_users',
			'from_fieldname'  => 'yim',
			'join_tablename'  => 'users',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON kunena_users.userid = users.id',
			'to_type'         => 'user',
			'to_fieldname'    => 'yim'
		);

		// Store Google Tak (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_users',
			'from_fieldname'  => 'gtalk',
			'join_tablename'  => 'users',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON kunena_users.userid = users.id',
			'to_type'         => 'user',
			'to_fieldname'    => 'jabber'
		);

		// Store ICQ (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_users',
			'from_fieldname'  => 'icq',
			'join_tablename'  => 'users',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON kunena_users.userid = users.id',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_kunena3_user_icq'
		);

		// Store MSN (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_users',
			'from_fieldname'  => 'msn',
			'join_tablename'  => 'users',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON kunena_users.userid = users.id',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_kunena3_user_msn'
		);

		// Store Skype (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_users',
			'from_fieldname'  => 'skype',
			'join_tablename'  => 'users',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON kunena_users.userid = users.id',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_kunena3_user_skype'
		);

		// Store Twitter (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_users',
			'from_fieldname'  => 'twitter',
			'join_tablename'  => 'users',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON kunena_users.userid = users.id',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_kunena3_user_twitter'
		);

		// Store Facebook (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_users',
			'from_fieldname'  => 'facebook',
			'join_tablename'  => 'users',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON kunena_users.userid = users.id',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_kunena3_user_facebook'
		);

		// Store myspace (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_users',
			'from_fieldname'  => 'myspace',
			'join_tablename'  => 'users',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON kunena_users.userid = users.id',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_kunena3_user_myspace'
		);

		// Store linkedin (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_users',
			'from_fieldname'  => 'linkedin',
			'join_tablename'  => 'users',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON kunena_users.userid = users.id',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_kunena3_user_linkedin'
		);

		// Store delicious (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_users',
			'from_fieldname'  => 'delicious',
			'join_tablename'  => 'users',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON kunena_users.userid = users.id',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_kunena3_user_delicious'
		);

		// Store friendfeed (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_users',
			'from_fieldname'  => 'friendfeed',
			'join_tablename'  => 'users',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON kunena_users.userid = users.id',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_kunena3_user_friendfeed'
		);

		// Store digg (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_users',
			'from_fieldname'  => 'digg',
			'join_tablename'  => 'users',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON kunena_users.userid = users.id',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_kunena3_user_digg'
		);

		// Store blogspot (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_users',
			'from_fieldname'  => 'blogspot',
			'join_tablename'  => 'users',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON kunena_users.userid = users.id',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_kunena3_user_blogspot'
		);

		// Store flickr (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_users',
			'from_fieldname'  => 'flickr',
			'join_tablename'  => 'users',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON kunena_users.userid = users.id',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_kunena3_user_flickr'
		);

		// Store bebo (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_users',
			'from_fieldname'  => 'bebo',
			'join_tablename'  => 'users',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON kunena_users.userid = users.id',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_kunena3_user_bebo'
		);

		// Store websitename (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_users',
			'from_fieldname'  => 'websitename',
			'join_tablename'  => 'users',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON kunena_users.userid = users.id',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_kunena3_user_websitename'
		);

		// Store location (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_users',
			'from_fieldname'  => 'location',
			'join_tablename'  => 'users',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON kunena_users.userid = users.id',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_kunena3_user_location'
		);

		// Store Signature (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_users',
			'from_fieldname'  => 'signature',
			'join_tablename'  => 'users',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON kunena_users.userid = users.id',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_kunena3_user_sig',
			'callback_method' => 'callback_html'
		);
	}

	/**
	 * This method allows us to indicates what is or is not converted for each
	 * converter.
	 */
	public function info()
	{
		return '';
	}

	/**
	 * This method is to save the salt and password together.  That
	 * way when we authenticate it we can get it out of the database
	 * as one value. Array values are auto sanitized by WordPress.
	 */
	public function callback_savepass( $field, $row )
	{
		$pass_array = array( 'hash' => $field, 'salt' => $row['salt'] );
		return $pass_array;
	}

	/**
	 * This method is to take the pass out of the database and compare
	 * to a pass the user has typed in.
	 */
	public function authenticate_pass( $password, $serialized_pass )
	{
		$pass_array = unserialize( $serialized_pass );
		return ( $pass_array['hash'] == md5( md5( $password ). $pass_array['salt'] ) );
	}

	/**
	 * Translate the forum type from Kunena v3.x numeric's to WordPress's strings.
	 *
	 * @param int $status Kunena v3.x numeric forum type
	 * @return string WordPress safe
	 */
	public function callback_forum_type( $status = 0 ) {
		if ( $status == 0 ) {
			$status = 'category';
		} else {
			$status = 'forum';
		}
		return $status;
	}

	/**
	 * Translate the forum status from Kunena v3.x numeric's to WordPress's strings.
	 *
	 * @param int $status Kunena v3.x numeric forum status
	 * @return string WordPress safe
	 */
	public function callback_forum_status( $status = 0 ) {
		switch ( $status ) {
			case 1 :
				$status = 'closed';
				break;

			case 0  :
			default :
				$status = 'open';
				break;
		}
		return $status;
	}

	/**
	 * Translate the post status from Kunena v3.x numeric's to WordPress's strings.
	 *
	 * @param int $status Kunena v3.x numeric topic status
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
	 * Verify the topic/reply count.
	 *
	 * @param int $count Kunena v3.x topic/reply counts
	 * @return string WordPress safe
	 */
	public function callback_topic_reply_count( $count = 1 ) {
		$count = absint( (int) $count - 1 );
		return $count;
	}

	/**
	 * Set the reply title
	 *
	 * @param string $title Kunena v3.x topic title of this reply
	 * @return string Prefixed topic title, or empty string
	 */
	public function callback_reply_title( $title = '' ) {
		$title = !empty( $title ) ? __( 'Re: ', 'bbpress' ) . html_entity_decode( $title ) : '';
		return $title;
	}
}