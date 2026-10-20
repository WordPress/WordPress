<?php
/**
 * The `page` collection: the fields pages have instead of the defaults.
 *
 * Pages support titles, yet their title shows whether the page is the
 * homepage, the posts page, or the privacy policy page. So they opt out of
 * the default title field, see wp_register_core_field_collections() in
 * `src/index.php`, and get the title field of this collection instead, with
 * its script module.
 *
 * @package WordPress
 */

return array(
	'origin' => 'core',
	'kind'   => 'postType',
	'name'   => 'page',
	'module' => '@wordpress/core-fields/page',
);
