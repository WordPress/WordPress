<?php

namespace Elementor\Core\Common\Modules\Finder\Categories;

use Elementor\Core\Common\Modules\Finder\Base_Category;
use Elementor\Modules\ElementManager\Module as ElementManagerModule;
use Elementor\Settings as ElementorSettings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Settings Category
 *
 * Provides items related to Elementor's settings.
 */
class Settings extends Base_Category {

	/**
	 * Get title.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Settings', 'elementor' );
	}

	public function get_id() {
		return 'settings';
	}

	/**
	 * Get category items.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public function get_category_items( array $options = [] ) {
		return [
			'general-settings' => [
				'title' => esc_html__( 'General Settings', 'elementor' ),
				'url' => ElementorSettings::get_settings_tab_url( 'general' ),
				'keywords' => [ 'general', 'settings', 'elementor' ],
			],
			'integrations' => [
				'title' => esc_html__( 'Integrations', 'elementor' ),
				'url' => ElementorSettings::get_settings_tab_url( 'integrations' ),
				'keywords' => [ 'integrations', 'settings', 'elementor' ],
			],
			'advanced' => [
				'title' => esc_html__( 'Advanced', 'elementor' ),
				'url' => ElementorSettings::get_settings_tab_url( 'advanced' ),
				'keywords' => [ 'advanced', 'settings', 'elementor' ],
			],
			'performance' => [
				'title' => esc_html__( 'Performance', 'elementor' ),
				'url' => ElementorSettings::get_settings_tab_url( 'performance' ),
				'keywords' => [ 'performance', 'settings', 'elementor' ],
			],
			'experiments' => [
				'title' => esc_html__( 'Experiments', 'elementor' ),
				'url' => ElementorSettings::get_settings_tab_url( 'experiments' ),
				'keywords' => [ 'settings', 'elementor', 'experiments' ],
			],
			'features' => [
				'title' => esc_html__( 'Features', 'elementor' ),
				'url' => ElementorSettings::get_settings_tab_url( 'experiments' ),
				'keywords' => [ 'settings', 'elementor', 'features' ],
			],
			'element-manager' => [
				'title' => esc_html__( 'Element Manager', 'elementor' ),
				'url' => admin_url( 'admin.php?page=' . ElementManagerModule::PAGE_ID ),
				'keywords' => [ 'settings', 'elements', 'widgets', 'manager' ],
			],
		];
	}
}
