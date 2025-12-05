<?php

namespace Elementor\Core\Utils\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Parse_Errors {

	/**
	 * @var array<array{key: string, error: string}>
	 */
	private array $errors = [];

	public static function make() {
		return new static();
	}

	public function add( string $key, string $error ): self {
		$this->errors[] = [
			'key' => $key,
			'error' => $error,
		];

		return $this;
	}

	public function is_empty(): bool {
		return empty( $this->errors );
	}

	public function all(): array {
		return $this->errors;
	}

	public function to_string(): string {
		$errors = [];

		foreach ( $this->errors as $error ) {
			$errors[] = $error['key'] . ': ' . $error['error'];
		}

		return implode( ', ', $errors );
	}

	public function merge( Parse_Errors $errors, ?string $prefix = null ): self {
		foreach ( $errors->all() as $error ) {
			$new_key = $prefix ? "{$prefix}.{$error['key']}" : $error['key'];

			$this->add( $new_key, $error['error'] );
		}

		return $this;
	}
}
