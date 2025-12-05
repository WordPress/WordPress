<?php
namespace Elementor\Modules\System_Info\Reporters;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor must-use plugins report.
 *
 * Elementor system report handler class responsible for generating a report for
 * must-use plugins.
 *
 * @since 1.0.0
 */
class MU_Plugins extends Base_Plugin {

	/**
	 * Must-Use plugins.
	 *
	 * Holds the sites must-use plugins list.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var array
	 */
	private $plugins;

	/**
	 * Get must-use plugins.
	 *
	 * Retrieve the must-use plugins.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return array Must-Use plugins.
	 */
	private function get_mu_plugins() {
		if ( ! $this->plugins ) {
			$this->plugins = get_mu_plugins();
		}

		return $this->plugins;
	}

	/**
	 * Is enabled.
	 *
	 * Whether there are must-use plugins or not.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return bool True if the site has must-use plugins, False otherwise.
	 */
	public function is_enabled() {
		return (bool) $this->get_mu_plugins();
	}

	/**
	 * Get must-use plugins reporter title.
	 *
	 * Retrieve must-use plugins reporter title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Reporter title.
	 */
	public function get_title() {
		return 'Must-Use Plugins';
	}

	/**
	 * Get must-use plugins report fields.
	 *
	 * Retrieve the required fields for the must-use plugins report.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Required report fields with field ID and field label.
	 */
	public function get_fields() {
		return [
			'must_use_plugins' => 'Must-Use Plugins',
		];
	}

	/**
	 * Get must-use plugins.
	 *
	 * Retrieve the sites must-use plugins.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value The must-use plugins list.
	 * }
	 */
	public function get_must_use_plugins() {
		return [
			'value' => $this->get_mu_plugins(),
		];
	}
}
