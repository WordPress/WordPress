<?php
/**
 * Edit Site Users Administration Screen
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.1.0
 */

/** Load WordPress Administration Bootstrap */
require_once( './admin.php' );

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
		'<p>' . __('<strong>Info</strong> - The domain and path are rarely edited as this can cause the site to not work properly. The Registered date and Last Updated date are displayed. Network admins can mark a site as archived, spam, deleted and mature, to remove from public listings or disable.') . '</p>' .
		'<p>' . __('<strong>Users</strong> - This displays the users associated with this site. You can also change their role, reset their password, or remove them from the site. Removing the user from the site does not remove the user from the network.') . '</p>' .
		'<p>' . sprintf( __('<strong>Themes</strong> - This area shows themes that are not already enabled across the network. Enabling a theme in this menu makes it accessible to this site. It does not activate the theme, but allows it to show in the site&#8217;s Appearance menu. To enable a theme for the entire network, see the <a href="%s">Network Themes</a> screen.' ), network_admin_url( 'themes.php' ) ) . '</p>' .
		'<p>' . __('<strong>Settings</strong> - This page shows a list of all settings associated with this site. Some are created by WordPress and others are created by plugins you activate. Note that some fields are grayed out and say Serialized Data. You cannot modify these values due to the way the setting is stored in the database.') . '</p>'
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Network_Admin_Sites_Screens" target="_blank">Documentation on Site Management</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/forum/multisite/" target="_blank">Support Forums</a>') . '</p>'
);

$_SERVER['REQUEST_URI'] = remove_query_arg( 'update', $_SERVER['REQUEST_URI'] );
$referer = remove_query_arg( 'update', wp_get_referer() );

$id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;

if ( ! $id )
	wp_die( __('Invalid site ID.') );

$details = get_blog_details( $id );
if ( !can_edit_network( $details->site_id ) )
	wp_die( __( 'You do not have permission to access this page.' ) );

$is_main_site = is_main_site( $id );

// get blog prefix
$blog_prefix = $wpdb->get_blog_prefix( $id );

// @todo This is a hack. Eventually, add API to WP_Roles allowing retrieval of roles for a particular blog.
if ( ! empty($wp_roles->use_db) ) {
	$editblog_roles = get_blog_option( $id, "{$blog_prefix}user_roles" );
} else {
	// Roles are stored in memory, not the DB.
	$editblog_roles = $wp_roles->roles;
}
$default_role = get_blog_option( $id, 'default_role' );

$action = $wp_list_table->current_action();

if ( $action ) {
	switch_to_blog( $id );

	switch ( $action ) {
		case 'newuser':
			check_admin_referer( 'add-user', '_wpnonce_add-new-user' );
			$user = $_POST['user'];
			if ( !is_array( $_POST['user'] ) || empty( $user['username'] ) || empty( $user['email'] ) ) {
				$update = 'err_new';
			} else {
				$password = wp_generate_password( 12, false);
				$user_id = wpmu_create_user( esc_html( strtolower( $user['username'] ) ), $password, esc_html( $user['email'] ) );

				if ( false == $user_id ) {
		 			$update = 'err_new_dup';
				} else {
					wp_new_user_notification( $user_id, $password );
					add_user_to_blog( $id, $user_id, $_POST['new_role'] );
					$update = 'newuser';
				}
			}
			break;

		case 'adduser':
			check_admin_referer( 'add-user', '_wpnonce_add-user' );
			if ( !empty( $_POST['newuser'] ) ) {
				$update = 'adduser';
				$newuser = $_POST['newuser'];
				$userid = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . $wpdb->users . " WHERE user_login = %s", $newuser ) );
				if ( $userid ) {
					$user = $wpdb->get_var( "SELECT user_id FROM " . $wpdb->usermeta . " WHERE user_id='$userid' AND meta_key='{$blog_prefix}capabilities'" );
					if ( $user == false )
						add_user_to_blog( $id, $userid, $_POST['new_role'] );
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
			if ( !current_user_can('remove_users')  )
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
					if ( !is_user_member_of_blog( $user_id ) )
						wp_die(__('Cheatin&#8217; uh?'));

					$user = new WP_User( $user_id );
					$user->set_role( $_REQUEST['new_role'] );
				}
			} else {
				$update = 'err_promote';
			}
			break;
	}

	restore_current_blog();
	wp_redirect( add_query_arg( 'update', $update, $referer ) );
	exit();
}

if ( isset( $_GET['action'] ) && 'update-site' == $_GET['action'] ) {
	wp_redirect( $referer );
	exit();
}

add_screen_option( 'per_page', array( 'label' => _x( 'Users', 'users per page (screen options)' ) ) );

$site_url_no_http = preg_replace( '#^http(s)?://#', '', get_blogaddress_by_id( $id ) );
$title_site_url_linked = sprintf( __('Edit Site: <a href="%1$s">%2$s</a>'), get_blogaddress_by_id( $id ), $site_url_no_http );
$title = sprintf( __('Edit Site: %s'), $site_url_no_http );

$parent_file = 'sites.php';
$submenu_file = 'sites.php';

require('../admin-header.php'); ?>

<div class="wrap">
<?php screen_icon('ms-admin'); ?>
<h2 id="edit-site"><?php echo $title_site_url_linked ?></h2>
<h3 class="nav-tab-wrapper">
<?php
$tabs = array(
	'site-info'     => array( 'label' => __( 'Info' ),     'url' => 'site-info.php'     ),
	'site-users'    => array( 'label' => __( 'Users' ),    'url' => 'site-users.php'    ),
	'site-themes'   => array( 'label' => __( 'Themes' ),   'url' => 'site-themes.php'   ),
	'site-settings' => array( 'label' => __( 'Settings' ), 'url' => 'site-settings.php' ),
);
foreach ( $tabs as $tab_id => $tab ) {
	$class = ( $tab['url'] == $pagenow ) ? ' nav-tab-active' : '';
	echo '<a href="' . $tab['url'] . '?id=' . $id .'" class="nav-tab' . $class . '">' .  esc_html( $tab['label'] ) . '</a>';
}
?>
</h3><?php

if ( isset($_GET['update']) ) :
	switch($_GET['update']) {
	case 'adduser':
		echo '<div id="message" class="updated"><p>' . __( 'User added.' ) . '</p></div>';
		break;
	case 'err_add_member':
		echo '<div id="message" class="error"><p>' . __( 'User is already a member of this site.' ) . '</p></div>';
		break;
	case 'err_add_notfound':
		echo '<div id="message" class="error"><p>' . __( 'Enter the username of an existing user.' ) . '</p></div>';
		break;
	case 'promote':
		echo '<div id="message" class="updated"><p>' . __( 'Changed roles.' ) . '</p></div>';
		break;
	case 'err_promote':
		echo '<div id="message" class="error"><p>' . __( 'Select a user to change role.' ) . '</p></div>';
		break;
	case 'remove':
		echo '<div id="message" class="updated"><p>' . __( 'User removed from this site.' ) . '</p></div>';
		break;
	case 'err_remove':
		echo '<div id="message" class="error"><p>' . __( 'Select a user to remove.' ) . '</p></div>';
		break;
	case 'newuser':
		echo '<div id="message" class="updated"><p>' . __( 'User created.' ) . '</p></div>';
		break;
	case 'err_new':
		echo '<div id="message" class="error"><p>' . __( 'Enter the username and email.' ) . '</p></div>';
		break;
	case 'err_new_dup':
		echo '<div id="message" class="error"><p>' . __( 'Duplicated username or email address.' ) . '</p></div>';
		break;
	}
endif; ?>

<form class="search-form" action="" method="get">
<?php $wp_list_table->search_box( __( 'Search Users' ), 'user' ); ?>
<input type="hidden" name="id" value="<?php echo esc_attr( $id ) ?>" />
</form>

<?php $wp_list_table->views(); ?>

<form method="post" action="site-users.php?action=update-site">
	<?php wp_nonce_field( 'edit-site' ); ?>
	<input type="hidden" name="id" value="<?php echo esc_attr( $id ) ?>" />

<?php $wp_list_table->display(); ?>

</form>

<?php do_action( 'network_site_users_after_list_table', '' );?>

<?php if ( current_user_can( 'promote_users' ) && apply_filters( 'show_network_site_users_add_existing_form', true ) ) : ?>
<h4 id="add-user"><?php _e('Add User to This Site') ?></h4>
	<?php if ( current_user_can( 'create_users' ) && apply_filters( 'show_network_site_users_add_new_form', true ) ) : ?>
<p><?php _e( 'You may add from existing network users, or set up a new user to add to this site.' ); ?></p>
	<?php else : ?>
<p><?php _e( 'You may add from existing network users to this site.' ); ?></p>
	<?php endif; ?>
<h5 id="add-existing-user"><?php _e('Add Existing User') ?></h5>
<form action="site-users.php?action=adduser" id="adduser" method="post">
	<?php wp_nonce_field( 'edit-site' ); ?>
	<input type="hidden" name="id" value="<?php echo esc_attr( $id ) ?>" />
	<table class="form-table">
		<tr>
			<th scope="row"><?php _e( 'Username' ); ?></th>
			<td><input type="text" class="regular-text" name="newuser" id="newuser" /></td>
		</tr>
		<tr>
			<th scope="row"><?php _e( 'Role'); ?></th>
			<td><select name="new_role" id="new_role_0">
			<?php
			reset( $editblog_roles );
			foreach ( $editblog_roles as $role => $role_assoc ){
				$name = translate_user_role( $role_assoc['name'] );
				$selected = ( $role == $default_role ) ? 'selected="selected"' : '';
				echo '<option ' . $selected . ' value="' . esc_attr( $role ) . '">' . esc_html( $name ) . '</option>';
			}
			?>
			</select></td>
		</tr>
	</table>
	<?php wp_nonce_field( 'add-user', '_wpnonce_add-user' ) ?>
	<?php submit_button( __('Add User'), 'primary', 'add-user', false, array( 'id' => 'submit-add-existing-user' ) ); ?>
</form>
<?php endif; ?>

<?php if ( current_user_can( 'create_users' ) && apply_filters( 'show_network_site_users_add_new_form', true ) ) : ?>
<h5 id="add-new-user"><?php _e('Add New User') ?></h5>
<form action="<?php echo network_admin_url('site-users.php?action=newuser'); ?>" id="newuser" method="post">
	<?php wp_nonce_field( 'edit-site' ); ?>
	<input type="hidden" name="id" value="<?php echo esc_attr( $id ) ?>" />
	<table class="form-table">
		<tr>
			<th scope="row"><?php _e( 'Username' ) ?></th>
			<td><input type="text" class="regular-text" name="user[username]" /></td>
		</tr>
		<tr>
			<th scope="row"><?php _e( 'Email' ) ?></th>
			<td><input type="text" class="regular-text" name="user[email]" /></td>
		</tr>
		<tr>
			<th scope="row"><?php _e( 'Role'); ?></th>
			<td><select name="new_role" id="new_role_0">
			<?php
			reset( $editblog_roles );
			foreach ( $editblog_roles as $role => $role_assoc ){
				$name = translate_user_role( $role_assoc['name'] );
				$selected = ( $role == $default_role ) ? 'selected="selected"' : '';
				echo '<option ' . $selected . ' value="' . esc_attr( $role ) . '">' . esc_html( $name ) . '</option>';
			}
			?>
			</select></td>
		</tr>
		<tr class="form-field">
			<td colspan="2"><?php _e( 'Username and password will be mailed to the above email address.' ) ?></td>
		</tr>
	</table>
	<?php wp_nonce_field( 'add-user', '_wpnonce_add-new-user' ) ?>
	<?php submit_button( __('Add New User'), 'primary', 'add-user', false, array( 'id' => 'submit-add-user' ) ); ?>
</form>
<?php endif; ?>
</div>
<?php
require('../admin-footer.php');
