<?php

/**
 * Implementation of Drupal v7.x Forum converter.
 *
 * @since bbPress (r5138)
 * @link Codex Docs http://codex.bbpress.org/import-forums/drupal
 */
class Drupal7 extends BBP_Converter_Base {

	/**
	 * Main Constructor
	 *
	 * @uses Drupal7::setup_globals()
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
			'from_tablename' => 'taxonomy_term_data',
			'from_fieldname' => 'tid',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_forum_id'
		);

		// Forum parent id (If no parent, then 0, Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'taxonomy_term_hierarchy',
			'from_fieldname'  => 'parent',
			'join_tablename'  => 'taxonomy_term_data',
			'join_type'       => 'INNER',
			'join_expression' => 'USING (tid)',
			'from_expression' => 'LEFT JOIN taxonomy_vocabulary AS taxonomy_vocabulary USING (vid) WHERE module = "forum"',
			'to_type'         => 'forum',
			'to_fieldname'    => '_bbp_forum_parent_id'
		);

		// Forum title.
		$this->field_map[] = array(
			'from_tablename' => 'taxonomy_term_data',
			'from_fieldname' => 'name',
			'to_type'        => 'forum',
			'to_fieldname'   => 'post_title'
		);

		// Forum slug (Clean name to avoid conflicts)
		$this->field_map[] = array(
			'from_tablename'  => 'taxonomy_term_data',
			'from_fieldname'  => 'name',
			'to_type'         => 'forum',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Forum description.
		$this->field_map[] = array(
			'from_tablename'  => 'taxonomy_term_data',
			'from_fieldname'  => 'description',
			'to_type'         => 'forum',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_null'
		);

		// Forum display order (Starts from 1)
		$this->field_map[] = array(
			'from_tablename' => 'taxonomy_term_data',
			'from_fieldname' => 'weight',
			'to_type'        => 'forum',
			'to_fieldname'   => 'menu_order'
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
			'from_tablename' => 'forum_index',
			'from_fieldname' => 'nid',
			'to_type'        => 'topic',
			'to_fieldname'   => '_bbp_topic_id'
		);

		// Topic reply count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'forum_index',
			'from_fieldname'  => 'comment_count',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_reply_count',
			'callback_method' => 'callback_topic_reply_count'
		);

		// Topic total reply count (Includes unpublished replies, Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'forum_index',
			'from_fieldname'  => 'comment_count',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_total_reply_count',
			'callback_method' => 'callback_topic_reply_count'
		);

		// Topic parent forum id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'forum_index',
			'from_fieldname'  => 'tid',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_forum_id',
			'callback_method' => 'callback_forumid'
		);

		// Topic author.
		// Note: We join the 'node' table because 'forum_index' table does not include author id.
		$this->field_map[] = array(
			'from_tablename'  => 'node',
			'from_fieldname'  => 'uid',
			'join_tablename'  => 'forum_index',
			'join_type'       => 'INNER',
			'join_expression' => 'ON node.nid = forum_index.nid',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_author',
			'callback_method' => 'callback_userid'
		);

		// Topic content.
		// Note: We join the 'field_data_body' table because 'node' or 'forum_index' table does not include topic content.
		$this->field_map[] = array(
			'from_tablename'  => 'field_data_body',
			'from_fieldname'  => 'body_value',
			'join_tablename'  => 'node',
			'join_type'       => 'INNER',
			'join_expression' => 'ON field_data_body.revision_id = node.vid',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_html'
		);

		// Topic title.
		$this->field_map[] = array(
			'from_tablename' => 'forum_index',
			'from_fieldname' => 'title',
			'to_type'        => 'topic',
			'to_fieldname'   => 'post_title'
		);

		// Topic slug (Clean name to avoid conflicts)
		$this->field_map[] = array(
			'from_tablename'  => 'forum_index',
			'from_fieldname'  => 'title',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Topic parent forum id (If no parent, then 0)
		$this->field_map[] = array(
			'from_tablename'  => 'forum_index',
			'from_fieldname'  => 'tid',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_parent',
			'callback_method' => 'callback_forumid'
		);

		// Sticky status (Stored in postmeta))
		$this->field_map[] = array(
			'from_tablename'  => 'forum_index',
			'from_fieldname'  => 'sticky',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_old_sticky_status',
			'callback_method' => 'callback_sticky_status'
		);

		// Topic dates.
		$this->field_map[] = array(
			'from_tablename'  => 'forum_index',
			'from_fieldname'  => 'created',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_date',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'forum_index',
			'from_fieldname'  => 'created',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_date_gmt',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'forum_index',
			'from_fieldname'  => 'last_comment_timestamp',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_modified',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'forum_index',
			'from_fieldname'  => 'last_comment_timestamp',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_modified_gmt',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'forum_index',
			'from_fieldname'  => 'last_comment_timestamp',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_last_active_time',
			'callback_method' => 'callback_datetime'
		);

		// Topic status (Drupal v7.x Comments Enabled no = 0, closed = 1 & open = 2)
		$this->field_map[] = array(
			'from_tablename'  => 'node',
			'from_fieldname'  => 'comment',
			'join_tablename'  => 'forum_index',
			'join_type'       => 'INNER',
			'join_expression' => 'ON node.nid = forum_index.nid',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_status',
			'callback_method' => 'callback_topic_status'
		);

		/** Tags Section ******************************************************/

		// Topic id.
		$this->field_map[] = array(
			'from_tablename'  => 'field_data_field_tags',
			'from_fieldname'  => 'entity_id',
			'to_type'         => 'tags',
			'to_fieldname'    => 'objectid',
			'callback_method' => 'callback_topicid'
		);

		// Taxonomy ID.
		$this->field_map[] = array(
			'from_tablename'  => 'field_data_field_tags',
			'from_fieldname'  => 'field_tags_tid',
			'to_type'         => 'tags',
			'to_fieldname'    => 'taxonomy'
		);

		// Term name.
		$this->field_map[] = array(
			'from_tablename'  => 'taxonomy_term_data',
			'from_fieldname'  => 'name',
			'join_tablename'  => 'field_data_field_tags',
			'join_type'       => 'INNER',
			'join_expression' => 'ON field_tags_tid = taxonomy_term_data.tid',
			'to_type'         => 'tags',
			'to_fieldname'    => 'name'
		);

		// Term slug.
		$this->field_map[] = array(
			'from_tablename'  => 'taxonomy_term_data',
			'from_fieldname'  => 'name',
			'join_tablename'  => 'field_data_field_tags',
			'join_type'       => 'INNER',
			'join_expression' => 'ON field_tags_tid = taxonomy_term_data.tid',
			'to_type'         => 'tags',
			'to_fieldname'    => 'slug',
			'callback_method' => 'callback_slug'
		);

		// Term description.
		$this->field_map[] = array(
			'from_tablename'  => 'taxonomy_term_data',
			'from_fieldname'  => 'description',
			'join_tablename'  => 'field_data_field_tags',
			'join_type'       => 'INNER',
			'join_expression' => 'ON field_tags_tid = taxonomy_term_data.tid',
			'to_type'         => 'tags',
			'to_fieldname'    => 'description'
		);

		/** Reply Section *****************************************************/

		// Reply id (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'comment',
			'from_fieldname' => 'cid',
			'to_type'        => 'reply',
			'to_fieldname'   => '_bbp_post_id'
		);

		// Reply parent forum id (If no parent, then 0. Stored in postmeta)
		// Note: We join the 'forum' table because 'comment' table does not include parent forum id.
		$this->field_map[] = array(
			'from_tablename'  => 'forum',
			'from_fieldname'  => 'tid',
			'join_tablename'  => 'comment',
			'join_type'       => 'INNER',
			'join_expression' => 'ON forum.nid = comment.nid',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_forum_id',
			'callback_method' => 'callback_topicid_to_forumid'
		);

		// Reply parent topic id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'comment',
			'from_fieldname'  => 'nid',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_topic_id',
			'callback_method' => 'callback_topicid'
		);

		// Reply parent reply id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'comment',
			'from_fieldname'  => 'pid',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_reply_to'
		);

		// Reply author ip (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'comment',
			'from_fieldname' => 'hostname',
			'to_type'        => 'reply',
			'to_fieldname'   => '_bbp_author_ip'
		);

		// Reply author.
		$this->field_map[] = array(
			'from_tablename'  => 'comment',
			'from_fieldname'  => 'uid',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_author',
			'callback_method' => 'callback_userid'
		);

		// Reply title.
		$this->field_map[] = array(
			'from_tablename' => 'comment',
			'from_fieldname' => 'subject',
			'to_type'        => 'reply',
			'to_fieldname'   => 'post_title'
		);

		// Reply slug (Clean name to avoid conflicts)
		$this->field_map[] = array(
			'from_tablename'  => 'comment',
			'from_fieldname'  => 'subject',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Reply content.
		// Note: We join the 'field_data_comment_body' table because 'comment' table does not include reply content.
		$this->field_map[] = array(
			'from_tablename'  => 'field_data_comment_body',
			'from_fieldname'  => 'comment_body_value',
			'join_tablename'  => 'comment',
			'join_type'       => 'INNER',
			'join_expression' => 'ON field_data_comment_body.entity_id = comment.cid',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_html'
		);

		// Reply parent topic id (If no parent, then 0)
		$this->field_map[] = array(
			'from_tablename'  => 'comment',
			'from_fieldname'  => 'nid',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_parent',
			'callback_method' => 'callback_topicid'
		);

		// Reply dates.
		$this->field_map[] = array(
			'from_tablename'  => 'comment',
			'from_fieldname'  => 'created',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_date',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'comment',
			'from_fieldname'  => 'created',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_date_gmt',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'comment',
			'from_fieldname'  => 'changed',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_modified',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'comment',
			'from_fieldname'  => 'changed',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_modified_gmt',
			'callback_method' => 'callback_datetime'
		);

		/** User Section ******************************************************/

		// Store old User id (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'users',
			'from_fieldname' => 'uid',
			'to_type'        => 'user',
			'to_fieldname'   => '_bbp_user_id'
		);

		// Store old User password (Stored in usermeta serialized with salt)
		$this->field_map[] = array(
			'from_tablename'  => 'users',
			'from_fieldname'  => 'pass',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_password'
//			'callback_method' => 'callback_savepass'
		);

		// Store old User Salt (This is only used for the SELECT row info for the above password save)
		$this->field_map[] = array(
			'from_tablename' => 'users',
			'from_fieldname' => 'pass',
			'to_type'        => 'user',
			'to_fieldname'   => ''
		);

		// User password verify class (Stored in usermeta for verifying password)
		$this->field_map[] = array(
			'to_type'      => 'users',
			'to_fieldname' => '_bbp_class',
			'default'      => 'Drupal7'
		);

		// User name.
		$this->field_map[] = array(
			'from_tablename' => 'users',
			'from_fieldname' => 'name',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_login'
		);

		// User nice name.
		$this->field_map[] = array(
			'from_tablename' => 'users',
			'from_fieldname' => 'name',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_nicename'
		);

		// User email.
		$this->field_map[] = array(
			'from_tablename' => 'users',
			'from_fieldname' => 'mail',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_email'
		);

		// User registered.
		$this->field_map[] = array(
			'from_tablename'  => 'users',
			'from_fieldname'  => 'created',
			'to_type'         => 'user',
			'to_fieldname'    => 'user_registered',
			'callback_method' => 'callback_datetime'
		);

		// Store Signature (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename'  => 'users',
			'from_fieldname'  => 'signature',
			'to_fieldname'    => '_bbp_drupal7_user_sig',
			'to_type'         => 'user',
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
	 * Translate the post status from Drupal v7.x numeric's to WordPress's strings.
	 *
	 * @param int $status Drupal v7.x numeric topic status
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
	 * Translate the topic sticky status type from Drupal v7.x numeric's to WordPress's strings.
	 *
	 * @param int $status Drupal v7.x numeric forum type
	 * @return string WordPress safe
	 */
	public function callback_sticky_status( $status = 0 ) {
		switch ( $status ) {
			case 1 :
				$status = 'sticky'; // Drupal Sticky 'topic_sticky = 1'
				break;

			case 0  :
			default :
				$status = 'normal'; // Drupal Normal Topic 'sticky = 0'
				break;
		}
		return $status;
	}

	/**
	 * Verify the topic/reply count.
	 *
	 * @param int $count Drupal v7.x topic/reply counts
	 * @return string WordPress safe
	 */
	public function callback_topic_reply_count( $count = 1 ) {
		$count = absint( (int) $count - 1 );
		return $count;
	}
}