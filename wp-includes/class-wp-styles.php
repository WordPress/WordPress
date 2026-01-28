<?php
/**
 * Dependencies API: WP_Styles class
 *
 * @since 2.6.0
 *
 * @package WordPress
 * @subpackage Dependencies
 */

/**
 * Core class used to register styles.
 *
 * @since 2.6.0
 *
 * @see WP_Dependencies
 */
class WP_Styles extends WP_Dependencies {
	/**
	 * Base URL for styles.
	 *
	 * Full URL with trailing slash.
	 *
	 * @since 2.6.0
	 * @see wp_default_styles()
	 * @var string|null
	 */
	public $base_url;

	/**
	 * URL of the content directory.
	 *
	 * @since 2.8.0
	 * @see wp_default_styles()
	 * @var string|null
	 */
	public $content_url;

	/**
	 * Default version string for stylesheets.
	 *
	 * @since 2.6.0
	 * @see wp_default_styles()
	 * @var string|null
	 */
	public $default_version;

	/**
	 * The current text direction.
	 *
	 * @since 2.6.0
	 * @see wp_default_styles()
	 * @var string
	 */
	public $text_direction = 'ltr';

	/**
	 * Holds a list of style handles which will be concatenated.
	 *
	 * @since 2.8.0
	 * @var string
	 */
	public $concat = '';

	/**
	 * Holds a string which contains style handles and their version.
	 *
	 * @since 2.8.0
	 * @deprecated 3.4.0
	 * @var string
	 */
	public $concat_version = '';

	/**
	 * Whether to perform concatenation.
	 *
	 * @since 2.8.0
	 * @var bool
	 */
	public $do_concat = false;

	/**
	 * Holds HTML markup of styles and additional data if concatenation
	 * is enabled.
	 *
	 * @since 2.8.0
	 * @var string
	 */
	public $print_html = '';

	/**
	 * Holds inline styles if concatenation is enabled.
	 *
	 * @since 3.3.0
	 * @var string
	 */
	public $print_code = '';

	/**
	 * List of default directories.
	 *
	 * @since 2.8.0
	 * @see wp_default_styles()
	 * @var string[]|null
	 */
	public $default_dirs;

	/**
	 * Constructor.
	 *
	 * @since 2.6.0
	 */
	public function __construct() {
		/**
		 * Fires when the WP_Styles instance is initialized.
		 *
		 * @since 2.6.0
		 *
		 * @param WP_Styles $wp_styles WP_Styles instance (passed by reference).
		 */
		do_action_ref_array( 'wp_default_styles', array( &$this ) );
	}

	/**
	 * Processes a style dependency.
	 *
	 * @since 2.6.0
	 * @since 5.5.0 Added the `$group` parameter.
	 *
	 * @see WP_Dependencies::do_item()
	 *
	 * @param string    $handle The style's registered handle.
	 * @param int|false $group  Optional. Group level: level (int), no groups (false).
	 *                          Default false.
	 * @return bool True on success, false on failure.
	 */
	public function do_item( $handle, $group = false ) {
		if ( ! parent::do_item( $handle ) ) {
			return false;
		}

		$obj = $this->registered[ $handle ];
		if ( $obj->extra['conditional'] ?? false ) {

			return false;
		}
		if ( null === $obj->ver ) {
			$ver = '';
		} else {
			$ver = $obj->ver ? $obj->ver : $this->default_version;
		}

		if ( isset( $this->args[ $handle ] ) ) {
			$ver = $ver ? $ver . '&amp;' . $this->args[ $handle ] : $this->args[ $handle ];
		}

		$src          = $obj->src;
		$inline_style = $this->print_inline_style( $handle, false );

		if ( $inline_style ) {
			$processor = new WP_HTML_Tag_Processor( '<style></style>' );
			$processor->next_tag();
			$processor->set_attribute( 'id', "{$handle}-inline-css" );
			$processor->set_modifiable_text( "\n{$inline_style}\n" );
			$inline_style_tag = "{$processor->get_updated_html()}\n";
		} else {
			$inline_style_tag = '';
		}

		if ( $this->do_concat ) {
			if ( is_string( $src ) && $this->in_default_dir( $src ) && ! isset( $obj->extra['alt'] ) ) {
				$this->concat         .= "$handle,";
				$this->concat_version .= "$handle$ver";

				$this->print_code .= $inline_style;

				return true;
			}
		}

		$media = $obj->args ?? 'all';

		// A single item may alias a set of items, by having dependencies, but no source.
		if ( ! $src ) {
			if ( $inline_style_tag ) {
				if ( $this->do_concat ) {
					$this->print_html .= $inline_style_tag;
				} else {
					echo $inline_style_tag;
				}
			}

			return true;
		}

		$href = $this->_css_href( $src, $obj->ver, $handle );
		if ( ! $href ) {
			return true;
		}

		$rel   = isset( $obj->extra['alt'] ) && $obj->extra['alt'] ? 'alternate stylesheet' : 'stylesheet';
		$title = $obj->extra['title'] ?? '';

		$tag = sprintf(
			"<link rel='%s' id='%s-css'%s href='%s' media='%s' />\n",
			$rel,
			esc_attr( $handle ),
			$title ? sprintf( " title='%s'", esc_attr( $title ) ) : '',
			$href,
			esc_attr( $media )
		);

		/**
		 * Filters the HTML link tag of an enqueued style.
		 *
		 * @since 2.6.0
		 * @since 4.3.0 Introduced the `$href` parameter.
		 * @since 4.5.0 Introduced the `$media` parameter.
		 *
		 * @param string $tag    The link tag for the enqueued style.
		 * @param string $handle The style's registered handle.
		 * @param string $href   The stylesheet's source URL.
		 * @param string $media  The stylesheet's media attribute.
		 */
		$tag = apply_filters( 'style_loader_tag', $tag, $handle, $href, $media );

		if ( 'rtl' === $this->text_direction && isset( $obj->extra['rtl'] ) && $obj->extra['rtl'] ) {
			if ( is_bool( $obj->extra['rtl'] ) || 'replace' === $obj->extra['rtl'] ) {
				$suffix   = $obj->extra['suffix'] ?? '';
				$rtl_href = str_replace( "{$suffix}.css", "-rtl{$suffix}.css", $this->_css_href( $src, $ver, "$handle-rtl" ) );
			} else {
				$rtl_href = $this->_css_href( $obj->extra['rtl'], $ver, "$handle-rtl" );
			}

			$rtl_tag = sprintf(
				"<link rel='%s' id='%s-rtl-css'%s href='%s' media='%s' />\n",
				$rel,
				esc_attr( $handle ),
				$title ? sprintf( " title='%s'", esc_attr( $title ) ) : '',
				$rtl_href,
				esc_attr( $media )
			);

			/** This filter is documented in wp-includes/class-wp-styles.php */
			$rtl_tag = apply_filters( 'style_loader_tag', $rtl_tag, $handle, $rtl_href, $media );

			if ( 'replace' === $obj->extra['rtl'] ) {
				$tag = $rtl_tag;
			} else {
				$tag .= $rtl_tag;
			}
		}

		if ( $this->do_concat ) {
			$this->print_html .= $tag;
			if ( $inline_style_tag ) {
				$this->print_html .= $inline_style_tag;
			}
		} else {
			echo $tag;
			$this->print_inline_style( $handle );
		}

		return true;
	}

	/**
	 * Adds extra CSS styles to a registered stylesheet.
	 *
	 * @since 3.3.0
	 *
	 * @param string $handle The style's registered handle.
	 * @param string $code   String containing the CSS styles to be added.
	 * @return bool True on success, false on failure.
	 */
	public function add_inline_style( $handle, $code ) {
		if ( ! $code ) {
			return false;
		}

		$after = $this->get_data( $handle, 'after' );
		if ( ! $after ) {
			$after = array();
		}

		$after[] = $code;

		return $this->add_data( $handle, 'after', $after );
	}

	/**
	 * Prints extra CSS styles of a registered stylesheet.
	 *
	 * @since 3.3.0
	 *
	 * @param string $handle  The style's registered handle.
	 * @param bool   $display Optional. Whether to print the inline style
	 *                        instead of just returning it. Default true.
	 * @return string|bool False if no data exists, inline styles if `$display` is true,
	 *                     true otherwise.
	 */
	public function print_inline_style( $handle, $display = true ) {
		$output = $this->get_data( $handle, 'after' );

		if ( empty( $output ) || ! is_array( $output ) ) {
			return false;
		}

		if ( ! $this->do_concat ) {

			// Obtain the original `src` for a stylesheet possibly inlined by wp_maybe_inline_styles().
			$inlined_src = $this->get_data( $handle, 'inlined_src' );

			// If there's only one `after` inline style, and that inline style had been inlined, then use the $inlined_src
			// as the sourceURL. Otherwise, if there is more than one inline `after` style associated with the handle,
			// then resort to using the handle to construct the sourceURL since there isn't a single source.
			if ( count( $output ) === 1 && is_string( $inlined_src ) && strlen( $inlined_src ) > 0 ) {
				$source_url = esc_url_raw( $inlined_src );
			} else {
				$source_url = rawurlencode( "{$handle}-inline-css" );
			}

			$output[] = sprintf(
				'/*# sourceURL=%s */',
				$source_url
			);
		}

		$output = implode( "\n", $output );

		if ( ! $display ) {
			return $output;
		}

		$processor = new WP_HTML_Tag_Processor( '<style></style>' );
		$processor->next_tag();
		$processor->set_attribute( 'id', "{$handle}-inline-css" );
		$processor->set_modifiable_text( "\n{$output}\n" );
		echo "{$processor->get_updated_html()}\n";

		return true;
	}

	/**
	 * Overrides the add_data method from WP_Dependencies, to allow unsetting dependencies for conditional styles.
	 *
	 * @since 6.9.0
	 *
	 * @param string $handle Name of the item. Should be unique.
	 * @param string $key    The data key.
	 * @param mixed  $value  The data value.
	 * @return bool True on success, false on failure.
	 */
	public function add_data( $handle, $key, $value ) {
		if ( ! isset( $this->registered[ $handle ] ) ) {
			return false;
		}

		if ( 'conditional' === $key ) {
			$this->registered[ $handle ]->deps = array();
		}

		return parent::add_data( $handle, $key, $value );
	}

	/**
	 * Determines style dependencies.
	 *
	 * @since 2.6.0
	 *
	 * @see WP_Dependencies::all_deps()
	 *
	 * @param string|string[] $handles   Item handle (string) or item handles (array of strings).
	 * @param bool            $recursion Optional. Internal flag that function is calling itself.
	 *                                   Default false.
	 * @param int|false       $group     Optional. Group level: level (int), no groups (false).
	 *                                   Default false.
	 * @return bool True on success, false on failure.
	 */
	public function all_deps( $handles, $recursion = false, $group = false ) {
		$result = parent::all_deps( $handles, $recursion, $group );
		if ( ! $recursion ) {
			/**
			 * Filters the array of enqueued styles before processing for output.
			 *
			 * @since 2.6.0
			 *
			 * @param string[] $to_do The list of enqueued style handles about to be processed.
			 */
			$this->to_do = apply_filters( 'print_styles_array', $this->to_do );
		}
		return $result;
	}

	/**
	 * Generates an enqueued style's fully-qualified URL.
	 *
	 * @since 2.6.0
	 *
	 * @param string            $src    The source of the enqueued style.
	 * @param string|false|null $ver    The version of the enqueued style.
	 * @param string            $handle The style's registered handle.
	 * @return string Style's fully-qualified URL.
	 */
	public function _css_href( $src, $ver, $handle ) {
		if ( ! is_bool( $src ) && ! preg_match( '|^(https?:)?//|', $src ) && ! ( $this->content_url && str_starts_with( $src, $this->content_url ) ) ) {
			$src = $this->base_url . $src;
		}

		$query_args = array();
		if ( empty( $ver ) && null !== $ver && is_string( $this->default_version ) ) {
			$query_args['ver'] = $this->default_version;
		} elseif ( is_scalar( $ver ) ) {
			$query_args['ver'] = (string) $ver;
		}
		if ( isset( $this->args[ $handle ] ) ) {
			parse_str( $this->args[ $handle ], $parsed_args );
			if ( $parsed_args ) {
				$query_args = array_merge( $query_args, $parsed_args );
			}
		}
		$src = add_query_arg( rawurlencode_deep( $query_args ), $src );

		/**
		 * Filters an enqueued style's fully-qualified URL.
		 *
		 * @since 2.6.0
		 *
		 * @param string $src    The source URL of the enqueued style.
		 * @param string $handle The style's registered handle.
		 */
		$src = apply_filters( 'style_loader_src', $src, $handle );
		return esc_url( $src );
	}

	/**
	 * Whether a handle's source is in a default directory.
	 *
	 * @since 2.8.0
	 *
	 * @param string $src The source of the enqueued style.
	 * @return bool True if found, false if not.
	 */
	public function in_default_dir( $src ) {
		if ( ! $this->default_dirs ) {
			return true;
		}

		foreach ( (array) $this->default_dirs as $test ) {
			if ( str_starts_with( $src, $test ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Processes items and dependencies for the footer group.
	 *
	 * HTML 5 allows styles in the body, grab late enqueued items and output them in the footer.
	 *
	 * @since 3.3.0
	 *
	 * @see WP_Dependencies::do_items()
	 *
	 * @return string[] Handles of items that have been processed.
	 */
	public function do_footer_items() {
		$this->do_items( false, 1 );
		return $this->done;
	}

	/**
	 * Resets class properties.
	 *
	 * @since 3.3.0
	 */
	public function reset() {
		$this->do_concat      = false;
		$this->concat         = '';
		$this->concat_version = '';
		$this->print_html     = '';
	}

	/**
	 * Gets a style-specific dependency warning message.
	 *
	 * @since 6.9.1
	 *
	 * @param string   $handle                     Style handle with missing dependencies.
	 * @param string[] $missing_dependency_handles Missing dependency handles.
	 * @return string Formatted, localized warning message.
	 */
	protected function get_dependency_warning_message( $handle, $missing_dependency_handles ) {
		return sprintf(
			/* translators: 1: Style handle, 2: List of missing dependency handles. */
			__( 'The style with the handle "%1$s" was enqueued with dependencies that are not registered: %2$s.' ),
			$handle,
			implode( wp_get_list_item_separator(), $missing_dependency_handles )
		);
	}
}
