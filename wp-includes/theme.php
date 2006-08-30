<?php
/*
 * Theme/template/stylesheet functions.  
 */

function get_stylesheet() {
	return apply_filters('stylesheet', get_option('stylesheet'));
}

function get_stylesheet_directory() {
	$stylesheet = get_stylesheet();
	$stylesheet_dir = get_theme_root() . "/$stylesheet";
	return apply_filters('stylesheet_directory', $stylesheet_dir, $stylesheet);
}

function get_stylesheet_directory_uri() {
	$stylesheet = rawurlencode( get_stylesheet() );
	$stylesheet_dir_uri = get_theme_root_uri() . "/$stylesheet";
	return apply_filters('stylesheet_directory_uri', $stylesheet_dir_uri, $stylesheet);
}

function get_stylesheet_uri() {
	$stylesheet_dir_uri = get_stylesheet_directory_uri();
	$stylesheet_uri = $stylesheet_dir_uri . "/style.css";
	return apply_filters('stylesheet_uri', $stylesheet_uri, $stylesheet_dir_uri);
}

function get_template() {
	return apply_filters('template', get_option('template'));
}

function get_template_directory() {
	$template = get_template();
	$template_dir = get_theme_root() . "/$template";
	return apply_filters('template_directory', $template_dir, $template);
}

function get_template_directory_uri() {
	$template = get_template();
	$template_dir_uri = get_theme_root_uri() . "/$template";
	return apply_filters('template_directory_uri', $template_dir_uri, $template);
}

function get_theme_data($theme_file) {
	$theme_data = implode('', file($theme_file));
	preg_match("|Theme Name:(.*)|i", $theme_data, $theme_name);
	preg_match("|Theme URI:(.*)|i", $theme_data, $theme_uri);
	preg_match("|Description:(.*)|i", $theme_data, $description);
	preg_match("|Author:(.*)|i", $theme_data, $author_name);
	preg_match("|Author URI:(.*)|i", $theme_data, $author_uri);
	preg_match("|Template:(.*)|i", $theme_data, $template);
	if ( preg_match("|Version:(.*)|i", $theme_data, $version) )
		$version = trim($version[1]);
	else
		$version ='';
	if ( preg_match("|Status:(.*)|i", $theme_data, $status) )
		$status = trim($status[1]);
	else
		$status = 'publish';

	$description = wptexturize(trim($description[1]));

	$name = $theme_name[1];
	$name = trim($name);
	$theme = $name;

	if ( '' == $author_uri[1] ) {
		$author = trim($author_name[1]);
	} else {
		$author = '<a href="' . trim($author_uri[1]) . '" title="' . __('Visit author homepage') . '">' . trim($author_name[1]) . '</a>';
	}

	return array('Name' => $name, 'Title' => $theme, 'Description' => $description, 'Author' => $author, 'Version' => $version, 'Template' => $template[1], 'Status' => $status);
}

function get_themes() {
	global $wp_themes;
	global $wp_broken_themes;

	if ( isset($wp_themes) )
		return $wp_themes;

	$themes = array();
	$wp_broken_themes = array();
	$theme_root = get_theme_root();
	$theme_loc = str_replace(ABSPATH, '', $theme_root);

	// Files in wp-content/themes directory
	$themes_dir = @ dir($theme_root);
	if ( $themes_dir ) {
		while(($theme_dir = $themes_dir->read()) !== false) {
			if ( is_dir($theme_root . '/' . $theme_dir) && is_readable($theme_root . '/' . $theme_dir) ) {
				if ( $theme_dir{0} == '.' || $theme_dir == '..' || $theme_dir == 'CVS' ) {
					continue;
				}
				$stylish_dir = @ dir($theme_root . '/' . $theme_dir);
				$found_stylesheet = false;
				while (($theme_file = $stylish_dir->read()) !== false) {
					if ( $theme_file == 'style.css' ) {
						$theme_files[] = $theme_dir . '/' . $theme_file;
						$found_stylesheet = true;
						break;
					}
				}
				if ( !$found_stylesheet ) {
					$wp_broken_themes[$theme_dir] = array('Name' => $theme_dir, 'Title' => $theme_dir, 'Description' => __('Stylesheet is missing.'));
				}
			}
		}
	}

	if ( !$themes_dir || !$theme_files ) {
		return $themes;
	}

	sort($theme_files);

	foreach($theme_files as $theme_file) {
		if ( ! is_readable("$theme_root/$theme_file") ) {
			$wp_broken_themes[$theme_file] = array('Name' => $theme_file, 'Title' => $theme_file, 'Description' => __('File not readable.'));
			continue;
		}

		$theme_data = get_theme_data("$theme_root/$theme_file");

		$name = $theme_data['Name'];
		$title = $theme_data['Title'];
		$description = wptexturize($theme_data['Description']);
		$version = $theme_data['Version'];
		$author = $theme_data['Author'];
		$template = $theme_data['Template'];
		$stylesheet = dirname($theme_file);

		foreach (array('png', 'gif', 'jpg', 'jpeg') as $ext) {
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
			if ( file_exists(dirname("$theme_root/$theme_file/index.php")) ) {
				$template = dirname($theme_file);
			} else {
				continue;
			}
		}

		$template = trim($template);

		if ( !file_exists("$theme_root/$template/index.php") ) {
			$wp_broken_themes[$name] = array('Name' => $name, 'Title' => $title, 'Description' => __('Template is missing.'));
			continue;
		}

		$stylesheet_files = array();
		$stylesheet_dir = @ dir("$theme_root/$stylesheet");
		if ( $stylesheet_dir ) {
			while(($file = $stylesheet_dir->read()) !== false) {
				if ( !preg_match('|^\.+$|', $file) && preg_match('|\.css$|', $file) )
					$stylesheet_files[] = "$theme_loc/$stylesheet/$file";
			}
		}

		$template_files = array();
		$template_dir = @ dir("$theme_root/$template");
		if ( $template_dir ) {
			while(($file = $template_dir->read()) !== false) {
				if ( !preg_match('|^\.+$|', $file) && preg_match('|\.php$|', $file) )
					$template_files[] = "$theme_loc/$template/$file";
			}
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

		$themes[$name] = array('Name' => $name, 'Title' => $title, 'Description' => $description, 'Author' => $author, 'Version' => $version, 'Template' => $template, 'Stylesheet' => $stylesheet, 'Template Files' => $template_files, 'Stylesheet Files' => $stylesheet_files, 'Template Dir' => $template_dir, 'Stylesheet Dir' => $stylesheet_dir, 'Status' => $theme_data['Status'], 'Screenshot' => $screenshot);
	}

	// Resolve theme dependencies.
	$theme_names = array_keys($themes);

	foreach ($theme_names as $theme_name) {
		$themes[$theme_name]['Parent Theme'] = '';
		if ( $themes[$theme_name]['Stylesheet'] != $themes[$theme_name]['Template'] ) {
			foreach ($theme_names as $parent_theme_name) {
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

function get_theme($theme) {
	$themes = get_themes();

	if ( array_key_exists($theme, $themes) )
		return $themes[$theme];

	return NULL;
}

function get_current_theme() {
	$themes = get_themes();
	$theme_names = array_keys($themes);
	$current_template = get_option('template');
	$current_stylesheet = get_option('stylesheet');
	$current_theme = 'WordPress Default';

	if ( $themes ) {
		foreach ($theme_names as $theme_name) {
			if ( $themes[$theme_name]['Stylesheet'] == $current_stylesheet &&
					$themes[$theme_name]['Template'] == $current_template ) {
				$current_theme = $themes[$theme_name]['Name'];
				break;
			}
		}
	}

	return $current_theme;
}

function get_theme_root() {
	return apply_filters('theme_root', ABSPATH . "wp-content/themes");
}

function get_theme_root_uri() {
	return apply_filters('theme_root_uri', get_option('siteurl') . "/wp-content/themes", get_option('siteurl'));
}

function get_query_template($type) {
	$template = '';
	if ( file_exists(TEMPLATEPATH . "/{$type}.php") )
		$template = TEMPLATEPATH . "/{$type}.php";

	return apply_filters("{$type}_template", $template);
}

function get_404_template() {
	return get_query_template('404');
}

function get_archive_template() {
	return get_query_template('archive');
}

function get_author_template() {
	return get_query_template('author');
}

function get_category_template() {
	$template = '';
	if ( file_exists(TEMPLATEPATH . "/category-" . get_query_var('cat') . '.php') )
		$template = TEMPLATEPATH . "/category-" . get_query_var('cat') . '.php';
	else if ( file_exists(TEMPLATEPATH . "/category.php") )
		$template = TEMPLATEPATH . "/category.php";

	return apply_filters('category_template', $template);
}

function get_date_template() {
	return get_query_template('date');
}

function get_home_template() {
	$template = '';

	if ( file_exists(TEMPLATEPATH . "/home.php") )
		$template = TEMPLATEPATH . "/home.php";
	else if ( file_exists(TEMPLATEPATH . "/index.php") )
		$template = TEMPLATEPATH . "/index.php";

	return apply_filters('home_template', $template);
}

function get_page_template() {
	global $wp_query;

	$id = $wp_query->post->ID;
	$template = get_post_meta($id, '_wp_page_template', true);

	if ( 'default' == $template )
		$template = '';

	if ( ! empty($template) && file_exists(TEMPLATEPATH . "/$template") )
		$template = TEMPLATEPATH . "/$template";
	else if ( file_exists(TEMPLATEPATH . "/page.php") )
		$template = TEMPLATEPATH . "/page.php";
	else
		$template = '';

	return apply_filters('page_template', $template);
}

function get_paged_template() {
	return get_query_template('paged');
}

function get_search_template() {
	return get_query_template('search');
}

function get_single_template() {
	return get_query_template('single');
}

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

function get_comments_popup_template() {
	if ( file_exists( TEMPLATEPATH . '/comments-popup.php') )
		$template = TEMPLATEPATH . '/comments-popup.php';
	else
		$template = get_theme_root() . '/default/comments-popup.php';

	return apply_filters('comments_popup_template', $template);
}

function load_template($file) {
	global $posts, $post, $wp_did_header, $wp_did_template_redirect, $wp_query,
		$wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment;

	extract($wp_query->query_vars);

	require_once($file);
}

function validate_current_theme() {
	// Don't validate during an install/upgrade.
	if ( defined('WP_INSTALLING') )
		return true;

	if ((get_template() != 'default') && (!file_exists(get_template_directory() . '/index.php'))) {
		update_option('template', 'default');
		update_option('stylesheet', 'default');
		do_action('switch_theme', 'Default');
		return false;
	}

	if ((get_stylesheet() != 'default') && (!file_exists(get_template_directory() . '/style.css'))) {
		update_option('template', 'default');
		update_option('stylesheet', 'default');
		do_action('switch_theme', 'Default');
		return false;
	}

	return true;
}

?>
