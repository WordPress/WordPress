<?php

global $wpcom_api_key, $akismet_api_host, $akismet_api_port;

$wpcom_api_key    = defined( 'WPCOM_API_KEY' ) ? constant( 'WPCOM_API_KEY' ) : '';
$akismet_api_host = Akismet::get_api_key() . '.rest.akismet.com';
$akismet_api_port = 80;

function akismet_test_mode() {
	return Akismet::is_test_mode();
}

function akismet_http_post( $request, $host, $path, $port = 80, $ip = null ) {
	$path = str_replace( '/1.1/', '', $path );

	return Akismet::http_post( $request, $path, $ip ); 
}

function akismet_microtime() {
	return Akismet::_get_microtime();
}

function akismet_delete_old() {
	return Akismet::delete_old_comments();
}

function akismet_delete_old_metadata() { 
	return Akismet::delete_old_comments_meta();
}

function akismet_check_db_comment( $id, $recheck_reason = 'recheck_queue' ) {
   	return Akismet::check_db_comment( $id, $recheck_reason );
}

function akismet_rightnow() {
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
	return wp_nonce_field( $action );
}
function akismet_plugin_action_links( $links, $file ) {
	return Akismet_Admin::plugin_action_links( $links, $file );
}
function akismet_conf() {
	_deprecated_function( __FUNCTION__, '3.0' );
}
function akismet_stats_display() {
	_deprecated_function( __FUNCTION__, '3.0' );
}
function akismet_stats() {
	return Akismet_Admin::dashboard_stats();
}
function akismet_admin_warnings() {
	_deprecated_function( __FUNCTION__, '3.0' );
}
function akismet_comment_row_action( $a, $comment ) {
	return Akismet_Admin::comment_row_actions( $a, $comment );
}
function akismet_comment_status_meta_box( $comment ) {
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
	return Akismet_Admin::text_add_link_callback( $m );
}
function akismet_text_add_link_class( $comment_text ) {
	return Akismet_Admin::text_add_link_class( $comment_text );
}
function akismet_check_for_spam_button( $comment_status ) {
	return Akismet_Admin::check_for_spam_button( $comment_status );
}
function akismet_submit_nonspam_comment( $comment_id ) {
	return Akismet::submit_nonspam_comment( $comment_id );
}
function akismet_submit_spam_comment( $comment_id ) {
	return Akismet::submit_spam_comment( $comment_id );
}
function akismet_transition_comment_status( $new_status, $old_status, $comment ) {
	return Akismet::transition_comment_status( $new_status, $old_status, $comment );
}
function akismet_spam_count( $type = false ) {
	return Akismet_Admin::get_spam_count( $type );
}
function akismet_recheck_queue() {
	return Akismet_Admin::recheck_queue();
}
function akismet_remove_comment_author_url() {
	return Akismet_Admin::remove_comment_author_url();
}
function akismet_add_comment_author_url() {
	return Akismet_Admin::add_comment_author_url();
}
function akismet_check_server_connectivity() {
	return Akismet_Admin::check_server_connectivity();
}
function akismet_get_server_connectivity( $cache_timeout = 86400 ) {
	return Akismet_Admin::get_server_connectivity( $cache_timeout );
}
function akismet_server_connectivity_ok() {
	_deprecated_function( __FUNCTION__, '3.0' );

	return true;
}
function akismet_admin_menu() {
	return Akismet_Admin::admin_menu();
}
function akismet_load_menu() {
	return Akismet_Admin::load_menu();
}
function akismet_init() {
	_deprecated_function( __FUNCTION__, '3.0' );
}
function akismet_get_key() {
	return Akismet::get_api_key();
}
function akismet_check_key_status( $key, $ip = null ) {
	return Akismet::check_key_status( $key, $ip );
}
function akismet_update_alert( $response ) {
	return Akismet::update_alert( $response );
}
function akismet_verify_key( $key, $ip = null ) {
	return Akismet::verify_key( $key, $ip );
}
function akismet_get_user_roles( $user_id ) {
	return Akismet::get_user_roles( $user_id );
}
function akismet_result_spam( $approved ) {
	return Akismet::comment_is_spam( $approved );
}
function akismet_result_hold( $approved ) {
	return Akismet::comment_needs_moderation( $approved );
}
function akismet_get_user_comments_approved( $user_id, $comment_author_email, $comment_author, $comment_author_url ) {
	return Akismet::get_user_comments_approved( $user_id, $comment_author_email, $comment_author, $comment_author_url );
}
function akismet_update_comment_history( $comment_id, $message, $event = null ) {
	return Akismet::update_comment_history( $comment_id, $message, $event );
}
function akismet_get_comment_history( $comment_id ) {
	return Akismet::get_comment_history( $comment_id );
}
function akismet_cmp_time( $a, $b ) {
	return Akismet::_cmp_time( $a, $b );
}
function akismet_auto_check_update_meta( $id, $comment ) {
	return Akismet::auto_check_update_meta( $id, $comment );
}
function akismet_auto_check_comment( $commentdata ) {
	return Akismet::auto_check_comment( $commentdata );
}
function akismet_get_ip_address() {
	return Akismet::get_ip_address();
}
function akismet_cron_recheck() {
	return Akismet::cron_recheck();
}
function akismet_add_comment_nonce( $post_id ) {
	return Akismet::add_comment_nonce( $post_id );
}
function akismet_fix_scheduled_recheck() {
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
function akismet_pingback_forwarded_for( $r, $url ) {
	return Akismet::pingback_forwarded_for( $r, $url );
}
function akismet_pre_check_pingback( $method ) {
	return Akismet::pre_check_pingback( $method );
}