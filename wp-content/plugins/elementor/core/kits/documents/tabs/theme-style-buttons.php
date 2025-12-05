<?php

namespace Elementor\Core\Kits\Documents\Tabs;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Theme_Style_Buttons extends Tab_Base {

	public function get_id() {
		return 'theme-style-buttons';
	}

	public function get_title() {
		return esc_html__( 'Buttons', 'elementor' );
	}

	public function get_group() {
		return 'theme-style';
	}

	public function get_icon() {
		return 'eicon-button';
	}

	public function get_help_url() {
		return 'https://go.elementor.com/global-theme-style-buttons/';
	}

	protected function register_tab_controls() {
		$button_selectors = [
			'{{WRAPPER}} button',
			'{{WRAPPER}} input[type="button"]',
			'{{WRAPPER}} input[type="submit"]',
			'{{WRAPPER}} .elementor-button',
		];

		$button_hover_selectors = [
			'{{WRAPPER}} button:hover',
			'{{WRAPPER}} button:focus',
			'{{WRAPPER}} input[type="button"]:hover',
			'{{WRAPPER}} input[type="button"]:focus',
			'{{WRAPPER}} input[type="submit"]:hover',
			'{{WRAPPER}} input[type="submit"]:focus',
			'{{WRAPPER}} .elementor-button:hover',
			'{{WRAPPER}} .elementor-button:focus',
		];

		$button_selector = implode( ',', $button_selectors );
		$button_hover_selector = implode( ',', $button_hover_selectors );

		$this->start_controls_section(
			'section_buttons',
			[
				'label' => esc_html__( 'Buttons', 'elementor' ),
				'tab' => $this->get_id(),
			]
		);

		$this->add_default_globals_notice();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => $button_selector,
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'button_text_shadow',
				'selector' => $button_selector,
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'elementor' ),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => [],
				'selectors' => [
					$button_selector => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_background',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => $button_selector,
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color' => [
						'dynamic' => [],
					],
					'color_b' => [
						'dynamic' => [],
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'selector' => $button_selector,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'button_border',
				'selector' => $button_selector,
				'fields_options' => [
					'color' => [
						'dynamic' => [],
					],
				],
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					$button_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'elementor' ),
			]
		);

		$this->add_control(
			'button_hover_text_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => [],
				'selectors' => [
					$button_hover_selector => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_hover_background',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => $button_hover_selector,
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color' => [
						'dynamic' => [],
					],
					'color_b' => [
						'dynamic' => [],
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_hover_box_shadow',
				'selector' => $button_hover_selector,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'button_hover_border',
				'selector' => $button_hover_selector,
				'fields_options' => [
					'color' => [
						'dynamic' => [],
					],
				],
			]
		);

		$this->add_control(
			'button_hover_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					$button_hover_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'button_padding',
			[
				'label' => esc_html__( 'Padding', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					$button_selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();
	}
}
