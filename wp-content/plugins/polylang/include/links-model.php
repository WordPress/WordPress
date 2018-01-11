<?php

/**
 * Links model abstract class
 *
 * @since 1.5
 */
abstract class PLL_Links_Model {
	public $model, $options;
	public $home; // used to store the home url before it is filtered
	public $using_permalinks;

	/**
	 * Constructor
	 *
	 * @since 1.5
	 *
	 * @param object $model PLL_Model instance
	 */
	public function __construct( &$model ) {
		$this->model = &$model;
		$this->options = &$model->options;

		$this->home = home_url();

		add_filter( 'pll_languages_list', array( $this, 'pll_languages_list' ), 4 ); // after PLL_Static_Pages
		add_filter( 'pll_after_languages_cache', array( $this, 'pll_after_languages_cache' ) );

		// adds our domains or subdomains to allowed hosts for safe redirection
		add_filter( 'allowed_redirect_hosts', array( $this, 'allowed_redirect_hosts' ) );
	}

	/**
	 * Changes the language code in url
	 *
	 * @since 1.5
	 *
	 * @param string $url  url to modify
	 * @param object $lang language
	 * @return string modified url
	 */
	public function switch_language_in_link( $url, $lang ) {
		$url = $this->remove_language_from_link( $url );
		return $this->add_language_to_link( $url, $lang );
	}

	/**
	 * Get hosts managed on the website
	 *
	 * @since 1.5
	 *
	 * @return array list of hosts
	 */
	public function get_hosts() {
		return array( parse_url( $this->home, PHP_URL_HOST ) );
	}

	/**
	 * Returns the home url
	 *
	 * @since 1.3.1
	 *
	 * @param object $lang PLL_Language object
	 * @return string
	 */
	public function home_url( $lang ) {
		$url = trailingslashit( $this->home );
		return $this->options['hide_default'] && $lang->slug == $this->options['default_lang'] ? $url : $this->add_language_to_link( $url, $lang );
	}

	/**
	 * Sets the home urls
	 *
	 * @since 1.8
	 *
	 * @param object $language
	 */
	protected function set_home_url( $language ) {
		$search_url = $this->home_url( $language );
		$home_url = empty( $language->page_on_front ) || $this->options['redirect_lang'] ? $search_url : $this->front_page_url( $language );
		$language->set_home_url( $search_url, $home_url );
	}

	/**
	 * Sets the home urls and flags before the languages are persistently cached
	 *
	 * @since 1.8
	 *
	 * @param array $languages array of PLL_Language objects
	 * @return array
	 */
	public function pll_languages_list( $languages ) {
		foreach ( $languages as $language ) {
			$this->set_home_url( $language );
			$language->set_flag();
		}
		return $languages;
	}

	/**
	 * Sets the home urls when not cached
	 * Sets the home urls scheme
	 *
	 * @since 1.8
	 *
	 * @param array $languages array of PLL_Language objects
	 * @return array
	 */
	public function pll_after_languages_cache( $languages ) {
		foreach ( $languages as $language ) {
			// Get the home urls when not cached
			if ( ( defined( 'PLL_CACHE_LANGUAGES' ) && ! PLL_CACHE_LANGUAGES ) || ( defined( 'PLL_CACHE_HOME_URL' ) && ! PLL_CACHE_HOME_URL ) ) {
				$this->set_home_url( $language );
			}

			// Ensures that the ( possibly cached ) home url uses the right scheme http or https
			$language->set_home_url_scheme();
		}
		return $languages;
	}

	/**
	 * Adds our domains or subdomains to allowed hosts for safe redirection
	 *
	 * @since 1.4.3
	 *
	 * @param array $hosts allowed hosts
	 * @return array
	 */
	public function allowed_redirect_hosts( $hosts ) {
		return array_unique( array_merge( $hosts, array_values( $this->get_hosts() ) ) );
	}
}
