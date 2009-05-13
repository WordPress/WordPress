<?php
/**
 * New User Administration Panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

if ( !current_user_can('create_users') )
	wp_die(__('Cheatin&#8217; uh?'));

/** WordPress Registration API */
require_once( ABSPATH . WPINC . '/registration.php');

if ( isset($_REQUEST['action']) && 'adduser' == $_REQUEST['action'] ) {
	check_admin_referer('add-user');

	if ( ! current_user_can('create_users') )
		wp_die(__('You can&#8217;t create users.'));

	$user_id = add_user();

	if ( is_wp_error( $user_id ) ) {
		$add_user_errors = $user_id;
	} else {
		$new_user_login = apply_filters('pre_user_login', sanitize_user(stripslashes($_REQUEST['user_login']), true));
		$redirect = 'users.php?usersearch='. urlencode($new_user_login) . '&update=add';
		wp_redirect( $redirect . '#user-' . $user_id );
		die();
	}
}

$title = __('Add New User');
$parent_file = 'users.php';

wp_enqueue_script('wp-ajax-response');

require_once ('admin-header.php');

?>
<div class="wrap">
<?php screen_icon(); ?>
<h2 id="add-new-user"><?php _e('Add New User') ?></h2>

<?php if ( isset($errors) && is_wp_error( $errors ) ) : ?>
	<div class="error">
		<ul>
		<?php
			foreach ( $errors->get_error_messages() as $err )
				echo "<li>$err</li>\n";
		?>
		</ul>
	</div>
<?php endif;

if ( ! empty($messages) ) {
	foreach ( $messages as $msg )
		echo $msg;
} ?>

<?php if ( isset($add_user_errors) && is_wp_error( $add_user_errors ) ) : ?>
	<div class="error">
		<?php
			foreach ( $add_user_errors->get_error_messages() as $message )
				echo "<p>$message</p>";
		?>
	</div>
<?php endif; ?>
<div id="ajax-response"></div>

<?php
	if ( get_option('users_can_register') )
		echo '<p>' . sprintf(__('Users can <a href="%1$s">register themselves</a> or you can manually create users here.'), site_url('wp-register.php')) . '</p>';
	else
		echo '<p>' . sprintf(__('Users cannot currently <a href="%1$s">register themselves</a>, but you can manually create users here.'), admin_url('options-general.php#users_can_register')) . '</p>';
?>
<form action="#add-new-user" method="post" name="adduser" id="adduser" class="add:users: validate">
<?php wp_nonce_field('add-user') ?>
<?php
//Load up the passed data, else set to a default.
foreach ( array('user_login' => 'login', 'first_name' => 'firstname', 'last_name' => 'lastname',
				'email' => 'email', 'url' => 'uri', 'role' => 'role') as $post_field => $var ) {
	$var = "new_user_$var";
	if ( ! isset($$var) )
		$$var = isset($_POST[$post_field]) ? stripslashes($_POST[$post_field]) : '';
}
$new_user_send_password = !$_POST || isset($_POST['send_password']);
?>
<table class="form-table">
	<tr class="form-field form-required">
		<th scope="row"><label for="user_login"><?php _e('Username (required)') ?></label><input name="action" type="hidden" id="action" value="adduser" /></th>
		<td ><input name="user_login" type="text" id="user_login" value="<?php echo esc_attr($new_user_login); ?>" aria-required="true" /></td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="first_name"><?php _e('First Name') ?> </label></th>
		<td><input name="first_name" type="text" id="first_name" value="<?php echo esc_attr($new_user_firstname); ?>" /></td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="last_name"><?php _e('Last Name') ?> </label></th>
		<td><input name="last_name" type="text" id="last_name" value="<?php echo esc_attr($new_user_lastname); ?>" /></td>
	</tr>
	<tr class="form-field form-required">
		<th scope="row"><label for="email"><?php _e('E-mail (required)') ?></label></th>
		<td><input name="email" type="text" id="email" value="<?php echo esc_attr($new_user_email); ?>" /></td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="url"><?php _e('Website') ?></label></th>
		<td><input name="url" type="text" id="url" class="code" value="<?php echo esc_attr($new_user_uri); ?>" /></td>
	</tr>

<?php if ( apply_filters('show_password_fields', true) ) : ?>
	<tr class="form-field form-required">
		<th scope="row"><label for="pass1"><?php _e('Password (twice)') ?> </label></th>
		<td><input name="pass1" type="password" id="pass1" autocomplete="off" />
		<br />
		<input name="pass2" type="password" id="pass2" autocomplete="off"/></td>
	</tr>
	<tr>
		<th scope="row"><label for="send_password"><?php _e('Send Password?') ?></label></th>
		<td><label for="send_password"><input type="checkbox" name="send_password" id="send_password" <?php checked($new_user_send_password, true); ?> /> <?php _e('Send this password to the new user by email.'); ?></label></td>
	</tr>
<?php endif; ?>

	<tr class="form-field">
		<th scope="row"><label for="role"><?php _e('Role'); ?></label></th>
		<td><select name="role" id="role">
			<?php
			if ( !$new_user_role )
				$new_user_role = !empty($current_role) ? $current_role : get_option('default_role');
			wp_dropdown_roles($new_user_role);
			?>
			</select>
		</td>
	</tr>
</table>
<p class="submit">
	<input name="adduser" type="submit" id="addusersub" class="button-primary" value="<?php esc_attr_e('Add User') ?>" />
</p>
</form>

</div>
<?php
include('admin-footer.php');
?>