<?php
/*
Plugin Name: Contact Form 7
Plugin URI: http://contactform7.com/
Description: Just another contact form plugin. Simple but flexible.
Author: Takayuki Miyoshi
Author URI: http://ideasilo.wordpress.com/
Text Domain: contact-form-7
Domain Path: /languages/
Version: 3.9.1
*/

/*  Copyright 2007-2014 Takayuki Miyoshi (email: takayukister at gmail.com)

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
*/

define( 'WPCF7_VERSION', '3.9.1' );

define( 'WPCF7_REQUIRED_WP_VERSION', '3.8' );

define( 'WPCF7_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

define( 'WPCF7_PLUGIN_NAME', trim( dirname( WPCF7_PLUGIN_BASENAME ), '/' ) );

define( 'WPCF7_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );

define( 'WPCF7_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );

define( 'WPCF7_PLUGIN_MODULES_DIR', WPCF7_PLUGIN_DIR . '/modules' );

if ( ! defined( 'WPCF7_LOAD_JS' ) )
	define( 'WPCF7_LOAD_JS', true );

if ( ! defined( 'WPCF7_LOAD_CSS' ) )
	define( 'WPCF7_LOAD_CSS', true );

if ( ! defined( 'WPCF7_AUTOP' ) )
	define( 'WPCF7_AUTOP', true );

if ( ! defined( 'WPCF7_USE_PIPE' ) )
	define( 'WPCF7_USE_PIPE', true );

if ( ! defined( 'WPCF7_ADMIN_READ_CAPABILITY' ) )
	define( 'WPCF7_ADMIN_READ_CAPABILITY', 'edit_posts' );

if ( ! defined( 'WPCF7_ADMIN_READ_WRITE_CAPABILITY' ) )
	define( 'WPCF7_ADMIN_READ_WRITE_CAPABILITY', 'publish_pages' );

if ( ! defined( 'WPCF7_VERIFY_NONCE' ) )
	define( 'WPCF7_VERIFY_NONCE', true );

require_once WPCF7_PLUGIN_DIR . '/settings.php';

?>