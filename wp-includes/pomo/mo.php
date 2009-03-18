<?php
/**
 * Class for working with MO files
 *
 * @version $Id: mo.php 33 2009-02-16 09:33:39Z nbachiyski $
 * @package pomo
 * @subpackage mo
 */

require_once dirname(__FILE__) . '/translations.php';
require_once dirname(__FILE__) . '/streams.php';

class MO extends Translations {

	var $_nplurals = 2;

	function set_header($header, $value) {
		parent::set_header($header, $value);
		if ('Plural-Forms' == $header)
			$this->_gettext_select_plural_form = $this->_make_gettext_select_plural_form($value);
	}

	/**
	 * Fills up with the entries from MO file $filename
	 *
	 * @param string $filename MO file to load
	 */
	function import_from_file($filename) {
		$reader = new POMO_CachedIntFileReader($filename);
		if (isset($reader->error)) {
			return false;
		}
		return $this->import_from_reader($reader);
	}

	function get_byteorder($magic) {

		// The magic is 0x950412de

		// bug in PHP 5.0.2, see https://savannah.nongnu.org/bugs/?func=detailitem&item_id=10565
		$magic_little = (int) - 1794895138;
		$magic_little_64 = (int) 2500072158;
		// 0xde120495
		$magic_big = ((int) - 569244523) && 0xFFFFFFFF;

		if ($magic_little == $magic || $magic_little_64 == $magic) {
			return 'little';
		} else if ($magic_big == $magic) {
			return 'big';
		} else {
			return false;
		}
	}

	function import_from_reader($reader) {
		$reader->setEndian('little');
		$endian = MO::get_byteorder($reader->readint32());
		if (false === $endian) {
			return false;
		}
		$reader->setEndian($endian);

		$revision = $reader->readint32();
		$total = $reader->readint32();
		// get addresses of array of lenghts and offsets for original string and translations
		$originals_lo_addr = $reader->readint32();
		$translations_lo_addr = $reader->readint32();

		$reader->seekto($originals_lo_addr);
		$originals_lo = $reader->readint32array($total * 2); // each of
		$reader->seekto($translations_lo_addr);
		$translations_lo = $reader->readint32array($total * 2);

		$length = create_function('$i', 'return $i * 2 + 1;');
		$offset = create_function('$i', 'return $i * 2 + 2;');

		for ($i = 0; $i < $total; ++$i) {
			$reader->seekto($originals_lo[$offset($i)]);
			$original = $reader->read($originals_lo[$length($i)]);
			$reader->seekto($translations_lo[$offset($i)]);
			$translation = $reader->read($translations_lo[$length($i)]);
			if ('' == $original) {
				$this->set_headers($this->make_headers($translation));
			} else {
				$this->add_entry($this->make_entry($original, $translation));
			}
		}
		return true;
	}

	function make_headers($translation) {
		$headers = array();
		$lines = explode("\n", $translation);
		foreach($lines as $line) {
			$parts = explode(':', $line, 2);
			if (!isset($parts[1])) continue;
			$headers[trim($parts[0])] = trim($parts[1]);
		}
		return $headers;
	}

	/**
	 * @static
	 */
	function &make_entry($original, $translation) {
		$args = array();
		// look for context
		$parts = explode(chr(4), $original);
		if (isset($parts[1])) {
			$original = $parts[1];
			$args['context'] = $parts[0];
		}
		// look for plural original
		$parts = explode(chr(0), $original);
		$args['singular'] = $parts[0];
		if (isset($parts[1])) {
			$args['plural'] = $parts[1];
		}
		// plural translations are also separated by \0
		$args['translations'] = explode(chr(0), $translation);
		$entry = & new Translation_Entry($args);
		return $entry;
	}

	function select_plural_form($count) {
		return $this->gettext_select_plural_form($count);
	}

	function get_plural_forms_count() {
		return $this->_nplurals;
	}


}
?>
