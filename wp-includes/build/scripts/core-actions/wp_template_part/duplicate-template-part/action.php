<?php
/**
 * The "Duplicate…" action of template parts, when the current user can
 * create them and the theme is a block theme. Its JavaScript parts
 * (`isEligible` and `RenderModal`) are in `action.tsx`, next to this file.
 *
 * @package WordPress
 */

return array(
	'label'             => _x( 'Duplicate…', 'action label' ),
	'modalHeader'       => _x( 'Duplicate template part', 'action label' ),
	'modalFocusOnMount' => 'firstContentElement',
);
