<?php
namespace Elementor\Core\Kits\Documents\Tabs;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Theme_Style_Typography extends Tab_Base {

	public function get_id() {
		return 'theme-style-typography';
	}

	public function get_title() {
		return esc_html__( 'Typography', 'elementor' );
	}

	public function get_group() {
		return 'theme-style';
	}

	public function get_icon() {
		return 'eicon-typography-1';
	}

	public function get_help_url() {
		return 'https://go.elementor.com/global-theme-style-typography/';
	}

	public function register_tab_controls() {
		$this->start_controls_section(
			'section_typography',
			[
				'label' => esc_html__( 'Typography', 'elementor' ),
				'tab' => $this->get_id(),
			]
		);

		$this->add_default_globals_notice();

		$this->add_control(
			'body_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Body', 'elementor' ),
			]
		);

		$this->add_control(
			'body_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => [],
				'selectors' => [
					'{{WRAPPER}}' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'body_typography',
				'selector' => '{{WRAPPER}}',
			]
		);

		$this->add_responsive_control(
			'paragraph_spacing',
			[
				'label' => esc_html__( 'Paragraph Spacing', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} p' => 'margin-block-end: {{SIZE}}{{UNIT}}',
				],
				'range' => [
					'px' => [
						'max' => 100,
					],
					'em' => [
						'min' => 0.1,
						'max' => 20,
					],
				],
				'size_units' => [ 'px', 'em', 'rem', 'vh', 'custom' ],
			]
		);

		// Link Selectors
		$link_selectors = [
			'{{WRAPPER}} a',
		];

		$link_hover_selectors = [
			'{{WRAPPER}} a:hover',
		];

		$link_selectors = implode( ',', $link_selectors );
		$link_hover_selectors = implode( ',', $link_hover_selectors );

		$this->add_control(
			'link_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Link', 'elementor' ),
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'tabs_link_style' );

		$this->start_controls_tab(
			'tab_link_normal',
			[
				'label' => esc_html__( 'Normal', 'elementor' ),
			]
		);

		$this->add_control(
			'link_normal_color',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => [],
				'selectors' => [
					$link_selectors => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'link_normal_typography',
				'selector' => $link_selectors,
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_link_hover',
			[
				'label' => esc_html__( 'Hover', 'elementor' ),
			]
		);

		$this->add_control(
			'link_hover_color',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => [],
				'selectors' => [
					$link_hover_selectors => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'link_hover_typography',
				'selector' => $link_hover_selectors,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		// Headings.
		$this->add_element_controls( 'H1', 'h1', '{{WRAPPER}} h1' );
		$this->add_element_controls( 'H2', 'h2', '{{WRAPPER}} h2' );
		$this->add_element_controls( 'H3', 'h3', '{{WRAPPER}} h3' );
		$this->add_element_controls( 'H4', 'h4', '{{WRAPPER}} h4' );
		$this->add_element_controls( 'H5', 'h5', '{{WRAPPER}} h5' );
		$this->add_element_controls( 'H6', 'h6', '{{WRAPPER}} h6' );

		$this->end_controls_section();
	}

	private function add_element_controls( $label, $prefix, $selector ) {
		$this->add_control(
			$prefix . '_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => $label,
				'separator' => 'before',
			]
		);

		$this->add_control(
			$prefix . '_color',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => [],
				'selectors' => [
					$selector => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => $prefix . '_typography',
				'selector' => $selector,
			]
		);
	}
}
