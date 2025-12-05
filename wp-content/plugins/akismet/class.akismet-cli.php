<?php

WP_CLI::add_command( 'akismet', 'Akismet_CLI' );

/**
 * Filter spam comments.
 */
class Akismet_CLI extends WP_CLI_Command {
	/**
	 * Checks one or more comments against the Akismet API.
	 *
	 * ## OPTIONS
	 * <comment_id>...
	 * : The ID(s) of the comment(s) to check.
	 *
	 * [--noaction]
	 * : Don't change the status of the comment. Just report what Akismet thinks it is.
	 *
	 * ## EXAMPLES
	 *
	 *     wp akismet check 12345
	 *
	 * @alias comment-check
	 */
	public function check( $args, $assoc_args ) {
		foreach ( $args as $comment_id ) {
			if ( isset( $assoc_args['noaction'] ) ) {
				// Check the comment, but don't reclassify it.
				$api_response = Akismet::check_db_comment( $comment_id, 'wp-cli' );
			} else {
				$api_response = Akismet::recheck_comment( $comment_id, 'wp-cli' );
			}

			if ( 'true' === $api_response ) {
				/* translators: %d: Comment ID. */
				WP_CLI::line( sprintf( __( 'Comment #%d is spam.', 'akismet' ), $comment_id ) );
			} elseif ( 'false' === $api_response ) {
				/* translators: %d: Comment ID. */
				WP_CLI::line( sprintf( __( 'Comment #%d is not spam.', 'akismet' ), $comment_id ) );
			} elseif ( false === $api_response ) {
				/* translators: %d: Comment ID. */
				WP_CLI::error( __( 'Failed to connect to Akismet.', 'akismet' ) );
			} elseif ( is_wp_error( $api_response ) ) {
				/* translators: %d: Comment ID. */
				WP_CLI::warning( sprintf( __( 'Comment #%d could not be checked.', 'akismet' ), $comment_id ) );
			}
		}
	}

	/**
	 * Recheck all comments in the Pending queue.
	 *
	 * ## EXAMPLES
	 *
	 *     wp akismet recheck_queue
	 *
	 * @alias recheck-queue
	 */
	public function recheck_queue() {
		$batch_size = 100;
		$start      = 0;

		$total_counts = array();

		do {
			$result_counts = Akismet_Admin::recheck_queue_portion( $start, $batch_size );

			if ( $result_counts['processed'] > 0 ) {
				foreach ( $result_counts as $key => $count ) {
					if ( ! isset( $total_counts[ $key ] ) ) {
						$total_counts[ $key ] = $count;
					} else {
						$total_counts[ $key ] += $count;
					}
				}
				$start += $batch_size;
				$start -= $result_counts['spam']; // These comments will have been removed from the queue.
			}
		} while ( $result_counts['processed'] > 0 );

		/* translators: %d: Number of comments. */
		WP_CLI::line( sprintf( _n( 'Processed %d comment.', 'Processed %d comments.', $total_counts['processed'], 'akismet' ), number_format( $total_counts['processed'] ) ) );

		/* translators: %d: Number of comments. */
		WP_CLI::line( sprintf( _n( '%d comment moved to Spam.', '%d comments moved to Spam.', $total_counts['spam'], 'akismet' ), number_format( $total_counts['spam'] ) ) );

		if ( $total_counts['error'] ) {
			/* translators: %d: Number of comments. */
			WP_CLI::line( sprintf( _n( '%d comment could not be checked.', '%d comments could not be checked.', $total_counts['error'], 'akismet' ), number_format( $total_counts['error'] ) ) );
		}
	}

	/**
	 * Fetches stats from the Akismet API.
	 *
	 * ## OPTIONS
	 *
	 * [<interval>]
	 * : The time period for which to retrieve stats.
	 * ---
	 * default: all
	 * options:
	 *  - days
	 *  - months
	 *  - all
	 * ---
	 *
	 * [--format=<format>]
	 * : Allows overriding the output of the command when listing connections.
	 * ---
	 * default: table
	 * options:
	 *  - table
	 *  - json
	 *  - csv
	 *  - yaml
	 *  - count
	 * ---
	 *
	 * [--summary]
	 * : When set, will display a summary of the stats.
	 *
	 * ## EXAMPLES
	 *
	 * wp akismet stats
	 * wp akismet stats all
	 * wp akismet stats days
	 * wp akismet stats months
	 * wp akismet stats all --summary
	 */
	public function stats( $args, $assoc_args ) {
		$api_key = Akismet::get_api_key();

		if ( empty( $api_key ) ) {
			WP_CLI::error( __( 'API key must be set to fetch stats.', 'akismet' ) );
		}

		switch ( $args[0] ) {
			case 'days':
				$interval = '60-days';
				break;
			case 'months':
				$interval = '6-months';
				break;
			default:
				$interval = 'all';
				break;
		}

		$request_args = array(
			'blog' => get_option( 'home' ),
			'key'  => $api_key,
			'from' => $interval,
		);

		$request_args = apply_filters( 'akismet_request_args', $request_args, 'get-stats' );

		$response = Akismet::http_post( Akismet::build_query( $request_args ), 'get-stats' );

		if ( empty( $response[1] ) ) {
			WP_CLI::error( __( 'Currently unable to fetch stats. Please try again.', 'akismet' ) );
		}

		$response_body = json_decode( $response[1], true );

		if ( is_null( $response_body ) ) {
			WP_CLI::error( __( 'Stats response could not be decoded.', 'akismet' ) );
		}

		if ( isset( $assoc_args['summary'] ) ) {
			$keys = array(
				'spam',
				'ham',
				'missed_spam',
				'false_positives',
				'accuracy',
				'time_saved',
			);

			WP_CLI\Utils\format_items( $assoc_args['format'], array( $response_body ), $keys );
		} else {
			$stats = $response_body['breakdown'];
			WP_CLI\Utils\format_items( $assoc_args['format'], $stats, array_keys( end( $stats ) ) );
		}
	}
}
