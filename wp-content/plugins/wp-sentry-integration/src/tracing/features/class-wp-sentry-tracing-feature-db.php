<?php

use Sentry\Breadcrumb;
use Sentry\SentrySdk;
use Sentry\Tracing\SpanContext;
use function Sentry\addBreadcrumb;

/**
 * @internal This class is not part of the public API and may be removed or changed at any time.
 */
class WP_Sentry_Tracing_Feature_DB extends WP_Sentry_Tracing_Feature {
	protected const FEATURE_KEY = 'db';

	public function __construct() {
		if ( ! $this->span_enabled() && ! $this->breadcrumb_enabled( defined( 'SAVEQUERIES' ) && SAVEQUERIES ) ) {
			return;
		}

		if ( ! defined( 'SAVEQUERIES' ) ) {
			define( 'SAVEQUERIES', true );
		}

		add_filter( 'log_query_custom_data', [ $this, 'handle_log_query_custom_data' ], 10, 5 );
	}

	public function handle_log_query_custom_data( array $query_data, string $query, float $query_time, string $query_callstack, float $query_start ): array {
		$callstack = array_reverse( explode( ', ', $query_callstack ) );

		if ( $this->span_enabled() ) {
			$this->handle_span( $query, $query_start, $query_start + $query_time, [
				'callstack' => $callstack,
			] );
		}

		if ( $this->breadcrumb_enabled() ) {
			$this->handle_breadcrumb( $query, $query_time * 1000, [
				'callstack' => $callstack,
			] );
		}

		return $query_data;
	}

	private function handle_span( string $query, float $start_timestamp, float $end_timestamp, array $data ): void {
		$parentSpan = SentrySdk::getCurrentHub()->getSpan();

		// If there is no sampled span there is no need to handle the event
		if ( $parentSpan === null || ! $parentSpan->getSampled() ) {
			return;
		}

		$context = new SpanContext;
		$context->setOp( 'db.sql.query' );
		$context->setData( $data );
		$context->setDescription( $query );
		$context->setStartTimestamp( $start_timestamp );

		$parentSpan->startChild( $context )->finish( $end_timestamp );
	}

	private function handle_breadcrumb( string $query, float $query_time_ms, array $data ): void {
		$data['executionTimeMs'] = $query_time_ms;

		addBreadcrumb( new Breadcrumb(
			Breadcrumb::LEVEL_INFO,
			Breadcrumb::TYPE_DEFAULT,
			'db.sql.query',
			$query,
			$data
		) );
	}
}
