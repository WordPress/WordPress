<?php
/**
 * The "Delete…" action, for every post type: it applies to the templates,
 * template parts, and patterns the user created. Its JavaScript parts
 * (`icon`, `isEligible`, and `RenderModal`) are in `action.tsx`, next to
 * this file.
 *
 * @package WordPress
 */

return array(
	'label'             => __( 'Delete…' ),
	'isPrimary'         => true,
	'supportsBulk'      => true,
	'hideModalHeader'   => true,
	'modalFocusOnMount' => 'firstContentElement',
);
