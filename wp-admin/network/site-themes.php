<?php
/**
 * Edit Site Themes Administration Screen
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.1.0
 */

/** Load WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

if ( ! current_user_can( 'manage_sites' ) )
	wp_die( __( 'Sorry, you are not allowed to manage themes for this site.' ) );

get_current_screen()->add_help_tab( array(
	'id'      => 'overview',
	'title'   => __('Overview'),
	'content' =>
		'<p>' . __('The menu is for editing information specific to individual sites, particularly if the admin area of a site is unavailable.') . '</p>' .
		'<p>' . __('<strong>Info</strong> &mdash; The site URL is rarely edited as this can cause the site to not work properly. The Registered date and Last Updated date are displayed. Network admins can mark a site as archived, spam, deleted and mature, to remove from public listings or disable.') . '</p>' .
		'<p>' . __('<strong>Users</strong> &mdash; This displays the users associated with this site. You can also change their role, reset their password, or remove them from the site. Removing the user from the site does not remove the user from the network.') . '</p>' .
		'<p>' . sprintf( __('<strong>Themes</strong> &mdash; This area shows themes that are not already enabled across the network. Enabling a theme in this menu makes it accessible to this site. It does not activate the theme, but allows it to show in the site&#8217;s Appearance menu. To enable a theme for the entire network, see the <a href="%s">Network Themes</a> screen.' ), network_admin_url( 'themes.php' ) ) . '</p>' .
		'<p>' . __('<strong>Settings</strong> &mdash; This page shows a list of all settings associated with this site. Some are created by WordPress and others are created by plugins you activate. Note that some fields are grayed out and say Serialized Data. You cannot modify these values due to the way the setting is stored in the database.') . '</p>'
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="https://codex.wordpress.org/Network_Admin_Sites_Screen">Documentation on Site Management</a>') . '</p>' .
	'<p>' . __('<a href="https://wordpress.org/support/forum/multisite/">Support Forums</a>') . '</p>'
);

get_current_screen()->set_screen_reader_content( array(
	'heading_views'      => __( 'Filter site themes list' ),
	'heading_pagination' => __( 'Site themes list navigation' ),
	'heading_list'       => __( 'Site themes list' ),
) );

$wp_list_table = _get_list_table('WP_MS_Themes_List_Table');

$action = $wp_list_table->current_action();

$s = isset($_REQUEST['s']) ? $_REQUEST['s'] : '';

// Clean up request URI from temporary args for screen options/paging uri's to work as expected.
$temp_args = array( 'enabled', 'disabled', 'error' );
$_SERVER['REQUEST_URI'] = remove_query_arg( $temp_args, $_SERVER['REQUEST_URI'] );
$referer = remove_query_arg( $temp_args, wp_get_referer() );

if ( ! empty( $_REQUEST['paged'] ) ) {
	$referer = add_query_arg( 'paged', (int) $_REQUEST['paged'], $referer );
}

$id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;

if ( ! $id )
	wp_die( __('Invalid site ID.') );

$wp_list_table->prepare_items();

$details = get_site( $id );
if ( ! $details ) {
	wp_die( __( 'The requested site does not exist.' ) );
}

if ( !can_edit_network( $details->site_id ) )
	wp_die( __( 'Sorry, you are not allowed to access this page.' ), 403 );

$is_main_site = is_main_site( $id );

if ( $action ) {
	switch_to_blog( $id );
	$allowed_themes = get_option( 'allowedthemes' );

	switch ( $action ) {
		case 'enable':
			check_admin_referer( 'enable-theme_' . $_GET['theme'] );
			$theme = $_GET['theme'];
			$action = 'enabled';
			$n = 1;
			if ( !$allowed_themes )
				$allowed_themes = array( $theme => true );
			else
				$allowed_themes[$theme] = true;
			break;
		case 'disable':
			check_admin_referer( 'disable-theme_' . $_GET['theme'] );
			$theme = $_GET['theme'];
			$action = 'disabled';
			$n = 1;
			if ( !$allowed_themes )
				$allowed_themes = array();
			else
				unset( $allowed_themes[$theme] );
			break;
		case 'enable-selected':
			check_admin_referer( 'bulk-themes' );
			if ( isset( $_POST['checked'] ) ) {
				$themes = (array) $_POST['checked'];
				$action = 'enabled';
				$n = count( $themes );
				foreach ( (array) $themes as $theme )
					$allowed_themes[ $theme ] = true;
			} else {
				$action = 'error';
				$n = 'none';
			}
			break;
		case 'disable-selected':
			check_admin_referer( 'bulk-themes' );
			if ( isset( $_POST['checked'] ) ) {
				$themes = (array) $_POST['checked'];
				$action = 'disabled';
				$n = count( $themes );
				foreach ( (array) $themes as $theme )
					unset( $allowed_themes[ $theme ] );
			} else {
				$action = 'error';
				$n = 'none';
			}
			break;
		default:
			if ( isset( $_POST['checked'] ) ) {
				check_admin_referer( 'bulk-themes' );
				$themes = (array) $_POST['checked'];
				$n = count( $themes );
				$screen = get_current_screen()->id;

				/**
				 * Fires when a custom bulk action should be handled.
				 *
				 * The redirect link should be modified with success or failure feedback
				 * from the action to be used to display feedback to the user.
				 *
				 * The dynamic portion of the hook name, `$screen`, refers to the current screen ID.
				 *
				 * @since 4.7.0
				 *
				 * @param string $redirect_url The redirect URL.
				 * @param string $action       The action being taken.
				 * @param array  $items        The items to take the action on.
				 * @param int    $site_id      The site ID.
				 */
				$referer = apply_filters( "handle_network_bulk_actions-{$screen}", $referer, $action, $themes, $id );
			} else {
				$action = 'error';
				$n = 'none';
			}
	}

	update_option( 'allowedthemes', $allowed_themes );
	restore_current_blog();

	wp_safe_redirect( add_query_arg( array( 'id' => $id, $action => $n ), $referer ) );
	exit;
}

if ( isset( $_GET['action'] ) && 'update-site' == $_GET['action'] ) {
	wp_safe_redirect( $referer );
	exit();
}

add_thickbox();
add_screen_option( 'per_page' );

/* translators: %s: site name */
$title = sprintf( __( 'Edit Site: %s' ), esc_html( $details->blogname ) );

$parent_file = 'sites.php';
$submenu_file = 'sites.php';

require( ABSPATH . 'wp-admin/admin-header.php' ); ?>

<div class="wrap">
<h1 id="edit-site"><?php echo $title; ?></h1>
<p class="edit-site-actions"><a href="<?php echo esc_url( get_home_url( $id, '/' ) ); ?>"><?php _e( 'Visit' ); ?></a> | <a href="<?php echo esc_url( get_admin_url( $id ) ); ?>"><?php _e( 'Dashboard' ); ?></a></p>
<?php

network_edit_site_nav( array(
	'blog_id'  => $id,
	'selected' => 'site-themes'
) );

if ( isset( $_GET['enabled'] ) ) {
	$enabled = absint( $_GET['enabled'] );
	if ( 1 == $enabled ) {
		$message = __( 'Theme enabled.' );
	} else {
		$message = _n( '%s theme enabled.', '%s themes enabled.', $enabled );
	}
	echo '<div id="message" class="updated notice is-dismissible"><p>' . sprintf( $message, number_format_i18n( $enabled ) ) . '</p></div>';
} elseif ( isset( $_GET['disabled'] ) ) {
	$disabled = absint( $_GET['disabled'] );
	if ( 1 == $disabled ) {
		$message = __( 'Theme disabled.' );
	} else {
		$message = _n( '%s theme disabled.', '%s themes disabled.', $disabled );
	}
	echo '<div id="message" class="updated notice is-dismissible"><p>' . sprintf( $message, number_format_i18n( $disabled ) ) . '</p></div>';
} elseif ( isset( $_GET['error'] ) && 'none' == $_GET['error'] ) {
	echo '<div id="message" class="error notice is-dismissible"><p>' . __( 'No theme selected.' ) . '</p></div>';
} ?>

<p><?php _e( 'Network enabled themes are not shown on this screen.' ) ?></p>

<form method="get">
<?php $wp_list_table->search_box( __( 'Search Installed Themes' ), 'theme' ); ?>
<input type="hidden" name="id" value="<?php echo esc_attr( $id ) ?>" />
</form>

<?php $wp_list_table->views(); ?>

<form method="post" action="site-themes.php?action=update-site">
	<input type="hidden" name="id" value="<?php echo esc_attr( $id ) ?>" />

<?php $wp_list_table->display(); ?>

</form>

</div>
<?php include(ABSPATH . 'wp-admin/admin-footer.php'); ?>
