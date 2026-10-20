<?php
/**
 * The "Set as homepage…" action, for pages: it applies to the pages with the
 * `action-set-homepage` REST link, the published pages that are neither the
 * homepage nor the posts page. Its JavaScript parts (`isEligible` and
 * `RenderModal`) are in `action.tsx`, next to this file.
 *
 * @package WordPress
 */

return array(
	'label'             => __( 'Set as homepage…' ),
	'modalHeader'       => __( 'Set as homepage' ),
	'modalFocusOnMount' => 'firstContentElement',
);
