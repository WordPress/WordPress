<?php
/**
 * Redirects to the RDF feed
 * This file is deprecated and only exists for backwards compatibility
 *
 * @package WordPress
 */

require( './wp-load.php' );
wp_redirect( get_bloginfo( 'rdf_url' ), 301 );

?>