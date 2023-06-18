<?php
namespace Automattic\WooCommerce\Blocks\Assets;

use Automattic\WooCommerce\Blocks\Domain\Package;
use Exception;
/**
 * The Api class provides an interface to various asset registration helpers.
 *
 * Contains asset api methods
 *
 * @since 2.5.0
 */
class Api {
	/**
	 * Stores inline scripts already enqueued.
	 *
	 * @var array
	 */
	private $inline_scripts = [];

	/**
	 * Reference to the Package instance
	 *
	 * @var Package
	 */
	private $package;

	/**
	 * Constructor for class
	 *
	 * @param Package $package An instance of Package.
	 */
	public function __construct( Package $package ) {
		$this->package = $package;
	}

	/**
	 * Get the file modified time as a cache buster if we're in dev mode.
	 *
	 * @param string $file Local path to the file (relative to the plugin
	 *                     directory).
	 * @return string The cache buster value to use for the given file.
	 */
	protected function get_file_version( $file ) {
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG && file_exists( $this->package->get_path() . $file ) ) {
			return filemtime( $this->package->get_path( trim( $file, '/' ) ) );
		}
		return $this->package->get_version();
	}

	/**
	 * Retrieve the url to an asset for this plugin.
	 *
	 * @param string $relative_path An optional relative path appended to the
	 *                              returned url.
	 *
	 * @return string
	 */
	protected function get_asset_url( $relative_path = '' ) {
		return $this->package->get_url( $relative_path );
	}

	/**
	 * Get the path to a block's metadata
	 *
	 * @param string $block_name The block to get metadata for.
	 * @param string $path Optional. The path to the metadata file inside the 'build' folder.
	 *
	 * @return string|boolean False if metadata file is not found for the block.
	 */
	public function get_block_metadata_path( $block_name, $path = '' ) {
		$path_to_metadata_from_plugin_root = $this->package->get_path( 'build/' . $path . $block_name . '/block.json' );
		if ( ! file_exists( $path_to_metadata_from_plugin_root ) ) {
			return false;
		}
		return $path_to_metadata_from_plugin_root;
	}

	/**
	 * Get src, version and dependencies given a script relative src.
	 *
	 * @param string $relative_src Relative src to the script.
	 * @param array  $dependencies Optional. An array of registered script handles this script depends on. Default empty array.
	 *
	 * @return array src, version and dependencies of the script.
	 */
	public function get_script_data( $relative_src, $dependencies = [] ) {
		$src     = '';
		$version = '1';

		if ( $relative_src ) {
			$src        = $this->get_asset_url( $relative_src );
			$asset_path = $this->package->get_path(
				str_replace( '.js', '.asset.php', $relative_src )
			);

			if ( file_exists( $asset_path ) ) {
				// The following require is safe because we are checking if the file exists and it is not a user input.
				// nosemgrep audit.php.lang.security.file.inclusion-arg.
				$asset        = require $asset_path;
				$dependencies = isset( $asset['dependencies'] ) ? array_merge( $asset['dependencies'], $dependencies ) : $dependencies;
				$version      = ! empty( $asset['version'] ) ? $asset['version'] : $this->get_file_version( $relative_src );
			} else {
				$version = $this->get_file_version( $relative_src );
			}
		}

		return array(
			'src'          => $src,
			'version'      => $version,
			'dependencies' => $dependencies,
		);
	}

	/**
	 * Registers a script according to `wp_register_script`, adding the correct prefix, and additionally loading translations.
	 *
	 * When creating script assets, the following rules should be followed:
	 *   1. All asset handles should have a `wc-` prefix.
	 *   2. If the asset handle is for a Block (in editor context) use the `-block` suffix.
	 *   3. If the asset handle is for a Block (in frontend context) use the `-block-frontend` suffix.
	 *   4. If the asset is for any other script being consumed or enqueued by the blocks plugin, use the `wc-blocks-` prefix.
	 *
	 * @since 2.5.0
	 * @throws Exception If the registered script has a dependency on itself.
	 *
	 * @param string $handle        Unique name of the script.
	 * @param string $relative_src  Relative url for the script to the path from plugin root.
	 * @param array  $dependencies  Optional. An array of registered script handles this script depends on. Default empty array.
	 * @param bool   $has_i18n      Optional. Whether to add a script translation call to this file. Default: true.
	 */
	public function register_script( $handle, $relative_src, $dependencies = [], $has_i18n = true ) {
		$script_data = $this->get_script_data( $relative_src, $dependencies );

		if ( in_array( $handle, $script_data['dependencies'], true ) ) {
			if ( $this->package->feature()->is_development_environment() ) {
				$dependencies = array_diff( $script_data['dependencies'], [ $handle ] );
					add_action(
						'admin_notices',
						function() use ( $handle ) {
								echo '<div class="error"><p>';
								/* translators: %s file handle name. */
								printf( esc_html__( 'Script with handle %s had a dependency on itself which has been removed. This is an indicator that your JS code has a circular dependency that can cause bugs.', 'woocommerce' ), esc_html( $handle ) );
								echo '</p></div>';
						}
					);
			} else {
				throw new Exception( sprintf( 'Script with handle %s had a dependency on itself. This is an indicator that your JS code has a circular dependency that can cause bugs.', $handle ) );
			}
		}

		/**
		 * Filters the list of script dependencies.
		 *
		 * @since 3.0.0
		 *
		 * @param array $dependencies The list of script dependencies.
		 * @param string $handle The script's handle.
		 * @return array
		 */
		$script_dependencies = apply_filters( 'woocommerce_blocks_register_script_dependencies', $script_data['dependencies'], $handle );

		wp_register_script( $handle, $script_data['src'], $script_dependencies, $script_data['version'], true );

		if ( $has_i18n && function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( $handle, 'woocommerce', $this->package->get_path( 'languages' ) );
		}
	}

	/**
	 * Registers a style according to `wp_register_style`.
	 *
	 * @since 2.5.0
	 * @since 2.6.0 Change src to be relative source.
	 *
	 * @param string $handle       Name of the stylesheet. Should be unique.
	 * @param string $relative_src Relative source of the stylesheet to the plugin path.
	 * @param array  $deps         Optional. An array of registered stylesheet handles this stylesheet depends on. Default empty array.
	 * @param string $media        Optional. The media for which this stylesheet has been defined. Default 'all'. Accepts media types like
	 *                             'all', 'print' and 'screen', or media queries like '(orientation: portrait)' and '(max-width: 640px)'.
	 */
	public function register_style( $handle, $relative_src, $deps = [], $media = 'all' ) {
		$filename = str_replace( plugins_url( '/', __DIR__ ), '', $relative_src );
		$src      = $this->get_asset_url( $relative_src );
		$ver      = $this->get_file_version( $filename );
		wp_register_style( $handle, $src, $deps, $ver, $media );
	}

	/**
	 * Returns the appropriate asset path for current builds.
	 *
	 * @param   string $filename  Filename for asset path (without extension).
	 * @param   string $type      File type (.css or .js).
	 * @return  string             The generated path.
	 */
	public function get_block_asset_build_path( $filename, $type = 'js' ) {
		return "build/$filename.$type";
	}

	/**
	 * Adds an inline script, once.
	 *
	 * @param string $handle Script handle.
	 * @param string $script Script contents.
	 */
	public function add_inline_script( $handle, $script ) {
		if ( ! empty( $this->inline_scripts[ $handle ] ) && in_array( $script, $this->inline_scripts[ $handle ], true ) ) {
			return;
		}

		wp_add_inline_script( $handle, $script );

		if ( isset( $this->inline_scripts[ $handle ] ) ) {
			$this->inline_scripts[ $handle ][] = $script;
		} else {
			$this->inline_scripts[ $handle ] = array( $script );
		}
	}
}
