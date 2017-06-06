<?php
/**
 * Load Elegant Themes Core.
 *
 * @package Core
 */

// Stop here if the Core was already loaded.
if ( defined( 'ET_CORE' ) ) {
	return;
}

define( 'ET_CORE', true );

if ( ! function_exists( 'et_core_setup' ) ) :
/**
 * Setup Core.
 *
 * @since 1.0.0
 *
 * @param string $url Url used to load the Core assets.
 *
 * @return bool Always return true.
 */
function et_core_setup( $url ) {
	if ( ! defined( 'ET_CORE_PATH' ) ) {
		define( 'ET_CORE_PATH', trailingslashit( dirname( __FILE__ ) ) );
	}

	if ( ! defined( 'ET_CORE_URL' ) ) {
		define( 'ET_CORE_URL', trailingslashit( $url ) . 'core/' );
	}

	if ( ! defined( 'ET_CORE_TEXTDOMAIN' ) ) {
		define( 'ET_CORE_TEXTDOMAIN', 'et-core' );
	}

	load_theme_textdomain( 'et-core', ET_CORE_PATH . 'languages/' );

	require_once( ET_CORE_PATH . 'components.php' );
	require_once( ET_CORE_PATH . 'functions.php' );
	require_once( ET_CORE_PATH . 'updates_init.php' );

	if ( is_admin() ) {
		require_once( ET_CORE_PATH . 'admin/includes/assets.php' );

		add_action( 'admin_enqueue_scripts', 'et_core_load_main_styles' );
	}
}
endif;

if ( ! function_exists( 'et_core_load_main_styles' ) ) :
function et_core_load_main_styles( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
		return;
	}

	wp_enqueue_style( 'et-core-admin' );
}
endif;