<?php

if (!defined('T_ML_COMMENT'))
	    define('T_ML_COMMENT', T_COMMENT);
else
	    define('T_DOC_COMMENT', T_ML_COMMENT);

class csp_l10n_parser {
	
	function csp_l10n_parser($basedir, $textdomain, $do_gettext = true, $do_domains=false) {
		$domains = array(
			'load_textdomain',
			'load_theme_textdomain',
			'load_plugin_textdomain',
			'load_muplugin_textdomain', 	//3.0
			'load_child_theme_textdomain',	//2.9
			'define'
		);
		$gettext = array(
			'__',
			'_e',
			'_c', //context by |
			'_nc', //context by |
			'__ngettext', '_n',
			'__ngettext_noop', '_n_noop',
			'_x', 		//see "_c" but explicite context
			'_ex', 		//see "_c" but explicite context
			'_nx', 		//see "_n" but  but additional context,
			'_nx_noop'	//see "_n_noop" but  but additional context,
		);

		$escapements = array(
			'esc_attr__',
			'esc_html__',
			'esc_attr_e',
			'esc_html_e',
			'esc_attr_x',
			'esc_html_x'
		); //needed only for checks against developer own functions for gettext like Ozz is using
		
		$this->component_type = 'unknown';
		$this->textdomain = $textdomain;
		$this->basedir = $basedir;
		$this->filename = '';
		$this->l10n_functions = array();
		$this->buildin_functions = array_merge($gettext, $escapements);
		
		if ($do_gettext) $this->l10n_functions = array_merge($this->l10n_functions, $gettext);
		if ($do_domains) $this->l10n_functions = array_merge($this->l10n_functions, $domains);
		
		$this->l10n_regular = '/('.implode('$|', $this->l10n_functions).'$)/';
		$this->l10n_domains = '/('.implode('|',$domains).')/';
		
		$this->regexp_wp_msfiles = "/(ms-.*|.*\/ms-.*|.*\/my-.*|wp-activate\.php|wp-signup\.php|wp-admin\/network\.php|wp-admin\/includes\/ms\.php|wp-admin\/network\/.*\.php|wp-admin\/includes\/class-wp-ms.*)/";
		
		$this->is_new_kernel_translation = @file_exists(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-admin/user/about.php');
	}
	
	function parseFile($filename, $component_type) {
		if (file_exists($filename)){
			$this->filename = str_replace($this->basedir, '', $filename);
			$content = file_get_contents($filename);
			return $this->parseString($content, $component_type);
		}
		return false;
	}
	
	function parseString($content, $component_type) {
		$results = array(
			'gettext' 	  => array(),
			'not_gettext' => array(),
			'textdomains' => array()
		);
		$this->component_type = $component_type;
		
		$in_func = false;
		$in_domain = false;
		$in_not_gettext = false;
		$args_started = false;
		$parens_balance = 0;
		
		$tokens = token_get_all($content);
	
		$cur_not_gettext = false;
		$cur_func = false;
		$cur_full_func = false;
		$cur_translator_hint = false;
		$line_number = 1;
		$cur_match_line = 1;
		$cur_argc = 0;
		$cur_args = array();
		$bad_argc = array();
		
		foreach($tokens as $token) {
			if (is_array($token)) {
				list($id, $text) = $token;				
				if (T_STRING == $id && preg_match($this->l10n_regular, $text, $m)) {
					$in_func = true;
					$in_domain = preg_match($this->l10n_domains, $text);
					$parens_balance = 0;
					$args_started = false;
					$cur_func = $m[1];
					$cur_full_func = $text;
					$token = $text;
				} elseif (T_STRING == $id && $in_func) {
					if($in_domain) {
						if(isset($cur_args[$cur_argc])){
							$cur_args[$cur_argc] .= '['.$text.']';	
						}else{
							$cur_args[$cur_argc] = '['.$text.']';	
						}
						$token = $text;
					}else{
						$bad_argc[] = $cur_argc;  //avoid stacked functions inside parts of required params!
						$token = $text;
					}
				} elseif (T_CONSTANT_ENCAPSED_STRING == $id) {
					if ($in_func && $args_started) {
						if ($text{0} == '"') {
							$text = substr($text, 1, strlen($text)-2);
							$text = str_replace('\"', '"', $text);
							$text = str_replace("\\$", "$", $text);
							$text = str_replace("\r\n", "\n", $text);
							$token = $text;
							$text = str_replace("\\n", "\n", $text);
						}
						else{
							$text = substr($text, 1, strlen($text)-2);
							$text = str_replace("\\'", "'", $text);
							$text = str_replace("\\$", "$", $text);
							$text = str_replace("\r\n", "\n", $text);
							$text = str_replace("\\n", "\n", $text);
							$text = str_replace("\\\\", "\\", $text);
							$token = $text;
						}
						if(isset($cur_args[$cur_argc])){
							$cur_args[$cur_argc] .= $text;	
						}else{
							$cur_args[$cur_argc] = $text;	
						}
						
						if ($cur_argc == 0) $cur_match_line = $line_number;
					}elseif($in_not_gettext) {
						if ($text{0} == '"') {
							$text = trim($text, '"');
							$text = str_replace('\"', '"', $text);
						}
						else{
							$text = trim($text, "'");
							$text = str_replace("\\'", "'", $text);
						}
						$text = str_replace("\\$", "$", $text);
						$text = str_replace("\r\n", "\n", $text);						
						$results['not_gettext'][] = $this->_build_non_gettext($line_number, $cur_not_gettext, $text);
						$cur_not_gettext = false;
						$token = $text;
					}
					else {
						$token = $text;
					}
				} elseif ((T_ML_COMMENT == $id || T_COMMENT == $id) && preg_match('|/\*\s*(/?WP_I18N_[a-z_]+)\s*\*/|i', $text, $matches)) {
					$in_not_gettext = $matches[1]{0} == 'W';
					if ($in_not_gettext) $cur_not_gettext = 'Not gettexted string '.$matches[1];
					$token = $text;
				} elseif ((T_ML_COMMENT == $id || T_COMMENT == $id) && preg_match('/\*\s(translators:.*)\*/i', $text, $matches)) {
					$cur_translator_hint = $matches[1];
					$token = $text;
				} elseif (T_ML_COMMENT == $id || T_COMMENT == $id || T_DOC_COMMENT == $id) {
					$header = $this->_detect_plugin_header($text);
					if (count($header) > 0) {
						foreach($header as $gt){
							$results['gettext'][] = $gt;
						}
					}
					$token = $text;
				} elseif((T_VARIABLE == $id)||(T_OBJECT_OPERATOR == $id)||(T_STRING == $id)) {
					if ($in_func && $in_domain && $args_started) {
						if(isset($cur_args[$cur_argc])){
							$cur_args[$cur_argc] .= $text;						
						}else{
							$cur_args[$cur_argc] = $text;
						}
					}
					$token = $text;
				}
				else {
					$token = $text;
				}
			} elseif ('(' == $token){
				$args_started = true;
				++$parens_balance;
			} elseif (',' == $token) {
				if ($in_func && $args_started) {
					$cur_argc++;
				}
			} elseif (')' == $token) {
				--$parens_balance;
				if ($in_func && 0 == $parens_balance) {				
					if (count($cur_args) && isset($cur_args[0])) {
						//skip those, where all args are variables
						$is_dev_func = !in_array($cur_full_func, $this->buildin_functions);
						$gt = $this->_build_gettext($cur_match_line, $cur_func, $cur_args, $cur_argc, $is_dev_func, $bad_argc);
						if (is_array($gt)) {
							if ($in_domain) {
								$results['textdomains'][] = $gt;
							}else {
								if ($cur_translator_hint !== false) {
									$gt['CC'][] = $cur_translator_hint;
								}
								$results['gettext'][] = $gt;
							}
						}
					}
					$in_func = false;
					$in_domain = false;
					$args_started = false;
					$cur_func = false;
					$cur_full_func = false;
					$cur_translator_hint = false;
					$cur_argc = 0;
					$cur_args = array();
					$bad_argc = array();
				}
			}else {
				if($in_domain) {
					if(isset($cur_args[$cur_argc])){
//						$cur_args[$cur_argc] .= $token;	
					}else{
//						$cur_args[$cur_argc] = $token;	
					}
					/*
					var_dump($token);
					var_dump($id);
					var_dump($text);
					var_dump(token_name($id));
					var_dump("===========");
					*/
				}
			}			
			$line_number += substr_count($token, "\n");
		}
		return $results;
	}
	
	function _detect_plugin_header($comment) {
		$result = array();
				
		if (($this->component_type != 'plugins') && ($this->component_type != 'plugins_mu'))
			return $result;	

		$default_headers = array(
			'Name' 			=> 'Plugin Name',
			'PluginURI' 	=> 'Plugin URI',
			'Version' 		=> 'Version',
			'Description' 	=> 'Description',
			'Author' 		=> 'Author',
			'AuthorURI' 	=> 'Author URI',
			'TextDomain' 	=> 'Text Domain',
			'DomainPath' 	=> 'Domain Path'
		);
		
		foreach ( $default_headers as $field => $regex ) {
			preg_match( '/' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $comment, ${$field});
			if ( !empty( ${$field} ) )
				${$field} =   trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', ${$field}[1]));
			else
				${$field} = '';
		}
		$data = compact( array_keys( $default_headers ) );
		if (!empty($data['Name']) && !empty($data['TextDomain']) && !empty($data['DomainPath'])) {
			//attach the header strings now
			foreach ( array('Name', 'PluginURI', 'Description', 'Author', 'AuthorURI', 'Version') as $field ) {
				$gt = $this->_build_gettext(
					0, 						//line
					'__', 					//func
					array($data[$field]),	//args
					1,
					true,					//dev func
					array()					//bad argc
				);
				if (is_array($gt)) {
					$gt['CC'][] = 'translators: plugin header field \''.$field.'\'';
					$result[] = $gt;
				}
			}
		}
		return $result;
			
	}
	
	function _ltd_validate($text)
	{
		$r = strip_tags($text);
		if ($r != $text) return "{bug-detected}";
		return $text;
	}
	
	function _build_gettext($line, $func, $args, $argc, $is_dev_func, $bad_argc) {
		$res = array(
			'msgid' => '',
			'R'		=> $this->filename.':'.$line,
			'CC' 	=> array(),
			'LTD'	=> ($is_dev_func ? $this->textdomain : 'default')
		);
		
		//check if we doing wordpress
		if ($this->component_type == 'wordpress') {
		
			//special handling of WordPress new separations starting version 3.4
			if (($res['LTD'] == 'default') && $this->is_new_kernel_translation) {
				//test for admin
				if (preg_match("/wp-admin\/.*/", $this->filename) && !preg_match("/(wp-admin\/includes\/continents-cities\.php|wp-admin\/network\/.*|wp-admin\/network\.php)/", $this->filename)) {
					$res['LTD'] = 'admin';
				}
				elseif (preg_match("/(wp-admin\/network\/.*|wp-admin\/network\.php)/", $this->filename)) {
					$res['LTD'] = 'admin-network';
				}
			}
			else{				
				//check if this is multi-site specific file for lower WordPress versions
				if ($res['LTD'] == 'default') {
					if (preg_match($this->regexp_wp_msfiles, $this->filename)) {
						$res['LTD'] = 'ms';
					}
				}			
			}
		}
		
		switch($func) {
			case '__':
				// see also esc_html__
				//see also esc_attr__
				//[0] =>  phrase
				//[1] => textdomain (optional)
				if (in_array(0, $bad_argc)) return null; //error, this can't be a function
				$res['msgid'] = $args[0];
				if (isset($args[1])) $res['LTD'] = $this->_ltd_validate(trim($args[1]));
				elseif ($argc == 1) $res['LTD'] = $this->textdomain;
			case '_e':
				//see also esc_html_e
				//see also esc_attr_e
				//[0] =>  phrase
				//[1] => textdomain (optional)
				if (in_array(0, $bad_argc)) return null; //error, this can't be a function
				$res['msgid'] = $args[0];
				if (isset($args[1])) $res['LTD'] = $this->_ltd_validate(trim($args[1]));
				elseif ($argc == 1) $res['LTD'] = $this->textdomain;
			case '_c':
				//[0] =>  phrase
				//[1] => textdomain (optional)
				$res['msgid'] = $args[0];
				if (in_array(0, $bad_argc)) return null; //error, this can't be a function
				if (isset($args[1])) $res['LTD'] = $this->_ltd_validate(trim($args[1]));
				elseif ($argc == 1) $res['LTD'] = $this->textdomain;
				break;
			case '_x': 		
				//see "_c" but explicite context
				//see also esc_html_x
				//see also esc_attr_x 
				//[0] =>  phrase
				//[1] =>  context
				//[2] => textdomain (optional)
				if (in_array(0, $bad_argc)) return null; //error, this can't be a function
				if (in_array(1, $bad_argc)) return null; //error, this can't be a function
				$res['msgid'] = $args[1]."\04".$args[0];
				if (isset($args[2])) $res['LTD'] = $this->_ltd_validate(trim($args[2]));
				elseif ($argc == 2) $res['LTD'] = $this->textdomain;
				break;
			case '_ex': 		
				//see "_c" but explicite context
				//[0] =>  phrase
				//[1] =>  context
				//[2] => textdomain (optional)
				if (in_array(0, $bad_argc)) return null; //error, this can't be a function
				if (in_array(1, $bad_argc)) return null; //error, this can't be a function
				$res['msgid'] = $args[1]."\04".$args[0];
				if (isset($args[2])) $res['LTD'] = $this->_ltd_validate(trim($args[2]));
				elseif ($argc == 2) $res['LTD'] = $this->textdomain;
				break;
			case '__ngettext':
				//[0] =>  phrase singular
				//[1] => phrase plural
				//[2] => number
				//[3] => textdomain (optional)
				if (in_array(0, $bad_argc)) return null; //error, this can't be a function
				if (in_array(1, $bad_argc)) return null; //error, this can't be a function
				$res['msgid'] = $args[0]."\00".$args[1];
				$res['P'] = true;
				if (isset($args[3])) $res['LTD'] = $this->_ltd_validate(trim($args[3]));
				elseif ($argc == 3) $res['LTD'] = $this->textdomain;
				break;
			case '_n':
				//[0] => phrase singular
				//[1] => phrase plural
				//[2] => number
				//[3] => textdomain (optional)				
				if (in_array(0, $bad_argc)) return null; //error, this can't be a function
				if (in_array(1, $bad_argc)) return null; //error, this can't be a function
				$res['msgid'] = $args[0]."\00".$args[1];
				$res['P'] = true;
				if (isset($args[3])) $res['LTD'] = $this->_ltd_validate(trim($args[3]));
				elseif ($argc == 3) $res['LTD'] = $this->textdomain;
				break;
			case '_nc':
				//[0] => phrase singular
				//[1] => phrase plural
				//[2] => number
				//[3] => textdomain (optional)				
				if (in_array(0, $bad_argc)) return null; //error, this can't be a function
				if (in_array(1, $bad_argc)) return null; //error, this can't be a function
				$res['msgid'] = $args[0]."\00".$args[1];
				$res['P'] = true;
				if (isset($args[3])) $res['LTD'] = $this->_ltd_validate(trim($args[3]));
				elseif ($argc == 3) $res['LTD'] = $this->textdomain;
				break;
			case '_nx':
				//see "_n" but  but additional context,
				//[0] => phrase singular
				//[1] => phrase plural
				//[2] => number
				//[3] => context
				//[4] => textdomain (optional)
				if (in_array(0, $bad_argc)) return null; //error, this can't be a function
				if (in_array(1, $bad_argc)) return null; //error, this can't be a function
				if (in_array(3, $bad_argc)) return null; //error, this can't be a function
				$res['msgid'] = $args[3]."\04".$args[0]."\00".$args[1];
				$res['P'] = true;
				if (isset($args[4])) $res['LTD'] = html_entity_decode(strip_tags(trim($args[4])));
				elseif ($argc == 4) $res['LTD'] = $this->textdomain;
				break;
			case '__ngettext_noop':
				//[0] =>  phrase singular
				//[1] => phrase plural
				if (in_array(0, $bad_argc)) return null; //error, this can't be a function
				if (in_array(1, $bad_argc)) return null; //error, this can't be a function
				$res['msgid'] = $args[0]."\00".$args[1];
				$res['P'] = true;
				$res['LTD'] = $this->textdomain; //noop's translated later mostly with correct texdomain
				break;
			case '_n_noop':
				//see deprecated __ngettext_noop
				//[0] =>  phrase singular
				//[1] => phrase plural				
				if (in_array(0, $bad_argc)) return null; //error, this can't be a function
				if (in_array(1, $bad_argc)) return null; //error, this can't be a function
				$res['msgid'] = $args[0]."\00".$args[1];
				$res['P'] = true;
				$res['LTD'] = $this->textdomain; //noop's translated later mostly with correct texdomain
				break;
			case '_nx_noop':
				//see "_n_noop" but  but additional context,
				//[0] => phrase singular
				//[1] => phrase plural				
				//[2] => context
				if (in_array(0, $bad_argc)) return null; //error, this can't be a function
				if (in_array(1, $bad_argc)) return null; //error, this can't be a function
				if (in_array(2, $bad_argc)) return null; //error, this can't be a function
				$res['msgid'] = $args[2]."\04".$args[0]."\00".$args[1];
				$res['P'] = true;
				$res['LTD'] = $this->textdomain; //noop's translated later mostly with correct texdomain
				break;				
			case 'load_textdomain':
				$res = array('func' => $func, 'textdomain' => '', 'rel_path' => false, 'path' => false);
				if (isset($args[0])) $res['textdomain'] = $args[0];
				if (isset($args[1])) $res['path'] = $args[1];
				break;
			case 'load_theme_textdomain':
				$res = array('func' => $func, 'textdomain' => '', 'rel_path' => false, 'path' => false);
				if (isset($args[0])) $res['textdomain'] = $args[0];
				if (isset($args[1])) $res['path'] = $args[1];
				break;
			case 'load_plugin_textdomain':
				$res = array('func' => $func, 'textdomain' => '', 'rel_path' => false, 'path' => false);
				if (isset($args[0])) $res['textdomain'] = $args[0];
				if (isset($args[1])) $res['path'] = $args[1];
				if (isset($args[2])) $res['rel_path'] = $args[2];
				break;
			case 'load_muplugin_textdomain':
				$res = array('func' => $func, 'textdomain' => '', 'rel_path' => false, 'path' => false);
				if (isset($args[0])) $res['textdomain'] = $args[0];
				if (isset($args[1])) $res['rel_path'] = $args[1];
				break;
			case 'load_child_theme_textdomain':
				$res = array('func' => $func, 'textdomain' => '', 'rel_path' => false, 'path' => false);
				if (isset($args[0])) $res['textdomain'] = $args[0];
				if (isset($args[1])) $res['path'] = $args[1];
				break;				
			case 'define':
				$res = array('func' => $func);
				if (isset($args[0])) $res['const'] = $args[0];
				if (isset($args[1])) $res['value'] = $args[1];
				break;
			default:
				/*
				var_dump('-----LINE-----');
				var_dump($line);
				var_dump('-----FUNC-----');
				var_dump($func);
				var_dump('-----ARGS-----');
				var_dump($args);
				var_dump('-----ARGC-----');
				var_dump($argc);
				var_dump('-----IS_DEV_FUNC-----');
				var_dump($is_dev_func);
				var_dump('-----BAD_ARGC-----');
				var_dump($bad_argc);
				exit();
				*/
				;
		}
		//permit splitting the "Center" qualified within one file, because WP doesn't provide it. 
		if ($this->component_type == 'wordpress' && preg_match('/continents-cities\.php/', $this->filename) && $res['msgid'] == 'Center') {
			$res['msgid'] = "continents-cities\04Center";
			$res['CC'] = array('translators: this is an artificial split between the admin and continent text, because of different contextual usage.');
		}
		return $res;
	}
	
	function _build_non_gettext($line, $stage, $text) {
		return array( 
			'msgid' => $text,
			'R' 	=> $this->filename.':'.$line, 
			'CC' 	=> array($stage), 
			'LTD'	=> '{php-code}'
		);
	}

}

?>