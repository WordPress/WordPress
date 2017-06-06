<?php
/**
 * Core Assets.
 *
 * @package Core\Assets
 */

if ( ! function_exists( 'et_core_register_admin_assets' ) ) :
/**
 * Register Core admin assets.
 *
 * @since 1.0.0
 *
 * @private
 */
function et_core_register_admin_assets() {
	wp_register_style( 'et-core-admin', ET_CORE_URL . 'admin/css/core.css', array(), ET_CORE_VERSION );
	wp_register_script( 'et-core-admin', ET_CORE_URL . 'admin/js/core.js', array(), ET_CORE_VERSION );
	wp_localize_script( 'et-core-admin', 'etCore', array(
		'ajaxurl'  => admin_url( 'admin-ajax.php' ),
		'text'     => array(
			'modalTempContentCheck' => esc_html__( 'Got it, thanks!', ET_CORE_TEXTDOMAIN ),
		),
	) );
}
endif;
add_action( 'admin_enqueue_scripts', 'et_core_register_admin_assets' );