<?php
/**
 * The `root_site` collection: the fields of the site, the `site` entity of
 * the `root` kind, shown in the identity screen of the site editor.
 *
 * The site is not a post type, so it gets none of the default fields and
 * has nothing to exclude: these are all of its fields.
 *
 * @package WordPress
 */

return array(
	'origin' => 'core',
	'kind'   => 'root',
	'name'   => 'site',
	'module' => '@wordpress/core-fields/root_site',
);
