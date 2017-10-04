<?php
/**
 * Class for a set of entries for translation and their associated headers
 *
 * @version $Id: translations.php 1157 2015-11-20 04:30:11Z dd32 $
 * @package pomo
 * @subpackage translations
 */

require_once dirname(__FILE__) . '/plural-forms.php';
require_once dirname(__FILE__) . '/entry.php';

if ( ! class_exists( 'Translations', false ) ):
class Translations {
	var $entries = array();
	var $headers = array();

	/**
	 * Add entry to the PO structure
	 *
	 * @param array|Translation_Entry $entry
	 * @return bool true on success, false if the entry doesn't have a key
	 */
	function add_entry($entry) {
		if (is_array($entry)) {
			$entry = new Translation_Entry($entry);
		}
		$key = $entry->key();
		if (false === $key) return false;
		$this->entries[$key] = &$entry;
		return true;
	}

	/**
	 * @param array|Translation_Entry $entry
	 * @return bool
	 */
	function add_entry_or_merge($entry) {
		if (is_array($entry)) {
			$entry = new Translation_Entry($entry);
		}
		$key = $entry->key();
		if (false === $key) return false;
		if (isset($this->entries[$key]))
			$this->entries[$key]->merge_with($entry);
		else
			$this->entries[$key] = &$entry;
		return true;
	}

	/**
	 * Sets $header PO header to $value
	 *
	 * If the header already exists, it will be overwritten
	 *
	 * TODO: this should be out of this class, it is gettext specific
	 *
	 * @param string $header header name, without trailing :
	 * @param string $value header value, without trailing \n
	 */
	function set_header($header, $value) {
		$this->headers[$header] = $value;
	}

	/**
	 * @param array $headers
	 */
	function set_headers($headers) {
		foreach($headers as $header => $value) {
			$this->set_header($header, $value);
		}
	}

	/**
	 * @param string $header
	 */
	function get_header($header) {
		return isset($this->headers[$header])? $this->headers[$header] : false;
	}

	/**
	 * @param Translation_Entry $entry
	 */
	function translate_entry(&$entry) {
		$key = $entry->key();
		return isset($this->entries[$key])? $this->entries[$key] : false;
	}

	/**
	 * @param string $singular
	 * @param string $context
	 * @return string
	 */
	function translate($singular, $context=null) {
		$entry = new Translation_Entry(array('singular' => $singular, 'context' => $context));
		$translated = $this->translate_entry($entry);
		return ($translated && !empty($translated->translations))? $translated->translations[0] : $singular;
	}

	/**
	 * Given the number of items, returns the 0-based index of the plural form to use
	 *
	 * Here, in the base Translations class, the common logic for English is implemented:
	 * 	0 if there is one element, 1 otherwise
	 *
	 * This function should be overridden by the sub-classes. For example MO/PO can derive the logic
	 * from their headers.
	 *
	 * @param integer $count number of items
	 */
	function select_plural_form($count) {
		return 1 == $count? 0 : 1;
	}

	/**
	 * @return int
	 */
	function get_plural_forms_count() {
		return 2;
	}

	/**
	 * @param string $singular
	 * @param string $plural
	 * @param int    $count
	 * @param string $context
	 */
	function translate_plural($singular, $plural, $count, $context = null) {
		$entry = new Translation_Entry(array('singular' => $singular, 'plural' => $plural, 'context' => $context));
		$translated = $this->translate_entry($entry);
		$index = $this->select_plural_form($count);
		$total_plural_forms = $this->get_plural_forms_count();
		if ($translated && 0 <= $index && $index < $total_plural_forms &&
				is_array($translated->translations) &&
				isset($translated->translations[$index]))
			return $translated->translations[$index];
		else
			return 1 == $count? $singular : $plural;
	}

	/**
	 * Merge $other in the current object.
	 *
	 * @param Object $other Another Translation object, whose translations will be merged in this one (passed by reference).
	 * @return void
	 **/
	function merge_with(&$other) {
		foreach( $other->entries as $entry ) {
			$this->entries[$entry->key()] = $entry;
		}
	}

	/**
	 * @param object $other
	 */
	function merge_originals_with(&$other) {
		foreach( $other->entries as $entry ) {
			if ( !isset( $this->entries[$entry->key()] ) )
				$this->entries[$entry->key()] = $entry;
			else
				$this->entries[$entry->key()]->merge_with($entry);
		}
	}
}

class Gettext_Translations extends Translations {
	/**
	 * The gettext implementation of select_plural_form.
	 *
	 * It lives in this class, because there are more than one descendand, which will use it and
	 * they can't share it effectively.
	 *
	 * @param int $count
	 */
	function gettext_select_plural_form($count) {
		if (!isset($this->_gettext_select_plural_form) || is_null($this->_gettext_select_plural_form)) {
			list( $nplurals, $expression ) = $this->nplurals_and_expression_from_header($this->get_header('Plural-Forms'));
			$this->_nplurals = $nplurals;
			$this->_gettext_select_plural_form = $this->make_plural_form_function($nplurals, $expression);
		}
		return call_user_func($this->_gettext_select_plural_form, $count);
	}

	/**
	 * @param string $header
	 * @return array
	 */
	function nplurals_and_expression_from_header($header) {
		if (preg_match('/^\s*nplurals\s*=\s*(\d+)\s*;\s+plural\s*=\s*(.+)$/', $header, $matches)) {
			$nplurals = (int)$matches[1];
			$expression = trim( $matches[2] );
			return array($nplurals, $expression);
		} else {
			return array(2, 'n != 1');
		}
	}

	/**
	 * Makes a function, which will return the right translation index, according to the
	 * plural forms header
	 * @param int    $nplurals
	 * @param string $expression
	 */
	function make_plural_form_function($nplurals, $expression) {
		try {
			$handler = new Plural_Forms( rtrim( $expression, ';' ) );
			return array( $handler, 'get' );
		} catch ( Exception $e ) {
			// Fall back to default plural-form function.
			return $this->make_plural_form_function( 2, 'n != 1' );
		}
	}

	/**
	 * Adds parentheses to the inner parts of ternary operators in
	 * plural expressions, because PHP evaluates ternary oerators from left to right
	 *
	 * @param string $expression the expression without parentheses
	 * @return string the expression with parentheses added
	 */
	function parenthesize_plural_exression($expression) {
		$expression .= ';';
		$res = '';
		$depth = 0;
		for ($i = 0; $i < strlen($expression); ++$i) {
			$char = $expression[$i];
			switch ($char) {
				case '?':
					$res .= ' ? (';
					$depth++;
					break;
				case ':':
					$res .= ') : (';
					break;
				case ';':
					$res .= str_repeat(')', $depth) . ';';
					$depth= 0;
					break;
				default:
					$res .= $char;
			}
		}
		return rtrim($res, ';');
	}

	/**
	 * @param string $translation
	 * @return array
	 */
	function make_headers($translation) {
		$headers = array();
		// sometimes \ns are used instead of real new lines
		$translation = str_replace('\n', "\n", $translation);
		$lines = explode("\n", $translation);
		foreach($lines as $line) {
			$parts = explode(':', $line, 2);
			if (!isset($parts[1])) continue;
			$headers[trim($parts[0])] = trim($parts[1]);
		}
		return $headers;
	}

	/**
	 * @param string $header
	 * @param string $value
	 */
	function set_header($header, $value) {
		parent::set_header($header, $value);
		if ('Plural-Forms' == $header) {
			list( $nplurals, $expression ) = $this->nplurals_and_expression_from_header($this->get_header('Plural-Forms'));
			$this->_nplurals = $nplurals;
			$this->_gettext_select_plural_form = $this->make_plural_form_function($nplurals, $expression);
		}
	}
}
endif;

if ( ! class_exists( 'NOOP_Translations', false ) ):
/**
 * Provides the same interface as Translations, but doesn't do anything
 */
class NOOP_Translations {
	var $entries = array();
	var $headers = array();

	function add_entry($entry) {
		return true;
	}

	/**
	 *
	 * @param string $header
	 * @param string $value
	 */
	function set_header($header, $value) {
	}

	/**
	 *
	 * @param array $headers
	 */
	function set_headers($headers) {
	}

	/**
	 * @param string $header
	 * @return false
	 */
	function get_header($header) {
		return false;
	}

	/**
	 * @param Translation_Entry $entry
	 * @return false
	 */
	function translate_entry(&$entry) {
		return false;
	}

	/**
	 * @param string $singular
	 * @param string $context
	 */
	function translate($singular, $context=null) {
		return $singular;
	}

	/**
	 *
	 * @param int $count
	 * @return bool
	 */
	function select_plural_form($count) {
		return 1 == $count? 0 : 1;
	}

	/**
	 * @return int
	 */
	function get_plural_forms_count() {
		return 2;
	}

	/**
	 * @param string $singular
	 * @param string $plural
	 * @param int    $count
	 * @param string $context
	 */
	function translate_plural($singular, $plural, $count, $context = null) {
			return 1 == $count? $singular : $plural;
	}

	/**
	 * @param object $other
	 */
	function merge_with(&$other) {
	}
}
endif;
