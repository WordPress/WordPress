<?php
/**
 * The file name of the media, a copy of the `filename` field of
 * `@wordpress/media-fields`. Its JavaScript parts (`getValue` and `render`)
 * are in `field.tsx`, next to this file.
 *
 * @package WordPress
 */

return array(
	'type'          => 'text',
	'label'         => __( 'File name' ),
	'enableSorting' => false,
	'filterBy'      => false,
	'readOnly'      => true,
);
