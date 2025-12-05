<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Llms_Txt\Infrastructure\File;

use Yoast\WP\SEO\Llms_Txt\Domain\File\Llms_File_System_Interface;

/**
 * Adapter class for handling file system operations in a WordPress environment.
 */
class WordPress_File_System_Adapter implements Llms_File_System_Interface {

	/**
	 * Creates a file and writes the specified content to it.
	 *
	 * @param string $content The content to write into the file.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function set_file_content( string $content ): bool {
		if ( $this->is_file_system_available() ) {
			global $wp_filesystem;
			$result = $wp_filesystem->put_contents(
				$this->get_llms_file_path(),
				$content,
				\FS_CHMOD_FILE
			);

			return $result;
		}

		return false;
	}

	/**
	 * Removes the llms.txt from the filesystem.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function remove_file(): bool {
		if ( $this->is_file_system_available() ) {
			global $wp_filesystem;
			$result = $wp_filesystem->delete( $this->get_llms_file_path() );

			return $result;
		}

		return false;
	}

	/**
	 * Gets the contents of the current llms.txt file.
	 *
	 * @return string The content of the file.
	 */
	public function get_file_contents(): string {
		if ( $this->is_file_system_available() ) {
			global $wp_filesystem;

			return $wp_filesystem->get_contents( $this->get_llms_file_path() );
		}

		return '';
	}

	/**
	 * Checks if the llms.txt file exists.
	 *
	 * @return bool Whether the llms.txt file exists.
	 */
	public function file_exists(): bool {
		if ( $this->is_file_system_available() ) {
			global $wp_filesystem;

			return $wp_filesystem->exists( $this->get_llms_file_path() );
		}

		return false;
	}

	/**
	 * Checks if the file system is available.
	 *
	 * @return bool If the file system is available.
	 */
	private function is_file_system_available(): ?bool {
		if ( ! \function_exists( 'WP_Filesystem' ) ) {
			require_once \ABSPATH . 'wp-admin/includes/file.php';
		}

		return \WP_Filesystem();
	}

	/**
	 * Creates the path to the llms.txt file.
	 *
	 * @return string
	 */
	private function get_llms_file_path(): string {

		$llms_filesystem_path = \get_home_path();

		// phpcs:disable WordPress.Security.ValidatedSanitizedInput -- Reason: This is how we used this for the robots.txt file as well.
		if ( ! \is_writable( $llms_filesystem_path ) && ! empty( $_SERVER['DOCUMENT_ROOT'] ) ) {
			$llms_filesystem_path = $_SERVER['DOCUMENT_ROOT'];
		}
		// phpcs:enable WordPress.Security.ValidatedSanitizedInput

		/**
		 * Filter: 'wpseo_llmstxt_filesystem_path' - Allows editing the filesystem path of the llmst.txt file to account for server restrictions to the filesystem.
		 *
		 * @param string $llms_filesystem_path The filesystem path of the llmst.txt file that defaults to get_home_path() or the $_SERVER['DOCUMENT_ROOT'] if the home path is not writeable.
		 */
		$llms_filesystem_path = \apply_filters( 'wpseo_llmstxt_filesystem_path', $llms_filesystem_path );

		return \trailingslashit( $llms_filesystem_path ) . 'llms.txt';
	}
}
