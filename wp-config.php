G<?php
// Don't show deprecations
error_reporting(E_ALL ^ E_DEPRECATED);

/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// Load database settings from PRESSFLOW_SETTINGS environment variable...
$pressflow_settings = json_decode($_SERVER['PRESSFLOW_SETTINGS'], TRUE);

$pantheon_environment = $pressflow_settings['conf']['pantheon_environment'];

$wp_url = "http://$pantheon_environment.nuclearrooster.gotpantheon.com";
if ($pantheon_environment == 'live') {
  $wp_url = 'http://dev.nuclearrooster.com';
}

define('WP_HOME', $wp_url);
define('WP_SITEURL', $wp_url);


$database_settings = $pressflow_settings['databases']['default']['default'];

/** MySQL configs */
define('DB_NAME', $database_settings['database']);
define('DB_USER', $database_settings['username']);
define('DB_PASSWORD', $database_settings['password']);
define('DB_HOST', $database_settings['host'].":".$database_settings['port']);

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'put your unique phrase here');
define('SECURE_AUTH_KEY',  'put your unique phrase here');
define('LOGGED_IN_KEY',    'put your unique phrase here');
define('NONCE_KEY',        'put your unique phrase here');
define('AUTH_SALT',        'put your unique phrase here');
define('SECURE_AUTH_SALT', 'put your unique phrase here');
define('LOGGED_IN_SALT',   'put your unique phrase here');
define('NONCE_SALT',       'put your unique phrase here');

// Sneak in wordpress cookies as Drupal cookies (change prefix from wordpress_ to SESS)
$salt = md5($_SERVER['PRESSFLOW_SETTINGS']);
define('USER_COOKIE', 'SESSuser' . $salt);
define('PASS_COOKIE', 'SESSpass' . $salt);
define('AUTH_COOKIE', 'SESSauth' . $salt);
define('SECURE_AUTH_COOKIE', 'SESSsecure' . $salt);
define('LOGGED_IN_COOKIE', 'SESSloggedin' . $salt);
define('TEST_COOKIE', 'SESStest' . $salt);

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
    define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
