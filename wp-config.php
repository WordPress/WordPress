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
define( 'DB_NAME', 'eano5dnapo2w7xi3' );

/** Database username */
define( 'DB_USER', 'nh0e04m55g1d0gxb' );

/** Database password */
define( 'DB_PASSWORD', 'usds0ixk5mpzm4o3' );

/** Database hostname */
define( 'DB_HOST', 'spryrr1myu6oalwl.chr7pe7iynqr.eu-west-1.rds.amazonaws.com' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         '3HR6WxT7!K10AE)wk,{zF}#)FGn$2)(.TRg)YN7M#+_|c2zfRJ rD{N.CD[V!i*z' );
define( 'SECURE_AUTH_KEY',  'uK*fp[3,P!.i/^>&%Pp**C)i9T1sY8= 7-i<pUQf5+.ET)OfwG|xk}*YlIM]%HK5' );
define( 'LOGGED_IN_KEY',    '}cr537%{8mIzP<1E}|ga%*p!1Fz23AsDI`N(&)=poSaqu>YEg:n6q%S[U,cs^2vC' );
define( 'NONCE_KEY',        '(Zr/&Zv3~[C?_qRf22n/DcTcs3h;vIQONt^_dEoXa?mY{K MfS/wi98:]xo1?G X' );
define( 'AUTH_SALT',        'TYL|es-&2uV2:wx_ 4OafN.O}Q~<?OaIgh]#kd{Too~fKyF}SC29%nf|@Z?ugayi' );
define( 'SECURE_AUTH_SALT', 'Q8Bo5<CvzETzAaO5+4+Py?3w_iW3#z7,)#Zx#rwJlfR0?PwK`R/W5r.H:!;E|q{5' );
define( 'LOGGED_IN_SALT',   'Z,`Ba6d2u]V:ophmY[,*fEKwp*o<EhfB a}:xMjXE7=iNJ?UHj#g^m3@!MM^:Y^h' );
define( 'NONCE_SALT',       'I?R8hRvIT&:<[$XV122V!^tImA2g`nLx8a=oFzf-UT>oXJ5;9W r5uHX$E+pq^6P' );

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
