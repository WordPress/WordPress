<?php
/**
 * The description of the media, a copy of the `description` field of
 * `@wordpress/media-fields`. Its JavaScript parts (`getValue` and `render`)
 * are in `field.tsx`, next to this file.
 *
 * @package WordPress
 */

return array(
	'type'          => 'text',
	'label'         => __( 'Description' ),
	'Edit'          => array(
		'control' => 'textarea',
		'rows'    => 5,
	),
	'enableSorting' => false,
	'filterBy'      => false,
);
