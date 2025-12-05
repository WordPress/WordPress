<?php
namespace Elementor;

use Elementor\Core\Common\Modules\Connect\Apps\Library;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor API.
 *
 * Elementor API handler class is responsible for communicating with Elementor
 * remote servers retrieving templates data and to send uninstall feedback.
 *
 * @since 1.0.0
 */
class Api {

	/**
	 * Elementor library option key.
	 */
	const LIBRARY_OPTION_KEY = 'elementor_remote_info_library';

	/**
	 * Elementor feed option key.
	 */
	const FEED_OPTION_KEY = 'elementor_remote_info_feed_data';

	const TRANSIENT_KEY_PREFIX = 'elementor_remote_info_api_data_';

	/**
	 * API info URL.
	 *
	 * Holds the URL of the info API.
	 *
	 * @access public
	 * @static
	 *
	 * @var string API info URL. (v2 excludes the Library info)
	 */
	public static $api_info_url = 'https://my.elementor.com/api/v2/info/';

	/**
	 * API feedback URL.
	 *
	 * Holds the URL of the feedback API.
	 *
	 * @access private
	 * @static
	 *
	 * @var string API feedback URL.
	 */
	private static $api_feedback_url = 'https://my.elementor.com/api/v1/feedback/';

	private static $api_library_info_url = 'https://my.elementor.com/api/v1/templates/info/';

	private static function get_info_data( $force_update = false, $additinal_status = false ) {
		$cache_key = self::TRANSIENT_KEY_PREFIX . ELEMENTOR_VERSION;

		$info_data = get_transient( $cache_key );

		if ( $force_update || empty( $info_data ) ) {
			$timeout = ( $force_update ) ? 25 : 8;

			$body_request = [
				// Which API version is used.
				'api_version' => ELEMENTOR_VERSION,
				// Which language to return.
				'site_lang' => get_bloginfo( 'language' ),
			];

			$site_key = self::get_site_key();
			if ( ! empty( $site_key ) ) {
				$body_request['site_key'] = $site_key;
			}

			if ( ! empty( $additinal_status ) ) {
				$body_request['status'] = $additinal_status;
				$timeout = 3;
			}

			$response = wp_remote_get( self::$api_info_url, [
				'timeout' => $timeout,
				'body' => $body_request,
			] );

			if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
				set_transient( $cache_key, [], 2 * HOUR_IN_SECONDS );

				return false;
			}

			$info_data = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( empty( $info_data ) || ! is_array( $info_data ) ) {
				set_transient( $cache_key, [], 2 * HOUR_IN_SECONDS );

				return false;
			}

			if ( isset( $info_data['library'] ) ) {
				unset( $info_data['library'] );
			}

			if ( isset( $info_data['feed'] ) ) {
				update_option( self::FEED_OPTION_KEY, $info_data['feed'], 'no' );

				unset( $info_data['feed'] );
			}

			set_transient( $cache_key, $info_data, 12 * HOUR_IN_SECONDS );
		}

		return $info_data;
	}

	public static function get_site_key() {
		if ( null === Plugin::$instance->common ) {
			return get_option( Library::OPTION_CONNECT_SITE_KEY );
		}

		/** @var Library $library */
		$library = Plugin::$instance->common->get_component( 'connect' )->get_app( 'library' );

		if ( ! $library || ! method_exists( $library, 'get_site_key' ) ) {
			return false;
		}

		return $library->get_site_key();
	}

	/**
	 * Get upgrade notice.
	 *
	 * Retrieve the upgrade notice if one exists, or false otherwise.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return array|false Upgrade notice, or false none exist.
	 */
	public static function get_upgrade_notice() {
		$data = self::get_info_data();

		if ( empty( $data['upgrade_notice'] ) ) {
			return false;
		}

		return $data['upgrade_notice'];
	}

	public static function get_admin_notice() {
		$data = self::get_info_data();
		if ( empty( $data['admin_notice'] ) ) {
			return false;
		}
		return $data['admin_notice'];
	}

	public static function get_canary_deployment_info( $force = false ) {
		$data = self::get_info_data( $force );

		if ( empty( $data['canary_deployment'] ) ) {
			return false;
		}

		return $data['canary_deployment'];
	}

	public static function get_promotion_widgets() {
		$data = self::get_info_data();

		if ( ! isset( $data['pro_widgets'] ) ) {
			$data['pro_widgets'] = [];
		}

		return $data['pro_widgets'];
	}

	/**
	 * Get templates data.
	 *
	 * Retrieve the templates data from a remote server.
	 *
	 * @since 2.0.0
	 * @access public
	 * @static
	 *
	 * @param bool $force_update Optional. Whether to force the data update or
	 *                                     not. Default is false.
	 *
	 * @return array The templates' data.
	 */
	public static function get_library_data( bool $force_update = false ): array {
		/**
		 * Filters the body of the request to get library templates data.
		 *
		 * @param-out array $body_request The body of the request.
		 */
		$body_request = apply_filters( 'elementor/remote/library/templates/request/body', [] );

		$site_key = self::get_site_key();
		if ( ! empty( $site_key ) ) {
			$body_request['site_key'] = $site_key;
		}

		/**
		 * Filters the URL to get library templates data.
		 *
		 * @param-out string $url The URL to get library templates data.
		 */
		$url = apply_filters( 'elementor/remote/library/templates/request/url', self::$api_library_info_url );

		$response = wp_remote_get( $url, [
			'timeout' => 25,
			'body' => $body_request,
		] );

		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			return [];
		}

		$library_data = json_decode( wp_remote_retrieve_body( $response ), true );

		/**
		 * Filters the library data to allow 3rd party extending the response data.
		 *
		 * @since 3.32.2
		 * @param-out array $library_data an array of templates data.
		 */
		$library_data = apply_filters( 'elementor/remote/library/data', $library_data );

		if ( empty( $library_data ) || ! is_array( $library_data ) ) {
			return [];
		}

		// the following update & get are a temporary measure, to allow 3rd party plugins inject more templates:
		update_option( self::LIBRARY_OPTION_KEY, $library_data, 'no' );

		return get_option( self::LIBRARY_OPTION_KEY, $library_data );
	}

	/**
	 * Get feed data.
	 *
	 * Retrieve the feed info data from remote elementor server.
	 *
	 * @since 1.9.0
	 * @access public
	 * @static
	 *
	 * @param bool $force_update Optional. Whether to force the data update or
	 *                                     not. Default is false.
	 *
	 * @return array Feed data.
	 */
	public static function get_feed_data( $force_update = false ) {
		self::get_info_data( $force_update );

		$feed = get_option( self::FEED_OPTION_KEY );

		if ( empty( $feed ) ) {
			return [];
		}

		return $feed;
	}

	public static function get_deactivation_data() {
		$data = self::get_info_data( true, 'deactivated' );

		if ( empty( $data['deactivate_data'] ) ) {
			return false;
		}

		return $data['deactivate_data'];
	}

	public static function get_uninstalled_data() {
		$data = self::get_info_data( true, 'uninstalled' );

		if ( empty( $data['uninstall_data'] ) ) {
			return false;
		}

		return $data['uninstall_data'];
	}

	/**
	 * Get template content.
	 *
	 * Retrieve the templates content received from a remote server.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return object|\WP_Error The template content.
	 */
	public static function get_template_content( $template_id ) {
		/** @var Library $library */
		$library = Plugin::$instance->common->get_component( 'connect' )->get_app( 'library' );

		return $library->get_template_content( $template_id );
	}

	/**
	 * Send Feedback.
	 *
	 * Fires a request to Elementor server with the feedback data.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param string $feedback_key  Feedback key.
	 * @param string $feedback_text Feedback text.
	 *
	 * @return array The response of the request.
	 */
	public static function send_feedback( $feedback_key, $feedback_text ) {
		return wp_remote_post( self::$api_feedback_url, [
			'timeout' => 30,
			'body' => [
				'api_version' => ELEMENTOR_VERSION,
				'site_lang' => get_bloginfo( 'language' ),
				'feedback_key' => $feedback_key,
				'feedback' => $feedback_text,
			],
		] );
	}

	/**
	 * Ajax reset API data.
	 *
	 * Reset Elementor library API data using an ajax call.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function ajax_reset_api_data() {
		check_ajax_referer( 'elementor_reset_library', '_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Permission denied' );
		}

		self::get_info_data( true );

		wp_send_json_success();
	}

	/**
	 * Init.
	 *
	 * Initialize Elementor API.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function init() {
		add_action( 'wp_ajax_elementor_reset_library', [ __CLASS__, 'ajax_reset_api_data' ] );
	}
}
