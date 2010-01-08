<?php
require_once('admin.php');

if ( !is_multisite() )
	wp_die( __('Multisite support is not enabled.') );

$title = __('Options');
$parent_file = 'ms-admin.php';

include('admin-header.php');

if ( !is_super_admin() )
    wp_die( __('You do not have permission to access this page.') );

if (isset($_GET['updated'])) {
	?>
	<div id="message" class="updated fade"><p><?php _e('Options saved.') ?></p></div>
	<?php
}
?>

<div class="wrap">
	<h2><?php _e('Site Options') ?></h2>
	<form method="post" action="ms-edit.php?action=siteoptions">
		<?php wp_nonce_field( "siteoptions" ); ?>
		<h3><?php _e('Operational Settings <em>(These settings cannot be modified by blog owners)</em>') ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Site Name') ?></th>
				<td>
					<input name="site_name" type="text" id="site_name" style="width: 95%" value="<?php echo esc_attr($current_site->site_name) ?>" size="45" />
					<br />
					<?php _e('What you would like to call this website.') ?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Site Admin Email') ?></th>
				<td>
					<input name="admin_email" type="text" id="admin_email" style="width: 95%" value="<?php echo esc_attr( stripslashes( get_site_option('admin_email') ) ) ?>" size="45" />
					<br />
					<?php printf( __( 'Registration and support mails will come from this address. Make it generic like "support@%s"' ), $current_site->domain ); ?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Allow new registrations') ?></th>
				<?php
				if( !get_site_option('registration') )
					update_site_option( 'registration', 'all' );
				?>
				<td>
					<label><input name="registration" type="radio" id="registration1" value='none' <?php echo get_site_option('registration') == 'none' ? 'checked="checked"' : ''; ?> /> <?php _e('Disabled'); ?></label><br />
					<label><input name="registration" type="radio" id="registration2" value='all' <?php echo get_site_option('registration') == 'all' ? 'checked="checked"' : ''; ?> /> <?php _e('Enabled. Blogs and user accounts can be created.'); ?></label><br />
					<label><input name="registration" type="radio" id="registration3" value='user' <?php echo get_site_option('registration') == 'user' ? 'checked="checked"' : ''; ?> /> <?php _e('Only user account can be created.'); ?></label><br />
					<label><input name="registration" type="radio" id="registration4" value='blog' <?php echo get_site_option('registration') == 'blog' ? 'checked="checked"' : ''; ?> /> <?php _e('Only logged in users can create new blogs.'); ?></label><br />
					<p><?php _e('Disable or enable registration and who or what can be registered. (Default=all)'); ?></p>
					<?php if( is_subdomain_install() ) {
						echo "<p>" . __('If registration is disabled, please set "NOBLOGREDIRECT" in wp-config.php to a url you will redirect visitors to if they visit a non existant blog.') . "</p>";
					} ?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Registration notification') ?></th>
				<?php
				if( !get_site_option('registrationnotification') )
					update_site_option( 'registrationnotification', 'yes' );
				?>
				<td>
					<input name="registrationnotification" type="radio" id="registrationnotification1" value='yes' <?php echo get_site_option('registrationnotification') == 'yes' ? 'checked="checked"' : ''; ?> /> <?php _e('Yes'); ?><br />
					<input name="registrationnotification" type="radio" id="registrationnotification2" value='no' <?php echo get_site_option('registrationnotification') == 'no' ? 'checked="checked"' : ''; ?> /> <?php _e('No'); ?><br />
					<?php _e('Send the site admin an email notification every time someone registers a blog or user account.') ?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Add New Users') ?></th>
				<td>
					<a name='addnewusers'></a>
					<input name="add_new_users" type="radio" id="add_new_users1" value='1' <?php echo get_site_option('add_new_users') == 1 ? 'checked="checked"' : ''; ?> /> <?php _e('Yes'); ?><br />
					<input name="add_new_users" type="radio" id="add_new_users2" value='0' <?php echo get_site_option('add_new_users') == 0 ? 'checked="checked"' : ''; ?> /> <?php _e('No'); ?><br />
					<?php _e('Allow blog administrators to add new users to their blog via the Users->Add New page.') ?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Dashboard Blog') ?></th>
				<td>
					<?php
					if ( $dashboard_blog = get_site_option( 'dashboard_blog' ) ) {
						$details = get_blog_details( $dashboard_blog );
						$blogname = untrailingslashit( sanitize_user( str_replace( '.', '', str_replace( $current_site->domain . $current_site->path, '', $details->domain . $details->path ) ) ) );
					} else {
						$blogname = '';
					}?>
					<input name="dashboard_blog_orig" type="hidden" id="dashboard_blog_orig" value="<?php echo esc_attr($blogname); ?>" />
					<input name="dashboard_blog" type="text" id="dashboard_blog" value="<?php echo esc_attr($blogname); ?>" size="30" />
					<br />
					<?php _e( "Blogname ('dashboard', 'control', 'manager', etc) or blog id.<br />New users are added to this blog as subscribers (or the user role defined below) if they don't have a blog. Leave blank for the main blog. 'Subscriber' users on old blog will be moved to the new blog if changed. New blog will be created if it does not exist." ); ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Dashboard User Default Role') ?></th>
				<td>
					<select name="default_user_role" id="role"><?php
					wp_dropdown_roles( get_site_option( 'default_user_role', 'subscriber' ) );
					?>
					</select>
					<br />
					<?php _e( "The default role for new users on the Dashboard blog. This should probably be 'Subscriber' or maybe 'Contributor'." ); ?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Banned Names') ?></th>
				<td>
					<input name="illegal_names" type="text" id="illegal_names" style="width: 95%" value="<?php echo esc_attr( implode( " ", get_site_option('illegal_names') ) ); ?>" size="45" />
					<br />
					<?php _e('Users are not allowed to register these blogs. Separate names by spaces.') ?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Limited Email Registrations') ?></th>
				<td>
					<?php $limited_email_domains = get_site_option('limited_email_domains');
					$limited_email_domains = str_replace( ' ', "\n", $limited_email_domains ); ?>
					<textarea name="limited_email_domains" id="limited_email_domains" cols='40' rows='5'><?php echo $limited_email_domains == '' ? '' : @implode( "\n", $limited_email_domains ); ?></textarea>
					<br />
					<?php _e('If you want to limit blog registrations to certain domains. One domain per line.') ?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Banned Email Domains') ?></th>
				<td>
					<textarea name="banned_email_domains" id="banned_email_domains" cols='40' rows='5'><?php echo get_site_option('banned_email_domains') == '' ? '' : @implode( "\n", get_site_option('banned_email_domains') ); ?></textarea>
					<br />
					<?php _e('If you want to ban certain email domains from blog registrations. One domain per line.') ?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Welcome Email') ?></th>
				<td>
					<textarea name="welcome_email" id="welcome_email" rows='5' cols='45' style="width: 95%"><?php echo stripslashes( get_site_option('welcome_email') ) ?></textarea>
					<br />
					<?php _e('The welcome email sent to new blog owners.') ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Welcome User Email') ?></th>
				<td>
			    		<textarea name="welcome_user_email" id="welcome_user_email" rows='5' cols='45' style="width: 95%"><?php echo stripslashes( get_site_option('welcome_user_email') ) ?></textarea>
					<br />
					<?php _e('The welcome email sent to new users.') ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('First Post') ?></th>
				<td>
					<textarea name="first_post" id="first_post" rows='5' cols='45' style="width: 95%"><?php echo stripslashes( get_site_option('first_post') ) ?></textarea>
					<br />
					<?php _e('First post on a new blog.') ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('First Page') ?></th>
				<td>
					<textarea name="first_page" id="first_page" rows='5' cols='45' style="width: 95%"><?php echo stripslashes( get_site_option('first_page') ) ?></textarea>
					<br />
					<?php _e('First page on a new blog.') ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('First Comment') ?></th>
				<td>
					<textarea name="first_comment" id="first_comment" rows='5' cols='45' style="width: 95%"><?php echo stripslashes( get_site_option('first_comment') ) ?></textarea>
					<br />
					<?php _e('First comment on a new blog.') ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('First Comment Author') ?></th>
				<td>
					<input type="text" size='40' name="first_comment_author" id="first_comment_author" value="<?php echo get_site_option('first_comment_author') ?>" />
					<br />
					<?php _e('Author of first comment on a new blog.') ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('First Comment URL') ?></th>
				<td>
					<input type="text" size='40' name="first_comment_url" id="first_comment_url" value="<?php echo esc_attr(get_site_option('first_comment_url')) ?>" />
					<br />
					<?php _e('URL on first comment on a new blog.') ?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Upload media button') ?></th>
				<?php $mu_media_buttons = get_site_option( 'mu_media_buttons', array() ); ?>
				<td><label><input type='checkbox' id="mu_media_buttons_image" name="mu_media_buttons[image]" value='1' <?php if( $mu_media_buttons[ 'image' ] ) { echo 'checked=checked '; } ?>/> <?php _e( 'Images' ); ?></label><br />
				<label><input type='checkbox' id="mu_media_buttons_video" name="mu_media_buttons[video]" value='1' <?php if( $mu_media_buttons[ 'video' ] ) { echo 'checked=checked '; } ?>/> <?php _e( 'Videos' ); ?></label><br />
				<label><input type='checkbox' id="mu_media_buttons_audio" name="mu_media_buttons[audio]" value='1' <?php if( $mu_media_buttons[ 'audio' ] ) { echo 'checked=checked '; } ?>/> <?php _e( 'Music' ); ?></label><br />
				<?php _e( 'The media upload buttons to display on the "Write Post" page. Make sure you update the "Upload File Types" below as well.' ); ?></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Blog upload space check') ?></th>
				<td>
				<label><input type='radio' id="upload_space_check_disabled" name="upload_space_check_disabled" value='0' <?php if( !get_site_option( 'upload_space_check_disabled' ) ) { echo 'checked=checked '; } ?>/> <?php _e( 'Enabled' ); ?></label><br />
				<label><input type='radio' id="upload_space_check_disabled" name="upload_space_check_disabled" value='1' <?php if( get_site_option( 'upload_space_check_disabled' ) ) { echo 'checked=checked '; } ?>/> <?php _e( 'Disabled' ); ?></label><br />
				<?php _e( 'By default there is a limit on the total size of files uploaded but it can be disabled here.' ); ?></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Blog upload space') ?></th>
				<td><input name="blog_upload_space" type="text" id="blog_upload_space" value="<?php echo esc_attr( get_site_option('blog_upload_space', 10) ) ?>" size="3" /> MB</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Upload File Types') ?></th>
				<td><input name="upload_filetypes" type="text" id="upload_filetypes" value="<?php echo esc_attr( get_site_option('upload_filetypes', 'jpg jpeg png gif') ) ?>" size="45" /></td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Max upload file size') ?></th>
				<td><input name="fileupload_maxk" type="text" id="fileupload_maxk" value="<?php echo esc_attr( get_site_option('fileupload_maxk', 300) ) ?>" size="5" /> KB</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Admin Notice Feed') ?></th>
				<td><input name="admin_notice_feed" style="width: 95%" type="text" id="admin_notice_feed" value="<?php echo esc_attr( get_site_option( 'admin_notice_feed' ) ) ?>" size="80" /><br />
				<?php _e( 'Display the latest post from this RSS or Atom feed on all blog dashboards. Leave blank to disable.' ); ?><br />
				<?php if( get_site_option( 'admin_notice_feed' ) != 'http://' . $current_site->domain . $current_site->path . 'feed/' )
					echo __( "A good one to use would be the feed from your main blog: " ) . 'http://' . $current_site->domain . $current_site->path . 'feed/'; ?></td>
			</tr>
		</table>

		<h3><?php _e('Administration Settings') ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Site Admins') ?></th>
				<td>
					<input name="site_admins" type="text" id="site_admins" style="width: 95%" value="<?php echo esc_attr( implode(' ', get_site_option( 'site_admins', array( 'admin' ) ) ) ) ?>" size="45" />
					<br />
					<?php _e('These users may login to the main blog and administer the site. Space separated list of usernames.') ?>
				</td>
			</tr>
		</table>

		<h3><?php _e('Site Wide Settings <em>(These settings may be overridden by blog owners)</em>') ?></h3>
		<table class="form-table">
			<?php
			if( is_dir( ABSPATH . LANGDIR ) && $dh = opendir( ABSPATH . LANGDIR ) )
				while( ( $lang_file = readdir( $dh ) ) !== false )
					if( substr( $lang_file, -3 ) == '.mo' )
						$lang_files[] = $lang_file;
			$lang = get_site_option('WPLANG');
			if( is_array($lang_files) && !empty($lang_files) ) {
				?>
				<tr valign="top">
					<th width="33%"><?php _e('Default Language') ?></th>
					<td>
						<select name="WPLANG" id="WPLANG">
							<?php mu_dropdown_languages( $lang_files, get_site_option('WPLANG') ); ?>
						</select>
					</td>
				</tr>
				<?php
			} // languages
			?>
		</table>

		<h3><?php _e('Menus <em>(Enable or disable WP Backend Menus)</em>') ?></h3>
		<table class="form-table">
			<tr>
				<th scope="row"><?php _e("Menu"); ?></th>
				<th scope="row"><?php _e("Enabled"); ?></th>
			</tr>
			<a name='menu'></a>
			<?php
			$menu_perms = get_site_option( "menu_items" );
			$menu_items = apply_filters( 'mu_menu_items', array('plugins' => __('Plugins')) );
			foreach ( (array) $menu_items as $key => $val ) {
				echo "<tr><th scope='row'>" . wp_specialchars($val) . "</th><td><input type='checkbox' name='menu_items[" . $key . "]' value='1'" . (( $menu_perms[$key] == '1' ) ? ' checked="checked"' : '') . " /></td></tr>";
			}
			?>
		</table>

		<?php do_action( 'wpmu_options' ); // Add more options here ?>

		<p class="submit">
			<input type="submit" name="Submit" value="<?php esc_attr_e('Update Options') ?>" /></p>
	</form>
</div>

<?php include('./admin-footer.php'); ?>
