<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Llms_Txt\Infrastructure\File;

use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Llms_Txt\Application\File\Commands\Populate_File_Command_Handler;
use Yoast\WP\SEO\Llms_Txt\Domain\File\Llms_Txt_Permission_Gate_Interface;

/**
 * Handles checks to see if we manage the llms.txt file.
 */
class WordPress_Llms_Txt_Permission_Gate implements Llms_Txt_Permission_Gate_Interface {

	/**
	 * The file system adapter.
	 *
	 * @var WordPress_File_System_Adapter
	 */
	private $file_system_adapter;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * Constructor.
	 *
	 * @param WordPress_File_System_Adapter $file_system_adapter The file system adapter.
	 * @param Options_Helper                $options_helper      The options helper.
	 */
	public function __construct(
		WordPress_File_System_Adapter $file_system_adapter,
		Options_Helper $options_helper
	) {
		$this->file_system_adapter = $file_system_adapter;
		$this->options_helper      = $options_helper;
	}

	/**
	 * Checks if Yoast SEO manages the llms.txt.
	 *
	 * @return bool Checks if Yoast SEO manages the llms.txt.
	 */
	public function is_managed_by_yoast_seo(): bool {
		$stored_hash = \get_option( Populate_File_Command_Handler::CONTENT_HASH_OPTION, '' );

		// If the file does not exist yet, we always regenerate/create it.
		if ( ! $this->file_system_adapter->file_exists() ) {
			return true;
		}

		// This means the file is already there (maybe hand made or another plugin created it). And since we don't have a hash it's not ours.
		if ( $stored_hash === '' ) {
			return false;
		}

		$current_content = $this->file_system_adapter->get_file_contents();

		// If you have a hash, we want to make sure it's the same. This check makes sure the file is not edited by the user.
		return \md5( $current_content ) === $stored_hash;
	}
}
