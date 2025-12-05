<?php

namespace Elementor\Core\Kits\Documents\Tabs;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Settings_Lightbox extends Tab_Base {

	public function get_id() {
		return 'settings-lightbox';
	}

	public function get_title() {
		return esc_html__( 'Lightbox', 'elementor' );
	}

	public function get_group() {
		return 'settings';
	}

	public function get_icon() {
		return 'eicon-lightbox-expand';
	}

	public function get_help_url() {
		return 'https://go.elementor.com/global-lightbox/';
	}

	protected function register_tab_controls() {
		$this->start_controls_section(
			'section_' . $this->get_id(),
			[
				'label' => $this->get_title(),
				'tab' => $this->get_id(),
			]
		);

		$this->add_control(
			'global_image_lightbox',
			[
				'label' => esc_html__( 'Image Lightbox', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'description' => esc_html__( 'Open all image links in a lightbox popup window. The lightbox will automatically work on any link that leads to an image file.', 'elementor' ),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'lightbox_enable_counter',
			[
				'label' => esc_html__( 'Counter', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'lightbox_enable_fullscreen',
			[
				'label' => esc_html__( 'Fullscreen', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'lightbox_enable_zoom',
			[
				'label' => esc_html__( 'Zoom', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'lightbox_enable_share',
			[
				'label' => esc_html__( 'Share', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'lightbox_title_src',
			[
				'label' => esc_html__( 'Title', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__( 'None', 'elementor' ),
					'title' => esc_html__( 'Title', 'elementor' ),
					'caption' => esc_html__( 'Caption', 'elementor' ),
					'alt' => esc_html__( 'Alt', 'elementor' ),
					'description' => esc_html__( 'Description', 'elementor' ),
				],
				'default' => 'title',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'lightbox_description_src',
			[
				'label' => esc_html__( 'Description', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__( 'None', 'elementor' ),
					'title' => esc_html__( 'Title', 'elementor' ),
					'caption' => esc_html__( 'Caption', 'elementor' ),
					'alt' => esc_html__( 'Alt', 'elementor' ),
					'description' => esc_html__( 'Description', 'elementor' ),
				],
				'default' => 'description',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'lightbox_color',
			[
				'label' => esc_html__( 'Background Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.elementor-lightbox' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'lightbox_ui_color',
			[
				'label' => esc_html__( 'UI Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.elementor-lightbox' => '--lightbox-ui-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'lightbox_ui_color_hover',
			[
				'label' => esc_html__( 'UI Hover Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.elementor-lightbox' => '--lightbox-ui-color-hover: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'lightbox_text_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.elementor-lightbox' => '--lightbox-text-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'lightbox_icons_size',
			[
				'label' => esc_html__( 'Toolbar Icons Size', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'.elementor-lightbox' => '--lightbox-header-icons-size: {{SIZE}}{{UNIT}}',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'lightbox_slider_icons_size',
			[
				'label' => esc_html__( 'Navigation Icons Size', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'.elementor-lightbox' => '--lightbox-navigation-icons-size: {{SIZE}}{{UNIT}}',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();
	}
}
