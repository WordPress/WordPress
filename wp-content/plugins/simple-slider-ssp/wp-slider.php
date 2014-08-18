<?php
/**
Plugin Name: WordPress Slider Plugin
Plugin URI: http://rocketplugins.com/wordpress-slider-plugin/
Description: The best WordPress slider plugin. Period.
Version: 1.1
Author: Muneeb
Author URI: http://rocketplugins.com/wordpress-slider-plugin/
License: GPLv2 or later
Copyright: 2012 Muneeb ur Rehman http://muneeb.me
**/

/**
This plugin uses some of the code and an image from the acf(Advanced Custom Fields) plugin for Slider Custom Post Type UI and also inspiration for great plugin design under GPL license. Thanks You Elliot Condon
Copyright: Elliot Condon
**/

require plugin_dir_path( __FILE__ ) . 'config.php';

require SLIDER_PLUGIN_INCLUDE_DIRECTORY . 'functions.php';

muneeb_load_ssp();

muneeb_ssp_loaded();