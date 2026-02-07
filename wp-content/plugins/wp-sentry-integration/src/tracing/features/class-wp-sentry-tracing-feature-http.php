<?php

use Sentry\Breadcrumb;
use Sentry\SentrySdk;
use Sentry\Tracing\SpanContext;
use function Sentry\addBreadcrumb;

/**
 * @internal This class is not part of the public API and may be removed or changed at any time.
 */
class WP_Sentry_Tracing_Feature_HTTP extends WP_Sentry_Tracing_Feature {
	use WP_Sentry_Tracks_Pushed_Scopes_And_Spans;

	protected const FEATURE_KEY = 'http';

	public function __construct() {
		if ( ! $this->span_or_breadcrumb_enabled() ) {
			return;
		}

		add_filter( 'pre_http_request', [ $this, 'handle_pre_http_request' ], 9999, 3 );
		add_action( 'http_api_debug', [ $this, 'handle_http_api_debug' ], 9999, 5 );
	}

	/** @param false|array|WP_Error $response */
	public function handle_pre_http_request( $response, array $parsed_args, ?string $url ) {
		// Fixes: https://github.com/stayallive/wp-sentry/issues/225
		if ( $url === null ) {
			return $response;
		}

		// We expect the response to be `false` otherwise it was filtered and we should not process it since it was filtered
		if ( $response !== false ) {
			return $response;
		}

		if ( $this->span_enabled() ) {
			$method     = strtoupper( $parsed_args['method'] );
			$fullUri    = $this->get_full_uri( $url );
			$partialUri = $this->get_partial_uri( $fullUri );

			$this->handle_span_start( $method . ' ' . $partialUri, [
				'url'                 => $partialUri,
				'http.query'          => $fullUri->getQuery(),
				'http.fragment'       => $fullUri->getFragment(),
				'http.request.method' => $method,
			] );
		}

		return $response;
	}

	/** @param array|WP_Error $response */
	public function handle_http_api_debug( $response, string $context, string $class, array $parsed_args, ?string $url ): void {
		// Fixes: https://github.com/stayallive/wp-sentry/issues/225
		if ( $url === null ) {
			return;
		}

		$method     = strtoupper( $parsed_args['method'] );
		$fullUri    = $this->get_full_uri( $url );
		$partialUri = $this->get_partial_uri( $fullUri );

		$http_status = 0;
		$data        = [
			'url'                 => $partialUri,
			// See: https://develop.sentry.dev/sdk/performance/span-data-conventions/#http
			'http.query'          => $fullUri->getQuery(),
			'http.fragment'       => $fullUri->getFragment(),
			'http.request.method' => $method,
			// @TODO: Figure out how to get the request body size
			// 'http.request.body.size' => strlen( $parsed_args['body'] ?? '' ),
		];

		if ( is_array( $response ) ) {
			$response = $response['http_response'] ?? null;

			if ( $response instanceof WP_HTTP_Requests_Response ) {
				$data['http.response.status_code'] = $http_status = $response->get_status();
				$data['http.response.body.size']   = strlen( $response->get_data() );
			}
		}

		if ( $this->span_enabled() ) {
			$this->handle_finish_span( $http_status, $data );
		}

		if ( $this->breadcrumb_enabled() ) {
			$this->handle_breadcrumb( $data );
		}
	}

	private function handle_span_start( string $description, array $data ): void {
		$parentSpan = SentrySdk::getCurrentHub()->getSpan();

		// If there is no sampled span there is no need to handle the event
		if ( $parentSpan === null || ! $parentSpan->getSampled() ) {
			return;
		}

		$context = new SpanContext;
		$context->setOp( 'http.client' );
		$context->setDescription( $description );
		$context->setData( $data );

		$this->push_span( $parentSpan->startChild( $context ) );
	}

	private function handle_finish_span( int $http_status, array $data ): void {
		$span = $this->maybe_pop_span();

		if ( $span === null ) {
			return;
		}

		$span->setData( $data );
		$span->setHttpStatus( $http_status );

		$span->finish();
	}

	private function handle_breadcrumb( array $data ): void {
		addBreadcrumb( new Breadcrumb(
			Breadcrumb::LEVEL_INFO,
			Breadcrumb::TYPE_HTTP,
			'http',
			null,
			$data
		) );
	}

	/** @return \Psr\Http\Message\UriInterface */
	private function get_full_uri( string $url ) {
		if ( class_exists( WPSentry\ScopedVendor\GuzzleHttp\Psr7\Uri::class ) ) {
			/** @noinspection PhpIncompatibleReturnTypeInspection */
			return new WPSentry\ScopedVendor\GuzzleHttp\Psr7\Uri( $url );
		}

		if ( class_exists( GuzzleHttp\Psr7\Uri::class ) ) {
			return new GuzzleHttp\Psr7\Uri( $url );
		}

		throw new RuntimeException( 'No compatible PSR-7 implementation found' );
	}

	/** @param \Psr\Http\Message\UriInterface $uri */
	private function get_partial_uri( $uri ): string {
		if ( class_exists( WPSentry\ScopedVendor\GuzzleHttp\Psr7\Uri::class ) ) {
			return (string) WPSentry\ScopedVendor\GuzzleHttp\Psr7\Uri::fromParts( [
				'scheme' => $uri->getScheme(),
				'host'   => $uri->getHost(),
				'port'   => $uri->getPort(),
				'path'   => $uri->getPath(),
			] );
		}

		if ( class_exists( GuzzleHttp\Psr7\Uri::class ) ) {
			return (string) GuzzleHttp\Psr7\Uri::fromParts( [
				'scheme' => $uri->getScheme(),
				'host'   => $uri->getHost(),
				'port'   => $uri->getPort(),
				'path'   => $uri->getPath(),
			] );
		}

		throw new RuntimeException( 'No compatible PSR-7 implementation found' );
	}
}
