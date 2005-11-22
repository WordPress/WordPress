<?php

/* Functions missing from older PHP versions */


/* Added in PHP 4.2.0 */

if (!function_exists('floatval')) {
	function floatval($string) {
		return ((float) $string);
	}
}

if (!function_exists('is_a')) {
	function is_a($object, $class) {
		// by Aidan Lister <aidan@php.net>
		if (get_class($object) == strtolower($class)) {
			return true;
		} else {
			return is_subclass_of($object, $class);
		}
	}
}

if (!function_exists('ob_clean')) {
	function ob_clean() {
		// by Aidan Lister <aidan@php.net>
		if (@ob_end_clean()) {
			return ob_start();
		}
		return false;
	}
}


/* Added in PHP 4.3.0 */

function printr($var, $do_not_echo = false) {
	// from php.net/print_r user contributed notes 
	ob_start();
	print_r($var);
	$code =  htmlentities(ob_get_contents());
	ob_clean();
	if (!$do_not_echo) {
	  echo "<pre>$code</pre>";
	}
	return $code;
}

if (!defined('CASE_LOWER')) {
    define('CASE_LOWER', 0);
}

if (!defined('CASE_UPPER')) {
    define('CASE_UPPER', 1);
}


/**
 * Replace array_change_key_case()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.array_change_key_case
 * @author      Stephan Schmidt <schst@php.net>
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision$
 * @since       PHP 4.2.0
 * @require     PHP 4.0.0 (user_error)
 */
if (!function_exists('array_change_key_case')) {
    function array_change_key_case($input, $case = CASE_LOWER)
    {
        if (!is_array($input)) {
            user_error('array_change_key_case(): The argument should be an array',
                E_USER_WARNING);
            return false;
        }

        $output   = array ();
        $keys     = array_keys($input);
        $casefunc = ($case == CASE_LOWER) ? 'strtolower' : 'strtoupper';

        foreach ($keys as $key) {
            $output[$casefunc($key)] = $input[$key];
        }

        return $output;
    }
}

/* Added in PHP 4.3.0 */

if( !(function_exists('glob')) ):
function glob($pattern) {
	// get pathname (everything up until the last / or \)
	$path=$output=null;
//	if(PHP_OS=='WIN32')
//		$slash='\\';
//	else
//		$slash='/';
	$slash = '/';
	$lastpos=strrpos($pattern,$slash);
	if(!($lastpos===false)) {
		$path=substr($pattern,0,$lastpos); #negative length means take from the right
		$pattern=substr($pattern,$lastpos+1);
  	} else {
  		//no dir info, use current dir
		$path=getcwd();
	}
	$handle=@ opendir($path);
	if($handle===false)
		return false;
	while($dir=readdir($handle)) {
		if ( '.' == $dir || '..' == $dir )
			continue;
		if (pattern_match($pattern,$dir))
			$output[]=$path . '/' . $dir;
	}
	closedir($handle);
	print_r($output);
	if(is_array($output))
		return $output;

	return false;
}

function pattern_match($pattern,$string) {
	// basically prepare a regular expression
	$out=null;
	$chunks=explode(';',$pattern);
	foreach($chunks as $pattern) {
		$escape=array('$','^','.','{','}','(',')','[',']','|');
		while(strpos($pattern,'**')!==false)
			$pattern=str_replace('**','*',$pattern);
		foreach($escape as $probe)
			$pattern=str_replace($probe,"\\$probe",$pattern);

		$pattern=str_replace('?*','*',
		str_replace('*?','*',
		str_replace('*',".*",
		str_replace('?','.{1,1}',$pattern))));
		$out[]=$pattern;
	}

	if(count($out)==1)
		return(eregi("^$out[0]$",$string));
	else
		foreach($out as $tester)
			if(eregi("^$tester$",$string))
				return true;
	return false;
}
endif;

?>