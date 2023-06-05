<?php
/**
 * The database service class file.
 *
 * @version 3.9.0
 * @package WooCommerce\Integrations
 */

defined( 'ABSPATH' ) || exit;

/**
 * The service class responsible for interacting with MaxMind databases.
 *
 * @since 3.9.0
 */
class WC_Integration_MaxMind_Database_Service {

	/**
	 * The name of the MaxMind database to utilize.
	 */
	const DATABASE = 'GeoLite2-Country';

	/**
	 * The extension for the MaxMind database.
	 */
	const DATABASE_EXTENSION = '.mmdb';

	/**
	 * A prefix for the MaxMind database filename.
	 *
	 * @var string
	 */
	private $database_prefix;

	/**
	 * WC_Integration_MaxMind_Database_Service constructor.
	 *
	 * @param string|null $database_prefix A prefix for the MaxMind database filename.
	 */
	public function __construct( $database_prefix ) {
		$this->database_prefix = $database_prefix;
	}

	/**
	 * Fetches the path that the database should be stored.
	 *
	 * @return string The local database path.
	 */
	public function get_database_path() {
		$uploads_dir = wp_upload_dir();

		$database_path = trailingslashit( $uploads_dir['basedir'] ) . 'woocommerce_uploads/';
		if ( ! empty( $this->database_prefix ) ) {
			$database_path .= $this->database_prefix . '-';
		}
		$database_path .= self::DATABASE . self::DATABASE_EXTENSION;

		/**
		 * Filter the geolocation database storage path.
		 *
		 * @param string $database_path The path to the database.
		 * @param int $version Deprecated since 3.4.0.
		 * @deprecated 3.9.0
		 */
		$database_path = apply_filters_deprecated(
			'woocommerce_geolocation_local_database_path',
			array( $database_path, 2 ),
			'3.9.0',
			'woocommerce_maxmind_geolocation_database_path'
		);

		/**
		 * Filter the geolocation database storage path.
		 *
		 * @since 3.9.0
		 * @param string $database_path The path to the database.
		 */
		return apply_filters( 'woocommerce_maxmind_geolocation_database_path', $database_path );
	}

	/**
	 * Fetches the database from the MaxMind service.
	 *
	 * @param string $license_key The license key to be used when downloading the database.
	 * @return string|WP_Error The path to the database file or an error if invalid.
	 */
	public function download_database( $license_key ) {
		$download_uri = add_query_arg(
			array(
				'edition_id'  => self::DATABASE,
				'license_key' => urlencode( wc_clean( $license_key ) ),
				'suffix'      => 'tar.gz',
			),
			'https://download.maxmind.com/app/geoip_download'
		);

		// Needed for the download_url call right below.
		require_once ABSPATH . 'wp-admin/includes/file.php';

		$tmp_archive_path = download_url( esc_url_raw( $download_uri ) );
		if ( is_wp_error( $tmp_archive_path ) ) {
			// Transform the error into something more informative.
			$error_data = $tmp_archive_path->get_error_data();
			if ( isset( $error_data['code'] ) ) {
				switch ( $error_data['code'] ) {
					case 401:
						return new WP_Error(
							'woocommerce_maxmind_geolocation_database_license_key',
							__( 'The MaxMind license key is invalid. If you have recently created this key, you may need to wait for it to become active.', 'woocommerce' )
						);
				}
			}

			return new WP_Error( 'woocommerce_maxmind_geolocation_database_download', __( 'Failed to download the MaxMind database.', 'woocommerce' ) );
		}

		// Extract the database from the archive.
		try {
			$file = new PharData( $tmp_archive_path );

			$tmp_database_path = trailingslashit( dirname( $tmp_archive_path ) ) . trailingslashit( $file->current()->getFilename() ) . self::DATABASE . self::DATABASE_EXTENSION;

			$file->extractTo(
				dirname( $tmp_archive_path ),
				trailingslashit( $file->current()->getFilename() ) . self::DATABASE . self::DATABASE_EXTENSION,
				true
			);
		} catch ( Exception $exception ) {
			return new WP_Error( 'woocommerce_maxmind_geolocation_database_archive', $exception->getMessage() );
		} finally {
			// Remove the archive since we only care about a single file in it.
			unlink( $tmp_archive_path );
		}

		return $tmp_database_path;
	}

	/**
	 * Fetches the ISO country code associated with an IP address.
	 *
	 * @param string $ip_address The IP address to find the country code for.
	 * @return string The country code for the IP address, or empty if not found.
	 */
	public function get_iso_country_code_for_ip( $ip_address ) {
		$country_code = '';

		if ( ! class_exists( 'MaxMind\Db\Reader' ) ) {
			wc_get_logger()->notice( __( 'Missing MaxMind Reader library!', 'woocommerce' ), array( 'source' => 'maxmind-geolocation' ) );
			return $country_code;
		}

		$database_path = $this->get_database_path();
		if ( ! file_exists( $database_path ) ) {
			return $country_code;
		}

		try {
			$reader = new MaxMind\Db\Reader( $database_path );
			$data   = $reader->get( $ip_address );

			if ( isset( $data['country']['iso_code'] ) ) {
				$country_code = $data['country']['iso_code'];
			}

			$reader->close();
		} catch ( Exception $e ) {
			wc_get_logger()->notice( $e->getMessage(), array( 'source' => 'maxmind-geolocation' ) );
		}

		return $country_code;
	}
}
