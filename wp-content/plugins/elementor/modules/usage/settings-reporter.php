<?php
namespace Elementor\Modules\Usage;

use Elementor\Modules\System_Info\Reporters\Base as Base_Reporter;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Settings_Reporter extends Base_Reporter {

	public function get_title() {
		return esc_html__( 'Settings', 'elementor' );
	}

	public function get_fields() {
		return [
			'settings' => '',
		];
	}

	public function get_settings(): array {
		$usage_settings_text = '';

		$settings = Module::get_settings_usage();

		foreach ( $settings as $setting_name => $setting_value ) {
			$setting_value_text = is_array( $setting_value ) ? implode( ', ', $setting_value ) : $setting_value;

			$usage_settings_text .= '<tr><td>' . $setting_name . '</td><td>' . $setting_value_text . '</td></tr>';
		}

		return [
			'value' => $usage_settings_text,
		];
	}

	public function get_raw_settings(): array {
		$usage_settings = PHP_EOL;

		$settings = Module::get_settings_usage();

		foreach ( $settings as $setting_name => $setting_value ) {
			$setting_value_text = is_array( $setting_value ) ? implode( ', ', $setting_value ) : $setting_value;

			$usage_settings .= "\t" . $setting_name . ': ' . $setting_value_text . PHP_EOL;
		}

		return [
			'value' => $usage_settings,
		];
	}
}
