<?php
/**
 * The description of a template, editable for the custom templates users
 * create. Its JavaScript parts (`getValue`, `render`, and `isVisible`) are in
 * `field.tsx`, next to this file.
 *
 * @package WordPress
 */

return array(
	'type'               => 'text',
	'label'              => __( 'Description' ),
	'placeholder'        => __( 'Add a description' ),
	'Edit'               => array(
		'control' => 'textarea',
		'rows'    => 4,
	),
	'enableSorting'      => false,
	'filterBy'           => false,
	'enableGlobalSearch' => true,
);
