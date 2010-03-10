<?php
/**
 * Execute an AJAX action.
 *
 * To take full advantage of this file, call wp_ajaxurl(); in your theme
 * or plugin while registering your front-end scripts. Doing so will make
 * an ajaxurl variable available for use in javascripts. The ajaxurl
 * variable will point to this file's absolute URL.
 *
 * In the admin area, an ajaxurl variable is always available, and points
 * to wp-admin/admin-ajax.php instead.
 *
 * @since 3.0
 */
define('DOING_AJAX', true);
require_once('wp-load.php');

@header('Content-Type: text/html; charset=' . get_option('blog_charset'));
send_nosniff_header();

do_action('ajax_init');

$hook = !empty($_REQUEST['action']) ? 'ajax_' . $_REQUEST['action'] : false;

if ( empty($hook) || ! has_action($hook) ) {
	status_header(400);
	exit;
}

do_action($hook);
?>