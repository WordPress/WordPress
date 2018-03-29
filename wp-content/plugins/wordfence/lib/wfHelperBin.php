<?php

class wfHelperBin {

	/**
	 * @param string $bin1
	 * @param string $bin2
	 * @return mixed
	 */
	public static function addbin2bin($bin1, $bin2) {
		if (strlen($bin1) % 4 != 0) {
			$bin1 = str_repeat("\0", 4 - (strlen($bin1) % 4)) . $bin1;
		}
		if (strlen($bin2) % 4 != 0) {
			$bin2 = str_repeat("\0", 4 - (strlen($bin2) % 4)) . $bin2;
		}

		$bin1_ints = array_reverse(array_values(unpack('N*', $bin1)));
		$bin2_ints = array_reverse(array_values(unpack('N*', $bin2)));
		$return = array();
		$carries = 0;
		for ($i=0; $i < max(count($bin1_ints), count($bin2_ints)); $i++) {
			$int1 = array_key_exists($i, $bin1_ints) ? $bin1_ints[$i] : 0;
			$int2 = array_key_exists($i, $bin2_ints) ? $bin2_ints[$i] : 0;
			$val = $int1 + $int2 + $carries;
			if ($carries > 0) {
				$carries = 0;
			}
			if ($val >= 0x100000000) {
				$val -= 0x100000000;
				$carries++;
			}
			$return[] = $val;
		}
		if ($carries) {
			$return[] += $carries;
		}
		$return = array_reverse($return);
		array_unshift($return, 'N*');
		$return = call_user_func_array('pack', $return);
		$return = ltrim($return, "\x00");
		return strlen($return) == 0 ? "\x00" : $return;
	}

	/**
	 * Convert binary string to the 10101's representation.
	 *
	 * @param string $string
	 * @return string
	 */
	public static function bin2str($string) {
		$return = '';
		for ($i = 0; $i < strlen($string); $i++) {
			$return .= str_pad(decbin(ord($string[$i])), 8, '0', STR_PAD_LEFT);
		}
		$return = ltrim($return, '0');
		return strlen($return) == 0 ? '0' : $return;
	}

	/**
	 * Convert 10101's representation back to the binary data.
	 *
	 * @param string $string
	 * @return string
	 */
	public static function str2bin($string) {
		if (strlen($string) % 32 > 0) {
			$string = str_repeat('0', 32 - (strlen($string) % 32)) . $string;
		}
		$ints = str_split($string, 32);
		$return = '';
		foreach ($ints as $int) {
			$return .= pack('N', bindec($int));
		}
		$return = ltrim($return, "\0");
		return strlen($return) == 0 ? "\0" : $return;
	}
}