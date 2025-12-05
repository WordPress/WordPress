<?php
namespace Elementor\Core\Settings\General;

use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * This file is deprecated, use Plugin::$instance->kits_manager->get_active_kit_for_frontend() instead.
 * it changed to support call like this: Manager::get_settings_managers( 'general' )->get_model()->get_settings( 'elementor_default_generic_fonts' )
 *
 * @deprecated 3.0.0 Use `Plugin::$instance->kits_manager->get_active_kit_for_frontend()` instead.
 */
class Model {

	/**
	 * @deprecated 3.0.0
	 */
	public function get_name() {
		return 'general-deprecated';
	}

	/**
	 * @deprecated 3.0.0
	 */
	public function get_panel_page_settings() {
		return [];
	}

	/**
	 * @deprecated 3.0.0
	 */
	public function get_tabs_controls() {
		return [];
	}

	/**
	 * @deprecated 3.0.0
	 */
	public function get_frontend_settings() {
		return [];
	}

	/**
	 * @deprecated 3.0.0
	 */
	public function get_controls() {
		return [];
	}

	/**
	 * @deprecated 3.0.0
	 */
	public function get_settings( $setting = null ) {

		if ( $setting ) {
			$setting = str_replace( 'elementor_', '', $setting );
		}

		return Plugin::$instance->kits_manager->get_current_settings( $setting );
	}
}
