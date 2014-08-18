<?php
/*
Plugin Name: Custom Sidebars
Plugin URI:  http://premium.wpmudev.org/project/custom-sidebars/
Description: Allows you to create widgetized areas and custom sidebars. Replace whole sidebars or single widgets for specific posts and pages.
Version:     2.0.7
Author:      WPMU DEV
Author URI:  http://premium.wpmudev.org/
Textdomain:  custom-sidebars
WDP ID:      910520
*/

/*
Copyright Incsub (http://incsub.com)
Author - Javier Marquez (http://arqex.com/)
Contributor - Philipp Stracker (Incsub)
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/*
This plugin was originally developed by Javier Marquez.
http://arqex.com/
*/

if ( ! class_exists( 'CustomSidebars' ) ) {

	define( 'CSB_VERSION', '2.0.7' );                  // Plugin version number.

	// used for more readable i18n functions: __( 'text', CSB_LANG );
	define( 'CSB_LANG', 'custom-sidebars' );

	$plugin_dir = dirname( __FILE__ );
	$plugin_dir_rel = dirname( plugin_basename( __FILE__ ) );
	$plugin_url = plugin_dir_url( __FILE__ );
	define( 'CSB_LANG_DIR', $plugin_dir_rel . '/lang/' );
	define( 'CSB_VIEWS_DIR', $plugin_dir . '/views/' );
	define( 'CSB_INC_DIR', $plugin_dir . '/inc/' );
	define( 'CSB_JS_URL', $plugin_url . 'js/' );
	define( 'CSB_CSS_URL', $plugin_url . 'css/' );

	// Load the actual core.
	require_once 'inc/class-custom-sidebars.php';
}

// Include function library
if ( file_exists( CSB_INC_DIR . 'external/wpmu-lib/core.php' ) ) {
	require_once CSB_INC_DIR . 'external/wpmu-lib/core.php';
}

// Initialize the plugin
$plugin_sidebars = CustomSidebars::instance();

if ( ! class_exists( 'CustomSidebarsEmptyPlugin' ) ) {
	class CustomSidebarsEmptyPlugin extends WP_Widget {
		public function CustomSidebarsEmptyPlugin() {
			parent::WP_Widget( false, $name = 'CustomSidebarsEmptyPlugin' );
		}
		public function form( $instance ) {
			//Nothing, just a dummy plugin to display nothing
		}
		public function update( $new_instance, $old_instance ) {
			//Nothing, just a dummy plugin to display nothing
		}
		public function widget( $args, $instance ) {
			echo '';
		}
	} //end class
} //end if class exists
