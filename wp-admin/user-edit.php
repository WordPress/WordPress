<?php
/**
 * Edit user administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

if ( !defined('IS_PROFILE_PAGE') )
	define('IS_PROFILE_PAGE', false);

wp_enqueue_script('user-profile');
wp_enqueue_script('password-strength-meter');

$title = IS_PROFILE_PAGE ? __('Profile') : __('Edit User');
if ( current_user_can('edit_users') && !IS_PROFILE_PAGE )
	$submenu_file = 'users.php';
else
	$submenu_file = 'profile.php';
$parent_file = 'users.php';

wp_reset_vars(array('action', 'redirect', 'profile', 'user_id', 'wp_http_referer'));

$wp_http_referer = remove_query_arg(array('update', 'delete_count'), stripslashes($wp_http_referer));

$user_id = (int) $user_id;

if ( !$user_id ) {
	if ( IS_PROFILE_PAGE ) {
		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;
	} else {
		wp_die(__('Invalid user ID.'));
	}
} elseif ( !get_userdata($user_id) ) {
	wp_die( __('Invalid user ID.') );
}

$all_post_caps = array('posts', 'pages');
$user_can_edit = false;
foreach ( $all_post_caps as $post_cap )
	$user_can_edit |= current_user_can("edit_$post_cap");

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

if ( IS_PROFILE_PAGE )
	do_action('personal_options_update', $user_id);
else
	do_action('edit_user_profile_update', $user_id);

$errors = edit_user($user_id);

if ( !is_wp_error( $errors ) ) {
	$redirect = (IS_PROFILE_PAGE ? "profile.php?" : "user-edit.php?user_id=$user_id&"). "updated=true";
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
	<?php if ( $wp_http_referer && !IS_PROFILE_PAGE ) : ?>
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
<h2><?php echo esc_html( $title ); ?></h2>

<form id="your-profile" action="<?php if ( IS_PROFILE_PAGE ) { echo admin_url('profile.php'); } else { echo admin_url('user-edit.php'); } ?>" method="post">
<?php wp_nonce_field('update-user_' . $user_id) ?>
<?php if ( $wp_http_referer ) : ?>
	<input type="hidden" name="wp_http_referer" value="<?php echo esc_url($wp_http_referer); ?>" />
<?php endif; ?>
<p>
<input type="hidden" name="from" value="profile" />
<input type="hidden" name="checkuser_id" value="<?php echo $user_ID ?>" />
</p>

<h3><?php _e('Personal Options'); ?></h3>

<table class="form-table">
<?php if ( rich_edit_exists() && !( IS_PROFILE_PAGE && !$user_can_edit ) ) : // don't bother showing the option if the editor has been removed ?>
	<tr>
		<th scope="row"><?php _e('Visual Editor')?></th>
		<td><label for="rich_editing"><input name="rich_editing" type="checkbox" id="rich_editing" value="false" <?php checked('false', $profileuser->rich_editing); ?> /> <?php _e('Disable the visual editor when writing'); ?></label></td>
	</tr>
<?php endif; ?>
<?php if (count($_wp_admin_css_colors) > 1 ) : ?>
<tr>
<th scope="row"><?php _e('Admin Color Scheme')?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e('Admin Color Scheme')?></span></legend>
<?php
$current_color = get_user_option('admin_color', $user_id);
if ( empty($current_color) )
	$current_color = 'fresh';
foreach ( $_wp_admin_css_colors as $color => $color_info ): ?>
<div class="color-option"><input name="admin_color" id="admin_color_<?php echo $color; ?>" type="radio" value="<?php echo esc_attr($color) ?>" class="tog" <?php checked($color, $current_color); ?> />
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
<?php if ( !( IS_PROFILE_PAGE && !$user_can_edit ) ) : ?>
<tr>
<th scope="row"><?php _e( 'Keyboard Shortcuts' ); ?></th>
<td><label for="comment_shortcuts"><input type="checkbox" name="comment_shortcuts" id="comment_shortcuts" value="true" <?php if ( !empty($profileuser->comment_shortcuts) ) checked('true', $profileuser->comment_shortcuts); ?> /> <?php _e('Enable keyboard shortcuts for comment moderation.'); ?></label> <?php _e('<a href="http://codex.wordpress.org/Keyboard_Shortcuts">More information</a>'); ?></td>
</tr>
<?php
endif;
endif;
do_action('personal_options', $profileuser);
?>
</table>
<?php
	if ( IS_PROFILE_PAGE )
		do_action('profile_personal_options', $profileuser);
?>

<h3><?php _e('Name') ?></h3>

<table class="form-table">
	<tr>
		<th><label for="user_login"><?php _e('Username'); ?></label></th>
		<td><input type="text" name="user_login" id="user_login" value="<?php echo esc_attr($profileuser->user_login); ?>" disabled="disabled" class="regular-text" /> <span class="description"><?php _e('Your username cannot be changed.'); ?></span></td>
	</tr>

<?php if ( !IS_PROFILE_PAGE ): ?>
<tr><th><label for="role"><?php _e('Role:') ?></label></th>
<td><select name="role" id="role">
<?php
// Get the highest/primary role for this user
// TODO: create a function that does this: wp_get_user_role()
$user_roles = $profileuser->roles;
$user_role = array_shift($user_roles);

// print the full list of roles with the primary one selected.
wp_dropdown_roles($user_role);

// print the 'no role' option. Make it selected if the user has no role yet.
if ( $user_role )
	echo '<option value="">' . __('&mdash; No role for this blog &mdash;') . '</option>';
else
	echo '<option value="" selected="selected">' . __('&mdash; No role for this blog &mdash;') . '</option>';
?>
</select></td></tr>
<?php endif; //!IS_PROFILE_PAGE ?>

<tr>
	<th><label for="first_name"><?php _e('First name') ?></label></th>
	<td><input type="text" name="first_name" id="first_name" value="<?php echo esc_attr($profileuser->first_name) ?>" class="regular-text" /></td>
</tr>

<tr>
	<th><label for="last_name"><?php _e('Last name') ?></label></th>
	<td><input type="text" name="last_name" id="last_name" value="<?php echo esc_attr($profileuser->last_name) ?>" class="regular-text" /></td>
</tr>

<tr>
	<th><label for="nickname"><?php _e('Nickname'); ?> <span class="description"><?php _e('(required)'); ?></span></label></th>
	<td><input type="text" name="nickname" id="nickname" value="<?php echo esc_attr($profileuser->nickname) ?>" class="regular-text" /></td>
</tr>

<tr>
	<th><label for="display_name"><?php _e('Display name publicly as') ?></label></th>
	<td>
		<select name="display_name" id="display_name">
		<?php
			$public_display = array();
			$public_display['display_nickname']  = $profileuser->nickname;
			$public_display['display_username']  = $profileuser->user_login;
			if ( !empty($profileuser->first_name) )
				$public_display['display_firstname'] = $profileuser->first_name;
			if ( !empty($profileuser->last_name) )
				$public_display['display_lastname'] = $profileuser->last_name;
			if ( !empty($profileuser->first_name) && !empty($profileuser->last_name) ) {
				$public_display['display_firstlast'] = $profileuser->first_name . ' ' . $profileuser->last_name;
				$public_display['display_lastfirst'] = $profileuser->last_name . ' ' . $profileuser->first_name;
			}
			if ( !in_array( $profileuser->display_name, $public_display ) )// Only add this if it isn't duplicated elsewhere
				$public_display = array( 'display_displayname' => $profileuser->display_name ) + $public_display;
			$public_display = array_map( 'trim', $public_display );
			foreach ( $public_display as $id => $item ) {
		?>
			<option id="<?php echo $id; ?>" value="<?php echo esc_attr($item); ?>"<?php selected( $profileuser->display_name, $item ); ?>><?php echo $item; ?></option>
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
	<th><label for="email"><?php _e('E-mail'); ?> <span class="description"><?php _e('(required)'); ?></span></label></th>
	<td><input type="text" name="email" id="email" value="<?php echo esc_attr($profileuser->user_email) ?>" class="regular-text" /></td>
</tr>

<tr>
	<th><label for="url"><?php _e('Website') ?></label></th>
	<td><input type="text" name="url" id="url" value="<?php echo esc_attr($profileuser->user_url) ?>" class="regular-text code" /></td>
</tr>

<?php
	foreach (_wp_get_user_contactmethods() as $name => $desc) {
?>
<tr>
	<th><label for="<?php echo $name; ?>"><?php echo apply_filters('user_'.$name.'_label', $desc); ?></label></th>
	<td><input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo esc_attr($profileuser->$name) ?>" class="regular-text" /></td>
</tr>
<?php
	}
?>
</table>

<h3><?php IS_PROFILE_PAGE ? _e('About Yourself') : _e('About the user'); ?></h3>

<table class="form-table">
<tr>
	<th><label for="description"><?php _e('Biographical Info'); ?></label></th>
	<td><textarea name="description" id="description" rows="5" cols="30"><?php echo esc_html($profileuser->description); ?></textarea><br />
	<span class="description"><?php _e('Share a little biographical information to fill out your profile. This may be shown publicly.'); ?></span></td>
</tr>

<?php
$show_password_fields = apply_filters('show_password_fields', true, $profileuser);
if ( $show_password_fields ) :
?>
<tr id="password">
	<th><label for="pass1"><?php _e('New Password'); ?></label></th>
	<td><input type="password" name="pass1" id="pass1" size="16" value="" autocomplete="off" /> <span class="description"><?php _e("If you would like to change the password type a new one. Otherwise leave this blank."); ?></span><br />
		<input type="password" name="pass2" id="pass2" size="16" value="" autocomplete="off" /> <span class="description"><?php _e("Type your new password again."); ?></span><br />
		<div id="pass-strength-result"><?php _e('Strength indicator'); ?></div>
		<p class="description indicator-hint"><?php _e('Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp; ).'); ?></p>
	</td>
</tr>
<?php endif; ?>
</table>

<?php
	if ( IS_PROFILE_PAGE ) {
		do_action('show_user_profile', $profileuser);
	} else {
		do_action('edit_user_profile', $profileuser);
	}
?>

<?php if ( count($profileuser->caps) > count($profileuser->roles) && apply_filters('additional_capabilities_display', true, $profileuser) ) { ?>
<br class="clear" />
	<table width="99%" style="border: none;" cellspacing="2" cellpadding="3" class="editform">
		<tr>
			<th scope="row"><?php _e('Additional Capabilities') ?></th>
			<td><?php
			$output = '';
			foreach ( $profileuser->caps as $cap => $value ) {
				if ( !$wp_roles->is_role($cap) ) {
					if ( $output != '' )
						$output .= ', ';
					$output .= $value ? $cap : "Denied: {$cap}";
				}
			}
			echo $output;
			?></td>
		</tr>
	</table>
<?php } ?>

<p class="submit">
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="user_id" id="user_id" value="<?php echo esc_attr($user_id); ?>" />
	<input type="submit" class="button-primary" value="<?php IS_PROFILE_PAGE ? esc_attr_e('Update Profile') : esc_attr_e('Update User') ?>" name="submit" />
</p>
</form>
</div>
<?php
break;
}

include('admin-footer.php');
?>
