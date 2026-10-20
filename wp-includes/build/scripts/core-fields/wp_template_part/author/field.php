<?php
/**
 * The serializable part of the author field of template parts, which shows
 * the theme, plugin, site, or user that provides a template part rather than
 * its post author, like the author field of templates. Its JavaScript parts
 * (`getValue`, `render`, and `getElements`) are in `field.tsx`, next to this
 * file.
 *
 * It has no `type`: its value is the `author_text` of the template part, not
 * the integer `author` of the post.
 *
 * @package WordPress
 */

return array(
	'label'         => __( 'Author' ),
	'enableSorting' => false,
	'filterBy'      => array(
		'isPrimary' => true,
	),
);
