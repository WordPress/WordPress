<?php
/**
 * File contains all the administration image manipulation functions.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** The descriptions for theme files. */
$wp_file_descriptions = array(
	'index.php' => __( 'Main Index Template' ),
	'style.css' => __( 'Stylesheet' ),
	'editor-style.css' => __( 'Visual Editor Stylesheet' ),
	'editor-style-rtl.css' => __( 'Visual Editor RTL Stylesheet' ),
	'rtl.css' => __( 'RTL Stylesheet' ),
	'comments.php' => __( 'Comments' ),
	'comments-popup.php' => __( 'Popup Comments' ),
	'footer.php' => __( 'Footer' ),
	'header.php' => __( 'Header' ),
	'sidebar.php' => __( 'Sidebar' ),
	'archive.php' => __( 'Archives' ),
	'author.php' => __( 'Author Template' ),
	'tag.php' => __( 'Tag Template' ),
	'category.php' => __( 'Category Template' ),
	'page.php' => __( 'Page Template' ),
	'search.php' => __( 'Search Results' ),
	'searchform.php' => __( 'Search Form' ),
	'single.php' => __( 'Single Post' ),
	'404.php' => __( '404 Template' ),
	'link.php' => __( 'Links Template' ),
	'functions.php' => __( 'Theme Functions' ),
	'attachment.php' => __( 'Attachment Template' ),
	'image.php' => __('Image Attachment Template'),
	'video.php' => __('Video Attachment Template'),
	'audio.php' => __('Audio Attachment Template'),
	'application.php' => __('Application Attachment Template'),
	'my-hacks.php' => __( 'my-hacks.php (legacy hacks support)' ),
	'.htaccess' => __( '.htaccess (for rewrite rules )' ),
	// Deprecated files
	'wp-layout.css' => __( 'Stylesheet' ),
	'wp-comments.php' => __( 'Comments Template' ),
	'wp-comments-popup.php' => __( 'Popup Comments Template' ),
);

/**
 * Get the description for standard WordPress theme files and other various standard
 * WordPress files
 *
 * @since 1.5.0
 *
 * @uses _cleanup_header_comment
 * @uses $wp_file_descriptions
 * @param string $file Filesystem path or filename
 * @return string Description of file from $wp_file_descriptions or basename of $file if description doesn't exist
 */
function get_file_description( $file ) {
	global $wp_file_descriptions;

	if ( isset( $wp_file_descriptions[basename( $file )] ) ) {
		return $wp_file_descriptions[basename( $file )];
	}
	elseif ( file_exists( $file ) && is_file( $file ) ) {
		$template_data = implode( '', file( $file ) );
		if ( preg_match( '|Template Name:(.*)$|mi', $template_data, $name ))
			return sprintf( __( '%s Page Template' ), _cleanup_header_comment($name[1]) );
	}

	return basename( $file );
}

/**
 * Get the absolute filesystem path to the root of the WordPress installation
 *
 * @since 1.5.0
 *
 * @uses get_option
 * @return string Full filesystem path to the root of the WordPress installation
 */
function get_home_path() {
	$home = get_option( 'home' );
	$siteurl = get_option( 'siteurl' );
	if ( $home != '' && $home != $siteurl ) {
		$wp_path_rel_to_home = str_replace($home, '', $siteurl); /* $siteurl - $home */
		$pos = strpos($_SERVER["SCRIPT_FILENAME"], $wp_path_rel_to_home);
		$home_path = substr($_SERVER["SCRIPT_FILENAME"], 0, $pos);
		$home_path = trailingslashit( $home_path );
	} else {
		$home_path = ABSPATH;
	}

	return $home_path;
}

/**
 * Get the real file system path to a file to edit within the admin
 *
 * If the $file is index.php or .htaccess this function will assume it is relative
 * to the install root, otherwise it is assumed the file is relative to the wp-content
 * directory
 *
 * @since 1.5.0
 *
 * @uses get_home_path
 * @uses WP_CONTENT_DIR full filesystem path to the wp-content directory
 * @param string $file filesystem path relative to the WordPress install directory or to the wp-content directory
 * @return string full file system path to edit
 */
function get_real_file_to_edit( $file ) {
	if ('index.php' == $file || '.htaccess' == $file ) {
		$real_file = get_home_path() . $file;
	} else {
		$real_file = WP_CONTENT_DIR . $file;
	}

	return $real_file;
}

/**
 * Returns a listing of all files in the specified folder and all subdirectories up to 100 levels deep.
 * The depth of the recursiveness can be controlled by the $levels param.
 *
 * @since 2.6.0
 *
 * @param string $folder Full path to folder
 * @param int $levels (optional) Levels of folders to follow, Default: 100 (PHP Loop limit).
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
 * while the directory can either be passed as well, or by leaving  it blank, default to a writable temporary directory.
 *
 * @since 2.6.0
 *
 * @param string $filename (optional) Filename to base the Unique file off
 * @param string $dir (optional) Directory to store the file in
 * @return string a writable filename
 */
function wp_tempnam($filename = '', $dir = '') {
	if ( empty($dir) )
		$dir = get_temp_dir();
	$filename = basename($filename);
	if ( empty($filename) )
		$filename = time();

	$filename = preg_replace('|\..*$|', '.tmp', $filename);
	$filename = $dir . wp_unique_filename($dir, $filename);
	touch($filename);
	return $filename;
}

/**
 * Make sure that the file that was requested to edit, is allowed to be edited
 *
 * Function will die if if you are not allowed to edit the file
 *
 * @since 1.5.0
 *
 * @uses wp_die
 * @uses validate_file
 * @param string $file file the users is attempting to edit
 * @param array $allowed_files Array of allowed files to edit, $file must match an entry exactly
 * @return null
 */
function validate_file_to_edit( $file, $allowed_files = '' ) {
	$code = validate_file( $file, $allowed_files );

	if (!$code )
		return $file;

	switch ( $code ) {
		case 1 :
			wp_die( __('Sorry, can&#8217;t edit files with &#8220;..&#8221; in the name. If you are trying to edit a file in your WordPress home directory, you can just type the name of the file in.' ));

		//case 2 :
		//	wp_die( __('Sorry, can&#8217;t call files with their real path.' ));

		case 3 :
			wp_die( __('Sorry, that file cannot be edited.' ));
	}
}

/**
 * Handle PHP uploads in WordPress, sanitizing file names, checking extensions for mime type,
 * and moving the file to the appropriate directory within the uploads directory.
 *
 * @since 2.0
 *
 * @uses wp_handle_upload_error
 * @uses apply_filters
 * @uses is_multisite
 * @uses wp_check_filetype_and_ext
 * @uses current_user_can
 * @uses wp_upload_dir
 * @uses wp_unique_filename
 * @uses delete_transient
 * @param array $file Reference to a single element of $_FILES. Call the function once for each uploaded file.
 * @param array $overrides Optional. An associative array of names=>values to override default variables with extract( $overrides, EXTR_OVERWRITE ).
 * @return array On success, returns an associative array of file attributes. On failure, returns $overrides['upload_error_handler'](&$file, $message ) or array( 'error'=>$message ).
 */
function wp_handle_upload( &$file, $overrides = false, $time = null ) {
	// The default error handler.
	if ( ! function_exists( 'wp_handle_upload_error' ) ) {
		function wp_handle_upload_error( &$file, $message ) {
			return array( 'error'=>$message );
		}
	}

	$file = apply_filters( 'wp_handle_upload_prefilter', $file );

	// You may define your own function and pass the name in $overrides['upload_error_handler']
	$upload_error_handler = 'wp_handle_upload_error';

	// You may have had one or more 'wp_handle_upload_prefilter' functions error out the file.  Handle that gracefully.
	if ( isset( $file['error'] ) && !is_numeric( $file['error'] ) && $file['error'] )
		return $upload_error_handler( $file, $file['error'] );

	// You may define your own function and pass the name in $overrides['unique_filename_callback']
	$unique_filename_callback = null;

	// $_POST['action'] must be set and its value must equal $overrides['action'] or this:
	$action = 'wp_handle_upload';

	// Courtesy of php.net, the strings that describe the error indicated in $_FILES[{form field}]['error'].
	$upload_error_strings = array( false,
		__( "The uploaded file exceeds the upload_max_filesize directive in php.ini." ),
		__( "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form." ),
		__( "The uploaded file was only partially uploaded." ),
		__( "No file was uploaded." ),
		'',
		__( "Missing a temporary folder." ),
		__( "Failed to write file to disk." ),
		__( "File upload stopped by extension." ));

	// All tests are on by default. Most can be turned off by $overrides[{test_name}] = false;
	$test_form = true;
	$test_size = true;
	$test_upload = true;

	// If you override this, you must provide $ext and $type!!!!
	$test_type = true;
	$mimes = false;

	// Install user overrides. Did we mention that this voids your warranty?
	if ( is_array( $overrides ) )
		extract( $overrides, EXTR_OVERWRITE );

	// A correct form post will pass this test.
	if ( $test_form && (!isset( $_POST['action'] ) || ($_POST['action'] != $action ) ) )
		return call_user_func($upload_error_handler, $file, __( 'Invalid form submission.' ));

	// A successful upload will pass this test. It makes no sense to override this one.
	if ( $file['error'] > 0 )
		return call_user_func($upload_error_handler, $file, $upload_error_strings[$file['error']] );

	// A non-empty file will pass this test.
	if ( $test_size && !($file['size'] > 0 ) ) {
		if ( is_multisite() )
			$error_msg = __( 'File is empty. Please upload something more substantial.' );
		else
			$error_msg = __( 'File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your php.ini or by post_max_size being defined as smaller than upload_max_filesize in php.ini.' );
		return call_user_func($upload_error_handler, $file, $error_msg);
	}

	// A properly uploaded file will pass this test. There should be no reason to override this one.
	if ( $test_upload && ! @ is_uploaded_file( $file['tmp_name'] ) )
		return call_user_func($upload_error_handler, $file, __( 'Specified file failed upload test.' ));

	// A correct MIME type will pass this test. Override $mimes or use the upload_mimes filter.
	if ( $test_type ) {
		$wp_filetype = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], $mimes );

		extract( $wp_filetype );

		// Check to see if wp_check_filetype_and_ext() determined the filename was incorrect
		if ( $proper_filename )
			$file['name'] = $proper_filename;

		if ( ( !$type || !$ext ) && !current_user_can( 'unfiltered_upload' ) )
			return call_user_func($upload_error_handler, $file, __( 'Sorry, this file type is not permitted for security reasons.' ));

		if ( !$ext )
			$ext = ltrim(strrchr($file['name'], '.'), '.');

		if ( !$type )
			$type = $file['type'];
	} else {
		$type = '';
	}

	// A writable uploads dir will pass this test. Again, there's no point overriding this one.
	if ( ! ( ( $uploads = wp_upload_dir($time) ) && false === $uploads['error'] ) )
		return call_user_func($upload_error_handler, $file, $uploads['error'] );

	$filename = wp_unique_filename( $uploads['path'], $file['name'], $unique_filename_callback );

	// Move the file to the uploads dir
	$new_file = $uploads['path'] . "/$filename";
	if ( false === @ move_uploaded_file( $file['tmp_name'], $new_file ) )
		return $upload_error_handler( $file, sprintf( __('The uploaded file could not be moved to %s.' ), $uploads['path'] ) );

	// Set correct file permissions
	$stat = stat( dirname( $new_file ));
	$perms = $stat['mode'] & 0000666;
	@ chmod( $new_file, $perms );

	// Compute the URL
	$url = $uploads['url'] . "/$filename";

	if ( is_multisite() )
		delete_transient( 'dirsize_cache' );

	return apply_filters( 'wp_handle_upload', array( 'file' => $new_file, 'url' => $url, 'type' => $type ), 'upload' );
}

/**
 * Handle sideloads, which is the process of retriving a media item from another server instead of
 * a traditional media upload.  This process involves sanitizing the filename, checking extensions
 * for mime type, and moving the file to the appropriate directory within the uploads directory.
 *
 * @since 2.6.0
 *
 * @uses wp_handle_upload_error
 * @uses apply_filters
 * @uses wp_check_filetype_and_ext
 * @uses current_user_can
 * @uses wp_upload_dir
 * @uses wp_unique_filename
 * @param array $file an array similar to that of a PHP $_FILES POST array
 * @param array $overrides Optional. An associative array of names=>values to override default variables with extract( $overrides, EXTR_OVERWRITE ).
 * @return array On success, returns an associative array of file attributes. On failure, returns $overrides['upload_error_handler'](&$file, $message ) or array( 'error'=>$message ).
 */
function wp_handle_sideload( &$file, $overrides = false ) {
	// The default error handler.
	if (! function_exists( 'wp_handle_upload_error' ) ) {
		function wp_handle_upload_error( &$file, $message ) {
			return array( 'error'=>$message );
		}
	}

	// You may define your own function and pass the name in $overrides['upload_error_handler']
	$upload_error_handler = 'wp_handle_upload_error';

	// You may define your own function and pass the name in $overrides['unique_filename_callback']
	$unique_filename_callback = null;

	// $_POST['action'] must be set and its value must equal $overrides['action'] or this:
	$action = 'wp_handle_sideload';

	// Courtesy of php.net, the strings that describe the error indicated in $_FILES[{form field}]['error'].
	$upload_error_strings = array( false,
		__( "The uploaded file exceeds the <code>upload_max_filesize</code> directive in <code>php.ini</code>." ),
		__( "The uploaded file exceeds the <em>MAX_FILE_SIZE</em> directive that was specified in the HTML form." ),
		__( "The uploaded file was only partially uploaded." ),
		__( "No file was uploaded." ),
		'',
		__( "Missing a temporary folder." ),
		__( "Failed to write file to disk." ),
		__( "File upload stopped by extension." ));

	// All tests are on by default. Most can be turned off by $overrides[{test_name}] = false;
	$test_form = true;
	$test_size = true;

	// If you override this, you must provide $ext and $type!!!!
	$test_type = true;
	$mimes = false;

	// Install user overrides. Did we mention that this voids your warranty?
	if ( is_array( $overrides ) )
		extract( $overrides, EXTR_OVERWRITE );

	// A correct form post will pass this test.
	if ( $test_form && (!isset( $_POST['action'] ) || ($_POST['action'] != $action ) ) )
		return $upload_error_handler( $file, __( 'Invalid form submission.' ));

	// A successful upload will pass this test. It makes no sense to override this one.
	if ( ! empty( $file['error'] ) )
		return $upload_error_handler( $file, $upload_error_strings[$file['error']] );

	// A non-empty file will pass this test.
	if ( $test_size && !(filesize($file['tmp_name']) > 0 ) )
		return $upload_error_handler( $file, __( 'File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your php.ini.' ));

	// A properly uploaded file will pass this test. There should be no reason to override this one.
	if (! @ is_file( $file['tmp_name'] ) )
		return $upload_error_handler( $file, __( 'Specified file does not exist.' ));

	// A correct MIME type will pass this test. Override $mimes or use the upload_mimes filter.
	if ( $test_type ) {
		$wp_filetype = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], $mimes );

		extract( $wp_filetype );

		// Check to see if wp_check_filetype_and_ext() determined the filename was incorrect
		if ( $proper_filename )
			$file['name'] = $proper_filename;

		if ( ( !$type || !$ext ) && !current_user_can( 'unfiltered_upload' ) )
			return $upload_error_handler( $file, __( 'Sorry, this file type is not permitted for security reasons.' ));

		if ( !$ext )
			$ext = ltrim(strrchr($file['name'], '.'), '.');

		if ( !$type )
			$type = $file['type'];
	}

	// A writable uploads dir will pass this test. Again, there's no point overriding this one.
	if ( ! ( ( $uploads = wp_upload_dir() ) && false === $uploads['error'] ) )
		return $upload_error_handler( $file, $uploads['error'] );

	$filename = wp_unique_filename( $uploads['path'], $file['name'], $unique_filename_callback );

	// Strip the query strings.
	$filename = str_replace('?','-', $filename);
	$filename = str_replace('&','-', $filename);

	// Move the file to the uploads dir
	$new_file = $uploads['path'] . "/$filename";
	if ( false === @ rename( $file['tmp_name'], $new_file ) ) {
		return $upload_error_handler( $file, sprintf( __('The uploaded file could not be moved to %s.' ), $uploads['path'] ) );
	}

	// Set correct file permissions
	$stat = stat( dirname( $new_file ));
	$perms = $stat['mode'] & 0000666;
	@ chmod( $new_file, $perms );

	// Compute the URL
	$url = $uploads['url'] . "/$filename";

	$return = apply_filters( 'wp_handle_upload', array( 'file' => $new_file, 'url' => $url, 'type' => $type ), 'sideload' );

	return $return;
}

/**
 * Downloads a url to a local temporary file using the WordPress HTTP Class.
 * Please note, That the calling function must unlink() the  file.
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

	$tmpfname = wp_tempnam($url);
	if ( ! $tmpfname )
		return new WP_Error('http_no_file', __('Could not create Temporary file.'));

	$response = wp_remote_get( $url, array( 'timeout' => $timeout, 'stream' => true, 'filename' => $tmpfname ) );

	if ( is_wp_error( $response ) ) {
		unlink( $tmpfname );
		return $response;
	}

	if ( 200 != wp_remote_retrieve_response_code( $response ) ){
		unlink( $tmpfname );
		return new WP_Error( 'http_404', trim( wp_remote_retrieve_response_message( $response ) ) );
	}

	return $tmpfname;
}

/**
 * Unzip's a specified ZIP file to a location on the Filesystem via the WordPress Filesystem Abstraction.
 * Assumes that WP_Filesystem() has already been called and set up. Does not extract a root-level __MACOSX directory, if present.
 *
 * Attempts to increase the PHP Memory limit to 256M before uncompressing,
 * However, The most memory required shouldn't be much larger than the Archive itself.
 *
 * @since 2.5.0
 *
 * @param string $file Full path and filename of zip archive
 * @param string $to Full path on the filesystem to extract archive to
 * @return mixed WP_Error on failure, True on success
 */
function unzip_file($file, $to) {
	global $wp_filesystem;

	if ( ! $wp_filesystem || !is_object($wp_filesystem) )
		return new WP_Error('fs_unavailable', __('Could not access filesystem.'));

	// Unzip can use a lot of memory, but not this much hopefully
	@ini_set( 'memory_limit', apply_filters( 'admin_memory_limit', WP_MAX_MEMORY_LIMIT ) );

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

	if ( class_exists('ZipArchive') && apply_filters('unzip_file_use_ziparchive', true ) ) {
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
 * @param string $file Full path and filename of zip archive
 * @param string $to Full path on the filesystem to extract archive to
 * @param array $needed_dirs A partial list of required folders needed to be created.
 * @return mixed WP_Error on failure, True on success
 */
function _unzip_file_ziparchive($file, $to, $needed_dirs = array() ) {
	global $wp_filesystem;

	$z = new ZipArchive();

	// PHP4-compat - php4 classes can't contain constants
	$zopen = $z->open($file, /* ZIPARCHIVE::CHECKCONS */ 4);
	if ( true !== $zopen )
		return new WP_Error('incompatible_archive', __('Incompatible Archive.'));

	for ( $i = 0; $i < $z->numFiles; $i++ ) {
		if ( ! $info = $z->statIndex($i) )
			return new WP_Error('stat_failed', __('Could not retrieve file from archive.'));

		if ( '__MACOSX/' === substr($info['name'], 0, 9) ) // Skip the OS X-created __MACOSX directory
			continue;

		if ( '/' == substr($info['name'], -1) ) // directory
			$needed_dirs[] = $to . untrailingslashit($info['name']);
		else
			$needed_dirs[] = $to . untrailingslashit(dirname($info['name']));
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
		if ( ! $wp_filesystem->mkdir($_dir, FS_CHMOD_DIR) && ! $wp_filesystem->is_dir($_dir) ) // Only check to see if the Dir exists upon creation failure. Less I/O this way.
			return new WP_Error('mkdir_failed', __('Could not create directory.'), $_dir);
	}
	unset($needed_dirs);

	for ( $i = 0; $i < $z->numFiles; $i++ ) {
		if ( ! $info = $z->statIndex($i) )
			return new WP_Error('stat_failed', __('Could not retrieve file from archive.'));

		if ( '/' == substr($info['name'], -1) ) // directory
			continue;

		if ( '__MACOSX/' === substr($info['name'], 0, 9) ) // Don't extract the OS X-created __MACOSX directory files
			continue;

		$contents = $z->getFromIndex($i);
		if ( false === $contents )
			return new WP_Error('extract_failed', __('Could not extract file from archive.'), $info['name']);

		if ( ! $wp_filesystem->put_contents( $to . $info['name'], $contents, FS_CHMOD_FILE) )
			return new WP_Error('copy_failed', __('Could not copy file.'), $to . $info['filename']);
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
 * @param string $file Full path and filename of zip archive
 * @param string $to Full path on the filesystem to extract archive to
 * @param array $needed_dirs A partial list of required folders needed to be created.
 * @return mixed WP_Error on failure, True on success
 */
function _unzip_file_pclzip($file, $to, $needed_dirs = array()) {
	global $wp_filesystem;

	// See #15789 - PclZip uses string functions on binary data, If it's overloaded with Multibyte safe functions the results are incorrect.
	if ( ini_get('mbstring.func_overload') && function_exists('mb_internal_encoding') ) {
		$previous_encoding = mb_internal_encoding();
		mb_internal_encoding('ISO-8859-1');
	}

	require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');

	$archive = new PclZip($file);

	$archive_files = $archive->extract(PCLZIP_OPT_EXTRACT_AS_STRING);

	if ( isset($previous_encoding) )
		mb_internal_encoding($previous_encoding);

	// Is the archive valid?
	if ( !is_array($archive_files) )
		return new WP_Error('incompatible_archive', __('Incompatible Archive.'), $archive->errorInfo(true));

	if ( 0 == count($archive_files) )
		return new WP_Error('empty_archive', __('Empty archive.'));

	// Determine any children directories needed (From within the archive)
	foreach ( $archive_files as $file ) {
		if ( '__MACOSX/' === substr($file['filename'], 0, 9) ) // Skip the OS X-created __MACOSX directory
			continue;

		$needed_dirs[] = $to . untrailingslashit( $file['folder'] ? $file['filename'] : dirname($file['filename']) );
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
		if ( ! $wp_filesystem->mkdir($_dir, FS_CHMOD_DIR) && ! $wp_filesystem->is_dir($_dir) ) // Only check to see if the dir exists upon creation failure. Less I/O this way.
			return new WP_Error('mkdir_failed', __('Could not create directory.'), $_dir);
	}
	unset($needed_dirs);

	// Extract the files from the zip
	foreach ( $archive_files as $file ) {
		if ( $file['folder'] )
			continue;

		if ( '__MACOSX/' === substr($file['filename'], 0, 9) ) // Don't extract the OS X-created __MACOSX directory files
			continue;

		if ( ! $wp_filesystem->put_contents( $to . $file['filename'], $file['content'], FS_CHMOD_FILE) )
			return new WP_Error('copy_failed', __('Could not copy file.'), $to . $file['filename']);
	}
	return true;
}

/**
 * Copies a directory from one location to another via the WordPress Filesystem Abstraction.
 * Assumes that WP_Filesystem() has already been called and setup.
 *
 * @since 2.5.0
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

	$skip_regex = '';
	foreach ( (array)$skip_list as $key => $skip_file )
		$skip_regex .= preg_quote($skip_file, '!') . '|';

	if ( !empty($skip_regex) )
		$skip_regex = '!(' . rtrim($skip_regex, '|') . ')$!i';

	foreach ( (array) $dirlist as $filename => $fileinfo ) {
		if ( !empty($skip_regex) )
			if ( preg_match($skip_regex, $from . $filename) )
				continue;

		if ( 'f' == $fileinfo['type'] ) {
			if ( ! $wp_filesystem->copy($from . $filename, $to . $filename, true, FS_CHMOD_FILE) ) {
				// If copy failed, chmod file to 0644 and try again.
				$wp_filesystem->chmod($to . $filename, 0644);
				if ( ! $wp_filesystem->copy($from . $filename, $to . $filename, true, FS_CHMOD_FILE) )
					return new WP_Error('copy_failed', __('Could not copy file.'), $to . $filename);
			}
		} elseif ( 'd' == $fileinfo['type'] ) {
			if ( !$wp_filesystem->is_dir($to . $filename) ) {
				if ( !$wp_filesystem->mkdir($to . $filename, FS_CHMOD_DIR) )
					return new WP_Error('mkdir_failed', __('Could not create directory.'), $to . $filename);
			}
			$result = copy_dir($from . $filename, $to . $filename, $skip_list);
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
 * Plugins may add extra transports, And force WordPress to use them by returning the filename via the 'filesystem_method_file' filter.
 *
 * @since 2.5.0
 *
 * @param array $args (optional) Connection args, These are passed directly to the WP_Filesystem_*() classes.
 * @param string $context (optional) Context for get_filesystem_method(), See function declaration for more information.
 * @return boolean false on failure, true on success
 */
function WP_Filesystem( $args = false, $context = false ) {
	global $wp_filesystem;

	require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php');

	$method = get_filesystem_method($args, $context);

	if ( ! $method )
		return false;

	if ( ! class_exists("WP_Filesystem_$method") ) {
		$abstraction_file = apply_filters('filesystem_method_file', ABSPATH . 'wp-admin/includes/class-wp-filesystem-' . $method . '.php', $method);
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
		return false; //There was an erorr connecting to the server.

	// Set the permission constants if not already set.
	if ( ! defined('FS_CHMOD_DIR') )
		define('FS_CHMOD_DIR', 0755 );
	if ( ! defined('FS_CHMOD_FILE') )
		define('FS_CHMOD_FILE', 0644 );

	return true;
}

/**
 * Determines which Filesystem Method to use.
 * The priority of the Transports are: Direct, SSH2, FTP PHP Extension, FTP Sockets (Via Sockets class, or fsoxkopen())
 *
 * Note that the return value of this function can be overridden in 2 ways
 *  - By defining FS_METHOD in your <code>wp-config.php</code> file
 *  - By using the filesystem_method filter
 * Valid values for these are: 'direct', 'ssh', 'ftpext' or 'ftpsockets'
 * Plugins may also define a custom transport handler, See the WP_Filesystem function for more information.
 *
 * @since 2.5.0
 *
 * @param array $args Connection details.
 * @param string $context Full path to the directory that is tested for being writable.
 * @return string The transport to use, see description for valid return values.
 */
function get_filesystem_method($args = array(), $context = false) {
	$method = defined('FS_METHOD') ? FS_METHOD : false; //Please ensure that this is either 'direct', 'ssh', 'ftpext' or 'ftpsockets'

	if ( ! $method && function_exists('getmyuid') && function_exists('fileowner') ){
		if ( !$context )
			$context = WP_CONTENT_DIR;
		$context = trailingslashit($context);
		$temp_file_name = $context . 'temp-write-test-' . time();
		$temp_handle = @fopen($temp_file_name, 'w');
		if ( $temp_handle ) {
			if ( getmyuid() == @fileowner($temp_file_name) )
				$method = 'direct';
			@fclose($temp_handle);
			@unlink($temp_file_name);
		}
 	}

	if ( ! $method && isset($args['connection_type']) && 'ssh' == $args['connection_type'] && extension_loaded('ssh2') && function_exists('stream_get_contents') ) $method = 'ssh2';
	if ( ! $method && extension_loaded('ftp') ) $method = 'ftpext';
	if ( ! $method && ( extension_loaded('sockets') || function_exists('fsockopen') ) ) $method = 'ftpsockets'; //Sockets: Socket extension; PHP Mode: FSockopen / fwrite / fread
	return apply_filters('filesystem_method', $method, $args);
}

/**
 * Displays a form to the user to request for their FTP/SSH details in order to  connect to the filesystem.
 * All chosen/entered details are saved, Excluding the Password.
 *
 * Hostnames may be in the form of hostname:portnumber (eg: wordpress.org:2467) to specify an alternate FTP/SSH port.
 *
 * Plugins may override this form by returning true|false via the <code>request_filesystem_credentials</code> filter.
 *
 * @since 2.5.0
 *
 * @param string $form_post the URL to post the form to
 * @param string $type the chosen Filesystem method in use
 * @param boolean $error if the current request has failed to connect
 * @param string $context The directory which is needed access to, The write-test will be performed on  this directory by get_filesystem_method()
 * @param string $extra_fields Extra POST fields which should be checked for to be included in the post.
 * @return boolean False on failure. True on success.
 */
function request_filesystem_credentials($form_post, $type = '', $error = false, $context = false, $extra_fields = null) {
	$req_cred = apply_filters( 'request_filesystem_credentials', '', $form_post, $type, $error, $context, $extra_fields );
	if ( '' !== $req_cred )
		return $req_cred;

	if ( empty($type) )
		$type = get_filesystem_method(array(), $context);

	if ( 'direct' == $type )
		return true;

	if ( is_null( $extra_fields ) )
		$extra_fields = array( 'version', 'locale' );

	$credentials = get_option('ftp_credentials', array( 'hostname' => '', 'username' => ''));

	// If defined, set it to that, Else, If POST'd, set it to that, If not, Set it to whatever it previously was(saved details in option)
	$credentials['hostname'] = defined('FTP_HOST') ? FTP_HOST : (!empty($_POST['hostname']) ? stripslashes($_POST['hostname']) : $credentials['hostname']);
	$credentials['username'] = defined('FTP_USER') ? FTP_USER : (!empty($_POST['username']) ? stripslashes($_POST['username']) : $credentials['username']);
	$credentials['password'] = defined('FTP_PASS') ? FTP_PASS : (!empty($_POST['password']) ? stripslashes($_POST['password']) : '');

	// Check to see if we are setting the public/private keys for ssh
	$credentials['public_key'] = defined('FTP_PUBKEY') ? FTP_PUBKEY : (!empty($_POST['public_key']) ? stripslashes($_POST['public_key']) : '');
	$credentials['private_key'] = defined('FTP_PRIKEY') ? FTP_PRIKEY : (!empty($_POST['private_key']) ? stripslashes($_POST['private_key']) : '');

	//sanitize the hostname, Some people might pass in odd-data:
	$credentials['hostname'] = preg_replace('|\w+://|', '', $credentials['hostname']); //Strip any schemes off

	if ( strpos($credentials['hostname'], ':') ) {
		list( $credentials['hostname'], $credentials['port'] ) = explode(':', $credentials['hostname'], 2);
		if ( ! is_numeric($credentials['port']) )
			unset($credentials['port']);
	} else {
		unset($credentials['port']);
	}

	if ( (defined('FTP_SSH') && FTP_SSH) || (defined('FS_METHOD') && 'ssh' == FS_METHOD) )
		$credentials['connection_type'] = 'ssh';
	else if ( (defined('FTP_SSL') && FTP_SSL) && 'ftpext' == $type ) //Only the FTP Extension understands SSL
		$credentials['connection_type'] = 'ftps';
	else if ( !empty($_POST['connection_type']) )
		$credentials['connection_type'] = stripslashes($_POST['connection_type']);
	else if ( !isset($credentials['connection_type']) ) //All else fails (And its not defaulted to something else saved), Default to FTP
		$credentials['connection_type'] = 'ftp';

	if ( ! $error &&
			(
				( !empty($credentials['password']) && !empty($credentials['username']) && !empty($credentials['hostname']) ) ||
				( 'ssh' == $credentials['connection_type'] && !empty($credentials['public_key']) && !empty($credentials['private_key']) )
			) ) {
		$stored_credentials = $credentials;
		if ( !empty($stored_credentials['port']) ) //save port as part of hostname to simplify above code.
			$stored_credentials['hostname'] .= ':' . $stored_credentials['port'];

		unset($stored_credentials['password'], $stored_credentials['port'], $stored_credentials['private_key'], $stored_credentials['public_key']);
		update_option('ftp_credentials', $stored_credentials);
		return $credentials;
	}
	$hostname = '';
	$username = '';
	$password = '';
	$connection_type = '';
	if ( !empty($credentials) )
		extract($credentials, EXTR_OVERWRITE);
	if ( $error ) {
		$error_string = __('<strong>Error:</strong> There was an error connecting to the server, Please verify the settings are correct.');
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

	$types = apply_filters('fs_ftp_connection_types', $types, $credentials, $type, $error, $context);

?>
<script type="text/javascript">
<!--
jQuery(function($){
	jQuery("#ssh").click(function () {
		jQuery("#ssh_keys").show();
	});
	jQuery("#ftp, #ftps").click(function () {
		jQuery("#ssh_keys").hide();
	});
	jQuery('form input[value=""]:first').focus();
});
-->
</script>
<form action="<?php echo $form_post ?>" method="post">
<div class="wrap">
<?php screen_icon(); ?>
<h2><?php _e('Connection Information') ?></h2>
<p><?php
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
<table class="form-table">
<tr valign="top">
<th scope="row"><label for="hostname"><?php _e('Hostname') ?></label></th>
<td><input name="hostname" type="text" id="hostname" value="<?php echo esc_attr($hostname); if ( !empty($port) ) echo ":$port"; ?>"<?php disabled( defined('FTP_HOST') ); ?> size="40" /></td>
</tr>

<tr valign="top">
<th scope="row"><label for="username"><?php echo $label_user; ?></label></th>
<td><input name="username" type="text" id="username" value="<?php echo esc_attr($username) ?>"<?php disabled( defined('FTP_USER') ); ?> size="40" /></td>
</tr>

<tr valign="top">
<th scope="row"><label for="password"><?php echo $label_pass; ?></label></th>
<td><input name="password" type="password" id="password" value="<?php if ( defined('FTP_PASS') ) echo '*****'; ?>"<?php disabled( defined('FTP_PASS') ); ?> size="40" /></td>
</tr>

<?php if ( isset($types['ssh']) ) : ?>
<tr id="ssh_keys" valign="top" style="<?php if ( 'ssh' != $connection_type ) echo 'display:none' ?>">
<th scope="row"><?php _e('Authentication Keys') ?>
<div class="key-labels textright">
<label for="public_key"><?php _e('Public Key:') ?></label ><br />
<label for="private_key"><?php _e('Private Key:') ?></label>
</div></th>
<td><br /><input name="public_key" type="text" id="public_key" value="<?php echo esc_attr($public_key) ?>"<?php disabled( defined('FTP_PUBKEY') ); ?> size="40" /><br /><input name="private_key" type="text" id="private_key" value="<?php echo esc_attr($private_key) ?>"<?php disabled( defined('FTP_PRIKEY') ); ?> size="40" />
<div><?php _e('Enter the location on the server where the keys are located. If a passphrase is needed, enter that in the password field above.') ?></div></td>
</tr>
<?php endif; ?>

<tr valign="top">
<th scope="row"><?php _e('Connection Type') ?></th>
<td>
<fieldset><legend class="screen-reader-text"><span><?php _e('Connection Type') ?></span></legend>
<?php
	$disabled = disabled( (defined('FTP_SSL') && FTP_SSL) || (defined('FTP_SSH') && FTP_SSH), true, false );
	foreach ( $types as $name => $text ) : ?>
	<label for="<?php echo esc_attr($name) ?>">
		<input type="radio" name="connection_type" id="<?php echo esc_attr($name) ?>" value="<?php echo esc_attr($name) ?>"<?php checked($name, $connection_type); echo $disabled; ?> />
		<?php echo $text ?>
	</label>
	<?php endforeach; ?>
</fieldset>
</td>
</tr>
</table>

<?php
foreach ( (array) $extra_fields as $field ) {
	if ( isset( $_POST[ $field ] ) )
		echo '<input type="hidden" name="' . esc_attr( $field ) . '" value="' . esc_attr( stripslashes( $_POST[ $field ] ) ) . '" />';
}
submit_button( __( 'Proceed' ), 'button', 'upgrade' );
?>
</div>
</form>
<?php
	return false;
}

?>
