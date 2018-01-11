<?php

/**
 * Manages links filters needed on both frontend and admin
 *
 * @since 1.8
 */
class PLL_Filters_Links {
	public $links, $links_model, $model, $options, $curlang;

	/**
	 * Constructor
	 *
	 * @since 1.8
	 *
	 * @param object $polylang
	 */
	public function __construct( &$polylang ) {
		$this->links = &$polylang->links;
		$this->links_model = &$polylang->links_model;
		$this->model = &$polylang->model;
		$this->options = &$polylang->options;
		$this->curlang = &$polylang->curlang;

		// Low priority on links filters to come after any other modifications
		if ( $this->options['force_lang'] ) {
			add_filter( 'post_link', array( $this, 'post_type_link' ), 20, 2 );
			add_filter( '_get_page_link', array( $this, '_get_page_link' ), 20, 2 );
		}

		add_filter( 'post_type_link', array( $this, 'post_type_link' ), 20, 2 );
		add_filter( 'term_link', array( $this, 'term_link' ), 20, 3 );

		if ( $this->options['force_lang'] > 0 ) {
			add_filter( 'attachment_link', array( $this, 'attachment_link' ), 20, 2 );
		}

		if ( 3 === $this->options['force_lang'] ) {
			add_filter( 'preview_post_link', array( $this, 'preview_post_link' ), 20 );
		}

		// Rewrites post types archives links to filter them by language
		add_filter( 'post_type_archive_link', array( $this, 'post_type_archive_link' ), 20, 2 );
	}

	/**
	 * Modifies page links
	 *
	 * @since 1.7
	 *
	 * @param string $link    post link
	 * @param int    $post_id post ID
	 * @return string modified post link
	 */
	public function _get_page_link( $link, $post_id ) {
		// /!\ WP does not use pretty permalinks for preview
		return false !== strpos( $link, 'preview=true' ) && false !== strpos( $link, 'page_id=' ) ? $link : $this->links_model->switch_language_in_link( $link, $this->model->post->get_language( $post_id ) );
	}

	/**
	 * Modifies attachment links
	 *
	 * @since 1.6.2
	 *
	 * @param string $link    attachment link
	 * @param int    $post_id attachment link
	 * @return string modified attachment link
	 */
	public function attachment_link( $link, $post_id ) {
		return wp_get_post_parent_id( $post_id ) ? $link : $this->links_model->switch_language_in_link( $link, $this->model->post->get_language( $post_id ) );
	}

	/**
	 * Modifies custom posts links
	 *
	 * @since 1.6
	 *
	 * @param string $link post link
	 * @param object $post post object
	 * @return string modified post link
	 */
	public function post_type_link( $link, $post ) {
		// /!\ WP does not use pretty permalinks for preview
		if ( ( false === strpos( $link, 'preview=true' ) || false === strpos( $link, 'p=' ) ) && $this->model->is_translated_post_type( $post->post_type ) ) {
			$lang = $this->model->post->get_language( $post->ID );
			$link = $this->options['force_lang'] ? $this->links_model->switch_language_in_link( $link, $lang ) : $link;

			/**
			 * Filter a post or custom post type link
			 *
			 * @since 1.6
			 *
			 * @param string $link the post link
			 * @param object $lang the current language
			 * @param object $post the post object
			 */
			$link = apply_filters( 'pll_post_type_link', $link, $lang, $post );
		}

		return $link;
	}

	/**
	 * Modifies term link
	 *
	 * @since 0.7
	 *
	 * @param string $link term link
	 * @param object $term term object
	 * @param string $tax  taxonomy name
	 * @return string modified term link
	 */
	public function term_link( $link, $term, $tax ) {
		if ( $this->model->is_translated_taxonomy( $tax ) ) {
			$lang = $this->model->term->get_language( $term->term_id );
			$link = $this->options['force_lang'] ? $this->links_model->switch_language_in_link( $link, $lang ) : $link;

			/**
			 * Filter a term link
			 *
			 * @since 1.6
			 *
			 * @param string $link the term link
			 * @param object $lang the current language
			 * @param object $term the term object
			 */
			return apply_filters( 'pll_term_link', $link, $lang, $term );
		}

		// in case someone calls get_term_link for the 'language' taxonomy
		if ( 'language' === $tax ) {
			return $this->links_model->home_url( $term );
		}

		return $link;
	}

	/**
	 * FIXME: keeps the preview post link on default domain when using multiple domains
	 *
	 * @since 1.6.1
	 *
	 * @param string $url
	 * @return string modified url
	 */
	public function preview_post_link( $url ) {
		return $this->links_model->remove_language_from_link( $url );
	}

	/**
	 * Modifies the post type archive links to add the language parameter
	 * only if the post type is translated
	 *
	 * The filter was originally only on frontend but is needed on admin too for
	 * compatibility with the archive link of the ACF link field since ACF 5.4.0
	 *
	 * @since 1.7.6
	 *
	 * @param string $link
	 * @param string $post_type
	 * @return string modified link
	 */
	public function post_type_archive_link( $link, $post_type ) {
		return $this->model->is_translated_post_type( $post_type ) && 'post' !== $post_type ? $this->links_model->switch_language_in_link( $link, $this->curlang ) : $link;
	}
}

