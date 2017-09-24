<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */

# Get DB_NAME from Openshift's environment variable
$db_name = getenv('MYSQL_DATABASE');
define('DB_NAME', $db_name);

/** MySQL database username */
# Get DB_USER from Openshift's environment variable
$db_user = getenv('MYSQL_USER');
define('DB_USER', $db_user);

/** MySQL database password */
# Get DB_PASSWORD from Openshift's environment variable
$db_password = getenv('MYSQL_PASSWORD');
define('DB_PASSWORD', $db_password);

/** MySQL hostname */
# Get DB_HOST from Openshift's environment variable
$db_host = getenv('MYSQL_SERVICE_HOST');
$db_port = getenv('MYSQL_SERVICE_PORT');
$db_host = $db_host.":".$db_port;
define('DB_HOST', $db_host);

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
# define('AUTH_KEY',         'put your unique phrase here');
# define('SECURE_AUTH_KEY',  'put your unique phrase here');
# define('LOGGED_IN_KEY',    'put your unique phrase here');
# define('NONCE_KEY',        'put your unique phrase here');
# define('AUTH_SALT',        'put your unique phrase here');
# define('SECURE_AUTH_SALT', 'put your unique phrase here');
# define('LOGGED_IN_SALT',   'put your unique phrase here');
# define('NONCE_SALT',       'put your unique phrase here');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
