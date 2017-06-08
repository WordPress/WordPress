<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$us_layout = US_Layout::instance();

if ( $us_layout->header_show == 'never' ) {
	return;
}

global $us_header_settings;
us_load_header_settings_once();

$options = us_arr_path( $us_header_settings, 'default.options', array() );
$layout = us_arr_path( $us_header_settings, 'default.layout', array() );
$data = us_arr_path( $us_header_settings, 'data', array() );

echo '<header class="l-header ' . $us_layout->header_classes();
if ( isset( $options['bg_img'] ) AND ! empty( $options['bg_img'] ) ) {
	echo ' with_bgimg';
}
echo '" itemscope="itemscope" itemtype="https://schema.org/WPHeader">';
foreach ( array( 'top', 'middle', 'bottom' ) as $valign ) {
	echo '<div class="l-subheader at_' . $valign;
	if ( isset( $options[ $valign . '_fullwidth' ] ) AND $options[ $valign . '_fullwidth' ] ) {
		echo ' width_full';
	}
	echo '"><div class="l-subheader-h">';
	foreach ( array( 'left', 'center', 'right' ) as $halign ) {
		echo '<div class="l-subheader-cell at_' . $halign . '">';
		if ( isset( $layout[ $valign . '_' . $halign ] ) ) {
			us_output_header_elms( $layout, $data, $valign . '_' . $halign );
		}
		echo '</div>';
	}
	echo '</div></div>';
}

// Outputting elements that are hidden in default state but are visible in tablets / mobiles state
$default_elms = us_get_header_shown_elements_list( us_get_header_layout() );
$tablets_elms = us_get_header_shown_elements_list( us_get_header_layout( 'tablets' ) );
$mobiles_elms = us_get_header_shown_elements_list( us_get_header_layout( 'mobiles' ) );
$layout['temporarily_hidden'] = array_diff( array_unique( array_merge( $tablets_elms, $mobiles_elms ) ), $default_elms );
echo '<div class="l-subheader for_hidden hidden">';
us_output_header_elms( $layout, $data, 'temporarily_hidden' );
echo '</div>';

echo '</header>';
