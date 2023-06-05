<?php

/**
 * Class ActionScheduler_DBStoreMigrator
 *
 * A  class for direct saving of actions to the table data store during migration.
 *
 * @since 3.0.0
 */
class ActionScheduler_DBStoreMigrator extends ActionScheduler_DBStore {

	/**
	 * Save an action with optional last attempt date.
	 *
	 * Normally, saving an action sets its attempted date to 0000-00-00 00:00:00 because when an action is first saved,
	 * it can't have been attempted yet, but migrated completed actions will have an attempted date, so we need to save
	 * that when first saving the action.
	 *
	 * @param ActionScheduler_Action $action
	 * @param \DateTime $scheduled_date Optional date of the first instance to store.
	 * @param \DateTime $last_attempt_date Optional date the action was last attempted.
	 *
	 * @return string The action ID
	 * @throws \RuntimeException When the action is not saved.
	 */
	public function save_action( ActionScheduler_Action $action, \DateTime $scheduled_date = null, \DateTime $last_attempt_date = null ){
		try {
			/** @var \wpdb $wpdb */
			global $wpdb;

			$action_id = parent::save_action( $action, $scheduled_date );

			if ( null !== $last_attempt_date ) {
				$data = [
					'last_attempt_gmt'   => $this->get_scheduled_date_string( $action, $last_attempt_date ),
					'last_attempt_local' => $this->get_scheduled_date_string_local( $action, $last_attempt_date ),
				];

				$wpdb->update( $wpdb->actionscheduler_actions, $data, array( 'action_id' => $action_id ), array( '%s', '%s' ), array( '%d' ) );
			}

			return $action_id;
		} catch ( \Exception $e ) {
			throw new \RuntimeException( sprintf( __( 'Error saving action: %s', 'woocommerce' ), $e->getMessage() ), 0 );
		}
	}
}
