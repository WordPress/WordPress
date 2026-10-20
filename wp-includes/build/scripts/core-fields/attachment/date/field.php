<?php
/**
 * The date the media was added, a copy of the `date_added` field of
 * `@wordpress/media-fields`. It is plain data: it has no JavaScript parts.
 *
 * @package WordPress
 */

return array(
	'type'     => 'datetime',
	'label'    => __( 'Date added' ),
	'filterBy' => array(
		'operators' => array( 'before', 'after' ),
	),
	'readOnly' => true,
);
