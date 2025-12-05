<?php

namespace Yoast\WP\SEO\Llms_Txt\User_Interface;

use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Llms_Txt\Application\File\Commands\Populate_File_Command_Handler;
use Yoast\WP\SEO\Llms_Txt\Application\File\Commands\Remove_File_Command_Handler;
use Yoast\WP\SEO\Llms_Txt\Application\File\Llms_Txt_Cron_Scheduler;

/**
 * Cron Callback integration. This handles the actual process of populating the llms.txt on a cron trigger.
 */
class Llms_Txt_Cron_Callback_Integration implements Integration_Interface {

	use No_Conditionals;

	/**
	 * The remove file command handler.
	 *
	 * @var Remove_File_Command_Handler
	 */
	private $remove_file_command_handler;

	/**
	 * The Create Populate Command Handler.
	 *
	 * @var Populate_File_Command_Handler
	 */
	private $populate_file_command_handler;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * The scheduler.
	 *
	 * @var Llms_Txt_Cron_Scheduler
	 */
	private $scheduler;

	/**
	 * Constructor.
	 *
	 * @param Options_Helper                $options_helper                The options helper.
	 * @param Llms_Txt_Cron_Scheduler       $scheduler                     The scheduler.
	 * @param Populate_File_Command_Handler $populate_file_command_handler The populate file command handler.
	 * @param Remove_File_Command_Handler   $remove_file_command_handler   The remove file command handler.
	 */
	public function __construct(
		Options_Helper $options_helper,
		Llms_Txt_Cron_Scheduler $scheduler,
		Populate_File_Command_Handler $populate_file_command_handler,
		Remove_File_Command_Handler $remove_file_command_handler
	) {
		$this->options_helper                = $options_helper;
		$this->scheduler                     = $scheduler;
		$this->populate_file_command_handler = $populate_file_command_handler;
		$this->remove_file_command_handler   = $remove_file_command_handler;
	}

	/**
	 * Registers the hooks with WordPress.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action(
			Llms_Txt_Cron_Scheduler::LLMS_TXT_POPULATION,
			[
				$this,
				'populate_file',
			]
		);
	}

	/**
	 * Populates and creates the file.
	 *
	 * @return void
	 */
	public function populate_file(): void {
		if ( ! \wp_doing_cron() ) {
			return;
		}

		if ( $this->options_helper->get( 'enable_llms_txt', false ) !== true ) {
			$this->scheduler->unschedule_llms_txt_population();
			$this->remove_file_command_handler->handle();

			return;
		}

		$this->populate_file_command_handler->handle();
	}
}
