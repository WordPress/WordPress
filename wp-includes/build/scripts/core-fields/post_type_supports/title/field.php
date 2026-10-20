<?php
/**
 * The title of a post, for the post types supporting `title`. Pages,
 * templates, template parts, and patterns exclude it, since their
 * collections define their own. Its JavaScript parts (`getValue` and
 * `render`) are in `field.tsx`, next to this file.
 *
 * @package WordPress
 */

return array(
	'type'               => 'text',
	'label'              => __( 'Title' ),
	'placeholder'        => __( 'No title' ),
	'enableHiding'       => true,
	'enableGlobalSearch' => true,
	'filterBy'           => false,
);
