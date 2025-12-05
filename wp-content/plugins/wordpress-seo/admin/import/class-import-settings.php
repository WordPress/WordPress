<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Import
 */

/**
 * Class WPSEO_Import_Settings.
 *
 * Class with functionality to import the Yoast SEO settings.
 */
class WPSEO_Import_Settings {

	/**
	 * Nonce action key.
	 *
	 * @var string
	 */
	public const NONCE_ACTION = 'wpseo-import-settings';

	/**
	 * Holds the import status instance.
	 *
	 * @var WPSEO_Import_Status
	 */
	public $status;

	/**
	 * Holds the old WPSEO version.
	 *
	 * @var string
	 */
	private $old_wpseo_version;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->status = new WPSEO_Import_Status( 'import', false );
	}

	/**
	 * Imports the data submitted by the user.
	 *
	 * @return void
	 */
	public function import() {
		check_admin_referer( self::NONCE_ACTION );

		if ( ! WPSEO_Capability_Utils::current_user_can( 'wpseo_manage_options' ) ) {
			return;
		}

		if ( ! isset( $_POST['settings_import'] ) || ! is_string( $_POST['settings_import'] ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: The raw content will be parsed afterwards.
		$content = wp_unslash( $_POST['settings_import'] );

		if ( empty( $content ) ) {
			return;
		}

		$this->parse_options( $content );
	}

	/**
	 * Parse the options.
	 *
	 * @param string $raw_options The content to parse.
	 *
	 * @return void
	 */
	protected function parse_options( $raw_options ) {
		$options = parse_ini_string( $raw_options, true, INI_SCANNER_RAW );

		if ( is_array( $options ) && $options !== [] ) {
			$this->import_options( $options );

			return;
		}

		$this->status->set_msg( __( 'Settings could not be imported:', 'wordpress-seo' ) . ' ' . __( 'No settings found.', 'wordpress-seo' ) );
	}

	/**
	 * Parse the option group and import it.
	 *
	 * @param string $name         Name string.
	 * @param array  $option_group Option group data.
	 * @param array  $options      Options data.
	 *
	 * @return void
	 */
	protected function parse_option_group( $name, $option_group, $options ) {
		// Make sure that the imported options are cleaned/converted on import.
		$option_instance = WPSEO_Options::get_option_instance( $name );
		if ( is_object( $option_instance ) && method_exists( $option_instance, 'import' ) ) {
			$option_instance->import( $option_group, $this->old_wpseo_version, $options );
		}
	}

	/**
	 * Imports the options if found.
	 *
	 * @param array $options The options parsed from the provided settings.
	 *
	 * @return void
	 */
	protected function import_options( $options ) {
		if ( isset( $options['wpseo']['version'] ) && $options['wpseo']['version'] !== '' ) {
			$this->old_wpseo_version = $options['wpseo']['version'];
		}

		foreach ( $options as $name => $option_group ) {
			$this->parse_option_group( $name, $option_group, $options );
		}

		$this->status->set_msg( __( 'Settings successfully imported.', 'wordpress-seo' ) );
		$this->status->set_status( true );

		// Reset the cached option values.
		WPSEO_Options::clear_cache();
	}
}
