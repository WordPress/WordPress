<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output main menu element
 *
 * @var $hover_effect string Hover Effect: 'simple' / 'underline'
 * @var $dropdown_effect string Dropdown Effect: 'opacity' / 'height' / 'mdesign'
 * @var $vstretch boolean Stretch menu items vertically to fit the available height
 * @var $indents int Items Indents
 * @var $mobile_width int On which screen width menu becomes mobile
 * @var $mobile_behavior boolean Mobile behavior
 * @var $design_options array
 * @var $id string
 */

$menu_location = apply_filters( 'us_main_menu_location', 'us_main_menu' );
if ( ! has_nav_menu( $menu_location ) ) {
	return;
}

$classes = '';
if ( isset( $design_options ) AND isset( $design_options['hide_for_sticky'] ) AND $design_options['hide_for_sticky'] ) {
	$classes .= ' hide-for-sticky';
}
$list_classes = ' level_1 hover_' . $hover_effect;
$classes .= ' type_desktop animation_' . $dropdown_effect;
if ( $vstretch ) {
	$classes .= ' height_full';
}
if ( isset( $id ) AND ! empty( $id ) ) {
	$classes .= ' ush_' . str_replace( ':', '_', $id );
}
$list_classes .= ' hidden';

echo '<nav class="w-nav' . $classes . '" itemscope="itemscope" itemtype="https://schema.org/SiteNavigationElement">';
echo '<a class="w-nav-control" href="javascript:void(0);"></a>';
echo '<ul class="w-nav-list' . $list_classes . '">';
wp_nav_menu( array(
	'theme_location' => $menu_location,
	'container' => 'ul',
	'container_class' => 'w-nav-list',
	'walker' => new US_Walker_Nav_Menu,
	'items_wrap' => '%3$s',
	'fallback_cb' => FALSE,
) );
echo '</ul>';
echo '<div class="w-nav-options hidden"';
echo us_pass_data_to_js( array(
	'mobileWidth' => intval( $mobile_width ),
	'mobileBehavior' => intval( $mobile_behavior ),
) );
echo '></div>';
echo '</nav>';
