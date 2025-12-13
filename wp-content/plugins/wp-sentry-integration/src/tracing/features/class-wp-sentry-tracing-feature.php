<?php

/**
 * @internal This class is not part of the public API and may be removed or changed at any time.
 */
abstract class WP_Sentry_Tracing_Feature {
	protected const FEATURE_KEY = 'undefined';

	private static $feature_memo = [];

	protected function span_enabled( bool $default = true ): bool {
		if ( ! defined( 'WP_SENTRY_TRACING_FEATURES' ) ) {
			return $default;
		}

		$memo_key = static::FEATURE_KEY . '_spans';

		if ( isset( self::$feature_memo[ $memo_key ] ) ) {
			return self::$feature_memo[ $memo_key ];
		}

		$result = defined( 'WP_SENTRY_TRACING_FEATURES' )
			? WP_SENTRY_TRACING_FEATURES[ static::FEATURE_KEY ]['spans'] ?? $default
			: $default;

		self::$feature_memo[ $memo_key ] = $result;

		return $result;
	}

	protected function breadcrumb_enabled( bool $default = true ): bool {
		$memo_key = static::FEATURE_KEY . '_breadcrumbs';

		if ( isset( self::$feature_memo[ $memo_key ] ) ) {
			return self::$feature_memo[ $memo_key ];
		}

		$result = defined( 'WP_SENTRY_TRACING_FEATURES' )
			? WP_SENTRY_TRACING_FEATURES[ static::FEATURE_KEY ]['breadcrumbs'] ?? $default
			: $default;

		self::$feature_memo[ $memo_key ] = $result;

		return $result;
	}

	protected function span_or_breadcrumb_enabled(): bool {
		return $this->span_enabled() || $this->breadcrumb_enabled();
	}
}
