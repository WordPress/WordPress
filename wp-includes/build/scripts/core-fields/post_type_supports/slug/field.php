<?php
/**
 * The slug of a post, for the viewable post types but the design ones. Its
 * JavaScript parts (`Edit`, `render`, and `isVisible`) are in `field.tsx`,
 * next to this file.
 *
 * There is no post type support flag for permalinks, and being viewable
 * alone is not the full condition (the post type must also be public), so
 * the field also checks each post, see `isVisible`.
 *
 * @package WordPress
 */

return array(
	'type'     => 'text',
	'label'    => __( 'Slug' ),
	'filterBy' => false,
);
