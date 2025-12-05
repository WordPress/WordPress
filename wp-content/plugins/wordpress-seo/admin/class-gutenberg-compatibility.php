<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Gutenberg_Compatibility
 */

/**
 * Class WPSEO_Gutenberg_Compatibility
 */
class WPSEO_Gutenberg_Compatibility {

	/**
	 * The currently released version of Gutenberg.
	 *
	 * @var string
	 */
	public const CURRENT_RELEASE = '22.1.2';

	/**
	 * The minimally supported version of Gutenberg by the plugin.
	 *
	 * @var string
	 */
	public const MINIMUM_SUPPORTED = '22.1.2';

	/**
	 * Holds the current version.
	 *
	 * @var string
	 */
	protected $current_version = '';

	/**
	 * WPSEO_Gutenberg_Compatibility constructor.
	 */
	public function __construct() {
		$this->current_version = $this->detect_installed_gutenberg_version();
	}

	/**
	 * Determines whether or not Gutenberg is installed.
	 *
	 * @return bool Whether or not Gutenberg is installed.
	 */
	public function is_installed() {
		return $this->current_version !== '';
	}

	/**
	 * Determines whether or not the currently installed version of Gutenberg is below the minimum supported version.
	 *
	 * @return bool True if the currently installed version is below the minimum supported version. False otherwise.
	 */
	public function is_below_minimum() {
		return version_compare( $this->current_version, $this->get_minimum_supported_version(), '<' );
	}

	/**
	 * Gets the currently installed version.
	 *
	 * @return string The currently installed version.
	 */
	public function get_installed_version() {
		return $this->current_version;
	}

	/**
	 * Determines whether or not the currently installed version of Gutenberg is the latest, fully compatible version.
	 *
	 * @return bool Whether or not the currently installed version is fully compatible.
	 */
	public function is_fully_compatible() {
		return version_compare( $this->current_version, $this->get_latest_release(), '>=' );
	}

	/**
	 * Gets the latest released version of Gutenberg.
	 *
	 * @return string The latest release.
	 */
	protected function get_latest_release() {
		return self::CURRENT_RELEASE;
	}

	/**
	 * Gets the minimum supported version of Gutenberg.
	 *
	 * @return string The minumum supported release.
	 */
	protected function get_minimum_supported_version() {
		return self::MINIMUM_SUPPORTED;
	}

	/**
	 * Detects the currently installed Gutenberg version.
	 *
	 * @return string The currently installed Gutenberg version. Empty if the version couldn't be detected.
	 */
	protected function detect_installed_gutenberg_version() {
		if ( defined( 'GUTENBERG_VERSION' ) ) {
			return GUTENBERG_VERSION;
		}

		return '';
	}
}
