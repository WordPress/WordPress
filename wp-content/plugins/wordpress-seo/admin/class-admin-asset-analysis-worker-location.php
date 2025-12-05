<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Represents a way to determine the analysis worker asset location.
 */
final class WPSEO_Admin_Asset_Analysis_Worker_Location implements WPSEO_Admin_Asset_Location {

	/**
	 * Holds the asset's location.
	 *
	 * @var WPSEO_Admin_Asset_Location
	 */
	private $asset_location;

	/**
	 * Holds the asset itself.
	 *
	 * @var WPSEO_Admin_Asset
	 */
	private $asset;

	/**
	 * Constructs the location of the analysis worker asset.
	 *
	 * @param string $flat_version The flat version of the asset.
	 * @param string $name         The name of the analysis worker asset.
	 */
	public function __construct( $flat_version = '', $name = 'analysis-worker' ) {
		if ( $flat_version === '' ) {
			$asset_manager = new WPSEO_Admin_Asset_Manager();
			$flat_version  = $asset_manager->flatten_version( WPSEO_VERSION );
		}

		$analysis_worker = $name . '-' . $flat_version . '.js';

		$this->asset_location = WPSEO_Admin_Asset_Manager::create_default_location();
		$this->asset          = new WPSEO_Admin_Asset(
			[
				'name' => $name,
				'src'  => $analysis_worker,
			]
		);
	}

	/**
	 * Retrieves the analysis worker asset.
	 *
	 * @return WPSEO_Admin_Asset The analysis worker asset.
	 */
	public function get_asset() {
		return $this->asset;
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
		$scheme = wp_parse_url( $asset->get_src(), PHP_URL_SCHEME );
		if ( in_array( $scheme, [ 'http', 'https' ], true ) ) {
			return $asset->get_src();
		}

		return $this->asset_location->get_url( $asset, $type );
	}
}
