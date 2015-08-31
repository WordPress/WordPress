<?php
/**
 * @package WPSEO\Admin\Import
 */

/**
 * Class WPSEO_Import
 *
 * Class with functionality to import the Yoast SEO settings
 */
class WPSEO_Import {

	/**
	 * Message about the import
	 *
	 * @var string
	 */
	public $msg = '';

	/**
	 * @var array
	 */
	private $file;

	/**
	 * @var string
	 */
	private $filename;

	/**
	 * @var string
	 */
	private $old_wpseo_version = null;

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @var array
	 */
	private $upload_dir;

	/**
	 * Class constructor
	 */
	public function __construct() {
		if ( ! $this->handle_upload() ) {
			return;
		}

		$this->determine_path();

		if ( ! $this->unzip_file() ) {
			$this->clean_up();

			return;
		}

		$this->parse_options();

		$this->clean_up();
	}

	/**
	 * Handle the file upload
	 *
	 * @return boolean
	 */
	private function handle_upload() {
		$overrides  = array( 'mimes' => array( 'zip' => 'application/zip' ) ); // Explicitly allow zip in multisite.
		$this->file = wp_handle_upload( $_FILES['settings_import_file'], $overrides );

		if ( is_wp_error( $this->file ) ) {
			$this->msg = __( 'Settings could not be imported:', 'wordpress-seo' ) . ' ' . $this->file->get_error_message();

			return false;
		}

		if ( is_array( $this->file ) && isset( $this->file['error'] ) ) {
			$this->msg = __( 'Settings could not be imported:', 'wordpress-seo' ) . ' ' . $this->file['error'];

			return false;
		}

		if ( ! isset( $this->file['file'] ) ) {
			$this->msg = __( 'Settings could not be imported:', 'wordpress-seo' ) . ' ' . __( 'Upload failed.', 'wordpress-seo' );

			return false;
		}

		return true;
	}

	/**
	 * Determine the path to the import file
	 */
	private function determine_path() {
		$this->upload_dir = wp_upload_dir();

		if ( ! defined( 'DIRECTORY_SEPARATOR' ) ) {
			define( 'DIRECTORY_SEPARATOR', '/' );
		}
		$this->path = $this->upload_dir['basedir'] . DIRECTORY_SEPARATOR . 'wpseo-import' . DIRECTORY_SEPARATOR;

		if ( ! isset( $GLOBALS['wp_filesystem'] ) || ! is_object( $GLOBALS['wp_filesystem'] ) ) {
			WP_Filesystem();
		}
	}

	/**
	 * Unzip the file
	 *
	 * @return boolean
	 */
	private function unzip_file() {
		$unzipped = unzip_file( $this->file['file'], $this->path );
		if ( is_wp_error( $unzipped ) ) {
			$this->msg = __( 'Settings could not be imported:', 'wordpress-seo' ) . ' ' . sprintf( __( 'Unzipping failed with error "%s".', 'wordpress-seo' ), $unzipped->get_error_message() );

			return false;
		}

		$this->filename = $this->path . 'settings.ini';
		if ( ! is_file( $this->filename ) || ! is_readable( $this->filename ) ) {
			$this->msg = __( 'Settings could not be imported:', 'wordpress-seo' ) . ' ' . __( 'Unzipping failed - file settings.ini not found.', 'wordpress-seo' );

			return false;
		}

		return true;
	}

	/**
	 * Parse the option file
	 */
	private function parse_options() {
		$options = parse_ini_file( $this->filename, true );

		if ( is_array( $options ) && $options !== array() ) {
			if ( isset( $options['wpseo']['version'] ) && $options['wpseo']['version'] !== '' ) {
				$this->old_wpseo_version = $options['wpseo']['version'];
			}
			foreach ( $options as $name => $opt_group ) {
				$this->parse_option_group( $name, $opt_group, $options );
			}
			$this->msg = __( 'Settings successfully imported.', 'wordpress-seo' );
		}
		else {
			$this->msg = __( 'Settings could not be imported:', 'wordpress-seo' ) . ' ' . __( 'No settings found in file.', 'wordpress-seo' );
		}
	}

	/**
	 * Parse the option group and import it
	 *
	 * @param string $name
	 * @param array  $opt_group
	 * @param array  $options
	 */
	private function parse_option_group( $name, $opt_group, $options ) {
		if ( $name === 'wpseo_taxonomy_meta' ) {
			$opt_group = json_decode( urldecode( $opt_group['wpseo_taxonomy_meta'] ), true );
		}

		// Make sure that the imported options are cleaned/converted on import.
		$option_instance = WPSEO_Options::get_option_instance( $name );
		if ( is_object( $option_instance ) && method_exists( $option_instance, 'import' ) ) {
			$option_instance->import( $opt_group, $this->old_wpseo_version, $options );
		}
		elseif ( WP_DEBUG === true || ( defined( 'WPSEO_DEBUG' ) && WPSEO_DEBUG === true ) ) {
			$this->msg = sprintf( __( 'Setting "%s" is no longer used and has been discarded.', 'wordpress-seo' ), $name );
		}
	}

	/**
	 * Remove the files
	 */
	private function clean_up() {
		if ( file_exists( $this->filename ) && is_writable( $this->filename ) ) {
			unlink( $this->filename );
		}
		if ( file_exists( $this->file['file'] ) && is_writable( $this->file['file'] ) ) {
			unlink( $this->file['file'] );
		}
		if ( file_exists( $this->path ) && is_writable( $this->path ) ) {
			rmdir( $this->path );
		}
	}

}
