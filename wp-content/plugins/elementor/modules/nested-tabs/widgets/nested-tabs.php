<?php
namespace Elementor\Modules\NestedTabs\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Modules\NestedElements\Base\Widget_Nested_Base;
use Elementor\Modules\NestedElements\Controls\Control_Nested_Repeater;
use Elementor\Plugin;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class NestedTabs extends Widget_Nested_Base {

	private $tab_item_settings = [];
	private $optimized_markup = null;
	private $widget_container_selector = '';

	public function get_name() {
		return 'nested-tabs';
	}

	public function get_title() {
		return esc_html__( 'Tabs', 'elementor' );
	}

	public function get_icon() {
		return 'eicon-tabs';
	}

	public function get_keywords() {
		return [ 'nested', 'tabs', 'accordion', 'toggle' ];
	}

	public function get_style_depends(): array {
		return [ 'widget-nested-tabs' ];
	}

	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	public function show_in_panel(): bool {
		return Plugin::$instance->experiments->is_feature_active( 'nested-elements', true );
	}

	protected function tab_content_container( int $index ) {
		return [
			'elType' => 'container',
			'settings' => [
				'_title' => sprintf(
					/* translators: %d: Tab index. */
					__( 'Tab #%d', 'elementor' ),
					$index
				),
				'content_width' => 'full',
			],
		];
	}

	protected function get_default_children_elements() {
		return [
			$this->tab_content_container( 1 ),
			$this->tab_content_container( 2 ),
			$this->tab_content_container( 3 ),
		];
	}

	protected function get_default_repeater_title_setting_key() {
		return 'tab_title';
	}

	protected function get_default_children_title() {
		/* translators: %d: Tab index. */
		return esc_html__( 'Tab #%d', 'elementor' );
	}

	protected function get_default_children_placeholder_selector() {
		return '.e-n-tabs-content';
	}

	protected function get_html_wrapper_class() {
		return 'elementor-widget-n-tabs';
	}

	protected function register_controls() {
		if ( null === $this->optimized_markup ) {
			$this->optimized_markup = Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' ) && ! $this->has_widget_inner_wrapper();
			$this->widget_container_selector = $this->optimized_markup ? '' : ' > .elementor-widget-container';
		}

		$start = is_rtl() ? 'right' : 'left';
		$end = is_rtl() ? 'left' : 'right';
		$start_logical = is_rtl() ? 'end' : 'start';
		$end_logical = is_rtl() ? 'start' : 'end';
		$heading_selector_non_touch_device = "{{WRAPPER}}.elementor-widget-n-tabs{$this->widget_container_selector} > .e-n-tabs[data-touch-mode='false'] > .e-n-tabs-heading";
		$heading_selector_touch_device = "{{WRAPPER}}.elementor-widget-n-tabs{$this->widget_container_selector} > .e-n-tabs[data-touch-mode='true'] > .e-n-tabs-heading";
		$heading_selector = "{{WRAPPER}}.elementor-widget-n-tabs{$this->widget_container_selector} > .e-n-tabs > .e-n-tabs-heading";
		$content_selector = ":where( {{WRAPPER}}.elementor-widget-n-tabs{$this->widget_container_selector} > .e-n-tabs > .e-n-tabs-content ) > .e-con";

		$this->start_controls_section( 'section_tabs', [
			'label' => esc_html__( 'Tabs', 'elementor' ),
		] );

		$repeater = new Repeater();

		$repeater->add_control( 'tab_title', [
			'label' => esc_html__( 'Title', 'elementor' ),
			'type' => Controls_Manager::TEXT,
			'default' => esc_html__( 'Tab Title', 'elementor' ),
			'placeholder' => esc_html__( 'Tab Title', 'elementor' ),
			'label_block' => true,
			'dynamic' => [
				'active' => true,
			],
		] );

		$repeater->add_control(
			'tab_icon',
			[
				'label' => esc_html__( 'Icon', 'elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$repeater->add_control(
			'tab_icon_active',
			[
				'label' => esc_html__( 'Active Icon', 'elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'skin' => 'inline',
				'label_block' => false,
				'condition' => [
					'tab_icon[value]!' => '',
				],
			]
		);

		$repeater->add_control(
			'element_id',
			[
				'label' => esc_html__( 'CSS ID', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'ai' => [
					'active' => false,
				],
				'dynamic' => [
					'active' => true,
				],
				'title' => esc_html__( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'elementor' ),
				'style_transfer' => false,
				'classes' => 'elementor-control-direction-ltr',
			]
		);

		$this->add_control( 'tabs', [
			'label' => esc_html__( 'Tabs Items', 'elementor' ),
			'type' => Control_Nested_Repeater::CONTROL_TYPE,
			'fields' => $repeater->get_controls(),
			'default' => [
				[
					'tab_title' => esc_html__( 'Tab #1', 'elementor' ),
				],
				[
					'tab_title' => esc_html__( 'Tab #2', 'elementor' ),
				],
				[
					'tab_title' => esc_html__( 'Tab #3', 'elementor' ),
				],
			],
			'title_field' => '{{{ tab_title }}}',
			'button_text' => esc_html__( 'Add Tab', 'elementor' ),
		] );

		$styling_block_start = '--n-tabs-direction: column; --n-tabs-heading-direction: row; --n-tabs-heading-width: initial; --n-tabs-title-flex-basis: content; --n-tabs-title-flex-shrink: 0;';
		$styling_block_end = '--n-tabs-direction: column-reverse; --n-tabs-heading-direction: row; --n-tabs-heading-width: initial; --n-tabs-title-flex-basis: content; --n-tabs-title-flex-shrink: 0';
		$styling_inline_end = '--n-tabs-direction: row-reverse; --n-tabs-heading-direction: column; --n-tabs-heading-width: 240px; --n-tabs-title-flex-basis: initial; --n-tabs-title-flex-shrink: initial;';
		$styling_inline_start = '--n-tabs-direction: row; --n-tabs-heading-direction: column; --n-tabs-heading-width: 240px; --n-tabs-title-flex-basis: initial; --n-tabs-title-flex-shrink: initial;';

		$this->add_responsive_control( 'tabs_direction', [
			'label' => esc_html__( 'Direction', 'elementor' ),
			'type' => Controls_Manager::CHOOSE,
			'options' => [
				'block-start' => [
					'title' => esc_html__( 'Above', 'elementor' ),
					'icon' => 'eicon-v-align-top',
				],
				'block-end' => [
					'title' => esc_html__( 'Below', 'elementor' ),
					'icon' => 'eicon-v-align-bottom',
				],
				'inline-end' => [
					'title' => esc_html__( 'After', 'elementor' ),
					'icon' => 'eicon-h-align-' . $end,
				],
				'inline-start' => [
					'title' => esc_html__( 'Before', 'elementor' ),
					'icon' => 'eicon-h-align-' . $start,
				],
			],
			'separator' => 'before',
			'selectors_dictionary' => [
				'block-start' => $styling_block_start,
				'block-end' => $styling_block_end,
				'inline-end' => $styling_inline_end,
				'inline-start' => $styling_inline_start,
				// Styling duplication for BC reasons.
				'top' => $styling_block_start,
				'bottom' => $styling_block_end,
				'end' => $styling_inline_end,
				'start' => $styling_inline_start,
			],
			'selectors' => [
				'{{WRAPPER}}' => '{{VALUE}}',
			],
			'control_type' => 'content',
		] );

		$this->add_responsive_control( 'tabs_justify_horizontal', [
			'label' => esc_html__( 'Justify', 'elementor' ),
			'type' => Controls_Manager::CHOOSE,
			'options' => [
				'start' => [
					'title' => esc_html__( 'Start', 'elementor' ),
					'icon' => "eicon-align-$start_logical-h",
				],
				'center' => [
					'title' => esc_html__( 'Center', 'elementor' ),
					'icon' => 'eicon-align-center-h',
				],
				'end' => [
					'title' => esc_html__( 'End', 'elementor' ),
					'icon' => "eicon-align-$end_logical-h",
				],
				'stretch' => [
					'title' => esc_html__( 'Stretch', 'elementor' ),
					'icon' => 'eicon-align-stretch-h',
				],
			],
			'selectors_dictionary' => [
				'start' => '--n-tabs-heading-justify-content: flex-start; --n-tabs-title-width: initial; --n-tabs-title-height: initial; --n-tabs-title-align-items: center; --n-tabs-title-flex-grow: 0;',
				'center' => '--n-tabs-heading-justify-content: center; --n-tabs-title-width: initial; --n-tabs-title-height: initial; --n-tabs-title-align-items: center; --n-tabs-title-flex-grow: 0;',
				'end' => '--n-tabs-heading-justify-content: flex-end; --n-tabs-title-width: initial; --n-tabs-title-height: initial; --n-tabs-title-align-items: center; --n-tabs-title-flex-grow: 0;',
				'stretch' => '--n-tabs-heading-justify-content: initial; --n-tabs-title-width: 100%; --n-tabs-title-height: initial; --n-tabs-title-align-items: center; --n-tabs-title-flex-grow: 1;',
			],
			'selectors' => [
				'{{WRAPPER}}' => '{{VALUE}}',
			],
			'condition' => [
				'tabs_direction' => [
					'',
					'block-start',
					'block-end',
					'top',
					'bottom',
				],
			],
			'frontend_available' => true,
		] );

		$this->add_responsive_control( 'tabs_justify_vertical', [
			'label' => esc_html__( 'Justify', 'elementor' ),
			'type' => Controls_Manager::CHOOSE,
			'options' => [
				'start' => [
					'title' => esc_html__( 'Start', 'elementor' ),
					'icon' => 'eicon-align-start-v',
				],
				'center' => [
					'title' => esc_html__( 'Center', 'elementor' ),
					'icon' => 'eicon-align-center-v',
				],
				'end' => [
					'title' => esc_html__( 'End', 'elementor' ),
					'icon' => 'eicon-align-end-v',
				],
				'stretch' => [
					'title' => esc_html__( 'Stretch', 'elementor' ),
					'icon' => 'eicon-align-stretch-v',
				],
			],
			'selectors_dictionary' => [
				'start' => '--n-tabs-heading-justify-content: flex-start; --n-tabs-title-width: initial; --n-tabs-title-height: initial; --n-tabs-title-align-items: initial; --n-tabs-heading-wrap: wrap; --n-tabs-title-flex-basis: content',
				'center' => '--n-tabs-heading-justify-content: center; --n-tabs-title-width: initial; --n-tabs-title-height: initial; --n-tabs-title-align-items: initial; --n-tabs-heading-wrap: wrap; --n-tabs-title-flex-basis: content',
				'end' => '--n-tabs-heading-justify-content: flex-end; --n-tabs-title-width: initial; --n-tabs-title-height: initial; --n-tabs-title-align-items: initial; --n-tabs-heading-wrap: wrap; --n-tabs-title-flex-basis: content',
				'stretch' => '--n-tabs-heading-justify-content: flex-start; --n-tabs-title-width: initial; --n-tabs-title-height: 100%; --n-tabs-title-align-items: center; --n-tabs-heading-wrap: nowrap; --n-tabs-title-flex-basis: auto',
			],
			'selectors' => [
				'{{WRAPPER}}' => '{{VALUE}}',
			],
			'condition' => [
				'tabs_direction' => [
					'inline-start',
					'inline-end',
					'start',
					'end',
				],
			],
		] );

		$this->add_responsive_control( 'tabs_width', [
			'label' => esc_html__( 'Width', 'elementor' ),
			'type' => Controls_Manager::SLIDER,
			'range' => [
				'%' => [
					'min' => 10,
					'max' => 50,
				],
				'px' => [
					'min' => 20,
					'max' => 600,
				],
			],
			'default' => [
				'unit' => '%',
			],
			'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
			'selectors' => [
				'{{WRAPPER}}' => '--n-tabs-heading-width: {{SIZE}}{{UNIT}}',
			],
			'condition' => [
				'tabs_direction' => [
					'inline-start',
					'inline-end',
					'start',
					'end',
				],
			],
		] );

		$this->add_responsive_control( 'title_alignment', [
			'label' => esc_html__( 'Align Title', 'elementor' ),
			'type' => Controls_Manager::CHOOSE,
			'options' => [
				'start' => [
					'title' => esc_html__( 'Start', 'elementor' ),
					'icon' => 'eicon-text-align-left',
				],
				'center' => [
					'title' => esc_html__( 'Center', 'elementor' ),
					'icon' => 'eicon-text-align-center',
				],
				'end' => [
					'title' => esc_html__( 'End', 'elementor' ),
					'icon' => 'eicon-text-align-right',
				],
			],
			'selectors_dictionary' => [
				'start' => '--n-tabs-title-justify-content: flex-start; --n-tabs-title-align-items: flex-start; --n-tabs-title-text-align: start;',
				'center' => '--n-tabs-title-justify-content: center; --n-tabs-title-align-items: center; --n-tabs-title-text-align: center;',
				'end' => '--n-tabs-title-justify-content: flex-end; --n-tabs-title-align-items: flex-end; --n-tabs-title-text-align: end;',
			],
			'selectors' => [
				'{{WRAPPER}}' => '{{VALUE}}',
			],
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_tabs_responsive', [
			'label' => esc_html__( 'Additional Settings', 'elementor' ),
		] );

		$this->add_responsive_control(
			'horizontal_scroll',
			[
				'label' => esc_html__( 'Horizontal Scroll', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'description' => esc_html__( 'Note: Scroll tabs if they don’t fit into their parent container.', 'elementor' ),
				'options' => [
					'disable' => esc_html__( 'Disable', 'elementor' ),
					'enable' => esc_html__( 'Enable', 'elementor' ),
				],
				'default' => 'disable',
				'selectors_dictionary' => [
					'disable' => '--n-tabs-heading-wrap: wrap; --n-tabs-heading-overflow-x: initial; --n-tabs-title-white-space: initial;',
					'enable' => '--n-tabs-heading-wrap: nowrap; --n-tabs-heading-overflow-x: scroll; --n-tabs-title-white-space: nowrap;',
				],
				'selectors' => [
					'{{WRAPPER}}' => '{{VALUE}}',
				],
				'frontend_available' => true,
				'condition' => [
					'tabs_direction' => [
						'',
						'block-start',
						'block-end',
						'top',
						'bottom',
					],
				],
			]
		);

		$dropdown_options = [
			'none' => esc_html__( 'None', 'elementor' ),
		];
		$excluded_breakpoints = [
			'laptop',
			'tablet_extra',
			'widescreen',
		];

		foreach ( Plugin::$instance->breakpoints->get_active_breakpoints() as $breakpoint_key => $breakpoint_instance ) {
			// Exclude the larger breakpoints from the dropdown selector.
			if ( in_array( $breakpoint_key, $excluded_breakpoints, true ) ) {
				continue;
			}

			$dropdown_options[ $breakpoint_key ] = sprintf(
				/* translators: 1: Breakpoint label, 2: `>` character, 3: Breakpoint value. */
				esc_html__( '%1$s (%2$s %3$dpx)', 'elementor' ),
				$breakpoint_instance->get_label(),
				'>',
				$breakpoint_instance->get_value()
			);
		}

		$this->add_control(
			'breakpoint_selector',
			[
				'label' => esc_html__( 'Breakpoint', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'description' => esc_html__( 'Note: Choose at which breakpoint tabs will automatically switch to a vertical (“accordion”) layout.', 'elementor' ),
				'options' => $dropdown_options,
				'default' => 'mobile',
				'prefix_class' => 'e-n-tabs-',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_tabs_style', [
			'label' => esc_html__( 'Tabs', 'elementor' ),
			'tab' => Controls_Manager::TAB_STYLE,
		] );

		$this->add_responsive_control( 'tabs_title_space_between', [
			'label' => esc_html__( 'Gap between tabs', 'elementor' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em', 'rem', 'custom' ],
			'range' => [
				'px' => [
					'max' => 400,
				],
				'em' => [
					'max' => 40,
				],
				'rem' => [
					'max' => 40,
				],
			],
			'selectors' => [
				'{{WRAPPER}}' => '--n-tabs-title-gap: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->add_responsive_control( 'tabs_title_spacing', [
			'label' => esc_html__( 'Distance from content', 'elementor' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em', 'rem', 'custom' ],
			'range' => [
				'px' => [
					'max' => 400,
				],
				'em' => [
					'max' => 40,
				],
				'rem' => [
					'max' => 40,
				],
			],
			'selectors' => [
				'{{WRAPPER}}' => '--n-tabs-gap: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->start_controls_tabs( 'tabs_title_style' );

		$this->start_controls_tab(
			'tabs_title_normal',
			[
				'label' => esc_html__( 'Normal', 'elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'tabs_title_background_color',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => "{{WRAPPER}}{$this->widget_container_selector} > .e-n-tabs > .e-n-tabs-heading > .e-n-tab-title[aria-selected='false']:not( :hover )",
				'fields_options' => [
					'color' => [
						'label' => esc_html__( 'Background Color', 'elementor' ),
						'selectors' => [
							'{{SELECTOR}}' => 'background: {{VALUE}}',
						],
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tabs_title_border',
				'selector' => "{$heading_selector} > .e-n-tab-title[aria-selected=\"false\"]:not( :hover )",
				'fields_options' => [
					'color' => [
						'label' => esc_html__( 'Border Color', 'elementor' ),
					],
					'width' => [
						'label' => esc_html__( 'Border Width', 'elementor' ),
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tabs_title_box_shadow',
				'separator' => 'after',
				'selector' => "{$heading_selector} > .e-n-tab-title[aria-selected=\"false\"]:not( :hover )",
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_title_hover',
			[
				'label' => esc_html__( 'Hover', 'elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'tabs_title_background_color_hover',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => "{$heading_selector_non_touch_device} > .e-n-tab-title[aria-selected=\"false\"]:hover",
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color' => [
						'global' => [
							'default' => Global_Colors::COLOR_ACCENT,
						],
						'label' => esc_html__( 'Background Color', 'elementor' ),
						'selectors' => [
							'{{SELECTOR}}' => 'background: {{VALUE}};',
						],
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tabs_title_border_hover',
				'selector' => "{$heading_selector_non_touch_device} > .e-n-tab-title[aria-selected=\"false\"]:hover",
				'fields_options' => [
					'color' => [
						'label' => esc_html__( 'Border Color', 'elementor' ),
					],
					'width' => [
						'label' => esc_html__( 'Border Width', 'elementor' ),
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tabs_title_box_shadow_hover',
				'separator' => 'after',
				'selector' => "{$heading_selector_non_touch_device} > .e-n-tab-title[aria-selected=\"false\"]:hover",
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
			'tabs_title_transition_duration',
			[
				'label' => esc_html__( 'Transition Duration', 'elementor' ) . ' (s)',
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}}' => '--n-tabs-title-transition: {{SIZE}}s',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 3,
						'step' => 0.1,
					],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_title_active',
			[
				'label' => esc_html__( 'Active', 'elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'tabs_title_background_color_active',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => "{$heading_selector} > .e-n-tab-title[aria-selected=\"true\"], {$heading_selector_touch_device} > .e-n-tab-title[aria-selected=\"false\"]:hover",
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color' => [
						'global' => [
							'default' => Global_Colors::COLOR_ACCENT,
						],
						'label' => esc_html__( 'Background Color', 'elementor' ),
						'selectors' => [
							'{{SELECTOR}}' => 'background: {{VALUE}};',
						],
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tabs_title_border_active',
				'selector' => "{$heading_selector} > .e-n-tab-title[aria-selected=\"true\"], {$heading_selector_touch_device} > .e-n-tab-title[aria-selected=\"false\"]:hover",
				'fields_options' => [
					'color' => [
						'label' => esc_html__( 'Border Color', 'elementor' ),
					],
					'width' => [
						'label' => esc_html__( 'Border Width', 'elementor' ),
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tabs_title_box_shadow_active',
				'selector' => "{$heading_selector} > .e-n-tab-title[aria-selected=\"true\"], {$heading_selector_touch_device} > .e-n-tab-title[aria-selected=\"false\"]:hover",
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'tabs_title_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}}' => '--n-tabs-title-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'padding',
			[
				'label' => esc_html__( 'Padding', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}}' => '--n-tabs-title-padding-top: {{TOP}}{{UNIT}}; --n-tabs-title-padding-right: {{RIGHT}}{{UNIT}}; --n-tabs-title-padding-bottom: {{BOTTOM}}{{UNIT}}; --n-tabs-title-padding-left: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_title_style', [
			'label' => esc_html__( 'Titles', 'elementor' ),
			'tab' => Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name' => 'title_typography',
			'global' => [
				'default' => Global_Typography::TYPOGRAPHY_ACCENT,
			],
			'selector' => "{$heading_selector} > :is( .e-n-tab-title > .e-n-tab-title-text, .e-n-tab-title )",
			'fields_options' => [
				'font_size' => [
					'selectors' => [
						'{{WRAPPER}}' => '--n-tabs-title-font-size: {{SIZE}}{{UNIT}}',
					],
				],
			],
		] );

		$this->start_controls_tabs( 'title_style' );

		$this->start_controls_tab(
			'title_normal',
			[
				'label' => esc_html__( 'Normal', 'elementor' ),
			]
		);

		$this->add_control(
			'title_text_color',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--n-tabs-title-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow',
				'selector' => "{$heading_selector} > .e-n-tab-title[aria-selected=\"false\"]:not( :hover )",
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'title_text_stroke',
				'selector' => "{$heading_selector} > .e-n-tab-title[aria-selected=\"false\"]:not( :hover ) :is( span, a, i )",
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'title_hover',
			[
				'label' => esc_html__( 'Hover', 'elementor' ),
			]
		);

		$this->add_control(
			'title_text_color_hover',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} [data-touch-mode="false"] .e-n-tab-title[aria-selected="false"]:hover' => '--n-tabs-title-color-hover: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow_hover',
				'selector' => "{$heading_selector_non_touch_device} > .e-n-tab-title[aria-selected=\"false\"]:hover",
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'title_text_stroke_hover',
				'selector' => "{$heading_selector_non_touch_device} > .e-n-tab-title[aria-selected=\"false\"]:hover :is( span, a, i )",
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'title_active',
			[
				'label' => esc_html__( 'Active', 'elementor' ),
			]
		);

		$this->add_control(
			'title_text_color_active',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--n-tabs-title-color-active: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow_active',
				'selector' => "{$heading_selector} > .e-n-tab-title[aria-selected=\"true\"], {$heading_selector_touch_device} > .e-n-tab-title[aria-selected=\"false\"]:hover",
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'title_text_stroke_active',
				'selector' => "{$heading_selector} > .e-n-tab-title[aria-selected=\"true\"] :is( span, a, i ), {$heading_selector_touch_device} > .e-n-tab-title[aria-selected=\"false\"]:hover :is( span, a, i )",
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section( 'icon_section_style', [
			'label' => esc_html__( 'Icon', 'elementor' ),
			'tab' => Controls_Manager::TAB_STYLE,
		] );

		$styling_block_start = '--n-tabs-title-direction: column; --n-tabs-icon-order: initial; --n-tabs-title-justify-content-toggle: center; --n-tabs-title-align-items-toggle: initial;';
		$styling_block_end = '--n-tabs-title-direction: column; --n-tabs-icon-order: 1; --n-tabs-title-justify-content-toggle: center; --n-tabs-title-align-items-toggle: initial;';
		$styling_inline_start = '--n-tabs-title-direction: row; --n-tabs-icon-order: initial; --n-tabs-title-justify-content-toggle: initial; --n-tabs-title-align-items-toggle: center;';
		$styling_inline_end = '--n-tabs-title-direction: row; --n-tabs-icon-order: 1; --n-tabs-title-justify-content-toggle: initial; --n-tabs-title-align-items-toggle: center;';

		$this->add_responsive_control( 'icon_position', [
			'label' => esc_html__( 'Position', 'elementor' ),
			'type' => Controls_Manager::CHOOSE,
			'options' => [
				'block-start' => [
					'title' => esc_html__( 'Above', 'elementor' ),
					'icon' => 'eicon-v-align-top',
				],
				'inline-end' => [
					'title' => esc_html__( 'After', 'elementor' ),
					'icon' => 'eicon-h-align-' . $end,
				],
				'block-end' => [
					'title' => esc_html__( 'Below', 'elementor' ),
					'icon' => 'eicon-v-align-bottom',
				],
				'inline-start' => [
					'title' => esc_html__( 'Before', 'elementor' ),
					'icon' => 'eicon-h-align-' . $start,
				],
			],
			'selectors_dictionary' => [
				// The toggle variables for 'align items' and 'justify content' have been added to separate the styling of the two 'flex direction' modes.
				'block-start' => $styling_block_start,
				'inline-end' => $styling_inline_end,
				'block-end' => $styling_block_end,
				'inline-start' => $styling_inline_start,
				// Styling duplication for BC reasons.
				'top' => $styling_block_start,
				'bottom' => $styling_block_end,
				'start' => $styling_inline_start,
				'end' => $styling_inline_end,
			],
			'selectors' => [
				'{{WRAPPER}}' => '{{VALUE}}',
			],
		] );

		$this->add_responsive_control( 'icon_size', [
			'label' => esc_html__( 'Size', 'elementor' ),
			'type' => Controls_Manager::SLIDER,
			'range' => [
				'px' => [
					'max' => 100,
				],
				'em' => [
					'max' => 10,
				],
				'rem' => [
					'max' => 10,
				],
			],
			'size_units' => [ 'px', 'em', 'rem', 'vw', 'custom' ],
			'selectors' => [
				'{{WRAPPER}}' => '--n-tabs-icon-size: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->add_responsive_control( 'icon_spacing', [
			'label' => esc_html__( 'Spacing', 'elementor' ),
			'type' => Controls_Manager::SLIDER,
			'range' => [
				'px' => [
					'max' => 400,
				],
				'vw' => [
					'max' => 50,
					'step' => 0.1,
				],
			],
			'size_units' => [ 'px', 'em', 'rem', 'vw', 'custom' ],
			'selectors' => [
				'{{WRAPPER}}' => '--n-tabs-icon-gap: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->start_controls_tabs( 'icon_style_states' );

		$this->start_controls_tab(
			'icon_section_normal',
			[
				'label' => esc_html__( 'Normal', 'elementor' ),
			]
		);

		$this->add_control( 'icon_color', [
			'label' => esc_html__( 'Color', 'elementor' ),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}}' => '--n-tabs-icon-color: {{VALUE}};',
			],
		] );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_section_hover',
			[
				'label' => esc_html__( 'Hover', 'elementor' ),
			]
		);

		$this->add_control( 'icon_color_hover', [
			'label' => esc_html__( 'Color', 'elementor' ),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} [data-touch-mode="false"] .e-n-tab-title[aria-selected="false"]:hover' => '--n-tabs-icon-color-hover: {{VALUE}};',
			],
		] );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_section_active',
			[
				'label' => esc_html__( 'Active', 'elementor' ),
			]
		);

		$this->add_control( 'icon_color_active', [
			'label' => esc_html__( 'Color', 'elementor' ),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}}' => '--n-tabs-icon-color-active: {{VALUE}};',
			],
		] );

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section( 'section_box_style', [
			'label' => esc_html__( 'Content', 'elementor' ),
			'tab' => Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'box_background_color',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => $content_selector,
				'fields_options' => [
					'color' => [
						'label' => esc_html__( 'Background Color', 'elementor' ),
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'box_border',
				'selector' => $content_selector,
				'fields_options' => [
					'color' => [
						'label' => esc_html__( 'Border Color', 'elementor' ),
					],
					'width' => [
						'label' => esc_html__( 'Border Width', 'elementor' ),
					],
				],
			]
		);

		$this->add_responsive_control(
			'box_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					$content_selector => '--border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow_box_shadow',
				'selector' => $content_selector,
				'condition' => [
					'box_height!' => 'height',
				],
			]
		);

		$this->add_responsive_control(
			'box_padding',
			[
				'label' => esc_html__( 'Padding', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					$content_selector => '--padding-top: {{TOP}}{{UNIT}}; --padding-right: {{RIGHT}}{{UNIT}}; --padding-bottom: {{BOTTOM}}{{UNIT}}; --padding-left: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render_tab_titles_html( $item_settings ): void {
		$setting_key = $this->get_repeater_setting_key( 'tab_title', 'tabs', $item_settings['index'] );
		$title = $item_settings['item']['tab_title'];
		$css_classes = [ 'e-n-tab-title' ];

		if ( $item_settings['settings']['hover_animation'] ) {
			$css_classes[] = 'elementor-animation-' . $item_settings['settings']['hover_animation'];
		}

		$this->add_render_attribute( $setting_key, [
			'id' => $item_settings['tab_id'],
			'data-tab-title-id' => $item_settings['tab_title_id'],
			'class' => $css_classes,
			'aria-selected' => 1 === $item_settings['tab_count'] ? 'true' : 'false',
			'data-tab-index' => $item_settings['tab_count'],
			'role' => 'tab',
			'tabindex' => 1 === $item_settings['tab_count'] ? '0' : '-1',
			'aria-controls' => $item_settings['container_id'],
			'style' => '--n-tabs-title-order: ' . $item_settings['tab_count'] . ';',
		] );
		?>
		<button <?php $this->print_render_attribute_string( $setting_key ); ?>>
			<?php $this->maybe_render_tab_icons_html( $item_settings ); ?>
			<span <?php $this->print_render_attribute_string( 'tab-title-text' ); ?>>
				<?php echo wp_kses_post( $title ); ?>
			</span>
		</button>
		<?php
	}

	protected function maybe_render_tab_icons_html( $item_settings ): void {
		$icon_settings = $item_settings['item']['tab_icon'];

		if ( empty( $icon_settings['value'] ) ) {
			return;
		}

		$active_icon_settings = $this->is_active_icon_exist( $item_settings['item'] )
			? $item_settings['item']['tab_icon_active']
			: $icon_settings;
		?>
		<span <?php $this->print_render_attribute_string( 'tab-icon' ); ?>>
			<?php Icons_Manager::render_icon( $icon_settings, [ 'aria-hidden' => 'true' ] ); ?>
			<?php Icons_Manager::render_icon( $active_icon_settings, [ 'aria-hidden' => 'true' ] ); ?>
		</span>
		<?php
	}

	protected function render_tab_containers_html( $settings ): void {
		foreach ( $settings['tabs'] as $index => $item ) {
			$item_settings = $this->tab_item_settings[ $index ];
			$this->print_child( $item_settings['index'], $item_settings );
		}
	}


	/**
	 * Print the content area.
	 *
	 * @param int   $index
	 * @param array $item_settings
	 */
	public function print_child( $index, $item_settings = [] ) {
		$children = $this->get_children();
		$child_ids = [];

		foreach ( $children as $child ) {
			$child_ids[] = $child->get_id();
		}

		// Add data-tab-index attribute to the content area.
		$add_attribute_to_container = function ( $should_render, $container ) use ( $item_settings, $child_ids ) {
			if ( in_array( $container->get_id(), $child_ids ) ) {
				$this->add_attributes_to_container( $container, $item_settings );
			}

			return $should_render;
		};

		add_filter( 'elementor/frontend/container/should_render', $add_attribute_to_container, 10, 3 );
		if ( isset( $children[ $index ] ) ) {
			$children[ $index ]->print_element();
		}
		remove_filter( 'elementor/frontend/container/should_render', $add_attribute_to_container );
	}

	protected function add_attributes_to_container( $container, $item_settings ) {
		$container->add_render_attribute( '_wrapper', [
			'id' => $item_settings['container_id'],
			'role' => 'tabpanel',
			'aria-labelledby' => $item_settings['tab_id'],
			'data-tab-index' => $item_settings['tab_count'],
			'style' => '--n-tabs-title-order: ' . $item_settings['tab_count'] . ';',
			'class' => 0 === $item_settings['index'] ? 'e-active' : '',
		] );
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$widget_number = $this->get_id_int();

		if ( ! empty( $settings['link'] ) ) {
			$this->add_link_attributes( 'elementor-tabs', $settings['link'] );
		}

		$this->add_render_attribute( 'elementor-tabs', [
			'class' => 'e-n-tabs',
			'data-widget-number' => $widget_number,
			'aria-label' => esc_html__( 'Tabs. Open items with Enter or Space, close with Escape and navigate using the Arrow keys.', 'elementor' ),
		] );

		$this->add_render_attribute( 'tab-title-text', 'class', 'e-n-tab-title-text' );
		$this->add_render_attribute( 'tab-icon', 'class', 'e-n-tab-icon' );
		$this->add_render_attribute( 'tab-icon-active', 'class', [ 'e-n-tab-icon' ] );
		?>
		<div <?php $this->print_render_attribute_string( 'elementor-tabs' ); ?>>
			<div class="e-n-tabs-heading" role="tablist">
			<?php
			foreach ( $settings['tabs'] as $index => $item ) {
				$tab_count = $index + 1;
				$tab_title_id = 'e-n-tab-title-' . $widget_number . $tab_count;
				$tab_id = empty( $item['element_id'] )
					? $tab_title_id
					: $item['element_id'];

				$item_settings = [
					'index' => $index,
					'tab_count' => $tab_count,
					'tab_id' => $tab_id,
					'tab_title_id' => $tab_title_id,
					'container_id' => 'e-n-tab-content-' . $widget_number . $tab_count,
					'widget_number' => $widget_number,
					'item' => $item,
					'settings' => $settings,
				];

				$this->tab_item_settings[] = $item_settings;

				$this->render_tab_titles_html( $item_settings );
			}
			?>
			</div>
			<div class="e-n-tabs-content">
				<?php $this->render_tab_containers_html( $settings ); ?>
			</div>
		</div>
		<?php
	}

	protected function get_initial_config(): array {
		return array_merge( parent::get_initial_config(), [
			'support_improved_repeaters' => true,
			'target_container' => [ '.e-n-tabs-heading' ],
			'node' => 'button',
		] );
	}

	protected function content_template_single_repeater_item() {
		?>
		<#
		const tabIndex = view.collection.length,
			elementUid = view.getIDInt().toString(),
			item = data,
			hoverAnimationSetting = view?.container?.settings?.attributes?.hover_animation;
			hoverAnimationClass = hoverAnimationSetting
				? `elementor-animation-${ hoverAnimationSetting }`
				: '';
		#>
		<?php $this->content_template_single_item( '{{ tabIndex }}', '{{ item }}', '{{ elementUid }}', '{{ hoverAnimationClass }}' );
	}

	protected function content_template() {
		?>
		<# const elementUid = view.getIDInt().toString(); #>
		<div class="e-n-tabs" data-widget-number="{{ elementUid }}" aria-label="<?php echo esc_html__( 'Tabs. Open items with Enter or Space, close with Escape and navigate using the Arrow keys.', 'elementor' ); ?>">
			<# if ( settings['tabs'] ) { #>
			<div class="e-n-tabs-heading" role="tablist">
				<# _.each( settings['tabs'], function( item, index ) {
					const tabIndex = index,
						hoverAnimationSetting = settings['hover_animation'],
						hoverAnimationClass = hoverAnimationSetting
							? `elementor-animation-${ hoverAnimationSetting }`
							: '';
				#>
				<?php $this->content_template_single_item( '{{ tabIndex }}', '{{ item }}', '{{ elementUid }}', '{{ hoverAnimationClass }}' ); ?>
				<# } ); #>
			</div>
			<div class="e-n-tabs-content"></div>
			<# } #>
		</div>
		<?php
	}

	private function content_template_single_item( $tab_index, $item, $element_uid, $hover_animation_class ) {
		?>
		<#
		const tabCount = tabIndex + 1,
			tabTitleId = 'e-n-tab-title-' + elementUid + tabCount,
			tabId = item.element_id
				? item.element_id
				: tabTitleId,
			tabUid = elementUid + tabCount,
			tabIcon = elementor.helpers.renderIcon( view, item.tab_icon, { 'aria-hidden': true }, 'i' , 'object' ),
			activeTabIcon = item.tab_icon_active.value
				? elementor.helpers.renderIcon( view, item.tab_icon_active, { 'aria-hidden': true }, 'i' , 'object' )
				: tabIcon,
			escapedHoverAnimationClass = _.escape( hoverAnimationClass );

		view.addRenderAttribute( 'tab-title', {
			'id': tabId,
			'data-tab-title-id': tabTitleId,
			'class': [ 'e-n-tab-title',escapedHoverAnimationClass ],
			'data-tab-index': tabCount,
			'role': 'tab',
			'aria-selected': 1 === tabCount ? 'true' : 'false',
			'tabindex': 1 === tabCount ? '0' : '-1',
			'aria-controls': 'e-n-tab-content-' + tabUid,
			'style': '--n-tabs-title-order: ' + tabCount + ';',
		}, null, true );

		view.addRenderAttribute( 'tab-title-text', {
			'class': [ 'e-n-tab-title-text' ],
			'data-binding-type': 'repeater-item',
			'data-binding-repeater-name': 'tabs',
			'data-binding-setting': [ 'tab_title', 'element_id' ],
			'data-binding-index': tabCount,
			'data-binding-config': JSON.stringify({
				'element_id': {
					attr: 'id',
					selector: 'button',
					editType: 'attribute',
				},
				'tab_title': {
					editType: 'text',
				},
			}),
		}, null, true );

		view.addRenderAttribute( 'tab-icon', {
			'class': [ 'e-n-tab-icon' ],
			'data-binding-type': 'repeater-item',
			'data-binding-repeater-name': 'tabs',
			'data-binding-index': tabCount,
		}, null, true );
		#>

		<button {{{ view.getRenderAttributeString( 'tab-title' ) }}}>
			<# if ( !! item.tab_icon.value ) { #>
			<span {{{ view.getRenderAttributeString( 'tab-icon' ) }}}>{{{ tabIcon.value }}}{{{ activeTabIcon.value }}}</span>
			<# } #>

			<span {{{ view.getRenderAttributeString( 'tab-title-text' ) }}}>{{{ item.tab_title }}}</span>
		</button>
		<?php
	}

	/**
	 * @param $item
	 * @return bool
	 */
	private function is_active_icon_exist( $item ) {
		return array_key_exists( 'tab_icon_active', $item ) && ! empty( $item['tab_icon_active'] ) && ! empty( $item['tab_icon_active']['value'] );
	}
}
