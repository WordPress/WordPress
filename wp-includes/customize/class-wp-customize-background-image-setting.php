<?php
/**
 * Customize API: WP_Customize_Background_Image_Setting class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.4.0
 */

/**
 * Customizer Background Image Setting class.
 *
 * @since 3.4.0
 *
 * @see WP_Customize_Setting
 */
final class WP_Customize_Background_Image_Setting extends WP_Customize_Setting {

	/**
	 * Unique string identifier for the setting.
	 *
	 * @since 3.4.0
	 * @var string
	 */
	public $id = 'background_image_thumb';

	/**
	 * @since 3.4.0
	 * @since 7.0.0 Return type updated from void to true for compatibility with base class.
	 *
	 * @param mixed $value The value to update. Not used.
	 * @return true Always returns true.
	 */
	public function update( $value ) {
		remove_theme_mod( 'background_image_thumb' );
		return true;
	}
}
