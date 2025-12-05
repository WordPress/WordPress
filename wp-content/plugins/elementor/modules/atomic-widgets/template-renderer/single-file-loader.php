<?php

namespace Elementor\Modules\AtomicWidgets\TemplateRenderer;

use ElementorDeps\Twig\Error\LoaderError;
use ElementorDeps\Twig\Loader\LoaderInterface;
use ElementorDeps\Twig\Source;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Single_File_Loader implements LoaderInterface {
	private $templates = [];

	private $validity_cache = [];

	public function getSourceContext( string $name ): Source {
		$path = $this->get_template_path( $name );

		return new Source(
			// This is safe to use because we're validating the file path inside `get_template_path`.
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			file_get_contents( $path ),
			$name,
			$path
		);
	}

	public function getCacheKey( string $name ): string {
		return $this->get_template_path( $name );
	}

	public function isFresh( string $name, int $time ): bool {
		$path = $this->get_template_path( $name );

		return filemtime( $path ) < $time;
	}

	public function exists( string $name ) {
		$path = $this->templates[ $name ] ?? null;

		return $this->is_valid_file( $path );
	}

	public function is_registered( string $name ): bool {
		return isset( $this->templates[ $name ] );
	}

	public function register( string $name, string $path ): self {
		if ( ! $this->is_valid_file( $path ) ) {
			throw new LoaderError( esc_html( "Invalid template '{$name}': {$path}" ) );
		}

		$this->templates[ $name ] = $path;

		return $this;
	}

	private function get_template_path( string $name ): string {
		$path = $this->templates[ $name ] ?? null;

		if ( ! $this->is_valid_file( $path ) ) {
			throw new LoaderError( esc_html( "Invalid template '{$name}': {$path}" ) );
		}

		return $path;
	}

	private function is_valid_file( $path ): bool {
		if ( ! $path ) {
			return false;
		}

		if ( isset( $this->validity_cache[ $path ] ) ) {
			return $this->validity_cache[ $path ];
		}

		// Ref: https://github.com/twigphp/Twig/blob/8432946eeeca009d75fc7fc568f3c3f4650f5a0f/src/Loader/FilesystemLoader.php#L260
		if ( str_contains( $path, "\0" ) ) {
			throw new LoaderError( 'A template name cannot contain NULL bytes.' );
		}

		$is_valid = is_file( $path ) && is_readable( $path );

		$this->validity_cache[ $path ] = $is_valid;

		return $is_valid;
	}
}
