<?php

namespace Elementor\Core\Common\Modules\EventsManager;

use Elementor\Core\Base\Module as BaseModule;
use Elementor\Core\Common\Modules\Connect\Apps\Base_App;
use Elementor\Core\Experiments\Manager as Experiments_Manager;
use Elementor\Utils;
use Elementor\Plugin;
use Elementor\Tracker;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends BaseModule {

	const EXPERIMENT_NAME = 'editor_events';

	public function get_name() {
		return 'events-manager';
	}

	public static function get_editor_events_config() {
		$can_send_events = ! empty( ELEMENTOR_EDITOR_EVENTS_MIXPANEL_TOKEN ) &&
			Tracker::is_allow_track() &&
			! Tracker::has_terms_changed( '2025-07-07' ) &&
			Plugin::$instance->experiments->is_feature_active( self::EXPERIMENT_NAME );

		$settings = [
			'can_send_events' => $can_send_events,
			'elementor_version' => ELEMENTOR_VERSION,
			'site_url' => hash( 'sha256', get_site_url() ),
			'wp_version' => get_bloginfo( 'version' ),
			'user_agent' => esc_html( Utils::get_super_global_value( $_SERVER, 'HTTP_USER_AGENT' ) ),
			'site_language' => get_locale(),
			'site_key' => get_option( Base_App::OPTION_CONNECT_SITE_KEY ),
			'subscription_id' => self::get_subscription_id(),
			'subscription' => self::get_subscription(),
			'token' => ELEMENTOR_EDITOR_EVENTS_MIXPANEL_TOKEN,
		];

		return $settings;
	}

	public static function get_experimental_data(): array {
		return [
			'name' => static::EXPERIMENT_NAME,
			'title' => esc_html__( 'Elementor Editor Events', 'elementor' ),
			'description' => esc_html__( 'Editor events processing', 'elementor' ),
			'hidden' => true,
			'release_status' => Experiments_Manager::RELEASE_STATUS_ALPHA,
			'default' => Experiments_Manager::STATE_INACTIVE,
			'new_site' => [
				'default_active' => true,
				'minimum_installation_version' => '3.32.0',
			],
		];
	}

	private static function get_subscription_id() {
		$subscription = self::get_subscription();

		return $subscription['subscription_id'] ?? null;
	}

	private static function get_subscription() {
		if ( ! Utils::has_pro() ) {
			return null;
		}

		$license_data = get_option( '_elementor_pro_license_v2_data' );
		if ( ! isset( $license_data['value'] ) ) {
			return null;
		}

		return json_decode( $license_data['value'], true );
	}
}
