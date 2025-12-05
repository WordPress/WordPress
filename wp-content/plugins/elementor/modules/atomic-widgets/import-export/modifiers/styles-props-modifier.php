<?php

namespace Elementor\Modules\AtomicWidgets\ImportExport\Modifiers;

use Elementor\Modules\AtomicWidgets\PropsResolver\Import_Export_Props_Resolver;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Styles_Props_Modifier {
	private Import_Export_Props_Resolver $props_resolver;

	private array $schema;

	public function __construct( Import_Export_Props_Resolver $props_resolver, array $schema ) {
		$this->props_resolver = $props_resolver;
		$this->schema = $schema;
	}

	public static function make( Import_Export_Props_Resolver $props_resolver, array $schema ) {
		return new self( $props_resolver, $schema );
	}

	public function run( array $element ) {
		if ( empty( $element['styles'] ) && ! is_array( $element['styles'] ) ) {
			return $element;
		}

		foreach ( $element['styles'] as $style_key => $style ) {
			if ( empty( $style['variants'] ) || ! is_array( $style['variants'] ) ) {
				continue;
			}

			foreach ( $style['variants'] as $variant_key => $variant ) {
				if ( empty( $variant['props'] ) || ! is_array( $variant['props'] ) ) {
					continue;
				}

				$element['styles'][ $style_key ]['variants'][ $variant_key ]['props'] = $this->props_resolver->resolve(
					$this->schema,
					$variant['props']
				);
			}
		}

		return $element;
	}
}
