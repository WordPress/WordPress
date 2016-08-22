<?php
/**
 * Multisite sites administration panel.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.0.0
 */

/** Load WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

if ( ! is_multisite() )
	wp_die( __( 'Multisite support is not enabled.' ) );

if ( ! current_user_can( 'manage_sites' ) )
	wp_die( __( 'Sorry, you are not allowed to access this page.' ), 403 );

$wp_list_table = _get_list_table( 'WP_MS_Sites_List_Table' );
$pagenum = $wp_list_table->get_pagenum();

$title = __( 'Sites' );
$parent_file = 'sites.php';

add_screen_option( 'per_page' );

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
		'<li>' . __('Visit to go to the front-end site live.') . '</li></ul>' .
		'<p>' . __('The site ID is used internally, and is not shown on the front end of the site or to users/viewers.') . '</p>' .
		'<p>' . __('Clicking on bold headings can re-sort this table.') . '</p>'
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="https://codex.wordpress.org/Network_Admin_Sites_Screen" target="_blank">Documentation on Site Management</a>') . '</p>' .
	'<p>' . __('<a href="https://wordpress.org/support/forum/multisite/" target="_blank">Support Forums</a>') . '</p>'
);

get_current_screen()->set_screen_reader_content( array(
	'heading_pagination' => __( 'Sites list navigation' ),
	'heading_list'       => __( 'Sites list' ),
) );

$id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;

if ( isset( $_GET['action'] ) ) {
	/** This action is documented in wp-admin/network/edit.php */
	do_action( 'wpmuadminedit' );

	// A list of valid actions and their associated messaging for confirmation output.
	$manage_actions = array(
		'activateblog'   => __( 'You are about to activate the site %s.' ),
		'deactivateblog' => __( 'You are about to deactivate the site %s.' ),
		'unarchiveblog'  => __( 'You are about to unarchive the site %s.' ),
		'archiveblog'    => __( 'You are about to archive the site %s.' ),
		'unspamblog'     => __( 'You are about to unspam the site %s.' ),
		'spamblog'       => __( 'You are about to mark the site %s as spam.' ),
		'deleteblog'     => __( 'You are about to delete the site %s.' ),
		'unmatureblog'   => __( 'You are about to mark the site %s as mature.' ),
		'matureblog'     => __( 'You are about to mark the site %s as not mature.' ),
	);

	if ( 'confirm' === $_GET['action'] ) {
		// The action2 parameter contains the action being taken on the site.
		$site_action = $_GET['action2'];

		if ( ! array_key_exists( $site_action, $manage_actions ) ) {
			wp_die( __( 'The requested action is not valid.' ) );
		}

		// The mature/unmature UI exists only as external code. Check the "confirm" nonce for backward compatibility.
		if ( 'matureblog' === $site_action || 'unmatureblog' === $site_action ) {
			check_admin_referer( 'confirm' );
		} else {
			check_admin_referer( $site_action . '_' . $id );
		}

		if ( ! headers_sent() ) {
			nocache_headers();
			header( 'Content-Type: text/html; charset=utf-8' );
		}

		if ( $current_site->blog_id == $id ) {
			wp_die( __( 'Sorry, you are not allowed to change the current site.' ) );
		}

		$site_details = get_blog_details( $id );
		$site_address = untrailingslashit( $site_details->domain . $site_details->path );

		require_once( ABSPATH . 'wp-admin/admin-header.php' );
		?>
			<div class="wrap">
				<h1><?php _e( 'Confirm your action' ); ?></h1>
				<form action="sites.php?action=<?php echo esc_attr( $site_action ); ?>" method="post">
					<input type="hidden" name="action" value="<?php echo esc_attr( $site_action ); ?>" />
					<input type="hidden" name="id" value="<?php echo esc_attr( $id ); ?>" />
					<input type="hidden" name="_wp_http_referer" value="<?php echo esc_attr( wp_get_referer() ); ?>" />
					<?php wp_nonce_field( $site_action . '_' . $id, '_wpnonce', false ); ?>
					<p><?php echo sprintf( $manage_actions[ $site_action ], $site_address ); ?></p>
					<?php submit_button( __( 'Confirm' ), 'primary' ); ?>
				</form>
			</div>
		<?php
		require_once( ABSPATH . 'wp-admin/admin-footer.php' );
		exit();
	} elseif ( array_key_exists( $_GET['action'], $manage_actions ) ) {
		$action = $_GET['action'];
		check_admin_referer( $action . '_' . $id );
	} elseif ( 'allblogs' === $_GET['action'] ) {
		check_admin_referer( 'bulk-sites' );
	}

	$updated_action = '';

	switch ( $_GET['action'] ) {

		case 'deleteblog':
			if ( ! current_user_can( 'delete_sites' ) )
				wp_die( __( 'Sorry, you are not allowed to access this page.' ), '', array( 'response' => 403 ) );

			$updated_action = 'not_deleted';
			if ( $id != '0' && $id != $current_site->blog_id && current_user_can( 'delete_site', $id ) ) {
				wpmu_delete_blog( $id, true );
				$updated_action = 'delete';
			}
		break;

		case 'allblogs':
			if ( ( isset( $_POST['action'] ) || isset( $_POST['action2'] ) ) && isset( $_POST['allblogs'] ) ) {
				$doaction = $_POST['action'] != -1 ? $_POST['action'] : $_POST['action2'];

				foreach ( (array) $_POST['allblogs'] as $key => $val ) {
					if ( $val != '0' && $val != $current_site->blog_id ) {
						switch ( $doaction ) {
							case 'delete':
								if ( ! current_user_can( 'delete_site', $val ) )
									wp_die( __( 'Sorry, you are not allowed to delete the site.' ) );

								$updated_action = 'all_delete';
								wpmu_delete_blog( $val, true );
							break;

							case 'spam':
							case 'notspam':
								$updated_action = ( 'spam' === $doaction ) ? 'all_spam' : 'all_notspam';
								update_blog_status( $val, 'spam', ( 'spam' === $doaction ) ? '1' : '0' );
							break;
						}
					} else {
						wp_die( __( 'Sorry, you are not allowed to change the current site.' ) );
					}
				}
			} else {
				$location = network_admin_url( 'sites.php' );
				if ( ! empty( $_REQUEST['paged'] ) ) {
					$location = add_query_arg( 'paged', (int) $_REQUEST['paged'], $location );
				}
				wp_redirect( $location );
				exit();
			}
		break;

		case 'archiveblog':
		case 'unarchiveblog':
			update_blog_status( $id, 'archived', ( 'archiveblog' === $_GET['action'] ) ? '1' : '0' );
		break;

		case 'activateblog':
			update_blog_status( $id, 'deleted', '0' );

			/**
			 * Fires after a network site is activated.
			 *
			 * @since MU
			 *
			 * @param string $id The ID of the activated site.
			 */
			do_action( 'activate_blog', $id );
		break;

		case 'deactivateblog':
			/**
			 * Fires before a network site is deactivated.
			 *
			 * @since MU
			 *
			 * @param string $id The ID of the site being deactivated.
			 */
			do_action( 'deactivate_blog', $id );
			update_blog_status( $id, 'deleted', '1' );
		break;

		case 'unspamblog':
		case 'spamblog':
			update_blog_status( $id, 'spam', ( 'spamblog' === $_GET['action'] ) ? '1' : '0' );
		break;

		case 'unmatureblog':
		case 'matureblog':
			update_blog_status( $id, 'mature', ( 'matureblog' === $_GET['action'] ) ? '1' : '0' );
		break;
	}

	if ( empty( $updated_action ) && array_key_exists( $_GET['action'], $manage_actions ) ) {
		$updated_action = $_GET['action'];
	}

	if ( ! empty( $updated_action ) ) {
		wp_safe_redirect( add_query_arg( array( 'updated' => $updated_action ), wp_get_referer() ) );
		exit();
	}
}

$msg = '';
if ( isset( $_GET['updated'] ) ) {
	switch ( $_GET['updated'] ) {
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
			$msg = __( 'Sorry, you are not allowed to delete that site.' );
		break;
		case 'archiveblog':
			$msg = __( 'Site archived.' );
		break;
		case 'unarchiveblog':
			$msg = __( 'Site unarchived.' );
		break;
		case 'activateblog':
			$msg = __( 'Site activated.' );
		break;
		case 'deactivateblog':
			$msg = __( 'Site deactivated.' );
		break;
		case 'unspamblog':
			$msg = __( 'Site removed from spam.' );
		break;
		case 'spamblog':
			$msg = __( 'Site marked as spam.' );
		break;
		default:
			/**
			 * Filters a specific, non-default site-updated message in the Network admin.
			 *
			 * The dynamic portion of the hook name, `$_GET['updated']`, refers to the
			 * non-default site update action.
			 *
			 * @since 3.1.0
			 *
			 * @param string $msg The update message. Default 'Settings saved'.
			 */
			$msg = apply_filters( 'network_sites_updated_message_' . $_GET['updated'], __( 'Settings saved.' ) );
		break;
	}

	if ( ! empty( $msg ) )
		$msg = '<div id="message" class="updated notice is-dismissible"><p>' . $msg . '</p></div>';
}

$wp_list_table->prepare_items();

require_once( ABSPATH . 'wp-admin/admin-header.php' );
?>

<div class="wrap">
<h1><?php _e( 'Sites' ); ?>

<?php if ( current_user_can( 'create_sites') ) : ?>
	<a href="<?php echo network_admin_url('site-new.php'); ?>" class="page-title-action"><?php echo esc_html_x( 'Add New', 'site' ); ?></a>
<?php endif; ?>

<?php
if ( isset( $_REQUEST['s'] ) && strlen( $_REQUEST['s'] ) ) {
	/* translators: %s: search keywords */
	printf( '<span class="subtitle">' . __( 'Search results for &#8220;%s&#8221;' ) . '</span>', esc_html( $s ) );
} ?>
</h1>

<?php echo $msg; ?>

<form method="get" id="ms-search">
<?php $wp_list_table->search_box( __( 'Search Sites' ), 'site' ); ?>
<input type="hidden" name="action" value="blogs" />
</form>

<form id="form-site-list" action="sites.php?action=allblogs" method="post">
	<?php $wp_list_table->display(); ?>
</form>
</div>
<?php

require_once( ABSPATH . 'wp-admin/admin-footer.php' ); ?>
