<?php
/**
 * $RCSfile: tiny_mce_gzip.php,v $
 * $Revision: $
 * $Date: $
 *
 * @version 1.08
 * @author Moxiecode
 * @copyright Copyright  2005-2006, Moxiecode Systems AB, All rights reserved.
 *
 * This file compresses the TinyMCE JavaScript using GZip and
 * enables the browser to do two requests instead of one for each .js file.
 * Notice: This script defaults the button_tile_map option to true for extra performance.
 */

@require_once('../../../wp-config.php');

// gzip_compression();

function wp_tinymce_lang($path) {
	global $language;

	$text = '';

	// Look for xx_YY.js, xx_yy.js, xx.js
	$file = realpath(sprintf($path, $language));
	if ( file_exists($file) )
		$text = file_get_contents($file);
	$file = realpath(sprintf($path, strtolower($language)));
	if ( file_exists($file) )
		$text = file_get_contents($file);
	$file = realpath(sprintf($path, substr($language, 0, 2)));
	if ( file_exists($file) )
		$text = file_get_contents($file);


	// Fall back on en.js
	$file = realpath(sprintf($path, 'en'));
	if ( empty($text) && file_exists($file) )
		$text = file_get_contents($file);

	// Send lang file through gettext
	if ( function_exists('__') && strtolower(substr($language, 0, 2)) != 'en' ) {
		$search1 = "/^tinyMCELang\\[(['\"])(.*)\\1\]( ?= ?)(['\"])(.*)\\4/Uem";
		$replace1 = "'tinyMCELang[\\1\\2\\1]\\3'.stripslashes('\\4').__('\\5').stripslashes('\\4')";

		$search2 = "/\\s:\\s(['\"])(.*)\\1(,|\\s*})/Uem";
		$replace2 = "' : '.stripslashes('\\1').__('\\2').stripslashes('\\1').'\\3'";

		$search = array($search1, $search2);
		$replace = array($replace1, $replace2);

		$text = preg_replace($search, $replace, $text);

		return $text;
	}

	return $text;
}

function wp_compact_tinymce_js($text) {
	// This function was custom-made for TinyMCE 2.0, not expected to work with any other JS.

	// Strip comments
	$text = preg_replace("!(^|\s+)//.*$!m", '', $text);
	$text = preg_replace("!/\*.*?\*/!s", '', $text);

	// Strip leading tabs, carriage returns and unnecessary line breaks.
	$text = preg_replace("!^\t+!m", '', $text);
	$text = str_replace("\r", '', $text);
	$text = preg_replace("!(^|{|}|;|:|\))\n!m", '\\1', $text);

	return "$text\n";
}


// General options
$suffix = "";							// Set to "_src" to use source version
$expiresOffset = 3600 * 24 * 10;		// 10 days util client cache expires
$diskCache = false;						// If you enable this option gzip files will be cached on disk.
$cacheDir = realpath(".");				// Absolute directory path to where cached gz files will be stored
$debug = false;							// Enable this option if you need debuging info

// Headers
header("Content-Type: text/javascript; charset=" . get_bloginfo('charset'));
// header("Cache-Control: must-revalidate");
header("Vary: Accept-Encoding"); // Handle proxies
header("Expires: " . gmdate("D, d M Y H:i:s", time() + $expiresOffset) . " GMT");

// Get data to load
$theme = isset($_GET['theme']) ? TinyMCE_cleanInput($_GET['theme']) : "";
$language = isset($_GET['language']) ? TinyMCE_cleanInput($_GET['language']) : "";
$plugins = isset($_GET['plugins']) ? TinyMCE_cleanInput($_GET['plugins']) : "";
$lang = isset($_GET['lang']) ? TinyMCE_cleanInput($_GET['lang']) : "en";
$index = isset($_GET['index']) ? TinyMCE_cleanInput($_GET['index']) : -1;
$cacheKey = md5($theme . $language . $plugins . $lang . $index . $debug);
$cacheFile = $cacheDir == "" ? "" : $cacheDir . "/" . "tinymce_" .  $cacheKey . ".gz";
$cacheData = "";

// Patch older versions of PHP < 4.3.0
if (!function_exists('file_get_contents')) {
	function file_get_contents($filename) {
		$fd = fopen($filename, 'rb');
		$content = fread($fd, filesize($filename));
		fclose($fd);
		return $content;
	}
}

// Security check function, can only contain a-z 0-9 , _ - and whitespace.
function TinyMCE_cleanInput($str) {
	return preg_replace("/[^0-9a-z\-_,]+/i", "", $str); // Remove anything but 0-9,a-z,-_
}

function TinyMCE_echo($str) {
	global $cacheData, $diskCache;

	if ($diskCache)
		$cacheData .= $str;
	else
		echo $str;
}

// Only gzip the contents if clients and server support it
$encodings = array();

if (isset($_SERVER['HTTP_ACCEPT_ENCODING']))
	$encodings = explode(',', strtolower(preg_replace("/\s+/", "", $_SERVER['HTTP_ACCEPT_ENCODING'])));

// Check for gzip header or northon internet securities
if ((in_array('gzip', $encodings) || in_array('x-gzip', $encodings) || isset($_SERVER['---------------'])) && function_exists('ob_gzhandler') && !ini_get('zlib.output_compression')) {
	$enc = in_array('x-gzip', $encodings) ? "x-gzip" : "gzip";

	// Use cached file if it exists but not in debug mode
	if (file_exists($cacheFile) && !$debug) {
		header("Content-Encoding: " . $enc);
		echo file_get_contents($cacheFile);
		die;
	}

	if (!$diskCache)
		ob_start("ob_gzhandler");
} else
	$diskCache = false;

if ($index > -1) {
	// Write main script and patch some things
	if ($index == 0) {
		TinyMCE_echo(wp_compact_tinymce_js(file_get_contents(realpath("tiny_mce" . $suffix . ".js")))); // WP
		TinyMCE_echo('TinyMCE.prototype.orgLoadScript = TinyMCE.prototype.loadScript;');
		TinyMCE_echo('TinyMCE.prototype.loadScript = function() {};var realTinyMCE = tinyMCE;');
	} else
		TinyMCE_echo('tinyMCE = realTinyMCE;');

	// Do init based on index
	TinyMCE_echo("tinyMCE.init(tinyMCECompressed.configs[" . $index . "]);");

	// Load external plugins
	if ($index == 0)
		TinyMCE_echo("tinyMCECompressed.loadPlugins();");

	// Load theme, language pack and theme language packs
	if ($theme) {
		TinyMCE_echo(wp_compact_tinymce_js(file_get_contents(realpath("themes/" . $theme . "/editor_template" . $suffix . ".js")))); // WP
		TinyMCE_echo(wp_tinymce_lang("themes/" . $theme . "/langs/%s.js")); // WP
	}

	/* WP if ($language) WP */
		TinyMCE_echo(wp_tinymce_lang("langs/%s.js")); // WP

	// Load all plugins and their language packs
	$plugins = explode(",", $plugins);
	foreach ($plugins as $plugin) {
		$pluginFile = realpath("plugins/" . $plugin . "/editor_plugin" . $suffix . ".js");
		/* WP $languageFile = realpath("plugins/" . $plugin . "/langs/" . $lang . ".js"); WP */

		if ($pluginFile)
			TinyMCE_echo(file_get_contents($pluginFile));

		/* WP if ($languageFile) WP */
			TinyMCE_echo(wp_tinymce_lang("plugins/" . $plugin . "/langs/%s.js")); // WP
	}

	// Reset tinyMCE compressor engine
	TinyMCE_echo("tinyMCE = tinyMCECompressed;");

	// Write to cache
	if ($diskCache) {
		// Calculate compression ratio and debug target output path
		if ($debug) {
			$ratio = round(100 - strlen(gzencode($cacheData, 9, FORCE_GZIP)) / strlen($cacheData) * 100.0);
			TinyMCE_echo("alert('TinyMCE was compressed by " . $ratio . "%.\\nOutput cache file: " . $cacheFile . "');");
		}

		$cacheData = gzencode($cacheData, 9, FORCE_GZIP);

		// Write to file if possible
		$fp = @fopen($cacheFile, "wb");
		if ($fp) {
			fwrite($fp, $cacheData);
			fclose($fp);
		}

		// Output
		header("Content-Encoding: " . $enc);
		echo $cacheData;
	}

	die;
}
?>

function TinyMCECompressed() {
	this.configs = new Array();
	this.loadedFiles = new Array();
	this.externalPlugins = new Array();
	this.loadAdded = false;
	this.isLoaded = false;
}

TinyMCECompressed.prototype.init = function(settings) {
	var elements = document.getElementsByTagName('script');
	var scriptURL = "";

	for (var i=0; i<elements.length; i++) {
		if (elements[i].src && elements[i].src.indexOf("tiny_mce_gzip.php") != -1) {
			scriptURL = elements[i].src;
			break;
		}
	}

	settings["theme"] = typeof(settings["theme"]) != "undefined" ? settings["theme"] : "default";
	settings["plugins"] = typeof(settings["plugins"]) != "undefined" ? settings["plugins"] : "";
	settings["language"] = typeof(settings["language"]) != "undefined" ? settings["language"] : "en";
	settings["button_tile_map"] = typeof(settings["button_tile_map"]) != "undefined" ? settings["button_tile_map"] : true;
	this.configs[this.configs.length] = settings;
	this.settings = settings;

	scriptURL += (scriptURL.indexOf('?') == -1) ? '?' : '&';
	scriptURL += "theme=" + escape(this.getOnce(settings["theme"])) + "&language=" + escape(this.getOnce(settings["language"])) + "&plugins=" + escape(this.getOnce(settings["plugins"])) + "&lang=" + settings["language"] + "&index=" + escape(this.configs.length-1);
	document.write('<sc'+'ript language="javascript" type="text/javascript" src="' + scriptURL + '"></script>');

	if (!this.loadAdded) {
		tinyMCE.addEvent(window, "DOMContentLoaded", TinyMCECompressed.prototype.onLoad);
		tinyMCE.addEvent(window, "load", TinyMCECompressed.prototype.onLoad);
		this.loadAdded = true;
	}
}

TinyMCECompressed.prototype.onLoad = function() {
	if (tinyMCE.isLoaded)
		return true;

	tinyMCE = realTinyMCE;
	TinyMCE_Engine.prototype.onLoad();
	tinyMCE._addUnloadEvents();

	tinyMCE.isLoaded = true;
}

TinyMCECompressed.prototype.addEvent = function(o, n, h) {
	if (o.attachEvent)
		o.attachEvent("on" + n, h);
	else
		o.addEventListener(n, h, false);
}

TinyMCECompressed.prototype.getOnce = function(str) {
	var ar = str.replace(/\s+/g, '').split(',');

	for (var i=0; i<ar.length; i++) {
		if (ar[i] == '' || ar[i].charAt(0) == '-') {
			ar[i] = null;
			continue;
		}

		// Skip load
		for (var x=0; x<this.loadedFiles.length; x++) {
			if (this.loadedFiles[x] == ar[i])
				ar[i] = null;
		}

		this.loadedFiles[this.loadedFiles.length] = ar[i];
	}

	// Glue
	str = "";
	for (var i=0; i<ar.length; i++) {
		if (ar[i] == null)
			continue;

		str += ar[i];

		if (i != ar.length-1)
			str += ",";
	}

	return str;
};

TinyMCECompressed.prototype.loadPlugins = function() {
	var i, ar;

	TinyMCE.prototype.loadScript = TinyMCE.prototype.orgLoadScript;
	tinyMCE = realTinyMCE;

	ar = tinyMCECompressed.externalPlugins;
	for (i=0; i<ar.length; i++)
		tinyMCE.loadPlugin(ar[i].name, ar[i].url);

	TinyMCE.prototype.loadScript = function() {};
};

TinyMCECompressed.prototype.loadPlugin = function(n, u) {
	this.externalPlugins[this.externalPlugins.length] = {name : n, url : u};
};

TinyMCECompressed.prototype.importPluginLanguagePack = function(n, v) {
	tinyMCE = realTinyMCE;
	TinyMCE.prototype.loadScript = TinyMCE.prototype.orgLoadScript;
	tinyMCE.importPluginLanguagePack(n, v);
};

TinyMCECompressed.prototype.addPlugin = function(n, p) {
	tinyMCE = realTinyMCE;
	tinyMCE.addPlugin(n, p);
};

var tinyMCE = new TinyMCECompressed();
var tinyMCECompressed = tinyMCE;
