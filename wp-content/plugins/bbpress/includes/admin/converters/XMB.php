<?php

/**
 * Implementation of XMB Forum converter.
 *
 * @since bbPress (r5143)
 * @link Codex Docs http://codex.bbpress.org/import-forums/xmb
 */
class XMB extends BBP_Converter_Base {

	/**
	 * Main Constructor
	 *
	 * @uses XMB::setup_globals()
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
			'from_tablename' => 'forums',
			'from_fieldname' => 'fid',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_forum_id'
		);

		// Forum parent id (If no parent, then 0, Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'forums',
			'from_fieldname'  => 'fup',
			'to_type'         => 'forum',
			'to_fieldname'    => '_bbp_forum_parent_id'
		);

		// Forum topic count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'forums',
			'from_fieldname' => 'threads',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_topic_count'
		);

		// Forum reply count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'forums',
			'from_fieldname' => 'posts',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_reply_count'
		);

		// Forum total topic count (Includes unpublished topics, Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'forums',
			'from_fieldname' => 'threads',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_total_topic_count'
		);

		// Forum total reply count (Includes unpublished replies, Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'forums',
			'from_fieldname' => 'posts',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_total_reply_count'
		);

		// Forum title.
		$this->field_map[] = array(
			'from_tablename' => 'forums',
			'from_fieldname' => 'name',
			'to_type'        => 'forum',
			'to_fieldname'   => 'post_title'
		);

		// Forum slug (Clean name to avoid conflicts)
		$this->field_map[] = array(
			'from_tablename'  => 'forums',
			'from_fieldname'  => 'name',
			'to_type'         => 'forum',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Forum description.
		$this->field_map[] = array(
			'from_tablename'  => 'forums',
			'from_fieldname'  => 'description',
			'to_type'         => 'forum',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_null'
		);

		// Forum display order (Starts from 1)
		$this->field_map[] = array(
			'from_tablename' => 'forums',
			'from_fieldname' => 'displayorder',
			'to_type'        => 'forum',
			'to_fieldname'   => 'menu_order'
		);

		// Forum type (Category = 'group', Forum = 'forum' or 'sub', Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'forums',
			'from_fieldname'  => 'type',
			'to_type'         => 'forum',
			'to_fieldname'    => '_bbp_forum_type',
			'callback_method' => 'callback_forum_type'
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
			'from_tablename' => 'threads',
			'from_fieldname' => 'tid',
			'to_type'        => 'topic',
			'to_fieldname'   => '_bbp_topic_id'
		);

		// Topic reply count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'threads',
			'from_fieldname'  => 'replies',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_reply_count',
			'callback_method' => 'callback_topic_reply_count'
		);

		// Topic total reply count (Includes unpublished replies, Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'threads',
			'from_fieldname'  => 'replies',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_total_reply_count',
			'callback_method' => 'callback_topic_reply_count'
		);

		// Topic parent forum id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'threads',
			'from_fieldname'  => 'fid',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_forum_id',
			'callback_method' => 'callback_forumid'
		);

		// Topic author.
		// Note: We join the 'members' table because 'threads' table does not have numerical author id.
		$this->field_map[] = array(
			'from_tablename'  => 'members',
			'from_fieldname'  => 'uid',
			'join_tablename'  => 'threads',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON threads.author = members.username',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_author',
			'callback_method' => 'callback_userid'
		);

		// Topic Author ip (Stored in postmeta)
		// Note: We join the 'posts' table because 'threads' table does not have author ip.
		$this->field_map[] = array(
			'from_tablename'  => 'posts',
			'from_fieldname'  => 'useip',
			'join_tablename'  => 'threads',
			'join_type'       => 'INNER',
			'join_expression' => 'USING (tid) WHERE posts.subject != ""',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_author_ip'
		);

		// Topic content.
		// Note: We join the 'posts' table because 'threads' table does not have content.
		$this->field_map[] = array(
			'from_tablename'  => 'posts',
			'from_fieldname'  => 'message',
			'join_tablename'  => 'threads',
			'join_type'       => 'INNER',
			'join_expression' => 'USING (tid) WHERE posts.subject != ""',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_html'
		);

		// Topic title.
		$this->field_map[] = array(
			'from_tablename' => 'threads',
			'from_fieldname' => 'subject',
			'to_type'        => 'topic',
			'to_fieldname'   => 'post_title'
		);

		// Topic slug (Clean name to avoid conflicts)
		$this->field_map[] = array(
			'from_tablename'  => 'threads',
			'from_fieldname'  => 'subject',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Topic parent forum id (If no parent, then 0)
		$this->field_map[] = array(
			'from_tablename'  => 'threads',
			'from_fieldname'  => 'fid',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_parent',
			'callback_method' => 'callback_forumid'
		);

		// Topic status (Open or Closed, XMB v1.9.11.13 ''=open & 'yes'=closed)
		$this->field_map[] = array(
			'from_tablename'  => 'threads',
			'from_fieldname'  => 'closed',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_status',
			'callback_method' => 'callback_topic_status'
		);

		// Sticky status (Stored in postmeta))
		$this->field_map[] = array(
			'from_tablename'  => 'threads',
			'from_fieldname'  => 'topped',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_old_sticky_status',
			'callback_method' => 'callback_sticky_status'
		);

		// Topic dates.
		// Note: We join the 'posts' table because 'threads' table does not include dates.
		$this->field_map[] = array(
			'from_tablename'  => 'posts',
			'from_fieldname'  => 'dateline',
			'join_tablename'  => 'threads',
			'join_type'       => 'INNER',
			'join_expression' => 'USING (tid) WHERE posts.subject != ""',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_date',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'posts',
			'from_fieldname'  => 'dateline',
			'join_tablename'  => 'threads',
			'join_type'       => 'INNER',
			'join_expression' => 'USING (tid) WHERE posts.subject != ""',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_date_gmt',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'posts',
			'from_fieldname'  => 'dateline',
			'join_tablename'  => 'threads',
			'join_type'       => 'INNER',
			'join_expression' => 'USING (tid) WHERE posts.subject != ""',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_modified',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'posts',
			'from_fieldname'  => 'dateline',
			'join_tablename'  => 'threads',
			'join_type'       => 'INNER',
			'join_expression' => 'USING (tid) WHERE posts.subject != ""',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_modified_gmt',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'posts',
			'from_fieldname'  => 'dateline',
			'join_tablename'  => 'threads',
			'join_type'       => 'INNER',
			'join_expression' => 'USING (tid) WHERE posts.subject != ""',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_last_active_time',
			'callback_method' => 'callback_datetime'
		);

		/** Tags Section ******************************************************/

		/**
		 * XMB v1.9.11.13 Forums do not support topic tags out of the box
		 */

		/** Reply Section *****************************************************/

		// Reply id (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'posts',
			'from_fieldname' => 'pid',
			'to_type'        => 'reply',
			'to_fieldname'   => '_bbp_post_id'
		);

		// Reply parent forum id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'posts',
			'from_fieldname'  => 'fid',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_forum_id',
			'callback_method' => 'callback_topicid_to_forumid'
		);

		// Reply parent topic id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'posts',
			'from_fieldname'  => 'tid',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_topic_id',
			'callback_method' => 'callback_topicid'
		);

		// Reply author ip (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'posts',
			'from_fieldname' => 'useip',
			'to_type'        => 'reply',
			'to_fieldname'   => '_bbp_author_ip'
		);

		// Reply author.
		// Note: We join the 'members' table because 'posts' table does not have numerical author id.
		$this->field_map[] = array(
			'from_tablename'  => 'members',
			'from_fieldname'  => 'uid',
			'join_tablename'  => 'posts',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON posts.author = members.username',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_author',
			'callback_method' => 'callback_userid'
		);

		// Reply title.
		// Note: We join the 'threads' table because 'posts' table does not have topic title.
		$this->field_map[] = array(
			'from_tablename'  => 'threads',
			'from_fieldname'  => 'subject',
			'join_tablename'  => 'posts',
			'join_type'       => 'INNER',
			'join_expression' => 'USING (tid) WHERE posts.subject = ""',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_title',
			'callback_method' => 'callback_reply_title'
		);

		// Reply slug (Clean name to avoid conflicts)
		// Note: We join the 'threads' table because 'posts' table does not have topic title.
		$this->field_map[] = array(
			'from_tablename'  => 'threads',
			'from_fieldname'  => 'subject',
			'join_tablename'  => 'posts',
			'join_type'       => 'INNER',
			'join_expression' => 'USING (tid) WHERE posts.subject = ""',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Reply content.
		$this->field_map[] = array(
			'from_tablename'  => 'posts',
			'from_fieldname'  => 'message',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_html'
		);

		// Reply parent topic id (If no parent, then 0)
		$this->field_map[] = array(
			'from_tablename'  => 'posts',
			'from_fieldname'  => 'tid',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_parent',
			'callback_method' => 'callback_topicid'
		);

		// Reply dates.
		$this->field_map[] = array(
			'from_tablename'  => 'posts',
			'from_fieldname'  => 'dateline',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_date',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'posts',
			'from_fieldname'  => 'dateline',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_date_gmt',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'posts',
			'from_fieldname'  => 'dateline',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_modified',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'posts',
			'from_fieldname'  => 'dateline',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_modified_gmt',
			'callback_method' => 'callback_datetime'
		);

		/** User Section ******************************************************/

		// Store old User id (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'members',
			'from_fieldname' => 'uid',
			'to_type'        => 'user',
			'to_fieldname'   => '_bbp_user_id'
		);

		// Store old User password (Stored in usermeta serialized with salt)
		$this->field_map[] = array(
			'from_tablename'  => 'members',
			'from_fieldname'  => 'password',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_password',
			'callback_method' => 'callback_savepass'
		);

		// Store old User Salt (This is only used for the SELECT row info for the above password save)
//		$this->field_map[] = array(
//			'from_tablename' => 'members',
//			'from_fieldname' => 'salt',
//			'to_type'        => 'user',
//			'to_fieldname'   => ''
//		);

		// User password verify class (Stored in usermeta for verifying password)
		$this->field_map[] = array(
			'to_type'      => 'members',
			'to_fieldname' => '_bbp_class',
			'default'      => 'XMB'
		);

		// User name.
		$this->field_map[] = array(
			'from_tablename' => 'members',
			'from_fieldname' => 'username',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_login'
		);

		// User nice name.
		$this->field_map[] = array(
			'from_tablename' => 'members',
			'from_fieldname' => 'username',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_nicename'
		);

		// User email.
		$this->field_map[] = array(
			'from_tablename' => 'members',
			'from_fieldname' => 'email',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_email'
		);

		// User homepage.
		$this->field_map[] = array(
			'from_tablename' => 'members',
			'from_fieldname' => 'site',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_url'
		);

		// User registered.
		$this->field_map[] = array(
			'from_tablename'  => 'members',
			'from_fieldname'  => 'regdate',
			'to_type'         => 'user',
			'to_fieldname'    => 'user_registered',
			'callback_method' => 'callback_datetime'
		);

		// User AIM (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'members',
			'from_fieldname' => 'aim',
			'to_type'        => 'user',
			'to_fieldname'   => 'aim'
		);

		// User Yahoo (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'members',
			'from_fieldname' => 'yahoo',
			'to_type'        => 'user',
			'to_fieldname'   => 'yim'
		);

		// Store ICQ (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'members',
			'from_fieldname' => 'icq',
			'to_type'        => 'user',
			'to_fieldname'   => '_bbp_xmb_user_icq'
		);

		// Store MSN (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'members',
			'from_fieldname' => 'msn',
			'to_type'        => 'user',
			'to_fieldname'   => '_bbp_xmb_user_msn'
		);

		// Store Signature (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename'  => 'members',
			'from_fieldname'  => 'sig',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_xmb_user_sig',
			'callback_method' => 'callback_html'
		);

		// Store Bio (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename'  => 'members',
			'from_fieldname'  => 'bio',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_xmb_user_bio',
			'callback_method' => 'callback_html'
		);

		// Store Location (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'members',
			'from_fieldname' => 'location',
			'to_type'        => 'user',
			'to_fieldname'   => '_bbp_xmb_user_location'
		);

		// Store Avatar (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'members',
			'from_fieldname' => 'avatar',
			'to_type'        => 'user',
			'to_fieldname'   => '_bbp_xmb_user_avatar'
		);

		// Store Mood (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'members',
			'from_fieldname' => 'mood',
			'to_type'        => 'user',
			'to_fieldname'   => '_bbp_xmb_user_mood'
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
	 * Translate the forum type from XMB v1.9.11.13 Capitalised case to WordPress's non-capatilise case strings.
	 *
	 * @param int $status XMB v1.9.11.13 numeric forum type
	 * @return string WordPress safe
	 */
	public function callback_forum_type( $status = 1 ) {
		switch ( $status ) {
			case 'group' :
				$status = 'category';
				break;

			case 'sub' :
				$status = 'forum';
				break;

			case 'forum' :
			default :
				$status = 'forum';
				break;
		}
		return $status;
	}

	/**
	 * Translate the post status from XMB v1.9.11.13 numeric's to WordPress's strings.
	 *
	 * @param int $status XMB v1.9.11.13 numeric topic status
	 * @return string WordPress safe
	 */
	public function callback_topic_status( $status = '' ) {
		switch ( $status ) {
			case 'yes' :
				$status = 'closed';
				break;

			case ''  :
			default :
				$status = 'publish';
				break;
		}
		return $status;
	}

	/**
	 * Translate the topic sticky status type from XMB v1.9.11.13 numeric's to WordPress's strings.
	 *
	 * @param int $status XMB v1.9.11.13 numeric forum type
	 * @return string WordPress safe
	 */
	public function callback_sticky_status( $status = 0 ) {
		switch ( $status ) {
			case 1 :
				$status = 'sticky';       // XMB Sticky 'topped = 1'
				break;

			case 0  :
			default :
				$status = 'normal';       // XMB Normal Topic 'topped = 0'
				break;
		}
		return $status;
	}

	/**
	 * Verify the topic/reply count.
	 *
	 * @param int $count XMB v1.9.11.13 topic/reply counts
	 * @return string WordPress safe
	 */
	public function callback_topic_reply_count( $count = 1 ) {
		$count = absint( (int) $count - 1 );
		return $count;
	}

	/**
	 * Set the reply title
	 *
	 * @param string $title XMB v1.9.11.13 topic title of this reply
	 * @return string Prefixed topic title, or empty string
	 */
	public function callback_reply_title( $title = '' ) {
		$title = !empty( $title ) ? __( 'Re: ', 'bbpress' ) . html_entity_decode( $title ) : '';
		return $title;
	}

}