<?php

use Sentry\SentrySdk;
use Sentry\Tracing\SpanContext;

/**
 * @internal This class is not part of the public API and may be removed or changed at any time.
 */
class WP_Sentry_Tracing_Feature_Plugins extends WP_Sentry_Tracing_Feature {
	use WP_Sentry_Tracks_Pushed_Scopes_And_Spans;

	protected const FEATURE_KEY = 'plugins';

	public function __construct() {
		if ( ! $this->span_enabled() ) {
			return;
		}

		// This is the first action we can hook into and that runs just before loading the plugins
		// This only works properly if the plugin is loaded as a mu-plugin or in the `wp-config.php`
		add_action( 'muplugins_loaded', [ $this, 'handle_muplugins_loaded' ], 0 );
		add_action( 'plugins_loaded', [ $this, 'handle_plugins_loaded' ], PHP_INT_MAX );
	}

	public function handle_muplugins_loaded(): void {
		$parentSpan = SentrySdk::getCurrentHub()->getSpan();

		// If there is no sampled span there is no need to handle the event
		if ( $parentSpan === null || ! $parentSpan->getSampled() ) {
			return;
		}

		$context = new SpanContext;
		$context->setOp( 'plugins.setup' );

		$this->push_span( $parentSpan->startChild( $context ) );
	}

	public function handle_plugins_loaded(): void {
		$this->maybe_finish_span();
	}
}
