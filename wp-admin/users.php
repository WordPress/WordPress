<?php
require_once('../wp-includes/wp-l10n.php');

$title = __('Users');
/* <Team> */
	
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
	function filter($value)	{
		return ereg('^[a-zA-Z0-9\_-\|]+$',$value);
	}

	$user_login = $_POST['user_login'];
	$pass1 = $_POST['pass1'];
	$pass2 = $_POST['pass2'];
	$user_email = $_POST['email'];
	$user_firstname = $_POST['firstname'];
	$user_lastname = $_POST['lastname'];
		
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
	$loginthere = $wpdb->get_var("SELECT user_login FROM $tableusers WHERE user_login = '$user_login'");
    if ($loginthere) {
		die (__('<strong>ERROR</strong>: This login is already registered, please choose another one.'));
	}


	$user_login = addslashes(stripslashes($user_login));
	$pass1 = addslashes(stripslashes($pass1));
	$user_nickname = addslashes(stripslashes($user_nickname));
    $user_nicename = sanitize_title($user_nickname);
	$user_firstname = addslashes(stripslashes($user_firstname));
	$user_lastname = addslashes(stripslashes($user_lastname));
	$now = gmdate('Y-m-d H:i:s');
	$new_users_can_blog = get_settings('new_users_can_blog');

	$result = $wpdb->query("INSERT INTO $tableusers 
		(user_login, user_pass, user_nickname, user_email, user_ip, user_domain, user_browser, dateYMDhour, user_level, user_idmode, user_firstname, user_lastname, user_nicename)
	VALUES 
		('$user_login', MD5('$pass1'), '$user_nickname', '$user_email', '$user_ip', '$user_domain', '$user_browser', '$now', '$new_users_can_blog', 'nickname', '$user_firstname', '$user_lastname', '$user_nicename')");
	
	if ($result == false) {
		die (__('<strong>ERROR</strong>: Couldn&#8217;t register you!'));
	}

	$stars = '';
	for ($i = 0; $i < strlen($pass1); $i = $i + 1) {
		$stars .= '*';
	}

	$message  = 'New user registration on your blog ' . get_settings('blogname') . ":\r\n\r\n";
	$message .= "Login: $user_login\r\n\r\nE-mail: $user_email";

	@mail(get_settings('admin_email'), '[' . get_settings('blogname') . '] New User Registration', $message);
	header('Location: users.php');
break;

case 'promote':

	$standalone = 1;
	require_once('admin-header.php');

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
		$sql="UPDATE $tableusers SET user_level=$new_level WHERE ID = $id AND $new_level < $user_level";
	} elseif ('down' == $prom) {
		$new_level = $usertopromote_level - 1;
		$sql="UPDATE $tableusers SET user_level=$new_level WHERE ID = $id AND $new_level < $user_level";
	}
	$result = $wpdb->query($sql);

	header('Location: users.php');

break;

case 'delete':

	$standalone = 1;
	require_once('admin-header.php');

	$id = intval($_GET['id']);

	if (!$id) {
		header('Location: users.php');
	}

	$user_data = get_userdata($id);
	$usertodelete_level = $user_data->user_level;

	if ($user_level <= $usertodelete_level)
		die(__('Can&#8217;t delete a user whose level is higher than yours.'));

	$post_ids = $wpdb->get_col("SELECT ID FROM $tableposts WHERE post_author = $id");
	if ($post_ids) {
		$post_ids = implode(',', $post_ids);
		
		// Delete comments, *backs
		$wpdb->query("DELETE FROM $tablecomments WHERE comment_post_ID IN ($post_ids)");
		// Clean cats
		$wpdb->query("DELETE FROM $tablepost2cat WHERE post_id IN ($post_ids)");
		// Clean post_meta
		$wpdb->query("DELETE FROM $tablepostmeta WHERE post_id IN ($post_ids)");
		// Clean links
		$wpdb->query("DELETE FROM $tablelinks WHERE link_owner = $id");
		// Delete posts
		$wpdb->query("DELETE FROM $tableposts WHERE post_author = $id");
	}

	// FINALLY, delete user
	$wpdb->query("DELETE FROM $tableusers WHERE ID = $id");
	header('Location: users.php?deleted=true');

break;

default:
	
	$standalone = 0;
	include ('admin-header.php');
	?>
<?php if ($_GET['deleted']) : ?>
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
	</tr>
	<?php
	$users = $wpdb->get_results("SELECT ID FROM $tableusers WHERE user_level > 0 ORDER BY ID");
	foreach ($users as $user) {
		$user_data = get_userdata($user->ID);
		$email = $user_data->user_email;
		$url = $user_data->user_url;
		$short_url = str_replace('http://', '', stripslashes($url));
		$short_url = str_replace('www.', '', $short_url);
		if ('/' == substr($short_url, -1))
			$short_url = substr($short_url, 0, -1);
		if (strlen($short_url) > 35)
		$short_url =  substr($short_url, 0, 32).'...';
		$style = ('class="alternate"' == $style) ? '' : 'class="alternate"';
		$numposts = $wpdb->get_var("SELECT COUNT(*) FROM $tableposts WHERE post_author = $user->ID and post_status = 'publish'");
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
		echo " <a href=\"users.php?action=promote&id=".$user_data->ID."&prom=down\">-</a> ";
	echo $user_data->user_level;
	if (($user_level >= 2) and ($user_level > ($user_data->user_level + 1)))
		echo " <a href=\"users.php?action=promote&id=".$user_data->ID."&prom=up\">+</a> ";
	echo "<td align='right'>$numposts</td>";
	echo '</tr>';
	}
	
	?>
	
  </table>
</div>

<?php
	$users = $wpdb->get_results("SELECT * FROM $tableusers WHERE user_level = 0 ORDER BY ID");
	if ($users) {
?>
<div class="wrap">
	<h2><?php _e('Users') ?></h2>
	<table cellpadding="3" cellspacing="3" width="100%">
	<tr>
		<th><?php _e('ID') ?></th>
		<th><?php _e('Nickname') ?></th>
		<th><?php _e('Name') ?></th>
		<th><?php _e('E-mail') ?></th>
		<th><?php _e('Website') ?></th>
		<th><?php _e('Level') ?></th>
	</tr>
	<?php
	foreach ($users as $user) {
		$user_data = get_userdata($user->ID);
		$email = $user_data->user_email;
		$url = $user_data->user_url;
		$short_url = str_replace('http://', '', stripslashes($url));
		$short_url = str_replace('www.', '', $short_url);
		if ('/' == substr($short_url, -1))
			$short_url = substr($short_url, 0, -1);
		if (strlen($short_url) > 35)
		$short_url =  substr($short_url, 0, 32).'...';
		$style = ('class="alternate"' == $style) ? '' : 'class="alternate"';
echo "\n<tr $style>
<td align='center'>$user_data->ID</td>
<td><strong>$user_data->user_nickname</td>
<td>$user_data->user_firstname $user_data->user_lastname</td>
<td><a href='mailto:$email' title='" . sprintf(__('e-mail: %s'), $email) . "'>$email</a></td>
<td><a href='$url' title='website: $url'>$short_url</a></td>
<td align='center'>";
		if ($user_level >= 3)
			echo " <a href=\"users.php?action=delete&id=".$user_data->ID."\" style=\"color:red;font-weight:bold;\">X</a> ";
		echo $user_data->user_level;
		if ($user_level >= 2)
			echo " <a href=\"users.php?action=promote&id=".$user_data->ID."&prom=up\">+</a> ";	
		echo "</td>\n</tr>\n";
	}
	?>
	
	</table>
	  <?php _e('<p>To delete a user, bring his level to zero, then click on the red X.<br />
    <strong>Warning:</strong> deleting a user also deletes all posts made by this user.</p>') ?>
</div>

	<?php 
	} ?>
<div class="wrap">
<h2><?php _e('Add User') ?></h2>
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
    <input name="adduser" type="submit" id="adduser" value="<?php _e('Add User') ?> &raquo;">
  </p>
  </form>
</div>
	<?php

break;
}
	
/* </Team> */
include('admin-footer.php');
?>
