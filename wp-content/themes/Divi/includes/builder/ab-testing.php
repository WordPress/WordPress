<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( -1 );
}

if ( ! defined( 'ET_PB_AB_DB_VERSION' ) ) {
	define( 'ET_PB_AB_DB_VERSION', 1.0 );
}

/**
 * AJAX endpoint for builder data
 * @return void
 */
function et_pb_ab_builder_data() {
	$defaults = array(
		'et_pb_ab_nonce'    => false,
		'et_pb_ab_test_id'  => false,
		'et_pb_ab_duration' => 'week',
	);

	$post = wp_parse_args( $_POST, $defaults );

	// Verify nonce
	if ( ! wp_verify_nonce( $post['et_pb_ab_nonce'], 'ab_testing_builder_nonce' ) ) {
		die( -1 );
	}

	// Verify user permission
	if ( ! current_user_can( 'edit_posts' ) || ! et_pb_is_allowed( 'ab_testing' ) ) {
		die( -1 );
	}

	// Whitelist the duration value
	$duration = in_array( $post['et_pb_ab_duration'], et_pb_ab_get_stats_data_duration() ) ? $post['et_pb_ab_duration'] : $defaults['et_pb_ab_duration'];

	// Get data
	$output = et_pb_ab_get_stats_data( intval( $post['et_pb_ab_test_id'] ), $duration );

	// Print output
	die( json_encode( $output ) );
}
add_action( 'wp_ajax_et_pb_ab_builder_data', 'et_pb_ab_builder_data' );

/**
 * Get Split testing subject ranking data
 * @return array
 */
function et_pb_ab_get_saved_subjects_ranks( $post_id ) {
	global $post;

	// Make sure that there are $post_id
	if ( ! isset( $post_id ) && isset( $post->ID ) ) {
		$post_id = $post->ID;
	}

	// Get list of subjects
	$subject_list = get_post_meta( $post_id, '_et_pb_ab_subjects', true );
	$subjects_ids = explode( ',', $subject_list );
	$subjects     = array();
	$goal_slug    = et_pb_ab_get_goal_module( $post_id );
	$rank_metrics = in_array( $goal_slug, et_pb_ab_get_modules_have_conversions() ) ? 'conversions' : 'clicks';

	if ( ! empty( $subjects_ids ) ) {
		// Get conversion rate data
		$subjects_ranks = et_pb_ab_get_subjects_ranks( $post_id, $rank_metrics, 'all' );

		// Sort from high to low and mantain key association
		arsort( $subjects_ranks );

		// Loop saved subject ids
		foreach ( $subjects_ids as $subject_id ) {
			$subject_key = 'subject_' . $subject_id;
			$subject_rank = isset( $subjects_ranks[ $subject_key ] ) ? array_search( $subjects_ranks[ $subject_key ], array_values( $subjects_ranks ) ) + 1 : false;

			// Check whether current subject has saved conversion rate data or not
			if ( $subject_rank ) {
				$subjects[ $subject_key ] = array(
					'percentage' => esc_html( $subjects_ranks[ $subject_key ] . '%' ),
					'rank'       => esc_attr( $subject_rank ),
				);
			}
		}
	}

	return $subjects;
}

/**
 * Define ranking-based subject color
 * @return array
 */
function et_pb_ab_get_subject_rank_colors() {
	return array_map( 'et_sanitize_alpha_color', apply_filters( 'et_pb_ab_get_subject_rank_colors', array(
		'#F3CB57',
		'#F8B852',
		'#F8A653',
		'#F88F55',
		'#F87356',
		'#F95A57',
		'#EA5552',
		'#DB514F',
		'#CE4441',
		'#BF2F2C',
		'#AA201C',
		'#920E08',
		'#7E0000',
	) ) );
}

/**
 * Print Split testing subject-ranking color scheme
 *
 * @return string inline CSS styling for subject rank
 */
function et_pb_ab_get_subject_rank_colors_style() {
	$style  = '';
	$colors = et_pb_ab_get_subject_rank_colors();
	$index  = 1;

	foreach ( $colors as $color ) {
		$style .= sprintf(
			'.et_pb_ab_subject.rank-%1$s .et_pb_module_block,
			.et_pb_ab_subject.rank-%1$s.et_pb_section .et-pb-controls,
			.et_pb_ab_subject.rank-%1$s.et_pb_row .et-pb-controls,
			.et_pb_ab_subject.rank-%1$s.et_pb_module_block {
				background: %2$s;
			}',
			esc_html( $index ),
			esc_html( $color )
		);
		$index++;
	}

	return $style;
}

/**
 * Get subjects' ranks
 *
 * @param int    post ID
 * @param string ranking basis. This can be any value on data's subjects_totals
 *               view_page|read_page|view_goal|read_goal|click_goal|con_goal|clicks|reads|bounces|engagements|conversions
 * @param string duration of the data that is used
 * @return array key = `subject_` + subject_id as key and the value as value, sorted in ascending
 */
function et_pb_ab_get_subjects_ranks( $post_id, $ranking_basis = 'engagements', $duration = 'week' ) {
	$data = et_pb_ab_get_stats_data( $post_id, $duration );
	$subjects = et_pb_ab_get_subjects( $post_id, 'array', 'subject_' );

	if ( isset( $data['subjects_totals'] ) && ! empty( $data['subjects_totals'] ) && ! empty( $subjects ) ) {
		// Pluck data
		$ranks = wp_list_pluck( $data['subjects_totals'], $ranking_basis );

		// Remove inactive subjects from ranks
		foreach ( $ranks as $rank_key => $rank_value ) {
			if ( ! in_array( $rank_key, $subjects ) ) {
				unset( $ranks[ $rank_key ] );
			}
		}

		// Sort rank
		arsort( $ranks );
	} else {
		$ranks = array();
	}

	return $ranks;
}

/**
 * Get formatted stats data that is used by builder's Split testing stats
 *
 * @param int    post ID
 * @param string day|week|month|all duration of stats
 * @param string has to be in Y-m-d H:i:s format
 * @return array stats data
 */
function et_pb_ab_get_stats_data( $post_id, $duration = 'week', $time = false, $force_update = false ) {
	global $wpdb;

	$post_id = intval( $post_id );
	$goal_slug    = et_pb_ab_get_goal_module( $post_id );
	$rank_metrics = in_array( $goal_slug, et_pb_ab_get_modules_have_conversions() ) ? 'conversions' : 'clicks';

	// Get subjects
	$subjects    = et_pb_ab_get_subjects( $post_id, 'array', 'subject_' );
	$subjects_id = et_pb_ab_get_subjects( $post_id, 'array' );

	// Get cached data
	$cached_data = get_transient( 'et_pb_ab_' . $post_id . '_stats_' . $duration );

	// Get rank coloring scheme
	$subject_rank_colors = et_pb_ab_get_subject_rank_colors();

	// return cached logs if exist and if force_update == false
	if ( $cached_data && ! $force_update ) {
		// Remove inactive subjects
		if ( isset( $cached_data['subjects_id'] ) && ! empty( $cached_data['subjects_id'] ) ) {
			foreach ( $cached_data['subjects_id'] as $subject_id_key => $subject_id_value ) {
				if ( ! in_array( $subject_id_value, $subjects_id ) ) {
					unset( $cached_data['subjects_id'][ $subject_id_key ] );
				}
			}
		}

		if ( isset( $cached_data['subjects_logs'] ) && ! empty( $cached_data['subjects_logs'] ) ) {
			foreach ( $cached_data['subjects_logs'] as $subject_log_id => $subject_logs ) {
				if ( ! in_array( $subject_log_id, $subjects ) ) {
					unset( $cached_data['subjects_logs'][ $subject_log_id ] );
				}
			}
		}

		if ( isset( $cached_data['subjects_analysis'] ) && ! empty( $cached_data['subjects_analysis'] ) ) {
			foreach ( $cached_data['subjects_analysis'] as $subject_analysis_id => $subject_analysis ) {
				if ( ! in_array( $subject_analysis_id, $subjects ) ) {
					unset( $cached_data['subjects_analysis'][ $subject_analysis_id ] );
				}
			}
		}

		if ( isset( $cached_data['subjects_totals'] ) && ! empty( $cached_data['subjects_totals'] ) ) {
			$subject_totals_index = 0;
			foreach ( $cached_data['subjects_totals'] as $subject_total_id => $subject_totals ) {
				if ( ! in_array( $subject_total_id, $subjects ) ) {
					unset( $cached_data['subjects_totals'][ $subject_total_id ] );
					continue;
				}
			}

			// Rank by engagement
			$cached_subjects_ranks = wp_list_pluck( $cached_data['subjects_totals'], $rank_metrics );
			$cached_subjects_ranks_index = 0;

			// Sort from high to low, mantain keys
			arsort( $cached_subjects_ranks );

			// Push color data
			foreach ( $cached_subjects_ranks as $subject_rank_id => $subject_rank_value ) {
				$cached_data['subjects_totals'][ $subject_rank_id ]['color'] = isset( $subject_rank_colors[ $cached_subjects_ranks_index ] ) ? $subject_rank_colors[ $cached_subjects_ranks_index ] : '#7E0000';

				$cached_subjects_ranks_index++;
			}
		}

		return $cached_data;
	}

	$table_name = $wpdb->prefix . 'et_divi_ab_testing_stats';

	// do nothing if no stats table exists in current WP
	if ( ! $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) {
		return false;
	}

	// Main placeholder
	$event_types       = et_pb_ab_get_event_types();
	$analysis_types    = et_pb_ab_get_analysis_types();
	$analysis_formulas = et_pb_ab_get_analysis_formulas();
	$time              = $time ? $time : date( 'Y-m-d H:i:s', current_time( 'timestamp' ) );
	$stats             = array(
		'subjects_id'       => $subjects_id,
		'subjects_logs'     => array(),
		'subjects_analysis' => array(),
		'subjects_totals'   => array(),
		'events_totals'     => array(),
		'dates'             => array(),
	);

	// Get all logs in test
	switch ( $duration ) {
		case 'all':
			$date_range_interval = 'week';
			$query = $wpdb->prepare(
				"SELECT subject_id, event, YEARWEEK(record_date) AS 'date', COUNT(id) AS 'count' FROM {$table_name} WHERE test_id = %d GROUP BY subject_id, YEARWEEK(record_date), event",
				$post_id
			);
			break;

		case 'month':
			$date_range_interval = 'day';
			$query = $wpdb->prepare(
				"SELECT subject_id, event, DATE(record_date) AS 'date', COUNT(id) AS 'count' FROM {$table_name} WHERE test_id = %d AND record_date <= %s AND record_date > DATE_SUB( %s, INTERVAL 1 MONTH ) GROUP BY subject_id, DAYOFMONTH(record_date), event",
				$post_id,
				$time,
				$time
			);
			break;

		case 'day':
			$date_range_interval = 'hour';
			$query = $wpdb->prepare(
				"SELECT subject_id, event, DATE_FORMAT(record_date, %s) AS 'date', COUNT(id) AS 'count' FROM {$table_name} WHERE test_id = %d AND record_date <= %s AND record_date > DATE_SUB( %s, INTERVAL 1 DAY ) GROUP BY subject_id, HOUR(record_date), event",
				'%Y-%m-%d %H:00',
				$post_id,
				$time,
				$time
			);
			break;

		default:
			$date_range_interval = 'day';
			$query = $wpdb->prepare(
				"SELECT subject_id, event, DATE(record_date) AS 'date', COUNT(id) AS 'count' FROM {$table_name} WHERE test_id = %d AND record_date <= %s AND record_date > DATE_SUB( %s, INTERVAL 1 WEEK ) GROUP BY subject_id, DAYOFMONTH(record_date), event",
				$post_id,
				$time,
				$time
			);
			break;
	}

	$results = $wpdb->get_results( $query );
	if ( ! empty( $results ) ) {
		// Get min and max timestamp based on query result
		$min_max_date = et_pb_ab_get_min_max_timestamp( $results, $date_range_interval );

		// Create default list
		$date_list = et_pb_ab_get_date_range( $min_max_date['min'], $min_max_date['max'], $date_range_interval );

		// Insert date list to main placeholder
		$stats['dates'] = $date_list;

		// Format YYYYWW format on all-time stats into human-readable format (M jS)
		foreach ( $stats['dates'] as $date_key => $date_time ) {
			if ( 'all' === $duration ) {
				$stats['dates'][ $date_key ] = date( 'M jS', strtotime( substr( $date_time, 0, 4 ) . 'W' . substr( $date_time, 4, 2 ) ) );
			} else if ( 'day' === $duration ) {
				$stats['dates'][ $date_key ] = date( 'H:i', strtotime( $date_time ) );
			} else {
				$stats['dates'][ $date_key ] = date( 'M jS', strtotime( $date_time ) );
			}
		}

		// Fill subject logs placeholder with proper default
		$stats['subjects_logs'] = array_fill_keys(
			$subjects,
			array_fill_keys(
				$event_types,
				array_fill_keys(
					$date_list,
					0
				)
			)
		);

		// Loop query result and place into placeholder
		foreach ( $results as $log ) {
			if ( ! in_array( $log->subject_id, $subjects_id ) ) {
				continue;
			}

			$stats['subjects_logs'][ "subject_{$log->subject_id}" ][ $log->event ][ $log->date ] = $log->count;
		}

		// Determine logs' totals and run analysis
		foreach ( $stats['subjects_logs'] as $subject_log_id => $subject_log ) {

			// Push stats total data
			foreach ( $subject_log as $log_type => $logs ) {
				$stats['subjects_totals'][ $subject_log_id ][ $log_type ] = array_sum( $logs );
			}

			// Run analysis for stats' total data
			foreach ( $analysis_types as $analysis_type ) {
				$numerator_event   = $analysis_formulas[ $analysis_type  ]['numerator'];
				$denominator_event = $analysis_formulas[ $analysis_type  ]['denominator'];
				$numerator         = isset( $stats['subjects_totals'][ $subject_log_id ][ $numerator_event ] ) ? $stats['subjects_totals'][ $subject_log_id ][ $numerator_event ] : 0;
				$denominator       = isset( $stats['subjects_totals'][ $subject_log_id ][ $denominator_event ] ) ? $stats['subjects_totals'][ $subject_log_id ][ $denominator_event ] : 0;
				$analysis          = $denominator === 0 ? 0 : floatval( number_format( ( $numerator / $denominator ) * 100, 2 ) );

				if ( $analysis_formulas[ $analysis_type ]['inverse'] ) {
					$analysis = 100 - $analysis;
				}

				$stats['subjects_totals'][ $subject_log_id ][ $analysis_type ] = $analysis;
			}

			// Run analysis for each log date
			foreach ( $date_list as $log_date ) {

				// Run analysis per analysis type
				foreach ( $analysis_types as $analysis_type ) {
					$numerator_event   = $analysis_formulas[ $analysis_type  ]['numerator'];
					$denominator_event = $analysis_formulas[ $analysis_type  ]['denominator'];
					$numerator         = isset( $stats['subjects_logs'][ $subject_log_id ][ $numerator_event ][ $log_date ] ) ? intval( $stats['subjects_logs'][ $subject_log_id ][ $numerator_event ][ $log_date ] ) : 0;
					$denominator       = isset( $stats['subjects_logs'][ $subject_log_id ][ $denominator_event ][ $log_date ] ) ? intval( $stats['subjects_logs'][ $subject_log_id ][ $denominator_event ][ $log_date ] ) : 0;
					$analysis          = $denominator === 0 ? 0 : floatval( number_format( ( $numerator / $denominator ) * 100, 2 ) );

					if ( $analysis_formulas[ $analysis_type ]['inverse'] ) {
						$analysis = 100 - $analysis;
					}

					$stats['subjects_analysis'][ $subject_log_id ][ $analysis_type ][ $log_date ] = $analysis;
				}
			}
		}

		// Push total events data
		foreach ( $event_types as $event_type ) {
			$stats['events_totals'][ $event_type ] = array_sum( wp_list_pluck( $stats['subjects_totals'], $event_type ) );
		}

		foreach ( $analysis_types as $analysis_type ) {
			$analysis_data = wp_list_pluck( $stats['subjects_totals'], $analysis_type );
			$analysis_count = count( $analysis_data );
			$stats['events_totals'][ $analysis_type ] = floatval( number_format( array_sum( $analysis_data ) / $analysis_count, 2 ) );
		}

		// Rank by engagement
		$subjects_ranks = wp_list_pluck( $stats['subjects_totals'], $rank_metrics );
		$subjects_ranks_index = 0;

		// Sort from high to low, mantain keys
		arsort( $subjects_ranks );

		// Push color data
		foreach ( $subjects_ranks as $subject_rank_id => $subject_rank_value ) {
			$stats['subjects_totals'][ $subject_rank_id ]['color'] = isset( $subject_rank_colors[ $subjects_ranks_index ] ) ? $subject_rank_colors[ $subjects_ranks_index ] : '#7E0000';

			$subjects_ranks_index++;
		}

		// update cache
		set_transient( 'et_pb_ab_' . $post_id . '_stats_' . $duration, $stats, DAY_IN_SECONDS );
	} else {
		// remove the cache if no logs found
		delete_transient( 'et_pb_ab_' . $post_id . '_stats_' . $duration );
		return false;
	}

	return $stats;
}

/**
 * Outputs get data stats duration
 *
 * @return array of data
 */
function et_pb_ab_get_stats_data_duration() {
	return apply_filters( 'et_pb_ab_get_stats_data_duration', array(
		'day',
		'week',
		'month',
		'all',
	) );
}

/**
 * Get subjects of particular post / Split test
 *
 * @param int    post id
 * @param string array|string type of output
 * @param mixed  string|bool  prefix that should be prepended
 */
function et_pb_ab_get_subjects( $post_id, $type = 'array', $prefix = false ) {
	$subjects_data = get_post_meta( $post_id, '_et_pb_ab_subjects', true );

	// If user wants string
	if ( 'string' === $type ) {
		return $subjects_data;
	}

	// Convert into array
	$subjects = explode(',', $subjects_data );

	if ( ! empty( $subjects ) && $prefix ) {

		$prefixed_subjects = array();

		// Loop subject, add prefix
		foreach ( $subjects as $subject ) {
			$prefixed_subjects[] = $prefix . (string) $subject;
		}

		return $prefixed_subjects;
	}

	return $subjects;
}

/**
 * Get list of Split testing event type
 *
 * @return array of event types
 */
function et_pb_ab_get_event_types() {
	return apply_filters( 'et_pb_ab_get_event_types', array(
		'view_page',
		'read_page',
		'view_goal',
		'read_goal',
		'click_goal',
		'con_goal',
		'con_short',
	) );
}

/**
 * Get min and max timestamp from returned MySQL query
 *
 * @param array  MySQL returned value. Expected to be array( array ( 'date' => 'YYYY-MM-DD' ) ) format
 * @param string day|week
 * @return array using min and max key
 */
function et_pb_ab_get_min_max_timestamp( $query_result, $interval = 'day' ) {
	$output = array(
		'min' => false,
		'max' => false,
	);

	// Get all available dates from logs
	$dates  = array_unique( wp_list_pluck( $query_result, 'date' ) );

	// Sort low-to-high and reset array keys
	sort( $dates );

	// Get min and max dates from logs
	$min_date = $dates[0];
	$max_date = $dates[ ( count( $dates ) - 1 ) ];

	switch ( $interval ) {
		case 'week':
			$output['min'] = strtotime( substr( $min_date, 0, 4 ) . 'W' . substr( $min_date, 4, 2 ) );
			$output['max'] = strtotime( substr( $max_date, 0, 4 ) . 'W' . substr( $max_date, 4, 2 ) );
			break;

		default:
			$output['min'] = strtotime( $min_date );
			$output['max'] = strtotime( $max_date );
			break;
	}

	return $output;
}

/**
 * Get all days between min and max dates from logs
 *
 * @param int start date timestamp
 * @param int end date timestamp
 * @param string day|week interval of rage
 * @return array of dates
 */
function et_pb_ab_get_date_range( $min_date_timestamp, $max_date_timestamp, $interval = 'day' ) {
	$day_timestamp = $min_date_timestamp;
	$full_dates    = array();

	switch ( $interval ) {
		case 'week':
			$date_format = 'YW';
			$time_interval = '+1 week';
			break;

		case 'hour':
			$date_format = 'Y-m-d H:i';
			$time_interval = '+1 hour';
			break;

		default:
			$date_format = 'Y-m-d';
			$time_interval = '+1 day';
			break;
	}

	while ( $day_timestamp <= $max_date_timestamp ) {
		$full_dates[] = date( $date_format, $day_timestamp );
		$day_timestamp = strtotime( $time_interval, $day_timestamp );
	}

	return $full_dates;
}

/**
 * Get list of Split analysis types
 *
 * @return array analysis types
 */
function et_pb_ab_get_analysis_types() {
	return apply_filters( 'et_pb_ab_get_analysis_types', array(
		'clicks',
		'reads',
		'bounces',
		'engagements',
		'conversions',
		'shortcode_conversions',
	) );
}

/**
 * Get numerator and denominator of various stats types
 *
 * @return array stats' data type formula
 */
function et_pb_ab_get_analysis_formulas() {
	return apply_filters( 'et_pb_ab_get_analysis_formulas', array(
		'clicks' => array(
			'numerator'   => 'click_goal',
			'denominator' => 'view_page',
			'inverse'     => false,
		),
		'reads' => array(
			'numerator'   => 'read_goal',
			'denominator' => 'view_page',
			'inverse'     => false,
		),
		'bounces' => array(
			'numerator'   => 'read_page',
			'denominator' => 'view_page',
			'inverse'     => true,
		),
		'engagements' => array(
			'numerator'   => 'read_goal',
			'denominator' => 'view_goal',
			'inverse'     => false,
		),
		'conversions' => array(
			'numerator'   => 'con_goal',
			'denominator' => 'view_page',
			'inverse'     => false,
		),
		'shortcode_conversions' => array(
			'numerator'   => 'con_short',
			'denominator' => 'view_page',
			'inverse'     => false,
		),
	) );
}

/**
 * List modules' slug which has conversions support
 *
 * @return array slugs of modules which have conversions support
 */
function et_pb_ab_get_modules_have_conversions() {
	return apply_filters( 'et_pb_ab_get_modules_have_conversions', array(
		'et_pb_shop',
		'et_pb_contact_form',
		'et_pb_signup',
		'et_pb_comments',
	) );
}

/**
 * Check whether Split testing active on current page
 *
 * @return bool
 */
function et_is_ab_testing_active() {
	$post_id = apply_filters( 'et_is_ab_testing_active_post_id', get_the_ID() );

	return 'on' === get_post_meta( $post_id, '_et_pb_use_ab_testing', true ) ? true : false;
}

/**
 * Check whether split test has report
 *
 * @return bool
 */
function et_pb_ab_has_report( $post_id ) {
	global $wpdb;

	if ( ! et_is_ab_testing_active() ) {
		return false;
	}

	$table_name = $wpdb->prefix . 'et_divi_ab_testing_stats';

	$query = $wpdb->prepare(
		"SELECT * FROM {$table_name} WHERE test_id = %d",
		$post_id
	);

	$result = $wpdb->get_row( $query ) ? true : false;

	return apply_filters( 'et_pb_ab_has_report', $result, $post_id );
}

/**
 * Check the status of the ab db version
 * @return bool
 */
function et_pb_db_status_up_to_date() {
	return ( $ab_db_settings = get_option( 'et_pb_ab_test_settings' ) ) && version_compare( $ab_db_settings['db_version'], ET_PB_AB_DB_VERSION, '>=' );
}

/**
 * Create Split testing table needed for Split testing feature
 *
 * @return void
 */
function et_pb_create_ab_tables() {
	if ( isset( $_POST['et_pb_ab_nonce'] ) && ! wp_verify_nonce( $_POST['et_pb_ab_nonce'], 'ab_testing_builder_nonce' ) ) {
		die( -1 );
	}

	// Verify user permission
	if ( ! current_user_can( 'edit_posts' ) || ! et_pb_is_allowed( 'ab_testing' ) ) {
		die( -1 );
	}

	// Verify update is needed
	if ( et_pb_db_status_up_to_date() ) {
		die( -1 );
	}

	global $wpdb;

	$stats_table_name = $wpdb->prefix . 'et_divi_ab_testing_stats';
	$client_subject_table_name = $wpdb->prefix . 'et_divi_ab_testing_clients';

	/*
	 * We'll set the default character set and collation for this table.
	 * If we don't do this, some characters could end up being converted
	 * to just ?'s when saved in our table.
	 */
	$charset_collate = '';

	if ( ! empty( $wpdb->charset ) ) {
		$charset_collate = sprintf(
			'DEFAULT CHARACTER SET %1$s',
			sanitize_text_field( $wpdb->charset )
		);
	}

	if ( ! empty( $wpdb->collate ) ) {
		$charset_collate .= sprintf(
			' COLLATE %1$s',
			sanitize_text_field( $wpdb->collate )
		);
	}

	$sql_stats = "CREATE TABLE $stats_table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		test_id varchar(20) NOT NULL,
		subject_id varchar(20) NOT NULL,
		record_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		event varchar(10) NOT NULL,
		client_id varchar(32) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	$sql_client_subject = "CREATE TABLE $client_subject_table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		test_id varchar(20) NOT NULL,
		subject_id varchar(20) NOT NULL,
		client_id varchar(32) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	dbDelta( array( $sql_stats, $sql_client_subject ) );

	$db_settings = array(
		'db_version' => ET_PB_AB_DB_VERSION,
	);

	update_option( 'et_pb_ab_test_settings', $db_settings );

	// Register Split Testing cron
	et_pb_create_ab_cron();

	die( 'success' );
}
add_action( 'wp_ajax_et_pb_create_ab_tables', 'et_pb_create_ab_tables' );

/**
 * Handle adding the Split testing log record via ajax
 *
 * @return void
 */
function et_pb_update_stats_table() {
	if ( isset( $_POST['et_ab_log_nonce'] ) && ! wp_verify_nonce( $_POST['et_ab_log_nonce'], 'et_ab_testing_log_nonce' ) ) {
		die( -1 );
	}

	$stats_data_json = str_replace( '\\', '',  $_POST['stats_data_array'] );
	$stats_data_array = json_decode( $stats_data_json, true );

	et_pb_add_stats_record( $stats_data_array );

	die( 1 );
}
add_action( 'wp_ajax_et_pb_update_stats_table', 'et_pb_update_stats_table' );
add_action( 'wp_ajax_nopriv_et_pb_update_stats_table', 'et_pb_update_stats_table' );

/**
 * List of valid split testing refresh interval duration
 *
 * @return array
 */
function et_pb_ab_refresh_interval_durations() {
	return apply_filters( 'et_pb_ab_refresh_interval_durations', array(
		'hourly' => 'day',
		'daily'  => 'week',
	));
}

/**
 * Get refresh interval of particular split test
 *
 * @param int     post ID
 * @param string  default interval
 * @return string interval used in particular split test
 */
function et_pb_ab_get_refresh_interval( $post_id, $default = 'hourly' ) {
	$interval = get_post_meta( $post_id, '_et_pb_ab_stats_refresh_interval', true );

	if ( in_array( $interval, array_keys( et_pb_ab_refresh_interval_durations() ) ) ) {
		return apply_filters( 'et_pb_ab_get_refresh_interval', $interval, $post_id );
	}

	return apply_filters( 'et_pb_ab_default_refresh_interval', $default, $post_id );
}

/**
 * Get refresh interval duration of particular split test
 *
 * @param int     post ID
 * @param string  default interval duration
 * @return string test's interval duration
 */
function et_pb_ab_get_refresh_interval_duration( $post_id, $default = 'day' ) {
	$durations = et_pb_ab_refresh_interval_durations();

	$interval = et_pb_ab_get_refresh_interval( $post_id );

	$interval_duration = isset( $durations[ $interval ] ) ? $durations[ $interval ] : $default;

	return apply_filters( 'et_pb_ab_get_refresh_interval_duration', $interval_duration, $post_id );
}

/**
 * Get goal module slug of particular split test
 *
 * @param int     post ID
 * @return string test's goal module slug
 */
function et_pb_ab_get_goal_module( $post_id ) {
	return get_post_meta( $post_id, '_et_pb_ab_goal_module', true );
}

/**
 * Register Divi's Split testing cron
 * There are 2 options - daily and hourly, so schedule 2 events
 * @return void
 */
function et_pb_create_ab_cron() {
	// schedule daily event
	if ( ! wp_next_scheduled( 'et_pb_ab_cron', array( 'interval' => 'daily' ) ) ) {
		wp_schedule_event( time(), 'daily', 'et_pb_ab_cron', array( 'interval' => 'daily' ) );
	}

	// schedule hourly event
	if ( ! wp_next_scheduled( 'et_pb_ab_cron', array( 'interval' => 'hourly' ) ) ) {
		wp_schedule_event( time(), 'hourly', 'et_pb_ab_cron', array( 'interval' => 'hourly' ) );
	}
}

/**
 * Perform Divi's Split testing cron
 *
 * @return void
 */
function et_pb_ab_cron( $args ) {
	$all_tests = et_pb_ab_get_all_tests();
	$interval = isset( $args ) ? $args : 'hourly';

	if ( empty( $all_tests ) ) {
		return;
	}

	// update cache for each test and for each duration
	foreach ( $all_tests as $test ) {
		$current_test_interval = et_pb_ab_get_refresh_interval( $test['test_id'] );

		// determine whether or not we should update the stats for current test depending on interval parameter
		if ( $current_test_interval !== $interval ) {
			continue;
		}

		foreach ( et_pb_ab_get_stats_data_duration() as $duration ) {
			et_pb_ab_get_stats_data( $test['test_id'], $duration, false, true );
		}
	}
}
add_action( 'et_pb_ab_cron', 'et_pb_ab_cron' );

function et_pb_ab_clear_cache_handler( $test_id ) {
	if ( ! $test_id ) {
		return;
	}

	foreach ( et_pb_ab_get_stats_data_duration() as $duration ) {
		delete_transient( 'et_pb_ab_' . $test_id . '_stats_' . $duration );
	}
}

function et_pb_ab_clear_cache() {
	// Verify nonce
	if ( isset( $_POST['et_pb_ab_nonce'] ) && ! wp_verify_nonce( $_POST['et_pb_ab_nonce'], 'ab_testing_builder_nonce' ) ) {
		die( -1 );
	}

	// Verify user permission
	if ( ! current_user_can( 'edit_posts' ) || ! et_pb_is_allowed( 'ab_testing' ) ) {
		die( -1 );
	}

	$test_id = intval( $_POST['et_pb_test_id'] );

	et_pb_ab_clear_cache_handler( $test_id );

	die( 1 );
}

add_action( 'wp_ajax_et_pb_ab_clear_cache', 'et_pb_ab_clear_cache' );

function et_pb_ab_get_all_tests() {
	global $wpdb;

	$table_name = $wpdb->prefix . 'et_divi_ab_testing_stats';

	if ( ! $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) {
		return false;
	}

	// construct sql query to get all the test ID from db
	$sql = "SELECT DISTINCT test_id FROM $table_name";

	// cache the data from conversions table
	$all_tests = $wpdb->get_results( $sql, ARRAY_A );

	return $all_tests;
}

function et_pb_ab_clear_stats() {
	// Verify nonce
	if ( isset( $_POST['et_pb_ab_nonce'] ) && ! wp_verify_nonce( $_POST['et_pb_ab_nonce'], 'ab_testing_builder_nonce' ) ) {
		die( -1 );
	}

	// Verify user permission
	if ( ! current_user_can( 'edit_posts' ) || ! et_pb_is_allowed( 'ab_testing' ) ) {
		die( -1 );
	}

	$test_id = intval( $_POST['et_pb_test_id'] );

	et_pb_ab_remove_stats( $test_id );

	et_pb_ab_clear_cache_handler( $test_id );

	die( 1 );
}
add_action( 'wp_ajax_et_pb_ab_clear_stats', 'et_pb_ab_clear_stats' );

/**
 * Remove Split testing log and clear stats cache
 *
 * @param int post ID
 * @return void
 */
function et_pb_ab_remove_stats( $test_id ) {
	global $wpdb;

	$test_id = intval( $test_id );

	et_pb_ab_clear_cache_handler( $test_id );

	$sql_args = array(
		$test_id,
	);

	foreach ( array( 'stats', 'clients' ) as $table_suffix ) {
		$table_name = $wpdb->prefix . 'et_divi_ab_testing_' . $table_suffix;

		// construct sql query to remove value from DB table
		$sql = "DELETE FROM $table_name WHERE test_id = %d";

		$wpdb->query( $wpdb->prepare( $sql, $sql_args ) );
	}
}

/**
 * Shop trigger DOM
 *
 * @return void
 */
function et_pb_ab_shop_trigger() {
	echo '<div class="et_pb_ab_shop_conversion"></div>';
}
add_action( 'woocommerce_thankyou', 'et_pb_ab_shop_trigger' );

/**
 * Tracking shortcode
 *
 * @return void
 */
function et_pb_split_track( $atts ) {
	$settings = shortcode_atts( array(
		'id' => '',
	), $atts );

	$output = sprintf( '<div class="et_pb_ab_split_track" style="display:none;" data-test_id="%1$s"></div>',
		esc_attr( $settings['id'] )
	);

	return $output;
}
add_shortcode( 'et_pb_split_track', 'et_pb_split_track' );
