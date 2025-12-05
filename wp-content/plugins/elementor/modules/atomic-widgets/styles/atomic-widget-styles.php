<?php

namespace Elementor\Modules\AtomicWidgets\Styles;

use Elementor\Core\Base\Document;
use Elementor\Modules\AtomicWidgets\Cache_Validity;
use Elementor\Modules\AtomicWidgets\Utils;
use Elementor\Modules\GlobalClasses\Utils\Atomic_Elements_Utils;
use Elementor\Plugin;

class Atomic_Widget_Styles {
	const STYLES_KEY = 'local';

	public function register_hooks() {
		add_action( 'elementor/atomic-widgets/styles/register', function( Atomic_Styles_Manager $styles_manager, array $post_ids ) {
			$this->register_styles( $styles_manager, $post_ids );
		}, 30, 2 );

		add_action( 'elementor/document/after_save', fn( Document $document ) => $this->invalidate_cache(
			[ $document->get_main_post()->ID ]
		), 20, 2 );

		add_action(
			'elementor/core/files/clear_cache',
			fn() => $this->invalidate_cache(),
		);
	}

	private function register_styles( Atomic_Styles_Manager $styles_manager, array $post_ids ) {
		foreach ( $post_ids as $post_id ) {
			$get_styles = fn() => $this->parse_post_styles( $post_id );

			$style_key = $this->get_style_key( $post_id );

			$styles_manager->register( $style_key, $get_styles, [ self::STYLES_KEY, $post_id ] );
		}
	}

	private function parse_post_styles( $post_id ) {
		$post_styles = [];

		Utils::traverse_post_elements( $post_id, function( $element_data ) use ( &$post_styles ) {
			$post_styles = array_merge( $post_styles, $this->parse_element_style( $element_data ) );
		} );

		return $post_styles;
	}

	private function parse_element_style( array $element_data ) {
		$element_type = Atomic_Elements_Utils::get_element_type( $element_data );
		$element_instance = Atomic_Elements_Utils::get_element_instance( $element_type );

		if ( ! Utils::is_atomic( $element_instance ) ) {
			return [];
		}

		return $element_data['styles'] ?? [];
	}

	private function invalidate_cache( ?array $post_ids = null ) {
		$cache_validity = new Cache_Validity();

		if ( empty( $post_ids ) ) {
			$cache_validity->invalidate( [ self::STYLES_KEY ] );

			return;
		}

		foreach ( $post_ids as $post_id ) {
			$cache_validity->invalidate( [ self::STYLES_KEY, $post_id ] );
		}
	}

	private function get_style_key( $post_id ) {
		return self::STYLES_KEY . '-' . $post_id;
	}
}
