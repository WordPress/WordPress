<?php
/**
 * Cron command extensions for asynchronous cron processing.
 */

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

/**
 * Handles asynchronous cron queue operations.
 */
class Cron_Async_Command extends WP_CLI_Command {

	/**
	* Processes queued cron events.
	*
	* ## OPTIONS
	*
	* [--limit=<number>]
	* : Maximum number of events to process. Defaults to processing the entire queue.
	*
	* ## EXAMPLES
	*
	*     $ wp cron process-queue --limit=10
	*     Success: Processed 3 queued cron events.
	*
	* @since 6.5.0
	*
	* @param array $args       Positional arguments.
	* @param array $assoc_args Associative arguments.
	*/
	public function process_queue( $args, $assoc_args ) {
		if ( ! wp_is_async_cron_enabled() ) {
			WP_CLI::error( __( 'Asynchronous cron mode is not enabled.' ) );
		}

		$limit = 0;
		if ( isset( $assoc_args['limit'] ) ) {
			$limit = (int) $assoc_args['limit'];

			if ( $limit < 0 ) {
				WP_CLI::error( __( 'Limit must be zero or a positive integer.' ) );
			}
		}

		$processed = wp_process_async_cron_queue( $limit );

		if ( $processed ) {
			WP_CLI::success(
				sprintf(
					_n(
						'Processed %d queued cron event.',
						'Processed %d queued cron events.',
						$processed
					),
					$processed
				)
			);

			return;
		}

		WP_CLI::success( __( 'No queued cron events were processed.' ) );
	}
}

WP_CLI::add_command( 'cron process-queue', 'Cron_Async_Command' );
