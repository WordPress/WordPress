<?php
/**
 * The notes field of the post types whose `editor` support has the `notes`
 * argument. It is plain data: it has no JavaScript parts.
 *
 * Its id is the name of the property of the post it reads, not the name of
 * the folder, hence the explicit `id`.
 *
 * @package WordPress
 */

return array(
	'id'            => 'notesCount',
	'type'          => 'integer',
	'label'         => __( 'Notes' ),
	'enableSorting' => false,
	'filterBy'      => false,
);
