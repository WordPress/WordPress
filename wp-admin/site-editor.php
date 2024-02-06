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

$is_template_part        = isset( $_GET['postType'] ) && 'wp_template_part' === sanitize_key( $_GET['postType'] );
$is_template_part_path   = isset( $_GET['path'] ) && 'wp_template_partall' === sanitize_key( $_GET['path'] );
$is_template_part_editor = $is_template_part || $is_template_part_path;
$is_patterns             = isset( $_GET['postType'] ) && 'wp_block' === sanitize_key( $_GET['postType'] );
$is_patterns_path        = isset( $_GET['path'] ) && 'patterns' === sanitize_key( $_GET['path'] );
$is_patterns_editor      = $is_patterns || $is_patterns_path;

if ( ! wp_is_block_theme() ) {
	if ( ! current_theme_supports( 'block-template-parts' ) && $is_template_part_editor ) {
		wp_die( __( 'The theme you are currently using is not compatible with the Site Editor.' ) );
	} elseif ( ! $is_patterns_editor && ! $is_template_part_editor ) {
		wp_die( __( 'The theme you are currently using is not compatible with the Site Editor.' ) );
	}
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

$block_editor_context = new WP_Block_Editor_Context( array( 'name' => 'core/edit-site' ) );
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
	array( '/wp/v2/media', 'OPTIONS' ),
	'/wp/v2/types?context=view',
	'/wp/v2/types/wp_template?context=edit',
	'/wp/v2/types/wp_template-part?context=edit',
	'/wp/v2/templates?context=edit&per_page=-1',
	'/wp/v2/template-parts?context=edit&per_page=-1',
	'/wp/v2/themes?context=edit&status=active',
	'/wp/v2/global-styles/' . $active_global_styles_id . '?context=edit',
	'/wp/v2/global-styles/' . $active_global_styles_id,
	'/wp/v2/global-styles/themes/' . $active_theme,
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
);

block_editor_rest_api_preload( $preload_paths, $block_editor_context );

wp_add_inline_script(
	'wp-edit-site',
	sprintf(
		'wp.domReady( function() {
			wp.editSite.initializeEditor( "site-editor", %s );
		} );',
		wp_json_encode( $editor_settings )
	)
);

// Preload server-registered block schemas.
wp_add_inline_script(
	'wp-blocks',
	'wp.blocks.unstable__bootstrapServerSideBlockDefinitions(' . wp_json_encode( get_block_editor_server_block_settings() ) . ');'
);

wp_add_inline_script(
	'wp-blocks',
	sprintf( 'wp.blocks.setCategories( %s );', wp_json_encode( isset( $editor_settings['blockCategories'] ) ? $editor_settings['blockCategories'] : array() ) ),
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
		<h1 class="wp-heading-inline"><?php _e( 'Edit site' ); ?></h1>
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
