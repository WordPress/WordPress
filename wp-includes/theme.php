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
 * @param array $args The search arguments. Optional.
 * - errors      mixed  True to return themes with errors, false to return themes without errors, null
 *                      to return all themes. Defaults to false.
 * - allowed     mixed  (Multisite) True to return only allowed themes for a site. False to return only
 *                      disallowed themes for a site. 'site' to return only site-allowed themes. 'network'
 *                      to return only network-allowed themes. Null to return all themes. Defaults to null.
 * - blog_id     int    (Multisite) The blog ID used to calculate which themes are allowed. Defaults to 0,
 *                      synonymous for the current blog.
 * @return Array of WP_Theme objects.
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
 * @param string $stylesheet Directory name for the theme. Optional. Defaults to current theme.
 * @param string $theme_root Absolute path of the theme root to look in. Optional. If not specified, get_raw_theme_root()
 * 	is used to calculate the theme root for the $stylesheet provided (or current theme).
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
 * For all extensive purposes, the template name and the stylesheet name are
 * going to be the same for most cases.
 *
 * @since 1.5.0
 * @uses apply_filters() Calls 'stylesheet' filter on stylesheet name.
 *
 * @return string Stylesheet name.
 */
function get_stylesheet() {
	return apply_filters('stylesheet', get_option('stylesheet'));
}

/**
 * Retrieve stylesheet directory path for current theme.
 *
 * @since 1.5.0
 * @uses apply_filters() Calls 'stylesheet_directory' filter on stylesheet directory and theme name.
 *
 * @return string Path to current theme directory.
 */
function get_stylesheet_directory() {
	$stylesheet = get_stylesheet();
	$theme_root = get_theme_root( $stylesheet );
	$stylesheet_dir = "$theme_root/$stylesheet";

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
	$stylesheet = get_stylesheet();
	$theme_root_uri = get_theme_root_uri( $stylesheet );
	$stylesheet_dir_uri = "$theme_root_uri/$stylesheet";

	return apply_filters( 'stylesheet_directory_uri', $stylesheet_dir_uri, $stylesheet, $theme_root_uri );
}

/**
 * Retrieve URI of current theme stylesheet.
 *
 * The stylesheet file name is 'style.css' which is appended to {@link
 * get_stylesheet_directory_uri() stylesheet directory URI} path.
 *
 * @since 1.5.0
 * @uses apply_filters() Calls 'stylesheet_uri' filter on stylesheet URI path and stylesheet directory URI.
 *
 * @return string
 */
function get_stylesheet_uri() {
	$stylesheet_dir_uri = get_stylesheet_directory_uri();
	$stylesheet_uri = $stylesheet_dir_uri . '/style.css';
	return apply_filters('stylesheet_uri', $stylesheet_uri, $stylesheet_dir_uri);
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
 * @uses apply_filters() Calls 'locale_stylesheet_uri' filter on stylesheet URI path and stylesheet directory URI.
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
	return apply_filters('locale_stylesheet_uri', $stylesheet_uri, $stylesheet_dir_uri);
}

/**
 * Retrieve name of the current theme.
 *
 * @since 1.5.0
 * @uses apply_filters() Calls 'template' filter on template option.
 *
 * @return string Template name.
 */
function get_template() {
	return apply_filters('template', get_option('template'));
}

/**
 * Retrieve current theme directory.
 *
 * @since 1.5.0
 * @uses apply_filters() Calls 'template_directory' filter on template directory path and template name.
 *
 * @return string Template directory path.
 */
function get_template_directory() {
	$template = get_template();
	$theme_root = get_theme_root( $template );
	$template_dir = "$theme_root/$template";

	return apply_filters( 'template_directory', $template_dir, $template, $theme_root );
}

/**
 * Retrieve theme directory URI.
 *
 * @since 1.5.0
 * @uses apply_filters() Calls 'template_directory_uri' filter on template directory URI path and template name.
 *
 * @return string Template directory URI.
 */
function get_template_directory_uri() {
	$template = get_template();
	$theme_root_uri = get_theme_root_uri( $template );
	$template_dir_uri = "$theme_root_uri/$template";

	return apply_filters( 'template_directory_uri', $template_dir_uri, $template, $theme_root_uri );
}

/**
 * Retrieve theme roots.
 *
 * @since 2.9.0
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
 * @param string $directory Either the full filesystem path to a theme folder or a folder within WP_CONTENT_DIR
 * @return bool
 */
function register_theme_directory( $directory ) {
	global $wp_theme_directories;

	if ( ! file_exists( $directory ) ) {
		// Try prepending as the theme directory could be relative to the content directory
		$directory = WP_CONTENT_DIR . '/' . $directory;
		// If this directory does not exist, return and do not register
		if ( ! file_exists( $directory ) )
			return false;
	}

	$wp_theme_directories[] = $directory;

	return true;
}

/**
 * Search all registered theme directories for complete and valid themes.
 *
 * @since 2.9.0
 *
 * @param bool $force Optional. Whether to force a new directory scan. Defaults to false.
 * @return array Valid themes found
 */
function search_theme_directories( $force = false ) {
	global $wp_theme_directories;
	if ( empty( $wp_theme_directories ) )
		return false;

	static $found_themes;
	if ( ! $force && isset( $found_themes ) )
		return $found_themes;

	$found_themes = array();

	$wp_theme_directories = (array) $wp_theme_directories;

	// Set up maybe-relative, maybe-absolute array of theme directories.
	// We always want to return absolute, but we need to cache relative
	// use in for get_theme_root().
	foreach ( $wp_theme_directories as $theme_root ) {
		if ( 0 === strpos( $theme_root, WP_CONTENT_DIR ) )
			$relative_theme_roots[ str_replace( WP_CONTENT_DIR, '', $theme_root ) ] = $theme_root;
		else
			$relative_theme_roots[ $theme_root ] = $theme_root;
	}

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
		if ( ! $dirs )
			return false;
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
				if ( ! $sub_dirs )
					return false;
				foreach ( $sub_dirs as $sub_dir ) {
					if ( ! is_dir( $theme_root . '/' . $dir ) || $dir[0] == '.' || $dir == 'CVS' )
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
 * @uses apply_filters() Calls 'theme_root' filter on path.
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

	return apply_filters( 'theme_root', $theme_root );
}

/**
 * Retrieve URI for themes directory.
 *
 * Does not have trailing slash.
 *
 * @since 1.5.0
 *
 * @param string $stylesheet_or_template Optional. The stylesheet or template name of the theme.
 * 	Default is to leverage the main theme root.
 * @param string $theme_root Optional. The theme root for which calculations will be based, preventing
 * 	the need for a get_raw_theme_root() call.
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

	return apply_filters( 'theme_root_uri', $theme_root_uri, get_option('siteurl'), $stylesheet_or_template );
}

/**
 * Get the raw theme root relative to the content directory with no filters applied.
 *
 * @since 3.1.0
 *
 * @param string $stylesheet_or_template The stylesheet or template name of the theme
 * @param bool $skip_cache Optional. Whether to skip the cache. Defaults to false, meaning the cache is used.
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
 * Start preview theme output buffer.
 *
 * Will only preform task if the user has permissions and template and preview
 * query variables exist.
 *
 * @since 2.6.0
 */
function preview_theme() {
	if ( ! (isset($_GET['template']) && isset($_GET['preview'])) )
		return;

	if ( !current_user_can( 'switch_themes' ) )
		return;

	// Admin Thickbox requests
	if ( isset( $_GET['preview_iframe'] ) )
		show_admin_bar( false );

	$_GET['template'] = preg_replace('|[^a-z0-9_./-]|i', '', $_GET['template']);

	if ( validate_file($_GET['template']) )
		return;

	add_filter( 'template', '_preview_theme_template_filter' );

	if ( isset($_GET['stylesheet']) ) {
		$_GET['stylesheet'] = preg_replace('|[^a-z0-9_./-]|i', '', $_GET['stylesheet']);
		if ( validate_file($_GET['stylesheet']) )
			return;
		add_filter( 'stylesheet', '_preview_theme_stylesheet_filter' );
	}

	// Prevent theme mods to current theme being used on theme being previewed
	add_filter( 'pre_option_theme_mods_' . get_option( 'stylesheet' ), '__return_empty_array' );

	ob_start( 'preview_theme_ob_filter' );
}
add_action('setup_theme', 'preview_theme');

/**
 * Private function to modify the current template when previewing a theme
 *
 * @since 2.9.0
 * @access private
 *
 * @return string
 */
function _preview_theme_template_filter() {
	return isset($_GET['template']) ? $_GET['template'] : '';
}

/**
 * Private function to modify the current stylesheet when previewing a theme
 *
 * @since 2.9.0
 * @access private
 *
 * @return string
 */
function _preview_theme_stylesheet_filter() {
	return isset($_GET['stylesheet']) ? $_GET['stylesheet'] : '';
}

/**
 * Callback function for ob_start() to capture all links in the theme.
 *
 * @since 2.6.0
 * @access private
 *
 * @param string $content
 * @return string
 */
function preview_theme_ob_filter( $content ) {
	return preg_replace_callback( "|(<a.*?href=([\"']))(.*?)([\"'].*?>)|", 'preview_theme_ob_filter_callback', $content );
}

/**
 * Manipulates preview theme links in order to control and maintain location.
 *
 * Callback function for preg_replace_callback() to accept and filter matches.
 *
 * @since 2.6.0
 * @access private
 *
 * @param array $matches
 * @return string
 */
function preview_theme_ob_filter_callback( $matches ) {
	if ( strpos($matches[4], 'onclick') !== false )
		$matches[4] = preg_replace('#onclick=([\'"]).*?(?<!\\\)\\1#i', '', $matches[4]); //Strip out any onclicks from rest of <a>. (?<!\\\) means to ignore the '" if its escaped by \  to prevent breaking mid-attribute.
	if (
		( false !== strpos($matches[3], '/wp-admin/') )
	||
		( false !== strpos( $matches[3], '://' ) && 0 !== strpos( $matches[3], home_url() ) )
	||
		( false !== strpos($matches[3], '/feed/') )
	||
		( false !== strpos($matches[3], '/trackback/') )
	)
		return $matches[1] . "#$matches[2] onclick=$matches[2]return false;" . $matches[4];

	$link = add_query_arg( array( 'preview' => 1, 'template' => $_GET['template'], 'stylesheet' => @$_GET['stylesheet'], 'preview_iframe' => 1 ), $matches[3] );
	if ( 0 === strpos($link, 'preview=1') )
		$link = "?$link";
	return $matches[1] . esc_attr( $link ) . $matches[4];
}

/**
 * Switches current theme to new template and stylesheet names.
 *
 * @since 2.5.0
 * @uses do_action() Calls 'switch_theme' action, passing the new theme.
 *
 * @param string $template Template name
 * @param string $stylesheet Stylesheet name.
 */
function switch_theme( $template, $stylesheet ) {
	global $wp_theme_directories, $sidebars_widgets;

	if ( is_array( $sidebars_widgets ) )
		set_theme_mod( 'sidebars_widgets', array( 'time' => time(), 'data' => $sidebars_widgets ) );

	$old_theme  = wp_get_theme();
	$new_theme = wp_get_theme( $stylesheet );
	$new_name  = $new_theme->get('Name');

	update_option( 'template', $template );
	update_option( 'stylesheet', $stylesheet );

	if ( count( $wp_theme_directories ) > 1 ) {
		update_option( 'template_root', get_raw_theme_root( $template, true ) );
		update_option( 'stylesheet_root', get_raw_theme_root( $stylesheet, true ) );
	}

	update_option( 'current_theme', $new_name );

	if ( is_admin() && false === get_option( 'theme_mods_' . $stylesheet ) ) {
		$default_theme_mods = (array) get_option( 'mods_' . $new_name );
		add_option( "theme_mods_$stylesheet", $default_theme_mods );
	}

	update_option( 'theme_switched', $old_theme->get_stylesheet() );
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
	// Don't validate during an install/upgrade.
	if ( defined('WP_INSTALLING') || !apply_filters( 'validate_current_theme', true ) )
		return true;

	if ( get_template() != WP_DEFAULT_THEME && !file_exists(get_template_directory() . '/index.php') ) {
		switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );
		return false;
	}

	if ( get_stylesheet() != WP_DEFAULT_THEME && !file_exists(get_template_directory() . '/style.css') ) {
		switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );
		return false;
	}

	if ( is_child_theme() && ! file_exists( get_stylesheet_directory() . '/style.css' ) ) {
		switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );
		return false;
	}

	return true;
}

/**
 * Retrieve all theme modifications.
 *
 * @since 3.1.0
 *
 * @return array Theme modifications.
 */
function get_theme_mods() {
	$theme_slug = get_option( 'stylesheet' );
	if ( false === ( $mods = get_option( "theme_mods_$theme_slug" ) ) ) {
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
 * @uses apply_filters() Calls 'theme_mod_$name' filter on the value.
 *
 * @param string $name Theme modification name.
 * @param bool|string $default
 * @return string
 */
function get_theme_mod( $name, $default = false ) {
	$mods = get_theme_mods();

	if ( isset( $mods[ $name ] ) )
		return apply_filters( "theme_mod_$name", $mods[ $name ] );

	if ( is_string( $default ) )
		$default = sprintf( $default, get_template_directory_uri(), get_stylesheet_directory_uri() );

	return apply_filters( "theme_mod_$name", $default );
}

/**
 * Update theme modification value for the current theme.
 *
 * @since 2.1.0
 *
 * @param string $name Theme modification name.
 * @param string $value theme modification value.
 */
function set_theme_mod( $name, $value ) {
	$mods = get_theme_mods();

	$mods[ $name ] = $value;

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
 * @return null
 */
function remove_theme_mod( $name ) {
	$mods = get_theme_mods();

	if ( ! isset( $mods[ $name ] ) )
		return;

	unset( $mods[ $name ] );

	if ( empty( $mods ) )
		return remove_theme_mods();

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
	return 'blank' != $text_color;
}

/**
 * Retrieve header image for custom header.
 *
 * @since 2.1.0
 *
 * @return string
 */
function get_header_image() {
	$url = get_theme_mod( 'header_image', get_theme_support( 'custom-header', 'default-image' ) );

	if ( 'remove-header' == $url )
		return false;

	if ( is_random_header_image() )
		$url = get_random_header_image();

	if ( is_ssl() )
		$url = str_replace( 'http://', 'https://', $url );
	else
		$url = str_replace( 'https://', 'http://', $url );

	return esc_url_raw( $url );
}

/**
 * Get random header image data from registered images in theme.
 *
 * @since 3.4.0
 *
 * @access private
 *
 * @return string Path to header image
 */

function _get_random_header_data() {
	static $_wp_random_header;

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
 * @return boolean
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
 * Display header image path.
 *
 * @since 2.1.0
 */
function header_image() {
	echo get_header_image();
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
		$url = esc_url_raw( $header->guid );
		$header_data = wp_get_attachment_metadata( $header->ID );
		$header_index = basename($url);
		$header_images[$header_index] = array();
		$header_images[$header_index]['attachment_id'] =  $header->ID;
		$header_images[$header_index]['url'] =  $url;
		$header_images[$header_index]['thumbnail_url'] =  $url;
		$header_images[$header_index]['width'] = $header_data['width'];
		$header_images[$header_index]['height'] = $header_data['height'];
	}

	return $header_images;
}

/**
 * Get the header image data.
 *
 * @since 3.4.0
 *
 * @return object
 */
function get_custom_header() {
	$data = is_random_header_image()? _get_random_header_data() : get_theme_mod( 'header_image_data' );
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
 * @param string|array $header The header string id (key of array) to remove, or an array thereof.
 * @return True on success, false on failure.
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
	$background = get_background_image();

	// $color is the saved custom color.
	// A default has to be specified in style.css. It will not be printed here.
	$color = get_theme_mod( 'background_color' );

	if ( ! $background && ! $color )
		return;

	$style = $color ? "background-color: #$color;" : '';

	if ( $background ) {
		$image = " background-image: url('$background');";

		$repeat = get_theme_mod( 'background_repeat', 'repeat' );
		if ( ! in_array( $repeat, array( 'no-repeat', 'repeat-x', 'repeat-y', 'repeat' ) ) )
			$repeat = 'repeat';
		$repeat = " background-repeat: $repeat;";

		$position = get_theme_mod( 'background_position_x', 'left' );
		if ( ! in_array( $position, array( 'center', 'right', 'left' ) ) )
			$position = 'left';
		$position = " background-position: top $position;";

		$attachment = get_theme_mod( 'background_attachment', 'scroll' );
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
 * @param mixed $stylesheet Optional. Stylesheet name or array thereof, relative to theme root.
 * 	Defaults to 'editor-style.css'
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
 * Allows a theme to register its support of a certain feature
 *
 * Must be called in the theme's functions.php file to work.
 * If attached to a hook, it must be after_setup_theme.
 * The init hook may be too late for some features.
 *
 * @since 2.9.0
 * @param string $feature the feature being added
 */
function add_theme_support( $feature ) {
	global $_wp_theme_features;

	if ( func_num_args() == 1 )
		$args = true;
	else
		$args = array_slice( func_get_args(), 1 );

	switch ( $feature ) {
		case 'post-formats' :
			if ( is_array( $args[0] ) )
				$args[0] = array_intersect( $args[0], array_keys( get_post_format_slugs() ) );
			break;

		case 'custom-header-uploads' :
			return add_theme_support( 'custom-header', array( 'uploads' => true ) );
			break;

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
				'default-image' => '',
				'default-color' => '',
				'wp-head-callback' => '_custom_background_cb',
				'admin-head-callback' => '',
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
	}

	$_wp_theme_features[ $feature ] = $args;
}

/**
 * Registers the internal custom header and background routines.
 *
 * @since 3.4.0
 * @access private
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
add_action( 'wp_loaded', '_custom_header_background_just_in_time' );

/**
 * Gets the theme support arguments passed when registering that support
 *
 * @since 3.1
 * @param string $feature the feature to check
 * @return array The array of extra arguments
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
			break;
		default :
			return $_wp_theme_features[ $feature ];
			break;
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
 * @return bool Whether feature was removed.
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
			$support = get_theme_support( 'custom-header' );
			if ( $support[0]['wp-head-callback'] )
				remove_action( 'wp_head', $support[0]['wp-head-callback'] );
			remove_action( 'admin_menu', array( $GLOBALS['custom_image_header'], 'init' ) );
			unset( $GLOBALS['custom_image_header'] );
			break;

		case 'custom-background' :
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
 * @param string $feature the feature being checked
 * @return boolean
 */
function current_theme_supports( $feature ) {
	global $_wp_theme_features;

	if ( 'custom-header-uploads' == $feature )
		return current_theme_supports( 'custom-header', 'uploads' );

	if ( !isset( $_wp_theme_features[$feature] ) )
		return false;

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
			break;

		case 'post-formats':
			// specific post formats can be registered by passing an array of types to
			// add_theme_support()
			$post_format = $args[0];
			return in_array( $post_format, $_wp_theme_features[$feature][0] );
			break;

		case 'custom-header':
		case 'custom-background' :
			// specific custom header and background capabilities can be registered by passing
			// an array to add_theme_support()
			$header_support = $args[0];
			return ( isset( $_wp_theme_features[$feature][0][$header_support] ) && $_wp_theme_features[$feature][0][$header_support] );
			break;
	}

	return apply_filters('current_theme_supports-' . $feature, true, $args, $_wp_theme_features[$feature]);
}

/**
 * Checks a theme's support for a given feature before loading the functions which implement it.
 *
 * @since 2.9.0
 * @param string $feature the feature being checked
 * @param string $include the file containing the functions that implement the feature
 */
function require_if_theme_supports( $feature, $include) {
	if ( current_theme_supports( $feature ) )
		require ( $include );
}

/**
 * Checks an attachment being deleted to see if it's a header or background image.
 *
 * If true it removes the theme modification which would be pointing at the deleted
 * attachment
 *
 * @access private
 * @since 3.0.0
 * @param int $id the attachment id
 */
function _delete_attachment_theme_mod( $id ) {
	$attachment_image = wp_get_attachment_url( $id );
	$header_image = get_header_image();
	$background_image = get_background_image();

	if ( $header_image && $header_image == $attachment_image )
		remove_theme_mod( 'header_image' );

	if ( $background_image && $background_image == $attachment_image )
		remove_theme_mod( 'background_image' );
}

add_action( 'delete_attachment', '_delete_attachment_theme_mod' );

/**
 * Checks if a theme has been changed and runs 'after_switch_theme' hook on the next WP load
 *
 * @since 3.3.0
 */
function check_theme_switched() {
	if ( $stylesheet = get_option( 'theme_switched' ) ) {
		$old_theme = wp_get_theme( $stylesheet );

		if ( $old_theme->exists() )
			do_action( 'after_switch_theme', $old_theme->get('Name'), $old_theme );
		else
			do_action( 'after_switch_theme', $stylesheet );

		update_option( 'theme_switched', false );
	}
}

/**
 * Includes and instantiates the WP_Customize_Manager class.
 *
 * Fires when ?wp_customize=on or on wp-admin/customize.php.
 *
 * @since 3.4.0
 */
function _wp_customize_include() {
	if ( ! ( ( isset( $_REQUEST['wp_customize'] ) && 'on' == $_REQUEST['wp_customize'] )
		|| ( is_admin() && 'customize.php' == basename( $_SERVER['PHP_SELF'] ) )
	) )
		return;

	require( ABSPATH . WPINC . '/class-wp-customize-manager.php' );
	// Init Customize class
	$GLOBALS['wp_customize'] = new WP_Customize_Manager;
}
add_action( 'plugins_loaded', '_wp_customize_include' );

/**
 * Adds settings for the customize-loader script.
 *
 * @since 3.4.0
 */
function _wp_customize_loader_settings() {
	global $wp_scripts;

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
	);

	$script = 'var _wpCustomizeLoaderSettings = ' . json_encode( $settings ) . ';';

	$data = $wp_scripts->get_data( 'customize-loader', 'data' );
	if ( $data )
		$script = "$data\n$script";

	$wp_scripts->add_data( 'customize-loader', 'data', $script );
}
add_action( 'admin_enqueue_scripts', '_wp_customize_loader_settings' );

/**
 * Returns a URL to load the theme customizer.
 *
 * @since 3.4.0
 *
 * @param string $stylesheet Optional. Theme to customize. Defaults to current theme.
 */
function wp_customize_url( $stylesheet = null ) {
	$url = admin_url( 'customize.php' );
	if ( $stylesheet )
		$url .= '?theme=' . $stylesheet;
	return esc_url( $url );
}

/**
 * Prints a script to check whether or not the customizer is supported,
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

			b[c] = b[c].replace( rcs, '' );
			b[c] += ( window.postMessage && request ? ' ' : ' no-' ) + cs;
		}());
	</script>
	<?php
}