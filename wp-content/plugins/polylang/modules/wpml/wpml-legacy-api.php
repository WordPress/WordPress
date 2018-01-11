<?php

/**
 * Compatibility with WPML legacy API
 * Deprecated since WPML 3.2 and no more documented
 * But a lot of 3rd party plugins / themes are still using these functions
 */

if ( ! function_exists( 'icl_get_home_url' ) ) {
	/**
	 * Link to the home page in the active language
	 *
	 * @since 0.9.4
	 *
	 * @return string
	 */
	function icl_get_home_url() {
		return pll_home_url();
	}
}

if ( ! function_exists( 'icl_get_languages' ) ) {
	/**
	 * Used for building custom language selectors
	 * available only on frontend
	 *
	 * list of paramaters accepted in $args
	 *
	 * skip_missing  => whether to skip missing translation or not, 0 or 1, defaults to 0
	 * orderby       => 'id', 'code', 'name', defaults to 'id'
	 * order         => 'ASC' or 'DESC', defaults to 'ASC'
	 * link_empty_to => link to use when the translation is missing {$lang} is replaced by the language code
	 *
	 * List of parameters returned per language:
	 *
	 * id               => the language id
	 * active           => whether this is the active language or no, 0 or 1
	 * native_name      => the language name
	 * missing          => whether the translation is missing or not, 0 or 1
	 * translated_name  => empty, does not exist in Polylang
	 * language_code    => the language code ( slug )
	 * country_flag_url => the url of the flag
	 * url              => the url of the translation
	 *
	 * @since 1.0
	 *
	 * @param string|array $args optional
	 * @return array array of arrays per language
	 */
	function icl_get_languages( $args = '' ) {
		$args = wp_parse_args( $args, array( 'skip_missing' => 0, 'orderby' => 'id', 'order' => 'ASC' ) );
		$orderby = ( isset( $args['orderby'] ) && 'code' == $args['orderby'] ) ? 'slug' : ( isset( $args['orderby'] ) && 'name' == $args['orderby'] ? 'name' : 'id' );
		$order = ( ! empty( $args['order'] ) && 'desc' == $args['order'] ) ? 'DESC' : 'ASC';

		$arr = array();

		// NB: When 'skip_missing' is false, WPML returns all languages even if there is no content
		$languages = PLL()->model->get_languages_list( array( 'hide_empty' => $args['skip_missing'] ) );

		// FIXME: Backward compatibility with WP < 4.7
		if ( function_exists( 'wp_list_sort' ) ) {
			$languages = wp_list_sort( $languages, $orderby, $order ); // Since WP 4.7
		}

		foreach ( $languages as $lang ) {
			// We can find a translation only on frontend
			if ( method_exists( PLL()->links, 'get_translation_url' ) ) {
				$url = PLL()->links->get_translation_url( $lang );
			}

			// It seems that WPML does not bother of skip_missing parameter on admin side and before the $wp_query object has been filled
			if ( empty( $url ) && ! empty( $args['skip_missing'] ) && ! is_admin() && did_action( 'parse_query' ) ) {
				continue;
			}

			$arr[ $lang->slug ] = array(
				'id'               => $lang->term_id,
				'active'           => isset( PLL()->curlang->slug ) && PLL()->curlang->slug == $lang->slug ? 1 : 0,
				'native_name'      => $lang->name,
				'missing'          => empty( $url ) ? 1 : 0,
				'translated_name'  => '', // Does not exist in Polylang
				'language_code'    => $lang->slug,
				'country_flag_url' => $lang->flag_url,
				'url'              => ! empty( $url ) ? $url :
					( empty( $args['link_empty_to'] ) ? PLL()->links->get_home_url( $lang ) :
					str_replace( '{$lang}', $lang->slug, $args['link_empty_to'] ) ),
			);
		}

		// Apply undocumented WPML filter
		$arr = apply_filters( 'icl_ls_languages', $arr );

		return $arr;
	}
}

if ( ! function_exists( 'icl_link_to_element' ) ) {
	/**
	 * Used for creating language dependent links in themes
	 *
	 * @since 1.0
	 * @since 2.0 add support for arguments 6 and 7
	 *
	 * @param int    $id                         object id
	 * @param string $type                       optional, post type or taxonomy name of the object, defaults to 'post'
	 * @param string $text                       optional, the link text. If not specified will produce the name of the element in the current language
	 * @param array  $args                       optional, an array of arguments to add to the link, defaults to empty
	 * @param string $anchor                     optional, the anchor to add to the link, defaults to empty
	 * @param bool   $echo                       optional, whether to echo the link, defaults to true
	 * @param bool   $return_original_if_missing optional, whether to return a value if the translation is missing
	 * @return string a language dependent link
	 */
	function icl_link_to_element( $id, $type = 'post', $text = '', $args = array(), $anchor = '', $echo = true, $return_original_if_missing = true ) {
		if ( 'tag' == $type ) {
			$type = 'post_tag';
		}

		$pll_type = ( 'post' == $type || pll_is_translated_post_type( $type ) ) ? 'post' : ( 'term' == $type || pll_is_translated_taxonomy( $type ) ? 'term' : false );
		if ( $pll_type && ( $lang = pll_current_language() ) && ( $tr_id = PLL()->model->$pll_type->get_translation( $id, $lang ) ) && PLL()->links->current_user_can_read( $tr_id ) ) {
			$id = $tr_id;
		} elseif ( ! $return_original_if_missing ) {
			return '';
		}

		if ( post_type_exists( $type ) ) {
			$link = get_permalink( $id );
			if ( empty( $text ) ) {
				$text = get_the_title( $id );
			}
		} elseif ( taxonomy_exists( $type ) ) {
			$link = wpcom_vip_get_term_link( $id, $type );
			if ( empty( $text ) && ( $term = get_term( $id, $type ) ) && ! empty( $term ) && ! is_wp_error( $term ) ) {
				$text = $term->name;
			}
		}

		if ( empty( $link ) || is_wp_error( $link ) ) {
			return '';
		}

		if ( ! empty( $args ) ) {
			$link .= ( false === strpos( $link, '?' ) ? '?' : '&' ) . http_build_query( $args );
		}

		if ( ! empty( $anchor ) ) {
			$link .= '#' . $anchor;
		}

		$link = sprintf( '<a href="%s">%s</a>', esc_url( $link ), esc_html( $text ) );

		if ( $echo ) {
			echo $link;
		}

		return $link;
	}
}

if ( ! function_exists( 'icl_object_id' ) ) {
	/**
	 * Used for calculating the IDs of objects (usually categories) in the current language
	 *
	 * @since 0.9.5
	 *
	 * @param int    $id                         Object id
	 * @param string $type                       Optional, post type or taxonomy name of the object, defaults to 'post'
	 * @param bool   $return_original_if_missing Optional, true if Polylang should return the original id if the translation is missing, defaults to false
	 * @param string $lang                       Optional, language code, defaults to current language
	 * @return int|null The object id of the translation, null if the translation is missing and $return_original_if_missing set to false
	 */
	function icl_object_id( $id, $type = 'post', $return_original_if_missing = false, $lang = false ) {
		$lang = $lang ? $lang : pll_current_language();

		if ( 'nav_menu' === $type ) {
			$theme = get_option( 'stylesheet' );
			if ( isset( PLL()->options['nav_menus'][ $theme ] ) ) {
				foreach ( PLL()->options['nav_menus'][ $theme ] as $loc => $menu ) {
					if ( array_search( $id, $menu ) && ! empty( $menu[ $lang ] ) ) {
						$tr_id = $menu[ $lang ];
						break;
					}
				}
			}
		} elseif ( $pll_type = ( 'post' === $type || pll_is_translated_post_type( $type ) ) ? 'post' : ( 'term' === $type || pll_is_translated_taxonomy( $type ) ? 'term' : false ) ) {
			$tr_id = PLL()->model->$pll_type->get_translation( $id, $lang );
		}

		return ! empty( $tr_id ) ? $tr_id : ( $return_original_if_missing ? $id : null );
	}
}

if ( ! function_exists( 'wpml_object_id_filter' ) ) {
	/**
	 * Undocumented alias of `icl_object_id` introduced in WPML 3.2, used by Yith WooCommerce compare
	 *
	 * @since 2.2.4
	 *
	 * @param int    $id                         object id
	 * @param string $type                       optional, post type or taxonomy name of the object, defaults to 'post'
	 * @param bool   $return_original_if_missing optional, true if Polylang should return the original id if the translation is missing, defaults to false
	 * @param string $lang                       optional, language code, defaults to current language
	 * @return int|null the object id of the translation, null if the translation is missing and $return_original_if_missing set to false
	 */
	function wpml_object_id_filter( $id, $type = 'post', $return_original_if_missing = false, $lang = null ) {
		return icl_object_id( $id, $type, $return_original_if_missing, $lang );
	}
}

if ( ! function_exists( 'wpml_get_language_information' ) ) {
	/**
	 * Undocumented function used by the theme Maya
	 * returns the post language
	 * @see original WPML code at https://wpml.org/forums/topic/canonical-urls-for-wpml-duplicated-posts/#post-52198
	 *
	 * @since 1.8
	 *
	 * @param null $empty   optional, not used
	 * @param int  $post_id optional, post id, defaults to current post
	 * @return array
	 */
	function wpml_get_language_information( $empty = null, $post_id = null ) {
		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}

		// FIXME WPML may return a WP_Error object
		return false === ( $lang = PLL()->model->post->get_language( $post_id ) ) ? array() : array(
			'language_code'      => $lang->slug,
			'locale'             => $lang->locale,
			'text_direction'     => (bool) $lang->is_rtl,
			'display_name'       => $lang->name, // Seems to be the post language name displayed in the current language, not a feature in Polylang
			'native_name'        => $lang->name,
			'different_language' => pll_current_language() !== $lang->slug,
		);
	}
}

if ( ! function_exists( 'icl_register_string' ) ) {
	/**
	 * Registers a string for translation in the "strings translation" panel
	 *
	 * @since 0.9.3
	 *
	 * @param string $context           the group in which the string is registered, defaults to 'polylang'
	 * @param string $name              a unique name for the string
	 * @param string $string            the string to register
	 * @param bool   $allow_empty_value not used
	 * @param string $source_lang       not used by Polylang
	 */
	function icl_register_string( $context, $name, $string, $allow_empty_value = false, $source_lang = '' ) {
		PLL_WPML_Compat::instance()->register_string( $context, $name, $string );
	}
}

if ( ! function_exists( 'icl_unregister_string' ) ) {
	/**
	 * Removes a string from the "strings translation" panel
	 *
	 * @since 1.0.2
	 *
	 * @param string $context the group in which the string is registered, defaults to 'polylang'
	 * @param string $name    a unique name for the string
	 */
	function icl_unregister_string( $context, $name ) {
		PLL_WPML_Compat::instance()->unregister_string( $context, $name );
	}
}

if ( ! function_exists( 'icl_t' ) ) {
	/**
	 * Gets the translated value of a string ( previously registered with icl_register_string or pll_register_string )
	 *
	 * @since 0.9.3
	 * @since 1.9.2 argument 3 is optional
	 * @since 2.0   add support for arguments 4 to 6
	 *
	 * @param string      $context         the group in which the string is registered
	 * @param string      $name            a unique name for the string
	 * @param string      $string          the string to translate, optional for strings registered with icl_register_string
	 * @param bool|null   $has_translation optional, not supported in Polylang
	 * @param bool        $bool            optional, not used
	 * @param string|null $lang            optional, return the translation in this language, defaults to current language
	 * @return string the translated string
	 */
	function icl_t( $context, $name, $string = false, &$has_translation = null, $bool = false, $lang = null ) {
		return icl_translate( $context, $name, $string, false, $has_translation, $lang );
	}
}

if ( ! function_exists( 'icl_translate' ) ) {
	/**
	 * Undocumented function used by NextGen Gallery
	 * used in PLL_Plugins_Compat for Jetpack with only 3 arguments
	 *
	 * @since 1.0.2
	 * @since 2.0   add support for arguments 5 and 6, strings are no more automatically registered
	 *
	 * @param string      $context         the group in which the string is registered
	 * @param string      $name            a unique name for the string
	 * @param string      $string          the string to translate, optional for strings registered with icl_register_string
	 * @param bool        $bool            optional, not used
	 * @param bool|null   $has_translation optional, not supported in Polylang
	 * @param string|null $lang            optional, return the translation in this language, defaults to current language
	 * @return string the translated string
	 */
	function icl_translate( $context, $name, $string = false, $bool = false, &$has_translation = null, $lang = null ) {
		// FIXME WPML can automatically registers the string based on an option
		if ( empty( $string ) ) {
			$string = PLL_WPML_Compat::instance()->get_string_by_context_and_name( $context, $name );
		}
		return empty( $lang ) ? pll__( $string ) : pll_translate_string( $string, $lang );
	}
}

if ( ! function_exists( 'wpml_get_copied_fields_for_post_edit' ) ) {
	/**
	 * Undocumented function used by Types
	 * FIXME: tested only with Types
	 * probably incomplete as Types locks the custom fields for a new post, but not when edited
	 * This is probably linked to the fact that WPML has always an original post in the default language and not Polylang :)
	 *
	 * @since 1.1.2
	 *
	 * @return array
	 */
	function wpml_get_copied_fields_for_post_edit() {
		if ( empty( $_GET['from_post'] ) ) {
			return array();
		}

		// Don't know what WPML does but Polylang does copy all public meta keys by default
		foreach ( $keys = array_unique( array_keys( get_post_custom( (int) $_GET['from_post'] ) ) ) as $k => $meta_key ) {
			if ( is_protected_meta( $meta_key ) ) {
				unset( $keys[ $k ] );
			}
		}

		// Apply our filter and fill the expected output ( see /types/embedded/includes/fields-post.php )
		/** This filter is documented in modules/sync/admin-sync.php */
		$arr['fields'] = array_unique( apply_filters( 'pll_copy_post_metas', empty( $keys ) ? array() : $keys, false ) );
		$arr['original_post_id'] = (int) $_GET['from_post'];
		return $arr;
	}
}

if ( ! function_exists( 'icl_get_default_language' ) ) {
	/**
	 * Undocumented function used by Warp 6 by Yootheme
	 *
	 * @since 1.0.5
	 *
	 * @return string default language code
	 */
	function icl_get_default_language() {
		return pll_default_language();
	}
}

if ( ! function_exists( 'wpml_get_default_language' ) ) {
	/**
	 * Undocumented function reported to be used by Table Rate Shipping for WooCommerce
	 * @see https://wordpress.org/support/topic/add-wpml-compatibility-function
	 *
	 * @since 1.8.2
	 *
	 * @return string default language code
	 */
	function wpml_get_default_language() {
		return pll_default_language();
	}
}

if ( ! function_exists( 'icl_get_current_language' ) ) {
	/**
	 * Undocumented function used by Ultimate Member
	 *
	 * @since 2.2.4
	 *
	 * @return string Current language code
	 */
	function icl_get_current_language() {
		return pll_current_language();
	}
}
