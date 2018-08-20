<?php
/**
 * Plugin Name: Preferred Languages
 * Plugin URI:  https://github.com/swissspidy/preferred-languages/
 * Description: Choose languages for displaying WordPress in, in order of preference.
 * Version:     1.4.0
 * Author:      Pascal Birchler
 * Author URI:  https://pascalbirchler.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: preferred-languages
 * Domain Path: /languages
 *
 * Copyright (c) 2017 Pascal Birchler (email: swissspidy@chat.wordpress.org)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package PreferredLanguages
 */

/**
 * Text domain registry class.
 */
require_once( dirname( __FILE__ ) . '/inc/class-preferred-languages-textdomain-registry.php' );

/**
 * Plugin functions.
 */
require_once( dirname( __FILE__ ) . '/inc/functions.php' );

/**
 * Adds all plugin actions and filters.
 */
require_once( dirname( __FILE__ ) . '/inc/default-filters.php' );
