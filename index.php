<?php
/**
 * Front to the WordPress application. Most of WordPress is loaded through this
 * file. This file doesn't do anything, but loads the file which does and tells
 * WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define('WP_USE_THEMES', true);

/** Loads the WordPress Environment and Template */
require('./wp-blog-header.php');
?>