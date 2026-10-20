<?php
/**
 * The password of a post, for every post type but the design ones. Its
 * JavaScript parts (`Edit` and `isVisible`) are in `field.tsx`, next to this
 * file.
 *
 * @package WordPress
 */

return array(
	'type'          => 'text',
	'label'         => __( 'Password' ),
	'enableSorting' => false,
	'enableHiding'  => false,
	'filterBy'      => false,
);
