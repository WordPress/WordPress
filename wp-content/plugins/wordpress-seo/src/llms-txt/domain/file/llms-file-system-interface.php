<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Llms_Txt\Domain\File;

/**
 * Interface to describe handeling the llms.txt file.
 */
interface Llms_File_System_Interface {

	/**
	 * Method to set the llms.txt file content.
	 *
	 * @param string $content The content for the file.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function set_file_content( string $content ): bool;

	/**
	 * Method to remove the llms.txt file from the file system.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function remove_file(): bool;

	/**
	 * Gets the contents of the current llms.txt file.
	 *
	 * @return string
	 */
	public function get_file_contents(): string;

	/**
	 * Checks if the llms.txt file exists.
	 *
	 * @return bool Whether the llms.txt file exists.
	 */
	public function file_exists(): bool;
}
