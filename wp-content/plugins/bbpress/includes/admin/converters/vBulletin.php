<?php

/**
 * Implementation of vBulletin v4.x Converter.
 *
 * @since bbPress (r4724)
 * @link Codex Docs http://codex.bbpress.org/import-forums/vbulletin
 */
class vBulletin extends BBP_Converter_Base {

	/**
	 * Main constructor
	 *
	 * @uses vBulletin::setup_globals()
	 */
	function __construct() {
		parent::__construct();
		$this->setup_globals();
	}

	/**
	 * Sets up the field mappings
	 */
	private function setup_globals() {

		/** Forum Section *****************************************************/

		// Forum id (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'forum',
			'from_fieldname' => 'forumid',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_forum_id'
		);

		// Forum parent id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'forum',
			'from_fieldname' => 'parentid',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_forum_parent_id'
		);

		// Forum topic count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'forum',
			'from_fieldname' => 'threadcount',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_topic_count'
		);

		// Forum reply count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'forum',
			'from_fieldname' => 'replycount',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_reply_count'
		);

		// Forum total topic count (Includes unpublished topics, Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'forum',
			'from_fieldname'  => 'threadcount',
			'to_type'         => 'forum',
			'to_fieldname'    => '_bbp_total_topic_count'
		);

		// Forum total reply count (Includes unpublished replies, Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'forum',
			'from_fieldname'  => 'replycount',
			'to_type'         => 'forum',
			'to_fieldname'    => '_bbp_total_reply_count'
		);

		// Forum title.
		$this->field_map[] = array(
			'from_tablename' => 'forum',
			'from_fieldname' => 'title',
			'to_type'        => 'forum',
			'to_fieldname'   => 'post_title'
		);

		// Forum slug (Clean name to avoid confilcts)
		$this->field_map[] = array(
			'from_tablename'  => 'forum',
			'from_fieldname'  => 'title_clean',
			'to_type'         => 'forum',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Forum description.
		$this->field_map[] = array(
			'from_tablename'  => 'forum',
			'from_fieldname'  => 'description',
			'to_type'         => 'forum',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_null'
		);

		// Forum display order (Starts from 1)
		$this->field_map[] = array(
			'from_tablename' => 'forum',
			'from_fieldname' => 'displayorder',
			'to_type'        => 'forum',
			'to_fieldname'   => 'menu_order'
		);

		// Forum type (Category = -1 or Forum > 0, Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'forum',
			'from_fieldname'  => 'parentid',
			'to_type'         => 'forum',
			'to_fieldname'    => '_bbp_forum_type',
			'callback_method' => 'callback_forum_type'
		);

		// Forum dates.
		$this->field_map[] = array(
			'to_type'      => 'forum',
			'to_fieldname' => 'post_date',
			'default'      => date( 'Y-m-d H:i:s' )
		);
		$this->field_map[]	 = array(
			'to_type'      => 'forum',
			'to_fieldname' => 'post_date_gmt',
			'default'      => date( 'Y-m-d H:i:s' )
		);
		$this->field_map[]	 = array(
			'to_type'      => 'forum',
			'to_fieldname' => 'post_modified',
			'default'      => date( 'Y-m-d H:i:s' )
		);
		$this->field_map[]	 = array(
			'to_type'      => 'forum',
			'to_fieldname' => 'post_modified_gmt',
			'default'      => date( 'Y-m-d H:i:s' )
		);

		/** Topic Section *****************************************************/

		// Topic id (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'thread',
			'from_fieldname' => 'threadid',
			'to_type'        => 'topic',
			'to_fieldname'   => '_bbp_topic_id'
		);

		// Topic parent forum id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'thread',
			'from_fieldname'  => 'forumid',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_forum_id',
			'callback_method' => 'callback_forumid'
		);

		// Topic reply count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'thread',
			'from_fieldname'  => 'replycount',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_reply_count',
			'callback_method' => 'callback_topic_reply_count'
		);

		// Topic total reply count (Includes unpublished replies, Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'thread',
			'from_fieldname'  => 'replycount',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_total_reply_count',
			'callback_method' => 'callback_topic_reply_count'
		);

		// Topic author.
		$this->field_map[] = array(
			'from_tablename'  => 'thread',
			'from_fieldname'  => 'postuserid',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_author',
			'callback_method' => 'callback_userid'
		);

		// Topic Author ip (Stored in postmeta)
		// Note: We join the 'post' table because 'thread' table does not include topic content.
		$this->field_map[] = array(
			'from_tablename'  => 'post',
			'from_fieldname'  => 'ipaddress',
			'join_tablename'  => 'thread',
			'join_type'       => 'INNER',
			'join_expression' => 'USING (threadid) WHERE post.parentid = 0',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_author_ip'
		);

		// Topic title.
		$this->field_map[] = array(
			'from_tablename' => 'thread',
			'from_fieldname' => 'title',
			'to_type'        => 'topic',
			'to_fieldname'   => 'post_title'
		);

		// Topic slug (Clean name to avoid conflicts)
		$this->field_map[] = array(
			'from_tablename'  => 'thread',
			'from_fieldname'  => 'title',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Topic parent forum id (If no parent, then 0)
		$this->field_map[] = array(
			'from_tablename'  => 'thread',
			'from_fieldname'  => 'forumid',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_parent',
			'callback_method' => 'callback_forumid'
		);

		// Topic content.
		// Note: We join the 'post' table because 'thread' table does not include topic content.
		$this->field_map[] = array(
			'from_tablename'  => 'post',
			'from_fieldname'  => 'pagetext',
			'join_tablename'  => 'thread',
			'join_type'       => 'INNER',
			'join_expression' => 'USING (threadid) WHERE post.parentid = 0',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_html'
		);

		// Topic status (Open or Closed)
		$this->field_map[] = array(
			'from_tablename'  => 'thread',
			'from_fieldname'  => 'open',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_status',
			'callback_method' => 'callback_topic_status'
		);

		// Sticky status (Stored in postmeta))
		$this->field_map[] = array(
			'from_tablename'  => 'thread',
			'from_fieldname'  => 'sticky',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_old_sticky_status',
			'callback_method' => 'callback_sticky_status'
		);

		// Topic dates.
		$this->field_map[] = array(
			'from_tablename'  => 'thread',
			'from_fieldname'  => 'dateline',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_date',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'thread',
			'from_fieldname'  => 'dateline',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_date_gmt',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'thread',
			'from_fieldname'  => 'dateline',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_modified',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'thread',
			'from_fieldname'  => 'dateline',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_modified_gmt',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename' => 'thread',
			'from_fieldname' => 'lastpost',
			'to_type'        => 'topic',
			'to_fieldname'   => '_bbp_last_active_time',
			'callback_method' => 'callback_datetime'
		);

		/** Tags Section ******************************************************/

		// Topic id.
		$this->field_map[] = array(
			'from_tablename'  => 'tagcontent',
			'from_fieldname'  => 'contentid',
			'to_type'         => 'tags',
			'to_fieldname'    => 'objectid',
			'callback_method' => 'callback_topicid'
		);

		// Taxonomy ID.
		$this->field_map[] = array(
			'from_tablename'  => 'tagcontent',
			'from_fieldname'  => 'tagid',
			'to_type'         => 'tags',
			'to_fieldname'    => 'taxonomy'
		);

		// Term text.
		$this->field_map[] = array(
			'from_tablename'  => 'tag',
			'from_fieldname'  => 'tagtext',
			'join_tablename'  => 'tagcontent',
			'join_type'       => 'INNER',
			'join_expression' => 'USING (tagid)',
			'to_type'         => 'tags',
			'to_fieldname'    => 'name'
		);

		// Term slug.
		$this->field_map[] = array(
			'from_tablename'  => 'tag',
			'from_fieldname'  => 'tagtext',
			'join_tablename'  => 'tagcontent',
			'join_type'       => 'INNER',
			'join_expression' => 'USING (tagid)',
			'to_type'         => 'tags',
			'to_fieldname'    => 'slug',
			'callback_method' => 'callback_slug'
		);

		/** Reply Section *****************************************************/

		// Reply id (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'post',
			'from_fieldname'  => 'postid',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_post_id'
		);

		// Reply parent forum id (If no parent, then 0. Stored in postmeta)
		// Note: We join the 'thread' table because 'post' table does not include forum id.
		$this->field_map[] = array(
			'from_tablename'  => 'thread',
			'from_fieldname'  => 'forumid',
			'join_tablename'  => 'post',
			'join_type'       => 'INNER',
			'join_expression' => 'USING (threadid) WHERE post.parentid != 0',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_forum_id',
			'callback_method' => 'callback_topicid_to_forumid'
		);

		// Reply parent topic id (If no parent, then 0, Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'post',
			'from_fieldname'  => 'threadid',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_topic_id',
			'callback_method' => 'callback_topicid'
		);

		// Reply author ip (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'post',
			'from_fieldname' => 'ipaddress',
			'to_type'        => 'reply',
			'to_fieldname'   => '_bbp_author_ip'
		);

		// Reply author.
		$this->field_map[] = array(
			'from_tablename'  => 'post',
			'from_fieldname'  => 'userid',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_author',
			'callback_method' => 'callback_userid'
		);

		// Reply title.
		// Note: We join the 'thread' table because 'post' table does not include reply title.
		$this->field_map[] = array(
			'from_tablename'  => 'thread',
			'from_fieldname'  => 'title',
			'join_tablename'  => 'post',
			'join_type'       => 'INNER',
			'join_expression' => 'USING (threadid) WHERE post.parentid != 0',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_title',
			'callback_method' => 'callback_reply_title'
		);

        // Reply slug (Clean name to avoid conflicts)
        // Note: We join the 'thread' table because 'post' table does not include reply slug.
        $this->field_map[] = array(
        	'from_tablename'  => 'thread',
        	'from_fieldname'  => 'title',
        	'join_tablename'  => 'post',
        	'join_type'       => 'INNER',
        	'join_expression' => 'USING (threadid) WHERE post.parentid != 0',
        	'to_type'         => 'reply',
        	'to_fieldname'    => 'post_name',
        	'callback_method' => 'callback_slug'
        );

		// Reply content.
		$this->field_map[] = array(
			'from_tablename'  => 'post',
			'from_fieldname'  => 'pagetext',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_html'
		);

		// Reply parent topic id (If no parent, then 0)
		$this->field_map[] = array(
			'from_tablename'  => 'post',
			'from_fieldname'  => 'threadid',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_parent',
			'callback_method' => 'callback_topicid'
		);

		// Reply dates.
		$this->field_map[] = array(
			'from_tablename'  => 'post',
			'from_fieldname'  => 'dateline',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_date',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'post',
			'from_fieldname'  => 'dateline',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_date_gmt',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'post',
			'from_fieldname'  => 'dateline',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_modified',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[]	 = array(
			'from_tablename'  => 'post',
			'from_fieldname'  => 'dateline',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_modified_gmt',
			'callback_method' => 'callback_datetime'
		);

		/** User Section ******************************************************/

		// Store old User id (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'user',
			'from_fieldname' => 'userid',
			'to_type'        => 'user',
			'to_fieldname'   => '_bbp_user_id'
		);

		// Store old User password (Stored in usermeta serialized with salt)
		$this->field_map[] = array(
			'from_tablename'  => 'user',
			'from_fieldname'  => 'password',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_password',
			'callback_method' => 'callback_savepass'
		);

		// Store old User Salt (This is only used for the SELECT row info for the above password save)
		$this->field_map[] = array(
			'from_tablename' => 'user',
			'from_fieldname' => 'salt',
			'to_type'        => 'user',
			'to_fieldname'   => '_bbp_salt'
		);

		// User password verify class (Stored in usermeta for verifying password)
		$this->field_map[] = array(
			'to_type'      => 'user',
			'to_fieldname' => '_bbp_class',
			'default'      => 'vBulletin'
		);

		// User name.
		$this->field_map[] = array(
			'from_tablename' => 'user',
			'from_fieldname' => 'username',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_login'
		);

		// User email.
		$this->field_map[] = array(
			'from_tablename' => 'user',
			'from_fieldname' => 'email',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_email'
		);

		// User homepage.
		$this->field_map[] = array(
			'from_tablename' => 'user',
			'from_fieldname' => 'homepage',
			'to_type'        => 'user',
			'to_fieldname'   => 'user_url'
		);

		// User registered.
		$this->field_map[] = array(
			'from_tablename'  => 'user',
			'from_fieldname'  => 'joindate',
			'to_type'         => 'user',
			'to_fieldname'    => 'user_registered',
			'callback_method' => 'callback_datetime'
		);

		// User AIM (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'user',
			'from_fieldname' => 'aim',
			'to_type'        => 'user',
			'to_fieldname'   => 'aim'
		);

		// User Yahoo (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'user',
			'from_fieldname' => 'yahoo',
			'to_type'        => 'user',
			'to_fieldname'   => 'yim'
		);

		// User ICQ (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'user',
			'from_fieldname' => 'icq',
			'to_type'        => 'user',
			'to_fieldname'   => '_bbp_vbulletin_user_icq'
		);

		// User MSN (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'user',
			'from_fieldname' => 'msn',
			'to_type'        => 'user',
			'to_fieldname'   => '_bbp_vbulletin_user_msn'
		);

		// User Skype (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename' => 'user',
			'from_fieldname' => 'skype',
			'to_type'        => 'user',
			'to_fieldname'   => '_bbp_vbulletin_user_skype'
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
	 *
	 */
	public function authenticate_pass( $password, $serialized_pass ) {
		$pass_array = unserialize( $serialized_pass );
		return ( $pass_array['hash'] == md5( md5( $password ) . $pass_array['salt'] ) );
	}

	/**
	 * Translate the forum type from vBulletin v4.x numeric's to WordPress's strings.
	 *
	 * @param int $status vBulletin v4.x numeric forum type
	 * @return string WordPress safe
	 */
	public function callback_forum_type( $status = 0 ) {
		if ( $status == -1 ) {
			$status = 'category';
		} else {
			$status = 'forum';
		}
		return $status;
	}

	/**
	 * Translate the topic sticky status type from vBulletin v4.x numeric's to WordPress's strings.
	 *
	 * @param int $status vBulletin v4.x numeric forum type
	 * @return string WordPress safe
	 */
	public function callback_sticky_status( $status = 0 ) {
		switch ( $status ) {
			case 2 :
				$status = 'super-sticky'; // vBulletin Super Sticky 'sticky = 2'
				break;

			case 1 :
				$status = 'sticky';       // vBulletin Sticky 'sticky = 1'
				break;

			case 0  :
			default :
				$status = 'normal';       // vBulletin Normal Topic 'sticky = 0'
				break;
		}
		return $status;
	}

	/**
	 * Verify the topic reply count.
	 *
	 * @param int $count vBulletin v4.x reply count
	 * @return string WordPress safe
	 */
	public function callback_topic_reply_count( $count = 1 ) {
		$count = absint( (int) $count - 1 );
		return $count;
	}

	/**
	 * Set the reply title
	 *
	 * @param string $title vBulletin v4.x topic title of this reply
	 * @return string Prefixed topic title, or empty string
	 */
	public function callback_reply_title( $title = '' ) {
		$title = !empty( $title ) ? __( 'Re: ', 'bbpress' ) . html_entity_decode( $title ) : '';
		return $title;
	}

	/**
	 * Translate the post status from vBulletin numeric's to WordPress's strings.
	 *
	 * @param int $status vBulletin v4.x numeric topic status
	 * @return string WordPress safe
	 */
	public function callback_topic_status( $status = 1 ) {
		switch ( $status ) {
			case 0 :
				$status = 'closed';
				break;

			case 1  :
			default :
				$status = 'publish';
				break;
		}
		return $status;
	}

	/**
	 * This callback processes any custom parser.php attributes and custom code with preg_replace
	 */
	protected function callback_html( $field ) {

		// Strips vBulletin custom HTML first from $field before parsing $field to parser.php
		$vbulletin_markup = $field;
		$vbulletin_markup = html_entity_decode( $vbulletin_markup );

		// Replace '[QUOTE]' with '<blockquote>'
		$vbulletin_markup = preg_replace( '/\[QUOTE\]/', '<blockquote>', $vbulletin_markup );
		// Replace '[QUOTE=User Name($1);PostID($2)]' with '<em>@$1 $2 wrote:</em><blockquote>"
		$vbulletin_markup = preg_replace( '/\[QUOTE=(.*?);(.*?)\]/' , '<em>@$1 $2 wrote:</em><blockquote>', $vbulletin_markup );
		// Replace '[/QUOTE]' with '</blockquote>'
		$vbulletin_markup = preg_replace( '/\[\/QUOTE\]/', '</blockquote>', $vbulletin_markup );
		// Replace '[MENTION=###($1)]User Name($2)[/MENTION]' with '@$2"
		$vbulletin_markup = preg_replace( '/\[MENTION=(.*?)\](.*?)\[\/MENTION\]/', '@$2', $vbulletin_markup );

		// Replace '[video=youtube;$1]$2[/video]' with '$2"
		$vbulletin_markup = preg_replace( '/\[video\=youtube;(.*?)\](.*?)\[\/video\]/', '$2', $vbulletin_markup );

		// Now that vBulletin custom HTML has been stripped put the cleaned HTML back in $field
		$field = $vbulletin_markup;

		// Parse out any bbCodes in $field with the BBCode 'parser.php'
		require_once( bbpress()->admin->admin_dir . 'parser.php' );
		$bbcode = BBCode::getInstance();
		$bbcode->enable_smileys = false;
		$bbcode->smiley_regex   = false;
		return html_entity_decode( $bbcode->Parse( $field ) );
	}
}
