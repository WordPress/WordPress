<?php
/**
 * @package Locale Cache
 * @version 1.0.0
 */
/*
Plugin Name: Locale cache enabler/disabler
Plugin URI: http://itma.pl/
Description: Locale cache tester. Only for development purposes.
Author: Andrzej Bernat
Version: 1.0.0
Author URI: http://itma.pl/
*/


add_filter( 'load_textdomain_from_cache', function() {
    return false;
}, 10 );