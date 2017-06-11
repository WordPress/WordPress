<?php
/**
 * Administration: Community Events class.
 *
 * @package WordPress
 * @subpackage Administration
 * @since 4.8.0
 */

/**
 * Class WP_Community_Events.
 *
 * A client for api.wordpress.org/events.
 *
 * @since 4.8.0
 */
class WP_Community_Events {
	/**
	 * ID for a WordPress user account.
	 *
	 * @access protected
	 * @since 4.8.0
	 *
	 * @var int
	 */
	protected $user_id = 0;

	/**
	 * Stores location data for the user.
	 *
	 * @access protected
	 * @since 4.8.0
	 *
	 * @var bool|array
	 */
	protected $user_location = false;

	/**
	 * Constructor for WP_Community_Events.
	 *
	 * @since 4.8.0
	 *
	 * @param int        $user_id       WP user ID.
	 * @param bool|array $user_location Stored location data for the user.
	 *                                  false to pass no location;
	 *                                  array to pass a location {
	 *     @type string $description The name of the location
	 *     @type string $latitude    The latitude in decimal degrees notation, without the degree
	 *                               symbol. e.g.: 47.615200.
	 *     @type string $longitude   The longitude in decimal degrees notation, without the degree
	 *                               symbol. e.g.: -122.341100.
	 *     @type string $country     The ISO 3166-1 alpha-2 country code. e.g.: BR
	 * }
	 */
	public function __construct( $user_id, $user_location = false ) {
		$this->user_id       = absint( $user_id );
		$this->user_location = $user_location;
	}

	/**
	 * Gets data about events near a particular location.
	 *
	 * Cached events will be immediately returned if the `user_location` property
	 * is set for the current user, and cached events exist for that location.
	 *
	 * Otherwise, this method sends a request to the w.org Events API with location
	 * data. The API will send back a recognized location based on the data, along
	 * with nearby events.
	 *
	 * The browser's request for events is proxied with this method, rather
	 * than having the browser make the request directly to api.wordpress.org,
	 * because it allows results to be cached server-side and shared with other
	 * users and sites in the network. This makes the process more efficient,
	 * since increasing the number of visits that get cached data means users
	 * don't have to wait as often; if the user's browser made the request
	 * directly, it would also need to make a second request to WP in order to
	 * pass the data for caching. Having WP make the request also introduces
	 * the opportunity to anonymize the IP before sending it to w.org, which
	 * mitigates possible privacy concerns.
	 *
	 * @since 4.8.0
	 *
	 * @param string $location_search Optional. City name to help determine the location.
	 *                                e.g., "Seattle". Default empty string.
	 * @param string $timezone        Optional. Timezone to help determine the location.
	 *                                Default empty string.
	 * @return array|WP_Error A WP_Error on failure; an array with location and events on
	 *                        success.
	 */
	public function get_events( $location_search = '', $timezone = '' ) {
		$cached_events = $this->get_cached_events();

		if ( ! $location_search && $cached_events ) {
			return $cached_events;
		}

		$api_url        = 'https://api.wordpress.org/events/1.0/';
		$request_args   = $this->get_request_args( $location_search, $timezone );
		$response       = wp_remote_get( $api_url, $request_args );
		$response_code  = wp_remote_retrieve_response_code( $response );
		$response_body  = json_decode( wp_remote_retrieve_body( $response ), true );
		$response_error = null;
		$debugging_info = compact( 'api_url', 'request_args', 'response_code', 'response_body' );

		if ( is_wp_error( $response ) ) {
			$response_error = $response;
		} elseif ( 200 !== $response_code ) {
			$response_error = new WP_Error(
				'api-error',
				/* translators: %s is a numeric HTTP status code; e.g., 400, 403, 500, 504, etc. */
				sprintf( __( 'Invalid API response code (%d)' ), $response_code )
			);
		} elseif ( ! isset( $response_body['location'], $response_body['events'] ) ) {
			$response_error = new WP_Error(
				'api-invalid-response',
				isset( $response_body['error'] ) ? $response_body['error'] : __( 'Unknown API error.' )
			);
		}

		if ( is_wp_error( $response_error ) ) {
			$this->maybe_log_events_response( $response_error->get_error_message(), $debugging_info );

			return $response_error;
		} else {
			$expiration = false;

			if ( isset( $response_body['ttl'] ) ) {
				$expiration = $response_body['ttl'];
				unset( $response_body['ttl'] );
			}

			/*
			 * The IP in the response is usually the same as the one that was sent
			 * in the request, but in some cases it is different. In those cases,
			 * it's important to reset it back to the IP from the request.
			 *
			 * For example, if the IP sent in the request is private (e.g., 192.168.1.100),
			 * then the API will ignore that and use the corresponding public IP instead,
			 * and the public IP will get returned. If the public IP were saved, though,
			 * then get_cached_events() would always return `false`, because the transient
			 * would be generated based on the public IP when saving the cache, but generated
			 * based on the private IP when retrieving the cache.
			 */
			if ( ! empty( $response_body['location']['ip'] ) ) {
				$response_body['location']['ip'] = $request_args['body']['ip'];
			}

			/*
			 * The API doesn't return a description for latitude/longitude requests,
			 * but the description is already saved in the user location, so that
			 * one can be used instead.
			 */
			if ( $this->coordinates_match( $request_args['body'], $response_body['location'] ) && empty( $response_body['location']['description'] ) ) {
				$response_body['location']['description'] = $this->user_location['description'];
			}

			$this->cache_events( $response_body, $expiration );

			$response_body = $this->trim_events( $response_body );
			$response_body = $this->format_event_data_time( $response_body );

			// Avoid bloating the log with all the event data, but keep the count.
			$debugging_info['response_body']['events'] = count( $debugging_info['response_body']['events'] ) . ' events trimmed.';

			$this->maybe_log_events_response( 'Valid response received', $debugging_info );

			return $response_body;
		}
	}

	/**
	 * Builds an array of args to use in an HTTP request to the w.org Events API.
	 *
	 * @access protected
	 * @since 4.8.0
	 *
	 * @param string $search   Optional. City search string. Default empty string.
	 * @param string $timezone Optional. Timezone string. Default empty string.
	 * @return array The request args.
	 */
	protected function get_request_args( $search = '', $timezone = '' ) {
		$args = array(
			'number' => 5, // Get more than three in case some get trimmed out.
			'ip'     => self::get_unsafe_client_ip(),
		);

		/*
		 * Include the minimal set of necessary arguments, in order to increase the
		 * chances of a cache-hit on the API side.
		 */
		if ( empty( $search ) && isset( $this->user_location['latitude'], $this->user_location['longitude'] ) ) {
			$args['latitude']  = $this->user_location['latitude'];
			$args['longitude'] = $this->user_location['longitude'];
		} else {
			$args['locale'] = get_user_locale( $this->user_id );

			if ( $timezone ) {
				$args['timezone'] = $timezone;
			}

			if ( $search ) {
				$args['location'] = $search;
			}
		}

		// Wrap the args in an array compatible with the second parameter of `wp_remote_get()`.
		return array(
			'body' => $args
		);
	}

	/**
	 * Determines the user's actual IP address and attempts to partially
	 * anonymize an IP address by converting it to a network ID.
	 *
	 * Geolocating the network ID usually returns a similar location as the
	 * actual IP, but provides some privacy for the user.
	 *
	 * $_SERVER['REMOTE_ADDR'] cannot be used in all cases, such as when the user
	 * is making their request through a proxy, or when the web server is behind
	 * a proxy. In those cases, $_SERVER['REMOTE_ADDR'] is set to the proxy address rather
	 * than the user's actual address.
	 *
	 * Modified from http://stackoverflow.com/a/2031935/450127, MIT license.
	 * Modified from https://github.com/geertw/php-ip-anonymizer, MIT license.
	 *
	 * SECURITY WARNING: This function is _NOT_ intended to be used in
	 * circumstances where the authenticity of the IP address matters. This does
	 * _NOT_ guarantee that the returned address is valid or accurate, and it can
	 * be easily spoofed.
	 *
	 * @access protected
	 * @since 4.8.0
	 *
	 * @return false|string The anonymized address on success; the given address
	 *                      or false on failure.
	 */
	public static function get_unsafe_client_ip() {
		$client_ip = false;

		// In order of preference, with the best ones for this purpose first.
		$address_headers = array(
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR',
		);

		foreach ( $address_headers as $header ) {
			if ( array_key_exists( $header, $_SERVER ) ) {
				/*
				 * HTTP_X_FORWARDED_FOR can contain a chain of comma-separated
				 * addresses. The first one is the original client. It can't be
				 * trusted for authenticity, but we don't need to for this purpose.
				 */
				$address_chain = explode( ',', $_SERVER[ $header ] );
				$client_ip     = trim( $address_chain[0] );

				break;
			}
		}

		// These functions are not available on Windows until PHP 5.3.
		if ( function_exists( 'inet_pton' ) && function_exists( 'inet_ntop' ) ) {
			if ( 4 === strlen( inet_pton( $client_ip ) ) ) {
				$netmask = '255.255.255.0'; // ipv4.
			} else {
				$netmask = 'ffff:ffff:ffff:ffff:0000:0000:0000:0000'; // ipv6.
			}

			$client_ip = inet_ntop( inet_pton( $client_ip ) & inet_pton( $netmask ) );
		}

		return $client_ip;
	}

	/**
	 * Test if two pairs of latitude/longitude coordinates match each other.
	 *
	 * @since 4.8.0
	 * @access protected
	 *
	 * @param array $a The first pair, with indexes 'latitude' and 'longitude'.
	 * @param array $b The second pair, with indexes 'latitude' and 'longitude'.
	 * @return bool True if they match, false if they don't.
	 */
	protected function coordinates_match( $a, $b ) {
		if ( ! isset( $a['latitude'], $a['longitude'], $b['latitude'], $b['longitude'] ) ) {
			return false;
		}

		return $a['latitude'] === $b['latitude'] && $a['longitude'] === $b['longitude'];
	}

	/**
	 * Generates a transient key based on user location.
	 *
	 * This could be reduced to a one-liner in the calling functions, but it's
	 * intentionally a separate function because it's called from multiple
	 * functions, and having it abstracted keeps the logic consistent and DRY,
	 * which is less prone to errors.
	 *
	 * @access protected
	 * @since 4.8.0
	 *
	 * @param  array $location Should contain 'latitude' and 'longitude' indexes.
	 * @return bool|string false on failure, or a string on success.
	 */
	protected function get_events_transient_key( $location ) {
		$key = false;

		if ( isset( $location['ip'] ) ) {
			$key = 'community-events-' . md5( $location['ip'] );
		} else if ( isset( $location['latitude'], $location['longitude'] ) ) {
			$key = 'community-events-' . md5( $location['latitude'] . $location['longitude'] );
		}

		return $key;
	}

	/**
	 * Caches an array of events data from the Events API.
	 *
	 * @access protected
	 * @since 4.8.0
	 *
	 * @param array    $events     Response body from the API request.
	 * @param int|bool $expiration Optional. Amount of time to cache the events. Defaults to false.
	 * @return bool true if events were cached; false if not.
	 */
	protected function cache_events( $events, $expiration = false ) {
		$set              = false;
		$transient_key    = $this->get_events_transient_key( $events['location'] );
		$cache_expiration = $expiration ? absint( $expiration ) : HOUR_IN_SECONDS * 12;

		if ( $transient_key ) {
			$set = set_site_transient( $transient_key, $events, $cache_expiration );
		}

		return $set;
	}

	/**
	 * Gets cached events.
	 *
	 * @since 4.8.0
	 *
	 * @return false|array false on failure; an array containing `location`
	 *                     and `events` items on success.
	 */
	public function get_cached_events() {
		$cached_response = get_site_transient( $this->get_events_transient_key( $this->user_location ) );
		$cached_response = $this->trim_events( $cached_response );

		return $this->format_event_data_time( $cached_response );
	}

	/**
	 * Adds formatted date and time items for each event in an API response.
	 *
	 * This has to be called after the data is pulled from the cache, because
	 * the cached events are shared by all users. If it was called before storing
	 * the cache, then all users would see the events in the localized data/time
	 * of the user who triggered the cache refresh, rather than their own.
	 *
	 * @access protected
	 * @since 4.8.0
	 *
	 * @param  array $response_body The response which contains the events.
	 * @return array The response with dates and times formatted.
	 */
	protected function format_event_data_time( $response_body ) {
		if ( isset( $response_body['events'] ) ) {
			foreach ( $response_body['events'] as $key => $event ) {
				$timestamp = strtotime( $event['date'] );

				/*
				 * The `date_format` option is not used because it's important
				 * in this context to keep the day of the week in the formatted date,
				 * so that users can tell at a glance if the event is on a day they
				 * are available, without having to open the link.
				 */
				/* translators: Date format for upcoming events on the dashboard. Include the day of the week. See https://secure.php.net/date. */
				$response_body['events'][ $key ]['formatted_date'] = date_i18n( __( 'l, M j, Y' ), $timestamp );
				$response_body['events'][ $key ]['formatted_time'] = date_i18n( get_option( 'time_format' ), $timestamp );
			}
		}

		return $response_body;
	}

	/**
	 * Discards expired events, and reduces the remaining list.
	 *
	 * @access protected
	 * @since 4.8.0
	 *
	 * @param  array $response_body The response body which contains the events.
	 * @return array The response body with events trimmed.
	 */
	protected function trim_events( $response_body ) {
		if ( isset( $response_body['events'] ) ) {
			$current_timestamp = current_time( 'timestamp' );

			foreach ( $response_body['events'] as $key => $event ) {
				// Skip WordCamps, because they might be multi-day events.
				if ( 'meetup' !== $event['type'] ) {
					continue;
				}

				$event_timestamp = strtotime( $event['date'] );

				if ( $current_timestamp > $event_timestamp && ( $current_timestamp - $event_timestamp ) > DAY_IN_SECONDS ) {
					unset( $response_body['events'][ $key ] );
				}
			}

			$response_body['events'] = array_slice( $response_body['events'], 0, 3 );
		}

		return $response_body;
	}

	/**
	 * Logs responses to Events API requests.
	 *
	 * All responses are logged when debugging, even if they're not WP_Errors.
	 * Debugging info is still needed for "successful" responses, because
	 * the API might have returned a different location than the one the user
	 * intended to receive. In those cases, knowing the exact `request_url` is
	 * critical.
	 *
	 * Errors are logged instead of being triggered, to avoid breaking the JSON
	 * response when called from AJAX handlers and `display_errors` is enabled.
	 *
	 * @access protected
	 * @since 4.8.0
	 *
	 * @param string $message A description of what occurred.
	 * @param array  $details Details that provide more context for the
	 *                        log entry.
	 */
	protected function maybe_log_events_response( $message, $details ) {
		if ( ! WP_DEBUG_LOG ) {
			return;
		}

		error_log( sprintf(
			'%s: %s. Details: %s',
			__METHOD__,
			trim( $message, '.' ),
			wp_json_encode( $details )
		) );
	}
}
