<?php

namespace Elementor\Core\Files\Assets;

use Elementor\Core\Files\File_Types\Svg;
use Elementor\Core\Files\Uploads_Manager;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Files Upload Handler
 *
 * @deprecated 3.5.0 Use `Elementor\Core\Files\Uploads_Manager` class instead.
 */
abstract class Files_Upload_Handler {

	/**
	 * @deprecated 3.5.0
	 */
	const OPTION_KEY = 'elementor_unfiltered_files_upload';

	/**
	 * @deprecated 3.5.0
	 */
	abstract public function get_mime_type();

	/**
	 * @deprecated 3.5.0
	 */
	abstract public function get_file_type();

	/**
	 * Is Elementor Media Upload
	 *
	 * @deprecated 3.5.0 Use `Elementor\Plugin::$instance->uploads_manager->are_unfiltered_uploads_enabled()` instead.
	 *
	 * @return bool
	 */
	private function is_elementor_media_upload() {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.5.0', 'Elementor\Plugin::$instance->uploads_manager->are_unfiltered_uploads_enabled()' );

		return Plugin::$instance->uploads_manager->is_elementor_media_upload();
	}

	/**
	 * Is Enabled
	 *
	 * @deprecated 3.5.0 Use `Elementor\Plugin::$instance->uploads_manager->are_unfiltered_uploads_enabled()` instead.
	 *
	 * @return bool
	 */
	final public static function is_enabled() {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.5.0', 'Elementor\Plugin::$instance->uploads_manager->are_unfiltered_uploads_enabled()' );

		return Plugin::$instance->uploads_manager->are_unfiltered_uploads_enabled();
	}

	/**
	 * @deprecated 3.5.0 Use `Elementor\Plugin::$instance->uploads_manager->are_unfiltered_uploads_enabled()` instead.
	 */
	final public function support_unfiltered_files_upload( $existing_mimes ) {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.5.0', 'Elementor\Plugin::$instance->uploads_manager->support_unfiltered_file_uploads()' );

		return Plugin::$instance->uploads_manager->support_unfiltered_elementor_file_uploads( $existing_mimes );
	}

	/**
	 * Handle_upload_prefilter
	 *
	 * @deprecated 3.5.0 Use `Elementor\Plugin::$instance->uploads_manager->handle_elementor_wp_media_upload()` instead.
	 *
	 * @param $file
	 *
	 * @return mixed
	 */
	public function handle_upload_prefilter( $file ) {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.5.0', 'Elementor\Plugin::$instance->uploads_manager->handle_elementor_wp_media_upload()' );

		return Plugin::$instance->uploads_manager->handle_elementor_wp_media_upload( $file );
	}

	/**
	 * Is_file_should_handled
	 *
	 * @deprecated 3.5.0
	 *
	 * @param $file
	 *
	 * @return bool
	 */
	protected function is_file_should_handled( $file ) {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.5.0' );

		$ext = pathinfo( $file['name'], PATHINFO_EXTENSION );

		return $this->is_elementor_media_upload() && $this->get_file_type() === $ext;
	}

	/**
	 * File_sanitizer_can_run
	 *
	 * @deprecated 3.5.0 Use `Elementor\Core\Files\File_Types\Svg::file_sanitizer_can_run()` instead.
	 *
	 * @return bool
	 */
	public static function file_sanitizer_can_run() {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.5.0', 'Elementor\Core\Files\File_Types\Svg::file_sanitizer_can_run()' );

		return Svg::file_sanitizer_can_run();
	}

	/**
	 * Check filetype and ext
	 *
	 * A workaround for upload validation which relies on a PHP extension (fileinfo)
	 * with inconsistent reporting behaviour.
	 * ref: https://core.trac.wordpress.org/ticket/39550
	 * ref: https://core.trac.wordpress.org/ticket/40175
	 *
	 * @deprecated 3.5.0 Use `Elementor\Plugin::$instance->uploads_manager->check_filetype_and_ext()` instead.
	 *
	 * @param $data
	 * @param $file
	 * @param $filename
	 * @param $mimes
	 *
	 * @return mixed
	 */
	public function check_filetype_and_ext( $data, $file, $filename, $mimes ) {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.5.0', 'Elementor\Plugin::$instance->uploads_manager->check_filetype_and_ext()' );

		Plugin::$instance->uploads_manager->check_filetype_and_ext( $data, $file, $filename, $mimes );
	}
}
