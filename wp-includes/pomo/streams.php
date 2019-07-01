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
	class POMO_Reader {

		var $endian = 'little';
		var $_post  = '';

		/**
		 * PHP5 constructor.
		 */
		function __construct() {
			$this->is_overloaded = ( ( ini_get( 'mbstring.func_overload' ) & 2 ) != 0 ) && function_exists( 'mb_substr' );
			$this->_pos          = 0;
		}

		/**
		 * PHP4 constructor.
		 */
		public function POMO_Reader() {
			self::__construct();
		}

		/**
		 * Sets the endianness of the file.
		 *
		 * @param string $endian Set the endianness of the file. Accepts 'big', or 'little'.
		 */
		function setEndian( $endian ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
			$this->endian = $endian;
		}

		/**
		 * Reads a 32bit Integer from the Stream
		 *
		 * @return mixed The integer, corresponding to the next 32 bits from
		 *  the stream of false if there are not enough bytes or on error
		 */
		function readint32() {
			$bytes = $this->read( 4 );
			if ( 4 != $this->strlen( $bytes ) ) {
				return false;
			}
			$endian_letter = ( 'big' == $this->endian ) ? 'N' : 'V';
			$int           = unpack( $endian_letter, $bytes );
			return reset( $int );
		}

		/**
		 * Reads an array of 32-bit Integers from the Stream
		 *
		 * @param integer $count How many elements should be read
		 * @return mixed Array of integers or false if there isn't
		 *  enough data or on error
		 */
		function readint32array( $count ) {
			$bytes = $this->read( 4 * $count );
			if ( 4 * $count != $this->strlen( $bytes ) ) {
				return false;
			}
			$endian_letter = ( 'big' == $this->endian ) ? 'N' : 'V';
			return unpack( $endian_letter . $count, $bytes );
		}

		/**
		 * @param string $string
		 * @param int    $start
		 * @param int    $length
		 * @return string
		 */
		function substr( $string, $start, $length ) {
			if ( $this->is_overloaded ) {
				return mb_substr( $string, $start, $length, 'ascii' );
			} else {
				return substr( $string, $start, $length );
			}
		}

		/**
		 * @param string $string
		 * @return int
		 */
		function strlen( $string ) {
			if ( $this->is_overloaded ) {
				return mb_strlen( $string, 'ascii' );
			} else {
				return strlen( $string );
			}
		}

		/**
		 * @param string $string
		 * @param int    $chunk_size
		 * @return array
		 */
		function str_split( $string, $chunk_size ) {
			if ( ! function_exists( 'str_split' ) ) {
				$length = $this->strlen( $string );
				$out    = array();
				for ( $i = 0; $i < $length; $i += $chunk_size ) {
					$out[] = $this->substr( $string, $i, $chunk_size );
				}
				return $out;
			} else {
				return str_split( $string, $chunk_size );
			}
		}

		/**
		 * @return int
		 */
		function pos() {
			return $this->_pos;
		}

		/**
		 * @return true
		 */
		function is_resource() {
			return true;
		}

		/**
		 * @return true
		 */
		function close() {
			return true;
		}
	}
endif;

if ( ! class_exists( 'POMO_FileReader', false ) ) :
	class POMO_FileReader extends POMO_Reader {

		/**
		 * @param string $filename
		 */
		function __construct( $filename ) {
			parent::POMO_Reader();
			$this->_f = fopen( $filename, 'rb' );
		}

		/**
		 * PHP4 constructor.
		 */
		public function POMO_FileReader( $filename ) {
			self::__construct( $filename );
		}

		/**
		 * @param int $bytes
		 * @return string|false Returns read string, otherwise false.
		 */
		function read( $bytes ) {
			return fread( $this->_f, $bytes );
		}

		/**
		 * @param int $pos
		 * @return boolean
		 */
		function seekto( $pos ) {
			if ( -1 == fseek( $this->_f, $pos, SEEK_SET ) ) {
				return false;
			}
			$this->_pos = $pos;
			return true;
		}

		/**
		 * @return bool
		 */
		function is_resource() {
			return is_resource( $this->_f );
		}

		/**
		 * @return bool
		 */
		function feof() {
			return feof( $this->_f );
		}

		/**
		 * @return bool
		 */
		function close() {
			return fclose( $this->_f );
		}

		/**
		 * @return string
		 */
		function read_all() {
			$all = '';
			while ( ! $this->feof() ) {
				$all .= $this->read( 4096 );
			}
			return $all;
		}
	}
endif;

if ( ! class_exists( 'POMO_StringReader', false ) ) :
	/**
	 * Provides file-like methods for manipulating a string instead
	 * of a physical file.
	 */
	class POMO_StringReader extends POMO_Reader {

		var $_str = '';

		/**
		 * PHP5 constructor.
		 */
		function __construct( $str = '' ) {
			parent::POMO_Reader();
			$this->_str = $str;
			$this->_pos = 0;
		}

		/**
		 * PHP4 constructor.
		 */
		public function POMO_StringReader( $str = '' ) {
			self::__construct( $str );
		}

		/**
		 * @param string $bytes
		 * @return string
		 */
		function read( $bytes ) {
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
		function seekto( $pos ) {
			$this->_pos = $pos;
			if ( $this->strlen( $this->_str ) < $this->_pos ) {
				$this->_pos = $this->strlen( $this->_str );
			}
			return $this->_pos;
		}

		/**
		 * @return int
		 */
		function length() {
			return $this->strlen( $this->_str );
		}

		/**
		 * @return string
		 */
		function read_all() {
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
		function __construct( $filename ) {
			parent::POMO_StringReader();
			$this->_str = file_get_contents( $filename );
			if ( false === $this->_str ) {
				return false;
			}
			$this->_pos = 0;
		}

		/**
		 * PHP4 constructor.
		 */
		public function POMO_CachedFileReader( $filename ) {
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
			parent::POMO_CachedFileReader( $filename );
		}

		/**
		 * PHP4 constructor.
		 */
		function POMO_CachedIntFileReader( $filename ) {
			self::__construct( $filename );
		}
	}
endif;

