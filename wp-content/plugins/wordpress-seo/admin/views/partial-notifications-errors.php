<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 *
 * @uses    array $notifications_data
 */

$yoast_seo_type     = 'errors';
$yoast_seo_dashicon = 'warning';

$yoast_seo_active    = $notifications_data['errors']['active'];
$yoast_seo_dismissed = $notifications_data['errors']['dismissed'];

$yoast_seo_active_total    = count( $yoast_seo_active );
$yoast_seo_dismissed_total = count( $yoast_seo_dismissed );
$yoast_seo_total           = $notifications_data['metrics']['errors'];

$yoast_seo_i18n_title              = __( 'Problems', 'wordpress-seo' );
$yoast_seo_i18n_issues             = __( 'We have detected the following issues that affect the SEO of your site.', 'wordpress-seo' );
$yoast_seo_i18n_no_issues          = __( 'Good job! We could detect no serious SEO problems.', 'wordpress-seo' );
$yoast_seo_i18n_muted_issues_title = sprintf(
	/* translators: %d expands the amount of hidden notifications. */
	_n( 'You have %d hidden notification:', 'You have %d hidden notifications:', $yoast_seo_dismissed_total, 'wordpress-seo' ),
	$yoast_seo_dismissed_total
);

require WPSEO_PATH . 'admin/views/partial-notifications-template.php';
