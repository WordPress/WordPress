<?php

namespace Elementor\Modules\Components;

use Elementor\Modules\Components\Documents\Component as Component_Document;
use Elementor\Plugin;
use Elementor\Modules\Components\Components_REST_API;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Components_Repository {

	public static function make(): Components_Repository {
		return new self();
	}

	public function all() {
		// Components count is limited to 50, if we increase this number, we need to iterate the posts in batches.
		$posts = get_posts( [
			'post_type' => Component_Document::TYPE,
			'post_status' => 'publish',
			'posts_per_page' => Components_REST_API::MAX_COMPONENTS,
		] );

		$components = [];

		foreach ( $posts as $post ) {
			$doc = Plugin::$instance->documents->get( $post->ID );

			if ( ! $doc ) {
				continue;
			}

			$components[] = [
				'id' => $doc->get_main_id(),
				'name' => $doc->get_post()->post_title,
				'styles' => $this->extract_styles( $doc->get_elements_data() ),
			];
		}

		return Components::make( $components );
	}

	public function create( string $name, array $content ) {
		$document = Plugin::$instance->documents->create(
			Component_Document::get_type(),
			[
				'post_title' => $name,
				'post_status' => 'publish',
			]
		);

		$saved = $document->save( [
			'elements' => $content,
		] );

		if ( ! $saved ) {
			throw new \Exception( 'Failed to create component' );
		}

		return $document->get_main_id();
	}
	private function extract_styles( array $elements, array $styles = [] ) {
		foreach ( $elements as $element ) {
			if ( isset( $element['styles'] ) ) {
				$styles = array_merge( $styles, $element['styles'] );
			}

			if ( isset( $element['elements'] ) ) {
				$styles = $this->extract_styles( $element['elements'], $styles );
			}
		}

		return $styles;
	}
}
