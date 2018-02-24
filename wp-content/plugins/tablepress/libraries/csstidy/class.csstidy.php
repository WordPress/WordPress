<?php
/**
 * CSSTidy Parsing PHP Class
 *
 * @package TablePress
 * @subpackage CSS
 * @author Florian Schmitz, Brett Zamir, Nikolay Matsievsky, Cedric Morin, Christopher Finke, Mark Scherer, Tobias Bäthge
 * @since 1.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * CSSTidy - CSS Parser and Optimiser
 *
 * CSS Parser class
 *
 * Copyright 2005, 2006, 2007 Florian Schmitz
 *
 * This file is part of CSSTidy.
 *
 *  CSSTidy is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published by
 *  the Free Software Foundation; either version 2.1 of the License, or
 *  (at your option) any later version.
 *
 *  CSSTidy is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public License
 *  along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @license https://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package CSSTidy
 * @author Florian Schmitz (floele at gmail dot com) 2005-2007
 * @author Brett Zamir (brettz9 at yahoo dot com) 2007
 * @author Nikolay Matsievsky (speed at webo dot name) 2009-2010
 * @author Cedric Morin (cedric at yterium dot com) 2010-2012
 * @author Christopher Finke (cfinke at gmail.com) 2012
 * @author Mark Scherer (remove $GLOBALS once and for all + PHP5.4 comp) 2012
 */

/**
 * Defines ctype functions if required.
 *
 * @TODO: Make these methods of CSSTidy.
 */
if ( ! function_exists( 'ctype_space' ) ) {
	/**
	 * Check for whitespace character(s).
	 *
	 * @since 1.0.0
	 *
	 * @param string $text Text to check.
	 * @return bool Whether all characters in the string are whitespace characters.
	 */
	function ctype_space( $text ) {
		return ( 1 === preg_match( "/^[ \r\n\t\f]+$/", $text ) );
	}
}
if ( ! function_exists( 'ctype_alpha' ) ) {
	/**
	 * Check for alphabetic character(s).
	 *
	 * @since 1.0.0
	 *
	 * @param string $text Text to check.
	 * @return bool Whether all characters in the string are alphabetic characters.
	 */
	function ctype_alpha( $text ) {
		return ( 1 === preg_match( '/^[a-zA-Z]+$/', $text ) );
	}
}
if ( ! function_exists( 'ctype_xdigit' ) ) {
	/**
	 * Check for character(s) representing a hexadecimal digit.
	 *
	 * @since 1.0.0
	 *
	 * @param string $text Text to check.
	 * @return bool Whether $text is a hexadecimal number.
	 */
	function ctype_xdigit( $text ) {
		return ( 1 === preg_match( '/^[a-fA-F0-9]+$/', $text ) );
	}
}

/**
 * Defines constants.
 *
 * @TODO: Make these class constants of CSSTidy.
 * @since 1.0.0
 */
define( 'AT_START', 1 );
define( 'AT_END', 2 );
define( 'SEL_START', 3 );
define( 'SEL_END', 4 );
define( 'PROPERTY', 5 );
define( 'VALUE', 6 );
define( 'COMMENT', 7 );
define( 'DEFAULT_AT', 41 );

/**
 * Load the class for printing CSS code.
 *
 * @since 1.0.0
 */
require dirname( __FILE__ ) . '/class.csstidy_print.php';

/**
 * Load the class for optimising CSS code.
 *
 * @since 1.0.0
 */
require dirname( __FILE__ ) . '/class.csstidy_optimise.php';

/**
 * CSS Parser class
 *
 * This class represents a CSS parser which reads CSS code and saves it in an array.
 * In opposite to most other CSS parsers, it does not use regular expressions and
 * thus has full CSS2 support and a higher reliability.
 * Additionally to that, it applies some optimizations and fixes to the CSS code.
 *
 * @package CSSTidy
 * @since 1.0.0
 */
class TablePress_CSSTidy {

	/**
	 * The parsed CSS.
	 *
	 * This array is empty if preserve_css is on.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $css = array();

	/**
	 * The raw parsed CSS.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $tokens = array();

	/**
	 * Instance of the CSS Printer class.
	 *
	 * @since 1.0.0
	 * @var TablePress_CSSTidy_print
	 */
	public $print;

	/**
	 * Instance of the CSS Optimiser class.
	 *
	 * @since 1.0.0
	 * @var TablePress_CSSTidy_optimise
	 */
	public $optimise;

	/**
	 * The CSS charset.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $charset = '';

	/**
	 * All @import URLs.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $import = array();

	/**
	 * The namespace.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $namespace = '';

	/**
	 * The CSSTidy version.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $version = '1.5.3';

	/**
	 * The settings.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $settings = array();

	/**
	 * The parser-status.
	 *
	 * Possible values:
	 * - is = in selector
	 * - ip = in property
	 * - iv = in value
	 * - instr = in string (started at " or ' or ( )
	 * - ic = in comment (ignore everything)
	 * - at = in @-block
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $status = 'is';

	/**
	 * The current at rule (@media).
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $at = '';

	/**
	 * The at rule for next selector (during @font-face or other @).
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $next_selector_at = '';

	/**
	 * The current selector.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $selector = '';

	/**
	 * The current property.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $property = '';

	/**
	 * The position of , in selectors.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $sel_separate = array();

	/**
	 * The current value.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $value = '';

	/**
	 * The current sub-value.
	 *
	 * Example for a sub-value: In the CSS rule
	 * background: url(foo.png) red no-repeat;
	 * "url(foo.png)", "red", and  "no-repeat" are sub-values,
	 * separated by whitespace.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $sub_value = '';

	/**
	 * All sub-values for a property.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $sub_value_arr = array();

	/**
	 * The stack of characters that opened the current strings.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $str_char = array();

	/**
	 * [$cur_string description]
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $cur_string = array();

	/**
	 * Status from which the parser switched to ic or instr
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $from = array();

	/**
	 * True if in invalid at-rule.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $invalid_at = false;

	/**
	 * True if something has been added to the current selector.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $added = false;

	/**
	 * The message log.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $log = array();

	/**
	 * The line number.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	protected $line = 1;

	/**
	 * Marks if we need to leave quotes for a string.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $quoted_string = array();

	/**
	 * List of tokens.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $tokens_list = '';

	/**
	 * Various CSS Data for CSSTidy.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $data = array();

	/**
	 * The output templates.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $template = array();

	/**
	 * Loads standard template and sets default settings.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		include dirname( __FILE__ ) . '/data.inc.php';
		$this->data = $data;

		$this->settings['remove_bslash'] = true;
		$this->settings['compress_colors'] = true;
		$this->settings['compress_font-weight'] = true;
		$this->settings['lowercase_s'] = false;
		/*
		 * 1 common shorthands optimization
		 * 2 + font property optimization
		 * 3 + background property optimization
		 */
		$this->settings['optimise_shorthands'] = 1;
		$this->settings['remove_last_;'] = true;
		// Rewrite all properties with lower case, better for later gzipping.
		$this->settings['case_properties'] = 1;
		/*
		 * Sort properties in alpabetic order, better for later gzipping,
		 * but can cause trouble in case of overiding same properties or using hacks.
		 */
		$this->settings['sort_properties'] = false;
		/*
		 * 1, 3, 5, etc -- Enable sorting selectors inside @media: a{}b{}c{}.
		 * 2, 5, 8, etc -- Enable sorting selectors inside one CSS declaration: a,b,c{}.
		 * Preserve order by default cause it can break functionality.
		 */
		$this->settings['sort_selectors'] = 0;
		// Is dangerous to be used: CSS is broken sometimes.
		$this->settings['merge_selectors'] = 0;
		// Whether to preserve browser hacks.
		$this->settings['discard_invalid_selectors'] = false;
		$this->settings['discard_invalid_properties'] = false;
		$this->settings['css_level'] = 'CSS3.0';
		$this->settings['preserve_css'] = false;
		$this->settings['timestamp'] = false;
		$this->settings['template'] = ''; // say that property exists.
		$this->set_cfg( 'template', 'default' ); // Call load_template.

		$this->print = new TablePress_CSSTidy_print( $this );
		$this->optimise = new TablePress_CSSTidy_optimise( $this );

		$this->tokens_list = &$this->data['csstidy']['tokens'];
	}

	/**
	 * Get the value of a setting.
	 *
	 * @since 1.0.0
	 *
	 * @param string $setting Setting to get.
	 * @return string|bool Value of the setting.
	 */
	public function get_cfg( $setting ) {
		if ( isset( $this->settings[ $setting ] ) ) {
			return $this->settings[ $setting ];
		}
		return false;
	}

	/**
	 * Load a template.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template Template used by set_cfg to load a template via a configuration setting.
	 */
	protected function _load_template( $template ) {
		switch ( $template ) {
			case 'default':
				$this->load_template( 'default' );
				break;
			case 'highest':
			case 'high':
			case 'low':
				$this->load_template( $template . '_compression' );
				break;
			default:
				$this->load_template( $template );
				break;
		}
	}

	/**
	 * Set the value of a setting.
	 *
	 * @since 1.0.0
	 *
	 * @param string $setting Setting.
	 * @param mixed  $value   Optional. Value of the setting.
	 * @return bool [return value]
	 */
	public function set_cfg( $setting, $value = null ) {
		if ( is_array( $setting ) && is_null( $value ) ) {
			foreach ( $setting as $setprop => $setval ) {
				$this->settings[ $setprop ] = $setval;
			}
			if ( array_key_exists( 'template', $setting ) ) {
				$this->_load_template( $this->settings['template'] );
			}
			return true;
		} elseif ( isset( $this->settings[ $setting ] ) && '' !== $value ) {
			$this->settings[ $setting ] = $value;
			if ( 'template' === $setting ) {
				$this->_load_template( $this->settings['template'] );
			}
			return true;
		}
		return false;
	}

	/**
	 * Adds a token to $this->tokens.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed  $type Type.
	 * @param string $data Data.
	 * @param bool   $do   Optional. Add a token even if preserve_css is off.
	 */
	public function _add_token( $type, $data, $do = false ) {
		if ( $this->get_cfg( 'preserve_css' ) || $do ) {
			$this->tokens[] = array( $type, ( COMMENT === $type ) ? $data : trim( $data ) );
		}
	}

	/**
	 * Add a message to the message log.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Message.
	 * @param string $type    Type.
	 * @param int    $line    Optional. Line number. -1 will use the current line.
	 */
	public function log( $message, $type, $line = -1 ) {
		if ( -1 === $line ) {
			$line = $this->line;
		}
		$line = intval( $line );
		$add = array(
			'm' => $message,
			't' => $type,
		);
		if ( ! isset( $this->log[ $line ] ) || ! in_array( $add, $this->log[ $line ], true ) ) {
			$this->log[ $line ][] = $add;
		}
	}

	/**
	 * Parse Unicode notations and find a replacement character.
	 *
	 * @since 1.0.0
	 *
	 * @param string $string String.
	 * @param int    $i      i.
	 * @return string [return value]
	 */
	public function _unicode( &$string, &$i ) {
		++$i;
		$add = '';
		$replaced = false;

		while ( $i < strlen( $string ) && ( ctype_xdigit( $string[ $i ] ) || ctype_space( $string[ $i ] ) ) && strlen( $add ) < 6 ) {
			$add .= $string[ $i ];
			if ( ctype_space( $string[ $i ] ) ) {
				break;
			}
			$i++;
		}

		if ( hexdec( $add ) > 47 && hexdec( $add ) < 58 || hexdec( $add ) > 64 && hexdec( $add ) < 91 || hexdec( $add ) > 96 && hexdec( $add ) < 123 ) {
			$this->log( 'Replaced unicode notation: Changed \\' . $add . ' to ' . chr( hexdec( $add ) ), 'Information' );
			$add = chr( hexdec( $add ) );
			$replaced = true;
		} else {
			$add = trim( '\\' . $add );
		}

		if ( @ctype_xdigit( $string[ $i + 1 ] ) && ctype_space( $string[ $i ] ) && ! $replaced || ! ctype_space( $string[ $i ] ) ) {
			$i--;
		}

		if ( '\\' !== $add || ! $this->get_cfg( 'remove_bslash' ) || false !== strpos( $this->tokens_list, $string[ $i + 1 ] ) ) {
			return $add;
		}

		if ( '\\' === $add ) {
			$this->log( 'Removed unnecessary backslash', 'Information' );
		}

		return '';
	}

	/**
	 * Write formatted output to a file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $filename File name.
	 */
	public function write_page( $filename ) {
		$this->write( $filename, true );
	}

	/**
	 * Write plain output to a file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $filename    File name.
	 * @param bool   $formatted   Optional. Whether to print formatted or not.
	 * @param string $doctype     Optional. When printing formatted, is a shorthand for the document type.
	 * @param bool   $externalcss Optional. When printing formatted, indicates whether styles to be attached internally or as an external stylesheet.
	 * @param string $title       Optional. When printing formatted, is the title to be added in the head of the document.
	 * @param string $lang        Optional. When printing formatted, gives a two-letter language code to be added to the output.
	 */
	public function write( $filename, $formatted = false, $doctype = 'xhtml1.1', $externalcss = true, $title = '', $lang = 'en' ) {
		$filename .= ( $formatted ) ? '.xhtml' : '.css';

		if ( ! is_dir( 'temp' ) ) {
			$madedir = mkdir( 'temp' );
			if ( ! $madedir ) {
				print 'Could not make directory "temp" in ' . dirname( __FILE__ );
				exit;
			}
		}
		$handle = fopen( 'temp/' . $filename, 'w' );
		if ( $handle ) {
			if ( ! $formatted ) {
				fwrite( $handle, $this->print->plain() );
			} else {
				fwrite( $handle, $this->print->formatted_page( $doctype, $externalcss, $title, $lang ) );
			}
		}
		fclose( $handle );
	}

	/**
	 * Loads a new template.
	 *
	 * @since 1.0.0
	 *
	 * @link http://csstidy.sourceforge.net/templates.php
	 *
	 * @param string $content   Either file name (if $from_file is true), content of a template file, "high_compression", "highest_compression", "low_compression", or "default".
	 * @param bool   $from_file Optional. Uses $content as filename if true.
	 */
	protected function load_template( $content, $from_file = true ) {
		$predefined_templates = &$this->data['csstidy']['predefined_templates'];
		if ( in_array( $content, array( 'default', 'low_compression', 'high_compression', 'highest_compression' ), true ) ) {
			$this->template = $predefined_templates[ $content ];
			return;
		}

		if ( $from_file ) {
			$content = strip_tags( file_get_contents( $content ), '<span>' );
		}
		// Unify newlines (because the output also only uses \n).
		$content = str_replace( "\r\n", "\n", $content );
		$template = explode( '|', $content );

		for ( $i = 0; $i < count( $template ); $i++ ) {
			$this->template[ $i ] = $template[ $i ];
		}
	}

	/**
	 * Starts parsing from URL.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url URL.
	 * @return bool
	 */
	protected function parse_from_url( $url ) {
		return $this->parse( @file_get_contents( $url ) );
	}

	/**
	 * Checks if there is a token at the current position.
	 *
	 * @since 1.0.0
	 *
	 * @param string $string String.
	 * @param int    $i      i.
	 * @return bool [return value]
	 */
	protected function is_token( &$string, $i ) {
		return ( false !== strpos( $this->tokens_list, $string[ $i ] ) && ! $this->escaped( $string, $i ) );
	}

	/**
	 * Parses CSS in a string. The output is saved as an array in $this->css.
	 *
	 * @since 1.0.0
	 *
	 * @param string $string The CSS code.
	 * @return bool [return value]
	 */
	public function parse( $string ) {
		// Temporarily set locale to en_US in order to handle floats properly.
		$old = @setlocale( LC_ALL, 0 );
		@setlocale( LC_ALL, 'C' );

		$all_properties = &$this->data['csstidy']['all_properties'];
		$at_rules = &$this->data['csstidy']['at_rules'];
		$quoted_string_properties = &$this->data['csstidy']['quoted_string_properties'];

		$this->css = array();
		$this->print->input_css = $string;
		$string = str_replace( "\r\n", "\n", $string ) . ' ';
		$cur_comment = '';

		for ( $i = 0, $size = strlen( $string ); $i < $size; $i++ ) {
			if ( "\n" === $string[ $i ] || "\r" === $string[ $i ] ) {
				++$this->line;
			}

			switch ( $this->status ) {
				/* Case in at-block */
				case 'at':
					if ( $this->is_token( $string, $i ) ) {
						if ( '/' === $string[ $i ] && '*' === @$string[ $i + 1 ] ) {
							$this->status = 'ic';
							++$i;
							$this->from[] = 'at';
						} elseif ( '{' === $string[ $i ] ) {
							$this->status = 'is';
							$this->at = $this->css_new_media_section( $this->at );
							$this->_add_token( AT_START, $this->at );
						} elseif ( ',' === $string[ $i ] ) {
							$this->at = trim( $this->at ) . ',';
						} elseif ( '\\' === $string[ $i ] ) {
							$this->at .= $this->_unicode( $string, $i );
						}
						// Fix for complicated media, i.e @media screen and (-webkit-min-device-pixel-ratio:1.5)
						// '/' is included for ratios in Opera: (-o-min-device-pixel-ratio: 3/2)
						elseif ( in_array( $string[ $i ], array( '(', ')', ':', '.', '/' ), true ) ) {
							$this->at .= $string[ $i ];
						}
					} else {
						$lastpos = strlen( $this->at ) - 1;
						if ( ! ( ( ctype_space( $this->at[ $lastpos ] ) || $this->is_token( $this->at, $lastpos ) && ',' === $this->at[ $lastpos ] ) && ctype_space( $string[ $i ] ) ) ) {
							$this->at .= $string[ $i ];
						}
					}
					break;
				/* Case in-selector */
				case 'is':
					if ( $this->is_token( $string, $i ) ) {
						if ( '/' === $string[ $i ] && '*' === @$string[ $i + 1 ] && '' === trim( $this->selector ) ) {
							$this->status = 'ic';
							++$i;
							$this->from[] = 'is';
						} elseif ( '@' === $string[ $i ] && '' === trim( $this->selector ) ) {
							// Check for at-rule.
							$this->invalid_at = true;
							foreach ( $at_rules as $name => $type ) {
								if ( ! strcasecmp( substr( $string, $i + 1, strlen( $name ) ), $name ) ) {
									if ( 'at' === $type ) {
										$this->at = '@' . $name;
									} else {
										$this->selector = '@' . $name;
									}
									if ( 'atis' === $type ) {
										$this->next_selector_at = ( $this->next_selector_at ? $this->next_selector_at : ( $this->at ? $this->at : DEFAULT_AT ) );
										$this->at = $this->css_new_media_section( ' ' );
										$type = 'is';
									}
									$this->status = $type;
									$i += strlen( $name );
									$this->invalid_at = false;
								}
							}

							if ( $this->invalid_at ) {
								$this->selector = '@';
								$invalid_at_name = '';
								for ( $j = $i + 1; $j < $size; ++$j ) {
									if ( ! ctype_alpha( $string[ $j ] ) ) {
										break;
									}
									$invalid_at_name .= $string[ $j ];
								}
								$this->log( 'Invalid @-rule: ' . $invalid_at_name . ' (removed)', 'Warning' );
							}
						} elseif ( ( '"' === $string[ $i ] || "'" === $string[ $i ] ) ) {
							$this->cur_string[] = $string[ $i ];
							$this->status = 'instr';
							$this->str_char[] = $string[ $i ];
							$this->from[] = 'is';
							/* Fixing CSS3 attribute selectors, i.e. a[href$=".mp3" */
							$this->quoted_string[] = ( '=' === $string[ $i - 1 ] );
						} elseif ( $this->invalid_at && ';' === $string[ $i ] ) {
							$this->invalid_at = false;
							$this->status = 'is';
							if ( $this->next_selector_at ) {
								$this->at = $this->css_new_media_section( $this->next_selector_at );
								$this->next_selector_at = '';
							}
						} elseif ( '{' === $string[ $i ] ) {
							$this->status = 'ip';
							if ( '' === $this->at ) {
								$this->at = $this->css_new_media_section( DEFAULT_AT );
							}
							$this->selector = $this->css_new_selector( $this->at, $this->selector );
							$this->_add_token( SEL_START, $this->selector );
							$this->added = false;
						} elseif ( '}' === $string[ $i ] ) {
							$this->_add_token( AT_END, $this->at );
							$this->at = '';
							$this->selector = '';
							$this->sel_separate = array();
						} elseif ( ',' === $string[ $i ] ) {
							$this->selector = trim( $this->selector ) . ',';
							$this->sel_separate[] = strlen( $this->selector );
						} elseif ( '\\' === $string[ $i ] ) {
							$this->selector .= $this->_unicode( $string, $i );
						} elseif ( '*' === $string[ $i ] && @in_array( $string[ $i + 1 ], array( '.', '#', '[', ':' ), true ) && ( 0 === $i || '/' !== $string[ $i - 1 ] ) ) {
							// Remove unnecessary universal selector, FS#147, but not comment in selector.
						} else {
							$this->selector .= $string[ $i ];
						}
					} else {
						$lastpos = strlen( $this->selector ) - 1;
						if ( -1 === $lastpos || ! ( ( ctype_space( $this->selector[ $lastpos ] ) || $this->is_token( $this->selector, $lastpos ) && ',' === $this->selector[ $lastpos ] ) && ctype_space( $string[ $i ] ) ) ) {
							$this->selector .= $string[ $i ];
						}
					}
					break;
				/* Case in-property */
				case 'ip':
					if ( $this->is_token( $string, $i ) ) {
						if ( ( ':' === $string[ $i ] || '=' === $string[ $i ] ) && '' !== $this->property ) {
							$this->status = 'iv';
							if ( ! $this->get_cfg( 'discard_invalid_properties' ) || $this->property_is_valid( $this->property ) ) {
								$this->property = $this->css_new_property( $this->at, $this->selector, $this->property );
								$this->property = strtolower( $this->property );
								$this->_add_token( PROPERTY, $this->property );
							}
						} elseif ( '/' === $string[ $i ] && '*' === @$string[ $i + 1 ] && '' === $this->property ) {
							$this->status = 'ic';
							++$i;
							$this->from[] = 'ip';
						} elseif ( '}' === $string[ $i ] ) {
							$this->explode_selectors();
							$this->status = 'is';
							$this->invalid_at = false;
							$this->_add_token( SEL_END, $this->selector );
							$this->selector = '';
							$this->property = '';
							if ( $this->next_selector_at ) {
								$this->at = $this->css_new_media_section( $this->next_selector_at );
								$this->next_selector_at = '';
							}
						} elseif ( ';' === $string[ $i ] ) {
							$this->property = '';
						} elseif ( '\\' === $string[ $i ] ) {
							$this->property .= $this->_unicode( $string, $i );
						}
						// else this is dumb IE a hack, keep it
						// including /
						elseif ( ( '' === $this->property && ! ctype_space( $string[ $i ] ) ) || ( '/' === $this->property || '/' === $string[ $i ] ) ) {
							$this->property .= $string[ $i ];
						}
					} elseif ( ! ctype_space( $string[ $i ] ) ) {
						$this->property .= $string[ $i ];
					}
					break;
				/* Case in-value */
				case 'iv':
					$pn = ( ( "\n" === $string[ $i ] || "\r" === $string[ $i ] ) && $this->property_is_next( $string, $i + 1 ) || ( strlen( $string ) - 1 ) === $i );
					if ( ( $this->is_token( $string, $i ) || $pn ) && ( ! ( ',' === $string[ $i ] && ! ctype_space( $string[ $i + 1 ] ) ) ) ) {
						if ( '/' === $string[ $i ] && '*' === @$string[ $i + 1 ] ) {
							$this->status = 'ic';
							++$i;
							$this->from[] = 'iv';
						} elseif ( ( '"' === $string[ $i ] || "'" === $string[ $i ] || '(' === $string[ $i ] ) ) {
							$this->cur_string[] = $string[ $i ];
							$this->str_char[] = ( '(' === $string[ $i ] ) ? ')' : $string[ $i ];
							$this->status = 'instr';
							$this->from[] = 'iv';
							$this->quoted_string[] = in_array( strtolower( $this->property ), $quoted_string_properties, true );
						} elseif ( ',' === $string[ $i ] ) {
							$this->sub_value = trim( $this->sub_value ) . ',';
						} elseif ( '\\' === $string[ $i ] ) {
							$this->sub_value .= $this->_unicode( $string, $i );
						} elseif ( ';' === $string[ $i ] || $pn ) {
							if ( '@' === $this->selector[0] && isset( $at_rules[ substr( $this->selector, 1 ) ] ) && 'iv' === $at_rules[ substr( $this->selector, 1 ) ] ) {
								$this->status = 'is';

								switch ( $this->selector ) {
									case '@charset':
										// Add quotes to charset.
										$this->sub_value_arr[] = '"' . trim( $this->sub_value ) . '"';
										$this->charset = $this->sub_value_arr[0];
										break;
									case '@namespace':
										// Add quotes to namespace.
										$this->sub_value_arr[] = '"' . trim( $this->sub_value ) . '"';
										$this->namespace = implode( ' ', $this->sub_value_arr );
										break;
									case '@import':
										$this->sub_value = trim( $this->sub_value );

										if ( empty( $this->sub_value_arr ) ) {
											// Quote URLs in imports only if they're not already inside url() and not already quoted.
											if ( 'url(' !== substr( $this->sub_value, 0, 4 ) ) {
												if ( ! ( substr( $this->sub_value, -1 ) === $this->sub_value[0] && in_array( $this->sub_value[0], array( "'", '"' ), true ) ) ) {
													$this->sub_value = '"' . $this->sub_value . '"';
												}
											}
										}

										$this->sub_value_arr[] = $this->sub_value;
										$this->import[] = implode( ' ', $this->sub_value_arr );
										break;
								}

								$this->sub_value_arr = array();
								$this->sub_value = '';
								$this->selector = '';
								$this->sel_separate = array();
							} else {
								$this->status = 'ip';
							}
						} elseif ( '}' !== $string[ $i ] ) {
							$this->sub_value .= $string[ $i ];
						}
						if ( ( '}' === $string[ $i ] || ';' === $string[ $i ] || $pn ) && ! empty( $this->selector ) ) {
							if ( '' === $this->at ) {
								$this->at = $this->css_new_media_section( DEFAULT_AT );
							}

							// Case settings.
							if ( $this->get_cfg( 'lowercase_s' ) ) {
								$this->selector = strtolower( $this->selector );
							}
							$this->property = strtolower( $this->property );

							$this->optimise->subvalue();
							if ( '' !== $this->sub_value ) {
								$this->sub_value_arr[] = $this->sub_value;
								$this->sub_value = '';
							}

							$this->value = '';
							while ( count( $this->sub_value_arr ) ) {
								$sub = array_shift( $this->sub_value_arr );
								if ( strstr( $this->selector, 'font-face' ) ) {
									$sub = $this->quote_font_format( $sub );
								}

								if ( '' !== $sub ) {
									if ( strlen( $this->value ) && ( ',' !== substr( $this->value, -1, 1 ) || $this->get_cfg( 'preserve_css' ) ) ) {
										$this->value .= ' ';
									}
									$this->value .= $sub;
								}
							}

							$this->optimise->value();

							$valid = $this->property_is_valid( $this->property );
							if ( ( ! $this->invalid_at || $this->get_cfg( 'preserve_css' ) ) && ( ! $this->get_cfg( 'discard_invalid_properties' ) || $valid ) ) {
								$this->css_add_property( $this->at, $this->selector, $this->property, $this->value );
								$this->_add_token( VALUE, $this->value );
								$this->optimise->shorthands();
							}
							if ( ! $valid ) {
								if ( $this->get_cfg( 'discard_invalid_properties' ) ) {
									$this->log( 'Removed invalid property: ' . $this->property, 'Warning' );
								} else {
									$this->log( 'Invalid property in ' . strtoupper( $this->get_cfg( 'css_level' ) ) . ': ' . $this->property, 'Warning' );
								}
							}

							$this->property = '';
							$this->sub_value_arr = array();
							$this->value = '';
						}
						if ( '}' === $string[ $i ] ) {
							$this->explode_selectors();
							$this->_add_token( SEL_END, $this->selector );
							$this->status = 'is';
							$this->invalid_at = false;
							$this->selector = '';
							if ( $this->next_selector_at ) {
								$this->at = $this->css_new_media_section( $this->next_selector_at );
								$this->next_selector_at = '';
							}
						}
					} elseif ( ! $pn ) {
						$this->sub_value .= $string[ $i ];

						if ( ctype_space( $string[ $i ] ) || ',' === $string[ $i ] ) {
							$this->optimise->subvalue();
							if ( '' !== $this->sub_value ) {
								$this->sub_value_arr[] = $this->sub_value;
								$this->sub_value = '';
							}
						}
					}
					break;
				/* Case in string */
				case 'instr':
					$_str_char = $this->str_char[ count( $this->str_char ) - 1 ];
					$_cur_string = $this->cur_string[ count( $this->cur_string ) - 1 ];
					$_quoted_string = $this->quoted_string[ count( $this->quoted_string ) - 1 ];
					$temp_add = $string[ $i ];

					// Add another string to the stack. Strings can't be nested inside of quotes, only parentheses,
					// but parentheticals can be nested more than once.
					if ( ')' === $_str_char && ( '(' === $string[ $i ] || '"' === $string[ $i ] || '\\' === $string[ $i ] ) && ! $this->escaped( $string, $i ) ) {
						$this->cur_string[] = $string[ $i ];
						$this->str_char[] = ( '(' === $string[ $i ] ) ? ')' : $string[ $i ];
						$this->from[] = 'instr';
						$this->quoted_string[] = ( ')' === $_str_char && '(' !== $string[ $i ] && '(' === trim( $_cur_string ) ) ? $_quoted_string : ( '(' !== $string[ $i ] );
						continue;
					}

					if ( ')' !== $_str_char && ( "\n" === $string[ $i ] || "\r" === $string[ $i ] ) && ! ( '\\' === $string[ $i - 1 ] && ! $this->escaped( $string, $i - 1 ) ) ) {
						$temp_add = '\\A';
						$this->log( 'Fixed incorrect newline in string', 'Warning' );
					}

					$_cur_string .= $temp_add;

					if ( $string[ $i ] === $_str_char && ! $this->escaped( $string, $i ) ) {
						$this->status = array_pop( $this->from );

						if ( ! preg_match( '|[' . implode( '', $this->data['csstidy']['whitespace'] ) . ']|uis', $_cur_string ) && 'content' !== $this->property ) {
							if ( ! $_quoted_string ) {
								if ( ')' !== $_str_char ) {
									// Convert properties like
									// font-family: 'Arial';
									// to
									// font-family: Arial;
									// or
									// url("abc")
									// to
									// url(abc)
									$_cur_string = substr( $_cur_string, 1, -1 );
								}
							} else {
								$_quoted_string = false;
							}
						}

						array_pop( $this->cur_string );
						array_pop( $this->quoted_string );
						array_pop( $this->str_char );

						if ( ')' === $_str_char ) {
							$_cur_string = '(' . trim( substr( $_cur_string, 1, -1 ) ) . ')';
						}

						if ( 'iv' === $this->status ) {
							if ( ! $_quoted_string ) {
								if ( false !== strpos( $_cur_string, ',' ) ) {
									// We can on only remove space next to ','
									$_cur_string = implode( ',', array_map( 'trim', explode( ',', $_cur_string ) ) );
								}
								// and multiple spaces (too expensive).
								if ( false !== strpos( $_cur_string, '  ' ) ) {
									$_cur_string = preg_replace( ',\s+,', ' ', $_cur_string );
								}
							}
							$this->sub_value .= $_cur_string;
						} elseif ( 'is' === $this->status ) {
							$this->selector .= $_cur_string;
						} elseif ( 'instr' === $this->status ) {
							$this->cur_string[ count( $this->cur_string ) - 1 ] .= $_cur_string;
						}
					} else {
						$this->cur_string[ count( $this->cur_string ) - 1 ] = $_cur_string;
					}
					break;
				/* Case in-comment */
				case 'ic':
					if ( '*' === $string[ $i ] && '/' === $string[ $i + 1 ] ) {
						$this->status = array_pop( $this->from );
						$i++;
						$this->_add_token( COMMENT, $cur_comment );
						$cur_comment = '';
					} else {
						$cur_comment .= $string[ $i ];
					}
					break;
			}
		}

		$this->optimise->postparse();
		$this->print->_reset();

		// Set locale back to original setting.
		@setlocale( LC_ALL, $old );

		return ! ( empty( $this->css ) && empty( $this->import ) && empty( $this->charset ) && empty( $this->tokens ) && empty( $this->namespace ) );
	}

	/**
	 * format() in font-face needs quoted values for somes browser (FF at least).
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Value.
	 * @return string String.
	 */
	protected function quote_font_format( $value ) {
		if ( 0 === strncmp( $value, 'format', 6 ) ) {
			$p = strpos( $value, ')', 7 );
			$end = substr( $value, $p );
			$format_strings = $this->parse_string_list( substr( $value, 7, $p - 7 ) );
			if ( ! $format_strings ) {
				$value = '';
			} else {
				$value = 'format(';
				foreach ( $format_strings as $format_string ) {
					$value .= '"' . str_replace( '"', '\\"', $format_string ) . '",';
				}
				$value = substr( $value, 0, -1 ) . $end;
			}
		}
		return $value;
	}

	/**
	 * Explodes selectors.
	 *
	 * @since 1.0.0
	 */
	protected function explode_selectors() {
		// Explode multiple selectors.
		if ( 1 === $this->get_cfg( 'merge_selectors' ) ) {
			$new_sels = array();
			$lastpos = 0;
			$this->sel_separate[] = strlen( $this->selector );
			foreach ( $this->sel_separate as $num => $pos ) {
				if ( ( count( $this->sel_separate ) - 1 ) === $num ) {
					$pos += 1;
				}

				$new_sels[] = substr( $this->selector, $lastpos, $pos - $lastpos - 1 );
				$lastpos = $pos;
			}

			if ( count( $new_sels ) > 1 ) {
				foreach ( $new_sels as $selector ) {
					if ( isset( $this->css[ $this->at ][ $this->selector ] ) ) {
						$this->merge_css_blocks( $this->at, $selector, $this->css[ $this->at ][ $this->selector ] );
					}
				}
				unset( $this->css[ $this->at ][ $this->selector ] );
			}
		}
		$this->sel_separate = array();
	}

	/**
	 * Checks if a character is escaped (and returns true if it is).
	 *
	 * @since 1.0.0
	 *
	 * @param string $string String.
	 * @param int    $pos    Position.
	 * @return bool [return value]
	 */
	public function escaped( &$string, $pos ) {
		return $pos ? ! ( @( '\\' !== $string[ $pos - 1 ] ) || $this->escaped( $string, $pos - 1 ) ) : false;
	}

	/**
	 * Adds a property with value to the existing CSS code.
	 *
	 * @since 1.0.0
	 *
	 * @param string $media    Media.
	 * @param string $selector Selector.
	 * @param string $property Property.
	 * @param string $new_val  New value.
	 */
	protected function css_add_property( $media, $selector, $property, $new_val ) {
		if ( $this->get_cfg( 'preserve_css' ) || '' === trim( $new_val ) ) {
			return;
		}

		$this->added = true;
		if ( isset( $this->css[ $media ][ $selector ][ $property ] ) ) {
			if ( ( $this->is_important( $this->css[ $media ][ $selector ][ $property ] ) && $this->is_important( $new_val ) ) || ! $this->is_important( $this->css[ $media ][ $selector ][ $property ] ) ) {
				$this->css[ $media ][ $selector ][ $property ] = trim( $new_val );
			}
		} else {
			$this->css[ $media ][ $selector ][ $property ] = trim( $new_val );
		}
	}

	/**
	 * Start a new media section.
	 *
	 * Check if the media is not already known, else rename it with extra spaces to avoid merging.
	 *
	 * @since 1.0.0
	 *
	 * @param string $media Media.
	 * @return string [return value]
	 */
	protected function css_new_media_section( $media ) {
		if ( $this->get_cfg( 'preserve_css' ) ) {
			return $media;
		}
		// If the last @media is the same as this, keep it.
		if ( ! $this->css || ! is_array( $this->css ) || empty( $this->css ) ) {
			return $media;
		}
		end( $this->css );
		$at = key( $this->css );
		if ( $at === $media ) {
			return $media;
		}
		while ( isset( $this->css[ $media ] ) ) {
			if ( is_numeric( $media ) ) {
				$media++;
			} else {
				$media .= ' ';
			}
		}
		return $media;
	}

	/**
	 * Start a new selector.
	 *
	 * If already referenced in this media section, rename it with extra space to avoid merging,
	 * except if merging is required, or last selector is the same (merge siblings).
	 * Never merge @font-face.
	 *
	 * @since 1.0.0
	 *
	 * @param string $media    Media.
	 * @param string $selector Selector.
	 * @return string [return value]
	 */
	protected function css_new_selector( $media, $selector ) {
		if ( $this->get_cfg( 'preserve_css' ) ) {
			return $selector;
		}
		$selector = trim( $selector );
		if ( 0 !== strncmp( $selector, '@font-face', 10 ) ) {
			if ( false !== $this->settings['merge_selectors'] ) {
				return $selector;
			}

			if ( ! $this->css || ! isset( $this->css[ $media ] ) || ! $this->css[ $media ] ) {
				return $selector;
			}

			// If last is the same, keep it.
			end( $this->css[ $media ] );
			$sel = key( $this->css[ $media ] );
			if ( $sel === $selector ) {
				return $selector;
			}
		}

		while ( isset( $this->css[ $media ][ $selector ] ) ) {
			$selector .= ' ';
		}
		return $selector;
	}

	/**
	 * Start a new property.
	 *
	 * If already references in this selector, rename it with extra space to avoid override.
	 *
	 * @since 1.0.0
	 *
	 * @param string $media    Media.
	 * @param string $selector Selector.
	 * @param string $property Property.
	 * @return string [return value]
	 */
	protected function css_new_property( $media, $selector, $property ) {
		if ( $this->get_cfg( 'preserve_css' ) ) {
			return $property;
		}
		if ( ! $this->css || ! isset( $this->css[ $media ][ $selector ] ) || ! $this->css[ $media ][ $selector ] ) {
			return $property;
		}

		while ( isset( $this->css[ $media ][ $selector ][ $property ] ) ) {
			$property .= ' ';
		}

		return $property;
	}

	/**
	 * Adds CSS to an existing media/selector.
	 *
	 * @since 1.0.0
	 *
	 * @param string $media    Media.
	 * @param string $selector Selector.
	 * @param array  $css_add  Additional CSS.
	 */
	public function merge_css_blocks( $media, $selector, array $css_add ) {
		foreach ( $css_add as $property => $value ) {
			$this->css_add_property( $media, $selector, $property, $value );
		}
	}

	/**
	 * Checks if $value is !important.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Value.
	 * @return bool Whether the value has the !important keyword.
	 */
	public function is_important( &$value ) {
		return (
			false !== strpos( $value, '!' ) // Quick test.
			&& ! strcasecmp( substr( str_replace( $this->data['csstidy']['whitespace'], '', $value ), -10, 10 ), '!important' ) );
	}

	/**
	 * Returns a value without !important.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Value.
	 * @return string Value without the !important;
	 */
	public function gvw_important( $value ) {
		if ( $this->is_important( $value ) ) {
			$value = trim( $value );
			$value = substr( $value, 0, -9 );
			$value = trim( $value );
			$value = substr( $value, 0, -1 );
			$value = trim( $value );
			return $value;
		}
		return $value;
	}

	/**
	 * Checks if the next word in a string from pos is a CSS property.
	 *
	 * @since 1.0.0
	 *
	 * @param string $istring String.
	 * @param int    $pos     Position.
	 * @return bool [return value]
	 */
	protected function property_is_next( $istring, $pos ) {
		$all_properties = &$this->data['csstidy']['all_properties'];
		$istring = substr( $istring, $pos, strlen( $istring ) - $pos );
		$pos = strpos( $istring, ':' );
		if ( false === $pos ) {
			return false;
		}
		$istring = strtolower( trim( substr( $istring, 0, $pos ) ) );
		if ( isset( $all_properties[ $istring ] ) ) {
			$this->log( 'Added semicolon to the end of declaration', 'Warning' );
			return true;
		}
		return false;
	}

	/**
	 * Checks if a property is valid.
	 *
	 * @since 1.0.0
	 *
	 * @param string $property Property.
	 * @return bool Whether the property is valid.
	 */
	public function property_is_valid( $property ) {
		$property = strtolower( $property );
		if ( in_array( trim( $property ), $this->data['csstidy']['multiple_properties'], true ) ) {
			$property = trim( $property );
		}
		$all_properties = &$this->data['csstidy']['all_properties'];
		return isset( $all_properties[ $property ] ) && false !== strpos( $all_properties[ $property ], strtoupper( $this->get_cfg( 'css_level' ) ) );
	}

	/**
	 * Accepts a list of strings (e.g. the argument to format() in a @font-face src property)
	 * and returns a list of the strings. Converts things like:
	 * format(abc) => format("abc")
	 * format(abc def) => format("abc","def")
	 * format(abc "def") => format("abc","def")
	 * format(abc, def, ghi) => format("abc","def","ghi")
	 * format("abc",'def') => format("abc","def")
	 * format("abc, def, ghi") => format("abc, def, ghi")
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $value [description]
	 * @return [type] [description]
	 */
	public function parse_string_list( $value ) {
		$value = trim( $value );

		// Case: empty
		if ( ! $value ) {
			return array();
		}

		$strings = array();

		$in_str = false;
		$current_string = '';

		for ( $i = 0, $_len = strlen( $value ); $i < $_len; $i++ ) {
			if ( ( ',' === $value[ $i ] || ' ' === $value[ $i ] ) && true === $in_str ) {
				$in_str = false;
				$strings[] = $current_string;
				$current_string = '';
			} elseif ( '"' === $value[ $i ] || "'" === $value[ $i ] ) {
				if ( $in_str === $value[ $i ] ) {
					$strings[] = $current_string;
					$in_str = false;
					$current_string = '';
					continue;
				} elseif ( ! $in_str ) {
					$in_str = $value[ $i ];
				}
			} else {
				if ( $in_str ) {
					$current_string .= $value[ $i ];
				} else {
					if ( ! preg_match( '/[\s,]/', $value[ $i ] ) ) {
						$in_str = true;
						$current_string = $value[ $i ];
					}
				}
			}
		}

		if ( $current_string ) {
			$strings[] = $current_string;
		}

		return $strings;
	}

} // class TablePress_CSSTidy
