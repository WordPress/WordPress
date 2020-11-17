<?php

/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <info@getid3.org>               //
//  available at https://github.com/JamesHeinrich/getID3       //
//            or https://www.getid3.org                        //
//            or http://getid3.sourceforge.net                 //
//                                                             //
// getid3.lib.php - part of getID3()                           //
//  see readme.txt for more details                            //
//                                                            ///
/////////////////////////////////////////////////////////////////


class getid3_lib
{
	/**
	 * @param string      $string
	 * @param bool        $hex
	 * @param bool        $spaces
	 * @param string|bool $htmlencoding
	 *
	 * @return string
	 */
	public static function PrintHexBytes($string, $hex=true, $spaces=true, $htmlencoding='UTF-8') {
		$returnstring = '';
		for ($i = 0; $i < strlen($string); $i++) {
			if ($hex) {
				$returnstring .= str_pad(dechex(ord($string[$i])), 2, '0', STR_PAD_LEFT);
			} else {
				$returnstring .= ' '.(preg_match("#[\x20-\x7E]#", $string[$i]) ? $string[$i] : '¤');
			}
			if ($spaces) {
				$returnstring .= ' ';
			}
		}
		if (!empty($htmlencoding)) {
			if ($htmlencoding === true) {
				$htmlencoding = 'UTF-8'; // prior to getID3 v1.9.0 the function's 4th parameter was boolean
			}
			$returnstring = htmlentities($returnstring, ENT_QUOTES, $htmlencoding);
		}
		return $returnstring;
	}

	/**
	 * Truncates a floating-point number at the decimal point.
	 *
	 * @param float $floatnumber
	 *
	 * @return float|int returns int (if possible, otherwise float)
	 */
	public static function trunc($floatnumber) {
		if ($floatnumber >= 1) {
			$truncatednumber = floor($floatnumber);
		} elseif ($floatnumber <= -1) {
			$truncatednumber = ceil($floatnumber);
		} else {
			$truncatednumber = 0;
		}
		if (self::intValueSupported($truncatednumber)) {
			$truncatednumber = (int) $truncatednumber;
		}
		return $truncatednumber;
	}

	/**
	 * @param int|null $variable
	 * @param int      $increment
	 *
	 * @return bool
	 */
	public static function safe_inc(&$variable, $increment=1) {
		if (isset($variable)) {
			$variable += $increment;
		} else {
			$variable = $increment;
		}
		return true;
	}

	/**
	 * @param int|float $floatnum
	 *
	 * @return int|float
	 */
	public static function CastAsInt($floatnum) {
		// convert to float if not already
		$floatnum = (float) $floatnum;

		// convert a float to type int, only if possible
		if (self::trunc($floatnum) == $floatnum) {
			// it's not floating point
			if (self::intValueSupported($floatnum)) {
				// it's within int range
				$floatnum = (int) $floatnum;
			}
		}
		return $floatnum;
	}

	/**
	 * @param int $num
	 *
	 * @return bool
	 */
	public static function intValueSupported($num) {
		// check if integers are 64-bit
		static $hasINT64 = null;
		if ($hasINT64 === null) { // 10x faster than is_null()
			$hasINT64 = is_int(pow(2, 31)); // 32-bit int are limited to (2^31)-1
			if (!$hasINT64 && !defined('PHP_INT_MIN')) {
				define('PHP_INT_MIN', ~PHP_INT_MAX);
			}
		}
		// if integers are 64-bit - no other check required
		if ($hasINT64 || (($num <= PHP_INT_MAX) && ($num >= PHP_INT_MIN))) { // phpcs:ignore PHPCompatibility.Constants.NewConstants.php_int_minFound
			return true;
		}
		return false;
	}

	/**
	 * @param string $fraction
	 *
	 * @return float
	 */
	public static function DecimalizeFraction($fraction) {
		list($numerator, $denominator) = explode('/', $fraction);
		return $numerator / ($denominator ? $denominator : 1);
	}

	/**
	 * @param string $binarynumerator
	 *
	 * @return float
	 */
	public static function DecimalBinary2Float($binarynumerator) {
		$numerator   = self::Bin2Dec($binarynumerator);
		$denominator = self::Bin2Dec('1'.str_repeat('0', strlen($binarynumerator)));
		return ($numerator / $denominator);
	}

	/**
	 * @link http://www.scri.fsu.edu/~jac/MAD3401/Backgrnd/binary.html
	 *
	 * @param string $binarypointnumber
	 * @param int    $maxbits
	 *
	 * @return array
	 */
	public static function NormalizeBinaryPoint($binarypointnumber, $maxbits=52) {
		if (strpos($binarypointnumber, '.') === false) {
			$binarypointnumber = '0.'.$binarypointnumber;
		} elseif ($binarypointnumber[0] == '.') {
			$binarypointnumber = '0'.$binarypointnumber;
		}
		$exponent = 0;
		while (($binarypointnumber[0] != '1') || (substr($binarypointnumber, 1, 1) != '.')) {
			if (substr($binarypointnumber, 1, 1) == '.') {
				$exponent--;
				$binarypointnumber = substr($binarypointnumber, 2, 1).'.'.substr($binarypointnumber, 3);
			} else {
				$pointpos = strpos($binarypointnumber, '.');
				$exponent += ($pointpos - 1);
				$binarypointnumber = str_replace('.', '', $binarypointnumber);
				$binarypointnumber = $binarypointnumber[0].'.'.substr($binarypointnumber, 1);
			}
		}
		$binarypointnumber = str_pad(substr($binarypointnumber, 0, $maxbits + 2), $maxbits + 2, '0', STR_PAD_RIGHT);
		return array('normalized'=>$binarypointnumber, 'exponent'=>(int) $exponent);
	}

	/**
	 * @link http://www.scri.fsu.edu/~jac/MAD3401/Backgrnd/binary.html
	 *
	 * @param float $floatvalue
	 *
	 * @return string
	 */
	public static function Float2BinaryDecimal($floatvalue) {
		$maxbits = 128; // to how many bits of precision should the calculations be taken?
		$intpart   = self::trunc($floatvalue);
		$floatpart = abs($floatvalue - $intpart);
		$pointbitstring = '';
		while (($floatpart != 0) && (strlen($pointbitstring) < $maxbits)) {
			$floatpart *= 2;
			$pointbitstring .= (string) self::trunc($floatpart);
			$floatpart -= self::trunc($floatpart);
		}
		$binarypointnumber = decbin($intpart).'.'.$pointbitstring;
		return $binarypointnumber;
	}

	/**
	 * @link http://www.scri.fsu.edu/~jac/MAD3401/Backgrnd/ieee-expl.html
	 *
	 * @param float $floatvalue
	 * @param int $bits
	 *
	 * @return string|false
	 */
	public static function Float2String($floatvalue, $bits) {
		$exponentbits = 0;
		$fractionbits = 0;
		switch ($bits) {
			case 32:
				$exponentbits = 8;
				$fractionbits = 23;
				break;

			case 64:
				$exponentbits = 11;
				$fractionbits = 52;
				break;

			default:
				return false;
		}
		if ($floatvalue >= 0) {
			$signbit = '0';
		} else {
			$signbit = '1';
		}
		$normalizedbinary  = self::NormalizeBinaryPoint(self::Float2BinaryDecimal($floatvalue), $fractionbits);
		$biasedexponent    = pow(2, $exponentbits - 1) - 1 + $normalizedbinary['exponent']; // (127 or 1023) +/- exponent
		$exponentbitstring = str_pad(decbin($biasedexponent), $exponentbits, '0', STR_PAD_LEFT);
		$fractionbitstring = str_pad(substr($normalizedbinary['normalized'], 2), $fractionbits, '0', STR_PAD_RIGHT);

		return self::BigEndian2String(self::Bin2Dec($signbit.$exponentbitstring.$fractionbitstring), $bits % 8, false);
	}

	/**
	 * @param string $byteword
	 *
	 * @return float|false
	 */
	public static function LittleEndian2Float($byteword) {
		return self::BigEndian2Float(strrev($byteword));
	}

	/**
	 * ANSI/IEEE Standard 754-1985, Standard for Binary Floating Point Arithmetic
	 *
	 * @link http://www.psc.edu/general/software/packages/ieee/ieee.html
	 * @link http://www.scri.fsu.edu/~jac/MAD3401/Backgrnd/ieee.html
	 *
	 * @param string $byteword
	 *
	 * @return float|false
	 */
	public static function BigEndian2Float($byteword) {
		$bitword = self::BigEndian2Bin($byteword);
		if (!$bitword) {
			return 0;
		}
		$signbit = $bitword[0];
		$floatvalue = 0;
		$exponentbits = 0;
		$fractionbits = 0;

		switch (strlen($byteword) * 8) {
			case 32:
				$exponentbits = 8;
				$fractionbits = 23;
				break;

			case 64:
				$exponentbits = 11;
				$fractionbits = 52;
				break;

			case 80:
				// 80-bit Apple SANE format
				// http://www.mactech.com/articles/mactech/Vol.06/06.01/SANENormalized/
				$exponentstring = substr($bitword, 1, 15);
				$isnormalized = intval($bitword[16]);
				$fractionstring = substr($bitword, 17, 63);
				$exponent = pow(2, self::Bin2Dec($exponentstring) - 16383);
				$fraction = $isnormalized + self::DecimalBinary2Float($fractionstring);
				$floatvalue = $exponent * $fraction;
				if ($signbit == '1') {
					$floatvalue *= -1;
				}
				return $floatvalue;

			default:
				return false;
		}
		$exponentstring = substr($bitword, 1, $exponentbits);
		$fractionstring = substr($bitword, $exponentbits + 1, $fractionbits);
		$exponent = self::Bin2Dec($exponentstring);
		$fraction = self::Bin2Dec($fractionstring);

		if (($exponent == (pow(2, $exponentbits) - 1)) && ($fraction != 0)) {
			// Not a Number
			$floatvalue = false;
		} elseif (($exponent == (pow(2, $exponentbits) - 1)) && ($fraction == 0)) {
			if ($signbit == '1') {
				$floatvalue = '-infinity';
			} else {
				$floatvalue = '+infinity';
			}
		} elseif (($exponent == 0) && ($fraction == 0)) {
			if ($signbit == '1') {
				$floatvalue = -0;
			} else {
				$floatvalue = 0;
			}
			$floatvalue = ($signbit ? 0 : -0);
		} elseif (($exponent == 0) && ($fraction != 0)) {
			// These are 'unnormalized' values
			$floatvalue = pow(2, (-1 * (pow(2, $exponentbits - 1) - 2))) * self::DecimalBinary2Float($fractionstring);
			if ($signbit == '1') {
				$floatvalue *= -1;
			}
		} elseif ($exponent != 0) {
			$floatvalue = pow(2, ($exponent - (pow(2, $exponentbits - 1) - 1))) * (1 + self::DecimalBinary2Float($fractionstring));
			if ($signbit == '1') {
				$floatvalue *= -1;
			}
		}
		return (float) $floatvalue;
	}

	/**
	 * @param string $byteword
	 * @param bool   $synchsafe
	 * @param bool   $signed
	 *
	 * @return int|float|false
	 * @throws Exception
	 */
	public static function BigEndian2Int($byteword, $synchsafe=false, $signed=false) {
		$intvalue = 0;
		$bytewordlen = strlen($byteword);
		if ($bytewordlen == 0) {
			return false;
		}
		for ($i = 0; $i < $bytewordlen; $i++) {
			if ($synchsafe) { // disregard MSB, effectively 7-bit bytes
				//$intvalue = $intvalue | (ord($byteword{$i}) & 0x7F) << (($bytewordlen - 1 - $i) * 7); // faster, but runs into problems past 2^31 on 32-bit systems
				$intvalue += (ord($byteword[$i]) & 0x7F) * pow(2, ($bytewordlen - 1 - $i) * 7);
			} else {
				$intvalue += ord($byteword[$i]) * pow(256, ($bytewordlen - 1 - $i));
			}
		}
		if ($signed && !$synchsafe) {
			// synchsafe ints are not allowed to be signed
			if ($bytewordlen <= PHP_INT_SIZE) {
				$signMaskBit = 0x80 << (8 * ($bytewordlen - 1));
				if ($intvalue & $signMaskBit) {
					$intvalue = 0 - ($intvalue & ($signMaskBit - 1));
				}
			} else {
				throw new Exception('ERROR: Cannot have signed integers larger than '.(8 * PHP_INT_SIZE).'-bits ('.strlen($byteword).') in self::BigEndian2Int()');
			}
		}
		return self::CastAsInt($intvalue);
	}

	/**
	 * @param string $byteword
	 * @param bool   $signed
	 *
	 * @return int|float|false
	 */
	public static function LittleEndian2Int($byteword, $signed=false) {
		return self::BigEndian2Int(strrev($byteword), false, $signed);
	}

	/**
	 * @param string $byteword
	 *
	 * @return string
	 */
	public static function LittleEndian2Bin($byteword) {
		return self::BigEndian2Bin(strrev($byteword));
	}

	/**
	 * @param string $byteword
	 *
	 * @return string
	 */
	public static function BigEndian2Bin($byteword) {
		$binvalue = '';
		$bytewordlen = strlen($byteword);
		for ($i = 0; $i < $bytewordlen; $i++) {
			$binvalue .= str_pad(decbin(ord($byteword[$i])), 8, '0', STR_PAD_LEFT);
		}
		return $binvalue;
	}

	/**
	 * @param int  $number
	 * @param int  $minbytes
	 * @param bool $synchsafe
	 * @param bool $signed
	 *
	 * @return string
	 * @throws Exception
	 */
	public static function BigEndian2String($number, $minbytes=1, $synchsafe=false, $signed=false) {
		if ($number < 0) {
			throw new Exception('ERROR: self::BigEndian2String() does not support negative numbers');
		}
		$maskbyte = (($synchsafe || $signed) ? 0x7F : 0xFF);
		$intstring = '';
		if ($signed) {
			if ($minbytes > PHP_INT_SIZE) {
				throw new Exception('ERROR: Cannot have signed integers larger than '.(8 * PHP_INT_SIZE).'-bits in self::BigEndian2String()');
			}
			$number = $number & (0x80 << (8 * ($minbytes - 1)));
		}
		while ($number != 0) {
			$quotient = ($number / ($maskbyte + 1));
			$intstring = chr(ceil(($quotient - floor($quotient)) * $maskbyte)).$intstring;
			$number = floor($quotient);
		}
		return str_pad($intstring, $minbytes, "\x00", STR_PAD_LEFT);
	}

	/**
	 * @param int $number
	 *
	 * @return string
	 */
	public static function Dec2Bin($number) {
		while ($number >= 256) {
			$bytes[] = (($number / 256) - (floor($number / 256))) * 256;
			$number = floor($number / 256);
		}
		$bytes[] = $number;
		$binstring = '';
		for ($i = 0; $i < count($bytes); $i++) {
			$binstring = (($i == count($bytes) - 1) ? decbin($bytes[$i]) : str_pad(decbin($bytes[$i]), 8, '0', STR_PAD_LEFT)).$binstring;
		}
		return $binstring;
	}

	/**
	 * @param string $binstring
	 * @param bool   $signed
	 *
	 * @return int|float
	 */
	public static function Bin2Dec($binstring, $signed=false) {
		$signmult = 1;
		if ($signed) {
			if ($binstring[0] == '1') {
				$signmult = -1;
			}
			$binstring = substr($binstring, 1);
		}
		$decvalue = 0;
		for ($i = 0; $i < strlen($binstring); $i++) {
			$decvalue += ((int) substr($binstring, strlen($binstring) - $i - 1, 1)) * pow(2, $i);
		}
		return self::CastAsInt($decvalue * $signmult);
	}

	/**
	 * @param string $binstring
	 *
	 * @return string
	 */
	public static function Bin2String($binstring) {
		// return 'hi' for input of '0110100001101001'
		$string = '';
		$binstringreversed = strrev($binstring);
		for ($i = 0; $i < strlen($binstringreversed); $i += 8) {
			$string = chr(self::Bin2Dec(strrev(substr($binstringreversed, $i, 8)))).$string;
		}
		return $string;
	}

	/**
	 * @param int  $number
	 * @param int  $minbytes
	 * @param bool $synchsafe
	 *
	 * @return string
	 */
	public static function LittleEndian2String($number, $minbytes=1, $synchsafe=false) {
		$intstring = '';
		while ($number > 0) {
			if ($synchsafe) {
				$intstring = $intstring.chr($number & 127);
				$number >>= 7;
			} else {
				$intstring = $intstring.chr($number & 255);
				$number >>= 8;
			}
		}
		return str_pad($intstring, $minbytes, "\x00", STR_PAD_RIGHT);
	}

	/**
	 * @param mixed $array1
	 * @param mixed $array2
	 *
	 * @return array|false
	 */
	public static function array_merge_clobber($array1, $array2) {
		// written by kcØhireability*com
		// taken from http://www.php.net/manual/en/function.array-merge-recursive.php
		if (!is_array($array1) || !is_array($array2)) {
			return false;
		}
		$newarray = $array1;
		foreach ($array2 as $key => $val) {
			if (is_array($val) && isset($newarray[$key]) && is_array($newarray[$key])) {
				$newarray[$key] = self::array_merge_clobber($newarray[$key], $val);
			} else {
				$newarray[$key] = $val;
			}
		}
		return $newarray;
	}

	/**
	 * @param mixed $array1
	 * @param mixed $array2
	 *
	 * @return array|false
	 */
	public static function array_merge_noclobber($array1, $array2) {
		if (!is_array($array1) || !is_array($array2)) {
			return false;
		}
		$newarray = $array1;
		foreach ($array2 as $key => $val) {
			if (is_array($val) && isset($newarray[$key]) && is_array($newarray[$key])) {
				$newarray[$key] = self::array_merge_noclobber($newarray[$key], $val);
			} elseif (!isset($newarray[$key])) {
				$newarray[$key] = $val;
			}
		}
		return $newarray;
	}

	/**
	 * @param mixed $array1
	 * @param mixed $array2
	 *
	 * @return array|false|null
	 */
	public static function flipped_array_merge_noclobber($array1, $array2) {
		if (!is_array($array1) || !is_array($array2)) {
			return false;
		}
		# naturally, this only works non-recursively
		$newarray = array_flip($array1);
		foreach (array_flip($array2) as $key => $val) {
			if (!isset($newarray[$key])) {
				$newarray[$key] = count($newarray);
			}
		}
		return array_flip($newarray);
	}

	/**
	 * @param array $theArray
	 *
	 * @return bool
	 */
	public static function ksort_recursive(&$theArray) {
		ksort($theArray);
		foreach ($theArray as $key => $value) {
			if (is_array($value)) {
				self::ksort_recursive($theArray[$key]);
			}
		}
		return true;
	}

	/**
	 * @param string $filename
	 * @param int    $numextensions
	 *
	 * @return string
	 */
	public static function fileextension($filename, $numextensions=1) {
		if (strstr($filename, '.')) {
			$reversedfilename = strrev($filename);
			$offset = 0;
			for ($i = 0; $i < $numextensions; $i++) {
				$offset = strpos($reversedfilename, '.', $offset + 1);
				if ($offset === false) {
					return '';
				}
			}
			return strrev(substr($reversedfilename, 0, $offset));
		}
		return '';
	}

	/**
	 * @param int $seconds
	 *
	 * @return string
	 */
	public static function PlaytimeString($seconds) {
		$sign = (($seconds < 0) ? '-' : '');
		$seconds = round(abs($seconds));
		$H = (int) floor( $seconds                            / 3600);
		$M = (int) floor(($seconds - (3600 * $H)            ) /   60);
		$S = (int) round( $seconds - (3600 * $H) - (60 * $M)        );
		return $sign.($H ? $H.':' : '').($H ? str_pad($M, 2, '0', STR_PAD_LEFT) : intval($M)).':'.str_pad($S, 2, 0, STR_PAD_LEFT);
	}

	/**
	 * @param int $macdate
	 *
	 * @return int|float
	 */
	public static function DateMac2Unix($macdate) {
		// Macintosh timestamp: seconds since 00:00h January 1, 1904
		// UNIX timestamp:      seconds since 00:00h January 1, 1970
		return self::CastAsInt($macdate - 2082844800);
	}

	/**
	 * @param string $rawdata
	 *
	 * @return float
	 */
	public static function FixedPoint8_8($rawdata) {
		return self::BigEndian2Int(substr($rawdata, 0, 1)) + (float) (self::BigEndian2Int(substr($rawdata, 1, 1)) / pow(2, 8));
	}

	/**
	 * @param string $rawdata
	 *
	 * @return float
	 */
	public static function FixedPoint16_16($rawdata) {
		return self::BigEndian2Int(substr($rawdata, 0, 2)) + (float) (self::BigEndian2Int(substr($rawdata, 2, 2)) / pow(2, 16));
	}

	/**
	 * @param string $rawdata
	 *
	 * @return float
	 */
	public static function FixedPoint2_30($rawdata) {
		$binarystring = self::BigEndian2Bin($rawdata);
		return self::Bin2Dec(substr($binarystring, 0, 2)) + (float) (self::Bin2Dec(substr($binarystring, 2, 30)) / pow(2, 30));
	}


	/**
	 * @param string $ArrayPath
	 * @param string $Separator
	 * @param mixed $Value
	 *
	 * @return array
	 */
	public static function CreateDeepArray($ArrayPath, $Separator, $Value) {
		// assigns $Value to a nested array path:
		//   $foo = self::CreateDeepArray('/path/to/my', '/', 'file.txt')
		// is the same as:
		//   $foo = array('path'=>array('to'=>'array('my'=>array('file.txt'))));
		// or
		//   $foo['path']['to']['my'] = 'file.txt';
		$ArrayPath = ltrim($ArrayPath, $Separator);
		if (($pos = strpos($ArrayPath, $Separator)) !== false) {
			$ReturnedArray[substr($ArrayPath, 0, $pos)] = self::CreateDeepArray(substr($ArrayPath, $pos + 1), $Separator, $Value);
		} else {
			$ReturnedArray[$ArrayPath] = $Value;
		}
		return $ReturnedArray;
	}

	/**
	 * @param array $arraydata
	 * @param bool  $returnkey
	 *
	 * @return int|false
	 */
	public static function array_max($arraydata, $returnkey=false) {
		$maxvalue = false;
		$maxkey   = false;
		foreach ($arraydata as $key => $value) {
			if (!is_array($value)) {
				if (($maxvalue === false) || ($value > $maxvalue)) {
					$maxvalue = $value;
					$maxkey = $key;
				}
			}
		}
		return ($returnkey ? $maxkey : $maxvalue);
	}

	/**
	 * @param array $arraydata
	 * @param bool  $returnkey
	 *
	 * @return int|false
	 */
	public static function array_min($arraydata, $returnkey=false) {
		$minvalue = false;
		$minkey   = false;
		foreach ($arraydata as $key => $value) {
			if (!is_array($value)) {
				if (($minvalue === false) || ($value < $minvalue)) {
					$minvalue = $value;
					$minkey = $key;
				}
			}
		}
		return ($returnkey ? $minkey : $minvalue);
	}

	/**
	 * @param string $XMLstring
	 *
	 * @return array|false
	 */
	public static function XML2array($XMLstring) {
		if (function_exists('simplexml_load_string') && function_exists('libxml_disable_entity_loader')) {
			if (PHP_VERSION_ID < 80000) {
				// http://websec.io/2012/08/27/Preventing-XEE-in-PHP.html
				// https://core.trac.wordpress.org/changeset/29378
				// This function has been deprecated in PHP 8.0 because in libxml 2.9.0, external entity loading is
				// disabled by default, so this function is no longer needed to protect against XXE attacks.
				$loader = libxml_disable_entity_loader(true);
			}
			$XMLobject = simplexml_load_string($XMLstring, 'SimpleXMLElement', LIBXML_NOENT);
			$return = self::SimpleXMLelement2array($XMLobject);
			if (PHP_VERSION_ID < 80000 && isset($loader)) {
				libxml_disable_entity_loader($loader);
			}
			return $return;
		}
		return false;
	}

	/**
	* @param SimpleXMLElement|array|mixed $XMLobject
	*
	* @return mixed
	*/
	public static function SimpleXMLelement2array($XMLobject) {
		if (!is_object($XMLobject) && !is_array($XMLobject)) {
			return $XMLobject;
		}
		$XMLarray = $XMLobject instanceof SimpleXMLElement ? get_object_vars($XMLobject) : $XMLobject;
		foreach ($XMLarray as $key => $value) {
			$XMLarray[$key] = self::SimpleXMLelement2array($value);
		}
		return $XMLarray;
	}

	/**
	 * Returns checksum for a file from starting position to absolute end position.
	 *
	 * @param string $file
	 * @param int    $offset
	 * @param int    $end
	 * @param string $algorithm
	 *
	 * @return string|false
	 * @throws getid3_exception
	 */
	public static function hash_data($file, $offset, $end, $algorithm) {
		if (!self::intValueSupported($end)) {
			return false;
		}
		if (!in_array($algorithm, array('md5', 'sha1'))) {
			throw new getid3_exception('Invalid algorithm ('.$algorithm.') in self::hash_data()');
		}

		$size = $end - $offset;

		$fp = fopen($file, 'rb');
		fseek($fp, $offset);
		$ctx = hash_init($algorithm);
		while ($size > 0) {
			$buffer = fread($fp, min($size, getID3::FREAD_BUFFER_SIZE));
			hash_update($ctx, $buffer);
			$size -= getID3::FREAD_BUFFER_SIZE;
		}
		$hash = hash_final($ctx);
		fclose($fp);

		return $hash;
	}

	/**
	 * @param string $filename_source
	 * @param string $filename_dest
	 * @param int    $offset
	 * @param int    $length
	 *
	 * @return bool
	 * @throws Exception
	 *
	 * @deprecated Unused, may be removed in future versions of getID3
	 */
	public static function CopyFileParts($filename_source, $filename_dest, $offset, $length) {
		if (!self::intValueSupported($offset + $length)) {
			throw new Exception('cannot copy file portion, it extends beyond the '.round(PHP_INT_MAX / 1073741824).'GB limit');
		}
		if (is_readable($filename_source) && is_file($filename_source) && ($fp_src = fopen($filename_source, 'rb'))) {
			if (($fp_dest = fopen($filename_dest, 'wb'))) {
				if (fseek($fp_src, $offset) == 0) {
					$byteslefttowrite = $length;
					while (($byteslefttowrite > 0) && ($buffer = fread($fp_src, min($byteslefttowrite, getID3::FREAD_BUFFER_SIZE)))) {
						$byteswritten = fwrite($fp_dest, $buffer, $byteslefttowrite);
						$byteslefttowrite -= $byteswritten;
					}
					fclose($fp_dest);
					return true;
				} else {
					fclose($fp_src);
					throw new Exception('failed to seek to offset '.$offset.' in '.$filename_source);
				}
			} else {
				throw new Exception('failed to create file for writing '.$filename_dest);
			}
		} else {
			throw new Exception('failed to open file for reading '.$filename_source);
		}
	}

	/**
	 * @param int $charval
	 *
	 * @return string
	 */
	public static function iconv_fallback_int_utf8($charval) {
		if ($charval < 128) {
			// 0bbbbbbb
			$newcharstring = chr($charval);
		} elseif ($charval < 2048) {
			// 110bbbbb 10bbbbbb
			$newcharstring  = chr(($charval >>   6) | 0xC0);
			$newcharstring .= chr(($charval & 0x3F) | 0x80);
		} elseif ($charval < 65536) {
			// 1110bbbb 10bbbbbb 10bbbbbb
			$newcharstring  = chr(($charval >>  12) | 0xE0);
			$newcharstring .= chr(($charval >>   6) | 0xC0);
			$newcharstring .= chr(($charval & 0x3F) | 0x80);
		} else {
			// 11110bbb 10bbbbbb 10bbbbbb 10bbbbbb
			$newcharstring  = chr(($charval >>  18) | 0xF0);
			$newcharstring .= chr(($charval >>  12) | 0xC0);
			$newcharstring .= chr(($charval >>   6) | 0xC0);
			$newcharstring .= chr(($charval & 0x3F) | 0x80);
		}
		return $newcharstring;
	}

	/**
	 * ISO-8859-1 => UTF-8
	 *
	 * @param string $string
	 * @param bool   $bom
	 *
	 * @return string
	 */
	public static function iconv_fallback_iso88591_utf8($string, $bom=false) {
		if (function_exists('utf8_encode')) {
			return utf8_encode($string);
		}
		// utf8_encode() unavailable, use getID3()'s iconv_fallback() conversions (possibly PHP is compiled without XML support)
		$newcharstring = '';
		if ($bom) {
			$newcharstring .= "\xEF\xBB\xBF";
		}
		for ($i = 0; $i < strlen($string); $i++) {
			$charval = ord($string[$i]);
			$newcharstring .= self::iconv_fallback_int_utf8($charval);
		}
		return $newcharstring;
	}

	/**
	 * ISO-8859-1 => UTF-16BE
	 *
	 * @param string $string
	 * @param bool   $bom
	 *
	 * @return string
	 */
	public static function iconv_fallback_iso88591_utf16be($string, $bom=false) {
		$newcharstring = '';
		if ($bom) {
			$newcharstring .= "\xFE\xFF";
		}
		for ($i = 0; $i < strlen($string); $i++) {
			$newcharstring .= "\x00".$string[$i];
		}
		return $newcharstring;
	}

	/**
	 * ISO-8859-1 => UTF-16LE
	 *
	 * @param string $string
	 * @param bool   $bom
	 *
	 * @return string
	 */
	public static function iconv_fallback_iso88591_utf16le($string, $bom=false) {
		$newcharstring = '';
		if ($bom) {
			$newcharstring .= "\xFF\xFE";
		}
		for ($i = 0; $i < strlen($string); $i++) {
			$newcharstring .= $string[$i]."\x00";
		}
		return $newcharstring;
	}

	/**
	 * ISO-8859-1 => UTF-16LE (BOM)
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function iconv_fallback_iso88591_utf16($string) {
		return self::iconv_fallback_iso88591_utf16le($string, true);
	}

	/**
	 * UTF-8 => ISO-8859-1
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function iconv_fallback_utf8_iso88591($string) {
		if (function_exists('utf8_decode')) {
			return utf8_decode($string);
		}
		// utf8_decode() unavailable, use getID3()'s iconv_fallback() conversions (possibly PHP is compiled without XML support)
		$newcharstring = '';
		$offset = 0;
		$stringlength = strlen($string);
		while ($offset < $stringlength) {
			if ((ord($string[$offset]) | 0x07) == 0xF7) {
				// 11110bbb 10bbbbbb 10bbbbbb 10bbbbbb
				$charval = ((ord($string[($offset + 0)]) & 0x07) << 18) &
						   ((ord($string[($offset + 1)]) & 0x3F) << 12) &
						   ((ord($string[($offset + 2)]) & 0x3F) <<  6) &
							(ord($string[($offset + 3)]) & 0x3F);
				$offset += 4;
			} elseif ((ord($string[$offset]) | 0x0F) == 0xEF) {
				// 1110bbbb 10bbbbbb 10bbbbbb
				$charval = ((ord($string[($offset + 0)]) & 0x0F) << 12) &
						   ((ord($string[($offset + 1)]) & 0x3F) <<  6) &
							(ord($string[($offset + 2)]) & 0x3F);
				$offset += 3;
			} elseif ((ord($string[$offset]) | 0x1F) == 0xDF) {
				// 110bbbbb 10bbbbbb
				$charval = ((ord($string[($offset + 0)]) & 0x1F) <<  6) &
							(ord($string[($offset + 1)]) & 0x3F);
				$offset += 2;
			} elseif ((ord($string[$offset]) | 0x7F) == 0x7F) {
				// 0bbbbbbb
				$charval = ord($string[$offset]);
				$offset += 1;
			} else {
				// error? throw some kind of warning here?
				$charval = false;
				$offset += 1;
			}
			if ($charval !== false) {
				$newcharstring .= (($charval < 256) ? chr($charval) : '?');
			}
		}
		return $newcharstring;
	}

	/**
	 * UTF-8 => UTF-16BE
	 *
	 * @param string $string
	 * @param bool   $bom
	 *
	 * @return string
	 */
	public static function iconv_fallback_utf8_utf16be($string, $bom=false) {
		$newcharstring = '';
		if ($bom) {
			$newcharstring .= "\xFE\xFF";
		}
		$offset = 0;
		$stringlength = strlen($string);
		while ($offset < $stringlength) {
			if ((ord($string[$offset]) | 0x07) == 0xF7) {
				// 11110bbb 10bbbbbb 10bbbbbb 10bbbbbb
				$charval = ((ord($string[($offset + 0)]) & 0x07) << 18) &
						   ((ord($string[($offset + 1)]) & 0x3F) << 12) &
						   ((ord($string[($offset + 2)]) & 0x3F) <<  6) &
							(ord($string[($offset + 3)]) & 0x3F);
				$offset += 4;
			} elseif ((ord($string[$offset]) | 0x0F) == 0xEF) {
				// 1110bbbb 10bbbbbb 10bbbbbb
				$charval = ((ord($string[($offset + 0)]) & 0x0F) << 12) &
						   ((ord($string[($offset + 1)]) & 0x3F) <<  6) &
							(ord($string[($offset + 2)]) & 0x3F);
				$offset += 3;
			} elseif ((ord($string[$offset]) | 0x1F) == 0xDF) {
				// 110bbbbb 10bbbbbb
				$charval = ((ord($string[($offset + 0)]) & 0x1F) <<  6) &
							(ord($string[($offset + 1)]) & 0x3F);
				$offset += 2;
			} elseif ((ord($string[$offset]) | 0x7F) == 0x7F) {
				// 0bbbbbbb
				$charval = ord($string[$offset]);
				$offset += 1;
			} else {
				// error? throw some kind of warning here?
				$charval = false;
				$offset += 1;
			}
			if ($charval !== false) {
				$newcharstring .= (($charval < 65536) ? self::BigEndian2String($charval, 2) : "\x00".'?');
			}
		}
		return $newcharstring;
	}

	/**
	 * UTF-8 => UTF-16LE
	 *
	 * @param string $string
	 * @param bool   $bom
	 *
	 * @return string
	 */
	public static function iconv_fallback_utf8_utf16le($string, $bom=false) {
		$newcharstring = '';
		if ($bom) {
			$newcharstring .= "\xFF\xFE";
		}
		$offset = 0;
		$stringlength = strlen($string);
		while ($offset < $stringlength) {
			if ((ord($string[$offset]) | 0x07) == 0xF7) {
				// 11110bbb 10bbbbbb 10bbbbbb 10bbbbbb
				$charval = ((ord($string[($offset + 0)]) & 0x07) << 18) &
						   ((ord($string[($offset + 1)]) & 0x3F) << 12) &
						   ((ord($string[($offset + 2)]) & 0x3F) <<  6) &
							(ord($string[($offset + 3)]) & 0x3F);
				$offset += 4;
			} elseif ((ord($string[$offset]) | 0x0F) == 0xEF) {
				// 1110bbbb 10bbbbbb 10bbbbbb
				$charval = ((ord($string[($offset + 0)]) & 0x0F) << 12) &
						   ((ord($string[($offset + 1)]) & 0x3F) <<  6) &
							(ord($string[($offset + 2)]) & 0x3F);
				$offset += 3;
			} elseif ((ord($string[$offset]) | 0x1F) == 0xDF) {
				// 110bbbbb 10bbbbbb
				$charval = ((ord($string[($offset + 0)]) & 0x1F) <<  6) &
							(ord($string[($offset + 1)]) & 0x3F);
				$offset += 2;
			} elseif ((ord($string[$offset]) | 0x7F) == 0x7F) {
				// 0bbbbbbb
				$charval = ord($string[$offset]);
				$offset += 1;
			} else {
				// error? maybe throw some warning here?
				$charval = false;
				$offset += 1;
			}
			if ($charval !== false) {
				$newcharstring .= (($charval < 65536) ? self::LittleEndian2String($charval, 2) : '?'."\x00");
			}
		}
		return $newcharstring;
	}

	/**
	 * UTF-8 => UTF-16LE (BOM)
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function iconv_fallback_utf8_utf16($string) {
		return self::iconv_fallback_utf8_utf16le($string, true);
	}

	/**
	 * UTF-16BE => UTF-8
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function iconv_fallback_utf16be_utf8($string) {
		if (substr($string, 0, 2) == "\xFE\xFF") {
			// strip BOM
			$string = substr($string, 2);
		}
		$newcharstring = '';
		for ($i = 0; $i < strlen($string); $i += 2) {
			$charval = self::BigEndian2Int(substr($string, $i, 2));
			$newcharstring .= self::iconv_fallback_int_utf8($charval);
		}
		return $newcharstring;
	}

	/**
	 * UTF-16LE => UTF-8
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function iconv_fallback_utf16le_utf8($string) {
		if (substr($string, 0, 2) == "\xFF\xFE") {
			// strip BOM
			$string = substr($string, 2);
		}
		$newcharstring = '';
		for ($i = 0; $i < strlen($string); $i += 2) {
			$charval = self::LittleEndian2Int(substr($string, $i, 2));
			$newcharstring .= self::iconv_fallback_int_utf8($charval);
		}
		return $newcharstring;
	}

	/**
	 * UTF-16BE => ISO-8859-1
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function iconv_fallback_utf16be_iso88591($string) {
		if (substr($string, 0, 2) == "\xFE\xFF") {
			// strip BOM
			$string = substr($string, 2);
		}
		$newcharstring = '';
		for ($i = 0; $i < strlen($string); $i += 2) {
			$charval = self::BigEndian2Int(substr($string, $i, 2));
			$newcharstring .= (($charval < 256) ? chr($charval) : '?');
		}
		return $newcharstring;
	}

	/**
	 * UTF-16LE => ISO-8859-1
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function iconv_fallback_utf16le_iso88591($string) {
		if (substr($string, 0, 2) == "\xFF\xFE") {
			// strip BOM
			$string = substr($string, 2);
		}
		$newcharstring = '';
		for ($i = 0; $i < strlen($string); $i += 2) {
			$charval = self::LittleEndian2Int(substr($string, $i, 2));
			$newcharstring .= (($charval < 256) ? chr($charval) : '?');
		}
		return $newcharstring;
	}

	/**
	 * UTF-16 (BOM) => ISO-8859-1
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function iconv_fallback_utf16_iso88591($string) {
		$bom = substr($string, 0, 2);
		if ($bom == "\xFE\xFF") {
			return self::iconv_fallback_utf16be_iso88591(substr($string, 2));
		} elseif ($bom == "\xFF\xFE") {
			return self::iconv_fallback_utf16le_iso88591(substr($string, 2));
		}
		return $string;
	}

	/**
	 * UTF-16 (BOM) => UTF-8
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function iconv_fallback_utf16_utf8($string) {
		$bom = substr($string, 0, 2);
		if ($bom == "\xFE\xFF") {
			return self::iconv_fallback_utf16be_utf8(substr($string, 2));
		} elseif ($bom == "\xFF\xFE") {
			return self::iconv_fallback_utf16le_utf8(substr($string, 2));
		}
		return $string;
	}

	/**
	 * @param string $in_charset
	 * @param string $out_charset
	 * @param string $string
	 *
	 * @return string
	 * @throws Exception
	 */
	public static function iconv_fallback($in_charset, $out_charset, $string) {

		if ($in_charset == $out_charset) {
			return $string;
		}

		// mb_convert_encoding() available
		if (function_exists('mb_convert_encoding')) {
			if ((strtoupper($in_charset) == 'UTF-16') && (substr($string, 0, 2) != "\xFE\xFF") && (substr($string, 0, 2) != "\xFF\xFE")) {
				// if BOM missing, mb_convert_encoding will mishandle the conversion, assume UTF-16BE and prepend appropriate BOM
				$string = "\xFF\xFE".$string;
			}
			if ((strtoupper($in_charset) == 'UTF-16') && (strtoupper($out_charset) == 'UTF-8')) {
				if (($string == "\xFF\xFE") || ($string == "\xFE\xFF")) {
					// if string consists of only BOM, mb_convert_encoding will return the BOM unmodified
					return '';
				}
			}
			if ($converted_string = @mb_convert_encoding($string, $out_charset, $in_charset)) {
				switch ($out_charset) {
					case 'ISO-8859-1':
						$converted_string = rtrim($converted_string, "\x00");
						break;
				}
				return $converted_string;
			}
			return $string;

		// iconv() available
		} elseif (function_exists('iconv')) {
			if ($converted_string = @iconv($in_charset, $out_charset.'//TRANSLIT', $string)) {
				switch ($out_charset) {
					case 'ISO-8859-1':
						$converted_string = rtrim($converted_string, "\x00");
						break;
				}
				return $converted_string;
			}

			// iconv() may sometimes fail with "illegal character in input string" error message
			// and return an empty string, but returning the unconverted string is more useful
			return $string;
		}


		// neither mb_convert_encoding or iconv() is available
		static $ConversionFunctionList = array();
		if (empty($ConversionFunctionList)) {
			$ConversionFunctionList['ISO-8859-1']['UTF-8']    = 'iconv_fallback_iso88591_utf8';
			$ConversionFunctionList['ISO-8859-1']['UTF-16']   = 'iconv_fallback_iso88591_utf16';
			$ConversionFunctionList['ISO-8859-1']['UTF-16BE'] = 'iconv_fallback_iso88591_utf16be';
			$ConversionFunctionList['ISO-8859-1']['UTF-16LE'] = 'iconv_fallback_iso88591_utf16le';
			$ConversionFunctionList['UTF-8']['ISO-8859-1']    = 'iconv_fallback_utf8_iso88591';
			$ConversionFunctionList['UTF-8']['UTF-16']        = 'iconv_fallback_utf8_utf16';
			$ConversionFunctionList['UTF-8']['UTF-16BE']      = 'iconv_fallback_utf8_utf16be';
			$ConversionFunctionList['UTF-8']['UTF-16LE']      = 'iconv_fallback_utf8_utf16le';
			$ConversionFunctionList['UTF-16']['ISO-8859-1']   = 'iconv_fallback_utf16_iso88591';
			$ConversionFunctionList['UTF-16']['UTF-8']        = 'iconv_fallback_utf16_utf8';
			$ConversionFunctionList['UTF-16LE']['ISO-8859-1'] = 'iconv_fallback_utf16le_iso88591';
			$ConversionFunctionList['UTF-16LE']['UTF-8']      = 'iconv_fallback_utf16le_utf8';
			$ConversionFunctionList['UTF-16BE']['ISO-8859-1'] = 'iconv_fallback_utf16be_iso88591';
			$ConversionFunctionList['UTF-16BE']['UTF-8']      = 'iconv_fallback_utf16be_utf8';
		}
		if (isset($ConversionFunctionList[strtoupper($in_charset)][strtoupper($out_charset)])) {
			$ConversionFunction = $ConversionFunctionList[strtoupper($in_charset)][strtoupper($out_charset)];
			return self::$ConversionFunction($string);
		}
		throw new Exception('PHP does not has mb_convert_encoding() or iconv() support - cannot convert from '.$in_charset.' to '.$out_charset);
	}

	/**
	 * @param mixed  $data
	 * @param string $charset
	 *
	 * @return mixed
	 */
	public static function recursiveMultiByteCharString2HTML($data, $charset='ISO-8859-1') {
		if (is_string($data)) {
			return self::MultiByteCharString2HTML($data, $charset);
		} elseif (is_array($data)) {
			$return_data = array();
			foreach ($data as $key => $value) {
				$return_data[$key] = self::recursiveMultiByteCharString2HTML($value, $charset);
			}
			return $return_data;
		}
		// integer, float, objects, resources, etc
		return $data;
	}

	/**
	 * @param string|int|float $string
	 * @param string           $charset
	 *
	 * @return string
	 */
	public static function MultiByteCharString2HTML($string, $charset='ISO-8859-1') {
		$string = (string) $string; // in case trying to pass a numeric (float, int) string, would otherwise return an empty string
		$HTMLstring = '';

		switch (strtolower($charset)) {
			case '1251':
			case '1252':
			case '866':
			case '932':
			case '936':
			case '950':
			case 'big5':
			case 'big5-hkscs':
			case 'cp1251':
			case 'cp1252':
			case 'cp866':
			case 'euc-jp':
			case 'eucjp':
			case 'gb2312':
			case 'ibm866':
			case 'iso-8859-1':
			case 'iso-8859-15':
			case 'iso8859-1':
			case 'iso8859-15':
			case 'koi8-r':
			case 'koi8-ru':
			case 'koi8r':
			case 'shift_jis':
			case 'sjis':
			case 'win-1251':
			case 'windows-1251':
			case 'windows-1252':
				$HTMLstring = htmlentities($string, ENT_COMPAT, $charset);
				break;

			case 'utf-8':
				$strlen = strlen($string);
				for ($i = 0; $i < $strlen; $i++) {
					$char_ord_val = ord($string[$i]);
					$charval = 0;
					if ($char_ord_val < 0x80) {
						$charval = $char_ord_val;
					} elseif ((($char_ord_val & 0xF0) >> 4) == 0x0F  &&  $i+3 < $strlen) {
						$charval  = (($char_ord_val & 0x07) << 18);
						$charval += ((ord($string[++$i]) & 0x3F) << 12);
						$charval += ((ord($string[++$i]) & 0x3F) << 6);
						$charval +=  (ord($string[++$i]) & 0x3F);
					} elseif ((($char_ord_val & 0xE0) >> 5) == 0x07  &&  $i+2 < $strlen) {
						$charval  = (($char_ord_val & 0x0F) << 12);
						$charval += ((ord($string[++$i]) & 0x3F) << 6);
						$charval +=  (ord($string[++$i]) & 0x3F);
					} elseif ((($char_ord_val & 0xC0) >> 6) == 0x03  &&  $i+1 < $strlen) {
						$charval  = (($char_ord_val & 0x1F) << 6);
						$charval += (ord($string[++$i]) & 0x3F);
					}
					if (($charval >= 32) && ($charval <= 127)) {
						$HTMLstring .= htmlentities(chr($charval));
					} else {
						$HTMLstring .= '&#'.$charval.';';
					}
				}
				break;

			case 'utf-16le':
				for ($i = 0; $i < strlen($string); $i += 2) {
					$charval = self::LittleEndian2Int(substr($string, $i, 2));
					if (($charval >= 32) && ($charval <= 127)) {
						$HTMLstring .= chr($charval);
					} else {
						$HTMLstring .= '&#'.$charval.';';
					}
				}
				break;

			case 'utf-16be':
				for ($i = 0; $i < strlen($string); $i += 2) {
					$charval = self::BigEndian2Int(substr($string, $i, 2));
					if (($charval >= 32) && ($charval <= 127)) {
						$HTMLstring .= chr($charval);
					} else {
						$HTMLstring .= '&#'.$charval.';';
					}
				}
				break;

			default:
				$HTMLstring = 'ERROR: Character set "'.$charset.'" not supported in MultiByteCharString2HTML()';
				break;
		}
		return $HTMLstring;
	}

	/**
	 * @param int $namecode
	 *
	 * @return string
	 */
	public static function RGADnameLookup($namecode) {
		static $RGADname = array();
		if (empty($RGADname)) {
			$RGADname[0] = 'not set';
			$RGADname[1] = 'Track Gain Adjustment';
			$RGADname[2] = 'Album Gain Adjustment';
		}

		return (isset($RGADname[$namecode]) ? $RGADname[$namecode] : '');
	}

	/**
	 * @param int $originatorcode
	 *
	 * @return string
	 */
	public static function RGADoriginatorLookup($originatorcode) {
		static $RGADoriginator = array();
		if (empty($RGADoriginator)) {
			$RGADoriginator[0] = 'unspecified';
			$RGADoriginator[1] = 'pre-set by artist/producer/mastering engineer';
			$RGADoriginator[2] = 'set by user';
			$RGADoriginator[3] = 'determined automatically';
		}

		return (isset($RGADoriginator[$originatorcode]) ? $RGADoriginator[$originatorcode] : '');
	}

	/**
	 * @param int $rawadjustment
	 * @param int $signbit
	 *
	 * @return float
	 */
	public static function RGADadjustmentLookup($rawadjustment, $signbit) {
		$adjustment = (float) $rawadjustment / 10;
		if ($signbit == 1) {
			$adjustment *= -1;
		}
		return $adjustment;
	}

	/**
	 * @param int $namecode
	 * @param int $originatorcode
	 * @param int $replaygain
	 *
	 * @return string
	 */
	public static function RGADgainString($namecode, $originatorcode, $replaygain) {
		if ($replaygain < 0) {
			$signbit = '1';
		} else {
			$signbit = '0';
		}
		$storedreplaygain = intval(round($replaygain * 10));
		$gainstring  = str_pad(decbin($namecode), 3, '0', STR_PAD_LEFT);
		$gainstring .= str_pad(decbin($originatorcode), 3, '0', STR_PAD_LEFT);
		$gainstring .= $signbit;
		$gainstring .= str_pad(decbin($storedreplaygain), 9, '0', STR_PAD_LEFT);

		return $gainstring;
	}

	/**
	 * @param float $amplitude
	 *
	 * @return float
	 */
	public static function RGADamplitude2dB($amplitude) {
		return 20 * log10($amplitude);
	}

	/**
	 * @param string $imgData
	 * @param array  $imageinfo
	 *
	 * @return array|false
	 */
	public static function GetDataImageSize($imgData, &$imageinfo=array()) {
		if (PHP_VERSION_ID >= 50400) {
			$GetDataImageSize = @getimagesizefromstring($imgData, $imageinfo);
			if ($GetDataImageSize === false || !isset($GetDataImageSize[0], $GetDataImageSize[1])) {
				return false;
			}
			$GetDataImageSize['height'] = $GetDataImageSize[0];
			$GetDataImageSize['width'] = $GetDataImageSize[1];
			return $GetDataImageSize;
		}
		static $tempdir = '';
		if (empty($tempdir)) {
			if (function_exists('sys_get_temp_dir')) {
				$tempdir = sys_get_temp_dir(); // https://github.com/JamesHeinrich/getID3/issues/52
			}

			// yes this is ugly, feel free to suggest a better way
			if (include_once(dirname(__FILE__).'/getid3.php')) {
				$getid3_temp = new getID3();
				if ($getid3_temp_tempdir = $getid3_temp->tempdir) {
					$tempdir = $getid3_temp_tempdir;
				}
				unset($getid3_temp, $getid3_temp_tempdir);
			}
		}
		$GetDataImageSize = false;
		if ($tempfilename = tempnam($tempdir, 'gI3')) {
			if (is_writable($tempfilename) && is_file($tempfilename) && ($tmp = fopen($tempfilename, 'wb'))) {
				fwrite($tmp, $imgData);
				fclose($tmp);
				$GetDataImageSize = @getimagesize($tempfilename, $imageinfo);
				if (($GetDataImageSize === false) || !isset($GetDataImageSize[0]) || !isset($GetDataImageSize[1])) {
					return false;
				}
				$GetDataImageSize['height'] = $GetDataImageSize[0];
				$GetDataImageSize['width']  = $GetDataImageSize[1];
			}
			unlink($tempfilename);
		}
		return $GetDataImageSize;
	}

	/**
	 * @param string $mime_type
	 *
	 * @return string
	 */
	public static function ImageExtFromMime($mime_type) {
		// temporary way, works OK for now, but should be reworked in the future
		return str_replace(array('image/', 'x-', 'jpeg'), array('', '', 'jpg'), $mime_type);
	}

	/**
	 * @param array $ThisFileInfo
	 * @param bool  $option_tags_html default true (just as in the main getID3 class)
	 *
	 * @return bool
	 */
	public static function CopyTagsToComments(&$ThisFileInfo, $option_tags_html=true) {
		// Copy all entries from ['tags'] into common ['comments']
		if (!empty($ThisFileInfo['tags'])) {
			if (isset($ThisFileInfo['tags']['id3v1'])) {
				// bubble ID3v1 to the end, if present to aid in detecting bad ID3v1 encodings
				$ID3v1 = $ThisFileInfo['tags']['id3v1'];
				unset($ThisFileInfo['tags']['id3v1']);
				$ThisFileInfo['tags']['id3v1'] = $ID3v1;
				unset($ID3v1);
			}
			foreach ($ThisFileInfo['tags'] as $tagtype => $tagarray) {
				foreach ($tagarray as $tagname => $tagdata) {
					foreach ($tagdata as $key => $value) {
						if (!empty($value)) {
							if (empty($ThisFileInfo['comments'][$tagname])) {

								// fall through and append value

							} elseif ($tagtype == 'id3v1') {

								$newvaluelength = strlen(trim($value));
								foreach ($ThisFileInfo['comments'][$tagname] as $existingkey => $existingvalue) {
									$oldvaluelength = strlen(trim($existingvalue));
									if (($newvaluelength <= $oldvaluelength) && (substr($existingvalue, 0, $newvaluelength) == trim($value))) {
										// new value is identical but shorter-than (or equal-length to) one already in comments - skip
										break 2;
									}
								}
								if (function_exists('mb_convert_encoding')) {
									if (trim($value) == trim(substr(mb_convert_encoding($existingvalue, $ThisFileInfo['id3v1']['encoding'], $ThisFileInfo['encoding']), 0, 30))) {
										// value stored in ID3v1 appears to be probably the multibyte value transliterated (badly) into ISO-8859-1 in ID3v1.
										// As an example, Foobar2000 will do this if you tag a file with Chinese or Arabic or Cyrillic or something that doesn't fit into ISO-8859-1 the ID3v1 will consist of mostly "?" characters, one per multibyte unrepresentable character
										break 2;
									}
								}

							} elseif (!is_array($value)) {

								$newvaluelength = strlen(trim($value));
								foreach ($ThisFileInfo['comments'][$tagname] as $existingkey => $existingvalue) {
									$oldvaluelength = strlen(trim($existingvalue));
									if ((strlen($existingvalue) > 10) && ($newvaluelength > $oldvaluelength) && (substr(trim($value), 0, strlen($existingvalue)) == $existingvalue)) {
										$ThisFileInfo['comments'][$tagname][$existingkey] = trim($value);
										break;
									}
								}

							}
							if (is_array($value) || empty($ThisFileInfo['comments'][$tagname]) || !in_array(trim($value), $ThisFileInfo['comments'][$tagname])) {
								$value = (is_string($value) ? trim($value) : $value);
								if (!is_int($key) && !ctype_digit($key)) {
									$ThisFileInfo['comments'][$tagname][$key] = $value;
								} else {
									if (!isset($ThisFileInfo['comments'][$tagname])) {
										$ThisFileInfo['comments'][$tagname] = array($value);
									} else {
										$ThisFileInfo['comments'][$tagname][] = $value;
									}
								}
							}
						}
					}
				}
			}

			// attempt to standardize spelling of returned keys
			$StandardizeFieldNames = array(
				'tracknumber' => 'track_number',
				'track'       => 'track_number',
			);
			foreach ($StandardizeFieldNames as $badkey => $goodkey) {
				if (array_key_exists($badkey, $ThisFileInfo['comments']) && !array_key_exists($goodkey, $ThisFileInfo['comments'])) {
					$ThisFileInfo['comments'][$goodkey] = $ThisFileInfo['comments'][$badkey];
					unset($ThisFileInfo['comments'][$badkey]);
				}
			}

			if ($option_tags_html) {
				// Copy ['comments'] to ['comments_html']
				if (!empty($ThisFileInfo['comments'])) {
					foreach ($ThisFileInfo['comments'] as $field => $values) {
						if ($field == 'picture') {
							// pictures can take up a lot of space, and we don't need multiple copies of them
							// let there be a single copy in [comments][picture], and not elsewhere
							continue;
						}
						foreach ($values as $index => $value) {
							if (is_array($value)) {
								$ThisFileInfo['comments_html'][$field][$index] = $value;
							} else {
								$ThisFileInfo['comments_html'][$field][$index] = str_replace('&#0;', '', self::MultiByteCharString2HTML($value, $ThisFileInfo['encoding']));
							}
						}
					}
				}
			}

		}
		return true;
	}

	/**
	 * @param string $key
	 * @param int    $begin
	 * @param int    $end
	 * @param string $file
	 * @param string $name
	 *
	 * @return string
	 */
	public static function EmbeddedLookup($key, $begin, $end, $file, $name) {

		// Cached
		static $cache;
		if (isset($cache[$file][$name])) {
			return (isset($cache[$file][$name][$key]) ? $cache[$file][$name][$key] : '');
		}

		// Init
		$keylength  = strlen($key);
		$line_count = $end - $begin - 7;

		// Open php file
		$fp = fopen($file, 'r');

		// Discard $begin lines
		for ($i = 0; $i < ($begin + 3); $i++) {
			fgets($fp, 1024);
		}

		// Loop thru line
		while (0 < $line_count--) {

			// Read line
			$line = ltrim(fgets($fp, 1024), "\t ");

			// METHOD A: only cache the matching key - less memory but slower on next lookup of not-previously-looked-up key
			//$keycheck = substr($line, 0, $keylength);
			//if ($key == $keycheck)  {
			//	$cache[$file][$name][$keycheck] = substr($line, $keylength + 1);
			//	break;
			//}

			// METHOD B: cache all keys in this lookup - more memory but faster on next lookup of not-previously-looked-up key
			//$cache[$file][$name][substr($line, 0, $keylength)] = trim(substr($line, $keylength + 1));
			$explodedLine = explode("\t", $line, 2);
			$ThisKey   = (isset($explodedLine[0]) ? $explodedLine[0] : '');
			$ThisValue = (isset($explodedLine[1]) ? $explodedLine[1] : '');
			$cache[$file][$name][$ThisKey] = trim($ThisValue);
		}

		// Close and return
		fclose($fp);
		return (isset($cache[$file][$name][$key]) ? $cache[$file][$name][$key] : '');
	}

	/**
	 * @param string $filename
	 * @param string $sourcefile
	 * @param bool   $DieOnFailure
	 *
	 * @return bool
	 * @throws Exception
	 */
	public static function IncludeDependency($filename, $sourcefile, $DieOnFailure=false) {
		global $GETID3_ERRORARRAY;

		if (file_exists($filename)) {
			if (include_once($filename)) {
				return true;
			} else {
				$diemessage = basename($sourcefile).' depends on '.$filename.', which has errors';
			}
		} else {
			$diemessage = basename($sourcefile).' depends on '.$filename.', which is missing';
		}
		if ($DieOnFailure) {
			throw new Exception($diemessage);
		} else {
			$GETID3_ERRORARRAY[] = $diemessage;
		}
		return false;
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	public static function trimNullByte($string) {
		return trim($string, "\x00");
	}

	/**
	 * @param string $path
	 *
	 * @return float|bool
	 */
	public static function getFileSizeSyscall($path) {
		$filesize = false;

		if (GETID3_OS_ISWINDOWS) {
			if (class_exists('COM')) { // From PHP 5.3.15 and 5.4.5, COM and DOTNET is no longer built into the php core.you have to add COM support in php.ini:
				$filesystem = new COM('Scripting.FileSystemObject');
				$file = $filesystem->GetFile($path);
				$filesize = $file->Size();
				unset($filesystem, $file);
			} else {
				$commandline = 'for %I in ('.escapeshellarg($path).') do @echo %~zI';
			}
		} else {
			$commandline = 'ls -l '.escapeshellarg($path).' | awk \'{print $5}\'';
		}
		if (isset($commandline)) {
			$output = trim(`$commandline`);
			if (ctype_digit($output)) {
				$filesize = (float) $output;
			}
		}
		return $filesize;
	}

	/**
	 * @param string $filename
	 *
	 * @return string|false
	 */
	public static function truepath($filename) {
		// 2017-11-08: this could use some improvement, patches welcome
		if (preg_match('#^(\\\\\\\\|//)[a-z0-9]#i', $filename, $matches)) {
			// PHP's built-in realpath function does not work on UNC Windows shares
			$goodpath = array();
			foreach (explode('/', str_replace('\\', '/', $filename)) as $part) {
				if ($part == '.') {
					continue;
				}
				if ($part == '..') {
					if (count($goodpath)) {
						array_pop($goodpath);
					} else {
						// cannot step above this level, already at top level
						return false;
					}
				} else {
					$goodpath[] = $part;
				}
			}
			return implode(DIRECTORY_SEPARATOR, $goodpath);
		}
		return realpath($filename);
	}

	/**
	 * Workaround for Bug #37268 (https://bugs.php.net/bug.php?id=37268)
	 *
	 * @param string $path A path.
	 * @param string $suffix If the name component ends in suffix this will also be cut off.
	 *
	 * @return string
	 */
	public static function mb_basename($path, $suffix = null) {
		$splited = preg_split('#/#', rtrim($path, '/ '));
		return substr(basename('X'.$splited[count($splited) - 1], $suffix), 1);
	}

}
