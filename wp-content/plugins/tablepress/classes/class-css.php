<?php
/**
 * TablePress CSS Class
 *
 * @package TablePress
 * @subpackage CSS
 * @author Tobias Bäthge
 * @since 1.1.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * TablePress CSS Class
 * @package TablePress
 * @subpackage CSS
 * @author Tobias Bäthge
 * @since 1.1.0
 */
class TablePress_CSS {

	/**
	 * Sanitize and tidy a string of CSS.
	 *
	 * @since 1.1.0
	 *
	 * @param string $css CSS code.
	 * @return string Sanitized and tidied CSS code.
	 */
	public function sanitize_css( $css ) {
		$csstidy = TablePress::load_class( 'TablePress_CSSTidy', 'class.csstidy.php', 'libraries/csstidy' );

		// Sanitization and not just tidying for users without enough privileges.
		if ( ! current_user_can( 'unfiltered_html' ) ) {
			$csstidy->optimise = new TablePress_CSSTidy_custom_sanitize( $csstidy );

			// Let "arrows" survive, otherwise this might be recognized as the beginning of an HTML tag and removed with other stuff behind it.
			$css = str_replace( '<=', '&lt;=', $css );
			// Remove all HTML tags.
			$css = wp_kses( $css, 'strip' );
			// KSES replaces single ">" with "&gt;", but ">" is valid in CSS selectors.
			$css = str_replace( '&gt;', '>', $css );
			// strip_tags again, because of the just added ">" (KSES for a second time would again bring the ">" problem).
			$css = strip_tags( $css );
		}

		$csstidy->set_cfg( 'remove_bslash', false );
		$csstidy->set_cfg( 'compress_colors', false );
		$csstidy->set_cfg( 'compress_font-weight', false );
		$csstidy->set_cfg( 'lowercase_s', false );
		$csstidy->set_cfg( 'optimise_shorthands', false );
		$csstidy->set_cfg( 'remove_last_;', false );
		$csstidy->set_cfg( 'case_properties', false );
		$csstidy->set_cfg( 'sort_properties', false );
		$csstidy->set_cfg( 'sort_selectors', false );
		$csstidy->set_cfg( 'discard_invalid_selectors', false );
		$csstidy->set_cfg( 'discard_invalid_properties', true );
		$csstidy->set_cfg( 'merge_selectors', false );
		$csstidy->set_cfg( 'css_level', 'CSS3.0' );
		$csstidy->set_cfg( 'preserve_css', true );
		$csstidy->set_cfg( 'timestamp', false );
		$csstidy->set_cfg( 'template', dirname( TABLEPRESS__FILE__ ) . '/libraries/csstidy/tablepress-standard.tpl' );

		$csstidy->parse( $css );
		return $csstidy->print->plain();
	}

	/**
	 * Minify a string of CSS code, that should have been sanitized/tidied before.
	 *
	 * @since 1.1.0
	 *
	 * @param string $css CSS code.
	 * @return string Minified CSS code.
	 */
	public function minify_css( $css ) {
		$csstidy = TablePress::load_class( 'TablePress_CSSTidy', 'class.csstidy.php', 'libraries/csstidy' );
		$csstidy->optimise = new TablePress_CSSTidy_custom_sanitize( $csstidy );
		$csstidy->set_cfg( 'remove_bslash', false );
		$csstidy->set_cfg( 'compress_colors', true );
		$csstidy->set_cfg( 'compress_font-weight', true );
		$csstidy->set_cfg( 'lowercase_s', false );
		$csstidy->set_cfg( 'optimise_shorthands', 1 );
		$csstidy->set_cfg( 'remove_last_;', true );
		$csstidy->set_cfg( 'case_properties', false );
		$csstidy->set_cfg( 'sort_properties', false );
		$csstidy->set_cfg( 'sort_selectors', false );
		$csstidy->set_cfg( 'discard_invalid_selectors', false );
		$csstidy->set_cfg( 'discard_invalid_properties', true );
		$csstidy->set_cfg( 'merge_selectors', false );
		$csstidy->set_cfg( 'css_level', 'CSS3.0' );
		$csstidy->set_cfg( 'preserve_css', false );
		$csstidy->set_cfg( 'timestamp', false );
		$csstidy->set_cfg( 'template', 'highest' );

		$csstidy->parse( $css );
		return $csstidy->print->plain();
	}

	/**
	 * Get the location (file path or URL) of the "Custom CSS" file, depending on whether it's a Multisite install or not.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type     "normal" version, "minified" version, or "combined" (with TablePress Default CSS) version.
	 * @param string $location "path" or "url", for file path or URL.
	 * @return string Full file path or full URL for the "Custom CSS" file.
	 */
	public function get_custom_css_location( $type, $location ) {
		switch ( $type ) {
			case 'combined':
				$file = 'tablepress-combined.min.css';
				break;
			case 'minified':
				$file = 'tablepress-custom.min.css';
				break;
			case 'normal':
			default:
				$file = 'tablepress-custom.css';
				break;
		}

		if ( is_multisite() ) {
			// Multisite installation: /wp-content/uploads/sites/<ID>/
			$upload_location = wp_upload_dir();
		} else {
			// Singlesite installation: /wp-content/
			$upload_location = array(
				'basedir' => WP_CONTENT_DIR,
				'baseurl' => content_url(),
			);
		}

		switch ( $location ) {
			case 'url':
				$url = set_url_scheme( $upload_location['baseurl'] . '/' . $file );
				/**
				 * Filter the URL from which the "Custom CSS" file is loaded.
				 *
				 * @since 1.0.0
				 *
				 * @param string $url  URL of the "Custom CSS" file.
				 * @param string $file File name of the "Custom CSS" file.
				 * @param string $type Type of the "Custom CSS" file ("normal", "minified", or "combined").
				 */
				$url = apply_filters( 'tablepress_custom_css_url', $url, $file, $type );
				return $url;
				// break; // unreachable
			case 'path':
				$path = $upload_location['basedir'] . '/' . $file;
				/**
				 * Filter the file path on the server from which the "Custom CSS" file is loaded.
				 *
				 * @since 1.0.0
				 *
				 * @param string $path File path of the "Custom CSS" file.
				 * @param string $file File name of the "Custom CSS" file.
				 * @param string $type Type of the "Custom CSS" file ("normal", "minified", or "combined").
				 */
				$path = apply_filters( 'tablepress_custom_css_file_name', $path, $file, $type );
				return $path;
				// break; // unreachable
		}
	}

	/**
	 * Load the contents of the file with the "Custom CSS".
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Optional. Whether to load "normal" version or "minified" version. Default "normal".
	 * @return string|bool Custom CSS on success, false on error.
	 */
	public function load_custom_css_from_file( $type = 'normal' ) {
		$filename = $this->get_custom_css_location( $type, 'path' );
		// Check if file name is valid (0 means yes).
		if ( 0 !== validate_file( $filename ) ) {
			return false;
		}
		if ( ! @is_file( $filename ) ) {
			return false;
		}
		if ( ! @is_readable( $filename ) ) {
			return false;
		}
		return file_get_contents( $filename );
	}

	/**
	 * Load the contents of the file with the TablePress Default CSS.
	 *
	 * @since 1.1.0
	 *
	 * @return string|bool TablePress Default CSS on success, false on error.
	 */
	public function load_default_css_from_file() {
		$filename = TABLEPRESS_ABSPATH . 'css/default.min.css';
		// Check if file name is valid (0 means yes).
		if ( 0 !== validate_file( $filename ) ) {
			return false;
		}
		if ( ! @is_file( $filename ) ) {
			return false;
		}
		if ( ! @is_readable( $filename ) ) {
			return false;
		}
		return file_get_contents( $filename );
	}

	/**
	 * Try to save "Custom CSS" to a file (requires "direct" method in WP_Filesystem, or stored FTP credentials).
	 *
	 * @since 1.1.0
	 *
	 * @param string $custom_css_normal   Custom CSS code to be saved.
	 * @param string $custom_css_minified Minified CSS code to be saved.
	 * @return bool True on success, false on failure.
	 */
	public function save_custom_css_to_file( $custom_css_normal, $custom_css_minified ) {
		/**
		 * Filter whether the "Custom CSS" code shall be saved to a file on the server.
		 *
		 * @since 1.1.0
		 *
		 * @param bool $save Whether to save the "Custom CSS" to a file. Default true.
		 */
		if ( ! apply_filters( 'tablepress_save_custom_css_to_file', true ) ) {
			return false;
		}

		// Start capturing the output, to later prevent it.
		ob_start();
		$credentials = request_filesystem_credentials( '', '', false, false, null );
		/*
		 * Do we have credentials already? (Otherwise the form will have been rendered, which is not supported here.)
		 * Or, if we have credentials, are they valid?
		 */
		if ( false === $credentials || ! WP_Filesystem( $credentials ) ) {
			ob_end_clean();
			return false;
		}

		// We have valid access to the filesystem now -> try to save the files.
		return $this->_custom_css_save_helper( $custom_css_normal, $custom_css_minified );
	}

	/**
	 * Save "Custom CSS" to files, delete "Custom CSS" files, or return HTML for the credentials form.
	 *
	 * Only used from "Plugin Options" screen, save_custom_css_to_file() is used in cases where no form output/redirection is possible (plugin updates, WP-Table Reloaded Import).
	 *
	 * @since 1.0.0
	 *
	 * @param string $custom_css_normal   Custom CSS code to be saved. If empty, files will be deleted.
	 * @param string $custom_css_minified Minified CSS code to be saved.
	 * @return bool|string True on success, false on failure, or string of HTML for the credentials form for the WP_Filesystem API, if necessary.
	 */
	public function save_custom_css_to_file_plugin_options( $custom_css_normal, $custom_css_minified ) {
		/** This filter is documented in classes/class-css.php */
		if ( ! apply_filters( 'tablepress_save_custom_css_to_file', true ) ) {
			return false;
		}

		// Start capturing the output, to get HTML of the credentials form (if needed).
		ob_start();
		$credentials = request_filesystem_credentials( '', '', false, false, null );
		// Do we have credentials already? (Otherwise the form will have been rendered already.)
		if ( false === $credentials ) {
			$form_data = ob_get_clean();
			$form_data = str_replace( 'name="upgrade" id="upgrade" class="button"', 'name="upgrade" id="upgrade" class="button button-primary button-large"', $form_data );
			return $form_data;
		}

		// We have received credentials, but don't know if they are valid yet.
		if ( ! WP_Filesystem( $credentials ) ) {
			// Credentials failed, so ask again (with $error flag true).
			request_filesystem_credentials( '', '', true, false, null );
			$form_data = ob_get_clean();
			$form_data = str_replace( 'name="upgrade" id="upgrade" class="button"', 'name="upgrade" id="upgrade" class="button button-primary button-large"', $form_data );
			return $form_data;
		}

		// We have valid access to the filesystem now -> try to save the files, or delete them if the "Custom CSS" is empty.
		if ( '' !== $custom_css_normal ) {
			return $this->_custom_css_save_helper( $custom_css_normal, $custom_css_minified );
		} else {
			return $this->_custom_css_delete_helper();
		}
	}

	/**
	 * Save "Custom CSS" to files, if validated access to the WP_Filesystem exists.
	 *
	 * Helper function to prevent code duplication.
	 *
	 * @since 1.1.0
	 *
	 * @global WP_Filesystem_* $wp_filesystem WordPress file system abstraction object.
	 * @see save_custom_css_to_file()
	 * @see save_custom_css_to_file_plugin_options()
	 *
	 * @param string $custom_css_normal   Custom CSS code to be saved.
	 * @param string $custom_css_minified Minified CSS code to be saved.
	 * @return bool True on success, false on failure.
	 */
	protected function _custom_css_save_helper( $custom_css_normal, $custom_css_minified ) {
		global $wp_filesystem;

		/*
		 * WP_CONTENT_DIR and (FTP-)Content-Dir can be different (e.g. if FTP working dir is /).
		 * We need to account for that by replacing the path difference in the filename.
		 */
		$path_difference = str_replace( $wp_filesystem->wp_content_dir(), '', trailingslashit( WP_CONTENT_DIR ) );

		$css_types = array( 'normal', 'minified', 'combined' );

		$default_css = $this->load_default_css_from_file();
		if ( false === $default_css ) {
			$default_css = '';
		} else {
			// Change relative URLs to web font files to absolute URLs, as combining the CSS files and saving to another directory breaks the relative URLs.
			$absolute_path = plugins_url( 'css/tablepress.', TABLEPRESS__FILE__ );
			// Make the absolute URL protocol-relative to prevent mixed content warnings.
			$absolute_path = str_replace( array( 'http:', 'https:' ), '', $absolute_path );
			$default_css = str_replace( 'url(tablepress.', 'url(' . $absolute_path, $default_css );
		}
		$file_content = array(
			'normal'   => $custom_css_normal,
			'minified' => $custom_css_minified,
			'combined' => $default_css . "\n" . $custom_css_minified,
		);

		$total_result = true; // whether all files were saved successfully
		foreach ( $css_types as $css_type ) {
			$filename = $this->get_custom_css_location( $css_type, 'path' );
			// Check if filename is valid (0 means yes).
			if ( 0 !== validate_file( $filename ) ) {
				$total_result = false;
				continue;
			}
			if ( '' !== $path_difference ) {
				$filename = str_replace( $path_difference, '', $filename );
			}
			$result = $wp_filesystem->put_contents( $filename, $file_content[ $css_type ], FS_CHMOD_FILE );
			$total_result = ( $total_result && $result );
		}

		$this->_flush_caching_plugins_css_minify_caches();

		return $total_result;
	}

	/**
	 * Delete the "Custom CSS" files, if possible.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True on success, false on failure.
	 */
	public function delete_custom_css_files() {
		// Start capturing the output, to later prevent it.
		ob_start();
		$credentials = request_filesystem_credentials( '', '', false, false, null );
		/*
		 * Do we have credentials already? (Otherwise the form will have been rendered, which is not supported here.)
		 * Or, if we have credentials, are they valid?
		 */
		if ( false === $credentials || ! WP_Filesystem( $credentials ) ) {
			ob_end_clean();
			return false;
		}

		// We have valid access to the filesystem now -> try to delete the files.
		return $this->_custom_css_delete_helper();
	}

	/**
	 * Delete "Custom CSS" files, if validated access to the WP_Filesystem exists.
	 *
	 * Helper function to prevent code duplication
	 *
	 * @since 1.1.0
	 *
	 * @global WP_Filesystem_* $wp_filesystem WordPress file system abstraction object.
	 * @see delete_custom_css_files()
	 * @see save_custom_css_to_file_plugin_options()
	 *
	 * @return bool True on success, false on failure.
	 */
	protected function _custom_css_delete_helper() {
		global $wp_filesystem;

		/*
		 * WP_CONTENT_DIR and (FTP-)Content-Dir can be different (e.g. if FTP working dir is /).
		 * We need to account for that by replacing the path difference in the filename.
		 */
		$path_difference = str_replace( $wp_filesystem->wp_content_dir(), '', trailingslashit( WP_CONTENT_DIR ) );

		$css_types = array( 'normal', 'minified', 'combined' );
		$total_result = true; // whether all files were deleted successfully
		foreach ( $css_types as $css_type ) {
			$filename = $this->get_custom_css_location( $css_type, 'path' );
			// Check if filename is valid (0 means yes).
			if ( 0 !== validate_file( $filename ) ) {
				$total_result = false;
				continue;
			}
			if ( '' !== $path_difference ) {
				$filename = str_replace( $path_difference, '', $filename );
			}
			// We have valid access to the filesystem now -> try to delete the file.
			if ( $wp_filesystem->exists( $filename ) ) {
				$result = $wp_filesystem->delete( $filename );
				$total_result = ( $total_result && $result );
			}
		}

		$this->_flush_caching_plugins_css_minify_caches();

		return $total_result;
	}

	/**
	 * Flush the CSS minification cache of W3 Total Cache.
	 *
	 * @since 1.4.0
	 */
	protected function _flush_caching_plugins_css_minify_caches() {
		/** This filter is documented in models/model-table.php */
		if ( ! apply_filters( 'tablepress_flush_caching_plugins_caches', true ) ) {
			return;
		}

		// W3 Total Cache
		if ( function_exists( 'w3tc_minify_flush' ) ) {
			w3tc_minify_flush();
		}
	}

} // class TablePress_CSS
