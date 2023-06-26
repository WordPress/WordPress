<?php

	/** The name of the database for WordPress */
	define( 'DB_NAME', 'wpnomod' );
	/** MySQL database username */
	define( 'DB_USER', 'admin_newwp2' );
	/** MySQL database password */
	define( 'DB_PASSWORD', '7Gh!5l88c' );
	/** MySQL hostname */
	define( 'DB_HOST', 'localhost' );
	/** Database charset to use in creating database tables. */
	define( 'DB_CHARSET', 'utf8' );
	/** The database collate type. Don't change this if in doubt. */
	define( 'DB_COLLATE', '' );
	define( 'WP_HOME', 'https://' . $_SERVER['HTTP_HOST'] );
	define( 'WP_SITEURL', WP_HOME );
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
	define( 'AUTH_KEY', 'put your unique phrase here' );
	define( 'SECURE_AUTH_KEY', 'put your unique phrase here' );
	define( 'LOGGED_IN_KEY', 'put your unique phrase here' );
	define( 'NONCE_KEY', 'put your unique phrase here' );
	define( 'AUTH_SALT', 'put your unique phrase here' );
	define( 'SECURE_AUTH_SALT', 'put your unique phrase here' );
	define( 'LOGGED_IN_SALT', 'put your unique phrase here' );
	define( 'NONCE_SALT', 'put your unique phrase here' );
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
	define( 'WP_POST_REVISIONS', 3 );
	
	define( 'WP_MEMORY_LIMIT', '512M' );
	
	define( 'WP_DEBUG', false );
	define( 'WP_DEBUG_DISPLAY', false );
	define( 'WP_DEBUG_LOG', false );
	define( 'DISALLOW_FILE_EDIT', true );
	/* Add any custom values between this line and the "stop editing" line. */
	/* That's all, stop editing! Happy publishing. */
	/** Absolute path to the WordPress directory. */
	if ( ! defined( 'ABSPATH' ) ) {
		define( 'ABSPATH', __DIR__ . '/' );
	}
	/** Sets up WordPress vars and included files. */
	require_once ABSPATH . 'wp-settings.php';
