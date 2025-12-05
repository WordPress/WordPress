<?php
namespace Elementor\Core\Editor\Loader;

use Elementor\Core\Utils\Assets_Config_Provider;
use Elementor\Core\Utils\Collection;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Editor_Base_Loader implements Editor_Loader_Interface {
	/**
	 * @var Collection
	 */
	protected $config;

	/**
	 * @var Assets_Config_Provider
	 */
	protected $assets_config_provider;

	/**
	 * @param Collection             $config
	 * @param Assets_Config_Provider $assets_config_provider
	 */
	public function __construct( Collection $config, Assets_Config_Provider $assets_config_provider ) {
		$this->config = $config;
		$this->assets_config_provider = $assets_config_provider;
	}

	/**
	 * @return void
	 */
	public function register_scripts() {
		$assets_url = $this->config->get( 'assets_url' );
		$min_suffix = $this->config->get( 'min_suffix' );

		wp_register_script(
			'elementor-editor-modules',
			"{$assets_url}js/editor-modules{$min_suffix}.js",
			[ 'elementor-common-modules' ],
			ELEMENTOR_VERSION,
			true
		);

		wp_register_script(
			'elementor-editor-document',
			"{$assets_url}js/editor-document{$min_suffix}.js",
			[ 'elementor-common-modules' ],
			ELEMENTOR_VERSION,
			true
		);

		wp_register_script(
			'perfect-scrollbar',
			"{$assets_url}lib/perfect-scrollbar/js/perfect-scrollbar{$min_suffix}.js",
			[],
			'1.4.0',
			true
		);

		wp_register_script(
			'jquery-easing',
			"{$assets_url}lib/jquery-easing/jquery-easing{$min_suffix}.js",
			[ 'jquery' ],
			'1.3.2',
			true
		);

		wp_register_script(
			'nprogress',
			"{$assets_url}lib/nprogress/nprogress{$min_suffix}.js",
			[],
			'0.2.0',
			true
		);

		wp_register_script(
			'tipsy',
			"{$assets_url}lib/tipsy/tipsy{$min_suffix}.js",
			[ 'jquery' ],
			'1.0.0',
			true
		);

		wp_register_script(
			'jquery-elementor-select2',
			"{$assets_url}lib/e-select2/js/e-select2.full{$min_suffix}.js",
			[ 'jquery' ],
			'4.0.6-rc.1',
			true
		);

		wp_register_script(
			'flatpickr',
			"{$assets_url}lib/flatpickr/flatpickr{$min_suffix}.js",
			[ 'jquery' ],
			'4.6.13',
			true
		);

		wp_register_script(
			'ace',
			'https://cdn.jsdelivr.net/npm/ace-builds@1.43.2/src-min-noconflict/ace.min.js',
			[],
			'1.43.2',
			true
		);

		wp_register_script(
			'ace-language-tools',
			'https://cdn.jsdelivr.net/npm/ace-builds@1.43.2/src-min-noconflict/ext-language_tools.js',
			[ 'ace' ],
			'1.43.2',
			true
		);

		wp_register_script(
			'jquery-hover-intent',
			"{$assets_url}lib/jquery-hover-intent/jquery-hover-intent{$min_suffix}.js",
			[],
			'1.0.0',
			true
		);

		wp_register_script(
			'nouislider',
			"{$assets_url}lib/nouislider/nouislider{$min_suffix}.js",
			[],
			'13.0.0',
			true
		);

		wp_register_script(
			'pickr',
			"{$assets_url}lib/pickr/pickr.min.js",
			[],
			'1.8.2',
			true
		);

		wp_register_script(
			'elementor-editor',
			"{$assets_url}js/editor{$min_suffix}.js",
			[
				'elementor-common',
				'elementor-editor-modules',
				'elementor-editor-document',
				'wp-auth-check',
				'jquery-ui-sortable',
				'jquery-ui-resizable',
				'perfect-scrollbar',
				'nprogress',
				'tipsy',
				'imagesloaded',
				'heartbeat',
				'jquery-elementor-select2',
				'flatpickr',
				'ace',
				'ace-language-tools',
				'jquery-hover-intent',
				'nouislider',
				'pickr',
				'react',
				'react-dom',
			],
			ELEMENTOR_VERSION,
			true
		);

		wp_set_script_translations( 'elementor-editor', 'elementor' );

		wp_register_script(
			'elementor-responsive-bar',
			"{$assets_url}js/responsive-bar{$min_suffix}.js",
			[ 'elementor-editor' ],
			ELEMENTOR_VERSION,
			true
		);

		wp_set_script_translations( 'elementor-responsive-bar', 'elementor' );
	}

	/**
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'elementor-responsive-bar' );
	}

	/**
	 * @return void
	 */
	public function register_styles() {
		$assets_url = $this->config->get( 'assets_url' );
		$min_suffix = $this->config->get( 'min_suffix' );
		$direction_suffix = $this->config->get( 'direction_suffix' );

		wp_register_style(
			'font-awesome',
			"{$assets_url}lib/font-awesome/css/font-awesome{$min_suffix}.css",
			[],
			'4.7.0'
		);

		wp_register_style(
			'elementor-select2',
			"{$assets_url}lib/e-select2/css/e-select2{$min_suffix}.css",
			[],
			'4.0.6-rc.1'
		);

		wp_register_style(
			'google-font-roboto',
			'https://fonts.googleapis.com/css?family=Roboto:300,400,500,700',
			[],
			ELEMENTOR_VERSION
		);

		wp_register_style(
			'flatpickr',
			"{$assets_url}lib/flatpickr/flatpickr{$min_suffix}.css",
			[],
			'4.6.13'
		);

		wp_register_style(
			'pickr',
			"{$assets_url}lib/pickr/themes/monolith.min.css",
			[],
			'1.8.2'
		);

		wp_register_style(
			'elementor-editor',
			"{$assets_url}css/editor{$direction_suffix}{$min_suffix}.css",
			[
				'elementor-common',
				'elementor-select2',
				'elementor-icons',
				'wp-auth-check',
				'google-font-roboto',
				'flatpickr',
				'pickr',
			],
			ELEMENTOR_VERSION
		);

		wp_register_style(
			'elementor-responsive-bar',
			"{$assets_url}css/responsive-bar{$min_suffix}.css",
			[],
			ELEMENTOR_VERSION
		);
	}

	/**
	 * @return void
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'elementor-editor' );

		wp_enqueue_style( 'elementor-responsive-bar' );
	}

	/**
	 * @return void
	 */
	public function register_additional_templates() {
		$templates = [
			'global',
			'panel',
			'panel-elements',
			'repeater',
			'templates',
			'navigator',
			'hotkeys',
			'responsive-bar',
		];

		$templates = apply_filters( 'elementor/editor/templates', $templates );

		foreach ( $templates as $template ) {
			Plugin::$instance->common->add_template( ELEMENTOR_PATH . "includes/editor-templates/{$template}.php" );
		}
	}
}
