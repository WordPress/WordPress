<?php
/**
 * User administration panel
 *
 * @package WordPress
 * @subpackage Administration
 * @since 1.0.0
 */

/** WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'list_users' ) ) {
	wp_die(
		'<h1>' . __( 'You need a higher level of permission.' ) . '</h1>' .
		'<p>' . __( 'Sorry, you are not allowed to list users.' ) . '</p>',
		403
	);
}

$wp_list_table = _get_list_table( 'WP_Users_List_Table' );
$pagenum       = $wp_list_table->get_pagenum();

// Used in the HTML title tag.
$title       = __( 'Users' );
$parent_file = 'users.php';

add_screen_option( 'per_page' );

// Contextual help - choose Help on the top right of admin panel to preview this.
get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( 'Overview' ),
		'content' => '<p>' . __( 'This screen lists all the existing users for your site. Each user has one of five defined roles as set by the site admin: Site Administrator, Editor, Author, Contributor, or Subscriber. Users with roles other than Administrator will see fewer options in the dashboard navigation when they are logged in, based on their role.' ) . '</p>' .
		'<p>' . __( 'To add a new user for your site, click the Add User button at the top of the screen or Add User in the Users menu section.' ) . '</p>',
	)
);

get_current_screen()->add_help_tab(
	array(
		'id'      => 'screen-content',
		'title'   => __( 'Screen Content' ),
		'content' => '<p>' . __( 'You can customize the display of this screen in a number of ways:' ) . '</p>' .
						'<ul>' .
						'<li>' . __( 'You can hide/display columns based on your needs and decide how many users to list per screen using the Screen Options tab.' ) . '</li>' .
						'<li>' . __( 'You can filter the list of users by User Role using the text links above the users list to show All, Administrator, Editor, Author, Contributor, or Subscriber. The default view is to show all users. Unused User Roles are not listed.' ) . '</li>' .
						'<li>' . __( 'You can view all posts made by a user by clicking on the number under the Posts column.' ) . '</li>' .
						'</ul>',
	)
);

$help = '<p>' . __( 'Hovering over a row in the users list will display action links that allow you to manage users. You can perform the following actions:' ) . '</p>' .
	'<ul>' .
	'<li>' . __( '<strong>Edit</strong> takes you to the editable profile screen for that user. You can also reach that screen by clicking on the username.' ) . '</li>';

if ( is_multisite() ) {
	$help .= '<li>' . __( '<strong>Remove</strong> allows you to remove a user from your site. It does not delete their content. You can also remove multiple users at once by using bulk actions.' ) . '</li>';
} else {
	$help .= '<li>' . __( '<strong>Delete</strong> brings you to the Delete Users screen for confirmation, where you can permanently remove a user from your site and delete their content. You can also delete multiple users at once by using bulk actions.' ) . '</li>';
}

$help .= '<li>' . __( '<strong>View</strong> takes you to a public author archive which lists all the posts published by the user.' ) . '</li>';

if ( current_user_can( 'edit_users' ) ) {
	$help .= '<li>' . __( '<strong>Send password reset</strong> sends the user an email with a link to set a new password.' ) . '</li>';
}

$help .= '</ul>';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'action-links',
		'title'   => __( 'Available Actions' ),
		'content' => $help,
	)
);
unset( $help );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://wordpress.org/documentation/article/users-screen/">Documentation on Managing Users</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://wordpress.org/documentation/article/roles-and-capabilities/">Descriptions of Roles and Capabilities</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/forums/">Support forums</a>' ) . '</p>'
);

get_current_screen()->set_screen_reader_content(
	array(
		'heading_views'      => __( 'Filter users list' ),
		'heading_pagination' => __( 'Users list navigation' ),
		'heading_list'       => __( 'Users list' ),
	)
);

if ( empty( $_REQUEST ) ) {
	$referer = '<input type="hidden" name="wp_http_referer" value="' . esc_attr( wp_unslash( $_SERVER['REQUEST_URI'] ) ) . '" />';
} elseif ( isset( $_REQUEST['wp_http_referer'] ) ) {
	$redirect = remove_query_arg( array( 'wp_http_referer', 'updated', 'delete_count' ), wp_unslash( $_REQUEST['wp_http_referer'] ) );
	$referer  = '<input type="hidden" name="wp_http_referer" value="' . esc_attr( $redirect ) . '" />';
} else {
	$redirect = 'users.php';
	$referer  = '';
}

$update = '';

switch ( $wp_list_table->current_action() ) {

	/* Bulk Dropdown menu Role changes */
	case 'promote':
		check_admin_referer( 'bulk-users' );

		if ( ! current_user_can( 'promote_users' ) ) {
			wp_die( __( 'Sorry, you are not allowed to edit this user.' ), 403 );
		}

		if ( empty( $_REQUEST['users'] ) ) {
			wp_redirect( $redirect );
			exit;
		}

		$editable_roles = get_editable_roles();
		$role           = $_REQUEST['new_role'];

		// Mocking the `none` role so we are able to save it to the database
		$editable_roles['none'] = array(
			'name' => __( '&mdash; No role for this site &mdash;' ),
		);

		if ( ! $role || empty( $editable_roles[ $role ] ) ) {
			wp_die( __( 'Sorry, you are not allowed to give users that role.' ), 403 );
		}

		if ( 'none' === $role ) {
			$role = '';
		}

		$user_ids = array_map( 'intval', (array) $_REQUEST['users'] );
		$update   = 'promote';

		foreach ( $user_ids as $id ) {
			if ( ! current_user_can( 'promote_user', $id ) ) {
				wp_die( __( 'Sorry, you are not allowed to edit this user.' ), 403 );
			}

			// The new role of the current user must also have the promote_users cap or be a multisite super admin.
			if ( $id === $current_user->ID
				&& ! $wp_roles->role_objects[ $role ]->has_cap( 'promote_users' )
				&& ! ( is_multisite() && current_user_can( 'manage_network_users' ) )
			) {
					$update = 'err_admin_role';
					continue;
			}

			// If the user doesn't already belong to the blog, bail.
			if ( is_multisite() && ! is_user_member_of_blog( $id ) ) {
				wp_die(
					'<h1>' . __( 'An error occurred.' ) . '</h1>' .
					'<p>' . __( 'One of the selected users is not a member of this site.' ) . '</p>',
					403
				);
			}

			$user = get_userdata( $id );
			$user->set_role( $role );
		}

		wp_redirect( add_query_arg( 'update', $update, $redirect ) );
		exit;

	case 'dodelete':
		if ( is_multisite() ) {
			wp_die( __( 'User deletion is not allowed from this screen.' ), 400 );
		}

		check_admin_referer( 'delete-users' );

		if ( empty( $_REQUEST['users'] ) ) {
			wp_redirect( $redirect );
			exit;
		}

		$user_ids = array_map( 'intval', (array) $_REQUEST['users'] );

		if ( empty( $_REQUEST['delete_option'] ) ) {
			$url = self_admin_url( 'users.php?action=delete&users[]=' . implode( '&users[]=', $user_ids ) . '&error=true' );
			$url = str_replace( '&amp;', '&', wp_nonce_url( $url, 'bulk-users' ) );
			wp_redirect( $url );
			exit;
		}

		if ( ! current_user_can( 'delete_users' ) ) {
			wp_die( __( 'Sorry, you are not allowed to delete users.' ), 403 );
		}

		$update       = 'del';
		$delete_count = 0;

		foreach ( $user_ids as $id ) {
			if ( ! current_user_can( 'delete_user', $id ) ) {
				wp_die( __( 'Sorry, you are not allowed to delete that user.' ), 403 );
			}

			if ( $id === $current_user->ID ) {
				$update = 'err_admin_del';
				continue;
			}

			switch ( $_REQUEST['delete_option'] ) {
				case 'delete':
					wp_delete_user( $id );
					break;
				case 'reassign':
					wp_delete_user( $id, $_REQUEST['reassign_user'] );
					break;
			}

			++$delete_count;
		}

		$redirect = add_query_arg(
			array(
				'delete_count' => $delete_count,
				'update'       => $update,
			),
			$redirect
		);
		wp_redirect( $redirect );
		exit;

	case 'resetpassword':
		check_admin_referer( 'bulk-users' );

		if ( ! current_user_can( 'edit_users' ) ) {
			$errors = new WP_Error( 'edit_users', __( 'Sorry, you are not allowed to edit users.' ) );
		}

		if ( empty( $_REQUEST['users'] ) ) {
			wp_redirect( $redirect );
			exit();
		}

		$user_ids = array_map( 'intval', (array) $_REQUEST['users'] );

		$reset_count = 0;

		foreach ( $user_ids as $id ) {
			if ( ! current_user_can( 'edit_user', $id ) ) {
				wp_die( __( 'Sorry, you are not allowed to edit this user.' ) );
			}

			if ( $id === $current_user->ID ) {
				$update = 'err_admin_reset';
				continue;
			}

			// Send the password reset link.
			$user = get_userdata( $id );
			if ( true === retrieve_password( $user->user_login ) ) {
				++$reset_count;
			}
		}

		$redirect = add_query_arg(
			array(
				'reset_count' => $reset_count,
				'update'      => 'resetpassword',
			),
			$redirect
		);
		wp_redirect( $redirect );
		exit;

	case 'delete':
		if ( is_multisite() ) {
			wp_die( __( 'User deletion is not allowed from this screen.' ), 400 );
		}

		check_admin_referer( 'bulk-users' );

		if ( empty( $_REQUEST['users'] ) && empty( $_REQUEST['user'] ) ) {
			wp_redirect( $redirect );
			exit;
		}

		if ( ! current_user_can( 'delete_users' ) ) {
			$errors = new WP_Error( 'edit_users', __( 'Sorry, you are not allowed to delete users.' ) );
		}

		if ( empty( $_REQUEST['users'] ) ) {
			$user_ids = array( (int) $_REQUEST['user'] );
		} else {
			$user_ids = array_map( 'intval', (array) $_REQUEST['users'] );
		}

		$all_user_ids = $user_ids;

		if ( in_array( $current_user->ID, $user_ids, true ) ) {
			$user_ids = array_diff( $user_ids, array( $current_user->ID ) );
		}

		/**
		 * Filters whether the users being deleted have additional content
		 * associated with them outside of the `post_author` and `link_owner` relationships.
		 *
		 * @since 5.2.0
		 *
		 * @param bool  $users_have_additional_content Whether the users have additional content. Default false.
		 * @param int[] $user_ids                      Array of IDs for users being deleted.
		 */
		$users_have_content = (bool) apply_filters( 'users_have_additional_content', false, $user_ids );

		if ( $user_ids && ! $users_have_content ) {
			if ( $wpdb->get_var(
				"SELECT ID FROM {$wpdb->posts}
				WHERE post_author IN( " . implode( ',', $user_ids ) . ' )
				LIMIT 1'
			) ) {
				$users_have_content = true;
			} elseif ( $wpdb->get_var(
				"SELECT link_id FROM {$wpdb->links}
				WHERE link_owner IN( " . implode( ',', $user_ids ) . ' )
				LIMIT 1'
			) ) {
				$users_have_content = true;
			}
		}

		if ( $users_have_content ) {
			add_action( 'admin_head', 'delete_users_add_js' );
		}

		require_once ABSPATH . 'wp-admin/admin-header.php';
		?>
		<form method="post" name="updateusers" id="updateusers">
		<?php wp_nonce_field( 'delete-users' ); ?>
		<?php echo $referer; ?>

		<div class="wrap">
		<h1><?php _e( 'Delete Users' ); ?></h1>

		<?php
		if ( isset( $_REQUEST['error'] ) ) :
			wp_admin_notice(
				'<strong>' . __( 'Error:' ) . '</strong> ' . __( 'Please select an option.' ),
				array(
					'additional_classes' => array( 'error' ),
				)
			);
		endif;
		?>

		<?php if ( 1 === count( $all_user_ids ) ) : ?>
			<p><?php _e( 'You have specified this user for deletion:' ); ?></p>
		<?php else : ?>
			<p><?php _e( 'You have specified these users for deletion:' ); ?></p>
		<?php endif; ?>

		<ul>
		<?php
		$go_delete = 0;

		foreach ( $all_user_ids as $id ) {
			$user = get_userdata( $id );

			if ( $id === $current_user->ID ) {
				echo '<li>';
				printf(
					/* translators: 1: User ID, 2: User login. */
					__( 'ID #%1$s: %2$s <strong>The current user will not be deleted.</strong>' ),
					$id,
					$user->user_login
				);
				echo "</li>\n";
			} else {
				echo '<li>';
				printf(
					'<input type="hidden" name="users[]" value="%s" />',
					esc_attr( $id )
				);
				printf(
					/* translators: 1: User ID, 2: User login. */
					__( 'ID #%1$s: %2$s' ),
					$id,
					$user->user_login
				);
				echo "</li>\n";

				++$go_delete;
			}
		}
		?>
		</ul>

		<?php
		if ( $go_delete ) :

			if ( ! $users_have_content ) :
				?>
				<input type="hidden" name="delete_option" value="delete" />
			<?php else : ?>
				<fieldset>
				<?php if ( 1 === $go_delete ) : ?>
					<p><legend><?php _e( 'What should be done with content owned by this user?' ); ?></legend></p>
				<?php else : ?>
					<p><legend><?php _e( 'What should be done with content owned by these users?' ); ?></legend></p>
				<?php endif; ?>

				<ul style="list-style:none;">
					<li>
						<input type="radio" id="delete_option0" name="delete_option" value="delete" />
						<label for="delete_option0"><?php _e( 'Delete all content.' ); ?></label>
					</li>
					<li>
						<input type="radio" id="delete_option1" name="delete_option" value="reassign" />
						<label for="delete_option1"><?php _e( 'Attribute all content to:' ); ?></label>
						<?php
						wp_dropdown_users(
							array(
								'name'    => 'reassign_user',
								'exclude' => $user_ids,
								'show'    => 'display_name_with_login',
							)
						);
						?>
					</li>
				</ul>
				</fieldset>
				<?php
			endif;

			/**
			 * Fires at the end of the delete users form prior to the confirm button.
			 *
			 * @since 4.0.0
			 * @since 4.5.0 The `$user_ids` parameter was added.
			 *
			 * @param WP_User $current_user WP_User object for the current user.
			 * @param int[]   $user_ids     Array of IDs for users being deleted.
			 */
			do_action( 'delete_user_form', $current_user, $user_ids );
			?>
			<input type="hidden" name="action" value="dodelete" />
			<?php submit_button( __( 'Confirm Deletion' ), 'primary' ); ?>

		<?php else : ?>

			<p><?php _e( 'There are no valid users selected for deletion.' ); ?></p>

		<?php endif; ?>
		</div><!-- .wrap -->
		</form><!-- #updateusers -->
		<?php

		break;

	case 'doremove':
		check_admin_referer( 'remove-users' );

		if ( ! is_multisite() ) {
			wp_die( __( 'You cannot remove users.' ), 400 );
		}

		if ( empty( $_REQUEST['users'] ) ) {
			wp_redirect( $redirect );
			exit;
		}

		if ( ! current_user_can( 'remove_users' ) ) {
			wp_die( __( 'Sorry, you are not allowed to remove users.' ), 403 );
		}

		$user_ids = array_map( 'intval', (array) $_REQUEST['users'] );
		$update   = 'remove';

		foreach ( $user_ids as $id ) {
			if ( ! current_user_can( 'remove_user', $id ) ) {
				$update = 'err_admin_remove';
				continue;
			}

			remove_user_from_blog( $id, $blog_id );
		}

		$redirect = add_query_arg( array( 'update' => $update ), $redirect );
		wp_redirect( $redirect );
		exit;

	case 'remove':
		check_admin_referer( 'bulk-users' );

		if ( ! is_multisite() ) {
			wp_die( __( 'You cannot remove users.' ), 400 );
		}

		if ( empty( $_REQUEST['users'] ) && empty( $_REQUEST['user'] ) ) {
			wp_redirect( $redirect );
			exit;
		}

		if ( ! current_user_can( 'remove_users' ) ) {
			$error = new WP_Error( 'edit_users', __( 'Sorry, you are not allowed to remove users.' ) );
		}

		if ( empty( $_REQUEST['users'] ) ) {
			$user_ids = array( (int) $_REQUEST['user'] );
		} else {
			$user_ids = array_map( 'intval', (array) $_REQUEST['users'] );
		}

		require_once ABSPATH . 'wp-admin/admin-header.php';
		?>
		<form method="post" name="updateusers" id="updateusers">
		<?php wp_nonce_field( 'remove-users' ); ?>
		<?php echo $referer; ?>

		<div class="wrap">
		<h1><?php _e( 'Remove Users from Site' ); ?></h1>

		<?php if ( 1 === count( $user_ids ) ) : ?>
			<p><?php _e( 'You have specified this user for removal:' ); ?></p>
		<?php else : ?>
			<p><?php _e( 'You have specified these users for removal:' ); ?></p>
		<?php endif; ?>

		<ul>
		<?php
		$go_remove = false;

		foreach ( $user_ids as $id ) {
			$user = get_userdata( $id );

			if ( ! current_user_can( 'remove_user', $id ) ) {
				echo '<li>';
				printf(
					/* translators: 1: User ID, 2: User login. */
					__( 'ID #%1$s: %2$s <strong>Sorry, you are not allowed to remove this user.</strong>' ),
					$id,
					$user->user_login
				);
				echo "</li>\n";
			} else {
				echo '<li>';
				printf(
					'<input type="hidden" name="users[]" value="%s" />',
					esc_attr( $id )
				);
				printf(
					/* translators: 1: User ID, 2: User login. */
					__( 'ID #%1$s: %2$s' ),
					$id,
					$user->user_login
				);
				echo "</li>\n";

				$go_remove = true;
			}
		}
		?>
		</ul>

		<?php if ( $go_remove ) : ?>

			<input type="hidden" name="action" value="doremove" />
			<?php submit_button( __( 'Confirm Removal' ), 'primary' ); ?>

		<?php else : ?>

			<p><?php _e( 'There are no valid users selected for removal.' ); ?></p>

		<?php endif; ?>
		</div><!-- .wrap -->
		</form><!-- #updateusers -->
		<?php

		break;

	default:
		if ( ! empty( $_GET['_wp_http_referer'] ) ) {
			wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
			exit;
		}

		if ( $wp_list_table->current_action() && ! empty( $_REQUEST['users'] ) ) {
			$screen   = get_current_screen()->id;
			$sendback = wp_get_referer();
			$user_ids = array_map( 'intval', (array) $_REQUEST['users'] );

			/** This action is documented in wp-admin/edit.php */
			$sendback = apply_filters( "handle_bulk_actions-{$screen}", $sendback, $wp_list_table->current_action(), $user_ids ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores

			wp_safe_redirect( $sendback );
			exit;
		}

		$wp_list_table->prepare_items();
		$total_pages = $wp_list_table->get_pagination_arg( 'total_pages' );

		if ( $pagenum > $total_pages && $total_pages > 0 ) {
			wp_redirect( add_query_arg( 'paged', $total_pages ) );
			exit;
		}

		require_once ABSPATH . 'wp-admin/admin-header.php';

		$messages = array();
		if ( isset( $_GET['update'] ) ) :
			switch ( $_GET['update'] ) {
				case 'del':
				case 'del_many':
					$delete_count = isset( $_GET['delete_count'] ) ? (int) $_GET['delete_count'] : 0;
					if ( 1 === $delete_count ) {
						$message = __( 'User deleted.' );
					} else {
						/* translators: %s: Number of users. */
						$message = _n( '%s user deleted.', '%s users deleted.', $delete_count );
					}
					$message    = sprintf( $message, number_format_i18n( $delete_count ) );
					$messages[] = wp_get_admin_notice(
						$message,
						array(
							'id'                 => 'message',
							'additional_classes' => array( 'updated' ),
							'dismissible'        => true,
						)
					);
					break;
				case 'add':
					$message = __( 'New user created.' );
					$user_id = isset( $_GET['id'] ) ? $_GET['id'] : false;
					if ( $user_id && current_user_can( 'edit_user', $user_id ) ) {
						$message .= sprintf(
							' <a href="%1$s">%2$s</a>',
							esc_url(
								add_query_arg(
									'wp_http_referer',
									urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ),
									self_admin_url( 'user-edit.php?user_id=' . $user_id )
								)
							),
							__( 'Edit user' )
						);
					}

					$messages[] = wp_get_admin_notice(
						$message,
						array(
							'id'                 => 'message',
							'additional_classes' => array( 'updated' ),
							'dismissible'        => true,
						)
					);
					break;
				case 'resetpassword':
					$reset_count = isset( $_GET['reset_count'] ) ? (int) $_GET['reset_count'] : 0;
					if ( 1 === $reset_count ) {
						$message = __( 'Password reset link sent.' );
					} else {
						/* translators: %s: Number of users. */
						$message = _n( 'Password reset links sent to %s user.', 'Password reset links sent to %s users.', $reset_count );
					}
					$message    = sprintf( $message, number_format_i18n( $reset_count ) );
					$messages[] = wp_get_admin_notice(
						$message,
						array(
							'id'                 => 'message',
							'additional_classes' => array( 'updated' ),
							'dismissible'        => true,
						)
					);
					break;
				case 'promote':
					$messages[] = wp_get_admin_notice(
						__( 'Changed roles.' ),
						array(
							'id'                 => 'message',
							'additional_classes' => array( 'updated' ),
							'dismissible'        => true,
						)
					);
					break;
				case 'err_admin_role':
					$messages[] = wp_get_admin_notice(
						__( 'The current user&#8217;s role must have user editing capabilities.' ),
						array(
							'id'                 => 'message',
							'additional_classes' => array( 'error' ),
							'dismissible'        => true,
						)
					);
					$messages[] = wp_get_admin_notice(
						__( 'Other user roles have been changed.' ),
						array(
							'id'                 => 'message',
							'additional_classes' => array( 'updated' ),
							'dismissible'        => true,
						)
					);
					break;
				case 'err_admin_del':
					$messages[] = wp_get_admin_notice(
						__( 'You cannot delete the current user.' ),
						array(
							'id'                 => 'message',
							'additional_classes' => array( 'error' ),
							'dismissible'        => true,
						)
					);
					$messages[] = wp_get_admin_notice(
						__( 'Other users have been deleted.' ),
						array(
							'id'                 => 'message',
							'additional_classes' => array( 'updated' ),
							'dismissible'        => true,
						)
					);
					break;
				case 'remove':
					$messages[] = wp_get_admin_notice(
						__( 'User removed from this site.' ),
						array(
							'id'                 => 'message',
							'additional_classes' => array( 'updated', 'fade' ),
							'dismissible'        => true,
						)
					);
					break;
				case 'err_admin_remove':
					$messages[] = wp_get_admin_notice(
						__( 'You cannot remove the current user.' ),
						array(
							'id'                 => 'message',
							'additional_classes' => array( 'error' ),
							'dismissible'        => true,
						)
					);
					$messages[] = wp_get_admin_notice(
						__( 'Other users have been removed.' ),
						array(
							'id'                 => 'message',
							'additional_classes' => array( 'updated', 'fade' ),
							'dismissible'        => true,
						)
					);
					break;
			}
		endif;
		?>

		<?php
		if ( isset( $errors ) && is_wp_error( $errors ) ) :
			$error_message = '';
			foreach ( $errors->get_error_messages() as $err ) {
				$error_message .= "<li>$err</li>\n";
			}
			wp_admin_notice(
				'<ul>' . $error_message . '</ul>',
				array(
					'additional_classes' => array( 'error' ),
				)
			);
		endif;

		if ( ! empty( $messages ) ) {
			foreach ( $messages as $msg ) {
				echo $msg;
			}
		}
		?>

		<div class="wrap">
		<h1 class="wp-heading-inline">
			<?php echo esc_html( $title ); ?>
		</h1>

		<?php
		if ( current_user_can( 'create_users' ) ) {
			printf(
				'<a href="%1$s" class="page-title-action">%2$s</a>',
				esc_url( admin_url( 'user-new.php' ) ),
				esc_html__( 'Add User' )
			);
		} elseif ( is_multisite() && current_user_can( 'promote_users' ) ) {
			printf(
				'<a href="%1$s" class="page-title-action">%2$s</a>',
				esc_url( admin_url( 'user-new.php' ) ),
				esc_html__( 'Add Existing User' )
			);
		}

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

		<form method="get">

		<?php $wp_list_table->search_box( __( 'Search Users' ), 'user' ); ?>

		<?php if ( ! empty( $_REQUEST['role'] ) ) { ?>
			<input type="hidden" name="role" value="<?php echo esc_attr( $_REQUEST['role'] ); ?>" />
		<?php } ?>

		<?php $wp_list_table->display(); ?>

		</form>

		<div class="clear"></div>
		</div><!-- .wrap -->
		<?php
		break;

} // End of the $doaction switch.

require_once ABSPATH . 'wp-admin/admin-footer.php';
