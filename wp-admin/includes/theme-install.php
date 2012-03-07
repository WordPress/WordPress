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
 * Retrieve list of WordPress theme features (aka theme tags)
 *
 * @since 2.8.0
 *
 * @deprecated since 3.1.0 Use get_theme_feature_list() instead.
 *
 * @return array
 */
function install_themes_feature_list( ) {
	if ( !$cache = get_transient( 'wporg_theme_feature_list' ) )
		set_transient( 'wporg_theme_feature_list', array( ), 10800);

	if ( $cache )
		return $cache;

	$feature_list = themes_api( 'feature_list', array( ) );
	if ( is_wp_error( $feature_list ) )
		return $features;

	set_transient( 'wporg_theme_feature_list', $feature_list, 10800 );

	return $feature_list;
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

<form id="search-themes" method="get" action="">
	<input type="hidden" name="tab" value="search" />
	<select	name="type" id="typeselector">
	<option value="term" <?php selected('term', $type) ?>><?php _e('Term'); ?></option>
	<option value="author" <?php selected('author', $type) ?>><?php _e('Author'); ?></option>
	<option value="tag" <?php selected('tag', $type) ?>><?php _ex('Tag', 'Theme Installer'); ?></option>
	</select>
	<input type="text" name="s" size="30" value="<?php echo esc_attr($term) ?>" />
	<?php submit_button( __( 'Search' ), 'button', 'search', false ); ?>
</form>
<?php
}

/**
 * Display tags filter for themes.
 *
 * @since 2.8.0
 */
function install_themes_dashboard() {
	install_theme_search_form();
?>
<h4><?php _e('Feature Filter') ?></h4>
<p class="install-help"><?php _e('Find a theme based on specific features') ?></p>

<form method="get" action="">
	<input type="hidden" name="tab" value="search" />
	<?php
	$feature_list = get_theme_feature_list( );
	echo '<div class="feature-filter">';

	foreach ( (array) $feature_list as $feature_name => $features ) {
		$feature_name = esc_html( $feature_name );
		echo '<div class="feature-name">' . $feature_name . '</div>';

		echo '<ol class="feature-group">';
		foreach ( $features as $feature => $feature_name ) {
			$feature_name = esc_html( $feature_name );
			$feature = esc_attr($feature);
?>

<li>
	<input type="checkbox" name="features[]" id="feature-id-<?php echo $feature; ?>" value="<?php echo $feature; ?>" />
	<label for="feature-id-<?php echo $feature; ?>"><?php echo $feature_name; ?></label>
</li>

<?php	} ?>
</ol>
<br class="clear" />
<?php
	} ?>

</div>
<br class="clear" />
<?php submit_button( __( 'Find Themes' ), 'button', 'search' ); ?>
</form>
<?php
}
add_action('install_themes_dashboard', 'install_themes_dashboard');

function install_themes_upload($page = 1) {
?>
<h4><?php _e('Install a theme in .zip format') ?></h4>
<p class="install-help"><?php _e('If you have a theme in a .zip format, you may install it by uploading it here.') ?></p>
<form method="post" enctype="multipart/form-data" action="<?php echo self_admin_url('update.php?action=upload-theme') ?>">
	<?php wp_nonce_field( 'theme-upload') ?>
	<input type="file" name="themezip" />
	<?php submit_button( __( 'Install Now' ), 'button', 'install-theme-submit', false ); ?>
</form>
	<?php
}
add_action('install_themes_upload', 'install_themes_upload', 10, 1);

/*
 * Prints a theme on the Install Themes pages.
 *
 * @param object $theme An object that contains theme data returned by the WordPress.org API.
 */
function display_theme( $theme ) {
	global $wp_list_table;

	return $wp_list_table->single_row( $theme );
}

/**
 * Display theme content based on theme list.
 *
 * @since 2.8.0
 */
function display_themes() {
	global $wp_list_table;

	$wp_list_table->display();
}
add_action('install_themes_search', 'display_themes');
add_action('install_themes_featured', 'display_themes');
add_action('install_themes_new', 'display_themes');
add_action('install_themes_updated', 'display_themes');

/**
 * Display theme information in dialog box form.
 *
 * @since 2.8.0
 */
function install_theme_information() {
	global $tab, $themes_allowedtags, $wp_list_table;

	$theme = themes_api( 'theme_information', array( 'slug' => stripslashes( $_REQUEST['theme'] ) ) );

	if ( is_wp_error( $theme ) )
		wp_die( $theme );

	$wp_list_table->theme_installer_single( $theme );
}
add_action('install_themes_pre_theme-information', 'install_theme_information');
