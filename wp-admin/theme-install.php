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
	wp_die( __( 'Sorry, you are not allowed to install themes on this site.' ) );

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

if ( false === $installed_themes ) {
	$installed_themes = array();
}

foreach ( $installed_themes as $k => $v ) {
	if ( false !== strpos( $k, '/' ) ) {
		unset( $installed_themes[ $k ] );
	}
}

wp_localize_script( 'theme', '_wpThemeSettings', array(
	'themes'   => false,
	'settings' => array(
		'isInstall'  => true,
		'canInstall' => current_user_can( 'install_themes' ),
		'installURI' => current_user_can( 'install_themes' ) ? self_admin_url( 'theme-install.php' ) : null,
		'adminUrl'   => parse_url( self_admin_url(), PHP_URL_PATH )
	),
	'l10n' => array(
		'addNew'              => __( 'Add New Theme' ),
		'search'              => __( 'Search Themes' ),
		'searchPlaceholder'   => __( 'Search themes...' ), // placeholder (no ellipsis)
		'upload'              => __( 'Upload Theme' ),
		'back'                => __( 'Back' ),
		'error'               => sprintf(
			/* translators: %s: support forums URL */
			__( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="%s">support forums</a>.' ),
			__( 'https://wordpress.org/support/' )
		),
		'tryAgain'            => __( 'Try Again' ),
		'themesFound'         => __( 'Number of Themes found: %d' ),
		'noThemesFound'       => __( 'No themes found. Try a different search.' ),
		'collapseSidebar'     => __( 'Collapse Sidebar' ),
		'expandSidebar'       => __( 'Expand Sidebar' ),
		/* translators: accessibility text */
		'selectFeatureFilter' => __( 'Select one or more Theme features to filter by' ),
	),
	'installedThemes' => array_keys( $installed_themes ),
) );

wp_enqueue_script( 'theme' );
wp_enqueue_script( 'updates' );

if ( $tab ) {
	/**
	 * Fires before each of the tabs are rendered on the Install Themes page.
	 *
	 * The dynamic portion of the hook name, `$tab`, refers to the current
	 * theme installation tab. Possible values are 'dashboard', 'search', 'upload',
	 * 'featured', 'new', or 'updated'.
	 *
	 * @since 2.8.0
	 */
	do_action( "install_themes_pre_{$tab}" );
}

$help_overview =
	'<p>' . sprintf(
			/* translators: %s: Theme Directory URL */
			__( 'You can find additional themes for your site by using the Theme Browser/Installer on this screen, which will display themes from the <a href="%s">WordPress Theme Directory</a>. These themes are designed and developed by third parties, are available free of charge, and are compatible with the license WordPress uses.' ),
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
	'<p>' . __('<a href="https://codex.wordpress.org/Using_Themes#Adding_New_Themes">Documentation on Adding New Themes</a>') . '</p>' .
	'<p>' . __('<a href="https://wordpress.org/support/">Support Forums</a>') . '</p>'
);

include(ABSPATH . 'wp-admin/admin-header.php');

?>
<div class="wrap">
	<h1 class="wp-heading-inline"><?php echo esc_html( $title ); ?></h1>

	<?php

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
		echo ' <button type="button" class="upload-view-toggle page-title-action hide-if-no-js" aria-expanded="false">' . __( 'Upload Theme' ) . '</button>';
	}
	?>

	<hr class="wp-header-end">

	<div class="error hide-if-js">
		<p><?php _e( 'The Theme Installer screen requires JavaScript.' ); ?></p>
	</div>

	<div class="upload-theme">
	<?php install_themes_upload(); ?>
	</div>

	<h2 class="screen-reader-text hide-if-no-js"><?php _e( 'Filter themes list' ); ?></h2>

	<div class="wp-filter hide-if-no-js">
		<div class="filter-count">
			<span class="count theme-count"></span>
		</div>

		<ul class="filter-links">
			<li><a href="#" data-sort="featured"><?php _ex( 'Featured', 'themes' ); ?></a></li>
			<li><a href="#" data-sort="popular"><?php _ex( 'Popular', 'themes' ); ?></a></li>
			<li><a href="#" data-sort="new"><?php _ex( 'Latest', 'themes' ); ?></a></li>
			<li><a href="#" data-sort="favorites"><?php _ex( 'Favorites', 'themes' ); ?></a></li>
		</ul>

		<button type="button" class="button drawer-toggle" aria-expanded="false"><?php _e( 'Feature Filter' ); ?></button>

		<form class="search-form"></form>

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
				<input type="button" class="button favorites-form-submit" value="<?php esc_attr_e( 'Get Favorites' ); ?>" />
			</p>
		</div>

		<div class="filter-drawer">
			<div class="buttons">
				<button type="button" class="apply-filters button"><?php _e( 'Apply Filters' ); ?><span></span></button>
				<button type="button" class="clear-filters button" aria-label="<?php esc_attr_e( 'Clear current filters' ); ?>"><?php _e( 'Clear' ); ?></button>
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
			<div class="buttons">
				<button type="button" class="apply-filters button"><?php _e( 'Apply Filters' ); ?><span></span></button>
				<button type="button" class="clear-filters button" aria-label="<?php esc_attr_e( 'Clear current filters' ); ?>"><?php _e( 'Clear' ); ?></button>
			</div>
			<div class="filtered-by">
				<span><?php _e( 'Filtering by:' ); ?></span>
				<div class="tags"></div>
				<button type="button" class="button-link edit-filters"><?php _e( 'Edit Filters' ); ?></button>
			</div>
		</div>
	</div>
	<h2 class="screen-reader-text hide-if-no-js"><?php _e( 'Themes list' ); ?></h2>
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
	 * theme installation tab. Possible values are 'dashboard', 'search', 'upload',
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
	<div class="theme-author">
		<?php
		/* translators: %s: Theme author name */
		printf( __( 'By %s' ), '{{ data.author }}' );
		?>
	</div>

	<div class="theme-id-container">
		<h3 class="theme-name">{{ data.name }}</h3>

		<div class="theme-actions">
			<# if ( data.installed ) { #>
				<?php
				/* translators: %s: Theme name */
				$aria_label = sprintf( _x( 'Activate %s', 'theme' ), '{{ data.name }}' );
				?>
				<# if ( data.activate_url ) { #>
					<a class="button button-primary activate" href="{{ data.activate_url }}" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php _e( 'Activate' ); ?></a>
				<# } #>
				<# if ( data.customize_url ) { #>
					<a class="button load-customize" href="{{ data.customize_url }}"><?php _e( 'Live Preview' ); ?></a>
				<# } else { #>
					<button class="button preview install-theme-preview"><?php _e( 'Preview' ); ?></button>
				<# } #>
			<# } else { #>
				<?php
				/* translators: %s: Theme name */
				$aria_label = sprintf( __( 'Install %s' ), '{{ data.name }}' );
				?>
				<a class="button button-primary theme-install" data-name="{{ data.name }}" data-slug="{{ data.id }}" href="{{ data.install_url }}" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php _e( 'Install' ); ?></a>
				<button class="button preview install-theme-preview"><?php _e( 'Preview' ); ?></button>
			<# } #>
		</div>
	</div>

	<# if ( data.installed ) { #>
		<div class="notice notice-success notice-alt"><p><?php _ex( 'Installed', 'theme' ); ?></p></div>
	<# } #>
</script>

<script id="tmpl-theme-preview" type="text/template">
	<div class="wp-full-overlay-sidebar">
		<div class="wp-full-overlay-header">
			<button class="close-full-overlay"><span class="screen-reader-text"><?php _e( 'Close' ); ?></span></button>
			<button class="previous-theme"><span class="screen-reader-text"><?php _ex( 'Previous', 'Button label for a theme' ); ?></span></button>
			<button class="next-theme"><span class="screen-reader-text"><?php _ex( 'Next', 'Button label for a theme' ); ?></span></button>
			<# if ( data.installed ) { #>
				<a class="button button-primary activate" href="{{ data.activate_url }}"><?php _e( 'Activate' ); ?></a>
			<# } else { #>
				<a href="{{ data.install_url }}" class="button button-primary theme-install" data-name="{{ data.name }}" data-slug="{{ data.id }}"><?php _e( 'Install' ); ?></a>
			<# } #>
		</div>
		<div class="wp-full-overlay-sidebar-content">
			<div class="install-theme-info">
				<h3 class="theme-name">{{ data.name }}</h3>
					<span class="theme-by">
						<?php
						/* translators: %s: Theme author name */
						printf( __( 'By %s' ), '{{ data.author }}' );
						?>
					</span>

					<img class="theme-screenshot" src="{{ data.screenshot_url }}" alt="" />

					<div class="theme-details">
						<# if ( data.rating ) { #>
							<div class="theme-rating">
								{{{ data.stars }}}
								<span class="num-ratings">({{ data.num_ratings }})</span>
							</div>
						<# } else { #>
							<span class="no-rating"><?php _e( 'This theme has not been rated yet.' ); ?></span>
						<# } #>
						<div class="theme-version">
							<?php
							/* translators: %s: Theme version */
							printf( __( 'Version: %s' ), '{{ data.version }}' );
							?>
						</div>
						<div class="theme-description">{{{ data.description }}}</div>
					</div>
				</div>
			</div>
			<div class="wp-full-overlay-footer">
				<button type="button" class="collapse-sidebar button" aria-expanded="true" aria-label="<?php esc_attr_e( 'Collapse Sidebar' ); ?>">
					<span class="collapse-sidebar-arrow"></span>
					<span class="collapse-sidebar-label"><?php _e( 'Collapse' ); ?></span>
				</button>
			</div>
		</div>
		<div class="wp-full-overlay-main">
		<iframe src="{{ data.preview_url }}" title="<?php esc_attr_e( 'Preview' ); ?>"></iframe>
	</div>
</script>

<?php
wp_print_request_filesystem_credentials_modal();
wp_print_admin_notice_templates();

include(ABSPATH . 'wp-admin/admin-footer.php');
