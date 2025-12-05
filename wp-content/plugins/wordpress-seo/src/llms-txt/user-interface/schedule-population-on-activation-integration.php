<?php

namespace Yoast\WP\SEO\Llms_Txt\User_Interface;

use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Llms_Txt\Application\File\Llms_Txt_Cron_Scheduler;


/**
 * Handles the cron when the plugin is activated.
 */
class Schedule_Population_On_Activation_Integration implements Integration_Interface {

	use No_Conditionals;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper $options_helper
	 */
	private $options_helper;

	/**
	 * The scheduler.
	 *
	 * @var Llms_Txt_Cron_Scheduler $scheduler
	 */
	private $scheduler;

	/**
	 * The constructor.
	 *
	 * @param Llms_Txt_Cron_Scheduler $scheduler      The cron scheduler.
	 * @param Options_Helper          $options_helper The options helper.
	 */
	public function __construct(
		Llms_Txt_Cron_Scheduler $scheduler,
		Options_Helper $options_helper
	) {
		$this->scheduler      = $scheduler;
		$this->options_helper = $options_helper;
	}

	/**
	 * Registers the scheduling of the cron to the activation action.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'wpseo_activate', [ $this, 'schedule_llms_txt_population' ] );
	}

	/**
	 * Schedules the cron if the option is turned on.
	 *
	 * @return void
	 */
	public function schedule_llms_txt_population() {
		if ( $this->options_helper->get( 'enable_llms_txt', false ) === true ) {
			$this->scheduler->schedule_quick_llms_txt_population();
		}
	}
}
