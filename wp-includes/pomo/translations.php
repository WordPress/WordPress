<?php
/**
 * Class for a set of entries for translation and their associated headers
 *
 * @version $Id: translations.php 1157 2015-11-20 04:30:11Z dd32 $
 * @package pomo
 * @subpackage translations
 * @since 2.8.0
 */

require_once __DIR__ . '/plural-forms.php';
require_once __DIR__ . '/entry.php';

if ( ! class_exists( 'Translations', false ) ) :
	/**
	 * Translations class.
	 *
	 * @since 2.8.0
	 */
	#[AllowDynamicProperties]
	class Translations {
		/**
		 * List of translation entries.
		 *
		 * @since 2.8.0
		 *
		 * @var Translation_Entry[]
		 */
		public $entries = array();

		/**
		 * List of translation headers.
		 *
		 * @since 2.8.0
		 *
		 * @var array<string, string>
		 */
		public $headers = array();

		/**
		 * Adds an entry to the PO structure.
		 *
		 * @since 2.8.0
		 *
		 * @param array|Translation_Entry $entry
		 * @return bool True on success, false if the entry doesn't have a key.
		 */
		public function add_entry( $entry ) {
			if ( is_array( $entry ) ) {
				$entry = new Translation_Entry( $entry );
			}
			$key = $entry->key();
			if ( false === $key ) {
				return false;
			}
			$this->entries[ $key ] = &$entry;
			return true;
		}

		/**
		 * Adds or merges an entry to the PO structure.
		 *
		 * @since 2.8.0
		 *
		 * @param array|Translation_Entry $entry
		 * @return bool True on success, false if the entry doesn't have a key.
		 */
		public function add_entry_or_merge( $entry ) {
			if ( is_array( $entry ) ) {
				$entry = new Translation_Entry( $entry );
			}
			$key = $entry->key();
			if ( false === $key ) {
				return false;
			}
			if ( isset( $this->entries[ $key ] ) ) {
				$this->entries[ $key ]->merge_with( $entry );
			} else {
				$this->entries[ $key ] = &$entry;
			}
			return true;
		}

		/**
		 * Sets $header PO header to $value
		 *
		 * If the header already exists, it will be overwritten
		 *
		 * TODO: this should be out of this class, it is gettext specific
		 *
		 * @since 2.8.0
		 *
		 * @param string $header header name, without trailing :
		 * @param string $value header value, without trailing \n
		 */
		public function set_header( $header, $value ) {
			$this->headers[ $header ] = $value;
		}

		/**
		 * Sets translation headers.
		 *
		 * @since 2.8.0
		 *
		 * @param array $headers Associative array of headers.
		 */
		public function set_headers( $headers ) {
			foreach ( $headers as $header => $value ) {
				$this->set_header( $header, $value );
			}
		}

		/**
		 * Returns a given translation header.
		 *
		 * @since 2.8.0
		 *
		 * @param string $header
		 * @return string|false Header if it exists, false otherwise.
		 */
		public function get_header( $header ) {
			return isset( $this->headers[ $header ] ) ? $this->headers[ $header ] : false;
		}

		/**
		 * Returns a given translation entry.
		 *
		 * @since 2.8.0
		 *
		 * @param Translation_Entry $entry Translation entry.
		 * @return Translation_Entry|false Translation entry if it exists, false otherwise.
		 */
		public function translate_entry( &$entry ) {
			$key = $entry->key();
			return isset( $this->entries[ $key ] ) ? $this->entries[ $key ] : false;
		}

		/**
		 * Translates a singular string.
		 *
		 * @since 2.8.0
		 *
		 * @param string $singular
		 * @param string $context
		 * @return string
		 */
		public function translate( $singular, $context = null ) {
			$entry      = new Translation_Entry(
				array(
					'singular' => $singular,
					'context'  => $context,
				)
			);
			$translated = $this->translate_entry( $entry );
			return ( $translated && ! empty( $translated->translations ) ) ? $translated->translations[0] : $singular;
		}

		/**
		 * Given the number of items, returns the 0-based index of the plural form to use
		 *
		 * Here, in the base Translations class, the common logic for English is implemented:
		 *  0 if there is one element, 1 otherwise
		 *
		 * This function should be overridden by the subclasses. For example MO/PO can derive the logic
		 * from their headers.
		 *
		 * @since 2.8.0
		 *
		 * @param int $count Number of items.
		 * @return int Plural form to use.
		 */
		public function select_plural_form( $count ) {
			return 1 === (int) $count ? 0 : 1;
		}

		/**
		 * Returns the plural forms count.
		 *
		 * @since 2.8.0
		 *
		 * @return int Plural forms count.
		 */
		public function get_plural_forms_count() {
			return 2;
		}

		/**
		 * Translates a plural string.
		 *
		 * @since 2.8.0
		 *
		 * @param string $singular
		 * @param string $plural
		 * @param int    $count
		 * @param string $context
		 * @return string
		 */
		public function translate_plural( $singular, $plural, $count, $context = null ) {
			$entry              = new Translation_Entry(
				array(
					'singular' => $singular,
					'plural'   => $plural,
					'context'  => $context,
				)
			);
			$translated         = $this->translate_entry( $entry );
			$index              = $this->select_plural_form( $count );
			$total_plural_forms = $this->get_plural_forms_count();
			if ( $translated && 0 <= $index && $index < $total_plural_forms &&
				is_array( $translated->translations ) &&
				isset( $translated->translations[ $index ] ) ) {
				return $translated->translations[ $index ];
			} else {
				return 1 === (int) $count ? $singular : $plural;
			}
		}

		/**
		 * Merges other translations into the current one.
		 *
		 * @since 2.8.0
		 *
		 * @param Translations $other Another Translation object, whose translations will be merged in this one (passed by reference).
		 */
		public function merge_with( &$other ) {
			foreach ( $other->entries as $entry ) {
				$this->entries[ $entry->key() ] = $entry;
			}
		}

		/**
		 * Merges originals with existing entries.
		 *
		 * @since 2.8.0
		 *
		 * @param Translations $other
		 */
		public function merge_originals_with( &$other ) {
			foreach ( $other->entries as $entry ) {
				if ( ! isset( $this->entries[ $entry->key() ] ) ) {
					$this->entries[ $entry->key() ] = $entry;
				} else {
					$this->entries[ $entry->key() ]->merge_with( $entry );
				}
			}
		}
	}

	/**
	 * Gettext_Translations class.
	 *
	 * @since 2.8.0
	 */
	class Gettext_Translations extends Translations {

		/**
		 * Number of plural forms.
		 *
		 * @var int
		 *
		 * @since 2.8.0
		 */
		public $_nplurals;

		/**
		 * Callback to retrieve the plural form.
		 *
		 * @var callable
		 *
		 * @since 2.8.0
		 */
		public $_gettext_select_plural_form;

		/**
		 * The gettext implementation of select_plural_form.
		 *
		 * It lives in this class, because there are more than one descendant, which will use it and
		 * they can't share it effectively.
		 *
		 * @since 2.8.0
		 *
		 * @param int $count Plural forms count.
		 * @return int Plural form to use.
		 */
		public function gettext_select_plural_form( $count ) {
			if ( ! isset( $this->_gettext_select_plural_form ) || is_null( $this->_gettext_select_plural_form ) ) {
				list( $nplurals, $expression )     = $this->nplurals_and_expression_from_header( $this->get_header( 'Plural-Forms' ) );
				$this->_nplurals                   = $nplurals;
				$this->_gettext_select_plural_form = $this->make_plural_form_function( $nplurals, $expression );
			}
			return call_user_func( $this->_gettext_select_plural_form, $count );
		}

		/**
		 * Returns the nplurals and plural forms expression from the Plural-Forms header.
		 *
		 * @since 2.8.0
		 *
		 * @param string $header
		 * @return array{0: int, 1: string}
		 */
		public function nplurals_and_expression_from_header( $header ) {
			if ( preg_match( '/^\s*nplurals\s*=\s*(\d+)\s*;\s+plural\s*=\s*(.+)$/', $header, $matches ) ) {
				$nplurals   = (int) $matches[1];
				$expression = trim( $matches[2] );
				return array( $nplurals, $expression );
			} else {
				return array( 2, 'n != 1' );
			}
		}

		/**
		 * Makes a function, which will return the right translation index, according to the
		 * plural forms header.
		 *
		 * @since 2.8.0
		 *
		 * @param int    $nplurals
		 * @param string $expression
		 * @return callable
		 */
		public function make_plural_form_function( $nplurals, $expression ) {
			try {
				$handler = new Plural_Forms( rtrim( $expression, ';' ) );
				return array( $handler, 'get' );
			} catch ( Exception $e ) {
				// Fall back to default plural-form function.
				return $this->make_plural_form_function( 2, 'n != 1' );
			}
		}

		/**
		 * Adds parentheses to the inner parts of ternary operators in
		 * plural expressions, because PHP evaluates ternary operators from left to right
		 *
		 * @since 2.8.0
		 * @deprecated 6.5.0 Use the Plural_Forms class instead.
		 *
		 * @see Plural_Forms
		 *
		 * @param string $expression the expression without parentheses
		 * @return string the expression with parentheses added
		 */
		public function parenthesize_plural_exression( $expression ) {
			$expression .= ';';
			$res         = '';
			$depth       = 0;
			for ( $i = 0; $i < strlen( $expression ); ++$i ) {
				$char = $expression[ $i ];
				switch ( $char ) {
					case '?':
						$res .= ' ? (';
						++$depth;
						break;
					case ':':
						$res .= ') : (';
						break;
					case ';':
						$res  .= str_repeat( ')', $depth ) . ';';
						$depth = 0;
						break;
					default:
						$res .= $char;
				}
			}
			return rtrim( $res, ';' );
		}

		/**
		 * Prepare translation headers.
		 *
		 * @since 2.8.0
		 *
		 * @param string $translation
		 * @return array<string, string> Translation headers
		 */
		public function make_headers( $translation ) {
			$headers = array();
			// Sometimes \n's are used instead of real new lines.
			$translation = str_replace( '\n', "\n", $translation );
			$lines       = explode( "\n", $translation );
			foreach ( $lines as $line ) {
				$parts = explode( ':', $line, 2 );
				if ( ! isset( $parts[1] ) ) {
					continue;
				}
				$headers[ trim( $parts[0] ) ] = trim( $parts[1] );
			}
			return $headers;
		}

		/**
		 * Sets translation headers.
		 *
		 * @since 2.8.0
		 *
		 * @param string $header
		 * @param string $value
		 */
		public function set_header( $header, $value ) {
			parent::set_header( $header, $value );
			if ( 'Plural-Forms' === $header ) {
				list( $nplurals, $expression )     = $this->nplurals_and_expression_from_header( $this->get_header( 'Plural-Forms' ) );
				$this->_nplurals                   = $nplurals;
				$this->_gettext_select_plural_form = $this->make_plural_form_function( $nplurals, $expression );
			}
		}
	}
endif;

if ( ! class_exists( 'NOOP_Translations', false ) ) :
	/**
	 * Provides the same interface as Translations, but doesn't do anything.
	 *
	 * @since 2.8.0
	 */
	#[AllowDynamicProperties]
	class NOOP_Translations {
		/**
		 * List of translation entries.
		 *
		 * @since 2.8.0
		 *
		 * @var Translation_Entry[]
		 */
		public $entries = array();

		/**
		 * List of translation headers.
		 *
		 * @since 2.8.0
		 *
		 * @var array<string, string>
		 */
		public $headers = array();

		public function add_entry( $entry ) {
			return true;
		}

		/**
		 * Sets a translation header.
		 *
		 * @since 2.8.0
		 *
		 * @param string $header
		 * @param string $value
		 */
		public function set_header( $header, $value ) {
		}

		/**
		 * Sets translation headers.
		 *
		 * @since 2.8.0
		 *
		 * @param array $headers
		 */
		public function set_headers( $headers ) {
		}

		/**
		 * Returns a translation header.
		 *
		 * @since 2.8.0
		 *
		 * @param string $header
		 * @return false
		 */
		public function get_header( $header ) {
			return false;
		}

		/**
		 * Returns a given translation entry.
		 *
		 * @since 2.8.0
		 *
		 * @param Translation_Entry $entry
		 * @return false
		 */
		public function translate_entry( &$entry ) {
			return false;
		}

		/**
		 * Translates a singular string.
		 *
		 * @since 2.8.0
		 *
		 * @param string $singular
		 * @param string $context
		 */
		public function translate( $singular, $context = null ) {
			return $singular;
		}

		/**
		 * Returns the plural form to use.
		 *
		 * @since 2.8.0
		 *
		 * @param int $count
		 * @return int
		 */
		public function select_plural_form( $count ) {
			return 1 === (int) $count ? 0 : 1;
		}

		/**
		 * Returns the plural forms count.
		 *
		 * @since 2.8.0
		 *
		 * @return int
		 */
		public function get_plural_forms_count() {
			return 2;
		}

		/**
		 * Translates a plural string.
		 *
		 * @since 2.8.0
		 *
		 * @param string $singular
		 * @param string $plural
		 * @param int    $count
		 * @param string $context
		 * @return string
		 */
		public function translate_plural( $singular, $plural, $count, $context = null ) {
			return 1 === (int) $count ? $singular : $plural;
		}

		/**
		 * Merges other translations into the current one.
		 *
		 * @since 2.8.0
		 *
		 * @param Translations $other
		 */
		public function merge_with( &$other ) {
		}
	}
endif;
