<?php
/**
 * Site Editor administration screen.
 *
 * @package WordPress
 * @subpackage Administration
 */

global $editor_styles;

/** WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'edit_theme_options' ) ) {
	wp_die(
		'<h1>' . __( 'You need a higher level of permission.' ) . '</h1>' .
		'<p>' . __( 'Sorry, you are not allowed to edit theme options on this site.' ) . '</p>',
		403
	);
}

/**
 * Maps old site editor urls to the new updated ones.
 *
 * @since 6.8.0
 * @access private
 *
 * @global string $pagenow The filename of the current screen.
 *
 * @return string|false The new URL to redirect to, or false if no redirection is needed.
 */
function _wp_get_site_editor_redirection_url() {
	global $pagenow;
	if ( 'site-editor.php' !== $pagenow || isset( $_REQUEST['p'] ) || empty( $_SERVER['QUERY_STRING'] ) ) {
		return false;
	}

	// The following redirects are for the new permalinks in the site editor.
	if ( isset( $_REQUEST['postType'] ) && 'wp_navigation' === $_REQUEST['postType'] && ! empty( $_REQUEST['postId'] ) ) {
		return add_query_arg( array( 'p' => '/wp_navigation/' . $_REQUEST['postId'] ), remove_query_arg( array( 'postType', 'postId' ) ) );
	}

	if ( isset( $_REQUEST['postType'] ) && 'wp_navigation' === $_REQUEST['postType'] && empty( $_REQUEST['postId'] ) ) {
		return add_query_arg( array( 'p' => '/navigation' ), remove_query_arg( 'postType' ) );
	}

	if ( isset( $_REQUEST['path'] ) && '/wp_global_styles' === $_REQUEST['path'] ) {
		return add_query_arg( array( 'p' => '/styles' ), remove_query_arg( 'path' ) );
	}

	if ( isset( $_REQUEST['postType'] ) && 'page' === $_REQUEST['postType'] && ( empty( $_REQUEST['canvas'] ) || empty( $_REQUEST['postId'] ) ) ) {
		return add_query_arg( array( 'p' => '/page' ), remove_query_arg( 'postType' ) );
	}

	if ( isset( $_REQUEST['postType'] ) && 'page' === $_REQUEST['postType'] && ! empty( $_REQUEST['postId'] ) ) {
		return add_query_arg( array( 'p' => '/page/' . $_REQUEST['postId'] ), remove_query_arg( array( 'postType', 'postId' ) ) );
	}

	if ( isset( $_REQUEST['postType'] ) && 'wp_template' === $_REQUEST['postType'] && ( empty( $_REQUEST['canvas'] ) || empty( $_REQUEST['postId'] ) ) ) {
		return add_query_arg( array( 'p' => '/template' ), remove_query_arg( 'postType' ) );
	}

	if ( isset( $_REQUEST['postType'] ) && 'wp_template' === $_REQUEST['postType'] && ! empty( $_REQUEST['postId'] ) ) {
		return add_query_arg( array( 'p' => '/wp_template/' . $_REQUEST['postId'] ), remove_query_arg( array( 'postType', 'postId' ) ) );
	}

	if ( isset( $_REQUEST['postType'] ) && 'wp_block' === $_REQUEST['postType'] && ( empty( $_REQUEST['canvas'] ) || empty( $_REQUEST['postId'] ) ) ) {
		return add_query_arg( array( 'p' => '/pattern' ), remove_query_arg( 'postType' ) );
	}

	if ( isset( $_REQUEST['postType'] ) && 'wp_block' === $_REQUEST['postType'] && ! empty( $_REQUEST['postId'] ) ) {
		return add_query_arg( array( 'p' => '/wp_block/' . $_REQUEST['postId'] ), remove_query_arg( array( 'postType', 'postId' ) ) );
	}

	if ( isset( $_REQUEST['postType'] ) && 'wp_template_part' === $_REQUEST['postType'] && ( empty( $_REQUEST['canvas'] ) || empty( $_REQUEST['postId'] ) ) ) {
		return add_query_arg( array( 'p' => '/pattern' ) );
	}

	if ( isset( $_REQUEST['postType'] ) && 'wp_template_part' === $_REQUEST['postType'] && ! empty( $_REQUEST['postId'] ) ) {
		return add_query_arg( array( 'p' => '/wp_template_part/' . $_REQUEST['postId'] ), remove_query_arg( array( 'postType', 'postId' ) ) );
	}

	// The following redirects are for backward compatibility with the old site editor URLs.
	if ( isset( $_REQUEST['path'] ) && '/wp_template_part/all' === $_REQUEST['path'] ) {
		return add_query_arg(
			array(
				'p'        => '/pattern',
				'postType' => 'wp_template_part',
			),
			remove_query_arg( 'path' )
		);
	}

	if ( isset( $_REQUEST['path'] ) && '/page' === $_REQUEST['path'] ) {
		return add_query_arg( array( 'p' => '/page' ), remove_query_arg( 'path' ) );
	}

	if ( isset( $_REQUEST['path'] ) && '/wp_template' === $_REQUEST['path'] ) {
		return add_query_arg( array( 'p' => '/template' ), remove_query_arg( 'path' ) );
	}

	if ( isset( $_REQUEST['path'] ) && '/patterns' === $_REQUEST['path'] ) {
		return add_query_arg( array( 'p' => '/pattern' ), remove_query_arg( 'path' ) );
	}

	if ( isset( $_REQUEST['path'] ) && '/navigation' === $_REQUEST['path'] ) {
		return add_query_arg( array( 'p' => '/navigation' ), remove_query_arg( 'path' ) );
	}

	return add_query_arg( array( 'p' => '/' ) );
}

// Redirect to the site editor to the new URLs if needed.
$redirection = _wp_get_site_editor_redirection_url();
if ( false !== $redirection ) {
	wp_safe_redirect( $redirection );
	exit;
}

// Used in the HTML title tag.
$title       = _x( 'Editor', 'site editor title tag' );
$parent_file = 'themes.php';

// Flag that we're loading the block editor.
$current_screen = get_current_screen();
$current_screen->is_block_editor( true );

// Default to is-fullscreen-mode to avoid jumps in the UI.
add_filter(
	'admin_body_class',
	static function ( $classes ) {
		return "$classes is-fullscreen-mode";
	}
);

$indexed_template_types = array();
foreach ( get_default_block_template_types() as $slug => $template_type ) {
	$template_type['slug']    = (string) $slug;
	$indexed_template_types[] = $template_type;
}

$context_settings = array( 'name' => 'core/edit-site' );

if ( ! empty( $_GET['postId'] ) && is_numeric( $_GET['postId'] ) ) {
	$context_settings['post'] = get_post( (int) $_GET['postId'] );
} elseif ( isset( $_GET['p'] ) && preg_match( '/^\/page\/(\d+)$/', $_GET['p'], $matches ) ) {
	$context_settings['post'] = get_post( (int) $matches[1] );
}

$block_editor_context = new WP_Block_Editor_Context( $context_settings );
$custom_settings      = array(
	'siteUrl'                   => site_url(),
	'postsPerPage'              => get_option( 'posts_per_page' ),
	'styles'                    => get_block_editor_theme_styles(),
	'defaultTemplateTypes'      => $indexed_template_types,
	'defaultTemplatePartAreas'  => get_allowed_block_template_part_areas(),
	'supportsLayout'            => wp_theme_has_theme_json(),
	'supportsTemplatePartsMode' => ! wp_is_block_theme() && current_theme_supports( 'block-template-parts' ),
);

// Add additional back-compat patterns registered by `current_screen` et al.
$custom_settings['__experimentalAdditionalBlockPatterns']          = WP_Block_Patterns_Registry::get_instance()->get_all_registered( true );
$custom_settings['__experimentalAdditionalBlockPatternCategories'] = WP_Block_Pattern_Categories_Registry::get_instance()->get_all_registered( true );

$editor_settings = get_block_editor_settings( $custom_settings, $block_editor_context );

if ( isset( $_GET['postType'] ) && ! isset( $_GET['postId'] ) ) {
	$post_type = get_post_type_object( $_GET['postType'] );
	if ( ! $post_type ) {
		wp_die( __( 'Invalid post type.' ) );
	}
}

$active_global_styles_id = WP_Theme_JSON_Resolver::get_user_global_styles_post_id();
$active_theme            = get_stylesheet();

$navigation_rest_route = rest_get_route_for_post_type_items(
	'wp_navigation'
);

$preload_paths = array(
	array( rest_get_route_for_post_type_items( 'attachment' ), 'OPTIONS' ),
	array( rest_get_route_for_post_type_items( 'page' ), 'OPTIONS' ),
	'/wp/v2/types?context=view',
	'/wp/v2/types/wp_template?context=edit',
	'/wp/v2/types/wp_template_part?context=edit',
	'/wp/v2/templates?context=edit&per_page=-1',
	'/wp/v2/template-parts?context=edit&per_page=-1',
	'/wp/v2/themes?context=edit&status=active',
	'/wp/v2/global-styles/' . $active_global_styles_id . '?context=edit',
	array( '/wp/v2/global-styles/' . $active_global_styles_id, 'OPTIONS' ),
	'/wp/v2/global-styles/themes/' . $active_theme . '?context=view',
	'/wp/v2/global-styles/themes/' . $active_theme . '/variations?context=view',
	array( $navigation_rest_route, 'OPTIONS' ),
	array(
		add_query_arg(
			array(
				'context'   => 'edit',
				'per_page'  => 100,
				'order'     => 'desc',
				'orderby'   => 'date',
				// array indices are required to avoid query being encoded and not matching in cache.
				'status[0]' => 'publish',
				'status[1]' => 'draft',
			),
			$navigation_rest_route
		),
		'GET',
	),
	'/wp/v2/settings',
	array( '/wp/v2/settings', 'OPTIONS' ),
	// Used by getBlockPatternCategories in useBlockEditorSettings.
	'/wp/v2/block-patterns/categories',
	// @see packages/core-data/src/entities.js
	'/?_fields=' . implode(
		',',
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
	),
);

if ( $block_editor_context->post ) {
	$route_for_post = rest_get_route_for_post( $block_editor_context->post );
	if ( $route_for_post ) {
		$preload_paths[] = add_query_arg( 'context', 'edit', $route_for_post );
		if ( 'page' === $block_editor_context->post->post_type ) {
			$preload_paths[] = add_query_arg(
				'slug',
				// @see https://github.com/WordPress/gutenberg/blob/e093fefd041eb6cc4a4e7f67b92ab54fd75c8858/packages/core-data/src/private-selectors.ts#L244-L254
				empty( $block_editor_context->post->post_name ) ? 'page' : 'page-' . $block_editor_context->post->post_name,
				'/wp/v2/templates/lookup'
			);
		}
	}
} else {
	$preload_paths[] = '/wp/v2/templates/lookup?slug=front-page';
	$preload_paths[] = '/wp/v2/templates/lookup?slug=home';
}

block_editor_rest_api_preload( $preload_paths, $block_editor_context );

wp_add_inline_script(
	'wp-edit-site',
	sprintf(
		'wp.domReady( function() {
			wp.editSite.initializeEditor( "site-editor", %s );
		} );',
		wp_json_encode( $editor_settings, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES )
	)
);

// Preload server-registered block schemas.
wp_add_inline_script(
	'wp-blocks',
	'wp.blocks.unstable__bootstrapServerSideBlockDefinitions(' . wp_json_encode( get_block_editor_server_block_settings(), JSON_HEX_TAG | JSON_UNESCAPED_SLASHES ) . ');'
);

// Preload server-registered block bindings sources.
$registered_sources = get_all_registered_block_bindings_sources();
if ( ! empty( $registered_sources ) ) {
	$filtered_sources = array();
	foreach ( $registered_sources as $source ) {
		$filtered_sources[] = array(
			'name'        => $source->name,
			'label'       => $source->label,
			'usesContext' => $source->uses_context,
		);
	}
	$script = sprintf( 'for ( const source of %s ) { wp.blocks.registerBlockBindingsSource( source ); }', wp_json_encode( $filtered_sources, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES ) );
	wp_add_inline_script(
		'wp-blocks',
		$script
	);
}

wp_add_inline_script(
	'wp-blocks',
	sprintf( 'wp.blocks.setCategories( %s );', wp_json_encode( isset( $editor_settings['blockCategories'] ) ? $editor_settings['blockCategories'] : array(), JSON_HEX_TAG | JSON_UNESCAPED_SLASHES ) ),
	'after'
);

wp_enqueue_script( 'wp-edit-site' );
wp_enqueue_script( 'wp-format-library' );
wp_enqueue_style( 'wp-edit-site' );
wp_enqueue_style( 'wp-format-library' );
wp_enqueue_media();

if (
	current_theme_supports( 'wp-block-styles' ) &&
	( ! is_array( $editor_styles ) || count( $editor_styles ) === 0 )
) {
	wp_enqueue_style( 'wp-block-library-theme' );
}

/** This action is documented in wp-admin/edit-form-blocks.php */
do_action( 'enqueue_block_editor_assets' );

require_once ABSPATH . 'wp-admin/admin-header.php';
?>

<div class="edit-site" id="site-editor">
	<?php // JavaScript is disabled. ?>
	<div class="wrap hide-if-js site-editor-no-js">
		<h1 class="wp-heading-inline"><?php _e( 'Edit Site' ); ?></h1>
		<?php
		/**
		 * Filters the message displayed in the site editor interface when JavaScript is
		 * not enabled in the browser.
		 *
		 * @since 6.3.0
		 *
		 * @param string  $message The message being displayed.
		 * @param WP_Post $post    The post being edited.
		 */
		$message = apply_filters( 'site_editor_no_javascript_message', __( 'The site editor requires JavaScript. Please enable JavaScript in your browser settings.' ), $post );
		wp_admin_notice(
			$message,
			array(
				'type'               => 'error',
				'additional_classes' => array( 'hide-if-js' ),
			)
		);
		?>
	</div>
</div>

<?php

require_once ABSPATH . 'wp-admin/admin-footer.php';
