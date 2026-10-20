<?php
/**
 * The featured image of a post, for the post types supporting `thumbnail`
 * when the theme supports post thumbnails for them. Its JavaScript parts
 * (`Edit`, `render`, and `setValue`) are in `field.tsx`, next to this file.
 *
 * @package WordPress
 */

return array(
	'type'          => 'media',
	'label'         => __( 'Featured Image' ),
	'placeholder'   => __( 'Set featured image' ),
	'enableSorting' => false,
	'filterBy'      => false,
);
