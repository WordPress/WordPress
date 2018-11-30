<?php
/**
 * The Block Editor page.
 *
 * @since 5.0.0
 *
 * @package WordPress
 * @subpackage Administration
 */

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @global string       $post_type
 * @global WP_Post_Type $post_type_object
 * @global WP_Post      $post
 * @global string       $title
 * @global array        $editor_styles
 * @global array        $wp_meta_boxes
 */
global $post_type, $post_type_object, $post, $title, $editor_styles, $wp_meta_boxes;

if ( ! empty( $post_type_object ) ) {
	$title = $post_type_object->labels->edit_item;
}

// Flag that we're loading the block editor.
$current_screen = get_current_screen();
$current_screen->is_block_editor( true );

/*
 * Emoji replacement is disabled for now, until it plays nicely with React.
 */
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );

wp_enqueue_script( 'heartbeat' );
wp_enqueue_script( 'wp-edit-post' );
wp_enqueue_script( 'wp-format-library' );

$rest_base = ! empty( $post_type_object->rest_base ) ? $post_type_object->rest_base : $post_type_object->name;

// Preload common data.
$preload_paths = array(
	'/',
	'/wp/v2/types?context=edit',
	'/wp/v2/taxonomies?per_page=-1&context=edit',
	'/wp/v2/themes?status=active',
	sprintf( '/wp/v2/%s/%s?context=edit', $rest_base, $post->ID ),
	sprintf( '/wp/v2/types/%s?context=edit', $post_type ),
	sprintf( '/wp/v2/users/me?post_type=%s&context=edit', $post_type ),
	array( '/wp/v2/media', 'OPTIONS' ),
);

/**
 * Preload common data by specifying an array of REST API paths that will be preloaded.
 *
 * Filters the array of paths that will be preloaded.
 *
 * @since 5.0.0
 *
 * @param array  $preload_paths Array of paths to preload.
 * @param object $post          The post resource data.
 */
$preload_paths = apply_filters( 'block_editor_preload_paths', $preload_paths, $post );

/*
 * Ensure the global $post remains the same after API data is preloaded.
 * Because API preloading can call the_content and other filters, plugins
 * can unexpectedly modify $post.
 */
$backup_global_post = $post;

$preload_data = array_reduce(
	$preload_paths,
	'rest_preload_api_request',
	array()
);

// Restore the global $post as it was before API preloading.
$post = $backup_global_post;

wp_add_inline_script(
	'wp-api-fetch',
	sprintf( 'wp.apiFetch.use( wp.apiFetch.createPreloadingMiddleware( %s ) );', wp_json_encode( $preload_data ) ),
	'after'
);

wp_add_inline_script(
	'wp-blocks',
	sprintf( 'wp.blocks.setCategories( %s );', wp_json_encode( get_block_categories( $post ) ) ),
	'after'
);

/*
 * Assign initial edits, if applicable. These are not initially assigned to the persisted post,
 * but should be included in its save payload.
 */
$initial_edits = null;
$is_new_post = false;
if ( 'auto-draft' === $post->post_status ) {
	$is_new_post = true;
	// Override "(Auto Draft)" new post default title with empty string, or filtered value.
	$initial_edits = array(
		'title'   => $post->post_title,
		'content' => $post->post_content,
		'excerpt' => $post->post_excerpt,
	);
}

// Preload server-registered block schemas.
wp_add_inline_script(
	'wp-blocks',
	'wp.blocks.unstable__bootstrapServerSideBlockDefinitions(' . wp_json_encode( get_block_editor_server_block_settings() ) . ');'
);

// Get admin url for handling meta boxes.
$meta_box_url = admin_url( 'post.php' );
$meta_box_url = add_query_arg(
	array(
		'post'            => $post->ID,
		'action'          => 'edit',
		'meta-box-loader' => true,
		'_wpnonce'        => wp_create_nonce( 'meta-box-loader' ),
	),
	$meta_box_url
);
wp_localize_script( 'wp-editor', '_wpMetaBoxUrl', $meta_box_url );


/*
 * Initialize the editor.
 */

$align_wide    = get_theme_support( 'align-wide' );
$color_palette = current( (array) get_theme_support( 'editor-color-palette' ) );
$font_sizes    = current( (array) get_theme_support( 'editor-font-sizes' ) );

/**
 * Filters the allowed block types for the editor, defaulting to true (all
 * block types supported).
 *
 * @since 5.0.0
 *
 * @param bool|array $allowed_block_types Array of block type slugs, or
 *                                        boolean to enable/disable all.
 * @param object $post                    The post resource data.
 */
$allowed_block_types = apply_filters( 'allowed_block_types', true, $post );

// Get all available templates for the post/page attributes meta-box.
// The "Default template" array element should only be added if the array is
// not empty so we do not trigger the template select element without any options
// besides the default value.
$available_templates = wp_get_theme()->get_page_templates( get_post( $post->ID ) );
$available_templates = ! empty( $available_templates ) ? array_merge(
	array(
		/** This filter is documented in wp-admin/includes/meta-boxes.php */
		'' => apply_filters( 'default_page_template_title', __( 'Default template' ), 'rest-api' ),
	),
	$available_templates
) : $available_templates;

// Media settings.
$max_upload_size = wp_max_upload_size();
if ( ! $max_upload_size ) {
	$max_upload_size = 0;
}

// Editor Styles.
$styles = array(
	array(
		'css' => file_get_contents(
			ABSPATH . WPINC . '/css/dist/editor/editor-styles.css'
		),
	),
);

/* Translators: Use this to specify the CSS font family for the default font */
$locale_font_family = esc_html_x( 'Noto Serif', 'CSS Font Family for Editor Font' );
$styles[]           = array(
	'css' => "body { font-family: '$locale_font_family' }",
);

if ( $editor_styles && current_theme_supports( 'editor-styles' ) ) {
	foreach ( $editor_styles as $style ) {
		if ( preg_match( '~^(https?:)?//~', $style ) ) {
			$response = wp_remote_get( $style );
			if ( ! is_wp_error( $response ) ) {
				$styles[] = array(
					'css' => wp_remote_retrieve_body( $response ),
				);
			}
		} else {
			$file = get_theme_file_path( $style );
			if ( file_exists( $file ) ) {
				$styles[] = array(
					'css'     => file_get_contents( $file ),
					'baseURL' => get_theme_file_uri( $style ),
				);
			}
		}
	}
}

// Image sizes.

/** This filter is documented in wp-admin/includes/media.php */
$image_size_names = apply_filters(
	'image_size_names_choose',
	array(
		'thumbnail' => __( 'Thumbnail' ),
		'medium'    => __( 'Medium' ),
		'large'     => __( 'Large' ),
		'full'      => __( 'Full Size' ),
	)
);

$available_image_sizes = array();
foreach ( $image_size_names as $image_size_slug => $image_size_name ) {
	$available_image_sizes[] = array(
		'slug' => $image_size_slug,
		'name' => $image_size_name,
	);
}

// Lock settings.
$user_id = wp_check_post_lock( $post->ID );
if ( $user_id ) {
	/** This filter is documented in wp-admin/includes/post.php */
	if ( apply_filters( 'show_post_locked_dialog', true, $post, $user_id ) ) {
		$locked = true;
	}

	$user_details = null;
	if ( $locked ) {
		$user         = get_userdata( $user_id );
		$user_details = array(
			'name' => $user->display_name,
		);
		$avatar       = get_avatar_url( $user_id, array( 'size' => 64 ) );
	}

	$lock_details = array(
		'isLocked' => $locked,
		'user'     => $user_details,
	);
} else {
	// Lock the post.
	$active_post_lock = wp_set_post_lock( $post->ID );
	$lock_details     = array(
		'isLocked'       => false,
		'activePostLock' => esc_attr( implode( ':', $active_post_lock ) ),
	);
}

/**
 * Filters the body placeholder text.
 *
 * @since 5.0.0
 *
 * @param string  $text Placeholder text. Default 'Start writing or type / to choose a block'.
 * @param WP_Post $post Post object.
 */
$body_placeholder = apply_filters( 'write_your_story', __( 'Start writing or type / to choose a block' ), $post );

$editor_settings = array(
	'alignWide'              => $align_wide,
	'availableTemplates'     => $available_templates,
	'allowedBlockTypes'      => $allowed_block_types,
	'disableCustomColors'    => get_theme_support( 'disable-custom-colors' ),
	'disableCustomFontSizes' => get_theme_support( 'disable-custom-font-sizes' ),
	'disablePostFormats'     => ! current_theme_supports( 'post-formats' ),
	/** This filter is documented in wp-admin/edit-form-advanced.php */
	'titlePlaceholder'       => apply_filters( 'enter_title_here', __( 'Add title' ), $post ),
	'bodyPlaceholder'        => $body_placeholder,
	'isRTL'                  => is_rtl(),
	'autosaveInterval'       => 10,
	'maxUploadFileSize'      => $max_upload_size,
	'allowedMimeTypes'       => get_allowed_mime_types(),
	'styles'                 => $styles,
	'imageSizes'             => $available_image_sizes,
	'richEditingEnabled'     => user_can_richedit(),
	'postLock'               => $lock_details,
	'postLockUtils'          => array(
		'nonce'       => wp_create_nonce( 'lock-post_' . $post->ID ),
		'unlockNonce' => wp_create_nonce( 'update-post_' . $post->ID ),
		'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
	),

	// Whether or not to load the 'postcustom' meta box is stored as a user meta
	// field so that we're not always loading its assets.
	'enableCustomFields'     => (bool) get_user_meta( get_current_user_id(), 'enable_custom_fields', true ),
);

$autosave = wp_get_post_autosave( $post_ID );
if ( $autosave ) {
	if ( mysql2date( 'U', $autosave->post_modified_gmt, false ) > mysql2date( 'U', $post->post_modified_gmt, false ) ) {
		$editor_settings['autosave'] = array(
			'editLink' => get_edit_post_link( $autosave->ID ),
		);
	} else {
		wp_delete_post_revision( $autosave->ID );
	}
}

if ( false !== $color_palette ) {
	$editor_settings['colors'] = $color_palette;
}

if ( ! empty( $font_sizes ) ) {
	$editor_settings['fontSizes'] = $font_sizes;
}

if ( ! empty( $post_type_object->template ) ) {
	$editor_settings['template']     = $post_type_object->template;
	$editor_settings['templateLock'] = ! empty( $post_type_object->template_lock ) ? $post_type_object->template_lock : false;
}

// If there's no template set on a new post, use the post format, instead.
if ( $is_new_post && ! isset( $editor_settings['template'] ) && 'post' === $post->post_type ) {
	$post_format = get_post_format( $post );
	if ( in_array( $post_format, array( 'audio', 'gallery', 'image', 'quote', 'video' ), true ) ) {
		$editor_settings['template'] = array( array( "core/$post_format" ) );
	}
}

/**
 * Scripts
 */
wp_enqueue_media(
	array(
		'post' => $post->ID,
	)
);
wp_tinymce_inline_scripts();
wp_enqueue_editor();

/**
 * Styles
 */
wp_enqueue_style( 'wp-edit-post' );
wp_enqueue_style( 'wp-format-library' );

/**
 * Fires after block assets have been enqueued for the editing interface.
 *
 * Call `add_action` on any hook before 'admin_enqueue_scripts'.
 *
 * In the function call you supply, simply use `wp_enqueue_script` and
 * `wp_enqueue_style` to add your functionality to the block editor.
 *
 * @since 5.0.0
 */
do_action( 'enqueue_block_editor_assets' );

// In order to duplicate classic meta box behaviour, we need to run the classic meta box actions.
require_once( ABSPATH . 'wp-admin/includes/meta-boxes.php' );
register_and_do_post_meta_boxes( $post );

// Check if the Custom Fields meta box has been removed at some point.
$core_meta_boxes = $wp_meta_boxes[ $current_screen->id ]['normal']['core'];
if ( ! isset( $core_meta_boxes['postcustom'] ) || ! $core_meta_boxes['postcustom'] ) {
	unset( $editor_settings['enableCustomFields'] );
}

/**
 * Filters the settings to pass to the block editor.
 *
 * @since 5.0.0
 *
 * @param array   $editor_settings Default editor settings.
 * @param WP_Post $post            Post being edited.
 */
$editor_settings = apply_filters( 'block_editor_settings', $editor_settings, $post );

$init_script = <<<JS
( function() {
	window._wpLoadBlockEditor = new Promise( function( resolve ) {
		wp.domReady( function() {
			resolve( wp.editPost.initializeEditor( 'editor', "%s", %d, %s, %s ) );
		} );
	} );
} )();
JS;

$script = sprintf(
	$init_script,
	$post->post_type,
	$post->ID,
	wp_json_encode( $editor_settings ),
	wp_json_encode( $initial_edits )
);
wp_add_inline_script( 'wp-edit-post', $script );

require_once( ABSPATH . 'wp-admin/admin-header.php' );
?>

<div class="block-editor">
	<h1 class="screen-reader-text"><?php echo esc_html( $post_type_object->labels->edit_item ); ?></h1>
	<div id="editor" class="block-editor__container"></div>
	<div id="metaboxes" class="hidden">
		<?php the_block_editor_meta_boxes(); ?>
	</div>
</div>
