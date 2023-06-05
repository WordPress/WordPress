<?php
/**
 * Move product to the correct location.
 *
 * @package WooCommerce\WCCom
 * @since   7.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_WCCOM_Site_Installation_Step_Move_Product class
 */
class WC_WCCOM_Site_Installation_Step_Move_Product implements WC_WCCOM_Site_Installation_Step {
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
		$upgrader = WC_WCCOM_Site_Installer::get_wp_upgrader();

		$destination = 'plugin' === $this->state->get_product_type()
			? WP_PLUGIN_DIR
			: get_theme_root();

		$package = array(
			'source'        => $this->state->get_unpacked_path(),
			'destination'   => $destination,
			'clear_working' => true,
			'hook_extra'    => array(
				'type'   => $this->state->get_product_type(),
				'action' => 'install',
			),
		);

		$result = $upgrader->install_package( $package );

		/**
		 * If install package returns error 'folder_exists' treat as success.
		 */
		if ( is_wp_error( $result ) && array_key_exists( 'folder_exists', $result->errors ) ) {
			$existing_folder_path = $result->error_data['folder_exists'];
			$plugin_info          = WC_WCCOM_Site_Installer::get_plugin_info( $existing_folder_path );

			$this->state->set_installed_path( $existing_folder_path );
			$this->state->set_already_installed_plugin_info( $plugin_info );

			return $this->state;
		}

		$this->state->set_installed_path( $result['destination'] );

		return $this->state;
	}
}
