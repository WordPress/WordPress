<?php

/**
 * A form-tag.
 *
 * @link https://contactform7.com/tag-syntax/#form_tag
 */
class WPCF7_FormTag implements ArrayAccess {

	public $type;
	public $basetype;
	public $raw_name = '';
	public $name = '';
	public $options = array();
	public $raw_values = array();
	public $values = array();
	public $pipes;
	public $labels = array();
	public $attr = '';
	public $content = '';

	public function __construct( $tag = array() ) {
		if ( is_array( $tag ) or $tag instanceof self ) {
			foreach ( $tag as $key => $value ) {
				if ( property_exists( __CLASS__, $key ) ) {
					$this->{$key} = $value;
				}
			}
		}
	}


	/**
	 * Returns true if the type has a trailing asterisk.
	 */
	public function is_required() {
		return str_ends_with( $this->type, '*' );
	}


	/**
	 * Returns true if the form-tag has a specified option.
	 */
	public function has_option( $option_name ) {
		$pattern = sprintf( '/^%s(:.+)?$/i', preg_quote( $option_name, '/' ) );
		return (bool) preg_grep( $pattern, $this->options );
	}


	/**
	 * Retrieves option values with the specified option name.
	 *
	 * @param string $option_name Option name.
	 * @param string $pattern Optional. A regular expression pattern or one of
	 *               the keys of preset patterns. If specified, only options
	 *               whose value part matches this pattern will be returned.
	 * @param bool $single Optional. If true, only the first matching option
	 *             will be returned. Default false.
	 * @return string|array|bool The option value or an array of option values.
	 *                           False if there is no option matches the pattern.
	 */
	public function get_option( $option_name, $pattern = '', $single = false ) {
		$preset_patterns = array(
			'date' => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
			'int' => '[0-9]+',
			'signed_int' => '[-]?[0-9]+',
			'num' => '(?:[0-9]+|(?:[0-9]+)?[.][0-9]+)',
			'signed_num' => '[-]?(?:[0-9]+|(?:[0-9]+)?[.][0-9]+)',
			'class' => '[-0-9a-zA-Z_]+',
			'id' => '[-0-9a-zA-Z_]+',
		);

		if ( isset( $preset_patterns[$pattern] ) ) {
			$pattern = $preset_patterns[$pattern];
		}

		if ( '' === $pattern ) {
			$pattern = '.+';
		}

		$pattern = sprintf(
			'/^%s:%s$/i',
			preg_quote( $option_name, '/' ),
			$pattern
		);

		if ( $single ) {
			$matches = $this->get_first_match_option( $pattern );

			if ( ! $matches ) {
				return false;
			}

			return substr( $matches[0], strlen( $option_name ) + 1 );
		} else {
			$matches_a = $this->get_all_match_options( $pattern );

			if ( ! $matches_a ) {
				return false;
			}

			$results = array();

			foreach ( $matches_a as $matches ) {
				$results[] = substr( $matches[0], strlen( $option_name ) + 1 );
			}

			return $results;
		}
	}


	/**
	 * Retrieves the id option value from the form-tag.
	 */
	public function get_id_option() {
		static $used = array();

		$option = $this->get_option( 'id', 'id', true );

		if (
			! $option or
			str_starts_with( $option, 'wpcf7' ) or
			in_array( $option, $used, true )
		) {
			return false;
		}

		$used[] = $option;

		return $option;
	}


	/**
	 * Retrieves the class option value from the form-tag.
	 *
	 * @param string|array $default_classes Optional. Preset classes as an array
	 *                     or a whitespace-separated list. Default empty string.
	 * @return string|bool A whitespace-separated list of classes.
	 *                     False if there is no class to return.
	 */
	public function get_class_option( $default_classes = '' ) {
		if ( is_string( $default_classes ) ) {
			$default_classes = explode( ' ', $default_classes );
		}

		$options = array_merge(
			(array) $default_classes,
			(array) $this->get_option( 'class' )
		);

		$options = array_map( 'sanitize_html_class', $options );
		$options = array_filter( array_unique( $options ) );

		if ( empty( $options ) ) {
			return false;
		}

		return implode( ' ', $options );
	}


	/**
	 * Retrieves the autocomplete option value from the form-tag.
	 *
	 * @return string|bool A whitespace-separated list of tokens.
	 *                     False if there is no token to return.
	 */
	public function get_autocomplete_option() {
		$options = (array) $this->get_option( 'autocomplete', '[-0-9a-zA-Z|]+' );

		$options = array_reduce( $options, static function ( $carry, $item ) {
			return array_merge( $carry,
				array_map( 'strtolower', explode( '|', $item ) )
			);
		}, array() );

		$options = array_filter( $options, static function ( $item ) {
			return preg_match( '/^[a-z]+(?:-[0-9a-z]+)*$/', $item );
		} );

		$options = array_unique( $options );

		if ( empty( $options ) ) {
			return false;
		} elseif ( in_array( 'off', $options, true ) ) {
			return 'off';
		} elseif ( in_array( 'on', $options, true ) ) {
			return 'on';
		} else {
			return implode( ' ', $options );
		}
	}


	/**
	 * Retrieves the size option value from the form-tag.
	 *
	 * @param string $default_value Optional default value.
	 * @return string The option value.
	 */
	public function get_size_option( $default_value = false ) {
		$option = $this->get_option( 'size', 'int', true );

		if ( $option ) {
			return $option;
		}

		$matches_a = $this->get_all_match_options( '%^([0-9]*)/[0-9]*$%' );

		foreach ( $matches_a as $matches ) {
			if ( isset( $matches[1] ) and '' !== $matches[1] ) {
				return $matches[1];
			}
		}

		return $default_value;
	}


	/**
	 * Retrieves the maxlength option value from the form-tag.
	 *
	 * @param string $default_value Optional default value.
	 * @return string The option value.
	 */
	public function get_maxlength_option( $default_value = false ) {
		$option = $this->get_option( 'maxlength', 'int', true );

		if ( $option ) {
			return $option;
		}

		$matches_a = $this->get_all_match_options(
			'%^(?:[0-9]*x?[0-9]*)?/([0-9]+)$%'
		);

		foreach ( $matches_a as $matches ) {
			if ( isset( $matches[1] ) and '' !== $matches[1] ) {
				return $matches[1];
			}
		}

		return $default_value;
	}


	/**
	 * Retrieves the minlength option value from the form-tag.
	 *
	 * @param string $default_value Optional default value.
	 * @return string The option value.
	 */
	public function get_minlength_option( $default_value = false ) {
		$option = $this->get_option( 'minlength', 'int', true );

		if ( $option ) {
			return $option;
		} else {
			return $default_value;
		}
	}


	/**
	 * Retrieves the cols option value from the form-tag.
	 *
	 * @param string $default_value Optional default value.
	 * @return string The option value.
	 */
	public function get_cols_option( $default_value = false ) {
		$option = $this->get_option( 'cols', 'int', true );

		if ( $option ) {
			return $option;
		}

		$matches_a = $this->get_all_match_options(
			'%^([0-9]*)x([0-9]*)(?:/[0-9]+)?$%'
		);

		foreach ( $matches_a as $matches ) {
			if ( isset( $matches[1] ) and '' !== $matches[1] ) {
				return $matches[1];
			}
		}

		return $default_value;
	}


	/**
	 * Retrieves the rows option value from the form-tag.
	 *
	 * @param string $default_value Optional default value.
	 * @return string The option value.
	 */
	public function get_rows_option( $default_value = false ) {
		$option = $this->get_option( 'rows', 'int', true );

		if ( $option ) {
			return $option;
		}

		$matches_a = $this->get_all_match_options(
			'%^([0-9]*)x([0-9]*)(?:/[0-9]+)?$%'
		);

		foreach ( $matches_a as $matches ) {
			if ( isset( $matches[2] ) and '' !== $matches[2] ) {
				return $matches[2];
			}
		}

		return $default_value;
	}


	/**
	 * Retrieves a date-type option value from the form-tag.
	 *
	 * @param string $option_name A date-type option name, such as 'min' or 'max'.
	 * @return string|bool The option value in YYYY-MM-DD format. False if the
	 *                     option does not exist or the date value is invalid.
	 */
	public function get_date_option( $option_name ) {
		$option_value = $this->get_option( $option_name, '', true );

		if ( empty( $option_value ) ) {
			return false;
		}

		$date = apply_filters( 'wpcf7_form_tag_date_option',
			null,
			array( $option_name => $option_value )
		);

		if ( $date ) {
			$date_pattern = '/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/';

			if (
				preg_match( $date_pattern, $date, $matches ) and
				checkdate( $matches[2], $matches[3], $matches[1] )
			) {
				return $date;
			}
		} else {
			$datetime_obj = date_create_immutable(
				preg_replace( '/[_]+/', ' ', $option_value ),
				wp_timezone()
			);

			if ( $datetime_obj ) {
				return $datetime_obj->format( 'Y-m-d' );
			}
		}

		return false;
	}


	/**
	 * Retrieves the default option value from the form-tag.
	 *
	 * @param string|array $default_value Optional default value.
	 * @param string|array $args Optional options for the option value retrieval.
	 * @return string|array The option value. If the multiple option is enabled,
	 *                      an array of option values.
	 */
	public function get_default_option( $default_value = '', $args = '' ) {
		$args = wp_parse_args( $args, array(
			'multiple' => false,
			'shifted' => false,
		) );

		$options = (array) $this->get_option( 'default' );
		$values = array();

		if ( empty( $options ) ) {
			return $args['multiple'] ? $values : $default_value;
		}

		foreach ( $options as $opt ) {
			$opt = sanitize_key( $opt );

			if ( 'user_' === substr( $opt, 0, 5 ) and is_user_logged_in() ) {
				$primary_props = array( 'user_login', 'user_email', 'user_url' );
				$opt = in_array( $opt, $primary_props, true ) ? $opt : substr( $opt, 5 );

				$user = wp_get_current_user();
				$user_prop = $user->get( $opt );

				if ( ! empty( $user_prop ) ) {
					if ( $args['multiple'] ) {
						$values[] = $user_prop;
					} else {
						return $user_prop;
					}
				}

			} elseif ( 'post_meta' === $opt and in_the_loop() ) {
				if ( $args['multiple'] ) {
					$values = array_merge( $values,
						get_post_meta( get_the_ID(), $this->name )
					);
				} else {
					$val = (string) get_post_meta( get_the_ID(), $this->name, true );

					if ( strlen( $val ) ) {
						return $val;
					}
				}

			} elseif (
				'get' === $opt and
				$vals = wpcf7_superglobal_get( $this->name )
			) {
				$vals = array_map( 'wpcf7_sanitize_query_var', (array) $vals );

				if ( $args['multiple'] ) {
					$values = array_merge( $values, $vals );
				} else {
					$val = isset( $vals[0] ) ? (string) $vals[0] : '';

					if ( strlen( $val ) ) {
						return $val;
					}
				}

			} elseif (
				'post' === $opt and
				$vals = wpcf7_superglobal_post( $this->name )
			) {
				$vals = array_map( 'wpcf7_sanitize_query_var', (array) $vals );

				if ( $args['multiple'] ) {
					$values = array_merge( $values, $vals );
				} else {
					$val = isset( $vals[0] ) ? (string) $vals[0] : '';

					if ( strlen( $val ) ) {
						return $val;
					}
				}

			} elseif ( 'shortcode_attr' === $opt ) {
				if ( $contact_form = WPCF7_ContactForm::get_current() ) {
					$val = $contact_form->shortcode_attr( $this->name );

					if ( isset( $val ) and strlen( $val ) ) {
						if ( $args['multiple'] ) {
							$values[] = $val;
						} else {
							return $val;
						}
					}
				}

			} elseif ( preg_match( '/^[0-9_]+$/', $opt ) ) {
				$nums = explode( '_', $opt );

				foreach ( $nums as $num ) {
					$num = absint( $num );
					$num = $args['shifted'] ? $num : $num - 1;

					if ( isset( $this->values[$num] ) ) {
						if ( $args['multiple'] ) {
							$values[] = $this->values[$num];
						} else {
							return $this->values[$num];
						}
					}
				}
			}
		}

		if ( $args['multiple'] ) {
			$values = array_unique( $values );
			return $values;
		} else {
			return $default_value;
		}
	}


	/**
	 * Retrieves the data option value from the form-tag.
	 *
	 * @param string|array $args Optional options for the option value retrieval.
	 * @return mixed The option value.
	 */
	public function get_data_option( $args = '' ) {
		$options = (array) $this->get_option( 'data' );

		return apply_filters( 'wpcf7_form_tag_data_option', null, $options, $args );
	}


	/**
	 * Retrieves the limit option value from the form-tag.
	 *
	 * @param int $default_value Optional default value. Default 1048576.
	 * @return int The option value.
	 */
	public function get_limit_option( $default_value = MB_IN_BYTES ) {
		$pattern = '/^limit:([1-9][0-9]*)([kKmM]?[bB])?$/';

		$matches = $this->get_first_match_option( $pattern );

		if ( $matches ) {
			$size = (int) $matches[1];

			if ( ! empty( $matches[2] ) ) {
				$kbmb = strtolower( $matches[2] );

				if ( 'kb' === $kbmb ) {
					$size *= KB_IN_BYTES;
				} elseif ( 'mb' === $kbmb ) {
					$size *= MB_IN_BYTES;
				}
			}

			return $size;
		}

		return (int) $default_value;
	}


	/**
	 * Retrieves the value of the first option matches the given
	 * regular expression pattern.
	 *
	 * @param string $pattern Regular expression pattern.
	 * @return array|bool Option value as an array of matched strings.
	 *                    False if there is no option matches the pattern.
	 */
	public function get_first_match_option( $pattern ) {
		foreach ( (array) $this->options as $option ) {
			if ( preg_match( $pattern, $option, $matches ) ) {
				return $matches;
			}
		}

		return false;
	}


	/**
	 * Retrieves values of options that match the given
	 * regular expression pattern.
	 *
	 * @param string $pattern Regular expression pattern.
	 * @return array Array of arrays of strings that match the pattern.
	 */
	public function get_all_match_options( $pattern ) {
		$result = array();

		foreach ( (array) $this->options as $option ) {
			if ( preg_match( $pattern, $option, $matches ) ) {
				$result[] = $matches;
			}
		}

		return $result;
	}


	/**
	 * Assigns a value to the specified offset.
	 *
	 * @link https://www.php.net/manual/en/arrayaccess.offsetset.php
	 */
	#[ReturnTypeWillChange]
	public function offsetSet( $offset, $value ) {
		if ( property_exists( __CLASS__, $offset ) ) {
			$this->{$offset} = $value;
		}
	}


	/**
	 * Returns the value at specified offset.
	 *
	 * @link https://www.php.net/manual/en/arrayaccess.offsetget.php
	 */
	#[ReturnTypeWillChange]
	public function offsetGet( $offset ) {
		if ( property_exists( __CLASS__, $offset ) ) {
			return $this->{$offset};
		}

		return null;
	}


	/**
	 * Returns true if the specified offset exists.
	 *
	 * @link https://www.php.net/manual/en/arrayaccess.offsetexists.php
	 */
	#[ReturnTypeWillChange]
	public function offsetExists( $offset ) {
		return property_exists( __CLASS__, $offset );
	}


	/**
	 * Unsets an offset.
	 *
	 * @link https://www.php.net/manual/en/arrayaccess.offsetunset.php
	 */
	#[ReturnTypeWillChange]
	public function offsetUnset( $offset ) {
	}

}
