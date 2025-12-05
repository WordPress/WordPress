<?php
namespace Elementor\Core\Settings\EditorPreferences;

use Elementor\Core\Settings\Base\Manager as BaseManager;
use Elementor\Core\Settings\Base\Model as BaseModel;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Manager extends BaseManager {

	const META_KEY = 'elementor_preferences';

	/**
	 * Get model for config.
	 *
	 * Retrieve the model for settings configuration.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @return BaseModel The model object.
	 */
	public function get_model_for_config() {
		return $this->get_model();
	}

	/**
	 * Get manager name.
	 *
	 * Retrieve settings manager name.
	 *
	 * @since 2.8.0
	 * @access public
	 */
	public function get_name() {
		return 'editorPreferences';
	}

	/**
	 * Get saved settings.
	 *
	 * Retrieve the saved settings from the database.
	 *
	 * @since 2.8.0
	 * @access protected
	 *
	 * @param int $id
	 * @return array
	 */
	protected function get_saved_settings( $id ) {
		$settings = get_user_meta( get_current_user_id(), self::META_KEY, true );

		if ( ! $settings ) {
			$settings = [];
		}

		return $settings;
	}

	/**
	 * Save settings to DB.
	 *
	 * Save settings to the database.
	 *
	 * @param array $settings Settings.
	 * @param int   $id Post ID.
	 * @since 2.8.0
	 * @access protected
	 */
	protected function save_settings_to_db( array $settings, $id ) {
		update_user_meta( get_current_user_id(), self::META_KEY, $settings );
	}
}
