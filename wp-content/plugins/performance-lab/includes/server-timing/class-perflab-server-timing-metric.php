<?php
/**
 * Server-Timing API: Perflab_Server_Timing_Metric class
 *
 * @package performance-lab
 * @since 1.8.0
 */

/**
 * Class representing a single Server-Timing metric.
 *
 * @since 1.8.0
 */
class Perflab_Server_Timing_Metric {

	/**
	 * The metric slug.
	 *
	 * @since 1.8.0
	 * @var string
	 */
	private $slug;

	/**
	 * The metric value in milliseconds.
	 *
	 * @since 1.8.0
	 * @var int|float|null
	 */
	private $value;

	/**
	 * The value measured before relevant execution logic in seconds, if used.
	 *
	 * @since 1.8.0
	 * @var float|null
	 */
	private $before_value;

	/**
	 * Constructor.
	 *
	 * @since 1.8.0
	 *
	 * @param string $slug The metric slug.
	 */
	public function __construct( string $slug ) {
		$this->slug = $slug;
	}

	/**
	 * Gets the metric slug.
	 *
	 * @since 1.8.0
	 *
	 * @return string The metric slug.
	 */
	public function get_slug(): string {
		return $this->slug;
	}

	/**
	 * Sets the metric value.
	 *
	 * Alternatively to setting the metric value directly, the {@see Perflab_Server_Timing_Metric::measure_before()}
	 * and {@see Perflab_Server_Timing_Metric::measure_after()} methods can be used to further simplify measuring.
	 *
	 * @since 1.8.0
	 *
	 * @param int|float|mixed $value The metric value to set, in milliseconds.
	 */
	public function set_value( $value ): void {
		if ( ! is_numeric( $value ) ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: %s: PHP parameter name */
				sprintf( esc_html__( 'The %s parameter must be an integer, float, or numeric string.', 'performance-lab' ), '$value' ),
				''
			);
			return;
		}

		if ( 0 !== did_action( 'perflab_server_timing_send_header' ) && ! doing_action( 'perflab_server_timing_send_header' ) ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: %s: WordPress action name */
				sprintf( esc_html__( 'The method must be called before or during the %s action.', 'performance-lab' ), 'perflab_server_timing_send_header' ),
				''
			);
			return;
		}

		// In case e.g. a numeric string is passed, cast it.
		if ( ! is_int( $value ) && ! is_float( $value ) ) {
			$value = (float) $value;
		}

		$this->value = $value;
	}

	/**
	 * Gets the metric value.
	 *
	 * @since 1.8.0
	 *
	 * @return int|float|null The metric value, or null if none set.
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * Captures the current time, as a reference point to calculate the duration of a task afterward.
	 *
	 * This should be used in combination with {@see Perflab_Server_Timing_Metric::measure_after()}. Alternatively,
	 * {@see Perflab_Server_Timing_Metric::set_value()} can be used to set a calculated value manually.
	 *
	 * @since 1.8.0
	 */
	public function measure_before(): void {
		$this->before_value = microtime( true );
	}

	/**
	 * Captures the current time and compares it to the reference point to calculate a task's duration.
	 *
	 * This should be used in combination with {@see Perflab_Server_Timing_Metric::measure_before()}. Alternatively,
	 * {@see Perflab_Server_Timing_Metric::set_value()} can be used to set a calculated value manually.
	 *
	 * @since 1.8.0
	 */
	public function measure_after(): void {
		if ( null === $this->before_value ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: %s: PHP method name */
				sprintf( esc_html__( 'The %s method must be called before.', 'performance-lab' ), __CLASS__ . '::measure_before()' ),
				''
			);
			return;
		}

		$this->set_value( ( microtime( true ) - $this->before_value ) * 1000.0 );
	}
}
