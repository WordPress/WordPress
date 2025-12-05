<?php
namespace Elementor\Core\Debug\Classes;

use Elementor\Modules\SafeMode\Module as Safe_Mode;
use Elementor\Utils;

class Htaccess extends Inspection_Base {

	private $message = '';

	public function __construct() {
		$this->message = esc_html__( 'Your site\'s .htaccess file appears to be missing.', 'elementor' );
	}

	public function run() {
		$safe_mode_enabled = get_option( Safe_Mode::OPTION_ENABLED, '' );
		if ( empty( $safe_mode_enabled ) || is_multisite() ) {
			return true;
		}

		$permalink_structure = get_option( 'permalink_structure' );
		if ( empty( $permalink_structure ) || empty( $_SERVER['SERVER_SOFTWARE'] ) ) {
			return true;
		}

		$server = strtoupper( Utils::get_super_global_value( $_SERVER, 'SERVER_SOFTWARE' ) );

		if ( strstr( $server, 'APACHE' ) ) {
			$htaccess_file = get_home_path() . '.htaccess';
			/* translators: %s: Path to .htaccess file. */
			$this->message .= ' ' . sprintf( esc_html__( 'File Path: %s', 'elementor' ), $htaccess_file ) . ' ';
			return file_exists( $htaccess_file );
		}
		return true;
	}

	public function get_name() {
		return 'apache-htaccess';
	}

	public function get_message() {
		return $this->message;
	}

	public function get_help_doc_url() {
		return 'https://go.elementor.com/preview-not-loaded/#htaccess';
	}
}
