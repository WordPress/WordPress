<?php
require_once('../wp-includes/wp-l10n.php');

$title = __('Users');
$parent_file = 'users.php';
	
$wpvarstoreset = array('action','standalone','redirect','profile');
for ($i=0; $i<count($wpvarstoreset); $i += 1) {
	$wpvar = $wpvarstoreset[$i];
	if (!isset($$wpvar)) {
		if (empty($_POST["$wpvar"])) {
			if (empty($_GET["$wpvar"])) {
				$$wpvar = '';
			} else {
				$$wpvar = $_GET["$wpvar"];
			}
		} else {
			$$wpvar = $_POST["$wpvar"];
		}
	}
}

switch ($action) {
case 'adduser':
	$standalone = 1;
	require_once('admin-header.php');

	check_admin_referer();

	function filter($value)	{
		return ereg('^[a-zA-Z0-9\_-\|]+$',$value);
	}

	$user_login = $_POST['user_login'];
	$pass1 = $_POST['pass1'];
	$pass2 = $_POST['pass2'];
	$user_email = $_POST['email'];
	$user_firstname = $_POST['firstname'];
	$user_lastname = $_POST['lastname'];
	$user_uri = $_POST['uri'];
		
	/* checking login has been typed */
	if ($user_login == '') {
		die (__('<strong>ERROR</strong>: Please enter a login.'));
	}

	/* checking the password has been typed twice */
	if ($pass1 == '' || $pass2 == '') {
		die (__('<strong>ERROR</strong>: Please enter your password twice.'));
	}

	/* checking the password has been typed twice the same */
	if ($pass1 != $pass2)	{
		die (__('<strong>ERROR</strong>: Please type the same password in the two password fields.'));
	}
	$user_nickname = $user_login;

	/* checking the login isn't already used by another user */
	$loginthere = $wpdb->get_var("SELECT user_login FROM $wpdb->users WHERE user_login = '$user_login'");
    if ($loginthere) {
		die (__('<strong>ERROR</strong>: This login is already registered, please choose another one.'));
	}

	/* checking e-mail address */
	if (empty($_POST["email"])) {
		die (__("<strong>ERROR</strong>: please type an e-mail address"));
		return false;
	} else if (!is_email($_POST["email"])) {
		die (__("<strong>ERROR</strong>: the email address isn't correct"));
		return false;
	}

	$user_ID = $wpdb->get_var("SELECT ID FROM $wpdb->users ORDER BY ID DESC LIMIT 1") + 1;

	$user_nicename = sanitize_title($user_nickname, $user_ID);
	$user_uri = preg_match('/^(https?|ftps?|mailto|news|gopher):/is', $user_uri) ? $user_uri : 'http://' . $user_uri;
	$now = gmdate('Y-m-d H:i:s');
	$new_users_can_blog = get_settings('new_users_can_blog');

	$result = $wpdb->query("INSERT INTO $wpdb->users 
		(user_login, user_pass, user_nickname, user_email, user_ip, user_domain, user_browser, dateYMDhour, user_level, user_idmode, user_firstname, user_lastname, user_nicename, user_url)
	VALUES 
		('$user_login', MD5('$pass1'), '$user_nickname', '$user_email', '$user_ip', '$user_domain', '$user_browser', '$now', '$new_users_can_blog', 'nickname', '$user_firstname', '$user_lastname', '$user_nicename', '$user_uri')");
	
	if ($result == false) {
		die (__('<strong>ERROR</strong>: Couldn&#8217;t register you!'));
	}

	$stars = '';
	for ($i = 0; $i < strlen($pass1); $i = $i + 1) {
		$stars .= '*';
	}

    $user_login = stripslashes($user_login);
	$message  = 'New user registration on your blog ' . get_settings('blogname') . ":\r\n\r\n";
	$message .= "Login: $user_login\r\n\r\nE-mail: $user_email";

	@mail(get_settings('admin_email'), '[' . get_settings('blogname') . '] New User Registration', $message);
	header('Location: users.php');
break;

case 'promote':

	$standalone = 1;
	require_once('admin-header.php');

	check_admin_referer();

	if (empty($_GET['prom'])) {
		header('Location: users.php');
	}

	$id = $_GET['id'];
	$prom = $_GET['prom'];

	$user_data = get_userdata($id);
	$usertopromote_level = $user_data->user_level;

	if ($user_level <= $usertopromote_level) {
		die(__('Can&#8217;t change the level of a user whose level is higher than yours.'));
	}

	if ('up' == $prom) {
		$new_level = $usertopromote_level + 1;
		$sql="UPDATE $wpdb->users SET user_level=$new_level WHERE ID = $id AND $new_level < $user_level";
	} elseif ('down' == $prom) {
		$new_level = $usertopromote_level - 1;
		$sql="UPDATE $wpdb->users SET user_level=$new_level WHERE ID = $id AND $new_level < $user_level";
	}
	$result = $wpdb->query($sql);

	header('Location: users.php');

break;

case 'delete':

	$standalone = 1;
	require_once('admin-header.php');

	check_admin_referer();

	$id = intval($_GET['id']);

	if (!$id) {
		header('Location: users.php');
	}

	$user_data = get_userdata($id);
	$usertodelete_level = $user_data->user_level;

	if ($user_level <= $usertodelete_level)
		die(__('Can&#8217;t delete a user whose level is higher than yours.'));

	$post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_author = $id");
	if ($post_ids) {
		$post_ids = implode(',', $post_ids);
		
		// Delete comments, *backs
		$wpdb->query("DELETE FROM $wpdb->comments WHERE comment_post_ID IN ($post_ids)");
		// Clean cats
		$wpdb->query("DELETE FROM $wpdb->post2cat WHERE post_id IN ($post_ids)");
		// Clean post_meta
		$wpdb->query("DELETE FROM $wpdb->postmeta WHERE post_id IN ($post_ids)");
		// Clean links
		$wpdb->query("DELETE FROM $wpdb->links WHERE link_owner = $id");
		// Delete posts
		$wpdb->query("DELETE FROM $wpdb->posts WHERE post_author = $id");
	}

	// FINALLY, delete user
	$wpdb->query("DELETE FROM $wpdb->users WHERE ID = $id");
	header('Location: users.php?deleted=true');

break;

default:
	
	$standalone = 0;
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
	$users = $wpdb->get_results("SELECT ID FROM $wpdb->users WHERE user_level > 0 ORDER BY ID");
	$style = '';
	foreach ($users as $user) {
		$user_data = get_userdata($user->ID);
		$email = $user_data->user_email;
		$url = $user_data->user_url;
		$short_url = str_replace('http://', '', $url);
		$short_url = str_replace('www.', '', $short_url);
		if ('/' == substr($short_url, -1))
			$short_url = substr($short_url, 0, -1);
		if (strlen($short_url) > 35)
		$short_url =  substr($short_url, 0, 32).'...';
		$style = ('class="alternate"' == $style) ? '' : 'class="alternate"';
		$numposts = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_author = $user->ID and post_status = 'publish'");
		if (0 < $numposts) $numposts = "<a href='edit.php?author=$user_data->ID' title='" . __('View posts') . "'>$numposts</a>";
		echo "
<tr $style>
	<td align='center'>$user_data->ID</td>
	<td><strong>$user_data->user_nickname</strong></td>
	<td>$user_data->user_firstname $user_data->user_lastname</td>
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
		echo "<a href='user-edit.php?user_id=$user_data->ID' class='edit'>Edit</a>";
	echo '</td>';
	echo '</tr>';
	}
	
	?>
	
  </table>
</div>

<?php
	$users = $wpdb->get_results("SELECT * FROM $wpdb->users WHERE user_level = 0 ORDER BY ID");
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
	$user_data = get_userdata($user->ID);
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
<td><strong>$user_data->user_nickname</strong></td>
<td>$user_data->user_firstname $user_data->user_lastname</td>
<td><a href='mailto:$email' title='" . sprintf(__('e-mail: %s'), $email) . "'>$email</a></td>
<td><a href='$url' title='website: $url'>$short_url</a></td>
<td align='center'>";

	if ($user_level >= 6)
		echo "<a href='users.php?action=promote&amp;id=$user_data->ID&amp;prom=up' class='edit'>". __('Promote') . '</a>';	
	echo "</td>\n";
	echo '<td>';
	if (($user_level >= 6) and ($user_level > $user_data->user_level))
		echo "<a href='user-edit.php?user_id=$user_data->ID' class='edit'>Edit</a>";
	echo '</td><td>';
	if ($user_level >= 6)
		echo "<a href='users.php?action=delete&amp;id=$user_data->ID' class='delete'>" . __('Delete'). '</a>';
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
    <tr>
      <th scope="row"><?php _e('Password (twice)') ?> </th>
      <td><input name="pass1" type="password" id="pass1" />
      <br />
      <input name="pass2" type="password" id="pass2" /></td>
    </tr>
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