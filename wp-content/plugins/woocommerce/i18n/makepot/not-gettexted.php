<?php
/**
 * Console application, which extracts or replaces strings for
 * translation, which cannot be gettexted
 *
 * @version $Id: not-gettexted.php 19275 2012-02-10 17:47:42Z nacin $
 * @package wordpress-i18n
 * @subpackage tools
 */

// see: http://php.net/tokenizer
if ( ! defined('T_ML_COMMENT') )
	define('T_ML_COMMENT', T_COMMENT);
else
	define('T_DOC_COMMENT', T_ML_COMMENT);

require_once dirname( __FILE__ ) . '/pomo/po.php';
require_once dirname( __FILE__ ) . '/pomo/mo.php';

class NotGettexted {
	var $enable_logging = false;

	var $STAGE_OUTSIDE = 0;
	var $STAGE_START_COMMENT = 1;
	var $STAGE_WHITESPACE_BEFORE = 2;
	var $STAGE_STRING = 3;
	var $STAGE_WHITESPACE_AFTER = 4;
	var $STAGE_END_COMMENT = 4;

	var $commands = array('extract' => 'command_extract', 'replace' => 'command_replace' );


	function logmsg() {
		$args = func_get_args();
		if ($this->enable_logging) error_log(implode(' ', $args));
	}

	function stderr($msg, $nl=true) {
		fwrite(STDERR, $msg.($nl? "\n" : ""));
	}

	function cli_die($msg) {
		$this->stderr($msg);
		exit(1);
	}

	function unchanged_token($token, $s='') {
		return is_array($token)? $token[1] : $token;
	}

	function ignore_token($token, $s='') {
		return '';
	}

	function list_php_files($dir) {
		$files = array();
		$items = scandir( $dir );
		foreach ( (array) $items as $item ) {
			$full_item = $dir . '/' . $item;
			if ('.' == $item || '..' == $item)
				continue;
			if ('.php' == substr($item, -4))
				$files[] = $full_item;
			if (is_dir($full_item))
				$files += array_merge($files, NotGettexted::list_php_files($full_item, $files));
		}
		return $files;
	}


	function make_string_aggregator($global_array_name, $filename) {
		$a = $global_array_name;
		return create_function('$string, $comment_id, $line_number', 'global $'.$a.'; $'.$a.'[] = array($string, $comment_id, '.var_export($filename, true).', $line_number);');
	}

	function make_mo_replacer($global_mo_name) {
		$m = $global_mo_name;
		return create_function('$token, $string', 'global $'.$m.'; return var_export($'.$m.'->translate($string), true);');
	}

	function walk_tokens(&$tokens, $string_action, $other_action, $register_action=null) {

		$current_comment_id = '';
		$current_string = '';
		$current_string_line = 0;

		$result = '';
		$line = 1;

		foreach($tokens as $token) {
			if (is_array($token)) {
				list($id, $text) = $token;
				$line += substr_count($text, "\n");
				if ((T_ML_COMMENT == $id || T_COMMENT == $id) && preg_match('|/\*\s*(/?WP_I18N_[a-z_]+)\s*\*/|i', $text, $matches)) {
					if ($this->STAGE_OUTSIDE == $stage) {
						$stage = $this->STAGE_START_COMMENT;
						$current_comment_id = $matches[1];
						$this->logmsg('start comment', $current_comment_id);
						$result .= call_user_func($other_action, $token);
						continue;
					}
					if ($this->STAGE_START_COMMENT <= $stage && $stage <= $this->STAGE_WHITESPACE_AFTER && '/'.$current_comment_id == $matches[1]) {
						$stage = $this->STAGE_END_COMMENT;
						$this->logmsg('end comment', $current_comment_id);
						$result .= call_user_func($other_action, $token);
						if (!is_null($register_action)) call_user_func($register_action, $current_string, $current_comment_id, $current_string_line);
						continue;
					}
				} else if (T_CONSTANT_ENCAPSED_STRING == $id) {
					if ($this->STAGE_START_COMMENT <= $stage && $stage < $this->STAGE_WHITESPACE_AFTER) {
						eval('$current_string='.$text.';');
						$this->logmsg('string', $current_string);
						$current_string_line = $line;
						$result .= call_user_func($string_action, $token, $current_string);
						continue;
					}
				} else if (T_WHITESPACE == $id) {
					if ($this->STAGE_START_COMMENT <= $stage && $stage < $this->STAGE_STRING) {
						$stage = $this->STAGE_WHITESPACE_BEFORE;
						$this->logmsg('whitespace before');
						$result .= call_user_func($other_action, $token);
						continue;
					}
					if ($this->STAGE_STRING < $stage && $stage < $this->STAGE_END_COMMENT) {
						$stage = $this->STAGE_WHITESPACE_AFTER;
						$this->logmsg('whitespace after');
						$result .= call_user_func($other_action, $token);
						continue;
					}
				}
			}
			$result .= call_user_func($other_action, $token);
			$stage = $this->STAGE_OUTSIDE;
			$current_comment_id = '';
			$current_string = '';
			$current_string_line = 0;
		}
		return $result;
	}


	function command_extract() {
		$args = func_get_args();
		$pot_filename = $args[0];
		if (isset($args[1]) && is_array($args[1]))
			$filenames = $args[1];
		else
			$filenames = array_slice($args, 1);

		$global_name = '__entries_'.mt_rand(1, 1000);
		$GLOBALS[$global_name] = array();

		foreach($filenames as $filename) {
			$tokens = token_get_all(file_get_contents($filename));
			$aggregator = $this->make_string_aggregator($global_name, $filename);
			$this->walk_tokens($tokens, array(&$this, 'ignore_token'), array(&$this, 'ignore_token'), $aggregator);
		}

		$potf = '-' == $pot_filename? STDOUT : @fopen($pot_filename, 'a');
		if (false === $potf) {
			$this->cli_die("Couldn't open pot file: $pot_filename");
		}

		foreach($GLOBALS[$global_name] as $item) {
			@list($string, $comment_id, $filename, $line_number) = $item;
			$filename = isset($filename)? preg_replace('|^\./|', '', $filename) : '';
			$ref_line_number = isset($line_number)? ":$line_number" : '';
			$args = array(
				'singular' => $string,
				'extracted_comments' => "Not gettexted string $comment_id",
				'references' => array("$filename$ref_line_number"),
			);
			$entry = new Translation_Entry($args);
			fwrite($potf, "\n".PO::export_entry($entry)."\n");
		}
		if ('-' != $pot_filename) fclose($potf);
		return true;
	}

	function command_replace() {
		$args = func_get_args();
		$mo_filename = $args[0];
		if (isset($args[1]) && is_array($args[1]))
			$filenames = $args[1];
		else
			$filenames = array_slice($args, 1);

		$global_name = '__mo_'.mt_rand(1, 1000);
		$GLOBALS[$global_name] = new MO();
		$replacer = $this->make_mo_replacer($global_name);

		$res = $GLOBALS[$global_name]->import_from_file($mo_filename);
		if (false === $res) {
			$this->cli_die("Couldn't read MO file '$mo_filename'!");
		}
		foreach($filenames as $filename) {
			$source = file_get_contents($filename);
			if ( strlen($source) > 150000 ) continue;
			$tokens = token_get_all($source);
			$new_file = $this->walk_tokens($tokens, $replacer, array(&$this, 'unchanged_token'));
			$f = fopen($filename, 'w');
			fwrite($f, $new_file);
			fclose($f);
		}
		return true;
	}

	function usage() {
		$this->stderr('php i18n-comments.php COMMAND OUTPUTFILE INPUTFILES');
		$this->stderr('Extracts and replaces strings, which cannot be gettexted');
		$this->stderr('Commands:');
		$this->stderr('	extract POTFILE PHPFILES appends the strings to POTFILE');
		$this->stderr('	replace MOFILE PHPFILES replaces strings in PHPFILES with translations from MOFILE');
	}

	function cli() {
		global $argv, $commands;
		if (count($argv) < 4 || !in_array($argv[1], array_keys($this->commands))) {
			$this->usage();
			exit(1);
		}
		call_user_func_array(array(&$this, $this->commands[$argv[1]]), array_slice($argv, 2));
	}
}

// run the CLI only if the file
// wasn't included
$included_files = get_included_files();
if ( $included_files[0] == __FILE__ ) {

	/**
	 * Note: this file is locked by default since it should not be publicly accessible
	 * on a live website. You can unlock it by temporarily removing the following line.
	 */
	exit( 'Locked' );

	error_reporting(E_ALL);
	$not_gettexted = new NotGettexted;
	$not_gettexted->cli();
}