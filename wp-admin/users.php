<?php
require_once('admin.php');
require_once( ABSPATH . WPINC . '/registration.php');

$title = __('Users');
if ( current_user_can('edit_users') )
	$parent_file = 'users.php';
else
	$parent_file = 'profile.php';

$action = $_REQUEST['action'];
$update = '';

if ( empty($_POST) ) {
	$referer = '<input type="hidden" name="wp_http_referer" value="'. wp_specialchars(stripslashes($_SERVER['REQUEST_URI'])) . '" />';
} elseif ( isset($_POST['wp_http_referer']) ) {
	$redirect = remove_query_arg(array('wp_http_referer', 'updated', 'delete_count'), urlencode(stripslashes($_POST['wp_http_referer'])));
	$referer = '<input type="hidden" name="wp_http_referer" value="' . wp_specialchars($redirect) . '" />';
} else {
	$redirect = 'users.php';
}


// WP_User_Search class
// by Mark Jaquith


class WP_User_Search {
	var $results;
	var $search_term;
	var $page;
	var $raw_page;
	var $users_per_page = 50;
	var $first_user;
	var $last_user;
	var $query_limit;
	var $query_from_where;
	var $total_users_for_query = 0;
	var $too_many_total_users = false;
	var $search_errors;

	function WP_User_Search ($search_term = '', $page = '') { // constructor
		$this->search_term = $search_term;
		$this->raw_page = ( '' == $page ) ? false : (int) $page;
		$this->page = (int) ( '' == $page ) ? 1 : $page;

		$this->prepare_query();
		$this->query();
		$this->prepare_vars_for_template_usage();
		$this->do_paging();
	}

	function prepare_query() {
		global $wpdb;
		$this->first_user = ($this->page - 1) * $this->users_per_page;
		$this->query_limit = 'LIMIT ' . $this->first_user . ',' . $this->users_per_page;
		if ( $this->search_term ) {
			$searches = array();
			$search_sql = 'AND (';
			foreach ( array('user_login', 'user_nicename', 'user_email', 'user_url', 'display_name') as $col )
				$searches[] = $col . " LIKE '%$this->search_term%'";
			$search_sql .= implode(' OR ', $searches);
			$search_sql .= ')';
		}
		$this->query_from_where = "FROM $wpdb->users WHERE 1=1 $search_sql";

		if ( !$_GET['update'] && !$this->search_term && !$this->raw_page && $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users") > $this->users_per_page )
			$this->too_many_total_users = sprintf(__('Because this blog has more than %s users, they cannot all be shown on one page.  Use the paging or search functionality in order to find the user you want to edit.'), $this->users_per_page);
	}

	function query() {
		global $wpdb;
		$this->results = $wpdb->get_col('SELECT ID ' . $this->query_from_where . $this->query_limit);

		if ( $this->results )
			$this->total_users_for_query = $wpdb->get_var('SELECT COUNT(ID) ' . $this->query_from_where); // no limit
		else
			$this->search_errors = new WP_Error('no_matching_users_found', __('No matching users were found!'));
	}

	function prepare_vars_for_template_usage() {
		$this->search_term = stripslashes($this->search_term); // done with DB, from now on we want slashes gone
	}

	function do_paging() {
		if ( $this->total_users_for_query > $this->users_per_page ) { // have to page the results
			$prev_page = ( $this->page > 1) ? true : false;
			$next_page = ( ($this->page * $this->users_per_page) < $this->total_users_for_query ) ? true : false;
			$this->paging_text = '';
			if ( $prev_page )
				$this->paging_text .= '<p class="alignleft"><a href="' . add_query_arg(array('usersearch' => $this->search_term, 'userspage' => $this->page - 1), 'users.php?') . '">&laquo; Previous Page</a></p>';
			if ( $next_page )
				$this->paging_text .= '<p class="alignright"><a href="' . add_query_arg(array('usersearch' => $this->search_term, 'userspage' => $this->page + 1), 'users.php?') . '">Next Page &raquo;</a></p>';
			if ( $prev_page || $next_page )
				$this->paging_text .= '<br style="clear:both" />';
		}
	}

	function get_results() {
		return $this->results;
	}

	function page_links() {
		echo $this->paging_text;
	}

	function results_are_paged() {
		if ( $this->paging_text )
			return true;
		return false;
	}

	function is_search() {
		if ( $this->search_term )
			return true;
		return false;
	}
}


switch ($action) {

case 'promote':
	check_admin_referer('bulk-users');

	if (empty($_POST['users'])) {
		header('Location: ' . $redirect);
	}

	if ( !current_user_can('edit_users') )
		die(__('You can&#8217;t edit users.'));

	$userids = $_POST['users'];
	$update = 'promote';
	foreach($userids as $id) {
		if ( ! current_user_can('edit_user', $id) )
			die(__('You can&#8217;t edit that user.'));
		// The new role of the current user must also have edit_users caps
		if($id == $current_user->id && !$wp_roles->role_objects[$_POST['new_role']]->has_cap('edit_users')) {
			$update = 'err_admin_role';
			continue;
		}

		$user = new WP_User($id);
		$user->set_role($_POST['new_role']);
	}

	header('Location: ' . add_query_arg('update', $update, $redirect));

break;

case 'dodelete':

	check_admin_referer('delete-users');

	if ( empty($_POST['users']) ) {
		header('Location: ' . $redirect);
	}

	if ( !current_user_can('delete_users') )
		die(__('You can&#8217;t delete users.'));

	$userids = $_POST['users'];
	$update = 'del';
	$delete_count = 0;

	foreach ( (array) $userids as $id) {
		if ( ! current_user_can('delete_user', $id) )
			die(__('You can&#8217;t delete that user.'));

		if($id == $current_user->id) {
			$update = 'err_admin_del';
			continue;
		}
		switch($_POST['delete_option']) {
		case 'delete':
			wp_delete_user($id);
			break;
		case 'reassign':
			wp_delete_user($id, $_POST['reassign_user']);
			break;
		}
		++$delete_count;
	}

	$redirect = add_query_arg('delete_count', $delete_count, $redirect);

	header('Location: ' . add_query_arg('update', $update, $redirect));

break;

case 'delete':

	check_admin_referer('bulk-users');

	if ( empty($_POST['users']) )
		header('Location: ' . $redirect);

	if ( !current_user_can('delete_users') )
		$errors = new WP_Error('edit_users', __('You can&#8217;t delete users.'));

	$userids = $_POST['users'];

	include ('admin-header.php');
?>
<form action="" method="post" name="updateusers" id="updateusers">
<?php wp_nonce_field('delete-users') ?>
<?php echo $referer; ?>
<div class="wrap">
<h2><?php _e('Delete Users'); ?></h2>
<p><?php _e('You have specified these users for deletion:'); ?></p>
<ul>
<?php
	$go_delete = false;
	foreach ( (array) $userids as $id ) {
		$user = new WP_User($id);
		if ( $id == $current_user->id ) {
			echo "<li>" . sprintf(__('ID #%1s: %2s <strong>The current user will not be deleted.</strong>'), $id, $user->user_login) . "</li>\n";
		} else {
			echo "<li><input type=\"hidden\" name=\"users[]\" value=\"{$id}\" />" . sprintf(__('ID #%1s: %2s'), $id, $user->user_login) . "</li>\n";
			$go_delete = true;
		}
	}
	$all_logins = $wpdb->get_results("SELECT ID, user_login FROM $wpdb->users ORDER BY user_login");
	$user_dropdown = '<select name="reassign_user">';
	foreach ( (array) $all_logins as $login )
		if ( $login->ID == $current_user->id || !in_array($login->ID, $userids) )
			$user_dropdown .= "<option value=\"{$login->ID}\">{$login->user_login}</option>";
	$user_dropdown .= '</select>';
	?>
	</ul>
<?php if ( $go_delete ) : ?>
	<p><?php _e('What should be done with posts and links owned by this user?'); ?></p>
	<ul style="list-style:none;">
		<li><label><input type="radio" id="delete_option0" name="delete_option" value="delete" checked="checked" />
		<?php _e('Delete all posts and links.'); ?></label></li>
		<li><input type="radio" id="delete_option1" name="delete_option" value="reassign" />
		<?php echo '<label for="delete_option1">'.__('Attribute all posts and links to:')."</label> $user_dropdown"; ?></li>
	</ul>
	<input type="hidden" name="action" value="dodelete" />
	<p class="submit"><input type="submit" name="submit" value="<?php _e('Confirm Deletion'); ?>" /></p>
<?php else : ?>
	<p><?php _e('There are no valid users selected for deletion.'); ?></p>
<?php endif; ?>
</div>
</form>
<?php

break;

case 'adduser':
	check_admin_referer('add-user');

	if ( ! current_user_can('create_users') )
		die(__('You can&#8217;t create users.'));

	$user_id = add_user();
	$update = 'add';
	if ( is_wp_error( $user_id ) )
		$add_user_errors = $user_id;
	else {
		$new_user_login = apply_filters('pre_user_login', sanitize_user(stripslashes($_POST['user_login']), true));
		$redirect = add_query_arg('usersearch', $new_user_login, $redirect);
		header('Location: ' . add_query_arg('update', $update, $redirect) . '#user-' . $user_id);
		die();
	}

default:
	wp_enqueue_script('admin-users');

	include('admin-header.php');

	// Query the users
	$wp_user_search = new WP_User_Search($_GET['usersearch'], $_GET['userspage']);

	// Make the user objects
	foreach ( $wp_user_search->get_results() as $userid ) {
		$tmp_user = new WP_User($userid);
		$roles = $tmp_user->roles;
		$role = array_shift($roles);
		$roleclasses[$role][$tmp_user->user_login] = $tmp_user;
	}

	if ( isset($_GET['update']) ) :
		switch($_GET['update']) {
		case 'del':
		case 'del_many':
		?>
			<?php $delete_count = (int) $_GET['delete_count']; ?>
			<div id="message" class="updated fade"><p><?php printf(__('%1$s %2$s deleted.'), $delete_count, __ngettext('user', 'users', $delete_count) ); ?></p></div>
		<?php
			break;
		case 'add':
		?>
			<div id="message" class="updated fade"><p><?php _e('New user created.'); ?></p></div>
		<?php
			break;
		case 'promote':
		?>
			<div id="message" class="updated fade"><p><?php _e('Changed roles.'); ?></p></div>
		<?php
			break;
		case 'err_admin_role':
		?>
			<div id="message" class="error"><p><?php _e("The current user's role must have user editing capabilities."); ?></p></div>
			<div id="message" class="updated fade"><p><?php _e('Other user roles have been changed.'); ?></p></div>
		<?php
			break;
		case 'err_admin_del':
		?>
			<div id="message" class="error"><p><?php _e("You can't delete the current user."); ?></p></div>
			<div id="message" class="updated fade"><p><?php _e('Other users have been deleted.'); ?></p></div>
		<?php
			break;
		}
	endif; ?>

<?php if ( is_wp_error( $errors ) ) : ?>
	<div class="error">
		<ul>
		<?php
			foreach ( $errors->get_error_messages() as $message )
				echo "<li>$message</li>";
		?>
		</ul>
	</div>
<?php endif; ?>

<?php if ( $wp_user_search->too_many_total_users ) : ?>
	<div id="message" class="updated">
		<p><?php echo $wp_user_search->too_many_total_users; ?></p>
	</div>
<?php endif; ?>

<div class="wrap">

	<?php if ( $wp_user_search->is_search() ) : ?>
		<h2><?php printf(__('Users Matching "%s" by Role'), $wp_user_search->search_term); ?></h2>
	<?php else : ?>
		<h2><?php _e('User List by Role'); ?></h2>
	<?php endif; ?>

	<form action="" method="get" name="search" id="search">
		<p><input type="text" name="usersearch" id="usersearch" value="<?php echo wp_specialchars($wp_user_search->search_term); ?>" /> <input type="submit" value="<?php _e('Search for users &raquo;'); ?>" /></p>
	</form>

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
		<p><a href="users.php"><?php _e('&laquo; Back to All Users'); ?></a></p>
	<?php endif; ?>

	<h3><?php printf(__('Results %1$s - %2$s of %3$s shown below'), $wp_user_search->first_user + 1, min($wp_user_search->first_user + $wp_user_search->users_per_page, $wp_user_search->total_users_for_query), $wp_user_search->total_users_for_query); ?></h3>

	<?php if ( $wp_user_search->results_are_paged() ) : ?>
		<div class="user-paging-text"><?php $wp_user_search->page_links(); ?></p></div>
	<?php endif; ?>

<form action="" method="post" name="updateusers" id="updateusers">
<?php wp_nonce_field('bulk-users') ?>
<table class="widefat">
<?php
foreach($roleclasses as $role => $roleclass) {
	ksort($roleclass);
?>

<tr>
<?php if ( !empty($role) ) : ?>
	<th colspan="7" align="left"><h3><?php echo $wp_roles->role_names[$role]; ?></h3></th>
<?php else : ?>
	<th colspan="7" align="left"><h3><em><?php _e('No role for this blog'); ?></h3></th>
<?php endif; ?>
</tr>
<tr class="thead">
	<th style="text-align: left"><?php _e('ID') ?></th>
	<th style="text-align: left"><?php _e('Username') ?></th>
	<th style="text-align: left"><?php _e('Name') ?></th>
	<th style="text-align: left"><?php _e('E-mail') ?></th>
	<th style="text-align: left"><?php _e('Website') ?></th>
	<th colspan="2"><?php _e('Actions') ?></th>
</tr>
</thead>
<tbody id="role-<?php echo $role; ?>"><?php
$style = '';
foreach ( (array) $roleclass as $user_object ) {
	$style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';
	echo "\n\t" . user_row($user_object, $style);
}
?>

</tbody>
<?php } ?>
</table>

<?php if ( $wp_user_search->results_are_paged() ) : ?>
	<div class="user-paging-text"><?php $wp_user_search->page_links(); ?></div>
<?php endif; ?>

	<h2><?php _e('Update Users'); ?></h2>
	<ul style="list-style:none;">
		<li><input type="radio" name="action" id="action0" value="delete" /> <label for="action0"><?php _e('Delete checked users.'); ?></label></li>
		<li>
			<input type="radio" name="action" id="action1" value="promote" /> <label for="action1"><?php _e('Set the Role of checked users to:'); ?></label>
			<select name="new_role"><?php wp_dropdown_roles(); ?></select>
		</li>
	</ul>
	<p class="submit">
		<?php echo $referer; ?>
		<input type="submit" value="<?php _e('Update &raquo;'); ?>" />
	</p>
</form>
<?php endif; ?>
</div>

<?php
	if ( is_wp_error($add_user_errors) ) {
		foreach ( array('user_login' => 'user_login', 'first_name' => 'user_firstname', 'last_name' => 'user_lastname', 'email' => 'user_email', 'url' => 'user_uri', 'role' => 'user_role') as $formpost => $var ) {
			$var = 'new_' . $var;
			$$var = wp_specialchars(stripslashes($_POST[$formpost]));
		}
		unset($name);
	}
?>

<div class="wrap">
<h2 id="add-new-user"><?php _e('Add New User') ?></h2>
<?php echo '<p>'.sprintf(__('Users can <a href="%1$s">register themselves</a> or you can manually create users here.'), get_settings('siteurl').'/wp-register.php').'</p>'; ?>
<form action="#add-new-user" method="post" name="adduser" id="adduser">
<?php wp_nonce_field('add-user') ?>
<table class="editform" width="100%" cellspacing="2" cellpadding="5">
	<tr>
		<th scope="row" width="33%"><?php _e('Nickname') ?><input name="action" type="hidden" id="action" value="adduser" /></th>
		<td width="66%"><input name="user_login" type="text" id="user_login" value="<?php echo $new_user_login; ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><?php _e('First Name') ?> </th>
		<td><input name="first_name" type="text" id="first_name" value="<?php echo $new_user_firstname; ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><?php _e('Last Name') ?> </th>
		<td><input name="last_name" type="text" id="last_name" value="<?php echo $new_user_lastname; ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><?php _e('E-mail') ?></th>
		<td><input name="email" type="text" id="email" value="<?php echo $new_user_email; ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><?php _e('Website') ?></th>
		<td><input name="url" type="text" id="url" value="<?php echo $new_user_uri; ?>" /></td>
	</tr>

<?php if ( apply_filters('show_password_fields', true) ) : ?>
	<tr>
		<th scope="row"><?php _e('Password (twice)') ?> </th>
		<td><input name="pass1" type="password" id="pass1" />
		<br />
		<input name="pass2" type="password" id="pass2" /></td>
	</tr>
<?php endif; ?>

	<tr>
		<th scope="row"><?php _e('Role'); ?></th>
		<td><select name="role" id="role">
			<?php
			if ( !$new_user_role )
				$new_user_role = get_settings('default_role');
			wp_dropdown_roles($new_user_role);
			?>
			</select>
		</td>
	</tr>
</table>
<p class="submit">
	<?php echo $referer; ?>
	<input name="adduser" type="submit" id="addusersub" value="<?php _e('Add User &raquo;') ?>" />
</p>
</form>

<?php if ( is_wp_error( $add_user_errors ) ) : ?>
	<div class="error">
		<ul>
		<?php
			foreach ( $add_user_errors->get_error_messages() as $message )
				echo "$message<br />";
		?>
		</ul>
	</div>
<?php endif; ?>
<div id="ajax-response"></div>
</div>

<?php
break;

} // end of the $action switch

include('admin-footer.php');
?>