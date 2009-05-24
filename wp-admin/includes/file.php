<?php
/**
 * File contains all the administration image manipulation functions.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** The descriptions for theme files. */
$wp_file_descriptions = array (
	'index.php' => __( 'Main Index Template' ),
	'style.css' => __( 'Stylesheet' ),
	'rtl.css' => __( 'RTL Stylesheet' ),
	'comments.php' => __( 'Comments' ),
	'comments-popup.php' => __( 'Popup Comments' ),
	'footer.php' => __( 'Footer' ),
	'header.php' => __( 'Header' ),
	'sidebar.php' => __( 'Sidebar' ),
	'archive.php' => __( 'Archives' ),
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
	'wp-layout.css' => __( 'Stylesheet' ), 'wp-comments.php' => __( 'Comments Template' ), 'wp-comments-popup.php' => __( 'Popup Comments Template' ));

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $file
 * @return unknown
 */
function get_file_description( $file ) {
	global $wp_file_descriptions;

	if ( isset( $wp_file_descriptions[basename( $file )] ) ) {
		return $wp_file_descriptions[basename( $file )];
	}
	elseif ( file_exists( WP_CONTENT_DIR . $file ) && is_file( WP_CONTENT_DIR . $file ) ) {
		$template_data = implode( '', file( WP_CONTENT_DIR . $file ) );
		if ( preg_match( '|Template Name:(.*)$|mi', $template_data, $name ))
			return _cleanup_header_comment($name[1]) . ' Page Template';
	}

	return basename( $file );
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
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
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $file
 * @return unknown
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
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param string $folder Optional. Full path to folder
 * @param int $levels Optional. Levels of folders to follow, Default: 100 (PHP Loop limit).
 * @return bool|array
 */
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

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
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

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $filename
 * @param unknown_type $dir
 * @return unknown
 */
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

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $file
 * @param unknown_type $allowed_files
 * @return unknown
 */
function validate_file_to_edit( $file, $allowed_files = '' ) {
	$file = stripslashes( $file );

	$code = validate_file( $file, $allowed_files );

	if (!$code )
		return $file;

	switch ( $code ) {
		case 1 :
			wp_die( __('Sorry, can&#8217;t edit files with &#8220;..&#8221; in the name. If you are trying to edit a file in your WordPress home directory, you can just type the name of the file in.' ));

		case 2 :
			wp_die( __('Sorry, can&#8217;t call files with their real path.' ));

		case 3 :
			wp_die( __('Sorry, that file cannot be edited.' ));
	}
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param array $file Reference to a single element of $_FILES. Call the function once for each uploaded file.
 * @param array $overrides Optional. An associative array of names=>values to override default variables with extract( $overrides, EXTR_OVERWRITE ).
 * @return array On success, returns an associative array of file attributes. On failure, returns $overrides['upload_error_handler'](&$file, $message ) or array( 'error'=>$message ).
 */
function wp_handle_upload( &$file, $overrides = false, $time = null ) {
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
	$action = 'wp_handle_upload';

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
	} else {
		$type = '';
	}

	// A writable uploads dir will pass this test. Again, there's no point overriding this one.
	if ( ! ( ( $uploads = wp_upload_dir($time) ) && false === $uploads['error'] ) )
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

	return apply_filters( 'wp_handle_upload', array( 'file' => $new_file, 'url' => $url, 'type' => $type ) );
}

/**
 * {@internal Missing Short Description}}
 *
 * Pass this function an array similar to that of a $_FILES POST array.
 *
 * @since unknown
 *
 * @param unknown_type $file
 * @param unknown_type $overrides
 * @return unknown
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
 * Downloads a url to a local file using the Snoopy HTTP Class.
 *
 * @since unknown
 * @todo Transition over to using the new HTTP Request API (jacob).
 *
 * @param string $url the URL of the file to download
 * @return mixed WP_Error on failure, string Filename on success.
 */
function download_url( $url ) {
	//WARNING: The file is not automatically deleted, The script must unlink() the file.
	if ( ! $url )
		return new WP_Error('http_no_url', __('Invalid URL Provided'));

	$tmpfname = wp_tempnam($url);
	if ( ! $tmpfname )
		return new WP_Error('http_no_file', __('Could not create Temporary file'));

	$handle = @fopen($tmpfname, 'wb');
	if ( ! $handle )
		return new WP_Error('http_no_file', __('Could not create Temporary file'));

	$response = wp_remote_get($url, array('timeout' => 30));

	if ( is_wp_error($response) ) {
		fclose($handle);
		unlink($tmpfname);
		return $response;
	}

	if ( $response['response']['code'] != '200' ){
		fclose($handle);
		unlink($tmpfname);
		return new WP_Error('http_404', trim($response['response']['message']));
	}

	fwrite($handle, $response['body']);
	fclose($handle);

	return $tmpfname;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $file
 * @param unknown_type $to
 * @return unknown
 */
function unzip_file($file, $to) {
	global $wp_filesystem;

	if ( ! $wp_filesystem || !is_object($wp_filesystem) )
		return new WP_Error('fs_unavailable', __('Could not access filesystem.'));

	// Unzip uses a lot of memory
	@ini_set('memory_limit', '256M');

	$fs =& $wp_filesystem;

	require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');

	$archive = new PclZip($file);

	// Is the archive valid?
	if ( false == ($archive_files = $archive->extract(PCLZIP_OPT_EXTRACT_AS_STRING)) )
		return new WP_Error('incompatible_archive', __('Incompatible archive'), $archive->errorInfo(true));

	if ( 0 == count($archive_files) )
		return new WP_Error('empty_archive', __('Empty archive'));

	$path = explode('/', untrailingslashit($to));
	for ( $i = count($path); $i > 0; $i-- ) { //>0 = first element is empty allways for paths starting with '/'
		$tmppath = implode('/', array_slice($path, 0, $i) );
		if ( $fs->is_dir($tmppath) ) { //Found the highest folder that exists, Create from here(ie +1)
			for ( $i = $i + 1; $i <= count($path); $i++ ) {
				$tmppath = implode('/', array_slice($path, 0, $i) );
				if ( ! $fs->mkdir($tmppath, FS_CHMOD_DIR) )
					return new WP_Error('mkdir_failed', __('Could not create directory'), $tmppath);
			}
			break; //Exit main for loop
		}
	}

	$to = trailingslashit($to);
	foreach ($archive_files as $file) {
		$path = $file['folder'] ? $file['filename'] : dirname($file['filename']);
		$path = explode('/', $path);
		for ( $i = count($path); $i >= 0; $i-- ) { //>=0 as the first element contains data
			if ( empty($path[$i]) )
				continue;
			$tmppath = $to . implode('/', array_slice($path, 0, $i) );
			if ( $fs->is_dir($tmppath) ) {//Found the highest folder that exists, Create from here
				for ( $i = $i + 1; $i <= count($path); $i++ ) { //< count() no file component please.
					$tmppath = $to . implode('/', array_slice($path, 0, $i) );
					if ( ! $fs->is_dir($tmppath) && ! $fs->mkdir($tmppath, FS_CHMOD_DIR) )
						return new WP_Error('mkdir_failed', __('Could not create directory'), $tmppath);
				}
				break; //Exit main for loop
			}
		}

		// We've made sure the folders are there, so let's extract the file now:
		if ( ! $file['folder'] ) {
			if ( !$fs->put_contents( $to . $file['filename'], $file['content']) )
				return new WP_Error('copy_failed', __('Could not copy file'), $to . $file['filename']);
			$fs->chmod($to . $file['filename'], FS_CHMOD_FILE);
		}
	}
	return true;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $from
 * @param unknown_type $to
 * @return unknown
 */
function copy_dir($from, $to) {
	global $wp_filesystem;

	$dirlist = $wp_filesystem->dirlist($from);

	$from = trailingslashit($from);
	$to = trailingslashit($to);

	foreach ( (array) $dirlist as $filename => $fileinfo ) {
		if ( 'f' == $fileinfo['type'] ) {
			if ( ! $wp_filesystem->copy($from . $filename, $to . $filename, true) ) {
				// If copy failed, chmod file to 0644 and try again.
				$wp_filesystem->chmod($to . $filename, 0644);
				if ( ! $wp_filesystem->copy($from . $filename, $to . $filename, true) )
					return new WP_Error('copy_failed', __('Could not copy file'), $to . $filename);
			}
			$wp_filesystem->chmod($to . $filename, FS_CHMOD_FILE);
		} elseif ( 'd' == $fileinfo['type'] ) {
			if ( !$wp_filesystem->is_dir($to . $filename) ) {
				if ( !$wp_filesystem->mkdir($to . $filename, FS_CHMOD_DIR) )
					return new WP_Error('mkdir_failed', __('Could not create directory'), $to . $filename);
			}
			$result = copy_dir($from . $filename, $to . $filename);
			if ( is_wp_error($result) )
				return $result;
		}
	}
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $args
 * @return unknown
 */
function WP_Filesystem( $args = false ) {
	global $wp_filesystem;

	require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php');

	$method = get_filesystem_method($args);

	if ( ! $method )
		return false;

	$abstraction_file = apply_filters('filesystem_method_file', ABSPATH . 'wp-admin/includes/class-wp-filesystem-' . $method . '.php', $method);
	if( ! file_exists($abstraction_file) )
		return;

	require_once($abstraction_file);
	$method = "WP_Filesystem_$method";

	$wp_filesystem = new $method($args);

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
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $args
 * @return unknown
 */
function get_filesystem_method($args = array()) {
	$method = false;
	if( function_exists('getmyuid') && function_exists('fileowner') ){
		$temp_file = wp_tempnam();
		if ( getmyuid() == fileowner($temp_file) )
			$method = 'direct';
		unlink($temp_file);
	}

	if ( ! $method && isset($args['connection_type']) && 'ssh' == $args['connection_type'] && extension_loaded('ssh2') && extension_loaded('sockets') ) $method = 'ssh2';
	if ( ! $method && extension_loaded('ftp') ) $method = 'ftpext';
	if ( ! $method && ( extension_loaded('sockets') || function_exists('fsockopen') ) ) $method = 'ftpsockets'; //Sockets: Socket extension; PHP Mode: FSockopen / fwrite / fread
	return apply_filters('filesystem_method', $method);
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $form_post
 * @param unknown_type $type
 * @param unknown_type $error
 * @return unknown
 */
function request_filesystem_credentials($form_post, $type = '', $error = false) {
	$req_cred = apply_filters('request_filesystem_credentials', '', $form_post, $type, $error);
	if ( '' !== $req_cred )
		return $req_cred;

	if ( empty($type) )
		$type = get_filesystem_method();

	if ( 'direct' == $type )
		return true;

	$credentials = get_option('ftp_credentials', array( 'hostname' => '', 'username' => ''));

	// If defined, set it to that, Else, If POST'd, set it to that, If not, Set it to whatever it previously was(saved details in option)
	$credentials['hostname'] = defined('FTP_HOST') ? FTP_HOST : (!empty($_POST['hostname']) ? $_POST['hostname'] : $credentials['hostname']);
	$credentials['username'] = defined('FTP_USER') ? FTP_USER : (!empty($_POST['username']) ? $_POST['username'] : $credentials['username']);
	$credentials['password'] = defined('FTP_PASS') ? FTP_PASS : (!empty($_POST['password']) ? $_POST['password'] : '');

	// Check to see if we are setting the public/private keys for ssh
	$credentials['public_key'] = defined('FTP_PUBKEY') ? FTP_PUBKEY : (!empty($_POST['public_key']) ? $_POST['public_key'] : '');
	$credentials['private_key'] = defined('FTP_PRIKEY') ? FTP_PRIKEY : (!empty($_POST['private_key']) ? $_POST['private_key'] : '');

	//sanitize the hostname, Some people might pass in odd-data:
	$credentials['hostname'] = preg_replace('|\w+://|', '', $credentials['hostname']); //Strip any schemes off

	if ( strpos($credentials['hostname'], ':') )
		list( $credentials['hostname'], $credentials['port'] ) = explode(':', $credentials['hostname'], 2);
	else
		unset($credentials['port']);

	if ( defined('FTP_SSH') || (isset($_POST['connection_type']) && 'ssh' == $_POST['connection_type']) )
		$credentials['connection_type'] = 'ssh';
	else if ( defined('FTP_SSL') || (isset($_POST['connection_type']) && 'ftps' == $_POST['connection_type']) )
		$credentials['connection_type'] = 'ftps';
	else if ( !isset($credentials['connection_type']) || (isset($_POST['connection_type']) && 'ftp' == $_POST['connection_type']) )
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
			$error_string = $error->get_error_message();
		echo '<div id="message" class="error"><p>' . $error_string . '</p></div>';
	}
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
<p><?php _e('To perform the requested action, connection information is required.') ?></p>

<table class="form-table">
<tr valign="top">
<th scope="row"><label for="hostname"><?php _e('Hostname') ?></label></th>
<td><input name="hostname" type="text" id="hostname" value="<?php echo esc_attr($hostname); if ( !empty($port) ) echo ":$port"; ?>"<?php if( defined('FTP_HOST') ) echo ' disabled="disabled"' ?> size="40" /></td>
</tr>

<tr valign="top">
<th scope="row"><label for="username"><?php _e('Username') ?></label></th>
<td><input name="username" type="text" id="username" value="<?php echo esc_attr($username) ?>"<?php if( defined('FTP_USER') ) echo ' disabled="disabled"' ?> size="40" /></td>
</tr>

<tr valign="top">
<th scope="row"><label for="password"><?php _e('Password') ?></label></th>
<td><input name="password" type="password" id="password" value=""<?php if( defined('FTP_PASS') ) echo ' disabled="disabled"' ?> size="40" /><?php if( defined('FTP_PASS') && !empty($password) ) echo '<em>'.__('(Password not shown)').'</em>'; ?></td>
</tr>

<tr id="ssh_keys" valign="top" style="<?php if ( 'ssh' != $connection_type ) echo 'display:none' ?>">
<th scope="row"><?php _e('Authentication Keys') ?>
<div class="key-labels textright">
<label for="public_key"><?php _e('Public Key:') ?></label ><br />
<label for="private_key"><?php _e('Private Key:') ?></label>
</div></th>
<td><br /><input name="public_key" type="text" id="public_key" value="<?php echo esc_attr($public_key) ?>"<?php if( defined('FTP_PUBKEY') ) echo ' disabled="disabled"' ?> size="40" /><br /><input name="private_key" type="text" id="private_key" value="<?php echo esc_attr($private_key) ?>"<?php if( defined('FTP_PRIKEY') ) echo ' disabled="disabled"' ?> size="40" />
<div><?php _e('Enter the location on the server where the keys are located. If a passphrase is needed, enter that in the password field above.') ?></div></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Connection Type') ?></th>
<td>
<fieldset><legend class="screen-reader-text"><span><?php _e('Connection Type') ?></span></legend>
<label><input id="ftp" name="connection_type"  type="radio" value="ftp" <?php checked('ftp', $connection_type); if ( defined('FTP_SSL') || defined('FTP_SSH') ) echo ' disabled="disabled"'; ?>/> <?php _e('FTP') ?></label><br />
<label><input id="ftps" name="connection_type" type="radio" value="ftps" <?php checked('ftps', $connection_type); if ( defined('FTP_SSH') || defined('FTP_SSH') ) echo ' disabled="disabled"';  ?>/> <?php _e('FTPS (SSL)') ?></label><br />
<?php if ( extension_loaded('ssh2') ) { ?><label><input id="ssh" name="connection_type" type="radio" value="ssh" <?php checked('ssh', $connection_type);  if ( defined('FTP_SSL') || defined('FTP_SSH') ) echo ' disabled="disabled"'; ?>/> <?php _e('SSH') ?></label><?php } ?>
</fieldset>
</td>
</tr>
</table>

<?php if ( isset( $_POST['version'] ) ) : ?>
<input type="hidden" name="version" value="<?php echo esc_attr($_POST['version']) ?>" />
<?php endif; ?>
<?php if ( isset( $_POST['locale'] ) ) : ?>
<input type="hidden" name="locale" value="<?php echo esc_attr($_POST['locale']) ?>" />
<?php endif; ?>
<p class="submit">
<input id="upgrade" name="upgrade" type="submit" class="button" value="<?php esc_attr_e('Proceed'); ?>" />
</p>
</div>
</form>
<?php
	return false;
}

?>
