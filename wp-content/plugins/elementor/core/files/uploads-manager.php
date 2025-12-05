<?php
namespace Elementor\Core\Files;

use Elementor\Core\Base\Base_Object;
use Elementor\Core\Common\Modules\Ajax\Module as Ajax;
use Elementor\Core\Files\File_Types\Base as File_Type_Base;
use Elementor\Core\Files\File_Types\Json;
use Elementor\Core\Files\File_Types\Svg;
use Elementor\Core\Files\File_Types\Zip;
use Elementor\Core\Files\Fonts\Google_Font;
use Elementor\Core\Utils\Exceptions;
use Elementor\Fonts;
use Elementor\User;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor uploads manager.
 *
 * Elementor uploads manager handler class is responsible for handling file uploads that are not done with WP Media.
 *
 * @since 3.3.0
 */
class Uploads_Manager extends Base_Object {

	const UNFILTERED_FILE_UPLOADS_KEY = 'elementor_unfiltered_files_upload';
	const INVALID_FILE_CONTENT = 'Invalid Content In File';

	/**
	 * @var File_Type_Base[]
	 */
	private $file_type_handlers = [];

	private $allowed_file_extensions;

	/**
	 * @var bool
	 */
	private $is_elementor_upload = false;

	/**
	 * @var string
	 */
	private $temp_dir;

	/**
	 * Register File Types
	 *
	 * To Add a new file type to Elementor, with its own handling logic, you need to add it to the $file_types array here.
	 *
	 * @since 3.3.0
	 * @access public
	 */
	public function register_file_types() {
		// All file types that have handlers should be included here.
		$file_types = [
			'json' => new Json(),
			'zip' => new Zip(),
			'svg' => new Svg(),
		];

		foreach ( $file_types as $file_type => $file_handler ) {
			$this->file_type_handlers[ $file_type ] = $file_handler;
		}
	}

	/**
	 * Extract and Validate Zip
	 *
	 * This method accepts a $file array (which minimally should include a 'tmp_name')
	 *
	 * @since 3.3.0
	 * @access public
	 *
	 * @param string $file_path
	 * @param array  $allowed_file_types
	 * @return array|\WP_Error
	 */
	public function extract_and_validate_zip( $file_path, $allowed_file_types = null ) {
		$result = [];

		/** @var Zip $zip_handler - File Type */
		$zip_handler = $this->file_type_handlers['zip'];

		// Returns an array of file paths.
		$extracted = $zip_handler->extract( $file_path, $allowed_file_types );

		if ( is_wp_error( $extracted ) ) {
			return $extracted;
		}

		// If there are no extracted file names, no files passed the extraction validation.
		if ( empty( $extracted['files'] ) ) {
			// TODO: Decide what to do if no files passed the extraction validation
			return new \WP_Error( 'file_error', self::INVALID_FILE_CONTENT );
		}

		$result['extraction_directory'] = $extracted['extraction_directory'];

		foreach ( $extracted['files'] as $extracted_file_path ) {
			// Each file is an array with a 'name' (file path) property.
			if ( ! is_wp_error( $this->validate_file( [ 'tmp_name' => $extracted_file_path ] ) ) ) {
				$result['files'][] = $extracted_file_path;
			}
		}

		return $result;
	}

	/**
	 * Handle Elementor Upload
	 *
	 * This method receives a $file array. If the received file is a Base64 string, the $file array should include a
	 * 'fileData' property containing the string, which is decoded and has its contents stored in a temporary file.
	 * If the $file parameter passed is a standard $file array, the 'name' and 'tmp_name' properties are used for
	 * validation.
	 *
	 * The file goes through validation; if it passes validation, the file is returned. Otherwise, an error is returned.
	 *
	 * @since 3.3.0
	 * @access public
	 *
	 * @param array $data
	 * @param array $allowed_file_extensions Optional. an array of file types that are allowed to pass validation for each
	 * upload.
	 * @return array|\WP_Error
	 */
	public function handle_elementor_upload( array $data, $allowed_file_extensions = null ) {
		// If $file['fileData'] is set, it signals that the passed file is a Base64 string that needs to be decoded and
		// saved to a temporary file.
		if ( isset( $data['fileData'] ) ) {
			$data = $this->save_base64_to_tmp_file( $data, $allowed_file_extensions );
		}

		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$validation_result = $this->validate_file( $data, $allowed_file_extensions );

		if ( is_wp_error( $validation_result ) ) {
			return $validation_result;
		}

		return $data;
	}

	/**
	 * Is Unfiltered Uploads Enabled
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @return bool
	 */
	final public static function are_unfiltered_uploads_enabled() {
		$enabled = (bool) get_option( self::UNFILTERED_FILE_UPLOADS_KEY )
			&& Svg::file_sanitizer_can_run()
			&& User::is_current_user_can_upload_json();

		/**
		 * Allow Unfiltered Files Upload.
		 *
		 * Determines whether to enable unfiltered file uploads.
		 *
		 * @since 3.0.0
		 *
		 * @param bool $enabled Whether upload is enabled or not.
		 */
		$enabled = apply_filters( 'elementor/files/allow_unfiltered_upload', $enabled );

		return $enabled;
	}

	/**
	 * Handle Elementor WP Media Upload
	 *
	 * Runs on the 'wp_handle_upload_prefilter' filter.
	 *
	 * @since 3.2.0
	 * @access public
	 *
	 * @param $file
	 * @return mixed
	 */
	public function handle_elementor_wp_media_upload( $file ) {
		// If it isn't a file uploaded by Elementor, we do not intervene.
		if ( ! $this->is_elementor_wp_media_upload() ) {
			return $file;
		}

		$result = $this->validate_file( $file );

		if ( is_wp_error( $result ) ) {
			$file['error'] = $result->get_error_message();
		}

		return $file;
	}

	/**
	 * Get File Type Handler
	 *
	 * Initialize the proper file type handler according to the file extension
	 * and assign it to the file type handlers array.
	 *
	 * @since 3.3.0
	 * @access public
	 *
	 * @param string|null $file_extension - file extension
	 * @return File_Type_Base[]|File_Type_Base
	 */
	public function get_file_type_handlers( $file_extension = null ) {
		return self::get_items( $this->file_type_handlers, $file_extension );
	}

	/**
	 * Check filetype and ext
	 *
	 * A workaround for upload validation which relies on a PHP extension (fileinfo)
	 * with inconsistent reporting behaviour.
	 * ref: https://core.trac.wordpress.org/ticket/39550
	 * ref: https://core.trac.wordpress.org/ticket/40175
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param $data
	 * @param $file
	 * @param $filename
	 * @param $mimes
	 *
	 * @return mixed
	 */
	public function check_filetype_and_ext( $data, $file, $filename, $mimes ) {
		if ( ! empty( $data['ext'] ) && ! empty( $data['type'] ) ) {
			return $data;
		}

		$wp_file_type = wp_check_filetype( $filename, $mimes );

		$file_type_handlers = $this->get_file_type_handlers();

		if ( isset( $file_type_handlers[ $wp_file_type['ext'] ] ) ) {
			$file_type_handler = $file_type_handlers[ $wp_file_type['ext'] ];

			$data['ext'] = $file_type_handler->get_file_extension();
			$data['type'] = $file_type_handler->get_mime_type();
		}

		return $data;
	}

	/**
	 * Remove File Or Directory
	 *
	 * Directory is deleted recursively with all of its contents (subdirectories and files).
	 *
	 * @since 3.3.0
	 * @access public
	 *
	 * @param string $path
	 */
	public function remove_file_or_dir( $path ) {
		if ( is_dir( $path ) ) {
			$this->remove_directory_with_files( $path );
		} elseif ( is_file( $path ) ) {
			unlink( $path );
		}
	}

	/**
	 * Create Temp File
	 *
	 * Create a random temporary file.
	 *
	 * @since 3.3.0
	 * @access public
	 *
	 * @param string $file_content
	 * @param string $file_name
	 * @return string|\WP_Error
	 */
	public function create_temp_file( $file_content, $file_name ) {
		$file_name = str_replace( ' ', '', sanitize_file_name( $file_name ) );

		if ( empty( $file_name ) ) {
			return new \WP_Error( 'invalid_file_name', esc_html__( 'Invalid file name.', 'elementor' ) );
		}

		$temp_filename = $this->create_unique_dir() . $file_name;

		/**
		 * Temp File Path
		 *
		 * Allows modifying the full path of the temporary file.
		 *
		 * @since 3.7.0
		 *
		 * @param string full path to file
		 */
		$temp_filename = apply_filters( 'elementor/files/temp-file-path', $temp_filename );

		file_put_contents( $temp_filename, $file_content ); // phpcs:ignore

		return $temp_filename;
	}

	/**
	 * Get Temp Directory
	 *
	 * Get the temporary files directory path. If the directory does not exist, this method creates it.
	 *
	 * @since 3.3.0
	 * @access public
	 *
	 * @return string $temp_dir
	 */
	public function get_temp_dir() {
		if ( ! $this->temp_dir ) {
			$wp_upload_dir = wp_upload_dir();

			$temp_dir = implode( DIRECTORY_SEPARATOR, [ $wp_upload_dir['basedir'], 'elementor', 'tmp' ] ) . DIRECTORY_SEPARATOR;

			/**
			 * Temp File Path
			 *
			 * Allows modifying the full path of the temporary file.
			 *
			 * @since 3.7.0
			 *
			 * @param string temporary directory
			 */
			$this->temp_dir = apply_filters( 'elementor/files/temp-dir', $temp_dir );

			if ( ! is_dir( $this->temp_dir ) ) {
				wp_mkdir_p( $this->temp_dir );
			}
		}

		return $this->temp_dir;
	}

	/**
	 * Create Unique Temp Dir
	 *
	 * Create a unique temporary directory
	 *
	 * @since 3.3.0
	 * @access public
	 *
	 * @return string the new directory path
	 */
	public function create_unique_dir() {
		$unique_dir_path = $this->get_temp_dir() . uniqid() . DIRECTORY_SEPARATOR;

		wp_mkdir_p( $unique_dir_path );

		return $unique_dir_path;
	}

	/**
	 * Register Ajax Actions
	 *
	 * Runs on the 'elementor/ajax/register_actions' hook. Receives the AJAX module as a parameter and registers
	 * callbacks for specified action IDs.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param Ajax $ajax
	 */
	public function register_ajax_actions( Ajax $ajax ) {
		$ajax->register_ajax_action( 'enable_unfiltered_files_upload', [ $this, 'enable_unfiltered_files_upload' ] );
		$ajax->register_ajax_action( 'enqueue_google_fonts', [ $this, 'ajax_enqueue_google_fonts' ] );
	}

	/**
	 * Set Unfiltered Files Upload
	 *
	 * @since 3.5.0
	 * @access public
	 */
	public function enable_unfiltered_files_upload() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		update_option( self::UNFILTERED_FILE_UPLOADS_KEY, 1 );
	}

	public function ajax_enqueue_google_fonts( $data ): bool {
		if ( empty( $data['font_name'] ) ) {
			return false;
		}

		$font_type = Fonts::get_font_type( $data['font_name'] );

		if ( Fonts::GOOGLE !== $font_type ) {
			return false;
		}

		Google_Font::enqueue( $data['font_name'] );

		return true;
	}

	/**
	 * Support Unfiltered File Uploads
	 *
	 * When uploading a file within Elementor, this method adds the registered
	 * file types to WordPress' allowed mimes list. This will only happen if the user allowed unfiltered file uploads
	 * in Elementor's settings in the admin dashboard.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param array $allowed_mimes
	 * @return array allowed mime types
	 */
	final public function support_unfiltered_elementor_file_uploads( $allowed_mimes ) {
		if ( $this->is_elementor_upload() && $this->are_unfiltered_uploads_enabled() ) {
			foreach ( $this->file_type_handlers as $file_type_handler ) {
				$allowed_mimes[ $file_type_handler->get_file_extension() ] = $file_type_handler->get_mime_type();
			}
		}

		return $allowed_mimes;
	}

	/**
	 * Set Elementor Upload State
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param $state
	 */
	public function set_elementor_upload_state( $state ) {
		$this->is_elementor_upload = $state;
	}

	/**
	 * Is Elementor Upload
	 *
	 * This method checks if the current session includes a request to upload files made via Elementor.
	 *
	 * @since 3.5.0
	 * @access private
	 *
	 * @return bool
	 */
	private function is_elementor_upload() {
		return $this->is_elementor_upload || $this->is_elementor_media_upload() || $this->is_elementor_wp_media_upload();
	}

	/**
	 * Is Elementor Media Upload
	 *
	 * Checks whether the current request includes uploading files via Elementor which are not destined for the Media
	 * Library.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @return bool
	 */
	public function is_elementor_media_upload() {
		// Sometimes `uploadTypeCaller` passed as a GET parameter when using the WP Media Library REST API, where the
		// whole request body is occupied by the uploaded file.
		return isset( $_REQUEST['uploadTypeCaller'] ) && 'elementor-media-upload' === $_REQUEST['uploadTypeCaller']; // phpcs:ignore
	}

	/**
	 * Is Elementor WP Media Upload
	 *
	 * Checks whether the current request is a request to upload files into the WP Media Library via Elementor.
	 *
	 * @since 3.3.0
	 * @access private
	 *
	 * @return bool
	 */
	private function is_elementor_wp_media_upload() {
		return isset( $_REQUEST['uploadTypeCaller'] ) && 'elementor-wp-media-upload' === $_REQUEST['uploadTypeCaller']; // phpcs:ignore
	}

	/**
	 * Add File Extension To Allowed Extensions List
	 *
	 * @since 3.3.0
	 * @access private
	 *
	 * @param string $file_type
	 */
	private function add_file_extension_to_allowed_extensions_list( $file_type ) {
		$file_handler = $this->file_type_handlers[ $file_type ];

		$file_extension = $file_handler->get_file_extension();

		// Only add the file extension to the list if it doesn't already exist in it.
		if ( ! in_array( $file_extension, $this->allowed_file_extensions, true ) ) {
			$this->allowed_file_extensions[] = $file_extension;
		}
	}

	/**
	 * Save Base64 as File
	 *
	 * Saves a Base64 string as a .tmp file in Elementor's temporary files directory.
	 *
	 * @since 3.3.0
	 * @access private
	 *
	 * @param $file
	 * @param array|null $allowed_file_extensions
	 *
	 * @return array|\WP_Error
	 */
	private function save_base64_to_tmp_file( $file, $allowed_file_extensions = null ) {
		if ( empty( $file['fileName'] ) || empty( $file['fileData'] ) ) {
			return new \WP_Error( 'file_error', self::INVALID_FILE_CONTENT );
		}

		$file_extension = pathinfo( $file['fileName'], PATHINFO_EXTENSION );
		$is_file_type_allowed = $this->is_file_type_allowed( $file_extension, $allowed_file_extensions );

		if ( is_wp_error( $is_file_type_allowed ) ) {
			return $is_file_type_allowed;
		}

		$file_content = base64_decode( $file['fileData'] ); // phpcs:ignore

		// If the decode fails
		if ( ! $file_content ) {
			return new \WP_Error( 'file_error', self::INVALID_FILE_CONTENT );
		}

		$temp_filename = $this->create_temp_file( $file_content, $file['fileName'] );

		if ( is_wp_error( $temp_filename ) ) {
			return $temp_filename;
		}

		return [
			// the original uploaded file name
			'name' => $file['fileName'],
			// The path to the temporary file
			'tmp_name' => $temp_filename,
		];
	}

	/**
	 * Validate File
	 *
	 * @since 3.3.0
	 * @access private
	 *
	 * @param array $file
	 * @param array $file_extensions Optional
	 * @return bool|\WP_Error
	 */
	private function validate_file( array $file, $file_extensions = [] ) {
		$uploaded_file_name = isset( $file['name'] ) ? $file['name'] : $file['tmp_name'];

		$file_extension = pathinfo( $uploaded_file_name, PATHINFO_EXTENSION );

		if ( ! $this->is_elementor_wp_media_upload() ) {
			$is_file_type_allowed = $this->is_file_type_allowed( $file_extension, $file_extensions );

			if ( is_wp_error( $is_file_type_allowed ) ) {
				return $is_file_type_allowed;
			}
		}

		$file_type_handler = $this->get_file_type_handlers( $file_extension );

		// If Elementor does not have a handler for this file type, don't block it.
		if ( ! $file_type_handler ) {
			return true;
		}

		// If there is a File Type Handler for the uploaded file, it means it is a non-standard file type. In this case,
		// we check if unfiltered file uploads are enabled or not before allowing it.
		if ( ! self::are_unfiltered_uploads_enabled() ) {
			$error = 'json' === $file_extension
				? esc_html__( 'You do not have permission to upload JSON files.', 'elementor' )
				: esc_html__( 'This file is not allowed for security reasons.', 'elementor' );
			return new \WP_Error( Exceptions::FORBIDDEN, $error );
		}

		// Here is each file type handler's chance to run its own specific validations
		return $file_type_handler->validate_file( $file );
	}

	/**
	 * Is File Type Allowed
	 *
	 * Checks whether the passed file extension is allowed for upload.
	 *
	 * @since 3.5.0
	 * @access private
	 *
	 * @param $file_extension
	 * @param $filtered_file_extensions
	 * @return bool|\WP_Error
	 */
	private function is_file_type_allowed( $file_extension, $filtered_file_extensions ) {
		$allowed_file_extensions = $this->get_allowed_file_extensions();

		if ( $filtered_file_extensions ) {
			$allowed_file_extensions = array_intersect( $allowed_file_extensions, $filtered_file_extensions );
		}

		$is_allowed = false;

		// Check if the file type (extension) is in the allowed extensions list. If it is a non-standard file type (not
		// enabled by default in WordPress) and unfiltered file uploads are not enabled, it will not be in the allowed
		// file extensions list.
		foreach ( $allowed_file_extensions as $allowed_extension ) {
			if ( preg_match( '/' . $allowed_extension . '/', $file_extension ) ) {
				$is_allowed = true;

				break;
			}
		}

		if ( ! $is_allowed ) {
			$is_allowed = new \WP_Error( Exceptions::FORBIDDEN, 'Uploading this file type is not allowed.' );
		}

		/**
		 * Elementor File Type Allowed
		 *
		 * Allows setting file types
		 *
		 * @since 3.5.0
		 *
		 * @param bool|\WP_Error $is_allowed
		 */
		return apply_filters( 'elementor/files/allow-file-type/' . $file_extension, $is_allowed );
	}

	/**
	 * Remove Directory with Files
	 *
	 * @since 3.3.0
	 * @access private
	 *
	 * @param string $dir
	 * @return bool
	 */
	private function remove_directory_with_files( $dir ) {
		$dir_iterator = new \RecursiveDirectoryIterator( $dir, \RecursiveDirectoryIterator::SKIP_DOTS );

		foreach ( new \RecursiveIteratorIterator( $dir_iterator, \RecursiveIteratorIterator::CHILD_FIRST ) as $name => $item ) {
			if ( is_dir( $name ) ) {
				rmdir( $name );
			} elseif ( is_file( $name ) ) {
				unlink( $name );
			}
		}

		return rmdir( $dir );
	}

	/**
	 * Get Allowed File Extensions
	 *
	 * Retrieve an array containing the list of file extensions allowed for upload.
	 *
	 * @since 3.3.0
	 * @access private
	 *
	 * @return array file extension/s
	 */
	private function get_allowed_file_extensions() {
		if ( ! $this->allowed_file_extensions ) {
			$this->allowed_file_extensions = array_keys( get_allowed_mime_types() );

			foreach ( $this->get_file_type_handlers() as $file_type => $handler ) {
				if ( $handler->is_upload_allowed() ) {
					// Add the file extension to the allowed extensions list only if unfiltered files upload is enabled.
					$this->add_file_extension_to_allowed_extensions_list( $file_type );
				}
			}
		}

		return $this->allowed_file_extensions;
	}

	public function __construct() {
		$this->register_file_types();

		add_filter( 'upload_mimes', [ $this, 'support_unfiltered_elementor_file_uploads' ] );
		add_filter( 'wp_handle_upload_prefilter', [ $this, 'handle_elementor_wp_media_upload' ] );
		add_filter( 'wp_check_filetype_and_ext', [ $this, 'check_filetype_and_ext' ], 10, 4 );

		// Ajax.
		add_action( 'elementor/ajax/register_actions', [ $this, 'register_ajax_actions' ] );
	}
}
