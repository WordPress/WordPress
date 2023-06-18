<?php
/**
 * File cache class. Store files in the uploads folder.
 * Use the expiration in the get function to control how often a file should be refreshed.
 *
 * @package WPCode
 */

/**
 * WPCode_File_Cache class.
 */
class WPCode_File_Cache {

	/**
	 * Name of the base folder in the Uploads folder.
	 *
	 * @var string
	 */
	private $basedir = 'wpcode';

	/**
	 * Name of the module-specific folder in the base folder.
	 *
	 * @var string
	 */
	private $dirname = 'cache';

	/**
	 * Full upload path, created form the WP uploads folder.
	 *
	 * @var string
	 */
	private $upload_path;

	/**
	 * Write a file to the server with an expiration date.
	 *
	 * @param string $name The key by which to retrieve the data.
	 * @param mixed  $data The data to save - if it's a JSON it should be decoded first as it gets encoded here.
	 *
	 * @return void
	 */
	public function set( $name, $data ) {
		$this->write_file( $this->get_cache_filename_by_key( $name ), wp_json_encode( $data ) );
	}

	/**
	 * Get some data by its name. Checks if the data is expired and if so
	 * returns false so you can update it.
	 *
	 * @param string $name The key of the data to save.
	 * @param int    $ttl For how long since creation should this file be used.
	 *
	 * @return array|false
	 */
	public function get( $name, $ttl = 0 ) {
		$file = $this->get_directory_path( $this->get_cache_filename_by_key( $name ) );

		// If the file doesn't exist there's not much to do.
		if ( ! file_exists( $file ) ) {
			return false;
		}

		// If TTL is 0, always return the file.
		if ( $ttl > 0 && (int) filemtime( $file ) + $ttl < time() ) {
			return false;
		}

		return json_decode( file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	}

	/**
	 * Delete a cached file by its key.
	 *
	 * @param string $key The key to find the file by.
	 *
	 * @return void
	 */
	public function delete( $key ) {
		$file = $this->get_directory_path( $this->get_cache_filename_by_key( $key ) );

		wp_delete_file( $file );
	}

	/**
	 * Basically just adds JSON to the end of the key but we should use this
	 * to also make sure it's a proper filename.
	 *
	 * @param string $name The key.
	 *
	 * @return string
	 */
	private function get_cache_filename_by_key( $name ) {
		return $name . '.json';
	}

	/**
	 * Write a file to the cache folder. Data should already be processed when using this.
	 *
	 * @param string $name The name of the file.
	 * @param string $data The data to write to the file.
	 *
	 * @return void
	 */
	private function write_file( $name, $data ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
		file_put_contents( $this->get_directory_path( $name ), $data );
	}

	/**
	 * Get a reliable path to write files to, it also creates the folders needed if they don't exist.
	 *
	 * @param string $filename The file path.
	 *
	 * @return string
	 */
	private function get_directory_path( $filename ) {
		if ( ! isset( $this->upload_path ) ) {
			$uploads           = wp_upload_dir();
			$base_path         = trailingslashit( $uploads['basedir'] ) . $this->basedir;
			$this->upload_path = $base_path . '/' . $this->dirname;

			if ( ! file_exists( $this->upload_path ) || ! wp_is_writable( $this->upload_path ) ) {
				wp_mkdir_p( $this->upload_path );
				$this->create_index_html_file( $this->upload_path );
			}
			// Ensure the base path has an index file.
			$this->create_index_html_file( $base_path );
		}

		$filepath  = trailingslashit( $this->upload_path ) . $filename;
		$directory = dirname( $filepath );
		if ( $directory !== $this->upload_path && ! file_exists( $directory ) ) {
			wp_mkdir_p( $directory );
			$this->create_index_html_file( $directory );
		}

		$this->create_index_html_file( $this->upload_path );

		return $filepath;
	}

	/**
	 * Create index.html file in the specified directory if it doesn't exist.
	 *
	 * @param string $path The path to the directory.
	 *
	 * @return false|int
	 */
	public static function create_index_html_file( $path ) {
		if ( ! is_dir( $path ) || is_link( $path ) ) {
			return false;
		}

		$index_file = wp_normalize_path( trailingslashit( $path ) . 'index.html' );

		// Do nothing if index.html exists in the directory.
		if ( file_exists( $index_file ) ) {
			return false;
		}

		// Create empty index.html.
		return file_put_contents( $index_file, '' ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	}

	/**
	 * Create .htaccess file in the specified directory if it doesn't exist.
	 *
	 * @param string $path The path to the directory.
	 *
	 * @return false|int
	 */
	public static function create_htaccess_file( $path ) {
		if ( ! is_dir( $path ) || is_link( $path ) ) {
			return false;
		}

		$htaccess_file = wp_normalize_path( trailingslashit( $path ) . '.htaccess' );

		// Do nothing if index.html exists in the directory.
		if ( file_exists( $htaccess_file ) ) {
			return false;
		}

		// Create empty index.html.
		return file_put_contents( $htaccess_file, 'deny from all' ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	}
}
