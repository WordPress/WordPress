<?php
/**
 * Load API functions, register scripts and actions, etc.
 *
 * @package gutenberg
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Silence is golden.' );
}

define( 'IS_GUTENBERG_PLUGIN', true );

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/upgrade.php';

// Load auto-generated build registration.
$build_registration = plugin_dir_path( __DIR__ ) . 'build/index.php';
if ( file_exists( $build_registration ) ) {
	require_once $build_registration;
}

/**
 * Checks whether the Gutenberg experiment is enabled.
 *
 * @since 6.7.0
 *
 * @param string $name The name of the experiment.
 *
 * @return bool True when the experiment is enabled.
 */
function gutenberg_is_experiment_enabled( $name ) {
	// Special handling for active_templates - check if the active_templates option exists.
	// This is not stored in the experiments array but as a separate option.
	if ( 'active_templates' === $name ) {
		return is_array( get_option( 'active_templates' ) );
	}

	$experiments = get_option( 'gutenberg-experiments' );
	return ! empty( $experiments[ $name ] );
}

// These files only need to be loaded if within a rest server instance.
// which this class will exist if that is the case.
if ( class_exists( 'WP_REST_Controller' ) ) {
	if ( ! class_exists( 'WP_REST_Block_Editor_Settings_Controller' ) ) {
		require_once __DIR__ . '/experimental/class-wp-rest-block-editor-settings-controller.php';
	}

	// WordPress 6.8 compat.
	require __DIR__ . '/compat/wordpress-6.8/rest-api.php';

	// WordPress 6.9 compat.
	require __DIR__ . '/compat/wordpress-6.9/class-gutenberg-rest-attachments-controller-6-9.php';
	require __DIR__ . '/compat/wordpress-6.9/class-gutenberg-rest-static-templates-controller.php';
	require __DIR__ . '/compat/wordpress-6.9/template-activate.php';
	require __DIR__ . '/compat/wordpress-6.9/block-bindings.php';
	require __DIR__ . '/compat/wordpress-6.9/post-data-block-bindings.php';
	require __DIR__ . '/compat/wordpress-6.9/term-data-block-bindings.php';
	require __DIR__ . '/compat/wordpress-6.9/rest-api.php';
	require __DIR__ . '/compat/wordpress-6.9/class-gutenberg-hierarchical-sort.php';
	require __DIR__ . '/compat/wordpress-6.9/block-comments.php';
	require __DIR__ . '/compat/wordpress-6.9/class-gutenberg-rest-comment-controller-6-9.php';

	// WordPress 7.0 compat.
	require __DIR__ . '/compat/wordpress-7.0/rest-api.php';

	// Plugin specific code.
	require_once __DIR__ . '/class-wp-rest-global-styles-controller-gutenberg.php';
	require_once __DIR__ . '/class-wp-rest-edit-site-export-controller-gutenberg.php';
	require_once __DIR__ . '/rest-api.php';

	require_once __DIR__ . '/experimental/rest-api.php';
	require_once __DIR__ . '/experimental/kses-allowed-html.php';
}

// Experimental signaling server.
if ( ! class_exists( 'Gutenberg_HTTP_Singling_Server' ) ) {
	require_once __DIR__ . '/experimental/sync/class-gutenberg-http-signaling-server.php';
}

require_once __DIR__ . '/experimental/editor-settings.php';
require_once __DIR__ . '/experimental/rest-api-overrides.php';

// Gutenberg plugin compat.
require __DIR__ . '/compat/plugin/edit-site-routes-backwards-compat.php';
require __DIR__ . '/compat/plugin/fonts.php';

// WordPress 6.8 compat.
// Note: admin-bar.php (69271) was reverted in Gutenberg 20.8.0. See https://github.com/WordPress/gutenberg/pull/69974.
require __DIR__ . '/compat/wordpress-6.8/preload.php';
require __DIR__ . '/compat/wordpress-6.8/blocks.php';
require __DIR__ . '/compat/wordpress-6.8/functions.php';
require __DIR__ . '/compat/wordpress-6.8/site-editor.php';
require __DIR__ . '/compat/wordpress-6.8/class-gutenberg-rest-user-controller.php';
require __DIR__ . '/compat/wordpress-6.8/block-template-utils.php';
require __DIR__ . '/compat/wordpress-6.8/site-preview.php';

// WordPress 6.9 compat.
require __DIR__ . '/compat/wordpress-6.9/customizer-preview-custom-css.php';
require __DIR__ . '/compat/wordpress-6.9/command-palette.php';
require __DIR__ . '/compat/wordpress-6.9/preload.php';
require __DIR__ . '/compat/wordpress-6.9/client-assets.php';

// WordPress 7.0 compat.
require __DIR__ . '/compat/wordpress-7.0/php-only-blocks.php';
require __DIR__ . '/compat/wordpress-7.0/blocks.php';

// Experimental features.
require __DIR__ . '/experimental/block-editor-settings-mobile.php';
require __DIR__ . '/experimental/blocks.php';
require __DIR__ . '/experimental/navigation-theme-opt-in.php';
require __DIR__ . '/experimental/kses.php';
require __DIR__ . '/experimental/synchronization.php';
require __DIR__ . '/experimental/script-modules.php';
require __DIR__ . '/experimental/pages/gutenberg-boot.php';
require __DIR__ . '/experimental/posts/load.php';

if ( gutenberg_is_experiment_enabled( 'gutenberg-no-tinymce' ) ) {
	require __DIR__ . '/experimental/disable-tinymce.php';
}

// Load the BC Layer to avoid fatal errors of extenders using the Fonts API.
// @core-merge: do not merge the BC layer files into WordPress Core.
require __DIR__ . '/experimental/font-face/bc-layer/class-wp-fonts-provider.php';
require __DIR__ . '/experimental/font-face/bc-layer/class-wp-fonts-utils.php';
require __DIR__ . '/experimental/font-face/bc-layer/class-wp-fonts.php';
require __DIR__ . '/experimental/font-face/bc-layer/class-wp-fonts-provider-local.php';
require __DIR__ . '/experimental/font-face/bc-layer/class-wp-fonts-resolver.php';
require __DIR__ . '/experimental/font-face/bc-layer/class-gutenberg-fonts-api-bc-layer.php';
require __DIR__ . '/experimental/font-face/bc-layer/webfonts-deprecations.php';
require __DIR__ . '/experimental/font-face/bc-layer/class-wp-webfonts-utils.php';
require __DIR__ . '/experimental/font-face/bc-layer/class-wp-webfonts-provider.php';
require __DIR__ . '/experimental/font-face/bc-layer/class-wp-webfonts-provider-local.php';
require __DIR__ . '/experimental/font-face/bc-layer/class-wp-webfonts.php';
require __DIR__ . '/experimental/font-face/bc-layer/class-wp-web-fonts.php';

// Plugin specific code.
require __DIR__ . '/script-loader.php';
require __DIR__ . '/global-styles-and-settings.php';
require __DIR__ . '/class-wp-theme-json-data-gutenberg.php';
require __DIR__ . '/class-wp-theme-json-gutenberg.php';
require __DIR__ . '/class-wp-theme-json-resolver-gutenberg.php';
require __DIR__ . '/class-wp-theme-json-schema-gutenberg.php';
require __DIR__ . '/class-wp-duotone-gutenberg.php';
require __DIR__ . '/blocks.php';
require __DIR__ . '/block-editor-settings.php';
require __DIR__ . '/client-assets.php';
require __DIR__ . '/mathml-kses.php';
require __DIR__ . '/demo.php';
require __DIR__ . '/experiments-page.php';
require __DIR__ . '/interactivity-api.php';
require __DIR__ . '/block-template-utils.php';

// Copied package PHP files.
if ( is_dir( __DIR__ . '/../build/scripts/style-engine' ) ) {
	require_once __DIR__ . '/../build/scripts/style-engine/class-wp-style-engine-css-declarations-gutenberg.php';
	require_once __DIR__ . '/../build/scripts/style-engine/class-wp-style-engine-css-rule-gutenberg.php';
	require_once __DIR__ . '/../build/scripts/style-engine/class-wp-style-engine-css-rules-store-gutenberg.php';
	require_once __DIR__ . '/../build/scripts/style-engine/class-wp-style-engine-processor-gutenberg.php';
	require_once __DIR__ . '/../build/scripts/style-engine/class-wp-style-engine-gutenberg.php';
	require_once __DIR__ . '/../build/scripts/style-engine/style-engine-gutenberg.php';
}

// Block supports overrides.
require __DIR__ . '/block-supports/settings.php';
require __DIR__ . '/block-supports/elements.php';
require __DIR__ . '/block-supports/colors.php';
require __DIR__ . '/block-supports/typography.php';
require __DIR__ . '/block-supports/border.php';
require __DIR__ . '/block-supports/layout.php';
require __DIR__ . '/block-supports/position.php';
require __DIR__ . '/block-supports/spacing.php';
require __DIR__ . '/block-supports/dimensions.php';
require __DIR__ . '/block-supports/duotone.php';
require __DIR__ . '/block-supports/shadow.php';
require __DIR__ . '/block-supports/background.php';
require __DIR__ . '/block-supports/block-style-variations.php';
require __DIR__ . '/block-supports/aria-label.php';
require __DIR__ . '/block-supports/block-visibility.php';

// Client-side media processing.
if ( gutenberg_is_experiment_enabled( 'gutenberg-media-processing' ) ) {
	require_once __DIR__ . '/experimental/media/load.php';
}

// Interactivity API full-page client-side navigation.
if ( gutenberg_is_experiment_enabled( 'gutenberg-full-page-client-side-navigation' ) ) {
	require __DIR__ . '/experimental/interactivity-api/class-gutenberg-interactivity-api-full-page-navigation.php';
	Gutenberg_Interactivity_API_Full_Page_Navigation::instance();
}
