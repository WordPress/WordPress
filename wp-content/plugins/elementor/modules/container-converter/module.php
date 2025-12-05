<?php

namespace Elementor\Modules\ContainerConverter;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends \Elementor\Core\Base\Module {

	// Event name dispatched by the buttons.
	const EVENT_NAME = 'elementorContainerConverter:convert';

	/**
	 * Retrieve the module name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'container-converter';
	}

	/**
	 * Determine whether the module is active.
	 *
	 * @return bool
	 */
	public static function is_active() {
		return Plugin::$instance->experiments->is_feature_active( 'container' );
	}

	/**
	 * Enqueue the module scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			'container-converter',
			$this->get_js_assets_url( 'container-converter' ),
			[ 'elementor-editor' ],
			ELEMENTOR_VERSION,
			true
		);
	}

	/**
	 * Enqueue the module styles.
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			'container-converter',
			$this->get_css_assets_url( 'modules/container-converter/editor' ),
			[],
			ELEMENTOR_VERSION
		);
	}

	/**
	 * Add a convert button to sections.
	 *
	 * @param \Elementor\Controls_Stack $controls_stack
	 *
	 * @return void
	 */
	protected function add_section_convert_button( Controls_Stack $controls_stack ) {
		if ( ! Plugin::$instance->editor->is_edit_mode() ) {
			return;
		}

		$controls_stack->start_injection( [
			'of' => '_title',
		] );

		$controls_stack->add_control(
			'convert_to_container',
			[
				'type' => Controls_Manager::BUTTON,
				'label' => esc_html__( 'Convert to container', 'elementor' ),
				'text' => esc_html__( 'Convert', 'elementor' ),
				'button_type' => 'default',
				'description' => esc_html__( 'Copies all of the selected sections and columns and pastes them in a container beneath the original.', 'elementor' ),
				'separator' => 'after',
				'event' => static::EVENT_NAME,
			]
		);

		$controls_stack->end_injection();
	}

	/**
	 * Add a convert button to page settings.
	 *
	 * @param \Elementor\Controls_Stack $controls_stack
	 *
	 * @return void
	 */
	protected function add_page_convert_button( Controls_Stack $controls_stack ) {
		if ( ! Plugin::$instance->editor->is_edit_mode() || ! $this->page_contains_sections( $controls_stack ) || ! Plugin::$instance->role_manager->user_can( 'design' ) ) {
			return;
		}

		$controls_stack->start_injection( [
			'of' => 'post_title',
			'at' => 'before',
		] );

		$controls_stack->add_control(
			'convert_to_container',
			[
				'type' => Controls_Manager::BUTTON,
				'label' => esc_html__( 'Convert to container', 'elementor' ),
				'text' => esc_html__( 'Convert', 'elementor' ),
				'button_type' => 'default',
				'description' => esc_html__( 'Copies all of the selected sections and columns and pastes them in a container beneath the original.', 'elementor' ),
				'separator' => 'after',
				'event' => static::EVENT_NAME,
			]
		);

		$controls_stack->end_injection();
	}

	/**
	 * Checks if document has any Section elements.
	 *
	 * @param \Elementor\Controls_Stack $controls_stack
	 *
	 * @return bool
	 */
	protected function page_contains_sections( $controls_stack ) {
		$data = $controls_stack->get_elements_data();

		if ( ! is_array( $data ) ) {
			return false;
		}

		foreach ( $data as $element ) {
			if ( isset( $element['elType'] ) && 'section' === $element['elType'] ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Initialize the Container-Converter module.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'enqueue_styles' ] );

		add_action( 'elementor/element/section/section_layout/after_section_end', function ( Controls_Stack $controls_stack ) {
			$this->add_section_convert_button( $controls_stack );
		} );

		add_action( 'elementor/documents/register_controls', function ( Controls_Stack $controls_stack ) {
			$this->add_page_convert_button( $controls_stack );
		} );
	}
}
