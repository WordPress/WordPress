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
	$stylesheet_dir = get_theme_root() . "/$stylesheet";
	return apply_filters('stylesheet_directory', $stylesheet_dir, $stylesheet);
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
	$stylesheet_dir_uri = get_theme_root_uri() . "/$stylesheet";
	return apply_filters('stylesheet_directory_uri', $stylesheet_dir_uri, $stylesheet);
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
	$template_dir = get_theme_root() . "/$template";
	return apply_filters('template_directory', $template_dir, $template);
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
	$template_dir_uri = get_theme_root_uri() . "/$template";
	return apply_filters('template_directory_uri', $template_dir_uri, $template);
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

	$theme_data = implode( '', file( $theme_file ) );
	$theme_data = str_replace ( '\r', '\n', $theme_data );
	if ( preg_match( '|Theme Name:(.*)$|mi', $theme_data, $theme_name ) )
		$name = $theme = wp_kses( _cleanup_header_comment($theme_name[1]), $themes_allowed_tags );
	else
		$name = $theme = '';

	if ( preg_match( '|Theme URI:(.*)$|mi', $theme_data, $theme_uri ) )
		$theme_uri = esc_url( _cleanup_header_comment($theme_uri[1]) );
	else
		$theme_uri = '';

	if ( preg_match( '|Description:(.*)$|mi', $theme_data, $description ) )
		$description = wptexturize( wp_kses( _cleanup_header_comment($description[1]), $themes_allowed_tags ) );
	else
		$description = '';

	if ( preg_match( '|Author URI:(.*)$|mi', $theme_data, $author_uri ) )
		$author_uri = esc_url( _cleanup_header_comment($author_uri[1]) );
	else
		$author_uri = '';

	if ( preg_match( '|Template:(.*)$|mi', $theme_data, $template ) )
		$template = wp_kses( _cleanup_header_comment($template[1]), $themes_allowed_tags );
	else
		$template = '';

	if ( preg_match( '|Version:(.*)|i', $theme_data, $version ) )
		$version = wp_kses( _cleanup_header_comment($version[1]), $themes_allowed_tags );
	else
		$version = '';

	if ( preg_match('|Status:(.*)|i', $theme_data, $status) )
		$status = wp_kses( _cleanup_header_comment($status[1]), $themes_allowed_tags );
	else
		$status = 'publish';

	if ( preg_match('|Tags:(.*)|i', $theme_data, $tags) )
		$tags = array_map( 'trim', explode( ',', wp_kses( _cleanup_header_comment($tags[1]), array() ) ) );
	else
		$tags = array();

	if ( preg_match( '|Author:(.*)$|mi', $theme_data, $author_name ) ) {
		if ( empty( $author_uri ) ) {
			$author = wp_kses( _cleanup_header_comment($author_name[1]), $themes_allowed_tags );
		} else {
			$author = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', $author_uri, __( 'Visit author homepage' ), wp_kses( _cleanup_header_comment($author_name[1]), $themes_allowed_tags ) );
		}
	} else {
		$author = __('Anonymous');
	}

	return array( 'Name' => $name, 'Title' => $theme, 'URI' => $theme_uri, 'Description' => $description, 'Author' => $author, 'Version' => $version, 'Template' => $template, 'Status' => $status, 'Tags' => $tags );
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

	$themes = array();
	$wp_broken_themes = array();
	$theme_loc = $theme_root = get_theme_root();
	if ( '/' != WP_CONTENT_DIR ) // don't want to replace all forward slashes, see Trac #4541
		$theme_loc = str_replace(WP_CONTENT_DIR, '', $theme_root);

	// Files in wp-content/themes directory and one subdir down
	$themes_dir = @ opendir($theme_root);
	if ( !$themes_dir )
		return false;

	while ( ($theme_dir = readdir($themes_dir)) !== false ) {
		if ( is_dir($theme_root . '/' . $theme_dir) && is_readable($theme_root . '/' . $theme_dir) ) {
			if ( $theme_dir{0} == '.' || $theme_dir == '..' || $theme_dir == 'CVS' )
				continue;
			$stylish_dir = @ opendir($theme_root . '/' . $theme_dir);
			$found_stylesheet = false;
			while ( ($theme_file = readdir($stylish_dir)) !== false ) {
				if ( $theme_file == 'style.css' ) {
					$theme_files[] = $theme_dir . '/' . $theme_file;
					$found_stylesheet = true;
					break;
				}
			}
			@closedir($stylish_dir);
			if ( !$found_stylesheet ) { // look for themes in that dir
				$subdir = "$theme_root/$theme_dir";
				$subdir_name = $theme_dir;
				$theme_subdir = @ opendir( $subdir );
				while ( ($theme_dir = readdir($theme_subdir)) !== false ) {
					if ( is_dir( $subdir . '/' . $theme_dir) && is_readable($subdir . '/' . $theme_dir) ) {
						if ( $theme_dir{0} == '.' || $theme_dir == '..' || $theme_dir == 'CVS' )
							continue;
						$stylish_dir = @ opendir($subdir . '/' . $theme_dir);
						$found_stylesheet = false;
						while ( ($theme_file = readdir($stylish_dir)) !== false ) {
							if ( $theme_file == 'style.css' ) {
								$theme_files[] = $subdir_name . '/' . $theme_dir . '/' . $theme_file;
								$found_stylesheet = true;
								break;
							}
						}
						@closedir($stylish_dir);
					}
				}
				@closedir($theme_subdir);
				$wp_broken_themes[$theme_dir] = array('Name' => $theme_dir, 'Title' => $theme_dir, 'Description' => __('Stylesheet is missing.'));
			}
		}
	}
	if ( is_dir( $theme_dir ) )
		@closedir( $theme_dir );

	if ( !$themes_dir || !$theme_files )
		return $themes;

	sort($theme_files);

	foreach ( (array) $theme_files as $theme_file ) {
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

		if ( empty($template) ) {
			if ( file_exists(dirname("$theme_root/$theme_file/index.php")) )
				$template = dirname($theme_file);
			else
				continue;
		}

		$template = trim($template);

		if ( !file_exists("$theme_root/$template/index.php") ) {
			$parent_dir = dirname(dirname($theme_file));
			if ( file_exists("$theme_root/$parent_dir/$template/index.php") ) {
				$template = "$parent_dir/$template";
			} else {
				$wp_broken_themes[$name] = array('Name' => $name, 'Title' => $title, 'Description' => __('Template is missing.'));
				continue;
			}
		}

		$stylesheet_files = array();
		$template_files = array();

		$stylesheet_dir = @ dir("$theme_root/$stylesheet");
		if ( $stylesheet_dir ) {
			while ( ($file = $stylesheet_dir->read()) !== false ) {
				if ( !preg_match('|^\.+$|', $file) ) {
					if ( preg_match('|\.css$|', $file) )
						$stylesheet_files[] = "$theme_loc/$stylesheet/$file";
					elseif ( preg_match('|\.php$|', $file) )
						$template_files[] = "$theme_loc/$stylesheet/$file";
				}
			}
			@ $stylesheet_dir->close();
		}

		$template_dir = @ dir("$theme_root/$template");
		if ( $template_dir ) {
			while ( ($file = $template_dir->read()) !== false ) {
				if ( preg_match('|^\.+$|', $file) )
					continue;
				if ( preg_match('|\.php$|', $file) ) {
					$template_files[] = "$theme_loc/$template/$file";
				} elseif ( is_dir("$theme_root/$template/$file") ) {
					$template_subdir = @ dir("$theme_root/$template/$file");
					while ( ($subfile = $template_subdir->read()) !== false ) {
						if ( preg_match('|^\.+$|', $subfile) )
							continue;
						if ( preg_match('|\.php$|', $subfile) )
							$template_files[] = "$theme_loc/$template/$file/$subfile";
					}
					@ $template_subdir->close();
				}
			}
			@ $template_dir->close();
		}

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
		if ( isset($themes[$name]) ) {
			if ( ('WordPress Default' == $name || 'WordPress Classic' == $name) &&
					 ('default' == $stylesheet || 'classic' == $stylesheet) ) {
				// If another theme has claimed to be one of our default themes, move
				// them aside.
				$suffix = $themes[$name]['Stylesheet'];
				$new_name = "$name/$suffix";
				$themes[$new_name] = $themes[$name];
				$themes[$new_name]['Name'] = $new_name;
			} else {
				$name = "$name/$stylesheet";
			}
		}

		$themes[$name] = array('Name' => $name, 'Title' => $title, 'Description' => $description, 'Author' => $author, 'Version' => $version, 'Template' => $template, 'Stylesheet' => $stylesheet, 'Template Files' => $template_files, 'Stylesheet Files' => $stylesheet_files, 'Template Dir' => $template_dir, 'Stylesheet Dir' => $stylesheet_dir, 'Status' => $theme_data['Status'], 'Screenshot' => $screenshot, 'Tags' => $theme_data['Tags']);
	}

	// Resolve theme dependencies.
	$theme_names = array_keys($themes);

	foreach ( (array) $theme_names as $theme_name ) {
		$themes[$theme_name]['Parent Theme'] = '';
		if ( $themes[$theme_name]['Stylesheet'] != $themes[$theme_name]['Template'] ) {
			foreach ( (array) $theme_names as $parent_theme_name ) {
				if ( ($themes[$parent_theme_name]['Stylesheet'] == $themes[$parent_theme_name]['Template']) && ($themes[$parent_theme_name]['Template'] == $themes[$theme_name]['Template']) ) {
					$themes[$theme_name]['Parent Theme'] = $themes[$parent_theme_name]['Name'];
					break;
				}
			}
		}
	}

	$wp_themes = $themes;

	return $themes;
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
 * Retrieve path to themes directory.
 *
 * Does not have trailing slash.
 *
 * @since 1.5.0
 * @uses apply_filters() Calls 'theme_root' filter on path.
 *
 * @return string Theme path.
 */
function get_theme_root() {
	return apply_filters('theme_root', WP_CONTENT_DIR . "/themes");
}

/**
 * Retrieve URI for themes directory.
 *
 * Does not have trailing slash.
 *
 * @since 1.5.0
 *
 * @return string Themes URI.
 */
function get_theme_root_uri() {
	return apply_filters('theme_root_uri', content_url('themes'), get_option('siteurl'));
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
	return get_query_template('author');
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
 * Works by retrieving the current tag name, for example 'tag-wordpress.php' and will
 * fallback to tag.php template, if the name tag file doesn't exist.
 *
 * @since 2.3.0
 * @uses apply_filters() Calls 'tag_template' on file path of tag template.
 *
 * @return string
 */
function get_tag_template() {
	$template = locate_template(array("tag-" . get_query_var('tag') . '.php', 'tag.php'));
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
 * First attempt is to look for the file in the '_wp_page_template' page meta
 * data. The second attempt, if the first has a file and is not empty, is to
 * look for 'page.php'.
 *
 * @since 1.5.0
 *
 * @return string
 */
function get_page_template() {
	global $wp_query;

	$id = (int) $wp_query->post->ID;
	$template = get_post_meta($id, '_wp_page_template', true);

	if ( 'default' == $template )
		$template = '';

	$templates = array();
	if ( !empty($template) && !validate_file($template) )
		$templates[] = $template;

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
	return get_query_template('single');
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
 * file from the default theme. The default theme must then exist for it to
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
		$template = get_theme_root() . '/default/comments-popup.php';

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
 * @return string The template filename if one is located.
 */
function locate_template($template_names, $load = false) {
	if (!is_array($template_names))
		return '';

	$located = '';
	foreach($template_names as $template_name) {
		if ( file_exists(STYLESHEETPATH . '/' . $template_name)) {
			$located = STYLESHEETPATH . '/' . $template_name;
			break;
		} else if ( file_exists(TEMPLATEPATH . '/' . $template_name) ) {
			$located = TEMPLATEPATH . '/' . $template_name;
			break;
		}
	}

	if ($load && '' != $located)
		load_template($located);

	return $located;
}

/**
 * Require once the template file with WordPress environment.
 *
 * The globals are set up for the template file to ensure that the WordPress
 * environment is available from within the function. The query variables are
 * also available.
 *
 * @since 1.5.0
 *
 * @param string $_template_file Path to template file.
 */
function load_template($_template_file) {
	global $posts, $post, $wp_did_header, $wp_did_template_redirect, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;

	if ( is_array($wp_query->query_vars) )
		extract($wp_query->query_vars, EXTR_SKIP);

	require_once($_template_file);
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
		( false !== strpos($matches[3], '://') && 0 !== strpos($matches[3], get_option('home')) )
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
 * Does not check the 'default' theme. The 'default' theme should always exist
 * or should have another theme renamed to that template name and directory
 * path. Will switch theme to default if current theme does not validate.
 * You can use the 'validate_current_theme' filter to return FALSE to
 * disable this functionality.
 *
 * @since 1.5.0
 *
 * @return bool
 */
function validate_current_theme() {
	// Don't validate during an install/upgrade.
	if ( defined('WP_INSTALLING') || !apply_filters( 'validate_current_theme', true ) )
		return true;

	if ( get_template() != 'default' && !file_exists(get_template_directory() . '/index.php') ) {
		switch_theme('default', 'default');
		return false;
	}

	if ( get_stylesheet() != 'default' && !file_exists(get_template_directory() . '/style.css') ) {
		switch_theme('default', 'default');
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

	$mods = get_option("mods_$theme");

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
 * @param callback $admin_header_callback Call on administration panels.
 */
function add_custom_image_header($header_callback, $admin_header_callback) {
	if ( ! empty($header_callback) )
		add_action('wp_head', $header_callback);

	if ( ! is_admin() )
		return;
	require_once(ABSPATH . 'wp-admin/custom-header.php');
	$GLOBALS['custom_image_header'] =& new Custom_Image_Header($admin_header_callback);
	add_action('admin_menu', array(&$GLOBALS['custom_image_header'], 'init'));
}

?>
