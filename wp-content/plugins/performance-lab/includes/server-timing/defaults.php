<?php
/**
 * Server-Timing API default metrics
 *
 * @package performance-lab
 * @since 1.8.0
 */

// @codeCoverageIgnoreStart
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
// @codeCoverageIgnoreEnd

// Do not add any of the hooks if Server-Timing is disabled.
if ( defined( 'PERFLAB_DISABLE_SERVER_TIMING' ) && PERFLAB_DISABLE_SERVER_TIMING ) {
	return;
}

/**
 * Registers the default Server-Timing metrics for before rendering the template.
 *
 * These metrics should be registered as soon as possible.
 *
 * @since 1.8.0
 */
function perflab_register_default_server_timing_before_template_metrics(): void {
	$calculate_before_template_metrics = static function (): void {
		// WordPress execution prior to serving the template.
		perflab_server_timing_register_metric(
			'before-template',
			array(
				'measure_callback' => static function ( $metric ): void {
					// The 'timestart' global is set right at the beginning of WordPress execution.
					$metric->set_value( ( microtime( true ) - $GLOBALS['timestart'] ) * 1000.0 );
				},
				'access_cap'       => 'exist',
			)
		);

		// SQL query time is only measured if the SAVEQUERIES constant is set to true.
		if ( defined( 'SAVEQUERIES' ) && SAVEQUERIES ) {
			// WordPress database query time before template.
			perflab_server_timing_register_metric(
				'before-template-db-queries',
				array(
					'measure_callback' => static function ( $metric ): void {
						// This should never happen, but some odd database implementations may be doing it wrong.
						if ( ! isset( $GLOBALS['wpdb']->queries ) || ! is_array( $GLOBALS['wpdb']->queries ) ) {
							return;
						}

						// Store this value in a global to later subtract it from total query time after template.
						$GLOBALS['perflab_query_time_before_template'] = array_reduce(
							$GLOBALS['wpdb']->queries,
							static function ( $acc, $query ) {
								return $acc + $query[1];
							},
							0.0
						);
						$metric->set_value( $GLOBALS['perflab_query_time_before_template'] * 1000.0 );
					},
					'access_cap'       => 'exist',
				)
			);
		}
	};

	// If output buffering is used, explicitly measure only the time before serving the template.
	// Otherwise, the Server-Timing header will be sent before serving the template anyway.
	// We need to check for output buffer usage in the callback so that e.g. plugins and theme can
	// modify the value prior to the check.
	add_filter(
		'template_include',
		static function ( $passthrough ) use ( $calculate_before_template_metrics ) {
			if ( perflab_server_timing_use_output_buffer() ) {
				$calculate_before_template_metrics();
			}
			return $passthrough;
		},
		PHP_INT_MAX
	);
	add_action(
		'perflab_server_timing_send_header',
		static function () use ( $calculate_before_template_metrics ): void {
			if ( ! perflab_server_timing_use_output_buffer() ) {
				$calculate_before_template_metrics();
			}
		},
		PHP_INT_MAX
	);
}
perflab_register_default_server_timing_before_template_metrics();

/**
 * Registers the default Server-Timing metrics while rendering the template.
 *
 * These metrics should be registered at a later point, e.g. the 'wp_loaded' action.
 * They will only be registered if the Server-Timing API is configured to use an
 * output buffer for the site's template.
 *
 * @since 1.8.0
 */
function perflab_register_default_server_timing_template_metrics(): void {
	// Template-related metrics can only be recorded if output buffering is used.
	if ( ! perflab_server_timing_use_output_buffer() ) {
		return;
	}

	add_filter(
		'template_include',
		static function ( $passthrough = null ) {
			// WordPress execution while serving the template.
			perflab_server_timing_register_metric(
				'template',
				array(
					'measure_callback' => static function ( Perflab_Server_Timing_Metric $metric ): void {
						$metric->measure_before();
						add_action( 'perflab_server_timing_send_header', array( $metric, 'measure_after' ), PHP_INT_MAX );
					},
					'access_cap'       => 'exist',
				)
			);

			return $passthrough;
		},
		PHP_INT_MAX
	);

	add_action(
		'perflab_server_timing_send_header',
		static function (): void {
			// WordPress total load time.
			perflab_server_timing_register_metric(
				'total',
				array(
					'measure_callback' => static function ( $metric ): void {
						// The 'timestart' global is set right at the beginning of WordPress execution.
						$metric->set_value( ( microtime( true ) - $GLOBALS['timestart'] ) * 1000.0 );
					},
					'access_cap'       => 'exist',
				)
			);
		}
	);

	// SQL query time is only measured if the SAVEQUERIES constant is set to true.
	if ( defined( 'SAVEQUERIES' ) && SAVEQUERIES ) {
		add_action(
			'perflab_server_timing_send_header',
			static function (): void {
				// WordPress database query time within template.
				perflab_server_timing_register_metric(
					'template-db-queries',
					array(
						'measure_callback' => static function ( $metric ): void {
							// This global should typically be set when this is called, but check just in case.
							if ( ! isset( $GLOBALS['perflab_query_time_before_template'] ) ) {
								return;
							}

							// This should never happen, but some odd database implementations may be doing it wrong.
							if ( ! isset( $GLOBALS['wpdb']->queries ) || ! is_array( $GLOBALS['wpdb']->queries ) ) {
								return;
							}

							$total_query_time = array_reduce(
								$GLOBALS['wpdb']->queries,
								static function ( $acc, $query ) {
									return $acc + $query[1];
								},
								0.0
							);
							$metric->set_value( ( $total_query_time - $GLOBALS['perflab_query_time_before_template'] ) * 1000.0 );
						},
						'access_cap'       => 'exist',
					)
				);
			},
			PHP_INT_MAX
		);
	}
}
add_action( 'wp_loaded', 'perflab_register_default_server_timing_template_metrics' );

/**
 * Registers additional Server-Timing metrics as configured in the setting.
 *
 * These metrics should be registered as soon as possible. They can be added
 * and modified in the "Tools > Server-Timing" screen.
 *
 * @since 2.6.0
 */
function perflab_register_additional_server_timing_metrics_from_setting(): void {
	$options = (array) get_option( PERFLAB_SERVER_TIMING_SETTING, array() );

	$hooks_to_measure = array();

	if ( isset( $options['benchmarking_actions'] ) ) {
		foreach ( $options['benchmarking_actions'] as $action ) {
			$hooks_to_measure[ $action ] = 'action';
		}
	}

	if ( isset( $options['benchmarking_filters'] ) ) {
		foreach ( $options['benchmarking_filters'] as $filter ) {
			$hooks_to_measure[ $filter ] = 'filter';
		}
	}

	// Bail early if there are no hooks to measure.
	if ( count( $hooks_to_measure ) === 0 ) {
		return;
	}

	/*
	 * This logic measures performance of a hook (action or filter).
	 *
	 * Currently, only hooks that run once are properly supported.
	 * For hooks that run multiple times, only the first occurrence will be measured.
	 *
	 * Here is an outline of the logic:
	 *
	 * 1. Use the 'all' hook at the minimum (i.e. earliest) priority possible.
	 * 2. In that callback, check that the hook should be measured and that it has not already been registered yet, and
	 *    if so, register the metric for the hook, with a prefix of either "action" or "filter".
	 * 3. Provide a measuring callback which captures the time span between beginning to end of the hook:
	 *     1. Capture the current time immediately, i.e. within the 'all' hook.
	 *     2. Add another hook callback at the maximum (i.e. latest) priority possible.
	 *     3. In that callback, capture the current time, leading the Server-Timing API to calculate the difference.
	 */
	add_action(
		'all',
		static function ( $hook_name ) use ( $hooks_to_measure ): void {
			if ( ! isset( $hooks_to_measure[ $hook_name ] ) ) {
				return;
			}

			$hook_type   = $hooks_to_measure[ $hook_name ];
			$metric_slug = "{$hook_type}-{$hook_name}";

			if ( perflab_server_timing()->has_registered_metric( $metric_slug ) ) {
				return;
			}

			$measure_callback = static function ( $metric ) use ( $hook_name, $hook_type ): void {
				$metric->measure_before();

				if ( 'action' === $hook_type ) {
					$cb = static function () use ( $metric, $hook_name, &$cb ): void {
						$metric->measure_after();
						remove_action( $hook_name, $cb, PHP_INT_MAX );
					};
					add_action( $hook_name, $cb, PHP_INT_MAX );
				} else {
					$cb = static function ( $passthrough ) use ( $metric, $hook_name, &$cb ) {
						$metric->measure_after();
						remove_filter( $hook_name, $cb, PHP_INT_MAX );
						return $passthrough;
					};
					add_filter( $hook_name, $cb, PHP_INT_MAX );
				}
			};

			perflab_server_timing_register_metric(
				$metric_slug,
				array(
					'measure_callback' => $measure_callback,
					'access_cap'       => 'exist',
				)
			);
		},
		PHP_INT_MIN
	);
}

/*
 * If this file is loaded from the Server-Timing logic in the object-cache.php
 * drop-in, it must not call this function right away since otherwise the cache
 * will not be loaded yet.
 */
if ( 0 === did_action( 'muplugins_loaded' ) ) {
	add_action( 'muplugins_loaded', 'perflab_register_additional_server_timing_metrics_from_setting' );
} else {
	perflab_register_additional_server_timing_metrics_from_setting();
}
