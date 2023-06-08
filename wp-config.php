<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpressdb' );

/** Database username */
define( 'DB_USER', 'wordpressdb' );

/** Database password */
define( 'DB_PASSWORD', 'wordpresspass' );

/** Database hostname */
define( 'DB_HOST', 'db:3306' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '_%!H)SrI O4{)??i4QxGK~l_t`NbFj|PSJK389_$|MP+,b<}>(s`B00=;~wYG+RY' );
define( 'SECURE_AUTH_KEY',  'FkvBh2>DBzA`^]%Yj`kxg3(e(&#+a(n@G^0@4l;N Ov3eYf @!U9YyAkK`rf**kK' );
define( 'LOGGED_IN_KEY',    '^]YJt.):|:!dA$6|7-6)j/aq&|sk ic7#BEGLa{+.C^qnCea2_.gC*|FRT_?+M{e' );
define( 'NONCE_KEY',        'eN,i#5qn8u/bP!7BtxF$&E!%owMp6w?%el8|!nO9Cd]I&0#uc#z-6lh8Q7 SAjn!' );
define( 'AUTH_SALT',        '?Yvm.SfrVd<J=&`z|22rm0WpG?Lwt$S_6>/]W4 IP]9vuKg}v,aH~~YN+>QtA2{L' );
define( 'SECURE_AUTH_SALT', 'rF4+{GjGg<v zgh$-sDtd*?%xO&*}71.9~EcLhwi>yq3>FR)bGQ 1[|H6*wTc_z1' );
define( 'LOGGED_IN_SALT',   '!L;F)pDNBm@^`]aoDklTKp.J6E8JVszeG0m(,Bm{qI$QI&Hc/ Eehj|TR*+=+R?H' );
define( 'NONCE_SALT',       '^Ja3C[TgMSF(3?3qy/Lu>ce`~1whW`/ ~Jv vOWHvHn+oP:;Zyyxg;1Mw5|KBax8' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */


/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
