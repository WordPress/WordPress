<?php

use Sentry\SentrySdk;
use Sentry\Tracing\Span;
use Sentry\Tracing\SpanStatus;

/**
 * @internal This class is not part of the public API and may be removed or changed at any time.
 */
trait WP_Sentry_Tracks_Pushed_Scopes_And_Spans {
	/**
	 * Hold the number of times the scope was pushed.
	 *
	 * @var int
	 */
	private $pushed_scope_count = 0;

	/**
	 * Hold the stack of parent spans that need to be put back on the scope.
	 *
	 * @var array<int, Span|null>
	 */
	private $parent_span_stack = [];

	/**
	 * Hold the stack of current spans that need to be finished still.
	 *
	 * @var array<int, Span|null>
	 */
	private $current_span_stack = [];

	protected function push_span( Span $span ): void {
		$hub = SentrySdk::getCurrentHub();

		$this->parent_span_stack[] = $hub->getSpan();

		$hub->setSpan( $span );

		$this->current_span_stack[] = $span;
	}

	protected function push_scope(): void {
		SentrySdk::getCurrentHub()->pushScope();

		++ $this->pushed_scope_count;
	}

	protected function maybe_pop_span(): ?Span {
		if ( count( $this->current_span_stack ) === 0 ) {
			return null;
		}

		$parent = array_pop( $this->parent_span_stack );

		SentrySdk::getCurrentHub()->setSpan( $parent );

		return array_pop( $this->current_span_stack );
	}

	protected function maybe_pop_scope(): void {
		if ( $this->pushed_scope_count === 0 ) {
			return;
		}

		SentrySdk::getCurrentHub()->popScope();

		-- $this->pushed_scope_count;
	}

	protected function maybe_finish_span( ?SpanStatus $status = null ): ?Span {
		$span = $this->maybe_pop_span();

		if ( $span === null ) {
			return null;
		}

		if ( $status !== null ) {
			$span->setStatus( $status );
		}

		$span->finish();

		return $span;
	}
}
