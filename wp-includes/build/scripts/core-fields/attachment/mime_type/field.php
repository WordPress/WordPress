<?php
/**
 * The file type of the media, a copy of the `mime_type` field of
 * `@wordpress/media-fields`. Its JavaScript parts (`getValue` and `render`)
 * are in `field.tsx`, next to this file.
 *
 * @package WordPress
 */

return array(
	'type'          => 'text',
	'label'         => __( 'File type' ),
	// Sorting is disabled until the REST API supports ordering by
	// `mime_type`, see https://core.trac.wordpress.org/ticket/64073.
	'enableSorting' => false,
	'filterBy'      => false,
	'readOnly'      => true,
);
