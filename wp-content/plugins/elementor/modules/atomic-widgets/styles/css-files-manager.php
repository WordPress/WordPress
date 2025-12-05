<?php

namespace Elementor\Modules\AtomicWidgets\Styles;

class CSS_Files_Manager {
	const DEFAULT_CSS_DIR = 'elementor/css/';
	const FILE_EXTENSION = '.css';
	// Read and write permissions for the owner
	const PERMISSIONS = 0644;

	public function get( string $handle, string $media, callable $get_css, bool $is_valid_cache ): ?Style_File {
		$filesystem = $this->get_filesystem();
		$path = $this->get_path( $handle );

		if ( $is_valid_cache ) {
			if ( ! $filesystem->exists( $path ) ) {
				return null;
			}

			// Return the existing file
			return Style_File::create(
				$this->sanitize_handle( $handle ),
				$this->get_filesystem_path( $this->get_path( $handle ) ),
				$this->get_url( $handle ),
				$media,
			);
		}

		$css = $get_css();

		if ( empty( $css ) ) {
			return null;
		}

		$filesystem_path = $this->get_filesystem_path( $path );

		$is_created = $filesystem->put_contents( $filesystem_path, $css, self::PERMISSIONS );

		if ( false === $is_created ) {
			return null;
		}

		return Style_File::create(
			$this->sanitize_handle( $handle ),
			$filesystem_path,
			$this->get_url( $handle ),
			$media
		);
	}

	private function get_filesystem(): \WP_Filesystem_Base {
		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		return $wp_filesystem;
	}

	private function get_filesystem_path( $path ): string {
		$filesystem = $this->get_filesystem();

		return str_replace( ABSPATH, $filesystem->abspath(), $path );
	}

	private function get_url( string $handle ): string {
		$upload_dir = wp_upload_dir();
		$sanitized_handle = $this->sanitize_handle( $handle );
		$handle = $sanitized_handle . self::FILE_EXTENSION;

		return trailingslashit( $upload_dir['baseurl'] ) . self::DEFAULT_CSS_DIR . $handle;
	}

	private function get_path( string $handle ): string {
		$upload_dir = wp_upload_dir();
		$sanitized_handle = $this->sanitize_handle( $handle );
		$handle = $sanitized_handle . self::FILE_EXTENSION;

		return trailingslashit( $upload_dir['basedir'] ) . self::DEFAULT_CSS_DIR . $handle;
	}

	private function sanitize_handle( string $handle ): string {
		return preg_replace( '/[^a-zA-Z0-9_-]/', '', $handle );
	}
}
