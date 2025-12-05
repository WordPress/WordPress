<?php

namespace Elementor\Core\Admin\Menu;

use Elementor\Plugin;
use Elementor\TemplateLibrary\Source_Local;
use Elementor\Tools;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Main extends Base {

	protected function get_init_args() {
		return [
			'page_title' => esc_html__( 'Elementor', 'elementor' ),
			'menu_title' => esc_html__( 'Elementor', 'elementor' ),
			'capability' => 'manage_options',
			'menu_slug' => 'elementor',
			'function' => [ Plugin::$instance->settings, 'display_settings_page' ],
			'position' => 58.5,
		];
	}

	protected function get_init_options() {
		return [
			'separator' => true,
		];
	}

	protected function register_default_submenus() {
		$this->add_submenu( [
			'page_title' => esc_html_x( 'Templates', 'Template Library', 'elementor' ),
			'menu_title' => esc_html_x( 'Templates', 'Template Library', 'elementor' ),
			'menu_slug' => Source_Local::ADMIN_MENU_SLUG,
			'index' => 0,
		] );

		$this->add_submenu( [
			'menu_title' => esc_html__( 'Help', 'elementor' ),
			'menu_slug' => 'go_knowledge_base_site',
			'function' => [ Plugin::$instance->settings, 'handle_external_redirects' ],
			'index' => 150,
		] );
	}

	protected function register() {
		parent::register();

		$this->rearrange_elementor_submenu();
	}

	private function rearrange_elementor_submenu() {
		global $submenu;

		$elementor_menu_slug = $this->get_args( 'menu_slug' );

		$elementor_submenu_old_index = null;

		$tools_submenu_index = null;

		foreach ( $submenu[ $elementor_menu_slug ] as $index => $submenu_item ) {
			if ( $elementor_menu_slug === $submenu_item[2] ) {
				$elementor_submenu_old_index = $index;
			} elseif ( Tools::PAGE_ID === $submenu_item[2] ) {
				$tools_submenu_index = $index;

				break;
			}
		}

		$elementor_submenu = array_splice( $submenu[ $elementor_menu_slug ], $elementor_submenu_old_index, 1 );

		$elementor_submenu[0][0] = esc_html__( 'Settings', 'elementor' );

		array_splice( $submenu[ $elementor_menu_slug ], $tools_submenu_index, 0, $elementor_submenu );
	}
}
