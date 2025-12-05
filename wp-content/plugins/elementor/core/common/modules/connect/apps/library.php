<?php
namespace Elementor\Core\Common\Modules\Connect\Apps;

use Elementor\Api;
use Elementor\User;
use Elementor\Plugin;
use Elementor\Core\Common\Modules\Connect\Module as ConnectModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Library extends Common_App {

	public function get_title() {
		return esc_html__( 'Library', 'elementor' );
	}

	/**
	 * @since 2.3.0
	 * @access protected
	 */
	protected function get_slug() {
		return 'library';
	}

	public function get_template_content( $id ) {
		if ( ! $this->is_connected() ) {
			return new \WP_Error( '401', esc_html__( 'Connecting to the Library failed. Please try reloading the page and try again', 'elementor' ) );
		}

		$body_args = [
			'id' => $id,

			// Which API version is used.
			'api_version' => ELEMENTOR_VERSION,
			// Which language to return.
			'site_lang' => get_bloginfo( 'language' ),
		];

		/**
		 * API: Template body args.
		 *
		 * Filters the body arguments send with the GET request when fetching the content.
		 *
		 * @since 1.0.0
		 *
		 * @param array $body_args Body arguments.
		 */
		$body_args = apply_filters( 'elementor/api/get_templates/body_args', $body_args );

		$template_content = $this->request( 'get_template_content', $body_args, true );

		if ( is_wp_error( $template_content ) && 401 === $template_content->get_error_code() ) {
			// Normalize 401 message
			return new \WP_Error( 401, esc_html__( 'Connecting to the Library failed. Please try reloading the page and try again', 'elementor' ) );
		}

		return $template_content;
	}

	public function localize_settings( $settings ) {
		$is_connected = $this->is_connected();

		/** @var ConnectModule $connect */
		$connect = Plugin::$instance->common->get_component( 'connect' );
		$user_id = $this->get_user_id();

		return array_replace_recursive( $settings, [
			'library_connect' => [
				'is_connected' => $is_connected,
				'user_id' => $user_id,
				'subscription_plans' => $connect->get_subscription_plans( 'template-library' ),
				// TODO: Remove `base_access_level`.
				'base_access_level' => ConnectModule::ACCESS_LEVEL_CORE,
				'base_access_tier' => ConnectModule::ACCESS_TIER_FREE,
				'current_access_level' => ConnectModule::ACCESS_LEVEL_CORE,
				'current_access_tier' => ConnectModule::ACCESS_TIER_FREE,
				'plan_type' => ConnectModule::ACCESS_TIER_FREE,
			],
		] );
	}

	public function library_connect_popup_seen() {
		User::set_introduction_viewed( [
			'introductionKey' => 'library_connect',
		] );
	}

	/**
	 * @param \Elementor\Core\Common\Modules\Ajax\Module $ajax_manager
	 */
	public function register_ajax_actions( $ajax_manager ) {
		$ajax_manager->register_ajax_action( 'library_connect_popup_seen', [ $this, 'library_connect_popup_seen' ] );
	}

	private function get_user_id() {
		$token = $this->get( 'access_token' );

		if ( ! is_string( $token ) ) {
			return null;
		}

		$parts = explode( '.', $token );

		if ( count( $parts ) !== 3 ) {
			return null;
		}

		try {
			$payload_encoded = $parts[1];

			$payload_encoded = str_pad( $payload_encoded, strlen( $payload_encoded ) + ( 4 - strlen( $payload_encoded ) % 4 ) % 4, '=' );

			$payload_json = base64_decode( strtr( $payload_encoded, '-_', '+/' ), true );

			$payload = json_decode( $payload_json, true );

			if ( ! isset( $payload['sub'] ) ) {
				return null;
			}

			return $payload['sub'];
		} catch ( Exception $e ) {
			error_log( 'JWT Decoding Error: ' . $e->getMessage() );
			return null;
		}
	}

	/**
	 * After Connect
	 *
	 * After Connecting to the library, re-fetch the library data to get it up to date.
	 *
	 * @since 3.7.0
	 */
	protected function after_connect() {
		Api::get_library_data( true );
	}

	protected function get_app_info() {
		return [
			'user_common_data' => [
				'label' => 'User Common Data',
				'value' => get_user_option( $this->get_option_name(), get_current_user_id() ),
			],
			'connect_site_key' => [
				'label' => 'Site Key',
				'value' => get_option( self::OPTION_CONNECT_SITE_KEY ),
			],
		];
	}

	protected function get_popup_success_event_data() {
		return [
			'access_level' => ConnectModule::ACCESS_LEVEL_CORE,
			'access_tier' => ConnectModule::ACCESS_TIER_FREE,
			'plan_type' => ConnectModule::ACCESS_TIER_FREE,
			'tracking_opted_in' => $this->get( 'data_share_opted_in' ) ?? false,
			'user_id' => $this->get_user_id(),
		];
	}

	protected function init() {
		add_filter( 'elementor/editor/localize_settings', [ $this, 'localize_settings' ] );
		add_filter( 'elementor/common/localize_settings', [ $this, 'localize_settings' ] );
		add_action( 'elementor/ajax/register_actions', [ $this, 'register_ajax_actions' ] );
	}
}
