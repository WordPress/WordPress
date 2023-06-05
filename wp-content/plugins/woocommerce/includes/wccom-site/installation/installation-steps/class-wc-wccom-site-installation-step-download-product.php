<?php
/**
 * Download product step.
 *
 * @package WooCommerce\WCCom
 * @since   7.7.0
 */

use WC_REST_WCCOM_Site_Installer_Error_Codes as Installer_Error_Codes;
use WC_REST_WCCOM_Site_Installer_Error as Installer_Error;

defined( 'ABSPATH' ) || exit;

/**
 * WC_WCCOM_Site_Installation_Step_Download_Product class
 */
class WC_WCCOM_Site_Installation_Step_Download_Product implements WC_WCCOM_Site_Installation_Step {
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
	 *
	 * @throws Installer_Error Installer Error.
	 */
	public function run() {
		$upgrader = WC_WCCOM_Site_Installer::get_wp_upgrader();

		$download_path = $upgrader->download_package( $this->state->get_download_url() );

		if ( empty( $download_path ) ) {
			throw new Installer_Error( Installer_Error_Codes::MISSING_DOWNLOAD_PATH );
		}

		$this->state->set_download_path( $download_path );

		return $this->state;
	}
}
