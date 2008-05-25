<?php
/**
 * Outputs the Atom feed XML format using the feed-atom.php file in wp-includes
 * folder. This file only sets the feed format and includes the feed-atom.php.
 *
 * This file is no longer used in WordPress and while it is not deprecated now.
 * This file will most likely be deprecated or removed in a later version.
 *
 * The link for the atom feed is /index.php?feed=atom with permalinks off.
 *
 * @package WordPress
 */

if (empty($wp)) {
	require_once('./wp-load.php');
	wp('feed=atom');
}

require (ABSPATH . WPINC . '/feed-atom.php');

?>