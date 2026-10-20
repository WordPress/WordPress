<?php
/**
 * The "Order…" action, for the post types supporting `page-attributes`. Its
 * JavaScript parts (`isEligible` and `RenderModal`) are in `action.tsx`,
 * next to this file.
 *
 * @package WordPress
 */

return array(
	'label'             => __( 'Order…' ),
	'modalHeader'       => __( 'Order' ),
	'modalFocusOnMount' => 'firstContentElement',
);
