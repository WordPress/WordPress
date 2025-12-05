<?php

/**
 * Class for the Stripe API.
 *
 * @link https://docs.stripe.com/api
 */
class WPCF7_Stripe_API {

	const api_version = '2022-11-15';
	const partner_id = 'pp_partner_HHbvqLh1AaO7Am';
	const app_name = 'WordPress Contact Form 7';
	const app_url = 'https://contactform7.com/stripe-integration/';

	private $secret;


	/**
	 * Constructor.
	 *
	 * @param string $secret Secret key.
	 */
	public function __construct( $secret ) {
		$this->secret = $secret;
	}


	/**
	 * Sends a debug information for a remote request to the PHP error log.
	 *
	 * @param string $url URL to retrieve.
	 * @param array $request Request arguments.
	 * @param array|WP_Error $response The response or WP_Error on failure.
	 */
	private function log( $url, $request, $response ) {
		wpcf7_log_remote_request( $url, $request, $response );
	}


	/**
	 * Returns default set of HTTP request headers used for Stripe API.
	 *
	 * @link https://docs.stripe.com/building-plugins#setappinfo
	 *
	 * @return array An associative array of headers.
	 */
	private function default_headers() {
		$app_info = array(
			'name' => self::app_name,
			'partner_id' => self::partner_id,
			'url' => self::app_url,
			'version' => WPCF7_VERSION,
		);

		$ua = array(
			'lang' => 'php',
			'lang_version' => PHP_VERSION,
			'application' => $app_info,
		);

		$headers = array(
			'Authorization' => sprintf( 'Bearer %s', $this->secret ),
			'Stripe-Version' => self::api_version,
			'X-Stripe-Client-User-Agent' => wp_json_encode( $ua ),
			'User-Agent' => sprintf(
				'%1$s/%2$s (%3$s)',
				self::app_name,
				WPCF7_VERSION,
				self::app_url
			),
		);

		return $headers;
	}


	/**
	 * Creates a Payment Intent.
	 *
	 * @link https://docs.stripe.com/api/payment_intents/create
	 *
	 * @param string|array $args Optional. Arguments to control behavior.
	 * @return array|bool An associative array if 200 OK, false otherwise.
	 */
	public function create_payment_intent( $args = '' ) {
		$args = wp_parse_args( $args, array(
			'amount' => 0,
			'currency' => '',
			'receipt_email' => '',
		) );

		if ( ! is_email( $args['receipt_email'] ) ) {
			unset( $args['receipt_email'] );
		}

		$endpoint = 'https://api.stripe.com/v1/payment_intents';

		$request = array(
			'headers' => $this->default_headers(),
			'body' => $args,
		);

		$response = wp_remote_post( sanitize_url( $endpoint ), $request );

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			if ( WP_DEBUG ) {
				$this->log( $endpoint, $request, $response );
			}

			return false;
		}

		$response_body = wp_remote_retrieve_body( $response );
		$response_body = json_decode( $response_body, true );

		return $response_body;
	}


	/**
	 * Retrieves a Payment Intent.
	 *
	 * @link https://docs.stripe.com/api/payment_intents/retrieve
	 *
	 * @param string $id Payment Intent identifier.
	 * @return array|bool An associative array if 200 OK, false otherwise.
	 */
	public function retrieve_payment_intent( $id ) {
		$endpoint = sprintf(
			'https://api.stripe.com/v1/payment_intents/%s',
			urlencode( $id )
		);

		$request = array(
			'headers' => $this->default_headers(),
		);

		$response = wp_remote_get( sanitize_url( $endpoint ), $request );

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			if ( WP_DEBUG ) {
				$this->log( $endpoint, $request, $response );
			}

			return false;
		}

		$response_body = wp_remote_retrieve_body( $response );
		$response_body = json_decode( $response_body, true );

		return $response_body;
	}


	/**
	 * Updates a Payment Intent.
	 *
	 * @link https://docs.stripe.com/api/payment_intents/update
	 *
	 * @param string $id Payment Intent identifier.
	 * @param array $parameters Parameters.
	 * @return array|bool An associative array if 200 OK, false otherwise.
	 */
	public function update_payment_intent( $id, $parameters ) {
		$endpoint = sprintf(
			'https://api.stripe.com/v1/payment_intents/%s',
			urlencode( $id )
		);

		$request = array(
			'headers' => $this->default_headers(),
			'body' => wp_parse_args( $parameters, array() ),
		);

		$response = wp_remote_post( sanitize_url( $endpoint ), $request );

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			if ( WP_DEBUG ) {
				$this->log( $endpoint, $request, $response );
			}

			return false;
		}

		$response_body = wp_remote_retrieve_body( $response );
		$response_body = json_decode( $response_body, true );

		return $response_body;
	}

}
