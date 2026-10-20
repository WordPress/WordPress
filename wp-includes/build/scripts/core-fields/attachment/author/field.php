<?php
/**
 * The author of the media, a copy of the `author` field of
 * `@wordpress/media-fields`: read-only, unlike the default author field of
 * the post types supporting authors. Its JavaScript parts (`getElements` and
 * `render`) are in `field.tsx`, next to this file.
 *
 * @package WordPress
 */

return array(
	'type'     => 'integer',
	'label'    => __( 'Author' ),
	'filterBy' => array(
		'operators' => array( 'isAny', 'isNone' ),
	),
	'readOnly' => true,
);
