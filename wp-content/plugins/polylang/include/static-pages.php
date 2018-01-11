<?php

/**
 * base class to manage the static front page and the page for posts
 *
 * @since 1.8
 */
abstract class PLL_Static_Pages {
	public $model, $options;
	public $page_on_front, $page_for_posts;

	/**
	 * constructor: setups filters and actions
	 *
	 * @since 1.8
	 *
	 * @param object $polylang
	 */
	public function __construct( &$polylang ) {
		$this->model = &$polylang->model;
		$this->options = &$polylang->options;
		$this->curlang = &$polylang->curlang;

		$this->init();

		// Modifies the page link in case the front page is not in the default language
		add_filter( 'page_link', array( $this, 'page_link' ), 20, 2 );

		// clean the languages cache when editing page of front, page for posts
		add_action( 'update_option_show_on_front', array( $this->model, 'clean_languages_cache' ) );
		add_action( 'update_option_page_on_front', array( $this->model, 'clean_languages_cache' ) );
		add_action( 'update_option_page_for_posts', array( $this->model, 'clean_languages_cache' ) );

		// refresh rewrite rules when the page on front is modified
		add_action( 'update_option_page_on_front', 'flush_rewrite_rules' );
	}

	/**
	 * stores the page on front and page for posts ids
	 *
	 * @since 1.8
	 */
	public function init() {
		if ( 'page' == get_option( 'show_on_front' ) ) {
			$this->page_on_front = get_option( 'page_on_front' );
			$this->page_for_posts = get_option( 'page_for_posts' );
		}

		else {
			$this->page_on_front = 0;
			$this->page_for_posts = 0;
		}
	}

	/**
	 * Modifies the page link in case the front page is not in the default language
	 *
	 * @since 0.7.2
	 *
	 * @param string $link link to the page
	 * @param int    $id   post id of the page
	 * @return string modified link
	 */
	public function page_link( $link, $id ) {
		if ( ( $lang = $this->model->post->get_language( $id ) ) && $id == $lang->page_on_front ) {
			return $lang->home_url;
		}
		return $link;
	}

	/**
	 * adds page_on_front and page_for_posts properties to the language objects
	 *
	 * @since 1.8
	 *
	 * @param array  $languages
	 * @param object $model
	 */
	public static function pll_languages_list( $languages, $model ) {
		if ( 'page' === get_option( 'show_on_front' ) ) {
			foreach ( $languages as $k => $language ) {
				$languages[ $k ]->page_on_front = $model->post->get( get_option( 'page_on_front' ), $language );
				$languages[ $k ]->page_for_posts = $model->post->get( get_option( 'page_for_posts' ), $language );
			}
		}

		return $languages;
	}

	/**
	 * Translates page for posts
	 *
	 * @since 1.8
	 *
	 * @param int $v page for posts page id
	 * @return int
	 */
	public function translate_page_for_posts( $v ) {
		// Returns the current page if there is no translation to avoid ugly notices
		return isset( $this->curlang->page_for_posts ) ? $this->curlang->page_for_posts : $v;
	}
}
