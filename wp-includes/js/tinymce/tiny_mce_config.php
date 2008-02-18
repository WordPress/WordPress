<?php 
// some code below is from:
/**
 * $Id: tiny_mce_gzip.php 315 2007-10-25 14:03:43Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2005-2006, Moxiecode Systems AB, All rights reserved.
 *
 * This file compresses the TinyMCE JavaScript using GZip.
 **/
  
@ require('../../../wp-config.php');

function getFileContents($path) {
	$path = realpath($path);

	if ( ! $path || ! @is_file($path) )
		return '';

	if ( function_exists('file_get_contents') )
		return @file_get_contents($path);

	$content = '';
	$fp = @fopen($path, 'r');
	if ( ! $fp )
		return '';

	while ( ! feof($fp) )
		$content .= fgets($fp);

	fclose($fp);
	return $content;
}

function putFileContents( $path, $content ) {
	if ( function_exists('file_put_contents') )
		return @file_put_contents( $path, $content );

	$fp = @fopen( $path, 'wb' );
	if ($fp) {
		fwrite( $fp, $content );
		fclose($fp);
	}
}

// Set up init variables
$https = ( isset($_SERVER['HTTPS']) && 'on' == $_SERVER['HTTPS'] ) ? true : false;
	
$baseurl = get_option('siteurl') . '/wp-includes/js/tinymce';
if ( $https ) str_replace('http://', 'https://', $baseurl);

$mce_css = $baseurl . '/wordpress.css';
$mce_css = apply_filters('mce_css', $mce_css);
if ( $https ) str_replace('http://', 'https://', $mce_css);

$valid_elements = '*[*]';
$valid_elements = apply_filters('mce_valid_elements', $valid_elements);
	
$invalid_elements = apply_filters('mce_invalid_elements', '');

$plugins = array( 'safari', 'inlinepopups', 'autosave', 'spellchecker', 'paste', 'wordpress', 'media', 'fullscreen' );

/* 
The following filter takes an associative array of external plugins for TinyMCE in the form "name" => "url".
It adds the plugin's name (including the required dash) to TinyMCE's plugins init and the call to PluginManager to load the plugin. 
The url should be absolute and should include the js file name to be loaded. 
Example: array( 'myplugin' => 'http://my-site.com/wp-content/plugins/myfolder/mce_plugin.js' ). 
If the plugin uses a button, it should be added with one of the "$mce_buttons" filters.
*/
$mce_external_plugins = apply_filters('mce_external_plugins', array()); 

$ext_plugins = "\n";
foreach ( $mce_external_plugins as $name => $url ) {
	$plugins[] = '-' . $name;
	if ( $https ) str_replace('http://', 'https://', $url);
	
	$ext_plugins .= 'tinymce.PluginManager.load("' . $name . '", "' . $url . '");' . "\n";
}

$plugins = implode($plugins, ',');

$mce_buttons = apply_filters('mce_buttons', array('bold', 'italic', 'strikethrough', '|', 'bullist', 'numlist', 'outdent', 'indent', '|', 'justifyleft', 'justifycenter', 'justifyright', '|', 'link', 'unlink', 'image', 'wp_more', '|', 'spellchecker', 'fullscreen', 'wp_adv' ));
$mce_buttons = implode($mce_buttons, ',');

$mce_buttons_2 = apply_filters('mce_buttons_2', array('formatselect', 'underline', 'justifyfull', 'forecolor', '|', 'pastetext', 'pasteword', '|', 'removeformat', 'cleanup', '|', 'media', 'charmap', 'blockquote', '|', 'undo', 'redo', 'wp_help' ));
$mce_buttons_2 = implode($mce_buttons_2, ',');

$mce_buttons_3 = apply_filters('mce_buttons_3', array());
$mce_buttons_3 = implode($mce_buttons_3, ',');
	
$mce_buttons_4 = apply_filters('mce_buttons_4', array());
$mce_buttons_4 = implode($mce_buttons_4, ',');

$mce_locale = ( '' == get_locale() ) ? 'en' : strtolower( substr(get_locale(), 0, 2) ); // only ISO 639-1

// TinyMCE init settings
$initArray = array (
	'mode' => 'none',
	'onpageload' => 'wpEditorInit',
    'width' => '100%',
	'theme' => 'advanced',
	'skin' => 'wp_theme',
	'theme_advanced_buttons1' => "$mce_buttons",
	'theme_advanced_buttons2' => "$mce_buttons_2",
	'theme_advanced_buttons3' => "$mce_buttons_3",
	'theme_advanced_buttons4' => "$mce_buttons_4",
	'language' => "$mce_locale",
	'theme_advanced_toolbar_location' => 'top',
	'theme_advanced_toolbar_align' => 'left',
	'theme_advanced_statusbar_location' => 'bottom',
	'theme_advanced_resizing' => true,
	'theme_advanced_resize_horizontal' => false,
	'dialog_type' => 'modal',
	'relative_urls' => false,
	'remove_script_host' => false,
	'fix_list_elements' => true,
//	'fix_table_elements' => true,
	'gecko_spellcheck' => true,
	'entities' => '38,amp,60,lt,62,gt',
	'accessibility_focus' => false,
	'tab_focus' => ':next',
	'content_css' => "$mce_css",
	'save_callback' => 'switchEditors.saveCallback',
	'plugins' => "$plugins",
	// pass-through the settings for compression and caching, so they can be changed with "tiny_mce_before_init"
	'disk_cache' => true,
	'compress' => true,
	'old_cache_max' => '3' // number of cache files to keep
);

if ( $valid_elements ) $initArray['valid_elements'] = $valid_elements;
if ( $invalid_elements ) $initArray['invalid_elements'] = $invalid_elements;

// For people who really REALLY know what they're doing with TinyMCE
// You can modify initArray to add, remove, change elements of the config before tinyMCE.init
$initArray = apply_filters('tiny_mce_before_init', $initArray); // changed from action to filter

// support for deprecated actions
ob_start();
do_action('mce_options');
$mce_deprecated1 = ob_get_contents() || '';
ob_end_clean();

/*
// Do we need to support this? Most likely will break TinyMCE 3...
ob_start();
do_action('tinymce_before_init');
$mce_deprecated2 = ob_get_contents() || '';
ob_end_clean();
*/

// Settings for the gzip compression and cache
$cache_path = dirname(__FILE__); // Cache path, this is where the .gz files will be stored
$cache_ext = '.js';

$disk_cache = ( ! isset($initArray['disk_cache']) || false == $initArray['disk_cache'] ) ? false : true;
$compress = ( ! isset($initArray['compress']) || false == $initArray['compress'] ) ? false : true;
$old_cache_max = ( isset($initArray['old_cache_max']) ) ? (int) $initArray['old_cache_max'] : 0;

$initArray['disk_cache'] = $initArray['compress'] = $initArray['old_cache_max'] = null;
unset( $initArray['disk_cache'], $initArray['compress'], $initArray['old_cache_max'] );

$plugins = explode( ',', $initArray['plugins'] );
$theme = ( 'simple' == $initArray['theme'] ) ? 'simple' : 'advanced';
$language = isset($initArray['language']) ? substr( $initArray['language'], 0, 2 ) : 'en';
$enc = $cacheKey = $suffix = $mce_options = '';	

// Custom extra javascripts to pack
$custom_js = array(); //$custom_js = apply_filters('tinymce_custom_js', array());

// Check if supports gzip
if ( $compress && isset($_SERVER['HTTP_ACCEPT_ENCODING']) ) {
	$encodings = explode( ',', strtolower( preg_replace('/\s+/', '', $_SERVER['HTTP_ACCEPT_ENCODING']) ) );

	if ( (in_array('gzip', $encodings) || in_array('x-gzip', $encodings) || isset($_SERVER['---------------']) ) && function_exists('ob_gzhandler') && !ini_get('zlib.output_compression') ) {
		$enc = in_array('x-gzip', $encodings) ? 'x-gzip' : 'gzip';
		$cache_ext = '.gz';
	}
}

// Setup cache info
if ( $disk_cache && $cache_path ) {

	$ver = isset($_GET['ver']) ? (int) $_GET['ver'] : '';
	$cacheKey = $suffix . $ver;

	foreach ( $initArray as $v )
		$cacheKey .= $v;
	
	foreach ( $custom_js as $file )
		$cacheKey .= $file;

	$cacheKey = md5( $cacheKey );
	$cache_file = $cache_path . '/tinymce_' . $cacheKey . $cache_ext;
}

cache_javascript_headers();

// Use cached file if exists
if ( $disk_cache && file_exists($cache_file) ) {
	if ( '.gz' == $cache_ext )
		header( 'Content-Encoding: ' . $enc );

	echo getFileContents( $cache_file );
	exit;
}

foreach ( $initArray as $k => $v ) 
    $mce_options .= $k . ':"' . $v . '",';

$mce_options .= $mce_deprecated1;
$mce_options = rtrim( trim($mce_options), '\n\r,' );

$content .= 'var tinyMCEPreInit = { settings : { themes : "' . $theme . '", plugins : "' . $initArray['plugins'] . '", languages : "' . $language . '", debug : false }, base : "' . $baseurl . '", suffix : "' . $suffix . '" };';

// Load patch
$content .= getFileContents( 'tiny_mce_ext.js' );

// Add core
$content .= getFileContents( 'tiny_mce' . $suffix . '.js' );

// Patch loading functions
$content .= 'tinyMCEPreInit.start();';

// Add all languages (WP)
include_once( dirname(__FILE__).'/langs/wp-langs.php' );
$content .= $strings;

// Add themes
$content .= getFileContents( 'themes/' . $theme . '/editor_template' . $suffix . '.js' );

// Add plugins
foreach ( $plugins as $plugin ) 
	$content .= getFileContents( 'plugins/' . $plugin . '/editor_plugin' . $suffix . '.js' );

// Add custom files
foreach ( $custom_js as $file )
	$content .= getFileContents($file);

// Add external plugins and init 
$content .= $ext_plugins . 'tinyMCE.init({' . $mce_options . '});'; // $mce_deprecated2 . 

// Generate GZIP'd content
if ( '.gz' == $cache_ext ) {
	header('Content-Encoding: ' . $enc);
	$content = gzencode( $content, 9, FORCE_GZIP );
}

// Stream to client
echo $content;

// Write file
if ( '' != $cacheKey && $cache_path ) {
	if ( $old_cache_max ) {
		$old_keys = getFileContents('tinymce_compressed_key' . $cache_ext);
			
		if ( '' != $old_keys ) {
			$keys_ar = explode( "\n", $old_keys );
			if ( ($old_cache_max - 1) > count($old_keys_ar) )
				$old_keys_rem = array_slice( $keys_ar, ($old_cache_max - 1) );
			
			foreach ( $old_keys_rem as $key ) {
				$key = trim($key);
				if ( 32 != strlen($key) ) continue;
				$old_cache = $cache_path . '/tinymce_' . $key . $cache_ext;
				@unlink($old_cache);
			}
			
			array_unshift( $keys_ar, $cacheKey );
			$keys_ar = array_slice( $keys_ar, 0, $old_cache_max );
			$cacheKey = trim( implode( "\n", $keys_ar ) );
			
		}
		
		putFileContents( 'tinymce_compressed_key' . $cache_ext, $cacheKey );
	}
	
	putFileContents( $cache_file, $content );
}
?>