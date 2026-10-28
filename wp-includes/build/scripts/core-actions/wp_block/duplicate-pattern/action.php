<?php
/**
 * The "Duplicate" action of patterns, when the current user can create
 * them. Its JavaScript parts (`isEligible` and `RenderModal`) are in
 * `action.tsx`, next to this file.
 *
 * @package WordPress
 */

return array(
	'label'             => _x( 'Duplicate', 'action label' ),
	'modalHeader'       => _x( 'Duplicate pattern', 'action label' ),
	'modalFocusOnMount' => 'firstContentElement',
);
