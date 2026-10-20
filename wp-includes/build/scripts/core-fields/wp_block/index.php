<?php
/**
 * The `wp_block` collection: the fields patterns have instead of the defaults.
 *
 * Patterns exclude the default excerpt and title fields; this collection
 * registers their description, title, and sync status instead.
 *
 * @package WordPress
 */

return array(
	'origin' => 'core',
	'kind'   => 'postType',
	'name'   => 'wp_block',
	'module' => '@wordpress/core-fields/wp_block',
);
