<?php
/**
 * The "Set as posts page" action, for pages: it applies to the pages with
 * the `action-set-posts-page` REST link, the published pages that are neither
 * the homepage nor the posts page. Its JavaScript parts (`isEligible` and
 * `RenderModal`) are in `action.tsx`, next to this file.
 *
 * @package WordPress
 */

return array(
	'label'             => __( 'Set as posts page' ),
	'modalHeader'       => __( 'Set as posts page' ),
	'modalFocusOnMount' => 'firstContentElement',
);
