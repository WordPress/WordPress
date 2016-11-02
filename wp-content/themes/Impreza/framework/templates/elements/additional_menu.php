<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output additional menu element
 *
 * @var $source string WP Menu source
 * @var $text_size int
 * @var $indents int
 * @var $design_options array
 * @var $id string
 */
if ( empty( $source ) OR ! is_nav_menu( $source ) ) {
	return;
}

$classes = '';
if ( isset( $design_options ) AND isset( $design_options['hide_for_sticky'] ) AND $design_options['hide_for_sticky'] ) {
	$classes .= ' hide-for-sticky';
}
if ( isset( $id ) AND ! empty( $id ) ) {
	$classes .= ' ush_' . str_replace( ':', '_', $id );
}
wp_nav_menu( array(
	'container' => 'div',
	'container_class' => 'w-menu ' . $classes,
	'menu' => $source,
	'walker' => new US_Walker_Simplenav_Menu,
	'items_wrap' => '<div class="w-menu-list">%3$s</div>',
	'depth' => 1,
) );
