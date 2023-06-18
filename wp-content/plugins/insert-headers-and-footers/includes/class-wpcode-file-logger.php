<?php
/**
 * This class handles logging errors to files.
 *
 * @package WPCode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WPCode_File_Logger class.
 */
class WPCode_File_Logger {

	/**
	 * Open file handlers.
	 *
	 * @var array
	 */
	protected $handles = array();

	/**
	 * Limit for log file size.
	 *
	 * @var int
	 */
	public $log_size_limit;

	/**
	 * Cache logs that could not be written directly for writing them later.
	 *
	 * @var array
	 */
	protected $cached_logs = array();

	/**
	 * Whether logging is enabled.
	 *
	 * @var bool
	 */
	public $enabled;

	/**
	 * Constructor.
	 *
	 * @param int $log_size_limit File size limit for log files in bytes.
	 */
	public function __construct( $log_size_limit = null ) {
		if ( null === $log_size_limit ) {
			$log_size_limit = 5 * 1024 * 1024;
		}

		$this->log_size_limit = apply_filters( 'wpcode_log_file_size_limit', $log_size_limit );

		add_action( 'plugins_loaded', array( $this, 'write_cached_logs' ) );
	}

	/**
	 * Close all open file handles.
	 */
	public function __destruct() {
		foreach ( $this->handles as $handle ) {
			if ( is_resource( $handle ) ) {
				fclose( $handle ); // @codingStandardsIgnoreLine.
			}
		}
	}

	/**
	 * Write cached logs.
	 */
	public function write_cached_logs() {
		foreach ( $this->cached_logs as $log ) {
			$this->write( $log['entry'], $log['handle'] );
		}
	}

	/**
	 * Format an entry for the log file.
	 *
	 * @param int    $timestamp The timestamp for the log entry.
	 * @param string $message The error message.
	 *
	 * @return mixed|void
	 */
	public function format_entry( $timestamp, $message ) {
		$time_string = date( 'c', $timestamp );// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
		$entry       = "{$time_string} {$message}";

		return apply_filters(
			'wpcode_format_log_entry',
			$entry,
			array(
				'timestamp' => $timestamp,
				'message'   => $message,
			)
		);
	}

	/**
	 * Handle a direct request to log something & format it before writing.
	 *
	 * @param int    $timestamp The timestamp for the log entry.
	 * @param string $message The error message.
	 * @param string $handle The log handle.
	 *
	 * @return void
	 */
	public function handle( $timestamp, $message, $handle ) {
		if ( ! $this->is_enabled() ) {
			return;
		}
		$entry = $this->format_entry( $timestamp, $message );
		$this->write( $entry, $handle );
	}

	/**
	 * Write an entry to a log file by its handle.
	 *
	 * @param string $entry The entry text.
	 * @param string $handle The log handle.
	 *
	 * @return bool
	 */
	public function write( $entry, $handle ) {
		$result = false;

		if ( $this->should_rotate( $handle ) ) {
			$this->log_rotate( $handle );
		}

		if ( $this->open( $handle ) && is_resource( $this->handles[ $handle ] ) ) {
			$result = fwrite( $this->handles[ $handle ], $entry . PHP_EOL ); // @codingStandardsIgnoreLine.
		} else {
			$this->cache_log( $entry, $handle );
		}

		return false !== $result;
	}

	/**
	 * Should the log file be rotated? Returns true if the file is above the size limit.
	 *
	 * @param string $handle Log handle.
	 *
	 * @return bool
	 */
	public function should_rotate( $handle ) {
		$file = self::get_file_path( $handle );
		if ( $file ) {
			if ( $this->is_open( $handle ) ) {
				$file_stat = fstat( $this->handles[ $handle ] );

				return $file_stat['size'] > $this->log_size_limit;
			} elseif ( file_exists( $file ) ) {
				return filesize( $file ) > $this->log_size_limit;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Rotate log files by adding a suffix up to 10.
	 *
	 * @param string $handle Log handle.
	 */
	protected function log_rotate( $handle ) {
		for ( $i = 8; $i >= 0; $i -- ) {
			$this->increment_log_infix( $handle, $i );
		}
		$this->increment_log_infix( $handle );
	}

	/**
	 * Increment a log file suffix.
	 *
	 * @param string   $handle Log handle.
	 * @param null|int $number Optional. Default null. Log suffix number to be incremented.
	 *
	 * @return bool True if increment was successful, otherwise false.
	 */
	protected function increment_log_infix( $handle, $number = null ) {
		if ( null === $number ) {
			$suffix      = '';
			$next_suffix = '.0';
		} else {
			$suffix      = '.' . $number;
			$next_suffix = '.' . ( $number + 1 );
		}

		$rename_from = self::get_file_path( "{$handle}{$suffix}" );
		$rename_to   = self::get_file_path( "{$handle}{$next_suffix}" );

		if ( $this->is_open( $rename_from ) ) {
			$this->close( $rename_from );
		}

		if ( is_writable( $rename_from ) ) { // phpcs:ignore WordPress.VIP.FileSystemWritesDisallow.file_ops_is_writable
			return rename( $rename_from, $rename_to ); // phpcs:ignore WordPress.VIP.FileSystemWritesDisallow.file_ops_rename
		} else {
			return false;
		}

	}

	/**
	 * Open a log file for writing.
	 *
	 * @param string $handle Log handle.
	 *
	 * @return bool
	 */
	public function open( $handle = 'log' ) {
		if ( $this->is_open( $handle ) ) {
			return true;
		}

		$file = self::get_file_path( $handle );

		if ( $file ) {
			if ( ! file_exists( $file ) ) {
				$temphandle = @fopen( $file, 'w+' ); // @codingStandardsIgnoreLine.
				if ( is_resource( $temphandle ) ) {
					@fclose( $temphandle ); // @codingStandardsIgnoreLine.

					if ( defined( 'FS_CHMOD_FILE' ) ) {
						@chmod( $file, FS_CHMOD_FILE ); // @codingStandardsIgnoreLine.
					}
				}
			}

			$resource = @fopen( $file, 'a' ); // @codingStandardsIgnoreLine.

			if ( $resource ) {
				$this->handles[ $handle ] = $resource;

				return true;
			}
		}

		return false;
	}

	/**
	 * Close a log file.
	 *
	 * @param string $handle Log handle.
	 *
	 * @return void
	 */
	public function close( $handle = 'log' ) {
		if ( $this->is_open( $handle ) ) {
			@fclose( $this->handles[ $handle ] ); // @codingStandardsIgnoreLine.
			unset( $this->handles[ $handle ] );
		}
	}

	/**
	 * Check if a handle is open.
	 *
	 * @param string $handle Log handle.
	 *
	 * @return bool True if $handle is open.
	 */
	protected function is_open( $handle ) {
		return array_key_exists( $handle, $this->handles ) && is_resource( $this->handles[ $handle ] );
	}

	/**
	 * Get the file path for a log handle.
	 *
	 * @param string $handle Log handle.
	 *
	 * @return string
	 */
	public static function get_file_path( $handle ) {

		$date_suffix = date( 'Y-m-d', time() );// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
		$hash_suffix = wp_hash( $handle );
		$filename    = sanitize_file_name( implode( '-', array( $handle, $date_suffix, $hash_suffix ) ) . '.log' );

		return self::get_log_dir() . $filename;
	}

	/**
	 * Get the log directory path while also making sure it exists & we have an index.html and a .htaccess file in it.
	 *
	 * @return string
	 */
	public static function get_log_dir() {
		$uploads = wp_upload_dir();

		$base_path = trailingslashit( $uploads['basedir'] ) . 'wpcode-logs/';
		if ( ! file_exists( $base_path ) ) {
			wp_mkdir_p( $base_path );
			WPCode_File_Cache::create_index_html_file( $base_path );
			WPCode_File_Cache::create_htaccess_file( $base_path );
		}

		return $base_path;
	}

	/**
	 * Store a log entry in the cache for writing later.
	 *
	 * @param string $entry Log entry.
	 * @param string $handle Log handle.
	 *
	 * @return void
	 */
	public function cache_log( $entry, $handle ) {
		$this->cached_logs[] = array(
			'entry'  => $entry,
			'handle' => $handle,
		);
	}

	/**
	 * Is logging errors enabled?
	 *
	 * @return bool
	 */
	public function is_enabled() {
		if ( ! isset( $this->enabled ) ) {
			$this->enabled = boolval( wpcode()->settings->get_option( 'error_logging', false ) );
		}

		return $this->enabled;
	}

	/**
	 * Go through the logs folder and return a list of all .log files.
	 *
	 * @return array
	 */
	public function get_logs() {
		$logs = array();

		$files = glob( self::get_log_dir() . '*.log' );

		if ( ! empty( $files ) ) {
			foreach ( $files as $file ) {
				$logs[] = array(
					'filename' => basename( $file ),
					'path'     => $file,
					'size'     => filesize( $file ),
				);
			}
		}

		return $logs;
	}

	/**
	 * Delete a log file by its name.
	 *
	 * @param string $name Log name.
	 *
	 * @return void
	 */
	public function delete_log( $name ) {
		// If the file doesn't include the .log extension, add it.
		if ( ! preg_match( '/\.log$/', $name ) ) {
			$name .= '.log';
		}

		$real_file_path = realpath( self::get_log_dir() . $name );
		$real_base_path = realpath( self::get_log_dir() ) . DIRECTORY_SEPARATOR;
		if ( false === $real_file_path || strpos( $real_file_path, $real_base_path ) !== 0 ) {
			// Traversal attempt.
			return;
		}

		// Delete the file from the server.
		@unlink( $real_file_path ); // @codingStandardsIgnoreLine.
	}

}
