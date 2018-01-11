<?php

/**
 * A class to easily manage licenses for Polylang Pro and addons
 *
 * @since 1.9
 */
class PLL_License {
	public $id, $name, $license_key, $license_data;
	private $file, $version, $author;
	private $api_url = 'https://polylang.pro';

	/**
	 * Constructor
	 *
	 * @since 1.9
	 *
	 * @param string $file
	 * @param string $item_name
	 * @param string $version
	 * @param string $author
	 * @param string $api_url optional
	 */
	function __construct( $file, $item_name, $version, $author, $api_url = null ) {
		$this->id      = sanitize_title( $item_name );
		$this->file    = $file;
		$this->name    = $item_name;
		$this->version = $version;
		$this->author  = $author;
		$this->api_url = empty( $api_url ) ? $this->api_url : $api_url;

		$licenses = get_option( 'polylang_licenses' );
		$this->license_key = empty( $licenses[ $this->id ]['key'] ) ? '' : $licenses[ $this->id ]['key'];
		if ( ! empty( $licenses[ $this->id ]['data'] ) ) {
			$this->license_data = $licenses[ $this->id ]['data'];
		}

		// Updater
		add_action( 'admin_init', array( $this, 'auto_updater' ), 0 );

		// Register settings
		add_filter( 'pll_settings_licenses', array( $this, 'settings' ) );

		// Weekly schedule
		if ( ! wp_next_scheduled( 'polylang_check_licenses' ) ) {
			wp_schedule_event( time(), 'weekly', 'polylang_check_licenses' );
		}

		add_action( 'polylang_check_licenses', array( $this, 'check_license' ) );
	}

	/**
	 * Auto updater
	 *
	 * @since 1.9
	 */
	public function auto_updater() {
		$args = array(
			'version'   => $this->version,
			'license'   => $this->license_key,
			'author'    => $this->author,
			'item_name' => $this->name,
		);

		// Setup the updater
		new PLL_Plugin_Updater( $this->api_url, $this->file, $args );
	}

	/**
	 * Registers the licence in the Settings
	 *
	 * @since 1.9
	 *
	 * @param array $items
	 * @return  array
	 */
	public function settings( $items ) {
		$items[ $this->id ] = $this;
		return $items;
	}

	/**
	 * Activate the license key
	 *
	 * @since 1.9
	 *
	 * @param string $license_key activation key
	 * @return object updated $this
	 */
	public function activate_license( $license_key ) {
		$this->license_key = $license_key;
		$this->api_request( 'activate_license' );

		// Tell WordPress to look for updates
		set_site_transient( 'update_plugins', null );
		return $this;
	}


	/**
	 * Deactivate the license key
	 *
	 * @since 1.9
	 *
	 * @return object updated $this
	 */
	public function deactivate_license() {
		$this->api_request( 'deactivate_license' );
		return $this;
	}

	/**
	 * Check if license key is valid
	 *
	 * @since 1.9
	 *
	 * @return object updated $this
	 */
	public function check_license() {
		$this->api_request( 'check_license' );
		return $this;
	}

	/**
	 * Sends an api request to check, activate or deactivate the license
	 * Updates the licenses option according to the status
	 *
	 * @since 1.9
	 *
	 * @param string $request check_license | activate_license | deactivate_license
	 */
	private function api_request( $request ) {
		$licenses = get_option( 'polylang_licenses' );
		unset( $licenses[ $this->id ], $this->license_data );

		if ( ! empty( $this->license_key ) ) {
			// Data to send in our API request
			$api_params = array(
				'edd_action' => $request,
				'license'    => $this->license_key,
				'item_name'  => urlencode( $this->name ),
				'url'        => home_url(),
			);

			// Call the API
			$response = wp_remote_post(
				$this->api_url,
				array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => $api_params,
				)
			);

			// Update the option only if we got a response
			if ( is_wp_error( $response ) ) {
				return;
			}

			// Save new license info
			$licenses[ $this->id ] = array( 'key' => $this->license_key );
			$data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( 'deactivated' !== $data->license ) {
				$licenses[ $this->id ]['data'] = $this->license_data = $data;
			}
		}

		update_option( 'polylang_licenses', $licenses ); // FIXME called multiple times when saving all licenses
	}
}
