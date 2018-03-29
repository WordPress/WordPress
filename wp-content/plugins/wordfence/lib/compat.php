<?php if (!defined('WORDFENCE_VERSION')) { exit; }

if (!function_exists('str_getcsv')) {

	function str_getcsv($input, $delimiter = ',', $enclosure = '"', $escape = null, $eol = null) {
		$temp = fopen("php://memory", "rw");
		fwrite($temp, $input);
		fseek($temp, 0);
		$r = array();
		while (($data = fgetcsv($temp, 0, $delimiter, $enclosure, $escape)) !== false) {
			$r[] = $data;
		}
		fclose($temp);
		return $r;
	}

}