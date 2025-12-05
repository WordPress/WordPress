<?php

namespace Elementor\Core\Kits\Documents\Tabs;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Kit;
use Elementor\Core\Kits\Manager;
use Elementor\Plugin;
use Elementor\Settings;
use Elementor\Sub_Controls_Stack;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Tab_Base extends Sub_Controls_Stack {
	/**
	 * @var Kit
	 */
	protected $parent;

	abstract protected function register_tab_controls();

	public function get_group() {
		return 'settings';
	}

	public function get_icon() {
		return '';
	}

	public function get_help_url() {
		return '';
	}

	public function get_additional_tab_content() {
		return '';
	}

	public function register_controls() {
		$this->register_tab();

		$this->register_tab_controls();
	}

	public function on_save( $data ) {}

	/**
	 * Before Save
	 *
	 * Allows for modifying the kit data before it is saved to the database.
	 *
	 * @param array $data
	 * @return array
	 */
	public function before_save( array $data ) {
		return $data;
	}

	protected function register_tab() {
		Controls_Manager::add_tab( $this->get_id(), $this->get_title() );
	}

	protected function add_default_globals_notice() {
		// Get the current section config (array - section id and tab) to use for creating a unique control ID and name
		$current_section = $this->parent->get_current_section();

		/** @var Manager $module */
		$kits_manager = Plugin::$instance->kits_manager;

		if ( $kits_manager->is_custom_colors_enabled() || $kits_manager->is_custom_typography_enabled() ) {
			$this->add_control(
				$current_section['section'] . '_schemes_notice',
				[
					'name' => $current_section['section'] . '_schemes_notice',
					'type' => Controls_Manager::ALERT,
					'alert_type' => 'warning',
					'content' => sprintf(
						/* translators: 1: Link open tag, 2: Link close tag. */
						esc_html__( 'In order for Theme Style to affect all relevant Elementor elements, please disable Default Colors and Fonts from the %1$sSettings Page%2$s.', 'elementor' ),
						'<a href="' . Settings::get_settings_tab_url( 'general' ) . '" target="_blank">',
						'</a>'
					),
					'render_type' => 'ui',
				]
			);
		}
	}
}
