<?php
/**
 * The status of a post, for every post type but the design ones. Its
 * JavaScript parts (`getValue`, `render`, and `isDisabled`) are in
 * `field.tsx`, next to this file; the render adds the icon of each status.
 *
 * Custom statuses are not supported, see
 * https://github.com/WordPress/gutenberg/issues/55886.
 *
 * @package WordPress
 */

return array(
	'type'          => 'text',
	'label'         => __( 'Status' ),
	'Edit'          => 'radio',
	'elements'      => array(
		array(
			'value'       => 'draft',
			'label'       => __( 'Draft' ),
			'description' => __( 'Not ready to publish.' ),
		),
		array(
			'value'       => 'future',
			'label'       => __( 'Scheduled' ),
			'description' => __( 'Publish automatically on a chosen date.' ),
		),
		array(
			'value'       => 'pending',
			'label'       => __( 'Pending Review' ),
			'description' => __( 'Waiting for review before publishing.' ),
		),
		array(
			'value'       => 'private',
			'label'       => __( 'Private' ),
			'description' => __( 'Only visible to site admins and editors.' ),
		),
		array(
			'value'       => 'publish',
			'label'       => __( 'Published' ),
			'description' => __( 'Visible to everyone.' ),
		),
		array(
			'value' => 'trash',
			'label' => __( 'Trash' ),
		),
	),
	'enableSorting' => false,
	'filterBy'      => array(
		'operators' => array( 'isAny' ),
	),
);
