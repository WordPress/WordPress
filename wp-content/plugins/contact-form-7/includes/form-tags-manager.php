<?php

/**
 * Wrapper function of WPCF7_FormTagsManager::add().
 */
function wpcf7_add_form_tag( $tag_types, $callback, $features = '' ) {
	$manager = WPCF7_FormTagsManager::get_instance();

	return $manager->add( $tag_types, $callback, $features );
}


/**
 * Wrapper function of WPCF7_FormTagsManager::remove().
 */
function wpcf7_remove_form_tag( $tag_type ) {
	$manager = WPCF7_FormTagsManager::get_instance();

	return $manager->remove( $tag_type );
}


/**
 * Wrapper function of WPCF7_FormTagsManager::replace_all().
 */
function wpcf7_replace_all_form_tags( $content ) {
	$manager = WPCF7_FormTagsManager::get_instance();

	return $manager->replace_all( $content );
}


/**
 * Wrapper function of WPCF7_ContactForm::scan_form_tags().
 */
function wpcf7_scan_form_tags( $cond = null ) {
	$contact_form = WPCF7_ContactForm::get_current();

	if ( $contact_form ) {
		return $contact_form->scan_form_tags( $cond );
	}

	return array();
}


/**
 * Wrapper function of WPCF7_FormTagsManager::tag_type_supports().
 */
function wpcf7_form_tag_supports( $tag_type, $feature ) {
	$manager = WPCF7_FormTagsManager::get_instance();

	return $manager->tag_type_supports( $tag_type, $feature );
}


/**
 * The singleton instance of this class manages the collection of form-tags.
 */
class WPCF7_FormTagsManager {

	private static $instance;

	private $tag_types = array();
	private $scanned_tags = null; // Tags scanned at the last time of scan()
	private $placeholders = array();

	private function __construct() {}


	/**
	 * Returns the singleton instance.
	 *
	 * @return WPCF7_FormTagsManager The singleton manager.
	 */
	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Returns scanned form-tags.
	 *
	 * @return array Array of WPCF7_FormTag objects.
	 */
	public function get_scanned_tags() {
		return $this->scanned_tags;
	}


	/**
	 * Registers form-tag types to the manager.
	 *
	 * @param string|array $tag_types The name of the form-tag type or
	 *                     an array of the names.
	 * @param callable $callback The callback to generates a form control HTML
	 *                 for a form-tag in this type.
	 * @param string|array $features Optional. Features a form-tag
	 *                     in this type supports.
	 */
	public function add( $tag_types, $callback, $features = '' ) {
		if ( ! is_callable( $callback ) ) {
			return;
		}

		if ( true === $features ) { // for back-compat
			$features = array( 'name-attr' => true );
		}

		$features = wp_parse_args( $features, array() );

		$tag_types = array_filter( array_unique( (array) $tag_types ) );

		foreach ( $tag_types as $tag_type ) {
			$tag_type = $this->sanitize_tag_type( $tag_type );

			if ( ! $this->tag_type_exists( $tag_type ) ) {
				$this->tag_types[$tag_type] = array(
					'function' => $callback,
					'features' => $features,
				);
			}
		}
	}


	/**
	 * Returns true if the given tag type exists.
	 */
	public function tag_type_exists( $tag_type ) {
		return isset( $this->tag_types[$tag_type] );
	}


	/**
	 * Returns true if the tag type supports the features.
	 *
	 * @param string $tag_type The name of the form-tag type.
	 * @param array|string $features The feature to check or an array of features.
	 * @return bool True if the form-tag type supports at least one of
	 *              the given features, false otherwise.
	 */
	public function tag_type_supports( $tag_type, $features ) {
		$features = array_filter( (array) $features );

		if ( isset( $this->tag_types[$tag_type]['features'] ) ) {
			return (bool) array_intersect(
				array_keys( array_filter( $this->tag_types[$tag_type]['features'] ) ),
				$features
			);
		}

		return false;
	}


	/**
	 * Returns form-tag types that support the given features.
	 *
	 * @param array|string $features Optional. The feature to check or
	 *                     an array of features. Default empty array.
	 * @param bool $invert Optional. If this value is true, returns form-tag
	 *             types that do not support the given features. Default false.
	 * @return array An array of form-tag types. If the $features param is empty,
	 *               returns all form-tag types that have been registered.
	 */
	public function collect_tag_types( $features = array(), $invert = false ) {
		$tag_types = array_keys( $this->tag_types );

		if ( empty( $features ) ) {
			return $tag_types;
		}

		$output = array();

		foreach ( $tag_types as $tag_type ) {
			if (
				! $invert and $this->tag_type_supports( $tag_type, $features ) or
				$invert and ! $this->tag_type_supports( $tag_type, $features )
			) {
				$output[] = $tag_type;
			}
		}

		return $output;
	}


	/**
	 * Sanitizes the form-tag type name.
	 */
	private function sanitize_tag_type( $tag_type ) {
		$tag_type = preg_replace( '/[^a-zA-Z0-9_*]+/', '_', $tag_type );
		$tag_type = rtrim( $tag_type, '_' );
		$tag_type = strtolower( $tag_type );
		return $tag_type;
	}


	/**
	 * Deregisters the form-tag type.
	 */
	public function remove( $tag_type ) {
		unset( $this->tag_types[$tag_type] );
	}


	/**
	 * Normalizes the text content that includes form-tags.
	 */
	public function normalize( $content ) {
		if ( empty( $this->tag_types ) ) {
			return $content;
		}

		$content = preg_replace_callback(
			'/' . $this->tag_regex() . '/su',
			array( $this, 'normalize_callback' ),
			$content
		);

		return $content;
	}


	/**
	 * The callback function used within normalize().
	 */
	private function normalize_callback( $matches ) {
		// allow [[foo]] syntax for escaping a tag
		if ( '[' === $matches[1] and ']' === $matches[6] ) {
			return $matches[0];
		}

		$tag = $matches[2];

		$attr = trim( preg_replace( '/[\r\n\t ]+/', ' ', $matches[3] ) );
		$attr = strtr( $attr, array( '<' => '&lt;', '>' => '&gt;' ) );

		$content = trim( $matches[5] );
		$content = str_replace( "\n", '<WPPreserveNewline />', $content );

		$result = $matches[1] . '[' . $tag
			. ( $attr ? ' ' . $attr : '' )
			. ( $matches[4] ? ' ' . $matches[4] : '' )
			. ']'
			. ( $content ? $content . '[/' . $tag . ']' : '' )
			. $matches[6];

		return $result;
	}


	/**
	 * Replace all form-tags in the given text with placeholders.
	 */
	public function replace_with_placeholders( $content ) {
		if ( empty( $this->tag_types ) ) {
			return $content;
		}

		$this->placeholders = array();

		$callback = function ( $matches ) {
			// Allow [[foo]] syntax for escaping a tag.
			if ( '[' === $matches[1] and ']' === $matches[6] ) {
				return $matches[0];
			}

			$tag = $matches[0];
			$tag_type = $matches[2];

			$block_or_hidden = $this->tag_type_supports(
				$tag_type,
				array( 'display-block', 'display-hidden' )
			);

			if ( $block_or_hidden ) {
				$placeholder_tag_name = WPCF7_HTMLFormatter::placeholder_block;
			} else {
				$placeholder_tag_name = WPCF7_HTMLFormatter::placeholder_inline;
			}

			$placeholder = sprintf(
				'<%1$s id="%2$s" />',
				$placeholder_tag_name,
				hash( 'sha256', $tag )
			);

			list( $placeholder ) =
				WPCF7_HTMLFormatter::normalize_start_tag( $placeholder );

			$this->placeholders[$placeholder] = $tag;

			return $placeholder;
		};

		return preg_replace_callback(
			'/' . $this->tag_regex() . '/su',
			$callback,
			$content
		);
	}


	/**
	 * Replace placeholders in the given text with original form-tags.
	 */
	public function restore_from_placeholders( $content ) {
		return str_replace(
			array_keys( $this->placeholders ),
			array_values( $this->placeholders ),
			$content
		);
	}


	/**
	 * Replaces all form-tags in the text content.
	 *
	 * @param string $content The text content including form-tags.
	 * @return string The result of replacements.
	 */
	public function replace_all( $content ) {
		return $this->scan( $content, true );
	}


	/**
	 * Scans form-tags in the text content.
	 *
	 * @param string $content The text content including form-tags.
	 * @param bool $replace Optional. Whether scanned form-tags will be
	 *             replaced. Default false.
	 * @return array|string An array of scanned form-tags if $replace is false.
	 *                      Otherwise text that scanned form-tags are replaced.
	 */
	public function scan( $content, $replace = false ) {
		$this->scanned_tags = array();

		if ( empty( $this->tag_types ) ) {
			if ( $replace ) {
				return $content;
			} else {
				return $this->scanned_tags;
			}
		}

		if ( $replace ) {
			$content = preg_replace_callback(
				'/' . $this->tag_regex() . '/su',
				array( $this, 'replace_callback' ),
				$content
			);

			return $content;
		} else {
			preg_replace_callback(
				'/' . $this->tag_regex() . '/su',
				array( $this, 'scan_callback' ),
				$content
			);

			return $this->scanned_tags;
		}
	}


	/**
	 * Filters form-tags based on a condition array argument.
	 *
	 * @param array|string $input The original form-tags collection.
	 *                     If it is a string, scans form-tags from it.
	 * @param array $cond The conditions that filtering will be based on.
	 * @return array The filtered form-tags collection.
	 */
	public function filter( $input, $cond ) {
		if ( is_array( $input ) ) {
			$tags = $input;
		} elseif ( is_string( $input ) ) {
			$tags = $this->scan( $input );
		} else {
			$tags = $this->scanned_tags;
		}

		$cond = wp_parse_args( $cond, array(
			'type' => array(),
			'basetype' => array(),
			'name' => array(),
			'feature' => array(),
		) );

		$cond = array_map( static function ( $c ) {
			return array_filter( array_map( 'trim', (array) $c ) );
		}, $cond );

		$tags = array_filter(
			(array) $tags,
			function ( $tag ) use ( $cond ) {
				$tag = new WPCF7_FormTag( $tag );

				if (
					$cond['type'] and
					! in_array( $tag->type, $cond['type'], true )
				) {
					return false;
				}

				if (
					$cond['basetype'] and
					! in_array( $tag->basetype, $cond['basetype'], true )
				) {
					return false;
				}

				if (
					$cond['name'] and
					! in_array( $tag->name, $cond['name'], true )
				) {
					return false;
				}

				foreach ( $cond['feature'] as $feature ) {
					if ( str_starts_with( $feature, '!' ) ) { // Negation
						$feature = trim( substr( $feature, 1 ) );

						if ( $this->tag_type_supports( $tag->type, $feature ) ) {
							return false;
						}
					} else {
						if ( ! $this->tag_type_supports( $tag->type, $feature ) ) {
							return false;
						}
					}
				}

				return true;
			}
		);

		return array_values( $tags );
	}


	/**
	 * Returns the regular expression for a form-tag.
	 */
	private function tag_regex() {
		$tag_types = implode( '|',
			array_map( 'preg_quote', array_keys( $this->tag_types ) )
		);

		$whitespaces = wpcf7_get_unicode_whitespaces();

		return '(\[?)'
			. '\[(' . $tag_types . ')'
			. '(?:[' . $whitespaces . ']+(.*?))?'
			. '(?:[' . $whitespaces . ']+(\/))?\]'
			. '(?:([^[]*?)\[\/\2\])?'
			. '(\]?)';
	}


	/**
	 * The callback function for the form-tag replacement.
	 */
	private function replace_callback( $matches ) {
		return $this->scan_callback( $matches, true );
	}


	/**
	 * The callback function for the form-tag scanning.
	 */
	private function scan_callback( $matches, $replace = false ) {
		// allow [[foo]] syntax for escaping a tag
		if ( '[' === $matches[1] and ']' === $matches[6] ) {
			return substr( $matches[0], 1, -1 );
		}

		$tag_type = $matches[2];
		$tag_basetype = trim( $tag_type, '*' );
		$attr = $this->parse_atts( $matches[3] );

		$scanned_tag = array(
			'type' => $tag_type,
			'basetype' => $tag_basetype,
			'raw_name' => '',
			'name' => '',
			'options' => array(),
			'raw_values' => array(),
			'values' => array(),
			'pipes' => null,
			'labels' => array(),
			'attr' => '',
			'content' => '',
		);

		if ( $this->tag_type_supports( $tag_type, 'singular' ) ) {
			$tags_in_same_basetype = $this->filter(
				$this->scanned_tags,
				array( 'basetype' => $tag_basetype )
			);

			if ( $tags_in_same_basetype ) {
				// Another tag in the same base type already exists. Ignore this one.
				return $matches[0];
			}
		}

		if ( $this->tag_type_supports( $tag_type, 'name-attr' ) ) {
			if ( ! is_array( $attr ) ) {
				return $matches[0]; // Invalid form-tag.
			}

			$scanned_tag['raw_name'] = (string) array_shift( $attr['options'] );

			if ( ! wpcf7_is_name( $scanned_tag['raw_name'] ) ) {
				return $matches[0]; // Invalid name is used. Ignore this tag.
			}

			$scanned_tag['name'] = strtr( $scanned_tag['raw_name'], '.', '_' );
		}

		if ( is_array( $attr ) ) {
			$scanned_tag['options'] = (array) $attr['options'];
			$scanned_tag['raw_values'] = (array) $attr['values'];

			if ( WPCF7_USE_PIPE ) {
				$pipes = new WPCF7_Pipes( $scanned_tag['raw_values'] );
				$scanned_tag['values'] = $pipes->collect_befores();
				$scanned_tag['pipes'] = $pipes;
			} else {
				$scanned_tag['values'] = $scanned_tag['raw_values'];
			}

			$scanned_tag['labels'] = $scanned_tag['values'];

		} else {
			$scanned_tag['attr'] = $attr;
		}

		$scanned_tag['values'] = array_map( 'trim', $scanned_tag['values'] );
		$scanned_tag['labels'] = array_map( 'trim', $scanned_tag['labels'] );

		$content = trim( $matches[5] );
		$content = preg_replace( "/<br[\r\n\t ]*\/?>$/m", '', $content );
		$scanned_tag['content'] = $content;

		$scanned_tag = apply_filters( 'wpcf7_form_tag', $scanned_tag, $replace );

		$scanned_tag = new WPCF7_FormTag( $scanned_tag );

		$this->scanned_tags[] = $scanned_tag;

		if ( $replace ) {
			$callback = $this->tag_types[$tag_type]['function'];
			return $matches[1] . call_user_func( $callback, $scanned_tag ) . $matches[6];
		} else {
			return $matches[0];
		}
	}


	/**
	 * Parses the attributes of a form-tag to extract the name,
	 * options, and values.
	 *
	 * @param string $text Attributes of a form-tag.
	 * @return array|string An associative array of the options and values
	 *                      if the input is in the correct syntax,
	 *                      otherwise the input text itself.
	 */
	private function parse_atts( $text ) {
		$atts = array(
			'options' => array(),
			'values' => array(),
		);

		$whitespaces = wpcf7_get_unicode_whitespaces();

		$text = preg_replace( '/[\x{00a0}\x{200b}]+/u', ' ', $text );
		$text = wpcf7_strip_whitespaces( $text );

		$pattern = '%^([-+*=0-9a-zA-Z:.!?#$&@_/|\%' . $whitespaces . ']*?)'
			. '((?:'
			. '[' . $whitespaces . ']*"[^"]*"'
			. '|'
			. '[' . $whitespaces . ']*\'[^\']*\''
			. ')*)$%u';

		if ( preg_match( $pattern, $text, $matches ) ) {
			if ( ! empty( $matches[1] ) ) {
				$atts['options'] = preg_split(
					sprintf( '/[%s]+/u', $whitespaces ),
					wpcf7_strip_whitespaces( $matches[1] )
				);
			}

			if ( ! empty( $matches[2] ) ) {
				preg_match_all( '/"[^"]*"|\'[^\']*\'/', $matches[2], $matched_values );
				$atts['values'] = wpcf7_strip_quote_deep( $matched_values[0] );
			}
		} else {
			$atts = $text;
		}

		return $atts;
	}

}
