<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

if ( ! defined( 'WPSEO_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

$yform = Yoast_Form::get_instance();
$yform->admin_header( true, 'wpseo_ms' );

$network_tabs = new WPSEO_Option_Tabs( 'network' );
$network_tabs->add_tab( new WPSEO_Option_Tab( 'general', __( 'General', 'wordpress-seo' ) ) );
$network_tabs->add_tab( new WPSEO_Option_Tab( 'features', __( 'Features', 'wordpress-seo' ) ) );
$network_tabs->add_tab( new WPSEO_Option_Tab( 'integrations', __( 'Integrations', 'wordpress-seo' ) ) );

$network_tabs->add_tab(
	new WPSEO_Option_Tab(
		'crawl-settings',
		__( 'Crawl settings', 'wordpress-seo' ),
		[
			'save_button' => true,
		]
	)
);
$network_tabs->add_tab( new WPSEO_Option_Tab( 'restore-site', __( 'Restore Site', 'wordpress-seo' ), [ 'save_button' => false ] ) );
$network_tabs->display( $yform );

$yform->admin_footer();
