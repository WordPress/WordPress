<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Changes the asset paths to dev server paths.
 */
final class WPSEO_Admin_Asset_Dev_Server_Location implements WPSEO_Admin_Asset_Location {

	/**
	 * Holds the dev server's default URL.
	 *
	 * @var string
	 */
	public const DEFAULT_URL = 'http://localhost:8080';

	/**
	 * Holds the url where the server is located.
	 *
	 * @var string
	 */
	private $url;

	/**
	 * Class constructor.
	 *
	 * @param string|null $url Where the dev server is located.
	 */
	public function __construct( $url = null ) {
		if ( $url === null ) {
			$url = self::DEFAULT_URL;
		}

		$this->url = $url;
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
		if ( $type === WPSEO_Admin_Asset::TYPE_CSS ) {
			return $this->get_default_url( $asset, $type );
		}

		$path = sprintf( 'js/dist/%s%s.js', $asset->get_src(), $asset->get_suffix() );

		return trailingslashit( $this->url ) . $path;
	}

	/**
	 * Determines the URL of the asset not using the dev server.
	 *
	 * @param WPSEO_Admin_Asset $asset The asset to determine the URL for.
	 * @param string            $type  The type of asset.
	 *
	 * @return string The URL of the asset file.
	 */
	public function get_default_url( WPSEO_Admin_Asset $asset, $type ) {
		$default_location = new WPSEO_Admin_Asset_SEO_Location( WPSEO_FILE );

		return $default_location->get_url( $asset, $type );
	}
}
