<?php

/**
 * A class to display a language switcher on frontend
 *
 * @since 1.2
 */
class PLL_Switcher {

	/**
	 * Returns options available for the language switcher - menu or widget
	 * either strings to display the options or default values
	 *
	 * @since 0.7
	 *
	 * @param string $type optional either 'menu' or 'widget', defaults to 'widget'
	 * @param string $key  optional either 'string' or 'default', defaults to 'string'
	 * @return array list of switcher options strings or default values
	 */
	static public function get_switcher_options( $type = 'widget', $key = 'string' ) {
		$options = array(
			'dropdown'               => array( 'string' => __( 'Displays as dropdown', 'polylang' ), 'default' => 0 ),
			'show_names'             => array( 'string' => __( 'Displays language names', 'polylang' ), 'default' => 1 ),
			'show_flags'             => array( 'string' => __( 'Displays flags', 'polylang' ), 'default' => 0 ),
			'force_home'             => array( 'string' => __( 'Forces link to front page', 'polylang' ), 'default' => 0 ),
			'hide_current'           => array( 'string' => __( 'Hides the current language', 'polylang' ), 'default' => 0 ),
			'hide_if_no_translation' => array( 'string' => __( 'Hides languages with no translation', 'polylang' ), 'default' => 0 ),
		);

		return wp_list_pluck( $options, $key );
	}

	/**
	 * Get the language elements for use in a walker
	 *
	 * List of parameters accepted in $args:
	 * @see PLL_Switcher::the_languages
	 *
	 * @since 1.2
	 *
	 * @param object $links instance of PLL_Frontend_Links
	 * @param array  $args
	 * @return array
	 */
	protected function get_elements( $links, $args ) {

		$first = true;

		foreach ( $links->model->get_languages_list( array( 'hide_empty' => $args['hide_if_empty'] ) ) as $language ) {
			$id = (int) $language->term_id;
			$order = (int) $language->term_group;
			$slug = $language->slug;
			$locale = $language->get_locale( 'display' );
			$classes = array( 'lang-item', 'lang-item-' . $id, 'lang-item-' . esc_attr( $slug ) );
			$url = null; // Avoids potential notice

			if ( $first ) {
				$classes[] = 'lang-item-first';
				$first = false;
			}

			if ( $current_lang = $links->curlang->slug == $slug ) {
				if ( $args['hide_current'] && ! ( $args['dropdown'] && ! $args['raw'] ) ) {
					continue; // Hide current language except for dropdown
				}
				else {
					$classes[] = 'current-lang';
				}
			}

			if ( null !== $args['post_id'] && ( $tr_id = $links->model->post->get( $args['post_id'], $language ) ) && $links->current_user_can_read( $tr_id ) ) {
				$url = get_permalink( $tr_id );
			} elseif ( null === $args['post_id'] ) {
				$url = $links->get_translation_url( $language );
			}

			if ( $no_translation = empty( $url ) ) {
				$classes[] = 'no-translation';
			}

			/**
			 * Filter the link in the language switcher
			 *
			 * @since 0.7
			 *
			 * @param string $url    the link
			 * @param string $slug   language code
			 * @param string $locale language locale
			 */
			$url = apply_filters( 'pll_the_language_link', $url, $slug, $language->locale );

			// Hide if no translation exists
			if ( empty( $url ) && $args['hide_if_no_translation'] ) {
				continue;
			}

			$url = empty( $url ) || $args['force_home'] ? $links->get_home_url( $language ) : $url; // If the page is not translated, link to the home page

			$name = $args['show_names'] || ! $args['show_flags'] || $args['raw'] ? ( 'slug' == $args['display_names_as'] ? $slug : $language->name ) : '';
			$flag = $args['raw'] && ! $args['show_flags'] ? $language->flag_url : ( $args['show_flags'] ? $language->flag : '' );

			$out[ $slug ] = compact( 'id', 'order', 'slug', 'locale', 'name', 'url', 'flag', 'current_lang', 'no_translation', 'classes' );
		}

		return empty( $out ) ? array() : $out;
	}

	/**
	 * Displays a language switcher
	 * or returns the raw elements to build a custom language switcher
	 *
	 * List of parameters accepted in $args:
	 *
	 * dropdown               => the list is displayed as dropdown if set, defaults to 0
	 * echo                   => echoes the list if set to 1, defaults to 1
	 * hide_if_empty          => hides languages with no posts ( or pages ) if set to 1, defaults to 1
	 * show_flags             => displays flags if set to 1, defaults to 0
	 * show_names             => show language names if set to 1, defaults to 1
	 * display_names_as       => whether to display the language name or its slug, valid options are 'slug' and 'name', defaults to name
	 * force_home             => will always link to home in translated language if set to 1, defaults to 0
	 * hide_if_no_translation => hide the link if there is no translation if set to 1, defaults to 0
	 * hide_current           => hide the current language if set to 1, defaults to 0
	 * post_id                => returns links to translations of post defined by post_id if set, defaults not set
	 * raw                    => return a raw array instead of html markup if set to 1, defaults to 0
	 * item_spacing           => whether to preserve or discard whitespace between list items, valid options are 'preserve' and 'discard', defaults to preserve
	 *
	 * @since 0.1
	 *
	 * @param object $links instance of PLL_Frontend_Links
	 * @param array  $args
	 * @return string|array either the html markup of the switcher or the raw elements to build a custom language switcher
	 */
	public function the_languages( $links, $args = '' ) {
		$defaults = array(
			'dropdown'               => 0, // display as list and not as dropdown
			'echo'                   => 1, // echoes the list
			'hide_if_empty'          => 1, // hides languages with no posts ( or pages )
			'menu'                   => 0, // not for nav menu ( this argument is deprecated since v1.1.1 )
			'show_flags'             => 0, // don't show flags
			'show_names'             => 1, // show language names
			'display_names_as'       => 'name', // valid options are slug and name
			'force_home'             => 0, // tries to find a translation
			'hide_if_no_translation' => 0, // don't hide the link if there is no translation
			'hide_current'           => 0, // don't hide current language
			'post_id'                => null, // if not null, link to translations of post defined by post_id
			'raw'                    => 0, // set this to true to build your own custom language switcher
			'item_spacing'           => 'preserve', // 'preserve' or 'discard' whitespace between list items
		);
		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filter the arguments of the 'pll_the_languages' template tag
		 *
		 * @since 1.5
		 *
		 * @param array $args
		 */
		$args = apply_filters( 'pll_the_languages_args', $args );

		// Prevents showing empty options in dropdown
		if ( $args['dropdown'] ) {
			$args['show_names'] = 1;
		}

		$elements = $this->get_elements( $links, $args );

		if ( $args['raw'] ) {
			return $elements;
		}

		if ( $args['dropdown'] ) {
			$args['name'] = 'lang_choice_' . $args['dropdown'];
			$walker = new PLL_Walker_Dropdown();
			$args['selected'] = $links->curlang->slug;
		}
		else {
			$walker = new PLL_Walker_List();
		}

		/**
		 * Filter the whole html markup returned by the 'pll_the_languages' template tag
		 *
		 * @since 0.8
		 *
		 * @param string $html html returned/outputted by the template tag
		 * @param array  $args arguments passed to the template tag
		 */
		$out = apply_filters( 'pll_the_languages', $walker->walk( $elements, $args ), $args );

		// Javascript to switch the language when using a dropdown list
		if ( $args['dropdown'] ) {
			foreach ( $links->model->get_languages_list() as $language ) {
				$url = $links->get_translation_url( $language );
				$urls[ $language->slug ] = $args['force_home'] || empty( $url ) ? $links->get_home_url( $language ) : $url;
			}

			// Accept only few valid characters for the urls_x variable name ( as the widget id includes '-' which is invalid )
			$out .= sprintf( '
				<script type="text/javascript">
					//<![CDATA[
					var %1$s = %2$s;
					document.getElementById( "%3$s" ).onchange = function() {
						location.href = %1$s[this.value];
					}
					//]]>
				</script>',
				'urls_' . preg_replace( '#[^a-zA-Z0-9]#', '', $args['dropdown'] ), json_encode( $urls ), esc_js( $args['name'] )
			);
		}

		if ( $args['echo'] ) {
			echo $out;
		}
		return $out;
	}
}
