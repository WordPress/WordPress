<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Backup
 *
 * Store / restore options backup
 *
 * @var $name string Field name
 * @var $id string Field ID
 * @var $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 *
 * @var $value string Current value
 */

$theme = wp_get_theme();
if ( is_child_theme() ) {
	$theme = wp_get_theme( $theme->get( 'Template' ) );
}
$theme_name = $theme->get( 'Name' );
$backup = get_option( 'usof_backup_' . $theme_name );
unset( $theme, $theme_name );

$output = '<div class="usof-backup">';
$output .= '<div class="usof-backup-status">';
if ( $backup AND is_array( $backup ) AND isset( $backup['time'] ) ) {
	$backup_time = strtotime( $backup['time'] ) + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
	$output .= __( 'Last Backup', 'us' ) . ': <span>' . date_i18n( 'F j, Y, g:i a', $backup_time ) . '</span>';
} else {
	$output .= __( 'No backups yet', 'us' );
}
$output .= '</div>';
$output .= '<div class="usof-button type_backup"><span>' . __( 'Backup Options', 'us' ) . '</span></div>';
$output .= '<div class="usof-button type_restore"';
if ( ! $backup OR ! is_array( $backup ) OR ! isset( $backup['usof_options'] ) ) {
	$output .= ' style="display: none"';
}
$output .= '><span class="usof-button-label">' . __( 'Restore Options', 'us' ) . '</span></div>';
$i18n = array(
	'restore_confirm' => __( 'Are you sure want to restore options from the backup?', 'us' ),
);
$output .= '<div class="usof-backup-i18n"' . us_pass_data_to_js( $i18n ) . '></div>';
$output .= '</div>';

echo $output;
