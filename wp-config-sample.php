<?php
/** 
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Key, WordPress Language, and ABSPATH. You can find more information by
 * visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

/**
 * The name of the database.
 *
 * @var string
 * @since unknown
 */
define('DB_NAME', 'putyourdbnamehere');

/**
 * Your MySQL username.
 *
 * @var string
 * @since unknown
 */
define('DB_USER', 'usernamehere');

/**
 * The MySQL user's password
 *
 * @var string
 * @since unknown
 */
define('DB_PASSWORD', 'yourpasswordhere');

/**
 * The host location to the database server.
 *
 * You can get the hostname of the database server from your Web host.
 * (99% chance you won't need to change this value) 
 *
 * @var string
 * @since unknown
 */
define('DB_HOST', 'localhost');

/**
 * DB Charset to use in creating database tables.
 *
 * (Don't change if in doubt)
 *
 * @var string
 * @since unknown
 */
define('DB_CHARSET', 'utf8');

/**
 * The DB Collate type.
 *
 * (Don't change if in doubt)  
 *
 * @var string
 * @since unknown
 */
define('DB_COLLATE', '');

/**
 * Non-SSL login cookie Key
 *
 * Change each KEY to a different unique phrase.  You won't have to remember the
 * phrases later, so make them long and complicated. You can visit
 * {@link http://api.wordpress.org/secret-key/1.1/} to get keys generated for
 * you, or just make something up. Each key should have a different phrase.
 *
 * @link http://api.wordpress.org/secret-key/1.1/ Visit for unique phrase.
 * @link http://boren.nu/archives/2008/07/14/ssl-and-cookies-in-wordpress-26/ More information on secured cookies.
 * @since 2.6
 * @var string
 * @package WordPress
 * @subpackage Security
 */
define('AUTH_KEY', 'put your unique phrase here');

/**
 * SSL login cookie key
 *
 * Change each KEY to a different unique phrase.  You won't have to remember the
 * phrases later, so make them long and complicated. You can visit
 * {@link http://api.wordpress.org/secret-key/1.1/} to get keys generated for
 * you, or just make something up. Each key should have a different phrase.
 *
 * @link http://api.wordpress.org/secret-key/1.1/ Visit for unique phrase.
 * @link http://boren.nu/archives/2008/07/14/ssl-and-cookies-in-wordpress-26/ More information on secured cookies.
 * @since 2.6
 * @var string
 * @package WordPress
 * @subpackage Security
 */
define('SECURE_AUTH_KEY', 'put your unique phrase here');

/**
 * Both SSL and non-SSL cookie
 *
 * Change each KEY to a different unique phrase.  You won't have to remember the
 * phrases later, so make them long and complicated. You can visit
 * {@link http://api.wordpress.org/secret-key/1.1/} to get keys generated for
 * you, or just make something up. Each key should have a different phrase.
 *
 * @link http://api.wordpress.org/secret-key/1.1/ Visit for unique phrase.
 * @link http://boren.nu/archives/2008/07/14/ssl-and-cookies-in-wordpress-26/ More information on secured cookies.
 * @since 2.6
 * @var string
 * @package WordPress
 * @subpackage Security
 */
define('LOGGED_IN_KEY', 'put your unique phrase here');

/**
 * Table prefix for WordPress
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores required!
 *
 * @global string $table_prefix
 * @var string
 * @name $table_prefix
 * @since unknown
 */
$table_prefix  = 'wp_';

/**
 * Change this to localize WordPress.
 *
 * A corresponding MO file for the chosen language must be installed to
 * wp-content/languages. For example, install de.mo to wp-content/languages and
 * set WPLANG to 'de' to enable German language support.
 *
 * @var string
 * @since unknown
 */
define ('WPLANG', '');

/* That's all, stop editing! Happy blogging. */

/**
 * Defines the base WordPress location.
 *
 * Has the forward slash at the end.
 *
 * @var string
 * @since unknown
 */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
?>