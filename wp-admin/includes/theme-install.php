<?php
/**
 * WordPress Theme Install Administration API
 *
 * @package WordPress
 * @subpackage Administration
 */

$themes_allowedtags = array('a' => array('href' => array(), 'title' => array(), 'target' => array()),
	'abbr' => array('title' => array()), 'acronym' => array('title' => array()),
	'code' => array(), 'pre' => array(), 'em' => array(), 'strong' => array(),
	'div' => array(), 'p' => array(), 'ul' => array(), 'ol' => array(), 'li' => array(),
	'h1' => array(), 'h2' => array(), 'h3' => array(), 'h4' => array(), 'h5' => array(), 'h6' => array(),
	'img' => array('src' => array(), 'class' => array(), 'alt' => array())
);

$theme_field_defaults = array( 'description' => true, 'sections' => false, 'tested' => true, 'requires' => true,
	'rating' => true, 'downloaded' => true, 'downloadlink' => true, 'last_updated' => true, 'homepage' => true,
	'tags' => true, 'num_ratings' => true
);


/**
 * Retrieve theme installer pages from WordPress Themes API.
 *
 * It is possible for a theme to override the Themes API result with three
 * filters. Assume this is for themes, which can extend on the Theme Info to
 * offer more choices. This is very powerful and must be used with care, when
 * overridding the filters.
 *
 * The first filter, 'themes_api_args', is for the args and gives the action as
 * the second parameter. The hook for 'themes_api_args' must ensure that an
 * object is returned.
 *
 * The second filter, 'themes_api', is the result that would be returned.
 *
 * @since 2.8.0
 *
 * @param string $action
 * @param array|object $args Optional. Arguments to serialize for the Theme Info API.
 * @return mixed
 */
function themes_api($action, $args = null) {

	if ( is_array($args) )
		$args = (object)$args;

	if ( !isset($args->per_page) )
		$args->per_page = 24;

	$args = apply_filters('themes_api_args', $args, $action); //NOTE: Ensure that an object is returned via this filter.
	$res = apply_filters('themes_api', false, $action, $args); //NOTE: Allows a theme to completely override the builtin WordPress.org API.

	if ( ! $res ) {
		$request = wp_remote_post('http://api.wordpress.org/themes/info/1.0/', array( 'body' => array('action' => $action, 'request' => serialize($args))) );
		if ( is_wp_error($request) ) {
			$res = new WP_Error('themes_api_failed', __('An Unexpected HTTP Error occured during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $request->get_error_message() );
		} else {
			$res = unserialize($request['body']);
			if ( ! $res )
			$res = new WP_Error('themes_api_failed', __('An unknown error occured'), $request['body']);
		}
	}
	//var_dump(array($args, $res));
	return apply_filters('themes_api_result', $res, $action, $args);
}

/**
 * Retrieve popular WordPress theme tags.
 *
 * @since 2.8.0
 *
 * @param array $args
 * @return array
 */
function install_themes_popular_tags( $args = array() ) {
	global $theme_field_defaults;
	if ( !$cache = get_option('wporg_theme_popular_tags') )
		add_option('wporg_theme_popular_tags', array(), '', 'no'); ///No autoload.

	if ( $cache && $cache->timeout + 3 * 60 * 60 > time() )
		return $cache->cached;

	$args['fields'] = $theme_field_defaults;

	$tags = themes_api('hot_tags', $args);

	if ( is_wp_error($tags) )
		return $tags;

	$cache = (object) array('timeout' => time(), 'cached' => $tags);

	update_option('wporg_theme_popular_tags', $cache);

	return $tags;
}

add_action('install_themes_search', 'install_theme_search', 10, 1);
/**
 * Display theme search results and display as tag cloud.
 *
 * @since 2.8.0
 *
 * @param string $page
 */
function install_theme_search($page) {
	$type = isset($_REQUEST['type']) ? stripslashes( $_REQUEST['type'] ) : '';
	$term = isset($_REQUEST['s']) ? stripslashes( $_REQUEST['s'] ) : '';

	$args = array();

	switch( $type ){
		case 'tag':
			$terms = explode(',', $term);
			$terms = array_map('trim', $terms);
			$terms = array_map('sanitize_title_with_dashes', $terms);
			$args['tag'] = $terms;
			break;
		case 'term':
			$args['search'] = $term;
			break;
		case 'author':
			$args['author'] = $term;
			break;
	}

	$args['page'] = $page;
	$args['fields'] = $theme_field_defaults;

	$api = themes_api('query_themes', $args);

	if ( is_wp_error($api) )
		wp_die($api);

	add_action('install_themes_table_header', 'install_theme_search_form');

	display_themes($api->themes, $api->info['page'], $api->info['pages']);
}

add_action('install_themes_dashboard', 'install_themes_dashboard');
function install_themes_dashboard() {
	?>
<p><?php _e('Search for themes by keyword, author, or tag.') ?></p>

	<?php install_theme_search_form(); ?>

<h4><?php _e('Advanced Search') ?></h4>
<p><?php _e('Tag filter goes here') ?></p>

<h4><?php _e('Popular tags') ?></h4>
<p><?php _e('You may also browse based on the most popular tags in the Theme Directory:') ?></p>
	<?php

	$api_tags = install_themes_popular_tags();

	//Set up the tags in a way which can be interprated by wp_generate_tag_cloud()
	$tags = array();
	foreach ( (array)$api_tags as $tag ) {
		$tags[ $tag['name'] ] = (object) array(
								'link' => clean_url( admin_url('theme-install.php?tab=search&type=tag&s=' . urlencode($tag['name'])) ),
								'name' => $tag['name'],
								'id' => sanitize_title_with_dashes($tag['name']),
								'count' => $tag['count'] );
	}
	echo '<p class="popular-tags">';
	echo wp_generate_tag_cloud($tags, array( 'single_text' => __('%d theme'), 'multiple_text' => __('%d themes') ) );
	echo '</p><br class="clear" />';
}

/**
 * Display search form for searching themes.
 *
 * @since 2.8.0
 */
function install_theme_search_form(){
	$type = isset($_REQUEST['type']) ? stripslashes( $_REQUEST['type'] ) : '';
	$term = isset($_REQUEST['s']) ? stripslashes( $_REQUEST['s'] ) : '';

	?>
<form id="search-themes" method="post"
	action="<?php echo admin_url('theme-install.php?tab=search') ?>"><select
	name="type" id="typeselector">
	<option value="term" <?php selected('term', $type) ?>><?php _e('Term') ?></option>
	<option value="author" <?php selected('author', $type) ?>><?php _e('Author') ?></option>
	<option value="tag" <?php selected('tag', $type) ?>><?php _e('Tag') ?></option>
</select> <input type="text" name="s" id="search-field"
	value="<?php echo attribute_escape($term) ?>" /> <input type="submit"
	name="search" value="<?php echo attribute_escape(__('Search')) ?>"
	class="button" /></form>
	<?php
}

add_action('install_themes_featured', 'install_themes_featured', 10, 1);
/**
 * Display featured themes.
 *
 * @since 2.8.0
 *
 * @param string $page
 */
function install_themes_featured($page = 1) {
	global $theme_field_defaults;
	$args = array('browse' => 'featured', 'page' => $page, 'fields' => $theme_field_defaults);
	$api = themes_api('query_themes', $args);
	if ( is_wp_error($api) )
		wp_die($api);
	display_themes($api->themes, $api->info['page'], $api->info['pages']);
}

add_action('install_thems_popular', 'install_themes_popular', 10, 1);
/**
 * Display popular themes.
 *
 * @since 2.8.0
 *
 * @param string $page
 */
function install_themes_popular($page = 1) {
	global $theme_field_defaults;
	$args = array('browse' => 'popular', 'page' => $page, 'fields' => $theme_field_defaults);
	$api = themes_api('query_themes', $args);
	display_themes($api->themes, $api->info['page'], $api->info['pages']);
}

add_action('install_themes_new', 'install_themes_new', 10, 1);
/**
 * Display new themes/
 *
 * @since 2.8.0
 *
 * @param string $page
 */
function install_themes_new($page = 1) {
	global $theme_field_defaults;
	$args = array('browse' => 'new', 'page' => $page, 'fields' => $theme_field_defaults);
	$api = themes_api('query_themes', $args);
	if ( is_wp_error($api) )
		wp_die($api);
	display_themes($api->themes, $api->info['page'], $api->info['pages']);
}

add_action('install_themes_updated', 'install_themes_updated', 10, 1);
/**
 * Display recently updated themes.
 *
 * @since 2.8.0
 *
 * @param string $page
 */
function install_themes_updated($page = 1) {
	global $theme_field_defaults;
	$args = array('browse' => 'updated', 'page' => $page, 'fields' => $theme_field_defaults);
	$api = themes_api('query_themes', $args);
	display_themes($api->themes, $api->info['page'], $api->info['pages']);
}

add_action('install_themes_upload', 'install_themes_upload', 10, 1);
function install_themes_upload($page = 1) {
	?>
<h4><?php _e('Install a theme in .zip format') ?></h4>
<p><?php _e('If you have a theme in a .zip format, you may install it by uploading it here.') ?></p>
<form method="post" enctype="multipart/form-data"
	action="<?php echo admin_url('theme-install.php?tab=do_upload') ?>"><?php wp_nonce_field( 'theme-upload') ?>
<input type="file" name="themezip" /> <input type="submit"
	class="button" value="<?php _e('Install Now') ?>" /></form>
	<?php
}

function display_theme($theme, $actions = null, $show_details = true) {
	global $themes_allowedtags;

	if ( empty($theme) )
		return;
	//var_dump($theme);

	$name = wp_kses($theme->name, $themes_allowedtags);
	$desc = wp_kses($theme->description, $themes_allowedtags);
	//if ( strlen($desc) > 30 )
	//	$desc =  substr($desc, 0, 30) . '<span class="dots">...</span><span>' . substr($desc, 30) . '</span>';

	$preview_link = $theme->preview_url . '?TB_iframe=true&amp;width=600&amp;height=400';
	if ( empty($actions) ) {
		$actions = array();
		$actions[] = '<a href="' . admin_url('theme-install.php?tab=theme-information&amp;theme=' . $theme->slug .
										'&amp;TB_iframe=true&amp;width=600&amp;height=800') . '" class="thickbox onclick" title="' . attribute_escape(sprintf(__('Install "%s"'), $name)) . '">' . __('Install') . '</a>';
		$actions[] = '<a href="' . $preview_link . '" class="thickbox thickbox-preview onclick previewlink" title="' . attribute_escape(sprintf(__('Preview "%s"'), $name)) . '">' . __('Preview') . '</a>';
		$actions = apply_filters('theme_install_action_links', $actions, $theme);
	}

	$actions = implode ( ' | ', $actions );
	?>
<a class='thickbox thickbox-preview screenshot'
	href='<? echo clean_url($preview_link) ?>'
	title='<?php attribute_escape(sprintf(__('Preview "%s"'), $name)) ?>'>
<img src='<?php echo clean_url($theme->screenshot_url) ?>' width='150' />
</a>
<h3><?php echo $name ?></h3>
<span class='action-links'><?php echo $actions ?></span>
<p><?php echo $desc ?></p>
<?php if ( $show_details ) { ?>
<a href="#theme_detail" class="theme-detail hide-if-no-js" tabindex='4'><?php _e('Details') ?></a>
<div class="themedetaildiv hide-if-js">
<p><strong><?php _e('Version:') ?></strong> <?php echo wp_kses($theme->version, $themes_allowedtags) ?></p>
<p><strong><?php _e('Author:') ?></strong> <?php echo wp_kses($theme->author, $themes_allowedtags) ?></p>
<?php if ( ! empty($theme->last_updated) ) : ?>
<p><strong><?php _e('Last Updated:') ?></strong> <span title="<?php echo $theme->last_updated ?>"><?php printf( __('%s ago'), human_time_diff(strtotime($theme->last_updated)) ) ?></span></p>
<?php endif; if ( ! empty($theme->requires) ) : ?>
<p><strong><?php _e('Requires WordPress Version:') ?></strong> <?php printf(__('%s or higher'), $theme->requires) ?></p>
<?php endif; if ( ! empty($theme->tested) ) : ?>
<p><strong><?php _e('Compatible up to:') ?></strong> <?php echo $theme->tested ?></p>
<?php endif; if ( !empty($theme->downloaded) ) : ?>
<p><strong><?php _e('Downloaded:') ?></strong> <?php printf(_n('%s time', '%s times', $theme->downloaded), number_format_i18n($theme->downloaded)) ?></p>
<?php endif; ?>
<div class="star-holder" title="<?php printf(_n('(based on %s rating)', '(based on %s ratings)', $theme->num_ratings), number_format_i18n($theme->num_ratings)) ?>">
	<div class="star star-rating" style="width: <?php echo attribute_escape($theme->rating) ?>px"></div>
	<div class="star star5"><img src="<?php echo admin_url('images/star.gif'); ?>" alt="<?php _e('5 stars') ?>" /></div>
	<div class="star star4"><img src="<?php echo admin_url('images/star.gif'); ?>" alt="<?php _e('4 stars') ?>" /></div>
	<div class="star star3"><img src="<?php echo admin_url('images/star.gif'); ?>" alt="<?php _e('3 stars') ?>" /></div>
	<div class="star star2"><img src="<?php echo admin_url('images/star.gif'); ?>" alt="<?php _e('2 stars') ?>" /></div>
	<div class="star star1"><img src="<?php echo admin_url('images/star.gif'); ?>" alt="<?php _e('1 star') ?>" /></div>
</div>
</div>
<?php }
	/*
	 object(stdClass)[59]
	 public 'name' => string 'Magazine Basic' (length=14)
	 public 'slug' => string 'magazine-basic' (length=14)
	 public 'version' => string '1.1' (length=3)
	 public 'author' => string 'tinkerpriest' (length=12)
	 public 'preview_url' => string 'http://wp-themes.com/?magazine-basic' (length=36)
	 public 'screenshot_url' => string 'http://wp-themes.com/wp-content/themes/magazine-basic/screenshot.png' (length=68)
	 public 'rating' => float 80
	 public 'num_ratings' => int 1
	 public 'homepage' => string 'http://wordpress.org/extend/themes/magazine-basic' (length=49)
	 public 'description' => string 'A basic magazine style layout with a fully customizable layout through a backend interface. Designed by <a href="http://bavotasan.com">c.bavota</a> of <a href="http://tinkerpriestmedia.com">Tinker Priest Media</a>.' (length=214)
	 public 'download_link' => string 'http://wordpress.org/extend/themes/download/magazine-basic.1.1.zip' (length=66)
	 */
}

/**
 * Display theme content based on theme list.
 *
 * @since 2.8.0
 *
 * @param array $themes List of themes.
 * @param string $page
 * @param int $totalpages Number of pages.
 */
function display_themes($themes, $page = 1, $totalpages = 1) {
	global $themes_allowedtags;

	$type = isset($_REQUEST['type']) ? stripslashes( $_REQUEST['type'] ) : '';
	$term = isset($_REQUEST['s']) ? stripslashes( $_REQUEST['s'] ) : '';
	?>
<div class="tablenav">
<div class="alignleft actions"><?php do_action('install_themes_table_header'); ?>
</div>
	<?php
	$url = clean_url($_SERVER['REQUEST_URI']);
	if ( ! empty($term) )
		$url = add_query_arg('s', $term, $url);
	if ( ! empty($type) )
		$url = add_query_arg('type', $type, $url);

	$page_links = paginate_links( array(
			'base' => add_query_arg('paged', '%#%', $url),
			'format' => '',
			'prev_text' => __('&laquo;'),
			'next_text' => __('&raquo;'),
			'total' => $totalpages,
			'current' => $page
	));

	if ( $page_links )
		echo "\t\t<div class='tablenav-pages'>$page_links</div>";
	?> <br class="clear" />
</div>
<?php
	if ( empty($themes) ) {
		_e('No themes found');
		return;
	}
?>
<table id="availablethemes" cellspacing="0" cellpadding="0">
<?php
	$in_column = 0;
	$rows = ceil(count($themes) / 3);
	$table = array();
	$i = 0;
	for ( $row = 1; $row <= $rows; $row++ ) {
		for ( $col = 1; $col <= 3; $col++ ) {
			$table[$row][$col] = $i;
			$i++;
		}
	}

	foreach ( $table as $row => $cols ) {
	?>
	<tr>
	<?php
	foreach ( $cols as $col => $theme_index ) {
		$class = array('available-theme');
		if ( $row == 1 ) $class[] = 'top';
		if ( $col == 1 ) $class[] = 'left';
		if ( $row == $rows ) $class[] = 'bottom';
		if ( $col == 3 ) $class[] = 'right';
		$theme = $themes[$theme_index];
		?>
		<td class="<?php echo join(' ', $class); ?>"><?php
			display_theme($theme);
		?></td>
		<?php } // end foreach $cols ?>
	</tr>
	<?php } // end foreach $table ?>
</table>

<div class="tablenav"><?php if ( $page_links )
echo "\t\t<div class='tablenav-pages'>$page_links</div>"; ?> <br
	class="clear" />
</div>

<?php
}

add_action('install_themes_pre_theme-information', 'install_theme_information');

/**
 * Display theme information in dialog box form.
 *
 * @since 2.8.0
 */
function install_theme_information() {
	//TODO: This function needs a LOT of UI work :)
	global $tab, $themes_allowedtags;;

	$api = themes_api('theme_information', array('slug' => stripslashes( $_REQUEST['theme'] ) ));

	if ( is_wp_error($api) )
		wp_die($api);

	// Sanitize HTML
	foreach ( (array)$api->sections as $section_name => $content )
		$api->sections[$section_name] = wp_kses($content, $themes_allowedtags);
	foreach ( array('version', 'author', 'requires', 'tested', 'homepage', 'downloaded', 'slug') as $key )
		$api->$key = wp_kses($api->$key, $themes_allowedtags);

	$section = isset($_REQUEST['section']) ? stripslashes( $_REQUEST['section'] ) : 'description'; //Default to the Description tab, Do not translate, API returns English.
	if ( empty($section) || ! isset($api->sections[ $section ]) )
		$section = array_shift( $section_titles = array_keys((array)$api->sections) );

	iframe_header( __('Theme Install') );

	if ( version_compare($GLOBALS['wp_version'], $api->tested, '>') )
		echo '<div class="updated"><p>' . __('<strong>Warning:</strong> This theme has <strong>not been tested</strong> with your current version of WordPress.') . '</p></div>';
	else if ( version_compare($GLOBALS['wp_version'], $api->requires, '<') )
		echo '<div class="updated"><p>' . __('<strong>Warning:</strong> This theme has not been marked as <strong>compatible</strong> with your version of WordPress.') . '</p></div>';

	if ( empty($api->download_link) ) {
		// No go
	}
	?>

<p class="action-button"><?php
	// Default to a "new" theme
	$type = 'install';
	// Check to see if this theme is known to be installed, and has an update awaiting it.
	$update_themes = get_option('update_themes');
	foreach ( (array)$update_themes->response as $file => $theme ) {
		if ( $theme->slug === $api->slug ) {
			$type = 'update_available';
		$update_file = $file;
		break;
	}
}

$action = '';
switch ( $type ) {
	default:
	case 'install':
		if ( current_user_can('install_themes') ) :
			$action = '<a class="button" href="' . wp_nonce_url(admin_url('theme-install.php?tab=install&theme=' . $api->slug), 'install-theme_' . $api->slug) . '" target="_parent">' . __('Install Now') . '</a>';
	endif;
	break;
case 'update_available':
	if ( current_user_can('update_themes') ) :
	?><a class="button"
	href="<?php echo wp_nonce_url(admin_url('update.php?action=upgrade-theme&theme=' . $update_file), 'upgrade-theme_' . $update_file) ?>"
	target="_parent"><?php _e('Install Update Now') ?></a><?php
	endif;
	break;
case 'newer_installed':
	if ( current_user_can('install_themes') || current_user_can('update_themes') ) :
	?><a><?php printf(__('Newer Version (%s) Installed'), $newer_version) ?></a><?php
	endif;
	break;
case 'latest_installed':
	if ( current_user_can('install_themes') || current_user_can('update_themes') ) :
	?><a><?php _e('Latest Version Installed') ?></a><?php
	endif;
	break;
} ?></p>
<div class='available-theme'>
<?php
display_theme($api, array($action), false);
?>
</div>
	<?php
	iframe_footer();
	exit;
}

add_action('install_themes_do_upload', 'upload_theme');
function upload_theme() {

	if ( ! ( ( $uploads = wp_upload_dir() ) && false === $uploads['error'] ) )
		wp_die($uploads['error']);

	if ( !empty($_FILES) )
		$filename = $_FILES['themezip']['name'];
	else if ( isset($_GET['package']) )
		$filename = $_GET['package'];

	check_admin_referer('theme-upload');

	echo '<div class="wrap">';
	echo '<h2>', sprintf( __('Installing theme from file: %s'), basename($filename) ), '</h2>';

	//Handle a newly uploaded file, Else assume it was
	if ( !empty($_FILES) ) {
		$filename = wp_unique_filename( $uploads['basedir'], $filename );
		$local_file = $uploads['basedir'] . '/' . $filename;

		// Move the file to the uploads dir
		if ( false === @ move_uploaded_file( $_FILES['themezip']['tmp_name'], $local_file) )
			wp_die( sprintf( __('The uploaded file could not be moved to %s.' ), $uploads['path']));
	} else {
		$local_file = $uploads['basedir'] . '/' . $filename;
	}

	do_theme_install_local_package($local_file, $filename);
	echo '</div>';
}

add_action('install_themes_install', 'install_theme');

/**
 * Display theme link and execute install.
 *
 * @since 2.8.0
 */
function install_theme() {

	$theme = isset($_REQUEST['theme']) ? stripslashes( $_REQUEST['theme'] ) : '';

	check_admin_referer('install-theme_' . $theme);
	$api = themes_api('theme_information', array('slug' => $theme, 'fields' => array('sections' => false) ) ); //Save on a bit of bandwidth.

	if ( is_wp_error($api) )
		wp_die($api);

	echo '<div class="wrap">';
	echo '<h2>', sprintf( __('Installing theme: %s'), $api->name . ' ' . $api->version ), '</h2>';

	do_theme_install($api->download_link, $api);
	echo '</div>';

}

/**
 * Retrieve theme and install.
 *
 * @since 2.8.0
 *
 * @param string $download_url Download URL.
 * @param object $theme_information Optional. Theme information
 */
function do_theme_install($download_url, $theme_information = null) {
	global $wp_filesystem;

	if ( empty($download_url) ) {
		show_message( __('No theme specified') );
		return;
	}

	$theme = isset($_REQUEST['theme']) ? stripslashes( $_REQUEST['theme'] ) : '';

	$url = 'theme-install.php?tab=install';
	$url = add_query_arg(array('theme' => $theme, 'theme_name' => stripslashes( $_REQUEST['theme_name'] ), 'download_url' => stripslashes( $_REQUEST['download_url'] ) ), $url);

	$url = wp_nonce_url($url, 'install-theme_' . $theme);
	if ( false === ($credentials = request_filesystem_credentials($url)) )
		return;

	if ( ! WP_Filesystem($credentials) ) {
		request_filesystem_credentials($url, '', true); //Failed to connect, Error and request again
		return;
	}

	if ( $wp_filesystem->errors->get_error_code() ) {
		foreach ( $wp_filesystem->errors->get_error_messages() as $message )
		show_message($message);
		return;
	}

	$result = wp_install_theme( $download_url, 'show_message' );

	if ( is_wp_error($result) ) {
		show_message($result);
		show_message( __('Installation Failed') );
	} else {
		show_message( sprintf(__('Successfully installed the theme <strong>%s %s</strong>.'), $theme_information->name, $theme_information->version) );
		$theme_file = $result;

		$install_actions = apply_filters('install_theme_complete_actions', array(
		//'activate_theme' => '<a href="' . wp_nonce_url('themes.php?action=activate&amp;theme=' . $theme_file, 'activate-theme_' . $theme_file) . '" title="' . attribute_escape(__('Activate this theme')) . '" target="_parent">' . __('Activate Theme') . '</a>',
			'themes_page' => '<a href="' . admin_url('themes.php') . '" title="' . attribute_escape(__('Return to Themes page')) . '" target="_parent">' . __('Return to Themes page') . '</a>'
			), $theme_information, $theme_file);
			if ( ! empty($install_actions) )
			show_message('<strong>' . __('Actions:') . '</strong> ' . implode(' | ', (array)$install_actions));
	}
}

/**
 * Install a theme from a local file.
 *
 * @since 2.8.0
 *
 * @param string $package Local Theme zip
 * @param string $filename Optional. Original filename
 * @param object $theme_information Optional. Theme information
 */
function do_theme_install_local_package($package, $filename = '') {
	global $wp_filesystem;

	if ( empty($package) ) {
		show_message( __('No theme specified') );
		return;
	}

	if ( empty($filename) )
		$filename = basename($package);

	$url = 'theme-install.php?tab=upload';
	$url = add_query_arg(array('package' => $filename), $url);

	$url = wp_nonce_url($url, 'theme-upload');
	if ( false === ($credentials = request_filesystem_credentials($url)) )
		return;

	if ( ! WP_Filesystem($credentials) ) {
		request_filesystem_credentials($url, '', true); //Failed to connect, Error and request again
		return;
	}

	if ( $wp_filesystem->errors->get_error_code() ) {
		foreach ( $wp_filesystem->errors->get_error_messages() as $message )
		show_message($message);
		return;
	}

	$result = wp_install_theme_local_package( $package, 'show_message' );

	if ( is_wp_error($result) ) {
		show_message($result);
		show_message( __('Installation Failed') );
	} else {
		show_message( __('Successfully installed the theme.') );
		$theme_file = $result;

		$install_actions = apply_filters('install_theme_complete_actions', array(
		//'activate_theme' => '<a href="' . wp_nonce_url('themes.php?action=activate&amp;theme=' . $theme_file, 'activate-theme_' . $theme_file) . '" title="' . __('Activate this theme') . '" target="_parent">' . __('Activate Theme') . '</a>',
							'themes_page' => '<a href="' . admin_url('themes.php') . '" title="' . __('Goto themes page') . '" target="_parent">' . __('Return to Themes page') . '</a>'
							), array(), $theme_file);
							if ( ! empty($install_actions) )
							show_message('<strong>' . __('Actions:') . '</strong> ' . implode(' | ', (array)$install_actions));
	}
}

/**
 * Install theme.
 *
 * @since 2.8.0
 *
 * @param string $package
 * @param string $feedback Optional.
 * @return mixed.
 */
function wp_install_theme($package, $feedback = '') {
	global $wp_filesystem;

	if ( !empty($feedback) )
		add_filter('install_feedback', $feedback);

	// Is a filesystem accessor setup?
	if ( ! $wp_filesystem || ! is_object($wp_filesystem) )
		WP_Filesystem();

	if ( ! is_object($wp_filesystem) )
		return new WP_Error('fs_unavailable', __('Could not access filesystem.'));

	if ( $wp_filesystem->errors->get_error_code() )
		return new WP_Error('fs_error', __('Filesystem error'), $wp_filesystem->errors);

	// Get the base theme folder
	$themes_dir = $wp_filesystem->wp_themes_dir();
	if ( empty($themes_dir) )
		return new WP_Error('fs_no_themes_dir', __('Unable to locate WordPress themes directory.'));

	// And the same for the Content directory.
	$content_dir = $wp_filesystem->wp_content_dir();
	if ( empty($content_dir) )
		return new WP_Error('fs_no_content_dir', __('Unable to locate WordPress content directory (wp-content).'));

	$themes_dir = trailingslashit( $themes_dir );
	$content_dir = trailingslashit( $content_dir );

	if ( empty($package) )
		return new WP_Error('no_package', __('Install package not available.'));

	// Download the package
	apply_filters('install_feedback', sprintf(__('Downloading theme package from %s'), $package));
	$download_file = download_url($package);

	if ( is_wp_error($download_file) )
		return new WP_Error('download_failed', __('Download failed.'), $download_file->get_error_message());

	$working_dir = $content_dir . 'upgrade/' . basename($package, '.zip');

	// Clean up working directory
	if ( $wp_filesystem->is_dir($working_dir) )
		$wp_filesystem->delete($working_dir, true);

	apply_filters('install_feedback', __('Unpacking the theme package'));
	// Unzip package to working directory
	$result = unzip_file($download_file, $working_dir);

	// Once extracted, delete the package
	@unlink($download_file);

	if ( is_wp_error($result) ) {
		$wp_filesystem->delete($working_dir, true);
		return $result;
	}

	//Get a list of the directories in the working directory before we delete it, We need to know the new folder for the theme
	$filelist = array_keys( $wp_filesystem->dirlist($working_dir) );

	if ( $wp_filesystem->exists( $themes_dir . $filelist[0] ) ) {
		$wp_filesystem->delete($working_dir, true);
		return new WP_Error('install_folder_exists', __('Folder already exists.'), $filelist[0] );
	}

	apply_filters('install_feedback', __('Installing the theme'));
	// Copy new version of theme into place.
	$result = copy_dir($working_dir, $themes_dir);
	if ( is_wp_error($result) ) {
		$wp_filesystem->delete($working_dir, true);
		return $result;
	}

	//Get a list of the directories in the working directory before we delete it, We need to know the new folder for the theme
	$filelist = array_keys( $wp_filesystem->dirlist($working_dir) );

	// Remove working directory
	$wp_filesystem->delete($working_dir, true);

	if ( empty($filelist) )
		return false; //We couldnt find any files in the working dir, therefor no theme installed? Failsafe backup.

	//TODO: TODO: TODO
	$stylesheet = $filelist[0];
	//	$theme = get_themes('/' . $folder); //Ensure to pass with leading slash //TODO: TODO: TODO
	//	$themefiles = array_keys($theme); //Assume the requested theme is the first in the list

	//Return the theme files name.
	return  $stylesheet; //$folder . '/' . $themefiles[0];
}

/**
 * Install theme from local package
 *
 * @since 2.8.0
 *
 * @param string $package
 * @param string $feedback Optional.
 * @return mixed.
 */
function wp_install_theme_local_package($package, $feedback = '') {
	global $wp_filesystem;

	if ( !empty($feedback) )
		add_filter('install_feedback', $feedback);

	// Is a filesystem accessor setup?
	if ( ! $wp_filesystem || ! is_object($wp_filesystem) )
		WP_Filesystem();

	if ( ! is_object($wp_filesystem) )
		return new WP_Error('fs_unavailable', __('Could not access filesystem.'));

	if ( $wp_filesystem->errors->get_error_code() )
		return new WP_Error('fs_error', __('Filesystem error'), $wp_filesystem->errors);

	//Get the base theme folder
	$themes_dir = $wp_filesystem->wp_themes_dir();
	if ( empty($themes_dir) )
		return new WP_Error('fs_no_themes_dir', __('Unable to locate WordPress themes directory.'));

	//And the same for the Content directory.
	$content_dir = $wp_filesystem->wp_content_dir();
	if ( empty($content_dir) )
		return new WP_Error('fs_no_content_dir', __('Unable to locate WordPress content directory (wp-content).'));

	$themes_dir = trailingslashit( $themes_dir );
	$content_dir = trailingslashit( $content_dir );

	if ( empty($package) )
		return new WP_Error('no_package', __('Install package not available.'));

	$working_dir = $content_dir . 'upgrade/' . basename($package, '.zip');


	// Clean up working directory
	if ( $wp_filesystem->is_dir($working_dir) )
		$wp_filesystem->delete($working_dir, true);

	apply_filters('install_feedback', __('Unpacking the theme package'));
	// Unzip package to working directory
	$result = unzip_file($package, $working_dir);

	// Once extracted, delete the package
	unlink($package);

	if ( is_wp_error($result) ) {
		$wp_filesystem->delete($working_dir, true);
		return $result;
	}

	//Get a list of the directories in the working directory before we delete it, We need to know the new folder for the theme
	$filelist = array_keys( $wp_filesystem->dirlist($working_dir) );

	if ( $wp_filesystem->exists( $themes_dir . $filelist[0] ) ) {
		$wp_filesystem->delete($working_dir, true);
		return new WP_Error('install_folder_exists', __('Folder already exists.'), $filelist[0] );
	}

	apply_filters('install_feedback', __('Installing the theme'));
	// Copy new version of theme into place.
	$result = copy_dir($working_dir, $themes_dir);
	if ( is_wp_error($result) ) {
		$wp_filesystem->delete($working_dir, true);
		return $result;
	}

	//Get a list of the directories in the working directory before we delete it, We need to know the new folder for the theme
	$filelist = array_keys( $wp_filesystem->dirlist($working_dir) );

	// Remove working directory
	$wp_filesystem->delete($working_dir, true);

	if ( empty($filelist) )
		return false; //We couldnt find any files in the working dir, therefor no theme installed? Failsafe backup.

	//TODO TODO TODO
	$stylesheet = $filelist[0];
	//	$theme = get_themes('/' . $folder); //Ensure to pass with leading slash
	//	$themefiles = array_keys($theme); //Assume the requested theme is the first in the list

	//Return the theme files name.
	return  $stylsheet; //$folder . '/' . $themefiles[0];
}
