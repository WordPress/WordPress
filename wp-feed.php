<?php
/**
 * Redirects to the default feed
 * This file is deprecated and only exists for backwards compatibility
 *
 * @package WordPress
 */

require( './wp-load.php' );
wp_redirect( get_bloginfo( get_default_feed() . '_url' ), 301 );

?>