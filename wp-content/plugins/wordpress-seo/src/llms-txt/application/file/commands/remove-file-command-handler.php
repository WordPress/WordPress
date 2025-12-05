<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Llms_Txt\Application\File\Commands;

use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Llms_Txt\Infrastructure\File\WordPress_File_System_Adapter;
use Yoast\WP\SEO\Llms_Txt\Infrastructure\File\WordPress_Llms_Txt_Permission_Gate;

/**
 * Handles the removal of the llms.txt
 */
class Remove_File_Command_Handler {

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * The file system adapter.
	 *
	 * @var WordPress_File_System_Adapter
	 */
	private $file_system_adapter;

	/**
	 * The permission gate.
	 *
	 * @var WordPress_Llms_Txt_Permission_Gate $permission_gate
	 */
	private $permission_gate;

	/**
	 * Constructor.
	 *
	 * @param Options_Helper                     $options_helper      The options helper.
	 * @param WordPress_File_System_Adapter      $file_system_adapter The file system adapter.
	 * @param WordPress_Llms_Txt_Permission_Gate $permission_gate     The permission gate.
	 */
	public function __construct(
		Options_Helper $options_helper,
		WordPress_File_System_Adapter $file_system_adapter,
		WordPress_Llms_Txt_Permission_Gate $permission_gate
	) {
		$this->options_helper      = $options_helper;
		$this->file_system_adapter = $file_system_adapter;
		$this->permission_gate     = $permission_gate;
	}

	/**
	 * Runs the command.
	 *
	 * @return void
	 */
	public function handle() {
		if ( $this->permission_gate->is_managed_by_yoast_seo() ) {
			$file_removed = $this->file_system_adapter->remove_file();

			if ( $file_removed ) {
				// Maybe move this to a class if we need to handle this option more often.
				\update_option( Populate_File_Command_Handler::CONTENT_HASH_OPTION, '' );
			}
		}
	}
}
