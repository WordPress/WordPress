<?php
namespace Automattic\WooCommerce\Blocks\Domain;

use Automattic\WooCommerce\Blocks\Options;
use Automattic\WooCommerce\Blocks\Domain\Services\FeatureGating;


/**
 * Main package class.
 *
 * Returns information about the package and handles init.
 *
 * @since 2.5.0
 */
class Package {

	/**
	 * Holds the current version of the blocks plugin.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Holds the main path to the blocks plugin directory.
	 *
	 * @var string
	 */
	private $path;

	/**
	 * Holds locally the plugin_dir_url to avoid recomputing it.
	 *
	 * @var string
	 */
	private $plugin_dir_url;

	/**
	 * Holds the feature gating class instance.
	 *
	 * @var FeatureGating
	 */
	private $feature_gating;

	/**
	 * Constructor
	 *
	 * @param string        $version        Version of the plugin.
	 * @param string        $plugin_path    Path to the main plugin file.
	 * @param FeatureGating $feature_gating Feature gating class instance.
	 */
	public function __construct( $version, $plugin_path, FeatureGating $feature_gating ) {
		$this->version        = $version;
		$this->path           = $plugin_path;
		$this->feature_gating = $feature_gating;
	}

	/**
	 * Returns the version of the plugin.
	 *
	 * @return string
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Returns the version of the plugin stored in the database.
	 *
	 * @return string
	 */
	public function get_version_stored_on_db() {
		return get_option( Options::WC_BLOCK_VERSION, '' );
	}

	/**
	 * Set the version of the plugin stored in the database.
	 * This is useful during the first installation or after the upgrade process.
	 */
	public function set_version_stored_on_db() {
		update_option( Options::WC_BLOCK_VERSION, $this->get_version() );

	}

	/**
	 * Returns the path to the plugin directory.
	 *
	 * @param string $relative_path  If provided, the relative path will be
	 *                               appended to the plugin path.
	 *
	 * @return string
	 */
	public function get_path( $relative_path = '' ) {
		return trailingslashit( $this->path ) . $relative_path;
	}

	/**
	 * Returns the url to the blocks plugin directory.
	 *
	 * @param string $relative_url If provided, the relative url will be
	 *                             appended to the plugin url.
	 *
	 * @return string
	 */
	public function get_url( $relative_url = '' ) {
		if ( ! $this->plugin_dir_url ) {
			// Append index.php so WP does not return the parent directory.
			$this->plugin_dir_url = plugin_dir_url( $this->path . '/index.php' );
		}

		return $this->plugin_dir_url . $relative_url;
	}

	/**
	 * Returns an instance of the the FeatureGating class.
	 *
	 * @return FeatureGating
	 */
	public function feature() {
		return $this->feature_gating;
	}

	/**
	 * Checks if we're executing the code in an experimental build mode.
	 *
	 * @return boolean
	 */
	public function is_experimental_build() {
		return $this->feature()->is_experimental_build();
	}

	/**
	 * Checks if we're executing the code in an feature plugin or experimental build mode.
	 *
	 * @return boolean
	 */
	public function is_feature_plugin_build() {
		return $this->feature()->is_feature_plugin_build();
	}
}
