<?php

/**
 * Implementation of Kunena v1.x Forums for Joomla Forum converter.
 *
 * @since bbPress (r5144)
 * @link Codex Docs http://codex.bbpress.org/import-forums/kunena/
 */
class Kunena1 extends BBP_Converter_Base {

	/**
	 * Main Constructor
	 *
	 * @uses Kunena1::setup_globals()
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
        	'from_tablename' => 'kunena_categories',
        	'from_fieldname' => 'parent',
        	'to_type'        => 'forum',
        	'to_fieldname'   => '_bbp_forum_parent_id'
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
        	'from_fieldname'  => 'name',
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

		// Forum type (Category = 0 or Forum > 0, Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_categories',
			'from_fieldname'  => 'parent',
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
			'from_tablename' => 'kunena_messages',
			'from_fieldname' => 'thread',
			'to_type'        => 'topic',
			'to_fieldname'   => '_bbp_topic_id'
		);

		// Topic parent forum id (If no parent, then 0. Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_messages',
			'from_fieldname'  => 'catid',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_forum_id',
			'callback_method' => 'callback_forumid'
		);

		// Topic author.
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_messages',
			'from_fieldname'  => 'userid',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_author',
			'callback_method' => 'callback_userid'
		);

		// Topic Author ip (Stored in postmeta)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_messages',
			'from_fieldname'  => 'ip',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_author_ip'
		);

		// Topic content.
		// Note: We join the 'kunena_messages_text' table because 'kunena_messages' table does not include topic content.
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_messages_text',
			'from_fieldname'  => 'message',
			'join_tablename'  => 'kunena_messages',
			'join_type'       => 'INNER',
			'join_expression' => 'ON kunena_messages_text.mesid = kunena_messages.id WHERE kunena_messages.parent = 0',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_content',
			'callback_method' => 'callback_html'
		);

		// Topic title.
		$this->field_map[] = array(
			'from_tablename' => 'kunena_messages',
			'from_fieldname' => 'subject',
			'to_type'        => 'topic',
			'to_fieldname'   => 'post_title'
		);

		// Topic slug (Clean name to avoid conflicts)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_messages',
			'from_fieldname'  => 'subject',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_name',
			'callback_method' => 'callback_slug'
		);

		// Topic parent forum id (If no parent, then 0)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_messages',
			'from_fieldname'  => 'catid',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_parent',
			'callback_method' => 'callback_forumid'
		);

		// Topic dates.
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_messages',
			'from_fieldname'  => 'time',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_date',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_messages',
			'from_fieldname'  => 'time',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_date_gmt',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_messages',
			'from_fieldname'  => 'time',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_modified',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_messages',
			'from_fieldname'  => 'time',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_modified_gmt',
			'callback_method' => 'callback_datetime'
		);
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_messages',
			'from_fieldname'  => 'time',
			'to_type'         => 'topic',
			'to_fieldname'    => '_bbp_last_active_time',
			'callback_method' => 'callback_datetime'
		);

		// Topic status (Open or Closed, Kunena v3.x 0=open & 1=closed)
		$this->field_map[] = array(
			'from_tablename'  => 'kunena_messages',
			'from_fieldname'  => 'locked',
			'to_type'         => 'topic',
			'to_fieldname'    => 'post_status',
			'callback_method' => 'callback_topic_status'
		);

        /** Tags Section ******************************************************/

		/**
		 * Kunena v1.x Forums do not support topic tags out of the box
		 */

        /** Reply Section *****************************************************/

        // Reply id (Stored in postmeta)
        $this->field_map[] = array(
        	'from_tablename' => 'kunena_messages',
        	'from_fieldname' => 'id',
        	'to_type'        => 'reply',
        	'to_fieldname'   => '_bbp_post_id'
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
        	'join_type'       => 'INNER',
        	'join_expression' => 'ON kunena_messages.id = kunena_messages_text.mesid WHERE kunena_messages.parent != 0',
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

        //Note: We are importing the Joomla User details and the Kunena v1.x user profile details.

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
//        $this->field_map[] = array(
//        	'from_tablename' => 'user',
//        	'from_fieldname' => 'salt',
//        	'to_type'        => 'user',
//        	'to_fieldname'   => ''
//		);

        // User password verify class (Stored in usermeta for verifying password)
//        $this->field_map[] = array(
//        	'to_type'      => 'user',
//        	'to_fieldname' => '_bbp_class',
//        	'default'      => 'Kunena1'
//		);

        // User name.
        $this->field_map[] = array(
        	'from_tablename' => 'users',
        	'from_fieldname' => 'username',
        	'to_type'        => 'user',
        	'to_fieldname'   => 'user_login'
		);

        // User email.
        $this->field_map[] = array(
        	'from_tablename' => 'users',
        	'from_fieldname' => 'email',
        	'to_type'        => 'user',
        	'to_fieldname'   => 'user_email'
		);

        // User registered.
        $this->field_map[] = array(
        	'from_tablename'  => 'users',
        	'from_fieldname'  => 'registerDate',
        	'to_type'         => 'user',
        	'to_fieldname'    => 'user_registered',
        	'callback_method' => 'callback_datetime'
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
    public function callback_savepass($field, $row) {
        $pass_array = array('hash' => $field, 'salt' => $row['salt']);
        return $pass_array;
    }

    /**
     * This method is to take the pass out of the database and compare
     * to a pass the user has typed in.
     */
    public function authenticate_pass($password, $serialized_pass) {
        $pass_array = unserialize($serialized_pass);
        return ( $pass_array['hash'] == md5(md5($password) . $pass_array['salt']) );
    }

	/**
	 * Translate the forum type from Kunena v1.x numeric's to WordPress's strings.
	 *
	 * @param int $status Kunena v1.x numeric forum type
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
	 * Translate the forum status from Kunena v1.x numeric's to WordPress's strings.
	 *
	 * @param int $status Kunena v1.x numeric forum status
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
	 * Translate the post status from Kunena v1.x numeric's to WordPress's strings.
	 *
	 * @param int $status Kunena v1.x numeric topic status
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
	 * Set the reply title
	 *
	 * @param string $title Kunena v1.x topic title of this reply
	 * @return string Prefixed topic title, or empty string
	 */
	public function callback_reply_title( $title = '' ) {
		$title = !empty( $title ) ? __( 'Re: ', 'bbpress' ) . html_entity_decode( $title ) : '';
		return $title;
	}
}