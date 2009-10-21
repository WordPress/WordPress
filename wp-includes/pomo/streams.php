<?php
/**
 * Classes, which help reading streams of data from files.
 * Based on the classes from Danilo Segan <danilo@kvota.net>
 *
 * @version $Id: streams.php 223 2009-09-07 21:20:13Z nbachiyski $
 * @package pomo
 * @subpackage streams
 */


if ( !class_exists( 'POMO_StringReader' ) ):
/**
 * Provides file-like methods for manipulating a string instead
 * of a physical file.
 */
class POMO_StringReader {
  var $_pos;
  var $_str;

	function POMO_StringReader($str = '') {
		$this->_str = $str;
		$this->_pos = 0;
		$this->is_overloaded = ((ini_get("mbstring.func_overload") & 2) != 0) && function_exists('mb_substr');
	}

	function _substr($string, $start, $length) {
		if ($this->is_overloaded) {
			return mb_substr($string,$start,$length,'ascii');
		} else {
			return substr($string,$start,$length);
		}
	}
	
	function _strlen($string) {
		if ($this->is_overloaded) {
			return mb_strlen($string,'ascii');
		} else {
			return strlen($string);
		}
	}

	function read($bytes) {
		$data = $this->_substr($this->_str, $this->_pos, $bytes);
		$this->_pos += $bytes;
		if ($this->_strlen($this->_str) < $this->_pos) $this->_pos = $this->_strlen($this->_str);
		return $data;
	}

	function seekto($pos) {
		$this->_pos = $pos;
		if ($this->_strlen($this->_str) < $this->_pos) $this->_pos = $this->_strlen($this->_str);
		return $this->_pos;
	}

	function pos() {
		return $this->_pos;
	}

	function length() {
		return $this->_strlen($this->_str);
	}

}
endif;

if ( !class_exists( 'POMO_CachedFileReader' ) ):
/**
 * Reads the contents of the file in the beginning.
 */
class POMO_CachedFileReader extends POMO_StringReader {
	function POMO_CachedFileReader($filename) {
		parent::POMO_StringReader();
		$this->_str = file_get_contents($filename);
		if (false === $this->_str)
			return false;
		$this->_pos = 0;
	}
}
endif;

if ( !class_exists( 'POMO_CachedIntFileReader' ) ):
/**
 * Allows reading integers from a file.
 */
class POMO_CachedIntFileReader extends POMO_CachedFileReader {

	var $endian = 'little';

	/**
	 * Opens a file and caches it.
	 *
	 * @param $filename string name of the file to be opened
	 * @param $endian string endianness of the words in the file, allowed
	 * 	values are 'little' or 'big'. Default value is 'little'
	 */
	function POMO_CachedIntFileReader($filename, $endian = 'little') {
		$this->endian = $endian;
		parent::POMO_CachedFileReader($filename);
	}

	/**
	 * Sets the endianness of the file.
	 *
	 * @param $endian string 'big' or 'little'
	 */
	function setEndian($endian) {
		$this->endian = $endian;
	}

	/**
	 * Reads a 32bit Integer from the Stream
	 *
	 * @return mixed The integer, corresponding to the next 32 bits from
	 * 	the stream of false if there are not enough bytes or on error
	 */
	function readint32() {
		$bytes = $this->read(4);
		if (4 != $this->_strlen($bytes))
			return false;
		$endian_letter = ('big' == $this->endian)? 'N' : 'V';
		$int = unpack($endian_letter, $bytes);
		return array_shift($int);
	}

	/**
	 * Reads an array of 32-bit Integers from the Stream
	 *
	 * @param integer count How many elements should be read
	 * @return mixed Array of integers or false if there isn't
	 * 	enough data or on error
	 */
	function readint32array($count) {
		$bytes = $this->read(4 * $count);
		if (4*$count != $this->_strlen($bytes))
			return false;
		$endian_letter = ('big' == $this->endian)? 'N' : 'V';
		return unpack($endian_letter.$count, $bytes);
	}
}
endif;