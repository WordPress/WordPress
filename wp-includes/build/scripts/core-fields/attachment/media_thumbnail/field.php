<?php
/**
 * The thumbnail of the media, a copy of the `media_thumbnail` field of
 * `@wordpress/media-fields`. Its JavaScript part (`render`) is in
 * `field.tsx`, next to this file.
 *
 * @package WordPress
 */

return array(
	'type'          => 'media',
	'label'         => __( 'Thumbnail' ),
	'enableSorting' => false,
	'filterBy'      => false,
);
