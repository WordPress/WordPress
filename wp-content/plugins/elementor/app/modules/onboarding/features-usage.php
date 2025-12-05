<?php

namespace Elementor\App\Modules\Onboarding;

use Elementor\Tracker;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Features_Usage {

	const ONBOARDING_FEATURES_OPTION = '_elementor_onboarding_features';

	public function register() {
		if ( ! Tracker::is_allow_track() ) {
			return;
		}

		add_filter( 'elementor/tracker/send_tracking_data_params', function ( array $params ) {
			$params['usages']['onboarding_features'] = $this->get_usage_data();

			return $params;
		} );
	}

	public function save_onboarding_features( $raw_post_data ) {
		if ( empty( $raw_post_data ) ) {
			return;
		}

		$post_data = json_decode( $raw_post_data, true );

		if ( empty( $post_data['features'] ) ) {
			return;
		}

		update_option( static::ONBOARDING_FEATURES_OPTION, $post_data['features'] );

		return [
			'status' => 'success',
			'payload' => [],
		];
	}

	private function get_usage_data() {
		return get_option( static::ONBOARDING_FEATURES_OPTION, [] );
	}
}
