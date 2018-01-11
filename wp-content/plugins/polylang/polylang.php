<?php

/**
Plugin Name: Polylang
Plugin URI: https://polylang.pro
Version: 2.2.8
Author: Frédéric Demarle
Author uri: https://polylang.pro
Description: Adds multilingual capability to WordPress
Text Domain: polylang
Domain Path: /languages
 */

/*
 * Copyright 2011-2018 Frédéric Demarle
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * ( at your option ) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // don't access directly
};

define( 'POLYLANG_VERSION', '2.2.8' );
define( 'PLL_MIN_WP_VERSION', '4.4' );

define( 'POLYLANG_FILE', __FILE__ ); // this file
define( 'POLYLANG_BASENAME', plugin_basename( POLYLANG_FILE ) ); // plugin name as known by WP
define( 'POLYLANG_DIR', dirname( POLYLANG_FILE ) ); // our directory

define( 'PLL_ADMIN_INC', POLYLANG_DIR . '/admin' );
define( 'PLL_FRONT_INC', POLYLANG_DIR . '/frontend' );
define( 'PLL_INC', POLYLANG_DIR . '/include' );
define( 'PLL_INSTALL_INC', POLYLANG_DIR . '/install' );
define( 'PLL_MODULES_INC', POLYLANG_DIR . '/modules' );
define( 'PLL_SETTINGS_INC', POLYLANG_DIR . '/settings' );

require_once PLL_INC . '/class-polylang.php';
