<?php
namespace Elementor\Core\Settings\General;

use Elementor\Core\Files\CSS\Base;
use Elementor\Core\Settings\Base\CSS_Manager;
use Elementor\Core\Settings\Base\Model as BaseModel;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * This class is deprecated, use Plugin::$instance->kits_manager->get_active_kit_for_frontend() instead.
 * it changed to support call like this: Manager::get_settings_managers( 'general' )->get_model()->get_settings( 'elementor_default_generic_fonts' )
 *
 * @deprecated 3.0.0 Use `Plugin::$instance->kits_manager->get_active_kit_for_frontend()` instead.
 */
class Manager extends CSS_Manager {

	/**
	 * Meta key for the general settings.
	 *
	 * @deprecated 3.0.0
	 */
	const META_KEY = '_elementor_general_settings';

	/**
	 * General settings manager constructor.
	 *
	 * Initializing Elementor general settings manager.
	 *
	 * @since 1.6.0
	 * @deprecated 3.0.0
	 * @access public
	 */
	public function __construct() {
		parent::__construct();

		_deprecated_file( __FILE__, '3.0.0', 'Plugin::$instance->kits_manager->get_active_kit_for_frontend()' );

		$name = $this->get_css_file_name();

		remove_action( "elementor/css-file/{$name}/parse", [ $this, 'add_settings_css_rules' ] );
	}

	/**
	 * Get manager name.
	 *
	 * Retrieve general settings manager name.
	 *
	 * @since 1.6.0
	 * @deprecated 3.0.0
	 * @access public
	 *
	 * @return string Manager name.
	 */
	public function get_name() {
		return 'general';
	}

	/**
	 * Get model for config.
	 *
	 * Retrieve the model for settings configuration.
	 *
	 * @since 1.6.0
	 * @deprecated 3.0.0
	 * @access public
	 *
	 * @return BaseModel The model object.
	 */
	public function get_model_for_config() {
		return $this->get_model();
	}

	/**
	 * @deprecated 3.0.0
	 */
	protected function get_saved_settings( $id ) {
		return [];
	}

	/**
	 * Get CSS file name.
	 *
	 * Retrieve CSS file name for the general settings manager.
	 *
	 * @since 1.6.0
	 * @deprecated 3.0.0
	 * @access protected
	 *
	 * @return string CSS file name.
	 */
	protected function get_css_file_name() {
		return 'global';
	}

	/**
	 * @deprecated 3.0.0
	 *
	 * @throws \Exception If settings validation fails or update operation encounters errors.
	 */
	protected function save_settings_to_db( array $settings, $id ) {
		throw new \Exception( __CLASS__ . ' is deprecated. Use Plugin::$instance->kits_manager->get_active_kit_for_frontend() instead.' );
	}

	/**
	 * @deprecated 3.0.0
	 */
	protected function get_model_for_css_file( Base $css_file ) {
		return false;
	}

	/**
	 * @deprecated 3.0.0
	 */
	protected function get_css_file_for_update( $id ) {
		return false;
	}
}
