<?php 
require_once('admin.php');

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
<form name="profile" id="your-profile" action="profile-update.php" method="post">
<p>
<input type="hidden" name="from" value="profile" />
<input type="hidden" name="checkuser_id" value="<?php echo $user_ID ?>" />
</p>

<fieldset>
<legend><?php _e('Name'); ?></legend>
<p><label><?php _e('Username: (no editing)'); ?><br />
<input type="text" name="user_login" value="<?php echo $profiledata->user_login; ?>" disabled="disabled" />
</label></p>
<p><label><?php _e('First name:') ?><br />
<input type="text" name="first_name" value="<?php echo $profiledata->first_name ?>" /></label></p>

<p><label><?php _e('Middle name:') ?><br />
<input type="text" name="middle_name" value="<?php echo $profiledata->middle_name ?>" /></label></p>

<p><label><?php _e('Last name:') ?><br />
<input type="text" name="last_name"  value="<?php echo $profiledata->last_name ?>" /></label></p>

<p><label><?php _e('Nickname:') ?><br />
<input type="text" name="nickname" value="<?php echo $profiledata->nickname ?>" /></label></p>

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
<?php if ( !empty( $profiledata->middle_name ) ) : ?>
<option value="<?php echo $n = $profiledata->first_name." ".$profiledata->middle_name." ".$profiledata->last_name ?>"><?php echo $n ?></option>
<?php endif; ?>
<?php endif; ?>
</select></label></p>
</fieldset>

<fieldset>
<legend><?php _e('Contact Info'); ?></legend>

<p><label><?php _e('E-mail: (required)') ?><br />
<input type="text" name="email" value="<?php echo $profiledata->user_email ?>" /></label></p>

<p><label><?php _e('Website:') ?><br />
<input type="text" name="url" value="<?php echo $profiledata->user_url ?>" />
</label></p>

<p><label><?php _e('Flickr Username:') ?><br />
<input type="text" name="flickr_username" value="<?php echo $profiledata->flickr_username ?>" />
</label></p>

<p><label><?php _e('AIM:') ?><br />
<input type="text" name="aim" value="<?php echo $profiledata->aim ?>" />
</label></p>

<p><label><?php _e('Yahoo IM:') ?><br />
<input type="text" name="yim" value="<?php echo $profiledata->yim ?>" />
</label></p>

<p><label><?php _e('Jabber / Google Talk:') ?>
<input type="text" name="jabber" value="<?php echo $profiledata->jabber ?>" /></label>
</p>
</fieldset>
<br clear="all" />
<fieldset>
<legend><?php _e('About yourself'); ?></legend>
<p class="desc"><?php _e('Share a little biographical information to fill out your profile. This may be shown publicly.'); ?></p>
<p><textarea name="description" rows="5" cols="30"><?php echo $profiledata->description ?></textarea></p>
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
