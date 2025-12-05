<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor CSS Filter control.
 *
 * A base control for applying css filters. Displays sliders to define the
 * values of different CSS filters including blur, brightens, contrast,
 * saturation and hue.
 *
 * @since 2.1.0
 */
class Group_Control_Css_Filter extends Group_Control_Base {

	/**
	 * Prepare fields.
	 *
	 * Process css_filter control fields before adding them to `add_control()`.
	 *
	 * @since 2.1.0
	 * @access protected
	 *
	 * @var array $fields CSS filter control fields.
	 *
	 * @return array Processed fields.
	 */
	protected static $fields;

	/**
	 * Get CSS filter control type.
	 *
	 * Retrieve the control type, in this case `css-filter`.
	 *
	 * @since 2.1.0
	 * @access public
	 * @static
	 *
	 * @return string Control type.
	 */
	public static function get_type() {
		return 'css-filter';
	}

	/**
	 * Init fields.
	 *
	 * Initialize CSS filter control fields.
	 *
	 * @since 2.1.0
	 * @access protected
	 *
	 * @return array Control fields.
	 */
	protected function init_fields() {
		$controls = [];

		$controls['blur'] = [
			'label' => esc_html_x( 'Blur', 'Filter Control', 'elementor' ),
			'type' => Controls_Manager::SLIDER,
			'required' => 'true',
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 10,
					'step' => 0.1,
				],
			],
			'default' => [
				'size' => 0,
			],
			'selectors' => [
				'{{SELECTOR}}' => 'filter: brightness( {{brightness.SIZE}}% ) contrast( {{contrast.SIZE}}% ) saturate( {{saturate.SIZE}}% ) blur( {{blur.SIZE}}px ) hue-rotate( {{hue.SIZE}}deg )',
			],
		];

		$controls['brightness'] = [
			'label' => esc_html_x( 'Brightness', 'Filter Control', 'elementor' ),
			'type' => Controls_Manager::SLIDER,
			'render_type' => 'ui',
			'required' => 'true',
			'default' => [
				'size' => 100,
			],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
		];

		$controls['contrast'] = [
			'label' => esc_html_x( 'Contrast', 'Filter Control', 'elementor' ),
			'type' => Controls_Manager::SLIDER,
			'render_type' => 'ui',
			'required' => 'true',
			'default' => [
				'size' => 100,
			],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
		];

		$controls['saturate'] = [
			'label' => esc_html_x( 'Saturation', 'Filter Control', 'elementor' ),
			'type' => Controls_Manager::SLIDER,
			'render_type' => 'ui',
			'required' => 'true',
			'default' => [
				'size' => 100,
			],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
		];

		$controls['hue'] = [
			'label' => esc_html_x( 'Hue', 'Filter Control', 'elementor' ),
			'type' => Controls_Manager::SLIDER,
			'render_type' => 'ui',
			'required' => 'true',
			'default' => [
				'size' => 0,
			],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 360,
				],
			],
		];

		return $controls;
	}

	/**
	 * Get default options.
	 *
	 * Retrieve the default options of the CSS filter control. Used to return the
	 * default options while initializing the CSS filter control.
	 *
	 * @since 2.1.0
	 * @access protected
	 *
	 * @return array Default CSS filter control options.
	 */
	protected function get_default_options() {
		return [
			'popover' => [
				'starter_name' => 'css_filter',
				'starter_title' => esc_html__( 'CSS Filters', 'elementor' ),
				'settings' => [
					'render_type' => 'ui',
				],
			],
		];
	}
}
