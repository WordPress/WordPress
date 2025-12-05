<?php

namespace Elementor\Modules\Shapes\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Modules\Shapes\Module as Shapes_Module;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Plugin;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor WordArt widget.
 *
 * Elementor widget that displays text along SVG path.
 */
class TextPath extends Widget_Base {

	const DEFAULT_PATH_FILL = '#E8178A';

	/**
	 * Get widget name.
	 *
	 * Retrieve Text Path widget name.
	 *
	 * @return string Widget name.
	 * @access public
	 */
	public function get_name() {
		return 'text-path';
	}

	public function get_group_name() {
		return 'shapes';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Text Path widget title.
	 *
	 * @return string Widget title.
	 * @access public
	 */
	public function get_title() {
		return esc_html__( 'Text Path', 'elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Text Path widget icon.
	 *
	 * @return string Widget icon.
	 * @access public
	 */
	public function get_icon() {
		return 'eicon-wordart';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 * @access public
	 */
	public function get_keywords() {
		return [ 'text path', 'word path', 'text on path', 'wordart', 'word art' ];
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 3.24.0
	 * @access public
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return [ 'widget-text-path' ];
	}

	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 * Register content controls under content tab.
	 */
	protected function register_content_tab() {
		$this->start_controls_section(
			'section_content_text_path',
			[
				'label' => esc_html__( 'Text Path', 'elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'text',
			[
				'label' => esc_html__( 'Text', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__( 'Add Your Curvy Text Here', 'elementor' ),
				'frontend_available' => true,
				'render_type' => 'none',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'path',
			[
				'label' => esc_html__( 'Path Type', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => Shapes_Module::get_paths(),
				'default' => 'wave',
			]
		);

		$this->add_control(
			'custom_path',
			[
				'label' => esc_html__( 'SVG', 'elementor' ),
				'type' => Controls_Manager::MEDIA,
				'media_types' => [
					'svg',
				],
				'condition' => [
					'path' => 'custom',
				],
				'dynamic' => [
					'active' => true,
				],
				'description' => sprintf(
					'%1$s <a target="_blank" href="https://go.elementor.com/text-path-create-paths/">%2$s</a>',
					esc_html__( 'Want to create custom text paths with SVG?', 'elementor' ),
					esc_html__( 'Learn more', 'elementor' )
				),
			]
		);

		$this->add_control(
			'link',
			[
				'label' => esc_html__( 'Link', 'elementor' ),
				'type' => Controls_Manager::URL,
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'Paste URL or type', 'elementor' ),
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => '',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementor' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--alignment: {{VALUE}}',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'text_path_direction',
			[
				'label' => esc_html__( 'Text Direction', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__( 'Default', 'elementor' ),
					'rtl' => esc_html__( 'RTL', 'elementor' ),
					'ltr' => esc_html__( 'LTR', 'elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}}' => '--direction: {{VALUE}}',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'show_path',
			[
				'label' => esc_html__( 'Show Path', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'elementor' ),
				'label_off' => esc_html__( 'Off', 'elementor' ),
				'return_value' => self::DEFAULT_PATH_FILL,
				'separator' => 'before',
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}' => '--path-stroke: {{VALUE}}; --path-fill: transparent;',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register style controls under style tab.
	 */
	protected function register_style_tab() {
		/**
		 * Text Path styling section.
		 */
		$this->start_controls_section(
			'section_style_text_path',
			[
				'label' => esc_html__( 'Text Path', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'size',
			[
				'label' => esc_html__( 'Size', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 10,
					],
					'px' => [
						'max' => 800,
						'step' => 50,
					],
				],
				'default' => [
					'size' => 500,
				],
				'tablet_default' => [
					'size' => 500,
				],
				'mobile_default' => [
					'size' => 500,
				],
				'selectors' => [
					'{{WRAPPER}}' => '--width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'rotation',
			[
				'label' => esc_html__( 'Rotate', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg', 'grad', 'rad', 'turn', 'custom' ],
				'default' => [
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--rotate: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'text_heading',
			[
				'label' => esc_html__( 'Text', 'elementor' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'selector' => '{{WRAPPER}}',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'fields_options' => [
					'font_size' => [
						'default' => [
							'size' => '20',
							'unit' => 'px',
						],
						'size_units' => [ 'px' ],
					],
					// Text decoration isn't an inherited property, so it's required to explicitly
					// target the specific `textPath` element.
					'text_decoration' => [
						'selectors' => [
							'{{WRAPPER}} textPath' => 'text-decoration: {{VALUE}};',
						],
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'text_stroke',
				'selector' => '{{WRAPPER}} textPath',
			]
		);

		$this->add_responsive_control(
			'word_spacing',
			[
				'label' => esc_html__( 'Word Spacing', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => -20,
						'max' => 20,
					],
					'em' => [
						'min' => -1,
						'max' => 1,
					],
					'rem' => [
						'min' => -1,
						'max' => 1,
					],
				],
				'default' => [
					'size' => '',
				],
				'tablet_default' => [
					'size' => '',
				],
				'mobile_default' => [
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--word-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'start_point',
			[
				'label' => esc_html__( 'Starting Point', 'elementor' ) . ' (%)',
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'frontend_available' => true,
				'render_type' => 'none',
			]
		);

		$this->start_controls_tabs( 'text_style' );

		/**
		 * Normal tab.
		 */
		$this->start_controls_tab(
			'text_normal',
			[
				'label' => esc_html__( 'Normal', 'elementor' ),
			]
		);

		$this->add_control(
			'text_color_normal',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}' => '--text-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		/**
		 * Hover tab.
		 */
		$this->start_controls_tab(
			'text_hover',
			[
				'label' => esc_html__( 'Hover', 'elementor' ),
			]
		);

		$this->add_control(
			'text_color_hover',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}' => '--text-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_animation',
			[
				'label' => esc_html__( 'Hover Animation', 'elementor' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->add_control(
			'hover_transition',
			[
				'label' => esc_html__( 'Transition Duration', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 's', 'ms', 'custom' ],
				'default' => [
					'unit' => 's',
					'size' => 0.3,
				],
				'selectors' => [
					'{{WRAPPER}}' => '--transition: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Path styling section.
		 */
		$this->start_controls_section(
			'section_style_path',
			[
				'label' => esc_html__( 'Path', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_path!' => '',
				],
			]
		);

		$this->start_controls_tabs( 'path_style' );

		/**
		 * Normal tab.
		 */
		$this->start_controls_tab(
			'path_normal',
			[
				'label' => esc_html__( 'Normal', 'elementor' ),
			]
		);

		$this->add_control(
			'path_fill_normal',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}' => '--path-fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'stroke_heading_normal',
			[
				'label' => esc_html__( 'Stroke', 'elementor' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'stroke_color_normal',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => self::DEFAULT_PATH_FILL,
				'selectors' => [
					'{{WRAPPER}}' => '--stroke-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'stroke_width_normal',
			[
				'label' => esc_html__( 'Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
					'em' => [
						'max' => 2,
					],
					'rem' => [
						'max' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--stroke-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();

		/**
		 * Hover tab.
		 */
		$this->start_controls_tab(
			'path_hover',
			[
				'label' => esc_html__( 'Hover', 'elementor' ),
			]
		);

		$this->add_control(
			'path_fill_hover',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}' => '--path-fill-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'stroke_heading_hover',
			[
				'label' => esc_html__( 'Stroke', 'elementor' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'stroke_color_hover',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}' => '--stroke-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'stroke_width_hover',
			[
				'label' => esc_html__( 'Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'default' => [
					'size' => '',
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
					'em' => [
						'max' => 2,
					],
					'rem' => [
						'max' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--stroke-width-hover: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'stroke_transition',
			[
				'label' => esc_html__( 'Transition Duration', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 's', 'ms', 'custom' ],
				'default' => [
					'unit' => 's',
					'size' => 0.3,
				],
				'selectors' => [
					'{{WRAPPER}}' => '--stroke-transition: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register Text Path widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->register_content_tab();
		$this->register_style_tab();
	}

	/**
	 * Render Text Path widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		// Get the path URL.
		$path_url = ( 'custom' === $settings['path'] )
			? wp_get_attachment_url( $settings['custom_path']['id'] )
			: Shapes_Module::get_path_url( $settings['path'] );

		// Remove the HTTP protocol to prevent Mixed Content error.
		$path_url = preg_replace( '/^https?:/i', '', $path_url );

		// Add Text Path attributes.
		$this->add_render_attribute( 'text_path', [
			'class' => 'e-text-path',
			'data-text' => htmlentities( esc_attr( $settings['text'] ) ),
			'data-url' => esc_url( $path_url ),
			'data-link-url' => esc_url( $settings['link']['url'] ?? '' ),
		] );

		// Add hover animation.
		if ( ! empty( $settings['hover_animation'] ) ) {
			$this->add_render_attribute( 'text_path', 'class', 'elementor-animation-' . $settings['hover_animation'] );
		}

		// Render.
		?>
		<div <?php $this->print_render_attribute_string( 'text_path' ); ?>></div>
		<?php
	}
}
