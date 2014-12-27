<?php
/**
 * Class for working with MO files
 *
 * @version $Id: mo.php 718 2012-10-31 00:32:02Z nbachiyski $
 * @package pomo
 * @subpackage mo
 */

require_once dirname(__FILE__) . '/translations.php';
require_once dirname(__FILE__) . '/streams.php';

if ( !class_exists( 'MO' ) ):
class MO extends Gettext_Translations {

	var $_nplurals = 2;

	/**
	 * Fills up with the entries from MO file $filename
	 *
	 * @param string $filename MO file to load
	 */
	function import_from_file($filename) {
		$reader = new POMO_FileReader($filename);
		if (!$reader->is_resource())
			return false;
		return $this->import_from_reader($reader);
	}

	function export_to_file($filename) {
		$fh = fopen($filename, 'wb');
		if ( !$fh ) return false;
		$res = $this->export_to_file_handle( $fh );
		fclose($fh);
		return $res;
	}

	function export() {
		$tmp_fh = fopen("php://temp", 'r+');
		if ( !$tmp_fh ) return false;
		$this->export_to_file_handle( $tmp_fh );
		rewind( $tmp_fh );
		return stream_get_contents( $tmp_fh );
	}

	function is_entry_good_for_export( $entry ) {
		if ( empty( $entry->translations ) ) {
			return false;
		}

		if ( !array_filter( $entry->translations ) ) {
			return false;
		}

		return true;
	}

	function export_to_file_handle($fh) {
		$entries = array_filter( $this->entries, array( $this, 'is_entry_good_for_export' ) );
		ksort($entries);
		$magic = 0x950412de;
		$revision = 0;
		$total = count($entries) + 1; // all the headers are one entry
		$originals_lenghts_addr = 28;
		$translations_lenghts_addr = $originals_lenghts_addr + 8 * $total;
		$size_of_hash = 0;
		$hash_addr = $translations_lenghts_addr + 8 * $total;
		$current_addr = $hash_addr;
		fwrite($fh, pack('V*', $magic, $revision, $total, $originals_lenghts_addr,
			$translations_lenghts_addr, $size_of_hash, $hash_addr));
		fseek($fh, $originals_lenghts_addr);

		// headers' msgid is an empty string
		fwrite($fh, pack('VV', 0, $current_addr));
		$current_addr++;
		$originals_table = chr(0);

		$reader = new POMO_Reader();

		foreach($entries as $entry) {
			$originals_table .= $this->export_original($entry) . chr(0);
			$length = $reader->strlen($this->export_original($entry));
			fwrite($fh, pack('VV', $length, $current_addr));
			$current_addr += $length + 1; // account for the NULL byte after
		}

		$exported_headers = $this->export_headers();
		fwrite($fh, pack('VV', $reader->strlen($exported_headers), $current_addr));
		$current_addr += strlen($exported_headers) + 1;
		$translations_table = $exported_headers . chr(0);

		foreach($entries as $entry) {
			$translations_table .= $this->export_translations($entry) . chr(0);
			$length = $reader->strlen($this->export_translations($entry));
			fwrite($fh, pack('VV', $length, $current_addr));
			$current_addr += $length + 1;
		}

		fwrite($fh, $originals_table);
		fwrite($fh, $translations_table);
		return true;
	}

	function export_original($entry) {
		//TODO: warnings for control characters
		$exported = $entry->singular;
		if ($entry->is_plural) $exported .= chr(0).$entry->plural;
		if (!is_null($entry->context)) $exported = $entry->context . chr(4) . $exported;
		return $exported;
	}

	function export_translations($entry) {
		//TODO: warnings for control characters
		return implode(chr(0), $entry->translations);
	}

	function export_headers() {
		$exported = '';
		foreach($this->headers as $header => $value) {
			$exported.= "$header: $value\n";
		}
		return $exported;
	}

	function get_byteorder($magic) {
		// The magic is 0x950412de

		// bug in PHP 5.0.2, see https://savannah.nongnu.org/bugs/?func=detailitem&item_id=10565
		$magic_little = (int) - 1794895138;
		$magic_little_64 = (int) 2500072158;
		// 0xde120495
		$magic_big = ((int) - 569244523) & 0xFFFFFFFF;
		if ($magic_little == $magic || $magic_little_64 == $magic) {
			return 'little';
		} else if ($magic_big == $magic) {
			return 'big';
		} else {
			return false;
		}
	}

	/**
	 * @param POMO_FileReader $reader
	 */
	function import_from_reader($reader) {
		$endian_string = MO::get_byteorder($reader->readint32());
		if (false === $endian_string) {
			return false;
		}
		$reader->setEndian($endian_string);

		$endian = ('big' == $endian_string)? 'N' : 'V';

		$header = $reader->read(24);
		if ($reader->strlen($header) != 24)
			return false;

		// parse header
		$header = unpack("{$endian}revision/{$endian}total/{$endian}originals_lenghts_addr/{$endian}translations_lenghts_addr/{$endian}hash_length/{$endian}hash_addr", $header);
		if (!is_array($header))
			return false;

		// support revision 0 of MO format specs, only
		if ( $header['revision'] != 0 ) {
			return false;
		}

		// seek to data blocks
		$reader->seekto( $header['originals_lenghts_addr'] );

		// read originals' indices
		$originals_lengths_length = $header['translations_lenghts_addr'] - $header['originals_lenghts_addr'];
		if ( $originals_lengths_length != $header['total'] * 8 ) {
			return false;
		}

		$originals = $reader->read($originals_lengths_length);
		if ( $reader->strlen( $originals ) != $originals_lengths_length ) {
			return false;
		}

		// read translations' indices
		$translations_lenghts_length = $header['hash_addr'] - $header['translations_lenghts_addr'];
		if ( $translations_lenghts_length != $header['total'] * 8 ) {
			return false;
		}

		$translations = $reader->read($translations_lenghts_length);
		if ( $reader->strlen( $translations ) != $translations_lenghts_length ) {
			return false;
		}

		// transform raw data into set of indices
		$originals    = $reader->str_split( $originals, 8 );
		$translations = $reader->str_split( $translations, 8 );

		// skip hash table
		$strings_addr = $header['hash_addr'] + $header['hash_length'] * 4;

		$reader->seekto($strings_addr);

		$strings = $reader->read_all();
		$reader->close();

		for ( $i = 0; $i < $header['total']; $i++ ) {
			$o = unpack( "{$endian}length/{$endian}pos", $originals[$i] );
			$t = unpack( "{$endian}length/{$endian}pos", $translations[$i] );
			if ( !$o || !$t ) return false;

			// adjust offset due to reading strings to separate space before
			$o['pos'] -= $strings_addr;
			$t['pos'] -= $strings_addr;

			$original    = $reader->substr( $strings, $o['pos'], $o['length'] );
			$translation = $reader->substr( $strings, $t['pos'], $t['length'] );

			if ('' === $original) {
				$this->set_headers($this->make_headers($translation));
			} else {
				$entry = &$this->make_entry($original, $translation);
				$this->entries[$entry->key()] = &$entry;
			}
		}
		return true;
	}

	/**
	 * Build a Translation_Entry from original string and translation strings,
	 * found in a MO file
	 *
	 * @static
	 * @param string $original original string to translate from MO file. Might contain
	 * 	0x04 as context separator or 0x00 as singular/plural separator
	 * @param string $translation translation string from MO file. Might contain
	 * 	0x00 as a plural translations separator
	 */
	function &make_entry($original, $translation) {
		$entry = new Translation_Entry();
		// look for context
		$parts = explode(chr(4), $original);
		if (isset($parts[1])) {
			$original = $parts[1];
			$entry->context = $parts[0];
		}
		// look for plural original
		$parts = explode(chr(0), $original);
		$entry->singular = $parts[0];
		if (isset($parts[1])) {
			$entry->is_plural = true;
			$entry->plural = $parts[1];
		}
		// plural translations are also separated by \0
		$entry->translations = explode(chr(0), $translation);
		return $entry;
	}

	function select_plural_form($count) {
		return $this->gettext_select_plural_form($count);
	}

	function get_plural_forms_count() {
		return $this->_nplurals;
	}
}
endif;