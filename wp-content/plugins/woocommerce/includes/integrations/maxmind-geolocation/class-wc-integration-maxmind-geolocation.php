<?php
/**
 * MaxMind Geolocation Integration
 *
 * @version 3.9.0
 * @package WooCommerce\Integrations
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/class-wc-integration-maxmind-database-service.php';

/**
 * WC Integration MaxMind Geolocation
 *
 * @since 3.9.0
 */
class WC_Integration_MaxMind_Geolocation extends WC_Integration {

	/**
	 * The service responsible for interacting with the MaxMind database.
	 *
	 * @var WC_Integration_MaxMind_Database_Service
	 */
	private $database_service;

	/**
	 * Initialize the integration.
	 */
	public function __construct() {
		$this->id                 = 'maxmind_geolocation';
		$this->method_title       = __( 'MaxMind Geolocation', 'woocommerce' );
		$this->method_description = __( 'An integration for utilizing MaxMind to do Geolocation lookups. Please note that this integration will only do country lookups.', 'woocommerce' );

		/**
		 * Supports overriding the database service to be used.
		 *
		 * @since 3.9.0
		 * @return mixed|null The geolocation database service.
		 */
		$this->database_service = apply_filters( 'woocommerce_maxmind_geolocation_database_service', null );
		if ( null === $this->database_service ) {
			$this->database_service = new WC_Integration_MaxMind_Database_Service( $this->get_database_prefix() );
		}

		$this->init_form_fields();
		$this->init_settings();

		// Bind to the save action for the settings.
		add_action( 'woocommerce_update_options_integration_' . $this->id, array( $this, 'process_admin_options' ) );

		// Trigger notice if license key is missing.
		add_action( 'update_option_woocommerce_default_customer_address', array( $this, 'display_missing_license_key_notice' ), 1000, 2 );

		/**
		 * Allows for the automatic database update to be disabled.
		 *
		 * @deprecated 3.9.0
		 * @return bool Whether or not the database should be updated periodically.
		 */
		$bind_updater = apply_filters_deprecated(
			'woocommerce_geolocation_update_database_periodically',
			array( true ),
			'3.9.0',
			'woocommerce_maxmind_geolocation_update_database_periodically'
		);

		/**
		 * Allows for the automatic database update to be disabled.
		 * Note that MaxMind's TOS requires that the databases be updated or removed periodically.
		 *
		 * @since 3.9.0
		 * @param bool $bind_updater Whether or not the database should be updated periodically.
		 */
		$bind_updater = apply_filters( 'woocommerce_maxmind_geolocation_update_database_periodically', $bind_updater );

		// Bind to the scheduled updater action.
		if ( $bind_updater ) {
			add_action( 'woocommerce_geoip_updater', array( $this, 'update_database' ) );
		}

		// Bind to the geolocation filter for MaxMind database lookups.
		add_filter( 'woocommerce_get_geolocation', array( $this, 'get_geolocation' ), 10, 2 );
	}

	/**
	 * Override the normal options so we can print the database file path to the admin,
	 */
	public function admin_options() {
		parent::admin_options();

		include dirname( __FILE__ ) . '/views/html-admin-options.php';
	}

	/**
	 * Initializes the settings fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'license_key' => array(
				'title'       => __( 'MaxMind License Key', 'woocommerce' ),
				'type'        => 'password',
				'description' => sprintf(
					/* translators: %1$s: Documentation URL */
					__(
						'The key that will be used when dealing with MaxMind Geolocation services. You can read how to generate one in <a href="%1$s">MaxMind Geolocation Integration documentation</a>.',
						'woocommerce'
					),
					'https://docs.woocommerce.com/document/maxmind-geolocation-integration/'
				),
				'desc_tip'    => false,
				'default'     => '',
			),
		);
	}

	/**
	 * Get database service.
	 *
	 * @return WC_Integration_MaxMind_Database_Service|null
	 */
	public function get_database_service() {
		return $this->database_service;
	}

	/**
	 * Checks to make sure that the license key is valid.
	 *
	 * @param string $key The key of the field.
	 * @param mixed  $value The value of the field.
	 * @return mixed
	 * @throws Exception When the license key is invalid.
	 */
	public function validate_license_key_field( $key, $value ) {
		// Trim whitespaces and strip slashes.
		$value = $this->validate_password_field( $key, $value );

		// Empty license keys have no need test downloading a database.
		if ( empty( $value ) ) {
			return $value;
		}

		// Check the license key by attempting to download the Geolocation database.
		$tmp_database_path = $this->database_service->download_database( $value );
		if ( is_wp_error( $tmp_database_path ) ) {
			WC_Admin_Settings::add_error( $tmp_database_path->get_error_message() );

			// Throw an exception to keep from changing this value. This will prevent
			// users from accidentally losing their license key, which cannot
			// be viewed again after generating.
			throw new Exception( $tmp_database_path->get_error_message() );
		}

		// We may as well put this archive to good use, now that we've downloaded one.
		self::update_database( $tmp_database_path );

		// Remove missing license key notice.
		$this->remove_missing_license_key_notice();

		return $value;
	}

	/**
	 * Updates the database used for geolocation queries.
	 *
	 * @param string|null $new_database_path The path to the new database file. Null will fetch a new archive.
	 */
	public function update_database( $new_database_path = null ) {
		// Allow us to easily interact with the filesystem.
		require_once ABSPATH . 'wp-admin/includes/file.php';
		if ( ! WP_Filesystem() ) {
			wc_get_logger()->warning( __( 'Failed to initialise WC_Filesystem API while trying to update the MaxMind Geolocation database.', 'woocommerce' ) );
			return;
		}
		global $wp_filesystem;

		// Remove any existing archives to comply with the MaxMind TOS.
		$target_database_path = $this->database_service->get_database_path();

		// If there's no database path, we can't store the database.
		if ( empty( $target_database_path ) ) {
			return;
		}

		if ( $wp_filesystem->exists( $target_database_path ) ) {
			$wp_filesystem->delete( $target_database_path );
		}

		if ( isset( $new_database_path ) ) {
			$tmp_database_path = $new_database_path;
		} else {
			// We can't download a database if there's no license key configured.
			$license_key = $this->get_option( 'license_key' );
			if ( empty( $license_key ) ) {
				return;
			}

			$tmp_database_path = $this->database_service->download_database( $license_key );
			if ( is_wp_error( $tmp_database_path ) ) {
				wc_get_logger()->notice( $tmp_database_path->get_error_message(), array( 'source' => 'maxmind-geolocation' ) );
				return;
			}
		}

		// Move the new database into position.
		$wp_filesystem->move( $tmp_database_path, $target_database_path, true );
		$wp_filesystem->delete( dirname( $tmp_database_path ) );
	}

	/**
	 * Performs a geolocation lookup against the MaxMind database for the given IP address.
	 *
	 * @param array  $data       Geolocation data.
	 * @param string $ip_address The IP address to geolocate.
	 * @return array Geolocation including country code, state, city and postcode based on an IP address.
	 */
	public function get_geolocation( $data, $ip_address ) {
		// WooCommerce look for headers first, and at this moment could be just enough.
		if ( ! empty( $data['country'] ) ) {
			return $data;
		}

		if ( empty( $ip_address ) ) {
			return $data;
		}

		$country_code = $this->database_service->get_iso_country_code_for_ip( $ip_address );

		return array(
			'country'  => $country_code,
			'state'    => '',
			'city'     => '',
			'postcode' => '',
		);
	}

	/**
	 * Fetches the prefix for the MaxMind database file.
	 *
	 * @return string
	 */
	private function get_database_prefix() {
		$prefix = $this->get_option( 'database_prefix' );
		if ( empty( $prefix ) ) {
			$prefix = wp_generate_password( 32, false );
			$this->update_option( 'database_prefix', $prefix );
		}

		return $prefix;
	}

	/**
	 * Add missing license key notice.
	 */
	private function add_missing_license_key_notice() {
		if ( ! class_exists( 'WC_Admin_Notices' ) ) {
			include_once WC_ABSPATH . 'includes/admin/class-wc-admin-notices.php';
		}
		WC_Admin_Notices::add_notice( 'maxmind_license_key' );
	}

	/**
	 * Remove missing license key notice.
	 */
	private function remove_missing_license_key_notice() {
		if ( ! class_exists( 'WC_Admin_Notices' ) ) {
			include_once WC_ABSPATH . 'includes/admin/class-wc-admin-notices.php';
		}
		WC_Admin_Notices::remove_notice( 'maxmind_license_key' );
	}

	/**
	 * Display notice if license key is missing.
	 *
	 * @param mixed $old_value Option old value.
	 * @param mixed $new_value Current value.
	 */
	public function display_missing_license_key_notice( $old_value, $new_value ) {
		if ( ! apply_filters( 'woocommerce_maxmind_geolocation_display_notices', true ) ) {
			return;
		}

		if ( ! in_array( $new_value, array( 'geolocation', 'geolocation_ajax' ), true ) ) {
			$this->remove_missing_license_key_notice();
			return;
		}

		$license_key = $this->get_option( 'license_key' );
		if ( ! empty( $license_key ) ) {
			return;
		}

		$this->add_missing_license_key_notice();
	}
}
