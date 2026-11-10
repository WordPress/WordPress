<?php
/**
 * The publish date of a post, as a form summarizes it, for every post type
 * but the design ones. It reads the date of the post, so its JavaScript parts
 * (`getValue` and `render`) are in `field.tsx`, next to this file.
 *
 * It is only meant as the summary of the `date` field in a form panel, so
 * lists can't show, sort, or filter by it: they keep the `date` field, whose
 * value is never "Immediately".
 *
 * @package WordPress
 */

return array(
	'type'          => 'datetime',
	'label'         => __( 'Publish date' ),
	'readOnly'      => true,
	'enableHiding'  => false,
	'enableSorting' => false,
	'filterBy'      => false,
);
