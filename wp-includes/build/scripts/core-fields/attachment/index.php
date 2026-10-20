<?php
/**
 * The `attachment` collection: the fields of the media editor.
 *
 * Attachments support authors and comments, yet the media editor shows its
 * own set of fields, copies of those of `@wordpress/media-fields`, none of
 * which is a default one. So they opt out of every default field, see
 * wp_register_core_field_collections() in `src/index.php`, and get the
 * fields of this collection instead, with its script module.
 *
 * @package WordPress
 */

return array(
	'origin' => 'core',
	'kind'   => 'postType',
	'name'   => 'attachment',
	'module' => '@wordpress/core-fields/attachment',
);
