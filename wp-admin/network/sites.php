<?php
/**
 * Multisite sites administration panel.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.0.0
 */

/** Load WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'manage_sites' ) ) {
	wp_die( __( 'Sorry, you are not allowed to access this page.' ), 403 );
}

$wp_list_table = _get_list_table( 'WP_MS_Sites_List_Table' );
$pagenum       = $wp_list_table->get_pagenum();

// Used in the HTML title tag.
$title       = __( 'Sites' );
$parent_file = 'sites.php';

add_screen_option( 'per_page' );

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( 'Overview' ),
		'content' =>
		'<p>' . __( 'Add Site takes you to the screen for adding a new site to the network. You can search for a site by Name, ID number, or IP address. Screen Options allows you to choose how many sites to display on one page.' ) . '</p>' .
		'<p>' . __( 'This is the main table of all sites on this network. Switch between list and excerpt views by using the icons above the right side of the table.' ) . '</p>' .
			'<p>' . __( 'Hovering over each site reveals seven options (three for the primary site):' ) . '</p>' .
			'<ul><li>' . __( 'An Edit link to a separate Edit Site screen.' ) . '</li>' .
			'<li>' . __( 'Dashboard leads to the Dashboard for that site.' ) . '</li>' .
			'<li>' . __( 'Flag for Deletion, Archive, and Spam which lead to confirmation screens. These actions can be reversed later.' ) . '</li>' .
			'<li>' . __( 'Delete Permanently which is a permanent action after the confirmation screen.' ) . '</li>' .
			'<li>' . __( 'Visit to go to the front-end of the live site.' ) . '</li></ul>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://developer.wordpress.org/advanced-administration/multisite/admin/#network-admin-sites-screen">Documentation on Site Management</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/forum/multisite/">Support forums</a>' ) . '</p>'
);

get_current_screen()->set_screen_reader_content(
	array(
		'heading_pagination' => __( 'Sites list navigation' ),
		'heading_list'       => __( 'Sites list' ),
	)
);

$id = isset( $_REQUEST['id'] ) ? (int) $_REQUEST['id'] : 0;

if ( isset( $_GET['action'] ) ) {
	/** This action is documented in wp-admin/network/edit.php */
	do_action( 'wpmuadminedit' );

	// A list of valid actions and their associated messaging for confirmation output.
	$manage_actions = array(
		/* translators: %s: Site URL. */
		'activateblog'   => __( 'You are about to remove the deletion flag from the site %s.' ),
		/* translators: %s: Site URL. */
		'deactivateblog' => __( 'You are about to flag the site %s for deletion.' ),
		/* translators: %s: Site URL. */
		'unarchiveblog'  => __( 'You are about to unarchive the site %s.' ),
		/* translators: %s: Site URL. */
		'archiveblog'    => __( 'You are about to archive the site %s.' ),
		/* translators: %s: Site URL. */
		'unspamblog'     => __( 'You are about to unspam the site %s.' ),
		/* translators: %s: Site URL. */
		'spamblog'       => __( 'You are about to mark the site %s as spam.' ),
		/* translators: %s: Site URL. */
		'deleteblog'     => __( 'You are about to delete the site %s.' ),
		/* translators: %s: Site URL. */
		'unmatureblog'   => __( 'You are about to mark the site %s as mature.' ),
		/* translators: %s: Site URL. */
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

		if ( is_main_site( $id ) ) {
			wp_die( __( 'Sorry, you are not allowed to change the current site.' ) );
		}

		$site_details = get_site( $id );
		$site_address = untrailingslashit( $site_details->domain . $site_details->path );
		$submit       = __( 'Confirm' );

		require_once ABSPATH . 'wp-admin/admin-header.php';
		?>
			<div class="wrap">
				<h1><?php _e( 'Confirm your action' ); ?></h1>
				<form action="sites.php?action=<?php echo esc_attr( $site_action ); ?>" method="post">
					<input type="hidden" name="action" value="<?php echo esc_attr( $site_action ); ?>" />
					<input type="hidden" name="id" value="<?php echo esc_attr( $id ); ?>" />
					<input type="hidden" name="_wp_http_referer" value="<?php echo esc_attr( wp_get_referer() ); ?>" />
					<?php wp_nonce_field( $site_action . '_' . $id, '_wpnonce', false ); ?>
					<?php
					if ( 'deleteblog' === $site_action ) {
						$submit = __( 'Delete this site permanently' );
						?>
						<div class="notice notice-warning inline">
							<p><?php _e( 'Deleting a site is a permanent action that cannot be undone. This will delete the entire site and its uploads directory.' ); ?>
						</div>
						<?php
					} elseif ( 'archiveblog' === $site_action ) {
						?>
						<div class="notice notice-warning inline">
							<p><?php _e( 'Archiving a site makes the site unavailable to its users and visitors. This is a reversible action.' ); ?>
						</div>
						<?php
					} elseif ( 'deactivateblog' === $site_action ) {
						?>
						<div class="notice notice-warning inline">
							<p><?php _e( 'Flagging a site for deletion makes the site unavailable to its users and visitors. This is a reversible action. A super admin can permanently delete the site at a later date.' ); ?>
						</div>
						<?php
					}
					?>
					<p><?php printf( $manage_actions[ $site_action ], "<strong>{$site_address}</strong>" ); ?></p>
					<?php submit_button( $submit, 'primary' ); ?>
				</form>
			</div>
		<?php
		require_once ABSPATH . 'wp-admin/admin-footer.php';
		exit;
	} elseif ( array_key_exists( $_GET['action'], $manage_actions ) ) {
		$action = $_GET['action'];
		check_admin_referer( $action . '_' . $id );
	} elseif ( 'allblogs' === $_GET['action'] ) {
		check_admin_referer( 'bulk-sites' );
	}

	$updated_action = '';

	switch ( $_GET['action'] ) {

		case 'deleteblog':
			if ( ! current_user_can( 'delete_sites' ) ) {
				wp_die( __( 'Sorry, you are not allowed to access this page.' ), '', array( 'response' => 403 ) );
			}

			$updated_action = 'not_deleted';
			if ( 0 !== $id && ! is_main_site( $id ) && current_user_can( 'delete_site', $id ) ) {
				wpmu_delete_blog( $id, true );
				$updated_action = 'delete';
			}
			break;

		case 'delete_sites':
			check_admin_referer( 'ms-delete-sites' );

			foreach ( (array) $_POST['site_ids'] as $site_id ) {
				$site_id = (int) $site_id;

				if ( is_main_site( $site_id ) ) {
					continue;
				}

				if ( ! current_user_can( 'delete_site', $site_id ) ) {
					$site         = get_site( $site_id );
					$site_address = untrailingslashit( $site->domain . $site->path );

					wp_die(
						sprintf(
							/* translators: %s: Site URL. */
							__( 'Sorry, you are not allowed to delete the site %s.' ),
							$site_address
						),
						403
					);
				}

				$updated_action = 'all_delete';
				wpmu_delete_blog( $site_id, true );
			}
			break;

		case 'allblogs':
			if ( isset( $_POST['action'] ) && isset( $_POST['allblogs'] ) ) {
				$doaction = $_POST['action'];

				foreach ( (array) $_POST['allblogs'] as $site_id ) {
					$site_id = (int) $site_id;

					if ( 0 !== $site_id && ! is_main_site( $site_id ) ) {
						switch ( $doaction ) {
							case 'delete':
								require_once ABSPATH . 'wp-admin/admin-header.php';
								?>
								<div class="wrap">
									<h1><?php _e( 'Confirm your action' ); ?></h1>
									<form action="sites.php?action=delete_sites" method="post">
										<input type="hidden" name="action" value="delete_sites" />
										<input type="hidden" name="_wp_http_referer" value="<?php echo esc_attr( wp_get_referer() ); ?>" />
										<?php wp_nonce_field( 'ms-delete-sites', '_wpnonce', false ); ?>
										<div class="notice notice-warning inline">
											<p><?php _e( 'Deleting a site is a permanent action that cannot be undone. This will delete the entire site and its uploads directory.' ); ?>
										</div>
										<p><?php _e( 'You are about to delete the following sites:' ); ?></p>
										<ul class="ul-disc">
											<?php
											foreach ( $_POST['allblogs'] as $site_id ) :
												$site_id = (int) $site_id;

												$site         = get_site( $site_id );
												$site_address = untrailingslashit( $site->domain . $site->path );
												?>
												<li>
													<?php echo $site_address; ?>
													<input type="hidden" name="site_ids[]" value="<?php echo esc_attr( $site_id ); ?>" />
												</li>
											<?php endforeach; ?>
										</ul>
										<?php submit_button( __( 'Delete these sites permanently' ), 'primary' ); ?>
									</form>
								</div>
								<?php
								require_once ABSPATH . 'wp-admin/admin-footer.php';
								exit;
							break;

							case 'spam':
							case 'notspam':
								$updated_action = ( 'spam' === $doaction ) ? 'all_spam' : 'all_notspam';
								update_blog_status( $site_id, 'spam', ( 'spam' === $doaction ) ? '1' : '0' );
								break;
						}
					} else {
						wp_die( __( 'Sorry, you are not allowed to change the current site.' ) );
					}
				}

				if ( ! in_array( $doaction, array( 'delete', 'spam', 'notspam' ), true ) ) {
					$redirect_to = wp_get_referer();
					$blogs       = (array) $_POST['allblogs'];

					/** This action is documented in wp-admin/network/site-themes.php */
					$redirect_to = apply_filters( 'handle_network_bulk_actions-' . get_current_screen()->id, $redirect_to, $doaction, $blogs, $id ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores

					wp_safe_redirect( $redirect_to );
					exit;
				}
			} else {
				// Process query defined by WP_MS_Site_List_Table::extra_table_nav().
				$location = remove_query_arg(
					array( '_wp_http_referer', '_wpnonce' ),
					add_query_arg( $_POST, network_admin_url( 'sites.php' ) )
				);

				wp_redirect( $location );
				exit;
			}

			break;

		case 'archiveblog':
		case 'unarchiveblog':
			update_blog_status( $id, 'archived', ( 'archiveblog' === $_GET['action'] ) ? '1' : '0' );
			break;

		case 'activateblog':
			update_blog_status( $id, 'deleted', '0' );

			/**
			 * Fires after a network site has its deletion flag removed.
			 *
			 * @since MU (3.0.0)
			 *
			 * @param int $id The ID of the reactivated site.
			 */
			do_action( 'activate_blog', $id );
			break;

		case 'deactivateblog':
			/**
			 * Fires before a network site is flagged for deletion.
			 *
			 * @since MU (3.0.0)
			 *
			 * @param int $id The ID of the site being flagged for deletion.
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
		exit;
	}
}

$msg = '';
if ( isset( $_GET['updated'] ) ) {
	$action = $_GET['updated'];

	switch ( $action ) {
		case 'all_notspam':
			$msg = __( 'Sites removed from spam.' );
			break;
		case 'all_spam':
			$msg = __( 'Sites marked as spam.' );
			break;
		case 'all_delete':
			$msg = __( 'Sites permanently deleted.' );
			break;
		case 'delete':
			$msg = __( 'Site permanently deleted.' );
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
			$msg = __( 'Site deletion flag removed.' );
			break;
		case 'deactivateblog':
			$msg = __( 'Site flagged for deletion.' );
			break;
		case 'unspamblog':
			$msg = __( 'Site removed from spam.' );
			break;
		case 'spamblog':
			$msg = __( 'Site marked as spam.' );
			break;
		default:
			/**
			 * Filters a specific, non-default, site-updated message in the Network admin.
			 *
			 * The dynamic portion of the hook name, `$action`, refers to the non-default
			 * site update action.
			 *
			 * @since 3.1.0
			 *
			 * @param string $msg The update message. Default 'Settings saved'.
			 */
			$msg = apply_filters( "network_sites_updated_message_{$action}", __( 'Settings saved.' ) );
			break;
	}

	if ( ! empty( $msg ) ) {
		$msg = wp_get_admin_notice(
			$msg,
			array(
				'type'        => 'success',
				'dismissible' => true,
				'id'          => 'message',
			)
		);
	}
}

$wp_list_table->prepare_items();

require_once ABSPATH . 'wp-admin/admin-header.php';
?>

<div class="wrap">
<h1 class="wp-heading-inline"><?php _e( 'Sites' ); ?></h1>

<?php if ( current_user_can( 'create_sites' ) ) : ?>
	<a href="<?php echo esc_url( network_admin_url( 'site-new.php' ) ); ?>" class="page-title-action"><?php echo esc_html__( 'Add Site' ); ?></a>
<?php endif; ?>

<?php
if ( isset( $_REQUEST['s'] ) && strlen( $_REQUEST['s'] ) ) {
	echo '<span class="subtitle">';
	printf(
		/* translators: %s: Search query. */
		__( 'Search results for: %s' ),
		'<strong>' . esc_html( $s ) . '</strong>'
	);
	echo '</span>';
}
?>

<hr class="wp-header-end">

<?php $wp_list_table->views(); ?>

<?php echo $msg; ?>

<form method="get" id="ms-search" class="wp-clearfix">
<?php $wp_list_table->search_box( __( 'Search Sites' ), 'site' ); ?>
<input type="hidden" name="action" value="blogs" />
</form>

<form id="form-site-list" action="sites.php?action=allblogs" method="post">
	<?php $wp_list_table->display(); ?>
</form>
</div>
<?php

require_once ABSPATH . 'wp-admin/admin-footer.php'; ?>
