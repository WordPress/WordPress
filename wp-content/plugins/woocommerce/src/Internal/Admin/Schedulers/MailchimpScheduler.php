<?php

namespace Automattic\WooCommerce\Internal\Admin\Schedulers;

/**
 * Class MailchimpScheduler
 *
 * @package Automattic\WooCommerce\Admin\Schedulers
 */
class MailchimpScheduler {

	const SUBSCRIBE_ENDPOINT     = 'https://woocommerce.com/wp-json/wccom/v1/subscribe';
	const SUBSCRIBE_ENDPOINT_DEV = 'http://woocommerce.test/wp-json/wccom/v1/subscribe';

	const SUBSCRIBED_OPTION_NAME             = 'woocommerce_onboarding_subscribed_to_mailchimp';
	const SUBSCRIBED_ERROR_COUNT_OPTION_NAME = 'woocommerce_onboarding_subscribed_to_mailchimp_error_count';
	const MAX_ERROR_THRESHOLD                = 3;

	const LOGGER_CONTEXT = 'mailchimp_scheduler';

	/**
	 * The logger instance.
	 *
	 * @var \WC_Logger_Interface|null
	 */
	private $logger;

	/**
	 * MailchimpScheduler constructor.
	 *
	 * @internal
	 * @param \WC_Logger_Interface|null $logger Logger instance.
	 */
	public function __construct( \WC_Logger_Interface $logger = null ) {
		if ( null === $logger ) {
			$logger = wc_get_logger();
		}
		$this->logger = $logger;
	}

	/**
	 * Attempt to subscribe store_email to MailChimp.
	 *
	 * @internal
	 */
	public function run() {
		// Abort if we've already subscribed to MailChimp.
		if ( 'yes' === get_option( self::SUBSCRIBED_OPTION_NAME ) ) {
			return false;
		}

		$profile_data = get_option( 'woocommerce_onboarding_profile' );
		if ( ! isset( $profile_data['is_agree_marketing'] ) || false === $profile_data['is_agree_marketing'] ) {
			return false;
		}

		// Abort if store_email doesn't exist.
		if ( ! isset( $profile_data['store_email'] ) ) {
			return false;
		}

		// Abort if failed requests reaches the threshold.
		if ( intval( get_option( self::SUBSCRIBED_ERROR_COUNT_OPTION_NAME, 0 ) ) >= self::MAX_ERROR_THRESHOLD ) {
			return false;
		}

		$response = $this->make_request( $profile_data['store_email'] );
		if ( is_wp_error( $response ) || ! isset( $response['body'] ) ) {
			$this->handle_request_error();
			return false;
		}

		$body = json_decode( $response['body'] );
		if ( isset( $body->success ) && true === $body->success ) {
			update_option( self::SUBSCRIBED_OPTION_NAME, 'yes' );
			return true;
		}

		$this->handle_request_error( $body );
		return false;
	}

	/**
	 * Make an HTTP request to the API.
	 *
	 * @internal
	 * @param string $store_email Email address to subscribe.
	 *
	 * @return mixed
	 */
	public function make_request( $store_email ) {
		if ( true === defined( 'WP_ENVIRONMENT_TYPE' ) && 'development' === constant( 'WP_ENVIRONMENT_TYPE' ) ) {
			$subscribe_endpoint = self::SUBSCRIBE_ENDPOINT_DEV;
		} else {
			$subscribe_endpoint = self::SUBSCRIBE_ENDPOINT;
		}

		return wp_remote_post(
			$subscribe_endpoint,
			array(
				'user-agent' => 'WooCommerce/' . WC()->version . '; ' . get_bloginfo( 'url' ),
				'method'     => 'POST',
				'body'       => array(
					'email' => $store_email,
				),
			)
		);
	}

	/**
	 * Reset options.
	 *
	 * @internal
	 */
	public static function reset() {
		delete_option( self::SUBSCRIBED_OPTION_NAME );
		delete_option( self::SUBSCRIBED_ERROR_COUNT_OPTION_NAME );
	}

	/**
	 * Handle subscribe API error.
	 *
	 * @internal
	 * @param string $extra_msg  Extra message to log.
	 */
	private function handle_request_error( $extra_msg = null ) {
		// phpcs:ignore
		$msg = isset( $extra_msg ) ? 'Incorrect response from Mailchimp API with: ' . print_r( $extra_msg, true ) : 'Error getting a response from Mailchimp API.';

		$this->logger->error( $msg, array( 'source' => self::LOGGER_CONTEXT ) );

		$accumulated_error_count = intval( get_option( self::SUBSCRIBED_ERROR_COUNT_OPTION_NAME, 0 ) ) + 1;
		update_option( self::SUBSCRIBED_ERROR_COUNT_OPTION_NAME, $accumulated_error_count );
	}
}
