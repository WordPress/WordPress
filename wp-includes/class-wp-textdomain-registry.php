<?php
/**
 * Locale API: WP_Textdomain_Registry class.
 *
 * This file uses rtrim() instead of untrailingslashit() and trailingslashit()
 * to avoid formatting.php dependency.
 *
 * @package WordPress
 * @subpackage i18n
 * @since 6.1.0
 */

/**
 * Core class used for registering text domains.
 *
 * @since 6.1.0
 */
#[AllowDynamicProperties]
class WP_Textdomain_Registry {
	/**
	 * List of domains and all their language directory paths for each locale.
	 *
	 * @since 6.1.0
	 *
	 * @var array
	 */
	protected $all = array();

	/**
	 * List of domains and their language directory path for the current (most recent) locale.
	 *
	 * @since 6.1.0
	 *
	 * @var array
	 */
	protected $current = array();

	/**
	 * List of domains and their custom language directory paths.
	 *
	 * @see load_plugin_textdomain()
	 * @see load_theme_textdomain()
	 *
	 * @since 6.1.0
	 *
	 * @var array
	 */
	protected $custom_paths = array();

	/**
	 * Holds a cached list of available .mo files to improve performance.
	 *
	 * @since 6.1.0
	 * @since 6.5.0 This property is no longer used.
	 *
	 * @var array
	 *
	 * @deprecated
	 */
	protected $cached_mo_files = array();

	/**
	 * Holds a cached list of domains with translations to improve performance.
	 *
	 * @since 6.2.0
	 *
	 * @var string[]
	 */
	protected $domains_with_translations = array();

	/**
	 * Initializes the registry.
	 *
	 * Hooks into the {@see 'upgrader_process_complete'} filter
	 * to invalidate MO files caches.
	 *
	 * @since 6.5.0
	 */
	public function init() {
		add_action( 'upgrader_process_complete', array( $this, 'invalidate_mo_files_cache' ), 10, 2 );
	}

	/**
	 * Returns the languages directory path for a specific domain and locale.
	 *
	 * @since 6.1.0
	 *
	 * @param string $domain Text domain.
	 * @param string $locale Locale.
	 *
	 * @return string|false Languages directory path or false if there is none available.
	 */
	public function get( $domain, $locale ) {
		$path = $this->all[ $domain ][ $locale ] ?? $this->get_path_from_lang_dir( $domain, $locale );

		/**
		 * Filters the determined languages directory path for a specific domain and locale.
		 *
		 * @since 6.6.0
		 *
		 * @param string|false $path   Languages directory path for the given domain and locale.
		 * @param string       $domain Text domain.
		 * @param string       $locale Locale.
		 */
		return apply_filters( 'lang_dir_for_domain', $path, $domain, $locale );
	}

	/**
	 * Determines whether any MO file paths are available for the domain.
	 *
	 * This is the case if a path has been set for the current locale,
	 * or if there is no information stored yet, in which case
	 * {@see _load_textdomain_just_in_time()} will fetch the information first.
	 *
	 * @since 6.1.0
	 *
	 * @param string $domain Text domain.
	 * @return bool Whether any MO file paths are available for the domain.
	 */
	public function has( $domain ) {
		return (
			isset( $this->current[ $domain ] ) ||
			empty( $this->all[ $domain ] ) ||
			in_array( $domain, $this->domains_with_translations, true )
		);
	}

	/**
	 * Sets the language directory path for a specific domain and locale.
	 *
	 * Also sets the 'current' property for direct access
	 * to the path for the current (most recent) locale.
	 *
	 * @since 6.1.0
	 *
	 * @param string       $domain Text domain.
	 * @param string       $locale Locale.
	 * @param string|false $path   Language directory path or false if there is none available.
	 */
	public function set( $domain, $locale, $path ) {
		$this->all[ $domain ][ $locale ] = $path ? rtrim( $path, '/' ) . '/' : false;
		$this->current[ $domain ]        = $this->all[ $domain ][ $locale ];
	}

	/**
	 * Sets the custom path to the plugin's/theme's languages directory.
	 *
	 * Used by {@see load_plugin_textdomain()} and {@see load_theme_textdomain()}.
	 *
	 * @since 6.1.0
	 *
	 * @param string $domain Text domain.
	 * @param string $path   Language directory path.
	 */
	public function set_custom_path( $domain, $path ) {
		$this->custom_paths[ $domain ] = rtrim( $path, '/' );
	}

	/**
	 * Retrieves translation files from the specified path.
	 *
	 * Allows early retrieval through the {@see 'pre_get_mo_files_from_path'} filter to optimize
	 * performance, especially in directories with many files.
	 *
	 * @since 6.5.0
	 *
	 * @param string $path The directory path to search for translation files.
	 * @return array Array of translation file paths. Can contain .mo and .l10n.php files.
	 */
	public function get_language_files_from_path( $path ) {
		$path = rtrim( $path, '/' ) . '/';

		/**
		 * Filters the translation files retrieved from a specified path before the actual lookup.
		 *
		 * Returning a non-null value from the filter will effectively short-circuit
		 * the MO files lookup, returning that value instead.
		 *
		 * This can be useful in situations where the directory contains a large number of files
		 * and the default glob() function becomes expensive in terms of performance.
		 *
		 * @since 6.5.0
		 *
		 * @param null|array $files List of translation files. Default null.
		 * @param string     $path  The path from which translation files are being fetched.
		 */
		$files = apply_filters( 'pre_get_language_files_from_path', null, $path );

		if ( null !== $files ) {
			return $files;
		}

		$cache_key = md5( $path );
		$files     = wp_cache_get( $cache_key, 'translation_files' );

		if ( false === $files ) {
			$files = glob( $path . '*.mo' );
			if ( false === $files ) {
				$files = array();
			}

			$php_files = glob( $path . '*.l10n.php' );
			if ( is_array( $php_files ) ) {
				$files = array_merge( $files, $php_files );
			}

			wp_cache_set( $cache_key, $files, 'translation_files', HOUR_IN_SECONDS );
		}

		return $files;
	}

	/**
	 * Invalidate the cache for .mo files.
	 *
	 * This function deletes the cache entries related to .mo files when triggered
	 * by specific actions, such as the completion of an upgrade process.
	 *
	 * @since 6.5.0
	 *
	 * @param WP_Upgrader $upgrader   Unused. WP_Upgrader instance. In other contexts this might be a
	 *                                Theme_Upgrader, Plugin_Upgrader, Core_Upgrade, or Language_Pack_Upgrader instance.
	 * @param array       $hook_extra {
	 *     Array of bulk item update data.
	 *
	 *     @type string $action       Type of action. Default 'update'.
	 *     @type string $type         Type of update process. Accepts 'plugin', 'theme', 'translation', or 'core'.
	 *     @type bool   $bulk         Whether the update process is a bulk update. Default true.
	 *     @type array  $plugins      Array of the basename paths of the plugins' main files.
	 *     @type array  $themes       The theme slugs.
	 *     @type array  $translations {
	 *         Array of translations update data.
	 *
	 *         @type string $language The locale the translation is for.
	 *         @type string $type     Type of translation. Accepts 'plugin', 'theme', or 'core'.
	 *         @type string $slug     Text domain the translation is for. The slug of a theme/plugin or
	 *                                'default' for core translations.
	 *         @type string $version  The version of a theme, plugin, or core.
	 *     }
	 * }
	 */
	public function invalidate_mo_files_cache( $upgrader, $hook_extra ) {
		if (
			! isset( $hook_extra['type'] ) ||
			'translation' !== $hook_extra['type'] ||
			array() === $hook_extra['translations']
		) {
			return;
		}

		$translation_types = array_unique( wp_list_pluck( $hook_extra['translations'], 'type' ) );

		foreach ( $translation_types as $type ) {
			switch ( $type ) {
				case 'plugin':
					wp_cache_delete( md5( WP_LANG_DIR . '/plugins/' ), 'translation_files' );
					break;
				case 'theme':
					wp_cache_delete( md5( WP_LANG_DIR . '/themes/' ), 'translation_files' );
					break;
				default:
					wp_cache_delete( md5( WP_LANG_DIR . '/' ), 'translation_files' );
					break;
			}
		}
	}

	/**
	 * Returns possible language directory paths for a given text domain.
	 *
	 * @since 6.2.0
	 *
	 * @param string $domain Text domain.
	 * @return string[] Array of language directory paths.
	 */
	private function get_paths_for_domain( $domain ) {
		$locations = array(
			WP_LANG_DIR . '/plugins',
			WP_LANG_DIR . '/themes',
		);

		if ( isset( $this->custom_paths[ $domain ] ) ) {
			$locations[] = $this->custom_paths[ $domain ];
		}

		return $locations;
	}

	/**
	 * Gets the path to the language directory for the current domain and locale.
	 *
	 * Checks the plugins and themes language directories as well as any
	 * custom directory set via {@see load_plugin_textdomain()} or {@see load_theme_textdomain()}.
	 *
	 * @since 6.1.0
	 *
	 * @see _get_path_to_translation_from_lang_dir()
	 *
	 * @param string $domain Text domain.
	 * @param string $locale Locale.
	 * @return string|false Language directory path or false if there is none available.
	 */
	private function get_path_from_lang_dir( $domain, $locale ) {
		$locations = $this->get_paths_for_domain( $domain );

		$found_location = false;

		foreach ( $locations as $location ) {
			$files = $this->get_language_files_from_path( $location );

			$mo_path  = "$location/$domain-$locale.mo";
			$php_path = "$location/$domain-$locale.l10n.php";

			foreach ( $files as $file_path ) {
				if (
					! in_array( $domain, $this->domains_with_translations, true ) &&
					str_starts_with( str_replace( "$location/", '', $file_path ), "$domain-" )
				) {
					$this->domains_with_translations[] = $domain;
				}

				if ( $file_path === $mo_path || $file_path === $php_path ) {
					$found_location = rtrim( $location, '/' ) . '/';
					break 2;
				}
			}
		}

		if ( $found_location ) {
			$this->set( $domain, $locale, $found_location );

			return $found_location;
		}

		/*
		 * If no path is found for the given locale and a custom path has been set
		 * using load_plugin_textdomain/load_theme_textdomain, use that one.
		 */
		if ( 'en_US' !== $locale && isset( $this->custom_paths[ $domain ] ) ) {
			$fallback_location = rtrim( $this->custom_paths[ $domain ], '/' ) . '/';
			$this->set( $domain, $locale, $fallback_location );
			return $fallback_location;
		}

		$this->set( $domain, $locale, false );

		return false;
	}
}
