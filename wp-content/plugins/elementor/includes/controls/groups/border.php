<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor border control.
 *
 * A base control for creating border control. Displays input fields to define
 * border type, border width and border color.
 *
 * @since 1.0.0
 */
class Group_Control_Border extends Group_Control_Base {

	/**
	 * Fields.
	 *
	 * Holds all the border control fields.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @static
	 *
	 * @var array Border control fields.
	 */
	protected static $fields;

	/**
	 * Get border control type.
	 *
	 * Retrieve the control type, in this case `border`.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return string Control type.
	 */
	public static function get_type() {
		return 'border';
	}

	/**
	 * Init fields.
	 *
	 * Initialize border control fields.
	 *
	 * @since 1.2.2
	 * @access protected
	 *
	 * @return array Control fields.
	 */
	protected function init_fields() {
		$fields = [];

		$fields['border'] = [
			'label' => esc_html__( 'Border Type', 'elementor' ),
			'type' => Controls_Manager::SELECT,
			'options' => [
				'' => esc_html__( 'Default', 'elementor' ),
				'none' => esc_html__( 'None', 'elementor' ),
				'solid' => esc_html__( 'Solid', 'elementor' ),
				'double' => esc_html__( 'Double', 'elementor' ),
				'dotted' => esc_html__( 'Dotted', 'elementor' ),
				'dashed' => esc_html__( 'Dashed', 'elementor' ),
				'groove' => esc_html__( 'Groove', 'elementor' ),
			],
			'selectors' => [
				'{{SELECTOR}}' => 'border-style: {{VALUE}};',
			],
		];

		$fields['width'] = [
			'label' => esc_html__( 'Border Width', 'elementor' ),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', 'rem', 'vw', 'custom' ],
			'selectors' => [
				'{{SELECTOR}}' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'condition' => [
				'border!' => [ '', 'none' ],
			],
			'responsive' => true,
		];

		$fields['color'] = [
			'label' => esc_html__( 'Border Color', 'elementor' ),
			'type' => Controls_Manager::COLOR,
			'default' => '',
			'selectors' => [
				'{{SELECTOR}}' => 'border-color: {{VALUE}};',
			],
			'condition' => [
				'border!' => [ '', 'none' ],
			],
		];

		return $fields;
	}

	/**
	 * Get default options.
	 *
	 * Retrieve the default options of the border control. Used to return the
	 * default options while initializing the border control.
	 *
	 * @since 1.9.0
	 * @access protected
	 *
	 * @return array Default border control options.
	 */
	protected function get_default_options() {
		return [
			'popover' => false,
		];
	}
}
