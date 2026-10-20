<?php
/**
 * The content information field of the post types supporting the editor:
 * the word count, the reading time, and when the post was last edited. Its
 * JavaScript part (`render`) is in `field.tsx`, next to this file.
 *
 * Its id is not the name of the folder, since folder names are written in
 * snake case, hence the explicit `id`.
 *
 * @package WordPress
 */

return array(
	'id'            => 'post-content-info',
	'type'          => 'text',
	'label'         => __( 'Post content information' ),
	'readOnly'      => true,
	'enableSorting' => false,
	'enableHiding'  => false,
	'filterBy'      => false,
);
