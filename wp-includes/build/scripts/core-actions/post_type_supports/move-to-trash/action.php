<?php
/**
 * The "Trash" action, for every post type: it applies to the posts the user
 * can move to the trash. Its JavaScript parts (`icon`, `isEligible`, and
 * `RenderModal`) are in `action.tsx`, next to this file.
 *
 * @package WordPress
 */

return array(
	'label'             => _x( 'Trash', 'verb' ),
	'isPrimary'         => true,
	'supportsBulk'      => true,
	'hideModalHeader'   => true,
	'modalFocusOnMount' => 'firstContentElement',
);
