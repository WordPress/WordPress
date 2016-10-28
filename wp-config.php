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
define('DB_NAME', getenv("DATABASE_NAME"));

/** MySQL database username */
define('DB_USER', getenv("DATABASE_USER"));

/** MySQL database password */
define('DB_PASSWORD', getenv("DATABASE_PASSWORD"));

/** MySQL hostname */
define('DB_HOST', getenv(strtoupper(getenv("DATABASE_SERVICE_NAME"))."_SERVICE_HOST"));

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
define('AUTH_KEY',         'JjA6u3V|7^l$4c}XxHTOF[&W:oNN=}zIGSn6vQ`hlgb@2)%:*v=zb{$H;f+J1_Z+');
define('SECURE_AUTH_KEY',  'vEouPtde.[mFR<5$W38cQ/~K48t5HK/GuKsafbB6,D)d{^I}mIG:_EIEOw9?4ky(');
define('LOGGED_IN_KEY',    '|$d9d8ON-xymE-L kG+.WKt/y]>!+i,=w9#9l1-2`|sUB6!fP|YV!s-F)RCi;c(R');
define('NONCE_KEY',        'Mi%(Y.3$N%|af-Rvcy$p[s7^pQK#X9E)%8?]2@FSAS||6#?)u.5o}#Xp~FtVZzM3');
define('AUTH_SALT',        '|+u~ru:P#y).CLQ(23E{O{u4,V RR,#22W?_#]pq]F3}f^4QU[i&)H3IYTayg;zq');
define('SECURE_AUTH_SALT', 'h!M@Va0<<S^WFJ:cyX.8Tlz8a%K[$|P<@g:V<ns)9/r43m)h#7gk7$@zDcIzG,`O');
define('LOGGED_IN_SALT',   'qT{zbK}eUTZOI:l(3{q_RmKg2+D(yZy7E/eiOu$B}(t~qgZ{UF8cT%O|W1KA_}r ');
define('NONCE_SALT',       'TAn/Iq+LCk13T-k{]@DLZXXO38dRLX-1s2MftCF%Fa|+qJH`o.A:g+z:k:V@bw4d');

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
