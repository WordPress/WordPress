<?php
/**
 * The "Duplicate" action, for the post types the current user can create,
 * except patterns, templates, and template parts. Only the Gutenberg plugin
 * registers it. Its JavaScript parts (`isEligible` and `RenderModal`) are in
 * `action.tsx`, next to this file.
 *
 * @package WordPress
 */

return array(
	'label'             => _x( 'Duplicate', 'action label' ),
	'modalHeader'       => _x( 'Duplicate', 'action label' ),
	'modalFocusOnMount' => 'firstContentElement',
);
