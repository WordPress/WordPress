<?php
/** Based on:
 * $Id: tiny_mce_gzip.php 315 2007-10-25 14:03:43Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2005-2006, Moxiecode Systems AB, All rights reserved.
 *
 * This file compresses the TinyMCE JavaScript using GZip and
 * enables the browser to do two requests instead of one for each .js file.
 * Notice: This script defaults the button_tile_map option to true for extra performance.
 */

@require_once('../../../wp-config.php');  // For get_bloginfo().
cache_javascript_headers();

if ( isset($_GET['load']) ) {

	function getParam( $name, $def = false ) {
		if ( ! isset($_GET[$name]) )
			return $def;

		return preg_replace( "/[^0-9a-z\-_,]+/i", "", $_GET[$name] ); // Remove anything but 0-9,a-z,-_
	}

	function getFileContents($path) {
		$path = realpath($path);

		if ( ! $path || !@is_file($path) )
			return '';

		if ( function_exists('file_get_contents') )
			return @file_get_contents($path);

		$content = '';
		$fp = @fopen( $path, 'r' );
		if (!$fp)
			return '';

		while ( ! feof($fp) )
			$content .= fgets($fp);

		fclose($fp);

		return $content;
	}

	function putFileContents( $path, $content ) {
		if ( function_exists('file_put_contents') )
			return @file_put_contents( $path, $content );

		$fp = @fopen($path, 'wb');
		if ($fp) {
			fwrite($fp, $content);
			fclose($fp);
		}
	}

	// WP defaults
	$themes = explode( ',', getParam('themes', 'advanced') );

	$language = getParam( 'languages', 'en' );
	$language = strtolower( substr($language, 0, 2) ); // only ISO 639-1

	$plugins = explode( ',', getParam('plugins', '') );
	$cachePath = realpath('.'); // Cache path, this is where the .gz files will be stored

	$encodings = array();
	$supportsGzip = $diskCache = false;
	$compress = $core = true;
	$suffix = $content = $enc = $cacheKey = '';

	// Custom extra javascripts to pack
	// WP - add a hook for external plugins to be compressed too?
	$custom = array(/*
		'some custom .js file',
		'some custom .js file'
	*/);

	// Setup cache info
	if ( $diskCache ) {
		if ( ! $cachePath )
			die('Real path failed.');

		$cacheKey = getParam( 'plugins', '' ) . getParam( 'languages', '' ) . getParam( 'themes', '' ) . $suffix;

		foreach ( $custom as $file )
			$cacheKey .= $file;

		$cacheKey = md5($cacheKey);

		if ( $compress )
			$cacheFile = $cachePath . '/tiny_mce_' . $cacheKey . '.gz';
		else
			$cacheFile = $cachePath . '/tiny_mce_' . $cacheKey . '.js';
	}

	// Check if it supports gzip
	if ( isset( $_SERVER['HTTP_ACCEPT_ENCODING'] ) )
		$encodings = explode(',', strtolower(preg_replace( '/\s+/', '', $_SERVER['HTTP_ACCEPT_ENCODING']) ) );

	if ( ( in_array('gzip', $encodings ) || in_array( 'x-gzip', $encodings ) || isset($_SERVER['---------------']) ) && function_exists( 'ob_gzhandler' ) && ! ini_get('zlib.output_compression') ) {
		$enc = in_array( 'x-gzip', $encodings ) ? 'x-gzip' : 'gzip';
		$supportsGzip = true;
	}

	// Use cached file disk cache
	if ( $diskCache && $supportsGzip && file_exists($cacheFile) ) {
		if ( $compress )
			header('Content-Encoding: ' . $enc);

		echo getFileContents( $cacheFile );
		die();
	}

	// Add core
	if ( $core == 'true' ) {
		$content .= getFileContents( 'tiny_mce' . $suffix . '.js' );

		// Patch loading functions
		$content .= 'tinyMCE_GZ.start();';
	}

	// Add all languages (WP)
	include_once( dirname(__file__).'/langs/wp-langs.php' );
	$content .= $strings;

	// Add themes
	foreach ( $themes as $theme ) 
		$content .= getFileContents( 'themes/' . $theme . '/editor_template' . $suffix . '.js' );
	
	// Add plugins
	foreach ( $plugins as $plugin ) 
		$content .= getFileContents( 'plugins/' . $plugin . '/editor_plugin' . $suffix . '.js' );

	// Add custom files
	foreach ( $custom as $file )
		$content .= getFileContents($file);

	// Restore loading functions
	if ( $core == 'true' )
		$content .= 'tinyMCE_GZ.end();';

	// Generate GZIP'd content
	if ( $supportsGzip ) {
		if ( $compress ) {
			header('Content-Encoding: ' . $enc);
			$cacheData = gzencode( $content, 9, FORCE_GZIP );
		} else
			$cacheData = $content;

		// Write gz file
		if ( $diskCache && '' != $cacheKey )
			putFileContents( $cacheFile, $cacheData );

		// Stream to client
		echo $cacheData;
	} else {
		// Stream uncompressed content
		echo $content;
	}

	exit;
}
?>

var tinyMCEPreInit = {suffix : ''};

var tinyMCE_GZ = {
	settings : {
		themes : '',
		plugins : '',
		languages : '',
		disk_cache : false,
		page_name : 'tiny_mce_gzip.php',
		debug : false,
		suffix : ''
	},

	opt : {},

	init : function(o, cb) {
		var t = this, n, s = t.settings, nl = document.getElementsByTagName('script');

		t.opt = o;

		s.themes = o.theme;
		s.plugins = o.plugins; 
		s.languages = o.language;
		t.settings = s;

		t.cb = cb || '';

		for (i=0; i<nl.length; i++) {
			n = nl[i];

			if (n.src && n.src.indexOf('tiny_mce') != -1)
				t.baseURL = n.src.substring(0, n.src.lastIndexOf('/'));
		}
		tinyMCEPreInit.base = t.baseURL;
		
		if (!t.coreLoaded)
			t.loadScripts(1, s.themes, s.plugins, s.languages);
	},

	loadScripts : function(co, th, pl, la, cb, sc) {
		var t = this, x, w = window, q, c = 0, ti, s = t.settings;

		function get(s) {
			x = 0;

			try {
				x = new ActiveXObject(s);
			} catch (s) {
			}

			return x;
		};

		// Build query string
		q = 'load=true&js=true&diskcache=' + (s.disk_cache ? 'true' : 'false') + '&core=' + (co ? 'true' : 'false') + '&suffix=' + escape(s.suffix) + '&themes=' + escape(th) + '&plugins=' + escape(pl) + '&languages=' + escape(la);

		if (co)
			t.coreLoaded = 1;

	//	document.write('<sc'+'ript language="javascript" type="text/javascript" src="' + t.baseURL + '/' + s.page_name + '?' + q + '"></script>');

		// Send request
		x = w.XMLHttpRequest ? new XMLHttpRequest() : get('Msxml2.XMLHTTP') || get('Microsoft.XMLHTTP');
		x.overrideMimeType && x.overrideMimeType('text/javascript');
		x.open('GET', t.baseURL + '/' + s.page_name + '?' + q, !!cb);
//		x.setRequestHeader('Content-Type', 'text/javascript');
		x.send('');

		// Handle asyncronous loading
		if (cb) {
			// Wait for response
			ti = w.setInterval(function() {
				if (x.readyState == 4 || c++ > 10000) {
					w.clearInterval(ti);

					if (c < 10000 && x.status == 200) {
						t.loaded = 1;
						t.eval(x.responseText);
						tinymce.dom.Event.domLoaded = true;
					//	cb.call(sc || t, x);
					}

					ti = x = null;
				}
			}, 10);
		} else
			t.eval(x.responseText);
	},

	start : function() {
		var t = this, each = tinymce.each, s = t.settings, sl, ln = s.languages.split(',');

		tinymce.suffix = s.suffix;

		// Extend script loader
		tinymce.create('tinymce.compressor.ScriptLoader:tinymce.dom.ScriptLoader', {
			loadScripts : function(sc, cb, s) {
				var ti = this, th = [], pl = [], la = [];

				each(sc, function(o) {
					var u = o.url;

					if ((!ti.lookup[u] || ti.lookup[u].state != 2) && u.indexOf(t.baseURL) === 0) {
						// Collect theme
						if (u.indexOf('editor_template') != -1) {
							th.push(/\/themes\/([^\/]+)/.exec(u)[1]);
							load(u, 1);
						}

						// Collect plugin
						if (u.indexOf('editor_plugin') != -1) {
							pl.push(/\/plugins\/([^\/]+)/.exec(u)[1]);
							load(u, 1);
						}

						// Collect language
						if (u.indexOf('/langs/') != -1) {
							la.push(/\/langs\/([^.]+)/.exec(u)[1]);
							load(u, 1);
						}
					}
				});

				if (th.length + pl.length + la.length > 0) {
					if (sl.settings.strict_mode) {
						// Async
						t.loadScripts(0, th.join(','), pl.join(','), la.join(','), cb, s);
						return;
					} else
						t.loadScripts(0, th.join(','), pl.join(','), la.join(','), cb, s);
				}

				return ti.parent(sc, cb, s);
			}
		});

		sl = tinymce.ScriptLoader = new tinymce.compressor.ScriptLoader();

		function load(u, sp) {
			var o;

			if (!sp)
				u = t.baseURL + u;

			o = {url : u, state : 2};
			sl.queue.push(o);
			sl.lookup[o.url] = o;
		};

		// Add core languages
		each (ln, function(c) {
			if (c)
				load('/langs/' + c + '.js');
		});

		// Add themes with languages
		each(s.themes.split(','), function(n) {
			if (n) {
				load('/themes/' + n + '/editor_template' + s.suffix + '.js');

				each (ln, function(c) {
					if (c)
						load('/themes/' + n + '/langs/' + c + '.js');
				});
			}
		});

		// Add plugins with languages
		each(s.plugins.split(','), function(n) {
			if (n) {
				load('/plugins/' + n + '/editor_plugin' + s.suffix + '.js');

				each (ln, function(c) {
					if (c)
						load('/plugins/' + n + '/langs/' + c + '.js');
				});
			}
		});
	},

	end : function() {
	   tinyMCE.init(this.opt);
	},

	eval : function(co) {
		var w = window;

		// Evaluate script
		if (!w.execScript) {
			try {
				eval.call(w, co);
			} catch (ex) {
				eval(co, w); // Firefox 3.0a8
			}
		} else
			w.execScript(co); // IE
	}
};
