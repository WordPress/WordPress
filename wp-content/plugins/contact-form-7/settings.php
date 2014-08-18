<?php

require_once WPCF7_PLUGIN_DIR . '/includes/functions.php';
require_once WPCF7_PLUGIN_DIR . '/includes/formatting.php';
require_once WPCF7_PLUGIN_DIR . '/includes/pipe.php';
require_once WPCF7_PLUGIN_DIR . '/includes/shortcodes.php';
require_once WPCF7_PLUGIN_DIR . '/includes/capabilities.php';
require_once WPCF7_PLUGIN_DIR . '/includes/contact-form.php';
require_once WPCF7_PLUGIN_DIR . '/includes/mail.php';
require_once WPCF7_PLUGIN_DIR . '/includes/submission.php';
require_once WPCF7_PLUGIN_DIR . '/includes/upgrade.php';

if ( is_admin() )
	require_once WPCF7_PLUGIN_DIR . '/admin/admin.php';
else
	require_once WPCF7_PLUGIN_DIR . '/includes/controller.php';

add_action( 'plugins_loaded', 'wpcf7' );

function wpcf7() {
	wpcf7_load_textdomain();
	wpcf7_load_modules();
}

add_action( 'init', 'wpcf7_init' );

function wpcf7_init() {
	wpcf7_get_request_uri();
	wpcf7_register_post_types();

	do_action( 'wpcf7_init' );
}

add_action( 'admin_init', 'wpcf7_upgrade' );

function wpcf7_upgrade() {
	$opt = get_option( 'wpcf7' );

	if ( ! is_array( $opt ) )
		$opt = array();

	$old_ver = isset( $opt['version'] ) ? (string) $opt['version'] : '0';
	$new_ver = WPCF7_VERSION;

	if ( $old_ver == $new_ver )
		return;

	do_action( 'wpcf7_upgrade', $new_ver, $old_ver );

	$opt['version'] = $new_ver;

	update_option( 'wpcf7', $opt );
}

/* Install and default settings */

add_action( 'activate_' . WPCF7_PLUGIN_BASENAME, 'wpcf7_install' );

function wpcf7_install() {
	if ( $opt = get_option( 'wpcf7' ) )
		return;

	wpcf7_load_textdomain();
	wpcf7_register_post_types();
	wpcf7_upgrade();

	if ( get_posts( array( 'post_type' => 'wpcf7_contact_form' ) ) )
		return;

	$contact_form = WPCF7_ContactForm::get_template( array(
		'title' => sprintf( __( 'Contact form %d', 'contact-form-7' ), 1 ) ) );

	$contact_form->save();
}

?>