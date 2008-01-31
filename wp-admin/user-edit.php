<?php

require_once('admin.php');

if ( defined('IS_PROFILE_PAGE') && IS_PROFILE_PAGE )
	$is_profile_page = true;
else
	$is_profile_page = false;

function profile_js ( ) {
?>
<script type="text/javascript">
	function check_pass_strength ( ) {

		var pass = jQuery('#pass1').val();
		var user = jQuery('#user_login').val();

		// get the result as an object, i'm tired of typing it
		var res = jQuery('#pass-strength-result');

		var strength = passwordStrength(pass, user);

		jQuery(res).removeClass('short bad good strong');

		if ( strength == 'Bad' ) {
			jQuery(res).addClass('bad');
			jQuery(res).html( pwsL10n.bad );
		}
		else if ( strength == 'Good' ) {
			jQuery(res).addClass('good');
			jQuery(res).html( pwsL10n.good );
		}
		else if ( strength == 'Strong' ) {
			jQuery(res).addClass('strong');
			jQuery(res).html( pwsL10n.strong );
		}
		else {
			// this catches 'Too short' and the off chance anything else comes along
			jQuery(res).addClass('short');
			jQuery(res).html( pwsL10n.short );
		}

	}

	jQuery(document).ready( function() { jQuery('#pass1').keyup( check_pass_strength ) } );
</script>
<?php
}

if ( $is_profile_page ) {
	add_action('admin_head', 'profile_js');
	wp_enqueue_script('jquery');
	wp_enqueue_script('password-strength-meter');
}

$title = $is_profile_page? __('Profile') : __('Edit User');
if ( current_user_can('edit_users') && !$is_profile_page )
	$submenu_file = 'users.php';
else
	$submenu_file = 'profile.php';
$parent_file = 'users.php';

wp_reset_vars(array('action', 'redirect', 'profile', 'user_id', 'wp_http_referer'));

$wp_http_referer = remove_query_arg(array('update', 'delete_count'), stripslashes($wp_http_referer));

$user_id = (int) $user_id;

if ( !$user_id )
	if ( $is_profile_page ) {
		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;
	} else {
		wp_die(__('Invalid user ID.'));
	}

switch ($action) {
case 'switchposts':

check_admin_referer();

/* TODO: Switch all posts from one user to another user */

break;

case 'update':

check_admin_referer('update-user_' . $user_id);

if ( !current_user_can('edit_user', $user_id) )
	wp_die(__('You do not have permission to edit this user.'));

if ( $is_profile_page ) {
	do_action('personal_options_update');
}

$errors = edit_user($user_id);

if( !is_wp_error( $errors ) ) {
	$redirect = ($is_profile_page? "profile.php?" : "user-edit.php?user_id=$user_id&"). "updated=true";
	$redirect = add_query_arg('wp_http_referer', urlencode($wp_http_referer), $redirect);
	wp_redirect($redirect);
	exit;
}

default:
$profileuser = get_user_to_edit($user_id);

if ( !current_user_can('edit_user', $user_id) )
		wp_die(__('You do not have permission to edit this user.'));

include ('admin-header.php');
?>

<?php if ( isset($_GET['updated']) ) : ?>
<div id="message" class="updated fade">
	<p><strong><?php _e('User updated.') ?></strong></p>
	<?php if ( $wp_http_referer && !$is_profile_page ) : ?>
	<p><a href="users.php"><?php _e('&laquo; Back to Authors and Users'); ?></a></p>
	<?php endif; ?>
</div>
<?php endif; ?>
<?php if ( is_wp_error( $errors ) ) : ?>
<div class="error">
	<ul>
	<?php
	foreach( $errors->get_error_messages() as $message )
		echo "<li>$message</li>";
	?>
	</ul>
</div>
<?php endif; ?>

<div class="wrap">
<h2><?php $is_profile_page? _e('Your Profile and Personal Options') : _e('Edit User'); ?></h2>

<form name="profile" id="your-profile" action="" method="post">
<?php wp_nonce_field('update-user_' . $user_id) ?>
<?php if ( $wp_http_referer ) : ?>
	<input type="hidden" name="wp_http_referer" value="<?php echo clean_url($wp_http_referer); ?>" />
<?php endif; ?>
<p>
<input type="hidden" name="from" value="profile" />
<input type="hidden" name="checkuser_id" value="<?php echo $user_ID ?>" />
</p>

<h3><?php _e('Personal Options'); ?></h3>

<?php if ( rich_edit_exists() ) : // don't bother showing the option if the editor has been removed ?>
<p><label for="rich_editing"><input name="rich_editing" type="checkbox" id="rich_editing" value="true" <?php checked('true', $profileuser->rich_editing); ?> /> <?php _e('Use the visual editor when writing'); ?></label></p>
<?php endif; ?>

<?php
	if ( $is_profile_page ) {
		do_action('profile_personal_options');
	}
?>

<p class="submit"><input type="submit" value="<?php $is_profile_page? _e('Update Profile &raquo;') : _e('Update User &raquo;'); ?>" name="submit" /></p>

<fieldset>
<legend><?php _e('Name'); ?></legend>
<p><label><?php _e('Username: (no editing)'); ?><br />
<input type="text" name="user_login" id="user_login" value="<?php echo $profileuser->user_login; ?>" disabled="disabled" />
</label></p>

<?php if ( !$is_profile_page ): ?>
<p><label><?php _e('Role:') ?><br />
<?php
// print_r($profileuser);
echo '<select name="role">';
$role_list = '';
$user_has_role = false;
foreach($wp_roles->role_names as $role => $name) {
	if ( $profileuser->has_cap($role) ) {
		$selected = ' selected="selected"';
		$user_has_role = true;
	} else {
		$selected = '';
	}
	$role_list .= "<option value=\"{$role}\"{$selected}>{$name}</option>";
}
if ( $user_has_role )
	$role_list .= '<option value="">' . __('&mdash; No role for this blog &mdash;') . '</option>';
else
	$role_list .= '<option value="" selected="selected">' . __('&mdash; No role for this blog &mdash;') . '</option>';
echo $role_list . '</select>';
?></label></p>
<?php endif; ?>

<p><label><?php _e('First name:') ?><br />
<input type="text" name="first_name" value="<?php echo $profileuser->first_name ?>" /></label></p>

<p><label><?php _e('Last name:') ?><br />
<input type="text" name="last_name"  value="<?php echo $profileuser->last_name ?>" /></label></p>

<p><label><?php _e('Nickname:') ?><br />
<input type="text" name="nickname" value="<?php echo $profileuser->nickname ?>" /></label></p>

<p><label><?php _e('Display name publicly as:') ?> <br />
<select name="display_name">
<?php
	$public_display = array();
	$public_display[] = $profileuser->display_name;
	$public_display[] = $profileuser->nickname;
	$public_display[] = $profileuser->user_login;
	$public_display[] = $profileuser->first_name;
	$public_display[] = $profileuser->first_name.' '.$profileuser->last_name;
	$public_display[] = $profileuser->last_name.' '.$profileuser->first_name;
	$public_display = array_unique(array_filter(array_map('trim', $public_display)));
	foreach($public_display as $item) {
?>
	<option value="<?php echo $item; ?>"><?php echo $item; ?></option>
<?php
	}
?>
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

<p><label><?php _e('Jabber / Google Talk:') ?><br />
<input type="text" name="jabber" value="<?php echo $profileuser->jabber ?>" /></label>
</p>
</fieldset>
<br clear="all" />
<fieldset>
<legend><?php $is_profile_page? _e('About Yourself') : _e('About the user'); ?></legend>
<p class="desc"><?php _e('Share a little biographical information to fill out your profile. This may be shown publicly.'); ?></p>
<p><textarea name="description" rows="5" cols="30"><?php echo $profileuser->description ?></textarea></p>
</fieldset>

<?php
$show_password_fields = apply_filters('show_password_fields', true);
if ( $show_password_fields ) :
?>
<fieldset>
<legend><?php $is_profile_page? _e('Update Your Password') : _e("Update User's Password"); ?></legend>
<p class="desc"><?php _e("If you would like to change the password type a new one twice below. Otherwise leave this blank."); ?></p>
<p><label><?php _e('New Password:'); ?><br />
<input type="password" name="pass1" id="pass1" size="16" value="" />
</label></p>
<p><label><?php _e('Type it one more time:'); ?><br />
<input type="password" name="pass2" id="pass2" size="16" value="" />
</label></p>
<?php if ( $is_profile_page ): ?>
<p><strong><?php _e('Password Strength:'); ?></strong></p>
<div id="pass-strength-result"><?php _e('Too short'); ?></div>
<p><?php _e('Hint: Use upper and lower case characters, numbers and symbols like !"?$%^&( in your password.'); ?></p>
<?php endif; ?>
</fieldset>
<?php endif; ?>

<?php
	if ( $is_profile_page ) { 
		do_action('show_user_profile');
	} else {
		do_action('edit_user_profile');
	}
?>

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
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>" />
	<input type="submit" value="<?php $is_profile_page? _e('Update Profile &raquo;') : _e('Update User &raquo;') ?>" name="submit" />
 </p>
</form>
</div>
<?php
break;
}

include('admin-footer.php');
?>
