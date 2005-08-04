<?php
	/**
	 * $RCSfile: tiny_mce_gzip.php,v $
	 * $Revision: 1.1 $
	 * $Date: 2005/06/14 18:55:34 $
	 *
	 * @author Moxiecode
	 * @copyright Copyright © 2004, Moxiecode Systems AB, All rights reserved.
	 *
	 * This file compresses the TinyMCE JavaScript using GZip and
	 * enables the browser to do two requests instead of one for each .js file.
	 * Notice: This script defaults the button_tile_map option to true for extra performance.
	 *
	 * Todo:
	 *  - Add local file cache for the GZip:ed version.
	 */

	// General options
	$suffix = "";							// Set to "_src" to use source version
	$expiresOffset = 3600 * 24 * 10;		// 10 days util client cache expires

	// Get data to load
	$theme = isset($_REQUEST['theme']) ? $_REQUEST['theme'] : "";
	$language = isset($_REQUEST['language']) ? $_REQUEST['language'] : "";
	$plugins = isset($_REQUEST['plugins']) ? $_REQUEST['plugins'] : "";

	// GZip compress and cache it for 10 days
	ob_start ("ob_gzhandler");
	header("Content-type: text/javascript; charset: UTF-8");
	header("Cache-Control: must-revalidate");
	header("Expires: " . gmdate("D, d M Y H:i:s", time() + $expiresOffset) . " GMT");

	if ($theme) {
		// Write main script and patch some things
		echo file_get_contents(realpath("tiny_mce" . $suffix . ".js"));
		echo 'TinyMCE.prototype.loadScript = function() {};';
		echo "tinyMCE.init(TinyMCECompressed_settings);";

		// Load theme, language pack and theme language packs
		echo file_get_contents(realpath("themes/" . $theme . "/editor_template" . $suffix . ".js"));
		echo file_get_contents(realpath("themes/" . $theme . "/langs/" . $language . ".js"));
		echo file_get_contents(realpath("langs/" . $language . ".js"));

		// Load all plugins and their language packs
		$plugins = explode(",", $plugins);
		foreach ($plugins as $plugin) {
			$pluginFile = realpath("plugins/" . $plugin . "/editor_plugin" . $suffix . ".js");
			$languageFile = realpath("plugins/" . $plugin . "/langs/" . $language . ".js");

			if ($pluginFile)
				echo file_get_contents($pluginFile);

			if ($languageFile)
				echo file_get_contents($languageFile);
		}

		die;
	}
?>

var TinyMCECompressed_settings = null;

function TinyMCECompressed() {
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

	scriptURL += "?theme=" + escape(settings["theme"]) + "&language=" + escape(settings["language"]) + "&plugins=" + escape(settings["plugins"]);
	document.write('<sc'+'ript language="javascript" type="text/javascript" src="' + scriptURL + '"></script>');

	TinyMCECompressed_settings = settings;
}

var tinyMCE = new TinyMCECompressed();
