<?php

namespace Elementor\Modules\Components\Styles;

use Elementor\Core\Base\Document;
use Elementor\Core\Utils\Collection;
use Elementor\Modules\AtomicWidgets\Cache_Validity;
use Elementor\Modules\AtomicWidgets\Utils;

/**
 * Component styles fetching for render
 */
class Component_Styles {
	const CACHE_ROOT_KEY = 'component-styles-related-posts';

	public function register_hooks() {
		add_action( 'elementor/post/render', fn( $post_id ) => $this->render_post( $post_id ) );

		add_action( 'elementor/document/after_save', fn( Document $document ) => $this->invalidate_cache(
			[ $document->get_main_post()->ID ]
		), 20, 2 );

		add_action(
			'elementor/core/files/clear_cache',
			fn() => $this->invalidate_cache(),
		);
	}

	private function render_post( string $post_id ) {
		$cache_validity = new Cache_Validity();

		if ( $cache_validity->is_valid( [ self::CACHE_ROOT_KEY, $post_id ] ) ) {
			$component_ids = $cache_validity->get_meta( [ self::CACHE_ROOT_KEY, $post_id ] );

			$this->declare_components_rendered( $component_ids );

			return;
		}

		$components = $this->get_components_from_post( $post_id );
		$component_ids = Collection::make( $components )
			->filter( fn( $component ) => isset( $component['settings']['component_id']['value'] ) )
			->map( fn( $component ) => $component['settings']['component_id']['value'] )
			->unique()
			->all();

		$cache_validity->validate( [ self::CACHE_ROOT_KEY, $post_id ], $component_ids );

		$this->declare_components_rendered( $component_ids );
	}

	private function declare_components_rendered( array $post_ids ) {
		foreach ( $post_ids as $post_id ) {
			do_action( 'elementor/post/render', $post_id );
		}
	}

	private function get_components_from_post( string $post_id ): array {
		$components = [];

		Utils::traverse_post_elements( $post_id, function( $element_data ) use ( &$components ) {
			if ( isset( $element_data['widgetType'] ) && 'e-component' === $element_data['widgetType'] ) {
				$components[] = $element_data;
			}
		} );

		return $components;
	}

	private function invalidate_cache( ?array $post_ids = null ) {
		$cache_validity = new Cache_Validity();

		if ( empty( $post_ids ) ) {
			$cache_validity->invalidate( [ self::CACHE_ROOT_KEY ] );

			return;
		}

		foreach ( $post_ids as $post_id ) {
			$cache_validity->invalidate( [ self::CACHE_ROOT_KEY, $post_id ] );
		}
	}
}
