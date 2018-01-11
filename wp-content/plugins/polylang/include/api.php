<?php

/**
 * Template tag: displays the language switcher
 *
 * List of parameters accepted in $args:
 *
 * dropdown               => displays a dropdown if set to 1, defaults to 0
 * echo                   => echoes the switcher if set to 1 ( default )
 * hide_if_empty          => hides languages with no posts ( or pages ) if set to 1 ( default )
 * show_flags             => shows flags if set to 1, defaults to 0
 * show_names             => shows languages names if set to 1 ( default )
 * display_names_as       => whether to display the language name or its slug, valid options are 'slug' and 'name', defaults to name
 * force_home             => forces linking to the home page is set to 1, defaults to 0
 * hide_if_no_translation => hides the link if there is no translation if set to 1, defaults to 0
 * hide_current           => hides the current language if set to 1, defaults to 0
 * post_id                => if not null, link to translations of post defined by post_id, defaults to null
 * raw                    => set this to true to build your own custom language switcher, defaults to 0
 * item_spacing           => whether to preserve or discard whitespace between list items, valid options are 'preserve' and 'discard', defaults to preserve
 *
 * @since 0.5
 *
 * @param array $args optional
 * @return null|string|array null if displaying, array if raw is requested, string otherwise
 */
function pll_the_languages( $args = '' ) {
	if ( PLL() instanceof PLL_Frontend ) {
		$switcher = new PLL_Switcher;
		return $switcher->the_languages( PLL()->links, $args );
	}
	return '';
}

/**
 * Returns the current language on frontend
 * Returns the language set in admin language filter on backend ( false if set to all languages )
 *
 * @since 0.8.1
 *
 * @param string $field Optional, the language field to return ( see PLL_Language ), defaults to 'slug'
 * @return string|bool The requested field for the current language
 */
function pll_current_language( $field = 'slug' ) {
	return isset( PLL()->curlang->$field ) ? PLL()->curlang->$field : false;
}

/**
 * Returns the default language
 *
 * @since 1.0
 *
 * @param string $field Optional, the language field to return ( see PLL_Language ), defaults to 'slug'
 * @return string The requested field for the default language
 */
function pll_default_language( $field = 'slug' ) {
	return isset( PLL()->options['default_lang'] ) && ( $lang = PLL()->model->get_language( PLL()->options['default_lang'] ) ) && isset( $lang->$field ) ? $lang->$field : false;
}

/**
 * Among the post and its translations, returns the id of the post which is in the language represented by $slug
 *
 * @since 0.5
 *
 * @param int    $post_id post id
 * @param string $slug    optional language code, defaults to current language
 * @return int|false|null post id of the translation if exists, false otherwise, null if the current language is not defined yet
 */
function pll_get_post( $post_id, $slug = '' ) {
	return ( $slug = $slug ? $slug : pll_current_language() ) ? PLL()->model->post->get( $post_id, $slug ) : null;
}

/**
 * Among the term and its translations, returns the id of the term which is in the language represented by $slug
 *
 * @since 0.5
 *
 * @param int    $term_id term id
 * @param string $slug    optional language code, defaults to current language
 * @return int|false|null term id of the translation if exists, false otherwise, null if the current language is not defined yet
 */
function pll_get_term( $term_id, $slug = '' ) {
	return ( $slug = $slug ? $slug : pll_current_language() ) ? PLL()->model->term->get( $term_id, $slug ) : null;
}

/**
 * Returns the home url in the current language
 *
 * @since 0.8
 *
 * @param string $lang language code ( optional on frontend )
 * @return string
 */
function pll_home_url( $lang = '' ) {
	if ( empty( $lang ) ) {
		$lang = pll_current_language();
	}

	return empty( $lang ) ? home_url( '/' ) : PLL()->links->get_home_url( $lang );
}

/**
 * Registers a string for translation in the "strings translation" panel
 *
 * @since 0.6
 *
 * @param string $name      a unique name for the string
 * @param string $string    the string to register
 * @param string $context   optional the group in which the string is registered, defaults to 'polylang'
 * @param bool   $multiline optional whether the string table should display a multiline textarea or a single line input, defaults to single line
 */
function pll_register_string( $name, $string, $context = 'polylang', $multiline = false ) {
	if ( PLL() instanceof PLL_Admin_Base ) {
		PLL_Admin_Strings::register_string( $name, $string, $context, $multiline );
	}
}

/**
 * Translates a string ( previously registered with pll_register_string )
 *
 * @since 0.6
 *
 * @param string $string the string to translate
 * @return string the string translation in the current language
 */
function pll__( $string ) {
	return is_scalar( $string ) ? __( $string, 'pll_string' ) : $string;
}

/**
 * Translates a string ( previously registered with pll_register_string ) and escapes it for safe use in HTML output.
 *
 * @since 2.1
 *
 * @param string $string the string to translate
 * @return string translation in the current language
 */
function pll_esc_html__( $string ) {
	return esc_html( pll__( $string ) );
}

/**
 * Translates a string ( previously registered with pll_register_string ) and escapes it for safe use in HTML attributes.
 *
 * @since 2.1
 *
 * @param string $string The string to translate
 * @return string
 */
function pll_esc_attr__( $string ) {
	return esc_attr( pll__( $string ) );
}

/**
 * Echoes a translated string ( previously registered with pll_register_string )
 *
 * @since 0.6
 *
 * @param string $string The string to translate
 */
function pll_e( $string ) {
	echo pll__( $string );
}

/**
 * Echoes a translated string ( previously registered with pll_register_string ) and escapes it for safe use in HTML output.
 *
 * @since 2.1
 *
 * @param string $string The string to translate
 */
function pll_esc_html_e( $string ) {
	echo pll_esc_html__( $string );
}

/**
 * Echoes a translated a string ( previously registered with pll_register_string ) and escapes it for safe use in HTML attributes.
 *
 * @since 2.1
 *
 * @param string $string The string to translate
 */
function pll_esc_attr_e( $string ) {
	echo pll_esc_attr__( $string );
}

/**
 * Translates a string ( previously registered with pll_register_string )
 *
 * @since 1.5.4
 *
 * @param string $string the string to translate
 * @param string $lang   language code
 * @return string the string translation in the requested language
 */
function pll_translate_string( $string, $lang ) {
	if ( PLL() instanceof PLL_Frontend && pll_current_language() == $lang ) {
		return pll__( $string );
	}

	if ( ! is_scalar( $string ) ) {
		return $string;
	}

	static $cache; // Cache object to avoid loading the same translations object several times

	if ( empty( $cache ) ) {
		$cache = new PLL_Cache();
	}

	if ( false === $mo = $cache->get( $lang ) ) {
		$mo = new PLL_MO();
		$mo->import_from_db( PLL()->model->get_language( $lang ) );
		$cache->set( $lang, $mo );
	}

	return $mo->translate( $string );
}

/**
 * Returns true if Polylang manages languages and translations for this post type
 *
 * @since 1.0.1
 *
 * @param string $post_type Post type name
 * @return bool
 */
function pll_is_translated_post_type( $post_type ) {
	return PLL()->model->is_translated_post_type( $post_type );
}

/**
 * Returns true if Polylang manages languages and translations for this taxonomy
 *
 * @since 1.0.1
 *
 * @param string $tax Taxonomy name
 * @return bool
 */
function pll_is_translated_taxonomy( $tax ) {
	return PLL()->model->is_translated_taxonomy( $tax );
}

/**
 * Returns the list of available languages
 *
 * List of parameters accepted in $args:
 *
 * hide_empty => hides languages with no posts if set to true ( defaults to false )
 * fields     => return only that field if set ( see PLL_Language for a list of fields )
 *
 * @since 1.5
 *
 * @param array $args list of parameters
 * @return array
 */
function pll_languages_list( $args = array() ) {
	$args = wp_parse_args( $args, array( 'fields' => 'slug' ) );
	return PLL()->model->get_languages_list( $args );
}

/**
 * Set the post language
 *
 * @since 1.5
 *
 * @param int    $id   post id
 * @param string $lang language code
 */
function pll_set_post_language( $id, $lang ) {
	PLL()->model->post->set_language( $id, $lang );
}

/**
 * Set the term language
 *
 * @since 1.5
 *
 * @param int    $id   term id
 * @param string $lang language code
 */
function pll_set_term_language( $id, $lang ) {
	PLL()->model->term->set_language( $id, $lang );
}

/**
 * Save posts translations
 *
 * @since 1.5
 *
 * @param array $arr an associative array of translations with language code as key and post id as value
 */
function pll_save_post_translations( $arr ) {
	PLL()->model->post->save_translations( reset( $arr ), $arr );
}

/**
 * Save terms translations
 *
 * @since 1.5
 *
 * @param array $arr an associative array of translations with language code as key and term id as value
 */
function pll_save_term_translations( $arr ) {
	PLL()->model->term->save_translations( reset( $arr ), $arr );
}

/**
 * Returns the post language
 *
 * @since 1.5.4
 *
 * @param int    $post_id
 * @param string $field   Optional, the language field to return ( see PLL_Language ), defaults to 'slug'
 * @return bool|string The requested field for the post language, false if no language is associated to that post
 */
function pll_get_post_language( $post_id, $field = 'slug' ) {
	return ( $lang = PLL()->model->post->get_language( $post_id ) ) ? $lang->$field : false;
}

/**
 * Returns the term language
 *
 * @since 1.5.4
 *
 * @param int    $term_id
 * @param string $field   Optional, the language field to return ( see PLL_Language ), defaults to 'slug'
 * @return bool|string The requested field for the term language, false if no language is associated to that term
 */
function pll_get_term_language( $term_id, $field = 'slug' ) {
	return ( $lang = PLL()->model->term->get_language( $term_id ) ) ? $lang->$field : false;
}

/**
 * Returns an array of translations of a post
 *
 * @since 1.8
 *
 * @param int $post_id
 * @return array an associative array of translations with language code as key and translation post_id as value
 */
function pll_get_post_translations( $post_id ) {
	return PLL()->model->post->get_translations( $post_id );
}

/**
 * Returns an array of translations of a term
 *
 * @since 1.8
 *
 * @param int $term_id
 * @return array an associative array of translations with language code as key and translation term_id as value
 */
function pll_get_term_translations( $term_id ) {
	return PLL()->model->term->get_translations( $term_id );
}

/**
 * Count posts in a language
 *
 * @since 1.5
 *
 * @param string $lang language code
 * @param array  $args ( accepted keys: post_type, m, year, monthnum, day, author, author_name, post_format )
 * @return int posts count
 */
function pll_count_posts( $lang, $args = array() ) {
	return PLL()->model->count_posts( PLL()->model->get_language( $lang ), $args );
}

/**
 * Allows to access the Polylang instance
 * It is always preferable to use API functions
 * Internal methods may be changed without prior notice
 *
 * @since 1.8
 */
function PLL() {
	return $GLOBALS['polylang'];
}
