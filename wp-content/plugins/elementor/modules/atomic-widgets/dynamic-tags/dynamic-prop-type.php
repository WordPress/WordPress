<?php

namespace Elementor\Modules\AtomicWidgets\DynamicTags;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Plain_Prop_Type;
use Elementor\Modules\AtomicWidgets\Parsers\Props_Parser;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Dynamic_Prop_Type extends Plain_Prop_Type {

	const META_KEY = 'dynamic';

	/**
	 * Return a tuple that lets the developer ignore the dynamic prop type in the props schema
	 * using `Prop_Type::add_meta()`, e.g. `String_Prop_Type::make()->add_meta( Dynamic_Prop_Type::ignore() )`.
	 */
	public static function ignore(): array {
		return [ static::META_KEY, false ];
	}

	public static function get_key(): string {
		return 'dynamic';
	}

	public function categories( array $categories ) {
		$this->settings['categories'] = $categories;

		return $this;
	}

	public function get_categories() {
		return $this->settings['categories'] ?? [];
	}

	protected function validate_value( $value ): bool {
		$is_valid_structure = (
			isset( $value['name'] ) &&
			is_string( $value['name'] ) &&
			isset( $value['settings'] ) &&
			is_array( $value['settings'] )
		);

		if ( ! $is_valid_structure ) {
			return false;
		}

		$tag = Dynamic_Tags_Module::instance()->registry->get_tag( $value['name'] );

		if ( ! $tag || ! $this->is_tag_in_supported_categories( $tag ) ) {
			return false;
		}

		return Props_Parser::make( $tag['props_schema'] )
			->validate( $value['settings'] )
			->is_valid();
	}

	protected function sanitize_value( $value ): array {
		$tag = Dynamic_Tags_Module::instance()->registry->get_tag( $value['name'] );

		$sanitized = Props_Parser::make( $tag['props_schema'] )
			->sanitize( $value['settings'] )
			->unwrap();

		return [
			'name' => $value['name'],
			'settings' => $sanitized,
		];
	}

	private function is_tag_in_supported_categories( array $tag ): bool {
		$intersection = array_intersect(
			$tag['categories'],
			$this->get_categories()
		);

		return ! empty( $intersection );
	}
}
