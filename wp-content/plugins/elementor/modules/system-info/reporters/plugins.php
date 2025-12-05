<?php
namespace Elementor\Modules\System_Info\Reporters;

use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor active plugins report.
 *
 * Elementor system report handler class responsible for generating a report for
 * active plugins.
 *
 * @since 1.0.0
 */
class Plugins extends Base_Plugin {

	/**
	 * Active plugins.
	 *
	 * Holds the sites active plugins list.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var array
	 */
	private $plugins;

	/**
	 * Get active plugins.
	 *
	 * Retrieve the active plugins from the list of all the installed plugins.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return array Active plugins.
	 */
	private function get_plugins() {
		if ( ! $this->plugins ) {
			$this->plugins = Plugin::$instance->wp->get_active_plugins()->all();
		}

		return $this->plugins;
	}

	/**
	 * Get active plugins reporter title.
	 *
	 * Retrieve active plugins reporter title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Reporter title.
	 */
	public function get_title() {
		return 'Active Plugins';
	}

	/**
	 * Is enabled.
	 *
	 * Whether there are active plugins or not.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return bool True if the site has active plugins, False otherwise.
	 */
	public function is_enabled() {
		return (bool) $this->get_plugins();
	}

	/**
	 * Get active plugins report fields.
	 *
	 * Retrieve the required fields for the active plugins report.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Required report fields with field ID and field label.
	 */
	public function get_fields() {
		return [
			'active_plugins' => 'Active Plugins',
		];
	}

	/**
	 * Get active plugins.
	 *
	 * Retrieve the sites active plugins.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value The active plugins list.
	 * }
	 */
	public function get_active_plugins() {
		return [
			'value' => $this->get_plugins(),
		];
	}
}
