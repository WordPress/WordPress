<?php
/**
 * I18N: WP_Translation_File class.
 *
 * @package WordPress
 * @subpackage I18N
 * @since 6.5.0
 */

/**
 * Class WP_Translation_File.
 *
 * @since 6.5.0
 */
abstract class WP_Translation_File {
	/**
	 * List of headers.
	 *
	 * @since 6.5.0
	 * @var array<string, string>
	 */
	protected $headers = array();

	/**
	 * Whether file has been parsed.
	 *
	 * @since 6.5.0
	 * @var bool
	 */
	protected $parsed = false;

	/**
	 * Error information.
	 *
	 * @since 6.5.0
	 * @var string|null Error message or null if no error.
	 */
	protected $error;

	/**
	 * File name.
	 *
	 * @since 6.5.0
	 * @var string
	 */
	protected $file = '';

	/**
	 * Translation entries.
	 *
	 * @since 6.5.0
	 * @var array<string, string>
	 */
	protected $entries = array();

	/**
	 * Plural forms function.
	 *
	 * @since 6.5.0
	 * @var callable|null Plural forms.
	 */
	protected $plural_forms = null;

	/**
	 * Constructor.
	 *
	 * @since 6.5.0
	 *
	 * @param string $file File to load.
	 */
	protected function __construct( string $file ) {
		$this->file = $file;
	}

	/**
	 * Creates a new WP_Translation_File instance for a given file.
	 *
	 * @since 6.5.0
	 *
	 * @param string      $file     File name.
	 * @param string|null $filetype Optional. File type. Default inferred from file name.
	 * @return false|WP_Translation_File
	 */
	public static function create( string $file, string $filetype = null ) {
		if ( ! is_readable( $file ) ) {
			return false;
		}

		if ( null === $filetype ) {
			$pos = strrpos( $file, '.' );
			if ( false !== $pos ) {
				$filetype = substr( $file, $pos + 1 );
			}
		}

		switch ( $filetype ) {
			case 'mo':
				return new WP_Translation_File_MO( $file );
			case 'php':
				return new WP_Translation_File_PHP( $file );
			default:
				return false;
		}
	}

	/**
	 * Creates a new WP_Translation_File instance for a given file.
	 *
	 * @since 6.5.0
	 *
	 * @param string $file     Source file name.
	 * @param string $filetype Desired target file type.
	 * @return string|false Transformed translation file contents on success, false otherwise.
	 */
	public static function transform( string $file, string $filetype ) {
		$source = self::create( $file );

		if ( false === $source ) {
			return false;
		}

		switch ( $filetype ) {
			case 'mo':
				$destination = new WP_Translation_File_MO( '' );
				break;
			case 'php':
				$destination = new WP_Translation_File_PHP( '' );
				break;
			default:
				return false;
		}

		$success = $destination->import( $source );

		if ( ! $success ) {
			return false;
		}

		return $destination->export();
	}

	/**
	 * Returns all headers.
	 *
	 * @since 6.5.0
	 *
	 * @return array<string, string> Headers.
	 */
	public function headers(): array {
		if ( ! $this->parsed ) {
			$this->parse_file();
		}
		return $this->headers;
	}

	/**
	 * Returns all entries.
	 *
	 * @since 6.5.0
	 *
	 * @return array<string, string[]> Entries.
	 */
	public function entries(): array {
		if ( ! $this->parsed ) {
			$this->parse_file();
		}

		return $this->entries;
	}

	/**
	 * Returns the current error information.
	 *
	 * @since 6.5.0
	 *
	 * @return string|null Error message or null if no error.
	 */
	public function error() {
		return $this->error;
	}

	/**
	 * Returns the file name.
	 *
	 * @since 6.5.0
	 *
	 * @return string File name.
	 */
	public function get_file(): string {
		return $this->file;
	}

	/**
	 * Translates a given string.
	 *
	 * @since 6.5.0
	 *
	 * @param string $text String to translate.
	 * @return false|string Translation(s) on success, false otherwise.
	 */
	public function translate( string $text ) {
		if ( ! $this->parsed ) {
			$this->parse_file();
		}

		return $this->entries[ $text ] ?? false;
	}

	/**
	 * Returns the plural form for a given number.
	 *
	 * @since 6.5.0
	 *
	 * @param int $number Count.
	 * @return int Plural form.
	 */
	public function get_plural_form( int $number ): int {
		if ( ! $this->parsed ) {
			$this->parse_file();
		}

		if ( null === $this->plural_forms && isset( $this->headers['plural-forms'] ) ) {
			$expression         = $this->get_plural_expression_from_header( $this->headers['plural-forms'] );
			$this->plural_forms = $this->make_plural_form_function( $expression );
		}

		if ( is_callable( $this->plural_forms ) ) {
			/**
			 * Plural form.
			 *
			 * @var int $result Plural form.
			 */
			$result = call_user_func( $this->plural_forms, $number );

			return $result;
		}

		// Default plural form matches English, only "One" is considered singular.
		return ( 1 === $number ? 0 : 1 );
	}

	/**
	 * Returns the plural forms expression as a tuple.
	 *
	 * @since 6.5.0
	 *
	 * @param string $header Plural-Forms header string.
	 * @return string Plural forms expression.
	 */
	protected function get_plural_expression_from_header( string $header ): string {
		if ( preg_match( '/^\s*nplurals\s*=\s*(\d+)\s*;\s+plural\s*=\s*(.+)$/', $header, $matches ) ) {
			return trim( $matches[2] );
		}

		return 'n != 1';
	}

	/**
	 * Makes a function, which will return the right translation index, according to the
	 * plural forms header.
	 *
	 * @since 6.5.0
	 *
	 * @param string $expression Plural form expression.
	 * @return callable(int $num): int Plural forms function.
	 */
	protected function make_plural_form_function( string $expression ): callable {
		try {
			$handler = new Plural_Forms( rtrim( $expression, ';' ) );
			return array( $handler, 'get' );
		} catch ( Exception $e ) {
			// Fall back to default plural-form function.
			return $this->make_plural_form_function( 'n != 1' );
		}
	}

	/**
	 * Imports translations from another file.
	 *
	 * @since 6.5.0
	 *
	 * @param WP_Translation_File $source Source file.
	 * @return bool True on success, false otherwise.
	 */
	protected function import( WP_Translation_File $source ): bool {
		if ( null !== $source->error() ) {
			return false;
		}

		$this->headers = $source->headers();
		$this->entries = $source->entries();
		$this->error   = $source->error();

		return null === $this->error;
	}

	/**
	 * Parses the file.
	 *
	 * @since 6.5.0
	 */
	abstract protected function parse_file();

	/**
	 * Exports translation contents as a string.
	 *
	 * @since 6.5.0
	 *
	 * @return string Translation file contents.
	 */
	abstract public function export();
}
