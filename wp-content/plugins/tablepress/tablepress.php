<?php
/**
 * WordPress plugin "TablePress" main file, responsible for initiating the plugin
 *
 * @package TablePress
 * @author Tobias Bäthge
 * @version 1.9
 */

/*
Plugin Name: TablePress
Plugin URI: https://tablepress.org/
Description: Embed beautiful and feature-rich tables into your posts and pages, without having to write code.
Version: 1.9
Author: Tobias Bäthge
Author URI: https://tobias.baethge.com/
Author email: wordpress@tobias.baethge.com
Text Domain: tablepress
Domain Path: /i18n
License: GPL 2
Donate URI: https://tablepress.org/donate/
*/

/*	Copyright 2012-2017 Tobias Bäthge

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

// Define certain plugin variables as constants.
define( 'TABLEPRESS_ABSPATH', plugin_dir_path( __FILE__ ) );
define( 'TABLEPRESS__FILE__', __FILE__ );
define( 'TABLEPRESS_BASENAME', plugin_basename( TABLEPRESS__FILE__ ) );

/**
 * Load TablePress class, which holds common functions and variables.
 */
require_once TABLEPRESS_ABSPATH . 'classes/class-tablepress.php';

// Start up TablePress on WordPress's "init" action hook.
add_action( 'init', array( 'TablePress', 'run' ) );
