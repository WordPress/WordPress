<?php 
require_once('admin.php');

if ( $_POST['action'] == 'update' ) {

	check_admin_referer();

	/* if the ICQ UIN has been entered, check to see if it has only numbers */
	if (!empty($_POST["newuser_icq"])) {
		if ((ereg("^[0-9]+$",$_POST["newuser_icq"]))==false) {
			die (__("<strong>ERROR</strong>: your ICQ UIN can only be a number, no letters allowed"));
			return false;
		}
	}

	/* checking e-mail address */
	if (empty($_POST["newuser_email"])) {
		die (__("<strong>ERROR</strong>: please type your e-mail address"));
		return false;
	} else if (!is_email($_POST["newuser_email"])) {
		die (__("<strong>ERROR</strong>: the e-mail address isn't correct"));
		return false;
	}

	$pass1 = $_POST['pass1'];
	$pass2 = $_POST['pass2'];
	do_action('check_passwords', array($user_login, &$pass1, &$pass2));

	if ( '' == $pass1 ) {
		if ( '' != $pass2 )
			die (__('<strong>ERROR</strong>: you typed your new password only once. Go back to type it twice.'));
		$updatepassword = '';
	} else {
		if ('' == $pass2)
			die (__('<strong>ERROR</strong>: you typed your new password only once. Go back to type it twice.'));
		if ( $pass1 != $pass2 )
			die (__('<strong>ERROR</strong>: you typed two different passwords. Go back to correct that.'));
		$newuser_pass = $pass1;
		$updatepassword = "user_pass=MD5('$newuser_pass'), ";
		wp_clearcookie();
		wp_setcookie($user_login, $newuser_pass);
	}

	$newuser_firstname = wp_specialchars($_POST['newuser_firstname']);
	$newuser_lastname = wp_specialchars($_POST['newuser_lastname']);
	$new_display_name = wp_specialchars($_POST['display_name']);
	$newuser_nickname = $_POST['newuser_nickname'];
	$newuser_nicename = sanitize_title($newuser_nickname);
	$newuser_icq = wp_specialchars($_POST['newuser_icq']);
	$newuser_aim = wp_specialchars($_POST['newuser_aim']);
	$newuser_msn = wp_specialchars($_POST['newuser_msn']);
	$newuser_yim = wp_specialchars($_POST['newuser_yim']);
	$newuser_email = wp_specialchars($_POST['newuser_email']);
	$newuser_url = wp_specialchars($_POST['newuser_url']);
	$newuser_url = preg_match('/^(https?|ftps?|mailto|news|gopher):/is', $newuser_url) ? $newuser_url : 'http://' . $newuser_url; 
	$user_description = $_POST['user_description'];

	$result = $wpdb->query("UPDATE $wpdb->users SET $updatepassword user_email='$newuser_email', user_url='$newuser_url', user_nicename = '$newuser_nicename', display_name = '$new_display_name' WHERE ID = $user_ID");

	update_usermeta( $user_ID, 'first_name', $newuser_firstname );
	update_usermeta( $user_ID, 'last_name', $newuser_lastname );
	update_usermeta( $user_ID, 'nickname', $newuser_nickname );
	update_usermeta( $user_ID, 'description', $user_description );
	update_usermeta( $user_ID, 'icq', $newuser_icq );
	update_usermeta( $user_ID, 'aim', $newuser_aim );
	update_usermeta( $user_ID, 'msn', $newuser_msn );
	update_usermeta( $user_ID, 'yim', $newuser_yim );

	do_action('profile_update', $user_ID);

	wp_redirect('profile.php?updated=true');
	exit;
}

$title = 'Profile';

$parent_file = 'profile.php';
include_once('admin-header.php');
$profileuser = new WP_User($user_ID);
$profiledata = &$profileuser->data;

$bookmarklet_height= 440;
?>

<?php if ( isset($_GET['updated']) ) { ?>
<div id="message" class="updated fade">
<p><strong><?php _e('Profile updated.') ?></strong></p>
</div>
<?php } ?>

<div class="wrap">
<h2><?php _e('Your Profile'); ?></h2>
<form name="profile" id="your-profile" action="profile.php" method="post">
<p>
<input type="hidden" name="action" value="update" />
<input type="hidden" name="checkuser_id" value="<?php echo $user_ID ?>" />
</p>

<fieldset>
<legend><?php _e('Name'); ?></legend>
<p><label><?php _e('Username: (no editing)'); ?><br />
<input type="text" name="username" value="<?php echo $profiledata->user_login; ?>" disabled="disabled" />
</label></p>
<p><label><?php _e('First name:') ?><br />
<input type="text" name="newuser_firstname" id="newuser_firstname" value="<?php echo $profiledata->first_name ?>" /></label></p>

<p><label><?php _e('Last name:') ?><br />
<input type="text" name="newuser_lastname" id="newuser_lastname2" value="<?php echo $profiledata->last_name ?>" /></label></p>

<p><label><?php _e('Nickname:') ?><br />
<input type="text" name="newuser_nickname" id="newuser_nickname2" value="<?php echo $profiledata->nickname ?>" /></label></p>

</p><label><?php _e('Display name publicly as:') ?> <br />
<select name="display_name">
<option value="<?php echo $profiledata->display_name; ?>"><?php echo $profiledata->display_name; ?></option>
<option value="<?php echo $profiledata->nickname ?>"><?php echo $profiledata->nickname ?></option>
<option value="<?php echo $profiledata->user_login ?>"><?php echo $profiledata->user_login ?></option>
<?php if ( !empty( $profiledata->first_name ) ) : ?>
<option value="<?php echo $profiledata->first_name ?>"><?php echo $profiledata->first_name ?></option>
<?php endif; ?>
<?php if ( !empty( $profiledata->last_name ) ) : ?>
<option value="<?php echo $profiledata->last_name ?>"><?php echo $profiledata->last_name ?></option>
<?php endif; ?>
<?php if ( !empty( $profiledata->first_name ) && !empty( $profiledata->last_name ) ) : ?>
<option value="<?php echo $profiledata->first_name." ".$profiledata->last_name ?>"><?php echo $profiledata->first_name." ".$profiledata->last_name ?></option>
<option value="<?php echo $profiledata->last_name." ".$profiledata->first_name ?>"><?php echo $profiledata->last_name." ".$profiledata->first_name ?></option>
<?php endif; ?>
</select></label></p>
</fieldset>

<fieldset>
<legend><?php _e('Contact Info'); ?></legend>

<p><label><?php _e('E-mail: (required)') ?><br />
<input type="text" name="newuser_email" id="newuser_email2" value="<?php echo $profiledata->user_email ?>" /></label></p>

<p><label><?php _e('Website:') ?><br />
<input type="text" name="newuser_url" id="newuser_url2" value="<?php echo $profiledata->user_url ?>" />
</label></p>

<p><label><?php _e('AIM:') ?><br />
<input type="text" name="newuser_aim" id="newuser_aim2" value="<?php echo $profiledata->aim ?>" />
</label></p>

<p><label><?php _e('Yahoo IM:') ?><br />
<input type="text" name="newuser_yim" id="newuser_yim2" value="<?php echo $profiledata->yim ?>" />
</label></p>

<p><label><?php _e('Jabber / Google Talk:') ?>
<input type="text" name="jabber" id="jabber" value="<?php echo $profiledata->jabber ?>" /></label>
</p>
</fieldset>
<br clear="all" />
<fieldset>
<legend><?php _e('About yourself'); ?></legend>
<p class="desc"><?php _e('Share a little biographical information to fill out your profile. This may be shown publicly.'); ?></p>
<p><textarea name="user_description" rows="5" cols="30"><?php echo $profiledata->user_description ?></textarea></p>
</fieldset>

<?php
$show_password_fields = apply_filters('show_password_fields', true);
if ( $show_password_fields ) :
?>
<fieldset>
<legend><?php _e('Update Your Password'); ?></legend>
<p class="desc"><?php _e('If you would like to change your password type a new one twice below. Otherwise leave this blank.'); ?></p>
<p><label><?php _e('New Password:'); ?><br />
<input type="password" name="pass1" size="16" value="" />
</label></p>
<p><label><?php _e('Type it one more time:'); ?><br />
<input type="password" name="pass2" size="16" value="" />
</label></p>
</fieldset>
<?php endif; ?>

<?php do_action('show_user_profile'); ?>

<br clear="all" />
  <table width="99%"  border="0" cellspacing="2" cellpadding="3" class="editform">
    <?php
    if(count($profileuser->caps) > count($profileuser->roles)):
    ?>
    <tr>
      <th scope="row"><?php _e('Additional Capabilities:') ?></th>
      <td><?php 
			$output = '';
			foreach($profileuser->caps as $cap => $value) {
				if(!$wp_roles->is_role($cap)) {
					if($output != '') $output .= ', ';
					$output .= $value ? $cap : "Denied: {$cap}";
				}
			}
			echo $output;
			?></td>
    </tr>
    <?php
    endif;
    ?>
  </table>
<p class="submit">
<input type="submit" value="<?php _e('Update Profile &raquo;') ?>" name="submit" />
</p>
</form>

</div>

<?php include('admin-footer.php'); ?>