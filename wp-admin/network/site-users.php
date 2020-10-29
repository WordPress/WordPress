<?php
/**
 * Edit Site Users Administration Screen
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.1.0
 */

/** Load WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

if ( ! is_multisite() )
	wp_die( __( 'Multisite support is not enabled.' ) );

if ( ! current_user_can('manage_sites') )
	wp_die(__('You do not have sufficient permissions to edit this site.'));

$wp_list_table = _get_list_table('WP_Users_List_Table');
$wp_list_table->prepare_items();

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
	'<p>' . __('<a href="https://codex.wordpress.org/Network_Admin_Sites_Screen" target="_blank">Documentation on Site Management</a>') . '</p>' .
	'<p>' . __('<a href="https://wordpress.org/support/forum/multisite/" target="_blank">Support Forums</a>') . '</p>'
);

get_current_screen()->set_screen_reader_content( array(
	'heading_views'      => __( 'Filter site users list' ),
	'heading_pagination' => __( 'Site users list navigation' ),
	'heading_list'       => __( 'Site users list' ),
) );

$_SERVER['REQUEST_URI'] = remove_query_arg( 'update', $_SERVER['REQUEST_URI'] );
$referer = remove_query_arg( 'update', wp_get_referer() );

if ( ! empty( $_REQUEST['paged'] ) ) {
	$referer = add_query_arg( 'paged', (int) $_REQUEST['paged'], $referer );
}

$id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;

if ( ! $id )
	wp_die( __('Invalid site ID.') );

$details = get_blog_details( $id );
if ( ! $details ) {
	wp_die( __( 'The requested site does not exist.' ) );
}

if ( ! can_edit_network( $details->site_id ) )
	wp_die( __( 'You do not have permission to access this page.' ), 403 );

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
				$password = wp_generate_password( 12, false);
				$user_id = wpmu_create_user( esc_html( strtolower( $user['username'] ) ), $password, esc_html( $user['email'] ) );

				if ( false === $user_id ) {
		 			$update = 'err_new_dup';
				} else {
					add_user_to_blog( $id, $user_id, $_POST['new_role'] );
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
			break;

		case 'adduser':
			check_admin_referer( 'add-user', '_wpnonce_add-user' );
			if ( !empty( $_POST['newuser'] ) ) {
				$update = 'adduser';
				$newuser = $_POST['newuser'];
				$user = get_user_by( 'login', $newuser );
				if ( $user && $user->exists() ) {
					if ( ! is_user_member_of_blog( $user->ID, $id ) )
						add_user_to_blog( $id, $user->ID, $_POST['new_role'] );
					else
						$update = 'err_add_member';
				} else {
					$update = 'err_add_notfound';
				}
			} else {
				$update = 'err_add_notfound';
			}
			break;

		case 'remove':
			if ( ! current_user_can( 'remove_users' )  )
				die(__('You can&#8217;t remove users.'));
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
			if ( empty( $editable_roles[$_REQUEST['new_role']] ) )
				wp_die(__('You can&#8217;t give users that role.'));

			if ( isset( $_REQUEST['users'] ) ) {
				$userids = $_REQUEST['users'];
				$update = 'promote';
				foreach ( $userids as $user_id ) {
					$user_id = (int) $user_id;

					// If the user doesn't already belong to the blog, bail.
					if ( ! is_user_member_of_blog( $user_id ) ) {
						wp_die(
							'<h1>' . __( 'Cheatin&#8217; uh?' ) . '</h1>' .
							'<p>' . __( 'One of the selected users is not a member of this site.' ) . '</p>',
							403
						);
					}

					$user = get_userdata( $user_id );
					$user->set_role( $_REQUEST['new_role'] );
				}
			} else {
				$update = 'err_promote';
			}
			break;
	}

	wp_safe_redirect( add_query_arg( 'update', $update, $referer ) );
	exit();
}

restore_current_blog();

if ( isset( $_GET['action'] ) && 'update-site' == $_GET['action'] ) {
	wp_safe_redirect( $referer );
	exit();
}

add_screen_option( 'per_page' );

$title = sprintf( __( 'Edit Site: %s' ), esc_html( $details->blogname ) );

$parent_file = 'sites.php';
$submenu_file = 'sites.php';

/**
 * Filter whether to show the Add Existing User form on the Multisite Users screen.
 *
 * @since 3.1.0
 *
 * @param bool $bool Whether to show the Add Existing User form. Default true.
 */
if ( ! wp_is_large_network( 'users' ) && apply_filters( 'show_network_site_users_add_existing_form', true ) )
	wp_enqueue_script( 'user-suggest' );

require( ABSPATH . 'wp-admin/admin-header.php' ); ?>

<script type="text/javascript">
var current_site_id = <?php echo absint( $id ); ?>;
</script>


<div class="wrap">
<h1 id="edit-site"><?php echo $title; ?></h1>
<p class="edit-site-actions"><a href="<?php echo esc_url( get_home_url( $id, '/' ) ); ?>"><?php _e( 'Visit' ); ?></a> | <a href="<?php echo esc_url( get_admin_url( $id ) ); ?>"><?php _e( 'Dashboard' ); ?></a></p>
<h2 class="nav-tab-wrapper nav-tab-small">
<?php
$tabs = array(
	'site-info'     => array( 'label' => __( 'Info' ),     'url' => 'site-info.php'     ),
	'site-users'    => array( 'label' => __( 'Users' ),    'url' => 'site-users.php'    ),
	'site-themes'   => array( 'label' => __( 'Themes' ),   'url' => 'site-themes.php'   ),
	'site-settings' => array( 'label' => __( 'Settings' ), 'url' => 'site-settings.php' ),
);
foreach ( $tabs as $tab_id => $tab ) {
	$class = ( $tab['url'] == $pagenow ) ? ' nav-tab-active' : '';
	echo '<a href="' . $tab['url'] . '?id=' . $id .'" class="nav-tab' . $class . '">' . esc_html( $tab['label'] ) . '</a>';
}
?>
</h2><?php

if ( isset($_GET['update']) ) :
	switch($_GET['update']) {
	case 'adduser':
		echo '<div id="message" class="updated notice is-dismissible"><p>' . __( 'User added.' ) . '</p></div>';
		break;
	case 'err_add_member':
		echo '<div id="message" class="error notice is-dismissible"><p>' . __( 'User is already a member of this site.' ) . '</p></div>';
		break;
	case 'err_add_notfound':
		echo '<div id="message" class="error notice is-dismissible"><p>' . __( 'Enter the username of an existing user.' ) . '</p></div>';
		break;
	case 'promote':
		echo '<div id="message" class="updated notice is-dismissible"><p>' . __( 'Changed roles.' ) . '</p></div>';
		break;
	case 'err_promote':
		echo '<div id="message" class="error notice is-dismissible"><p>' . __( 'Select a user to change role.' ) . '</p></div>';
		break;
	case 'remove':
		echo '<div id="message" class="updated notice is-dismissible"><p>' . __( 'User removed from this site.' ) . '</p></div>';
		break;
	case 'err_remove':
		echo '<div id="message" class="error notice is-dismissible"><p>' . __( 'Select a user to remove.' ) . '</p></div>';
		break;
	case 'newuser':
		echo '<div id="message" class="updated notice is-dismissible"><p>' . __( 'User created.' ) . '</p></div>';
		break;
	case 'err_new':
		echo '<div id="message" class="error notice is-dismissible"><p>' . __( 'Enter the username and email.' ) . '</p></div>';
		break;
	case 'err_new_dup':
		echo '<div id="message" class="error notice is-dismissible"><p>' . __( 'Duplicated username or email address.' ) . '</p></div>';
		break;
	}
endif; ?>

<form class="search-form" method="get">
<?php $wp_list_table->search_box( __( 'Search Users' ), 'user' ); ?>
<input type="hidden" name="id" value="<?php echo esc_attr( $id ) ?>" />
</form>

<?php $wp_list_table->views(); ?>

<form method="post" action="site-users.php?action=update-site">
	<input type="hidden" name="id" value="<?php echo esc_attr( $id ) ?>" />

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
if ( current_user_can( 'promote_users' ) && apply_filters( 'show_network_site_users_add_existing_form', true ) ) : ?>
<h2 id="add-existing-user"><?php _e( 'Add Existing User' ); ?></h2>
<form action="site-users.php?action=adduser" id="adduser" method="post">
	<input type="hidden" name="id" value="<?php echo esc_attr( $id ) ?>" />
	<table class="form-table">
		<tr>
			<th scope="row"><label for="newuser"><?php _e( 'Username' ); ?></label></th>
			<td><input type="text" class="regular-text wp-suggest-user" name="newuser" id="newuser" /></td>
		</tr>
		<tr>
			<th scope="row"><label for="new_role_adduser"><?php _e( 'Role' ); ?></label></th>
			<td><select name="new_role" id="new_role_adduser">
			<?php wp_dropdown_roles( get_option( 'default_role' ) ); ?>
			</select></td>
		</tr>
	</table>
	<?php wp_nonce_field( 'add-user', '_wpnonce_add-user' ) ?>
	<?php submit_button( __( 'Add User' ), 'primary', 'add-user', true, array( 'id' => 'submit-add-existing-user' ) ); ?>
</form>
<?php endif; ?>

<?php
/**
 * Filter whether to show the Add New User form on the Multisite Users screen.
 *
 * @since 3.1.0
 *
 * @param bool $bool Whether to show the Add New User form. Default true.
 */
if ( current_user_can( 'create_users' ) && apply_filters( 'show_network_site_users_add_new_form', true ) ) : ?>
<h2 id="add-new-user"><?php _e( 'Add New User' ); ?></h2>
<form action="<?php echo network_admin_url('site-users.php?action=newuser'); ?>" id="newuser" method="post">
	<input type="hidden" name="id" value="<?php echo esc_attr( $id ) ?>" />
	<table class="form-table">
		<tr>
			<th scope="row"><label for="user_username"><?php _e( 'Username' ) ?></label></th>
			<td><input type="text" class="regular-text" name="user[username]" id="user_username" /></td>
		</tr>
		<tr>
			<th scope="row"><label for="user_email"><?php _e( 'Email' ) ?></label></th>
			<td><input type="text" class="regular-text" name="user[email]" id="user_email" /></td>
		</tr>
		<tr>
			<th scope="row"><label for="new_role_newuser"><?php _e( 'Role' ); ?></label></th>
			<td><select name="new_role" id="new_role_newuser">
			<?php wp_dropdown_roles( get_option( 'default_role' ) ); ?>
			</select></td>
		</tr>
		<tr class="form-field">
			<td colspan="2"><?php _e( 'A password reset link will be sent to the user via email.' ) ?></td>
		</tr>
	</table>
	<?php wp_nonce_field( 'add-user', '_wpnonce_add-new-user' ) ?>
	<?php submit_button( __( 'Add New User' ), 'primary', 'add-user', true, array( 'id' => 'submit-add-user' ) ); ?>
</form>
<?php endif; ?>
</div>
<?php
require( ABSPATH . 'wp-admin/admin-footer.php' );
