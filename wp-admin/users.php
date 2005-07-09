<?php
require_once('admin.php');
require_once( ABSPATH . WPINC . '/registration-functions.php');

$title = __('Users');
$parent_file = 'profile.php';
	
$action = $_REQUEST['action'];

switch ($action) {
case 'adduser':
	check_admin_referer();

	$user_login     = wp_specialchars(trim($_POST['user_login']));
	$pass1          = $_POST['pass1'];
	$pass2          = $_POST['pass2'];
	$user_email     = wp_specialchars(trim($_POST['email']));
	$user_firstname = wp_specialchars(trim($_POST['firstname']));
	$user_lastname  = wp_specialchars(trim($_POST['lastname']));
	$user_uri       = wp_specialchars(trim($_POST['uri']));
		
	/* checking that username has been typed */
	if ($user_login == '')
		die (__('<strong>ERROR</strong>: Please enter a username.'));

	/* checking the password has been typed twice */
	do_action('check_passwords', array($user_login, &$pass1, &$pass2));
	if ($pass1 == '' || $pass2 == '')
		die (__('<strong>ERROR</strong>: Please enter your password twice.'));

	/* checking the password has been typed twice the same */
	if ($pass1 != $pass2)
		die (__('<strong>ERROR</strong>: Please type the same password in the two password fields.'));

	$user_nickname = $user_login;

    if ( username_exists( $user_login ) )
		die (__('<strong>ERROR</strong>: This username is already registered, please choose another one.'));

	/* checking e-mail address */
	if (empty($user_email)) {
		die (__("<strong>ERROR</strong>: please type an e-mail address"));
		return false;
	} else if (!is_email($user_email)) {
		die (__("<strong>ERROR</strong>: the email address isn't correct"));
		return false;
	}

	$user_ID = create_user( $user_login, $pass1, $user_email, 0 );

	update_usermeta( $user_ID, 'first_name', $user_firstname);
	update_usermeta( $user_ID, 'last_name', $user_lastname);
	update_usermeta( $user_ID, 'first_name', $user_firstname);
	
	$stars = '';
	for ($i = 0; $i < strlen($pass1); $i = $i + 1)
		$stars .= '*';

	$user_login = stripslashes($user_login);
	$message  = sprintf(__('New user registration on your blog %s:'), get_settings('blogname')) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
	$message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";

	@wp_mail(get_settings('admin_email'), sprintf(__('[%s] New User Registration'), get_settings('blogname')), $message);
	header('Location: users.php');
break;

case 'promote':
	check_admin_referer();

	if (empty($_GET['prom'])) {
		header('Location: users.php');
	}

	$id = (int) $_GET['id'];
	$prom = $_GET['prom'];

	$user_data = get_userdata($id);

	$usertopromote_level = $user_data->user_level;

	if ( $user_level <= $usertopromote_level )
		die(__('Can&#8217;t change the level of a user whose level is higher than yours.'));

	if ('up' == $prom) {
		$new_level = $usertopromote_level + 1;
	} elseif ('down' == $prom) {
		$new_level = $usertopromote_level - 1;
	}
	update_usermeta( $id, $wpdb->prefix . 'user_level', $new_level);

	header('Location: users.php');

break;

case 'delete':

	check_admin_referer();

	$id = (int) $_GET['id'];

	if (!$id) {
		header('Location: users.php');
	}

	$user_data = get_userdata($id);
	$usertodelete_level = $user_data->user_level;

	if ($user_level <= $usertodelete_level)
		die(__('Can&#8217;t delete a user whose level is higher than yours.'));

	wp_delete_user($id);

	header('Location: users.php?deleted=true');

break;

default:
	
	include ('admin-header.php');
	?>

<?php if (isset($_GET['deleted'])) : ?>
<div class="updated"><p><?php _e('User deleted.') ?></p></div>
<?php endif; ?>
<div class="wrap">
  <h2><?php _e('Authors') ?></h2>
  <table cellpadding="3" cellspacing="3" width="100%">
	<tr>
	<th><?php _e('ID') ?></th>
	<th><?php _e('Nickname') ?></th>
	<th><?php _e('Name') ?></th>
	<th><?php _e('E-mail') ?></th>
	<th><?php _e('Website') ?></th>
	<th><?php _e('Level') ?></th>
	<th><?php _e('Posts') ?></th>
	<th>&nbsp;</th>
	</tr>
	<?php
	$authors = 
	$users = get_author_user_ids();
	$style = '';
	foreach ($users as $user) {
		$user_data = get_userdata($user);
		$email = $user_data->user_email;
		$url = $user_data->user_url;
		$short_url = str_replace('http://', '', $url);
		$short_url = str_replace('www.', '', $short_url);
		if ('/' == substr($short_url, -1))
			$short_url = substr($short_url, 0, -1);
		if (strlen($short_url) > 35)
		$short_url =  substr($short_url, 0, 32).'...';
		$style = ('class="alternate"' == $style) ? '' : 'class="alternate"';
		$numposts = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_author = '$user' and post_status = 'publish'");
		if (0 < $numposts) $numposts = "<a href='edit.php?author=$user_data->ID' title='" . __('View posts') . "'>$numposts</a>";
		echo "
<tr $style>
	<td align='center'>$user_data->ID</td>
	<td><strong>$user_data->user_login</strong></td>
	<td>$user_data->first_name $user_data->last_name</td>
	<td><a href='mailto:$email' title='" . sprintf(__('e-mail: %s'), $email) . "'>$email</a></td>
	<td><a href='$url' title='website: $url'>$short_url</a></td>
	<td align='center'>";
	if (($user_level >= 2) and ($user_level > $user_data->user_level) and ($user_data->user_level > 0))
		echo " <a href=\"users.php?action=promote&amp;id=".$user_data->ID."&amp;prom=down\">-</a> ";
	echo $user_data->user_level;
	if (($user_level >= 2) and ($user_level > ($user_data->user_level + 1)))
		echo " <a href=\"users.php?action=promote&amp;id=".$user_data->ID."&amp;prom=up\">+</a> ";
	echo "</td><td align='right'>$numposts</td>";
	echo '<td>';
	if (($user_level >= 2) and ($user_level > $user_data->user_level))
		echo "<a href='user-edit.php?user_id=$user_data->ID' class='edit'>".__('Edit')."</a>";
	echo '</td>';
	echo '</tr>';
	}
	
	?>
	
  </table>
</div>

<?php
$users = get_nonauthor_user_ids();
if ($users) {
?>
<div class="wrap">
	<h2><?php _e('Registered Users') ?></h2>
	<table cellpadding="3" cellspacing="3" width="100%">
	<tr>
		<th><?php _e('ID') ?></th>
		<th><?php _e('Nickname') ?></th>
		<th><?php _e('Name') ?></th>
		<th><?php _e('E-mail') ?></th>
		<th><?php _e('Website') ?></th>
		<th></th>
		<th></th>
		<th></th>
	</tr>
<?php
$style = '';
foreach ($users as $user) {
	$user_data = get_userdata($user);
	$email = $user_data->user_email;
	$url = $user_data->user_url;
	$short_url = str_replace('http://', '', $url);
	$short_url = str_replace('www.', '', $short_url);
	if ('/' == substr($short_url, -1))
		$short_url = substr($short_url, 0, -1);
	if (strlen($short_url) > 35)
	$short_url =  substr($short_url, 0, 32).'...';
	$style = ('class="alternate"' == $style) ? '' : 'class="alternate"';
echo "\n<tr $style>
<td align='center'>$user_data->ID</td>
<td><strong>$user_data->user_login</strong></td>
<td>$user_data->first_name $user_data->last_name</td>
<td><a href='mailto:$email' title='" . sprintf(__('e-mail: %s'), $email) . "'>$email</a></td>
<td><a href='$url' title='website: $url'>$short_url</a></td>
<td align='center'>";

	if ($user_level >= 6)
		echo "<a href='users.php?action=promote&amp;id=$user_data->ID&amp;prom=up' class='edit'>". __('Promote') . '</a>';	
	echo "</td>\n";
	echo '<td>';
	if (($user_level >= 6) and ($user_level > $user_data->user_level))
		echo "<a href='user-edit.php?user_id=$user_data->ID' class='edit'>".__('Edit')."</a>";
	echo '</td><td>';
	if ($user_level >= 6)
		echo "<a href='users.php?action=delete&amp;id=$user_data->ID' class='delete' onclick='return confirm(\"" . __('You are about to delete this user \n  OK to delete, Cancel to stop.') . "\")'>" . __('Delete'). '</a>';
	echo '</td></tr>';

}

?>
	
	</table>
	  <p><?php _e('Deleting a user also deletes all posts made by that user.') ?></p>
</div>

	<?php 
	} ?>
<div class="wrap">
<h2><?php _e('Add New User') ?></h2>
<?php printf(__('<p>Users can <a href="%s/wp-register.php">register themselves</a> or you can manually create users here.</p>'), get_settings('siteurl')); ?>
<form action="" method="post" name="adduser" id="adduser">
  <table class="editform" width="100%" cellspacing="2" cellpadding="5">
    <tr>
      <th scope="row" width="33%"><?php _e('Nickname') ?>
      <input name="action" type="hidden" id="action" value="adduser" /></th>
      <td width="66%"><input name="user_login" type="text" id="user_login" /></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('First Name') ?> </th>
      <td><input name="firstname" type="text" id="firstname" /></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('Last Name') ?> </th>
      <td><input name="lastname" type="text" id="lastname" /></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('E-mail') ?></th>
      <td><input name="email" type="text" id="email" /></td>
    </tr>
    <tr>
      <th scope="row"><?php _e('Website') ?></th>
      <td><input name="uri" type="text" id="uri" /></td>
    </tr>
<?php
$show_password_fields = apply_filters('show_password_fields', true);
if ( $show_password_fields ) :
?>
    <tr>
      <th scope="row"><?php _e('Password (twice)') ?> </th>
      <td><input name="pass1" type="password" id="pass1" />
      <br />
      <input name="pass2" type="password" id="pass2" /></td>
    </tr>
<?php endif; ?>
  </table>
  <p class="submit">
    <input name="adduser" type="submit" id="adduser" value="<?php _e('Add User') ?> &raquo;" />
  </p>
  </form>
</div>
	<?php

break;
}

include('admin-footer.php');
?>
