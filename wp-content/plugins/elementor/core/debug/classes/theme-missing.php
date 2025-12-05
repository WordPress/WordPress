<?php
namespace Elementor\Core\Debug\Classes;

use Elementor\Modules\SafeMode\Module as Safe_Mode;

class Theme_Missing extends Inspection_Base {

	public function run() {
		$safe_mode_enabled = get_option( Safe_Mode::OPTION_ENABLED, '' );
		if ( ! empty( $safe_mode_enabled ) ) {
			return true;
		}
		$theme = wp_get_theme();
		return $theme->exists();
	}

	public function get_name() {
		return 'theme-missing';
	}

	public function get_message() {
		return esc_html__( 'Some of your theme files are missing.', 'elementor' );
	}

	public function get_help_doc_url() {
		return 'https://go.elementor.com/preview-not-loaded/#theme-files';
	}
}
