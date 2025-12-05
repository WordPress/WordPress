<?php

/**
 * Replaces double line breaks with paragraph elements.
 *
 * @param string $input The text which has to be formatted.
 * @param bool $br Optional. If set, this will convert all remaining
 *             line breaks after paragraphing. Default true.
 * @return string Text which has been converted into correct paragraph tags.
 */
function wpcf7_autop( $input, $br = true ) {
	$placeholders = array();

	// Replace non-HTML embedded elements with placeholders.
	$input = preg_replace_callback(
		'/<(math|svg).*?<\/\1>/is',
		static function ( $matches ) use ( &$placeholders ) {
			$placeholder = sprintf(
				'<%1$s id="%2$s" />',
				WPCF7_HTMLFormatter::placeholder_inline,
				hash( 'sha256', $matches[0] )
			);

			list( $placeholder ) =
				WPCF7_HTMLFormatter::normalize_start_tag( $placeholder );

			$placeholders[$placeholder] = $matches[0];

			return $placeholder;
		},
		$input
	);

	$formatter = new WPCF7_HTMLFormatter( array(
		'auto_br' => $br,
		'allowed_html' => null,
	) );

	$chunks = $formatter->separate_into_chunks( $input );

	$output = $formatter->format( $chunks );

	// Restore from placeholders.
	$output = str_replace(
		array_keys( $placeholders ),
		array_values( $placeholders ),
		$output
	);

	return $output;
}


/**
 * Newline preservation help function for wpcf7_autop().
 *
 * @deprecated 5.7 Unnecessary to use any more.
 *
 * @param array $matches preg_replace_callback() matches array.
 * @return string Text including newline placeholders.
 */
function wpcf7_autop_preserve_newline_callback( $matches ) {
	return str_replace( "\n", '<WPPreserveNewline />', $matches[0] );
}


/**
 * Sanitizes the query variables.
 *
 * @param string $text Query variable.
 * @return string Text sanitized.
 */
function wpcf7_sanitize_query_var( $text ) {
	$text = wp_unslash( $text );
	$text = wp_check_invalid_utf8( $text );

	if ( false !== strpos( $text, '<' ) ) {
		$text = wp_pre_kses_less_than( $text );
		$text = wp_strip_all_tags( $text );
	}

	$text = preg_replace( '/%[a-f0-9]{2}/i', '', $text );
	$text = preg_replace( '/ +/', ' ', $text );
	$text = trim( $text, ' ' );

	return $text;
}


/**
 * Strips quote characters surrounding the input.
 *
 * @param string $text Input text.
 * @return string Processed output.
 */
function wpcf7_strip_quote( $text ) {
	$text = wpcf7_strip_whitespaces( $text );

	if ( preg_match( '/^"(.*)"$/s', $text, $matches ) ) {
		$text = $matches[1];
	} elseif ( preg_match( "/^'(.*)'$/s", $text, $matches ) ) {
		$text = $matches[1];
	}

	return $text;
}


/**
 * Navigates through an array, object, or scalar, and
 * strips quote characters surrounding the each value.
 *
 * @param mixed $input The array or string to be processed.
 * @return mixed Processed value.
 */
function wpcf7_strip_quote_deep( $input ) {
	if ( is_string( $input ) ) {
		return wpcf7_strip_quote( $input );
	}

	if ( is_array( $input ) ) {
		$result = array();

		foreach ( $input as $key => $text ) {
			$result[$key] = wpcf7_strip_quote_deep( $text );
		}

		return $result;
	}
}


/**
 * Normalizes newline characters.
 *
 * @param string $text Input text.
 * @param string $to Optional. The newline character that is used in the output.
 * @return string Normalized text.
 */
function wpcf7_normalize_newline( $text, $to = "\n" ) {
	if ( ! is_string( $text ) ) {
		return $text;
	}

	$nls = array( "\r\n", "\r", "\n" );

	if ( ! in_array( $to, $nls, true ) ) {
		return $text;
	}

	return str_replace( $nls, $to, $text );
}


/**
 * Navigates through an array, object, or scalar, and
 * normalizes newline characters in the each value.
 *
 * @param mixed $input The array or string to be processed.
 * @param string $to Optional. The newline character that is used in the output.
 * @return mixed Processed value.
 */
function wpcf7_normalize_newline_deep( $input, $to = "\n" ) {
	if ( is_array( $input ) ) {
		$result = array();

		foreach ( $input as $key => $text ) {
			$result[$key] = wpcf7_normalize_newline_deep( $text, $to );
		}

		return $result;
	}

	return wpcf7_normalize_newline( $input, $to );
}


/**
 * Strips newline characters.
 *
 * @param string $text Input text.
 * @return string Processed one-line text.
 */
function wpcf7_strip_newline( $text ) {
	$text = (string) $text;
	$text = str_replace( array( "\r", "\n" ), '', $text );
	return wpcf7_strip_whitespaces( $text );
}


/**
 * Canonicalizes text.
 *
 * @param string $text Input text.
 * @param string|array|object $options Options.
 * @return string Canonicalized text.
 */
function wpcf7_canonicalize( $text, $options = '' ) {
	// for back-compat
	if (
		is_string( $options ) and
		'' !== $options and
		! str_contains( $options, '=' )
	) {
		$options = array(
			'strto' => $options,
		);
	}

	$options = wp_parse_args( $options, array(
		'strto' => 'lower',
		'strip_separators' => false,
	) );

	static $charset = null;

	if ( ! isset( $charset ) ) {
		$charset = get_option( 'blog_charset' );

		$is_utf8 = in_array(
			$charset,
			array( 'utf8', 'utf-8', 'UTF8', 'UTF-8' ),
			true
		);

		if ( $is_utf8 ) {
			$charset = 'UTF-8';
		}
	}

	$text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, $charset );

	if ( function_exists( 'mb_convert_kana' ) ) {
		$text = mb_convert_kana( $text, 'asKV', $charset );
	}

	if ( $options['strip_separators'] ) {
		$text = preg_replace( '/[\r\n\t ]+/', '', $text );
	} else {
		$text = preg_replace( '/[\r\n\t ]+/', ' ', $text );
	}

	if ( 'lower' === $options['strto'] ) {
		if ( function_exists( 'mb_strtolower' ) ) {
			$text = mb_strtolower( $text, $charset );
		} else {
			$text = strtolower( $text );
		}
	} elseif ( 'upper' === $options['strto'] ) {
		if ( function_exists( 'mb_strtoupper' ) ) {
			$text = mb_strtoupper( $text, $charset );
		} else {
			$text = strtoupper( $text );
		}
	}

	return wpcf7_strip_whitespaces( $text );
}


/**
 * Returns a canonical keyword usable for a name or an ID purposes.
 */
function wpcf7_canonicalize_name( $text ) {
	return preg_replace( '/[^0-9a-z]+/i', '-', $text );
}


/**
 * Sanitizes Contact Form 7's form unit-tag.
 *
 * @param string $tag Unit-tag.
 * @return string Sanitized unit-tag.
 */
function wpcf7_sanitize_unit_tag( $tag ) {
	$tag = preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $tag );
	return $tag;
}


/**
 * Converts a file name to one that is not executable as a script.
 *
 * @param string $filename File name.
 * @return string Converted file name.
 */
function wpcf7_antiscript_file_name( $filename ) {
	$filename = wp_basename( $filename );

	// Apply part of protection logic from sanitize_file_name().
	$filename = str_replace(
		array(
			'?', '[', ']', '/', '\\', '=', '<', '>', ':', ';', ',', "'", '"',
			'&', '$', '#', '*', '(', ')', '|', '~', '`', '!', '{', '}',
			'%', '+', '’', '«', '»', '”', '“', chr( 0 )
		),
		'',
		$filename
	);

	$filename = preg_replace( '/[\r\n\t -]+/', '-', $filename );
	$filename = preg_replace( '/[\pC\pZ]+/iu', '', $filename );

	$parts = explode( '.', $filename );

	if ( count( $parts ) < 2 ) {
		return $filename;
	}

	$script_pattern = '/^(php|phtml|pl|py|rb|cgi|asp|aspx)\d?$/i';

	$filename = array_shift( $parts );
	$extension = array_pop( $parts );

	foreach ( $parts as $part ) {
		if ( preg_match( $script_pattern, $part ) ) {
			$filename .= '.' . $part . '_';
		} else {
			$filename .= '.' . $part;
		}
	}

	if ( preg_match( $script_pattern, $extension ) ) {
		$filename .= '.' . $extension . '_.txt';
	} else {
		$filename .= '.' . $extension;
	}

	return $filename;
}


/**
 * Masks a password with asterisks (*).
 *
 * @param int $right Length of right-hand unmasked text. Default 0.
 * @param int $left Length of left-hand unmasked text. Default 0.
 * @return string Text of masked password.
 */
function wpcf7_mask_password( $text, $right = 0, $left = 0 ) {
	$length = strlen( $text );

	$right = absint( $right );
	$left = absint( $left );

	if ( $length < $right + $left ) {
		$right = $left = 0;
	}

	if ( $length <= 48 ) {
		$masked = str_repeat( '*', $length - ( $right + $left ) );
	} elseif ( $right + $left < 48 ) {
		$masked = str_repeat( '*', 48 - ( $right + $left ) );
	} else {
		$masked = '****';
	}

	$left_unmasked = $left ? substr( $text, 0, $left ) : '';
	$right_unmasked = $right ? substr( $text, -1 * $right ) : '';

	$text = $left_unmasked . $masked . $right_unmasked;

	return $text;
}


/**
 * Returns an array of allowed HTML tags and attributes for a given context.
 *
 * @param string $context Context used to decide allowed tags and attributes.
 * @return array Array of allowed HTML tags and their allowed attributes.
 */
function wpcf7_kses_allowed_html( $context = 'form' ) {
	static $allowed_tags = array();

	if ( isset( $allowed_tags[$context] ) ) {
		return apply_filters(
			'wpcf7_kses_allowed_html',
			$allowed_tags[$context],
			$context
		);
	}

	$allowed_tags[$context] = wp_kses_allowed_html( 'post' );

	if ( 'form' === $context ) {
		$additional_tags_for_form = array(
			'button' => array(
				'disabled' => true,
				'name' => true,
				'type' => true,
				'value' => true,
			),
			'datalist' => array(),
			'fieldset' => array(
				'disabled' => true,
				'name' => true,
			),
			'input' => array(
				'accept' => true,
				'alt' => true,
				'autocomplete' => true,
				'capture' => true,
				'checked' => true,
				'disabled' => true,
				'list' => true,
				'max' => true,
				'maxlength' => true,
				'min' => true,
				'minlength' => true,
				'multiple' => true,
				'name' => true,
				'pattern' => true,
				'placeholder' => true,
				'readonly' => true,
				'required' => true,
				'size' => true,
				'step' => true,
				'type' => true,
				'value' => true,
			),
			'label' => array(
				'for' => true,
			),
			'legend' => array(),
			'meter' => array(
				'value' => true,
				'min' => true,
				'max' => true,
				'low' => true,
				'high' => true,
				'optimum' => true,
			),
			'optgroup' => array(
				'disabled' => true,
				'label' => true,
			),
			'option' => array(
				'disabled' => true,
				'label' => true,
				'selected' => true,
				'value' => true,
			),
			'output' => array(
				'for' => true,
				'name' => true,
			),
			'progress' => array(
				'max' => true,
				'value' => true,
			),
			'select' => array(
				'autocomplete' => true,
				'disabled' => true,
				'multiple' => true,
				'name' => true,
				'required' => true,
				'size' => true,
			),
			'textarea' => array(
				'autocomplete' => true,
				'cols' => true,
				'disabled' => true,
				'maxlength' => true,
				'minlength' => true,
				'name' => true,
				'placeholder' => true,
				'readonly' => true,
				'required' => true,
				'rows' => true,
				'wrap' => true,
			),
		);

		$allowed_tags[$context] = array_merge(
			$allowed_tags[$context],
			$additional_tags_for_form
		);

		$allowed_tags[$context] = array_map(
			static function ( $elm ) {
				$global_attributes = array(
					'aria-atomic' => true,
					'aria-checked' => true,
					'aria-controls' => true,
					'aria-current' => true,
					'aria-describedby' => true,
					'aria-details' => true,
					'aria-disabled' => true,
					'aria-expanded' => true,
					'aria-hidden' => true,
					'aria-invalid' => true,
					'aria-label' => true,
					'aria-labelledby' => true,
					'aria-live' => true,
					'aria-relevant' => true,
					'aria-required' => true,
					'aria-selected' => true,
					'class' => true,
					'data-*' => true,
					'dir' => true,
					'hidden' => true,
					'id' => true,
					'inputmode' => true,
					'lang' => true,
					'role' => true,
					'spellcheck' => true,
					'style' => true,
					'tabindex' => true,
					'title' => true,
					'xml:lang' => true,
				);

				return array_merge( $global_attributes, (array) $elm );
			},
			$allowed_tags[$context]
		);
	}

	return apply_filters(
		'wpcf7_kses_allowed_html',
		$allowed_tags[$context],
		$context
	);
}


/**
 * Sanitizes content for allowed HTML tags for the specified context.
 *
 * @param string $input Content to filter.
 * @param string $context Context used to decide allowed tags and attributes.
 * @return string Filtered text with allowed HTML tags and attributes intact.
 */
function wpcf7_kses( $input, $context = 'form' ) {
	$output = wp_kses(
		$input,
		wpcf7_kses_allowed_html( $context )
	);

	return $output;
}


/**
 * Returns a formatted string of HTML attributes.
 *
 * @param array $atts Associative array of attribute name and value pairs.
 * @return string Formatted HTML attributes.
 */
function wpcf7_format_atts( $atts ) {
	$atts_filtered = array();

	foreach ( $atts as $name => $value ) {
		$name = strtolower( trim( $name ) );

		if ( ! preg_match( '/^[a-z_:][a-z_:.0-9-]*$/', $name ) ) {
			continue;
		}

		static $boolean_attributes = array(
			'checked',
			'disabled',
			'inert',
			'multiple',
			'readonly',
			'required',
			'selected',
		);

		if ( in_array( $name, $boolean_attributes, true ) and '' === $value ) {
			$value = false;
		}

		if ( is_numeric( $value ) ) {
			$value = (string) $value;
		}

		if ( null === $value or false === $value ) {
			unset( $atts_filtered[$name] );
		} elseif ( true === $value ) {
			$atts_filtered[$name] = $name; // boolean attribute
		} elseif ( is_string( $value ) ) {
			$atts_filtered[$name] = trim( $value );
		}
	}

	$output = '';

	foreach ( $atts_filtered as $name => $value ) {
		$output .= sprintf( ' %1$s="%2$s"', $name, esc_attr( $value ) );
	}

	return trim( $output );
}


/**
 * Returns the regular expression pattern that represents
 * whitespace characters Unicode defines.
 *
 * @link https://www.unicode.org/Public/UCD/latest/ucd/PropList.txt
 *
 * @return string Regular expression pattern.
 */
function wpcf7_get_unicode_whitespaces() {
	return '\x09-\x0D\x20\x85\xA0\x{1680}\x{2000}-\x{200A}\x{2028}\x{2029}\x{202F}\x{205F}\x{3000}';
}


/**
 * Strips surrounding whitespaces.
 *
 * @link https://contactform7.com/2024/07/13/consistent-handling-policy-of-surrounding-whitespaces/
 *
 * @param string|array $input Input text.
 * @param string $side The side from which whitespaces are stripped.
 *               'start', 'end', or 'both' (default).
 * @return string|array Output text.
 */
function wpcf7_strip_whitespaces( $input, $side = 'both' ) {
	if ( is_array( $input ) ) {
		return array_map(
			static function ( $i ) use ( $side ) {
				return wpcf7_strip_whitespaces( $i, $side );
			},
			$input
		);
	}

	// https://tc39.es/ecma262/multipage/ecmascript-language-lexical-grammar.html
	$whitespaces = wpcf7_get_unicode_whitespaces() . '\x{FEFF}';

	if ( 'end' !== $side ) {
		// Strip leading whitespaces
		$input = preg_replace(
			sprintf( '/^[%s]+/u', $whitespaces ),
			'',
			$input
		);
	}

	if ( 'start' !== $side ) {
		// Strip trailing whitespaces
		$input = preg_replace(
			sprintf( '/[%s]+$/u', $whitespaces ),
			'',
			$input
		);
	}

	return $input;
}
