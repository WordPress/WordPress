<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Given it's a very specific case.
namespace Yoast\WP\SEO\Services\Importing\Aioseo;

/**
 * Transforms AISOEO search appearance robot settings.
 */
class Aioseo_Robots_Transformer_Service {

	/**
	 * The robots transfomer service.
	 *
	 * @var Aioseo_Robots_Provider_Service
	 */
	protected $robots_provider;

	/**
	 * Class constructor.
	 *
	 * @param Aioseo_Robots_Provider_Service $robots_provider The robots provider service.
	 */
	public function __construct( Aioseo_Robots_Provider_Service $robots_provider ) {
		$this->robots_provider = $robots_provider;
	}

	/**
	 * Transforms the robot setting, taking into consideration whether they defer to global defaults.
	 *
	 * @param string $setting_name  The name of the robot setting, eg. noindex.
	 * @param bool   $setting_value The value of the robot setting.
	 * @param array  $mapping       The mapping of the setting we're working with.
	 *
	 * @return bool The transformed robot setting.
	 */
	public function transform_robot_setting( $setting_name, $setting_value, $mapping ) {
		$aioseo_settings = \json_decode( \get_option( $mapping['option_name'], '' ), true );

		// Let's check first if it defers to global robot settings.
		if ( empty( $aioseo_settings ) || ! isset( $aioseo_settings['searchAppearance'][ $mapping['type'] ][ $mapping['subtype'] ]['advanced']['robotsMeta']['default'] ) ) {
			return $setting_value;
		}

		$defers_to_defaults = $aioseo_settings['searchAppearance'][ $mapping['type'] ][ $mapping['subtype'] ]['advanced']['robotsMeta']['default'];

		if ( $defers_to_defaults ) {
			return $this->robots_provider->get_global_robot_settings( $setting_name );
		}

		return $setting_value;
	}
}
