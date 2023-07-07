<?php
/**
 * Plugin Name: WooCommerce Blocks
 * Plugin URI: https://github.com/woocommerce/woocommerce-gutenberg-products-block
 * Description: WooCommerce blocks for the Gutenberg editor.
 * Version: 10.2.4
 * Author: Automattic
 * Author URI: https://woocommerce.com
 * Text Domain:  woo-gutenberg-products-block
 * Requires at least: 6.2
 * Requires PHP: 7.3
 * WC requires at least: 7.5
 * WC tested up to: 7.6
 *
 * @package WooCommerce\Blocks
 * @internal This file is only used when running as a feature plugin.
 */

defined( 'ABSPATH' ) || exit;

$minimum_wp_version = '6.2';

if ( ! defined( 'WC_BLOCKS_IS_FEATURE_PLUGIN' ) ) {
	define( 'WC_BLOCKS_IS_FEATURE_PLUGIN', true );
}

// Declare compatibility with custom order tables for WooCommerce.
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

/**
 * Whether notices must be displayed in the current page (plugins and WooCommerce pages).
 *
 * @since 2.5.0
 */
function should_display_compatibility_notices() {
	$current_screen = get_current_screen();

	if ( ! isset( $current_screen ) ) {
		return false;
	}

	$is_plugins_page     =
		property_exists( $current_screen, 'id' ) &&
		'plugins' === $current_screen->id;
	$is_woocommerce_page =
		property_exists( $current_screen, 'parent_base' ) &&
		'woocommerce' === $current_screen->parent_base;

	return $is_plugins_page || $is_woocommerce_page;
}

if ( version_compare( $GLOBALS['wp_version'], $minimum_wp_version, '<' ) ) {
	/**
	 * Outputs for an admin notice about running WooCommerce Blocks on outdated WordPress.
	 *
	 * @since 2.5.0
	 */
	function woocommerce_blocks_admin_unsupported_wp_notice() {
		if ( should_display_compatibility_notices() ) {
			?>
			<div class="notice notice-error">
				<p><?php esc_html_e( 'The WooCommerce Blocks feature plugin requires a more recent version of WordPress and has been paused. Please update WordPress to continue enjoying WooCommerce Blocks.', 'woocommerce' ); ?></p>
			</div>
			<?php
		}
	}
	add_action( 'admin_notices', 'woocommerce_blocks_admin_unsupported_wp_notice' );
	return;
}

/**
 * Returns whether the current version is a development version
 * Note this relies on composer.json version, not plugin version.
 * Development installs of the plugin don't have a version defined in
 * composer json.
 *
 * @return bool True means the current version is a development version.
 */
function woocommerce_blocks_is_development_version() {
	$composer_file = __DIR__ . '/composer.json';
	if ( ! is_readable( $composer_file ) ) {
		return false;
	}
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- including local file
	$composer_config = json_decode( file_get_contents( $composer_file ), true );
	return ! isset( $composer_config['version'] );
}

/**
 * If development version is detected and the Jetpack constant is not defined, show a notice.
 */
if ( woocommerce_blocks_is_development_version() && ( ! defined( 'JETPACK_AUTOLOAD_DEV' ) || true !== JETPACK_AUTOLOAD_DEV ) ) {
	add_action(
		'admin_notices',
		function () {
			echo '<div class="error"><p>';
			printf(
				/* translators: %1$s is referring to a php constant name, %2$s is referring to the wp-config.php file. */
				esc_html__( 'WooCommerce Blocks development mode requires the %1$s constant to be defined and true in your %2$s file. Otherwise you are loading the blocks package from WooCommerce core.', 'woocommerce' ),
				'JETPACK_AUTOLOAD_DEV',
				'wp-config.php'
			);
			echo '</p></div>';
		}
	);
}


/**
 * Autoload packages.
 *
 * The package autoloader includes version information which prevents classes in this feature plugin
 * conflicting with WooCommerce core.
 *
 * We want to fail gracefully if `composer install` has not been executed yet, so we are checking for the autoloader.
 * If the autoloader is not present, let's log the failure and display a nice admin notice.
 */
$autoloader = __DIR__ . '/vendor/autoload_packages.php';
if ( is_readable( $autoloader ) ) {
	require $autoloader;
} else {
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		error_log(  // phpcs:ignore
			sprintf(
				/* translators: 1: composer command. 2: plugin directory */
				esc_html__( 'Your installation of the WooCommerce Blocks feature plugin is incomplete. Please run %1$s within the %2$s directory.', 'woocommerce' ),
				'`composer install`',
				'`' . esc_html( str_replace( ABSPATH, '', __DIR__ ) ) . '`'
			)
		);
	}
	/**
	 * Outputs an admin notice if composer install has not been ran.
	 */
	add_action(
		'admin_notices',
		function () {
			?>
		<div class="notice notice-error">
			<p>
				<?php
				printf(
					/* translators: 1: composer command. 2: plugin directory */
					esc_html__( 'Your installation of the WooCommerce Blocks feature plugin is incomplete. Please run %1$s within the %2$s directory.', 'woocommerce' ),
					'<code>composer install</code>',
					'<code>' . esc_html( str_replace( ABSPATH, '', __DIR__ ) ) . '</code>'
				);
				?>
			</p>
		</div>
			<?php
		}
	);
	return;
}

add_action( 'plugins_loaded', array( '\Automattic\WooCommerce\Blocks\Package', 'init' ) );

/**
 * WordPress will look for translation in the following order:
 * - wp-content/plugins/woocommerce-blocks/languages/woo-gutenberg-products-block-{locale}-{handle}.json
 * - wp-content/plugins/woocommerce-blocks/languages/woo-gutenberg-products-block-{locale}-{md5-handle}.json
 * - wp-content/languages/plugins/woo-gutenberg-products-block-{locale}-{md5-handle}.json
 *
 * We check if the last one exists, and if it doesn't we try to load the
 * corresponding JSON file from the WC Core.
 *
 * @param string|false $file   Path to the translation file to load. False if there isn't one.
 * @param string       $handle Name of the script to register a translation domain to.
 * @param string       $domain The text domain.
 *
 * @return string|false        Path to the translation file to load. False if there isn't one.
 */
function load_woocommerce_core_js_translation( $file, $handle, $domain ) {
	if ( 'woo-gutenberg-products-block' !== $domain ) {
		return $file;
	}

	$lang_dir = WP_LANG_DIR . '/plugins';

	/**
	 * We only care about the translation file of the feature plugin in the
	 * wp-content/languages folder.
	 */
	if ( false === strpos( $file, $lang_dir ) ) {
		return $file;
	}

	// If the translation file for feature plugin exist, use it.
	if ( is_readable( $file ) ) {
		return $file;
	}

	global $wp_scripts;

	if ( ! isset( $wp_scripts->registered[ $handle ], $wp_scripts->registered[ $handle ]->src ) ) {
		return $file;
	}

	$handle_src      = explode( '/build/', $wp_scripts->registered[ $handle ]->src );
	$handle_filename = $handle_src[1];
	$locale          = determine_locale();
	$lang_dir        = WP_LANG_DIR . '/plugins';

	// Translations are always based on the unminified filename.
	if ( substr( $handle_filename, -7 ) === '.min.js' ) {
		$handle_filename = substr( $handle_filename, 0, -7 ) . '.js';
	}

	$core_path_md5 = md5( 'packages/woocommerce-blocks/build/' . $handle_filename );

	/**
	 * Return file path of the corresponding translation file in the WC Core is
	 * enough because `load_script_translations()` will check for its existence
	 * before loading it.
	 */
	return $lang_dir . '/woocommerce-' . $locale . '-' . $core_path_md5 . '.json';
}

add_filter( 'load_script_translation_file', 'load_woocommerce_core_js_translation', 10, 3 );

/**
 * Filter translations so we can retrieve translations from Core when the original and the translated
 * texts are the same (which happens when translations are missing).
 *
 * @param string $translation Translated text based on WC Blocks translations.
 * @param string $text        Text to translate.
 * @param string $domain      The text domain.
 * @return string WC Blocks translation. In case it's the same as $text, Core translation.
 */
function woocommerce_blocks_get_php_translation_from_core( $translation, $text, $domain ) {
	if ( 'woo-gutenberg-products-block' !== $domain ) {
		return $translation;
	}

	// When translation is the same, that could mean the string is not translated.
	// In that case, load it from core.
	if ( $translation === $text ) {
		return translate( $text, 'woocommerce' ); // phpcs:ignore WordPress.WP.I18n.LowLevelTranslationFunction, WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.TextDomainMismatch
	}
	return $translation;
}

add_filter( 'gettext', 'woocommerce_blocks_get_php_translation_from_core', 10, 3 );

/**
 * Add notice to the admin dashboard if the plugin is outdated.
 *
 * @see https://github.com/woocommerce/woocommerce-blocks/issues/5587
 */
function woocommerce_blocks_plugin_outdated_notice() {
	$is_active =
		is_plugin_active( 'woo-gutenberg-products-block/woocommerce-gutenberg-products-block.php' ) ||
		is_plugin_active( 'woocommerce-gutenberg-products-block/woocommerce-gutenberg-products-block.php' ) ||
		is_plugin_active( 'woocommerce-blocks/woocommerce-gutenberg-products-block.php' );

	if ( ! $is_active ) {
		return;
	}

	$woocommerce_blocks_path = \Automattic\WooCommerce\Blocks\Package::get_path();

	/**
	 * Check the current WC Blocks path. If the WC Blocks plugin is active but
	 * the current path is from the WC Core, we can consider the plugin is
	 * outdated because Jetpack Autoloader always loads the newer package.
	 */
	if ( ! strpos( $woocommerce_blocks_path, 'packages/woocommerce-blocks' ) ) {
		return;
	}

	if ( should_display_compatibility_notices() ) {
		?>
		<div class="notice notice-warning">
			<p><?php esc_html_e( 'You have WooCommerce Blocks installed, but the WooCommerce bundled version is running because it is more up-to-date. This may cause unexpected compatibility issues. Please update the WooCommerce Blocks plugin.', 'woocommerce' ); ?></p>
		</div>
		<?php
	}
}

add_action( 'admin_notices', 'woocommerce_blocks_plugin_outdated_notice' );

/**
 * Disable the Interactivity API if the required `WP_HTML_Tag_Processor` class
 * doesn't exist, regardless of whether it was enabled manually.
 *
 * @param bool $enabled Current filter value.
 * @return bool True if _also_ the `WP_HTML_Tag_Processor` class was found.
 */
function woocommerce_blocks_has_wp_html_tag_processor( $enabled ) {
	return $enabled && class_exists( 'WP_HTML_Tag_Processor' );
}
add_filter(
	'woocommerce_blocks_enable_interactivity_api',
	'woocommerce_blocks_has_wp_html_tag_processor',
	999
);

/**
 * Load and set up the Interactivity API if enabled.
 */
function woocommerce_blocks_interactivity_setup() {
	// phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
	$is_enabled = apply_filters(
		'woocommerce_blocks_enable_interactivity_api',
		false
	);

	if ( $is_enabled ) {
		require_once __DIR__ . '/src/Interactivity/woo-directives.php';
	}
}
add_action( 'plugins_loaded', 'woocommerce_blocks_interactivity_setup' );
