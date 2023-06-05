<?php
/**
 * Get product info step.
 *
 * @package WooCommerce\WCCom
 * @since   7.7.0
 */

use WC_REST_WCCOM_Site_Installer_Error_Codes as Installer_Error_Codes;
use WC_REST_WCCOM_Site_Installer_Error as Installer_Error;

defined( 'ABSPATH' ) || exit;

/**
 * WC_WCCOM_Site_Installation_Step_Unpack_Product class
 */
class WC_WCCOM_Site_Installation_Step_Unpack_Product implements WC_WCCOM_Site_Installation_Step {
	/**
	 * The current installation state.
	 *
	 * @var WC_WCCOM_Site_Installation_State
	 */
	protected $state;

	/**
	 * Constructor.
	 *
	 * @param array $state The current installation state.
	 */
	public function __construct( $state ) {
		$this->state = $state;
	}

	/**
	 * Run the step installation process.
	 */
	public function run() {
		$upgrader      = WC_WCCOM_Site_Installer::get_wp_upgrader();
		$unpacked_path = $upgrader->unpack_package( $this->state->get_download_path(), true );

		if ( empty( $unpacked_path ) ) {
			return new Installer_Error( Installer_Error_Codes::MISSING_UNPACKED_PATH );
		}

		$this->state->set_unpacked_path( $unpacked_path );

		return $this->state;
	}
}
