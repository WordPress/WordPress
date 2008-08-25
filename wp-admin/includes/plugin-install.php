<?php

function plugins_api($action, $args = NULL) {
	global $wp_version;

	if( is_array($args) )
		$args = (object)$args;

	$args = apply_filters('plugins_api_args', $args, $action); //NOTE: Ensure that an object is returned via this filter.
	$res = apply_filters('plugins_api', false, $action, $args); //NOTE: Allows a plugin to completely override the builtin WordPress.org API.

	if ( ! $res ) {
		$request = wp_remote_post('http://api.wordpress.org/plugins/info/1.0/', array( 'body' => array('action' => $action, 'request' => serialize($args))) );
		$res = unserialize($request['body']);
		if ( ! $res )
			wp_die($request['body']);
	}

	return apply_filters('plugins_api_result', $res, $action, $args);
}

function install_popular_tags( $args = array() ) {
	if ( ! ($cache = wp_cache_get('popular_tags', 'api')) && ! ($cache = get_option('wporg_popular_tags')) )
		add_option('wporg_popular_tags', array(), '', 'no');///No autoload.

	if ( $cache && $cache->timeout + 3 * 60 * 60 > time() )
		return $cache->cached;

	$tags = plugins_api('hot_tags', $args);

	$cache = (object) array('timeout' => time(), 'cached' => $tags);

	update_option('wporg_popular_tags', $cache);
	wp_cache_set('popular_tags', $cache, 'api');

	return $tags;
}

add_action('install_plugins_search', 'install_search', 10, 1);
function install_search($page) {
	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';
	$term = isset($_REQUEST['s']) ? $_REQUEST['s'] : '';

	if( ! empty($term) ){
		$args = array();

		switch( $type ){
			case 'tag':
				$args['tag'] = sanitize_title_with_dashes($term);
				break;
			case 'term':
				$args['search'] = $term;
				break;
			case 'author':
				$args['author'] = $term;
				break;
		}

		$args['page'] = $page;

		$api = plugins_api('query_plugins', $args);

		add_action('install_plugins_table_header', 'install_search_form');

		display_plugins_table($api->plugins, $api->info['page'], $api->info['pages']);

		return;
	}
	?>

	<p><?php _e('Plugins extend and expand the functionality of WordPress. You may automatically install plugins from the <a href="http://wordpress.org/extend/plugins/">WordPress Plugin Directory</a> via this page.') ?></p>

	<h4><?php _e('Search') ?></h4>
	<?php install_search_form() ?>
	<p>	<?php _e('You may search based on 3 criteria:') ?><br />
		<?php _e('<strong>Term:</strong> Searches plugins names and descriptions for the specified term') ?><br />
		<?php _e('<strong>Tag:</strong> Searches for plugins tagged as such') ?><br />
		<?php _e('<strong>Author:</strong> Searches for plugins created by the Author, or which the Author contributed to.') ?></p>

	<h4><?php _e('Popular tags') ?></h4>
	<p><?php _e('You may also search based on these popular tags, These are tags which are most popular on WordPress.org') ?></p>
	<?php

		$api_tags = install_popular_tags();

		//Set up the tags in a way which can be interprated by wp_generate_tag_cloud()
		$tags = array();
		foreach ( (array)$api_tags as $tag )
			$tags[ $tag['name'] ] = (object) array(
									'link' => clean_url( admin_url('plugin-install.php?tab=search&type=tag&s=' . urlencode($tag['name'])) ),
									'name' => $tag['name'],
									'count' => $tag['count'] );
		echo wp_generate_tag_cloud($tags, array( 'single_text' => __('%d plugin'), 'multiple_text' => __('%d plugins') ) );
}

function install_search_form(){
	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';
	$term = isset($_REQUEST['s']) ? $_REQUEST['s'] : '';

	?><form id="search-plugins" method="post" action="<?php echo admin_url('plugin-install.php?tab=search') ?>">
		<select name="type" id="typeselector">
			<option value="term"<?php selected('term', $type) ?>><?php _e('Term') ?></option>
			<option value="tag"<?php selected('tag', $type) ?>><?php _e('Tag') ?></option>
			<option value="author"<?php selected('author', $type) ?>><?php _e('Author') ?></option>
		</select>
		<input type="text" name="s" id="search-field" value="<?php echo attribute_escape($term) ?>" />
		<input type="submit" name="search" value="<?php echo attribute_escape(__('Search')) ?>" class="button" />
	</form><?php
}

add_action('install_plugins_featured', 'install_featured', 10, 1);
function install_featured($page){
	$args = array('browse' => 'featured', 'page' => $page);
	$api = plugins_api('query_plugins', $args);
	display_plugins_table($api->plugins, $api->info['page'], $api->info['pages']);
}
add_action('install_plugins_popular', 'install_popular', 10, 1);
function install_popular($page){
	$args = array('browse' => 'popular', 'page' => $page);
	$api = plugins_api('query_plugins', $args);
	display_plugins_table($api->plugins, $api->info['page'], $api->info['pages']);
}
add_action('install_plugins_new', 'install_new', 10, 1);
function install_new($page){
	$args = array('browse' => 'new', 'page' => $page);
	$api = plugins_api('query_plugins', $args);
	display_plugins_table($api->plugins, $api->info['page'], $api->info['pages']);
}
add_action('install_plugins_updated', 'install_updated', 10, 1);
function install_updated($page){
	$args = array('browse' => 'updated', 'page' => $page);
	$api = plugins_api('query_plugins', $args);
	display_plugins_table($api->plugins, $api->info['page'], $api->info['pages']);
}
add_action('install_plugins_upload', 'install_upload_custom', 10, 1);
function install_upload_custom($page){
	//$args = array('browse' => 'updated', 'page' => $page);
	//$api = plugins_api('query_plugins', $args);
	//display_plugins_table($api->plugins, $api->info['page'], $api->info['pages']);
	echo '<h1>Not Implemented</h1> <p>Will utilise SwfUpload(if available) & unzip .zip plugin packages</p>';
}

function display_plugins_table($plugins, $page = 1, $totalpages = 1){
	global $tab;

	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';
	$term = isset($_REQUEST['s']) ? $_REQUEST['s'] : '';

	$plugins_allowedtags = array('a' => array('href' => array(),'title' => array(), 'target' => array()),'abbr' => array('title' => array()),'acronym' => array('title' => array()),'code' => array(),'em' => array(),'strong' => array());

?>
	<div class="tablenav">
		<div class="alignleft">
		<?php do_action('install_plugins_table_header'); ?>
		</div>
		<?php
			$url = $_SERVER['REQUEST_URI'];
			if ( ! empty($term) )
				$url = add_query_arg('s', $term, $url);
			if ( ! empty($type) )
				$url = add_query_arg('type', $type, $url);

			$page_links = paginate_links( array(
				'base' => add_query_arg('paged', '%#%', $url),
				'format' => '',
				'total' => $totalpages,
				'current' => $page
			));

			if ( $page_links )
				echo "\t\t<div class='tablenav-pages'>$page_links</div>";
?>
	</div>
	<br class="clear" />
	<table class="widefat" id="install-plugins">
		<thead>
			<tr>
				<th scope="col" class="name"><?php _e('Name'); ?></th>
				<th scope="col" class="num"><?php _e('Version'); ?></th>
				<th scope="col" class="num"><?php _e('Rating'); ?></th>
				<th scope="col" class="desc"><?php _e('Description'); ?></th>
				<th scope="col" class="action-links"><?php _e('Actions'); ?></th>
			</tr>
		</thead>
		<tbody class="plugins">
		<?php
			if( empty($plugins) )
				echo '<tr><td colspan="5">', __('No plugins match your request.'), '</td></tr>';

			foreach( (array) $plugins as $plugin ){
				if ( is_object($plugin) )
					$plugin = (array) $plugin;

				$title = wp_kses($plugin['name'], $plugins_allowedtags);
				$description = wp_kses($plugin['description'], $plugins_allowedtags);
				$version = wp_kses($plugin['version'], $plugins_allowedtags);

				$name = strip_tags($title . ' ' . $version);

				$author = $plugin['author'];
				if( ! empty($plugin['author']) )
					$author = ' <cite>' . sprintf( __('By %s'), $author ) . '.</cite>';

				$author = wp_kses($author, $plugins_allowedtags);

				if( isset($plugin['homepage']) )
					$title = '<a target="_blank" href="' . $plugin['homepage'] . '">' . $title . '</a>';

				$action_links = array();
				$action_links[] = '<a href="' . admin_url('plugin-install.php?tab=plugin-information&plugin=' . $plugin['slug'] .
									'&TB_iframe=true&width=600&height=800') . '" class="thickbox onclick" title="' .
									attribute_escape($name) . '">' . __('Install') . '</a>';

				$action_links = apply_filters('plugin_install_action_links', $action_links, $plugin);
			?>
			<tr>
				<td class="name"><?php echo $title; ?></td>
				<td class="vers"><?php echo $version; ?></td>
				<td class="vers">
					<div class="star-holder" title="<?php printf( __('based on %d ratings'), $plugin['num_ratings'] ); ?>">
						<div class="star star-rating" style="width: <?php echo attribute_escape($plugin['rating']) ?>px"></div>
						<div class="star star5"><img src="<?php echo admin_url('images/star.gif'); ?>" alt="<?php _e('5 stars') ?>" /></div>
						<div class="star star4"><img src="<?php echo admin_url('images/star.gif'); ?>" alt="<?php _e('4 stars') ?>" /></div>
						<div class="star star3"><img src="<?php echo admin_url('images/star.gif'); ?>" alt="<?php _e('3 stars') ?>" /></div>
						<div class="star star2"><img src="<?php echo admin_url('images/star.gif'); ?>" alt="<?php _e('2 stars') ?>" /></div>
						<div class="star star1"><img src="<?php echo admin_url('images/star.gif'); ?>" alt="<?php _e('1 star') ?>" /></div>
					</div>
				</td>
				<td class="desc"><p><?php echo $description, $author; ?></p></td>
				<td class="action-links"><?php if ( !empty($action_links) )	echo implode(' | ', $action_links); ?></td>
			</tr>
			<?php
			}
			?>
		</tbody>
	</table>
<?php
}

function install_iframe_header($title = '') {
if( empty($title) )
	$title = __('Plugin Install &#8212; WordPress');

register_shutdown_function('install_iframe_footer'); //Do footer after content, Allows us to simply die or return at any point as may happen with error handlers

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<title><?php bloginfo('name') ?> &rsaquo; <?php echo $title ?></title>
<?php
wp_enqueue_style( 'global' );
wp_enqueue_style( 'wp-admin' );
wp_enqueue_style( 'colors' );
?>
<script type="text/javascript">
//<![CDATA[
function addLoadEvent(func) {if ( typeof wpOnload!='function'){wpOnload=func;}else{ var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}}
//]]>
</script>
<?php
do_action('admin_print_styles');
do_action('admin_print_scripts');
do_action('admin_head');
?>
</head>
<body<?php if ( isset($GLOBALS['body_id']) ) echo ' id="' . $GLOBALS['body_id'] . '"'; ?>>
<?php
}

function install_iframe_footer() {
echo '
</body>
</html>';
}

add_action('install_plugins_pre_plugin-information', 'install_plugin_information');
function install_plugin_information() {
	global $tab;

	$api = plugins_api('plugin_information', array('slug' => $_REQUEST['plugin']));

	$section = isset($_REQUEST['section']) ? $_REQUEST['section'] : 'description'; //Default to the Description tab, Do not translate, API returns English.
	if( empty($section) || ! isset($api->sections[ $section ]) )
		$section = array_shift( $section_titles = array_keys((array)$api->sections) );

	install_iframe_header();
	echo "<div id='$tab-header'>\n";
	echo "<ul id='sidemenu'>\n";
	foreach ( (array)$api->sections as $section_name => $content ) {

		$title = $section_name;
		$title[0] = strtoupper($title[0]); //Capitalize first character.
		$title = str_replace('_', ' ', $title);

		$class = ( $section_name == $section ) ? ' class="current"' : '';
		$href = add_query_arg( array('tab' => $tab, 'section' => $section_name) );
		$href = clean_url($href);
		$san_title = attribute_escape(sanitize_title_with_dashes($title));
		echo "\t<li><a name='$san_title' target='' href='$href'$class>$title</a></li>\n";
	}
	echo "</ul>\n";
	echo "</div>\n";

	?>
	<div class="alignright fyi">
		<?php if ( ! empty($api->download_link) ) : ?>
		<p class="action-button">
		<?php
			//Default to a "new" plugin
			$type = 'install';
			//Check to see if this plugin is known to be installed, and has an update awaiting it.
			$update_plugins = get_option('update_plugins');
			foreach ( (array)$update_plugins->response as $file => $plugin ) {
				if ( $plugin->slug === $api->slug ) {
					$type = 'update_available';
					$update_file = $file;
					break;
				}
			}
			if ( 'install' == $type && file_exists( WP_PLUGIN_DIR  . '/' . $api->slug ) ) //TODO: Make more.. searchable?
				$type = 'latest_installed';

			switch ( $type ) :
				default:
				case 'install':
					if ( current_user_can('install_plugins') ) :
				?><a href="<?php echo wp_nonce_url(admin_url('plugin-install.php?tab=install&plugin=' . $api->slug), 'install-plugin_' . $api->slug) ?>"><?php _e('Install Now') ?></a><?php
					endif;
				break;
				case 'update_available':
					if ( current_user_can('update_plugins') ) :
						?><a href="<?php echo wp_nonce_url(admin_url('update.php?action=upgrade-plugin&plugin=' . $update_file), 'upgrade-plugin_' . $update_file) ?>"><?php _e('Install Update Now') ?></a><?php
					endif;
				break;
				case 'latest_installed':
					if ( current_user_can('install_plugins') || current_user_can('update_plugins') ) :
					?><a><?php _e('Latest Version Installed') ?></a><?php
					endif;
				break;
			endswitch; ?>
		</p>
		<?php endif; ?>
		<h2 class="mainheader"><?php _e('FYI') ?></h2>
		<ul>
<?php if ( ! empty($api->version) ) : ?>
			<li><strong><?php _e('Version:') ?></strong> <?php echo $api->version ?></li>
<?php endif; if ( ! empty($api->author) ) : ?>
			<li><strong><?php _e('Author:') ?></strong> <?php echo links_add_target($api->author, '_blank') ?></li>
<?php endif; if ( ! empty($api->last_updated) ) : ?>
			<li><strong><?php _e('Last Updated:') ?></strong> <span title="<?php echo $api->last_updated ?>"><?php
							printf( __('%s ago'), human_time_diff(strtotime($api->last_updated)) ) ?></span></li>
<?php endif; if ( ! empty($api->requires) ) : ?>
			<li><strong><?php _e('Requires WordPress Version:') ?></strong> <?php printf(__('%s or higher'), $api->requires) ?></li>
<?php endif; if ( ! empty($api->tested) ) : ?>
			<li><strong><?php _e('Compatible up to:') ?></strong> <?php echo $api->tested ?></li>
<?php endif; if ( ! empty($api->downloaded) ) : ?>
			<li><strong><?php _e('Downloaded:') ?></strong> <?php printf('%s times', number_format_i18n($api->downloaded)) ?></li>
<?php endif; if ( ! empty($api->slug) ) : ?>
			<li><a target="_blank" href="http://wordpress.org/extend/plugins/<?php echo $api->slug ?>/"><?php _e('WordPress.org Plugin Page &#187;') ?></a></li>
<?php endif; if ( ! empty($api->homepage) ) : ?>
			<li><a target="_blank" href="<?php echo $api->homepage ?>"><?php _e('Plugin Homepage  &#187;') ?></a></li>
<?php endif; ?>
		</ul>
		<h2><?php _e('Average Rating') ?></h2>
		<div class="star-holder" title="<?php printf(__('(based on %d ratings)'), $api->num_ratings) ?>">
			<div class="star star-rating" style="width: <?php echo attribute_escape($api->rating) ?>px"></div>
			<div class="star star5"><img src="<?php echo admin_url('images/star.gif'); ?>" alt="<?php _e('5 stars') ?>" /></div>
			<div class="star star4"><img src="<?php echo admin_url('images/star.gif'); ?>" alt="<?php _e('4 stars') ?>" /></div>
			<div class="star star3"><img src="<?php echo admin_url('images/star.gif'); ?>" alt="<?php _e('3 stars') ?>" /></div>
			<div class="star star2"><img src="<?php echo admin_url('images/star.gif'); ?>" alt="<?php _e('2 stars') ?>" /></div>
			<div class="star star1"><img src="<?php echo admin_url('images/star.gif'); ?>" alt="<?php _e('1 star') ?>" /></div>
		</div>
		<small><?php printf(__('(based on %d ratings)'), $api->num_ratings) ?></small>
	</div>
	<div id="section-holder" class="wrap">
		<?php
		foreach ( (array)$api->sections as $section_name => $content ) {
			$title = $section_name;
			$title[0] = strtoupper($title[0]);
			$title = str_replace('_', ' ', $title);

			$content = links_add_base_url($content, 'http://wordpress.org/extend/plugins/' . $api->slug . '/');
			$content = links_add_target($content, '_blank');

			$san_title = attribute_escape(sanitize_title_with_dashes($title));

			$display = ( $section_name == $section ) ? 'block' : 'none';

			echo "\t<div id='section-{$san_title}' style='display: {$display};'>\n";
			echo "\t\t<h2 class='long-header'>$title</h2>";
			echo $content;
			echo "\t</div>\n";
		}
	echo "</div>\n";

	exit;
}

add_action('install_plugins_pre_install', 'install_plugin');
function install_plugin() {

	$plugin = isset($_REQUEST['plugin']) ? $_REQUEST['plugin'] : '';

	check_admin_referer('install-plugin_' . $plugin);

	install_iframe_header();

	$api = plugins_api('plugin_information', array('slug' => $plugin, 'fields' => array('sections' => false) ) ); //Save on a bit of bandwidth.

	echo '<div class="wrap">';
	echo '<h2>', sprintf( __('Installing Plugin: %s'), $api->name . ' ' . $api->version ), '</h2>';

	do_plugin_install($api->download_link, $api);
	echo '</div>';

	exit;
}
function do_plugin_install($download_url = '', $plugin_information = NULL) {
	global $wp_filesystem;

	if ( empty($download_url) ) {
		show_message( __('No plugin Specified') );
		return;
	}

	$plugin = isset($_REQUEST['plugin']) ? $_REQUEST['plugin'] : '';

	$url = 'plugin-install.php?tab=install';
	$url = add_query_arg(array('plugin' => $plugin, 'plugin_name' => $_REQUEST['plugin_name'], 'download_url' => $_REQUEST['download_url']), $url);

	$url = wp_nonce_url($url, 'install-plugin_' . $plugin);
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

	$result = wp_install_plugin( $download_url, 'show_message' );

	if ( is_wp_error($result) ) {
		show_message($result);
		show_message( __('Installation Failed') );
	} else {
		show_message( sprintf(__('Successfully installed the plugin <strong>%s %s</strong>.'), $plugin_information->name, $plugin_information->version) );
		$plugin_file = $result;

		$install_actions = apply_filters('install_plugin_complete_actions', array(
							'activate_plugin' => '<a href="' . wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin_file, 'activate-plugin_' . $plugin_file) . '" title="' . __('Activate this plugin') . '" target="_parent">' . __('Activate Plugin') . '</a>',
							'plugins_page' => '<a href="' . admin_url('plugins.php') . '" title="' . __('Goto plugins page') . '" target="_parent">' . __('Return to Plugins page') . '</a>',
							'dismiss_dialog' => '<a href="' . admin_url('plugin-installer.php') . '" onclick="window.parent.tb_remove(); return false;" title="' . __('Dismiss Dialog') . '" target="_parent">' . __('Dismiss Dialog') . '</a>'
							), $plugin_information, $plugin_file);

		echo '<p><strong>' . __('Actions:') . '</strong>' . implode(' | ', (array)$install_actions) . '</p>';
	}
}

function wp_install_plugin($package, $feedback = '') {
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

	//Get the base plugin folder
	$plugins_dir = $wp_filesystem->wp_plugins_dir();
	if ( empty($plugins_dir) )
		return new WP_Error('fs_no_plugins_dir', __('Unable to locate WordPress Plugin directory.'));

	//And the same for the Content directory.
	$content_dir = $wp_filesystem->wp_content_dir();
	if( empty($content_dir) )
		return new WP_Error('fs_no_content_dir', __('Unable to locate WordPress Content directory (wp-content).'));

	$plugins_dir = trailingslashit( $plugins_dir );
	$content_dir = trailingslashit( $content_dir );

	if ( empty($package) )
		return new WP_Error('no_package', __('Install package not available.'));

	// Download the package
	apply_filters('install_feedback', sprintf(__('Downloading plugin package from %s'), $package));
	$download_file = download_url($package);

	if ( is_wp_error($download_file) )
		return new WP_Error('download_failed', __('Download failed.'), $download_file->get_error_message());

	$working_dir = $content_dir . 'upgrade/' . basename($package, '.zip');

	// Clean up working directory
	if ( $wp_filesystem->is_dir($working_dir) )
		$wp_filesystem->delete($working_dir, true);

	apply_filters('install_feedback', __('Unpacking the plugin package'));
	// Unzip package to working directory
	$result = unzip_file($download_file, $working_dir);

	// Once extracted, delete the package
	unlink($download_file);

	if ( is_wp_error($result) ) {
		$wp_filesystem->delete($working_dir, true);
		return $result;
	}

	//Get a list of the directories in the working directory before we delete it, We need to know the new folder for the plugin
	$filelist = array_keys( $wp_filesystem->dirlist($working_dir) );

	if( $wp_filesystem->exists( $plugins_dir . $filelist[0] ) ) {
		$wp_filesystem->delete($working_dir, true);
		return new WP_Error('install_folder_exists', __('Folder allready exists.'), $filelist[0] );
	}

	apply_filters('install_feedback', __('Installing the plugin'));
	// Copy new version of plugin into place.
	$result = copy_dir($working_dir, $plugins_dir);
	if ( is_wp_error($result) ) {
		$wp_filesystem->delete($working_dir, true);
		return $result;
	}

	//Get a list of the directories in the working directory before we delete it, We need to know the new folder for the plugin
	$filelist = array_keys( $wp_filesystem->dirlist($working_dir) );

	// Remove working directory
	$wp_filesystem->delete($working_dir, true);

	if( empty($filelist) )
		return false; //We couldnt find any files in the working dir, therefor no plugin installed? Failsafe backup.

	$folder = $filelist[0];
	$plugin = get_plugins('/' . $folder); //Ensure to pass with leading slash
	$pluginfiles = array_keys($plugin); //Assume the requested plugin is the first in the list

	//Return the plugin files name.
	return  $folder . '/' . $pluginfiles[0];
}


?>