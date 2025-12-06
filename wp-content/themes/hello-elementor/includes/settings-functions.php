<?php

use HelloTheme\Theme;
use HelloTheme\Modules\AdminHome\Components\Settings_Controller;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action( 'init', 'hello_elementor_tweak_settings', 0 );

function hello_elementor_tweak_settings() {
	/**
	 * @var Settings_Controller $settings_controller
	 */
	$settings_controller = Theme::instance()
								->get_module( 'AdminHome' )
								->get_component( 'Settings_Controller' );

	$settings_controller->legacy_register_settings();
}
/**
 * Register a new setting.
 *
 * @deprecated 3.4.0
 */
function hello_elementor_register_settings( $settings_group, $settings ) {
	/**
	 * @var Settings_Controller $settings_controller
	 */
	$settings_controller = Theme::instance()
								->get_module( 'AdminHome' )
								->get_component( 'Settings_Controller' );

	$settings_controller->register_settings( $settings_group, $settings );
}

/**
 * Run a tweek only if the user requested it.
 *
 * @deprecated 3.4.0
 */
function hello_elementor_do_tweak( $setting, $tweak_callback ) {
	/**
	 * @var Settings_Controller $settings_controller
	 */
	$settings_controller = Theme::instance()
								->get_module( 'AdminHome' )
								->get_component( 'Settings_Controller' );

	$settings_controller->apply_setting( $setting, $tweak_callback );
}

/**
 * Render theme tweaks.
 *
 * @deprecated 3.4.0
 */
function hello_elementor_render_tweaks( $settings_group, $settings ) {
	/**
	 * @var Settings_Controller $settings_controller
	 */
	$settings_controller = Theme::instance()
								->get_module( 'AdminHome' )
								->get_component( 'Settings_Controller' );

	$settings_controller->apply_settings( $settings_group, $settings );
}
