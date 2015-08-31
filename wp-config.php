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

// Multisite code below. Remember to add your domain to DOMAIN_CURRENT_SITE.

/* 
define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', false);
define('DOMAIN_CURRENT_SITE', 'www.monaghan-mushrooms.com');
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);
define( 'SUNRISE', 'on' );
*/

/* MySQL settings */
switch($_SERVER['SERVER_NAME']){
	// Local Machine settings
	case '':
		define('DB_NAME', '');
		define('DB_USER', 'root');
		define('DB_PASSWORD', '');
		define('DB_HOST', 'localhost');
	break;
	
	// Staging server settings
	case '':
		define('DB_NAME', '');
		define('DB_USER', '');
		define('DB_PASSWORD', '');
		define('DB_HOST', 'localhost');
	break;
	
	// Live website settings
	default:
		define('DB_NAME', '');
		define('DB_USER', '');
		define('DB_PASSWORD', '');
		define('DB_HOST', 'localhost');
	break;
}

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         'D})=DQUtX= eO*K@`$[[Y/s(&,U++t59X&Hx(H#6wSC y{+~Kb ,Y:/]L&UdbcE=');
define('SECURE_AUTH_KEY',  ')Eqo-DQ-:-[x~lXDtaGf)0Dm}+$vpf-3!5x{arhyEg(.NYZ6)]][XO3yn#z6+]{^');
define('LOGGED_IN_KEY',    'zfh+&R8<JYZ<`pZBvJ%_.1:N|5d&^tEHc_CGc<C-U-=:(-5O5h7jW!@TA|]>D-h2');
define('NONCE_KEY',        'g*ND:}<kXK~Fih[1.w3pk+y;f-+VsZ$Pz6J[W_mhz}#I$K?I3%#jMf#RKNIO-}E`');
define('AUTH_SALT',        'J4&(SYxT@]aP2;;!t<6g$<OTNiMW^X/-Ak~@<IPMB=b1!!`.N_8.Ad|8er5w7n5N');
define('SECURE_AUTH_SALT', '9<_|GfdnsDspHWbh6qB({Wm76H++zF)1BsG?&ylWAnk~/-aA:K5|m-g)#G%A`c?0');
define('LOGGED_IN_SALT',   '^v~J&NI%R=Mrm1s72nF6-pm0hiK_~]C/YJ7|>klXpo8KGQzthwJ+Hy#H{;dzaK;_');
define('NONCE_SALT',       'DYPjoCHH7=(%JORbun9Ic!VoUuR+d8?x[tno/gKTdet(`}7{v$!cb#4+2~hhv)Bs');

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
define('WP_AUTO_UPDATE_CORE', false );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Turn off WP auto updates */
define( 'WP_AUTO_UPDATE_CORE', false );

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

/** Turn off WP auto updates */
define( 'AUTOMATIC_UPDATER_DISABLED', true );