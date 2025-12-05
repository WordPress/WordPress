<?php
namespace Elementor\Core\Common\Modules\Finder\Categories;

use Elementor\Core\Common\Modules\Finder\Base_Category;
use Elementor\Core\RoleManager\Role_Manager;
use Elementor\Plugin;
use Elementor\TemplateLibrary\Source_Local;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * General Category
 *
 * Provides general items related to Elementor Admin.
 */
class General extends Base_Category {

	/**
	 * Get title.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'General', 'elementor' );
	}

	public function get_id() {
		return 'general';
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
			'saved-templates' => [
				'title' => esc_html__( 'Saved Templates', 'elementor' ),
				'icon' => 'library-save',
				'url' => Source_Local::get_admin_url(),
				'keywords' => [ 'template', 'section', 'page', 'library' ],
			],
			'system-info' => [
				'title' => esc_html__( 'System Info', 'elementor' ),
				'icon' => 'info-circle-o',
				'url' => admin_url( 'admin.php?page=elementor-system-info' ),
				'keywords' => [ 'system', 'info', 'environment', 'elementor' ],
			],
			'role-manager' => [
				'title' => esc_html__( 'Role Manager', 'elementor' ),
				'icon' => 'person',
				'url' => Role_Manager::get_url(),
				'keywords' => [ 'role', 'manager', 'user', 'elementor' ],
			],
			'knowledge-base' => [
				'title' => esc_html__( 'Knowledge Base', 'elementor' ),
				'url' => admin_url( 'admin.php?page=go_knowledge_base_site' ),
				'keywords' => [ 'help', 'knowledge', 'docs', 'elementor' ],
			],
			'theme-builder' => [
				'title' => esc_html__( 'Theme Builder', 'elementor' ),
				'icon' => 'library-save',
				'url' => Plugin::$instance->app->get_settings( 'menu_url' ),
				'keywords' => [ 'template', 'header', 'footer', 'single', 'archive', 'search', '404', 'library' ],
			],
			'kit-library' => [
				'title' => esc_html__( 'Website Templates', 'elementor' ),
				'icon' => 'kit-parts',
				'url' => Plugin::$instance->app->get_base_url() . '&source=finder#/kit-library',
				'keywords' => [ 'Website Templates', 'kit library', 'kit', 'library', 'site parts', 'parts', 'assets', 'templates' ],
			],
		];
	}
}
