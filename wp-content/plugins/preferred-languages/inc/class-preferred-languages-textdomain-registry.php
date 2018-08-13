<?php
/**
 * Locale API: Preferred_Languages_Textdomain_Registry class
 *
 * @package    WordPress
 * @subpackage i18n
 * @since      1.1.0
 */

/**
 * Core class used for registering textdomains
 *
 * @since 1.1.0
 */
class Preferred_Languages_Textdomain_Registry {
	/**
	 * List of domains and their language directory paths.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $domains = array();

	/**
	 * Holds a cached list of available .mo files to improve performance.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $cached_mofiles;

	/**
	 * Returns the MO file path for a specific domain.
	 *
	 * @since  1.1.0
	 * @access public
	 *
	 * @param string $domain Text domain.
	 *
	 * @return string|false|null MO file path or false if there is none available.
	 *                           Null if none have been fetched yet.
	 */
	public function get( $domain ) {
		return isset( $this->domains[ $domain ] ) ? $this->domains[ $domain ] : null;
	}

	/**
	 * Sets the MO file path for a specific domain.
	 *
	 * @since  1.1.0
	 * @access public
	 *
	 * @param string $domain Text domain.
	 * @param string $path   Language directory path.
	 */
	public function set( $domain, $path ) {
		$this->domains[ $domain ] = $path;
	}

	/**
	 * Resets the registry state.
	 *
	 * @since  1.1.0
	 * @access public
	 */
	public function reset() {
		$this->cached_mofiles = null;
		$this->domains        = array();
	}

	/**
	 * Gets the path to a translation file in the languages directory for the current locale.
	 *
	 * @since  1.1.0
	 * @access public
	 *
	 * @see    _get_path_to_translation_from_lang_dir()
	 *
	 * @param string $domain Text domain.
	 */
	public function get_translation_from_lang_dir( $domain ) {
		if ( null === $this->cached_mofiles ) {
			$this->cached_mofiles = array();

			$this->fetch_available_mofiles();
		}

		foreach ( preferred_languages_get_list() as $locale ) {
			$mofile = "{$domain}-{$locale}.mo";

			$path = WP_LANG_DIR . '/plugins/' . $mofile;
			if ( in_array( $path, $this->cached_mofiles, true ) ) {
				$this->set( $domain, WP_LANG_DIR . '/plugins/' );

				return;
			}

			$path = WP_LANG_DIR . '/themes/' . $mofile;
			if ( in_array( $path, $this->cached_mofiles, true ) ) {
				$this->set( $domain, WP_LANG_DIR . '/themes/' );

				return;
			}
		}

		$this->set( $domain, false );
	}

	/**
	 * Fetches all available MO files from the plugins and themes language directories.
	 *
	 * @since  1.1.0
	 * @access protected
	 *
	 * @see _get_path_to_translation_from_lang_dir()
	 */
	protected function fetch_available_mofiles() {
		$locations = array(
			WP_LANG_DIR . '/plugins',
			WP_LANG_DIR . '/themes',
		);

		foreach ( $locations as $location ) {
			$mofiles = glob( $location . '/*.mo' );

			if ( $mofiles ) {
				$this->cached_mofiles = array_merge( $this->cached_mofiles, $mofiles );
			}
		}
	}
}
