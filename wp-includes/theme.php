<?php
/**
 * Theme, template, and stylesheet functions.
 *
 * @package WordPress
 * @subpackage Template
 */

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
	$stylesheet_uri = $stylesheet_dir_uri . "/style.css";
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
 * Retrieve theme data from parsed theme file.
 *
 * The description will have the tags filtered with the following HTML elements
 * whitelisted. The <b>'a'</b> element with the <em>href</em> and <em>title</em>
 * attributes. The <b>abbr</b> element with the <em>title</em> attribute. The
 * <b>acronym<b> element with the <em>title</em> attribute allowed. The
 * <b>code</b>, <b>em</b>, and <b>strong</b> elements also allowed.
 *
 * The style.css file must contain theme name, theme URI, and description. The
 * data can also contain author URI, author, template (parent template),
 * version, status, and finally tags. Some of these are not used by WordPress
 * administration panels, but are used by theme directory web sites which list
 * the theme.
 *
 * @since 1.5.0
 *
 * @param string $theme_file Theme file path.
 * @return array Theme data.
 */
function get_theme_data( $theme_file ) {
	$default_headers = array(
		'Name' => 'Theme Name',
		'URI' => 'Theme URI',
		'Description' => 'Description',
		'Author' => 'Author',
		'AuthorURI' => 'Author URI',
		'Version' => 'Version',
		'Template' => 'Template',
		'Status' => 'Status',
		'Tags' => 'Tags'
		);

	$themes_allowed_tags = array(
		'a' => array(
			'href' => array(),'title' => array()
			),
		'abbr' => array(
			'title' => array()
			),
		'acronym' => array(
			'title' => array()
			),
		'code' => array(),
		'em' => array(),
		'strong' => array()
	);

	$theme_data = get_file_data( $theme_file, $default_headers, 'theme' );

	$theme_data['Name'] = $theme_data['Title'] = wp_kses( $theme_data['Name'], $themes_allowed_tags );

	$theme_data['URI'] = esc_url( $theme_data['URI'] );

	$theme_data['Description'] = wptexturize( wp_kses( $theme_data['Description'], $themes_allowed_tags ) );

	$theme_data['AuthorURI'] = esc_url( $theme_data['AuthorURI'] );

	$theme_data['Template'] = wp_kses( $theme_data['Template'], $themes_allowed_tags );

	$theme_data['Version'] = wp_kses( $theme_data['Version'], $themes_allowed_tags );

	if ( $theme_data['Status'] == '' )
		$theme_data['Status'] = 'publish';
	else
		$theme_data['Status'] = wp_kses( $theme_data['Status'], $themes_allowed_tags );

	if ( $theme_data['Tags'] == '' )
		$theme_data['Tags'] = array();
	else
		$theme_data['Tags'] = array_map( 'trim', explode( ',', wp_kses( $theme_data['Tags'], array() ) ) );

	if ( $theme_data['Author'] == '' ) {
		$theme_data['Author'] = __('Anonymous');
	} else {
		if ( empty( $theme_data['AuthorURI'] ) ) {
			$theme_data['Author'] = wp_kses( $theme_data['Author'], $themes_allowed_tags );
		} else {
			$theme_data['Author'] = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', $theme_data['AuthorURI'], __( 'Visit author homepage' ), wp_kses( $theme_data['Author'], $themes_allowed_tags ) );
		}
	}

	return $theme_data;
}

/**
 * Retrieve list of themes with theme data in theme directory.
 *
 * The theme is broken, if it doesn't have a parent theme and is missing either
 * style.css and, or index.php. If the theme has a parent theme then it is
 * broken, if it is missing style.css; index.php is optional. The broken theme
 * list is saved in the {@link $wp_broken_themes} global, which is displayed on
 * the theme list in the administration panels.
 *
 * @since 1.5.0
 * @global array $wp_broken_themes Stores the broken themes.
 * @global array $wp_themes Stores the working themes.
 *
 * @return array Theme list with theme data.
 */
function get_themes() {
	global $wp_themes, $wp_broken_themes;

	if ( isset($wp_themes) )
		return $wp_themes;

	/* Register the default root as a theme directory */
	register_theme_directory( get_theme_root() );

	if ( !$theme_files = search_theme_directories() )
		return false;

	asort( $theme_files );

	$wp_themes = array();

	foreach ( (array) $theme_files as $theme_file ) {
		$theme_root = $theme_file['theme_root'];
		$theme_file = $theme_file['theme_file'];

		if ( !is_readable("$theme_root/$theme_file") ) {
			$wp_broken_themes[$theme_file] = array('Name' => $theme_file, 'Title' => $theme_file, 'Description' => __('File not readable.'));
			continue;
		}

		$theme_data = get_theme_data("$theme_root/$theme_file");

		$name        = $theme_data['Name'];
		$title       = $theme_data['Title'];
		$description = wptexturize($theme_data['Description']);
		$version     = $theme_data['Version'];
		$author      = $theme_data['Author'];
		$template    = $theme_data['Template'];
		$stylesheet  = dirname($theme_file);

		$screenshot = false;
		foreach ( array('png', 'gif', 'jpg', 'jpeg') as $ext ) {
			if (file_exists("$theme_root/$stylesheet/screenshot.$ext")) {
				$screenshot = "screenshot.$ext";
				break;
			}
		}

		if ( empty($name) ) {
			$name = dirname($theme_file);
			$title = $name;
		}

		$parent_template = $template;

		if ( empty($template) ) {
			if ( file_exists("$theme_root/$stylesheet/index.php") )
				$template = $stylesheet;
			else
				continue;
		}

		$template = trim( $template );

		if ( !file_exists("$theme_root/$template/index.php") ) {
			$parent_dir = dirname(dirname($theme_file));
			if ( file_exists("$theme_root/$parent_dir/$template/index.php") ) {
				$template = "$parent_dir/$template";
				$template_directory = "$theme_root/$template";
			} else {
				/**
				 * The parent theme doesn't exist in the current theme's folder or sub folder
				 * so lets use the theme root for the parent template.
				 */
				if ( isset($theme_files[$template]) && file_exists( $theme_files[$template]['theme_root'] . "/$template/index.php" ) ) {
					$template_directory = $theme_files[$template]['theme_root'] . "/$template";
				} else {
					if ( empty( $parent_template) )
						$wp_broken_themes[$name] = array('Name' => $name, 'Title' => $title, 'Description' => __('Template is missing.'), 'error' => 'no_template');
					else
						$wp_broken_themes[$name] = array('Name' => $name, 'Title' => $title, 'Description' => sprintf( __('The parent theme is missing. Please install the "%s" parent theme.'),  $parent_template ), 'error' => 'no_parent', 'parent' => $parent_template );
					continue;
				}

			}
		} else {
			$template_directory = trim( $theme_root . '/' . $template );
		}

		$stylesheet_files = array();
		$template_files = array();

		$stylesheet_dir = @ dir("$theme_root/$stylesheet");
		if ( $stylesheet_dir ) {
			while ( ($file = $stylesheet_dir->read()) !== false ) {
				if ( !preg_match('|^\.+$|', $file) ) {
					if ( preg_match('|\.css$|', $file) )
						$stylesheet_files[] = "$theme_root/$stylesheet/$file";
					elseif ( preg_match('|\.php$|', $file) )
						$template_files[] = "$theme_root/$stylesheet/$file";
				}
			}
			@ $stylesheet_dir->close();
		}

		$template_dir = @ dir("$template_directory");
		if ( $template_dir ) {
			while ( ($file = $template_dir->read()) !== false ) {
				if ( preg_match('|^\.+$|', $file) )
					continue;
				if ( preg_match('|\.php$|', $file) ) {
					$template_files[] = "$template_directory/$file";
				} elseif ( is_dir("$template_directory/$file") ) {
					$template_subdir = @ dir("$template_directory/$file");
					if ( !$template_subdir )
						continue;
					while ( ($subfile = $template_subdir->read()) !== false ) {
						if ( preg_match('|^\.+$|', $subfile) )
							continue;
						if ( preg_match('|\.php$|', $subfile) )
							$template_files[] = "$template_directory/$file/$subfile";
					}
					@ $template_subdir->close();
				}
			}
			@ $template_dir->close();
		}

		//Make unique and remove duplicates when stylesheet and template are the same i.e. most themes
		$template_files = array_unique($template_files);
		$stylesheet_files = array_unique($stylesheet_files);

		$template_dir = dirname($template_files[0]);
		$stylesheet_dir = dirname($stylesheet_files[0]);

		if ( empty($template_dir) )
			$template_dir = '/';
		if ( empty($stylesheet_dir) )
			$stylesheet_dir = '/';

		// Check for theme name collision.  This occurs if a theme is copied to
		// a new theme directory and the theme header is not updated.  Whichever
		// theme is first keeps the name.  Subsequent themes get a suffix applied.
		// The Default and Classic themes always trump their pretenders.
		if ( isset($wp_themes[$name]) ) {
			if ( ('WordPress Default' == $name || 'WordPress Classic' == $name) &&
					 ('default' == $stylesheet || 'classic' == $stylesheet) ) {
				// If another theme has claimed to be one of our default themes, move
				// them aside.
				$suffix = $wp_themes[$name]['Stylesheet'];
				$new_name = "$name/$suffix";
				$wp_themes[$new_name] = $wp_themes[$name];
				$wp_themes[$new_name]['Name'] = $new_name;
			} else {
				$name = "$name/$stylesheet";
			}
		}

		$theme_roots[$stylesheet] = str_replace( WP_CONTENT_DIR, '', $theme_root );
		$wp_themes[$name] = array( 'Name' => $name, 'Title' => $title, 'Description' => $description, 'Author' => $author, 'Version' => $version, 'Template' => $template, 'Stylesheet' => $stylesheet, 'Template Files' => $template_files, 'Stylesheet Files' => $stylesheet_files, 'Template Dir' => $template_dir, 'Stylesheet Dir' => $stylesheet_dir, 'Status' => $theme_data['Status'], 'Screenshot' => $screenshot, 'Tags' => $theme_data['Tags'], 'Theme Root' => $theme_root, 'Theme Root URI' => str_replace( WP_CONTENT_DIR, content_url(), $theme_root ) );
	}

	unset($theme_files);

	/* Store theme roots in the DB */
	if ( get_site_transient( 'theme_roots' ) != $theme_roots )
		set_site_transient( 'theme_roots', $theme_roots, 7200 ); // cache for two hours
	unset($theme_roots);

	/* Resolve theme dependencies. */
	$theme_names = array_keys( $wp_themes );
	foreach ( (array) $theme_names as $theme_name ) {
		$wp_themes[$theme_name]['Parent Theme'] = '';
		if ( $wp_themes[$theme_name]['Stylesheet'] != $wp_themes[$theme_name]['Template'] ) {
			foreach ( (array) $theme_names as $parent_theme_name ) {
				if ( ($wp_themes[$parent_theme_name]['Stylesheet'] == $wp_themes[$parent_theme_name]['Template']) && ($wp_themes[$parent_theme_name]['Template'] == $wp_themes[$theme_name]['Template']) ) {
					$wp_themes[$theme_name]['Parent Theme'] = $wp_themes[$parent_theme_name]['Name'];
					break;
				}
			}
		}
	}

	return $wp_themes;
}

/**
 * Retrieve theme roots.
 *
 * @since 2.9.0
 *
 * @return array Theme roots
 */
function get_theme_roots() {
	$theme_roots = get_site_transient( 'theme_roots' );
	if ( false === $theme_roots ) {
		get_themes();
		$theme_roots = get_site_transient( 'theme_roots' ); // this is set in get_theme()
	}
	return $theme_roots;
}

/**
 * Retrieve theme data.
 *
 * @since 1.5.0
 *
 * @param string $theme Theme name.
 * @return array|null Null, if theme name does not exist. Theme data, if exists.
 */
function get_theme($theme) {
	$themes = get_themes();

	if ( array_key_exists($theme, $themes) )
		return $themes[$theme];

	return null;
}

/**
 * Retrieve current theme display name.
 *
 * If the 'current_theme' option has already been set, then it will be returned
 * instead. If it is not set, then each theme will be iterated over until both
 * the current stylesheet and current template name.
 *
 * @since 1.5.0
 *
 * @return string
 */
function get_current_theme() {
	if ( $theme = get_option('current_theme') )
		return $theme;

	$themes = get_themes();
	$theme_names = array_keys($themes);
	$current_template = get_option('template');
	$current_stylesheet = get_option('stylesheet');
	$current_theme = 'WordPress Default';

	if ( $themes ) {
		foreach ( (array) $theme_names as $theme_name ) {
			if ( $themes[$theme_name]['Stylesheet'] == $current_stylesheet &&
					$themes[$theme_name]['Template'] == $current_template ) {
				$current_theme = $themes[$theme_name]['Name'];
				break;
			}
		}
	}

	update_option('current_theme', $current_theme);

	return $current_theme;
}

/**
 * Register a directory that contains themes.
 *
 * @since 2.9.0
 *
 * @param string $directory Either the full filesystem path to a theme folder or a folder within WP_CONTENT_DIR
 * @return bool
 */
function register_theme_directory( $directory) {
	global $wp_theme_directories;

	/* If this folder does not exist, return and do not register */
	if ( !file_exists( $directory ) )
			/* Try prepending as the theme directory could be relative to the content directory */
		$registered_directory = WP_CONTENT_DIR . '/' . $directory;
	else
		$registered_directory = $directory;

	/* If this folder does not exist, return and do not register */
	if ( !file_exists( $registered_directory ) )
		return false;

	$wp_theme_directories[] = $registered_directory;

	return true;
}

/**
 * Search all registered theme directories for complete and valid themes.
 *
 * @since 2.9.0
 *
 * @return array Valid themes found
 */
function search_theme_directories() {
	global $wp_theme_directories, $wp_broken_themes;
	if ( empty( $wp_theme_directories ) )
		return false;

	$theme_files = array();
	$wp_broken_themes = array();

	/* Loop the registered theme directories and extract all themes */
	foreach ( (array) $wp_theme_directories as $theme_root ) {
		$theme_loc = $theme_root;

		/* We don't want to replace all forward slashes, see Trac #4541 */
		if ( '/' != WP_CONTENT_DIR )
			$theme_loc = str_replace(WP_CONTENT_DIR, '', $theme_root);

		/* Files in the root of the current theme directory and one subdir down */
		$themes_dir = @ opendir($theme_root);

		if ( !$themes_dir )
			return false;

		while ( ($theme_dir = readdir($themes_dir)) !== false ) {
			if ( is_dir($theme_root . '/' . $theme_dir) && is_readable($theme_root . '/' . $theme_dir) ) {
				if ( $theme_dir{0} == '.' || $theme_dir == 'CVS' )
					continue;

				$stylish_dir = @opendir($theme_root . '/' . $theme_dir);
				$found_stylesheet = false;

				while ( ($theme_file = readdir($stylish_dir)) !== false ) {
					if ( $theme_file == 'style.css' ) {
						$theme_files[$theme_dir] = array( 'theme_file' => $theme_dir . '/' . $theme_file, 'theme_root' => $theme_root );
						$found_stylesheet = true;
						break;
					}
				}
				@closedir($stylish_dir);

				if ( !$found_stylesheet ) { // look for themes in that dir
					$subdir = "$theme_root/$theme_dir";
					$subdir_name = $theme_dir;
					$theme_subdirs = @opendir( $subdir );

					$found_subdir_themes = false;
					while ( ($theme_subdir = readdir($theme_subdirs)) !== false ) {
						if ( is_dir( $subdir . '/' . $theme_subdir) && is_readable($subdir . '/' . $theme_subdir) ) {
							if ( $theme_subdir{0} == '.' || $theme_subdir == 'CVS' )
								continue;

							$stylish_dir = @opendir($subdir . '/' . $theme_subdir);
							$found_stylesheet = false;

							while ( ($theme_file = readdir($stylish_dir)) !== false ) {
								if ( $theme_file == 'style.css' ) {
									$theme_files["$theme_dir/$theme_subdir"] = array( 'theme_file' => $subdir_name . '/' . $theme_subdir . '/' . $theme_file, 'theme_root' => $theme_root );
									$found_stylesheet = true;
									$found_subdir_themes = true;
									break;
								}
							}
							@closedir($stylish_dir);
						}
					}
					@closedir($theme_subdir);
					if ( !$found_subdir_themes )
						$wp_broken_themes[$theme_dir] = array('Name' => $theme_dir, 'Title' => $theme_dir, 'Description' => __('Stylesheet is missing.'));
				}
			}
		}
		if ( is_dir( $theme_dir ) )
			@closedir( $theme_dir );
	}
	return $theme_files;
}

/**
 * Retrieve path to themes directory.
 *
 * Does not have trailing slash.
 *
 * @since 1.5.0
 * @param $stylesheet_or_template The stylesheet or template name of the theme
 * @uses apply_filters() Calls 'theme_root' filter on path.
 *
 * @return string Theme path.
 */
function get_theme_root( $stylesheet_or_template = false ) {
	if ($stylesheet_or_template) {
		$theme_roots = get_theme_roots();

		if ( ! empty( $theme_roots[$stylesheet_or_template] ) )
			$theme_root = WP_CONTENT_DIR . $theme_roots[$stylesheet_or_template];
		else
			$theme_root = WP_CONTENT_DIR . '/themes';
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
 * @param $stylesheet_or_template The stylesheet or template name of the theme
 *
 * @return string Themes URI.
 */
function get_theme_root_uri( $stylesheet_or_template = false ) {
	$theme_roots = get_theme_roots();

	if ( isset( $theme_roots[$stylesheet_or_template] ) && $theme_roots[$stylesheet_or_template] )
		$theme_root_uri = content_url( $theme_roots[$stylesheet_or_template] );
	else
		$theme_root_uri = content_url( 'themes' );

	return apply_filters( 'theme_root_uri', $theme_root_uri, get_option('siteurl'), $stylesheet_or_template );
}

/**
 * Retrieve path to file without the use of extension.
 *
 * Used to quickly retrieve the path of file without including the file
 * extension. It will also check the parent template, if the file exists, with
 * the use of {@link locate_template()}. Allows for more generic file location
 * without the use of the other get_*_template() functions.
 *
 * Can be used with include() or require() to retrieve path.
 * <code>
 * if( '' != get_query_template( '404' ) )
 *     include( get_query_template( '404' ) );
 * </code>
 * or the same can be accomplished with
 * <code>
 * if( '' != get_404_template() )
 *     include( get_404_template() );
 * </code>
 *
 * @since 1.5.0
 *
 * @param string $type Filename without extension.
 * @return string Full path to file.
 */
function get_query_template($type) {
	$type = preg_replace( '|[^a-z0-9-]+|', '', $type );
	return apply_filters("{$type}_template", locate_template(array("{$type}.php")));
}

/**
 * Retrieve path of index template in current or parent template.
 *
 * @since 3.0.0
 *
 * @return string
 */
function get_index_template() {
	return get_query_template('index');
}

/**
 * Retrieve path of 404 template in current or parent template.
 *
 * @since 1.5.0
 *
 * @return string
 */
function get_404_template() {
	return get_query_template('404');
}

/**
 * Retrieve path of archive template in current or parent template.
 *
 * @since 1.5.0
 *
 * @return string
 */
function get_archive_template() {
	return get_query_template('archive');
}

/**
 * Retrieve path of author template in current or parent template.
 *
 * @since 1.5.0
 *
 * @return string
 */
function get_author_template() {
	$author_id = absint( get_query_var( 'author' ) );
	$author = get_user_by( 'id', $author_id );
	$author = $author->user_nicename;

	$templates = array();

	if ( $author )
		$templates[] = "author-{$author}.php";
	if ( $author_id )
		$templates[] = "author-{$author_id}.php";
	$templates[] = 'author.php';

	$template = locate_template( $templates );
	return apply_filters( 'author_template', $template );
}

/**
 * Retrieve path of category template in current or parent template.
 *
 * Works by first retrieving the current slug for example 'category-default.php' and then
 * trying category ID, for example 'category-1.php' and will finally fallback to category.php
 * template, if those files don't exist.
 *
 * @since 1.5.0
 * @uses apply_filters() Calls 'category_template' on file path of category template.
 *
 * @return string
 */
function get_category_template() {
	$cat_ID = absint( get_query_var('cat') );
	$category = get_category( $cat_ID );

	$templates = array();

	if ( !is_wp_error($category) )
		$templates[] = "category-{$category->slug}.php";

	$templates[] = "category-$cat_ID.php";
	$templates[] = "category.php";

	$template = locate_template($templates);
	return apply_filters('category_template', $template);
}

/**
 * Retrieve path of tag template in current or parent template.
 *
 * Works by first retrieving the current tag name, for example 'tag-wordpress.php' and then
 * trying tag ID, for example 'tag-1.php' and will finally fallback to tag.php
 * template, if those files don't exist.
 *
 * @since 2.3.0
 * @uses apply_filters() Calls 'tag_template' on file path of tag template.
 *
 * @return string
 */
function get_tag_template() {
	$tag_id = absint( get_query_var('tag_id') );
	$tag_name = get_query_var('tag');

	$templates = array();

	if ( $tag_name )
		$templates[] = "tag-$tag_name.php";
	if ( $tag_id )
		$templates[] = "tag-$tag_id.php";
	$templates[] = "tag.php";

	$template = locate_template($templates);
	return apply_filters('tag_template', $template);
}

/**
 * Retrieve path of taxonomy template in current or parent template.
 *
 * Retrieves the taxonomy and term, if term is available. The template is
 * prepended with 'taxonomy-' and followed by both the taxonomy string and
 * the taxonomy string followed by a dash and then followed by the term.
 *
 * The taxonomy and term template is checked and used first, if it exists.
 * Second, just the taxonomy template is checked, and then finally, taxonomy.php
 * template is used. If none of the files exist, then it will fall back on to
 * index.php.
 *
 * @since unknown (2.6.0 most likely)
 * @uses apply_filters() Calls 'taxonomy_template' filter on found path.
 *
 * @return string
 */
function get_taxonomy_template() {
	$taxonomy = get_query_var('taxonomy');
	$term = get_query_var('term');

	$templates = array();
	if ( $taxonomy && $term )
		$templates[] = "taxonomy-$taxonomy-$term.php";
	if ( $taxonomy )
		$templates[] = "taxonomy-$taxonomy.php";

	$templates[] = "taxonomy.php";

	$template = locate_template($templates);
	return apply_filters('taxonomy_template', $template);
}

/**
 * Retrieve path of date template in current or parent template.
 *
 * @since 1.5.0
 *
 * @return string
 */
function get_date_template() {
	return get_query_template('date');
}

/**
 * Retrieve path of home template in current or parent template.
 *
 * Attempts to locate 'home.php' first before falling back to 'index.php'.
 *
 * @since 1.5.0
 * @uses apply_filters() Calls 'home_template' on file path of home template.
 *
 * @return string
 */
function get_home_template() {
	$template = locate_template(array('home.php', 'index.php'));
	return apply_filters('home_template', $template);
}

/**
 * Retrieve path of page template in current or parent template.
 *
 * Will first look for the specifically assigned page template
 * The will search for 'page-{slug}.php' followed by 'page-id.php'
 * and finally 'page.php'
 *
 * @since 1.5.0
 *
 * @return string
 */
function get_page_template() {
	global $wp_query;

	$id = (int) $wp_query->get_queried_object_id();
	$template = get_post_meta($id, '_wp_page_template', true);
	$pagename = get_query_var('pagename');

	if ( !$pagename && $id > 0 ) {
		// If a static page is set as the front page, $pagename will not be set. Retrieve it from the queried object
		$post = $wp_query->get_queried_object();
		$pagename = $post->post_name;
	}

	if ( 'default' == $template )
		$template = '';

	$templates = array();
	if ( !empty($template) && !validate_file($template) )
		$templates[] = $template;
	if ( $pagename )
		$templates[] = "page-$pagename.php";
	if ( $id )
		$templates[] = "page-$id.php";
	$templates[] = "page.php";

	return apply_filters('page_template', locate_template($templates));
}

/**
 * Retrieve path of paged template in current or parent template.
 *
 * @since 1.5.0
 *
 * @return string
 */
function get_paged_template() {
	return get_query_template('paged');
}

/**
 * Retrieve path of search template in current or parent template.
 *
 * @since 1.5.0
 *
 * @return string
 */
function get_search_template() {
	return get_query_template('search');
}

/**
 * Retrieve path of single template in current or parent template.
 *
 * @since 1.5.0
 *
 * @return string
 */
function get_single_template() {
	global $wp_query;

	$object = $wp_query->get_queried_object();
	$templates = array('single-' . $object->post_type . '.php', 'single.php');
	return apply_filters('single_template', locate_template($templates));
}

/**
 * Retrieve path of attachment template in current or parent template.
 *
 * The attachment path first checks if the first part of the mime type exists.
 * The second check is for the second part of the mime type. The last check is
 * for both types separated by an underscore. If neither are found then the file
 * 'attachment.php' is checked and returned.
 *
 * Some examples for the 'text/plain' mime type are 'text.php', 'plain.php', and
 * finally 'text_plain.php'.
 *
 * @since 2.0.0
 *
 * @return string
 */
function get_attachment_template() {
	global $posts;
	$type = explode('/', $posts[0]->post_mime_type);
	if ( $template = get_query_template($type[0]) )
		return $template;
	elseif ( $template = get_query_template($type[1]) )
		return $template;
	elseif ( $template = get_query_template("$type[0]_$type[1]") )
		return $template;
	else
		return get_query_template('attachment');
}

/**
 * Retrieve path of comment popup template in current or parent template.
 *
 * Checks for comment popup template in current template, if it exists or in the
 * parent template. If it doesn't exist, then it retrieves the comment-popup.php
 * file from the WP_FALLBACK_THEME theme. The WP_FALLBACK_THEME theme must then exist for it to
 * work.
 *
 * @since 1.5.0
 * @uses apply_filters() Calls 'comments_popup_template' filter on path.
 *
 * @return string
 */
function get_comments_popup_template() {
	$template = locate_template(array("comments-popup.php"));
	if ('' == $template)
		$template = get_theme_root() . '/' . WP_FALLBACK_THEME . '/comments-popup.php';

	return apply_filters('comments_popup_template', $template);
}

/**
 * Retrieve the name of the highest priority template file that exists.
 *
 * Searches in the STYLESHEETPATH before TEMPLATEPATH so that themes which
 * inherit from a parent theme can just overload one file.
 *
 * @since 2.7.0
 *
 * @param array $template_names Array of template files to search for in priority order.
 * @param bool $load If true the template file will be loaded if it is found.
 * @param bool $require_once Whether to require_once or require. Default true. Has no effect if $load is false.
 * @return string The template filename if one is located.
 */
function locate_template($template_names, $load = false, $require_once = true ) {
	if ( !is_array($template_names) )
		return '';

	$located = '';
	foreach ( $template_names as $template_name ) {
		if ( file_exists(STYLESHEETPATH . '/' . $template_name)) {
			$located = STYLESHEETPATH . '/' . $template_name;
			break;
		} else if ( file_exists(TEMPLATEPATH . '/' . $template_name) ) {
			$located = TEMPLATEPATH . '/' . $template_name;
			break;
		}
	}

	if ( $load && '' != $located )
		load_template( $located, $require_once );

	return $located;
}

/**
 * Require the template file with WordPress environment.
 *
 * The globals are set up for the template file to ensure that the WordPress
 * environment is available from within the function. The query variables are
 * also available.
 *
 * @since 1.5.0
 *
 * @param string $_template_file Path to template file.
 * @param bool $require_once Whether to require_once or require. Default true.
 */
function load_template( $_template_file, $require_once = true ) {
	global $posts, $post, $wp_did_header, $wp_did_template_redirect, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;

	if ( is_array( $wp_query->query_vars ) )
		extract( $wp_query->query_vars, EXTR_SKIP );

	if ( $require_once )
		require_once( $_template_file );
	else
		require( $_template_file );
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
	add_filter( 'pre_option_mods_' . get_current_theme(), create_function( '', "return array();" ) );

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

	$link = add_query_arg( array('preview' => 1, 'template' => $_GET['template'], 'stylesheet' => @$_GET['stylesheet'] ), $matches[3] );
	if ( 0 === strpos($link, 'preview=1') )
		$link = "?$link";
	return $matches[1] . esc_attr( $link ) . $matches[4];
}

/**
 * Switches current theme to new template and stylesheet names.
 *
 * @since unknown
 * @uses do_action() Calls 'switch_theme' action on updated theme display name.
 *
 * @param string $template Template name
 * @param string $stylesheet Stylesheet name.
 */
function switch_theme($template, $stylesheet) {
	update_option('template', $template);
	update_option('stylesheet', $stylesheet);
	delete_option('current_theme');
	$theme = get_current_theme();
	do_action('switch_theme', $theme);
}

/**
 * Checks that current theme files 'index.php' and 'style.css' exists.
 *
 * Does not check the fallback theme. The fallback theme should always exist.
 * Will switch theme to the fallback theme if current theme does not validate.
 * You can use the 'validate_current_theme' filter to return FALSE to
 * disable this functionality.
 *
 * @since 1.5.0
 * @see WP_FALLBACK_THEME
 *
 * @return bool
 */
function validate_current_theme() {
	// Don't validate during an install/upgrade.
	if ( defined('WP_INSTALLING') || !apply_filters( 'validate_current_theme', true ) )
		return true;

	if ( get_template() != WP_FALLBACK_THEME && !file_exists(get_template_directory() . '/index.php') ) {
		switch_theme( WP_FALLBACK_THEME, WP_FALLBACK_THEME );
		return false;
	}

	if ( get_stylesheet() != WP_FALLBACK_THEME && !file_exists(get_template_directory() . '/style.css') ) {
		switch_theme( WP_FALLBACK_THEME, WP_FALLBACK_THEME );
		return false;
	}

	return true;
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
function get_theme_mod($name, $default = false) {
	$theme = get_current_theme();

	$mods = get_option( "mods_$theme" );

	if ( isset($mods[$name]) )
		return apply_filters( "theme_mod_$name", $mods[$name] );

	return apply_filters( "theme_mod_$name", sprintf($default, get_template_directory_uri(), get_stylesheet_directory_uri()) );
}

/**
 * Update theme modification value for the current theme.
 *
 * @since 2.1.0
 *
 * @param string $name Theme modification name.
 * @param string $value theme modification value.
 */
function set_theme_mod($name, $value) {
	$theme = get_current_theme();

	$mods = get_option("mods_$theme");

	$mods[$name] = $value;

	update_option("mods_$theme", $mods);
	wp_cache_delete("mods_$theme", 'options');
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
	$theme = get_current_theme();

	$mods = get_option("mods_$theme");

	if ( !isset($mods[$name]) )
		return;

	unset($mods[$name]);

	if ( empty($mods) )
		return remove_theme_mods();

	update_option("mods_$theme", $mods);
	wp_cache_delete("mods_$theme", 'options');
}

/**
 * Remove theme modifications option for current theme.
 *
 * @since 2.1.0
 */
function remove_theme_mods() {
	$theme = get_current_theme();

	delete_option("mods_$theme");
}

/**
 * Retrieve text color for custom header.
 *
 * @since 2.1.0
 * @uses HEADER_TEXTCOLOR
 *
 * @return string
 */
function get_header_textcolor() {
	return get_theme_mod('header_textcolor', HEADER_TEXTCOLOR);
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
 * Retrieve header image for custom header.
 *
 * @since 2.1.0
 * @uses HEADER_IMAGE
 *
 * @return string
 */
function get_header_image() {
	return get_theme_mod('header_image', HEADER_IMAGE);
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
 * Add callbacks for image header display.
 *
 * The parameter $header_callback callback will be required to display the
 * content for the 'wp_head' action. The parameter $admin_header_callback
 * callback will be added to Custom_Image_Header class and that will be added
 * to the 'admin_menu' action.
 *
 * @since 2.1.0
 * @uses Custom_Image_Header Sets up for $admin_header_callback for administration panel display.
 *
 * @param callback $header_callback Call on 'wp_head' action.
 * @param callback $admin_header_callback Call on custom header administration screen.
 * @param callback $admin_image_div_callback Output a custom header image div on the custom header administration screen. Optional.
 */
function add_custom_image_header($header_callback, $admin_header_callback, $admin_image_div_callback = '') {
	if ( ! empty($header_callback) )
		add_action('wp_head', $header_callback);

	add_theme_support( 'custom-header' );

	if ( ! is_admin() )
		return;
	require_once(ABSPATH . 'wp-admin/custom-header.php');
	$GLOBALS['custom_image_header'] =& new Custom_Image_Header($admin_header_callback, $admin_image_div_callback);
	add_action('admin_menu', array(&$GLOBALS['custom_image_header'], 'init'));
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
 * @param string|array The header string id (key of array) to remove, or an array thereof.
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
	$default = defined('BACKGROUND_IMAGE') ? BACKGROUND_IMAGE : '';

	return get_theme_mod('background_image', $default);
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
 * @uses BACKGROUND_COLOR
 *
 * @return string
 */
function get_background_color() {
	$default = defined('BACKGROUND_COLOR') ? BACKGROUND_COLOR : '';

	return get_theme_mod('background_color', $default);
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
 * Add callbacks for background image display.
 *
 * The parameter $header_callback callback will be required to display the
 * content for the 'wp_head' action. The parameter $admin_header_callback
 * callback will be added to Custom_Background class and that will be added
 * to the 'admin_menu' action.
 *
 * @since 3.0.0
 * @uses Custom_Background Sets up for $admin_header_callback for administration panel display.
 *
 * @param callback $header_callback Call on 'wp_head' action.
 * @param callback $admin_header_callback Call on custom background administration screen.
 * @param callback $admin_image_div_callback Output a custom background image div on the custom background administration screen. Optional.
 */
function add_custom_background($header_callback = '', $admin_header_callback = '', $admin_image_div_callback = '') {
	if ( isset($GLOBALS['custom_background']) )
		return;

	if ( empty($header_callback) )
		$header_callback = '_custom_background_cb';

	add_action('wp_head', $header_callback);

	add_theme_support( 'custom-background' );

	if ( ! is_admin() )
		return;
	require_once(ABSPATH . 'wp-admin/custom-background.php');
	$GLOBALS['custom_background'] =& new Custom_Background($admin_header_callback, $admin_image_div_callback);
	add_action('admin_menu', array(&$GLOBALS['custom_background'], 'init'));
}

/**
 * Default custom background callback.
 *
 * @since 3.0.0
 * @see add_custom_background()
 * @access protected
 */
function _custom_background_cb() {
	$background = get_background_image();
	$color = get_background_color();
	if ( !$background && !$color )
		return;

	switch ( get_theme_mod('background_repeat', 'repeat') ) {
		case 'no-repeat':
			$repeat = 'background-repeat: no-repeat;';
			break;
		case 'repeat-x':
			$repeat = 'background-repeat: repeat-x;';
			break;
		case 'repeat-y':
			$repeat = 'background-repeat: repeat-y;';
			break;
		default:
			$repeat = 'background-repeat: repeat;';
	}

	switch ( get_theme_mod('background_position', 'left') ) {
		case 'center':
			$position = 'background-position: top center;';
			break;
		case 'right':
			$position = 'background-position: top right;';
			break;
		default:
			$position = 'background-position: top left;';
	}

	if ( 'scroll' == get_theme_mod('background_attachment', 'fixed') )
		$attachment = 'background-attachment: scroll;';
	else
		$attachment = 'background-attachment: fixed;';

	if ( !empty($background ) )
		$image = "background-image: url('$background');";
	else
		$image = '';

	if ( !empty($color) )
		$color = "background-color: #$color;";
	else
		$color = '';
?>
<style type="text/css">
body {
	<?php echo $image; ?>
	<?php echo $color; ?>
	<?php echo $repeat; ?>
	<?php echo $position; ?>
	<?php echo $attachment; ?>
}
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
	$editor_styles = array_merge( $editor_styles, $stylesheet );
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
		$_wp_theme_features[$feature] = true;
	else
		$_wp_theme_features[$feature] = array_slice( func_get_args(), 1 );
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
	if ( in_array( $feature, array( 'custom-background', 'custom-header', 'editor-style', 'widgets' ) ) )
		return false;

	global $_wp_theme_features;

	if ( ! isset( $_wp_theme_features[$feature] ) )
		return false;
	unset( $_wp_theme_features[$feature] );
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

	if ( !isset( $_wp_theme_features[$feature] ) )
		return false;

	// If no args passed then no extra checks need be performed
	if ( func_num_args() <= 1 )
		return true;

	$args = array_slice( func_get_args(), 1 );

	// @todo Allow pluggable arg checking
	switch ( $feature ) {
		case 'post-thumbnails':
			// post-thumbnails can be registered for only certain content/post types by passing
			// an array of types to add_theme_support().  If no array was passed, then
			// any type is accepted
			if ( true === $_wp_theme_features[$feature] )  // Registered for all types
				return true;
			$content_type = $args[0];
			if ( in_array($content_type, $_wp_theme_features[$feature][0]) )
				return true;
			else
				return false;
			break;
	}

	return true;
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

?>
