<?php

namespace Elementor\Core\Common\Modules\Finder;

use Elementor\Core\Base\Module as BaseModule;
use Elementor\Core\Common\Modules\Ajax\Module as Ajax;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Finder Module
 *
 * Responsible for initializing Elementor Finder functionality
 */
class Module extends BaseModule {

	/**
	 * Categories manager.
	 *
	 * @access private
	 *
	 * @var Categories_Manager
	 */
	private $categories_manager;

	/**
	 * Module constructor.
	 *
	 * @since 2.3.0
	 * @access public
	 */
	public function __construct() {
		$this->categories_manager = new Categories_Manager();

		$this->add_template();

		add_action( 'elementor/ajax/register_actions', [ $this, 'register_ajax_actions' ] );
	}

	/**
	 * Get name.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_name() {
		return 'finder';
	}

	/**
	 * Add template.
	 *
	 * @since 2.3.0
	 * @access public
	 */
	public function add_template() {
		Plugin::$instance->common->add_template( __DIR__ . '/template.php' );
	}

	/**
	 * Register ajax actions.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @param Ajax $ajax
	 */
	public function register_ajax_actions( Ajax $ajax ) {
		$ajax->register_ajax_action( 'finder_get_category_items', [ $this, 'ajax_get_category_items' ] );
	}

	/**
	 * Ajax get category items.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @param array $data
	 *
	 * @return array
	 *
	 * @throws \Exception If finder category registration fails or validation errors occur.
	 */
	public function ajax_get_category_items( array $data ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			throw new \Exception( 'Access denied.' );
		}

		$category = $this->categories_manager->get_categories( $data['category'] );

		return $category->get_category_items( $data );
	}

	/**
	 * Get init settings.
	 *
	 * @since 2.3.0
	 * @access protected
	 *
	 * @return array
	 */
	protected function get_init_settings() {
		$categories = $this->categories_manager->get_categories();

		$categories_data = [];

		foreach ( $categories as $category_name => $category ) {
			$categories_data[ $category_name ] = array_merge( $category->get_settings(), [ 'name' => $category_name ] );
		}

		/**
		 * Finder categories.
		 *
		 * Filters the list of finder categories. This hook is used to manage Finder
		 * categories - to add new categories, remove and edit existing categories.
		 *
		 * @since 2.3.0
		 *
		 * @param array $categories_data A list of finder categories.
		 */
		$categories_data = apply_filters( 'elementor/finder/categories', $categories_data );

		return [
			'data' => $categories_data,
		];
	}
}
