<?php
namespace Elementor\Modules\CompatibilityTag;

use Elementor\Plugin;
use Elementor\Core\Utils\Version;
use Elementor\Core\Base\Base_Object;
use Elementor\Core\Utils\Collection;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Compatibility_Tag extends Base_Object {
	const PLUGIN_NOT_EXISTS = 'plugin_not_exists';
	const HEADER_NOT_EXISTS = 'header_not_exists';
	const INVALID_VERSION = 'invalid_version';
	const INCOMPATIBLE = 'incompatible';
	const COMPATIBLE = 'compatible';

	/**
	 * @var string Holds the header that should be checked.
	 */
	private $header;

	/**
	 * Compatibility_Tag constructor.
	 *
	 * @param string $header
	 */
	public function __construct( $header ) {
		$this->header = $header;
	}

	/**
	 * Return if plugins is compatible or not.
	 *
	 * @param Version $version
	 * @param array   $plugins_names
	 *
	 * @return array
	 * @throws \Exception If an error occurs during compatibility check.
	 */
	public function check( Version $version, array $plugins_names ) {
		return ( new Collection( $plugins_names ) )
			->map_with_keys( function ( $plugin_name ) use ( $version ) {
				return [ $plugin_name => $this->is_compatible( $version, $plugin_name ) ];
			} )
			->all();
	}

	/**
	 * Check single plugin if is compatible or not.
	 *
	 * @param Version $version
	 * @param         $plugin_name
	 *
	 * @return string
	 * @throws \Exception If an error occurs during the compatibility check.
	 */
	private function is_compatible( Version $version, $plugin_name ) {
		$plugins = Plugin::$instance->wp->get_plugins();

		if ( ! isset( $plugins[ $plugin_name ] ) ) {
			return self::PLUGIN_NOT_EXISTS;
		}

		$requested_plugin = $plugins[ $plugin_name ];

		if ( empty( $requested_plugin[ $this->header ] ) ) {
			return self::HEADER_NOT_EXISTS;
		}

		if ( ! Version::is_valid_version( $requested_plugin[ $this->header ] ) ) {
			return self::INVALID_VERSION;
		}

		if ( $version->compare( '>', $requested_plugin[ $this->header ], Version::PART_MAJOR_2 ) ) {
			return self::INCOMPATIBLE;
		}

		return self::COMPATIBLE;
	}
}
