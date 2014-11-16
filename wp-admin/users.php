<?php
/**
 * Users administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

if ( ! current_user_can( 'list_users' ) )
	wp_die( __( 'Cheatin&#8217; uh?' ), 403 );

$wp_list_table = _get_list_table('WP_Users_List_Table');
$pagenum = $wp_list_table->get_pagenum();
$title = __('Users');
$parent_file = 'users.php';

add_screen_option( 'per_page', array('label' => _x( 'Users', 'users per page (screen options)' )) );

// contextual help - choose Help on the top right of admin panel to preview this.
get_current_screen()->add_help_tab( array(
	'id'      => 'overview',
	'title'   => __('Overview'),
	'content' => '<p>' . __('This screen lists all the existing users for your site. Each user has one of five defined roles as set by the site admin: Site Administrator, Editor, Author, Contributor, or Subscriber. Users with roles other than Administrator will see fewer options in the dashboard navigation when they are logged in, based on their role.') . '</p>' .
				 '<p>' . __('To add a new user for your site, click the Add New button at the top of the screen or Add New in the Users menu section.') . '</p>'
) ) ;

get_current_screen()->add_help_tab( array(
	'id'      => 'screen-display',
	'title'   => __('Screen Display'),
	'content' => '<p>' . __('You can customize the display of this screen in a number of ways:') . '</p>' .
					'<ul>' .
					'<li>' . __('You can hide/display columns based on your needs and decide how many users to list per screen using the Screen Options tab.') . '</li>' .
					'<li>' . __('You can filter the list of users by User Role using the text links in the upper left to show All, Administrator, Editor, Author, Contributor, or Subscriber. The default view is to show all users. Unused User Roles are not listed.') . '</li>' .
					'<li>' . __('You can view all posts made by a user by clicking on the number under the Posts column.') . '</li>' .
					'</ul>'
) );

$help = '<p>' . __('Hovering over a row in the users list will display action links that allow you to manage users. You can perform the following actions:') . '</p>' .
	'<ul>' .
	'<li>' . __('Edit takes you to the editable profile screen for that user. You can also reach that screen by clicking on the username.') . '</li>';

if ( is_multisite() )
	$help .= '<li>' . __( 'Remove allows you to remove a user from your site. It does not delete their content. You can also remove multiple users at once by using Bulk Actions.' ) . '</li>';
else
	$help .= '<li>' . __( 'Delete brings you to the Delete Users screen for confirmation, where you can permanently remove a user from your site and delete their content. You can also delete multiple users at once by using Bulk Actions.' ) . '</li>';

$help .= '</ul>';

get_current_screen()->add_help_tab( array(
	'id'      => 'actions',
	'title'   => __('Actions'),
	'content' => $help,
) );
unset( $help );

get_current_screen()->set_help_sidebar(
    '<p><strong>' . __('For more information:') . '</strong></p>' .
    '<p>' . __('<a href="http://codex.wordpress.org/Users_Screen" target="_blank">Documentation on Managing Users</a>') . '</p>' .
    '<p>' . __('<a href="http://codex.wordpress.org/Roles_and_Capabilities" target="_blank">Descriptions of Roles and Capabilities</a>') . '</p>' .
    '<p>' . __('<a href="https://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

if ( empty($_REQUEST) ) {
	$referer = '<input type="hidden" name="wp_http_referer" value="'. esc_attr( wp_unslash( $_SERVER['REQUEST_URI'] ) ) . '" />';
} elseif ( isset($_REQUEST['wp_http_referer']) ) {
	$redirect = remove_query_arg(array('wp_http_referer', 'updated', 'delete_count'), wp_unslash( $_REQUEST['wp_http_referer'] ) );
	$referer = '<input type="hidden" name="wp_http_referer" value="' . esc_attr($redirect) . '" />';
} else {
	$redirect = 'users.php';
	$referer = '';
}

$update = '';

/**
 * @since 3.5.0
 * @access private
 */
function delete_users_add_js() { ?>
<script>
jQuery(document).ready( function($) {
	var submit = $('#submit').prop('disabled', true);
	$('input[name=delete_option]').one('change', function() {
		submit.prop('disabled', false);
	});
	$('#reassign_user').focus( function() {
		$('#delete_option1').prop('checked', true).trigger('change');
	});
});
</script>
<?php
}

switch ( $wp_list_table->current_action() ) {

/* Bulk Dropdown menu Role changes */
case 'promote':
	check_admin_referer('bulk-users');

	if ( ! current_user_can( 'promote_users' ) )
		wp_die( __( 'You can&#8217;t edit that user.' ) );

	if ( empty($_REQUEST['users']) ) {
		wp_redirect($redirect);
		exit();
	}

	$editable_roles = get_editable_roles();
	if ( empty( $editable_roles[$_REQUEST['new_role']] ) )
		wp_die(__('You can&#8217;t give users that role.'));

	$userids = $_REQUEST['users'];
	$update = 'promote';
	foreach ( $userids as $id ) {
		$id = (int) $id;

		if ( ! current_user_can('promote_user', $id) )
			wp_die(__('You can&#8217;t edit that user.'));
		// The new role of the current user must also have the promote_users cap or be a multisite super admin
		if ( $id == $current_user->ID && ! $wp_roles->role_objects[ $_REQUEST['new_role'] ]->has_cap('promote_users')
			&& ! ( is_multisite() && is_super_admin() ) ) {
				$update = 'err_admin_role';
				continue;
		}

		// If the user doesn't already belong to the blog, bail.
		if ( is_multisite() && !is_user_member_of_blog( $id ) )
			wp_die( __( 'Cheatin&#8217; uh?' ), 403 );

		$user = get_userdata( $id );
		$user->set_role($_REQUEST['new_role']);
	}

	wp_redirect(add_query_arg('update', $update, $redirect));
	exit();

case 'dodelete':
	if ( is_multisite() )
		wp_die( __('User deletion is not allowed from this screen.') );

	check_admin_referer('delete-users');

	if ( empty($_REQUEST['users']) ) {
		wp_redirect($redirect);
		exit();
	}

	$userids = array_map( 'intval', (array) $_REQUEST['users'] );

	if ( empty( $_REQUEST['delete_option'] ) ) {
		$url = self_admin_url( 'users.php?action=delete&users[]=' . implode( '&users[]=', $userids ) . '&error=true' );
		$url = str_replace( '&amp;', '&', wp_nonce_url( $url, 'bulk-users' ) );
		wp_redirect( $url );
		exit;
	}

	if ( ! current_user_can( 'delete_users' ) )
		wp_die(__('You can&#8217;t delete users.'));

	$update = 'del';
	$delete_count = 0;

	foreach ( $userids as $id ) {
		if ( ! current_user_can( 'delete_user', $id ) )
			wp_die(__( 'You can&#8217;t delete that user.' ) );

		if ( $id == $current_user->ID ) {
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

	$redirect = add_query_arg( array('delete_count' => $delete_count, 'update' => $update), $redirect);
	wp_redirect($redirect);
	exit();

case 'delete':
	if ( is_multisite() )
		wp_die( __('User deletion is not allowed from this screen.') );

	check_admin_referer('bulk-users');

	if ( empty($_REQUEST['users']) && empty($_REQUEST['user']) ) {
		wp_redirect($redirect);
		exit();
	}

	if ( ! current_user_can( 'delete_users' ) )
		$errors = new WP_Error( 'edit_users', __( 'You can&#8217;t delete users.' ) );

	if ( empty($_REQUEST['users']) )
		$userids = array( intval( $_REQUEST['user'] ) );
	else
		$userids = array_map( 'intval', (array) $_REQUEST['users'] );

	add_action( 'admin_head', 'delete_users_add_js' );

	include( ABSPATH . 'wp-admin/admin-header.php' );
?>
<form action="" method="post" name="updateusers" id="updateusers">
<?php wp_nonce_field('delete-users') ?>
<?php echo $referer; ?>

<div class="wrap">
<h2><?php _e('Delete Users'); ?></h2>
<?php if ( isset( $_REQUEST['error'] ) ) : ?>
<div class="error">
	<p><strong><?php _e( 'ERROR:' ); ?></strong> <?php _e( 'Please select an option.' ); ?></p>
</div>
<?php endif; ?>
<p><?php echo _n( 'You have specified this user for deletion:', 'You have specified these users for deletion:', count( $userids ) ); ?></p>
<ul>
<?php
	$go_delete = 0;
	foreach ( $userids as $id ) {
		$user = get_userdata( $id );
		if ( $id == $current_user->ID ) {
			echo "<li>" . sprintf(__('ID #%1$s: %2$s <strong>The current user will not be deleted.</strong>'), $id, $user->user_login) . "</li>\n";
		} else {
			echo "<li><input type=\"hidden\" name=\"users[]\" value=\"" . esc_attr($id) . "\" />" . sprintf(__('ID #%1$s: %2$s'), $id, $user->user_login) . "</li>\n";
			$go_delete++;
		}
	}
	?>
	</ul>
<?php if ( $go_delete ) : ?>
	<fieldset><p><legend><?php echo _n( 'What should be done with content owned by this user?', 'What should be done with content owned by these users?', $go_delete ); ?></legend></p>
	<ul style="list-style:none;">
		<li><label><input type="radio" id="delete_option0" name="delete_option" value="delete" />
		<?php _e('Delete all content.'); ?></label></li>
		<li><input type="radio" id="delete_option1" name="delete_option" value="reassign" />
		<?php echo '<label for="delete_option1">' . __( 'Attribute all content to:' ) . '</label> ';
		wp_dropdown_users( array( 'name' => 'reassign_user', 'exclude' => array_diff( $userids, array($current_user->ID) ) ) ); ?></li>
	</ul></fieldset>
	<?php
	/**
	 * Fires at the end of the delete users form prior to the confirm button.
	 *
	 * @since 4.0.0
	 *
	 * @param WP_User $current_user WP_User object for the user being deleted.
	 */
	do_action( 'delete_user_form', $current_user );
	?>
	<input type="hidden" name="action" value="dodelete" />
	<?php submit_button( __('Confirm Deletion'), 'secondary' ); ?>
<?php else : ?>
	<p><?php _e('There are no valid users selected for deletion.'); ?></p>
<?php endif; ?>
</div>
</form>
<?php

break;

case 'doremove':
	check_admin_referer('remove-users');

	if ( ! is_multisite() )
		wp_die( __( 'You can&#8217;t remove users.' ) );

	if ( empty($_REQUEST['users']) ) {
		wp_redirect($redirect);
		exit;
	}

	if ( ! current_user_can( 'remove_users' ) )
		wp_die( __( 'You can&#8217;t remove users.' ) );

	$userids = $_REQUEST['users'];

	$update = 'remove';
 	foreach ( $userids as $id ) {
		$id = (int) $id;
		if ( $id == $current_user->ID && !is_super_admin() ) {
			$update = 'err_admin_remove';
			continue;
		}
		if ( !current_user_can('remove_user', $id) ) {
			$update = 'err_admin_remove';
			continue;
		}
		remove_user_from_blog($id, $blog_id);
	}

	$redirect = add_query_arg( array('update' => $update), $redirect);
	wp_redirect($redirect);
	exit;

case 'remove':

	check_admin_referer('bulk-users');

	if ( ! is_multisite() )
		wp_die( __( 'You can&#8217;t remove users.' ) );

	if ( empty($_REQUEST['users']) && empty($_REQUEST['user']) ) {
		wp_redirect($redirect);
		exit();
	}

	if ( !current_user_can('remove_users') )
		$error = new WP_Error('edit_users', __('You can&#8217;t remove users.'));

	if ( empty($_REQUEST['users']) )
		$userids = array(intval($_REQUEST['user']));
	else
		$userids = $_REQUEST['users'];

	include( ABSPATH . 'wp-admin/admin-header.php' );
?>
<form action="" method="post" name="updateusers" id="updateusers">
<?php wp_nonce_field('remove-users') ?>
<?php echo $referer; ?>

<div class="wrap">
<h2><?php _e('Remove Users from Site'); ?></h2>
<p><?php _e('You have specified these users for removal:'); ?></p>
<ul>
<?php
	$go_remove = false;
 	foreach ( $userids as $id ) {
		$id = (int) $id;
 		$user = get_userdata( $id );
		if ( $id == $current_user->ID && !is_super_admin() ) {
			echo "<li>" . sprintf(__('ID #%1$s: %2$s <strong>The current user will not be removed.</strong>'), $id, $user->user_login) . "</li>\n";
		} elseif ( !current_user_can('remove_user', $id) ) {
			echo "<li>" . sprintf(__('ID #%1$s: %2$s <strong>You don\'t have permission to remove this user.</strong>'), $id, $user->user_login) . "</li>\n";
		} else {
			echo "<li><input type=\"hidden\" name=\"users[]\" value=\"{$id}\" />" . sprintf(__('ID #%1$s: %2$s'), $id, $user->user_login) . "</li>\n";
			$go_remove = true;
		}
 	}
 	?>
</ul>
<?php if ( $go_remove ) : ?>
		<input type="hidden" name="action" value="doremove" />
		<?php submit_button( __('Confirm Removal'), 'secondary' ); ?>
<?php else : ?>
	<p><?php _e('There are no valid users selected for removal.'); ?></p>
<?php endif; ?>
</div>
</form>
<?php

break;

default:

	if ( !empty($_GET['_wp_http_referer']) ) {
		wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce'), wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
		exit;
	}

	$wp_list_table->prepare_items();
	$total_pages = $wp_list_table->get_pagination_arg( 'total_pages' );
	if ( $pagenum > $total_pages && $total_pages > 0 ) {
		wp_redirect( add_query_arg( 'paged', $total_pages ) );
		exit;
	}

	include( ABSPATH . 'wp-admin/admin-header.php' );

	$messages = array();
	if ( isset($_GET['update']) ) :
		switch($_GET['update']) {
		case 'del':
		case 'del_many':
			$delete_count = isset($_GET['delete_count']) ? (int) $_GET['delete_count'] : 0;
			$messages[] = '<div id="message" class="updated"><p>' . sprintf( _n( 'User deleted.', '%s users deleted.', $delete_count ), number_format_i18n( $delete_count ) ) . '</p></div>';
			break;
		case 'add':
			if ( isset( $_GET['id'] ) && ( $user_id = $_GET['id'] ) && current_user_can( 'edit_user', $user_id ) ) {
				$messages[] = '<div id="message" class="updated"><p>' . sprintf( __( 'New user created. <a href="%s">Edit user</a>' ),
					esc_url( add_query_arg( 'wp_http_referer', urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ),
						self_admin_url( 'user-edit.php?user_id=' . $user_id ) ) ) ) . '</p></div>';
			} else {
				$messages[] = '<div id="message" class="updated"><p>' . __( 'New user created.' ) . '</p></div>';
			}
			break;
		case 'promote':
			$messages[] = '<div id="message" class="updated"><p>' . __('Changed roles.') . '</p></div>';
			break;
		case 'err_admin_role':
			$messages[] = '<div id="message" class="error"><p>' . __('The current user&#8217;s role must have user editing capabilities.') . '</p></div>';
			$messages[] = '<div id="message" class="updated"><p>' . __('Other user roles have been changed.') . '</p></div>';
			break;
		case 'err_admin_del':
			$messages[] = '<div id="message" class="error"><p>' . __('You can&#8217;t delete the current user.') . '</p></div>';
			$messages[] = '<div id="message" class="updated"><p>' . __('Other users have been deleted.') . '</p></div>';
			break;
		case 'remove':
			$messages[] = '<div id="message" class="updated fade"><p>' . __('User removed from this site.') . '</p></div>';
			break;
		case 'err_admin_remove':
			$messages[] = '<div id="message" class="error"><p>' . __("You can't remove the current user.") . '</p></div>';
			$messages[] = '<div id="message" class="updated fade"><p>' . __('Other users have been removed.') . '</p></div>';
			break;
		}
	endif; ?>

<?php if ( isset($errors) && is_wp_error( $errors ) ) : ?>
	<div class="error">
		<ul>
		<?php
			foreach ( $errors->get_error_messages() as $err )
				echo "<li>$err</li>\n";
		?>
		</ul>
	</div>
<?php endif;

if ( ! empty($messages) ) {
	foreach ( $messages as $msg )
		echo $msg;
} ?>

<div class="wrap">
<h2>
<?php
echo esc_html( $title );
if ( current_user_can( 'create_users' ) ) { ?>
	<a href="user-new.php" class="add-new-h2"><?php echo esc_html_x( 'Add New', 'user' ); ?></a>
<?php } elseif ( is_multisite() && current_user_can( 'promote_users' ) ) { ?>
	<a href="user-new.php" class="add-new-h2"><?php echo esc_html_x( 'Add Existing', 'user' ); ?></a>
<?php }

if ( $usersearch )
	printf( '<span class="subtitle">' . __('Search results for &#8220;%s&#8221;') . '</span>', esc_html( $usersearch ) ); ?>
</h2>

<?php $wp_list_table->views(); ?>

<form action="" method="get">

<?php $wp_list_table->search_box( __( 'Search Users' ), 'user' ); ?>

<?php $wp_list_table->display(); ?>
</form>

<br class="clear" />
</div>
<?php
break;

} // end of the $doaction switch

include( ABSPATH . 'wp-admin/admin-footer.php' );
