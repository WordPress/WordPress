<?php

namespace Elementor\Core\Kits\Documents\Tabs;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Theme_Style_Form_Fields extends Tab_Base {

	public function get_id() {
		return 'theme-style-form-fields';
	}

	public function get_title() {
		return esc_html__( 'Form Fields', 'elementor' );
	}

	public function get_group() {
		return 'theme-style';
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	public function get_help_url() {
		return 'https://go.elementor.com/global-theme-style-form-fields/';
	}

	protected function register_tab_controls() {
		$label_selectors = [
			'{{WRAPPER}} label',
		];

		$input_selectors = [
			'{{WRAPPER}} input:not([type="button"]):not([type="submit"])',
			'{{WRAPPER}} textarea',
			'{{WRAPPER}} .elementor-field-textual',
		];

		$input_focus_selectors = [
			'{{WRAPPER}} input:focus:not([type="button"]):not([type="submit"])',
			'{{WRAPPER}} textarea:focus',
			'{{WRAPPER}} .elementor-field-textual:focus',
		];

		$label_selector = implode( ',', $label_selectors );
		$input_selector = implode( ',', $input_selectors );
		$input_focus_selector = implode( ',', $input_focus_selectors );

		$this->start_controls_section(
			'section_form_fields',
			[
				'label' => esc_html__( 'Form Fields', 'elementor' ),
				'tab' => $this->get_id(),
			]
		);

		$this->add_default_globals_notice();

		$this->add_control(
			'form_label_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Label', 'elementor' ),
			]
		);

		$this->add_control(
			'form_label_color',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => [],
				'selectors' => [
					$label_selector => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'form_label_typography',
				'selector' => $label_selector,
			]
		);

		$this->add_control(
			'form_field_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Field', 'elementor' ),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'form_field_typography',
				'selector' => $input_selector,
			]
		);

		$this->start_controls_tabs( 'tabs_form_field_style' );

		$this->start_controls_tab(
			'tab_form_field_normal',
			[
				'label' => esc_html__( 'Normal', 'elementor' ),
			]
		);

		$this->add_form_field_state_tab_controls( 'form_field', $input_selector );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_form_field_focus',
			[
				'label' => esc_html__( 'Focus', 'elementor' ),
			]
		);

		$this->add_form_field_state_tab_controls( 'form_field_focus', $input_focus_selector );

		$this->add_control(
			'form_field_focus_transition_duration',
			[
				'label' => esc_html__( 'Transition Duration', 'elementor' ) . ' (ms)',
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					$input_selector => 'transition: {{SIZE}}ms',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 3000,
						'step' => 100,
					],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'form_field_padding',
			[
				'label' => esc_html__( 'Padding', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					$input_selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();
	}

	private function add_form_field_state_tab_controls( $prefix, $selector ) {
		$this->add_control(
			$prefix . '_text_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => [],
				'selectors' => [
					$selector => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix . '_accent_color',
			[
				'label' => esc_html__( 'Accent Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => [],
				'selectors' => [
					$selector => 'accent-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix . '_background_color',
			[
				'label' => esc_html__( 'Background Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => [],
				'selectors' => [
					$selector => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => $prefix . '_box_shadow',
				'selector' => $selector,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => $prefix . '_border',
				'selector' => $selector,
				'fields_options' => [
					'color' => [
						'dynamic' => [],
					],
				],
			]
		);

		$this->add_control(
			$prefix . '_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
	}
}
