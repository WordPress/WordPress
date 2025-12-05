<?php
namespace Elementor\Core\Files\Assets\Svg;

use Elementor\Core\Files\Assets\Files_Upload_Handler;
use Elementor\Core\Files\File_Types\Svg;
use Elementor\Core\Files\Uploads_Manager;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * SVG Handler
 *
 * @deprecated 3.5.0 Use `Elementor\Core\Files\File_Types\Svg` instead, accessed by calling: `Plugin::$instance->uploads_manager->get_file_type_handlers( 'svg' );`
 */
class Svg_Handler extends Files_Upload_Handler {

	/**
	 * Inline svg attachment meta key
	 *
	 * @deprecated 3.5.0
	 */
	const META_KEY = '_elementor_inline_svg';

	/**
	 * @deprecated 3.5.0
	 */
	const SCRIPT_REGEX = '/(?:\w+script|data):/xi';

	/**
	 * Attachment ID.
	 *
	 * Holds the current attachment ID.
	 *
	 * @deprecated 3.5.0
	 *
	 * @var int
	 */
	private $attachment_id;

	/**
	 * @deprecated 3.5.0
	 */
	public static function get_name() {
		return 'svg-handler';
	}

	/**
	 * Get meta
	 *
	 * @deprecated 3.5.0
	 *
	 * @return mixed
	 */
	protected function get_meta() {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.5.0' );

		return get_post_meta( $this->attachment_id, self::META_KEY, true );
	}

	/**
	 * Update meta
	 *
	 * @deprecated 3.5.0
	 *
	 * @param $meta
	 */
	protected function update_meta( $meta ) {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.5.0' );

		update_post_meta( $this->attachment_id, self::META_KEY, $meta );
	}

	/**
	 * Delete meta
	 *
	 * @deprecated 3.5.0
	 */
	protected function delete_meta() {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.5.0' );

		delete_post_meta( $this->attachment_id, self::META_KEY );
	}

	/**
	 * Get mime type
	 *
	 * @deprecated 3.5.0
	 */
	public function get_mime_type() {
		return 'image/svg+xml';
	}

	/**
	 * Get file type
	 *
	 * @deprecated 3.5.0
	 */
	public function get_file_type() {
		return 'svg';
	}

	/**
	 * Delete meta cache
	 *
	 * @deprecated 3.5.0 Use `Plugin::$instance->uploads_manager->get_file_type_handlers( 'svg' )->delete_meta_cache()` instead.
	 */
	public function delete_meta_cache() {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.5.0', 'Plugin::$instance->uploads_manager->get_file_type_handlers( \'svg\' )->delete_meta_cache()' );

		/** @var Svg $svg_handler */
		$svg_handler = Plugin::$instance->uploads_manager->get_file_type_handlers( 'svg' );

		$svg_handler->delete_meta_cache();
	}

	/**
	 * Get inline svg
	 *
	 * @deprecated 3.5.0 Use `Elementor\Core\Files\File_Types\Svg::get_inline_svg()` instead.
	 *
	 * @param $attachment_id
	 *
	 * @return bool|mixed|string
	 */
	public static function get_inline_svg( $attachment_id ) {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.5.0', 'Elementor\Core\Files\File_Types\Svg::get_inline_svg()' );

		return Svg::get_inline_svg( $attachment_id );
	}

	/**
	 * Sanitize svg
	 *
	 * @deprecated 3.5.0 Use `Plugin::$instance->uploads_manager->get_file_type_handlers( 'svg' )->delete_meta_cache()->sanitize_svg()` instead.
	 *
	 * @param $filename
	 *
	 * @return bool
	 */
	public function sanitize_svg( $filename ) {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.5.0', 'Plugin::$instance->uploads_manager->get_file_type_handlers( \'svg\' )->delete_meta_cache()->sanitize_svg()' );

		/** @var Svg $svg_handler */
		$svg_handler = Plugin::$instance->uploads_manager->get_file_type_handlers( 'svg' );

		return $svg_handler->sanitize_svg( $filename );
	}

	/**
	 * Sanitizer
	 *
	 * @deprecated 3.5.0 Use `Plugin::$instance->uploads_manager->get_file_type_handlers( 'svg' )->sanitizer()` instead.
	 *
	 * @param $content
	 *
	 * @return bool|string
	 */
	public function sanitizer( $content ) {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.5.0', 'Plugin::$instance->uploads_manager->get_file_type_handlers( \'svg\' )->sanitizer()' );

		/** @var Svg $svg_handler */
		$svg_handler = Plugin::$instance->uploads_manager->get_file_type_handlers( 'svg' );

		return $svg_handler->sanitizer( $content );
	}

	/**
	 * Prepare attachment for js
	 *
	 * @deprecated 3.5.0 Use `Plugin::$instance->uploads_manager->get_file_type_handlers( 'svg' )->wp_prepare_attachment_for_js()` instead.
	 *
	 * @param $attachment_data
	 * @param $attachment
	 * @param $meta
	 *
	 * @return mixed
	 */
	public function wp_prepare_attachment_for_js( $attachment_data, $attachment, $meta ) {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.5.0', 'Plugin::$instance->uploads_manager->get_file_type_handlers( \'svg\' )->wp_prepare_attachment_for_js()' );

		/** @var Svg $svg_handler */
		$svg_handler = Plugin::$instance->uploads_manager->get_file_type_handlers( 'svg' );

		return $svg_handler->wp_prepare_attachment_for_js( $attachment_data, $attachment, $meta );
	}

	/**
	 * Set attachment id
	 *
	 * @deprecated 3.5.0
	 *
	 * @param $attachment_id
	 *
	 * @return int
	 */
	public function set_attachment_id( $attachment_id ) {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.5.0' );

		$this->attachment_id = $attachment_id;
		return $this->attachment_id;
	}

	/**
	 * Get attachment id
	 *
	 * @deprecated 3.5.0
	 *
	 * @return int
	 */
	public function get_attachment_id() {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.5.0' );

		return $this->attachment_id;
	}

	/**
	 * Set svg meta data
	 *
	 * @deprecated 3.5.0 Use `Plugin::$instance->uploads_manager->get_file_type_handlers( 'svg' )->set_svg_meta_data()` instead.
	 *
	 * @return mixed
	 */
	public function set_svg_meta_data( $data, $id ) {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.5.0', 'Plugin::$instance->uploads_manager->get_file_type_handlers( \'svg\' )->set_svg_meta_data()' );

		/** @var Svg $svg_handler */
		$svg_handler = Plugin::$instance->uploads_manager->get_file_type_handlers( 'svg' );

		return $svg_handler->set_svg_meta_data( $data, $id );
	}

	/**
	 * Handle upload prefilter
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
}
