<?php
require_once('admin.php');

$title = __('Edit User');

$wpvarstoreset = array('action', 'redirect', 'profile', 'user_id');
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
case 'update':

get_currentuserinfo();
$edituser = get_userdata($user_id);
if ($edituser->user_level >= $user_level) die( __('You do not have permission to edit this user.') );

/* checking the nickname has been typed */
if (empty($_POST["new_nickname"])) {
	die (__("<strong>ERROR</strong>: please enter your nickname (can be the same as your login)"));
	return false;
}

if ($_POST['pass1'] == '') {
	if ($_POST['pass2'] != '')
		die (__("<strong>ERROR</strong>: you typed your new password only once. Go back to type it twice."));
	$updatepassword = '';
} else {
	if ($_POST['pass2'] == "")
		die (__("<strong>ERROR</strong>: you typed your new password only once. Go back to type it twice."));
	if ($_POST['pass1'] != $_POST['pass2'])
		die (__("<strong>ERROR</strong>: you typed two different passwords. Go back to correct that."));
	$new_pass = $_POST["pass1"];
	$updatepassword = "user_pass=MD5('$new_pass'), ";
}

$new_user_login  = $_POST['new_user_login'];
$new_firstname   = $_POST['new_firstname'];
$new_lastname    = $_POST['new_lastname'];
$new_nickname    = $_POST['new_nickname'];
$new_nicename    = sanitize_title($new_nickname, $user_id);
$new_icq         = $_POST['new_icq'];
$new_aim         = $_POST['new_aim'];
$new_msn         = $_POST['new_msn'];
$new_yim         = $_POST['new_yim'];
$new_email       = $_POST['new_email'];
$new_url         = $_POST['new_url'];
$new_url         = preg_match('/^(https?|ftps?|mailto|news|gopher):/is', $new_url) ? $new_url : 'http://' . $new_url; 
$new_idmode      = $_POST['new_idmode'];
$new_description = $_POST['new_description'];

$result = $wpdb->query("UPDATE $wpdb->users SET user_login = '$new_user_login', user_firstname = '$new_firstname', $updatepassword user_lastname='$new_lastname', user_nickname='$new_nickname', user_icq='$new_icq', user_email='$new_email', user_url='$new_url', user_aim='$new_aim', user_msn='$new_msn', user_yim='$new_yim', user_idmode='$new_idmode', user_description = '$new_description', user_nicename = '$new_nicename' WHERE ID = $user_id");

header("Location: user-edit.php?user_id=$user_id&updated=true");

break;

case 'switchposts':

check_admin_referer();

/* TODO: Switch all posts from one user to another user */

break;

default:
	
include ('admin-header.php');

$edituser = get_userdata($user_id);

if ($edituser->user_level >= $user_level) die( __('You do not have permission to edit this user.') );
?>
<ul id="adminmenu2">
	<li><a href="users.php" class="current"><?php _e('Authors &amp; Users') ?></a></li>
    <li><a href="profile.php"><?php _e('Your Profile') ?></a></li>
</ul>

<?php if ( isset($_GET['updated']) ) : ?>
<div class="updated">
	<p><strong><?php _e('User updated.') ?></strong></p>
</div>
<?php endif; ?>

<div class="wrap">
<h2><?php _e('Edit User'); ?></h2>
<form name="edituser" id="edituser" action="user-edit.php" method="post">
<table width="99%"  border="0" cellspacing="2" cellpadding="3">
	<tr>
		<th width="33%" scope="row"><?php _e('Login:') ?></th>
		<td width="73%"><input type="text" name="new_user_login" id="new_user_login" value="<?php echo $edituser->user_login; ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><?php _e('Level:') ?></th>
		<td><?php echo $edituser->user_level; ?></td>
	</tr>
	<tr>
		<th scope="row"><?php _e('Posts:') ?></th>
		<td><?php echo get_usernumposts($edituser->ID); ?></td>
	</tr>
	<tr>
		<th scope="row"><?php _e('First name:') ?></th>
		<td><input type="text" name="new_firstname" id="new_firstname" value="<?php echo $edituser->user_firstname ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><?php _e('Last name:') ?></th>
		<td><input type="text" name="new_lastname" id="new_lastname2" value="<?php echo $edituser->user_lastname ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><?php _e('Profile:') ?></th>
		<td><textarea name="new_description" rows="5" id="new_description" style="width: 99%; "><?php echo $edituser->user_description ?></textarea></td>
	</tr>
	<tr>
		<th scope="row"><?php _e('Nickname:') ?></th>
		<td><input type="text" name="new_nickname" id="new_nickname" value="<?php echo $edituser->user_nickname ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><?php _e('E-mail:') ?></th>
		<td><input type="text" name="new_email" id="new_email" value="<?php echo $edituser->user_email ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><?php _e('Website:') ?></th>
		<td><input type="text" name="new_url" id="new_url" value="<?php echo $edituser->user_url ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><?php _e('ICQ:') ?></th>
		<td><input type="text" name="new_icq" id="new_icq" value="<?php if ($edituser->user_icq > 0) { echo $edituser->user_icq; } ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><?php _e('AIM:') ?></th>
		<td><input type="text" name="new_aim" id="new_aim" value="<?php echo $edituser->user_aim ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><?php _e('MSN IM:') ?>
		</th>
		<td><input type="text" name="new_msn" id="new_msn" value="<?php echo $edituser->user_msn ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><?php _e('Yahoo IM:') ?>
		</th>
		<td><input type="text" name="new_yim" id="new_yim" value="<?php echo $edituser->user_yim ?>" />
		</td>
	</tr>
	<tr>
		<th scope="row"><?php _e('Identity on blog:') ?>
		</th>
		<td><select name="new_idmode">
				<option value="nickname"<?php
	if ($edituser->user_idmode == 'nickname')
	echo ' selected="selected"'; ?>><?php echo $edituser->user_nickname ?></option>
				<option value="login"<?php
	if ($edituser->user_idmode=="login")
	echo ' selected="selected"'; ?>><?php echo $edituser->user_login ?></option>
				<option value="firstname"<?php
	if ($edituser->user_idmode=="firstname")
	echo ' selected="selected"'; ?>><?php echo $edituser->user_firstname ?></option>
				<option value="lastname"<?php
	if ($edituser->user_idmode=="lastname")
	echo ' selected="selected"'; ?>><?php echo $edituser->user_lastname ?></option>
				<option value="namefl"<?php
	if ($edituser->user_idmode=="namefl")
	echo ' selected="selected"'; ?>><?php echo $edituser->user_firstname." ".$edituser->user_lastname ?></option>
				<option value="namelf"<?php
	if ($edituser->user_idmode=="namelf")
	echo ' selected="selected"'; ?>><?php echo $edituser->user_lastname." ".$edituser->user_firstname ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php _e('New <strong>Password</strong> (Leave blank to stay the same.)') ?></th>
		<td><input type="password" name="pass1" size="16" value="" />
			<br />
			<input type="password" name="pass2" size="16" value="" /></td>
	</tr>
</table>
  <p class="submit">
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>" />
    <input type="submit" value="<?php _e('Update User &raquo;') ?>" name="submit" />
  </p>
</form>
</div>

<?php
break;
}
	
/* </Team> */
include('admin-footer.php');
?>
