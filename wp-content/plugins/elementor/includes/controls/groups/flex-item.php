<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Group_Control_Flex_Item extends Group_Control_Base {

	protected static $fields;

	public static function get_type() {
		return 'flex-item';
	}

	protected function init_fields() {
		$fields = [];

		$fields['basis_type'] = [
			'label' => esc_html__( 'Flex Basis', 'elementor' ),
			'type' => Controls_Manager::SELECT,
			'options' => [
				'' => esc_html__( 'Default', 'elementor' ),
				'custom' => esc_html__( 'Custom', 'elementor' ),
			],
			'responsive' => true,
		];

		$fields['basis'] = [
			'label' => esc_html__( 'Custom Width', 'elementor' ),
			'type' => Controls_Manager::SLIDER,
			'range' => [
				'px' => [
					'max' => 1000,
				],
			],
			'default' => [
				'unit' => '%',
			],
			'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
			'selectors' => [
				'{{SELECTOR}}' => '--flex-basis: {{SIZE}}{{UNIT}};',
			],
			'condition' => [
				'basis_type' => 'custom',
			],
			'responsive' => true,
		];

		$fields['align_self'] = [
			'label' => esc_html__( 'Align Self', 'elementor' ),
			'type' => Controls_Manager::CHOOSE,
			'options' => [
				'flex-start' => [
					'title' => esc_html__( 'Start', 'elementor' ),
					'icon' => 'eicon-flex eicon-align-start-v',
				],
				'center' => [
					'title' => esc_html__( 'Center', 'elementor' ),
					'icon' => 'eicon-flex eicon-align-center-v',
				],
				'flex-end' => [
					'title' => esc_html__( 'End', 'elementor' ),
					'icon' => 'eicon-flex eicon-align-end-v',
				],
				'stretch' => [
					'title' => esc_html__( 'Stretch', 'elementor' ),
					'icon' => 'eicon-flex eicon-align-stretch-v',
				],
			],
			'default' => '',
			'selectors' => [
				'{{SELECTOR}}' => '--align-self: {{VALUE}};',
			],
			'responsive' => true,
			'description' => esc_html__( 'This control will affect contained elements only.', 'elementor' ),
		];

		$fields['order'] = [
			'label' => esc_html__( 'Order', 'elementor' ),
			'type' => Controls_Manager::CHOOSE,
			'default' => '',
			'options' => [
				'start' => [
					'title' => esc_html__( 'Start', 'elementor' ),
					'icon' => 'eicon-flex eicon-order-start',
				],
				'end' => [
					'title' => esc_html__( 'End', 'elementor' ),
					'icon' => 'eicon-flex eicon-order-end',
				],
				'custom' => [
					'title' => esc_html__( 'Custom', 'elementor' ),
					'icon' => 'eicon-ellipsis-v',
				],
			],
			'selectors_dictionary' => [
				// Hacks to set the order to start / end.
				// For example, if the user has 10 widgets, but wants to set the 5th one to be first,
				// this hack should do the trick while taking in account elements with `order: 0` or less.
				'start' => '-99999 /* order start hack */',
				'end' => '99999 /* order end hack */',
				'custom' => '',
			],
			'selectors' => [
				'{{SELECTOR}}' => '--order: {{VALUE}};',
			],
			'responsive' => true,
			'description' => esc_html__( 'This control will affect contained elements only.', 'elementor' ),
		];

		$fields['order_custom'] = [
			'label' => esc_html__( 'Custom Order', 'elementor' ),
			'type' => Controls_Manager::NUMBER,
			'selectors' => [
				'{{SELECTOR}}' => '--order: {{VALUE}};',
			],
			'responsive' => true,
			'condition' => [
				'order' => 'custom',
			],
		];

		$fields['size'] = [
			'label' => esc_html__( 'Size', 'elementor' ),
			'type' => Controls_Manager::CHOOSE,
			'default' => '',
			'options' => [
				'none' => [
					'title' => esc_html__( 'None', 'elementor' ),
					'icon' => 'eicon-ban',
				],
				'grow' => [
					'title' => esc_html__( 'Grow', 'elementor' ),
					'icon' => 'eicon-grow',
				],
				'shrink' => [
					'title' => esc_html__( 'Shrink', 'elementor' ),
					'icon' => 'eicon-shrink',
				],
				'custom' => [
					'title' => esc_html__( 'Custom', 'elementor' ),
					'icon' => 'eicon-ellipsis-v',
				],
			],
			'selectors_dictionary' => [
				'grow' => '--flex-grow: 1; --flex-shrink: 0;',
				'shrink' => '--flex-grow: 0; --flex-shrink: 1;',
				'custom' => '',
				'none' => '--flex-grow: 0; --flex-shrink: 0;',
			],
			'selectors' => [
				'{{SELECTOR}}' => '{{VALUE}};',
			],
			'responsive' => true,
		];

		$fields['grow'] = [
			'label' => esc_html__( 'Flex Grow', 'elementor' ),
			'type' => Controls_Manager::NUMBER,
			'selectors' => [
				'{{SELECTOR}}' => '--flex-grow: {{VALUE}};',
			],
			'default' => 1,
			'placeholder' => 1,
			'responsive' => true,
			'condition' => [
				'size' => 'custom',
			],
		];

		$fields['shrink'] = [
			'label' => esc_html__( 'Flex Shrink', 'elementor' ),
			'type' => Controls_Manager::NUMBER,
			'selectors' => [
				'{{SELECTOR}}' => '--flex-shrink: {{VALUE}};',
			],
			'default' => 1,
			'placeholder' => 1,
			'responsive' => true,
			'condition' => [
				'size' => 'custom',
			],
		];

		return $fields;
	}

	protected function get_default_options() {
		return [
			'popover' => false,
		];
	}
}
