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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'sena_b' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',         '^1P(r~$g??NDWzYDv?y[SG]qt7KOo&176gB~kl3-V.%zI{Y srjMt/KOb!Cq)?U ' );
define( 'SECURE_AUTH_KEY',  'L~n^?Qc/^5(x`y|6|B&-$K`:lL@lb^40O]yRS-p,nrDxaiAecOKRY&K6kN+dY0p}' );
define( 'LOGGED_IN_KEY',    'j4$~p]O(2lL`-;BdkZ4GQ(J1$h[J~WgKm_Khs;Q5rI$-jQrI3|vZ{s]*Y[Au}V5D' );
define( 'NONCE_KEY',        'E;O6LBpbTu8(i[UALc{hX]aU8G[*ah6*:ek%sxmM?m$_Ap^sFA2y#lya;!bQjJR-' );
define( 'AUTH_SALT',        'Ni=0}-*F]&<Sc,t@hu=L$7^^@[83<>5=<oK((i?T^8W^$(hxu:I~22syT8 gYgi=' );
define( 'SECURE_AUTH_SALT', '%nk ;i^JH]rlsxZVj&3^A>%Ug#kZ<elA`S~^zZl<ML{%~2] @(]YtdY )961+Kw}' );
define( 'LOGGED_IN_SALT',   'vD]m$,N3L+#7%mC)r()TrY2t$Tx65M.XE;vLraH>9wK!>;URL@7V_yA^tZ]tJ:!~' );
define( 'NONCE_SALT',       'W-S}KBK>9J477Se+fnq#(w(ijE>Yj~p*K_DG/m#VvK dK^upYvZInBV,#nT.M%!#' );

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
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
