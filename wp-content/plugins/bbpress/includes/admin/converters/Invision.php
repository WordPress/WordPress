<?php

/**
 * Implementation of Invision Power Board v3.x converter.
 *
 * @since bbPress (r4713)
 * @link Codex Docs http://codex.bbpress.org/import-forums/invision
 */
class Invision extends BBP_Converter_Base {

	/**
	 * Main Constructor
	 *
	 * @uses Invision::setup_globals()
	 */
	function __construct() {
		parent::__construct();
		$this->setup_globals();
	}

	/**
	 * Sets up the field mappings
	 */
	public function setup_globals()	{

		/** Forum Section *****************************************************/

		// Forum id (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'forums',
			'from_fieldname'  => 'id',
			'to_type'         => 'forum',
			'to_fieldname'    => '_bbp_forum_id'
		);

		// Forum parent id (If no parent, then 0, Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'forums',
			'from_fieldname'  => 'parent_id',
			'to_type'         => 'forum',
			'to_fieldname'    => '_bbp_forum_parent_id'
		);

		// Forum topic count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'forums',
			'from_fieldname' => 'topics',
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

		// Forum total topic count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'forums',
			'from_fieldname' => 'topics',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_total_topic_count'
		);

		// Forum total reply count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename' => 'forums',
			'from_fieldname' => 'posts',
			'to_type'        => 'forum',
			'to_fieldname'   => '_bbp_total_reply_count'
		);

		// Forum title.
		$this->field_map[] = array(
			'from_tablename'  => 'forums',
			'from_fieldname'  => 'name',
			'to_type'         => 'forum',
			'to_fieldname'    => 'post_title'
		);

		// Forum slug (Clean name to avoid confilcts)
		$this->field_map[] = array(
			'from_tablename'  => 'forums',
			'from_fieldname'  => 'name_seo',
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
			'from_tablename'  => 'forums',
			'from_fieldname'  => 'position',
			'to_type'         => 'forum',
			'to_fieldname'    => 'menu_order'
		);

		// Forum type (Forum = 0 or Category = -1, Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'forums',
			'from_fieldname'  => 'parent_id',
			'to_type'         => 'forum',
			'to_fieldname'    => '_bbp_forum_type',
			'callback_method' => 'callback_forum_type'
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

		/** Topic Section *****************************************************/

		// Topic id (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'topics',
			'from_fieldname'  => 'tid',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_topic_id'
		);

		// Topic reply count (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'topics',
			'from_fieldname'  => 'posts',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_reply_count',
			'callback_method' => 'callback_topic_reply_count'
		);

		// Topic parent forum id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'topics',
			'from_fieldname'  => 'forum_id',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_forum_id',
			'callback_method' => 'callback_forumid'
		);

		// Topic author.
		$this->field_map[] = array(
			'from_tablename'  => 'topics',
			'from_fieldname'  => 'starter_id',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_author',
			'callback_method' => 'callback_userid'
		);

		// Topic content.
		// Note: We join the posts table because topics do not have content.
		$this->field_map[] = array(
			'from_tablename'  => 'posts',
			'from_fieldname'  => 'post',
			'join_tablename'  => 'topics',
			'join_type'       => 'INNER',
			'join_expression' => 'ON(topics.tid = posts.topic_id) WHERE posts.new_topic = 1',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_html'
		);

		// Topic title.
		$this->field_map[] = array(
			'from_tablename'  => 'topics',
			'from_fieldname'  => 'title',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_title'
		);

		// Topic slug (Clean name to avoid conflicts)
		$this->field_map[] = array(
			'from_tablename'  => 'topics',
			'from_fieldname'  => 'title',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Topic parent forum id (If no parent, then 0)
		$this->field_map[] = array(
			'from_tablename'  => 'topics',
			'from_fieldname'  => 'forum_id',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_parent',
			'callback_method' => 'callback_forumid'
		);

		// Sticky status (Stored in postmeta))
		$this->field_map[] = array(
			'from_tablename'  => 'topics',
			'from_fieldname'  => 'pinned',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_old_sticky_status',
			'callback_method' => 'callback_sticky_status'
		);

		// Topic dates.
		$this->field_map[] = array(
			'from_tablename'  => 'topics',
			'from_fieldname'  => 'start_date',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_date',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'topics',
			'from_fieldname'  => 'start_date',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_date_gmt',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'topics',
			'from_fieldname'  => 'last_post',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_modified',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'topics',
			'from_fieldname'  => 'last_post',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_modified_gmt',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename' => 'topics',
			'from_fieldname' => 'last_post',
			'to_type'        => 'topic',
			'to_fieldname'   => '_bbp_last_active_time',
			'callback_method' => 'callback_datetime'
		);

		/** Tags Section ******************************************************/

		// Topic id.
		$this->field_map[] = array(
			'from_tablename'  => 'core_tags',
			'from_fieldname'  => 'tag_meta_id',
			'to_type'         => 'tags',
			'to_fieldname'    => 'objectid',
			'callback_method' => 'callback_topicid'
		);

		// Term text.
		$this->field_map[] = array(
			'from_tablename'  => 'core_tags',
			'from_fieldname'  => 'tag_text',
			'to_type'         => 'tags',
			'to_fieldname'    => 'name'
		);

		/** Reply Section *****************************************************/

		// Reply id (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'posts',
			'from_fieldname'  => 'pid',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_post_id'
		);

		// Reply parent forum id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'posts',
			'from_fieldname'  => 'topic_id',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_forum_id',
			'callback_method' => 'callback_topicid_to_forumid'
		);

		// Reply parent topic id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'posts',
			'from_fieldname'  => 'topic_id',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_topic_id',
			'callback_method' => 'callback_topicid'
		);

		// Reply author ip (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'posts',
			'from_fieldname'  => 'ip_address',
			'to_type'         => 'reply',
			'to_fieldname'    => '_bbp_author_ip'
		);

		// Reply author.
		$this->field_map[] = array(
			'from_tablename'  => 'posts',
			'from_fieldname'  => 'author_id',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_author',
			'callback_method' => 'callback_userid'
		);

		// Reply title.
		// Note: We join the topics table because post table does not include topic title.
		$this->field_map[] = array(
			'from_tablename'  => 'topics',
			'from_fieldname'  => 'title',
			'join_tablename'  => 'posts',
			'join_type'       => 'INNER',
			'join_expression' => 'ON (topics.tid = posts.topic_id) WHERE posts.new_topic = 0',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_title',
			'callback_method' => 'callback_reply_title'
		);

		// Reply content.
		$this->field_map[] = array(
			'from_tablename'  => 'posts',
			'from_fieldname'  => 'post',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_html'
		);

		// Reply parent topic id (If no parent, then 0)
		$this->field_map[] = array(
			'from_tablename'  => 'posts',
			'from_fieldname'  => 'topic_id',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_parent',
			'callback_method' => 'callback_topicid'
		);

		// Reply dates.
		$this->field_map[] = array(
			'from_tablename'  => 'posts',
			'from_fieldname'  => 'post_date',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_date',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'posts',
			'from_fieldname'  => 'post_date',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_date_gmt',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'posts',
			'from_fieldname'  => 'edit_time',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_modified',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'posts',
			'from_fieldname'  => 'edit_time',
			'to_type'         => 'reply',
			'to_fieldname'    => 'post_modified_gmt',
			'callback_method' => 'callback_datetime'
		);

		/** User Section ******************************************************/

		// Store old User id (Stored in usermeta)
		$this->field_map[] = array(
			'from_tablename'  => 'members',
			'from_fieldname'  => 'member_id',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_user_id'
		);

		// Store old User password (Stored in usermeta serialized with salt)
		$this->field_map[] = array(
			'from_tablename'  => 'members',
			'from_fieldname'  => 'members_pass_hash',
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_password',
			'callback_method' => 'callback_savepass'
		);

		// Store old User Salt (This is only used for the SELECT row info for the above password save)
		$this->field_map[] = array(
			'from_tablename'  => 'members',
			'from_fieldname'  => 'members_pass_salt',
			'to_type'         => 'user',
			'to_fieldname'    => ''
		);

		// User password verify class (Stored in usermeta for verifying password)
		$this->field_map[] = array(
			'to_type'         => 'user',
			'to_fieldname'    => '_bbp_class',
			'default' => 'Invision'
		);

		// User name.
		$this->field_map[] = array(
			'from_tablename'  => 'members',
			'from_fieldname'  => 'name',
			'to_type'         => 'user',
			'to_fieldname'    => 'user_login'
		);

		// User nice name.
		$this->field_map[] = array(
			'from_tablename'  => 'members',
			'from_fieldname'  => 'name',
			'to_type'         => 'user',
			'to_fieldname'    => 'user_nicename'
		);

		// User email.
		$this->field_map[] = array(
			'from_tablename'  => 'members',
			'from_fieldname'  => 'email',
			'to_type'         => 'user',
			'to_fieldname'    => 'user_email'
		);

		// User registered.
		$this->field_map[] = array(
			'from_tablename'  => 'members',
			'from_fieldname'  => 'joined',
			'to_type'         => 'user',
			'to_fieldname'    => 'user_registered',
			'callback_method' => 'callback_datetime'
		);

		// User display name.
		$this->field_map[] = array(
			'from_tablename' => 'members',
			'from_fieldname' => 'members_display_name',
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
	 * Translate the forum type from Invision numeric's to WordPress's strings.
	 *
	 * @param int $status Invision numeric forum type
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
	 * Translate the topic sticky status type from Invision numeric's to WordPress's strings.
	 *
	 * @param int $status Invision numeric forum type
	 * @return string WordPress safe
	 */
	public function callback_sticky_status( $status = 0 ) {
		switch ( $status ) {
			case 1 :
				$status = 'sticky';       // Invision Pinned Topic 'pinned = 1'
				break;

			case 0  :
			default :
				$status = 'normal';       // Invision Normal Topic 'pinned = 0'
				break;
		}
		return $status;
	}

	/**
	 * Verify the topic reply count.
	 *
	 * @param int $count Invision reply count
	 * @return string WordPress safe
	 */
	public function callback_topic_reply_count( $count = 1 ) {
		$count = absint( (int) $count - 1 );
		return $count;
	}

	/**
	 * Set the reply title
	 *
	 * @param string $title Invision topic title of this reply
	 * @return string Prefixed topic title, or empty string
	 */
	public function callback_reply_title( $title = '' ) {
		$title = !empty( $title ) ? __( 'Re: ', 'bbpress' ) . html_entity_decode( $title ) : '';
		return $title;
	}

	/**
	 * This method is to save the salt and password together.  That
	 * way when we authenticate it we can get it out of the database
	 * as one value. Array values are auto sanitized by WordPress.
	 */
	public function callback_savepass( $field, $row ) {
		return array( 'hash' => $field, 'salt' => $row['members_pass_salt'] );
	}

	/**
	 * This method is to take the pass out of the database and compare
	 * to a pass the user has typed in.
	 */
	public function authenticate_pass( $password, $serialized_pass ) {
		$pass_array = unserialize( $serialized_pass );
		return ( $pass_array['hash'] == md5( md5( $pass_array['salt'] ) . md5( $this->to_char( $password ) ) ) );
	}

	private function to_char( $input ) {
		$output = "";
		for ( $i = 0; $i < strlen( $input ); $i++ ) {
			$j = ord( $input{$i} );
			if ( ( $j >= 65 && $j <= 90 )
				|| ( $j >= 97 && $j <= 122 )
				|| ( $j >= 48 && $j <= 57 ) )
			{
				$output .= $input{$i};
			} else {
				$output .= "&#" . ord( $input{$i} ) . ";";
			}
		}
		return $output;
	}

	/**
	* This callback processes any custom BBCodes with parser.php
	*/
	protected function callback_html( $field ) {

		// Strips Invision custom HTML first from $field before parsing $field to parser.php
		$invision_markup = $field;
		$invision_markup = html_entity_decode( $invision_markup );

		// Replace '[html]' with '<pre><code>'
		$invision_markup = preg_replace( '/\[html\]/', '<pre><code>',     $invision_markup );
		// Replace '[/html]' with '</code></pre>'
		$invision_markup = preg_replace( '/\[\/html\]/', '</code></pre>', $invision_markup );
		// Replace '[sql]' with '<pre><code>'
		$invision_markup = preg_replace( '/\[sql\]/', '<pre><code>',      $invision_markup );
		// Replace '[/sql]' with '</code></pre>'
		$invision_markup = preg_replace( '/\[\/sql\]/', '</code></pre>',  $invision_markup );
		// Replace '[php]' with '<pre><code>'
		$invision_markup = preg_replace( '/\[php\]/', '<pre><code>',      $invision_markup );
		// Replace '[/php]' with '</code></pre>'
		$invision_markup = preg_replace( '/\[\/php\]/', '</code></pre>',  $invision_markup );
		// Replace '[xml]' with '<pre><code>'
		$invision_markup = preg_replace( '/\[xml\]/', '<pre><code>',      $invision_markup );
		// Replace '[/xml]' with '</code></pre>'
		$invision_markup = preg_replace( '/\[\/xml\]/', '</code></pre>',  $invision_markup );
		// Replace '[CODE]' with '<pre><code>'
		$invision_markup = preg_replace( '/\[CODE\]/', '<pre><code>',     $invision_markup );
		// Replace '[/CODE]' with '</code></pre>'
		$invision_markup = preg_replace( '/\[\/CODE\]/', '</code></pre>', $invision_markup );

		// Replace '[quote:XXXXXXX]' with '<blockquote>'
		$invision_markup = preg_replace( '/\[quote:(.*?)\]/', '<blockquote>',                            $invision_markup );
		// Replace '[quote="$1"]' with '<em>@$1 wrote:</em><blockquote>'
		$invision_markup = preg_replace( '/\[quote="(.*?)":(.*?)\]/', '<em>@$1 wrote:</em><blockquote>', $invision_markup );
		// Replace '[/quote:XXXXXXX]' with '</blockquote>'
		$invision_markup = preg_replace( '/\[\/quote:(.*?)\]/', '</blockquote>',                         $invision_markup );

		// Replace '[twitter]$1[/twitter]' with '<a href="https://twitter.com/$1">@$1</a>"
		$invision_markup = preg_replace( '/\[twitter\](.*?)\[\/twitter\]/', '<a href="https://twitter.com/$1">@$1</a>', $invision_markup );

		// Replace '[member='username']' with '@username"
		$invision_markup = preg_replace( '/\[member=\'(.*?)\'\]/', '@$1 ', $invision_markup );

		// Replace '[media]' with ''
		$invision_markup = preg_replace( '/\[media\]/', '',   $invision_markup );
		// Replace '[/media]' with ''
		$invision_markup = preg_replace( '/\[\/media\]/', '', $invision_markup );

		// Replace '[list:XXXXXXX]' with '<ul>'
		$invision_markup = preg_replace( '/\[list\]/', '<ul>',                    $invision_markup );
		// Replace '[list=1:XXXXXXX]' with '<ul>'
		$invision_markup = preg_replace( '/\[list=1\]/', '<ul>',                  $invision_markup );
		// Replace '[*:XXXXXXX]' with '<li>'
		$invision_markup = preg_replace( '/\[\*\](.*?)\<br \/\>/', '<li>$1</li>', $invision_markup );
		// Replace '[/list:u:XXXXXXX]' with '</ul>'
		$invision_markup = preg_replace( '/\[\/list\]/', '</ul>',                 $invision_markup );

		// Replace '[hr]' with '<hr>"
		$invision_markup = preg_replace( '/\[hr\]/', '<hr>',     $invision_markup );

		// Replace '[font=XXXXXXX]' with ''
		$invision_markup = preg_replace( '/\[font=(.*?)\]/', '', $invision_markup );
		// Replace '[/font]' with ''
		$invision_markup = preg_replace( '/\[\/font\]/', '',     $invision_markup );

		// Replace any Invision smilies from path '/sp-resources/forum-smileys/sf-smily.gif' with the equivelant WordPress Smilie
		$invision_markup = preg_replace( '/\<img src=(.*?)EMO\_DIR(.*?)bbc_emoticon(.*?)alt=\'(.*?)\' \/\>/', '$4', $invision_markup );
		$invision_markup = preg_replace( '/\:angry\:/',    ':mad:',     $invision_markup );
		$invision_markup = preg_replace( '/\:mellow\:/',   ':neutral:', $invision_markup );
		$invision_markup = preg_replace( '/\:blink\:/',    ':eek:',     $invision_markup );
		$invision_markup = preg_replace( '/B\)/',          ':cool:',    $invision_markup );
		$invision_markup = preg_replace( '/\:rolleyes\:/', ':roll:',    $invision_markup );
		$invision_markup = preg_replace( '/\:unsure\:/',   ':???:',     $invision_markup );

		// Now that Invision custom HTML has been stripped put the cleaned HTML back in $field
		$field = $invision_markup;

		require_once( bbpress()->admin->admin_dir . 'parser.php' );
		$bbcode = BBCode::getInstance();
		$bbcode->enable_smileys = false;
		$bbcode->smiley_regex   = false;
		return html_entity_decode( $bbcode->Parse( $field ) );
	}
}
