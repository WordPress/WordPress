<?php
/**
 * The serializable part of the author field of the post types supporting
 * authors. Its JavaScript parts (`getElements`, `setValue`, `render`, and
 * `isVisible`) are in `field.tsx`, next to this file.
 *
 * @package WordPress
 */

return array(
	'type'     => 'integer',
	'label'    => __( 'Author' ),
	'filterBy' => array(
		'operators' => array( 'isAny', 'isNone' ),
	),
);
