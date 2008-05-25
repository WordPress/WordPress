<?php
/**
 * Outputs the RDF feed using the feed-rdf.php
 * file in wp-includes folder.
 *
 * This file only sets the feed format and includes the
 * feed-rdf.php.
 *
 * This file is no longer used in WordPress and while it is
 * not deprecated now. This file will most likely be
 * deprecated or removed in a later version.
 *
 * @package WordPress
 */

if (empty($wp)) {
	require_once('./wp-load.php');
	wp('feed=rdf');
}

require (ABSPATH . WPINC . '/feed-rdf.php');

?>