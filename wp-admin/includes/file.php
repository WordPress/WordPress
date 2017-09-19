<?php
/**
 * Filesystem API: Top-level functionality
 *
 * Functions for reading, writing, modifying, and deleting files on the file system.
 * Includes functionality for theme-specific files as well as operations for uploading,
 * archiving, and rendering output when necessary.
 *
 * @package WordPress
 * @subpackage Filesystem
 * @since 2.3.0
 */

/** The descriptions for theme files. */
$wp_file_descriptions = array(
	'functions.php'         => __( 'Theme Functions' ),
	'header.php'            => __( 'Theme Header' ),
	'footer.php'            => __( 'Theme Footer' ),
	'sidebar.php'           => __( 'Sidebar' ),
	'comments.php'          => __( 'Comments' ),
	'searchform.php'        => __( 'Search Form' ),
	'404.php'               => __( '404 Template' ),
	'link.php'              => __( 'Links Template' ),
	// Archives
	'index.php'             => __( 'Main Index Template' ),
	'archive.php'           => __( 'Archives' ),
	'author.php'            => __( 'Author Template' ),
	'taxonomy.php'          => __( 'Taxonomy Template' ),
	'category.php'          => __( 'Category Template' ),
	'tag.php'               => __( 'Tag Template' ),
	'home.php'              => __( 'Posts Page' ),
	'search.php'            => __( 'Search Results' ),
	'date.php'              => __( 'Date Template' ),
	// Content
	'singular.php'          => __( 'Singular Template' ),
	'single.php'            => __( 'Single Post' ),
	'page.php'              => __( 'Single Page' ),
	'front-page.php'        => __( 'Static Front Page' ),
	// Attachments
	'attachment.php'        => __( 'Attachment Template' ),
	'image.php'             => __( 'Image Attachment Template' ),
	'video.php'             => __( 'Video Attachment Template' ),
	'audio.php'             => __( 'Audio Attachment Template' ),
	'application.php'       => __( 'Application Attachment Template' ),
	// Embeds
	'embed.php'             => __( 'Embed Template' ),
	'embed-404.php'         => __( 'Embed 404 Template' ),
	'embed-content.php'     => __( 'Embed Content Template' ),
	'header-embed.php'      => __( 'Embed Header Template' ),
	'footer-embed.php'      => __( 'Embed Footer Template' ),
	// Stylesheets
	'style.css'             => __( 'Stylesheet' ),
	'editor-style.css'      => __( 'Visual Editor Stylesheet' ),
	'editor-style-rtl.css'  => __( 'Visual Editor RTL Stylesheet' ),
	'rtl.css'               => __( 'RTL Stylesheet' ),
	// Other
	'my-hacks.php'          => __( 'my-hacks.php (legacy hacks support)' ),
	'.htaccess'             => __( '.htaccess (for rewrite rules )' ),
	// Deprecated files
	'wp-layout.css'         => __( 'Stylesheet' ),
	'wp-comments.php'       => __( 'Comments Template' ),
	'wp-comments-popup.php' => __( 'Popup Comments Template' ),
	'comments-popup.php'    => __( 'Popup Comments' ),
);

/**
 * Get the description for standard WordPress theme files and other various standard
 * WordPress files
 *
 * @since 1.5.0
 *
 * @global array $wp_file_descriptions
 * @param string $file Filesystem path or filename
 * @return string Description of file from $wp_file_descriptions or basename of $file if description doesn't exist.
 *                Appends 'Page Template' to basename of $file if the file is a page template
 */
function get_file_description( $file ) {
	global $wp_file_descriptions, $allowed_files;

	$dirname = pathinfo( $file, PATHINFO_DIRNAME );

	$file_path = $allowed_files[ $file ];
	if ( isset( $wp_file_descriptions[ basename( $file ) ] ) && '.' === $dirname ) {
		return $wp_file_descriptions[ basename( $file ) ];
	} elseif ( file_exists( $file_path ) && is_file( $file_path ) ) {
		$template_data = implode( '', file( $file_path ) );
		if ( preg_match( '|Template Name:(.*)$|mi', $template_data, $name ) ) {
			return sprintf( __( '%s Page Template' ), _cleanup_header_comment( $name[1] ) );
		}
	}

	return trim( basename( $file ) );
}

/**
 * Get the absolute filesystem path to the root of the WordPress installation
 *
 * @since 1.5.0
 *
 * @return string Full filesystem path to the root of the WordPress installation
 */
function get_home_path() {
	$home    = set_url_scheme( get_option( 'home' ), 'http' );
	$siteurl = set_url_scheme( get_option( 'siteurl' ), 'http' );
	if ( ! empty( $home ) && 0 !== strcasecmp( $home, $siteurl ) ) {
		$wp_path_rel_to_home = str_ireplace( $home, '', $siteurl ); /* $siteurl - $home */
		$pos = strripos( str_replace( '\\', '/', $_SERVER['SCRIPT_FILENAME'] ), trailingslashit( $wp_path_rel_to_home ) );
		$home_path = substr( $_SERVER['SCRIPT_FILENAME'], 0, $pos );
		$home_path = trailingslashit( $home_path );
	} else {
		$home_path = ABSPATH;
	}

	return str_replace( '\\', '/', $home_path );
}

/**
 * Returns a listing of all files in the specified folder and all subdirectories up to 100 levels deep.
 * The depth of the recursiveness can be controlled by the $levels param.
 *
 * @since 2.6.0
 *
 * @param string $folder Optional. Full path to folder. Default empty.
 * @param int    $levels Optional. Levels of folders to follow, Default 100 (PHP Loop limit).
 * @return bool|array False on failure, Else array of files
 */
function list_files( $folder = '', $levels = 100 ) {
	if ( empty($folder) )
		return false;

	if ( ! $levels )
		return false;

	$files = array();
	if ( $dir = @opendir( $folder ) ) {
		while (($file = readdir( $dir ) ) !== false ) {
			if ( in_array($file, array('.', '..') ) )
				continue;
			if ( is_dir( $folder . '/' . $file ) ) {
				$files2 = list_files( $folder . '/' . $file, $levels - 1);
				if ( $files2 )
					$files = array_merge($files, $files2 );
				else
					$files[] = $folder . '/' . $file . '/';
			} else {
				$files[] = $folder . '/' . $file;
			}
		}
	}
	@closedir( $dir );
	return $files;
}

/**
 * Returns a filename of a Temporary unique file.
 * Please note that the calling function must unlink() this itself.
 *
 * The filename is based off the passed parameter or defaults to the current unix timestamp,
 * while the directory can either be passed as well, or by leaving it blank, default to a writable temporary directory.
 *
 * @since 2.6.0
 *
 * @param string $filename Optional. Filename to base the Unique file off. Default empty.
 * @param string $dir      Optional. Directory to store the file in. Default empty.
 * @return string a writable filename
 */
function wp_tempnam( $filename = '', $dir = '' ) {
	if ( empty( $dir ) ) {
		$dir = get_temp_dir();
	}

	if ( empty( $filename ) || '.' == $filename || '/' == $filename || '\\' == $filename ) {
		$filename = time();
	}

	// Use the basename of the given file without the extension as the name for the temporary directory
	$temp_filename = basename( $filename );
	$temp_filename = preg_replace( '|\.[^.]*$|', '', $temp_filename );

	// If the folder is falsey, use its parent directory name instead.
	if ( ! $temp_filename ) {
		return wp_tempnam( dirname( $filename ), $dir );
	}

	// Suffix some random data to avoid filename conflicts
	$temp_filename .= '-' . wp_generate_password( 6, false );
	$temp_filename .= '.tmp';
	$temp_filename = $dir . wp_unique_filename( $dir, $temp_filename );

	$fp = @fopen( $temp_filename, 'x' );
	if ( ! $fp && is_writable( $dir ) && file_exists( $temp_filename ) ) {
		return wp_tempnam( $filename, $dir );
	}
	if ( $fp ) {
		fclose( $fp );
	}

	return $temp_filename;
}

/**
 * Make sure that the file that was requested to edit, is allowed to be edited
 *
 * Function will die if you are not allowed to edit the file
 *
 * @since 1.5.0
 *
 * @param string $file file the users is attempting to edit
 * @param array $allowed_files Array of allowed files to edit, $file must match an entry exactly
 * @return string|null
 */
function validate_file_to_edit( $file, $allowed_files = '' ) {
	$code = validate_file( $file, $allowed_files );

	if (!$code )
		return $file;

	switch ( $code ) {
		case 1 :
			wp_die( __( 'Sorry, that file cannot be edited.' ) );

		// case 2 :
		// wp_die( __('Sorry, can&#8217;t call files with their real path.' ));

		case 3 :
			wp_die( __( 'Sorry, that file cannot be edited.' ) );
	}
}

/**
 * Handle PHP uploads in WordPress, sanitizing file names, checking extensions for mime type,
 * and moving the file to the appropriate directory within the uploads directory.
 *
 * @access private
 * @since 4.0.0
 *
 * @see wp_handle_upload_error
 *
 * @param array       $file      Reference to a single element of $_FILES. Call the function once for each uploaded file.
 * @param array|false $overrides An associative array of names => values to override default variables. Default false.
 * @param string      $time      Time formatted in 'yyyy/mm'.
 * @param string      $action    Expected value for $_POST['action'].
 * @return array On success, returns an associative array of file attributes. On failure, returns
 *               $overrides['upload_error_handler'](&$file, $message ) or array( 'error'=>$message ).
 */
function _wp_handle_upload( &$file, $overrides, $time, $action ) {
	// The default error handler.
	if ( ! function_exists( 'wp_handle_upload_error' ) ) {
		function wp_handle_upload_error( &$file, $message ) {
			return array( 'error' => $message );
		}
	}

	/**
	 * Filters the data for a file before it is uploaded to WordPress.
	 *
	 * The dynamic portion of the hook name, `$action`, refers to the post action.
	 *
	 * @since 2.9.0 as 'wp_handle_upload_prefilter'.
	 * @since 4.0.0 Converted to a dynamic hook with `$action`.
	 *
	 * @param array $file An array of data for a single file.
	 */
	$file = apply_filters( "{$action}_prefilter", $file );

	// You may define your own function and pass the name in $overrides['upload_error_handler']
	$upload_error_handler = 'wp_handle_upload_error';
	if ( isset( $overrides['upload_error_handler'] ) ) {
		$upload_error_handler = $overrides['upload_error_handler'];
	}

	// You may have had one or more 'wp_handle_upload_prefilter' functions error out the file. Handle that gracefully.
	if ( isset( $file['error'] ) && ! is_numeric( $file['error'] ) && $file['error'] ) {
		return call_user_func_array( $upload_error_handler, array( &$file, $file['error'] ) );
	}

	// Install user overrides. Did we mention that this voids your warranty?

	// You may define your own function and pass the name in $overrides['unique_filename_callback']
	$unique_filename_callback = null;
	if ( isset( $overrides['unique_filename_callback'] ) ) {
		$unique_filename_callback = $overrides['unique_filename_callback'];
	}

	/*
	 * This may not have orignially been intended to be overrideable,
	 * but historically has been.
	 */
	if ( isset( $overrides['upload_error_strings'] ) ) {
		$upload_error_strings = $overrides['upload_error_strings'];
	} else {
		// Courtesy of php.net, the strings that describe the error indicated in $_FILES[{form field}]['error'].
		$upload_error_strings = array(
			false,
			__( 'The uploaded file exceeds the upload_max_filesize directive in php.ini.' ),
			__( 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.' ),
			__( 'The uploaded file was only partially uploaded.' ),
			__( 'No file was uploaded.' ),
			'',
			__( 'Missing a temporary folder.' ),
			__( 'Failed to write file to disk.' ),
			__( 'File upload stopped by extension.' )
		);
	}

	// All tests are on by default. Most can be turned off by $overrides[{test_name}] = false;
	$test_form = isset( $overrides['test_form'] ) ? $overrides['test_form'] : true;
	$test_size = isset( $overrides['test_size'] ) ? $overrides['test_size'] : true;

	// If you override this, you must provide $ext and $type!!
	$test_type = isset( $overrides['test_type'] ) ? $overrides['test_type'] : true;
	$mimes = isset( $overrides['mimes'] ) ? $overrides['mimes'] : false;

	// A correct form post will pass this test.
	if ( $test_form && ( ! isset( $_POST['action'] ) || ( $_POST['action'] != $action ) ) ) {
		return call_user_func_array( $upload_error_handler, array( &$file, __( 'Invalid form submission.' ) ) );
	}
	// A successful upload will pass this test. It makes no sense to override this one.
	if ( isset( $file['error'] ) && $file['error'] > 0 ) {
		return call_user_func_array( $upload_error_handler, array( &$file, $upload_error_strings[ $file['error'] ] ) );
	}

	$test_file_size = 'wp_handle_upload' === $action ? $file['size'] : filesize( $file['tmp_name'] );
	// A non-empty file will pass this test.
	if ( $test_size && ! ( $test_file_size > 0 ) ) {
		if ( is_multisite() ) {
			$error_msg = __( 'File is empty. Please upload something more substantial.' );
		} else {
			$error_msg = __( 'File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your php.ini or by post_max_size being defined as smaller than upload_max_filesize in php.ini.' );
		}
		return call_user_func_array( $upload_error_handler, array( &$file, $error_msg ) );
	}

	// A properly uploaded file will pass this test. There should be no reason to override this one.
	$test_uploaded_file = 'wp_handle_upload' === $action ? @ is_uploaded_file( $file['tmp_name'] ) : @ is_file( $file['tmp_name'] );
	if ( ! $test_uploaded_file ) {
		return call_user_func_array( $upload_error_handler, array( &$file, __( 'Specified file failed upload test.' ) ) );
	}

	// A correct MIME type will pass this test. Override $mimes or use the upload_mimes filter.
	if ( $test_type ) {
		$wp_filetype = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], $mimes );
		$ext = empty( $wp_filetype['ext'] ) ? '' : $wp_filetype['ext'];
		$type = empty( $wp_filetype['type'] ) ? '' : $wp_filetype['type'];
		$proper_filename = empty( $wp_filetype['proper_filename'] ) ? '' : $wp_filetype['proper_filename'];

		// Check to see if wp_check_filetype_and_ext() determined the filename was incorrect
		if ( $proper_filename ) {
			$file['name'] = $proper_filename;
		}
		if ( ( ! $type || !$ext ) && ! current_user_can( 'unfiltered_upload' ) ) {
			return call_user_func_array( $upload_error_handler, array( &$file, __( 'Sorry, this file type is not permitted for security reasons.' ) ) );
		}
		if ( ! $type ) {
			$type = $file['type'];
		}
	} else {
		$type = '';
	}

	/*
	 * A writable uploads dir will pass this test. Again, there's no point
	 * overriding this one.
	 */
	if ( ! ( ( $uploads = wp_upload_dir( $time ) ) && false === $uploads['error'] ) ) {
		return call_user_func_array( $upload_error_handler, array( &$file, $uploads['error'] ) );
	}

	$filename = wp_unique_filename( $uploads['path'], $file['name'], $unique_filename_callback );

	// Move the file to the uploads dir.
	$new_file = $uploads['path'] . "/$filename";
	if ( 'wp_handle_upload' === $action ) {
		$move_new_file = @ move_uploaded_file( $file['tmp_name'], $new_file );
	} else {
		// use copy and unlink because rename breaks streams.
		$move_new_file = @ copy( $file['tmp_name'], $new_file );
		unlink( $file['tmp_name'] );
	}

	if ( false === $move_new_file ) {
		if ( 0 === strpos( $uploads['basedir'], ABSPATH ) ) {
			$error_path = str_replace( ABSPATH, '', $uploads['basedir'] ) . $uploads['subdir'];
		} else {
			$error_path = basename( $uploads['basedir'] ) . $uploads['subdir'];
		}
		return $upload_error_handler( $file, sprintf( __('The uploaded file could not be moved to %s.' ), $error_path ) );
	}

	// Set correct file permissions.
	$stat = stat( dirname( $new_file ));
	$perms = $stat['mode'] & 0000666;
	@ chmod( $new_file, $perms );

	// Compute the URL.
	$url = $uploads['url'] . "/$filename";

	if ( is_multisite() ) {
		delete_transient( 'dirsize_cache' );
	}

	/**
	 * Filters the data array for the uploaded file.
	 *
	 * @since 2.1.0
	 *
	 * @param array  $upload {
	 *     Array of upload data.
	 *
	 *     @type string $file Filename of the newly-uploaded file.
	 *     @type string $url  URL of the uploaded file.
	 *     @type string $type File type.
	 * }
	 * @param string $context The type of upload action. Values include 'upload' or 'sideload'.
	 */
	return apply_filters( 'wp_handle_upload', array(
		'file' => $new_file,
		'url'  => $url,
		'type' => $type
	), 'wp_handle_sideload' === $action ? 'sideload' : 'upload' );
}

/**
 * Wrapper for _wp_handle_upload().
 *
 * Passes the {@see 'wp_handle_upload'} action.
 *
 * @since 2.0.0
 *
 * @see _wp_handle_upload()
 *
 * @param array      $file      Reference to a single element of `$_FILES`. Call the function once for
 *                              each uploaded file.
 * @param array|bool $overrides Optional. An associative array of names=>values to override default
 *                              variables. Default false.
 * @param string     $time      Optional. Time formatted in 'yyyy/mm'. Default null.
 * @return array On success, returns an associative array of file attributes. On failure, returns
 *               $overrides['upload_error_handler'](&$file, $message ) or array( 'error'=>$message ).
 */
function wp_handle_upload( &$file, $overrides = false, $time = null ) {
	/*
	 *  $_POST['action'] must be set and its value must equal $overrides['action']
	 *  or this:
	 */
	$action = 'wp_handle_upload';
	if ( isset( $overrides['action'] ) ) {
		$action = $overrides['action'];
	}

	return _wp_handle_upload( $file, $overrides, $time, $action );
}

/**
 * Wrapper for _wp_handle_upload().
 *
 * Passes the {@see 'wp_handle_sideload'} action.
 *
 * @since 2.6.0
 *
 * @see _wp_handle_upload()
 *
 * @param array      $file      An array similar to that of a PHP `$_FILES` POST array
 * @param array|bool $overrides Optional. An associative array of names=>values to override default
 *                              variables. Default false.
 * @param string     $time      Optional. Time formatted in 'yyyy/mm'. Default null.
 * @return array On success, returns an associative array of file attributes. On failure, returns
 *               $overrides['upload_error_handler'](&$file, $message ) or array( 'error'=>$message ).
 */
function wp_handle_sideload( &$file, $overrides = false, $time = null ) {
	/*
	 *  $_POST['action'] must be set and its value must equal $overrides['action']
	 *  or this:
	 */
	$action = 'wp_handle_sideload';
	if ( isset( $overrides['action'] ) ) {
		$action = $overrides['action'];
	}
	return _wp_handle_upload( $file, $overrides, $time, $action );
}


/**
 * Downloads a URL to a local temporary file using the WordPress HTTP Class.
 * Please note, That the calling function must unlink() the file.
 *
 * @since 2.5.0
 *
 * @param string $url the URL of the file to download
 * @param int $timeout The timeout for the request to download the file default 300 seconds
 * @return mixed WP_Error on failure, string Filename on success.
 */
function download_url( $url, $timeout = 300 ) {
	//WARNING: The file is not automatically deleted, The script must unlink() the file.
	if ( ! $url )
		return new WP_Error('http_no_url', __('Invalid URL Provided.'));

	$url_filename = basename( parse_url( $url, PHP_URL_PATH ) );

	$tmpfname = wp_tempnam( $url_filename );
	if ( ! $tmpfname )
		return new WP_Error('http_no_file', __('Could not create Temporary file.'));

	$response = wp_safe_remote_get( $url, array( 'timeout' => $timeout, 'stream' => true, 'filename' => $tmpfname ) );

	if ( is_wp_error( $response ) ) {
		unlink( $tmpfname );
		return $response;
	}

	if ( 200 != wp_remote_retrieve_response_code( $response ) ){
		unlink( $tmpfname );
		return new WP_Error( 'http_404', trim( wp_remote_retrieve_response_message( $response ) ) );
	}

	$content_md5 = wp_remote_retrieve_header( $response, 'content-md5' );
	if ( $content_md5 ) {
		$md5_check = verify_file_md5( $tmpfname, $content_md5 );
		if ( is_wp_error( $md5_check ) ) {
			unlink( $tmpfname );
			return $md5_check;
		}
	}

	return $tmpfname;
}

/**
 * Calculates and compares the MD5 of a file to its expected value.
 *
 * @since 3.7.0
 *
 * @param string $filename The filename to check the MD5 of.
 * @param string $expected_md5 The expected MD5 of the file, either a base64 encoded raw md5, or a hex-encoded md5
 * @return bool|object WP_Error on failure, true on success, false when the MD5 format is unknown/unexpected
 */
function verify_file_md5( $filename, $expected_md5 ) {
	if ( 32 == strlen( $expected_md5 ) )
		$expected_raw_md5 = pack( 'H*', $expected_md5 );
	elseif ( 24 == strlen( $expected_md5 ) )
		$expected_raw_md5 = base64_decode( $expected_md5 );
	else
		return false; // unknown format

	$file_md5 = md5_file( $filename, true );

	if ( $file_md5 === $expected_raw_md5 )
		return true;

	return new WP_Error( 'md5_mismatch', sprintf( __( 'The checksum of the file (%1$s) does not match the expected checksum value (%2$s).' ), bin2hex( $file_md5 ), bin2hex( $expected_raw_md5 ) ) );
}

/**
 * Unzips a specified ZIP file to a location on the Filesystem via the WordPress Filesystem Abstraction.
 * Assumes that WP_Filesystem() has already been called and set up. Does not extract a root-level __MACOSX directory, if present.
 *
 * Attempts to increase the PHP Memory limit to 256M before uncompressing,
 * However, The most memory required shouldn't be much larger than the Archive itself.
 *
 * @since 2.5.0
 *
 * @global WP_Filesystem_Base $wp_filesystem Subclass
 *
 * @param string $file Full path and filename of zip archive
 * @param string $to Full path on the filesystem to extract archive to
 * @return mixed WP_Error on failure, True on success
 */
function unzip_file($file, $to) {
	global $wp_filesystem;

	if ( ! $wp_filesystem || !is_object($wp_filesystem) )
		return new WP_Error('fs_unavailable', __('Could not access filesystem.'));

	// Unzip can use a lot of memory, but not this much hopefully.
	wp_raise_memory_limit( 'admin' );

	$needed_dirs = array();
	$to = trailingslashit($to);

	// Determine any parent dir's needed (of the upgrade directory)
	if ( ! $wp_filesystem->is_dir($to) ) { //Only do parents if no children exist
		$path = preg_split('![/\\\]!', untrailingslashit($to));
		for ( $i = count($path); $i >= 0; $i-- ) {
			if ( empty($path[$i]) )
				continue;

			$dir = implode('/', array_slice($path, 0, $i+1) );
			if ( preg_match('!^[a-z]:$!i', $dir) ) // Skip it if it looks like a Windows Drive letter.
				continue;

			if ( ! $wp_filesystem->is_dir($dir) )
				$needed_dirs[] = $dir;
			else
				break; // A folder exists, therefor, we dont need the check the levels below this
		}
	}

	/**
	 * Filters whether to use ZipArchive to unzip archives.
	 *
	 * @since 3.0.0
	 *
	 * @param bool $ziparchive Whether to use ZipArchive. Default true.
	 */
	if ( class_exists( 'ZipArchive', false ) && apply_filters( 'unzip_file_use_ziparchive', true ) ) {
		$result = _unzip_file_ziparchive($file, $to, $needed_dirs);
		if ( true === $result ) {
			return $result;
		} elseif ( is_wp_error($result) ) {
			if ( 'incompatible_archive' != $result->get_error_code() )
				return $result;
		}
	}
	// Fall through to PclZip if ZipArchive is not available, or encountered an error opening the file.
	return _unzip_file_pclzip($file, $to, $needed_dirs);
}

/**
 * This function should not be called directly, use unzip_file instead. Attempts to unzip an archive using the ZipArchive class.
 * Assumes that WP_Filesystem() has already been called and set up.
 *
 * @since 3.0.0
 * @see unzip_file
 * @access private
 *
 * @global WP_Filesystem_Base $wp_filesystem Subclass
 *
 * @param string $file Full path and filename of zip archive
 * @param string $to Full path on the filesystem to extract archive to
 * @param array $needed_dirs A partial list of required folders needed to be created.
 * @return mixed WP_Error on failure, True on success
 */
function _unzip_file_ziparchive($file, $to, $needed_dirs = array() ) {
	global $wp_filesystem;

	$z = new ZipArchive();

	$zopen = $z->open( $file, ZIPARCHIVE::CHECKCONS );
	if ( true !== $zopen )
		return new WP_Error( 'incompatible_archive', __( 'Incompatible Archive.' ), array( 'ziparchive_error' => $zopen ) );

	$uncompressed_size = 0;

	for ( $i = 0; $i < $z->numFiles; $i++ ) {
		if ( ! $info = $z->statIndex($i) )
			return new WP_Error( 'stat_failed_ziparchive', __( 'Could not retrieve file from archive.' ) );

		if ( '__MACOSX/' === substr($info['name'], 0, 9) ) // Skip the OS X-created __MACOSX directory
			continue;

		if ( 0 !== validate_file( $info['name'] ) ) {
			return new WP_Error( 'invalid_file_ziparchive', __( 'Could not extract file from archive.' ), $info['name'] );
		}

		$uncompressed_size += $info['size'];

		if ( '/' === substr( $info['name'], -1 ) ) {
			// Directory.
			$needed_dirs[] = $to . untrailingslashit( $info['name'] );
		} elseif ( '.' !== $dirname = dirname( $info['name'] ) ) {
			// Path to a file.
			$needed_dirs[] = $to . untrailingslashit( $dirname );
		}
	}

	/*
	 * disk_free_space() could return false. Assume that any falsey value is an error.
	 * A disk that has zero free bytes has bigger problems.
	 * Require we have enough space to unzip the file and copy its contents, with a 10% buffer.
	 */
	if ( wp_doing_cron() ) {
		$available_space = @disk_free_space( WP_CONTENT_DIR );
		if ( $available_space && ( $uncompressed_size * 2.1 ) > $available_space )
			return new WP_Error( 'disk_full_unzip_file', __( 'Could not copy files. You may have run out of disk space.' ), compact( 'uncompressed_size', 'available_space' ) );
	}

	$needed_dirs = array_unique($needed_dirs);
	foreach ( $needed_dirs as $dir ) {
		// Check the parent folders of the folders all exist within the creation array.
		if ( untrailingslashit($to) == $dir ) // Skip over the working directory, We know this exists (or will exist)
			continue;
		if ( strpos($dir, $to) === false ) // If the directory is not within the working directory, Skip it
			continue;

		$parent_folder = dirname($dir);
		while ( !empty($parent_folder) && untrailingslashit($to) != $parent_folder && !in_array($parent_folder, $needed_dirs) ) {
			$needed_dirs[] = $parent_folder;
			$parent_folder = dirname($parent_folder);
		}
	}
	asort($needed_dirs);

	// Create those directories if need be:
	foreach ( $needed_dirs as $_dir ) {
		// Only check to see if the Dir exists upon creation failure. Less I/O this way.
		if ( ! $wp_filesystem->mkdir( $_dir, FS_CHMOD_DIR ) && ! $wp_filesystem->is_dir( $_dir ) ) {
			return new WP_Error( 'mkdir_failed_ziparchive', __( 'Could not create directory.' ), substr( $_dir, strlen( $to ) ) );
		}
	}
	unset($needed_dirs);

	for ( $i = 0; $i < $z->numFiles; $i++ ) {
		if ( ! $info = $z->statIndex($i) )
			return new WP_Error( 'stat_failed_ziparchive', __( 'Could not retrieve file from archive.' ) );

		if ( '/' == substr($info['name'], -1) ) // directory
			continue;

		if ( '__MACOSX/' === substr($info['name'], 0, 9) ) // Don't extract the OS X-created __MACOSX directory files
			continue;

		$contents = $z->getFromIndex($i);
		if ( false === $contents )
			return new WP_Error( 'extract_failed_ziparchive', __( 'Could not extract file from archive.' ), $info['name'] );

		if ( ! $wp_filesystem->put_contents( $to . $info['name'], $contents, FS_CHMOD_FILE) )
			return new WP_Error( 'copy_failed_ziparchive', __( 'Could not copy file.' ), $info['name'] );
	}

	$z->close();

	return true;
}

/**
 * This function should not be called directly, use unzip_file instead. Attempts to unzip an archive using the PclZip library.
 * Assumes that WP_Filesystem() has already been called and set up.
 *
 * @since 3.0.0
 * @see unzip_file
 * @access private
 *
 * @global WP_Filesystem_Base $wp_filesystem Subclass
 *
 * @param string $file Full path and filename of zip archive
 * @param string $to Full path on the filesystem to extract archive to
 * @param array $needed_dirs A partial list of required folders needed to be created.
 * @return mixed WP_Error on failure, True on success
 */
function _unzip_file_pclzip($file, $to, $needed_dirs = array()) {
	global $wp_filesystem;

	mbstring_binary_safe_encoding();

	require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');

	$archive = new PclZip($file);

	$archive_files = $archive->extract(PCLZIP_OPT_EXTRACT_AS_STRING);

	reset_mbstring_encoding();

	// Is the archive valid?
	if ( !is_array($archive_files) )
		return new WP_Error('incompatible_archive', __('Incompatible Archive.'), $archive->errorInfo(true));

	if ( 0 == count($archive_files) )
		return new WP_Error( 'empty_archive_pclzip', __( 'Empty archive.' ) );

	$uncompressed_size = 0;

	// Determine any children directories needed (From within the archive)
	foreach ( $archive_files as $file ) {
		if ( '__MACOSX/' === substr($file['filename'], 0, 9) ) // Skip the OS X-created __MACOSX directory
			continue;

		$uncompressed_size += $file['size'];

		$needed_dirs[] = $to . untrailingslashit( $file['folder'] ? $file['filename'] : dirname($file['filename']) );
	}

	/*
	 * disk_free_space() could return false. Assume that any falsey value is an error.
	 * A disk that has zero free bytes has bigger problems.
	 * Require we have enough space to unzip the file and copy its contents, with a 10% buffer.
	 */
	if ( wp_doing_cron() ) {
		$available_space = @disk_free_space( WP_CONTENT_DIR );
		if ( $available_space && ( $uncompressed_size * 2.1 ) > $available_space )
			return new WP_Error( 'disk_full_unzip_file', __( 'Could not copy files. You may have run out of disk space.' ), compact( 'uncompressed_size', 'available_space' ) );
	}

	$needed_dirs = array_unique($needed_dirs);
	foreach ( $needed_dirs as $dir ) {
		// Check the parent folders of the folders all exist within the creation array.
		if ( untrailingslashit($to) == $dir ) // Skip over the working directory, We know this exists (or will exist)
			continue;
		if ( strpos($dir, $to) === false ) // If the directory is not within the working directory, Skip it
			continue;

		$parent_folder = dirname($dir);
		while ( !empty($parent_folder) && untrailingslashit($to) != $parent_folder && !in_array($parent_folder, $needed_dirs) ) {
			$needed_dirs[] = $parent_folder;
			$parent_folder = dirname($parent_folder);
		}
	}
	asort($needed_dirs);

	// Create those directories if need be:
	foreach ( $needed_dirs as $_dir ) {
		// Only check to see if the dir exists upon creation failure. Less I/O this way.
		if ( ! $wp_filesystem->mkdir( $_dir, FS_CHMOD_DIR ) && ! $wp_filesystem->is_dir( $_dir ) )
			return new WP_Error( 'mkdir_failed_pclzip', __( 'Could not create directory.' ), substr( $_dir, strlen( $to ) ) );
	}
	unset($needed_dirs);

	// Extract the files from the zip
	foreach ( $archive_files as $file ) {
		if ( $file['folder'] )
			continue;

		if ( '__MACOSX/' === substr($file['filename'], 0, 9) ) // Don't extract the OS X-created __MACOSX directory files
			continue;

		if ( 0 !== validate_file( $file['filename'] ) ) {
			return new WP_Error( 'invalid_file_pclzip', __( 'Could not extract file from archive.' ), $file['filename'] );
		}

		if ( ! $wp_filesystem->put_contents( $to . $file['filename'], $file['content'], FS_CHMOD_FILE) )
			return new WP_Error( 'copy_failed_pclzip', __( 'Could not copy file.' ), $file['filename'] );
	}
	return true;
}

/**
 * Copies a directory from one location to another via the WordPress Filesystem Abstraction.
 * Assumes that WP_Filesystem() has already been called and setup.
 *
 * @since 2.5.0
 *
 * @global WP_Filesystem_Base $wp_filesystem Subclass
 *
 * @param string $from source directory
 * @param string $to destination directory
 * @param array $skip_list a list of files/folders to skip copying
 * @return mixed WP_Error on failure, True on success.
 */
function copy_dir($from, $to, $skip_list = array() ) {
	global $wp_filesystem;

	$dirlist = $wp_filesystem->dirlist($from);

	$from = trailingslashit($from);
	$to = trailingslashit($to);

	foreach ( (array) $dirlist as $filename => $fileinfo ) {
		if ( in_array( $filename, $skip_list ) )
			continue;

		if ( 'f' == $fileinfo['type'] ) {
			if ( ! $wp_filesystem->copy($from . $filename, $to . $filename, true, FS_CHMOD_FILE) ) {
				// If copy failed, chmod file to 0644 and try again.
				$wp_filesystem->chmod( $to . $filename, FS_CHMOD_FILE );
				if ( ! $wp_filesystem->copy($from . $filename, $to . $filename, true, FS_CHMOD_FILE) )
					return new WP_Error( 'copy_failed_copy_dir', __( 'Could not copy file.' ), $to . $filename );
			}
		} elseif ( 'd' == $fileinfo['type'] ) {
			if ( !$wp_filesystem->is_dir($to . $filename) ) {
				if ( !$wp_filesystem->mkdir($to . $filename, FS_CHMOD_DIR) )
					return new WP_Error( 'mkdir_failed_copy_dir', __( 'Could not create directory.' ), $to . $filename );
			}

			// generate the $sub_skip_list for the subdirectory as a sub-set of the existing $skip_list
			$sub_skip_list = array();
			foreach ( $skip_list as $skip_item ) {
				if ( 0 === strpos( $skip_item, $filename . '/' ) )
					$sub_skip_list[] = preg_replace( '!^' . preg_quote( $filename, '!' ) . '/!i', '', $skip_item );
			}

			$result = copy_dir($from . $filename, $to . $filename, $sub_skip_list);
			if ( is_wp_error($result) )
				return $result;
		}
	}
	return true;
}

/**
 * Initialises and connects the WordPress Filesystem Abstraction classes.
 * This function will include the chosen transport and attempt connecting.
 *
 * Plugins may add extra transports, And force WordPress to use them by returning
 * the filename via the {@see 'filesystem_method_file'} filter.
 *
 * @since 2.5.0
 *
 * @global WP_Filesystem_Base $wp_filesystem Subclass
 *
 * @param array|false  $args                         Optional. Connection args, These are passed directly to
 *                                                   the `WP_Filesystem_*()` classes. Default false.
 * @param string|false $context                      Optional. Context for get_filesystem_method(). Default false.
 * @param bool         $allow_relaxed_file_ownership Optional. Whether to allow Group/World writable. Default false.
 * @return null|bool false on failure, true on success.
 */
function WP_Filesystem( $args = false, $context = false, $allow_relaxed_file_ownership = false ) {
	global $wp_filesystem;

	require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php');

	$method = get_filesystem_method( $args, $context, $allow_relaxed_file_ownership );

	if ( ! $method )
		return false;

	if ( ! class_exists( "WP_Filesystem_$method" ) ) {

		/**
		 * Filters the path for a specific filesystem method class file.
		 *
		 * @since 2.6.0
		 *
		 * @see get_filesystem_method()
		 *
		 * @param string $path   Path to the specific filesystem method class file.
		 * @param string $method The filesystem method to use.
		 */
		$abstraction_file = apply_filters( 'filesystem_method_file', ABSPATH . 'wp-admin/includes/class-wp-filesystem-' . $method . '.php', $method );

		if ( ! file_exists($abstraction_file) )
			return;

		require_once($abstraction_file);
	}
	$method = "WP_Filesystem_$method";

	$wp_filesystem = new $method($args);

	//Define the timeouts for the connections. Only available after the construct is called to allow for per-transport overriding of the default.
	if ( ! defined('FS_CONNECT_TIMEOUT') )
		define('FS_CONNECT_TIMEOUT', 30);
	if ( ! defined('FS_TIMEOUT') )
		define('FS_TIMEOUT', 30);

	if ( is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code() )
		return false;

	if ( !$wp_filesystem->connect() )
		return false; //There was an error connecting to the server.

	// Set the permission constants if not already set.
	if ( ! defined('FS_CHMOD_DIR') )
		define('FS_CHMOD_DIR', ( fileperms( ABSPATH ) & 0777 | 0755 ) );
	if ( ! defined('FS_CHMOD_FILE') )
		define('FS_CHMOD_FILE', ( fileperms( ABSPATH . 'index.php' ) & 0777 | 0644 ) );

	return true;
}

/**
 * Determines which method to use for reading, writing, modifying, or deleting
 * files on the filesystem.
 *
 * The priority of the transports are: Direct, SSH2, FTP PHP Extension, FTP Sockets
 * (Via Sockets class, or `fsockopen()`). Valid values for these are: 'direct', 'ssh2',
 * 'ftpext' or 'ftpsockets'.
 *
 * The return value can be overridden by defining the `FS_METHOD` constant in `wp-config.php`,
 * or filtering via {@see 'filesystem_method'}.
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants
 *
 * Plugins may define a custom transport handler, See WP_Filesystem().
 *
 * @since 2.5.0
 *
 * @global callable $_wp_filesystem_direct_method
 *
 * @param array  $args                         Optional. Connection details. Default empty array.
 * @param string $context                      Optional. Full path to the directory that is tested
 *                                             for being writable. Default empty.
 * @param bool   $allow_relaxed_file_ownership Optional. Whether to allow Group/World writable.
 *                                             Default false.
 * @return string The transport to use, see description for valid return values.
 */
function get_filesystem_method( $args = array(), $context = '', $allow_relaxed_file_ownership = false ) {
	$method = defined('FS_METHOD') ? FS_METHOD : false; // Please ensure that this is either 'direct', 'ssh2', 'ftpext' or 'ftpsockets'

	if ( ! $context ) {
		$context = WP_CONTENT_DIR;
	}

	// If the directory doesn't exist (wp-content/languages) then use the parent directory as we'll create it.
	if ( WP_LANG_DIR == $context && ! is_dir( $context ) ) {
		$context = dirname( $context );
	}

	$context = trailingslashit( $context );

	if ( ! $method ) {

		$temp_file_name = $context . 'temp-write-test-' . time();
		$temp_handle = @fopen($temp_file_name, 'w');
		if ( $temp_handle ) {

			// Attempt to determine the file owner of the WordPress files, and that of newly created files
			$wp_file_owner = $temp_file_owner = false;
			if ( function_exists('fileowner') ) {
				$wp_file_owner = @fileowner( __FILE__ );
				$temp_file_owner = @fileowner( $temp_file_name );
			}

			if ( $wp_file_owner !== false && $wp_file_owner === $temp_file_owner ) {
				// WordPress is creating files as the same owner as the WordPress files,
				// this means it's safe to modify & create new files via PHP.
				$method = 'direct';
				$GLOBALS['_wp_filesystem_direct_method'] = 'file_owner';
			} elseif ( $allow_relaxed_file_ownership ) {
				// The $context directory is writable, and $allow_relaxed_file_ownership is set, this means we can modify files
				// safely in this directory. This mode doesn't create new files, only alter existing ones.
				$method = 'direct';
				$GLOBALS['_wp_filesystem_direct_method'] = 'relaxed_ownership';
			}

			@fclose($temp_handle);
			@unlink($temp_file_name);
		}
 	}

	if ( ! $method && isset($args['connection_type']) && 'ssh' == $args['connection_type'] && extension_loaded('ssh2') && function_exists('stream_get_contents') ) $method = 'ssh2';
	if ( ! $method && extension_loaded('ftp') ) $method = 'ftpext';
	if ( ! $method && ( extension_loaded('sockets') || function_exists('fsockopen') ) ) $method = 'ftpsockets'; //Sockets: Socket extension; PHP Mode: FSockopen / fwrite / fread

	/**
	 * Filters the filesystem method to use.
	 *
	 * @since 2.6.0
	 *
	 * @param string $method  Filesystem method to return.
	 * @param array  $args    An array of connection details for the method.
	 * @param string $context Full path to the directory that is tested for being writable.
	 * @param bool   $allow_relaxed_file_ownership Whether to allow Group/World writable.
	 */
	return apply_filters( 'filesystem_method', $method, $args, $context, $allow_relaxed_file_ownership );
}

/**
 * Displays a form to the user to request for their FTP/SSH details in order
 * to connect to the filesystem.
 *
 * All chosen/entered details are saved, excluding the password.
 *
 * Hostnames may be in the form of hostname:portnumber (eg: wordpress.org:2467)
 * to specify an alternate FTP/SSH port.
 *
 * Plugins may override this form by returning true|false via the {@see 'request_filesystem_credentials'} filter.
 *
 * @since 2.5.0
 * @since 4.6.0 The `$context` parameter default changed from `false` to an empty string.
 *
 * @global string $pagenow
 *
 * @param string $form_post                    The URL to post the form to.
 * @param string $type                         Optional. Chosen type of filesystem. Default empty.
 * @param bool   $error                        Optional. Whether the current request has failed to connect.
 *                                             Default false.
 * @param string $context                      Optional. Full path to the directory that is tested for being
 *                                             writable. Default empty.
 * @param array  $extra_fields                 Optional. Extra `POST` fields to be checked for inclusion in
 *                                             the post. Default null.
 * @param bool   $allow_relaxed_file_ownership Optional. Whether to allow Group/World writable. Default false.
 *
 * @return bool False on failure, true on success.
 */
function request_filesystem_credentials( $form_post, $type = '', $error = false, $context = '', $extra_fields = null, $allow_relaxed_file_ownership = false ) {
	global $pagenow;

	/**
	 * Filters the filesystem credentials form output.
	 *
	 * Returning anything other than an empty string will effectively short-circuit
	 * output of the filesystem credentials form, returning that value instead.
	 *
	 * @since 2.5.0
	 * @since 4.6.0 The `$context` parameter default changed from `false` to an empty string.
	 *
	 * @param mixed  $output                       Form output to return instead. Default empty.
	 * @param string $form_post                    The URL to post the form to.
	 * @param string $type                         Chosen type of filesystem.
	 * @param bool   $error                        Whether the current request has failed to connect.
	 *                                             Default false.
	 * @param string $context                      Full path to the directory that is tested for
	 *                                             being writable.
	 * @param bool   $allow_relaxed_file_ownership Whether to allow Group/World writable.
	 *                                             Default false.
	 * @param array  $extra_fields                 Extra POST fields.
	 */
	$req_cred = apply_filters( 'request_filesystem_credentials', '', $form_post, $type, $error, $context, $extra_fields, $allow_relaxed_file_ownership );
	if ( '' !== $req_cred )
		return $req_cred;

	if ( empty($type) ) {
		$type = get_filesystem_method( array(), $context, $allow_relaxed_file_ownership );
	}

	if ( 'direct' == $type )
		return true;

	if ( is_null( $extra_fields ) )
		$extra_fields = array( 'version', 'locale' );

	$credentials = get_option('ftp_credentials', array( 'hostname' => '', 'username' => ''));

	$submitted_form = wp_unslash( $_POST );

	// Verify nonce, or unset submitted form field values on failure
	if ( ! isset( $_POST['_fs_nonce'] ) || ! wp_verify_nonce( $_POST['_fs_nonce'], 'filesystem-credentials' ) ) {
		unset(
			$submitted_form['hostname'],
			$submitted_form['username'],
			$submitted_form['password'],
			$submitted_form['public_key'],
			$submitted_form['private_key'],
			$submitted_form['connection_type']
		);
	}

	// If defined, set it to that, Else, If POST'd, set it to that, If not, Set it to whatever it previously was(saved details in option)
	$credentials['hostname'] = defined('FTP_HOST') ? FTP_HOST : (!empty($submitted_form['hostname']) ? $submitted_form['hostname'] : $credentials['hostname']);
	$credentials['username'] = defined('FTP_USER') ? FTP_USER : (!empty($submitted_form['username']) ? $submitted_form['username'] : $credentials['username']);
	$credentials['password'] = defined('FTP_PASS') ? FTP_PASS : (!empty($submitted_form['password']) ? $submitted_form['password'] : '');

	// Check to see if we are setting the public/private keys for ssh
	$credentials['public_key'] = defined('FTP_PUBKEY') ? FTP_PUBKEY : (!empty($submitted_form['public_key']) ? $submitted_form['public_key'] : '');
	$credentials['private_key'] = defined('FTP_PRIKEY') ? FTP_PRIKEY : (!empty($submitted_form['private_key']) ? $submitted_form['private_key'] : '');

	// Sanitize the hostname, Some people might pass in odd-data:
	$credentials['hostname'] = preg_replace('|\w+://|', '', $credentials['hostname']); //Strip any schemes off

	if ( strpos($credentials['hostname'], ':') ) {
		list( $credentials['hostname'], $credentials['port'] ) = explode(':', $credentials['hostname'], 2);
		if ( ! is_numeric($credentials['port']) )
			unset($credentials['port']);
	} else {
		unset($credentials['port']);
	}

	if ( ( defined( 'FTP_SSH' ) && FTP_SSH ) || ( defined( 'FS_METHOD' ) && 'ssh2' == FS_METHOD ) ) {
		$credentials['connection_type'] = 'ssh';
	} elseif ( ( defined( 'FTP_SSL' ) && FTP_SSL ) && 'ftpext' == $type ) { //Only the FTP Extension understands SSL
		$credentials['connection_type'] = 'ftps';
	} elseif ( ! empty( $submitted_form['connection_type'] ) ) {
		$credentials['connection_type'] = $submitted_form['connection_type'];
	} elseif ( ! isset( $credentials['connection_type'] ) ) { //All else fails (And it's not defaulted to something else saved), Default to FTP
		$credentials['connection_type'] = 'ftp';
	}
	if ( ! $error &&
			(
				( !empty($credentials['password']) && !empty($credentials['username']) && !empty($credentials['hostname']) ) ||
				( 'ssh' == $credentials['connection_type'] && !empty($credentials['public_key']) && !empty($credentials['private_key']) )
			) ) {
		$stored_credentials = $credentials;
		if ( !empty($stored_credentials['port']) ) //save port as part of hostname to simplify above code.
			$stored_credentials['hostname'] .= ':' . $stored_credentials['port'];

		unset($stored_credentials['password'], $stored_credentials['port'], $stored_credentials['private_key'], $stored_credentials['public_key']);
		if ( ! wp_installing() ) {
			update_option( 'ftp_credentials', $stored_credentials );
		}
		return $credentials;
	}
	$hostname = isset( $credentials['hostname'] ) ? $credentials['hostname'] : '';
	$username = isset( $credentials['username'] ) ? $credentials['username'] : '';
	$public_key = isset( $credentials['public_key'] ) ? $credentials['public_key'] : '';
	$private_key = isset( $credentials['private_key'] ) ? $credentials['private_key'] : '';
	$port = isset( $credentials['port'] ) ? $credentials['port'] : '';
	$connection_type = isset( $credentials['connection_type'] ) ? $credentials['connection_type'] : '';

	if ( $error ) {
		$error_string = __('<strong>ERROR:</strong> There was an error connecting to the server, Please verify the settings are correct.');
		if ( is_wp_error($error) )
			$error_string = esc_html( $error->get_error_message() );
		echo '<div id="message" class="error"><p>' . $error_string . '</p></div>';
	}

	$types = array();
	if ( extension_loaded('ftp') || extension_loaded('sockets') || function_exists('fsockopen') )
		$types[ 'ftp' ] = __('FTP');
	if ( extension_loaded('ftp') ) //Only this supports FTPS
		$types[ 'ftps' ] = __('FTPS (SSL)');
	if ( extension_loaded('ssh2') && function_exists('stream_get_contents') )
		$types[ 'ssh' ] = __('SSH2');

	/**
	 * Filters the connection types to output to the filesystem credentials form.
	 *
	 * @since 2.9.0
	 * @since 4.6.0 The `$context` parameter default changed from `false` to an empty string.
	 *
	 * @param array  $types       Types of connections.
	 * @param array  $credentials Credentials to connect with.
	 * @param string $type        Chosen filesystem method.
	 * @param object $error       Error object.
	 * @param string $context     Full path to the directory that is tested
	 *                            for being writable.
	 */
	$types = apply_filters( 'fs_ftp_connection_types', $types, $credentials, $type, $error, $context );

?>
<form action="<?php echo esc_url( $form_post ) ?>" method="post">
<div id="request-filesystem-credentials-form" class="request-filesystem-credentials-form">
<?php
// Print a H1 heading in the FTP credentials modal dialog, default is a H2.
$heading_tag = 'h2';
if ( 'plugins.php' === $pagenow || 'plugin-install.php' === $pagenow ) {
	$heading_tag = 'h1';
}
echo "<$heading_tag id='request-filesystem-credentials-title'>" . __( 'Connection Information' ) . "</$heading_tag>";
?>
<p id="request-filesystem-credentials-desc"><?php
	$label_user = __('Username');
	$label_pass = __('Password');
	_e('To perform the requested action, WordPress needs to access your web server.');
	echo ' ';
	if ( ( isset( $types['ftp'] ) || isset( $types['ftps'] ) ) ) {
		if ( isset( $types['ssh'] ) ) {
			_e('Please enter your FTP or SSH credentials to proceed.');
			$label_user = __('FTP/SSH Username');
			$label_pass = __('FTP/SSH Password');
		} else {
			_e('Please enter your FTP credentials to proceed.');
			$label_user = __('FTP Username');
			$label_pass = __('FTP Password');
		}
		echo ' ';
	}
	_e('If you do not remember your credentials, you should contact your web host.');
?></p>
<label for="hostname">
	<span class="field-title"><?php _e( 'Hostname' ) ?></span>
	<input name="hostname" type="text" id="hostname" aria-describedby="request-filesystem-credentials-desc" class="code" placeholder="<?php esc_attr_e( 'example: www.wordpress.org' ) ?>" value="<?php echo esc_attr($hostname); if ( !empty($port) ) echo ":$port"; ?>"<?php disabled( defined('FTP_HOST') ); ?> />
</label>
<div class="ftp-username">
	<label for="username">
		<span class="field-title"><?php echo $label_user; ?></span>
		<input name="username" type="text" id="username" value="<?php echo esc_attr($username) ?>"<?php disabled( defined('FTP_USER') ); ?> />
	</label>
</div>
<div class="ftp-password">
	<label for="password">
		<span class="field-title"><?php echo $label_pass; ?></span>
		<input name="password" type="password" id="password" value="<?php if ( defined('FTP_PASS') ) echo '*****'; ?>"<?php disabled( defined('FTP_PASS') ); ?> />
		<em><?php if ( ! defined('FTP_PASS') ) _e( 'This password will not be stored on the server.' ); ?></em>
	</label>
</div>
<fieldset>
<legend><?php _e( 'Connection Type' ); ?></legend>
<?php
	$disabled = disabled( ( defined( 'FTP_SSL' ) && FTP_SSL ) || ( defined( 'FTP_SSH' ) && FTP_SSH ), true, false );
	foreach ( $types as $name => $text ) : ?>
	<label for="<?php echo esc_attr( $name ) ?>">
		<input type="radio" name="connection_type" id="<?php echo esc_attr( $name ) ?>" value="<?php echo esc_attr( $name ) ?>"<?php checked( $name, $connection_type ); echo $disabled; ?> />
		<?php echo $text; ?>
	</label>
<?php
	endforeach;
?>
</fieldset>
<?php
if ( isset( $types['ssh'] ) ) {
	$hidden_class = '';
	if ( 'ssh' != $connection_type || empty( $connection_type ) ) {
		$hidden_class = ' class="hidden"';
	}
?>
<fieldset id="ssh-keys"<?php echo $hidden_class; ?>>
<legend><?php _e( 'Authentication Keys' ); ?></legend>
<label for="public_key">
	<span class="field-title"><?php _e('Public Key:') ?></span>
	<input name="public_key" type="text" id="public_key" aria-describedby="auth-keys-desc" value="<?php echo esc_attr($public_key) ?>"<?php disabled( defined('FTP_PUBKEY') ); ?> />
</label>
<label for="private_key">
	<span class="field-title"><?php _e('Private Key:') ?></span>
	<input name="private_key" type="text" id="private_key" value="<?php echo esc_attr($private_key) ?>"<?php disabled( defined('FTP_PRIKEY') ); ?> />
</label>
<p id="auth-keys-desc"><?php _e( 'Enter the location on the server where the public and private keys are located. If a passphrase is needed, enter that in the password field above.' ) ?></p>
</fieldset>
<?php
}

foreach ( (array) $extra_fields as $field ) {
	if ( isset( $submitted_form[ $field ] ) )
		echo '<input type="hidden" name="' . esc_attr( $field ) . '" value="' . esc_attr( $submitted_form[ $field ] ) . '" />';
}
?>
	<p class="request-filesystem-credentials-action-buttons">
		<?php wp_nonce_field( 'filesystem-credentials', '_fs_nonce', false, true ); ?>
		<button class="button cancel-button" data-js-action="close" type="button"><?php _e( 'Cancel' ); ?></button>
		<?php submit_button( __( 'Proceed' ), '', 'upgrade', false ); ?>
	</p>
</div>
</form>
<?php
	return false;
}

/**
 * Print the filesystem credentials modal when needed.
 *
 * @since 4.2.0
 */
function wp_print_request_filesystem_credentials_modal() {
	$filesystem_method = get_filesystem_method();
	ob_start();
	$filesystem_credentials_are_stored = request_filesystem_credentials( self_admin_url() );
	ob_end_clean();
	$request_filesystem_credentials = ( $filesystem_method != 'direct' && ! $filesystem_credentials_are_stored );
	if ( ! $request_filesystem_credentials ) {
		return;
	}
	?>
	<div id="request-filesystem-credentials-dialog" class="notification-dialog-wrap request-filesystem-credentials-dialog">
		<div class="notification-dialog-background"></div>
		<div class="notification-dialog" role="dialog" aria-labelledby="request-filesystem-credentials-title" tabindex="0">
			<div class="request-filesystem-credentials-dialog-content">
				<?php request_filesystem_credentials( site_url() ); ?>
			</div>
		</div>
	</div>
	<?php
}
