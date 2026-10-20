<?php
/**
 * The date a post was last edited, for every post type. Its JavaScript parts
 * (`getValue`, `isVisible`, and `render`) are in `field.tsx`, next to this
 * file.
 *
 * @package WordPress
 */

return array(
	'type'          => 'datetime',
	'label'         => __( 'Last edited' ),
	'readOnly'      => true,
	'enableHiding'  => false,
	'enableSorting' => false,
	'filterBy'      => false,
);
