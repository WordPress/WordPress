<?php
namespace Elementor\Modules\FloatingButtons\Base;

use Elementor\Modules\FloatingButtons\Classes\Render\Floating_Bars_Core_Render;
use Elementor\Core\Base\Providers\Social_Network_Provider;

use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;

use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Widget_Base;

abstract class Widget_Floating_Bars_Base extends Widget_Base {

	const TAB_ADVANCED = 'advanced-tab-floating-bars';

	public function get_style_depends(): array {
		$widget_name = $this->get_name();

		$style_depends = Plugin::$instance->experiments->is_feature_active( 'e_font_icon_svg' )
			? parent::get_style_depends()
			: [ 'elementor-icons-fa-solid', 'elementor-icons-fa-brands', 'elementor-icons-fa-regular' ];

		$style_depends[] = 'widget-floating-bars-base';

		$style_depends[] = "widget-{$widget_name}";

		return $style_depends;
	}

	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	public function get_icon(): string {
		return 'eicon-banner';
	}

	public function show_in_panel() {
		return false;
	}

	public function hide_on_search() {
		return true;
	}

	protected function get_initial_config(): array {
		return array_merge( parent::get_initial_config(), [
			'commonMerged' => true,
		] );
	}

	public static function get_configuration() {
		return [
			'content' => [
				'announcement_section' => [
					'icon_default' => [
						'value' => 'fas fa-tshirt',
						'library' => 'fa-solid',
					],
					'text_label' => esc_html__( 'Text', 'elementor' ),
					'text_default' => esc_html__( 'Just in! Cool summer tees', 'elementor' ),
				],
				'floating_bar_section' => [
					'close_switch_default' => 'yes',
					'has_pause_switch' => false,
					'accessible_name_default' => esc_html__( 'Banner', 'elementor' ),
				],
			],
			'style' => [
				'floating_bar_section' => [
					'has_close_bg' => false,
					'close_position_selectors' => [
						'{{WRAPPER}} .e-floating-bars__close-button' => 'inset-inline-{{VALUE}}: 10px;',
					],
					'has_close_position_control' => true,
					'background_selector' => '{{WRAPPER}} .e-floating-bars',
					'align_elements_selector' => [
						'{{WRAPPER}} .e-floating-bars' => 'justify-content: {{VALUE}};',
						'{{WRAPPER}} .e-floating-bars__cta-button-container' => 'justify-content: {{VALUE}};',
						'{{WRAPPER}} .e-floating-bars__announcement-text' => 'text-align: {{VALUE}};',
					],
				],
			],
			'advanced' => [],
		];
	}

	protected function register_controls(): void {
		$this->add_content_tab();

		$this->add_style_tab();

		$this->add_advanced_tab();
	}

	protected function add_announcement_content_section(): void {
		$config = static::get_configuration();

		$this->start_controls_section(
			'announcement_content_section',
			[
				'label' => __( 'Announcement', 'elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'announcement_icon',
			[
				'label' => esc_html__( 'Icon', 'elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => $config['content']['announcement_section']['icon_default'],
				'skin' => 'inline',
				'label_block' => false,
				'icon_exclude_inline_options' => [],
			]
		);

		$this->add_control(
			'announcement_text',
			[
				'label' => $config['content']['announcement_section']['text_label'],
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'Enter your text here', 'elementor' ),
				'default' => $config['content']['announcement_section']['text_default'],
			]
		);

		$this->end_controls_section();
	}

	protected function add_cta_button_content_section(): void {
		$this->start_controls_section(
			'cta_button_content_section',
			[
				'label' => __( 'CTA Button', 'elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'cta_text',
			[
				'label' => esc_html__( 'Text', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'Enter text', 'elementor' ),
				'default' => esc_html__( 'Shop now', 'elementor' ),
			],
		);

		$this->add_control(
			'cta_link',
			[
				'label' => esc_html__( 'Link', 'elementor' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'Paste URL or type', 'elementor' ),
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => false,
				],
			]
		);

		$this->add_control(
			'cta_icon',
			[
				'label' => esc_html__( 'Icon', 'elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'skin' => 'inline',
				'label_block' => false,
				'icon_exclude_inline_options' => [],
			]
		);

		$this->end_controls_section();
	}

	protected function add_accessible_name_control(): void {
		$config = static::get_configuration();

		$this->add_control(
			'accessible_name',
			[
				'label' => esc_html__( 'Accessible Name', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'Enter text', 'elementor' ),
				'default' => $config['content']['floating_bar_section']['accessible_name_default'],
				'condition' => [
					'floating_bar_close_switch' => 'yes',
				],
			],
		);
	}

	protected function add_floating_bar_content_section(): void {
		$config = static::get_configuration();

		$this->start_controls_section(
			'floating_bar_content_section',
			[
				'label' => __( 'Floating Bar', 'elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		if ( $config['content']['floating_bar_section']['has_pause_switch'] ) {
			$this->add_control(
				'floating_bar_pause_switch',
				[
					'label' => esc_html__( 'Pause and Play', 'elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'elementor' ),
					'label_off' => esc_html__( 'Hide', 'elementor' ),
					'return_value' => 'yes',
					'default' => 'no',
				]
			);

			$this->add_control(
				'floating_bar_pause_icon',
				[
					'label' => esc_html__( 'Pause Icon', 'elementor' ),
					'type' => Controls_Manager::ICONS,
					'fa4compatibility' => 'icon',
					'default' => [
						'value' => 'fas fa-pause',
						'library' => 'fa-solid',
					],
					'skin' => 'inline',
					'label_block' => false,
					'exclude_inline_options' => [ 'none' ],
					'recommended' => [
						'fa-regular' => [
							'pause-circle',
						],
						'fa-solid' => [
							'pause-circle',
						],
					],
					'condition' => [
						'floating_bar_pause_switch' => 'yes',
					],
				],
			);

			$this->add_control(
				'floating_bar_play_icon',
				[
					'label' => esc_html__( 'Play Icon', 'elementor' ),
					'type' => Controls_Manager::ICONS,
					'fa4compatibility' => 'icon',
					'default' => [
						'value' => 'fas fa-play',
						'library' => 'fa-solid',
					],
					'skin' => 'inline',
					'label_block' => false,
					'exclude_inline_options' => [ 'none' ],
					'recommended' => [
						'fa-regular' => [
							'play-circle',
						],
						'fa-solid' => [
							'play-circle',
						],
					],
					'condition' => [
						'floating_bar_pause_switch' => 'yes',
					],
				],
			);
		}

		$this->add_control(
			'floating_bar_close_switch',
			[
				'label' => esc_html__( 'Close Button', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementor' ),
				'label_off' => esc_html__( 'Hide', 'elementor' ),
				'return_value' => 'yes',
				'default' => $config['content']['floating_bar_section']['close_switch_default'],
			]
		);

		$this->add_accessible_name_control();

		$this->end_controls_section();
	}

	protected function add_headlines_content_section(): void {
		$config = static::get_configuration();

		$this->start_controls_section(
			'headlines_content',
			[
				'label' => esc_html__( 'Headlines', 'elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'headlines_icon',
			[
				'label' => esc_html__( 'Icon', 'elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'skin' => 'inline',
				'label_block' => false,
				'icon_exclude_inline_options' => [],
			]
		);

		$repeater->add_control(
			'headlines_text',
			[
				'label' => esc_html__( 'Text', 'elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Enter your text', 'elementor' ),
				'default' => esc_html__( 'Item Title', 'elementor' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'headlines_url',
			[
				'label' => esc_html__( 'Link', 'elementor' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'Paste URL or type', 'elementor' ),
				'dynamic' => [
					'active' => true,
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'headlines_repeater',
			[
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ headlines_text }}}',
				'prevent_empty' => true,
				'button_text' => esc_html__( 'Add Item', 'elementor' ),
				'default' => [
					[
						'headlines_text' => esc_html__( 'Item #1', 'elementor' ),
					],
					[
						'headlines_text' => esc_html__( 'Item #2', 'elementor' ),
					],
					[
						'headlines_text' => esc_html__( 'Item #3', 'elementor' ),
					],
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_announcement_style_section(): void {
		$config = static::get_configuration();

		$this->start_controls_section(
			'style_announcement',
			[
				'label' => esc_html__( 'Announcement', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'style_announcement_icon_heading',
			[
				'label' => esc_html__( 'Icon', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'announcement_icon[value]',
							'operator' => '!==',
							'value' => '',
						],
						[
							'name' => 'announcement_icon[value]',
							'operator' => '!==',
							'value' => null,
						],
					],
				],
			]
		);

		$this->add_control(
			'style_announcement_icon_color',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars' => '--e-floating-bars-announcement-icon-color: {{VALUE}}',
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'announcement_icon[value]',
							'operator' => '!==',
							'value' => '',
						],
						[
							'name' => 'announcement_icon[value]',
							'operator' => '!==',
							'value' => null,
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'style_announcement_icon_position',
			[
				'label' => esc_html__( 'Position', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start' => [
						'title' => esc_html__( 'Left', 'elementor' ),
						'icon' => 'eicon-h-align-left',
					],
					'end' => [
						'title' => esc_html__( 'Right', 'elementor' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars__announcement-icon' => 'order: {{VALUE}};',
				],
				'selectors_dictionary' => [
					'start' => '-1',
					'end' => '2',
				],
				'default' => 'start',
				'toggle' => false,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'announcement_icon[value]',
							'operator' => '!==',
							'value' => '',
						],
						[
							'name' => 'announcement_icon[value]',
							'operator' => '!==',
							'value' => null,
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'style_announcement_icon_size',
			[
				'label' => esc_html__( 'Size', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
				],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars' => '--e-floating-bars-announcement-icon-size: {{SIZE}}{{UNIT}}',
				],
				'separator' => 'after',
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'announcement_icon[value]',
							'operator' => '!==',
							'value' => '',
						],
						[
							'name' => 'announcement_icon[value]',
							'operator' => '!==',
							'value' => null,
						],
					],
				],
			]
		);

		$this->add_control(
			'style_announcement_text_heading',
			[
				'label' => $config['content']['announcement_section']['text_label'],
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'style_announcement_text_typography',
				'selector' => '{{WRAPPER}} .e-floating-bars__announcement-text',
			]
		);

		$this->add_control(
			'style_announcement_text_color',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars' => '--e-floating-bars-announcement-text-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_cta_button_style_section(): void {
		$this->start_controls_section(
			'style_cta_button',
			[
				'label' => esc_html__( 'CTA Button', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'style_cta_type',
			[
				'label' => esc_html__( 'Type', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'button',
				'options' => [
					'button' => esc_html__( 'Button', 'elementor' ),
					'link' => esc_html__( 'Link', 'elementor' ),
				],
			]
		);

		$this->add_responsive_control(
			'style_cta_icon_position',
			[
				'label' => esc_html__( 'Icon Position', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => is_rtl() ? 'row-reverse' : 'row',
				'toggle' => false,
				'options' => [
					'row' => [
						'title' => esc_html__( 'Start', 'elementor' ),
						'icon' => 'eicon-h-align-left',
					],
					'row-reverse' => [
						'title' => esc_html__( 'End', 'elementor' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors_dictionary' => [
					'left' => is_rtl() ? 'row-reverse' : 'row',
					'right' => is_rtl() ? 'row' : 'row-reverse',
				],
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars__cta-button' => 'flex-direction: {{VALUE}};',
				],
				'condition' => [
					'cta_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'style_cta_icon_spacing',
			[
				'label' => esc_html__( 'Icon Spacing', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 50,
					],
					'em' => [
						'max' => 5,
					],
					'rem' => [
						'max' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars' => '--e-floating-bars-cta-button-icon-gap: {{SIZE}}{{UNIT}};',
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'cta_icon[value]',
							'operator' => '!==',
							'value' => '',
						],
						[
							'name' => 'cta_icon[value]',
							'operator' => '!==',
							'value' => null,
						],
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'style_cta_typography',
				'selector' => '{{WRAPPER}} .e-floating-bars__cta-button',
			]
		);

		$this->start_controls_tabs(
			'style_cta_button_tabs'
		);

		$this->start_controls_tab(
			'style_cta_button_tabs_normal',
			[
				'label' => esc_html__( 'Normal', 'elementor' ),
			]
		);

		$this->add_control(
			'style_cta_button_text_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars' => '--e-floating-bars-cta-button-text-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'style_cta_button_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars' => '--e-floating-bars-cta-button-bg-color: {{VALUE}}',
				],
				'condition' => [
					'style_cta_type' => 'button',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_cta_button_tabs_hover',
			[
				'label' => esc_html__( 'Hover', 'elementor' ),
			]
		);

		$this->add_control(
			'style_cta_button_text_color_hover',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars' => '--e-floating-bars-cta-button-text-color-hover: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'style_cta_button_bg_color_hover',
			[
				'label' => esc_html__( 'Background Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars' => '--e-floating-bars-cta-button-bg-color-hover: {{VALUE}}',
				],
				'condition' => [
					'style_cta_type' => 'button',
				],
			]
		);

		$this->add_control(
			'style_cta_button_border_color_hover',
			[
				'label' => esc_html__( 'Border Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars' => '--e-floating-bars-cta-button-border-color-hover: {{VALUE}}',
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'style_cta_button_show_border',
							'operator' => '===',
							'value' => 'yes',
						],
						[
							'name' => 'style_cta_type',
							'operator' => '===',
							'value' => 'button',
						],
					],
				],
			]
		);

		$this->add_control(
			'style_cta_button_hover_animation',
			[
				'label' => esc_html__( 'Hover Animation', 'elementor' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
				'frontend_available' => true,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'style_cta_button_show_border',
			[
				'label' => esc_html__( 'Border', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementor' ),
				'label_off' => esc_html__( 'No', 'elementor' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'separator' => 'before',
				'condition' => [
					'style_cta_type' => 'button',
				],
			]
		);

		$this->add_responsive_control(
			'style_cta_button_border_width',
			[
				'label' => esc_html__( 'Border Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'%' => [
						'min' => 10,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars' => '--e-floating-bars-cta-button-border-width: {{SIZE}}{{UNIT}}',
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'style_cta_button_show_border',
							'operator' => '===',
							'value' => 'yes',
						],
						[
							'name' => 'style_cta_type',
							'operator' => '===',
							'value' => 'button',
						],
					],
				],
			]
		);

		$this->add_control(
			'style_cta_button_border_color',
			[
				'label' => esc_html__( 'Border Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars' => '--e-floating-bars-cta-button-border-color: {{VALUE}}',
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'style_cta_button_show_border',
							'operator' => '===',
							'value' => 'yes',
						],
						[
							'name' => 'style_cta_type',
							'operator' => '===',
							'value' => 'button',
						],
					],
				],
			]
		);

		$this->add_control(
			'style_cta_button_corners',
			[
				'label' => esc_html__( 'Corners', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'round',
				'options' => [
					'round' => esc_html__( 'Round', 'elementor' ),
					'rounded' => esc_html__( 'Rounded', 'elementor' ),
					'sharp' => esc_html__( 'Sharp', 'elementor' ),
				],
				'condition' => [
					'style_cta_type' => 'button',
				],
			]
		);

		$this->add_responsive_control(
			'style_cta_button_padding',
			[
				'label' => esc_html__( 'Padding', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars' => '--e-floating-bars-cta-button-padding-block-end: {{BOTTOM}}{{UNIT}}; --e-floating-bars-cta-button-padding-block-start: {{TOP}}{{UNIT}}; --e-floating-bars-cta-button-padding-inline-end: {{RIGHT}}{{UNIT}}; --e-floating-bars-cta-button-padding-inline-start: {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
				'condition' => [
					'style_cta_type' => 'button',
				],
			]
		);

		$this->add_responsive_control(
			'style_cta_button_animation',
			[
				'label' => esc_html__( 'Entrance Animation', 'elementor' ),
				'type' => Controls_Manager::ANIMATION,
				'frontend_available' => true,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'style_cta_button_animation_duration',
			[
				'label' => esc_html__( 'Animation Duration', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '1000',
				'options' => [
					'2000' => esc_html__( 'Slow', 'elementor' ),
					'1000' => esc_html__( 'Normal', 'elementor' ),
					'800' => esc_html__( 'Fast', 'elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars' => '--e-floating-bars-cta-button-animation-duration: {{VALUE}}ms',
				],
				'prefix_class' => 'animated-',
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'style_cta_button_animation',
							'operator' => '!==',
							'value' => '',
						],
						[
							'name' => 'style_cta_button_animation',
							'operator' => '!==',
							'value' => 'none',
						],
					],
				],
			]
		);

		$this->add_control(
			'style_cta_button_animation_delay',
			[
				'label' => esc_html__( 'Animation Delay', 'elementor' ) . ' (ms)',
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'step' => 100,
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars' => '--e-floating-bars-cta-button-animation-delay: {{SIZE}}ms;',
				],
				'render_type' => 'none',
				'frontend_available' => true,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'style_cta_button_animation',
							'operator' => '!==',
							'value' => '',
						],
						[
							'name' => 'style_cta_button_animation',
							'operator' => '!==',
							'value' => 'none',
						],
					],
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_floating_bar_background_style_controls(): void {
		$config = static::get_configuration();

		$this->add_control(
			'floating_bar_background_heading',
			[
				'label' => esc_html__( 'Background', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'floating_bar_background_type',
				'types' => [ 'classic', 'gradient' ],
				'selector' => $config['style']['floating_bar_section']['background_selector'],
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'position' => [
						'default' => 'center center',
					],
					'size' => [
						'default' => 'cover',
					],
				],
			]
		);

		$this->add_control(
			'floating_bar_background_overlay_heading',
			[
				'label' => esc_html__( 'Background Overlay', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'floating_bar_background_overlay_type',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .e-floating-bars__overlay',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'position' => [
						'default' => 'center center',
					],
					'size' => [
						'default' => 'cover',
					],
				],
			]
		);

		$this->add_responsive_control(
			'floating_bar_background_overlay_opacity',
			[
				'label' => esc_html__( 'Opacity', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'%' => [
						'max' => 1,
						'min' => 0,
						'step' => 0.01,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 0.5,
				],
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars' => '--e-floating-bars-background-overlay-opacity: {{SIZE}};',
				],
			]
		);
	}

	protected function add_floating_bar_close_button_style_controls(): void {
		$config = static::get_configuration();

		$this->add_control(
			'floating_bar_close_button_heading',
			[
				'label' => esc_html__( 'Close Button', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'floating_bar_close_switch' => 'yes',
				],
			]
		);

		if ( $config['style']['floating_bar_section']['has_close_position_control'] ) {
			$this->add_responsive_control(
				'floating_bar_close_button_position',
				[
					'label' => esc_html__( 'Horizontal position', 'elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'start' => [
							'title' => esc_html__( 'Left', 'elementor' ),
							'icon' => 'eicon-h-align-left',
						],
						'end' => [
							'title' => esc_html__( 'Right', 'elementor' ),
							'icon' => 'eicon-h-align-right',
						],
					],
					'default' => 'end',
					'toggle' => false,
					'selectors' => $config['style']['floating_bar_section']['close_position_selectors'],
					'condition' => [
						'floating_bar_close_switch' => 'yes',
					],
				]
			);
		}

		$this->add_control(
			'floating_bar_close_button_color',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars' => '--e-floating-bars-close-button-color: {{VALUE}}',
				],
				'condition' => [
					'floating_bar_close_switch' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'style_floating_bar_close_button_size',
			[
				'label' => esc_html__( 'Size', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
				],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars' => '--e-floating-bars-close-icon-size: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'floating_bar_close_switch' => 'yes',
				],
			]
		);

		if ( $config['style']['floating_bar_section']['has_close_bg'] ) {
			$this->add_control(
				'floating_bar_close_bg_color',
				[
					'label' => esc_html__( 'Background Color', 'elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .e-floating-bars' => '--e-floating-bars-close-button-bg-color: {{VALUE}}',
					],
					'condition' => [
						'floating_bar_close_switch' => 'yes',
					],
					'separator' => 'after',
				]
			);
		}
	}

	protected function add_floating_bar_pause_style_controls(): void {
		$config = static::get_configuration();

		$this->add_control(
			'floating_bar_pause_button_heading',
			[
				'label' => esc_html__( 'Pause and Play', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'floating_bar_pause_switch' => 'yes',
				],
			]
		);

		$this->add_control(
			'floating_bar_pause_button_color',
			[
				'label' => esc_html__( 'Icon Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars' => '--e-floating-bars-pause-play-icon-color: {{VALUE}}',
				],
				'condition' => [
					'floating_bar_pause_switch' => 'yes',
				],
			]
		);

		$this->add_control(
			'floating_bar_pause_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars' => '--e-floating-bars-pause-play-bg-color: {{VALUE}}',
				],
				'condition' => [
					'floating_bar_pause_switch' => 'yes',
				],
			]
		);
	}

	protected function add_floating_bar_style_section(): void {
		$config = static::get_configuration();

		$this->start_controls_section(
			'style_floating_bar',
			[
				'label' => esc_html__( 'Floating Bar', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_floating_bar_close_button_style_controls();

		$this->add_responsive_control(
			'style_floating_bar_elements_align',
			[
				'label' => esc_html__( 'Align Elements', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'center',
				'options' => [
					'start' => [
						'title' => esc_html__( 'Start', 'elementor' ),
						'icon' => 'eicon-align-start-h',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementor' ),
						'icon' => 'eicon-align-center-h',
					],
					'end' => [
						'title' => esc_html__( 'End', 'elementor' ),
						'icon' => 'eicon-align-end-h',
					],
					'space-between' => [
						'title' => esc_html__( 'Stretch', 'elementor' ),
						'icon' => 'eicon-align-stretch-h',
					],
				],
				'selectors' => $config['style']['floating_bar_section']['align_elements_selector'],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'style_floating_bar_elements_spacing',
			[
				'label' => esc_html__( 'Element spacing', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars' => '--e-floating-bars-elements-gap: {{SIZE}}{{UNIT}}',
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'style_floating_bar_elements_align',
							'operator' => '!==',
							'value' => 'stretch',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'style_floating_bar_elements_padding',
			[
				'label' => esc_html__( 'Padding', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars' => '--e-floating-bars-elements-padding-block-end: {{BOTTOM}}{{UNIT}}; --e-floating-bars-elements-padding-block-start: {{TOP}}{{UNIT}}; --e-floating-bars-elements-padding-inline-end: {{RIGHT}}{{UNIT}}; --e-floating-bars-elements-padding-inline-start: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_floating_bar_background_style_controls();

		$this->end_controls_section();
	}

	protected function add_headlines_style_section(): void {
		$this->start_controls_section(
			'style_headlines',
			[
				'label' => esc_html__( 'Headline', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'style_headlines_icon_heading',
			[
				'label' => esc_html__( 'Icon', 'elementor' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'style_headlines_icon_color',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars' => '--e-floating-bars-headline-icon-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'style_headlines_icon_position',
			[
				'label' => esc_html__( 'Icon Position', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => is_rtl() ? 'row-reverse' : 'row',
				'toggle' => false,
				'options' => [
					'row' => [
						'title' => esc_html__( 'Start', 'elementor' ),
						'icon' => 'eicon-h-align-left',
					],
					'row-reverse' => [
						'title' => esc_html__( 'End', 'elementor' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors_dictionary' => [
					'row' => is_rtl() ? 'row-reverse' : 'row',
					'row-reverse' => is_rtl() ? 'row' : 'row-reverse',
				],
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars__headline' => '--e-floating-bars-headline-icon-position: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'style_headlines_icon_size',
			[
				'label' => esc_html__( 'Size', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
				],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars' => '--e-floating-bars-headline-icon-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'style_headlines_icon_spacing',
			[
				'label' => esc_html__( 'Icon Spacing', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 50,
					],
					'em' => [
						'max' => 5,
					],
					'rem' => [
						'max' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars' => '--e-floating-bars-headline-icon-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->add_control(
			'style_headline_text_heading',
			[
				'label' => esc_html__( 'Text', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'style_headline_text_typography',
				'selector' => '{{WRAPPER}} .e-floating-bars__headline-text',
			]
		);

		$this->start_controls_tabs(
			'style_headline_tabs'
		);

		$this->start_controls_tab(
			'style_headline_tabs_normal',
			[
				'label' => esc_html__( 'Normal', 'elementor' ),
			]
		);

		$this->add_control(
			'style_headline_text_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars' => '--e-floating-bars-headline-text-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_headline_tabs_hover',
			[
				'label' => esc_html__( 'Hover', 'elementor' ),
			]
		);

		$this->add_control(
			'style_headline_text_color_hover',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .e-floating-bars' => '--e-floating-bars-headline-text-color-hover: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function add_advanced_tab(): void {
		Controls_Manager::add_tab(
			static::TAB_ADVANCED,
			esc_html__( 'Advanced', 'elementor' )
		);

		$this->start_controls_section(
			'advanced_layout_section',
			[
				'label' => esc_html__( 'Layout', 'elementor' ),
				'tab' => static::TAB_ADVANCED,
			]
		);

		$this->add_control(
			'advanced_vertical_position',
			[
				'label' => esc_html__( 'Vertical Position', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => esc_html__( 'Top', 'elementor' ),
						'icon' => 'eicon-v-align-top',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'elementor' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'default' => 'top',
				'toggle' => false,
			]
		);

		$this->add_control(
			'advanced_toggle_sticky',
			[
				'label' => esc_html__( 'Sticky', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementor' ),
				'label_off' => esc_html__( 'No', 'elementor' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'advanced_responsive_section',
			[
				'label' => esc_html__( 'Responsive', 'elementor' ),
				'tab' => static::TAB_ADVANCED,
			]
		);

		$this->add_control(
			'responsive_description',
			[
				'raw' => __( 'Responsive visibility will take effect only on preview mode or live page, and not while editing in Elementor.', 'elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
			]
		);

		$this->add_hidden_device_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'advanced_custom_controls_section',
			[
				'label' => esc_html__( 'CSS', 'elementor' ),
				'tab' => static::TAB_ADVANCED,
			]
		);

		$this->add_control(
			'advanced_custom_css_id',
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
			]
		);

		$this->add_control(
			'advanced_custom_css_classes',
			[
				'label' => esc_html__( 'CSS Classes', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'ai' => [
					'active' => false,
				],
				'dynamic' => [
					'active' => true,
				],
				'title' => esc_html__( 'Add your custom class WITHOUT the dot. e.g: my-class', 'elementor' ),
			]
		);

		$this->end_controls_section();

		Plugin::$instance->controls_manager->add_custom_css_controls( $this, static::TAB_ADVANCED );

		Plugin::$instance->controls_manager->add_custom_attributes_controls( $this, static::TAB_ADVANCED );
	}

	protected function add_content_tab(): void {
		$this->add_announcement_content_section();

		$this->add_cta_button_content_section();

		$this->add_floating_bar_content_section();
	}

	protected function add_style_tab(): void {
		$this->add_announcement_style_section();

		$this->add_cta_button_style_section();

		$this->add_floating_bar_style_section();
	}

	protected function render(): void {
		$render_strategy = new Floating_Bars_Core_Render( $this );

		$render_strategy->render();
	}
}
