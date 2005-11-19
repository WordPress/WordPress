<?php
	/**
	 * $RCSfile: tiny_mce_gzip.php,v $
	 * $Revision: $
	 * $Date: $
	 *
	 * @version 1.02
	 * @author Moxiecode
	 * @copyright Copyright © 2005, Moxiecode Systems AB, All rights reserved.
	 *
	 * This file compresses the TinyMCE JavaScript using GZip and
	 * enables the browser to do two requests instead of one for each .js file.
	 * Notice: This script defaults the button_tile_map option to true for extra performance.
	 *
	 * Todo:
	 *  - Add local file cache for the GZip:ed version.
	 */

	@ include('../../../wp-config.php');

	function wp_translate_tinymce_lang($text) {
		if ( ! function_exists('__') ) {
			return $text;
		} else {
			$search1 = "/^tinyMCELang\\[(['\"])(.*)\\1\]( ?= ?)(['\"])(.*)\\4/Uem";
			$replace1 = "'tinyMCELang[\\1\\2\\1]\\3'.stripslashes('\\4').__('\\5').stripslashes('\\4')";

			$search2 = "/ : (['\"])(.*)\\1/Uem";
			$replace2 = "' : '.stripslashes('\\1').__('\\2').stripslashes('\\1')";

			$search = array($search1, $search2);
			$replace = array($replace1, $replace2);

			$text = preg_replace($search, $replace, $text);

			return $text;
		}
	}

	// General options
	$suffix = "";							// Set to "_src" to use source version
	$expiresOffset = 3600 * 24 * 10;		// 10 days util client cache expires

	// Get data to load
	$theme = isset($_REQUEST['theme']) ? $_REQUEST['theme'] : "";
	$language = isset($_REQUEST['language']) ? $_REQUEST['language'] : "";
	$plugins = isset($_REQUEST['plugins']) ? $_REQUEST['plugins'] : "";
	$lang = isset($_REQUEST['lang']) ? $_REQUEST['lang'] : "en";
	$index = isset($_REQUEST['index']) ? $_REQUEST['index'] : -1;

	// Only gzip the contents if clients and server support it
	$encodings = explode(',', strtolower($_SERVER['HTTP_ACCEPT_ENCODING']));
	if (in_array('gzip', $encodings) && function_exists('ob_gzhandler'))
		ob_start("ob_gzhandler");

	// Output rest of headers
	header("Content-type: text/javascript; charset: UTF-8");
	// header("Cache-Control: must-revalidate");
	header("Vary: Accept-Encoding"); // Handle proxies
	header("Expires: " . gmdate("D, d M Y H:i:s", time() + $expiresOffset) . " GMT");

	if ($index > -1) {
		// Write main script and patch some things
		if ($index == 0) {
			echo file_get_contents(realpath("tiny_mce" . $suffix . ".js"));
			echo "\n\n";
			echo "TinyMCE.prototype.loadScript = function() {};\n";
		}

		// WP
		$lang = $language = 'en';
		echo "\n/* WP Cancels all TinyMCE language handling */\n";
		echo "TinyMCE.prototype.importThemeLanguagePack = function() {};\n";
		echo "TinyMCE.prototype.importPluginLanguagePack = function() {};\n\n";

		// Do init based on index
// WP		echo "tinyMCE.init(tinyMCECompressed.configs[" . $index . "]);\n\n";

		// Load theme, language pack and theme language packs
		if ($theme) {
			echo file_get_contents(realpath("themes/" . $theme . "/editor_template" . $suffix . ".js"));
			echo wp_translate_tinymce_lang(file_get_contents(realpath("themes/" . $theme . "/langs/" . $lang . ".js")));
		}

		if ($language)
			echo wp_translate_tinymce_lang(file_get_contents(realpath("langs/" . $language . ".js")));

		// Load all plugins and their language packs
		$plugins = explode(",", $plugins);
		foreach ($plugins as $plugin) {
			$pluginFile = realpath("plugins/" . $plugin . "/editor_plugin" . $suffix . ".js");
			$languageFile = realpath("plugins/" . $plugin . "/langs/" . $lang . ".js");

			if ($pluginFile)
				echo file_get_contents($pluginFile);

			if ($languageFile)
				echo wp_translate_tinymce_lang(file_get_contents($languageFile));
		}

		die;
	}
?>

function TinyMCECompressed() {
	this.configs = new Array();
	this.loadedFiles = new Array();
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

	scriptURL += "?theme=" + escape(this.getOnce(settings["theme"])) + "&language=" + escape(this.getOnce(settings["language"])) + "&plugins=" + escape(this.getOnce(settings["plugins"])) + "&lang=" + settings["language"] + "&index=" + escape(this.configs.length-1);
	document.write('<sc'+'ript language="javascript" type="text/javascript" src="' + scriptURL + '"></script>');
}

TinyMCECompressed.prototype.getOnce = function(str) {
	var ar = str.split(',');

	for (var i=0; i<ar.length; i++) {
		if (ar[i] == '')
			continue;

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
}

var tinyMCE = new TinyMCECompressed();
var tinyMCECompressed = tinyMCE;
