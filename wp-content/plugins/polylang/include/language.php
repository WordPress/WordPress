<?php

/**
 * a language object is made of two terms in 'language' and 'term_language' taxonomies
 * manipulating only one object per language instead of two terms should make things easier
 *
 * properties:
 * term_id             => id of term in 'language' taxonomy
 * name                => language name. Ex: English
 * slug                => language code used in url. Ex: en
 * term_group          => order of the language when displayed in a list of languages
 * term_taxonomy_id    => term taxonomy id in 'language' taxonomy
 * taxonomy            => 'language'
 * description         => language locale for backward compatibility
 * parent              => 0 / not used
 * count               => number of posts and pages in that language
 * tl_term_id          => id of the term in 'term_language' taxonomy
 * tl_term_taxonomy_id => term taxonomy id in 'term_language' taxonomy
 * tl_count            => number of terms in that language ( not used by Polylang )
 * locale              => WordPress language locale. Ex: en_US
 * is_rtl              => 1 if the language is rtl
 * flag_code           => code of the flag
 * flag_url            => url of the flag
 * flag                => html img of the flag
 * custom_flag_url     => url of the custom flag if exists, internal use only, moves to flag_url on frontend
 * custom_flag         => html img of the custom flag if exists, internal use only, moves to flag on frontend
 * home_url            => home url in this language
 * search_url          => home url to use in search forms
 * host                => host of this language
 * mo_id               => id of the post storing strings translations
 * page_on_front       => id of the page on front in this language ( set from pll_languages_list filter )
 * page_for_posts      => id of the page for posts in this language ( set from pll_languages_list filter )
 *
 * @since 1.2
 */
class PLL_Language {
	public $term_id, $name, $slug, $term_group, $term_taxonomy_id, $taxonomy, $description, $parent, $count;
	public $tl_term_id, $tl_term_taxonomy_id, $tl_count;
	public $locale, $is_rtl;
	public $flag_url, $flag;
	public $home_url, $search_url;
	public $host, $mo_id;
	public $page_on_front, $page_for_posts;

	/**
	 * constructor: builds a language object given its two corresponding terms in language and term_language taxonomies
	 *
	 * @since 1.2
	 *
	 * @param object|array $language      'language' term or language object properties stored as an array
	 * @param object       $term_language Corresponding 'term_language' term
	 */
	public function __construct( $language, $term_language = null ) {
		// build the object from all properties stored as an array
		if ( empty( $term_language ) ) {
			foreach ( $language as $prop => $value ) {
				$this->$prop = $value;
			}
		}

		// build the object from taxonomies
		else {
			foreach ( $language as $prop => $value ) {
				$this->$prop = in_array( $prop, array( 'term_id', 'term_taxonomy_id', 'count' ) ) ? (int) $language->$prop : $language->$prop;
			}

			// although it would be convenient here, don't assume the term is shared between taxonomies as it may not be the case in future
			// http://make.wordpress.org/core/2013/07/28/potential-roadmap-for-taxonomy-meta-and-post-relationships/
			$this->tl_term_id = (int) $term_language->term_id;
			$this->tl_term_taxonomy_id = (int) $term_language->term_taxonomy_id;
			$this->tl_count = (int) $term_language->count;

			// the description field can contain any property
			// backward compatibility for is_rtl
			$description = maybe_unserialize( $language->description );
			foreach ( $description as $prop => $value ) {
				'rtl' == $prop ? $this->is_rtl = $value : $this->$prop = $value;
			}

			$this->description = &$this->locale; // backward compatibility with Polylang < 1.2

			$this->mo_id = PLL_MO::get_id( $this );
		}
	}

	/**
	 * sets flag_url and flag properties
	 *
	 * @since 1.2
	 */
	public function set_flag() {
		$flags['flag']['url'] = '';

		// Polylang builtin flags
		if ( ! empty( $this->flag_code ) && file_exists( POLYLANG_DIR . ( $file = '/flags/' . $this->flag_code . '.png' ) ) ) {
			$flags['flag']['url'] = esc_url_raw( plugins_url( $file, POLYLANG_FILE ) );

			// if base64 encoded flags are preferred
			if ( ! defined( 'PLL_ENCODED_FLAGS' ) || PLL_ENCODED_FLAGS ) {
				$flags['flag']['src'] = 'data:image/png;base64,' . base64_encode( file_get_contents( POLYLANG_DIR . $file ) );
			} else {
				$flags['flag']['src'] = esc_url( plugins_url( $file, POLYLANG_FILE ) );
			}
		}

		// custom flags ?
		if ( file_exists( PLL_LOCAL_DIR . ( $file = '/' . $this->locale . '.png' ) ) || file_exists( PLL_LOCAL_DIR . ( $file = '/' . $this->locale . '.jpg' ) ) ) {
			$url = content_url( '/polylang' . $file );
			$flags['custom_flag']['url'] = esc_url_raw( $url );
			$flags['custom_flag']['src'] = esc_url( $url );
		}

		/**
		 * Filter the flag title attribute
		 * Defaults to the language name
		 *
		 * @since 0.7
		 *
		 * @param string $title  the flag title attribute
		 * @param string $slug   the language code
		 * @param string $locale the language locale
		 */
		$title = apply_filters( 'pll_flag_title', $this->name, $this->slug, $this->locale );

		foreach ( $flags as $key => $flag ) {
			$this->{$key . '_url'} = empty( $flag['url'] ) ? '' : $flag['url'];

			/**
			 * Filter the html markup of a flag
			 *
			 * @since 1.0.2
			 *
			 * @param string $flag html markup of the flag or empty string
			 * @param string $slug language code
			 */
			$this->{$key} = apply_filters( 'pll_get_flag', empty( $flag['src'] ) ? '' :
				sprintf(
					'<img src="%s" title="%s" alt="%s" />',
					$flag['src'],
					esc_attr( $title ),
					esc_attr( $this->name )
				),
				$this->slug
			);
		}
	}

	/**
	 * replace flag by custom flag
	 * takes care of url scheme
	 *
	 * @since 1.7
	 */
	public function set_custom_flag() {
		// overwrite with custom flags on frontend only
		if ( ! empty( $this->custom_flag ) ) {
			$this->flag = $this->custom_flag;
			$this->flag_url = $this->custom_flag_url;
			unset( $this->custom_flag, $this->custom_flag_url ); // hide this
		}

		// set url scheme, also for default flags
		if ( is_ssl() ) {
			$this->flag = str_replace( 'http://', 'https://', $this->flag );
			$this->flag_url = str_replace( 'http://', 'https://', $this->flag_url );
		} else {
			$this->flag = str_replace( 'https://', 'http://', $this->flag );
			$this->flag_url = str_replace( 'https://', 'http://', $this->flag_url );
		}
	}

	/**
	 * updates post and term count
	 *
	 * @since 1.2
	 */
	public function update_count() {
		wp_update_term_count( $this->term_taxonomy_id, 'language' ); // posts count
		wp_update_term_count( $this->tl_term_taxonomy_id, 'term_language' ); // terms count
	}

	/**
	 * set home_url and search_url properties
	 *
	 * @since 1.3
	 *
	 * @param string $search_url
	 * @param string $home_url
	 */
	public function set_home_url( $search_url, $home_url ) {
		$this->search_url = $search_url;
		$this->home_url = $home_url;
	}

	/**
	 * set home_url scheme
	 * this can't be cached across pages
	 *
	 * @since 1.6.4
	 */
	public function set_home_url_scheme() {
		if ( is_ssl() ) {
			$this->home_url = str_replace( 'http://', 'https://', $this->home_url );
			$this->search_url = str_replace( 'http://', 'https://', $this->search_url );
		}

		else {
			$this->home_url = str_replace( 'https://', 'http://', $this->home_url );
			$this->search_url = str_replace( 'https://', 'http://', $this->search_url );
		}
	}

	/**
	 * returns the language locale
	 * converts WP locales to W3C valid locales for display
	 * @see #33511
	 *
	 * @since 1.8
	 *
	 * @param string $filter either 'display' or 'raw', defaults to raw
	 * @return string
	 */
	public function get_locale( $filter = 'raw' ) {
		if ( 'display' == $filter ) {
			static $valid_locales = array(
				'bel'            => 'be',
				'bre'            => 'br',
				'de_CH_informal' => 'de_CH',
				'de_DE_formal'   => 'de_DE',
				'dzo'            => 'dz',
				'ido'            => 'io',
				'kin'            => 'rw',
				'oci'            => 'oc',
				'mri'            => 'mi',
				'nl_NL_formal'   => 'nl_NL',
				'roh'            => 'rm',
				'srd'            => 'sc',
				'tuk'            => 'tk',
			);
			$locale = isset( $valid_locales[ $this->locale ] ) ? $valid_locales[ $this->locale ] : $this->locale;
			return str_replace( '_', '-', $locale );
		}
		return $this->locale;
	}
}
