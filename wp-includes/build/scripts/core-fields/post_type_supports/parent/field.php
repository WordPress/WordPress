<?php
/**
 * The parent of a post, for the post types supporting `page-attributes`. Its
 * JavaScript parts (`Edit` and `render`) are in `field.tsx`, next to this
 * file.
 *
 * @package WordPress
 */

return array(
	'type'          => 'text',
	'label'         => __( 'Parent' ),
	'enableSorting' => true,
	'filterBy'      => false,
);
