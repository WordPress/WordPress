<?php

if ( ! function_exists( 'et_core_api_email_init' ) ):
function et_core_api_email_init() {}
endif;


if ( ! function_exists( 'et_core_api_email_fetch_lists' ) ):
/**
 * Fetch the latest email lists for a provider account and update the database accordingly.
 *
 * @param string $name_or_slug The provider name or slug.
 * @param string $account      The account name.
 * @param string $api_key      Optional. The api key (if fetch succeeds, the key will be saved).
 *
 * @return string 'success' if successful, an error message otherwise.
 */
function et_core_api_email_fetch_lists( $name_or_slug, $account, $api_key = '' ) {
	if ( ! empty( $api_key ) ) {
		// The account provided either doesn't exist yet or has a new api key.
		et_core_security_check( 'manage_options' );
	}

	if ( empty( $name_or_slug ) || empty( $account ) ) {
		return esc_html__( 'ERROR: Invalid arguments.', 'et_core' );
	}

	$providers = et_core_api_email_providers();
	$provider  = $providers->get( $name_or_slug, $account );

	if ( ! $provider ) {
		return '';
	}

	if ( '' !== $api_key ) {
		$provider->data['api_key'] = sanitize_text_field( $api_key );
	}

	return $provider->fetch_subscriber_lists();
}
endif;


if ( ! function_exists( 'et_core_api_email_providers' ) ):
function et_core_api_email_providers() {
	static $providers = null;

	if ( null === $providers ) {
		$providers = new ET_Core_API_Email_Providers();
	}

	return $providers;
}
endif;


if ( ! function_exists( 'et_core_api_email_remove_account' ) ):
/**
 * Delete an existing provider account.
 *
 * @param string $name_or_slug The provider name or slug.
 * @param string $account      The account name.
 */
function et_core_api_email_remove_account( $name_or_slug, $account ) {
	et_core_security_check( 'manage_options' );

	if ( empty( $name_or_slug ) || empty( $account ) ) {
		return;
	}

	// If the account being removed is a legacy account (pre-dates core api), remove the old data.
	switch( $account ) {
		case 'Divi Builder Aweber':
			et_delete_option( 'divi_aweber_consumer_key' );
			et_delete_option( 'divi_aweber_consumer_secret' );
			et_delete_option( 'divi_aweber_access_key' );
			et_delete_option( 'divi_aweber_access_secret' );
			break;
		case 'Divi Builder Plugin Aweber':
			$opts  = (array) get_option( 'et_pb_builder_options' );
			unset( $opts['aweber_consumer_key'], $opts['aweber_consumer_secret'], $opts['aweber_access_key'], $opts['aweber_access_secret'] );
			update_option( 'et_pb_builder_options', $opts );
			break;
		case 'Divi Builder MailChimp':
			et_delete_option( 'divi_mailchimp_api_key' );
			break;
		case 'Divi Builder Plugin MailChimp':
			$options  = (array) get_option( 'et_pb_builder_options' );
			unset( $options['newsletter_main_mailchimp_key'] );
			update_option( 'et_pb_builder_options', $options );
			break;
	}

	$providers = et_core_api_email_providers();
	$provider  = $providers->get( $name_or_slug, $account );

	if ( $provider ) {
		$provider->delete();
	}
}
endif;
