<?php
/**
 * The `wp_template_part` collection: the fields template parts have instead
 * of the defaults.
 *
 * Template parts support authors, yet their author is the theme, plugin,
 * site, or user that provides them rather than their post author.
 * So they opt out of the default author field, see
 * wp_register_core_field_collections() in `src/index.php`, and get the
 * author field of this collection instead, with its script module. The
 * collection also defines the title field of template parts.
 *
 * @package WordPress
 */

return array(
	'origin' => 'core',
	'kind'   => 'postType',
	'name'   => 'wp_template_part',
	'module' => '@wordpress/core-fields/wp_template_part',
);
