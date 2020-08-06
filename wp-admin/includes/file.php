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
	// Archives.
	'index.php'             => __( 'Main Index Template' ),
	'archive.php'           => __( 'Archives' ),
	'author.php'            => __( 'Author Template' ),
	'taxonomy.php'          => __( 'Taxonomy Template' ),
	'category.php'          => __( 'Category Template' ),
	'tag.php'               => __( 'Tag Template' ),
	'home.php'              => __( 'Posts Page' ),
	'search.php'            => __( 'Search Results' ),
	'date.php'              => __( 'Date Template' ),
	// Content.
	'singular.php'          => __( 'Singular Template' ),
	'single.php'            => __( 'Single Post' ),
	'page.php'              => __( 'Single Page' ),
	'front-page.php'        => __( 'Homepage' ),
	'privacy-policy.php'    => __( 'Privacy Policy Page' ),
	// Attachments.
	'attachment.php'        => __( 'Attachment Template' ),
	'image.php'             => __( 'Image Attachment Template' ),
	'video.php'             => __( 'Video Attachment Template' ),
	'audio.php'             => __( 'Audio Attachment Template' ),
	'application.php'       => __( 'Application Attachment Template' ),
	// Embeds.
	'embed.php'             => __( 'Embed Template' ),
	'embed-404.php'         => __( 'Embed 404 Template' ),
	'embed-content.php'     => __( 'Embed Content Template' ),
	'header-embed.php'      => __( 'Embed Header Template' ),
	'footer-embed.php'      => __( 'Embed Footer Template' ),
	// Stylesheets.
	'style.css'             => __( 'Stylesheet' ),
	'editor-style.css'      => __( 'Visual Editor Stylesheet' ),
	'editor-style-rtl.css'  => __( 'Visual Editor RTL Stylesheet' ),
	'rtl.css'               => __( 'RTL Stylesheet' ),
	// Other.
	'my-hacks.php'          => __( 'my-hacks.php (legacy hacks support)' ),
	'.htaccess'             => __( '.htaccess (for rewrite rules )' ),
	// Deprecated files.
	'wp-layout.css'         => __( 'Stylesheet' ),
	'wp-comments.php'       => __( 'Comments Template' ),
	'wp-comments-popup.php' => __( 'Popup Comments Template' ),
	'comments-popup.php'    => __( 'Popup Comments' ),
);

/**
 * Gets the description for standard WordPress theme files.
 *
 * @since 1.5.0
 *
 * @global array $wp_file_descriptions Theme file descriptions.
 * @global array $allowed_files        List of allowed files.
 *
 * @param string $file Filesystem path or filename.
 * @return string Description of file from $wp_file_descriptions or basename of $file if description doesn't exist.
 *                Appends 'Page Template' to basename of $file if the file is a page template.
 */
function get_file_description( $file ) {
	global $wp_file_descriptions, $allowed_files;

	$dirname   = pathinfo( $file, PATHINFO_DIRNAME );
	$file_path = $allowed_files[ $file ];

	if ( isset( $wp_file_descriptions[ basename( $file ) ] ) && '.' === $dirname ) {
		return $wp_file_descriptions[ basename( $file ) ];
	} elseif ( file_exists( $file_path ) && is_file( $file_path ) ) {
		$template_data = implode( '', file( $file_path ) );

		if ( preg_match( '|Template Name:(.*)$|mi', $template_data, $name ) ) {
			/* translators: %s: Template name. */
			return sprintf( __( '%s Page Template' ), _cleanup_header_comment( $name[1] ) );
		}
	}

	return trim( basename( $file ) );
}

/**
 * Gets the absolute filesystem path to the root of the WordPress installation.
 *
 * @since 1.5.0
 *
 * @return string Full filesystem path to the root of the WordPress installation.
 */
function get_home_path() {
	$home    = set_url_scheme( get_option( 'home' ), 'http' );
	$siteurl = set_url_scheme( get_option( 'siteurl' ), 'http' );

	if ( ! empty( $home ) && 0 !== strcasecmp( $home, $siteurl ) ) {
		$wp_path_rel_to_home = str_ireplace( $home, '', $siteurl ); /* $siteurl - $home */
		$pos                 = strripos( str_replace( '\\', '/', $_SERVER['SCRIPT_FILENAME'] ), trailingslashit( $wp_path_rel_to_home ) );
		$home_path           = substr( $_SERVER['SCRIPT_FILENAME'], 0, $pos );
		$home_path           = trailingslashit( $home_path );
	} else {
		$home_path = ABSPATH;
	}

	return str_replace( '\\', '/', $home_path );
}

/**
 * Returns a listing of all files in the specified folder and all subdirectories up to 100 levels deep.
 *
 * The depth of the recursiveness can be controlled by the $levels param.
 *
 * @since 2.6.0
 * @since 4.9.0 Added the `$exclusions` parameter.
 *
 * @param string   $folder     Optional. Full path to folder. Default empty.
 * @param int      $levels     Optional. Levels of folders to follow, Default 100 (PHP Loop limit).
 * @param string[] $exclusions Optional. List of folders and files to skip.
 * @return bool|string[] False on failure, else array of files.
 */
function list_files( $folder = '', $levels = 100, $exclusions = array() ) {
	if ( empty( $folder ) ) {
		return false;
	}

	$folder = trailingslashit( $folder );

	if ( ! $levels ) {
		return false;
	}

	$files = array();

	$dir = @opendir( $folder );
	if ( $dir ) {
		while ( ( $file = readdir( $dir ) ) !== false ) {
			// Skip current and parent folder links.
			if ( in_array( $file, array( '.', '..' ), true ) ) {
				continue;
			}

			// Skip hidden and excluded files.
			if ( '.' === $file[0] || in_array( $file, $exclusions, true ) ) {
				continue;
			}

			if ( is_dir( $folder . $file ) ) {
				$files2 = list_files( $folder . $file, $levels - 1 );
				if ( $files2 ) {
					$files = array_merge( $files, $files2 );
				} else {
					$files[] = $folder . $file . '/';
				}
			} else {
				$files[] = $folder . $file;
			}
		}

		closedir( $dir );
	}

	return $files;
}

/**
 * Gets the list of file extensions that are editable in plugins.
 *
 * @since 4.9.0
 *
 * @param string $plugin Path to the plugin file relative to the plugins directory.
 * @return string[] Array of editable file extensions.
 */
function wp_get_plugin_file_editable_extensions( $plugin ) {

	$default_types = array(
		'bash',
		'conf',
		'css',
		'diff',
		'htm',
		'html',
		'http',
		'inc',
		'include',
		'js',
		'json',
		'jsx',
		'less',
		'md',
		'patch',
		'php',
		'php3',
		'php4',
		'php5',
		'php7',
		'phps',
		'phtml',
		'sass',
		'scss',
		'sh',
		'sql',
		'svg',
		'text',
		'txt',
		'xml',
		'yaml',
		'yml',
	);

	/**
	 * Filters the list of file types allowed for editing in the plugin editor.
	 *
	 * @since 2.8.0
	 * @since 4.9.0 Added the `$plugin` parameter.
	 *
	 * @param string[] $default_types An array of editable plugin file extensions.
	 * @param string   $plugin        Path to the plugin file relative to the plugins directory.
	 */
	$file_types = (array) apply_filters( 'editable_extensions', $default_types, $plugin );

	return $file_types;
}

/**
 * Gets the list of file extensions that are editable for a given theme.
 *
 * @since 4.9.0
 *
 * @param WP_Theme $theme Theme object.
 * @return string[] Array of editable file extensions.
 */
function wp_get_theme_file_editable_extensions( $theme ) {

	$default_types = array(
		'bash',
		'conf',
		'css',
		'diff',
		'htm',
		'html',
		'http',
		'inc',
		'include',
		'js',
		'json',
		'jsx',
		'less',
		'md',
		'patch',
		'php',
		'php3',
		'php4',
		'php5',
		'php7',
		'phps',
		'phtml',
		'sass',
		'scss',
		'sh',
		'sql',
		'svg',
		'text',
		'txt',
		'xml',
		'yaml',
		'yml',
	);

	/**
	 * Filters the list of file types allowed for editing in the theme editor.
	 *
	 * @since 4.4.0
	 *
	 * @param string[] $default_types An array of editable theme file extensions.
	 * @param WP_Theme $theme         The current theme object.
	 */
	$file_types = apply_filters( 'wp_theme_editor_filetypes', $default_types, $theme );

	// Ensure that default types are still there.
	return array_unique( array_merge( $file_types, $default_types ) );
}

/**
 * Prints file editor templates (for plugins and themes).
 *
 * @since 4.9.0
 */
function wp_print_file_editor_templates() {
	?>
	<script type="text/html" id="tmpl-wp-file-editor-notice">
		<div class="notice inline notice-{{ data.type || 'info' }} {{ data.alt ? 'notice-alt' : '' }} {{ data.dismissible ? 'is-dismissible' : '' }} {{ data.classes || '' }}">
			<# if ( 'php_error' === data.code ) { #>
				<p>
					<?php
					printf(
						/* translators: 1: Line number, 2: File path. */
						__( 'Your PHP code changes were rolled back due to an error on line %1$s of file %2$s. Please fix and try saving again.' ),
						'{{ data.line }}',
						'{{ data.file }}'
					);
					?>
				</p>
				<pre>{{ data.message }}</pre>
			<# } else if ( 'file_not_writable' === data.code ) { #>
				<p>
					<?php
					printf(
						/* translators: %s: Documentation URL. */
						__( 'You need to make this file writable before you can save your changes. See <a href="%s">Changing File Permissions</a> for more information.' ),
						__( 'https://wordpress.org/support/article/changing-file-permissions/' )
					);
					?>
				</p>
			<# } else { #>
				<p>{{ data.message || data.code }}</p>

				<# if ( 'lint_errors' === data.code ) { #>
					<p>
						<# var elementId = 'el-' + String( Math.random() ); #>
						<input id="{{ elementId }}"  type="checkbox">
						<label for="{{ elementId }}"><?php _e( 'Update anyway, even though it might break your site?' ); ?></label>
					</p>
				<# } #>
			<# } #>
			<# if ( data.dismissible ) { #>
				<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e( 'Dismiss' ); ?></span></button>
			<# } #>
		</div>
	</script>
	<?php
}

/**
 * Attempts to edit a file for a theme or plugin.
 *
 * When editing a PHP file, loopback requests will be made to the admin and the homepage
 * to attempt to see if there is a fatal error introduced. If so, the PHP change will be
 * reverted.
 *
 * @since 4.9.0
 *
 * @param string[] $args {
 *     Args. Note that all of the arg values are already unslashed. They are, however,
 *     coming straight from `$_POST` and are not validated or sanitized in any way.
 *
 *     @type string $file       Relative path to file.
 *     @type string $plugin     Path to the plugin file relative to the plugins directory.
 *     @type string $theme      Theme being edited.
 *     @type string $newcontent New content for the file.
 *     @type string $nonce      Nonce.
 * }
 * @return true|WP_Error True on success or `WP_Error` on failure.
 */
function wp_edit_theme_plugin_file( $args ) {
	if ( empty( $args['file'] ) ) {
		return new WP_Error( 'missing_file' );
	}
	$file = $args['file'];
	if ( 0 !== validate_file( $file ) ) {
		return new WP_Error( 'bad_file' );
	}

	if ( ! isset( $args['newcontent'] ) ) {
		return new WP_Error( 'missing_content' );
	}
	$content = $args['newcontent'];

	if ( ! isset( $args['nonce'] ) ) {
		return new WP_Error( 'missing_nonce' );
	}

	$plugin    = null;
	$theme     = null;
	$real_file = null;
	if ( ! empty( $args['plugin'] ) ) {
		$plugin = $args['plugin'];

		if ( ! current_user_can( 'edit_plugins' ) ) {
			return new WP_Error( 'unauthorized', __( 'Sorry, you are not allowed to edit plugins for this site.' ) );
		}

		if ( ! wp_verify_nonce( $args['nonce'], 'edit-plugin_' . $file ) ) {
			return new WP_Error( 'nonce_failure' );
		}

		if ( ! array_key_exists( $plugin, get_plugins() ) ) {
			return new WP_Error( 'invalid_plugin' );
		}

		if ( 0 !== validate_file( $file, get_plugin_files( $plugin ) ) ) {
			return new WP_Error( 'bad_plugin_file_path', __( 'Sorry, that file cannot be edited.' ) );
		}

		$editable_extensions = wp_get_plugin_file_editable_extensions( $plugin );

		$real_file = WP_PLUGIN_DIR . '/' . $file;

		$is_active = in_array(
			$plugin,
			(array) get_option( 'active_plugins', array() ),
			true
		);

	} elseif ( ! empty( $args['theme'] ) ) {
		$stylesheet = $args['theme'];
		if ( 0 !== validate_file( $stylesheet ) ) {
			return new WP_Error( 'bad_theme_path' );
		}

		if ( ! current_user_can( 'edit_themes' ) ) {
			return new WP_Error( 'unauthorized', __( 'Sorry, you are not allowed to edit templates for this site.' ) );
		}

		$theme = wp_get_theme( $stylesheet );
		if ( ! $theme->exists() ) {
			return new WP_Error( 'non_existent_theme', __( 'The requested theme does not exist.' ) );
		}

		if ( ! wp_verify_nonce( $args['nonce'], 'edit-theme_' . $stylesheet . '_' . $file ) ) {
			return new WP_Error( 'nonce_failure' );
		}

		if ( $theme->errors() && 'theme_no_stylesheet' === $theme->errors()->get_error_code() ) {
			return new WP_Error(
				'theme_no_stylesheet',
				__( 'The requested theme does not exist.' ) . ' ' . $theme->errors()->get_error_message()
			);
		}

		$editable_extensions = wp_get_theme_file_editable_extensions( $theme );

		$allowed_files = array();
		foreach ( $editable_extensions as $type ) {
			switch ( $type ) {
				case 'php':
					$allowed_files = array_merge( $allowed_files, $theme->get_files( 'php', -1 ) );
					break;
				case 'css':
					$style_files                = $theme->get_files( 'css', -1 );
					$allowed_files['style.css'] = $style_files['style.css'];
					$allowed_files              = array_merge( $allowed_files, $style_files );
					break;
				default:
					$allowed_files = array_merge( $allowed_files, $theme->get_files( $type, -1 ) );
					break;
			}
		}

		// Compare based on relative paths.
		if ( 0 !== validate_file( $file, array_keys( $allowed_files ) ) ) {
			return new WP_Error( 'disallowed_theme_file', __( 'Sorry, that file cannot be edited.' ) );
		}

		$real_file = $theme->get_stylesheet_directory() . '/' . $file;

		$is_active = ( get_stylesheet() === $stylesheet || get_template() === $stylesheet );

	} else {
		return new WP_Error( 'missing_theme_or_plugin' );
	}

	// Ensure file is real.
	if ( ! is_file( $real_file ) ) {
		return new WP_Error( 'file_does_not_exist', __( 'File does not exist! Please double check the name and try again.' ) );
	}

	// Ensure file extension is allowed.
	$extension = null;
	if ( preg_match( '/\.([^.]+)$/', $real_file, $matches ) ) {
		$extension = strtolower( $matches[1] );
		if ( ! in_array( $extension, $editable_extensions, true ) ) {
			return new WP_Error( 'illegal_file_type', __( 'Files of this type are not editable.' ) );
		}
	}

	$previous_content = file_get_contents( $real_file );

	if ( ! is_writeable( $real_file ) ) {
		return new WP_Error( 'file_not_writable' );
	}

	$f = fopen( $real_file, 'w+' );
	if ( false === $f ) {
		return new WP_Error( 'file_not_writable' );
	}

	$written = fwrite( $f, $content );
	fclose( $f );
	if ( false === $written ) {
		return new WP_Error( 'unable_to_write', __( 'Unable to write to file.' ) );
	}

	wp_opcache_invalidate( $real_file, true );

	if ( $is_active && 'php' === $extension ) {

		$scrape_key   = md5( rand() );
		$transient    = 'scrape_key_' . $scrape_key;
		$scrape_nonce = strval( rand() );
		// It shouldn't take more than 60 seconds to make the two loopback requests.
		set_transient( $transient, $scrape_nonce, 60 );

		$cookies       = wp_unslash( $_COOKIE );
		$scrape_params = array(
			'wp_scrape_key'   => $scrape_key,
			'wp_scrape_nonce' => $scrape_nonce,
		);
		$headers       = array(
			'Cache-Control' => 'no-cache',
		);

		/** This filter is documented in wp-includes/class-wp-http-streams.php */
		$sslverify = apply_filters( 'https_local_ssl_verify', false );

		// Include Basic auth in loopback requests.
		if ( isset( $_SERVER['PHP_AUTH_USER'] ) && isset( $_SERVER['PHP_AUTH_PW'] ) ) {
			$headers['Authorization'] = 'Basic ' . base64_encode( wp_unslash( $_SERVER['PHP_AUTH_USER'] ) . ':' . wp_unslash( $_SERVER['PHP_AUTH_PW'] ) );
		}

		// Make sure PHP process doesn't die before loopback requests complete.
		set_time_limit( 300 );

		// Time to wait for loopback requests to finish.
		$timeout = 100;

		$needle_start = "###### wp_scraping_result_start:$scrape_key ######";
		$needle_end   = "###### wp_scraping_result_end:$scrape_key ######";

		// Attempt loopback request to editor to see if user just whitescreened themselves.
		if ( $plugin ) {
			$url = add_query_arg( compact( 'plugin', 'file' ), admin_url( 'plugin-editor.php' ) );
		} elseif ( isset( $stylesheet ) ) {
			$url = add_query_arg(
				array(
					'theme' => $stylesheet,
					'file'  => $file,
				),
				admin_url( 'theme-editor.php' )
			);
		} else {
			$url = admin_url();
		}

		if ( function_exists( 'session_status' ) && PHP_SESSION_ACTIVE === session_status() ) {
			// Close any active session to prevent HTTP requests from timing out
			// when attempting to connect back to the site.
			session_write_close();
		}

		$url                    = add_query_arg( $scrape_params, $url );
		$r                      = wp_remote_get( $url, compact( 'cookies', 'headers', 'timeout', 'sslverify' ) );
		$body                   = wp_remote_retrieve_body( $r );
		$scrape_result_position = strpos( $body, $needle_start );

		$loopback_request_failure = array(
			'code'    => 'loopback_request_failed',
			'message' => __( 'Unable to communicate back with site to check for fatal errors, so the PHP change was reverted. You will need to upload your PHP file change by some other means, such as by using SFTP.' ),
		);
		$json_parse_failure       = array(
			'code' => 'json_parse_error',
		);

		$result = null;
		if ( false === $scrape_result_position ) {
			$result = $loopback_request_failure;
		} else {
			$error_output = substr( $body, $scrape_result_position + strlen( $needle_start ) );
			$error_output = substr( $error_output, 0, strpos( $error_output, $needle_end ) );
			$result       = json_decode( trim( $error_output ), true );
			if ( empty( $result ) ) {
				$result = $json_parse_failure;
			}
		}

		// Try making request to homepage as well to see if visitors have been whitescreened.
		if ( true === $result ) {
			$url                    = home_url( '/' );
			$url                    = add_query_arg( $scrape_params, $url );
			$r                      = wp_remote_get( $url, compact( 'cookies', 'headers', 'timeout', 'sslverify' ) );
			$body                   = wp_remote_retrieve_body( $r );
			$scrape_result_position = strpos( $body, $needle_start );

			if ( false === $scrape_result_position ) {
				$result = $loopback_request_failure;
			} else {
				$error_output = substr( $body, $scrape_result_position + strlen( $needle_start ) );
				$error_output = substr( $error_output, 0, strpos( $error_output, $needle_end ) );
				$result       = json_decode( trim( $error_output ), true );
				if ( empty( $result ) ) {
					$result = $json_parse_failure;
				}
			}
		}

		delete_transient( $transient );

		if ( true !== $result ) {

			// Roll-back file change.
			file_put_contents( $real_file, $previous_content );
			wp_opcache_invalidate( $real_file, true );

			if ( ! isset( $result['message'] ) ) {
				$message = __( 'Something went wrong.' );
			} else {
				$message = $result['message'];
				unset( $result['message'] );
			}
			return new WP_Error( 'php_error', $message, $result );
		}
	}

	if ( $theme instanceof WP_Theme ) {
		$theme->cache_delete();
	}

	return true;
}


/**
 * Returns a filename of a temporary unique file.
 *
 * Please note that the calling function must unlink() this itself.
 *
 * The filename is based off the passed parameter or defaults to the current unix timestamp,
 * while the directory can either be passed as well, or by leaving it blank, default to a writable
 * temporary directory.
 *
 * @since 2.6.0
 *
 * @param string $filename Optional. Filename to base the Unique file off. Default empty.
 * @param string $dir      Optional. Directory to store the file in. Default empty.
 * @return string A writable filename.
 */
function wp_tempnam( $filename = '', $dir = '' ) {
	if ( empty( $dir ) ) {
		$dir = get_temp_dir();
	}

	if ( empty( $filename ) || in_array( $filename, array( '.', '/', '\\' ), true ) ) {
		$filename = uniqid();
	}

	// Use the basename of the given file without the extension as the name for the temporary directory.
	$temp_filename = basename( $filename );
	$temp_filename = preg_replace( '|\.[^.]*$|', '', $temp_filename );

	// If the folder is falsey, use its parent directory name instead.
	if ( ! $temp_filename ) {
		return wp_tempnam( dirname( $filename ), $dir );
	}

	// Suffix some random data to avoid filename conflicts.
	$temp_filename .= '-' . wp_generate_password( 6, false );
	$temp_filename .= '.tmp';
	$temp_filename  = $dir . wp_unique_filename( $dir, $temp_filename );

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
 * Makes sure that the file that was requested to be edited is allowed to be edited.
 *
 * Function will die if you are not allowed to edit the file.
 *
 * @since 1.5.0
 *
 * @param string   $file          File the user is attempting to edit.
 * @param string[] $allowed_files Optional. Array of allowed files to edit.
 *                                `$file` must match an entry exactly.
 * @return string|void Returns the file name on success, dies on failure.
 */
function validate_file_to_edit( $file, $allowed_files = array() ) {
	$code = validate_file( $file, $allowed_files );

	if ( ! $code ) {
		return $file;
	}

	switch ( $code ) {
		case 1:
			wp_die( __( 'Sorry, that file cannot be edited.' ) );

			// case 2 :
			// wp_die( __('Sorry, can&#8217;t call files with their real path.' ));

		case 3:
			wp_die( __( 'Sorry, that file cannot be edited.' ) );
	}
}

/**
 * Handles PHP uploads in WordPress.
 *
 * Sanitizes file names, checks extensions for mime type, and moves the file
 * to the appropriate directory within the uploads directory.
 *
 * @access private
 * @since 4.0.0
 *
 * @see wp_handle_upload_error
 *
 * @param string[]       $file      Reference to a single element of `$_FILES`.
 *                                  Call the function once for each uploaded file.
 * @param string[]|false $overrides An associative array of names => values
 *                                  to override default variables. Default false.
 * @param string         $time      Time formatted in 'yyyy/mm'.
 * @param string         $action    Expected value for `$_POST['action']`.
 * @return string[] On success, returns an associative array of file attributes.
 *                  On failure, returns `$overrides['upload_error_handler']( &$file, $message )`
 *                  or `array( 'error' => $message )`.
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
	 * @param string[] $file An array of data for a single file.
	 */
	$file = apply_filters( "{$action}_prefilter", $file );

	// You may define your own function and pass the name in $overrides['upload_error_handler'].
	$upload_error_handler = 'wp_handle_upload_error';
	if ( isset( $overrides['upload_error_handler'] ) ) {
		$upload_error_handler = $overrides['upload_error_handler'];
	}

	// You may have had one or more 'wp_handle_upload_prefilter' functions error out the file. Handle that gracefully.
	if ( isset( $file['error'] ) && ! is_numeric( $file['error'] ) && $file['error'] ) {
		return call_user_func_array( $upload_error_handler, array( &$file, $file['error'] ) );
	}

	// Install user overrides. Did we mention that this voids your warranty?

	// You may define your own function and pass the name in $overrides['unique_filename_callback'].
	$unique_filename_callback = null;
	if ( isset( $overrides['unique_filename_callback'] ) ) {
		$unique_filename_callback = $overrides['unique_filename_callback'];
	}

	/*
	 * This may not have originally been intended to be overridable,
	 * but historically has been.
	 */
	if ( isset( $overrides['upload_error_strings'] ) ) {
		$upload_error_strings = $overrides['upload_error_strings'];
	} else {
		// Courtesy of php.net, the strings that describe the error indicated in $_FILES[{form field}]['error'].
		$upload_error_strings = array(
			false,
			sprintf(
				/* translators: 1: upload_max_filesize, 2: php.ini */
				__( 'The uploaded file exceeds the %1$s directive in %2$s.' ),
				'upload_max_filesize',
				'php.ini'
			),
			sprintf(
				/* translators: %s: MAX_FILE_SIZE */
				__( 'The uploaded file exceeds the %s directive that was specified in the HTML form.' ),
				'MAX_FILE_SIZE'
			),
			__( 'The uploaded file was only partially uploaded.' ),
			__( 'No file was uploaded.' ),
			'',
			__( 'Missing a temporary folder.' ),
			__( 'Failed to write file to disk.' ),
			__( 'File upload stopped by extension.' ),
		);
	}

	// All tests are on by default. Most can be turned off by $overrides[{test_name}] = false;
	$test_form = isset( $overrides['test_form'] ) ? $overrides['test_form'] : true;
	$test_size = isset( $overrides['test_size'] ) ? $overrides['test_size'] : true;

	// If you override this, you must provide $ext and $type!!
	$test_type = isset( $overrides['test_type'] ) ? $overrides['test_type'] : true;
	$mimes     = isset( $overrides['mimes'] ) ? $overrides['mimes'] : false;

	// A correct form post will pass this test.
	if ( $test_form && ( ! isset( $_POST['action'] ) || ( $_POST['action'] != $action ) ) ) {
		return call_user_func_array( $upload_error_handler, array( &$file, __( 'Invalid form submission.' ) ) );
	}
	// A successful upload will pass this test. It makes no sense to override this one.
	if ( isset( $file['error'] ) && $file['error'] > 0 ) {
		return call_user_func_array( $upload_error_handler, array( &$file, $upload_error_strings[ $file['error'] ] ) );
	}

	// A properly uploaded file will pass this test. There should be no reason to override this one.
	$test_uploaded_file = 'wp_handle_upload' === $action ? is_uploaded_file( $file['tmp_name'] ) : @is_readable( $file['tmp_name'] );
	if ( ! $test_uploaded_file ) {
		return call_user_func_array( $upload_error_handler, array( &$file, __( 'Specified file failed upload test.' ) ) );
	}

	$test_file_size = 'wp_handle_upload' === $action ? $file['size'] : filesize( $file['tmp_name'] );
	// A non-empty file will pass this test.
	if ( $test_size && ! ( $test_file_size > 0 ) ) {
		if ( is_multisite() ) {
			$error_msg = __( 'File is empty. Please upload something more substantial.' );
		} else {
			$error_msg = sprintf(
				/* translators: 1: php.ini, 2: post_max_size, 3: upload_max_filesize */
				__( 'File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your %1$s file or by %2$s being defined as smaller than %3$s in %1$s.' ),
				'php.ini',
				'post_max_size',
				'upload_max_filesize'
			);
		}
		return call_user_func_array( $upload_error_handler, array( &$file, $error_msg ) );
	}

	// A correct MIME type will pass this test. Override $mimes or use the upload_mimes filter.
	if ( $test_type ) {
		$wp_filetype     = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], $mimes );
		$ext             = empty( $wp_filetype['ext'] ) ? '' : $wp_filetype['ext'];
		$type            = empty( $wp_filetype['type'] ) ? '' : $wp_filetype['type'];
		$proper_filename = empty( $wp_filetype['proper_filename'] ) ? '' : $wp_filetype['proper_filename'];

		// Check to see if wp_check_filetype_and_ext() determined the filename was incorrect.
		if ( $proper_filename ) {
			$file['name'] = $proper_filename;
		}
		if ( ( ! $type || ! $ext ) && ! current_user_can( 'unfiltered_upload' ) ) {
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
	$uploads = wp_upload_dir( $time );
	if ( ! ( $uploads && false === $uploads['error'] ) ) {
		return call_user_func_array( $upload_error_handler, array( &$file, $uploads['error'] ) );
	}

	$filename = wp_unique_filename( $uploads['path'], $file['name'], $unique_filename_callback );

	// Move the file to the uploads dir.
	$new_file = $uploads['path'] . "/$filename";

	/**
	 * Filters whether to short-circuit moving the uploaded file after passing all checks.
	 *
	 * If a non-null value is returned from the filter, moving the file and any related
	 * error reporting will be completely skipped.
	 *
	 * @since 4.9.0
	 *
	 * @param mixed    $move_new_file If null (default) move the file after the upload.
	 * @param string[] $file          An array of data for a single file.
	 * @param string   $new_file      Filename of the newly-uploaded file.
	 * @param string   $type          Mime type of the newly-uploaded file.
	 */
	$move_new_file = apply_filters( 'pre_move_uploaded_file', null, $file, $new_file, $type );

	if ( null === $move_new_file ) {
		if ( 'wp_handle_upload' === $action ) {
			$move_new_file = @move_uploaded_file( $file['tmp_name'], $new_file );
		} else {
			// Use copy and unlink because rename breaks streams.
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			$move_new_file = @copy( $file['tmp_name'], $new_file );
			unlink( $file['tmp_name'] );
		}

		if ( false === $move_new_file ) {
			if ( 0 === strpos( $uploads['basedir'], ABSPATH ) ) {
				$error_path = str_replace( ABSPATH, '', $uploads['basedir'] ) . $uploads['subdir'];
			} else {
				$error_path = basename( $uploads['basedir'] ) . $uploads['subdir'];
			}
			return $upload_error_handler(
				$file,
				sprintf(
					/* translators: %s: Destination file path. */
					__( 'The uploaded file could not be moved to %s.' ),
					$error_path
				)
			);
		}
	}

	// Set correct file permissions.
	$stat  = stat( dirname( $new_file ) );
	$perms = $stat['mode'] & 0000666;
	chmod( $new_file, $perms );

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
	 *     @type string $url  URL of the newly-uploaded file.
	 *     @type string $type Mime type of the newly-uploaded file.
	 * }
	 * @param string $context The type of upload action. Values include 'upload' or 'sideload'.
	 */
	return apply_filters(
		'wp_handle_upload',
		array(
			'file' => $new_file,
			'url'  => $url,
			'type' => $type,
		),
		'wp_handle_sideload' === $action ? 'sideload' : 'upload'
	);
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
 * @param array      $file      Reference to a single element of `$_FILES`.
 *                              Call the function once for each uploaded file.
 * @param array|bool $overrides Optional. An associative array of names => values
 *                              to override default variables. Default false.
 * @param string     $time      Optional. Time formatted in 'yyyy/mm'. Default null.
 * @return array On success, returns an associative array of file attributes.
 *               On failure, returns `$overrides['upload_error_handler']( &$file, $message )`
 *               or `array( 'error' => $message )`.
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
 * @param array      $file      Reference to a single element of `$_FILES`.
 *                              Call the function once for each uploaded file.
 * @param array|bool $overrides Optional. An associative array of names => values
 *                              to override default variables. Default false.
 * @param string     $time      Optional. Time formatted in 'yyyy/mm'. Default null.
 * @return array On success, returns an associative array of file attributes.
 *               On failure, returns `$overrides['upload_error_handler']( &$file, $message )`
 *               or `array( 'error' => $message )`.
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
 * Downloads a URL to a local temporary file using the WordPress HTTP API.
 *
 * Please note that the calling function must unlink() the file.
 *
 * @since 2.5.0
 * @since 5.2.0 Signature Verification with SoftFail was added.
 *
 * @param string $url                    The URL of the file to download.
 * @param int    $timeout                The timeout for the request to download the file.
 *                                       Default 300 seconds.
 * @param bool   $signature_verification Whether to perform Signature Verification.
 *                                       Default false.
 * @return string|WP_Error Filename on success, WP_Error on failure.
 */
function download_url( $url, $timeout = 300, $signature_verification = false ) {
	// WARNING: The file is not automatically deleted, the script must unlink() the file.
	if ( ! $url ) {
		return new WP_Error( 'http_no_url', __( 'Invalid URL Provided.' ) );
	}

	$url_filename = basename( parse_url( $url, PHP_URL_PATH ) );

	$tmpfname = wp_tempnam( $url_filename );
	if ( ! $tmpfname ) {
		return new WP_Error( 'http_no_file', __( 'Could not create Temporary file.' ) );
	}

	$response = wp_safe_remote_get(
		$url,
		array(
			'timeout'  => $timeout,
			'stream'   => true,
			'filename' => $tmpfname,
		)
	);

	if ( is_wp_error( $response ) ) {
		unlink( $tmpfname );
		return $response;
	}

	$response_code = wp_remote_retrieve_response_code( $response );

	if ( 200 != $response_code ) {
		$data = array(
			'code' => $response_code,
		);

		// Retrieve a sample of the response body for debugging purposes.
		$tmpf = fopen( $tmpfname, 'rb' );
		if ( $tmpf ) {
			/**
			 * Filters the maximum error response body size in `download_url()`.
			 *
			 * @since 5.1.0
			 *
			 * @see download_url()
			 *
			 * @param int $size The maximum error response body size. Default 1 KB.
			 */
			$response_size = apply_filters( 'download_url_error_max_body_size', KB_IN_BYTES );
			$data['body']  = fread( $tmpf, $response_size );
			fclose( $tmpf );
		}

		unlink( $tmpfname );
		return new WP_Error( 'http_404', trim( wp_remote_retrieve_response_message( $response ) ), $data );
	}

	$content_md5 = wp_remote_retrieve_header( $response, 'content-md5' );
	if ( $content_md5 ) {
		$md5_check = verify_file_md5( $tmpfname, $content_md5 );
		if ( is_wp_error( $md5_check ) ) {
			unlink( $tmpfname );
			return $md5_check;
		}
	}

	// If the caller expects signature verification to occur, check to see if this URL supports it.
	if ( $signature_verification ) {
		/**
		 * Filters the list of hosts which should have Signature Verification attempted on.
		 *
		 * @since 5.2.0
		 *
		 * @param string[] $hostnames List of hostnames.
		 */
		$signed_hostnames       = apply_filters( 'wp_signature_hosts', array( 'wordpress.org', 'downloads.wordpress.org', 's.w.org' ) );
		$signature_verification = in_array( parse_url( $url, PHP_URL_HOST ), $signed_hostnames, true );
	}

	// Perform signature valiation if supported.
	if ( $signature_verification ) {
		$signature = wp_remote_retrieve_header( $response, 'x-content-signature' );
		if ( ! $signature ) {
			// Retrieve signatures from a file if the header wasn't included.
			// WordPress.org stores signatures at $package_url.sig.

			$signature_url = false;
			$url_path      = parse_url( $url, PHP_URL_PATH );

			if ( '.zip' === substr( $url_path, -4 ) || '.tar.gz' === substr( $url_path, -7 ) ) {
				$signature_url = str_replace( $url_path, $url_path . '.sig', $url );
			}

			/**
			 * Filter the URL where the signature for a file is located.
			 *
			 * @since 5.2.0
			 *
			 * @param false|string $signature_url The URL where signatures can be found for a file, or false if none are known.
			 * @param string $url                 The URL being verified.
			 */
			$signature_url = apply_filters( 'wp_signature_url', $signature_url, $url );

			if ( $signature_url ) {
				$signature_request = wp_safe_remote_get(
					$signature_url,
					array(
						'limit_response_size' => 10 * KB_IN_BYTES, // 10KB should be large enough for quite a few signatures.
					)
				);

				if ( ! is_wp_error( $signature_request ) && 200 === wp_remote_retrieve_response_code( $signature_request ) ) {
					$signature = explode( "\n", wp_remote_retrieve_body( $signature_request ) );
				}
			}
		}

		// Perform the checks.
		$signature_verification = verify_file_signature( $tmpfname, $signature, basename( parse_url( $url, PHP_URL_PATH ) ) );
	}

	if ( is_wp_error( $signature_verification ) ) {
		if (
			/**
			 * Filters whether Signature Verification failures should be allowed to soft fail.
			 *
			 * WARNING: This may be removed from a future release.
			 *
			 * @since 5.2.0
			 *
			 * @param bool   $signature_softfail If a softfail is allowed.
			 * @param string $url                The url being accessed.
			 */
			apply_filters( 'wp_signature_softfail', true, $url )
		) {
			$signature_verification->add_data( $tmpfname, 'softfail-filename' );
		} else {
			// Hard-fail.
			unlink( $tmpfname );
		}

		return $signature_verification;
	}

	return $tmpfname;
}

/**
 * Calculates and compares the MD5 of a file to its expected value.
 *
 * @since 3.7.0
 *
 * @param string $filename     The filename to check the MD5 of.
 * @param string $expected_md5 The expected MD5 of the file, either a base64-encoded raw md5,
 *                             or a hex-encoded md5.
 * @return bool|WP_Error True on success, false when the MD5 format is unknown/unexpected,
 *                       WP_Error on failure.
 */
function verify_file_md5( $filename, $expected_md5 ) {
	if ( 32 == strlen( $expected_md5 ) ) {
		$expected_raw_md5 = pack( 'H*', $expected_md5 );
	} elseif ( 24 == strlen( $expected_md5 ) ) {
		$expected_raw_md5 = base64_decode( $expected_md5 );
	} else {
		return false; // Unknown format.
	}

	$file_md5 = md5_file( $filename, true );

	if ( $file_md5 === $expected_raw_md5 ) {
		return true;
	}

	return new WP_Error(
		'md5_mismatch',
		sprintf(
			/* translators: 1: File checksum, 2: Expected checksum value. */
			__( 'The checksum of the file (%1$s) does not match the expected checksum value (%2$s).' ),
			bin2hex( $file_md5 ),
			bin2hex( $expected_raw_md5 )
		)
	);
}

/**
 * Verifies the contents of a file against its ED25519 signature.
 *
 * @since 5.2.0
 *
 * @param string       $filename            The file to validate.
 * @param string|array $signatures          A Signature provided for the file.
 * @param string       $filename_for_errors A friendly filename for errors. Optional.
 * @return bool|WP_Error True on success, false if verification not attempted,
 *                       or WP_Error describing an error condition.
 */
function verify_file_signature( $filename, $signatures, $filename_for_errors = false ) {
	if ( ! $filename_for_errors ) {
		$filename_for_errors = wp_basename( $filename );
	}

	// Check we can process signatures.
	if ( ! function_exists( 'sodium_crypto_sign_verify_detached' ) || ! in_array( 'sha384', array_map( 'strtolower', hash_algos() ), true ) ) {
		return new WP_Error(
			'signature_verification_unsupported',
			sprintf(
				/* translators: %s: The filename of the package. */
				__( 'The authenticity of %s could not be verified as signature verification is unavailable on this system.' ),
				'<span class="code">' . esc_html( $filename_for_errors ) . '</span>'
			),
			( ! function_exists( 'sodium_crypto_sign_verify_detached' ) ? 'sodium_crypto_sign_verify_detached' : 'sha384' )
		);
	}

	// Check for a edge-case affecting PHP Maths abilities.
	if (
		! extension_loaded( 'sodium' ) &&
		in_array( PHP_VERSION_ID, array( 70200, 70201, 70202 ), true ) &&
		extension_loaded( 'opcache' )
	) {
		// Sodium_Compat isn't compatible with PHP 7.2.0~7.2.2 due to a bug in the PHP Opcache extension, bail early as it'll fail.
		// https://bugs.php.net/bug.php?id=75938

		return new WP_Error(
			'signature_verification_unsupported',
			sprintf(
				/* translators: %s: The filename of the package. */
				__( 'The authenticity of %s could not be verified as signature verification is unavailable on this system.' ),
				'<span class="code">' . esc_html( $filename_for_errors ) . '</span>'
			),
			array(
				'php'    => phpversion(),
				// phpcs:ignore PHPCompatibility.Constants.NewConstants.sodium_library_versionFound
				'sodium' => defined( 'SODIUM_LIBRARY_VERSION' ) ? SODIUM_LIBRARY_VERSION : ( defined( 'ParagonIE_Sodium_Compat::VERSION_STRING' ) ? ParagonIE_Sodium_Compat::VERSION_STRING : false ),
			)
		);

	}

	// Verify runtime speed of Sodium_Compat is acceptable.
	if ( ! extension_loaded( 'sodium' ) && ! ParagonIE_Sodium_Compat::polyfill_is_fast() ) {
		$sodium_compat_is_fast = false;

		// Allow for an old version of Sodium_Compat being loaded before the bundled WordPress one.
		if ( method_exists( 'ParagonIE_Sodium_Compat', 'runtime_speed_test' ) ) {
			// Run `ParagonIE_Sodium_Compat::runtime_speed_test()` in optimized integer mode, as that's what WordPress utilises during signing verifications.
			// phpcs:disable WordPress.NamingConventions.ValidVariableName
			$old_fastMult                      = ParagonIE_Sodium_Compat::$fastMult;
			ParagonIE_Sodium_Compat::$fastMult = true;
			$sodium_compat_is_fast             = ParagonIE_Sodium_Compat::runtime_speed_test( 100, 10 );
			ParagonIE_Sodium_Compat::$fastMult = $old_fastMult;
			// phpcs:enable
		}

		// This cannot be performed in a reasonable amount of time.
		// https://github.com/paragonie/sodium_compat#help-sodium_compat-is-slow-how-can-i-make-it-fast
		if ( ! $sodium_compat_is_fast ) {
			return new WP_Error(
				'signature_verification_unsupported',
				sprintf(
					/* translators: %s: The filename of the package. */
					__( 'The authenticity of %s could not be verified as signature verification is unavailable on this system.' ),
					'<span class="code">' . esc_html( $filename_for_errors ) . '</span>'
				),
				array(
					'php'                => phpversion(),
					// phpcs:ignore PHPCompatibility.Constants.NewConstants.sodium_library_versionFound
					'sodium'             => defined( 'SODIUM_LIBRARY_VERSION' ) ? SODIUM_LIBRARY_VERSION : ( defined( 'ParagonIE_Sodium_Compat::VERSION_STRING' ) ? ParagonIE_Sodium_Compat::VERSION_STRING : false ),
					'polyfill_is_fast'   => false,
					'max_execution_time' => ini_get( 'max_execution_time' ),
				)
			);
		}
	}

	if ( ! $signatures ) {
		return new WP_Error(
			'signature_verification_no_signature',
			sprintf(
				/* translators: %s: The filename of the package. */
				__( 'The authenticity of %s could not be verified as no signature was found.' ),
				'<span class="code">' . esc_html( $filename_for_errors ) . '</span>'
			),
			array(
				'filename' => $filename_for_errors,
			)
		);
	}

	$trusted_keys = wp_trusted_keys();
	$file_hash    = hash_file( 'sha384', $filename, true );

	mbstring_binary_safe_encoding();

	$skipped_key       = 0;
	$skipped_signature = 0;

	foreach ( (array) $signatures as $signature ) {
		$signature_raw = base64_decode( $signature );

		// Ensure only valid-length signatures are considered.
		if ( SODIUM_CRYPTO_SIGN_BYTES !== strlen( $signature_raw ) ) {
			$skipped_signature++;
			continue;
		}

		foreach ( (array) $trusted_keys as $key ) {
			$key_raw = base64_decode( $key );

			// Only pass valid public keys through.
			if ( SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES !== strlen( $key_raw ) ) {
				$skipped_key++;
				continue;
			}

			if ( sodium_crypto_sign_verify_detached( $signature_raw, $file_hash, $key_raw ) ) {
				reset_mbstring_encoding();
				return true;
			}
		}
	}

	reset_mbstring_encoding();

	return new WP_Error(
		'signature_verification_failed',
		sprintf(
			/* translators: %s: The filename of the package. */
			__( 'The authenticity of %s could not be verified.' ),
			'<span class="code">' . esc_html( $filename_for_errors ) . '</span>'
		),
		// Error data helpful for debugging:
		array(
			'filename'    => $filename_for_errors,
			'keys'        => $trusted_keys,
			'signatures'  => $signatures,
			'hash'        => bin2hex( $file_hash ),
			'skipped_key' => $skipped_key,
			'skipped_sig' => $skipped_signature,
			'php'         => phpversion(),
			// phpcs:ignore PHPCompatibility.Constants.NewConstants.sodium_library_versionFound
			'sodium'      => defined( 'SODIUM_LIBRARY_VERSION' ) ? SODIUM_LIBRARY_VERSION : ( defined( 'ParagonIE_Sodium_Compat::VERSION_STRING' ) ? ParagonIE_Sodium_Compat::VERSION_STRING : false ),
		)
	);
}

/**
 * Retrieves the list of signing keys trusted by WordPress.
 *
 * @since 5.2.0
 *
 * @return string[] Array of base64-encoded signing keys.
 */
function wp_trusted_keys() {
	$trusted_keys = array();

	if ( time() < 1617235200 ) {
		// WordPress.org Key #1 - This key is only valid before April 1st, 2021.
		$trusted_keys[] = 'fRPyrxb/MvVLbdsYi+OOEv4xc+Eqpsj+kkAS6gNOkI0=';
	}

	// TODO: Add key #2 with longer expiration.

	/**
	 * Filter the valid signing keys used to verify the contents of files.
	 *
	 * @since 5.2.0
	 *
	 * @param string[] $trusted_keys The trusted keys that may sign packages.
	 */
	return apply_filters( 'wp_trusted_keys', $trusted_keys );
}

/**
 * Unzips a specified ZIP file to a location on the filesystem via the WordPress
 * Filesystem Abstraction.
 *
 * Assumes that WP_Filesystem() has already been called and set up. Does not extract
 * a root-level __MACOSX directory, if present.
 *
 * Attempts to increase the PHP memory limit to 256M before uncompressing. However,
 * the most memory required shouldn't be much larger than the archive itself.
 *
 * @since 2.5.0
 *
 * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
 *
 * @param string $file Full path and filename of ZIP archive.
 * @param string $to   Full path on the filesystem to extract archive to.
 * @return true|WP_Error True on success, WP_Error on failure.
 */
function unzip_file( $file, $to ) {
	global $wp_filesystem;

	if ( ! $wp_filesystem || ! is_object( $wp_filesystem ) ) {
		return new WP_Error( 'fs_unavailable', __( 'Could not access filesystem.' ) );
	}

	// Unzip can use a lot of memory, but not this much hopefully.
	wp_raise_memory_limit( 'admin' );

	$needed_dirs = array();
	$to          = trailingslashit( $to );

	// Determine any parent directories needed (of the upgrade directory).
	if ( ! $wp_filesystem->is_dir( $to ) ) { // Only do parents if no children exist.
		$path = preg_split( '![/\\\]!', untrailingslashit( $to ) );
		for ( $i = count( $path ); $i >= 0; $i-- ) {
			if ( empty( $path[ $i ] ) ) {
				continue;
			}

			$dir = implode( '/', array_slice( $path, 0, $i + 1 ) );
			if ( preg_match( '!^[a-z]:$!i', $dir ) ) { // Skip it if it looks like a Windows Drive letter.
				continue;
			}

			if ( ! $wp_filesystem->is_dir( $dir ) ) {
				$needed_dirs[] = $dir;
			} else {
				break; // A folder exists, therefore we don't need to check the levels below this.
			}
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
		$result = _unzip_file_ziparchive( $file, $to, $needed_dirs );
		if ( true === $result ) {
			return $result;
		} elseif ( is_wp_error( $result ) ) {
			if ( 'incompatible_archive' !== $result->get_error_code() ) {
				return $result;
			}
		}
	}
	// Fall through to PclZip if ZipArchive is not available, or encountered an error opening the file.
	return _unzip_file_pclzip( $file, $to, $needed_dirs );
}

/**
 * Attempts to unzip an archive using the ZipArchive class.
 *
 * This function should not be called directly, use `unzip_file()` instead.
 *
 * Assumes that WP_Filesystem() has already been called and set up.
 *
 * @since 3.0.0
 * @access private
 *
 * @see unzip_file()
 *
 * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
 *
 * @param string   $file        Full path and filename of ZIP archive.
 * @param string   $to          Full path on the filesystem to extract archive to.
 * @param string[] $needed_dirs A partial list of required folders needed to be created.
 * @return true|WP_Error True on success, WP_Error on failure.
 */
function _unzip_file_ziparchive( $file, $to, $needed_dirs = array() ) {
	global $wp_filesystem;

	$z = new ZipArchive();

	$zopen = $z->open( $file, ZIPARCHIVE::CHECKCONS );
	if ( true !== $zopen ) {
		return new WP_Error( 'incompatible_archive', __( 'Incompatible Archive.' ), array( 'ziparchive_error' => $zopen ) );
	}

	$uncompressed_size = 0;

	for ( $i = 0; $i < $z->numFiles; $i++ ) {
		$info = $z->statIndex( $i );
		if ( ! $info ) {
			return new WP_Error( 'stat_failed_ziparchive', __( 'Could not retrieve file from archive.' ) );
		}

		if ( '__MACOSX/' === substr( $info['name'], 0, 9 ) ) { // Skip the OS X-created __MACOSX directory.
			continue;
		}

		// Don't extract invalid files:
		if ( 0 !== validate_file( $info['name'] ) ) {
			continue;
		}

		$uncompressed_size += $info['size'];

		$dirname = dirname( $info['name'] );

		if ( '/' === substr( $info['name'], -1 ) ) {
			// Directory.
			$needed_dirs[] = $to . untrailingslashit( $info['name'] );
		} elseif ( '.' !== $dirname ) {
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
		if ( $available_space && ( $uncompressed_size * 2.1 ) > $available_space ) {
			return new WP_Error( 'disk_full_unzip_file', __( 'Could not copy files. You may have run out of disk space.' ), compact( 'uncompressed_size', 'available_space' ) );
		}
	}

	$needed_dirs = array_unique( $needed_dirs );
	foreach ( $needed_dirs as $dir ) {
		// Check the parent folders of the folders all exist within the creation array.
		if ( untrailingslashit( $to ) == $dir ) { // Skip over the working directory, we know this exists (or will exist).
			continue;
		}
		if ( strpos( $dir, $to ) === false ) { // If the directory is not within the working directory, skip it.
			continue;
		}

		$parent_folder = dirname( $dir );
		while ( ! empty( $parent_folder ) && untrailingslashit( $to ) != $parent_folder && ! in_array( $parent_folder, $needed_dirs, true ) ) {
			$needed_dirs[] = $parent_folder;
			$parent_folder = dirname( $parent_folder );
		}
	}
	asort( $needed_dirs );

	// Create those directories if need be:
	foreach ( $needed_dirs as $_dir ) {
		// Only check to see if the Dir exists upon creation failure. Less I/O this way.
		if ( ! $wp_filesystem->mkdir( $_dir, FS_CHMOD_DIR ) && ! $wp_filesystem->is_dir( $_dir ) ) {
			return new WP_Error( 'mkdir_failed_ziparchive', __( 'Could not create directory.' ), substr( $_dir, strlen( $to ) ) );
		}
	}
	unset( $needed_dirs );

	for ( $i = 0; $i < $z->numFiles; $i++ ) {
		$info = $z->statIndex( $i );
		if ( ! $info ) {
			return new WP_Error( 'stat_failed_ziparchive', __( 'Could not retrieve file from archive.' ) );
		}

		if ( '/' === substr( $info['name'], -1 ) ) { // Directory.
			continue;
		}

		if ( '__MACOSX/' === substr( $info['name'], 0, 9 ) ) { // Don't extract the OS X-created __MACOSX directory files.
			continue;
		}

		// Don't extract invalid files:
		if ( 0 !== validate_file( $info['name'] ) ) {
			continue;
		}

		$contents = $z->getFromIndex( $i );
		if ( false === $contents ) {
			return new WP_Error( 'extract_failed_ziparchive', __( 'Could not extract file from archive.' ), $info['name'] );
		}

		if ( ! $wp_filesystem->put_contents( $to . $info['name'], $contents, FS_CHMOD_FILE ) ) {
			return new WP_Error( 'copy_failed_ziparchive', __( 'Could not copy file.' ), $info['name'] );
		}
	}

	$z->close();

	return true;
}

/**
 * Attempts to unzip an archive using the PclZip library.
 *
 * This function should not be called directly, use `unzip_file()` instead.
 *
 * Assumes that WP_Filesystem() has already been called and set up.
 *
 * @since 3.0.0
 * @access private
 *
 * @see unzip_file()
 *
 * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
 *
 * @param string   $file        Full path and filename of ZIP archive.
 * @param string   $to          Full path on the filesystem to extract archive to.
 * @param string[] $needed_dirs A partial list of required folders needed to be created.
 * @return true|WP_Error True on success, WP_Error on failure.
 */
function _unzip_file_pclzip( $file, $to, $needed_dirs = array() ) {
	global $wp_filesystem;

	mbstring_binary_safe_encoding();

	require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';

	$archive = new PclZip( $file );

	$archive_files = $archive->extract( PCLZIP_OPT_EXTRACT_AS_STRING );

	reset_mbstring_encoding();

	// Is the archive valid?
	if ( ! is_array( $archive_files ) ) {
		return new WP_Error( 'incompatible_archive', __( 'Incompatible Archive.' ), $archive->errorInfo( true ) );
	}

	if ( 0 === count( $archive_files ) ) {
		return new WP_Error( 'empty_archive_pclzip', __( 'Empty archive.' ) );
	}

	$uncompressed_size = 0;

	// Determine any children directories needed (From within the archive).
	foreach ( $archive_files as $file ) {
		if ( '__MACOSX/' === substr( $file['filename'], 0, 9 ) ) { // Skip the OS X-created __MACOSX directory.
			continue;
		}

		$uncompressed_size += $file['size'];

		$needed_dirs[] = $to . untrailingslashit( $file['folder'] ? $file['filename'] : dirname( $file['filename'] ) );
	}

	/*
	 * disk_free_space() could return false. Assume that any falsey value is an error.
	 * A disk that has zero free bytes has bigger problems.
	 * Require we have enough space to unzip the file and copy its contents, with a 10% buffer.
	 */
	if ( wp_doing_cron() ) {
		$available_space = @disk_free_space( WP_CONTENT_DIR );
		if ( $available_space && ( $uncompressed_size * 2.1 ) > $available_space ) {
			return new WP_Error( 'disk_full_unzip_file', __( 'Could not copy files. You may have run out of disk space.' ), compact( 'uncompressed_size', 'available_space' ) );
		}
	}

	$needed_dirs = array_unique( $needed_dirs );
	foreach ( $needed_dirs as $dir ) {
		// Check the parent folders of the folders all exist within the creation array.
		if ( untrailingslashit( $to ) == $dir ) { // Skip over the working directory, we know this exists (or will exist).
			continue;
		}
		if ( strpos( $dir, $to ) === false ) { // If the directory is not within the working directory, skip it.
			continue;
		}

		$parent_folder = dirname( $dir );
		while ( ! empty( $parent_folder ) && untrailingslashit( $to ) != $parent_folder && ! in_array( $parent_folder, $needed_dirs, true ) ) {
			$needed_dirs[] = $parent_folder;
			$parent_folder = dirname( $parent_folder );
		}
	}
	asort( $needed_dirs );

	// Create those directories if need be:
	foreach ( $needed_dirs as $_dir ) {
		// Only check to see if the dir exists upon creation failure. Less I/O this way.
		if ( ! $wp_filesystem->mkdir( $_dir, FS_CHMOD_DIR ) && ! $wp_filesystem->is_dir( $_dir ) ) {
			return new WP_Error( 'mkdir_failed_pclzip', __( 'Could not create directory.' ), substr( $_dir, strlen( $to ) ) );
		}
	}
	unset( $needed_dirs );

	// Extract the files from the zip.
	foreach ( $archive_files as $file ) {
		if ( $file['folder'] ) {
			continue;
		}

		if ( '__MACOSX/' === substr( $file['filename'], 0, 9 ) ) { // Don't extract the OS X-created __MACOSX directory files.
			continue;
		}

		// Don't extract invalid files:
		if ( 0 !== validate_file( $file['filename'] ) ) {
			continue;
		}

		if ( ! $wp_filesystem->put_contents( $to . $file['filename'], $file['content'], FS_CHMOD_FILE ) ) {
			return new WP_Error( 'copy_failed_pclzip', __( 'Could not copy file.' ), $file['filename'] );
		}
	}
	return true;
}

/**
 * Copies a directory from one location to another via the WordPress Filesystem
 * Abstraction.
 *
 * Assumes that WP_Filesystem() has already been called and setup.
 *
 * @since 2.5.0
 *
 * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
 *
 * @param string   $from      Source directory.
 * @param string   $to        Destination directory.
 * @param string[] $skip_list An array of files/folders to skip copying.
 * @return true|WP_Error True on success, WP_Error on failure.
 */
function copy_dir( $from, $to, $skip_list = array() ) {
	global $wp_filesystem;

	$dirlist = $wp_filesystem->dirlist( $from );

	$from = trailingslashit( $from );
	$to   = trailingslashit( $to );

	foreach ( (array) $dirlist as $filename => $fileinfo ) {
		if ( in_array( $filename, $skip_list, true ) ) {
			continue;
		}

		if ( 'f' === $fileinfo['type'] ) {
			if ( ! $wp_filesystem->copy( $from . $filename, $to . $filename, true, FS_CHMOD_FILE ) ) {
				// If copy failed, chmod file to 0644 and try again.
				$wp_filesystem->chmod( $to . $filename, FS_CHMOD_FILE );
				if ( ! $wp_filesystem->copy( $from . $filename, $to . $filename, true, FS_CHMOD_FILE ) ) {
					return new WP_Error( 'copy_failed_copy_dir', __( 'Could not copy file.' ), $to . $filename );
				}
			}

			wp_opcache_invalidate( $to . $filename );
		} elseif ( 'd' === $fileinfo['type'] ) {
			if ( ! $wp_filesystem->is_dir( $to . $filename ) ) {
				if ( ! $wp_filesystem->mkdir( $to . $filename, FS_CHMOD_DIR ) ) {
					return new WP_Error( 'mkdir_failed_copy_dir', __( 'Could not create directory.' ), $to . $filename );
				}
			}

			// Generate the $sub_skip_list for the subdirectory as a sub-set of the existing $skip_list.
			$sub_skip_list = array();
			foreach ( $skip_list as $skip_item ) {
				if ( 0 === strpos( $skip_item, $filename . '/' ) ) {
					$sub_skip_list[] = preg_replace( '!^' . preg_quote( $filename, '!' ) . '/!i', '', $skip_item );
				}
			}

			$result = copy_dir( $from . $filename, $to . $filename, $sub_skip_list );
			if ( is_wp_error( $result ) ) {
				return $result;
			}
		}
	}

	return true;
}

/**
 * Initializes and connects the WordPress Filesystem Abstraction classes.
 *
 * This function will include the chosen transport and attempt connecting.
 *
 * Plugins may add extra transports, And force WordPress to use them by returning
 * the filename via the {@see 'filesystem_method_file'} filter.
 *
 * @since 2.5.0
 *
 * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
 *
 * @param array|false  $args                         Optional. Connection args, These are passed
 *                                                   directly to the `WP_Filesystem_*()` classes.
 *                                                   Default false.
 * @param string|false $context                      Optional. Context for get_filesystem_method().
 *                                                   Default false.
 * @param bool         $allow_relaxed_file_ownership Optional. Whether to allow Group/World writable.
 *                                                   Default false.
 * @return bool|null True on success, false on failure,
 *                   null if the filesystem method class file does not exist.
 */
function WP_Filesystem( $args = false, $context = false, $allow_relaxed_file_ownership = false ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	global $wp_filesystem;

	require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';

	$method = get_filesystem_method( $args, $context, $allow_relaxed_file_ownership );

	if ( ! $method ) {
		return false;
	}

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

		if ( ! file_exists( $abstraction_file ) ) {
			return;
		}

		require_once $abstraction_file;
	}
	$method = "WP_Filesystem_$method";

	$wp_filesystem = new $method( $args );

	/*
	 * Define the timeouts for the connections. Only available after the constructor is called
	 * to allow for per-transport overriding of the default.
	 */
	if ( ! defined( 'FS_CONNECT_TIMEOUT' ) ) {
		define( 'FS_CONNECT_TIMEOUT', 30 );
	}
	if ( ! defined( 'FS_TIMEOUT' ) ) {
		define( 'FS_TIMEOUT', 30 );
	}

	if ( is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->has_errors() ) {
		return false;
	}

	if ( ! $wp_filesystem->connect() ) {
		return false; // There was an error connecting to the server.
	}

	// Set the permission constants if not already set.
	if ( ! defined( 'FS_CHMOD_DIR' ) ) {
		define( 'FS_CHMOD_DIR', ( fileperms( ABSPATH ) & 0777 | 0755 ) );
	}
	if ( ! defined( 'FS_CHMOD_FILE' ) ) {
		define( 'FS_CHMOD_FILE', ( fileperms( ABSPATH . 'index.php' ) & 0777 | 0644 ) );
	}

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
 * @link https://wordpress.org/support/article/editing-wp-config-php/#wordpress-upgrade-constants
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
	// Please ensure that this is either 'direct', 'ssh2', 'ftpext', or 'ftpsockets'.
	$method = defined( 'FS_METHOD' ) ? FS_METHOD : false;

	if ( ! $context ) {
		$context = WP_CONTENT_DIR;
	}

	// If the directory doesn't exist (wp-content/languages) then use the parent directory as we'll create it.
	if ( WP_LANG_DIR == $context && ! is_dir( $context ) ) {
		$context = dirname( $context );
	}

	$context = trailingslashit( $context );

	if ( ! $method ) {

		$temp_file_name = $context . 'temp-write-test-' . str_replace( '.', '-', uniqid( '', true ) );
		$temp_handle    = @fopen( $temp_file_name, 'w' );
		if ( $temp_handle ) {

			// Attempt to determine the file owner of the WordPress files, and that of newly created files.
			$wp_file_owner   = false;
			$temp_file_owner = false;
			if ( function_exists( 'fileowner' ) ) {
				$wp_file_owner   = @fileowner( __FILE__ );
				$temp_file_owner = @fileowner( $temp_file_name );
			}

			if ( false !== $wp_file_owner && $wp_file_owner === $temp_file_owner ) {
				/*
				 * WordPress is creating files as the same owner as the WordPress files,
				 * this means it's safe to modify & create new files via PHP.
				 */
				$method                                  = 'direct';
				$GLOBALS['_wp_filesystem_direct_method'] = 'file_owner';
			} elseif ( $allow_relaxed_file_ownership ) {
				/*
				 * The $context directory is writable, and $allow_relaxed_file_ownership is set,
				 * this means we can modify files safely in this directory.
				 * This mode doesn't create new files, only alter existing ones.
				 */
				$method                                  = 'direct';
				$GLOBALS['_wp_filesystem_direct_method'] = 'relaxed_ownership';
			}

			fclose( $temp_handle );
			@unlink( $temp_file_name );
		}
	}

	if ( ! $method && isset( $args['connection_type'] ) && 'ssh' === $args['connection_type'] && extension_loaded( 'ssh2' ) ) {
		$method = 'ssh2';
	}
	if ( ! $method && extension_loaded( 'ftp' ) ) {
		$method = 'ftpext';
	}
	if ( ! $method && ( extension_loaded( 'sockets' ) || function_exists( 'fsockopen' ) ) ) {
		$method = 'ftpsockets'; // Sockets: Socket extension; PHP Mode: FSockopen / fwrite / fread.
	}

	/**
	 * Filters the filesystem method to use.
	 *
	 * @since 2.6.0
	 *
	 * @param string $method                       Filesystem method to return.
	 * @param array  $args                         An array of connection details for the method.
	 * @param string $context                      Full path to the directory that is tested for being writable.
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
 * @param string        $form_post                    The URL to post the form to.
 * @param string        $type                         Optional. Chosen type of filesystem. Default empty.
 * @param bool|WP_Error $error                        Optional. Whether the current request has failed
 *                                                    to connect, or an error object. Default false.
 * @param string        $context                      Optional. Full path to the directory that is tested
 *                                                    for being writable. Default empty.
 * @param array         $extra_fields                 Optional. Extra `POST` fields to be checked
 *                                                    for inclusion in the post. Default null.
 * @param bool          $allow_relaxed_file_ownership Optional. Whether to allow Group/World writable.
 *                                                    Default false.
 * @return bool|array True if no filesystem credentials are required,
 *                    false if they are required but have not been provided,
 *                    array of credentials if they are required and have been provided.
 */
function request_filesystem_credentials( $form_post, $type = '', $error = false, $context = '', $extra_fields = null, $allow_relaxed_file_ownership = false ) {
	global $pagenow;

	/**
	 * Filters the filesystem credentials.
	 *
	 * Returning anything other than an empty string will effectively short-circuit
	 * output of the filesystem credentials form, returning that value instead.
	 *
	 * A filter should return true if no filesystem credentials are required, false if they are required but have not been
	 * provided, or an array of credentials if they are required and have been provided.
	 *
	 * @since 2.5.0
	 * @since 4.6.0 The `$context` parameter default changed from `false` to an empty string.
	 *
	 * @param mixed         $credentials                  Credentials to return instead. Default empty string.
	 * @param string        $form_post                    The URL to post the form to.
	 * @param string        $type                         Chosen type of filesystem.
	 * @param bool|WP_Error $error                        Whether the current request has failed to connect,
	 *                                                    or an error object.
	 * @param string        $context                      Full path to the directory that is tested for
	 *                                                    being writable.
	 * @param array         $extra_fields                 Extra POST fields.
	 * @param bool          $allow_relaxed_file_ownership Whether to allow Group/World writable.
	 */
	$req_cred = apply_filters( 'request_filesystem_credentials', '', $form_post, $type, $error, $context, $extra_fields, $allow_relaxed_file_ownership );
	if ( '' !== $req_cred ) {
		return $req_cred;
	}

	if ( empty( $type ) ) {
		$type = get_filesystem_method( array(), $context, $allow_relaxed_file_ownership );
	}

	if ( 'direct' === $type ) {
		return true;
	}

	if ( is_null( $extra_fields ) ) {
		$extra_fields = array( 'version', 'locale' );
	}

	$credentials = get_option(
		'ftp_credentials',
		array(
			'hostname' => '',
			'username' => '',
		)
	);

	$submitted_form = wp_unslash( $_POST );

	// Verify nonce, or unset submitted form field values on failure.
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

	// If defined, set it to that. Else, if POST'd, set it to that. If not, set it to whatever it previously was (saved details in option).
	$credentials['hostname'] = defined( 'FTP_HOST' ) ? FTP_HOST : ( ! empty( $submitted_form['hostname'] ) ? $submitted_form['hostname'] : $credentials['hostname'] );
	$credentials['username'] = defined( 'FTP_USER' ) ? FTP_USER : ( ! empty( $submitted_form['username'] ) ? $submitted_form['username'] : $credentials['username'] );
	$credentials['password'] = defined( 'FTP_PASS' ) ? FTP_PASS : ( ! empty( $submitted_form['password'] ) ? $submitted_form['password'] : '' );

	// Check to see if we are setting the public/private keys for ssh.
	$credentials['public_key']  = defined( 'FTP_PUBKEY' ) ? FTP_PUBKEY : ( ! empty( $submitted_form['public_key'] ) ? $submitted_form['public_key'] : '' );
	$credentials['private_key'] = defined( 'FTP_PRIKEY' ) ? FTP_PRIKEY : ( ! empty( $submitted_form['private_key'] ) ? $submitted_form['private_key'] : '' );

	// Sanitize the hostname, some people might pass in odd data.
	$credentials['hostname'] = preg_replace( '|\w+://|', '', $credentials['hostname'] ); // Strip any schemes off.

	if ( strpos( $credentials['hostname'], ':' ) ) {
		list( $credentials['hostname'], $credentials['port'] ) = explode( ':', $credentials['hostname'], 2 );
		if ( ! is_numeric( $credentials['port'] ) ) {
			unset( $credentials['port'] );
		}
	} else {
		unset( $credentials['port'] );
	}

	if ( ( defined( 'FTP_SSH' ) && FTP_SSH ) || ( defined( 'FS_METHOD' ) && 'ssh2' === FS_METHOD ) ) {
		$credentials['connection_type'] = 'ssh';
	} elseif ( ( defined( 'FTP_SSL' ) && FTP_SSL ) && 'ftpext' === $type ) { // Only the FTP Extension understands SSL.
		$credentials['connection_type'] = 'ftps';
	} elseif ( ! empty( $submitted_form['connection_type'] ) ) {
		$credentials['connection_type'] = $submitted_form['connection_type'];
	} elseif ( ! isset( $credentials['connection_type'] ) ) { // All else fails (and it's not defaulted to something else saved), default to FTP.
		$credentials['connection_type'] = 'ftp';
	}
	if ( ! $error
		&& ( ( ! empty( $credentials['password'] ) && ! empty( $credentials['username'] ) && ! empty( $credentials['hostname'] ) )
			|| ( 'ssh' === $credentials['connection_type'] && ! empty( $credentials['public_key'] ) && ! empty( $credentials['private_key'] ) )
		)
	) {
		$stored_credentials = $credentials;

		if ( ! empty( $stored_credentials['port'] ) ) { // Save port as part of hostname to simplify above code.
			$stored_credentials['hostname'] .= ':' . $stored_credentials['port'];
		}

		unset( $stored_credentials['password'], $stored_credentials['port'], $stored_credentials['private_key'], $stored_credentials['public_key'] );

		if ( ! wp_installing() ) {
			update_option( 'ftp_credentials', $stored_credentials );
		}

		return $credentials;
	}
	$hostname        = isset( $credentials['hostname'] ) ? $credentials['hostname'] : '';
	$username        = isset( $credentials['username'] ) ? $credentials['username'] : '';
	$public_key      = isset( $credentials['public_key'] ) ? $credentials['public_key'] : '';
	$private_key     = isset( $credentials['private_key'] ) ? $credentials['private_key'] : '';
	$port            = isset( $credentials['port'] ) ? $credentials['port'] : '';
	$connection_type = isset( $credentials['connection_type'] ) ? $credentials['connection_type'] : '';

	if ( $error ) {
		$error_string = __( '<strong>Error</strong>: Could not connect to the server. Please verify the settings are correct.' );
		if ( is_wp_error( $error ) ) {
			$error_string = esc_html( $error->get_error_message() );
		}
		echo '<div id="message" class="error"><p>' . $error_string . '</p></div>';
	}

	$types = array();
	if ( extension_loaded( 'ftp' ) || extension_loaded( 'sockets' ) || function_exists( 'fsockopen' ) ) {
		$types['ftp'] = __( 'FTP' );
	}
	if ( extension_loaded( 'ftp' ) ) { // Only this supports FTPS.
		$types['ftps'] = __( 'FTPS (SSL)' );
	}
	if ( extension_loaded( 'ssh2' ) ) {
		$types['ssh'] = __( 'SSH2' );
	}

	/**
	 * Filters the connection types to output to the filesystem credentials form.
	 *
	 * @since 2.9.0
	 * @since 4.6.0 The `$context` parameter default changed from `false` to an empty string.
	 *
	 * @param string[]      $types       Types of connections.
	 * @param array         $credentials Credentials to connect with.
	 * @param string        $type        Chosen filesystem method.
	 * @param bool|WP_Error $error       Whether the current request has failed to connect,
	 *                                   or an error object.
	 * @param string        $context     Full path to the directory that is tested for being writable.
	 */
	$types = apply_filters( 'fs_ftp_connection_types', $types, $credentials, $type, $error, $context );

	?>
<form action="<?php echo esc_url( $form_post ); ?>" method="post">
<div id="request-filesystem-credentials-form" class="request-filesystem-credentials-form">
	<?php
	// Print a H1 heading in the FTP credentials modal dialog, default is a H2.
	$heading_tag = 'h2';
	if ( 'plugins.php' === $pagenow || 'plugin-install.php' === $pagenow ) {
		$heading_tag = 'h1';
	}
	echo "<$heading_tag id='request-filesystem-credentials-title'>" . __( 'Connection Information' ) . "</$heading_tag>";
	?>
<p id="request-filesystem-credentials-desc">
	<?php
	$label_user = __( 'Username' );
	$label_pass = __( 'Password' );
	_e( 'To perform the requested action, WordPress needs to access your web server.' );
	echo ' ';
	if ( ( isset( $types['ftp'] ) || isset( $types['ftps'] ) ) ) {
		if ( isset( $types['ssh'] ) ) {
			_e( 'Please enter your FTP or SSH credentials to proceed.' );
			$label_user = __( 'FTP/SSH Username' );
			$label_pass = __( 'FTP/SSH Password' );
		} else {
			_e( 'Please enter your FTP credentials to proceed.' );
			$label_user = __( 'FTP Username' );
			$label_pass = __( 'FTP Password' );
		}
		echo ' ';
	}
	_e( 'If you do not remember your credentials, you should contact your web host.' );

	$hostname_value = esc_attr( $hostname );
	if ( ! empty( $port ) ) {
		$hostname_value .= ":$port";
	}

	$password_value = '';
	if ( defined( 'FTP_PASS' ) ) {
		$password_value = '*****';
	}
	?>
</p>
<label for="hostname">
	<span class="field-title"><?php _e( 'Hostname' ); ?></span>
	<input name="hostname" type="text" id="hostname" aria-describedby="request-filesystem-credentials-desc" class="code" placeholder="<?php esc_attr_e( 'example: www.wordpress.org' ); ?>" value="<?php echo $hostname_value; ?>"<?php disabled( defined( 'FTP_HOST' ) ); ?> />
</label>
<div class="ftp-username">
	<label for="username">
		<span class="field-title"><?php echo $label_user; ?></span>
		<input name="username" type="text" id="username" value="<?php echo esc_attr( $username ); ?>"<?php disabled( defined( 'FTP_USER' ) ); ?> />
	</label>
</div>
<div class="ftp-password">
	<label for="password">
		<span class="field-title"><?php echo $label_pass; ?></span>
		<input name="password" type="password" id="password" value="<?php echo $password_value; ?>"<?php disabled( defined( 'FTP_PASS' ) ); ?> />
		<em>
		<?php
		if ( ! defined( 'FTP_PASS' ) ) {
			_e( 'This password will not be stored on the server.' );}
		?>
</em>
	</label>
</div>
<fieldset>
<legend><?php _e( 'Connection Type' ); ?></legend>
	<?php
	$disabled = disabled( ( defined( 'FTP_SSL' ) && FTP_SSL ) || ( defined( 'FTP_SSH' ) && FTP_SSH ), true, false );
	foreach ( $types as $name => $text ) :
		?>
	<label for="<?php echo esc_attr( $name ); ?>">
		<input type="radio" name="connection_type" id="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $name ); ?>" <?php checked( $name, $connection_type ); ?> <?php echo $disabled; ?> />
		<?php echo $text; ?>
	</label>
		<?php
	endforeach;
	?>
</fieldset>
	<?php
	if ( isset( $types['ssh'] ) ) {
		$hidden_class = '';
		if ( 'ssh' !== $connection_type || empty( $connection_type ) ) {
			$hidden_class = ' class="hidden"';
		}
		?>
<fieldset id="ssh-keys"<?php echo $hidden_class; ?>>
<legend><?php _e( 'Authentication Keys' ); ?></legend>
<label for="public_key">
	<span class="field-title"><?php _e( 'Public Key:' ); ?></span>
	<input name="public_key" type="text" id="public_key" aria-describedby="auth-keys-desc" value="<?php echo esc_attr( $public_key ); ?>"<?php disabled( defined( 'FTP_PUBKEY' ) ); ?> />
</label>
<label for="private_key">
	<span class="field-title"><?php _e( 'Private Key:' ); ?></span>
	<input name="private_key" type="text" id="private_key" value="<?php echo esc_attr( $private_key ); ?>"<?php disabled( defined( 'FTP_PRIKEY' ) ); ?> />
</label>
<p id="auth-keys-desc"><?php _e( 'Enter the location on the server where the public and private keys are located. If a passphrase is needed, enter that in the password field above.' ); ?></p>
</fieldset>
		<?php
	}

	foreach ( (array) $extra_fields as $field ) {
		if ( isset( $submitted_form[ $field ] ) ) {
			echo '<input type="hidden" name="' . esc_attr( $field ) . '" value="' . esc_attr( $submitted_form[ $field ] ) . '" />';
		}
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
 * Prints the filesystem credentials modal when needed.
 *
 * @since 4.2.0
 */
function wp_print_request_filesystem_credentials_modal() {
	$filesystem_method = get_filesystem_method();

	ob_start();
	$filesystem_credentials_are_stored = request_filesystem_credentials( self_admin_url() );
	ob_end_clean();

	$request_filesystem_credentials = ( 'direct' !== $filesystem_method && ! $filesystem_credentials_are_stored );
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

/**
 * Attempts to clear the opcode cache for an individual PHP file.
 *
 * This function can be called safely without having to check the file extension
 * or availability of the OPcache extension.
 *
 * Whether or not invalidation is possible is cached to improve performance.
 *
 * @since 5.5.0
 *
 * @link https://www.php.net/manual/en/function.opcache-invalidate.php
 *
 * @param string $filepath Path to the file, including extension, for which the opcode cache is to be cleared.
 * @param bool   $force    Invalidate even if the modification time is not newer than the file in cache.
 *                         Default false.
 * @return bool True if opcache was invalidated for `$filepath`, or there was nothing to invalidate.
 *              False if opcache invalidation is not available, or is disabled via filter.
 */
function wp_opcache_invalidate( $filepath, $force = false ) {
	static $can_invalidate = null;

	/*
	 * Check to see if WordPress is able to run `opcache_invalidate()` or not, and cache the value.
	 *
	 * First, check to see if the function is available to call, then if the host has restricted
	 * the ability to run the function to avoid a PHP warning.
	 *
	 * `opcache.restrict_api` can specify the path for files allowed to call `opcache_invalidate()`.
	 *
	 * If the host has this set, check whether the path in `opcache.restrict_api` matches
	 * the beginning of the path of the origin file.
	 *
	 * `$_SERVER['SCRIPT_FILENAME']` approximates the origin file's path, but `realpath()`
	 * is necessary because `SCRIPT_FILENAME` can be a relative path when run from CLI.
	 *
	 * For more details, see:
	 * - https://www.php.net/manual/en/opcache.configuration.php
	 * - https://www.php.net/manual/en/reserved.variables.server.php
	 * - https://core.trac.wordpress.org/ticket/36455
	 */
	if ( null === $can_invalidate
		&& function_exists( 'opcache_invalidate' )
		&& ( ! ini_get( 'opcache.restrict_api' )
			|| stripos( realpath( $_SERVER['SCRIPT_FILENAME'] ), ini_get( 'opcache.restrict_api' ) ) === 0 )
	) {
		$can_invalidate = true;
	}

	// If invalidation is not available, return early.
	if ( ! $can_invalidate ) {
		return false;
	}

	// Verify that file to be invalidated has a PHP extension.
	if ( '.php' !== strtolower( substr( $filepath, -4 ) ) ) {
		return false;
	}

	/**
	 * Filters whether to invalidate a file from the opcode cache.
	 *
	 * @since 5.5.0
	 *
	 * @param bool   $will_invalidate Whether WordPress will invalidate `$filepath`. Default true.
	 * @param string $filepath        The path to the PHP file to invalidate.
	 */
	if ( apply_filters( 'wp_opcache_invalidate_file', true, $filepath ) ) {
		return opcache_invalidate( $filepath, $force );
	}

	return false;
}
