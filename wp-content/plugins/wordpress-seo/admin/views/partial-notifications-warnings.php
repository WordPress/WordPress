<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 *
 * @uses    array $notifications_data
 */

$yoast_seo_type     = 'warnings';
$yoast_seo_dashicon = 'flag';

$yoast_seo_active    = $notifications_data['warnings']['active'];
$yoast_seo_dismissed = $notifications_data['warnings']['dismissed'];

$yoast_seo_active_total    = count( $notifications_data['warnings']['active'] );
$yoast_seo_dismissed_total = count( $notifications_data['warnings']['dismissed'] );
$yoast_seo_total           = $notifications_data['metrics']['warnings'];

$yoast_seo_i18n_title              = __( 'Notifications', 'wordpress-seo' );
$yoast_seo_i18n_issues             = '';
$yoast_seo_i18n_no_issues          = __( 'No new notifications.', 'wordpress-seo' );
$yoast_seo_i18n_muted_issues_title = sprintf(
	/* translators: %d expands the amount of hidden notifications. */
	_n( 'You have %d hidden notification:', 'You have %d hidden notifications:', $yoast_seo_dismissed_total, 'wordpress-seo' ),
	$yoast_seo_dismissed_total
);

require WPSEO_PATH . 'admin/views/partial-notifications-template.php';
