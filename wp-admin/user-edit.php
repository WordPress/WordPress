<?php
/**
 * Edit user administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

if ( defined('IS_PROFILE_PAGE') && IS_PROFILE_PAGE )
	$is_profile_page = true;
else
	$is_profile_page = false;

/**
 * Display JavaScript for profile page.
 *
 * @since 2.5.0
 */
function profile_js ( ) {
?>
<script type="text/javascript">
(function($){

	function check_pass_strength () {

		var pass = $('#pass1').val();
		var user = $('#user_login').val();

		$('#pass-strength-result').removeClass('short bad good strong');
		if ( ! pass ) {
			$('#pass-strength-result').html( pwsL10n.empty );
			return;
		}

		var strength = passwordStrength(pass, user);

		if ( 2 == strength )
			$('#pass-strength-result').addClass('bad').html( pwsL10n.bad );
		else if ( 3 == strength )
			$('#pass-strength-result').addClass('good').html( pwsL10n.good );
		else if ( 4 == strength )
			$('#pass-strength-result').addClass('strong').html( pwsL10n.strong );
		else
			// this catches 'Too short' and the off chance anything else comes along
			$('#pass-strength-result').addClass('short').html( pwsL10n.short );

	}

	function update_nickname () {

		var nickname = $('#nickname').val();
		var display_nickname = $('#display_nickname').val();

		if ( nickname == '' ) {
			$('#display_nickname').remove();
		}
		$('#display_nickname').val(nickname).html(nickname);

	}

	$(document).ready( function() {
		$('#nickname').blur(update_nickname);
		$('#pass1').val('').keyup( check_pass_strength );
		$('.color-palette').click(function(){$(this).siblings('input[name=admin_color]').attr('checked', 'checked')});
    });
})(jQuery);
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

if ( !$user_id ) {
	if ( $is_profile_page ) {
		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;
	} else {
		wp_die(__('Invalid user ID.'));
	}
} elseif ( !get_userdata($user_id) ) {
	wp_die( __('Invalid user ID.') );
}

/**
 * Optional SSL preference that can be turned on by hooking to the 'personal_options' action.
 *
 * @since 2.7.0
 *
 * @param object $user User data object
 */
function use_ssl_preference($user) {
?>
	<tr>
		<th scope="row"><?php _e('Use https')?></th>
		<td><label for="use_ssl"><input name="use_ssl" type="checkbox" id="use_ssl" value="1" <?php checked('1', $user->use_ssl); ?> /> <?php _e('Always use https when visiting the admin'); ?></label></td>
	</tr>
<?php
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

if ($is_profile_page)
	do_action('personal_options_update');
else
	do_action('edit_user_profile_update');

$errors = edit_user($user_id);

if ( !is_wp_error( $errors ) ) {
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
	<p><a href="users.php"><?php _e('&larr; Back to Authors and Users'); ?></a></p>
	<?php endif; ?>
</div>
<?php endif; ?>
<?php if ( isset( $errors ) && is_wp_error( $errors ) ) : ?>
<div class="error">
	<ul>
	<?php
	foreach( $errors->get_error_messages() as $message )
		echo "<li>$message</li>";
	?>
	</ul>
</div>
<?php endif; ?>

<div class="wrap" id="profile-page">
<?php screen_icon(); ?>
<h2><?php echo wp_specialchars( $title ); ?></h2>

<form id="your-profile" action="" method="post">
<?php wp_nonce_field('update-user_' . $user_id) ?>
<?php if ( $wp_http_referer ) : ?>
	<input type="hidden" name="wp_http_referer" value="<?php echo clean_url($wp_http_referer); ?>" />
<?php endif; ?>
<p>
<input type="hidden" name="from" value="profile" />
<input type="hidden" name="checkuser_id" value="<?php echo $user_ID ?>" />
</p>

<h3><?php _e('Personal Options'); ?></h3>

<table class="form-table">
<?php if ( rich_edit_exists() ) : // don't bother showing the option if the editor has been removed ?>
	<tr>
		<th scope="row"><?php _e('Visual Editor')?></th>
		<td><label for="rich_editing"><input name="rich_editing" type="checkbox" id="rich_editing" value="false" <?php checked('false', $profileuser->rich_editing); ?> /> <?php _e('Disable the visual editor when writing'); ?></label></td>
	</tr>
<?php endif; ?>
<?php if (count($_wp_admin_css_colors) > 1 ) : ?>
<tr>
<th scope="row"><?php _e('Admin Color Scheme')?></th>
<td><fieldset><legend class="hidden"><?php _e('Admin Color Scheme')?></legend>
<?php
$current_color = get_user_option('admin_color', $user_id);
if ( empty($current_color) )
	$current_color = 'fresh';
foreach ( $_wp_admin_css_colors as $color => $color_info ): ?>
<div class="color-option"><input name="admin_color" id="admin_color_<?php echo $color; ?>" type="radio" value="<?php echo $color ?>" class="tog" <?php checked($color, $current_color); ?> />
	<table class="color-palette">
	<tr>
	<?php foreach ( $color_info->colors as $html_color ): ?>
	<td style="background-color: <?php echo $html_color ?>" title="<?php echo $color ?>">&nbsp;</td>
	<?php endforeach; ?>
	</tr>
	</table>

	<label for="admin_color_<?php echo $color; ?>"><?php echo $color_info->name ?></label>
</div>
	<?php endforeach; ?>
</fieldset></td>
</tr>
<tr>
<th scope="row"><?php _e( 'Keyboard Shortcuts' ); ?></th>
<td><label for="comment_shortcuts"><input type="checkbox" name="comment_shortcuts" id="comment_shortcuts" value="true" <?php if ( !empty($profileuser->comment_shortcuts) ) checked('true', $profileuser->comment_shortcuts); ?> /> <?php _e( 'Enable keyboard shortcuts for comment moderation. <a href="http://codex.wordpress.org/Keyboard_Shortcuts">More information</a>' ); ?></label></td>
</tr>
<?php
endif;
do_action('personal_options', $profileuser);
?>
</table>
<?php
	if ( $is_profile_page )
		do_action('profile_personal_options', $profileuser);
?>

<h3><?php _e('Name') ?></h3>

<table class="form-table">
	<tr>
		<th><label for="user_login"><?php _e('Username'); ?></label></th>
		<td><input type="text" name="user_login" id="user_login" value="<?php echo $profileuser->user_login; ?>" disabled="disabled" class="regular-text" /> <?php _e('Your username cannot be changed.'); ?></td>
	</tr>

<?php if ( !$is_profile_page ): ?>
<tr><th><label for="role"><?php _e('Role:') ?></label></th>
<?php
// print_r($profileuser);
echo '<td><select name="role" id="role">';
$role_list = '';
$user_has_role = false;
foreach($wp_roles->role_names as $role => $name) {
	$name = translate_with_context($name);
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
echo $role_list . '</select></td></tr>';
?>
<?php endif; ?>

<tr>
	<th><label for="first_name"><?php _e('First name') ?></label></th>
	<td><input type="text" name="first_name" id="first_name" value="<?php echo $profileuser->first_name ?>" class="regular-text" /></td>
</tr>

<tr>
	<th><label for="last_name"><?php _e('Last name') ?></label></th>
	<td><input type="text" name="last_name" id="last_name" value="<?php echo $profileuser->last_name ?>" class="regular-text" /></td>
</tr>

<tr>
	<th><label for="nickname"><?php _e('Nickname') ?></label></th>
	<td><input type="text" name="nickname" id="nickname" value="<?php echo $profileuser->nickname ?>" class="regular-text" /></td>
</tr>

<tr>
	<th><label for="display_name"><?php _e('Display name publicly&nbsp;as') ?></label></th>
	<td>
		<select name="display_name" id="display_name">
		<?php
			$public_display = array();
			$public_display['display_displayname'] = $profileuser->display_name;
			$public_display['display_nickname'] = $profileuser->nickname;
			$public_display['display_username'] = $profileuser->user_login;
			$public_display['display_firstname'] = $profileuser->first_name;
			$public_display['display_firstlast'] = $profileuser->first_name.' '.$profileuser->last_name;
			$public_display['display_lastfirst'] = $profileuser->last_name.' '.$profileuser->first_name;
			$public_display = array_unique(array_filter(array_map('trim', $public_display)));
			foreach($public_display as $id => $item) {
		?>
			<option id="<?php echo $id; ?>" value="<?php echo $item; ?>"><?php echo $item; ?></option>
		<?php
			}
		?>
		</select>
	</td>
</tr>
</table>

<h3><?php _e('Contact Info') ?></h3>

<table class="form-table">
<tr>
	<th><label for="email"><?php _e('E-mail') ?></label></th>
	<td><input type="text" name="email" id="email" value="<?php echo $profileuser->user_email ?>" class="regular-text" /> <?php _e('Required.');?></td>
</tr>

<tr>
	<th><label for="url"><?php _e('Website') ?></label></th>
	<td><input type="text" name="url" id="url" value="<?php echo $profileuser->user_url ?>" class="regular-text" /></td>
</tr>

<tr>
	<th><label for="aim"><?php _e('AIM') ?></label></th>
	<td><input type="text" name="aim" id="aim" value="<?php echo $profileuser->aim ?>" class="regular-text" /></td>
</tr>

<tr>
	<th><label for="yim"><?php _e('Yahoo IM') ?></label></th>
	<td><input type="text" name="yim" id="yim" value="<?php echo $profileuser->yim ?>" class="regular-text" /></td>
</tr>

<tr>
	<th><label for="jabber"><?php _e('Jabber / Google Talk') ?></label></th>
	<td><input type="text" name="jabber" id="jabber" value="<?php echo $profileuser->jabber ?>" class="regular-text" /></td>
</tr>
</table>

<h3><?php $is_profile_page? _e('About Yourself') : _e('About the user'); ?></h3>

<table class="form-table">
<tr>
	<th><label for="description"><?php _e('Biographical Info'); ?></label></th>
	<td><textarea name="description" id="description" rows="5" cols="30"><?php echo $profileuser->description ?></textarea><br /><?php _e('Share a little biographical information to fill out your profile. This may be shown publicly.'); ?></td>
</tr>

<?php
$show_password_fields = apply_filters('show_password_fields', true);
if ( $show_password_fields ) :
?>
<tr>
	<th><label for="pass1"><?php _e('New Password'); ?></label></th>
	<td><input type="password" name="pass1" id="pass1" size="16" value="" autocomplete="off" /> <?php _e("If you would like to change the password type a new one. Otherwise leave this blank."); ?><br />
		<input type="password" name="pass2" id="pass2" size="16" value="" autocomplete="off" /> <?php _e("Type your new password again."); ?><br />
	<?php if ( $is_profile_page ): ?>
		<div id="pass-strength-result"><?php _e('Strength indicator'); ?></div>
		<p><?php _e('Hint: Your password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp; ).'); ?></p>
	<?php endif; ?>
	</td>
</tr>
<?php endif; ?>
</table>

<?php
	if ( $is_profile_page ) {
		do_action('show_user_profile');
	} else {
		do_action('edit_user_profile');
	}
?>

<?php if (count($profileuser->caps) > count($profileuser->roles)): ?>
<br class="clear" />
	<table width="99%" style="border: none;" cellspacing="2" cellpadding="3" class="editform">
		<tr>
			<th scope="row"><?php _e('Additional Capabilities') ?></th>
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
	</table>
<?php endif; ?>

<p class="submit">
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>" />
	<input type="submit" class="button-primary" value="<?php $is_profile_page? _e('Update Profile') : _e('Update User') ?>" name="submit" />
</p>
</form>
</div>
<?php
break;
}

include('admin-footer.php');
?>
