<?php
/**
 * The dimensions of the media, a copy of the `media_dimensions` field of
 * `@wordpress/media-fields`. Its JavaScript parts (`getValue` and
 * `isVisible`) are in `field.tsx`, next to this file.
 *
 * @package WordPress
 */

return array(
	'type'          => 'text',
	'label'         => __( 'Dimensions' ),
	'enableSorting' => false,
	'filterBy'      => false,
	'readOnly'      => true,
);
