<?php
/**
 * The template that renders a post, for every post type but the design
 * ones. Its JavaScript parts (`Edit` and `render`) are in `field.tsx`, next
 * to this file.
 *
 * Whether a post can be assigned a template depends on the theme and on the
 * post resolving to one, which only the client knows: both the control and
 * the view render nothing when it cannot, see `hooks.ts`.
 *
 * @package WordPress
 */

return array(
	'type'          => 'text',
	'label'         => __( 'Template' ),
	'enableSorting' => false,
	'filterBy'      => false,
);
