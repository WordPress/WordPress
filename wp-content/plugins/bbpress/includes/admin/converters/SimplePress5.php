<?php

/**
 * Implementation of SimplePress v5 converter.
 *
 * @since bbPress (r4638)
 * @link Codex Docs http://codex.bbpress.org/import-forums/simplepress/
 */
class SimplePress5 extends BBP_Converter_Base {

	/**
	 * Main Constructor
	 *
	 * @uses SimplePress5::setup_globals()
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
			'from_tablename' => 'sfforums',
			'from_fieldname' => 'forum_id',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_forum_id'
		);

		// Forum parent id (If no parent, then 0, Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'sfforums',
			'from_fieldname' => 'parent',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_forum_parent_id'
		);

		// Forum topic count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'sfforums',
			'from_fieldname' => 'topic_count',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_topic_count'
		);

		// Forum reply count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'sfforums',
			'from_fieldname' => 'post_count',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_reply_count'
		);

		// Forum total topic count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'sfforums',
			'from_fieldname' => 'topic_count',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_total_topic_count'
		);

		// Forum total reply count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'sfforums',
			'from_fieldname' => 'post_count',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_total_reply_count'
		);

		// Forum title.
		$this->field_map[] = array(
			'from_tablename' => 'sfforums',
			'from_fieldname' => 'forum_name',
			'to_type'        => 'forum',
			'to_fieldname'   => 'post_title'
		);

		// Forum slug (Clean name to avoid conflicts)
		$this->field_map[] = array(
			'from_tablename'  => 'sfforums',
			'from_fieldname'  => 'forum_name',
			'to_type'         => 'forum',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Forum description.
		$this->field_map[] = array(
			'from_tablename'  => 'sfforums',
			'from_fieldname'  => 'forum_desc',
			'to_type'         => 'forum',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_null'
		);

		// Forum display order (Starts from 1)
		$this->field_map[] = array(
			'from_tablename' => 'sfforums',
			'from_fieldname' => 'forum_seq',
			'to_type'        => 'forum',
			'to_fieldname'   => 'menu_order'
		);

		// Forum dates.
		$this->field_map[] = array(
			'to_type'      => 'forums',
			'to_fieldname' => 'post_date',
			'default'      => date('Y-m-d H:i:s')
		);
		$this->field_map[] = array(
			'to_type'      => 'forums',
			'to_fieldname' => 'post_date_gmt',
			'default'      => date('Y-m-d H:i:s')
		);
		$this->field_map[] = array(
			'to_type'      => 'forums',
			'to_fieldname' => 'post_modified',
			'default'      => date('Y-m-d H:i:s')
		);
		$this->field_map[] = array(
			'to_type'      => 'forums',
			'to_fieldname' => 'post_modified_gmt',
			'default'      => date('Y-m-d H:i:s')
		);

		/** Topic Section *****************************************************/

		// Topic id (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'sftopics',
			'from_fieldname' => 'topic_id',
			'to_type'        => 'topic',
			'to_fieldname'   => '_bbp_topic_id'
		);

		// Topic reply count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'sftopics',
			'from_fieldname' => 'post_count',
			'to_type'        => 'topic',
			'to_fieldname'   => '_bbp_reply_count',
			'callback_method' => 'callback_topic_reply_count'
		);

		// Topic parent forum id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'sftopics',
			'from_fieldname'  => 'forum_id',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_forum_id',
			'callback_method' => 'callback_forumid'
		);

		// Topic author.
		$this->field_map[] = array(
			'from_tablename'  => 'sftopics',
			'from_fieldname'  => 'user_id',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_author',
			'callback_method' => 'callback_userid'
		);

		// Topic content.
		// Note: We join the sfposts table because sftopics do not have content.
		$this->field_map[] = array(
			'from_tablename'  => 'sfposts',
			'from_fieldname'  => 'post_content',
			'join_tablename'  => 'sftopics',
			'join_type'       => 'INNER',
			'join_expression' => 'USING (topic_id) WHERE sfposts.post_index = 1',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_html'
		);

		// Topic title.
		$this->field_map[] = array(
			'from_tablename' => 'sftopics',
			'from_fieldname' => 'topic_name',
			'to_type'        => 'topic',
			'to_fieldname'   => 'post_title'
		);

		// Topic slug (Clean name to avoid conflicts)
		$this->field_map[] = array(
			'from_tablename'  => 'sftopics',
			'from_fieldname'  => 'topic_name',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Topic parent forum id (If no parent, then 0)
		$this->field_map[] = array(
			'from_tablename'  => 'sftopics',
			'from_fieldname'  => 'forum_id',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_parent',
			'callback_method' => 'callback_forumid'
		);

		// Topic status (Open or Closed)
		$this->field_map[] = array(
			'from_tablename'  => 'sftopics',
			'from_fieldname'  => 'topic_status',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_status',
			'callback_method' => 'callback_status'
		);

		// Sticky status (Stored in postmeta))
		$this->field_map[] = array(
			'from_tablename'  => 'sftopics',
			'from_fieldname'  => 'topic_pinned',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_old_sticky_status',
			'callback_method' => 'callback_sticky_status'
		);

		// Topic dates.
		$this->field_map[] = array(
			'from_tablename'  => 'sftopics',
			'from_fieldname'  => 'topic_date',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_date'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'sftopics',
			'from_fieldname'  => 'topic_date',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_date_gmt'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'sftopics',
			'from_fieldname'  => 'topic_date',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_modified'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'sftopics',
			'from_fieldname'  => 'topic_date',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_modified_gmt'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'sftopics',
			'from_fieldname'  => 'topic_date',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_last_active_time'
		);

		/** Tags Section ******************************************************/

		/**
		 * SimplePress Forums do not support topic tags without paid extensions
		 */

		/** Reply Section *****************************************************/

		// Reply id (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'sfposts',
			'from_fieldname' => 'post_id',
			'to_type'        => 'reply',
			'to_fieldname'   => '_bbp_post_id'
		);

		// Reply parent forum id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'sfposts',
			'from_fieldname'  => 'forum_id',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_forum_id',
			'callback_method' => 'callback_topicid_to_forumid'
		);

		// Reply parent topic id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'sfposts',
			'from_fieldname'  => 'topic_id',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_topic_id',
			'callback_method' => 'callback_topicid'
		);

		// Reply author ip (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'sfposts',
			'from_fieldname' => 'poster_ip',
			'to_type'        => 'reply',
			'to_fieldname'   => '_bbp_author_ip'
		);

		// Reply author.
		$this->field_map[] = array(
			'from_tablename'  => 'sfposts',
			'from_fieldname'  => 'user_id',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_author',
			'callback_method' => 'callback_userid'
		);

		// Reply title.
		// Note: We join the sftopics table because sfposts table does not include topic title.
		$this->field_map[] = array(
			'from_tablename'  => 'sftopics',
			'from_fieldname'  => 'topic_name',
			'join_tablename'  => 'sfposts',
			'join_type'       => 'LEFT',
			'join_expression' => 'USING (topic_id) WHERE sfposts.post_index != 1',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_title',
			'callback_method' => 'callback_reply_title'
		);

		// Reply slug (Clean name to avoid conflicts)
		// Note: We join the sftopics table because sfposts table does not include topic title.
		$this->field_map[] = array(
			'from_tablename'  => 'sftopics',
			'from_fieldname'  => 'topic_name',
			'join_tablename'  => 'sfposts',
			'join_type'       => 'LEFT',
			'join_expression' => 'USING (topic_id) WHERE sfposts.post_index != 1',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Reply content.
		$this->field_map[] = array(
			'from_tablename'  => 'sfposts',
			'from_fieldname'  => 'post_content',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_html'
		);

		// Reply parent topic id (If no parent, then 0)
		$this->field_map[] = array(
			'from_tablename'  => 'sfposts',
			'from_fieldname'  => 'topic_id',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_parent',
			'callback_method' => 'callback_topicid'
		);

		// Reply dates.
		$this->field_map[] = array(
			'from_tablename'  => 'sfposts',
			'from_fieldname'  => 'post_date',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_date'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'sfposts',
			'from_fieldname'  => 'post_date',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_date_gmt'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'sfposts',
			'from_fieldname'  => 'post_date',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_modified'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'sfposts',
			'from_fieldname'  => 'post_date',
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
	 * Translate the post status from Simple:Press v5.x numeric's to WordPress's strings.
	 *
	 * @param int $status Simple:Press numeric status
	 * @return string WordPress safe
	 */
	public function callback_status( $status = 0 ) {
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
	 * Translate the topic sticky status type from Simple:Press v5.x numeric's to WordPress's strings.
	 *
	 * @param int $status Simple:Press v5.x numeric forum type
	 * @return string WordPress safe
	 */
	public function callback_sticky_status( $status = 0 ) {
		switch ( $status ) {
			case 1 :
				$status = 'sticky';       // Simple:Press Sticky 'topic_sticky = 1'
				break;

			case 0  :
			default :
				$status = 'normal';       // Simple:Press Normal Topic 'topic_sticky = 0'
				break;
		}
		return $status;
	}

	/**
	 * Verify the topic reply count.
	 *
	 * @param int $count Simple:Press v5.x reply count
	 * @return string WordPress safe
	 */
	public function callback_topic_reply_count( $count = 1 ) {
		$count = absint( (int) $count - 1 );
		return $count;
	}

	/**
	 * Set the reply title
	 *
	 * @param string $title Simple:Press v5.x topic title of this reply
	 * @return string Prefixed topic title, or empty string
	 */
	public function callback_reply_title( $title = '' ) {
		$title = !empty( $title ) ? __( 'Re: ', 'bbpress' ) . html_entity_decode( $title ) : '';
		return $title;
	}

	/**
	 * This callback processes any custom parser.php attributes and custom HTML code with preg_replace
	 */
	protected function callback_html( $field ) {

		// Strip any custom HTML not supported by parser.php first from $field before parsing $field to parser.php
		$simplepress_markup = $field;
		$simplepress_markup = html_entity_decode( $simplepress_markup );

		// Replace any SimplePress smilies from path '/sp-resources/forum-smileys/sf-smily.gif' with the equivelant WordPress Smilie
		$simplepress_markup = preg_replace( '/\<img src=(.*?)\/sp-resources\/forum-smileys\/sf-confused\.gif(.*?)\" \/>/',     ':?' ,     $simplepress_markup );
		$simplepress_markup = preg_replace( '/\<img src=(.*?)\/sp-resources\/forum-smileys\/sf-cool\.gif(.*?)\" \/>/',        ':cool:',   $simplepress_markup );
		$simplepress_markup = preg_replace( '/\<img src=(.*?)\/sp-resources\/forum-smileys\/sf-cry\.gif(.*?)\" \/>/',         ':cry:',    $simplepress_markup );
		$simplepress_markup = preg_replace( '/\<img src=(.*?)\/sp-resources\/forum-smileys\/sf-embarassed\.gif(.*?)\" \/>/' , ':oops:',   $simplepress_markup );
		$simplepress_markup = preg_replace( '/\<img src=(.*?)\/sp-resources\/forum-smileys\/sf-frown\.gif(.*?)\" \/>/',       ':(',       $simplepress_markup );
		$simplepress_markup = preg_replace( '/\<img src=(.*?)\/sp-resources\/forum-smileys\/sf-kiss\.gif(.*?)\" \/>/',        ':P',       $simplepress_markup );
		$simplepress_markup = preg_replace( '/\<img src=(.*?)\/sp-resources\/forum-smileys\/sf-laugh\.gif(.*?)\" \/>/',       ':D',       $simplepress_markup );
		$simplepress_markup = preg_replace( '/\<img src=(.*?)\/sp-resources\/forum-smileys\/sf-smile\.gif(.*?)\" \/>/',       ':smile:',  $simplepress_markup );
		$simplepress_markup = preg_replace( '/\<img src=(.*?)\/sp-resources\/forum-smileys\/sf-surprised\.gif(.*?)\" \/>/',   ':o',       $simplepress_markup );
		$simplepress_markup = preg_replace( '/\<img src=(.*?)\/sp-resources\/forum-smileys\/sf-wink\.gif(.*?)\" \/>/',        ':wink:',   $simplepress_markup );
		$simplepress_markup = preg_replace( '/\<img src=(.*?)\/sp-resources\/forum-smileys\/sf-yell\.gif(.*?)\" \/>/',         ':x',      $simplepress_markup );

		// Replace '<div class="sfcode">example code</div>' with '<code>*</code>'
		$simplepress_markup = preg_replace( '/\<div class\=\"sfcode\"\>(.*?)\<\/div\>/' , '<code>$1</code>' , $simplepress_markup );

		// Replace '<strong>username said </strong>' with '@username said:'
		$simplepress_markup = preg_replace ( '/\<strong\>(.*?)\ said\ \<\/strong\>/',     '@$1 said:',        $simplepress_markup );

		// Replace '<p>&nbsp;</p>' with '<p>&nbsp;</p>'
		$simplepress_markup = preg_replace ( '/\n(&nbsp;|[\s\p{Z}\xA0\x{00A0}]+)\r/', '<br>', $simplepress_markup );

		// Now that SimplePress' custom HTML codes have been stripped put the cleaned HTML back in $field
		$field = $simplepress_markup;

		// Parse out any bbCodes with the BBCode 'parser.php'
		require_once( bbpress()->admin->admin_dir . 'parser.php' );
		$bbcode = BBCode::getInstance();
		$bbcode->enable_smileys = false;
		$bbcode->smiley_regex   = false;
		return html_entity_decode( $bbcode->Parse( $field ) );
	}
}
