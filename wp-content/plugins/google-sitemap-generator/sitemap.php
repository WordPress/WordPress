<?php

/*
 $Id: sitemap.php 1026247 2014-11-15 16:47:36Z arnee $

 Google XML Sitemaps Generator for WordPress
 ==============================================================================

 This generator will create a sitemaps.org compliant sitemap of your WordPress blog.

 For additional details like installation instructions, please check the readme.txt and documentation.txt files.

 Have fun!
   Arne

 Info for WordPress:
 ==============================================================================
 Plugin Name: Google XML Sitemaps
 Plugin URI: http://www.arnebrachhold.de/redir/sitemap-home/
 Description: This plugin will generate a special XML sitemap which will help search engines like Google, Yahoo, Bing and Ask.com to better index your blog.
 Version: 4.0.8
 Author: Arne Brachhold
 Author URI: http://www.arnebrachhold.de/
 Text Domain: sitemap
 Domain Path: /lang


 Copyright 2005 - 2014 ARNE BRACHHOLD  (email : himself - arnebrachhold - de)

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

 Please see license.txt for the full license.

*/

define("SM_SUPPORTFEED_URL","http://plugin-info.arnebrachhold.de/support/support_4.xml");

/**
 * Check if the requirements of the sitemap plugin are met and loads the actual loader
 *
 * @package sitemap
 * @since 4.0
 */
function sm_Setup() {

	$fail = false;

	//Check minimum PHP requirements, which is 5.2 at the moment.
	if (version_compare(PHP_VERSION, "5.2", "<")) {
		add_action('admin_notices', 'sm_AddPhpVersionError');
		$fail = true;
	}

	//Check minimum WP requirements, which is 3.3 at the moment.
	if (version_compare($GLOBALS["wp_version"], "3.3", "<")) {
		add_action('admin_notices', 'sm_AddWpVersionError');
		$fail = true;
	}

	if (!$fail) {
		require_once(trailingslashit(dirname(__FILE__)) . "sitemap-loader.php");
	}

}

/**
 * Adds a notice to the admin interface that the WordPress version is too old for the plugin
 *
 * @package sitemap
 * @since 4.0
 */
function sm_AddWpVersionError() {
	echo "<div id='sm-version-error' class='error fade'><p><strong>" . __('Your WordPress version is too old for XML Sitemaps.', 'sitemap') . "</strong><br /> " . sprintf(__('Unfortunately this release of Google XML Sitemaps requires at least WordPress %4$s. You are using Wordpress %2$s, which is out-dated and insecure. Please upgrade or go to <a href="%1$s">active plugins</a> and deactivate the Google XML Sitemaps plugin to hide this message. You can download an older version of this plugin from the <a href="%3$s">plugin website</a>.', 'sitemap'), "plugins.php?plugin_status=active", $GLOBALS["wp_version"], "http://www.arnebrachhold.de/redir/sitemap-home/","3.3") . "</p></div>";
}

/**
 * Adds a notice to the admin interface that the WordPress version is too old for the plugin
 *
 * @package sitemap
 * @since 4.0
 */
function sm_AddPhpVersionError() {
	echo "<div id='sm-version-error' class='error fade'><p><strong>" . __('Your PHP version is too old for XML Sitemaps.', 'sitemap') . "</strong><br /> " . sprintf(__('Unfortunately this release of Google XML Sitemaps requires at least PHP %4$s. You are using PHP %2$s, which is out-dated and insecure. Please ask your web host to update your PHP installation or go to <a href="%1$s">active plugins</a> and deactivate the Google XML Sitemaps plugin to hide this message. You can download an older version of this plugin from the <a href="%3$s">plugin website</a>.', 'sitemap'), "plugins.php?plugin_status=active", PHP_VERSION, "http://www.arnebrachhold.de/redir/sitemap-home/","5.2") . "</p></div>";
}

/**
 * Returns the file used to load the sitemap plugin
 *
 * @package sitemap
 * @since 4.0
 * @return string The path and file of the sitemap plugin entry point
 */
function sm_GetInitFile() {
	return __FILE__;
}

//Don't do anything if this file was called directly
if (defined('ABSPATH') && defined('WPINC') && !class_exists("GoogleSitemapGeneratorLoader", false)) {
	sm_Setup();
}

