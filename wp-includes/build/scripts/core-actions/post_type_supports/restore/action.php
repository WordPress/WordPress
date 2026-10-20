<?php
/**
 * The "Restore" action, for every post type: it applies to the posts in the
 * trash. Its JavaScript parts (`icon`, `isEligible`, and `callback`) are in
 * `action.tsx`, next to this file.
 *
 * @package WordPress
 */

return array(
	'label'        => __( 'Restore' ),
	'isPrimary'    => true,
	'supportsBulk' => true,
);
