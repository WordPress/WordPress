<?php
/*
Plugin Name: Easy Instagram
Plugin URI: http://wordpress.org/plugins/easy-instagram/
Description: Display one or more Instagram images by user id or tag
Version: 3.1
Author: VeloMedia
Author URI: http://www.velomedia.com
Licence:
*/
if ( ! defined( 'ABSPATH' ) ) exit;

require 'include/Instagram-PHP-API/Instagram.php';
require 'include/class-easy-instagram.php';
require 'include/class-easy-instagram-widget.php';

define( 'EASY_INSTAGRAM_PLUGIN_PATH', dirname( __FILE__ ) );

$easy_instagram = new Easy_Instagram();
$GLOBALS['easy_instagram'] = $easy_instagram;

register_activation_hook( __FILE__, array( $easy_instagram, 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( $easy_instagram, 'plugin_deactivation' ) );

add_action( 'widgets_init', create_function( '', 'register_widget( "Easy_Instagram_Widget" );' ) );

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $easy_instagram, 'plugin_action_links'), 10 );

load_plugin_textdomain( 'Easy_Instagram', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
