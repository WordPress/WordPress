<?php
/**
 * Functions to register client-side assets (scripts and stylesheets) specific
 * for the Gutenberg editor plugin.
 *
 * @package gutenberg
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Silence is golden.' );
}

/**
 * Retrieves the root plugin path.
 *
 * @since 0.1.0
 *
 * @return string Root path to the gutenberg plugin.
 */
function gutenberg_dir_path() {
	return plugin_dir_path( __DIR__ );
}

/**
 * Retrieves a URL to a file in the gutenberg plugin.
 *
 * @since 0.1.0
 *
 * @param  string $path Relative path of the desired file.
 *
 * @return string       Fully qualified URL pointing to the desired file.
 */
function gutenberg_url( $path ) {
	return plugins_url( $path, __DIR__ );
}

/**
 * Filters the default translation file load behavior to load the Gutenberg
 * plugin translation file, if available.
 *
 * @param string|false $file   Path to the translation file to load. False if
 *                             there isn't one.
 * @param string       $handle Name of the script to register a translation
 *                             domain to.
 *
 * @return string|false Filtered path to the Gutenberg translation file, if
 *                      available.
 */
function gutenberg_override_translation_file( $file, $handle ) {
	if ( ! $file ) {
		return $file;
	}

	// Ignore scripts whose handle does not have the "wp-" prefix.
	if ( ! str_starts_with( $handle, 'wp-' ) ) {
		return $file;
	}

	// Ignore scripts that are not found in the expected `build/scripts/` location.
	$script_path = gutenberg_dir_path() . 'build/scripts/' . substr( $handle, 3 ) . '/index.min.js';
	if ( ! file_exists( $script_path ) ) {
		return $file;
	}

	/*
	 * The default file will be in the plugins language directory, omitting the
	 * domain since Gutenberg assigns the script translations as the default.
	 *
	 * Example: /www/wp-content/languages/plugins/de_DE-07d88e6a803e01276b9bfcc1203e862e.json
	 *
	 * The logic of `load_script_textdomain` is such that it will assume to
	 * search in the plugins language directory, since the assigned source of
	 * the overridden Gutenberg script originates in the plugins directory.
	 *
	 * The plugin translation files each begin with the slug of the plugin, so
	 * it's a simple matter of prepending the Gutenberg plugin slug.
	 */
	$path_parts              = pathinfo( $file );
	$plugin_translation_file = (
		$path_parts['dirname'] .
		'/gutenberg-' .
		$path_parts['basename']
	);

	return $plugin_translation_file;
}
add_filter( 'load_script_translation_file', 'gutenberg_override_translation_file', 10, 2 );

/**
 * Handle special case dependencies for wp-block-library that depend on runtime conditions.
 *
 * This adds the 'editor' dependency conditionally based on experiments and classic block requirements.
 * All other script registrations are handled by the auto-generated build/scripts.php file.
 *
 * @param WP_Scripts $scripts WP_Scripts instance.
 */
function gutenberg_register_block_library_script_special_case( $scripts ) {
	$handle = 'wp-block-library';
	$script = $scripts->query( $handle, 'registered' );
	if (
		! gutenberg_is_experiment_enabled( 'gutenberg-no-tinymce' ) ||
		! empty( $_GET['requiresTinymce'] ) ||
		gutenberg_post_being_edited_requires_classic_block()
	) {
		if ( ! in_array( 'editor', $script->deps, true ) ) {
			$script->deps[] = 'editor';
		}
	}
}
add_action( 'wp_default_scripts', 'gutenberg_register_block_library_script_special_case', 11 );

/**
 * Registers WordPress package styles with complex requirements.
 *
 * Simple styles (main style.css with inferred dependencies) are auto-registered
 * via build/styles.php at default priority (10). This function runs at priority 15 to handle:
 * - Multiple style files per package (content.css, classic.css, etc.)
 * - Non-WordPress dependencies (dashicons, common, forms)
 * - Custom handles that don't match wp-{package} pattern
 * - Conditional dependencies based on theme/settings
 * - Dynamic filename logic
 *
 * These override calls will replace the auto-registered versions as needed.
 *
 * @since 6.7.0
 *
 * @global array $editor_styles
 *
 * @param WP_Styles $styles WP_Styles instance.
 */
function gutenberg_register_packages_styles( $styles ) {
	// When in production, use the plugin's version as the asset version;
	// else (for development or test) default to use the current time.
	$version = defined( 'GUTENBERG_VERSION' ) && ! SCRIPT_DEBUG ? GUTENBERG_VERSION : time();

	// wp-components: add dashicons (icon font dependency)
	$styles->query( 'wp-components', 'registered' )->deps[] = 'dashicons';

	// wp-edit-post: add wp-edit-blocks (custom handle not auto-inferred)
	$styles->query( 'wp-edit-post', 'registered' )->deps[] = 'wp-edit-blocks';

	// wp-edit-site: add core WP styles and custom handles
	$edit_site_style         = $styles->query( 'wp-edit-site', 'registered' );
	$edit_site_style->deps[] = 'common';
	$edit_site_style->deps[] = 'forms';
	$edit_site_style->deps[] = 'wp-block-library-editor';

	// wp-edit-widgets: add wp-edit-blocks (custom handle not auto-inferred)
	$styles->query( 'wp-edit-widgets', 'registered' )->deps[] = 'wp-edit-blocks';

	// wp-customize-widgets: add wp-edit-blocks (custom handle not auto-inferred)
	$styles->query( 'wp-customize-widgets', 'registered' )->deps[] = 'wp-edit-blocks';

	gutenberg_override_style(
		$styles,
		'wp-block-editor-content',
		gutenberg_url( 'build/styles/block-editor/content.css' ),
		array( 'wp-components' ),
		$version
	);
	$styles->add_data( 'wp-block-editor-content', 'rtl', 'replace' );

	$block_library_filename = wp_should_load_separate_core_block_assets() ? 'common' : 'style';
	gutenberg_override_style(
		$styles,
		'wp-block-library',
		gutenberg_url( 'build/styles/block-library/' . $block_library_filename . '.css' ),
		array(),
		$version
	);
	$styles->add_data( 'wp-block-library', 'rtl', 'replace' );
	$styles->add_data( 'wp-block-library', 'path', gutenberg_dir_path() . 'build/styles/block-library/' . $block_library_filename . '.css' );

	// Only add CONTENT styles here that should be enqueued in the iframe!
	$wp_edit_blocks_dependencies = array(
		'wp-components',
		// This need to be added before the block library styles,
		// The block library styles override the "reset" styles.
		'wp-reset-editor-styles',
		'wp-block-library',
		// Until #37466, we can't specifically add them as editor styles yet,
		// so we must hard-code it here as a dependency.
		'wp-block-editor-content',
	);

	// Only load the default layout and margin styles for themes without theme.json file.
	if ( ! wp_theme_has_theme_json() ) {
		$wp_edit_blocks_dependencies[] = 'wp-editor-classic-layout-styles';
	}

	global $editor_styles;
	if ( current_theme_supports( 'wp-block-styles' ) && ( ! is_array( $editor_styles ) || count( $editor_styles ) === 0 ) ) {
		// Include opinionated block styles if the theme supports block styles and no $editor_styles are declared, so the editor never appears broken.
		$wp_edit_blocks_dependencies[] = 'wp-block-library-theme';
	}

	gutenberg_override_style(
		$styles,
		'wp-reset-editor-styles',
		gutenberg_url( 'build/styles/block-library/reset.css' ),
		array( 'common', 'forms' ), // Make sure the reset is loaded after the default WP Admin styles.
		$version
	);
	$styles->add_data( 'wp-reset-editor-styles', 'rtl', 'replace' );

	gutenberg_override_style(
		$styles,
		'wp-editor-classic-layout-styles',
		gutenberg_url( 'build/styles/edit-post/classic.css' ),
		array(),
		$version
	);
	$styles->add_data( 'wp-editor-classic-layout-styles', 'rtl', 'replace' );

	gutenberg_override_style(
		$styles,
		'wp-block-library-editor',
		gutenberg_url( 'build/styles/block-library/editor.css' ),
		array(),
		$version
	);
	$styles->add_data( 'wp-block-library-editor', 'rtl', 'replace' );

	gutenberg_override_style(
		$styles,
		'wp-edit-blocks',
		gutenberg_url( 'build/styles/block-library/editor.css' ),
		$wp_edit_blocks_dependencies,
		$version
	);
	$styles->add_data( 'wp-edit-blocks', 'rtl', 'replace' );

	gutenberg_override_style(
		$styles,
		'wp-block-library-theme',
		gutenberg_url( 'build/styles/block-library/theme.css' ),
		array(),
		$version
	);
	$styles->add_data( 'wp-block-library-theme', 'rtl', 'replace' );
}
add_action( 'wp_default_styles', 'gutenberg_register_packages_styles', 15 );

/**
 * Fetches, processes and compiles stored core styles, then combines and renders them to the page.
 * Styles are stored via the Style Engine API.
 *
 * This hook also exists, and should be backported to Core in future versions.
 * However, it is envisaged that Gutenberg will continue to use the Style Engine's `gutenberg_*` functions and `_Gutenberg` classes to aid continuous development.
 *
 * @since 6.1
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-style-engine/
 *
 * @param array $options {
 *     Optional. An array of options to pass to gutenberg_style_engine_get_stylesheet_from_context(). Default empty array.
 *
 *     @type bool $optimize Whether to optimize the CSS output, e.g., combine rules. Default is `false`.
 *     @type bool $prettify Whether to add new lines and indents to output. Default is the test of whether the global constant `SCRIPT_DEBUG` is defined.
 * }
 *
 * @return void
 */
function gutenberg_enqueue_stored_styles( $options = array() ) {
	$is_block_theme   = wp_is_block_theme();
	$is_classic_theme = ! $is_block_theme;

	/*
	 * For block themes, print stored styles in the header.
	 * For classic themes, in the footer.
	 */
	if (
		( $is_block_theme && doing_action( 'wp_footer' ) ) ||
		( $is_classic_theme && doing_action( 'wp_enqueue_scripts' ) )
	) {
		return;
	}

	$core_styles_keys         = array( 'block-supports' );
	$compiled_core_stylesheet = '';
	$style_tag_id             = 'core';
	foreach ( $core_styles_keys as $style_key ) {
		// Adds comment if code is prettified to identify core styles sections in debugging.
		$should_prettify = isset( $options['prettify'] ) ? true === $options['prettify'] : SCRIPT_DEBUG;
		if ( $should_prettify ) {
			$compiled_core_stylesheet .= "/**\n * Core styles: $style_key\n */\n";
		}
		// Chains core store ids to signify what the styles contain.
		$style_tag_id             .= '-' . $style_key;
		$compiled_core_stylesheet .= gutenberg_style_engine_get_stylesheet_from_context( $style_key, $options );
	}

	// Combines Core styles.
	if ( ! empty( $compiled_core_stylesheet ) ) {
		wp_register_style( $style_tag_id, false, array(), true );
		wp_add_inline_style( $style_tag_id, $compiled_core_stylesheet );
		wp_enqueue_style( $style_tag_id );
	}

	// If there are any other stores registered by themes etc., print them out.
	$additional_stores = WP_Style_Engine_CSS_Rules_Store_Gutenberg::get_stores();

	/*
	 * Since the corresponding action hook in Core is removed below,
	 * this function should still honour any styles stored using the Core Style Engine store.
	 */
	if ( class_exists( 'WP_Style_Engine_CSS_Rules_Store' ) ) {
		$additional_stores = array_merge( $additional_stores, WP_Style_Engine_CSS_Rules_Store::get_stores() );
	}

	foreach ( array_keys( $additional_stores ) as $store_name ) {
		if ( in_array( $store_name, $core_styles_keys, true ) ) {
			continue;
		}
		$styles = gutenberg_style_engine_get_stylesheet_from_context( $store_name, $options );
		if ( ! empty( $styles ) ) {
			$key = "wp-style-engine-$store_name";
			wp_register_style( $key, false, array(), true );
			wp_add_inline_style( $key, $styles );
			wp_enqueue_style( $key );
		}
	}
}

/**
 * Registers vendor JavaScript files to be used as dependencies of the editor
 * and plugins.
 *
 * This function is called from a script during the plugin build process, so it
 * should not call any WordPress PHP functions.
 *
 * @since 13.0
 *
 * @param WP_Scripts $scripts WP_Scripts instance.
 */
function gutenberg_register_vendor_scripts( $scripts ) {
	$extension = SCRIPT_DEBUG ? '.js' : '.min.js';

	gutenberg_override_script(
		$scripts,
		'react',
		gutenberg_url( 'build/scripts/vendors/react' . $extension ),
		// See https://github.com/pmmmwh/react-refresh-webpack-plugin/blob/main/docs/TROUBLESHOOTING.md#externalising-react.
		SCRIPT_DEBUG ? array( 'wp-react-refresh-entry', 'wp-polyfill' ) : array( 'wp-polyfill' ),
		'18'
	);
	gutenberg_override_script(
		$scripts,
		'react-dom',
		gutenberg_url( 'build/scripts/vendors/react-dom' . $extension ),
		array( 'react' ),
		'18'
	);

	gutenberg_override_script(
		$scripts,
		'react-jsx-runtime',
		gutenberg_url( 'build/scripts/vendors/react-jsx-runtime' . $extension ),
		array( 'react' ),
		'18'
	);
}
add_action( 'wp_default_scripts', 'gutenberg_register_vendor_scripts' );

/**
 * Registers or re-registers Gutenberg Script Modules.
 *
 * Script modules that are registered by Core will be re-registered by Gutenberg.
 *
 * @since 19.3.0
 */
function gutenberg_define_interactivity_modules_support() {
	// Load the auto-generated module registry.
	$modules_registry_file = gutenberg_dir_path() . 'build/modules/index.php';
	if ( ! file_exists( $modules_registry_file ) ) {
		return;
	}

	$modules = require $modules_registry_file;

	// Add client navigation support to block library modules.
	foreach ( $modules as $module ) {
		if ( str_starts_with( $module['id'], '@wordpress/block-library' ) && method_exists( 'WP_Interactivity_API', 'add_client_navigation_support_to_script_module' ) ) {
			wp_interactivity()->add_client_navigation_support_to_script_module( $module['id'] );
		}
	}
}
remove_action( 'wp_default_scripts', 'wp_define_interactivity_modules_support' );
add_action( 'wp_default_scripts', 'gutenberg_define_interactivity_modules_support' );

/**
 * Always remove the Core action hook while gutenberg_enqueue_stored_styles() exists to avoid styles being printed twice.
 * This is also because gutenberg_enqueue_stored_styles uses the Style Engine's `gutenberg_*` functions and `_Gutenberg` classes,
 * which are in continuous development and generally ahead of Core.
 */
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_stored_styles' );
remove_action( 'wp_footer', 'wp_enqueue_stored_styles', 1 );

// Enqueue stored styles.
add_action( 'wp_enqueue_scripts', 'gutenberg_enqueue_stored_styles' );
add_action( 'wp_footer', 'gutenberg_enqueue_stored_styles', 1 );

add_action( 'enqueue_block_editor_assets', 'gutenberg_enqueue_latex_to_mathml_loader' );
function gutenberg_enqueue_latex_to_mathml_loader() {
	wp_enqueue_script_module( '@wordpress/latex-to-mathml/loader' );
}
