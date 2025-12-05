<?php
namespace Elementor\Modules\Favorites\Types;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Modules\Favorites\Favorites_Type;
use Elementor\Plugin;

class Widgets extends Favorites_Type {
	const CATEGORY_SLUG = 'favorites';

	/**
	 * Widgets favorites type constructor.
	 */
	public function __construct( array $items = [] ) {
		parent::__construct( $items );

		add_action( 'elementor/document/before_get_config', [ $this, 'update_widget_categories' ], 10, 1 );
	}

	public function get_name() {
		return 'widgets';
	}

	public function prepare( $favorites ) {
		return array_intersect( parent::prepare( $favorites ), $this->get_available() );
	}

	/**
	 * Get all available widgets.
	 *
	 * @return string[]
	 */
	public function get_available() {
		return array_merge(
			array_keys(
				Plugin::instance()->widgets_manager->get_widget_types()
			),
			array_keys(
				Plugin::instance()->elements_manager->get_element_types()
			)
		);
	}

	/**
	 * Update the categories of a widget inside a filter.
	 *
	 * @param $document
	 */
	public function update_widget_categories( $document ) {
		foreach ( $this->values() as $favorite ) {
			$widget = Plugin::$instance->widgets_manager->get_widget_types( $favorite );

			// If it's not a widget, maybe it's an element.
			if ( ! $widget ) {
				$widget = Plugin::$instance->elements_manager->get_element_types( $favorite );
			}

			if ( $widget ) {
				$widget->set_config( 'categories', [ static::CATEGORY_SLUG ] );
			}
		}
	}
}
