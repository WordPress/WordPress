<?php
/**
 * The title of the media. Its JavaScript parts (`getValue` and `render`) are in `field.tsx`, next to
 * this file.
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
