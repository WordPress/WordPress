<?php
namespace Elementor\TemplateLibrary\Classes;

use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor media mapper.
 *
 * Maps media URLs to their corresponding filenames.
 */
class Media_Mapper {

	const TRANSIENT_KEY = 'elementor_media_mapping';

	public static function set_mapping( $mapping, $media_dir = '' ) {
		$mapping = is_array( $mapping ) ? $mapping : [];

		if ( ! empty( $mapping ) ) {
			set_transient( self::TRANSIENT_KEY, [
				'mapping' => $mapping,
				'media_dir' => $media_dir,
			], HOUR_IN_SECONDS );
		}
	}

	public static function get_local_file_path( $original_url ) {
		$stored_mapping = get_transient( self::TRANSIENT_KEY );

		if ( ! $stored_mapping || ! is_array( $stored_mapping ) || empty( $original_url ) ) {
			return $original_url;
		}

		$mapping = $stored_mapping['mapping'] ?? [];
		$media_dir = $stored_mapping['media_dir'] ?? '';

		if ( empty( $mapping ) ) {
			return $original_url;
		}

		$filename = $mapping[ $original_url ] ?? null;
		if ( $filename && $media_dir ) {
			$file_path = $media_dir . '/' . $filename;
			if ( file_exists( $file_path ) ) {
				return $file_path;
			}
		}

		return $original_url;
	}

	public static function clear_mapping() {
		$stored_mapping = get_transient( self::TRANSIENT_KEY );

		if ( $stored_mapping && is_array( $stored_mapping ) ) {
			$media_dir = $stored_mapping['media_dir'] ?? '';
			if ( $media_dir ) {
				Plugin::$instance->uploads_manager->remove_file_or_dir( $media_dir );
			}
		}

		delete_transient( self::TRANSIENT_KEY );
	}
}
