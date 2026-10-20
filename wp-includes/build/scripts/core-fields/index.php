<?php
/**
 * Registers the fields of WordPress core through the Entity fields API, like a
 * plugin registers its own.
 *
 * The default fields every post type derives from its supports are
 * registered in code, see wp_register_core_post_type_supports_fields(). Their
 * definitions are the `field.php` files of the `post_type_supports` folder. The
 * post types whose fields differ from the defaults unregister the ones they
 * do not get, as a plugin would, see wp_register_core_field_collections().
 *
 * The fields of a single entity, a post type or the site, are declarative
 * collections, the other folders next to this file, see
 * wp_register_entity_field_collection() for their format.
 *
 * @package WordPress
 */

/**
 * Registers the default fields of each post type exposed in the REST API,
 * from what it supports, or whatever it supports:
 *
 * - `author`, for the post types supporting `author`.
 * - `comment_status`, for the post types supporting `comments`.
 * - `date`, for every post type but the design ones.
 * - `discussion`, for the post types supporting `comments` or `trackbacks`.
 * - `excerpt`, for the post types supporting `excerpt`.
 * - `featured_media`, for the post types supporting `thumbnail` when the
 *   theme supports post thumbnails for them.
 * - `format`, for the post types supporting `post-formats` when the theme
 *   supports post formats.
 * - `last_edited_date`, for every post type.
 * - `notesCount`, for the post types whose `editor` support has the `notes`
 *   argument.
 * - `parent`, for the post types supporting `page-attributes`.
 * - `password`, for every post type but the design ones.
 * - `ping_status`, for the post types supporting `trackbacks`.
 * - `post-content-info`, for the post types supporting `editor`.
 * - `scheduled_date`, for every post type but the design ones.
 * - `slug`, for the viewable post types but the design ones.
 * - `status`, for every post type but the design ones.
 * - `sticky`, for posts, the only post type with sticky posts.
 * - `template`, for every post type but the design ones.
 * - `title`, for the post types supporting `title`.
 *
 * It makes no exception for any post type: the fields a post type does not
 * get are unregistered afterwards, see wp_register_core_field_collections().
 *
 * Only some fields have JavaScript parts, but every field is registered
 * with the script module of the folder: a post type supporting only
 * comments or notes lists the module too, which is small.
 *
 * @since 7.2.0
 */
function wp_register_core_post_type_supports_fields() {
	$definitions = wp_get_entity_field_collection_fields( __DIR__ . '/post_type_supports' );

	foreach ( get_post_types( array( 'show_in_rest' => true ) ) as $post_type ) {
		// WordPress stores the arguments of a support as a list of argument
		// arrays, and a support without arguments as `true`.
		$editor = get_all_post_type_supports( $post_type )['editor'] ?? null;

		$applies = array(
			'author'            => post_type_supports( $post_type, 'author' ),
			'comment_status'    => post_type_supports( $post_type, 'comments' ),
			'date'              => true,
			'discussion'        => post_type_supports( $post_type, 'comments' ) || post_type_supports( $post_type, 'trackbacks' ),
			'excerpt'           => post_type_supports( $post_type, 'excerpt' ),
			'featured_media'    => post_type_supports( $post_type, 'thumbnail' ) && current_theme_supports( 'post-thumbnails', $post_type ),
			'format'            => post_type_supports( $post_type, 'post-formats' ) && current_theme_supports( 'post-formats' ),
			'last_edited_date'  => true,
			'notesCount'        => is_array( $editor ) && (bool) array_filter( array_column( $editor, 'notes' ) ),
			'parent'            => post_type_supports( $post_type, 'page-attributes' ),
			'password'          => true,
			'ping_status'       => post_type_supports( $post_type, 'trackbacks' ),
			'post-content-info' => post_type_supports( $post_type, 'editor' ),
			'scheduled_date'    => true,
			'slug'              => is_post_type_viewable( $post_type ),
			'status'            => true,
			'sticky'            => 'post' === $post_type,
			'template'          => true,
			'title'             => post_type_supports( $post_type, 'title' ),
		);

		// Keeps the alphabetical order of the folders.
		$fields = array_values( array_intersect_key( $definitions, array_filter( $applies ) ) );
		if ( $fields ) {
			wp_register_entity_fields( 'core', 'postType', $post_type, $fields, '@wordpress/core-fields/post_type_supports' );
		}
	}
}

/**
 * Registers the fields of WordPress core: the defaults first, without the
 * ones the core post types do not get, then the collections of single post
 * types, then the collection of the site.
 *
 * Hooked at priority 0, so a plugin hooking `wp_entity_fields_api_init` at the
 * default priority sees the core fields registered, and can update or
 * unregister them.
 *
 * @since 7.2.0
 */
function wp_register_core_field_collections() {
	// Defaults registered for all post types based on their supports.
	wp_register_core_post_type_supports_fields();

	// page: unregister unwanted default fields, and register its own.
	wp_unregister_entity_fields( 'postType', 'page', array( 'title' ) );
	wp_register_entity_field_collection( __DIR__ . '/page' );

	// wp_template: unregister unwanted default fields, and register its own.
	wp_unregister_entity_fields( 'postType', 'wp_template', array( 'author', 'date', 'excerpt', 'password', 'post-content-info', 'scheduled_date', 'slug', 'status', 'template', 'title' ) );
	wp_register_entity_field_collection( __DIR__ . '/wp_template' );

	// wp_template_part: unregister unwanted default fields, and register its own.
	wp_unregister_entity_fields( 'postType', 'wp_template_part', array( 'author', 'date', 'excerpt', 'password', 'post-content-info', 'scheduled_date', 'slug', 'status', 'template', 'title' ) );
	wp_register_entity_field_collection( __DIR__ . '/wp_template_part' );

	// wp_block: unregister unwanted default fields, and register its own.
	wp_unregister_entity_fields( 'postType', 'wp_block', array( 'date', 'excerpt', 'password', 'scheduled_date', 'slug', 'status', 'template', 'title' ) );
	wp_register_entity_field_collection( __DIR__ . '/wp_block' );
	// Patterns are sorted client-side, where the author's ID isn't a useful
	// order, like the template authors.
	if ( post_type_supports( 'wp_block', 'author' ) ) {
		wp_update_entity_fields(
			'core',
			'postType',
			'wp_block',
			array(
				array(
					'id'            => 'author',
					'enableSorting' => false,
				),
			)
		);
	}

	// attachment: unregister all default fields, and register its own.
	wp_unregister_entity_fields( 'postType', 'attachment' );
	wp_register_entity_field_collection( __DIR__ . '/attachment' );

	// wp_navigation: unregister unwanted default fields.
	wp_unregister_entity_fields( 'postType', 'wp_navigation', array( 'date', 'password', 'post-content-info', 'scheduled_date', 'slug', 'status', 'template' ) );

	// Register fields for root/site (entity/kind).
	wp_register_entity_field_collection( __DIR__ . '/root_site' );
}
add_action( 'wp_entity_fields_api_init', 'wp_register_core_field_collections', 0 );
