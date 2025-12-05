<?php
namespace Elementor\Core\Kits\Documents\Tabs;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Controls\Repeater as Global_Style_Repeater;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Global_Colors extends Tab_Base {

	const COLOR_PRIMARY = 'globals/colors?id=primary';
	const COLOR_SECONDARY = 'globals/colors?id=secondary';
	const COLOR_TEXT = 'globals/colors?id=text';
	const COLOR_ACCENT = 'globals/colors?id=accent';

	public function get_id() {
		return 'global-colors';
	}

	public function get_title() {
		return esc_html__( 'Global Colors', 'elementor' );
	}

	public function get_group() {
		return 'global';
	}

	public function get_icon() {
		return 'eicon-global-colors';
	}

	public function get_help_url() {
		return 'https://go.elementor.com/global-colors/';
	}

	protected function register_tab_controls() {
		$this->start_controls_section(
			'section_global_colors',
			[
				'label' => esc_html__( 'Global Colors', 'elementor' ),
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

		// Color Value
		$repeater->add_control(
			'color',
			[
				'type' => Controls_Manager::COLOR,
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}}' => '--e-global-color-{{_id.VALUE}}: {{VALUE}}',
				],
				'global' => [
					'active' => false,
				],
			]
		);

		$default_colors = [
			[
				'_id' => 'primary',
				'title' => esc_html__( 'Primary', 'elementor' ),
				'color' => '#6EC1E4',
			],
			[
				'_id' => 'secondary',
				'title' => esc_html__( 'Secondary', 'elementor' ),
				'color' => '#54595F',
			],
			[
				'_id' => 'text',
				'title' => esc_html__( 'Text', 'elementor' ),
				'color' => '#7A7A7A',
			],
			[
				'_id' => 'accent',
				'title' => esc_html__( 'Accent', 'elementor' ),
				'color' => '#61CE70',
			],
		];

		$this->add_control(
			'heading_system_colors',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'System Colors', 'elementor' ),
			]
		);

		$this->add_control(
			'system_colors',
			[
				'type' => Global_Style_Repeater::CONTROL_TYPE,
				'fields' => $repeater->get_controls(),
				'default' => $default_colors,
				'item_actions' => [
					'add' => false,
					'remove' => false,
				],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'heading_custom_colors',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Custom Colors', 'elementor' ),
			]
		);

		$this->add_control(
			'custom_colors',
			[
				'type' => Global_Style_Repeater::CONTROL_TYPE,
				'fields' => $repeater->get_controls(),
			]
		);

		$this->end_controls_section();
	}
}
