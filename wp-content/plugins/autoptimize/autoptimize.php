<?php
/**
 * Plugin Name: Autoptimize
 * Plugin URI: https://autoptimize.com/pro/
 * Description: Makes your site faster by optimizing CSS, JS, Images, Google fonts and more.
 * Version: 3.1.8.1
 * Author: Frank Goossens (futtta)
 * Author URI: https://autoptimize.com/pro/
 * Text Domain: autoptimize
 * License: GPLv2
 * Released under the GNU General Public License (GPL)
 * https://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 */

/**
 * Autoptimize main plugin file.
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'AUTOPTIMIZE_PLUGIN_VERSION', '3.1.8.1' );

// plugin_dir_path() returns the trailing slash!
define( 'AUTOPTIMIZE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'AUTOPTIMIZE_PLUGIN_FILE', __FILE__ );

// Bail early if attempting to run on non-supported php versions.
if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
    function autoptimize_incompatible_admin_notice() {
        echo '<div class="error"><p>' . __( 'Autoptimize requires PHP 5.6 (or higher) to function properly. Please upgrade PHP. The Plugin has been auto-deactivated.', 'autoptimize' ) . '</p></div>';
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }
    function autoptimize_deactivate_self() {
        deactivate_plugins( plugin_basename( AUTOPTIMIZE_PLUGIN_FILE ) );
    }
    add_action( 'admin_notices', 'autoptimize_incompatible_admin_notice' );
    add_action( 'admin_init', 'autoptimize_deactivate_self' );
    return;
}

function autoptimize_autoload( $class_name ) {
    if ( in_array( $class_name, array( 'AO_Minify_HTML', 'JSMin' ) ) ) {
        $file     = strtolower( $class_name );
        $file     = str_replace( '_', '-', $file );
        $path     = dirname( __FILE__ ) . '/classes/external/php/';
        $filepath = $path . $file . '.php';
    } elseif ( false !== strpos( $class_name, 'Autoptimize\\tubalmartin\\CssMin' ) ) {
        $file     = str_replace( 'Autoptimize\\tubalmartin\\CssMin\\', '', $class_name );
        $path     = dirname( __FILE__ ) . '/classes/external/php/yui-php-cssmin-bundled/';
        $filepath = $path . $file . '.php';
    } elseif ( 'autoptimize' === substr( $class_name, 0, 11 ) ) {
        // One of our "old" classes.
        $file     = $class_name;
        $path     = dirname( __FILE__ ) . '/classes/';
        $filepath = $path . $file . '.php';
    } elseif ( 'PAnD' === $class_name ) {
        $file     = 'persist-admin-notices-dismissal';
        $path     = dirname( __FILE__ ) . '/classes/external/php/persist-admin-notices-dismissal/';
        $filepath = $path . $file . '.php';
    }

    // If we didn't match one of our rules, bail!
    if ( ! isset( $filepath ) || ! is_readable( $filepath ) ) {
        return;
    }

    require $filepath;
}

spl_autoload_register( 'autoptimize_autoload' );

// Load WP CLI command(s) on demand.
if ( defined( 'WP_CLI' ) && WP_CLI ) {
    require AUTOPTIMIZE_PLUGIN_DIR . 'classes/autoptimizeCLI.php';
}

// filter to disable AO both on front- and backend.
if ( apply_filters( 'autoptimize_filter_disable_plugin', false ) ) {
    return;
}

/**
 * Retrieve the instance of the main plugin class.
 *
 * @return autoptimizeMain
 */
function autoptimize() {
    static $plugin = null;

    if ( null === $plugin ) {
        $plugin = new autoptimizeMain( AUTOPTIMIZE_PLUGIN_VERSION, AUTOPTIMIZE_PLUGIN_FILE );
    }

    return $plugin;
}

autoptimize()->run();
