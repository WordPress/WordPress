<?php
/**
 * $Id: editor_plugin_src.js 201 2007-02-12 15:56:56Z spocke $
 *
 * This class was contributed by Michel Weimerskirch.
 *
 * @package MCManager.includes
 * @author Moxiecode
 * @copyright Copyright © 2004-2007, Moxiecode Systems AB, All rights reserved.
 */

class EnchantSpell extends SpellChecker {
	/**
	 * Spellchecks an array of words.
	 *
	 * @param String $lang Selected language code (like en_US or de_DE). Shortcodes like "en" and "de" work with enchant >= 1.4.1
	 * @param Array $words Array of words to check.
	 * @return Array of misspelled words.
	 */
	function &checkWords($lang, $words) {
		$r = enchant_broker_init();
		
		if (enchant_broker_dict_exists($r,$lang)) {
			$d = enchant_broker_request_dict($r, $lang);
			
			$returnData = array();
			foreach($words as $key => $value) {
				$correct = enchant_dict_check($d, $value);
				if(!$correct) {
					$returnData[] = trim($value);
				}
			}
	
			return $returnData;
			enchant_broker_free_dict($d);
		} else {

		}
		enchant_broker_free($r);
	}

	/**
	 * Returns suggestions for a specific word.
	 *
	 * @param String $lang Selected language code (like en_US or de_DE). Shortcodes like "en" and "de" work with enchant >= 1.4.1
	 * @param String $word Specific word to get suggestions for.
	 * @return Array of suggestions for the specified word.
	 */
	function &getSuggestions($lang, $word) {
		$r = enchant_broker_init();
		$suggs = array();

		if (enchant_broker_dict_exists($r,$lang)) {
			$d = enchant_broker_request_dict($r, $lang);
			$suggs = enchant_dict_suggest($d, $word);

			enchant_broker_free_dict($d);
		} else {

		}
		enchant_broker_free($r);

		return $suggs;
	}
}

?>