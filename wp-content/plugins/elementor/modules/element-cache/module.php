<?php
namespace Elementor\Modules\ElementCache;

use Elementor\Controls_Manager;
use Elementor\Core\Base\Module as BaseModule;
use Elementor\Core\Experiments\Manager as ExperimentsManager;
use Elementor\Element_Base;
use Elementor\Plugin;
use Elementor\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends BaseModule {

	const OPTION_UNIQUE_ID = '_elementor_element_cache_unique_id';

	public function get_name() {
		return 'element-cache';
	}

	public function __construct() {
		parent::__construct();

		$this->register_shortcode();

		add_filter( 'elementor/element_cache/unique_id', [ $this, 'get_unique_id' ] );

		$this->add_advanced_tab_actions();

		if ( is_admin() ) {
			add_action( 'elementor/admin/after_create_settings/' . Settings::PAGE_ID, [ $this, 'register_admin_fields' ], 100 );
		}

		$this->clear_cache_on_site_changed();
	}

	public function get_unique_id() {
		$unique_id = get_option( static::OPTION_UNIQUE_ID );

		if ( ! $unique_id ) {
			$unique_id = md5( uniqid( wp_generate_password() ) );
			update_option( static::OPTION_UNIQUE_ID, $unique_id );
		}

		return $unique_id;
	}

	private function register_shortcode() {
		add_shortcode( 'elementor-element', function ( $atts ) {
			if ( empty( $atts['data'] ) ) {
				return '';
			}

			if ( empty( $atts['k'] ) || $atts['k'] !== $this->get_unique_id() ) {
				return '';
			}

			$widget_data = json_decode( base64_decode( $atts['data'] ), true );

			if ( empty( $widget_data ) || ! is_array( $widget_data ) ) {
				return '';
			}

			ob_start();

			$element = Plugin::$instance->elements_manager->create_element_instance( $widget_data );

			if ( $element ) {
				$element->print_element();
			}

			return ob_get_clean();
		} );
	}

	private function add_advanced_tab_actions() {
		$hooks = [
			'elementor/element/common/_section_style/after_section_end' => '_css_classes', // Widgets
		];

		foreach ( $hooks as $hook => $injection_position ) {
			add_action(
				$hook,
				function( $element, $args ) use ( $injection_position ) {
					$this->add_control_to_advanced_tab( $element, $args, $injection_position );
				},
				10,
				2
			);
		}
	}

	private function add_control_to_advanced_tab( Element_Base $element, $args, $injection_position ) {
		$element->start_injection(
			[
				'of' => $injection_position,
			]
		);

		$control_data = [
			'label' => esc_html__( 'Cache Settings', 'elementor' ),
			'type' => Controls_Manager::SELECT,
			'default' => '',
			'options' => [
				'' => esc_html__( 'Default', 'elementor' ),
				'yes' => esc_html__( 'Inactive', 'elementor' ),
				'no' => esc_html__( 'Active', 'elementor' ),
			],
		];

		$element->add_control( '_element_cache', $control_data );

		$element->end_injection();
	}

	public function register_admin_fields( Settings $settings ) {
		$settings->add_field(
			Settings::TAB_PERFORMANCE,
			Settings::TAB_PERFORMANCE,
			'element_cache_ttl',
			[
				'label' => esc_html__( 'Element Cache', 'elementor' ),
				'field_args' => [
					'class' => 'elementor-element-cache-ttl',
					'type' => 'select',
					'std' => '24',
					'options' => [
						'disable' => esc_html__( 'Disable', 'elementor' ),
						'1' => esc_html__( '1 Hour', 'elementor' ),
						'6' => esc_html__( '6 Hours', 'elementor' ),
						'12' => esc_html__( '12 Hours', 'elementor' ),
						'24' => esc_html__( '1 Day', 'elementor' ),
						'72' => esc_html__( '3 Days', 'elementor' ),
						'168' => esc_html__( '1 Week', 'elementor' ),
						'336' => esc_html__( '2 Weeks', 'elementor' ),
						'720' => esc_html__( '1 Month', 'elementor' ),
						'8760' => esc_html__( '1 Year', 'elementor' ),
					],
					'desc' => esc_html__( 'Specify the duration for which data is stored in the cache. Elements caching speeds up loading by serving pre-rendered copies of elements, rather than rendering them fresh each time. This control ensures efficient performance and up-to-date content.', 'elementor' ),
				],
			]
		);
	}

	private function clear_cache_on_site_changed() {
		add_action( 'activated_plugin', [ $this, 'clear_cache' ] );
		add_action( 'deactivated_plugin', [ $this, 'clear_cache' ] );
		add_action( 'switch_theme', [ $this, 'clear_cache' ] );
		add_action( 'upgrader_process_complete', [ $this, 'clear_cache' ] );

		add_action( 'update_option_elementor_element_cache_ttl', [ $this, 'clear_cache' ] );
	}

	public function clear_cache() {
		Plugin::$instance->files_manager->clear_cache();
	}
}
