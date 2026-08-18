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
	 * Translation_Entry class encapsulates a translatable string.
	 *
	 * @since 2.8.0
	 */
	#[AllowDynamicProperties]
	class Translation_Entry {

		/**
		 * Whether the entry contains a string and its plural form, default is false.
		 *
		 * @var bool
		 */
		public $is_plural = false;

		/**
		 * The string to translate.
		 *
		 * @var string|null
		 */
		public $singular = null;

		/**
		 * The plural form of the string.
		 *
		 * @var string|null
		 */
		public $plural = null;

		/**
		 * Translations of the string and possibly its plural forms.
		 *
		 * Plural forms which have not been filled in are null.
		 *
		 * @var (string|null)[]
		 */
		public $translations = array();

		/**
		 * Context of the string, used to differentiate two equal strings used in different contexts.
		 *
		 * @var string|null
		 */
		public $context = null;

		/**
		 * Comments left by translators.
		 *
		 * @var string
		 */
		public $translator_comments = '';

		/**
		 * Comments left by developers.
		 *
		 * @var string
		 */
		public $extracted_comments = '';

		/**
		 * Places in the code this string is used, in relative_to_root_path/file.php:linenum form.
		 *
		 * @var string[]
		 */
		public $references = array();

		/**
		 * Flags like php-format.
		 *
		 * @var string[]
		 */
		public $flags = array();

		/**
		 * Constructor.
		 *
		 * @since 2.8.0
		 *
		 * @param array $args {
		 *     Optional. Array of arguments. Default empty array.
		 *
		 *     @type string          $singular            The string to translate, if omitted an
		 *                                                empty entry will be created.
		 *     @type string          $plural              The plural form of the string, setting
		 *                                                this will set `$is_plural` to true.
		 *     @type (string|null)[] $translations        Translations of the string and possibly
		 *                                                its plural forms.
		 *     @type string          $context             A string differentiating two equal strings
		 *                                                used in different contexts.
		 *     @type string          $translator_comments Comments left by translators.
		 *     @type string          $extracted_comments  Comments left by developers.
		 *     @type string[]        $references          Places in the code this string is used, in
		 *                                                relative_to_root_path/file.php:linenum form.
		 *     @type string[]        $flags               Flags like php-format.
		 * }
		 */
		public function __construct( $args = array() ) {
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
		 * @since 2.8.0
		 * @deprecated 5.4.0 Use __construct() instead.
		 *
		 * @see Translation_Entry::__construct()
		 *
		 * @param array $args Optional. Array of arguments. Default empty array.
		 */
		public function Translation_Entry( $args = array() ) {
			_deprecated_constructor( self::class, '5.4.0', static::class );
			self::__construct( $args );
		}

		/**
		 * Generates a unique key for this entry.
		 *
		 * @since 2.8.0
		 *
		 * @return string|false The key or false if the entry is null.
		 */
		public function key() {
			if ( null === $this->singular ) {
				return false;
			}

			// Prepend context and EOT, like in MO files.
			$key = ! $this->context ? $this->singular : $this->context . "\4" . $this->singular;
			// Standardize on \n line endings.
			$key = str_replace( array( "\r\n", "\r" ), "\n", $key );

			return $key;
		}

		/**
		 * Merges another translation entry with the current one.
		 *
		 * @since 2.8.0
		 *
		 * @param Translation_Entry $other Other translation entry.
		 */
		public function merge_with( &$other ) {
			$this->flags      = array_unique( array_merge( $this->flags, $other->flags ) );
			$this->references = array_unique( array_merge( $this->references, $other->references ) );
			if ( $this->extracted_comments !== $other->extracted_comments ) {
				$this->extracted_comments .= $other->extracted_comments;
			}
		}
	}
endif;
