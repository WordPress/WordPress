<?php
/**
 * The alternative text of the media, a copy of the `alt_text` field of
 * `@wordpress/media-fields`. Its JavaScript parts (`description`,
 * `isVisible`, and `render`) are in `field.tsx`, next to this file.
 *
 * @package WordPress
 */

return array(
	'type'          => 'text',
	'label'         => __( 'Alt text' ),
	'Edit'          => array(
		'control' => 'textarea',
		'rows'    => 2,
	),
	'enableSorting' => false,
	'filterBy'      => false,
);
