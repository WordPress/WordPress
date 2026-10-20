<?php
/**
 * The serializable part of the author field of templates, which shows the
 * theme, plugin, site, or user that provides a template rather than its post
 * author. Its JavaScript parts (`getValue`, `render`, and `getElements`) are
 * in `field.tsx`, next to this file.
 *
 * It has no `type`: its value is the `author_text` of the template, not the
 * integer `author` of the post.
 *
 * @package WordPress
 */

return array(
	'label' => __( 'Author' ),
);
