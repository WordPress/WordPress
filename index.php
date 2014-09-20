<?php
/**
 * Front to the WurstPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WurstPress to load the theme.
 *
 * @package WurstPress
 */

/**
 * Tells WurstPress to load the WurstPress theme and output it.
 *
 * @var bool
 */
define('WP_USE_THEMES', true);

/** Loads the WurstPress Environment and Template */
require( dirname( __FILE__ ) . '/wp-blog-header.php' );
