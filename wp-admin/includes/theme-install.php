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
 * Retrieve list of WordPress theme features (aka theme tags)
 *
 * @since 2.8.0
 *
 * @return array
 */
function install_themes_feature_list( ) {
	if ( !$cache = get_transient( 'wporg_theme_feature_list' ) )
		set_transient( 'wporg_theme_feature_list', array( ),  10800);

	if ( $cache  )
		return $cache;

	$feature_list = themes_api( 'feature_list', array( ) );
	if ( is_wp_error( $feature_list ) )
		return $features;

	set_transient( 'wporg_theme_feature_list', $feature_list, 10800 );

	return $feature_list;
}

add_action('install_themes_search', 'install_theme_search', 10, 1);
/**
 * Display theme search results
 *
 * @since 2.8.0
 *
 * @param string $page
 */
function install_theme_search($page) {
	global $theme_field_defaults;

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

	if ( !empty( $_POST['features'] ) ) {
		$terms = $_POST['features'];
		$terms = array_map( 'trim', $terms );
		$terms = array_map( 'sanitize_title_with_dashes', $terms );
		$args['tag'] = $terms;
		$_REQUEST['s'] = implode( ',', $terms );
		$_REQUEST['type'] = 'tag';
	}

	$api = themes_api('query_themes', $args);

	if ( is_wp_error($api) )
		wp_die($api);

	add_action('install_themes_table_header', 'install_theme_search_form');

	display_themes($api->themes, $api->info['page'], $api->info['pages']);
}

/**
 * Display search form for searching themes.
 *
 * @since 2.8.0
 */
function install_theme_search_form() {
	$type = isset( $_REQUEST['type'] ) ? stripslashes( $_REQUEST['type'] ) : '';
	$term = isset( $_REQUEST['s'] ) ? stripslashes( $_REQUEST['s'] ) : '';
	?>
<p class="install-help"><?php _e('Search for themes by keyword, author, or tag.') ?></p>

<form id="search-themes" method="post" action="<?php echo admin_url( 'theme-install.php?tab=search' ); ?>">
	<select	name="type" id="typeselector">
	<option value="term" <?php selected('term', $type) ?>><?php _e('Term'); ?></option>
	<option value="author" <?php selected('author', $type) ?>><?php _e('Author'); ?></option>
	<option value="tag" <?php selected('tag', $type) ?>><?php echo _x('Tag', 'Theme Installer'); ?></option>
	</select>
	<input type="text" name="s" size="30" value="<?php echo esc_attr($term) ?>" />
	<input type="submit" name="search" value="<?php esc_attr_e('Search'); ?>" class="button" />
</form>
<?php
}

add_action('install_themes_dashboard', 'install_themes_dashboard');
/**
 * Display tags filter for themes.
 *
 * @since 2.8.0
 */
function install_themes_dashboard() {
	install_theme_search_form();
?>
<h4><?php _e('Feature Filter') ?></h4>
<form method="post" action="<?php echo admin_url( 'theme-install.php?tab=search' ); ?>">
<p class="install-help"><?php _e('Find a theme based on specific features') ?></p>
	<?php
	$feature_list = install_themes_feature_list( );
	echo '<div class="feature-filter">';
	$trans = array ('Colors' => __('Colors'), 'black' => __('Black'), 'blue' => __('Blue'), 'brown' => __('Brown'),
		'green' => __('Green'), 'orange' => __('Orange'), 'pink' => __('Pink'), 'purple' => __('Purple'), 'red' => __('Red'),
		'silver' => __('Silver'), 'tan' => __('Tan'), 'white' => __('White'), 'yellow' => __('Yellow'), 'dark' => __('Dark'),
		'light' => __('Light'), 'Columns' => __('Columns'), 'one-column' => __('One Column'), 'two-columns' => __('Two Columns'),
		'three-columns' => __('Three Columns'), 'four-columns' => __('Four Columns'), 'left-sidebar' => __('Left Sidebar'),
		'right-sidebar' => __('Right Sidebar'), 'Width' => __('Width'), 'fixed-width' => __('Fixed Width'), 'flexible-width' => __('Flexible Width'),
		'Features' => __('Features'), 'custom-colors' => __('Custom Colors'), 'custom-header' => __('Custom Header'), 'theme-options' => __('Theme Options'),
		'threaded-comments' => __('Threaded Comments'), 'sticky-post' => __('Sticky Post'), 'microformats' => __('Microformats'),
		'Subject' => __('Subject'), 'holiday' => __('Holiday'), 'photoblogging' => __('Photoblogging'), 'seasonal' => __('Seasonal'),
	);

	foreach ( (array) $feature_list as $feature_name => $features ) {
		if ( isset($trans[$feature_name]) )
			 $feature_name = $trans[$feature_name];
		$feature_name = esc_html( $feature_name );
		echo '<div class="feature-name">' . $feature_name . '</div>';

		echo '<ol style="float: left; width: 725px;" class="feature-group">';
		foreach ( $features as $feature ) {
			$feature_name = $feature;
			if ( isset($trans[$feature]) )
				$feature_name = $trans[$feature];
			$feature_name = esc_html( $feature_name );
			$feature = esc_attr($feature);
?>

<li>
	<input type="checkbox" name="features[<?php echo $feature; ?>]" id="feature-id-<?php echo $feature; ?>" value="<?php echo $feature; ?>" />
	<label for="feature-id-<?php echo $feature; ?>"><?php echo $feature_name; ?></label>
</li>

<?php	} ?>
</ol>
<br class="clear" />
<?php
	} ?>

</div>
<br class="clear" />
<p><input type="submit" name="search" value="<?php esc_attr_e('Find Themes'); ?>" class="button" /></p>
</form>
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
<p class="install-help"><?php _e('If you have a theme in a .zip format, you may install it by uploading it here.') ?></p>
<form method="post" enctype="multipart/form-data" action="<?php echo admin_url('update.php?action=upload-theme') ?>">
	<?php wp_nonce_field( 'theme-upload') ?>
	<input type="file" name="themezip" />
	<input type="submit"
	class="button" value="<?php esc_attr_e('Install Now') ?>" />
</form>
	<?php
}

function display_theme($theme, $actions = null, $show_details = true) {
	global $themes_allowedtags;

	if ( empty($theme) )
		return;

	$name = wp_kses($theme->name, $themes_allowedtags);
	$desc = wp_kses($theme->description, $themes_allowedtags);
	//if ( strlen($desc) > 30 )
	//	$desc =  substr($desc, 0, 15) . '<span class="dots">...</span><span>' . substr($desc, -15) . '</span>';

	$preview_link = $theme->preview_url . '?TB_iframe=true&amp;width=600&amp;height=400';
	if ( !is_array($actions) ) {
		$actions = array();
		$actions[] = '<a href="' . admin_url('theme-install.php?tab=theme-information&amp;theme=' . $theme->slug .
										'&amp;TB_iframe=true&amp;tbWidth=500&amp;tbHeight=350') . '" class="thickbox thickbox-preview onclick" title="' . esc_attr(sprintf(__('Install &#8220;%s&#8221;'), $name)) . '">' . __('Install') . '</a>';
		$actions[] = '<a href="' . $preview_link . '" class="thickbox thickbox-preview onclick previewlink" title="' . esc_attr(sprintf(__('Preview &#8220;%s&#8221;'), $name)) . '">' . __('Preview') . '</a>';
		$actions = apply_filters('theme_install_action_links', $actions, $theme);
	}

	$actions = implode ( ' | ', $actions );
	?>
<a class='thickbox thickbox-preview screenshot'
	href='<?php echo esc_url($preview_link); ?>'
	title='<?php echo esc_attr(sprintf(__('Preview &#8220;%s&#8221;'), $name)); ?>'>
<img src='<?php echo esc_url($theme->screenshot_url); ?>' width='150' />
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
	<div class="star star-rating" style="width: <?php echo esc_attr($theme->rating) ?>px"></div>
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
<div class="alignleft actions"><?php do_action('install_themes_table_header'); ?></div>
	<?php
	$url = esc_url($_SERVER['REQUEST_URI']);
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
	?>
</div>
<br class="clear" />
<?php
	if ( empty($themes) ) {
		_e('No themes found');
		return;
	}
?>
<table id="availablethemes" cellspacing="0" cellpadding="0">
<?php
	$rows = ceil(count($themes) / 3);
	$table = array();
	$theme_keys = array_keys($themes);
	for ( $row = 1; $row <= $rows; $row++ )
		for ( $col = 1; $col <= 3; $col++ )
			$table[$row][$col] = array_shift($theme_keys);

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
		?>
		<td class="<?php echo join(' ', $class); ?>"><?php
			if ( isset($themes[$theme_index]) )
				display_theme($themes[$theme_index]);
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
	global $tab, $themes_allowedtags;

	$api = themes_api('theme_information', array('slug' => stripslashes( $_REQUEST['theme'] ) ));

	if ( is_wp_error($api) )
		wp_die($api);

	// Sanitize HTML
	foreach ( (array)$api->sections as $section_name => $content )
		$api->sections[$section_name] = wp_kses($content, $themes_allowedtags);
	foreach ( array('version', 'author', 'requires', 'tested', 'homepage', 'downloaded', 'slug') as $key )
		$api->$key = wp_kses($api->$key, $themes_allowedtags);

	iframe_header( __('Theme Install') );

	if ( empty($api->download_link) ) {
		echo '<div id="message" class="error"><p>' . __('<strong>Error:</strong> This theme is currently not available. Please try again later.') . '</p></div>';
		iframe_footer();
		exit;
	}

	if ( !empty($api->tested) && version_compare($GLOBALS['wp_version'], $api->tested, '>') )
		echo '<div class="updated"><p>' . __('<strong>Warning:</strong> This theme has <strong>not been tested</strong> with your current version of WordPress.') . '</p></div>';
	else if ( !empty($api->requires) && version_compare($GLOBALS['wp_version'], $api->requires, '<') )
		echo '<div class="updated"><p>' . __('<strong>Warning:</strong> This theme has not been marked as <strong>compatible</strong> with your version of WordPress.') . '</p></div>';

	// Default to a "new" theme
	$type = 'install';
	// Check to see if this theme is known to be installed, and has an update awaiting it.
	$update_themes = get_transient('update_themes');
	if ( is_object($update_themes) && isset($update_themes->response) ) {
		foreach ( (array)$update_themes->response as $theme_slug => $theme_info ) {
			if ( $theme_slug === $api->slug ) {
				$type = 'update_available';
				$update_file = $theme_slug;
				break;
			}
		}
	}

	$themes = get_themes();
	foreach ( $themes as $this_theme ) {
		if ( is_array($this_theme) && $this_theme['Stylesheet'] == $api->slug ) {
			if ( $this_theme['Version'] == $api->version ) {
				$type = 'latest_installed';
			} elseif ( $this_theme['Version'] > $api->version ) {
				$type = 'newer_installed';
				$newer_version = $this_theme['Version'];
			}
			break;
		}
	}
?>

<div class='available-theme'>
<img src='<?php echo esc_url($api->screenshot_url) ?>' width='300' class="theme-preview-img" />
<h3><?php echo $api->name; ?></h3>
<p><?php printf(__('by %s'), $api->author); ?></p>
<p><?php printf(__('Version: %s'), $api->version); ?></p>

<?php
$buttons = '<a class="button" id="cancel" href="#" onclick="tb_close();return false;">' . __('Cancel') . '</a> ';

switch ( $type ) {
default:
case 'install':
	if ( current_user_can('install_themes') ) :
	$buttons .= '<a class="button-primary" id="install" href="' . wp_nonce_url(admin_url('update.php?action=install-theme&theme=' . $api->slug), 'install-theme_' . $api->slug) . '" target="_parent">' . __('Install Now') . '</a>';
	endif;
	break;
case 'update_available':
	if ( current_user_can('update_themes') ) :
	$buttons .= '<a class="button-primary" id="install"	href="' . wp_nonce_url(admin_url('update.php?action=upgrade-theme&theme=' . $update_file), 'upgrade-theme_' . $update_file) . '" target="_parent">' . __('Install Update Now') . '</a>';
	endif;
	break;
case 'newer_installed':
	if ( current_user_can('install_themes') || current_user_can('update_themes') ) :
	?><p><?php printf(__('Newer version (%s) is installed.'), $newer_version); ?></p><?php
	endif;
	break;
case 'latest_installed':
	if ( current_user_can('install_themes') || current_user_can('update_themes') ) :
	?><p><?php _e('This version is already installed.'); ?></p><?php
	endif;
	break;
} ?>
<br class="clear" />
</div>

<p class="action-button">
<?php echo $buttons; ?>
<br class="clear" />
</p>

<?php
	iframe_footer();
	exit;
}
