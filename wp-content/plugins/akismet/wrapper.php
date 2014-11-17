<?php

global $wpcom_api_key, $akismet_api_host, $akismet_api_port;

$wpcom_api_key    = defined( 'WPCOM_API_KEY' ) ? constant( 'WPCOM_API_KEY' ) : '';
$akismet_api_host = Akismet::get_api_key() . '.rest.akismet.com';
$akismet_api_port = 80;

function akismet_test_mode() {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet::is_test_mode()' );

	return Akismet::is_test_mode();
}

function akismet_http_post( $request, $host, $path, $port = 80, $ip = null ) {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet::http_post()' );

	$path = str_replace( '/1.1/', '', $path );

	return Akismet::http_post( $request, $path, $ip ); 
}

function akismet_microtime() {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet::_get_microtime()' );

	return Akismet::_get_microtime();
}

function akismet_delete_old() {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet::delete_old_comments()' );

	return Akismet::delete_old_comments();
}

function akismet_delete_old_metadata() { 
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet::delete_old_comments_meta()' );

	return Akismet::delete_old_comments_meta();
}

function akismet_check_db_comment( $id, $recheck_reason = 'recheck_queue' ) {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet::check_db_comment()' );
   
   	return Akismet::check_db_comment( $id, $recheck_reason );
}

function akismet_rightnow() {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet_Admin::rightnow_stats()' );

	if ( !class_exists( 'Akismet_Admin' ) )
		return false;
   
   	return Akismet_Admin::rightnow_stats();
}

function akismet_admin_init() {
	_deprecated_function( __FUNCTION__, '3.0' );
}
function akismet_version_warning() {
	_deprecated_function( __FUNCTION__, '3.0' );
}
function akismet_load_js_and_css() {
	_deprecated_function( __FUNCTION__, '3.0' );
}
function akismet_nonce_field( $action = -1 ) {
	_deprecated_function( __FUNCTION__, '3.0', 'wp_nonce_field' );

	return wp_nonce_field( $action );
}
function akismet_plugin_action_links( $links, $file ) {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet_Admin::plugin_action_links()' );

	return Akismet_Admin::plugin_action_links( $links, $file );
}
function akismet_conf() {
	_deprecated_function( __FUNCTION__, '3.0' );
}
function akismet_stats_display() {
	_deprecated_function( __FUNCTION__, '3.0' );
}
function akismet_stats() {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet_Admin::dashboard_stats()' );

	return Akismet_Admin::dashboard_stats();
}
function akismet_admin_warnings() {
	_deprecated_function( __FUNCTION__, '3.0' );
}
function akismet_comment_row_action( $a, $comment ) {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet_Admin::comment_row_action()' );

	return Akismet_Admin::comment_row_actions( $a, $comment );
}
function akismet_comment_status_meta_box( $comment ) {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet_Admin::comment_status_meta_box()' );

	return Akismet_Admin::comment_status_meta_box( $comment );
}
function akismet_comments_columns( $columns ) {
	_deprecated_function( __FUNCTION__, '3.0' );

	return $columns;
}
function akismet_comment_column_row( $column, $comment_id ) {
	_deprecated_function( __FUNCTION__, '3.0' );
}
function akismet_text_add_link_callback( $m ) {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet_Admin::text_add_link_callback()' );

	return Akismet_Admin::text_add_link_callback( $m );
}
function akismet_text_add_link_class( $comment_text ) {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet_Admin::text_add_link_class()' );

	return Akismet_Admin::text_add_link_class( $comment_text );
}
function akismet_check_for_spam_button( $comment_status ) {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet_Admin::check_for_spam_button()' );

	return Akismet_Admin::check_for_spam_button( $comment_status );
}
function akismet_submit_nonspam_comment( $comment_id ) {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet::submit_nonspam_comment()' );

	return Akismet::submit_nonspam_comment( $comment_id );
}
function akismet_submit_spam_comment( $comment_id ) {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet::submit_spam_comment()' );

	return Akismet::submit_spam_comment( $comment_id );
}
function akismet_transition_comment_status( $new_status, $old_status, $comment ) {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet::transition_comment_status()' );

	return Akismet::transition_comment_status( $new_status, $old_status, $comment );
}
function akismet_spam_count( $type = false ) {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet_Admin::get_spam_count()' );

	return Akismet_Admin::get_spam_count( $type );
}
function akismet_recheck_queue() {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet_Admin::recheck_queue()' );

	return Akismet_Admin::recheck_queue();
}
function akismet_remove_comment_author_url() {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet_Admin::remove_comment_author_url()' );

	return Akismet_Admin::remove_comment_author_url();
}
function akismet_add_comment_author_url() {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet_Admin::add_comment_author_url()' );

	return Akismet_Admin::add_comment_author_url();
}
function akismet_check_server_connectivity() {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet_Admin::check_server_connectivity()' );

	return Akismet_Admin::check_server_connectivity();
}
function akismet_get_server_connectivity( $cache_timeout = 86400 ) {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet_Admin::()' );

	return Akismet_Admin::get_server_connectivity( $cache_timeout );
}
function akismet_server_connectivity_ok() {
	_deprecated_function( __FUNCTION__, '3.0' );

	return true;
}
function akismet_admin_menu() {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet_Admin::admin_menu()' );

	return Akismet_Admin::admin_menu();
}
function akismet_load_menu() {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet_Admin::load_menu()' );

	return Akismet_Admin::load_menu();
}
function akismet_init() {
	_deprecated_function( __FUNCTION__, '3.0' );
}
function akismet_get_key() {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet::get_api_key()' );

	return Akismet::get_api_key();
}
function akismet_check_key_status( $key, $ip = null ) {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet::check_key_status()' );

	return Akismet::check_key_status( $key, $ip );
}
function akismet_update_alert( $response ) {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet::update_alert()' );

	return Akismet::update_alert( $response );
}
function akismet_verify_key( $key, $ip = null ) {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet::verify_key()' );

	return Akismet::verify_key( $key, $ip );
}
function akismet_get_user_roles( $user_id ) {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet::get_user_roles()' );

	return Akismet::get_user_roles( $user_id );
}
function akismet_result_spam( $approved ) {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet::comment_is_spam()' );

	return Akismet::comment_is_spam( $approved );
}
function akismet_result_hold( $approved ) {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet::comment_needs_moderation()' );

	return Akismet::comment_needs_moderation( $approved );
}
function akismet_get_user_comments_approved( $user_id, $comment_author_email, $comment_author, $comment_author_url ) {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet::get_user_comments_approved()' );

	return Akismet::get_user_comments_approved( $user_id, $comment_author_email, $comment_author, $comment_author_url );
}
function akismet_update_comment_history( $comment_id, $message, $event = null ) {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet::update_comment_history()' );

	return Akismet::update_comment_history( $comment_id, $message, $event );
}
function akismet_get_comment_history( $comment_id ) {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet::get_comment_history()' );

	return Akismet::get_comment_history( $comment_id );
}
function akismet_cmp_time( $a, $b ) {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet::_cmp_time()' );

	return Akismet::_cmp_time( $a, $b );
}
function akismet_auto_check_update_meta( $id, $comment ) {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet::auto_check_update_meta()' );

	return Akismet::auto_check_update_meta( $id, $comment );
}
function akismet_auto_check_comment( $commentdata ) {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet::auto_check_comment()' );

	return Akismet::auto_check_comment( $commentdata );
}
function akismet_get_ip_address() {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet::get_ip_address()' );

	return Akismet::get_ip_address();
}
function akismet_cron_recheck() {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet::cron_recheck()' );

	return Akismet::cron_recheck();
}
function akismet_add_comment_nonce() {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet::add_comment_nonce()' );

	return Akismet::add_comment_nonce( $post_id );
}
function akismet_fix_scheduled_recheck() {
	_deprecated_function( __FUNCTION__, '3.0', 'Akismet::fix_scheduled_recheck()' );

	return Akismet::fix_scheduled_recheck();
}
function akismet_spam_comments() {
	_deprecated_function( __FUNCTION__, '3.0' );

	return array();
}
function akismet_spam_totals() {
	_deprecated_function( __FUNCTION__, '3.0' );

	return array();
}
function akismet_manage_page() {
	_deprecated_function( __FUNCTION__, '3.0' );
}
function akismet_caught() {
	_deprecated_function( __FUNCTION__, '3.0' );
}
function redirect_old_akismet_urls() {
	_deprecated_function( __FUNCTION__, '3.0' );
}
function akismet_kill_proxy_check( $option ) {
	_deprecated_function( __FUNCTION__, '3.0' );

	return 0;
}