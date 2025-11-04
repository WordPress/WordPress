<?php
/**
 * Bootstrap file for setting the ABSPATH constant
 * and loading the wp-config.php file. The wp-config.php
 * file will then load the wp-settings.php file, which
 * sets up the WordPress environment.
 */

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}

/** Load wp-config.php, which then loads wp-settings.php */
if ( file_exists( ABSPATH . 'wp-config.php' ) ) {
    require_once ABSPATH . 'wp-config.php';
} elseif ( file_exists( dirname( ABSPATH ) . '/wp-config.php' ) ) {
    require_once dirname( ABSPATH ) . '/wp-config.php';
} else {
    // If no config found, go to installer
    require_once ABSPATH . 'wp-admin/setup-config.php';
    exit;
}
