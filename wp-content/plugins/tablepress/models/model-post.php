<?php
/**
 * Post Model
 *
 * @package TablePress
 * @subpackage Models
 * @author Tobias Bäthge
 * @since 1.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Post Model class
 * @package TablePress
 * @subpackage Models
 * @author Tobias Bäthge
 * @since 1.0.0
 */
class TablePress_Post_Model extends TablePress_Model {

	/**
	 * Name of the "Custom Post Type" for the tables.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $post_type = 'tablepress_table';

	/**
	 * Init the model by registering the Custom Post Type.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->_register_post_type(); // we are on WP "init" hook already
	}

	/**
	 * Register the Custom Post Type which the tables use.
	 *
	 * @since 1.0.0
	 */
	protected function _register_post_type() {
		/**
		 * Filter the "Custom Post Type" that TablePress uses for storing tables in the database.
		 *
		 * @since 1.0.0
		 *
		 * @param string $post_type The "Custom Post Type" that TablePress uses.
		 */
		$this->post_type = apply_filters( 'tablepress_post_type', $this->post_type );
		$post_type_args = array(
			'labels'          => array(
				'name' => 'TablePress Tables',
			),
			'public'          => false,
			'show_ui'         => false,
			'query_var'       => false,
			'rewrite'         => false,
			'capability_type' => 'tablepress_table', // This ensures, that WP's regular CPT UI respects our capabilities.
			'map_meta_cap'    => false, // Integrated WP mapping does not fit our needs, therefore use our own in a filter.
			'supports'        => array( 'title', 'editor', 'excerpt', 'revisions' ),
			'can_export'      => true,
		);
		/**
		 * Filter the arguments for the registration of the "Custom Post Type" that TablePress uses.
		 *
		 * @since 1.0.0
		 *
		 * @param array $post_type_args Arguments for the registration of the TablePress "Custom Post Type".
		 */
		$post_type_args = apply_filters( 'tablepress_post_type_args', $post_type_args );
		register_post_type( $this->post_type, $post_type_args );
	}

	/**
	 * Insert a post with the correct Custom Post Type and default values in the the wp_posts table in the database.
	 *
	 * @since 1.0.0
	 *
	 * @param array $post Post to insert.
	 * @return int|WP_Error Post ID of the inserted post on success, WP_Error on error.
	 */
	public function insert( array $post ) {
		$default_post = array(
			'ID'             => false, // false on new insert, but existing post ID on update
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_category'  => false,
			'post_content'   => '',
			'post_excerpt'   => '',
			'post_parent'    => 0,
			'post_password'  => '',
			'post_status'    => 'publish',
			'post_title'     => '',
			'post_type'      => $this->post_type,
			'tags_input'     => '',
			'to_ping'        => '',
		);
		$post = array_merge( $default_post, $post );
		// WP expects everything to be slashed.
		$post = wp_slash( $post );

		// Remove balanceTags() from sanitize_post(), as it can destroy the JSON when messing with HTML.
		remove_filter( 'content_save_pre', 'balanceTags', 50 );
		remove_filter( 'excerpt_save_pre', 'balanceTags', 50 );
		/*
		 * Remove possible KSES filtering here, as it can destroy the JSON when messing with HTML.
		 * KSES filtering is done to table cells individually, when saving.
		 */
		remove_filter( 'content_save_pre', 'wp_filter_post_kses' );

		$post_id = wp_insert_post( $post, true );

		// Re-add balanceTags() to sanitize_post().
		add_filter( 'content_save_pre', 'balanceTags', 50 );
		add_filter( 'excerpt_save_pre', 'balanceTags', 50 );
		// Re-add KSES filtering, if necessary.
		if ( ! current_user_can( 'unfiltered_html' ) ) {
			add_filter( 'content_save_pre', 'wp_filter_post_kses' );
		}

		return $post_id;
	}

	/**
	 * Update an existing post with the correct Custom Post Type and default values in the the wp_posts table in the database.
	 *
	 * @since 1.0.0
	 *
	 * @param array $post Post.
	 * @return int|WP_Error Post ID of the updated post on success, WP_Error on error.
	 */
	public function update( array $post ) {
		$default_post = array(
			'ID'             => false, // false on new insert, but existing post ID on update
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_category'  => false,
			'post_content'   => '',
			'post_excerpt'   => '',
			'post_parent'    => 0,
			'post_password'  => '',
			'post_status'    => 'publish',
			'post_title'     => '',
			'post_type'      => $this->post_type,
			'tags_input'     => '',
			'to_ping'        => '',
		);
		$post = array_merge( $default_post, $post );
		// WP expects everything to be slashed.
		$post = wp_slash( $post );

		// Remove balanceTags() from sanitize_post(), as it can destroy the JSON when messing with HTML
		remove_filter( 'content_save_pre', 'balanceTags', 50 );
		remove_filter( 'excerpt_save_pre', 'balanceTags', 50 );
		/*
		 * Remove possible KSES filtering here, as it can destroy the JSON when messing with HTML
		 * KSES filtering is done to table cells individually, when saving.
		 */
		remove_filter( 'content_save_pre', 'wp_filter_post_kses' );

		$post_id = wp_update_post( $post, true );

		// Re-add balanceTags() to sanitize_post().
		add_filter( 'content_save_pre', 'balanceTags', 50 );
		add_filter( 'excerpt_save_pre', 'balanceTags', 50 );
		// Re-add KSES filtering, if necessary.
		if ( ! current_user_can( 'unfiltered_html' ) ) {
			add_filter( 'content_save_pre', 'wp_filter_post_kses' );
		}

		return $post_id;
	}

	/**
	 * Get a post from the wp_posts table in the database.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID.
	 * @return WP_Post|bool Post on success, false on error.
	 */
	public function get( $post_id ) {
		$post = get_post( $post_id );
		if ( is_null( $post ) ) {
			return false;
		}
		return $post;
	}

	/**
	 * Delete a post (and all revisions) from the wp_posts table in the database.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID.
	 * @return mixed|bool Post on success, false on error.
	 */
	public function delete( $post_id ) {
		return wp_delete_post( $post_id, true ); // true means force delete, although for CPTs this is automatic in this function
	}

	/**
	 * Move a post to the trash (if trash is globally enabled), instead of directly deleting the post.
	 * (yet unused)
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID.
	 * @return mixed|bool Post on success, false on error.
	 */
	public function trash( $post_id ) {
		return wp_trash_post( $post_id );
	}

	/**
	 * Restore a post from the trash.
	 * (yet unused)
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID.
	 * @return array|bool Post on success, false on error.
	 */
	public function untrash( $post_id ) {
		return wp_untrash_post( $post_id );
	}

	/**
	 * Load all posts with one query, to prime the cache.
	 *
	 * @since 1.0.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 * @see get_post()
	 *
	 * @param array $all_post_ids      List of Post IDs.
	 * @param bool  $update_meta_cache Optional. Whether to update the Post Meta Cache (for table options and visibility).
	 */
	public function load_posts( array $all_post_ids, $update_meta_cache = true ) {
		global $wpdb;

		// Split post loading, to save memory.
		$offset = 0;
		$length = 100; // 100 posts at a time
		$number_of_posts = count( $all_post_ids );
		while ( $offset < $number_of_posts ) {
			$post_ids = array_slice( $all_post_ids, $offset, $length );
			// Don't load posts that are in the cache already.
			$post_ids = _get_non_cached_ids( $post_ids, 'posts' );
			if ( ! empty( $post_ids ) ) {
				$post_ids_list = implode( ',', $post_ids );
				$posts = $wpdb->get_results( "SELECT {$wpdb->posts}.* FROM {$wpdb->posts} WHERE ID IN ({$post_ids_list})" );
				update_post_cache( $posts );
				if ( $update_meta_cache ) {
					// Get all post meta data for all table posts, @see get_post_meta().
					update_meta_cache( 'post', $post_ids );
				}
			}
			$offset += $length; // next array_slice() $offset
		}
	}

	/**
	 * Count the number of posts with the model's CPT in the wp_posts table in the database.
	 * (currently for debug only)
	 *
	 * @since 1.0.0
	 *
	 * @return int Number of posts.
	 */
	public function count_posts() {
		return array_sum( (array) wp_count_posts( $this->post_type ) ); // original return value is object with the counts for each post_status
	}

	/**
	 * Add a post meta field to a post.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $post_id ID of the post for which the field shall be added.
	 * @param string $field   Name of the post meta field.
	 * @param string $value   Value of the post meta field (not slashed).
	 * @return bool True on success, false on error.
	 */
	public function add_meta_field( $post_id, $field, $value ) {
		// WP expects a slashed value.
		$value = wp_slash( $value );
		$success = add_post_meta( $post_id, $field, $value, true ); // true means unique
		// Make sure that $success is a boolean, as add_post_meta() returns an ID or false.
		$success = ( false === $success ) ? false : true;
		return $success;
	}

	/**
	 * Update the value of a post meta field of a post.
	 *
	 * If the field does not yet exist, it is added.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $post_id ID of the post for which the field shall be updated.
	 * @param string $field   Name of the post meta field.
	 * @param string $value   Value of the post meta field (not slashed).
	 * @return bool True on success, false on error.
	 */
	public function update_meta_field( $post_id, $field, $value ) {
		$prev_value = (string) get_post_meta( $post_id, $field, true );
		// No need to update, if values are equal (also, update_post_meta() would return false for this).
		if ( $prev_value === $value ) {
			return true;
		}

		// WP expects a slashed value.
		$value = wp_slash( $value );
		return update_post_meta( $post_id, $field, $value, $prev_value );
	}

	/**
	 * Get the value of a post meta field of a post.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $post_id ID of the post for which the field shall be retrieved.
	 * @param string $field   Name of the post meta field.
	 * @return string Value of the meta field.
	 */
	public function get_meta_field( $post_id, $field ) {
		return get_post_meta( $post_id, $field, true ); // true means single value
	}

	/**
	 * Delete a post meta field of a post.
	 * (yet unused)
	 *
	 * @since 1.0.0
	 *
	 * @param int    $post_id ID of the post of which the field shall be deleted.
	 * @param string $field   Name of the post meta field.
	 * @return bool True on success, false on error.
	 */
	public function delete_meta_field( $post_id, $field ) {
		return delete_post_meta( $post_id, $field, true ); // true means single value
	}

	/**
	 * Return the Custom Post Type that TablePress uses.
	 *
	 * @since 1.5.0
	 *
	 * @return string The used Custom Post Type.
	 */
	public function get_post_type() {
		return $this->post_type;
	}

} // class TablePress_Post_Model
