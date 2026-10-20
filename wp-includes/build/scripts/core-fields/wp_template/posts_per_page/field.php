<?php
/**
 * The default number of posts per page. The editor summary form maps this
 * field to the `root/site` entity.
 *
 * @package WordPress
 */

return array(
	'type'          => 'integer',
	'label'         => __( 'Posts per page' ),
	'description'   => __( 'Set the default number of posts to display on blog pages, including categories and tags. Some templates may override this setting.' ),
	'isValid'       => array( 'min' => 1 ),
	'enableSorting' => false,
	'enableHiding'  => false,
	'filterBy'      => false,
);
