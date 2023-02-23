<?php
/**
 * Edit Site Users Administration Screen
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.1.0
 */

/** Load WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'manage_sites' ) ) {
	wp_die( __( 'Sorry, you are not allowed to edit this site.' ), 403 );
}

$wp_list_table = _get_list_table( 'WP_Users_List_Table' );
$wp_list_table->prepare_items();

get_current_screen()->add_help_tab( get_site_screen_help_tab_args() );
get_current_screen()->set_help_sidebar( get_site_screen_help_sidebar_content() );

get_current_screen()->set_screen_reader_content(
	array(
		'heading_views'      => __( 'Filter site users list' ),
		'heading_pagination' => __( 'Site users list navigation' ),
		'heading_list'       => __( 'Site users list' ),
	)
);

$_SERVER['REQUEST_URI'] = remove_query_arg( 'update', $_SERVER['REQUEST_URI'] );
$referer                = remove_query_arg( 'update', wp_get_referer() );

if ( ! empty( $_REQUEST['paged'] ) ) {
	$referer = add_query_arg( 'paged', (int) $_REQUEST['paged'], $referer );
}

$id = isset( $_REQUEST['id'] ) ? (int) $_REQUEST['id'] : 0;

if ( ! $id ) {
	wp_die( __( 'Invalid site ID.' ) );
}

$details = get_site( $id );
if ( ! $details ) {
	wp_die( __( 'The requested site does not exist.' ) );
}

if ( ! can_edit_network( $details->site_id ) ) {
	wp_die( __( 'Sorry, you are not allowed to access this page.' ), 403 );
}

$is_main_site = is_main_site( $id );

switch_to_blog( $id );

$action = $wp_list_table->current_action();

if ( $action ) {

	switch ( $action ) {
		case 'newuser':
			check_admin_referer( 'add-user', '_wpnonce_add-new-user' );
			$user = $_POST['user'];
			if ( ! is_array( $_POST['user'] ) || empty( $user['username'] ) || empty( $user['email'] ) ) {
				$update = 'err_new';
			} else {
				$password = wp_generate_password( 12, false );
				$user_id  = wpmu_create_user( esc_html( strtolower( $user['username'] ) ), $password, esc_html( $user['email'] ) );

				if ( false === $user_id ) {
					$update = 'err_new_dup';
				} else {
					$result = add_user_to_blog( $id, $user_id, $_POST['new_role'] );

					if ( is_wp_error( $result ) ) {
						$update = 'err_add_fail';
					} else {
						$update = 'newuser';

						/**
						 * Fires after a user has been created via the network site-users.php page.
						 *
						 * @since 4.4.0
						 *
						 * @param int $user_id ID of the newly created user.
						 */
						do_action( 'network_site_users_created_user', $user_id );
					}
				}
			}
			break;

		case 'adduser':
			check_admin_referer( 'add-user', '_wpnonce_add-user' );
			if ( ! empty( $_POST['newuser'] ) ) {
				$update  = 'adduser';
				$newuser = $_POST['newuser'];
				$user    = get_user_by( 'login', $newuser );
				if ( $user && $user->exists() ) {
					if ( ! is_user_member_of_blog( $user->ID, $id ) ) {
						$result = add_user_to_blog( $id, $user->ID, $_POST['new_role'] );

						if ( is_wp_error( $result ) ) {
							$update = 'err_add_fail';
						}
					} else {
						$update = 'err_add_member';
					}
				} else {
					$update = 'err_add_notfound';
				}
			} else {
				$update = 'err_add_notfound';
			}
			break;

		case 'remove':
			if ( ! current_user_can( 'remove_users' ) ) {
				wp_die( __( 'Sorry, you are not allowed to remove users.' ), 403 );
			}

			check_admin_referer( 'bulk-users' );

			$update = 'remove';
			if ( isset( $_REQUEST['users'] ) ) {
				$userids = $_REQUEST['users'];

				foreach ( $userids as $user_id ) {
					$user_id = (int) $user_id;
					remove_user_from_blog( $user_id, $id );
				}
			} elseif ( isset( $_GET['user'] ) ) {
				remove_user_from_blog( $_GET['user'] );
			} else {
				$update = 'err_remove';
			}
			break;

		case 'promote':
			check_admin_referer( 'bulk-users' );
			$editable_roles = get_editable_roles();
			$role           = $_REQUEST['new_role'];

			if ( empty( $editable_roles[ $role ] ) ) {
				wp_die( __( 'Sorry, you are not allowed to give users that role.' ), 403 );
			}

			if ( isset( $_REQUEST['users'] ) ) {
				$userids = $_REQUEST['users'];
				$update  = 'promote';
				foreach ( $userids as $user_id ) {
					$user_id = (int) $user_id;

					// If the user doesn't already belong to the blog, bail.
					if ( ! is_user_member_of_blog( $user_id ) ) {
						wp_die(
							'<h1>' . __( 'Something went wrong.' ) . '</h1>' .
							'<p>' . __( 'One of the selected users is not a member of this site.' ) . '</p>',
							403
						);
					}

					$user = get_userdata( $user_id );
					$user->set_role( $role );
				}
			} else {
				$update = 'err_promote';
			}
			break;
		default:
			if ( ! isset( $_REQUEST['users'] ) ) {
				break;
			}
			check_admin_referer( 'bulk-users' );
			$userids = $_REQUEST['users'];

			/** This action is documented in wp-admin/network/site-themes.php */
			$referer = apply_filters( 'handle_network_bulk_actions-' . get_current_screen()->id, $referer, $action, $userids, $id ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores

			$update = $action;
			break;
	}

	wp_safe_redirect( add_query_arg( 'update', $update, $referer ) );
	exit;
}

restore_current_blog();

if ( isset( $_GET['action'] ) && 'update-site' === $_GET['action'] ) {
	wp_safe_redirect( $referer );
	exit;
}

add_screen_option( 'per_page' );

// Used in the HTML title tag.
/* translators: %s: Site title. */
$title = sprintf( __( 'Edit Site: %s' ), esc_html( $details->blogname ) );

$parent_file  = 'sites.php';
$submenu_file = 'sites.php';

/**
 * Filters whether to show the Add Existing User form on the Multisite Users screen.
 *
 * @since 3.1.0
 *
 * @param bool $bool Whether to show the Add Existing User form. Default true.
 */
if ( ! wp_is_large_network( 'users' ) && apply_filters( 'show_network_site_users_add_existing_form', true ) ) {
	wp_enqueue_script( 'user-suggest' );
}

require_once ABSPATH . 'wp-admin/admin-header.php'; ?>

<script type="text/javascript">
var current_site_id = <?php echo absint( $id ); ?>;
</script>


<div class="wrap">
<h1 id="edit-site"><?php echo $title; ?></h1>
<p class="edit-site-actions"><a href="<?php echo esc_url( get_home_url( $id, '/' ) ); ?>"><?php _e( 'Visit' ); ?></a> | <a href="<?php echo esc_url( get_admin_url( $id ) ); ?>"><?php _e( 'Dashboard' ); ?></a></p>
<?php

network_edit_site_nav(
	array(
		'blog_id'  => $id,
		'selected' => 'site-users',
	)
);

if ( isset( $_GET['update'] ) ) :
	switch ( $_GET['update'] ) {
		case 'adduser':
			echo '<div id="message" class="notice notice-success is-dismissible"><p>' . __( 'User added.' ) . '</p></div>';
			break;
		case 'err_add_member':
			echo '<div id="message" class="notice notice-error  is-dismissible"><p>' . __( 'User is already a member of this site.' ) . '</p></div>';
			break;
		case 'err_add_fail':
			echo '<div id="message" class="notice notice-error is-dismissible"><p>' . __( 'User could not be added to this site.' ) . '</p></div>';
			break;
		case 'err_add_notfound':
			echo '<div id="message" class="notice notice-error is-dismissible"><p>' . __( 'Enter the username of an existing user.' ) . '</p></div>';
			break;
		case 'promote':
			echo '<div id="message" class="notice notice-success is-dismissible"><p>' . __( 'Changed roles.' ) . '</p></div>';
			break;
		case 'err_promote':
			echo '<div id="message" class="notice notice-error is-dismissible"><p>' . __( 'Select a user to change role.' ) . '</p></div>';
			break;
		case 'remove':
			echo '<div id="message" class="notice notice-success is-dismissible"><p>' . __( 'User removed from this site.' ) . '</p></div>';
			break;
		case 'err_remove':
			echo '<div id="message" class="notice notice-error is-dismissible"><p>' . __( 'Select a user to remove.' ) . '</p></div>';
			break;
		case 'newuser':
			echo '<div id="message" class="notice notice-success is-dismissible"><p>' . __( 'User created.' ) . '</p></div>';
			break;
		case 'err_new':
			echo '<div id="message" class="notice notice-error is-dismissible"><p>' . __( 'Enter the username and email.' ) . '</p></div>';
			break;
		case 'err_new_dup':
			echo '<div id="message" class="notice notice-error is-dismissible"><p>' . __( 'Duplicated username or email address.' ) . '</p></div>';
			break;
	}
endif;
?>

<form class="search-form" method="get">
<?php $wp_list_table->search_box( __( 'Search Users' ), 'user' ); ?>
<input type="hidden" name="id" value="<?php echo esc_attr( $id ); ?>" />
</form>

<?php $wp_list_table->views(); ?>

<form method="post" action="site-users.php?action=update-site">
	<input type="hidden" name="id" value="<?php echo esc_attr( $id ); ?>" />

<?php $wp_list_table->display(); ?>

</form>

<?php
/**
 * Fires after the list table on the Users screen in the Multisite Network Admin.
 *
 * @since 3.1.0
 */
do_action( 'network_site_users_after_list_table' );

/** This filter is documented in wp-admin/network/site-users.php */
if ( current_user_can( 'promote_users' ) && apply_filters( 'show_network_site_users_add_existing_form', true ) ) :
	?>
<h2 id="add-existing-user"><?php _e( 'Add Existing User' ); ?></h2>
<form action="site-users.php?action=adduser" id="adduser" method="post">
	<input type="hidden" name="id" value="<?php echo esc_attr( $id ); ?>" />
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><label for="newuser"><?php _e( 'Username' ); ?></label></th>
			<td><input type="text" class="regular-text wp-suggest-user" name="newuser" id="newuser" /></td>
		</tr>
		<tr>
			<th scope="row"><label for="new_role_adduser"><?php _e( 'Role' ); ?></label></th>
			<td><select name="new_role" id="new_role_adduser">
			<?php
			switch_to_blog( $id );
			wp_dropdown_roles( get_option( 'default_role' ) );
			restore_current_blog();
			?>
			</select></td>
		</tr>
	</table>
	<?php wp_nonce_field( 'add-user', '_wpnonce_add-user' ); ?>
	<?php submit_button( __( 'Add User' ), 'primary', 'add-user', true, array( 'id' => 'submit-add-existing-user' ) ); ?>
</form>
<?php endif; ?>

<?php
/**
 * Filters whether to show the Add New User form on the Multisite Users screen.
 *
 * @since 3.1.0
 *
 * @param bool $bool Whether to show the Add New User form. Default true.
 */
if ( current_user_can( 'create_users' ) && apply_filters( 'show_network_site_users_add_new_form', true ) ) :
	?>
<h2 id="add-new-user"><?php _e( 'Add New User' ); ?></h2>
<form action="<?php echo esc_url( network_admin_url( 'site-users.php?action=newuser' ) ); ?>" id="newuser" method="post">
	<input type="hidden" name="id" value="<?php echo esc_attr( $id ); ?>" />
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><label for="user_username"><?php _e( 'Username' ); ?></label></th>
			<td><input type="text" class="regular-text" name="user[username]" id="user_username" /></td>
		</tr>
		<tr>
			<th scope="row"><label for="user_email"><?php _e( 'Email' ); ?></label></th>
			<td><input type="text" class="regular-text" name="user[email]" id="user_email" /></td>
		</tr>
		<tr>
			<th scope="row"><label for="new_role_newuser"><?php _e( 'Role' ); ?></label></th>
			<td><select name="new_role" id="new_role_newuser">
			<?php
			switch_to_blog( $id );
			wp_dropdown_roles( get_option( 'default_role' ) );
			restore_current_blog();
			?>
			</select></td>
		</tr>
		<tr class="form-field">
			<td colspan="2" class="td-full"><?php _e( 'A password reset link will be sent to the user via email.' ); ?></td>
		</tr>
	</table>
	<?php wp_nonce_field( 'add-user', '_wpnonce_add-new-user' ); ?>
	<?php submit_button( __( 'Add New User' ), 'primary', 'add-user', true, array( 'id' => 'submit-add-user' ) ); ?>
</form>
<?php endif; ?>
</div>
<?php
require_once ABSPATH . 'wp-admin/admin-footer.php';
