<?php
/**
 * Contains Translation_Entry class
 *
 * @version $Id: entry.php 1157 2015-11-20 04:30:11Z dd32 $
 * @package pomo
 * @subpackage entry
 */

if ( ! class_exists( 'Translation_Entry', false ) ) :
	/**
	 * Translation_Entry class encapsulates a translatable string
	 */
	class Translation_Entry {

		/**
		 * Whether the entry contains a string and its plural form, default is false
		 *
		 * @var boolean
		 */
		public $is_plural = false;

		public $context             = null;
		public $singular            = null;
		public $plural              = null;
		public $translations        = array();
		public $translator_comments = '';
		public $extracted_comments  = '';
		public $references          = array();
		public $flags               = array();

		/**
		 * @param array $args associative array, support following keys:
		 *  - singular (string) -- the string to translate, if omitted and empty entry will be created
		 *  - plural (string) -- the plural form of the string, setting this will set {@link $is_plural} to true
		 *  - translations (array) -- translations of the string and possibly -- its plural forms
		 *  - context (string) -- a string differentiating two equal strings used in different contexts
		 *  - translator_comments (string) -- comments left by translators
		 *  - extracted_comments (string) -- comments left by developers
		 *  - references (array) -- places in the code this strings is used, in relative_to_root_path/file.php:linenum form
		 *  - flags (array) -- flags like php-format
		 */
		function __construct( $args = array() ) {
			// If no singular -- empty object.
			if ( ! isset( $args['singular'] ) ) {
				return;
			}
			// Get member variable values from args hash.
			foreach ( $args as $varname => $value ) {
				$this->$varname = $value;
			}
			if ( isset( $args['plural'] ) && $args['plural'] ) {
				$this->is_plural = true;
			}
			if ( ! is_array( $this->translations ) ) {
				$this->translations = array();
			}
			if ( ! is_array( $this->references ) ) {
				$this->references = array();
			}
			if ( ! is_array( $this->flags ) ) {
				$this->flags = array();
			}
		}

		/**
		 * PHP4 constructor.
		 *
		 * @deprecated 5.4.0 Use __construct() instead.
		 *
		 * @see Translation_Entry::__construct()
		 */
		public function Translation_Entry( $args = array() ) {
			_deprecated_constructor( self::class, '5.4.0', static::class );
			self::__construct( $args );
		}

		/**
		 * Generates a unique key for this entry
		 *
		 * @return string|bool the key or false if the entry is empty
		 */
		function key() {
			if ( null === $this->singular || '' === $this->singular ) {
				return false;
			}

			// Prepend context and EOT, like in MO files.
			$key = ! $this->context ? $this->singular : $this->context . "\4" . $this->singular;
			// Standardize on \n line endings.
			$key = str_replace( array( "\r\n", "\r" ), "\n", $key );

			return $key;
		}

		/**
		 * @param object $other
		 */
		function merge_with( &$other ) {
			$this->flags      = array_unique( array_merge( $this->flags, $other->flags ) );
			$this->references = array_unique( array_merge( $this->references, $other->references ) );
			if ( $this->extracted_comments != $other->extracted_comments ) {
				$this->extracted_comments .= $other->extracted_comments;
			}

		}
	}
endif;
