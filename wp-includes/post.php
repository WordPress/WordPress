<?php
/**
 * Post functions and post utility function.
 *
 * @package WordPress
 * @subpackage Post
 * @since 1.5.0
 */

//
// Post Type Registration
//

/**
 * Creates the initial post types when 'init' action is fired.
 *
 * @since 2.9.0
 */
function create_initial_post_types() {
	register_post_type( 'post', array(
		'labels' => array(
			'name_admin_bar' => _x( 'Post', 'add new on admin bar' ),
		),
		'public'  => true,
		'_builtin' => true, /* internal use only. don't use this when registering your own post type. */
		'_edit_link' => 'post.php?post=%d', /* internal use only. don't use this when registering your own post type. */
		'capability_type' => 'post',
		'map_meta_cap' => true,
		'hierarchical' => false,
		'rewrite' => false,
		'query_var' => false,
		'delete_with_user' => true,
		'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'post-formats' ),
	) );

	register_post_type( 'page', array(
		'labels' => array(
			'name_admin_bar' => _x( 'Page', 'add new on admin bar' ),
		),
		'public' => true,
		'publicly_queryable' => false,
		'_builtin' => true, /* internal use only. don't use this when registering your own post type. */
		'_edit_link' => 'post.php?post=%d', /* internal use only. don't use this when registering your own post type. */
		'capability_type' => 'page',
		'map_meta_cap' => true,
		'hierarchical' => true,
		'rewrite' => false,
		'query_var' => false,
		'delete_with_user' => true,
		'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'page-attributes', 'custom-fields', 'comments', 'revisions' ),
	) );

	register_post_type( 'attachment', array(
		'labels' => array(
			'name' => _x('Media', 'post type general name'),
			'name_admin_bar' => _x( 'Media', 'add new from admin bar' ),
			'add_new' => _x( 'Add New', 'add new media' ),
 			'edit_item' => __( 'Edit Media' ),
 			'view_item' => __( 'View Attachment Page' ),
		),
		'public' => true,
		'show_ui' => true,
		'_builtin' => true, /* internal use only. don't use this when registering your own post type. */
		'_edit_link' => 'post.php?post=%d', /* internal use only. don't use this when registering your own post type. */
		'capability_type' => 'post',
		'capabilities' => array(
			'create_posts' => 'upload_files',
		),
		'map_meta_cap' => true,
		'hierarchical' => false,
		'rewrite' => false,
		'query_var' => false,
		'show_in_nav_menus' => false,
		'delete_with_user' => true,
		'supports' => array( 'title', 'author', 'comments' ),
	) );
	add_post_type_support( 'attachment:audio', 'thumbnail' );
	add_post_type_support( 'attachment:video', 'thumbnail' );

	register_post_type( 'revision', array(
		'labels' => array(
			'name' => __( 'Revisions' ),
			'singular_name' => __( 'Revision' ),
		),
		'public' => false,
		'_builtin' => true, /* internal use only. don't use this when registering your own post type. */
		'_edit_link' => 'revision.php?revision=%d', /* internal use only. don't use this when registering your own post type. */
		'capability_type' => 'post',
		'map_meta_cap' => true,
		'hierarchical' => false,
		'rewrite' => false,
		'query_var' => false,
		'can_export' => false,
		'delete_with_user' => true,
		'supports' => array( 'author' ),
	) );

	register_post_type( 'nav_menu_item', array(
		'labels' => array(
			'name' => __( 'Navigation Menu Items' ),
			'singular_name' => __( 'Navigation Menu Item' ),
		),
		'public' => false,
		'_builtin' => true, /* internal use only. don't use this when registering your own post type. */
		'hierarchical' => false,
		'rewrite' => false,
		'delete_with_user' => false,
		'query_var' => false,
	) );

	register_post_status( 'publish', array(
		'label'       => _x( 'Published', 'post' ),
		'public'      => true,
		'_builtin'    => true, /* internal use only. */
		'label_count' => _n_noop( 'Published <span class="count">(%s)</span>', 'Published <span class="count">(%s)</span>' ),
	) );

	register_post_status( 'future', array(
		'label'       => _x( 'Scheduled', 'post' ),
		'protected'   => true,
		'_builtin'    => true, /* internal use only. */
		'label_count' => _n_noop('Scheduled <span class="count">(%s)</span>', 'Scheduled <span class="count">(%s)</span>' ),
	) );

	register_post_status( 'draft', array(
		'label'       => _x( 'Draft', 'post' ),
		'protected'   => true,
		'_builtin'    => true, /* internal use only. */
		'label_count' => _n_noop( 'Draft <span class="count">(%s)</span>', 'Drafts <span class="count">(%s)</span>' ),
	) );

	register_post_status( 'pending', array(
		'label'       => _x( 'Pending', 'post' ),
		'protected'   => true,
		'_builtin'    => true, /* internal use only. */
		'label_count' => _n_noop( 'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>' ),
	) );

	register_post_status( 'private', array(
		'label'       => _x( 'Private', 'post' ),
		'private'     => true,
		'_builtin'    => true, /* internal use only. */
		'label_count' => _n_noop( 'Private <span class="count">(%s)</span>', 'Private <span class="count">(%s)</span>' ),
	) );

	register_post_status( 'trash', array(
		'label'       => _x( 'Trash', 'post' ),
		'internal'    => true,
		'_builtin'    => true, /* internal use only. */
		'label_count' => _n_noop( 'Trash <span class="count">(%s)</span>', 'Trash <span class="count">(%s)</span>' ),
		'show_in_admin_status_list' => true,
	) );

	register_post_status( 'auto-draft', array(
		'label'    => 'auto-draft',
		'internal' => true,
		'_builtin' => true, /* internal use only. */
	) );

	register_post_status( 'inherit', array(
		'label'    => 'inherit',
		'internal' => true,
		'_builtin' => true, /* internal use only. */
		'exclude_from_search' => false,
	) );
}

/**
 * Retrieve attached file path based on attachment ID.
 *
 * By default the path will go through the 'get_attached_file' filter, but
 * passing a true to the $unfiltered argument of get_attached_file() will
 * return the file path unfiltered.
 *
 * The function works by getting the single post meta name, named
 * '_wp_attached_file' and returning it. This is a convenience function to
 * prevent looking up the meta name and provide a mechanism for sending the
 * attached filename through a filter.
 *
 * @since 2.0.0
 *
 * @param int  $attachment_id Attachment ID.
 * @param bool $unfiltered    Optional. Whether to apply filters. Default false.
 * @return string|bool The file path to where the attached file should be, false otherwise.
 */
function get_attached_file( $attachment_id, $unfiltered = false ) {
	$file = get_post_meta( $attachment_id, '_wp_attached_file', true );
	// If the file is relative, prepend upload dir.
	if ( $file && 0 !== strpos($file, '/') && !preg_match('|^.:\\\|', $file) && ( ($uploads = wp_upload_dir()) && false === $uploads['error'] ) )
		$file = $uploads['basedir'] . "/$file";
	if ( $unfiltered )
		return $file;

	/**
	 * Filter the attached file based on the given ID.
	 *
	 * @since 2.1.0
	 *
	 * @param string $file          Path to attached file.
	 * @param int    $attachment_id Attachment ID.
	 */
	return apply_filters( 'get_attached_file', $file, $attachment_id );
}

/**
 * Update attachment file path based on attachment ID.
 *
 * Used to update the file path of the attachment, which uses post meta name
 * '_wp_attached_file' to store the path of the attachment.
 *
 * @since 2.1.0
 *
 * @param int    $attachment_id Attachment ID.
 * @param string $file          File path for the attachment.
 * @return bool True on success, false on failure.
 */
function update_attached_file( $attachment_id, $file ) {
	if ( !get_post( $attachment_id ) )
		return false;

	/**
	 * Filter the path to the attached file to update.
	 *
	 * @since 2.1.0
	 *
	 * @param string $file          Path to the attached file to update.
	 * @param int    $attachment_id Attachment ID.
	 */
	$file = apply_filters( 'update_attached_file', $file, $attachment_id );

	if ( $file = _wp_relative_upload_path( $file ) )
		return update_post_meta( $attachment_id, '_wp_attached_file', $file );
	else
		return delete_post_meta( $attachment_id, '_wp_attached_file' );
}

/**
 * Return relative path to an uploaded file.
 *
 * The path is relative to the current upload dir.
 *
 * @since 2.9.0
 *
 * @param string $path Full path to the file.
 * @return string Relative path on success, unchanged path on failure.
 */
function _wp_relative_upload_path( $path ) {
	$new_path = $path;

	$uploads = wp_upload_dir();
	if ( 0 === strpos( $new_path, $uploads['basedir'] ) ) {
			$new_path = str_replace( $uploads['basedir'], '', $new_path );
			$new_path = ltrim( $new_path, '/' );
	}

	/**
	 * Filter the relative path to an uploaded file.
	 *
	 * @since 2.9.0
	 *
	 * @param string $new_path Relative path to the file.
	 * @param string $path     Full path to the file.
	 */
	return apply_filters( '_wp_relative_upload_path', $new_path, $path );
}

/**
 * Retrieve all children of the post parent ID.
 *
 * Normally, without any enhancements, the children would apply to pages. In the
 * context of the inner workings of WordPress, pages, posts, and attachments
 * share the same table, so therefore the functionality could apply to any one
 * of them. It is then noted that while this function does not work on posts, it
 * does not mean that it won't work on posts. It is recommended that you know
 * what context you wish to retrieve the children of.
 *
 * Attachments may also be made the child of a post, so if that is an accurate
 * statement (which needs to be verified), it would then be possible to get
 * all of the attachments for a post. Attachments have since changed since
 * version 2.5, so this is most likely unaccurate, but serves generally as an
 * example of what is possible.
 *
 * The arguments listed as defaults are for this function and also of the
 * {@link get_posts()} function. The arguments are combined with the
 * get_children defaults and are then passed to the {@link get_posts()}
 * function, which accepts additional arguments. You can replace the defaults in
 * this function, listed below and the additional arguments listed in the
 * {@link get_posts()} function.
 *
 * The 'post_parent' is the most important argument and important attention
 * needs to be paid to the $args parameter. If you pass either an object or an
 * integer (number), then just the 'post_parent' is grabbed and everything else
 * is lost. If you don't specify any arguments, then it is assumed that you are
 * in The Loop and the post parent will be grabbed for from the current post.
 *
 * The 'post_parent' argument is the ID to get the children. The 'numberposts'
 * is the amount of posts to retrieve that has a default of '-1', which is
 * used to get all of the posts. Giving a number higher than 0 will only
 * retrieve that amount of posts.
 *
 * The 'post_type' and 'post_status' arguments can be used to choose what
 * criteria of posts to retrieve. The 'post_type' can be anything, but WordPress
 * post types are 'post', 'pages', and 'attachments'. The 'post_status'
 * argument will accept any post status within the write administration panels.
 *
 * @since 2.0.0
 *
 * @see get_posts()
 * @todo Check validity of description.
 *
 * @param mixed  $args   Optional. User defined arguments for replacing the defaults. Default empty.
 * @param string $output Optional. Constant for return type. Accepts OBJECT, ARRAY_A, ARRAY_N.
 *                       Default OBJECt.
 * @return array Array of children, where the type of each element is determined by $output parameter.
 *               Empty array on failure.
 */
function get_children( $args = '', $output = OBJECT ) {
	$kids = array();
	if ( empty( $args ) ) {
		if ( isset( $GLOBALS['post'] ) ) {
			$args = array('post_parent' => (int) $GLOBALS['post']->post_parent );
		} else {
			return $kids;
		}
	} elseif ( is_object( $args ) ) {
		$args = array('post_parent' => (int) $args->post_parent );
	} elseif ( is_numeric( $args ) ) {
		$args = array('post_parent' => (int) $args);
	}

	$defaults = array(
		'numberposts' => -1, 'post_type' => 'any',
		'post_status' => 'any', 'post_parent' => 0,
	);

	$r = wp_parse_args( $args, $defaults );

	$children = get_posts( $r );

	if ( ! $children )
		return $kids;

	if ( ! empty( $r['fields'] ) )
		return $children;

	update_post_cache($children);

	foreach ( $children as $key => $child )
		$kids[$child->ID] = $children[$key];

	if ( $output == OBJECT ) {
		return $kids;
	} elseif ( $output == ARRAY_A ) {
		$weeuns = array();
		foreach ( (array) $kids as $kid ) {
			$weeuns[$kid->ID] = get_object_vars($kids[$kid->ID]);
		}
		return $weeuns;
	} elseif ( $output == ARRAY_N ) {
		$babes = array();
		foreach ( (array) $kids as $kid ) {
			$babes[$kid->ID] = array_values(get_object_vars($kids[$kid->ID]));
		}
		return $babes;
	} else {
		return $kids;
	}
}

/**
 * Get extended entry info (<!--more-->).
 *
 * There should not be any space after the second dash and before the word
 * 'more'. There can be text or space(s) after the word 'more', but won't be
 * referenced.
 *
 * The returned array has 'main', 'extended', and 'more_text' keys. Main has the text before
 * the `<!--more-->`. The 'extended' key has the content after the
 * `<!--more-->` comment. The 'more_text' key has the custom "Read More" text.
 *
 * @since 1.0.0
 *
 * @param string $post Post content.
 * @return array Post before ('main'), after ('extended'), and custom readmore ('more_text').
 */
function get_extended( $post ) {
	//Match the new style more links.
	if ( preg_match('/<!--more(.*?)?-->/', $post, $matches) ) {
		list($main, $extended) = explode($matches[0], $post, 2);
		$more_text = $matches[1];
	} else {
		$main = $post;
		$extended = '';
		$more_text = '';
	}

	//  leading and trailing whitespace.
	$main = preg_replace('/^[\s]*(.*)[\s]*$/', '\\1', $main);
	$extended = preg_replace('/^[\s]*(.*)[\s]*$/', '\\1', $extended);
	$more_text = preg_replace('/^[\s]*(.*)[\s]*$/', '\\1', $more_text);

	return array( 'main' => $main, 'extended' => $extended, 'more_text' => $more_text );
}

/**
 * Retrieves post data given a post ID or post object.
 *
 * See {@link sanitize_post()} for optional $filter values. Also, the parameter
 * $post, must be given as a variable, since it is passed by reference.
 *
 * @since 1.5.1
 *
 * @param int|WP_Post $post   Optional. Post ID or post object. Defaults to global $post.
 * @param string      $output Optional, default is Object. Accepts OBJECT, ARRAY_A, or ARRAY_N.
 *                            Default OBJECT.
 * @param string      $filter Optional. Type of filter to apply. Accepts 'raw', 'edit', 'db',
 *                            or 'display'. Default 'raw'.
 * @return WP_Post|array|null Type corresponding to $output on success or null on failure.
 *                            When $output is OBJECT, a `WP_Post` instance is returned.
 */
function get_post( $post = null, $output = OBJECT, $filter = 'raw' ) {
	if ( empty( $post ) && isset( $GLOBALS['post'] ) )
		$post = $GLOBALS['post'];

	if ( $post instanceof WP_Post ) {
		$_post = $post;
	} elseif ( is_object( $post ) ) {
		if ( empty( $post->filter ) ) {
			$_post = sanitize_post( $post, 'raw' );
			$_post = new WP_Post( $_post );
		} elseif ( 'raw' == $post->filter ) {
			$_post = new WP_Post( $post );
		} else {
			$_post = WP_Post::get_instance( $post->ID );
		}
	} else {
		$_post = WP_Post::get_instance( $post );
	}

	if ( ! $_post )
		return null;

	$_post = $_post->filter( $filter );

	if ( $output == ARRAY_A )
		return $_post->to_array();
	elseif ( $output == ARRAY_N )
		return array_values( $_post->to_array() );

	return $_post;
}

/**
 * WordPress Post class.
 *
 * @since 3.5.0
 *
 * @property-read array  $ancestors
 * @property-read string $page_template
 * @property-read int    $post_category
 * @property-read string $tag_input
 *
 */
final class WP_Post {

	/**
	 * Post ID.
	 *
	 * @var int
	 */
	public $ID;

	/**
	 * ID of post author.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @var string
	 */
	public $post_author = 0;

	/**
	 * The post's local publication time.
	 *
	 * @var string
	 */
	public $post_date = '0000-00-00 00:00:00';

	/**
	 * The post's GMT publication time.
	 *
	 * @var string
	 */
	public $post_date_gmt = '0000-00-00 00:00:00';

	/**
	 * The post's content.
	 *
	 * @var string
	 */
	public $post_content = '';

	/**
	 * The post's title.
	 *
	 * @var string
	 */
	public $post_title = '';

	/**
	 * The post's excerpt.
	 *
	 * @var string
	 */
	public $post_excerpt = '';

	/**
	 * The post's status.
	 *
	 * @var string
	 */
	public $post_status = 'publish';

	/**
	 * Whether comments are allowed.
	 *
	 * @var string
	 */
	public $comment_status = 'open';

	/**
	 * Whether pings are allowed.
	 *
	 * @var string
	 */
	public $ping_status = 'open';

	/**
	 * The post's password in plain text.
	 *
	 * @var string
	 */
	public $post_password = '';

	/**
	 * The post's slug.
	 *
	 * @var string
	 */
	public $post_name = '';

	/**
	 * URLs queued to be pinged.
	 *
	 * @var string
	 */
	public $to_ping = '';

	/**
	 * URLs that have been pinged.
	 *
	 * @var string
	 */
	public $pinged = '';

	/**
	 * The post's local modified time.
	 *
	 * @var string
	 */
	public $post_modified = '0000-00-00 00:00:00';

	/**
	 * The post's GMT modified time.
	 *
	 * @var string
	 */
	public $post_modified_gmt = '0000-00-00 00:00:00';

	/**
	 * A utility DB field for post content.
	 *
	 *
	 * @var string
	 */
	public $post_content_filtered = '';

	/**
	 * ID of a post's parent post.
	 *
	 * @var int
	 */
	public $post_parent = 0;

	/**
	 * The unique identifier for a post, not necessarily a URL, used as the feed GUID.
	 *
	 * @var string
	 */
	public $guid = '';

	/**
	 * A field used for ordering posts.
	 *
	 * @var int
	 */
	public $menu_order = 0;

	/**
	 * The post's type, like post or page.
	 *
	 * @var string
	 */
	public $post_type = 'post';

	/**
	 * An attachment's mime type.
	 *
	 * @var string
	 */
	public $post_mime_type = '';

	/**
	 * Cached comment count.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @var string
	 */
	public $comment_count = 0;

	/**
	 * Stores the post object's sanitization level.
	 *
	 * Does not correspond to a DB field.
	 *
	 * @var string
	 */
	public $filter;

	/**
	 * Retrieve WP_Post instance.
	 *
	 * @static
	 * @access public
	 *
	 * @param int $post_id Post ID.
	 * @return WP_Post|bool Post object, false otherwise.
	 */
	public static function get_instance( $post_id ) {
		global $wpdb;

		$post_id = (int) $post_id;
		if ( ! $post_id )
			return false;

		$_post = wp_cache_get( $post_id, 'posts' );

		if ( ! $_post ) {
			$_post = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE ID = %d LIMIT 1", $post_id ) );

			if ( ! $_post )
				return false;

			$_post = sanitize_post( $_post, 'raw' );
			wp_cache_add( $_post->ID, $_post, 'posts' );
		} elseif ( empty( $_post->filter ) ) {
			$_post = sanitize_post( $_post, 'raw' );
		}

		return new WP_Post( $_post );
	}

	/**
	 * Constructor.
	 *
	 * @param WP_Post $post Post object.
	 */
	public function __construct( $post ) {
		foreach ( get_object_vars( $post ) as $key => $value )
			$this->$key = $value;
	}

	/**
	 * Isset-er.
	 *
	 * @param string $key Property to check if set.
	 * @return bool
	 */
	public function __isset( $key ) {
		if ( 'ancestors' == $key )
			return true;

		if ( 'page_template' == $key )
			return ( 'page' == $this->post_type );

		if ( 'post_category' == $key )
		   return true;

		if ( 'tags_input' == $key )
		   return true;

		return metadata_exists( 'post', $this->ID, $key );
	}

	/**
	 * Getter.
	 *
	 * @param string $key Key to get.
	 * @return array|mixed
	 */
	public function __get( $key ) {
		if ( 'page_template' == $key && $this->__isset( $key ) ) {
			return get_post_meta( $this->ID, '_wp_page_template', true );
		}

		if ( 'post_category' == $key ) {
			if ( is_object_in_taxonomy( $this->post_type, 'category' ) )
				$terms = get_the_terms( $this, 'category' );

			if ( empty( $terms ) )
				return array();

			return wp_list_pluck( $terms, 'term_id' );
		}

		if ( 'tags_input' == $key ) {
			if ( is_object_in_taxonomy( $this->post_type, 'post_tag' ) )
				$terms = get_the_terms( $this, 'post_tag' );

			if ( empty( $terms ) )
				return array();

			return wp_list_pluck( $terms, 'name' );
		}

		// Rest of the values need filtering.
		if ( 'ancestors' == $key )
			$value = get_post_ancestors( $this );
		else
			$value = get_post_meta( $this->ID, $key, true );

		if ( $this->filter )
			$value = sanitize_post_field( $key, $value, $this->ID, $this->filter );

		return $value;
	}

	/**
	 * {@Missing Summary}
	 *
	 * @param string $filter Filter.
	 * @return $this|array|bool|object|WP_Post
	 */
	public function filter( $filter ) {
		if ( $this->filter == $filter )
			return $this;

		if ( $filter == 'raw' )
			return self::get_instance( $this->ID );

		return sanitize_post( $this, $filter );
	}

	/**
	 * Convert object to array.
	 *
	 * @return array Object as array.
	 */
	public function to_array() {
		$post = get_object_vars( $this );

		foreach ( array( 'ancestors', 'page_template', 'post_category', 'tags_input' ) as $key ) {
			if ( $this->__isset( $key ) )
				$post[ $key ] = $this->__get( $key );
		}

		return $post;
	}
}

/**
 * Retrieve ancestors of a post.
 *
 * @since 2.5.0
 *
 * @param int|WP_Post $post Post ID or post object.
 * @return array Ancestor IDs or empty array if none are found.
 */
function get_post_ancestors( $post ) {
	$post = get_post( $post );

	if ( ! $post || empty( $post->post_parent ) || $post->post_parent == $post->ID )
		return array();

	$ancestors = array();

	$id = $ancestors[] = $post->post_parent;

	while ( $ancestor = get_post( $id ) ) {
		// Loop detection: If the ancestor has been seen before, break.
		if ( empty( $ancestor->post_parent ) || ( $ancestor->post_parent == $post->ID ) || in_array( $ancestor->post_parent, $ancestors ) )
			break;

		$id = $ancestors[] = $ancestor->post_parent;
	}

	return $ancestors;
}

/**
 * Retrieve data from a post field based on Post ID.
 *
 * Examples of the post field will be, 'post_type', 'post_status', 'post_content',
 * etc and based off of the post object property or key names.
 *
 * The context values are based off of the taxonomy filter functions and
 * supported values are found within those functions.
 *
 * @since 2.3.0
 *
 * @see sanitize_post_field()
 *
 * @param string      $field   Post field name.
 * @param int|WP_Post $post    Post ID or post object.
 * @param string      $context Optional. How to filter the field. Accepts 'raw', 'edit', 'db',
 *                             or 'display'. Default 'display'.
 * @return string The value of the post field on success, empty string on failure.
 */
function get_post_field( $field, $post, $context = 'display' ) {
	$post = get_post( $post );

	if ( !$post )
		return '';

	if ( !isset($post->$field) )
		return '';

	return sanitize_post_field($field, $post->$field, $post->ID, $context);
}

/**
 * Retrieve the mime type of an attachment based on the ID.
 *
 * This function can be used with any post type, but it makes more sense with
 * attachments.
 *
 * @since 2.0.0
 *
 * @param int|WP_Post $ID Optional. Post ID or post object. Default empty.
 * @return string|false The mime type on success, false on failure.
 */
function get_post_mime_type( $ID = '' ) {
	$post = get_post($ID);

	if ( is_object($post) )
		return $post->post_mime_type;

	return false;
}

/**
 * Retrieve the post status based on the Post ID.
 *
 * If the post ID is of an attachment, then the parent post status will be given
 * instead.
 *
 * @since 2.0.0
 *
 * @param int|WP_Post $ID Optional. Post ID or post object. Default empty.
 * @return string|false Post status on success, false on failure.
 */
function get_post_status( $ID = '' ) {
	$post = get_post($ID);

	if ( !is_object($post) )
		return false;

	if ( 'attachment' == $post->post_type ) {
		if ( 'private' == $post->post_status )
			return 'private';

		// Unattached attachments are assumed to be published.
		if ( ( 'inherit' == $post->post_status ) && ( 0 == $post->post_parent) )
			return 'publish';

		// Inherit status from the parent.
		if ( $post->post_parent && ( $post->ID != $post->post_parent ) ) {
			$parent_post_status = get_post_status( $post->post_parent );
			if ( 'trash' == $parent_post_status ) {
				return get_post_meta( $post->post_parent, '_wp_trash_meta_status', true );
			} else {
				return $parent_post_status;
			}
		}

	}

	return $post->post_status;
}

/**
 * Retrieve all of the WordPress supported post statuses.
 *
 * Posts have a limited set of valid status values, this provides the
 * post_status values and descriptions.
 *
 * @since 2.5.0
 *
 * @return array List of post statuses.
 */
function get_post_statuses() {
	$status = array(
		'draft'   => __( 'Draft' ),
		'pending' => __( 'Pending Review' ),
		'private' => __( 'Private' ),
		'publish' => __( 'Published' )
	);

	return $status;
}

/**
 * Retrieve all of the WordPress support page statuses.
 *
 * Pages have a limited set of valid status values, this provides the
 * post_status values and descriptions.
 *
 * @since 2.5.0
 *
 * @return array List of page statuses.
 */
function get_page_statuses() {
	$status = array(
		'draft'   => __( 'Draft' ),
		'private' => __( 'Private' ),
		'publish' => __( 'Published' )
	);

	return $status;
}

/**
 * Register a post status. Do not use before init.
 *
 * A simple function for creating or modifying a post status based on the
 * parameters given. The function will accept an array (second optional
 * parameter), along with a string for the post status name.
 *
 * Arguments prefixed with an _underscore shouldn't be used by plugins and themes.
 *
 * @since 3.0.0
 * @uses $wp_post_statuses Inserts new post status object into the list
 *
 * @param string $post_status Name of the post status.
 * @param array|string $args {
 *     Optional. Array or string of post status arguments.
 *
 *     @type bool|string $label                     A descriptive name for the post status marked
 *                                                  for translation. Defaults to value of $post_status.
 *     @type bool|array  $label_count               Descriptive text to use for nooped plurals.
 *                                                  Default array of $label, twice
 *     @type bool        $exclude_from_search       Whether to exclude posts with this post status
 *                                                  from search results. Default is value of $internal.
 *     @type bool        $_builtin                  Whether the status is built-in. Core-use only.
 *                                                  Default false.
 *     @type bool        $public                    Whether posts of this status should be shown
 *                                                  in the front end of the site. Default true.
 *     @type bool        $internal                  Whether the status is for internal use only.
 *                                                  Default false.
 *     @type bool        $protected                 Whether posts with this status should be protected.
 *                                                  Default false.
 *     @type bool        $private                   Whether posts with this status should be private.
 *                                                  Default false.
 *     @type bool        $publicly_queryable        Whether posts with this status should be publicly-
 *                                                  queryable. Default is value of $public.
 *     @type bool        $show_in_admin_all_list    Whether to include posts in the edit listing for
 *                                                  their post type. Default is value of $internal.
 *     @type bool        $show_in_admin_status_list Show in the list of statuses with post counts at
 *                                                  the top of the edit listings,
 *                                                  e.g. All (12) | Published (9) | My Custom Status (2)
 *                                                  Default is value of $internal.
 * }
 */
function register_post_status( $post_status, $args = array() ) {
	global $wp_post_statuses;

	if (!is_array($wp_post_statuses))
		$wp_post_statuses = array();

	// Args prefixed with an underscore are reserved for internal use.
	$defaults = array(
		'label' => false,
		'label_count' => false,
		'exclude_from_search' => null,
		'_builtin' => false,
		'public' => null,
		'internal' => null,
		'protected' => null,
		'private' => null,
		'publicly_queryable' => null,
		'show_in_admin_status_list' => null,
		'show_in_admin_all_list' => null,
	);
	$args = wp_parse_args($args, $defaults);
	$args = (object) $args;

	$post_status = sanitize_key($post_status);
	$args->name = $post_status;

	// Set various defaults.
	if ( null === $args->public && null === $args->internal && null === $args->protected && null === $args->private )
		$args->internal = true;

	if ( null === $args->public  )
		$args->public = false;

	if ( null === $args->private  )
		$args->private = false;

	if ( null === $args->protected  )
		$args->protected = false;

	if ( null === $args->internal  )
		$args->internal = false;

	if ( null === $args->publicly_queryable )
		$args->publicly_queryable = $args->public;

	if ( null === $args->exclude_from_search )
		$args->exclude_from_search = $args->internal;

	if ( null === $args->show_in_admin_all_list )
		$args->show_in_admin_all_list = !$args->internal;

	if ( null === $args->show_in_admin_status_list )
		$args->show_in_admin_status_list = !$args->internal;

	if ( false === $args->label )
		$args->label = $post_status;

	if ( false === $args->label_count )
		$args->label_count = array( $args->label, $args->label );

	$wp_post_statuses[$post_status] = $args;

	return $args;
}

/**
 * Retrieve a post status object by name.
 *
 * @since 3.0.0
 *
 * @global array $wp_post_statuses List of post statuses.
 *
 * @see register_post_status()
 *
 * @param string $post_status The name of a registered post status.
 * @return object A post status object.
 */
function get_post_status_object( $post_status ) {
	global $wp_post_statuses;

	if ( empty($wp_post_statuses[$post_status]) )
		return null;

	return $wp_post_statuses[$post_status];
}

/**
 * Get a list of post statuses.
 *
 * @since 3.0.0
 *
 * @global array $wp_post_statuses List of post statuses.
 *
 * @see register_post_status()
 *
 * @param array|string $args     Optional. Array or string of post status arguments to compare against
 *                               properties of the global `$wp_post_statuses objects`. Default empty array.
 * @param string       $output   Optional. The type of output to return, either 'names' or 'objects'. Default 'names'.
 * @param string       $operator Optional. The logical operation to perform. 'or' means only one element
 *                               from the array needs to match; 'and' means all elements must match.
 *                               Default 'and'.
 * @return array A list of post status names or objects.
 */
function get_post_stati( $args = array(), $output = 'names', $operator = 'and' ) {
	global $wp_post_statuses;

	$field = ('names' == $output) ? 'name' : false;

	return wp_filter_object_list($wp_post_statuses, $args, $operator, $field);
}

/**
 * Whether the post type is hierarchical.
 *
 * A false return value might also mean that the post type does not exist.
 *
 * @since 3.0.0
 *
 * @see get_post_type_object()
 *
 * @param string $post_type Post type name
 * @return bool Whether post type is hierarchical.
 */
function is_post_type_hierarchical( $post_type ) {
	if ( ! post_type_exists( $post_type ) )
		return false;

	$post_type = get_post_type_object( $post_type );
	return $post_type->hierarchical;
}

/**
 * Check if a post type is registered.
 *
 * @since 3.0.0
 *
 * @see get_post_type_object()
 *
 * @param string $post_type Post type name.
 * @return bool Whether post type is registered.
 */
function post_type_exists( $post_type ) {
	return (bool) get_post_type_object( $post_type );
}

/**
 * Retrieve the post type of the current post or of a given post.
 *
 * @since 2.1.0
 *
 * @param int|WP_Post $post Optional. Post ID or post object. Default is global $post.
 * @return string|false Post type on success, false on failure.
 */
function get_post_type( $post = null ) {
	if ( $post = get_post( $post ) )
		return $post->post_type;

	return false;
}

/**
 * Retrieve a post type object by name.
 *
 * @since 3.0.0
 *
 * @global array $wp_post_types List of post types.
 *
 * @see register_post_type()
 *
 * @param string $post_type The name of a registered post type.
 * @return object A post type object.
 */
function get_post_type_object( $post_type ) {
	global $wp_post_types;

	if ( empty($wp_post_types[$post_type]) )
		return null;

	return $wp_post_types[$post_type];
}

/**
 * Get a list of all registered post type objects.
 *
 * @since 2.9.0
 *
 * @global array $wp_post_types List of post types.
 *
 * @see register_post_type() for accepted arguments.
 *
 * @param array|string $args     Optional. An array of key => value arguments to match against
 *                               the post type objects. Default empty array.
 * @param string       $output   Optional. The type of output to return. Accepts post type 'names'
 *                               or 'objects'. Default 'names'.
 * @param string       $operator Optional. The logical operation to perform. 'or' means only one
 *                               element from the array needs to match; 'and' means all elements
 *                               must match. Accepts 'or' or 'and'. Default 'and'.
 * @return array A list of post type names or objects.
 */
function get_post_types( $args = array(), $output = 'names', $operator = 'and' ) {
	global $wp_post_types;

	$field = ('names' == $output) ? 'name' : false;

	return wp_filter_object_list($wp_post_types, $args, $operator, $field);
}

/**
 * Register a post type. Do not use before init.
 *
 * A function for creating or modifying a post type based on the
 * parameters given. The function will accept an array (second optional
 * parameter), along with a string for the post type name.
 *
 * @since 2.9.0
 *
 * @global array      $wp_post_types List of post types.
 * @global WP_Rewrite $wp_rewrite    Used for default feeds.
 * @global WP         $wp            Used to add query vars.
 *
 * @param string $post_type Post type key, must not exceed 20 characters.
 * @param array|string $args {
 *     Array or string of arguments for registering a post type.
 *
 *     @type string      $label                Name of the post type shown in the menu. Usually plural.
 *                                             Default is value of $labels['name'].
 *     @type array       $labels               An array of labels for this post type. If not set, post
 *                                             labels are inherited for non-hierarchical types and page
 *                                             labels for hierarchical ones. {@see get_post_type_labels()}.
 *     @type string      $description          A short descriptive summary of what the post type is.
 *                                             Default empty.
 *     @type bool        $public               Whether a post type is intended for use publicly either via
 *                                             the admin interface or by front-end users. While the default
 *                                             settings of $exclude_from_search, $publicly_queryable, $show_ui,
 *                                             and $show_in_nav_menus are inherited from public, each does not
 *                                             rely on this relationship and controls a very specific intention.
 *                                             Default false.
 *     @type bool        $hierarchical         Whether the post type is hierarchical (e.g. page). Default false.
 *     @type bool        $exclude_from_search  Whether to exclude posts with this post type from front end search
 *                                             results. Default is the opposite value of $public.
 *     @type bool        $publicly_queryable   Whether queries can be performed on the front end for the post type
 *                                             as part of {@see parse_request()}. Endpoints would include:
 *                                             * ?post_type={post_type_key}
 *                                             * ?{post_type_key}={single_post_slug}
 *                                             * ?{post_type_query_var}={single_post_slug}
 *                                             If not set, the default is inherited from $public.
 *     @type bool        $show_ui              Whether to generate a default UI for managing this post type in the
 *                                             admin. Default is value of $public.
 *     @type bool        $show_in_menu         Where to show the post type in the admin menu. To work, $show_ui
 *                                             must be true. If true, the post type is shown in its own top level
 *                                             menu. If false, no menu is shown. If a string of an existing top
 *                                             level menu (eg. 'tools.php' or 'edit.php?post_type=page'), the post
 *                                             type will be placed as a sub-menu of that.
 *                                             Default is value of $show_ui.
 *     @type bool        $show_in_nav_menus    Makes this post type available for selection in navigation menus.
 *                                             Default is value $public.
 *     @type bool        $show_in_admin_bar    Makes this post type available via the admin bar. Default is value
 *                                             of $show_in_menu.
 *     @type int         $menu_position        The position in the menu order the post type should appear. To work,
 *                                             $show_in_menu must be true. Default null (at the bottom).
 *     @type string      $menu_icon            The url to the icon to be used for this menu. Pass a base64-encoded
 *                                             SVG using a data URI, which will be colored to match the color scheme
 *                                             -- this should begin with 'data:image/svg+xml;base64,'. Pass the name
 *                                             of a Dashicons helper class to use a font icon, e.g.
 *                                             'dashicons-chart-pie'. Pass 'none' to leave div.wp-menu-image empty
 *                                             so an icon can be added via CSS. Defaults to use the posts icon.
 *     @type string      $capability_type      The string to use to build the read, edit, and delete capabilities.
 *                                             May be passed as an array to allow for alternative plurals when using
 *                                             this argument as a base to construct the capabilities, e.g.
 *                                             array('story', 'stories'). Default 'post'.
 *     @type array       $capabilities         Array of capabilities for this post type. $capability_type is used
 *                                             as a base to construct capabilities by default.
 *                                             {@see get_post_type_capabilities()}.
 *     @type bool        $map_meta_cap         Whether to use the internal default meta capability handling.
 *                                             Default false.
 *     @type array       $supports             An alias for calling {@see add_post_type_support()} directly.
 *                                             Defaults to array containing 'title' & 'editor'.
 *     @type callback    $register_meta_box_cb Provide a callback function that sets up the meta boxes for the
 *                                             edit form. Do remove_meta_box() and add_meta_box() calls in the
 *                                             callback. Default null.
 *     @type array       $taxonomies           An array of taxonomy identifiers that will be registered for the
 *                                             post type. Taxonomies can be registered later with
 *                                             {@see register_taxonomy()} or {@see register_taxonomy_for_object_type()}.
 *                                             Default empty array.
 *     @type bool|string $has_archive          Whether there should be post type archives, or if a string, the
 *                                             archive slug to use. Will generate the proper rewrite rules if
 *                                             $rewrite is enabled. Default false.
 *     @type bool|array  $rewrite              {
 *         Triggers the handling of rewrites for this post type. To prevent rewrite, set to false.
 *         Defaults to true, using $post_type as slug. To specify rewrite rules, an array can be
 *         passed with any of these keys:
 *
 *         @type string $slug       Customize the permastruct slug. Defaults to $post_type key.
 *         @type bool   $with_front Whether the permastruct should be prepended with WP_Rewrite::$front.
 *                                  Default true.
 *         @type bool   $feeds      Whether the feed permastruct should be built for this post type.
 *                                  Default is value of $has_archive.
 *         @type bool   $pages      Whether the permastruct should provide for pagination. Default true.
 *         @type const  $ep_mask    Endpoint mask to assign. If not specified and permalink_epmask is set,
 *                                  inherits from $permalink_epmask. If not specified and permalink_epmask
 *                                  is not set, defaults to EP_PERMALINK.
 *     }
 *     @type string|bool $query_var            Sets the query_var key for this post type. Defaults to $post_type
 *                                             key. If false, a post type cannot be loaded at
 *                                             ?{query_var}={post_slug}. If specified as a string, the query
 *                                             ?{query_var_string}={post_slug} will be valid.
 *     @type bool        $can_export           Whether to allow this post type to be exported. Default true.
 *     @type bool        $delete_with_user     Whether to delete posts of this type when deleting a user. If true,
 *                                             posts of this type belonging to the user will be moved to trash
 *                                             when then user is deleted. If false, posts of this type belonging
 *                                             to the user will *not* be trashed or deleted. If not set (the default),
 *                                             posts are trashed if post_type_supports('author'). Otherwise posts
 *                                             are not trashed or deleted. Default null.
 *     @type bool        $_builtin             FOR INTERNAL USE ONLY! True if this post type is a native or
 *                                             "built-in" post_type. Default false.
 *     @type string      $_edit_link           FOR INTERNAL USE ONLY! URL segment to use for edit link of
 *                                             this post type. Default 'post.php?post=%d'.
 * }
 * @return object|WP_Error The registered post type object, or an error object.
 */
function register_post_type( $post_type, $args = array() ) {
	global $wp_post_types, $wp_rewrite, $wp;

	if ( ! is_array( $wp_post_types ) )
		$wp_post_types = array();

	// Args prefixed with an underscore are reserved for internal use.
	$defaults = array(
		'labels'               => array(),
		'description'          => '',
		'public'               => false,
		'hierarchical'         => false,
		'exclude_from_search'  => null,
		'publicly_queryable'   => null,
		'show_ui'              => null,
		'show_in_menu'         => null,
		'show_in_nav_menus'    => null,
		'show_in_admin_bar'    => null,
		'menu_position'        => null,
		'menu_icon'            => null,
		'capability_type'      => 'post',
		'capabilities'         => array(),
		'map_meta_cap'         => null,
		'supports'             => array(),
		'register_meta_box_cb' => null,
		'taxonomies'           => array(),
		'has_archive'          => false,
		'rewrite'              => true,
		'query_var'            => true,
		'can_export'           => true,
		'delete_with_user'     => null,
		'_builtin'             => false,
		'_edit_link'           => 'post.php?post=%d',
	);
	$args = wp_parse_args( $args, $defaults );
	$args = (object) $args;

	$post_type = sanitize_key( $post_type );
	$args->name = $post_type;

	if ( empty( $post_type ) || strlen( $post_type ) > 20 ) {
		_doing_it_wrong( __FUNCTION__, __( 'Post type names must be between 1 and 20 characters in length.' ), '4.2' );
		return new WP_Error( 'post_type_length_invalid', __( 'Post type names must be between 1 and 20 characters in length.' ) );
	}

	// If not set, default to the setting for public.
	if ( null === $args->publicly_queryable )
		$args->publicly_queryable = $args->public;

	// If not set, default to the setting for public.
	if ( null === $args->show_ui )
		$args->show_ui = $args->public;

	// If not set, default to the setting for show_ui.
	if ( null === $args->show_in_menu || ! $args->show_ui )
		$args->show_in_menu = $args->show_ui;

	// If not set, default to the whether the full UI is shown.
	if ( null === $args->show_in_admin_bar )
		$args->show_in_admin_bar = (bool) $args->show_in_menu;

	// If not set, default to the setting for public.
	if ( null === $args->show_in_nav_menus )
		$args->show_in_nav_menus = $args->public;

	// If not set, default to true if not public, false if public.
	if ( null === $args->exclude_from_search )
		$args->exclude_from_search = !$args->public;

	// Back compat with quirky handling in version 3.0. #14122.
	if ( empty( $args->capabilities ) && null === $args->map_meta_cap && in_array( $args->capability_type, array( 'post', 'page' ) ) )
		$args->map_meta_cap = true;

	// If not set, default to false.
	if ( null === $args->map_meta_cap )
		$args->map_meta_cap = false;

	$args->cap = get_post_type_capabilities( $args );
	unset( $args->capabilities );

	if ( is_array( $args->capability_type ) )
		$args->capability_type = $args->capability_type[0];

	if ( ! empty( $args->supports ) ) {
		add_post_type_support( $post_type, $args->supports );
		unset( $args->supports );
	} elseif ( false !== $args->supports ) {
		// Add default features
		add_post_type_support( $post_type, array( 'title', 'editor' ) );
	}

	if ( false !== $args->query_var && ! empty( $wp ) ) {
		if ( true === $args->query_var )
			$args->query_var = $post_type;
		else
			$args->query_var = sanitize_title_with_dashes( $args->query_var );
		$wp->add_query_var( $args->query_var );
	}

	if ( false !== $args->rewrite && ( is_admin() || '' != get_option( 'permalink_structure' ) ) ) {
		if ( ! is_array( $args->rewrite ) )
			$args->rewrite = array();
		if ( empty( $args->rewrite['slug'] ) )
			$args->rewrite['slug'] = $post_type;
		if ( ! isset( $args->rewrite['with_front'] ) )
			$args->rewrite['with_front'] = true;
		if ( ! isset( $args->rewrite['pages'] ) )
			$args->rewrite['pages'] = true;
		if ( ! isset( $args->rewrite['feeds'] ) || ! $args->has_archive )
			$args->rewrite['feeds'] = (bool) $args->has_archive;
		if ( ! isset( $args->rewrite['ep_mask'] ) ) {
			if ( isset( $args->permalink_epmask ) )
				$args->rewrite['ep_mask'] = $args->permalink_epmask;
			else
				$args->rewrite['ep_mask'] = EP_PERMALINK;
		}

		if ( $args->hierarchical )
			add_rewrite_tag( "%$post_type%", '(.+?)', $args->query_var ? "{$args->query_var}=" : "post_type=$post_type&pagename=" );
		else
			add_rewrite_tag( "%$post_type%", '([^/]+)', $args->query_var ? "{$args->query_var}=" : "post_type=$post_type&name=" );

		if ( $args->has_archive ) {
			$archive_slug = $args->has_archive === true ? $args->rewrite['slug'] : $args->has_archive;
			if ( $args->rewrite['with_front'] )
				$archive_slug = substr( $wp_rewrite->front, 1 ) . $archive_slug;
			else
				$archive_slug = $wp_rewrite->root . $archive_slug;

			add_rewrite_rule( "{$archive_slug}/?$", "index.php?post_type=$post_type", 'top' );
			if ( $args->rewrite['feeds'] && $wp_rewrite->feeds ) {
				$feeds = '(' . trim( implode( '|', $wp_rewrite->feeds ) ) . ')';
				add_rewrite_rule( "{$archive_slug}/feed/$feeds/?$", "index.php?post_type=$post_type" . '&feed=$matches[1]', 'top' );
				add_rewrite_rule( "{$archive_slug}/$feeds/?$", "index.php?post_type=$post_type" . '&feed=$matches[1]', 'top' );
			}
			if ( $args->rewrite['pages'] )
				add_rewrite_rule( "{$archive_slug}/{$wp_rewrite->pagination_base}/([0-9]{1,})/?$", "index.php?post_type=$post_type" . '&paged=$matches[1]', 'top' );
		}

		$permastruct_args = $args->rewrite;
		$permastruct_args['feed'] = $permastruct_args['feeds'];
		add_permastruct( $post_type, "{$args->rewrite['slug']}/%$post_type%", $permastruct_args );
	}

	// Register the post type meta box if a custom callback was specified.
	if ( $args->register_meta_box_cb )
		add_action( 'add_meta_boxes_' . $post_type, $args->register_meta_box_cb, 10, 1 );

	$args->labels = get_post_type_labels( $args );
	$args->label = $args->labels->name;

	$wp_post_types[ $post_type ] = $args;

	add_action( 'future_' . $post_type, '_future_post_hook', 5, 2 );

	foreach ( $args->taxonomies as $taxonomy ) {
		register_taxonomy_for_object_type( $taxonomy, $post_type );
	}

	/**
	 * Fires after a post type is registered.
	 *
	 * @since 3.3.0
	 *
	 * @param string $post_type Post type.
	 * @param object $args      Arguments used to register the post type.
	 */
	do_action( 'registered_post_type', $post_type, $args );

	return $args;
}

/**
 * Build an object with all post type capabilities out of a post type object
 *
 * Post type capabilities use the 'capability_type' argument as a base, if the
 * capability is not set in the 'capabilities' argument array or if the
 * 'capabilities' argument is not supplied.
 *
 * The capability_type argument can optionally be registered as an array, with
 * the first value being singular and the second plural, e.g. array('story, 'stories')
 * Otherwise, an 's' will be added to the value for the plural form. After
 * registration, capability_type will always be a string of the singular value.
 *
 * By default, seven keys are accepted as part of the capabilities array:
 *
 * - edit_post, read_post, and delete_post are meta capabilities, which are then
 *   generally mapped to corresponding primitive capabilities depending on the
 *   context, which would be the post being edited/read/deleted and the user or
 *   role being checked. Thus these capabilities would generally not be granted
 *   directly to users or roles.
 *
 * - edit_posts - Controls whether objects of this post type can be edited.
 * - edit_others_posts - Controls whether objects of this type owned by other users
 *   can be edited. If the post type does not support an author, then this will
 *   behave like edit_posts.
 * - publish_posts - Controls publishing objects of this post type.
 * - read_private_posts - Controls whether private objects can be read.
 *
 * These four primitive capabilities are checked in core in various locations.
 * There are also seven other primitive capabilities which are not referenced
 * directly in core, except in map_meta_cap(), which takes the three aforementioned
 * meta capabilities and translates them into one or more primitive capabilities
 * that must then be checked against the user or role, depending on the context.
 *
 * - read - Controls whether objects of this post type can be read.
 * - delete_posts - Controls whether objects of this post type can be deleted.
 * - delete_private_posts - Controls whether private objects can be deleted.
 * - delete_published_posts - Controls whether published objects can be deleted.
 * - delete_others_posts - Controls whether objects owned by other users can be
 *   can be deleted. If the post type does not support an author, then this will
 *   behave like delete_posts.
 * - edit_private_posts - Controls whether private objects can be edited.
 * - edit_published_posts - Controls whether published objects can be edited.
 *
 * These additional capabilities are only used in map_meta_cap(). Thus, they are
 * only assigned by default if the post type is registered with the 'map_meta_cap'
 * argument set to true (default is false).
 *
 * @since 3.0.0
 *
 * @see register_post_type()
 * @see map_meta_cap()
 *
 * @param object $args Post type registration arguments.
 * @return object object with all the capabilities as member variables.
 */
function get_post_type_capabilities( $args ) {
	if ( ! is_array( $args->capability_type ) )
		$args->capability_type = array( $args->capability_type, $args->capability_type . 's' );

	// Singular base for meta capabilities, plural base for primitive capabilities.
	list( $singular_base, $plural_base ) = $args->capability_type;

	$default_capabilities = array(
		// Meta capabilities
		'edit_post'          => 'edit_'         . $singular_base,
		'read_post'          => 'read_'         . $singular_base,
		'delete_post'        => 'delete_'       . $singular_base,
		// Primitive capabilities used outside of map_meta_cap():
		'edit_posts'         => 'edit_'         . $plural_base,
		'edit_others_posts'  => 'edit_others_'  . $plural_base,
		'publish_posts'      => 'publish_'      . $plural_base,
		'read_private_posts' => 'read_private_' . $plural_base,
	);

	// Primitive capabilities used within map_meta_cap():
	if ( $args->map_meta_cap ) {
		$default_capabilities_for_mapping = array(
			'read'                   => 'read',
			'delete_posts'           => 'delete_'           . $plural_base,
			'delete_private_posts'   => 'delete_private_'   . $plural_base,
			'delete_published_posts' => 'delete_published_' . $plural_base,
			'delete_others_posts'    => 'delete_others_'    . $plural_base,
			'edit_private_posts'     => 'edit_private_'     . $plural_base,
			'edit_published_posts'   => 'edit_published_'   . $plural_base,
		);
		$default_capabilities = array_merge( $default_capabilities, $default_capabilities_for_mapping );
	}

	$capabilities = array_merge( $default_capabilities, $args->capabilities );

	// Post creation capability simply maps to edit_posts by default:
	if ( ! isset( $capabilities['create_posts'] ) )
		$capabilities['create_posts'] = $capabilities['edit_posts'];

	// Remember meta capabilities for future reference.
	if ( $args->map_meta_cap )
		_post_type_meta_capabilities( $capabilities );

	return (object) $capabilities;
}

/**
 * Store or return a list of post type meta caps for map_meta_cap().
 *
 * @since 3.1.0
 * @access private
 *
 * @param null|array $capabilities Post type meta capabilities.
 */
function _post_type_meta_capabilities( $capabilities = null ) {
	static $meta_caps = array();
	if ( null === $capabilities )
		return $meta_caps;
	foreach ( $capabilities as $core => $custom ) {
		if ( in_array( $core, array( 'read_post', 'delete_post', 'edit_post' ) ) )
			$meta_caps[ $custom ] = $core;
	}
}

/**
 * Build an object with all post type labels out of a post type object
 *
 * Accepted keys of the label array in the post type object:
 *
 * - name - general name for the post type, usually plural. The same and overridden
 *          by $post_type_object->label. Default is Posts/Pages
 * - singular_name - name for one object of this post type. Default is Post/Page
 * - add_new - Default is Add New for both hierarchical and non-hierarchical types.
 *             When internationalizing this string, please use a gettext context
 *             {@link https://codex.wordpress.org/I18n_for_WordPress_Developers#Disambiguation_by_context}
 *             matching your post type. Example: `_x( 'Add New', 'product' );`.
 * - add_new_item - Default is Add New Post/Add New Page.
 * - edit_item - Default is Edit Post/Edit Page.
 * - new_item - Default is New Post/New Page.
 * - view_item - Default is View Post/View Page.
 * - search_items - Default is Search Posts/Search Pages.
 * - not_found - Default is No posts found/No pages found.
 * - not_found_in_trash - Default is No posts found in Trash/No pages found in Trash.
 * - parent_item_colon - This string isn't used on non-hierarchical types. In hierarchical
 *                       ones the default is 'Parent Page:'.
 * - all_items - String for the submenu. Default is All Posts/All Pages.
 * - menu_name - Default is the same as `name`.
 *
 * Above, the first default value is for non-hierarchical post types (like posts)
 * and the second one is for hierarchical post types (like pages).
 *
 * @since 3.0.0
 * @access private
 *
 * @param object $post_type_object Post type object.
 * @return object object with all the labels as member variables.
 */
function get_post_type_labels( $post_type_object ) {
	$nohier_vs_hier_defaults = array(
		'name' => array( _x('Posts', 'post type general name'), _x('Pages', 'post type general name') ),
		'singular_name' => array( _x('Post', 'post type singular name'), _x('Page', 'post type singular name') ),
		'add_new' => array( _x('Add New', 'post'), _x('Add New', 'page') ),
		'add_new_item' => array( __('Add New Post'), __('Add New Page') ),
		'edit_item' => array( __('Edit Post'), __('Edit Page') ),
		'new_item' => array( __('New Post'), __('New Page') ),
		'view_item' => array( __('View Post'), __('View Page') ),
		'search_items' => array( __('Search Posts'), __('Search Pages') ),
		'not_found' => array( __('No posts found.'), __('No pages found.') ),
		'not_found_in_trash' => array( __('No posts found in Trash.'), __('No pages found in Trash.') ),
		'parent_item_colon' => array( null, __('Parent Page:') ),
		'all_items' => array( __( 'All Posts' ), __( 'All Pages' ) )
	);
	$nohier_vs_hier_defaults['menu_name'] = $nohier_vs_hier_defaults['name'];

	$labels = _get_custom_object_labels( $post_type_object, $nohier_vs_hier_defaults );

	$post_type = $post_type_object->name;

	/**
	 * Filter the labels of a specific post type.
	 *
	 * The dynamic portion of the hook name, `$post_type`, refers to
	 * the post type slug.
	 *
	 * @since 3.5.0
	 *
	 * @see get_post_type_labels() for the full list of labels.
	 *
	 * @param array $labels Array of labels for the given post type.
	 */
	return apply_filters( "post_type_labels_{$post_type}", $labels );
}

/**
 * Build an object with custom-something object (post type, taxonomy) labels
 * out of a custom-something object
 *
 * @since 3.0.0
 * @access private
 *
 * @param object $object                  A custom-something object.
 * @param array  $nohier_vs_hier_defaults Hierarchical vs non-hierarchical default labels.
 */
function _get_custom_object_labels( $object, $nohier_vs_hier_defaults ) {
	$object->labels = (array) $object->labels;

	if ( isset( $object->label ) && empty( $object->labels['name'] ) )
		$object->labels['name'] = $object->label;

	if ( !isset( $object->labels['singular_name'] ) && isset( $object->labels['name'] ) )
		$object->labels['singular_name'] = $object->labels['name'];

	if ( ! isset( $object->labels['name_admin_bar'] ) )
		$object->labels['name_admin_bar'] = isset( $object->labels['singular_name'] ) ? $object->labels['singular_name'] : $object->name;

	if ( !isset( $object->labels['menu_name'] ) && isset( $object->labels['name'] ) )
		$object->labels['menu_name'] = $object->labels['name'];

	if ( !isset( $object->labels['all_items'] ) && isset( $object->labels['menu_name'] ) )
		$object->labels['all_items'] = $object->labels['menu_name'];

	$defaults = array();
	foreach ( $nohier_vs_hier_defaults as $key => $value ) {
		$defaults[$key] = $object->hierarchical ? $value[1] : $value[0];
	}
	$labels = array_merge( $defaults, $object->labels );
	return (object)$labels;
}

/**
 * Add submenus for post types.
 *
 * @access private
 * @since 3.1.0
 */
function _add_post_type_submenus() {
	foreach ( get_post_types( array( 'show_ui' => true ) ) as $ptype ) {
		$ptype_obj = get_post_type_object( $ptype );
		// Sub-menus only.
		if ( ! $ptype_obj->show_in_menu || $ptype_obj->show_in_menu === true )
			continue;
		add_submenu_page( $ptype_obj->show_in_menu, $ptype_obj->labels->name, $ptype_obj->labels->all_items, $ptype_obj->cap->edit_posts, "edit.php?post_type=$ptype" );
	}
}

/**
 * Register support of certain features for a post type.
 *
 * All core features are directly associated with a functional area of the edit
 * screen, such as the editor or a meta box. Features include: 'title', 'editor',
 * 'comments', 'revisions', 'trackbacks', 'author', 'excerpt', 'page-attributes',
 * 'thumbnail', 'custom-fields', and 'post-formats'.
 *
 * Additionally, the 'revisions' feature dictates whether the post type will
 * store revisions, and the 'comments' feature dictates whether the comments
 * count will show on the edit screen.
 *
 * @since 3.0.0
 *
 * @param string       $post_type The post type for which to add the feature.
 * @param string|array $feature   The feature being added, accepts an array of
 *                                feature strings or a single string.
 */
function add_post_type_support( $post_type, $feature ) {
	global $_wp_post_type_features;

	$features = (array) $feature;
	foreach ($features as $feature) {
		if ( func_num_args() == 2 )
			$_wp_post_type_features[$post_type][$feature] = true;
		else
			$_wp_post_type_features[$post_type][$feature] = array_slice( func_get_args(), 2 );
	}
}

/**
 * Remove support for a feature from a post type.
 *
 * @since 3.0.0
 *
 * @param string $post_type The post type for which to remove the feature.
 * @param string $feature   The feature being removed.
 */
function remove_post_type_support( $post_type, $feature ) {
	global $_wp_post_type_features;

	if ( isset( $_wp_post_type_features[$post_type][$feature] ) )
		unset( $_wp_post_type_features[$post_type][$feature] );
}

/**
 * Get all the post type features
 *
 * @since 3.4.0
 *
 * @param string $post_type The post type.
 * @return array Post type supports list.
 */
function get_all_post_type_supports( $post_type ) {
	global $_wp_post_type_features;

	if ( isset( $_wp_post_type_features[$post_type] ) )
		return $_wp_post_type_features[$post_type];

	return array();
}

/**
 * Check a post type's support for a given feature.
 *
 * @since 3.0.0
 *
 * @param string $post_type The post type being checked.
 * @param string $feature the feature being checked.
 * @return bool Whether the post type supports the given feature.
 */
function post_type_supports( $post_type, $feature ) {
	global $_wp_post_type_features;

	return ( isset( $_wp_post_type_features[$post_type][$feature] ) );
}

/**
 * Update the post type for the post ID.
 *
 * The page or post cache will be cleaned for the post ID.
 *
 * @since 2.5.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int    $post_id   Optional. Post ID to change post type. Default 0.
 * @param string $post_type Optional. Post type. Accepts 'post' or 'page' to
 *                          name a few. Default 'post'.
 * @return int Amount of rows changed. Should be 1 for success and 0 for failure.
 */
function set_post_type( $post_id = 0, $post_type = 'post' ) {
	global $wpdb;

	$post_type = sanitize_post_field('post_type', $post_type, $post_id, 'db');
	$return = $wpdb->update( $wpdb->posts, array('post_type' => $post_type), array('ID' => $post_id) );

	clean_post_cache( $post_id );

	return $return;
}

/**
 * Retrieve list of latest posts or posts matching criteria.
 *
 * The defaults are as follows:
 *
 * @since 1.2.0
 *
 * @see WP_Query::parse_query()
 *
 * @param array $args {
 *     Optional. Arguments to retrieve posts. {@see WP_Query::parse_query()} for more
 *     available arguments.
 *
 *     @type int        $numberposts      Total number of posts to retrieve. Is an alias of $posts_per_page
 *                                        in WP_Query. Accepts 1+ and -1 for all. Default 5.
 *     @type int        $offset           The number of posts to offset before retrieval. Default 0.
 *     @type int|string $category         Category ID or comma-separated list of IDs (this or any children).
 *                                        Is an alias of $cat in WP_Query. Default 0.
 *     @type string     $orderby          Which field to order posts by. Accepts post fields. Default 'date'.
 *     @type array      $include          An array of post IDs to retrieve, sticky posts will be included.
 *                                        Is an alias of $post__in in WP_Query. Default empty array.
 *     @type array      $exclude          An array of post IDs not to retrieve. Default empty array.
 *     @type string     $meta_key         Custom field key. Default empty.
 *     @type mixed      $meta_value       Custom field value. Default empty string.
 *     @type string     $post_type        Post type. Default 'post'.
 *     @type bool       $suppress_filters Whether to suppress filters. Default true.
 * }
 * @return array List of posts.
 */
function get_posts( $args = null ) {
	$defaults = array(
		'numberposts' => 5, 'offset' => 0,
		'category' => 0, 'orderby' => 'date',
		'order' => 'DESC', 'include' => array(),
		'exclude' => array(), 'meta_key' => '',
		'meta_value' =>'', 'post_type' => 'post',
		'suppress_filters' => true
	);

	$r = wp_parse_args( $args, $defaults );
	if ( empty( $r['post_status'] ) )
		$r['post_status'] = ( 'attachment' == $r['post_type'] ) ? 'inherit' : 'publish';
	if ( ! empty($r['numberposts']) && empty($r['posts_per_page']) )
		$r['posts_per_page'] = $r['numberposts'];
	if ( ! empty($r['category']) )
		$r['cat'] = $r['category'];
	if ( ! empty($r['include']) ) {
		$incposts = wp_parse_id_list( $r['include'] );
		$r['posts_per_page'] = count($incposts);  // only the number of posts included
		$r['post__in'] = $incposts;
	} elseif ( ! empty($r['exclude']) )
		$r['post__not_in'] = wp_parse_id_list( $r['exclude'] );

	$r['ignore_sticky_posts'] = true;
	$r['no_found_rows'] = true;

	$get_posts = new WP_Query;
	return $get_posts->query($r);

}

//
// Post meta functions
//

/**
 * Add meta data field to a post.
 *
 * Post meta data is called "Custom Fields" on the Administration Screen.
 *
 * @since 1.5.0
 *
 * @param int    $post_id    Post ID.
 * @param string $meta_key   Metadata name.
 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
 * @param bool   $unique     Optional. Whether the same key should not be added.
 *                           Default false.
 * @return int|bool Meta ID on success, false on failure.
 */
function add_post_meta( $post_id, $meta_key, $meta_value, $unique = false ) {
	// Make sure meta is added to the post, not a revision.
	if ( $the_post = wp_is_post_revision($post_id) )
		$post_id = $the_post;

	return add_metadata('post', $post_id, $meta_key, $meta_value, $unique);
}

/**
 * Remove metadata matching criteria from a post.
 *
 * You can match based on the key, or key and value. Removing based on key and
 * value, will keep from removing duplicate metadata with the same key. It also
 * allows removing all metadata matching key, if needed.
 *
 * @since 1.5.0
 *
 * @param int    $post_id    Post ID.
 * @param string $meta_key   Metadata name.
 * @param mixed  $meta_value Optional. Metadata value. Must be serializable if
 *                           non-scalar. Default empty.
 * @return bool True on success, false on failure.
 */
function delete_post_meta( $post_id, $meta_key, $meta_value = '' ) {
	// Make sure meta is added to the post, not a revision.
	if ( $the_post = wp_is_post_revision($post_id) )
		$post_id = $the_post;

	return delete_metadata('post', $post_id, $meta_key, $meta_value);
}

/**
 * Retrieve post meta field for a post.
 *
 * @since 1.5.0
 *
 * @param int    $post_id Post ID.
 * @param string $key     Optional. The meta key to retrieve. By default, returns
 *                        data for all keys. Default empty.
 * @param bool   $single  Optional. Whether to return a single value. Default false.
 * @return mixed Will be an array if $single is false. Will be value of meta data
 *               field if $single is true.
 */
function get_post_meta( $post_id, $key = '', $single = false ) {
	return get_metadata('post', $post_id, $key, $single);
}

/**
 * Update post meta field based on post ID.
 *
 * Use the $prev_value parameter to differentiate between meta fields with the
 * same key and post ID.
 *
 * If the meta field for the post does not exist, it will be added.
 *
 * @since 1.5.0
 *
 * @param int    $post_id    Post ID.
 * @param string $meta_key   Metadata key.
 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
 * @param mixed  $prev_value Optional. Previous value to check before removing.
 *                           Default empty.
 * @return int|bool Meta ID if the key didn't exist, true on successful update,
 *                  false on failure.
 */
function update_post_meta( $post_id, $meta_key, $meta_value, $prev_value = '' ) {
	// Make sure meta is added to the post, not a revision.
	if ( $the_post = wp_is_post_revision($post_id) )
		$post_id = $the_post;

	return update_metadata('post', $post_id, $meta_key, $meta_value, $prev_value);
}

/**
 * Delete everything from post meta matching meta key.
 *
 * @since 2.3.0
 *
 * @param string $post_meta_key Key to search for when deleting.
 * @return bool Whether the post meta key was deleted from the database.
 */
function delete_post_meta_by_key( $post_meta_key ) {
	return delete_metadata( 'post', null, $post_meta_key, '', true );
}

/**
 * Retrieve post meta fields, based on post ID.
 *
 * The post meta fields are retrieved from the cache where possible,
 * so the function is optimized to be called more than once.
 *
 * @since 1.2.0
 *
 * @param int $post_id Optional. Post ID. Default is ID of the global $post.
 * @return array Post meta for the given post.
 */
function get_post_custom( $post_id = 0 ) {
	$post_id = absint( $post_id );
	if ( ! $post_id )
		$post_id = get_the_ID();

	return get_post_meta( $post_id );
}

/**
 * Retrieve meta field names for a post.
 *
 * If there are no meta fields, then nothing (null) will be returned.
 *
 * @since 1.2.0
 *
 * @param int $post_id Optional. Post ID. Default is ID of the global $post.
 * @return array|null Either array of the keys, or null if keys could not be
 *                    retrieved.
 */
function get_post_custom_keys( $post_id = 0 ) {
	$custom = get_post_custom( $post_id );

	if ( !is_array($custom) )
		return;

	if ( $keys = array_keys($custom) )
		return $keys;
}

/**
 * Retrieve values for a custom post field.
 *
 * The parameters must not be considered optional. All of the post meta fields
 * will be retrieved and only the meta field key values returned.
 *
 * @since 1.2.0
 *
 * @param string $key     Optional. Meta field key. Default empty.
 * @param int    $post_id Optional. Post ID. Default is ID of the global $post.
 * @return array Meta field values.
 */
function get_post_custom_values( $key = '', $post_id = 0 ) {
	if ( !$key )
		return null;

	$custom = get_post_custom($post_id);

	return isset($custom[$key]) ? $custom[$key] : null;
}

/**
 * Check if post is sticky.
 *
 * Sticky posts should remain at the top of The Loop. If the post ID is not
 * given, then The Loop ID for the current post will be used.
 *
 * @since 2.7.0
 *
 * @param int $post_id Optional. Post ID. Default is ID of the global $post.
 * @return bool Whether post is sticky.
 */
function is_sticky( $post_id = 0 ) {
	$post_id = absint( $post_id );

	if ( ! $post_id )
		$post_id = get_the_ID();

	$stickies = get_option( 'sticky_posts' );

	if ( ! is_array( $stickies ) )
		return false;

	if ( in_array( $post_id, $stickies ) )
		return true;

	return false;
}

/**
 * Sanitize every post field.
 *
 * If the context is 'raw', then the post object or array will get minimal
 * sanitization of the integer fields.
 *
 * @since 2.3.0
 *
 * @see sanitize_post_field()
 *
 * @param object|WP_Post|array $post    The Post Object or Array
 * @param string               $context Optional. How to sanitize post fields.
 *                                      Accepts 'raw', 'edit', 'db', or 'display'.
 *                                      Default 'display'.
 * @return object|WP_Post|array The now sanitized Post Object or Array (will be the
 *                              same type as $post).
 */
function sanitize_post( $post, $context = 'display' ) {
	if ( is_object($post) ) {
		// Check if post already filtered for this context.
		if ( isset($post->filter) && $context == $post->filter )
			return $post;
		if ( !isset($post->ID) )
			$post->ID = 0;
		foreach ( array_keys(get_object_vars($post)) as $field )
			$post->$field = sanitize_post_field($field, $post->$field, $post->ID, $context);
		$post->filter = $context;
	} else {
		// Check if post already filtered for this context.
		if ( isset($post['filter']) && $context == $post['filter'] )
			return $post;
		if ( !isset($post['ID']) )
			$post['ID'] = 0;
		foreach ( array_keys($post) as $field )
			$post[$field] = sanitize_post_field($field, $post[$field], $post['ID'], $context);
		$post['filter'] = $context;
	}
	return $post;
}

/**
 * Sanitize post field based on context.
 *
 * Possible context values are:  'raw', 'edit', 'db', 'display', 'attribute' and
 * 'js'. The 'display' context is used by default. 'attribute' and 'js' contexts
 * are treated like 'display' when calling filters.
 *
 * @since 2.3.0
 *
 * @param string $field   The Post Object field name.
 * @param mixed  $value   The Post Object value.
 * @param int    $post_id Post ID.
 * @param string $context How to sanitize post fields. Looks for 'raw', 'edit',
 *                        'db', 'display', 'attribute' and 'js'.
 * @return mixed Sanitized value.
 */
function sanitize_post_field($field, $value, $post_id, $context) {
	$int_fields = array('ID', 'post_parent', 'menu_order');
	if ( in_array($field, $int_fields) )
		$value = (int) $value;

	// Fields which contain arrays of integers.
	$array_int_fields = array( 'ancestors' );
	if ( in_array($field, $array_int_fields) ) {
		$value = array_map( 'absint', $value);
		return $value;
	}

	if ( 'raw' == $context )
		return $value;

	$prefixed = false;
	if ( false !== strpos($field, 'post_') ) {
		$prefixed = true;
		$field_no_prefix = str_replace('post_', '', $field);
	}

	if ( 'edit' == $context ) {
		$format_to_edit = array('post_content', 'post_excerpt', 'post_title', 'post_password');

		if ( $prefixed ) {

			/**
			 * Filter the value of a specific post field to edit.
			 *
			 * The dynamic portion of the hook name, `$field`, refers to the post
			 * field name.
			 *
			 * @since 2.3.0
			 *
			 * @param mixed $value   Value of the post field.
			 * @param int   $post_id Post ID.
			 */
			$value = apply_filters( "edit_{$field}", $value, $post_id );

			/**
			 * Filter the value of a specific post field to edit.
			 *
			 * The dynamic portion of the hook name, `$field_no_prefix`, refers to
			 * the post field name.
			 *
			 * @since 2.3.0
			 *
			 * @param mixed $value   Value of the post field.
			 * @param int   $post_id Post ID.
			 */
			$value = apply_filters( "{$field_no_prefix}_edit_pre", $value, $post_id );
		} else {
			$value = apply_filters( "edit_post_{$field}", $value, $post_id );
		}

		if ( in_array($field, $format_to_edit) ) {
			if ( 'post_content' == $field )
				$value = format_to_edit($value, user_can_richedit());
			else
				$value = format_to_edit($value);
		} else {
			$value = esc_attr($value);
		}
	} elseif ( 'db' == $context ) {
		if ( $prefixed ) {

			/**
			 * Filter the value of a specific post field before saving.
			 *
			 * The dynamic portion of the hook name, `$field`, refers to the post
			 * field name.
			 *
			 * @since 2.3.0
			 *
			 * @param mixed $value Value of the post field.
			 */
			$value = apply_filters( "pre_{$field}", $value );

			/**
			 * Filter the value of a specific field before saving.
			 *
			 * The dynamic portion of the hook name, `$field_no_prefix`, refers
			 * to the post field name.
			 *
			 * @since 2.3.0
			 *
			 * @param mixed $value Value of the post field.
			 */
			$value = apply_filters( "{$field_no_prefix}_save_pre", $value );
		} else {
			$value = apply_filters( "pre_post_{$field}", $value );

			/**
			 * Filter the value of a specific post field before saving.
			 *
			 * The dynamic portion of the hook name, `$field`, refers to the post
			 * field name.
			 *
			 * @since 2.3.0
			 *
			 * @param mixed $value Value of the post field.
			 */
			$value = apply_filters( "{$field}_pre", $value );
		}
	} else {

		// Use display filters by default.
		if ( $prefixed ) {

			/**
			 * Filter the value of a specific post field for display.
			 *
			 * The dynamic portion of the hook name, `$field`, refers to the post
			 * field name.
			 *
			 * @since 2.3.0
			 *
			 * @param mixed  $value   Value of the prefixed post field.
			 * @param int    $post_id Post ID.
			 * @param string $context Context for how to sanitize the field. Possible
			 *                        values include 'raw', 'edit', 'db', 'display',
			 *                        'attribute' and 'js'.
			 */
			$value = apply_filters( $field, $value, $post_id, $context );
		} else {
			$value = apply_filters( "post_{$field}", $value, $post_id, $context );
		}
	}

	if ( 'attribute' == $context )
		$value = esc_attr($value);
	elseif ( 'js' == $context )
		$value = esc_js($value);

	return $value;
}

/**
 * Make a post sticky.
 *
 * Sticky posts should be displayed at the top of the front page.
 *
 * @since 2.7.0
 *
 * @param int $post_id Post ID.
 */
function stick_post( $post_id ) {
	$stickies = get_option('sticky_posts');

	if ( !is_array($stickies) )
		$stickies = array($post_id);

	if ( ! in_array($post_id, $stickies) )
		$stickies[] = $post_id;

	update_option('sticky_posts', $stickies);
}

/**
 * Un-stick a post.
 *
 * Sticky posts should be displayed at the top of the front page.
 *
 * @since 2.7.0
 *
 * @param int $post_id Post ID.
 */
function unstick_post( $post_id ) {
	$stickies = get_option('sticky_posts');

	if ( !is_array($stickies) )
		return;

	if ( ! in_array($post_id, $stickies) )
		return;

	$offset = array_search($post_id, $stickies);
	if ( false === $offset )
		return;

	array_splice($stickies, $offset, 1);

	update_option('sticky_posts', $stickies);
}

/**
 * Return the cache key for wp_count_posts() based on the passed arguments.
 *
 * @since 3.9.0
 *
 * @param string $type Optional. Post type to retrieve count Default 'post'.
 * @param string $perm Optional. 'readable' or empty. Default empty.
 * @return string The cache key.
 */
function _count_posts_cache_key( $type = 'post', $perm = '' ) {
	$cache_key = 'posts-' . $type;
	if ( 'readable' == $perm && is_user_logged_in() ) {
		$post_type_object = get_post_type_object( $type );
		if ( $post_type_object && ! current_user_can( $post_type_object->cap->read_private_posts ) ) {
			$cache_key .= '_' . $perm . '_' . get_current_user_id();
		}
	}
	return $cache_key;
}

/**
 * Count number of posts of a post type and if user has permissions to view.
 *
 * This function provides an efficient method of finding the amount of post's
 * type a blog has. Another method is to count the amount of items in
 * get_posts(), but that method has a lot of overhead with doing so. Therefore,
 * when developing for 2.5+, use this function instead.
 *
 * The $perm parameter checks for 'readable' value and if the user can read
 * private posts, it will display that for the user that is signed in.
 *
 * @since 2.5.0
 *
 * @param string $type Optional. Post type to retrieve count. Default 'post'.
 * @param string $perm Optional. 'readable' or empty. Default empty.
 * @return object Number of posts for each status.
 */
function wp_count_posts( $type = 'post', $perm = '' ) {
	global $wpdb;

	if ( ! post_type_exists( $type ) )
		return new stdClass;

	$cache_key = _count_posts_cache_key( $type, $perm );

	$counts = wp_cache_get( $cache_key, 'counts' );
	if ( false !== $counts ) {
		/** This filter is documented in wp-includes/post.php */
		return apply_filters( 'wp_count_posts', $counts, $type, $perm );
	}

	$query = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s";
	if ( 'readable' == $perm && is_user_logged_in() ) {
		$post_type_object = get_post_type_object($type);
		if ( ! current_user_can( $post_type_object->cap->read_private_posts ) ) {
			$query .= $wpdb->prepare( " AND (post_status != 'private' OR ( post_author = %d AND post_status = 'private' ))",
				get_current_user_id()
			);
		}
	}
	$query .= ' GROUP BY post_status';

	$results = (array) $wpdb->get_results( $wpdb->prepare( $query, $type ), ARRAY_A );
	$counts = array_fill_keys( get_post_stati(), 0 );

	foreach ( $results as $row ) {
		$counts[ $row['post_status'] ] = $row['num_posts'];
	}

	$counts = (object) $counts;
	wp_cache_set( $cache_key, $counts, 'counts' );

	/**
	 * Modify returned post counts by status for the current post type.
	 *
	 * @since 3.7.0
	 *
	 * @param object $counts An object containing the current post_type's post
	 *                       counts by status.
	 * @param string $type   Post type.
	 * @param string $perm   The permission to determine if the posts are 'readable'
	 *                       by the current user.
	 */
	return apply_filters( 'wp_count_posts', $counts, $type, $perm );
}

/**
 * Count number of attachments for the mime type(s).
 *
 * If you set the optional mime_type parameter, then an array will still be
 * returned, but will only have the item you are looking for. It does not give
 * you the number of attachments that are children of a post. You can get that
 * by counting the number of children that post has.
 *
 * @since 2.5.0
 *
 * @param string|array $mime_type Optional. Array or comma-separated list of
 *                                MIME patterns. Default empty.
 * @return object An object containing the attachment counts by mime type.
 */
function wp_count_attachments( $mime_type = '' ) {
	global $wpdb;

	$and = wp_post_mime_type_where( $mime_type );
	$count = $wpdb->get_results( "SELECT post_mime_type, COUNT( * ) AS num_posts FROM $wpdb->posts WHERE post_type = 'attachment' AND post_status != 'trash' $and GROUP BY post_mime_type", ARRAY_A );

	$counts = array();
	foreach( (array) $count as $row ) {
		$counts[ $row['post_mime_type'] ] = $row['num_posts'];
	}
	$counts['trash'] = $wpdb->get_var( "SELECT COUNT( * ) FROM $wpdb->posts WHERE post_type = 'attachment' AND post_status = 'trash' $and");

	/**
	 * Modify returned attachment counts by mime type.
	 *
	 * @since 3.7.0
	 *
	 * @param object $counts    An object containing the attachment counts by
	 *                          mime type.
	 * @param string $mime_type The mime type pattern used to filter the attachments
	 *                          counted.
	 */
	return apply_filters( 'wp_count_attachments', (object) $counts, $mime_type );
}

/**
 * Get default post mime types.
 *
 * @since 2.9.0
 *
 * @return array List of post mime types.
 */
function get_post_mime_types() {
	$post_mime_types = array(	//	array( adj, noun )
		'image' => array(__('Images'), __('Manage Images'), _n_noop('Image <span class="count">(%s)</span>', 'Images <span class="count">(%s)</span>')),
		'audio' => array(__('Audio'), __('Manage Audio'), _n_noop('Audio <span class="count">(%s)</span>', 'Audio <span class="count">(%s)</span>')),
		'video' => array(__('Video'), __('Manage Video'), _n_noop('Video <span class="count">(%s)</span>', 'Video <span class="count">(%s)</span>')),
	);

	/**
	 * Filter the default list of post mime types.
	 *
	 * @since 2.5.0
	 *
	 * @param array $post_mime_types Default list of post mime types.
	 */
	return apply_filters( 'post_mime_types', $post_mime_types );
}

/**
 * Check a MIME-Type against a list.
 *
 * If the wildcard_mime_types parameter is a string, it must be comma separated
 * list. If the real_mime_types is a string, it is also comma separated to
 * create the list.
 *
 * @since 2.5.0
 *
 * @param string|array $wildcard_mime_types Mime types, e.g. audio/mpeg or image (same as image/*)
 *                                          or flash (same as *flash*).
 * @param string|array $real_mime_types     Real post mime type values.
 * @return array array(wildcard=>array(real types)).
 */
function wp_match_mime_types( $wildcard_mime_types, $real_mime_types ) {
	$matches = array();
	if ( is_string( $wildcard_mime_types ) ) {
		$wildcard_mime_types = array_map( 'trim', explode( ',', $wildcard_mime_types ) );
	}
	if ( is_string( $real_mime_types ) ) {
		$real_mime_types = array_map( 'trim', explode( ',', $real_mime_types ) );
	}

	$patternses = array();
	$wild = '[-._a-z0-9]*';

	foreach ( (array) $wildcard_mime_types as $type ) {
		$mimes = array_map( 'trim', explode( ',', $type ) );
		foreach ( $mimes as $mime ) {
			$regex = str_replace( '__wildcard__', $wild, preg_quote( str_replace( '*', '__wildcard__', $mime ) ) );
			$patternses[][$type] = "^$regex$";
			if ( false === strpos( $mime, '/' ) ) {
				$patternses[][$type] = "^$regex/";
				$patternses[][$type] = $regex;
			}
		}
	}
	asort( $patternses );

	foreach ( $patternses as $patterns ) {
		foreach ( $patterns as $type => $pattern ) {
			foreach ( (array) $real_mime_types as $real ) {
				if ( preg_match( "#$pattern#", $real ) && ( empty( $matches[$type] ) || false === array_search( $real, $matches[$type] ) ) ) {
					$matches[$type][] = $real;
				}
			}
		}
	}
	return $matches;
}

/**
 * Convert MIME types into SQL.
 *
 * @since 2.5.0
 *
 * @param string|array $post_mime_types List of mime types or comma separated string
 *                                      of mime types.
 * @param string       $table_alias     Optional. Specify a table alias, if needed.
 *                                      Default empty.
 * @return string The SQL AND clause for mime searching.
 */
function wp_post_mime_type_where( $post_mime_types, $table_alias = '' ) {
	$where = '';
	$wildcards = array('', '%', '%/%');
	if ( is_string($post_mime_types) )
		$post_mime_types = array_map('trim', explode(',', $post_mime_types));

	$wheres = array();

	foreach ( (array) $post_mime_types as $mime_type ) {
		$mime_type = preg_replace('/\s/', '', $mime_type);
		$slashpos = strpos($mime_type, '/');
		if ( false !== $slashpos ) {
			$mime_group = preg_replace('/[^-*.a-zA-Z0-9]/', '', substr($mime_type, 0, $slashpos));
			$mime_subgroup = preg_replace('/[^-*.+a-zA-Z0-9]/', '', substr($mime_type, $slashpos + 1));
			if ( empty($mime_subgroup) )
				$mime_subgroup = '*';
			else
				$mime_subgroup = str_replace('/', '', $mime_subgroup);
			$mime_pattern = "$mime_group/$mime_subgroup";
		} else {
			$mime_pattern = preg_replace('/[^-*.a-zA-Z0-9]/', '', $mime_type);
			if ( false === strpos($mime_pattern, '*') )
				$mime_pattern .= '/*';
		}

		$mime_pattern = preg_replace('/\*+/', '%', $mime_pattern);

		if ( in_array( $mime_type, $wildcards ) )
			return '';

		if ( false !== strpos($mime_pattern, '%') )
			$wheres[] = empty($table_alias) ? "post_mime_type LIKE '$mime_pattern'" : "$table_alias.post_mime_type LIKE '$mime_pattern'";
		else
			$wheres[] = empty($table_alias) ? "post_mime_type = '$mime_pattern'" : "$table_alias.post_mime_type = '$mime_pattern'";
	}
	if ( !empty($wheres) )
		$where = ' AND (' . join(' OR ', $wheres) . ') ';
	return $where;
}

/**
 * Trash or delete a post or page.
 *
 * When the post and page is permanently deleted, everything that is tied to
 * it is deleted also. This includes comments, post meta fields, and terms
 * associated with the post.
 *
 * The post or page is moved to trash instead of permanently deleted unless
 * trash is disabled, item is already in the trash, or $force_delete is true.
 *
 * @since 1.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 * @see wp_delete_attachment()
 * @see wp_trash_post()
 *
 * @param int  $postid       Optional. Post ID. Default 0.
 * @param bool $force_delete Optional. Whether to bypass trash and force deletion.
 *                           Default false.
 * @return array|bool|WP_Post False on failure.
 */
function wp_delete_post( $postid = 0, $force_delete = false ) {
	global $wpdb;

	if ( !$post = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE ID = %d", $postid)) )
		return $post;

	if ( !$force_delete && ( $post->post_type == 'post' || $post->post_type == 'page') && get_post_status( $postid ) != 'trash' && EMPTY_TRASH_DAYS )
			return wp_trash_post($postid);

	if ( $post->post_type == 'attachment' )
		return wp_delete_attachment( $postid, $force_delete );

	/**
	 * Fires before a post is deleted, at the start of wp_delete_post().
	 *
	 * @since 3.2.0
	 *
	 * @see wp_delete_post()
	 *
	 * @param int $postid Post ID.
	 */
	do_action( 'before_delete_post', $postid );

	delete_post_meta($postid,'_wp_trash_meta_status');
	delete_post_meta($postid,'_wp_trash_meta_time');

	wp_delete_object_term_relationships($postid, get_object_taxonomies($post->post_type));

	$parent_data = array( 'post_parent' => $post->post_parent );
	$parent_where = array( 'post_parent' => $postid );

	if ( is_post_type_hierarchical( $post->post_type ) ) {
		// Point children of this page to its parent, also clean the cache of affected children.
		$children_query = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_parent = %d AND post_type = %s", $postid, $post->post_type );
		$children = $wpdb->get_results( $children_query );

		$wpdb->update( $wpdb->posts, $parent_data, $parent_where + array( 'post_type' => $post->post_type ) );
	}

	// Do raw query. wp_get_post_revisions() is filtered.
	$revision_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_parent = %d AND post_type = 'revision'", $postid ) );
	// Use wp_delete_post (via wp_delete_post_revision) again. Ensures any meta/misplaced data gets cleaned up.
	foreach ( $revision_ids as $revision_id )
		wp_delete_post_revision( $revision_id );

	// Point all attachments to this post up one level.
	$wpdb->update( $wpdb->posts, $parent_data, $parent_where + array( 'post_type' => 'attachment' ) );

	$comment_ids = $wpdb->get_col( $wpdb->prepare( "SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = %d", $postid ));
	foreach ( $comment_ids as $comment_id )
		wp_delete_comment( $comment_id, true );

	$post_meta_ids = $wpdb->get_col( $wpdb->prepare( "SELECT meta_id FROM $wpdb->postmeta WHERE post_id = %d ", $postid ));
	foreach ( $post_meta_ids as $mid )
		delete_metadata_by_mid( 'post', $mid );

	/**
	 * Fires immediately before a post is deleted from the database.
	 *
	 * @since 1.2.0
	 *
	 * @param int $postid Post ID.
	 */
	do_action( 'delete_post', $postid );
	$result = $wpdb->delete( $wpdb->posts, array( 'ID' => $postid ) );
	if ( ! $result ) {
		return false;
	}

	/**
	 * Fires immediately after a post is deleted from the database.
	 *
	 * @since 2.2.0
	 *
	 * @param int $postid Post ID.
	 */
	do_action( 'deleted_post', $postid );

	clean_post_cache( $post );

	if ( is_post_type_hierarchical( $post->post_type ) && $children ) {
		foreach ( $children as $child )
			clean_post_cache( $child );
	}

	wp_clear_scheduled_hook('publish_future_post', array( $postid ) );

	/**
	 * Fires after a post is deleted, at the conclusion of wp_delete_post().
	 *
	 * @since 3.2.0
	 *
	 * @see wp_delete_post()
	 *
	 * @param int $postid Post ID.
	 */
	do_action( 'after_delete_post', $postid );

	return $post;
}

/**
 * Reset the page_on_front, show_on_front, and page_for_post settings when
 * a linked page is deleted or trashed.
 *
 * Also ensures the post is no longer sticky.
 *
 * @since 3.7.0
 * @access private
 *
 * @param int $post_id Post ID.
 */
function _reset_front_page_settings_for_post( $post_id ) {
	$post = get_post( $post_id );
	if ( 'page' == $post->post_type ) {
	 	/*
	 	 * If the page is defined in option page_on_front or post_for_posts,
	 	 * adjust the corresponding options.
	 	 */
		if ( get_option( 'page_on_front' ) == $post->ID ) {
			update_option( 'show_on_front', 'posts' );
			update_option( 'page_on_front', 0 );
		}
		if ( get_option( 'page_for_posts' ) == $post->ID ) {
			delete_option( 'page_for_posts', 0 );
		}
	}
	unstick_post( $post->ID );
}

/**
 * Move a post or page to the Trash
 *
 * If trash is disabled, the post or page is permanently deleted.
 *
 * @since 2.9.0
 *
 * @see wp_delete_post()
 *
 * @param int $post_id Optional. Post ID. Default is ID of the global $post
 *                     if EMPTY_TRASH_DAYS equals true.
 * @return bool|array Post data array, otherwise false.
 */
function wp_trash_post( $post_id = 0 ) {
	if ( !EMPTY_TRASH_DAYS )
		return wp_delete_post($post_id, true);

	if ( !$post = get_post($post_id, ARRAY_A) )
		return $post;

	if ( $post['post_status'] == 'trash' )
		return false;

	/**
	 * Fires before a post is sent to the trash.
	 *
	 * @since 3.3.0
	 *
	 * @param int $post_id Post ID.
	 */
	do_action( 'wp_trash_post', $post_id );

	add_post_meta($post_id,'_wp_trash_meta_status', $post['post_status']);
	add_post_meta($post_id,'_wp_trash_meta_time', time());

	$post['post_status'] = 'trash';
	wp_insert_post($post);

	wp_trash_post_comments($post_id);

	/**
	 * Fires after a post is sent to the trash.
	 *
	 * @since 2.9.0
	 *
	 * @param int $post_id Post ID.
	 */
	do_action( 'trashed_post', $post_id );

	return $post;
}

/**
 * Restore a post or page from the Trash.
 *
 * @since 2.9.0
 *
 * @param int $post_id Optional. Post ID. Default is ID of the global $post.
 * @return WP_Post|bool WP_Post object. False on failure.
 */
function wp_untrash_post( $post_id = 0 ) {
	if ( !$post = get_post($post_id, ARRAY_A) )
		return $post;

	if ( $post['post_status'] != 'trash' )
		return false;

	/**
	 * Fires before a post is restored from the trash.
	 *
	 * @since 2.9.0
	 *
	 * @param int $post_id Post ID.
	 */
	do_action( 'untrash_post', $post_id );

	$post_status = get_post_meta($post_id, '_wp_trash_meta_status', true);

	$post['post_status'] = $post_status;

	delete_post_meta($post_id, '_wp_trash_meta_status');
	delete_post_meta($post_id, '_wp_trash_meta_time');

	wp_insert_post($post);

	wp_untrash_post_comments($post_id);

	/**
	 * Fires after a post is restored from the trash.
	 *
	 * @since 2.9.0
	 *
	 * @param int $post_id Post ID.
	 */
	do_action( 'untrashed_post', $post_id );

	return $post;
}

/**
 * Moves comments for a post to the trash.
 *
 * @since 2.9.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int|WP_Post $post Optional. Post ID or post object. Defaults to global $post.
 * @return mixed False on failure.
 */
function wp_trash_post_comments( $post = null ) {
	global $wpdb;

	$post = get_post($post);
	if ( empty($post) )
		return;

	$post_id = $post->ID;

	/**
	 * Fires before comments are sent to the trash.
	 *
	 * @since 2.9.0
	 *
	 * @param int $post_id Post ID.
	 */
	do_action( 'trash_post_comments', $post_id );

	$comments = $wpdb->get_results( $wpdb->prepare("SELECT comment_ID, comment_approved FROM $wpdb->comments WHERE comment_post_ID = %d", $post_id) );
	if ( empty($comments) )
		return;

	// Cache current status for each comment.
	$statuses = array();
	foreach ( $comments as $comment )
		$statuses[$comment->comment_ID] = $comment->comment_approved;
	add_post_meta($post_id, '_wp_trash_meta_comments_status', $statuses);

	// Set status for all comments to post-trashed.
	$result = $wpdb->update($wpdb->comments, array('comment_approved' => 'post-trashed'), array('comment_post_ID' => $post_id));

	clean_comment_cache( array_keys($statuses) );

	/**
	 * Fires after comments are sent to the trash.
	 *
	 * @since 2.9.0
	 *
	 * @param int   $post_id  Post ID.
	 * @param array $statuses Array of comment statuses.
	 */
	do_action( 'trashed_post_comments', $post_id, $statuses );

	return $result;
}

/**
 * Restore comments for a post from the trash.
 *
 * @since 2.9.0
 *
 * @param int|WP_Post $post Optional. Post ID or post object. Defaults to global $post.
 * @return null|bool Null on failure.
 */
function wp_untrash_post_comments( $post = null ) {
	global $wpdb;

	$post = get_post($post);
	if ( empty($post) )
		return;

	$post_id = $post->ID;

	$statuses = get_post_meta($post_id, '_wp_trash_meta_comments_status', true);

	if ( empty($statuses) )
		return true;

	/**
	 * Fires before comments are restored for a post from the trash.
	 *
	 * @since 2.9.0
	 *
	 * @param int $post_id Post ID.
	 */
	do_action( 'untrash_post_comments', $post_id );

	// Restore each comment to its original status.
	$group_by_status = array();
	foreach ( $statuses as $comment_id => $comment_status )
		$group_by_status[$comment_status][] = $comment_id;

	foreach ( $group_by_status as $status => $comments ) {
		// Sanity check. This shouldn't happen.
		if ( 'post-trashed' == $status ) {
			$status = '0';
		}
		$comments_in = implode( ', ', array_map( 'intval', $comments ) );
		$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->comments SET comment_approved = %s WHERE comment_ID IN ($comments_in)", $status ) );
	}

	clean_comment_cache( array_keys($statuses) );

	delete_post_meta($post_id, '_wp_trash_meta_comments_status');

	/**
	 * Fires after comments are restored for a post from the trash.
	 *
	 * @since 2.9.0
	 *
	 * @param int $post_id Post ID.
	 */
	do_action( 'untrashed_post_comments', $post_id );
}

/**
 * Retrieve the list of categories for a post.
 *
 * Compatibility layer for themes and plugins. Also an easy layer of abstraction
 * away from the complexity of the taxonomy layer.
 *
 * @since 2.1.0
 *
 * @see wp_get_object_terms()
 *
 * @param int   $post_id Optional. The Post ID. Does not default to the ID of the
 *                       global $post. Default 0.
 * @param array $args    Optional. Category arguments. Default empty.
 * @return array List of categories.
 */
function wp_get_post_categories( $post_id = 0, $args = array() ) {
	$post_id = (int) $post_id;

	$defaults = array('fields' => 'ids');
	$args = wp_parse_args( $args, $defaults );

	$cats = wp_get_object_terms($post_id, 'category', $args);
	return $cats;
}

/**
 * Retrieve the tags for a post.
 *
 * There is only one default for this function, called 'fields' and by default
 * is set to 'all'. There are other defaults that can be overridden in
 * {@link wp_get_object_terms()}.
 *
 * @since 2.3.0
 *
 * @param int   $post_id Optional. The Post ID. Does not default to the ID of the
 *                       global $post. Defualt 0.
 * @param array $args Optional. Overwrite the defaults
 * @return array List of post tags.
 */
function wp_get_post_tags( $post_id = 0, $args = array() ) {
	return wp_get_post_terms( $post_id, 'post_tag', $args);
}

/**
 * Retrieve the terms for a post.
 *
 * There is only one default for this function, called 'fields' and by default
 * is set to 'all'. There are other defaults that can be overridden in
 * {@link wp_get_object_terms()}.
 *
 * @since 2.8.0
 *
 * @param int    $post_id  Optional. The Post ID. Does not default to the ID of the
 *                         global $post. Default 0.
 * @param string $taxonomy Optional. The taxonomy for which to retrieve terms. Default 'post_tag'.
 * @param array  $args     Optional. {@link wp_get_object_terms()} arguments. Default empty array.
 * @return array List of post tags.
 */
function wp_get_post_terms( $post_id = 0, $taxonomy = 'post_tag', $args = array() ) {
	$post_id = (int) $post_id;

	$defaults = array('fields' => 'all');
	$args = wp_parse_args( $args, $defaults );

	$tags = wp_get_object_terms($post_id, $taxonomy, $args);

	return $tags;
}

/**
 * Retrieve a number of recent posts.
 *
 * @since 1.0.0
 *
 * @see get_posts()
 *
 * @param array  $args       Optional. Arguments to retrieve posts. Default empty array.
 * @param string $output     Optional. Type of output. Accepts ARRAY_A or ''. Default ARRAY_A.
 * @return array|bool Associative array if $output equals ARRAY_A, array or false if no results.
 */
function wp_get_recent_posts( $args = array(), $output = ARRAY_A ) {

	if ( is_numeric( $args ) ) {
		_deprecated_argument( __FUNCTION__, '3.1', __( 'Passing an integer number of posts is deprecated. Pass an array of arguments instead.' ) );
		$args = array( 'numberposts' => absint( $args ) );
	}

	// Set default arguments.
	$defaults = array(
		'numberposts' => 10, 'offset' => 0,
		'category' => 0, 'orderby' => 'post_date',
		'order' => 'DESC', 'include' => '',
		'exclude' => '', 'meta_key' => '',
		'meta_value' =>'', 'post_type' => 'post', 'post_status' => 'draft, publish, future, pending, private',
		'suppress_filters' => true
	);

	$r = wp_parse_args( $args, $defaults );

	$results = get_posts( $r );

	// Backward compatibility. Prior to 3.1 expected posts to be returned in array.
	if ( ARRAY_A == $output ){
		foreach( $results as $key => $result ) {
			$results[$key] = get_object_vars( $result );
		}
		return $results ? $results : array();
	}

	return $results ? $results : false;

}

/**
 * Insert or update a post.
 *
 * If the $postarr parameter has 'ID' set to a value, then post will be updated.
 *
 * You can set the post date manually, by setting the values for 'post_date'
 * and 'post_date_gmt' keys. You can close the comments or open the comments by
 * setting the value for 'comment_status' key.
 *
 * @since 1.0.0
 * @since 4.2.0 Support was added for encoding emoji in the post title, content, and excerpt.
 *
 * @see sanitize_post()
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param array $postarr {
 *     An array of elements that make up a post to update or insert.
 *
 *     @type int    $ID                    The post ID. If equal to something other than 0,
 *                                         the post with that ID will be updated. Default 0.
 *     @type int    $post_author           The ID of the user who added the post. Default is
 *                                         the current user ID.
 *     @type string $post_date             The date of the post. Default is the current time.
 *     @type string $post_date_gmt         The date of the post in the GMT timezone. Default is
 *                                         the value of `$post_date`.
 *     @type mixed  $post_content          The post content. Default empty.
 *     @type string $post_content_filtered The filtered post content. Default empty.
 *     @type string $post_title            The post title. Default empty.
 *     @type string $post_excerpt          The post excerpt. Default empty.
 *     @type string $post_status           The post status. Default 'draft'.
 *     @type string $post_type             The post type. Default 'post'.
 *     @type string $comment_status        Whether the post can accept comments. Accepts 'open' or 'closed'.
 *                                         Default is the value of 'default_comment_status' option.
 *     @type string $ping_status           Whether the post can accept pings. Accepts 'open' or 'closed'.
 *                                         Default is the value of 'default_ping_status' option.
 *     @type string $post_password         The password to access the post. Default empty.
 *     @type string $post_name             The post name. Default is the sanitized post title.
 *     @type string $to_ping               Space or carriage return-separated list of URLs to ping.
 *                                         Default empty.
 *     @type string $pinged                Space or carriage return-separated list of URLs that have
 *                                         been pinged. Default empty.
 *     @type string $post_modified         The date when the post was last modified. Default is
 *                                         the current time.
 *     @type string $post_modified_gmt     The date when the post was last modified in the GMT
 *                                         timezone. Default is the current time.
 *     @type int    $post_parent           Set this for the post it belongs to, if any. Default 0.
 *     @type int    $menu_order            The order the post should be displayed in. Default 0.
 *     @type string $post_mime_type        The mime type of the post. Default empty.
 *     @type string $guid                  Global Unique ID for referencing the post. Default empty.
 * }
 * @param bool  $wp_error Optional. Whether to allow return of WP_Error on failure. Default false.
 * @return int|WP_Error The post ID on success. The value 0 or WP_Error on failure.
 */
function wp_insert_post( $postarr, $wp_error = false ) {
	global $wpdb;

	$user_id = get_current_user_id();

	$defaults = array('post_status' => 'draft', 'post_type' => 'post', 'post_author' => $user_id,
		'ping_status' => get_option('default_ping_status'), 'post_parent' => 0,
		'menu_order' => 0, 'to_ping' =>  '', 'pinged' => '', 'post_password' => '',
		'guid' => '', 'post_content_filtered' => '', 'post_excerpt' => '', 'import_id' => 0,
		'post_content' => '', 'post_title' => '', 'context' => '');

	$postarr = wp_parse_args($postarr, $defaults);

	unset( $postarr[ 'filter' ] );

	$postarr = sanitize_post($postarr, 'db');

	// Are we updating or creating?
	$post_ID = 0;
	$update = false;
	$guid = $postarr['guid'];

	if ( ! empty( $postarr['ID'] ) ) {
		$update = true;

		// Get the post ID and GUID.
		$post_ID = $postarr['ID'];
		$post_before = get_post( $post_ID );
		if ( is_null( $post_before ) ) {
			if ( $wp_error ) {
				return new WP_Error( 'invalid_post', __( 'Invalid post ID.' ) );
			}
			return 0;
		}

		$guid = get_post_field( 'guid', $post_ID );
		$previous_status = get_post_field('post_status', $post_ID );
	} else {
		$previous_status = 'new';
	}

	$post_type = empty( $postarr['post_type'] ) ? 'post' : $postarr['post_type'];

	$post_title = $postarr['post_title'];
	$post_content = $postarr['post_content'];
	$post_excerpt = $postarr['post_excerpt'];
	if ( isset( $postarr['post_name'] ) ) {
		$post_name = $postarr['post_name'];
	}

	$maybe_empty = 'attachment' !== $post_type
		&& ! $post_content && ! $post_title && ! $post_excerpt
		&& post_type_supports( $post_type, 'editor' )
		&& post_type_supports( $post_type, 'title' )
		&& post_type_supports( $post_type, 'excerpt' );

	/**
	 * Filter whether the post should be considered "empty".
	 *
	 * The post is considered "empty" if both:
	 * 1. The post type supports the title, editor, and excerpt fields
	 * 2. The title, editor, and excerpt fields are all empty
	 *
	 * Returning a truthy value to the filter will effectively short-circuit
	 * the new post being inserted, returning 0. If $wp_error is true, a WP_Error
	 * will be returned instead.
	 *
	 * @since 3.3.0
	 *
	 * @param bool  $maybe_empty Whether the post should be considered "empty".
	 * @param array $postarr     Array of post data.
	 */
	if ( apply_filters( 'wp_insert_post_empty_content', $maybe_empty, $postarr ) ) {
		if ( $wp_error ) {
			return new WP_Error( 'empty_content', __( 'Content, title, and excerpt are empty.' ) );
		} else {
			return 0;
		}
	}

	$post_status = empty( $postarr['post_status'] ) ? 'draft' : $postarr['post_status'];
	if ( 'attachment' === $post_type && ! in_array( $post_status, array( 'inherit', 'private', 'trash' ) ) ) {
		$post_status = 'inherit';
	}

	if ( ! empty( $postarr['post_category'] ) ) {
		// Filter out empty terms.
		$post_category = array_filter( $postarr['post_category'] );
	}

	// Make sure we set a valid category.
	if ( empty( $post_category ) || 0 == count( $post_category ) || ! is_array( $post_category ) ) {
		// 'post' requires at least one category.
		if ( 'post' == $post_type && 'auto-draft' != $post_status ) {
			$post_category = array( get_option('default_category') );
		} else {
			$post_category = array();
		}
	}

	// Don't allow contributors to set the post slug for pending review posts.
	if ( 'pending' == $post_status && !current_user_can( 'publish_posts' ) ) {
		$post_name = '';
	}

	/*
	 * Create a valid post name. Drafts and pending posts are allowed to have
	 * an empty post name.
	 */
	if ( empty($post_name) ) {
		if ( !in_array( $post_status, array( 'draft', 'pending', 'auto-draft' ) ) ) {
			$post_name = sanitize_title($post_title);
		} else {
			$post_name = '';
		}
	} else {
		// On updates, we need to check to see if it's using the old, fixed sanitization context.
		$check_name = sanitize_title( $post_name, '', 'old-save' );
		if ( $update && strtolower( urlencode( $post_name ) ) == $check_name && get_post_field( 'post_name', $post_ID ) == $check_name ) {
			$post_name = $check_name;
		} else { // new post, or slug has changed.
			$post_name = sanitize_title($post_name);
		}
	}

	/*
	 * If the post date is empty (due to having been new or a draft) and status
	 * is not 'draft' or 'pending', set date to now.
	 */
	if ( empty( $postarr['post_date'] ) || '0000-00-00 00:00:00' == $postarr['post_date'] ) {
		$post_date = current_time( 'mysql' );
	} else {
		$post_date = $postarr['post_date'];
	}

	// Validate the date.
	$mm = substr( $post_date, 5, 2 );
	$jj = substr( $post_date, 8, 2 );
	$aa = substr( $post_date, 0, 4 );
	$valid_date = wp_checkdate( $mm, $jj, $aa, $post_date );
	if ( ! $valid_date ) {
		if ( $wp_error ) {
			return new WP_Error( 'invalid_date', __( 'Whoops, the provided date is invalid.' ) );
		} else {
			return 0;
		}
	}

	if ( empty( $postarr['post_date_gmt'] ) || '0000-00-00 00:00:00' == $postarr['post_date_gmt'] ) {
		if ( ! in_array( $post_status, array( 'draft', 'pending', 'auto-draft' ) ) ) {
			$post_date_gmt = get_gmt_from_date( $post_date );
		} else {
			$post_date_gmt = '0000-00-00 00:00:00';
		}
	} else {
		$post_date_gmt = $postarr['post_date_gmt'];
	}

	if ( $update || '0000-00-00 00:00:00' == $post_date ) {
		$post_modified     = current_time( 'mysql' );
		$post_modified_gmt = current_time( 'mysql', 1 );
	} else {
		$post_modified     = $post_date;
		$post_modified_gmt = $post_date_gmt;
	}

	if ( 'attachment' !== $post_type ) {
		if ( 'publish' == $post_status ) {
			$now = gmdate('Y-m-d H:i:59');
			if ( mysql2date('U', $post_date_gmt, false) > mysql2date('U', $now, false) ) {
				$post_status = 'future';
			}
		} elseif( 'future' == $post_status ) {
			$now = gmdate('Y-m-d H:i:59');
			if ( mysql2date('U', $post_date_gmt, false) <= mysql2date('U', $now, false) ) {
				$post_status = 'publish';
			}
		}
	}

	if ( empty( $postarr['comment_status'] ) ) {
		if ( $update ) {
			$comment_status = 'closed';
		} else {
			$comment_status = get_option('default_comment_status');
		}
	} else {
		$comment_status = $postarr['comment_status'];
	}

	// These variables are needed by compact() later.
	$post_content_filtered = $postarr['post_content_filtered'];
	$post_author = empty( $postarr['post_author'] ) ? $user_id : $postarr['post_author'];
	$ping_status = empty( $postarr['ping_status'] ) ? get_option( 'default_ping_status' ) : $postarr['ping_status'];
	$to_ping = isset( $postarr['to_ping'] ) ? sanitize_trackback_urls( $postarr['to_ping'] ) : '';
	$pinged = isset( $postarr['pinged'] ) ? $postarr['pinged'] : '';
	$import_id = isset( $postarr['import_id'] ) ? $postarr['import_id'] : 0;

	/*
	 * The 'wp_insert_post_parent' filter expects all variables to be present.
	 * Previously, these variables would have already been extracted
	 */
	if ( isset( $postarr['menu_order'] ) ) {
		$menu_order = (int) $postarr['menu_order'];
	} else {
		$menu_order = 0;
	}

	$post_password = isset( $postarr['post_password'] ) ? $postarr['post_password'] : '';
	if ( 'private' == $post_status ) {
		$post_password = '';
	}

	if ( isset( $postarr['post_parent'] ) ) {
		$post_parent = (int) $postarr['post_parent'];
	} else {
		$post_parent = 0;
	}

	/**
	 * Filter the post parent -- used to check for and prevent hierarchy loops.
	 *
	 * @since 3.1.0
	 *
	 * @param int   $post_parent Post parent ID.
	 * @param int   $post_ID     Post ID.
	 * @param array $new_postarr Array of parsed post data.
	 * @param array $postarr     Array of sanitized, but otherwise unmodified post data.
	 */
	$post_parent = apply_filters( 'wp_insert_post_parent', $post_parent, $post_ID, compact( array_keys( $postarr ) ), $postarr );

	$post_name = wp_unique_post_slug( $post_name, $post_ID, $post_status, $post_type, $post_parent );

	// Don't unslash.
	$post_mime_type = isset( $postarr['post_mime_type'] ) ? $postarr['post_mime_type'] : '';

	// Expected_slashed (everything!).
	$data = compact( 'post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_content_filtered', 'post_title', 'post_excerpt', 'post_status', 'post_type', 'comment_status', 'ping_status', 'post_password', 'post_name', 'to_ping', 'pinged', 'post_modified', 'post_modified_gmt', 'post_parent', 'menu_order', 'post_mime_type', 'guid' );

	$emoji_fields = array( 'post_title', 'post_content', 'post_excerpt' );

	foreach( $emoji_fields as $emoji_field ) {
		if ( isset( $data[ $emoji_field ] ) ) {
			$charset = $wpdb->get_col_charset( $wpdb->posts, $emoji_field );
			if ( 'utf8' === $charset ) {
				$data[ $emoji_field ] = wp_encode_emoji( $data[ $emoji_field ] );
			}
		}
	}

	if ( 'attachment' === $post_type ) {
		/**
		 * Filter attachment post data before it is updated in or added to the database.
		 *
		 * @since 3.9.0
		 *
		 * @param array $data    An array of sanitized attachment post data.
		 * @param array $postarr An array of unsanitized attachment post data.
		 */
		$data = apply_filters( 'wp_insert_attachment_data', $data, $postarr );
	} else {
		/**
		 * Filter slashed post data just before it is inserted into the database.
		 *
		 * @since 2.7.0
		 *
		 * @param array $data    An array of slashed post data.
		 * @param array $postarr An array of sanitized, but otherwise unmodified post data.
		 */
		$data = apply_filters( 'wp_insert_post_data', $data, $postarr );
	}
	$data = wp_unslash( $data );
	$where = array( 'ID' => $post_ID );

	if ( $update ) {
		/**
		 * Fires immediately before an existing post is updated in the database.
		 *
		 * @since 2.5.0
		 *
		 * @param int   $post_ID Post ID.
		 * @param array $data    Array of unslashed post data.
		 */
		do_action( 'pre_post_update', $post_ID, $data );
		if ( false === $wpdb->update( $wpdb->posts, $data, $where ) ) {
			if ( $wp_error ) {
				return new WP_Error('db_update_error', __('Could not update post in the database'), $wpdb->last_error);
			} else {
				return 0;
			}
		}
	} else {
		// If there is a suggested ID, use it if not already present.
		if ( ! empty( $import_id ) ) {
			$import_id = (int) $import_id;
			if ( ! $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE ID = %d", $import_id) ) ) {
				$data['ID'] = $import_id;
			}
		}
		if ( false === $wpdb->insert( $wpdb->posts, $data ) ) {
			if ( $wp_error ) {
				return new WP_Error('db_insert_error', __('Could not insert post into the database'), $wpdb->last_error);
			} else {
				return 0;
			}
		}
		$post_ID = (int) $wpdb->insert_id;

		// Use the newly generated $post_ID.
		$where = array( 'ID' => $post_ID );
	}

	if ( empty( $data['post_name'] ) && ! in_array( $data['post_status'], array( 'draft', 'pending', 'auto-draft' ) ) ) {
		$data['post_name'] = sanitize_title( $data['post_title'], $post_ID );
		$wpdb->update( $wpdb->posts, array( 'post_name' => $data['post_name'] ), $where );
	}

	if ( is_object_in_taxonomy( $post_type, 'category' ) ) {
		wp_set_post_categories( $post_ID, $post_category );
	}

	if ( isset( $postarr['tags_input'] ) && is_object_in_taxonomy( $post_type, 'post_tag' ) ) {
		wp_set_post_tags( $post_ID, $postarr['tags_input'] );
	}

	// New-style support for all custom taxonomies.
	if ( ! empty( $postarr['tax_input'] ) ) {
		foreach ( $postarr['tax_input'] as $taxonomy => $tags ) {
			$taxonomy_obj = get_taxonomy($taxonomy);
			// array = hierarchical, string = non-hierarchical.
			if ( is_array( $tags ) ) {
				$tags = array_filter($tags);
			}
			if ( current_user_can( $taxonomy_obj->cap->assign_terms ) ) {
				wp_set_post_terms( $post_ID, $tags, $taxonomy );
			}
		}
	}

	$current_guid = get_post_field( 'guid', $post_ID );

	// Set GUID.
	if ( ! $update && '' == $current_guid ) {
		$wpdb->update( $wpdb->posts, array( 'guid' => get_permalink( $post_ID ) ), $where );
	}

	if ( 'attachment' === $postarr['post_type'] ) {
		if ( ! empty( $postarr['file'] ) ) {
			update_attached_file( $post_ID, $postarr['file'] );
		}

		if ( ! empty( $postarr['context'] ) ) {
			add_post_meta( $post_ID, '_wp_attachment_context', $postarr['context'], true );
		}
	}

	clean_post_cache( $post_ID );

	$post = get_post( $post_ID );

	if ( ! empty( $postarr['page_template'] ) && 'page' == $data['post_type'] ) {
		$post->page_template = $postarr['page_template'];
		$page_templates = wp_get_theme()->get_page_templates( $post );
		if ( 'default' != $postarr['page_template'] && ! isset( $page_templates[ $postarr['page_template'] ] ) ) {
			if ( $wp_error ) {
				return new WP_Error('invalid_page_template', __('The page template is invalid.'));
			}
			update_post_meta( $post_ID, '_wp_page_template', 'default' );
		} else {
			update_post_meta( $post_ID, '_wp_page_template', $postarr['page_template'] );
		}
	}

	if ( 'attachment' !== $postarr['post_type'] ) {
		wp_transition_post_status( $data['post_status'], $previous_status, $post );
	} else {
		if ( $update ) {
			/**
			 * Fires once an existing attachment has been updated.
			 *
			 * @since 2.0.0
			 *
			 * @param int $post_ID Attachment ID.
			 */
			do_action( 'edit_attachment', $post_ID );
		} else {

			/**
			 * Fires once an attachment has been added.
			 *
			 * @since 2.0.0
			 *
			 * @param int $post_ID Attachment ID.
			 */
			do_action( 'add_attachment', $post_ID );
		}

		return $post_ID;
	}

	if ( $update ) {
		/**
		 * Fires once an existing post has been updated.
		 *
		 * @since 1.2.0
		 *
		 * @param int     $post_ID Post ID.
		 * @param WP_Post $post    Post object.
		 */
		do_action( 'edit_post', $post_ID, $post );
		$post_after = get_post($post_ID);

		/**
		 * Fires once an existing post has been updated.
		 *
		 * @since 3.0.0
		 *
		 * @param int     $post_ID      Post ID.
		 * @param WP_Post $post_after   Post object following the update.
		 * @param WP_Post $post_before  Post object before the update.
		 */
		do_action( 'post_updated', $post_ID, $post_after, $post_before);
	}

	/**
	 * Fires once a post has been saved.
	 *
	 * The dynamic portion of the hook name, `$post->post_type`, refers to
	 * the post type slug.
	 *
	 * @since 3.7.0
	 *
	 * @param int     $post_ID Post ID.
	 * @param WP_Post $post    Post object.
	 * @param bool    $update  Whether this is an existing post being updated or not.
	 */
	do_action( "save_post_{$post->post_type}", $post_ID, $post, $update );

	/**
	 * Fires once a post has been saved.
	 *
	 * @since 1.5.0
	 *
	 * @param int     $post_ID Post ID.
	 * @param WP_Post $post    Post object.
	 * @param bool    $update  Whether this is an existing post being updated or not.
	 */
	do_action( 'save_post', $post_ID, $post, $update );

	/**
	 * Fires once a post has been saved.
	 *
	 * @since 2.0.0
	 *
	 * @param int     $post_ID Post ID.
	 * @param WP_Post $post    Post object.
	 * @param bool    $update  Whether this is an existing post being updated or not.
	 */
	do_action( 'wp_insert_post', $post_ID, $post, $update );

	return $post_ID;
}

/**
 * Update a post with new post data.
 *
 * The date does not have to be set for drafts. You can set the date and it will
 * not be overridden.
 *
 * @since 1.0.0
 *
 * @param array|object $postarr  Optional. Post data. Arrays are expected to be escaped,
 *                               objects are not. Default array.
 * @param bool         $wp_error Optional. Allow return of WP_Error on failure. Default false.
 * @return int|WP_Error The value 0 or WP_Error on failure. The post ID on success.
 */
function wp_update_post( $postarr = array(), $wp_error = false ) {
	if ( is_object($postarr) ) {
		// Non-escaped post was passed.
		$postarr = get_object_vars($postarr);
		$postarr = wp_slash($postarr);
	}

	// First, get all of the original fields.
	$post = get_post($postarr['ID'], ARRAY_A);

	if ( is_null( $post ) ) {
		if ( $wp_error )
			return new WP_Error( 'invalid_post', __( 'Invalid post ID.' ) );
		return 0;
	}

	// Escape data pulled from DB.
	$post = wp_slash($post);

	// Passed post category list overwrites existing category list if not empty.
	if ( isset($postarr['post_category']) && is_array($postarr['post_category'])
			 && 0 != count($postarr['post_category']) )
		$post_cats = $postarr['post_category'];
	else
		$post_cats = $post['post_category'];

	// Drafts shouldn't be assigned a date unless explicitly done so by the user.
	if ( isset( $post['post_status'] ) && in_array($post['post_status'], array('draft', 'pending', 'auto-draft')) && empty($postarr['edit_date']) &&
			 ('0000-00-00 00:00:00' == $post['post_date_gmt']) )
		$clear_date = true;
	else
		$clear_date = false;

	// Merge old and new fields with new fields overwriting old ones.
	$postarr = array_merge($post, $postarr);
	$postarr['post_category'] = $post_cats;
	if ( $clear_date ) {
		$postarr['post_date'] = current_time('mysql');
		$postarr['post_date_gmt'] = '';
	}

	if ($postarr['post_type'] == 'attachment')
		return wp_insert_attachment($postarr);

	return wp_insert_post( $postarr, $wp_error );
}

/**
 * Publish a post by transitioning the post status.
 *
 * @since 2.1.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int|WP_Post $post Post ID or post object.
 */
function wp_publish_post( $post ) {
	global $wpdb;

	if ( ! $post = get_post( $post ) )
		return;

	if ( 'publish' == $post->post_status )
		return;

	$wpdb->update( $wpdb->posts, array( 'post_status' => 'publish' ), array( 'ID' => $post->ID ) );

	clean_post_cache( $post->ID );

	$old_status = $post->post_status;
	$post->post_status = 'publish';
	wp_transition_post_status( 'publish', $old_status, $post );

	/** This action is documented in wp-includes/post.php */
	do_action( 'edit_post', $post->ID, $post );

	/** This action is documented in wp-includes/post.php */
	do_action( "save_post_{$post->post_type}", $post->ID, $post, true );

	/** This action is documented in wp-includes/post.php */
	do_action( 'save_post', $post->ID, $post, true );

	/** This action is documented in wp-includes/post.php */
	do_action( 'wp_insert_post', $post->ID, $post, true );
}

/**
 * Publish future post and make sure post ID has future post status.
 *
 * Invoked by cron 'publish_future_post' event. This safeguard prevents cron
 * from publishing drafts, etc.
 *
 * @since 2.5.0
 *
 * @param int|WP_Post $post_id Post ID or post object.
 * @return null Nothing is returned. Which can mean that no action is required
 *              or post was published.
 */
function check_and_publish_future_post( $post_id ) {

	$post = get_post($post_id);

	if ( empty($post) )
		return;

	if ( 'future' != $post->post_status )
		return;

	$time = strtotime( $post->post_date_gmt . ' GMT' );

	// Uh oh, someone jumped the gun!
	if ( $time > time() ) {
		wp_clear_scheduled_hook( 'publish_future_post', array( $post_id ) ); // clear anything else in the system
		wp_schedule_single_event( $time, 'publish_future_post', array( $post_id ) );
		return;
	}

	return wp_publish_post($post_id);
}

/**
 * Computes a unique slug for the post, when given the desired slug and some post details.
 *
 * @since 2.8.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 * @global WP_Rewrite $wp_rewrite
 *
 * @param string $slug        The desired slug (post_name).
 * @param int    $post_ID     Post ID.
 * @param string $post_status No uniqueness checks are made if the post is still draft or pending.
 * @param string $post_type   Post type.
 * @param int    $post_parent Post parent ID.
 * @return string Unique slug for the post, based on $post_name (with a -1, -2, etc. suffix)
 */
function wp_unique_post_slug( $slug, $post_ID, $post_status, $post_type, $post_parent ) {
	if ( in_array( $post_status, array( 'draft', 'pending', 'auto-draft' ) ) || ( 'inherit' == $post_status && 'revision' == $post_type ) )
		return $slug;

	global $wpdb, $wp_rewrite;

	$original_slug = $slug;

	$feeds = $wp_rewrite->feeds;
	if ( ! is_array( $feeds ) )
		$feeds = array();

	if ( 'attachment' == $post_type ) {
		// Attachment slugs must be unique across all types.
		$check_sql = "SELECT post_name FROM $wpdb->posts WHERE post_name = %s AND ID != %d LIMIT 1";
		$post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $slug, $post_ID ) );

		/**
		 * Filter whether the post slug would make a bad attachment slug.
		 *
		 * @since 3.1.0
		 *
		 * @param bool   $bad_slug Whether the slug would be bad as an attachment slug.
		 * @param string $slug     The post slug.
		 */
		if ( $post_name_check || in_array( $slug, $feeds ) || apply_filters( 'wp_unique_post_slug_is_bad_attachment_slug', false, $slug ) ) {
			$suffix = 2;
			do {
				$alt_post_name = _truncate_post_slug( $slug, 200 - ( strlen( $suffix ) + 1 ) ) . "-$suffix";
				$post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $alt_post_name, $post_ID ) );
				$suffix++;
			} while ( $post_name_check );
			$slug = $alt_post_name;
		}
	} elseif ( is_post_type_hierarchical( $post_type ) ) {
		if ( 'nav_menu_item' == $post_type )
			return $slug;

		/*
		 * Page slugs must be unique within their own trees. Pages are in a separate
		 * namespace than posts so page slugs are allowed to overlap post slugs.
		 */
		$check_sql = "SELECT post_name FROM $wpdb->posts WHERE post_name = %s AND post_type IN ( %s, 'attachment' ) AND ID != %d AND post_parent = %d LIMIT 1";
		$post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $slug, $post_type, $post_ID, $post_parent ) );

		/**
		 * Filter whether the post slug would make a bad hierarchical post slug.
		 *
		 * @since 3.1.0
		 *
		 * @param bool   $bad_slug    Whether the post slug would be bad in a hierarchical post context.
		 * @param string $slug        The post slug.
		 * @param string $post_type   Post type.
		 * @param int    $post_parent Post parent ID.
		 */
		if ( $post_name_check || in_array( $slug, $feeds ) || preg_match( "@^($wp_rewrite->pagination_base)?\d+$@", $slug )  || apply_filters( 'wp_unique_post_slug_is_bad_hierarchical_slug', false, $slug, $post_type, $post_parent ) ) {
			$suffix = 2;
			do {
				$alt_post_name = _truncate_post_slug( $slug, 200 - ( strlen( $suffix ) + 1 ) ) . "-$suffix";
				$post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $alt_post_name, $post_type, $post_ID, $post_parent ) );
				$suffix++;
			} while ( $post_name_check );
			$slug = $alt_post_name;
		}
	} else {
		// Post slugs must be unique across all posts.
		$check_sql = "SELECT post_name FROM $wpdb->posts WHERE post_name = %s AND post_type = %s AND ID != %d LIMIT 1";
		$post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $slug, $post_type, $post_ID ) );

		/**
		 * Filter whether the post slug would be bad as a flat slug.
		 *
		 * @since 3.1.0
		 *
		 * @param bool   $bad_slug  Whether the post slug would be bad as a flat slug.
		 * @param string $slug      The post slug.
		 * @param string $post_type Post type.
		 */
		if ( $post_name_check || in_array( $slug, $feeds ) || apply_filters( 'wp_unique_post_slug_is_bad_flat_slug', false, $slug, $post_type ) ) {
			$suffix = 2;
			do {
				$alt_post_name = _truncate_post_slug( $slug, 200 - ( strlen( $suffix ) + 1 ) ) . "-$suffix";
				$post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $alt_post_name, $post_type, $post_ID ) );
				$suffix++;
			} while ( $post_name_check );
			$slug = $alt_post_name;
		}
	}

	/**
	 * Filter the unique post slug.
	 *
	 * @since 3.3.0
	 *
	 * @param string $slug          The post slug.
	 * @param int    $post_ID       Post ID.
	 * @param string $post_status   The post status.
	 * @param string $post_type     Post type.
	 * @param int    $post_parent   Post parent ID
	 * @param string $original_slug The original post slug.
	 */
	return apply_filters( 'wp_unique_post_slug', $slug, $post_ID, $post_status, $post_type, $post_parent, $original_slug );
}

/**
 * Truncate a post slug.
 *
 * @since 3.6.0
 * @access private
 *
 * @see utf8_uri_encode()
 *
 * @param string $slug   The slug to truncate.
 * @param int    $length Optional. Max length of the slug. Default 200 (characters).
 * @return string The truncated slug.
 */
function _truncate_post_slug( $slug, $length = 200 ) {
	if ( strlen( $slug ) > $length ) {
		$decoded_slug = urldecode( $slug );
		if ( $decoded_slug === $slug )
			$slug = substr( $slug, 0, $length );
		else
			$slug = utf8_uri_encode( $decoded_slug, $length );
	}

	return rtrim( $slug, '-' );
}

/**
 * Add tags to a post.
 *
 * @see wp_set_post_tags()
 *
 * @since 2.3.0
 *
 * @param int    $post_id Optional. The Post ID. Does not default to the ID of the global $post.
 *                        Default 0.
 * @param string $tags    Optional. The tags to set for the post, separated by commas. Default empty.
 * @return bool|null Will return false if $post_id is not an integer or is 0. Will return null otherwise.
 */
function wp_add_post_tags( $post_id = 0, $tags = '' ) {
	return wp_set_post_tags($post_id, $tags, true);
}

/**
 * Set the tags for a post.
 *
 * @since 2.3.0
 *
 * @see wp_set_object_terms()
 *
 * @param int    $post_id Optional. The Post ID. Does not default to the ID of the global $post.
 * @param string $tags    Optional. The tags to set for the post, separated by commas.
 *                        Default empty.
 * @param bool   $append  Optional. If true, don't delete existing tags, just add on. If false,
 *                        replace the tags with the new tags. Default false.
 * @return mixed Array of affected term IDs. WP_Error or false on failure.
 */
function wp_set_post_tags( $post_id = 0, $tags = '', $append = false ) {
	return wp_set_post_terms( $post_id, $tags, 'post_tag', $append);
}

/**
 * Set the terms for a post.
 *
 * @since 2.8.0
 *
 * @see wp_set_object_terms()
 *
 * @param int    $post_id  Optional. The Post ID. Does not default to the ID of the global $post.
 * @param string $tags     Optional. The tags to set for the post, separated by commas. Default empty.
 * @param string $taxonomy Optional. Taxonomy name. Default 'post_tag'.
 * @param bool   $append   Optional. If true, don't delete existing tags, just add on. If false,
 *                         replace the tags with the new tags. Default false.
 * @return mixed Array of affected term IDs. WP_Error or false on failure.
 */
function wp_set_post_terms( $post_id = 0, $tags = '', $taxonomy = 'post_tag', $append = false ) {
	$post_id = (int) $post_id;

	if ( !$post_id )
		return false;

	if ( empty($tags) )
		$tags = array();

	if ( ! is_array( $tags ) ) {
		$comma = _x( ',', 'tag delimiter' );
		if ( ',' !== $comma )
			$tags = str_replace( $comma, ',', $tags );
		$tags = explode( ',', trim( $tags, " \n\t\r\0\x0B," ) );
	}

	/*
	 * Hierarchical taxonomies must always pass IDs rather than names so that
	 * children with the same names but different parents aren't confused.
	 */
	if ( is_taxonomy_hierarchical( $taxonomy ) ) {
		$tags = array_unique( array_map( 'intval', $tags ) );
	}

	return wp_set_object_terms( $post_id, $tags, $taxonomy, $append );
}

/**
 * Set categories for a post.
 *
 * If the post categories parameter is not set, then the default category is
 * going used.
 *
 * @since 2.1.0
 *
 * @param int       $post_ID         Optional. The Post ID. Does not default to the ID
 *                                   of the global $post. Default 0.
 * @param array|int $post_categories Optional. List of categories or ID of category.
 *                                   Default empty array.
 * @param bool      $append         If true, don't delete existing categories, just add on.
 *                                  If false, replace the categories with the new categories.
 * @return bool|mixed
 */
function wp_set_post_categories( $post_ID = 0, $post_categories = array(), $append = false ) {
	$post_ID = (int) $post_ID;
	$post_type = get_post_type( $post_ID );
	$post_status = get_post_status( $post_ID );
	// If $post_categories isn't already an array, make it one:
	$post_categories = (array) $post_categories;
	if ( empty( $post_categories ) ) {
		if ( 'post' == $post_type && 'auto-draft' != $post_status ) {
			$post_categories = array( get_option('default_category') );
			$append = false;
		} else {
			$post_categories = array();
		}
	} elseif ( 1 == count( $post_categories ) && '' == reset( $post_categories ) ) {
		return true;
	}

	return wp_set_post_terms( $post_ID, $post_categories, 'category', $append );
}

/**
 * Transition the post status of a post.
 *
 * When a post is saved, the post status is "transitioned" from one status to another,
 * though this does not always mean the status has actually changed before and after
 * the save.
 *
 * For instance: When publishing a post for the first time, the post status may transition
 * from 'draft' – or some other status – to 'publish'. However, if a post is already
 * published and is simply being updated, the "old" and "new" statuses may both be 'publish'
 * before and after the transition.
 *
 * @since 2.3.0
 *
 * @param string  $new_status Transition to this post status.
 * @param string  $old_status Previous post status.
 * @param WP_Post $post Post data.
 */
function wp_transition_post_status( $new_status, $old_status, $post ) {
	/**
	 * Fires when a post is transitioned from one status to another.
	 *
	 * @since 2.3.0
	 *
	 * @param string  $new_status New post status.
	 * @param string  $old_status Old post status.
	 * @param WP_Post $post       Post object.
	 */
	do_action( 'transition_post_status', $new_status, $old_status, $post );

	/**
	 * Fires when a post is transitioned from one status to another.
	 *
	 * The dynamic portions of the hook name, `$new_status` and `$old status`,
	 * refer to the old and new post statuses, respectively.
	 *
	 * @since 2.3.0
	 *
	 * @param WP_Post $post Post object.
	 */
	do_action( "{$old_status}_to_{$new_status}", $post );

	/**
	 * Fires when a post is transitioned from one status to another.
	 *
	 * The dynamic portions of the hook name, `$new_status` and `$post->post_type`,
	 * refer to the new post status and post type, respectively.
	 *
	 * Please note: When this action is hooked using a particular post status (like
	 * 'publish', as `publish_{$post->post_type}`), it will fire both when a post is
	 * first transitioned to that status from something else, as well as upon
	 * subsequent post updates (old and new status are both the same).
	 *
	 * Therefore, if you are looking to only fire a callback when a post is first
	 * transitioned to a status, use the {@see 'transition_post_status'} hook instead.
	 *
	 * @since 2.3.0
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 */
	do_action( "{$new_status}_{$post->post_type}", $post->ID, $post );
}

//
// Trackback and ping functions
//

/**
 * Add a URL to those already pinged.
 *
 * @since 1.5.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int    $post_id Post ID.
 * @param string $uri     Ping URI.
 * @return int How many rows were updated.
 */
function add_ping( $post_id, $uri ) {
	global $wpdb;
	$pung = $wpdb->get_var( $wpdb->prepare( "SELECT pinged FROM $wpdb->posts WHERE ID = %d", $post_id ));
	$pung = trim($pung);
	$pung = preg_split('/\s/', $pung);
	$pung[] = $uri;
	$new = implode("\n", $pung);

	/**
	 * Filter the new ping URL to add for the given post.
	 *
	 * @since 2.0.0
	 *
	 * @param string $new New ping URL to add.
	 */
	$new = apply_filters( 'add_ping', $new );

	// expected_slashed ($new).
	$new = wp_unslash($new);
	return $wpdb->update( $wpdb->posts, array( 'pinged' => $new ), array( 'ID' => $post_id ) );
}

/**
 * Retrieve enclosures already enclosed for a post.
 *
 * @since 1.5.0
 *
 * @param int $post_id Post ID.
 * @return array List of enclosures.
 */
function get_enclosed( $post_id ) {
	$custom_fields = get_post_custom( $post_id );
	$pung = array();
	if ( !is_array( $custom_fields ) )
		return $pung;

	foreach ( $custom_fields as $key => $val ) {
		if ( 'enclosure' != $key || !is_array( $val ) )
			continue;
		foreach( $val as $enc ) {
			$enclosure = explode( "\n", $enc );
			$pung[] = trim( $enclosure[ 0 ] );
		}
	}

	/**
	 * Filter the list of enclosures already enclosed for the given post.
	 *
	 * @since 2.0.0
	 *
	 * @param array $pung    Array of enclosures for the given post.
	 * @param int   $post_id Post ID.
	 */
	$pung = apply_filters( 'get_enclosed', $pung, $post_id );
	return $pung;
}

/**
 * Retrieve URLs already pinged for a post.
 *
 * @since 1.5.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int $post_id Post ID.
 * @return array
 */
function get_pung( $post_id ) {
	global $wpdb;
	$pung = $wpdb->get_var( $wpdb->prepare( "SELECT pinged FROM $wpdb->posts WHERE ID = %d", $post_id ));
	$pung = trim($pung);
	$pung = preg_split('/\s/', $pung);

	/**
	 * Filter the list of already-pinged URLs for the given post.
	 *
	 * @since 2.0.0
	 *
	 * @param array $pung Array of URLs already pinged for the given post.
	 */
	$pung = apply_filters( 'get_pung', $pung );
	return $pung;
}

/**
 * Retrieve URLs that need to be pinged.
 *
 * @since 1.5.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int $post_id Post ID
 * @return array
 */
function get_to_ping( $post_id ) {
	global $wpdb;
	$to_ping = $wpdb->get_var( $wpdb->prepare( "SELECT to_ping FROM $wpdb->posts WHERE ID = %d", $post_id ));
	$to_ping = sanitize_trackback_urls( $to_ping );
	$to_ping = preg_split('/\s/', $to_ping, -1, PREG_SPLIT_NO_EMPTY);

	/**
	 * Filter the list of URLs yet to ping for the given post.
	 *
	 * @since 2.0.0
	 *
	 * @param array $to_ping List of URLs yet to ping.
	 */
	$to_ping = apply_filters( 'get_to_ping', $to_ping );
	return $to_ping;
}

/**
 * Do trackbacks for a list of URLs.
 *
 * @since 1.0.0
 *
 * @param string $tb_list Comma separated list of URLs.
 * @param int    $post_id Post ID.
 */
function trackback_url_list( $tb_list, $post_id ) {
	if ( ! empty( $tb_list ) ) {
		// Get post data.
		$postdata = get_post( $post_id, ARRAY_A );

		// Form an excerpt.
		$excerpt = strip_tags( $postdata['post_excerpt'] ? $postdata['post_excerpt'] : $postdata['post_content'] );

		if ( strlen( $excerpt ) > 255 ) {
			$excerpt = substr( $excerpt, 0, 252 ) . '&hellip;';
		}

		$trackback_urls = explode( ',', $tb_list );
		foreach( (array) $trackback_urls as $tb_url ) {
			$tb_url = trim( $tb_url );
			trackback( $tb_url, wp_unslash( $postdata['post_title'] ), $excerpt, $post_id );
		}
	}
}

//
// Page functions
//

/**
 * Get a list of page IDs.
 *
 * @since 2.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @return array List of page IDs.
 */
function get_all_page_ids() {
	global $wpdb;

	$page_ids = wp_cache_get('all_page_ids', 'posts');
	if ( ! is_array( $page_ids ) ) {
		$page_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_type = 'page'");
		wp_cache_add('all_page_ids', $page_ids, 'posts');
	}

	return $page_ids;
}

/**
 * Retrieves page data given a page ID or page object.
 *
 * Use get_post() instead of get_page().
 *
 * @since 1.5.1
 * @deprecated 3.5.0 Use get_post()
 *
 * @param mixed  $page   Page object or page ID. Passed by reference.
 * @param string $output Optional. What to output. Accepts OBJECT, ARRAY_A, or ARRAY_N.
 *                       Default OBJECT.
 * @param string $filter Optional. How the return value should be filtered. Accepts 'raw',
 *                       'edit', 'db', 'display'. Default 'raw'.
 * @return WP_Post|null WP_Post on success or null on failure.
 */
function get_page( $page, $output = OBJECT, $filter = 'raw') {
	return get_post( $page, $output, $filter );
}

/**
 * Retrieves a page given its path.
 *
 * @since 2.1.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string       $page_path Page path.
 * @param string       $output    Optional. Output type. Accepts OBJECT, ARRAY_N, or ARRAY_A.
 *                                Default OBJECT.
 * @param string|array $post_type Optional. Post type or array of post types. Default 'page'.
 * @return WP_Post|null WP_Post on success or null on failure.
 */
function get_page_by_path( $page_path, $output = OBJECT, $post_type = 'page' ) {
	global $wpdb;

	$page_path = rawurlencode(urldecode($page_path));
	$page_path = str_replace('%2F', '/', $page_path);
	$page_path = str_replace('%20', ' ', $page_path);
	$parts = explode( '/', trim( $page_path, '/' ) );
	$parts = array_map( 'sanitize_title_for_query', $parts );
	$escaped_parts = esc_sql( $parts );

	$in_string = "'" . implode( "','", $escaped_parts ) . "'";

	if ( is_array( $post_type ) ) {
		$post_types = $post_type;
	} else {
		$post_types = array( $post_type, 'attachment' );
	}

	$post_types = esc_sql( $post_types );
	$post_type_in_string = "'" . implode( "','", $post_types ) . "'";
	$sql = "
		SELECT ID, post_name, post_parent, post_type
		FROM $wpdb->posts
		WHERE post_name IN ($in_string)
		AND post_type IN ($post_type_in_string)
	";

	$pages = $wpdb->get_results( $sql, OBJECT_K );

	$revparts = array_reverse( $parts );

	$foundid = 0;
	foreach ( (array) $pages as $page ) {
		if ( $page->post_name == $revparts[0] ) {
			$count = 0;
			$p = $page;
			while ( $p->post_parent != 0 && isset( $pages[ $p->post_parent ] ) ) {
				$count++;
				$parent = $pages[ $p->post_parent ];
				if ( ! isset( $revparts[ $count ] ) || $parent->post_name != $revparts[ $count ] )
					break;
				$p = $parent;
			}

			if ( $p->post_parent == 0 && $count+1 == count( $revparts ) && $p->post_name == $revparts[ $count ] ) {
				$foundid = $page->ID;
				if ( $page->post_type == $post_type )
					break;
			}
		}
	}

	if ( $foundid )
		return get_post( $foundid, $output );

	return null;
}

/**
 * Retrieve a page given its title.
 *
 * @since 2.1.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string       $page_title Page title
 * @param string       $output     Optional. Output type. OBJECT, ARRAY_N, or ARRAY_A.
 *                                 Default OBJECT.
 * @param string|array $post_type  Optional. Post type or array of post types. Default 'page'.
 * @return WP_Post|null WP_Post on success or null on failure
 */
function get_page_by_title( $page_title, $output = OBJECT, $post_type = 'page' ) {
	global $wpdb;

	if ( is_array( $post_type ) ) {
		$post_type = esc_sql( $post_type );
		$post_type_in_string = "'" . implode( "','", $post_type ) . "'";
		$sql = $wpdb->prepare( "
			SELECT ID
			FROM $wpdb->posts
			WHERE post_title = %s
			AND post_type IN ($post_type_in_string)
		", $page_title );
	} else {
		$sql = $wpdb->prepare( "
			SELECT ID
			FROM $wpdb->posts
			WHERE post_title = %s
			AND post_type = %s
		", $page_title, $post_type );
	}

	$page = $wpdb->get_var( $sql );

	if ( $page )
		return get_post( $page, $output );

	return null;
}

/**
 * Retrieve child pages from list of pages matching page ID.
 *
 * Matches against the pages parameter against the page ID. Also matches all
 * children for the same to retrieve all children of a page. Does not make any
 * SQL queries to get the children.
 *
 * @since 1.5.1
 *
 * @param int   $page_id    Page ID.
 * @param array $pages      List of pages' objects.
 * @return array List of page children.
 */
function get_page_children( $page_id, $pages ) {
	$page_list = array();
	foreach ( (array) $pages as $page ) {
		if ( $page->post_parent == $page_id ) {
			$page_list[] = $page;
			if ( $children = get_page_children( $page->ID, $pages ) ) {
				$page_list = array_merge( $page_list, $children );
			}
		}
	}

	return $page_list;
}

/**
 * Order the pages with children under parents in a flat list.
 *
 * It uses auxiliary structure to hold parent-children relationships and
 * runs in O(N) complexity
 *
 * @since 2.0.0
 *
 * @param array $pages   Posts array, passed by reference.
 * @param int   $page_id Optional. Parent page ID. Default 0.
 * @return array A list arranged by hierarchy. Children immediately follow their parents.
 */
function get_page_hierarchy( &$pages, $page_id = 0 ) {
	if ( empty( $pages ) ) {
		$result = array();
		return $result;
	}

	$children = array();
	foreach ( (array) $pages as $p ) {
		$parent_id = intval( $p->post_parent );
		$children[ $parent_id ][] = $p;
	}

	$result = array();
	_page_traverse_name( $page_id, $children, $result );

	return $result;
}

/**
 * Traverse and return all the nested children post names of a root page.
 *
 * $children contains parent-children relations
 *
 * @since 2.9.0
 *
 * @see _page_traverse_name()
 *
 * @param int   $page_id   Page ID.
 * @param array &$children Parent-children relations, passed by reference.
 * @param array &$result   Result, passed by reference.
 */
function _page_traverse_name( $page_id, &$children, &$result ){
	if ( isset( $children[ $page_id ] ) ){
		foreach( (array)$children[ $page_id ] as $child ) {
			$result[ $child->ID ] = $child->post_name;
			_page_traverse_name( $child->ID, $children, $result );
		}
	}
}

/**
 * Build URI for a page.
 *
 * Sub pages will be in the "directory" under the parent page post name.
 *
 * @since 1.5.0
 *
 * @param WP_Post|object|int $page Page object or page ID.
 * @return string|false Page URI, false on error.
 */
function get_page_uri( $page ) {
	$page = get_post( $page );

	if ( ! $page )
		return false;

	$uri = $page->post_name;

	foreach ( $page->ancestors as $parent ) {
		$uri = get_post( $parent )->post_name . '/' . $uri;
	}

	return $uri;
}

/**
 * Retrieve a list of pages.
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @since 1.5.0
 *
 * @param array|string $args {
 *     Optional. Array or string of arguments to retrieve pages.
 *
 *     @type int          $child_of     Page ID to return child and grandchild pages of.
 *                                      Default 0, or no restriction.
 *     @type string       $sort_order   How to sort retrieved pages. Accepts 'ASC', 'DESC'. Default 'ASC'.
 *     @type string       $sort_column  What columns to sort pages by, comma-separated. Accepts 'post_author',
 *                                      'post_date', 'post_title', 'post_name', 'post_modified', 'menu_order',
 *                                      'post_modified_gmt', 'post_parent', 'ID', 'rand', 'comment_count'.
 *                                      'post_' can be omitted for any values that start with it.
 *                                      Default 'post_title'.
 *     @type bool         $hierarchical Whether to return pages hierarchically. Default true.
 *     @type array        $exclude      Array of page IDs to exclude. Default empty array.
 *     @type array        $include      Array of page IDs to include. Cannot be used with `$child_of`,
 *                                      `$parent`, `$exclude`, `$meta_key`, `$meta_value`, or `$hierarchical`.
 *                                      Default empty array.
 *     @type string       $meta_key     Only include pages with this meta key. Default empty.
 *     @type string       $meta_value   Only include pages with this meta value. Requires `$meta_key`.
 *                                      Default empty.
 *     @type string       $authors      A comma-separated list of author IDs. Default empty.
 *     @type int          $parent       Page ID to return direct children of. `$hierarchical` must be false.
 *                                      Default -1, or no restriction.
 *     @type string|array $exclude_tree Comma-separated string or array of page IDs to exclude.
 *                                      Default empty array.
 *     @type int          $number       The number of pages to return. Default 0, or all pages.
 *     @type int          $offset       The number of pages to skip before returning. Requires `$number`.
 *                                      Default 0.
 *     @type string       $post_type    The post type to query. Default 'page'.
 *     @type string       $post_status  A comma-separated list of post status types to include.
 *                                      Default 'publish'.
 * }
 * @return array List of pages matching defaults or `$args`.
 */
function get_pages( $args = array() ) {
	global $wpdb;

	$defaults = array(
		'child_of' => 0, 'sort_order' => 'ASC',
		'sort_column' => 'post_title', 'hierarchical' => 1,
		'exclude' => array(), 'include' => array(),
		'meta_key' => '', 'meta_value' => '',
		'authors' => '', 'parent' => -1, 'exclude_tree' => array(),
		'number' => '', 'offset' => 0,
		'post_type' => 'page', 'post_status' => 'publish',
	);

	$r = wp_parse_args( $args, $defaults );

	$number = (int) $r['number'];
	$offset = (int) $r['offset'];
	$child_of = (int) $r['child_of'];
	$hierarchical = $r['hierarchical'];
	$exclude = $r['exclude'];
	$meta_key = $r['meta_key'];
	$meta_value = $r['meta_value'];
	$parent = $r['parent'];
	$post_status = $r['post_status'];

	// Make sure the post type is hierarchical.
	$hierarchical_post_types = get_post_types( array( 'hierarchical' => true ) );
	if ( ! in_array( $r['post_type'], $hierarchical_post_types ) ) {
		return false;
	}

	if ( $parent > 0 && ! $child_of ) {
		$hierarchical = false;
	}

	// Make sure we have a valid post status.
	if ( ! is_array( $post_status ) ) {
		$post_status = explode( ',', $post_status );
	}
	if ( array_diff( $post_status, get_post_stati() ) ) {
		return false;
	}

	// $args can be whatever, only use the args defined in defaults to compute the key.
	$key = md5( serialize( wp_array_slice_assoc( $r, array_keys( $defaults ) ) ) );
	$last_changed = wp_cache_get( 'last_changed', 'posts' );
	if ( ! $last_changed ) {
		$last_changed = microtime();
		wp_cache_set( 'last_changed', $last_changed, 'posts' );
	}

	$cache_key = "get_pages:$key:$last_changed";
	if ( $cache = wp_cache_get( $cache_key, 'posts' ) ) {
		// Convert to WP_Post instances.
		$pages = array_map( 'get_post', $cache );
		/** This filter is documented in wp-includes/post.php */
		$pages = apply_filters( 'get_pages', $pages, $r );
		return $pages;
	}

	$inclusions = '';
	if ( ! empty( $r['include'] ) ) {
		$child_of = 0; //ignore child_of, parent, exclude, meta_key, and meta_value params if using include
		$parent = -1;
		$exclude = '';
		$meta_key = '';
		$meta_value = '';
		$hierarchical = false;
		$incpages = wp_parse_id_list( $r['include'] );
		if ( ! empty( $incpages ) ) {
			$inclusions = ' AND ID IN (' . implode( ',', $incpages ) .  ')';
		}
	}

	$exclusions = '';
	if ( ! empty( $exclude ) ) {
		$expages = wp_parse_id_list( $exclude );
		if ( ! empty( $expages ) ) {
			$exclusions = ' AND ID NOT IN (' . implode( ',', $expages ) .  ')';
		}
	}

	$author_query = '';
	if ( ! empty( $r['authors'] ) ) {
		$post_authors = preg_split( '/[\s,]+/', $r['authors'] );

		if ( ! empty( $post_authors ) ) {
			foreach ( $post_authors as $post_author ) {
				//Do we have an author id or an author login?
				if ( 0 == intval($post_author) ) {
					$post_author = get_user_by('login', $post_author);
					if ( empty( $post_author ) ) {
						continue;
					}
					if ( empty( $post_author->ID ) ) {
						continue;
					}
					$post_author = $post_author->ID;
				}

				if ( '' == $author_query ) {
					$author_query = $wpdb->prepare(' post_author = %d ', $post_author);
				} else {
					$author_query .= $wpdb->prepare(' OR post_author = %d ', $post_author);
				}
			}
			if ( '' != $author_query ) {
				$author_query = " AND ($author_query)";
			}
		}
	}

	$join = '';
	$where = "$exclusions $inclusions ";
	if ( '' !== $meta_key || '' !== $meta_value ) {
		$join = " LEFT JOIN $wpdb->postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id )";

		// meta_key and meta_value might be slashed
		$meta_key = wp_unslash($meta_key);
		$meta_value = wp_unslash($meta_value);
		if ( '' !== $meta_key ) {
			$where .= $wpdb->prepare(" AND $wpdb->postmeta.meta_key = %s", $meta_key);
		}
		if ( '' !== $meta_value ) {
			$where .= $wpdb->prepare(" AND $wpdb->postmeta.meta_value = %s", $meta_value);
		}

	}

	if ( is_array( $parent ) ) {
		$post_parent__in = implode( ',', array_map( 'absint', (array) $parent ) );
		if ( ! empty( $post_parent__in ) ) {
			$where .= " AND post_parent IN ($post_parent__in)";
		}
	} elseif ( $parent >= 0 ) {
		$where .= $wpdb->prepare(' AND post_parent = %d ', $parent);
	}

	if ( 1 == count( $post_status ) ) {
		$where_post_type = $wpdb->prepare( "post_type = %s AND post_status = %s", $r['post_type'], reset( $post_status ) );
	} else {
		$post_status = implode( "', '", $post_status );
		$where_post_type = $wpdb->prepare( "post_type = %s AND post_status IN ('$post_status')", $r['post_type'] );
	}

	$orderby_array = array();
	$allowed_keys = array( 'author', 'post_author', 'date', 'post_date', 'title', 'post_title', 'name', 'post_name', 'modified',
		'post_modified', 'modified_gmt', 'post_modified_gmt', 'menu_order', 'parent', 'post_parent',
		'ID', 'rand', 'comment_count' );

	foreach ( explode( ',', $r['sort_column'] ) as $orderby ) {
		$orderby = trim( $orderby );
		if ( ! in_array( $orderby, $allowed_keys ) ) {
			continue;
		}

		switch ( $orderby ) {
			case 'menu_order':
				break;
			case 'ID':
				$orderby = "$wpdb->posts.ID";
				break;
			case 'rand':
				$orderby = 'RAND()';
				break;
			case 'comment_count':
				$orderby = "$wpdb->posts.comment_count";
				break;
			default:
				if ( 0 === strpos( $orderby, 'post_' ) ) {
					$orderby = "$wpdb->posts." . $orderby;
				} else {
					$orderby = "$wpdb->posts.post_" . $orderby;
				}
		}

		$orderby_array[] = $orderby;

	}
	$sort_column = ! empty( $orderby_array ) ? implode( ',', $orderby_array ) : "$wpdb->posts.post_title";

	$sort_order = strtoupper( $r['sort_order'] );
	if ( '' !== $sort_order && ! in_array( $sort_order, array( 'ASC', 'DESC' ) ) ) {
		$sort_order = 'ASC';
	}

	$query = "SELECT * FROM $wpdb->posts $join WHERE ($where_post_type) $where ";
	$query .= $author_query;
	$query .= " ORDER BY " . $sort_column . " " . $sort_order ;

	if ( ! empty( $number ) ) {
		$query .= ' LIMIT ' . $offset . ',' . $number;
	}

	$pages = $wpdb->get_results($query);

	if ( empty($pages) ) {
		/** This filter is documented in wp-includes/post.php */
		$pages = apply_filters( 'get_pages', array(), $r );
		return $pages;
	}

	// Sanitize before caching so it'll only get done once.
	$num_pages = count($pages);
	for ($i = 0; $i < $num_pages; $i++) {
		$pages[$i] = sanitize_post($pages[$i], 'raw');
	}

	// Update cache.
	update_post_cache( $pages );

	if ( $child_of || $hierarchical ) {
		$pages = get_page_children($child_of, $pages);
	}

	if ( ! empty( $r['exclude_tree'] ) ) {
		$exclude = wp_parse_id_list( $r['exclude_tree'] );
		foreach( $exclude as $id ) {
			$children = get_page_children( $id, $pages );
			foreach ( $children as $child ) {
				$exclude[] = $child->ID;
			}
		}

		$num_pages = count( $pages );
		for ( $i = 0; $i < $num_pages; $i++ ) {
			if ( in_array( $pages[$i]->ID, $exclude ) ) {
				unset( $pages[$i] );
			}
		}
	}

	$page_structure = array();
	foreach ( $pages as $page ) {
		$page_structure[] = $page->ID;
	}

	wp_cache_set( $cache_key, $page_structure, 'posts' );

	// Convert to WP_Post instances
	$pages = array_map( 'get_post', $pages );

	/**
	 * Filter the retrieved list of pages.
	 *
	 * @since 2.1.0
	 *
	 * @param array $pages List of pages to retrieve.
	 * @param array $r     Array of get_pages() arguments.
	 */
	$pages = apply_filters( 'get_pages', $pages, $r );

	return $pages;
}

//
// Attachment functions
//

/**
 * Check if the attachment URI is local one and is really an attachment.
 *
 * @since 2.0.0
 *
 * @param string $url URL to check
 * @return bool True on success, false on failure.
 */
function is_local_attachment($url) {
	if (strpos($url, home_url()) === false)
		return false;
	if (strpos($url, home_url('/?attachment_id=')) !== false)
		return true;
	if ( $id = url_to_postid($url) ) {
		$post = get_post($id);
		if ( 'attachment' == $post->post_type )
			return true;
	}
	return false;
}

/**
 * Insert an attachment.
 *
 * If you set the 'ID' in the $args parameter, it will mean that you are
 * updating and attempt to update the attachment. You can also set the
 * attachment name or title by setting the key 'post_name' or 'post_title'.
 *
 * You can set the dates for the attachment manually by setting the 'post_date'
 * and 'post_date_gmt' keys' values.
 *
 * By default, the comments will use the default settings for whether the
 * comments are allowed. You can close them manually or keep them open by
 * setting the value for the 'comment_status' key.
 *
 * @since 2.0.0
 *
 * @see wp_insert_post()
 *
 * @param string|array $args   Arguments for inserting an attachment.
 * @param string       $file   Optional. Filename.
 * @param int          $parent Optional. Parent post ID.
 * @return int Attachment ID.
 */
function wp_insert_attachment( $args, $file = false, $parent = 0 ) {
	$defaults = array(
		'file'        => $file,
		'post_parent' => 0
	);

	$data = wp_parse_args( $args, $defaults );

	if ( ! empty( $parent ) ) {
		$data['post_parent'] = $parent;
	}

	$data['post_type'] = 'attachment';

	return wp_insert_post( $data );
}

/**
 * Trash or delete an attachment.
 *
 * When an attachment is permanently deleted, the file will also be removed.
 * Deletion removes all post meta fields, taxonomy, comments, etc. associated
 * with the attachment (except the main post).
 *
 * The attachment is moved to the trash instead of permanently deleted unless trash
 * for media is disabled, item is already in the trash, or $force_delete is true.
 *
 * @since 2.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int  $post_id      Attachment ID.
 * @param bool $force_delete Optional. Whether to bypass trash and force deletion.
 *                           Default false.
 * @return mixed False on failure. Post data on success.
 */
function wp_delete_attachment( $post_id, $force_delete = false ) {
	global $wpdb;

	if ( !$post = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->posts WHERE ID = %d", $post_id) ) )
		return $post;

	if ( 'attachment' != $post->post_type )
		return false;

	if ( !$force_delete && EMPTY_TRASH_DAYS && MEDIA_TRASH && 'trash' != $post->post_status )
		return wp_trash_post( $post_id );

	delete_post_meta($post_id, '_wp_trash_meta_status');
	delete_post_meta($post_id, '_wp_trash_meta_time');

	$meta = wp_get_attachment_metadata( $post_id );
	$backup_sizes = get_post_meta( $post->ID, '_wp_attachment_backup_sizes', true );
	$file = get_attached_file( $post_id );

	if ( is_multisite() )
		delete_transient( 'dirsize_cache' );

	/**
	 * Fires before an attachment is deleted, at the start of wp_delete_attachment().
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_id Attachment ID.
	 */
	do_action( 'delete_attachment', $post_id );

	wp_delete_object_term_relationships($post_id, array('category', 'post_tag'));
	wp_delete_object_term_relationships($post_id, get_object_taxonomies($post->post_type));

	// Delete all for any posts.
	delete_metadata( 'post', null, '_thumbnail_id', $post_id, true );

	$comment_ids = $wpdb->get_col( $wpdb->prepare( "SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = %d", $post_id ));
	foreach ( $comment_ids as $comment_id )
		wp_delete_comment( $comment_id, true );

	$post_meta_ids = $wpdb->get_col( $wpdb->prepare( "SELECT meta_id FROM $wpdb->postmeta WHERE post_id = %d ", $post_id ));
	foreach ( $post_meta_ids as $mid )
		delete_metadata_by_mid( 'post', $mid );

	/** This action is documented in wp-includes/post.php */
	do_action( 'delete_post', $post_id );
	$result = $wpdb->delete( $wpdb->posts, array( 'ID' => $post_id ) );
	if ( ! $result ) {
		return false;
	}
	/** This action is documented in wp-includes/post.php */
	do_action( 'deleted_post', $post_id );

	$uploadpath = wp_upload_dir();

	if ( ! empty($meta['thumb']) ) {
		// Don't delete the thumb if another attachment uses it.
		if (! $wpdb->get_row( $wpdb->prepare( "SELECT meta_id FROM $wpdb->postmeta WHERE meta_key = '_wp_attachment_metadata' AND meta_value LIKE %s AND post_id <> %d", '%' . $wpdb->esc_like( $meta['thumb'] ) . '%', $post_id)) ) {
			$thumbfile = str_replace(basename($file), $meta['thumb'], $file);
			/** This filter is documented in wp-includes/functions.php */
			$thumbfile = apply_filters( 'wp_delete_file', $thumbfile );
			@ unlink( path_join($uploadpath['basedir'], $thumbfile) );
		}
	}

	// Remove intermediate and backup images if there are any.
	if ( isset( $meta['sizes'] ) && is_array( $meta['sizes'] ) ) {
		foreach ( $meta['sizes'] as $size => $sizeinfo ) {
			$intermediate_file = str_replace( basename( $file ), $sizeinfo['file'], $file );
			/** This filter is documented in wp-includes/functions.php */
			$intermediate_file = apply_filters( 'wp_delete_file', $intermediate_file );
			@ unlink( path_join( $uploadpath['basedir'], $intermediate_file ) );
		}
	}

	if ( is_array($backup_sizes) ) {
		foreach ( $backup_sizes as $size ) {
			$del_file = path_join( dirname($meta['file']), $size['file'] );
			/** This filter is documented in wp-includes/functions.php */
			$del_file = apply_filters( 'wp_delete_file', $del_file );
			@ unlink( path_join($uploadpath['basedir'], $del_file) );
		}
	}

	wp_delete_file( $file );

	clean_post_cache( $post );

	return $post;
}

/**
 * Retrieve attachment meta field for attachment ID.
 *
 * @since 2.1.0
 *
 * @param int  $post_id    Attachment ID. Default 0.
 * @param bool $unfiltered Optional. If true, filters are not run. Default false.
 * @return string|bool Attachment meta field. False on failure.
 */
function wp_get_attachment_metadata( $post_id = 0, $unfiltered = false ) {
	$post_id = (int) $post_id;
	if ( !$post = get_post( $post_id ) )
		return false;

	$data = get_post_meta( $post->ID, '_wp_attachment_metadata', true );

	if ( $unfiltered )
		return $data;

	/**
	 * Filter the attachment meta data.
	 *
	 * @since 2.1.0
	 *
	 * @param array|bool $data    Array of meta data for the given attachment, or false
	 *                            if the object does not exist.
	 * @param int        $post_id Attachment ID.
	 */
	return apply_filters( 'wp_get_attachment_metadata', $data, $post->ID );
}

/**
 * Update metadata for an attachment.
 *
 * @since 2.1.0
 *
 * @param int   $post_id Attachment ID.
 * @param array $data    Attachment data.
 * @return int|bool False if $post is invalid.
 */
function wp_update_attachment_metadata( $post_id, $data ) {
	$post_id = (int) $post_id;
	if ( !$post = get_post( $post_id ) )
		return false;

	/**
	 * Filter the updated attachment meta data.
	 *
	 * @since 2.1.0
	 *
	 * @param array $data    Array of updated attachment meta data.
	 * @param int   $post_id Attachment ID.
	 */
	if ( $data = apply_filters( 'wp_update_attachment_metadata', $data, $post->ID ) )
		return update_post_meta( $post->ID, '_wp_attachment_metadata', $data );
	else
		return delete_post_meta( $post->ID, '_wp_attachment_metadata' );
}

/**
 * Retrieve the URL for an attachment.
 *
 * @since 2.1.0
 *
 * @param int $post_id Optional. Attachment ID. Default 0.
 * @return string|bool Attachment URL, otherwise false.
 */
function wp_get_attachment_url( $post_id = 0 ) {
	$post_id = (int) $post_id;
	if ( !$post = get_post( $post_id ) )
		return false;

	if ( 'attachment' != $post->post_type )
		return false;

	$url = '';
	// Get attached file.
	if ( $file = get_post_meta( $post->ID, '_wp_attached_file', true) ) {
		// Get upload directory.
		if ( ($uploads = wp_upload_dir()) && false === $uploads['error'] ) {
			// Check that the upload base exists in the file location.
			if ( 0 === strpos( $file, $uploads['basedir'] ) ) {
				// Replace file location with url location.
				$url = str_replace($uploads['basedir'], $uploads['baseurl'], $file);
			} elseif ( false !== strpos($file, 'wp-content/uploads') ) {
				$url = $uploads['baseurl'] . substr( $file, strpos($file, 'wp-content/uploads') + 18 );
			} else {
				// It's a newly-uploaded file, therefore $file is relative to the basedir.
				$url = $uploads['baseurl'] . "/$file";
			}
		}
	}

	/*
	 * If any of the above options failed, Fallback on the GUID as used pre-2.7,
	 * not recommended to rely upon this.
	 */
	if ( empty($url) ) {
		$url = get_the_guid( $post->ID );
	}

	// On SSL front-end, URLs should be HTTPS.
	if ( is_ssl() && ! is_admin() && 'wp-login.php' !== $GLOBALS['pagenow'] ) {
		$url = set_url_scheme( $url );
	}

	/**
	 * Filter the attachment URL.
	 *
	 * @since 2.1.0
	 *
	 * @param string $url     URL for the given attachment.
	 * @param int    $post_id Attachment ID.
	 */
	$url = apply_filters( 'wp_get_attachment_url', $url, $post->ID );

	if ( empty( $url ) )
		return false;

	return $url;
}

/**
 * Retrieve thumbnail for an attachment.
 *
 * @since 2.1.0
 *
 * @param int $post_id Optional. Attachment ID. Default 0.
 * @return mixed False on failure. Thumbnail file path on success.
 */
function wp_get_attachment_thumb_file( $post_id = 0 ) {
	$post_id = (int) $post_id;
	if ( !$post = get_post( $post_id ) )
		return false;
	if ( !is_array( $imagedata = wp_get_attachment_metadata( $post->ID ) ) )
		return false;

	$file = get_attached_file( $post->ID );

	if ( !empty($imagedata['thumb']) && ($thumbfile = str_replace(basename($file), $imagedata['thumb'], $file)) && file_exists($thumbfile) ) {
		/**
		 * Filter the attachment thumbnail file path.
		 *
		 * @since 2.1.0
		 *
		 * @param string $thumbfile File path to the attachment thumbnail.
		 * @param int    $post_id   Attachment ID.
		 */
		return apply_filters( 'wp_get_attachment_thumb_file', $thumbfile, $post->ID );
	}
	return false;
}

/**
 * Retrieve URL for an attachment thumbnail.
 *
 * @since 2.1.0
 *
 * @param int $post_id Optional. Attachment ID. Default 0.
 * @return string|bool False on failure. Thumbnail URL on success.
 */
function wp_get_attachment_thumb_url( $post_id = 0 ) {
	$post_id = (int) $post_id;
	if ( !$post = get_post( $post_id ) )
		return false;
	if ( !$url = wp_get_attachment_url( $post->ID ) )
		return false;

	$sized = image_downsize( $post_id, 'thumbnail' );
	if ( $sized )
		return $sized[0];

	if ( !$thumb = wp_get_attachment_thumb_file( $post->ID ) )
		return false;

	$url = str_replace(basename($url), basename($thumb), $url);

	/**
	 * Filter the attachment thumbnail URL.
	 *
	 * @since 2.1.0
	 *
	 * @param string $url     URL for the attachment thumbnail.
	 * @param int    $post_id Attachment ID.
	 */
	return apply_filters( 'wp_get_attachment_thumb_url', $url, $post->ID );
}

/**
 * Verifies an attachment is of a given type.
 *
 * @since 4.2.0
 *
 * @param string      $type    Attachment type. Accepts 'image', 'audio', or 'video'.
 * @param int|WP_Post $post_id Optional. Attachment ID. Default 0.
 * @return bool True if one of the accepted types, false otherwise.
 */
function wp_attachment_is( $type, $post_id = 0 ) {
	if ( ! $post = get_post( $post_id ) ) {
		return false;
	}

	if ( ! $file = get_attached_file( $post->ID ) ) {
		return false;
	}

	if ( 0 === strpos( $post->post_mime_type, $type . '/' ) ) {
		return true;
	}

	$check = wp_check_filetype( $file );
	if ( empty( $check['ext'] ) ) {
		return false;
	}

	$ext = $check['ext'];

	if ( 'import' !== $post->post_mime_type ) {
		return $type === $ext;
	}

	switch ( $type ) {
	case 'image':
		$image_exts = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png' );
		return in_array( $ext, $image_exts );

	case 'audio':
		return in_array( $ext, wp_get_audio_extensions() );

	case 'video':
		return in_array( $ext, wp_get_video_extensions() );

	default:
		return $type === $ext;
	}
}

/**
 * Checks if the attachment is an image.
 *
 * @since 2.1.0
 * @since 4.2.0 Modified into wrapper for wp_attachment_is()
 *
 * @param int|WP_Post $post Optional. Attachment ID. Default 0.
 * @return bool Whether the attachment is an image.
 */
function wp_attachment_is_image( $post = 0 ) {
	return wp_attachment_is( 'image', $post );
}

/**
 * Retrieve the icon for a MIME type.
 *
 * @since 2.1.0
 *
 * @param string|int $mime MIME type or attachment ID.
 * @return string|bool Icon, false otherwise.
 */
function wp_mime_type_icon( $mime = 0 ) {
	if ( !is_numeric($mime) )
		$icon = wp_cache_get("mime_type_icon_$mime");

	$post_id = 0;
	if ( empty($icon) ) {
		$post_mimes = array();
		if ( is_numeric($mime) ) {
			$mime = (int) $mime;
			if ( $post = get_post( $mime ) ) {
				$post_id = (int) $post->ID;
				$ext = preg_replace('/^.+?\.([^.]+)$/', '$1', $post->guid);
				if ( !empty($ext) ) {
					$post_mimes[] = $ext;
					if ( $ext_type = wp_ext2type( $ext ) )
						$post_mimes[] = $ext_type;
				}
				$mime = $post->post_mime_type;
			} else {
				$mime = 0;
			}
		} else {
			$post_mimes[] = $mime;
		}

		$icon_files = wp_cache_get('icon_files');

		if ( !is_array($icon_files) ) {
			/**
			 * Filter the icon directory path.
			 *
			 * @since 2.0.0
			 *
			 * @param string $path Icon directory absolute path.
			 */
			$icon_dir = apply_filters( 'icon_dir', ABSPATH . WPINC . '/images/media' );

			/**
			 * Filter the icon directory URI.
			 *
			 * @since 2.0.0
			 *
			 * @param string $uri Icon directory URI.
			 */
			$icon_dir_uri = apply_filters( 'icon_dir_uri', includes_url( 'images/media' ) );

			/**
			 * Filter the list of icon directory URIs.
			 *
			 * @since 2.5.0
			 *
			 * @param array $uris List of icon directory URIs.
			 */
			$dirs = apply_filters( 'icon_dirs', array( $icon_dir => $icon_dir_uri ) );
			$icon_files = array();
			while ( $dirs ) {
				$keys = array_keys( $dirs );
				$dir = array_shift( $keys );
				$uri = array_shift($dirs);
				if ( $dh = opendir($dir) ) {
					while ( false !== $file = readdir($dh) ) {
						$file = basename($file);
						if ( substr($file, 0, 1) == '.' )
							continue;
						if ( !in_array(strtolower(substr($file, -4)), array('.png', '.gif', '.jpg') ) ) {
							if ( is_dir("$dir/$file") )
								$dirs["$dir/$file"] = "$uri/$file";
							continue;
						}
						$icon_files["$dir/$file"] = "$uri/$file";
					}
					closedir($dh);
				}
			}
			wp_cache_add( 'icon_files', $icon_files, 'default', 600 );
		}

		$types = array();
		// Icon basename - extension = MIME wildcard.
		foreach ( $icon_files as $file => $uri )
			$types[ preg_replace('/^([^.]*).*$/', '$1', basename($file)) ] =& $icon_files[$file];

		if ( ! empty($mime) ) {
			$post_mimes[] = substr($mime, 0, strpos($mime, '/'));
			$post_mimes[] = substr($mime, strpos($mime, '/') + 1);
			$post_mimes[] = str_replace('/', '_', $mime);
		}

		$matches = wp_match_mime_types(array_keys($types), $post_mimes);
		$matches['default'] = array('default');

		foreach ( $matches as $match => $wilds ) {
			if ( isset($types[$wilds[0]])) {
				$icon = $types[$wilds[0]];
				if ( !is_numeric($mime) )
					wp_cache_add("mime_type_icon_$mime", $icon);
				break;
			}
		}
	}

	/**
	 * Filter the mime type icon.
	 *
	 * @since 2.1.0
	 *
	 * @param string $icon    Path to the mime type icon.
	 * @param string $mime    Mime type.
	 * @param int    $post_id Attachment ID. Will equal 0 if the function passed
	 *                        the mime type.
	 */
	return apply_filters( 'wp_mime_type_icon', $icon, $mime, $post_id );
}

/**
 * Check for changed slugs for published post objects and save the old slug.
 *
 * The function is used when a post object of any type is updated,
 * by comparing the current and previous post objects.
 *
 * If the slug was changed and not already part of the old slugs then it will be
 * added to the post meta field ('_wp_old_slug') for storing old slugs for that
 * post.
 *
 * The most logically usage of this function is redirecting changed post objects, so
 * that those that linked to an changed post will be redirected to the new post.
 *
 * @since 2.1.0
 *
 * @param int     $post_id     Post ID.
 * @param WP_Post $post        The Post Object
 * @param WP_Post $post_before The Previous Post Object
 * @return int Same as $post_id
 */
function wp_check_for_changed_slugs( $post_id, $post, $post_before ) {
	// Don't bother if it hasnt changed.
	if ( $post->post_name == $post_before->post_name )
		return;

	// We're only concerned with published, non-hierarchical objects.
	if ( $post->post_status != 'publish' || is_post_type_hierarchical( $post->post_type ) )
		return;

	$old_slugs = (array) get_post_meta($post_id, '_wp_old_slug');

	// If we haven't added this old slug before, add it now.
	if ( !empty( $post_before->post_name ) && !in_array($post_before->post_name, $old_slugs) )
		add_post_meta($post_id, '_wp_old_slug', $post_before->post_name);

	// If the new slug was used previously, delete it from the list.
	if ( in_array($post->post_name, $old_slugs) )
		delete_post_meta($post_id, '_wp_old_slug', $post->post_name);
}

/**
 * Retrieve the private post SQL based on capability.
 *
 * This function provides a standardized way to appropriately select on the
 * post_status of a post type. The function will return a piece of SQL code
 * that can be added to a WHERE clause; this SQL is constructed to allow all
 * published posts, and all private posts to which the user has access.
 *
 * @since 2.2.0
 *
 * @param string $post_type Post type. Currently only supports 'post' or 'page'.
 * @return string SQL code that can be added to a where clause.
 */
function get_private_posts_cap_sql( $post_type ) {
	return get_posts_by_author_sql( $post_type, false );
}

/**
 * Retrieve the post SQL based on capability, author, and type.
 *
 * @since 3.0.0
 *
 * @see get_private_posts_cap_sql()
 *
 * @param string $post_type   Post type.
 * @param bool   $full        Optional. Returns a full WHERE statement instead of just
 *                            an 'andalso' term. Default true.
 * @param int    $post_author Optional. Query posts having a single author ID. Default null.
 * @param bool   $public_only Optional. Only return public posts. Skips cap checks for
 *                            $current_user.  Default false.
 * @return string SQL WHERE code that can be added to a query.
 */
function get_posts_by_author_sql( $post_type, $full = true, $post_author = null, $public_only = false ) {
	global $wpdb;

	// Private posts.
	$post_type_obj = get_post_type_object( $post_type );
	if ( ! $post_type_obj )
		return $full ? 'WHERE 1 = 0' : ' 1 = 0 ';

	/**
	 * Filter the capability to read private posts for a custom post type
	 * when generating SQL for getting posts by author.
	 *
	 * @since 2.2.0
	 * @deprecated 3.2.0 The hook transitioned from "somewhat useless" to "totally useless".
	 *
	 * @param string $cap Capability.
	 */
	if ( ! $cap = apply_filters( 'pub_priv_sql_capability', '' ) ) {
		$cap = $post_type_obj->cap->read_private_posts;
	}

	$sql = $wpdb->prepare( 'post_type = %s', $post_type );

	if ( null !== $post_author ) {
		$sql .= $wpdb->prepare( ' AND post_author = %d', $post_author );
	}

	// Only need to check the cap if $public_only is false.
	$post_status_sql = "post_status = 'publish'";
	if ( false === $public_only ) {
		if ( current_user_can( $cap ) ) {
			// Does the user have the capability to view private posts? Guess so.
			$post_status_sql .= " OR post_status = 'private'";
		} elseif ( is_user_logged_in() ) {
			// Users can view their own private posts.
			$id = get_current_user_id();
			if ( null === $post_author || ! $full ) {
				$post_status_sql .= " OR post_status = 'private' AND post_author = $id";
			} elseif ( $id == (int) $post_author ) {
				$post_status_sql .= " OR post_status = 'private'";
			} // else none
		} // else none
	}

	$sql .= " AND ($post_status_sql)";

	if ( $full ) {
		$sql = 'WHERE ' . $sql;
	}

	return $sql;
}

/**
 * Retrieve the date that the last post was published.
 *
 * The server timezone is the default and is the difference between GMT and
 * server time. The 'blog' value is the date when the last post was posted. The
 * 'gmt' is when the last post was posted in GMT formatted date.
 *
 * @since 0.71
 *
 * @param string $timezone The location to get the time. Accepts 'gmt', 'blog',
 *                         or 'server'. Default 'server'.
 * @return string The date of the last post.
 */
function get_lastpostdate( $timezone = 'server' ) {
	/**
	 * Filter the date the last post was published.
	 *
	 * @since 2.3.0
	 *
	 * @param string $date     Date the last post was published. Likely values are 'gmt',
	 *                         'blog', or 'server'.
	 * @param string $timezone Location to use for getting the post published date.
	 */
	return apply_filters( 'get_lastpostdate', _get_last_post_time( $timezone, 'date' ), $timezone );
}

/**
 * Get the timestamp of the last time any post was modified.
 *
 * The server timezone is the default and is the difference between GMT and
 * server time. The 'blog' value is just when the last post was modified. The
 * 'gmt' is when the last post was modified in GMT time.
 *
 * @since 1.2.0
 *
 * @param string $timezone Optional. The timezone for the timestamp. Uses the server's internal timezone.
 *                         Accepts 'server', 'blog', 'gmt'. or 'server'. 'server' uses the server's
 *                         internal timezone. 'blog' uses the `post_modified` field, which proxies
 *                         to the timezone set for the site. 'gmt' uses the `post_modified_gmt` field.
 *                         Default 'server'.
 * @return string The timestamp.
 */
function get_lastpostmodified( $timezone = 'server' ) {
	$lastpostmodified = _get_last_post_time( $timezone, 'modified' );

	$lastpostdate = get_lastpostdate($timezone);
	if ( $lastpostdate > $lastpostmodified )
		$lastpostmodified = $lastpostdate;

	/**
	 * Filter the date the last post was modified.
	 *
	 * @since 2.3.0
	 *
	 * @param string $lastpostmodified Date the last post was modified.
	 * @param string $timezone         Location to use for getting the post modified date.
	 *                                 See {@see get_lastpostmodified()} for accepted `$timezone` values.
	 */
	return apply_filters( 'get_lastpostmodified', $lastpostmodified, $timezone );
}

/**
 * Get the timestamp of the last time any post was modified or published.
 *
 * @since 3.1.0
 * @access private
 *
 * @param string $timezone The timezone for the timestamp. See {@see get_lastpostmodified()}
 *                         for information on accepted values.
 * @param string $field    Post field to check. Accepts 'date' or 'modified'.
 * @return string The timestamp.
 */
function _get_last_post_time( $timezone, $field ) {
	global $wpdb;

	if ( !in_array( $field, array( 'date', 'modified' ) ) )
		return false;

	$timezone = strtolower( $timezone );

	$key = "lastpost{$field}:$timezone";

	$date = wp_cache_get( $key, 'timeinfo' );

	if ( !$date ) {
		$add_seconds_server = date('Z');

		$post_types = get_post_types( array( 'public' => true ) );
		array_walk( $post_types, array( &$wpdb, 'escape_by_ref' ) );
		$post_types = "'" . implode( "', '", $post_types ) . "'";

		switch ( $timezone ) {
			case 'gmt':
				$date = $wpdb->get_var("SELECT post_{$field}_gmt FROM $wpdb->posts WHERE post_status = 'publish' AND post_type IN ({$post_types}) ORDER BY post_{$field}_gmt DESC LIMIT 1");
				break;
			case 'blog':
				$date = $wpdb->get_var("SELECT post_{$field} FROM $wpdb->posts WHERE post_status = 'publish' AND post_type IN ({$post_types}) ORDER BY post_{$field}_gmt DESC LIMIT 1");
				break;
			case 'server':
				$date = $wpdb->get_var("SELECT DATE_ADD(post_{$field}_gmt, INTERVAL '$add_seconds_server' SECOND) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type IN ({$post_types}) ORDER BY post_{$field}_gmt DESC LIMIT 1");
				break;
		}

		if ( $date )
			wp_cache_set( $key, $date, 'timeinfo' );
	}

	return $date;
}

/**
 * Updates posts in cache.
 *
 * @since 1.5.1
 *
 * @param array $posts Array of post objects, passed by reference.
 */
function update_post_cache( &$posts ) {
	if ( ! $posts )
		return;

	foreach ( $posts as $post )
		wp_cache_add( $post->ID, $post, 'posts' );
}

/**
 * Will clean the post in the cache.
 *
 * Cleaning means delete from the cache of the post. Will call to clean the term
 * object cache associated with the post ID.
 *
 * This function not run if $_wp_suspend_cache_invalidation is not empty. See
 * wp_suspend_cache_invalidation().
 *
 * @since 2.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int|WP_Post $post Post ID or post object to remove from the cache.
 */
function clean_post_cache( $post ) {
	global $_wp_suspend_cache_invalidation, $wpdb;

	if ( ! empty( $_wp_suspend_cache_invalidation ) )
		return;

	$post = get_post( $post );
	if ( empty( $post ) )
		return;

	wp_cache_delete( $post->ID, 'posts' );
	wp_cache_delete( $post->ID, 'post_meta' );

	clean_object_term_cache( $post->ID, $post->post_type );

	wp_cache_delete( 'wp_get_archives', 'general' );

	/**
	 * Fires immediately after the given post's cache is cleaned.
	 *
	 * @since 2.5.0
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 */
	do_action( 'clean_post_cache', $post->ID, $post );

	if ( 'page' == $post->post_type ) {
		wp_cache_delete( 'all_page_ids', 'posts' );

		/**
		 * Fires immediately after the given page's cache is cleaned.
		 *
		 * @since 2.5.0
		 *
		 * @param int $post_id Post ID.
		 */
		do_action( 'clean_page_cache', $post->ID );
	}

	wp_cache_set( 'last_changed', microtime(), 'posts' );
}

/**
 * Call major cache updating functions for list of Post objects.
 *
 * @since 1.5.0
 *
 * @param array  $posts             Array of Post objects
 * @param string $post_type         Optional. Post type. Default 'post'.
 * @param bool   $update_term_cache Optional. Whether to update the term cache. Default true.
 * @param bool   $update_meta_cache Optional. Whether to update the meta cache. Default true.
 */
function update_post_caches( &$posts, $post_type = 'post', $update_term_cache = true, $update_meta_cache = true ) {
	// No point in doing all this work if we didn't match any posts.
	if ( !$posts )
		return;

	update_post_cache($posts);

	$post_ids = array();
	foreach ( $posts as $post )
		$post_ids[] = $post->ID;

	if ( ! $post_type )
		$post_type = 'any';

	if ( $update_term_cache ) {
		if ( is_array($post_type) ) {
			$ptypes = $post_type;
		} elseif ( 'any' == $post_type ) {
			$ptypes = array();
			// Just use the post_types in the supplied posts.
			foreach ( $posts as $post ) {
				$ptypes[] = $post->post_type;
			}
			$ptypes = array_unique($ptypes);
		} else {
			$ptypes = array($post_type);
		}

		if ( ! empty($ptypes) )
			update_object_term_cache($post_ids, $ptypes);
	}

	if ( $update_meta_cache )
		update_postmeta_cache($post_ids);
}

/**
 * Updates metadata cache for list of post IDs.
 *
 * Performs SQL query to retrieve the metadata for the post IDs and updates the
 * metadata cache for the posts. Therefore, the functions, which call this
 * function, do not need to perform SQL queries on their own.
 *
 * @since 2.1.0
 *
 * @param array $post_ids List of post IDs.
 * @return bool|array Returns false if there is nothing to update or an array
 *                    of metadata.
 */
function update_postmeta_cache( $post_ids ) {
	return update_meta_cache('post', $post_ids);
}

/**
 * Will clean the attachment in the cache.
 *
 * Cleaning means delete from the cache. Optionally will clean the term
 * object cache associated with the attachment ID.
 *
 * This function will not run if $_wp_suspend_cache_invalidation is not empty.
 *
 * @since 3.0.0
 *
 * @see wp_suspend_cache_invalidation()
 *
 * @param int  $id          The attachment ID in the cache to clean.
 * @param bool $clean_terms Optional. Whether to clean terms cache. Default false.
 */
function clean_attachment_cache( $id, $clean_terms = false ) {
	global $_wp_suspend_cache_invalidation;

	if ( !empty($_wp_suspend_cache_invalidation) )
		return;

	$id = (int) $id;

	wp_cache_delete($id, 'posts');
	wp_cache_delete($id, 'post_meta');

	if ( $clean_terms )
		clean_object_term_cache($id, 'attachment');

	/**
	 * Fires after the given attachment's cache is cleaned.
	 *
	 * @since 3.0.0
	 *
	 * @param int $id Attachment ID.
	 */
	do_action( 'clean_attachment_cache', $id );
}

//
// Hooks
//

/**
 * Hook for managing future post transitions to published.
 *
 * @since 2.3.0
 * @access private
 *
 * @see wp_clear_scheduled_hook()
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string  $new_status New post status.
 * @param string  $old_status Previous post status.
 * @param WP_Post $post       Post object.
 */
function _transition_post_status( $new_status, $old_status, $post ) {
	global $wpdb;

	if ( $old_status != 'publish' && $new_status == 'publish' ) {
		// Reset GUID if transitioning to publish and it is empty.
		if ( '' == get_the_guid($post->ID) )
			$wpdb->update( $wpdb->posts, array( 'guid' => get_permalink( $post->ID ) ), array( 'ID' => $post->ID ) );

		/**
		 * Fires when a post's status is transitioned from private to published.
		 *
		 * @since 1.5.0
		 * @deprecated 2.3.0 Use 'private_to_publish' instead.
		 *
		 * @param int $post_id Post ID.
		 */
		do_action('private_to_published', $post->ID);
	}

	// If published posts changed clear the lastpostmodified cache.
	if ( 'publish' == $new_status || 'publish' == $old_status) {
		foreach ( array( 'server', 'gmt', 'blog' ) as $timezone ) {
			wp_cache_delete( "lastpostmodified:$timezone", 'timeinfo' );
			wp_cache_delete( "lastpostdate:$timezone", 'timeinfo' );
		}
	}

	if ( $new_status !== $old_status ) {
		wp_cache_delete( _count_posts_cache_key( $post->post_type ), 'counts' );
		wp_cache_delete( _count_posts_cache_key( $post->post_type, 'readable' ), 'counts' );
	}

	// Always clears the hook in case the post status bounced from future to draft.
	wp_clear_scheduled_hook('publish_future_post', array( $post->ID ) );
}

/**
 * Hook used to schedule publication for a post marked for the future.
 *
 * The $post properties used and must exist are 'ID' and 'post_date_gmt'.
 *
 * @since 2.3.0
 * @access private
 *
 * @param int     $deprecated Not used. Can be set to null. Never implemented. Not marked
 *                            as deprecated with _deprecated_argument() as it conflicts with
 *                            wp_transition_post_status() and the default filter for
 *                            {@see _future_post_hook()}.
 * @param WP_Post $post       Post object.
 */
function _future_post_hook( $deprecated, $post ) {
	wp_clear_scheduled_hook( 'publish_future_post', array( $post->ID ) );
	wp_schedule_single_event( strtotime( get_gmt_from_date( $post->post_date ) . ' GMT') , 'publish_future_post', array( $post->ID ) );
}

/**
 * Hook to schedule pings and enclosures when a post is published.
 *
 * Uses XMLRPC_REQUEST and WP_IMPORTING constants.
 *
 * @since 2.3.0
 * @access private
 *
 * @param int $post_id The ID in the database table of the post being published.
 */
function _publish_post_hook( $post_id ) {
	if ( defined( 'XMLRPC_REQUEST' ) ) {
		/**
		 * Fires when _publish_post_hook() is called during an XML-RPC request.
		 *
		 * @since 2.1.0
		 *
		 * @param int $post_id Post ID.
		 */
		do_action( 'xmlrpc_publish_post', $post_id );
	}

	if ( defined('WP_IMPORTING') )
		return;

	if ( get_option('default_pingback_flag') )
		add_post_meta( $post_id, '_pingme', '1' );
	add_post_meta( $post_id, '_encloseme', '1' );

	wp_schedule_single_event(time(), 'do_pings');
}

/**
 * Return the post's parent's post_ID
 *
 * @since 3.1.0
 *
 * @param int $post_ID
 *
 * @return int|bool Post parent ID, otherwise false.
 */
function wp_get_post_parent_id( $post_ID ) {
	$post = get_post( $post_ID );
	if ( !$post || is_wp_error( $post ) )
		return false;
	return (int) $post->post_parent;
}

/**
 * Check the given subset of the post hierarchy for hierarchy loops.
 *
 * Prevents loops from forming and breaks those that it finds. Attached
 * to the 'wp_insert_post_parent' filter.
 *
 * @since 3.1.0
 *
 * @see wp_find_hierarchy_loop()
 *
 * @param int $post_parent ID of the parent for the post we're checking.
 * @param int $post_ID     ID of the post we're checking.
 * @return int The new post_parent for the post, 0 otherwise.
 */
function wp_check_post_hierarchy_for_loops( $post_parent, $post_ID ) {
	// Nothing fancy here - bail.
	if ( !$post_parent )
		return 0;

	// New post can't cause a loop.
	if ( empty( $post_ID ) )
		return $post_parent;

	// Can't be its own parent.
	if ( $post_parent == $post_ID )
		return 0;

	// Now look for larger loops.
	if ( !$loop = wp_find_hierarchy_loop( 'wp_get_post_parent_id', $post_ID, $post_parent ) )
		return $post_parent; // No loop

	// Setting $post_parent to the given value causes a loop.
	if ( isset( $loop[$post_ID] ) )
		return 0;

	// There's a loop, but it doesn't contain $post_ID. Break the loop.
	foreach ( array_keys( $loop ) as $loop_member )
		wp_update_post( array( 'ID' => $loop_member, 'post_parent' => 0 ) );

	return $post_parent;
}

/**
 * Set a post thumbnail.
 *
 * @since 3.1.0
 *
 * @param int|WP_Post $post         Post ID or post object where thumbnail should be attached.
 * @param int         $thumbnail_id Thumbnail to attach.
 * @return bool True on success, false on failure.
 */
function set_post_thumbnail( $post, $thumbnail_id ) {
	$post = get_post( $post );
	$thumbnail_id = absint( $thumbnail_id );
	if ( $post && $thumbnail_id && get_post( $thumbnail_id ) ) {
		if ( wp_get_attachment_image( $thumbnail_id, 'thumbnail' ) )
			return update_post_meta( $post->ID, '_thumbnail_id', $thumbnail_id );
		else
			return delete_post_meta( $post->ID, '_thumbnail_id' );
	}
	return false;
}

/**
 * Remove a post thumbnail.
 *
 * @since 3.3.0
 *
 * @param int|WP_Post $post Post ID or post object where thumbnail should be removed from.
 * @return bool True on success, false on failure.
 */
function delete_post_thumbnail( $post ) {
	$post = get_post( $post );
	if ( $post )
		return delete_post_meta( $post->ID, '_thumbnail_id' );
	return false;
}

/**
 * Delete auto-drafts for new posts that are > 7 days old.
 *
 * @since 3.4.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 */
function wp_delete_auto_drafts() {
	global $wpdb;

	// Cleanup old auto-drafts more than 7 days old.
	$old_posts = $wpdb->get_col( "SELECT ID FROM $wpdb->posts WHERE post_status = 'auto-draft' AND DATE_SUB( NOW(), INTERVAL 7 DAY ) > post_date" );
	foreach ( (array) $old_posts as $delete ) {
		// Force delete.
		wp_delete_post( $delete, true );
	}
}

/**
 * Update the custom taxonomies' term counts when a post's status is changed.
 *
 * For example, default posts term counts (for custom taxonomies) don't include
 * private / draft posts.
 *
 * @since 3.3.0
 * @access private
 *
 * @param string  $new_status New post status.
 * @param string  $old_status Old post status.
 * @param WP_Post $post       Post object.
 */
function _update_term_count_on_transition_post_status( $new_status, $old_status, $post ) {
	// Update counts for the post's terms.
	foreach ( (array) get_object_taxonomies( $post->post_type ) as $taxonomy ) {
		$tt_ids = wp_get_object_terms( $post->ID, $taxonomy, array( 'fields' => 'tt_ids' ) );
		wp_update_term_count( $tt_ids, $taxonomy );
	}
}

/**
 * Adds any posts from the given ids to the cache that do not already exist in cache
 *
 * @since 3.4.0
 * @access private
 *
 * @see update_post_caches()
 *
 * @param array $ids               ID list
 * @param bool  $update_term_cache Optional. Whether to update the term cache. Default true.
 * @param bool  $update_meta_cache Optional. Whether to update the meta cache. Default true.
 */
function _prime_post_caches( $ids, $update_term_cache = true, $update_meta_cache = true ) {
	global $wpdb;

	$non_cached_ids = _get_non_cached_ids( $ids, 'posts' );
	if ( !empty( $non_cached_ids ) ) {
		$fresh_posts = $wpdb->get_results( sprintf( "SELECT $wpdb->posts.* FROM $wpdb->posts WHERE ID IN (%s)", join( ",", $non_cached_ids ) ) );

		update_post_caches( $fresh_posts, 'any', $update_term_cache, $update_meta_cache );
	}
}
