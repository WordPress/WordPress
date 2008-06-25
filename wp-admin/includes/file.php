<?php

$wp_file_descriptions = array ('index.php' => __( 'Main Index Template' ), 'style.css' => __( 'Stylesheet' ), 'rtl.css' => __( 'RTL Stylesheet' ), 'comments.php' => __( 'Comments' ), 'comments-popup.php' => __( 'Popup Comments' ), 'footer.php' => __( 'Footer' ), 'header.php' => __( 'Header' ), 'sidebar.php' => __( 'Sidebar' ), 'archive.php' => __( 'Archives' ), 'category.php' => __( 'Category Template' ), 'page.php' => __( 'Page Template' ), 'search.php' => __( 'Search Results' ), 'searchform.php' => __( 'Search Form' ), 'single.php' => __( 'Single Post' ), '404.php' => __( '404 Template' ), 'link.php' => __( 'Links Template' ), 'functions.php' => __( 'Theme Functions' ), 'attachment.php' => __( 'Attachment Template' ), 'my-hacks.php' => __( 'my-hacks.php (legacy hacks support)' ), '.htaccess' => __( '.htaccess (for rewrite rules )' ),
	// Deprecated files
	'wp-layout.css' => __( 'Stylesheet' ), 'wp-comments.php' => __( 'Comments Template' ), 'wp-comments-popup.php' => __( 'Popup Comments Template' ));
function get_file_description( $file ) {
	global $wp_file_descriptions;

	if ( isset( $wp_file_descriptions[basename( $file )] ) ) {
		return $wp_file_descriptions[basename( $file )];
	}
	elseif ( file_exists( ABSPATH . $file ) && is_file( ABSPATH . $file ) ) {
		$template_data = implode( '', file( ABSPATH . $file ) );
		if ( preg_match( "|Template Name:(.*)|i", $template_data, $name ))
			return $name[1];
	}

	return basename( $file );
}

function get_home_path() {
	$home = get_option( 'home' );
	if ( $home != '' && $home != get_option( 'siteurl' ) ) {
		$home_path = parse_url( $home );
		$home_path = $home_path['path'];
		$root = str_replace( $_SERVER["PHP_SELF"], '', $_SERVER["SCRIPT_FILENAME"] );
		$home_path = trailingslashit( $root.$home_path );
	} else {
		$home_path = ABSPATH;
	}

	return $home_path;
}

function get_real_file_to_edit( $file ) {
	if ('index.php' == $file || '.htaccess' == $file ) {
		$real_file = get_home_path() . $file;
	} else {
		$real_file = WP_CONTENT_DIR . $file;
	}

	return $real_file;
}
//$folder = Full path to folder
//$levels = Levels of folders to follow, Default: 100 (PHP Loop limit)
function list_files( $folder = '', $levels = 100 ) {
	if( empty($folder) )
		return false;

	if( ! $levels )
		return false;

	$files = array();
	if ( $dir = @opendir( $folder ) ) {
		while (($file = readdir( $dir ) ) !== false ) {
			if ( in_array($file, array('.', '..') ) )
				continue;
			if ( is_dir( $folder . '/' . $file ) ) {
				$files2 = list_files( $folder . '/' . $file, $levels - 1);
				if( $files2 )
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

function get_temp_dir() {
	if ( defined('WP_TEMP_DIR') )
		return trailingslashit(WP_TEMP_DIR);

	$temp = WP_CONTENT_DIR . '/';
	if ( is_dir($temp) && is_writable($temp) )
		return $temp;

	if  ( function_exists('sys_get_temp_dir') )
		return trailingslashit(sys_get_temp_dir());

	return '/tmp/';
}

function wp_tempnam($filename = '', $dir = ''){
	if ( empty($dir) )
		$dir = get_temp_dir();
	$filename = basename($filename);
	if ( empty($filename) )
		$filename = time();

	$filename = $dir . wp_unique_filename($dir, $filename);
	touch($filename);
	return $filename;
}

function validate_file_to_edit( $file, $allowed_files = '' ) {
	$file = stripslashes( $file );

	$code = validate_file( $file, $allowed_files );

	if (!$code )
		return $file;

	switch ( $code ) {
		case 1 :
			wp_die( __('Sorry, can&#8217;t edit files with ".." in the name. If you are trying to edit a file in your WordPress home directory, you can just type the name of the file in.' ));

		case 2 :
			wp_die( __('Sorry, can&#8217;t call files with their real path.' ));

		case 3 :
			wp_die( __('Sorry, that file cannot be edited.' ));
	}
}

// array wp_handle_upload ( array &file [, array overrides] )
// file: reference to a single element of $_FILES. Call the function once for each uploaded file.
// overrides: an associative array of names=>values to override default variables with extract( $overrides, EXTR_OVERWRITE ).
// On success, returns an associative array of file attributes.
// On failure, returns $overrides['upload_error_handler'](&$file, $message ) or array( 'error'=>$message ).
function wp_handle_upload( &$file, $overrides = false ) {
	// The default error handler.
	if (! function_exists( 'wp_handle_upload_error' ) ) {
		function wp_handle_upload_error( &$file, $message ) {
			return array( 'error'=>$message );
		}
	}

	// You may define your own function and pass the name in $overrides['upload_error_handler']
	$upload_error_handler = 'wp_handle_upload_error';

	// $_POST['action'] must be set and its value must equal $overrides['action'] or this:
	$action = 'wp_handle_upload';

	// Courtesy of php.net, the strings that describe the error indicated in $_FILES[{form field}]['error'].
	$upload_error_strings = array( false,
		__( "The uploaded file exceeds the <code>upload_max_filesize</code> directive in <code>php.ini</code>." ),
		__( "The uploaded file exceeds the <em>MAX_FILE_SIZE</em> directive that was specified in the HTML form." ),
		__( "The uploaded file was only partially uploaded." ),
		__( "No file was uploaded." ),
		__( "Missing a temporary folder." ),
		__( "Failed to write file to disk." ));

	// All tests are on by default. Most can be turned off by $override[{test_name}] = false;
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
	if ( $file['error'] > 0 )
		return $upload_error_handler( $file, $upload_error_strings[$file['error']] );

	// A non-empty file will pass this test.
	if ( $test_size && !($file['size'] > 0 ) )
		return $upload_error_handler( $file, __( 'File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your php.ini.' ));

	// A properly uploaded file will pass this test. There should be no reason to override this one.
	if (! @ is_uploaded_file( $file['tmp_name'] ) )
		return $upload_error_handler( $file, __( 'Specified file failed upload test.' ));

	// A correct MIME type will pass this test. Override $mimes or use the upload_mimes filter.
	if ( $test_type ) {
		$wp_filetype = wp_check_filetype( $file['name'], $mimes );

		extract( $wp_filetype );

		if ( ( !$type || !$ext ) && !current_user_can( 'unfiltered_upload' ) )
			return $upload_error_handler( $file, __( 'File type does not meet security guidelines. Try another.' ));

		if ( !$ext )
			$ext = ltrim(strrchr($file['name'], '.'), '.');

		if ( !$type )
			$type = $file['type'];
	}

	// A writable uploads dir will pass this test. Again, there's no point overriding this one.
	if ( ! ( ( $uploads = wp_upload_dir() ) && false === $uploads['error'] ) )
		return $upload_error_handler( $file, $uploads['error'] );

	$filename = wp_unique_filename( $uploads['path'], $file['name'], $unique_filename_callback );

	// Move the file to the uploads dir
	$new_file = $uploads['path'] . "/$filename";
	if ( false === @ move_uploaded_file( $file['tmp_name'], $new_file ) ) {
		return $upload_error_handler( $file, sprintf( __('The uploaded file could not be moved to %s.' ), $uploads['path'] ) );
	}

	// Set correct file permissions
	$stat = stat( dirname( $new_file ));
	$perms = $stat['mode'] & 0000666;
	@ chmod( $new_file, $perms );

	// Compute the URL
	$url = $uploads['url'] . "/$filename";

	$return = apply_filters( 'wp_handle_upload', array( 'file' => $new_file, 'url' => $url, 'type' => $type ) );

	return $return;
}
// Pass this function an array similar to that of a $_FILES POST array.
function wp_handle_sideload( &$file, $overrides = false ) {
	// The default error handler.
	if (! function_exists( 'wp_handle_upload_error' ) ) {
		function wp_handle_upload_error( &$file, $message ) {
			return array( 'error'=>$message );
		}
	}

	// You may define your own function and pass the name in $overrides['upload_error_handler']
	$upload_error_handler = 'wp_handle_upload_error';

	// $_POST['action'] must be set and its value must equal $overrides['action'] or this:
	$action = 'wp_handle_sideload';

	// Courtesy of php.net, the strings that describe the error indicated in $_FILES[{form field}]['error'].
	$upload_error_strings = array( false,
		__( "The file exceeds the <code>upload_max_filesize</code> directive in <code>php.ini</code>." ),
		__( "The file exceeds the <em>MAX_FILE_SIZE</em> directive that was specified in the HTML form." ),
		__( "The file was only partially uploaded." ),
		__( "No file was sent." ),
		__( "Missing a temporary folder." ),
		__( "Failed to write file to disk." ));

	// All tests are on by default. Most can be turned off by $override[{test_name}] = false;
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
	if ( $file['error'] > 0 )
		return $upload_error_handler( $file, $upload_error_strings[$file['error']] );

	// A non-empty file will pass this test.
	if ( $test_size && !(filesize($file['tmp_name']) > 0 ) )
		return $upload_error_handler( $file, __( 'File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your php.ini.' ));

	// A properly uploaded file will pass this test. There should be no reason to override this one.
	if (! @ is_file( $file['tmp_name'] ) )
		return $upload_error_handler( $file, __( 'Specified file does not exist.' ));

	// A correct MIME type will pass this test. Override $mimes or use the upload_mimes filter.
	if ( $test_type ) {
		$wp_filetype = wp_check_filetype( $file['name'], $mimes );

		extract( $wp_filetype );

		if ( ( !$type || !$ext ) && !current_user_can( 'unfiltered_upload' ) )
			return $upload_error_handler( $file, __( 'File type does not meet security guidelines. Try another.' ));

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

	$return = apply_filters( 'wp_handle_upload', array( 'file' => $new_file, 'url' => $url, 'type' => $type ) );

	return $return;
}

/**
* Downloads a url to a local file using the Snoopy HTTP Class
*
* @param string $url the URL of the file to download
* @return mixed WP_Error on failure, string Filename on success.
*/
function download_url( $url ) {
	//WARNING: The file is not automatically deleted, The script must unlink() the file.
	if( ! $url )
		return new WP_Error('http_no_url', __('Invalid URL Provided'));

	$tmpfname = wp_tempnam($url);
	if( ! $tmpfname )
		return new WP_Error('http_no_file', __('Could not create Temporary file'));

	$handle = @fopen($tmpfname, 'w');
	if( ! $handle )
		return new WP_Error('http_no_file', __('Could not create Temporary file'));

	require_once( ABSPATH . 'wp-includes/class-snoopy.php' );
	$snoopy = new Snoopy();
	$snoopy->fetch($url);

	if( $snoopy->status != '200' ){
		fclose($handle);
		unlink($tmpfname);
		return new WP_Error('http_404', trim($snoopy->response_code));
	}
	fwrite($handle, $snoopy->results);
	fclose($handle);

	return $tmpfname;
}

function unzip_file($file, $to) {
	global $wp_filesystem;

	if ( ! $wp_filesystem || !is_object($wp_filesystem) )
		return new WP_Error('fs_unavailable', __('Could not access filesystem.'));

	$fs =& $wp_filesystem;

	require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');

	$archive = new PclZip($file);

	// Is the archive valid?
	if ( false == ($archive_files = $archive->extract(PCLZIP_OPT_EXTRACT_AS_STRING)) )
		return new WP_Error('incompatible_archive', __('Incompatible archive'), $archive->errorInfo(true));

	if ( 0 == count($archive_files) )
		return new WP_Error('empty_archive', __('Empty archive'));

	$to = trailingslashit($to);
	$path = explode('/', $to);
	$tmppath = '';
	for ( $j = 0; $j < count($path) - 1; $j++ ) {
		$tmppath .= $path[$j] . '/';
		if ( ! $fs->is_dir($tmppath) )
			$fs->mkdir($tmppath, 0755);
	}

	foreach ($archive_files as $file) {
		$path = explode('/', $file['filename']);
		$tmppath = '';

		// Loop through each of the items and check that the folder exists.
		for ( $j = 0; $j < count($path) - 1; $j++ ) {
			$tmppath .= $path[$j] . '/';
			if ( ! $fs->is_dir($to . $tmppath) )
				if ( !$fs->mkdir($to . $tmppath, 0755) )
					return new WP_Error('mkdir_failed', __('Could not create directory'), $to . $tmppath);
		}

		// We've made sure the folders are there, so let's extract the file now:
		if ( ! $file['folder'] )
			if ( !$fs->put_contents( $to . $file['filename'], $file['content']) )
				return new WP_Error('copy_failed', __('Could not copy file'), $to . $file['filename']);
			$fs->chmod($to . $file['filename'], 0644);
	}

	return true;
}

function copy_dir($from, $to) {
	global $wp_filesystem;

	$dirlist = $wp_filesystem->dirlist($from);

	$from = trailingslashit($from);
	$to = trailingslashit($to);

	foreach ( (array) $dirlist as $filename => $fileinfo ) {
		if ( 'f' == $fileinfo['type'] ) {
			if ( ! $wp_filesystem->copy($from . $filename, $to . $filename, true) )
				return new WP_Error('copy_failed', __('Could not copy file'), $to . $filename);
			$wp_filesystem->chmod($to . $filename, 0644);
		} elseif ( 'd' == $fileinfo['type'] ) {
			if ( !$wp_filesystem->mkdir($to . $filename, 0755) )
				return new WP_Error('mkdir_failed', __('Could not create directory'), $to . $filename);
			$result = copy_dir($from . $filename, $to . $filename);
			if ( is_wp_error($result) )
				return $result;
		}
	}
}

function WP_Filesystem( $args = false ) {
	global $wp_filesystem;

	require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php');

	$method = get_filesystem_method();

	if ( ! $method )
		return false;

	$abstraction_file = apply_filters('filesystem_method_file', ABSPATH . 'wp-admin/includes/class-wp-filesystem-'.$method.'.php', $method);
	if( ! file_exists($abstraction_file) )
		return;

	require_once($abstraction_file);
	$method = "WP_Filesystem_$method";

	$wp_filesystem = new $method($args);

	if ( $wp_filesystem->errors->get_error_code() )
		return false;

	if ( !$wp_filesystem->connect() )
		return false; //There was an erorr connecting to the server.

	return true;
}

function get_filesystem_method() {
	$method = false;
	if( function_exists('getmyuid') && function_exists('fileowner') ){
		$temp_file = wp_tempnam();
		if ( getmyuid() == fileowner($temp_file) )
			$method = 'direct';
		unlink($temp_file);
	}

	if ( ! $method && extension_loaded('ftp') ) $method = 'ftpext';
	if ( ! $method && ( extension_loaded('sockets') || function_exists('fsockopen') ) ) $method = 'ftpsockets'; //Sockets: Socket extension; PHP Mode: FSockopen / fwrite / fread
	return apply_filters('filesystem_method', $method);
}

?>