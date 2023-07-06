<?php
/**
 * Classic Widgets
 *
 * Plugin Name: Classic Widgets
 * Plugin URI:  https://wordpress.org/plugins/classic-widgets/
 * Description: Enables the classic widgets settings screens in Appearance - Widgets and the Customizer. Disables the block editor from managing widgets.
 * Version:     0.3
 * Author:      WordPress Contributors
 * Author URI:  https://github.com/WordPress/classic-widgets/
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: classic-widgets
 * Domain Path: /languages
 * Requires at least: 4.9
 * Requires PHP: 5.6 or later
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation. You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Invalid request.' );
}

// Disables the block editor from managing widgets in the Gutenberg plugin.
add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );
// Disables the block editor from managing widgets.
add_filter( 'use_widgets_block_editor', '__return_false' );
