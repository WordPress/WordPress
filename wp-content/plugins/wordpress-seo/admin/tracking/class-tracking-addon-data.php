<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Tracking
 */

use Yoast\WP\SEO\Conditionals\WooCommerce_Conditional;

/**
 * Represents the addon option data.
 */
class WPSEO_Tracking_Addon_Data implements WPSEO_Collection {

	/**
	 * The local options we want to track.
	 *
	 * @var string[] The option_names for the options we want to track.
	 */
	private $local_include_list = [
		'use_multiple_locations',
		'multiple_locations_same_organization',
		'business_type',
		'woocommerce_local_pickup_setting',
	];

	/**
	 * The woo options we want to track.
	 *
	 * @var string[] The option_names for the options we want to track.
	 */
	private $woo_include_list = [];

	/**
	 * The news options we want to track.
	 *
	 * @var string[] The option_names for the options we want to track.
	 */
	private $news_include_list = [];

	/**
	 * The video options we want to track.
	 *
	 * @var string[] The option_names for the options we want to track.
	 */
	private $video_include_list = [];

	/**
	 * Returns the collection data.
	 *
	 * @return array The collection data.
	 */
	public function get() {

		$addon_settings = [];
		$addon_manager  = new WPSEO_Addon_Manager();

		if ( $addon_manager->is_installed( WPSEO_Addon_Manager::LOCAL_SLUG ) ) {
			$addon_settings = $this->get_local_addon_settings( $addon_settings, 'wpseo_local', WPSEO_Addon_Manager::LOCAL_SLUG, $this->local_include_list );
		}

		if ( $addon_manager->is_installed( WPSEO_Addon_Manager::WOOCOMMERCE_SLUG ) ) {
			$addon_settings = $this->get_addon_settings( $addon_settings, 'wpseo_woo', WPSEO_Addon_Manager::WOOCOMMERCE_SLUG, $this->woo_include_list );
		}

		if ( $addon_manager->is_installed( WPSEO_Addon_Manager::NEWS_SLUG ) ) {
			$addon_settings = $this->get_addon_settings( $addon_settings, 'wpseo_news', WPSEO_Addon_Manager::NEWS_SLUG, $this->news_include_list );
		}

		if ( $addon_manager->is_installed( WPSEO_Addon_Manager::VIDEO_SLUG ) ) {
			$addon_settings = $this->get_addon_settings( $addon_settings, 'wpseo_video', WPSEO_Addon_Manager::VIDEO_SLUG, $this->video_include_list );
		}

		return $addon_settings;
	}

	/**
	 * Gets the tracked options from the addon
	 *
	 * @param array  $addon_settings      The current list of addon settings.
	 * @param string $source_name         The option key of the addon.
	 * @param string $slug                The addon slug.
	 * @param array  $option_include_list All the options to be included in tracking.
	 *
	 * @return array
	 */
	public function get_addon_settings( array $addon_settings, $source_name, $slug, $option_include_list ) {
		$source_options = get_option( $source_name, [] );
		if ( ! is_array( $source_options ) || empty( $source_options ) ) {
			return $addon_settings;
		}
		$addon_settings[ $slug ] = array_intersect_key( $source_options, array_flip( $option_include_list ) );

		return $addon_settings;
	}

	/**
	 * Filter business_type in local addon settings.
	 *
	 * Remove the business_type setting when 'multiple_locations_shared_business_info' setting is turned off.
	 *
	 * @param array  $addon_settings      The current list of addon settings.
	 * @param string $source_name         The option key of the addon.
	 * @param string $slug                The addon slug.
	 * @param array  $option_include_list All the options to be included in tracking.
	 *
	 * @return array
	 */
	public function get_local_addon_settings( array $addon_settings, $source_name, $slug, $option_include_list ) {
		$source_options = get_option( $source_name, [] );
		if ( ! is_array( $source_options ) || empty( $source_options ) ) {
			return $addon_settings;
		}
		$addon_settings[ $slug ] = array_intersect_key( $source_options, array_flip( $option_include_list ) );

		if ( array_key_exists( 'use_multiple_locations', $source_options ) && array_key_exists( 'business_type', $addon_settings[ $slug ] ) && $source_options['use_multiple_locations'] === 'on' && $source_options['multiple_locations_shared_business_info'] === 'off' ) {
			$addon_settings[ $slug ]['business_type'] = 'multiple_locations';
		}

		if ( ! ( new WooCommerce_Conditional() )->is_met() ) {
			unset( $addon_settings[ $slug ]['woocommerce_local_pickup_setting'] );
		}

		return $addon_settings;
	}
}
