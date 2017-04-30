<?php

/*
 License:
 ==============================================================================
 Copyright 2008 Heiko Rabe  (email : info@code-styling.de)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.
 
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
==============================================================================

contribution for performant mo-file reading:    Thomas Urban (www.toxa.de)

fixed arround PHP preg_match bug
references and possible explainations:
	Bug #37793  child pid xxx exit signal Segmentation fault (11) => http://bugs.php.net/bug.php?id=37793
	Bugzilla  Bug 841 => http://bugs.exim.org/show_bug.cgi?id=841
	http://mobile-website.mobi/php-utf8-vs-iso-8859-1-59
	
 */
 
class CspStringsAreAscii {
	function _strlen($string) { return strlen($string); }
	function _strpos($haystack, $needle, $offset = null) { return strpos($haystack, $needle, $offset); }
	function _substr($string, $offset, $length = null) { return (is_null($length) ? substr($string, $offset) : substr($string, $offset, $length)); }
	function _str_split($string, $chunkSize) { return str_split($string, $chunkSize); }
	function _substr_count($haystack, $needle) { return substr_count($haystack, $needle); }
	function _seems_utf8($string) { return seems_utf8($string); }
	function _utf8_encode($string) { return utf8_encode($string); }
}

class CspStringsAreMultibyte {
	function _strlen($string) { return mb_strlen($string, 'ascii'); }
	function _strpos($haystack, $needle, $offset = null) { return mb_strpos($haystack, $needle, $offset, 'ascii'); }
	function _substr($string, $offset, $length = null) { return (is_null($length) ? mb_substr($string, $offset, 1073741824, 'ascii') : mb_substr($string, $offset, $length, 'ascii')); }
	function _str_split($string, $chunkSize) { 
		//do not! break unicode / uft8 character in the middle of encoding, just at char border
		$length = $this->_strlen($string); 
		$out = array(); 
		for ($i=0;$i<$length;$i+=$chunkSize) { 
			$out[] = $this->_substr($string, $i, $chunkSize); 
		}
		return $out; 
	}
	function _substr_count($haystack, $needle) { return mb_substr_count($haystack, $needle, 'ascii'); }
	function _seems_utf8($string) { return mb_check_encoding($string, 'UTF-8'); }
	function _utf8_encode($string) { return mb_convert_encoding($string, 'UTF-8'); }
}

class CspTranslationFile {
	
	function CspTranslationFile($type = 'unknown') {
		$this->__construct($type);
	}
	
	function __construct($type = 'unknown') {
		//now lets check whether overloaded functions been used and provide the correct str_* functions as usual
		if(( ini_get( 'mbstring.func_overload' ) & 0x02) === 0x02 && extension_loaded('mbstring') && is_callable( 'mb_substr' )) {
			$this->strings = new CspStringsAreMultibyte();
		}
		else{
			$this->strings = new CspStringsAreAscii();
		}
		$this->component_type = $type;
		$this->header = array(
			'Project-Id-Version' 			=> '',
			'Report-Msgid-Bugs-To' 			=> '',
			'POT-Creation-Date' 			=> '',
			'PO-Revision-Date'				=> '',
			'Last-Translator'				=> '',
			'Language-Team'					=> '',
			'MIME-Version'					=> '1.0',
			'Content-Type'					=> 'text/plain; charset=UTF-8',
			'Content-Transfer-Encoding'		=> '8bit',
			'Plural-Forms'					=> 'nplurals=2; plural=n != 1;',
			'X-Generator'					=> 'CSL v1.x',
			'X-Poedit-Language'				=> '',
			'X-Poedit-Country'				=> '',
			'X-Poedit-SourceCharset'		=> 'utf-8',
			'X-Poedit-KeywordsList'			=> '__;_e;__ngettext:1,2;_n:1,2;__ngettext_noop:1,2;_n_noop:1,2;_c,_nc:4c,1,2;_x:1,2c;_ex:1,2c;_nx:4c,1,2;_nx_noop:4c,1,2;',
			'X-Poedit-Basepath'				=> '',
			'X-Poedit-Bookmarks'			=> '',
			'X-Poedit-SearchPath-0'			=> '.',
			'X-Textdomain-Support'			=> 'no'
		);
		$this->header_vars = array_keys($this->header);
		$this->mo_header_vars = array(
			'PO-Revision-Date',	
			'MIME-Version',	
			'Content-Type',	
			'Content-Transfer-Encoding', 
			'Plural-Forms', 
			'X-Generator',
			'Project-Id-Version'
		);
		array_splice($this->header_vars, array_search('X-Poedit-KeywordsList', $this->header_vars), 1 );
		$this->plural_definitions				= array(
			'nplurals=1; plural=0;' 																				=> array('hu', 'ja', 'ko', 'tr'),
			'nplurals=2; plural=1;' 																				=> array('zh'),
			'nplurals=2; plural=n>1;' 																				=> array('fr'),
			'nplurals=2; plural=n != 1;'																			=> array('af','be','bg','ca','da','de','el','en','es','et','eo','eu','fi','fo','fy','he','id','in','is','it','kk','ky','lb','nk','nb','nl','no','pt','ro','sr','sv','th','tl','vi','xh','zu'),
			'nplurals=3; plural=(n==1) ? 1 : (n>=2 && n<=4) ? 2 : 0;' 												=> array('sk'),
			'nplurals=3; plural=n%10==1 && n%100!=11 ? 0 : n != 0 ? 1 : 2;' 										=> array('lv'),
			'nplurals=3; plural=n%100/10==1 ? 2 : n%10==1 ? 0 : (n+9)%10>3 ? 2 : 1;' 								=> array('cs', 'hr', 'ru', 'uk'),
			'nplurals=3; plural=n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2;' 					=> array('pl'),
			'nplurals=3; plural=n%10==1 && n%100!=11 ? 0 : n%10>=2 && (n%100<10 || n%100>=20) ? 1 : 2;'				=> array('lt'),
			'nplurals=4; plural=n%100==1 ? 0 : n%100==2 ? 1 : n%100==3 || n%100==4 ? 2 : 3;' 						=> array('sl'),
			'nplurals=5; plural=n==1 ? 0 : n==2 ? 1 : n<7 ? 2 : n<11 ? 3 : 4;'										=> array('ga'),
			'nplurals=6; plural=n==0 ? 0 : n==1 ? 1 : n==2 ? 2 : n%100>=3 && n%100<=10 ? 3 : n%100>=11 ? 4 : 5;' 	=> array('ar')
		);		
		$this->map 					= array();
		$this->nplurals 			= 2;
		$this->plural_func 			= 'n != 1;';

		$this->reg_comment			= '/^#\s(.*)/';
		$this->reg_comment_ex 		= '/^#\.\s+(.*)/';
		$this->reg_reference		= '/^#:\s+(.*)/';
		$this->reg_flags			= '/^#,\s+(.*)/';
		$this->reg_textdomain		= '/^#\s*@\s*(.*)/';
		$this->reg_msgctxt			= '/^msgctxt\s+(".*")/';
		$this->reg_msgid			= '/^msgid\s+(".*")/';
		$this->reg_msgstr			= '/^msgstr\s+(".*")/';
		$this->reg_msgid_plural		= '/^msgid_plural\s+(".*")/';
		$this->reg_msgstr_plural	= '/^msgstr\[\d+\]\s+(".*")/';
		$this->reg_multi_line		= "/^(\".*\")/s";
	}
	
	function _set_header_from_string($head, $lang='', $mo_hdr_vars = false) {
		if (!is_string($head)) return;
		$hdr = explode("\n", $head);
		$hdr_vars = $mo_hdr_vars ? $this->mo_header_vars : $this->header_vars;
		foreach($hdr as $e) {
			if ($this->strings->_strpos($e, ':') === false) continue;
			list($key, $val) = explode(':', $e, 2);
			$key = trim($key);$val = str_replace("\\","/", trim($val));
			if (in_array($key, $hdr_vars)) {
				$this->header[$key] = $val;
				//ensure qualified pluralization forms now
				if ($key == 'Plural-Forms' && !$mo_hdr_vars) {
					$func = '';
					foreach($this->plural_definitions as $f => $langs) {
						if (in_array($lang, $langs)) $func = $f;
					}
					if (empty($func)) { $func = 'nplurals=2; plural=n != 1;'; }
					$this->header[$key] = $func;
				}
			}
		}
		$msgstr = array();
		foreach($this->header as $key => $value) {
			$msgstr[] = $key.": ".$value;
		}
		$msgstr = implode("\n", $msgstr);
		$this->map[''] = $this->_new_entry('', $msgstr);
		
		if (preg_match("/nplurals\s*=\s*(\d+)\s*\;\s*plural\s*=\s*([^\n]+)\;/", $this->header['Plural-Forms'], $matches)) {
			$this->nplurals = (int)$matches[1];
			$this->plural_func = $matches[2];
		}		
	}
	
	
	function _new_entry($org, $trans, $reference=false, $flags=false, $tcomment=false, $ccomment=false, $ltd = false) {
		// T ... translation data, contains embed \00 if plurals
		// X ... true, if org contains \04 context in front of
		// P ... true, if is a pluralization,
		// CT ... remark (comment) translator
		// CC ... remark (code) - hard code translations required
		// F ... flags like 'php-format'
		// R ... reference
		// LTD ... loaded text domain
		
		//Bugfix: illegal line separators contained
		if ($trans !== false)
			$trans = preg_replace('/ /', '', $trans); //LINE SEPARATOR decimal: &#8232; UTF-8 (e2, 80, a8)
		
		return array(
			'T' 	=> $trans,
			'X'		=> ($this->strings->_strpos( $org, "\04" ) !== false),
			'P'		=> ($this->strings->_strpos( $org, "\00" ) !== false),
			'CT' 	=> (is_string($tcomment) ? array($tcomment) : (is_array($tcomment) ? $tcomment : array())),
			'CC' 	=> (is_string($ccomment) ? array($ccomment) : (is_array($ccomment) ? $ccomment : array())),
			'F'		=> (is_string($flags) ? array($flags) : (is_array($flags) ? $flags : array())),
			'R'		=> (is_string($reference) ? array($reference) : (is_array($reference) ? $reference : array())),
			'LTD'	=> (is_string($ltd) ? array($ltd) : (is_array($ltd) ? $ltd : array()))
		);
	}
		
	function trim_quotes($s) {
		if ( $this->strings->_substr($s, 0, 1) == '"') $s = $this->strings->_substr($s, 1);
		if ( $this->strings->_substr($s, -1, 1) == '"') $s = $this->strings->_substr($s, 0, -1);
		return $s;
	}
		
	function _clean_import($string) {
		$escapes = array('t' => "\t", 'n' => "\n", '\\' => '\\');
		$lines = array_map('trim', explode("\n", $string));
		$lines = array_map(array('CspTranslationFile', 'trim_quotes'), $lines);
		$unpoified = '';
		$previous_is_backslash = false;
		foreach($lines as $line) {
			preg_match_all('/./u', $line, $chars);
			$chars = $chars[0];
			foreach($chars as $char) {
				if (!$previous_is_backslash) {
					if ('\\' == $char)
						$previous_is_backslash = true;
					else
						$unpoified .= $char;
				} else {
					$previous_is_backslash = false;
					$unpoified .= isset($escapes[$char])? $escapes[$char] : $char;
				}
			}
		}
		unset($lines);
		return $unpoified;
	}	
	
	function _clean_export($string) {
		$quote = '"';
		$slash = '\\';
		$newline = "\n";

		$replaces = array(
			"$slash" 	=> "$slash$slash",
			"$quote"	=> "$slash$quote",
			"\t" 		=> '\t',
		);

		$string = str_replace(array_keys($replaces), array_values($replaces), $string);

		$po = $quote.implode("${slash}n$quote$newline$quote", explode($newline, $string)).$quote;
		// add empty string on first line for readbility
		if (false !== $this->strings->_strpos($string, $newline) &&
				($this->strings->_substr_count($string, $newline) > 1 || !($newline === $this->strings->_substr($string, -$this->strings->_strlen($newline))))) {
			$po = "$quote$quote$newline$po";
		}
		// remove empty strings
		$po = str_replace("$newline$quote$quote", '', $po);
		return $po;
	}
	
	function _build_rel_path($base_file) {
		$a = explode('/', $base_file);
		$rel = '';
		for ($i=0; $i<count($a)-1; $i++) { $rel.="../"; }
		return $rel;
	}

	function supports_textdomain_extension() {
		return ($this->header['X-Textdomain-Support'] == 'yes');
	}
	
	function new_pofile($pofile, $base_file, $proj_id, $timestamp, $translator, $pluralforms, $language, $country) {
		$rel = $this->_build_rel_path($base_file);
		preg_match("/([a-z][a-z]_[A-Z][A-Z]).(mo|po|pot)$/", $pofile, $hits);
		$po_lang = $this->strings->_substr($hits[1],0,2);
		$country = strtoupper($country);
		$this->_set_header_from_string(
			"Project-Id-Version: $proj_id\nPO-Revision-Date: $timestamp\nLast-Translator: $translator\nX-Poedit-Language: $language\nX-Poedit-Country: $country\nX-Poedit-Basepath: $rel\nPlural-Forms: \nX-Textdomain-Support: yes\n",
			$po_lang
		);
		return true;
	}
		
	function read_pofile($pofile, $check_plurals=false, $base_file=false) {
		if (!empty($pofile) && file_exists($pofile) && is_readable($pofile)) {		
			$handle = fopen($pofile,'rb');

			$msgid = false;
			$cur_entry = $this->_new_entry('', false); //empty 
			
			while (!feof($handle)) {
				$line = trim(fgets($handle));  
				if (!$this->strings->_seems_utf8($line)) $line = $this->strings->_utf8_encode($line);
			
				if (empty($line)) {
					if ($msgid !== false) {		
						$temp = ($cur_entry['X'] !== false ? $cur_entry['X']."\04".$msgid : $msgid);
						//merge test: existing do not kill by empty!
						if(!isset($this->map[$temp]) || !(!empty($this->map[$temp]['T']) && empty($cur_entry['T']))) {
							$this->map[$temp] = $this->_new_entry(
								$temp, 
								$cur_entry['T'], 
								$cur_entry['R'], 
								$cur_entry['F'], 
								$cur_entry['CT'], 
								$cur_entry['CC'], 
								$cur_entry['LTD']
							);
						}
					}
					$msgid = false;
					unset($cur_entry);
					$cur_entry = $this->_new_entry('',false);
					continue;
				}

				if (preg_match($this->reg_multi_line, $line, $hits)) {
					if ($cur_entry['T'] === false) { $msgid .= $this->_clean_import($line); }
					else { $cur_entry['T'] .= $this->_clean_import($line); }
					continue;
				}

				if (preg_match($this->reg_msgctxt, $line, $hits)) { $cur_entry['X'] = $this->_clean_import($hits[1]); };
				if (preg_match($this->reg_textdomain, $line, $hits)) { $cur_entry['LTD'][] = $hits[1]; }
				elseif (preg_match($this->reg_comment, $line, $hits)) { $cur_entry['CT'][] = $this->_clean_import($hits[1]); }
				if (preg_match($this->reg_comment_ex, $line, $hits)) { $cur_entry['CC'][] = $this->_clean_import($hits[1]); }
				if (preg_match($this->reg_reference, $line, $hits)) { $cur_entry['R'][] = $hits[1]; }
				if (preg_match($this->reg_flags, $line, $hits)) { $cur_entry['F'][] = $hits[1]; }
				if (preg_match($this->reg_msgid, $line, $hits)) { $msgid = $this->_clean_import($hits[1]); }
				if (preg_match($this->reg_msgstr, $line, $hits)) { $cur_entry['T'] = $this->_clean_import($hits[1]); }
				if (preg_match($this->reg_msgid_plural, $line, $hits)) { $msgid .= "\0"; $msgid .= $this->_clean_import($hits[1]); }
				if (preg_match($this->reg_msgstr_plural, $line, $hits)) { 
					if ($cur_entry['T'] === false) $cur_entry['T'] = $this->_clean_import($hits[1]);
					else {
						$cur_entry['T'] .= (preg_match("/[^\\0*]*/", $cur_entry['T']) ? "\0" : '');
						$cur_entry['T'] .= $this->_clean_import($hits[1]); 
					}
				}			
				
				
			}
			fclose ($handle);		
			
			//BUGFIX: language not possible if it's a template file
			$po_lang = 'en';
			if (preg_match("/([a-z][a-z]_[A-Z][A-Z]).(mo|po)$/", $pofile, $hits)) {
				$po_lang = $this->strings->_substr($hits[1],0,2);
			}else{
				$po_lang = $this->strings->_substr($_POST['language'],0,2);
			}
			$this->_set_header_from_string($this->map[""]['T'], $po_lang);
			$this->_set_header_from_string('Plural-Forms: ', $po_lang); //for safetly the plural forms!
			if ($base_file) {
				$rel = $this->_build_rel_path($base_file);
				$this->_set_header_from_string("X-Poedit-Basepath: $rel\nX-Poedit-SearchPath-0: .", $po_lang);
			}
			return true;
		}
		return false;
	}
	
	//extension made to stamp pot files to textdomain entirely
	function write_pofile($pofile, $last = false, $textdomain = false, $tds = 'yes') {
		if (file_exists($pofile) && !is_writable($pofile)) return false;
		$handle = @fopen($pofile, "wb");
		if ($handle === false) return false;
		//set the plurals and multi textdomain support
		//update the revision date
		$stamp = date("Y-m-d H:i:sO");
		//BUGFIX: language not possible if it's a template file
		$po_lang = 'en';
		if (preg_match("/([a-z][a-z]_[A-Z][A-Z]).(mo|po)$/", $pofile, $hits)) {
			$po_lang = $this->strings->_substr($hits[1],0,2);
		}else{
			$po_lang = $this->strings->_substr($_POST['language'],0,2);
		}
		$this->_set_header_from_string("PO-Revision-Date: $stamp\nPlural-Forms: \nX-Textdomain-Support: $tds\n", $po_lang);

		//write header if last because it has no code ref anyway
		if ($last === true) {
			fwrite($handle, 'msgid ""'."\n");
			fwrite($handle, 'msgstr '.$this->_clean_export($this->map['']['T'])."\n\n");
		}
		
		foreach($this->map as $key => $entry) {
			
			if ((is_array($entry['R']) && (count($entry['R']) > 0)) || ($last === false)) {
						
				if (is_array($entry['CT'])) {
					foreach($entry['CT'] as $comt) {
						fwrite($handle, '#  '.$comt."\n");
					}
				}
				if (is_array($entry['CC'])) {
					foreach($entry['CC'] as $comc) {
						fwrite($handle, '#. '.$comc."\n");
					}
				}
				if (is_array($entry['R'])) {
					foreach($entry['R'] as $ref) {
						fwrite($handle, '#: '.$ref."\n");
					}
				}
				if (is_array($entry['F']) && count($entry['F'])) {
					fwrite($handle, '#, '.implode(', ', $entry['F'])."\n");
				}				
				if (is_array($entry['LTD']) && count($entry['LTD'])) {
					foreach($entry['LTD'] as $domain) {
						if(!empty($domain)) fwrite($handle, '#@ '.$domain."\n");
					}
				}elseif($textdomain) {
					fwrite($handle, '#@ '.$textdomain."\n");
				}
				
				if($entry['P'] !== false) {
					list($msgid, $msgid_plural) = explode("\0", $key);
					if ($entry['X'] !== false) {
						list($ctx, $msgid) = explode("\04", $msgid);
						fwrite($handle, 'msgctxt '.$this->_clean_export($ctx)."\n");
					}
					fwrite($handle, 'msgid '.$this->_clean_export($msgid)."\n");
					fwrite($handle, 'msgid_plural '.$this->_clean_export($msgid_plural)."\n");
					$msgstr_arr = explode("\0", $entry['T']);
					for ($i=0; $i<count($msgstr_arr); $i++) {
						fwrite($handle, 'msgstr['.$i.'] '.$this->_clean_export($msgstr_arr[$i])."\n");
					}
				}
				else{
					$msgid = $key;
					if ($entry['X'] !== false) {
						list($ctx, $msgid) = explode("\04", $key);
						fwrite($handle, 'msgctxt '.$this->_clean_export($ctx)."\n");
					}
					fwrite($handle, 'msgid '.$this->_clean_export($msgid)."\n");
					fwrite($handle, 'msgstr '.$this->_clean_export($entry['T'])."\n");
				}
				fwrite($handle, "\n");
				
			}
		}
		fclose($handle);	
		return true;
	}

	function ftp_get_pofile_content($pofile, $last = false, $textdomain = false, $tds = 'yes') {
		$content = '';
		//set the plurals and multi textdomain support
		//update the revision date
		$stamp = date("Y-m-d H:i:sO");
		$this->_set_header_from_string("PO-Revision-Date: $stamp\nPlural-Forms: \nX-Textdomain-Support: $tds\n");

		//write header if last because it has no code ref anyway
		if ($last === true) {
			$content .= 'msgid ""'."\n";
			$content .= 'msgstr '.$this->_clean_export($this->map['']['T'])."\n\n";
		}
		
		foreach($this->map as $key => $entry) {
			
			if ((is_array($entry['R']) && (count($entry['R']) > 0)) || ($last === false)) {
						
				if (is_array($entry['CT'])) {
					foreach($entry['CT'] as $comt) {
						$content .= '#  '.$comt."\n";
					}
				}
				if (is_array($entry['CC'])) {
					foreach($entry['CC'] as $comc) {
						$content .= '#. '.$comc."\n";
					}
				}
				if (is_array($entry['R'])) {
					foreach($entry['R'] as $ref) {
						$content .=  '#: '.$ref."\n";
					}
				}
				if (is_array($entry['F']) && count($entry['F'])) {
					$content .= '#, '.implode(', ', $entry['F'])."\n";
				}				
				if (is_array($entry['LTD']) && count($entry['LTD'])) {
					foreach($entry['LTD'] as $domain) {
						if(!empty($domain)) $content .= '#@ '.$domain."\n";
					}
				}elseif($textdomain) {
					$content .= '#@ '.$textdomain."\n";
				}
				
				if($entry['P'] !== false) {
					list($msgid, $msgid_plural) = explode("\0", $key);
					if ($entry['X'] !== false) {
						list($ctx, $msgid) = explode("\04", $msgid);
						$content .= 'msgctxt '.$this->_clean_export($ctx)."\n";
					}
					$content .= 'msgid '.$this->_clean_export($msgid)."\n";
					$content .= 'msgid_plural '.$this->_clean_export($msgid_plural)."\n";
					$msgstr_arr = explode("\0", $entry['T']);
					for ($i=0; $i<count($msgstr_arr); $i++) {
						$content .= 'msgstr['.$i.'] '.$this->_clean_export($msgstr_arr[$i])."\n";
					}
				}
				else{
					$msgid = $key;
					if ($entry['X'] !== false) {
						list($ctx, $msgid) = explode("\04", $key);
						$content .= 'msgctxt '.$this->_clean_export($ctx)."\n";
					}
					$content .= 'msgid '.$this->_clean_export($msgid)."\n";
					$content .= 'msgstr '.$this->_clean_export($entry['T'])."\n";
				}
				$content .= "\n";
				
			}
		}
		return $content;
	}
	
	function read_mofile($mofile, $check_plurals, $base_file=false, $default_textdomain='') {

		//mo file reading without need of further WP introduced classes !
		if (file_exists($mofile)) {				
			if (is_readable($mofile)) {						
				$file = fopen( $mofile, 'rb' );
				if ( !$file )
					return false;
					
				$header = fread( $file, 28 );
				if ( $this->strings->_strlen( $header ) != 28 )
					return false;

				// detect endianess
				$endian = unpack( 'Nendian', $this->strings->_substr( $header, 0, 4 ) );
				if ( $endian['endian'] == intval( hexdec( '950412de' ) ) )
					$endian = 'N';
				else if ( $endian['endian'] == intval( hexdec( 'de120495' ) ) )
					$endian = 'V';
				else
					return false;
								
				// parse header
				$header = unpack( "{$endian}Hrevision/{$endian}Hcount/{$endian}HposOriginals/{$endian}HposTranslations/{$endian}HsizeHash/{$endian}HposHash", $this->strings->_substr( $header, 4 ) );
				if ( !is_array( $header ) )
					return false;

				extract( $header );
				
				// support revision 0 of MO format specs, only
				if ( $Hrevision != 0 )
					return false;		
					
				// read originals' index
				fseek( $file, $HposOriginals, SEEK_SET );

				$originals = fread( $file, $Hcount * 8 );
				if ( $this->strings->_strlen( $originals ) != $Hcount * 8 )
					return false;

				// read translations index
				fseek( $file, $HposTranslations, SEEK_SET );

				$translations = fread( $file, $Hcount * 8 );
				if ( $this->strings->_strlen( $translations ) != $Hcount * 8 )
					return false;

				// transform raw data into set of indices
				$originals    = $this->strings->_str_split( $originals, 8 );
				$translations = $this->strings->_str_split( $translations, 8 );

				// find position of first string in file
				$HposStrings = 0x7FFFFFFF;
				
				for ( $i = 0; $i < $Hcount; $i++ )
				{

					// parse index records on original and related translation
					$o = unpack( "{$endian}length/{$endian}pos", $originals[$i] );
					$t = unpack( "{$endian}length/{$endian}pos", $translations[$i] );

					if ( !$o || !$t )
						return false;

					$originals[$i]    = $o;
					$translations[$i] = $t;

					$HposStrings = min( $HposStrings, $o['pos'], $t['pos'] );

				}

				// read strings expected in rest of file
				fseek( $file, $HposStrings, SEEK_SET );

				$strings = '';
				while ( !feof( $file ) )
					$strings .= fread( $file, 4096 );

				fclose( $file );
				
				//now reading the contents
				$this->map = array();
				for ( $i = 0; $i < $Hcount; $i++ )
				{
					// adjust offset due to reading strings to separate space before
					$originals[$i]['pos']    -= $HposStrings;
					$translations[$i]['pos'] -= $HposStrings;

					// extract original and translations
					$original    = $this->strings->_substr( $strings, $originals[$i]['pos'], $originals[$i]['length'] );
					$translation = $this->strings->_substr( $strings, $translations[$i]['pos'], $translations[$i]['length'] );
					//Bugfix: 1.9.19 - trailing nul were occuring somehow, removed now.
					$translation = trim($translation, "\0");
								
					$this->map[$original] = $this->_new_entry($original, $translation, false, false, false, false, $default_textdomain);
				}

				preg_match("/([a-z][a-z]_[A-Z][A-Z]).(mo|po)$/", $mofile, $hits);
				$po_lang = $this->strings->_substr($hits[1],0,2);
				$this->_set_header_from_string((isset($this->map['']) ? $this->map['']['T'] : ''), $po_lang);
				$this->_set_header_from_string('Plural-Forms: ',$po_lang); //for safetly the plural forms!
				if ($base_file) {
					$rel = $this->_build_rel_path($base_file);
					$this->_set_header_from_string("X-Poedit-Basepath: $rel\nX-Poedit-SearchPath-0: .", $po_lang);
				}
				return true;
			}
		}
		return false;
	}
	
	function _is_valid_entry(&$entry, &$textdomain) {
		return (
			($this->strings->_strlen(str_replace("\0", "", $entry['T'])) > 0)
			&&
			in_array($textdomain, $entry['LTD'])
		)
		||
		(stripos($entry['T'], 'Plural-Forms') !== false);
	}
	
	function is_illegal_empty_mofile($textdomain) {
		$entries = 0;
		foreach($this->map as $key => $value) {
			if($this->_is_valid_entry($value, $textdomain)) { $entries++; }
		}
		return ($entries == 0);
	}
	
	function _reduce_header_for_mofile() {
		if (isset($this->map[''])) {
			$header = $this->map[''];
			
			$this->map[''] = $header;
		}
	}
	
	function write_mofile($mofile, $textdomain) {
		//handle WordPress continent cities patch to separate "Center" for UI and Continent/City use
		if (isset($this->map["continents-cities\04Center"])) {
			$trans = $this->map["continents-cities\04Center"];
			unset($this->map["continents-cities\04Center"]);
			$this->map["Center"] = $trans;
		}
		
		//reduce the mofile header
		$mohdr = $this->map['']['T'];
		$mohdr_h = $this->header;
		$this->header = array();
		$this->_set_header_from_string($mohdr, '', true);
		
		$handle = @fopen($mofile, "wb");
		if ($handle === false){
			$this->header = $mohdr_h;
			$this->_set_header_from_string($mohdr, '', false);
			return false;
		}
		ksort($this->map, SORT_REGULAR);
		//let's calculate none empty values	
		$entries = 0;
		foreach($this->map as $key => $value) {
			if($this->_is_valid_entry($value, $textdomain)) { $entries++; }
		}
		$tab_size = $entries * 8;
		//header: little endian magic|revision|entries|offset originals|offset translations|hashing table size|hashing table ofs
		$header = pack('NVVVVVV@'.(28+$tab_size*2),0xDE120495,0x00000000,$entries,28,28+$tab_size,0x00000000,28+$tab_size*2);
		$org_table = '';
		$trans_table = '';
		fwrite($handle, $header);
		foreach($this->map as $key => $value) {
			if ($this->_is_valid_entry($value, $textdomain)) {
				$l=$this->strings->_strlen($key);
				$org_table .= pack('VV', $l, ftell($handle)); 
				$res = pack('A'.$l.'x',$key);
				fwrite($handle, $res);
			}
		}
		foreach($this->map as $key => $value) {
			if ($this->_is_valid_entry($value, $textdomain)) {
				$l=$this->strings->_strlen($value['T']);
				$trans_table .= pack('VV', $l, ftell($handle)); 
				$res = pack('A'.$l.'x',$value['T']);
				fwrite($handle, $res);
			}
		}
		fseek($handle, 28, SEEK_SET);
		fwrite($handle,$org_table);
		fwrite($handle,$trans_table);
		fclose($handle);	
		$this->header = $mohdr_h;
		$this->_set_header_from_string($mohdr, '', false);
		return true;
	}
	
	function ftp_get_mofile_content($mofile, $textdomain) {
		//handle WordPress continent cities patch to separate "Center" for UI and Continent/City use
		if (isset($this->map["continents-cities\04Center"])) {
			$trans = $this->map["continents-cities\04Center"];
			unset($this->map["continents-cities\04Center"]);
			$this->map["Center"] = $trans;
		}

		//reduce the mofile header
		$mohdr = $this->map['']['T'];
		$mohdr_h = $this->header;
		$this->header = array();
		$this->_set_header_from_string($mohdr, '', true);
		
		$content = '';
		
		ksort($this->map, SORT_REGULAR);
		//let's calculate none empty values	
		$entries = 0;
		foreach($this->map as $key => $value) {
			if($this->_is_valid_entry($value, $textdomain)) { $entries++; }
		}
		$tab_size = $entries * 8;
		//header: little endian magic|revision|entries|offset originals|offset translations|hashing table size|hashing table ofs
		$header = pack('NVVVVVV@'.(28+$tab_size*2),0xDE120495,0x00000000,$entries,28,28+$tab_size,0x00000000,28+$tab_size*2);
		$org_table = '';
		$trans_table = '';
		$content .= $header;
		foreach($this->map as $key => $value) {
			if ($this->_is_valid_entry($value, $textdomain)) {
				$l=$this->strings->_strlen($key);
				$org_table .= pack('VV', $l, strlen($content)); 
				$res = pack('A'.$l.'x',$key);
				$content .= $res;
			}
		}
		foreach($this->map as $key => $value) {
			if ($this->_is_valid_entry($value, $textdomain)) {
				$l=$this->strings->_strlen($value['T']);
				$trans_table .= pack('VV', $l, strlen($content)); 
				$res = pack('A'.$l.'x',$value['T']);
				$content .= $res;
			}
		}
		$content = substr_replace($content, $org_table, 28, strlen($org_table));
		$content = substr_replace($content, $trans_table, 28 + strlen($org_table), strlen($trans_table));
		$this->header = $mohdr_h;
		$this->_set_header_from_string($mohdr, '', false);
		return $content;
	}
	
	function _reset_data(&$val, $key) {
		unset($val['R']); $val['R'] = array();
		unset($val['CC']); $val['CC'] = array();
		unset($val['LTD']); $val['LTD'] = array();
	}

	function parsing_init() {	
		//reset the references and textdomains
		$func = array(&$this, '_reset_data');
		array_walk($this->map, $func);
		$this->_set_header_from_string("X-Textdomain-Support: yes\n");	
	}
	
	function add_messages($r = false) {
		if ($r === false) return; //file doesn't exist
		$gettext = $r['gettext'];
		$not_gettext = $r['not_gettext'];
		if (count($gettext)) {
			foreach($gettext as $match) {
				$entry = null;
								
				if (isset($this->map[$match['msgid']]))
					$entry = $this->map[$match['msgid']];
				if (!is_array($entry)) {
					$entry = $this->_new_entry(
						$match['msgid'], 
						str_pad('', (isset($match['P']) ? $this->nplurals -1 : 0), "\0"),
						$match['R'],
						false, false,false,
						$match['LTD']
					);
				}
				else{
					if (!in_array($match['R'], $entry['R']))
						$entry['R'][] = $match['R'];
				}
				if (!in_array($match['LTD'], $entry['LTD'])) {
					$entry['LTD'][] = $match['LTD'];
				}
				foreach($match['CC'] as $cc) {
					if (!in_array($cc, $entry['CC'])) {
						$entry['CC'][] = $cc;
					}
				}
				
				if(preg_match("/(%[A-Za-z0-9])/", $match['msgid']) > 0) {
					if (!is_array($entry['F'])||(!in_array('php-format', $entry['F']))) {
						$entry['F'][] = 'php-format';
					}
				}			
				$this->map[$match['msgid']] = $entry;				
			}
		}
		if (count($not_gettext)) {
			foreach($not_gettext as $match) {
				$entry = null;
				if (isset($this->map[$match['msgid']]))
					$entry = $this->map[$match['msgid']];
				if (!is_array($entry)) {
					$entry = $this->_new_entry(
						$match['msgid'], 
						'',
						$match['R'],
						false,
						false,
						$match['CC'],
						$match['LTD']
					);
				}
				else{
					if (!in_array($match['R'], $entry['R'])) {
						$entry['R'][] = $match['R'];
					}
					foreach($match['CC'] as $cc) {
						if (!in_array($cc, $entry['CC'])) {
							$entry['CC'][] = $cc;
						}
					}
				}
				if (!in_array($match['LTD'], $entry['LTD'])) {
					$entry['LTD'][] = $match['LTD'];
				}
				$this->map[$match['msgid']] = $entry;				
			}
		}		
	}

	function parsing_add_messages($path, $sourcefile, $textdomain='') {	
		require_once('class.parser.php');
		$parser = new csp_l10n_parser($path, $textdomain, true, false);
		$r = $parser->parseFile($sourcefile, $this->component_type);
		$this->add_messages($r);
	}
	
	function repair_illegal_single_multi_utilization() {
		//find a solution for this crude examples (duplications po edit warnings):
		// ...e('All', 'texdomain');  ...n('All', 'All', 5, 'textdomain'); ...n('All', 'Garbage', 5, 'textdomain');
		//plural string have to get precedence over single string!
		//plural string with identical singual strings have to be joined too
		$single_to_remap = array();
		$plural_to_join = array();
		foreach($this->map as $key => $entry) {
			$test = explode("\0", $key, 2);
			if (is_array($test) && count($test) == 2) {
				//check if we have that singular as single and remap it
				if(isset($this->map[$test[0]])) {
					$single_to_remap[$test[0]] = $key;
				}				
				//collect potential duplicated plurals
				if (!isset($plural_to_join[$test[0]])) $plural_to_join[$test[0]] = array();
				$plural_to_join[$test[0]][] = $key;
			}	
		}
		foreach($single_to_remap as $single => $plural){
			$e_single = $this->map[$single];
			$e_plural = $this->map[$plural];
			//re-base code comments
			foreach($e_single['CC'] as $cc) {
				if (!in_array($cc, $e_plural['CC'])) {
					$this->map[$plural]['CC'][] = $cc;
				}
			}
			//re-base lines found
			foreach($e_single['R'] as $r) {
				if (!in_array($r, $e_plural['R'])) {
					$this->map[$plural]['R'][] = $r;
				}
			}
			//re-base textdomains
			foreach($e_single['LTD'] as $ltd) {
				if (!in_array($ltd, $e_plural['LTD'])) {
					$this->map[$plural]['LTD'][] = $ltd;
				}
			}
			//additional place a warning at code comment
			$this->map[$plural]['CC'][] = 'gettext fix: identical singular and plural forms found, that may be ambiguous! Please check the code!';
			
			//remove that single now
			unset($this->map[$single]);
		}
		foreach($plural_to_join as $key => $plurals) {
			if (count($plurals) < 2) continue;
			$target = array_shift($plurals);
			foreach($plurals as $plural) {
				$e_single = $this->map[$plural];
				$e_plural = $this->map[$target];
				//re-base code comments
				foreach($e_single['CC'] as $cc) {
					if (!in_array($cc, $e_plural['CC'])) {
						$this->map[$target]['CC'][] = $cc;
					}
				}
				//re-base lines found
				foreach($e_single['R'] as $r) {
					if (!in_array($r, $e_plural['R'])) {
						$this->map[$target]['R'][] = $r;
					}
				}
				//re-base textdomains
				foreach($e_single['LTD'] as $ltd) {
					if (!in_array($ltd, $e_plural['LTD'])) {
						$this->map[$target]['LTD'][] = $ltd;
					}
				}
				//additional place a warning at code comment
				$this->map[$target]['CC'][] = 'gettext fix: duplicate plural forms found, that may be ambiguous! Please check the code!';
			}	
			//remove the duplicated plural form now
			unset($this->map[$plural]);
		}
	}
	
	function parsing_finalize($textdomain, $prjidver) {
		//if there is only one textdomain included and this is '' (empty string) replace all with the given textdomain
		$ltd = array();
		foreach($this->map as $key => $entry) {
			if (is_array($entry['R']) && (count($entry['R']) > 0)) {
				if (count($entry['LTD']) == 0) {
					$this->map[$key]['LTD'] = array($textdomain);
					$entry['LTD'] = array($textdomain);
				}
				foreach($entry['LTD'] as $domain) {
					if (!in_array($domain, $ltd)) $ltd[] = $domain;
				}
			}
		}		
		if ((count($ltd) == 1) && ($ltd[0] == '')) {
			$keys = array_keys($this->map);
			foreach($keys as $key) {
				$this->map[$key]['LTD'] = array($textdomain);
			}
		}
		$this->repair_illegal_single_multi_utilization();
		$this->_set_header_from_string("Project-Id-Version: $prjidver");
	}
	
	function _convert_for_js($str) {
		$search = array( '"\"', "\\", "\n", "\r", "\t", "\"");
		$replace = array( '"\\\\"', '\\\\', '\\\\n', '\\\\r', '\\\\t', '\\\\\"');
		$str = str_replace( $search, $replace, $str );

		return $str;
	}
	
	function _convert_js_input($str) {
		$search = array('\\\\\\\"', '\\\\\"','\\\\n', '\\\\t','\\0', "\\'", '\\\\');
		$replace = array('\"', '"', "\n", "\\t", "\0", "'", "\\");
		$str = str_replace( $search, $replace, $str );
		return $str;
	}
	
	function echo_as_json($path, $file, $sys_locales, $api_type) {
		$loc = $this->strings->_substr($file,strlen($file)-8,-3);
		header('Content-Type: application/json; charset=utf-8');
?>
{
	header : "<table id=\"po-hdr\" style=\"display:none;\"><?php
		foreach($this->header as $key => $value) {
			echo "<tr><td class=\\\"po-hdr-key\\\">".$key."</td><td class=\\\"po-hdr-val\\\">".htmlspecialchars($value)."</td></tr>";
		}?>",
	destlang: "<?php echo ( isset($sys_locales[$loc]) && !empty($api_type) && $api_type != 'none' ? $sys_locales[$loc][$api_type.'-api'] : ''); ?>",
	api_type: "<?php echo $api_type; ?>",
	last_saved : "<?php $mo = $this->strings->_substr($path.$file,0,-2)."mo"; if (file_exists($mo)) { echo date (__('m/d/Y H:i:s',CSP_PO_TEXTDOMAIN), filemtime($mo)); } else { _e('unknown',CSP_PO_TEXTDOMAIN); } ?>",
	plurals_num : <?php echo $this->nplurals; ?>,
	plurals_func : "<?php echo $this->plural_func; ?>",
	path : "<?php echo $path; ?>",
	file : "<?php echo $file; ?>",
	index : {
		'total' : [],
		'plurals' : [],
		'open' : [],
		'rem' : [],
		'code' : [],
		'ctx' : [],
		'cur' : [],
		'ltd' : [],
		'trail' : []
	},
	content : [
<?php
		$num = count($this->map);
		$c = 0;
		$ltd = array();
		foreach($this->map as $key => $entry) {
			$c++;
			if (!strlen($key)) { continue; }
			
			if ($this->strings->_strpos($key, "\04") > 0) {
				list($ctx, $key) = explode("\04", $key);
				echo "{ \"ctx\" : \"".$this->_convert_for_js($ctx)."\",";
			}else {
				echo "{ ";
			}
			
			if (is_array($entry['LTD']) && count($entry['LTD'])) { echo " \"ltd\" : [\"".implode("\",\"",$entry['LTD'])."\"],"; }
			else { echo " \"ltd\" : [\"\"],"; }
			
			if ($entry['P'] !== false) { 
				$parts = explode("\0", $key);
				for($i=0; $i<count($parts); $i++) {
					$parts[$i] = $this->_convert_for_js($parts[$i]);
				}
				echo " \"key\" : [\"".implode("\",\"",$parts)."\"],"; 
			} else{ echo " \"key\" : \"".$this->_convert_for_js($key)."\","; }
			
			if ($this->strings->_strpos($entry['T'], "\0") !== false) {
				$parts = explode("\0", $entry['T']);
				for($i=0; $i<count($parts); $i++) {
					$parts[$i] = $this->_convert_for_js($parts[$i]);
				}			
				//BUGFIX: extend template plurals
				if(count($parts) < $this->nplurals) {
					for($i=count($parts); $i<$this->nplurals; $i++) {
						$parts[] = '';
					}
				}
				echo " \"val\" : [\"".implode("\",\"",$parts)."\"]"; 
			} else { echo " \"val\" : \"".$this->_convert_for_js($entry['T'])."\""; }
			
			if (is_array($entry['CT']) && count($entry['CT'])) { echo ", \"rem\" : \"".implode('\n',$this->_convert_for_js($entry['CT']))."\""; }
			else { echo  ", \"rem\" : \"\""; }

			if (is_array($entry['CC']) && count($entry['CC'])) {
				echo  ", \"code\" : \"".implode('\n',$this->_convert_for_js($entry['CC']))."\""; 
			}
			
			if (is_array($entry['R']) && count($entry['R'])) { echo ", \"ref\" : [\"".implode("\",\"",$entry['R'])."\"]"; }
			else { echo ", \"ref\" : []"; }
			
			echo "}".($c != $num ? ',' : '')."\n";
	
			foreach($entry['LTD'] as $d) {	
				if (!in_array($d, $ltd)) $ltd[] = esc_js($d);
			}
		}
?>	
	],
	textdomains : ["<?php sort($ltd); echo implode('","', array_reverse($ltd)); ?>"]
}
<?php		
	}

	function update_entry($msgid, $msgstr) {	
		$msgid = $this->_convert_js_input($msgid);
		if (array_key_exists($msgid, $this->map)) {
			$this->map[$msgid]['T'] = $this->_convert_js_input($msgstr);
			return true;
		}
		//the \t issue must be handled carefully
		$msgid = str_replace('\\t', "\t", $msgid);
		$msgstr = str_replace('\\t', "\t", $msgstr);
		if (array_key_exists($msgid, $this->map)) {
			$this->map[$msgid]['T'] = $this->_convert_js_input($msgstr);
			return true;
		}		
		return false;
	}
}

?>