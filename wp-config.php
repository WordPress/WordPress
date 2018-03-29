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
define('DB_NAME', 'kostentr_nsvo');

/** MySQL database username */
define('DB_USER', 'kostentr_nsvo');

/** MySQL database password */
define('DB_PASSWORD', 'Merhaba123');

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
define('AUTH_KEY',         'X2,F8gFqTT63=T,VP=Nq`Ri1pH[)(@Pew&&q+:Y~3iCvE9y=)Se]=F}6[iq3BpgN');
define('SECURE_AUTH_KEY',  'ah!@,}SS|:}LR@*`|}ccDl!uJ|E6K~l^I2W=(Yyk)jFI)xAyqkU>FN=J=zc?%$rj');
define('LOGGED_IN_KEY',    'R~#Vlk|^Yqt2|y2+RnDk@#)<sn?u]2s1:sVO Kk%&|Gsy1a]|vnzD~AL;iPwl1yy');
define('NONCE_KEY',        '$-<IcRYgfP0ptF;<?tkXQ^mW[ga|u;hYboV]9pC|H+{EW8fWd#$<8~ i,N9y3cP6');
define('AUTH_SALT',        'L&@!(:1{?[W`E%*lZInp~x?[oM-g89mQRMp.!i}I8QK)tiViZWe7OHo|;kN?ZkVr');
define('SECURE_AUTH_SALT', 'U-@0t0^,GcfO{c{nLe;m)al4Xx6_QG5S&:qoeCCwDOVksJkP&|VW89vhd1cfbD{D');
define('LOGGED_IN_SALT',   'XbI=qVF%(w]f =QR##I.F-4H=kr^,?IOJ<|cg$uC^%;T}rt=%%f0#2 R&GgF}ExB');
define('NONCE_SALT',       '@HS4Fs|;<QDr#jMc>3q9$)T,+Z`Fi)!vlNC4=fU>;gx%/d1c9kZ=9.3wzL9fY$k<');

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
