<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wproots');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

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
define('AUTH_KEY',         '0Tj(ST!!Hs5Sf,&JJTtGu_+20mL7))dWs>WQef]3llGb5,J{5NN*e}WxY1~#uQke');
define('SECURE_AUTH_KEY',  'GW_)YuO/%PfYu#vNbPuV,MC]^7O*p!%iES9P5*`Fs`fcT,05)O9}tw=m66#HHXGG');
define('LOGGED_IN_KEY',    '/{;y[-A,]?3[Q`),5RhEU`s,vM/L+KL9x.uxcg[zqMnkXER79F}qwwq=(SN?6k||');
define('NONCE_KEY',        '#~<[+ICx-&x$M=(4TXW)mngP)yc)#YYUfg? Wp[&=He*)?6/ZQkKUDbw)5^]Oy~6');
define('AUTH_SALT',        '$#aX>Qy@tj aqFZ(uXhoHn]zn17j`xd)s]9p|8:a|>U$zz|.Ja[f.WC.bXUr$(Dd');
define('SECURE_AUTH_SALT', 'ML3T6|~CmTXR_i[|F4jjz@@$G+H*O:+6zh$okyh1oYrfU@c[M53_,0oDFb-9ml,H');
define('LOGGED_IN_SALT',   ',vD1YEMtN,Uv|7y)Cp>_%Zu_ub=[mSV)yptap=ZLJ=sl&cZu:rjG)PNN2v`@)k>,');
define('NONCE_SALT',       '7~v<#oY}[:tC[ETKQ+LExMNC~,6[`-mn>#nK0{6s~D5rrM*i(/yb+3%Zig&K,_&x');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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

/** foor roots */
define('WP_ENV', 'development');