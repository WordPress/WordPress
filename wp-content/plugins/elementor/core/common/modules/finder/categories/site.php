<?php
namespace Elementor\Core\Common\Modules\Finder\Categories;

use Elementor\Core\Common\Modules\Finder\Base_Category;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Site Category
 *
 * Provides general site items.
 */
class Site extends Base_Category {

	/**
	 * Get title.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Site', 'elementor' );
	}

	public function get_id() {
		return 'site';
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
			'homepage' => [
				'title' => esc_html__( 'Homepage', 'elementor' ),
				'url' => home_url(),
				'icon' => 'home-heart',
				'keywords' => [ 'home', 'page' ],
			],
			'wordpress-dashboard' => [
				'title' => esc_html__( 'Dashboard', 'elementor' ),
				'icon' => 'dashboard',
				'url' => admin_url(),
				'keywords' => [ 'dashboard', 'wordpress' ],
			],
			'wordpress-menus' => [
				'title' => esc_html__( 'Menus', 'elementor' ),
				'icon' => 'wordpress',
				'url' => admin_url( 'nav-menus.php' ),
				'keywords' => [ 'menu', 'wordpress' ],
			],
			'wordpress-themes' => [
				'title' => esc_html__( 'Themes', 'elementor' ),
				'icon' => 'wordpress',
				'url' => admin_url( 'themes.php' ),
				'keywords' => [ 'themes', 'wordpress' ],
			],
			'wordpress-customizer' => [
				'title' => esc_html__( 'Customizer', 'elementor' ),
				'icon' => 'wordpress',
				'url' => admin_url( 'customize.php' ),
				'keywords' => [ 'customizer', 'wordpress' ],
			],
			'wordpress-plugins' => [
				'title' => esc_html__( 'Plugins', 'elementor' ),
				'icon' => 'wordpress',
				'url' => admin_url( 'plugins.php' ),
				'keywords' => [ 'plugins', 'wordpress' ],
			],
			'wordpress-users' => [
				'title' => esc_html__( 'Users', 'elementor' ),
				'icon' => 'wordpress',
				'url' => admin_url( 'users.php' ),
				'keywords' => [ 'users', 'profile', 'wordpress' ],
			],
		];
	}
}
