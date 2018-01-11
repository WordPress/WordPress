<?php

/**
 * Links model for default permalinks
 * for example mysite.com/?somevar=something&lang=en
 * implements the "links_model interface"
 *
 * @since 1.2
 */
class PLL_Links_Default extends PLL_Links_Model {
	public $using_permalinks = false;

	/**
	 * Adds language information to an url
	 * links_model interface
	 *
	 * @since 1.2
	 *
	 * @param string $url  url to modify
	 * @param object $lang language
	 * @return string modified url
	 */
	public function add_language_to_link( $url, $lang ) {
		return empty( $lang ) || ( $this->options['hide_default'] && $this->options['default_lang'] == $lang->slug ) ? $url : add_query_arg( 'lang', $lang->slug, $url );
	}

	/**
	 * Removes the language information from an url
	 * links_model interface
	 *
	 * @since 1.2
	 *
	 * @param string $url url to modify
	 * @return string modified url
	 */
	public function remove_language_from_link( $url ) {
		return remove_query_arg( 'lang', $url );
	}

	/**
	 * Returns the link to the first page
	 * links_model interface
	 *
	 * @since 1.2
	 *
	 * @param string $url url to modify
	 * @return string modified url
	 */
	function remove_paged_from_link( $url ) {
		return remove_query_arg( 'paged', $url );
	}

	/**
	 * Returns the link to the paged page when using pretty permalinks
	 *
	 * @since 1.5
	 *
	 * @param string $url  url to modify
	 * @param int    $page
	 * @return string modified url
	 */
	public function add_paged_to_link( $url, $page ) {
		return add_query_arg( array( 'paged' => $page ), $url );
	}

	/**
	 * Gets the language slug from the url if present
	 * links_model interface
	 *
	 * @since 1.2
	 * @since 2.0 add $url argument
	 *
	 * @param string $url optional, defaults to current url
	 * @return string language slug
	 */
	public function get_language_from_url( $url = '' ) {
		if ( empty( $url ) ) {
			$url = $_SERVER['REQUEST_URI'];
		}

		$pattern = '#lang=(' . implode( '|', $this->model->get_languages_list( array( 'fields' => 'slug' ) ) ) . ')#';
		return preg_match( $pattern, trailingslashit( $url ), $matches ) ? $matches[1] : ''; // $matches[1] is the slug of the requested language
	}

	/**
	 * Returns the static front page url
	 *
	 * @since 1.8
	 *
	 * @param object $lang
	 * @return string
	 */
	public function front_page_url( $lang ) {
		if ( $this->options['hide_default'] && $lang->slug == $this->options['default_lang'] ) {
			return trailingslashit( $this->home );
		}
		$url = home_url( '/?page_id=' . $lang->page_on_front );
		return $this->options['force_lang'] ? $this->add_language_to_link( $url, $lang ) : $url;
	}
}
