<?php

namespace Elementor\Modules\AtomicWidgets\ImportExport\Modifiers;

use Elementor\Modules\AtomicWidgets\PropsResolver\Import_Export_Props_Resolver;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Settings_Props_Modifier {
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
		if ( empty( $element['settings'] ) || ! is_array( $element['settings'] ) ) {
			return $element;
		}

		$element['settings'] = $this->props_resolver->resolve(
			$this->schema,
			$element['settings']
		);

		return $element;
	}
}
