<?php
/**
 * The read-only description of the templates themes and plugins provide,
 * whose description cannot be edited. Its JavaScript parts (`getValue`,
 * `render`, and `isVisible`) are in `field.tsx`, next to this file.
 *
 * @package WordPress
 */

return array(
	'type'          => 'text',
	'label'         => __( 'Description' ),
	'readOnly'      => true,
	'enableSorting' => false,
	'enableHiding'  => false,
	'filterBy'      => false,
);
