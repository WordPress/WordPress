<?php
/**
 * The block editor page.
 *
 * @since 5.0.0
 *
 * @package WordPress
 * @subpackage Administration
 */

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @global string       $post_type
 * @global WP_Post_Type $post_type_object
 * @global WP_Post      $post             Global post object.
 * @global string       $title
 * @global array        $wp_meta_boxes
 */
global $post_type, $post_type_object, $post, $title, $wp_meta_boxes;

$block_editor_context = new WP_Block_Editor_Context( array( 'post' => $post ) );

// Flag that we're loading the block editor.
$current_screen = get_current_screen();
$current_screen->is_block_editor( true );

// Default to is-fullscreen-mode to avoid jumps in the UI.
add_filter(
	'admin_body_class',
	static function( $classes ) {
		return "$classes is-fullscreen-mode";
	}
);

/*
 * Emoji replacement is disabled for now, until it plays nicely with React.
 */
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );

/*
 * Block editor implements its own Options menu for toggling Document Panels.
 */
add_filter( 'screen_options_show_screen', '__return_false' );

wp_enqueue_script( 'heartbeat' );
wp_enqueue_script( 'wp-edit-post' );

$rest_path = rest_get_route_for_post( $post );

// Preload common data.
$preload_paths = array(
	'/wp/v2/types?context=view',
	'/wp/v2/taxonomies?context=view',
	add_query_arg(
		array(
			'context'  => 'edit',
			'per_page' => -1,
		),
		rest_get_route_for_post_type_items( 'wp_block' )
	),
	add_query_arg( 'context', 'edit', $rest_path ),
	sprintf( '/wp/v2/types/%s?context=edit', $post_type ),
	'/wp/v2/users/me',
	array( rest_get_route_for_post_type_items( 'attachment' ), 'OPTIONS' ),
	array( rest_get_route_for_post_type_items( 'page' ), 'OPTIONS' ),
	array( rest_get_route_for_post_type_items( 'wp_block' ), 'OPTIONS' ),
	array( rest_get_route_for_post_type_items( 'wp_template' ), 'OPTIONS' ),
	sprintf( '%s/autosaves?context=edit', $rest_path ),
	'/wp/v2/settings',
	array( '/wp/v2/settings', 'OPTIONS' ),
);

block_editor_rest_api_preload( $preload_paths, $block_editor_context );

wp_add_inline_script(
	'wp-blocks',
	sprintf( 'wp.blocks.setCategories( %s );', wp_json_encode( get_block_categories( $post ) ) ),
	'after'
);

/*
 * Assign initial edits, if applicable. These are not initially assigned to the persisted post,
 * but should be included in its save payload.
 */
$initial_edits = array();
$is_new_post   = false;
if ( 'auto-draft' === $post->post_status ) {
	$is_new_post = true;
	// Override "(Auto Draft)" new post default title with empty string, or filtered value.
	if ( post_type_supports( $post->post_type, 'title' ) ) {
		$initial_edits['title'] = $post->post_title;
	}

	if ( post_type_supports( $post->post_type, 'editor' ) ) {
		$initial_edits['content'] = $post->post_content;
	}

	if ( post_type_supports( $post->post_type, 'excerpt' ) ) {
		$initial_edits['excerpt'] = $post->post_excerpt;
	}
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
		'post'                  => $post->ID,
		'action'                => 'edit',
		'meta-box-loader'       => true,
		'meta-box-loader-nonce' => wp_create_nonce( 'meta-box-loader' ),
	),
	$meta_box_url
);
wp_add_inline_script(
	'wp-editor',
	sprintf( 'var _wpMetaBoxUrl = %s;', wp_json_encode( $meta_box_url ) ),
	'before'
);

/*
 * Get all available templates for the post/page attributes meta-box.
 * The "Default template" array element should only be added if the array is
 * not empty so we do not trigger the template select element without any options
 * besides the default value.
 */
$available_templates = wp_get_theme()->get_page_templates( get_post( $post->ID ) );
$available_templates = ! empty( $available_templates ) ? array_replace(
	array(
		/** This filter is documented in wp-admin/includes/meta-boxes.php */
		'' => apply_filters( 'default_page_template_title', __( 'Default template' ), 'rest-api' ),
	),
	$available_templates
) : $available_templates;

// Lock settings.
$user_id = wp_check_post_lock( $post->ID );
if ( $user_id ) {
	$locked = false;

	/** This filter is documented in wp-admin/includes/post.php */
	if ( apply_filters( 'show_post_locked_dialog', true, $post, $user_id ) ) {
		$locked = true;
	}

	$user_details = null;
	if ( $locked ) {
		$user         = get_userdata( $user_id );
		$user_details = array(
			'avatar' => get_avatar_url( $user_id, array( 'size' => 128 ) ),
			'name'   => $user->display_name,
		);
	}

	$lock_details = array(
		'isLocked' => $locked,
		'user'     => $user_details,
	);
} else {
	// Lock the post.
	$active_post_lock = wp_set_post_lock( $post->ID );
	if ( $active_post_lock ) {
		$active_post_lock = esc_attr( implode( ':', $active_post_lock ) );
	}

	$lock_details = array(
		'isLocked'       => false,
		'activePostLock' => $active_post_lock,
	);
}

/**
 * Filters the body placeholder text.
 *
 * @since 5.0.0
 * @since 5.8.0 Changed the default placeholder text.
 *
 * @param string  $text Placeholder text. Default 'Type / to choose a block'.
 * @param WP_Post $post Post object.
 */
$body_placeholder = apply_filters( 'write_your_story', __( 'Type / to choose a block' ), $post );

$editor_settings = array(
	'availableTemplates'   => $available_templates,
	'disablePostFormats'   => ! current_theme_supports( 'post-formats' ),
	/** This filter is documented in wp-admin/edit-form-advanced.php */
	'titlePlaceholder'     => apply_filters( 'enter_title_here', __( 'Add title' ), $post ),
	'bodyPlaceholder'      => $body_placeholder,
	'autosaveInterval'     => AUTOSAVE_INTERVAL,
	'richEditingEnabled'   => user_can_richedit(),
	'postLock'             => $lock_details,
	'postLockUtils'        => array(
		'nonce'       => wp_create_nonce( 'lock-post_' . $post->ID ),
		'unlockNonce' => wp_create_nonce( 'update-post_' . $post->ID ),
		'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
	),
	'supportsLayout'       => wp_theme_has_theme_json(),
	'supportsTemplateMode' => current_theme_supports( 'block-templates' ),

	// Whether or not to load the 'postcustom' meta box is stored as a user meta
	// field so that we're not always loading its assets.
	'enableCustomFields'   => (bool) get_user_meta( get_current_user_id(), 'enable_custom_fields', true ),
);

// Add additional back-compat patterns registered by `current_screen` et al.
$editor_settings['__experimentalAdditionalBlockPatterns']          = WP_Block_Patterns_Registry::get_instance()->get_all_registered( true );
$editor_settings['__experimentalAdditionalBlockPatternCategories'] = WP_Block_Pattern_Categories_Registry::get_instance()->get_all_registered( true );

$autosave = wp_get_post_autosave( $post->ID );
if ( $autosave ) {
	if ( mysql2date( 'U', $autosave->post_modified_gmt, false ) > mysql2date( 'U', $post->post_modified_gmt, false ) ) {
		$editor_settings['autosave'] = array(
			'editLink' => get_edit_post_link( $autosave->ID ),
		);
	} else {
		wp_delete_post_revision( $autosave->ID );
	}
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

if ( wp_is_block_theme() && $editor_settings['supportsTemplateMode'] ) {
	$editor_settings['defaultTemplatePartAreas'] = get_allowed_block_template_part_areas();
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

// In order to duplicate classic meta box behavior, we need to run the classic meta box actions.
require_once ABSPATH . 'wp-admin/includes/meta-boxes.php';
register_and_do_post_meta_boxes( $post );

// Check if the Custom Fields meta box has been removed at some point.
$core_meta_boxes = $wp_meta_boxes[ $current_screen->id ]['normal']['core'];
if ( ! isset( $core_meta_boxes['postcustom'] ) || ! $core_meta_boxes['postcustom'] ) {
	unset( $editor_settings['enableCustomFields'] );
}

$editor_settings = get_block_editor_settings( $editor_settings, $block_editor_context );

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

if ( (int) get_option( 'page_for_posts' ) === $post->ID ) {
	add_action( 'admin_enqueue_scripts', '_wp_block_editor_posts_page_notice' );
}

require_once ABSPATH . 'wp-admin/admin-header.php';
?>

<div class="block-editor">
	<h1 class="screen-reader-text hide-if-no-js"><?php echo esc_html( $title ); ?></h1>
	<div id="editor" class="block-editor__container hide-if-no-js"></div>
	<div id="metaboxes" class="hidden">
		<?php the_block_editor_meta_boxes(); ?>
	</div>

	<?php // JavaScript is disabled. ?>
	<div class="wrap hide-if-js block-editor-no-js">
		<h1 class="wp-heading-inline"><?php echo esc_html( $title ); ?></h1>
		<div class="notice notice-error">
			<p>
				<?php
					$message = sprintf(
						/* translators: %s: A link to install the Classic Editor plugin. */
						__( 'The block editor requires JavaScript. Please enable JavaScript in your browser settings, or try the <a href="%s">Classic Editor plugin</a>.' ),
						esc_url( wp_nonce_url( self_admin_url( 'plugin-install.php?tab=favorites&user=wordpressdotorg&save=0' ), 'save_wporg_username_' . get_current_user_id() ) )
					);

					/**
					 * Filters the message displayed in the block editor interface when JavaScript is
					 * not enabled in the browser.
					 *
					 * @since 5.0.3
					 *
					 * @param string  $message The message being displayed.
					 * @param WP_Post $post    The post being edited.
					 */
					echo apply_filters( 'block_editor_no_javascript_message', $message, $post );
					?>
			</p>
		</div>
	</div>
</div>
