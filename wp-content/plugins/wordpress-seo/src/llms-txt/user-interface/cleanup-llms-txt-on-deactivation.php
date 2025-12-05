<?php

namespace Yoast\WP\SEO\Llms_Txt\User_Interface;

use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Llms_Txt\Application\File\Commands\Remove_File_Command_Handler;
use Yoast\WP\SEO\Llms_Txt\Application\File\Llms_Txt_Cron_Scheduler;

/**
 * Trys to clean up the llms.txt file when the plugin is deactivated.
 */
class Cleanup_Llms_Txt_On_Deactivation implements Integration_Interface {

	use No_Conditionals;

	/**
	 * The command handler.
	 *
	 * @var Remove_File_Command_Handler
	 */
	private $command_handler;

	/**
	 * The cron scheduler.
	 *
	 * @var Llms_Txt_Cron_Scheduler
	 */
	private $cron_scheduler;

	/**
	 * Constructor.
	 *
	 * @param Remove_File_Command_Handler $command_handler The command handler.
	 * @param Llms_Txt_Cron_Scheduler     $cron_scheduler  The scheduler.
	 */
	public function __construct(
		Remove_File_Command_Handler $command_handler,
		Llms_Txt_Cron_Scheduler $cron_scheduler
	) {
		$this->command_handler = $command_handler;
		$this->cron_scheduler  = $cron_scheduler;
	}

	/**
	 * Registers the unscheduling of the cron to the deactivation action.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'wpseo_deactivate', [ $this, 'maybe_remove_llms_file' ] );
	}

	/**
	 * Call the command handler to remove the file.
	 *
	 * @return void
	 */
	public function maybe_remove_llms_file(): void {
		$this->command_handler->handle();
		$this->cron_scheduler->unschedule_llms_txt_population();
	}
}
