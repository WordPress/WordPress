<?php
/**
 * Register the scripts, and styles used within WooCommerce Admin.
 */

namespace Automattic\WooCommerce\Internal\Admin;

use _WP_Dependency;
use Automattic\WooCommerce\Admin\Features\Features;
use Automattic\WooCommerce\Admin\PageController;
use Automattic\WooCommerce\Internal\Admin\Loader;

/**
 * WCAdminAssets Class.
 */
class WCAdminAssets {

	/**
	 * Class instance.
	 *
	 * @var WCAdminAssets instance
	 */
	protected static $instance = null;

	/**
	 * Get class instance.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 * Hooks added here should be removed in `wc_admin_initialize` via the feature plugin.
	 */
	public function __construct() {
		Features::get_instance();
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'inject_wc_settings_dependencies' ), 14 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ), 15 );
	}

	/**
	 * Gets the path for the asset depending on file type.
	 *
	 * @param  string $ext File extension.
	 * @return string Folder path of asset.
	 */
	public static function get_path( $ext ) {
		return ( $ext === 'css' ) ? WC_ADMIN_DIST_CSS_FOLDER : WC_ADMIN_DIST_JS_FOLDER;
	}

	/**
	 * Determines if a minified JS file should be served.
	 *
	 * @param  boolean $script_debug Only serve unminified files if script debug is on.
	 * @return boolean If js asset should use minified version.
	 */
	public static function should_use_minified_js_file( $script_debug ) {
		// minified files are only shipped in non-core versions of wc-admin, return false if minified files are not available.
		if ( ! Features::exists( 'minified-js' ) ) {
			return false;
		}

		// Otherwise we will serve un-minified files if SCRIPT_DEBUG is on, or if anything truthy is passed in-lieu of SCRIPT_DEBUG.
		return ! $script_debug;
	}

	/**
	 * Gets the URL to an asset file.
	 *
	 * @param  string $file File name (without extension).
	 * @param  string $ext File extension.
	 * @return string URL to asset.
	 */
	public static function get_url( $file, $ext ) {
		$suffix = '';

		// Potentially enqueue minified JavaScript.
		if ( $ext === 'js' ) {
			$script_debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
			$suffix       = self::should_use_minified_js_file( $script_debug ) ? '.min' : '';
		}

		return plugins_url( self::get_path( $ext ) . $file . $suffix . '.' . $ext, WC_ADMIN_PLUGIN_FILE );
	}

	/**
	 * Gets the file modified time as a cache buster if we're in dev mode, or the plugin version otherwise.
	 *
	 * @param string $ext File extension.
	 * @return string The cache buster value to use for the given file.
	 */
	public static function get_file_version( $ext ) {
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			return filemtime( WC_ADMIN_ABSPATH . self::get_path( $ext ) );
		}
		return WC_VERSION;
	}

	/**
	 * Gets a script asset registry filename. The asset registry lists dependencies for the given script.
	 *
	 * @param  string $script_path_name Path to where the script asset registry is contained.
	 * @param  string $file File name (without extension).
	 * @return string complete asset filename.
	 *
	 * @throws \Exception Throws an exception when a readable asset registry file cannot be found.
	 */
	public static function get_script_asset_filename( $script_path_name, $file ) {
		$minification_supported = Features::exists( 'minified-js' );
		$script_min_filename    = $file . '.min.asset.php';
		$script_nonmin_filename = $file . '.asset.php';
		$script_asset_path      = WC_ADMIN_ABSPATH . WC_ADMIN_DIST_JS_FOLDER . $script_path_name . '/';

		// Check minification is supported first, to avoid multiple is_readable checks when minification is
		// not supported.
		if ( $minification_supported && is_readable( $script_asset_path . $script_min_filename ) ) {
			return $script_min_filename;
		} elseif ( is_readable( $script_asset_path . $script_nonmin_filename ) ) {
			return $script_nonmin_filename;
		} else {
			// could not find an asset file, throw an error.
			throw new \Exception( 'Could not find asset registry for ' . $script_path_name );
		}
	}

	/**
	 * Render a preload link tag for a dependency, optionally
	 * checked against a provided allowlist.
	 *
	 * See: https://macarthur.me/posts/preloading-javascript-in-wordpress
	 *
	 * @param WP_Dependency $dependency The WP_Dependency being preloaded.
	 * @param string        $type Dependency type - 'script' or 'style'.
	 * @param array         $allowlist Optional. List of allowed dependency handles.
	 */
	private function maybe_output_preload_link_tag( $dependency, $type, $allowlist = array() ) {
		if (
			(
				! empty( $allowlist ) &&
				! in_array( $dependency->handle, $allowlist, true )
			) ||
			( ! empty( $this->preloaded_dependencies[ $type ] ) &&
			in_array( $dependency->handle, $this->preloaded_dependencies[ $type ], true ) )
		) {
			return;
		}

		$this->preloaded_dependencies[ $type ][] = $dependency->handle;

		$source = $dependency->ver ? add_query_arg( 'ver', $dependency->ver, $dependency->src ) : $dependency->src;

		echo '<link rel="preload" href="', esc_url( $source ), '" as="', esc_attr( $type ), '" />', "\n";
	}

	/**
	 * Output a preload link tag for dependencies (and their sub dependencies)
	 * with an optional allowlist.
	 *
	 * See: https://macarthur.me/posts/preloading-javascript-in-wordpress
	 *
	 * @param string $type Dependency type - 'script' or 'style'.
	 * @param array  $allowlist Optional. List of allowed dependency handles.
	 */
	private function output_header_preload_tags_for_type( $type, $allowlist = array() ) {
		if ( $type === 'script' ) {
			$dependencies_of_type = wp_scripts();
		} elseif ( $type === 'style' ) {
			$dependencies_of_type = wp_styles();
		} else {
			return;
		}

		foreach ( $dependencies_of_type->queue as $dependency_handle ) {
			$dependency = $dependencies_of_type->query( $dependency_handle, 'registered' );

			if ( $dependency === false ) {
				continue;
			}

			// Preload the subdependencies first.
			foreach ( $dependency->deps as $sub_dependency_handle ) {
				$sub_dependency = $dependencies_of_type->query( $sub_dependency_handle, 'registered' );

				if ( $sub_dependency ) {
					$this->maybe_output_preload_link_tag( $sub_dependency, $type, $allowlist );
				}
			}

			$this->maybe_output_preload_link_tag( $dependency, $type, $allowlist );
		}
	}

	/**
	 * Output preload link tags for all enqueued stylesheets and scripts.
	 *
	 * See: https://macarthur.me/posts/preloading-javascript-in-wordpress
	 */
	private function output_header_preload_tags() {
		$wc_admin_scripts = array(
			WC_ADMIN_APP,
			'wc-components',
		);

		$wc_admin_styles = array(
			WC_ADMIN_APP,
			'wc-components',
			'wc-material-icons',
		);

		// Preload styles.
		$this->output_header_preload_tags_for_type( 'style', $wc_admin_styles );

		// Preload scripts.
		$this->output_header_preload_tags_for_type( 'script', $wc_admin_scripts );
	}

	/**
	 * Loads the required scripts on the correct pages.
	 */
	public function enqueue_assets() {
		if ( ! PageController::is_admin_or_embed_page() ) {
			return;
		}

		wp_enqueue_script( WC_ADMIN_APP );
		wp_enqueue_style( WC_ADMIN_APP );
		wp_enqueue_style( 'wc-material-icons' );
		wp_enqueue_style( 'wc-onboarding' );

		// Preload our assets.
		$this->output_header_preload_tags();
	}

	/**
	 * Registers all the neccessary scripts and styles to show the admin experience.
	 */
	public function register_scripts() {
		if ( ! function_exists( 'wp_set_script_translations' ) ) {
			return;
		}

		$js_file_version  = self::get_file_version( 'js' );
		$css_file_version = self::get_file_version( 'css' );

		$scripts = array(
			'wc-admin-layout',
			'wc-explat',
			'wc-experimental',
			'wc-customer-effort-score',
			// NOTE: This should be removed when Gutenberg is updated and the notices package is removed from WooCommerce Admin.
			'wc-notices',
			'wc-number',
			'wc-tracks',
			'wc-date',
			'wc-components',
			WC_ADMIN_APP,
			'wc-csv',
			'wc-store-data',
			'wc-currency',
			'wc-navigation',
			'wc-product-editor',
		);

		$scripts_map = array(
			WC_ADMIN_APP    => 'app',
			'wc-csv'        => 'csv-export',
			'wc-store-data' => 'data',
		);

		$translated_scripts = array(
			'wc-currency',
			'wc-date',
			'wc-components',
			'wc-customer-effort-score',
			WC_ADMIN_APP,
		);

		foreach ( $scripts as $script ) {
			$script_path_name = isset( $scripts_map[ $script ] ) ? $scripts_map[ $script ] : str_replace( 'wc-', '', $script );

			try {
				$script_assets_filename = self::get_script_asset_filename( $script_path_name, 'index' );
				$script_assets          = require WC_ADMIN_ABSPATH . WC_ADMIN_DIST_JS_FOLDER . $script_path_name . '/' . $script_assets_filename;

				wp_register_script(
					$script,
					self::get_url( $script_path_name . '/index', 'js' ),
					$script_assets ['dependencies'],
					$js_file_version,
					true
				);

				if ( in_array( $script, $translated_scripts, true ) ) {
					wp_set_script_translations( $script, 'woocommerce' );
				}
			} catch ( \Exception $e ) {
				// Avoid crashing WordPress if an asset file could not be loaded.
				wc_caught_exception( $e, __CLASS__ . '::' . __FUNCTION__, $script_path_name );
			}
		}

		wp_register_style(
			'wc-admin-layout',
			self::get_url( 'admin-layout/style', 'css' ),
			array(),
			$css_file_version
		);
		wp_style_add_data( 'wc-admin-layout', 'rtl', 'replace' );

		wp_register_style(
			'wc-components',
			self::get_url( 'components/style', 'css' ),
			array(),
			$css_file_version
		);
		wp_style_add_data( 'wc-components', 'rtl', 'replace' );

		wp_register_style(
			'wc-product-editor',
			self::get_url( 'product-editor/style', 'css' ),
			array(),
			$css_file_version
		);
		wp_style_add_data( 'wc-product-editor', 'rtl', 'replace' );

		wp_register_style(
			'wc-customer-effort-score',
			self::get_url( 'customer-effort-score/style', 'css' ),
			array(),
			$css_file_version
		);
		wp_style_add_data( 'wc-customer-effort-score', 'rtl', 'replace' );

		wp_register_style(
			'wc-experimental',
			self::get_url( 'experimental/style', 'css' ),
			array(),
			$css_file_version
		);
		wp_style_add_data( 'wc-experimental', 'rtl', 'replace' );

		wp_localize_script(
			WC_ADMIN_APP,
			'wcAdminAssets',
			array(
				'path'    => plugins_url( self::get_path( 'js' ), WC_ADMIN_PLUGIN_FILE ),
				'version' => $js_file_version,
			)
		);

		wp_register_style(
			WC_ADMIN_APP,
			self::get_url( 'app/style', 'css' ),
			array( 'wc-components', 'wc-admin-layout', 'wc-customer-effort-score', 'wc-product-editor', 'wp-components', 'wc-experimental' ),
			$css_file_version
		);
		wp_style_add_data( WC_ADMIN_APP, 'rtl', 'replace' );

		wp_register_style(
			'wc-onboarding',
			self::get_url( 'onboarding/style', 'css' ),
			array(),
			$css_file_version
		);
		wp_style_add_data( 'wc-onboarding', 'rtl', 'replace' );
	}

	/**
	 * Injects wp-shared-settings as a dependency if it's present.
	 */
	public function inject_wc_settings_dependencies() {
		if ( wp_script_is( 'wc-settings', 'registered' ) ) {
			$handles_for_injection = [
				'wc-admin-layout',
				'wc-csv',
				'wc-currency',
				'wc-customer-effort-score',
				'wc-navigation',
				// NOTE: This should be removed when Gutenberg is updated and
				// the notices package is removed from WooCommerce Admin.
				'wc-notices',
				'wc-number',
				'wc-date',
				'wc-components',
				'wc-tracks',
				'wc-product-editor',
			];
			foreach ( $handles_for_injection as $handle ) {
				$script = wp_scripts()->query( $handle, 'registered' );
				if ( $script instanceof _WP_Dependency ) {
					$script->deps[] = 'wc-settings';
				}
			}
		}
	}

	/**
	 * Loads a script
	 *
	 * @param string $script_path_name The script path name.
	 * @param string $script_name Filename of the script to load.
	 * @param bool   $need_translation Whether the script need translations.
	 */
	public static function register_script( $script_path_name, $script_name, $need_translation = false ) {
		$script_assets_filename = self::get_script_asset_filename( $script_path_name, $script_name );
		$script_assets          = require WC_ADMIN_ABSPATH . WC_ADMIN_DIST_JS_FOLDER . $script_path_name . '/' . $script_assets_filename;

		wp_enqueue_script(
			'wc-admin-' . $script_name,
			self::get_url( $script_path_name . '/' . $script_name, 'js' ),
			array_merge( array( WC_ADMIN_APP ), $script_assets ['dependencies'] ),
			self::get_file_version( 'js' ),
			true
		);
		if ( $need_translation ) {
			wp_set_script_translations( 'wc-admin-' . $script_name, 'woocommerce' );
		}
	}
}
