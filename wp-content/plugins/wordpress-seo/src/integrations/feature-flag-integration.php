<?php

namespace Yoast\WP\SEO\Integrations;

use WPSEO_Admin_Asset_Manager;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Conditionals\Feature_Flag_Conditional;

/**
 * Gathers all feature flags and surfaces them to the JavaScript side of the plugin.
 */
class Feature_Flag_Integration implements Integration_Interface {

	/**
	 * The admin asset manager.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	protected $asset_manager;

	/**
	 * All of the feature flag conditionals.
	 *
	 * @var Feature_Flag_Conditional[]
	 */
	protected $feature_flags;

	/**
	 * Feature_Flag_Integration constructor.
	 *
	 * @param WPSEO_Admin_Asset_Manager $asset_manager    The admin asset manager.
	 * @param Feature_Flag_Conditional  ...$feature_flags All of the known feature flag conditionals.
	 */
	public function __construct( WPSEO_Admin_Asset_Manager $asset_manager, Feature_Flag_Conditional ...$feature_flags ) {
		$this->asset_manager = $asset_manager;
		$this->feature_flags = $feature_flags;
	}

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return string[] The conditionals based on which this loadable should be active.
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class ];
	}

	/**
	 * Initializes the integration.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'admin_init', [ $this, 'add_feature_flags' ] );
	}

	/**
	 * Gathers all the feature flags and injects them into the JavaScript.
	 *
	 * @return void
	 */
	public function add_feature_flags() {
		$enabled_features = $this->get_enabled_features();
		// Localize under both names for BC.
		$this->asset_manager->localize_script( 'feature-flag-package', 'wpseoFeatureFlags', $enabled_features );
		$this->asset_manager->localize_script( 'feature-flag-package', 'wpseoFeaturesL10n', $enabled_features );
	}

	/**
	 * Returns an array of all enabled feature flags.
	 *
	 * @return string[] The array of enabled features.
	 */
	public function get_enabled_features() {
		$enabled_features = [];
		foreach ( $this->feature_flags as $feature_flag ) {
			if ( $feature_flag->is_met() ) {
				$enabled_features[] = $feature_flag->get_feature_name();
			}
		}

		return $this->filter_enabled_features( $enabled_features );
	}

	/**
	 * Runs the list of enabled feature flags through a filter.
	 *
	 * @param string[] $enabled_features The list of currently enabled feature flags.
	 *
	 * @return string[] The (possibly adapted) list of enabled features.
	 */
	protected function filter_enabled_features( $enabled_features ) {
		/**
		 * Filters the list of currently enabled feature flags.
		 *
		 * @param string[] $enabled_features The current list of enabled feature flags.
		 */
		$filtered_enabled_features = \apply_filters( 'wpseo_enable_feature', $enabled_features );

		if ( ! \is_array( $filtered_enabled_features ) ) {
			$filtered_enabled_features = $enabled_features;
		}

		return $filtered_enabled_features;
	}
}
