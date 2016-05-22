<?php
/**
 * Install theme administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );
require( ABSPATH . 'wp-admin/includes/theme-install.php' );

wp_reset_vars( array( 'tab' ) );

if ( ! current_user_can('install_themes') )
	wp_die( __( 'You do not have sufficient permissions to install themes on this site.' ) );

if ( is_multisite() && ! is_network_admin() ) {
	wp_redirect( network_admin_url( 'theme-install.php' ) );
	exit();
}

$title = __( 'Add Themes' );
$parent_file = 'themes.php';

if ( ! is_network_admin() ) {
	$submenu_file = 'themes.php';
}

$installed_themes = search_theme_directories();
foreach ( $installed_themes as $k => $v ) {
	if ( false !== strpos( $k, '/' ) ) {
		unset( $installed_themes[ $k ] );
	}
}

wp_localize_script( 'theme', '_wpThemeSettings', array(
	'themes'   => false,
	'settings' => array(
		'isInstall'     => true,
		'canInstall'    => current_user_can( 'install_themes' ),
		'installURI'    => current_user_can( 'install_themes' ) ? self_admin_url( 'theme-install.php' ) : null,
		'adminUrl'      => parse_url( self_admin_url(), PHP_URL_PATH )
	),
	'l10n' => array(
		'addNew' => __( 'Add New Theme' ),
		'search' => __( 'Search Themes' ),
		'searchPlaceholder' => __( 'Search themes...' ), // placeholder (no ellipsis)
		'upload' => __( 'Upload Theme' ),
		'back'   => __( 'Back' ),
		'error'  => __( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="https://wordpress.org/support/">support forums</a>.' ),
		'themesFound'   => __( 'Number of Themes found: %d' ),
		'noThemesFound' => __( 'No themes found. Try a different search.' ),
		'collapseSidebar'    => __( 'Collapse Sidebar' ),
		'expandSidebar'      => __( 'Expand Sidebar' ),
	),
	'installedThemes' => array_keys( $installed_themes ),
) );

wp_enqueue_script( 'theme' );

if ( $tab ) {
	/**
	 * Fires before each of the tabs are rendered on the Install Themes page.
	 *
	 * The dynamic portion of the hook name, `$tab`, refers to the current
	 * theme install tab. Possible values are 'dashboard', 'search', 'upload',
	 * 'featured', 'new', or 'updated'.
	 *
	 * @since 2.8.0
	 */
	do_action( "install_themes_pre_{$tab}" );
}

$help_overview =
	'<p>' . sprintf(
			/* translators: %s: Theme Directory URL */
			__( 'You can find additional themes for your site by using the Theme Browser/Installer on this screen, which will display themes from the <a href="%s" target="_blank">WordPress Theme Directory</a>. These themes are designed and developed by third parties, are available free of charge, and are compatible with the license WordPress uses.' ),
			__( 'https://wordpress.org/themes/' )
		) . '</p>' .
	'<p>' . __( 'You can Search for themes by keyword, author, or tag, or can get more specific and search by criteria listed in the feature filter.' ) . ' <span id="live-search-desc">' . __( 'The search results will be updated as you type.' ) . '</span></p>' .
	'<p>' . __( 'Alternately, you can browse the themes that are Featured, Popular, or Latest. When you find a theme you like, you can preview it or install it.' ) . '</p>' .
	'<p>' . sprintf(
			/* translators: %s: /wp-content/themes */
			__( 'You can Upload a theme manually if you have already downloaded its ZIP archive onto your computer (make sure it is from a trusted and original source). You can also do it the old-fashioned way and copy a downloaded theme&#8217;s folder via FTP into your %s directory.' ),
			'<code>/wp-content/themes</code>'
		) . '</p>';

get_current_screen()->add_help_tab( array(
	'id'      => 'overview',
	'title'   => __('Overview'),
	'content' => $help_overview
) );

$help_installing =
	'<p>' . __('Once you have generated a list of themes, you can preview and install any of them. Click on the thumbnail of the theme you&#8217;re interested in previewing. It will open up in a full-screen Preview page to give you a better idea of how that theme will look.') . '</p>' .
	'<p>' . __('To install the theme so you can preview it with your site&#8217;s content and customize its theme options, click the "Install" button at the top of the left-hand pane. The theme files will be downloaded to your website automatically. When this is complete, the theme is now available for activation, which you can do by clicking the "Activate" link, or by navigating to your Manage Themes screen and clicking the "Live Preview" link under any installed theme&#8217;s thumbnail image.') . '</p>';

get_current_screen()->add_help_tab( array(
	'id'      => 'installing',
	'title'   => __('Previewing and Installing'),
	'content' => $help_installing
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="https://codex.wordpress.org/Using_Themes#Adding_New_Themes" target="_blank">Documentation on Adding New Themes</a>') . '</p>' .
	'<p>' . __('<a href="https://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

include(ABSPATH . 'wp-admin/admin-header.php');

?>
<div class="wrap">
	<h1><?php
	echo esc_html( $title );

	/**
	 * Filters the tabs shown on the Add Themes screen.
	 *
	 * This filter is for backward compatibility only, for the suppression of the upload tab.
	 *
	 * @since 2.8.0
	 *
	 * @param array $tabs The tabs shown on the Add Themes screen. Default is 'upload'.
	 */
	$tabs = apply_filters( 'install_themes_tabs', array( 'upload' => __( 'Upload Theme' ) ) );
	if ( ! empty( $tabs['upload'] ) && current_user_can( 'upload_themes' ) ) {
		echo ' <a href="#" class="upload-view-toggle page-title-action">' . __( 'Upload Theme' ) . '</a>';
	}
	?></h1>

	<div class="upload-theme">
	<?php install_themes_upload(); ?>
	</div>

	<h2 class="screen-reader-text"><?php _e( 'Filter themes list' ); ?></h2>

	<div class="wp-filter">
		<div class="filter-count">
			<span class="count theme-count"></span>
		</div>

		<ul class="filter-links">
			<li><a href="#" data-sort="featured"><?php _ex( 'Featured', 'themes' ); ?></a></li>
			<li><a href="#" data-sort="popular"><?php _ex( 'Popular', 'themes' ); ?></a></li>
			<li><a href="#" data-sort="new"><?php _ex( 'Latest', 'themes' ); ?></a></li>
			<li><a href="#" data-sort="favorites"><?php _ex( 'Favorites', 'themes' ); ?></a></li>
		</ul>

		<a class="drawer-toggle" href="#"><?php _e( 'Feature Filter' ); ?></a>

		<div class="search-form"></div>

		<div class="favorites-form">
			<?php
			$action = 'save_wporg_username_' . get_current_user_id();
			if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ), $action ) ) {
				$user = isset( $_GET['user'] ) ? wp_unslash( $_GET['user'] ) : get_user_option( 'wporg_favorites' );
				update_user_meta( get_current_user_id(), 'wporg_favorites', $user );
			} else {
				$user = get_user_option( 'wporg_favorites' );
			}
			?>
			<p class="install-help"><?php _e( 'If you have marked themes as favorites on WordPress.org, you can browse them here.' ); ?></p>

			<p>
				<label for="wporg-username-input"><?php _e( 'Your WordPress.org username:' ); ?></label>
				<input type="hidden" id="wporg-username-nonce" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( $action ) ); ?>" />
				<input type="search" id="wporg-username-input" value="<?php echo esc_attr( $user ); ?>" />
				<input type="button" class="button button-secondary favorites-form-submit" value="<?php esc_attr_e( 'Get Favorites' ); ?>" />
			</p>
		</div>

		<div class="filter-drawer">
			<div class="buttons">
				<a class="apply-filters button button-secondary" href="#"><?php _e( 'Apply Filters' ); ?><span></span></a>
				<a class="clear-filters button button-secondary" href="#"><?php _e( 'Clear' ); ?></a>
			</div>
		<?php
		$feature_list = get_theme_feature_list();
		foreach ( $feature_list as $feature_name => $features ) {
			echo '<fieldset class="filter-group">';
			$feature_name = esc_html( $feature_name );
			echo '<legend>' . $feature_name . '</legend>';
			echo '<div class="filter-group-feature">';
			foreach ( $features as $feature => $feature_name ) {
				$feature = esc_attr( $feature );
				echo '<input type="checkbox" id="filter-id-' . $feature . '" value="' . $feature . '" /> ';
				echo '<label for="filter-id-' . $feature . '">' . $feature_name . '</label><br>';
			}
			echo '</div>';
			echo '</fieldset>';
		}
		?>
			<div class="filtered-by">
				<span><?php _e( 'Filtering by:' ); ?></span>
				<div class="tags"></div>
				<a href="#"><?php _e( 'Edit' ); ?></a>
			</div>
		</div>
	</div>
	<h2 class="screen-reader-text"><?php _e( 'Themes list' ); ?></h2>
	<div class="theme-browser content-filterable"></div>
	<div class="theme-install-overlay wp-full-overlay expanded"></div>

	<p class="no-themes"><?php _e( 'No themes found. Try a different search.' ); ?></p>
	<span class="spinner"></span>

<?php
if ( $tab ) {
	/**
	 * Fires at the top of each of the tabs on the Install Themes page.
	 *
	 * The dynamic portion of the hook name, `$tab`, refers to the current
	 * theme install tab. Possible values are 'dashboard', 'search', 'upload',
	 * 'featured', 'new', or 'updated'.
	 *
	 * @since 2.8.0
	 *
	 * @param int $paged Number of the current page of results being viewed.
	 */
	do_action( "install_themes_{$tab}", $paged );
}
?>
</div>

<script id="tmpl-theme" type="text/template">
	<# if ( data.screenshot_url ) { #>
		<div class="theme-screenshot">
			<img src="{{ data.screenshot_url }}" alt="" />
		</div>
	<# } else { #>
		<div class="theme-screenshot blank"></div>
	<# } #>
	<span class="more-details"><?php _ex( 'Details &amp; Preview', 'theme' ); ?></span>
	<div class="theme-author"><?php printf( __( 'By %s' ), '{{ data.author }}' ); ?></div>
	<h3 class="theme-name">{{ data.name }}</h3>

	<div class="theme-actions">
		<a class="button button-primary" href="{{ data.install_url }}"><?php esc_html_e( 'Install' ); ?></a>
		<a class="button button-secondary preview install-theme-preview" href="#"><?php esc_html_e( 'Preview' ); ?></a>
	</div>

	<# if ( data.installed ) { #>
		<div class="theme-installed"><?php _ex( 'Already Installed', 'theme' ); ?></div>
	<# } #>
</script>

<script id="tmpl-theme-preview" type="text/template">
	<div class="wp-full-overlay-sidebar">
		<div class="wp-full-overlay-header">
			<a href="#" class="close-full-overlay"><span class="screen-reader-text"><?php _e( 'Close' ); ?></span></a>
			<a href="#" class="previous-theme"><span class="screen-reader-text"><?php _ex( 'Previous', 'Button label for a theme' ); ?></span></a>
			<a href="#" class="next-theme"><span class="screen-reader-text"><?php _ex( 'Next', 'Button label for a theme' ); ?></span></a>
		<# if ( data.installed ) { #>
			<a href="#" class="button button-primary theme-install disabled"><?php _ex( 'Installed', 'theme' ); ?></a>
		<# } else { #>
			<a href="{{ data.install_url }}" class="button button-primary theme-install"><?php _e( 'Install' ); ?></a>
		<# } #>
		</div>
		<div class="wp-full-overlay-sidebar-content">
			<div class="install-theme-info">
				<h3 class="theme-name">{{ data.name }}</h3>
				<span class="theme-by"><?php printf( __( 'By %s' ), '{{ data.author }}' ); ?></span>

				<img class="theme-screenshot" src="{{ data.screenshot_url }}" alt="" />

				<div class="theme-details">
					<# if ( data.rating ) { #>
						<div class="theme-rating">
							{{{ data.stars }}}
							<span class="num-ratings" aria-hidden="true">({{ data.num_ratings }})</span>
						</div>
					<# } else { #>
						<span class="no-rating"><?php _e( 'This theme has not been rated yet.' ); ?></span>
					<# } #>
					<div class="theme-version"><?php printf( __( 'Version: %s' ), '{{ data.version }}' ); ?></div>
					<div class="theme-description">{{{ data.description }}}</div>
				</div>
			</div>
		</div>
		<div class="wp-full-overlay-footer">
			<div class="devices">
				<button type="button" class="preview-desktop active" aria-pressed="true" data-device="desktop"><span class="screen-reader-text"><?php _e( 'Enter desktop preview mode' ); ?></span></button>
				<button type="button" class="preview-tablet" aria-pressed="false" data-device="tablet"><span class="screen-reader-text"><?php _e( 'Enter tablet preview mode' ); ?></span></button>
				<button type="button" class="preview-mobile" aria-pressed="false" data-device="mobile"><span class="screen-reader-text"><?php _e( 'Enter mobile preview mode' ); ?></span></button>
			</div>
			<button type="button" class="collapse-sidebar button-secondary" aria-expanded="true" aria-label="<?php esc_attr_e( 'Collapse Sidebar' ); ?>">
				<span class="collapse-sidebar-arrow"></span>
				<span class="collapse-sidebar-label"><?php _e( 'Collapse' ); ?></span>
			</button>
		</div>
	</div>
	<div class="wp-full-overlay-main">
		<iframe src="{{ data.preview_url }}" title="<?php esc_attr_e( 'Preview' ); ?>" />
	</div>
</script>

<?php
include(ABSPATH . 'wp-admin/admin-footer.php');
