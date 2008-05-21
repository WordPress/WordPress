<?php /* RDF 1.0 generator, original version by garym@teledyn.com */

if (empty($wp)) {
	require_once('./wp-load.php');
	wp('feed=rdf');
}

require (ABSPATH . WPINC . '/feed-rdf.php');

?>