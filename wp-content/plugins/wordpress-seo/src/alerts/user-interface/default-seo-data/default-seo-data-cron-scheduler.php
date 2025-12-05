<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Alerts\User_Interface\Default_Seo_Data;

use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Responsible for scheduling and unscheduling the cron.
 */
class Default_SEO_Data_Cron_Scheduler implements Integration_Interface {

	use No_Conditionals;

	/**
	 * The name of the cron job.
	 */
	public const CRON_HOOK = 'wpseo_detect_default_seo_data';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'admin_init', [ $this, 'schedule_default_seo_data_detection' ] );
		\add_action( 'wpseo_deactivate', [ $this, 'unschedule_default_seo_data_detection' ] );
	}

	/**
	 * Schedules the default SEO data detection cron.
	 *
	 * @return void
	 */
	public function schedule_default_seo_data_detection(): void {
		if ( ! \wp_next_scheduled( self::CRON_HOOK ) ) {
			\wp_schedule_event( ( \time() + \DAY_IN_SECONDS ), 'daily', self::CRON_HOOK );
		}
	}

	/**
	 * Unschedules the default SEO data detection cron.
	 *
	 * @return void
	 */
	public function unschedule_default_seo_data_detection() {
		$scheduled = \wp_next_scheduled( self::CRON_HOOK );
		if ( $scheduled ) {
			\wp_unschedule_event( $scheduled, self::CRON_HOOK );
		}
	}
}
