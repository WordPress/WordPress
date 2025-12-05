<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Llms_Txt\Application\File;

use Yoast\WP\SEO\Helpers\Options_Helper;

/**
 * Responsible for scheduling and unscheduling the cron.
 */
class Llms_Txt_Cron_Scheduler {

	/**
	 * The name of the cron job.
	 */
	public const LLMS_TXT_POPULATION = 'wpseo_llms_txt_population';

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * Constructor.
	 *
	 * @param Options_Helper $options_helper The options helper.
	 */
	public function __construct(
		Options_Helper $options_helper
	) {
		$this->options_helper = $options_helper;
	}

	/**
	 * Schedules the llms txt population cron a week from now.
	 *
	 * @return void
	 */
	public function schedule_weekly_llms_txt_population(): void {
		if ( $this->options_helper->get( 'enable_llms_txt', false ) !== true ) {
			return;
		}

		if ( ! \wp_next_scheduled( self::LLMS_TXT_POPULATION ) ) {
			\wp_schedule_event( ( \time() + \WEEK_IN_SECONDS ), 'weekly', self::LLMS_TXT_POPULATION );
		}
	}

	/**
	 * Schedules the llms txt population cron 5 minutes from now.
	 *
	 * @return void
	 */
	public function schedule_quick_llms_txt_population(): void {
		if ( $this->options_helper->get( 'enable_llms_txt', false ) !== true ) {
			return;
		}

		if ( \wp_next_scheduled( self::LLMS_TXT_POPULATION ) ) {
			$this->unschedule_llms_txt_population();
		}

		\wp_schedule_event( ( \time() + ( \MINUTE_IN_SECONDS * 5 ) ), 'weekly', self::LLMS_TXT_POPULATION );
	}

	/**
	 * Unschedules the llms txt population cron.
	 *
	 * @return void
	 */
	public function unschedule_llms_txt_population() {
		$scheduled = \wp_next_scheduled( self::LLMS_TXT_POPULATION );
		if ( $scheduled ) {
			\wp_unschedule_event( $scheduled, self::LLMS_TXT_POPULATION );
		}
	}
}
