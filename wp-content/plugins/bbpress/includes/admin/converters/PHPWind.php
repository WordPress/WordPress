<?php

/**
 * Implementation of PHPWind Forum converter.
 *
 * @since bbPress (r5142)
 * @link Codex Docs http://codex.bbpress.org/import-forums/phpwind
 */
class PHPWind extends BBP_Converter_Base {

	/**
	 * Main Constructor
	 *
	 * @uses PHPWind::setup_globals()
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
			'from_tablename' => 'bbs_forum',
			'from_fieldname' => 'fid',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_forum_id'
		);

		// Forum parent id (If no parent, then 0, Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_forum',
			'from_fieldname'  => 'parentid',
			'to_type'         => 'forum',
			'to_fieldname'    => '_bbp_forum_parent_id'
		);

		// Forum topic count (Stored in postmeta)
		// Note: We join the 'bbs_forum_statistics' table because 'bbs_forum' table does not include topic and reply counts.
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_forum_statistics',
			'from_fieldname'  => 'threads',
			'join_tablename'  => 'bbs_forum',
			'join_type'       => 'LEFT',
			'join_expression' => 'USING (fid)',
			'to_type'         => 'forum',
			'to_fieldname'    => '_bbp_topic_count'
		);

		// Forum reply count (Stored in postmeta)
		// Note: We join the 'bbs_forum_statistics' table because 'bbs_forum' table does not include topic and reply counts.
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_forum_statistics',
			'from_fieldname'  => 'posts',
			'join_tablename'  => 'bbs_forum',
			'join_type'       => 'LEFT',
			'join_expression' => 'USING (fid)',
			'to_type'         => 'forum',
			'to_fieldname'    => '_bbp_reply_count'
		);

		// Forum total topic count (Stored in postmeta)
		// Note: We join the 'bbs_forum_statistics' table because 'bbs_forum' table does not include topic and reply counts.
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_forum_statistics',
			'from_fieldname'  => 'threads',
			'join_tablename'  => 'bbs_forum',
			'join_type'       => 'LEFT',
			'join_expression' => 'USING (fid)',
			'to_type'         => 'forum',
			'to_fieldname'    => '_bbp_total_topic_count'
		);

		// Forum total reply count (Stored in postmeta)
		// Note: We join the 'bbs_forum_statistics' table because 'bbs_forum' table does not include topic and reply counts.
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_forum_statistics',
			'from_fieldname'  => 'posts',
			'join_tablename'  => 'bbs_forum',
			'join_type'       => 'LEFT',
			'join_expression' => 'USING (fid)',
			'to_type'         => 'forum',
			'to_fieldname'    => '_bbp_total_reply_count'
		);

		// Forum title.
		$this->field_map[] = array(
			'from_tablename' => 'bbs_forum',
			'from_fieldname' => 'name',
			'to_type'        => 'forum',
			'to_fieldname'   => 'post_title'
		);

		// Forum slug (Clean name to avoid conflicts)
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_forum',
			'from_fieldname'  => 'name',
			'to_type'         => 'forum',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Forum description.
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_forum',
			'from_fieldname'  => 'descrip',
			'to_type'         => 'forum',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_null'
		);

		// Forum display order (Starts from 1)
		$this->field_map[] = array(
			'from_tablename' => 'bbs_forum',
			'from_fieldname' => 'vieworder',
			'to_type'        => 'forum',
			'to_fieldname'   => 'menu_order'
		);

		// Forum type (Category = category or Forum = forum, sub or sub2,  Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_forum',
			'from_fieldname'  => 'type',
			'to_type'         => 'forum',
			'to_fieldname'    => '_bbp_forum_type',
			'callback_method' => 'callback_forum_type'
		);

		// Forum dates.
		$this->field_map[] = array(
			'to_type'      => 'bbs_forum',
			'to_fieldname' => 'post_date',
			'default'      => date('Y-m-d H:i:s')
		);
		$this->field_map[] = array(
			'to_type'      => 'bbs_forum',
			'to_fieldname' => 'post_date_gmt',
			'default'      => date('Y-m-d H:i:s')
		);
		$this->field_map[] = array(
			'to_type'      => 'bbs_forum',
			'to_fieldname' => 'post_modified',
			'default'      => date('Y-m-d H:i:s')
		);
		$this->field_map[] = array(
			'to_type'      => 'bbs_forum',
			'to_fieldname' => 'post_modified_gmt',
			'default'      => date('Y-m-d H:i:s')
		);

		/** Topic Section *****************************************************/

		// Topic id (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'bbs_threads',
			'from_fieldname' => 'tid',
			'to_type'        => 'topic',
			'to_fieldname'   => '_bbp_topic_id'
		);

		// Topic reply count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_threads',
			'from_fieldname'  => 'replies',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_reply_count',
			'callback_method' => 'callback_topic_reply_count'
		);

		// Topic total reply count (Includes unpublished replies, Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_threads',
			'from_fieldname'  => 'replies',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_total_reply_count',
			'callback_method' => 'callback_topic_reply_count'
		);

		// Topic parent forum id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_threads',
			'from_fieldname'  => 'fid',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_forum_id',
			'callback_method' => 'callback_forumid'
		);

		// Topic author.
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_threads',
			'from_fieldname'  => 'created_userid',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_author',
			'callback_method' => 'callback_userid'
		);

		// Topic Author ip (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_threads',
			'from_fieldname'  => 'created_ip',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_author_ip'
		);

		// Topic content.
		// Note: We join the 'bbs_threads_content' table because 'bbs_threads' table does not have content.
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_threads_content',
			'from_fieldname'  => 'content',
			'join_tablename'  => 'bbs_threads',
			'join_type'       => 'INNER',
			'join_expression' => 'USING (tid)',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_html'
		);

		// Topic title.
		$this->field_map[] = array(
			'from_tablename' => 'bbs_threads',
			'from_fieldname' => 'subject',
			'to_type'        => 'topic',
			'to_fieldname'   => 'post_title'
		);

		// Topic slug (Clean name to avoid conflicts)
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_threads',
			'from_fieldname'  => 'subject',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Topic parent forum id (If no parent, then 0)
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_threads',
			'from_fieldname'  => 'fid',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_parent',
			'callback_method' => 'callback_forumid'
		);

		// Topic dates.
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_threads',
			'from_fieldname'  => 'created_time',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_date',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_threads',
			'from_fieldname'  => 'created_time',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_date_gmt',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_threads',
			'from_fieldname'  => 'lastpost_time',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_modified',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_threads',
			'from_fieldname'  => 'lastpost_time',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_modified_gmt',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_threads',
			'from_fieldname'  => 'lastpost_time',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_last_active_time',
			'callback_method' => 'callback_datetime'
		);

		// Topic status (Open or Closed, PHPWind v9.x 0=no, 1=closed & 2=open)
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_threads',
			'from_fieldname'  => 'tpcstatus',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_status',
			'callback_method' => 'callback_topic_status'
		);

		/** Tags Section ******************************************************/

		/**
		 * PHPWind v9.x Forums do not support topic tags out of the box
		 */

		/** Reply Section *****************************************************/

		// Reply id (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'bbs_posts',
			'from_fieldname' => 'pid',
			'to_type'        => 'reply',
			'to_fieldname'   => '_bbp_post_id'
		);

		// Reply parent forum id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_posts',
			'from_fieldname'  => 'fid',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_forum_id',
			'callback_method' => 'callback_forumid'
		);

		// Reply parent topic id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_posts',
			'from_fieldname'  => 'tid',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_topic_id',
			'callback_method' => 'callback_topicid'
		);

		// Reply author ip (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'bbs_posts',
			'from_fieldname' => 'created_ip',
			'to_type'        => 'reply',
			'to_fieldname'   => '_bbp_author_ip'
		);

		// Reply author.
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_posts',
			'from_fieldname'  => 'created_userid',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_author',
			'callback_method' => 'callback_userid'
		);

		// Reply title.
		// Note: We join the 'bbs_threads' table because 'bbs_posts' table does not include reply title.
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_threads',
			'from_fieldname'  => 'subject',
			'join_tablename'  => 'bbs_posts',
			'join_type'       => 'LEFT',
			'join_expression' => 'USING (tid)',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_title'
		);

		// Reply slug (Clean name to avoid conflicts)
		// Note: We join the 'bbs_threads' table because 'bbs_posts' table does not include reply slug.
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_threads',
			'from_fieldname'  => 'subject',
			'join_tablename'  => 'bbs_posts',
			'join_type'       => 'LEFT',
			'join_expression' => 'USING (tid)',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Reply content.
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_posts',
			'from_fieldname'  => 'content',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_html'
		);

		// Reply parent topic id (If no parent, then 0)
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_posts',
			'from_fieldname'  => 'tid',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_parent',
			'callback_method' => 'callback_topicid'
		);

		// Reply dates.
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_posts',
			'from_fieldname'  => 'created_time',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_date',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_posts',
			'from_fieldname'  => 'created_time',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_date_gmt',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_posts',
			'from_fieldname'  => 'created_time',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_modified',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'bbs_posts',
			'from_fieldname'  => 'created_time',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_modified_gmt',
			'callback_method' => 'callback_datetime'
		);

		/** User Section ******************************************************/

		// Store old User id (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'user',
			'from_fieldname' => 'uid',
			'to_type'        => 'user',
			'to_fieldname'   => '_bbp_user_id'
		);

		// Store old User password (Stored in usermeta serialized with salt)
		$this->field_map[] = array(
			'from_tablename'  => 'user',
			'from_fieldname'  => 'password',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_password'
//			'callback_method' => 'callback_savepass'
		);

		// Store old User Salt (This is only used for the SELECT row info for the above password save)
/*		$this->field_map[] = array(
			'from_tablename' => 'user',
			'from_fieldname' => 'pass',
			'to_type'        => 'user',
			'to_fieldname'   => ''
		);
*/
		// User password verify class (Stored in usermeta for verifying password)
		$this->field_map[] = array(
			'to_type'      => 'user',
			'to_fieldname' => '_bbp_class',
			'default'      => 'PHPWind'
		);

		// User name.
		$this->field_map[] = array(
			'from_tablename' => 'user',
			'from_fieldname' => 'username',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_login'
		);

		// User nice name.
		$this->field_map[] = array(
			'from_tablename' => 'user',
			'from_fieldname' => 'username',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_nicename'
		);

		// User email.
		$this->field_map[] = array(
			'from_tablename' => 'user',
			'from_fieldname' => 'email',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_email'
		);

		// User registered.
		$this->field_map[] = array(
			'from_tablename'  => 'user',
			'from_fieldname'  => 'regdate',
			'to_type'         => 'user',
			'to_fieldname'    => 'user_registered',
			'callback_method' => 'callback_datetime'
		);

		// User display name.
		$this->field_map[] = array(
			'from_tablename' => 'user',
			'from_fieldname' => 'realname',
			'to_type'        => 'user',
			'to_fieldname'   => 'display_name'
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
	 * Translate the forum type from PHPWind v9.x Capitalised case to WordPress's non-capatilise case strings.
	 *
	 * @param int $status PHPWind v9.x numeric forum type
	 * @return string WordPress safe
	 */
	public function callback_forum_type( $status = 1 ) {
		switch ( $status ) {
			case 'category' :
				$status = 'category';
				break;

			case 'sub' :
				$status = 'forum';
				break;

			case 'sub2' :
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
	 * Translate the post status from PHPWind v9.x numeric's to WordPress's strings.
	 *
	 * @param int $status PHPWind v9.x numeric topic status
	 * @return string WordPress safe
	 */
	public function callback_topic_status( $status = 2 ) {
		switch ( $status ) {
			case 1 :
				$status = 'closed';
				break;

			case 2  :
			default :
				$status = 'publish';
				break;
		}
		return $status;
	}

	/**
	 * Verify the topic/reply count.
	 *
	 * @param int $count PHPWind v9.x topic/reply counts
	 * @return string WordPress safe
	 */
	public function callback_topic_reply_count( $count = 1 ) {
		$count = absint( (int) $count - 1 );
		return $count;
	}

}