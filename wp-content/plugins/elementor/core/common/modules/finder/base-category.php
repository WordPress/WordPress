<?php

namespace Elementor\Core\Common\Modules\Finder;

use Elementor\Core\Base\Base_Object;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Base Category
 *
 * Base class for Elementor Finder categories.
 */
abstract class Base_Category extends Base_Object {

	/**
	 * Get title.
	 *
	 * @since 2.3.0
	 * @abstract
	 * @access public
	 *
	 * @return string
	 */
	abstract public function get_title();

	/**
	 * Get a unique category ID.
	 *
	 * TODO: Make abstract.
	 *
	 * @since 3.5.0
	 * @deprecated 3.5.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_id() {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function(
			get_class( $this ) . '::' . __FUNCTION__,
			'3.5.0',
			'This method will be replaced with an abstract method.'
		);

		return '';
	}

	/**
	 * Get category items.
	 *
	 * @since 2.3.0
	 * @abstract
	 * @access public
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	abstract public function get_category_items( array $options = [] );

	/**
	 * Is dynamic.
	 *
	 * Determine if the category is dynamic.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @return bool
	 */
	public function is_dynamic() {
		return false;
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
		$settings = [
			'title' => $this->get_title(),
			'dynamic' => $this->is_dynamic(),
		];

		if ( ! $settings['dynamic'] ) {
			$settings['items'] = $this->get_category_items();
		}

		return $settings;
	}
}
