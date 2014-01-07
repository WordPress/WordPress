<?php
/**
 * WordPress Plugin Install Administration API
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Retrieve plugin installer pages from WordPress Plugins API.
 *
 * It is possible for a plugin to override the Plugin API result with three
 * filters. Assume this is for plugins, which can extend on the Plugin Info to
 * offer more choices. This is very powerful and must be used with care, when
 * overriding the filters.
 *
 * The first filter, 'plugins_api_args', is for the args and gives the action as
 * the second parameter. The hook for 'plugins_api_args' must ensure that an
 * object is returned.
 *
 * The second filter, 'plugins_api', is the result that would be returned.
 *
 * @since 2.7.0
 *
 * @param string $action
 * @param array|object $args Optional. Arguments to serialize for the Plugin Info API.
 * @return object plugins_api response object on success, WP_Error on failure.
 */
function plugins_api($action, $args = null) {

	if ( is_array($args) )
		$args = (object)$args;

	if ( !isset($args->per_page) )
		$args->per_page = 24;

	/**
	 * Override the Plugin Install API arguments.
	 *
	 * Please ensure that an object is returned.
	 *
	 * @since 2.7.0
	 *
	 * @param object $args   Plugin API arguments.
	 * @param string $action The type of information being requested from the Plugin Install API.
	 */
	$args = apply_filters( 'plugins_api_args', $args, $action );

	/**
	 * Allows a plugin to override the WordPress.org Plugin Install API entirely.
	 *
	 * Please ensure that an object is returned.
	 *
	 * @since 2.7.0
	 *
	 * @param bool|object         The result object. Default is false.
	 * @param string      $action The type of information being requested from the Plugin Install API.
	 * @param object      $args   Plugin API arguments.
	 */
	$res = apply_filters( 'plugins_api', false, $action, $args );

	if ( false === $res ) {
		$url = $http_url = 'http://api.wordpress.org/plugins/info/1.0/';
		if ( $ssl = wp_http_supports( array( 'ssl' ) ) )
			$url = set_url_scheme( $url, 'https' );

		$args = array(
			'timeout' => 15,
			'body' => array(
				'action' => $action,
				'request' => serialize( $args )
			)
		);
		$request = wp_remote_post( $url, $args );

		if ( $ssl && is_wp_error( $request ) ) {
			trigger_error( __( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="http://wordpress.org/support/">support forums</a>.' ) . ' ' . '(WordPress could not establish a secure connection to WordPress.org. Please contact your server administrator.)', headers_sent() || WP_DEBUG ? E_USER_WARNING : E_USER_NOTICE );
			$request = wp_remote_post( $http_url, $args );
		}

		if ( is_wp_error($request) ) {
			$res = new WP_Error('plugins_api_failed', __( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="http://wordpress.org/support/">support forums</a>.' ), $request->get_error_message() );
		} else {
			$res = maybe_unserialize( wp_remote_retrieve_body( $request ) );
			if ( ! is_object( $res ) && ! is_array( $res ) )
				$res = new WP_Error('plugins_api_failed', __( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="http://wordpress.org/support/">support forums</a>.' ), wp_remote_retrieve_body( $request ) );
		}
	} elseif ( !is_wp_error($res) ) {
		$res->external = true;
	}

	/**
	 * Filter the Plugin Install API response results.
	 *
	 * @since 2.7.0
	 *
	 * @param object|WP_Error $res    Response object or WP_Error.
	 * @param string          $action The type of information being requested from the Plugin Install API.
	 * @param object          $args   Plugin API arguments.
	 */
	return apply_filters( 'plugins_api_result', $res, $action, $args );
}

/**
 * Retrieve popular WordPress plugin tags.
 *
 * @since 2.7.0
 *
 * @param array $args
 * @return array
 */
function install_popular_tags( $args = array() ) {
	$key = md5(serialize($args));
	if ( false !== ($tags = get_site_transient('poptags_' . $key) ) )
		return $tags;

	$tags = plugins_api('hot_tags', $args);

	if ( is_wp_error($tags) )
		return $tags;

	set_site_transient( 'poptags_' . $key, $tags, 3 * HOUR_IN_SECONDS );

	return $tags;
}

function install_dashboard() {
	?>
	<p><?php printf( __( 'Plugins extend and expand the functionality of WordPress. You may automatically install plugins from the <a href="%1$s">WordPress Plugin Directory</a> or upload a plugin in .zip format via <a href="%2$s">this page</a>.' ), 'http://wordpress.org/plugins/', self_admin_url( 'plugin-install.php?tab=upload' ) ); ?></p>

	<h4><?php _e('Search') ?></h4>
	<?php install_search_form( false ); ?>

	<h4><?php _e('Popular tags') ?></h4>
	<p class="install-help"><?php _e('You may also browse based on the most popular tags in the Plugin Directory:') ?></p>
	<?php

	$api_tags = install_popular_tags();

	echo '<p class="popular-tags">';
	if ( is_wp_error($api_tags) ) {
		echo $api_tags->get_error_message();
	} else {
		//Set up the tags in a way which can be interpreted by wp_generate_tag_cloud()
		$tags = array();
		foreach ( (array)$api_tags as $tag )
			$tags[ $tag['name'] ] = (object) array(
									'link' => esc_url( self_admin_url('plugin-install.php?tab=search&type=tag&s=' . urlencode($tag['name'])) ),
									'name' => $tag['name'],
									'id' => sanitize_title_with_dashes($tag['name']),
									'count' => $tag['count'] );
		echo wp_generate_tag_cloud($tags, array( 'single_text' => __('%s plugin'), 'multiple_text' => __('%s plugins') ) );
	}
	echo '</p><br class="clear" />';
}
add_action('install_plugins_dashboard', 'install_dashboard');

/**
 * Display search form for searching plugins.
 *
 * @since 2.7.0
 */
function install_search_form( $type_selector = true ) {
	$type = isset($_REQUEST['type']) ? wp_unslash( $_REQUEST['type'] ) : 'term';
	$term = isset($_REQUEST['s']) ? wp_unslash( $_REQUEST['s'] ) : '';

	?><form id="search-plugins" method="get" action="">
		<input type="hidden" name="tab" value="search" />
		<?php if ( $type_selector ) : ?>
		<select name="type" id="typeselector">
			<option value="term"<?php selected('term', $type) ?>><?php _e('Keyword'); ?></option>
			<option value="author"<?php selected('author', $type) ?>><?php _e('Author'); ?></option>
			<option value="tag"<?php selected('tag', $type) ?>><?php _ex('Tag', 'Plugin Installer'); ?></option>
		</select>
		<?php endif; ?>
		<input type="search" name="s" value="<?php echo esc_attr($term) ?>" autofocus="autofocus" />
		<label class="screen-reader-text" for="plugin-search-input"><?php _e('Search Plugins'); ?></label>
		<?php submit_button( __( 'Search Plugins' ), 'button', 'plugin-search-input', false ); ?>
	</form><?php
}

/**
 * Upload from zip
 * @since 2.8.0
 *
 * @param string $page
 */
function install_plugins_upload( $page = 1 ) {
?>
	<h4><?php _e('Install a plugin in .zip format'); ?></h4>
	<p class="install-help"><?php _e('If you have a plugin in a .zip format, you may install it by uploading it here.'); ?></p>
	<form method="post" enctype="multipart/form-data" class="wp-upload-form" action="<?php echo self_admin_url('update.php?action=upload-plugin'); ?>">
		<?php wp_nonce_field( 'plugin-upload'); ?>
		<label class="screen-reader-text" for="pluginzip"><?php _e('Plugin zip file'); ?></label>
		<input type="file" id="pluginzip" name="pluginzip" />
		<?php submit_button( __( 'Install Now' ), 'button', 'install-plugin-submit', false ); ?>
	</form>
<?php
}
add_action('install_plugins_upload', 'install_plugins_upload', 10, 1);

/**
 * Show a username form for the favorites page
 * @since 3.5.0
 *
 */
function install_plugins_favorites_form() {
	$user = ! empty( $_GET['user'] ) ? wp_unslash( $_GET['user'] ) : get_user_option( 'wporg_favorites' );
	?>
	<p class="install-help"><?php _e( 'If you have marked plugins as favorites on WordPress.org, you can browse them here.' ); ?></p>
	<form method="get" action="">
		<input type="hidden" name="tab" value="favorites" />
		<p>
			<label for="user"><?php _e( 'Your WordPress.org username:' ); ?></label>
			<input type="search" id="user" name="user" value="<?php echo esc_attr( $user ); ?>" />
			<input type="submit" class="button" value="<?php esc_attr_e( 'Get Favorites' ); ?>" />
		</p>
	</form>
	<?php
}

/**
 * Display plugin content based on plugin list.
 *
 * @since 2.7.0
 */
function display_plugins_table() {
	global $wp_list_table;

	if ( current_filter() == 'install_plugins_favorites' && empty( $_GET['user'] ) && ! get_user_option( 'wporg_favorites' ) )
			return;

	$wp_list_table->display();
}
add_action( 'install_plugins_search',    'display_plugins_table' );
add_action( 'install_plugins_featured',  'display_plugins_table' );
add_action( 'install_plugins_popular',   'display_plugins_table' );
add_action( 'install_plugins_new',       'display_plugins_table' );
add_action( 'install_plugins_favorites', 'display_plugins_table' );

/**
 * Determine the status we can perform on a plugin.
 *
 * @since 3.0.0
 */
function install_plugin_install_status($api, $loop = false) {
	// this function is called recursively, $loop prevents further loops.
	if ( is_array($api) )
		$api = (object) $api;

	//Default to a "new" plugin
	$status = 'install';
	$url = false;

	//Check to see if this plugin is known to be installed, and has an update awaiting it.
	$update_plugins = get_site_transient('update_plugins');
	if ( isset( $update_plugins->response ) ) {
		foreach ( (array)$update_plugins->response as $file => $plugin ) {
			if ( $plugin->slug === $api->slug ) {
				$status = 'update_available';
				$update_file = $file;
				$version = $plugin->new_version;
				if ( current_user_can('update_plugins') )
					$url = wp_nonce_url(self_admin_url('update.php?action=upgrade-plugin&plugin=' . $update_file), 'upgrade-plugin_' . $update_file);
				break;
			}
		}
	}

	if ( 'install' == $status ) {
		if ( is_dir( WP_PLUGIN_DIR . '/' . $api->slug ) ) {
			$installed_plugin = get_plugins('/' . $api->slug);
			if ( empty($installed_plugin) ) {
				if ( current_user_can('install_plugins') )
					$url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $api->slug), 'install-plugin_' . $api->slug);
			} else {
				$key = array_keys( $installed_plugin );
				$key = array_shift( $key ); //Use the first plugin regardless of the name, Could have issues for multiple-plugins in one directory if they share different version numbers
				if ( version_compare($api->version, $installed_plugin[ $key ]['Version'], '=') ){
					$status = 'latest_installed';
				} elseif ( version_compare($api->version, $installed_plugin[ $key ]['Version'], '<') ) {
					$status = 'newer_installed';
					$version = $installed_plugin[ $key ]['Version'];
				} else {
					//If the above update check failed, Then that probably means that the update checker has out-of-date information, force a refresh
					if ( ! $loop ) {
						delete_site_transient('update_plugins');
						wp_update_plugins();
						return install_plugin_install_status($api, true);
					}
				}
			}
		} else {
			// "install" & no directory with that slug
			if ( current_user_can('install_plugins') )
				$url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $api->slug), 'install-plugin_' . $api->slug);
		}
	}
	if ( isset($_GET['from']) )
		$url .= '&amp;from=' . urlencode( wp_unslash( $_GET['from'] ) );

	return compact('status', 'url', 'version');
}

/**
 * Display plugin information in dialog box form.
 *
 * @since 2.7.0
 */
function install_plugin_information() {
	global $tab;

	$api = plugins_api( 'plugin_information', array( 'slug' => wp_unslash( $_REQUEST['plugin'] ), 'is_ssl' => is_ssl() ) );

	if ( is_wp_error($api) )
		wp_die($api);

	$plugins_allowedtags = array(
		'a' => array( 'href' => array(), 'title' => array(), 'target' => array() ),
		'abbr' => array( 'title' => array() ), 'acronym' => array( 'title' => array() ),
		'code' => array(), 'pre' => array(), 'em' => array(), 'strong' => array(),
		'div' => array(), 'p' => array(), 'ul' => array(), 'ol' => array(), 'li' => array(),
		'h1' => array(), 'h2' => array(), 'h3' => array(), 'h4' => array(), 'h5' => array(), 'h6' => array(),
		'img' => array( 'src' => array(), 'class' => array(), 'alt' => array() )
	);

	$plugins_section_titles = array(
		'description'  => _x('Description',  'Plugin installer section title'),
		'installation' => _x('Installation', 'Plugin installer section title'),
		'faq'          => _x('FAQ',          'Plugin installer section title'),
		'screenshots'  => _x('Screenshots',  'Plugin installer section title'),
		'changelog'    => _x('Changelog',    'Plugin installer section title'),
		'other_notes'  => _x('Other Notes',  'Plugin installer section title')
	);

	//Sanitize HTML
	foreach ( (array)$api->sections as $section_name => $content )
		$api->sections[$section_name] = wp_kses($content, $plugins_allowedtags);
	foreach ( array( 'version', 'author', 'requires', 'tested', 'homepage', 'downloaded', 'slug' ) as $key ) {
		if ( isset( $api->$key ) )
			$api->$key = wp_kses( $api->$key, $plugins_allowedtags );
	}

	$section = isset( $_REQUEST['section'] ) ? wp_unslash( $_REQUEST['section'] ) : 'description'; //Default to the Description tab, Do not translate, API returns English.
	if ( empty( $section ) || ! isset( $api->sections[ $section ] ) ) {
		$section_titles = array_keys( (array) $api->sections );
		$section = array_shift( $section_titles );
	}

	iframe_header( __('Plugin Install') );
	echo "<div id='$tab-header'>\n";
	echo "<ul id='sidemenu'>\n";
	foreach ( (array)$api->sections as $section_name => $content ) {

		if ( isset( $plugins_section_titles[ $section_name ] ) )
			$title = $plugins_section_titles[ $section_name ];
		else
			$title = ucwords( str_replace( '_', ' ', $section_name ) );

		$class = ( $section_name == $section ) ? ' class="current"' : '';
		$href = add_query_arg( array('tab' => $tab, 'section' => $section_name) );
		$href = esc_url($href);
		$san_section = esc_attr( $section_name );
		echo "\t<li><a name='$san_section' href='$href' $class>$title</a></li>\n";
	}
	echo "</ul>\n";
	echo "</div>\n";
	?>
	<div class="alignright fyi">
		<?php if ( ! empty($api->download_link) && ( current_user_can('install_plugins') || current_user_can('update_plugins') ) ) : ?>
		<p class="action-button">
		<?php
		$status = install_plugin_install_status($api);
		switch ( $status['status'] ) {
			case 'install':
				if ( $status['url'] )
					echo '<a href="' . $status['url'] . '" target="_parent">' . __('Install Now') . '</a>';
				break;
			case 'update_available':
				if ( $status['url'] )
					echo '<a href="' . $status['url'] . '" target="_parent">' . __('Install Update Now') .'</a>';
				break;
			case 'newer_installed':
				echo '<a>' . sprintf(__('Newer Version (%s) Installed'), $status['version']) . '</a>';
				break;
			case 'latest_installed':
				echo '<a>' . __('Latest Version Installed') . '</a>';
				break;
		}
		?>
		</p>
		<?php endif; ?>
		<h2 class="mainheader"><?php /* translators: For Your Information */ _e('FYI') ?></h2>
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
			<li><strong><?php _e('Downloaded:') ?></strong> <?php printf(_n('%s time', '%s times', $api->downloaded), number_format_i18n($api->downloaded)) ?></li>
<?php endif; if ( ! empty($api->slug) && empty($api->external) ) : ?>
			<li><a target="_blank" href="http://wordpress.org/plugins/<?php echo $api->slug ?>/"><?php _e('WordPress.org Plugin Page &#187;') ?></a></li>
<?php endif; if ( ! empty($api->homepage) ) : ?>
			<li><a target="_blank" href="<?php echo $api->homepage ?>"><?php _e('Plugin Homepage &#187;') ?></a></li>
<?php endif; ?>
		</ul>
		<?php if ( ! empty($api->rating) ) : ?>
		<h2><?php _e('Average Rating') ?></h2>
		<?php wp_star_rating( array( 'rating' => $api->rating, 'type' => 'percent', 'number' => $api->num_ratings ) ); ?>
		<small><?php printf(_n('(based on %s rating)', '(based on %s ratings)', $api->num_ratings), number_format_i18n($api->num_ratings)); ?></small>
		<?php endif; ?>
	</div>
	<div id="section-holder" class="wrap">
	<?php
		if ( !empty($api->tested) && version_compare( substr($GLOBALS['wp_version'], 0, strlen($api->tested)), $api->tested, '>') )
			echo '<div class="updated"><p>' . __('<strong>Warning:</strong> This plugin has <strong>not been tested</strong> with your current version of WordPress.') . '</p></div>';

		else if ( !empty($api->requires) && version_compare( substr($GLOBALS['wp_version'], 0, strlen($api->requires)), $api->requires, '<') )
			echo '<div class="updated"><p>' . __('<strong>Warning:</strong> This plugin has <strong>not been marked as compatible</strong> with your version of WordPress.') . '</p></div>';

		foreach ( (array)$api->sections as $section_name => $content ) {

			if ( isset( $plugins_section_titles[ $section_name ] ) )
				$title = $plugins_section_titles[ $section_name ];
			else
				$title = ucwords( str_replace( '_', ' ', $section_name ) );

			$content = links_add_base_url($content, 'http://wordpress.org/plugins/' . $api->slug . '/');
			$content = links_add_target($content, '_blank');

			$san_section = esc_attr( $section_name );

			$display = ( $section_name == $section ) ? 'block' : 'none';

			echo "\t<div id='section-{$san_section}' class='section' style='display: {$display};'>\n";
			echo "\t\t<h2 class='long-header'>$title</h2>";
			echo $content;
			echo "\t</div>\n";
		}
	echo "</div>\n";

	iframe_footer();
	exit;
}
add_action('install_plugins_pre_plugin-information', 'install_plugin_information');
