<?php
/**
 * Registers the actions of WordPress core through the entity actions API, like a
 * plugin registers its own.
 *
 * The folders next to this file are the script modules of the actions:
 * `post_type_supports` for the actions of every post type, `page` for the
 * actions of pages, `wp_block` for the actions of patterns, and
 * `wp_template_part` for the actions of template parts. Each action has a
 * folder named after its id, whose `action.php` returns the serializable
 * part of the action, and whose `action.tsx` holds its JavaScript parts.
 *
 * @package WordPress
 */

/**
 * Registers the actions of WordPress core on each post type exposed in the
 * REST API, from what it supports and what the current user can do:
 *
 * - `view-post`, for the viewable post types.
 * - `view-post-revisions`, for the post types supporting `revisions`.
 * - `duplicate-post`, for the post types the user can create, except
 *   patterns, templates, and template parts. Only in the Gutenberg plugin.
 * - `duplicate-template-part`, for template parts, when the user can create
 *   them and the theme is a block theme.
 * - `duplicate-pattern`, for patterns, when the user can create them.
 * - `rename-post`, for the post types supporting `title`.
 * - `order-pages`, for the post types supporting `page-attributes`.
 * - `export-pattern`, for patterns.
 * - `restore`, `reset-post`, `delete-post`, `move-to-trash`, and
 *   `permanently-delete`, for every post type: each one checks which posts
 *   it applies to.
 * - `set-as-homepage` and `set-as-posts-page`, for pages, when the user can
 *   manage the site options and the theme has no `front-page` template:
 *   each one applies to the pages with its `action-*` REST link.
 *
 * The actions are registered in that order, which is the order they are
 * shown in, each with the script module of its folder. The registry is built
 * for each request, so the actions only depending on the user are registered
 * for the current one.
 *
 * Hooked at priority 0, so a plugin hooking `wp_entity_actions_api_init` at the
 * default priority sees the core actions registered, and can unregister or
 * replace them.
 *
 * @since 7.2.0
 */
function register_core_entity_actions() {
	$is_gutenberg   = defined( 'IS_GUTENBERG_PLUGIN' ) && IS_GUTENBERG_PLUGIN;
	$is_block_theme = wp_is_block_theme();
	$design_types   = array( 'wp_block', 'wp_template', 'wp_template_part' );
	$definitions    = array();

	// Each file is required in its own variable scope.
	$require = static function ( $file ) {
		return require $file;
	};

	foreach ( get_post_types( array( 'show_in_rest' => true ), 'objects' ) as $post_type ) {
		$name       = $post_type->name;
		$can_create = current_user_can( $post_type->cap->create_posts );

		// The homepage and the posts page are site settings. When the theme
		// has a `front-page` template, the front page cannot be changed, see
		// https://developer.wordpress.org/themes/basics/template-hierarchy/.
		$can_set_homepage = 'page' === $name &&
			current_user_can( 'manage_options' ) &&
			empty( get_block_templates( array( 'slug__in' => array( 'front-page' ) ) ) );

		// The actions of the post type, keyed by the folder of their script
		// module and their id, in the order they are shown.
		$applies = array(
			'post_type_supports/view-post'             => is_post_type_viewable( $post_type ),
			'post_type_supports/view-post-revisions'   => post_type_supports( $name, 'revisions' ),
			'post_type_supports/duplicate-post'        => $is_gutenberg && $can_create && ! in_array( $name, $design_types, true ),
			'wp_template_part/duplicate-template-part' => 'wp_template_part' === $name && $can_create && $is_block_theme,
			'wp_block/duplicate-pattern'               => 'wp_block' === $name && $can_create,
			'post_type_supports/rename-post'           => post_type_supports( $name, 'title' ),
			'post_type_supports/order-pages'           => post_type_supports( $name, 'page-attributes' ),
			'wp_block/export-pattern'                  => 'wp_block' === $name,
			'post_type_supports/restore'               => true,
			'post_type_supports/reset-post'            => true,
			'post_type_supports/delete-post'           => true,
			'post_type_supports/move-to-trash'         => true,
			'post_type_supports/permanently-delete'    => true,
			'page/set-as-homepage'                     => $can_set_homepage,
			'page/set-as-posts-page'                   => $can_set_homepage,
		);

		foreach ( array_keys( array_filter( $applies ) ) as $key ) {
			list( $module, $id ) = explode( '/', $key );
			if ( ! isset( $definitions[ $key ] ) ) {
				// The id comes first, like in the actions registered directly.
				$definitions[ $key ] = array_merge( array( 'id' => $id ), $require( __DIR__ . "/{$key}/action.php" ) );
			}
			wp_register_entity_actions( 'core', 'postType', $name, array( $definitions[ $key ] ), "@wordpress/core-actions/{$module}" );
		}
	}
}
add_action( 'wp_entity_actions_api_init', 'register_core_entity_actions', 0 );
