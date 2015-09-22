<?php
/**
 * Comment API: WP_Comment object class
 *
 * @package WordPress
 * @subpackage Comments
 * @since 4.4.0
 */

/**
 * Core class used to organize comments as instantiated objects with defined members.
 *
 * @since 4.4.0
 */
final class WP_Comment {

	/**
	 * Comment ID.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var int
	 */
	public $comment_ID;

	/**
	 * ID of the post the comment is associated with.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var int
	 */
	public $comment_post_ID = 0;

	/**
	 * Comment author ID.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var string
	 */
	public $comment_author = '';

	/**
	 * Comment author email address.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var string
	 */
	public $comment_author_email = '';

	/**
	 * Comment author URL.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var string
	 */
	public $comment_author_url = '';

	/**
	 * Comment author IP address (IPv4 format).
	 *
	 * @since 4.4.0
	 * @access public
	 * @var string
	 */
	public $comment_author_IP = '';

	/**
	 * Comment date in YYYY-MM-DD HH:MM:SS format.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var string
	 */
	public $comment_date = '0000-00-00 00:00:00';

	/**
	 * Comment GMT date in YYYY-MM-DD HH::MM:SS format.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var string
	 */
	public $comment_date_gmt = '0000-00-00 00:00:00';

	/**
	 * Comment content.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var string
	 */
	public $comment_content;

	/**
	 * Comment karma count.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var int
	 */
	public $comment_karma = 0;

	/**
	 * Comment approval status.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var string
	 */
	public $comment_approved = '1';

	/**
	 * Comment author HTTP user agent.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var string
	 */
	public $comment_agent = '';

	/**
	 * Comment type.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var string
	 */
	public $comment_type = '';

	/**
	 * Parent comment ID.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var int
	 */
	public $comment_parent = 0;

	/**
	 * Comment author ID.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var int
	 */
	public $user_id = 0;

	/**
	 * Retrieves a WP_Comment instance.
	 *
	 * @since 4.4.0
	 * @access public
	 * @static
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param int $id Comment ID.
	 * @return WP_Comment|false Comment object, otherwise false.
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
	 * Populates properties with object vars.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param WP_Comment $comment Comment object.
	 */
	public function __construct( $comment ) {
		foreach ( get_object_vars( $comment ) as $key => $value ) {
			$this->$key = $value;
		}
	}

	/**
	 * Convert object to array.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return array Object as array.
	 */
	public function to_array() {
		return get_object_vars( $this );
	}
}
