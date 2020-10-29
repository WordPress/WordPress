<?php
/**
 * Theme, template, and stylesheet functions.
 *
 * @package WordPress
 * @subpackage Theme
 */

/**
 * Returns an array of WP_Theme objects based on the arguments.
 *
 * Despite advances over get_themes(), this function is quite expensive, and grows
 * linearly with additional themes. Stick to wp_get_theme() if possible.
 *
 * @since 3.4.0
 *
 * @global array $wp_theme_directories
 *
 * @param array $args {
 *     Optional. The search arguments.
 *
 *     @type mixed $errors  True to return themes with errors, false to return
 *                          themes without errors, null to return all themes.
 *                          Default false.
 *     @type mixed $allowed (Multisite) True to return only allowed themes for a site.
 *                          False to return only disallowed themes for a site.
 *                          'site' to return only site-allowed themes.
 *                          'network' to return only network-allowed themes.
 *                          Null to return all themes. Default null.
 *     @type int   $blog_id (Multisite) The blog ID used to calculate which themes
 *                          are allowed. Default 0, synonymous for the current blog.
 * }
 * @return WP_Theme[] Array of WP_Theme objects.
 */
function wp_get_themes( $args = array() ) {
	global $wp_theme_directories;

	$defaults = array(
		'errors'  => false,
		'allowed' => null,
		'blog_id' => 0,
	);
	$args     = wp_parse_args( $args, $defaults );

	$theme_directories = search_theme_directories();

	if ( is_array( $wp_theme_directories ) && count( $wp_theme_directories ) > 1 ) {
		// Make sure the current theme wins out, in case search_theme_directories() picks the wrong
		// one in the case of a conflict. (Normally, last registered theme root wins.)
		$current_theme = get_stylesheet();
		if ( isset( $theme_directories[ $current_theme ] ) ) {
			$root_of_current_theme = get_raw_theme_root( $current_theme );
			if ( ! in_array( $root_of_current_theme, $wp_theme_directories, true ) ) {
				$root_of_current_theme = WP_CONTENT_DIR . $root_of_current_theme;
			}
			$theme_directories[ $current_theme ]['theme_root'] = $root_of_current_theme;
		}
	}

	if ( empty( $theme_directories ) ) {
		return array();
	}

	if ( is_multisite() && null !== $args['allowed'] ) {
		$allowed = $args['allowed'];
		if ( 'network' === $allowed ) {
			$theme_directories = array_intersect_key( $theme_directories, WP_Theme::get_allowed_on_network() );
		} elseif ( 'site' === $allowed ) {
			$theme_directories = array_intersect_key( $theme_directories, WP_Theme::get_allowed_on_site( $args['blog_id'] ) );
		} elseif ( $allowed ) {
			$theme_directories = array_intersect_key( $theme_directories, WP_Theme::get_allowed( $args['blog_id'] ) );
		} else {
			$theme_directories = array_diff_key( $theme_directories, WP_Theme::get_allowed( $args['blog_id'] ) );
		}
	}

	$themes         = array();
	static $_themes = array();

	foreach ( $theme_directories as $theme => $theme_root ) {
		if ( isset( $_themes[ $theme_root['theme_root'] . '/' . $theme ] ) ) {
			$themes[ $theme ] = $_themes[ $theme_root['theme_root'] . '/' . $theme ];
		} else {
			$themes[ $theme ] = new WP_Theme( $theme, $theme_root['theme_root'] );

			$_themes[ $theme_root['theme_root'] . '/' . $theme ] = $themes[ $theme ];
		}
	}

	if ( null !== $args['errors'] ) {
		foreach ( $themes as $theme => $wp_theme ) {
			if ( $wp_theme->errors() != $args['errors'] ) {
				unset( $themes[ $theme ] );
			}
		}
	}

	return $themes;
}

/**
 * Gets a WP_Theme object for a theme.
 *
 * @since 3.4.0
 *
 * @global array $wp_theme_directories
 *
 * @param string $stylesheet Optional. Directory name for the theme. Defaults to current theme.
 * @param string $theme_root Optional. Absolute path of the theme root to look in.
 *                           If not specified, get_raw_theme_root() is used to calculate
 *                           the theme root for the $stylesheet provided (or current theme).
 * @return WP_Theme Theme object. Be sure to check the object's exists() method
 *                  if you need to confirm the theme's existence.
 */
function wp_get_theme( $stylesheet = '', $theme_root = '' ) {
	global $wp_theme_directories;

	if ( empty( $stylesheet ) ) {
		$stylesheet = get_stylesheet();
	}

	if ( empty( $theme_root ) ) {
		$theme_root = get_raw_theme_root( $stylesheet );
		if ( false === $theme_root ) {
			$theme_root = WP_CONTENT_DIR . '/themes';
		} elseif ( ! in_array( $theme_root, (array) $wp_theme_directories, true ) ) {
			$theme_root = WP_CONTENT_DIR . $theme_root;
		}
	}

	return new WP_Theme( $stylesheet, $theme_root );
}

/**
 * Clears the cache held by get_theme_roots() and WP_Theme.
 *
 * @since 3.5.0
 * @param bool $clear_update_cache Whether to clear the theme updates cache.
 */
function wp_clean_themes_cache( $clear_update_cache = true ) {
	if ( $clear_update_cache ) {
		delete_site_transient( 'update_themes' );
	}
	search_theme_directories( true );
	foreach ( wp_get_themes( array( 'errors' => null ) ) as $theme ) {
		$theme->cache_delete();
	}
}

/**
 * Whether a child theme is in use.
 *
 * @since 3.0.0
 *
 * @return bool True if a child theme is in use, false otherwise.
 */
function is_child_theme() {
	return ( TEMPLATEPATH !== STYLESHEETPATH );
}

/**
 * Retrieves name of the current stylesheet.
 *
 * The theme name that is currently set as the front end theme.
 *
 * For all intents and purposes, the template name and the stylesheet name
 * are going to be the same for most cases.
 *
 * @since 1.5.0
 *
 * @return string Stylesheet name.
 */
function get_stylesheet() {
	/**
	 * Filters the name of current stylesheet.
	 *
	 * @since 1.5.0
	 *
	 * @param string $stylesheet Name of the current stylesheet.
	 */
	return apply_filters( 'stylesheet', get_option( 'stylesheet' ) );
}

/**
 * Retrieves stylesheet directory path for current theme.
 *
 * @since 1.5.0
 *
 * @return string Path to current theme's stylesheet directory.
 */
function get_stylesheet_directory() {
	$stylesheet     = get_stylesheet();
	$theme_root     = get_theme_root( $stylesheet );
	$stylesheet_dir = "$theme_root/$stylesheet";

	/**
	 * Filters the stylesheet directory path for current theme.
	 *
	 * @since 1.5.0
	 *
	 * @param string $stylesheet_dir Absolute path to the current theme.
	 * @param string $stylesheet     Directory name of the current theme.
	 * @param string $theme_root     Absolute path to themes directory.
	 */
	return apply_filters( 'stylesheet_directory', $stylesheet_dir, $stylesheet, $theme_root );
}

/**
 * Retrieves stylesheet directory URI for current theme.
 *
 * @since 1.5.0
 *
 * @return string URI to current theme's stylesheet directory.
 */
function get_stylesheet_directory_uri() {
	$stylesheet         = str_replace( '%2F', '/', rawurlencode( get_stylesheet() ) );
	$theme_root_uri     = get_theme_root_uri( $stylesheet );
	$stylesheet_dir_uri = "$theme_root_uri/$stylesheet";

	/**
	 * Filters the stylesheet directory URI.
	 *
	 * @since 1.5.0
	 *
	 * @param string $stylesheet_dir_uri Stylesheet directory URI.
	 * @param string $stylesheet         Name of the activated theme's directory.
	 * @param string $theme_root_uri     Themes root URI.
	 */
	return apply_filters( 'stylesheet_directory_uri', $stylesheet_dir_uri, $stylesheet, $theme_root_uri );
}

/**
 * Retrieves stylesheet URI for current theme.
 *
 * The stylesheet file name is 'style.css' which is appended to the stylesheet directory URI path.
 * See get_stylesheet_directory_uri().
 *
 * @since 1.5.0
 *
 * @return string URI to current theme's stylesheet.
 */
function get_stylesheet_uri() {
	$stylesheet_dir_uri = get_stylesheet_directory_uri();
	$stylesheet_uri     = $stylesheet_dir_uri . '/style.css';
	/**
	 * Filters the URI of the current theme stylesheet.
	 *
	 * @since 1.5.0
	 *
	 * @param string $stylesheet_uri     Stylesheet URI for the current theme/child theme.
	 * @param string $stylesheet_dir_uri Stylesheet directory URI for the current theme/child theme.
	 */
	return apply_filters( 'stylesheet_uri', $stylesheet_uri, $stylesheet_dir_uri );
}

/**
 * Retrieves the localized stylesheet URI.
 *
 * The stylesheet directory for the localized stylesheet files are located, by
 * default, in the base theme directory. The name of the locale file will be the
 * locale followed by '.css'. If that does not exist, then the text direction
 * stylesheet will be checked for existence, for example 'ltr.css'.
 *
 * The theme may change the location of the stylesheet directory by either using
 * the {@see 'stylesheet_directory_uri'} or {@see 'locale_stylesheet_uri'} filters.
 *
 * If you want to change the location of the stylesheet files for the entire
 * WordPress workflow, then change the former. If you just have the locale in a
 * separate folder, then change the latter.
 *
 * @since 2.1.0
 *
 * @global WP_Locale $wp_locale WordPress date and time locale object.
 *
 * @return string URI to current theme's localized stylesheet.
 */
function get_locale_stylesheet_uri() {
	global $wp_locale;
	$stylesheet_dir_uri = get_stylesheet_directory_uri();
	$dir                = get_stylesheet_directory();
	$locale             = get_locale();
	if ( file_exists( "$dir/$locale.css" ) ) {
		$stylesheet_uri = "$stylesheet_dir_uri/$locale.css";
	} elseif ( ! empty( $wp_locale->text_direction ) && file_exists( "$dir/{$wp_locale->text_direction}.css" ) ) {
		$stylesheet_uri = "$stylesheet_dir_uri/{$wp_locale->text_direction}.css";
	} else {
		$stylesheet_uri = '';
	}
	/**
	 * Filters the localized stylesheet URI.
	 *
	 * @since 2.1.0
	 *
	 * @param string $stylesheet_uri     Localized stylesheet URI.
	 * @param string $stylesheet_dir_uri Stylesheet directory URI.
	 */
	return apply_filters( 'locale_stylesheet_uri', $stylesheet_uri, $stylesheet_dir_uri );
}

/**
 * Retrieves name of the current theme.
 *
 * @since 1.5.0
 *
 * @return string Template name.
 */
function get_template() {
	/**
	 * Filters the name of the current theme.
	 *
	 * @since 1.5.0
	 *
	 * @param string $template Current theme's directory name.
	 */
	return apply_filters( 'template', get_option( 'template' ) );
}

/**
 * Retrieves template directory path for current theme.
 *
 * @since 1.5.0
 *
 * @return string Path to current theme's template directory.
 */
function get_template_directory() {
	$template     = get_template();
	$theme_root   = get_theme_root( $template );
	$template_dir = "$theme_root/$template";

	/**
	 * Filters the current theme directory path.
	 *
	 * @since 1.5.0
	 *
	 * @param string $template_dir The path of the current theme directory.
	 * @param string $template     Directory name of the current theme.
	 * @param string $theme_root   Absolute path to the themes directory.
	 */
	return apply_filters( 'template_directory', $template_dir, $template, $theme_root );
}

/**
 * Retrieves template directory URI for current theme.
 *
 * @since 1.5.0
 *
 * @return string URI to current theme's template directory.
 */
function get_template_directory_uri() {
	$template         = str_replace( '%2F', '/', rawurlencode( get_template() ) );
	$theme_root_uri   = get_theme_root_uri( $template );
	$template_dir_uri = "$theme_root_uri/$template";

	/**
	 * Filters the current theme directory URI.
	 *
	 * @since 1.5.0
	 *
	 * @param string $template_dir_uri The URI of the current theme directory.
	 * @param string $template         Directory name of the current theme.
	 * @param string $theme_root_uri   The themes root URI.
	 */
	return apply_filters( 'template_directory_uri', $template_dir_uri, $template, $theme_root_uri );
}

/**
 * Retrieves theme roots.
 *
 * @since 2.9.0
 *
 * @global array $wp_theme_directories
 *
 * @return array|string An array of theme roots keyed by template/stylesheet
 *                      or a single theme root if all themes have the same root.
 */
function get_theme_roots() {
	global $wp_theme_directories;

	if ( ! is_array( $wp_theme_directories ) || count( $wp_theme_directories ) <= 1 ) {
		return '/themes';
	}

	$theme_roots = get_site_transient( 'theme_roots' );
	if ( false === $theme_roots ) {
		search_theme_directories( true ); // Regenerate the transient.
		$theme_roots = get_site_transient( 'theme_roots' );
	}
	return $theme_roots;
}

/**
 * Registers a directory that contains themes.
 *
 * @since 2.9.0
 *
 * @global array $wp_theme_directories
 *
 * @param string $directory Either the full filesystem path to a theme folder
 *                          or a folder within WP_CONTENT_DIR.
 * @return bool True if successfully registered a directory that contains themes,
 *              false if the directory does not exist.
 */
function register_theme_directory( $directory ) {
	global $wp_theme_directories;

	if ( ! file_exists( $directory ) ) {
		// Try prepending as the theme directory could be relative to the content directory.
		$directory = WP_CONTENT_DIR . '/' . $directory;
		// If this directory does not exist, return and do not register.
		if ( ! file_exists( $directory ) ) {
			return false;
		}
	}

	if ( ! is_array( $wp_theme_directories ) ) {
		$wp_theme_directories = array();
	}

	$untrailed = untrailingslashit( $directory );
	if ( ! empty( $untrailed ) && ! in_array( $untrailed, $wp_theme_directories, true ) ) {
		$wp_theme_directories[] = $untrailed;
	}

	return true;
}

/**
 * Searches all registered theme directories for complete and valid themes.
 *
 * @since 2.9.0
 *
 * @global array $wp_theme_directories
 *
 * @param bool $force Optional. Whether to force a new directory scan. Default false.
 * @return array|false Valid themes found on success, false on failure.
 */
function search_theme_directories( $force = false ) {
	global $wp_theme_directories;
	static $found_themes = null;

	if ( empty( $wp_theme_directories ) ) {
		return false;
	}

	if ( ! $force && isset( $found_themes ) ) {
		return $found_themes;
	}

	$found_themes = array();

	$wp_theme_directories = (array) $wp_theme_directories;
	$relative_theme_roots = array();

	/*
	 * Set up maybe-relative, maybe-absolute array of theme directories.
	 * We always want to return absolute, but we need to cache relative
	 * to use in get_theme_root().
	 */
	foreach ( $wp_theme_directories as $theme_root ) {
		if ( 0 === strpos( $theme_root, WP_CONTENT_DIR ) ) {
			$relative_theme_roots[ str_replace( WP_CONTENT_DIR, '', $theme_root ) ] = $theme_root;
		} else {
			$relative_theme_roots[ $theme_root ] = $theme_root;
		}
	}

	/**
	 * Filters whether to get the cache of the registered theme directories.
	 *
	 * @since 3.4.0
	 *
	 * @param bool   $cache_expiration Whether to get the cache of the theme directories. Default false.
	 * @param string $context          The class or function name calling the filter.
	 */
	$cache_expiration = apply_filters( 'wp_cache_themes_persistently', false, 'search_theme_directories' );

	if ( $cache_expiration ) {
		$cached_roots = get_site_transient( 'theme_roots' );
		if ( is_array( $cached_roots ) ) {
			foreach ( $cached_roots as $theme_dir => $theme_root ) {
				// A cached theme root is no longer around, so skip it.
				if ( ! isset( $relative_theme_roots[ $theme_root ] ) ) {
					continue;
				}
				$found_themes[ $theme_dir ] = array(
					'theme_file' => $theme_dir . '/style.css',
					'theme_root' => $relative_theme_roots[ $theme_root ], // Convert relative to absolute.
				);
			}
			return $found_themes;
		}
		if ( ! is_int( $cache_expiration ) ) {
			$cache_expiration = 30 * MINUTE_IN_SECONDS;
		}
	} else {
		$cache_expiration = 30 * MINUTE_IN_SECONDS;
	}

	/* Loop the registered theme directories and extract all themes */
	foreach ( $wp_theme_directories as $theme_root ) {

		// Start with directories in the root of the current theme directory.
		$dirs = @ scandir( $theme_root );
		if ( ! $dirs ) {
			trigger_error( "$theme_root is not readable", E_USER_NOTICE );
			continue;
		}
		foreach ( $dirs as $dir ) {
			if ( ! is_dir( $theme_root . '/' . $dir ) || '.' === $dir[0] || 'CVS' === $dir ) {
				continue;
			}
			if ( file_exists( $theme_root . '/' . $dir . '/style.css' ) ) {
				// wp-content/themes/a-single-theme
				// wp-content/themes is $theme_root, a-single-theme is $dir.
				$found_themes[ $dir ] = array(
					'theme_file' => $dir . '/style.css',
					'theme_root' => $theme_root,
				);
			} else {
				$found_theme = false;
				// wp-content/themes/a-folder-of-themes/*
				// wp-content/themes is $theme_root, a-folder-of-themes is $dir, then themes are $sub_dirs.
				$sub_dirs = @ scandir( $theme_root . '/' . $dir );
				if ( ! $sub_dirs ) {
					trigger_error( "$theme_root/$dir is not readable", E_USER_NOTICE );
					continue;
				}
				foreach ( $sub_dirs as $sub_dir ) {
					if ( ! is_dir( $theme_root . '/' . $dir . '/' . $sub_dir ) || '.' === $dir[0] || 'CVS' === $dir ) {
						continue;
					}
					if ( ! file_exists( $theme_root . '/' . $dir . '/' . $sub_dir . '/style.css' ) ) {
						continue;
					}
					$found_themes[ $dir . '/' . $sub_dir ] = array(
						'theme_file' => $dir . '/' . $sub_dir . '/style.css',
						'theme_root' => $theme_root,
					);
					$found_theme                           = true;
				}
				// Never mind the above, it's just a theme missing a style.css.
				// Return it; WP_Theme will catch the error.
				if ( ! $found_theme ) {
					$found_themes[ $dir ] = array(
						'theme_file' => $dir . '/style.css',
						'theme_root' => $theme_root,
					);
				}
			}
		}
	}

	asort( $found_themes );

	$theme_roots          = array();
	$relative_theme_roots = array_flip( $relative_theme_roots );

	foreach ( $found_themes as $theme_dir => $theme_data ) {
		$theme_roots[ $theme_dir ] = $relative_theme_roots[ $theme_data['theme_root'] ]; // Convert absolute to relative.
	}

	if ( get_site_transient( 'theme_roots' ) != $theme_roots ) {
		set_site_transient( 'theme_roots', $theme_roots, $cache_expiration );
	}

	return $found_themes;
}

/**
 * Retrieves path to themes directory.
 *
 * Does not have trailing slash.
 *
 * @since 1.5.0
 *
 * @global array $wp_theme_directories
 *
 * @param string $stylesheet_or_template Optional. The stylesheet or template name of the theme.
 *                                       Default is to leverage the main theme root.
 * @return string Themes directory path.
 */
function get_theme_root( $stylesheet_or_template = '' ) {
	global $wp_theme_directories;

	$theme_root = '';

	if ( $stylesheet_or_template ) {
		$theme_root = get_raw_theme_root( $stylesheet_or_template );
		if ( $theme_root ) {
			// Always prepend WP_CONTENT_DIR unless the root currently registered as a theme directory.
			// This gives relative theme roots the benefit of the doubt when things go haywire.
			if ( ! in_array( $theme_root, (array) $wp_theme_directories, true ) ) {
				$theme_root = WP_CONTENT_DIR . $theme_root;
			}
		}
	}

	if ( ! $theme_root ) {
		$theme_root = WP_CONTENT_DIR . '/themes';
	}

	/**
	 * Filters the absolute path to the themes directory.
	 *
	 * @since 1.5.0
	 *
	 * @param string $theme_root Absolute path to themes directory.
	 */
	return apply_filters( 'theme_root', $theme_root );
}

/**
 * Retrieves URI for themes directory.
 *
 * Does not have trailing slash.
 *
 * @since 1.5.0
 *
 * @global array $wp_theme_directories
 *
 * @param string $stylesheet_or_template Optional. The stylesheet or template name of the theme.
 *                                       Default is to leverage the main theme root.
 * @param string $theme_root             Optional. The theme root for which calculations will be based,
 *                                       preventing the need for a get_raw_theme_root() call. Default empty.
 * @return string Themes directory URI.
 */
function get_theme_root_uri( $stylesheet_or_template = '', $theme_root = '' ) {
	global $wp_theme_directories;

	if ( $stylesheet_or_template && ! $theme_root ) {
		$theme_root = get_raw_theme_root( $stylesheet_or_template );
	}

	if ( $stylesheet_or_template && $theme_root ) {
		if ( in_array( $theme_root, (array) $wp_theme_directories, true ) ) {
			// Absolute path. Make an educated guess. YMMV -- but note the filter below.
			if ( 0 === strpos( $theme_root, WP_CONTENT_DIR ) ) {
				$theme_root_uri = content_url( str_replace( WP_CONTENT_DIR, '', $theme_root ) );
			} elseif ( 0 === strpos( $theme_root, ABSPATH ) ) {
				$theme_root_uri = site_url( str_replace( ABSPATH, '', $theme_root ) );
			} elseif ( 0 === strpos( $theme_root, WP_PLUGIN_DIR ) || 0 === strpos( $theme_root, WPMU_PLUGIN_DIR ) ) {
				$theme_root_uri = plugins_url( basename( $theme_root ), $theme_root );
			} else {
				$theme_root_uri = $theme_root;
			}
		} else {
			$theme_root_uri = content_url( $theme_root );
		}
	} else {
		$theme_root_uri = content_url( 'themes' );
	}

	/**
	 * Filters the URI for themes directory.
	 *
	 * @since 1.5.0
	 *
	 * @param string $theme_root_uri         The URI for themes directory.
	 * @param string $siteurl                WordPress web address which is set in General Options.
	 * @param string $stylesheet_or_template The stylesheet or template name of the theme.
	 */
	return apply_filters( 'theme_root_uri', $theme_root_uri, get_option( 'siteurl' ), $stylesheet_or_template );
}

/**
 * Gets the raw theme root relative to the content directory with no filters applied.
 *
 * @since 3.1.0
 *
 * @global array $wp_theme_directories
 *
 * @param string $stylesheet_or_template The stylesheet or template name of the theme.
 * @param bool   $skip_cache             Optional. Whether to skip the cache.
 *                                       Defaults to false, meaning the cache is used.
 * @return string Theme root.
 */
function get_raw_theme_root( $stylesheet_or_template, $skip_cache = false ) {
	global $wp_theme_directories;

	if ( ! is_array( $wp_theme_directories ) || count( $wp_theme_directories ) <= 1 ) {
		return '/themes';
	}

	$theme_root = false;

	// If requesting the root for the current theme, consult options to avoid calling get_theme_roots().
	if ( ! $skip_cache ) {
		if ( get_option( 'stylesheet' ) == $stylesheet_or_template ) {
			$theme_root = get_option( 'stylesheet_root' );
		} elseif ( get_option( 'template' ) == $stylesheet_or_template ) {
			$theme_root = get_option( 'template_root' );
		}
	}

	if ( empty( $theme_root ) ) {
		$theme_roots = get_theme_roots();
		if ( ! empty( $theme_roots[ $stylesheet_or_template ] ) ) {
			$theme_root = $theme_roots[ $stylesheet_or_template ];
		}
	}

	return $theme_root;
}

/**
 * Displays localized stylesheet link element.
 *
 * @since 2.1.0
 */
function locale_stylesheet() {
	$stylesheet = get_locale_stylesheet_uri();
	if ( empty( $stylesheet ) ) {
		return;
	}

	$type_attr = current_theme_supports( 'html5', 'style' ) ? '' : ' type="text/css"';

	printf(
		'<link rel="stylesheet" href="%s"%s media="screen" />',
		$stylesheet,
		$type_attr
	);
}

/**
 * Switches the theme.
 *
 * Accepts one argument: $stylesheet of the theme. It also accepts an additional function signature
 * of two arguments: $template then $stylesheet. This is for backward compatibility.
 *
 * @since 2.5.0
 *
 * @global array                $wp_theme_directories
 * @global WP_Customize_Manager $wp_customize
 * @global array                $sidebars_widgets
 *
 * @param string $stylesheet Stylesheet name.
 */
function switch_theme( $stylesheet ) {
	global $wp_theme_directories, $wp_customize, $sidebars_widgets;

	$requirements = validate_theme_requirements( $stylesheet );
	if ( is_wp_error( $requirements ) ) {
		wp_die( $requirements );
	}

	$_sidebars_widgets = null;
	if ( 'wp_ajax_customize_save' === current_action() ) {
		$old_sidebars_widgets_data_setting = $wp_customize->get_setting( 'old_sidebars_widgets_data' );
		if ( $old_sidebars_widgets_data_setting ) {
			$_sidebars_widgets = $wp_customize->post_value( $old_sidebars_widgets_data_setting );
		}
	} elseif ( is_array( $sidebars_widgets ) ) {
		$_sidebars_widgets = $sidebars_widgets;
	}

	if ( is_array( $_sidebars_widgets ) ) {
		set_theme_mod(
			'sidebars_widgets',
			array(
				'time' => time(),
				'data' => $_sidebars_widgets,
			)
		);
	}

	$nav_menu_locations = get_theme_mod( 'nav_menu_locations' );
	update_option( 'theme_switch_menu_locations', $nav_menu_locations );

	if ( func_num_args() > 1 ) {
		$stylesheet = func_get_arg( 1 );
	}

	$old_theme = wp_get_theme();
	$new_theme = wp_get_theme( $stylesheet );
	$template  = $new_theme->get_template();

	if ( wp_is_recovery_mode() ) {
		$paused_themes = wp_paused_themes();
		$paused_themes->delete( $old_theme->get_stylesheet() );
		$paused_themes->delete( $old_theme->get_template() );
	}

	update_option( 'template', $template );
	update_option( 'stylesheet', $stylesheet );

	if ( count( $wp_theme_directories ) > 1 ) {
		update_option( 'template_root', get_raw_theme_root( $template, true ) );
		update_option( 'stylesheet_root', get_raw_theme_root( $stylesheet, true ) );
	} else {
		delete_option( 'template_root' );
		delete_option( 'stylesheet_root' );
	}

	$new_name = $new_theme->get( 'Name' );

	update_option( 'current_theme', $new_name );

	// Migrate from the old mods_{name} option to theme_mods_{slug}.
	if ( is_admin() && false === get_option( 'theme_mods_' . $stylesheet ) ) {
		$default_theme_mods = (array) get_option( 'mods_' . $new_name );
		if ( ! empty( $nav_menu_locations ) && empty( $default_theme_mods['nav_menu_locations'] ) ) {
			$default_theme_mods['nav_menu_locations'] = $nav_menu_locations;
		}
		add_option( "theme_mods_$stylesheet", $default_theme_mods );
	} else {
		/*
		 * Since retrieve_widgets() is called when initializing a theme in the Customizer,
		 * we need to remove the theme mods to avoid overwriting changes made via
		 * the Customizer when accessing wp-admin/widgets.php.
		 */
		if ( 'wp_ajax_customize_save' === current_action() ) {
			remove_theme_mod( 'sidebars_widgets' );
		}
	}

	update_option( 'theme_switched', $old_theme->get_stylesheet() );

	/**
	 * Fires after the theme is switched.
	 *
	 * @since 1.5.0
	 * @since 4.5.0 Introduced the `$old_theme` parameter.
	 *
	 * @param string   $new_name  Name of the new theme.
	 * @param WP_Theme $new_theme WP_Theme instance of the new theme.
	 * @param WP_Theme $old_theme WP_Theme instance of the old theme.
	 */
	do_action( 'switch_theme', $new_name, $new_theme, $old_theme );
}

/**
 * Checks that the current theme has 'index.php' and 'style.css' files.
 *
 * Does not initially check the default theme, which is the fallback and should always exist.
 * But if it doesn't exist, it'll fall back to the latest core default theme that does exist.
 * Will switch theme to the fallback theme if current theme does not validate.
 *
 * You can use the {@see 'validate_current_theme'} filter to return false to disable
 * this functionality.
 *
 * @since 1.5.0
 *
 * @see WP_DEFAULT_THEME
 *
 * @return bool
 */
function validate_current_theme() {
	/**
	 * Filters whether to validate the current theme.
	 *
	 * @since 2.7.0
	 *
	 * @param bool $validate Whether to validate the current theme. Default true.
	 */
	if ( wp_installing() || ! apply_filters( 'validate_current_theme', true ) ) {
		return true;
	}

	if ( ! file_exists( get_template_directory() . '/index.php' ) ) {
		// Invalid.
	} elseif ( ! file_exists( get_template_directory() . '/style.css' ) ) {
		// Invalid.
	} elseif ( is_child_theme() && ! file_exists( get_stylesheet_directory() . '/style.css' ) ) {
		// Invalid.
	} else {
		// Valid.
		return true;
	}

	$default = wp_get_theme( WP_DEFAULT_THEME );
	if ( $default->exists() ) {
		switch_theme( WP_DEFAULT_THEME );
		return false;
	}

	/**
	 * If we're in an invalid state but WP_DEFAULT_THEME doesn't exist,
	 * switch to the latest core default theme that's installed.
	 *
	 * If it turns out that this latest core default theme is our current
	 * theme, then there's nothing we can do about that, so we have to bail,
	 * rather than going into an infinite loop. (This is why there are
	 * checks against WP_DEFAULT_THEME above, also.) We also can't do anything
	 * if it turns out there is no default theme installed. (That's `false`.)
	 */
	$default = WP_Theme::get_core_default_theme();
	if ( false === $default || get_stylesheet() == $default->get_stylesheet() ) {
		return true;
	}

	switch_theme( $default->get_stylesheet() );
	return false;
}

/**
 * Validates the theme requirements for WordPress version and PHP version.
 *
 * Uses the information from `Requires at least` and `Requires PHP` headers
 * defined in the theme's `style.css` file.
 *
 * If the headers are not present in the theme's stylesheet file,
 * `readme.txt` is also checked as a fallback.
 *
 * @since 5.5.0
 *
 * @param string $stylesheet Directory name for the theme.
 * @return true|WP_Error True if requirements are met, WP_Error on failure.
 */
function validate_theme_requirements( $stylesheet ) {
	$theme = wp_get_theme( $stylesheet );

	$requirements = array(
		'requires'     => ! empty( $theme->get( 'RequiresWP' ) ) ? $theme->get( 'RequiresWP' ) : '',
		'requires_php' => ! empty( $theme->get( 'RequiresPHP' ) ) ? $theme->get( 'RequiresPHP' ) : '',
	);

	$readme_file = $theme->theme_root . '/' . $stylesheet . '/readme.txt';

	if ( file_exists( $readme_file ) ) {
		$readme_headers = get_file_data(
			$readme_file,
			array(
				'requires'     => 'Requires at least',
				'requires_php' => 'Requires PHP',
			),
			'theme'
		);

		$requirements = array_merge( $readme_headers, $requirements );
	}

	$compatible_wp  = is_wp_version_compatible( $requirements['requires'] );
	$compatible_php = is_php_version_compatible( $requirements['requires_php'] );

	if ( ! $compatible_wp && ! $compatible_php ) {
		return new WP_Error(
			'theme_wp_php_incompatible',
			sprintf(
				/* translators: %s: Theme name. */
				_x( '<strong>Error:</strong> Current WordPress and PHP versions do not meet minimum requirements for %s.', 'theme' ),
				$theme->display( 'Name' )
			)
		);
	} elseif ( ! $compatible_php ) {
		return new WP_Error(
			'theme_php_incompatible',
			sprintf(
				/* translators: %s: Theme name. */
				_x( '<strong>Error:</strong> Current PHP version does not meet minimum requirements for %s.', 'theme' ),
				$theme->display( 'Name' )
			)
		);
	} elseif ( ! $compatible_wp ) {
		return new WP_Error(
			'theme_wp_incompatible',
			sprintf(
				/* translators: %s: Theme name. */
				_x( '<strong>Error:</strong> Current WordPress version does not meet minimum requirements for %s.', 'theme' ),
				$theme->display( 'Name' )
			)
		);
	}

	return true;
}

/**
 * Retrieves all theme modifications.
 *
 * @since 3.1.0
 *
 * @return array|void Theme modifications.
 */
function get_theme_mods() {
	$theme_slug = get_option( 'stylesheet' );
	$mods       = get_option( "theme_mods_$theme_slug" );
	if ( false === $mods ) {
		$theme_name = get_option( 'current_theme' );
		if ( false === $theme_name ) {
			$theme_name = wp_get_theme()->get( 'Name' );
		}
		$mods = get_option( "mods_$theme_name" ); // Deprecated location.
		if ( is_admin() && false !== $mods ) {
			update_option( "theme_mods_$theme_slug", $mods );
			delete_option( "mods_$theme_name" );
		}
	}
	return $mods;
}

/**
 * Retrieves theme modification value for the current theme.
 *
 * If the modification name does not exist, then the $default will be passed
 * through {@link https://www.php.net/sprintf sprintf()} PHP function with
 * the template directory URI as the first string and the stylesheet directory URI
 * as the second string.
 *
 * @since 2.1.0
 *
 * @param string       $name    Theme modification name.
 * @param string|false $default Optional. Theme modification default value. Default false.
 * @return mixed Theme modification value.
 */
function get_theme_mod( $name, $default = false ) {
	$mods = get_theme_mods();

	if ( isset( $mods[ $name ] ) ) {
		/**
		 * Filters the theme modification, or 'theme_mod', value.
		 *
		 * The dynamic portion of the hook name, `$name`, refers to the key name
		 * of the modification array. For example, 'header_textcolor', 'header_image',
		 * and so on depending on the theme options.
		 *
		 * @since 2.2.0
		 *
		 * @param string $current_mod The value of the current theme modification.
		 */
		return apply_filters( "theme_mod_{$name}", $mods[ $name ] );
	}

	if ( is_string( $default ) ) {
		// Only run the replacement if an sprintf() string format pattern was found.
		if ( preg_match( '#(?<!%)%(?:\d+\$?)?s#', $default ) ) {
			// Remove a single trailing percent sign.
			$default = preg_replace( '#(?<!%)%$#', '', $default );
			$default = sprintf( $default, get_template_directory_uri(), get_stylesheet_directory_uri() );
		}
	}

	/** This filter is documented in wp-includes/theme.php */
	return apply_filters( "theme_mod_{$name}", $default );
}

/**
 * Updates theme modification value for the current theme.
 *
 * @since 2.1.0
 * @since 5.6.0 A return value was added.
 *
 * @param string $name  Theme modification name.
 * @param mixed  $value Theme modification value.
 * @return bool True if the value was updated, false otherwise.
 */
function set_theme_mod( $name, $value ) {
	$mods      = get_theme_mods();
	$old_value = isset( $mods[ $name ] ) ? $mods[ $name ] : false;

	/**
	 * Filters the theme modification, or 'theme_mod', value on save.
	 *
	 * The dynamic portion of the hook name, `$name`, refers to the key name
	 * of the modification array. For example, 'header_textcolor', 'header_image',
	 * and so on depending on the theme options.
	 *
	 * @since 3.9.0
	 *
	 * @param string $value     The new value of the theme modification.
	 * @param string $old_value The current value of the theme modification.
	 */
	$mods[ $name ] = apply_filters( "pre_set_theme_mod_{$name}", $value, $old_value );

	$theme = get_option( 'stylesheet' );

	return update_option( "theme_mods_$theme", $mods );
}

/**
 * Removes theme modification name from current theme list.
 *
 * If removing the name also removes all elements, then the entire option
 * will be removed.
 *
 * @since 2.1.0
 *
 * @param string $name Theme modification name.
 */
function remove_theme_mod( $name ) {
	$mods = get_theme_mods();

	if ( ! isset( $mods[ $name ] ) ) {
		return;
	}

	unset( $mods[ $name ] );

	if ( empty( $mods ) ) {
		remove_theme_mods();
		return;
	}

	$theme = get_option( 'stylesheet' );

	update_option( "theme_mods_$theme", $mods );
}

/**
 * Removes theme modifications option for current theme.
 *
 * @since 2.1.0
 */
function remove_theme_mods() {
	delete_option( 'theme_mods_' . get_option( 'stylesheet' ) );

	// Old style.
	$theme_name = get_option( 'current_theme' );
	if ( false === $theme_name ) {
		$theme_name = wp_get_theme()->get( 'Name' );
	}

	delete_option( 'mods_' . $theme_name );
}

/**
 * Retrieves the custom header text color in 3- or 6-digit hexadecimal form.
 *
 * @since 2.1.0
 *
 * @return string Header text color in 3- or 6-digit hexadecimal form (minus the hash symbol).
 */
function get_header_textcolor() {
	return get_theme_mod( 'header_textcolor', get_theme_support( 'custom-header', 'default-text-color' ) );
}

/**
 * Displays the custom header text color in 3- or 6-digit hexadecimal form (minus the hash symbol).
 *
 * @since 2.1.0
 */
function header_textcolor() {
	echo get_header_textcolor();
}

/**
 * Whether to display the header text.
 *
 * @since 3.4.0
 *
 * @return bool
 */
function display_header_text() {
	if ( ! current_theme_supports( 'custom-header', 'header-text' ) ) {
		return false;
	}

	$text_color = get_theme_mod( 'header_textcolor', get_theme_support( 'custom-header', 'default-text-color' ) );
	return 'blank' !== $text_color;
}

/**
 * Checks whether a header image is set or not.
 *
 * @since 4.2.0
 *
 * @see get_header_image()
 *
 * @return bool Whether a header image is set or not.
 */
function has_header_image() {
	return (bool) get_header_image();
}

/**
 * Retrieves header image for custom header.
 *
 * @since 2.1.0
 *
 * @return string|false
 */
function get_header_image() {
	$url = get_theme_mod( 'header_image', get_theme_support( 'custom-header', 'default-image' ) );

	if ( 'remove-header' === $url ) {
		return false;
	}

	if ( is_random_header_image() ) {
		$url = get_random_header_image();
	}

	return esc_url_raw( set_url_scheme( $url ) );
}

/**
 * Creates image tag markup for a custom header image.
 *
 * @since 4.4.0
 *
 * @param array $attr Optional. Additional attributes for the image tag. Can be used
 *                              to override the default attributes. Default empty.
 * @return string HTML image element markup or empty string on failure.
 */
function get_header_image_tag( $attr = array() ) {
	$header      = get_custom_header();
	$header->url = get_header_image();

	if ( ! $header->url ) {
		return '';
	}

	$width  = absint( $header->width );
	$height = absint( $header->height );

	$attr = wp_parse_args(
		$attr,
		array(
			'src'    => $header->url,
			'width'  => $width,
			'height' => $height,
			'alt'    => get_bloginfo( 'name' ),
		)
	);

	// Generate 'srcset' and 'sizes' if not already present.
	if ( empty( $attr['srcset'] ) && ! empty( $header->attachment_id ) ) {
		$image_meta = get_post_meta( $header->attachment_id, '_wp_attachment_metadata', true );
		$size_array = array( $width, $height );

		if ( is_array( $image_meta ) ) {
			$srcset = wp_calculate_image_srcset( $size_array, $header->url, $image_meta, $header->attachment_id );
			$sizes  = ! empty( $attr['sizes'] ) ? $attr['sizes'] : wp_calculate_image_sizes( $size_array, $header->url, $image_meta, $header->attachment_id );

			if ( $srcset && $sizes ) {
				$attr['srcset'] = $srcset;
				$attr['sizes']  = $sizes;
			}
		}
	}

	$attr = array_map( 'esc_attr', $attr );
	$html = '<img';

	foreach ( $attr as $name => $value ) {
		$html .= ' ' . $name . '="' . $value . '"';
	}

	$html .= ' />';

	/**
	 * Filters the markup of header images.
	 *
	 * @since 4.4.0
	 *
	 * @param string $html   The HTML image tag markup being filtered.
	 * @param object $header The custom header object returned by 'get_custom_header()'.
	 * @param array  $attr   Array of the attributes for the image tag.
	 */
	return apply_filters( 'get_header_image_tag', $html, $header, $attr );
}

/**
 * Displays the image markup for a custom header image.
 *
 * @since 4.4.0
 *
 * @param array $attr Optional. Attributes for the image markup. Default empty.
 */
function the_header_image_tag( $attr = array() ) {
	echo get_header_image_tag( $attr );
}

/**
 * Gets random header image data from registered images in theme.
 *
 * @since 3.4.0
 *
 * @access private
 *
 * @global array $_wp_default_headers
 *
 * @return object
 */
function _get_random_header_data() {
	static $_wp_random_header = null;

	if ( empty( $_wp_random_header ) ) {
		global $_wp_default_headers;
		$header_image_mod = get_theme_mod( 'header_image', '' );
		$headers          = array();

		if ( 'random-uploaded-image' === $header_image_mod ) {
			$headers = get_uploaded_header_images();
		} elseif ( ! empty( $_wp_default_headers ) ) {
			if ( 'random-default-image' === $header_image_mod ) {
				$headers = $_wp_default_headers;
			} else {
				if ( current_theme_supports( 'custom-header', 'random-default' ) ) {
					$headers = $_wp_default_headers;
				}
			}
		}

		if ( empty( $headers ) ) {
			return new stdClass;
		}

		$_wp_random_header = (object) $headers[ array_rand( $headers ) ];

		$_wp_random_header->url           = sprintf( $_wp_random_header->url, get_template_directory_uri(), get_stylesheet_directory_uri() );
		$_wp_random_header->thumbnail_url = sprintf( $_wp_random_header->thumbnail_url, get_template_directory_uri(), get_stylesheet_directory_uri() );
	}

	return $_wp_random_header;
}

/**
 * Gets random header image URL from registered images in theme.
 *
 * @since 3.2.0
 *
 * @return string Path to header image.
 */
function get_random_header_image() {
	$random_image = _get_random_header_data();

	if ( empty( $random_image->url ) ) {
		return '';
	}

	return $random_image->url;
}

/**
 * Checks if random header image is in use.
 *
 * Always true if user expressly chooses the option in Appearance > Header.
 * Also true if theme has multiple header images registered, no specific header image
 * is chosen, and theme turns on random headers with add_theme_support().
 *
 * @since 3.2.0
 *
 * @param string $type The random pool to use. Possible values include 'any',
 *                     'default', 'uploaded'. Default 'any'.
 * @return bool
 */
function is_random_header_image( $type = 'any' ) {
	$header_image_mod = get_theme_mod( 'header_image', get_theme_support( 'custom-header', 'default-image' ) );

	if ( 'any' === $type ) {
		if ( 'random-default-image' === $header_image_mod
			|| 'random-uploaded-image' === $header_image_mod
			|| ( '' !== get_random_header_image() && empty( $header_image_mod ) )
		) {
			return true;
		}
	} else {
		if ( "random-$type-image" === $header_image_mod ) {
			return true;
		} elseif ( 'default' === $type && empty( $header_image_mod ) && '' !== get_random_header_image() ) {
			return true;
		}
	}

	return false;
}

/**
 * Displays header image URL.
 *
 * @since 2.1.0
 */
function header_image() {
	$image = get_header_image();

	if ( $image ) {
		echo esc_url( $image );
	}
}

/**
 * Gets the header images uploaded for the current theme.
 *
 * @since 3.2.0
 *
 * @return array
 */
function get_uploaded_header_images() {
	$header_images = array();

	// @todo Caching.
	$headers = get_posts(
		array(
			'post_type'  => 'attachment',
			'meta_key'   => '_wp_attachment_is_custom_header',
			'meta_value' => get_option( 'stylesheet' ),
			'orderby'    => 'none',
			'nopaging'   => true,
		)
	);

	if ( empty( $headers ) ) {
		return array();
	}

	foreach ( (array) $headers as $header ) {
		$url          = esc_url_raw( wp_get_attachment_url( $header->ID ) );
		$header_data  = wp_get_attachment_metadata( $header->ID );
		$header_index = $header->ID;

		$header_images[ $header_index ]                      = array();
		$header_images[ $header_index ]['attachment_id']     = $header->ID;
		$header_images[ $header_index ]['url']               = $url;
		$header_images[ $header_index ]['thumbnail_url']     = $url;
		$header_images[ $header_index ]['alt_text']          = get_post_meta( $header->ID, '_wp_attachment_image_alt', true );
		$header_images[ $header_index ]['attachment_parent'] = isset( $header_data['attachment_parent'] ) ? $header_data['attachment_parent'] : '';

		if ( isset( $header_data['width'] ) ) {
			$header_images[ $header_index ]['width'] = $header_data['width'];
		}
		if ( isset( $header_data['height'] ) ) {
			$header_images[ $header_index ]['height'] = $header_data['height'];
		}
	}

	return $header_images;
}

/**
 * Gets the header image data.
 *
 * @since 3.4.0
 *
 * @global array $_wp_default_headers
 *
 * @return object
 */
function get_custom_header() {
	global $_wp_default_headers;

	if ( is_random_header_image() ) {
		$data = _get_random_header_data();
	} else {
		$data = get_theme_mod( 'header_image_data' );
		if ( ! $data && current_theme_supports( 'custom-header', 'default-image' ) ) {
			$directory_args        = array( get_template_directory_uri(), get_stylesheet_directory_uri() );
			$data                  = array();
			$data['url']           = vsprintf( get_theme_support( 'custom-header', 'default-image' ), $directory_args );
			$data['thumbnail_url'] = $data['url'];
			if ( ! empty( $_wp_default_headers ) ) {
				foreach ( (array) $_wp_default_headers as $default_header ) {
					$url = vsprintf( $default_header['url'], $directory_args );
					if ( $data['url'] == $url ) {
						$data                  = $default_header;
						$data['url']           = $url;
						$data['thumbnail_url'] = vsprintf( $data['thumbnail_url'], $directory_args );
						break;
					}
				}
			}
		}
	}

	$default = array(
		'url'           => '',
		'thumbnail_url' => '',
		'width'         => get_theme_support( 'custom-header', 'width' ),
		'height'        => get_theme_support( 'custom-header', 'height' ),
		'video'         => get_theme_support( 'custom-header', 'video' ),
	);
	return (object) wp_parse_args( $data, $default );
}

/**
 * Registers a selection of default headers to be displayed by the custom header admin UI.
 *
 * @since 3.0.0
 *
 * @global array $_wp_default_headers
 *
 * @param array $headers Array of headers keyed by a string ID. The IDs point to arrays
 *                       containing 'url', 'thumbnail_url', and 'description' keys.
 */
function register_default_headers( $headers ) {
	global $_wp_default_headers;

	$_wp_default_headers = array_merge( (array) $_wp_default_headers, (array) $headers );
}

/**
 * Unregisters default headers.
 *
 * This function must be called after register_default_headers() has already added the
 * header you want to remove.
 *
 * @see register_default_headers()
 * @since 3.0.0
 *
 * @global array $_wp_default_headers
 *
 * @param string|array $header The header string id (key of array) to remove, or an array thereof.
 * @return bool|void A single header returns true on success, false on failure.
 *                   There is currently no return value for multiple headers.
 */
function unregister_default_headers( $header ) {
	global $_wp_default_headers;
	if ( is_array( $header ) ) {
		array_map( 'unregister_default_headers', $header );
	} elseif ( isset( $_wp_default_headers[ $header ] ) ) {
		unset( $_wp_default_headers[ $header ] );
		return true;
	} else {
		return false;
	}
}

/**
 * Checks whether a header video is set or not.
 *
 * @since 4.7.0
 *
 * @see get_header_video_url()
 *
 * @return bool Whether a header video is set or not.
 */
function has_header_video() {
	return (bool) get_header_video_url();
}

/**
 * Retrieves header video URL for custom header.
 *
 * Uses a local video if present, or falls back to an external video.
 *
 * @since 4.7.0
 *
 * @return string|false Header video URL or false if there is no video.
 */
function get_header_video_url() {
	$id = absint( get_theme_mod( 'header_video' ) );

	if ( $id ) {
		// Get the file URL from the attachment ID.
		$url = wp_get_attachment_url( $id );
	} else {
		$url = get_theme_mod( 'external_header_video' );
	}

	/**
	 * Filters the header video URL.
	 *
	 * @since 4.7.3
	 *
	 * @param string $url Header video URL, if available.
	 */
	$url = apply_filters( 'get_header_video_url', $url );

	if ( ! $id && ! $url ) {
		return false;
	}

	return esc_url_raw( set_url_scheme( $url ) );
}

/**
 * Displays header video URL.
 *
 * @since 4.7.0
 */
function the_header_video_url() {
	$video = get_header_video_url();

	if ( $video ) {
		echo esc_url( $video );
	}
}

/**
 * Retrieves header video settings.
 *
 * @since 4.7.0
 *
 * @return array
 */
function get_header_video_settings() {
	$header     = get_custom_header();
	$video_url  = get_header_video_url();
	$video_type = wp_check_filetype( $video_url, wp_get_mime_types() );

	$settings = array(
		'mimeType'  => '',
		'posterUrl' => get_header_image(),
		'videoUrl'  => $video_url,
		'width'     => absint( $header->width ),
		'height'    => absint( $header->height ),
		'minWidth'  => 900,
		'minHeight' => 500,
		'l10n'      => array(
			'pause'      => __( 'Pause' ),
			'play'       => __( 'Play' ),
			'pauseSpeak' => __( 'Video is paused.' ),
			'playSpeak'  => __( 'Video is playing.' ),
		),
	);

	if ( preg_match( '#^https?://(?:www\.)?(?:youtube\.com/watch|youtu\.be/)#', $video_url ) ) {
		$settings['mimeType'] = 'video/x-youtube';
	} elseif ( ! empty( $video_type['type'] ) ) {
		$settings['mimeType'] = $video_type['type'];
	}

	/**
	 * Filters header video settings.
	 *
	 * @since 4.7.0
	 *
	 * @param array $settings An array of header video settings.
	 */
	return apply_filters( 'header_video_settings', $settings );
}

/**
 * Checks whether a custom header is set or not.
 *
 * @since 4.7.0
 *
 * @return bool True if a custom header is set. False if not.
 */
function has_custom_header() {
	if ( has_header_image() || ( has_header_video() && is_header_video_active() ) ) {
		return true;
	}

	return false;
}

/**
 * Checks whether the custom header video is eligible to show on the current page.
 *
 * @since 4.7.0
 *
 * @return bool True if the custom header video should be shown. False if not.
 */
function is_header_video_active() {
	if ( ! get_theme_support( 'custom-header', 'video' ) ) {
		return false;
	}

	$video_active_cb = get_theme_support( 'custom-header', 'video-active-callback' );

	if ( empty( $video_active_cb ) || ! is_callable( $video_active_cb ) ) {
		$show_video = true;
	} else {
		$show_video = call_user_func( $video_active_cb );
	}

	/**
	 * Filters whether the custom header video is eligible to show on the current page.
	 *
	 * @since 4.7.0
	 *
	 * @param bool $show_video Whether the custom header video should be shown. Returns the value
	 *                         of the theme setting for the `custom-header`'s `video-active-callback`.
	 *                         If no callback is set, the default value is that of `is_front_page()`.
	 */
	return apply_filters( 'is_header_video_active', $show_video );
}

/**
 * Retrieves the markup for a custom header.
 *
 * The container div will always be returned in the Customizer preview.
 *
 * @since 4.7.0
 *
 * @return string The markup for a custom header on success.
 */
function get_custom_header_markup() {
	if ( ! has_custom_header() && ! is_customize_preview() ) {
		return '';
	}

	return sprintf(
		'<div id="wp-custom-header" class="wp-custom-header">%s</div>',
		get_header_image_tag()
	);
}

/**
 * Prints the markup for a custom header.
 *
 * A container div will always be printed in the Customizer preview.
 *
 * @since 4.7.0
 */
function the_custom_header_markup() {
	$custom_header = get_custom_header_markup();
	if ( empty( $custom_header ) ) {
		return;
	}

	echo $custom_header;

	if ( is_header_video_active() && ( has_header_video() || is_customize_preview() ) ) {
		wp_enqueue_script( 'wp-custom-header' );
		wp_localize_script( 'wp-custom-header', '_wpCustomHeaderSettings', get_header_video_settings() );
	}
}

/**
 * Retrieves background image for custom background.
 *
 * @since 3.0.0
 *
 * @return string
 */
function get_background_image() {
	return get_theme_mod( 'background_image', get_theme_support( 'custom-background', 'default-image' ) );
}

/**
 * Displays background image path.
 *
 * @since 3.0.0
 */
function background_image() {
	echo get_background_image();
}

/**
 * Retrieves value for custom background color.
 *
 * @since 3.0.0
 *
 * @return string
 */
function get_background_color() {
	return get_theme_mod( 'background_color', get_theme_support( 'custom-background', 'default-color' ) );
}

/**
 * Displays background color value.
 *
 * @since 3.0.0
 */
function background_color() {
	echo get_background_color();
}

/**
 * Default custom background callback.
 *
 * @since 3.0.0
 */
function _custom_background_cb() {
	// $background is the saved custom image, or the default image.
	$background = set_url_scheme( get_background_image() );

	// $color is the saved custom color.
	// A default has to be specified in style.css. It will not be printed here.
	$color = get_background_color();

	if ( get_theme_support( 'custom-background', 'default-color' ) === $color ) {
		$color = false;
	}

	$type_attr = current_theme_supports( 'html5', 'style' ) ? '' : ' type="text/css"';

	if ( ! $background && ! $color ) {
		if ( is_customize_preview() ) {
			printf( '<style%s id="custom-background-css"></style>', $type_attr );
		}
		return;
	}

	$style = $color ? "background-color: #$color;" : '';

	if ( $background ) {
		$image = ' background-image: url("' . esc_url_raw( $background ) . '");';

		// Background Position.
		$position_x = get_theme_mod( 'background_position_x', get_theme_support( 'custom-background', 'default-position-x' ) );
		$position_y = get_theme_mod( 'background_position_y', get_theme_support( 'custom-background', 'default-position-y' ) );

		if ( ! in_array( $position_x, array( 'left', 'center', 'right' ), true ) ) {
			$position_x = 'left';
		}

		if ( ! in_array( $position_y, array( 'top', 'center', 'bottom' ), true ) ) {
			$position_y = 'top';
		}

		$position = " background-position: $position_x $position_y;";

		// Background Size.
		$size = get_theme_mod( 'background_size', get_theme_support( 'custom-background', 'default-size' ) );

		if ( ! in_array( $size, array( 'auto', 'contain', 'cover' ), true ) ) {
			$size = 'auto';
		}

		$size = " background-size: $size;";

		// Background Repeat.
		$repeat = get_theme_mod( 'background_repeat', get_theme_support( 'custom-background', 'default-repeat' ) );

		if ( ! in_array( $repeat, array( 'repeat-x', 'repeat-y', 'repeat', 'no-repeat' ), true ) ) {
			$repeat = 'repeat';
		}

		$repeat = " background-repeat: $repeat;";

		// Background Scroll.
		$attachment = get_theme_mod( 'background_attachment', get_theme_support( 'custom-background', 'default-attachment' ) );

		if ( 'fixed' !== $attachment ) {
			$attachment = 'scroll';
		}

		$attachment = " background-attachment: $attachment;";

		$style .= $image . $position . $size . $repeat . $attachment;
	}
	?>
<style<?php echo $type_attr; ?> id="custom-background-css">
body.custom-background { <?php echo trim( $style ); ?> }
</style>
	<?php
}

/**
 * Renders the Custom CSS style element.
 *
 * @since 4.7.0
 */
function wp_custom_css_cb() {
	$styles = wp_get_custom_css();
	if ( $styles || is_customize_preview() ) :
		$type_attr = current_theme_supports( 'html5', 'style' ) ? '' : ' type="text/css"';
		?>
		<style<?php echo $type_attr; ?> id="wp-custom-css">
			<?php echo strip_tags( $styles ); // Note that esc_html() cannot be used because `div &gt; span` is not interpreted properly. ?>
		</style>
		<?php
	endif;
}

/**
 * Fetches the `custom_css` post for a given theme.
 *
 * @since 4.7.0
 *
 * @param string $stylesheet Optional. A theme object stylesheet name. Defaults to the current theme.
 * @return WP_Post|null The custom_css post or null if none exists.
 */
function wp_get_custom_css_post( $stylesheet = '' ) {
	if ( empty( $stylesheet ) ) {
		$stylesheet = get_stylesheet();
	}

	$custom_css_query_vars = array(
		'post_type'              => 'custom_css',
		'post_status'            => get_post_stati(),
		'name'                   => sanitize_title( $stylesheet ),
		'posts_per_page'         => 1,
		'no_found_rows'          => true,
		'cache_results'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'lazy_load_term_meta'    => false,
	);

	$post = null;
	if ( get_stylesheet() === $stylesheet ) {
		$post_id = get_theme_mod( 'custom_css_post_id' );

		if ( $post_id > 0 && get_post( $post_id ) ) {
			$post = get_post( $post_id );
		}

		// `-1` indicates no post exists; no query necessary.
		if ( ! $post && -1 !== $post_id ) {
			$query = new WP_Query( $custom_css_query_vars );
			$post  = $query->post;
			/*
			 * Cache the lookup. See wp_update_custom_css_post().
			 * @todo This should get cleared if a custom_css post is added/removed.
			 */
			set_theme_mod( 'custom_css_post_id', $post ? $post->ID : -1 );
		}
	} else {
		$query = new WP_Query( $custom_css_query_vars );
		$post  = $query->post;
	}

	return $post;
}

/**
 * Fetches the saved Custom CSS content for rendering.
 *
 * @since 4.7.0
 *
 * @param string $stylesheet Optional. A theme object stylesheet name. Defaults to the current theme.
 * @return string The Custom CSS Post content.
 */
function wp_get_custom_css( $stylesheet = '' ) {
	$css = '';

	if ( empty( $stylesheet ) ) {
		$stylesheet = get_stylesheet();
	}

	$post = wp_get_custom_css_post( $stylesheet );
	if ( $post ) {
		$css = $post->post_content;
	}

	/**
	 * Filters the Custom CSS Output into the <head>.
	 *
	 * @since 4.7.0
	 *
	 * @param string $css        CSS pulled in from the Custom CSS CPT.
	 * @param string $stylesheet The theme stylesheet name.
	 */
	$css = apply_filters( 'wp_get_custom_css', $css, $stylesheet );

	return $css;
}

/**
 * Updates the `custom_css` post for a given theme.
 *
 * Inserts a `custom_css` post when one doesn't yet exist.
 *
 * @since 4.7.0
 *
 * @param string $css CSS, stored in `post_content`.
 * @param array  $args {
 *     Args.
 *
 *     @type string $preprocessed Pre-processed CSS, stored in `post_content_filtered`. Normally empty string. Optional.
 *     @type string $stylesheet   Stylesheet (child theme) to update. Optional, defaults to current theme/stylesheet.
 * }
 * @return WP_Post|WP_Error Post on success, error on failure.
 */
function wp_update_custom_css_post( $css, $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'preprocessed' => '',
			'stylesheet'   => get_stylesheet(),
		)
	);

	$data = array(
		'css'          => $css,
		'preprocessed' => $args['preprocessed'],
	);

	/**
	 * Filters the `css` (`post_content`) and `preprocessed` (`post_content_filtered`) args for a `custom_css` post being updated.
	 *
	 * This filter can be used by plugin that offer CSS pre-processors, to store the original
	 * pre-processed CSS in `post_content_filtered` and then store processed CSS in `post_content`.
	 * When used in this way, the `post_content_filtered` should be supplied as the setting value
	 * instead of `post_content` via a the `customize_value_custom_css` filter, for example:
	 *
	 * <code>
	 * add_filter( 'customize_value_custom_css', function( $value, $setting ) {
	 *     $post = wp_get_custom_css_post( $setting->stylesheet );
	 *     if ( $post && ! empty( $post->post_content_filtered ) ) {
	 *         $css = $post->post_content_filtered;
	 *     }
	 *     return $css;
	 * }, 10, 2 );
	 * </code>
	 *
	 * @since 4.7.0
	 * @param array $data {
	 *     Custom CSS data.
	 *
	 *     @type string $css          CSS stored in `post_content`.
	 *     @type string $preprocessed Pre-processed CSS stored in `post_content_filtered`. Normally empty string.
	 * }
	 * @param array $args {
	 *     The args passed into `wp_update_custom_css_post()` merged with defaults.
	 *
	 *     @type string $css          The original CSS passed in to be updated.
	 *     @type string $preprocessed The original preprocessed CSS passed in to be updated.
	 *     @type string $stylesheet   The stylesheet (theme) being updated.
	 * }
	 */
	$data = apply_filters( 'update_custom_css_data', $data, array_merge( $args, compact( 'css' ) ) );

	$post_data = array(
		'post_title'            => $args['stylesheet'],
		'post_name'             => sanitize_title( $args['stylesheet'] ),
		'post_type'             => 'custom_css',
		'post_status'           => 'publish',
		'post_content'          => $data['css'],
		'post_content_filtered' => $data['preprocessed'],
	);

	// Update post if it already exists, otherwise create a new one.
	$post = wp_get_custom_css_post( $args['stylesheet'] );
	if ( $post ) {
		$post_data['ID'] = $post->ID;
		$r               = wp_update_post( wp_slash( $post_data ), true );
	} else {
		$r = wp_insert_post( wp_slash( $post_data ), true );

		if ( ! is_wp_error( $r ) ) {
			if ( get_stylesheet() === $args['stylesheet'] ) {
				set_theme_mod( 'custom_css_post_id', $r );
			}

			// Trigger creation of a revision. This should be removed once #30854 is resolved.
			if ( 0 === count( wp_get_post_revisions( $r ) ) ) {
				wp_save_post_revision( $r );
			}
		}
	}

	if ( is_wp_error( $r ) ) {
		return $r;
	}
	return get_post( $r );
}

/**
 * Adds callback for custom TinyMCE editor stylesheets.
 *
 * The parameter $stylesheet is the name of the stylesheet, relative to
 * the theme root. It also accepts an array of stylesheets.
 * It is optional and defaults to 'editor-style.css'.
 *
 * This function automatically adds another stylesheet with -rtl prefix, e.g. editor-style-rtl.css.
 * If that file doesn't exist, it is removed before adding the stylesheet(s) to TinyMCE.
 * If an array of stylesheets is passed to add_editor_style(),
 * RTL is only added for the first stylesheet.
 *
 * Since version 3.4 the TinyMCE body has .rtl CSS class.
 * It is a better option to use that class and add any RTL styles to the main stylesheet.
 *
 * @since 3.0.0
 *
 * @global array $editor_styles
 *
 * @param array|string $stylesheet Optional. Stylesheet name or array thereof, relative to theme root.
 *                                 Defaults to 'editor-style.css'
 */
function add_editor_style( $stylesheet = 'editor-style.css' ) {
	global $editor_styles;

	add_theme_support( 'editor-style' );

	$editor_styles = (array) $editor_styles;
	$stylesheet    = (array) $stylesheet;

	if ( is_rtl() ) {
		$rtl_stylesheet = str_replace( '.css', '-rtl.css', $stylesheet[0] );
		$stylesheet[]   = $rtl_stylesheet;
	}

	$editor_styles = array_merge( $editor_styles, $stylesheet );
}

/**
 * Removes all visual editor stylesheets.
 *
 * @since 3.1.0
 *
 * @global array $editor_styles
 *
 * @return bool True on success, false if there were no stylesheets to remove.
 */
function remove_editor_styles() {
	if ( ! current_theme_supports( 'editor-style' ) ) {
		return false;
	}
	_remove_theme_support( 'editor-style' );
	if ( is_admin() ) {
		$GLOBALS['editor_styles'] = array();
	}
	return true;
}

/**
 * Retrieves any registered editor stylesheet URLs.
 *
 * @since 4.0.0
 *
 * @global array $editor_styles Registered editor stylesheets
 *
 * @return string[] If registered, a list of editor stylesheet URLs.
 */
function get_editor_stylesheets() {
	$stylesheets = array();
	// Load editor_style.css if the current theme supports it.
	if ( ! empty( $GLOBALS['editor_styles'] ) && is_array( $GLOBALS['editor_styles'] ) ) {
		$editor_styles = $GLOBALS['editor_styles'];

		$editor_styles = array_unique( array_filter( $editor_styles ) );
		$style_uri     = get_stylesheet_directory_uri();
		$style_dir     = get_stylesheet_directory();

		// Support externally referenced styles (like, say, fonts).
		foreach ( $editor_styles as $key => $file ) {
			if ( preg_match( '~^(https?:)?//~', $file ) ) {
				$stylesheets[] = esc_url_raw( $file );
				unset( $editor_styles[ $key ] );
			}
		}

		// Look in a parent theme first, that way child theme CSS overrides.
		if ( is_child_theme() ) {
			$template_uri = get_template_directory_uri();
			$template_dir = get_template_directory();

			foreach ( $editor_styles as $key => $file ) {
				if ( $file && file_exists( "$template_dir/$file" ) ) {
					$stylesheets[] = "$template_uri/$file";
				}
			}
		}

		foreach ( $editor_styles as $file ) {
			if ( $file && file_exists( "$style_dir/$file" ) ) {
				$stylesheets[] = "$style_uri/$file";
			}
		}
	}

	/**
	 * Filters the array of URLs of stylesheets applied to the editor.
	 *
	 * @since 4.3.0
	 *
	 * @param string[] $stylesheets Array of URLs of stylesheets to be applied to the editor.
	 */
	return apply_filters( 'editor_stylesheets', $stylesheets );
}

/**
 * Expands a theme's starter content configuration using core-provided data.
 *
 * @since 4.7.0
 *
 * @return array Array of starter content.
 */
function get_theme_starter_content() {
	$theme_support = get_theme_support( 'starter-content' );
	if ( is_array( $theme_support ) && ! empty( $theme_support[0] ) && is_array( $theme_support[0] ) ) {
		$config = $theme_support[0];
	} else {
		$config = array();
	}

	$core_content = array(
		'widgets'   => array(
			'text_business_info' => array(
				'text',
				array(
					'title'  => _x( 'Find Us', 'Theme starter content' ),
					'text'   => implode(
						'',
						array(
							'<strong>' . _x( 'Address', 'Theme starter content' ) . "</strong>\n",
							_x( '123 Main Street', 'Theme starter content' ) . "\n" . _x( 'New York, NY 10001', 'Theme starter content' ) . "\n\n",
							'<strong>' . _x( 'Hours', 'Theme starter content' ) . "</strong>\n",
							_x( 'Monday&ndash;Friday: 9:00AM&ndash;5:00PM', 'Theme starter content' ) . "\n" . _x( 'Saturday &amp; Sunday: 11:00AM&ndash;3:00PM', 'Theme starter content' ),
						)
					),
					'filter' => true,
					'visual' => true,
				),
			),
			'text_about'         => array(
				'text',
				array(
					'title'  => _x( 'About This Site', 'Theme starter content' ),
					'text'   => _x( 'This may be a good place to introduce yourself and your site or include some credits.', 'Theme starter content' ),
					'filter' => true,
					'visual' => true,
				),
			),
			'archives'           => array(
				'archives',
				array(
					'title' => _x( 'Archives', 'Theme starter content' ),
				),
			),
			'calendar'           => array(
				'calendar',
				array(
					'title' => _x( 'Calendar', 'Theme starter content' ),
				),
			),
			'categories'         => array(
				'categories',
				array(
					'title' => _x( 'Categories', 'Theme starter content' ),
				),
			),
			'meta'               => array(
				'meta',
				array(
					'title' => _x( 'Meta', 'Theme starter content' ),
				),
			),
			'recent-comments'    => array(
				'recent-comments',
				array(
					'title' => _x( 'Recent Comments', 'Theme starter content' ),
				),
			),
			'recent-posts'       => array(
				'recent-posts',
				array(
					'title' => _x( 'Recent Posts', 'Theme starter content' ),
				),
			),
			'search'             => array(
				'search',
				array(
					'title' => _x( 'Search', 'Theme starter content' ),
				),
			),
		),
		'nav_menus' => array(
			'link_home'       => array(
				'type'  => 'custom',
				'title' => _x( 'Home', 'Theme starter content' ),
				'url'   => home_url( '/' ),
			),
			'page_home'       => array( // Deprecated in favor of 'link_home'.
				'type'      => 'post_type',
				'object'    => 'page',
				'object_id' => '{{home}}',
			),
			'page_about'      => array(
				'type'      => 'post_type',
				'object'    => 'page',
				'object_id' => '{{about}}',
			),
			'page_blog'       => array(
				'type'      => 'post_type',
				'object'    => 'page',
				'object_id' => '{{blog}}',
			),
			'page_news'       => array(
				'type'      => 'post_type',
				'object'    => 'page',
				'object_id' => '{{news}}',
			),
			'page_contact'    => array(
				'type'      => 'post_type',
				'object'    => 'page',
				'object_id' => '{{contact}}',
			),

			'link_email'      => array(
				'title' => _x( 'Email', 'Theme starter content' ),
				'url'   => 'mailto:wordpress@example.com',
			),
			'link_facebook'   => array(
				'title' => _x( 'Facebook', 'Theme starter content' ),
				'url'   => 'https://www.facebook.com/wordpress',
			),
			'link_foursquare' => array(
				'title' => _x( 'Foursquare', 'Theme starter content' ),
				'url'   => 'https://foursquare.com/',
			),
			'link_github'     => array(
				'title' => _x( 'GitHub', 'Theme starter content' ),
				'url'   => 'https://github.com/wordpress/',
			),
			'link_instagram'  => array(
				'title' => _x( 'Instagram', 'Theme starter content' ),
				'url'   => 'https://www.instagram.com/explore/tags/wordcamp/',
			),
			'link_linkedin'   => array(
				'title' => _x( 'LinkedIn', 'Theme starter content' ),
				'url'   => 'https://www.linkedin.com/company/1089783',
			),
			'link_pinterest'  => array(
				'title' => _x( 'Pinterest', 'Theme starter content' ),
				'url'   => 'https://www.pinterest.com/',
			),
			'link_twitter'    => array(
				'title' => _x( 'Twitter', 'Theme starter content' ),
				'url'   => 'https://twitter.com/wordpress',
			),
			'link_yelp'       => array(
				'title' => _x( 'Yelp', 'Theme starter content' ),
				'url'   => 'https://www.yelp.com',
			),
			'link_youtube'    => array(
				'title' => _x( 'YouTube', 'Theme starter content' ),
				'url'   => 'https://www.youtube.com/channel/UCdof4Ju7amm1chz1gi1T2ZA',
			),
		),
		'posts'     => array(
			'home'             => array(
				'post_type'    => 'page',
				'post_title'   => _x( 'Home', 'Theme starter content' ),
				'post_content' => sprintf(
					"<!-- wp:paragraph -->\n<p>%s</p>\n<!-- /wp:paragraph -->",
					_x( 'Welcome to your site! This is your homepage, which is what most visitors will see when they come to your site for the first time.', 'Theme starter content' )
				),
			),
			'about'            => array(
				'post_type'    => 'page',
				'post_title'   => _x( 'About', 'Theme starter content' ),
				'post_content' => sprintf(
					"<!-- wp:paragraph -->\n<p>%s</p>\n<!-- /wp:paragraph -->",
					_x( 'You might be an artist who would like to introduce yourself and your work here or maybe you&rsquo;re a business with a mission to describe.', 'Theme starter content' )
				),
			),
			'contact'          => array(
				'post_type'    => 'page',
				'post_title'   => _x( 'Contact', 'Theme starter content' ),
				'post_content' => sprintf(
					"<!-- wp:paragraph -->\n<p>%s</p>\n<!-- /wp:paragraph -->",
					_x( 'This is a page with some basic contact information, such as an address and phone number. You might also try a plugin to add a contact form.', 'Theme starter content' )
				),
			),
			'blog'             => array(
				'post_type'  => 'page',
				'post_title' => _x( 'Blog', 'Theme starter content' ),
			),
			'news'             => array(
				'post_type'  => 'page',
				'post_title' => _x( 'News', 'Theme starter content' ),
			),

			'homepage-section' => array(
				'post_type'    => 'page',
				'post_title'   => _x( 'A homepage section', 'Theme starter content' ),
				'post_content' => sprintf(
					"<!-- wp:paragraph -->\n<p>%s</p>\n<!-- /wp:paragraph -->",
					_x( 'This is an example of a homepage section. Homepage sections can be any page other than the homepage itself, including the page that shows your latest blog posts.', 'Theme starter content' )
				),
			),
		),
	);

	$content = array();

	foreach ( $config as $type => $args ) {
		switch ( $type ) {
			// Use options and theme_mods as-is.
			case 'options':
			case 'theme_mods':
				$content[ $type ] = $config[ $type ];
				break;

			// Widgets are grouped into sidebars.
			case 'widgets':
				foreach ( $config[ $type ] as $sidebar_id => $widgets ) {
					foreach ( $widgets as $id => $widget ) {
						if ( is_array( $widget ) ) {

							// Item extends core content.
							if ( ! empty( $core_content[ $type ][ $id ] ) ) {
								$widget = array(
									$core_content[ $type ][ $id ][0],
									array_merge( $core_content[ $type ][ $id ][1], $widget ),
								);
							}

							$content[ $type ][ $sidebar_id ][] = $widget;
						} elseif ( is_string( $widget ) && ! empty( $core_content[ $type ] ) && ! empty( $core_content[ $type ][ $widget ] ) ) {
							$content[ $type ][ $sidebar_id ][] = $core_content[ $type ][ $widget ];
						}
					}
				}
				break;

			// And nav menu items are grouped into nav menus.
			case 'nav_menus':
				foreach ( $config[ $type ] as $nav_menu_location => $nav_menu ) {

					// Ensure nav menus get a name.
					if ( empty( $nav_menu['name'] ) ) {
						$nav_menu['name'] = $nav_menu_location;
					}

					$content[ $type ][ $nav_menu_location ]['name'] = $nav_menu['name'];

					foreach ( $nav_menu['items'] as $id => $nav_menu_item ) {
						if ( is_array( $nav_menu_item ) ) {

							// Item extends core content.
							if ( ! empty( $core_content[ $type ][ $id ] ) ) {
								$nav_menu_item = array_merge( $core_content[ $type ][ $id ], $nav_menu_item );
							}

							$content[ $type ][ $nav_menu_location ]['items'][] = $nav_menu_item;
						} elseif ( is_string( $nav_menu_item ) && ! empty( $core_content[ $type ] ) && ! empty( $core_content[ $type ][ $nav_menu_item ] ) ) {
							$content[ $type ][ $nav_menu_location ]['items'][] = $core_content[ $type ][ $nav_menu_item ];
						}
					}
				}
				break;

			// Attachments are posts but have special treatment.
			case 'attachments':
				foreach ( $config[ $type ] as $id => $item ) {
					if ( ! empty( $item['file'] ) ) {
						$content[ $type ][ $id ] = $item;
					}
				}
				break;

			// All that's left now are posts (besides attachments).
			// Not a default case for the sake of clarity and future work.
			case 'posts':
				foreach ( $config[ $type ] as $id => $item ) {
					if ( is_array( $item ) ) {

						// Item extends core content.
						if ( ! empty( $core_content[ $type ][ $id ] ) ) {
							$item = array_merge( $core_content[ $type ][ $id ], $item );
						}

						// Enforce a subset of fields.
						$content[ $type ][ $id ] = wp_array_slice_assoc(
							$item,
							array(
								'post_type',
								'post_title',
								'post_excerpt',
								'post_name',
								'post_content',
								'menu_order',
								'comment_status',
								'thumbnail',
								'template',
							)
						);
					} elseif ( is_string( $item ) && ! empty( $core_content[ $type ][ $item ] ) ) {
						$content[ $type ][ $item ] = $core_content[ $type ][ $item ];
					}
				}
				break;
		}
	}

	/**
	 * Filters the expanded array of starter content.
	 *
	 * @since 4.7.0
	 *
	 * @param array $content Array of starter content.
	 * @param array $config  Array of theme-specific starter content configuration.
	 */
	return apply_filters( 'get_theme_starter_content', $content, $config );
}

/**
 * Registers theme support for a given feature.
 *
 * Must be called in the theme's functions.php file to work.
 * If attached to a hook, it must be {@see 'after_setup_theme'}.
 * The {@see 'init'} hook may be too late for some features.
 *
 * Example usage:
 *
 *     add_theme_support( 'title-tag' );
 *     add_theme_support( 'custom-logo', array(
 *         'height' => 480,
 *         'width'  => 720,
 *     ) );
 *
 * @since 2.9.0
 * @since 3.4.0 The `custom-header-uploads` feature was deprecated.
 * @since 3.6.0 The `html5` feature was added.
 * @since 3.9.0 The `html5` feature now also accepts 'gallery' and 'caption'.
 * @since 4.1.0 The `title-tag` feature was added.
 * @since 4.5.0 The `customize-selective-refresh-widgets` feature was added.
 * @since 4.7.0 The `starter-content` feature was added.
 * @since 5.0.0 The `responsive-embeds`, `align-wide`, `dark-editor-style`, `disable-custom-colors`,
 *              `disable-custom-font-sizes`, `editor-color-palette`, `editor-font-sizes`,
 *              `editor-styles`, and `wp-block-styles` features were added.
 * @since 5.3.0 The `html5` feature now also accepts 'script' and 'style'.
 * @since 5.3.0 Formalized the existing and already documented `...$args` parameter
 *              by adding it to the function signature.
 * @since 5.5.0 The `core-block-patterns` feature was added and is enabled by default.
 * @since 5.5.0 The `custom-logo` feature now also accepts 'unlink-homepage-logo'.
 * @since 5.6.0 The `post-formats` feature warns if no array is passed.
 *
 * @global array $_wp_theme_features
 *
 * @param string $feature The feature being added. Likely core values include 'post-formats', 'post-thumbnails',
 *                        'custom-header', 'custom-background', 'custom-logo', 'menus', 'automatic-feed-links',
 *                        'html5', 'title-tag', 'customize-selective-refresh-widgets', 'starter-content',
 *                        'responsive-embeds', 'align-wide', 'dark-editor-style', 'disable-custom-colors',
 *                        'disable-custom-font-sizes', 'editor-color-palette', 'editor-font-sizes',
 *                        'editor-styles', 'wp-block-styles', and 'core-block-patterns'.
 * @param mixed  ...$args Optional extra arguments to pass along with certain features.
 * @return void|bool False on failure, void otherwise.
 */
function add_theme_support( $feature, ...$args ) {
	global $_wp_theme_features;

	if ( ! $args ) {
		$args = true;
	}

	switch ( $feature ) {
		case 'post-thumbnails':
			// All post types are already supported.
			if ( true === get_theme_support( 'post-thumbnails' ) ) {
				return;
			}

			/*
			 * Merge post types with any that already declared their support
			 * for post thumbnails.
			 */
			if ( isset( $args[0] ) && is_array( $args[0] ) && isset( $_wp_theme_features['post-thumbnails'] ) ) {
				$args[0] = array_unique( array_merge( $_wp_theme_features['post-thumbnails'][0], $args[0] ) );
			}

			break;

		case 'post-formats':
			if ( isset( $args[0] ) && is_array( $args[0] ) ) {
				$post_formats = get_post_format_slugs();
				unset( $post_formats['standard'] );

				$args[0] = array_intersect( $args[0], array_keys( $post_formats ) );
			} else {
				_doing_it_wrong( "add_theme_support( 'post-formats' )", __( 'You need to pass an array of post formats.' ), '5.6.0' );
				return false;
			}
			break;

		case 'html5':
			// You can't just pass 'html5', you need to pass an array of types.
			if ( empty( $args[0] ) ) {
				// Build an array of types for back-compat.
				$args = array( 0 => array( 'comment-list', 'comment-form', 'search-form' ) );
			} elseif ( ! isset( $args[0] ) || ! is_array( $args[0] ) ) {
				_doing_it_wrong( "add_theme_support( 'html5' )", __( 'You need to pass an array of types.' ), '3.6.1' );
				return false;
			}

			// Calling 'html5' again merges, rather than overwrites.
			if ( isset( $_wp_theme_features['html5'] ) ) {
				$args[0] = array_merge( $_wp_theme_features['html5'][0], $args[0] );
			}
			break;

		case 'custom-logo':
			if ( true === $args ) {
				$args = array( 0 => array() );
			}
			$defaults = array(
				'width'                => null,
				'height'               => null,
				'flex-width'           => false,
				'flex-height'          => false,
				'header-text'          => '',
				'unlink-homepage-logo' => false,
			);
			$args[0]  = wp_parse_args( array_intersect_key( $args[0], $defaults ), $defaults );

			// Allow full flexibility if no size is specified.
			if ( is_null( $args[0]['width'] ) && is_null( $args[0]['height'] ) ) {
				$args[0]['flex-width']  = true;
				$args[0]['flex-height'] = true;
			}
			break;

		case 'custom-header-uploads':
			return add_theme_support( 'custom-header', array( 'uploads' => true ) );

		case 'custom-header':
			if ( true === $args ) {
				$args = array( 0 => array() );
			}

			$defaults = array(
				'default-image'          => '',
				'random-default'         => false,
				'width'                  => 0,
				'height'                 => 0,
				'flex-height'            => false,
				'flex-width'             => false,
				'default-text-color'     => '',
				'header-text'            => true,
				'uploads'                => true,
				'wp-head-callback'       => '',
				'admin-head-callback'    => '',
				'admin-preview-callback' => '',
				'video'                  => false,
				'video-active-callback'  => 'is_front_page',
			);

			$jit = isset( $args[0]['__jit'] );
			unset( $args[0]['__jit'] );

			// Merge in data from previous add_theme_support() calls.
			// The first value registered wins. (A child theme is set up first.)
			if ( isset( $_wp_theme_features['custom-header'] ) ) {
				$args[0] = wp_parse_args( $_wp_theme_features['custom-header'][0], $args[0] );
			}

			// Load in the defaults at the end, as we need to insure first one wins.
			// This will cause all constants to be defined, as each arg will then be set to the default.
			if ( $jit ) {
				$args[0] = wp_parse_args( $args[0], $defaults );
			}

			/*
			 * If a constant was defined, use that value. Otherwise, define the constant to ensure
			 * the constant is always accurate (and is not defined later,  overriding our value).
			 * As stated above, the first value wins.
			 * Once we get to wp_loaded (just-in-time), define any constants we haven't already.
			 * Constants are lame. Don't reference them. This is just for backward compatibility.
			 */

			if ( defined( 'NO_HEADER_TEXT' ) ) {
				$args[0]['header-text'] = ! NO_HEADER_TEXT;
			} elseif ( isset( $args[0]['header-text'] ) ) {
				define( 'NO_HEADER_TEXT', empty( $args[0]['header-text'] ) );
			}

			if ( defined( 'HEADER_IMAGE_WIDTH' ) ) {
				$args[0]['width'] = (int) HEADER_IMAGE_WIDTH;
			} elseif ( isset( $args[0]['width'] ) ) {
				define( 'HEADER_IMAGE_WIDTH', (int) $args[0]['width'] );
			}

			if ( defined( 'HEADER_IMAGE_HEIGHT' ) ) {
				$args[0]['height'] = (int) HEADER_IMAGE_HEIGHT;
			} elseif ( isset( $args[0]['height'] ) ) {
				define( 'HEADER_IMAGE_HEIGHT', (int) $args[0]['height'] );
			}

			if ( defined( 'HEADER_TEXTCOLOR' ) ) {
				$args[0]['default-text-color'] = HEADER_TEXTCOLOR;
			} elseif ( isset( $args[0]['default-text-color'] ) ) {
				define( 'HEADER_TEXTCOLOR', $args[0]['default-text-color'] );
			}

			if ( defined( 'HEADER_IMAGE' ) ) {
				$args[0]['default-image'] = HEADER_IMAGE;
			} elseif ( isset( $args[0]['default-image'] ) ) {
				define( 'HEADER_IMAGE', $args[0]['default-image'] );
			}

			if ( $jit && ! empty( $args[0]['default-image'] ) ) {
				$args[0]['random-default'] = false;
			}

			// If headers are supported, and we still don't have a defined width or height,
			// we have implicit flex sizes.
			if ( $jit ) {
				if ( empty( $args[0]['width'] ) && empty( $args[0]['flex-width'] ) ) {
					$args[0]['flex-width'] = true;
				}
				if ( empty( $args[0]['height'] ) && empty( $args[0]['flex-height'] ) ) {
					$args[0]['flex-height'] = true;
				}
			}

			break;

		case 'custom-background':
			if ( true === $args ) {
				$args = array( 0 => array() );
			}

			$defaults = array(
				'default-image'          => '',
				'default-preset'         => 'default',
				'default-position-x'     => 'left',
				'default-position-y'     => 'top',
				'default-size'           => 'auto',
				'default-repeat'         => 'repeat',
				'default-attachment'     => 'scroll',
				'default-color'          => '',
				'wp-head-callback'       => '_custom_background_cb',
				'admin-head-callback'    => '',
				'admin-preview-callback' => '',
			);

			$jit = isset( $args[0]['__jit'] );
			unset( $args[0]['__jit'] );

			// Merge in data from previous add_theme_support() calls. The first value registered wins.
			if ( isset( $_wp_theme_features['custom-background'] ) ) {
				$args[0] = wp_parse_args( $_wp_theme_features['custom-background'][0], $args[0] );
			}

			if ( $jit ) {
				$args[0] = wp_parse_args( $args[0], $defaults );
			}

			if ( defined( 'BACKGROUND_COLOR' ) ) {
				$args[0]['default-color'] = BACKGROUND_COLOR;
			} elseif ( isset( $args[0]['default-color'] ) || $jit ) {
				define( 'BACKGROUND_COLOR', $args[0]['default-color'] );
			}

			if ( defined( 'BACKGROUND_IMAGE' ) ) {
				$args[0]['default-image'] = BACKGROUND_IMAGE;
			} elseif ( isset( $args[0]['default-image'] ) || $jit ) {
				define( 'BACKGROUND_IMAGE', $args[0]['default-image'] );
			}

			break;

		// Ensure that 'title-tag' is accessible in the admin.
		case 'title-tag':
			// Can be called in functions.php but must happen before wp_loaded, i.e. not in header.php.
			if ( did_action( 'wp_loaded' ) ) {
				_doing_it_wrong(
					"add_theme_support( 'title-tag' )",
					sprintf(
						/* translators: 1: title-tag, 2: wp_loaded */
						__( 'Theme support for %1$s should be registered before the %2$s hook.' ),
						'<code>title-tag</code>',
						'<code>wp_loaded</code>'
					),
					'4.1.0'
				);

				return false;
			}
	}

	$_wp_theme_features[ $feature ] = $args;
}

/**
 * Registers the internal custom header and background routines.
 *
 * @since 3.4.0
 * @access private
 *
 * @global Custom_Image_Header $custom_image_header
 * @global Custom_Background   $custom_background
 */
function _custom_header_background_just_in_time() {
	global $custom_image_header, $custom_background;

	if ( current_theme_supports( 'custom-header' ) ) {
		// In case any constants were defined after an add_custom_image_header() call, re-run.
		add_theme_support( 'custom-header', array( '__jit' => true ) );

		$args = get_theme_support( 'custom-header' );
		if ( $args[0]['wp-head-callback'] ) {
			add_action( 'wp_head', $args[0]['wp-head-callback'] );
		}

		if ( is_admin() ) {
			require_once ABSPATH . 'wp-admin/includes/class-custom-image-header.php';
			$custom_image_header = new Custom_Image_Header( $args[0]['admin-head-callback'], $args[0]['admin-preview-callback'] );
		}
	}

	if ( current_theme_supports( 'custom-background' ) ) {
		// In case any constants were defined after an add_custom_background() call, re-run.
		add_theme_support( 'custom-background', array( '__jit' => true ) );

		$args = get_theme_support( 'custom-background' );
		add_action( 'wp_head', $args[0]['wp-head-callback'] );

		if ( is_admin() ) {
			require_once ABSPATH . 'wp-admin/includes/class-custom-background.php';
			$custom_background = new Custom_Background( $args[0]['admin-head-callback'], $args[0]['admin-preview-callback'] );
		}
	}
}

/**
 * Adds CSS to hide header text for custom logo, based on Customizer setting.
 *
 * @since 4.5.0
 * @access private
 */
function _custom_logo_header_styles() {
	if ( ! current_theme_supports( 'custom-header', 'header-text' ) && get_theme_support( 'custom-logo', 'header-text' ) && ! get_theme_mod( 'header_text', true ) ) {
		$classes = (array) get_theme_support( 'custom-logo', 'header-text' );
		$classes = array_map( 'sanitize_html_class', $classes );
		$classes = '.' . implode( ', .', $classes );

		$type_attr = current_theme_supports( 'html5', 'style' ) ? '' : ' type="text/css"';
		?>
		<!-- Custom Logo: hide header text -->
		<style id="custom-logo-css"<?php echo $type_attr; ?>>
			<?php echo $classes; ?> {
				position: absolute;
				clip: rect(1px, 1px, 1px, 1px);
			}
		</style>
		<?php
	}
}

/**
 * Gets the theme support arguments passed when registering that support.
 *
 * Example usage:
 *
 *     get_theme_support( 'custom-logo' );
 *     get_theme_support( 'custom-header', 'width' );
 *
 * @since 3.1.0
 * @since 5.3.0 Formalized the existing and already documented `...$args` parameter
 *              by adding it to the function signature.
 *
 * @global array $_wp_theme_features
 *
 * @param string $feature The feature to check. See add_theme_support() for the list
 *                        of possible values.
 * @param mixed  ...$args Optional extra arguments to be checked against certain features.
 * @return mixed The array of extra arguments or the value for the registered feature.
 */
function get_theme_support( $feature, ...$args ) {
	global $_wp_theme_features;
	if ( ! isset( $_wp_theme_features[ $feature ] ) ) {
		return false;
	}

	if ( ! $args ) {
		return $_wp_theme_features[ $feature ];
	}

	switch ( $feature ) {
		case 'custom-logo':
		case 'custom-header':
		case 'custom-background':
			if ( isset( $_wp_theme_features[ $feature ][0][ $args[0] ] ) ) {
				return $_wp_theme_features[ $feature ][0][ $args[0] ];
			}
			return false;

		default:
			return $_wp_theme_features[ $feature ];
	}
}

/**
 * Allows a theme to de-register its support of a certain feature
 *
 * Should be called in the theme's functions.php file. Generally would
 * be used for child themes to override support from the parent theme.
 *
 * @since 3.0.0
 *
 * @see add_theme_support()
 *
 * @param string $feature The feature being removed. See add_theme_support() for the list
 *                        of possible values.
 * @return bool|void Whether feature was removed.
 */
function remove_theme_support( $feature ) {
	// Do not remove internal registrations that are not used directly by themes.
	if ( in_array( $feature, array( 'editor-style', 'widgets', 'menus' ), true ) ) {
		return false;
	}

	return _remove_theme_support( $feature );
}

/**
 * Do not use. Removes theme support internally without knowledge of those not used
 * by themes directly.
 *
 * @access private
 * @since 3.1.0
 * @global array               $_wp_theme_features
 * @global Custom_Image_Header $custom_image_header
 * @global Custom_Background   $custom_background
 *
 * @param string $feature The feature being removed. See add_theme_support() for the list
 *                        of possible values.
 * @return bool True if support was removed, false if the feature was not registered.
 */
function _remove_theme_support( $feature ) {
	global $_wp_theme_features;

	switch ( $feature ) {
		case 'custom-header-uploads':
			if ( ! isset( $_wp_theme_features['custom-header'] ) ) {
				return false;
			}
			add_theme_support( 'custom-header', array( 'uploads' => false ) );
			return; // Do not continue - custom-header-uploads no longer exists.
	}

	if ( ! isset( $_wp_theme_features[ $feature ] ) ) {
		return false;
	}

	switch ( $feature ) {
		case 'custom-header':
			if ( ! did_action( 'wp_loaded' ) ) {
				break;
			}
			$support = get_theme_support( 'custom-header' );
			if ( isset( $support[0]['wp-head-callback'] ) ) {
				remove_action( 'wp_head', $support[0]['wp-head-callback'] );
			}
			if ( isset( $GLOBALS['custom_image_header'] ) ) {
				remove_action( 'admin_menu', array( $GLOBALS['custom_image_header'], 'init' ) );
				unset( $GLOBALS['custom_image_header'] );
			}
			break;

		case 'custom-background':
			if ( ! did_action( 'wp_loaded' ) ) {
				break;
			}
			$support = get_theme_support( 'custom-background' );
			if ( isset( $support[0]['wp-head-callback'] ) ) {
				remove_action( 'wp_head', $support[0]['wp-head-callback'] );
			}
			remove_action( 'admin_menu', array( $GLOBALS['custom_background'], 'init' ) );
			unset( $GLOBALS['custom_background'] );
			break;
	}

	unset( $_wp_theme_features[ $feature ] );

	return true;
}

/**
 * Checks a theme's support for a given feature.
 *
 * Example usage:
 *
 *     current_theme_supports( 'custom-logo' );
 *     current_theme_supports( 'html5', 'comment-form' );
 *
 * @since 2.9.0
 * @since 5.3.0 Formalized the existing and already documented `...$args` parameter
 *              by adding it to the function signature.
 *
 * @global array $_wp_theme_features
 *
 * @param string $feature The feature being checked. See add_theme_support() for the list
 *                        of possible values.
 * @param mixed  ...$args Optional extra arguments to be checked against certain features.
 * @return bool True if the current theme supports the feature, false otherwise.
 */
function current_theme_supports( $feature, ...$args ) {
	global $_wp_theme_features;

	if ( 'custom-header-uploads' === $feature ) {
		return current_theme_supports( 'custom-header', 'uploads' );
	}

	if ( ! isset( $_wp_theme_features[ $feature ] ) ) {
		return false;
	}

	// If no args passed then no extra checks need be performed.
	if ( ! $args ) {
		return true;
	}

	switch ( $feature ) {
		case 'post-thumbnails':
			/*
			 * post-thumbnails can be registered for only certain content/post types
			 * by passing an array of types to add_theme_support().
			 * If no array was passed, then any type is accepted.
			 */
			if ( true === $_wp_theme_features[ $feature ] ) {  // Registered for all types.
				return true;
			}
			$content_type = $args[0];
			return in_array( $content_type, $_wp_theme_features[ $feature ][0], true );

		case 'html5':
		case 'post-formats':
			/*
			 * Specific post formats can be registered by passing an array of types
			 * to add_theme_support().
			 *
			 * Specific areas of HTML5 support *must* be passed via an array to add_theme_support().
			 */
			$type = $args[0];
			return in_array( $type, $_wp_theme_features[ $feature ][0], true );

		case 'custom-logo':
		case 'custom-header':
		case 'custom-background':
			// Specific capabilities can be registered by passing an array to add_theme_support().
			return ( isset( $_wp_theme_features[ $feature ][0][ $args[0] ] ) && $_wp_theme_features[ $feature ][0][ $args[0] ] );
	}

	/**
	 * Filters whether the current theme supports a specific feature.
	 *
	 * The dynamic portion of the hook name, `$feature`, refers to the specific
	 * theme feature. See add_theme_support() for the list of possible values.
	 *
	 * @since 3.4.0
	 *
	 * @param bool   $supports Whether the current theme supports the given feature. Default true.
	 * @param array  $args     Array of arguments for the feature.
	 * @param string $feature  The theme feature.
	 */
	return apply_filters( "current_theme_supports-{$feature}", true, $args, $_wp_theme_features[ $feature ] ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
}

/**
 * Checks a theme's support for a given feature before loading the functions which implement it.
 *
 * @since 2.9.0
 *
 * @param string $feature The feature being checked. See add_theme_support() for the list
 *                        of possible values.
 * @param string $include Path to the file.
 * @return bool True if the current theme supports the supplied feature, false otherwise.
 */
function require_if_theme_supports( $feature, $include ) {
	if ( current_theme_supports( $feature ) ) {
		require $include;
		return true;
	}
	return false;
}

/**
 * Registers a theme feature for use in add_theme_support().
 *
 * This does not indicate that the current theme supports the feature, it only describes
 * the feature's supported options.
 *
 * @since 5.5.0
 *
 * @see add_theme_support()
 *
 * @global array $_wp_registered_theme_features
 *
 * @param string $feature The name uniquely identifying the feature. See add_theme_support()
 *                        for the list of possible values.
 * @param array  $args {
 *     Data used to describe the theme.
 *
 *     @type string     $type         The type of data associated with this feature.
 *                                    Valid values are 'string', 'boolean', 'integer',
 *                                    'number', 'array', and 'object'. Defaults to 'boolean'.
 *     @type boolean    $variadic     Does this feature utilize the variadic support
 *                                    of add_theme_support(), or are all arguments specified
 *                                    as the second parameter. Must be used with the "array" type.
 *     @type string     $description  A short description of the feature. Included in
 *                                    the Themes REST API schema. Intended for developers.
 *     @type bool|array $show_in_rest {
 *         Whether this feature should be included in the Themes REST API endpoint.
 *         Defaults to not being included. When registering an 'array' or 'object' type,
 *         this argument must be an array with the 'schema' key.
 *
 *         @type array    $schema           Specifies the JSON Schema definition describing
 *                                          the feature. If any objects in the schema do not include
 *                                          the 'additionalProperties' keyword, it is set to false.
 *         @type string   $name             An alternate name to be used as the property name
 *                                          in the REST API.
 *         @type callable $prepare_callback A function used to format the theme support in the REST API.
 *                                          Receives the raw theme support value.
 *      }
 * }
 * @return true|WP_Error True if the theme feature was successfully registered, a WP_Error object if not.
 */
function register_theme_feature( $feature, $args = array() ) {
	global $_wp_registered_theme_features;

	if ( ! is_array( $_wp_registered_theme_features ) ) {
		$_wp_registered_theme_features = array();
	}

	$defaults = array(
		'type'         => 'boolean',
		'variadic'     => false,
		'description'  => '',
		'show_in_rest' => false,
	);

	$args = wp_parse_args( $args, $defaults );

	if ( true === $args['show_in_rest'] ) {
		$args['show_in_rest'] = array();
	}

	if ( is_array( $args['show_in_rest'] ) ) {
		$args['show_in_rest'] = wp_parse_args(
			$args['show_in_rest'],
			array(
				'schema'           => array(),
				'name'             => $feature,
				'prepare_callback' => null,
			)
		);
	}

	if ( ! in_array( $args['type'], array( 'string', 'boolean', 'integer', 'number', 'array', 'object' ), true ) ) {
		return new WP_Error(
			'invalid_type',
			__( 'The feature "type" is not valid JSON Schema type.' )
		);
	}

	if ( true === $args['variadic'] && 'array' !== $args['type'] ) {
		return new WP_Error(
			'variadic_must_be_array',
			__( 'When registering a "variadic" theme feature, the "type" must be an "array".' )
		);
	}

	if ( false !== $args['show_in_rest'] && in_array( $args['type'], array( 'array', 'object' ), true ) ) {
		if ( ! is_array( $args['show_in_rest'] ) || empty( $args['show_in_rest']['schema'] ) ) {
			return new WP_Error(
				'missing_schema',
				__( 'When registering an "array" or "object" feature to show in the REST API, the feature\'s schema must also be defined.' )
			);
		}

		if ( 'array' === $args['type'] && ! isset( $args['show_in_rest']['schema']['items'] ) ) {
			return new WP_Error(
				'missing_schema_items',
				__( 'When registering an "array" feature, the feature\'s schema must include the "items" keyword.' )
			);
		}

		if ( 'object' === $args['type'] && ! isset( $args['show_in_rest']['schema']['properties'] ) ) {
			return new WP_Error(
				'missing_schema_properties',
				__( 'When registering an "object" feature, the feature\'s schema must include the "properties" keyword.' )
			);
		}
	}

	if ( is_array( $args['show_in_rest'] ) ) {
		if ( isset( $args['show_in_rest']['prepare_callback'] ) && ! is_callable( $args['show_in_rest']['prepare_callback'] ) ) {
			return new WP_Error(
				'invalid_rest_prepare_callback',
				sprintf(
					/* translators: %s: prepare_callback */
					__( 'The "%s" must be a callable function.' ),
					'prepare_callback'
				)
			);
		}

		$args['show_in_rest']['schema'] = wp_parse_args(
			$args['show_in_rest']['schema'],
			array(
				'description' => $args['description'],
				'type'        => $args['type'],
				'default'     => false,
			)
		);

		if ( is_bool( $args['show_in_rest']['schema']['default'] )
			&& ! in_array( 'boolean', (array) $args['show_in_rest']['schema']['type'], true )
		) {
			// Automatically include the "boolean" type when the default value is a boolean.
			$args['show_in_rest']['schema']['type'] = (array) $args['show_in_rest']['schema']['type'];
			array_unshift( $args['show_in_rest']['schema']['type'], 'boolean' );
		}

		$args['show_in_rest']['schema'] = rest_default_additional_properties_to_false( $args['show_in_rest']['schema'] );
	}

	$_wp_registered_theme_features[ $feature ] = $args;

	return true;
}

/**
 * Gets the list of registered theme features.
 *
 * @since 5.5.0
 *
 * @global array $_wp_registered_theme_features
 *
 * @return array[] List of theme features, keyed by their name.
 */
function get_registered_theme_features() {
	global $_wp_registered_theme_features;

	if ( ! is_array( $_wp_registered_theme_features ) ) {
		return array();
	}

	return $_wp_registered_theme_features;
}

/**
 * Gets the registration config for a theme feature.
 *
 * @since 5.5.0
 *
 * @global array $_wp_registered_theme_features
 *
 * @param string $feature The feature name. See add_theme_support() for the list
 *                        of possible values.
 * @return array|null The registration args, or null if the feature was not registered.
 */
function get_registered_theme_feature( $feature ) {
	global $_wp_registered_theme_features;

	if ( ! is_array( $_wp_registered_theme_features ) ) {
		return null;
	}

	return isset( $_wp_registered_theme_features[ $feature ] ) ? $_wp_registered_theme_features[ $feature ] : null;
}

/**
 * Checks an attachment being deleted to see if it's a header or background image.
 *
 * If true it removes the theme modification which would be pointing at the deleted
 * attachment.
 *
 * @access private
 * @since 3.0.0
 * @since 4.3.0 Also removes `header_image_data`.
 * @since 4.5.0 Also removes custom logo theme mods.
 *
 * @param int $id The attachment ID.
 */
function _delete_attachment_theme_mod( $id ) {
	$attachment_image = wp_get_attachment_url( $id );
	$header_image     = get_header_image();
	$background_image = get_background_image();
	$custom_logo_id   = get_theme_mod( 'custom_logo' );

	if ( $custom_logo_id && $custom_logo_id == $id ) {
		remove_theme_mod( 'custom_logo' );
		remove_theme_mod( 'header_text' );
	}

	if ( $header_image && $header_image == $attachment_image ) {
		remove_theme_mod( 'header_image' );
		remove_theme_mod( 'header_image_data' );
	}

	if ( $background_image && $background_image == $attachment_image ) {
		remove_theme_mod( 'background_image' );
	}
}

/**
 * Checks if a theme has been changed and runs 'after_switch_theme' hook on the next WP load.
 *
 * See {@see 'after_switch_theme'}.
 *
 * @since 3.3.0
 */
function check_theme_switched() {
	$stylesheet = get_option( 'theme_switched' );
	if ( $stylesheet ) {
		$old_theme = wp_get_theme( $stylesheet );

		// Prevent widget & menu mapping from running since Customizer already called it up front.
		if ( get_option( 'theme_switched_via_customizer' ) ) {
			remove_action( 'after_switch_theme', '_wp_menus_changed' );
			remove_action( 'after_switch_theme', '_wp_sidebars_changed' );
			update_option( 'theme_switched_via_customizer', false );
		}

		if ( $old_theme->exists() ) {
			/**
			 * Fires on the first WP load after a theme switch if the old theme still exists.
			 *
			 * This action fires multiple times and the parameters differs
			 * according to the context, if the old theme exists or not.
			 * If the old theme is missing, the parameter will be the slug
			 * of the old theme.
			 *
			 * @since 3.3.0
			 *
			 * @param string   $old_name  Old theme name.
			 * @param WP_Theme $old_theme WP_Theme instance of the old theme.
			 */
			do_action( 'after_switch_theme', $old_theme->get( 'Name' ), $old_theme );
		} else {
			/** This action is documented in wp-includes/theme.php */
			do_action( 'after_switch_theme', $stylesheet, $old_theme );
		}
		flush_rewrite_rules();

		update_option( 'theme_switched', false );
	}
}

/**
 * Includes and instantiates the WP_Customize_Manager class.
 *
 * Loads the Customizer at plugins_loaded when accessing the customize.php admin
 * page or when any request includes a wp_customize=on param or a customize_changeset
 * param (a UUID). This param is a signal for whether to bootstrap the Customizer when
 * WordPress is loading, especially in the Customizer preview
 * or when making Customizer Ajax requests for widgets or menus.
 *
 * @since 3.4.0
 *
 * @global WP_Customize_Manager $wp_customize
 */
function _wp_customize_include() {

	$is_customize_admin_page = ( is_admin() && 'customize.php' === basename( $_SERVER['PHP_SELF'] ) );
	$should_include          = (
		$is_customize_admin_page
		||
		( isset( $_REQUEST['wp_customize'] ) && 'on' === $_REQUEST['wp_customize'] )
		||
		( ! empty( $_GET['customize_changeset_uuid'] ) || ! empty( $_POST['customize_changeset_uuid'] ) )
	);

	if ( ! $should_include ) {
		return;
	}

	/*
	 * Note that wp_unslash() is not being used on the input vars because it is
	 * called before wp_magic_quotes() gets called. Besides this fact, none of
	 * the values should contain any characters needing slashes anyway.
	 */
	$keys       = array( 'changeset_uuid', 'customize_changeset_uuid', 'customize_theme', 'theme', 'customize_messenger_channel', 'customize_autosaved' );
	$input_vars = array_merge(
		wp_array_slice_assoc( $_GET, $keys ),
		wp_array_slice_assoc( $_POST, $keys )
	);

	$theme             = null;
	$autosaved         = null;
	$messenger_channel = null;

	// Value false indicates UUID should be determined after_setup_theme
	// to either re-use existing saved changeset or else generate a new UUID if none exists.
	$changeset_uuid = false;

	// Set initially fo false since defaults to true for back-compat;
	// can be overridden via the customize_changeset_branching filter.
	$branching = false;

	if ( $is_customize_admin_page && isset( $input_vars['changeset_uuid'] ) ) {
		$changeset_uuid = sanitize_key( $input_vars['changeset_uuid'] );
	} elseif ( ! empty( $input_vars['customize_changeset_uuid'] ) ) {
		$changeset_uuid = sanitize_key( $input_vars['customize_changeset_uuid'] );
	}

	// Note that theme will be sanitized via WP_Theme.
	if ( $is_customize_admin_page && isset( $input_vars['theme'] ) ) {
		$theme = $input_vars['theme'];
	} elseif ( isset( $input_vars['customize_theme'] ) ) {
		$theme = $input_vars['customize_theme'];
	}

	if ( ! empty( $input_vars['customize_autosaved'] ) ) {
		$autosaved = true;
	}

	if ( isset( $input_vars['customize_messenger_channel'] ) ) {
		$messenger_channel = sanitize_key( $input_vars['customize_messenger_channel'] );
	}

	/*
	 * Note that settings must be previewed even outside the customizer preview
	 * and also in the customizer pane itself. This is to enable loading an existing
	 * changeset into the customizer. Previewing the settings only has to be prevented
	 * here in the case of a customize_save action because this will cause WP to think
	 * there is nothing changed that needs to be saved.
	 */
	$is_customize_save_action = (
		wp_doing_ajax()
		&&
		isset( $_REQUEST['action'] )
		&&
		'customize_save' === wp_unslash( $_REQUEST['action'] )
	);
	$settings_previewed       = ! $is_customize_save_action;

	require_once ABSPATH . WPINC . '/class-wp-customize-manager.php';
	$GLOBALS['wp_customize'] = new WP_Customize_Manager( compact( 'changeset_uuid', 'theme', 'messenger_channel', 'settings_previewed', 'autosaved', 'branching' ) );
}

/**
 * Publishes a snapshot's changes.
 *
 * @since 4.7.0
 * @access private
 *
 * @global wpdb                 $wpdb         WordPress database abstraction object.
 * @global WP_Customize_Manager $wp_customize Customizer instance.
 *
 * @param string  $new_status     New post status.
 * @param string  $old_status     Old post status.
 * @param WP_Post $changeset_post Changeset post object.
 */
function _wp_customize_publish_changeset( $new_status, $old_status, $changeset_post ) {
	global $wp_customize, $wpdb;

	$is_publishing_changeset = (
		'customize_changeset' === $changeset_post->post_type
		&&
		'publish' === $new_status
		&&
		'publish' !== $old_status
	);
	if ( ! $is_publishing_changeset ) {
		return;
	}

	if ( empty( $wp_customize ) ) {
		require_once ABSPATH . WPINC . '/class-wp-customize-manager.php';
		$wp_customize = new WP_Customize_Manager(
			array(
				'changeset_uuid'     => $changeset_post->post_name,
				'settings_previewed' => false,
			)
		);
	}

	if ( ! did_action( 'customize_register' ) ) {
		/*
		 * When running from CLI or Cron, the customize_register action will need
		 * to be triggered in order for core, themes, and plugins to register their
		 * settings. Normally core will add_action( 'customize_register' ) at
		 * priority 10 to register the core settings, and if any themes/plugins
		 * also add_action( 'customize_register' ) at the same priority, they
		 * will have a $wp_customize with those settings registered since they
		 * call add_action() afterward, normally. However, when manually doing
		 * the customize_register action after the setup_theme, then the order
		 * will be reversed for two actions added at priority 10, resulting in
		 * the core settings no longer being available as expected to themes/plugins.
		 * So the following manually calls the method that registers the core
		 * settings up front before doing the action.
		 */
		remove_action( 'customize_register', array( $wp_customize, 'register_controls' ) );
		$wp_customize->register_controls();

		/** This filter is documented in /wp-includes/class-wp-customize-manager.php */
		do_action( 'customize_register', $wp_customize );
	}
	$wp_customize->_publish_changeset_values( $changeset_post->ID );

	/*
	 * Trash the changeset post if revisions are not enabled. Unpublished
	 * changesets by default get garbage collected due to the auto-draft status.
	 * When a changeset post is published, however, it would no longer get cleaned
	 * out. This is a problem when the changeset posts are never displayed anywhere,
	 * since they would just be endlessly piling up. So here we use the revisions
	 * feature to indicate whether or not a published changeset should get trashed
	 * and thus garbage collected.
	 */
	if ( ! wp_revisions_enabled( $changeset_post ) ) {
		$wp_customize->trash_changeset_post( $changeset_post->ID );
	}
}

/**
 * Filters changeset post data upon insert to ensure post_name is intact.
 *
 * This is needed to prevent the post_name from being dropped when the post is
 * transitioned into pending status by a contributor.
 *
 * @since 4.7.0
 *
 * @see wp_insert_post()
 *
 * @param array $post_data          An array of slashed post data.
 * @param array $supplied_post_data An array of sanitized, but otherwise unmodified post data.
 * @return array Filtered data.
 */
function _wp_customize_changeset_filter_insert_post_data( $post_data, $supplied_post_data ) {
	if ( isset( $post_data['post_type'] ) && 'customize_changeset' === $post_data['post_type'] ) {

		// Prevent post_name from being dropped, such as when contributor saves a changeset post as pending.
		if ( empty( $post_data['post_name'] ) && ! empty( $supplied_post_data['post_name'] ) ) {
			$post_data['post_name'] = $supplied_post_data['post_name'];
		}
	}
	return $post_data;
}

/**
 * Adds settings for the customize-loader script.
 *
 * @since 3.4.0
 */
function _wp_customize_loader_settings() {
	$admin_origin = parse_url( admin_url() );
	$home_origin  = parse_url( home_url() );
	$cross_domain = ( strtolower( $admin_origin['host'] ) != strtolower( $home_origin['host'] ) );

	$browser = array(
		'mobile' => wp_is_mobile(),
		'ios'    => wp_is_mobile() && preg_match( '/iPad|iPod|iPhone/', $_SERVER['HTTP_USER_AGENT'] ),
	);

	$settings = array(
		'url'           => esc_url( admin_url( 'customize.php' ) ),
		'isCrossDomain' => $cross_domain,
		'browser'       => $browser,
		'l10n'          => array(
			'saveAlert'       => __( 'The changes you made will be lost if you navigate away from this page.' ),
			'mainIframeTitle' => __( 'Customizer' ),
		),
	);

	$script = 'var _wpCustomizeLoaderSettings = ' . wp_json_encode( $settings ) . ';';

	$wp_scripts = wp_scripts();
	$data       = $wp_scripts->get_data( 'customize-loader', 'data' );
	if ( $data ) {
		$script = "$data\n$script";
	}

	$wp_scripts->add_data( 'customize-loader', 'data', $script );
}

/**
 * Returns a URL to load the Customizer.
 *
 * @since 3.4.0
 *
 * @param string $stylesheet Optional. Theme to customize. Defaults to current theme.
 *                           The theme's stylesheet will be urlencoded if necessary.
 * @return string
 */
function wp_customize_url( $stylesheet = '' ) {
	$url = admin_url( 'customize.php' );
	if ( $stylesheet ) {
		$url .= '?theme=' . urlencode( $stylesheet );
	}
	return esc_url( $url );
}

/**
 * Prints a script to check whether or not the Customizer is supported,
 * and apply either the no-customize-support or customize-support class
 * to the body.
 *
 * This function MUST be called inside the body tag.
 *
 * Ideally, call this function immediately after the body tag is opened.
 * This prevents a flash of unstyled content.
 *
 * It is also recommended that you add the "no-customize-support" class
 * to the body tag by default.
 *
 * @since 3.4.0
 * @since 4.7.0 Support for IE8 and below is explicitly removed via conditional comments.
 * @since 5.5.0 IE8 and older are no longer supported.
 */
function wp_customize_support_script() {
	$admin_origin = parse_url( admin_url() );
	$home_origin  = parse_url( home_url() );
	$cross_domain = ( strtolower( $admin_origin['host'] ) != strtolower( $home_origin['host'] ) );
	$type_attr    = current_theme_supports( 'html5', 'script' ) ? '' : ' type="text/javascript"';
	?>
	<script<?php echo $type_attr; ?>>
		(function() {
			var request, b = document.body, c = 'className', cs = 'customize-support', rcs = new RegExp('(^|\\s+)(no-)?'+cs+'(\\s+|$)');

	<?php	if ( $cross_domain ) : ?>
			request = (function(){ var xhr = new XMLHttpRequest(); return ('withCredentials' in xhr); })();
	<?php	else : ?>
			request = true;
	<?php	endif; ?>

			b[c] = b[c].replace( rcs, ' ' );
			// The customizer requires postMessage and CORS (if the site is cross domain).
			b[c] += ( window.postMessage && request ? ' ' : ' no-' ) + cs;
		}());
	</script>
	<?php
}

/**
 * Whether the site is being previewed in the Customizer.
 *
 * @since 4.0.0
 *
 * @global WP_Customize_Manager $wp_customize Customizer instance.
 *
 * @return bool True if the site is being previewed in the Customizer, false otherwise.
 */
function is_customize_preview() {
	global $wp_customize;

	return ( $wp_customize instanceof WP_Customize_Manager ) && $wp_customize->is_preview();
}

/**
 * Makes sure that auto-draft posts get their post_date bumped or status changed to draft to prevent premature garbage-collection.
 *
 * When a changeset is updated but remains an auto-draft, ensure the post_date
 * for the auto-draft posts remains the same so that it will be
 * garbage-collected at the same time by `wp_delete_auto_drafts()`. Otherwise,
 * if the changeset is updated to be a draft then update the posts
 * to have a far-future post_date so that they will never be garbage collected
 * unless the changeset post itself is deleted.
 *
 * When a changeset is updated to be a persistent draft or to be scheduled for
 * publishing, then transition any dependent auto-drafts to a draft status so
 * that they likewise will not be garbage-collected but also so that they can
 * be edited in the admin before publishing since there is not yet a post/page
 * editing flow in the Customizer. See #39752.
 *
 * @link https://core.trac.wordpress.org/ticket/39752
 *
 * @since 4.8.0
 * @access private
 * @see wp_delete_auto_drafts()
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string   $new_status Transition to this post status.
 * @param string   $old_status Previous post status.
 * @param \WP_Post $post       Post data.
 */
function _wp_keep_alive_customize_changeset_dependent_auto_drafts( $new_status, $old_status, $post ) {
	global $wpdb;
	unset( $old_status );

	// Short-circuit if not a changeset or if the changeset was published.
	if ( 'customize_changeset' !== $post->post_type || 'publish' === $new_status ) {
		return;
	}

	$data = json_decode( $post->post_content, true );
	if ( empty( $data['nav_menus_created_posts']['value'] ) ) {
		return;
	}

	/*
	 * Actually, in lieu of keeping alive, trash any customization drafts here if the changeset itself is
	 * getting trashed. This is needed because when a changeset transitions to a draft, then any of the
	 * dependent auto-draft post/page stubs will also get transitioned to customization drafts which
	 * are then visible in the WP Admin. We cannot wait for the deletion of the changeset in which
	 * _wp_delete_customize_changeset_dependent_auto_drafts() will be called, since they need to be
	 * trashed to remove from visibility immediately.
	 */
	if ( 'trash' === $new_status ) {
		foreach ( $data['nav_menus_created_posts']['value'] as $post_id ) {
			if ( ! empty( $post_id ) && 'draft' === get_post_status( $post_id ) ) {
				wp_trash_post( $post_id );
			}
		}
		return;
	}

	$post_args = array();
	if ( 'auto-draft' === $new_status ) {
		/*
		 * Keep the post date for the post matching the changeset
		 * so that it will not be garbage-collected before the changeset.
		 */
		$post_args['post_date'] = $post->post_date; // Note wp_delete_auto_drafts() only looks at this date.
	} else {
		/*
		 * Since the changeset no longer has an auto-draft (and it is not published)
		 * it is now a persistent changeset, a long-lived draft, and so any
		 * associated auto-draft posts should likewise transition into having a draft
		 * status. These drafts will be treated differently than regular drafts in
		 * that they will be tied to the given changeset. The publish meta box is
		 * replaced with a notice about how the post is part of a set of customized changes
		 * which will be published when the changeset is published.
		 */
		$post_args['post_status'] = 'draft';
	}

	foreach ( $data['nav_menus_created_posts']['value'] as $post_id ) {
		if ( empty( $post_id ) || 'auto-draft' !== get_post_status( $post_id ) ) {
			continue;
		}
		$wpdb->update(
			$wpdb->posts,
			$post_args,
			array( 'ID' => $post_id )
		);
		clean_post_cache( $post_id );
	}
}

/**
 * Creates the initial theme features when the 'setup_theme' action is fired.
 *
 * See {@see 'setup_theme'}.
 *
 * @since 5.5.0
 */
function create_initial_theme_features() {
	register_theme_feature(
		'align-wide',
		array(
			'description'  => __( 'Whether theme opts in to wide alignment CSS class.' ),
			'show_in_rest' => true,
		)
	);
	register_theme_feature(
		'automatic-feed-links',
		array(
			'description'  => __( 'Whether posts and comments RSS feed links are added to head.' ),
			'show_in_rest' => true,
		)
	);
	register_theme_feature(
		'custom-background',
		array(
			'description'  => __( 'Custom background if defined by the theme.' ),
			'type'         => 'object',
			'show_in_rest' => array(
				'schema' => array(
					'properties' => array(
						'default-image'      => array(
							'type'   => 'string',
							'format' => 'uri',
						),
						'default-preset'     => array(
							'type' => 'string',
							'enum' => array(
								'default',
								'fill',
								'fit',
								'repeat',
								'custom',
							),
						),
						'default-position-x' => array(
							'type' => 'string',
							'enum' => array(
								'left',
								'center',
								'right',
							),
						),
						'default-position-y' => array(
							'type' => 'string',
							'enum' => array(
								'left',
								'center',
								'right',
							),
						),
						'default-size'       => array(
							'type' => 'string',
							'enum' => array(
								'auto',
								'contain',
								'cover',
							),
						),
						'default-repeat'     => array(
							'type' => 'string',
							'enum' => array(
								'repeat-x',
								'repeat-y',
								'repeat',
								'no-repeat',
							),
						),
						'default-attachment' => array(
							'type' => 'string',
							'enum' => array(
								'scroll',
								'fixed',
							),
						),
						'default-color'      => array(
							'type' => 'string',
						),
					),
				),
			),
		)
	);
	register_theme_feature(
		'custom-header',
		array(
			'description'  => __( 'Custom header if defined by the theme.' ),
			'type'         => 'object',
			'show_in_rest' => array(
				'schema' => array(
					'properties' => array(
						'default-image'      => array(
							'type'   => 'string',
							'format' => 'uri',
						),
						'random-default'     => array(
							'type' => 'boolean',
						),
						'width'              => array(
							'type' => 'integer',
						),
						'height'             => array(
							'type' => 'integer',
						),
						'flex-height'        => array(
							'type' => 'boolean',
						),
						'flex-width'         => array(
							'type' => 'boolean',
						),
						'default-text-color' => array(
							'type' => 'string',
						),
						'header-text'        => array(
							'type' => 'boolean',
						),
						'uploads'            => array(
							'type' => 'boolean',
						),
						'video'              => array(
							'type' => 'boolean',
						),
					),
				),
			),
		)
	);
	register_theme_feature(
		'custom-logo',
		array(
			'type'         => 'object',
			'description'  => __( 'Custom logo if defined by the theme.' ),
			'show_in_rest' => array(
				'schema' => array(
					'properties' => array(
						'width'                => array(
							'type' => 'integer',
						),
						'height'               => array(
							'type' => 'integer',
						),
						'flex-width'           => array(
							'type' => 'boolean',
						),
						'flex-height'          => array(
							'type' => 'boolean',
						),
						'header-text'          => array(
							'type'  => 'array',
							'items' => array(
								'type' => 'string',
							),
						),
						'unlink-homepage-logo' => array(
							'type' => 'boolean',
						),
					),
				),
			),
		)
	);
	register_theme_feature(
		'customize-selective-refresh-widgets',
		array(
			'description'  => __( 'Whether the theme enables Selective Refresh for Widgets being managed with the Customizer.' ),
			'show_in_rest' => true,
		)
	);
	register_theme_feature(
		'dark-editor-style',
		array(
			'description'  => __( 'Whether theme opts in to the dark editor style UI.' ),
			'show_in_rest' => true,
		)
	);
	register_theme_feature(
		'disable-custom-colors',
		array(
			'description'  => __( 'Whether the theme disables custom colors.' ),
			'show_in_rest' => true,
		)
	);
	register_theme_feature(
		'disable-custom-font-sizes',
		array(
			'description'  => __( 'Whether the theme disables custom font sizes.' ),
			'show_in_rest' => true,
		)
	);
	register_theme_feature(
		'disable-custom-gradients',
		array(
			'description'  => __( 'Whether the theme disables custom gradients.' ),
			'show_in_rest' => true,
		)
	);
	register_theme_feature(
		'editor-color-palette',
		array(
			'type'         => 'array',
			'description'  => __( 'Custom color palette if defined by the theme.' ),
			'show_in_rest' => array(
				'schema' => array(
					'items' => array(
						'type'       => 'object',
						'properties' => array(
							'name'  => array(
								'type' => 'string',
							),
							'slug'  => array(
								'type' => 'string',
							),
							'color' => array(
								'type' => 'string',
							),
						),
					),
				),
			),
		)
	);
	register_theme_feature(
		'editor-font-sizes',
		array(
			'type'         => 'array',
			'description'  => __( 'Custom font sizes if defined by the theme.' ),
			'show_in_rest' => array(
				'schema' => array(
					'items' => array(
						'type'       => 'object',
						'properties' => array(
							'name' => array(
								'type' => 'string',
							),
							'size' => array(
								'type' => 'number',
							),
							'slug' => array(
								'type' => 'string',
							),
						),
					),
				),
			),
		)
	);
	register_theme_feature(
		'editor-gradient-presets',
		array(
			'type'         => 'array',
			'description'  => __( 'Custom gradient presets if defined by the theme.' ),
			'show_in_rest' => array(
				'schema' => array(
					'items' => array(
						'type'       => 'object',
						'properties' => array(
							'name'     => array(
								'type' => 'string',
							),
							'gradient' => array(
								'type' => 'string',
							),
							'slug'     => array(
								'type' => 'string',
							),
						),
					),
				),
			),
		)
	);
	register_theme_feature(
		'editor-styles',
		array(
			'description'  => __( 'Whether theme opts in to the editor styles CSS wrapper.' ),
			'show_in_rest' => true,
		)
	);
	register_theme_feature(
		'html5',
		array(
			'type'         => 'array',
			'description'  => __( 'Allows use of HTML5 markup for search forms, comment forms, comment lists, gallery, and caption.' ),
			'show_in_rest' => array(
				'schema' => array(
					'items' => array(
						'type' => 'string',
						'enum' => array(
							'search-form',
							'comment-form',
							'comment-list',
							'gallery',
							'caption',
							'script',
							'style',
						),
					),
				),
			),
		)
	);
	register_theme_feature(
		'post-formats',
		array(
			'type'         => 'array',
			'description'  => __( 'Post formats supported.' ),
			'show_in_rest' => array(
				'name'             => 'formats',
				'schema'           => array(
					'items'   => array(
						'type' => 'string',
						'enum' => get_post_format_slugs(),
					),
					'default' => array( 'standard' ),
				),
				'prepare_callback' => static function ( $formats ) {
					$formats = is_array( $formats ) ? array_values( $formats[0] ) : array();
					$formats = array_merge( array( 'standard' ), $formats );

					return $formats;
				},
			),
		)
	);
	register_theme_feature(
		'post-thumbnails',
		array(
			'type'         => 'array',
			'description'  => __( 'The post types that support thumbnails or true if all post types are supported.' ),
			'show_in_rest' => array(
				'type'   => array( 'boolean', 'array' ),
				'schema' => array(
					'items' => array(
						'type' => 'string',
					),
				),
			),
		)
	);
	register_theme_feature(
		'responsive-embeds',
		array(
			'description'  => __( 'Whether the theme supports responsive embedded content.' ),
			'show_in_rest' => true,
		)
	);
	register_theme_feature(
		'title-tag',
		array(
			'description'  => __( 'Whether the theme can manage the document title tag.' ),
			'show_in_rest' => true,
		)
	);
	register_theme_feature(
		'wp-block-styles',
		array(
			'description'  => __( 'Whether theme opts in to default WordPress block styles for viewing.' ),
			'show_in_rest' => true,
		)
	);
}
