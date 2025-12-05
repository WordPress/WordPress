<?php

namespace Elementor\Modules\Variables\Classes;

use Elementor\Modules\Variables\Storage\Repository as Variables_Repository;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class CSS_Renderer {
	private Variables_Repository $repository;

	public function __construct( Variables_Repository $repository ) {
		$this->repository = $repository;
	}

	private function global_variables(): array {
		return $this->repository->variables();
	}

	public function raw_css(): string {
		$list_of_variables = $this->global_variables();

		if ( empty( $list_of_variables ) ) {
			return '';
		}

		$css_entries = $this->css_entries_for( $list_of_variables );

		if ( empty( $css_entries ) ) {
			return '';
		}

		return $this->wrap_with_root( $css_entries );
	}

	private function css_entries_for( array $list_of_variables ): array {
		$entries = [];

		foreach ( $list_of_variables as $variable_id => $variable ) {
			$entry = $this->build_css_variable_entry( $variable_id, $variable );

			if ( empty( $entry ) ) {
				continue;
			}

			$entries[] = $entry;
		}

		return $entries;
	}

	private function build_css_variable_entry( string $id, array $variable ): ?string {
		$variable_name = sanitize_text_field( $id );

		if ( ! array_key_exists( 'deleted', $variable ) ) {
			$variable_name = sanitize_text_field( $variable['label'] ?? '' );
		}

		$value = sanitize_text_field( $variable['value'] ?? '' );

		if ( empty( $value ) || empty( $variable_name ) ) {
			return null;
		}

		return "--{$variable_name}:{$value};";
	}

	private function wrap_with_root( array $css_entries ): string {
		return ':root { ' . implode( ' ', $css_entries ) . ' }';
	}
}
