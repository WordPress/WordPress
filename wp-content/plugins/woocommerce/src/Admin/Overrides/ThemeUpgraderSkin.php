<?php
/**
 * Theme upgrader skin used in REST API response.
 */

namespace Automattic\WooCommerce\Admin\Overrides;

defined( 'ABSPATH' ) || exit;

/**
 * Admin\Overrides\ThemeUpgraderSkin Class.
 */
class ThemeUpgraderSkin extends \Theme_Upgrader_Skin {
	/**
	 * Avoid undefined property error from \Theme_Upgrader::check_parent_theme_filter().
	 *
	 * @var array
	 */
	public $api;

	/**
	 * Hide the skin header display.
	 */
	public function header() {}

	/**
	 * Hide the skin footer display.
	 */
	public function footer() {}

	/**
	 * Hide the skin feedback display.
	 *
	 * @param string $string String to display.
	 * @param mixed  ...$args Optional text replacements.
	 */
	public function feedback( $string, ...$args ) {}

	/**
	 * Hide the skin after display.
	 */
	public function after() {}
}
