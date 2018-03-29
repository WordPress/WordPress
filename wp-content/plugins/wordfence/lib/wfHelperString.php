<?php


class wfHelperString {

	/**
	 * cycle through arguments
	 *
	 * @return mixed
	 */
	public static function cycle() {
		static $counter = 0;
		$args = func_get_args();
		if (empty($args)) {
			$counter = 0;
			return null;
		}
		$return_val = $args[$counter % count($args)];
		$counter++;
		return $return_val;
	}
}