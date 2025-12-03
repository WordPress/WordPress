<?php
/**
 * Block functions specific for the Gutenberg editor plugin.
 *
 * @package gutenberg
 */

/**
 * Substitutes the implementation of a core-registered block type, if exists,
 * with the built result from the plugin.
 */
function gutenberg_reregister_core_block_types() {
	// Blocks directory may not exist if working from a fresh clone.
	$blocks_dirs = array(
		__DIR__ . '/../build/scripts/block-library/',
		__DIR__ . '/../build/scripts/edit-widgets/blocks/',
		__DIR__ . '/../build/scripts/widgets/blocks/',
	);

	foreach ( $blocks_dirs as $blocks_dir ) {
		if ( ! is_dir( $blocks_dir ) ) {
			continue;
		}

		// Scan for block directories (those with block.json).
		$block_json_files = glob( $blocks_dir . '*/block.json' );

		foreach ( $block_json_files as $block_json_file ) {
			$block_folder      = dirname( $block_json_file );
			$block_name_folder = basename( $block_folder );

			// Read block.json to get block name.
			$metadata = json_decode( file_get_contents( $block_json_file ), true );
			if ( ! is_array( $metadata ) || ! isset( $metadata['name'] ) ) {
				continue;
			}

			// Deregister core version.
			gutenberg_deregister_core_block_and_assets( $metadata['name'] );
			gutenberg_register_core_block_assets( $block_name_folder );
			$php_file = dirname( $block_folder ) . '/' . $block_name_folder . '.php';
			if ( file_exists( $php_file ) ) {
				require_once $php_file;
			} else {
				register_block_type_from_metadata( $block_json_file );
			}
		}
	}
}

add_action( 'init', 'gutenberg_reregister_core_block_types' );

/**
 * Adds the defer loading strategy to all registered blocks.
 *
 * This function would not be part of core merge. Instead, the register_block_script_handle() function would be patched
 * as follows.
 *
 * ```
 * --- a/wp-includes/blocks.php
 * +++ b/wp-includes/blocks.php
 * @ @ -153,7 +153,8 @ @ function register_block_script_handle( $metadata, $field_name, $index = 0 ) {
 *                 $script_handle,
 *                 $script_uri,
 *                 $script_dependencies,
 * -           isset( $script_asset['version'] ) ? $script_asset['version'] : false
 * +         isset( $script_asset['version'] ) ? $script_asset['version'] : false,
 * +         array( 'strategy' => 'defer' )
 *         );
 *         if ( ! $result ) {
 *                 return false;
 * ```
 *
 * @see register_block_script_handle()
 */
function gutenberg_defer_block_view_scripts() {
	$block_types = WP_Block_Type_Registry::get_instance()->get_all_registered();
	foreach ( $block_types as $block_type ) {
		foreach ( $block_type->view_script_handles as $view_script_handle ) {
			wp_script_add_data( $view_script_handle, 'strategy', 'defer' );
		}
	}
}

add_action( 'init', 'gutenberg_defer_block_view_scripts', 100 );

/**
 * Deregisters the existing core block type and its assets.
 *
 * @param string $block_name The name of the block.
 *
 * @return void
 */
function gutenberg_deregister_core_block_and_assets( $block_name ) {
	$registry = WP_Block_Type_Registry::get_instance();
	if ( $registry->is_registered( $block_name ) ) {
		$block_type = $registry->get_registered( $block_name );
		if ( ! empty( $block_type->view_script_handles ) ) {
			foreach ( $block_type->view_script_handles as $view_script_handle ) {
				if ( str_starts_with( $view_script_handle, 'wp-block-' ) ) {
					wp_deregister_script( $view_script_handle );
				}
			}
		}
		$registry->unregister( $block_name );
	}
}

/**
 * Registers block styles for a core block.
 *
 * @param string $block_name The block-name.
 *
 * @return void
 */
function gutenberg_register_core_block_assets( $block_name ) {
	if ( ! wp_should_load_separate_core_block_assets() ) {
		return;
	}

	$block_name = str_replace( 'core/', '', $block_name );

	// When in production, use the plugin's version as the default asset version;
	// else (for development or test) default to use the current time.
	$default_version = defined( 'GUTENBERG_VERSION' ) && ! SCRIPT_DEBUG ? GUTENBERG_VERSION : time();

	$style_path      = "build/styles/block-library/$block_name/";
	$stylesheet_url  = gutenberg_url( $style_path . 'style.css' );
	$stylesheet_path = gutenberg_dir_path() . $style_path . ( is_rtl() ? 'style-rtl.css' : 'style.css' );

	if ( file_exists( $stylesheet_path ) ) {

		wp_deregister_style( "wp-block-{$block_name}" );
		wp_register_style(
			"wp-block-{$block_name}",
			$stylesheet_url,
			array(),
			$default_version
		);
		wp_style_add_data( "wp-block-{$block_name}", 'rtl', 'replace' );
		// Add a reference to the stylesheet's path to allow calculations for inlining styles in `wp_head`.
		wp_style_add_data( "wp-block-{$block_name}", 'path', $stylesheet_path );
	} else {
		wp_register_style( "wp-block-{$block_name}", false, array() );
	}

	/*
	 * If the current theme supports wp-block-styles, dequeue the core styles
	 * and enqueue the plugin ones instead.
	 */
	if ( current_theme_supports( 'wp-block-styles' ) ) {

		// Get the path to the block's stylesheet.
		$theme_style_path = is_rtl()
			? "build/styles/block-library/$block_name/theme-rtl.css"
			: "build/styles/block-library/$block_name/theme.css";

		// If the file exists, enqueue it.
		if ( file_exists( gutenberg_dir_path() . $theme_style_path ) ) {
			wp_deregister_style( "wp-block-{$block_name}-theme" );
			wp_register_style(
				"wp-block-{$block_name}-theme",
				gutenberg_url( $theme_style_path ),
				array(),
				$default_version
			);
			wp_style_add_data( "wp-block-{$block_name}-theme", 'path', gutenberg_dir_path() . $theme_style_path );
		}
	}

	$editor_style_path = "build/styles/block-library/$block_name/style-editor.css";
	if ( file_exists( gutenberg_dir_path() . $editor_style_path ) ) {
		wp_deregister_style( "wp-block-{$block_name}-editor" );
		wp_register_style(
			"wp-block-{$block_name}-editor",
			gutenberg_url( $editor_style_path ),
			array(),
			$default_version
		);
		wp_style_add_data( "wp-block-{$block_name}-editor", 'rtl', 'replace' );
	} else {
		wp_register_style( "wp-block-{$block_name}-editor", false );
	}
}

/**
 * Complements the implementation of block type `core/social-icon`, whether it
 * be provided by core or the plugin, with derived block types for each
 * "service" (WordPress, Twitter, etc.) supported by Social Links.
 *
 * This ensures backwards compatibility for any users running the Gutenberg
 * plugin who have used Social Links prior to their conversion to block
 * variations.
 *
 * This shim is INTENTIONALLY left out of core, as Social Links have never
 * landed there.
 *
 * @see https://github.com/WordPress/gutenberg/pull/19887
 */
function gutenberg_register_legacy_social_link_blocks() {
	$services = array(
		'amazon',
		'bandcamp',
		'behance',
		'chain',
		'codepen',
		'deviantart',
		'dribbble',
		'dropbox',
		'etsy',
		'facebook',
		'feed',
		'fivehundredpx',
		'flickr',
		'foursquare',
		'goodreads',
		'google',
		'github',
		'instagram',
		'lastfm',
		'linkedin',
		'mail',
		'mastodon',
		'meetup',
		'medium',
		'pinterest',
		'pocket',
		'reddit',
		'skype',
		'snapchat',
		'soundcloud',
		'spotify',
		'tumblr',
		'twitch',
		'twitter',
		'vimeo',
		'vk',
		'wordpress',
		'yelp',
		'youtube',
	);

	foreach ( $services as $service ) {
		register_block_type(
			'core/social-link-' . $service,
			array(
				'category'        => 'widgets',
				'attributes'      => array(
					'url'     => array(
						'type' => 'string',
					),
					'service' => array(
						'type'    => 'string',
						'default' => $service,
					),
					'label'   => array(
						'type' => 'string',
					),
				),
				'render_callback' => 'gutenberg_render_block_core_social_link',
			)
		);
	}
}

add_action( 'init', 'gutenberg_register_legacy_social_link_blocks' );

/**
 * Migrate the legacy `sync_status` meta key (added 16.1) to the new `wp_pattern_sync_status` meta key (16.1.1).
 *
 * This filter is INTENTIONALLY left out of core as the meta key was fist introduced to core in 6.3 as `wp_pattern_sync_status`.
 * see https://github.com/WordPress/gutenberg/pull/52232
 *
 * @param mixed  $value     The value to return, either a single metadata value or an array of values depending on the value of $single.
 * @param int    $object_id ID of the object metadata is for.
 * @param string $meta_key  Metadata key.
 * @param bool   $single    Whether to return only the first value of the specified $meta_key.
 */
function gutenberg_legacy_wp_block_post_meta( $value, $object_id, $meta_key, $single ) {
	if ( 'wp_pattern_sync_status' !== $meta_key ) {
		return $value;
	}

	$sync_status = get_post_meta( $object_id, 'sync_status', $single );

	if ( $single && 'unsynced' === $sync_status ) {
		return $sync_status;
	} elseif ( isset( $sync_status[0] ) && 'unsynced' === $sync_status[0] ) {
		return $sync_status;
	}

	return $value;
}

add_filter( 'default_post_metadata', 'gutenberg_legacy_wp_block_post_meta', 10, 4 );



/**
 * Strips all HTML from the content of footnotes, and sanitizes the ID.
 *
 * This function expects slashed data on the footnotes content.
 *
 * @access private
 *
 * @param string $footnotes JSON encoded string of an array containing the content and ID of each footnote.
 * @return string Filtered content without any HTML on the footnote content and with the sanitized id.
 */
function _gutenberg_filter_post_meta_footnotes( $footnotes ) {
	$footnotes_decoded = json_decode( $footnotes, true );
	if ( ! is_array( $footnotes_decoded ) ) {
		return '';
	}
	$footnotes_sanitized = array();
	foreach ( $footnotes_decoded as $footnote ) {
		if ( ! empty( $footnote['content'] ) && ! empty( $footnote['id'] ) ) {
			$footnotes_sanitized[] = array(
				'id'      => sanitize_key( $footnote['id'] ),
				'content' => wp_unslash( wp_filter_post_kses( wp_slash( $footnote['content'] ) ) ),
			);
		}
	}
	return wp_json_encode( $footnotes_sanitized );
}

/**
 * Adds the filters to filter footnotes meta field.
 *
 * @access private
 */
function _gutenberg_footnotes_kses_init_filters() {
	add_filter( 'sanitize_post_meta_footnotes', '_gutenberg_filter_post_meta_footnotes' );
}

/**
 * Removes the filters that filter footnotes meta field.
 *
 * @access private
 */
function _gutenberg_footnotes_remove_filters() {
	remove_filter( 'sanitize_post_meta_footnotes', '_gutenberg_filter_post_meta_footnotes' );
}

/**
 * Registers the filter of footnotes meta field if the user does not have unfiltered_html capability.
 *
 * @access private
 */
function _gutenberg_footnotes_kses_init() {
	if ( function_exists( '_wp_filter_post_meta_footnotes' ) ) {
		return;
	}
	_gutenberg_footnotes_remove_filters();
	if ( ! current_user_can( 'unfiltered_html' ) ) {
		_gutenberg_footnotes_kses_init_filters();
	}
}

/**
 * Initializes footnotes meta field filters when imported data should be filtered.
 *
 * This filter is the last being executed on force_filtered_html_on_import.
 * If the input of the filter is true it means we are in an import situation and should
 * enable kses, independently of the user capabilities.
 * So in that case we call _gutenberg_footnotes_kses_init_filters;
 *
 * @access private
 *
 * @param string $arg Input argument of the filter.
 * @return string Input argument of the filter.
 */
function _gutenberg_footnotes_force_filtered_html_on_import_filter( $arg ) {
	if ( function_exists( '_wp_filter_post_meta_footnotes' ) ) {
		return;
	}
	// force_filtered_html_on_import is true we need to init the global styles kses filters.
	if ( $arg ) {
		_gutenberg_footnotes_kses_init_filters();
	}
	return $arg;
}

add_action( 'init', '_gutenberg_footnotes_kses_init' );
add_action( 'set_current_user', '_gutenberg_footnotes_kses_init' );
add_filter( 'force_filtered_html_on_import', '_gutenberg_footnotes_force_filtered_html_on_import_filter', 999 );
