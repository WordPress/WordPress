<?php
namespace Elementor\Core\Settings\Base;

use Elementor\Core\Files\CSS\Base as CSS_File;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class CSS_Manager extends Manager {

	/**
	 * Get CSS file name.
	 *
	 * Retrieve CSS file name for the settings base css manager.
	 *
	 * @since 2.8.0
	 * @access protected
	 * @abstract
	 *
	 * @return string CSS file name
	 */
	abstract protected function get_css_file_name();

	/**
	 * Get model for CSS file.
	 *
	 * Retrieve the model for the CSS file.
	 *
	 * @since 2.8.0
	 * @access protected
	 * @abstract
	 *
	 * @param CSS_File $css_file The requested CSS file.
	 *
	 * @return CSS_Model
	 */
	abstract protected function get_model_for_css_file( CSS_File $css_file );

	/**
	 * Get CSS file for update.
	 *
	 * Retrieve the CSS file before updating it.
	 *
	 * @since 2.8.0
	 * @access protected
	 * @abstract
	 *
	 * @param int $id Post ID.
	 *
	 * @return CSS_File
	 */
	abstract protected function get_css_file_for_update( $id );

	/**
	 * Settings base manager constructor.
	 *
	 * Initializing Elementor settings base css manager.
	 *
	 * @since 2.8.0
	 * @access public
	 */
	public function __construct() {
		parent::__construct();

		$name = $this->get_css_file_name();

		add_action( "elementor/css-file/{$name}/parse", [ $this, 'add_settings_css_rules' ] );
	}

	/**
	 * Save settings.
	 *
	 * Save settings to the database and update the CSS file.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $settings Settings.
	 * @param int   $id       Optional. Post ID. Default is `0`.
	 */
	public function save_settings( array $settings, $id = 0 ) {
		parent::save_settings( $settings, $id );

		$css_file = $this->get_css_file_for_update( $id );

		if ( $css_file ) {
			$css_file->update();
		}
	}

	/**
	 * Add settings CSS rules.
	 *
	 * Add new CSS rules to the settings manager.
	 *
	 * Fired by `elementor/css-file/{$name}/parse` action.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param CSS_File $css_file The requested CSS file.
	 */
	public function add_settings_css_rules( CSS_File $css_file ) {
		$model = $this->get_model_for_css_file( $css_file );

		$css_file->add_controls_stack_style_rules(
			$model,
			$css_file->get_style_controls( $model, null, $model->get_settings() ),
			$model->get_settings(),
			[ '{{WRAPPER}}' ],
			[ $model->get_css_wrapper_selector() ]
		);
	}
}
