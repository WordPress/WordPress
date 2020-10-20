<?php
/**
 * Locale API: WP_Textdomain_Registry class
 *
 * @package WordPress
 * @subpackage i18n
 * @since 5.6.0
 */

/**
 * Core class used for registering text domains.
 *
 * @since 5.6.0
 */
class WP_Textdomain_Registry {
	/**
	 * List of domains and their language directory paths.
	 *
	 * @since 5.6.0
	 *
	 * @var array
	 */
	protected $domains = array();

	/**
	 * Holds a cached list of available .mo files to improve performance.
	 *
	 * @since 5.6.0
	 *
	 * @var array
	 */
	protected $cached_mo_files;

	/**
	 * Returns the MO file path for a specific domain.
	 *
	 * @since 5.6.0
	 *
	 * @param string $domain Text domain.
	 * @return string|false MO file path or false if there is none available.
	 */
	public function get( $domain ) {
		if ( isset( $this->domains[ $domain ] ) ) {
			return $this->domains[ $domain ];
		}

		return $this->get_path_from_lang_dir( $domain );
	}

	/**
	 * Sets the MO file path for a specific domain.
	 *
	 * @since 5.6.0
	 *
	 * @param string $domain Text domain.
	 * @param string|false $path Language directory path or false if there is none available.
	 */
	public function set( $domain, $path ) {
		$this->domains[ $domain ] = $path ? trailingslashit( $path ) : false;
	}

	/**
	 * Resets the registry state.
	 *
	 * @since 5.6.0
	 */
	public function reset() {
		$this->cached_mo_files = null;
		$this->domains         = array();
	}

	/**
	 * Gets the path to a translation file in the languages directory for the current locale.
	 *
	 * @since 5.6.0
	 *
	 * @param string $domain Text domain.
	 * @return string|false MO file path or false if there is none available.
	 */
	private function get_path_from_lang_dir( $domain ) {
		if ( null === $this->cached_mo_files ) {
			$this->cached_mo_files = array();

			$this->set_cached_mo_files();
		}

		$locale = determine_locale();
		$mofile = "{$domain}-{$locale}.mo";

		$path = WP_LANG_DIR . '/plugins/' . $mofile;
		if ( in_array( $path, $this->cached_mo_files, true ) ) {
			$path = WP_LANG_DIR . '/plugins/';
			$this->set( $domain, $path );

			return $path;
		}

		$path = WP_LANG_DIR . '/themes/' . $mofile;
		if ( in_array( $path, $this->cached_mo_files, true ) ) {
			$path = WP_LANG_DIR . '/themes/';
			$this->set( $domain, $path );

			return $path;
		}

		$this->set( $domain, false );

		return false;
	}

	/**
	 * Reads and caches all available MO files from the plugins and themes language directories.
	 *
	 * @since 5.6.0
	 */
	protected function set_cached_mo_files() {
		$locations = array(
			WP_LANG_DIR . '/plugins',
			WP_LANG_DIR . '/themes',
		);

		foreach ( $locations as $location ) {
			$mo_files = glob( $location . '/*.mo' );

			if ( $mo_files ) {
				$this->cached_mo_files = array_merge( $this->cached_mo_files, $mo_files );
			}
		}
	}
}
