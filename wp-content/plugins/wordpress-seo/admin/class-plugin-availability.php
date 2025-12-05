<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Plugin_Availability
 */

use Yoast\WP\SEO\Conditionals\Conditional;
use Yoast\WP\SEO\Conditionals\WooCommerce_Conditional;

/**
 * Class WPSEO_Plugin_Availability
 */
class WPSEO_Plugin_Availability {

	/**
	 * Holds the plugins.
	 *
	 * @var array
	 */
	protected $plugins = [];

	/**
	 * Registers the plugins so we can access them.
	 *
	 * @return void
	 */
	public function register() {
		$this->register_yoast_plugins();
		$this->register_yoast_plugins_status();
	}

	/**
	 * Registers all the available Yoast SEO plugins.
	 *
	 * @return void
	 */
	protected function register_yoast_plugins() {
		$this->plugins = [
			'yoast-seo-premium' => [
				'url'          => WPSEO_Shortlinker::get( 'https://yoa.st/1y7' ),
				'title'        => 'Yoast SEO Premium',
				'description'  => sprintf(
					/* translators: %1$s expands to Yoast SEO */
					__( 'The premium version of %1$s with more features & support.', 'wordpress-seo' ),
					'Yoast SEO'
				),
				'installed'    => false,
				'slug'         => 'wordpress-seo-premium/wp-seo-premium.php',
				'version_sync' => true,
				'premium'      => true,
			],

			'video-seo-for-wordpress-seo-by-yoast' => [
				'url'          => WPSEO_Shortlinker::get( 'https://yoa.st/1y8' ),
				'title'        => 'Video SEO',
				'description'  => __( 'Optimize your videos to show them off in search results and get more clicks!', 'wordpress-seo' ),
				'installed'    => false,
				'slug'         => 'wpseo-video/video-seo.php',
				'version_sync' => true,
				'premium'      => true,
			],

			'yoast-news-seo' => [
				'url'          => WPSEO_Shortlinker::get( 'https://yoa.st/1y9' ),
				'title'        => 'News SEO',
				'description'  => __( 'Are you in Google News? Increase your traffic from Google News by optimizing for it!', 'wordpress-seo' ),
				'installed'    => false,
				'slug'         => 'wpseo-news/wpseo-news.php',
				'version_sync' => true,
				'premium'      => true,
			],

			'local-seo-for-yoast-seo' => [
				'url'          => WPSEO_Shortlinker::get( 'https://yoa.st/1ya' ),
				'title'        => 'Local SEO',
				'description'  => __( 'Rank better locally and in Google Maps, without breaking a sweat!', 'wordpress-seo' ),
				'installed'    => false,
				'slug'         => 'wordpress-seo-local/local-seo.php',
				'version_sync' => true,
				'premium'      => true,
			],

			'yoast-woocommerce-seo' => [
				'url'           => WPSEO_Shortlinker::get( 'https://yoa.st/1o0' ),
				'title'         => 'Yoast WooCommerce SEO',
				'description'   => sprintf(
					/* translators: %1$s expands to Yoast SEO */
					__( 'Seamlessly integrate WooCommerce with %1$s and get extra features!', 'wordpress-seo' ),
					'Yoast SEO'
				),
				'_dependencies' => [
					'WooCommerce' => [
						'slug'        => 'woocommerce/woocommerce.php', // Kept for backwards compatibility, in case external code uses get_dependencies(). Deprecated in 22.4.
						'conditional' => new WooCommerce_Conditional(),
					],
				],
				'installed'     => false,
				'slug'          => 'wpseo-woocommerce/wpseo-woocommerce.php',
				'version_sync'  => true,
				'premium'       => true,
			],
		];
	}

	/**
	 * Sets certain plugin properties based on WordPress' status.
	 *
	 * @return void
	 */
	protected function register_yoast_plugins_status() {

		foreach ( $this->plugins as $name => $plugin ) {

			$plugin_slug = $plugin['slug'];
			$plugin_path = WP_PLUGIN_DIR . '/' . $plugin_slug;

			if ( file_exists( $plugin_path ) ) {
				$plugin_data                         = get_plugin_data( $plugin_path, false, false );
				$this->plugins[ $name ]['installed'] = true;
				$this->plugins[ $name ]['version']   = $plugin_data['Version'];
				$this->plugins[ $name ]['active']    = is_plugin_active( $plugin_slug );
			}
		}
	}

	/**
	 * Checks if there are dependencies available for the plugin.
	 *
	 * @param array $plugin The information available about the plugin.
	 *
	 * @return bool Whether there is a dependency present.
	 */
	public function has_dependencies( $plugin ) {
		return ( isset( $plugin['_dependencies'] ) && ! empty( $plugin['_dependencies'] ) );
	}

	/**
	 * Gets the dependencies for the plugin.
	 *
	 * @param array $plugin The information available about the plugin.
	 *
	 * @return array Array containing all the dependencies associated with the plugin.
	 */
	public function get_dependencies( $plugin ) {
		if ( ! $this->has_dependencies( $plugin ) ) {
			return [];
		}

		return $plugin['_dependencies'];
	}

	/**
	 * Checks if all dependencies are satisfied.
	 *
	 * @param array $plugin The information available about the plugin.
	 *
	 * @return bool Whether or not the dependencies are satisfied.
	 */
	public function dependencies_are_satisfied( $plugin ) {
		if ( ! $this->has_dependencies( $plugin ) ) {
			return true;
		}

		$dependencies        = $this->get_dependencies( $plugin );
		$active_dependencies = array_filter( $dependencies, [ $this, 'is_dependency_active' ] );

		return count( $active_dependencies ) === count( $dependencies );
	}

	/**
	 * Checks whether or not one of the plugins is properly installed and usable.
	 *
	 * @param array $plugin The information available about the plugin.
	 *
	 * @return bool Whether or not the plugin is properly installed.
	 */
	public function is_installed( $plugin ) {
		if ( empty( $plugin ) ) {
			return false;
		}

		return $this->is_available( $plugin );
	}

	/**
	 * Checks for the availability of the plugin.
	 *
	 * @param array $plugin The information available about the plugin.
	 *
	 * @return bool Whether or not the plugin is available.
	 */
	public function is_available( $plugin ) {
		return isset( $plugin['installed'] ) && $plugin['installed'] === true;
	}

	/**
	 * Checks whether a dependency is active.
	 *
	 * @param array<string, Conditional> $dependency The information about the dependency to look for.
	 *
	 * @return bool Whether or not the dependency is active.
	 */
	public function is_dependency_active( $dependency ) {
		return $dependency['conditional']->is_met();
	}

	/**
	 * Gets an array of plugins that have defined dependencies.
	 *
	 * @return array Array of the plugins that have dependencies.
	 */
	public function get_plugins_with_dependencies() {
		return array_filter( $this->plugins, [ $this, 'has_dependencies' ] );
	}

	/**
	 * Determines whether or not a plugin is active.
	 *
	 * @deprecated 23.4
	 * @codeCoverageIgnore
	 *
	 * @param string $plugin The plugin slug to check.
	 *
	 * @return bool Whether or not the plugin is active.
	 */
	public function is_active( $plugin ) {
		_deprecated_function( __METHOD__, 'Yoast SEO 23.4', 'is_plugin_active' );

		return is_plugin_active( $plugin );
	}

	/**
	 * Gets all the possibly available plugins.
	 *
	 * @deprecated 23.4
	 * @codeCoverageIgnore
	 *
	 * @return array Array containing the information about the plugins.
	 */
	public function get_plugins() {
		_deprecated_function( __METHOD__, 'Yoast SEO 23.4', 'WPSEO_Addon_Manager::get_addon_filenames' );

		return $this->plugins;
	}

	/**
	 * Gets a specific plugin. Returns an empty array if it cannot be found.
	 *
	 * @deprecated 23.4
	 * @codeCoverageIgnore
	 *
	 * @param string $plugin The plugin to search for.
	 *
	 * @return array The plugin properties.
	 */
	public function get_plugin( $plugin ) { // @phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found, VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- needed for BC reasons
		_deprecated_function( __METHOD__, 'Yoast SEO 23.4', 'WPSEO_Addon_Manager::get_plugin_file' );
		if ( ! isset( $this->plugins[ $plugin ] ) ) {
			return [];
		}

		return $this->plugins[ $plugin ];
	}

	/**
	 * Gets the version of the plugin.
	 *
	 * @deprecated 23.4
	 * @codeCoverageIgnore
	 *
	 * @param array $plugin The information available about the plugin.
	 *
	 * @return string The version associated with the plugin.
	 */
	public function get_version( $plugin ) { // @phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found, VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- needed for BC reasons
		_deprecated_function( __METHOD__, 'Yoast SEO 23.4', 'WPSEO_Addon_Manager::get_installed_addons_versions' );
		if ( ! isset( $plugin['version'] ) ) {
			return '';
		}

		return $plugin['version'];
	}

	/**
	 * Checks whether a dependency is available.
	 *
	 * @deprecated 22.4
	 * @codeCoverageIgnore
	 *
	 * @param array $dependency The information about the dependency to look for.
	 *
	 * @return bool Whether or not the dependency is available.
	 */
	public function is_dependency_available( $dependency ) {
		_deprecated_function( __METHOD__, 'Yoast SEO 22.4' );

		return isset( get_plugins()[ $dependency['slug'] ] );
	}

	/**
	 * Gets the names of the dependencies.
	 *
	 * @deprecated 23.4
	 * @codeCoverageIgnore
	 *
	 * @param array $plugin The plugin to get the dependency names from.
	 *
	 * @return array Array containing the names of the associated dependencies.
	 */
	public function get_dependency_names( $plugin ) { // @phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found, VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- needed for BC reasons
		_deprecated_function( __METHOD__, 'Yoast SEO 23.4' );
		if ( ! $this->has_dependencies( $plugin ) ) {
			return [];
		}

		return array_keys( $plugin['_dependencies'] );
	}

	/**
	 * Determines whether or not a plugin is a Premium product.
	 *
	 * @deprecated 23.4
	 * @codeCoverageIgnore
	 *
	 * @param array $plugin The plugin to check.
	 *
	 * @return bool Whether or not the plugin is a Premium product.
	 */
	public function is_premium( $plugin ) {
		_deprecated_function( __METHOD__, 'Yoast SEO 23.4' );

		return isset( $plugin['premium'] ) && $plugin['premium'] === true;
	}

	/**
	 * Gets all installed plugins.
	 *
	 * @deprecated 23.4
	 * @codeCoverageIgnore
	 *
	 * @return array The installed plugins.
	 */
	public function get_installed_plugins() {

		_deprecated_function( __METHOD__, 'Yoast SEO 23.4', 'WPSEO_Addon_Manager::get_installed_addons_versions' );
		$installed = [];

		foreach ( $this->plugins as $plugin_key => $plugin ) {
			if ( $this->is_installed( $plugin ) ) {
				$installed[ $plugin_key ] = $plugin;
			}
		}

		return $installed;
	}
}
