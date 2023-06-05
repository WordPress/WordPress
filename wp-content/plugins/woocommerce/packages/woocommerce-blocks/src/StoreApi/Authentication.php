<?php
namespace Automattic\WooCommerce\StoreApi;

use Automattic\WooCommerce\StoreApi\Utilities\RateLimits;

/**
 * Authentication class.
 */
class Authentication {
	/**
	 * Hook into WP lifecycle events.
	 */
	public function init() {
		add_filter( 'rest_authentication_errors', array( $this, 'check_authentication' ) );
		add_action( 'set_logged_in_cookie', array( $this, 'set_logged_in_cookie' ) );
	}

	/**
	 * The Store API does not require authentication.
	 *
	 * @param \WP_Error|mixed $result Error from another authentication handler, null if we should handle it, or another value if not.
	 * @return \WP_Error|null|bool
	 */
	public function check_authentication( $result ) {
		if ( ! $this->is_request_to_store_api() ) {
			return $result;
		}

		// Enable Rate Limiting for logged-in users without 'edit posts' capability.
		if ( ! current_user_can( 'edit_posts' ) ) {
			$result = $this->apply_rate_limiting( $result );
		}

		// Pass through errors from other authentication methods used before this one.
		return ! empty( $result ) ? $result : true;
	}

	/**
	 * When the login cookies are set, they are not available until the next page reload. For the Store API, specifically
	 * for returning updated nonces, we need this to be available immediately.
	 *
	 * @param string $logged_in_cookie The value for the logged in cookie.
	 */
	public function set_logged_in_cookie( $logged_in_cookie ) {
		if ( ! defined( 'LOGGED_IN_COOKIE' ) || ! $this->is_request_to_store_api() ) {
			return;
		}
		$_COOKIE[ LOGGED_IN_COOKIE ] = $logged_in_cookie;
	}

	/**
	 * Applies Rate Limiting to the request, and passes through any errors from other authentication methods used before this one.
	 *
	 * @param \WP_Error|mixed $result Error from another authentication handler, null if we should handle it, or another value if not.
	 * @return \WP_Error|null|bool
	 */
	protected function apply_rate_limiting( $result ) {
		$rate_limiting_options = RateLimits::get_options();

		if ( $rate_limiting_options->enabled ) {
			$action_id = 'store_api_request_';

			if ( is_user_logged_in() ) {
				$action_id .= get_current_user_id();
			} else {
				$ip_address = self::get_ip_address( $rate_limiting_options->proxy_support );
				$action_id .= md5( $ip_address );
			}

			$retry  = RateLimits::is_exceeded_retry_after( $action_id );
			$server = rest_get_server();
			$server->send_header( 'RateLimit-Limit', $rate_limiting_options->limit );

			if ( false !== $retry ) {
				$server->send_header( 'RateLimit-Retry-After', $retry );
				$server->send_header( 'RateLimit-Remaining', 0 );
				$server->send_header( 'RateLimit-Reset', time() + $retry );

				$ip_address = $ip_address ?? self::get_ip_address( $rate_limiting_options->proxy_support );
				/**
				 * Fires when the rate limit is exceeded.
				 *
				 * @since 8.9.0
				 *
				 * @param string $ip_address The IP address of the request.
				 */
				do_action( 'woocommerce_store_api_rate_limit_exceeded', $ip_address );

				return new \WP_Error(
					'rate_limit_exceeded',
					sprintf(
						'Too many requests. Please wait %d seconds before trying again.',
						$retry
					),
					array( 'status' => 400 )
				);
			}

			$rate_limit = RateLimits::update_rate_limit( $action_id );
			$server->send_header( 'RateLimit-Remaining', $rate_limit->remaining );
			$server->send_header( 'RateLimit-Reset', $rate_limit->reset );
		}

		return $result;
	}

	/**
	 * Check if is request to the Store API.
	 *
	 * @return bool
	 */
	protected function is_request_to_store_api() {
		if ( empty( $GLOBALS['wp']->query_vars['rest_route'] ) ) {
			return false;
		}
		return 0 === strpos( $GLOBALS['wp']->query_vars['rest_route'], '/wc/store/' );
	}

	/**
	 * Get current user IP Address.
	 *
	 * X_REAL_IP and CLIENT_IP are custom implementations designed to facilitate obtaining a user's ip through proxies, load balancers etc.
	 *
	 * _FORWARDED_FOR (XFF) request header is a de-facto standard header for identifying the originating IP address of a client connecting to a web server through a proxy server.
	 * Note for X_FORWARDED_FOR, Proxy servers can send through this header like this: X-Forwarded-For: client1, proxy1, proxy2.
	 * Make sure we always only send through the first IP in the list which should always be the client IP.
	 * Documentation at https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Forwarded-For
	 *
	 * Forwarded request header contains information that may be added by reverse proxy servers (load balancers, CDNs, and so on).
	 * Documentation at https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Forwarded
	 * Full RFC at https://datatracker.ietf.org/doc/html/rfc7239
	 *
	 * @param boolean $proxy_support Enables/disables proxy support.
	 *
	 * @return string
	 */
	protected static function get_ip_address( bool $proxy_support = false ) {

		if ( ! $proxy_support ) {
			return self::validate_ip( sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? 'unresolved_ip' ) ) );
		}

		if ( array_key_exists( 'HTTP_X_REAL_IP', $_SERVER ) ) {
			return self::validate_ip( sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_REAL_IP'] ) ) );
		}

		if ( array_key_exists( 'HTTP_CLIENT_IP', $_SERVER ) ) {
			return self::validate_ip( sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) ) );
		}

		if ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $_SERVER ) ) {
			$ips = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
			if ( is_array( $ips ) && ! empty( $ips ) ) {
				return self::validate_ip( trim( $ips[0] ) );
			}
		}

		if ( array_key_exists( 'HTTP_FORWARDED', $_SERVER ) ) {
			// Using regex instead of explode() for a smaller code footprint.
			// Expected format: Forwarded: for=192.0.2.60;proto=http;by=203.0.113.43,for="[2001:db8:cafe::17]:4711"...
			preg_match(
				'/(?<=for\=)[^;,]*/i', // We catch everything on the first "for" entry, and validate later.
				sanitize_text_field( wp_unslash( $_SERVER['HTTP_FORWARDED'] ) ),
				$matches
			);

			if ( strpos( $matches[0] ?? '', '"[' ) !== false ) { // Detect for ipv6, eg "[ipv6]:port".
				preg_match(
					'/(?<=\[).*(?=\])/i', // We catch only the ipv6 and overwrite $matches.
					$matches[0],
					$matches
				);
			}

			if ( ! empty( $matches ) ) {
				return self::validate_ip( trim( $matches[0] ) );
			}
		}

		return '0.0.0.0';
	}

	/**
	 * Uses filter_var() to validate and return ipv4 and ipv6 addresses
	 * Will return 0.0.0.0 if the ip is not valid. This is done to group and still rate limit invalid ips.
	 *
	 * @param string $ip ipv4 or ipv6 ip string.
	 *
	 * @return string
	 */
	protected static function validate_ip( $ip ) {
		$ip = filter_var(
			$ip,
			FILTER_VALIDATE_IP,
			array( FILTER_FLAG_NO_RES_RANGE, FILTER_FLAG_IPV6 )
		);

		return $ip ?: '0.0.0.0';
	}
}
