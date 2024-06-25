<?php
/**
 * Install theme administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';
require ABSPATH . 'wp-admin/includes/theme-install.php';

$tab = ! empty( $_REQUEST['tab'] ) ? sanitize_text_field( $_REQUEST['tab'] ) : '';

if ( ! current_user_can( 'install_themes' ) ) {
	wp_die( __( 'Sorry, you are not allowed to install themes on this site.' ) );
}

if ( is_multisite() && ! is_network_admin() ) {
	wp_redirect( network_admin_url( 'theme-install.php' ) );
	exit;
}

// Used in the HTML title tag.
$title       = __( 'Add Themes' );
$parent_file = 'themes.php';

if ( ! is_network_admin() ) {
	$submenu_file = 'themes.php';
}

$installed_themes = search_theme_directories();

if ( false === $installed_themes ) {
	$installed_themes = array();
}

foreach ( $installed_themes as $theme_slug => $theme_data ) {
	// Ignore child themes.
	if ( str_contains( $theme_slug, '/' ) ) {
		unset( $installed_themes[ $theme_slug ] );
	}
}

wp_localize_script(
	'theme',
	'_wpThemeSettings',
	array(
		'themes'          => false,
		'settings'        => array(
			'isInstall'  => true,
			'canInstall' => current_user_can( 'install_themes' ),
			'installURI' => current_user_can( 'install_themes' ) ? self_admin_url( 'theme-install.php' ) : null,
			'adminUrl'   => parse_url( self_admin_url(), PHP_URL_PATH ),
		),
		'l10n'            => array(
			'addNew'              => __( 'Add New Theme' ),
			'search'              => __( 'Search Themes' ),
			'upload'              => __( 'Upload Theme' ),
			'back'                => __( 'Back' ),
			'error'               => sprintf(
				/* translators: %s: Support forums URL. */
				__( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="%s">support forums</a>.' ),
				__( 'https://wordpress.org/support/forums/' )
			),
			'tryAgain'            => __( 'Try Again' ),
			/* translators: %d: Number of themes. */
			'themesFound'         => __( 'Number of Themes found: %d' ),
			'noThemesFound'       => __( 'No themes found. Try a different search.' ),
			'collapseSidebar'     => __( 'Collapse Sidebar' ),
			'expandSidebar'       => __( 'Expand Sidebar' ),
			/* translators: Hidden accessibility text. */
			'selectFeatureFilter' => __( 'Select one or more Theme features to filter by' ),
		),
		'installedThemes' => array_keys( $installed_themes ),
		'activeTheme'     => get_stylesheet(),
	)
);

wp_enqueue_script( 'theme' );
wp_enqueue_script( 'updates' );

if ( $tab ) {
	/**
	 * Fires before each of the tabs are rendered on the Install Themes page.
	 *
	 * The dynamic portion of the hook name, `$tab`, refers to the current
	 * theme installation tab.
	 *
	 * Possible hook names include:
	 *
	 *  - `install_themes_pre_block-themes`
	 *  - `install_themes_pre_dashboard`
	 *  - `install_themes_pre_featured`
	 *  - `install_themes_pre_new`
	 *  - `install_themes_pre_search`
	 *  - `install_themes_pre_updated`
	 *  - `install_themes_pre_upload`
	 *
	 * @since 2.8.0
	 * @since 6.1.0 Added the `install_themes_pre_block-themes` hook name.
	 */
	do_action( "install_themes_pre_{$tab}" );
}

$help_overview =
	'<p>' . sprintf(
		/* translators: %s: Theme Directory URL. */
		__( 'You can find additional themes for your site by using the Theme Browser/Installer on this screen, which will display themes from the <a href="%s">WordPress Theme Directory</a>. These themes are designed and developed by third parties, are available free of charge, and are compatible with the license WordPress uses.' ),
		__( 'https://wordpress.org/themes/' )
	) . '</p>' .
	'<p>' . __( 'You can Search for themes by keyword, author, or tag, or can get more specific and search by criteria listed in the feature filter.' ) . ' <span id="live-search-desc">' . __( 'The search results will be updated as you type.' ) . '</span></p>' .
	'<p>' . __( 'Alternately, you can browse the themes that are Popular or Latest. When you find a theme you like, you can preview it or install it.' ) . '</p>' .
	'<p>' . sprintf(
		/* translators: %s: /wp-content/themes */
		__( 'You can Upload a theme manually if you have already downloaded its ZIP archive onto your computer (make sure it is from a trusted and original source). You can also do it the old-fashioned way and copy a downloaded theme&#8217;s folder via FTP into your %s directory.' ),
		'<code>/wp-content/themes</code>'
	) . '</p>';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( 'Overview' ),
		'content' => $help_overview,
	)
);

$help_installing =
	'<p>' . __( 'Once you have generated a list of themes, you can preview and install any of them. Click on the thumbnail of the theme you are interested in previewing. It will open up in a full-screen Preview page to give you a better idea of how that theme will look.' ) . '</p>' .
	'<p>' . __( 'To install the theme so you can preview it with your site&#8217;s content and customize its theme options, click the "Install" button at the top of the left-hand pane. The theme files will be downloaded to your website automatically. When this is complete, the theme is now available for activation, which you can do by clicking the "Activate" link, or by navigating to your Manage Themes screen and clicking the "Live Preview" link under any installed theme&#8217;s thumbnail image.' ) . '</p>';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'installing',
		'title'   => __( 'Previewing and Installing' ),
		'content' => $help_installing,
	)
);

// Help tab: Block themes.
$help_block_themes =
	'<p>' . __( 'A block theme is a theme that uses blocks for all parts of a site including navigation menus, header, content, and site footer. These themes are built for the features that allow you to edit and customize all parts of your site.' ) . '</p>' .
	'<p>' . __( 'With a block theme, you can place and edit blocks without affecting your content by customizing or creating new templates.' ) . '</p>';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'block_themes',
		'title'   => __( 'Block themes' ),
		'content' => $help_block_themes,
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://wordpress.org/documentation/article/appearance-themes-screen/#install-themes">Documentation on Adding New Themes</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://wordpress.org/documentation/article/block-themes/">Documentation on Block Themes</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/forums/">Support forums</a>' ) . '</p>'
);

require_once ABSPATH . 'wp-admin/admin-header.php';

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
	 * @param string[] $tabs Associative array of the tabs shown on the Add Themes screen. Default is 'upload'.
	 */
	$tabs = apply_filters( 'install_themes_tabs', array( 'upload' => __( 'Upload Theme' ) ) );
	if ( ! empty( $tabs['upload'] ) && current_user_can( 'upload_themes' ) ) {
		echo ' <button type="button" class="upload-view-toggle page-title-action hide-if-no-js" aria-expanded="false">' . __( 'Upload Theme' ) . '</button>';
	}
	?>

	<hr class="wp-header-end">

	<?php
	wp_admin_notice(
		__( 'The Theme Installer screen requires JavaScript.' ),
		array(
			'additional_classes' => array( 'error', 'hide-if-js' ),
		)
	);
	?>

	<div class="upload-theme">
	<?php install_themes_upload(); ?>
	</div>

	<h2 class="screen-reader-text hide-if-no-js">
		<?php
		/* translators: Hidden accessibility text. */
		_e( 'Filter themes list' );
		?>
	</h2>

	<div class="wp-filter hide-if-no-js">
		<div class="filter-count">
			<span class="count theme-count"></span>
		</div>

		<ul class="filter-links">
			<li><a href="#" data-sort="popular"><?php _ex( 'Popular', 'themes' ); ?></a></li>
			<li><a href="#" data-sort="new"><?php _ex( 'Latest', 'themes' ); ?></a></li>
			<li><a href="#" data-sort="block-themes"><?php _ex( 'Block Themes', 'themes' ); ?></a></li>
			<li><a href="#" data-sort="favorites"><?php _ex( 'Favorites', 'themes' ); ?></a></li>
		</ul>

		<button type="button" class="button drawer-toggle" aria-expanded="false"><?php _e( 'Feature Filter' ); ?></button>

		<form class="search-form"><p class="search-box"></p></form>

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
		// Use the core list, rather than the .org API, due to inconsistencies
		// and to ensure tags are translated.
		$feature_list = get_theme_feature_list( false );

		foreach ( $feature_list as $feature_group => $features ) {
			echo '<fieldset class="filter-group">';
			echo '<legend>' . esc_html( $feature_group ) . '</legend>';
			echo '<div class="filter-group-feature">';
			foreach ( $features as $feature => $feature_name ) {
				$feature = esc_attr( $feature );
				echo '<input type="checkbox" id="filter-id-' . $feature . '" value="' . $feature . '" /> ';
				echo '<label for="filter-id-' . $feature . '">' . esc_html( $feature_name ) . '</label>';
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
	<h2 class="screen-reader-text hide-if-no-js">
		<?php
		/* translators: Hidden accessibility text. */
		_e( 'Themes list' );
		?>
	</h2>
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
	 * theme installation tab.
	 *
	 * Possible hook names include:
	 *
	 *  - `install_themes_block-themes`
	 *  - `install_themes_dashboard`
	 *  - `install_themes_featured`
	 *  - `install_themes_new`
	 *  - `install_themes_search`
	 *  - `install_themes_updated`
	 *  - `install_themes_upload`
	 *
	 * @since 2.8.0
	 * @since 6.1.0 Added the `install_themes_block-themes` hook name.
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
			<img src="{{ data.screenshot_url }}?ver={{ data.version }}" alt="" />
		</div>
	<# } else { #>
		<div class="theme-screenshot blank"></div>
	<# } #>

	<# if ( data.installed ) { #>
		<?php
		wp_admin_notice(
			_x( 'Installed', 'theme' ),
			array(
				'type'               => 'success',
				'additional_classes' => array( 'notice-alt' ),
			)
		);
		?>
	<# } #>

	<# if ( ! data.compatible_wp || ! data.compatible_php ) { #>
		<div class="notice notice-error notice-alt"><p>
			<# if ( ! data.compatible_wp && ! data.compatible_php ) { #>
				<?php
				_e( 'This theme does not work with your versions of WordPress and PHP.' );
				if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
					printf(
						/* translators: 1: URL to WordPress Updates screen, 2: URL to Update PHP page. */
						' ' . __( '<a href="%1$s">Please update WordPress</a>, and then <a href="%2$s">learn more about updating PHP</a>.' ),
						self_admin_url( 'update-core.php' ),
						esc_url( wp_get_update_php_url() )
					);
					wp_update_php_annotation( '</p><p><em>', '</em>' );
				} elseif ( current_user_can( 'update_core' ) ) {
					printf(
						/* translators: %s: URL to WordPress Updates screen. */
						' ' . __( '<a href="%s">Please update WordPress</a>.' ),
						self_admin_url( 'update-core.php' )
					);
				} elseif ( current_user_can( 'update_php' ) ) {
					printf(
						/* translators: %s: URL to Update PHP page. */
						' ' . __( '<a href="%s">Learn more about updating PHP</a>.' ),
						esc_url( wp_get_update_php_url() )
					);
					wp_update_php_annotation( '</p><p><em>', '</em>' );
				}
				?>
			<# } else if ( ! data.compatible_wp ) { #>
				<?php
				_e( 'This theme does not work with your version of WordPress.' );
				if ( current_user_can( 'update_core' ) ) {
					printf(
						/* translators: %s: URL to WordPress Updates screen. */
						' ' . __( '<a href="%s">Please update WordPress</a>.' ),
						self_admin_url( 'update-core.php' )
					);
				}
				?>
			<# } else if ( ! data.compatible_php ) { #>
				<?php
				_e( 'This theme does not work with your version of PHP.' );
				if ( current_user_can( 'update_php' ) ) {
					printf(
						/* translators: %s: URL to Update PHP page. */
						' ' . __( '<a href="%s">Learn more about updating PHP</a>.' ),
						esc_url( wp_get_update_php_url() )
					);
					wp_update_php_annotation( '</p><p><em>', '</em>' );
				}
				?>
			<# } #>
		</p></div>
	<# } #>

	<span class="more-details"><?php _ex( 'Details &amp; Preview', 'theme' ); ?></span>
	<div class="theme-author">
		<?php
		/* translators: %s: Theme author name. */
		printf( __( 'By %s' ), '{{ data.author }}' );
		?>
	</div>

	<div class="theme-id-container">
		<h3 class="theme-name">{{ data.name }}</h3>

		<div class="theme-actions">
			<# if ( data.installed ) { #>
				<# if ( data.compatible_wp && data.compatible_php ) { #>
					<?php
					/* translators: %s: Theme name. */
					$aria_label = sprintf( _x( 'Activate %s', 'theme' ), '{{ data.name }}' );
					?>
					<# if ( data.activate_url ) { #>
						<# if ( ! data.active ) { #>
							<a class="button button-primary activate" href="{{ data.activate_url }}" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php _e( 'Activate' ); ?></a>
						<# } else { #>
							<button class="button button-primary disabled"><?php _ex( 'Activated', 'theme' ); ?></button>
						<# } #>
					<# } #>
					<# if ( data.customize_url ) { #>
						<# if ( ! data.active ) { #>
							<# if ( ! data.block_theme ) { #>
								<a class="button load-customize" href="{{ data.customize_url }}"><?php _e( 'Live Preview' ); ?></a>
							<# } #>
						<# } else { #>
							<a class="button load-customize" href="{{ data.customize_url }}"><?php _e( 'Customize' ); ?></a>
						<# } #>
					<# } else { #>
						<button class="button preview install-theme-preview"><?php _e( 'Preview' ); ?></button>
					<# } #>
				<# } else { #>
					<?php
					/* translators: %s: Theme name. */
					$aria_label = sprintf( _x( 'Cannot Activate %s', 'theme' ), '{{ data.name }}' );
					?>
					<# if ( data.activate_url ) { #>
						<a class="button button-primary disabled" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php _ex( 'Cannot Activate', 'theme' ); ?></a>
					<# } #>
					<# if ( data.customize_url ) { #>
						<a class="button disabled"><?php _e( 'Live Preview' ); ?></a>
					<# } else { #>
						<button class="button disabled"><?php _e( 'Preview' ); ?></button>
					<# } #>
				<# } #>
			<# } else { #>
				<# if ( data.compatible_wp && data.compatible_php ) { #>
					<?php
					/* translators: %s: Theme name. */
					$aria_label = sprintf( _x( 'Install %s', 'theme' ), '{{ data.name }}' );
					?>
					<a class="button button-primary theme-install" data-name="{{ data.name }}" data-slug="{{ data.id }}" href="{{ data.install_url }}" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php _e( 'Install' ); ?></a>
					<button class="button preview install-theme-preview"><?php _e( 'Preview' ); ?></button>
				<# } else { #>
					<?php
					/* translators: %s: Theme name. */
					$aria_label = sprintf( _x( 'Cannot Install %s', 'theme' ), '{{ data.name }}' );
					?>
					<a class="button button-primary disabled" data-name="{{ data.name }}" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php _ex( 'Cannot Install', 'theme' ); ?></a>
					<button class="button disabled"><?php _e( 'Preview' ); ?></button>
				<# } #>
			<# } #>
		</div>
	</div>
</script>

<script id="tmpl-theme-preview" type="text/template">
	<div class="wp-full-overlay-sidebar">
		<div class="wp-full-overlay-header">
			<button class="close-full-overlay"><span class="screen-reader-text">
				<?php
				/* translators: Hidden accessibility text. */
				_e( 'Close' );
				?>
			</span></button>
			<button class="previous-theme"><span class="screen-reader-text">
				<?php
				/* translators: Hidden accessibility text. */
				_e( 'Previous theme' );
				?>
			</span></button>
			<button class="next-theme"><span class="screen-reader-text">
				<?php
				/* translators: Hidden accessibility text. */
				_e( 'Next theme' );
				?>
			</span></button>
			<# if ( data.installed ) { #>
				<# if ( data.compatible_wp && data.compatible_php ) { #>
					<?php
					/* translators: %s: Theme name. */
					$aria_label = sprintf( _x( 'Activate %s', 'theme' ), '{{ data.name }}' );
					?>
					<# if ( ! data.active ) { #>
						<a class="button button-primary activate" href="{{ data.activate_url }}" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php _e( 'Activate' ); ?></a>
					<# } else { #>
						<button class="button button-primary disabled"><?php _ex( 'Activated', 'theme' ); ?></button>
					<# } #>
				<# } else { #>
					<a class="button button-primary disabled" ><?php _ex( 'Cannot Activate', 'theme' ); ?></a>
				<# } #>
			<# } else { #>
				<# if ( data.compatible_wp && data.compatible_php ) { #>
					<a href="{{ data.install_url }}" class="button button-primary theme-install" data-name="{{ data.name }}" data-slug="{{ data.id }}"><?php _e( 'Install' ); ?></a>
				<# } else { #>
					<a class="button button-primary disabled" ><?php _ex( 'Cannot Install', 'theme' ); ?></a>
				<# } #>
			<# } #>
		</div>
		<div class="wp-full-overlay-sidebar-content">
			<div class="install-theme-info">
				<h3 class="theme-name">{{ data.name }}</h3>
					<span class="theme-by">
						<?php
						/* translators: %s: Theme author name. */
						printf( __( 'By %s' ), '{{ data.author }}' );
						?>
					</span>

					<div class="theme-screenshot">
						<img class="theme-screenshot" src="{{ data.screenshot_url }}?ver={{ data.version }}" alt="" />
					</div>

					<div class="theme-details">
						<# if ( data.rating ) { #>
							<div class="theme-rating">
								{{{ data.stars }}}
								<a class="num-ratings" href="{{ data.reviews_url }}">
									<?php
									/* translators: %s: Number of ratings. */
									printf( __( '(%s ratings)' ), '{{ data.num_ratings }}' );
									?>
								</a>
							</div>
						<# } else { #>
							<span class="no-rating"><?php _e( 'This theme has not been rated yet.' ); ?></span>
						<# } #>

						<div class="theme-version">
							<?php
							/* translators: %s: Theme version. */
							printf( __( 'Version: %s' ), '{{ data.version }}' );
							?>
						</div>

						<# if ( ! data.compatible_wp || ! data.compatible_php ) { #>
							<div class="notice notice-error notice-alt notice-large"><p>
								<# if ( ! data.compatible_wp && ! data.compatible_php ) { #>
									<?php
									_e( 'This theme does not work with your versions of WordPress and PHP.' );
									if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
										printf(
											/* translators: 1: URL to WordPress Updates screen, 2: URL to Update PHP page. */
											' ' . __( '<a href="%1$s">Please update WordPress</a>, and then <a href="%2$s">learn more about updating PHP</a>.' ),
											self_admin_url( 'update-core.php' ),
											esc_url( wp_get_update_php_url() )
										);
										wp_update_php_annotation( '</p><p><em>', '</em>' );
									} elseif ( current_user_can( 'update_core' ) ) {
										printf(
											/* translators: %s: URL to WordPress Updates screen. */
											' ' . __( '<a href="%s">Please update WordPress</a>.' ),
											self_admin_url( 'update-core.php' )
										);
									} elseif ( current_user_can( 'update_php' ) ) {
										printf(
											/* translators: %s: URL to Update PHP page. */
											' ' . __( '<a href="%s">Learn more about updating PHP</a>.' ),
											esc_url( wp_get_update_php_url() )
										);
										wp_update_php_annotation( '</p><p><em>', '</em>' );
									}
									?>
								<# } else if ( ! data.compatible_wp ) { #>
									<?php
									_e( 'This theme does not work with your version of WordPress.' );
									if ( current_user_can( 'update_core' ) ) {
										printf(
											/* translators: %s: URL to WordPress Updates screen. */
											' ' . __( '<a href="%s">Please update WordPress</a>.' ),
											self_admin_url( 'update-core.php' )
										);
									}
									?>
								<# } else if ( ! data.compatible_php ) { #>
									<?php
									_e( 'This theme does not work with your version of PHP.' );
									if ( current_user_can( 'update_php' ) ) {
										printf(
											/* translators: %s: URL to Update PHP page. */
											' ' . __( '<a href="%s">Learn more about updating PHP</a>.' ),
											esc_url( wp_get_update_php_url() )
										);
										wp_update_php_annotation( '</p><p><em>', '</em>' );
									}
									?>
								<# } #>
							</p></div>
						<# } #>

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

require_once ABSPATH . 'wp-admin/admin-footer.php';
