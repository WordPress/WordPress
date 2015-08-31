<?php
/**
 * @package WPSEO\Admin\Import\External
 */

/**
 * Class WPSEO_Import_WooThemes_SEO
 *
 * Class with functionality to import Yoast SEO settings from WooThemes SEO
 */
class WPSEO_Import_AIOSEO extends WPSEO_Import_External {

	/**
	 * Holds the AOIOSEO options
	 *
	 * @var array
	 */
	private $aioseo_options;

	/**
	 * Import All In One SEO settings
	 */
	public function __construct() {
		parent::__construct();

		$this->aioseo_options = get_option( 'aioseop_options' );

		$this->import_metas();
		$this->import_ga();
	}

	/**
	 * Import All In One SEO meta values
	 */
	private function import_metas() {
		WPSEO_Meta::replace_meta( '_aioseop_description', WPSEO_Meta::$meta_prefix . 'metadesc', $this->replace );
		WPSEO_Meta::replace_meta( '_aioseop_keywords', WPSEO_Meta::$meta_prefix . 'metakeywords', $this->replace );
		WPSEO_Meta::replace_meta( '_aioseop_title', WPSEO_Meta::$meta_prefix . 'title', $this->replace );
	}

	/**
	 * Import the Google Analytics settings
	 */
	private function import_ga() {
		if ( isset( $this->aioseo_options['aiosp_google_analytics_id'] ) ) {

			if ( get_option( 'yst_ga' ) === false ) {
				update_option( 'yst_ga', $this->determine_ga_settings() );
			}

			$plugin_install_nonce = wp_create_nonce( 'install-plugin_google-analytics-for-wordpress' ); // Use the old name because that's the WordPress.org repo.

			$this->set_msg( __( sprintf(
				'All in One SEO data successfully imported. Would you like to %sdisable the All in One SEO plugin%s. You\'ve had Google Analytics enabled in All in One SEO, would you like to install our %sGoogle Analytics plugin%s?',
				'<a href="' . esc_url( admin_url( 'admin.php?page=wpseo_tools&tool=import-export&deactivate_aioseo=1#top#import-seo' ) ) . '">',
				'</a>',
				'<a href="' . esc_url( admin_url( 'update.php?action=install-plugin&plugin=google-analytics-for-wordpress&_wpnonce=' . $plugin_install_nonce ) ) . '">',
				'</a>'
			), 'wordpress-seo' ) );
		}
		else {
			$this->set_msg( __( sprintf( 'All in One SEO data successfully imported. Would you like to %sdisable the All in One SEO plugin%s.', '<a href="' . esc_url( admin_url( 'admin.php?page=wpseo_tools&tool=import-export&deactivate_aioseo=1#top#import-seo' ) ) . '">', '</a>' ), 'wordpress-seo' ) );
		}
	}

	/**
	 * Determine the appropriate GA settings for this site
	 *
	 * @return array $ga_settings
	 */
	private function determine_ga_settings() {
		$ga_universal = 0;
		if ( $this->aioseo_options['aiosp_ga_use_universal_analytics'] == 'on' ) {
			$ga_universal = 1;
		}

		$ga_track_outbound = 0;
		if ( $this->aioseo_options['aiosp_ga_track_outbound_links'] == 'on' ) {
			$ga_track_outbound = 1;
		}

		$ga_anonymize_ip = 0;
		if ( $this->aioseo_options['aiosp_ga_anonymize_ip'] == 'on' ) {
			$ga_anonymize_ip = 1;
		}

		return array(
			'ga_general' => array(
				'manual_ua_code'       => (int) 1,
				'manual_ua_code_field' => $this->aioseo_options['aiosp_google_analytics_id'],
				'enable_universal'     => $ga_universal,
				'track_outbound'       => $ga_track_outbound,
				'ignore_users'         => (array) $this->aioseo_options['aiosp_ga_exclude_users'],
				'anonymize_ips'        => (int) $ga_anonymize_ip,
			),
		);
	}
}
