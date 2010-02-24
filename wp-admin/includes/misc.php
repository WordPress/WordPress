<?php
/**
 * Misc WordPress Administration API.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
function got_mod_rewrite() {
	$got_rewrite = apache_mod_loaded('mod_rewrite', true);
	return apply_filters('got_rewrite', $got_rewrite);
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $filename
 * @param unknown_type $marker
 * @return array An array of strings from a file (.htaccess ) from between BEGIN and END markers.
 */
function extract_from_markers( $filename, $marker ) {
	$result = array ();

	if (!file_exists( $filename ) ) {
		return $result;
	}

	if ( $markerdata = explode( "\n", implode( '', file( $filename ) ) ));
	{
		$state = false;
		foreach ( $markerdata as $markerline ) {
			if (strpos($markerline, '# END ' . $marker) !== false)
				$state = false;
			if ( $state )
				$result[] = $markerline;
			if (strpos($markerline, '# BEGIN ' . $marker) !== false)
				$state = true;
		}
	}

	return $result;
}

/**
 * {@internal Missing Short Description}}
 *
 * Inserts an array of strings into a file (.htaccess ), placing it between
 * BEGIN and END markers. Replaces existing marked info. Retains surrounding
 * data. Creates file if none exists.
 *
 * @since unknown
 *
 * @param unknown_type $filename
 * @param unknown_type $marker
 * @param unknown_type $insertion
 * @return bool True on write success, false on failure.
 */
function insert_with_markers( $filename, $marker, $insertion ) {
	if (!file_exists( $filename ) || is_writeable( $filename ) ) {
		if (!file_exists( $filename ) ) {
			$markerdata = '';
		} else {
			$markerdata = explode( "\n", implode( '', file( $filename ) ) );
		}

		if ( !$f = @fopen( $filename, 'w' ) )
			return false;

		$foundit = false;
		if ( $markerdata ) {
			$state = true;
			foreach ( $markerdata as $n => $markerline ) {
				if (strpos($markerline, '# BEGIN ' . $marker) !== false)
					$state = false;
				if ( $state ) {
					if ( $n + 1 < count( $markerdata ) )
						fwrite( $f, "{$markerline}\n" );
					else
						fwrite( $f, "{$markerline}" );
				}
				if (strpos($markerline, '# END ' . $marker) !== false) {
					fwrite( $f, "# BEGIN {$marker}\n" );
					if ( is_array( $insertion ))
						foreach ( $insertion as $insertline )
							fwrite( $f, "{$insertline}\n" );
					fwrite( $f, "# END {$marker}\n" );
					$state = true;
					$foundit = true;
				}
			}
		}
		if (!$foundit) {
			fwrite( $f, "\n# BEGIN {$marker}\n" );
			foreach ( $insertion as $insertline )
				fwrite( $f, "{$insertline}\n" );
			fwrite( $f, "# END {$marker}\n" );
		}
		fclose( $f );
		return true;
	} else {
		return false;
	}
}

/**
 * Updates the htaccess file with the current rules if it is writable.
 *
 * Always writes to the file if it exists and is writable to ensure that we
 * blank out old rules.
 *
 * @since unknown
 */
function save_mod_rewrite_rules() {
	if ( is_multisite() )
		return;

	global $wp_rewrite;

	$home_path = get_home_path();
	$htaccess_file = $home_path.'.htaccess';

	// If the file doesn't already exists check for write access to the directory and whether we have some rules.
	// else check for write access to the file.
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
 * @return bool True if web.config was updated successfully
 */
function iis7_save_url_rewrite_rules(){
	global $wp_rewrite;

	$home_path = get_home_path();
	$web_config_file = $home_path . 'web.config';

	// Using win_is_writable() instead of is_writable() because of a bug in Windows PHP
	if ( ( ! file_exists($web_config_file) && win_is_writable($home_path) && $wp_rewrite->using_mod_rewrite_permalinks() ) || win_is_writable($web_config_file) ) {
		if ( iis7_supports_permalinks() ) {
			$rule = $wp_rewrite->iis7_url_rewrite_rules(false, '', '');
			if ( ! empty($rule) ) {
				return iis7_add_rewrite_rule($web_config_file, $rule);
			} else {
				return iis7_delete_rewrite_rule($web_config_file);
			}
		}
	}
	return false;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $file
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
 * If siteurl or home changed, flush rewrite rules.
 *
 * @since unknown
 *
 * @param unknown_type $old_value
 * @param unknown_type $value
 */
function update_home_siteurl( $old_value, $value ) {
	global $wp_rewrite;

	if ( defined( "WP_INSTALLING" ) )
		return;

	// If home changed, write rewrite rules to new location.
	$wp_rewrite->flush_rules();
}

add_action( 'update_option_home', 'update_home_siteurl', 10, 2 );
add_action( 'update_option_siteurl', 'update_home_siteurl', 10, 2 );

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $url
 * @return unknown
 */
function url_shorten( $url ) {
	$short_url = str_replace( 'http://', '', stripslashes( $url ));
	$short_url = str_replace( 'www.', '', $short_url );
	if ('/' == substr( $short_url, -1 ))
		$short_url = substr( $short_url, 0, -1 );
	if ( strlen( $short_url ) > 35 )
		$short_url = substr( $short_url, 0, 32 ).'...';
	return $short_url;
}

/**
 * Resets global variables based on $_GET and $_POST
 *
 * This function resets global variables based on the names passed
 * in the $vars array to the value of $_POST[$var] or $_GET[$var] or ''
 * if neither is defined.
 *
 * @since unknown
 *
 * @param array $vars An array of globals to reset.
 */
function wp_reset_vars( $vars ) {
	for ( $i=0; $i<count( $vars ); $i += 1 ) {
		$var = $vars[$i];
		global $$var;

		if ( empty( $_POST[$var] ) ) {
			if ( empty( $_GET[$var] ) )
				$$var = '';
			else
				$$var = $_GET[$var];
		} else {
			$$var = $_POST[$var];
		}
	}
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $message
 */
function show_message($message) {
	if ( is_wp_error($message) ){
		if ( $message->get_error_data() )
			$message = $message->get_error_message() . ': ' . $message->get_error_data();
		else
			$message = $message->get_error_message();
	}
	echo "<p>$message</p>\n";
}

function wp_doc_link_parse( $content ) {
	if ( !is_string( $content ) || empty( $content ) )
		return array();

	if ( !function_exists('token_get_all') )
		return array();

	$tokens = token_get_all( $content );
	$functions = array();
	$ignore_functions = array();
	for ( $t = 0, $count = count( $tokens ); $t < $count; $t++ ) {
		if ( !is_array( $tokens[$t] ) ) continue;
		if ( T_STRING == $tokens[$t][0] && ( '(' == $tokens[ $t + 1 ] || '(' == $tokens[ $t + 2 ] ) ) {
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
 * Determines the language to use for CodePress syntax highlighting,
 * based only on a filename.
 *
 * @since 2.8
 *
 * @param string $filename The name of the file to be highlighting
**/
function codepress_get_lang( $filename ) {
	$codepress_supported_langs = apply_filters( 'codepress_supported_langs',
									array( '.css' => 'css',
											'.js' => 'javascript',
											'.php' => 'php',
											'.html' => 'html',
											'.htm' => 'html',
											'.txt' => 'text'
											) );
	$extension = substr( $filename, strrpos( $filename, '.' ) );
	if ( $extension && array_key_exists( $extension, $codepress_supported_langs ) )
		return $codepress_supported_langs[$extension];

	return 'generic';
}

/**
 * Adds Javascript required to make CodePress work on the theme/plugin editors.
 *
 * This code is attached to the action admin_print_footer_scripts.
 *
 * @since 2.8
**/
function codepress_footer_js() {
	// Script-loader breaks CP's automatic path-detection, thus CodePress.path
	// CP edits in an iframe, so we need to grab content back into normal form
	?><script type="text/javascript">
/* <![CDATA[ */
var codepress_path = '<?php echo includes_url('js/codepress/'); ?>';
jQuery('#template').submit(function(){
	if (jQuery('#newcontent_cp').length)
		jQuery('#newcontent_cp').val(newcontent.getCode()).removeAttr('disabled');
});
jQuery('#codepress-on').hide();
jQuery('#codepress-off').show();
/* ]]> */
</script>
<?php
}

/**
 * Determine whether to use CodePress.
 *
 * @since 2.8
**/
function use_codepress() {

	if ( isset($_GET['codepress']) ) {
		$on = 'on' == $_GET['codepress'] ? 'on' : 'off';
		set_user_setting( 'codepress', $on );
	} else {
		$on = get_user_setting('codepress', 'on');
	}

	if ( 'on' == $on ) {
		add_action( 'admin_print_footer_scripts', 'codepress_footer_js' );
		return true;
	}

	return false;
}

/**
 * Saves option for number of rows when listing posts, pages, comments, etc.
 *
 * @since 2.8
**/
function set_screen_options() {

	if ( isset($_POST['wp_screen_options']) && is_array($_POST['wp_screen_options']) ) {
		check_admin_referer( 'screen-options-nonce', 'screenoptionnonce' );

		if ( !$user = wp_get_current_user() )
			return;
		$option = $_POST['wp_screen_options']['option'];
		$value = $_POST['wp_screen_options']['value'];

		if ( !preg_match( '/^[a-z_-]+$/', $option ) )
			return;

		$option = str_replace('-', '_', $option);

		$map_option = $option;
		$type = str_replace('edit_', '', $map_option);
		$type = str_replace('_per_page', '', $type);
		if ( in_array($type, get_post_types()) )
			$map_option = 'edit_per_page';

		switch ( $map_option ) {
			case 'edit_per_page':
			case 'edit_comments_per_page':
			case 'upload_per_page':
			case 'categories_per_page':
			case 'edit_tags_per_page':
			case 'plugins_per_page':
				$value = (int) $value;
				if ( $value < 1 || $value > 999 )
					return;
				break;
			default:
				$value = apply_filters('set-screen-option', false, $option, $value);
				if ( false === $value )
					return;
				break;
		}

		update_user_meta($user->ID, $option, $value);
		wp_redirect( remove_query_arg( array('pagenum', 'apage', 'paged'), wp_get_referer() ) );
		exit;
	}
}

function wp_menu_unfold() {
	if ( isset($_GET['unfoldmenu']) ) {
		delete_user_setting('mfold');
		wp_redirect( remove_query_arg( 'unfoldmenu', stripslashes($_SERVER['REQUEST_URI']) ) );
	 	exit;
	}
}

/**
 * Check if IIS 7 supports pretty permalinks
 *
 * @since 2.8.0
 *
 * @return bool
 */
function iis7_supports_permalinks() {
	global $is_iis7;

	$supports_permalinks = false;
	if ( $is_iis7 ) {
		/* First we check if the DOMDocument class exists. If it does not exist,
		 * which is the case for PHP 4.X, then we cannot easily update the xml configuration file,
		 * hence we just bail out and tell user that pretty permalinks cannot be used.
		 * This is not a big issue because PHP 4.X is going to be depricated and for IIS it
		 * is recommended to use PHP 5.X NTS.
		 * Next we check if the URL Rewrite Module 1.1 is loaded and enabled for the web site. When
		 * URL Rewrite 1.1 is loaded it always sets a server variable called 'IIS_UrlRewriteModule'.
		 * Lastly we make sure that PHP is running via FastCGI. This is important because if it runs
		 * via ISAPI then pretty permalinks will not work.
		 */
		$supports_permalinks = class_exists('DOMDocument') && isset($_SERVER['IIS_UrlRewriteModule']) && ( php_sapi_name() == 'cgi-fcgi' );
	}

	return apply_filters('iis7_supports_permalinks', $supports_permalinks);
}

/**
 * Check if rewrite rule for WordPress already exists in the IIS 7 configuration file
 *
 * @since 2.8.0
 *
 * @return bool
 * @param string $filename The file path to the configuration file
 */
function iis7_rewrite_rule_exists($filename) {
	if ( ! file_exists($filename) )
		return false;
	if ( ! class_exists('DOMDocument') )
		return false;

	$doc = new DOMDocument();
	if ( $doc->load($filename) === false )
		return false;
	$xpath = new DOMXPath($doc);
	$rules = $xpath->query('/configuration/system.webServer/rewrite/rules/rule[@name=\'wordpress\']');
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

	if ( ! class_exists('DOMDocument') )
		return false;

	$doc = new DOMDocument();
	$doc->preserveWhiteSpace = false;

	if ( $doc -> load($filename) === false )
		return false;
	$xpath = new DOMXPath($doc);
	$rules = $xpath->query('/configuration/system.webServer/rewrite/rules/rule[@name=\'wordpress\']');
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
 * Add WordPress rewrite rule to the IIS 7 configuration file.
 *
 * @since 2.8.0
 *
 * @param string $filename The file path to the configuration file
 * @param string $rewrite_rule The XML fragment with URL Rewrite rule
 * @return bool
 */
function iis7_add_rewrite_rule($filename, $rewrite_rule) {
	if ( ! class_exists('DOMDocument') )
		return false;

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
	$wordpress_rules = $xpath->query('/configuration/system.webServer/rewrite/rules/rule[@name=\'wordpress\']');
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
 * Workaround for Windows bug in is_writable() function
 *
 * @since 2.8.0
 *
 * @param object $path
 * @return bool
 */
function win_is_writable($path) {
	/* will work in despite of Windows ACLs bug
	 * NOTE: use a trailing slash for folders!!!
	 * see http://bugs.php.net/bug.php?id=27609
	 * see http://bugs.php.net/bug.php?id=30931
	 */

    if ( $path{strlen($path)-1} == '/' ) // recursively return a temporary file path
        return win_is_writable($path . uniqid(mt_rand()) . '.tmp');
    else if ( is_dir($path) )
        return win_is_writable($path . '/' . uniqid(mt_rand()) . '.tmp');
    // check tmp file for read/write capabilities
    $rm = file_exists($path);
    $f = @fopen($path, 'a');
    if ($f===false)
        return false;
    fclose($f);
    if ( ! $rm )
        unlink($path);
    return true;
}
?>
