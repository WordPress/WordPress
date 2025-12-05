<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Views
 *
 * @uses Yoast_Form $yform Form object.
 */

if ( ! defined( 'WPSEO_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

echo '<p>' . esc_html__( 'Using this form you can reset a site to the default SEO settings.', 'wordpress-seo' ) . '</p>';

if ( get_blog_count() <= 100 ) {
	$network_admin = new Yoast_Network_Admin();

	$yform->select(
		'site_id',
		__( 'Site ID', 'wordpress-seo' ),
		$network_admin->get_site_choices( false, true )
	);
}
else {
	$yform->textinput( 'site_id', __( 'Site ID', 'wordpress-seo' ) );
}

wp_nonce_field( 'wpseo-network-restore', 'restore_site_nonce', false );
echo '<button type="submit" name="action" value="' . esc_attr( Yoast_Network_Admin::RESTORE_SITE_ACTION ) . '" class="button button-primary">' . esc_html__( 'Restore site to defaults', 'wordpress-seo' ) . '</button>';
