<?php
/**
 * The sticky field of posts, the only post type with sticky posts. Its
 * JavaScript part (`isVisible`) is in `field.tsx`, next to this file.
 *
 * @package WordPress
 */

return array(
	'type'          => 'boolean',
	'label'         => __( 'Sticky' ),
	'description'   => __( 'Pin this post to the top of the blog.' ),
	'enableSorting' => false,
	'enableHiding'  => false,
	'filterBy'      => false,
);
