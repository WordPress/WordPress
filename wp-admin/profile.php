<?php 
require_once('admin.php');

$title = __('Profile');

if ( current_user_can('edit_users') )
	$parent_file = 'users.php';
else
	$parent_file = 'profile.php';
include_once('admin-header.php');
$profileuser = get_user_to_edit($user_ID);

$bookmarklet_height= 440;
?>

<?php if ( isset($_GET['updated']) ) { ?>
<div id="message" class="updated fade">
<p><strong><?php _e('Profile updated.') ?></strong></p>
</div>
<?php } ?>

<div class="wrap">
<h2><?php _e('Your Profile and Personal Options'); ?></h2>
<form name="profile" id="your-profile" action="profile-update.php" method="post">
<?php wp_nonce_field('update-profile_' . $user_ID) ?>
<p>
<input type="hidden" name="from" value="profile" />
<input type="hidden" name="checkuser_id" value="<?php echo $user_ID ?>" />
</p>

<h3><?php _e('Personal Options'); ?></h3>

<?php if ( rich_edit_exists() ) : // don't bother showing the option if the editor has been removed ?>
<p><label for="rich_editing"><input name="rich_editing" type="checkbox" id="rich_editing" value="true" <?php checked('true', get_user_option('rich_editing')); ?> />
<?php _e('Use the visual editor when writing') ?></label></p>
<?php endif; ?>

<?php do_action('profile_personal_options'); ?>

<p class="submit"><input type="submit" value="<?php _e('Update Profile &raquo;') ?>" name="submit" /></p>

<fieldset>
<legend><?php _e('Name'); ?></legend>
<p><label><?php _e('Username: (no editing)'); ?><br />
<input type="text" name="user_login" value="<?php echo $profileuser->user_login; ?>" disabled="disabled" />
</label></p>

<p><label><?php _e('First name:') ?><br />
<input type="text" name="first_name" value="<?php echo $profileuser->first_name ?>" /></label></p>

<p><label><?php _e('Last name:') ?><br />
<input type="text" name="last_name"  value="<?php echo $profileuser->last_name ?>" /></label></p>

<p><label><?php _e('Nickname:') ?><br />
<input type="text" name="nickname" value="<?php echo $profileuser->nickname ?>" /></label></p>

<p><label><?php _e('Display name publicly as:') ?> <br />
<select name="display_name">
<option value="<?php echo $profileuser->display_name; ?>"><?php echo $profileuser->display_name; ?></option>
<option value="<?php echo $profileuser->nickname ?>"><?php echo $profileuser->nickname ?></option>
<option value="<?php echo $profileuser->user_login ?>"><?php echo $profileuser->user_login ?></option>
<?php if ( !empty( $profileuser->first_name ) ) : ?>
<option value="<?php echo $profileuser->first_name ?>"><?php echo $profileuser->first_name ?></option>
<?php endif; ?>
<?php if ( !empty( $profileuser->last_name ) ) : ?>
<option value="<?php echo $profileuser->last_name ?>"><?php echo $profileuser->last_name ?></option>
<?php endif; ?>
<?php if ( !empty( $profileuser->first_name ) && !empty( $profileuser->last_name ) ) : ?>
<option value="<?php echo $profileuser->first_name." ".$profileuser->last_name ?>"><?php echo $profileuser->first_name." ".$profileuser->last_name ?></option>
<option value="<?php echo $profileuser->last_name." ".$profileuser->first_name ?>"><?php echo $profileuser->last_name." ".$profileuser->first_name ?></option>
<?php endif; ?>
</select></label></p>
</fieldset>

<fieldset>
<legend><?php _e('Contact Info'); ?></legend>

<p><label><?php _e('E-mail: (required)') ?><br />
<input type="text" name="email" value="<?php echo $profileuser->user_email ?>" /></label></p>

<p><label><?php _e('Website:') ?><br />
<input type="text" name="url" value="<?php echo $profileuser->user_url ?>" />
</label></p>

<p><label><?php _e('AIM:') ?><br />
<input type="text" name="aim" value="<?php echo $profileuser->aim ?>" />
</label></p>

<p><label><?php _e('Yahoo IM:') ?><br />
<input type="text" name="yim" value="<?php echo $profileuser->yim ?>" />
</label></p>

<p><label><?php _e('Jabber / Google Talk:') ?>
<input type="text" name="jabber" value="<?php echo $profileuser->jabber ?>" /></label>
</p>
</fieldset>
<br clear="all" />
<fieldset>
<legend><?php _e('About Yourself'); ?></legend>
<p class="desc"><?php _e('Share a little biographical information. '); ?></p>
<p><textarea name="description" rows="5" cols="30"><?php echo $profileuser->description ?></textarea></p>
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
<p class="submit"><input type="submit" value="<?php _e('Update Profile &raquo;') ?>" name="submit" /></p>
</form>

</div>

<?php include('admin-footer.php'); ?>
