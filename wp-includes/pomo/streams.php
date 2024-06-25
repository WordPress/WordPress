<?php
/**
 * Classes, which help reading streams of data from files.
 * Based on the classes from Danilo Segan <danilo@kvota.net>
 *
 * @version $Id: streams.php 1157 2015-11-20 04:30:11Z dd32 $
 * @package pomo
 * @subpackage streams
 */

if ( ! class_exists( 'POMO_Reader', false ) ) :
	#[AllowDynamicProperties]
	class POMO_Reader {

		public $endian = 'little';
		public $_pos;
		public $is_overloaded;

		/**
		 * PHP5 constructor.
		 */
		public function __construct() {
			if ( function_exists( 'mb_substr' )
				&& ( (int) ini_get( 'mbstring.func_overload' ) & 2 ) // phpcs:ignore PHPCompatibility.IniDirectives.RemovedIniDirectives.mbstring_func_overloadDeprecated
			) {
				$this->is_overloaded = true;
			} else {
				$this->is_overloaded = false;
			}

			$this->_pos = 0;
		}

		/**
		 * PHP4 constructor.
		 *
		 * @deprecated 5.4.0 Use __construct() instead.
		 *
		 * @see POMO_Reader::__construct()
		 */
		public function POMO_Reader() {
			_deprecated_constructor( self::class, '5.4.0', static::class );
			self::__construct();
		}

		/**
		 * Sets the endianness of the file.
		 *
		 * @param string $endian Set the endianness of the file. Accepts 'big', or 'little'.
		 */
		public function setEndian( $endian ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
			$this->endian = $endian;
		}

		/**
		 * Reads a 32bit Integer from the Stream
		 *
		 * @return mixed The integer, corresponding to the next 32 bits from
		 *  the stream of false if there are not enough bytes or on error
		 */
		public function readint32() {
			$bytes = $this->read( 4 );
			if ( 4 !== $this->strlen( $bytes ) ) {
				return false;
			}
			$endian_letter = ( 'big' === $this->endian ) ? 'N' : 'V';
			$int           = unpack( $endian_letter, $bytes );
			return reset( $int );
		}

		/**
		 * Reads an array of 32-bit Integers from the Stream
		 *
		 * @param int $count How many elements should be read
		 * @return mixed Array of integers or false if there isn't
		 *  enough data or on error
		 */
		public function readint32array( $count ) {
			$bytes = $this->read( 4 * $count );
			if ( 4 * $count !== $this->strlen( $bytes ) ) {
				return false;
			}
			$endian_letter = ( 'big' === $this->endian ) ? 'N' : 'V';
			return unpack( $endian_letter . $count, $bytes );
		}

		/**
		 * @param string $input_string
		 * @param int    $start
		 * @param int    $length
		 * @return string
		 */
		public function substr( $input_string, $start, $length ) {
			if ( $this->is_overloaded ) {
				return mb_substr( $input_string, $start, $length, 'ascii' );
			} else {
				return substr( $input_string, $start, $length );
			}
		}

		/**
		 * @param string $input_string
		 * @return int
		 */
		public function strlen( $input_string ) {
			if ( $this->is_overloaded ) {
				return mb_strlen( $input_string, 'ascii' );
			} else {
				return strlen( $input_string );
			}
		}

		/**
		 * @param string $input_string
		 * @param int    $chunk_size
		 * @return array
		 */
		public function str_split( $input_string, $chunk_size ) {
			if ( ! function_exists( 'str_split' ) ) {
				$length = $this->strlen( $input_string );
				$out    = array();
				for ( $i = 0; $i < $length; $i += $chunk_size ) {
					$out[] = $this->substr( $input_string, $i, $chunk_size );
				}
				return $out;
			} else {
				return str_split( $input_string, $chunk_size );
			}
		}

		/**
		 * @return int
		 */
		public function pos() {
			return $this->_pos;
		}

		/**
		 * @return true
		 */
		public function is_resource() {
			return true;
		}

		/**
		 * @return true
		 */
		public function close() {
			return true;
		}
	}
endif;

if ( ! class_exists( 'POMO_FileReader', false ) ) :
	class POMO_FileReader extends POMO_Reader {

		/**
		 * File pointer resource.
		 *
		 * @var resource|false
		 */
		public $_f;

		/**
		 * @param string $filename
		 */
		public function __construct( $filename ) {
			parent::__construct();
			$this->_f = fopen( $filename, 'rb' );
		}

		/**
		 * PHP4 constructor.
		 *
		 * @deprecated 5.4.0 Use __construct() instead.
		 *
		 * @see POMO_FileReader::__construct()
		 */
		public function POMO_FileReader( $filename ) {
			_deprecated_constructor( self::class, '5.4.0', static::class );
			self::__construct( $filename );
		}

		/**
		 * @param int $bytes
		 * @return string|false Returns read string, otherwise false.
		 */
		public function read( $bytes ) {
			return fread( $this->_f, $bytes );
		}

		/**
		 * @param int $pos
		 * @return bool
		 */
		public function seekto( $pos ) {
			if ( -1 === fseek( $this->_f, $pos, SEEK_SET ) ) {
				return false;
			}
			$this->_pos = $pos;
			return true;
		}

		/**
		 * @return bool
		 */
		public function is_resource() {
			return is_resource( $this->_f );
		}

		/**
		 * @return bool
		 */
		public function feof() {
			return feof( $this->_f );
		}

		/**
		 * @return bool
		 */
		public function close() {
			return fclose( $this->_f );
		}

		/**
		 * @return string
		 */
		public function read_all() {
			return stream_get_contents( $this->_f );
		}
	}
endif;

if ( ! class_exists( 'POMO_StringReader', false ) ) :
	/**
	 * Provides file-like methods for manipulating a string instead
	 * of a physical file.
	 */
	class POMO_StringReader extends POMO_Reader {

		public $_str = '';

		/**
		 * PHP5 constructor.
		 */
		public function __construct( $str = '' ) {
			parent::__construct();
			$this->_str = $str;
			$this->_pos = 0;
		}

		/**
		 * PHP4 constructor.
		 *
		 * @deprecated 5.4.0 Use __construct() instead.
		 *
		 * @see POMO_StringReader::__construct()
		 */
		public function POMO_StringReader( $str = '' ) {
			_deprecated_constructor( self::class, '5.4.0', static::class );
			self::__construct( $str );
		}

		/**
		 * @param string $bytes
		 * @return string
		 */
		public function read( $bytes ) {
			$data        = $this->substr( $this->_str, $this->_pos, $bytes );
			$this->_pos += $bytes;
			if ( $this->strlen( $this->_str ) < $this->_pos ) {
				$this->_pos = $this->strlen( $this->_str );
			}
			return $data;
		}

		/**
		 * @param int $pos
		 * @return int
		 */
		public function seekto( $pos ) {
			$this->_pos = $pos;
			if ( $this->strlen( $this->_str ) < $this->_pos ) {
				$this->_pos = $this->strlen( $this->_str );
			}
			return $this->_pos;
		}

		/**
		 * @return int
		 */
		public function length() {
			return $this->strlen( $this->_str );
		}

		/**
		 * @return string
		 */
		public function read_all() {
			return $this->substr( $this->_str, $this->_pos, $this->strlen( $this->_str ) );
		}
	}
endif;

if ( ! class_exists( 'POMO_CachedFileReader', false ) ) :
	/**
	 * Reads the contents of the file in the beginning.
	 */
	class POMO_CachedFileReader extends POMO_StringReader {
		/**
		 * PHP5 constructor.
		 */
		public function __construct( $filename ) {
			parent::__construct();
			$this->_str = file_get_contents( $filename );
			if ( false === $this->_str ) {
				return false;
			}
			$this->_pos = 0;
		}

		/**
		 * PHP4 constructor.
		 *
		 * @deprecated 5.4.0 Use __construct() instead.
		 *
		 * @see POMO_CachedFileReader::__construct()
		 */
		public function POMO_CachedFileReader( $filename ) {
			_deprecated_constructor( self::class, '5.4.0', static::class );
			self::__construct( $filename );
		}
	}
endif;

if ( ! class_exists( 'POMO_CachedIntFileReader', false ) ) :
	/**
	 * Reads the contents of the file in the beginning.
	 */
	class POMO_CachedIntFileReader extends POMO_CachedFileReader {
		/**
		 * PHP5 constructor.
		 */
		public function __construct( $filename ) {
			parent::__construct( $filename );
		}

		/**
		 * PHP4 constructor.
		 *
		 * @deprecated 5.4.0 Use __construct() instead.
		 *
		 * @see POMO_CachedIntFileReader::__construct()
		 */
		public function POMO_CachedIntFileReader( $filename ) {
			_deprecated_constructor( self::class, '5.4.0', static::class );
			self::__construct( $filename );
		}
	}
endif;
