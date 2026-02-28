<?php

use Sentry\State\Scope;
use Sentry\State\HubInterface;

/**
 * Sentry for WordPress Action Scheduler Integration
 *
 * @see      https://wordpress.org/plugins/action-scheduler/
 *
 * @internal This class is not part of the public API and may be removed or changed at any time.
 */
final class WP_Sentry_Action_Scheduler_Integration {
	/**
	 * Holds the class instance.
	 *
	 * @var WP_Sentry_Action_Scheduler_Integration
	 */
	private static $instance;

	/**
	 * Get the Sentry Action Scheduler Integration instance.
	 *
	 * @return WP_Sentry_Action_Scheduler_Integration
	 */
	public static function get_instance(): WP_Sentry_Action_Scheduler_Integration {
		return self::$instance ?: self::$instance = new self;
	}

	/**
	 * Class constructor.
	 */
	protected function __construct() {
		add_action( 'action_scheduler_failed_execution', [ $this, 'handle_action_scheduler_failure' ], 10, 3 );
	}

	/**
	 * Capture and send Action Scheduler failures to Sentry.
	 *
	 * @param int        $action_id The action ID that failed.
	 * @param \Throwable $e         The exception that was thrown.
	 * @param string     $context   The context in which the exception was thrown.
	 *
	 * @return void
	 */
	public function handle_action_scheduler_failure( $action_id, $e, $context ): void {
		// This should never happen, but let's be safe.
		if ( ! $e instanceof Throwable ) {
			return;
		}

		wp_sentry_safe(
			function ( HubInterface $client ) use ( $action_id, $e, $context ) {
				$client->withScope( function ( Scope $scope ) use ( $client, $action_id, $e, $context ) {
					$scope->setContext(
						'action_scheduler',
						[
							'action_id' => (string) $action_id,
							'context'   => (string) $context,
						]
					);

					$client->captureException( $e );
				} );
			}
		);
	}
}
