<?php
/**
 * Multisite themes administration panel.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.1.0
 */

/** Load WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

if ( ! is_multisite() )
	wp_die( __( 'Multisite support is not enabled.' ) );

if ( !current_user_can('manage_network_themes') )
	wp_die( __( 'You do not have sufficient permissions to manage network themes.' ) );

$wp_list_table = _get_list_table('WP_MS_Themes_List_Table');
$pagenum = $wp_list_table->get_pagenum();

$action = $wp_list_table->current_action();

$s = isset($_REQUEST['s']) ? $_REQUEST['s'] : '';

// Clean up request URI from temporary args for screen options/paging uri's to work as expected.
$temp_args = array( 'enabled', 'disabled', 'deleted', 'error' );
$_SERVER['REQUEST_URI'] = remove_query_arg( $temp_args, $_SERVER['REQUEST_URI'] );
$referer = remove_query_arg( $temp_args, wp_get_referer() );

if ( $action ) {
	$allowed_themes = get_site_option( 'allowedthemes' );
	switch ( $action ) {
		case 'enable':
			check_admin_referer('enable-theme_' . $_GET['theme']);
			$allowed_themes[ $_GET['theme'] ] = true;
			update_site_option( 'allowedthemes', $allowed_themes );
			if ( false === strpos( $referer, '/network/themes.php' ) )
				wp_redirect( network_admin_url( 'themes.php?enabled=1' ) );
			else
				wp_safe_redirect( add_query_arg( 'enabled', 1, $referer ) );
			exit;
		case 'disable':
			check_admin_referer('disable-theme_' . $_GET['theme']);
			unset( $allowed_themes[ $_GET['theme'] ] );
			update_site_option( 'allowedthemes', $allowed_themes );
			wp_safe_redirect( add_query_arg( 'disabled', '1', $referer ) );
			exit;
		case 'enable-selected':
			check_admin_referer('bulk-themes');
			$themes = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
			if ( empty($themes) ) {
				wp_safe_redirect( add_query_arg( 'error', 'none', $referer ) );
				exit;
			}
			foreach( (array) $themes as $theme )
				$allowed_themes[ $theme ] = true;
			update_site_option( 'allowedthemes', $allowed_themes );
			wp_safe_redirect( add_query_arg( 'enabled', count( $themes ), $referer ) );
			exit;
		case 'disable-selected':
			check_admin_referer('bulk-themes');
			$themes = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
			if ( empty($themes) ) {
				wp_safe_redirect( add_query_arg( 'error', 'none', $referer ) );
				exit;
			}
			foreach( (array) $themes as $theme )
				unset( $allowed_themes[ $theme ] );
			update_site_option( 'allowedthemes', $allowed_themes );
			wp_safe_redirect( add_query_arg( 'disabled', count( $themes ), $referer ) );
			exit;
		case 'update-selected' :
			check_admin_referer( 'bulk-themes' );

			if ( isset( $_GET['themes'] ) )
				$themes = explode( ',', $_GET['themes'] );
			elseif ( isset( $_POST['checked'] ) )
				$themes = (array) $_POST['checked'];
			else
				$themes = array();

			$title = __( 'Update Themes' );
			$parent_file = 'themes.php';

			require_once(ABSPATH . 'wp-admin/admin-header.php');

			echo '<div class="wrap">';
			echo '<h2>' . esc_html( $title ) . '</h2>';

			$url = self_admin_url('update.php?action=update-selected-themes&amp;themes=' . urlencode( join(',', $themes) ));
			$url = wp_nonce_url($url, 'bulk-update-themes');

			echo "<iframe src='$url' style='width: 100%; height:100%; min-height:850px;'></iframe>";
			echo '</div>';
			require_once(ABSPATH . 'wp-admin/admin-footer.php');
			exit;
		case 'delete-selected':
			if ( ! current_user_can( 'delete_themes' ) )
				wp_die( __('You do not have sufficient permissions to delete themes for this site.') );
			check_admin_referer( 'bulk-themes' );

			$themes = isset( $_REQUEST['checked'] ) ? (array) $_REQUEST['checked'] : array();

			unset( $themes[ get_option( 'stylesheet' ) ], $themes[ get_option( 'template' ) ] );

			if ( empty( $themes ) ) {
				wp_safe_redirect( add_query_arg( 'error', 'none', $referer ) );
				exit;
			}

			$files_to_delete = $theme_info = array();
			foreach ( $themes as $key => $theme ) {
				$theme_info[ $theme ] = wp_get_theme( $theme );
				$files_to_delete = array_merge( $files_to_delete, list_files( $theme_info[ $theme ]->get_stylesheet_directory() ) );
			}

			if ( empty( $themes ) ) {
				wp_safe_redirect( add_query_arg( 'error', 'main', $referer ) );
				exit;
			}

			include(ABSPATH . 'wp-admin/update.php');

			$parent_file = 'themes.php';

			if ( ! isset( $_REQUEST['verify-delete'] ) ) {
				wp_enqueue_script( 'jquery' );
				require_once( ABSPATH . 'wp-admin/admin-header.php' );
				?>
			<div class="wrap">
				<?php
					$themes_to_delete = count( $themes );
					echo '<h2>' . _n( 'Delete Theme', 'Delete Themes', $themes_to_delete ) . '</h2>';
				?>
				<div class="error"><p><strong><?php _e( 'Caution:' ); ?></strong> <?php echo _n( 'This theme may be active on other sites in the network.', 'These themes may be active on other sites in the network.', $themes_to_delete ); ?></p></div>
				<p><?php echo _n( 'You are about to remove the following theme:', 'You are about to remove the following themes:', $themes_to_delete ); ?></p>
					<ul class="ul-disc">
						<?php foreach ( $theme_info as $theme )
							echo '<li>', sprintf( __('<strong>%1$s</strong> by <em>%2$s</em>' ), $theme->display('Name'), $theme->display('Author') ), '</li>'; /* translators: 1: theme name, 2: theme author */ ?>
					</ul>
				<p><?php _e('Are you sure you wish to delete these themes?'); ?></p>
				<form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" style="display:inline;">
					<input type="hidden" name="verify-delete" value="1" />
					<input type="hidden" name="action" value="delete-selected" />
					<?php
						foreach ( (array) $themes as $theme )
							echo '<input type="hidden" name="checked[]" value="' . esc_attr($theme) . '" />';
					?>
					<?php wp_nonce_field('bulk-themes') ?>
					<?php submit_button( _n( 'Yes, Delete this theme', 'Yes, Delete these themes', $themes_to_delete ), 'button', 'submit', false ); ?>
				</form>
				<form method="post" action="<?php echo esc_url(wp_get_referer()); ?>" style="display:inline;">
					<?php submit_button( __( 'No, Return me to the theme list' ), 'button', 'submit', false ); ?>
				</form>

				<p><a href="#" onclick="jQuery('#files-list').toggle(); return false;"><?php _e('Click to view entire list of files which will be deleted'); ?></a></p>
				<div id="files-list" style="display:none;">
					<ul class="code">
					<?php
						foreach ( (array) $files_to_delete as $file )
							echo '<li>' . esc_html( str_replace( WP_CONTENT_DIR . "/themes", '', $file) ) . '</li>';
					?>
					</ul>
				</div>
			</div>
				<?php
				require_once(ABSPATH . 'wp-admin/admin-footer.php');
				exit;
			} // Endif verify-delete

			foreach ( $themes as $theme ) {
				$delete_result = delete_theme( $theme, esc_url( add_query_arg( array(
					'verify-delete' => 1,
					'action' => 'delete-selected',
					'checked' => $_REQUEST['checked'],
					'_wpnonce' => $_REQUEST['_wpnonce']
				), network_admin_url( 'themes.php' ) ) ) );
			}

			$paged = ( $_REQUEST['paged'] ) ? $_REQUEST['paged'] : 1;
			wp_redirect( add_query_arg( array(
				'deleted' => count( $themes ),
				'paged' => $paged,
				's' => $s
			), network_admin_url( 'themes.php' ) ) );
			exit;
	}
}

$wp_list_table->prepare_items();

add_thickbox();

add_screen_option( 'per_page', array('label' => _x( 'Themes', 'themes per page (screen options)' )) );

get_current_screen()->add_help_tab( array(
	'id'      => 'overview',
	'title'   => __('Overview'),
	'content' =>
		'<p>' . __('This screen enables and disables the inclusion of themes available to choose in the Appearance menu for each site. It does not activate or deactivate which theme a site is currently using.') . '</p>' .
		'<p>' . __('If the network admin disables a theme that is in use, it can still remain selected on that site. If another theme is chosen, the disabled theme will not appear in the site&#8217;s Appearance > Themes screen.') . '</p>' .
		'<p>' . __('Themes can be enabled on a site by site basis by the network admin on the Edit Site screen (which has a Themes tab); get there via the Edit action link on the All Sites screen. Only network admins are able to install or edit themes.') . '</p>'
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Network_Admin_Themes_Screen" target="_blank">Documentation on Network Themes</a>') . '</p>' .
	'<p>' . __('<a href="https://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

$title = __('Themes');
$parent_file = 'themes.php';

wp_enqueue_script( 'theme-preview' );

require_once(ABSPATH . 'wp-admin/admin-header.php');

?>

<div class="wrap">
<h2><?php echo esc_html( $title ); if ( current_user_can('install_themes') ) { ?> <a href="theme-install.php" class="add-new-h2"><?php echo esc_html_x('Add New', 'theme'); ?></a><?php }
if ( $s )
	printf( '<span class="subtitle">' . __('Search results for &#8220;%s&#8221;') . '</span>', esc_html( $s ) ); ?>
</h2>

<?php
if ( isset( $_GET['enabled'] ) ) {
	$_GET['enabled'] = absint( $_GET['enabled'] );
	echo '<div id="message" class="updated"><p>' . sprintf( _n( 'Theme enabled.', '%s themes enabled.', $_GET['enabled'] ), number_format_i18n( $_GET['enabled'] ) ) . '</p></div>';
} elseif ( isset( $_GET['disabled'] ) ) {
	$_GET['disabled'] = absint( $_GET['disabled'] );
	echo '<div id="message" class="updated"><p>' . sprintf( _n( 'Theme disabled.', '%s themes disabled.', $_GET['disabled'] ), number_format_i18n( $_GET['disabled'] ) ) . '</p></div>';
} elseif ( isset( $_GET['deleted'] ) ) {
	$_GET['deleted'] = absint( $_GET['deleted'] );
	echo '<div id="message" class="updated"><p>' . sprintf( _nx( 'Theme deleted.', '%s themes deleted.', $_GET['deleted'], 'network' ), number_format_i18n( $_GET['deleted'] ) ) . '</p></div>';
} elseif ( isset( $_GET['error'] ) && 'none' == $_GET['error'] ) {
	echo '<div id="message" class="error"><p>' . __( 'No theme selected.' ) . '</p></div>';
} elseif ( isset( $_GET['error'] ) && 'main' == $_GET['error'] ) {
	echo '<div class="error"><p>' . __( 'You cannot delete a theme while it is active on the main site.' ) . '</p></div>';
}

?>

<form method="get" action="">
<?php $wp_list_table->search_box( __( 'Search Installed Themes' ), 'theme' ); ?>
</form>

<?php
$wp_list_table->views();

if ( 'broken' == $status )
	echo '<p class="clear">' . __('The following themes are installed but incomplete. Themes must have a stylesheet and a template.') . '</p>';
?>

<form method="post" action="">
<input type="hidden" name="theme_status" value="<?php echo esc_attr($status) ?>" />
<input type="hidden" name="paged" value="<?php echo esc_attr($page) ?>" />

<?php $wp_list_table->display(); ?>
</form>

</div>

<?php
include(ABSPATH . 'wp-admin/admin-footer.php');
