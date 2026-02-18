<?php
/**
 * Customize API: WP_Customize_Filter_Setting class
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
class WP_Customize_Filter_Setting extends WP_Customize_Setting {

	/**
	 * Saves the value of the setting, using the related API.
	 *
	 * @since 3.4.0
	 * @since 7.0.0 Return type updated from void to true for compatibility with base class.
	 *
	 * @param mixed $value The value to update.
	 * @return true Always returns true.
	 */
	public function update( $value ) {
		return true;
	}
}
