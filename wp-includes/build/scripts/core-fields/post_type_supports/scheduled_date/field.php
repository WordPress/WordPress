<?php
/**
 * The date a scheduled post will be published, for every post type but the
 * design ones. It edits the date of the post, so its JavaScript parts
 * (`getValue`, `setValue`, and `isVisible`) are in `field.tsx`, next to this
 * file.
 *
 * @package WordPress
 */

return array(
	'type'          => 'datetime',
	'label'         => __( 'Scheduled Date' ),
	'Edit'          => array(
		'control' => 'datetime',
		'compact' => true,
	),
	'enableHiding'  => false,
	'enableSorting' => false,
	'filterBy'      => false,
);
