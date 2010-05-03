<?php
/**
 * Users administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('./admin.php');

/** WordPress Registration API */
require_once( ABSPATH . WPINC . '/registration.php');

if ( !current_user_can('list_users') )
	wp_die(__('Cheatin&#8217; uh?'));

$title = __('Users');
$parent_file = 'users.php';

$update = $doaction = '';
if ( isset($_REQUEST['action']) )
	$doaction = $_REQUEST['action'] ? $_REQUEST['action'] : $_REQUEST['action2'];

if ( empty($doaction) ) {
	if ( isset($_GET['changeit']) && !empty($_GET['new_role']) )
		$doaction = 'promote';
}

if ( empty($_REQUEST) ) {
	$referer = '<input type="hidden" name="wp_http_referer" value="'. esc_attr(stripslashes($_SERVER['REQUEST_URI'])) . '" />';
} elseif ( isset($_REQUEST['wp_http_referer']) ) {
	$redirect = remove_query_arg(array('wp_http_referer', 'updated', 'delete_count'), stripslashes($_REQUEST['wp_http_referer']));
	$referer = '<input type="hidden" name="wp_http_referer" value="' . esc_attr($redirect) . '" />';
} else {
	$redirect = 'users.php';
	$referer = '';
}

switch ($doaction) {

/* Bulk Dropdown menu Role changes */
case 'promote':
	check_admin_referer('bulk-users');

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
		if ( ! current_user_can('promote_user', $id) )
			wp_die(__('You can&#8217;t edit that user.'));
		// The new role of the current user must also have promote_users caps
		if ( $id == $current_user->ID && !$wp_roles->role_objects[$_REQUEST['new_role']]->has_cap('promote_users') ) {
			$update = 'err_admin_role';
			continue;
		}

		$user = new WP_User($id);
		$user->set_role($_REQUEST['new_role']);
	}

	wp_redirect(add_query_arg('update', $update, $redirect));
	exit();

break;

case 'dodelete':
	if ( is_multisite() )
		wp_die( __('User deletion is not allowed from this screen.') );

	check_admin_referer('delete-users');

	if ( empty($_REQUEST['users']) ) {
		wp_redirect($redirect);
		exit();
	}

	if ( ! current_user_can( 'delete_users' ) )
		wp_die(__('You can&#8217;t delete users.'));

	$userids = $_REQUEST['users'];
	$update = 'del';
	$delete_count = 0;

	foreach ( (array) $userids as $id) {
		if ( ! current_user_can( 'delete_user', $id ) )
			wp_die(__( 'You can&#8217;t delete that user.' ) );

		if ( $id == $current_user->ID ) {
			$update = 'err_admin_del';
			continue;
		}
		switch ( $_REQUEST['delete_option'] ) {
		case 'delete':
			if ( current_user_can('delete_user', $id) )
				wp_delete_user($id);
			break;
		case 'reassign':
			if ( current_user_can('delete_user', $id) )
				wp_delete_user($id, $_REQUEST['reassign_user']);
			break;
		}
		++$delete_count;
	}

	$redirect = add_query_arg( array('delete_count' => $delete_count, 'update' => $update), $redirect);
	wp_redirect($redirect);
	exit();

break;

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
		$userids = array(intval($_REQUEST['user']));
	else
		$userids = $_REQUEST['users'];

	include ('admin-header.php');
?>
<form action="" method="post" name="updateusers" id="updateusers">
<?php wp_nonce_field('delete-users') ?>
<?php echo $referer; ?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php _e('Delete Users'); ?></h2>
<p><?php _e('You have specified these users for deletion:'); ?></p>
<ul>
<?php
	$go_delete = false;
	foreach ( (array) $userids as $id ) {
		$id = (int) $id;
		$user = new WP_User($id);
		if ( $id == $current_user->ID ) {
			echo "<li>" . sprintf(__('ID #%1s: %2s <strong>The current user will not be deleted.</strong>'), $id, $user->user_login) . "</li>\n";
		} else {
			echo "<li><input type=\"hidden\" name=\"users[]\" value=\"" . esc_attr($id) . "\" />" . sprintf(__('ID #%1s: %2s'), $id, $user->user_login) . "</li>\n";
			$go_delete = true;
		}
	}
	// @todo Delete is always for !is_multisite(). Use API.
	if ( !is_multisite() ) {
		$all_logins = $wpdb->get_results("SELECT ID, user_login FROM $wpdb->users ORDER BY user_login");
	} else {
		// WPMU only searches users of current blog
		$all_logins = $wpdb->get_results("SELECT ID, user_login FROM $wpdb->users, $wpdb->usermeta WHERE $wpdb->users.ID = $wpdb->usermeta.user_id AND meta_key = '".$wpdb->prefix."capabilities' ORDER BY user_login");
	}
	$user_dropdown = '<select name="reassign_user">';
	foreach ( (array) $all_logins as $login )
		if ( $login->ID == $current_user->ID || !in_array($login->ID, $userids) )
			$user_dropdown .= "<option value=\"" . esc_attr($login->ID) . "\">{$login->user_login}</option>";
	$user_dropdown .= '</select>';
	?>
	</ul>
<?php if ( $go_delete ) : ?>
	<fieldset><p><legend><?php _e('What should be done with posts and links owned by this user?'); ?></legend></p>
	<ul style="list-style:none;">
		<li><label><input type="radio" id="delete_option0" name="delete_option" value="delete" checked="checked" />
		<?php _e('Delete all posts and links.'); ?></label></li>
		<li><input type="radio" id="delete_option1" name="delete_option" value="reassign" />
		<?php echo '<label for="delete_option1">'.__('Attribute all posts and links to:')."</label> $user_dropdown"; ?></li>
	</ul></fieldset>
	<input type="hidden" name="action" value="dodelete" />
	<p class="submit"><input type="submit" name="submit" value="<?php esc_attr_e('Confirm Deletion'); ?>" class="button-secondary" /></p>
<?php else : ?>
	<p><?php _e('There are no valid users selected for deletion.'); ?></p>
<?php endif; ?>
</div>
</form>
<?php

break;

case 'doremove':
	check_admin_referer('remove-users');

	if ( empty($_REQUEST['users']) ) {
		wp_redirect($redirect);
		exit;
	}

	if ( !current_user_can('remove_users')  )
		die(__('You can&#8217;t remove users.'));

	$userids = $_REQUEST['users'];

	$update = 'remove';
 	foreach ( $userids as $id ) {
		$id = (int) $id;
		if ( $id == $current_user->id && !is_super_admin() ) {
			$update = 'err_admin_remove';
			continue;
		}
		if ( !current_user_can('delete_user', $id) ) {
			$update = 'err_admin_remove';
			continue;
		}
		remove_user_from_blog($id, $blog_id);
	}

	$redirect = add_query_arg( array('update' => $update), $redirect);
	wp_redirect($redirect);
	exit;

break;

case 'remove':

	check_admin_referer('bulk-users');

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

	include ('admin-header.php');
?>
<form action="" method="post" name="updateusers" id="updateusers">
<?php wp_nonce_field('remove-users') ?>
<?php echo $referer; ?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php _e('Remove Users from Site'); ?></h2>
<p><?php _e('You have specified these users for removal:'); ?></p>
<ul>
<?php
	$go_remove = false;
 	foreach ( $userids as $id ) {
		$id = (int) $id;
 		$user = new WP_User($id);
		if ( $id == $current_user->id && !is_super_admin() ) {
			echo "<li>" . sprintf(__('ID #%1s: %2s <strong>The current user will not be removed.</strong>'), $id, $user->user_login) . "</li>\n";
		} elseif ( !current_user_can('remove_user', $id) ) {
			echo "<li>" . sprintf(__('ID #%1s: %2s <strong>You don\'t have permission to remove this user.</strong>'), $id, $user->user_login) . "</li>\n";
		} else {
			echo "<li><input type=\"hidden\" name=\"users[]\" value=\"{$id}\" />" . sprintf(__('ID #%1s: %2s'), $id, $user->user_login) . "</li>\n";
			$go_remove = true;
		}
 	}
 	?>
<?php if ( $go_remove ) : ?>
		<input type="hidden" name="action" value="doremove" />
		<p class="submit"><input type="submit" name="submit" value="<?php esc_attr_e('Confirm Removal'); ?>" class="button-secondary" /></p>
<?php else : ?>
	<p><?php _e('There are no valid users selected for removal.'); ?></p>
<?php endif; ?>
</div>
</form>
<?php

break;

default:

	if ( !empty($_GET['_wp_http_referer']) ) {
		wp_redirect(remove_query_arg(array('_wp_http_referer', '_wpnonce'), stripslashes($_SERVER['REQUEST_URI'])));
		exit;
	}

	include('./admin-header.php');

	$usersearch = isset($_GET['usersearch']) ? $_GET['usersearch'] : null;
	$userspage = isset($_GET['userspage']) ? $_GET['userspage'] : null;
	$role = isset($_GET['role']) ? $_GET['role'] : null;

	// Query the user IDs for this page
	$wp_user_search = new WP_User_Search($usersearch, $userspage, $role);

	// Query the post counts for this page
	$post_counts = count_many_users_posts($wp_user_search->get_results());

	// Query the users for this page
	cache_users($wp_user_search->get_results());

	$messages = array();
	if ( isset($_GET['update']) ) :
		switch($_GET['update']) {
		case 'del':
		case 'del_many':
			$delete_count = isset($_GET['delete_count']) ? (int) $_GET['delete_count'] : 0;
			$messages[] = '<div id="message" class="updated"><p>' . sprintf(_n('%s user deleted', '%s users deleted', $delete_count), $delete_count) . '</p></div>';
			break;
		case 'add':
			$messages[] = '<div id="message" class="updated"><p>' . __('New user created.') . '</p></div>';
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
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); if ( current_user_can( 'create_users' ) ) { ?>  <a href="user-new.php" class="button add-new-h2"><?php echo esc_html_x('Add New', 'user'); ?></a><?php }
if ( isset($_GET['usersearch']) && $_GET['usersearch'] )
	printf( '<span class="subtitle">' . __('Search results for &#8220;%s&#8221;') . '</span>', esc_html( $_GET['usersearch'] ) ); ?>
</h2>

<div class="filter">
<form id="list-filter" action="" method="get">
<ul class="subsubsub">
<?php
$users_of_blog = count_users();
$total_users = $users_of_blog['total_users'];
$avail_roles =& $users_of_blog['avail_roles'];
unset($users_of_blog);

$current_role = false;
$class = empty($role) ? ' class="current"' : '';
$role_links = array();
$role_links[] = "<li><a href='users.php'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_users, 'users' ), number_format_i18n( $total_users ) ) . '</a>';
foreach ( $wp_roles->get_names() as $this_role => $name ) {
	if ( !isset($avail_roles[$this_role]) )
		continue;

	$class = '';

	if ( $this_role == $role ) {
		$current_role = $role;
		$class = ' class="current"';
	}

	$name = translate_user_role( $name );
	/* translators: User role name with count */
	$name = sprintf( __('%1$s <span class="count">(%2$s)</span>'), $name, $avail_roles[$this_role] );
	$role_links[] = "<li><a href='users.php?role=$this_role'$class>$name</a>";
}
echo implode( " |</li>\n", $role_links) . '</li>';
unset($role_links);
?>
</ul>
</form>
</div>

<form class="search-form" action="" method="get">
<p class="search-box">
	<label class="screen-reader-text" for="user-search-input"><?php _e( 'Search Users' ); ?>:</label>
	<input type="text" id="user-search-input" name="usersearch" value="<?php echo esc_attr($wp_user_search->search_term); ?>" />
	<input type="submit" value="<?php esc_attr_e( 'Search Users' ); ?>" class="button" />
</p>
</form>

<form id="posts-filter" action="" method="get">
<div class="tablenav">

<?php if ( $wp_user_search->results_are_paged() ) : ?>
	<div class="tablenav-pages"><?php $wp_user_search->page_links(); ?></div>
<?php endif; ?>

<div class="alignleft actions">
<select name="action">
<option value="" selected="selected"><?php _e('Bulk Actions'); ?></option>
<?php if ( !is_multisite() && current_user_can('delete_users') ) { ?>
<option value="delete"><?php _e('Delete'); ?></option>
<?php } else { ?>
<option value="remove"><?php _e('Remove'); ?></option>
<?php } ?>
</select>
<input type="submit" value="<?php esc_attr_e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" />
<label class="screen-reader-text" for="new_role"><?php _e('Change role to&hellip;') ?></label><select name="new_role" id="new_role"><option value=''><?php _e('Change role to&hellip;') ?></option><?php wp_dropdown_roles(); ?></select>
<input type="submit" value="<?php esc_attr_e('Change'); ?>" name="changeit" class="button-secondary" />
<?php wp_nonce_field('bulk-users'); ?>
</div>

<br class="clear" />
</div>

	<?php if ( is_wp_error( $wp_user_search->search_errors ) ) : ?>
		<div class="error">
			<ul>
			<?php
				foreach ( $wp_user_search->search_errors->get_error_messages() as $message )
					echo "<li>$message</li>";
			?>
			</ul>
		</div>
	<?php endif; ?>


<?php if ( $wp_user_search->get_results() ) : ?>

	<?php if ( $wp_user_search->is_search() ) : ?>
		<p><a href="users.php"><?php _e('&larr; Back to All Users'); ?></a></p>
	<?php endif; ?>

<table class="widefat fixed" cellspacing="0">
<thead>
<tr class="thead">
<?php print_column_headers('users') ?>
</tr>
</thead>

<tfoot>
<tr class="thead">
<?php print_column_headers('users', false) ?>
</tr>
</tfoot>

<tbody id="users" class="list:user user-list">
<?php
$style = '';
foreach ( $wp_user_search->get_results() as $userid ) {
	$user_object = new WP_User($userid);
	$roles = $user_object->roles;
	$role = array_shift($roles);

	if ( is_multisite() && empty( $role ) )
		continue;

	$style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';
	echo "\n\t", user_row( $user_object, $style, $role, $post_counts[ $userid ] );
}
?>
</tbody>
</table>

<div class="tablenav">

<?php if ( $wp_user_search->results_are_paged() ) : ?>
	<div class="tablenav-pages"><?php $wp_user_search->page_links(); ?></div>
<?php endif; ?>

<div class="alignleft actions">
<select name="action2">
<option value="" selected="selected"><?php _e('Bulk Actions'); ?></option>
<?php if ( !is_multisite() && current_user_can('delete_users') ) { ?>
<option value="delete"><?php _e('Delete'); ?></option>
<?php } else { ?>
<option value="remove"><?php _e('Remove'); ?></option>
<?php } ?></select>
<input type="submit" value="<?php esc_attr_e('Apply'); ?>" name="doaction2" id="doaction2" class="button-secondary action" />
</div>

<br class="clear" />
</div>

<?php endif; ?>

</form>
</div>

<?php
if ( is_multisite() ) {
	foreach ( array('user_login' => 'user_login', 'first_name' => 'user_firstname', 'last_name' => 'user_lastname', 'email' => 'user_email', 'url' => 'user_uri', 'role' => 'user_role') as $formpost => $var ) {
		$var = 'new_' . $var;
		$$var = isset($_REQUEST[$formpost]) ? esc_attr(stripslashes($_REQUEST[$formpost])) : '';
	}
	unset($name);
}
?>

<br class="clear" />
<?php
break;

} // end of the $doaction switch

include('./admin-footer.php');
?>
