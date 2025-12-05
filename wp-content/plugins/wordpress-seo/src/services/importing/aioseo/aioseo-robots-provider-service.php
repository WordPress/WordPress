<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Given it's a very specific case.
namespace Yoast\WP\SEO\Services\Importing\Aioseo;

use Yoast\WP\SEO\Helpers\Aioseo_Helper;

/**
 * Provides AISOEO search appearance robot settings.
 */
class Aioseo_Robots_Provider_Service {

	/**
	 * The AIOSEO helper.
	 *
	 * @var Aioseo_Helper
	 */
	protected $aioseo_helper;

	/**
	 * Class constructor.
	 *
	 * @param Aioseo_Helper $aioseo_helper The AIOSEO helper.
	 */
	public function __construct( Aioseo_Helper $aioseo_helper ) {
		$this->aioseo_helper = $aioseo_helper;
	}

	/**
	 * Retrieves the robot setting set globally in AIOSEO.
	 *
	 * @param string $setting_name The name of the robot setting, eg. noindex.
	 *
	 * @return bool Whether global robot settings enable or not the specific setting.
	 */
	public function get_global_robot_settings( $setting_name ) {
		$aioseo_settings = $this->aioseo_helper->get_global_option();
		if ( empty( $aioseo_settings ) ) {
			return false;
		}

		$global_robot_settings = $aioseo_settings['searchAppearance']['advanced']['globalRobotsMeta'];
		if ( $global_robot_settings['default'] === true ) {
			return false;
		}

		return $global_robot_settings[ $setting_name ];
	}

	/**
	 * Gets the subtype's robot setting from the db.
	 *
	 * @param array $mapping The mapping of the setting we're working with.
	 *
	 * @return bool The robot setting.
	 */
	public function get_subtype_robot_setting( $mapping ) {
		$aioseo_settings = \json_decode( \get_option( $mapping['option_name'], '' ), true );

		return $aioseo_settings['searchAppearance'][ $mapping['type'] ][ $mapping['subtype'] ]['advanced']['robotsMeta'][ $mapping['robot_type'] ];
	}
}
