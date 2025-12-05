<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Metabox
 */

/**
 * Describes an interface for an analysis that can either be enabled or disabled.
 */
interface WPSEO_Metabox_Analysis {

	/**
	 * Whether this analysis is enabled.
	 *
	 * @return bool Whether or not this analysis is enabled.
	 */
	public function is_enabled();

	/**
	 * Whether or not this analysis is enabled by the user.
	 *
	 * @return bool Whether or not this analysis is enabled by the user.
	 */
	public function is_user_enabled();

	/**
	 * Whether or not this analysis is enabled globally.
	 *
	 * @return bool Whether or not this analysis is enabled globally.
	 */
	public function is_globally_enabled();
}
