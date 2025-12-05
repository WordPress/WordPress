<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Determines the location of an asset within the SEO plugin.
 */
final class WPSEO_Admin_Asset_SEO_Location implements WPSEO_Admin_Asset_Location {

	/**
	 * Path to the plugin file.
	 *
	 * @var string
	 */
	protected $plugin_file;

	/**
	 * Whether or not to add the file suffix to the asset.
	 *
	 * @var bool
	 */
	protected $add_suffix = true;

	/**
	 * The plugin file to base the asset location upon.
	 *
	 * @param string $plugin_file The plugin file string.
	 * @param bool   $add_suffix  Optional. Whether or not a file suffix should be added.
	 */
	public function __construct( $plugin_file, $add_suffix = true ) {
		$this->plugin_file = $plugin_file;
		$this->add_suffix  = $add_suffix;
	}

	/**
	 * Determines the URL of the asset on the dev server.
	 *
	 * @param WPSEO_Admin_Asset $asset The asset to determine the URL for.
	 * @param string            $type  The type of asset. Usually JS or CSS.
	 *
	 * @return string The URL of the asset.
	 */
	public function get_url( WPSEO_Admin_Asset $asset, $type ) {
		$path = $this->get_path( $asset, $type );
		if ( empty( $path ) ) {
			return '';
		}

		return plugins_url( $path, $this->plugin_file );
	}

	/**
	 * Determines the path relative to the plugin folder of an asset.
	 *
	 * @param WPSEO_Admin_Asset $asset The asset to determine the path for.
	 * @param string            $type  The type of asset.
	 *
	 * @return string The path to the asset file.
	 */
	protected function get_path( WPSEO_Admin_Asset $asset, $type ) {
		$relative_path = '';
		$rtl_suffix    = '';

		switch ( $type ) {
			case WPSEO_Admin_Asset::TYPE_JS:
				$relative_path = 'js/dist/' . $asset->get_src();
				if ( $this->add_suffix ) {
					$relative_path .= $asset->get_suffix() . '.js';
				}
				break;

			case WPSEO_Admin_Asset::TYPE_CSS:
				// Path and suffix for RTL stylesheets.
				if ( is_rtl() && $asset->has_rtl() ) {
					$rtl_suffix = '-rtl';
				}
				$relative_path = 'css/dist/' . $asset->get_src() . $rtl_suffix . $asset->get_suffix() . '.css';
				break;
		}

		return $relative_path;
	}
}
