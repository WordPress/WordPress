<?php
/**
 * The Redux Framework Plugin
 *
 * A simple, truly extensible and fully responsive options framework
 * for WordPress themes and plugins. Developed with WordPress coding
 * standards and PHP best practices in mind.
 *
 * Plugin Name:     Redux Framework
 * Plugin URI:      http://wordpress.org/plugins/redux-framework
 * Github URI:      https://github.com/ReduxFramework/redux-framework
 * Description:     Redux is a simple, truly extensible options framework for WordPress themes and plugins.
 * Author:          Team Redux
 * Author URI:      http://reduxframework.com
 * Version:         3.6.2
 * Text Domain:     redux-framework
 * License:         GPL3+
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path:     ReduxCore/languages
 * Provides:        ReduxFramework
 *
 * @package         ReduxFramework
 * @author          Dovy Paukstys <dovy@reduxframework.com>
 * @author          Kevin Provance <kevin@reduxframework.com>
 * @license         GNU General Public License, version 3
 * @copyright       2012-2016 Redux.io
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) {
    die;
}

// Require the main plugin class
require_once plugin_dir_path( __FILE__ ) . 'class.redux-plugin.php';

// Register hooks that are fired when the plugin is activated and deactivated, respectively.
register_activation_hook( __FILE__, array( 'ReduxFrameworkPlugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'ReduxFrameworkPlugin', 'deactivate' ) );

// Get plugin instance
//add_action( 'plugins_loaded', array( 'ReduxFrameworkPlugin', 'instance' ) );

// The above line prevents ReduxFramework from instancing until all plugins have loaded.
// While this does not matter for themes, any plugin using Redux will not load properly.
// Waiting until all plugins have been loaded prevents the ReduxFramework class from
// being created, and fails the !class_exists('ReduxFramework') check in the sample_config.php,
// and thus prevents any plugin using Redux from loading their config file.
ReduxFrameworkPlugin::instance();
