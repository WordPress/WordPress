<?php
/**
 * The "Permanently delete…" action, for every post type: it applies to the
 * posts in the trash, and to the posts that cannot be moved to the trash.
 * Its JavaScript parts (`icon`, `isEligible`, and `RenderModal`) are in
 * `action.tsx`, next to this file.
 *
 * @package WordPress
 */

return array(
	'label'             => __( 'Permanently delete…' ),
	'supportsBulk'      => true,
	'hideModalHeader'   => true,
	'modalFocusOnMount' => 'firstContentElement',
);
