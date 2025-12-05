<?php
namespace Elementor\Core\Editor\Loader\V1;

use Elementor\Core\Editor\Loader\Common\Editor_Common_Scripts_Settings;
use Elementor\Core\Editor\Loader\Editor_Base_Loader;
use Elementor\Plugin;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Editor_V1_Loader extends Editor_Base_Loader {
	/**
	 * @return void
	 */
	public function init() {
		// Loading UI and Icons v2 scrips for the use of new features that should live in V1.
		$packages_to_register = [ 'ui', 'icons', 'query' ];

		foreach ( $packages_to_register as $package ) {
			$this->assets_config_provider->load( $package );
		}
	}

	/**
	 * @return void
	 */
	public function register_scripts() {
		parent::register_scripts();

		$assets_url = $this->config->get( 'assets_url' );
		$min_suffix = $this->config->get( 'min_suffix' );

		foreach ( $this->assets_config_provider->all() as $package => $config ) {
			wp_register_script(
				$config['handle'],
				"{$assets_url}js/packages/{$package}/{$package}{$min_suffix}.js",
				$config['deps'],
				ELEMENTOR_VERSION,
				true
			);
		}

		wp_register_script(
			'elementor-editor-loader-v1',
			"{$assets_url}js/editor-loader-v1{$min_suffix}.js",
			[ 'elementor-editor' ],
			ELEMENTOR_VERSION,
			true
		);
	}

	/**
	 * @return void
	 */
	public function enqueue_scripts() {
		parent::enqueue_scripts();

		// Must be last.
		wp_enqueue_script( 'elementor-editor-loader-v1' );

		Utils::print_js_config(
			'elementor-editor',
			'ElementorConfig',
			Editor_Common_Scripts_Settings::get()
		);
	}

	/**
	 * @return void
	 */
	public function print_root_template() {
		// Exposing the path for the view part to render the body of the editor template.
		$body_file_path = __DIR__ . '/templates/editor-body-v1-view.php';

		include ELEMENTOR_PATH . 'includes/editor-templates/editor-wrapper.php';
	}

	/**
	 * @return void
	 */
	public function register_additional_templates() {
		parent::register_additional_templates();

		Plugin::$instance->common->add_template( ELEMENTOR_PATH . 'includes/editor-templates/responsive-bar.php' );
	}
}
