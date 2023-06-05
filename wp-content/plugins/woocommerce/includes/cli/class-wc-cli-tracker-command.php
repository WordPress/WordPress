<?php
/**
 * WC_CLI_Tracker_Command class file.
 *
 * @package WooCommerce\CLI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allows access to tracker snapshot for transparency and debugging.
 *
 * @since 5.5.0
 * @package WooCommerce
 */
class WC_CLI_Tracker_Command {

	/**
	 * Registers a command for showing WooCommerce Tracker snapshot data.
	 */
	public static function register_commands() {
		WP_CLI::add_command( 'wc tracker snapshot', array( 'WC_CLI_Tracker_Command', 'show_tracker_snapshot' ) );
	}

	/**
	 * Dump tracker snapshot data to screen.
	 *
	 * ## EXAMPLES
	 *
	 * wp wc tracker snapshot --format=yaml
	 * wp wc tracker snapshot --format=json
	 *
	 * ## OPTIONS
	 *
	 * [--format=<format>]
	 * : Render output in a particular format, see WP_CLI\Formatter for details.
	 *
	 * @see \WP_CLI\Formatter
	 * @see WC_Tracker::get_tracking_data()
	 * @param array $args WP-CLI positional arguments.
	 * @param array $assoc_args WP-CLI associative arguments.
	 */
	public static function show_tracker_snapshot( $args, $assoc_args ) {
		$snapshot_data = WC_Tracker::get_tracking_data();

		$formatter = new \WP_CLI\Formatter(
			$assoc_args,
			array_keys( $snapshot_data )
		);

		$formatter->display_items( array( $snapshot_data ) );
	}
}
