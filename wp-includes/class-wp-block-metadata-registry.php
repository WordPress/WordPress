<?php
/**
 * Block Metadata Registry
 *
 * @package WordPress
 * @subpackage Blocks
 * @since 6.7.0
 */

/**
 * Class used for managing block metadata collections.
 *
 * The WP_Block_Metadata_Registry allows plugins to register metadata for large
 * collections of blocks (e.g., 50-100+) using a single PHP file. This approach
 * reduces the need to read and decode multiple `block.json` files, enhancing
 * performance through opcode caching.
 *
 * @since 6.7.0
 */
class WP_Block_Metadata_Registry {

	/**
	 * Container for storing block metadata collections.
	 *
	 * Each entry maps a base path to its corresponding metadata and callback.
	 *
	 * @since 6.7.0
	 * @var array<string, array<string, mixed>>
	 */
	private static $collections = array();

	/**
	 * Caches the last matched collection path for performance optimization.
	 *
	 * @since 6.7.0
	 * @var string|null
	 */
	private static $last_matched_collection = null;

	/**
	 * Stores the WordPress 'wp-includes' directory path.
	 *
	 * @since 6.7.0
	 * @var string|null
	 */
	private static $wpinc_dir = null;

	/**
	 * Stores the normalized WordPress plugin directory path.
	 *
	 * @since 6.7.0
	 * @var string|null
	 */
	private static $plugin_dir = null;

	/**
	 * Registers a block metadata collection.
	 *
	 * This method allows registering a collection of block metadata from a single
	 * manifest file, improving performance for large sets of blocks.
	 *
	 * The manifest file should be a PHP file that returns an associative array, where
	 * the keys are the block identifiers (without their namespace) and the values are
	 * the corresponding block metadata arrays. The block identifiers must match the
	 * parent directory name for the respective `block.json` file.
	 *
	 * Example manifest file structure:
	 * ```
	 * return array(
	 *     'example-block' => array(
	 *         'title' => 'Example Block',
	 *         'category' => 'widgets',
	 *         'icon' => 'smiley',
	 *         // ... other block metadata
	 *     ),
	 *     'another-block' => array(
	 *         'title' => 'Another Block',
	 *         'category' => 'formatting',
	 *         'icon' => 'star-filled',
	 *         // ... other block metadata
	 *     ),
	 *     // ... more block metadata entries
	 * );
	 * ```
	 *
	 * @since 6.7.0
	 *
	 * @param string $path     The absolute base path for the collection ( e.g., WP_PLUGIN_DIR . '/my-plugin/blocks/' ).
	 * @param string $manifest The absolute path to the manifest file containing the metadata collection.
	 * @return bool True if the collection was registered successfully, false otherwise.
	 */
	public static function register_collection( $path, $manifest ) {
		$path = wp_normalize_path( rtrim( $path, '/' ) );

		$wpinc_dir  = self::get_wpinc_dir();
		$plugin_dir = self::get_plugin_dir();

		// Check if the path is valid:
		if ( str_starts_with( $path, $plugin_dir ) ) {
			// For plugins, ensure the path is within a specific plugin directory and not the base plugin directory.
			$relative_path = substr( $path, strlen( $plugin_dir ) + 1 );
			$plugin_name   = strtok( $relative_path, '/' );

			if ( empty( $plugin_name ) || $plugin_name === $relative_path ) {
				_doing_it_wrong(
					__METHOD__,
					__( 'Block metadata collections can only be registered for a specific plugin. The provided path is neither a core path nor a valid plugin path.' ),
					'6.7.0'
				);
				return false;
			}
		} elseif ( ! str_starts_with( $path, $wpinc_dir ) ) {
			// If it's neither a plugin directory path nor within 'wp-includes', the path is invalid.
			_doing_it_wrong(
				__METHOD__,
				__( 'Block metadata collections can only be registered for a specific plugin. The provided path is neither a core path nor a valid plugin path.' ),
				'6.7.0'
			);
			return false;
		}

		if ( ! file_exists( $manifest ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'The specified manifest file does not exist.' ),
				'6.7.0'
			);
			return false;
		}

		self::$collections[ $path ] = array(
			'manifest' => $manifest,
			'metadata' => null,
		);

		return true;
	}

	/**
	 * Retrieves block metadata for a given block within a specific collection.
	 *
	 * This method uses the registered collections to efficiently lookup
	 * block metadata without reading individual `block.json` files.
	 *
	 * @since 6.7.0
	 *
	 * @param string $file_or_folder The path to the file or folder containing the block.
	 * @return array|null The block metadata for the block, or null if not found.
	 */
	public static function get_metadata( $file_or_folder ) {
		$path = self::find_collection_path( $file_or_folder );
		if ( ! $path ) {
			return null;
		}

		$collection = &self::$collections[ $path ];

		if ( null === $collection['metadata'] ) {
			// Load the manifest file if not already loaded
			$collection['metadata'] = require $collection['manifest'];
		}

		// Get the block name from the path.
		$block_name = self::default_identifier_callback( $file_or_folder );

		return isset( $collection['metadata'][ $block_name ] ) ? $collection['metadata'][ $block_name ] : null;
	}

	/**
	 * Finds the collection path for a given file or folder.
	 *
	 * @since 6.7.0
	 *
	 * @param string $file_or_folder The path to the file or folder.
	 * @return string|null The collection path if found, or null if not found.
	 */
	private static function find_collection_path( $file_or_folder ) {
		if ( empty( $file_or_folder ) ) {
			return null;
		}

		// Check the last matched collection first, since block registration usually happens in batches per plugin or theme.
		$path = wp_normalize_path( rtrim( $file_or_folder, '/' ) );
		if ( self::$last_matched_collection && str_starts_with( $path, self::$last_matched_collection ) ) {
			return self::$last_matched_collection;
		}

		$collection_paths = array_keys( self::$collections );
		foreach ( $collection_paths as $collection_path ) {
			if ( str_starts_with( $path, $collection_path ) ) {
				self::$last_matched_collection = $collection_path;
				return $collection_path;
			}
		}
		return null;
	}

	/**
	 * Checks if metadata exists for a given block name in a specific collection.
	 *
	 * @since 6.7.0
	 *
	 * @param string $file_or_folder The path to the file or folder containing the block metadata.
	 * @return bool True if metadata exists for the block, false otherwise.
	 */
	public static function has_metadata( $file_or_folder ) {
		return null !== self::get_metadata( $file_or_folder );
	}

	/**
	 * Default identifier function to determine the block identifier from a given path.
	 *
	 * This function extracts the block identifier from the path:
	 * - For 'block.json' files, it uses the parent directory name.
	 * - For directories, it uses the directory name itself.
	 * - For empty paths, it returns an empty string.
	 *
	 * For example:
	 * - Path: '/wp-content/plugins/my-plugin/blocks/example/block.json'
	 *   Identifier: 'example'
	 * - Path: '/wp-content/plugins/my-plugin/blocks/another-block'
	 *   Identifier: 'another-block'
	 *
	 * This default behavior matches the standard WordPress block structure.
	 *
	 * @since 6.7.0
	 *
	 * @param string $path The file or folder path to determine the block identifier from.
	 * @return string The block identifier, or an empty string if the path is empty.
	 */
	private static function default_identifier_callback( $path ) {
		// Ensure $path is not empty to prevent unexpected behavior.
		if ( empty( $path ) ) {
			return '';
		}

		if ( str_ends_with( $path, 'block.json' ) ) {
			// Return the parent directory name if it's a block.json file.
			return basename( dirname( $path ) );
		}

		// Otherwise, assume it's a directory and return its name.
		return basename( $path );
	}

	/**
	 * Gets the WordPress 'wp-includes' directory path.
	 *
	 * @since 6.7.0
	 *
	 * @return string The WordPress 'wp-includes' directory path.
	 */
	private static function get_wpinc_dir() {
		if ( ! isset( self::$wpinc_dir ) ) {
			self::$wpinc_dir = wp_normalize_path( ABSPATH . WPINC );
		}
		return self::$wpinc_dir;
	}

	/**
	 * Gets the normalized WordPress plugin directory path.
	 *
	 * @since 6.7.0
	 *
	 * @return string The normalized WordPress plugin directory path.
	 */
	private static function get_plugin_dir() {
		if ( ! isset( self::$plugin_dir ) ) {
			self::$plugin_dir = wp_normalize_path( WP_PLUGIN_DIR );
		}
		return self::$plugin_dir;
	}
}
