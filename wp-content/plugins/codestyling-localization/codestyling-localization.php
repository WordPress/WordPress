<?php
/*
Plugin Name: CodeStyling Localization
Plugin URI: http://www.code-styling.de/english/development/wordpress-plugin-codestyling-localization-en
Description: You can manage and edit all gettext translation files (*.po/*.mo) directly out of your WordPress Admin Center without any need of an external editor. It automatically detects the gettext ready components like <b>WordPress</b> itself or any <b>Plugin</b> / <b>Theme</b> supporting gettext, is able to scan the related source files and can assists you using <b>Google Translate API</b> or <b>Microsoft Translator API</b> during translation.This plugin supports <b>WordPress MU</b> and allows explicit <b>WPMU Plugin</b> translation too. It newly introduces ignore-case and regular expression search during translation. <b>BuddyPress</b> and <b>bbPress</b> as part of BuddyPress can be translated too. Produces transalation files are 100% compatible to <b>PoEdit</b>.
Version: 1.99.30
Author: Heiko Rabe
Author URI: http://www.code-styling.de/english/
Text Domain: codestyling-localization
Domain Path: /languages


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

   Requirements:
 ==============================================================================
 This plugin requires WordPress >= 2.5 and PHP Interpreter >= 4.4.2
 Since version 1.90 the PHP module "Tokenizer" is required.
 
  Version History:
 ==============================================================================
 Since WordPress 2.7 version history will be available by Context Help System newly introduced.
 
 */
 
//////////////////////////////////////////////////////////////////////////////////////////
//	constant definition
//////////////////////////////////////////////////////////////////////////////////////////

//Enable this only for debugging reasons. 
//Attention: the strict logging may prevent WP from proper working because of many not handled issues.
//error_reporting(E_ALL|E_STRICT);
//@unlink(dirname(__FILE__).'/.htaccess');

if (!defined('E_RECOVERABLE_ERROR'))
	define('E_RECOVERABLE_ERROR', 4096);
if (!defined('E_DEPRECATED'))
	define('E_DEPRECATED', 8192);
if (!defined('E_USER_DEPRECATED '))
	define('E_USER_DEPRECATED ', 16384);

function csp_split_url($url) {
  $parsed_url = parse_url($url);
  $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
  $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
  $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
  $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
  $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
  $pass     = ($user || $pass) ? "$pass@" : '';
  $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
  $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
  $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
  return array("$scheme$user$pass$host$port","$path$query$fragment"); 
}

if (!function_exists('get_site_url')) {
	function get_site_url() { return get_option('site_url'); }
}
if (!function_exists('plugins_url')) {
	function plugins_url($plugin) {  
		return WP_PLUGIN_URL . $plugin; 
	}
} 

if (function_exists('add_action')) {
	if ( !defined('WP_CONTENT_URL') )
	    define('WP_CONTENT_URL', get_site_url() . '/wp-content');
	if ( !defined('WP_CONTENT_DIR') )
	    define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
	if ( !defined('WP_PLUGIN_URL') ) 
		define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
	if ( !defined('WP_PLUGIN_DIR') ) 
		define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');
	if ( !defined('PLUGINDIR') )
		define( 'PLUGINDIR', 'wp-content/plugins' ); // Relative to ABSPATH.  For back compat.
		
	if ( !defined('WP_LANG_DIR') )
		define('WP_LANG_DIR', WP_CONTENT_DIR . '/languages');
		
	//WPMU definitions
	if ( !defined('WPMU_PLUGIN_DIR') )
		define( 'WPMU_PLUGIN_DIR', WP_CONTENT_DIR . '/mu-plugins' ); // full path, no trailing slash
	if ( !defined('WPMU_PLUGIN_URL') )
		define( 'WPMU_PLUGIN_URL', WP_CONTENT_URL . '/mu-plugins' ); // full url, no trailing slash
	if( defined( 'MUPLUGINDIR' ) == false ) 
		define( 'MUPLUGINDIR', 'wp-content/mu-plugins' ); // Relative to ABSPATH.  For back compat.

	define("CSP_PO_PLUGINPATH", "/" . dirname(plugin_basename( __FILE__ )));

    define('CSP_PO_TEXTDOMAIN', 'codestyling-localization');
    define('CSP_PO_BASE_URL', plugins_url(CSP_PO_PLUGINPATH));
		
	//Bugfix: ensure valid JSON requests at IDN locations!
	//Attention: Google Chrome and Safari behave in different way (shared WebKit issue or all other are wrong?)!
	list($csp_domain, $csp_target) = csp_split_url( ( function_exists("admin_url") ? rtrim(admin_url(), '/') : rtrim(get_site_url().'/wp-admin/', '/') ) );
	define('CSP_SELF_DOMAIN', $csp_domain);
	if (
		stripos($_SERVER['HTTP_USER_AGENT'], 'chrome') !== false 
		|| 
		stripos($_SERVER['HTTP_USER_AGENT'], 'safari') !== false
		||
		version_compare(phpversion(), '5.2.1', '<') //IDNA class requires PHP 5.2.1 or higher
	) {
		define('CSP_PO_ADMIN_URL', strtolower($csp_domain).$csp_target);
	}
	else{
		if (!class_exists('idna_convert'))
			require_once('includes/idna_convert.class.php');
		$idn = new idna_convert();
		define('CSP_PO_ADMIN_URL', $idn->decode(strtolower($csp_domain), 'utf8').$csp_target);
	}
	
    define('CSP_PO_BASE_PATH', WP_PLUGIN_DIR . CSP_PO_PLUGINPATH);
	
	define('CSP_PO_MIN_REQUIRED_WP_VERSION', '2.5');
	define('CSP_PO_MIN_REQUIRED_PHP_VERSION', '4.4.2');
		
	register_activation_hook(__FILE__, 'csp_po_install_plugin');

	add_action('plugins_loaded', 'csp_trace_php_errors', 0);
}

function csp_is_multisite() {
	return (
		isset($GLOBALS['wpmu_version'])
		||
		(function_exists('is_multisite') && is_multisite())
		||
		(function_exists('wp_get_mu_plugins') && count(wp_get_mu_plugins()) > 0)
	);
}

if (function_exists('csp_po_install_plugin')) {
	//rewrite and extend the error messages displayed at failed activation
	//fall trough, if it's a real code bug forcing the activation error to get the appropriated message instead
	if (isset($_GET['action']) && isset($_GET['plugin']) && ($_GET['action'] == 'error_scrape') && ($_GET['plugin'] == plugin_basename(__FILE__) )) {
		if (
			(!version_compare($wp_version, CSP_PO_MIN_REQUIRED_WP_VERSION, '>=')) 
			|| 
			(!version_compare(phpversion(), CSP_PO_MIN_REQUIRED_PHP_VERSION, '>='))
			||
			!function_exists('token_get_all')
		) {
			load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages');
			echo "<table>";
			echo "<tr style=\"font-size: 12px;\"><td><strong style=\"border-bottom: 1px solid #000;\">Codestyling Localization</strong></td><td> | ".__('required', CSP_PO_TEXTDOMAIN)."</td><td> | ".__('actual', CSP_PO_TEXTDOMAIN)."</td></tr>";			
			if (!version_compare($wp_version, CSP_PO_MIN_REQUIRED_WP_VERSION, '>=')) {
				echo "<tr style=\"font-size: 12px;\"><td>WordPress Blog Version:</td><td align=\"center\"> &gt;= <strong>".CSP_PO_MIN_REQUIRED_WP_VERSION."</strong></td><td align=\"center\"><span style=\"color:#f00;\">".$wp_version."</span></td></tr>";
			}
			if (!version_compare(phpversion(), CSP_PO_MIN_REQUIRED_PHP_VERSION, '>=')) {
				echo "<tr style=\"font-size: 12px;\"><td>PHP Interpreter Version:</td><td align=\"center\"> &gt;= <strong>".CSP_PO_MIN_REQUIRED_PHP_VERSION."</strong></td><td align=\"center\"><span style=\"color:#f00;\">".phpversion()."</span></td></tr>";
			}
			if (!function_exists('token_get_all')) {
				echo "<tr style=\"font-size: 12px;\"><td>PHP Tokenizer Module:</td><td align=\"center\"><strong>active</strong></td><td align=\"center\"><span style=\"color:#f00;\">not installed</span></td></tr>";			
			}
			echo "</table>";
		}
	}
}


function csp_po_install_plugin(){
	global $wp_version;
	if (
		(!version_compare($wp_version, CSP_PO_MIN_REQUIRED_WP_VERSION, '>=')) 
		|| 
		(!version_compare(phpversion(), CSP_PO_MIN_REQUIRED_PHP_VERSION, '>='))
		|| 
		!function_exists('token_get_all')
	){
		$current = get_option('active_plugins');
		array_splice($current, array_search( plugin_basename(__FILE__), $current), 1 );
		update_option('active_plugins', $current);
		exit;
	}
}


//////////////////////////////////////////////////////////////////////////////////////////
//	general purpose methods
//////////////////////////////////////////////////////////////////////////////////////////

if (!function_exists('_n')) {
	function _n() {
		$args = func_get_args();
		return call_user_func_array('__ngettext', $args);
	}
}

if (!function_exists('_n_noop')) {
	function _n_noop() {
		$args = func_get_args();
		return call_user_func_array('__ngettext_noop', $args);
	}
}

if (!function_exists('_x')) {
	function _x() {
		$args = func_get_args();
		$what = array_shift($args); 
		$args[0] = $what.'|'.$args[0];
		return call_user_func_array('_c', $args);
	}
}

if (!function_exists('esc_js')) {
	function esc_js() {
		$args = func_get_args();
		return call_user_func_array('js_escape', $args);
	}
}

if (!function_exists('__checked_selected_helper')) {
	function __checked_selected_helper( $helper, $current, $echo, $type ) {
		if ( (string) $helper === (string) $current )
			$result = " $type='$type'";
		else
			$result = '';

		if ( $echo )
			echo $result;

		return $result;
	}
}

if (!function_exists('disabled')) {
	function disabled( $disabled, $current = true, $echo = true ) {
		return __checked_selected_helper( $disabled, $current, $echo, 'disabled' );
	}	
}

if (!function_exists('file_get_contents')) {
	function file_get_contents($filename, $incpath = false, $resource_context = null) {
		if (false === $fh = fopen($filename, 'rb', $incpath)) {
			user_error('file_get_contents() failed to open stream: No such file or directory', E_USER_WARNING);
			return false;
		}
		
		clearstatcache();
		if ($fsize = @filesize($filename)) {
			$data = fread($fh, $fsize);
		} else {
			$data = '';
			while (!feof($fh)) {
				$data .= fread($fh, 8192);
			}
		}
		
		fclose($fh);
		return $data;
	}	
}

if (!function_exists('scandir')) {
	function scandir($dir) {
		$files = array();
		$dh  = @opendir($dir);
		while (false !== ($filename = @readdir($dh))) {
		    $files[] = $filename;
		}
		@closedir($dh);
		return $files;
	}
}

function has_subdirs($base='') {
  if (!is_dir($base) || !is_readable($base)) return $false;
  $array = array_diff(scandir($base), array('.', '..'));
  foreach($array as $value) : 
    if (is_dir($base.$value)) return true; 
  endforeach;
  return false;
}

function lscandir($base='', $reg='', &$data) {
  if (!is_dir($base) || !is_readable($base)) return $data;
  $array = array_diff(scandir($base), array('.', '..')); 
  foreach($array as $value) : 
		if (is_file($base.$value) && preg_match($reg, $value) ) : 
			$data[] = str_replace("\\","/",$base.$value); 
		endif;
  endforeach;  
  return $data; 
}

function rscandir($base='', $reg='', &$data) {
  if (!is_dir($base) || !is_readable($base)) return $data;
  $array = array_diff(scandir($base), array('.', '..')); 
  foreach($array as $value) : 
    if (is_dir($base.$value)) : 
      $data = rscandir($base.$value.'/', $reg, $data); 
    elseif (is_file($base.$value) && preg_match($reg, $value) ) : 
      $data[] = str_replace("\\","/",$base.$value); 
    endif;
  endforeach;
  return $data; 
}		

function rscanpath($base='', &$data) {
  if (!is_dir($base) || !is_readable($base)) return $data;
  $array = array_diff(scandir($base), array('.', '..')); 
  foreach($array as $value) : 
    if (is_dir($base.$value)) : 
	  $data[] = str_replace("\\","/",$base.$value);
      $data = rscanpath($base.$value.'/', $data); 
    endif;
  endforeach;
  return $data; 
}		


function rscandir_php($base='', &$exclude_dirs, &$data) {
  if (!is_dir($base) || !is_readable($base)) return $data;
  $array = array_diff(scandir($base), array('.', '..')); 
  foreach($array as $value) : 
    if (is_dir($base.$value)) : 
      if (!in_array($base.$value, $exclude_dirs)) : $data = rscandir_php($base.$value.'/', $exclude_dirs, $data); endif; 
    elseif (is_file($base.$value) && preg_match('/\.(php|phtml)$/', $value) ) : 
      $data[] = str_replace("\\","/",$base.$value); 
    endif;
  endforeach;
  return $data; 
}		

function file_permissions($filename) {
	static $R = array("---","--x","-w-","-wx","r--","r-x","rw-","rwx");
	$perm_o	= substr(decoct(fileperms( $filename )),3);
	return "[".$R[(int)$perm_o[0]] . '|' . $R[(int)$perm_o[1]] . '|' . $R[(int)$perm_o[2]]."]";
}

function csp_fetch_remote_content($url) {
	global $wp_version;
	$res = null;
	
	if(file_exists(ABSPATH . 'wp-includes/class-snoopy.php') && version_compare($wp_version, '3.0', '<')) {
		require_once( ABSPATH . 'wp-includes/class-snoopy.php');
		$s = new Snoopy();
		$s->fetch($url);	
		if($s->status == 200) {
			$res = $s->results;	
		}
	} else {
		$res = wp_remote_fopen($url);	
	}
	return $res;	
}

function csp_po_check_security() {
	if (!is_user_logged_in() || !current_user_can('manage_options')) {
		wp_die(__('You do not have permission to manage translation files.', CSP_PO_TEXTDOMAIN));
	}
}

function csp_find_translation_template(&$files) {
	$result = null;
	foreach($files as $tt) {
		if (preg_match('/\.pot$/',$tt)) {
			$result = $tt;
		}
	}
	return $result;
}

function csp_po_get_wordpress_capabilities() {
	$data = array();
	$data['dev-hints'] = null;
	$data['deny_scanning'] = false;
	$data['locale'] = get_locale();
	$data['type'] = 'wordpress';
	$data['img_type'] = 'wordpress';
	if (csp_is_multisite()) $data['img_type'] .= "_mu";
	$data['type-desc'] = __('WordPress',CSP_PO_TEXTDOMAIN);
	$data['name'] = "WordPress";
	$data['author'] = "<a href=\"http://codex.wordpress.org/WordPress_in_Your_Language\">WordPress.org</a>";
	$data['version'] = $GLOBALS['wp_version'];
	if (csp_is_multisite()) $data['version'] .= " | ".(isset($GLOBALS['wpmu_version']) ? $GLOBALS['wpmu_version'] : $GLOBALS['wp_version']);
	$data['description'] = "WordPress is a state-of-the-art publishing platform with a focus on aesthetics, web standards, and usability. WordPress is both free and priceless at the same time.<br />More simply, WordPress is what you use when you want to work with your blogging software, not fight it.";
	$data['status'] =  __("activated",CSP_PO_TEXTDOMAIN);
	$data['base_path'] = str_replace("\\","/", ABSPATH);
	$data['special_path'] = '';
	$data['filename'] = str_replace(str_replace("\\","/",ABSPATH), '', str_replace("\\","/",WP_LANG_DIR));
	$data['is-simple'] = false;
	$data['simple-filename'] = '';
	$data['textdomain'] = array('identifier' => 'default', 'is_const' => false );
	$data['languages'] = array();
	$data['is-path-unclear'] = false;
	$data['gettext_ready'] = true;
	$data['translation_template'] = null;
	$tmp = array();
	$data['is_US_Version'] = !is_dir(WP_LANG_DIR);
	if (!$data['is_US_Version']) {
		$files = rscandir(str_replace("\\","/",WP_LANG_DIR).'/', "/(.\mo|\.po|\.pot)$/", $tmp);
		$data['translation_template'] = csp_find_translation_template($files);
		foreach($files as $filename) {
			$file = str_replace(str_replace("\\","/",WP_LANG_DIR).'/', '', $filename);
			preg_match("/^([a-z][a-z]_[A-Z][A-Z]).(mo|po)$/", $file, $hits);
			if (empty($hits[1]) === false) {
				$data['languages'][$hits[1]][$hits[2]] = array(
					'class' => "-".(is_readable($filename) ? 'r' : '').(is_writable($filename) ? 'w' : ''),
					'stamp' => date(__('m/d/Y H:i:s',CSP_PO_TEXTDOMAIN), filemtime($filename))." ".file_permissions($filename)
				);
				$data['special_path'] = '';
			}
		}

		$data['base_file'] = (empty($data['special_path']) ? '' : $data['special_path'].'/') . $data['filename'].'/';
	}
	return $data;
}

function csp_po_get_plugin_capabilities($plug, $values) {
	$data = array();
	$data['dev-hints'] 		= null;
	$data['dev-security'] 	= null;
	$data['deny_scanning'] 	= false;
	$data['locale'] = get_locale();
	$data['type'] = 'plugins';	
	$data['img_type'] = 'plugins';	
	$data['type-desc'] = __('Plugin',CSP_PO_TEXTDOMAIN);	
	$data['name'] = $values['Name'];
	if (isset($values['AuthorURI'])) {
		$data['author'] = "<a href='".$values['AuthorURI']."'>".$values['Author']."</a>";
	}else{
		$data['author'] = $values['Author'];
	}
	$data['version'] = $values['Version'];
	$data['description'] = $values['Description'];
	$data['status'] = is_plugin_active($plug) ? __("activated",CSP_PO_TEXTDOMAIN) : __("deactivated",CSP_PO_TEXTDOMAIN);
	$data['base_path'] = str_replace("\\","/", WP_PLUGIN_DIR.'/'.dirname($plug).'/');
	$data['special_path'] = '';
	$data['filename'] = "";
	$data['is-simple'] = (dirname($plug) == '.');
	$data['simple-filename'] = '';
	$data['is-path-unclear'] = false;
	$data['gettext_ready'] = false;
	$data['translation_template'] = null;
	if ($data['is-simple']) {
		$files = array(WP_PLUGIN_DIR.'/'.$plug);
		$data['simple-filename'] = str_replace("\\","/",WP_PLUGIN_DIR.'/'.$plug);
		$data['base_path'] = str_replace("\\","/", WP_PLUGIN_DIR.'/');
	}
	else{
		$tmp = array();
		$files = rscandir(str_replace("\\","/",WP_PLUGIN_DIR).'/'.dirname($plug)."/", "/.(php|phtml)$/", $tmp);
	}
	$const_list = array();
	foreach($files as $file) {	
		$content = file_get_contents($file);
		if (preg_match("/[^_^!]load_(|plugin_)textdomain\s*\(\s*(\'|\"|)([\w\d\-_]+|[A-Z\d\-_]+)(\'|\"|)\s*(,|\))\s*([^;]+)\)/", $content, $hits)) {
			$data['textdomain'] = array('identifier' => $hits[3], 'is_const' => empty($hits[2]) );
			$data['gettext_ready'] = true;
			$data['php-path-string'] = $hits[6];
		}
		else if(preg_match("/[^_^!]load_(|plugin_)textdomain\s*\(/", $content, $hits)) {
			//ATTENTION: it is gettext ready but we don't realy know the textdomain name! Assume it's equal to plugin's name.
			//TODO: let's think about it in future to find a better solution.
			$data['textdomain'] = array('identifier' => substr(basename($plug),0,-4), 'is_const' => false );
			$data['gettext_ready'] = true;
			$data['php-path-string'] = '';	
		}
		if (isset($hits[1]) && $hits[1] != 'plugin_') 	$data['dev-hints'] = __("<strong>Loading Issue: </strong>Author is using <em>load_textdomain</em> instead of <em>load_plugin_textdomain</em> function. This may break behavior of WordPress, because some filters and actions won't be executed anymore. Please contact the Author about that.",CSP_PO_TEXTDOMAIN);
		if($data['gettext_ready'] && !$data['textdomain']['is_const']) break; //make it short :-)
		if (preg_match_all("/define\s*\(([^\)]+)\)/" , $content, $hits)) {
			$const_list = array_merge($const_list, $hits[1]);
		}
	}
	if ($data['gettext_ready']) {
		
		if ($data['textdomain']['is_const']) {
			foreach($const_list as $e) {
				$a = explode(',', $e);
				$c = trim($a[0], "\"' \t");
				if ($c == $data['textdomain']['identifier']) {
					$data['textdomain']['is_const'] = $data['textdomain']['identifier'];
					$data['textdomain']['identifier'] = trim($a[1], "\"' \t");
				}
			}
		}
		$data['filename'] = $data['textdomain']['identifier'];
		//check if const contains brackets, mostly by functional defined const
		if(preg_match("/(\(|\))/", $data['textdomain']['identifier'])) {
			$data['filename'] = str_replace('.php', '', basename($plug));
			$data['textdomain']['is_const'] = false;
			$data['textdomain']['identifier'] = str_replace('.php', '', basename($plug));
			//var_dump(str_replace('.php', '', basename($plug)));
		}
	}		
	
	if (!$data['gettext_ready']) {
		//lets check, if the plugin is a encrypted one could be translated or an unknow but with defined textdomain
		//ATTENTION: mark encrypted plugins as a high security risk!!!
		if (isset($values['TextDomain']) && !empty($values['TextDomain'])) {
			$data['textdomain'] = array('identifier' => $values['TextDomain'], 'is_const' => false );
			$data['gettext_ready'] = true;
			$data['filename'] = $data['textdomain']['identifier'];
			
			$inside = token_get_all(file_get_contents(WP_PLUGIN_DIR."/".$plug));
			$encrypted = false;
			foreach($inside as $token) {
				if (is_array($token)) {
					list($id, $text) = $token;
					if (T_EVAL == $id) {
						$encrypted =true;
						break;
					}
				}
			}
			if($encrypted) {
				$data['img_type'] = 'plugins_encrypted';
				$data['dev-security'] .= __("<strong>Full Encryped PHP Code: </strong>This plugin consists out of encryped code will be <strong>eval</strong>'d at runtime! It can't be checked against exploitable code pieces. That's why it will become potential target of hidden intrusion.",CSP_PO_TEXTDOMAIN);
				$data['deny_scanning'] = true;
			}
			else {
				$data['img_type'] = 'plugins_maybe';
				$data['dev-hints'] .= __("<strong>Textdomain definition: </strong>This plugin provides a textdomain definition at plugin header fields but seems not to load any translation file. If it doesn't show your translation, please contact the plugin Author.",CSP_PO_TEXTDOMAIN);
			}
		}
	}
	
	$data['languages'] = array();
	if($data['gettext_ready']){
		if ($data['is-simple']) { $tmp = array(); $files = lscandir(str_replace("\\","/",dirname(WP_PLUGIN_DIR.'/'.$plug)).'/', "/(\.mo|\.po|\.pot)$/", $tmp); }
		else { 	$tmp = array(); $files = rscandir(str_replace("\\","/",dirname(WP_PLUGIN_DIR.'/'.$plug)).'/', "/(\.mo|\.po|\.pot)$/", $tmp); }
		$data['translation_template'] = csp_find_translation_template($files);
			
		if ($data['is-simple']) { //simple plugin case
			//1st - try to find the assumed one files
			foreach($files as $filename) {
				$file = str_replace(str_replace("\\","/",WP_PLUGIN_DIR).'/'.dirname($plug), '', $filename);
				preg_match("/".$data['filename']."-([a-z][a-z]_[A-Z][A-Z])\.(mo|po)$/", $file, $hits);
				if (empty($hits[2]) === false) {				
					$data['languages'][$hits[1]][$hits[2]] = array(
						'class' => "-".(is_readable($filename) ? 'r' : '').(is_writable($filename) ? 'w' : ''),
						'stamp' => date(__('m/d/Y H:i:s',CSP_PO_TEXTDOMAIN), filemtime($filename))." ".file_permissions($filename)
					);
					$data['special_path'] = '';
				}
			}
			//2nd - try to re-construct, if nessessary, avoid multi textdomain issues
			if(count($data['languages']) == 0) {
				foreach($files as $filename) {
					//bugfix: uppercase filenames supported
					preg_match("/([A-Za-z0-9\-_]+)-([a-z][a-z]_[A-Z][A-Z])\.(mo|po)$/", $file, $hits);
					if (empty($hits[2]) === false) {				
						$data['filename'] = $hits[1];
						$data['textdomain']['identifier'] = $hits[1];
						$data['img_type'] = 'plugins_maybe';
						$data['dev-hints'] .= __("<strong>Textdomain definition: </strong>There are problems to find the used textdomain. It has been taken from existing translation files. If it doesn't work with your install, please contact the Author of this plugin.",CSP_PO_TEXTDOMAIN);
						
						$data['languages'][$hits[2]][$hits[3]] = array(
							'class' => "-".(is_readable($filename) ? 'r' : '').(is_writable($filename) ? 'w' : ''),
							'stamp' => date(__('m/d/Y H:i:s',CSP_PO_TEXTDOMAIN), filemtime($filename))." ".file_permissions($filename)
						);
						$data['special_path'] = '';
					}
				}
			}
		}
		else { //complex plugin case
			//1st - try to find the assumed one files
			foreach($files as $filename) {
				$file = str_replace(str_replace("\\","/",WP_PLUGIN_DIR).'/'.dirname($plug), '', $filename);
				//bugfix: uppercase folders supported
				preg_match("/([\/A-Za-z0-9\-_]*)\/".$data['filename']."-([a-z][a-z]_[A-Z][A-Z])\.(mo|po)$/", $file, $hits);
				if (empty($hits[2]) === false) {
					//bugfix: only accept those mathing known textdomain
					if ($data['textdomain']['identifier'] == $data['filename'])
					{
						$data['languages'][$hits[2]][$hits[3]] = array(
							'class' => "-".(is_readable($filename) ? 'r' : '').(is_writable($filename) ? 'w' : ''),
							'stamp' => date(__('m/d/Y H:i:s',CSP_PO_TEXTDOMAIN), filemtime($filename))." ".file_permissions($filename)
						);
					}
					$data['special_path'] = ltrim($hits[1], "/");
				}
			}
			//2nd - try to re-construct, if nessessary, avoid multi textdomain issues
			if(count($data['languages']) == 0) {
				foreach($files as $filename) {
					//try to re-construct from real file.
					//bugfix: uppercase folders supported, additional uppercased filenames!
					preg_match("/([\/A-Za-z0-9\-_]*)\/([\/A-Za-z0-9\-_]+)-([a-z][a-z]_[A-Z][A-Z])\.(mo|po)$/", $file, $hits);
					if (empty($hits[3]) === false) {
						$data['filename'] = $hits[2];
						$data['textdomain']['identifier'] = $hits[2];
						$data['img_type'] = 'plugins_maybe';
						$data['dev-hints'] .= __("<strong>Textdomain definition: </strong>There are problems to find the used textdomain. It has been taken from existing translation files. If it doesn't work with your install, please contact the Author of this plugin.",CSP_PO_TEXTDOMAIN);

						$data['languages'][$hits[3]][$hits[4]] = array(
							'class' => "-".(is_readable($filename) ? 'r' : '').(is_writable($filename) ? 'w' : ''),
							'stamp' => date(__('m/d/Y H:i:s',CSP_PO_TEXTDOMAIN), filemtime($filename))." ".file_permissions($filename)
						);
						$data['special_path'] = ltrim($hits[1], "/");
					}
				}			
			}
		}
		if (!$data['is-simple'] && ($data['special_path'] == '') && (count($data['languages']) == 0)) {
			$data['is-path-unclear'] = has_subdirs(str_replace("\\","/",dirname(WP_PLUGIN_DIR.'/'.$plug)).'/');
			if ($data['is-path-unclear'] && (count($files) > 0)) {
				$file = str_replace(str_replace("\\","/",WP_PLUGIN_DIR).'/'.dirname($plug), '', $files[0]);
				//bugfix: uppercase folders supported
				preg_match("/^\/([\/A-Za-z0-9\-_]*)\//", $file, $hits);
				$data['is-path-unclear'] = false;
				if (empty($hits[1]) === false) { $data['special_path'] = $hits[1]; }
			}
		}
		//supporting the plugins suggestion for language path
		if ($data['is-path-unclear'] && isset($values['DomainPath']) && is_dir(dirname(WP_PLUGIN_DIR.'/'.$plug).'/'.trim($values['DomainPath'], "\\/")) )
		{
			$data['is-path-unclear'] = false;
			$data['special_path'] = trim($values['DomainPath'], "\\/");		
		}

		//DEBUG:  $data['php-path-string']  will contain real path part like: "false,'codestyling-localization'" | "'wp-content/plugins/' . NGGFOLDER . '/lang'" | "GENGO_LANGUAGES_DIR" | "$moFile"
		//this may be part of later excessive parsing to find correct lang file path even if no lang files exist as hint or implementation of directory selector, if 0 languages contained
		//if any lang files may be contained the qualified sub path will be extracted out of
		//will be handled in case of  $data['is-path-unclear'] == true by display of treeview at file creation dialog 
		//var_dump($data['php-path-string']);

	}
	$data['base_file'] = (empty($data['special_path']) ? $data['filename'] : $data['special_path']."/".$data['filename']).'-';	
	return $data;
}

function csp_po_get_plugin_mu_capabilities($plug, $values){
	$data = array();
	$data['dev-hints'] = null;
	$data['deny_scanning'] = false;
	$data['locale'] = get_locale();
	$data['type'] = 'plugins_mu';	
	$data['img_type'] = 'plugins_mu';	
	$data['type-desc'] = __('μ Plugin',CSP_PO_TEXTDOMAIN);	
	$data['name'] = $values['Name'];
	if (isset($values['AuthorURI'])) {
		$data['author'] = "<a href='".$values['AuthorURI']."'>".$values['Author']."</a>";
	}else{
		$data['author'] = $values['Author'];
	}
	$data['version'] = $values['Version'];
	$data['description'] = $values['Description'];
	$data['status'] = __("activated",CSP_PO_TEXTDOMAIN);
	$data['base_path'] = str_replace("\\","/", WPMU_PLUGIN_DIR.'/');
	$data['special_path'] = '';
	$data['filename'] = "";
	$data['is-simple'] = true;
	$data['simple-filename'] = str_replace("\\","/",WPMU_PLUGIN_DIR.'/'.$plug); 
	$data['is-path-unclear'] = false;
	$data['gettext_ready'] = false;
	$data['translation_template'] = null;
	$file = WPMU_PLUGIN_DIR.'/'.$plug;

	$const_list = array();
	$content = file_get_contents($file);
	if (preg_match("/[^_^!]load_(|plugin_|muplugin_)textdomain\s*\(\s*(\'|\"|)([\w\d\-_]+|[A-Z\d\-_]+)(\'|\"|)\s*(,|\))\s*([^;]+)\)/", $content, $hits)) {
		$data['textdomain'] = array('identifier' => $hits[3], 'is_const' => empty($hits[2]) );
		$data['gettext_ready'] = true;
		$data['php-path-string'] = $hits[6];
	}
	else if(preg_match("/[^_^!]load_(|plugin_|muplugin_)textdomain\s*\(/", $content, $hits)) {
		//ATTENTION: it is gettext ready but we don't realy know the textdomain name! Assume it's equal to plugin's name.
		//TODO: let's think about it in future to find a better solution.
		$data['textdomain'] = array('identifier' => substr(basename($plug),0,-4), 'is_const' => false );
		$data['gettext_ready'] = true;
		$data['php-path-string'] = '';			
	}
	if (!($data['gettext_ready'] && !$data['textdomain']['is_const'])) {
		if (preg_match_all("/define\s*\(([^\)]+)\)/" , $content, $hits)) {
			$const_list = array_merge($const_list, $hits[1]);
		}
	}

	if ($data['gettext_ready']) {
		
		if ($data['textdomain']['is_const']) {
			foreach($const_list as $e) {
				$a = split(',', $e);
				$c = trim($a[0], "\"' \t");
				if ($c == $data['textdomain']['identifier']) {
					$data['textdomain']['is_const'] = $data['textdomain']['identifier'];
					$data['textdomain']['identifier'] = trim($a[1], "\"' \t");
				}
			}
		}
		$data['filename'] = $data['textdomain']['identifier'];
	}		
	
	$data['languages'] = array();
	if($data['gettext_ready']){
		$tmp = array(); $files = lscandir(str_replace("\\","/",dirname(WPMU_PLUGIN_DIR.'/'.$plug)).'/', "/(\.mo|\.po|\.pot)$/", $tmp); 		
		$data['translation_template'] = csp_find_translation_template($files);
		foreach($files as $filename) {
			$file = str_replace(str_replace("\\","/",WPMU_PLUGIN_DIR).'/'.dirname($plug), '', $filename);
			preg_match("/".$data['filename']."-([a-z][a-z]_[A-Z][A-Z]).(mo|po)$/", $file, $hits);		
			if (empty($hits[2]) === false) {				
				$data['languages'][$hits[1]][$hits[2]] = array(
					'class' => "-".(is_readable($filename) ? 'r' : '').(is_writable($filename) ? 'w' : ''),
					'stamp' => date(__('m/d/Y H:i:s',CSP_PO_TEXTDOMAIN), filemtime($filename))." ".file_permissions($filename)
				);
				$data['special_path'] = '';
			}
		}
	}
	$data['base_file'] = (empty($data['special_path']) ? $data['filename'] : $data['special_path']."/".$data['filename']).'-';		
	return $data;
}

function csp_po_get_theme_capabilities($theme, $values, $active) {
	$data = array();
	$data['dev-hints'] = null;
	$data['deny_scanning'] = false;

	//let's first check the whether we have a child or base theme
	if(is_object($values) && get_class($values) == 'WP_Theme') {
		//WORDPRESS Version 3.4 changes theme handling!
		$theme_root = trailingslashit(str_replace("\\","/", get_theme_root()));
		$firstfile = array_values($values['Template Files']);
		$firstfile = array_shift($firstfile);
		$firstfile = str_replace("\\","/", $firstfile);
		$firstfile = str_replace($theme_root, '', $firstfile);
		$firstfile = explode('/',$firstfile);
		$firstfile = reset($firstfile);
		$data['base_path'] = $theme_root.$firstfile.'/';
	}else{
		$data['base_path'] = str_replace("\\","/", WP_CONTENT_DIR.str_replace('wp-content', '', dirname($values['Template Files'][0])).'/');
		if (file_exists($values['Template Files'][0])){
			$data['base_path'] = dirname(str_replace("\\","/",$values['Template Files'][0])).'/';
		}
	}
	$fc = explode('/',untrailingslashit($data['base_path']));
	$folder_filesys = end($fc);
	$folder_data = $values['Template']; 
	$is_child_theme = $folder_filesys != $folder_data;
	$data['theme-self'] = $folder_filesys;
	$data['theme-template'] = $folder_data;
	
	$data['locale'] = get_locale();
	$data['type'] = 'themes';
	$data['img_type'] = ($is_child_theme ? 'childthemes' : 'themes');	
	$data['type-desc'] = ($is_child_theme ? __('Childtheme',CSP_PO_TEXTDOMAIN) : __('Theme',CSP_PO_TEXTDOMAIN));	
	$data['name'] = $values['Name'];
	$data['author'] = $values['Author'];
	$data['version'] = $values['Version'];
	$data['description'] = $values['Description'];
	$data['status'] = $values['Name'] == $active->name ? __("activated",CSP_PO_TEXTDOMAIN) : __("deactivated",CSP_PO_TEXTDOMAIN);
//	$data['status'] = $theme == $active->name ? __("activated",CSP_PO_TEXTDOMAIN) : __("deactivated",CSP_PO_TEXTDOMAIN);
	if ($is_child_theme) {
		$data['status'] .= ' / <b></i>'.__('child theme of',CSP_PO_TEXTDOMAIN).' '.$values['Parent Theme'].'</i></b>';
	}
	$data['special-path'] = '';
	$data['is-path-unclear'] = false;
	$data['gettext_ready'] = false;
	$data['translation_template'] = null;
	$data['is-simple'] = false;
	$data['simple-filename'] = '';
	
	//now scanning the child's own files
	$parent_files = array();
	$files = array();
	$const_list = array();
	$tmp = array();
	$files = rscandir($data["base_path"], "/\.(php|phtml)$/", $tmp);
	foreach($files as $themefile) {
		$main = file_get_contents($themefile);
		if (
			preg_match("/[^_^!]load_(child_theme_|theme_|)textdomain\s*\(\s*(\'|\"|)([\w\d\-_]+|[A-Z\d\-_]+)(\'|\"|)\s*(,|\))/", $main, $hits)
			||
			preg_match("/[^_^!]load_(child_theme_|theme_|)textdomain\s*\(\s*/", $main, $hits)			
		) {
			if (isset($hits[1]) && $hits[1] != 'child_theme_' && $hits[1] != 'theme_') 	$data['dev-hints'] = __("<strong>Loading Issue: </strong>Author is using <em>load_textdomain</em> instead of <em>load_theme_textdomain</em> or <em>load_child_theme_textdomain</em> function. This may break behavior of WordPress, because some filters and actions won't be executed anymore. Please contact the Author about that.",CSP_PO_TEXTDOMAIN);
		
			//fallback for variable names used to load textdomain, assumes theme name
			if(isset($hits[3]) && strpos($hits[3], '$') !== false) {
				unset($hits[3]);
				if (isset($data['dev-hints'])) $data['dev-hints'] .= "<br/><br/>";
				$data['dev-hints'] = __("<strong>Textdomain Naming Issue: </strong>Author uses a variable to load the textdomain. It will be assumed to be equal to theme name now.",CSP_PO_TEXTDOMAIN);
			}			
			//make it short
			$data['gettext_ready'] = true;
			if ($data['gettext_ready']) {
				if (!isset($hits[3])) {
					$data['textdomain'] = array('identifier' => $values['Template'], 'is_const' => false );
				}else {
					$data['textdomain'] = array('identifier' => $hits[3], 'is_const' => empty($hits[2]) );
				}
				$data['languages'] = array();
			}

			$dn = $data["base_path"];
			$tmp = array();
			$lng_files = rscandir($dn, "/(\.mo|\.po|\.pot)$/", $tmp);
			$data['translation_template'] = csp_find_translation_template($lng_files);
			$sub_dirs = array();
			$naming_convention_error = false;
			foreach($lng_files as $filename) {
				//somebody did place buddypress themes at sub folder hierarchy like:  themes/buddypress/bp-default
				//results at $values['Template'] to 'buddypress/bp-default' which damages the preg_match
				$v = explode('/',$values['Template']);
				$theme_langfile_check = end($v);
				preg_match("/\/(|".preg_quote($theme_langfile_check)."\-)([a-z][a-z]_[A-Z][A-Z])\.(mo|po)$/", $filename, $hits);
				if (empty($hits[1]) === false) {
					$naming_convention_error = true;

					$data['filename'] = '';
					$sd = dirname(str_replace($dn, '', $filename));
					if ($sd == '.') $sd = '';
					if (!in_array($sd, $sub_dirs)) $sub_dirs[] = $sd;
					
				}elseif (empty($hits[2]) === false) {
					$data['languages'][$hits[2]][$hits[3]] = array(
						'class' => "-".(is_readable($filename) ? 'r' : '').(is_writable($filename) ? 'w' : ''),
						'stamp' => date(__('m/d/Y H:i:s',CSP_PO_TEXTDOMAIN), filemtime($filename))." ".file_permissions($filename)
					);
					$data['filename'] = '';
					$sd = dirname(str_replace($dn, '', $filename));
					if ($sd == '.') $sd = '';
					if (!in_array($sd, $sub_dirs)) $sub_dirs[] = $sd;
				}
			}
			if($naming_convention_error && count($data['languages']) == 0) {
				if (isset($data['dev-hints'])) $data['dev-hints'] .= "<br/><br/>";
				$data['dev-hints'] .= sprintf(__("<strong>Naming Issue: </strong>Author uses unsupported language file naming convention! Instead of example <em>de_DE.po</em> the non theme standard version <em>%s</em> has been used. If you translate this Theme, only renamed language files will be working!",CSP_PO_TEXTDOMAIN), $values['Template'].'-de_DE.po');
			}
			
			//completely other directories can be defined WP if >= 2.7.0
			global $wp_version;
			if (version_compare($wp_version, '2.7', '>=')) {
				if (count($data['languages']) == 0) {
					$data['is-path-unclear'] = has_subdirs($dn);
					if ($data['is-path-unclear'] && (count($lng_files) > 0)) {
						foreach($lng_files as $file) {
							$f = str_replace($dn, '', $file);
							if (
								preg_match("/^([a-z][a-z]_[A-Z][A-Z])\.(mo|po|pot)$/", basename($f))
								||
								preg_match("/\.po(t|)$/", basename($f))
							) {
								$data['special_path'] = (dirname($f) == '.' ? '' : dirname($f));
								$data['is-path-unclear'] = false;
								break;
							}
						}
					}
				}
				else{
					if ($sub_dirs[0] != '') {
						$data['special_path'] = ltrim($sub_dirs[0], "/");
					}
				}
			}

		}
		if($data['gettext_ready'] && !$data['textdomain']['is_const']) break; //make it short :-)
		if (preg_match_all("/define\s*\(([^\)]+)\)/" , $main, $hits)) {
			$const_list = array_merge($const_list, $hits[1]);
		}
	}
	$data['base_file'] = (empty($data['special_path']) ? '' : $data['special_path']."/");

	$constant_failed = false;
	if ($data['gettext_ready']) {	
		if ($data['textdomain']['is_const']) {
			foreach($const_list as $e) {
				$a = explode(',', $e);
				$c = trim($a[0], "\"' \t");
				if ($c == $data['textdomain']['identifier']) {
					$data['textdomain']['is_const'] = $data['textdomain']['identifier'];
					$data['textdomain']['identifier'] = trim($a[1], "\"' \t");
				}
			}
		}
		
		//fallback for constants defined by variables! assume the theme name instead
		if(
			(strpos($data['textdomain']['identifier'], '$') !== false) 
			||
			(strpos($data['textdomain']['identifier'], '"') !== false)
			||
			(strpos($data['textdomain']['identifier'], '\'') !== false)
		){
			$constant_failed = true;
			$data['textdomain']['identifier'] = $values['Template'];
			if (isset($data['dev-hints'])) $data['dev-hints'] .= "<br/><br/>";
			$data['dev-hints'] = __("<strong>Textdomain Naming Issue: </strong>Author uses a variable to define the textdomain constant. It will be assumed to be equal to theme name now.",CSP_PO_TEXTDOMAIN);
		}			

	}		
	//check now known issues for themes
	if(isset($data['textdomain']['identifier']) && $data['textdomain']['identifier'] == 'woothemes') {
		if (isset($data['dev-hints'])) $data['dev-hints'] .= "<br/><br/>";
		$data['dev-hints'] .= __("<strong>WooThemes Issue: </strong>The Author is known for not supporting a translatable backend. Please expect only translations for frontend or contact the Author for support!",CSP_PO_TEXTDOMAIN);
	}
	if(isset($data['textdomain']['identifier']) && $data['textdomain']['identifier'] == 'ares' && $constant_failed) {
		if (isset($data['dev-hints'])) $data['dev-hints'] .= "<br/><br/>";
		$data['dev-hints'] .= __("<strong>Ares Theme Issue: </strong>This theme uses a textdomain defined by string concatination code. The textdomain will be patched to 'AresLanguage', please contact the theme author to change this into a fix constant value! ",CSP_PO_TEXTDOMAIN);
		$data['textdomain']['identifier'] = 'AresLanguage';
	}
	
	
	return $data;
}

function csp_po_get_buddypress_capabilities($plug, $values) {
	$data = array();
	$data['dev-hints'] = null;
	$data['deny_scanning'] = false;
	$data['locale'] = get_locale();
	$data['type'] = 'plugins';	
	$data['img_type'] = 'buddypress';	
	$data['type-desc'] = __('BuddyPress',CSP_PO_TEXTDOMAIN);	
	$data['name'] = $values['Name'];
	if (isset($values['AuthorURI'])) {
		$data['author'] = "<a href='".$values['AuthorURI']."'>".$values['Author']."</a>";
	}else{
		$data['author'] = $values['Author'];
	}
	$data['version'] = $values['Version'];
	$data['description'] = $values['Description'];
	$data['status'] = is_plugin_active($plug) ? __("activated",CSP_PO_TEXTDOMAIN) : __("deactivated",CSP_PO_TEXTDOMAIN);
	$data['base_path'] = str_replace("\\","/", WP_PLUGIN_DIR.'/'.dirname($plug).'/');
	$data['special_path'] = '';
	$data['filename'] = "buddypress";
	$data['is-simple'] = false;
	$data['simple-filename'] = '';
	$data['is-path-unclear'] = false;
	$data['gettext_ready'] = true;	
	$data['translation_template'] = null;
	$data['textdomain'] = array('identifier' => 'buddypress', 'is_const' => false );
	$data['special_path'] = 'bp-languages';
	$data['languages'] = array();
	$tmp = array(); 
	$files = lscandir(str_replace("\\","/",dirname(WP_PLUGIN_DIR.'/'.$plug)).'/bp-languages/', "/(\.mo|\.po|\.pot)$/", $tmp); 
	$data['translation_template'] = csp_find_translation_template($files);
	foreach($files as $filename) {
		$file = str_replace(str_replace("\\","/",WP_PLUGIN_DIR).'/'.dirname($plug), '', $filename);
		preg_match("/".$data['filename']."-([a-z][a-z]_[A-Z][A-Z]).(mo|po)$/", $file, $hits);		
		if (empty($hits[2]) === false) {				
			$data['languages'][$hits[1]][$hits[2]] = array(
				'class' => "-".(is_readable($filename) ? 'r' : '').(is_writable($filename) ? 'w' : ''),
				'stamp' => date(__('m/d/Y H:i:s',CSP_PO_TEXTDOMAIN), filemtime($filename))." ".file_permissions($filename)
			);
		}
	}
	$data['base_file'] = (empty($data['special_path']) ? $data['filename'] : $data['special_path']."/".$data['filename']).'-';	
	return $data;
}

function csp_po_get_bbpress_on_buddypress_capabilities($plug, $values) {
	$data = array();
	$data['dev-hints'] = null;
	$data['deny_scanning'] = false;
	$data['locale'] = get_locale();
	$data['type'] = 'plugins';	
	$data['img_type'] = 'buddypress-bbpress';	
	$data['type-desc'] = __('bbPress',CSP_PO_TEXTDOMAIN);	
	$data['name'] = "bbPress";
	$data['author'] = "<a href='http://bbpress.org/'>bbPress.org</a>";
	$data['version'] = '-n.a.-';
	$data['description'] = "bbPress is forum software with a twist from the creators of WordPress.";
	$data['status'] = is_plugin_active($plug) ? __("activated",CSP_PO_TEXTDOMAIN) : __("deactivated",CSP_PO_TEXTDOMAIN);
	$data['base_path'] = str_replace("\\","/", WP_PLUGIN_DIR.'/'.dirname($plug).'/bp-forums/bbpress/');
	if (!is_dir($data['base_path'])) return false;
	$data['special_path'] = '';
	$data['filename'] = "";
	$data['is-simple'] = false;
	$data['simple-filename'] = '';
	$data['is-path-unclear'] = false;
	$data['gettext_ready'] = true;	
	$data['translation_template'] = null;
	$data['textdomain'] = array('identifier' => 'default', 'is_const' => false );
	$data['special_path'] = 'my-languages';
	$data['languages'] = array();
	$data['is_US_Version'] = !is_dir(str_replace("\\","/",dirname(WP_PLUGIN_DIR.'/'.$plug)).'/bp-forums/bbpress/my-languages');
	if (!$data['is_US_Version']) {	
		$tmp = array(); 	
		$files = lscandir(str_replace("\\","/",dirname(WP_PLUGIN_DIR.'/'.$plug)).'/bp-forums/bbpress/my-languages/', "/(\.mo|\.po|\.pot)$/", $tmp); 
		$data['translation_template'] = csp_find_translation_template($files);
		foreach($files as $filename) {
			$file = str_replace(str_replace("\\","/",WP_PLUGIN_DIR).'/'.dirname($plug), '', $filename);
			preg_match("/([a-z][a-z]_[A-Z][A-Z]).(mo|po)$/", $file, $hits);		
			if (empty($hits[2]) === false) {				
				$data['languages'][$hits[1]][$hits[2]] = array(
					'class' => "-".(is_readable($filename) ? 'r' : '').(is_writable($filename) ? 'w' : ''),
					'stamp' => date(__('m/d/Y H:i:s',CSP_PO_TEXTDOMAIN), filemtime($filename))." ".file_permissions($filename)
				);
			}
		}
	}
	$data['base_file'] = (empty($data['special_path']) ? $data['filename'] : $data['special_path']."/");	
	return $data;
}


function csp_po_collect_by_type($type){
	$res = array();
	$do_compat_filter = ($type == 'compat');
	$do_security_filter = ($type == 'security');
	if ($do_compat_filter || $do_security_filter) $type = '';
	if (empty($type) || ($type == 'wordpress')) {
		if (!$do_compat_filter && !$do_security_filter)
			$res[] = csp_po_get_wordpress_capabilities();
	}
	if (empty($type) || ($type == 'plugins')) {
		//WARNING: Plugin handling is not well coded by WordPress core
		$err = error_reporting(0);
		$plugs = get_plugins(); 
		error_reporting($err);
		$textdomains = array();
		foreach($plugs as $key => $value) { 
			$data = null;
			if (dirname($key) == 'buddypress') {
				if ($do_compat_filter || $do_security_filter) continue;
				$data = csp_po_get_buddypress_capabilities($key, $value);
				$res[] = $data;
				$data = csp_po_get_bbpress_on_buddypress_capabilities($key, $value);
				if($data !== false) $res[] = $data;
			}else {
				$data = csp_po_get_plugin_capabilities($key, $value);
				if (!$data['gettext_ready']) continue;
				if (in_array($data['textdomain'], $textdomains)) {
					for ($i=0; $i<count($res); $i++) {
						if ($data['textdomain'] == $res[$i]['textdomain']) {
							$res[$i]['child-plugins'][] = $data;
							break;
						}
					}
				}
				else{
					if ($do_compat_filter && !isset($data['dev-hints'])) continue;
					elseif ($do_security_filter && !isset($data['dev-security'])) continue;
					array_push($textdomains, $data['textdomain']);
					$res[] = $data;
				}
			}
		}
	}
	if (csp_is_multisite()) {
		if (empty($type) || ($type == 'plugins_mu')) {
			$plugs = array();
			$textdomains = array();
			if( is_dir( WPMU_PLUGIN_DIR ) ) {
				if( $dh = opendir( WPMU_PLUGIN_DIR ) ) {
					while( ( $plugin = readdir( $dh ) ) !== false ) {
						if( substr( $plugin, -4 ) == '.php' ) {
							$plugs[$plugin] = get_plugin_data( WPMU_PLUGIN_DIR . '/' . $plugin );
						}
					}
				}
			}		
			foreach($plugs as $key => $value) { 
				$data = csp_po_get_plugin_mu_capabilities($key, $value);
				if (!$data['gettext_ready']) continue;
				if ($do_compat_filter && !isset($data['dev-hints'])) continue;
				elseif ($do_security_filter && !isset($data['dev-security'])) continue;
				if (in_array($data['textdomain'], $textdomains)) {
					for ($i=0; $i<count($res); $i++) {
						if ($data['textdomain'] == $res[$i]['textdomain']) {
							$res[$i]['child-plugins'][] = $data;
							break;
						}
					}
				}
				else{
					if ($do_compat_filter && !isset($data['dev-hints'])) continue;
					elseif ($do_security_filter && !isset($data['dev-security'])) continue;
					array_push($textdomains, $data['textdomain']);
					$res[] = $data;
				}
			}
		}
	}
	if (empty($type) || ($type == 'themes')) {
		$themes = function_exists('wp_get_themes') ? wp_get_themes() : get_themes();
		//WARNING: Theme handling is not well coded by WordPress core
		$err = error_reporting(0);
		$ct = function_exists('wp_get_theme') ? wp_get_theme() : current_theme_info();
		error_reporting($err);
		foreach($themes as $key => $value) { 
			$data = csp_po_get_theme_capabilities($key, $value, $ct);
			if (!$data['gettext_ready']) continue;
			if ($do_compat_filter && !isset($data['dev-hints'])) continue;
			elseif ($do_security_filter && !isset($data['dev-security'])) continue;
			$res[] = $data;
		}	
	}
	return $res;
}

//////////////////////////////////////////////////////////////////////////////////////////
//	Admin Ajax Handler
//////////////////////////////////////////////////////////////////////////////////////////

if (function_exists('add_action')) {
	add_action('wp_ajax_csp_po_dlg_new', 'csp_po_ajax_handle_dlg_new');
	add_action('wp_ajax_csp_po_dlg_delete', 'csp_po_ajax_handle_dlg_delete');
	add_action('wp_ajax_csp_po_dlg_rescan', 'csp_po_ajax_handle_dlg_rescan');
	add_action('wp_ajax_csp_po_dlg_show_source', 'csp_po_ajax_handle_dlg_show_source');
	
	add_action('wp_ajax_csp_po_merge_from_maintheme', 'csp_po_ajax_handle_merge_from_maintheme');
	add_action('wp_ajax_csp_po_create', 'csp_po_ajax_handle_create');
	add_action('wp_ajax_csp_po_destroy', 'csp_po_ajax_handle_destroy');
	add_action('wp_ajax_csp_po_scan_source_file', 'csp_po_ajax_handle_scan_source_file');	
	add_action('wp_ajax_csp_po_change_low_memory_mode', 'csp_po_ajax_csp_po_change_low_memory_mode');
	add_action('wp_ajax_csp_po_change_translate_api', 'csp_po_ajax_change_translate_api');
	add_action('wp_ajax_csp_po_change_permission', 'csp_po_ajax_handle_change_permission');
	add_action('wp_ajax_csp_po_launch_editor', 'csp_po_ajax_handle_launch_editor');
	add_action('wp_ajax_csp_po_translate_by_google', 'csp_po_ajax_handle_translate_by_google');
	add_action('wp_ajax_csp_po_translate_by_microsoft', 'csp_po_ajax_handle_translate_by_microsoft');
	add_action('wp_ajax_csp_po_save_catalog_entry', 'csp_po_ajax_handle_save_catalog_entry');
	add_action('wp_ajax_csp_po_generate_mo_file', 'csp_po_ajax_handle_generate_mo_file');
	add_action('wp_ajax_csp_po_create_language_path', 'csp_po_ajax_handle_create_language_path');
	add_action('wp_ajax_csp_po_create_pot_indicator', 'csp_po_ajax_handle_create_pot_indicator');

	add_action('wp_ajax_csp_self_protection_result', 'csp_handle_csp_self_protection_result');
}

//WP 2.7 help extensions
//TODO: doesn't work as expected beginning at WP 3.0 (object now!) and never gets called while already object skipps filtering!
function csp_po_filter_screen_meta_screen($screen) {
	if (preg_match('/codestyling-localization$/', $screen)) return "codestyling-localization";
	return $screen;
}

//WP 2.7 help extensions
function csp_po_filter_help_list_filter($_wp_contextual_help) {

	global $wp_version;
	if (version_compare($wp_version, '3', '<')) {

		require_once(ABSPATH.'/wp-includes/rss.php');
		$rss = fetch_rss('http://www.code-styling.de/online-help/plugins.php?type=config&locale='.get_locale().'&plug=codestyling-localization');	
		if ( $rss ) {
			$_wp_contextual_help['codestyling-localization'] = '';
			foreach ($rss->items as $item ) {
				if ($item['category'] == 'thickbox') {
					$_wp_contextual_help['codestyling-localization'] .= '<a href="'. $item['link'] . '&amp;TB_iframe=true" class="thickbox" name="<strong>'. $item['title'] . '</strong>">'. $item['title'] . '</a> | ';
				} else {
					$_wp_contextual_help['codestyling-localization'] .= '<a target="_blank" href="'. $item['link'] . '" >'. $item['title'] . '</a> | ';
				}
			}
		}
		
	} else {
	
		//TODO: WP 3.0 introduces only accepts the new classes without depreciate, furthermore the screen key is handled different now (see function above!)
		require_once(ABSPATH.'/wp-includes/feed.php');
		$rss = fetch_feed('http://www.code-styling.de/online-help/plugins.php?type=config&locale='.get_locale().'&plug=codestyling-localization');
		if ( $rss && !is_wp_error($rss)) {
			$_wp_contextual_help['tools_page_codestyling-localization/codestyling-localization'] = '';
			foreach ($rss->get_items(0, 9999) as $item ) {		
				$cat = $item->get_category();
				if ($cat->get_term() == 'thickbox') {
					$_wp_contextual_help['tools_page_codestyling-localization/codestyling-localization'] .= '<a href="'. $item->get_link() . '&amp;TB_iframe=true" class="thickbox" name="<strong>'. $item->get_title() . '</strong>">'. $item->get_title() . '</a> | ';
				} else {
					$_wp_contextual_help['tools_page_codestyling-localization/codestyling-localization'] .= '<a target="_blank" href="'. $item->get_link() . '" >'. $item->get_title() . '</a> | ';
				}
			}
		}
		
	}
	return $_wp_contextual_help;
}

function csp_po_ajax_handle_dlg_new() {
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages');
	require_once('includes/locale-definitions.php');
?>
	<table class="widefat" cellspacing="2px">
		<tr>
			<td nowrap="nowrap"><strong><?php _e('Project-Id-Version',CSP_PO_TEXTDOMAIN); ?>:</strong></td>
			<td><?php echo strip_tags(rawurldecode($_POST['name'])); ?><input type="hidden" id="csp-dialog-name" value="<?php echo strip_tags(rawurldecode($_POST['name'])); ?>" /></td>
		</tr>
		<tr>
			<td><strong><?php _e('Creation-Date',CSP_PO_TEXTDOMAIN); ?>:</strong></td>
			<td><?php echo date("Y-m-d H:iO"); ?><input type="hidden" id="csp-dialog-timestamp" value="<?php echo date("Y-m-d H:iO"); ?>" /></td>
		</tr>
		<tr>
			<td style="vertical-align:middle;"><strong><?php _e('Last-Translator',CSP_PO_TEXTDOMAIN); ?>:</strong></td>
			<td><input style="width:330px;" type="text" id="csp-dialog-translator" value="<?php $myself = wp_get_current_user(); echo "$myself->user_nicename &lt;$myself->user_email&gt;"; ?>" /></td>
		</tr>
		<tr>
			<td valign="top"><strong><?php echo $csp_l10n_login_label[substr(get_locale(),0,2)]?>:</strong></td>
			<td>
				<div style="width:332px;height:300px; overflow:scroll;border:solid 1px #54585B;overflow-x:hidden;">
					<?php $existing = explode('|', ltrim($_POST['existing'],'|')); if(strlen($existing[0]) == 0) $existing=array(); ?>
					<input type="hidden" id="csp-dialog-row" value="<?php echo strip_tags($_POST['row']); ?>" />
					<input type="hidden" id="csp-dialog-numlangs" value="<?php echo count($existing)+1; ?>" />
					<input type="hidden" id="csp-dialog-language" value="" />
					<input type="hidden" id="csp-dialog-path" value="<?php echo strip_tags($_POST['path']); ?>" />
					<input type="hidden" id="csp-dialog-subpath" value="<?php echo strip_tags($_POST['subpath']); ?>" />
					<input type="hidden" id="csp-dialog-simplefilename" value="<?php echo strip_tags($_POST['simplefilename']); ?>" />			
					<input type="hidden" id="csp-dialog-transtemplate" value="<?php echo strip_tags($_POST['transtemplate']); ?>" />					
					<input type="hidden" id="csp-dialog-textdomain" value="<?php echo strip_tags($_POST['textdomain']); ?>" />					
					<input type="hidden" id="csp-dialog-denyscan" value="<?php echo ($_POST['denyscan'] ? "true" : "false"); ?>" />					
					<table style="font-family:monospace;">
					<?php
						$total = array_keys($csp_l10n_sys_locales);
						foreach($total as $key) {
							if (in_array($key, $existing)) continue;
							$values = $csp_l10n_sys_locales[$key];
							if (get_locale() == $key) { $selected = '" selected="selected'; } else { $selected=""; };
							?>
							<tr>
								<td><input type="radio" name="mo-locale" value="<?php echo $key; ?><?php echo $selected; ?>" onclick="$('submit_language').enable();$('csp-dialog-language').value = this.value;" /></td>
								<td><img alt="" title="locale: <?php echo $key ?>" src="<?php echo CSP_PO_BASE_URL."/images/flags/".$csp_l10n_sys_locales[$key]['country-www'].".gif\""; ?>" /></td>
								<td><?php echo $key; ?></td>
								<td style="padding-left: 5px;border-left: 1px solid #aaa;"><?php echo $values['lang-native']."<br />"; ?></td>
							</tr>
							<?php
						}
					?>
					</table>
				</div>
			</td>
		</tr>
	</table>
	<div style="text-align:center; padding-top: 10px"><input class="button" id="submit_language" type="submit" disabled="disabled" value="<?php _e('create po-file',CSP_PO_TEXTDOMAIN); ?>" onclick="return csp_create_new_pofile(this,<?php echo "'".strip_tags($_POST['type'])."'"; ?>);"/></div>
<?php
exit();
}

function csp_po_ajax_handle_dlg_delete() {
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages');
	require_once('includes/locale-definitions.php');
	$lang = isset($csp_l10n_sys_locales[$_POST['language']]) ? $csp_l10n_sys_locales[$_POST['language']]['lang-native'] : $_POST['language'];
?>
	<p style="text-align:center;"><?php echo sprintf(__('You are about to delete <strong>%s</strong> from "<strong>%s</strong>" permanently.<br/>Are you sure you wish to delete these files?', CSP_PO_TEXTDOMAIN), $lang, strip_tags(rawurldecode($_POST['name']))); ?></p>
	<div style="text-align:center; padding-top: 10px"><input class="button" id="submit_language" type="submit" value="<?php _e('delete files',CSP_PO_TEXTDOMAIN); ?>" onclick="csp_destroy_files(this,'<?php echo str_replace("'", "\\'", strip_tags(rawurldecode($_POST['name'])))."','".strip_tags($_POST['row'])."','".strip_tags($_POST['path'])."','".strip_tags($_POST['subpath'])."','".strip_tags($_POST['language'])."','".strip_tags($_POST['numlangs']);?>');" /></div>
<?php
	exit();
}

function csp_po_ajax_handle_dlg_rescan() {
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages');
	require_once('includes/locale-definitions.php');	
	global $wp_version;
	if ($_POST['type'] == 'wordpress') {	
		$abs_root = rtrim(str_replace('\\', '/', ABSPATH), '/');
		$excludes = array();
		$files = array(
			$abs_root.'/wp-activate.php',
			$abs_root.'/wp-app.php',
			$abs_root.'/wp-atom.php',
			$abs_root.'/wp-blog-header.php',
			$abs_root.'/wp-comments-post.php',
			$abs_root.'/wp-commentsrss2.php',
			$abs_root.'/wp-cron.php',
			$abs_root.'/wp-feed.php',
			$abs_root.'/wp-links-opml.php',
			$abs_root.'/wp-load.php',
			$abs_root.'/wp-login.php',
			$abs_root.'/wp-mail.php',
			$abs_root.'/wp-pass.php',
			$abs_root.'/wp-rdf.php',
			$abs_root.'/wp-register.php',
			$abs_root.'/wp-rss.php',
			$abs_root.'/wp-rss2.php',
			$abs_root.'/wp-settings.php',
			$abs_root.'/wp-signup.php',
			$abs_root.'/wp-trackback.php',
			$abs_root.'/xmlrpc.php',
			str_replace("\\", "/", WP_PLUGIN_DIR).'/akismet/akismet.php'
		);
		rscandir_php($abs_root.'/wp-admin/', $excludes, $files);
		rscandir_php($abs_root.'/wp-includes/', $excludes, $files);
		//do not longer rescan old themes prior hosted the the main localization file starting from WP 3.0!
		if (version_compare($wp_version, '3', '<')) {
			rscandir_php(str_replace("\\","/",WP_CONTENT_DIR)."/themes/default/", $excludes, $files);
			rscandir_php(str_replace("\\","/",WP_CONTENT_DIR)."/themes/classic/", $excludes, $files);
		}	
	}
	elseif ($_POST['type'] == 'plugins_mu') {
		$files[] = strip_tags($_POST['simplefilename']);
	}
	elseif ($_POST['textdomain'] == 'buddypress') {
		$files = array();
		$excludes = array(strip_tags($_POST['path']).'bp-forums/bbpress');
		rscandir_php(strip_tags($_POST['path']), $excludes, $files);
	}
	else{
		$files = array();
		$excludes = array();
		if (isset($_POST['simplefilename']) && !empty($_POST['simplefilename'])) { $files[] = strip_tags($_POST['simplefilename']); }
		else { rscandir_php(strip_tags($_POST['path']), $excludes, $files); }
		if ($_POST['type'] == 'themes' && isset($_POST['themetemplate']) && !empty($_POST['themetemplate'])) {
			rscandir_php(str_replace("\\","/",WP_CONTENT_DIR).'/themes/'.strip_tags($_POST['themetemplate']).'/',$excludes, $files);
		}
	}
	$country_www = isset($csp_l10n_sys_locales[$_POST['language']]) ? $csp_l10n_sys_locales[$_POST['language']]['country-www'] : 'unknown';
	$lang_native = isset($csp_l10n_sys_locales[$_POST['language']]) ? $csp_l10n_sys_locales[$_POST['language']]['lang-native'] : $_POST['language'];
	$filename = strip_tags($_POST['path'].$_POST['subpath'].$_POST['language']).".po";
?>	
	<input id="csp-dialog-source-file-json" type="hidden" value="{ <?php 
		echo "name: '".strip_tags($_POST['name'])."',";
		echo "row: '".strip_tags($_POST['row'])."',";
		echo "language: '".strip_tags($_POST['language'])."',";
		echo "textdomain: '".strip_tags($_POST['textdomain'])."',";
		echo "next : 0,";
		echo "path : '".strip_tags($_POST['path'])."',";
		echo "pofile : '".strip_tags($_POST['path'].$_POST['subpath'].$_POST['language']).".po',";
		echo "type : '".strip_tags($_POST['type'])."',";
		echo "files : ['".implode("','",$files)."']"
	?>}" />
	<table class="widefat" cellspacing="2px">
		<tr>
			<td nowrap="nowrap"><strong><?php _e('Project-Id-Version',CSP_PO_TEXTDOMAIN); ?>:</strong></td>
			<td colspan="2"><?php echo strip_tags(rawurldecode($_POST['name'])); ?><input type="hidden" name="name" value="<?php echo strip_tags(rawurldecode($_POST['name'])); ?>" /></td>
		</tr>
		<tr>
			<td nowrap="nowrap"><strong><?php _e('Language Target',CSP_PO_TEXTDOMAIN); ?>:</strong></td>
			<td><img alt="" title="locale: <?php echo strip_tags($_POST['language']); ?>" src="<?php echo CSP_PO_BASE_URL."/images/flags/".$country_www.".gif\""; ?>" /></td>			
			<td><?php echo $lang_native; ?></td>
		</tr>	
		<tr>
			<td nowrap="nowrap"><strong><?php _e('Affected Total Files',CSP_PO_TEXTDOMAIN); ?>:</strong></td>
			<td nowrap="nowrap" align="right"><?php echo count($files); ?></td>
			<td><em><?php echo "/".str_replace(str_replace("\\",'/',ABSPATH), '', strip_tags($_POST['path'])); ?></em></td>
		</tr>
		<tr>
			<td nowrap="nowrap" valign="top"><strong><?php _e('Scanning Progress',CSP_PO_TEXTDOMAIN); ?>:</strong></td>
			<td id="csp-dialog-progressvalue" nowrap="nowrap" valign="top" align="right">0</td>
			<td>
				<div style="height:13px;width:290px;border:solid 1px #333;"><div id="csp-dialog-progressbar" style="height: 13px;width:0%; background-color:#0073D9"></div></div>
				<div id="csp-dialog-progressfile" style="width:290px;white-space:nowrap;overflow:hidden;font-size:8px;font-family:monospace;padding-top:3px;">&nbsp;</div>
			</td>
		<tr>
	</table>
	<div style="text-align:center; padding-top: 10px"><input class="button" id="csp-dialog-rescan" type="submit" value="<?php _e('scan now',CSP_PO_TEXTDOMAIN); ?>" onclick="csp_scan_source_files(this);"/><span id="csp-dialog-scan-info" style="display:none"><?php _e('Please standby, files presently being scanned ...',CSP_PO_TEXTDOMAIN); ?></span></div>
<?php
	exit();
}

function csp_po_convert_js_input_for_source($str) {
	$search = array('\\\\\"','\\\\n', '\\\\t', '\\\\$','\\0', "\\'", '\\\\');
	$replace = array('"', "\n", "\\t", "\\$", "\0", "'", "\\");
	$str = str_replace( $search, $replace, $str );
	return $str;
}

function csp_po_ajax_handle_dlg_show_source() {
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages');
	list($file, $match_line) = explode(':', $_POST['file']);
	$l = filesize(strip_tags($_POST['path']).$file);
	$handle = fopen(strip_tags($_POST['path']).$file,'rb');
	$content = str_replace(array("\r","\\$"),array('','$'), fread($handle, $l));
	fclose($handle);

	$msgid = $_POST['msgid'];
	$msgid = csp_po_convert_js_input_for_source($msgid);	
	if (strlen($msgid) > 0) {
		if (strpos($msgid, "\00") > 0)
			$msgid = explode("\00", $msgid);
		else
			$msgid = explode("\01", $msgid); //opera fix
		foreach($msgid as $item) {	
			if (strpos($content, $item) === false) {
				//difficult to separate between real \n notation and LF brocken strings also \t 
				$test = str_replace("\n", '\n', $item);
				if (strpos($content, $test) === false) {
					$test2 = str_replace('\t', "\t", $item);
					if (strpos($content, $test2) === false) {
						$test2 = str_replace('\t', "\t", $test);
						if (strpos($content, $test2) === true) {
							$item = $test2;
						}
					}else{
						$item = $test2;
					}
				}else {
					$item = $test;
				}
			}
			$content = str_replace($item, "\1".$item."\2", $content);
		}
	}
	$tmp = htmlentities($content, ENT_COMPAT, 'UTF-8');
	if (empty($tmp)) $tmp = htmlentities($content, ENT_COMPAT);
	$content = str_replace("\t","&nbsp;&nbsp;&nbsp;&nbsp;",$tmp);
	$content = preg_split("/\n/", $content);
	$c=0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head>
<body style="margin:0; padding:0;font-family:monospace;font-size:13px;">
	<table id="php_source" cellspacing="0" width="100%" style="padding:0; margin:0;">
<?php	
	$open = 0;
	$closed = 0;
	foreach($content as $line) {
		$c++;
		$style = $c % 2 == 1 ? "#fff" : "#eee";
		
		$open += substr_count($line,"\1");
		$closed += substr_count($line,"\2");
		$contained = preg_match("/(\1|\2)/", $line) || ($c == $match_line) || ($open != $closed);
		if ($contained) $style="#FFEF3F";
		
		if (!preg_match("/(\1|\2)/", $line) && $contained) $line = "<span style='background-color:#f00; color:#fff;padding:0 3px;'>".$line."</span>";
		if((substr_count($line,"\1") < substr_count($line,"\2")) && ($open == $closed)) $line = "<span style='background-color:#f00; color:#fff;padding:0 3px;'>".$line;
		if(substr_count($line,"\1") > substr_count($line,"\2")) $line .= "</span>";
		$line = str_replace("\1", "<span style='background-color:#f00; color:#fff;padding:0 3px;'>", $line);
		$line = str_replace("\2", "</span>", $line);
		
		echo "<tr id=\"l-$c\" style=\"background-color:$style;\"><td align=\"right\" style=\"background-color:#888;padding-right:5px;\">$c</td><td nowrap=\"nowrap\" style=\"padding-left:5px;\">$line</td></tr>\n";
	}
?>
	</table>
	<script type="text/javascript">
	/* <![CDATA[ */
function init() {
	try{
		window.scrollTo(0,document.getElementById('l-'+<?php echo max($match_line-15,1); ?>).offsetTop);
	}catch(e) {
		//silently kill errors if *.po files line numbers comes out of an outdated file and exceed the line range
	}
}
	
if (typeof Event == 'undefined') Event = new Object();
Event.domReady = {
	add: function(fn) {
		//-----------------------------------------------------------
		// Already loaded?
		//-----------------------------------------------------------
		if (Event.domReady.loaded) return fn();

		//-----------------------------------------------------------
		// Observers
		//-----------------------------------------------------------
	
		var observers = Event.domReady.observers;
		if (!observers) observers = Event.domReady.observers = [];
		// Array#push is not supported by Mac IE 5
		observers[observers.length] = fn;
 
		//-----------------------------------------------------------
		// domReady function
		//-----------------------------------------------------------
		if (Event.domReady.callback) return;
		Event.domReady.callback = function() {
			if (Event.domReady.loaded) return;
			Event.domReady.loaded = true;
			if (Event.domReady.timer) {
				clearInterval(Event.domReady.timer);
				Event.domReady.timer = null;
			}

			var observers = Event.domReady.observers;
			for (var i = 0, length = observers.length; i < length; i++) {
				var fn = observers[i];
				observers[i] = null;
				fn(); // make 'this' as window
			}
			Event.domReady.callback = Event.domReady.observers = null;
		};

		//-----------------------------------------------------------
		// Emulates 'onDOMContentLoaded'
		//-----------------------------------------------------------
		var ie = !!(window.attachEvent && !window.opera);
		var webkit = navigator.userAgent.indexOf('AppleWebKit/') > -1;
 
		if (document.readyState && webkit) {
 
			// Apple WebKit (Safari, OmniWeb, ...)
			Event.domReady.timer = setInterval(function() {
				var state = document.readyState;
				if (state == 'loaded' || state == 'complete') {
					Event.domReady.callback();
				}
			}, 50);
 
		} else if (document.readyState && ie) {
 
			// Windows IE
			var src = (window.location.protocol == 'https:') ? '://0' : 'javascript:void(0)';
			document.write(
				'<script type="text/javascript" defer="defer" src="' + src + '" ' +
				'onreadystatechange="if (this.readyState == \'complete\') Event.domReady.callback();"' +
				'><\/script>');
 
		} else {
 
			if (window.addEventListener) {
				// for Mozilla browsers, Opera 9
				document.addEventListener("DOMContentLoaded", Event.domReady.callback, false);
				// Fail safe
				window.addEventListener("load", Event.domReady.callback, false);
			} else if (window.attachEvent) {
				window.attachEvent('onload', Event.domReady.callback);
			} else {
				// Legacy browsers (e.g. Mac IE 5)
				var fn = window.onload;
				window.onload = function() {
					Event.domReady.callback();
					if (fn) fn();
				}
			}
		}
	}
}	
	Event.domReady.add(init);
	/* ]]> */
	</script>	
</body>
</html>
<?php
	exit();
}

function csp_po_ajax_handle_merge_from_maintheme() {
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages');
	require_once('includes/locale-definitions.php');
	require_once('includes/class-filesystem-translationfile.php');
	
	//source|dest|basepath|textdomain|molist
	$tmp = array();
	$files = rscandir(str_replace("\\","/",WP_CONTENT_DIR).'/themes/'.strip_tags($_POST['source']).'/', "/(\.po|\.mo)$/", $tmp);
	foreach($files as $file) {
		$pofile = new CspFileSystem_TranslationFile();
		$target = strip_tags($_POST['basepath']).basename($file);
		if(preg_match('/\.mo/', $file)) {
			$pofile->read_mofile($file, $csp_l10n_plurals, false, strip_tags($_POST['textdomain']));
			$pofile->write_mofile($target, strip_tags($_POST['textdomain']));
		}else{
			$pofile->read_pofile($file);
			if (file_exists($target)) {
				//merge it now
				$pofile->read_pofile($target);
			}
			$pofile->write_pofile($target, true, strip_tags($_POST['textdomain']));
		}
	}
	exit();
}

function csp_po_ajax_handle_create() {
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages');
	require_once('includes/locale-definitions.php');
	require_once('includes/class-filesystem-translationfile.php');
	
	$pofile = new CspFileSystem_TranslationFile();
	$filename = strip_tags($_POST['path'].$_POST['subpath'].$_POST['language']).'.po';
	$pofile->new_pofile(
		$filename, 
		strip_tags($_POST['subpath']),
		strip_tags($_POST['name']), 
		strip_tags($_POST['timestamp']), 
		$_POST['translator'], 
		$csp_l10n_plurals[substr($_POST['language'],0,2)], 
		$csp_l10n_sys_locales[$_POST['language']]['lang'], 
		$csp_l10n_sys_locales[$_POST['language']]['country']
	);
	if(!$pofile->write_pofile($filename)) {
		header('Status: 404 Not Found');
		header('HTTP/1.1 404 Not Found');
		echo sprintf(__("You do not have the permission to create the file '%s'.", CSP_PO_TEXTDOMAIN), $filename);
	}
	else{	
		header('Content-Type: application/json');
?>
{
		name: '<?php echo strip_tags(rawurldecode($_POST['name'])); ?>',
		row : '<?php echo strip_tags($_POST['row']); ?>',
		head: '<?php echo sprintf(_n('<strong>%d</strong> Language', '<strong>%d</strong> Languages',(int)$_POST['numlangs'],CSP_PO_TEXTDOMAIN), $_POST['numlangs']); ?>',
		path: '<?php echo strip_tags($_POST['path']); ?>',
		subpath: '<?php echo strip_tags($_POST['subpath']); ?>',
		language: '<?php echo strip_tags($_POST['language']); ?>',
		lang_native: '<?php echo $csp_l10n_sys_locales[strip_tags($_POST['language'])]['lang-native']; ?>',
		image: '<?php echo CSP_PO_BASE_URL."/images/flags/".$csp_l10n_sys_locales[strip_tags($_POST['language'])]['country-www'].".gif";?>',
		type: '<?php echo strip_tags($_POST['type']); ?>',
		simplefilename: '<?php echo strip_tags($_POST['simplefilename']); ?>',
		transtemplate: '<?php echo strip_tags($_POST['transtemplate']); ?>',
		permissions: '<?php echo date(__('m/d/Y H:i:s',CSP_PO_TEXTDOMAIN), filemtime($filename))." ".file_permissions($filename); ?>',
		denyscan: <?php echo strip_tags($_POST['denyscan']); ?>,
		google: "<?php echo $csp_l10n_sys_locales[$_POST['language']]['google-api'] ? 'yes' : 'no'; ?>",
		microsoft: "<?php echo $csp_l10n_sys_locales[$_POST['language']]['microsoft-api'] ? 'yes' : 'no'; ?>"
}
<?php		
	}
	exit();
}

function csp_po_ajax_handle_destroy() {
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages');
	$pofile = strip_tags($_POST['path'].$_POST['subpath'].$_POST['language']).'.po';
	$mofile = strip_tags($_POST['path'].$_POST['subpath'].$_POST['language']).'.mo';
	$error = false;
	
	require_once('includes/class-filesystem-translationfile.php');
	$transfile = new CspFileSystem_TranslationFile();
	
	$transfile->destroy_pofile($pofile);
	$transfile->destroy_mofile($mofile);
	
	$num = (int)$_POST['numlangs'] - 1;
	header('Content-Type: application/json');
?>
{
	row : '<?php echo strip_tags($_POST['row']); ?>',
	head: '<?php echo sprintf(_n('<strong>%d</strong> Language', '<strong>%d</strong> Languages',$num,CSP_PO_TEXTDOMAIN), $num); ?>',
	language: '<?php echo strip_tags($_POST['language']); ?>'
}
<?php	
	exit();
}
function csp_po_ajax_csp_po_change_low_memory_mode() {
	csp_po_check_security();
	update_option('codestyling-localization.low-memory', ($_POST['mode'] == 'true' ? true : false));
	exit();
}

function csp_po_ajax_change_translate_api() {
	csp_po_check_security();
	$api_type = 'none';
	if (in_array($_POST['api_type'], array('google','microsoft'))) {
		$api_type = $_POST['api_type'];
	}
	update_option('codestyling-localization.translate-api', $api_type);
	exit();
}

function csp_po_ajax_handle_scan_source_file() {
	csp_po_check_security();

	$low_mem_scanning = (bool)get_option('codestyling-localization.low-memory', false);
	
	require_once('includes/class-filesystem-translationfile.php');
	require_once('includes/locale-definitions.php');
	$textdomain = $_POST['textdomain'];
	//TODO: give the domain into translation file as default domain
	$pofile = new CspFileSystem_TranslationFile($_POST['type']);
	//BUGFIX: 1.90 - may be, we have only the mo but no po, so we dump it out as base po file first
	if (!file_exists($_POST['pofile'])) {
		//try implicite convert first and reopen as po second
		if($pofile->read_mofile(substr($_POST['pofile'],0,-2)."mo", $csp_l10n_plurals, false, $textdomain)) {
			$pofile->write_pofile($_POST['pofile'],false,false, ($_POST['type'] == 'wordpress' ? 'no' : 'yes'));
		}
		//check, if we have to reverse all the other *.mo's too
		if($_POST['type'] == 'wordpress') {
			$root_po = basename($_POST['pofile']);
			$root_mo = substr($root_po,0,-2)."mo";
			$part = str_replace($root_po, '', $_POST['pofile']);
			if($pofile->read_mofile($part.'continents-cities-'.$root_mo, $csp_l10n_plurals, $part.'continents-cities-'.$root_mo, $_POST['textdomain'])) {
				$pofile->write_pofile($part.'continents-cities-'.$root_po,false,false,'no');
			}
			if($pofile->read_mofile($part.'ms-'.$root_mo, $csp_l10n_plurals, $part.'ms-'.$root_mo, $_POST['textdomain'])) {		
				$pofile->write_pofile($part.'ms-'.$root_po,false,false,'no');
			}
			global $wp_version;			
			if (version_compare($wp_version, '3.4-alpha', ">=")) {
				if($pofile->read_mofile($part.'admin-'.$root_mo, $csp_l10n_plurals, $part.'admin-'.$root_mo, $_POST['textdomain'])) {
					$pofile->write_pofile($part.'admin-'.$root_po,false,false,'no');
				}
				if($pofile->read_mofile($part.'admin-network-'.$root_mo, $csp_l10n_plurals, $part.'admin-network-'.$root_mo, $_POST['textdomain'])) {
					$pofile->write_pofile($part.'admin-network-'.$root_po,false,false,'no');
				}
			}
		}
	}		
	$pofile = new CspFileSystem_TranslationFile($_POST['type']);
	if ($pofile->read_pofile($_POST['pofile'])) {
		if ((int)$_POST['num'] == 0) { 
		
			if (!$pofile->supports_textdomain_extension() && $_POST['type'] == 'wordpress'){
				//try to merge up first all splitted translations.
				$root = basename($_POST['pofile']);
				$part = str_replace($root, '', $_POST['pofile']);
				//load existing files for backward compatibility if existing
				$pofile->read_pofile($part.'continents-cities-'.$root, $csp_l10n_plurals, $part.'continents-cities-'.$root);
				$pofile->read_pofile($part.'ms-'.$root, $csp_l10n_plurals, $part.'ms-'.$root);
				global $wp_version;			
				if (version_compare($wp_version, '3.4-alpha', ">=")) {
					$pofile->read_pofile($part.'admin-'.$root, $csp_l10n_plurals, $part.'admin-'.$root);
					$pofile->read_pofile($part.'admin-network-'.$root, $csp_l10n_plurals, $part.'admin-network-'.$root);
				}
				//again read it to get the right header overwritten last
				$pofile->read_pofile($_POST['pofile']);
				//overwrite with full imploded sparse file contents now
				$pofile->write_pofile($_POST['pofile'],false,false,'no');
			}		
		
			$pofile->parsing_init(); 
		}
		
		$php_files = explode("|", $_POST['php']);
		$s = (int)$_POST['num'];
		$e = min($s + (int)$_POST['cnt'], count($php_files));
		$last = ($e >= count($php_files));
		for ($i=$s; $i<$e; $i++) {
			if ($low_mem_scanning) {
				$options = array(
					'type' => $_POST['type'],
					'path' => $_POST['path'],
					'textdomain' => $_POST['textdomain'],
					'file' => $php_files[$i]
				);
				$r = wp_remote_post(CSP_PO_BASE_URL.'/includes/low-memory-parsing.php', array('body' => $options));
				$data = unserialize(base64_decode($r['body']));
				$pofile->add_messages($data);
			}else{
				$pofile->parsing_add_messages($_POST['path'], $php_files[$i], $textdomain);
			}
		}	
		if ($last) { $pofile->parsing_finalize($textdomain, strip_tags(rawurldecode($_POST['name']))); }
		if ($pofile->write_pofile($_POST['pofile'], $last)) {
			header('Content-Type: application/json');
			echo '{ title: "'.date(__('m/d/Y H:i:s',CSP_PO_TEXTDOMAIN), filemtime($_POST['pofile']))." ".file_permissions($_POST['pofile']).'" }';
		}
		else{
			header('Status: 404 Not Found');
			header('HTTP/1.1 404 Not Found');
			echo sprintf(__("You do not have the permission to write to the file '%s'.", CSP_PO_TEXTDOMAIN), $_POST['pofile']);
		}
	}
	else{
		header('Status: 404 Not Found');
		header('HTTP/1.1 404 Not Found');
		echo sprintf(__("You do not have the permission to read the file '%s'.", CSP_PO_TEXTDOMAIN), $_POST['pofile']);
	}
	exit();
}

function csp_po_ajax_handle_change_permission() {
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages');
	$filename = strip_tags($_POST['file']);
	$error = false;
	
	require_once('includes/class-filesystem-translationfile.php');
	$transfile = new CspFileSystem_TranslationFile();
	
	$transfile->change_permission($filename);

	header('Content-Type: application/json');
	echo '{ title: "'.date(__('m/d/Y H:i:s',CSP_PO_TEXTDOMAIN), filemtime($filename))." ".file_permissions($filename).'" }';
	exit();
}

function csp_po_ajax_handle_launch_editor() {
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages');
	require_once('includes/locale-definitions.php');
//	require_once('includes/class-translationfile.php');
	require_once('includes/class-filesystem-translationfile.php');
	$f = new CspFileSystem_TranslationFile($_POST['type']);
	if (!file_exists($_POST['basepath'].$_POST['file'])) {
		//try implicite convert first
		if($f->read_mofile(substr($_POST['basepath'].$_POST['file'],0,-2)."mo", $csp_l10n_plurals, $_POST['file'], $_POST['textdomain'])) {
			$f->write_pofile($_POST['basepath'].$_POST['file'],false,false,'no');
		}
		//check, if we have to reverse all the other *.mo's too
		if($_POST['type'] == 'wordpress') {
			$root_po = basename($_POST['file']);
			$root_mo = substr($root_po,0,-2)."mo";
			$part = str_replace($root_po, '', $_POST['file']);
			if($f->read_mofile($_POST['basepath'].$part.'continents-cities-'.$root_mo, $csp_l10n_plurals, $part.'continents-cities-'.$root_mo, $_POST['textdomain'])) {
				$f->write_pofile($_POST['basepath'].$part.'continents-cities-'.$root_po,false,false,'no');
			}
			if($f->read_mofile($_POST['basepath'].$part.'ms-'.$root_mo, $csp_l10n_plurals, $part.'ms-'.$root_mo, $_POST['textdomain'])) {		
				$f->write_pofile($_POST['basepath'].$part.'ms-'.$root_po,false,false,'no');
			}
			global $wp_version;			
			if (version_compare($wp_version, '3.4-alpha', ">=")) {
				if($f->read_mofile($_POST['basepath'].$part.'admin-'.$root_mo, $csp_l10n_plurals, $part.'admin-'.$root_mo, $_POST['textdomain'])) {
					$f->write_pofile($_POST['basepath'].$part.'admin-'.$root_po,false,false,'no');
				}
				if($f->read_mofile($_POST['basepath'].$part.'admin-network-'.$root_mo, $csp_l10n_plurals, $part.'admin-network-'.$root_mo, $_POST['textdomain'])) {
					$f->write_pofile($_POST['basepath'].$part.'admin-network-'.$root_po,false,false,'no');
				}
			}
		}
	}
	$f = new CspFileSystem_TranslationFile($_POST['type']);
	$f->read_pofile($_POST['basepath'].$_POST['file'], $csp_l10n_plurals, $_POST['file']);
	if (!$f->supports_textdomain_extension() && $_POST['type'] == 'wordpress'){
		//try to merge up first all splitted translations.
		$root = basename($_POST['file']);
		$part = str_replace($root, '', $_POST['file']);
		//load existing files for backward compatibility if existing
		$f->read_pofile($_POST['basepath'].$part.'continents-cities-'.$root, $csp_l10n_plurals, $part.'continents-cities-'.$root);
		$f->read_pofile($_POST['basepath'].$part.'ms-'.$root, $csp_l10n_plurals, $part.'ms-'.$root);
		global $wp_version;			
		if (version_compare($wp_version, '3.4-alpha', ">=")) {
			$f->read_pofile($_POST['basepath'].$part.'admin-'.$root, $csp_l10n_plurals, $part.'admin-'.$root);
			$f->read_pofile($_POST['basepath'].$part.'admin-network-'.$root, $csp_l10n_plurals, $part.'admin-network-'.$root);
		}
		//again read it to get the right header overwritten last
		$f->read_pofile($_POST['basepath'].$_POST['file'], $csp_l10n_plurals, $_POST['file']);
		//overwrite with full imploded sparse file contents now
		$f->write_pofile($_POST['basepath'].$_POST['file'],false,false,'no');
	}
	if ($f->supports_textdomain_extension() || $_POST['type'] == 'wordpress'){
		if (!defined('TRANSLATION_API_PER_USER_DONE')) csp_po_init_per_user_trans();
		$f->echo_as_json($_POST['basepath'], $_POST['file'], $csp_l10n_sys_locales, csp_get_translate_api_type());
	}else {
		header('Status: 404 Not Found');
		header('HTTP/1.1 404 Not Found');
		_e("Your translation file doesn't support the <em>multiple textdomains in one translation file</em> extension.<br/>Please re-scan the related source files at the overview page to enable this feature.",CSP_PO_TEXTDOMAIN);
		?>&nbsp;<a align="left" class="question-help" href="javascript:void(0);" title="<?php _e("What does that mean?",CSP_PO_TEXTDOMAIN) ?>" rel="translationformat"><img src="<?php echo CSP_PO_BASE_URL."/images/question.gif"; ?>" /></a><?php
	}
	exit();
}

function csp_po_ajax_handle_translate_by_google() {
	csp_po_check_security();
	if (!defined('TRANSLATION_API_PER_USER_DONE')) csp_po_init_per_user_trans();
	// reference documentation: http://code.google.com/intl/de-DE/apis/ajaxlanguage/documentation/reference.html
	// example API v1 - 'http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&q=hello%20world&langpair=en%7Cit'
	// example API v2 - [ GET https://www.googleapis.com/language/translate/v2?key=INSERT-YOUR-KEY&source=en&target=de&q=Hello%20world ]
	$msgid = $_POST['msgid'];
	$search = array('\\\\\\\"', '\\\\\"','\\\\n', '\\\\r', '\\\\t', '\\\\$','\\0', "\\'", '\\\\');
	$replace = array('\"', '"', "\n", "\r", "\\t", "\\$", "\0", "'", "\\");
	$msgid = str_replace( $search, $replace, $msgid );
	add_filter('https_ssl_verify', '__return_false');
	//OLD: $res = csp_fetch_remote_content("http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&format=html&q=".urlencode($msgid)."&langpair=en%7C".$_POST['destlang']);
	$res = csp_fetch_remote_content("https://www.googleapis.com/language/translate/v2?key=".(defined('GOOGLE_TRANSLATE_KEY') ? GOOGLE_TRANSLATE_KEY : '')."&source=en&target=".$_POST['destlang']."&q=".urlencode($msgid));
	if ($res) {
		header('Content-Type: application/json');
		echo $res;
	}
	else{
		header('Status: 404 Not Found');
		header('HTTP/1.1 404 Not Found');
		load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages');
		_e("Sorry, Google Translation is not available.", CSP_PO_TEXTDOMAIN);	
	}
	exit();
}

function csp_po_ajax_handle_translate_by_microsoft() {
	csp_po_check_security();
	if (!defined('TRANSLATION_API_PER_USER_DONE')) csp_po_init_per_user_trans();
	$msgid = $_POST['msgid'];
	$search = array('\\\\\\\"', '\\\\\"','\\\\n', '\\\\r', '\\\\t', '\\\\$','\\0', "\\'", '\\\\');
	$replace = array('\"', '"', "\n", "\r", "\\t", "\\$", "\0", "'", "\\");
	$msgid = str_replace( $search, $replace, $msgid );	
	
	require_once('includes/translation-api-microsoft.php');
	header('Content-Type: text/plain');
	try {
		//Client ID of the application.
		$clientID     = defined('MICROSOFT_TRANSLATE_CLIENT_ID') ? MICROSOFT_TRANSLATE_CLIENT_ID : '';
		//Client Secret key of the application.
		$clientSecret = defined('MICROSOFT_TRANSLATE_CLIENT_SECRET') ? MICROSOFT_TRANSLATE_CLIENT_SECRET : '';
		//OAuth Url.
		$authUrl      = "https://datamarket.accesscontrol.windows.net/v2/OAuth2-13/";
		//Application Scope Url
		$scopeUrl     = "http://api.microsofttranslator.com";
		//Application grant type
		$grantType    = "client_credentials";

		//Create the AccessTokenAuthentication object.
		$authObj      = new AccessTokenAuthentication();
		//Get the Access token.
		$accessToken  = $authObj->getTokens($grantType, $scopeUrl, $clientID, $clientSecret, $authUrl);
		//Create the authorization Header string.
		$authHeader = "Authorization: Bearer ". $accessToken;

		//Set the params.//
		$fromLanguage = "en";
		$toLanguage   = strip_tags($_POST['destlang']);
		$inputStr     = $msgid;
		$contentType  = 'text/plain';
		$category     = 'general';
		
		$params = "text=".urlencode($inputStr)."&to=".$toLanguage."&from=".$fromLanguage;
		$translateUrl = "http://api.microsofttranslator.com/v2/Http.svc/Translate?$params";
		
		//Create the Translator Object.
		$translatorObj = new HTTPTranslator();
		
		//Get the curlResponse.
		$curlResponse = $translatorObj->curlRequest($translateUrl, $authHeader);
		
		//Interprets a string of XML into an object.
		$xmlObj = simplexml_load_string($curlResponse);
		foreach((array)$xmlObj[0] as $val){
			$translatedStr = $val;
		}
		echo $translatedStr;
	} catch(Exception $e) {
		header('Status: 404 Not Found');
		header('HTTP/1.1 404 Not Found');
		echo $e->getMessage();
	}	

	exit();
}

function csp_po_ajax_handle_save_catalog_entry() {
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages');
//	require_once('includes/class-translationfile.php');
	require_once('includes/class-filesystem-translationfile.php');
	$f = new CspFileSystem_TranslationFile();
	//opera bugfix: replace embedded \1 with \0 because Opera can't send embeded 0
	$_POST['msgid'] = str_replace("\1", "\0", $_POST['msgid']);
	$_POST['msgstr'] = str_replace("\1", "\0", $_POST['msgstr']);
	if ($f->read_pofile($_POST['path'].$_POST['file'])) {
		if (!$f->update_entry($_POST['msgid'], $_POST['msgstr'])) {
			header('Status: 404 Not Found');
			header('HTTP/1.1 404 Not Found');
			echo sprintf(__("You do not have the permission to write to the file '%s'.", CSP_PO_TEXTDOMAIN), $_POST['file']);
		}
		else{
			$f->write_pofile($_POST['path'].$_POST['file']);
			header('Status: 200 Ok');
			header('HTTP/1.1 200 Ok');
			header('Content-Length: 1');	
			echo "0";
		}
	}
	else{
		header('Status: 404 Not Found');
		header('HTTP/1.1 404 Not Found');
		echo sprintf(__("You do not have the permission to read the file '%s'.", CSP_PO_TEXTDOMAIN), $_POST['file']);
	}
	exit();
}

function csp_po_ajax_handle_generate_mo_file(){
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages');
//	require_once('includes/class-translationfile.php');
	require_once('includes/class-filesystem-translationfile.php');
	$pofile = (string)$_POST['pofile'];
	$textdomain = (string)$_POST['textdomain'];
	$f = new CspFileSystem_TranslationFile();
	if (!$f->read_pofile($pofile)) {
		header('Status: 404 Not Found');
		header('HTTP/1.1 404 Not Found');
		echo sprintf(__("You do not have the permission to read the file '%s'.", CSP_PO_TEXTDOMAIN), $pofile);
		exit();
	}
	//lets detected, what we are about to be writing:
	$mo = substr($pofile,0,-2).'mo';

	$wp_dir = str_replace("\\","/",WP_LANG_DIR);
	$pl_dir = str_replace("\\","/",WP_PLUGIN_DIR);
	$plm_dir = str_replace("\\","/",WPMU_PLUGIN_DIR);
	$parts = pathinfo($mo);
	//dirname|basename|extension
	if (preg_match("|^".$wp_dir."|", $mo)) {
		//we are WordPress itself
		if ($textdomain != 'default') {
			$mo	= $parts['dirname'].'/'.$textdomain.'-'.$parts['basename'];
		}
	}elseif(preg_match("|^".$pl_dir."|", $mo)|| preg_match("|^".$plm_dir."|", $mo)) {
		//we are a normal or wpmu plugin
		if ((strpos($parts['basename'], $textdomain) === false) && ($textdomain != 'default')) {
			preg_match("/([a-z][a-z]_[A-Z][A-Z]\.mo)$/", $parts['basename'], $h);
			if (!empty($textdomain)) {
				$mo	= $parts['dirname'].'/'.$textdomain.'-'.$h[1];
			}else {
				$mo	= $parts['dirname'].'/'.$h[1];
			}
		}
	}else{
		//we are a theme plugin, could be tested but skipped for now.
	}
	
	if ($f->is_illegal_empty_mofile($textdomain)) {
		header('Status: 404 Not Found');
		header('HTTP/1.1 404 Not Found');
		_e("You are trying to create an empty mo-file without any translations. This is not possible, please translate at least one entry.", CSP_PO_TEXTDOMAIN);
		exit();
	}
	
	if (!$f->write_mofile($mo,$textdomain)) {
		header('Status: 404 Not Found');
		header('HTTP/1.1 404 Not Found');
		echo sprintf(__("You do not have the permission to write to the file '%s'.", CSP_PO_TEXTDOMAIN), $mo);
		exit();
	}

	header('Content-Type: application/json');
?>
{
	filetime: '<?php echo date (__('m/d/Y H:i:s',CSP_PO_TEXTDOMAIN), filemtime($mo)); ?>'
}
<?php		
	exit();
}

function csp_po_ajax_handle_create_language_path() {
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages');
	require_once('includes/locale-definitions.php');
	require_once('includes/class-filesystem-translationfile.php');
	
	$path = strip_tags($_POST['path']);
	
	$pofile = new CspFileSystem_TranslationFile();
	
	if (!$pofile->create_directory($path)) {
		header('Status: 404 Not Found');
		header('HTTP/1.1 404 Not Found');
		_e("You do not have the permission to create a new Language File Path.<br/>Please create the appropriated path using your FTP access.", CSP_PO_TEXTDOMAIN);
	}
	else{
			header('Status: 200 ok');
			header('HTTP/1.1 200 ok');
			header('Content-Length: 1');	
			print 0;
	}
	exit();
}

function csp_po_ajax_handle_create_pot_indicator() {
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages');
	require_once('includes/locale-definitions.php');
	require_once('includes/class-filesystem-translationfile.php');
	
	$locale = 'en_US';
	
	$pofile = new CspFileSystem_TranslationFile();
	$filename = strip_tags($_POST['potfile']);
	$pofile->new_pofile(
		$filename, 
		'/',
		'PlaceHolder', 
		date("Y-m-d H:iO"), 
		'none', 
		$csp_l10n_plurals[substr($locale,0,2)], 
		$csp_l10n_sys_locales[$locale]['lang'], 
		$csp_l10n_sys_locales[$locale]['country']
	);
	if(!$pofile->write_pofile($filename)) {
		header('Status: 404 Not Found');
		header('HTTP/1.1 404 Not Found');
		echo sprintf(__("You do not have the permission to create the file '%s'.", CSP_PO_TEXTDOMAIN), $filename);
	}
	else{	
		header('Status: 200 ok');
		header('HTTP/1.1 200 ok');
		header('Content-Length: 1');	
		print 0;
	}
/*	
	$handle = @fopen(strip_tags($_POST['potfile']), "w");
	
	if ($handle === false) {
		header('Status: 404 Not Found');
		header('HTTP/1.1 404 Not Found');
		_e("You do not have the permission to choose the translation file directory<br/>Please upload at least one language file (*.mo|*.po) or an empty template file (*.pot) at the appropriated folder using FTP.", CSP_PO_TEXTDOMAIN);
	}
	else{
		@fwrite($handle, 
			"msgid \"\"\n".
			"msgstr \"\"\n".
			"\"MIME-Version: 1.0\"\n".
			"\"Content-Type: text/plain; charset=UTF-8\"\n".
			"\"Content-Transfer-Encoding: 8bit\"\n"
		);
		@fclose($handle);
		header('Status: 200 ok');
		header('HTTP/1.1 200 ok');
		header('Content-Length: 1');	
		print 0;
	}
	exit();
*/	
}

//////////////////////////////////////////////////////////////////////////////////////////
//	Admin Initialization ad Page Handler
//////////////////////////////////////////////////////////////////////////////////////////
if (function_exists('add_action')) {
	if (is_admin() && !defined('DOING_AJAX')) {
		add_action('admin_init', 'csp_po_init');
		add_action('admin_head', 'csp_po_admin_head');
		add_action('admin_menu', 'csp_po_admin_menu');
		require_once('includes/locale-definitions.php');
	}
	if(is_admin()) {
		add_action('admin_init', 'csp_po_init_per_user_trans');	
		add_action('admin_init', 'csp_check_filesystem');
	}
}

function csp_check_filesystem() {
	//file system investigation
	if (function_exists('get_filesystem_method')) {
		$fsm = get_filesystem_method(array());
		define("CSL_FILESYSTEM_DIRECT", $fsm == 'direct');
	}else{
		define("CSL_FILESYSTEM_DIRECT", true);
	}
}

function csp_po_init_per_user_trans() {
	//process per user settings
	if (is_user_logged_in() && defined('TRANSLATION_API_PER_USER') && (TRANSLATION_API_PER_USER === true) && current_user_can('manage_options')) {
		$myself = wp_get_current_user();
		$func = function_exists('get_user_meta') ? 'get_user_meta' : 'get_usermeta';
		$g = call_user_func($func, $myself->ID, 'csp-google-api-key', true);
		if (!empty($g) && !defined('GOOGLE_TRANSLATE_KEY'))  define('GOOGLE_TRANSLATE_KEY', $g);
		$m1 = call_user_func($func, $myself->ID, 'csp-microsoft-api-client-id', true);
		if (!empty($m1) && !defined('MICROSOFT_TRANSLATE_CLIENT_ID'))  define('MICROSOFT_TRANSLATE_CLIENT_ID', $m1);
		$m2 = call_user_func($func, $myself->ID, 'csp-microsoft-api-client-secret', true);
		if (!empty($m2) && !defined('MICROSOFT_TRANSLATE_CLIENT_SECRET'))  define('MICROSOFT_TRANSLATE_CLIENT_SECRET', $m2);
	}		
	if (!defined('TRANSLATION_API_PER_USER_DONE')) define('TRANSLATION_API_PER_USER_DONE', true);
}

function csp_po_init() {
	//currently not used, subject of later extension
	$low_mem_mode = (bool)get_option('codestyling-localization.low-memory', false);
	define('CSL_LOW_MEMORY', $low_mem_mode);	
}
function csp_callback_help_overview() {
?>
	<p>
		<strong>Codestyling Localization </strong> - <em>"<?php _e('... translate your WordPress, Plugins and Themes', CSP_PO_TEXTDOMAIN); ?>"</em>
	</p>
	<p>
	<?php _e('While get in touch with WordPress you will find out, that the initial delivery package comes only with english localization. If you want WordPress to show your native language, you have to provide the appropriated language file at languages folder. This files will be used to replace the english text phrases during the process of page generation. This translation capability has the origin at the gettext functionality which currently been used across a wide range of open source projects.', CSP_PO_TEXTDOMAIN); ?>
	</p>
	<p style="margin-top: 50px;padding-top:10px; border-top: solid 1px #ccc;">
		<small class="alignright" style="position:relative; margin-top: -30px; color: #aaa;">&copy; 2008 - 2012 by Heiko Rabe</small>
		<a href="http://wordpress.org/extend/plugins/codestyling-localization/" target="_blank">Plugin Directory</a> | 
		<a href="http://wordpress.org/extend/plugins/codestyling-localization/changelog/" target="_blank">Change Logs</a> | 
		<a href="<?php echo CSP_PO_BASE_URL."/license.txt";?>" target="_blank">License</a> 
		<a class="alignright" href="http://wordpress.org/extend/plugins/wp-native-dashboard/" target="_blank"><?php _e('Dashboard in your Language',CSP_PO_TEXTDOMAIN);?></a>
	</p>
<?php
}

function csp_callback_help_low_memory() {
?>
<p>
	<strong><?php _e('PHP Memory Limit Problems', CSP_PO_TEXTDOMAIN); ?></strong>
</p>
<p>
<?php _e('If your Installation is running under low remaining memory conditions, you will face the memory limit error during scan process or opening catalog content. If you hitting your limit, you can enable this special mode. This will try to perform the actions in a slightly different way but that will lead to a considerably slower response times but nevertheless gives no warranty, that it will solve your memory related problems at all cases.', CSP_PO_TEXTDOMAIN); ?>
</p>
<p>
<?php _e('It could be, that your provider confirms, that you have enough PHP memory for your installation but it is not. You can detect your real available memory limit using the plugin <a href="http://wordpress.org/extend/plugins/wp-system-health/" target="_blank">WP System Health</a>. It has a build in feature (called <em>Test Suite</em>) to evaluate correctly the memory limit the server will permit.', CSP_PO_TEXTDOMAIN); ?>
</p>
<?php
}

function csp_callback_help_compatibility() {
?>
<p>
	<strong><?php _e('Compatibility - Hints and Errors', CSP_PO_TEXTDOMAIN); ?></strong>
</p>
<p> 
	<?php _e("If you get compatibility warnings, than they are often related to a wrong usage of WordPress core functionality by the authors of the affected Themes or Plugins.",CSP_PO_TEXTDOMAIN); ?> 
	<?php _e("There are several reason for such reports, but in each of this cases only the original author can solve it:",CSP_PO_TEXTDOMAIN); ?>
</p>
<p>
	<ul>
		<li>
		<?php _e("Loading of translation files will be performed beside the WordPress standard functionality.",CSP_PO_TEXTDOMAIN); ?>
		</li>
		<li>
		<?php _e("Textdomains can not be parsed from source files because of used coding syntax.",CSP_PO_TEXTDOMAIN); ?>
		</li>
		<li>
		<?php _e("Component seems to be translatable but doesn't use a translation file load call.",CSP_PO_TEXTDOMAIN); ?>
		</li>
	</ul>
</p>
<p>
	<?php _e("Reported issues are not a problem of <em>Codestyling Localization</em>, it's caused by the author of the affected component within it's code.",CSP_PO_TEXTDOMAIN); ?>
</p>
<?php
}

function csp_callback_help_textdomain() {
?>
<p>
	<strong><?php _e('What is a textdomain?', CSP_PO_TEXTDOMAIN); ?></strong>
</p>
<p>
	<?php _e('Textdomains are used to specified the context for the translation file to be loaded and processed. If a component tries to load a translation file using a textdomain, all texts assigned to this domain gets translated during page creation.', CSP_PO_TEXTDOMAIN); ?>
</p>
<p>
	<?php _e('The extended feature for textdomain separation shows at dropdown box <i>Textdomain</i> the pre-selected primary textdomain.',CSP_PO_TEXTDOMAIN); ?><br/>
	<?php _e('All other additional contained textdomains occur at the source but will not be used, if not explicitely supported by this component!',CSP_PO_TEXTDOMAIN); ?><br/>
	<?php _e('Please contact the author, if some of the non primary textdomain based phrases will not show up translated at the required position!',CSP_PO_TEXTDOMAIN); ?><br/>
	<?php _e('The Textdomain <i><b>default</b></i> always stands for the WordPress main language file, this could be either intentionally or accidentally!',CSP_PO_TEXTDOMAIN); ?><br/>
</p>
<p>
	<strong><?php _e('Warning Messages', CSP_PO_TEXTDOMAIN); ?></strong>
</p>
<p>
	<?php _e('If you get warnings either at the overview page or at the editor page, somethings is wrong within the analysed component.', CSP_PO_TEXTDOMAIN); ?>
	<?php _e('The overview page will show warnings, if the textdomain can not be found clearly. In this case the author has written the components code in a way make it hard to detect.', CSP_PO_TEXTDOMAIN); ?>
</p>
<p>
	<?php _e('Warnings at the editors view will show up, if the component is using badly coded textdomains. This could be either by integration of other plugins code or accidentally by typing mistakes.', CSP_PO_TEXTDOMAIN); ?>
</p>
<p>
	<?php _e("Reported issues are not a problem of <em>Codestyling Localization</em>, it's caused by the author of the affected component within it's code.",CSP_PO_TEXTDOMAIN); ?>
</p>
<?php
}

function csp_callback_help_filepermissions() {
?>
<p>
	<strong><?php _e('File Permission and Access Rights', CSP_PO_TEXTDOMAIN); ?></strong>
</p>
<p>
	<?php _e('Your provider does not permit the ability to modify files at your installation by executed scripts. This translation plugins requires this permission to work properly. WordPress solves this at updates by presenting a dialog for your FTP parameters. This plugin will prompt for your FTP credentials if they are required.', CSP_PO_TEXTDOMAIN); ?>
</p>
<p>
	<strong><?php _e('Permit File Modifications without prompting for User Credentials', CSP_PO_TEXTDOMAIN); ?></strong>
</p>
<p>
	<?php _e('You can define the necessary constants at your <em>wp-config.php</em> file as described at the <a href="http://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants" target="_blank">WordPress Codex Page - Upgrade Constants</a> to get it working at your installation without recurrently occuring credential requests. If your constants are properly defined, this plugin will work smoothly and the WordPress Automatic Updates will work without any further question about FTP User Credentials too.', CSP_PO_TEXTDOMAIN); ?>
</p>

<?php
}

function csp_callback_help_translationformat() {
?>
<p>
	<strong><?php _e('Extended Translation File Format', CSP_PO_TEXTDOMAIN); ?></strong>
</p>
<p>
	<?php _e('You may get an error message if you try to open a translation file for editing. The reason behind is the necessary separation of contained textdomains within your components code to be translated.', CSP_PO_TEXTDOMAIN); ?>
</p>
<p>
	<?php _e('Many authors do not care, if they mix up textdomains during code writing. Furthermore the textdomain <b><em>default</em></b> will be used by WordPress itself only. Any text assigned to the textdomain <b><em>default</em></b> will become untranslated at output even if you would translate it. Thats why this plugin separates this textdomains to show up possible mistakes.', CSP_PO_TEXTDOMAIN); ?>
</p>
<p>
	<strong><?php _e('How to edit files with this error message ?', CSP_PO_TEXTDOMAIN); ?></strong>
</p>
<p>
	<?php _e('Just go back the the overview page, search your affected plugin/theme and re-scan the translation content. Afterwards it will be possible to open the translation file for editing.', CSP_PO_TEXTDOMAIN); ?>
</p>
<?php
}

function csp_callback_help_workonchildthemes() {
?>
<p>
	<strong><?php _e('Working with Child Theme Translations', CSP_PO_TEXTDOMAIN); ?></strong>
</p>
<p>
	<?php _e('Child Themes are using in normal cases the translation files of the main theme. In some cases it could be necessary to have a separate language file handling at the Child Theme itself.', CSP_PO_TEXTDOMAIN); ?>
</p>
<p>
	<strong><?php _e('How to make your Child Theme ready to use its own translation files?', CSP_PO_TEXTDOMAIN); ?></strong>
</p>
<p>
	<?php _e('First of all you have to modify your Child Themes <em>functions.php</em> file and call the appropriated load method as shown below. Assume the textdomain is defined at the Main Theme as <b>supertheme</b> the load function should look like:', CSP_PO_TEXTDOMAIN); ?>
</p>
<p><pre>       load_child_theme_textdomain('supertheme', get_stylesheet_directory().'/languages');</pre></p>
<p>
	<?php _e('The path has been defined as subdirectory within the Child Themes directory but you can skip the directory parameter and place the language files at the Child Themes main folder.', CSP_PO_TEXTDOMAIN); ?>
</p>
<p>
	<strong><?php _e('(Re)scan process and Synchronization at Child Themes', CSP_PO_TEXTDOMAIN); ?></strong>
</p>
<p>
	<?php _e('Scanning a Child Theme always includes the files from Main Theme too. So you always get the mixed translation from Main and Child Theme. Doing a Synchronization with the Main Theme will preserve the texts from Child Theme and will attach new texts from Main Theme only.', CSP_PO_TEXTDOMAIN); ?>
</p>
<?php
}

function csp_callback_help_selfprotection() {
?>
<p>
	<strong><?php _e('Scripting Guard - hardening against plugin/theme based malformed script injections', CSP_PO_TEXTDOMAIN); ?></strong>
</p>
<p>
	<?php _e('Some authors of plugins and themes does not care about how they attach javascripts into WordPress backend pages. They pollute pages from other plugins with their own script code and damage the proper function of those plugins.', CSP_PO_TEXTDOMAIN); ?>
</p>
<p>
	<?php _e('The plugin <em>Codestyling Localization</em> introduced a high sophisticated inject detection and will show error messages, if themes or plugins try to inject their own scripts into this plugin pages. Furthermore all embedded scripts will be safe guarded and traced in case they will raise runtime exceptions. Doing so this plugin protects itself of malfunction caused by 3rd party plugin/theme authors. This will ensure the correct behavoir for this page, but expect at other backend pages malfunctioning code, because this is a global issue.', CSP_PO_TEXTDOMAIN); ?>
</p>
<p>
	<strong><?php _e('CDN - Javascripts loaded using Content Delivery Networks instead of WordPress provide files', CSP_PO_TEXTDOMAIN); ?></strong>
</p>
<p>
	<?php _e('Some Blog owner decide to replace the location where the Javascripts will be loaded from by using a Plugin. In normal cases this should work proper but sometimes WordPress includes versions of Scripts not yet hosted at CDN provider. The Guard will threat CDN usage as warning and checks if all files can be load from CDN. If not possible to load at least one file, additionally an error message will be issued to show, that this page will not work as expected.', CSP_PO_TEXTDOMAIN); ?>
</p>
<p>
	<strong><?php _e('What can I do, if I get this protection message alert?', CSP_PO_TEXTDOMAIN); ?></strong>
</p>
<p>
	<?php _e('If your Installation has this kind of problems, please contact the author of theme or plugin(s) which inject their script code either accidentally or intentionally (click message details). He/she must repair the affected theme/plugin to play nicely with other plugins at WordPress backend and/or restrict its scripts to the 3rd party theme/plugin pages only.', CSP_PO_TEXTDOMAIN); ?>
</p>
<p>
	<strong><?php _e('Theme & Plugin Authors - exclusion from Guard for developers', CSP_PO_TEXTDOMAIN); ?></strong>
</p>
<p>
	<?php _e('You may have written a Plugin or Theme that requires scripts at all pages but play nicely at backend pages. In those cases please send me an email with your repository link. I will check this Plugin or Theme and exclude it from trace, if the test will show, that it is working well.', CSP_PO_TEXTDOMAIN); ?>
</p>
<p>
	<?php _e('Plugins currently supported by Scripting Guard:', CSP_PO_TEXTDOMAIN); ?>
</p>
<ul>
	<li><a href="http://wordpress.org/extend/plugins/wp-native-dashboard/" target="_blank">WP Native Dashboard</a> <small>(by codestyling)</small></li>
	<li><a href="http://wordpress.org/extend/plugins/debug-bar/" target="_blank">Debug Bar</a> <small>(by wordpressdotorg)</small></li>
	<li><a href="http://wordpress.org/extend/plugins/debug-bar-console/" target="_blank">Debug Bar Console</a> <small>(by koopersmith)</small></li>
	<li><a href="http://wordpress.org/extend/plugins/wp-piwik/" target="_blank">WP-Piwik</a> <small>(by braekling)</small></li>
</ul>
<?php
}

function csp_try_jquery_document_ready_hardening_pattern($content, $pattern) {
	$pieces = explode($pattern, $content);
	if (count($pieces) > 1) {
		for ($loop=1; $loop<count($pieces); $loop++) {
			$counter = 0;
			$startofready = -1;
			$endofready = -1;
			$script  = $pieces[$loop];
			for($i=0; $i < strlen($script); $i++) {
				switch(substr($script, $i, 1)) {
					case '{':
						$counter++;
						if ($counter == 1) {
							$startofready = $i;
						}
						break;
					case '}';
						$counter--;
						if ($counter == 0) {
							$endofready = $i;
							$i = strlen($script);
						}
						break;
					default:
						break;
				}
			}
			if ($startofready != -1 && $endofready != -1) {
				if ($script[$endofready+1] == ')') $endofready++;
				$sub = substr($script, $startofready+1, $endofready-$startofready-2);
				$pieces[$loop] = str_replace($sub, "\ntry{\n".$sub."\n}catch(e){csp_self_protection.runtime.push(e.message);}" , $script);
			}			
		}
	}
	return implode($pattern, $pieces);
}

function csp_try_jquery_document_ready_hardening($content) {
	$script = csp_try_jquery_document_ready_hardening_pattern($content, '(document).ready(');
	return csp_try_jquery_document_ready_hardening_pattern($script, 'jQuery(function()');	
}

$csp_traced_php_errors = array(
	'suppress_errors' => false,
	'old_handler' => null,
	'messages' => array()
);

$csp_external_scripts = array(
	'cdn' => array(
		'tokens' => array(),
		'scripts' => array()
	),
	'dubious' => array(
		'tokens' => array(),
		'scripts' => array()
	)
);

$csp_known_wordpress_externals = array(
	//none wordpress own files
	'colorpicker', 'prototype', 'scriptaculous-root', 'scriptaculous-builder', 'scriptaculous-dragdrop', 'scriptaculous-effects',
	'scriptaculous-slider', 'scriptaculous-sound', 'scriptaculous-controls', 'scriptaculous', 'cropper', 'jquery',
	'jquery-ui-core', 'jquery-effects-core', 'jquery-effects-blind', 'jquery-effects-bounce', 'jquery-effects-clip', 
	'jquery-effects-drop', 'jquery-effects-explode', 'jquery-effects-fade', 'jquery-effects-fold', 'jquery-effects-highlight',
	'jquery-effects-pulsate', 'jquery-effects-scale', 'jquery-effects-shake', 'jquery-effects-slide', 'jquery-effects-transfer',
	'jquery-ui-accordion', 'jquery-ui-autocomplete', 'jquery-ui-button', 'jquery-ui-datepicker', 'jquery-ui-dialog', 'jquery-ui-draggable',
	'jquery-ui-droppable', 'jquery-ui-mouse', 'jquery-ui-position', 'jquery-ui-progressbar', 'jquery-ui-resizable', 'jquery-ui-selectable',
	'jquery-ui-slider', 'jquery-ui-sortable', 'jquery-ui-tabs', 'jquery-ui-widget', 'jquery-form', 'jquery-color', 'suggest',
	'schedule', 'jquery-query', 'jquery-serialize-object', 'jquery-hotkeys', 'jquery-table-hotkeys', 'jquery-touch-punch',
	'thickbox', 'jcrop', 'swfobject', 'plupload', 'plupload-html5', 'plupload-flash', 'plupload-silverlight', 'plupload-html4',
	'plupload-all', 'plupload-handlers', 'swfupload', 'swfupload-swfobject', 'swfupload-queue', 'swfupload-speed','swfupload-all',
	'swfupload-handlers', 'json2', 'farbtastic',
	//wordpress admin files
	'utils', 'common', 'sack', 'quicktags', 'editor', 'wp-fullscreen', 'wp-ajax-response', 'wp-pointer', 'autosave',
	'wp-lists', 'comment-reply', 'imgareaselect', 'password-strength-meter', 'user-profile', 'user-search', 'site-search',
	'admin-bar', 'wplink', 'wpdialogs', 'wpdialogs-popup', 'word-count', 'media-upload', 'hoverIntent', 'customize-base',
	'customize-loader', 'customize-preview', 'customize-controls', 'ajaxcat', 'admin-categories', 'admin-tags', 'admin-custom-fields',
	'admin-comments', 'xfn', 'postbox', 'post', 'link', 'comment', 'admin-gallery', 'admin-widgets', 'theme', 'theme-preview',
	'inline-edit-post', 'inline-edit-tax', 'plugin-install', 'dashboard', 'list-revisions', 'media', 'image-edit', 'set-post-thumbnail',
	'nav-menu', 'custom-background', 'media-gallery'
);

function csp_known_and_valid_cdn($url) {
	return preg_match("/^https?:\/\/[^\.]*\.wp\.com/", $url);
}

function csp_plugin_denied_by_guard($url)
{
	$valid = array(
		'/codestyling-localization/',
		'/wp-native-dashboard/',
		'/debug-bar/',
		'/debug-bar-console/',
		'/localization/',
		'/wp-piwik/'
	);
	foreach($valid as $slug)
	{
		if(stripos($url, $slug) !== false)
		{
			 return false;
		}
	}
	return true;
}

function csp_filter_print_scripts_array($scripts) {
	//detect CDN script redirecting
	global $wp_scripts, $csp_external_scripts, $csp_known_wordpress_externals;
	if (is_object($wp_scripts)) {
		foreach($scripts as $token) {
			if(isset($wp_scripts->registered[$token])) {
				if (isset($wp_scripts->registered[$token]->src) && !empty($wp_scripts->registered[$token]->src)) {
					if (preg_match('|^http|', $wp_scripts->registered[$token]->src)) {
						if(!preg_match('|^'.str_replace('.','\.',CSP_SELF_DOMAIN).'|', $wp_scripts->registered[$token]->src)) {
							if (in_array($token, $csp_known_wordpress_externals) || csp_known_and_valid_cdn($wp_scripts->registered[$token]->src)) {
								if (!in_array($token, $csp_external_scripts['cdn']['tokens'])) {
									$csp_external_scripts['cdn']['tokens'][] = $token;
									$csp_external_scripts['cdn']['scripts'][] = $wp_scripts->registered[$token]->src;
								}
							} else {
								if (!in_array($token, $csp_external_scripts['dubious']['tokens'])) {
									$csp_external_scripts['dubious']['tokens'][] = $token;
									$csp_external_scripts['dubious']['scripts'][] = $wp_scripts->registered[$token]->src;
								}
							}
						}
					}
				}
			}
		}
	}
	
	//protect against injected media upload script, modifies thickbox for media uploads not required here!
	if (in_array('media-upload', $scripts)) {
		if (!defined('CSL_MEDIA_UPLOAD_STRIPPED')) define('CSL_MEDIA_UPLOAD_STRIPPED', true);
		$scripts = array_diff($scripts, array('media-upload'));
	}
	//protect against "dubious" scripts !
	$scripts = array_diff($scripts, $csp_external_scripts['dubious']['tokens']);
	return $scripts;
}

function csp_php_error_handler($errno, $errstr, $errfile, $errline) {
	global $csp_traced_php_errors;
	$errorType = array (  
         E_ERROR                => 'ERROR',  
         E_CORE_ERROR           => 'CORE ERROR',  
         E_COMPILE_ERROR        => 'COMPILE ERROR',  
         E_USER_ERROR           => 'USER ERROR',  
         E_RECOVERABLE_ERROR  	=> 'RECOVERABLE ERROR',  
         E_WARNING              => 'WARNING',  
         E_CORE_WARNING         => 'CORE WARNING',  
         E_COMPILE_WARNING      => 'COMPILE WARNING',  
         E_USER_WARNING         => 'USER WARNING',  
         E_NOTICE               => 'NOTICE',  
         E_USER_NOTICE          => 'USER NOTICE',  
         E_DEPRECATED           => 'DEPRECATED',  
         E_USER_DEPRECATED      => 'USER_DEPRECATED',  
         E_PARSE                => 'PARSING ERROR',
		 E_STRICT				=> 'STRICT'
    );  
  
    if (array_key_exists($errno, $errorType)) {  
        $errname = $errorType[$errno];  
    } else {  
        $errname = 'UNKNOWN ERROR';  
    }  
	$csp_traced_php_errors['messages'][] = "$errname <strong>Error: [$errno] </strong> $errstr <strong>$errfile</strong> on line <strong>$errline</strong>";
	if ($csp_traced_php_errors['old_handler'] != null && !$csp_traced_php_errors['suppress_errors']) {
		return call_user_func($csp_traced_php_errors['old_handler'], $errno, $errstr, $errfile, $errline);
	}
	return $csp_traced_php_errors['suppress_errors'];
}

function csp_trace_php_errors() {
	global $csp_traced_php_errors;
	
	$csp_traced_php_errors['suppress_errors'] = (is_admin() && isset($_REQUEST['page']) && ($_REQUEST['page'] == 'codestyling-localization/codestyling-localization.php'));
	if(defined('DOING_AJAX') && DOING_AJAX && isset($_POST['action'])) {
		$actions = array(
			'csp_po_dlg_new',
			'csp_po_dlg_delete',
			'csp_po_dlg_rescan',
			'csp_po_dlg_show_source',
			'csp_po_merge_from_maintheme',
			'csp_po_create',
			'csp_po_destroy',
			'csp_po_scan_source_file',
			'csp_po_change_low_memory_mode',
			'csp_po_change_translate_api',
			'csp_po_change_permission',
			'csp_po_launch_editor',
			'csp_po_translate_by_google',
			'csp_po_translate_by_microsoft',
			'csp_po_save_catalog_entry',
			'csp_po_generate_mo_file',
			'csp_po_create_language_path',
			'csp_po_create_pot_indicator',
			'csp_self_protection_result'
		);
		if (in_array($_POST['action'], $actions))
			$csp_traced_php_errors['suppress_errors'] = true;
	}
	
	if (function_exists('set_error_handler'))
		$csp_traced_php_errors['old_handler'] = set_error_handler("csp_php_error_handler", E_ALL);
}

function csp_start_protection($hook_suffix) {
	ob_start();
}

function csp_self_script_protection_head() {
	$content = ob_get_clean();
	//1st - unify script tags
	$content = preg_replace("/(<script[^>]*)(\/\s*>)/i", '$1></script>', $content);
	$scripts = array();
	$dirty_plugins = array();
	$dirty_theme = array();
	$dirty_scripts = array();
	$dirty_index = array();
	//2nd - analyse scripts
	if (preg_match_all("/<script[^>]*>([\s\S]*?)<\/script>/i", $content, $scripts)) {	
		$num = count($scripts[0]);
		for($i=0; $i<$num; $i++) {
			if (empty($scripts[1][$i])) {
				//url based scripts - mark as dirty if required
				preg_match("/src=[\"']([^\"^']*\.js|[^\"^']*\.php)(\?[^\"^']*[\"']|[\"'])/", $scripts[0][$i], $url);
				if (isset($url[1]) && !empty($url[1])){
					global $csp_external_scripts;				
					if(	stripos($url[1], content_url()) !== false && csp_plugin_denied_by_guard($url[1]) ) {
						//internal scripts
						$dirty_scripts[] = $url[1];
						$dirty_index[] = $i;
						if (stripos($url[1], plugins_url()) !== false || stripos($url[1], content_url().'/mu-plugins') !== false) {
							$dirty_plugins[] = $url[1];
						}else{
							$dirty_theme[] = $url[1];
						}
					}
					elseif (stripos($url[1], CSP_SELF_DOMAIN) === false && !in_array($url[1], $csp_external_scripts['cdn']['scripts'])) {
						//external
						$dirty_index[] = $i;			
						$csp_external_scripts['dubious']['tokens'][] = "hook:admin_head#$i";
						$csp_external_scripts['dubious']['scripts'][] = $url[1];
					}
				}
			}else{
				//embedded scripts - wrap within exception handler
				$content = str_replace($scripts[0][$i], '<script type="text/javascript">'."\n//<![CDATA[\ntry {\n".csp_try_jquery_document_ready_hardening($scripts[1][$i])."\n}catch (e) {\n\tcsp_self_protection.runtime.push(e.message); \n}\n//]]>\n</script>", $content);
			}
		}
	}
	//3rd - remove critical injected scripts
	if (count($dirty_index) > 0) {
		foreach($dirty_index as $i) {
			$content = str_replace($scripts[0][$i], '', $content);
		}
	}
	//4th - define our protection
	echo '<script type="text/javascript">var csp_self_protection = { "dirty_theme" : '.json_encode($dirty_theme).', "dirty_plugins" : ' . json_encode($dirty_plugins). ", \"runtime\" : [] };</script>\n";
	echo $content;
}

function csp_self_script_protection_footer() {
	$content = ob_get_clean();
	//1st - unify script tags
	$content = preg_replace("/(<script[^>]*)(\/\s*>)/i", '$1></script>', $content);
	$scripts = array();
	$dirty_plugins = array();
	$dirty_theme = array();
	$dirty_scripts = array();
	$dirty_index = array();
	//2nd - analyse scripts
	if (preg_match_all("/<script[^>]*>([\s\S]*?)<\/script>/i", $content, $scripts)) {	
		$num = count($scripts[0]);
		for($i=0; $i<$num; $i++) {
			if (empty($scripts[1][$i])) {
				//url based scripts - mark as dirty if required
				preg_match("/src=[\"']([^\"^']*\.js|[^\"^']*\.php)(\?[^\"^']*[\"']|[\"'])/", $scripts[0][$i], $url);
				if (isset($url[1]) && !empty($url[1])){
					global $csp_external_scripts;				
					if(stripos($url[1], content_url()) !== false && csp_plugin_denied_by_guard($url[1])) {
						//internal scripts
						$dirty_scripts[] = $url[1];
						$dirty_index[] = $i;
						if (stripos($url[1], plugins_url()) !== false || stripos($url[1], content_url().'/mu-plugins') !== false) {
							$dirty_plugins[] = $url[1];
						}else{
							$dirty_theme[] = $url[1];
						}
					}
					elseif (stripos($url[1], CSP_SELF_DOMAIN) === false && !in_array($url[1], $csp_external_scripts['cdn']['scripts'])) {
						//external
						$dirty_index[] = $i;			
						$csp_external_scripts['dubious']['tokens'][] = "hook:admin_footer#$i";
						$csp_external_scripts['dubious']['scripts'][] = $url[1];
					}
				}
			}else{
				//embedded scripts - wrap within exception handler
				$content = str_replace($scripts[0][$i], '<script type="text/javascript">'."\ntry {\n".csp_try_jquery_document_ready_hardening($scripts[1][$i])."\n }\ncatch(e) {\n\tcsp_self_protection.runtime.push(e.message); \n};\n</script>", $content);
			}
		}
	}
	//3rd - remove critical injected scripts
	if (count($dirty_index) > 0) {
		foreach($dirty_index as $i) {
			$content = str_replace($scripts[0][$i], '', $content);
		}
	}
	//4th - define our protection
	echo '<script type="text/javascript">csp_self_protection.dirty_theme = csp_self_protection.dirty_theme.concat('.json_encode($dirty_theme).");</script>\n";
	echo '<script type="text/javascript">csp_self_protection.dirty_plugins = csp_self_protection.dirty_plugins.concat('.json_encode($dirty_plugins).");</script>\n";
	$media_upload = ((defined('CSL_MEDIA_UPLOAD_STRIPPED') && CSL_MEDIA_UPLOAD_STRIPPED === true) ? ( function_exists("admin_url") ? admin_url('js/media-upload.js') : get_site_url().'/wp-admin/js/media-upload.js' ) : '');
	if (!empty($media_upload))
		echo '<script type="text/javascript">csp_self_protection.dirty_enqueues = ["'.$media_upload."\"];</script>\n";
	else
		echo "<script type=\"text/javascript\">csp_self_protection.dirty_enqueues = [];</script>\n";

	global $csp_external_scripts;
	if (count($csp_external_scripts['cdn']['tokens']) > 0 || count($csp_external_scripts['dubious']['tokens']) > 0)
		echo '<script type="text/javascript">csp_self_protection.externals = '.json_encode($csp_external_scripts).";</script>\n";
	else
		echo "<script type=\"text/javascript\">csp_self_protection.externals = { 'cdn' : { 'tokens' : [], 'scripts' : [] }, 'dubious' : { 'tokens' : [], 'scripts' : [] } };</script>\n";
	
	global $csp_traced_php_errors;
	if(count($csp_traced_php_errors['messages'])) {
		echo '<script type="text/javascript">csp_self_protection.php = '.json_encode($csp_traced_php_errors['messages']).";</script>\n";
	}else{
		echo "<script type=\"text/javascript\">csp_self_protection.php = []; </script>\n";
	}
		
	echo $content;
?>
<script type="text/javascript">
	jQuery(document).ready(function($) { 
		if (
			csp_self_protection.dirty_theme.length 
			|| 
			csp_self_protection.dirty_plugins.length 
			|| 
			csp_self_protection.runtime.length 
			|| 
			csp_self_protection.dirty_enqueues.length
			||
			csp_self_protection.externals.cdn.tokens.length
			||
			csp_self_protection.externals.dubious.tokens.length
			||
			csp_self_protection.php.length
		) {
			$.post("<?php echo CSP_PO_ADMIN_URL.'/admin-ajax.php' ?>", { "action" : "csp_self_protection_result" , "data" :  csp_self_protection }, function(data) {
				$('#csp-wrap-main h2').after(data);
				$('.self-protection-details').live('click', function(event) {
					event.preventDefault();
					$('#self-protection-details').toggle();
				});
			});
		}
	});
</script>
<?php	
}

function csp_handle_csp_self_protection_result() {
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages');	
	$incidents = 0;
	if (isset($_POST['data']['dirty_enqueues'])) $incidents += count($_POST['data']['dirty_enqueues']);
	if (isset($_POST['data']['dirty_theme'])) $incidents += count($_POST['data']['dirty_theme']);
	if (isset($_POST['data']['dirty_plugins'])) $incidents += count($_POST['data']['dirty_plugins']);
	if (isset($_POST['data']['runtime'])) $incidents += count($_POST['data']['runtime']);
	if (isset($_POST['data']['externals']) && isset($_POST['data']['externals']['cdn'])) $incidents += count($_POST['data']['externals']['cdn']['tokens']);
	if (isset($_POST['data']['externals']) && isset($_POST['data']['externals']['dubious'])) $incidents += count($_POST['data']['externals']['dubious']['tokens']);
	if (isset($_POST['data']['php'])) $incidents += count($_POST['data']['php']);
?>
<p class="self-protection"><strong><?php _e('Scripting Guard',CSP_PO_TEXTDOMAIN);?></strong> [ <a class="self-protection-details" href="javascript:void(0)"><?php _e('details',CSP_PO_TEXTDOMAIN); ?></a> ]&nbsp;&nbsp;&nbsp;<?php echo sprintf(__('The Plugin <em>Codestyling Localization</em> was forced to protect its own page rendering process against <b>%s</b> %s !', CSP_PO_TEXTDOMAIN), $incidents, _n('incident', 'incidents', $incidents, CSP_PO_TEXTDOMAIN)); ?>&nbsp;<a align="left" class="question-help" href="javascript:void(0);" title="<?php _e("What does that mean?",CSP_PO_TEXTDOMAIN) ?>" rel="selfprotection"><img src="<?php echo CSP_PO_BASE_URL."/images/question.gif"; ?>" /></a></p>
<div id="self-protection-details" style="display:none;">
<?php
	if (isset($_POST['data']['php']) && count($_POST['data']['php'])) : ?>
		<div>
		<img class="alignleft" alt="" src="<?php echo CSP_PO_BASE_URL."/images/php-core.gif"; ?>" />
		<strong style="color:#800;"><?php _e('PHP runtime error reporting detected !',CSP_PO_TEXTDOMAIN); ?></strong><br/>
		<?php _e('Reason:',CSP_PO_TEXTDOMAIN);?> <strong><?php _e('some executed PHP code is not written proper',CSP_PO_TEXTDOMAIN); ?></strong> | 
		<?php _e('Originator:',CSP_PO_TEXTDOMAIN);?> <strong><?php _e('unknown', CSP_PO_TEXTDOMAIN); ?></strong> <small>(<?php _e('probably by Theme or Plugin',CSP_PO_TEXTDOMAIN); ?>)</small><br/>
		<?php _e('Below listed error reports has been traced and removed during page creation:',CSP_PO_TEXTDOMAIN); ?><br/>
		<ol>
		<?php foreach($_POST['data']['php'] as $message) : ?>
			<li><?php echo $message; ?></li>
		<?php endforeach; ?>
		</ol>
		</div>
	<?php endif; ?>
<?php
	if (isset($_POST['data']['dirty_enqueues']) && count($_POST['data']['dirty_enqueues'])) : ?>
		<div>
		<img class="alignleft" alt="" src="<?php echo CSP_PO_BASE_URL."/images/wordpress.gif"; ?>" />
		<strong style="color:#800;"><?php _e('Malfunction at admin script core detected !',CSP_PO_TEXTDOMAIN); ?></strong><br/>
		<?php _e('Reason:',CSP_PO_TEXTDOMAIN);?> <strong><?php _e('misplaced core file(s) enqueued',CSP_PO_TEXTDOMAIN); ?></strong> | 
		<?php _e('Polluter:',CSP_PO_TEXTDOMAIN);?> <strong><?php _e('unknown', CSP_PO_TEXTDOMAIN); ?></strong> <small>(<?php _e('probably by Theme or Plugin',CSP_PO_TEXTDOMAIN); ?>)</small><br/>
		<?php _e('Below listed scripts has been dequeued because of injection:',CSP_PO_TEXTDOMAIN); ?><br/>
		<ol>
		<?php foreach($_POST['data']['dirty_enqueues'] as $script) : ?>
			<li><?php echo strip_tags($script); ?></li>
		<?php endforeach; ?>
		</ol>
		</div>
	<?php endif; ?>
<?php
	if (isset($_POST['data']['dirty_theme']) && count($_POST['data']['dirty_theme'])) : $ct = function_exists('wp_get_theme') ? wp_get_theme() : current_theme_info(); ?>
		<div>
		<img class="alignleft" alt="" src="<?php echo CSP_PO_BASE_URL."/images/themes.gif"; ?>" />
		<strong style="color:#800;"><?php _e('Malfunction at current Theme detected!',CSP_PO_TEXTDOMAIN); ?></strong><br/>
		<?php _e('Name:',CSP_PO_TEXTDOMAIN);?> <strong><?php echo $ct->name; ?></strong> | 
		<?php _e('Author:',CSP_PO_TEXTDOMAIN);?> <strong><?php echo $ct->author; ?></strong><br/>
		<?php _e('Below listed scripts has been automatically stripped because of injection:',CSP_PO_TEXTDOMAIN); ?><br/>
		<ol>
		<?php foreach($_POST['data']['dirty_theme'] as $script) : ?>
			<li><?php echo strip_tags($script); ?></li>
		<?php endforeach; ?>
		</ol>
		</div>
	<?php endif; ?>
<?php
	if (isset($_POST['data']['dirty_plugins']) && count($_POST['data']['dirty_plugins'])) : 
		//WARNING: Plugin handling is not well coded by WordPress core
		$err = error_reporting(0);
		$plugs = get_plugins(); 
		error_reporting($err);

		foreach($plugs as $slug => $data) :
			list($slug) = explode('/', $slug);
			$affected = array();
			foreach($_POST['data']['dirty_plugins'] as $script) {
				if (stripos($script, $slug) !== false) $affected[] = $script;
			}
			if (count($affected) == 0) continue;
?>
		<div>
		<img class="alignleft" alt="" src="<?php echo CSP_PO_BASE_URL."/images/plugins.gif"; ?>" />
		<strong style="color:#800;"><?php _e('Malfunction at 3rd party Plugin detected!' ,CSP_PO_TEXTDOMAIN); ?></strong><br/>
		<?php _e('Name:',CSP_PO_TEXTDOMAIN);?> <strong><?php echo $data['Name']; ?></strong> | 
		<?php _e('Author:',CSP_PO_TEXTDOMAIN);?> <strong><?php echo $data['Author']; ?></strong><br/>
		<?php _e('Below listed scripts has been automatically stripped because of injection:',CSP_PO_TEXTDOMAIN); ?><br/>
		<ol>
		<?php foreach($affected as $script) : ?>
			<li><?php echo strip_tags($script); ?></li>
		<?php endforeach; ?>
		</ol>
		</div>
		<?php endforeach; ?>
	<?php endif; ?>
<?php
	if (isset($_POST['data']['runtime']) && count($_POST['data']['runtime'])) : ?>
		<div>
		<img class="alignleft" alt="" src="<?php echo CSP_PO_BASE_URL."/images/badscript.png"; ?>" />
		<strong style="color:#800;"><?php _e('Malfunction at 3rd party inlined Javascript(s) detected!' ,CSP_PO_TEXTDOMAIN); ?></strong><br/>
		<?php _e('Reason:',CSP_PO_TEXTDOMAIN);?> <strong><?php _e('javascript runtime exception', CSP_PO_TEXTDOMAIN); ?></strong> | 
		<?php _e('Polluter:',CSP_PO_TEXTDOMAIN);?> <strong><?php _e('unknown', CSP_PO_TEXTDOMAIN); ?></strong><br/>
		<?php _e('Below listed exception(s) has been caught and traced:',CSP_PO_TEXTDOMAIN); ?><br/>
		<ol>
		<?php foreach($_POST['data']['runtime'] as $script) : ?>
			<li><?php echo strip_tags(stripslashes($script)); ?></li>
		<?php endforeach; ?>
		</ol>
		</div>
	<?php endif; ?>	
<?php
	if (isset($_POST['data']['externals']) && isset($_POST['data']['externals']['dubious']) && count($_POST['data']['externals']['dubious']['tokens'])) : $errors = 0; ?>
		<div>
		<img class="alignleft" alt="" src="<?php echo CSP_PO_BASE_URL."/images/dubious-scripts.png"; ?>" />
		<strong style="color:#800;"><?php _e('Malfunction at dubious external scripts detected !' ,CSP_PO_TEXTDOMAIN); ?></strong><br/>
		<?php _e('Reason:',CSP_PO_TEXTDOMAIN);?> <strong><?php _e('unknown external script has been enqueued or hardly attached.', CSP_PO_TEXTDOMAIN); ?></strong> | 
		<?php _e('Polluter:',CSP_PO_TEXTDOMAIN);?> <strong><?php _e('unknown', CSP_PO_TEXTDOMAIN); ?></strong> <small>(<?php _e('probably by Theme or Plugin',CSP_PO_TEXTDOMAIN); ?>)</small><br/>
		<?php _e('Below listed external scripts have been traced, verified and automatically stripped because of injection:',CSP_PO_TEXTDOMAIN); ?><br/>
		<ol>
		<?php  
		for($i=0;$i<count($_POST['data']['externals']['dubious']['tokens']); $i++) :
				$token = $_POST['data']['externals']['dubious']['tokens'][$i];
				$script = $_POST['data']['externals']['dubious']['scripts'][$i];
				$res = wp_remote_head($script, array('sslverify' => false));
				$style = (($res === false || (is_object($res) && get_class($res) == 'WP_Error') || $res['response']['code'] != 200) ? ' style="color: #800;"': '' ) ;
				if(!empty($style)) $errors += 1; 
		?>
			<li<?php echo $style; ?>>[<strong><?php echo strip_tags(stripslashes($token)); ?></strong>] - <span class="cdn-file"><?php echo strip_tags(stripslashes($script));?></span> <img src="<?php echo CSP_PO_BASE_URL."/images/status-".(empty($style) ? '200' : '404').'.gif'; ?>" /></li>
		<?php endfor; ?>
		</ol>
		<?php if ($errors > 0) : ?>
		<p style="color:#800;font-weight:bold;"><?php 
			$text = sprintf(_n('%d file', '%d files', $errors, CSP_PO_TEXTDOMAIN), $errors);
			echo sprintf(__('This page will not work as expected because %s could not be get from CDN. Check and update the Plugin doing your CDN redirection!',CSP_PO_TEXTDOMAIN), $text); 
		?></p>
		<?php endif; ?>
		</div>
	<?php endif; ?>		
<?php
	if (isset($_POST['data']['externals']) && isset($_POST['data']['externals']['cdn']) && count($_POST['data']['externals']['cdn']['tokens'])) : $errors = 0; ?>
		<div style="border-top: 1px dashed gray; padding-top: 10px;">
		<img class="alignleft" alt="" src="<?php echo CSP_PO_BASE_URL."/images/cdn-scripts.png"; ?>" />
		<strong style="color:#008;"><?php _e('CDN based script loading redirection detected!' ,CSP_PO_TEXTDOMAIN); ?></strong><br/>
		<?php _e('Warning:',CSP_PO_TEXTDOMAIN);?> <strong><?php _e('may break the dependency script loading feature within WordPress core files.', CSP_PO_TEXTDOMAIN); ?></strong><br/>
		<?php _e('Below listed redirects have been traced and verified but not revoked:',CSP_PO_TEXTDOMAIN); ?><br/>
		<ol>
		<?php  
		for($i=0;$i<count($_POST['data']['externals']['cdn']['tokens']); $i++) :
				$token = $_POST['data']['externals']['cdn']['tokens'][$i];
				$script = $_POST['data']['externals']['cdn']['scripts'][$i];
				$res = wp_remote_head($script, array('sslverify' => false));
				$style = (($res === false || (is_object($res) && get_class($res) == 'WP_Error') || $res['response']['code'] != 200) ? ' style="color: #800;"': '' ) ;
				if(!empty($style)) $errors += 1; 
		?>
			<li<?php echo $style; ?>>[<strong><?php echo strip_tags(stripslashes($token)); ?></strong>] - <span class="cdn-file"><?php echo strip_tags(stripslashes($script));?></span> <img src="<?php echo CSP_PO_BASE_URL."/images/status-".(empty($style) ? '200' : '404').'.gif'; ?>" /></li>
		<?php endfor; ?>
		</ol>
		<?php if ($errors > 0) : ?>
		<p style="color:#800;font-weight:bold;"><?php 
			$text = sprintf(_n('%d file', '%d files', $errors, CSP_PO_TEXTDOMAIN), $errors);
			echo sprintf(__('This page will not work as expected because %s could not be get from CDN. Check and update the Plugin doing your CDN redirection!',CSP_PO_TEXTDOMAIN), $text); 
		?></p>
		<?php endif; ?>
		</div>
	<?php endif; ?>		
</div>
<?php
	exit();
}
function csp_redirect_prototype_js($src, $handle) {
	global $wp_version;
	if (version_compare($wp_version, '3.5-alpha', '>=')) {
		$handles = array(
			'prototype' 			=> 'prototype',
			'scriptaculous-root' 	=> 'wp-scriptaculous',
			'scriptaculous-effects' => 'effects'
		);
		//load own older versions of the scripts that are working!
		if (isset($handles[$handle])) {
			return CSP_PO_BASE_URL.'/js/'.$handles[$handle].'.js';
		}
	}
	return $src;
}

function csp_load_po_edit_admin_page(){

	add_filter('print_scripts_array', 'csp_filter_print_scripts_array', 0);
	add_action('admin_enqueue_scripts', 'csp_start_protection', 0);
	add_action('in_admin_footer', 'csp_start_protection', 0);
	add_action('admin_head', 'csp_self_script_protection_head', 9999);
	add_action('admin_print_footer_scripts', 'csp_self_script_protection_footer', 9999);
	add_filter('script_loader_src', 'csp_redirect_prototype_js', 10, 9999);

	wp_enqueue_script( 'thickbox' );
	wp_enqueue_script('jquery-ui-dialog');
	wp_enqueue_script('prototype');
	wp_enqueue_script('scriptaculous-effects');
	if (function_exists('wp_enqueue_style')) {
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_style('codestyling-localization-ui', CSP_PO_BASE_URL.'/css/ui.all.css');
		wp_enqueue_style('codestyling-localization', CSP_PO_BASE_URL.'/css/plugin.css');
		if(function_exists('is_rtl') && is_rtl())
			wp_enqueue_style('codestyling-localization-rtl', CSP_PO_BASE_URL.'/css/plugin-rtl.css');
	}
	
	//new help system
	global $wp_version;
	if (version_compare($wp_version, '3.3', '>=')) {
		$screen = get_current_screen();
		//$request = unserialize(csp_fetch_remote_content('http://api.wordpress.org/plugins/info/1.0/codestyling-localization'));
		$screen->add_help_tab(array(
			'title' => __('Overview',CSP_PO_TEXTDOMAIN),
			'id' => 'overview',
			'content' => '',
			'callback' => 'csp_callback_help_overview'
		));
		$screen->add_help_tab(array(
			'title' => __('Low Memory Mode', CSP_PO_TEXTDOMAIN),
			'id' => 'lowmemory',
			'content' => '',
			'callback' => 'csp_callback_help_low_memory'
		));
		
		$content = array();
		$screen->add_help_tab(array(
			'title' => __('Compatibility', CSP_PO_TEXTDOMAIN),
			'id' => 'compatibility',
			'content' => '',
			'callback' => 'csp_callback_help_compatibility'
		));
		$screen->add_help_tab(array(
			'title' => __('Textdomains', CSP_PO_TEXTDOMAIN),
			'id' => 'textdomain',
			'content' => '',
			'callback' => 'csp_callback_help_textdomain'
		));
		$screen->add_help_tab(array(
			'title' => __('Translation Format', CSP_PO_TEXTDOMAIN),
			'id' => 'translationformat',
			'content' => '',
			'callback' => 'csp_callback_help_translationformat'
		));	
		if (CSL_FILESYSTEM_DIRECT !== true) {
			$screen->add_help_tab(array(
				'title' => __('File Permissions', CSP_PO_TEXTDOMAIN),
				'id' => 'filepermissions',
				'content' => '',
				'callback' => 'csp_callback_help_filepermissions'
			));
		}
		$screen->add_help_tab(array(
			'title' => __('Child Themes', CSP_PO_TEXTDOMAIN),
			'id' => 'workonchildthemes',
			'content' => '',
			'callback' => 'csp_callback_help_workonchildthemes'
		));	
		$screen->add_help_tab(array(
			'title' => __('Scripting Guard', CSP_PO_TEXTDOMAIN),
			'id' => 'selfprotection',
			'content' => '',
			'callback' => 'csp_callback_help_selfprotection'
		));	
		$content = array();
		$content[]= "<p><strong>".__("For more information:",CSP_PO_TEXTDOMAIN)."</strong></p>";
		$content[]= "<p><a target=\"_blank\" href=\"http://www.code-styling.de/\">Code Styling Project</a></p>";
		$content[]= "<p><a target=\"_blank\" href=\"http://plugins.trac.wordpress.org/log/codestyling-localization/\">".__("Trac Development Log",CSP_PO_TEXTDOMAIN)."</a></p>";
		$content[]= "<p><a target=\"_blank\" href=\"http://wordpress.org/support/plugin/codestyling-localization\">".__("Plugin Support Forum",CSP_PO_TEXTDOMAIN)."</a></p>";		
		$screen->set_help_sidebar(implode('', $content));
	}else{
		//WP 2.7 help extensions
		add_filter('screen_meta_screen', 'csp_po_filter_screen_meta_screen');
		add_filter('contextual_help_list', 'csp_po_filter_help_list_filter');
	}
	
}

function csp_po_admin_head() {
	if (!function_exists('wp_enqueue_style') 
		&& 
		preg_match("/^codestyling\-localization\/codestyling\-localization\.php/", $_GET['page'])
	) {
		print '<link rel="stylesheet" href="'.get_site_url()."/wp-includes/js/thickbox/thickbox.css".'" type="text/css" media="screen"/>';
		print '<link rel="stylesheet" href="'.CSP_PO_BASE_URL.'/css/ui.all.css'.'" type="text/css" media="screen"/>';
		print '<link rel="stylesheet" href="'.CSP_PO_BASE_URL.'/css/plugin.css'.'" type="text/css" media="screen"/>';
		if(function_exists('is_rtl') && is_rtl())
			print '<link rel="stylesheet" href="'.CSP_PO_BASE_URL.'/css/plugin-rtl.css'.'" type="text/css" media="screen"/>';
	}
}

function csp_po_admin_menu() {
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages', 'codestyling-localization/languages');
	$hook = add_management_page(__('WordPress Localization',CSP_PO_TEXTDOMAIN), __('Localization', CSP_PO_TEXTDOMAIN), 'manage_options', __FILE__, 'csp_po_main_page');
	add_action('load-'.$hook, 'csp_load_po_edit_admin_page'); //only load the scripts and stylesheets by hook, if this admin page will be shown
	
	//User Profile extension if necessary
	if (defined('TRANSLATION_API_PER_USER') && (TRANSLATION_API_PER_USER === true) /*&& current_user_can('manage_options')*/) {
		add_action('show_user_profile', 'csp_extend_user_profile');
		add_action('personal_options_update', 'csp_save_user_profile');
	}	
}
function csp_extend_user_profile($profileuser) {
	if (!@is_object($profiluser)) {
		$profileuser = wp_get_current_user();
	}
	$func = function_exists('get_user_meta') ? 'get_user_meta' : 'get_usermeta';
?>
<h3 id="translations"><?php _e('Translation API Keys', CSP_PO_TEXTDOMAIN); ?><br/><small><em>(Codestyling Localization)</em></small></h3>
<table class="form-table">
<tr>
<th><label for="google-api-key"><?php _e('Google Translate API Key', CSP_PO_TEXTDOMAIN); ?></label></th>
<td><input type="text" class="regular-text" name="csp-google-api-key" id="csp-google-api-key" value="<?php echo call_user_func($func, $profileuser->ID, 'csp-google-api-key', true); ?>" autocomplete="off" />
</tr>
<tr>
<th><label for="microsoft-api-client-id"><?php _e('Microsoft Translator - Client ID', CSP_PO_TEXTDOMAIN); ?></label></th>
<td><input type="text" class="regular-text" name="csp-microsoft-api-client-id" id="csp-microsoft-api-client-id" value="<?php echo call_user_func($func, $profileuser->ID, 'csp-microsoft-api-client-id', true); ?>" autocomplete="off" />
</tr>
<tr>
<th><label for="microsoft-api-client-secret"><?php _e('Microsoft Translator - Client Secret', CSP_PO_TEXTDOMAIN); ?></label></th>
<td><input type="text" class="regular-text" name="csp-microsoft-api-client-secret" id="csp-microsoft-api-client-secret" value="<?php echo call_user_func($func, $profileuser->ID, 'csp-microsoft-api-client-secret', true); ?>" autocomplete="off" />
</tr>
</table>
<?php
}

function csp_save_user_profile() {
	$myself = wp_get_current_user();
	$func = function_exists('update_user_meta') ? 'update_user_meta' : 'update_usermeta';
	if (isset($_POST['csp-google-api-key'])) {
		call_user_func($func, $myself->ID, 'csp-google-api-key', $_POST['csp-google-api-key']);
	}
	if (isset($_POST['csp-microsoft-api-client-id'])) {
		call_user_func($func, $myself->ID, 'csp-microsoft-api-client-id', $_POST['csp-microsoft-api-client-id']);
	}
	if (isset($_POST['csp-microsoft-api-client-secret'])) {
		call_user_func($func, $myself->ID, 'csp-microsoft-api-client-secret', $_POST['csp-microsoft-api-client-secret']);
	}
}

function csp_get_translate_api_type() {
	$api_type = (string)get_option('codestyling-localization.translate-api', 'none');
	switch($api_type) {
		case 'google':
			if(!defined('GOOGLE_TRANSLATE_KEY')) $api_type = 'none';
			break;
		case 'microsoft':
			if(!defined('MICROSOFT_TRANSLATE_CLIENT_ID') || !defined('MICROSOFT_TRANSLATE_CLIENT_SECRET') || !function_exists('curl_version')) $api_type = 'none';
			break;
		default:
			$api_type = 'none';
			break;
	}
	return $api_type;
}

function csp_po_main_page() {
	csp_po_check_security();
	$mo_list_counter = 0;
	global $csp_l10n_sys_locales, $wp_version;
	$csp_wp_main_page = (version_compare($wp_version, '2.7 ', '>=') ? "tools" : "edit");
	
	$google_api = defined('GOOGLE_TRANSLATE_KEY');
	$microsoft_api = defined('MICROSOFT_TRANSLATE_CLIENT_ID') && defined('MICROSOFT_TRANSLATE_CLIENT_SECRET') && function_exists('curl_version');
	$api_type = csp_get_translate_api_type();
?>
<div id="csp-wrap-main" class="wrap">
<div class="icon32" id="icon-tools"><br/></div>
<h2><?php _e('Manage Language Files', CSP_PO_TEXTDOMAIN); ?></h2>
<?php if (CSL_FILESYSTEM_DIRECT !== true) : ?>
	<div>
	<p class="warning"><strong><?php _e('File Permission Problem:',CSP_PO_TEXTDOMAIN);?></strong> <?php _e('Your WordPress installation does not permit the modification of translation files directly. You will be prompt for FTP credentials if required.', CSP_PO_TEXTDOMAIN); ?>&nbsp;<a align="left" class="question-help" href="javascript:void(0);" title="<?php _e("What does that mean?",CSP_PO_TEXTDOMAIN) ?>" rel="filepermissions"><img src="<?php echo CSP_PO_BASE_URL."/images/question.gif"; ?>" /></a></p>
	</div>
<?php endif; ?>
<p>
	<input id= "enable_low_memory_mode" type="checkbox" name="enable_low_memory_mode" value="1" <?php if (CSL_LOW_MEMORY) echo 'checked="checked"'; ?>> <label for="enable_low_memory_mode"><?php _e('enable low memory mode', CSP_PO_TEXTDOMAIN); ?></label> <img id="enable_low_memory_mode_indicator" style="display:none;" alt="" src="<?php echo CSP_PO_BASE_URL."/images/loading-small.gif"?>" />
	<?php if (version_compare($wp_version, '3.3', '<')) : ?>
	<br /><small><?php _e('If your Installation is running under low remaining memory conditions, you will face the memory limit error during scan process or opening catalog content. If you hitting your limit, you can enable this special mode. This will try to perform the actions in a slightly different way but that will lead to a considerably slower response times but nevertheless gives no warranty, that it will solve your memory related problems at all cases.', CSP_PO_TEXTDOMAIN); ?></small>
	<?php else : ?>
	&nbsp;<a align="left" class="question-help" href="javascript:void(0);" title="<?php _e("What does that mean?",CSP_PO_TEXTDOMAIN) ?>" rel="lowmemory"><img src="<?php echo CSP_PO_BASE_URL."/images/question.gif"; ?>" /></a>
	<?php endif; ?>
</p>
<p class="translation-apis">
	<label class="alignleft"><strong><?php _e('Translation Service-APIs:',CSP_PO_TEXTDOMAIN); ?></strong></label> 
	<img class="alignleft" alt="" title="API: not used" src="<?php echo CSP_PO_BASE_URL."/images/off.png"; ?>" /><input id="translate-api-none" class="translate-api-none alignleft" name="translate-api" value="none" type="radio" autocomplete="off" <?php checked('none', $api_type); ?>/> <label class="alignleft" for="translate-api-none"><?php _e('None',CSP_PO_TEXTDOMAIN); ?></label>
	<img class="alignleft" alt="" title="API: Google Translate" src="<?php echo CSP_PO_BASE_URL."/images/google.png"; ?>" /><input id="translate-api-google" class="translate-api-google alignleft" name="translate-api" value="google" type="radio" autocomplete="off" <?php checked('google', $api_type); ?> <?php disabled(false, $google_api); ?>/> <label class="alignleft<?php if(!$google_api) echo ' disabled'; ?>" for="translate-api-google"><?php _e('Google',CSP_PO_TEXTDOMAIN); ?></label>
	<img class="alignleft" alt="" title="API: Microsoft Translate" src="<?php echo CSP_PO_BASE_URL."/images/bing.gif"; ?>" /><input id="translate-api-microsoft" class="translate-api-microsoft alignleft" name="translate-api" value="microsoft" type="radio" autocomplete="off" <?php checked('microsoft', $api_type); ?> <?php disabled(false, $microsoft_api); ?>/> <label class="alignleft<?php if(!$microsoft_api) echo ' disabled'; ?>" for="translate-api-microsoft"><?php _e('Microsoft',CSP_PO_TEXTDOMAIN); ?></label>
	<?php if(defined('TRANSLATION_PROVIDER_MODE') && TRANSLATION_PROVIDER_MODE === true) : ?>
		<?php if(defined('TRANSLATION_API_PER_USER') && TRANSLATION_API_PER_USER === true) : ?>
		<a class="alignright" href="profile.php?#translations"><?php _e('User Profile settings...',CSP_PO_TEXTDOMAIN); ?></a><img class="alignright" alt="" title="API: How to use" src="<?php echo CSP_PO_BASE_URL."/images/user.gif"; ?>" />
		<?php endif; ?>
	<?php else: ?>
	<a class="alignright" id="explain-apis" href="#"><?php _e('How to use translation API services...',CSP_PO_TEXTDOMAIN); ?></a><img class="alignright" alt="" title="API: How to use" src="<?php echo CSP_PO_BASE_URL."/images/question.png"; ?>" />
	<?php endif; ?>
</p>
<?php if(!defined('TRANSLATION_PROVIDER_MODE') || TRANSLATION_PROVIDER_MODE === false) : ?>
<div class="translation-apis-info">
	<h5><?php _e("a) Global Unique Keys - single user configuration", CSP_PO_TEXTDOMAIN); ?></h5>
	<div style="margin-left: 25px;">
		<small style="color: #f33;">
		<strong><?php _e('Attention:', CSP_PO_TEXTDOMAIN); ?></strong> <?php _e('Keep in mind, that any WordPress administrator can use the service for translation purpose and may raise your costs in case of paid option used.', CSP_PO_TEXTDOMAIN); ?>
		</small>
		<br/><br/>
		<h5>Google Translate API | <small><a target="_blank" href="https://developers.google.com/translate/v2/faq">FAQ</a></small></h5>
		<p>
			<small>
			<strong><?php _e('Attention:', CSP_PO_TEXTDOMAIN); ?></strong>
			<?php echo sprintf(__('This API is not longer a free service, Google has relaunched the API in version 2 as a pay per use service. Please read the explantions at %s first.', CSP_PO_TEXTDOMAIN), '<a target="_blank" href="https://developers.google.com/translate/v2/terms">Terms of Service</a>'); ?>
			<?php _e('Using this API within <em>Codestyling Localization</em> requires an API Key to be created at your Google account first. Once you have such a key, you can activate this API by defining a new constant at your <b>wp-config.php</b> file:', CSP_PO_TEXTDOMAIN); ?>
			</br/>
			<textarea class="google" readonly="readonly">define('GOOGLE_TRANSLATE_KEY', 'enter your key here');</textarea>
			</small>
		</p>
		<h5>Microsoft Translate API | <small><a target="_blank" href="http://social.msdn.microsoft.com/Forums/en-US/microsofttranslator/thread/c71aeddd-cc90-4228-93cc-51fb969fde09">FAQ</a></small></h5>
		<p>
			<small>
			<?php  echo sprintf(__('Microsoft provides the translation services with a free option of 2 million characters per month. But this also requires a subscription at %s either for free or for extended payed service volumes.', CSP_PO_TEXTDOMAIN), '<a target="_blank" href="http://go.microsoft.com/?linkid=9782667">Azure Marketplace</a>'); ?>
			<?php _e('Using this API within <em>Codestyling Localization</em> requires <em>client_id</em> and <em>client_secret</em> to be created at your Azure subscription first. Once you have this values, you can activate this API by defining new constants at your <b>wp-config.php</b> file:', CSP_PO_TEXTDOMAIN); ?>
			</br/>
			<textarea class="microsoft" readonly="readonly">
define('MICROSOFT_TRANSLATE_CLIENT_ID', 'enter your client id here');
define('MICROSOFT_TRANSLATE_CLIENT_SECRET', 'enter your secret here');
			</textarea>
			<br/>
			<strong><?php _e('Attention:', CSP_PO_TEXTDOMAIN); ?></strong> <?php _e('This API additionally requires PHP curl functions and will not be available without. Current curl version:', CSP_PO_TEXTDOMAIN); ?>
			&nbsp;<b><i><?php if (function_exists('curl_version')) { $ver = curl_version(); echo $ver['version']; } else _e('not installed',CSP_PO_TEXTDOMAIN); ?></i></b>
			</small>
		</p>
	</div>
	<h5><?php _e("b) User Dedicated Keys - multiple user configurations", CSP_PO_TEXTDOMAIN); ?></h5>
	<div style="margin-left: 25px;">
		<small style="color: #f33;">
		<strong><?php _e('Attention:', CSP_PO_TEXTDOMAIN); ?></strong> <?php _e('This will extends all <em>User Profile</em> pages with a new section to enter all required translation key data. Keep im mind, that this data are stored at the database and are contained at SQL backups.', CSP_PO_TEXTDOMAIN); ?>
		</small>
		<p>
		<small>
			<?php _e('You can activate the per user behavoir, if you define only a single constant at your <b>wp-config.php</b> file. This enables the new section at each <a target="_blank" href="profile.php?#translations">User Profile</a> with sufficiant permissions and is only editable by the releated logged in user.',CSP_PO_TEXTDOMAIN); ?>
			<textarea class="google" readonly="readonly">define('TRANSLATION_API_PER_USER', true);</textarea>
		</small>
		</p>
	</div>
	<h5 style="border-top: 1px dashed gray;padding-top: 5px;"><?php _e("Special Hosting Configuration", CSP_PO_TEXTDOMAIN); ?></h5>
	<div style="margin-left: 25px;">
		<small>
			<?php _e('If your are a provider and you are hosting WordPress installations for your customer, it is possible to deactivate this help information using an additional constant at your <b>wp-config.php</b> file. At single user mode (a) this simply does not show any help for API configuration, at multiuser mode (b) it shows the link to the profile page.', CSP_PO_TEXTDOMAIN); ?>
			<textarea class="google" readonly="readonly">define('TRANSLATION_PROVIDER_MODE', true);</textarea>
		</small>
	</div>
</div>
<?php endif; ?>
<ul class="subsubsub">
<li>
	<a<?php if(!isset($_GET['type'])) echo " class=\"current\""; ?> href="<?php echo $csp_wp_main_page ?>.php?page=codestyling-localization/codestyling-localization.php"><?php  _e('All Translations', CSP_PO_TEXTDOMAIN); ?>
	</a> | </li>
<li>
	<a<?php if(isset($_GET['type']) && $_GET['type'] == 'wordpress') echo " class=\"current\""; ?> href="<?php echo $csp_wp_main_page ?>.php?page=codestyling-localization/codestyling-localization.php&amp;type=wordpress"><?php _e('WordPress', CSP_PO_TEXTDOMAIN); ?>
	</a> | </li>
<?php if (csp_is_multisite()) { ?>
<li>
	<a<?php if(isset($_GET['type']) && $_GET['type'] == 'plugins_mu') echo " class=\"current\""; ?> href="<?php echo $csp_wp_main_page ?>.php?page=codestyling-localization/codestyling-localization.php&amp;type=plugins_mu"><?php _e('μ Plugins', CSP_PO_TEXTDOMAIN); ?>
	</a> | </li>
<?php } ?>
<li>
	<a<?php if(isset($_GET['type']) && $_GET['type'] == 'plugins') echo " class=\"current\""; ?> href="<?php echo $csp_wp_main_page ?>.php?page=codestyling-localization/codestyling-localization.php&amp;type=plugins"><?php _e('Plugins', CSP_PO_TEXTDOMAIN); ?>
	</a> | </li>
<li>
	<a<?php if(isset($_GET['type']) && $_GET['type'] == 'themes') echo " class=\"current\""; ?> href="<?php echo $csp_wp_main_page ?>.php?page=codestyling-localization/codestyling-localization.php&amp;type=themes"><?php _e('Themes', CSP_PO_TEXTDOMAIN); ?>
	</a> | </li>
<li>
	<a<?php if(isset($_GET['type']) && $_GET['type'] == 'compat') echo " class=\"current\""; ?> href="<?php echo $csp_wp_main_page ?>.php?page=codestyling-localization/codestyling-localization.php&amp;type=compat"><?php _e('Compatibility', CSP_PO_TEXTDOMAIN); ?>
	</a> | </li>
<li>
	<a<?php if(isset($_GET['type']) && $_GET['type'] == 'security') echo " class=\"current\""; ?> href="<?php echo $csp_wp_main_page ?>.php?page=codestyling-localization/codestyling-localization.php&amp;type=security"><?php _e('Security Risk', CSP_PO_TEXTDOMAIN); ?>
	</a></li>
</ul>
<div style="float:<?php if (function_exists('is_rtl') && is_rtl()) echo 'left'; else echo 'right'; ?>;">
<small><em><?php _e('You like it?', CSP_PO_TEXTDOMAIN); ?></em></small>
<form style="float:right;" method="post" action="https://www.paypal.com/cgi-bin/webscr">
<input type="hidden" value="" name="amount">
<input type="hidden" value="_xclick" name="cmd">
<input type="hidden" value="donate@code-styling.de" name="business">
<input type="hidden" value="Donation www.code-styling.de - Plugin: Codestyling Localization" name="item_name">
<input type="hidden" value="1" name="no_shipping">
<input type="hidden" value="http://www.code-styling.de/" name="return">
<input type="hidden" value="http://www.code-styling.de/" name="cancel_return">
<input type="hidden" value="USD" name="currency_code">
<input type="hidden" value="0" name="tax">
<input type="hidden" value="PP-DonationsBF" name="bn">
<?php $valid_loc_for_button = array('en_US', 'de_DE', 'it_IT', 'fr_FR', 'es_ES', 'zh_TW', 'zh_CN', 'he_IL', 'nl_NL'); ?>
<?php $loc = get_locale(); if(!in_array($loc, $valid_loc_for_button)) { if ($loc == 'de_DE' || $loc == 'de') { $loc = 'de_DE'; } else { $loc = 'en_US'; } } ?>
<input border="0" type="image" alt="Make payments with PayPal - it's fast, free and secure!" name="submit" src="https://www.paypal.com/<?php echo $loc ?>/i/btn/btn_donate_SM.gif">
</form>
<br/>
</div>
<table class="widefat clear" style="cursor:default;" cellspacing="0">
<thead>
  <tr>
    <th scope="col"><?php _e('Type',CSP_PO_TEXTDOMAIN); ?></th>
    <th scope="col"><?php _e('Description',CSP_PO_TEXTDOMAIN); ?></th>
	<th scope="col"><?php _e('Languages',CSP_PO_TEXTDOMAIN); ?></th>
  </tr>
</thead>
<tbody class="list" id="the-gettext-list">
<?php 
	$rows = csp_po_collect_by_type(isset($_GET['type']) ? $_GET['type'] : ''); 
	if (isset($_GET['type']) && $_GET['type'] == 'compat') $_GET['type'] = '';
	foreach($rows as $data) : 
?>
<tr<?php if (__("activated",CSP_PO_TEXTDOMAIN) == $data['status']) echo " class=\"csp-active\""; ?>>
	<td align="center"><img alt="" src="<?php echo CSP_PO_BASE_URL."/images/".$data['img_type'].".gif"; ?>" /><div><strong><?php echo $data['type-desc']; ?></strong></div></td>
	<td>
		<h3 class="csp-type-name"><?php echo $data['name']; ?><span style="font-weight:normal;">&nbsp;&nbsp;&copy;&nbsp;</span><sup><em><?php echo $data['author']; ?></em></sup></h3>
		<table class="csp-type-info" border="0" width="100%">
			<tr>
				<td width="140px"><strong><?php _e('Textdomain',CSP_PO_TEXTDOMAIN); ?>:</strong></td>
				<td class="csp-info-value"><?php echo $data['textdomain']['identifier']; ?><?php if ($data['textdomain']['is_const']) echo " (".__('defined by constant',CSP_PO_TEXTDOMAIN).")"; ?></td>
			</tr>
			<tr>
				<td><strong><?php _e('Version',CSP_PO_TEXTDOMAIN); ?>:</strong></td>
				<td class="csp-info-value"><?php echo $data['version']; ?></td>
			</tr>
			<tr>
				<td><strong><?php _e('State',CSP_PO_TEXTDOMAIN); ?>:</strong></td>
				<td class="csp-info-value csp-info-status"><?php echo $data['status']; ?></td>
			</tr>
			<tr>
				<td colspan="2" class="csp-desc-value"><small><?php echo call_user_func('__', $data['description'], $data['textdomain']['identifier']);?></small></td>
			</tr>
			<?php if (isset($data['dev-hints'])) : ?>
			<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
			<tr>
				<td><strong style="color: #f00;"><?php _e('Compatibility',CSP_PO_TEXTDOMAIN); ?>:</strong>&nbsp;<a align="left" class="question-help" href="javascript:void(0);" title="<?php _e("What does that mean?",CSP_PO_TEXTDOMAIN) ?>" rel="compatibility"><img src="<?php echo CSP_PO_BASE_URL."/images/question.gif"; ?>" /></a></td>
				<td class="csp-info-value"><?php echo $data['dev-hints'];?></td>
			</tr>
			<?php endif; ?>
			<?php if (isset($data['dev-security'])) : ?>
			<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
			<tr>
				<td><strong style="color: #f00;"><?php _e('Security Risk',CSP_PO_TEXTDOMAIN); ?>:</strong></td>
				<td class="csp-info-value"><?php echo $data['dev-security'];?></td>
			</tr>
			<?php endif; ?>
			<?php  if ($data['type'] == 'wordpress-xxx') : ?>
			<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
			<tr>
				<td><strong style="color: #f00;"><?php _e('Memory Warning',CSP_PO_TEXTDOMAIN); ?>:</strong></td>
				<td class="csp-info-value"><?php _e('Since WordPress 3.x version it may require at least <strong>58MB</strong> PHP memory_limit! The reason is still unclear but it doesn\'t freeze anymore. Instead a error message will be shown and the scanning process aborts while reaching your limits.',CSP_PO_TEXTDOMAIN); ?></td>
			<tr>
			<?php endif; ?>
			<?php if ($data['is-path-unclear']) : ?>
			<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
			<tr>
				<td><strong style="color: #f00;"><?php _e('Language Folder',CSP_PO_TEXTDOMAIN); ?>:</strong></td>
				<td class="csp-info-value"><?php _e('The translation file folder is ambiguous, please select by clicking the appropriated language file folder or ask the Author about!',CSP_PO_TEXTDOMAIN); ?></td>
			<tr>
			<?php endif; ?>
		</table>
		<?php if (isset($data['child-plugins'])) { foreach($data['child-plugins'] as $child) { ?>
		<div style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #ccc;">
			<h3 class="csp-type-name"><?php echo $child['name']; ?> <small><em><?php _e('by',CSP_PO_TEXTDOMAIN); ?> <?php echo $child['author']; ?></em></small></h3>
			<table class="csp-type-info" border="0">
				<tr>
					<td><strong><?php _e('Version',CSP_PO_TEXTDOMAIN); ?>:</strong></td>
					<td width="100%" class="csp-info-value"><?php echo $child['version']; ?></td>
				</tr>
				<tr>
					<td><strong><?php _e('State',CSP_PO_TEXTDOMAIN); ?>:</strong></td>
					<td class="csp-info-value csp-info-status"><?php echo $child['status']; ?></td>
				</tr>
				<tr>
					<td colspan="2" class="csp-desc-value"><small><?php echo call_user_func('__', $child['description'], $data['textdomain']['identifier']);?></small></td>
				</tr>
			</table>
		</div>
		<?php } } ?>
	</td>
	<td class="component-details">
		<?php  if ($data['type'] == 'wordpress' && $data['is_US_Version'] ) {?>
			<div style="color:#f00;"><?php _e("The original US version doesn't contain the language directory.",CSP_PO_TEXTDOMAIN); ?></div>
			<br/>
			<div><a class="clickable button" onclick="csp_create_languange_path(this, '<?php echo str_replace("\\", '/', WP_CONTENT_DIR)."/languages" ?>');"><?php _e('try to create the WordPress language directory',CSP_PO_TEXTDOMAIN); ?></a></div>
			<br/>
			<div>
				<?php _e('or create the missing directory using FTP Access as:',CSP_PO_TEXTDOMAIN); ?>
				<br/><br/>
				<?php echo str_replace("\\", '/', WP_CONTENT_DIR)."/"; ?><strong style="color:#f00;">languages</strong>			
			</div>
		<?php } elseif($data['is-path-unclear']) { ?>
			<strong style="border-bottom: 1px solid #ccc;"><?php _e('Available Directories:',CSP_PO_TEXTDOMAIN) ?></strong><br/><br/>
			<?php 
				$tmp = array(); 
				$dirs = rscanpath($data['base_path'], $tmp);
				$dir = $data['base_path'];
				echo '<a class="clickable pot-folder" onclick="csp_create_pot_indicator(this,\''.$dir.$data['base_file'].'xx_XX.pot\');">'. str_replace(str_replace("\\","/",WP_PLUGIN_DIR), '', $dir)."</a><br/>";
				foreach($dirs as $dir) { 
					echo '<a class="clickable pot-folder" onclick="csp_create_pot_indicator(this,\''.$dir.'/'.$data['base_file'].'xx_XX.pot\');">'. str_replace(str_replace("\\","/",WP_PLUGIN_DIR), '', $dir)."</a><br/>";
				} 
			?>
		<?php } elseif($data['name'] == 'bbPress' && isset($data['is_US_Version']) && $data['is_US_Version']) { ?>	
			<div style="color:#f00;"><?php _e("The original bbPress component doesn't contain a language directory.",CSP_PO_TEXTDOMAIN); ?></div>
			<br/>
			<div><a class="clickable button" onclick="csp_create_languange_path(this, '<?php echo $data['base_path']."my-languages"; ?>');"><?php _e('try to create the bbPress language directory',CSP_PO_TEXTDOMAIN); ?></a></div>
			<br/>
			<div>
				<?php _e('or create the missing directory using FTP Access as:',CSP_PO_TEXTDOMAIN); ?>
				<br/><br/>
				<?php echo $data['base_path']; ?><strong style="color:#f00;">my-languages</strong>			
			</div>			
		<?php	} else { ?>
		<table width="100%" cellspacing="0" class="mo-list" id="mo-list-<?php echo ++$mo_list_counter; ?>" summary="<?php echo $data['textdomain']['identifier'].'|'.$data['type'].'|'.$data['name'].' v'.$data['version']; ?>">
			<tr class="mo-list-head">
				<td colspan="4" nowrap="nowrap">
					<img alt="GNU GetText" class="alignleft" src="<?php echo CSP_PO_BASE_URL; ?>/images/gettext.gif" style="display:none;" />
					<a rel="<?php echo implode('|', array_keys($data['languages']));?>" class="clickable mofile button" onclick="csp_add_language(this,'<?php echo $data['type']; ?>','<?php echo rawurlencode($data['name'])." v".$data['version']."','mo-list-".$mo_list_counter."','".$data['base_path']."','".$data['base_file']."',this.rel,'".$data['type']."','".$data['simple-filename']."','".$data['translation_template']."','".$data['textdomain']['identifier']."',".($data['deny_scanning'] ? '1' : '0') ?>);"><?php _e("Add New Language", CSP_PO_TEXTDOMAIN); ?></a>
					<?php if (isset($data['theme-self']) && ($data['theme-self'] != $data['theme-template'])) : ?>
					&nbsp;<a class="clickable mofile button" onclick="csp_merge_maintheme_languages(this,'<?php echo $data['theme-template']; ?>','<?php echo $data['theme-self']; ?>','<?php echo $data['base_path']; if(!empty($data['special_path'])) echo $data['special_path'].'/' ?>','<?php echo $data['textdomain']['identifier']; ?>','mo-list-<?php echo $mo_list_counter; ?>');"><?php _e("Sync Files with Main Theme", CSP_PO_TEXTDOMAIN); ?></a>
					<a rel="workonchildthemes" title="<?php _e("What does that mean?",CSP_PO_TEXTDOMAIN) ?>" href="javascript:void(0);" class="question-help" align="left"><img src="http://wp34.de/wp-content/plugins/codestyling-localization/images/question.gif"></a>
					<?php endif; ?>
				</td>
				<td colspan="1" nowrap="nowrap" class="csp-ta-right"><?php echo sprintf(_n('<strong>%d</strong> Language', '<strong>%d</strong> Languages',count($data['languages']),CSP_PO_TEXTDOMAIN), count($data['languages'])); ?></td>
			</tr>
			<tr class="mo-list-desc">
				<td nowrap="nowrap" width="16px" align="center"><img src="<?php echo CSP_PO_BASE_URL."/images/google.png"; ?>" /></td>
				<td nowrap="nowrap" width="16px" align="center" class="lang-info-api"><img src="<?php echo CSP_PO_BASE_URL."/images/bing.gif"; ?>" /></td>
				<td nowrap="nowrap" align="left" class="lang-info-desc"><?php _e('Language',CSP_PO_TEXTDOMAIN);?></td>
				<td nowrap="nowrap" align="center"><?php _e('Permissions',CSP_PO_TEXTDOMAIN);?></td>
				<td nowrap="nowrap" align="center"><?php _e('Actions',CSP_PO_TEXTDOMAIN);?></td>
			</tr>
			<?php 
				foreach($data['languages'] as $lang => $gtf) : 
					$country_www = isset($csp_l10n_sys_locales[$lang]) ? $csp_l10n_sys_locales[$lang]['country-www'] : 'unknown';
					$lang_native = isset($csp_l10n_sys_locales[$lang]) ? $csp_l10n_sys_locales[$lang]['lang-native'] : '<em>locale: </em>'.$lang;
			?>
			<?php if ($data['textdomain']['identifier'] == 'woocommerce' && $lang == 'de_DE') : ?>
			<!-- special case woocommerce german: start -->
			<?php $copy_base_file = $data['base_file']; $data['base_file'] = 'languages/informal/woocommerce-'; ?>
			<tr class="mo-file" lang="<?php echo $lang; ?>">
				<td nowrap="nowrap" width="16px" align="center"><img src="<?php echo CSP_PO_BASE_URL."/images/".(isset($csp_l10n_sys_locales[$lang]) && !empty($csp_l10n_sys_locales[$lang]['google-api']) ? 'yes' : 'no').'.png'; ?>" /></td>
				<td nowrap="nowrap" width="16px"align="center" class="lang-info-api"><img src="<?php echo CSP_PO_BASE_URL."/images/".(isset($csp_l10n_sys_locales[$lang]) && !empty($csp_l10n_sys_locales[$lang]['microsoft-api']) ? 'yes' : 'no').'.png'; ?>" /></td>
				<td nowrap="nowrap" width="100%" class="lang-info-desc"><img title="<?php _e('Locale',CSP_PO_TEXTDOMAIN); ?>: <?php echo $lang ?>" alt="(locale: <?php echo $lang; ?>)" src="<?php echo CSP_PO_BASE_URL."/images/flags/".$country_www.".gif"; ?>" /><?php if (get_locale() == $lang) echo "<strong>"; ?>&nbsp;<?php echo $lang_native.' '.__('(informal)',CSP_PO_TEXTDOMAIN); ?><?php if (get_locale() == $lang) echo "</strong>"; ?></td>
				<td nowrap="nowrap" align="center">
					<div style="width:44px">
						<?php if (array_key_exists('po', $gtf)) {
							echo "<a class=\"csp-filetype-po".$gtf['po']['class']."\" title=\"".$gtf['po']['stamp'].($gtf['po']['class'] == '-r' ? '" onclick="csp_make_writable(this,\''.$data['base_path'].$data['base_file'].$lang.".po".'\',\'csp-filetype-po-rw\');' : '')."\">&nbsp;</a>";
						} else { ?>
						<a class="csp-filetype-po" title="<?php _e('-n.a.-',CSP_PO_TEXTDOMAIN); ?> [---|---|---]">&nbsp;</a>
						<?php } ?>
						<?php if (array_key_exists('mo', $gtf)) {
							echo "<a class=\"csp-filetype-mo".$gtf['mo']['class']."\" title=\"".$gtf['mo']['stamp'].($gtf['mo']['class'] == '-r' ? '" onclick="csp_make_writable(this,\''.$data['base_path'].$data['base_file'].$lang.".mo".'\',\'csp-filetype-mo-rw\');' : '')."\">&nbsp;</a>";
						} else { ?>
						<a class="csp-filetype-mo" title="<?php _e('-n.a.-',CSP_PO_TEXTDOMAIN); ?> [---|---|---]">&nbsp;</a>
						<?php } ?>
					</div>
				</td>
				<td nowrap="nowrap" style="padding-right: 5px;">
					<a class="clickable button" onclick="csp_launch_editor(this, '<?php echo $data['base_file'].$lang.".po" ;?>', '<?php echo $data['base_path']; ?>','<?php echo $data['textdomain']['identifier']; ?>');"><?php _e('Edit',CSP_PO_TEXTDOMAIN); ?></a>
					<span>&nbsp;</span>
					<?php if (!$data['deny_scanning']) : ?>
					<a class="clickable button" onclick="csp_rescan_language(this,'<?php echo rawurlencode($data['name'])." v".$data['version']."','mo-list-".$mo_list_counter."','".$data['base_path']."','".$data['base_file']."','".$lang."','".$data['type']."','".$data['simple-filename']."'"; ?>)"><?php _e('Rescan',CSP_PO_TEXTDOMAIN); ?></a>
					<span>&nbsp;</span>
					<?php else: ?>
					<span style="text-decoration: line-through;"><?php _e('Rescan',CSP_PO_TEXTDOMAIN); ?></span>
					<span>&nbsp;</span>
					<?php endif; ?>
					<a class="clickable button" onclick="csp_remove_language(this,'<?php echo rawurlencode($data['name'])." v".$data['version']."','mo-list-".$mo_list_counter."','".$data['base_path']."','".$data['base_file']."','".$lang."'"; ?>)"><?php _e('Delete',CSP_PO_TEXTDOMAIN); ?></a>
				</td>
			</tr>
			<?php $data['base_file'] = 'languages/formal/woocommerce-'; ?>
			<tr class="mo-file" lang="<?php echo $lang; ?>">
				<td nowrap="nowrap" width="16px" align="center"><img src="<?php echo CSP_PO_BASE_URL."/images/".(isset($csp_l10n_sys_locales[$lang]) && !empty($csp_l10n_sys_locales[$lang]['google-api']) ? 'yes' : 'no').'.png'; ?>" /></td>
				<td nowrap="nowrap" width="16px" align="center" class="lang-info-api"><img src="<?php echo CSP_PO_BASE_URL."/images/".(isset($csp_l10n_sys_locales[$lang]) && !empty($csp_l10n_sys_locales[$lang]['microsoft-api']) ? 'yes' : 'no').'.png'; ?>" /></td>
				<td nowrap="nowrap" width="100%" class="lang-info-desc"><img title="<?php _e('Locale',CSP_PO_TEXTDOMAIN); ?>: <?php echo $lang ?>" alt="(locale: <?php echo $lang; ?>)" src="<?php echo CSP_PO_BASE_URL."/images/flags/".$country_www.".gif"; ?>" /><?php if (get_locale() == $lang) echo "<strong>"; ?>&nbsp;<?php echo $lang_native.' '.__('(formal)',CSP_PO_TEXTDOMAIN); ?><?php if (get_locale() == $lang) echo "</strong>"; ?></td>
				<td nowrap="nowrap" align="center">
					<div style="width:44px">
						<?php if (array_key_exists('po', $gtf)) {
							echo "<a class=\"csp-filetype-po".$gtf['po']['class']."\" title=\"".$gtf['po']['stamp'].($gtf['po']['class'] == '-r' ? '" onclick="csp_make_writable(this,\''.$data['base_path'].$data['base_file'].$lang.".po".'\',\'csp-filetype-po-rw\');' : '')."\">&nbsp;</a>";
						} else { ?>
						<a class="csp-filetype-po" title="<?php _e('-n.a.-',CSP_PO_TEXTDOMAIN); ?> [---|---|---]">&nbsp;</a>
						<?php } ?>
						<?php if (array_key_exists('mo', $gtf)) {
							echo "<a class=\"csp-filetype-mo".$gtf['mo']['class']."\" title=\"".$gtf['mo']['stamp'].($gtf['mo']['class'] == '-r' ? '" onclick="csp_make_writable(this,\''.$data['base_path'].$data['base_file'].$lang.".mo".'\',\'csp-filetype-mo-rw\');' : '')."\">&nbsp;</a>";
						} else { ?>
						<a class="csp-filetype-mo" title="<?php _e('-n.a.-',CSP_PO_TEXTDOMAIN); ?> [---|---|---]">&nbsp;</a>
						<?php } ?>
					</div>
				</td>
				<td nowrap="nowrap" style="padding-right: 5px;">
					<a class="clickable button" onclick="csp_launch_editor(this, '<?php echo $data['base_file'].$lang.".po" ;?>', '<?php echo $data['base_path']; ?>','<?php echo $data['textdomain']['identifier']; ?>');"><?php _e('Edit',CSP_PO_TEXTDOMAIN); ?></a>
					<span>&nbsp;</span>
					<?php if (!$data['deny_scanning']) : ?>
					<a class="clickable button" onclick="csp_rescan_language(this,'<?php echo rawurlencode($data['name'])." v".$data['version']."','mo-list-".$mo_list_counter."','".$data['base_path']."','".$data['base_file']."','".$lang."','".$data['type']."','".$data['simple-filename']."'"; ?>)"><?php _e('Rescan',CSP_PO_TEXTDOMAIN); ?></a>
					<span>&nbsp;</span>
					<?php else: ?>
					<span style="text-decoration: line-through;"><?php _e('Rescan',CSP_PO_TEXTDOMAIN); ?></span>
					<span>&nbsp;</span>
					<?php endif; ?>
					<a class="clickable button" onclick="csp_remove_language(this,'<?php echo rawurlencode($data['name'])." v".$data['version']."','mo-list-".$mo_list_counter."','".$data['base_path']."','".$data['base_file']."','".$lang."'"; ?>)"><?php _e('Delete',CSP_PO_TEXTDOMAIN); ?></a>
				</td>
			</tr>
			<?php $data['base_file'] =  $copy_base_file;?>
			<tr class="mo-file" lang="<?php echo $lang; ?>">
				<td colspan="2" class="lang-info-api">&nbsp;</td>
				<td width="100%" colspan="3" class="lang-info-desc"><small><strong style="color:#f00;"><?php _e('Warning',CSP_PO_TEXTDOMAIN); ?>: </strong><?php _e('German translations are currently supported by a temporary workaround only, because they will be handled completely uncommon beside WordPress standards!',CSP_PO_TEXTDOMAIN); ?></small></td>
			</tr>
			<!-- special case woocommerce german: end -->
			<?php else : ?>
			<tr class="mo-file" lang="<?php echo $lang; ?>">
				<td nowrap="nowrap" width="16px" align="center"><img src="<?php echo CSP_PO_BASE_URL."/images/".(isset($csp_l10n_sys_locales[$lang]) && !empty($csp_l10n_sys_locales[$lang]['google-api']) ? 'yes' : 'no').'.png'; ?>" /></td>
				<td nowrap="nowrap" width="16px" align="center" class="lang-info-api"><img src="<?php echo CSP_PO_BASE_URL."/images/".(isset($csp_l10n_sys_locales[$lang]) && !empty($csp_l10n_sys_locales[$lang]['microsoft-api']) ? 'yes' : 'no').'.png'; ?>" /></td>
				<td nowrap="nowrap" width="100%" class="lang-info-desc"><img title="<?php _e('Locale',CSP_PO_TEXTDOMAIN); ?>: <?php echo $lang ?>" alt="(locale: <?php echo $lang; ?>)" src="<?php echo CSP_PO_BASE_URL."/images/flags/".$country_www.".gif"; ?>" /><?php if (get_locale() == $lang) echo "<strong>"; ?>&nbsp;<?php echo $lang_native; ?><?php if (get_locale() == $lang) echo "</strong>"; ?></td>
				<td nowrap="nowrap" align="center">
					<div style="width:44px">
						<?php if (array_key_exists('po', $gtf)) {
							echo "<a class=\"csp-filetype-po".$gtf['po']['class']."\" title=\"".$gtf['po']['stamp'].($gtf['po']['class'] == '-r' ? '" onclick="csp_make_writable(this,\''.$data['base_path'].$data['base_file'].$lang.".po".'\',\'csp-filetype-po-rw\');' : '')."\">&nbsp;</a>";
						} else { ?>
						<a class="csp-filetype-po" title="<?php _e('-n.a.-',CSP_PO_TEXTDOMAIN); ?> [---|---|---]">&nbsp;</a>
						<?php } ?>
						<?php if (array_key_exists('mo', $gtf)) {
							echo "<a class=\"csp-filetype-mo".$gtf['mo']['class']."\" title=\"".$gtf['mo']['stamp'].($gtf['mo']['class'] == '-r' ? '" onclick="csp_make_writable(this,\''.$data['base_path'].$data['base_file'].$lang.".mo".'\',\'csp-filetype-mo-rw\');' : '')."\">&nbsp;</a>";
						} else { ?>
						<a class="csp-filetype-mo" title="<?php _e('-n.a.-',CSP_PO_TEXTDOMAIN); ?> [---|---|---]">&nbsp;</a>
						<?php } ?>
					</div>
				</td>
				<td nowrap="nowrap" style="padding-right: 5px;">
					<a class="clickable button" onclick="csp_launch_editor(this, '<?php echo $data['base_file'].$lang.".po" ;?>', '<?php echo $data['base_path']; ?>','<?php echo $data['textdomain']['identifier']; ?>');"><?php _e('Edit',CSP_PO_TEXTDOMAIN); ?></a>
					<span>&nbsp;</span>
					<?php if (!$data['deny_scanning']) : ?>
						<?php if (isset($data['theme-self']) && ($data['theme-self'] != $data['theme-template'])) : ?>
							<a class="clickable button" onclick="csp_rescan_language(this,'<?php echo rawurlencode($data['name'])." v".$data['version']."','mo-list-".$mo_list_counter."','".$data['base_path']."','".$data['base_file']."','".$lang."','".$data['type']."','".$data['simple-filename']."','".$data['theme-template']."'"; ?>)"><?php _e('Rescan',CSP_PO_TEXTDOMAIN); ?></a>
						<?php else: ?>
							<a class="clickable button" onclick="csp_rescan_language(this,'<?php echo rawurlencode($data['name'])." v".$data['version']."','mo-list-".$mo_list_counter."','".$data['base_path']."','".$data['base_file']."','".$lang."','".$data['type']."','".$data['simple-filename']."',''"; ?>)"><?php _e('Rescan',CSP_PO_TEXTDOMAIN); ?></a>
						<?php endif; ?>
					<span>&nbsp;</span>
					<?php else: ?>
					<span style="text-decoration: line-through;"><?php _e('Rescan',CSP_PO_TEXTDOMAIN); ?></span>
					<span>&nbsp;</span>
					<?php endif; ?>
					<a class="clickable button" onclick="csp_remove_language(this,'<?php echo rawurlencode($data['name'])." v".$data['version']."','mo-list-".$mo_list_counter."','".$data['base_path']."','".$data['base_file']."','".$lang."'"; ?>)"><?php _e('Delete',CSP_PO_TEXTDOMAIN); ?></a>
				</td>
			</tr>
			<?php endif; ?>
			<?php endforeach; ?>		
		</table>
		<?php } ?>
	</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div><!-- csp-wrap-main closed -->
<div id="csp-wrap-editor" class="wrap" style="display:none">
	<div class="icon32" id="icon-tools"><br/></div>
	<h2><?php _e('Translate Language File', CSP_PO_TEXTDOMAIN); ?>&nbsp;&nbsp;&nbsp;<a class="clickable button" onclick="window.location.reload()"><?php _e('back to overview page &raquo;', CSP_PO_TEXTDOMAIN) ?></a></h2>
	<div id="csp-json-header">
		<div class="po-header-toggle"><span><b><?php _e('Project-Id-Version:',CSP_PO_TEXTDOMAIN); ?></b></span> <span id="prj-id-ver">---</span> | <strong><?php _e('File:', CSP_PO_TEXTDOMAIN); ?></strong> <a onclick="csp_toggle_header(this,'po-hdr');"><?php _e('unknown', CSP_PO_TEXTDOMAIN); ?></a></div>
	</div>
	<div class="action-bar">
		<?php if (version_compare($wp_version, '3.3', '<')) : ?>
		<p>
			<small>
			<?php _e('<b>Hint:</b> The extended feature for textdomain separation shows at dropdown box <i>Textdomain</i> the pre-selected primary textdomain.',CSP_PO_TEXTDOMAIN); ?><br/>
			<?php _e('All other additional contained textdomains occur at the source but will not be used, if not explicitely supported by this component!',CSP_PO_TEXTDOMAIN); ?><br/>
			<?php _e('Please contact the author, if some of the non primary textdomain based phrases will not show up translated at the required position!',CSP_PO_TEXTDOMAIN); ?><br/>
			<?php _e('The Textdomain <i><b>default</b></i> always stands for the WordPress main language file, this could be either intentionally or accidentally!',CSP_PO_TEXTDOMAIN); ?><br/>
			</small>
		</p>
		<?php endif; ?>
		<p id="textdomain-error" class="hidden"><small><?php 
			_e('<strong>Error</strong>: The actual loaded translation content does not match the textdomain:',CSP_PO_TEXTDOMAIN); 
			echo '&nbsp;<span></span><br/>';
			_e('Expect, that any text you translate will not occure as long as the textdomain is mismatching!',CSP_PO_TEXTDOMAIN); 
			echo '<br/>';
			_e('This is a coding issue at the source files you try to translate, please contact the original Author and explain this mismatch.',CSP_PO_TEXTDOMAIN); 
		?>&nbsp;<a class="question-help" href="javascript:void(0);" title="<?php _e("What does that mean?",CSP_PO_TEXTDOMAIN) ?>" rel="textdomain"><img src="<?php echo CSP_PO_BASE_URL."/images/question.gif"; ?>" /></a></small></p>
		<p id="textdomain-warning" class="hidden"><small><?php 
			_e('<strong>Warning</strong>: The actual loaded translation content contains mixed textdomains and is not pure translateable within one textdomain.',CSP_PO_TEXTDOMAIN); 
			echo '<br/>';
			_e('It seems, that there is code contained extracted out of other plugins, themes or widgets and used by copy & paste inside some source files.',CSP_PO_TEXTDOMAIN); 
			echo '<br/>';
			_e('The affected unknown textdomains are:',CSP_PO_TEXTDOMAIN); 
			echo '&nbsp;<span>&nbsp;</span>';		
		?>&nbsp;<a class="question-help" href="javascript:void(0);" title="<?php _e("What does that mean?",CSP_PO_TEXTDOMAIN) ?>" rel="textdomain"><img src="<?php echo CSP_PO_BASE_URL."/images/question.gif"; ?>" /></a></small></p>
		<div class="alignleft"id="csp-mo-textdomain"><span><b><?php _e('Textdomain:',CSP_PO_TEXTDOMAIN); ?></b>&nbsp;&nbsp;<a class="question-help" href="javascript:void(0);" title="<?php _e("What does that mean?",CSP_PO_TEXTDOMAIN) ?>" rel="textdomain"><img src="<?php echo CSP_PO_BASE_URL."/images/question.gif"; ?>" /></a><span>&nbsp;&nbsp;<select id="csp-mo-textdomain-val" onchange="csp_change_textdomain_view(this.value);"></select></div>
		<div class="alignleft">&nbsp;&nbsp;<input id="csp-write-mo-file" class="button button-secondary" style="display:none" type="submit" value="<?php _e('generate mo-file', CSP_PO_TEXTDOMAIN); ?>" onclick="csp_generate_mofile(this);" /></div>
		<div class="alignleft" style="margin-left:10px;font-size:11px;padding-top:3px;"><?php _e('last written:',CSP_PO_TEXTDOMAIN);?>&nbsp;&nbsp;<span id="catalog-last-saved" ><?php _e('unknown',CSP_PO_TEXTDOMAIN); ?></span><img id="csp-generate-mofile" src="<?php echo CSP_PO_BASE_URL."/images/";?>write-mofile.gif" /></div>
		<br class="clear" />
	</div>
	<ul class="subsubsub">
		<li><a id="csp-filter-all" class="csp-filter current" onclick="csp_filter_result(this, csp_idx.total)"><?php _e('Total', CSP_PO_TEXTDOMAIN); ?> ( <span class="csp-flt-cnt">0</span> )</a> | </li>
		<li><a id="csp-filter-plurals" class="csp-filter" onclick="csp_filter_result(this, csp_idx.plurals)"><?php _e('Plural', CSP_PO_TEXTDOMAIN); ?> ( <span class="csp-flt-cnt">0</span> )</a> | </li>
		<li><a id="csp-filter-ctx" class="csp-filter" onclick="csp_filter_result(this, csp_idx.ctx)"><?php _e('Context', CSP_PO_TEXTDOMAIN); ?> ( <span class="csp-flt-cnt">0</span> )</a> | </li>
		<li><a id="csp-filter-open" class="csp-filter" onclick="csp_filter_result(this, csp_idx.open)"><?php _e('Not translated', CSP_PO_TEXTDOMAIN); ?> ( <span class="csp-flt-cnt">0</span> )</a> | </li>
		<li><a id="csp-filter-rem" class="csp-filter" onclick="csp_filter_result(this, csp_idx.rem)"><?php _e('Comments', CSP_PO_TEXTDOMAIN); ?> ( <span class="csp-flt-cnt">0</span> )</a> | </li>
		<li><a id="csp-filter-code" class="csp-filter" onclick="csp_filter_result(this, csp_idx.code)"><?php _e('Code Hint', CSP_PO_TEXTDOMAIN); ?> ( <span class="csp-flt-cnt">0</span> )</a> | </li>
		<li><a id="csp-filter-trail" class="csp-filter" onclick="csp_filter_result(this, csp_idx.trail)"><?php _e('Trailing Space', CSP_PO_TEXTDOMAIN); ?> ( <span class="csp-flt-cnt">0</span> )</a></li>
		<li style="display:none;"> | <span id="csp-filter-search" class="current"><?php _e('Search Result', CSP_PO_TEXTDOMAIN); ?>  ( <span class="csp-flt-cnt">0</span> )</span></li>
		<li style="display:none;"> | <span id="csp-filter-regexp" class="current"><?php _e('Expression Result', CSP_PO_TEXTDOMAIN); ?>  ( <span class="csp-flt-cnt">0</span> )</span></li>
	</ul>
	<div class="tablenav">
		<div class="alignleft">
			<div class="alignleft" style="padding-top: 5px;font-size:11px;"><strong><?php _e('Page Size', CSP_PO_TEXTDOMAIN); ?>:&nbsp;</strong></div>
			<select id="catalog-pagesize" name="catalog-pagesize" onchange="csp_change_pagesize(this.value);" class="alignleft" style="font-size:11px;" autocomplete="off">
				<option value="10">10</option>
				<option value="25">25</option>
				<option value="50">50</option>
				<option value="75">75</option>
				<option value="100" selected="selected">100</option>
				<option value="150">150</option>
				<option value="200">200</option>
			</select>
		</div>
		<div id="catalog-pages-top" class="tablenav-pages alignright">
			<a href="#" class="prev page-numbers"><?php _e('&laquo; Previous', CSP_PO_TEXTDOMAIN); ?></a>
			<a href="#" class="page-numbers">1</a>
			<a href="#" class="page-numbers">2</a>
			<a href="#" class="page-numbers">3</a>
			<span class="page-numbers current">4</span>
			<a href="#" class="next page-numbers"><?php _e('Next &raquo;', CSP_PO_TEXTDOMAIN); ?></a>
		</div>
		<br class="clear" />
	</div>
	<table class="widefat" cellspacing="0">
		<thead>
			<tr>
				<th nowrap="nowrap"><span><?php _e('Infos',CSP_PO_TEXTDOMAIN); ?></span></th>
				<th width="50%">
					<table>
						<tr>
							<th style="background:transparent;border-bottom:0px;padding:0px;"><?php _e('Original:',CSP_PO_TEXTDOMAIN); ?></th>
							<th style="background:transparent;border-bottom:0px;padding:0px;vertical-align:top;">
								<input id="s_original" name="s_original" type="text" size="16" value="" onkeyup="csp_search_result(this)" style="margin-bottom:3px;" autocomplete="off" />
								<br/>
								<input id="ignorecase_key" name="ignorecase_key" type="checkbox" value="" onclick="csp_search_key('s_original')" /><label for="ignorecase_key" style="font-weight:normal;margin-top:-2px;"> <?php _e('non case-sensitive', CSP_PO_TEXTDOMAIN) ?></label>
							</th>
							<th style="background:transparent;border-bottom:0px;padding:0px;vertical-align:top;">
								<a class="clickable regexp" onclick="csp_search_regexp('s_original')"></a>
							</th>
						</tr>
					</table>
				</th>
				<th width="50%">
					<table>
						<tr>
							<th style="background:transparent;border-bottom:0px;padding:0px;"><?php _e('Translation:',CSP_PO_TEXTDOMAIN); ?></th>
							<th style="background:transparent;border-bottom:0px;padding:0px;vertical-align:top;">
								<input id="s_translation" name="s_translation" type="text" size="16" value="" onkeyup="csp_search_result(this)" style="margin-bottom:3px;" autocomplete="off" />
								<br/>
								<input id="ignorecase_val" name="ignorecase_val" type="checkbox" value="" onclick="csp_search_val('s_translation')" /><label for="ignorecase_val" style="font-weight:normal;margin-top:-2px;"> <?php _e('non case-sensitive', CSP_PO_TEXTDOMAIN) ?></label>
							</th>
							<th style="background:transparent;border-bottom:0px;padding:0px;vertical-align:top;">
								<a class="clickable regexp" onclick="csp_search_regexp('s_translation')"></a>
							</th>
						</tr>
					</table>
				</th>
				<th nowrap="nowrap"><span><?php _e('Actions',CSP_PO_TEXTDOMAIN); ?></span></th>
			</tr>
		</thead>
		<tbody id="catalog-body">
			<tr><td colspan="4" align="center"><img alt="" src="<?php echo CSP_PO_BASE_URL."/images/loading.gif"?>" /><br /><span style="color:#328AB2;"><?php _e('Please wait, file content presently being loaded ...',CSP_PO_TEXTDOMAIN); ?></span></td></tr>
		</tbody>
	</table>	
	<div class="tablenav">
		<a class="alignright button" href="javascript:void(0);" onclick="window.scrollTo(0,0);" style="margin:3px 0 0 30px;"><?php _e('scroll to top', CSP_PO_TEXTDOMAIN); ?></a>
		<div id="catalog-pages-bottom" class="tablenav-pages">
			<a href="#" class="prev page-numbers"><?php _e('&laquo; Previous', CSP_PO_TEXTDOMAIN); ?></a>
			<a href="#" class="page-numbers">1</a>
			<a href="#" class="page-numbers">2</a>
			<a href="#" class="page-numbers">3</a>
			<span class="page-numbers current">4</span>
			<a href="#" class="next page-numbers"><?php _e('Next &raquo;', CSP_PO_TEXTDOMAIN); ?></a>
		</div>
		<br class="clear" />
	</div>
	<br class="clear" />
</div><!-- csp-wrap-editor closed -->
<div id="csp-dialog-container" style="display:none;">
	<div>
		<h3 id="csp-dialog-header">
			<img alt="" id="csp-dialog-icon" class="alignleft" src="<?php echo CSP_PO_BASE_URL; ?>/images/gettext.gif" />
			<span id="csp-dialog-caption" class="alignleft"><?php _e('Edit Catalog Entry',CSP_PO_TEXTDOMAIN); ?></span>
			<img alt="" id="csp-dialog-cancel" class="alignright clickable" title="<?php _e('close', CSP_PO_TEXTDOMAIN); ?>" src="<?php echo CSP_PO_BASE_URL."/images/close.gif"; ?>" onclick="csp_cancel_dialog();" />
			<br class="clear" />
		</h3>	
		<div id="csp-dialog-body"></div>
		<div style="text-align:center;"><img id="csp-dialog-saving" src="<?php echo CSP_PO_BASE_URL; ?>/images/saving.gif" style="margin-top:20%;display:none;" /></div>
	</div>
</div><!-- csp-dialog-container closed -->
<div id="csp-credentials"></div><!-- credential for filesystem -->
<br />
<script type="text/javascript">
/* <![CDATA[ */

//ajax call parameter
var csp_ajax_params = {
	'action' 			: '',
	'file'				: '',
	'type'				: '',
	'name'				: '',
	'row'				: '',
	'path'				: '',
	'subpath'			: '',
	'existing'			: '',
	'simplefilename'	: '',
	'transtemplate'		: '',
	'textdomain'		: '',
	'denyscan'			: '',
	'timestamp'			: '',
	'translator'		: '',
	'language'			: '',
	'numlangs'			: '',

	'pofile'			: '',
	'potfile'			: '',
	'num'				: '',
	'cnt'				: '',
	'php'				: '',
	
	'isplural'			: '',
	'msgid'				: '',
	'msgstr'			: '',
	'msgidx'			: '',
	'destlang'			: ''

};

Object.extend(Array.prototype, {
  intersect: function(array){
    return this.findAll( function(token){ return array.include(token) } );
  }
});

//write mofile indication
$('csp-generate-mofile').hide();

//--- management based functions ---
function csp_make_writable(elem, file, success_class) {
	elem = $(elem);
	elem.blur();
	
	if(csp_ajax_params.action.length) {
		jQuery('#csp-credentials > form').find('input').each(function(i, e) {
			if ((jQuery(e).attr('type') == 'radio') && !jQuery(e).attr('checked')) return;
			var s = jQuery(e).attr('name');
			var v = jQuery(e).val();
			csp_ajax_params[s] = v;
		});		
	}else{
		csp_ajax_params.action = 'csp_po_change_permission';
		csp_ajax_params.file = file;
	}

	new Ajax.Request('<?php echo CSP_PO_ADMIN_URL.'/admin-ajax.php' ?>', 
		{  
			parameters: csp_ajax_params,
			onSuccess: function(transport) {		
				elem.className=success_class;
				elem.title=transport.responseJSON.title;
				elem.onclick = null;
			},
			onFailure: function(transport) {
				if (transport.status == '401') {
					jQuery('#csp-credentials').html(transport.responseText).dialog({
						width: '500px',
						closeOnEscape: false,
						modal: true,
						resizable: false,
						title: '<b><?php echo esc_js(__('User Credentials required', CSP_PO_TEXTDOMAIN)); ?></b>',
						buttons: { 
							"<?php echo esc_js(__('Ok', CSP_PO_TEXTDOMAIN)); ?>": function() { 
								jQuery('#csp-credentials').dialog("close");
								jQuery(elem).trigger('click');
							},
							"<?php echo esc_js(__('Cancel', CSP_PO_TEXTDOMAIN)); ?>": function() { 
								jQuery('#csp-credentials').dialog("close"); 
								csp_ajax_params.action = '';
							} 
						},
						open: function(event, ui) {
							jQuery('#csp-credentials').show().css('width', 'auto');
						},
						close: function() {
							jQuery('#csp-credentials').dialog("destroy");
						}
					});
					jQuery('#upgrade').hide().attr('disabled', 'disabled');	
				}else {
					csp_show_error(transport.responseText);
					csp_ajax_params.action = '';
				}
			}
		}
	);
	return false;	
}

function csp_add_language(elem, type, name, row, path, subpath, existing, type, simplefilename, transtemplate, textdomain, denyscan) {
	elem = $(elem);
	elem.blur();
	new Ajax.Request('<?php echo CSP_PO_ADMIN_URL.'/admin-ajax.php' ?>', 
		{  
			parameters: {
				action: 'csp_po_dlg_new',
				type: type,
				name: name,
				row: row,
				path: path,
				subpath: subpath,
				existing: existing,
				type: type,
				simplefilename: simplefilename,
				transtemplate: transtemplate,
				textdomain: textdomain,
				denyscan: denyscan
			},
			onSuccess: function(transport) {
				$('csp-dialog-caption').update("<?php _e('Add New Language',CSP_PO_TEXTDOMAIN); ?>");
				$("csp-dialog-body").update(transport.responseText).setStyle({'padding' : '10px'});
				tb_show(null,"#TB_inline?height=530&width=500&inlineId=csp-dialog-container&modal=true",false);
			}
		}
	); 	
	return false;
}

function csp_merge_maintheme_languages(elem, source, dest, basepath, textdomain, molist) {
	
	elem = $(elem);
	elem.blur();
	
	if(csp_ajax_params.action.length) {
		jQuery('#csp-credentials > form').find('input').each(function(i, e) {
			if ((jQuery(e).attr('type') == 'radio') && !jQuery(e).attr('checked')) return;
			var s = jQuery(e).attr('name');
			var v = jQuery(e).val();
			csp_ajax_params[s] = v;
		});		
	}else{
		csp_ajax_params.action = 'csp_po_merge_from_maintheme';
		csp_ajax_params.source = source;
		csp_ajax_params.dest = dest;
		csp_ajax_params.basepath = basepath;
		csp_ajax_params.textdomain = textdomain;
		csp_ajax_params.molist = molist;
	}
	new Ajax.Request('<?php echo CSP_PO_ADMIN_URL.'/admin-ajax.php' ?>', 
		{  
			parameters: csp_ajax_params,
			onSuccess: function(transport) {
				//remeber the last edited component by id hash 
				//old jquery is unable to do that in WP 2.5
				csp_ajax_params.action = '';
				try{ window.location.hash = csp_ajax_params.molist; } catch(e) {}
				window.location.reload();
			},
			onFailure: function(transport) {
				if (transport.status == '401') {
					jQuery('#csp-credentials').html(transport.responseText).dialog({
						width: '500px',
						closeOnEscape: false,
						modal: true,
						resizable: false,
						title: '<b><?php echo esc_js(__('User Credentials required', CSP_PO_TEXTDOMAIN)); ?></b>',
						buttons: { 
							"<?php echo esc_js(__('Ok', CSP_PO_TEXTDOMAIN)); ?>": function() { 
								jQuery('#csp-credentials').dialog("close");
								jQuery(elem).trigger('click');
							},
							"<?php echo esc_js(__('Cancel', CSP_PO_TEXTDOMAIN)); ?>": function() { 
								jQuery('#csp-credentials').dialog("close"); 
								csp_ajax_params.action = '';
							} 
						},
						open: function(event, ui) {
							jQuery('#csp-credentials').show().css('width', 'auto');
						},
						close: function() {
							jQuery('#csp-credentials').dialog("destroy");
						}
					});
					jQuery('#upgrade').hide().attr('disabled', 'disabled');	
				}else {
					csp_show_error(transport.responseText);
					csp_ajax_params.action = '';
				}
			}
		}
	);
}

function csp_create_new_pofile(elem, type){
	elem = $(elem);
	elem.blur();
	
	if(csp_ajax_params.action.length) {
		jQuery('#csp-credentials > form').find('input').each(function(i, e) {
			if ((jQuery(e).attr('type') == 'radio') && !jQuery(e).attr('checked')) return;
			var s = jQuery(e).attr('name');
			var v = jQuery(e).val();
			csp_ajax_params[s] = v;
		});		
	}else{
		csp_ajax_params.action = 'csp_po_create';
		csp_ajax_params.name = $('csp-dialog-name').value;
		csp_ajax_params.timestamp = $('csp-dialog-timestamp').value,
		csp_ajax_params.translator = $('csp-dialog-translator').value,
		csp_ajax_params.path = $('csp-dialog-path').value,
		csp_ajax_params.subpath = $('csp-dialog-subpath').value,
		csp_ajax_params.language = $('csp-dialog-language').value,
		csp_ajax_params.row = $('csp-dialog-row').value,
		csp_ajax_params.numlangs = $('csp-dialog-numlangs').value,
		csp_ajax_params.type  = type,
		csp_ajax_params.simplefilename = $('csp-dialog-simplefilename').value,
		csp_ajax_params.transtemplate  =  $('csp-dialog-transtemplate').value,
		csp_ajax_params.textdomain  =  $('csp-dialog-textdomain').value,
		csp_ajax_params.denyscan = $('csp-dialog-denyscan').value
	}
	
	new Ajax.Request('<?php echo CSP_PO_ADMIN_URL.'/admin-ajax.php' ?>', 
		{  
			parameters: csp_ajax_params,
			onSuccess: function(transport) {	
				jQuery('#'+transport.responseJSON.row+' .mo-list-head  td.csp-ta-right').html(transport.responseJSON.head);
				rel = $$('#'+transport.responseJSON.row+' .mo-list-head').first().down(2).rel;
				$$('#'+transport.responseJSON.row+' .mo-list-head').first().down(2).rel += ((rel.empty() ? '' : "|" ) + transport.responseJSON.language);
				elem_after = null;
								
				content = "<tr class=\"mo-file\" lang=\""+transport.responseJSON.language+"\">"+
					"<td nowrap=\"nowrap\" width=\"16px\" align=\"center\"><img src=\"<?php echo CSP_PO_BASE_URL."/images/"; ?>"+transport.responseJSON.google+".png\" /></td>"+
					"<td nowrap=\"nowrap\" width=\"16px\" align=\"center\" class=\"lang-info-api\"><img src=\"<?php echo CSP_PO_BASE_URL."/images/"; ?>"+transport.responseJSON.microsoft+".png\" /></td>"+
					"<td nowrap=\"nowrap\" width=\"100%\"  class=\"lang-info-desc\">"+
						"<img title=\"<?php _e('Locale',CSP_PO_TEXTDOMAIN); ?>: "+transport.responseJSON.language+"\" alt=\"(locale: "+transport.responseJSON.language+")\" src=\""+transport.responseJSON.image+"\" />" +
						("<?php echo get_locale(); ?>" == transport.responseJSON.language ? "<strong>" : "") + 
						"&nbsp;" + transport.responseJSON.lang_native +
						("<?php echo get_locale(); ?>" == transport.responseJSON.language ? "</strong>" : "") + 
					"</td>"+
					"<td align=\"center\">"+
						"<div style=\"width:44px\">"+
						"<a class=\"csp-filetype-po-rw\" title=\""+transport.responseJSON.permissions+"\">&nbsp;</a>"+
						"<a class=\"csp-filetype-mo\" title=\"<?php _e('-n.a.-',CSP_PO_TEXTDOMAIN); ?> [---|---|---]\">&nbsp;</a>"+
						"</div>"+
					"</td>"+
					"<td nowrap=\"nowrap\">"+
						"<a class=\"clickable button\" onclick=\"csp_launch_editor(this, '"+transport.responseJSON.subpath+transport.responseJSON.language+".po"+"', '"+transport.responseJSON.path+"','"+transport.responseJSON.textdomain+"');\"><?php _e('Edit',CSP_PO_TEXTDOMAIN); ?></a>"+
						"\n<span>&nbsp;</span>\n"+(transport.responseJSON.denyscan == false ? 
						"<a class=\"clickable button\" onclick=\"csp_rescan_language(this,'"+escape(transport.responseJSON.name)+"','"+transport.responseJSON.row+"','"+transport.responseJSON.path+"','"+transport.responseJSON.subpath+"','"+transport.responseJSON.language+"','"+transport.responseJSON.type+"','"+transport.responseJSON.simplefilename+"')\"><?php _e('Rescan',CSP_PO_TEXTDOMAIN); ?></a>"+
						"\n<span>&nbsp;</span>\n" 
						: 
						"<span style=\"text-decoration: line-through;\"><?php _e('Rescan',CSP_PO_TEXTDOMAIN); ?></span>"+
						"\n<span>&nbsp;</span>\n" 
						) +
						"<a class=\"clickable button\" onclick=\"csp_remove_language(this,'"+escape(transport.responseJSON.name)+"','"+transport.responseJSON.row+"','"+transport.responseJSON.path+"','"+transport.responseJSON.subpath+"','"+transport.responseJSON.language+"');\"><?php _e('Delete',CSP_PO_TEXTDOMAIN); ?></a>"+
					"</td>"+
					"</tr>";			
				$$('#'+transport.responseJSON.row+' .mo-file').each(function(tr) {
					if ((tr.lang > transport.responseJSON.language) && !Object.isElement(elem_after)) {	elem_after = tr; }
				});
				ne = null;
				if (Object.isElement(elem_after)) { ne = elem_after.insert({ 'before' : content }).previous(); }
				else { ne = $$('#'+transport.responseJSON.row+' tbody').first().insert(content).childElements().last(); }
				new Effect.Highlight(ne, { startcolor: '#25FF00', endcolor: '#FFFFCF' });
				csp_ajax_params.action = ''; //reset
			},
			onFailure: function(transport) {
				if (transport.status == '401') {
					jQuery('#csp-credentials').html(transport.responseText).dialog({
						width: '500px',
						closeOnEscape: false,
						modal: true,
						resizable: false,
						title: '<b><?php echo esc_js(__('User Credentials required', CSP_PO_TEXTDOMAIN)); ?></b>',
						buttons: { 
							"<?php echo esc_js(__('Ok', CSP_PO_TEXTDOMAIN)); ?>": function() { 
								jQuery('#csp-credentials').dialog("close");
								jQuery(elem).trigger('click');
							},
							"<?php echo esc_js(__('Cancel', CSP_PO_TEXTDOMAIN)); ?>": function() { 
								jQuery('#csp-credentials').dialog("close"); 
								csp_ajax_params.action = '';
							} 
						},
						open: function(event, ui) {
							jQuery('#csp-credentials').show().css('width', 'auto');
						},
						close: function() {
							jQuery('#csp-credentials').dialog("destroy");
						}
					});
					jQuery('#upgrade').hide().attr('disabled', 'disabled');	
				}else {
					csp_show_error(transport.responseText);
					csp_ajax_params.action = '';
				}
			}
		}
	); 	
	csp_cancel_dialog();
	return false;
}

function csp_remove_language(elem, name, row, path, subpath, language) {
	elem = $(elem);
	elem.blur();
	new Ajax.Request('<?php echo CSP_PO_ADMIN_URL.'/admin-ajax.php' ?>', 
		{  
			parameters: {
				action: 'csp_po_dlg_delete',
				name: name,
				row: row,
				path: path,
				subpath: subpath,
				language: language,
				numlangs: $$('#'+row+' .mo-list-head').first().down(2).rel.split('|').size()
			},
			onSuccess: function(transport) {
				$('csp-dialog-caption').update("<?php _e('Confirm Delete Language',CSP_PO_TEXTDOMAIN); ?>");
				$("csp-dialog-body").update(transport.responseText).setStyle({'padding' : '10px'});
				tb_show.defer(null,"#TB_inline?height=180&width=300&inlineId=csp-dialog-container&modal=true",false);
			}
		}
	); 	
	return false;
}

function csp_destroy_files(elem, name, row, path, subpath, language, numlangs){
	elem = $(elem);
	elem.blur();
	csp_cancel_dialog();

	if(csp_ajax_params.action.length) {
		jQuery('#csp-credentials > form').find('input').each(function(i, e) {
			if ((jQuery(e).attr('type') == 'radio') && !jQuery(e).attr('checked')) return;
			var s = jQuery(e).attr('name');
			var v = jQuery(e).val();
			csp_ajax_params[s] = v;
		});		
	}
	else{
		csp_ajax_params.action = 'csp_po_destroy';
		csp_ajax_params.name = name;
		csp_ajax_params.row = row;
		csp_ajax_params.path = path;
		csp_ajax_params.subpath = subpath;
		csp_ajax_params.language = language;
		csp_ajax_params.numlangs = numlangs;
	}
	new Ajax.Request('<?php echo CSP_PO_ADMIN_URL.'/admin-ajax.php' ?>', 
		{  
			parameters: csp_ajax_params,
			onSuccess: function(transport) {
				$$('#'+transport.responseJSON.row+' .mo-file').each(function(tr) {
					if (tr.lang == transport.responseJSON.language) { 
						new Effect.Highlight(tr, { 
							startcolor: '#FF7A0F', 
							endcolor: '#FFFFCF', 
							duration: 1,
							afterFinish: function(obj) { 
								jQuery('#'+transport.responseJSON.row+' .mo-list-head  td.csp-ta-right').html(transport.responseJSON.head);
								a = $$('#'+transport.responseJSON.row+' .mo-list-head').first().down(2).rel.split('|').without(transport.responseJSON.language);
								$$('#'+transport.responseJSON.row+' .mo-list-head').first().down(2).rel = a.join('|');
								obj.element.remove(); 
							}
						});
					}
				});
				csp_ajax_params.action = ''; //reset
			},
			onFailure: function(transport) {
				if (transport.status == '401') {
					jQuery('#csp-credentials').html(transport.responseText).dialog({
						width: '500px',
						closeOnEscape: false,
						modal: true,
						resizable: false,
						title: '<b><?php echo esc_js(__('User Credentials required', CSP_PO_TEXTDOMAIN)); ?></b>',
						buttons: { 
							"<?php echo esc_js(__('Ok', CSP_PO_TEXTDOMAIN)); ?>": function() { 
								jQuery('#csp-credentials').dialog("close");
								jQuery(elem).trigger('click');
							},
							"<?php echo esc_js(__('Cancel', CSP_PO_TEXTDOMAIN)); ?>": function() { 
								jQuery('#csp-credentials').dialog("close"); 
								csp_ajax_params.action = '';
							} 
						},
						open: function(event, ui) {
							jQuery('#csp-credentials').show().css('width', 'auto');
						},
						close: function() {
							jQuery('#csp-credentials').dialog("destroy");
						}
					});
					jQuery('#upgrade').hide().attr('disabled', 'disabled');	
				}else {
					csp_show_error(transport.responseText);
					csp_ajax_params.action = '';
				}
			}
		}
	); 	
	return false;	
}

function csp_rescan_language(elem, name, row, path, subpath, language, type, simplefilename, themetemplate) {
	elem = $(elem);
	elem.blur();
	var a = elem.up('table').summary.split('|');
	actual_domain = a[0];
	$('prj-id-ver').update(a[2]);
	new Ajax.Request('<?php echo CSP_PO_ADMIN_URL.'/admin-ajax.php' ?>', 
		{  
			parameters: {
				action: 'csp_po_dlg_rescan',
				name: name,
				row: row,
				path: path,
				subpath: subpath,
				language: language,
				numlangs: $$('#'+row+' .mo-list-head').first().down(2).rel.split('|').size(),
				type: type,
				textdomain: actual_domain,
				simplefilename: simplefilename,
				themetemplate: themetemplate
			},
			onSuccess: function(transport) {
				$('csp-dialog-caption').update("<?php _e('Rescanning PHP Source Files',CSP_PO_TEXTDOMAIN); ?>");
				$("csp-dialog-body").update(transport.responseText).setStyle({'padding' : '10px'});
				tb_show.defer(null,"#TB_inline?height=230&width=510&inlineId=csp-dialog-container&modal=true",false);
			}
		}
	); 		
	return false;
}

var csp_php_source_json = 0;
var csp_chuck_size = <?php echo (CSL_LOW_MEMORY ? 1 : 20); ?>;

function csp_scan_source_files() {
	if (csp_php_source_json == 0) {
		$('csp-dialog-rescan').hide();
		$('csp-dialog-cancel').hide();
		$('csp-dialog-scan-info').show();
		csp_php_source_json = $('csp-dialog-source-file-json').value.evalJSON();
	}
	if (csp_php_source_json.next >= csp_php_source_json.files.size()) {
		if ($('csp-dialog-cancel').visible()) {
			csp_cancel_dialog();
			csp_php_source_json = 0;
			csp_ajax_params.action = '';
			return false;
		}
		$('csp-dialog-scan-info').hide();
		$('csp-dialog-rescan').show().writeAttribute({'value' : '<?php _e('finished', CSP_PO_TEXTDOMAIN); ?>' });
		$('csp-dialog-cancel').show();
		$('csp-dialog-progressfile').update('&nbsp;');
		elem = $$("#"+csp_php_source_json.row+" .mo-file[lang=\""+csp_php_source_json.language+"\"] div a").first();
		elem.className = "csp-filetype-po-rw";
		elem.title = csp_php_source_json.title;
		return false;
	}
	
	if(csp_ajax_params.action.length) {
		jQuery('#csp-credentials > form').find('input').each(function(i, e) {
			if ((jQuery(e).attr('type') == 'radio') && !jQuery(e).attr('checked')) return;
			var s = jQuery(e).attr('name');
			var v = jQuery(e).val();
			csp_ajax_params[s] = v;
		});		
	}
	else{
		csp_ajax_params.action = 'csp_po_scan_source_file';
		csp_ajax_params.name = csp_php_source_json.name;
		csp_ajax_params.type = csp_php_source_json.type;
		csp_ajax_params.pofile = csp_php_source_json.pofile;
		csp_ajax_params.textdomain = csp_php_source_json.textdomain;
		csp_ajax_params.num = csp_php_source_json.next;
		csp_ajax_params.cnt = csp_chuck_size;
		csp_ajax_params.path = csp_php_source_json.path;
		csp_ajax_params.php = csp_php_source_json.files.join("|");
	}
	
	new Ajax.Request('<?php echo CSP_PO_ADMIN_URL.'/admin-ajax.php' ?>', 
		{  
			parameters: csp_ajax_params,
			onSuccess: function(transport) {
				try{
					csp_php_source_json.title = transport.responseJSON.title;
				}catch(e) {
					$('csp-dialog-scan-info').hide();
					$('csp-dialog-rescan').show().writeAttribute({'value' : '<?php _e('finished', CSP_PO_TEXTDOMAIN); ?>' });
					$('csp-dialog-cancel').show();
					csp_php_source_json = 0;
					var mem_reg = /Allowed memory size of (\d+) bytes exhausted/;
					mem_reg.exec(transport.responseText);
					error_text = "<?php _e('You are trying to rescan files which expands above your PHP Memory Limit at %s MB during the analysis.<br/>Please enable the <em>low memory mode</em> for scanning this component.',CSP_PO_TEXTDOMAIN); ?>";
					csp_show_error(error_text.replace('%s', RegExp.$1 / 1024.0 / 1024.0));
					csp_ajax_params.action = '';
				}
				csp_php_source_json.next += csp_chuck_size;
				csp_ajax_params.num = csp_php_source_json.next;
				var perc = Math.min(Math.round(csp_php_source_json.next*1000.0/csp_php_source_json.files.size())/10.0, 100.00);
				$('csp-dialog-progressvalue').update(Math.min(csp_php_source_json.next, csp_php_source_json.files.size()));
				$('csp-dialog-progressbar').setStyle({'width' : ''+perc+'%'});
				if (csp_php_source_json.files[csp_php_source_json.next-csp_chuck_size]) $('csp-dialog-progressfile').update("<?php _e('File:', CSP_PO_TEXTDOMAIN); ?>&nbsp;"+csp_php_source_json.files[csp_php_source_json.next-csp_chuck_size].replace(csp_php_source_json.path,""));
				csp_scan_source_files().delay(0.1);
			},
			onFailure: function(transport) {
				if (transport.status == '401') {
					jQuery('#csp-credentials').html(transport.responseText).dialog({
						width: '500px',
						closeOnEscape: false,
						modal: true,
						resizable: false,
						title: '<b><?php echo esc_js(__('User Credentials required', CSP_PO_TEXTDOMAIN)); ?></b>',
						buttons: { 
							"<?php echo esc_js(__('Ok', CSP_PO_TEXTDOMAIN)); ?>": function() { 
								jQuery('#csp-credentials').dialog("close");
								csp_scan_source_files();
							},
							"<?php echo esc_js(__('Cancel', CSP_PO_TEXTDOMAIN)); ?>": function() { 
								jQuery('#csp-credentials').dialog("close"); 
								csp_ajax_params.action = '';
								csp_php_source_json = 0;
								csp_cancel_dialog();
							} 
						},
						open: function(event, ui) {
							jQuery('#csp-credentials').show().css('width', 'auto');
						},
						close: function() {
							jQuery('#csp-credentials').dialog("destroy");
						}
					});
					jQuery('#upgrade').hide().attr('disabled', 'disabled');	
				}else {			
					$('csp-dialog-scan-info').hide();
					$('csp-dialog-rescan').show().writeAttribute({'value' : '<?php _e('finished', CSP_PO_TEXTDOMAIN); ?>' });
					$('csp-dialog-cancel').show();
					csp_php_source_json = 0;
					csp_show_error(transport.responseText);
					csp_ajax_params.action = '';
				}
			}
		}
	); 	
	return false;
}

//--- editor based functions ---
var csp_pagesize = 100;
var csp_pagenum = 1;
var csp_search_timer = null;
var csp_search_interval = Prototype.Browser.IE ? 0.3 : 0.1;

var csp_destlang = 'de';
var csp_api_type = 'none';
var csp_path = '';
var csp_file = '';
var csp_num_plurals = 2;
var csp_func_plurals = '';
var csp_idx = {	'total' : [], 'plurals' : [], 'open' : [], 'rem' : [], 'code' : [], 'ctx' : [], 'cur' : [] , 'ltd' : [] , 'trail' : [] }
var csp_searchbase = [];
var csp_pofile = [];
var csp_textdomains = [];
var csp_actual_type = '';

function csp_init_editor(actual_domain, actual_type) {
	//list all contained text domains
	opt_list = '';
	csp_actual_type = actual_type;
	tderror = true;
	tdmixed = new Array();
	for (i=0; i<csp_textdomains.size(); i++) {
		tderror = tderror && (csp_textdomains[i] != actual_domain);
		if (csp_textdomains[i] != 'default' && csp_textdomains[i] != actual_domain && csp_textdomains[i] != '{bug-detected}') tdmixed.push(csp_textdomains[i]);
		opt_list += '<option value="'+csp_textdomains[i]+'"'+(csp_textdomains[i] == actual_domain ? ' selected="selected"' : '')+'>'+(csp_textdomains[i].empty() ? 'default' : csp_textdomains[i])+'</option>';
	}
	initial_domain = $('csp-mo-textdomain-val').update(opt_list).value;
	if(tderror && (csp_actual_type != 'wordpress')) {
		$('textdomain-error').removeClassName('hidden');
		$$("#textdomain-error span").first().update(actual_domain);
	}
	else {
		$('textdomain-error').addClassName('hidden');
	}
	if (csp_actual_type != 'wordpress') {
		if (tdmixed.length) {
			$$("#textdomain-warning span").first().update(tdmixed.join(', '));
			$('textdomain-warning').removeClassName('hidden');
		}else {
			$('textdomain-warning').addClassName('hidden');
		}
	}else{
		$('textdomain-warning').addClassName('hidden');
	}
	
	//setup all indizee register
	for (i=0; i<csp_pofile.size(); i++) {
		csp_idx.total.push(i);
		if (Object.isArray(csp_pofile[i].key)) {
			if (csp_pofile[i].key[0].match(/\s+$/g) || csp_pofile[i].key[1].match(/\s+$/g)) {
				csp_idx.trail.push(i);
			}

			if (!Object.isArray(csp_pofile[i].val)) {
				if(csp_pofile[i].val.blank()) csp_idx.open.push(i);
			}
			else{
				if(csp_pofile[i].val.join('').blank()) csp_idx.open.push(i);
			}
			csp_idx.plurals.push(i);
		}else{
			if (csp_pofile[i].key.match(/\s+$/g)) {
				csp_idx.trail.push(i);
			}
			
			if(csp_pofile[i].val.empty()) {
				csp_idx.open.push(i);
			}
		}
		if(!csp_pofile[i].rem.empty()) csp_idx.rem.push(i);
		if(csp_pofile[i].ctx) csp_idx.ctx.push(i);
		if(csp_pofile[i].code) csp_idx.code.push(i);
		if(csp_pofile[i].ltd.indexOf(initial_domain) != -1) csp_idx.ltd.push(i);
	}
//$	csp_idx.cur = csp_idx.total;
	csp_idx.cur = csp_idx.ltd.intersect(csp_idx.total);
	csp_searchbase = csp_idx.cur;
/*
	if(csp_textdomains[0] != '{php-code}'){
		$('csp-write-mo-file').show();
	}else{
		$('csp-write-mo-file').hide();
	}
*/	
	csp_change_pagesize(100);
	window.scrollTo(0,0);
	$('s_original').value="";
	$('s_original').autoComplete="off";
	$('s_translation').value="";
	$('s_translation').autoComplete="off";	
	csp_change_textdomain_view(initial_domain);
}

function csp_change_textdomain_view(textdomain) {
	csp_idx.ltd = [];
	for (i=0; i<csp_pofile.size(); i++) {
		if (csp_pofile[i].ltd.indexOf(textdomain) != -1) csp_idx.ltd.push(i);
	}
	csp_idx.cur = csp_idx.ltd.intersect(csp_idx.total);
	csp_searchbase = csp_idx.cur;
	$$("a.csp-filter").each(function(e) { e.removeClassName('current')});
	$('csp-filter-all').addClassName('current');
	hide = false;
	if (textdomain == '{php-code}' || textdomain == '{bug-detected}') { hide = true; }
	else if(textdomain == 'default') {
		hide = true;
		//special bbPress on BuddyPress test because of default domain too
		reg = /\/bp-forums\/bbpress\/$/;
		if ((csp_actual_type == 'wordpress')||reg.test(csp_path)) { hide = false; }
	}
	if (hide) {
		$('csp-write-mo-file').hide();
	}
	else {
		$('csp-write-mo-file').show();
	}
	csp_filter_result('csp-filter-all', csp_idx.total);
}

function csp_show_error(message) {
	error = "<div style=\"text-align:center\"><img src=\"<?php echo CSP_PO_BASE_URL."/images/error.gif"; ?>\" align=\"left\" />"+message+
			"<p style=\"margin:15px 0 0 0;text-align:center; padding-top: 5px;border-top: solid 1px #aaa;\">"+
			"<input class=\"button\" type=\"submit\" onclick=\"return csp_cancel_dialog();\" value=\"  Ok  \"/>"+
			"</p>"+
			"</div>";
	$('csp-dialog-caption').update("CodeStyling Localization - <?php _e('Access Error',CSP_PO_TEXTDOMAIN); ?>");
	$("csp-dialog-body").update(error).setStyle({'padding' : '10px'});
	if ($('csp-dialog-saving')) $('csp-dialog-saving').hide();
	tb_show.defer(null,"#TB_inline?height=140&width=510&inlineId=csp-dialog-container&modal=true",false);
}

function csp_cancel_dialog(){
	tb_remove();
	$('csp-dialog-body').update("");
	$$('.highlight-editing').each(function(e) {
		e.removeClassName('highlight-editing');
	});
}

function csp_launch_editor(elem, file, path, textdomain) {
	if(csp_ajax_params.action.length) {
		jQuery('#csp-credentials > form').find('input').each(function(i, e) {
			if ((jQuery(e).attr('type') == 'radio') && !jQuery(e).attr('checked')) return;
			var s = jQuery(e).attr('name');
			var v = jQuery(e).val();
			csp_ajax_params[s] = v;
		});		
	}
	else{
		var a = $(elem).up('table').summary.split('|');
		$('csp-wrap-main').hide();
		$('csp-wrap-editor').show();
		$('prj-id-ver').update(a[2]);

		csp_ajax_params.action = 'csp_po_launch_editor';
		csp_ajax_params.basepath = path;
		csp_ajax_params.file = file;
		csp_ajax_params.textdomain = textdomain;
		csp_ajax_params.type = a[1];
		
		//remeber the last edited component by id hash 
		//old jquery is unable to do that in WP 2.5
		try{ window.location.hash = jQuery(elem).closest('table').attr('id'); } catch(e) {} 
	}		
	new Ajax.Request('<?php echo CSP_PO_ADMIN_URL.'/admin-ajax.php' ?>', 
		{  
			parameters: csp_ajax_params,
			onSuccess: function(transport) {
				//switch to editor now
				try{
					$('csp-json-header').insert(transport.responseJSON.header);
				}catch(e) {
					var mem_reg = /Allowed memory size of (\d+) bytes exhausted/;
					mem_reg.exec(transport.responseText);
					error_text = "<?php _e('You are trying to open a translation catalog which expands above your PHP Memory Limit at %s MB during read.<br/>Please enable the <em>low memory mode</em> for opening this components catalog.',CSP_PO_TEXTDOMAIN); ?>";
					$('catalog-body').update('<tr><td colspan="4" align="center" style="color:#f00;">'+error_text.replace('%s', RegExp.$1 / 1024.0 / 1024.0)+'</td></tr>');
				}				
				$('catalog-last-saved').update(transport.responseJSON.last_saved);
				$$('#csp-json-header a')[0].update(transport.responseJSON.file);
				csp_destlang = transport.responseJSON.destlang;
				csp_api_type = transport.responseJSON.api_type;
				if (csp_api_type == 'none') csp_destlang = '';
				csp_path = transport.responseJSON.path;
				csp_file = transport.responseJSON.file;
				csp_num_plurals = transport.responseJSON.plurals_num;
				csp_func_plurals = transport.responseJSON.plurals_func;
				csp_idx = transport.responseJSON.index;
				csp_pofile = transport.responseJSON.content;
				csp_textdomains = transport.responseJSON.textdomains;
				csp_init_editor(a[0], a[1]);
			},
			onFailure: function(transport) {
				if (transport.status == '401') {
					jQuery('#csp-credentials').html(transport.responseText).dialog({
						width: '500px',
						closeOnEscape: false,
						modal: true,
						resizable: false,
						title: '<b><?php echo esc_js(__('User Credentials required', CSP_PO_TEXTDOMAIN)); ?></b>',
						buttons: { 
							"<?php echo esc_js(__('Ok', CSP_PO_TEXTDOMAIN)); ?>": function() { 
								jQuery('#csp-credentials').dialog("close");
								jQuery(elem).trigger('click');
							},
							"<?php echo esc_js(__('Cancel', CSP_PO_TEXTDOMAIN)); ?>": function() { 
								jQuery('#csp-credentials').dialog("close"); 
								csp_ajax_params.action = '';
							} 
						},
						open: function(event, ui) {
							jQuery('#csp-credentials').show().css('width', 'auto');
						},
						close: function() {
							jQuery('#csp-credentials').dialog("destroy");
						}
					});
					jQuery('#upgrade').hide().attr('disabled', 'disabled');	
				}else {
					$('catalog-body').update('<tr><td colspan="4" align="center" style="color:#f00;">'+transport.responseText+'</td></tr>');
					csp_ajax_params.action = '';
				}
			}
		}
	); 
	return false;	
}

function csp_toggle_header(host, elem) {
	$(host).up().toggleClassName('po-header-collapse');
	$(elem).toggle();
}

function csp_change_pagesize(newsize) {
	csp_pagesize = parseInt(newsize);
	csp_change_pagenum(1);
}

function csp_change_pagenum(newpage) {
	csp_pagenum = newpage;
	var cp = $('catalog-pages-top');
	var cb = $('catalog-body')
	
	var inner = '';
	
	var cnt = Math.round(csp_idx.cur.size() * 1.0 / csp_pagesize + 0.499);
	if (cnt > 1) {
		
		if (csp_pagenum > 1) { inner += "<a class=\"next page-numbers\" onclick=\"csp_change_pagenum("+(csp_pagenum-1)+")\"><?php _e('&laquo; Previous', CSP_PO_TEXTDOMAIN); ?></a>"; }
		var low = Math.max(csp_pagenum - 5,1);
		if (low > 1) inner += "<span>&nbsp;...&nbsp;</span>"; 
		for (i=low; i<=Math.min(low+10,cnt); i++) {
			inner += "<a class=\"page-numbers"+(i==csp_pagenum ? ' current' : '')+"\" onclick=\"csp_change_pagenum("+i+")\">"+i+"</a>";
		}
		if (Math.min(low+10,cnt) < cnt) inner += "<span>&nbsp;...&nbsp;</span>"; 
		if (csp_pagenum < cnt) { inner += "<a class=\"next page-numbers\" onclick=\"csp_change_pagenum("+(csp_pagenum+1)+")\"><?php _e('Next &raquo;', CSP_PO_TEXTDOMAIN); ?></a>"; }
	}
	cp.update(inner);
	$('catalog-pages-bottom').update(inner);
	
	inner = '';

	for (var i=(csp_pagenum-1)*csp_pagesize; i<Math.min(csp_pagenum * csp_pagesize, csp_idx.cur.size());i++) {
		inner += "<tr"+(i % 2 == 0 ? '' : ' class="odd"')+" id=\"msg-row-"+csp_idx.cur[i]+"\">";
		var tooltip = [];
		if (!csp_pofile[csp_idx.cur[i]].rem.empty()) tooltip.push(String.fromCharCode(3)+"<?php _e('Comment',CSP_PO_TEXTDOMAIN); ?>"+String.fromCharCode(4)+csp_pofile[csp_idx.cur[i]].rem);
		if (csp_pofile[csp_idx.cur[i]].code) tooltip.push(String.fromCharCode(3)+"<?php _e('Code Hint',CSP_PO_TEXTDOMAIN); ?>"+String.fromCharCode(4)+csp_pofile[csp_idx.cur[i]].code);
		if (tooltip.size() > 0) {
			tooltip = tooltip.join(String.fromCharCode(1)).replace("\n", String.fromCharCode(1)).escapeHTML();
			tooltip = tooltip.replace(/\1/g, '<br/>').replace(/\3/g, '<strong>').replace(/\4/g, '</strong>');
		}
		else { tooltip = '' };
		inner += "<td nowrap=\"nowrap\">";
		if(csp_pofile[csp_idx.cur[i]].ref.size() > 0) {
			inner += "<a class=\"csp-msg-tip\"><img alt=\"\" src=\"<?php echo CSP_PO_BASE_URL;?>/images/php.gif\" /><span><strong><?php _e('Files:',CSP_PO_TEXTDOMAIN); ?></strong>";
			csp_pofile[csp_idx.cur[i]].ref.each(function(r) {
				inner += "<em onclick=\"csp_view_phpfile(this, '"+r+"', "+csp_idx.cur[i]+")\">"+r+"</em><br />";
			});
			inner += "</span></a>";
		}		
		inner += (tooltip.empty() ? '' : "<a class=\"csp-msg-tip\"><img alt=\"\" src=\"<?php echo CSP_PO_BASE_URL;?>/images/comment.gif\" /><span>"+tooltip+"</span></a>");
		inner += "</td>";
		ctx_str = '';
		if (csp_pofile[csp_idx.cur[i]].ctx) {
			ctx_str = "<div><b style=\"border-bottom: 1px dotted #000;\"><?php _e('Context',CSP_PO_TEXTDOMAIN); ?>:</b>&nbsp;<span style=\"color:#f00;\">"+csp_pofile[csp_idx.cur[i]].ctx+"</span></div>";
		}
		if (Object.isArray(csp_pofile[csp_idx.cur[i]].key)) {
			inner += 
				"<td>"+ctx_str+"<div><span class=\"csp-pl-form\"><?php _e('Singular:',CSP_PO_TEXTDOMAIN); ?> </span>"+csp_pofile[csp_idx.cur[i]].key[0].escapeHTML().replace(/\s+$/g,'<span style="border: solid 1px #FF8080;">&nbsp;</span>')+"</div><div><span class=\"csp-pl-form\"><?php _e('Plural:',CSP_PO_TEXTDOMAIN); ?> </span>"+csp_pofile[csp_idx.cur[i]].key[1].escapeHTML().replace(/\s+$/g,'<span style="border: solid 1px #FF8080;">&nbsp;</span>')+"</div></td>"+
				"<td>"+ctx_str;
			for (pl=0;pl<csp_num_plurals; pl++) {
				if (csp_num_plurals == 1) {
					inner += "<div><span class=\"csp-pl-form\"><?php _e('Plural Index Result =',CSP_PO_TEXTDOMAIN); ?> "+pl+" </span>"+(!csp_pofile[csp_idx.cur[i]].val.empty() ? csp_pofile[csp_idx.cur[i]].val.escapeHTML().replace(/\s+$/g,'<span style="border: solid 1px #FF8080;">&nbsp;</span>') : '&nbsp;')+"</div>"
				}
				else{
					inner += "<div><span class=\"csp-pl-form\"><?php _e('Plural Index Result =',CSP_PO_TEXTDOMAIN); ?> "+pl+" </span>"+(!csp_pofile[csp_idx.cur[i]].val[pl].empty() ? csp_pofile[csp_idx.cur[i]].val[pl].escapeHTML().replace(/\s+$/g,'<span style="border: solid 1px #FF8080;">&nbsp;</span>') : '&nbsp;')+"</div>"
				}
			}
			inner += "</td>";
		}
		else{			
			inner += 
				"<td>"+ctx_str+csp_pofile[csp_idx.cur[i]].key.escapeHTML().replace(/\s+$/g,'<span style="border: solid 1px #FF8080;">&nbsp;</span>')+"</td>"+
				"<td>"+ctx_str+(csp_pofile[csp_idx.cur[i]].val.empty() ? '&nbsp;' : csp_pofile[csp_idx.cur[i]].val.escapeHTML().replace(/\s+$/g,'<span style="border: solid 1px #FF8080;">&nbsp;</span>'))+"</td>";
		}
		inner += 
			"<td nowrap=\"nowrap\">"+
			  "<a class=\"tr-edit-link\" onclick=\"return csp_edit_catalog(this);\"><?php _e('Edit',CSP_PO_TEXTDOMAIN); ?></a>&nbsp;|&nbsp;"+  
			  "<a onclick=\"return csp_copy_catalog(this);\"><?php _e('Copy',CSP_PO_TEXTDOMAIN); ?></a>"; // TODO: add here comment editing link
		inner += "</td></tr>";
	}	
	cb.replace("<tbody id=\"catalog-body\">"+inner+"</tbody>");
	
	$$("#csp-filter-all span").first().update(csp_idx.cur.size() + " / " + csp_idx.total.size());
	$$("#csp-filter-plurals span").first().update(csp_idx.plurals.size());
	$$("#csp-filter-open span").first().update(csp_idx.open.size());
	$$("#csp-filter-rem span").first().update(csp_idx.rem.size());
	$$("#csp-filter-code span").first().update(csp_idx.code.size());
	$$("#csp-filter-ctx span").first().update(csp_idx.ctx.size());
	$$("#csp-filter-trail span").first().update(csp_idx.trail.size());
	$$("#csp-filter-search span").first().update(csp_idx.cur.size());
	$$("#csp-filter-regexp span").first().update(csp_idx.cur.size());
}

function csp_filter_result(elem, set) {
	$$("a.csp-filter").each(function(e) { e.removeClassName('current')});
	$(elem).addClassName('current');
	$('s_original').clear();
	$('s_translation').clear();
	$('csp-filter-search').up().hide();
	$('csp-filter-regexp').up().hide();
//$	csp_idx.cur = set;
	csp_idx.cur = csp_idx.ltd.intersect(set);
	csp_searchbase = csp_idx.cur;
	csp_change_pagenum(1);
}

function csp_search_key(elem, expr) {
	var term = $(elem).value;
	var ignore_case = $('ignorecase_key').checked;
	var is_expr = (typeof(expr) == "object");
	if (is_expr) { 
		term = expr; ignore_case = false; 
		$('s_original').clear();
	}
	else { 
		if (ignore_case) term = term.toLowerCase(); 
	}
	$('s_translation').clear();
	$$("a.csp-filter").each(function(e) { e.removeClassName('current')});
	csp_idx.cur = [];
	try{
		for (i=0; i<csp_searchbase.size(); i++) {
			if (Object.isArray(csp_pofile[csp_searchbase[i]].key)) {
				if (csp_pofile[csp_searchbase[i]].key.find(function(s){ return (ignore_case ? s.toLowerCase().include(term) : s.match(term)); })) csp_idx.cur.push(csp_searchbase[i]);			
			}
			else{
				if ( (ignore_case ? csp_pofile[csp_searchbase[i]].key.toLowerCase().include(term) : csp_pofile[csp_searchbase[i]].key.match(term) ) ) csp_idx.cur.push(csp_searchbase[i]);
			}
		}
	}catch(e) {
		//in case of half ready typed regexp catch it silently
		csp_idx.cur = csp_idx.total;
	}
	$('csp-filter-search').up().hide();
	$('csp-filter-regexp').up().hide();
	if (term) {
		if (is_expr) $('csp-filter-regexp').up().show();
		else $('csp-filter-search').up().show();
		csp_change_pagenum(1);
	}
	else {
		csp_filter_result('csp-filter-all', csp_idx.total);
	}
}

function csp_search_val(elem, expr) {
	var term = $(elem).value;
	var ignore_case = $('ignorecase_val').checked;
	var is_expr = (typeof(expr) == "object");
	if (is_expr) { 
		term = expr; ignore_case = false; 
		$('s_translation').clear();
	}
	else { 
		if (ignore_case) term = term.toLowerCase(); 
	}
	$('s_original').clear();
	$$("a.csp-filter").each(function(e) { e.removeClassName('current')});
	csp_idx.cur = [];
	try{
		for (i=0; i<csp_searchbase.size(); i++) {
			if (Object.isArray(csp_pofile[csp_searchbase[i]].val)) {
				if (csp_pofile[csp_searchbase[i]].val.find(function(s){ return (ignore_case ? s.toLowerCase().include(term) : s.match(term)); })) csp_idx.cur.push(csp_searchbase[i]);
			}
			else{
				if ( (ignore_case ? csp_pofile[csp_searchbase[i]].val.toLowerCase().include(term) : csp_pofile[csp_searchbase[i]].val.match(term) ) ) csp_idx.cur.push(csp_searchbase[i]);
			}
		}
	}catch(e) {
		//in case of half ready typed regexp catch it silently
		csp_idx.cur = csp_idx.total;
	}
	$('csp-filter-search').up().hide();
	$('csp-filter-regexp').up().hide();
	if (term) {
		if (is_expr) $('csp-filter-regexp').up().show();
		else $('csp-filter-search').up().show();
		csp_change_pagenum(1);
	}
	else {
		csp_filter_result('csp-filter-all', csp_idx.total);
	}
}

function csp_search_result(elem) {
	window.clearTimeout(csp_search_timer);
	if ($(elem).id == "s_original") {
		csp_search_timer = this.csp_search_key.delay(csp_search_interval, elem);
	}else{
		csp_search_timer = this.csp_search_val.delay(csp_search_interval, elem);
	}
}

function csp_exec_expression(elem) {
	var s = $("csp-dialog-expression").value;
	var t = /^\/(.*)\/([gi]*)/;
	var a = t.exec(s);
	var r = (a != null ? RegExp(a[1], a[2]) : RegExp(s, ''));
	if (elem == "s_original") {
		csp_search_key(elem, r);
	}else{
		csp_search_val(elem, r);
	}
	csp_cancel_dialog();
}

function csp_search_regexp(elem) {
	$(elem).blur();
	$('csp-dialog-caption').update("<?php _e('Extended Expression Search',CSP_PO_TEXTDOMAIN); ?>");
	$("csp-dialog-body").update(
		"<div><strong><?php _e('Expression:',CSP_PO_TEXTDOMAIN); ?></strong></div>"+
		"<input type=\"text\" id=\"csp-dialog-expression\" style=\"width:98%;font-size:11px;line-height:normal;\" value=\"\"\>"+		
		"<div style=\"margin-top:10px; color:#888;\"><strong><?php _e('Examples: <small>Please refer to official Perl regular expression descriptions</small>',CSP_PO_TEXTDOMAIN); ?></strong></div>"+
		'<div style="height: 215px; overflow:scroll;">'+
		<?php require('includes/js-help-perlreg.php'); ?>
		'</div>'+
		"<p style=\"margin:5px 0 0 0;text-align:center; padding-top: 5px;border-top: solid 1px #aaa;\">"+
		"<input class=\"button\" type=\"submit\" onclick=\"return csp_exec_expression('"+elem+"');\" value=\"  <?php echo _e('Search', CSP_PO_TEXTDOMAIN); ?>  \"/>"+
		"</p>"
	).setStyle({'padding' : '10px'});		
	tb_show(null,"#TB_inline?height=385&width=600&inlineId=csp-dialog-container&modal=true",false);	
	$("csp-dialog-expression").focus();
}

function csp_translate_google(elem, source, dest) {
	$(elem).blur();
	$(elem).down().show();
	//resulting V1 API: {"responseData": {"translatedText":"Kann nicht öffnen zu schreiben!"}, "responseDetails": null, "responseStatus": 200}
	//resulting V2 API: { "data": { "translations" : [ { "translatedText": "Hallo Welt" } ] } }
	//TODO: can't handle google errors by own error dialog, because Thickbox is not multi instance ready (modal over modal) !!!
	new Ajax.Request('<?php echo CSP_PO_ADMIN_URL.'/admin-ajax.php' ?>', 
		{
			parameters: {
				action: 'csp_po_translate_by_google',
				msgid: $(source).value,
				destlang: csp_destlang
			},
			onSuccess: function(transport) {
				if (transport.responseJSON) {
					if (!transport.responseJSON.error) {
						//V1: $(dest).value = transport.responseJSON.responseData.translatedText;
						//V2:
						$(dest).value = transport.responseJSON.data.translations[0].translatedText;
					}else{
						//V1: alert(transport.responseJSON.responseDetails);
						//V2:
						alert(transport.responseJSON.error.errors[0].reason);
					}
				}else{
					alert(transport.responseText);
				}
				$(elem).down().hide();
			},
			onFailure: function(transport) {
				$(elem).down().hide();
				if (transport.responseJSON && transport.responseJSON.error)
					alert(transport.responseJSON.error.errors[0].reason); 
				else
					alert(transport.responseText);
			}
		}
	);
}

function csp_translate_microsoft(elem, source, dest) {
	$(elem).blur();
	$(elem).down().show();
	new Ajax.Request('<?php echo CSP_PO_ADMIN_URL.'/admin-ajax.php' ?>', 
		{
			parameters: {
				action: 'csp_po_translate_by_microsoft',
				msgid: $(source).value,
				destlang: csp_destlang
			},
			onSuccess: function(transport) {
				$(dest).value = transport.responseText;
				$(elem).down().hide();
			},
			onFailure: function(transport) {
				$(elem).down().hide();
				alert(transport.responseText); 
			}
		}
	);
}

function csp_translate_none(elem, source, dest) {
	$(elem).blur();
	$(elem).down().show();
}

function csp_save_translation(elem, isplural, additional_action){
	$(elem).blur();
	
	msgid = $('csp-dialog-msgid').value;
	msgstr = '';
	
	glue = (Prototype.Browser.Opera ? '\1' : '\0'); //opera bug: can't send embedded 0 in strings!
	
	if (isplural) {
		msgid = [$('csp-dialog-msgid').value, $('csp-dialog-msgid-plural').value].join(glue);
		msgstr = [];
		if (csp_num_plurals == 1){
			msgstr = $('csp-dialog-msgstr-0').value;
		}
		else {
			for (pl=0;pl<csp_num_plurals; pl++) {
				msgstr.push($('csp-dialog-msgstr-'+pl).value);
			}
			msgstr = msgstr.join(glue);
		}
	}
	else{
		msgstr = $('csp-dialog-msgstr').value;
	}
	idx = parseInt($('csp-dialog-msg-idx').value);
	if (additional_action != 'close') {
		$('csp-dialog-body').hide();
		$('csp-dialog-saving').show();
	}
	//add the context in front of again
	if (csp_pofile[idx].ctx) msgid = csp_pofile[idx].ctx+ String.fromCharCode(4) + msgid;
	
	jQuery('#csp-credentials > form').find('input').each(function(i, e) {
		if ((jQuery(e).attr('type') == 'radio') && !jQuery(e).attr('checked')) return;
		var s = jQuery(e).attr('name');
		var v = jQuery(e).val();
		csp_ajax_params[s] = v;
	});
	
	csp_ajax_params.action = 'csp_po_save_catalog_entry';
	csp_ajax_params.path = csp_path;
	csp_ajax_params.file = csp_file;
	csp_ajax_params.isplural = isplural;
	csp_ajax_params.msgid = msgid;
	csp_ajax_params.msgstr = msgstr;
	csp_ajax_params.msgidx = idx;
		
	new Ajax.Request('<?php echo CSP_PO_ADMIN_URL.'/admin-ajax.php' ?>', 
		{  
			parameters: csp_ajax_params,
			onSuccess: function(transport) {
				if (isplural && (csp_num_plurals != 1)) {
					csp_pofile[idx].val = msgstr.split(glue);
				}
				else{
					csp_pofile[idx].val = msgstr;
				}
				//TODO: check also erasing fields !!!!
				if (!msgstr.empty() && (csp_idx.open.indexOf(idx) != -1)) { 
					csp_idx.open = csp_idx.open.without(idx); 
//					csp_idx.cur = csp_idx.cur.without(idx); //TODO: only allowed if this is not total !!!
				}else if (msgstr.empty() && (csp_idx.open.indexOf(idx) == -1)) { 
					csp_idx.open.push(idx); 
				}
				csp_change_pagenum(csp_pagenum);
				if (additional_action != 'close') {
					var lin_idx = csp_idx.cur.indexOf(idx);
					if (additional_action == 'prev') {
						lin_idx--; 
					}
					if (additional_action == 'next') {
						lin_idx++; 
					}					
					if (Math.floor(lin_idx / csp_pagesize) != csp_pagenum -1) {
						csp_change_pagenum(Math.floor(lin_idx / csp_pagesize) + 1);
					}
					$('csp-dialog-saving').hide();
					$('csp-dialog-body').show();
					csp_edit_catalog($$("#msg-row-"+csp_idx.cur[lin_idx]+" a.tr-edit-link")[0]);
				}
				else {
					csp_cancel_dialog();
				}
				csp_ajax_params.action = '';
			},
			onFailure: function(transport) {
				if (transport.status == '401') {
					jQuery('#csp-credentials').html(transport.responseText).dialog({
						width: '500px',
						closeOnEscape: false,
						modal: true,
						resizable: false,
						title: '<b><?php echo esc_js(__('User Credentials required', CSP_PO_TEXTDOMAIN)); ?></b>',
						buttons: { 
							"<?php echo esc_js(__('Ok', CSP_PO_TEXTDOMAIN)); ?>": function() { 
								jQuery('#csp-credentials').dialog("close");
								csp_save_translation(elem, isplural, additional_action);
							},
							"<?php echo esc_js(__('Cancel', CSP_PO_TEXTDOMAIN)); ?>": function() { 
								jQuery('#csp-credentials').dialog("close"); 
								csp_ajax_params.action = '';
								if (additional_action != 'close') {
									$('csp-dialog-body').show();
									$('csp-dialog-saving').hide();
								}
							} 
						},
						open: function(event, ui) {
							jQuery('#csp-credentials').show().css('width', 'auto');
						},
						close: function() {
							jQuery('#csp-credentials').dialog("destroy");
						}
					});
					jQuery('#upgrade').hide().attr('disabled', 'disabled');	
				}else {				
					$('csp-dialog-saving').hide();
					$('csp-dialog-body').show();
					//opera bug: Opera has in case of error no valid responseText (always empty), even if server sends it! Ensure status text instead (dirty fallback)
					csp_show_error( (Prototype.Browser.Opera ? transport.statusText : transport.responseText));
					csp_ajax_params.action = '';
				}
			}
		}
	); 	
	return false;
}

function csp_suppress_enter(event) {
	if(event.keyCode == Event.KEY_RETURN) Event.stop(event);
}

function csp_copy_catalog(elem) {
	elem = $(elem);
	elem.blur();

	jQuery('#csp-credentials > form').find('input').each(function(i, e) {
		if ((jQuery(e).attr('type') == 'radio') && !jQuery(e).attr('checked')) return;
		var s = jQuery(e).attr('name');
		var v = jQuery(e).val();
		csp_ajax_params[s] = v;
	});
		
	var msg_idx = parseInt(elem.up().up().id.replace('msg-row-',''));
	msgid = csp_pofile[msg_idx].key;
	msgstr = csp_pofile[msg_idx].key;
	if(Object.isArray(csp_pofile[msg_idx].key)) {
		msgid = csp_pofile[msg_idx].key.join("\0");
		if (csp_num_plurals == 1) {
			msgstr = csp_pofile[msg_idx].key[0];
		}
		else{
			msgstr = msgid;
		}
	}
	
	csp_ajax_params.action = 'csp_po_save_catalog_entry';
	csp_ajax_params.path = csp_path;
	csp_ajax_params.file = csp_file;
	csp_ajax_params.isplural =  Object.isArray(csp_pofile[msg_idx].key);
	csp_ajax_params.msgid = msgid;
	csp_ajax_params.msgstr = msgstr;
	csp_ajax_params.msgidx = msg_idx;
	
	new Ajax.Request('<?php echo CSP_PO_ADMIN_URL.'/admin-ajax.php' ?>', 
		{  
			parameters: csp_ajax_params,
			onSuccess: function(transport) {
				idx = msg_idx;
				if (Object.isArray(csp_pofile[msg_idx].key) && (csp_num_plurals != 1)) {
					csp_pofile[idx].val = msgstr.split("\0");
				}
				else{
					csp_pofile[idx].val = msgstr;
				}
				//TODO: check also erasing fields !!!!
				if (!msgstr.empty() && (csp_idx.open.indexOf(idx) != -1)) { 
					csp_idx.open = csp_idx.open.without(idx); 
				}
				csp_change_pagenum(csp_pagenum);
				csp_ajax_params.action = '';
			},
			onFailure: function(transport) {
				if (transport.status == '401') {
					jQuery('#csp-credentials').html(transport.responseText).dialog({
						width: '500px',
						closeOnEscape: false,
						modal: true,
						resizable: false,
						title: '<b><?php echo esc_js(__('User Credentials required', CSP_PO_TEXTDOMAIN)); ?></b>',
						buttons: { 
							"<?php echo esc_js(__('Ok', CSP_PO_TEXTDOMAIN)); ?>": function() { 
								jQuery('#csp-credentials').dialog("close");
								jQuery(elem).trigger('click');
							},
							"<?php echo esc_js(__('Cancel', CSP_PO_TEXTDOMAIN)); ?>": function() { 
								jQuery('#csp-credentials').dialog("close"); 
								csp_ajax_params.action = '';
							} 
						},
						open: function(event, ui) {
							jQuery('#csp-credentials').show().css('width', 'auto');
						},
						close: function() {
							jQuery('#csp-credentials').dialog("destroy");
						}
					});
					jQuery('#upgrade').hide().attr('disabled', 'disabled');	
				}else {
					csp_show_error(transport.responseText);
					csp_ajax_params.action = '';
				}
			}
		}
	); 	
	return false;	
}

function csp_edit_catalog(elem) {
	elem = $(elem);
	elem.blur();
	elem.up().up().addClassName('highlight-editing');
	var msg_idx = parseInt(elem.up().up().id.replace('msg-row-',''));
	$('csp-dialog-caption').update("<?php _e('Edit Catalog Entry',CSP_PO_TEXTDOMAIN); ?>");
	if (Object.isArray(csp_pofile[msg_idx].key)) {
		trans = '';
		for (pl=0;pl<csp_num_plurals; pl++) {
			if (!csp_destlang.empty()) {
				switch(pl){
					case 0:
						trans += "<div style=\"margin-top:10px;height:20px;\"><strong class=\"alignleft\"><?php _e('Plural Index Result =',CSP_PO_TEXTDOMAIN); ?> "+pl+"</strong><a class=\"alignright clickable service-api\" onclick=\"csp_translate_"+csp_api_type+"(this, 'csp-dialog-msgid', 'csp-dialog-msgstr-0');\"><img style=\"display:none;\" src=\"<?php echo CSP_PO_BASE_URL; ?>/images/loading-small.gif\" />&nbsp;<?php _e('translate with API Service by',CSP_PO_TEXTDOMAIN); ?> "+csp_api_type.capitalize()+"</a><br class=\"clear\" /></div>";
					break;
					case 1:
						trans += "<div style=\"margin-top:10px;height:20px;\"><strong class=\"alignleft\"><?php _e('Plural Index Result =',CSP_PO_TEXTDOMAIN); ?> "+pl+"</strong><a class=\"alignright clickable service-api\" onclick=\"csp_translate_"+csp_api_type+"(this, 'csp-dialog-msgid-plural', 'csp-dialog-msgstr-1');\"><img style=\"display:none;\" src=\"<?php echo CSP_PO_BASE_URL; ?>/images/loading-small.gif\" />&nbsp;<?php _e('translate with API Service by',CSP_PO_TEXTDOMAIN); ?> "+csp_api_type.capitalize()+"</a><br class=\"clear\" /></div>";
					break;
					default:
						trans += "<div style=\"margin-top:10px;height:20px;\"><strong><?php _e('Plural Index Result =',CSP_PO_TEXTDOMAIN); ?> "+pl+"</strong></div>";
					break;
				}
			}
			else{
				trans += "<div style=\"margin-top:10px;\"><strong><?php _e('Plural Index Result =',CSP_PO_TEXTDOMAIN); ?> "+pl+"</strong></div>";
			}
			if (csp_num_plurals == 1) {
				trans += "<textarea id=\"csp-dialog-msgstr-"+pl+"\" class=\"csp-area-multi\" cols=\"50\" rows=\"1\" style=\"width:98%;font-size:11px;line-height:normal;\">"+csp_pofile[msg_idx].val.escapeHTML()+"</textarea>";
			}
			else{
				trans += "<textarea id=\"csp-dialog-msgstr-"+pl+"\" class=\"csp-area-multi\" cols=\"50\" rows=\"1\" style=\"width:98%;font-size:11px;line-height:normal;\">"+csp_pofile[msg_idx].val[pl].escapeHTML()+"</textarea>";
			}
		}
	
		$("csp-dialog-body").update(	
			"<small style=\"display:block;text-align:right;\"><b><?php _e('Access Keys:',CSP_PO_TEXTDOMAIN); ?></b> <em>ALT</em> + <em>Shift</em> + [<b>p</b>]revious | [<b>s</b>]ave | [<b>n</b>]next</small>"+
			"<div><strong><?php _e('Singular:',CSP_PO_TEXTDOMAIN); ?></strong></div>"+
			"<textarea id=\"csp-dialog-msgid\" class=\"csp-area-multi\" cols=\"50\" rows=\"1\" style=\"width:98%;font-size:11px;line-height:normal;\" readonly=\"readonly\">"+csp_pofile[msg_idx].key[0].escapeHTML()+"</textarea>"+
			"<div style=\"margin-top:10px;\"><strong><?php _e('Plural:',CSP_PO_TEXTDOMAIN); ?></strong></div>"+
			"<textarea id=\"csp-dialog-msgid-plural\" class=\"csp-area-multi\" cols=\"50\" rows=\"1\" style=\"width:98%;font-size:11px;line-height:normal;\" readonly=\"readonly\">"+csp_pofile[msg_idx].key[1].escapeHTML()+"</textarea>"+
			"<div style=\"font-weight:bold;padding-top: 5px;border-bottom: dotted 1px #aaa;\"><?php _e("Plural Index Calculation:",CSP_PO_TEXTDOMAIN);?>&nbsp;&nbsp;&nbsp;<span style=\"color:#D54E21;\">"+csp_func_plurals+"</span></div>"+
			trans+
			"<p style=\"margin:5px 0 0 0;text-align:center; padding-top: 5px;border-top: solid 1px #aaa;\">"+
			"<input class=\"button\""+(csp_idx.cur.indexOf(msg_idx) > 0 ? "" : " disabled=\"disabled\"")+" type=\"submit\" onclick=\"return csp_save_translation(this, true, 'prev');\" value=\"  <?php echo _e('« Save & Previous',CSP_PO_TEXTDOMAIN); ?>  \" accesskey=\"p\"/>&nbsp;&nbsp;&nbsp;&nbsp;"+
			"<input class=\"button\" type=\"submit\" onclick=\"return csp_save_translation(this, true, 'close');\" value=\"  <?php echo _e('Save',CSP_PO_TEXTDOMAIN); ?>  \" accesskey=\"s\"/>"+
			"&nbsp;&nbsp;&nbsp;&nbsp;<input class=\"button\""+(csp_idx.cur.indexOf(msg_idx)+1 < csp_idx.cur.size() ? "" : " disabled=\"disabled\"")+" type=\"submit\" onclick=\"return csp_save_translation(this, true, 'next');\" value=\"  <?php echo _e('Save & Next »',CSP_PO_TEXTDOMAIN); ?>  \" accesskey=\"n\"/>"+
			"</p><input id=\"csp-dialog-msg-idx\" type=\"hidden\" value=\""+msg_idx+"\" />"
		).setStyle({'padding' : '10px'});		
	}else{
		$("csp-dialog-body").update(	
			"<small style=\"display:block;text-align:right;\"><b><?php _e('Access Keys:',CSP_PO_TEXTDOMAIN); ?></b> <em>ALT</em> + <em>Shift</em> + [p]revious | [s]ave | [n]next</small>"+
			"<div><strong><?php _e('Original:',CSP_PO_TEXTDOMAIN); ?></strong></div>"+
			"<textarea id=\"csp-dialog-msgid\" class=\"csp-area-single\" cols=\"50\" rows=\"7\" style=\"width:98%;font-size:11px;line-height:normal;\" readonly=\"readonly\">"+csp_pofile[msg_idx].key.escapeHTML()+"</textarea>"
			+ (csp_destlang.empty() ? 
			"<div style=\"margin-top:10px;\"><strong><?php _e('Translation:',CSP_PO_TEXTDOMAIN); ?></strong></div>"
			:
			 "<div style=\"margin-top:10px;height:20px;\"><strong class=\"alignleft\"><?php _e('Translation:',CSP_PO_TEXTDOMAIN); ?></strong><a class=\"alignright clickable service-api\" onclick=\"csp_translate_"+csp_api_type+"(this, 'csp-dialog-msgid', 'csp-dialog-msgstr');\"><img style=\"display:none;\" align=\"left\" src=\"<?php echo CSP_PO_BASE_URL; ?>/images/loading-small.gif\" />&nbsp;<?php _e('translate with API Service by',CSP_PO_TEXTDOMAIN); ?> "+csp_api_type.capitalize()+"</a><br class=\"clear\" /></div>"
			 ) +
			"<textarea id=\"csp-dialog-msgstr\" class=\"csp-area-single\" cols=\"50\" rows=\"7\" style=\"width:98%;font-size:11px;line-height:normal;\">"+csp_pofile[msg_idx].val.escapeHTML()+"</textarea>"+
			"<p style=\"margin:5px 0 0 0;text-align:center; padding-top: 5px;border-top: solid 1px #aaa;\">"+
			"<input class=\"button\""+(csp_idx.cur.indexOf(msg_idx) > 0 ? "" : " disabled=\"disabled\"")+" type=\"submit\" onclick=\"return csp_save_translation(this, false, 'prev');\" value=\"  <?php echo _e('« Save & Previous',CSP_PO_TEXTDOMAIN); ?>  \" accesskey=\"p\"/>&nbsp;&nbsp;&nbsp;&nbsp;"+
			"<input class=\"button\" type=\"submit\" onclick=\"return csp_save_translation(this, false, 'close');\" value=\"  <?php echo _e('Save',CSP_PO_TEXTDOMAIN); ?>  \" accesskey=\"s\"/>"+
			"&nbsp;&nbsp;&nbsp;&nbsp;<input class=\"button\""+(csp_idx.cur.indexOf(msg_idx)+1 < csp_idx.cur.size() ? "" : " disabled=\"disabled\"")+" type=\"submit\" onclick=\"return csp_save_translation(this, false, 'next');\" value=\"  <?php echo _e('Save & Next »',CSP_PO_TEXTDOMAIN); ?>  \" accesskey=\"n\"/>"+
			"</p><input id=\"csp-dialog-msg-idx\" type=\"hidden\" value=\""+msg_idx+"\" />"
		).setStyle({'padding' : '10px'});
	}
	tb_show(null,"#TB_inline?height="+(csp_num_plurals > 2 && Object.isArray(csp_pofile[msg_idx].key) ? '520' : '385')+"&width=680&inlineId=csp-dialog-container&modal=true",false);
	$$('#csp-dialog-body textarea').each(function(e) {
		e.observe('keydown', csp_suppress_enter);
		e.observe('keypress', csp_suppress_enter);
		e.observe('keyup', csp_suppress_enter);
	});
	$("csp-dialog-msgstr", "csp-dialog-msgstr-0").each(function(e) {
		csp_focus_editor.defer(e);
	});
	return false;
}

function csp_focus_editor(e) {
	try{e.focus();}catch(a){};
}

function csp_view_phpfile(elem, phpfile, idx) {
	elem.blur();	
	glue = (Prototype.Browser.Opera ? '\1' : '\0'); //opera bug: can't send embedded 0 in strings!
	msgid = csp_pofile[idx].key;
	if (Object.isArray(msgid)) {
		msgid = msgid.join(glue);
	}
	new Ajax.Request('<?php echo CSP_PO_ADMIN_URL.'/admin-ajax.php' ?>', 
		{  
			parameters: {
				action: 'csp_po_dlg_show_source',
				path: csp_path,
				file: phpfile,
				msgid: msgid
			},
			onSuccess: function(transport) {
				//own <iframe> creation, because of POST content filling into inline thickbox
				var iframe = null;
				$('csp-dialog-caption').update("<?php _e('File:', CSP_PO_TEXTDOMAIN); ?> "+phpfile.split(':')[0]);
				$('csp-dialog-body').insert(iframe = new Element('iframe', {'class' : 'csp-dialog-iframe', 'frameBorder' : '0'}).writeAttribute({'width' : '100%', 'height' : '570px', 'margin': '0'})).setStyle({'padding' : '0px'});
				tb_show(null,"#TB_inline?height=600&width=600&inlineId=csp-dialog-container&modal=true",false);
				iframe.contentWindow.document.open();
				iframe.contentWindow.document.write(transport.responseText);
				iframe.contentWindow.document.close();
			}
		}
	); 
	return false;	
}

function csp_generate_mofile(elem) {
	elem.blur();
	$('csp-generate-mofile').show();
	$('catalog-last-saved').hide();
	
	jQuery('#csp-credentials > form').find('input').each(function(i, e) {
		if ((jQuery(e).attr('type') == 'radio') && !jQuery(e).attr('checked')) return;
		var s = jQuery(e).attr('name');
		var v = jQuery(e).val();
		csp_ajax_params[s] = v;
	});

	csp_ajax_params.action = 'csp_po_generate_mo_file';
	csp_ajax_params.pofile = csp_path + csp_file;
	csp_ajax_params.textdomain = $('csp-mo-textdomain-val').value;
	
	new Ajax.Request('<?php echo CSP_PO_ADMIN_URL.'/admin-ajax.php' ?>', 
		{  
			parameters: csp_ajax_params,
			onSuccess: function(transport) {
			$('csp-generate-mofile').hide();
			$('catalog-last-saved').show();
				new Effect.Highlight($('catalog-last-saved').update(transport.responseJSON.filetime), { startcolor: '#25FF00', endcolor: '#FFFFCF' });
			},
			onFailure: function(transport) {
				if (transport.status == '401') {
					jQuery('#csp-credentials').html(transport.responseText).dialog({
						width: '500px',
						closeOnEscape: false,
						modal: true,
						resizable: false,
						title: '<b><?php echo esc_js(__('User Credentials required', CSP_PO_TEXTDOMAIN)); ?></b>',
						buttons: { 
							"<?php echo esc_js(__('Ok', CSP_PO_TEXTDOMAIN)); ?>": function() { 
								jQuery('#csp-credentials').dialog("close");
								jQuery(elem).trigger('click');
							},
							"<?php echo esc_js(__('Cancel', CSP_PO_TEXTDOMAIN)); ?>": function() { 
								jQuery('#csp-credentials').dialog("close"); 
								csp_ajax_params.action = '';
								$('csp-generate-mofile').hide();
								$('catalog-last-saved').show();
							} 
						},
						open: function(event, ui) {
							jQuery('#csp-credentials').show().css('width', 'auto');
						},
						close: function() {
							jQuery('#csp-credentials').dialog("destroy");
						}
					});
					jQuery('#upgrade').hide().attr('disabled', 'disabled');	
				}else {
					$('csp-generate-mofile').hide();
					$('catalog-last-saved').show();
					csp_show_error(transport.responseText);
					csp_ajax_params.action = '';
				}
			}
		}
	); 
	return false;
}

function csp_create_languange_path(elem, path) {
	elem.blur();
	
	if(csp_ajax_params.action.length) {	
		jQuery('#csp-credentials > form').find('input').each(function(i, e) {
			if ((jQuery(e).attr('type') == 'radio') && !jQuery(e).attr('checked')) return;
			var s = jQuery(e).attr('name');
			var v = jQuery(e).val();
			csp_ajax_params[s] = v;
		});
	} else {
		csp_ajax_params.action = 'csp_po_create_language_path';
		csp_ajax_params.path = path;
	}
	
	new Ajax.Request('<?php echo CSP_PO_ADMIN_URL.'/admin-ajax.php' ?>', 
		{  
			parameters: csp_ajax_params,
			onSuccess: function(transport) {
				window.location.reload();
			},
			onFailure: function(transport) {
				if (transport.status == '401') {
					jQuery('#csp-credentials').html(transport.responseText).dialog({
						width: '500px',
						closeOnEscape: false,
						modal: true,
						resizable: false,
						title: '<b><?php echo esc_js(__('User Credentials required', CSP_PO_TEXTDOMAIN)); ?></b>',
						buttons: { 
							"<?php echo esc_js(__('Ok', CSP_PO_TEXTDOMAIN)); ?>": function() { 
								jQuery('#csp-credentials').dialog("close");
								jQuery(elem).trigger('click');
							},
							"<?php echo esc_js(__('Cancel', CSP_PO_TEXTDOMAIN)); ?>": function() { 
								jQuery('#csp-credentials').dialog("close"); 
								csp_ajax_params.action = '';
							} 
						},
						open: function(event, ui) {
							jQuery('#csp-credentials').show().css('width', 'auto');
						},
						close: function() {
							jQuery('#csp-credentials').dialog("destroy");
						}
					});
					jQuery('#upgrade').hide().attr('disabled', 'disabled');	
				} else {
					csp_show_error(transport.responseText);
					csp_ajax_params.action = '';
				}
			}
		}
	); 
	return false;	
}

function csp_create_pot_indicator(elem, potfile) {
	elem.blur();

	if(csp_ajax_params.action.length) {	
		jQuery('#csp-credentials > form').find('input').each(function(i, e) {
			if ((jQuery(e).attr('type') == 'radio') && !jQuery(e).attr('checked')) return;
			var s = jQuery(e).attr('name');
			var v = jQuery(e).val();
			csp_ajax_params[s] = v;
		});
	}else{
		csp_ajax_params.action = 'csp_po_create_pot_indicator';
		csp_ajax_params.potfile = potfile;
	}
	
	new Ajax.Request('<?php echo CSP_PO_ADMIN_URL.'/admin-ajax.php' ?>', 
		{  
			parameters: csp_ajax_params,
			onSuccess: function(transport) {
				window.location.reload();
			},
			onFailure: function(transport) {
				if (transport.status == '401') {
					jQuery('#csp-credentials').html(transport.responseText).dialog({
						width: '500px',
						closeOnEscape: false,
						modal: true,
						resizable: false,
						title: '<b><?php echo esc_js(__('User Credentials required', CSP_PO_TEXTDOMAIN)); ?></b>',
						buttons: { 
							"<?php echo esc_js(__('Ok', CSP_PO_TEXTDOMAIN)); ?>": function() { 
								jQuery('#csp-credentials').dialog("close");
								jQuery(elem).trigger('click');
							},
							"<?php echo esc_js(__('Cancel', CSP_PO_TEXTDOMAIN)); ?>": function() { 
								jQuery('#csp-credentials').dialog("close"); 
								csp_ajax_params.action = '';
							} 
						},
						open: function(event, ui) {
							jQuery('#csp-credentials').show().css('width', 'auto');
						},
						close: function() {
							jQuery('#csp-credentials').dialog("destroy");
						}
					});
					jQuery('#upgrade').hide().attr('disabled', 'disabled');	
				}else {
					csp_show_error(transport.responseText);
					csp_ajax_params.action = '';
				}
			}
		}
	); 
	return false;	
}

jQuery(document).ready(function() { 
	jQuery('#enable_low_memory_mode').click(function(e) {
		jQuery('#enable_low_memory_mode_indicator').toggle();
		mode = jQuery(e.target).is(':checked');
		new Ajax.Request('<?php echo CSP_PO_ADMIN_URL.'/admin-ajax.php' ?>', 
			{  
				parameters: {
					action: 'csp_po_change_low_memory_mode',
					mode: mode
				},
				onSuccess: function(transport) {
					jQuery('#enable_low_memory_mode_indicator').toggle();
				}
			});		
		csp_chuck_size = (jQuery(e.target).is(':checked') ? 1 : 20);
	});
	jQuery('#explain-apis').click(function(event) {
		event.preventDefault();
		jQuery('.translation-apis-info').slideToggle();
	});
	jQuery('.translation-apis input').click(function(event) {
		new Ajax.Request('<?php echo CSP_PO_ADMIN_URL.'/admin-ajax.php' ?>', {  
				parameters: {
					action: 'csp_po_change_translate_api',
					api_type: jQuery(this).val()
				}
		});	
	});
	<?php global $wp_version; if (version_compare($wp_version, '3.3', '<')) : ?>
	jQuery('.question-help').hide();
	<?php else : ?>
	jQuery('.question-help').live('click', function(event) {
		event.preventDefault();
		window.scrollTo(0,0);
		jQuery('#tab-link-'+jQuery(this).attr('rel')+' a').trigger('click');
		if (!jQuery('#contextual-help-link').hasClass('screen-meta-active')) jQuery('#contextual-help-link').trigger('click');
	});
	<?php endif; ?>
});

/* TODO: implement context sensitive help 
function csp_process_online_help(event) {
	if (event) {
		if (event.keyCode == 112) {
			Event.stop(event);
			//TODO: launch appropriated help ajax here for none IE
			return false;
		}
	}else{
		//TODO: launch appropriated help ajax here for IE
		return false;
	}
	return true;
}

function csp_term_help_key(event) {
	if(event.keyCode == 112) {
		Event.stop(event);
		return false;
	}
	return true;
}

if (Prototype.Browser.IE) {
	document.onhelp = csp_process_online_help;
}else{
	document.observe("keydown", csp_process_online_help);
}
document.observe("keyup", csp_term_help_key);
document.observe("keypress", csp_term_help_key);
*/

/* ]]> */
</script>
<?php	
}