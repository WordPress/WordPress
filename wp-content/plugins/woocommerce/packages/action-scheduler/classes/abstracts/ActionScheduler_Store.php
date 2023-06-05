<?php

/**
 * Class ActionScheduler_Store
 * @codeCoverageIgnore
 */
abstract class ActionScheduler_Store extends ActionScheduler_Store_Deprecated {
	const STATUS_COMPLETE = 'complete';
	const STATUS_PENDING  = 'pending';
	const STATUS_RUNNING  = 'in-progress';
	const STATUS_FAILED   = 'failed';
	const STATUS_CANCELED = 'canceled';
	const DEFAULT_CLASS   = 'ActionScheduler_wpPostStore';

	/** @var ActionScheduler_Store */
	private static $store = NULL;

	/** @var int */
	protected static $max_args_length = 191;

	/**
	 * @param ActionScheduler_Action $action
	 * @param DateTime $scheduled_date Optional Date of the first instance
	 *        to store. Otherwise uses the first date of the action's
	 *        schedule.
	 *
	 * @return int The action ID
	 */
	abstract public function save_action( ActionScheduler_Action $action, DateTime $scheduled_date = NULL );

	/**
	 * @param string $action_id
	 *
	 * @return ActionScheduler_Action
	 */
	abstract public function fetch_action( $action_id );

	/**
	 * Find an action.
	 *
	 * Note: the query ordering changes based on the passed 'status' value.
	 *
	 * @param string $hook Action hook.
	 * @param array  $params Parameters of the action to find.
	 *
	 * @return string|null ID of the next action matching the criteria or NULL if not found.
	 */
	public function find_action( $hook, $params = array() ) {
		$params = wp_parse_args(
			$params,
			array(
				'args'   => null,
				'status' => self::STATUS_PENDING,
				'group'  => '',
			)
		);

		// These params are fixed for this method.
		$params['hook']     = $hook;
		$params['orderby']  = 'date';
		$params['per_page'] = 1;

		if ( ! empty( $params['status'] ) ) {
			if ( self::STATUS_PENDING === $params['status'] ) {
				$params['order'] = 'ASC'; // Find the next action that matches.
			} else {
				$params['order'] = 'DESC'; // Find the most recent action that matches.
			}
		}

		$results = $this->query_actions( $params );

		return empty( $results ) ? null : $results[0];
	}

	/**
	 * Query for action count or list of action IDs.
	 *
	 * @since 3.3.0 $query['status'] accepts array of statuses instead of a single status.
	 *
	 * @param array  $query {
	 *      Query filtering options.
	 *
	 *      @type string       $hook             The name of the actions. Optional.
	 *      @type string|array $status           The status or statuses of the actions. Optional.
	 *      @type array        $args             The args array of the actions. Optional.
	 *      @type DateTime     $date             The scheduled date of the action. Used in UTC timezone. Optional.
	 *      @type string       $date_compare     Operator for selecting by $date param. Accepted values are '!=', '>', '>=', '<', '<=', '='. Defaults to '<='.
	 *      @type DateTime     $modified         The last modified date of the action. Used in UTC timezone. Optional.
	 *      @type string       $modified_compare Operator for comparing $modified param. Accepted values are '!=', '>', '>=', '<', '<=', '='. Defaults to '<='.
	 *      @type string       $group            The group the action belongs to. Optional.
	 *      @type bool|int     $claimed          TRUE to find claimed actions, FALSE to find unclaimed actions, an int to find a specific claim ID. Optional.
	 *      @type int          $per_page         Number of results to return. Defaults to 5.
	 *      @type int          $offset           The query pagination offset. Defaults to 0.
	 *      @type int          $orderby          Accepted values are 'hook', 'group', 'modified', 'date' or 'none'. Defaults to 'date'.
	 *      @type string       $order            Accepted values are 'ASC' or 'DESC'. Defaults to 'ASC'.
	 * }
	 * @param string $query_type Whether to select or count the results. Default, select.
	 *
	 * @return string|array|null The IDs of actions matching the query. Null on failure.
	 */
	abstract public function query_actions( $query = array(), $query_type = 'select' );

	/**
	 * Run query to get a single action ID.
	 *
	 * @since 3.3.0
	 *
	 * @see ActionScheduler_Store::query_actions for $query arg usage but 'per_page' and 'offset' can't be used.
	 *
	 * @param array $query Query parameters.
	 *
	 * @return int|null
	 */
	public function query_action( $query ) {
		$query['per_page'] = 1;
		$query['offset']   = 0;
		$results           = $this->query_actions( $query );

		if ( empty( $results ) ) {
			return null;
		} else {
			return (int) $results[0];
		}
	}

	/**
	 * Get a count of all actions in the store, grouped by status
	 *
	 * @return array
	 */
	abstract public function action_counts();

	/**
	 * Get additional action counts.
	 *
	 * - add past-due actions
	 *
	 * @return array
	 */
	public function extra_action_counts() {
		$extra_actions = array();

		$pastdue_action_counts = ( int ) $this->query_actions( array(
			'status' => self::STATUS_PENDING,
			'date'   => as_get_datetime_object(),
		), 'count' );

		if ( $pastdue_action_counts ) {
			$extra_actions['past-due'] = $pastdue_action_counts;
		}

		/**
		 * Allows 3rd party code to add extra action counts (used in filters in the list table).
		 *
		 * @since 3.5.0
		 * @param $extra_actions array Array with format action_count_identifier => action count.
		 */
		return apply_filters( 'action_scheduler_extra_action_counts', $extra_actions );
	}

	/**
	 * @param string $action_id
	 */
	abstract public function cancel_action( $action_id );

	/**
	 * @param string $action_id
	 */
	abstract public function delete_action( $action_id );

	/**
	 * @param string $action_id
	 *
	 * @return DateTime The date the action is schedule to run, or the date that it ran.
	 */
	abstract public function get_date( $action_id );


	/**
	 * @param int      $max_actions
	 * @param DateTime $before_date Claim only actions schedule before the given date. Defaults to now.
	 * @param array    $hooks       Claim only actions with a hook or hooks.
	 * @param string   $group       Claim only actions in the given group.
	 *
	 * @return ActionScheduler_ActionClaim
	 */
	abstract public function stake_claim( $max_actions = 10, DateTime $before_date = null, $hooks = array(), $group = '' );

	/**
	 * @return int
	 */
	abstract public function get_claim_count();

	/**
	 * @param ActionScheduler_ActionClaim $claim
	 */
	abstract public function release_claim( ActionScheduler_ActionClaim $claim );

	/**
	 * @param string $action_id
	 */
	abstract public function unclaim_action( $action_id );

	/**
	 * @param string $action_id
	 */
	abstract public function mark_failure( $action_id );

	/**
	 * @param string $action_id
	 */
	abstract public function log_execution( $action_id );

	/**
	 * @param string $action_id
	 */
	abstract public function mark_complete( $action_id );

	/**
	 * @param string $action_id
	 *
	 * @return string
	 */
	abstract public function get_status( $action_id );

	/**
	 * @param string $action_id
	 * @return mixed
	 */
	abstract public function get_claim_id( $action_id );

	/**
	 * @param string $claim_id
	 * @return array
	 */
	abstract public function find_actions_by_claim_id( $claim_id );

	/**
	 * @param string $comparison_operator
	 * @return string
	 */
	protected function validate_sql_comparator( $comparison_operator ) {
		if ( in_array( $comparison_operator, array('!=', '>', '>=', '<', '<=', '=') ) ) {
			return $comparison_operator;
		}
		return '=';
	}

	/**
	 * Get the time MySQL formated date/time string for an action's (next) scheduled date.
	 *
	 * @param ActionScheduler_Action $action
	 * @param DateTime $scheduled_date (optional)
	 * @return string
	 */
	protected function get_scheduled_date_string( ActionScheduler_Action $action, DateTime $scheduled_date = NULL ) {
		$next = null === $scheduled_date ? $action->get_schedule()->get_date() : $scheduled_date;
		if ( ! $next ) {
			$next = date_create();
		}
		$next->setTimezone( new DateTimeZone( 'UTC' ) );

		return $next->format( 'Y-m-d H:i:s' );
	}

	/**
	 * Get the time MySQL formated date/time string for an action's (next) scheduled date.
	 *
	 * @param ActionScheduler_Action $action
	 * @param DateTime $scheduled_date (optional)
	 * @return string
	 */
	protected function get_scheduled_date_string_local( ActionScheduler_Action $action, DateTime $scheduled_date = NULL ) {
		$next = null === $scheduled_date ? $action->get_schedule()->get_date() : $scheduled_date;
		if ( ! $next ) {
			$next = date_create();
		}

		ActionScheduler_TimezoneHelper::set_local_timezone( $next );
		return $next->format( 'Y-m-d H:i:s' );
	}

	/**
	 * Validate that we could decode action arguments.
	 *
	 * @param mixed $args      The decoded arguments.
	 * @param int   $action_id The action ID.
	 *
	 * @throws ActionScheduler_InvalidActionException When the decoded arguments are invalid.
	 */
	protected function validate_args( $args, $action_id ) {
		// Ensure we have an array of args.
		if ( ! is_array( $args ) ) {
			throw ActionScheduler_InvalidActionException::from_decoding_args( $action_id );
		}

		// Validate JSON decoding if possible.
		if ( function_exists( 'json_last_error' ) && JSON_ERROR_NONE !== json_last_error() ) {
			throw ActionScheduler_InvalidActionException::from_decoding_args( $action_id, $args );
		}
	}

	/**
	 * Validate a ActionScheduler_Schedule object.
	 *
	 * @param mixed $schedule  The unserialized ActionScheduler_Schedule object.
	 * @param int   $action_id The action ID.
	 *
	 * @throws ActionScheduler_InvalidActionException When the schedule is invalid.
	 */
	protected function validate_schedule( $schedule, $action_id ) {
		if ( empty( $schedule ) || ! is_a( $schedule, 'ActionScheduler_Schedule' ) ) {
			throw ActionScheduler_InvalidActionException::from_schedule( $action_id, $schedule );
		}
	}

	/**
	 * InnoDB indexes have a maximum size of 767 bytes by default, which is only 191 characters with utf8mb4.
	 *
	 * Previously, AS wasn't concerned about args length, as we used the (unindex) post_content column. However,
	 * with custom tables, we use an indexed VARCHAR column instead.
	 *
	 * @param  ActionScheduler_Action $action Action to be validated.
	 * @throws InvalidArgumentException When json encoded args is too long.
	 */
	protected function validate_action( ActionScheduler_Action $action ) {
		if ( strlen( json_encode( $action->get_args() ) ) > static::$max_args_length ) {
			throw new InvalidArgumentException( sprintf( __( 'ActionScheduler_Action::$args too long. To ensure the args column can be indexed, action args should not be more than %d characters when encoded as JSON.', 'woocommerce' ), static::$max_args_length ) );
		}
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
		$action_ids = true;
		while ( ! empty( $action_ids ) ) {
			$action_ids = $this->query_actions(
				array(
					'hook'     => $hook,
					'status'   => self::STATUS_PENDING,
					'per_page' => 1000,
					'orderby'  => 'action_id',
				)
			);

			$this->bulk_cancel_actions( $action_ids );
		}
	}

	/**
	 * Cancel pending actions by group.
	 *
	 * @since 3.0.0
	 *
	 * @param string $group Group slug.
	 *
	 * @return void
	 */
	public function cancel_actions_by_group( $group ) {
		$action_ids = true;
		while ( ! empty( $action_ids ) ) {
			$action_ids = $this->query_actions(
				array(
					'group'    => $group,
					'status'   => self::STATUS_PENDING,
					'per_page' => 1000,
					'orderby'  => 'action_id',
				)
			);

			$this->bulk_cancel_actions( $action_ids );
		}
	}

	/**
	 * Cancel a set of action IDs.
	 *
	 * @since 3.0.0
	 *
	 * @param array $action_ids List of action IDs.
	 *
	 * @return void
	 */
	private function bulk_cancel_actions( $action_ids ) {
		foreach ( $action_ids as $action_id ) {
			$this->cancel_action( $action_id );
		}

		do_action( 'action_scheduler_bulk_cancel_actions', $action_ids );
	}

	/**
	 * @return array
	 */
	public function get_status_labels() {
		return array(
			self::STATUS_COMPLETE => __( 'Complete', 'woocommerce' ),
			self::STATUS_PENDING  => __( 'Pending', 'woocommerce' ),
			self::STATUS_RUNNING  => __( 'In-progress', 'woocommerce' ),
			self::STATUS_FAILED   => __( 'Failed', 'woocommerce' ),
			self::STATUS_CANCELED => __( 'Canceled', 'woocommerce' ),
		);
	}

	/**
	 * Check if there are any pending scheduled actions due to run.
	 *
	 * @param ActionScheduler_Action $action
	 * @param DateTime $scheduled_date (optional)
	 * @return string
	 */
	public function has_pending_actions_due() {
		$pending_actions = $this->query_actions( array(
			'date'    => as_get_datetime_object(),
			'status'  => ActionScheduler_Store::STATUS_PENDING,
			'orderby' => 'none',
		) );

		return ! empty( $pending_actions );
	}

	/**
	 * Callable initialization function optionally overridden in derived classes.
	 */
	public function init() {}

	/**
	 * Callable function to mark an action as migrated optionally overridden in derived classes.
	 */
	public function mark_migrated( $action_id ) {}

	/**
	 * @return ActionScheduler_Store
	 */
	public static function instance() {
		if ( empty( self::$store ) ) {
			$class = apply_filters( 'action_scheduler_store_class', self::DEFAULT_CLASS );
			self::$store = new $class();
		}
		return self::$store;
	}
}
