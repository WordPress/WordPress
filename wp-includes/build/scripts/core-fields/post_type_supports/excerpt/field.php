<?php
/**
 * The excerpt field of the post types supporting excerpts. Its JavaScript
 * parts (`description` and `render`) are in `field.tsx`, next to this file.
 *
 * @package WordPress
 */

return array(
	'type'          => 'text',
	'label'         => __( 'Excerpt' ),
	'placeholder'   => __( 'Add an excerpt' ),
	'Edit'          => array(
		'control' => 'textarea',
		'rows'    => 4,
	),
	'enableSorting' => false,
	'filterBy'      => false,
);
