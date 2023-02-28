<?php
/**
 * Customize API: WP_Customize_Header_Image_Setting class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.4.0
 */

/**
 * A setting that is used to filter a value, but will not save the results.
 *
 * Results should be properly handled using another setting or callback.
 *
 * @since 3.4.0
 *
 * @see WP_Customize_Setting
 */
final class WP_Customize_Header_Image_Setting extends WP_Customize_Setting {

	/**
	 * Unique string identifier for the setting.
	 *
	 * @since 3.4.0
	 * @var string
	 */
	public $id = 'header_image_data';

	/**
	 * @since 3.4.0
	 *
	 * @global Custom_Image_Header $custom_image_header
	 *
	 * @param mixed $value The value to update.
	 */
	public function update( $value ) {
		global $custom_image_header;

		// If _custom_header_background_just_in_time() fails to initialize $custom_image_header when not is_admin().
		if ( empty( $custom_image_header ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-custom-image-header.php';
			$args                   = get_theme_support( 'custom-header' );
			$admin_head_callback    = isset( $args[0]['admin-head-callback'] ) ? $args[0]['admin-head-callback'] : null;
			$admin_preview_callback = isset( $args[0]['admin-preview-callback'] ) ? $args[0]['admin-preview-callback'] : null;
			$custom_image_header    = new Custom_Image_Header( $admin_head_callback, $admin_preview_callback );
		}

		// If the value doesn't exist (removed or random),
		// use the header_image value.
		if ( ! $value ) {
			$value = $this->manager->get_setting( 'header_image' )->post_value();
		}

		if ( is_array( $value ) && isset( $value['choice'] ) ) {
			$custom_image_header->set_header_image( $value['choice'] );
		} else {
			$custom_image_header->set_header_image( $value );
		}
	}
}
