<?php
/**
 * Comment API: WP_Comment class
 *
 * @package WordPress
 * @subpackage Comments
 * @since 4.4.0
 */

/**
 * Core class used to organize comments as instantiated objects with defined members.
 *
 * The `@property-read` fields below are not stored on the comment. They are proxied to the
 * comment's post by {@see WP_Comment::__get()}, and are null when the comment is not attached
 * to a post or when that post no longer exists.
 *
 * @since 4.4.0
 *
 * @property-read numeric-string|''|null $post_author
 * @property-read string|null            $post_date
 * @property-read string|null            $post_date_gmt
 * @property-read string|null            $post_content
 * @property-read string|null            $post_title
 * @property-read string|null            $post_excerpt
 * @property-read non-empty-string|null  $post_status
 * @property-read non-empty-string|null  $comment_status
 * @property-read non-empty-string|null  $ping_status
 * @property-read string|null            $post_name
 * @property-read string|null            $to_ping
 * @property-read string|null            $pinged
 * @property-read string|null            $post_modified
 * @property-read string|null            $post_modified_gmt
 * @property-read string|null            $post_content_filtered
 * @property-read non-negative-int|null  $post_parent
 * @property-read string|null            $guid
 * @property-read int|null               $menu_order
 * @property-read non-empty-string|null  $post_type
 * @property-read string|null            $post_mime_type
 * @property-read numeric-string|null    $comment_count
 *
 * @phpstan-type Data_Array array{
 *     comment_ID: numeric-string,
 *     comment_post_ID: numeric-string,
 *     comment_author: string,
 *     comment_author_email: string,
 *     comment_author_url: string,
 *     comment_author_IP: string,
 *     comment_date: non-empty-string,
 *     comment_date_gmt: non-empty-string,
 *     comment_content: string,
 *     comment_karma: numeric-string,
 *     comment_approved: non-empty-string,
 *     comment_agent: string,
 *     comment_type: string,
 *     comment_parent: numeric-string,
 *     user_id: numeric-string,
 *     ...
 * }
 */
#[AllowDynamicProperties]
final class WP_Comment {

	/**
	 * Comment ID.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @since 4.4.0
	 * @var string
	 * @phpstan-var numeric-string
	 */
	public $comment_ID;

	/**
	 * ID of the post the comment is associated with.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @since 4.4.0
	 * @var string
	 * @phpstan-var numeric-string
	 */
	public $comment_post_ID = '0';

	/**
	 * Comment author name.
	 *
	 * @since 4.4.0
	 * @var string
	 */
	public $comment_author = '';

	/**
	 * Comment author email address.
	 *
	 * @since 4.4.0
	 * @var string
	 */
	public $comment_author_email = '';

	/**
	 * Comment author URL.
	 *
	 * @since 4.4.0
	 * @var string
	 */
	public $comment_author_url = '';

	/**
	 * Comment author IP address (IPv4 format).
	 *
	 * @since 4.4.0
	 * @var string
	 */
	public $comment_author_IP = '';

	/**
	 * Comment date in YYYY-MM-DD HH:MM:SS format.
	 *
	 * @since 4.4.0
	 * @var string
	 * @phpstan-var non-empty-string
	 */
	public $comment_date = '0000-00-00 00:00:00';

	/**
	 * Comment GMT date in YYYY-MM-DD HH::MM:SS format.
	 *
	 * @since 4.4.0
	 * @var string
	 * @phpstan-var non-empty-string
	 */
	public $comment_date_gmt = '0000-00-00 00:00:00';

	/**
	 * Comment content.
	 *
	 * @since 4.4.0
	 * @var string
	 */
	public $comment_content;

	/**
	 * Comment karma count.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @since 4.4.0
	 * @var string
	 * @phpstan-var numeric-string
	 */
	public $comment_karma = '0';

	/**
	 * Comment approval status.
	 *
	 * The values used in core are '0' (unapproved), '1' (approved), 'spam', 'trash',
	 * and 'post-trashed' (set for every comment on a post that is moved to the trash).
	 *
	 * @since 4.4.0
	 * @var string
	 * @phpstan-var non-empty-string
	 */
	public $comment_approved = '1';

	/**
	 * Comment author HTTP user agent.
	 *
	 * @since 4.4.0
	 * @var string
	 */
	public $comment_agent = '';

	/**
	 * Comment type.
	 *
	 * The values used in core are 'comment', 'pingback', 'trackback', and 'note'. Custom
	 * comment types are possible.
	 *
	 * Comments created before 5.5.0 may store an empty string rather than 'comment', so this
	 * cannot be relied upon to be non-empty. {@see get_comment_type()} normalizes that case
	 * when reading.
	 *
	 * @since 4.4.0
	 * @since 5.5.0 Default value changed to `comment`.
	 * @var string
	 */
	public $comment_type = 'comment';

	/**
	 * Parent comment ID.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @since 4.4.0
	 * @var string
	 * @phpstan-var numeric-string
	 */
	public $comment_parent = '0';

	/**
	 * Comment author ID.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @since 4.4.0
	 * @var string
	 * @phpstan-var numeric-string
	 */
	public $user_id = '0';

	/**
	 * Comment children.
	 *
	 * Mapping of comment ID to WP_Comment object, as populated by the default
	 * `hierarchical => 'threaded'` argument of get_children(). Note that if a
	 * caller passes a `hierarchical` value of 'flat' or `false` to
	 * get_children(), a sequentially-keyed array of WP_Comment objects (also
	 * including all descendants, in the 'flat' case) is stored here instead.
	 *
	 * Null until populated by {@see WP_Comment::get_children()}.
	 *
	 * @since 4.4.0
	 * @var array<int, WP_Comment>|null
	 */
	protected $children;

	/**
	 * Whether children have been populated for this comment object.
	 *
	 * @since 4.4.0
	 * @var bool
	 */
	protected $populated_children = false;

	/**
	 * Post fields.
	 *
	 * @since 4.4.0
	 * @var string[]
	 * @phpstan-var list<non-empty-string>
	 */
	protected $post_fields = array( 'post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_title', 'post_excerpt', 'post_status', 'comment_status', 'ping_status', 'post_name', 'to_ping', 'pinged', 'post_modified', 'post_modified_gmt', 'post_content_filtered', 'post_parent', 'guid', 'menu_order', 'post_type', 'post_mime_type', 'comment_count' );

	/**
	 * Retrieves a WP_Comment instance.
	 *
	 * @since 4.4.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param int $id Comment ID.
	 * @phpstan-param int|numeric-string $id
	 * @return WP_Comment|false Comment object, otherwise false.
	 */
	public static function get_instance( $id ) {
		global $wpdb;

		$comment_id = (int) $id;
		if ( ! $comment_id ) {
			return false;
		}

		$_comment = wp_cache_get( $comment_id, 'comment' );

		if ( ! is_object( $_comment ) ) {
			/** @var object{ comment_ID: string, comment_post_ID: string, comment_author: string, comment_author_email: string, comment_author_url: string, comment_author_IP: string, comment_date: string, comment_date_gmt: string, comment_content: string, comment_karma: string, comment_approved: string, comment_agent: string, comment_type: string, comment_parent: string, user_id: string }|null $_comment */
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
	 *
	 * @param object $comment Comment object.
	 */
	public function __construct( $comment ) {
		foreach ( get_object_vars( $comment ) as $key => $value ) {
			$this->$key = $value;
		}
	}

	/**
	 * Converts object to array.
	 *
	 * @since 4.4.0
	 *
	 * @return array<string, mixed> Object as array.
	 * @phpstan-return Data_Array
	 */
	public function to_array(): array {
		/** @var Data_Array $comment */
		$comment = get_object_vars( $this );
		return $comment;
	}

	/**
	 * Gets the children of a comment.
	 *
	 * @since 4.4.0
	 * @since 7.1.0 A `count` or `fields` query now returns its result directly rather than
	 *              erroneously storing it in the comment's children cache.
	 *
	 * @param array $args {
	 *     Array of arguments used to pass to {@see get_comments()} and determine format.
	 *     Any other argument accepted by {@see WP_Comment_Query::__construct()} may also be passed, and is
	 *     forwarded to `get_comments()`. Note that `parent` is always overridden with this comment's ID.
	 *     A `$count` or `$fields` query returns the direct children only, and does not populate the
	 *     comment's cached children, since that cache holds `WP_Comment` objects.
	 *
	 *     @type string $format        Return value format. 'tree' for a hierarchical tree, 'flat' for a flattened array.
	 *                                 Default 'tree'.
	 *     @type string $status        Comment status to limit results by. Accepts 'hold' (`comment_status=0`),
	 *                                 'approve' (`comment_status=1`), 'all', or a custom comment status.
	 *                                 Default 'all'.
	 *     @type string $hierarchical  Whether to include comment descendants in the results.
	 *                                 'threaded' returns a tree, with each comment's children
	 *                                 stored in a `children` property on the `WP_Comment` object.
	 *                                 'flat' returns a flat array of found comments plus their children.
	 *                                 Pass `false` to leave out descendants.
	 *                                 The parameter is ignored (forced to `false`) when `$fields` is 'ids' or 'counts'.
	 *                                 Accepts 'threaded', 'flat', or false. Default: 'threaded'.
	 *     @type string|array $orderby Comment status or array of statuses. To use 'meta_value'
	 *                                 or 'meta_value_num', `$meta_key` must also be defined.
	 *                                 To sort by a specific `$meta_query` clause, use that
	 *                                 clause's array key. Accepts 'comment_agent',
	 *                                 'comment_approved', 'comment_author',
	 *                                 'comment_author_email', 'comment_author_IP',
	 *                                 'comment_author_url', 'comment_content', 'comment_date',
	 *                                 'comment_date_gmt', 'comment_ID', 'comment_karma',
	 *                                 'comment_parent', 'comment_post_ID', 'comment_type',
	 *                                 'user_id', 'comment__in', 'meta_value', 'meta_value_num',
	 *                                 the value of $meta_key, and the array keys of
	 *                                 `$meta_query`. Also accepts false, an empty array, or
	 *                                 'none' to disable `ORDER BY` clause.
	 *     @type string $fields        Which fields to return. Accepts 'ids' for comment IDs, or an
	 *                                 empty string for full `WP_Comment` objects. Default empty.
	 *     @type bool   $count         Whether to return a comment count rather than comments.
	 *                                 Default false.
	 *     @type string $type          Limit results to comments of a given type, such as 'comment',
	 *                                 'pingback', 'trackback', or 'note'. Accepts 'all' for every
	 *                                 type. Default empty.
	 *     @type int    $number        Maximum number of comments to retrieve. Default empty (no limit).
	 *     @type int    $post_id       Limit results to comments on a given post. Default 0.
	 *     @type string $order         How to order retrieved comments. Accepts 'ASC' or 'DESC'.
	 *                                 Default 'DESC'.
	 * }
	 * @return WP_Comment[]|int[]|int Array of `WP_Comment` objects, an array of comment IDs when
	 *                                `$fields` is 'ids', or the number of children when `$count`
	 *                                is true.
	 *
	 * @phpstan-param array{
	 *                    format?: 'tree'|'flat',
	 *                    status?: 'hold'|'approve'|'all'|string,
	 *                    hierarchical?: 'threaded'|'flat'|false,
	 *                    orderby?: string|string[]|false,
	 *                    fields?: 'ids'|'',
	 *                    count?: bool,
	 *                    type?: string,
	 *                    number?: int,
	 *                    post_id?: int,
	 *                    order?: 'ASC'|'DESC',
	 *                    ...
	 *                } $args
	 * @phpstan-return (
	 *     $args is array{ count: true, ... } ? non-negative-int : (
	 *         $args is array{ fields: 'ids', ... } ? non-negative-int[] : (
	 *             $args is array{ format: 'flat', ... } ? list<WP_Comment> : array<int, WP_Comment>
	 *         )
	 *     )
	 * )
	 */
	public function get_children( $args = array() ) {
		$defaults = array(
			'format'       => 'tree',
			'status'       => 'all',
			'hierarchical' => 'threaded',
			'orderby'      => '',
		);

		/** @var array{ format: 'tree'|'flat', status: string, hierarchical: 'threaded'|'flat'|false, orderby: string|string[]|false, fields?: 'ids'|'', count?: bool, type?: string, number?: int, post_id?: int, order?: 'ASC'|'DESC', ... } $_args */
		$_args           = wp_parse_args( $args, $defaults );
		$_args['parent'] = $this->comment_ID;

		/*
		 * A 'count' or 'ids' query returns an integer or a list of comment IDs rather than
		 * WP_Comment objects. Neither may be written to the children cache, which holds
		 * WP_Comment objects and is read back by add_child(), get_child(), and the 'flat'
		 * format below. Return the result directly and leave the cache untouched. The two
		 * branches must stay separate: each is narrowed independently, and `count` is only
		 * safe to overwrite in the 'ids' branch.
		 */
		if ( ! empty( $_args['count'] ) ) {
			return get_comments( $_args );
		} elseif ( isset( $_args['fields'] ) && 'ids' === $_args['fields'] ) {
			$_args['count'] = false; // For static analysis of the conditional return type.
			return get_comments( $_args );
		}

		// Only WP_Comment objects are returned past this point. Stated positively for static analysis.
		$_args['count']  = false;
		$_args['fields'] = '';

		if ( is_null( $this->children ) ) {
			if ( $this->populated_children ) {
				$this->children = array();
			} else {
				$this->children = get_comments( $_args );
			}
		}

		if ( 'flat' === $_args['format'] ) {
			$children = array();
			foreach ( $this->children as $child ) {
				$child_args           = $_args;
				$child_args['format'] = 'flat';
				// get_children() resets this value automatically.
				unset( $child_args['parent'] );

				$children = array_merge( $children, array( $child ), $child->get_children( $child_args ) );
			}
		} else {
			$children = $this->children;
		}

		return $children;
	}

	/**
	 * Adds a child to the comment.
	 *
	 * Used by `WP_Comment_Query` when bulk-filling descendants.
	 *
	 * @since 4.4.0
	 *
	 * @param WP_Comment $child Child comment.
	 */
	public function add_child( WP_Comment $child ): void {
		$this->children[ (int) $child->comment_ID ] = $child;
	}

	/**
	 * Gets a child comment by ID.
	 *
	 * @since 4.4.0
	 *
	 * @param int $child_id ID of the child.
	 * @return WP_Comment|false Returns the comment object if found, otherwise false.
	 */
	public function get_child( $child_id ) {
		return $this->children[ $child_id ] ?? false;
	}

	/**
	 * Sets the 'populated_children' flag.
	 *
	 * This flag is important for ensuring that calling `get_children()` on a childless comment will not trigger
	 * unneeded database queries.
	 *
	 * @since 4.4.0
	 *
	 * @param bool $set Whether the comment's children have already been populated.
	 */
	public function populated_children( $set ): void {
		$this->populated_children = (bool) $set;
	}

	/**
	 * Determines whether a non-public property is set.
	 *
	 * If `$name` matches a post field, the comment post will be loaded and the post's value checked.
	 *
	 * @since 4.4.0
	 * @since 7.1.0 Returns false instead of causing a fatal error when the comment's post cannot be found.
	 *
	 * @param string $name Property to check if set.
	 * @return bool Whether the property is set.
	 */
	public function __isset( $name ) {
		if ( in_array( $name, $this->post_fields, true ) && 0 !== (int) $this->comment_post_ID ) {
			$post = get_post( (int) $this->comment_post_ID );
			return $post && property_exists( $post, $name );
		}

		return false;
	}

	/**
	 * Magic getter.
	 *
	 * If `$name` matches a post field, the comment post will be loaded and the post's value returned.
	 *
	 * @since 4.4.0
	 * @since 7.1.0 Returns null instead of the global post's field when the comment is not attached to
	 *              a post, and no longer raises a warning when the comment's post cannot be found.
	 *
	 * @param string $name Property name.
	 * @return mixed
	 */
	public function __get( $name ) {
		if ( in_array( $name, $this->post_fields, true ) && 0 !== (int) $this->comment_post_ID ) {
			$post = get_post( (int) $this->comment_post_ID );
			if ( ! $post ) {
				return null;
			}
			return $post->$name;
		}
		return null;
	}
}
