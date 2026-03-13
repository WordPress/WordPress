<?php
/**
 * Server-Timing API integration file
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

define( 'PERFLAB_SERVER_TIMING_SETTING', 'perflab_server_timing_settings' );
define( 'PERFLAB_SERVER_TIMING_SCREEN', 'perflab-server-timing' );

require_once __DIR__ . '/hooks.php';

/**
 * Provides access the Server-Timing API.
 *
 * When called for the first time, this also initializes the API to schedule the header for output.
 * In case that no metrics are registered, this is still called on {@see 'wp_loaded'}, so that even then it still fires
 * its action hooks as expected.
 *
 * @since 1.8.0
 */
function perflab_server_timing(): Perflab_Server_Timing {
	static $server_timing;

	if ( null === $server_timing ) {
		$server_timing = new Perflab_Server_Timing();

		/*
		 * Do not add the hook for Server-Timing header output if it is entirely disabled.
		 * While the constant checks on top of the file prevent this from happening by default, external code could
		 * still call the `perflab_server_timing()` function. It needs to be ensured that such calls do not result in
		 * fatal errors, but they should at least not lead to the header being output.
		 */
		if ( defined( 'PERFLAB_DISABLE_SERVER_TIMING' ) && PERFLAB_DISABLE_SERVER_TIMING ) {
			return $server_timing;
		}

		$server_timing->add_hooks();
	}

	return $server_timing;
}

/**
 * Initializes the Server-Timing API.
 *
 * @since 3.1.0
 */
function perflab_server_timing_init(): void {
	perflab_server_timing();
}

add_action( 'wp_loaded', 'perflab_server_timing_init' );

/**
 * Registers a metric to calculate for the Server-Timing header.
 *
 * This method must be called before the {@see 'perflab_server_timing_send_header'} hook.
 *
 * @since 1.8.0
 *
 * @param string                                                $metric_slug The metric slug.
 * @param array{measure_callback: callable, access_cap: string} $args        {
 *     Arguments for the metric.
 *
 *     @type callable $measure_callback The callback that initiates calculating the metric value. It will receive
 *                                      the Perflab_Server_Timing_Metric instance as a parameter, in order to set
 *                                      the value when it has been calculated. Metric values must be provided in
 *                                      milliseconds.
 *     @type string   $access_cap       Capability required to view the metric. If this is a public metric, this
 *                                      needs to be set to "exist".
 * }
 */
function perflab_server_timing_register_metric( string $metric_slug, array $args ): void {
	perflab_server_timing()->register_metric( $metric_slug, $args );
}

/**
 * Returns whether an output buffer should be used to gather Server-Timing metrics during template rendering.
 *
 * @since 1.8.0
 *
 * @return bool True if an output buffer should be used, false otherwise.
 */
function perflab_server_timing_use_output_buffer(): bool {
	return perflab_server_timing()->use_output_buffer();
}

/**
 * Wraps a callback (e.g. for an action or filter) to be measured and included in the Server-Timing header.
 *
 * @since 1.8.0
 *
 * @param callable $callback    The callback to wrap.
 * @param string   $metric_slug The metric slug to use within the Server-Timing header.
 * @param string   $access_cap  Capability required to view the metric. If this is a public metric, this needs to be
 *                              set to "exist".
 * @return Closure Callback function that will run $callback and measure its execution time once called.
 */
function perflab_wrap_server_timing( callable $callback, string $metric_slug, string $access_cap ): Closure {
	return static function ( ...$callback_args ) use ( $callback, $metric_slug, $access_cap ) {
		// Gain access to Perflab_Server_Timing_Metric instance.
		$server_timing_metric = null;

		// Only register the metric the first time the function is called.
		// For now, this also means only the first function call is measured.
		if ( ! perflab_server_timing()->has_registered_metric( $metric_slug ) ) {
			perflab_server_timing_register_metric(
				$metric_slug,
				array(
					'measure_callback' => static function ( $metric ) use ( &$server_timing_metric ): void {
						$server_timing_metric = $metric;
					},
					'access_cap'       => $access_cap,
				)
			);
		}

		// If metric instance was not set, this metric should not be calculated.
		if ( null === $server_timing_metric ) {
			return call_user_func_array( $callback, $callback_args );
		}

		// Measure time before the callback.
		$server_timing_metric->measure_before();

		// Execute the callback.
		$result = call_user_func_array( $callback, $callback_args );

		// Measure time after the callback and calculate total.
		$server_timing_metric->measure_after();

		// Return result (e.g. in case this is a filter callback).
		return $result;
	};
}

/**
 * Gets default value for server timing setting.
 *
 * @since 3.1.0
 *
 * @return array{benchmarking_actions: string[], benchmarking_filters: string[], output_buffering: bool} Default value.
 */
function perflab_get_server_timing_setting_default_value(): array {
	return array(
		'benchmarking_actions' => array(),
		'benchmarking_filters' => array(),
		'output_buffering'     => false,
	);
}

/**
 * Registers the Server-Timing setting.
 *
 * @since 2.6.0
 */
function perflab_register_server_timing_setting(): void {
	register_setting(
		PERFLAB_SERVER_TIMING_SCREEN,
		PERFLAB_SERVER_TIMING_SETTING,
		array(
			'type'              => 'object',
			'sanitize_callback' => 'perflab_sanitize_server_timing_setting',
			'default'           => perflab_get_server_timing_setting_default_value(),
		)
	);
}
add_action( 'init', 'perflab_register_server_timing_setting' );

/**
 * Sanitizes the Server-Timing setting.
 *
 * @since 2.6.0
 *
 * @param array|mixed $value Server-Timing setting value.
 * @return array{benchmarking_actions: string[], benchmarking_filters: string[], output_buffering: bool} Sanitized Server-Timing setting value.
 */
function perflab_sanitize_server_timing_setting( $value ): array {
	if ( ! is_array( $value ) ) {
		$value = array();
	}
	$value = wp_array_slice_assoc(
		array_merge( perflab_get_server_timing_setting_default_value(), $value ),
		array_keys( perflab_get_server_timing_setting_default_value() )
	);

	/*
	 * Ensure that every element is an indexed array of hook names.
	 * Any duplicates across a group of hooks are removed.
	 */
	foreach ( wp_array_slice_assoc( $value, array( 'benchmarking_actions', 'benchmarking_filters' ) ) as $key => $hooks ) {
		if ( ! is_array( $hooks ) ) {
			$hooks = explode( "\n", $hooks );
		}
		$value[ $key ] = array_values(
			array_unique(
				array_filter(
					array_map(
						static function ( string $hook_name ): string {
							/*
							 * Allow any characters except whitespace.
							 * While most hooks use a limited set of characters, hook names in plugins are not
							 * restricted to them, therefore the sanitization does not limit the characters
							 * used.
							 */
							return (string) preg_replace(
								'/\s/',
								'',
								sanitize_text_field( $hook_name )
							);
						},
						$hooks
					)
				)
			)
		);
	}

	$value['output_buffering'] = (bool) $value['output_buffering'];

	/**
	 * Validated value.
	 *
	 * @var array{benchmarking_actions: string[], benchmarking_filters: string[], output_buffering: bool} $value
	 */
	return $value;
}
