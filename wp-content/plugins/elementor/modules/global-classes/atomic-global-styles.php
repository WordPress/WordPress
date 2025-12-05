<?php

namespace Elementor\Modules\GlobalClasses;

use Elementor\Plugin;
use Elementor\Modules\AtomicWidgets\Styles\Atomic_Styles_Manager;
use Elementor\Modules\AtomicWidgets\Cache_Validity;

class Atomic_Global_Styles {
	const STYLES_KEY = 'global';

	public function register_hooks() {
		add_action(
			'elementor/atomic-widgets/styles/register',
			fn( Atomic_Styles_Manager $styles_manager, array $post_ids ) => $this->register_styles( $styles_manager ),
			20,
			2
		);

		add_action( 'elementor/global_classes/update', fn( string $context ) => $this->invalidate_cache( $context ), 10, 1 );

		add_action(
			'deleted_post',
			fn( $post_id ) => $this->on_post_delete( $post_id )
		);

		add_action(
			'elementor/core/files/clear_cache',
			fn() => $this->invalidate_cache(),
		);

		add_filter('elementor/atomic-widgets/settings/transformers/classes',
			fn( $value ) => $this->transform_classes_names( $value )
		);
	}

	private function register_styles( Atomic_Styles_Manager $styles_manager ) {
		$context = is_preview() ? Global_Classes_Repository::CONTEXT_PREVIEW : Global_Classes_Repository::CONTEXT_FRONTEND;

		$get_styles = function () use ( $context ) {
			return Global_Classes_Repository::make()->context( $context )->all()->get_items()->map( function( $item ) {
				$item['id'] = $item['label'];
				return $item;
			})->all();
		};

		$styles_manager->register(
			self::STYLES_KEY . '-' . $context,
			$get_styles,
			[ self::STYLES_KEY, $context ]
		);
	}

	private function on_post_delete( $post_id ) {
		if ( ! Plugin::$instance->kits_manager->is_kit( $post_id ) ) {
			return;
		}

		$this->invalidate_cache();
	}

	private function invalidate_cache( ?string $context = null ) {
		$cache_validity = new Cache_Validity();

		if ( empty( $context ) || Global_Classes_Repository::CONTEXT_FRONTEND === $context ) {
			$cache_validity->invalidate( [ self::STYLES_KEY ] );

			return;
		}

		$cache_validity->invalidate( [ self::STYLES_KEY, $context ] );
	}

	private function transform_classes_names( $ids ) {
		$context = is_preview() ? Global_Classes_Repository::CONTEXT_PREVIEW : Global_Classes_Repository::CONTEXT_FRONTEND;

		$classes = Global_Classes_Repository::make()
			->context( $context )
			->all()
			->get_items();

		return array_map(
			function( $id ) use ( $classes ) {
				$class = $classes->get( $id );

				return $class ? $class['label'] : $id;
			},
			$ids
		);
	}
}
