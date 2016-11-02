<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Prepare a proper icon classname from user's custom input
 *
 * @param {String} $icon_class
 *
 * @return mixed|string
 */
function us_prepare_icon_class( $icon_class ) {
	$icon_class = trim( $icon_class );
	// mdfi-toggle-check-box => mdfi_toggle_check_box
	if ( substr( $icon_class, 0, 4 ) == 'mdfi' ) {
		return str_replace( '-', '_', $icon_class );
	} // fa-check => fa fa-check
	elseif ( substr( $icon_class, 0, 3 ) == 'fa-' ) {
		return 'fa ' . $icon_class;
	} // check => fa fa-check
	else {
		return 'fa fa-' . $icon_class;
	}
}

/**
 * Search for some file in child theme, in parent theme and in framework
 *
 * @param string $filename Relative path to filename with extension
 * @param bool $all List an array of found files
 *
 * @return mixed Single mode: full path to file or FALSE if no file was found
 * @return array All mode: array or all the found files
 */
function us_locate_file( $filename, $all = FALSE ) {
	global $us_template_directory, $us_stylesheet_directory, $us_files_search_paths, $us_file_paths;
	if ( ! isset( $us_files_search_paths ) ) {
		$us_files_search_paths = array();
		if ( is_child_theme() ) {
			// Searching in child theme first
			$us_files_search_paths[] = $us_stylesheet_directory . '/';
		}
		// Parent theme
		$us_files_search_paths[] = $us_template_directory . '/';
		// The framework with files common for all themes
		$us_files_search_paths[] = $us_template_directory . '/framework/';
		// Can be overloaded if you decide to overload something from certain plugin
		$us_files_search_paths = apply_filters( 'us_files_search_paths', $us_files_search_paths );
	}
	if ( ! $all ) {
		if ( ! isset( $us_file_paths ) ) {
			$us_file_paths = apply_filters( 'us_file_paths', array() );
		}
		$filename = untrailingslashit( $filename );
		if ( ! isset( $us_file_paths[ $filename ] ) ) {
			$us_file_paths[ $filename ] = FALSE;
			foreach ( $us_files_search_paths as $search_path ) {
				if ( file_exists( $search_path . $filename ) ) {
					$us_file_paths[ $filename ] = $search_path . $filename;
					break;
				}
			}
		}

		return $us_file_paths[ $filename ];
	} else {
		$found = array();

		foreach ( $us_files_search_paths as $search_path ) {
			if ( file_exists( $search_path . $filename ) ) {
				$found[] = $search_path . $filename;
			}
		}

		return $found;
	}
}

/**
 * Search for some file in framework, in parent theme and in child theme
 *
 * @param string $filename
 *
 * @deprecated Use us_locate_file with $all argument instead. This function will be deleted soon!
 *
 * @return array Found files in their reversed priority order
 */
function us_locate_files( $filename ) {
	return array_reverse( us_locate_file( $filename, TRUE ) );
}

/**
 * Load some specified template and pass variables to it's scope.
 *
 * (!) If you create a template that is loaded via this method, please describe the variables that it should receive.
 *
 * @param string $template_name Template name to include (ex: 'templates/form/form')
 * @param array $vars Array of variables to pass to a included templated
 */
function us_load_template( $template_name, $vars = NULL ) {

	// Searching for the needed file in a child theme, in the parent theme and, finally, in the framework
	$file_path = us_locate_file( $template_name . '.php' );

	// Template not found
	if ( $file_path === FALSE ) {
		do_action( 'us_template_not_found:' . $template_name, $vars );

		return;
	}

	$vars = apply_filters( 'us_template_vars:' . $template_name, (array) $vars );
	if ( is_array( $vars ) AND count( $vars ) > 0 ) {
		extract( $vars, EXTR_SKIP );
	}

	do_action( 'us_before_template:' . $template_name, $vars );

	include $file_path;

	do_action( 'us_after_template:' . $template_name, $vars );
}

/**
 * Get some specified template output with variables passed to it's scope.
 *
 * (!) If you create a template that is loaded via this method, please describe the variables that it should receive.
 *
 * @param string $template_name Template name to include (ex: 'templates/form/form')
 * @param array $vars Array of variables to pass to a included templated
 *
 * @return string
 */
function us_get_template( $template_name, $vars = NULL ) {
	ob_start();
	us_load_template( $template_name, $vars );

	return ob_get_clean();
}

/**
 * Get theme option or return default value
 *
 * @param string $name
 * @param mixed $default_value
 *
 * @return mixed
 */
function us_get_option( $name, $default_value = NULL ) {
	return usof_get_option( $name, $default_value );
}

/**
 * @var $us_query array Allows to use different global $wp_query in different context safely
 */
$us_wp_queries = array();

/**
 * Opens a new context to use a new custom global $wp_query
 *
 * (!) Don't forget to close it!
 */
function us_open_wp_query_context() {
	array_unshift( $GLOBALS['us_wp_queries'], $GLOBALS['wp_query'] );
}

/**
 * Closes last context with a custom
 */
function us_close_wp_query_context() {
	if ( count( $GLOBALS['us_wp_queries'] ) > 0 ) {
		$GLOBALS['wp_query'] = array_shift( $GLOBALS['us_wp_queries'] );
		wp_reset_postdata();
	} else {
		// In case someone forgot to open the context
		wp_reset_query();
	}
}

/**
 * Get a value from multidimensional array by path
 *
 * @param array $arr
 * @param string $path <key1>[.<key2>[...]]
 * @param mixed $default
 *
 * @return mixed
 */
function us_arr_path( &$arr, $path, $default = NULL ) {
	foreach ( explode( '.', $path ) as $key ) {
		if ( ! is_array( $arr ) OR ! isset( $arr[ $key ] ) ) {
			return $default;
		}
		$arr = &$arr[ $key ];
	}

	return $arr;
}

/**
 * Flatten array: get only it's values without keys from any depth
 *
 * @param array $arr
 *
 * @return array
 */
function us_arr_flatten( &$arr ) {
	$result = array();
	foreach ( $arr as $value ) {
		if ( ! is_array( $value ) ) {
			$result[] = $value;
		} else {
			$result += us_arr_flatten( $value );
		}
	}

	return $result;
}

/**
 * Load and return some specific config or it's part
 *
 * @param string $path <config_name>[.<key1>[.<key2>[...]]]
 *
 * @oaram mixed $default Value to return if no data is found
 *
 * @return mixed
 */
function us_config( $path, $default = NULL ) {
	global $us_template_directory;
	// Caching configuration values in a inner static value within the same request
	static $configs = array();
	// Defined paths to configuration files
	$config_name = strtok( $path, '.' );
	if ( ! isset( $configs[ $config_name ] ) ) {
		$config_paths = array_reverse( us_locate_file( 'config/' . $config_name . '.php', TRUE ) );
		if ( empty( $config_paths ) ) {
			if ( WP_DEBUG ) {
				wp_die( 'Config not found: ' . $config_name );
			}
			$configs[ $config_name ] = array();
		} else {
			us_maybe_load_theme_textdomain();
			// Parent $config data may be used from a config file
			$config = array();
			foreach ( $config_paths as $config_path ) {
				$config = require $config_path;
				// Config may be forced not to be overloaded from a config file
				if ( isset( $final_config ) AND $final_config ) {
					break;
				}
			}
			$configs[ $config_name ] = apply_filters( 'us_config_' . $config_name, $config );
		}
	}

	$path = substr( $path, strlen( $config_name ) + 1 );
	if ( $path == '' ) {
		return $configs[ $config_name ];
	}

	return us_arr_path( $configs[ $config_name ], $path, $default );
}

/**
 * Get image size information as an array
 *
 * @param string $size_name
 *
 * @return array
 */
function us_get_intermediate_image_size( $size_name ) {
	global $_wp_additional_image_sizes;
	if ( isset( $_wp_additional_image_sizes[ $size_name ] ) ) {
		// Getting custom image size
		return $_wp_additional_image_sizes[ $size_name ];
	} else {
		// Getting standard image size
		return array(
			'width' => get_option( "{$size_name}_size_w" ),
			'height' => get_option( "{$size_name}_size_h" ),
			'crop' => get_option( "{$size_name}_crop" ),
		);
	}
}

/**
 * Transform some variable to elm's onclick attribute, so it could be obtained from JavaScript as:
 * var data = elm.onclick()
 *
 * @param mixed $data Data to pass
 *
 * @return string Element attribute ' onclick="..."'
 */
function us_pass_data_to_js( $data ) {
	return ' onclick=\'return ' . htmlspecialchars( json_encode( $data ), ENT_QUOTES, 'UTF-8' ) . '\'';
}

/**
 * Try to get variable from JSON-encoded post variable
 *
 * Note: we pass some params via json-encoded variables, as via pure post some data (ex empty array) will be absent
 *
 * @param string $name $_POST's variable name
 *
 * @return array
 */
function us_maybe_get_post_json( $name = 'template_vars' ) {
	if ( isset( $_POST[ $name ] ) AND is_string( $_POST[ $name ] ) ) {
		$result = json_decode( stripslashes( $_POST[ $name ] ), TRUE );
		if ( ! is_array( $result ) ) {
			$result = array();
		}

		return $result;
	} else {
		return array();
	}
}

/**
 * No js_composer enabled link parsing compatibility
 *
 * @param $value
 *
 * @return array
 */
function us_vc_build_link( $value ) {
	if ( function_exists( 'vc_build_link' ) ) {
		$result = vc_build_link( $value );
	} else {
		$result = array( 'url' => '', 'title' => '', 'target' => '' );
		$params_pairs = explode( '|', $value );
		if ( ! empty( $params_pairs ) ) {
			foreach ( $params_pairs as $pair ) {
				$param = explode( ':', $pair, 2 );
				if ( ! empty( $param[0] ) && isset( $param[1] ) ) {
					$result[ $param[0] ] = rawurldecode( $param[1] );
				}
			}
		}
	}

	// Some of the values may have excess spaces, like the target's ' _blank' value.
	return array_map( 'trim', $result );
}

/**
 * Load theme's textdomain
 *
 * @param string $domain
 * @param string $path Relative path to seek in child theme and theme
 *
 * @return bool
 */
function us_maybe_load_theme_textdomain( $domain = 'us', $path = '/languages' ) {
	if ( is_textdomain_loaded( $domain ) ) {
		return TRUE;
	}
	$locale = apply_filters( 'theme_locale', get_locale(), $domain );
	$filepath = us_locate_file( $path . '/' . $locale . '.mo' );
	if ( $filepath === FALSE ) {
		return FALSE;
	}

	return load_textdomain( $domain, $filepath );
}

/**
 * Merge arrays, inserting $arr2 into $arr1 before/after certain key
 *
 * @param array $arr Modifyed array
 * @param array $inserted Inserted array
 * @param string $position 'before' / 'after' / 'top' / 'bottom'
 * @param string $key Associative key of $arr1 for before/after insertion
 *
 * @return array
 */
function us_array_merge_insert( array $arr, array $inserted, $position = 'bottom', $key = NULL ) {
	if ( $position == 'top' ) {
		return array_merge( $inserted, $arr );
	}
	$key_position = ( $key === NULL ) ? FALSE : array_search( $key, array_keys( $arr ) );
	if ( $key_position === FALSE OR ( $position != 'before' AND $position != 'after' ) ) {
		return array_merge( $arr, $inserted );
	}
	if ( $position == 'after' ) {
		$key_position++;
	}

	return array_merge( array_slice( $arr, 0, $key_position, TRUE ), $inserted, array_slice( $arr, $key_position, NULL, TRUE ) );
}

/**
 * Recursively merge two or more arrays in a proper way
 *
 * @param array $array1
 * @param array $array2
 * @param array ...
 *
 * @return array
 */
function us_array_merge( $array1, $array2 ) {
	$keys = array_keys( $array2 );
	// Is associative array?
	if ( array_keys( $keys ) !== $keys ) {
		foreach ( $array2 as $key => $value ) {
			if ( is_array( $value ) AND isset( $array1[ $key ] ) AND is_array( $array1[ $key ] ) ) {
				$array1[ $key ] = us_array_merge( $array1[ $key ], $value );
			} else {
				$array1[ $key ] = $value;
			}
		}
	} else {
		foreach ( $array2 as $value ) {
			if ( ! in_array( $value, $array1, TRUE ) ) {
				$array1[] = $value;
			}
		}
	}

	if ( func_num_args() > 2 ) {
		foreach ( array_slice( func_get_args(), 2 ) as $array2 ) {
			$array1 = us_array_merge( $array1, $array2 );
		}
	}

	return $array1;
}

/**
 * Combine user attributes with known attributes and fill in defaults from config when needed.
 *
 * @param array $atts Passed attributes
 * @param string $shortcode Shortcode name
 * @param string $param_name Shortcode's config param to take pairs from
 *
 * @return array
 */
function us_shortcode_atts( $atts, $shortcode, $param_name = 'atts' ) {
	$pairs = us_config( 'shortcodes.' . $shortcode . '.' . $param_name, array() );

	return shortcode_atts( $pairs, $atts, $shortcode );
}

/**
 * Get number of shares of the provided URL.
 *
 * @param string $url The url to count shares
 * @param array $providers Possible array values: 'facebook', 'twitter', 'linkedin', 'gplus', 'pinterest'
 *
 * @link https://gist.github.com/jonathanmoore/2640302 Great relevant code snippets
 *
 * Dev note: keep in mind that list of providers may differ for the same URL in different function calls.
 *
 * @return array Associative array of providers => share counts
 */
function us_get_sharing_counts( $url, $providers ) {
	// TODO One-end hashing function needed
	$transient = 'us_sharing_count_' . md5( $url );
	// Will be used for array keys operations
	$flipped = array_flip( $providers );
	$cached_counts = get_transient( $transient );
	if ( is_array( $cached_counts ) ) {
		$counts = array_intersect_key( $cached_counts, $flipped );
		if ( count( $counts ) == count( $providers ) ) {
			// The data exists and is complete
			return $counts;
		}
	} else {
		$counts = array();
	}

	// Facebook share count
	if ( in_array( 'facebook', $providers ) AND ! isset( $counts['facebook'] ) ) {
		$fb_query = 'SELECT share_count FROM link_stat WHERE url = "';
		$remote_get_url = 'https://graph.facebook.com/fql?q=' . urlencode( $fb_query ) . $url . urlencode( '"' );
		$result = wp_remote_get( $remote_get_url, array( 'timeout' => 3 ) );
		if ( is_array( $result ) ) {
			$data = json_decode( $result['body'], TRUE );
		} else {
			$data = NULL;
		}
		if ( is_array( $data ) AND isset( $data['data'] ) AND isset( $data['data'][0] ) AND isset( $data['data'][0]['share_count'] ) ) {
			$counts['facebook'] = $data['data'][0]['share_count'];
		} else {
			$counts['facebook'] = '0';
		}
	}

	// Twitter share count
	if ( in_array( 'twitter', $providers ) AND ! isset( $counts['twitter'] ) ) {
		// Twitter is not supporting sharing counts API and has no plans for it at the moment
		$counts['twitter'] = '0';
	}

	// Google+ share count
	if ( in_array( 'gplus', $providers ) AND ! isset( $counts['gplus'] ) ) {
		// Cannot use the official API, as it requires a separate key, and even with this key doesn't work
		$result = wp_remote_get( 'https://plusone.google.com/_/+1/fastbutton?url=' . $url, array( 'timeout' => 3 ) );
		if ( is_array( $result ) AND preg_match( '~\<div[^\>]+id=\"aggregateCount\"[^\>]*\>([^\>]+)\<\/div\>~', $result['body'], $matches ) ) {
			$counts['gplus'] = $matches[1];
		} else {
			$counts['gplus'] = '0';
		}
	}

	// LinkedIn share count
	if ( in_array( 'linkedin', $providers ) AND ! isset( $counts['linkedin'] ) ) {
		$result = wp_remote_get( 'http://www.linkedin.com/countserv/count/share?url=' . $url . '&format=json', array( 'timeout' => 3 ) );
		if ( is_array( $result ) ) {
			$data = json_decode( $result['body'], TRUE );
		} else {
			$data = NULL;
		}
		$counts['linkedin'] = isset( $data['count'] ) ? $data['count'] : '0';
	}

	// Pinterest share count
	if ( in_array( 'pinterest', $providers ) AND ! isset( $counts['pinterest'] ) ) {
		$result = wp_remote_get( 'http://api.pinterest.com/v1/urls/count.json?callback=receiveCount&url=' . $url, array( 'timeout' => 3 ) );
		if ( is_array( $result ) ) {
			$data = json_decode( rtrim( str_replace( 'receiveCount(', '', $result['body'] ), ')' ), TRUE );
		} else {
			$data = NULL;
		}
		$counts['pinterest'] = isset( $data['count'] ) ? $data['count'] : '0';
	}

	// VK share count
	if ( in_array( 'vk', $providers ) AND ! isset( $counts['vk'] ) ) {
		$result = wp_remote_get( 'http://vkontakte.ru/share.php?act=count&index=1&url=' . $url, array( 'timeout' => 3 ) );
		if ( is_array( $result ) ) {
			$data = intval( trim( str_replace( ');', '', str_replace( 'VK.Share.count(1, ', '', $result['body'] ) ) ) );
		} else {
			$data = NULL;
		}
		$counts['vk'] = ( ! empty( $data ) ) ? $data : '0';
	}

	// Caching the result for the next 2 hours
	set_transient( $transient, $counts, 2 * HOUR_IN_SECONDS );

	return $counts;
}

/**
 * Call __ language functuion with phrase existing in supported plugin using plugin text domain
 * and prevent this phrase from going into our .po/.mo files
 *
 * @param string $text text to translate
 * @param string $domain plugin's text domain
 *
 * @return string Translated text.
 */
function us_translate_with_external_domain( $text, $domain = NULL ) {
	if ( $domain == NULL ) {
		return __( $text );
	} else {
		return __( $text, $domain );
	}

}

/**
 * Prepare a proper inline-css string from given css proper
 *
 * @param array $props
 * @param bool $style_attr
 *
 * @return string
 */
function us_prepare_inline_css( $props, $style_attr = TRUE ) {
	$result = '';
	foreach ( $props as $prop => $value ) {
		if ( empty( $value ) ) {
			continue;
		}
		switch ( $prop ) {
			// Properties that can be set either in percents or in pixels
			case 'width':
			case 'padding':
				if ( is_string( $value ) AND strpos( $value, '%' ) !== FALSE ) {
					$result .= $prop . ':' . floatval( $value ) . '%;';
				} else {
					$result .= $prop . ':' . intval( $value ) . 'px;';
				}
				break;
			// Properties that can be set only in pixels
			case 'height':
			case 'font-size':
			case 'line-height':
			case 'border-width':
			case 'border-radius':
				$result .= $prop . ':' . intval( $value ) . 'px;';
				break;
			// Properties that need vendor prefixes
			case 'transition-duration':
				if ( ! preg_match( '~^(\d+ms)|(\d{0,2}(\.\d+)?s)$~', $value ) ) {
					$value = ( ( strpos( $value, '.' ) !== FALSE ) ? intval( ( floatval( $value ) * 1000 ) ) : intval( $value ) ) . 'ms';
				}
				$result .= '-webkit-' . $prop . ':' . $value . ';' . $prop . ':' . $value . ';';
				break;
			// Properties with image values
			case 'background-image':
				if ( is_numeric( $value ) ) {
					$image = wp_get_attachment_image_src( $value, 'full' );
					if ( $image ) {
						$result .= $prop . ':url("' . $image[0] . '");';
					}
				} else {
					$result .= $prop . ':url("' . $value . '");';
				}
				break;
			// All other properties
			default:
				$result .= $prop . ':' . $value . ';';
				break;
		}
	}
	if ( $style_attr AND ! empty( $result ) ) {
		$result = ' style="' . esc_attr( $result ) . '"';
	}

	return $result;
}

/**
 * Perorm request to US Portal API
 *
 * @param $url
 *
 * @return array|bool|mixed|object
 */
function us_api_remote_request( $url ) {

	if ( empty( $url ) ) {
		return FALSE;
	}

	$args = array(
		'headers' => array( 'Accept-Encoding' => '' ),
		'sslverify' => FALSE,
		'timeout' => 300,
		'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/32.0.1700.107 Chrome/32.0.1700.107 Safari/537.36',
	);
	$request = wp_remote_request( $url, $args );

	if ( is_wp_error( $request ) ) {
		//		echo $request->get_error_message();
		return FALSE;
	}

	$data = json_decode( $request['body'] );

	return $data;
}



function usof_meta( $key, $args = array(), $post_id = null )
{
	if ( ! $post_id )
		$post_id = get_the_ID();

	$value = '';
	if ( ! empty( $key ) )
	{
		$value  = get_post_meta( $post_id, $key, TRUE );
	}

	return $value;
}
