<?php
/**
 * The default comment status. The editor summary form maps this field to the
 * `root/site` entity.
 *
 * @package WordPress
 */

return array(
	'type'          => 'text',
	'label'         => __( 'Discussion' ),
	'Edit'          => 'radio',
	'elements'      => array(
		array(
			'value'       => 'open',
			'label'       => __( 'Open' ),
			'description' => __( 'Visitors can add new comments and replies.' ),
		),
		array(
			'value'       => '',
			'label'       => __( 'Closed' ),
			'description' => __( 'Visitors cannot add new comments or replies. Existing comments remain visible.' ),
		),
	),
	'enableSorting' => false,
	'enableHiding'  => false,
	'filterBy'      => false,
);
