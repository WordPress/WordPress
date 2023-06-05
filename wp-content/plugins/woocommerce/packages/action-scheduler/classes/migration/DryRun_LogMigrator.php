<?php


namespace Action_Scheduler\Migration;

/**
 * Class DryRun_LogMigrator
 *
 * @package Action_Scheduler\Migration
 *
 * @codeCoverageIgnore
 */
class DryRun_LogMigrator extends LogMigrator {
	/**
	 * Simulate migrating an action log.
	 *
	 * @param int $source_action_id Source logger object.
	 * @param int $destination_action_id Destination logger object.
	 */
	public function migrate( $source_action_id, $destination_action_id ) {
		// no-op
	}
}