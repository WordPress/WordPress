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
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'jenkins-local');

/** MySQL database password */
define('DB_PASSWORD', '1');

/** MySQL hostname */
define('DB_HOST', 'localhost');

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
define('AUTH_KEY',         'M+|(s^FG=/}8*dZ<G=`U/tLXBm=B5%=>}}`q*SmtU.ehN?H$ITH0k,dfG+^O2^f_');
define('SECURE_AUTH_KEY',  '`.CCy{(*62_^gQS|pmU/:k`V71>t;n:!v;G1T})Q`n6QK@,Bfx3o$8!Nsr;:XM5?');
define('LOGGED_IN_KEY',    'bR?Rut0mv/,kusQLmm^4l1zuF:u*$~LIh.&^K950^]UZU@E{w[bn*&=tvz|}:qVK');
define('NONCE_KEY',        '1od:FoK|1fc2~H&o,C2Xzsmn5{{TVb%Ci9e9XgZa2{]H8+fJ6n+y~QoN:mTQLqQ^');
define('AUTH_SALT',        'mh+>mg(}GmV{a#8plSRf z#gPR[@`|&=~X+cY)3To*$uEKb(r+x5gi&MWC/ah!Kc');
define('SECURE_AUTH_SALT', 'YYQZ-;O!$:bnr8C_FwQ!cego|4X+Y9)Kx.XJL(cHz$n>iJEeJ!,544ybE6Rf{fHw');
define('LOGGED_IN_SALT',   '5M[P0[Aqkd@o<0YsQHQ%:pWVck5)JD5gI%pFeDVH/Na;1p@]?09`$KWG+5W[uZnH');
define('NONCE_SALT',       '[`_8YJ(3p27OckC-.:=Vw,EQ0}C1^WRps/_KnLx7DtA4I7cS#N2Lmb5>;gn#[tZ;');

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
