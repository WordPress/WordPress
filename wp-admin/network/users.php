<?php
/**
 * Multisite users administration panel.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.0.0
 */

/** Load WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'manage_network_users' ) ) {
	wp_die( __( 'Sorry, you are not allowed to access this page.' ), 403 );
}

if ( isset( $_GET['action'] ) ) {
	/** This action is documented in wp-admin/network/edit.php */
	do_action( 'wpmuadminedit' );

	switch ( $_GET['action'] ) {
		case 'deleteuser':
			if ( ! current_user_can( 'manage_network_users' ) ) {
				wp_die( __( 'Sorry, you are not allowed to access this page.' ), 403 );
			}

			check_admin_referer( 'deleteuser' );

			$id = (int) $_GET['id'];
			if ( $id > 1 ) {
				$_POST['allusers'] = array( $id ); // confirm_delete_users() can only handle arrays.

				// Used in the HTML title tag.
				$title       = __( 'Users' );
				$parent_file = 'users.php';

				require_once ABSPATH . 'wp-admin/admin-header.php';

				echo '<div class="wrap">';
				confirm_delete_users( $_POST['allusers'] );
				echo '</div>';

				require_once ABSPATH . 'wp-admin/admin-footer.php';
			} else {
				wp_redirect( network_admin_url( 'users.php' ) );
			}
			exit;

		case 'allusers':
			if ( ! current_user_can( 'manage_network_users' ) ) {
				wp_die( __( 'Sorry, you are not allowed to access this page.' ), 403 );
			}

			if ( isset( $_POST['action'] ) && isset( $_POST['allusers'] ) ) {
				check_admin_referer( 'bulk-users-network' );

				$doaction     = $_POST['action'];
				$userfunction = '';

				foreach ( (array) $_POST['allusers'] as $user_id ) {
					if ( ! empty( $user_id ) ) {
						switch ( $doaction ) {
							case 'delete':
								if ( ! current_user_can( 'delete_users' ) ) {
									wp_die( __( 'Sorry, you are not allowed to access this page.' ), 403 );
								}

								// Used in the HTML title tag.
								$title       = __( 'Users' );
								$parent_file = 'users.php';

								require_once ABSPATH . 'wp-admin/admin-header.php';

								echo '<div class="wrap">';
								confirm_delete_users( $_POST['allusers'] );
								echo '</div>';

								require_once ABSPATH . 'wp-admin/admin-footer.php';
								exit;

							case 'spam':
								$user = get_userdata( $user_id );
								if ( is_super_admin( $user->ID ) ) {
									wp_die(
										sprintf(
											/* translators: %s: User login. */
											__( 'Warning! User cannot be modified. The user %s is a network administrator.' ),
											esc_html( $user->user_login )
										)
									);
								}

								$userfunction = 'all_spam';
								$blogs        = get_blogs_of_user( $user_id, true );

								foreach ( (array) $blogs as $details ) {
									if ( ! is_main_site( $details->userblog_id ) ) { // Main site is not a spam!
										update_blog_status( $details->userblog_id, 'spam', '1' );
									}
								}

								$user_data         = $user->to_array();
								$user_data['spam'] = '1';

								wp_update_user( $user_data );
								break;

							case 'notspam':
								$user = get_userdata( $user_id );

								$userfunction = 'all_notspam';
								$blogs        = get_blogs_of_user( $user_id, true );

								foreach ( (array) $blogs as $details ) {
									update_blog_status( $details->userblog_id, 'spam', '0' );
								}

								$user_data         = $user->to_array();
								$user_data['spam'] = '0';

								wp_update_user( $user_data );
								break;
						}
					}
				}

				if ( ! in_array( $doaction, array( 'delete', 'spam', 'notspam' ), true ) ) {
					$sendback = wp_get_referer();
					$user_ids = (array) $_POST['allusers'];

					/** This action is documented in wp-admin/network/site-themes.php */
					$sendback = apply_filters( 'handle_network_bulk_actions-' . get_current_screen()->id, $sendback, $doaction, $user_ids ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores

					wp_safe_redirect( $sendback );
					exit;
				}

				wp_safe_redirect(
					add_query_arg(
						array(
							'updated' => 'true',
							'action'  => $userfunction,
						),
						wp_get_referer()
					)
				);
			} else {
				$location = network_admin_url( 'users.php' );

				if ( ! empty( $_REQUEST['paged'] ) ) {
					$location = add_query_arg( 'paged', (int) $_REQUEST['paged'], $location );
				}
				wp_redirect( $location );
			}
			exit;

		case 'dodelete':
			check_admin_referer( 'ms-users-delete' );
			if ( ! ( current_user_can( 'manage_network_users' ) && current_user_can( 'delete_users' ) ) ) {
				wp_die( __( 'Sorry, you are not allowed to access this page.' ), 403 );
			}

			if ( ! empty( $_POST['blog'] ) && is_array( $_POST['blog'] ) ) {
				foreach ( $_POST['blog'] as $id => $users ) {
					foreach ( $users as $blogid => $user_id ) {
						if ( ! current_user_can( 'delete_user', $id ) ) {
							continue;
						}

						if ( ! empty( $_POST['delete'] ) && 'reassign' === $_POST['delete'][ $blogid ][ $id ] ) {
							remove_user_from_blog( $id, $blogid, (int) $user_id );
						} else {
							remove_user_from_blog( $id, $blogid );
						}
					}
				}
			}

			$i = 0;

			if ( is_array( $_POST['user'] ) && ! empty( $_POST['user'] ) ) {
				foreach ( $_POST['user'] as $id ) {
					if ( ! current_user_can( 'delete_user', $id ) ) {
						continue;
					}
					wpmu_delete_user( $id );
					$i++;
				}
			}

			if ( 1 === $i ) {
				$deletefunction = 'delete';
			} else {
				$deletefunction = 'all_delete';
			}

			wp_redirect(
				add_query_arg(
					array(
						'updated' => 'true',
						'action'  => $deletefunction,
					),
					network_admin_url( 'users.php' )
				)
			);
			exit;
	}
}

$wp_list_table = _get_list_table( 'WP_MS_Users_List_Table' );
$pagenum       = $wp_list_table->get_pagenum();
$wp_list_table->prepare_items();
$total_pages = $wp_list_table->get_pagination_arg( 'total_pages' );

if ( $pagenum > $total_pages && $total_pages > 0 ) {
	wp_redirect( add_query_arg( 'paged', $total_pages ) );
	exit;
}

// Used in the HTML title tag.
$title       = __( 'Users' );
$parent_file = 'users.php';

add_screen_option( 'per_page' );

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( 'Overview' ),
		'content' =>
			'<p>' . __( 'This table shows all users across the network and the sites to which they are assigned.' ) . '</p>' .
			'<p>' . __( 'Hover over any user on the list to make the edit links appear. The Edit link on the left will take you to their Edit User profile page; the Edit link on the right by any site name goes to an Edit Site screen for that site.' ) . '</p>' .
			'<p>' . __( 'You can also go to the user&#8217;s profile page by clicking on the individual username.' ) . '</p>' .
			'<p>' . __( 'You can sort the table by clicking on any of the table headings and switch between list and excerpt views by using the icons above the users list.' ) . '</p>' .
			'<p>' . __( 'The bulk action will permanently delete selected users, or mark/unmark those selected as spam. Spam users will have posts removed and will be unable to sign up again with the same email addresses.' ) . '</p>' .
			'<p>' . __( 'You can make an existing user an additional super admin by going to the Edit User profile page and checking the box to grant that privilege.' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://codex.wordpress.org/Network_Admin_Users_Screen">Documentation on Network Users</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/forum/multisite/">Support forums</a>' ) . '</p>'
);

get_current_screen()->set_screen_reader_content(
	array(
		'heading_views'      => __( 'Filter users list' ),
		'heading_pagination' => __( 'Users list navigation' ),
		'heading_list'       => __( 'Users list' ),
	)
);

require_once ABSPATH . 'wp-admin/admin-header.php';

if ( isset( $_REQUEST['updated'] ) && 'true' === $_REQUEST['updated'] && ! empty( $_REQUEST['action'] ) ) {
	?>
	<div id="message" class="notice notice-success is-dismissible"><p>
		<?php
		switch ( $_REQUEST['action'] ) {
			case 'delete':
				_e( 'User deleted.' );
				break;
			case 'all_spam':
				_e( 'Users marked as spam.' );
				break;
			case 'all_notspam':
				_e( 'Users removed from spam.' );
				break;
			case 'all_delete':
				_e( 'Users deleted.' );
				break;
			case 'add':
				_e( 'User added.' );
				break;
		}
		?>
	</p></div>
	<?php
}
?>
<div class="wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Users' ); ?></h1>

	<?php
	if ( current_user_can( 'create_users' ) ) :
		?>
		<a href="<?php echo esc_url( network_admin_url( 'user-new.php' ) ); ?>" class="page-title-action"><?php echo esc_html_x( 'Add New', 'user' ); ?></a>
		<?php
	endif;

	if ( strlen( $usersearch ) ) {
		echo '<span class="subtitle">';
		printf(
			/* translators: %s: Search query. */
			__( 'Search results for: %s' ),
			'<strong>' . esc_html( $usersearch ) . '</strong>'
		);
		echo '</span>';
	}
	?>

	<hr class="wp-header-end">

	<?php $wp_list_table->views(); ?>

	<form method="get" class="search-form">
		<?php $wp_list_table->search_box( __( 'Search Users' ), 'all-user' ); ?>
	</form>

	<form id="form-user-list" action="users.php?action=allusers" method="post">
		<?php $wp_list_table->display(); ?>
	</form>
</div>

<?php require_once ABSPATH . 'wp-admin/admin-footer.php'; ?>
