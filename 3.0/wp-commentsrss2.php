<?php
/**
 * Redirects to the Comments RSS2 feed
 * This file is deprecated and only exists for backwards compatibility
 *
 * @package WordPress
 */

require( './wp-load.php' );
wp_redirect( get_bloginfo( 'comments_rss2_url' ), 301 );

?>