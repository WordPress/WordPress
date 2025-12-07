<?php

use Sentry\SentrySdk;
use Sentry\Tracing\SpanContext;

/**
 * @internal This class is not part of the public API and may be removed or changed at any time.
 */
class WP_Sentry_Tracing_Feature_Theme extends WP_Sentry_Tracing_Feature {
	use WP_Sentry_Tracks_Pushed_Scopes_And_Spans;

	protected const FEATURE_KEY = 'theme';

	public function __construct() {
		if ( ! $this->span_enabled() ) {
			return;
		}

		add_action( 'setup_theme', [ $this, 'handle_setup_theme' ], 0 );
		add_action( 'after_setup_theme', [ $this, 'handle_after_setup_theme' ], PHP_INT_MAX );
	}

	public function handle_setup_theme(): void {
		$parentSpan = SentrySdk::getCurrentHub()->getSpan();

		// If there is no sampled span there is no need to handle the event
		if ( $parentSpan === null || ! $parentSpan->getSampled() ) {
			return;
		}

		$context = new SpanContext;
		$context->setOp( 'theme.setup' );

		$this->push_span( $parentSpan->startChild( $context ) );
	}

	public function handle_after_setup_theme(): void {
		$this->maybe_finish_span();
	}
}
