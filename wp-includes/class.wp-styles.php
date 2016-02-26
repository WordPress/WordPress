<?php
/**
 * BackPress Styles enqueue.
 *
 * These classes were refactored from the WordPress WP_Scripts and WordPress
 * script enqueue API.
 *
 * @package BackPress
 * @since r74
 */

/**
 * BackPress Styles enqueue class.
 *
 * @package BackPress
 * @uses WP_Dependencies
 * @since r74
 */
class WP_Styles extends WP_Dependencies {
	/**
	 * Base URL for styles.
	 *
	 * Full URL with trailing slash.
	 *
	 * @since 2.6.0
	 * @access public
	 * @var string
	 */
	public $base_url;

	/**
	 *
	 * @since 2.8.0
	 * @access public
	 * @var string
	 */
	public $content_url;

	/**
	 *
	 * @since 2.6.0
	 * @access public
	 * @var string
	 */
	public $default_version;

	/**
	 *
	 * @since 2.6.0
	 * @access public
	 * @var string
	 */
	public $text_direction = 'ltr';

	/**
	 *
	 * @since 2.8.0
	 * @access public
	 * @var string
	 */
	public $concat = '';

	/**
	 *
	 * @since 2.8.0
	 * @access public
	 * @var string
	 */
	public $concat_version = '';

	/**
	 *
	 * @since 2.8.0
	 * @access public
	 * @var bool
	 */
	public $do_concat = false;

	/**
	 *
	 * @since 2.8.0
	 * @access public
	 * @var string
	 */
	public $print_html = '';

	/**
	 *
	 * @since 3.3.0
	 * @access public
	 * @var string
	 */
	public $print_code = '';

	/**
	 *
	 * @since 2.8.0
	 * @access public
	 * @var array
	 */
	public $default_dirs;

	/**
	 * Constructor.
	 *
	 * @since 2.6.0
	 * @access public
	 */
	public function __construct() {
		/**
		 * Fires when the WP_Styles instance is initialized.
		 *
		 * @since 2.6.0
		 *
		 * @param WP_Styles &$this WP_Styles instance, passed by reference.
		 */
		do_action_ref_array( 'wp_default_styles', array(&$this) );
	}

	/**
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param string $handle
	 * @return bool
	 */
	public function do_item( $handle ) {
		if ( !parent::do_item($handle) )
			return false;

		$obj = $this->registered[$handle];
		if ( null === $obj->ver )
			$ver = '';
		else
			$ver = $obj->ver ? $obj->ver : $this->default_version;

		if ( isset($this->args[$handle]) )
			$ver = $ver ? $ver . '&amp;' . $this->args[$handle] : $this->args[$handle];

		if ( $this->do_concat ) {
			if ( $this->in_default_dir($obj->src) && !isset($obj->extra['conditional']) && !isset($obj->extra['alt']) ) {
				$this->concat .= "$handle,";
				$this->concat_version .= "$handle$ver";

				$this->print_code .= $this->print_inline_style( $handle, false );

				return true;
			}
		}

		if ( isset($obj->args) )
			$media = esc_attr( $obj->args );
		else
			$media = 'all';

		// A single item may alias a set of items, by having dependencies, but no source.
		if ( ! $obj->src ) {
			if ( $inline_style = $this->print_inline_style( $handle, false ) ) {
				$inline_style = sprintf( "<style id='%s-inline-css' type='text/css'>\n%s\n</style>\n", esc_attr( $handle ), $inline_style );
				if ( $this->do_concat ) {
					$this->print_html .= $inline_style;
				} else {
					echo $inline_style;
				}
			}
			return true;
		}

		$href = $this->_css_href( $obj->src, $ver, $handle );
		if ( ! $href ) {
			return true;
		}

		$rel = isset($obj->extra['alt']) && $obj->extra['alt'] ? 'alternate stylesheet' : 'stylesheet';
		$title = isset($obj->extra['title']) ? "title='" . esc_attr( $obj->extra['title'] ) . "'" : '';

		/**
		 * Filter the HTML link tag of an enqueued style.
		 *
		 * @since 2.6.0
		 * @since 4.3.0 Introduced the `$href` parameter.
		 * @since 4.5.0 Introduced the `$media` parameter.
		 *
		 * @param string $html   The link tag for the enqueued style.
		 * @param string $handle The style's registered handle.
		 * @param string $href   The stylesheet's source URL.
		 * @param string $media  The stylesheet's media attribute.
		 */
		$tag = apply_filters( 'style_loader_tag', "<link rel='$rel' id='$handle-css' $title href='$href' type='text/css' media='$media' />\n", $handle, $href, $media);
		if ( 'rtl' === $this->text_direction && isset($obj->extra['rtl']) && $obj->extra['rtl'] ) {
			if ( is_bool( $obj->extra['rtl'] ) || 'replace' === $obj->extra['rtl'] ) {
				$suffix = isset( $obj->extra['suffix'] ) ? $obj->extra['suffix'] : '';
				$rtl_href = str_replace( "{$suffix}.css", "-rtl{$suffix}.css", $this->_css_href( $obj->src , $ver, "$handle-rtl" ));
			} else {
				$rtl_href = $this->_css_href( $obj->extra['rtl'], $ver, "$handle-rtl" );
			}

			/** This filter is documented in wp-includes/class.wp-styles.php */
			$rtl_tag = apply_filters( 'style_loader_tag', "<link rel='$rel' id='$handle-rtl-css' $title href='$rtl_href' type='text/css' media='$media' />\n", $handle, $rtl_href, $media );

			if ( $obj->extra['rtl'] === 'replace' ) {
				$tag = $rtl_tag;
			} else {
				$tag .= $rtl_tag;
			}
		}

		$conditional_pre = $conditional_post = '';
		if ( isset( $obj->extra['conditional'] ) && $obj->extra['conditional'] ) {
			$conditional_pre  = "<!--[if {$obj->extra['conditional']}]>\n";
			$conditional_post = "<![endif]-->\n";
		}

		if ( $this->do_concat ) {
			$this->print_html .= $conditional_pre;
			$this->print_html .= $tag;
			if ( $inline_style = $this->print_inline_style( $handle, false ) ) {
				$this->print_html .= sprintf( "<style id='%s-inline-css' type='text/css'>\n%s\n</style>\n", esc_attr( $handle ), $inline_style );
			}
			$this->print_html .= $conditional_post;
		} else {
			echo $conditional_pre;
			echo $tag;
			$this->print_inline_style( $handle );
			echo $conditional_post;
		}

		return true;
	}

	/**
	 *
	 * @since 3.3.0
	 * @access public
	 *
	 * @param string $handle
	 * @param string $code
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
	 *
	 * @since 3.3.0
	 * @access public
	 *
	 * @param string $handle
	 * @param bool $echo
	 * @return bool
	 */
	public function print_inline_style( $handle, $echo = true ) {
		$output = $this->get_data( $handle, 'after' );

		if ( empty( $output ) ) {
			return false;
		}

		$output = implode( "\n", $output );

		if ( ! $echo ) {
			return $output;
		}

		printf( "<style id='%s-inline-css' type='text/css'>\n%s\n</style>\n", esc_attr( $handle ), $output );

		return true;
	}

	/**
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param mixed $handles
	 * @param bool $recursion
	 * @param mixed $group
	 * @return bool
	 */
	public function all_deps( $handles, $recursion = false, $group = false ) {
		$r = parent::all_deps( $handles, $recursion );
		if ( !$recursion ) {
			/**
			 * Filter the array of enqueued styles before processing for output.
			 *
			 * @since 2.6.0
			 *
			 * @param array $to_do The list of enqueued styles about to be processed.
			 */
			$this->to_do = apply_filters( 'print_styles_array', $this->to_do );
		}
		return $r;
	}

	/**
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param string $src
	 * @param string $ver
	 * @param string $handle
	 * @return string
	 */
	public function _css_href( $src, $ver, $handle ) {
		if ( !is_bool($src) && !preg_match('|^(https?:)?//|', $src) && ! ( $this->content_url && 0 === strpos($src, $this->content_url) ) ) {
			$src = $this->base_url . $src;
		}

		if ( !empty($ver) )
			$src = add_query_arg('ver', $ver, $src);

		/**
		 * Filter an enqueued style's fully-qualified URL.
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
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param string $src
	 * @return bool
	 */
	public function in_default_dir( $src ) {
		if ( ! $this->default_dirs )
			return true;

		foreach ( (array) $this->default_dirs as $test ) {
			if ( 0 === strpos($src, $test) )
				return true;
		}
		return false;
	}

	/**
	 *
	 * HTML 5 allows styles in the body, grab late enqueued items and output them in the footer.
	 *
	 * @since 3.3.0
	 * @access public
	 *
	 * @return array
	 */
	public function do_footer_items() {
		$this->do_items(false, 1);
		return $this->done;
	}

	/**
	 *
	 * @since 3.3.0
	 * @access public
	 */
	public function reset() {
		$this->do_concat = false;
		$this->concat = '';
		$this->concat_version = '';
		$this->print_html = '';
	}
}
