<?php
/**
 * $Id: editor_plugin_src.js 201 2007-02-12 15:56:56Z spocke $
 *
 * @package MCManager.includes
 * @author Moxiecode
 * @copyright Copyright © 2004-2007, Moxiecode Systems AB, All rights reserved.
 */

class PSpell extends SpellChecker {
	/**
	 * Spellchecks an array of words.
	 *
	 * @param {String} $lang Language code like sv or en.
	 * @param {Array} $words Array of words to spellcheck.
	 * @return {Array} Array of misspelled words.
	 */
	function &checkWords($lang, $words) {
		$plink = $this->_getPLink($lang);

		$outWords = array();
		foreach ($words as $word) {
			if (!pspell_check($plink, trim($word)))
				$outWords[] = utf8_encode($word);
		}

		return $outWords;
	}

	/**
	 * Returns suggestions of for a specific word.
	 *
	 * @param {String} $lang Language code like sv or en.
	 * @param {String} $word Specific word to get suggestions for.
	 * @return {Array} Array of suggestions for the specified word.
	 */
	function &getSuggestions($lang, $word) {
		$words = pspell_suggest($this->_getPLink($lang), $word);

		for ($i=0; $i<count($words); $i++)
			$words[$i] = utf8_encode($words[$i]);

		return $words;
	}

	/**
	 * Opens a link for pspell.
	 */
	function &_getPLink($lang) {
		// Check for native PSpell support
		if (!function_exists("pspell_new"))
			$this->throwError("PSpell support not found in PHP installation.");

		// Setup PSpell link
		$plink = pspell_new(
			$lang,
			$this->_config['PSpell.spelling'],
			$this->_config['PSpell.jargon'],
			$this->_config['PSpell.encoding'],
			$this->_config['PSpell.mode']
		);

		// Setup PSpell link
/*		if (!$plink) {
			$pspellConfig = pspell_config_create(
				$lang,
				$this->_config['PSpell.spelling'],
				$this->_config['PSpell.jargon'],
				$this->_config['PSpell.encoding']
			);

			$plink = pspell_new_config($pspell_config);
		}*/

		if (!$plink)
			$this->throwError("No PSpell link found opened.");

		return $plink;
	}
}

?>
