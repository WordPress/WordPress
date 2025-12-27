<?php

use Sentry\Breadcrumb;
use Sentry\SentrySdk;
use Sentry\Tracing\SpanContext;
use function Sentry\addBreadcrumb;

/**
 * Creates spans for the following transient API functions:
 * - get_transient();
 * - get_site_transient();
 * - set_transient();
 * - set_site_transient();
 * - delete_transient();
 * - delete_site_transient();
 *
 * @internal This class is not part of the public API and may be removed or changed at any time.
 */
class WP_Sentry_Tracing_Feature_Transients extends WP_Sentry_Tracing_Feature {
	use WP_Sentry_Tracks_Pushed_Scopes_And_Spans;

	protected const FEATURE_KEY = 'transients';

	public function __construct() {
		if ( ! $this->span_or_breadcrumb_enabled() ) {
			return;
		}

		add_filter( 'all', [ $this, 'handle_all_filter' ], 9999 );

		if ( $this->span_enabled() ) {
			if ( array_key_exists( 'wp_version', $GLOBALS ) && version_compare( $GLOBALS['wp_version'], '6.8.0', '<' ) ) {
				add_action( 'setted_transient', [ $this, 'maybe_finish_current_span' ], 10, 0 );
				add_action( 'setted_site_transient', [ $this, 'maybe_finish_current_span' ], 10, 0 );
			} else {
				add_action( 'set_transient', [ $this, 'maybe_finish_current_span' ], 10, 0 );
				add_action( 'set_site_transient', [ $this, 'maybe_finish_current_span' ], 10, 0 );
			}

			add_action( 'deleted_transient', [ $this, 'maybe_finish_current_span' ], 10, 0 );
			add_action( 'deleted_site_transient', [ $this, 'maybe_finish_current_span' ], 10, 0 );
		}
	}

	public function handle_all_filter( string $hook_name ): void {
		if ( $this->str_starts_with( $hook_name, 'pre_set_transient_' ) || $this->str_starts_with( $hook_name, 'pre_set_site_transient_' ) ) {
			// @TODO: This _shouldn't_ be necessary, but it is. Investigate.
			$this->maybe_finish_current_span();

			$transientKey = func_get_args()[2];

			$this->record_transient_operation( 'put', $transientKey );
		} elseif ( $this->str_starts_with( $hook_name, 'pre_transient_' ) || $this->str_starts_with( $hook_name, 'pre_site_transient_' ) ) {
			// @TODO: This _shouldn't_ be necessary, but it is. Investigate.
			$this->maybe_finish_current_span();

			$transientKey = func_get_args()[2];

			$this->record_transient_operation( 'get', $transientKey );
		} elseif ( $this->str_starts_with( $hook_name, 'delete_transient_' ) || $this->str_starts_with( $hook_name, 'delete_site_transient_' ) ) {
			// @TODO: This _shouldn't_ be necessary, but it is. Investigate.
			$this->maybe_finish_current_span();

			$transientKey = func_get_args()[1];

			$this->record_transient_operation( 'remove', $transientKey );
		} elseif ( $this->str_starts_with( $hook_name, 'transient_' ) || $this->str_starts_with( $hook_name, 'site_transient_' ) ) {
			$span = $this->maybe_pop_span();

			if ( $span !== null ) {
				$span->setData( [
					// If the transient does not exist, does not have a value, or has expired, then the return value will be false.
					// See: https://developer.wordpress.org/reference/functions/get_transient/
					'cache.hit' => func_get_args()[1] !== false,
				] );
				$span->finish();
			}
		}
	}

	private function record_transient_operation( string $operation, string $key ): void {
		if ( $this->span_enabled() ) {
			$this->maybe_start_span( $operation, $key );
		}

		if ( $this->breadcrumb_enabled() ) {
			addBreadcrumb( new Breadcrumb(
				Breadcrumb::LEVEL_INFO,
				Breadcrumb::TYPE_DEFAULT,
				'cache',
				"{$operation}: {$key}"
			) );
		}
	}

	private function maybe_start_span( string $operation, string $key ): void {
		$parentSpan = SentrySdk::getCurrentHub()->getSpan();

		// If there is no sampled span there is no need to handle the event
		if ( $parentSpan === null || ! $parentSpan->getSampled() ) {
			return;
		}

		$context = new SpanContext;
		$context->setOp( "cache.{$operation}" );
		$context->setData( [
			'cache.key' => $key,
		] );
		$context->setDescription( $key );

		$this->push_span( $parentSpan->startChild( $context ) );
	}

	public function maybe_finish_current_span(): void {
		$this->maybe_finish_span();
	}

	private function str_starts_with( string $haystack, string $needle ): bool {
		return strpos( $haystack, $needle ) === 0;
	}
}
