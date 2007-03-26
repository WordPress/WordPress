<?php
/**
 * $Id: tiny_mce_gzip.php 158 2006-12-21 14:32:19Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright  2005-2006, Moxiecode Systems AB, All rights reserved.
 *
 * This file compresses the TinyMCE JavaScript using GZip and
 * enables the browser to do two requests instead of one for each .js file.
 * Notice: This script defaults the button_tile_map option to true for extra performance.
 */

	@require_once('../../../wp-config.php');  // For get_bloginfo().

	// Get input
	$plugins = explode(',', getParam("plugins", ""));
	$languages = explode(',', getParam("languages", ""));
	$themes = explode(',', getParam("themes", ""));
	$diskCache = getParam("diskcache", "") == "true";
	$isJS = getParam("js", "") == "true";
	$compress = getParam("compress", "true") == "true";
	$suffix = getParam("suffix", "_src") == "_src" ? "_src" : "";
	$cachePath = realpath("."); // Cache path, this is where the .gz files will be stored
	$expiresOffset = 3600 * 24 * 10; // Cache for 10 days in browser cache
	$content = "";
	$encodings = array();
	$supportsGzip = false;
	$enc = "";
	$cacheKey = "";

	// Custom extra javascripts to pack
	$custom = array(/*
		"some custom .js file",
		"some custom .js file"
	*/);

	// WP
	$index = getParam("index", -1);
	$theme = getParam("theme", "");
	$themes = array($theme);
	$language = getParam("language", "en");
	if ( empty($language) )
		$language = 'en';
	$languages = array($language);
	if ( $language != strtolower($language) )
		$languages[] = strtolower($language);
	if ( $language != substr($language, 0, 2) )
		$languages[] = substr($language, 0, 2);
	$diskCache = false;
	$isJS = true;
	$suffix = '';

	// Headers
	header("Content-Type: text/javascript; charset=" . get_bloginfo('charset'));
	header("Vary: Accept-Encoding");  // Handle proxies
	header("Expires: " . gmdate("D, d M Y H:i:s", time() + $expiresOffset) . " GMT");

	// Is called directly then auto init with default settings
	if (!$isJS) {
		echo getFileContents("tiny_mce_gzip.js");
		echo "tinyMCE_GZ.init({});";
		die();
	}

	// Setup cache info
	if ($diskCache) {
		if (!$cachePath)
			die("alert('Real path failed.');");

		$cacheKey = getParam("plugins", "") . getParam("languages", "") . getParam("themes", "");

		foreach ($custom as $file)
			$cacheKey .= $file;

		$cacheKey = md5($cacheKey);

		if ($compress)
			$cacheFile = $cachePath . "/tiny_mce_" . $cacheKey . ".gz";
		else
			$cacheFile = $cachePath . "/tiny_mce_" . $cacheKey . ".js";
	}

	// Check if it supports gzip
	if (isset($_SERVER['HTTP_ACCEPT_ENCODING']))
		$encodings = explode(',', strtolower(preg_replace("/\s+/", "", $_SERVER['HTTP_ACCEPT_ENCODING'])));

	if ((in_array('gzip', $encodings) || in_array('x-gzip', $encodings) || isset($_SERVER['---------------'])) && function_exists('ob_gzhandler') && !ini_get('zlib.output_compression')) {
		$enc = in_array('x-gzip', $encodings) ? "x-gzip" : "gzip";
		$supportsGzip = true;
	}

	// Use cached file disk cache
	if ($diskCache && $supportsGzip && file_exists($cacheFile)) {
		if ($compress)
			header("Content-Encoding: " . $enc);

		echo getFileContents($cacheFile);
		die();
	}

if ($index > -1) {
	// Write main script and patch some things
	if ( $index == 0 ) {
		// Add core
		$content .= wp_compact_tinymce_js(getFileContents("tiny_mce" . $suffix . ".js"));
		$content .= 'TinyMCE.prototype.orgLoadScript = TinyMCE.prototype.loadScript;';
		$content .= 'TinyMCE.prototype.loadScript = function() {};var realTinyMCE = tinyMCE;';
	} else
		$content .= 'tinyMCE = realTinyMCE;';

	// Patch loading functions
	//$content .= "tinyMCE_GZ.start();";
	
	// Do init based on index
	$content .= "tinyMCE.init(tinyMCECompressed.configs[" . $index . "]);";

	// Load external plugins
	if ( $index == 0 )
		$content .= "tinyMCECompressed.loadPlugins();";

	// Add core languages
	$lang_content = '';
	foreach ($languages as $lang)
		$lang_content .= getFileContents("langs/" . $lang . ".js");
	if ( empty($lang_content) )
		$lang_content .= getFileContents("langs/en.js");
	$content .= $lang_content;

	// Add themes
	foreach ($themes as $theme) {
		$content .= wp_compact_tinymce_js(getFileContents( "themes/" . $theme . "/editor_template" . $suffix . ".js"));

		$lang_content = '';
		foreach ($languages as $lang)
			$lang_content .= getFileContents("themes/" . $theme . "/langs/" . $lang . ".js");
		if ( empty($lang_content) )
			$lang_content .= getFileContents("themes/" . $theme . "/langs/en.js");
		$content .= $lang_content;
	}

	// Add plugins
	foreach ($plugins as $plugin) {
		$content .= getFileContents("plugins/" . $plugin . "/editor_plugin" . $suffix . ".js");

		$lang_content = '';
		foreach ($languages as $lang)
			$lang_content .= getFileContents("plugins/" . $plugin . "/langs/" . $lang . ".js");
		if ( empty($lang_content) )
			$lang_content .= getFileContents("plugins/" . $plugin . "/langs/en.js");
		$content .= $lang_content;
	}

	// Add custom files
	foreach ($custom as $file)
		$content .= getFileContents($file);

	// Reset tinyMCE compressor engine
	$content .= "tinyMCE = tinyMCECompressed;";

	// Restore loading functions
	//$content .= "tinyMCE_GZ.end();";

	// Generate GZIP'd content
	if ($supportsGzip) {
		if ($compress) {
			header("Content-Encoding: " . $enc);
			$cacheData = gzencode($content, 9, FORCE_GZIP);
		} else
			$cacheData = $content;

		// Write gz file
		if ($diskCache && $cacheKey != "")
			putFileContents($cacheFile, $cacheData);

		// Stream to client
		echo $cacheData;
	} else {
		// Stream uncompressed content
		echo $content;
	}

	die;
}

	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

	function getParam($name, $def = false) {
		if (!isset($_GET[$name]))
			return $def;

		return preg_replace("/[^0-9a-z\-_,]+/i", "", $_GET[$name]); // Remove anything but 0-9,a-z,-_
	}

	function getFileContents($path) {
		$path = realpath($path);

		if (!$path || !@is_file($path))
			return "";

		if (function_exists("file_get_contents"))
			return @file_get_contents($path);

		$content = "";
		$fp = @fopen($path, "r");
		if (!$fp)
			return "";

		while (!feof($fp))
			$content .= fgets($fp);

		fclose($fp);

		return $content;
	}

	function putFileContents($path, $content) {
		if (function_exists("file_put_contents"))
			return @file_put_contents($path, $content);

		$fp = @fopen($path, "wb");
		if ($fp) {
			fwrite($fp, $content);
			fclose($fp);
		}
	}

	// WP specific
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
