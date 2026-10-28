<?php
/**
 * The "Reset" action, for every post type: it applies to the templates and
 * template parts the user customized. Its JavaScript parts (`icon`,
 * `isEligible`, and `RenderModal`) are in `action.tsx`, next to this file.
 *
 * @package WordPress
 */

return array(
	'label'             => __( 'Reset' ),
	'supportsBulk'      => true,
	'hideModalHeader'   => true,
	'modalFocusOnMount' => 'firstContentElement',
);
