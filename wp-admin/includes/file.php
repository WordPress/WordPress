<?php

$wp_file_descriptions = array ('index.php' => __( 'Main Index Template' ), 'style.css' => __( 'Stylesheet' ), 'comments.php' => __( 'Comments' ), 'comments-popup.php' => __( 'Popup Comments' ), 'footer.php' => __( 'Footer' ), 'header.php' => __( 'Header' ), 'sidebar.php' => __( 'Sidebar' ), 'archive.php' => __( 'Archives' ), 'category.php' => __( 'Category Template' ), 'page.php' => __( 'Page Template' ), 'search.php' => __( 'Search Results' ), 'single.php' => __( 'Single Post' ), '404.php' => __( '404 Template' ), 'my-hacks.php' => __( 'my-hacks.php (legacy hacks support)' ), '.htaccess' => __( '.htaccess (for rewrite rules )' ),
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
		$real_file = get_home_path().$file;
	} else {
		$real_file = ABSPATH.$file;
	}

	return $real_file;
}

function validate_file( $file, $allowed_files = '' ) {
	if ( false !== strpos( $file, './' ))
		return 1;

	if (':' == substr( $file, 1, 1 ))
		return 2;

	if (!empty ( $allowed_files ) && (!in_array( $file, $allowed_files ) ) )
		return 3;

	return 0;
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
	}

	// A writable uploads dir will pass this test. Again, there's no point overriding this one.
	if ( ! ( ( $uploads = wp_upload_dir() ) && false === $uploads['error'] ) )
		return $upload_error_handler( $file, $uploads['error'] );

	// Increment the file number until we have a unique file to save in $dir. Use $override['unique_filename_callback'] if supplied.
	if ( isset( $unique_filename_callback ) && function_exists( $unique_filename_callback ) ) {
		$filename = $unique_filename_callback( $uploads['path'], $file['name'] );
	} else {
		$number = '';
		$filename = str_replace( '#', '_', $file['name'] );
		$filename = str_replace( array( '\\', "'" ), '', $filename );
		if ( empty( $ext) )
			$ext = '';
		else
			$ext = ".$ext";
		while ( file_exists( $uploads['path'] . "/$filename" ) ) {
			if ( '' == "$number$ext" )
				$filename = $filename . ++$number . $ext;
			else
				$filename = str_replace( "$number$ext", ++$number . $ext, $filename );
		}
		$filename = str_replace( $ext, '', $filename );
		$filename = sanitize_title_with_dashes( $filename ) . $ext;
	}

	// Move the file to the uploads dir
	$new_file = $uploads['path'] . "/$filename";
	if ( false === @ move_uploaded_file( $file['tmp_name'], $new_file ) )
		wp_die( printf( __('The uploaded file could not be moved to %s.' ), $uploads['path'] ));

	// Set correct file permissions
	$stat = stat( dirname( $new_file ));
	$perms = $stat['mode'] & 0000666;
	@ chmod( $new_file, $perms );

	// Compute the URL
	$url = $uploads['url'] . "/$filename";

	$return = apply_filters( 'wp_handle_upload', array( 'file' => $new_file, 'url' => $url, 'type' => $type ) );

	return $return;
}

?>
