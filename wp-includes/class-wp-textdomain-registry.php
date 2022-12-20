<?php
/**
 * Locale API: WP_Textdomain_Registry class
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
	 *
	 * @var array
	 */
	protected $cached_mo_files = array();

	/**
	 * Holds a cached list of domains with translations to improve performance.
	 *
	 * @since 6.1.2
	 *
	 * @var string[]
	 */
	protected $domains_with_translations = array();

	/**
	 * Returns the languages directory path for a specific domain and locale.
	 *
	 * @since 6.1.0
	 *
	 * @param string $domain Text domain.
	 * @param string $locale Locale.
	 *
	 * @return string|false MO file path or false if there is none available.
	 */
	public function get( $domain, $locale ) {
		if ( isset( $this->all[ $domain ][ $locale ] ) ) {
			return $this->all[ $domain ][ $locale ];
		}

		return $this->get_path_from_lang_dir( $domain, $locale );
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
			! empty( $this->current[ $domain ] ) ||
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
		$this->all[ $domain ][ $locale ] = $path ? trailingslashit( $path ) : false;
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
		$this->custom_paths[ $domain ] = untrailingslashit( $path );
	}

	/**
	 * Returns possible language directory paths for a given text domain.
	 *
	 * @since 6.1.2
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
	 * Gets the path to the language directory for the current locale.
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
			if ( ! isset( $this->cached_mo_files[ $location ] ) ) {
				$this->set_cached_mo_files( $location );
			}

			$path = "$location/$domain-$locale.mo";

			foreach ( $this->cached_mo_files[ $location ] as $mo_path ) {
				if (
					! in_array( $domain, $this->domains_with_translations, true ) &&
					str_starts_with( str_replace( "$location/", '', $mo_path ), "$domain-" )
				) {
					$this->domains_with_translations[] = $domain;
				}

				if ( $mo_path === $path ) {
					$found_location = trailingslashit( $location );
				}
			}
		}

		if ( $found_location ) {
			$this->set( $domain, $locale, $found_location );

			return $found_location;
		}

		// If no path is found for the given locale and a custom path has been set
		// using load_plugin_textdomain/load_theme_textdomain, use that one.
		if ( 'en_US' !== $locale && isset( $this->custom_paths[ $domain ] ) ) {
			$fallback_location = trailingslashit( $this->custom_paths[ $domain ] );
			$this->set( $domain, $locale, $fallback_location );
			return $fallback_location;
		}

		$this->set( $domain, $locale, false );

		return false;
	}

	/**
	 * Reads and caches all available MO files from a given directory.
	 *
	 * @since 6.1.0
	 *
	 * @param string $path Language directory path.
	 */
	private function set_cached_mo_files( $path ) {
		$this->cached_mo_files[ $path ] = array();

		$mo_files = glob( $path . '/*.mo' );

		if ( $mo_files ) {
			$this->cached_mo_files[ $path ] = $mo_files;
		}
	}
}
