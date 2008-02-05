<?php
/**
 * $Id: editor_plugin_src.js 201 2007-02-12 15:56:56Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2007, Moxiecode Systems AB, All rights reserved.
 */

class SpellChecker {
	/**
	 * Constructor.
	 *
	 * @param $config Configuration name/value array.
	 */
	function SpellChecker(&$config) {
		$this->_config = $config;
	}

	/**
	 * Simple loopback function everything that gets in will be send back.
	 *
	 * @param $args.. Arguments.
	 * @return {Array} Array of all input arguments.
	 */
	function &loopback(/* args.. */) {
		return func_get_args();
	}

	/**
	 * Spellchecks an array of words.
	 *
	 * @param {String} $lang Language code like sv or en.
	 * @param {Array} $words Array of words to spellcheck.
	 * @return {Array} Array of misspelled words.
	 */
	function &checkWords($lang, $words) {
		return $words;
	}

	/**
	 * Returns suggestions of for a specific word.
	 *
	 * @param {String} $lang Language code like sv or en.
	 * @param {String} $word Specific word to get suggestions for.
	 * @return {Array} Array of suggestions for the specified word.
	 */
	function &getSuggestions($lang, $word) {
		return array();
	}

	/**
	 * Throws an error message back to the user. This will stop all execution.
	 *
	 * @param {String} $str Message to send back to user.
	 */
	function throwError($str) {
		die('{"result":null,"id":null,"error":{"errstr":"' . addslashes($str) . '","errfile":"","errline":null,"errcontext":"","level":"FATAL"}}');
	}
}

?>
