<?php
/**
 * Helper functions used for Autoloaded Options Health Check.
 *
 * @package performance-lab
 * @since 2.1.0
 */

// @codeCoverageIgnoreStart
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
// @codeCoverageIgnoreEnd

/**
 * Callback for autoloaded_options test.
 *
 * @since 1.0.0
 *
 * @return array{label: string, status: string, badge: array{label: string, color: string}, description: string, actions: string, test: string} Result.
 */
function perflab_aao_autoloaded_options_test(): array {

	$autoloaded_options_size  = perflab_aao_autoloaded_options_size();
	$autoloaded_options_count = count( wp_load_alloptions() );

	$base_description = __( 'Autoloaded options are configuration settings for plugins and themes that are automatically loaded with every page load in WordPress. Having too many autoloaded options can slow down your site.', 'performance-lab' );

	$result = array(
		'label'       => __( 'Autoloaded options are acceptable', 'performance-lab' ),
		'status'      => 'good',
		'badge'       => array(
			'label' => __( 'Performance', 'performance-lab' ),
			'color' => 'blue',
		),
		'description' => sprintf(
			/* translators: 1. Number of autoloaded options. 2. Autoloaded options size. */
			'<p>' . esc_html( $base_description ) . ' ' . __( 'Your site has %1$s autoloaded options (size: %2$s) in the options table, which is acceptable.', 'performance-lab' ) . '</p>',
			$autoloaded_options_count,
			size_format( $autoloaded_options_size )
		),
		'actions'     => '',
		'test'        => 'autoloaded_options',
	);

	/**
	 * Filters max bytes threshold to trigger warning in Site Health.
	 *
	 * @since 1.0.0
	 *
	 * @param int $limit Autoloaded options threshold size. Default 800000.
	 */
	$limit = apply_filters( 'perflab_aao_autoloaded_options_limit_size_in_bytes', 800000 );

	if ( $autoloaded_options_size < $limit ) {
		return $result;
	}

	$result['status']      = 'critical';
	$result['label']       = __( 'Autoloaded options could affect performance', 'performance-lab' );
	$result['description'] = sprintf(
		/* translators: 1. Number of autoloaded options. 2. Autoloaded options size. */
		'<p>' . esc_html( $base_description ) . ' ' . __( 'Your site has %1$s autoloaded options (size: %2$s) in the options table, which could cause your site to be slow. You can reduce the number of autoloaded options by cleaning up your site\'s options table.', 'performance-lab' ) . '</p>',
		$autoloaded_options_count,
		size_format( $autoloaded_options_size )
	) . perflab_aao_get_autoloaded_options_table() . perflab_aao_get_disabled_autoloaded_options_table();

	/**
	 * Filters description to be shown on Site Health warning when threshold is met.
	 *
	 * @since 1.0.0
	 *
	 * @param string $description Description message when autoloaded options bigger than threshold.
	 */
	$result['description'] = apply_filters( 'perflab_aao_autoloaded_options_limit_description', $result['description'] );

	$result['actions'] = sprintf(
		/* translators: 1: HelpHub URL. 2: Link description. */
		'<p><a target="_blank" href="%1$s">%2$s</a></p>',
		esc_url( __( 'https://wordpress.org/support/article/optimization/#autoloaded-options', 'performance-lab' ) ),
		__( 'More info about performance optimization', 'performance-lab' )
	);

	/**
	 * Filters actionable information to tackle the problem. It can be a link to an external guide.
	 *
	 * @since 1.0.0
	 *
	 * @param string $actions Call to Action to be used to point to the right direction to solve the issue.
	 */
	$result['actions'] = apply_filters( 'perflab_aao_autoloaded_options_action_to_perform', $result['actions'] );
	return $result;
}

/**
 * Calculate total amount of autoloaded data.
 *
 * @since 1.0.0
 *
 * @return int autoloaded data in bytes.
 */
function perflab_aao_autoloaded_options_size(): int {
	/**
	 * External object cache plugins may return mixed values including arrays and objects instead of them being serialized.
	 *
	 * @var array<string, string|array<int|string, mixed>|object> $all_options
	 */
	$all_options = wp_load_alloptions();

	$total_length = 0;

	foreach ( $all_options as $option_value ) {
		if ( is_array( $option_value ) || is_object( $option_value ) ) {
			$option_value = serialize( $option_value ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
		}
		$total_length += strlen( (string) $option_value );
	}

	return $total_length;
}

/**
 * Fetches autoload top list.
 *
 * @since 1.5.0
 *
 * @return array<object{option_name: string, option_value_length: int}> Autoloaded data as option names and their sizes.
 */
function perflab_aao_query_autoloaded_options(): array {

	/**
	 * Filters the threshold for an autoloaded option to be considered large.
	 *
	 * The Site Health report will show users a notice if any of their autoloaded
	 * options exceed the threshold for being considered large. This filters the value
	 * for what is considered a large option.
	 *
	 * @since 1.5.0
	 *
	 * @param int $option_threshold Threshold for an option's value to be considered
	 *                              large, in bytes. Default 100.
	 */
	$option_threshold = apply_filters( 'perflab_aao_autoloaded_options_table_threshold', 100 );

	$all_options = wp_load_alloptions();

	$large_options = array();

	foreach ( $all_options as $option_name => $option_value ) {
		if ( is_array( $option_value ) || is_object( $option_value ) ) {
			$option_value = serialize( $option_value ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
		}
		if ( strlen( $option_value ) > $option_threshold ) {
			$large_options[] = (object) array(
				'option_name'         => $option_name,
				'option_value_length' => strlen( $option_value ),
			);
		}
	}

	usort(
		$large_options,
		static function ( $a, $b ) {
			return $b->option_value_length - $a->option_value_length;
		}
	);

	return array_slice( $large_options, 0, 20 );
}

/**
 * Gets formatted autoload options table.
 *
 * @since 1.5.0
 *
 * @return string HTML formatted table.
 */
function perflab_aao_get_autoloaded_options_table(): string {
	$autoload_summary = perflab_aao_query_autoloaded_options();

	$html_table = sprintf(
		'<table class="widefat striped"><thead><tr><th scope="col">%s</th><th scope="col">%s</th><th scope="col">%s</th></tr></thead><tbody>',
		esc_html__( 'Option Name', 'performance-lab' ),
		esc_html__( 'Size', 'performance-lab' ),
		esc_html__( 'Action', 'performance-lab' )
	);

	$nonce = wp_create_nonce( 'perflab_aao_update_autoload' );
	foreach ( $autoload_summary as $value ) {
		$url            = esc_url_raw(
			add_query_arg(
				array(
					'action'      => 'perflab_aao_update_autoload',
					'_wpnonce'    => $nonce,
					'option_name' => $value->option_name,
					'autoload'    => 'false',
				),
				admin_url( 'site-health.php' )
			)
		);
		$disable_button = sprintf( '<a class="button" href="%s">%s</a>', esc_url( $url ), esc_html__( 'Disable Autoload', 'performance-lab' ) );
		$html_table    .= sprintf( '<tr><td>%s</td><td>%s</td><td>%s</td></tr>', esc_html( $value->option_name ), size_format( $value->option_value_length, 2 ), $disable_button );
	}
	$html_table .= '</tbody></table>';

	return $html_table;
}

/**
 * Gets disabled autoload options table.
 *
 * @since 3.0.0
 *
 * @return string HTML formatted table.
 */
function perflab_aao_get_disabled_autoloaded_options_table(): string {
	$disabled_options = get_option( 'perflab_aao_disabled_options', array() );

	if ( ! is_array( $disabled_options ) ) {
		return '';
	}

	$disabled_options_summary = array();
	wp_prime_option_caches( $disabled_options );

	foreach ( $disabled_options as $option_name ) {
		if ( ! is_string( $option_name ) ) {
			continue;
		}
		$option_value = get_option( $option_name );

		if ( false !== $option_value ) {
			$option_length                            = strlen( maybe_serialize( $option_value ) );
			$disabled_options_summary[ $option_name ] = $option_length;
		}
	}

	if ( count( $disabled_options_summary ) === 0 ) {
		return '';
	}

	arsort( $disabled_options_summary );

	$html_table = sprintf(
		'<p>%s</p><table class="widefat striped"><thead><tr><th scope="col">%s</th><th scope="col">%s</th><th scope="col">%s</th></tr></thead><tbody>',
		__( 'The following table shows the options for which you have previously disabled Autoload.', 'performance-lab' ),
		esc_html__( 'Option Name', 'performance-lab' ),
		esc_html__( 'Size', 'performance-lab' ),
		esc_html__( 'Action', 'performance-lab' )
	);

	$nonce = wp_create_nonce( 'perflab_aao_update_autoload' );

	foreach ( $disabled_options_summary as $option_name => $option_length ) {
		$url            = esc_url_raw(
			add_query_arg(
				array(
					'action'      => 'perflab_aao_update_autoload',
					'_wpnonce'    => $nonce,
					'option_name' => $option_name,
					'autoload'    => 'true',
				),
				admin_url( 'site-health.php' )
			)
		);
		$disable_button = sprintf( '<a class="button" href="%s">%s</a>', esc_url( $url ), esc_html__( 'Revert to Autoload', 'performance-lab' ) );
		$html_table    .= sprintf( '<tr><td>%s</td><td>%s</td><td>%s</td></tr>', esc_html( $option_name ), size_format( $option_length, 2 ), $disable_button );
	}

	$html_table .= '</tbody></table>';

	return $html_table;
}

/**
 * Gets the autoload values in the database that should trigger their option to be autoloaded.
 *
 * @since 3.0.0
 *
 * @return string[] List of autoload values.
 */
function perflab_aao_get_autoload_values_to_autoload(): array {
	if ( function_exists( 'wp_autoload_values_to_autoload' ) ) {
		return wp_autoload_values_to_autoload();
	}

	return array( 'yes' );
}
