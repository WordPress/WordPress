<?php

namespace Yoast\WP\SEO\Actions\Addon_Installation;

use WPSEO_Addon_Manager;
use Yoast\WP\SEO\Exceptions\Addon_Installation\Addon_Activation_Error_Exception;
use Yoast\WP\SEO\Exceptions\Addon_Installation\User_Cannot_Activate_Plugins_Exception;
use Yoast\WP\SEO\Helpers\Require_File_Helper;

/**
 * Represents the endpoint for activating a specific Yoast Plugin on WordPress.
 */
class Addon_Activate_Action {

	/**
	 * The addon manager.
	 *
	 * @var WPSEO_Addon_Manager
	 */
	protected $addon_manager;

	/**
	 * The require file helper.
	 *
	 * @var Require_File_Helper
	 */
	protected $require_file_helper;

	/**
	 * Addon_Activate_Action constructor.
	 *
	 * @param WPSEO_Addon_Manager $addon_manager       The addon manager.
	 * @param Require_File_Helper $require_file_helper A file helper.
	 */
	public function __construct(
		WPSEO_Addon_Manager $addon_manager,
		Require_File_Helper $require_file_helper
	) {
		$this->addon_manager       = $addon_manager;
		$this->require_file_helper = $require_file_helper;
	}

	/**
	 * Activates the plugin based on the given plugin file.
	 *
	 * @param string $plugin_slug The plugin slug to get download url for.
	 *
	 * @return bool True when activation is successful.
	 *
	 * @throws Addon_Activation_Error_Exception       Exception when the activation encounters an error.
	 * @throws User_Cannot_Activate_Plugins_Exception Exception when the user is not allowed to activate.
	 */
	public function activate_addon( $plugin_slug ) {
		if ( ! \current_user_can( 'activate_plugins' ) ) {
			throw new User_Cannot_Activate_Plugins_Exception();
		}

		if ( $this->addon_manager->is_installed( $plugin_slug ) ) {
			return true;
		}

		$this->load_wordpress_classes();

		$plugin_file       = $this->addon_manager->get_plugin_file( $plugin_slug );
		$activation_result = \activate_plugin( $plugin_file );

		if ( $activation_result !== null && \is_wp_error( $activation_result ) ) {
			throw new Addon_Activation_Error_Exception( $activation_result->get_error_message() );
		}

		return true;
	}

	/**
	 * Requires the files needed from WordPress itself.
	 *
	 * @codeCoverageIgnore Only loads a WordPress file.
	 *
	 * @return void
	 */
	protected function load_wordpress_classes() {
		if ( ! \function_exists( 'get_plugins' ) ) {
			$this->require_file_helper->require_file_once( \ABSPATH . 'wp-admin/includes/plugin.php' );
		}
	}
}
