<?php
/**
 * The "View revisions" action, for the post types supporting `revisions`.
 * Only the lists of posts show it. Its JavaScript parts (`isEligible`,
 * `callback`, and the `label` with the number of revisions, which overrides
 * this one) are in `action.tsx`, next to this file.
 *
 * @package WordPress
 */

return array(
	'label'   => __( 'View revisions' ),
	'context' => 'list',
);
