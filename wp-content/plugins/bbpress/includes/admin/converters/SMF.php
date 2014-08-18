<?php

/**
 * Implementation of SMF Forum converter.
 *
 * @since bbPress (r5189)
 * @link Codex Docs http://codex.bbpress.org/import-forums/smf
 */
class SMF extends BBP_Converter_Base {

	/**
	 * Main Constructor
	 *
	 * @uses SMF::setup_globals()
	 */
	function __construct() {
		parent::__construct();
		$this->setup_globals();
	}

	/**
	 * Sets up the field mappings
	 */
	public function setup_globals() {

		/** Forum Section ******************************************************/

		// Forum id (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'boards',
			'from_fieldname' => 'id_board',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_forum_id'
		);

		// Forum parent id (If no parent, then 0, Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'boards',
			'from_fieldname'  => 'id_parent',
			'to_type'         => 'forum',
			'to_fieldname'    => '_bbp_forum_parent_id'
		);

		// Forum topic count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'boards',
			'from_fieldname' => 'num_topics',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_topic_count'
		);

		// Forum reply count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'boards',
			'from_fieldname' => 'num_posts',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_reply_count'
		);

		// Forum total topic count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'boards',
			'from_fieldname' => 'num_topics',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_total_topic_count'
		);

		// Forum total reply count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'boards',
			'from_fieldname' => 'num_posts',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_total_reply_count'
		);

		// Forum title.
		$this->field_map[] = array(
			'from_tablename' => 'boards',
			'from_fieldname' => 'name',
			'to_type'        => 'forum',
			'to_fieldname'   => 'post_title'
		);

		// Forum slug (Clean name to avoid conflicts)
		$this->field_map[] = array(
			'from_tablename'  => 'boards',
			'from_fieldname'  => 'name',
			'to_type'         => 'forum',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Forum description.
		$this->field_map[] = array(
			'from_tablename'  => 'boards',
			'from_fieldname'  => 'description',
			'to_type'         => 'forum',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_null'
		);

		// Forum display order (Starts from 1)
		$this->field_map[] = array(
			'from_tablename' => 'boards',
			'from_fieldname' => 'board_order',
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

		/** Topic Section ******************************************************/

		// Topic id (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'topics',
			'from_fieldname' => 'id_topic',
			'to_type'        => 'topic',
			'to_fieldname'   => '_bbp_topic_id'
		);

		// Topic reply count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'topics',
			'from_fieldname'  => 'num_replies',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_reply_count',
			'callback_method' => 'callback_topic_reply_count'
		);

		// Topic total reply count (Includes unpublished replies, Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'topics',
			'from_fieldname'  => 'num_replies',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_total_reply_count',
			'callback_method' => 'callback_topic_reply_count'
		);

		// Topic parent forum id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'topics',
			'from_fieldname'  => 'id_board',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_forum_id',
			'callback_method' => 'callback_forumid'
		);

		// Topic author.
		$this->field_map[] = array(
			'from_tablename'  => 'topics',
			'from_fieldname'  => 'id_member_started',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_author',
			'callback_method' => 'callback_userid'
		);

		// Topic Author ip (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'messages',
			'from_fieldname'  => 'poster_ip',
			'join_tablename'  => 'topics',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON topics.id_first_msg = messages.id_msg',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_author_ip'
		);

		// Topic content.
		// Note: We join the 'messages' table because 'topics' table does not have content.
		$this->field_map[] = array(
			'from_tablename'  => 'messages',
			'from_fieldname'  => 'body',
			'join_tablename'  => 'topics',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON topics.id_first_msg = messages.id_msg',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_html'
		);

		// Topic title.
		$this->field_map[] = array(
			'from_tablename'  => 'messages',
			'from_fieldname'  => 'subject',
			'join_tablename'  => 'topics',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON topics.id_first_msg = messages.id_msg',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_title'
		);

		// Topic slug (Clean name to avoid conflicts)
		$this->field_map[] = array(
			'from_tablename'  => 'messages',
			'from_fieldname'  => 'subject',
			'join_tablename'  => 'topics',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON topics.id_first_msg = messages.id_msg',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Topic parent forum id (If no parent, then 0)
		$this->field_map[] = array(
			'from_tablename'  => 'topics',
			'from_fieldname'  => 'id_board',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_parent',
			'callback_method' => 'callback_forumid'
		);

		// Topic status (Open or Closed, SMF v2.0.4 0=open & 1=closed)
		$this->field_map[] = array(
			'from_tablename'  => 'topics',
			'from_fieldname'  => 'locked',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_status',
			'callback_method' => 'callback_topic_status'
		);

		// Sticky status (Stored in postmeta))
		$this->field_map[] = array(
			'from_tablename'  => 'topics',
			'from_fieldname'  => 'is_sticky',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_old_sticky_status',
			'callback_method' => 'callback_sticky_status'
		);

		// Topic dates.
		$this->field_map[] = array(
			'from_tablename'  => 'messages',
			'from_fieldname'  => 'poster_time',
			'join_tablename'  => 'topics',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON topics.id_first_msg = messages.id_msg',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_date',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'messages',
			'from_fieldname'  => 'poster_time',
			'join_tablename'  => 'topics',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON topics.id_first_msg = messages.id_msg',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_date_gmt',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'messages',
			'from_fieldname'  => 'poster_time',
			'join_tablename'  => 'topics',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON topics.id_first_msg = messages.id_msg',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_modified',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'messages',
			'from_fieldname'  => 'poster_time',
			'join_tablename'  => 'topics',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON topics.id_first_msg = messages.id_msg',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_modified_gmt',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'messages',
			'from_fieldname'  => 'poster_time',
			'join_tablename'  => 'topics',
			'join_type'       => 'LEFT',
			'join_expression' => 'ON topics.id_first_msg = messages.id_msg',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_last_active_time',
			'callback_method' => 'callback_datetime'
		);

		/** Tags Section ******************************************************/

		/**
		 * SMF v2.0.4 Forums do not support topic tags out of the box
		 */

		/** Reply Section ******************************************************/

		// Reply id (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'messages',
			'from_fieldname'  => 'id_msg',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_post_id'
		);

		// Reply parent forum id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'topics',
			'from_fieldname'  => 'id_board',
			'join_tablename'  => 'messages',
			'join_type'       => 'LEFT',
			'join_expression' => 'USING (id_topic) WHERE topics.id_first_msg != messages.id_msg',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_forum_id',
			'callback_method' => 'callback_topicid_to_forumid'
		);

		// Reply parent topic id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'messages',
			'from_fieldname'  => 'id_topic',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_topic_id',
			'callback_method' => 'callback_topicid'
		);

		// Reply author ip (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'messages',
			'from_fieldname' => 'poster_ip',
			'to_type'        => 'reply',
			'to_fieldname'   => '_bbp_author_ip'
		);

		// Reply author.
		$this->field_map[] = array(
			'from_tablename'  => 'messages',
			'from_fieldname'  => 'id_member',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_author',
			'callback_method' => 'callback_userid'
		);

		// Reply title.
		$this->field_map[] = array(
			'from_tablename' => 'messages',
			'from_fieldname' => 'subject',
			'to_type'        => 'reply',
			'to_fieldname'   => 'post_title',
			'callback_method' => 'callback_reply_title'
		);

		// Reply slug (Clean name to avoid conflicts)
		$this->field_map[] = array(
			'from_tablename'  => 'messages',
			'from_fieldname'  => 'subject',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Reply content.
		$this->field_map[] = array(
			'from_tablename'  => 'messages',
			'from_fieldname'  => 'body',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_html'
		);

		// Reply parent topic id (If no parent, then 0)
		$this->field_map[] = array(
			'from_tablename'  => 'messages',
			'from_fieldname'  => 'id_topic',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_parent',
			'callback_method' => 'callback_topicid'
		);

		// Reply dates.
		$this->field_map[] = array(
			'from_tablename'  => 'messages',
			'from_fieldname'  => 'poster_time',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_date',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'messages',
			'from_fieldname'  => 'poster_time',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_date_gmt',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'messages',
			'from_fieldname'  => 'poster_time',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_modified',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'messages',
			'from_fieldname'  => 'poster_time',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_modified_gmt',
			'callback_method' => 'callback_datetime'
		);

		/** User Section ******************************************************/

		// Store old User id (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'members',
			'from_fieldname' => 'id_member',
			'to_type'        => 'user',
			'to_fieldname'   => '_bbp_user_id'
		);

		// Store old User password (Stored in usermeta serialized with salt)
		$this->field_map[] = array(
			'from_tablename'  => 'members',
			'from_fieldname'  => 'passwd',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_password',
			'callback_method' => 'callback_savepass'
		);

		// User password verify class (Stored in usermeta for verifying password)
		$this->field_map[] = array(
			'to_type'      => 'user',
			'to_fieldname' => '_bbp_class',
			'default'      => 'SMF'
		);

		// User name.
		$this->field_map[] = array(
			'from_tablename' => 'members',
			'from_fieldname' => 'member_name',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_login'
		);

		// User nice name.
		$this->field_map[] = array(
			'from_tablename' => 'members',
			'from_fieldname' => 'member_name',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_nicename'
		);

		// User email.
		$this->field_map[] = array(
			'from_tablename' => 'members',
			'from_fieldname' => 'email_address',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_email'
		);

		// User homepage.
		$this->field_map[] = array(
			'from_tablename' => 'members',
			'from_fieldname' => 'website_url',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_url'
		);

		// User registered.
		$this->field_map[] = array(
			'from_tablename'  => 'members',
			'from_fieldname'  => 'date_registered',
			'to_type'         => 'user',
			'to_fieldname'    => 'user_registered',
			'callback_method' => 'callback_datetime'
		);

		// User display name.
		$this->field_map[] = array(
			'from_tablename' => 'members',
			'from_fieldname' => 'real_name',
			'to_type'        => 'user',
			'to_fieldname'   => 'display_name'
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
			'from_fieldname' => 'yim',
			'to_type'        => 'user',
			'to_fieldname'   => 'yim'
		);

		// Store ICQ (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'members',
			'from_fieldname' => 'icq',
			'to_type'        => 'user',
			'to_fieldname'   => '_bbp_smf_user_icq'
		);

		// Store MSN (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'members',
			'from_fieldname' => 'msn',
			'to_type'        => 'user',
			'to_fieldname'   => '_bbp_smf_user_msn'
		);

		// Store Signature (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'members',
			'from_fieldname' => 'signature',
			'to_type'        => 'user',
			'to_fieldname'   => '_bbp_smf_user_sig',
			'callback_method' => 'callback_html'
		);

		// Store Avatar (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'members',
			'from_fieldname' => 'avatar',
			'to_type'        => 'user',
			'to_fieldname'   => '_bbp_smf_user_avatar',
			'callback_method' => 'callback_html'
		);

		// Store Location (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'members',
			'from_fieldname' => 'location',
			'to_type'        => 'user',
			'to_fieldname'   => '_bbp_smf_user_location',
			'callback_method' => 'callback_html'
		);

		// Store Personal Text (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'members',
			'from_fieldname' => 'personal_text',
			'to_type'        => 'user',
			'to_fieldname'   => '_bbp_smf_user_personal_text',
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
		$pass_array = array( 'hash' => $field, 'username' => $row['member_name'] );
		return $pass_array;
	}

	/**
	 * This method is to take the pass out of the database and compare
	 * to a pass the user has typed in.
	 */
	public function authenticate_pass( $password, $serialized_pass )
	{
		$pass_array = unserialize( $serialized_pass );
		return ( $pass_array['hash'] === sha1( strtolower( $pass_array['username'] ) . $password ) ? true : false );
	}

	/**
	 * Translate the post status from SMF v2.0.4 numeric's to WordPress's strings.
	 *
	 * @param int $status SMF v2.0.4 numeric topic status
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
	 * Translate the topic sticky status type from SMF v2.0.4 numeric's to WordPress's strings.
	 *
	 * @param int $status SMF v2.0.4 numeric forum type
	 * @return string WordPress safe
	 */
	public function callback_sticky_status( $status = 0 ) {
		switch ( $status ) {
			case 1 :
				$status = 'sticky'; // SMF Sticky 'is_sticky = 1'
				break;

			case 0  :
			default :
				$status = 'normal'; // SMF normal topic 'is_sticky = 0'
				break;
		}
		return $status;
	}

	/**
	 * Verify the topic/reply count.
	 *
	 * @param int $count SMF v2.0.4 topic/reply counts
	 * @return string WordPress safe
	 */
	public function callback_topic_reply_count( $count = 1 ) {
		$count = absint( (int) $count - 1 );
		return $count;
	}

	/**
	 * Set the reply title
	 *
	 * @param string $title SMF v2.0.4 topic title of this reply
	 * @return string Prefixed topic title, or empty string
	 */
	public function callback_reply_title( $title = '' ) {
		$title = !empty( $title ) ? __( 'Re: ', 'bbpress' ) . html_entity_decode( $title ) : '';
		return $title;
	}

	/**
	 * This callback processes any custom parser.php attributes and custom code with preg_replace
	 */
	protected function callback_html( $field ) {

		// Strips SMF custom HTML first from $field before parsing $field to parser.php
		$SMF_markup = $field;
		$SMF_markup = html_entity_decode( $SMF_markup );

		// Replace '[quote]' with '<blockquote>'
		$SMF_markup = preg_replace( '/\[quote\]/',        '<blockquote>',  $SMF_markup );
		// Replace '[quote ($1)]' with '<blockquote>"
		$SMF_markup = preg_replace( '/\[quote (.*?)\]/' , '<blockquote>',  $SMF_markup );
		// Replace '[/quote]' with '</blockquote>'
		$SMF_markup = preg_replace( '/\[\/quote\]/',      '</blockquote>', $SMF_markup );

		// Replace '[glow]' with ''
		$SMF_markup = preg_replace( '/\[glow\]/',   '',       $SMF_markup );
		// Replace '[glow]' with ''
		$SMF_markup = preg_replace( '/\[glow=(.*?)\]/',   '', $SMF_markup );
		// Replace '[/glow]' with ''
		$SMF_markup = preg_replace( '/\[\/glow\]/', '',       $SMF_markup );

		// Replace '[shadow]' with ''
		$SMF_markup = preg_replace( '/\[shadow\]/',   '',       $SMF_markup );
		// Replace '[shadow]' with ''
		$SMF_markup = preg_replace( '/\[shadow=(.*?)\]/',   '', $SMF_markup );
		// Replace '[/shadow]' with ''
		$SMF_markup = preg_replace( '/\[\/shadow\]/', '',       $SMF_markup );

		// Replace '[move]' with ''
		$SMF_markup = preg_replace( '/\[move\]/',   '', $SMF_markup );
		// Replace '[/move]' with ''
		$SMF_markup = preg_replace( '/\[\/move\]/', '', $SMF_markup );

		// Replace '[table]' with '<table>'
		$SMF_markup = preg_replace( '/\[table\]/',   '<table>',  $SMF_markup );
		// Replace '[/table]' with '</table>'
		$SMF_markup = preg_replace( '/\[\/table\]/', '</table>', $SMF_markup );
		// Replace '[tr]' with '<tr>'
		$SMF_markup = preg_replace( '/\[tr\]/',   '<tr>',  $SMF_markup );
		// Replace '[/tr]' with '</tr>'
		$SMF_markup = preg_replace( '/\[\/tr\]/', '</tr>', $SMF_markup );
		// Replace '[td]' with '<td>'
		$SMF_markup = preg_replace( '/\[td\]/',   '<td>',  $SMF_markup );
		// Replace '[/td]' with '</td>'
		$SMF_markup = preg_replace( '/\[\/td\]/', '</td>', $SMF_markup );

		// Replace '[list]' with '<ul>'
		$phpbb_uid = preg_replace( '/\[list\]/',     '<ul>',          $phpbb_uid );
		// Replace '[liist type=decimal]' with '<ol type="a">'
		$phpbb_uid = preg_replace( '/\[list\ type=decimal\]/',   '<ol type="a">', $phpbb_uid );
		// Replace '[li]' with '<li>'
		$SMF_markup = preg_replace( '/\[li\]/',   '<li>',  $SMF_markup );
		// Replace '[/li]' with '</li>'
		$SMF_markup = preg_replace( '/\[\/li\]/', '</li>', $SMF_markup );

		// Replace '[tt]' with '<tt>'
		$SMF_markup = preg_replace( '/\[tt\]/',   '<tt>',  $SMF_markup );
		// Replace '[/tt]' with '</tt>'
		$SMF_markup = preg_replace( '/\[\/tt\]/', '</tt>', $SMF_markup );

		// Replace '<br />' with ''
		$SMF_markup = preg_replace( '/\<br \/\>/',   '<tt>',  $SMF_markup );

		// Replace '[size=$1]' with '<span style="font-size:$1%;">$3</span>'
		$SMF_markup = preg_replace( '/\[size=(.*?)\]/', '<span style="font-size:$1">', $SMF_markup );
		// Replace '[/size]' with '</span>'
		$SMF_markup = preg_replace( '/\[\/size\]/',     '</span>',                     $SMF_markup );

		// Now that SMF custom HTML has been stripped put the cleaned HTML back in $field
		$field = $SMF_markup;

		// Parse out any bbCodes in $field with the BBCode 'parser.php'
		require_once( bbpress()->admin->admin_dir . 'parser.php' );
		$bbcode = BBCode::getInstance();
		$bbcode->enable_smileys = false;
		$bbcode->smiley_regex   = false;
		return html_entity_decode( $bbcode->Parse( $field ) );
	}
}