<?php

/**
 * Links model for use when using one domain or subdomain per language
 *
 * @since 2.0
 */
abstract class PLL_Links_Abstract_Domain extends PLL_Links_Permalinks {

	/**
	 * Constructor
	 *
	 * @since 2.0
	 *
	 * @param object $model PLL_Model instance
	 */
	public function __construct( &$model ) {
		parent::__construct( $model );

		// Avoid cross domain requests ( mainly for custom fonts )
		add_filter( 'content_url', array( $this, 'site_url' ) );
		add_filter( 'plugins_url', array( $this, 'site_url' ) );
		add_filter( 'rest_url', array( $this, 'site_url' ) );
		add_filter( 'upload_dir', array( $this, 'upload_dir' ) );
	}

	/**
	 * Returns the language based on language code in url
	 * links_model interface
	 *
	 * @since 1.2
	 * @since 2.0 add $url argument
	 *
	 * @param string $url optional, defaults to current url
	 * @return string language slug
	 */
	public function get_language_from_url( $url = '' ) {
		$host = empty( $url ) ? $_SERVER['HTTP_HOST'] : parse_url( $url, PHP_URL_HOST );
		return ( $lang = array_search( $host, $this->get_hosts() ) ) ? $lang : '';
	}

	/**
	 * Sets the home urls
	 *
	 * @since 2.2
	 *
	 * @param object $language
	 */
	protected function set_home_url( $language ) {
		$home_url = $this->home_url( $language );
		$language->set_home_url( $home_url, $home_url ); // Search url and home url are the same
	}

	/**
	 * Returns the current site url
	 *
	 * @since 1.8
	 *
	 * @param string $url
	 * @return string
	 */
	public function site_url( $url ) {
		$lang = $this->get_language_from_url();
		$lang = $this->model->get_language( $lang );
		return $this->add_language_to_link( $url, $lang );
	}

	/**
	 * Fix the domain for upload directory
	 *
	 * @since 2.0.6
	 *
	 * @param array $uploads
	 * @return array
	 */
	public function upload_dir( $uploads ) {
		$lang = $this->get_language_from_url();
		$lang = $this->model->get_language( $lang );
		$uploads['url'] = $this->add_language_to_link( $uploads['url'], $lang );
		$uploads['baseurl'] = $this->add_language_to_link( $uploads['baseurl'], $lang );
		return $uploads;
	}
}
