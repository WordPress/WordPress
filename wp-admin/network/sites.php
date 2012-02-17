<?php
/**
 * Multisite sites administration panel.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.0.0
 */

/** Load WordPress Administration Bootstrap */
require_once( './admin.php' );

if ( ! is_multisite() )
	wp_die( __( 'Multisite support is not enabled.' ) );

if ( ! current_user_can( 'manage_sites' ) )
	wp_die( __( 'You do not have permission to access this page.' ) );

$wp_list_table = _get_list_table('WP_MS_Sites_List_Table');
$pagenum = $wp_list_table->get_pagenum();

$title = __( 'Sites' );
$parent_file = 'sites.php';

add_screen_option( 'per_page', array('label' => _x( 'Sites', 'sites per page (screen options)' )) );

get_current_screen()->add_help_tab( array(
	'id'      => 'overview',
	'title'   => __('Overview'),
	'content' =>
		'<p>' . __('Add New takes you to the Add New Site screen. You can search for a site by Name, ID number, or IP address. Screen Options allows you to choose how many sites to display on one page.') . '</p>' .
		'<p>' . __('This is the main table of all sites on this network. Switch between list and excerpt views by using the icons above the right side of the table.') . '</p>' .
		'<p>' . __('Hovering over each site reveals seven options (three for the primary site):') . '</p>' .
		'<ul><li>' . __('An Edit link to a separate Edit Site screen.') . '</li>' .
		'<li>' . __('Dashboard leads to the Dashboard for that site.') . '</li>' .
		'<li>' . __('Deactivate, Archive, and Spam which lead to confirmation screens. These actions can be reversed later.') . '</li>' .
		'<li>' . __('Delete which is a permanent action after the confirmation screens.') . '</li>' .
		'<li>' . __('Visit to go to the frontend site live.') . '</li></ul>' .
		'<p>' . __('The site ID is used internally, and is not shown on the front end of the site or to users/viewers.') . '</p>' .
		'<p>' . __('Clicking on bold headings can re-sort this table.') . '</p>'
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Network_Admin_Sites_Screens" target="_blank">Documentation on Site Management</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/forum/multisite/" target="_blank">Support Forums</a>') . '</p>'
);

$id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;

if ( isset( $_GET['action'] ) ) {
	do_action( 'wpmuadminedit' , '' );

	switch ( $_GET['action'] ) {
		case 'updateblog':
			// No longer used.
		break;

		case 'deleteblog':
			check_admin_referer('deleteblog');
			if ( ! ( current_user_can( 'manage_sites' ) && current_user_can( 'delete_sites' ) ) )
				wp_die( __( 'You do not have permission to access this page.' ) );

			if ( $id != '0' && $id != $current_site->blog_id && current_user_can( 'delete_site', $id ) ) {
				wpmu_delete_blog( $id, true );
				wp_safe_redirect( add_query_arg( array( 'updated' => 'true', 'action' => 'delete' ), wp_get_referer() ) );
			} else {
				wp_safe_redirect( add_query_arg( array( 'updated' => 'true', 'action' => 'not_deleted' ), wp_get_referer() ) );
			}

			exit();
		break;

		case 'allblogs':
			if ( ( isset( $_POST['action'] ) || isset( $_POST['action2'] ) ) && isset( $_POST['allblogs'] ) ) {
				check_admin_referer( 'bulk-sites' );

				if ( ! current_user_can( 'manage_sites' ) )
					wp_die( __( 'You do not have permission to access this page.' ) );

				if ( $_GET['action'] != -1 || $_POST['action2'] != -1 )
					$doaction = $_POST['action'] != -1 ? $_POST['action'] : $_POST['action2'];

				$blogfunction = '';

				foreach ( (array) $_POST['allblogs'] as $key => $val ) {
					if ( $val != '0' && $val != $current_site->blog_id ) {
						switch ( $doaction ) {
							case 'delete':
								if ( ! current_user_can( 'delete_site', $val ) )
									wp_die( __( 'You are not allowed to delete the site.' ) );
								$blogfunction = 'all_delete';
								wpmu_delete_blog( $val, true );
							break;

							case 'spam':
								$blogfunction = 'all_spam';
								update_blog_status( $val, 'spam', '1' );
								set_time_limit( 60 );
							break;

							case 'notspam':
								$blogfunction = 'all_notspam';
								update_blog_status( $val, 'spam', '0' );
								set_time_limit( 60 );
							break;
						}
					} else {
						wp_die( __( 'You are not allowed to change the current site.' ) );
					}
				}

				wp_safe_redirect( add_query_arg( array( 'updated' => 'true', 'action' => $blogfunction ), wp_get_referer() ) );
			} else {
				wp_redirect( network_admin_url( 'sites.php' ) );
			}
			exit();
		break;

		case 'archiveblog':
			check_admin_referer( 'archiveblog' );
			if ( ! current_user_can( 'manage_sites' ) )
				wp_die( __( 'You do not have permission to access this page.' ) );

			update_blog_status( $id, 'archived', '1' );
			wp_safe_redirect( add_query_arg( array( 'updated' => 'true', 'action' => 'archive' ), wp_get_referer() ) );
			exit();
		break;

		case 'unarchiveblog':
			check_admin_referer( 'unarchiveblog' );
			if ( ! current_user_can( 'manage_sites' ) )
				wp_die( __( 'You do not have permission to access this page.' ) );

			update_blog_status( $id, 'archived', '0' );
			wp_safe_redirect( add_query_arg( array( 'updated' => 'true', 'action' => 'unarchive' ), wp_get_referer() ) );
			exit();
		break;

		case 'activateblog':
			check_admin_referer( 'activateblog' );
			if ( ! current_user_can( 'manage_sites' ) )
				wp_die( __( 'You do not have permission to access this page.' ) );

			update_blog_status( $id, 'deleted', '0' );
			do_action( 'activate_blog', $id );
			wp_safe_redirect( add_query_arg( array( 'updated' => 'true', 'action' => 'activate' ), wp_get_referer() ) );
			exit();
		break;

		case 'deactivateblog':
			check_admin_referer( 'deactivateblog' );
			if ( ! current_user_can( 'manage_sites' ) )
				wp_die( __( 'You do not have permission to access this page.' ) );

			do_action( 'deactivate_blog', $id );
			update_blog_status( $id, 'deleted', '1' );
			wp_safe_redirect( add_query_arg( array( 'updated' => 'true', 'action' => 'deactivate' ), wp_get_referer() ) );
			exit();
		break;

		case 'unspamblog':
			check_admin_referer( 'unspamblog' );
			if ( ! current_user_can( 'manage_sites' ) )
				wp_die( __( 'You do not have permission to access this page.' ) );

			update_blog_status( $id, 'spam', '0' );
			wp_safe_redirect( add_query_arg( array( 'updated' => 'true', 'action' => 'unspam' ), wp_get_referer() ) );
			exit();
		break;

		case 'spamblog':
			check_admin_referer( 'spamblog' );
			if ( ! current_user_can( 'manage_sites' ) )
				wp_die( __( 'You do not have permission to access this page.' ) );

			update_blog_status( $id, 'spam', '1' );
			wp_safe_redirect( add_query_arg( array( 'updated' => 'true', 'action' => 'spam' ), wp_get_referer() ) );
			exit();
		break;

		case 'unmatureblog':
			check_admin_referer( 'unmatureblog' );
			if ( ! current_user_can( 'manage_sites' ) )
				wp_die( __( 'You do not have permission to access this page.' ) );

			update_blog_status( $id, 'mature', '0' );
			wp_safe_redirect( add_query_arg( array( 'updated' => 'true', 'action' => 'unmature' ), wp_get_referer() ) );
			exit();
		break;

		case 'matureblog':
			check_admin_referer( 'matureblog' );
			if ( ! current_user_can( 'manage_sites' ) )
				wp_die( __( 'You do not have permission to access this page.' ) );

			update_blog_status( $id, 'mature', '1' );
			wp_safe_redirect( add_query_arg( array( 'updated' => 'true', 'action' => 'mature' ), wp_get_referer() ) );
			exit();
		break;

		// Common
		case 'confirm':
			check_admin_referer( 'confirm' );
			if ( !headers_sent() ) {
				nocache_headers();
				header( 'Content-Type: text/html; charset=utf-8' );
			}
			if ( $current_site->blog_id == $id )
				wp_die( __( 'You are not allowed to change the current site.' ) );
			?>
			<!DOCTYPE html>
			<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
				<head>
					<title><?php _e( 'WordPress &rsaquo; Confirm your action' ); ?></title>

					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					<?php
					wp_admin_css( 'install', true );
					wp_admin_css( 'ie', true );
					?>
				</head>
				<body>
					<h1 id="logo"><img alt="WordPress" src="<?php echo esc_attr( admin_url( 'images/wordpress-logo.png?ver=20120216' ) ); ?>" /></h1>
					<form action="sites.php?action=<?php echo esc_attr( $_GET['action2'] ) ?>" method="post">
						<input type="hidden" name="action" value="<?php echo esc_attr( $_GET['action2'] ) ?>" />
						<input type="hidden" name="id" value="<?php echo esc_attr( $id ); ?>" />
						<input type="hidden" name="_wp_http_referer" value="<?php echo esc_attr( wp_get_referer() ); ?>" />
						<?php wp_nonce_field( $_GET['action2'], '_wpnonce', false ); ?>
						<p><?php echo esc_html( stripslashes( $_GET['msg'] ) ); ?></p>
						<?php submit_button( __('Confirm'), 'button' ); ?>
					</form>
				</body>
			</html>
			<?php
			exit();
		break;
	}
}

$msg = '';
if ( isset( $_REQUEST['updated'] ) && $_REQUEST['updated'] == 'true' && ! empty( $_REQUEST['action'] ) ) {
	switch ( $_REQUEST['action'] ) {
		case 'all_notspam':
			$msg = __( 'Sites removed from spam.' );
		break;
		case 'all_spam':
			$msg = __( 'Sites marked as spam.' );
		break;
		case 'all_delete':
			$msg = __( 'Sites deleted.' );
		break;
		case 'delete':
			$msg = __( 'Site deleted.' );
		break;
		case 'not_deleted':
			$msg = __( 'You do not have permission to delete that site.' );
		break;
		case 'archive':
			$msg = __( 'Site archived.' );
		break;
		case 'unarchive':
			$msg = __( 'Site unarchived.' );
		break;
		case 'activate':
			$msg = __( 'Site activated.' );
		break;
		case 'deactivate':
			$msg = __( 'Site deactivated.' );
		break;
		case 'unspam':
			$msg = __( 'Site removed from spam.' );
		break;
		case 'spam':
			$msg = __( 'Site marked as spam.' );
		break;
		default:
			$msg = apply_filters( 'network_sites_updated_message_' . $_REQUEST['action'] , __( 'Settings saved.' ) );
		break;
	}
	if ( $msg )
		$msg = '<div class="updated" id="message"><p>' . $msg . '</p></div>';
}

$wp_list_table->prepare_items();

require_once( '../admin-header.php' );
?>

<div class="wrap">
<?php screen_icon('ms-admin'); ?>
<h2><?php _e('Sites') ?>
<?php echo $msg; ?>
<?php if ( current_user_can( 'create_sites') ) : ?>
        <a href="<?php echo network_admin_url('site-new.php'); ?>" class="add-new-h2"><?php echo esc_html_x( 'Add New', 'site' ); ?></a>
<?php endif; ?>

<?php if ( isset( $_REQUEST['s'] ) && $_REQUEST['s'] ) {
	printf( '<span class="subtitle">' . __( 'Search results for &#8220;%s&#8221;' ) . '</span>', esc_html( $s ) );
} ?>
</h2>

<form action="" method="get" id="ms-search">
<?php $wp_list_table->search_box( __( 'Search Sites' ), 'site' ); ?>
<input type="hidden" name="action" value="blogs" />
</form>

<form id="form-site-list" action="sites.php?action=allblogs" method="post">
	<?php $wp_list_table->display(); ?>
</form>
</div>
<?php

require_once( '../admin-footer.php' ); ?>
