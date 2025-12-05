<?php
namespace Elementor\Core\Kits\Documents\Tabs;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Controls\Repeater as Global_Style_Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Global_Typography extends Tab_Base {

	const TYPOGRAPHY_PRIMARY = 'globals/typography?id=primary';
	const TYPOGRAPHY_SECONDARY = 'globals/typography?id=secondary';
	const TYPOGRAPHY_TEXT = 'globals/typography?id=text';
	const TYPOGRAPHY_ACCENT = 'globals/typography?id=accent';

	const TYPOGRAPHY_NAME = 'typography';
	const TYPOGRAPHY_GROUP_PREFIX = self::TYPOGRAPHY_NAME . '_';

	public function get_id() {
		return 'global-typography';
	}

	public function get_title() {
		return esc_html__( 'Global Fonts', 'elementor' );
	}

	public function get_group() {
		return 'global';
	}

	public function get_icon() {
		return 'eicon-t-letter';
	}

	public function get_help_url() {
		return 'https://go.elementor.com/global-fonts/';
	}

	protected function register_tab_controls() {
		$this->start_controls_section(
			'section_text_style',
			[
				'label' => esc_html__( 'Global Fonts', 'elementor' ),
				'tab' => $this->get_id(),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			[
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'required' => true,
			]
		);

		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => self::TYPOGRAPHY_NAME,
				'label' => '',
				'global' => [
					'active' => false,
				],
				'fields_options' => [
					'font_family' => [
						'selectors' => [
							'{{SELECTOR}}' => '--e-global-typography-{{external._id.VALUE}}-font-family: "{{VALUE}}"',
						],
					],
					'font_size' => [
						'selectors' => [
							'{{SELECTOR}}' => '--e-global-typography-{{external._id.VALUE}}-font-size: {{SIZE}}{{UNIT}}',
						],
					],
					'font_weight' => [
						'selectors' => [
							'{{SELECTOR}}' => '--e-global-typography-{{external._id.VALUE}}-font-weight: {{VALUE}}',
						],
					],
					'text_transform' => [
						'selectors' => [
							'{{SELECTOR}}' => '--e-global-typography-{{external._id.VALUE}}-text-transform: {{VALUE}}',
						],
					],
					'font_style' => [
						'selectors' => [
							'{{SELECTOR}}' => '--e-global-typography-{{external._id.VALUE}}-font-style: {{VALUE}}',
						],
					],
					'text_decoration' => [
						'selectors' => [
							'{{SELECTOR}}' => '--e-global-typography-{{external._id.VALUE}}-text-decoration: {{VALUE}}',
						],
					],
					'line_height' => [
						'selectors' => [
							'{{SELECTOR}}' => '--e-global-typography-{{external._id.VALUE}}-line-height: {{SIZE}}{{UNIT}}',
						],
					],
					'letter_spacing' => [
						'selectors' => [
							'{{SELECTOR}}' => '--e-global-typography-{{external._id.VALUE}}-letter-spacing: {{SIZE}}{{UNIT}}',
						],
					],
					'word_spacing' => [
						'selectors' => [
							'{{SELECTOR}}' => '--e-global-typography-{{external._id.VALUE}}-word-spacing: {{SIZE}}{{UNIT}}',
						],
					],
				],
			]
		);

		$typography_key = self::TYPOGRAPHY_GROUP_PREFIX . 'typography';
		$font_family_key = self::TYPOGRAPHY_GROUP_PREFIX . 'font_family';
		$font_weight_key = self::TYPOGRAPHY_GROUP_PREFIX . 'font_weight';

		$default_typography = [
			[
				'_id' => 'primary',
				'title' => esc_html__( 'Primary', 'elementor' ),
				$typography_key => 'custom',
				$font_family_key => 'Roboto',
				$font_weight_key => '600',
			],
			[
				'_id' => 'secondary',
				'title' => esc_html__( 'Secondary', 'elementor' ),
				$typography_key => 'custom',
				$font_family_key => 'Roboto Slab',
				$font_weight_key => '400',
			],
			[
				'_id' => 'text',
				'title' => esc_html__( 'Text', 'elementor' ),
				$typography_key => 'custom',
				$font_family_key => 'Roboto',
				$font_weight_key => '400',
			],
			[
				'_id' => 'accent',
				'title' => esc_html__( 'Accent', 'elementor' ),
				$typography_key => 'custom',
				$font_family_key => 'Roboto',
				$font_weight_key => '500',
			],
		];

		$this->add_control(
			'heading_system_typography',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'System Fonts', 'elementor' ),
			]
		);

		$this->add_control(
			'system_typography',
			[
				'type' => Global_Style_Repeater::CONTROL_TYPE,
				'fields' => $repeater->get_controls(),
				'default' => $default_typography,
				'item_actions' => [
					'add' => false,
					'remove' => false,
				],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'heading_custom_typography',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Custom Fonts', 'elementor' ),
			]
		);

		$this->add_control(
			'custom_typography',
			[
				'type' => Global_Style_Repeater::CONTROL_TYPE,
				'fields' => $repeater->get_controls(),
			]
		);

		$this->add_control(
			'default_generic_fonts',
			[
				'label' => esc_html__( 'Fallback Font Family', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'Sans-serif',
				'description' => esc_html__( 'The list of fonts used if the chosen font is not available.', 'elementor' ),
				'label_block' => true,
				'separator' => 'before',
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->end_controls_section();
	}
}
