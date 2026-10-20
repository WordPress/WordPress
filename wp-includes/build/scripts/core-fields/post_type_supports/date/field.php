<?php
/**
 * The date of a post, for every post type but the design ones. Its
 * JavaScript parts (`isVisible` and `render`) are in `field.tsx`, next to
 * this file.
 *
 * @package WordPress
 */

return array(
	'type'     => 'datetime',
	'label'    => __( 'Date' ),
	'filterBy' => array(
		'operators' => array( 'before', 'after' ),
	),
);
