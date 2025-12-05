<?php

/**
 * Class for filesystem operations.
 */
class WPCF7_Filesystem {

	/**
	 * The singleton instance.
	 *
	 * @var WPCF7_Filesystem
	 */
	private static $instance;

	/**
	 * Filesystem object.
	 *
	 * @var WP_Filesystem_Base
	 */
	private $filesystem;


	/**
	 * Retrieves the singleton instance.
	 */
	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->connect();
	}


	/**
	 * Connects to the filesystem.
	 *
	 * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
	 */
	private function connect() {
		global $wp_filesystem;

		if ( $this->filesystem ) {
			return false;
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

		ob_start();
		$credentials = request_filesystem_credentials( '' );
		ob_end_clean();

		if ( false === $credentials or ! WP_Filesystem( $credentials ) ) {
			wp_trigger_error(
				__FUNCTION__,
				__( 'Could not access filesystem.', 'contact-form-7' )
			);
		}

		if ( $wp_filesystem instanceof WP_Filesystem_Base ) {
			$this->filesystem = $wp_filesystem;
		} else {
			$this->filesystem = new WP_Filesystem_Direct( 1 );
		}

		if ( ! defined( 'FS_CHMOD_DIR' ) ) {
			define( 'FS_CHMOD_DIR', fileperms( ABSPATH ) & 0777 | 0755 );
		}

		if ( ! defined( 'FS_CHMOD_FILE' ) ) {
			define( 'FS_CHMOD_FILE', fileperms( ABSPATH . 'index.php' ) & 0777 | 0644 );
		}
	}


	/**
	 * Changes filesystem permissions.
	 *
	 * @param string $file Path to the file.
	 * @param int|false $mode Optional. The permissions as octal number.
	 * @param bool $recursive Optional. If set to true,
	 *             changes file permissions recursively. Default false.
	 * @return bool True on success, false on failure.
	 */
	public function chmod( $file, $mode = false, $recursive = false ) {
		return $this->filesystem->chmod( $file, $mode, $recursive );
	}


	/**
	 * Deletes a file or directory.
	 *
	 * @param string $file Path to the file or directory.
	 * @param bool $recursive Optional. If set to true, deletes
	 *             files and folders recursively. Default false.
	 * @param string|false $type Type of resource.
	 *                     'f' for file, 'd' for directory. Default false.
	 * @return bool True on success, false on failure.
	 */
	public function delete( $file, $recursive = false, $type = false ) {
		return $this->filesystem->delete( $file, $recursive, $type );
	}


	/**
	 * Writes a string to a file.
	 *
	 * @param string $file Path to the file where to write the data.
	 * @param string $contents The data to write.
	 * @param int $mode The file permissions as octal number.
	 * @return bool True on success, false on failure.
	 */
	public function put_contents( $file, $contents, $mode = false ) {
		return $this->filesystem->put_contents( $file, $contents, $mode );
	}

}
