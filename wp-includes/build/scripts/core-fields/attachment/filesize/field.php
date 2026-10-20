<?php
/**
 * The file size of the media, a copy of the `filesize` field of
 * `@wordpress/media-fields`. Its JavaScript parts (`getValue` and
 * `isVisible`) are in `field.tsx`, next to this file.
 *
 * @package WordPress
 */

return array(
	'type'          => 'text',
	'label'         => __( 'File size' ),
	'enableSorting' => false,
	'filterBy'      => false,
	'readOnly'      => true,
);
