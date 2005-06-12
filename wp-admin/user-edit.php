<?php
require_once('admin.php');

$title = __('Edit User');
$parent_file = 'profile.php';	
$submenu_file = 'users.php';

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
	die (__("<strong>ERROR</strong>: please enter your nickname (can be the same as your username)"));
	return false;
}

$new_user_login  = wp_specialchars($_POST['new_user_login']);
$pass1 = $_POST['pass1'];
$pass2 = $_POST['pass2'];
do_action('check_passwords', array($new_user_login, &$pass1, &$pass2));

if ( '' == $pass1 ) {
	if ( '' != $pass2 )
		die (__("<strong>ERROR</strong>: you typed your new password only once. Go back to type it twice."));
	$updatepassword = '';
} else {
	if ( '' == $pass2)
		die (__("<strong>ERROR</strong>: you typed your new password only once. Go back to type it twice."));
	if ( $pass1 != $pass2 )
		die (__("<strong>ERROR</strong>: you typed two different passwords. Go back to correct that."));
	$new_pass = $pass1;
	$updatepassword = "user_pass=MD5('$new_pass'), ";
}

$new_firstname   = wp_specialchars($_POST['new_firstname']);
$new_lastname    = wp_specialchars($_POST['new_lastname']);
$new_nickname    = $_POST['new_nickname'];
$new_nicename    = sanitize_title($new_nickname, $user_id);
$new_icq         = wp_specialchars($_POST['new_icq']);
$new_aim         = wp_specialchars($_POST['new_aim']);
$new_msn         = wp_specialchars($_POST['new_msn']);
$new_yim         = wp_specialchars($_POST['new_yim']);
$new_email       = wp_specialchars($_POST['new_email']);
$new_url         = wp_specialchars($_POST['new_url']);
$new_url         = preg_match('/^(https?|ftps?|mailto|news|gopher):/is', $new_url) ? $new_url : 'http://' . $new_url; 
$new_idmode      = wp_specialchars($_POST['new_idmode']);
$new_description = $_POST['new_description'];

$result = $wpdb->query("UPDATE $wpdb->users SET user_login = '$new_user_login', $updatepassword user_email='$new_email', user_url='$new_url', user_nicename = '$new_nicename' WHERE ID = '$user_id'");

update_user_meta( $user_ID, 'first_name', $new_firstname );
update_user_meta( $user_ID, 'last_name', $new_lastname );
update_user_meta( $user_ID, 'nickname', $new_nickname );
update_user_meta( $user_ID, 'description', $new_description );
update_user_meta( $user_ID, 'icq', $new_icq );
update_user_meta( $user_ID, 'aim', $new_aim );
update_user_meta( $user_ID, 'msn', $new_msn );
update_user_meta( $user_ID, 'yim', $new_yim );

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
		<th width="33%" scope="row"><?php _e('Username:') ?></th>
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
<?php if ( '0000-00-00 00:00:00' != $edituser->user_registered ) { ?>
	<tr>
		<th scope="row"><?php _e('Registered on:') ?></th>
		<td><?php echo substr($edituser->user_registered, 0, 11); ?></td>
	</tr>
<?php } ?>
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
<?php
$show_password_fields = apply_filters('show_password_fields', true);
if ( $show_password_fields ) :
?>
	<tr>
		<th scope="row"><?php _e('New <strong>Password</strong> (Leave blank to stay the same.)') ?></th>
		<td><input type="password" name="pass1" size="16" value="" />
			<br />
			<input type="password" name="pass2" size="16" value="" /></td>
	</tr>
<?php endif; ?>
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

include('admin-footer.php');
?>
