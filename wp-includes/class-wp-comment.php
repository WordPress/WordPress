<?php

 /**
 * WordPress Comment class
 *
 * @since 4.4.0
 */
final class WP_Comment {
	/**
	 * @var int
	 */
	public $comment_ID;
	/**
	 * @var int
	 */
	public $comment_post_ID = 0;
	/**
	 * @var int
	 */
	public $comment_author;
	/**
	 * @var string
	 */
	public $comment_author_email = '';
	/**
	 * @var string
	 */
	public $comment_author_url = '';
	/**
	 * @var string
	 */
	public $comment_author_IP = '';
	/**
	 * @var string
	 */
	public $comment_date = '0000-00-00 00:00:00';
	/**
	 * @var string
	 */
	public $comment_date_gmt = '0000-00-00 00:00:00';
	/**
	 * @var string
	 */
	public $comment_content;
	/**
	 * @var int
	 */
	public $comment_karma = 0;
	/**
	 * @var string
	 */
	public $comment_approved = '1';
	/**
	 * @var string
	 */
	public $comment_agent = '';
	/**
	 * @var string
	 */
	public $comment_type = '';
	/**
	 * @var int
	 */
	public $comment_parent = 0;
	/**
	 * @var int
	 */
	public $user_id = 0;

	/**
	 * Retrieve WP_Comment instance.
	 *
	 * @static
	 * @access public
	 *
	 * @global wpdb $wpdb
	 *
	 * @param int $id Comment ID.
	 * @return WP_Comment|false Comment object, false otherwise.
	 */
	public static function get_instance( $id ) {
		global $wpdb;

		$comment_id = (int) $id;
		if ( ! $comment_id ) {
			return false;
		}

		$_comment = wp_cache_get( $comment_id, 'comment' );

		if ( ! $_comment ) {
			$_comment = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->comments WHERE comment_ID = %d LIMIT 1", $comment_id ) );

			if ( ! $_comment ) {
				return false;
			}

			wp_cache_add( $_comment->comment_ID, $_comment, 'comment' );
		}

		return new WP_Comment( $_comment );
	}

	/**
	 * Constructor.
	 *
	 * @param WP_Comment $comment Comment object.
	 */
	public function __construct( $comment ) {
		foreach ( get_object_vars( $comment ) as $key => $value ) {
			$this->$key = $value;
		}
	}

	public function to_array() {
		return get_object_vars( $this );
	}
}
