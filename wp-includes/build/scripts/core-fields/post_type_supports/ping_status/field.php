<?php
/**
 * The pingbacks and trackbacks field of the post types supporting
 * trackbacks. Its JavaScript parts (`description`, `getValue`, `setValue`,
 * and `render`) are in `field.tsx`, next to this file: the REST API stores
 * the setting as `open` or `closed`, and the field exposes it as a boolean.
 *
 * @package WordPress
 */

return array(
	'type'          => 'boolean',
	'label'         => __( 'Enable pingbacks & trackbacks' ),
	'header'        => __( 'Trackbacks & Pingbacks' ),
	'enableSorting' => false,
	'enableHiding'  => false,
	'filterBy'      => false,
);
