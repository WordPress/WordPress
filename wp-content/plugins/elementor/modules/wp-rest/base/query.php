<?php

namespace Elementor\Modules\WpRest\Base;

use Elementor\Core\Utils\Api\Error_Builder;
use Elementor\Core\Utils\Collection;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Query {
	const NAMESPACE = 'elementor/v1';
	const NONCE_KEY = 'x_wp_nonce';

	const KEYS_CONVERSION_MAP_KEY = 'keys_conversion_map';
	const IS_PUBLIC_KEY = 'is_public';
	const TAX_QUERY_KEY = 'tax_query';
	const META_QUERY_KEY = 'meta_query';

	const MAX_RESPONSE_COUNT = 100;
	const ITEMS_COUNT_KEY = 'items_count';

	const INCLUDED_TYPE_KEY = 'included_types';
	const EXCLUDED_TYPE_KEY = 'excluded_types';
	const HIDE_EMPTY_KEY = 'hide_empty';

	const SEARCH_TERM_KEY = 'term';
	const SEARCH_FILTER_PRIORITY = 10;

	/**
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response
	 **/
	abstract protected function get( \WP_REST_Request $request );

	abstract protected static function get_allowed_param_keys(): array;

	abstract protected static function get_keys_to_encode(): array;

	abstract protected function get_endpoint_registration_args(): array;

	public function register( $endpoint, bool $override_existing_endpoints = false ): void {
		register_rest_route( self::NAMESPACE, $endpoint, [
			[
				'methods' => \WP_REST_Server::READABLE,
				'permission_callback' => fn ( \WP_REST_Request $request ) => $this->validate_access_permission( $request ),
				'args' => $this->get_endpoint_registration_args(),
				'callback' => fn ( \WP_REST_Request $request ) => $this->send( fn () => $this->get( $request ) ),
			],
		], $override_existing_endpoints );
	}

	/**
	 * @param array $item The input array with original keys.
	 * @param array $dictionary An associative array mapping old keys to new keys.
	 * @return array The array with translated keys.
	 */
	public function translate_keys( array $item, array $dictionary ): array {
		if ( empty( $dictionary ) ) {
			return $item;
		}

		$replaced = [];

		foreach ( $item as $key => $value ) {
			if ( ! isset( $dictionary[ $key ] ) ) {
				continue;
			}

			$replaced[ $dictionary[ $key ] ] = $value;
		}

		return $replaced;
	}

	/**
	 * @param array<string>|string $input The input data, expected to be an array or JSON-encoded string.
	 * @return array The sanitized array of strings.
	 */
	public static function sanitize_string_array( $input ) {
		if ( ! is_array( $input ) ) {
			$raw = sanitize_text_field( $input );
			$decoded = json_decode( $raw, true );
			if ( is_array( $decoded ) ) {
				$input = $decoded;
			} else {
				$input = false !== strpos( $raw, ',' ) ? explode( ',', $raw ) : ( '' !== $raw ? [ $raw ] : [] );
			}
		}

		return Collection::make( $input )
			->reduce( function ( $carry, $value, $key ) {
				if ( $value ) {
					$carry[ $key ] = is_array( $value ) ? self::sanitize_string_array( $value ) : sanitize_text_field( $value );
				}

				return $carry;
			}, [] );
	}


	private function validate_access_permission( $request ): bool {
		$nonce = $request->get_header( self::NONCE_KEY );

		return current_user_can( 'edit_posts' ) && wp_verify_nonce( $nonce, 'wp_rest' );
	}

	/**
	 * @param callable $cb The route callback.
	 * @return \WP_REST_Response | \WP_Error
	 */
	private function send( callable $cb ) {
		try {
			$response = $cb();
		} catch ( \Exception $e ) {
			return Error_Builder::make( $e->getCode() )
				->set_message( $e->getMessage() )
				->build();
		}

		return $response;
	}

	/**
	 * @param $args array{
	 *     excluded_types: array,
	 *     included_types: array,
	 *     keys_conversion_map: array,
	 * } The query parameters
	 * @return array The query parameters.
	 */
	public static function build_query_params( array $args ): array {
		$allowed_keys = static::get_allowed_param_keys();
		$keys_to_encode = static::get_keys_to_encode();
		$params = [];

		foreach ( $args as $key => $value ) {
			if ( ! in_array( $key, $allowed_keys, true ) || ! isset( $value ) ) {
				continue;
			}

			if ( ! in_array( $key, $keys_to_encode, true ) ) {
				$params[ $key ] = $value;
				continue;
			}

			$params[ $key ] = wp_json_encode( $value );
		}

		return $params;
	}
}
