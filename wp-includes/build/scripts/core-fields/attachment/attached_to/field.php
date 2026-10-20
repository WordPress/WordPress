<?php
/**
 * The post the media is attached to, a copy of the `attached_to` field of
 * `@wordpress/media-fields`. Its JavaScript parts (`Edit` and `render`) are
 * in `field.tsx`, next to this file.
 *
 * @package WordPress
 */

return array(
	'type'          => 'text',
	'label'         => __( 'Attached to' ),
	'enableSorting' => false,
	'filterBy'      => false,
);
