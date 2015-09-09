<?php
/**
 * Core Comments API
 *
 * @package WordPress
 * @subpackage Comment
 * @since 1.5.0
 */

/** WP_Comment class */
require_once( ABSPATH . WPINC . '/class-wp-comment.php' );

/** WP_Comment_Query class */
require_once( ABSPATH . WPINC . '/class-wp-comment-query.php' );

/** Walker_Comment class */
require_once( ABSPATH . WPINC . '/class-walker-comment.php' );

/** Core comments functionality */
require_once( ABSPATH . WPINC . '/comment-functions.php' );
