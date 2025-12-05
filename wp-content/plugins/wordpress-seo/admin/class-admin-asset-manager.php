<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * This class registers all the necessary styles and scripts.
 *
 * Also has methods for the enqueing of scripts and styles.
 * It automatically adds a prefix to the handle.
 */
class WPSEO_Admin_Asset_Manager {

	/**
	 * Prefix for naming the assets.
	 *
	 * @var string
	 */
	public const PREFIX = 'yoast-seo-';

	/**
	 * Class that manages the assets' location.
	 *
	 * @var WPSEO_Admin_Asset_Location
	 */
	protected $asset_location;

	/**
	 * Prefix for naming the assets.
	 *
	 * @var string
	 */
	private $prefix;

	/**
	 * Constructs a manager of assets. Needs a location to know where to register assets at.
	 *
	 * @param WPSEO_Admin_Asset_Location|null $asset_location The provider of the asset location.
	 * @param string                          $prefix         The prefix for naming assets.
	 */
	public function __construct( ?WPSEO_Admin_Asset_Location $asset_location = null, $prefix = self::PREFIX ) {
		if ( $asset_location === null ) {
			$asset_location = self::create_default_location();
		}

		$this->asset_location = $asset_location;
		$this->prefix         = $prefix;
	}

	/**
	 * Enqueues scripts.
	 *
	 * @param string $script The name of the script to enqueue.
	 *
	 * @return void
	 */
	public function enqueue_script( $script ) {
		wp_enqueue_script( $this->prefix . $script );
	}

	/**
	 * Enqueues styles.
	 *
	 * @param string $style The name of the style to enqueue.
	 *
	 * @return void
	 */
	public function enqueue_style( $style ) {
		wp_enqueue_style( $this->prefix . $style );
	}

	/**
	 * Enqueues the appropriate language for the user.
	 *
	 * @return void
	 */
	public function enqueue_user_language_script() {
		$this->enqueue_script( 'language-' . YoastSEO()->helpers->language->get_researcher_language() );
	}

	/**
	 * Registers scripts based on it's parameters.
	 *
	 * @param WPSEO_Admin_Asset $script The script to register.
	 *
	 * @return void
	 */
	public function register_script( WPSEO_Admin_Asset $script ) {
		$url  = $script->get_src() ? $this->get_url( $script, WPSEO_Admin_Asset::TYPE_JS ) : false;
		$args = [
			'in_footer' => $script->is_in_footer(),
		];

		if ( $script->get_strategy() !== '' ) {
			$args['strategy'] = $script->get_strategy();
		}

		wp_register_script(
			$this->prefix . $script->get_name(),
			$url,
			$script->get_deps(),
			$script->get_version(),
			$args
		);

		if ( in_array( 'wp-i18n', $script->get_deps(), true ) ) {
			wp_set_script_translations( $this->prefix . $script->get_name(), 'wordpress-seo' );
		}
	}

	/**
	 * Registers styles based on it's parameters.
	 *
	 * @param WPSEO_Admin_Asset $style The style to register.
	 *
	 * @return void
	 */
	public function register_style( WPSEO_Admin_Asset $style ) {
		wp_register_style(
			$this->prefix . $style->get_name(),
			$this->get_url( $style, WPSEO_Admin_Asset::TYPE_CSS ),
			$style->get_deps(),
			$style->get_version(),
			$style->get_media()
		);
	}

	/**
	 * Calls the functions that register scripts and styles with the scripts and styles to be registered as arguments.
	 *
	 * @return void
	 */
	public function register_assets() {
		$this->register_scripts( $this->scripts_to_be_registered() );
		$this->register_styles( $this->styles_to_be_registered() );
	}

	/**
	 * Registers all the scripts passed to it.
	 *
	 * @param array $scripts The scripts passed to it.
	 *
	 * @return void
	 */
	public function register_scripts( $scripts ) {
		foreach ( $scripts as $script ) {
			$script = new WPSEO_Admin_Asset( $script );
			$this->register_script( $script );
		}
	}

	/**
	 * Registers all the styles it receives.
	 *
	 * @param array $styles Styles that need to be registered.
	 *
	 * @return void
	 */
	public function register_styles( $styles ) {
		foreach ( $styles as $style ) {
			$style = new WPSEO_Admin_Asset( $style );
			$this->register_style( $style );
		}
	}

	/**
	 * Localizes the script.
	 *
	 * @param string $handle      The script handle.
	 * @param string $object_name The object name.
	 * @param array  $data        The l10n data.
	 *
	 * @return void
	 */
	public function localize_script( $handle, $object_name, $data ) {
		wp_localize_script( $this->prefix . $handle, $object_name, $data );
	}

	/**
	 * Adds an inline script.
	 *
	 * @param string $handle   The script handle.
	 * @param string $data     The l10n data.
	 * @param string $position Optional. Whether to add the inline script before the handle or after.
	 *
	 * @return void
	 */
	public function add_inline_script( $handle, $data, $position = 'after' ) {
		wp_add_inline_script( $this->prefix . $handle, $data, $position );
	}

	/**
	 * A list of styles that shouldn't be registered but are needed in other locations in the plugin.
	 *
	 * @return array
	 */
	public function special_styles() {
		$flat_version = $this->flatten_version( WPSEO_VERSION );
		$asset_args   = [
			'name' => 'inside-editor',
			'src'  => 'inside-editor-' . $flat_version,
		];

		return [ 'inside-editor' => new WPSEO_Admin_Asset( $asset_args ) ];
	}

	/**
	 * Flattens a version number for use in a filename.
	 *
	 * @param string $version The original version number.
	 *
	 * @return string The flattened version number.
	 */
	public function flatten_version( $version ) {
		$parts = explode( '.', $version );

		if ( count( $parts ) === 2 && preg_match( '/^\d+$/', $parts[1] ) === 1 ) {
			$parts[] = '0';
		}

		return implode( '', $parts );
	}

	/**
	 * Creates a default location object for use in the admin asset manager.
	 *
	 * @return WPSEO_Admin_Asset_Location The location to use in the asset manager.
	 */
	public static function create_default_location() {
		if ( defined( 'YOAST_SEO_DEV_SERVER' ) && YOAST_SEO_DEV_SERVER ) {
			$url = defined( 'YOAST_SEO_DEV_SERVER_URL' ) ? YOAST_SEO_DEV_SERVER_URL : WPSEO_Admin_Asset_Dev_Server_Location::DEFAULT_URL;

			return new WPSEO_Admin_Asset_Dev_Server_Location( $url );
		}

		return new WPSEO_Admin_Asset_SEO_Location( WPSEO_FILE, false );
	}

	/**
	 * Checks if the given script is enqueued.
	 *
	 * @param string $script The script to check.
	 *
	 * @return bool True when the script is enqueued.
	 */
	public function is_script_enqueued( $script ) {
		return wp_script_is( $this->prefix . $script );
	}

	/**
	 * Gets the list of Elementor dependencies.
	 *
	 * @return array<string> The array of elementor dependencies.
	 */
	protected function get_elementor_dependencies() {
		$dependencies = [
			'backbone-marionette',
			'elementor-common-modules',
			self::PREFIX . 'api-client',
			self::PREFIX . 'externals-components',
			self::PREFIX . 'externals-contexts',
			self::PREFIX . 'externals-redux',
		];
		// Conditionally add Elementor v2 dependency if available.
		if ( wp_script_is( 'elementor-v2-editor-app-bar', 'registered' ) ) {
			$dependencies[] = 'elementor-v2-editor-app-bar';
		}
		return $dependencies;
	}

	/**
	 * Returns the scripts that need to be registered.
	 *
	 * @todo Data format is not self-documenting. Needs explanation inline. R.
	 *
	 * @return array The scripts that need to be registered.
	 */
	protected function scripts_to_be_registered() {
		$header_scripts          = [
			'admin-global',
			'block-editor',
			'classic-editor',
			'post-edit',
			'help-scout-beacon',
			'redirect-old-features-tab',
		];
		$elementor_dependencies  = $this->get_elementor_dependencies();
		$additional_dependencies = [
			'analysis-worker'          => [ self::PREFIX . 'analysis-package' ],
			'api-client'               => [ 'wp-api' ],
			'crawl-settings'           => [ 'jquery' ],
			'dashboard-widget'         => [ self::PREFIX . 'api-client' ],
			'wincher-dashboard-widget' => [ self::PREFIX . 'api-client' ],
			'editor-modules'           => [ 'jquery' ],
			'elementor'                => $elementor_dependencies,
			'indexation'               => [
				'jquery-ui-core',
				'jquery-ui-progressbar',
			],
			'first-time-configuration' => [
				self::PREFIX . 'api-client',
				self::PREFIX . 'externals-components',
				self::PREFIX . 'externals-contexts',
				self::PREFIX . 'externals-redux',
			],
			'integrations-page'        => [
				self::PREFIX . 'api-client',
				self::PREFIX . 'externals-components',
				self::PREFIX . 'externals-contexts',
				self::PREFIX . 'externals-redux',
			],
			'post-edit'                => [
				self::PREFIX . 'api-client',
				self::PREFIX . 'block-editor',
				self::PREFIX . 'externals-components',
				self::PREFIX . 'externals-contexts',
				self::PREFIX . 'externals-redux',
			],
			'reindex-links'            => [
				'jquery-ui-core',
				'jquery-ui-progressbar',
			],
			'settings'                 => [
				'jquery-ui-core',
				'jquery-ui-progressbar',
				self::PREFIX . 'api-client',
				self::PREFIX . 'externals-components',
				self::PREFIX . 'externals-contexts',
				self::PREFIX . 'externals-redux',
			],
			'term-edit'                => [
				self::PREFIX . 'api-client',
				self::PREFIX . 'classic-editor',
				self::PREFIX . 'externals-components',
				self::PREFIX . 'externals-contexts',
				self::PREFIX . 'externals-redux',
			],
			'general-page'             => [
				self::PREFIX . 'api-client',
			],
		];

		$plugin_scripts   = $this->load_generated_asset_file(
			[
				'asset_file'      => __DIR__ . '/../src/generated/assets/plugin.php',
				'ext_length'      => 3,
				'additional_deps' => $additional_dependencies,
				'header_scripts'  => $header_scripts,
			]
		);
		$external_scripts = $this->load_generated_asset_file(
			[
				'asset_file'      => __DIR__ . '/../src/generated/assets/externals.php',
				'ext_length'      => 3,
				'suffix'          => '-package',
				'base_dir'        => 'externals/',
				'additional_deps' => $additional_dependencies,
				'header_scripts'  => $header_scripts,
			]
		);
		$language_scripts = $this->load_generated_asset_file(
			[
				'asset_file'      => __DIR__ . '/../src/generated/assets/languages.php',
				'ext_length'      => 3,
				'suffix'          => '-language',
				'base_dir'        => 'languages/',
				'additional_deps' => $additional_dependencies,
				'header_scripts'  => $header_scripts,
			]
		);
		$renamed_scripts  = $this->load_renamed_scripts();

		$scripts = array_merge(
			$plugin_scripts,
			$external_scripts,
			$language_scripts,
			$renamed_scripts
		);

		$scripts['installation-success'] = [
			'name'    => 'installation-success',
			'src'     => 'installation-success.js',
			'deps'    => [
				'wp-a11y',
				'wp-dom-ready',
				'wp-components',
				'wp-element',
				'wp-i18n',
				self::PREFIX . 'components-new-package',
				self::PREFIX . 'externals-components',
			],
			'version' => $scripts['installation-success']['version'],
		];

		$scripts['post-edit-classic'] = [
			'name'      => 'post-edit-classic',
			'src'       => $scripts['post-edit']['src'],
			'deps'      => array_map(
				static function ( $dep ) {
					if ( $dep === self::PREFIX . 'block-editor' ) {
						return self::PREFIX . 'classic-editor';
					}
					return $dep;
				},
				$scripts['post-edit']['deps']
			),
			'in_footer' => ! in_array( 'post-edit-classic', $header_scripts, true ),
			'version'   => $scripts['post-edit']['version'],
		];

		$scripts['workouts'] = [
			'name'    => 'workouts',
			'src'     => 'workouts.js',
			'deps'    => [
				'clipboard',
				'lodash',
				'wp-api-fetch',
				'wp-a11y',
				'wp-components',
				'wp-compose',
				'wp-data',
				'wp-dom-ready',
				'wp-element',
				'wp-i18n',
				self::PREFIX . 'externals-components',
				self::PREFIX . 'externals-contexts',
				self::PREFIX . 'externals-redux',
				self::PREFIX . 'analysis',
				self::PREFIX . 'components-new-package',
			],
			'version' => $scripts['workouts']['version'],
		];

		// Add the current language to every script that requires the analysis package.
		foreach ( $scripts as $name => $script ) {
			if ( substr( $name, -8 ) === 'language' ) {
				continue;
			}
			if ( in_array( self::PREFIX . 'analysis-package', $script['deps'], true ) ) {
				$scripts[ $name ]['deps'][] = self::PREFIX . YoastSEO()->helpers->language->get_researcher_language() . '-language';
			}
		}

		return $scripts;
	}

	/**
	 * Loads a generated asset file.
	 *
	 * @param array $args {
	 *     The arguments.
	 *
	 *     @type string                  $asset_file      The asset file to load.
	 *     @type int                     $ext_length      The length of the extension, including suffix, of the filename.
	 *     @type string                  $suffix          Optional. The suffix of the asset name.
	 *     @type array<string, string[]> $additional_deps Optional. The additional dependencies assets may have.
	 *     @type string                  $base_dir        Optional. The base directory of the asset.
	 *     @type string[]                $header_scripts  Optional. The script names that should be in the header.
	 * }
	 *
	 * @return array {
	 *     The scripts to be registered.
	 *
	 *     @type string   $name      The name of the asset.
	 *     @type string   $src       The src of the asset.
	 *     @type string[] $deps      The dependenies of the asset.
	 *     @type bool     $in_footer Whether or not the asset should be in the footer.
	 * }
	 */
	protected function load_generated_asset_file( $args ) {
		$args    = wp_parse_args(
			$args,
			[
				'suffix'          => '',
				'additional_deps' => [],
				'base_dir'        => '',
				'header_scripts'  => [],
			]
		);
		$scripts = [];
		$assets  = require $args['asset_file'];
		foreach ( $assets as $file => $data ) {
			$name  = substr( $file, 0, -$args['ext_length'] );
			$name  = strtolower( preg_replace( '/([A-Z])/', '-$1', $name ) );
			$name .= $args['suffix'];

			$deps = $data['dependencies'];
			if ( isset( $args['additional_deps'][ $name ] ) ) {
				$deps = array_merge( $deps, $args['additional_deps'][ $name ] );
			}

			$scripts[ $name ] = [
				'name'      => $name,
				'src'       => $args['base_dir'] . $file,
				'deps'      => $deps,
				'in_footer' => ! in_array( $name, $args['header_scripts'], true ),
				'version'   => $data['version'],
			];
		}

		return $scripts;
	}

	/**
	 * Loads the scripts that should be renamed for BC.
	 *
	 * @return array {
	 *     The scripts to be registered.
	 *
	 *     @type string   $name      The name of the asset.
	 *     @type string   $src       The src of the asset.
	 *     @type string[] $deps      The dependenies of the asset.
	 *     @type bool     $in_footer Whether or not the asset should be in the footer.
	 * }
	 */
	protected function load_renamed_scripts() {
		$scripts         = [];
		$renamed_scripts = [
			'admin-global-script'         => 'admin-global',
			'analysis'                    => 'analysis-package',
			'analysis-report'             => 'analysis-report-package',
			'api'                         => 'api-client',
			'commons'                     => 'commons-package',
			'edit-page'                   => 'edit-page-script',
			'draft-js'                    => 'draft-js-package',
			'feature-flag'                => 'feature-flag-package',
			'helpers'                     => 'helpers-package',
			'jed'                         => 'jed-package',
			'chart.js'                    => 'chart.js-package',
			'network-admin-script'        => 'network-admin',
			'redux'                       => 'redux-package',
			'replacement-variable-editor' => 'replacement-variable-editor-package',
			'search-metadata-previews'    => 'search-metadata-previews-package',
			'social-metadata-forms'       => 'social-metadata-forms-package',
			'styled-components'           => 'styled-components-package',
			'style-guide'                 => 'style-guide-package',
			'yoast-components'            => 'components-new-package',
		];

		foreach ( $renamed_scripts as $original => $replacement ) {
			$scripts[] = [
				'name' => $original,
				'src'  => false,
				'deps' => [ self::PREFIX . $replacement ],
			];
		}

		return $scripts;
	}

	/**
	 * Returns the styles that need to be registered.
	 *
	 * @todo Data format is not self-documenting. Needs explanation inline. R.
	 *
	 * @return array Styles that need to be registered.
	 */
	protected function styles_to_be_registered() {
		$flat_version = $this->flatten_version( WPSEO_VERSION );

		return [
			[
				'name' => 'admin-css',
				'src'  => 'yst_plugin_tools-' . $flat_version,
				'deps' => [ self::PREFIX . 'toggle-switch' ],
			],
			[
				'name' => 'toggle-switch',
				'src'  => 'toggle-switch-' . $flat_version,
			],
			[
				'name' => 'dismissible',
				'src'  => 'wpseo-dismissible-' . $flat_version,
			],
			[
				'name' => 'notifications',
				'src'  => 'notifications-' . $flat_version,
			],
			[
				'name' => 'alert',
				'src'  => 'alerts-' . $flat_version,
			],
			[
				'name' => 'edit-page',
				'src'  => 'edit-page-' . $flat_version,
			],
			[
				'name' => 'featured-image',
				'src'  => 'featured-image-' . $flat_version,
			],
			[
				'name' => 'metabox-css',
				'src'  => 'metabox-' . $flat_version,
				'deps' => [
					self::PREFIX . 'admin-css',
					self::PREFIX . 'tailwind',
					'wp-components',
				],
			],
			[
				'name' => 'block-editor',
				'src'  => 'block-editor-' . $flat_version,
			],
			[
				'name' => 'ai-generator',
				'src'  => 'ai-generator-' . $flat_version,
				'deps' => [
					self::PREFIX . 'ai-frontend',
					self::PREFIX . 'tailwind',
					self::PREFIX . 'introductions',
				],
			],
			[
				'name' => 'ai-fix-assessments',
				'src'  => 'ai-fix-assessments-' . $flat_version,
			],
			[
				'name' => 'ai-frontend',
				'src'  => 'ai-frontend-' . $flat_version,
			],
			[
				'name' => 'introductions',
				'src'  => 'introductions-' . $flat_version,
				'deps' => [ self::PREFIX . 'tailwind' ],
			],
			[
				'name' => 'wp-dashboard',
				'src'  => 'dashboard-' . $flat_version,
			],
			[
				'name' => 'scoring',
				'src'  => 'yst_seo_score-' . $flat_version,
			],
			[
				'name' => 'adminbar',
				'src'  => 'adminbar-' . $flat_version,
				'deps' => [
					'admin-bar',
				],
			],
			[
				'name' => 'primary-category',
				'src'  => 'metabox-primary-category-' . $flat_version,
			],
			[
				'name' => 'admin-global',
				'src'  => 'admin-global-' . $flat_version,
				'deps' => [ self::PREFIX . 'tailwind' ],
			],
			[
				'name' => 'filter-explanation',
				'src'  => 'filter-explanation-' . $flat_version,
			],
			[
				'name' => 'monorepo',
				'src'  => 'monorepo-' . $flat_version,
			],
			[
				'name' => 'structured-data-blocks',
				'src'  => 'structured-data-blocks-' . $flat_version,
				'deps' => [
					'dashicons',
					'forms',
					'wp-edit-blocks',
				],
			],
			[
				'name' => 'elementor',
				'src'  => 'elementor-' . $flat_version,
			],
			[
				'name' => 'tailwind',
				'src'  => 'tailwind-' . $flat_version,
				// Note: The RTL suffix is not added here.
				// Tailwind and our UI library provide styling that should be standalone compatible with RTL.
				// To make it easier we should use the logical properties and values when possible.
				// If there are exceptions, we can use the Tailwind modifier, e.g. `rtl:yst-space-x-reverse`.
				'rtl'  => false,
			],
			[
				'name' => 'new-settings',
				'src'  => 'new-settings-' . $flat_version,
				'deps' => [ self::PREFIX . 'tailwind' ],
			],
			[
				'name' => 'redirects',
				'src'  => 'redirects-' . $flat_version,
				'deps' => [ self::PREFIX . 'tailwind' ],
			],
			[
				'name' => 'black-friday-banner',
				'src'  => 'black-friday-banner-' . $flat_version,
				'deps' => [ self::PREFIX . 'tailwind' ],
			],
			[
				'name' => 'academy',
				'src'  => 'academy-' . $flat_version,
				'deps' => [ self::PREFIX . 'tailwind' ],
			],
			[
				'name' => 'general-page',
				'src'  => 'general-page-' . $flat_version,
				'deps' => [ self::PREFIX . 'tailwind' ],
			],
			[
				'name' => 'installation-success',
				'src'  => 'installation-success-' . $flat_version,
				'deps' => [ self::PREFIX . 'tailwind' ],
			],
			[
				'name' => 'support',
				'src'  => 'support-' . $flat_version,
				'deps' => [ self::PREFIX . 'tailwind' ],
			],
			[
				'name' => 'workouts',
				'src'  => 'workouts-' . $flat_version,
				'deps' => [
					self::PREFIX . 'monorepo',
				],
			],
			[
				'name' => 'first-time-configuration',
				'src'  => 'first-time-configuration-' . $flat_version,
				'deps' => [ self::PREFIX . 'tailwind' ],
			],
			[
				'name' => 'inside-editor',
				'src'  => 'inside-editor-' . $flat_version,
			],
			[
				'name' => 'plans',
				'src'  => 'plans-' . $flat_version,
				'deps' => [ self::PREFIX . 'tailwind' ],
			],
		];
	}

	/**
	 * Determines the URL of the asset.
	 *
	 * @param WPSEO_Admin_Asset $asset The asset to determine the URL for.
	 * @param string            $type  The type of asset. Usually JS or CSS.
	 *
	 * @return string The URL of the asset.
	 */
	protected function get_url( WPSEO_Admin_Asset $asset, $type ) {
		$scheme = wp_parse_url( $asset->get_src(), PHP_URL_SCHEME );
		if ( in_array( $scheme, [ 'http', 'https' ], true ) ) {
			return $asset->get_src();
		}

		return $this->asset_location->get_url( $asset, $type );
	}
}
