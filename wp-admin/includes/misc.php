<?php
/**
 * Misc WordPress Administration API.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Returns whether the server is running Apache with the mod_rewrite module loaded.
 *
 * @since 2.0.0
 *
 * @return bool
 */
function got_mod_rewrite() {
	$got_rewrite = apache_mod_loaded('mod_rewrite', true);

	/**
	 * Filters whether Apache and mod_rewrite are present.
	 *
	 * This filter was previously used to force URL rewriting for other servers,
	 * like nginx. Use the {@see 'got_url_rewrite'} filter in got_url_rewrite() instead.
	 *
	 * @since 2.5.0
	 *
	 * @see got_url_rewrite()
	 *
	 * @param bool $got_rewrite Whether Apache and mod_rewrite are present.
	 */
	return apply_filters( 'got_rewrite', $got_rewrite );
}

/**
 * Returns whether the server supports URL rewriting.
 *
 * Detects Apache's mod_rewrite, IIS 7.0+ permalink support, and nginx.
 *
 * @since 3.7.0
 *
 * @global bool $is_nginx
 *
 * @return bool Whether the server supports URL rewriting.
 */
function got_url_rewrite() {
	$got_url_rewrite = ( got_mod_rewrite() || $GLOBALS['is_nginx'] || iis7_supports_permalinks() );

	/**
	 * Filters whether URL rewriting is available.
	 *
	 * @since 3.7.0
	 *
	 * @param bool $got_url_rewrite Whether URL rewriting is available.
	 */
	return apply_filters( 'got_url_rewrite', $got_url_rewrite );
}

/**
 * Extracts strings from between the BEGIN and END markers in the .htaccess file.
 *
 * @since 1.5.0
 *
 * @param string $filename
 * @param string $marker
 * @return array An array of strings from a file (.htaccess ) from between BEGIN and END markers.
 */
function extract_from_markers( $filename, $marker ) {
	$result = array ();

	if ( ! file_exists( $filename ) ) {
		return $result;
	}

	$markerdata = explode( "\n", implode( '', file( $filename ) ) );

	$state = false;
	foreach ( $markerdata as $markerline ) {
		if ( false !== strpos( $markerline, '# END ' . $marker ) ) {
			$state = false;
		}
		if ( $state ) {
			$result[] = $markerline;
		}
		if ( false !== strpos( $markerline, '# BEGIN ' . $marker ) ) {
			$state = true;
		}
	}

	return $result;
}

/**
 * Inserts an array of strings into a file (.htaccess ), placing it between
 * BEGIN and END markers.
 *
 * Replaces existing marked info. Retains surrounding
 * data. Creates file if none exists.
 *
 * @since 1.5.0
 *
 * @param string       $filename  Filename to alter.
 * @param string       $marker    The marker to alter.
 * @param array|string $insertion The new content to insert.
 * @return bool True on write success, false on failure.
 */
function insert_with_markers( $filename, $marker, $insertion ) {
	if ( ! file_exists( $filename ) ) {
		if ( ! is_writable( dirname( $filename ) ) ) {
			return false;
		}
		if ( ! touch( $filename ) ) {
			return false;
		}
	} elseif ( ! is_writeable( $filename ) ) {
		return false;
	}

	if ( ! is_array( $insertion ) ) {
		$insertion = explode( "\n", $insertion );
	}

	$start_marker = "# BEGIN {$marker}";
	$end_marker   = "# END {$marker}";

	$fp = fopen( $filename, 'r+' );
	if ( ! $fp ) {
		return false;
	}

	// Attempt to get a lock. If the filesystem supports locking, this will block until the lock is acquired.
	flock( $fp, LOCK_EX );

	$lines = array();
	while ( ! feof( $fp ) ) {
		$lines[] = rtrim( fgets( $fp ), "\r\n" );
	}

	// Split out the existing file into the preceding lines, and those that appear after the marker
	$pre_lines = $post_lines = $existing_lines = array();
	$found_marker = $found_end_marker = false;
	foreach ( $lines as $line ) {
		if ( ! $found_marker && false !== strpos( $line, $start_marker ) ) {
			$found_marker = true;
			continue;
		} elseif ( ! $found_end_marker && false !== strpos( $line, $end_marker ) ) {
			$found_end_marker = true;
			continue;
		}
		if ( ! $found_marker ) {
			$pre_lines[] = $line;
		} elseif ( $found_marker && $found_end_marker ) {
			$post_lines[] = $line;
		} else {
			$existing_lines[] = $line;
		}
	}

	// Check to see if there was a change
	if ( $existing_lines === $insertion ) {
		flock( $fp, LOCK_UN );
		fclose( $fp );

		return true;
	}

	// Generate the new file data
	$new_file_data = implode( "\n", array_merge(
		$pre_lines,
		array( $start_marker ),
		$insertion,
		array( $end_marker ),
		$post_lines
	) );

	// Write to the start of the file, and truncate it to that length
	fseek( $fp, 0 );
	$bytes = fwrite( $fp, $new_file_data );
	if ( $bytes ) {
		ftruncate( $fp, ftell( $fp ) );
	}
	fflush( $fp );
	flock( $fp, LOCK_UN );
	fclose( $fp );

	return (bool) $bytes;
}

/**
 * Updates the htaccess file with the current rules if it is writable.
 *
 * Always writes to the file if it exists and is writable to ensure that we
 * blank out old rules.
 *
 * @since 1.5.0
 *
 * @global WP_Rewrite $wp_rewrite
 *
 * @return bool|null True on write success, false on failure. Null in multisite.
 */
function save_mod_rewrite_rules() {
	if ( is_multisite() )
		return;

	global $wp_rewrite;

	// Ensure get_home_path() is declared.
	require_once( ABSPATH . 'wp-admin/includes/file.php' );

	$home_path     = get_home_path();
	$htaccess_file = $home_path . '.htaccess';

	/*
	 * If the file doesn't already exist check for write access to the directory
	 * and whether we have some rules. Else check for write access to the file.
	 */
	if ((!file_exists($htaccess_file) && is_writable($home_path) && $wp_rewrite->using_mod_rewrite_permalinks()) || is_writable($htaccess_file)) {
		if ( got_mod_rewrite() ) {
			$rules = explode( "\n", $wp_rewrite->mod_rewrite_rules() );
			return insert_with_markers( $htaccess_file, 'WordPress', $rules );
		}
	}

	return false;
}

/**
 * Updates the IIS web.config file with the current rules if it is writable.
 * If the permalinks do not require rewrite rules then the rules are deleted from the web.config file.
 *
 * @since 2.8.0
 *
 * @global WP_Rewrite $wp_rewrite
 *
 * @return bool|null True on write success, false on failure. Null in multisite.
 */
function iis7_save_url_rewrite_rules(){
	if ( is_multisite() )
		return;

	global $wp_rewrite;

	// Ensure get_home_path() is declared.
	require_once( ABSPATH . 'wp-admin/includes/file.php' );

	$home_path       = get_home_path();
	$web_config_file = $home_path . 'web.config';

	// Using win_is_writable() instead of is_writable() because of a bug in Windows PHP
	if ( iis7_supports_permalinks() && ( ( ! file_exists($web_config_file) && win_is_writable($home_path) && $wp_rewrite->using_mod_rewrite_permalinks() ) || win_is_writable($web_config_file) ) ) {
		$rule = $wp_rewrite->iis7_url_rewrite_rules(false, '', '');
		if ( ! empty($rule) ) {
			return iis7_add_rewrite_rule($web_config_file, $rule);
		} else {
			return iis7_delete_rewrite_rule($web_config_file);
		}
	}
	return false;
}

/**
 * Update the "recently-edited" file for the plugin or theme editor.
 *
 * @since 1.5.0
 *
 * @param string $file
 */
function update_recently_edited( $file ) {
	$oldfiles = (array ) get_option( 'recently_edited' );
	if ( $oldfiles ) {
		$oldfiles = array_reverse( $oldfiles );
		$oldfiles[] = $file;
		$oldfiles = array_reverse( $oldfiles );
		$oldfiles = array_unique( $oldfiles );
		if ( 5 < count( $oldfiles ))
			array_pop( $oldfiles );
	} else {
		$oldfiles[] = $file;
	}
	update_option( 'recently_edited', $oldfiles );
}

/**
 * Makes a tree structure for the Theme Editor's file list.
 *
 * @since 4.9.0
 * @access private
 *
 * @param array $allowed_files List of theme file paths.
 * @return array Tree structure for listing theme files.
 */
function wp_make_theme_file_tree( $allowed_files ) {
	$tree_list = array();
	foreach ( $allowed_files as $file_name => $absolute_filename ) {
		$list = explode( '/', $file_name );
		$last_dir = &$tree_list;
		foreach ( $list as $dir ) {
			$last_dir =& $last_dir[ $dir ];
		}
		$last_dir = $file_name;
	}
	return $tree_list;
}

/**
 * Outputs the formatted file list for the Theme Editor.
 *
 * @since 4.9.0
 * @access private
 *
 * @param array|string $tree  List of file/folder paths, or filename.
 * @param int          $level The aria-level for the current iteration.
 * @param int          $size  The aria-setsize for the current iteration.
 * @param int          $index The aria-posinset for the current iteration.
 */
function wp_print_theme_file_tree( $tree, $level = 2, $size = 1, $index = 1 ) {
	global $relative_file, $stylesheet;

	if ( is_array( $tree ) ) {
		$index = 0;
		$size = count( $tree );
		foreach ( $tree as $label => $theme_file ) :
			$index++;
			if ( ! is_array( $theme_file ) ) {
				wp_print_theme_file_tree( $theme_file, $level, $index, $size );
				continue;
			}
			?>
			<li role="treeitem" aria-expanded="true" tabindex="-1"
				aria-level="<?php echo esc_attr( $level ); ?>"
				aria-setsize="<?php echo esc_attr( $size ); ?>"
				aria-posinset="<?php echo esc_attr( $index ); ?>">
				<span class="folder-label"><?php echo esc_html( $label ); ?> <span class="screen-reader-text"><?php _e( 'folder' ); ?></span><span aria-hidden="true" class="icon"></span></span>
				<ul role="group" class="tree-folder"><?php wp_print_theme_file_tree( $theme_file, $level + 1, $index, $size ); ?></ul>
			</li>
			<?php
		endforeach;
	} else {
		$filename = $tree;
		$url = add_query_arg(
			array(
				'file' => rawurlencode( $tree ),
				'theme' => rawurlencode( $stylesheet ),
			),
			self_admin_url( 'theme-editor.php' )
		);
		?>
		<li role="none" class="<?php echo esc_attr( $relative_file === $filename ? 'current-file' : '' ); ?>">
			<a role="treeitem" tabindex="<?php echo esc_attr( $relative_file === $filename ? '0' : '-1' ); ?>"
				href="<?php echo esc_url( $url ); ?>"
				aria-level="<?php echo esc_attr( $level ); ?>"
				aria-setsize="<?php echo esc_attr( $size ); ?>"
				aria-posinset="<?php echo esc_attr( $index ); ?>">
				<?php
				$file_description = esc_html( get_file_description( $filename ) );
				if ( $file_description !== $filename && basename( $filename ) !== $file_description ) {
					$file_description .= '<br /><span class="nonessential">(' . esc_html( $filename ) . ')</span>';
				}

				if ( $relative_file === $filename ) {
					echo '<span class="notice notice-info">' . $file_description . '</span>';
				} else {
					echo $file_description;
				}
				?>
			</a>
		</li>
		<?php
	}
}

/**
 * Makes a tree structure for the Plugin Editor's file list.
 *
 * @since 4.9.0
 * @access private
 *
 * @param string $plugin_editable_files List of plugin file paths.
 * @return array Tree structure for listing plugin files.
 */
function wp_make_plugin_file_tree( $plugin_editable_files ) {
	$tree_list = array();
	foreach ( $plugin_editable_files as $plugin_file ) {
		$list = explode( '/', preg_replace( '#^.+?/#', '', $plugin_file ) );
		$last_dir = &$tree_list;
		foreach ( $list as $dir ) {
			$last_dir =& $last_dir[ $dir ];
		}
		$last_dir = $plugin_file;
	}
	return $tree_list;
}

/**
 * Outputs the formatted file list for the Plugin Editor.
 *
 * @since 4.9.0
 * @access private
 *
 * @param array|string $tree  List of file/folder paths, or filename.
 * @param string       $label Name of file or folder to print.
 * @param int          $level The aria-level for the current iteration.
 * @param int          $size  The aria-setsize for the current iteration.
 * @param int          $index The aria-posinset for the current iteration.
 */
function wp_print_plugin_file_tree( $tree, $label = '', $level = 2, $size = 1, $index = 1 ) {
	global $file, $plugin;
	if ( is_array( $tree ) ) {
		$index = 0;
		$size = count( $tree );
		foreach ( $tree as $label => $plugin_file ) :
			$index++;
			if ( ! is_array( $plugin_file ) ) {
				wp_print_plugin_file_tree( $plugin_file, $label, $level, $index, $size );
				continue;
			}
			?>
			<li role="treeitem" aria-expanded="true" tabindex="-1"
				aria-level="<?php echo esc_attr( $level ); ?>"
				aria-setsize="<?php echo esc_attr( $size ); ?>"
				aria-posinset="<?php echo esc_attr( $index ); ?>">
				<span class="folder-label"><?php echo esc_html( $label ); ?> <span class="screen-reader-text"><?php _e( 'folder' ); ?></span><span aria-hidden="true" class="icon"></span></span>
				<ul role="group" class="tree-folder"><?php wp_print_plugin_file_tree( $plugin_file, '', $level + 1, $index, $size ); ?></ul>
			</li>
			<?php
		endforeach;
	} else {
		$url = add_query_arg(
			array(
				'file' => rawurlencode( $tree ),
				'plugin' => rawurlencode( $plugin ),
			),
			self_admin_url( 'plugin-editor.php' )
		);
		?>
		<li role="none" class="<?php echo esc_attr( $file === $tree ? 'current-file' : '' ); ?>">
			<a role="treeitem" tabindex="<?php echo esc_attr( $file === $tree ? '0' : '-1' ); ?>"
				href="<?php echo esc_url( $url ); ?>"
				aria-level="<?php echo esc_attr( $level ); ?>"
				aria-setsize="<?php echo esc_attr( $size ); ?>"
				aria-posinset="<?php echo esc_attr( $index ); ?>">
				<?php
				if ( $file === $tree ) {
					echo '<span class="notice notice-info">' . esc_html( $label ) . '</span>';
				} else {
					echo esc_html( $label );
				}
				?>
			</a>
		</li>
		<?php
	}
}

/**
 * Flushes rewrite rules if siteurl, home or page_on_front changed.
 *
 * @since 2.1.0
 *
 * @param string $old_value
 * @param string $value
 */
function update_home_siteurl( $old_value, $value ) {
	if ( wp_installing() )
		return;

	if ( is_multisite() && ms_is_switched() ) {
		delete_option( 'rewrite_rules' );
	} else {
		flush_rewrite_rules();
	}
}


/**
 * Resets global variables based on $_GET and $_POST
 *
 * This function resets global variables based on the names passed
 * in the $vars array to the value of $_POST[$var] or $_GET[$var] or ''
 * if neither is defined.
 *
 * @since 2.0.0
 *
 * @param array $vars An array of globals to reset.
 */
function wp_reset_vars( $vars ) {
	foreach ( $vars as $var ) {
		if ( empty( $_POST[ $var ] ) ) {
			if ( empty( $_GET[ $var ] ) ) {
				$GLOBALS[ $var ] = '';
			} else {
				$GLOBALS[ $var ] = $_GET[ $var ];
			}
		} else {
			$GLOBALS[ $var ] = $_POST[ $var ];
		}
	}
}

/**
 * Displays the given administration message.
 *
 * @since 2.1.0
 *
 * @param string|WP_Error $message
 */
function show_message($message) {
	if ( is_wp_error($message) ){
		if ( $message->get_error_data() && is_string( $message->get_error_data() ) )
			$message = $message->get_error_message() . ': ' . $message->get_error_data();
		else
			$message = $message->get_error_message();
	}
	echo "<p>$message</p>\n";
	wp_ob_end_flush_all();
	flush();
}

/**
 * @since 2.8.0
 *
 * @param string $content
 * @return array
 */
function wp_doc_link_parse( $content ) {
	if ( !is_string( $content ) || empty( $content ) )
		return array();

	if ( !function_exists('token_get_all') )
		return array();

	$tokens = token_get_all( $content );
	$count = count( $tokens );
	$functions = array();
	$ignore_functions = array();
	for ( $t = 0; $t < $count - 2; $t++ ) {
		if ( ! is_array( $tokens[ $t ] ) ) {
			continue;
		}

		if ( T_STRING == $tokens[ $t ][0] && ( '(' == $tokens[ $t + 1 ] || '(' == $tokens[ $t + 2 ] ) ) {
			// If it's a function or class defined locally, there's not going to be any docs available
			if ( ( isset( $tokens[ $t - 2 ][1] ) && in_array( $tokens[ $t - 2 ][1], array( 'function', 'class' ) ) ) || ( isset( $tokens[ $t - 2 ][0] ) && T_OBJECT_OPERATOR == $tokens[ $t - 1 ][0] ) ) {
				$ignore_functions[] = $tokens[$t][1];
			}
			// Add this to our stack of unique references
			$functions[] = $tokens[$t][1];
		}
	}

	$functions = array_unique( $functions );
	sort( $functions );

	/**
	 * Filters the list of functions and classes to be ignored from the documentation lookup.
	 *
	 * @since 2.8.0
	 *
	 * @param array $ignore_functions Functions and classes to be ignored.
	 */
	$ignore_functions = apply_filters( 'documentation_ignore_functions', $ignore_functions );

	$ignore_functions = array_unique( $ignore_functions );

	$out = array();
	foreach ( $functions as $function ) {
		if ( in_array( $function, $ignore_functions ) )
			continue;
		$out[] = $function;
	}

	return $out;
}

/**
 * Saves option for number of rows when listing posts, pages, comments, etc.
 *
 * @since 2.8.0
 */
function set_screen_options() {

	if ( isset($_POST['wp_screen_options']) && is_array($_POST['wp_screen_options']) ) {
		check_admin_referer( 'screen-options-nonce', 'screenoptionnonce' );

		if ( !$user = wp_get_current_user() )
			return;
		$option = $_POST['wp_screen_options']['option'];
		$value = $_POST['wp_screen_options']['value'];

		if ( $option != sanitize_key( $option ) )
			return;

		$map_option = $option;
		$type = str_replace('edit_', '', $map_option);
		$type = str_replace('_per_page', '', $type);
		if ( in_array( $type, get_taxonomies() ) )
			$map_option = 'edit_tags_per_page';
		elseif ( in_array( $type, get_post_types() ) )
			$map_option = 'edit_per_page';
		else
			$option = str_replace('-', '_', $option);

		switch ( $map_option ) {
			case 'edit_per_page':
			case 'users_per_page':
			case 'edit_comments_per_page':
			case 'upload_per_page':
			case 'edit_tags_per_page':
			case 'plugins_per_page':
			case 'export_personal_data_requests_per_page':
			case 'remove_personal_data_requests_per_page':
			// Network admin
			case 'sites_network_per_page':
			case 'users_network_per_page':
			case 'site_users_network_per_page':
			case 'plugins_network_per_page':
			case 'themes_network_per_page':
			case 'site_themes_network_per_page':
				$value = (int) $value;
				if ( $value < 1 || $value > 999 )
					return;
				break;
			default:
				if ( '_page' === substr( $option, -5 ) || 'layout_columns' === $option ) {
					/**
					 * Filters a screen option value before it is set.
					 *
					 * The filter can also be used to modify non-standard [items]_per_page
					 * settings. See the parent function for a full list of standard options.
					 *
					 * Returning false to the filter will skip saving the current option.
					 *
					 * @since 2.8.0
					 * @since 5.4.2 Only applied to options ending with '_page',
					 *              or the 'layout_columns' option.
					 *
					 * @see set_screen_options()
					 *
					 * @param bool   $keep   Whether to save or skip saving the screen option value.
					 *                       Default false.
					 * @param string $option The option name.
					 * @param int    $value  The number of rows to use.
					 */
					$value = apply_filters( 'set-screen-option', false, $option, $value ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
				}

				/**
				 * Filters a screen option value before it is set.
				 *
				 * The dynamic portion of the hook, `$option`, refers to the option name.
				 *
				 * Returning false to the filter will skip saving the current option.
				 *
				 * @since 5.4.2
				 *
				 * @see set_screen_options()
				 *
				 * @param bool   $keep   Whether to save or skip saving the screen option value.
				 *                       Default false.
				 * @param string $option The option name.
				 * @param int    $value  The number of rows to use.
				 */
				$value = apply_filters( "set_screen_option_{$option}", false, $option, $value );

				if ( false === $value )
					return;
				break;
		}

		update_user_meta($user->ID, $option, $value);

		$url = remove_query_arg( array( 'pagenum', 'apage', 'paged' ), wp_get_referer() );
		if ( isset( $_POST['mode'] ) ) {
			$url = add_query_arg( array( 'mode' => $_POST['mode'] ), $url );
		}

		wp_safe_redirect( $url );
		exit;
	}
}

/**
 * Check if rewrite rule for WordPress already exists in the IIS 7+ configuration file
 *
 * @since 2.8.0
 *
 * @return bool
 * @param string $filename The file path to the configuration file
 */
function iis7_rewrite_rule_exists($filename) {
	if ( ! file_exists($filename) )
		return false;
	if ( ! class_exists( 'DOMDocument', false ) ) {
		return false;
	}

	$doc = new DOMDocument();
	if ( $doc->load($filename) === false )
		return false;
	$xpath = new DOMXPath($doc);
	$rules = $xpath->query('/configuration/system.webServer/rewrite/rules/rule[starts-with(@name,\'wordpress\')] | /configuration/system.webServer/rewrite/rules/rule[starts-with(@name,\'WordPress\')]');
	if ( $rules->length == 0 )
		return false;
	else
		return true;
}

/**
 * Delete WordPress rewrite rule from web.config file if it exists there
 *
 * @since 2.8.0
 *
 * @param string $filename Name of the configuration file
 * @return bool
 */
function iis7_delete_rewrite_rule($filename) {
	// If configuration file does not exist then rules also do not exist so there is nothing to delete
	if ( ! file_exists($filename) )
		return true;

	if ( ! class_exists( 'DOMDocument', false ) ) {
		return false;
	}

	$doc = new DOMDocument();
	$doc->preserveWhiteSpace = false;

	if ( $doc -> load($filename) === false )
		return false;
	$xpath = new DOMXPath($doc);
	$rules = $xpath->query('/configuration/system.webServer/rewrite/rules/rule[starts-with(@name,\'wordpress\')] | /configuration/system.webServer/rewrite/rules/rule[starts-with(@name,\'WordPress\')]');
	if ( $rules->length > 0 ) {
		$child = $rules->item(0);
		$parent = $child->parentNode;
		$parent->removeChild($child);
		$doc->formatOutput = true;
		saveDomDocument($doc, $filename);
	}
	return true;
}

/**
 * Add WordPress rewrite rule to the IIS 7+ configuration file.
 *
 * @since 2.8.0
 *
 * @param string $filename The file path to the configuration file
 * @param string $rewrite_rule The XML fragment with URL Rewrite rule
 * @return bool
 */
function iis7_add_rewrite_rule($filename, $rewrite_rule) {
	if ( ! class_exists( 'DOMDocument', false ) ) {
		return false;
	}

	// If configuration file does not exist then we create one.
	if ( ! file_exists($filename) ) {
		$fp = fopen( $filename, 'w');
		fwrite($fp, '<configuration/>');
		fclose($fp);
	}

	$doc = new DOMDocument();
	$doc->preserveWhiteSpace = false;

	if ( $doc->load($filename) === false )
		return false;

	$xpath = new DOMXPath($doc);

	// First check if the rule already exists as in that case there is no need to re-add it
	$wordpress_rules = $xpath->query('/configuration/system.webServer/rewrite/rules/rule[starts-with(@name,\'wordpress\')] | /configuration/system.webServer/rewrite/rules/rule[starts-with(@name,\'WordPress\')]');
	if ( $wordpress_rules->length > 0 )
		return true;

	// Check the XPath to the rewrite rule and create XML nodes if they do not exist
	$xmlnodes = $xpath->query('/configuration/system.webServer/rewrite/rules');
	if ( $xmlnodes->length > 0 ) {
		$rules_node = $xmlnodes->item(0);
	} else {
		$rules_node = $doc->createElement('rules');

		$xmlnodes = $xpath->query('/configuration/system.webServer/rewrite');
		if ( $xmlnodes->length > 0 ) {
			$rewrite_node = $xmlnodes->item(0);
			$rewrite_node->appendChild($rules_node);
		} else {
			$rewrite_node = $doc->createElement('rewrite');
			$rewrite_node->appendChild($rules_node);

			$xmlnodes = $xpath->query('/configuration/system.webServer');
			if ( $xmlnodes->length > 0 ) {
				$system_webServer_node = $xmlnodes->item(0);
				$system_webServer_node->appendChild($rewrite_node);
			} else {
				$system_webServer_node = $doc->createElement('system.webServer');
				$system_webServer_node->appendChild($rewrite_node);

				$xmlnodes = $xpath->query('/configuration');
				if ( $xmlnodes->length > 0 ) {
					$config_node = $xmlnodes->item(0);
					$config_node->appendChild($system_webServer_node);
				} else {
					$config_node = $doc->createElement('configuration');
					$doc->appendChild($config_node);
					$config_node->appendChild($system_webServer_node);
				}
			}
		}
	}

	$rule_fragment = $doc->createDocumentFragment();
	$rule_fragment->appendXML($rewrite_rule);
	$rules_node->appendChild($rule_fragment);

	$doc->encoding = "UTF-8";
	$doc->formatOutput = true;
	saveDomDocument($doc, $filename);

	return true;
}

/**
 * Saves the XML document into a file
 *
 * @since 2.8.0
 *
 * @param DOMDocument $doc
 * @param string $filename
 */
function saveDomDocument($doc, $filename) {
	$config = $doc->saveXML();
	$config = preg_replace("/([^\r])\n/", "$1\r\n", $config);
	$fp = fopen($filename, 'w');
	fwrite($fp, $config);
	fclose($fp);
}

/**
 * Display the default admin color scheme picker (Used in user-edit.php)
 *
 * @since 3.0.0
 *
 * @global array $_wp_admin_css_colors
 *
 * @param int $user_id User ID.
 */
function admin_color_scheme_picker( $user_id ) {
	global $_wp_admin_css_colors;

	ksort( $_wp_admin_css_colors );

	if ( isset( $_wp_admin_css_colors['fresh'] ) ) {
		// Set Default ('fresh') and Light should go first.
		$_wp_admin_css_colors = array_filter( array_merge( array( 'fresh' => '', 'light' => '' ), $_wp_admin_css_colors ) );
	}

	$current_color = get_user_option( 'admin_color', $user_id );

	if ( empty( $current_color ) || ! isset( $_wp_admin_css_colors[ $current_color ] ) ) {
		$current_color = 'fresh';
	}

	?>
	<fieldset id="color-picker" class="scheme-list">
		<legend class="screen-reader-text"><span><?php _e( 'Admin Color Scheme' ); ?></span></legend>
		<?php
		wp_nonce_field( 'save-color-scheme', 'color-nonce', false );
		foreach ( $_wp_admin_css_colors as $color => $color_info ) :

			?>
			<div class="color-option <?php echo ( $color == $current_color ) ? 'selected' : ''; ?>">
				<input name="admin_color" id="admin_color_<?php echo esc_attr( $color ); ?>" type="radio" value="<?php echo esc_attr( $color ); ?>" class="tog" <?php checked( $color, $current_color ); ?> />
				<input type="hidden" class="css_url" value="<?php echo esc_url( $color_info->url ); ?>" />
				<input type="hidden" class="icon_colors" value="<?php echo esc_attr( wp_json_encode( array( 'icons' => $color_info->icon_colors ) ) ); ?>" />
				<label for="admin_color_<?php echo esc_attr( $color ); ?>"><?php echo esc_html( $color_info->name ); ?></label>
				<table class="color-palette">
					<tr>
					<?php

					foreach ( $color_info->colors as $html_color ) {
						?>
						<td style="background-color: <?php echo esc_attr( $html_color ); ?>">&nbsp;</td>
						<?php
					}

					?>
					</tr>
				</table>
			</div>
			<?php

		endforeach;

	?>
	</fieldset>
	<?php
}

/**
 *
 * @global array $_wp_admin_css_colors
 */
function wp_color_scheme_settings() {
	global $_wp_admin_css_colors;

	$color_scheme = get_user_option( 'admin_color' );

	// It's possible to have a color scheme set that is no longer registered.
	if ( empty( $_wp_admin_css_colors[ $color_scheme ] ) ) {
		$color_scheme = 'fresh';
	}

	if ( ! empty( $_wp_admin_css_colors[ $color_scheme ]->icon_colors ) ) {
		$icon_colors = $_wp_admin_css_colors[ $color_scheme ]->icon_colors;
	} elseif ( ! empty( $_wp_admin_css_colors['fresh']->icon_colors ) ) {
		$icon_colors = $_wp_admin_css_colors['fresh']->icon_colors;
	} else {
		// Fall back to the default set of icon colors if the default scheme is missing.
		$icon_colors = array( 'base' => '#82878c', 'focus' => '#00a0d2', 'current' => '#fff' );
	}

	echo '<script type="text/javascript">var _wpColorScheme = ' . wp_json_encode( array( 'icons' => $icon_colors ) ) . ";</script>\n";
}

/**
 * @since 3.3.0
 */
function _ipad_meta() {
	if ( wp_is_mobile() ) {
		?>
		<meta name="viewport" id="viewport-meta" content="width=device-width, initial-scale=1">
		<?php
	}
}

/**
 * Check lock status for posts displayed on the Posts screen
 *
 * @since 3.6.0
 *
 * @param array  $response  The Heartbeat response.
 * @param array  $data      The $_POST data sent.
 * @param string $screen_id The screen id.
 * @return array The Heartbeat response.
 */
function wp_check_locked_posts( $response, $data, $screen_id ) {
	$checked = array();

	if ( array_key_exists( 'wp-check-locked-posts', $data ) && is_array( $data['wp-check-locked-posts'] ) ) {
		foreach ( $data['wp-check-locked-posts'] as $key ) {
			if ( ! $post_id = absint( substr( $key, 5 ) ) )
				continue;

			if ( ( $user_id = wp_check_post_lock( $post_id ) ) && ( $user = get_userdata( $user_id ) ) && current_user_can( 'edit_post', $post_id ) ) {
				$send = array( 'text' => sprintf( __( '%s is currently editing' ), $user->display_name ) );

				if ( ( $avatar = get_avatar( $user->ID, 18 ) ) && preg_match( "|src='([^']+)'|", $avatar, $matches ) )
					$send['avatar_src'] = $matches[1];

				$checked[$key] = $send;
			}
		}
	}

	if ( ! empty( $checked ) )
		$response['wp-check-locked-posts'] = $checked;

	return $response;
}

/**
 * Check lock status on the New/Edit Post screen and refresh the lock
 *
 * @since 3.6.0
 *
 * @param array  $response  The Heartbeat response.
 * @param array  $data      The $_POST data sent.
 * @param string $screen_id The screen id.
 * @return array The Heartbeat response.
 */
function wp_refresh_post_lock( $response, $data, $screen_id ) {
	if ( array_key_exists( 'wp-refresh-post-lock', $data ) ) {
		$received = $data['wp-refresh-post-lock'];
		$send = array();

		if ( ! $post_id = absint( $received['post_id'] ) )
			return $response;

		if ( ! current_user_can('edit_post', $post_id) )
			return $response;

		if ( ( $user_id = wp_check_post_lock( $post_id ) ) && ( $user = get_userdata( $user_id ) ) ) {
			$error = array(
				'text' => sprintf( __( '%s has taken over and is currently editing.' ), $user->display_name )
			);

			if ( $avatar = get_avatar( $user->ID, 64 ) ) {
				if ( preg_match( "|src='([^']+)'|", $avatar, $matches ) )
					$error['avatar_src'] = $matches[1];
			}

			$send['lock_error'] = $error;
		} else {
			if ( $new_lock = wp_set_post_lock( $post_id ) )
				$send['new_lock'] = implode( ':', $new_lock );
		}

		$response['wp-refresh-post-lock'] = $send;
	}

	return $response;
}

/**
 * Check nonce expiration on the New/Edit Post screen and refresh if needed
 *
 * @since 3.6.0
 *
 * @param array  $response  The Heartbeat response.
 * @param array  $data      The $_POST data sent.
 * @param string $screen_id The screen id.
 * @return array The Heartbeat response.
 */
function wp_refresh_post_nonces( $response, $data, $screen_id ) {
	if ( array_key_exists( 'wp-refresh-post-nonces', $data ) ) {
		$received = $data['wp-refresh-post-nonces'];
		$response['wp-refresh-post-nonces'] = array( 'check' => 1 );

		if ( ! $post_id = absint( $received['post_id'] ) ) {
			return $response;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $response;
		}

		$response['wp-refresh-post-nonces'] = array(
			'replace' => array(
				'getpermalinknonce' => wp_create_nonce('getpermalink'),
				'samplepermalinknonce' => wp_create_nonce('samplepermalink'),
				'closedpostboxesnonce' => wp_create_nonce('closedpostboxes'),
				'_ajax_linking_nonce' => wp_create_nonce( 'internal-linking' ),
				'_wpnonce' => wp_create_nonce( 'update-post_' . $post_id ),
			),
			'heartbeatNonce' => wp_create_nonce( 'heartbeat-nonce' ),
		);
	}

	return $response;
}

/**
 * Disable suspension of Heartbeat on the Add/Edit Post screens.
 *
 * @since 3.8.0
 *
 * @global string $pagenow
 *
 * @param array $settings An array of Heartbeat settings.
 * @return array Filtered Heartbeat settings.
 */
function wp_heartbeat_set_suspension( $settings ) {
	global $pagenow;

	if ( 'post.php' === $pagenow || 'post-new.php' === $pagenow ) {
		$settings['suspension'] = 'disable';
	}

	return $settings;
}

/**
 * Autosave with heartbeat
 *
 * @since 3.9.0
 *
 * @param array $response The Heartbeat response.
 * @param array $data     The $_POST data sent.
 * @return array The Heartbeat response.
 */
function heartbeat_autosave( $response, $data ) {
	if ( ! empty( $data['wp_autosave'] ) ) {
		$saved = wp_autosave( $data['wp_autosave'] );

		if ( is_wp_error( $saved ) ) {
			$response['wp_autosave'] = array( 'success' => false, 'message' => $saved->get_error_message() );
		} elseif ( empty( $saved ) ) {
			$response['wp_autosave'] = array( 'success' => false, 'message' => __( 'Error while saving.' ) );
		} else {
			/* translators: draft saved date format, see https://secure.php.net/date */
			$draft_saved_date_format = __( 'g:i:s a' );
			/* translators: %s: date and time */
			$response['wp_autosave'] = array( 'success' => true, 'message' => sprintf( __( 'Draft saved at %s.' ), date_i18n( $draft_saved_date_format ) ) );
		}
	}

	return $response;
}

/**
 * Remove single-use URL parameters and create canonical link based on new URL.
 *
 * Remove specific query string parameters from a URL, create the canonical link,
 * put it in the admin header, and change the current URL to match.
 *
 * @since 4.2.0
 */
function wp_admin_canonical_url() {
	$removable_query_args = wp_removable_query_args();

	if ( empty( $removable_query_args ) ) {
		return;
	}

	// Ensure we're using an absolute URL.
	$current_url  = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
	$filtered_url = remove_query_arg( $removable_query_args, $current_url );
	?>
	<link id="wp-admin-canonical" rel="canonical" href="<?php echo esc_url( $filtered_url ); ?>" />
	<script>
		if ( window.history.replaceState ) {
			window.history.replaceState( null, null, document.getElementById( 'wp-admin-canonical' ).href + window.location.hash );
		}
	</script>
<?php
}

/**
 * Send a referrer policy header so referrers are not sent externally from administration screens.
 *
 * @since 4.9.0
 */
function wp_admin_headers() {
	$policy = 'strict-origin-when-cross-origin';

	/**
	 * Filters the admin referrer policy header value.
	 *
	 * @since 4.9.0
	 * @since 4.9.5 The default value was changed to 'strict-origin-when-cross-origin'.
	 *
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Referrer-Policy
	 *
	 * @param string $policy The admin referrer policy header value. Default 'strict-origin-when-cross-origin'.
	 */
	$policy = apply_filters( 'admin_referrer_policy', $policy );

	header( sprintf( 'Referrer-Policy: %s', $policy ) );
}

/**
 * Outputs JS that reloads the page if the user navigated to it with the Back or Forward button.
 *
 * Used on the Edit Post and Add New Post screens. Needed to ensure the page is not loaded from browser cache,
 * so the post title and editor content are the last saved versions. Ideally this script should run first in the head.
 *
 * @since 4.6.0
 */
function wp_page_reload_on_back_button_js() {
	?>
	<script>
		if ( typeof performance !== 'undefined' && performance.navigation && performance.navigation.type === 2 ) {
			document.location.reload( true );
		}
	</script>
	<?php
}

/**
 * Send a confirmation request email when a change of site admin email address is attempted.
 *
 * The new site admin address will not become active until confirmed.
 *
 * @since 3.0.0
 * @since 4.9.0 This function was moved from wp-admin/includes/ms.php so it's no longer Multisite specific.
 *
 * @param string $old_value The old site admin email address.
 * @param string $value     The proposed new site admin email address.
 */
function update_option_new_admin_email( $old_value, $value ) {
	if ( $value == get_option( 'admin_email' ) || ! is_email( $value ) ) {
		return;
	}

	$hash = md5( $value . time() . wp_rand() );
	$new_admin_email = array(
		'hash'     => $hash,
		'newemail' => $value,
	);
	update_option( 'adminhash', $new_admin_email );

	$switched_locale = switch_to_locale( get_user_locale() );

	/* translators: Do not translate USERNAME, ADMIN_URL, EMAIL, SITENAME, SITEURL: those are placeholders. */
	$email_text = __( 'Howdy ###USERNAME###,

You recently requested to have the administration email address on
your site changed.

If this is correct, please click on the following link to change it:
###ADMIN_URL###

You can safely ignore and delete this email if you do not want to
take this action.

This email has been sent to ###EMAIL###

Regards,
All at ###SITENAME###
###SITEURL###' );

	/**
	 * Filters the text of the email sent when a change of site admin email address is attempted.
	 *
	 * The following strings have a special meaning and will get replaced dynamically:
	 * ###USERNAME###  The current user's username.
	 * ###ADMIN_URL### The link to click on to confirm the email change.
	 * ###EMAIL###     The proposed new site admin email address.
	 * ###SITENAME###  The name of the site.
	 * ###SITEURL###   The URL to the site.
	 *
	 * @since MU (3.0.0)
	 * @since 4.9.0 This filter is no longer Multisite specific.
	 *
	 * @param string $email_text      Text in the email.
	 * @param array  $new_admin_email {
	 *     Data relating to the new site admin email address.
	 *
	 *     @type string $hash     The secure hash used in the confirmation link URL.
	 *     @type string $newemail The proposed new site admin email address.
	 * }
	 */
	$content = apply_filters( 'new_admin_email_content', $email_text, $new_admin_email );

	$current_user = wp_get_current_user();
	$content = str_replace( '###USERNAME###', $current_user->user_login, $content );
	$content = str_replace( '###ADMIN_URL###', esc_url( self_admin_url( 'options.php?adminhash=' . $hash ) ), $content );
	$content = str_replace( '###EMAIL###', $value, $content );
	$content = str_replace( '###SITENAME###', wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ), $content );
	$content = str_replace( '###SITEURL###', home_url(), $content );

	wp_mail( $value, sprintf( __( '[%s] New Admin Email Address' ), wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) ), $content );

	if ( $switched_locale ) {
		restore_previous_locale();
	}
}

/**
 * Appends '(Draft)' to draft page titles in the privacy page dropdown
 * so that unpublished content is obvious.
 *
 * @since 4.9.8
 * @access private
 *
 * @param string  $title Page title.
 * @param WP_Post $page  Page data object.
 *
 * @return string Page title.
 */
function _wp_privacy_settings_filter_draft_page_titles( $title, $page ) {
	if ( 'draft' === $page->post_status && 'privacy' === get_current_screen()->id ) {
		/* translators: %s: Page Title */
		$title = sprintf( __( '%s (Draft)' ), $title );
	}

	return $title;
}

/**
 * WP_Privacy_Policy_Content class.
 * TODO: move this to a new file.
 *
 * @since 4.9.6
 */
final class WP_Privacy_Policy_Content {

	private static $policy_content = array();

	/**
	 * Constructor
	 *
	 * @since 4.9.6
	 */
	private function __construct() {}

	/**
	 * Add content to the postbox shown when editing the privacy policy.
	 *
	 * Plugins and themes should suggest text for inclusion in the site's privacy policy.
	 * The suggested text should contain information about any functionality that affects user privacy,
	 * and will be shown in the Suggested Privacy Policy Content postbox.
	 *
	 * Intended for use from `wp_add_privacy_policy_content()`.
	 *
	 * @since 4.9.6
	 *
	 * @param string $plugin_name The name of the plugin or theme that is suggesting content for the site's privacy policy.
	 * @param string $policy_text The suggested content for inclusion in the policy.
	 */
	public static function add( $plugin_name, $policy_text ) {
		if ( empty( $plugin_name ) || empty( $policy_text ) ) {
			return;
		}

		$data = array(
			'plugin_name' => $plugin_name,
			'policy_text' => $policy_text,
		);

		if ( ! in_array( $data, self::$policy_content, true ) ) {
			self::$policy_content[] = $data;
		}
	}

	/**
	 * Quick check if any privacy info has changed.
	 *
	 * @since 4.9.6
	 */
	public static function text_change_check() {

		$policy_page_id = (int) get_option( 'wp_page_for_privacy_policy' );

		// The site doesn't have a privacy policy.
		if ( empty( $policy_page_id ) ) {
			return false;
		}

		if ( ! current_user_can( 'edit_post', $policy_page_id ) ) {
			return false;
		}

		$old = (array) get_post_meta( $policy_page_id, '_wp_suggested_privacy_policy_content' );

		// Updates are not relevant if the user has not reviewed any suggestions yet.
		if ( empty( $old ) ) {
			return false;
		}

		$cached = get_option( '_wp_suggested_policy_text_has_changed' );

		/*
		 * When this function is called before `admin_init`, `self::$policy_content`
		 * has not been populated yet, so use the cached result from the last
		 * execution instead.
		 */
		if ( ! did_action( 'admin_init' ) ) {
			return 'changed' === $cached;
		}

		$new = self::$policy_content;

		// Remove the extra values added to the meta.
		foreach ( $old as $key => $data ) {
			if ( ! empty( $data['removed'] ) ) {
				unset( $old[ $key ] );
				continue;
			}

			$old[ $key ] = array(
				'plugin_name' => $data['plugin_name'],
				'policy_text' => $data['policy_text'],
			);
		}

		// Normalize the order of texts, to facilitate comparison.
		sort( $old );
		sort( $new );

		// The == operator (equal, not identical) was used intentionally.
		// See http://php.net/manual/en/language.operators.array.php
		if ( $new != $old ) {
			// A plugin was activated or deactivated, or some policy text has changed.
			// Show a notice on the relevant screens to inform the admin.
			add_action( 'admin_notices', array( 'WP_Privacy_Policy_Content', 'policy_text_changed_notice' ) );
			$state = 'changed';
		} else {
			$state = 'not-changed';
		}

		// Cache the result for use before `admin_init` (see above).
		if ( $cached !== $state ) {
			update_option( '_wp_suggested_policy_text_has_changed', $state );
		}

		return 'changed' === $state;
	}

	/**
	 * Output a warning when some privacy info has changed.
	 *
	 * @since 4.9.6
	 */
	public static function policy_text_changed_notice() {
		global $post;

		$screen = get_current_screen()->id;

		if ( 'privacy' !== $screen ) {
			return;
		}

		?>
		<div class="policy-text-updated notice notice-warning is-dismissible">
			<p><?php
				printf(
					/* translators: %s: Privacy Policy Guide URL */
					__( 'The suggested privacy policy text has changed. Please <a href="%s">review the guide</a> and update your privacy policy.' ),
					esc_url( admin_url( 'tools.php?wp-privacy-policy-guide=1' ) )
				);
			?></p>
		</div>
		<?php
	}

	/**
	 * Update the cached policy info when the policy page is updated.
	 *
	 * @since 4.9.6
	 * @access private
	 */
	public static function _policy_page_updated( $post_id ) {
		$policy_page_id = (int) get_option( 'wp_page_for_privacy_policy' );

		if ( ! $policy_page_id || $policy_page_id !== (int) $post_id ) {
			return;
		}

		// Remove updated|removed status.
		$old = (array) get_post_meta( $policy_page_id, '_wp_suggested_privacy_policy_content' );
		$done = array();
		$update_cache = false;

		foreach ( $old as $old_key => $old_data ) {
			if ( ! empty( $old_data['removed'] ) ) {
				// Remove the old policy text.
				$update_cache = true;
				continue;
			}

			if ( ! empty( $old_data['updated'] ) ) {
				// 'updated' is now 'added'.
				$done[] = array(
					'plugin_name' => $old_data['plugin_name'],
					'policy_text' => $old_data['policy_text'],
					'added'       => $old_data['updated'],
				);
				$update_cache = true;
			} else {
				$done[] = $old_data;
			}
		}

		if ( $update_cache ) {
			delete_post_meta( $policy_page_id, '_wp_suggested_privacy_policy_content' );
			// Update the cache.
			foreach ( $done as $data ) {
				add_post_meta( $policy_page_id, '_wp_suggested_privacy_policy_content', $data );
			}
		}
	}

	/**
	 * Check for updated, added or removed privacy policy information from plugins.
	 *
	 * Caches the current info in post_meta of the policy page.
	 *
	 * @since 4.9.6
	 *
	 * @return array The privacy policy text/informtion added by core and plugins.
	 */
	public static function get_suggested_policy_text() {
		$policy_page_id = (int) get_option( 'wp_page_for_privacy_policy' );
		$checked = array();
		$time = time();
		$update_cache = false;
		$new = self::$policy_content;
		$old = array();

		if ( $policy_page_id ) {
			$old = (array) get_post_meta( $policy_page_id, '_wp_suggested_privacy_policy_content' );
		}

		// Check for no-changes and updates.
		foreach ( $new as $new_key => $new_data ) {
			foreach ( $old as $old_key => $old_data ) {
				$found = false;

				if ( $new_data['policy_text'] === $old_data['policy_text'] ) {
					// Use the new plugin name in case it was changed, translated, etc.
					if ( $old_data['plugin_name'] !== $new_data['plugin_name'] ) {
						$old_data['plugin_name'] = $new_data['plugin_name'];
						$update_cache = true;
					}

					// A plugin was re-activated.
					if ( ! empty( $old_data['removed'] ) ) {
						unset( $old_data['removed'] );
						$old_data['added'] = $time;
						$update_cache = true;
					}

					$checked[] = $old_data;
					$found = true;
				} elseif ( $new_data['plugin_name'] === $old_data['plugin_name'] ) {
					// The info for the policy was updated.
					$checked[] = array(
						'plugin_name' => $new_data['plugin_name'],
						'policy_text' => $new_data['policy_text'],
						'updated'     => $time,
					);
					$found = $update_cache = true;
				}

				if ( $found ) {
					unset( $new[ $new_key ], $old[ $old_key ] );
					continue 2;
				}
			}
		}

		if ( ! empty( $new ) ) {
			// A plugin was activated.
			foreach ( $new as $new_data ) {
				if ( ! empty( $new_data['plugin_name'] ) && ! empty( $new_data['policy_text'] ) ) {
					$new_data['added'] = $time;
					$checked[]         = $new_data;
				}
			}
			$update_cache = true;
		}

		if ( ! empty( $old ) ) {
			// A plugin was deactivated.
			foreach ( $old as $old_data ) {
				if ( ! empty( $old_data['plugin_name'] ) && ! empty( $old_data['policy_text'] ) ) {
					$data = array(
						'plugin_name' => $old_data['plugin_name'],
						'policy_text' => $old_data['policy_text'],
						'removed'     => $time,
					);

					$checked[] = $data;
				}
			}
			$update_cache = true;
		}

		if ( $update_cache && $policy_page_id ) {
			delete_post_meta( $policy_page_id, '_wp_suggested_privacy_policy_content' );
			// Update the cache.
			foreach ( $checked as $data ) {
				add_post_meta( $policy_page_id, '_wp_suggested_privacy_policy_content', $data );
			}
		}

		return $checked;
	}

	/**
	 * Add a notice with a link to the guide when editing the privacy policy page.
	 *
	 * @since 4.9.6
	 *
	 * @param WP_Post $post The currently edited post.
	 */
	public static function notice( $post ) {
		if ( ! ( $post instanceof WP_Post ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_privacy_options' ) ) {
			return;
		}

		$policy_page_id = (int) get_option( 'wp_page_for_privacy_policy' );

		if ( ! $policy_page_id || $policy_page_id != $post->ID ) {
			return;
		}

		?>
		<div class="notice notice-warning inline wp-pp-notice">
			<p>
			<?php
			/* translators: 1: Privacy Policy guide URL, 2: additional link attributes, 3: accessibility text */
			printf(
				__( 'Need help putting together your new Privacy Policy page? <a href="%1$s" %2$s>Check out our guide%3$s</a> for recommendations on what content to include, along with policies suggested by your plugins and theme.' ),
				admin_url( 'tools.php?wp-privacy-policy-guide=1' ),
				'target="_blank"',
				sprintf(
					'<span class="screen-reader-text"> %s</span>',
					/* translators: accessibility text */
					__( '(opens in a new tab)' )
				)
			);
			?>
			</p>
		</div>
		<?php

	}

	/**
	 * Output the privacy policy guide together with content from the theme and plugins.
	 *
	 * @since 4.9.6
	 */
	public static function privacy_policy_guide() {

		$content_array = self::get_suggested_policy_text();

		$content = '';
		$toc = array( '<li><a href="#wp-privacy-policy-guide-introduction">' . __( 'Introduction' ) . '</a></li>' );
		$date_format = __( 'F j, Y' );
		$copy = __( 'Copy' );
		$return_to_top = '<a href="#" class="return-to-top">' . __( '&uarr; Return to Top' ) . '</a>';

		foreach ( $content_array as $section ) {
			$class = $meta = $removed = '';

			if ( ! empty( $section['removed'] ) ) {
				$class = ' text-removed';
				$date = date_i18n( $date_format, $section['removed'] );
				$meta  = sprintf( __( 'Removed %s.' ), $date );

				$removed = __( 'You deactivated this plugin on %s and may no longer need this policy.' );
				$removed = '<div class="error inline"><p>' . sprintf( $removed, $date ) . '</p></div>';
			} elseif ( ! empty( $section['updated'] ) ) {
				$class = ' text-updated';
				$date = date_i18n( $date_format, $section['updated'] );
				$meta  = sprintf( __( 'Updated %s.' ), $date );
			}

			if ( $meta ) {
				$meta = '<br><span class="privacy-text-meta">' . $meta . '</span>';
			}

			$plugin_name = esc_html( $section['plugin_name'] );
			$toc_id = 'wp-privacy-policy-guide-' . sanitize_title( $plugin_name );
			$toc[] = sprintf( '<li><a href="#%1$s">%2$s</a>' . $meta . '</li>', $toc_id, $plugin_name );

			$content .= '<div class="privacy-text-section' . $class . '">';
			$content .= '<a id="' . $toc_id . '">&nbsp;</a>';
			/* translators: %s: plugin name */
			$content .= '<h2>' . sprintf( __( 'Source: %s' ), $plugin_name ) . '</h2>';
			$content .= $removed;

			$content .= '<div class="policy-text">' . $section['policy_text'] . '</div>';
			$content .= $return_to_top;

			if ( empty( $section['removed'] ) ) {
				$content .= '<div class="privacy-text-actions">';
					$content .= '<button type="button" class="privacy-text-copy button">';
						$content .= $copy;
						$content .= '<span class="screen-reader-text">' . sprintf( __( 'Copy suggested policy text from %s.' ), $plugin_name ) . '</span>';
					$content .= '</button>';
				$content .= '</div>';
			}

			$content .= "</div>\n"; // End of .privacy-text-section.
		}

		if ( count( $toc ) > 2 ) {
			?>
			<div  class="privacy-text-box-toc">
				<p><?php _e( 'Table of Contents' ); ?></p>
				<ol>
					<?php echo implode( "\n", $toc ); ?>
				</ol>
			</div>
			<?php
		}

		?>
		<div class="privacy-text-box">
			<div class="privacy-text-box-head">
				<a id="wp-privacy-policy-guide-introduction">&nbsp;</a>
				<h2><?php _e( 'Introduction' ); ?></h2>
				<p><?php _e( 'Hello,' ); ?></p>
				<p><?php _e( 'This text template will help you to create your web site&#8217;s privacy policy.' ); ?></p>
				<p><?php _e( 'We have suggested the sections you will need. Under each section heading you will find a short summary of what information you should provide, which will help you to get started. Some sections include suggested policy content, others will have to be completed with information from your theme and plugins.' ); ?></p>
				<p><?php _e( 'Please edit your privacy policy content, making sure to delete the summaries, and adding any information from your theme and plugins. Once you publish your policy page, remember to add it to your navigation menu.' ); ?></p>
				<p><?php _e( 'It is your responsibility to write a comprehensive privacy policy, to make sure it reflects all national and international legal requirements on privacy, and to keep your policy current and accurate.' ); ?></p>
			</div>

			<div class="privacy-text-box-body">
				<?php echo $content; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Return the default suggested privacy policy content.
	 *
	 * @since 4.9.6
	 *
	 * @param bool $descr Whether to include the descriptions under the section headings. Default false.
	 * @return string The default policy content.
	 */
	public static function get_default_content( $descr = false ) {
		$suggested_text = $descr ? '<strong class="privacy-policy-tutorial">' . __( 'Suggested text:' ) . ' </strong>' : '';
		$content = '';

		// Start of the suggested privacy policy text.
		$descr && $content .=
			'<div class="wp-suggested-text">';
		$content .=
			'<h2>' . __( 'Who we are' ) . '</h2>';
		$descr && $content .=
			'<p class="privacy-policy-tutorial">' . __( 'In this section you should note your site URL, as well as the name of the company, organization, or individual behind it, and some accurate contact information.' ) . '</p>' .
			'<p class="privacy-policy-tutorial">' . __( 'The amount of information you may be required to show will vary depending on your local or national business regulations. You may, for example, be required to display a physical address, a registered address, or your company registration number.' ) . '</p>';
		$content .=
			/* translators: %s Site URL */
			'<p>' . $suggested_text . sprintf( __( 'Our website address is: %s.' ), get_bloginfo( 'url', 'display' ) ) . '</p>' .

			'<h2>' . __( 'What personal data we collect and why we collect it' ) . '</h2>';
		$descr && $content .=
			'<p class="privacy-policy-tutorial">' . __( 'In this section you should note what personal data you collect from users and site visitors. This may include personal data, such as name, email address, personal account preferences; transactional data, such as purchase information; and technical data, such as information about cookies.' ) . '</p>' .
			'<p class="privacy-policy-tutorial">' . __( 'You should also note any collection and retention of sensitive personal data, such as data concerning health.' ) . '</p>' .
			'<p class="privacy-policy-tutorial">' . __( 'In addition to listing what personal data you collect, you need to note why you collect it. These explanations must note either the legal basis for your data collection and retention or the active consent the user has given.' ) . '</p>' .
			'<p class="privacy-policy-tutorial">' . __( 'Personal data is not just created by a user&#8217;s interactions with your site. Personal data is also generated from technical processes such as contact forms, comments, cookies, analytics, and third party embeds.' ) . '</p>' .
			'<p class="privacy-policy-tutorial">' . __( 'By default WordPress does not collect any personal data about visitors, and only collects the data shown on the User Profile screen from registered users. However some of your plugins may collect personal data. You should add the relevant information below.' ) . '</p>';

		$content .=
			'<h3>' . __( 'Comments' ) . '</h3>';
		$descr && $content .=
			'<p class="privacy-policy-tutorial">' . __( 'In this subsection you should note what information is captured through comments. We have noted the data which WordPress collects by default.' ) . '</p>';
		$content .=
			'<p>' . $suggested_text . __( 'When visitors leave comments on the site we collect the data shown in the comments form, and also the visitor&#8217;s IP address and browser user agent string to help spam detection.' ) . '</p>' .
			'<p>' . __( 'An anonymized string created from your email address (also called a hash) may be provided to the Gravatar service to see if you are using it. The Gravatar service privacy policy is available here: https://automattic.com/privacy/. After approval of your comment, your profile picture is visible to the public in the context of your comment.' ) . '</p>' .

			'<h3>' . __( 'Media' ) . '</h3>';
		$descr && $content .=
			'<p class="privacy-policy-tutorial">' . __( 'In this subsection you should note what information may be disclosed by users who can upload media files. All uploaded files are usually publicly accessible.' ) . '</p>';
		$content .=
			'<p>' . $suggested_text . __( 'If you upload images to the website, you should avoid uploading images with embedded location data (EXIF GPS) included. Visitors to the website can download and extract any location data from images on the website.' ) . '</p>' .

			'<h3>' . __( 'Contact forms' ) . '</h3>';
		$descr && $content .=
			'<p class="privacy-policy-tutorial">' . __( 'By default, WordPress does not include a contact form. If you use a contact form plugin, use this subsection to note what personal data is captured when someone submits a contact form, and how long you keep it. For example, you may note that you keep contact form submissions for a certain period for customer service purposes, but you do not use the information submitted through them for marketing purposes.' ) . '</p>';

		$content .=
			'<h3>' . __( 'Cookies' ) . '</h3>';
		$descr && $content .=
			'<p class="privacy-policy-tutorial">' . __( 'In this subsection you should list the cookies your web site uses, including those set by your plugins, social media, and analytics. We have provided the cookies which WordPress installs by default.' ) . '</p>';
		$content .=
			'<p>' . $suggested_text . __( 'If you leave a comment on our site you may opt-in to saving your name, email address and website in cookies. These are for your convenience so that you do not have to fill in your details again when you leave another comment. These cookies will last for one year.' ) . '</p>' .
			'<p>' . __( 'If you have an account and you log in to this site, we will set a temporary cookie to determine if your browser accepts cookies. This cookie contains no personal data and is discarded when you close your browser.' ) . '</p>' .
			'<p>' . __( 'When you log in, we will also set up several cookies to save your login information and your screen display choices. Login cookies last for two days, and screen options cookies last for a year. If you select &quot;Remember Me&quot;, your login will persist for two weeks. If you log out of your account, the login cookies will be removed.' ) . '</p>' .
			'<p>' . __( 'If you edit or publish an article, an additional cookie will be saved in your browser. This cookie includes no personal data and simply indicates the post ID of the article you just edited. It expires after 1 day.' ) . '</p>' .

			'<h3>' . __( 'Embedded content from other websites' ) . '</h3>' .
			'<p>' . $suggested_text . __( 'Articles on this site may include embedded content (e.g. videos, images, articles, etc.). Embedded content from other websites behaves in the exact same way as if the visitor has visited the other website.' ) . '</p>' .
			'<p>' . __( 'These websites may collect data about you, use cookies, embed additional third-party tracking, and monitor your interaction with that embedded content, including tracking your interaction with the embedded content if you have an account and are logged in to that website.' ) . '</p>' .

			'<h3>' . __( 'Analytics' ) . '</h3>';
		$descr && $content .=
			'<p class="privacy-policy-tutorial">' . __( 'In this subsection you should note what analytics package you use, how users can opt out of analytics tracking, and a link to your analytics provider&#8217;s privacy policy, if any.' ) . '</p>' .
			'<p class="privacy-policy-tutorial">' . __( 'By default WordPress does not collect any analytics data. However, many web hosting accounts collect some anonymous analytics data. You may also have installed a WordPress plugin that provides analytics services. In that case, add information from that plugin here.' ) . '</p>';

		$content .=
			'<h2>' . __( 'Who we share your data with' ) . '</h2>';
		$descr && $content .=
			'<p class="privacy-policy-tutorial">' . __( 'In this section you should name and list all third party providers with whom you share site data, including partners, cloud-based services, payment processors, and third party service providers, and note what data you share with them and why. Link to their own privacy policies if possible.' ) . '</p>' .
			'<p class="privacy-policy-tutorial">' . __( 'By default WordPress does not share any personal data with anyone.' ) . '</p>';

		$content .=
			'<h2>' . __( 'How long we retain your data' ) . '</h2>';
		$descr && $content .=
			'<p class="privacy-policy-tutorial">' . __( 'In this section you should explain how long you retain personal data collected or processed by the web site. While it is your responsibility to come up with the schedule of how long you keep each dataset for and why you keep it, that information does need to be listed here. For example, you may want to say that you keep contact form entries for six months, analytics records for a year, and customer purchase records for ten years.' ) . '</p>';
		$content .=
			'<p>' . $suggested_text . __( 'If you leave a comment, the comment and its metadata are retained indefinitely. This is so we can recognize and approve any follow-up comments automatically instead of holding them in a moderation queue.' ) . '</p>' .
			'<p>' . __( 'For users that register on our website (if any), we also store the personal information they provide in their user profile. All users can see, edit, or delete their personal information at any time (except they cannot change their username). Website administrators can also see and edit that information.' ) . '</p>' .

			'<h2>' . __( 'What rights you have over your data' ) . '</h2>';
		$descr && $content .=
			'<p class="privacy-policy-tutorial">' . __( 'In this section you should explain what rights your users have over their data and how they can invoke those rights.' ) . '</p>';
		$content .=
			'<p>' . $suggested_text . __( 'If you have an account on this site, or have left comments, you can request to receive an exported file of the personal data we hold about you, including any data you have provided to us. You can also request that we erase any personal data we hold about you. This does not include any data we are obliged to keep for administrative, legal, or security purposes.' ) . '</p>' .

			'<h2>' . __( 'Where we send your data' ) . '</h2>';
		$descr && $content .=
			'<p class="privacy-policy-tutorial">' . __( 'In this section you should list all transfers of your site data outside the European Union and describe the means by which that data is safeguarded to European data protection standards. This could include your web hosting, cloud storage, or other third party services.' ) . '</p>' .
			'<p class="privacy-policy-tutorial">' . __( 'European data protection law requires data about European residents which is transferred outside the European Union to be safeguarded to the same standards as if the data was in Europe. So in addition to listing where data goes, you should describe how you ensure that these standards are met either by yourself or by your third party providers, whether that is through an agreement such as Privacy Shield, model clauses in your contracts, or binding corporate rules.' ) . '</p>';
		$content .=
			'<p>' . $suggested_text . __( 'Visitor comments may be checked through an automated spam detection service.' ) . '</p>' .

			'<h2>' . __( 'Your contact information' ) . '</h2>';
		$descr && $content .=
			'<p class="privacy-policy-tutorial">' . __( 'In this section you should provide a contact method for privacy-specific concerns. If you are required to have a Data Protection Officer, list their name and full contact details here as well.' ) . '</p>';

		$content .=
			'<h2>' . __( 'Additional information' ) . '</h2>';
		$descr && $content .=
			'<p class="privacy-policy-tutorial">' . __( 'If you use your site for commercial purposes and you engage in more complex collection or processing of personal data, you should note the following information in your privacy policy in addition to the information we have already discussed.' ) . '</p>';

		$content .=
			'<h3>' . __( 'How we protect your data' ) . '</h3>';
		$descr && $content .=
			'<p class="privacy-policy-tutorial">' . __( 'In this section you should explain what measures you have taken to protect your users&#8217; data. This could include technical measures such as encryption; security measures such as two factor authentication; and measures such as staff training in data protection. If you have carried out a Privacy Impact Assessment, you can mention it here too.' ) . '</p>';

		$content .=
			'<h3>' . __( 'What data breach procedures we have in place' ) . '</h3>';
		$descr && $content .=
			'<p class="privacy-policy-tutorial">' . __( 'In this section you should explain what procedures you have in place to deal with data breaches, either potential or real, such as internal reporting systems, contact mechanisms, or bug bounties.' ) . '</p>';

		$content .=
			'<h3>' . __( 'What third parties we receive data from' ) . '</h3>';
		$descr && $content .=
			'<p class="privacy-policy-tutorial">' . __( 'If your web site receives data about users from third parties, including advertisers, this information must be included within the section of your privacy policy dealing with third party data.' ) . '</p>';

		$content .=
			'<h3>' . __( 'What automated decision making and/or profiling we do with user data' ) . '</h3>';
		$descr && $content .=
			'<p class="privacy-policy-tutorial">' . __( 'If your web site provides a service which includes automated decision making - for example, allowing customers to apply for credit, or aggregating their data into an advertising profile - you must note that this is taking place, and include information about how that information is used, what decisions are made with that aggregated data, and what rights users have over decisions made without human intervention.' ) . '</p>';

		$content .=
			'<h3>' . __( 'Industry regulatory disclosure requirements' ) . '</h3>';
		$descr && $content .=
			'<p class="privacy-policy-tutorial">' . __( 'If you are a member of a regulated industry, or if you are subject to additional privacy laws, you may be required to disclose that information here.' ) . '</p>' .
			'</div>';
		// End of the suggested privacy policy text.

		/**
		 * Filters the default content suggested for inclusion in a privacy policy.
		 *
		 * @since 4.9.6
		 *
		 * @param $content string The default policy content.
		 */
		return apply_filters( 'wp_get_default_privacy_policy_content', $content );
	}

	/**
	 * Add the suggested privacy policy text to the policy postbox.
	 *
	 * @since 4.9.6
	 */
	public static function add_suggested_content() {
		$content = self::get_default_content( true );
		wp_add_privacy_policy_content( __( 'WordPress' ), $content );
	}
}
