<?php
/**
 * Redirects to the RSS feed
 * This file is deprecated and only exists for backwards compatibility
 *
 * @package WordPress
 */

require( './wp-load.php' );
wp_redirect( get_bloginfo( 'rss_url' ), 301 );

?>