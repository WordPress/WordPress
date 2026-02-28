<?php

/**
 * Preload necessary resources for the editors.
 *
 * @param array                   $paths   REST API paths to preload.
 * @param WP_Block_Editor_Context $context Current block editor context
 *
 * @return array Filtered preload paths.
 */
function gutenberg_block_editor_preload_paths_6_8( $paths, $context ) {
	if ( 'core/edit-site' === $context->name ) {
		$post = null;
		if ( isset( $_GET['postId'] ) && is_numeric( $_GET['postId'] ) ) {
			$post = get_post( (int) $_GET['postId'] );
		}
		if ( isset( $_GET['p'] ) && preg_match( '/^\/page\/(\d+)$/', $_GET['p'], $matches ) ) {
			$post = get_post( (int) $matches[1] );
		}

		if ( $post ) {
			$route_for_post = rest_get_route_for_post( $post );
			if ( $route_for_post ) {
				$paths[] = add_query_arg( 'context', 'edit', $route_for_post );
				$paths[] = add_query_arg( 'context', 'edit', '/wp/v2/types/' . $post->post_type );
				if ( 'page' === $post->post_type ) {
					$paths[] = add_query_arg(
						'slug',
						// @see https://github.com/WordPress/gutenberg/blob/e093fefd041eb6cc4a4e7f67b92ab54fd75c8858/packages/core-data/src/private-selectors.ts#L244-L254
						empty( $post->post_name ) ? 'page' : 'page-' . $post->post_name,
						'/wp/v2/templates/lookup'
					);
				}
			}
		}

		$paths[] = '/wp/v2/settings';
		$paths[] = array( '/wp/v2/settings', 'OPTIONS' );
		$paths[] = '/wp/v2/templates/lookup?slug=front-page';
		$paths[] = '/wp/v2/templates/lookup?slug=home';
	}

	// Preload theme and global styles paths.
	if ( 'core/edit-site' === $context->name || 'core/edit-post' === $context->name ) {
		$global_styles_id = WP_Theme_JSON_Resolver_Gutenberg::get_user_global_styles_post_id();
		$excluded_paths   = array();
		/*
		 * Removes any edit or view context paths originating from Core,
		 * or elsewhere, e.g., gutenberg_block_editor_preload_paths_6_6().
		 * Aside from not preloading unnecessary contexts, it also ensures there no duplicates,
		 * leading to a small optimization: block_editor_rest_api_preload() does not dedupe,
		 * and will fire off a WP_REST_Request for every path. In the case of
		 * `/wp/v2/global-styles/*` this will create a new WP_Theme_JSON() instance.
		 */
		$excluded_paths[] = '/wp/v2/global-styles/' . $global_styles_id . '?context=view';
		// Removes any edit context path originating from gutenberg_block_editor_preload_paths_6_6().
		$excluded_paths[] = '/wp/v2/global-styles/' . $global_styles_id . '?context=edit';
		foreach ( $paths as $key => $path ) {
			if ( in_array( $path, $excluded_paths, true ) ) {
				unset( $paths[ $key ] );
			}
		}

		/*
		 * Preload the global styles path with the correct context based on user caps.
		 * NOTE: There is an equivalent conditional check in the client-side code to fetch
		 * the global styles entity using the appropriate context value.
		 * See the call to `canUser()`, under `useGlobalStylesUserConfig()` in `packages/edit-site/src/components/use-global-styles-user-config/index.js`.
		 * Please ensure that the equivalent check is kept in sync with this preload path.
		 */
		$rest_context = current_user_can( 'edit_theme_options' ) ? 'edit' : 'view';
		$paths[]      = "/wp/v2/global-styles/$global_styles_id?context=$rest_context";

		// Used by getBlockPatternCategories in useBlockEditorSettings.
		$paths[] = '/wp/v2/block-patterns/categories';
		$paths[] = '/?_fields=' . implode(
			',',
			// @see packages/core-data/src/entities.js
			array(
				'description',
				'gmt_offset',
				'home',
				'name',
				'site_icon',
				'site_icon_url',
				'site_logo',
				'timezone_string',
				'url',
				'page_for_posts',
				'page_on_front',
				'show_on_front',
			)
		);
	}

	if ( 'core/edit-post' === $context->name ) {
		$slug = 'page' === $context->post->post_type ? 'page' : 'single-' . $context->post->post_type;
		if ( ! empty( $context->post->post_name ) ) {
			$slug .= '-' . $context->post->post_name;
		}

		$paths[] = add_query_arg(
			'slug',
			// @see https://github.com/WordPress/gutenberg/blob/e093fefd041eb6cc4a4e7f67b92ab54fd75c8858/packages/core-data/src/private-selectors.ts#L244-L254
			$slug,
			'/wp/v2/templates/lookup'
		);
	}

	return $paths;
}
add_filter( 'block_editor_rest_api_preload_paths', 'gutenberg_block_editor_preload_paths_6_8', 10, 2 );
