<?php

// Language class, used for localization
// Copyright 2004 Alex King, used with permission

class language {
	var $author;		// name of the translator
	var $author_url;	// URL of the translator
	var $charset;		// defaults to ISO-8859-I for english
	var $name;			// name of language
	var $strings;		// the strings that are translated
	
// initialize
	function language($author = ''
	                 ,$author_url = ''
	                 ,$charset = 'ISO-8859-I'
	                 ,$name = ''
	                 ,$strings = array()
	                 ) {
		$this->author = $author;
		$this->author_url = $author_url;
		$this->charset = $charset;
		$this->name = $name;
		$this->strings = $strings;
	}

	function str($key, $vars = '') {
		if (!isset($this->strings[$key])) { // not using array_key_exists() because it is slower
			return false;
		}
		if (empty($vars)) {
			print($this->strings[$key]);
			return true;
		}
		else {
			if (strstr($vars, ',')) {
				$vars = explode(',', $vars);
			}
			else {
				$vars = array($vars);
			}
			$string = $this->strings[$key];
			for ($i = 0; $i < count($vars); $i++) {
				$string = @str_replace("__".$i, $vars[$i], $string);
				if (!$string) {
					print('<p><strong>Error</strong>, could not replace __'.$i
						 .' with '.$vars[$i].' in string '.$key.'.</p>'
						 );
				}
			}
			print($string);
			return true;
		}
	}
}

?>