<?php

// Don't show deprecations
error_reporting(E_ALL ^ E_DEPRECATED);

if (isset($_SERVER['PANTHEON_ENVIRONMENT'])) {
  // Tweak #1
  // Load database settings from PRESSFLOW_SETTINGS environment variable...
  $pressflow_settings = json_decode($_SERVER['PRESSFLOW_SETTINGS'], TRUE);
  $database_settings = $pressflow_settings['databases']['default']['default'];
  $wp_upload_url = "sites/default/files";

  /** MySQL configs */
  define('DB_NAME', $database_settings['database']);
  define('DB_USER', $database_settings['username']);
  define('DB_PASSWORD', $database_settings['password']);
  define('DB_HOST', $database_settings['host'] . ":" . $database_settings['port']);
  define('DB_CHARSET', 'utf8');
  define('DB_COLLATE', '');

  // Tweak #2
  // Sneak in wordpress cookies as Drupal cookies (change prefix from wordpress_ to SESS)
  // But continue to use WP's siteurl-based cookie hash, unless it is for some reason not defined.

  if ( !defined( 'COOKIEHASH' ) ) {
    define( 'COOKIEHASH', '1f7246e65e25c67c745b1eed4b6d7d7b' );
  }
  define('USER_COOKIE', 'SESSuser' . COOKIEHASH);
  define('PASS_COOKIE', 'SESSpass' . COOKIEHASH);
  define('AUTH_COOKIE', 'SESSauth' . COOKIEHASH);
  define('SECURE_AUTH_COOKIE', 'SESSsecure' . COOKIEHASH);
  define('LOGGED_IN_COOKIE', 'SESSloggedin' . COOKIEHASH);
  define('TEST_COOKIE', 'SESStest' . COOKIEHASH);
  
  // Tweak #3
  // Other constants that need to be dynamic for pantheon */
  define('WP_HOME', 'http://' . $_SERVER['HTTP_HOST']);
  define('WP_SITEURL', 'http://' . $_SERVER['HTTP_HOST']);
  define('UPLOADS', $wp_upload_url);  // @TODO Set this to be the binding path
} else {
  //These setting will be used on environments other than Pantheon (ie, your localhost)
  define('DB_NAME', 'local_db_database');
  /** MySQL database username */
  define('DB_USER', 'local_db_username');
  /** MySQL database password */
  define('DB_PASSWORD', 'local_db_password');
  /** MySQL hostname */
  define('DB_HOST', 'localhost');
  /** Database Charset to use in creating database tables. */
  define('DB_CHARSET', 'utf8');
  /** The Database Collate type. Don't change this if in doubt. */
  define('DB_COLLATE', '');
}

// Standard wp-config.php from here on down.
define('AUTH_KEY', 'put your unique phrase here');
define('SECURE_AUTH_KEY', 'put your unique phrase here');
define('LOGGED_IN_KEY', 'put your unique phrase here');
define('NONCE_KEY', 'put your unique phrase here');
define('AUTH_SALT', 'put your unique phrase here');
define('SECURE_AUTH_SALT', 'put your unique phrase here');
define('LOGGED_IN_SALT', 'put your unique phrase here');
define('NONCE_SALT', 'put your unique phrase here');
$table_prefix = 'wp_';
define('WPLANG', '');
define('WP_DEBUG', false);
if (!defined('ABSPATH'))
	define('ABSPATH', dirname(__FILE__) . '/');
require_once (ABSPATH . 'wp-settings.php');
