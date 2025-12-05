<?php
/**
 * Plugin Name: Matomo Analytics - Ethical Stats. Powerful Insights.
 * Description: Privacy friendly, GDPR compliant and self-hosted. Matomo is the #1 Google Analytics alternative that gives you control of your data. Free and secure.
 * Author: Matomo
 * Author URI: https://matomo.org
 * Version: 5.3.3
 * Domain Path: /languages
 * WC requires at least: 2.4.0
 * WC tested up to: 10.1.1
 *
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 * phpcs:disable WordPress.Security.ValidatedSanitizedInput
 * phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
 * phpcs:disable WordPress.PHP.NoSilencedErrors.Discouraged
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

if ( ! defined( 'MATOMO_ANALYTICS_FILE' ) ) {
	define( 'MATOMO_ANALYTICS_FILE', __FILE__ );
}

if ( ! defined( 'MATOMO_MARKETPLACE_PLUGIN_NAME' ) ) {
	define( 'MATOMO_MARKETPLACE_PLUGIN_NAME', 'matomo-marketplace-for-wordpress/matomo-marketplace-for-wordpress.php' );
}

$GLOBALS['MATOMO_PLUGINS_ENABLED'] = array();

/** MATOMO_PLUGIN_FILES => used to check for updates etc */
$GLOBALS['MATOMO_PLUGIN_FILES'] = array( MATOMO_ANALYTICS_FILE );

add_action(
	'init',
	function () {
		load_plugin_textdomain( 'matomo', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}
);

function matomo_has_compatible_content_dir() {
	if ( ! empty( $_SERVER['MATOMO_WP_ROOT_PATH'] )
		 && file_exists( rtrim( $_SERVER['MATOMO_WP_ROOT_PATH'], '/' ) . '/wp-load.php' ) ) {
		return true;
	}

	if ( ! defined( 'WP_CONTENT_DIR' ) ) {
		return false;
	}

	$content_dir = rtrim( rtrim( WP_CONTENT_DIR, '/' ), DIRECTORY_SEPARATOR );
	$content_dir = wp_normalize_path( $content_dir );
	$abs_path    = wp_normalize_path( ABSPATH );

	$abs_paths = array(
		$abs_path . 'wp-content',
		$abs_path . '/wp-content',
		$abs_path . DIRECTORY_SEPARATOR . 'wp-content',
	);

	if ( in_array( $content_dir, $abs_paths, true ) ) {
		 return true;
	}

	$wpload_base = '../../../wp-load.php';
	$wpload_full = dirname( __FILE__ ) . '/' . $wpload_base;
	if ( file_exists( $wpload_full ) && is_readable( $wpload_full ) ) {
		return true;
	} elseif ( realpath( $wpload_full ) && file_exists( realpath( $wpload_full ) ) && is_readable( realpath( $wpload_full ) ) ) {
		return true;
	} elseif ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && file_exists( $_SERVER['SCRIPT_FILENAME'] ) ) {
		// seems symlinked... eg the wp-content dir or wp-content/plugins dir is symlinked from some very much other place...
		$wpload_full = dirname( $_SERVER['SCRIPT_FILENAME'] ) . '/' . $wpload_base;
		if ( file_exists( $wpload_full ) ) {
			return true;
		} elseif ( realpath( $wpload_full ) && file_exists( realpath( $wpload_full ) ) ) {
			return true;
		} elseif ( file_exists( dirname( $_SERVER['SCRIPT_FILENAME'] ) ) . '/wp-load.php' ) {
			return true;
		}
	}

	// look in plugins directory if there is a config file for us
	$wpload_config = dirname( __FILE__ ) . '/../matomo.wpload_dir.php';
	if ( file_exists( $wpload_config ) && is_readable( $wpload_config ) ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$content = @file_get_contents( $wpload_config ); // we do not include that file for security reasons
		if ( ! empty( $content ) ) {
			$content = str_replace( array( '<?php', 'exit;' ), '', $content );
			$content = preg_replace( '/\s/', '', $content );
			$content = trim( ltrim( trim( $content ), '#' ) ); // the path may be commented out # /abs/path
			if ( strpos( $content, DIRECTORY_SEPARATOR ) === 0 ) {
				$wpload_file = rtrim( $content, DIRECTORY_SEPARATOR ) . '/wp-load.php';
				return file_exists( $wpload_file ) && is_readable( $wpload_file );
			}
		}
	}

	return false;
}

function matomo_header_icon( $full = false ) {
	$file = 'logo';
	if ( $full ) {
		$file = 'logo-full';
	}
	echo '<img height="32" src="' . esc_url( plugins_url( 'assets/img/' . $file . '.png', MATOMO_ANALYTICS_FILE ) ) . '" class="matomo-header-icon">';
}

function matomo_is_app_request() {
	return ! empty( $_SERVER['SCRIPT_NAME'] )
	&& ( substr( $_SERVER['SCRIPT_NAME'], - 1 * strlen( 'matomo/app/index.php' ) ) === 'matomo/app/index.php' );
}

function matomo_has_tag_manager() {
	if ( defined( 'MATOMO_ENABLE_TAG_MANAGER' ) ) {
		return ! empty( MATOMO_ENABLE_TAG_MANAGER );
	}

	$is_multisite = function_exists( 'is_multisite' ) && is_multisite();
	if ( $is_multisite ) {
		return false;
	}

	return true;
}

function matomo_anonymize_value( $value ) {
	if ( is_string( $value ) && ! empty( $value ) ) {
		$values_to_anonymize = array(
			ABSPATH                                  => '$abs_path/',
			str_replace( '/', '\/', ABSPATH )        => '$abs_path\/',
			str_replace( '/', '\\', ABSPATH )        => '$abs_path\/',
			WP_CONTENT_DIR                           => '$WP_CONTENT_DIR/',
			str_replace( '/', '\\', WP_CONTENT_DIR ) => '$WP_CONTENT_DIR\\',
			home_url()                               => '$home_url',
			site_url()                               => '$site_url',
			DB_PASSWORD                              => '$DB_PASSWORD',
			DB_USER                                  => '$DB_USER',
			DB_HOST                                  => '$DB_HOST',
			DB_NAME                                  => '$DB_NAME',
		);
		$keys                = array( 'AUTH_KEY', 'SECURE_AUTH_KEY', 'LOGGED_IN_KEY', 'AUTH_SALT', 'NONCE_KEY', 'SECURE_AUTH_SALT', 'LOGGED_IN_SALT', 'NONCE_SALT' );
		foreach ( $keys as $key ) {
			if ( defined( $key ) ) {
				$const_value = constant( $key );
				if ( ! empty( $const_value ) && is_string( $const_value ) && strlen( $key ) > 3 ) {
					$values_to_anonymize[ $const_value ] = '$' . $key;
				}
			}
		}
		foreach ( $values_to_anonymize as $search => $replace ) {
			if ( $search ) {
				$value = str_replace( $search, $replace, $value );
			}
		}
		// replace anything like token_auth etc or md5 or sha1 ...
		$value = preg_replace( '/[[:xdigit:]]{31,80}/', 'TOKEN_REPLACED', $value );
	}

	return $value;
}

$GLOBALS['MATOMO_MARKETPLACE_PLUGINS'] = array();

function matomo_rel_path( $to_dir, $from_dir ) {
	$to_dir_parts   = array_values( array_filter( explode( DIRECTORY_SEPARATOR, $to_dir ) ) );
	$from_dir_parts = array_values( array_filter( explode( DIRECTORY_SEPARATOR, $from_dir ) ) );

	$to_index   = 0;
	$from_index = 0;

	$to_dir_segment_count   = count( $to_dir_parts );
	$from_dir_segment_count = count( $from_dir_parts );

	// skip over common parts of $to_dir and $from_dir
	for ( ; $to_index < $to_dir_segment_count && $from_index < $from_dir_segment_count && $to_dir_parts[ $to_index ] === $from_dir_parts[ $from_index ]; ++$to_index, ++$from_index );

	// ascend from $to_dir to common root it has with $from_dir
	$relative_path = str_repeat( '..' . DIRECTORY_SEPARATOR, count( $from_dir_parts ) - $from_index );

	// descend from common root to target in rest of $to_dir
	$rest = array_slice( $to_dir_parts, $to_index );
	if ( ! empty( $rest ) ) {
		$relative_path = $relative_path . implode( DIRECTORY_SEPARATOR, $rest );
	}

	return $relative_path;
}

function matomo_is_plugin_compatible( $wp_plugin_file ) {
	require_once __DIR__ . '/app/core/Version.php';

	$plugin_manifest_path = dirname( $wp_plugin_file ) . '/plugin.json';
	clearstatcache( false, $plugin_manifest_path );

	if ( ! is_file( $plugin_manifest_path )
		|| ! is_readable( $plugin_manifest_path )
	) {
		return false;
	}

	$modified_time = filemtime( $plugin_manifest_path );
	if ( false === $modified_time ) {
		return false;
	}

	$cache_key   = 'matomo_plugin_compatible_' . basename( $wp_plugin_file ) . '_' . \Piwik\Version::VERSION . '_' . $modified_time;
	$cache_value = get_transient( $cache_key );
	if ( false === $cache_value ) {
		// assume the plugin is not compatible in case the below code fails.
		// this way, the next request will work rather than trigger the same
		// error.
		$one_day = 24 * 60 * 60;
		set_transient( $cache_key, 0, $one_day );

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$plugin_manifest = file_get_contents( $plugin_manifest_path );
		$plugin_manifest = json_decode( $plugin_manifest, true );
		if ( empty( $plugin_manifest['require']['matomo'] )
			&& empty( $plugin_manifest['require']['piwik'] )
		) {
			return false;
		}

		$core_requirement = isset( $plugin_manifest['require']['matomo'] )
			? $plugin_manifest['require']['matomo']
			: $plugin_manifest['require']['piwik'];

		require_once __DIR__ . '/app/vendor/autoload.php';

		$dependency           = new \Piwik\Plugin\Dependency();
		$missing_dependencies = $dependency->getMissingDependencies( [ 'matomo' => $core_requirement ] );

		$is_compatible = empty( $missing_dependencies );
		$cache_value   = (int) $is_compatible;

		$two_months = 60 * 60 * 24 * 60;
		set_transient( $cache_key, $cache_value, $two_months );
	}

	return 1 === (int) $cache_value;
}

function matomo_filter_incompatible_plugins( &$plugin_list ) {
	if ( empty( $GLOBALS['MATOMO_MARKETPLACE_PLUGINS'] ) ) {
		return;
	}

	$incompatible_plugins = [];
	foreach ( $GLOBALS['MATOMO_MARKETPLACE_PLUGINS'] as $wp_plugin_file ) {
		if ( matomo_is_plugin_compatible( $wp_plugin_file ) ) {
			continue;
		}

		$plugin_name            = basename( dirname( $wp_plugin_file ) );
		$incompatible_plugins[] = $plugin_name;
	}

	$plugin_list = array_values( array_diff( $plugin_list, $incompatible_plugins ) );
}

function matomo_add_plugin( $plugins_directory, $wp_plugin_file, $is_marketplace_plugin = false ) {
	if ( ! in_array( $wp_plugin_file, $GLOBALS['MATOMO_PLUGIN_FILES'], true ) ) {
		$GLOBALS['MATOMO_PLUGIN_FILES'][] = $wp_plugin_file;
	}

	if ( empty( $GLOBALS['MATOMO_PLUGIN_DIRS'] ) ) {
		$GLOBALS['MATOMO_PLUGIN_DIRS'] = array();
	}

	if ( $is_marketplace_plugin && dirname( $wp_plugin_file ) === $plugins_directory ) {
		$GLOBALS['MATOMO_MARKETPLACE_PLUGINS'][] = $wp_plugin_file;
	}

	$GLOBALS['MATOMO_PLUGINS_ENABLED'][] = basename( $plugins_directory );
	$root_dir                            = dirname( $plugins_directory );
	foreach ( $GLOBALS['MATOMO_PLUGIN_DIRS'] as $path ) {
		if ( $path['pluginsPathAbsolute'] === $root_dir ) {
			return; // already added
		}
	}

	$matomo_dir  = __DIR__ . DIRECTORY_SEPARATOR . 'app';
	$webroot_dir = matomo_rel_path( $root_dir, $matomo_dir );

	$GLOBALS['MATOMO_PLUGIN_DIRS'][] = array(
		'pluginsPathAbsolute'        => $root_dir,
		'webrootDirRelativeToMatomo' => $webroot_dir,
	);
}

if ( matomo_is_app_request() || ! empty( $GLOBALS['MATOMO_LOADED_DIRECTLY'] ) ) {
	// prevent layout being broken when thegem theme is used. their lazy items class causes the reporting UI to not appear
	// because it creates a JS error because of escaping " too often. only breaks when " Activate image loading optimization (for desktops)"
	// is enabled in the general theme settings
	add_filter( 'thegem_lazy_items_need_process_content', '__return_false', 99999999, $args = 0 );
}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'WpMatomo.php';
require 'shared.php';
matomo_add_plugin( __DIR__ . '/plugins/WordPress', MATOMO_ANALYTICS_FILE );

new WpMatomo();
