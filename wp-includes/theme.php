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
 * @staticvar array $_themes
 *
 * @param array $args The search arguments. Optional.
 * - errors      mixed  True to return themes with errors, false to return themes without errors, null
 *                      to return all themes. Defaults to false.
 * - allowed     mixed  (Multisite) True to return only allowed themes for a site. False to return only
 *                      disallowed themes for a site. 'site' to return only site-allowed themes. 'network'
 *                      to return only network-allowed themes. Null to return all themes. Defaults to null.
 * - blog_id     int    (Multisite) The blog ID used to calculate which themes are allowed. Defaults to 0,
 *                      synonymous for the current blog.
 * @return array Array of WP_Theme objects.
 */
function wp_get_themes( $args = array() ) {
	global $wp_theme_directories;

	$defaults = array( 'errors' => false, 'allowed' => null, 'blog_id' => 0 );
	$args = wp_parse_args( $args, $defaults );

	$theme_directories = search_theme_directories();

	if ( count( $wp_theme_directories ) > 1 ) {
		// Make sure the current theme wins out, in case search_theme_directories() picks the wrong
		// one in the case of a conflict. (Normally, last registered theme root wins.)
		$current_theme = get_stylesheet();
		if ( isset( $theme_directories[ $current_theme ] ) ) {
			$root_of_current_theme = get_raw_theme_root( $current_theme );
			if ( ! in_array( $root_of_current_theme, $wp_theme_directories ) )
				$root_of_current_theme = WP_CONTENT_DIR . $root_of_current_theme;
			$theme_directories[ $current_theme ]['theme_root'] = $root_of_current_theme;
		}
	}

	if ( empty( $theme_directories ) )
		return array();

	if ( is_multisite() && null !== $args['allowed'] ) {
		$allowed = $args['allowed'];
		if ( 'network' === $allowed )
			$theme_directories = array_intersect_key( $theme_directories, WP_Theme::get_allowed_on_network() );
		elseif ( 'site' === $allowed )
			$theme_directories = array_intersect_key( $theme_directories, WP_Theme::get_allowed_on_site( $args['blog_id'] ) );
		elseif ( $allowed )
			$theme_directories = array_intersect_key( $theme_directories, WP_Theme::get_allowed( $args['blog_id'] ) );
		else
			$theme_directories = array_diff_key( $theme_directories, WP_Theme::get_allowed( $args['blog_id'] ) );
	}

	$themes = array();
	static $_themes = array();

	foreach ( $theme_directories as $theme => $theme_root ) {
		if ( isset( $_themes[ $theme_root['theme_root'] . '/' . $theme ] ) )
			$themes[ $theme ] = $_themes[ $theme_root['theme_root'] . '/' . $theme ];
		else
			$themes[ $theme ] = $_themes[ $theme_root['theme_root'] . '/' . $theme ] = new WP_Theme( $theme, $theme_root['theme_root'] );
	}

	if ( null !== $args['errors'] ) {
		foreach ( $themes as $theme => $wp_theme ) {
			if ( $wp_theme->errors() != $args['errors'] )
				unset( $themes[ $theme ] );
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
 * @param string $stylesheet Directory name for the theme. Optional. Defaults to current theme.
 * @param string $theme_root Absolute path of the theme root to look in. Optional. If not specified, get_raw_theme_root()
 * 	                         is used to calculate the theme root for the $stylesheet provided (or current theme).
 * @return WP_Theme Theme object. Be sure to check the object's exists() method if you need to confirm the theme's existence.
 */
function wp_get_theme( $stylesheet = null, $theme_root = null ) {
	global $wp_theme_directories;

	if ( empty( $stylesheet ) )
		$stylesheet = get_stylesheet();

	if ( empty( $theme_root ) ) {
		$theme_root = get_raw_theme_root( $stylesheet );
		if ( false === $theme_root )
			$theme_root = WP_CONTENT_DIR . '/themes';
		elseif ( ! in_array( $theme_root, (array) $wp_theme_directories ) )
			$theme_root = WP_CONTENT_DIR . $theme_root;
	}

	return new WP_Theme( $stylesheet, $theme_root );
}

/**
 * Clears the cache held by get_theme_roots() and WP_Theme.
 *
 * @since 3.5.0
 * @param bool $clear_update_cache Whether to clear the Theme updates cache
 */
function wp_clean_themes_cache( $clear_update_cache = true ) {
	if ( $clear_update_cache )
		delete_site_transient( 'update_themes' );
	search_theme_directories( true );
	foreach ( wp_get_themes( array( 'errors' => null ) ) as $theme )
		$theme->cache_delete();
}

/**
 * Whether a child theme is in use.
 *
 * @since 3.0.0
 *
 * @return bool true if a child theme is in use, false otherwise.
 **/
function is_child_theme() {
	return ( TEMPLATEPATH !== STYLESHEETPATH );
}

/**
 * Retrieve name of the current stylesheet.
 *
 * The theme name that the administrator has currently set the front end theme
 * as.
 *
 * For all intents and purposes, the template name and the stylesheet name are
 * going to be the same for most cases.
 *
 * @since 1.5.0
 *
 * @return string Stylesheet name.
 */
function get_stylesheet() {
	/**
	 * Filter the name of current stylesheet.
	 *
	 * @since 1.5.0
	 *
	 * @param string $stylesheet Name of the current stylesheet.
	 */
	return apply_filters( 'stylesheet', get_option( 'stylesheet' ) );
}

/**
 * Retrieve stylesheet directory path for current theme.
 *
 * @since 1.5.0
 *
 * @return string Path to current theme directory.
 */
function get_stylesheet_directory() {
	$stylesheet = get_stylesheet();
	$theme_root = get_theme_root( $stylesheet );
	$stylesheet_dir = "$theme_root/$stylesheet";

	/**
	 * Filter the stylesheet directory path for current theme.
	 *
	 * @since 1.5.0
	 *
	 * @param string $stylesheet_dir Absolute path to the current them.
	 * @param string $stylesheet     Directory name of the current theme.
	 * @param string $theme_root     Absolute path to themes directory.
	 */
	return apply_filters( 'stylesheet_directory', $stylesheet_dir, $stylesheet, $theme_root );
}

/**
 * Retrieve stylesheet directory URI.
 *
 * @since 1.5.0
 *
 * @return string
 */
function get_stylesheet_directory_uri() {
	$stylesheet = str_replace( '%2F', '/', rawurlencode( get_stylesheet() ) );
	$theme_root_uri = get_theme_root_uri( $stylesheet );
	$stylesheet_dir_uri = "$theme_root_uri/$stylesheet";

	/**
	 * Filter the stylesheet directory URI.
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
 * Retrieve URI of current theme stylesheet.
 *
 * The stylesheet file name is 'style.css' which is appended to {@link
 * get_stylesheet_directory_uri() stylesheet directory URI} path.
 *
 * @since 1.5.0
 *
 * @return string
 */
function get_stylesheet_uri() {
	$stylesheet_dir_uri = get_stylesheet_directory_uri();
	$stylesheet_uri = $stylesheet_dir_uri . '/style.css';
	/**
	 * Filter the URI of the current theme stylesheet.
	 *
	 * @since 1.5.0
	 *
	 * @param string $stylesheet_uri     Stylesheet URI for the current theme/child theme.
	 * @param string $stylesheet_dir_uri Stylesheet directory URI for the current theme/child theme.
	 */
	return apply_filters( 'stylesheet_uri', $stylesheet_uri, $stylesheet_dir_uri );
}

/**
 * Retrieve localized stylesheet URI.
 *
 * The stylesheet directory for the localized stylesheet files are located, by
 * default, in the base theme directory. The name of the locale file will be the
 * locale followed by '.css'. If that does not exist, then the text direction
 * stylesheet will be checked for existence, for example 'ltr.css'.
 *
 * The theme may change the location of the stylesheet directory by either using
 * the 'stylesheet_directory_uri' filter or the 'locale_stylesheet_uri' filter.
 * If you want to change the location of the stylesheet files for the entire
 * WordPress workflow, then change the former. If you just have the locale in a
 * separate folder, then change the latter.
 *
 * @since 2.1.0
 *
 * @global WP_Locale $wp_locale
 *
 * @return string
 */
function get_locale_stylesheet_uri() {
	global $wp_locale;
	$stylesheet_dir_uri = get_stylesheet_directory_uri();
	$dir = get_stylesheet_directory();
	$locale = get_locale();
	if ( file_exists("$dir/$locale.css") )
		$stylesheet_uri = "$stylesheet_dir_uri/$locale.css";
	elseif ( !empty($wp_locale->text_direction) && file_exists("$dir/{$wp_locale->text_direction}.css") )
		$stylesheet_uri = "$stylesheet_dir_uri/{$wp_locale->text_direction}.css";
	else
		$stylesheet_uri = '';
	/**
	 * Filter the localized stylesheet URI.
	 *
	 * @since 2.1.0
	 *
	 * @param string $stylesheet_uri     Localized stylesheet URI.
	 * @param string $stylesheet_dir_uri Stylesheet directory URI.
	 */
	return apply_filters( 'locale_stylesheet_uri', $stylesheet_uri, $stylesheet_dir_uri );
}

/**
 * Retrieve name of the current theme.
 *
 * @since 1.5.0
 *
 * @return string Template name.
 */
function get_template() {
	/**
	 * Filter the name of the current theme.
	 *
	 * @since 1.5.0
	 *
	 * @param string $template Current theme's directory name.
	 */
	return apply_filters( 'template', get_option( 'template' ) );
}

/**
 * Retrieve current theme directory.
 *
 * @since 1.5.0
 *
 * @return string Template directory path.
 */
function get_template_directory() {
	$template = get_template();
	$theme_root = get_theme_root( $template );
	$template_dir = "$theme_root/$template";

	/**
	 * Filter the current theme directory path.
	 *
	 * @since 1.5.0
	 *
	 * @param string $template_dir The URI of the current theme directory.
	 * @param string $template     Directory name of the current theme.
	 * @param string $theme_root   Absolute path to the themes directory.
	 */
	return apply_filters( 'template_directory', $template_dir, $template, $theme_root );
}

/**
 * Retrieve theme directory URI.
 *
 * @since 1.5.0
 *
 * @return string Template directory URI.
 */
function get_template_directory_uri() {
	$template = str_replace( '%2F', '/', rawurlencode( get_template() ) );
	$theme_root_uri = get_theme_root_uri( $template );
	$template_dir_uri = "$theme_root_uri/$template";

	/**
	 * Filter the current theme directory URI.
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
 * Retrieve theme roots.
 *
 * @since 2.9.0
 *
 * @global array $wp_theme_directories
 *
 * @return array|string An array of theme roots keyed by template/stylesheet or a single theme root if all themes have the same root.
 */
function get_theme_roots() {
	global $wp_theme_directories;

	if ( count($wp_theme_directories) <= 1 )
		return '/themes';

	$theme_roots = get_site_transient( 'theme_roots' );
	if ( false === $theme_roots ) {
		search_theme_directories( true ); // Regenerate the transient.
		$theme_roots = get_site_transient( 'theme_roots' );
	}
	return $theme_roots;
}

/**
 * Register a directory that contains themes.
 *
 * @since 2.9.0
 *
 * @global array $wp_theme_directories
 *
 * @param string $directory Either the full filesystem path to a theme folder or a folder within WP_CONTENT_DIR
 * @return bool
 */
function register_theme_directory( $directory ) {
	global $wp_theme_directories;

	if ( ! file_exists( $directory ) ) {
		// Try prepending as the theme directory could be relative to the content directory
		$directory = WP_CONTENT_DIR . '/' . $directory;
		// If this directory does not exist, return and do not register
		if ( ! file_exists( $directory ) ) {
			return false;
		}
	}

	if ( ! is_array( $wp_theme_directories ) ) {
		$wp_theme_directories = array();
	}

	$untrailed = untrailingslashit( $directory );
	if ( ! empty( $untrailed ) && ! in_array( $untrailed, $wp_theme_directories ) ) {
		$wp_theme_directories[] = $untrailed;
	}

	return true;
}

/**
 * Search all registered theme directories for complete and valid themes.
 *
 * @since 2.9.0
 *
 * @global array $wp_theme_directories
 * @staticvar array $found_themes
 *
 * @param bool $force Optional. Whether to force a new directory scan. Defaults to false.
 * @return array|false Valid themes found
 */
function search_theme_directories( $force = false ) {
	global $wp_theme_directories;
	static $found_themes = null;

	if ( empty( $wp_theme_directories ) )
		return false;

	if ( ! $force && isset( $found_themes ) )
		return $found_themes;

	$found_themes = array();

	$wp_theme_directories = (array) $wp_theme_directories;
	$relative_theme_roots = array();

	// Set up maybe-relative, maybe-absolute array of theme directories.
	// We always want to return absolute, but we need to cache relative
	// to use in get_theme_root().
	foreach ( $wp_theme_directories as $theme_root ) {
		if ( 0 === strpos( $theme_root, WP_CONTENT_DIR ) )
			$relative_theme_roots[ str_replace( WP_CONTENT_DIR, '', $theme_root ) ] = $theme_root;
		else
			$relative_theme_roots[ $theme_root ] = $theme_root;
	}

	/**
	 * Filter whether to get the cache of the registered theme directories.
	 *
	 * @since 3.4.0
	 *
	 * @param bool   $cache_expiration Whether to get the cache of the theme directories. Default false.
	 * @param string $cache_directory  Directory to be searched for the cache.
	 */
	if ( $cache_expiration = apply_filters( 'wp_cache_themes_persistently', false, 'search_theme_directories' ) ) {
		$cached_roots = get_site_transient( 'theme_roots' );
		if ( is_array( $cached_roots ) ) {
			foreach ( $cached_roots as $theme_dir => $theme_root ) {
				// A cached theme root is no longer around, so skip it.
				if ( ! isset( $relative_theme_roots[ $theme_root ] ) )
					continue;
				$found_themes[ $theme_dir ] = array(
					'theme_file' => $theme_dir . '/style.css',
					'theme_root' => $relative_theme_roots[ $theme_root ], // Convert relative to absolute.
				);
			}
			return $found_themes;
		}
		if ( ! is_int( $cache_expiration ) )
			$cache_expiration = 1800; // half hour
	} else {
		$cache_expiration = 1800; // half hour
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
			if ( ! is_dir( $theme_root . '/' . $dir ) || $dir[0] == '.' || $dir == 'CVS' )
				continue;
			if ( file_exists( $theme_root . '/' . $dir . '/style.css' ) ) {
				// wp-content/themes/a-single-theme
				// wp-content/themes is $theme_root, a-single-theme is $dir
				$found_themes[ $dir ] = array(
					'theme_file' => $dir . '/style.css',
					'theme_root' => $theme_root,
				);
			} else {
				$found_theme = false;
				// wp-content/themes/a-folder-of-themes/*
				// wp-content/themes is $theme_root, a-folder-of-themes is $dir, then themes are $sub_dirs
				$sub_dirs = @ scandir( $theme_root . '/' . $dir );
				if ( ! $sub_dirs ) {
					trigger_error( "$theme_root/$dir is not readable", E_USER_NOTICE );
					continue;
				}
				foreach ( $sub_dirs as $sub_dir ) {
					if ( ! is_dir( $theme_root . '/' . $dir . '/' . $sub_dir ) || $dir[0] == '.' || $dir == 'CVS' )
						continue;
					if ( ! file_exists( $theme_root . '/' . $dir . '/' . $sub_dir . '/style.css' ) )
						continue;
					$found_themes[ $dir . '/' . $sub_dir ] = array(
						'theme_file' => $dir . '/' . $sub_dir . '/style.css',
						'theme_root' => $theme_root,
					);
					$found_theme = true;
				}
				// Never mind the above, it's just a theme missing a style.css.
				// Return it; WP_Theme will catch the error.
				if ( ! $found_theme )
					$found_themes[ $dir ] = array(
						'theme_file' => $dir . '/style.css',
						'theme_root' => $theme_root,
					);
			}
		}
	}

	asort( $found_themes );

	$theme_roots = array();
	$relative_theme_roots = array_flip( $relative_theme_roots );

	foreach ( $found_themes as $theme_dir => $theme_data ) {
		$theme_roots[ $theme_dir ] = $relative_theme_roots[ $theme_data['theme_root'] ]; // Convert absolute to relative.
	}

	if ( $theme_roots != get_site_transient( 'theme_roots' ) )
		set_site_transient( 'theme_roots', $theme_roots, $cache_expiration );

	return $found_themes;
}

/**
 * Retrieve path to themes directory.
 *
 * Does not have trailing slash.
 *
 * @since 1.5.0
 *
 * @global array $wp_theme_directories
 *
 * @param string $stylesheet_or_template The stylesheet or template name of the theme
 * @return string Theme path.
 */
function get_theme_root( $stylesheet_or_template = false ) {
	global $wp_theme_directories;

	if ( $stylesheet_or_template && $theme_root = get_raw_theme_root( $stylesheet_or_template ) ) {
		// Always prepend WP_CONTENT_DIR unless the root currently registered as a theme directory.
		// This gives relative theme roots the benefit of the doubt when things go haywire.
		if ( ! in_array( $theme_root, (array) $wp_theme_directories ) )
			$theme_root = WP_CONTENT_DIR . $theme_root;
	} else {
		$theme_root = WP_CONTENT_DIR . '/themes';
	}

	/**
	 * Filter the absolute path to the themes directory.
	 *
	 * @since 1.5.0
	 *
	 * @param string $theme_root Absolute path to themes directory.
	 */
	return apply_filters( 'theme_root', $theme_root );
}

/**
 * Retrieve URI for themes directory.
 *
 * Does not have trailing slash.
 *
 * @since 1.5.0
 *
 * @global array $wp_theme_directories
 *
 * @param string $stylesheet_or_template Optional. The stylesheet or template name of the theme.
 * 	                                     Default is to leverage the main theme root.
 * @param string $theme_root             Optional. The theme root for which calculations will be based, preventing
 * 	                                     the need for a get_raw_theme_root() call.
 * @return string Themes URI.
 */
function get_theme_root_uri( $stylesheet_or_template = false, $theme_root = false ) {
	global $wp_theme_directories;

	if ( $stylesheet_or_template && ! $theme_root )
		$theme_root = get_raw_theme_root( $stylesheet_or_template );

	if ( $stylesheet_or_template && $theme_root ) {
		if ( in_array( $theme_root, (array) $wp_theme_directories ) ) {
			// Absolute path. Make an educated guess. YMMV -- but note the filter below.
			if ( 0 === strpos( $theme_root, WP_CONTENT_DIR ) )
				$theme_root_uri = content_url( str_replace( WP_CONTENT_DIR, '', $theme_root ) );
			elseif ( 0 === strpos( $theme_root, ABSPATH ) )
				$theme_root_uri = site_url( str_replace( ABSPATH, '', $theme_root ) );
			elseif ( 0 === strpos( $theme_root, WP_PLUGIN_DIR ) || 0 === strpos( $theme_root, WPMU_PLUGIN_DIR ) )
				$theme_root_uri = plugins_url( basename( $theme_root ), $theme_root );
			else
				$theme_root_uri = $theme_root;
		} else {
			$theme_root_uri = content_url( $theme_root );
		}
	} else {
		$theme_root_uri = content_url( 'themes' );
	}

	/**
	 * Filter the URI for themes directory.
	 *
	 * @since 1.5.0
	 *
	 * @param string $theme_root_uri         The URI for themes directory.
	 * @param string $siteurl                WordPress web address which is set in General Options.
	 * @param string $stylesheet_or_template Stylesheet or template name of the theme.
	 */
	return apply_filters( 'theme_root_uri', $theme_root_uri, get_option( 'siteurl' ), $stylesheet_or_template );
}

/**
 * Get the raw theme root relative to the content directory with no filters applied.
 *
 * @since 3.1.0
 *
 * @global array $wp_theme_directories
 *
 * @param string $stylesheet_or_template The stylesheet or template name of the theme
 * @param bool   $skip_cache             Optional. Whether to skip the cache.
 *                                       Defaults to false, meaning the cache is used.
 * @return string Theme root
 */
function get_raw_theme_root( $stylesheet_or_template, $skip_cache = false ) {
	global $wp_theme_directories;

	if ( count($wp_theme_directories) <= 1 )
		return '/themes';

	$theme_root = false;

	// If requesting the root for the current theme, consult options to avoid calling get_theme_roots()
	if ( ! $skip_cache ) {
		if ( get_option('stylesheet') == $stylesheet_or_template )
			$theme_root = get_option('stylesheet_root');
		elseif ( get_option('template') == $stylesheet_or_template )
			$theme_root = get_option('template_root');
	}

	if ( empty($theme_root) ) {
		$theme_roots = get_theme_roots();
		if ( !empty($theme_roots[$stylesheet_or_template]) )
			$theme_root = $theme_roots[$stylesheet_or_template];
	}

	return $theme_root;
}

/**
 * Display localized stylesheet link element.
 *
 * @since 2.1.0
 */
function locale_stylesheet() {
	$stylesheet = get_locale_stylesheet_uri();
	if ( empty($stylesheet) )
		return;
	echo '<link rel="stylesheet" href="' . $stylesheet . '" type="text/css" media="screen" />';
}

/**
 * Switches the theme.
 *
 * Accepts one argument: $stylesheet of the theme. It also accepts an additional function signature
 * of two arguments: $template then $stylesheet. This is for backwards compatibility.
 *
 * @since 2.5.0
 *
 * @global array                $wp_theme_directories
 * @global WP_Customize_Manager $wp_customize
 * @global array                $sidebars_widgets
 *
 * @param string $stylesheet Stylesheet name
 */
function switch_theme( $stylesheet ) {
	global $wp_theme_directories, $wp_customize, $sidebars_widgets;

	$_sidebars_widgets = null;
	if ( 'wp_ajax_customize_save' === current_action() ) {
		$_sidebars_widgets = $wp_customize->post_value( $wp_customize->get_setting( 'old_sidebars_widgets_data' ) );
	} elseif ( is_array( $sidebars_widgets ) ) {
		$_sidebars_widgets = $sidebars_widgets;
	}

	if ( is_array( $_sidebars_widgets ) ) {
		set_theme_mod( 'sidebars_widgets', array( 'time' => time(), 'data' => $_sidebars_widgets ) );
	}

	$nav_menu_locations = get_theme_mod( 'nav_menu_locations' );

	if ( func_num_args() > 1 ) {
		$stylesheet = func_get_arg( 1 );
	}

	$old_theme = wp_get_theme();
	$new_theme = wp_get_theme( $stylesheet );
	$template  = $new_theme->get_template();

	update_option( 'template', $template );
	update_option( 'stylesheet', $stylesheet );

	if ( count( $wp_theme_directories ) > 1 ) {
		update_option( 'template_root', get_raw_theme_root( $template, true ) );
		update_option( 'stylesheet_root', get_raw_theme_root( $stylesheet, true ) );
	} else {
		delete_option( 'template_root' );
		delete_option( 'stylesheet_root' );
	}

	$new_name  = $new_theme->get('Name');

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
		 * we need to to remove the theme mods to avoid overwriting changes made via
		 * the Customizer when accessing wp-admin/widgets.php.
		 */
		if ( 'wp_ajax_customize_save' === current_action() ) {
			remove_theme_mod( 'sidebars_widgets' );
		}

		if ( ! empty( $nav_menu_locations ) ) {
			$nav_mods = get_theme_mod( 'nav_menu_locations' );
			if ( empty( $nav_mods ) ) {
				set_theme_mod( 'nav_menu_locations', $nav_menu_locations );
			}
		}
	}

	update_option( 'theme_switched', $old_theme->get_stylesheet() );
	/**
	 * Fires after the theme is switched.
	 *
	 * @since 1.5.0
	 *
	 * @param string   $new_name  Name of the new theme.
	 * @param WP_Theme $new_theme WP_Theme instance of the new theme.
	 */
	do_action( 'switch_theme', $new_name, $new_theme );
}

/**
 * Checks that current theme files 'index.php' and 'style.css' exists.
 *
 * Does not check the default theme, which is the fallback and should always exist.
 * Will switch theme to the fallback theme if current theme does not validate.
 * You can use the 'validate_current_theme' filter to return false to
 * disable this functionality.
 *
 * @since 1.5.0
 * @see WP_DEFAULT_THEME
 *
 * @return bool
 */
function validate_current_theme() {
	/**
	 * Filter whether to validate the current theme.
	 *
	 * @since 2.7.0
	 *
	 * @param bool true Validation flag to check the current theme.
	 */
	if ( wp_installing() || ! apply_filters( 'validate_current_theme', true ) )
		return true;

	if ( get_template() != WP_DEFAULT_THEME && !file_exists(get_template_directory() . '/index.php') ) {
		switch_theme( WP_DEFAULT_THEME );
		return false;
	}

	if ( get_stylesheet() != WP_DEFAULT_THEME && !file_exists(get_template_directory() . '/style.css') ) {
		switch_theme( WP_DEFAULT_THEME );
		return false;
	}

	if ( is_child_theme() && ! file_exists( get_stylesheet_directory() . '/style.css' ) ) {
		switch_theme( WP_DEFAULT_THEME );
		return false;
	}

	return true;
}

/**
 * Retrieve all theme modifications.
 *
 * @since 3.1.0
 *
 * @return array|void Theme modifications.
 */
function get_theme_mods() {
	$theme_slug = get_option( 'stylesheet' );
	$mods = get_option( "theme_mods_$theme_slug" );
	if ( false === $mods ) {
		$theme_name = get_option( 'current_theme' );
		if ( false === $theme_name )
			$theme_name = wp_get_theme()->get('Name');
		$mods = get_option( "mods_$theme_name" ); // Deprecated location.
		if ( is_admin() && false !== $mods ) {
			update_option( "theme_mods_$theme_slug", $mods );
			delete_option( "mods_$theme_name" );
		}
	}
	return $mods;
}

/**
 * Retrieve theme modification value for the current theme.
 *
 * If the modification name does not exist, then the $default will be passed
 * through {@link http://php.net/sprintf sprintf()} PHP function with the first
 * string the template directory URI and the second string the stylesheet
 * directory URI.
 *
 * @since 2.1.0
 *
 * @param string      $name    Theme modification name.
 * @param bool|string $default
 * @return string
 */
function get_theme_mod( $name, $default = false ) {
	$mods = get_theme_mods();

	if ( isset( $mods[$name] ) ) {
		/**
		 * Filter the theme modification, or 'theme_mod', value.
		 *
		 * The dynamic portion of the hook name, `$name`, refers to
		 * the key name of the modification array. For example,
		 * 'header_textcolor', 'header_image', and so on depending
		 * on the theme options.
		 *
		 * @since 2.2.0
		 *
		 * @param string $current_mod The value of the current theme modification.
		 */
		return apply_filters( "theme_mod_{$name}", $mods[$name] );
	}

	if ( is_string( $default ) )
		$default = sprintf( $default, get_template_directory_uri(), get_stylesheet_directory_uri() );

	/** This filter is documented in wp-includes/theme.php */
	return apply_filters( "theme_mod_{$name}", $default );
}

/**
 * Update theme modification value for the current theme.
 *
 * @since 2.1.0
 *
 * @param string $name  Theme modification name.
 * @param mixed  $value Theme modification value.
 */
function set_theme_mod( $name, $value ) {
	$mods = get_theme_mods();
	$old_value = isset( $mods[ $name ] ) ? $mods[ $name ] : false;

	/**
	 * Filter the theme mod value on save.
	 *
	 * The dynamic portion of the hook name, `$name`, refers to the key name of
	 * the modification array. For example, 'header_textcolor', 'header_image',
	 * and so on depending on the theme options.
	 *
	 * @since 3.9.0
	 *
	 * @param string $value     The new value of the theme mod.
	 * @param string $old_value The current value of the theme mod.
	 */
	$mods[ $name ] = apply_filters( "pre_set_theme_mod_$name", $value, $old_value );

	$theme = get_option( 'stylesheet' );
	update_option( "theme_mods_$theme", $mods );
}

/**
 * Remove theme modification name from current theme list.
 *
 * If removing the name also removes all elements, then the entire option will
 * be removed.
 *
 * @since 2.1.0
 *
 * @param string $name Theme modification name.
 */
function remove_theme_mod( $name ) {
	$mods = get_theme_mods();

	if ( ! isset( $mods[ $name ] ) )
		return;

	unset( $mods[ $name ] );

	if ( empty( $mods ) ) {
		remove_theme_mods();
		return;
	}
	$theme = get_option( 'stylesheet' );
	update_option( "theme_mods_$theme", $mods );
}

/**
 * Remove theme modifications option for current theme.
 *
 * @since 2.1.0
 */
function remove_theme_mods() {
	delete_option( 'theme_mods_' . get_option( 'stylesheet' ) );

	// Old style.
	$theme_name = get_option( 'current_theme' );
	if ( false === $theme_name )
		$theme_name = wp_get_theme()->get('Name');
	delete_option( 'mods_' . $theme_name );
}

/**
 * Retrieve text color for custom header.
 *
 * @since 2.1.0
 *
 * @return string
 */
function get_header_textcolor() {
	return get_theme_mod('header_textcolor', get_theme_support( 'custom-header', 'default-text-color' ) );
}

/**
 * Display text color for custom header.
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
	if ( ! current_theme_supports( 'custom-header', 'header-text' ) )
		return false;

	$text_color = get_theme_mod( 'header_textcolor', get_theme_support( 'custom-header', 'default-text-color' ) );
	return 'blank' !== $text_color;
}

/**
 * Check whether a header image is set or not.
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
 * Retrieve header image for custom header.
 *
 * @since 2.1.0
 *
 * @return string|false
 */
function get_header_image() {
	$url = get_theme_mod( 'header_image', get_theme_support( 'custom-header', 'default-image' ) );

	if ( 'remove-header' == $url )
		return false;

	if ( is_random_header_image() )
		$url = get_random_header_image();

	return esc_url_raw( set_url_scheme( $url ) );
}

/**
 * Get random header image data from registered images in theme.
 *
 * @since 3.4.0
 *
 * @access private
 *
 * @global array  $_wp_default_headers
 * @staticvar object $_wp_random_header
 *
 * @return object
 */
function _get_random_header_data() {
	static $_wp_random_header = null;

	if ( empty( $_wp_random_header ) ) {
		global $_wp_default_headers;
		$header_image_mod = get_theme_mod( 'header_image', '' );
		$headers = array();

		if ( 'random-uploaded-image' == $header_image_mod )
			$headers = get_uploaded_header_images();
		elseif ( ! empty( $_wp_default_headers ) ) {
			if ( 'random-default-image' == $header_image_mod ) {
				$headers = $_wp_default_headers;
			} else {
				if ( current_theme_supports( 'custom-header', 'random-default' ) )
					$headers = $_wp_default_headers;
			}
		}

		if ( empty( $headers ) )
			return new stdClass;

		$_wp_random_header = (object) $headers[ array_rand( $headers ) ];

		$_wp_random_header->url =  sprintf( $_wp_random_header->url, get_template_directory_uri(), get_stylesheet_directory_uri() );
		$_wp_random_header->thumbnail_url =  sprintf( $_wp_random_header->thumbnail_url, get_template_directory_uri(), get_stylesheet_directory_uri() );
	}
	return $_wp_random_header;
}

/**
 * Get random header image url from registered images in theme.
 *
 * @since 3.2.0
 *
 * @return string Path to header image
 */
function get_random_header_image() {
	$random_image = _get_random_header_data();
	if ( empty( $random_image->url ) )
		return '';
	return $random_image->url;
}

/**
 * Check if random header image is in use.
 *
 * Always true if user expressly chooses the option in Appearance > Header.
 * Also true if theme has multiple header images registered, no specific header image
 * is chosen, and theme turns on random headers with add_theme_support().
 *
 * @since 3.2.0
 *
 * @param string $type The random pool to use. any|default|uploaded
 * @return bool
 */
function is_random_header_image( $type = 'any' ) {
	$header_image_mod = get_theme_mod( 'header_image', get_theme_support( 'custom-header', 'default-image' ) );

	if ( 'any' == $type ) {
		if ( 'random-default-image' == $header_image_mod || 'random-uploaded-image' == $header_image_mod || ( '' != get_random_header_image() && empty( $header_image_mod ) ) )
			return true;
	} else {
		if ( "random-$type-image" == $header_image_mod )
			return true;
		elseif ( 'default' == $type && empty( $header_image_mod ) && '' != get_random_header_image() )
			return true;
	}

	return false;
}

/**
 * Display header image URL.
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
 * Get the header images uploaded for the current theme.
 *
 * @since 3.2.0
 *
 * @return array
 */
function get_uploaded_header_images() {
	$header_images = array();

	// @todo caching
	$headers = get_posts( array( 'post_type' => 'attachment', 'meta_key' => '_wp_attachment_is_custom_header', 'meta_value' => get_option('stylesheet'), 'orderby' => 'none', 'nopaging' => true ) );

	if ( empty( $headers ) )
		return array();

	foreach ( (array) $headers as $header ) {
		$url = esc_url_raw( wp_get_attachment_url( $header->ID ) );
		$header_data = wp_get_attachment_metadata( $header->ID );
		$header_index = basename($url);

		$header_images[$header_index] = array();
		$header_images[$header_index]['attachment_id'] = $header->ID;
		$header_images[$header_index]['url'] =  $url;
		$header_images[$header_index]['thumbnail_url'] = $url;
		$header_images[$header_index]['alt_text'] = get_post_meta( $header->ID, '_wp_attachment_image_alt', true );

		if ( isset( $header_data['width'] ) )
			$header_images[$header_index]['width'] = $header_data['width'];
		if ( isset( $header_data['height'] ) )
			$header_images[$header_index]['height'] = $header_data['height'];
	}

	return $header_images;
}

/**
 * Get the header image data.
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
			$directory_args = array( get_template_directory_uri(), get_stylesheet_directory_uri() );
			$data = array();
			$data['url'] = $data['thumbnail_url'] = vsprintf( get_theme_support( 'custom-header', 'default-image' ), $directory_args );
			if ( ! empty( $_wp_default_headers ) ) {
				foreach ( (array) $_wp_default_headers as $default_header ) {
					$url = vsprintf( $default_header['url'], $directory_args );
					if ( $data['url'] == $url ) {
						$data = $default_header;
						$data['url'] = $url;
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
	);
	return (object) wp_parse_args( $data, $default );
}

/**
 * Register a selection of default headers to be displayed by the custom header admin UI.
 *
 * @since 3.0.0
 *
 * @global array $_wp_default_headers
 *
 * @param array $headers Array of headers keyed by a string id. The ids point to arrays containing 'url', 'thumbnail_url', and 'description' keys.
 */
function register_default_headers( $headers ) {
	global $_wp_default_headers;

	$_wp_default_headers = array_merge( (array) $_wp_default_headers, (array) $headers );
}

/**
 * Unregister default headers.
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
 * Retrieve background image for custom background.
 *
 * @since 3.0.0
 *
 * @return string
 */
function get_background_image() {
	return get_theme_mod('background_image', get_theme_support( 'custom-background', 'default-image' ) );
}

/**
 * Display background image path.
 *
 * @since 3.0.0
 */
function background_image() {
	echo get_background_image();
}

/**
 * Retrieve value for custom background color.
 *
 * @since 3.0.0
 *
 * @return string
 */
function get_background_color() {
	return get_theme_mod('background_color', get_theme_support( 'custom-background', 'default-color' ) );
}

/**
 * Display background color value.
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
 * @access protected
 */
function _custom_background_cb() {
	// $background is the saved custom image, or the default image.
	$background = set_url_scheme( get_background_image() );

	// $color is the saved custom color.
	// A default has to be specified in style.css. It will not be printed here.
	$color = get_background_color();

	if ( $color === get_theme_support( 'custom-background', 'default-color' ) ) {
		$color = false;
	}

	if ( ! $background && ! $color )
		return;

	$style = $color ? "background-color: #$color;" : '';

	if ( $background ) {
		$image = " background-image: url('$background');";

		$repeat = get_theme_mod( 'background_repeat', get_theme_support( 'custom-background', 'default-repeat' ) );
		if ( ! in_array( $repeat, array( 'no-repeat', 'repeat-x', 'repeat-y', 'repeat' ) ) )
			$repeat = 'repeat';
		$repeat = " background-repeat: $repeat;";

		$position = get_theme_mod( 'background_position_x', get_theme_support( 'custom-background', 'default-position-x' ) );
		if ( ! in_array( $position, array( 'center', 'right', 'left' ) ) )
			$position = 'left';
		$position = " background-position: top $position;";

		$attachment = get_theme_mod( 'background_attachment', get_theme_support( 'custom-background', 'default-attachment' ) );
		if ( ! in_array( $attachment, array( 'fixed', 'scroll' ) ) )
			$attachment = 'scroll';
		$attachment = " background-attachment: $attachment;";

		$style .= $image . $repeat . $position . $attachment;
	}
?>
<style type="text/css" id="custom-background-css">
body.custom-background { <?php echo trim( $style ); ?> }
</style>
<?php
}

/**
 * Add callback for custom TinyMCE editor stylesheets.
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
 * 	                               Defaults to 'editor-style.css'
 */
function add_editor_style( $stylesheet = 'editor-style.css' ) {
	add_theme_support( 'editor-style' );

	if ( ! is_admin() )
		return;

	global $editor_styles;
	$editor_styles = (array) $editor_styles;
	$stylesheet    = (array) $stylesheet;
	if ( is_rtl() ) {
		$rtl_stylesheet = str_replace('.css', '-rtl.css', $stylesheet[0]);
		$stylesheet[] = $rtl_stylesheet;
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
	if ( ! current_theme_supports( 'editor-style' ) )
		return false;
	_remove_theme_support( 'editor-style' );
	if ( is_admin() )
		$GLOBALS['editor_styles'] = array();
	return true;
}

/**
 * Retrieve any registered editor stylesheets
 *
 * @since 4.0.0
 *
 * @global array $editor_styles Registered editor stylesheets
 *
 * @return array If registered, a list of editor stylesheet URLs.
 */
function get_editor_stylesheets() {
	$stylesheets = array();
	// load editor_style.css if the current theme supports it
	if ( ! empty( $GLOBALS['editor_styles'] ) && is_array( $GLOBALS['editor_styles'] ) ) {
		$editor_styles = $GLOBALS['editor_styles'];

		$editor_styles = array_unique( array_filter( $editor_styles ) );
		$style_uri = get_stylesheet_directory_uri();
		$style_dir = get_stylesheet_directory();

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
	 * Filter the array of stylesheets applied to the editor.
	 *
	 * @since 4.3.0
	 *
	 * @param array $stylesheets Array of stylesheets to be applied to the editor.
	 */
	return apply_filters( 'editor_stylesheets', $stylesheets );
}

/**
 * Allows a theme to register its support of a certain feature
 *
 * Must be called in the theme's functions.php file to work.
 * If attached to a hook, it must be after_setup_theme.
 * The init hook may be too late for some features.
 *
 * @since 2.9.0
 *
 * @global array $_wp_theme_features
 *
 * @param string $feature The feature being added.
 * @return void|bool False on failure, void otherwise.
 */
function add_theme_support( $feature ) {
	global $_wp_theme_features;

	if ( func_num_args() == 1 )
		$args = true;
	else
		$args = array_slice( func_get_args(), 1 );

	switch ( $feature ) {
		case 'post-formats' :
			if ( is_array( $args[0] ) ) {
				$post_formats = get_post_format_slugs();
				unset( $post_formats['standard'] );

				$args[0] = array_intersect( $args[0], array_keys( $post_formats ) );
			}
			break;

		case 'html5' :
			// You can't just pass 'html5', you need to pass an array of types.
			if ( empty( $args[0] ) ) {
				// Build an array of types for back-compat.
				$args = array( 0 => array( 'comment-list', 'comment-form', 'search-form' ) );
			} elseif ( ! is_array( $args[0] ) ) {
				_doing_it_wrong( "add_theme_support( 'html5' )", __( 'You need to pass an array of types.' ), '3.6.1' );
				return false;
			}

			// Calling 'html5' again merges, rather than overwrites.
			if ( isset( $_wp_theme_features['html5'] ) )
				$args[0] = array_merge( $_wp_theme_features['html5'][0], $args[0] );
			break;

		case 'custom-header-uploads' :
			return add_theme_support( 'custom-header', array( 'uploads' => true ) );

		case 'custom-header' :
			if ( ! is_array( $args ) )
				$args = array( 0 => array() );

			$defaults = array(
				'default-image' => '',
				'random-default' => false,
				'width' => 0,
				'height' => 0,
				'flex-height' => false,
				'flex-width' => false,
				'default-text-color' => '',
				'header-text' => true,
				'uploads' => true,
				'wp-head-callback' => '',
				'admin-head-callback' => '',
				'admin-preview-callback' => '',
			);

			$jit = isset( $args[0]['__jit'] );
			unset( $args[0]['__jit'] );

			// Merge in data from previous add_theme_support() calls.
			// The first value registered wins. (A child theme is set up first.)
			if ( isset( $_wp_theme_features['custom-header'] ) )
				$args[0] = wp_parse_args( $_wp_theme_features['custom-header'][0], $args[0] );

			// Load in the defaults at the end, as we need to insure first one wins.
			// This will cause all constants to be defined, as each arg will then be set to the default.
			if ( $jit )
				$args[0] = wp_parse_args( $args[0], $defaults );

			// If a constant was defined, use that value. Otherwise, define the constant to ensure
			// the constant is always accurate (and is not defined later,  overriding our value).
			// As stated above, the first value wins.
			// Once we get to wp_loaded (just-in-time), define any constants we haven't already.
			// Constants are lame. Don't reference them. This is just for backwards compatibility.

			if ( defined( 'NO_HEADER_TEXT' ) )
				$args[0]['header-text'] = ! NO_HEADER_TEXT;
			elseif ( isset( $args[0]['header-text'] ) )
				define( 'NO_HEADER_TEXT', empty( $args[0]['header-text'] ) );

			if ( defined( 'HEADER_IMAGE_WIDTH' ) )
				$args[0]['width'] = (int) HEADER_IMAGE_WIDTH;
			elseif ( isset( $args[0]['width'] ) )
				define( 'HEADER_IMAGE_WIDTH', (int) $args[0]['width'] );

			if ( defined( 'HEADER_IMAGE_HEIGHT' ) )
				$args[0]['height'] = (int) HEADER_IMAGE_HEIGHT;
			elseif ( isset( $args[0]['height'] ) )
				define( 'HEADER_IMAGE_HEIGHT', (int) $args[0]['height'] );

			if ( defined( 'HEADER_TEXTCOLOR' ) )
				$args[0]['default-text-color'] = HEADER_TEXTCOLOR;
			elseif ( isset( $args[0]['default-text-color'] ) )
				define( 'HEADER_TEXTCOLOR', $args[0]['default-text-color'] );

			if ( defined( 'HEADER_IMAGE' ) )
				$args[0]['default-image'] = HEADER_IMAGE;
			elseif ( isset( $args[0]['default-image'] ) )
				define( 'HEADER_IMAGE', $args[0]['default-image'] );

			if ( $jit && ! empty( $args[0]['default-image'] ) )
				$args[0]['random-default'] = false;

			// If headers are supported, and we still don't have a defined width or height,
			// we have implicit flex sizes.
			if ( $jit ) {
				if ( empty( $args[0]['width'] ) && empty( $args[0]['flex-width'] ) )
					$args[0]['flex-width'] = true;
				if ( empty( $args[0]['height'] ) && empty( $args[0]['flex-height'] ) )
					$args[0]['flex-height'] = true;
			}

			break;

		case 'custom-background' :
			if ( ! is_array( $args ) )
				$args = array( 0 => array() );

			$defaults = array(
				'default-image'          => '',
				'default-repeat'         => 'repeat',
				'default-position-x'     => 'left',
				'default-attachment'     => 'scroll',
				'default-color'          => '',
				'wp-head-callback'       => '_custom_background_cb',
				'admin-head-callback'    => '',
				'admin-preview-callback' => '',
			);

			$jit = isset( $args[0]['__jit'] );
			unset( $args[0]['__jit'] );

			// Merge in data from previous add_theme_support() calls. The first value registered wins.
			if ( isset( $_wp_theme_features['custom-background'] ) )
				$args[0] = wp_parse_args( $_wp_theme_features['custom-background'][0], $args[0] );

			if ( $jit )
				$args[0] = wp_parse_args( $args[0], $defaults );

			if ( defined( 'BACKGROUND_COLOR' ) )
				$args[0]['default-color'] = BACKGROUND_COLOR;
			elseif ( isset( $args[0]['default-color'] ) || $jit )
				define( 'BACKGROUND_COLOR', $args[0]['default-color'] );

			if ( defined( 'BACKGROUND_IMAGE' ) )
				$args[0]['default-image'] = BACKGROUND_IMAGE;
			elseif ( isset( $args[0]['default-image'] ) || $jit )
				define( 'BACKGROUND_IMAGE', $args[0]['default-image'] );

			break;

		// Ensure that 'title-tag' is accessible in the admin.
		case 'title-tag' :
			// Can be called in functions.php but must happen before wp_loaded, i.e. not in header.php.
			if ( did_action( 'wp_loaded' ) ) {
				/* translators: 1: Theme support 2: hook name */
				_doing_it_wrong( "add_theme_support( 'title-tag' )", sprintf( __( 'Theme support for %1$s should be registered before the %2$s hook.' ),
					'<code>title-tag</code>', '<code>wp_loaded</code>' ), '4.1' );

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
		if ( $args[0]['wp-head-callback'] )
			add_action( 'wp_head', $args[0]['wp-head-callback'] );

		if ( is_admin() ) {
			require_once( ABSPATH . 'wp-admin/custom-header.php' );
			$custom_image_header = new Custom_Image_Header( $args[0]['admin-head-callback'], $args[0]['admin-preview-callback'] );
		}
	}

	if ( current_theme_supports( 'custom-background' ) ) {
		// In case any constants were defined after an add_custom_background() call, re-run.
		add_theme_support( 'custom-background', array( '__jit' => true ) );

		$args = get_theme_support( 'custom-background' );
		add_action( 'wp_head', $args[0]['wp-head-callback'] );

		if ( is_admin() ) {
			require_once( ABSPATH . 'wp-admin/custom-background.php' );
			$custom_background = new Custom_Background( $args[0]['admin-head-callback'], $args[0]['admin-preview-callback'] );
		}
	}
}

/**
 * Gets the theme support arguments passed when registering that support
 *
 * @since 3.1.0
 *
 * @global array $_wp_theme_features
 *
 * @param string $feature the feature to check
 * @return mixed The array of extra arguments or the value for the registered feature.
 */
function get_theme_support( $feature ) {
	global $_wp_theme_features;
	if ( ! isset( $_wp_theme_features[ $feature ] ) )
		return false;

	if ( func_num_args() <= 1 )
		return $_wp_theme_features[ $feature ];

	$args = array_slice( func_get_args(), 1 );
	switch ( $feature ) {
		case 'custom-header' :
		case 'custom-background' :
			if ( isset( $_wp_theme_features[ $feature ][0][ $args[0] ] ) )
				return $_wp_theme_features[ $feature ][0][ $args[0] ];
			return false;

		default :
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
 * @see add_theme_support()
 * @param string $feature the feature being added
 * @return bool|void Whether feature was removed.
 */
function remove_theme_support( $feature ) {
	// Blacklist: for internal registrations not used directly by themes.
	if ( in_array( $feature, array( 'editor-style', 'widgets', 'menus' ) ) )
		return false;

	return _remove_theme_support( $feature );
}

/**
 * Do not use. Removes theme support internally, ignorant of the blacklist.
 *
 * @access private
 * @since 3.1.0
 *
 * @global array               $_wp_theme_features
 * @global Custom_Image_Header $custom_image_header
 * @global Custom_Background   $custom_background
 *
 * @param string $feature
 */
function _remove_theme_support( $feature ) {
	global $_wp_theme_features;

	switch ( $feature ) {
		case 'custom-header-uploads' :
			if ( ! isset( $_wp_theme_features['custom-header'] ) )
				return false;
			add_theme_support( 'custom-header', array( 'uploads' => false ) );
			return; // Do not continue - custom-header-uploads no longer exists.
	}

	if ( ! isset( $_wp_theme_features[ $feature ] ) )
		return false;

	switch ( $feature ) {
		case 'custom-header' :
			if ( ! did_action( 'wp_loaded' ) )
				break;
			$support = get_theme_support( 'custom-header' );
			if ( $support[0]['wp-head-callback'] )
				remove_action( 'wp_head', $support[0]['wp-head-callback'] );
			remove_action( 'admin_menu', array( $GLOBALS['custom_image_header'], 'init' ) );
			unset( $GLOBALS['custom_image_header'] );
			break;

		case 'custom-background' :
			if ( ! did_action( 'wp_loaded' ) )
				break;
			$support = get_theme_support( 'custom-background' );
			remove_action( 'wp_head', $support[0]['wp-head-callback'] );
			remove_action( 'admin_menu', array( $GLOBALS['custom_background'], 'init' ) );
			unset( $GLOBALS['custom_background'] );
			break;
	}

	unset( $_wp_theme_features[ $feature ] );
	return true;
}

/**
 * Checks a theme's support for a given feature
 *
 * @since 2.9.0
 *
 * @global array $_wp_theme_features
 *
 * @param string $feature the feature being checked
 * @return bool
 */
function current_theme_supports( $feature ) {
	global $_wp_theme_features;

	if ( 'custom-header-uploads' == $feature )
		return current_theme_supports( 'custom-header', 'uploads' );

	if ( !isset( $_wp_theme_features[$feature] ) )
		return false;

	if ( 'title-tag' == $feature ) {
		// Don't confirm support unless called internally.
		$trace = debug_backtrace();
		if ( ! in_array( $trace[1]['function'], array( '_wp_render_title_tag', 'wp_title' ) ) ) {
			return false;
		}
	}

	// If no args passed then no extra checks need be performed
	if ( func_num_args() <= 1 )
		return true;

	$args = array_slice( func_get_args(), 1 );

	switch ( $feature ) {
		case 'post-thumbnails':
			// post-thumbnails can be registered for only certain content/post types by passing
			// an array of types to add_theme_support(). If no array was passed, then
			// any type is accepted
			if ( true === $_wp_theme_features[$feature] )  // Registered for all types
				return true;
			$content_type = $args[0];
			return in_array( $content_type, $_wp_theme_features[$feature][0] );

		case 'html5':
		case 'post-formats':
			// specific post formats can be registered by passing an array of types to
			// add_theme_support()

			// Specific areas of HTML5 support *must* be passed via an array to add_theme_support()

			$type = $args[0];
			return in_array( $type, $_wp_theme_features[$feature][0] );

		case 'custom-header':
		case 'custom-background' :
			// specific custom header and background capabilities can be registered by passing
			// an array to add_theme_support()
			$header_support = $args[0];
			return ( isset( $_wp_theme_features[$feature][0][$header_support] ) && $_wp_theme_features[$feature][0][$header_support] );
	}

	/**
	 * Filter whether the current theme supports a specific feature.
	 *
	 * The dynamic portion of the hook name, `$feature`, refers to the specific theme
	 * feature. Possible values include 'post-formats', 'post-thumbnails', 'custom-background',
	 * 'custom-header', 'menus', 'automatic-feed-links', and 'html5'.
	 *
	 * @since 3.4.0
	 *
	 * @param bool   true     Whether the current theme supports the given feature. Default true.
	 * @param array  $args    Array of arguments for the feature.
	 * @param string $feature The theme feature.
	 */
	return apply_filters( "current_theme_supports-{$feature}", true, $args, $_wp_theme_features[$feature] );
}

/**
 * Checks a theme's support for a given feature before loading the functions which implement it.
 *
 * @since 2.9.0
 *
 * @param string $feature The feature being checked.
 * @param string $include Path to the file.
 * @return bool True if the current theme supports the supplied feature, false otherwise.
 */
function require_if_theme_supports( $feature, $include ) {
	if ( current_theme_supports( $feature ) ) {
		require ( $include );
		return true;
	}
	return false;
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
 *
 * @param int $id The attachment id.
 */
function _delete_attachment_theme_mod( $id ) {
	$attachment_image = wp_get_attachment_url( $id );
	$header_image     = get_header_image();
	$background_image = get_background_image();

	if ( $header_image && $header_image == $attachment_image ) {
		remove_theme_mod( 'header_image' );
		remove_theme_mod( 'header_image_data' );
	}

	if ( $background_image && $background_image == $attachment_image ) {
		remove_theme_mod( 'background_image' );
	}
}

/**
 * Checks if a theme has been changed and runs 'after_switch_theme' hook on the next WP load
 *
 * @since 3.3.0
 */
function check_theme_switched() {
	if ( $stylesheet = get_option( 'theme_switched' ) ) {
		$old_theme = wp_get_theme( $stylesheet );

		// Prevent retrieve_widgets() from running since Customizer already called it up front
		if ( get_option( 'theme_switched_via_customizer' ) ) {
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
			do_action( 'after_switch_theme', $stylesheet );
		}
		flush_rewrite_rules();

		update_option( 'theme_switched', false );
	}
}

/**
 * Includes and instantiates the WP_Customize_Manager class.
 *
 * Loads the Customizer at plugins_loaded when accessing the customize.php admin
 * page or when any request includes a wp_customize=on param, either as a GET
 * query var or as POST data. This param is a signal for whether to bootstrap
 * the Customizer when WordPress is loading, especially in the Customizer preview
 * or when making Customizer Ajax requests for widgets or menus.
 *
 * @since 3.4.0
 *
 * @global WP_Customize_Manager $wp_customize
 */
function _wp_customize_include() {
	if ( ! ( ( isset( $_REQUEST['wp_customize'] ) && 'on' == $_REQUEST['wp_customize'] )
		|| ( is_admin() && 'customize.php' == basename( $_SERVER['PHP_SELF'] ) )
	) ) {
		return;
	}

	require_once ABSPATH . WPINC . '/class-wp-customize-manager.php';
	$GLOBALS['wp_customize'] = new WP_Customize_Manager();
}

/**
 * Adds settings for the customize-loader script.
 *
 * @since 3.4.0
 */
function _wp_customize_loader_settings() {
	$admin_origin = parse_url( admin_url() );
	$home_origin  = parse_url( home_url() );
	$cross_domain = ( strtolower( $admin_origin[ 'host' ] ) != strtolower( $home_origin[ 'host' ] ) );

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
	$data = $wp_scripts->get_data( 'customize-loader', 'data' );
	if ( $data )
		$script = "$data\n$script";

	$wp_scripts->add_data( 'customize-loader', 'data', $script );
}

/**
 * Returns a URL to load the Customizer.
 *
 * @since 3.4.0
 *
 * @param string $stylesheet Optional. Theme to customize. Defaults to current theme.
 * 	                         The theme's stylesheet will be urlencoded if necessary.
 * @return string
 */
function wp_customize_url( $stylesheet = null ) {
	$url = admin_url( 'customize.php' );
	if ( $stylesheet )
		$url .= '?theme=' . urlencode( $stylesheet );
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
 */
function wp_customize_support_script() {
	$admin_origin = parse_url( admin_url() );
	$home_origin  = parse_url( home_url() );
	$cross_domain = ( strtolower( $admin_origin[ 'host' ] ) != strtolower( $home_origin[ 'host' ] ) );

	?>
	<script type="text/javascript">
		(function() {
			var request, b = document.body, c = 'className', cs = 'customize-support', rcs = new RegExp('(^|\\s+)(no-)?'+cs+'(\\s+|$)');

<?php		if ( $cross_domain ): ?>
			request = (function(){ var xhr = new XMLHttpRequest(); return ('withCredentials' in xhr); })();
<?php		else: ?>
			request = true;
<?php		endif; ?>

			b[c] = b[c].replace( rcs, ' ' );
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
