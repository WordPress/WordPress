<?php

namespace Elementor\Modules\AtomicWidgets\Styles;

use Elementor\Core\Utils\Collection;
use Elementor\Modules\AtomicWidgets\Cache_Validity;
use Elementor\Modules\AtomicWidgets\Utils;
use Elementor\Plugin;

class Atomic_Widget_Base_Styles {
	const STYLES_KEY = 'base';

	public function register_hooks() {
		add_action(
			'elementor/atomic-widgets/styles/register',
			fn( Atomic_Styles_Manager $styles_manager ) => $this->register_styles( $styles_manager ),
			10,
			1
		);

		add_action(
			'elementor/core/files/clear_cache',
			fn() => $this->invalidate_cache(),
		);
	}

	private function register_styles( Atomic_Styles_Manager $styles_manager ) {
		$styles_manager->register(
			self::STYLES_KEY,
			fn () => $this->get_all_base_styles(),
			[ self::STYLES_KEY ]
		);
	}

	private function invalidate_cache() {
		$cache_validity = new Cache_Validity();

		$cache_validity->invalidate( [ self::STYLES_KEY ] );
	}

	public function get_all_base_styles(): array {
		$elements = Plugin::$instance->elements_manager->get_element_types();
		$widgets = Plugin::$instance->widgets_manager->get_widget_types();

		return Collection::make( $elements )
		->merge( $widgets )
		->filter( fn( $element ) => Utils::is_atomic( $element ) )
		->map( fn( $element ) => $element->get_base_styles() )
		->flatten()
		->all();
	}
}
