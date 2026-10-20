<?php
/**
 * The caption of the media, a copy of the `caption` field of
 * `@wordpress/media-fields`. Its JavaScript parts (`getValue` and `render`)
 * are in `field.tsx`, next to this file.
 *
 * @package WordPress
 */

return array(
	'type'          => 'text',
	'label'         => __( 'Caption' ),
	'Edit'          => array(
		'control' => 'textarea',
		'rows'    => 2,
	),
	'enableSorting' => false,
	'filterBy'      => false,
);
