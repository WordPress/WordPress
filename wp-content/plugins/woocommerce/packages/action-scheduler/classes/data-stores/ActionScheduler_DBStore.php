<?php

/**
 * Class ActionScheduler_DBStore
 *
 * Action data table data store.
 *
 * @since 3.0.0
 */
class ActionScheduler_DBStore extends ActionScheduler_Store {

	/**
	 * Used to share information about the before_date property of claims internally.
	 *
	 * This is used in preference to passing the same information as a method param
	 * for backwards-compatibility reasons.
	 *
	 * @var DateTime|null
	 */
	private $claim_before_date = null;

	/** @var int */
	protected static $max_args_length = 8000;

	/** @var int */
	protected static $max_index_length = 191;

	/**
	 * Initialize the data store
	 *
	 * @codeCoverageIgnore
	 */
	public function init() {
		$table_maker = new ActionScheduler_StoreSchema();
		$table_maker->init();
		$table_maker->register_tables();
	}

	/**
	 * Save an action, checks if this is a unique action before actually saving.
	 *
	 * @param ActionScheduler_Action $action         Action object.
	 * @param \DateTime              $scheduled_date Optional schedule date. Default null.
	 *
	 * @return int                  Action ID.
	 * @throws RuntimeException     Throws exception when saving the action fails.
	 */
	public function save_unique_action( ActionScheduler_Action $action, \DateTime $scheduled_date = null ) {
		return $this->save_action_to_db( $action, $scheduled_date, true );
	}

	/**
	 * Save an action. Can save duplicate action as well, prefer using `save_unique_action` instead.
	 *
	 * @param ActionScheduler_Action $action Action object.
	 * @param \DateTime              $scheduled_date Optional schedule date. Default null.
	 *
	 * @return int Action ID.
	 * @throws RuntimeException     Throws exception when saving the action fails.
	 */
	public function save_action( ActionScheduler_Action $action, \DateTime $scheduled_date = null ) {
		return $this->save_action_to_db( $action, $scheduled_date, false );
	}

	/**
	 * Save an action.
	 *
	 * @param ActionScheduler_Action $action Action object.
	 * @param ?DateTime              $date Optional schedule date. Default null.
	 * @param bool                   $unique Whether the action should be unique.
	 *
	 * @return int Action ID.
	 * @throws RuntimeException     Throws exception when saving the action fails.
	 */
	private function save_action_to_db( ActionScheduler_Action $action, DateTime $date = null, $unique = false ) {
		global $wpdb;

		try {
			$this->validate_action( $action );

			$data = array(
				'hook'                 => $action->get_hook(),
				'status'               => ( $action->is_finished() ? self::STATUS_COMPLETE : self::STATUS_PENDING ),
				'scheduled_date_gmt'   => $this->get_scheduled_date_string( $action, $date ),
				'scheduled_date_local' => $this->get_scheduled_date_string_local( $action, $date ),
				'schedule'             => serialize( $action->get_schedule() ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
				'group_id'             => $this->get_group_id( $action->get_group() ),
			);

			$args = wp_json_encode( $action->get_args() );
			if ( strlen( $args ) <= static::$max_index_length ) {
				$data['args'] = $args;
			} else {
				$data['args']          = $this->hash_args( $args );
				$data['extended_args'] = $args;
			}

			$insert_sql = $this->build_insert_sql( $data, $unique );

			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $insert_sql should be already prepared.
			$wpdb->query( $insert_sql );
			$action_id = $wpdb->insert_id;

			if ( is_wp_error( $action_id ) ) {
				throw new \RuntimeException( $action_id->get_error_message() );
			} elseif ( empty( $action_id ) ) {
				if ( $unique ) {
					return 0;
				}
				throw new \RuntimeException( $wpdb->last_error ? $wpdb->last_error : __( 'Database error.', 'woocommerce' ) );
			}

			do_action( 'action_scheduler_stored_action', $action_id );

			return $action_id;
		} catch ( \Exception $e ) {
			/* translators: %s: error message */
			throw new \RuntimeException( sprintf( __( 'Error saving action: %s', 'woocommerce' ), $e->getMessage() ), 0 );
		}
	}

	/**
	 * Helper function to build insert query.
	 *
	 * @param array $data Row data for action.
	 * @param bool  $unique Whether the action should be unique.
	 *
	 * @return string Insert query.
	 */
	private function build_insert_sql( array $data, $unique ) {
		global $wpdb;
		$columns      = array_keys( $data );
		$values       = array_values( $data );
		$placeholders = array_map( array( $this, 'get_placeholder_for_column' ), $columns );

		$table_name = ! empty( $wpdb->actionscheduler_actions ) ? $wpdb->actionscheduler_actions : $wpdb->prefix . 'actionscheduler_actions';

		$column_sql      = '`' . implode( '`, `', $columns ) . '`';
		$placeholder_sql = implode( ', ', $placeholders );
		$where_clause    = $this->build_where_clause_for_insert( $data, $table_name, $unique );
		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $column_sql and $where_clause are already prepared. $placeholder_sql is hardcoded.
		$insert_query    = $wpdb->prepare(
			"
INSERT INTO $table_name ( $column_sql )
SELECT $placeholder_sql FROM DUAL
WHERE ( $where_clause ) IS NULL",
			$values
		);
		// phpcs:enable

		return $insert_query;
	}

	/**
	 * Helper method to build where clause for action insert statement.
	 *
	 * @param array  $data Row data for action.
	 * @param string $table_name Action table name.
	 * @param bool   $unique Where action should be unique.
	 *
	 * @return string Where clause to be used with insert.
	 */
	private function build_where_clause_for_insert( $data, $table_name, $unique ) {
		global $wpdb;

		if ( ! $unique ) {
			return 'SELECT NULL FROM DUAL';
		}

		$pending_statuses            = array(
			ActionScheduler_Store::STATUS_PENDING,
			ActionScheduler_Store::STATUS_RUNNING,
		);
		$pending_status_placeholders = implode( ', ', array_fill( 0, count( $pending_statuses ), '%s' ) );
		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $pending_status_placeholders is hardcoded.
		$where_clause = $wpdb->prepare(
			"
SELECT action_id FROM $table_name
WHERE status IN ( $pending_status_placeholders )
AND hook = %s
AND `group_id` = %d
",
			array_merge(
				$pending_statuses,
				array(
					$data['hook'],
					$data['group_id'],
				)
			)
		);
		// phpcs:enable

		return "$where_clause" . ' LIMIT 1';
	}

	/**
	 * Helper method to get $wpdb->prepare placeholder for a given column name.
	 *
	 * @param string $column_name Name of column in actions table.
	 *
	 * @return string Placeholder to use for given column.
	 */
	private function get_placeholder_for_column( $column_name ) {
		$string_columns = array(
			'hook',
			'status',
			'scheduled_date_gmt',
			'scheduled_date_local',
			'args',
			'schedule',
			'last_attempt_gmt',
			'last_attempt_local',
			'extended_args',
		);

		return in_array( $column_name, $string_columns ) ? '%s' : '%d';
	}

	/**
	 * Generate a hash from json_encoded $args using MD5 as this isn't for security.
	 *
	 * @param string $args JSON encoded action args.
	 * @return string
	 */
	protected function hash_args( $args ) {
		return md5( $args );
	}

	/**
	 * Get action args query param value from action args.
	 *
	 * @param array $args Action args.
	 * @return string
	 */
	protected function get_args_for_query( $args ) {
		$encoded = wp_json_encode( $args );
		if ( strlen( $encoded ) <= static::$max_index_length ) {
			return $encoded;
		}
		return $this->hash_args( $encoded );
	}
	/**
	 * Get a group's ID based on its name/slug.
	 *
	 * @param string $slug The string name of a group.
	 * @param bool   $create_if_not_exists Whether to create the group if it does not already exist. Default, true - create the group.
	 *
	 * @return int The group's ID, if it exists or is created, or 0 if it does not exist and is not created.
	 */
	protected function get_group_id( $slug, $create_if_not_exists = true ) {
		if ( empty( $slug ) ) {
			return 0;
		}
		/** @var \wpdb $wpdb */
		global $wpdb;
		$group_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT group_id FROM {$wpdb->actionscheduler_groups} WHERE slug=%s", $slug ) );
		if ( empty( $group_id ) && $create_if_not_exists ) {
			$group_id = $this->create_group( $slug );
		}

		return $group_id;
	}

	/**
	 * Create an action group.
	 *
	 * @param string $slug Group slug.
	 *
	 * @return int Group ID.
	 */
	protected function create_group( $slug ) {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$wpdb->insert( $wpdb->actionscheduler_groups, array( 'slug' => $slug ) );

		return (int) $wpdb->insert_id;
	}

	/**
	 * Retrieve an action.
	 *
	 * @param int $action_id Action ID.
	 *
	 * @return ActionScheduler_Action
	 */
	public function fetch_action( $action_id ) {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$data = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT a.*, g.slug AS `group` FROM {$wpdb->actionscheduler_actions} a LEFT JOIN {$wpdb->actionscheduler_groups} g ON a.group_id=g.group_id WHERE a.action_id=%d",
				$action_id
			)
		);

		if ( empty( $data ) ) {
			return $this->get_null_action();
		}

		if ( ! empty( $data->extended_args ) ) {
			$data->args = $data->extended_args;
			unset( $data->extended_args );
		}

		// Convert NULL dates to zero dates.
		$date_fields = array(
			'scheduled_date_gmt',
			'scheduled_date_local',
			'last_attempt_gmt',
			'last_attempt_gmt',
		);
		foreach ( $date_fields as $date_field ) {
			if ( is_null( $data->$date_field ) ) {
				$data->$date_field = ActionScheduler_StoreSchema::DEFAULT_DATE;
			}
		}

		try {
			$action = $this->make_action_from_db_record( $data );
		} catch ( ActionScheduler_InvalidActionException $exception ) {
			do_action( 'action_scheduler_failed_fetch_action', $action_id, $exception );
			return $this->get_null_action();
		}

		return $action;
	}

	/**
	 * Create a null action.
	 *
	 * @return ActionScheduler_NullAction
	 */
	protected function get_null_action() {
		return new ActionScheduler_NullAction();
	}

	/**
	 * Create an action from a database record.
	 *
	 * @param object $data Action database record.
	 *
	 * @return ActionScheduler_Action|ActionScheduler_CanceledAction|ActionScheduler_FinishedAction
	 */
	protected function make_action_from_db_record( $data ) {

		$hook     = $data->hook;
		$args     = json_decode( $data->args, true );
		$schedule = unserialize( $data->schedule ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize

		$this->validate_args( $args, $data->action_id );
		$this->validate_schedule( $schedule, $data->action_id );

		if ( empty( $schedule ) ) {
			$schedule = new ActionScheduler_NullSchedule();
		}
		$group = $data->group ? $data->group : '';

		return ActionScheduler::factory()->get_stored_action( $data->status, $data->hook, $args, $schedule, $group );
	}

	/**
	 * Returns the SQL statement to query (or count) actions.
	 *
	 * @since 3.3.0 $query['status'] accepts array of statuses instead of a single status.
	 *
	 * @param array  $query Filtering options.
	 * @param string $select_or_count  Whether the SQL should select and return the IDs or just the row count.
	 *
	 * @return string SQL statement already properly escaped.
	 * @throws InvalidArgumentException If the query is invalid.
	 */
	protected function get_query_actions_sql( array $query, $select_or_count = 'select' ) {

		if ( ! in_array( $select_or_count, array( 'select', 'count' ), true ) ) {
			throw new InvalidArgumentException( __( 'Invalid value for select or count parameter. Cannot query actions.', 'woocommerce' ) );
		}

		$query = wp_parse_args( $query, array(
			'hook'                  => '',
			'args'                  => null,
			'partial_args_matching' => 'off', // can be 'like' or 'json'
			'date'                  => null,
			'date_compare'          => '<=',
			'modified'              => null,
			'modified_compare'      => '<=',
			'group'                 => '',
			'status'                => '',
			'claimed'               => null,
			'per_page'              => 5,
			'offset'                => 0,
			'orderby'               => 'date',
			'order'                 => 'ASC',
		 ) );

		/** @var \wpdb $wpdb */
		global $wpdb;

		$db_server_info = is_callable( array( $wpdb, 'db_server_info' ) ) ? $wpdb->db_server_info() : $wpdb->db_version();
		if ( false !== strpos( $db_server_info, 'MariaDB' ) ) {
			$supports_json = version_compare(
				PHP_VERSION_ID >= 80016 ? $wpdb->db_version() : preg_replace( '/[^0-9.].*/', '', str_replace( '5.5.5-', '', $db_server_info ) ),
				'10.2',
				'>='
			);
		} else {
			$supports_json = version_compare( $wpdb->db_version(), '5.7', '>=' );
		}

		$sql        = ( 'count' === $select_or_count ) ? 'SELECT count(a.action_id)' : 'SELECT a.action_id';
		$sql        .= " FROM {$wpdb->actionscheduler_actions} a";
		$sql_params = array();

		if ( ! empty( $query['group'] ) || 'group' === $query['orderby'] ) {
			$sql .= " LEFT JOIN {$wpdb->actionscheduler_groups} g ON g.group_id=a.group_id";
		}

		$sql .= " WHERE 1=1";

		if ( ! empty( $query['group'] ) ) {
			$sql          .= " AND g.slug=%s";
			$sql_params[] = $query['group'];
		}

		if ( ! empty( $query['hook'] ) ) {
			$sql          .= " AND a.hook=%s";
			$sql_params[] = $query['hook'];
		}

		if ( ! is_null( $query['args'] ) ) {
			switch ( $query['partial_args_matching'] ) {
				case 'json':
					if ( ! $supports_json ) {
						throw new \RuntimeException( __( 'JSON partial matching not supported in your environment. Please check your MySQL/MariaDB version.', 'woocommerce' ) );
					}
					$supported_types = array(
						'integer' => '%d',
						'boolean' => '%s',
						'double'  => '%f',
						'string'  => '%s',
					);
					foreach ( $query['args'] as $key => $value ) {
						$value_type = gettype( $value );
						if ( 'boolean' === $value_type ) {
							$value = $value ? 'true' : 'false';
						}
						$placeholder = isset( $supported_types[ $value_type ] ) ? $supported_types[ $value_type ] : false;
						if ( ! $placeholder ) {
							throw new \RuntimeException( sprintf(
								/* translators: %s: provided value type */
								__( 'The value type for the JSON partial matching is not supported. Must be either integer, boolean, double or string. %s type provided.', 'woocommerce' ),
								$value_type
							) );
						}
						$sql          .= ' AND JSON_EXTRACT(a.args, %s)='.$placeholder;
						$sql_params[] = '$.'.$key;
						$sql_params[] = $value;
					}
					break;
				case 'like':
					foreach ( $query['args'] as $key => $value ) {
						$sql          .= ' AND a.args LIKE %s';
						$json_partial = $wpdb->esc_like( trim( json_encode( array( $key => $value ) ), '{}' ) );
						$sql_params[] = "%{$json_partial}%";
					}
					break;
				case 'off':
					$sql          .= " AND a.args=%s";
					$sql_params[] = $this->get_args_for_query( $query['args'] );
					break;
				default:
					throw new \RuntimeException( __( 'Unknown partial args matching value.', 'woocommerce' ) );
			}
		}

		if ( $query['status'] ) {
			$statuses     = (array) $query['status'];
			$placeholders = array_fill( 0, count( $statuses ), '%s' );
			$sql         .= ' AND a.status IN (' . join( ', ', $placeholders ) . ')';
			$sql_params   = array_merge( $sql_params, array_values( $statuses ) );
		}

		if ( $query['date'] instanceof \DateTime ) {
			$date = clone $query['date'];
			$date->setTimezone( new \DateTimeZone( 'UTC' ) );
			$date_string  = $date->format( 'Y-m-d H:i:s' );
			$comparator   = $this->validate_sql_comparator( $query['date_compare'] );
			$sql         .= " AND a.scheduled_date_gmt $comparator %s";
			$sql_params[] = $date_string;
		}

		if ( $query['modified'] instanceof \DateTime ) {
			$modified = clone $query['modified'];
			$modified->setTimezone( new \DateTimeZone( 'UTC' ) );
			$date_string  = $modified->format( 'Y-m-d H:i:s' );
			$comparator   = $this->validate_sql_comparator( $query['modified_compare'] );
			$sql         .= " AND a.last_attempt_gmt $comparator %s";
			$sql_params[] = $date_string;
		}

		if ( true === $query['claimed'] ) {
			$sql .= ' AND a.claim_id != 0';
		} elseif ( false === $query['claimed'] ) {
			$sql .= ' AND a.claim_id = 0';
		} elseif ( ! is_null( $query['claimed'] ) ) {
			$sql         .= ' AND a.claim_id = %d';
			$sql_params[] = $query['claimed'];
		}

		if ( ! empty( $query['search'] ) ) {
			$sql .= ' AND (a.hook LIKE %s OR (a.extended_args IS NULL AND a.args LIKE %s) OR a.extended_args LIKE %s';
			for ( $i = 0; $i < 3; $i++ ) {
				$sql_params[] = sprintf( '%%%s%%', $query['search'] );
			}

			$search_claim_id = (int) $query['search'];
			if ( $search_claim_id ) {
				$sql         .= ' OR a.claim_id = %d';
				$sql_params[] = $search_claim_id;
			}

			$sql .= ')';
		}

		if ( 'select' === $select_or_count ) {
			if ( 'ASC' === strtoupper( $query['order'] ) ) {
				$order = 'ASC';
			} else {
				$order = 'DESC';
			}
			switch ( $query['orderby'] ) {
				case 'hook':
					$sql .= " ORDER BY a.hook $order";
					break;
				case 'group':
					$sql .= " ORDER BY g.slug $order";
					break;
				case 'modified':
					$sql .= " ORDER BY a.last_attempt_gmt $order";
					break;
				case 'none':
					break;
				case 'action_id':
					$sql .= " ORDER BY a.action_id $order";
					break;
				case 'date':
				default:
					$sql .= " ORDER BY a.scheduled_date_gmt $order";
					break;
			}

			if ( $query['per_page'] > 0 ) {
				$sql         .= ' LIMIT %d, %d';
				$sql_params[] = $query['offset'];
				$sql_params[] = $query['per_page'];
			}
		}

		if ( ! empty( $sql_params ) ) {
			$sql = $wpdb->prepare( $sql, $sql_params ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}

		return $sql;
	}

	/**
	 * Query for action count or list of action IDs.
	 *
	 * @since 3.3.0 $query['status'] accepts array of statuses instead of a single status.
	 *
	 * @see ActionScheduler_Store::query_actions for $query arg usage.
	 *
	 * @param array  $query      Query filtering options.
	 * @param string $query_type Whether to select or count the results. Defaults to select.
	 *
	 * @return string|array|null The IDs of actions matching the query. Null on failure.
	 */
	public function query_actions( $query = array(), $query_type = 'select' ) {
		/** @var wpdb $wpdb */
		global $wpdb;

		$sql = $this->get_query_actions_sql( $query, $query_type );

		return ( 'count' === $query_type ) ? $wpdb->get_var( $sql ) : $wpdb->get_col( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.NoSql, WordPress.DB.DirectDatabaseQuery.NoCaching
	}

	/**
	 * Get a count of all actions in the store, grouped by status.
	 *
	 * @return array Set of 'status' => int $count pairs for statuses with 1 or more actions of that status.
	 */
	public function action_counts() {
		global $wpdb;

		$sql  = "SELECT a.status, count(a.status) as 'count'";
		$sql .= " FROM {$wpdb->actionscheduler_actions} a";
		$sql .= ' GROUP BY a.status';

		$actions_count_by_status = array();
		$action_stati_and_labels = $this->get_status_labels();

		foreach ( $wpdb->get_results( $sql ) as $action_data ) { // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			// Ignore any actions with invalid status.
			if ( array_key_exists( $action_data->status, $action_stati_and_labels ) ) {
				$actions_count_by_status[ $action_data->status ] = $action_data->count;
			}
		}

		return $actions_count_by_status;
	}

	/**
	 * Cancel an action.
	 *
	 * @param int $action_id Action ID.
	 *
	 * @return void
	 * @throws \InvalidArgumentException If the action update failed.
	 */
	public function cancel_action( $action_id ) {
		/** @var \wpdb $wpdb */
		global $wpdb;

		$updated = $wpdb->update(
			$wpdb->actionscheduler_actions,
			array( 'status' => self::STATUS_CANCELED ),
			array( 'action_id' => $action_id ),
			array( '%s' ),
			array( '%d' )
		);
		if ( false === $updated ) {
			/* translators: %s: action ID */
			throw new \InvalidArgumentException( sprintf( __( 'Unidentified action %s', 'woocommerce' ), $action_id ) );
		}
		do_action( 'action_scheduler_canceled_action', $action_id );
	}

	/**
	 * Cancel pending actions by hook.
	 *
	 * @since 3.0.0
	 *
	 * @param string $hook Hook name.
	 *
	 * @return void
	 */
	public function cancel_actions_by_hook( $hook ) {
		$this->bulk_cancel_actions( array( 'hook' => $hook ) );
	}

	/**
	 * Cancel pending actions by group.
	 *
	 * @param string $group Group slug.
	 *
	 * @return void
	 */
	public function cancel_actions_by_group( $group ) {
		$this->bulk_cancel_actions( array( 'group' => $group ) );
	}

	/**
	 * Bulk cancel actions.
	 *
	 * @since 3.0.0
	 *
	 * @param array $query_args Query parameters.
	 */
	protected function bulk_cancel_actions( $query_args ) {
		/** @var \wpdb $wpdb */
		global $wpdb;

		if ( ! is_array( $query_args ) ) {
			return;
		}

		// Don't cancel actions that are already canceled.
		if ( isset( $query_args['status'] ) && self::STATUS_CANCELED === $query_args['status'] ) {
			return;
		}

		$action_ids = true;
		$query_args = wp_parse_args(
			$query_args,
			array(
				'per_page' => 1000,
				'status'   => self::STATUS_PENDING,
				'orderby'  => 'action_id',
			)
		);

		while ( $action_ids ) {
			$action_ids = $this->query_actions( $query_args );
			if ( empty( $action_ids ) ) {
				break;
			}

			$format     = array_fill( 0, count( $action_ids ), '%d' );
			$query_in   = '(' . implode( ',', $format ) . ')';
			$parameters = $action_ids;
			array_unshift( $parameters, self::STATUS_CANCELED );

			$wpdb->query(
				$wpdb->prepare(
					"UPDATE {$wpdb->actionscheduler_actions} SET status = %s WHERE action_id IN {$query_in}", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					$parameters
				)
			);

			do_action( 'action_scheduler_bulk_cancel_actions', $action_ids );
		}
	}

	/**
	 * Delete an action.
	 *
	 * @param int $action_id Action ID.
	 * @throws \InvalidArgumentException If the action deletion failed.
	 */
	public function delete_action( $action_id ) {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$deleted = $wpdb->delete( $wpdb->actionscheduler_actions, array( 'action_id' => $action_id ), array( '%d' ) );
		if ( empty( $deleted ) ) {
			throw new \InvalidArgumentException( sprintf( __( 'Unidentified action %s', 'woocommerce' ), $action_id ) ); //phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
		}
		do_action( 'action_scheduler_deleted_action', $action_id );
	}

	/**
	 * Get the schedule date for an action.
	 *
	 * @param string $action_id Action ID.
	 *
	 * @return \DateTime The local date the action is scheduled to run, or the date that it ran.
	 */
	public function get_date( $action_id ) {
		$date = $this->get_date_gmt( $action_id );
		ActionScheduler_TimezoneHelper::set_local_timezone( $date );
		return $date;
	}

	/**
	 * Get the GMT schedule date for an action.
	 *
	 * @param int $action_id Action ID.
	 *
	 * @throws \InvalidArgumentException If action cannot be identified.
	 * @return \DateTime The GMT date the action is scheduled to run, or the date that it ran.
	 */
	protected function get_date_gmt( $action_id ) {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$record = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->actionscheduler_actions} WHERE action_id=%d", $action_id ) );
		if ( empty( $record ) ) {
			throw new \InvalidArgumentException( sprintf( __( 'Unidentified action %s', 'woocommerce' ), $action_id ) ); //phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
		}
		if ( self::STATUS_PENDING === $record->status ) {
			return as_get_datetime_object( $record->scheduled_date_gmt );
		} else {
			return as_get_datetime_object( $record->last_attempt_gmt );
		}
	}

	/**
	 * Stake a claim on actions.
	 *
	 * @param int       $max_actions Maximum number of action to include in claim.
	 * @param \DateTime $before_date Jobs must be schedule before this date. Defaults to now.
	 * @param array     $hooks Hooks to filter for.
	 * @param string    $group Group to filter for.
	 *
	 * @return ActionScheduler_ActionClaim
	 */
	public function stake_claim( $max_actions = 10, \DateTime $before_date = null, $hooks = array(), $group = '' ) {
		$claim_id = $this->generate_claim_id();

		$this->claim_before_date = $before_date;
		$this->claim_actions( $claim_id, $max_actions, $before_date, $hooks, $group );
		$action_ids              = $this->find_actions_by_claim_id( $claim_id );
		$this->claim_before_date = null;

		return new ActionScheduler_ActionClaim( $claim_id, $action_ids );
	}

	/**
	 * Generate a new action claim.
	 *
	 * @return int Claim ID.
	 */
	protected function generate_claim_id() {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$now = as_get_datetime_object();
		$wpdb->insert( $wpdb->actionscheduler_claims, array( 'date_created_gmt' => $now->format( 'Y-m-d H:i:s' ) ) );

		return $wpdb->insert_id;
	}

	/**
	 * Mark actions claimed.
	 *
	 * @param string    $claim_id Claim Id.
	 * @param int       $limit Number of action to include in claim.
	 * @param \DateTime $before_date Should use UTC timezone.
	 * @param array     $hooks Hooks to filter for.
	 * @param string    $group Group to filter for.
	 *
	 * @return int The number of actions that were claimed.
	 * @throws \InvalidArgumentException Throws InvalidArgumentException if group doesn't exist.
	 * @throws \RuntimeException Throws RuntimeException if unable to claim action.
	 */
	protected function claim_actions( $claim_id, $limit, \DateTime $before_date = null, $hooks = array(), $group = '' ) {
		/** @var \wpdb $wpdb */
		global $wpdb;

		$now  = as_get_datetime_object();
		$date = is_null( $before_date ) ? $now : clone $before_date;

		// can't use $wpdb->update() because of the <= condition.
		$update = "UPDATE {$wpdb->actionscheduler_actions} SET claim_id=%d, last_attempt_gmt=%s, last_attempt_local=%s";
		$params = array(
			$claim_id,
			$now->format( 'Y-m-d H:i:s' ),
			current_time( 'mysql' ),
		);

		$where    = 'WHERE claim_id = 0 AND scheduled_date_gmt <= %s AND status=%s';
		$params[] = $date->format( 'Y-m-d H:i:s' );
		$params[] = self::STATUS_PENDING;

		if ( ! empty( $hooks ) ) {
			$placeholders = array_fill( 0, count( $hooks ), '%s' );
			$where        .= ' AND hook IN (' . join( ', ', $placeholders ) . ')';
			$params       = array_merge( $params, array_values( $hooks ) );
		}

		if ( ! empty( $group ) ) {

			$group_id = $this->get_group_id( $group, false );

			// throw exception if no matching group found, this matches ActionScheduler_wpPostStore's behaviour.
			if ( empty( $group_id ) ) {
				/* translators: %s: group name */
				throw new InvalidArgumentException( sprintf( __( 'The group "%s" does not exist.', 'woocommerce' ), $group ) );
			}

			$where    .= ' AND group_id = %d';
			$params[] = $group_id;
		}

		/**
		 * Sets the order-by clause used in the action claim query.
		 *
		 * @since 3.4.0
		 *
		 * @param string $order_by_sql
		 */
		$order    = apply_filters( 'action_scheduler_claim_actions_order_by', 'ORDER BY attempts ASC, scheduled_date_gmt ASC, action_id ASC' );
		$params[] = $limit;

		$sql           = $wpdb->prepare( "{$update} {$where} {$order} LIMIT %d", $params ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders
		$rows_affected = $wpdb->query( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		if ( false === $rows_affected ) {
			throw new \RuntimeException( __( 'Unable to claim actions. Database error.', 'woocommerce' ) );
		}

		return (int) $rows_affected;
	}

	/**
	 * Get the number of active claims.
	 *
	 * @return int
	 */
	public function get_claim_count() {
		global $wpdb;

		$sql = "SELECT COUNT(DISTINCT claim_id) FROM {$wpdb->actionscheduler_actions} WHERE claim_id != 0 AND status IN ( %s, %s)";
		$sql = $wpdb->prepare( $sql, array( self::STATUS_PENDING, self::STATUS_RUNNING ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		return (int) $wpdb->get_var( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * Return an action's claim ID, as stored in the claim_id column.
	 *
	 * @param string $action_id Action ID.
	 * @return mixed
	 */
	public function get_claim_id( $action_id ) {
		/** @var \wpdb $wpdb */
		global $wpdb;

		$sql = "SELECT claim_id FROM {$wpdb->actionscheduler_actions} WHERE action_id=%d";
		$sql = $wpdb->prepare( $sql, $action_id ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		return (int) $wpdb->get_var( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * Retrieve the action IDs of action in a claim.
	 *
	 * @param  int $claim_id Claim ID.
	 * @return int[]
	 */
	public function find_actions_by_claim_id( $claim_id ) {
		/** @var \wpdb $wpdb */
		global $wpdb;

		$action_ids  = array();
		$before_date = isset( $this->claim_before_date ) ? $this->claim_before_date : as_get_datetime_object();
		$cut_off     = $before_date->format( 'Y-m-d H:i:s' );

		$sql = $wpdb->prepare(
			"SELECT action_id, scheduled_date_gmt FROM {$wpdb->actionscheduler_actions} WHERE claim_id = %d",
			$claim_id
		);

		// Verify that the scheduled date for each action is within the expected bounds (in some unusual
		// cases, we cannot depend on MySQL to honor all of the WHERE conditions we specify).
		foreach ( $wpdb->get_results( $sql ) as $claimed_action ) { // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			if ( $claimed_action->scheduled_date_gmt <= $cut_off ) {
				$action_ids[] = absint( $claimed_action->action_id );
			}
		}

		return $action_ids;
	}

	/**
	 * Release actions from a claim and delete the claim.
	 *
	 * @param ActionScheduler_ActionClaim $claim Claim object.
	 */
	public function release_claim( ActionScheduler_ActionClaim $claim ) {
		/** @var \wpdb $wpdb */
		global $wpdb;
		/**
		 * Deadlock warning: This function modifies actions to release them from claims that have been processed. Earlier, we used to it in a atomic query, i.e. we would update all actions belonging to a particular claim_id with claim_id = 0.
		 * While this was functionally correct, it would cause deadlock, since this update query will hold a lock on the claim_id_.. index on the action table.
		 * This allowed the possibility of a race condition, where the claimer query is also running at the same time, then the claimer query will also try to acquire a lock on the claim_id_.. index, and in this case if claim release query has already progressed to the point of acquiring the lock, but have not updated yet, it would cause a deadlock.
		 *
		 * We resolve this by getting all the actions_id that we want to release claim from in a separate query, and then releasing the claim on each of them. This way, our lock is acquired on the action_id index instead of the claim_id index. Note that the lock on claim_id will still be acquired, but it will only when we actually make the update, rather than when we select the actions.
		 */
		$action_ids = $wpdb->get_col( $wpdb->prepare( "SELECT action_id FROM {$wpdb->actionscheduler_actions} WHERE claim_id = %d", $claim->get_id() ) );

		$row_updates = 0;
		if ( count( $action_ids ) > 0 ) {
			$action_id_string = implode( ',', array_map( 'absint', $action_ids ) );
			$row_updates = $wpdb->query( "UPDATE {$wpdb->actionscheduler_actions} SET claim_id = 0 WHERE action_id IN ({$action_id_string})" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}

		$wpdb->delete( $wpdb->actionscheduler_claims, array( 'claim_id' => $claim->get_id() ), array( '%d' ) );

		if ( $row_updates < count( $action_ids ) ) {
			throw new RuntimeException(
				sprintf(
					__( 'Unable to release actions from claim id %d.', 'woocommerce' ),
					$claim->get_id()
				)
			);
		}
	}

	/**
	 * Remove the claim from an action.
	 *
	 * @param int $action_id Action ID.
	 *
	 * @return void
	 */
	public function unclaim_action( $action_id ) {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$wpdb->update(
			$wpdb->actionscheduler_actions,
			array( 'claim_id' => 0 ),
			array( 'action_id' => $action_id ),
			array( '%s' ),
			array( '%d' )
		);
	}

	/**
	 * Mark an action as failed.
	 *
	 * @param int $action_id Action ID.
	 * @throws \InvalidArgumentException Throw an exception if action was not updated.
	 */
	public function mark_failure( $action_id ) {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$updated = $wpdb->update(
			$wpdb->actionscheduler_actions,
			array( 'status' => self::STATUS_FAILED ),
			array( 'action_id' => $action_id ),
			array( '%s' ),
			array( '%d' )
		);
		if ( empty( $updated ) ) {
			throw new \InvalidArgumentException( sprintf( __( 'Unidentified action %s', 'woocommerce' ), $action_id ) ); //phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
		}
	}

	/**
	 * Add execution message to action log.
	 *
	 * @param int $action_id Action ID.
	 *
	 * @return void
	 */
	public function log_execution( $action_id ) {
		/** @var \wpdb $wpdb */
		global $wpdb;

		$sql = "UPDATE {$wpdb->actionscheduler_actions} SET attempts = attempts+1, status=%s, last_attempt_gmt = %s, last_attempt_local = %s WHERE action_id = %d";
		$sql = $wpdb->prepare( $sql, self::STATUS_RUNNING, current_time( 'mysql', true ), current_time( 'mysql' ), $action_id ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$wpdb->query( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * Mark an action as complete.
	 *
	 * @param int $action_id Action ID.
	 *
	 * @return void
	 * @throws \InvalidArgumentException Throw an exception if action was not updated.
	 */
	public function mark_complete( $action_id ) {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$updated = $wpdb->update(
			$wpdb->actionscheduler_actions,
			array(
				'status'             => self::STATUS_COMPLETE,
				'last_attempt_gmt'   => current_time( 'mysql', true ),
				'last_attempt_local' => current_time( 'mysql' ),
			),
			array( 'action_id' => $action_id ),
			array( '%s' ),
			array( '%d' )
		);
		if ( empty( $updated ) ) {
			throw new \InvalidArgumentException( sprintf( __( 'Unidentified action %s', 'woocommerce' ), $action_id ) ); //phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
		}

		/**
		 * Fires after a scheduled action has been completed.
		 *
		 * @since 3.4.2
		 *
		 * @param int $action_id Action ID.
		 */
		do_action( 'action_scheduler_completed_action', $action_id );
	}

	/**
	 * Get an action's status.
	 *
	 * @param int $action_id Action ID.
	 *
	 * @return string
	 * @throws \InvalidArgumentException Throw an exception if not status was found for action_id.
	 * @throws \RuntimeException Throw an exception if action status could not be retrieved.
	 */
	public function get_status( $action_id ) {
		/** @var \wpdb $wpdb */
		global $wpdb;
		$sql    = "SELECT status FROM {$wpdb->actionscheduler_actions} WHERE action_id=%d";
		$sql    = $wpdb->prepare( $sql, $action_id ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$status = $wpdb->get_var( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		if ( null === $status ) {
			throw new \InvalidArgumentException( __( 'Invalid action ID. No status found.', 'woocommerce' ) );
		} elseif ( empty( $status ) ) {
			throw new \RuntimeException( __( 'Unknown status found for action.', 'woocommerce' ) );
		} else {
			return $status;
		}
	}
}
