<?php

/**
 * Validates uploaded files and moves them to the temporary directory.
 *
 * @param array $file An item of `$_FILES`.
 * @param string|array $options Optional. Options to control behavior.
 * @return array|WP_Error Array of file paths, or WP_Error if validation fails.
 */
function wpcf7_unship_uploaded_file( $file, $options = '' ) {
	$filesystem = WPCF7_Filesystem::get_instance();

	$options = wp_parse_args( $options, array(
		'required' => false,
		'filetypes' => '',
		'limit' => MB_IN_BYTES,
	) );

	foreach ( array( 'name', 'size', 'tmp_name', 'error' ) as $key ) {
		if ( ! isset( $file[$key] ) ) {
			$file[$key] = array();
		}
	}

	$names = wpcf7_array_flatten( $file['name'] );
	$sizes = wpcf7_array_flatten( $file['size'] );
	$tmp_names = wpcf7_array_flatten( $file['tmp_name'] );
	$errors = wpcf7_array_flatten( $file['error'] );

	foreach ( $errors as $error ) {
		if ( ! empty( $error ) and UPLOAD_ERR_NO_FILE !== $error ) {
			return new WP_Error( 'wpcf7_upload_failed_php_error',
				wpcf7_get_message( 'upload_failed_php_error' )
			);
		}
	}

	if ( isset( $options['schema'] ) and isset( $options['name'] ) ) {
		$context = array(
			'file' => true,
			'field' => $options['name'],
		);

		foreach ( $options['schema']->validate( $context ) as $result ) {
			if ( is_wp_error( $result ) ) {
				return $result;
			}
		}
	}

	// Move uploaded file to tmp dir
	$uploads_dir = wpcf7_upload_tmp_dir();
	$uploads_dir = wpcf7_maybe_add_random_dir( $uploads_dir );

	$uploaded_files = array();

	foreach ( $names as $key => $name ) {
		$tmp_name = $tmp_names[$key];

		if ( empty( $tmp_name ) or ! is_uploaded_file( $tmp_name ) ) {
			continue;
		}

		$filename = $name;
		$filename = wpcf7_canonicalize( $filename, array( 'strto' => 'as-is' ) );
		$filename = wpcf7_antiscript_file_name( $filename );

		$filename = apply_filters( 'wpcf7_upload_file_name',
			$filename, $name, $options
		);

		$filename = wp_unique_filename( $uploads_dir, $filename );
		$new_file = path_join( $uploads_dir, $filename );

		// phpcs:ignore Generic.PHP.ForbiddenFunctions.Found
		if ( false === @move_uploaded_file( $tmp_name, $new_file ) ) {
			return new WP_Error( 'wpcf7_upload_failed',
				wpcf7_get_message( 'upload_failed' )
			);
		}

		// Make sure the uploaded file is only readable for the owner process.
		$filesystem->chmod( $new_file, 0400 );

		$uploaded_files[] = $new_file;
	}

	return $uploaded_files;
}


add_filter(
	'wpcf7_messages',
	'wpcf7_file_messages',
	10, 1
);

/**
 * A wpcf7_messages filter callback that adds messages for
 * file-uploading fields.
 */
function wpcf7_file_messages( $messages ) {
	return array_merge( $messages, array(
		'upload_failed' => array(
			'description' => __( 'Uploading a file fails for any reason', 'contact-form-7' ),
			'default' => __( 'There was an unknown error uploading the file.', 'contact-form-7' ),
		),

		'upload_file_type_invalid' => array(
			'description' => __( 'Uploaded file is not allowed for file type', 'contact-form-7' ),
			'default' => __( 'You are not allowed to upload files of this type.', 'contact-form-7' ),
		),

		'upload_file_too_large' => array(
			'description' => __( 'Uploaded file is too large', 'contact-form-7' ),
			'default' => __( 'The uploaded file is too large.', 'contact-form-7' ),
		),

		'upload_failed_php_error' => array(
			'description' => __( 'Uploading a file fails for PHP error', 'contact-form-7' ),
			'default' => __( 'There was an error uploading the file.', 'contact-form-7' ),
		),
	) );
}


add_filter(
	'wpcf7_form_enctype',
	'wpcf7_file_form_enctype_filter',
	10, 1
);

/**
 * A wpcf7_form_enctype filter callback that sets the enctype attribute
 * to multipart/form-data if the form has file-uploading fields.
 */
function wpcf7_file_form_enctype_filter( $enctype ) {
	$multipart = (bool) wpcf7_scan_form_tags( array(
		'feature' => 'file-uploading',
	) );

	if ( $multipart ) {
		$enctype = 'multipart/form-data';
	}

	return $enctype;
}


/**
 * Converts a MIME type string to an array of corresponding file extensions.
 *
 * @param string $mime MIME type.
 *                     Wildcard (*) is available for the subtype part.
 * @return array Corresponding file extensions.
 */
function wpcf7_convert_mime_to_ext( $mime ) {
	static $mime_types = array();

	$mime_types = wp_get_mime_types();

	$results = array();

	if ( preg_match( '%^([a-z]+)/([*]|[a-z0-9.+-]+)$%i', $mime, $matches ) ) {
		foreach ( $mime_types as $key => $val ) {
			if (
				'*' === $matches[2] and str_starts_with( $val, $matches[1] . '/' ) or
				$val === $matches[0]
			) {
				$results = array_merge( $results, explode( '|', $key ) );
			}
		}
	}

	$results = array_unique( $results );
	$results = array_filter( $results );
	$results = array_values( $results );

	return $results;
}


/**
 * Returns a formatted list of acceptable filetypes.
 *
 * @param string|array $types Optional. Array of filetypes.
 * @param string $format Optional. Pre-defined format designator.
 * @return string Formatted list of acceptable filetypes.
 */
function wpcf7_acceptable_filetypes( $types = 'default', $format = 'regex' ) {
	if ( 'default' === $types or empty( $types ) ) {
		$types = array(
			'audio/*',
			'video/*',
			'image/*',
		);
	} else {
		$types = array_map(
			static function ( $type ) {
				if ( is_string( $type ) ) {
					return preg_split( '/[\s|,]+/', strtolower( $type ) );
				}
			},
			(array) $types
		);

		$types = wpcf7_array_flatten( $types );
		$types = array_filter( array_unique( $types ) );
	}

	if ( 'attr' === $format or 'attribute' === $format ) {
		$types = array_map(
			static function ( $type ) {
				if ( false === strpos( $type, '/' ) ) {
					return sprintf( '.%s', trim( $type, '.' ) );
				} elseif ( preg_match( '%^([a-z]+)/[*]$%i', $type, $matches ) ) {
					if (
						in_array( $matches[1], array( 'audio', 'video', 'image' ), true )
					) {
						return $type;
					} else {
						return '';
					}
				} elseif ( wpcf7_convert_mime_to_ext( $type ) ) {
					return $type;
				}
			},
			$types
		);

		$types = array_filter( $types );

		return implode( ',', $types );

	} elseif ( 'regex' === $format ) {
		$types = array_map(
			static function ( $type ) {
				if ( false === strpos( $type, '/' ) ) {
					return preg_quote( trim( $type, '.' ) );
				} elseif ( $type = wpcf7_convert_mime_to_ext( $type ) ) {
					return $type;
				}
			},
			$types
		);

		$types = wpcf7_array_flatten( $types );
		$types = array_filter( array_unique( $types ) );

		return implode( '|', $types );
	}

	return '';
}


add_action(
	'wpcf7_init',
	'wpcf7_init_uploads',
	10, 0
);

/**
 * Initializes the temporary directory for uploaded files.
 */
function wpcf7_init_uploads() {
	$dir = wpcf7_upload_tmp_dir();

	if ( ! is_dir( $dir ) or ! wp_is_writable( $dir ) ) {
		return;
	}

	$htaccess_file = path_join( $dir, '.htaccess' );

	if ( file_exists( $htaccess_file ) ) {
		list( $first_line_comment ) = (array) file(
			$htaccess_file,
			FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
		);

		if ( '# Apache 2.4+' === $first_line_comment ) {
			return;
		}
	}

	$filesystem = WPCF7_Filesystem::get_instance();

	$htaccess_body = '
# Apache 2.4+
<IfModule authz_core_module>
    Require all denied
</IfModule>

# Apache 2.2
<IfModule !authz_core_module>
    Deny from all
</IfModule>
';

	$filesystem->put_contents( $htaccess_file, ltrim( $htaccess_body ) );
}


/**
 * Creates a child directory with a randomly generated name.
 *
 * @param string $dir The parent directory path.
 * @return string The child directory path if created, otherwise the parent.
 */
function wpcf7_maybe_add_random_dir( $dir ) {
	do {
		$dir_new = path_join( $dir, zeroise( wp_rand(), 10 ) );
	} while ( file_exists( $dir_new ) );

	if ( wp_mkdir_p( $dir_new ) ) {
		return $dir_new;
	}

	return $dir;
}


/**
 * Returns the directory path for uploaded files.
 *
 * @return string Directory path.
 */
function wpcf7_upload_tmp_dir() {
	static $output = '';

	if ( $output ) {
		return $output;
	}

	if ( defined( 'WPCF7_UPLOADS_TMP_DIR' ) ) {
		$dir = path_join( WP_CONTENT_DIR, WPCF7_UPLOADS_TMP_DIR );
		wp_mkdir_p( $dir );

		if ( wpcf7_is_file_path_in_content_dir( $dir ) ) {
			return $output = $dir;
		}
	}

	$dir = path_join( wpcf7_upload_dir( 'dir' ), 'wpcf7_uploads' );
	wp_mkdir_p( $dir );

	return $output = $dir;
}


add_action(
	'shutdown',
	'wpcf7_cleanup_upload_files',
	20, 0
);

/**
 * Cleans up files in the temporary directory for uploaded files.
 *
 * @param int $seconds Files older than this are removed. Default 60.
 * @param int $max Maximum number of files to be removed in a function call.
 *                 Default 100.
 */
function wpcf7_cleanup_upload_files( $seconds = 60, $max = 100 ) {
	$dir = trailingslashit( wpcf7_upload_tmp_dir() );

	if (
		! is_dir( $dir ) or
		! is_readable( $dir ) or
		! wp_is_writable( $dir )
	) {
		return;
	}

	$seconds = absint( $seconds );
	$max = absint( $max );
	$count = 0;

	if ( $handle = opendir( $dir ) ) {
		while ( false !== ( $file = readdir( $handle ) ) ) {
			if ( '.' === $file or '..' === $file or '.htaccess' === $file ) {
				continue;
			}

			$mtime = @filemtime( path_join( $dir, $file ) );

			if ( $mtime and time() < $mtime + $seconds ) { // less than $seconds old
				continue;
			}

			wpcf7_rmdir_p( path_join( $dir, $file ) );
			$count += 1;

			if ( $max <= $count ) {
				break;
			}
		}

		closedir( $handle );
	}
}


add_action(
	'wpcf7_admin_warnings',
	'wpcf7_file_display_warning_message',
	10, 3
);

/**
 * Displays warning messages about file-uploading fields.
 */
function wpcf7_file_display_warning_message( $page, $action, $object ) {
	if ( $object instanceof WPCF7_ContactForm ) {
		$contact_form = $object;
	} else {
		return;
	}

	$has_tags = (bool) $contact_form->scan_form_tags( array(
		'feature' => 'file-uploading',
	) );

	if ( ! $has_tags ) {
		return;
	}

	$uploads_dir = wpcf7_upload_tmp_dir();

	if ( ! is_dir( $uploads_dir ) or ! wp_is_writable( $uploads_dir ) ) {
		wp_admin_notice(
			sprintf(
				/* translators: %s: the path of the temporary folder */
				__( 'This contact form has file uploading fields, but the temporary folder for the files (%s) does not exist or is not writable. You can create the folder or change its permission manually.', 'contact-form-7' ),
				$uploads_dir
			),
			array( 'type' => 'warning' )
		);
	}
}
