<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit; // if accessed directly
}

if ( ! defined( 'MATOMO_UPLOAD_DIR' ) ) {
	define( 'MATOMO_UPLOAD_DIR', 'matomo' );
}
if ( ! defined( 'MATOMO_CONFIG_PATH' ) ) {
	define( 'MATOMO_CONFIG_PATH', 'config/config.ini.php' );
}
if ( ! defined( 'MATOMO_JS_NAME' ) ) {
	define( 'MATOMO_JS_NAME', 'matomo.js' );
}
if ( ! defined( 'MATOMO_DATABASE_PREFIX' ) ) {
	define( 'MATOMO_DATABASE_PREFIX', 'matomo_' );
}
/**
 * @param string $class_name
 */
function matomo_plugin_autoloader( $class_name ) {
	$root_namespace      = 'WpMatomo';
	$root_len            = strlen( $root_namespace ) + 1; // +1 for namespace separator
	$namespace_separator = '\\';

	if ( substr( $class_name, 0, $root_len ) === $root_namespace . $namespace_separator ) {
		$class_name = str_replace( '.', '', str_replace( $namespace_separator, DIRECTORY_SEPARATOR, substr( $class_name, $root_len ) ) );
		require_once __DIR__ . '/classes' . DIRECTORY_SEPARATOR . $root_namespace . DIRECTORY_SEPARATOR . $class_name . '.php';
	}
}

spl_autoload_register( 'matomo_plugin_autoloader' );
